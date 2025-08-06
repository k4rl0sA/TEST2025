<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
// session_regenerate_id(true);
session_start();
ini_set('display_errors','1');
setlocale(LC_TIME, 'es_CO');
// $GLOBALS['app']='sds';
ini_set('memory_limit','1024M');
date_default_timezone_set('America/Bogota');
setlocale(LC_ALL,'es_CO');
$APP='GTAPS';
if (!isset($_SESSION["us_sds"])) {
  header("Location: /index.php"); 
  exit;
}
$ruta_upload='/public_html/upload/';

$dom = $_SERVER['HTTP_HOST'];
$dominio = preg_replace('/^www\./i', '', $dom);
$comy = array(
  'pruebagtaps.site' => [
      's' => 'localhost',
      'u' => 'u470700275_17',
      'p' => 'z9#KqH!YK2VEyJpT',
      'bd' => 'u470700275_17'
  ],
  'gitapps.site' => [
      's' => 'localhost',
      'u' => 'u470700275_08',
      'p' => 'z9#KqH!YK2VEyJpT',
      'bd' => 'u470700275_08'
  ],
  'gtaps.saludcapital.gov.co' => [
      's' => '10.234.8.132',
      'u' => 'u470700275_08',
      'p' => 'z9#KqH!YK2VEyJpT',
      'bd' => 'saludencasa_pru'
  ]
);
// var_dump($dominio);
$allowed_domains = ['pruebagtaps.site', 'gitapps.site'];
if (in_array($dominio, $allowed_domains)) {
  $dbConfig = $comy[$dominio];
}else{
  die('Dominio no permitido.');
}
$con=mysqli_connect($dbConfig['s'],$dbConfig['u'],$dbConfig['p'],$dbConfig['bd']);

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

function log_error($message) {
    $timestamp = date('Y-m-d H:i:s');
    $marca = date('Y-m-d H:i:s');
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    $logMessage = "[$marca] - ".usuSess()." = $message" . PHP_EOL;
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
	$rta=array();
	$sql="SELECT perfil,componente,crear,editar,consultar,ajustar,importar FROM adm_roles WHERE modulo = '".$a."' and perfil = FN_PERFIL('".$_SESSION['us_sds']."') AND componente=FN_COMPONENTE('".$_SESSION['us_sds']."') AND estado = 'A'";
	$data=datos_mysql($sql);
  //print_r($data);
	if ($data && isset($data['responseResult'][0])) {
        $rta = $data['responseResult'][0];
  }
	return $rta;
}

function perfil($a){
	$perf=rol($a);
	//  var_dump($perf);
	if (empty($perf['perfil']) || $perf['perfil'] === array()){
		echo "<H1>ACCESO NO AUTORIZADO,PARA {$a} VALIDA TUS PERMISOS CON EL ADMINISTRADOR DEL SISTEMA</H1><div class='messag rtawarn'></div>";
		exit();
		 }
}

function acceso($a){
  $acc=rol($a);
  // print_r($acc);
  if (!empty($acc['perfil'])){
    return true;
  }else{
    return;
  }
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
    if (!$con) {
        $arr['code'] = 30;
        $arr['message'] = 'No hay conexión activa a la base de datos.';
        log_error($_SESSION["us_sds"] . ' = Connection error');
        return $arr;
    }
    try {
        $con->set_charset('utf8');
        if ($params && is_array($params) && count($params) > 0) {
            // --- Consulta preparada ---
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                log_error($_SESSION["us_sds"] . ' Error preparando: ' . $con->error);
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
                log_error($_SESSION["us_sds"] . ' Error ejecutando: ' . $stmt->error);
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
                log_error($_SESSION["us_sds"] . ' Error en la consulta: ' . $con->error, $con->errno);
                throw new mysqli_sql_exception("Error en la consulta: " . $con->error, $con->errno);
            }
            fetch($con, $rs, $resulttype, $arr);
        }
    } catch (mysqli_sql_exception $e) {
        $arr['code'] = 30;
        $arr['message'] = 'Error BD';
        $arr['errors'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
        log_error($_SESSION["us_sds"].'=>'.$e->getCode().'='.$e->getMessage());
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