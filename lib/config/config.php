<?php
function loadEnv($filePath, $requiredEnv) {
    if (!file_exists($filePath)) {
        throw new Exception("El archivo .env no existe en la ruta especificada: $filePath");
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '#') === 0) {
            continue;
        }
        // Buscar la primera '=' para separar KEY=VALUE (permitiendo '=' en el valor)
        $pos = strpos($line, '=');
        if ($pos === false) {
            // Línea no válida sin '=', ignorarla
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $rawValue = substr($line, $pos + 1);
        $value = trim($rawValue);
        // Preservar valores entrecomillados (permitir '#' dentro de las comillas)
        $firstChar = isset($value[0]) ? $value[0] : '';
        $lastChar = isset($value[strlen($value) - 1]) ? $value[strlen($value) - 1] : '';
        if (($firstChar === '"' && $lastChar === '"') || ($firstChar === "'" && $lastChar === "'")) {
            $value = substr($value, 1, -1);
            if ($firstChar === '"') {
                // Interpretar escapes en comillas dobles
                $value = stripcslashes($value);
            } else {
                // Mantener comillas simples, desescapando si se usó \'
                $value = str_replace("\\'", "'", $value);
            }
        } else {
            // Para valores sin comillas, eliminar comentarios inline que comienzan con espacio seguido de #
            // (ej: VALUE # comentario). Si necesita preservar un '#' sin comillas, envuelva el valor entre comillas.
            $parts = preg_split('/\s+#/', $value, 2);
            $value = rtrim($parts[0]);
        }
        if (!in_array($key, $requiredEnv, true)) {
            throw new Exception("La clave '$key' en el archivo .env no está definida en la lista de variables requeridas.");
        }
        $envValue = getenv($key);
        $finalValue = ($envValue !== false) ? $envValue : $value;
        if (!defined($key)) {
            define($key, $finalValue); // Definir la constante
        }
    }
}
$requiredEnv = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_PORT', 'SESSION_NAME', 'HASH_ALGORITHM', 'ENCRYPTION_KEY', 'API_BASE_URL', 'API_KEY', 'MAIL_HOST', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_PORT', 'MAIL_ENCRYPTION', 'APP', 'VERS', 'DOMINIO', 'ERROR_LOG_PATH', 'MOSTRAR_ERRORES', 'SESSION_SAVE_PATH', 'APP_ENV'];
try {
    loadEnv(__DIR__ . '/.env', $requiredEnv);
} catch (Exception $e) {
    echo 'Error de configuración: ' . $e->getMessage();
    exit(1);
}
// Configuración de la sesión (USAR CONSTANTES)
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => DOMINIO, // Usar la constante DOMINIO
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Strict'
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_regenerate_id();
// Verificar si es una nueva sesion
if (!isset($_SESSION['LAST_ACTIVITY'])) {
    // Es una sesión nueva.
    $_SESSION['LAST_ACTIVITY'] = time();
}
// Verificar si la sesión ha expirado (ej. 30 minutos de inactividad)
if (time() - $_SESSION['LAST_ACTIVITY'] > 3600) {
    // Destruir la sesión anterior
    session_destroy();
    session_start();
    $_SESSION['LAST_ACTIVITY'] = time();
}
$_SESSION['LAST_ACTIVITY'] = time();
if (!isset($_SESSION['csrf_tkn'])) {
    $_SESSION['csrf_tkn'] = bin2hex(random_bytes(32)); // Genera un token seguro
}
// Configuración de errores
$mostrar_errores = filter_var(MOSTRAR_ERRORES, FILTER_VALIDATE_BOOLEAN);
ini_set('display_errors', $mostrar_errores ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ERROR_LOG_PATH ?: __DIR__ . '/../errors.log');
// Otras configuraciones de la aplicación
setlocale(LC_TIME, 'es_CO');

//date_default_timezone_set('America/Bogota');
