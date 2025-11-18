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
      'bd' => 'saludencasa_migrada'
  ]
);
// var_dump($dominio);
$allowed_domains = ['pruebagtaps.site', 'gitapps.site','gtaps.saludcapital.gov.co'];
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
			/* echo $cabecera;
			echo "Archivo <b>" . $_POST['b'] . "</b>" . $ar . "<br>";
			echo "<center>";
			echo "<div id='progress-ordennovedadvalor'></div>";
			$GLOBALS['def_' . $tb] = define_objeto($tb, $_REQUEST['d']);
			if (isset($GLOBALS['def_' . $tb]))
				importar($tb, $fi, $_REQUEST['d']);
			echo "<input type=button value='Continuar' OnClick=\"retornar('" . $cr . "','" . $ar . ".csv')\" >";
			echo "</center>"; */
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

function validCsrfTok() {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    log_error($_SESSION["us_sds"].' = Invalid CSRF token');
  return die("Error: msj['Invalid CSRF token']");
  // exit;
  }else{
    return die("Error: msj['OK']");
  }
}

/* function log_error($message) {
  if (!is_dir('../logs')) {
    mkdir('../logs', 0777, true);
}
  file_put_contents('../logs/file.log', "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL, FILE_APPEND);
} */

function usuSess(){
  return $usu = isset($_SESSION['us_sds']) ? $_SESSION['us_sds'] : 'Usuario Desconocido';
}
function log_error($message) {
  $timestamp = date('Y-m-d H:i:s');
  $marca = date('Y-m-d H:i:s'); 
  $logMessage = "[$marca] - ".usuSess()." = $message" . PHP_EOL;
  try {
      file_put_contents(__DIR__ . '/../logs/file.log', $logMessage, FILE_APPEND);
  } catch (Throwable $e) {
      file_put_contents(__DIR__ . '/../logs/errors_backup.log', "[$marca] Error al registrar: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
  }
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

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
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
function validEmail($email) {
    if (empty($email)) {
        return "msj['Error: El correo electrónico es obligatorio.']";
    }
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "msj['Error: El correo electrónico no es válido.']";
    }
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    if (!preg_match($pattern, $email)) {
        return "msj['Error: El correo electrónico no tiene el formato correcto.']";
    }
    return true;
}

function cleanTx($val) {
  $val = trim($val);
  $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');//maneja las inyecciones XSS
  // Permite letras, números, espacios, punto, guion, arroba, dos puntos, caracteres acentuados latinos, y el símbolo de porcentaje y igual.
  $pattern = '/[^\w\s\.\-@:,()+\x{00C0}-\x{00FF}%=]/u'; // UTF-8 para incluir caracteres especiales
  $val = preg_replace('/\s+/', ' ', $val); // Remover múltiples espacios
  $val = preg_replace($pattern, '', $val); // Quitar caracteres no permitidos
  $val = str_replace(array("\n", "\r", "\t"), '', $val); // Eliminar saltos de línea y tabulaciones
  $val = mb_strtoupper($val, 'UTF-8'); // Convierte a mayúsculas conservando acentos
  return $val;
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


function datos_mysql($sql,$resulttype = MYSQLI_ASSOC, $pdbs = false){
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
  $rs = $con->query($sql);
  if (!$rs) {
    log_error($_SESSION["us_sds"] . ' Error en la consulta: ' . $con->error, $con->errno);
    throw new mysqli_sql_exception("Error en la consulta: " . $con->error, $con->errno);
  }
  fetch($con, $rs, $resulttype, $arr);
} catch (mysqli_sql_exception $e) {
  echo json_encode(['code' => 30, 'message' => 'Error BD', 'errors' => ['code' => $e->getCode(), 'message' => $e->getMessage()]]);
  if($e->getCode()!=1062)log_error($_SESSION["us_sds"].'=>'.$e->getCode().'='.$e->getMessage().' SQL: '.$sql);
}finally {
  // $GLOBALS['con']->close();
}
return $arr;
}

function dato_mysql($sql, $resulttype = MYSQLI_ASSOC, $pdbs = false) {
  $arr = ['code' => 0, 'message' => '', 'responseResult' => []];
  $con = $GLOBALS['con'];
  $con->set_charset('utf8');

  try {
      if (strpos($sql, 'DELETE') !== false) {
          $op = 'Eliminado';
      } elseif (strpos($sql, 'INSERT') !== false) {
          $op = 'Insertado';
      } else {
          $op = 'Actualizado';
      }

      if (!$con->query($sql)) {
          $err = $con->error;
          $con->query("ROLLBACK;");
          if ($con->error == '') {
              $rs = "Error: " . $err;
          } else {
              $rs = "Error: " . $err . " Ouchh! NO se modificó ningún registro, por favor valide la información e intente nuevamente.";
          }
      } else {
          if ($con->affected_rows > 0) {
              $rs = "Se ha " . $op . ": " . $con->affected_rows . " Registro Correctamente.";
          } else {
              $rs = "Ouchh!, NO se ha " . $op . ", por favor valide la información e intente nuevamente.";
          }
      }
  } catch (mysqli_sql_exception $e) {
    log_error($_SESSION["us_sds"].'=>'.$e->getCode().'='.$e->getMessage());
    $rs = "Error = " . $e->getCode() . " " . $e->getMessage();
  }
  return $rs;
}

function params($campos) {
  // validCsrfTok();
  $params = [];
  foreach ($campos as $campo) {
      if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
          $params[] = array('type' => is_numeric($_POST[$campo]) ? 'i' : 's', 'value' => $_POST[$campo]);
      } else {
          $params[] = array('type' => 's', 'value' => NULL);
      }
  }
  return $params;
}

