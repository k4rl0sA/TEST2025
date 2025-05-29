<?php
session_start();
ini_set('display_errors','1');
setlocale(LC_TIME, 'es_CO');
ini_set('memory_limit','1024M');
date_default_timezone_set('America/Bogota');
setlocale(LC_ALL,'es_CO');
if (!isset($_SESSION["us_sds"])) {
    http_response_code(302);
    header("Location: /index.php"); 
    exit();
}

function getConnection() {
  $env = ($_SERVER['SERVER_NAME']==='www.siginf-sds.com') ? 'prod' : 'pru' ;
  $comy=array('prod' => ['s'=>'localhost','u' => 'u470700275_06','p' => 'z9#KqH!YK2VEyJpT','bd' => 'u470700275_06'],'pru'=>['s'=>'localhost','u' => 'u470700275_17','p' => 'z9#KqH!YK2VEyJpT','bd' => 'u470700275_17']);
  $dsn = 'mysql:host='.$comy[$env]['s'].';dbname='.$comy[$env]['bd'].';charset=utf8';
  $username = $comy[$env]['u'];
  $password = $comy[$env]['p'];
  $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];
  try {
      return new PDO($dsn, $username, $password, $options);
  } catch (PDOException $e) {
      die("Error de conexión: " . $e->getMessage());
  }
}

$con= getConnection();

function exportarDatos($sql,$name) {
    $con = getConnection();
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $rta = $stmt->fetchAll();
    $totalRegistros = count($rta);
    if ($totalRegistros > 0) {
        $rta[] = ["Total de registros" => $totalRegistros];
    } else {
        $rta[] = ["Total de registros" => 0];
    }
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename={$name}.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    $separator = "\t";
    if (count($rta) > 0) {
      $keys = array_keys($rta[0]);
      echo implode($separator, $keys) . "\n";
  }
  foreach ($rta as $row) {
    echo implode($separator, array_values($row)) . "\n";
}
}

// Consulta SQL
/* $sql = "SELECT id_usuario, nombre, clave, correo FROM usuarios";
$datos = exportarDatos($sql,$name);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename={$name}.xls");
header("Pragma: no-cache");
header("Expires: 0");

$separator = "\t";

// Imprimir nombres de columnas
echo "ID" . $separator . "Nombre" . $separator . "Apellido" . $separator . "Email" . "\n";

// Imprimir filas de datos
foreach ($datos as $row) {
    echo implode($separator, array_values($row)) . "\n";
}
 */

