<?php
ini_set('display_errors','1');
// print_r($_POST['a']);
require_once "../lib/php/app.php";
if (!isset($_SESSION["us_sds"])) {
    if (isAjax()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    } else {
        header("Location: /index.php");
    }
    exit;
}
  if (isset($_POST['a']) && isset($_POST['tb']) && $_POST['a'] && $_POST['tb']) {
    $rta = "";
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) json_encode($rta);
    else echo $rta;
  }
$req = $_GET['a'] ?? $_POST['a'] ?? '';
if ($req == 'getProfiles') {
    $sql = "SELECT descripcion AS id, descripcion AS name FROM catadeta WHERE idcatalogo=218 AND estado='A'";
    getSelectOptions($sql);
    exit;
}
if ($req == 'getProfessionals') {
    $profileId = $_GET['profileId'] ?? 0;
    $sql = "SELECT id_usuario AS id, nombre AS name FROM usuarios WHERE perfil IN ('$profileId') AND estado='A'";
    getSelectOptions($sql);
    exit;
}
if ($req == 'getDocTypes') {
    $sql = "SELECT idcatadeta AS value, descripcion AS label FROM catadeta WHERE idcatalogo=1 AND estado='A'";
    getSelectOptions($sql,'value','label');
    exit;
}
if ($req == 'searchPatient') {
    $docType = $_GET['docType'] ?? '';
    $docNumber = $_GET['docNumber'] ?? '';
    $sql = "SELECT concat_ws(' ',nombre1,nombre2,apellido1,apellido2) AS fullName, 
        COALESCE(NULLIF(P.telefono1, ''), NULLIF(F.telefono1, ''), NULLIF(P.telefono2, ''), NULLIF(F.telefono2, ''), NULLIF(F.telefono3, '')) AS phone, 
        G.direccion AS address,G,idgeo AS idgeo, P.idpeople AS idpeople
    FROM person P
    LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam 
    LEFT JOIN hog_geo G ON F.idpre = G.idgeo
    WHERE tipo_doc = '$docType' AND idpersona = '$docNumber' LIMIT 1";
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
    $sql = "INSERT INTO agendas (cupo, profesionalid, idpeople, idgeo, fecha, actividad, notas, usu_creo, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,1)";
    $params = [
        ['type' => 'i', 'value' => $input['cupo']],
        ['type' => 'i', 'value' => $input['profesionalid']],
        ['type' => 'i', 'value' => $input['idpeople']],
        ['type' => 'i', 'value' => $input['idgeo']],
        ['type' => 's', 'value' => $input['fecha']],
        ['type' => 'i', 'value' => $input['actividad']],
        ['type' => 's', 'value' => $input['notas']],
        ['type' => 's', 'value' => $_SESSION["us_sds"]],
    ];
    $result = mysql_prepd($sql, $params);
    if ($result['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al guardar']);
    }
    exit;
}
if ($req == 'getAppointments') {
   $professionalId = intval($_GET['professionalId'] ?? 0);
    $weekStart = $_GET['weekStart'] ?? '';
    $weekEnd = $_GET['weekEnd'] ?? '';
    $sql = "SELECT idagenda, cupo, profesionalid, idpeople, idgeo, fecha, actividad, notas, estado
            FROM agendas
            WHERE profesionalid=? AND fecha BETWEEN ? AND ?";
    $params = [
        ['type' => 'i', 'value' => $professionalId],
        ['type' => 's', 'value' => $weekStart],
        ['type' => 's', 'value' => $weekEnd],
    ];
    $result = mysql_prepd($sql, $params);
    $appointments = [];
    if (is_array($result) && isset($result['responseResult']) && is_array($result['responseResult'])) {
        foreach ($result['responseResult'] as $row) {
            $appointments[] = [
                'id' => $row['idagenda'],
                'cupo' => $row['cupo'],
                'professionalId' => $row['profesionalid'],
                'idpeople' => $row['idpeople'],
                'idgeo' => $row['idgeo'],
                'date' => $row['fecha'],
                'activity' => $row['actividad'],
                'notes' => $row['notas'],
                'status' => $row['estado'],
            ];
        }
        echo json_encode($appointments);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al consultar citas']);
    }
    exit;
}
if ($req == 'updateAppointmentStatus') {
   $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id']);
    $status = $input['status'];
    $sql = "UPDATE agendas SET estado=? WHERE idagenda=?";
    $params = [
        ['type' => 's', 'value' => $status],
        ['type' => 'i', 'value' => $id]
    ];
    $result = mysql_prepd($sql, $params);
    if ($result['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al actualizar']);
    }
    exit;
}