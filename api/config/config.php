<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

define('JWT_SECRET', $_ENV['JWT_SECRET']);
define('JWT_ALGORITHM', $_ENV['JWT_ALGORITHM']);
define('JWT_ISSUER', $_ENV['APP_NAME']);
define('JWT_AUDIENCE', $_ENV['DOMAIN']);
define('JWT_EXPIRATION', intval($_ENV['JWT_EXPIRATION']));