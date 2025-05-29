<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye tu archivo de configuración
require_once __DIR__ . '/config.php'; // Asegúrate de que esta ruta sea correcta

try {
    // Crear la conexión utilizando las credenciales del archivo de configuración
    $pdo = new PDO("mysql:host={$dbConfig['s']};dbname={$dbConfig['bd']}", $dbConfig['u'], $dbConfig['p']);
    echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
