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
            error_log('JWT error: ' . $e->getMessage(), 3, __DIR__ . '/../../logs/api.log');
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
        error_log(__FILE__ . ':' . __LINE__ . ' HEADERS: ' . print_r($headers, true), 3, __DIR__ . '/../../logs/api.log');
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        error_log(__FILE__ . ':' . __LINE__ . ' AUTH_HEADER: ' . print_r($authHeader, true), 3, __DIR__ . '/../../logs/api.log');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            error_log(__FILE__ . ':' . __LINE__ . ' NO BEARER TOKEN ENCONTRADO', 3, __DIR__ . '/../../logs/api.log');
            return null;
        }
        error_log(__FILE__ . ':' . __LINE__ . ' TOKEN EXTRAÍDO: ' . $matches[1], 3, __DIR__ . '/../../logs/api.log');
        return $matches[1];
    }

    private static function validateClaims(array $payload): bool {
        $now = time();
        return
            ($payload['iss'] ?? null) === self::$jwtIssuer &&
            ($payload['aud'] ?? null) === self::$jwtAudience &&
            ($payload['iat'] ?? 0) <= $now &&
            ($payload['exp'] ?? 0) > $now &&
            !empty($payload['sub']);
    }

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
            // Si el refresh token contiene roles/permisos, mantenerlos
            $customClaims = [];
            if (isset($payload['roles'])) $customClaims['roles'] = $payload['roles'];
            if (isset($payload['scope'])) $customClaims['scope'] = $payload['scope'];

            return self::generarTokenJWT($payload['sub'], $customClaims);
        } catch (Exception $e) {
            error_log('Error al refrescar token: ' . $e->getMessage(), 3, __DIR__ . '/../../logs/api.log');
            return null;
        }
    }

    /**
     * Verifica si el payload contiene un rol específico
     */
    public static function tieneRol(array $payload, string $rol): bool {
        return isset($payload['roles']) && in_array($rol, (array)$payload['roles']);
    }

    /**
     * Verifica si el payload contiene un permiso/scope específico
     */
    public static function tienePermiso(array $payload, string $permiso): bool {
        return isset($payload['scope']) && in_array($permiso, (array)$payload['scope']);
    }
}

Auth::init();
