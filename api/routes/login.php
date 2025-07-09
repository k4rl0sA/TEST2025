<?php
// File: api/routes/login.php

declare(strict_types=1);

define('API_DIR', __DIR__ . '/..');

require_once API_DIR . '/config.php';
require_once API_DIR . '/lib/security.php';
require_once API_DIR . '/lib/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$input = Security::sanitizeArray($input);

$id = $input['documento'] ?? '';
$pass = $input['clave'] ?? '';

if (empty($id) || empty($pass)) {
    http_response_code(400);
    echo json_encode(['error' => 'Id y password son requeridos']);
    exit;
}

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT id_usuario, correo,nombre,clave,subred, perfil FROM usuarios WHERE id_usuario = :id AND estado = 'A' LIMIT 1");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user || !password_verify($pass, $user['clave'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit;
    }

    $access_token  = Auth::generarTokenJWT((string)$user['id_usuario'], ['roles' => [$user['perfil']]]);
    $refresh_token = Auth::generarRefreshToken((string)$user['id_usuario']);

    echo json_encode([
        'access_token'  => $access_token,
        'refresh_token' => $refresh_token,
        'token_type'    => 'Bearer',
        'expires_in'    => JWT_EXPIRATION,
        'user' => [
            'id'    => $user['id_usuario'],
            'correo' => $user['correo'],
            'nombre' => $user['nombre'],
            'subred' => $user['subred'],
            'perfil'   => $user['perfil']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en el servidor']);
}
