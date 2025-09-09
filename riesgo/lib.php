
<?php
ini_set('display_errors','1');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../lib/php/app.php';

// --- Utilidad para respuesta de error segura y log ---
function error_response($msg, $code = 400) {
    http_response_code($code);
    if (function_exists('log_error')) log_error($msg);
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

// --- Seguridad: solo POST para mutaciones, GET para consulta ---
function require_method($method) {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        error_response('Método no permitido', 405);
    }
}

// --- CSRF solo para POST autenticados ---
function require_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            error_response('CSRF token inválido o ausente', 403);
        }
    }
}

$a = $_GET['a'] ?? $_POST['a'] ?? '';

switch ($a) {
    // Endpoint para generar JWT (solo ejemplo, deberías validar usuario/clave)
    case 'login_jwt':
        require_method('POST');
        $usuario = $_POST['usuario'] ?? '';
        $clave = $_POST['clave'] ?? '';
        if (!$usuario || !$clave) error_response('Usuario y clave requeridos', 400);
        // Consulta segura usando prepared statements
        $sql = "SELECT id_usuario, correo, nombre, clave, subred, perfil FROM usuarios WHERE id_usuario = ? AND estado = 'A' LIMIT 1";
        $params = [ ['type' => 's', 'value' => $usuario] ];
        require_once __DIR__ . '/../lib/php/app.php';
        $row = datos_mysql_row($sql, $params);
        if (!$row) error_response('Usuario no encontrado o inactivo', 401);
        if (!password_verify($clave, $row['clave'])) error_response('Clave incorrecta', 401);
        $jwt_secret = isset($_ENV['JWT_SECRET']) ? $_ENV['JWT_SECRET'] : 'CAMBIAESTESECRETO';
        $payload = [
            'usuario' => $row['id_usuario'],
            'correo' => $row['correo'],
            'nombre' => $row['nombre'],
            'perfil' => $row['perfil'],
            'iat' => time(),
            'exp' => time() + 3600 // 1 hora
        ];
        $token = jwt_encode($payload, $jwt_secret);
        echo json_encode(['success'=>true, 'token'=>$token, 'expira'=>$payload['exp']]);
        exit;
    // Endpoint público para monitoreo/healthcheck
    case 'health':
        require_method('GET');
        echo json_encode(['success'=>true, 'status'=>'ok', 'ts'=>date('c')]);
        exit;

    case 'list':
        // --- Solo usuarios autenticados ---
        if (!acceso('roles')) error_response('No tienes permisos para acceder a este módulo', 403);
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


    // Ejemplo de endpoint público (sin sesión):
    case 'publico':
        require_method('GET');
        // Aquí lógica pública, por ejemplo consulta de info general
        echo json_encode(['success'=>true, 'info'=>'Este endpoint es público']);
        exit;

    // Ejemplo de endpoint privado (requiere sesión y CSRF para POST):
    case 'create':
        require_method('POST');
        if (!isset($_SESSION["us_sds"])) error_response('Sesión expirada', 401);
        require_csrf();
        // ... lógica de creación ...
        success_response('Creado correctamente');
        break;

    // ...otros endpoints privados...

    default:
        error_response("Acción no válida", 400);
}