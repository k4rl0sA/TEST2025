<?php
// File: api/routes/modulos.php

require_once __DIR__ . '/../modules/controller.php';
require_once __DIR__ . '/../lib/security.php';

$whitelist = ['usuarios', 'hogares', 'familias', 'descargas', 'caracterizaciones'];
$tabla = $_GET['tabla'] ?? '';
$accion = $_GET['accion'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$input = Security::sanitizeArray($input);

if (!in_array($tabla, $whitelist)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tabla no permitida']);
    exit;
}

switch ($accion) {
    case 'crear':
        echo json_encode(crearRegistro($tabla, $input));
        break;

    case 'actualizar':
        echo json_encode(actualizarRegistro($tabla, $id, $input));
        break;

    case 'inactivar':
        echo json_encode(cambiarEstado($tabla, $id, 'INACTIVO'));
        break;

    case 'activar':
        echo json_encode(cambiarEstado($tabla, $id, 'ACTIVO'));
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no reconocida']);
}
