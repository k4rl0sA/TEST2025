<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}
// Rate limiting básico
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

ini_set('display_errors','0');
setlocale(LC_TIME, 'es_CO');
// $GLOBALS['app']='sds';
ini_set('memory_limit','1024M');
date_default_timezone_set('America/Bogota');
setlocale(LC_ALL,'es_CO');
$APP='GTAPS';

// --- Cargar variables de entorno desde .env ---
function load_env($file = __DIR__ . '/.env') {
  if (!file_exists($file)) return;
  $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = array_map('trim', explode('=', $line, 2));
    if (!array_key_exists($name, $_ENV)) {
      $_ENV[$name] = $value;
    }
  }
}
load_env();
require_once __DIR__ . '/jwt_helper.php';
$ruta_upload='/public_html/upload/';

// --- Configuración de base de datos desde .env ---
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? '';
$db_pass = $_ENV['DB_PASS'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? '';
$db_port = isset($_ENV['DB_PORT']) ? intval($_ENV['DB_PORT']) : 3306;

if (!$db_user || !$db_pass || !$db_name) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Configuración de base de datos incompleta']);
    exit;
}
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$con) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// --- Configuración CORS Permitir solo dominios HTTPS listados en ALLOWED_DOMAINS
$allowed_domains = array_map('trim', explode(',', $_ENV['ALLOWED_DOMAINS'] ?? ''));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin && in_array($origin, $allowed_domains)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token');

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
} elseif ($origin) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success'=>false, 'error'=>'Dominio no permitido']);
    exit;
}else{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}

// Validar método HTTP
$allowed_methods = ['GET', 'POST', 'OPTIONS'];
if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
    http_response_code(405);
    exit('Método no permitido');
}

/* if (!isset($_SESSION["us_sds"])) {
  header("Location: /index.php"); 
  exit;
} */

mysqli_set_charset($con,"utf8");
$GLOBALS['con']=$con;
$req = (isset($_REQUEST['a'])) ? $_REQUEST['a'] : '';
switch ($req) {
	case '';
	break;
	case 'exportar':
    $now=date("ymd");
		header_csv($_REQUEST['b'] .'_'.$now.'.csv');
    $info=datos_mysql($_SESSION['tot_' . $_REQUEST['b']]);
		$total=$info['responseResult'][0]['total'];
		if ($rs = mysqli_query($GLOBALS[isset($_REQUEST['con']) ? $_REQUEST['con'] : 'con'], $_SESSION['sql_' . $_REQUEST['b']])) {
			$ts = mysqli_fetch_array($rs, MYSQLI_ASSOC);
			echo csv($ts, $rs,$total);
		} else {
      die(log_error($_SESSION["us_sds"].'=>'.$GLOBALS['con']->errno.'='.$GLOBALS['con']->error));
			//echo "Error " . $GLOBALS['con']->errno . ": " . $GLOBALS['con']->error;
      $GLOBALS['con']->close();
		}
		die;
		break;
	case 'upload':
		$cr = $_REQUEST['c'];
		$ya = new DateTime();
		$tb = $_POST['b'];
		$fe = strftime("%Y-%m-%d %H:%M");
		$ru = $GLOBALS['ruta_upload'] . '/' . $tb . '/' . $_SESSION['us_riesgo'] . '/';
		$fi = $ru . $fe . '.csv';
		$ar = str_replace($GLOBALS['ruta_upload'], '', $fi);
		if (!is_dir($ru))
			mkdir($ru);
		if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $fi))
			echo "Error " . $_FILES['archivo']['error'] . " " . $fi;
		else {
		}
		break;
}

function header_csv($a) {
  $now = gmdate("D, d M Y H:i:s");
  header("Expires:".$now);
  header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
  header("Last-Modified: {$now} GMT");
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");
  header("Content-Disposition: attachment;filename={$a}");
  header("Content-Transfer-Encoding: binary");
  header("Content-Type: text/csv; charset=UTF-8");
}

function csv($a,$b,$tot= null){
  $df=fopen("php://output", 'w');
  ob_start();
  if(isset($a)){fwrite($df, "\xEF\xBB\xBF"); fputcsv($df,array_keys($a),'|');}
  if(isset($b)){
    foreach ($b as $row) fputcsv($df,$row,'|');
  }
  if ($tot !== null) {
    fwrite($df, "Total Registros: " . $tot . PHP_EOL);
  }
  fclose($df);
  return ob_get_clean();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
           log_error($_SESSION["us_sds"].' = Invalid CSRF token');
            error_response('CSRF token inválido o ausente', 403);
        }
    }
}

//HASHEAR MIS IDS 
function myhash($a){
  $usuario = isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : '';
  $hash = md5($a . $usuario . 'D2AC5E5211884EA15F1E950D1445C5E8');
  return $hash;
}

