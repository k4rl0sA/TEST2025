<?php
// File: api/lib/security.php
declare(strict_types=1);

// Evitar acceso directo
// defined('API_DIR') || exit(header('HTTP/1.1 403 Forbidden'));

class Security {
    // Tipos de sanitización disponibles
    public const FILTER_STRING = 'string';
    public const FILTER_EMAIL = 'email';
    public const FILTER_URL = 'url';
    public const FILTER_INT = 'int';
    public const FILTER_FLOAT = 'float';
    public const FILTER_BOOL = 'bool';
    public const FILTER_RAW = 'raw'; // Para datos que requieren sanitización especial
    
    /**
     * Sanitiza un valor de entrada según su tipo
     */
    public static function sanitize($value, string $type = self::FILTER_STRING) {
        if (is_array($value)) {
            return self::sanitizeArray($value, $type);
        }
        
        $value = trim($value);
        
        switch ($type) {
            case self::FILTER_EMAIL:
                return filter_var($value, FILTER_SANITIZE_EMAIL);
                
            case self::FILTER_URL:
                return filter_var($value, FILTER_SANITIZE_URL);
                
            case self::FILTER_INT:
                return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                
            case self::FILTER_FLOAT:
                return (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                
            case self::FILTER_BOOL:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                
            case self::FILTER_RAW:
                // Para datos que necesitan sanitización especial (ej: contenido HTML)
                return self::sanitizeRaw($value);
                
            case self::FILTER_STRING:
            default:
                return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        }
    }

    /**
     * Sanitiza recursivamente un array
     */
    public static function sanitizeArray(array $input, string $type = self::FILTER_STRING): array {
        $sanitized = [];
        foreach ($input as $key => $value) {
            $sanitized[$key] = is_array($value) 
                ? self::sanitizeArray($value, $type) 
                : self::sanitize($value, $type);
        }
        return $sanitized;
    }

    /**
     * Sanitización especial para contenido raw (HTML, etc.)
     * Usar con precaución - solo cuando sea necesario
     */
    public static function sanitizeRaw(string $value): string {
         // Usar HTMLPurifier solo si es necesario
        if (strip_tags($value) !== $value && class_exists('HTMLPurifier')) {
                // Eliminar caracteres no deseados
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value);
                // Configuración segura para HTMLPurifier si está disponible
                    $config = HTMLPurifier_Config::createDefault();
                    $config->set('Core.Encoding', 'UTF-8');
                    $config->set('HTML.Doctype', 'HTML 5');
                    $config->set('HTML.Allowed', 'p,br,strong,em,a[href|title],ul,ol,li');
                    $purifier = new HTMLPurifier($config);
                    return $purifier->purify($value);
            }
            // Fallback básico si HTMLPurifier no está disponible
     return strip_tags($value);
    }

    /**
     * Verifica si el método HTTP está permitido
     * @throws Exception si el método no está permitido
     */
    public static function validateHttpMethod(array $allowedMethods): void {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, $allowedMethods, true)) {
            throw new Exception("Método HTTP no permitido", 405);
        }
    }

    /**
     * Obtiene los headers de forma segura
     */
    public static function getSafeHeaders(): array {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = self::sanitize($value);
            }
        }
        return $headers;
    }

    /**
     * Prevención de ataques XSS en la salida
     * @param mixed $data
     * @return mixed
     */
    public static function escapeOutput($data) {
        if (is_array($data)) {
            return array_map([self, 'escapeOutput'], $data);
        }
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
    }

    /**
     * Validación de origen para prevención de CSRF
     */
    public static function validateRequestOrigin(): bool {
        $allowedOrigins = [$_SERVER['HTTP_HOST']];
        
        // Añadir dominios adicionales desde configuración si es necesario
        if (defined('ALLOWED_DOMAINS')) {
            $extraDomains = array_filter(array_map('trim', explode(',', ALLOWED_DOMAINS)));
            $allowedOrigins = array_merge($allowedOrigins, $extraDomains);
        }
        
        $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? null;
        if (!$origin) return false;
        
        $originHost = parse_url($origin, PHP_URL_HOST);
        return in_array($originHost, $allowedOrigins, true);
    }

    /**
     * Rate Limiting básico por IP
     */
    public static function applyRateLimiting(string $key, int $maxRequests = 100, int $period = 60): void {
        $rateLimitKey = "rate_limit_{$key}_" . $_SERVER['REMOTE_ADDR'];
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (function_exists('apcu_exists') && function_exists('apcu_store') && function_exists('apcu_inc')) {
            if (!apcu_exists($rateLimitKey)) {
                apcu_store($rateLimitKey, 1, $period);
                return;
            }
            $count = apcu_inc($rateLimitKey, 1, $success);
            if (!$success) {
                apcu_store($rateLimitKey, 1, $period);
                $count = 1;
            }
            if ($count > $maxRequests) {
                throw new Exception("Demasiadas solicitudes", 429);
            }
            return;
        }
        if (!isset($_SESSION[$rateLimitKey])) {
            $_SESSION[$rateLimitKey] = [
                'count' => 1,
                'expire' => time() + $period
            ];
            return;
        }
        if ($_SESSION[$rateLimitKey]['expire'] < time()) {
            $_SESSION[$rateLimitKey] = [
                'count' => 1,
                'expire' => time() + $period
            ];
            return;
        }
        $_SESSION[$rateLimitKey]['count']++;
        if ($_SESSION[$rateLimitKey]['count'] > $maxRequests) {
            throw new Exception("Demasiadas solicitudes", 429);
        }
    }
}