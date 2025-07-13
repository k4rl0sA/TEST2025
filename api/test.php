<?php
// test.php en la raíz de tu API
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'API funcionando',
    'server' => $_SERVER,
    'get' => $_GET,
    'post' => $_POST
]);