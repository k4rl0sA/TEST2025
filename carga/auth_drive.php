<?php
session_start();
require_once '../libs/vendor/autoload.php';
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../libs/credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setRedirectUri('https://pruebagtaps.site/carga/auth_drive.php');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
if (!isset($_GET['code'])) {
    // Redirige al usuario a la pantalla de autorización de Google
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit;
} else {
    // Recibe el código de autorización y obtiene el token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['access_token'])) {
        $_SESSION['google_access_token'] = $token;
        echo "Token guardado en sesión. Ya puedes usar cargadrive.php.";
    } else {
        echo "Error al obtener el token: " . json_encode($token);
    }
}