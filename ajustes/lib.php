<?php
ini_set('display_errors','1');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["us_sds"])) {
    echo json_encode(['success' => false, 'error' => 'Sesión expirada', 'redirect' => '/index.php']);
    exit;
}
require_once __DIR__ . '/../lib/php/app.php';

// --- Utilidad para respuesta de error segura ---
function error_response($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

$a = isset($_GET['a']) ? $_GET['a'] : (isset($_POST['a']) ? $_POST['a'] : '');

switch ($a) {
    case 'list':
        // --- Filtros ---
        $where = [];
        if (!empty($_GET['modulo'])) $where[] = "modulo LIKE '%" . mysqli_real_escape_string($con, $_GET['modulo']) . "%'";
        if (!empty($_GET['perfil'])) $where[] = "perfil LIKE '%" . mysqli_real_escape_string($con, $_GET['perfil']) . "%'";
        if (!empty($_GET['estado'])) $where[] = "estado = '" . mysqli_real_escape_string($con, $_GET['estado']) . "'";
        if (!empty($_GET['search'])) {
            $s = mysqli_real_escape_string($con, $_GET['search']);
            $where[] = "(modulo LIKE '%$s%' OR perfil LIKE '%$s%' OR componente LIKE '%$s%')";
        }
        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // --- Orden y paginación ---
        $sort = in_array($_GET['sort'] ?? '', ['id_rol','modulo','perfil','componente','estado']) ? $_GET['sort'] : 'id_rol';
        $dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $page = max(1, intval($_GET['page'] ?? 1));
        $pageSize = max(1, min(100, intval($_GET['pageSize'] ?? 10)));
        $offset = ($page - 1) * $pageSize;

        // --- Total ---
        $sql_count = "SELECT COUNT(*) as total FROM adm_roles $where_sql";
        $res_count = mysqli_query($con, $sql_count);
        $total = $res_count ? intval(mysqli_fetch_assoc($res_count)['total']) : 0;
        $totalPages = ceil($total / $pageSize);

        // --- Datos ---
        $sql = "SELECT * FROM adm_roles $where_sql ORDER BY $sort $dir LIMIT $offset, $pageSize";
        $res = mysqli_query($con, $sql);
        if (!$res) error_response("Error al consultar: " . mysqli_error($con));
        $roles = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $roles[] = $row;
        }
        echo json_encode([
            'success' => true,
            'roles' => $roles,
            'total' => $total,
            'totalPages' => $totalPages
        ]);
        break;

    case 'get':
        $id = intval($_GET['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $sql = "SELECT * FROM adm_roles WHERE id_rol = $id LIMIT 1";
        $res = mysqli_query($con, $sql);
        if (!$res || !mysqli_num_rows($res)) error_response("Rol no encontrado", 404);
        $role = mysqli_fetch_assoc($res);
        echo json_encode($role);
        break;

    case 'create':
        // Validar campos requeridos
        $fields = ['modulo','perfil','componente','consultar','editar','crear','ajustar','importar','estado'];
        foreach ($fields as $f) {
            if (empty($_POST[$f])) error_response("El campo '$f' es obligatorio");
        }
        $modulo = clean($_POST['modulo']);
        $perfil = clean($_POST['perfil']);
        $componente = clean($_POST['componente']);
        $consultar = clean($_POST['consultar']);
        $editar = clean($_POST['editar']);
        $crear = clean($_POST['crear']);
        $ajustar = clean($_POST['ajustar']);
        $importar = clean($_POST['importar']);
        $estado = clean($_POST['estado']);

        // Validar unicidad
        $sql_check = "SELECT 1 FROM adm_roles WHERE modulo='$modulo' AND perfil='$perfil' AND componente='$componente'";
        $res_check = mysqli_query($con, $sql_check);
        if ($res_check && mysqli_num_rows($res_check)) error_response("Ya existe un rol con esos datos");

        $sql = "INSERT INTO adm_roles (modulo,perfil,componente,consultar,editar,crear,ajustar,importar,estado)
                VALUES ('$modulo','$perfil','$componente','$consultar','$editar','$crear','$ajustar','$importar','$estado')";
        if (!mysqli_query($con, $sql)) error_response("Error al crear: " . mysqli_error($con));
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id = intval($_POST['id_rol'] ?? 0);
        if (!$id) error_response("ID inválido");
        $fields = ['modulo','perfil','componente','consultar','editar','crear','ajustar','importar','estado'];
        foreach ($fields as $f) {
            if (empty($_POST[$f])) error_response("El campo '$f' es obligatorio");
        }
        $modulo = clean($_POST['modulo']);
        $perfil = clean($_POST['perfil']);
        $componente = clean($_POST['componente']);
        $consultar = clean($_POST['consultar']);
        $editar = clean($_POST['editar']);
        $crear = clean($_POST['crear']);
        $ajustar = clean($_POST['ajustar']);
        $importar = clean($_POST['importar']);
        $estado = clean($_POST['estado']);

        // Validar unicidad (excepto el actual)
        $sql_check = "SELECT 1 FROM adm_roles WHERE modulo='$modulo' AND perfil='$perfil' AND componente='$componente' AND id_rol != $id";
        $res_check = mysqli_query($con, $sql_check);
        if ($res_check && mysqli_num_rows($res_check)) error_response("Ya existe un rol con esos datos");

        $sql = "UPDATE adm_roles SET 
            modulo='$modulo', perfil='$perfil', componente='$componente',
            consultar='$consultar', editar='$editar', crear='$crear',
            ajustar='$ajustar', importar='$importar', estado='$estado'
            WHERE id_rol = $id";
        if (!mysqli_query($con, $sql)) error_response("Error al actualizar: " . mysqli_error($con));
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $sql = "DELETE FROM adm_roles WHERE id_rol = $id";
        if (!mysqli_query($con, $sql)) error_response("Error al eliminar: " . mysqli_error($con));
        echo json_encode(['success' => true]);
        break;

    default:
        error_response("Acción no válida", 400);
}