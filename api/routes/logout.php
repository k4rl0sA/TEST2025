<?php
// File: /api/routes/logout.php
declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

defined('API_DIR') || exit(header('HTTP/1.1 403 Forbidden'));

require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../lib/auth.php';

header('Content-Type: application/json');

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Obtener el token del encabezado Authorization
    $headers = Security::getSafeHeaders();
    if (!isset($headers['Authorization']) || strpos($headers['Authorization'], 'Bearer ') !== 0) {
        throw new Exception('Token de autenticación faltante', 401);
    }

    $token = trim(str_replace('Bearer ', '', $headers['Authorization']));

    // Decodificar el token usando la clave secreta
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
    } catch (ExpiredException $e) {
        throw new Exception('Token expirado', 401);
    } catch (SignatureInvalidException $e) {
        throw new Exception('Firma del token inválida', 401);
    } catch (\Throwable $e) {
        throw new Exception('Token inválido', 401);
    }

    // Se puede guardar aquí el token expirado o en base de datos o memoria temporal por X tiempo

    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada correctamente'
    ]);
    exit;

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}