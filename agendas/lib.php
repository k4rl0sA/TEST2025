<?php
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
    $sql = "SELECT idcatadeta AS id, descripcion AS name FROM catadeta WHERE idcatalogo=218 AND estado='A'";
    getSelectOptions($sql);
}
if ($req == 'getProfessionals') {
    $profileId = intval($_GET['profileId'] ?? 0);
    $sql = "SELECT id_usuario AS id, nombre AS name FROM usuarios WHERE perfil=$profileId AND estado='A'";
    getSelectOptions($sql);
}
if ($req == 'getDocTypes') {
    $sql = "SELECT idcatadeta AS id, descripcion AS name FROM catadeta WHERE idcatalogo=1 AND estado='A'";
    getSelectOptions($sql);
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