function param_null($value, $type = 's') {
    $is_empty = ($value === '' || $value === null);
    return [
        'type' => $is_empty ? 'z' : $type,
        'value' => $is_empty ? null : $value
    ];
}

function mysql_prepd($sql, $params) {
  // validCsrfTok();
  $arr = ['code' => 0, 'message' => '', 'responseResult' => []];
  $con = $GLOBALS['con'];
  $con->set_charset('utf8');
  try {
      $stmt = $con->prepare($sql);
      if ($stmt) {
          $types = '';
          $values = [];
          foreach ($params as $param) {
              $type = $param['type'];
              if ($type === 's' && $param['value'] === NULL) {
                  $types .= 's'; // Agregar tipo 's' para NULL
                  $values[] = NULL; // No limpiar, solo agregar NULL
              } else {
                if ($type === 'z') {
                    $value = $param['value']; // Dejar el valor sin limpiar ni modificar
                    $types .= 's'; // Tratar 'z' como 's'
                } else {
                    $value = ($type === 's') ? cleanTx(strtoupper($param['value'])) : cleanTx($param['value']);
                    $types .= $type;
                }
                $values[] = $value; // Agregar el valor limpio
              }
          }
          $num_placeholders = substr_count($sql, '?');
          $num_params = count($values);
          if ($num_placeholders !== $num_params) {
            log_error($_SESSION["us_sds"].'=>'."Error: El número de placeholders (?) no coincide con el número de parámetros.");
            die("Error: El número de placeholders (?) no coincide con el número de parámetros.");
          }

          // var_dump($values); // Para depurar valores y tipos

          $stmt->bind_param($types, ...$values);
          if (!$stmt->execute()) {
              $rs = "Error al ejecutar la consulta: " . $stmt->error . " | SQL: " . $sql;
              log_error($_SESSION["us_sds"].'=>'."Error al ejecutar la consulta: ". $stmt->error . " | SQL: " . $sql);
          } else {
              $sqlType = strtoupper($sql);
              if (strpos($sqlType, 'DELETE') !== false) {
                  $op = 'Eliminado';
              } elseif (strpos($sqlType, 'INSERT') !== false) {
                  $op = 'Insertado';
              } elseif (strpos($sqlType, 'UPDATE') !== false) {
                  $op = 'Actualizado';
              } else {
                  $op = 'Operación desconocida';
              }
              $affected = $stmt->affected_rows;
              if ($affected > 0) {
                  $rs = "Se ha " . $op . ": " . $affected . " registro(s) correctamente.";
              } else {
                  $rs = "No se afectaron registros con la operación: " . $op;
              }
          }
          $stmt->close();
      } else {
        log_error($_SESSION["us_sds"].'=>'."Error al ejecutar la consulta: ". $con->error . " | SQL: " . $sql);
          $rs = "Error preparando la consulta: " . $con->error . " | SQL: " . $sql;
      }
  } catch (mysqli_sql_exception $e) {
    log_error($_SESSION["us_sds"].'=> '.$e->getCode() . " = " . $e->getMessage());
      $rs = "Error = " . $e->getCode() . " " . $e->getMessage();
  }
  return $rs;
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


function panel_content($data_arr,$obj_name,$rp = 20,$no = array('R')) {
	$rta = "";
	$pg = si_noexiste('pag-'.$obj_name,1);
	$rta.= "<table class='tablesorter' cellspacing=0>";
	if($data_arr!=[]){
		$numeroRegistros = count($data_arr);
		$np = floor(($numeroRegistros - 1) / $rp + 1);
		$ri = ($pg - 1) * $rp;
		$rta.= "<thead>";
		foreach ($data_arr[0] as $key => $cmp) {
			if (!in_array($key,$no)) {
				$rta.= "<th>".htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ."</th>";
			}
		}	
		$rta.= "</thead id='".$obj_name."_cab'>";
		$rta.= "<tbody id='".$obj_name."_fil'>";
		for($idx=$ri; $idx<=($ri + $rp); $idx++){
			if(!isset($data_arr[$idx])){
				break;
			}
			$r = $data_arr[$idx];
			$rta.= "<tr ".bgcolor($obj_name,$r,"r")." >";
			foreach ($data_arr[0] as $key => $cmp) {
				if (!in_array($key,$no)) {
					$rta.= "<td data-tit='".htmlspecialchars($key, ENT_QUOTES, 'UTF-8')."' class='".alinea($r[$key])."' ".bgcolor($obj_name,$r,"c").">";
					$rta.= formato_dato($obj_name,$key,$r,$key );
					$rta.= "</td>";
				}
			}
			$rta.= "</tr>\n";
		}
		$nc = count($data_arr[0]);
		if ($numeroRegistros != 1) {
			// $rta.= "<tr><td class='resumen' colspan=$nc >".menu_reg($obj_name,$pg,$np,$numeroRegistros)."</td></tr>";
      $rta.= "<tr><td class='resumen' colspan=$nc >".pags_table($obj_name,$pg,$np,$numeroRegistros,'')."</td></tr>";
		}
	}
	$rta.= "</tbody>";
	$rta.= "</table>";
	return $rta;
}

function opc_sql($sql,$val,$str=true){
  $val = is_array($val) ? $val[0] ?? '' : (string)$val;
	$rta="<option value class='alerta' >SELECCIONE</option>";
	$con=$GLOBALS['con'];
  // var_dump($con);
	if($con->multi_query($sql)){
	do {
		if ($con->errno == 0) {
			$rs = $con->store_result();
			if ($con->errno == 0) {
				if ($rs != FALSE) {
					while ($r = $rs->fetch_array(MYSQLI_NUM))
						if($r[0]==$val){
							$rta.="<option value='".$r[0]."' selected>".htmlentities($r[1],ENT_QUOTES)."</option>";
						}else{
							$rta.="<option value='".$r[0]."'>".htmlentities($r[1],ENT_QUOTES)."</option>";
						}						
				}
				//~ $con->close();
			}
			//~ $rs->free();
		}
		//~ $con->next_result();//11-01-2020
		} while ($con->more_results() && $con->next_result());
		$rs->free();
	}
	//~ $con->close();
  //$con->close();//16-06-2023
	return $rta;
}


function si_noexiste($a,$b){
  if (isset($_REQUEST[$a]))
	 return $_REQUEST[$a];
  else
	 return $b;
}
function alinea($a){
  if (is_numeric($a)) return 'txt-right';
  elseif (is_numeric(str_replace(",","",$a))) return 'txt-right';
  elseif (strpos($a,'%')>0) return 'txt-right';
  elseif (strlen($a)<=2) return 'txt-center';
  else return 'txt-left';
}

/* function menu_reg($tb,$pg,$np,$nr){
  $rta="<nav class='menu left'>";
  $rta.="<li class='icono regini' OnClick=\"ir_pagina('".$tb."',1,".$np.");\" ></li>";
  $rta.="<li class='icono pgatra' OnClick=\"ir_pagina('".$tb."',$pg-1,".$np.");\"></li>";
  $rta.="<li class='icono pgsigu' OnClick=\"ir_pagina('".$tb."',$pg+1,".$np.");\"></li>";
  $rta.="<li class='icono regfin' OnClick=\"ir_pagina('".$tb."',$np,".$np.");\"></li>&nbsp;";
  $rta.="<input type='text' class='pagina ".$tb." filtro txt-right' maxlength=5 id='pag-".$tb."' value='".$pg."' 
             Onkeypress=\"return solo_numero(event);\" OnChange=\"ir_pagina('".$tb."',this.value,".$np.");\" > ";
  $rta.="<span><b> DE ".$np." PAGINAS ";
  $rta.="<input type='text' class='pagina txt-right' id='rec-".$tb."' value='".$nr."' disabled >"; 
  $rta.=" REGISTROS</b></span>";
  $rta.="</nav><nav class='menu right'>";
  $rta.="<li class='icono regini' OnClick=\"ir_pagina('".$tb."',1,".$np.");\" ></li>";
  $rta.="<li class='icono pgatra' OnClick=\"ir_pagina('".$tb."',$pg-1,".$np.");\"></li>";
  $rta.="<li class='icono pgsigu' OnClick=\"ir_pagina('".$tb."',$pg+1,".$np.");\"></li>";
  $rta.="<li class='icono regfin' OnClick=\"ir_pagina('".$tb."',$np,".$np.");\"></li>";
  $rta.="</nav>";
  return $rta;
}
 */
function create_table($totalReg, $data_arr, $obj_name, $rp = 20,$mod='lib.php', $no = array('R')) {
  $rta = "";
  $pg = si_noexiste('pag-'.$obj_name, 1);
  $rta .= "<table class='tablesorter' cellspacing=0>";
  if (!empty($data_arr)) {
    $np = ceil(($totalReg) / $rp);
    $ri = ($pg - 1) * $rp;
    $rta .= "<thead>";
    foreach ($data_arr[0] as $key => $cmp) {
        if (!in_array($key, $no)) {
           $rta .= "<th>".$key."</th>";
        }
    }
    $rta .= "</thead id='".$obj_name."_cab'>";
    $rta .= "<tbody id='".$obj_name."_fil'>";
    for ($idx = 0; $idx <= ($ri + $rp); $idx++) {
      if (isset($data_arr[$idx])) {
         $r = $data_arr[$idx];
         $rta .= "<tr>"; // No aplicar color a la fila
         foreach ($data_arr[0] as $key => $cmp) {
            if (!in_array($key, $no)) {
               $rta .= "<td data-tit='".$key."' class='".alinea($r[$key])."' ".bgcolor($obj_name, $r, "c",$key).">";
               $rta .= formato_dato($obj_name, $key, $r, $key);
               $rta .= "</td>";
            }
         }
         $rta .= "</tr>\n";
      }
    }
    $nc = count($data_arr[0]);
    if ($totalReg != 1) {
      $rta .= "<tr><td class='resumen' colspan=$nc >".pags_table($obj_name, $pg, $np, $totalReg,$mod)."</td></tr>";
    }
  }
  $rta .= "</tbody>";
  $rta .= "</table>";
  return $rta;
}

function pags_table($tb, $pg, $np, $nr,$mod) {
  $np= ($np>$nr) ? ($np-1) : $np;
  $rta = "<nav class='menu left'>";
  $rta .= "<li class='icono regini' OnClick=\"ir_pag('".$tb."', 1, ".$np.",'".$mod."');\"></li>";
  $rta .= "<li class='icono pgatra' OnClick=\"ir_pag('".$tb."', $pg-1, ".$np.",'".$mod."');\"></li>";
  $rta .= "<li class='icono pgsigu' OnClick=\"ir_pag('".$tb."', $pg+1, ".$np.",'".$mod."');\"></li>";
  $rta .= "<li class='icono regfin' OnClick=\"ir_pag('".$tb."', $np, ".$np.",'".$mod."');\"></li>&nbsp;";
  $rta .= "<input type='text' class='pagina ".$tb." filtro txt-right' maxlength=8 id='pag-".$tb."' value='".$pg."' 
            Onkeypress=\"return solo_numero(event);\" OnChange=\"ir_pag('".$tb."', this.value, ".$np.",'".$mod."');\">";
  $rta .= "<span><b> DE ".$np." PAGINAS ";
  $rta .= "<input type='text' class='pagina txt-right' id='rec-".$tb."' value='".$nr."' disabled>"; 
  $rta .= " REGISTROS</b></span>";
  $rta .= "</nav><nav class='menu right'>";
  $rta .= "<li class='icono regini' OnClick=\"ir_pag('".$tb."', 1, ".$np.");\"></li>";
  $rta .= "<li class='icono pgatra' OnClick=\"ir_pag('".$tb."', $pg-1, ".$np.");\"></li>";
  $rta .= "<li class='icono pgsigu' OnClick=\"ir_pag('".$tb."', $pg+1, ".$np.");\"></li>";
  $rta .= "<li class='icono regfin' OnClick=\"ir_pag('".$tb."', $np, ".$np.");\"></li>";
  $rta .= "</nav>";
  return $rta;
}

  function initializeMail(&$mail, $config) {
    $mail->SMTPDebug = 2;
    $mail->IsSMTP();
    $mail->CharSet = (isset($config['CharSet']) ? $config['CharSet'] : 'UTF-8');
    $mail->SMTPSecure = (isset($config['SMTPSecure']) ? $config['SMTPSecure'] : 'tls');
    $mail->Host = (isset($config['Host']) ? $config['Host'] : 'smtp.gmail.com');
    $mail->Port = (isset($config['Port']) ? $config['Port'] : 587);
    $mail->Username = (isset($config['Username']) ? $config['Username'] : 'gerenciadelainformaciongif@gmail.com');
    $mail->Password = (isset($config['Password']) ? $config['Password'] : 'G3r3nc14+');
    $mail->SMTPAuth = (isset($config['SMTPAuth']) ? $config['SMTPAuth'] : true);
    $mail->IsHTML((isset($config['IsHTML']) ? $config['IsHTML'] : true));
    $mail->From = (isset($config['From']) ? $config['From'] : 'gerenciadelainformaciongif@gmail.com');
    $mail->FromName = (isset($config['FromName']) ? $config['FromName'] : 'Gerencia de la información GIF - SDS');
    $mail->Subject = (isset($config['Subject']) ? $config['Subject'] : 'Correo de gerenciadelainformaciongif@gmail.com');
    $mail->AltBody = (isset($config['AltBody']) ? $config['AltBody'] : 'Utilice un lector de mail apropiado!');
  }
  
  function sendMail($mails, $subject, $body) {
    require_once('../libs/mailer/PHPMailerAutoload.php');
    $mail = new PHPMailer();
    initializeMail($mail, ["Subject" => $subject, "Body" => $body]);
    foreach ($mails as $x) {
      $mail->AddAddress($x);
    }
    $plantilla = "";
    $file = fopen("../libs/plantilla.html", "r");
    while ($buff = fgets($file)) {
      $plantilla .= $buff;
    }
    fclose($file);
    eval("\$mail->Body = \"$plantilla\";");
    if ($mail->Send()) {
      $rta = ["code" => 0, "message" => "Succesfully sent.", "email" => $mail];
    } else {
      $rta = ["code" => 60, "message" => 'Mailer Error, Message could not be sent.', "ErrorInfo" => $mail->ErrorInfo];
    }
    return $rta;
  }

function divide($a){
	$id=explode("_", $a);
	return ($id);
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

function perfil1($a = null) {
  if ($a === null) $a = $_SESSION['us_sds'];
  $per = datos_mysql("SELECT FN_PERFIL({$a}) AS perfil");
  $perfil = $per["responseResult"][0]['perfil'];
  return $perfil;
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

function fieldsRequired($elements, $except = ['observaciones']) {
  foreach ($elements as $field) {
    if (!in_array($field, $except)) {
      if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        return ['error' => "El campo $field es obligatorio."];
      }
    }
  }
  return true;
}

 function myhash($a){
    $hash = md5($a . $_SESSION['us_sds'] . 'D2AC5E5211884EA15F1E950D1445C5E8');
    return $hash;
}

function limpiar_hashes($max = 500) {
    if (!isset($_SESSION['hash']) || !is_array($_SESSION['hash'])) return;
    // Si hay más de $max hashes, elimina los más antiguos
    if (count($_SESSION['hash']) > $max) {
        // Mantén solo los últimos $max elementos
        $_SESSION['hash'] = array_slice($_SESSION['hash'], -$max, $max, true);
    }
}  

function idReal($postId, $sessionHash = [], $suffix = '') {
    $hash_id = $postId ?? '';
    $real_id = null;
    // Si es '0' o vacío, es un nuevo registro
    if (empty($hash_id) || $hash_id === '0') {
        return null;
    }
    // Buscar el ID real en la sesión usando el hash
    if (!empty($sessionHash)) {
        foreach ($sessionHash as $key => $value) {
            // Si se especifica sufijo, buscar con ese sufijo
            if (!empty($suffix)) {
                if (strpos($key, $hash_id . $suffix) !== false) {
                    $real_id = $value;
                    break;
                }
            } else {
                // Búsqueda genérica por hash
                if (strpos($key, $hash_id) !== false) {
                    $real_id = $value;
                    break;
                }
            }
        }
    }
    // Si no encontró en hash, intentar dividir el ID directo
    if (!$real_id) {
        $id_array = divide($hash_id);
        if (is_array($id_array) && isset($id_array[0])) {
            $real_id = $id_array[0];
        }
    }
    return $real_id ? intval($real_id) : null;
}

/*COMPONENTES*/
class cmp { //ntwplcsdxhvuf
  public $n; //1 name
  public $t; //2 type
  public $s; //3 size
  public $d; //4 valor
  public $w; //5 div
  public $l; //6 label
  public $c; //7 list
  public $x; //8 regexp
  public $h; //9 holder
  public $v; //10valid
  public $u; //11update 
  public $tt;//12tittle
  public $ww;//13width field
  public $vc;//14Validaciones personalizadas
  public $sd;//15Select dependientes
  public $so;//16Validaciones personalizadas otro evento
  function __construct($n='dato',$t='t',$s=10,$d='',$w='div',$l='',$c='',$x='rgxtxt',$h='..',$v=true,$u=true,$tt='',$ww='col-10',$vc=false,array $sd=array(''),$so=false,$path='lib.php',$min=null) {
    $this->n=$n; 
    $this->t=$t; 
    $this->w=$w;  
    $this->l=($l==''?$n:$l); 
    $this->c=$c;  
    $this->s=$s;  // max
    $this->min=$min; // min opcional
    $this->d=$d;  
    $this->x=($x==null?($t=='n'?'rgxdfnum':'rgxtxt'):$x);  
    $this->h=$h;  
    $this->v=$v;       
    $this->u=$u;
    $this->tt=$tt;
    $this->ww=$ww;    
    $this->vc=$vc;    
    $this->sd=$sd;
    $this->so=$so;
    $this->path=$path;
  }
  function put(){    
    switch ($this->t) {
    case 's':
        $b=input_sel($this);
		break;
    case 'o':
		$b=input_opt($this);
		break;    
    case 'a':
        $b=input_area($this);
		break;
	case 'd':
		 $b=input_date($this);
		break;
	case 'e':
		 $b=encabezado($this);
		break;
	case 'l':
		 $b=subtitulo($this);
		break;
	case 'c':
		 $b=input_clock($this);
     break;
  case 'm':
      $b=select_mult($this);
		break;
  case 'mu':
      $b=select_multi($this);
		break;
  case 'nu':
      $b = input_num($this);
      break;
  case 'em':
      $b = input_email($this);
      break;
    default:
        $b=input_txt($this);
    }    
    return $b."</div>";
  }
}

function input_sel($a){
  $rta="<div class='campo {$a->w} {$a->ww} borde1 oscuro'><div>{$a->l}</div>";
  $rta.="<select ";
  $rta.=" id='{$a->n}'";
  $rta.=" name='{$a->n}'";  
  $rta.=" class='{$a->w} captura  ";
  $rta.= ($a->v==true) ? 'valido' : '';
  $rta.=" title='{$a->tt}'";
  $rta.=" required onChange=\"";
  if ($a->v) $rta.="valido(this);";
  if ($a->vc!=false) $rta.="{$a->vc}(this);";	
  $rta.="\"";  
  if (!$a->u) $rta.=" disabled='true' ";
  for($i=0;$i<count($a->sd);$i++){
	if ($i==0){
		if ($a->sd[$i]!='') $rta.=" onblur=\"changeSelect('{$a->n}','{$a->sd[$i]}','{$a->path}');";
	}else{
		if ($a->sd[$i]!='') $rta.="changeSelect('{$a->n}','{$a->sd[$i]}','{$a->path}');";
	}
  }
  if ($a->so)$rta.=" OnChange='{$a->so}(this)'";
  $rta.="\"";
  $opc="opc=opc_{$a->c}('$a->d');";
  eval('$'.$opc);
  $rta.=">$opc</select>";	
  return $rta;
}

function select_mult($a){
  $rta="<div class='campo {$a->w} {$a->ww} borde1 oscuro'><div>{$a->l}</div>";
  $rta.="<input type='search' id='{$a->n}' class='mult' placeholder='-- SELECCIONE --' onClick='showMult(this,true);' onSearch='searchMult(this);'>"; 
  $rta.="<select multiple";
  $rta.=" id='f{$a->n}'";
  $rta.=" name='f{$a->n}'";  
  $rta.=" class='{$a->w} captura check mult close ";
  $rta.= ($a->v==true) ? 'valido ' : '';
  if (!$a->u) $rta.="' disabled='true ' ";
  $rta.="' onBlur='showMult(this,false);'";
   $rta.=" required onChange=\"";
  if ($a->v) $rta.="valido(this);";
  if ($a->vc!=false) $rta.="{$a->vc}(this);";	
  $rta.="\"";  
  for($i=0;$i<count($a->sd);$i++){
	  if ($i==0){
		  if ($a->sd[$i]!='') $rta.=" OnChange=\"changeSelect('{$a->n}','{$a->sd[$i]}');";
	  }else{
		  if ($a->sd[$i]!='') $rta.="changeSelect('{$a->n}','{$a->sd[$i]}');";
	  }
  }
  if ($a->so)$rta.=" OnChange='{$a->so}(this)'";
  $rta.="\"";
  $opc="opc=opc_{$a->c}('$a->d');";
  eval('$'.$opc);
  $rta.=">$opc</select>";	
  return $rta;
}

function select_multi($a) {
  // Sanitizar todas las entradas
  $w = saniti($a->w);$ww = saniti($a->ww);$n = saniti($a->n);$x = saniti($a->x);$vc = saniti($a->vc);$so = saniti($a->so);$l = saniti($a->l);$u = $a->u; // Propiedad para habilitar/deshabilitar el campo
  // Construir el HTML
  $rta = "<div class='campo {$w} {$ww} borde1 oscuro'><div>{$l}</div>";
  $rta .= "<input type='search' id='{$n}' class='mult' placeholder='-- SELECCIONE --' onclick='showMult(this,true);' onsearch='searchMult(this);'" . (!$u ? " disabled" : "") . ">";
  $rta .= "<select multiple id='f{$n}' name='f{$n}' class='{$w} captura check mult close " . ($a->v ? 'valido' : '') . "' onblur='showMult(this,false);'";
  $rta .= " required onchange=\"";
  if ($a->v) $rta .= "if(valido(this))";
  if ($x) $rta .= "solo_reg(this,{$x});";
  if ($vc) $rta .= "{$vc}(this);";
  $rta .= "\"";
  if (!empty($a->sd)) {
      $rta .= " onchange=\"";
      foreach ($a->sd as $dep) {
          if ($dep) $rta .= "changeSelect('{$n}','" . saniti($dep) . "');";
      }
      if ($so) $rta .= "{$so}(this);";
      $rta .= "\"";
  }
  $rta .= (!$u ? " disabled" : "") . ">"; // Deshabilitar el select si $u es false
  // Obtener las opciones del select
  $func = "opc_{$a->c}";
  $opc = '';
  if (function_exists($func)) {
      $opc = call_user_func($func, saniti($a->d));
  }
  $rta .= $opc . "</select></div>";
  // Agregar lógica de Choices.js si el campo está deshabilitado
  if (!$u) {
      $rta .= "<script>
          document.addEventListener('DOMContentLoaded', function() {
              const selectElement = document.getElementById('f{$n}');
              const choices = new Choices(selectElement);
              choices.disable(); // Desactivar el campo con Choices.js
          });
          alert('El campo {$n} está deshabilitado.');
      </script>";
  }
  return $rta;
}

function input_opt($a){
  $rta=($a->ww!='col-9')? "<div class='campo {$a->w} {$a->ww} borde1 oscuro'>" : 
  "<div class=\"campo {$a->w} {$a->ww} borde1 oscuro\" style=\"height:20px;\">";
  $rta.="<div>{$a->l}</div>";
  $rta.=($a->ww=='col-9') ? "<div class=\"chk\" style=\"left: 100%;top:-16px;\">" : "<div class='chk'\">";
  $rta.="<input type='checkbox' ";
  $rta.=" id='{$a->n}'";
  $rta.=" name='{$a->n}'";  
  $rta.=" class='{$a->w} captura ";
  if($a->vc) $rta.="validar";
  $rta.="' title='{$a->tt}'";
  if (!$a->u) $rta.=" readonly ";
  if($a->d=='SI') {
	$rta.=" checked value ='SI'"; 
  }else{
	  $rta.=" value='NO'";   
  }
  $rta.=" Onclick=\"checkon(this);";
   if ($a->vc!=false) $rta.="{$a->vc};";
  $rta.="\"><label for='{$a->n}'></label></div>"; 
  return $rta;	
}

function input_area($a){
  $rta="<div class='campo {$a->w} {$a->ww} borde1 oscuro'><div>{$a->l}</div>";
  $rta.="<textarea ";
  $rta.=" id='{$a->n}'";
  $rta.=" name='{$a->n}'";  
  $rta.=" cols='{$a->s}'";
  $rta.=" title='{$a->tt}'";  
  $rta.=" class='{$a->w} ".($a->v?'valido':'')." ".($a->u?'captura':'bloqueo')." '";
  if (!$a->u) $rta.=" readonly ";
  if ($a->v) $rta.=" required onblur=\"valido(this);\" ";
  $rta.=" onkeypress='countMaxChar(this);' Style='width:95%;'";
  $rta.=">".$a->d;
  $rta.="</textarea>";
  return $rta;	
}

function input_txt($a){
  $rta="";
  $t=($a->t=='h'?'hidden':'text');
  if ($a->t!='h') $rta="<div class='campo {$a->w} {$a->ww} borde1 oscuro'><div>{$a->l}</div>";  
  if ($a->t=='fhms') {$a->x='rgxdatehms';$a->h='YYYY-MM-DD HH:MM:SS';$a->s=19;}
  if ($a->t=='fhm')  {$a->x='rgxdatehm';$a->h='YYYY-MM-DD HH:MM';$a->s=16;}
  if ($a->t=='hm')   {$a->x='rgxtime';$a->h='HH:MM';$a->s=5;}
  if ($a->t=='f')    {$a->x='rgxdate';$a->h='YYYY-MM-DD';$a->s=10;}
  $rta.="<input type='$t' ";
  $rta.=" id='{$a->n}'";
  $rta.=" name='{$a->n}'";  
  $rta.=" maxlength='{$a->s}'";  
  $rta.=" title='{$a->tt}'";
  $rta.=" pattern='{$a->x}'";  
  $rta.=" class='{$a->w} ".($a->v?'valido':'')." ".($a->u?'captura':'bloqueo')." ".($a->t=='t'?'':'txt-right')."'";
  if (!$a->u) $rta.=" readonly ";
  if ($a->t!='h') {
      $rta.=" required ";
	  $rta.=" onblur=\"";	  
	  if ($a->v) $rta.="valido(this);";
	  if ($a->x!='') $rta.="solo_reg(this,{$a->x});";
	  if ($a->vc!=false) $rta.="{$a->vc}(this);";	  	  
	  $rta.="\"";
  }	  
  if ($a->t=='n') $rta.="onkeypress=\"return solo_numero(event);\" ";
  if ($a->t=='sd') $rta.="onkeypress=\"return solo_numeroFloat(event);\" ";
  if (strpos($a->t,'f')>-1) $rta.="onkeypress=\"return solo_fecha(event);\" ";    
  if ($a->c!='') $rta.=" list='lista_{$a->c}'"; 
  if ($a->h!='') $rta.=" placeholder='{$a->h}'"; 
  $rta.=" value=\"{$a->d}\" ";
  // $rta.=" onfocus=\"evalue(this);\" ";
  $rta.=">";
  return $rta;	
}  

function input_date($a) {
  $name = htmlspecialchars($a->n, ENT_QUOTES, 'UTF-8');
  $label = htmlspecialchars($a->l, ENT_QUOTES, 'UTF-8');
  $title = htmlspecialchars($a->tt, ENT_QUOTES, 'UTF-8');
  $value = htmlspecialchars($a->d, ENT_QUOTES, 'UTF-8');
  $rta = "<div class='campo {$a->w} {$a->ww} borde1 oscuro'><div>{$label}</div>";
  $rta .= "<input type='date' ";
  $rta .= " id='{$name}'";
  $rta .= " name='{$name}'";
  $rta .= " class='{$a->w} " . ($a->v ? 'valido' : '') . " " . ($a->u ? 'captura' : 'bloqueo') . " " . ($a->t == 't' ? '' : 'txt-right') . "'";
  $rta .= " title='{$title}'";
  if ($a->vc != false) $rta .= "onfocus=\"{$a->vc};\"";
  if ($a->so != false) $rta .= "onchange=\"{$a->so};\"";
  if (!$a->u) $rta .= " readonly ";
  if ($value != '') $rta .= " value=\"{$value}\" ";
  $rta .= ">";
  return $rta;
}

function input_clock($a){
  $rta = "<div class='campo {$a->w} {$a->ww} borde1 oscuro'><div>{$a->l}</div>";
  $rta .= "<input type='time' ";
  $rta .= " id='{$a->n}'";
  $rta .= " name='{$a->n}'";  
  $rta .= " class='{$a->w} " . ($a->v ? 'valido' : '') . " " . ($a->u ? 'captura' : 'bloqueo') . " " . ($a->t == 't' ? '' : 'txt-right') . "'";
  $rta .= " title='{$a->tt}'";
  if (!$a->u) $rta .= " readonly ";
  if ($a->d != '') $rta .= " value=\"{$a->d}\" ";
  $rta .= ">";
  return $rta;	
}

function encabezado($a){
  $rta="<div class='encabezado {$a->n}'>{$a->d}<div class='text-right'><li class='icono desplegar-panel' id='{$a->n}' title='ocultar' onclick=\"plegarPanel('{$a->w}','{$a->n}');\"></li></div></div>";
  return $rta;	
}

function subtitulo($a){
 $rta="<div class='subtitulo {$a->n}'>{$a->d}</div>";
  return $rta;	
}

function saniti($value) {
  return htmlspecialchars($value === null ? '' : $value, ENT_QUOTES, 'UTF-8'); // Manejando valor nulo
}

function input_num($a){
  $name = saniti($a->n);
  $label = ($a->l);
  $value = is_numeric($a->d) ? $a->d : '';
  $title = saniti($a->tt);
  $x = saniti($a->x);
  $a->w = $a->w ?? '';
  $a->ww = $a->ww ?? '';
  $a->s = is_numeric($a->s) ? $a->s : ''; // max
  $a->min = isset($a->min) && is_numeric($a->min) ? $a->min : '';
  $a->v = $a->v ?? false;
  $a->u = $a->u ?? true;
  $a->t = $a->t ?? '';
  $a->vc = $a->vc ?? '';
  $a->so = $a->so ?? '';
  $rta = "<div class='campo " . saniti($a->w) . " " . saniti($a->ww) . " borde1 oscuro'>";
  $rta .= "<div>{$label}</div>";
  $rta .= "<input type='number' id='{$name}' name='{$name}'";
  if ($a->s !== '') $rta .= " max='" . saniti($a->s) . "'";
  if ($a->min !== '') $rta .= " min='" . saniti($a->min) . "'";
  $rta .= " class='" . saniti($a->w) . " " . ($a->v ? 'valido' : '') . " " . ($a->u ? 'captura' : 'bloqueo') . " " . ($a->t == 't' ? '' : 'txt-right') . "'";
  $rta .= " title='{$title}'";
  $rta .= " oninput=\"this.value = this.value.replace(/[eE]/g, '')\"";
  $rta .= " onblur=\"";
  if ($a->v) $rta .= "if(valido(this));";
  if ($a->x) $rta .= "solo_reg(this," . saniti($a->x) . ");";
  if ($a->vc !== '') $rta .= "{$a->vc}(this);";
  $rta .= "\"";
  if ($a->so !== '') $rta .= " onchange=\"" . saniti($a->so) . "\"";
  if (!$a->u) $rta .= " readonly";
  if ($value !== '') $rta .= " value='" . saniti($value) . "'";
  $rta .= "></div>";
  return $rta;
}

function input_email($a) {
  $name = saniti($a->n);
  $label = saniti($a->l);
  $value = filter_var($a->d, FILTER_VALIDATE_EMAIL) ? $a->d : ''; // Validar email
  $title = saniti($a->tt);
  $a->w = $a->w ?? '';
  $a->ww = $a->ww ?? '';
  $a->v = $a->v ?? false;
  $a->u = $a->u ?? true;
  $a->t = $a->t ?? '';
  $a->vc = $a->vc ?? '';
  $a->so = $a->so ?? '';
  $rta = "<div class='campo " . saniti($a->w) . " " . saniti($a->ww) . " borde1 oscuro'>";
  $rta .= "<div>{$label}</div>";
  $rta .= "<input type='email' id='{$name}' name='{$name}'";
  $rta .= " class='" . saniti($a->w) . " " . ($a->v ? 'valido' : '') . " " . ($a->u ? 'captura' : 'bloqueo') . " " . ($a->t == 't' ? '' : 'txt-right') . "'";
  $rta .= " title='{$title}'";
  $rta .= " onblur=\"";
  if ($a->v) $rta .= "if(valido(this));";
  if ($a->x) $rta .= "solo_reg(this," . saniti($a->x) . ");";
  $rta .= "\"";
  if ($a->vc !== '') $rta .= " onfocus=\"" . saniti($a->vc) . "\"";
  if ($a->so !== '') $rta .= " onchange=\"" . saniti($a->so) . "\"";
  if (!$a->u) $rta .= " readonly";
  if ($value !== '') $rta .= " value='" . saniti($value) . "'";  
  $rta .= "></div>";
  return $rta;
}

//~ <input class='captura valido agendar' type='date' id='fecha_atenc' name='fecha_atenc' value=".$hoy." min='".$hoy."' max='3000-01-01' required></div>";
?>