<?php
// File: api/lib/auth.php
declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

defined('API_DIR') || exit(header('HTTP/1.1 403 Forbidden'));

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

class Auth {
    private static string $jwtSecret;
    private static string $jwtAlgorithm;
    private static string $jwtIssuer;
    private static string $jwtAudience;
    private static int $jwtExpiration;

    public static function init(): void {
        self::$jwtSecret = JWT_SECRET;
        self::$jwtAlgorithm = JWT_ALGORITHM;
        self::$jwtIssuer = JWT_ISSUER;
        self::$jwtAudience = JWT_AUDIENCE;
        self::$jwtExpiration = JWT_EXPIRATION;
    }

    public static function isAuthorized(): array|false {
        $token = self::getBearerToken();
        if (!$token) return false;

        try {
            $key = self::getCurrentKey(); // clave rotativa
            $decoded = JWT::decode($token, new Key($key, self::$jwtAlgorithm));
            $payload = (array) $decoded;
        if (self::isTokenRevoked($payload['jti'])) {
            return false;
        }
            return self::validateClaims($payload) ? $payload : false;
        } catch (ExpiredException|SignatureInvalidException|DomainException|UnexpectedValueException|Exception $e) {
            error_log(date('Y-m-d H:i:s').' JWT error: ' . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../../logs/api.log');
            return false;
        }
    }

    private static function getCurrentKey(): string {
       $keyVersion = $_ENV['JWT_KEY_VERSION'] ?? 'default';
        return $_ENV["JWT_SECRET_{$keyVersion}"];
    }

    private static function isTokenRevoked(string $jti): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM revoked_tokens WHERE jti = ?");
        $stmt->execute([$jti]);
        return $stmt->fetchColumn() > 0;
    }

    private static function getBearerToken(): ?string {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            // error_log(date('Y-m-d H:i:s') . ' ' . __FILE__ . ':' . __LINE__ . ' NO BEARER TOKEN ENCONTRADO' . PHP_EOL, 3, __DIR__ . '/../../logs/api.log');
            return null;
        }
        // error_log(date('Y-m-d H:i:s') . ' ' . __FILE__ . ':' . __LINE__ . ' TOKEN EXTRAÍDO: ' . $matches[1] . PHP_EOL, 3, __DIR__ . '/../../logs/api.log');
        return $matches[1];
    }

    private static function validateClaims(array $payload): bool {
        $now = time();
        error_log('Validando claims: ' . json_encode($payload), 3, __DIR__ . '/../../logs/api.log');
        return
            ($payload['iss'] ?? null) === self::$jwtIssuer &&
            ($payload['aud'] ?? null) === self::$jwtAudience &&
            ($payload['iat'] ?? 0) <= $now &&
            ($payload['exp'] ?? 0) > $now &&
            !empty($payload['sub']);
    }

    // Genera un JWT solo con claims mínimos (sin permisos, solo info esencial)
    public static function generarTokenJWT(string $userId, array $customClaims = []): string {
        $now = time();
        $payload = array_merge([
            'iss' => self::$jwtIssuer,
            'aud' => self::$jwtAudience,
            'iat' => $now,
            'nbf' => $now - 1,
            'exp' => $now + self::$jwtExpiration,
            'sub' => $userId,
            'jti' => bin2hex(random_bytes(16))
        ], $customClaims);
        error_log(date('Y-m-d H:i:s') . ' Generando JWT para usuario: ' . $userId . ' con claims: ' . json_encode($customClaims) . PHP_EOL, 3, __DIR__ . '/../../logs/api.log');
        return JWT::encode($payload, self::$jwtSecret, self::$jwtAlgorithm);
    }

    public static function generarRefreshToken(string $userId): string {
        $now = time();
        $payload = [
            'iss' => self::$jwtIssuer,
            'aud' => self::$jwtAudience,
            'iat' => $now,
            'nbf' => $now - 1,
            'exp' => $now + (self::$jwtExpiration * 7 * 24),
            'sub' => $userId,
            'jti' => bin2hex(random_bytes(16)),
            'type' => 'refresh'
        ];
        return JWT::encode($payload, self::$jwtSecret, self::$jwtAlgorithm);
    }

    public static function refrescarToken(string $refreshToken): ?string {
        try {
            $key = self::getCurrentKey(); // Usar clave rotativa
            $decoded = JWT::decode($refreshToken, new Key($key, self::$jwtAlgorithm));
            $payload = (array) $decoded;

            if (($payload['type'] ?? '') !== 'refresh' || !self::validateClaims($payload)) {
                return null;
            }
            $customClaims = [];
            return self::generarTokenJWT($payload['sub'], $customClaims);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Verifica si el perfil tiene permiso para la acción en el módulo consultando la BD.
     * Esta función debe usarse SIEMPRE para validar permisos, nunca el JWT.
     */
    public static function tienePermisoBD(string $perfil, string $modulo, string $accion): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT $accion FROM adm_roles WHERE perfil = ? AND modulo = ?");
        $stmt->execute([$perfil, $modulo]);
        $permiso = $stmt->fetchColumn();
        $logMsg = sprintf('Permiso consultado: perfil=%s, modulo=%s, accion=%s, resultado=%s', $perfil, $modulo, $accion, var_export($permiso, true));
        error_log(date('Y-m-d H:i:s') . ' ' . $logMsg . PHP_EOL, 3, __DIR__ . '/../../logs/api.log');
        return $permiso === 'SI'; // O el valor que uses para permitir
    }
}

Auth::init();
