<?php
// File: api/routes/router.php

declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('API_DIR', __DIR__ . '/..');

require_once API_DIR . '/config.php';
require_once API_DIR . '/modules/controller.php';
require_once API_DIR . '/lib/security.php';
require_once API_DIR . '/lib/middleware.php';

$tabla = $_GET['tabla'] ?? '';
$accion = $_GET['accion'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$input  = Security::sanitizeArray($input);

$tablasConfig = require API_DIR . '/config/tablas.php';

if (!isset($tablasConfig[$tabla])) {
    http_response_code(400);
    echo json_encode(['error' => 'Tabla no registrada']);
    exit;
}

$config = $tablasConfig[$tabla];

// Determinar roles requeridos para la acción según la DB
$rolesRequeridos = obtenerRolesPermitidos($tabla, $accion);
requireAuth($rolesRequeridos);

switch ($accion) {
    case 'crear':
        $input = array_intersect_key($input, array_flip($config['editable']));
        echo json_encode(crearRegistro($tabla, $input));
        break;

    case 'actualizar':
        $input = array_intersect_key($input, array_flip($config['editable']));
        echo json_encode(actualizarRegistro($tabla, $id, $input));
        break;

    case 'inactivar':
        echo json_encode(cambiarEstado($tabla, $id, 'INACTIVO'));
        break;

    case 'activar':
        echo json_encode(cambiarEstado($tabla, $id, 'ACTIVO'));
        break;

    case 'listar':
        $pagina     = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $limite     = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;
        $ordenCampo = $_GET['ordenCampo'] ?? $config['order'][0] ?? $config['fields'][0];
        $ordenDir   = strtoupper($_GET['ordenDir'] ?? 'ASC');
        $filtros    = isset($_GET['filtros']) ? json_decode($_GET['filtros'], true) : [];

        if (!in_array($ordenCampo, $config['order'])) {
            $ordenCampo = $config['order'][0] ?? $config['fields'][0];
        }

        $filtros = array_filter($filtros, fn($f) => is_array($f) && in_array($f[0], $config['filters'] ?? []));

        $resultado = listarRegistros($tabla, $pagina, $limite, $filtros, $ordenCampo, $ordenDir);

        // Eliminar campos ocultos si están definidos
        if (!empty($config['hidden']) && is_array($resultado['data'])) {
            foreach ($resultado['data'] as &$fila) {
                foreach ($config['hidden'] as $oculto) {
                    unset($fila[$oculto]);
                }
            }
        }

        echo json_encode($resultado);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no reconocida']);
        break;
}

function obtenerRolesPermitidos(string $tabla, string $accion): array {
    $pdo = Database::getConnection();
    $columna = match ($accion) {
        'listar' => 'consultar',
        'crear' => 'crear',
        'actualizar' => 'editar',
        'inactivar', 'activar' => 'ajustar',
        default => null
    };
    if (!$columna) return [];

    $sql = "SELECT perfil FROM adm_roles WHERE componente = :tabla AND $columna = 'SI' AND estado = 'A'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':tabla', $tabla);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}