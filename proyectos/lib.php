<?php
ini_set('display_errors','1');
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../lib/php/app.php';

// --- Validar sesión (ajusta según tu lógica de login) ---
if (!isset($_SESSION["us_sds"])) {
    echo json_encode(['success' => false, 'error' => 'Sesión expirada', 'redirect' => '/index.php']);
    exit;
}

// --- Utilidades ---
function error_response($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}
function success_response($msg = 'Operación exitosa', $extra = []) {
    echo json_encode(array_merge(['success' => true, 'message' => $msg], $extra));
    exit;
}
function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

$a = $_GET['a'] ?? $_POST['a'] ?? '';

function progreso_por_estado($estado) {
    $map = [
        'analisis'        => 10,
        'desarrollo'      => 40,
        'pruebas'         => 70,
        'aprobacion'      => 80,
        'manual'          => 85,
        'pruebasSub'      => 90,
        'socializacion'   => 95,
        'implementacion'  => 98,
        'notifica'        => 100
    ];
    return $map[$estado] ?? 0;
}

switch ($a) {
    // --- Listar proyectos (dashboard) ---
    case 'list_proyectos':
        $where = [];
        $params = [];
        if (!empty($_GET['estado'])) {
            $where[] = "estado = ?";
            $params[] = ['type' => 's', 'value' => $_GET['estado']];
        }
        if (!empty($_GET['search'])) {
            $where[] = "(nombre LIKE ? OR descripcion LIKE ? OR cliente LIKE ?)";
            $params[] = ['type' => 's', 'value' => '%' . $_GET['search'] . '%'];
            $params[] = ['type' => 's', 'value' => '%' . $_GET['search'] . '%'];
            $params[] = ['type' => 's', 'value' => '%' . $_GET['search'] . '%'];
        }
        $where[] = "activo = 1";
        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT p.*, u.nombre AS responsable 
                FROM proyectos p 
                LEFT JOIN usuarios u ON p.responsable_id = u.id_usuario 
                $where_sql 
                ORDER BY p.fecha_creacion DESC";    
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $proyectos = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode(['success' => true, 'proyectos' => $proyectos]);
        break;

    // --- Crear proyecto ---
    case 'crear_proyecto':
        $nombre = clean($_POST['nombre'] ?? '');
        $descripcion = clean($_POST['descripcion'] ?? '');
        $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $fecha_fin_estimada = $_POST['fecha_fin_estimada'] ?? null;
        $estado = $_POST['estado'] ?? 'planificacion';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $progreso = progreso_por_estado($estado);
        $presupuesto = floatval($_POST['presupuesto'] ?? 0);
        $responsable_id = intval($_POST['responsable_id'] ?? 0);
        $cliente = clean($_POST['cliente'] ?? '');
        if (!$nombre) error_response("El nombre del proyecto es obligatorio");
        $sql = "INSERT INTO proyectos 
            (nombre, descripcion, fecha_inicio, fecha_fin_estimada, estado, prioridad, progreso, presupuesto, responsable_id, cliente) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            ['type'=>'s','value'=>$nombre],
            ['type'=>'s','value'=>$descripcion],
            ['type'=>'s','value'=>$fecha_inicio],
            ['type'=>'s','value'=>$fecha_fin_estimada],
            ['type'=>'s','value'=>$estado],
            ['type'=>'s','value'=>$prioridad],
            ['type'=>'i','value'=>$progreso],
            ['type'=>'d','value'=>$presupuesto],
            ['type'=>'i','value'=>$responsable_id],
            ['type'=>'s','value'=>$cliente]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['insert_id'])) error_response("Error al crear el proyecto");
        success_response('Proyecto creado correctamente', ['id' => $arr['responseResult'][0]['insert_id']]);
        break;

    // --- Obtener un proyecto ---
    case 'get_proyecto':
        $id = intval($_GET['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $sql = "SELECT p.*, p.responsable_id AS responsable 
                FROM proyectos p 
                LEFT JOIN usuarios u ON p.responsable_id = u.id_usuario
                WHERE p.id = ?";
        $params = [['type'=>'i','value'=>$id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (empty($arr['responseResult'])) error_response("Proyecto no encontrado", 404);
        echo json_encode(['success'=>true, 'proyecto'=>$arr['responseResult'][0]]);
        break;

    // --- Actualizar proyecto ---
    case 'actualizar_proyecto':
        $id = intval($_POST['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $nombre = clean($_POST['nombre'] ?? '');
        $descripcion = clean($_POST['descripcion'] ?? '');
        $fecha_fin_estimada = $_POST['fecha_fin_estimada'] ?? null;
        $fecha_fin_real = $_POST['fecha_fin_real'] ?? null;
        $estado = $_POST['estado'] ?? 'planificacion';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $progreso = progreso_por_estado($estado);
        $presupuesto = floatval($_POST['presupuesto'] ?? 0);
        $responsable_id = intval($_POST['responsable_id'] ?? 0);
        $cliente = clean($_POST['cliente'] ?? '');
        if (!$nombre) error_response("El nombre del proyecto es obligatorio");
        $sql = "UPDATE proyectos SET 
            nombre=?, descripcion=?, fecha_fin_estimada=?, fecha_fin_real=?, estado=?, prioridad=?, progreso=?, presupuesto=?, responsable_id=?, cliente=?
            WHERE id=?";
        $params = [
            ['type'=>'s','value'=>$nombre],
            ['type'=>'s','value'=>$descripcion],
            ['type'=>'s','value'=>$fecha_fin_estimada],
            ['type'=>'s','value'=>$fecha_fin_real],
            ['type'=>'s','value'=>$estado],
            ['type'=>'s','value'=>$prioridad],
            ['type'=>'i','value'=>$progreso],
            ['type'=>'d','value'=>$presupuesto],
            ['type'=>'i','value'=>$responsable_id],
            ['type'=>'s','value'=>$cliente],
            ['type'=>'i','value'=>$id]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al actualizar el proyecto");
        }
        success_response('Proyecto actualizado correctamente');
        break;

    // --- Eliminar proyecto (borrado lógico) ---
    case 'eliminar_proyecto':
        $id = intval($_POST['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $sql = "UPDATE proyectos SET activo=0 WHERE id=?";
        $params = [['type'=>'i','value'=>$id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al eliminar el proyecto");
        }
        success_response('Proyecto eliminado correctamente');
        break;

    // --- Listar responsables (usuarios) ---
    case 'list_responsables':
        $sql = "SELECT id_usuario,nombre FROM usuarios WHERE estado='A' and perfil='ADM' ORDER BY nombre";
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, []);
        $usuarios = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode(['success' => true, 'usuarios' => $usuarios]);
        break;

    // --- Listar tareas de un proyecto ---
    case 'list_tareas':
        $proyecto_id = intval($_GET['proyecto_id'] ?? 0);
        if (!$proyecto_id) error_response("ID de proyecto inválido");
        $sql = "SELECT t.*, u.nombre AS responsable 
                FROM tareas t 
                LEFT JOIN usuarios u ON t.responsable_id = u.id 
                WHERE t.proyecto_id = ? AND t.estado='A'
                ORDER BY t.fase, t.fecha_inicio_estimada";
        $params = [['type'=>'i','value'=>$proyecto_id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $tareas = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode(['success' => true, 'tareas' => $tareas]);
        break;

    // --- Crear tarea ---
    case 'crear_tarea':
        $proyecto_id = intval($_POST['proyecto_id'] ?? 0);
        $nombre = clean($_POST['nombre'] ?? '');
        $descripcion = clean($_POST['descripcion'] ?? '');
        $fase = $_POST['fase'] ?? '';
        $estado_tarea = $_POST['estado_tarea'] ?? 'pendiente';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $fecha_inicio_estimada = $_POST['fecha_inicio_estimada'] ?? null;
        $fecha_fin_estimada = $_POST['fecha_fin_estimada'] ?? null;
        $responsable_id = intval($_POST['responsable_id'] ?? 0);
        $usu_creo = $_SESSION['us_sds'] ?? 'sistema';
        if (!$proyecto_id || !$nombre || !$fase) error_response("Datos obligatorios faltantes");
        $sql = "INSERT INTO tareas 
            (proyecto_id, nombre, descripcion, fase, estado_tarea, prioridad, fecha_inicio_estimada, fecha_fin_estimada, responsable_id, usu_creo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            ['type'=>'i','value'=>$proyecto_id],
            ['type'=>'s','value'=>$nombre],
            ['type'=>'s','value'=>$descripcion],
            ['type'=>'s','value'=>$fase],
            ['type'=>'s','value'=>$estado_tarea],
            ['type'=>'s','value'=>$prioridad],
            ['type'=>'s','value'=>$fecha_inicio_estimada],
            ['type'=>'s','value'=>$fecha_fin_estimada],
            ['type'=>'i','value'=>$responsable_id],
            ['type'=>'s','value'=>$usu_creo]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['insert_id'])) error_response("Error al crear la tarea");
        success_response('Tarea creada correctamente', ['id' => $arr['responseResult'][0]['insert_id']]);
        break;

    // --- Actualizar tarea ---
    case 'actualizar_tarea':
        $id = intval($_POST['id'] ?? 0);
        $nombre = clean($_POST['nombre'] ?? '');
        $descripcion = clean($_POST['descripcion'] ?? '');
        $fase = $_POST['fase'] ?? '';
        $estado_tarea = $_POST['estado_tarea'] ?? 'pendiente';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $fecha_inicio_estimada = $_POST['fecha_inicio_estimada'] ?? null;
        $fecha_fin_estimada = $_POST['fecha_fin_estimada'] ?? null;
        $responsable_id = intval($_POST['responsable_id'] ?? 0);
        $usu_update = $_SESSION['us_sds'] ?? 'sistema';
        if (!$id || !$nombre || !$fase) error_response("Datos obligatorios faltantes");
        $sql = "UPDATE tareas SET 
            nombre=?, descripcion=?, fase=?, estado_tarea=?, prioridad=?, fecha_inicio_estimada=?, fecha_fin_estimada=?, responsable_id=?, usu_update=?, fecha_update=NOW()
            WHERE id=?";
        $params = [
            ['type'=>'s','value'=>$nombre],
            ['type'=>'s','value'=>$descripcion],
            ['type'=>'s','value'=>$fase],
            ['type'=>'s','value'=>$estado_tarea],
            ['type'=>'s','value'=>$prioridad],
            ['type'=>'s','value'=>$fecha_inicio_estimada],
            ['type'=>'s','value'=>$fecha_fin_estimada],
            ['type'=>'i','value'=>$responsable_id],
            ['type'=>'s','value'=>$usu_update],
            ['type'=>'i','value'=>$id]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al actualizar la tarea");
        }
        success_response('Tarea actualizada correctamente');
        break;

    // --- Eliminar tarea (borrado lógico) ---
    case 'eliminar_tarea':
        $id = intval($_POST['id'] ?? 0);
        if (!$id) error_response("ID inválido");
        $usu_update = $_SESSION['us_sds'] ?? 'sistema';
        $sql = "UPDATE tareas SET estado='I', usu_update=?, fecha_update=NOW() WHERE id=?";
        $params = [
            ['type'=>'s','value'=>$usu_update],
            ['type'=>'i','value'=>$id]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al eliminar la tarea");
        }
        success_response('Tarea eliminada correctamente');
        break;

    // --- Listar comentarios de una tarea ---
    case 'list_comentarios':
        $tarea_id = intval($_GET['tarea_id'] ?? 0);
        if (!$tarea_id) error_response("ID de tarea inválido");
        $sql = "SELECT c.*, u.nombre AS usuario 
                FROM comentarios_tareas c 
                LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
                WHERE c.tarea_id = ? AND c.estado='A'
                ORDER BY c.fecha_create ASC";
        $params = [['type'=>'i','value'=>$tarea_id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $comentarios = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode(['success' => true, 'comentarios' => $comentarios]);
        break;

    // --- Agregar comentario a tarea ---
    case 'agregar_comentario':
        $tarea_id = intval($_POST['tarea_id'] ?? 0);
        $usuario_id = intval($_POST['usuario_id'] ?? 0);
        $comentario = clean($_POST['comentario'] ?? '');
        $usu_creo = $_SESSION['us_sds'] ?? 'sistema';
        if (!$tarea_id || !$usuario_id || !$comentario) error_response("Datos obligatorios faltantes");
        $sql = "INSERT INTO comentarios_tareas (tarea_id, usuario_id, comentario, usu_creo) VALUES (?, ?, ?, ?)";
        $params = [
            ['type'=>'i','value'=>$tarea_id],
            ['type'=>'i','value'=>$usuario_id],
            ['type'=>'s','value'=>$comentario],
            ['type'=>'s','value'=>$usu_creo]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['insert_id'])) error_response("Error al agregar el comentario");
        success_response('Comentario agregado correctamente', ['id' => $arr['responseResult'][0]['insert_id']]);
        break;

    // --- Listar archivos adjuntos de una tarea ---
    case 'list_adjuntos':
        $tarea_id = intval($_GET['tarea_id'] ?? 0);
        if (!$tarea_id) error_response("ID de tarea inválido");
        $sql = "SELECT * FROM archivos_adjuntos WHERE tarea_id = ? AND estado='A' ORDER BY fecha_subida DESC";
        $params = [['type'=>'i','value'=>$tarea_id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $adjuntos = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode(['success' => true, 'adjuntos' => $adjuntos]);
        break;

    // --- Subir archivo adjunto (solo lógica, no manejo de archivos reales aquí) ---
    case 'agregar_adjunto':
        $tarea_id = intval($_POST['tarea_id'] ?? 0);
        $usuario_id = intval($_POST['usuario_id'] ?? 0);
        $nombre_archivo = clean($_POST['nombre_archivo'] ?? '');
        $ruta_archivo = clean($_POST['ruta_archivo'] ?? '');
        $tipo_archivo = clean($_POST['tipo_archivo'] ?? '');
        $tamanio = intval($_POST['tamanio'] ?? 0);
        $usu_creo = $_SESSION['us_sds'] ?? 'sistema';
        if (!$tarea_id || !$usuario_id || !$nombre_archivo || !$ruta_archivo) error_response("Datos obligatorios faltantes");
        $sql = "INSERT INTO archivos_adjuntos (tarea_id, usuario_id, nombre_archivo, ruta_archivo, tipo_archivo, tamanio, usu_creo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            ['type'=>'i','value'=>$tarea_id],
            ['type'=>'i','value'=>$usuario_id],
            ['type'=>'s','value'=>$nombre_archivo],
            ['type'=>'s','value'=>$ruta_archivo],
            ['type'=>'s','value'=>$tipo_archivo],
            ['type'=>'i','value'=>$tamanio],
            ['type'=>'s','value'=>$usu_creo]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['insert_id'])) error_response("Error al agregar el adjunto");
        success_response('Archivo adjunto agregado correctamente', ['id' => $arr['responseResult'][0]['insert_id']]);
        break;

    // --- Listar historial de cambios de una tarea ---
  case 'list_historial':
        $tarea_id = intval($_GET['tarea_id'] ?? 0);
        if (!$tarea_id) error_response("ID de tarea inválido");
        $sql = "SELECT h.*, u.nombre AS usuario 
                FROM historial_cambios h 
                LEFT JOIN usuarios u ON h.usuario_id = u.id_usuario
                WHERE h.tarea_id = ? AND h.estado='A'
                ORDER BY h.fecha_cambio DESC";
        $params = [['type'=>'i','value'=>$tarea_id]];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $historial = isset($arr['responseResult']) ? $arr['responseResult'] : [];
        echo json_encode(['success' => true, 'historial' => $historial]);
        break;

    default:
        error_response("Acción no válida", 400);
}