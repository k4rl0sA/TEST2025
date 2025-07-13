<?php
// test.php en la raíz de tu API
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'API funcionando',
    'server' => [
    'get' => array_intersect_key($_GET, array_flip(['safe_key1', 'safe_key2'])),
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? null,
    ],
    'post' => array_intersect_key($_POST, array_flip(['key1', 'key2'])), // Replace 'key1', 'key2' with required keys
    'hasheada' => password_hash('Hogar2020+', PASSWORD_DEFAULT),
    'claveOk' => password_verify('Hogar2020+', '$2y$10$lTl9tdSIGWq1FUASoxZuRurRZ0VxDWnq4NBOhdpsyfaULiSEZO2Ia'),
    'pruebaLog' => error_log('Prueba de log', 3, __DIR__ . '/../logs/pi.log')
]);