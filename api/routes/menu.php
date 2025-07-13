<?php
require_once '../lib/auth.php';
require_once '../config.php';

header('Content-Type: application/json');

$user = Auth::verificarToken(); // Valida el JWT y retorna datos del usuario
/* if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
} */

$perfil = $user['perfil'];

// Consulta los menús permitidos para el perfil
$pdo = Database::getConnection();
$stmt = $pdo->prepare("
    SELECT m.id, m.link, m.icono, m.tipo, m.enlace, m.menu, m.contenedor
    FROM adm_menu m
    JOIN adm_menuusuarios mu ON m.id = mu.idmenu
    WHERE mu.perfil = :perfil AND m.estado = 'A' AND mu.estado = 'A'
    ORDER BY m.id
");
$stmt->bindParam(':perfil', $perfil);
$stmt->execute();
$menus = $stmt->fetchAll();

echo json_encode(['menu' => $menus]);