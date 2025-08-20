<?php
// filepath: vscode-vfs://github/k4rl0sA/TEST2025/adm-menu/lib.php

header('Content-Type: application/json');
session_start();

// --- Seguridad: CSRF y sesión ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'error' => 'CSRF token inválido o ausente']);
        exit;
    }
}
if (!isset($_SESSION["us_sds"])) {
    echo json_encode(['success' => false, 'error' => 'Sesión expirada', 'redirect' => '/index.php']);
    exit;
}

// --- Conexión a la base de datos ---
require_once '../lib/php/app.php'; // Debe contener la función datos_mysql

function error_response($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}
function success_response($msg, $extra = []) {
    echo json_encode(array_merge(['success' => true, 'msg' => $msg], $extra));
    exit;
}

// --- CRUD principal ---
$a = $_GET['a'] ?? $_POST['a'] ?? '';
switch ($a) {
    case 'list':
        // Filtros y paginación
        $where = [];
        $params = [];
        if (!empty($_GET['link'])) {
            $where[] = "link LIKE ?";
            $params[] = ['type'=>'s', 'value'=>'%'.$_GET['link'].'%'];
        }
        if (!empty($_GET['tipo'])) {
            $where[] = "tipo = ?";
            $params[] = ['type'=>'s', 'value'=>$_GET['tipo']];
        }
        if (!empty($_GET['estado'])) {
            $where[] = "estado = ?";
            $params[] = ['type'=>'s', 'value'=>$_GET['estado']];
        }
        $whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';
        $sort = in_array($_GET['sort'] ?? '', ['id','link','tipo','estado']) ? $_GET['sort'] : 'id';
        $dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $page = max(1, intval($_GET['page'] ?? 1));
        $pageSize = max(1, intval($_GET['pageSize'] ?? 10));
        $offset = ($page-1)*$pageSize;

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM adm_menu $whereSql ORDER BY $sort $dir LIMIT $offset, $pageSize";
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        $menus = $arr['responseResult'] ?? [];
        $totalRows = datos_mysql("SELECT FOUND_ROWS() as total", MYSQLI_ASSOC)['responseResult'][0]['total'] ?? 0;
        $totalPages = ceil($totalRows/$pageSize);

        echo json_encode([
            'success' => true,
            'menus' => $menus,
            'totalPages' => $totalPages,
            'totalRows' => $totalRows
        ]);
        exit;

    case 'get':
        $token = $_GET['token'] ?? '';
        $id = IdHash($token);
        if (!$id) error_response("ID inválido");
        $sql = "SELECT * FROM adm_menu WHERE id = ?";
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, [['type'=>'i','value'=>$id]]);

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
        if (empty($arr['responseResult'])) error_response("Menú no encontrado");
        $datos = $arr['responseResult'][0];
        $datos['token'] = IdHash($datos['id']);
        unset($datos['id']);
        echo json_encode(['success' => true, 'menu' => $datos]);    
        
        exit;

    case 'create':
        $fields = ['link','icono','tipo','enlace','menu','contenedor','estado'];
        foreach ($fields as $f) {
            if (empty($_POST[$f])) error_response("El campo '$f' es obligatorio");
        }
        $sql = "INSERT INTO adm_menu (link,icono,tipo,enlace,menu,contenedor,estado) VALUES (?,?,?,?,?,?,?)";
        $params = [
            ['type'=>'s','value'=>$_POST['link']],
            ['type'=>'s','value'=>$_POST['icono']],
            ['type'=>'s','value'=>$_POST['tipo']],
            ['type'=>'s','value'=>$_POST['enlace']],
            ['type'=>'s','value'=>$_POST['menu']],
            ['type'=>'s','value'=>$_POST['contenedor']],
            ['type'=>'s','value'=>$_POST['estado']]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al crear el menú");
        }
        success_response('Menú creado correctamente');
        exit;

    case 'update':
        $id = intval($_POST['id'] ?? 0);
        if (!$id) error_response('ID inválido');
        $fields = ['link','icono','tipo','enlace','menu','contenedor','estado'];
        foreach ($fields as $f) {
            if (empty($_POST[$f])) error_response("El campo '$f' es obligatorio");
        }
        $sql = "UPDATE adm_menu SET link=?,icono=?,tipo=?,enlace=?,menu=?,contenedor=?,estado=? WHERE id=?";
        $params = [
            ['type'=>'s','value'=>$_POST['link']],
            ['type'=>'s','value'=>$_POST['icono']],
            ['type'=>'s','value'=>$_POST['tipo']],
            ['type'=>'s','value'=>$_POST['enlace']],
            ['type'=>'s','value'=>$_POST['menu']],
            ['type'=>'s','value'=>$_POST['contenedor']],
            ['type'=>'s','value'=>$_POST['estado']],
            ['type'=>'i','value'=>$id]
        ];
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, $params);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al actualizar el menú");
        }
        success_response('Menú actualizado correctamente');
        exit;

    case 'delete':
        $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
        if (!$id) error_response('ID inválido');
        $sql = "DELETE FROM adm_menu WHERE id=?";
        $arr = datos_mysql($sql, MYSQLI_ASSOC, false, [['type'=>'i','value'=>$id]]);
        if (!isset($arr['responseResult'][0]['affected_rows']) || $arr['responseResult'][0]['affected_rows'] < 1) {
            error_response("Error al eliminar el menú");
        }
        success_response('Menú eliminado correctamente');
        exit;

    default:
        error_response('Acción no válida', 400);
}