function IdHash($hash, $accion = '') {
    $key = $accion ? $hash . '_' . $accion : $hash;
    if (isset($_SESSION['hash'][$hash])) {
        return $_SESSION['hash'][$hash];
    }
    if (isset($_SESSION['hash'][$key])) {
        return $_SESSION['hash'][$key];
    }
    return null;
}
function limpiar_hashes($max = 500) {
    if (!isset($_SESSION['hash']) || !is_array($_SESSION['hash'])) return;
    // Si hay más de $max hashes, elimina los más antiguos
    if (count($_SESSION['hash']) > $max) {
        // Mantén solo los últimos $max elementos
        $_SESSION['hash'] = array_slice($_SESSION['hash'], -$max, $max, true);
    }
}   
//FIN HASHEAR MIS IDS 

function log_error($message) {
  $timestamp = date('Y-m-d H:i:s');
  $marca = date('Y-m-d H:i:s');
  $logDir = __DIR__ . '/../logs/';
  if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
  }
  $usuario = isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : 'Usuario Desconocido';
  $logMessage = "[$marca] - ".$usuario." = $message" . PHP_EOL;
  try {
    file_put_contents($logDir . 'file.log', $logMessage, FILE_APPEND);
  } catch (Throwable $e) {
    file_put_contents($logDir . 'errors_backup.log', "[$marca] Error al registrar: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
  }
}

function usuSess(){
  return $usu = isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : 'Usuario Desconocido';
}

function validFecha($mod,$fecha){
  $days = fechas_app($mod);
  $fechaMin = date('Y-m-d', strtotime("$days days"));
  $fechaMax= date('Y-m-d');
  $fech = $fecha ?? '';
  if (empty($fech)) {
    return "msj['Error: La fecha es obligatoria.']";
  }
  if ($fech < $fechaMin) {
    log_error("Fraude: fecha = " . $_POST['tb'] . ' - ' . $fech);
    return "msj['Error: La fecha no puede ser menor a $fechaMin.']";
  }
  return true;
}

function validNum($num,$ncar=[],$nlit=[]){
  if ($num === '' || $num === null) {
    return "msj['Error: El número es obligatorio.']";
  }
  if (!is_numeric($num)) {
    return "msj['Error: El valor ingresado no es un número válido.']";
  }
  if (in_array($num,$nlit,true)) {
    return true;
  }
  $nDig = strlen((string) $num);
  if (!in_array($nDig, $ncar)) {
    return "msj['Error: El número ingresado ($num) no tiene la cantidad de dígitos permitida.']";
}
  return true;
}

function rol($a){ //a=modulo, b=perfil c=componente
  $rta = array();
  $id_usuario = isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : '';
  if (!$id_usuario) return $rta;
  $sql = "SELECT perfil,componente,crear,editar,consultar,ajustar,importar FROM adm_roles WHERE modulo = '".$a."' and perfil= (SELECT perfil FROM usuarios where id_usuario= '".$id_usuario."') AND componente=(SELECT componente FROM usuarios where id_usuario= '".$id_usuario."') AND estado = 'A'";
  $data = datos_mysql($sql);
  //print_r($data);
  if ($data && isset($data['responseResult'][0])) {
    $rta = $data['responseResult'][0];
  }
  return $rta;
}

function perfil($a){
  $perf = rol($a);
  // Si no hay sesión, simplemente retorna (API pública)
  if (!isset($_SESSION['us_sds'])) return;
  // Si hay sesión pero no permisos, muestra mensaje y termina
  if (empty($perf['perfil']) || $perf['perfil'] === array()){
    echo "<H1>ACCESO NO AUTORIZADO,PARA {$a} VALIDA TUS PERMISOS CON EL ADMINISTRADOR DEL SISTEMA</H1><div class='messag rtawarn'></div>";
    exit();
  }
}

function acceso($a){
  // Si hay sesión, usa permisos de sesión
  if (isset($_SESSION['us_sds'])) {
    $acc = rol($a);
    if (!empty($acc['perfil'])){
      return true;
    } else {
      return;
    }
  }
  // Si no hay sesión, buscar JWT en Authorization
  $jwt = null;
  if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (stripos($authHeader, 'Bearer ') === 0) {
      $jwt = trim(substr($authHeader, 7));
    }
  }
  if ($jwt) {
    $jwt_secret = isset($_ENV['JWT_SECRET']) ? $_ENV['JWT_SECRET'] : $_ENV['JWT_SECRET_default'];
    $payload = jwt_decode($jwt, $jwt_secret);
    if ($payload && isset($payload['perfil'])) {
      // Puedes agregar más validaciones aquí (exp, iss, etc)
      return true;
    }
    // JWT inválido
    return;
  }
  // Si no hay sesión ni JWT válido, denegar
  return;
}

