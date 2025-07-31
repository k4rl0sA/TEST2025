<?php
ini_set('display_errors','1');
// print_r($_POST['a']);
require_once "../lib/php/app.php";
if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
else {
  if (isset($_POST['a']) && isset($_POST['tb']) && $_POST['a'] && $_POST['tb']) {
    $rta = "";
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) json_encode($rta);
    else echo $rta;
  }
}   

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
    $sql = "SELECT idcatadeta AS id, descripcion AS name FROM catadeta WHERE idcatalogo=218 AND estado='A'";
    getSelectOptions($sql);
}
if ($req == 'getProfessionals') {
    $profileId = $_GET['profileId'] ?? 0;
    $sql = "SELECT id_usuario AS id, nombre AS name FROM usuarios WHERE perfil IN ('$profileId') AND estado='A'";
    // var_dump($sql);
    getSelectOptions($sql);
}
if ($req == 'getDocTypes') {
    $sql = "SELECT idcatadeta AS id, descripcion AS name FROM catadeta WHERE idcatalogo=1 AND estado='A'";
    getSelectOptions($sql);
}
if ($req == 'searchPatient') {
    $docType = $_GET['docType'] ?? '';
    $docNumber = $_GET['docNumber'] ?? '';
    $sql = "SELECT nombre AS fullName, telefono AS phone, direccion AS address FROM pacientes WHERE tipo_doc = '$docType' AND num_doc = '$docNumber' LIMIT 1";
    $result = datos_mysql($sql);
    if (!empty($result['responseResult'])) {
        echo json_encode(['success' => true, 'patient' => $result['responseResult'][0]]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}
if ($req == 'saveAppointment') {
    $input = json_decode(file_get_contents('php://input'), true);
    // Validar y sanitizar $input aquí
    // Guardar en la BD (INSERT INTO citas ...)
    // Si todo va bien:
    echo json_encode(['success' => true]);
    // Si hay error:
    // echo json_encode(['success' => false, 'error' => 'Mensaje de error']);
    exit;
}
if ($req == 'getAppointments') {
    $professionalId = intval($_GET['professionalId'] ?? 0);
    $weekStart = $_GET['weekStart'] ?? '';
    $weekEnd = $_GET['weekEnd'] ?? '';
    $sql = "SELECT * FROM citas WHERE professionalId=$professionalId AND date BETWEEN '$weekStart' AND '$weekEnd'";
    $result = datos_mysql($sql);
    echo json_encode($result['responseResult']);
    exit;
}