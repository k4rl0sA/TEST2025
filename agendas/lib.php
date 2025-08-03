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
    $sql = "SELECT concat_ws('',nombre1,nombre2,apellido1,apellido2) AS fullName, IFNULL(P.telefono1,F.telefono1,P.telefono2,F.telefono2,F.telefono3)  AS phone,P.direccion AS address 
    FROM person P
    LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam 
    WHERE tipo_doc = '$docType' AND num_doc = '$docNumber' LIMIT 1";
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
    $sql = "INSERT INTO citas (professionalId, date, time, status, activity, notes, docType, docNumber, fullName, phone, address)
            VALUES (?, ?, ?, 'Agendado', ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        ['type' => 'i', 'value' => $input['professionalId']],
        ['type' => 's', 'value' => $input['date']],
        ['type' => 's', 'value' => $input['time']],
        ['type' => 's', 'value' => $input['activity']],
        ['type' => 's', 'value' => $input['notes']],
        ['type' => 's', 'value' => $input['patient']['docType']],
        ['type' => 's', 'value' => $input['patient']['docNumber']],
        ['type' => 's', 'value' => $input['patient']['fullName']],
        ['type' => 's', 'value' => $input['patient']['phone']],
        ['type' => 's', 'value' => $input['patient']['address']],
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
    $sql = "SELECT id, professionalId, fecha, hora, estado, actividad, notas, tipo_doc, documento, nombre, telefono, direccion
            FROM agenda
            WHERE professionalId=? AND date BETWEEN ? AND ?";
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
            'id' => $row['id'],
            'professionalId' => $row['professionalId'],
            'fecha' => $row['fecha'],
            'hora' => $row['hora'],
            'estado' => $row['estado'],
            'actividad' => $row['actividad'],
            'notas' => $row['notas'],
            'patient' => [
                'tipo_doc' => $row['tipo_doc'],
                'documento' => $row['documento'],
                'nombre' => $row['nombre'],
                'telefono' => $row['telefono'],
                'direccion' => $row['direccion'],
            ]
        ];
    }
    echo json_encode($appointments);
} else {
    // Devuelve error en formato JSON para el frontend
    echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al consultar citas']);
}
exit;
}
if ($req == 'updateAppointmentStatus') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id']);
    $status = $input['status'];
    $sql = "UPDATE citas SET status=? WHERE id=?";
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