function show_sql($sql, $params) {
  // Dividir la consulta SQL en partes basadas en los placeholders
  $parts = explode('?', $sql);
  $final_sql = '';
  foreach ($parts as $index => $part) {
      $final_sql .= $part;
      if (isset($params[$index])) {
          $param = $params[$index];
          $value = $param['value'];
          if ($param['type'] === 's') {
              $final_sql .= "'" . addslashes($value) . "'";
          } else {
              $final_sql .= $value;
          }
      }
  }
  return $final_sql;
}

function fechas_app($modu){
    switch ($modu) {
    case 'vsp':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=1;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    case 'vivienda':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=2;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    case 'relevo':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=3;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    case 'psicologia':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=4;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    case 'ruteo':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=6;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    case 'etnias':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=5;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    case 'agendamiento':
      $sql="SELECT valor FROM `catadeta` WHERE idcatalogo='224' and estado='A' and idcatadeta=7;";
      $info=datos_mysql($sql);
      $dias=$info['responseResult'][0]['valor'];
    break;
    default:
      $dias=-4;
      break;
  }
  return intval($dias);
}

function datos_mysql($sql, $resulttype = MYSQLI_ASSOC, $pdbs = false, $params = []) {
  $arr = ['code' => 0, 'message' => '', 'responseResult' => []];
  $con = $GLOBALS['con'];
  $usuario = isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : 'Usuario Desconocido';
  if (!$con) {
    $arr['code'] = 30;
    $arr['message'] = 'No hay conexión activa a la base de datos.';
    log_error($usuario . ' = Connection error');
    return $arr;
  }
    try {
        $con->set_charset('utf8');
        if ($params && is_array($params) && count($params) > 0) {
            // --- Consulta preparada ---
            $stmt = $con->prepare($sql);
      if (!$stmt) {
        log_error($usuario . ' Error preparando: ' . $con->error);
        throw new mysqli_sql_exception("Error preparando: " . $con->error, $con->errno);
      }
            $types = '';
            $values = [];
            foreach ($params as $param) {
                $types .= $param['type'];
                $values[] = $param['value'];
            }
            // bind_param requiere referencias
            $bind_names[] = $types;
            for ($i=0; $i<count($values); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $values[$i];
                $bind_names[] = &$$bind_name;
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_names);

      if (!$stmt->execute()) {
        log_error($usuario . ' Error ejecutando: ' . $stmt->error);
        throw new mysqli_sql_exception("Error ejecutando: " . $stmt->error, $stmt->errno);
      }
            $result = $stmt->get_result();
            if ($result) {
                while ($r = $result->fetch_array($resulttype)) {
                    $arr['responseResult'][] = $r;
                }
                $result->free();
            } else {
                // Para sentencias que no retornan resultado (INSERT/UPDATE/DELETE)
                $arr['responseResult'][] = ['affected_rows' => $stmt->affected_rows];
            }
            $stmt->close();
        } else {
            // --- Consulta directa (sin parámetros) ---
            $rs = $con->query($sql);
      if (!$rs) {
        log_error($usuario . ' Error en la consulta: ' . $con->error, $con->errno);
        throw new mysqli_sql_exception("Error en la consulta: " . $con->error, $con->errno);
      }
            fetch($con, $rs, $resulttype, $arr);
        }
  } catch (mysqli_sql_exception $e) {
    $arr['code'] = 30;
    $arr['message'] = 'Error BD';
    $arr['errors'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
    log_error($usuario.'=>'.$e->getCode().'='.$e->getMessage());
  }
    return $arr;
}

function fetch(&$con, &$rs, $resulttype, &$arr) {
	if ($rs === TRUE) {
		$arr['responseResult'][] = ['affected_rows' => $con->affected_rows];
	}else {
		if ($rs === FALSE) {
			die(json_encode(['code' => $con->errno, 'message' => $con->error]));
		}
		while ($r = $rs->fetch_array($resulttype)) {
			$arr['responseResult'][] = $r;
		}
		$rs->free();
	}
	return $arr;
}

//Función para obtener una sola fila
function datos_mysql_row($sql, $params = [], $resulttype = MYSQLI_ASSOC) {
    $arr = datos_mysql($sql, $resulttype, false, $params);
    return isset($arr['responseResult'][0]) ? $arr['responseResult'][0] : null;
}

//Function opc
function getSelectOptions($sql, $idField = 'id', $labelField = 'name') {
    $result = datos_mysql($sql);
    $data = [];
    if (isset($result['responseResult']) && is_array($result['responseResult'])) {
        foreach ($result['responseResult'] as $row) {
            $data[] = [
                'value' => $row[$idField],
                'label' => $row[$labelField]
            ];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// --- UTILIDADES DE SEGURIDAD AVANZADAS ---
// Extrae el token Bearer del header Authorization
function getBearerToken() {
  $headers = getallheaders();
  if (isset($headers['Authorization']) && stripos($headers['Authorization'], 'Bearer ') === 0) {
    return trim(substr($headers['Authorization'], 7));
  }
  if (isset($_SERVER['HTTP_AUTHORIZATION']) && stripos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ') === 0) {
    return trim(substr($_SERVER['HTTP_AUTHORIZATION'], 7));
  }
  return null;
}

// Valida un JWT y retorna el payload si es válido Opcionalmente, valida si el token es requerido y si tiene el scope necesario.
function validateJWT($jwt = null, $required = false, $requiredScope = null) {
  if (!$jwt) $jwt = getBearerToken();
  if (!$jwt) {
    if ($required) {
      http_response_code(401);
      echo json_encode(['success' => false, 'error' => 'Token requerido']);
      exit;
    }
    return false;
  }
  $jwt_secret = $_ENV['JWT_SECRET'] ?? ($_ENV['JWT_SECRET_default'] ?? '');
  try {
    $payload = jwt_decode($jwt, $jwt_secret);
    if (!$payload) throw new Exception('Token inválido');
    if (isset($payload['exp']) && $payload['exp'] < time()) {
      if ($required) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Token expirado']);
        exit;
      }
      return false;
    }
    // Validar scope si se requiere
    if ($requiredScope) {
      $tokenScopes = [];
      if (isset($payload['scope'])) {
        if (is_array($payload['scope'])) {
          $tokenScopes = $payload['scope'];
        } else {
          $tokenScopes = explode(' ', (string)$payload['scope']);
        }
      }
      $requiredScopes = is_array($requiredScope) ? $requiredScope : [$requiredScope];
      $missing = array_diff($requiredScopes, $tokenScopes);
      if (!empty($missing)) {
        if ($required) {
          http_response_code(403);
          echo json_encode(['success' => false, 'error' => 'Permiso insuficiente (scope requerido)']);
          exit;
        }
        return false;
      }
    }
    return $payload;
  } catch (Exception $e) {
    log_error('JWT inválido: ' . $e->getMessage());
    if ($required) {
      http_response_code(401);
      echo json_encode(['success' => false, 'error' => 'Token inválido']);
      exit;
    }
    return false;
  }
}

// Valida el CSRF token (POST)
function validateCSRF() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
      log_error('CSRF token inválido o ausente');
      http_response_code(403);
      echo json_encode(['success' => false, 'error' => 'CSRF token inválido o ausente']);
      exit;
    }
  }
}

