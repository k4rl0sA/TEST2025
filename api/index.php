<?php
declare(strict_types=1);

define('API_DIR', dirname(__DIR__));

require __DIR__ . '/vendor/autoload.php';// Carga el autoloader de Composer
require __DIR__ . '/config.php';// Inicializa la configuración global
require_once API_DIR . '/lib/auth.php';

// Configuración de encabezados CORS y seguridad
header('Access-Control-Allow-Origin: ' . Config::get('ALLOWED_ORIGINS', '*'));
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, X-CSRF-Token');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Manejar solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluye el enrutador principal
require_once __DIR__ . '/routes/router.php';