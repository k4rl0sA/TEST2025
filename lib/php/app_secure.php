<?php
// --- Seguridad de sesión y cookies ---
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800); // 30 minutos

// --- Cargar variables de entorno ---
require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// --- Configuración básica ---
if (session_status() === PHP_SESSION_NONE) session_start();
ini_set('display_errors', getenv('APP_ENV') === 'production' ? '0' : '1');
setlocale(LC_TIME, 'es_CO');
ini_set('memory_limit','1024M');
date_default_timezone_set(getenv('TIMEZONE') ?: 'America/Bogota');
setlocale(LC_ALL,'es_CO');
$APP = getenv('APP_NAME') ?: 'APP';

// --- Rate limiting básico por sesión ---
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
    $_SESSION['first_request'] = time();
}
$_SESSION['request_count']++;
$time_elapsed = time() - $_SESSION['first_request'];
if ($time_elapsed < 60 && $_SESSION['request_count'] > 100) {
    http_response_code(429);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Demasiadas solicitudes']);
    exit;
}
if ($time_elapsed >= 60) {
    $_SESSION['request_count'] = 1;
    $_SESSION['first_request'] = time();
}

// --- Validación de dominio y CORS ---
$dom = $_SERVER['HTTP_HOST'];
$dominio = preg_replace('/^www\./i', '', $dom);
$allowed_domains = array_map('trim', explode(',', getenv('ALLOWED_DOMAINS')));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowed_domains)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
} elseif ($origin) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success'=>false, 'error'=>'Dominio no permitido']);
    exit;
} elseif (!in_array($dominio, $allowed_domains)) {
    die('Dominio no permitido.'.' '.htmlentities($dominio));
}

// --- Configuración de base de datos ---
$dbConfig = [
    'host' => getenv('DB_HOST'),
    'user' => getenv('DB_USER'),
    'pass' => getenv('DB_PASS'),
    'name' => getenv('DB_NAME'),
    'port' => getenv('DB_PORT') ?: 3306
];
$con = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['name'], $dbConfig['port']);
if (!$con) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}
mysqli_set_charset($con, "utf8");
$GLOBALS['con'] = $con;

// --- Seguridad de sesión avanzada ---
if(!isset($_SESSION['created'])){
    $_SESSION['created']=time();
}else if(time()-$_SESSION['created']>1800){
    session_regenerate_id(true);
    $_SESSION['created']=time();
}

// --- CSRF Token seguro ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}
function check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $session_token = $_SESSION['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($token, $session_token)) {
            log_error(usuSess().' = Invalid CSRF token');
            error_response('CSRF token inválido o ausente', 403);
        }
        if(time() - ($_SESSION['csrf_token_time'] ?? 0) > 7200) {
            log_error(usuSess().' = CSRF token expirado');
            error_response('CSRF token expirado', 403);
        }
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    }
}

// --- JWT Helper ---
require_once __DIR__ . '/jwt_helper.php';

