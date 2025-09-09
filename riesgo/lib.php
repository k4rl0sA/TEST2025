
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
        $jwt_secret = isset($_ENV['JWT_SECRET']) ? $_ENV['JWT_SECRET'] :$_ENV['JWT_SECRET_default'];
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
    case 'list':
        // --- Solo usuarios autenticados ---
        if (!acceso('roles')) error_response('No tienes permisos para acceder a este módulo', 403);
        // --- Filtros ---
        $where = [];
        $params = [];
        if (!empty($_GET['tipo_doc'])) {
            $where[] = "P.tipo_doc = ?";
            $params[] = ['type' => 's', 'value' => $_GET['tipo_doc']];
        }
        if (!empty($_GET['idpersona'])) {
            $where[] = "P.idpersona = ?";
            $params[] = ['type' => 's', 'value' => $_GET['idpersona']];
        }
        if (!empty($_GET['nombre'])) {
            $where[] = "(P.nombre1 LIKE ? OR P.nombre2 LIKE ?)";
            $params[] = ['type' => 's', 'value' => '%' . $_GET['nombre'] . '%'];
            $params[] = ['type' => 's', 'value' => '%' . $_GET['nombre'] . '%'];
        }
        // Puedes agregar más filtros según tus necesidades
        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // --- Orden y paginación ---
        $allowedSort = ['P.tipo_doc','P.idpersona','P.nombre1','P.nombre2','P.sexo','P.fecha_nacimiento'];
        $sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'P.idpersona';
        $dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $page = max(1, intval($_GET['page'] ?? 1));
        $pageSize = max(1, min(100, intval($_GET['pageSize'] ?? 10)));
        $offset = ($page - 1) * $pageSize;

        // --- Total ---
        $sql_count = "SELECT COUNT(*) as total FROM person P
        INNER JOIN hog_fam F ON P.vivipersona = F.id_fam
        LEFT JOIN hog_geo G ON F.idpre = G.idgeo
        INNER JOIN (
            SELECT hc.* FROM hog_carac hc
            INNER JOIN (
                SELECT idfam, MAX(fecha) AS fecha_max FROM hog_carac GROUP BY idfam
            ) ult ON hc.idfam = ult.idfam AND hc.fecha = ult.fecha_max
        ) C ON P.vivipersona = C.idfam
        INNER JOIN (
            SELECT f.id_fam, ha.descripcion, ha.fecha_toma FROM hog_fam f
            INNER JOIN person per ON per.vivipersona = f.id_fam
            INNER JOIN hog_tam_apgar ha ON ha.idpeople = per.idpeople
            INNER JOIN (
                SELECT p.vivipersona, MAX(hta.fecha_toma) AS fecha_max FROM person p
                INNER JOIN hog_tam_apgar hta ON hta.idpeople = p.idpeople
                GROUP BY p.vivipersona
            ) ult2 ON per.vivipersona = ult2.vivipersona AND ha.fecha_toma = ult2.fecha_max
            GROUP BY f.id_fam
        ) A ON P.vivipersona = A.id_fam
        $where_sql";
        $arr_count = datos_mysql($sql_count, MYSQLI_ASSOC, false, $params);
        $total = isset($arr_count['responseResult'][0]['total']) ? intval($arr_count['responseResult'][0]['total']) : 0;
        $totalPages = ceil($total / $pageSize);

        // --- Datos ---
        $sql = "SELECT 
    P.tipo_doc, P.idpersona, P.nombre1, P.nombre2, P.sexo, P.fecha_nacimiento,
    G.localidad, G.direccion, G.cordx, G.cordy,
    P.vivipersona,
    CASE WHEN C.energia = 'SI' THEN 1 WHEN C.energia = 'NO' THEN 2 END AS energia,
    CASE WHEN C.gas = 'SI' THEN 1 WHEN C.gas = 'NO' THEN 2 END AS gas,
    CASE WHEN C.acueducto = 'SI' THEN 1 WHEN C.acueducto = 'NO' THEN 2 END AS acueducto,
    CASE WHEN C.alcantarillado = 'SI' THEN 1 WHEN C.alcantarillado = 'NO' THEN 2 END AS alcantarillado,
    CASE WHEN C.basuras = 'SI' THEN 1 WHEN C.basuras = 'NO' THEN 2 END AS basuras,
    CASE WHEN C.facamb8 = 'SI' THEN 1 WHEN C.facamb8 = 'NO' THEN 2 END AS facamb8,
    CASE WHEN C.facamb5 = 'SI' THEN 1 WHEN C.facamb5 = 'NO' THEN 2 END AS facamb5,
    CASE WHEN C.facamb6 = 'SI' THEN 1 WHEN C.facamb6 = 'NO' THEN 2 END AS facamb6,
    CASE WHEN C.facamb9 = 'SI' THEN 1 WHEN C.facamb9 = 'NO' THEN 2 END AS facamb9,
    C.ingreso,
    CASE 
        WHEN A.descripcion = 'FUNCIÓN FAMILIAR NORMAL' THEN 1
        WHEN A.descripcion = 'DISFUNCIÓN FAMILIAR LEVE' THEN 2
        WHEN A.descripcion = 'DISFUNCIÓN FAMILIAR MODERADA' THEN 3
        WHEN A.descripcion = 'DISFUNCIÓN FAMILIAR SEVERA' THEN 4
        ELSE NULL
    END AS apgar_familiar,
    P.pobladifer,
    P.discapacidad,
    P.etnia,
    CASE WHEN C.seg_pre1 = 'SI' THEN 1 WHEN C.seg_pre1 = 'NO' THEN 2 END AS seg_pre1,
    CASE WHEN C.seg_pre2 = 'SI' THEN 1 WHEN C.seg_pre2 = 'NO' THEN 2 END AS seg_pre2,
    CASE WHEN C.seg_pre3 = 'SI' THEN 1 WHEN C.seg_pre3 = 'NO' THEN 2 END AS seg_pre3,
    CASE WHEN C.seg_pre5 = 'SI' THEN 1 WHEN C.seg_pre5 = 'NO' THEN 2 END AS seg_pre5,
    CASE WHEN C.seg_pre6 = 'SI' THEN 1 WHEN C.seg_pre6 = 'NO' THEN 2 END AS seg_pre6,
    CASE WHEN C.seg_pre7 = 'SI' THEN 1 WHEN C.seg_pre7 = 'NO' THEN 2 END AS seg_pre7,
    CASE WHEN C.seg_pre8 = 'SI' THEN 1 WHEN C.seg_pre8 = 'NO' THEN 2 END AS seg_pre8
