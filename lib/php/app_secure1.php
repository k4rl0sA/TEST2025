<?php
declare(strict_types=1);

// ==================== CONFIGURACIÓN INICIAL ====================
use Dotenv\Dotenv;

// Autoloading de Composer para gestión de dependencias
require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configuración de sesión segura
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', '1800'); // 30 minutos
ini_set('session.cookie_lifetime', '0'); // Expira al cerrar el navegador

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Regenerar ID de sesión periódicamente para prevenir fixation attacks
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Configuración de la aplicación
ini_set('display_errors', $_ENV['APP_ENV'] === 'production' ? '0' : '1');
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'America/Bogota');
setlocale(LC_ALL, 'es_CO');
setlocale(LC_TIME, 'es_CO');
ini_set('memory_limit', $_ENV['MEMORY_LIMIT'] ?? '1024M');

$APP = $_ENV['APP_NAME'] ?? 'APP';

// ==================== MANEJO DE ERRORES ====================
error_reporting(E_ALL);
if ($_ENV['APP_ENV'] === 'production') {
    ini_set('display_errors', '0');
} else {
    ini_set('display_errors', '1');
}

// Función para manejo de errores no capturados
set_exception_handler(function (Throwable $e) {
    log_error('Excepción no capturada: ' . $e->getMessage());
    if ($_ENV['APP_ENV'] !== 'production') {
        error_response($e->getMessage(), 500);
    } else {
        error_response('Error interno del servidor', 500);
    }
    exit;
});

// ==================== CONEXIÓN A BASE DE DATOS ====================
$dbConfig = [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'user' => $_ENV['DB_USER'] ?? '',
    'pass' => $_ENV['DB_PASS'] ?? '',
    'name' => $_ENV['DB_NAME'] ?? '',
    'port' => isset($_ENV['DB_PORT']) ? (int)$_ENV['DB_PORT'] : 3306,
];

if (!$dbConfig['user'] || !$dbConfig['pass'] || !$dbConfig['name']) {
    error_response('Configuración de base de datos incompleta', 500);
}

$con = mysqli_connect($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['name'], $dbConfig['port']);
if (!$con) {
    log_error('Error de conexión a la base de datos: ' . mysqli_connect_error());
    error_response('Error de conexión a la base de datos', 500);
}
mysqli_set_charset($con, "utf8");
$GLOBALS['con'] = $con;

// ==================== CONFIGURACIÓN DE CORS Y HEADERS DE SEGURIDAD ====================
$allowed_domains = array_map('trim', explode(',', $_ENV['ALLOWED_DOMAINS'] ?? ''));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Normalizar dominio (eliminar www y otros subdominios)
$normalized_domain = preg_replace('/^www\./i', '', parse_url($origin, PHP_URL_HOST) ?? '');

if ($origin && in_array($normalized_domain, $allowed_domains)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token');
    header('Access-Control-Max-Age: 3600');
} elseif ($origin) {
    error_response('Dominio no permitido', 403);
}

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ==================== RATE LIMITING MEJORADO ====================
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
    $_SESSION['first_request'] = time();
    $_SESSION['last_request'] = time();
}

$current_time = time();
$time_elapsed = $current_time - $_SESSION['first_request'];

// Rate limiting por IP y por usuario
$max_requests_per_minute = (int)($_ENV['RATE_LIMIT_PER_MINUTE'] ?? 100);
$max_requests_per_hour = (int)($_ENV['RATE_LIMIT_PER_HOUR'] ?? 1000);

// Contador por minuto
if ($time_elapsed < 60 && $_SESSION['request_count'] > $max_requests_per_minute) {
    error_response('Demasiadas solicitudes', 429);
}

// Contador por hora (reinicia cada hora)
if ($current_time - $_SESSION['last_request'] > 3600) {
    $_SESSION['request_count'] = 0;
    $_SESSION['first_request'] = $current_time;
}

$_SESSION['request_count']++;
$_SESSION['last_request'] = $current_time;

// ==================== MANEJO DE PETICIONES ====================
// Validar método HTTP
$allowed_methods = ['GET', 'POST', 'OPTIONS', 'PUT', 'DELETE'];
if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
    error_response('Método no permitido', 405);
}

// Validar CSRF para métodos que lo requieren
if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
    validateCSRF();
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    generate_csrf_token();
}

// Procesar la solicitud
$req = $_REQUEST['a'] ?? '';
switch ($req) {
    case '':
        // Página principal o endpoint por defecto
        break;
    case 'exportar':
        handle_export();
        break;
    case 'upload':
        handle_upload();
        break;
    // Agregar más casos según necesidades
    default:
        // Para APIs, buscar en rutas RESTful
        if (isAjax() || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
            handle_api_request();
        } else {
            error_response('Recurso no encontrado', 404);
        }
        break;
}

// ==================== FUNCIONES PRINCIPALES ====================

/**
 * Maneja solicitudes de API RESTful
 */
function handle_api_request(): void {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path_segments = explode('/', trim($path, '/'));
    
    // Ejemplo básico de routing para API
    $resource = $path_segments[1] ?? '';
    $id = $path_segments[2] ?? '';
    
    header('Content-Type: application/json');
    
    switch ("$method $resource") {
        case 'GET users':
            // Lógica para obtener usuarios
            break;
        case 'POST users':
            // Lógica para crear usuario
            break;
        // Más casos según necesidades
        default:
            error_response('Endpoint no válido', 404);
    }
}

/**
 * Maneja la exportación de datos
 */
