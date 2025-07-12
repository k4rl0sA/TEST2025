<?php
// File: api/routes/router.php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Manejar solicitudes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 204 No Content');
    exit;
}

// Directorio base de la API
define('API_DIR', dirname(__DIR__));

// Cargar configuración y dependencias
require_once API_DIR . '/config.php';
require_once API_DIR . '/lib/security.php';
require_once API_DIR . '/lib/middleware.php';

// Obtener ruta limpia
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/api';
$route = str_replace($basePath, '', $requestUri);

// Sistema de enrutamiento mejorado
$routes = [
    'POST /auth/login' => ['AuthController', 'login', []],
    'POST /auth/logout' => ['AuthController', 'logout', ['auth']],
    'GET /{tabla}' => ['CrudController', 'listar', ['permission:{tabla}.leer']],
    'GET /{tabla}/{id}' => ['CrudController', 'obtenerUno', ['permission:{tabla}.leer']],
    'POST /{tabla}' => ['CrudController', 'crear', ['permission:{tabla}.crear']],
    'PUT /{tabla}/{id}' => ['CrudController', 'actualizar', ['permission:{tabla}.editar']],
    'PATCH /{tabla}/{id}/activar' => ['CrudController', 'activar', ['permission:{tabla}.ajustar']],
    'PATCH /{tabla}/{id}/inactivar' => ['CrudController', 'inactivar', ['permission:{tabla}.ajustar']],
    'GET /reportes/{tipo}' => ['ReportController', 'generarReporte', ['auth']],
];

// Registrar shutdown function para manejo de errores
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Internal server error',
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
    }
});

// Procesamiento de rutas
try {
    $matched = false;
    
    foreach ($routes as $pattern => $handlerConfig) {
        [$method, $pathPattern] = explode(' ', $pattern, 2);
        $pathRegex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pathPattern) . '$#';
        
        if ($_SERVER['REQUEST_METHOD'] === $method && preg_match($pathRegex, $route, $matches)) {
            $matched = true;
            
            // Extraer parámetros de la ruta
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            
            // Cargar controlador
            [$controllerName, $actionName, $middlewares] = $handlerConfig;
            $controllerFile = API_DIR . "/controllers/$controllerName.php";
            
            if (!file_exists($controllerFile)) {
                throw new RuntimeException("Controlador $controllerName no encontrado");
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controllerName)) {
                throw new RuntimeException("Clase $controllerName no definida");
            }
            
            // Aplicar middlewares
            foreach ($middlewares as $middleware) {
                if ($middleware === 'auth') {
                    requireAuth();
                } elseif (strpos($middleware, 'permission:') === 0) {
                    $permission = str_replace('permission:', '', $middleware);
                    
                    // Reemplazar placeholders con valores reales
                    foreach ($params as $key => $value) {
                        $permission = str_replace("{{$key}}", $value, $permission);
                    }
                    
                    requirePermission($permission);
                }
            }
            
            // Instanciar controlador y ejecutar acción
            $controller = new $controllerName();
            
            if (!method_exists($controller, $actionName)) {
                throw new RuntimeException("Método $actionName no existe en $controllerName");
            }
            
            call_user_func_array([$controller, $actionName], $params);
            break;
        }
    }
    
    if (!$matched) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint no encontrado']);
    }
} catch (Throwable $e) {
    $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
    http_response_code($statusCode);
    header('Content-Type: application/json');
    
    $response = ['error' => $e->getMessage()];
    if ($_ENV['APP_ENV'] !== 'production') {
        $response['trace'] = $e->getTrace();
    }
    
    echo json_encode($response);
}