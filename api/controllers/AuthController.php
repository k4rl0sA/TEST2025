<?php
// File: api/controllers/AuthController.php
declare(strict_types=1);

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/security.php';
require_once __DIR__ . '/../config.php';

class AuthController {
    public static function login(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $input = Security::sanitizeArray($input);

        $id = isset($input['documento']) ? trim($input['documento']) : '';
        $pass = $input['clave'] ?? '';
        
       if (empty($id) || !ctype_digit($id) || strlen($id) > 20 || empty($pass)) {
            http_response_code(400);
            echo json_encode(['error' => 'Credenciales inválidas']);
            return;
        }
        try {
            $pdo = Database::getConnection();
            // Registrar intento de acceso
            $logStmt = $pdo->prepare("INSERT INTO access_log (user_id, ip, success, fecha_create) VALUES (?, ?, ?, ?)");
            $logStmt->execute([$id, $_SERVER['REMOTE_ADDR'], 0, date('Y-m-d H:i:s')]);
            $logId = $pdo->lastInsertId();
            // Verificar usuario
            $stmt = $pdo->prepare("SELECT id_usuario, correo, nombre, clave, subred, perfil FROM usuarios WHERE id_usuario = :id AND estado = 'A' LIMIT 1");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch();
            if ($user) {
                $verif = password_verify($pass, $user['clave']);
            }
            if (!$user || !password_verify($pass, $user['clave'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Credenciales inválidas']);
                return;
            }

            // Actualizar log a exitoso
            $updateLog = $pdo->prepare("UPDATE access_log SET success = 1 WHERE user_id = ?");
            $updateLog->execute([$logId]);
            $tokenPayload = [
                'sub'      => $user['id_usuario'],
                'nombre'   => $user['nombre'],
                'perfil'   => $user['perfil'],
                'exp'      => time() + JWT_EXPIRATION
            ];

            
            $access_token = Auth::generarTokenJWT((string)$user['id_usuario'], $tokenPayload);
            $refresh_token = Auth::generarRefreshToken((string)$user['id_usuario']);

            echo json_encode([
                'access_token'  => $access_token,
                'refresh_token' => $refresh_token,
                'token_type'    => 'Bearer',
                'expires_in'    => JWT_EXPIRATION,
                'user' => [
                    'id'      => $user['id_usuario'],
                    'correo'  => $user['correo'],
                    'nombre'  => $user['nombre'],
                    'subred'  => $user['subred'],
                    'perfil'  => $user['perfil']
                ]
            ]);
        } catch (Exception $e) {
            // error_log(date('Y-m-d H:i:s') . ' LOGIN ERROR: ' . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../../logs/api.log');
            http_response_code(500);
            echo json_encode(['error' => 'Error en el servidor']);
        }
    }

    public static function logout(): void {
        try {
            $payload = Auth::isAuthorized();
            if (!$payload) {
                http_response_code(401);
                echo json_encode(['error' => 'Token inválido']);
                return;
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("INSERT INTO revoked_tokens (jti) VALUES (?)");
            $stmt->execute([$payload['jti']]);

            echo json_encode(['message' => 'Sesión cerrada correctamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al cerrar sesión']);
        }
    }

    private static function obtenerPermisosUsuario(string $perfil): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT modulo, componente, consultar, editar, crear, ajustar, importar
            FROM adm_roles WHERE perfil = ? AND estado = 'A'");
        $stmt->execute([$perfil]);
        $permisos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = "{$row['modulo']}.{$row['componente']}";
            $permisos[$key] = [
                'consultar' => $row['consultar'] === 'SI',
                'editar'    => $row['editar'] === 'SI',
                'crear'     => $row['crear'] === 'SI',
                'ajustar'   => $row['ajustar'] === 'SI',
                'importar'  => $row['importar'] === 'SI'
            ];
        }
        return $permisos;
    }
}