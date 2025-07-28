<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "../libs/gestion.php";
header('Content-Type: application/json');

if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK || !isset($_POST['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Archivo o usuario no recibido']);
    exit;
}

$id_usuario = intval($_POST['id_usuario']);
$pdfTmp = $_FILES['pdf']['tmp_name'];
$pdfName = $id_usuario . '.pdf';

// Carpeta destino local (puedes adaptar para OneDrive)
$carpetaDestino = __DIR__ . "/pdfs/";
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0777, true);
}
$rutaFinal = $carpetaDestino . $pdfName;

if (!move_uploaded_file($pdfTmp, $rutaFinal)) {
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']);
    exit;
}

// Actualiza el campo file en la tabla usuarios
$sql = "UPDATE usuarios SET file = 1 WHERE id_usuario = $id_usuario";
$res = mysqli_query($GLOBALS['con'], $sql);
var_dump($res);
if ($res) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($GLOBALS['con'])]);
}
?>