<?php
// File: api/lib/middleware.php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

defined('API_DIR') || exit(header('HTTP/1.1 403 Forbidden'));

/**
 * Middleware para validar autenticación y opcionalmente roles/permisos
 *
 * @param string[] $roles Lista de roles requeridos (opcional)
 * @param string[] $permisos Lista de permisos requeridos (opcional)
 * @return array Payload JWT si pasa la validación
 */
function requireAuth(array $roles = [], array $permisos = []): array {
    $payload = Auth::isAuthorized();
    if (!$payload) {
        http_response_code(401);
        exit(json_encode(['error' => 'No autorizado. Token inválido o ausente']));
    }
    foreach ($roles as $rol) {
        if (!Auth::tieneRol($payload, $rol)) {
            http_response_code(403);
            exit(json_encode(['error' => "Acceso denegado. Se requiere perfil: $rol"]));
        }
    }
    foreach ($permisos as $permiso) {
        if (!Auth::tienePermiso($payload, $permiso)) {
            http_response_code(403);
            exit(json_encode(['error' => "Permiso insuficiente: $permiso"]));
        }
    }
    return $payload;
}

    // Añadir sistema de permisos
function requirePermission(string $requiredPermission): array {
    $payload = requireAuth();
    
    if (!Auth::tienePermiso($payload, $requiredPermission)) {
        http_response_code(403);
        exit(json_encode([
            'error' => 'Acceso denegado',
            'required_permission' => $requiredPermission,
            'user_permissions' => $payload['scope'] ?? []
        ]));
    }
    
    return $payload;
}