// Sanitiza un array de entrada (por ejemplo $_POST o $_GET)
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
      // Limitar longitud si se especifica
      if ($maxLength !== null && is_string($val)) {
        $val = mb_substr($val, 0, $maxLength, 'UTF-8');
      }
      // Saneamiento básico
      $val = htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      // Opcional: eliminar caracteres de control invisibles
      $val = preg_replace('/[\x00-\x1F\x7F]/u', '', $val);
      $out[$k] = $val;
    }
  }
  return $out;
}

// Valida campos requeridos y tipos básicos
function validateInput($data, $rules) {
    $errors = [];
    foreach ($rules as $field => $ruleStr) {
        $value = $data[$field] ?? null;
        $rules = explode('|', $ruleStr);
        foreach ($rules as $rule) {
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
            // Validar fecha
            if ($rule === 'date' && $value !== null) {
                $d = DateTime::createFromFormat('Y-m-d', $value);
                if (!$d || $d->format('Y-m-d') !== $value) {
                    $errors[$field] = "El campo $field debe ser una fecha válida (YYYY-MM-DD)";
                    break;
                }
            }
            // Validar min/max para números
            if (preg_match('/min:(\d+)/', $rule, $matches) && is_numeric($value) && $value < $matches[1]) {
                $errors[$field] = "El campo $field debe ser al menos " . $matches[1];
                break;
            }
            if (preg_match('/max:(\d+)/', $rule, $matches) && is_numeric($value) && $value > $matches[1]) {
                $errors[$field] = "El campo $field debe ser como máximo " . $matches[1];
                break;
            }
            // Validar minlen/maxlen para strings
            if (preg_match('/minlen:(\d+)/', $rule, $matches) && is_string($value) && strlen($value) < $matches[1]) {
                $errors[$field] = "El campo $field debe tener al menos " . $matches[1] . " caracteres";
                break;
            }
            if (preg_match('/maxlen:(\d+)/', $rule, $matches) && is_string($value) && strlen($value) > $matches[1]) {
                $errors[$field] = "El campo $field debe tener como máximo " . $matches[1] . " caracteres";
                break;
            }
            // Validar patrones regex
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