<?php
// File: api/config.php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Evitar acceso directo
defined('API_DIR') || exit(header('HTTP/1.1 403 Forbidden'));
ini_set('error_log', __DIR__ . '/../logs/api.log');

// 1. Carga de variables de entorno optimizada
class Config {
    private static $loaded = false;
    private static $env = [];
    
    public static function init() {
        if (self::$loaded) return;

        $envPath = __DIR__.'/.env';
        // var_dump($envPath); // Depuración: Verifica la ruta buscada
        
        // Cargar variables de entorno desde .env si existe
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            // var_dump($lines); // Depuración: Verificar líneas leídas
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '#') === 0) continue; // Saltar comentarios y líneas vacías
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'"); // Elimina espacios y comillas
                putenv("$name=$value");
                $_ENV[$name] = $value;
                self::$env[$name] = $value;
            }
        }else{
            var_dump("No se encontró el archivo .env"); // Depuración: Archivo .env no encontrado
        }
        
        // Validar variables críticas
        self::validateRequiredEnv();
        
        self::$loaded = true;
    }
    
    private static function validateRequiredEnv() {
    $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'JWT_SECRET'];
        foreach ($required as $var) {
            if (empty(self::$env[$var])) {
                throw new RuntimeException("Variable de entorno crítica faltante: $var");
            }
        }
    }

    public static function get($key, $default = null) {
            return self::$env[$key] ?? $default;
        }
    }

// Inicializar configuración
Config::init();

// 2. Definición de constantes mejorada
// Configuración de base de datos
define('DB_HOST', Config::get('DB_HOST', 'localhost'));
define('DB_NAME', Config::get('DB_NAME', 'mi_base_de_datos'));
define('DB_USER', Config::get('DB_USER', 'usuario'));
define('DB_PASS', Config::get('DB_PASS', 'clave'));
define('DB_PORT', Config::get('DB_PORT', '3306'));
define('DB_CHARSET', Config::get('DB_CHARSET', 'utf8mb4'));

// Seguridad JWT
// Configuración JWT
define('JWT_ISSUER', 'pruebagtaps.site');
define('JWT_AUDIENCE', 'pruebagtaps.site');
define('JWT_SECRET', Config::get('JWT_SECRET', 'TU_SECRETO_SUPER_SEGURO'));
define('JWT_ALGORITHM', Config::get('JWT_ALGORITHM', 'HS256'));
define('JWT_EXPIRATION', (int)Config::get('JWT_EXPIRATION', 3600));

// Conexión a base de datos mejorada

class Database {
    private static $pdo = null;
    
    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
                PDO::ATTR_STRINGIFY_FETCHES  => false
            ];
            
            // Conexión segura con SSL si está configurado
            if (Config::get('DB_SSL', 'false') === 'true') {
                $options[PDO::MYSQL_ATTR_SSL_CA] = Config::get('DB_SSL_CA');
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }
            
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                self::handleConnectionError($e);
            }
        }
        return self::$pdo;
    }
    
    private static function handleConnectionError(PDOException $e) {
        // En producción, no mostrar detalles internos
        $isProduction = Config::get('APP_ENV', 'development') === 'production';
        
        error_log('Database connection error: ' . $e->getMessage());
        
        if ($isProduction) {
            header('HTTP/1.1 503 Service Unavailable');
            exit(json_encode(['error' => 'Service temporarily unavailable']));
        } else {
            throw new PDOException(
                'Database connection error: ' . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }
    
    // Evitar clonación e instanciación
    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// 4. Configuración de seguridad adicional
// Configuración de CORS
if (!headers_sent()) {
header('Access-Control-Allow-Origin: ' . Config::get('ALLOWED_ORIGINS', '*'));
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');
header('Access-Control-Allow-Credentials: true');
// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
}

// Configuración de sesión segura
/* if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', Config::get('SESSION_SECURE', '1'));
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.sid_length', '128');
    ini_set('session.sid_bits_per_character', '6');
    
    session_name(Config::get('SESSION_NAME', 'secure_session'));
    session_start();
}
 */
// ==============================================
// 5. Manejo de errores
// ==============================================

/* if (Config::get('APP_ENV', 'development') === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} */

set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        error_log("Fatal error: {$error['message']} in {$error['file']} on line {$error['line']}");
        if (Config::get('APP_ENV') !== 'production') {
            echo json_encode([
                'error' => 'Internal Server Error',
                'details' => [
                    'message' => $error['message'],
                    'file' => $error['file'],
                    'line' => $error['line']
                ]
            ]);
        } else {
            echo json_encode(['error' => 'Internal Server Error']);
        }
        exit;
    }
});