FROM person P
INNER JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
INNER JOIN (
    SELECT hc.* FROM hog_carac hc
    INNER JOIN (
        SELECT idfam, MAX(fecha) AS fecha_max FROM hog_carac GROUP BY idfam
    ) ult ON hc.idfam = ult.idfam AND hc.fecha = ult.fecha_max
) C ON P.vivipersona = C.idfam
INNER JOIN (
    SELECT f.id_fam, ha.descripcion, ha.fecha_toma FROM hog_fam f
    INNER JOIN person per ON per.vivipersona = f.id_fam
    INNER JOIN hog_tam_apgar ha ON ha.idpeople = per.idpeople
    INNER JOIN (
        SELECT p.vivipersona, MAX(hta.fecha_toma) AS fecha_max FROM person p
        INNER JOIN hog_tam_apgar hta ON hta.idpeople = p.idpeople
        GROUP BY p.vivipersona
    ) ult2 ON per.vivipersona = ult2.vivipersona AND ha.fecha_toma = ult2.fecha_max
    GROUP BY f.id_fam
) A ON P.vivipersona = A.id_fam
$where_sql
ORDER BY $sort $dir
LIMIT ?, ?";
        $params_limit = $params;
        $params_limit[] = ['type' => 'i', 'value' => $offset];
        $params_limit[] = ['type' => 'i', 'value' => $pageSize];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params_limit);
        $personas = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode([
            'success' => true,
            'data' => $personas,
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