<?php
// File: api/routes/logout.php
declare(strict_types=1);

define('API_DIR', __DIR__ . '/..');

require_once API_DIR . '/config.php';
require_once API_DIR . '/lib/security.php';
require_once API_DIR . '/lib/auth.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    $headers = Security::getSafeHeaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!str_starts_with($auth, 'Bearer ')) {
        throw new Exception('Token de autenticación faltante', 401);
    }

    $token = trim(substr($auth, 7));

    $payload = Auth::isAuthorized();
    if (!$payload) {
        throw new Exception('Token inválido o expirado', 401);
    }

    // Aquí podrías guardar jti en blacklist temporal (no implementado por defecto)
    // ej: Blacklist::add($payload['jti']);

    echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente']);
    exit;

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}