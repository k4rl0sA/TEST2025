<?php
ini_set('display_errors','1');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../lib/php/app.php';

// --- Validar sesión ---
if (!isset($_SESSION["us_sds"])) {
    echo json_encode(['success' => false, 'error' => 'Sesión expirada', 'redirect' => '/index.php']);
    exit;
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
        $sql = "SELECT * FROM adm_roles $where_sql ORDER BY $sort $dir LIMIT ?, ?";
        $params_limit = $params;
        $params_limit[] = ['type' => 'i', 'value' => $offset];
        $params_limit[] = ['type' => 'i', 'value' => $pageSize];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params_limit);
        $roles = isset($arr['responseResult']) ? $arr['responseResult'] : [];

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
        $sql = "SELECT * FROM adm_roles WHERE id_rol = ?";
        $params = [['type' => 'i', 'value' => $id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (empty($arr['responseResult'])) error_response("Rol no encontrado", 404);
        $datos = $arr['responseResult'][0];
         // Mapear texto a id para los campos rta si/no
      /*   $rta_map = ['SI' => '1', 'NO' => '2'];
        foreach (['consultar','editar','crear','ajustar','importar'] as $campo) {
        if (isset($datos[$campo])) {
            $datos[$campo] = $rta_map[$datos[$campo]] ?? $datos[$campo];
        }
    } */

    // Obtener catálogo rta (SI/NO)
    $sql_cat = "SELECT idcatadeta AS value, descripcion AS label FROM catadeta WHERE idcatalogo=170 AND estado='A'";
    $cat_arr = datos_mysql($sql_cat, MYSQLI_ASSOC, false, []);
    $rta_map = [];
    if (!empty($cat_arr['responseResult'])) {
        foreach ($cat_arr['responseResult'] as $item) {
            $rta_map[$item['label']] = $item['value'];
            $rta_map[$item['value']] = $item['value'];
        }
    }

    // Normalizar campos rta
    foreach (['consultar','editar','crear','ajustar','importar'] as $campo) {
        if (isset($datos[$campo])) {
            $val = $datos[$campo];
            $datos[$campo] = $rta_map[$val] ?? $val;
        }
    }

    // Normalizar estado si aplica (ejemplo para estado)
    $sql_est = "SELECT idcatadeta AS value, descripcion AS label FROM catadeta WHERE idcatalogo=11 AND estado='A'";
    $est_arr = datos_mysql($sql_est, MYSQLI_ASSOC, false, []);
    $est_map = [];
    if (!empty($est_arr['responseResult'])) {
        foreach ($est_arr['responseResult'] as $item) {
            $est_map[$item['label']] = $item['value'];
            $est_map[$item['value']] = $item['value'];
        }
    }
    if (isset($datos['estado'])) {
        $val = $datos['estado'];
        $datos['estado'] = $est_map[$val] ?? $val;
    }

    echo json_encode([
        'success'=>true,
        'datos' => $datos
    ]);
        break;

    case 'create':
        check_csrf();
        $fields = ['modulo','perfil','componente','consultar','editar','crear','ajustar','importar','estado'];
        foreach ($fields as $f) {
            if (!isset($_POST[$f]) || $_POST[$f] === '') error_response("El campo '$f' es obligatorio");
        }

        $modulo    = clean($_POST['modulo']);
        $perfil    = clean($_POST['perfil']);
        $componente= clean($_POST['componente']);
        $estado= clean($_POST['estado']);
        // Campos tipo SI/NO
        foreach (['consultar','editar','crear','ajustar','importar'] as $campo) {
            $$campo = clean((isset($_POST[$campo]) && $_POST[$campo] == 1) ? 'SI' : 'NO');
        }

        // Validar unicidad
        $sql_check = "SELECT 1 FROM adm_roles WHERE modulo=? AND perfil=? AND componente=?";
        $params_check = [
            ['type' => 's', 'value' => $modulo],
            ['type' => 's', 'value' => $perfil],
            ['type' => 's', 'value' => $componente]
        ];
        $arr_check = datos_mysql($sql_check, MYSQLI_ASSOC, false, $params_check);
        if (!empty($arr_check['responseResult'])) error_response("Ya existe un rol con esos datos");

        // Insertar
        $sql = "INSERT INTO adm_roles (modulo,perfil,componente,consultar,editar,crear,ajustar,importar,estado)
                VALUES (?,?,?,?,?,?,?,?,?)";
        $params_insert = [
            ['type' => 's', 'value' => $modulo],
            ['type' => 's', 'value' => $perfil],
            ['type' => 's', 'value' => $componente],
            ['type' => 's', 'value' => $consultar],
            ['type' => 's', 'value' => $editar],
            ['type' => 's', 'value' => $crear],
            ['type' => 's', 'value' => $ajustar],
            ['type' => 's', 'value' => $importar],
            ['type' => 's', 'value' => $estado]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params_insert);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al crear el rol");
        }
        success_response('Rol creado correctamente');
        break;

    case 'update':
        check_csrf();
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
        $sql_check = "SELECT 1 FROM adm_roles WHERE modulo=? AND perfil=? AND componente=? AND id_rol != ?";
        $params_check = [
            ['type' => 's', 'value' => $modulo],
            ['type' => 's', 'value' => $perfil],
            ['type' => 's', 'value' => $componente],
            ['type' => 'i', 'value' => $id]
        ];
        $arr_check = datos_mysql($sql_check, MYSQLI_ASSOC, false, $params_check);
        if (!empty($arr_check['responseResult'])) error_response("Ya existe un rol con esos datos");

        // Actualizar
        $sql = "UPDATE adm_roles SET 
            modulo=?, perfil=?, componente=?, consultar=?, editar=?, crear=?, ajustar=?, importar=?, estado=?
            WHERE id_rol = ?";
        $params_update = [
            ['type' => 's', 'value' => $modulo],
            ['type' => 's', 'value' => $perfil],
            ['type' => 's', 'value' => $componente],
            ['type' => 's', 'value' => $consultar],
            ['type' => 's', 'value' => $editar],
            ['type' => 's', 'value' => $crear],
            ['type' => 's', 'value' => $ajustar],
            ['type' => 's', 'value' => $importar],
            ['type' => 's', 'value' => $estado],
            ['type' => 'i', 'value' => $id]
        ];
        $res = datos_mysql($sql, MYSQLI_ASSOC, false, $params_update);
        if (!isset($res['responseResult'][0]['affected_rows']) || $res['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al actualizar el rol");
        }
        success_response('Rol actualizado correctamente');
        break;

    case 'inactive':
        check_csrf();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $sql = "UPDATE adm_roles SET estado='I' WHERE id_rol = ?";
        $params = [['type' => 's', 'value' => 'I'],['type' => 'i', 'value' => $id]];
        $res = mysql_prepd($sql, $params);
        break;
    
    case 'opciones':
        $catalogos = ['estado'    => 11,'rta' => 170];
        $opciones = [];
    foreach ($catalogos as $campo => $idcat) {
        $sql = "SELECT idcatadeta AS value, descripcion AS label FROM catadeta WHERE idcatalogo=? AND estado='A' ORDER BY 1";
        $params = [['type' => 'i', 'value' => $idcat]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $opciones[$campo] = isset($arr['responseResult']) ? $arr['responseResult'] : [];
    }
    $perfiles= datos_mysql("SELECT perfil as value, perfil as label FROM adm_roles GROUP BY perfil ORDER BY 1");
    $opciones['perfil'] = isset($perfiles['responseResult']) ? $perfiles['responseResult'] : [];
    echo json_encode([
        'success'=> true,
        'opciones' => $opciones
    ]);
        break;

    default:
        error_response("Acción no válida", 400);

        /* $sql = "SELECT * FROM adm_roles";
        $result = datos_mysql($sql); */
        /* // Obtener un solo rol por ID
$id = 5;
$sql = "SELECT * FROM adm_roles WHERE id_rol = ?";
$params = [['type' => 'i', 'value' => $id]];
$rol = datos_mysql_row($sql, $params);
if ($rol) {
    // $rol es un array asociativo con los campos del registro
} */
 /*   case 'delete':
        check_csrf();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $sql = "DELETE FROM adm_roles WHERE id_rol = ?";
        $params = [['type' => 'i', 'value' => $id]];
        $res = mysql_prepd($sql, $params);
        if (strpos($res, 'Error') !== false) error_response("Error al eliminar: $res");
        success_response('Rol eliminado correctamente');
        break; */

        // --- CSRF seguro ---
/* function check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            log_error('CSRF token inválido o ausente');
            error_response('CSRF token inválido o ausente', 403);
        }
    }
}
 */
}