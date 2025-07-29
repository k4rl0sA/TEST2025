<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../libs/gestion.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Validar sesión
if (!isset($_SESSION['us_sds'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Sesión no iniciada. <a href=\"/\">Iniciar sesión</a>'
    ]);
if (!file_exists(__DIR__ . '/../libs/vendor/autoload.php')) {
    echo json_encode(['success' => false, 'error' => 'No se encuentra el archivo de Google API Client (vendor/autoload.php)']);
    exit;
}
require_once __DIR__ . '/../libs/vendor/autoload.php'; // Google API Client
}
header('Content-Type: application/json');



// Validar archivo y usuario
if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK || !isset($_POST['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Archivo o usuario no recibido']);
    exit;
}

$id_usuario = intval($_POST['id_usuario']);
$pdfTmp = $_FILES['pdf']['tmp_name'];
$pdfName = $id_usuario . '.pdf';

// Autenticación con Google
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../libs/credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

// Puedes guardar y reutilizar el token de acceso en sesión o archivo
if (isset($_SESSION['google_access_token'])) {
    $client->setAccessToken($_SESSION['google_access_token']);
} else {
    // Aquí deberías implementar el flujo OAuth2 para obtener el token
    echo json_encode(['success' => false, 'error' => 'No hay token de Google Drive']);
    exit;
}

$service = new Google_Service_Drive($client);

// Carpeta destino en Google Drive (ID de la carpeta)
$folderId = '1Dh4_o5mrTY-DGFec1-bfzKqDhCpVCLA9'; // Reemplaza por el ID real de la carpeta
if (!$folderId) {
    echo json_encode(['success' => false, 'error' => 'Carpeta de destino no configurada']);
    exit;
}
// Verificar si el archivo ya existe en la carpeta
$files = $service->files->listFiles([
    'q' => "name='$pdfName' and '$folderId' in parents",
    'fields' => 'files(id, name)'
]);
if (count($files->files) > 0) {
    // Si el archivo ya existe, puedes optar por actualizarlo o eliminarlo
    $existingFile = $files->files[0];
    $fileId = $existingFile->id;

    // Eliminar el archivo existente
    try {
        $service->files->delete($fileId);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar el archivo existente: ' . $e->getMessage()]);
        exit;
    }
}
$fileMetadata = [
    'name' => $pdfName,
    'parents' => [$folderId]
];

$content = file_get_contents($pdfTmp);

try {
    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => 'application/pdf',
        'uploadType' => 'multipart'
    ]);

    // Actualiza el campo file en la tabla usuarios (puedes guardar el ID o la URL del archivo)
    $fileId = $file->id;
    $sql = "UPDATE usuarios SET file = '$fileId' WHERE id_usuario = $id_usuario";
    $res = dato_mysql($sql);

    if (strpos($res, 'Se ha Actualizado') !== false) {
        echo json_encode(['success' => true, 'fileId' => $fileId]);
    } else {
        echo json_encode(['success' => false, 'error' => $res]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>