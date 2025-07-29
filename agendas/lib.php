<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
// print_r($_POST['a']);
if ($_POST['a']!='opc' && $_POST['tb']!='person') $perf=perfil($_POST['tb']);
if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
else {
  $rta="";
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) json_encode($rta);
	else echo $rta;
  }   

require_once "../libs/gestion.php";

// Validar sesión
if (!isset($_SESSION['us_sds'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Sesión no iniciada. <a href=\"/\">Iniciar sesión</a>'
    ]);
    exit;
}
$req = $_GET['a'] ?? $_POST['a'] ?? '';

if ($req == 'getProfiles') {
    $sql = "SELECT id, name FROM perfiles WHERE estado='A'";
    $result = datos_mysql($sql);
    $data = [];
    foreach ($result['responseResult'] as $row) {
        $data[] = ['value' => $row['id'], 'label' => $row['name']];
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($req == 'getProfessionals') {
    $profileId = intval($_GET['profileId'] ?? 0);
    $sql = "SELECT id, name FROM profesionales WHERE profileId=$profileId AND estado='A'";
    $result = datos_mysql($sql);
    $data = [];
    foreach ($result['responseResult'] as $row) {
        $data[] = ['value' => $row['id'], 'label' => $row['name']];
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}