function handle_export(): void {
    // Validar permisos
    if (!acceso('exportar')) {
        error_response('Acceso no autorizado', 403);
    }
    
    $type = $_REQUEST['b'] ?? '';
    if (empty($type) || !isset($_SESSION['sql_' . $type])) {
        error_response('Tipo de exportación no válido', 400);
    }
    
    $now = date("ymd");
    header_csv($type . '_' . $now . '.csv');
    
    $info = datos_mysql($_SESSION['tot_' . $type]);
    $total = $info['responseResult'][0]['total'] ?? 0;
    
    if ($rs = mysqli_query($GLOBALS['con'], $_SESSION['sql_' . $type])) {
        $ts = mysqli_fetch_array($rs, MYSQLI_ASSOC);
        echo csv($ts, $rs, $total);
    } else {
        log_error($_SESSION["us_sds"] . ' => ' . $GLOBALS['con']->errno . ' = ' . $GLOBALS['con']->error);
        error_response('Error en la consulta de exportación', 500);
    }
    exit;
}

/**
 * Maneja la subida de archivos
 */
function handle_upload(): void {
    $allowed_types = ['text/csv', 'application/vnd.ms-excel', 'application/csv', 'text/plain'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        error_response("Error al subir el archivo.");
    }
    
    // Validar tipo MIME real (no confiar en la extensión)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['archivo']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        error_response("Tipo de archivo no permitido.");
    }
    
    if ($_FILES['archivo']['size'] > $max_size) {
        error_response("El archivo excede el tamaño máximo permitido.");
    }
    
    $file_ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
    if ($file_ext !== 'csv') {
        error_response("Solo se permiten archivos con extensión .csv");
    }

    $tb = $_POST['b'] ?? '';
    if (empty($tb)) {
        error_response("Parámetro de tabla no especificado.");
    }
    
    $usuario = $_SESSION['us_sds'] ?? 'default';
    $fecha = date("Ymd_His");
    $ruta_upload_env = $_ENV['RUTA_UPLOAD'] ?? '/uploads';
    $ru = $ruta_upload_env . '/' . $tb . '/' . $usuario . '/';
    
    if (!is_dir($ru)) {
        if (!mkdir($ru, 0755, true)) {
            error_response("No se pudo crear el directorio de destino.");
        }
    }
    
    // Generar nombre de archivo seguro
    $safe_name = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $_FILES['archivo']['name']);
    $fi = $ru . $fecha . '_' . $safe_name;
    
    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $fi)) {
        error_response("Error al mover el archivo subido.");
    }
    
    // Responder con éxito
    echo json_encode([
        'success' => true, 
        'file' => str_replace($ruta_upload_env, '', $fi),
        'message' => 'Archivo subido correctamente'
    ]);
    exit;
}

// ==================== FUNCIONES DE SEGURIDAD ====================

/**
 * Genera un token CSRF seguro
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida el token CSRF
 */
function validateCSRF(): void {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $session_token = $_SESSION['csrf_token'] ?? '';
    
    if (empty($token) || !hash_equals($token, $session_token)) {
        log_error((usuSess() ?? 'Unknown') . ' = Invalid CSRF token');
        error_response('CSRF token inválido o ausente', 403);
    }
    
    // Verificar expiración (2 horas)
    if (time() - ($_SESSION['csrf_token_time'] ?? 0) > 7200) {
        log_error(usuSess() . ' = CSRF token expirado');
        error_response('CSRF token expirado', 403);
    }
    
    // Rotar token después de su uso
    unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    generate_csrf_token();
}

/**
 * Función de hashing segura
 */
function myhash($a): string {
    $usuario = $_SESSION['us_sds'] ?? '';
    $salt = $_ENV['HASH_SALT'] ?? 'D2AC5E5211884EA15F1E950D1445C5E8';
    return hash_hmac('sha256', $a . $usuario, $salt);
}

/**
 * Valida un hash
 */
function validateHash($hash, $original): bool {
    $calculado = myhash($original);
    return hash_equals($calculado, $hash);
}

// ==================== FUNCIONES AUXILIARES ====================

/**
 * Registra errores en log
 */
function log_error($message): void {
    $timestamp = date('Y-m-d H:i:s');
    $logDir = __DIR__ . '/../logs/';
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $usuario = usuSess();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $request = $_SERVER['REQUEST_URI'] ?? 'unknown';
    
    $logMessage = "[$timestamp] - $usuario ($ip) - $request - $message" . PHP_EOL;
    
    try {
        file_put_contents($logDir . 'application.log', $logMessage, FILE_APPEND | LOCK_EX);
    } catch (Throwable $e) {
        // Fallback seguro si no se puede escribir en el log principal
        error_log("Error al escribir en log: " . $e->getMessage());
    }
}

/**
 * Devuelve el usuario de sesión
 */
function usuSess(): string {
    return $_SESSION['us_sds'] ?? 'Usuario Desconocido';
}

/**
 * Respuesta de error estandarizada
 */
function error_response($message, $code = 400): void {
    http_response_code($code);
    
    if (isAjax() || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $message]);
    } else {
        // Para solicitudes no-AJAX, mostrar página de error
        include __DIR__ . '/views/error.php';
    }
    exit;
}

/**
 * Verifica si es una solicitud AJAX
 */
function isAjax(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// ... (aquí irían el resto de funciones como rol(), perfil(), acceso(), 
// datos_mysql(), etc. con las mejoras de seguridad de app1.php)

// Cerrar conexión a base de datos al finalizar
if (isset($GLOBALS['con'])) {
    mysqli_close($GLOBALS['con']);
}