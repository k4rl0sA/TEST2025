<?php
// File: api/routes/permisos.php
require_once __DIR__ . '/../modules/controller.php';
require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Validar token y obtener perfil
$user = Auth::verificarToken();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado -']);
    exit;
}
$perfil = $user['perfil'] ?? '';

// Definir módulos permitidos
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

// Consultar permisos del perfil para el módulo
$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT consultar, editar, crear, ajustar, importar FROM adm_roles WHERE modulo = :modulo AND perfil = :perfil AND estado = 'A' LIMIT 1");
$stmt->bindParam(':modulo', $tabla);
$stmt->bindParam(':perfil', $perfil);
$stmt->execute();
$permisos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$permisos) {
    http_response_code(403);
    echo json_encode(['error' => 'Sin permisos']);
    exit;
}

// Validar permiso según acción
$mapAccionPermiso = [
    'crear'      => 'crear',
    'actualizar' => 'editar',
    'inactivar'  => 'ajustar',
    'activar'    => 'ajustar',
    'consultar'  => 'consultar'
];
$permisoNecesario = $mapAccionPermiso[$accion] ?? null;

if (!$permisoNecesario || ($permisos[$permisoNecesario] ?? 'NO') !== 'SI') {
    http_response_code(403);
    echo json_encode(['error' => 'Permiso denegado']);
    exit;
}
// Ejecutar acción si tiene permiso
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
    case 'consultar':
        // Ejemplo: retorna los permisos para el frontend
        echo json_encode(['permisos' => $permisos]);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no reconocida']);
}