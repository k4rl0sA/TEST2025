<?php
ini_set('display_errors','1');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../lib/php/app.php';

// --- Validar sesión ---
if (!isset($_SESSION["us_sds"])) {
    echo json_encode(['success' => false, 'error' => 'Sesión expirada', 'redirect' => '/index.php']);
    exit;
}
// --- Validar permisos ---
if (!acceso('roles')) { // Cambia 'ajustes' por el módulo
    error_response('No tienes permisos para acceder a este módulo', 403);
}

// --- Utilidad para respuesta de error segura y log ---
function error_response($msg, $code = 400) {
    http_response_code($code);
    log_error($msg);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// --- Utilidad para respuesta de éxito ---
function success_response($msg = 'Operación exitosa', $extra = []) {
    echo json_encode(array_merge(['success' => true, 'message' => $msg], $extra));
    exit;
}

// --- Limpiar entradas ---
function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

$a = $_GET['a'] ?? $_POST['a'] ?? '';

switch ($a) {
    case 'list':
        // --- Filtros ---
        $where = [];
        $params = [];
        if (!empty($_GET['modulo'])) {
            $where[] = "modulo LIKE ?";
            $params[] = ['type' => 's', 'value' => '%' . $_GET['modulo'] . '%'];
        }
        if (!empty($_GET['perfil'])) {
            $where[] = "perfil LIKE ?";
            $params[] = ['type' => 's', 'value' => '%' . $_GET['perfil'] . '%'];
        }
        if (!empty($_GET['estado'])) {
            $where[] = "estado = ?";
            $params[] = ['type' => 's', 'value' => $_GET['estado']];
        }
        if (!empty($_GET['search'])) {
            $where[] = "(modulo LIKE ? OR perfil LIKE ? OR componente LIKE ?)";
            $params[] = ['type' => 's', 'value' => '%' . $_GET['search'] . '%'];
            $params[] = ['type' => 's', 'value' => '%' . $_GET['search'] . '%'];
            $params[] = ['type' => 's', 'value' => '%' . $_GET['search'] . '%'];
        }
        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // --- Orden y paginación ---
        $allowedSort = ['id_rol','modulo','perfil','componente','estado'];
        $sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'id_rol';
        $dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $page = max(1, intval($_GET['page'] ?? 1));
        $pageSize = max(1, min(100, intval($_GET['pageSize'] ?? 10)));
        $offset = ($page - 1) * $pageSize;

        // --- Total ---
        $sql_count = "SELECT COUNT(*) as total FROM adm_roles $where_sql";
        $arr_count = datos_mysql($sql_count, MYSQLI_ASSOC, false, $params);
        $total = isset($arr_count['responseResult'][0]['total']) ? intval($arr_count['responseResult'][0]['total']) : 0;
        $totalPages = ceil($total / $pageSize);

        // --- Datos ---
        $sql = "SELECT *  FROM adm_roles $where_sql ORDER BY $sort $dir LIMIT ?, ?";//modulo,perfil,componente,consultar,editar,crear,ajustar,importar,estado
        $params_limit = $params;
        $params_limit[] = ['type' => 'i', 'value' => $offset];
        $params_limit[] = ['type' => 'i', 'value' => $pageSize];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params_limit);
        $roles = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        foreach ($roles as &$role) {
            $token = myhash($role['id_rol']);
            $_SESSION['hash'][$token] = $role['id_rol'];
            $role['token'] = $token;
            unset($role['id_rol']); // Opcional: oculta el id real
        }
        limpiar_hashes(500);
        echo json_encode([
            'success' => true,
            'roles' => $roles,
            'total' => $total,
            'totalRows' => $total,
            'totalPages' => $totalPages
        ]);
        break;

    case 'get':
    
        break;

    case 'create':
    
        break;

    case 'update':
        break;

    case 'inactive':
        break;
    
    case 'opciones':
        break;
    case 'bulk':
        break;

    default:
        error_response("Acción no válida", 400);
}