// --- Funciones de utilidad y seguridad ---
function error_response($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}
function success_response($msg = 'Operación exitosa', $extra = []) {
    echo json_encode(array_merge(['success' => true, 'message' => $msg], $extra));
    exit;
}
function log_error($message) {
    $marca = date('Y-m-d H:i:s');
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) mkdir($logDir, 0777, true);
    $logMessage = "[$marca] - ".usuSess()." = $message" . PHP_EOL;
    try {
        file_put_contents($logDir . 'file.log', $logMessage, FILE_APPEND);
    } catch (Throwable $e) {
        file_put_contents($logDir . 'errors_backup.log', "[$marca] Error al registrar: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
}
function usuSess(){
    return isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : 'Usuario Desconocido';
}

// --- Sanitización y validación de entrada ---
function sanitizeInput($arr, $exclude = [], $maxLength = null) {
    $out = [];
    foreach ($arr as $k => $v) {
        if (in_array($k, $exclude, true)) {
            $out[$k] = $v;
            continue;
        }
        if (is_array($v)) {
            $out[$k] = sanitizeInput($v, $exclude, $maxLength);
        } else {
            $val = trim($v);
            if ($maxLength !== null && is_string($val)) {
                $val = mb_substr($val, 0, $maxLength, 'UTF-8');
            }
            $val = htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $val = preg_replace('/[\x00-\x1F\x7F]/u', '', $val);
            $out[$k] = $val;
        }
    }
    return $out;
}
function validateInput($data, $rules) {
    $errors = [];
    foreach ($rules as $field => $ruleStr) {
        $value = $data[$field] ?? null;
        $rulesArr = explode('|', $ruleStr);
        foreach ($rulesArr as $rule) {
            if ($rule === 'required' && ($value === null || $value === '')) {
                $errors[$field] = "El campo $field es obligatorio";
                break;
            }
            if ($rule === 'int' && $value !== null && !is_numeric($value)) {
                $errors[$field] = "El campo $field debe ser numérico";
                break;
            }
            if ($rule === 'string' && $value !== null && !is_string($value)) {
                $errors[$field] = "El campo $field debe ser texto";
                break;
            }
            if ($rule === 'email' && $value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "El campo $field debe ser un email válido";
                break;
            }
            if ($rule === 'url' && $value !== null && !filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[$field] = "El campo $field debe ser una URL válida";
                break;
            }
            if ($rule === 'date' && $value !== null) {
                $d = DateTime::createFromFormat('Y-m-d', $value);
                if (!$d || $d->format('Y-m-d') !== $value) {
                    $errors[$field] = "El campo $field debe ser una fecha válida (YYYY-MM-DD)";
                    break;
                }
            }
            if (preg_match('/min:(\d+)/', $rule, $matches) && is_numeric($value) && $value < $matches[1]) {
                $errors[$field] = "El campo $field debe ser al menos " . $matches[1];
                break;
            }
            if (preg_match('/max:(\d+)/', $rule, $matches) && is_numeric($value) && $value > $matches[1]) {
                $errors[$field] = "El campo $field debe ser como máximo " . $matches[1];
                break;
            }
            if (preg_match('/minlen:(\d+)/', $rule, $matches) && is_string($value) && strlen($value) < $matches[1]) {
                $errors[$field] = "El campo $field debe tener al menos " . $matches[1] . " caracteres";
                break;
            }
            if (preg_match('/maxlen:(\d+)/', $rule, $matches) && is_string($value) && strlen($value) > $matches[1]) {
                $errors[$field] = "El campo $field debe tener como máximo " . $matches[1] . " caracteres";
                break;
            }
            if (preg_match('/pattern:\/(.+)\/([a-zA-Z]*)/', $rule, $matches) && is_string($value)) {
                if (!preg_match('/' . $matches[1] . '/' . $matches[2], $value)) {
                    $errors[$field] = "El campo $field tiene un formato inválido";
                    break;
                }
            }
        }
    }
    return empty($errors) ? true : $errors;
}

// --- Manejo seguro de archivos (ejemplo para uploads CSV) ---
function handle_upload($input_name = 'archivo', $allowed_types = ['text/csv', 'application/vnd.ms-excel', 'application/csv', 'text/plain'], $max_size = 5 * 1024 * 1024) {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        error_response("Error al subir el archivo.");
    }
    if (!in_array($_FILES[$input_name]['type'], $allowed_types)) {
        error_response("Tipo de archivo no permitido.");
    }
    if ($_FILES[$input_name]['size'] > $max_size) {
        error_response("El archivo excede el tamaño máximo permitido.");
    }
    $file_ext = strtolower(pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION));
    if ($file_ext !== 'csv') {
        error_response("Solo se permiten archivos con extensión .csv");
    }
    $tb = $_POST['b'] ?? '';
    if (empty($tb)) {
        error_response("Parámetro de tabla no especificado.");
    }
    $usuario = $_SESSION['us_riesgo'] ?? 'default';
    $fecha = date("Ymd_His");
    $ruta_upload_env = getenv('RUTA_UPLOAD') ?: '/tmp';
    $ru = $ruta_upload_env . '/' . $tb . '/' . $usuario . '/';
    if (!is_dir($ru)) {
        if (!mkdir($ru, 0777, true)) {
            error_response("No se pudo crear el directorio de destino.");
        }
    }
    $fi = $ru . $fecha . '.csv';
    if (!move_uploaded_file($_FILES[$input_name]['tmp_name'], $fi)) {
        error_response("Error al mover el archivo subido.");
    }
    return str_replace($ruta_upload_env, '', $fi);
}

// --- Ejemplo de uso en endpoints ---
$req = $_REQUEST['a'] ?? '';
switch ($req) {
    case 'upload':
        check_csrf();
        $file = handle_upload();
        success_response('Archivo subido correctamente', ['file' => $file]);
        break;
    // Agrega aquí tus otros endpoints...
}

// --- Ejemplo de función para consultas SQL seguras ---
function datos_mysql($sql, $resulttype = MYSQLI_ASSOC, $params = []) {
    $arr = ['code' => 0, 'message' => '', 'responseResult' => []];
    $con = $GLOBALS['con'];
    if (!$con) {
        $arr['code'] = 30;
        $arr['message'] = 'No hay conexión activa a la base de datos.';
        log_error(usuSess() . ' = Connection error');
        return $arr;
    }
    try {
        $con->set_charset('utf8');
        if ($params && is_array($params) && count($params) > 0) {
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                log_error(usuSess() . ' Error preparando: ' . $con->error);
                throw new mysqli_sql_exception("Error preparando: " . $con->error, $con->errno);
            }
            $types = '';
            $values = [];
            foreach ($params as $param) {
                $types .= $param['type'];
                $values[] = $param['value'];
            }
            $bind_names[] = $types;
            for ($i=0; $i<count($values); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $values[$i];
                $bind_names[] = &$$bind_name;
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_names);
            if (!$stmt->execute()) {
                log_error(usuSess() . ' Error ejecutando: ' . $stmt->error);
                throw new mysqli_sql_exception("Error ejecutando: " . $stmt->error, $stmt->errno);
            }
            $result = $stmt->get_result();
            if ($result) {
                while ($r = $result->fetch_array($resulttype)) {
                    $arr['responseResult'][] = $r;
                }
                $result->free();
            } else {
                $arr['responseResult'][] = ['affected_rows' => $stmt->affected_rows];
            }
            $stmt->close();
        } else {
            $rs = $con->query($sql);
            if (!$rs) {
                log_error(usuSess() . ' Error en la consulta: ' . $con->error, $con->errno);
                throw new mysqli_sql_exception("Error en la consulta: " . $con->error, $con->errno);
            }
            while ($r = $rs->fetch_array($resulttype)) {
                $arr['responseResult'][] = $r;
            }
            $rs->free();
        }
    } catch (mysqli_sql_exception $e) {
        $arr['code'] = 30;
        $arr['message'] = 'Error BD';
        $arr['errors'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
        log_error(usuSess().' => '.$e->getCode().'='.$e->getMessage());
    }
    return $arr;
}