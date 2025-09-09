<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}
ini_set('display_errors','1');
setlocale(LC_TIME, 'es_CO');
// $GLOBALS['app']='sds';
ini_set('memory_limit','1024M');
date_default_timezone_set('America/Bogota');
setlocale(LC_ALL,'es_CO');
$APP='GTAPS';
/* if (!isset($_SESSION["us_sds"])) {
  header("Location: /index.php"); 
  exit;
} */
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
  die('Faltan variables de entorno para la base de datos.');
}

$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);


// --- Validación estricta de dominio permitido: SIEMPRE se requiere Origin y debe estar permitido ---
$allowed_domains = array_map('strtolower', array_map('trim', explode(',', $_ENV['ALLOWED_DOMAINS'] ?? 'localhost')));
if (!isset($_SERVER['HTTP_ORIGIN'])) {
  http_response_code(403);
  echo json_encode(['success'=>false, 'error'=>'Dominio no permitido (Origin requerido)']);
  exit;
}
$origin = $_SERVER['HTTP_ORIGIN'];
$parsed = parse_url($origin);
$origin_host = strtolower($parsed['host'] ?? '');
$permitido = in_array($origin_host, $allowed_domains, true);
if ($permitido) {
  header('Access-Control-Allow-Origin: ' . $origin);
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
  }
} else {
  http_response_code(403);
  echo json_encode(['success'=>false, 'error'=>'Dominio no permitido']);
  exit;
}

if (!$con) { $error = mysqli_connect_error();  exit; }
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