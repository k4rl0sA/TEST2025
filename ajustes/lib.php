<?php
ini_set('display_errors','1');
require_once __DIR__ . '/php/app.php';

if (!isset($_SESSION["us_sds"])) {
    if (isAjax()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    } else {
        header("Location: /index.php");
    }
    exit;
}
// Utilidad para limpiar entradas
function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

// --- ENDPOINT: Listar roles con filtros, paginación y orden ---
if (isset($_GET['a']) && $_GET['a'] === 'list') {
    $page = max(1, intval($_GET['page'] ?? 1));
    $pageSize = max(1, intval($_GET['pageSize'] ?? 10));
    $offset = ($page - 1) * $pageSize;

    $where = [];
    if (!empty($_GET['search'])) {
        $search = clean($_GET['search']);
        $where[] = "(modulo LIKE '%$search%' OR perfil LIKE '%$search%' OR componente LIKE '%$search%')";
    }
    if (!empty($_GET['modulo'])) {
        $modulo = clean($_GET['modulo']);
        $where[] = "modulo LIKE '%$modulo%'";
    }
    if (!empty($_GET['perfil'])) {
        $perfil = clean($_GET['perfil']);
        $where[] = "perfil LIKE '%$perfil%'";
    }
    if (!empty($_GET['estado'])) {
        $estado = clean($_GET['estado']);
        $where[] = "estado = '$estado'";
    }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sort = in_array($_GET['sort'] ?? '', [
        'id_rol', 'modulo', 'perfil', 'componente', 'consultar', 'editar', 'crear', 'ajustar', 'importar', 'estado'
    ]) ? $_GET['sort'] : 'id_rol';
    $dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

    // Total de registros
    $sqlCount = "SELECT COUNT(*) as total FROM adm_roles $whereSql";
    $resCount = datos_mysql($sqlCount);
    $total = $resCount['responseResult'][0]['total'] ?? 0;
    $totalPages = ceil($total / $pageSize);

    // Datos paginados
    $sql = "SELECT * FROM adm_roles $whereSql ORDER BY $sort $dir LIMIT $offset, $pageSize";
    $res = datos_mysql($sql);

    var_dump($sql);

    header('Content-Type: application/json');
    echo json_encode([
        'roles' => $res['responseResult'],
        'total' => $total,
        'totalPages' => $totalPages,
        'page' => $page
    ]);
    exit;
}

// --- ENDPOINT: Obtener un rol por id ---
if (isset($_GET['a']) && $_GET['a'] === 'get' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM adm_roles WHERE id_rol = $id LIMIT 1";
    $res = datos_mysql($sql);
    header('Content-Type: application/json');
    echo json_encode($res['responseResult'][0] ?? []);
    exit;
}

// --- ENDPOINT: Crear un nuevo rol ---
if (isset($_GET['a']) && $_GET['a'] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulo = clean($_POST['modulo'] ?? '');
    $perfil = clean($_POST['perfil'] ?? '');
    $componente = clean($_POST['componente'] ?? '');
    $consultar = clean($_POST['consultar'] ?? '');
    $editar = clean($_POST['editar'] ?? '');
    $crear = clean($_POST['crear'] ?? '');
    $ajustar = clean($_POST['ajustar'] ?? '');
    $importar = clean($_POST['importar'] ?? '');
    $estado = clean($_POST['estado'] ?? '');

    // Validación básica
    if (!$modulo || !$perfil || !$componente || !$consultar || !$editar || !$crear || !$ajustar || !$importar || !$estado) {
        echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
        exit;
    }

    // Insertar
    $sql = "INSERT INTO adm_roles (modulo, perfil, componente, consultar, editar, crear, ajustar, importar, estado)
            VALUES ('$modulo', '$perfil', '$componente', '$consultar', '$editar', '$crear', '$ajustar', '$importar', '$estado')";
    $con = $GLOBALS['con'];
    if ($con->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al crear: ' . $con->error]);
    }
    exit;
}

// --- ENDPOINT: Actualizar un rol ---
if (isset($_GET['a']) && $_GET['a'] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_rol = intval($_POST['id_rol'] ?? 0);
    $modulo = clean($_POST['modulo'] ?? '');
    $perfil = clean($_POST['perfil'] ?? '');
    $componente = clean($_POST['componente'] ?? '');
    $consultar = clean($_POST['consultar'] ?? '');
    $editar = clean($_POST['editar'] ?? '');
    $crear = clean($_POST['crear'] ?? '');
    $ajustar = clean($_POST['ajustar'] ?? '');
    $importar = clean($_POST['importar'] ?? '');
    $estado = clean($_POST['estado'] ?? '');

    if (!$id_rol || !$modulo || !$perfil || !$componente || !$consultar || !$editar || !$crear || !$ajustar || !$importar || !$estado) {
        echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
        exit;
    }

    $sql = "UPDATE adm_roles SET 
                modulo='$modulo', perfil='$perfil', componente='$componente',
                consultar='$consultar', editar='$editar', crear='$crear',
                ajustar='$ajustar', importar='$importar', estado='$estado'
            WHERE id_rol = $id_rol";
    $con = $GLOBALS['con'];
    if ($con->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $con->error]);
    }
    exit;
}

// --- ENDPOINT: Eliminar un rol ---
if (isset($_GET['a']) && $_GET['a'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM adm_roles WHERE id_rol = $id";
    $con = $GLOBALS['con'];
    if ($con->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $con->error]);
    }
    exit;
}

// --- ENDPOINT: Opciones para el select de estado ---
if (isset($_GET['a']) && $_GET['a'] === 'estado_options') {
    header('Content-Type: application/json');
    echo json_encode([
        ['value' => 'A', 'label' => 'Activo'],
        ['value' => 'I', 'label' => 'Inactivo']
    ]);
    exit;
}

// Si no coincide ningún endpoint
http_response_code(404);
echo json_encode(['success' => false, 'error' =>