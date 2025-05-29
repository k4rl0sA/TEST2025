<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/libs/config.php'; // Asegúrate de incluir la configuración primero
require_once __DIR__ . '/libs/auth.php';    // Luego incluye auth.php para poder usar login()

session_start(); // Inicia la sesión aquí

// Conectar a la base de datos
conectarBD($dbConfig); // Asegúrate de llamar a esta función para establecer la conexión

if (isset($_SESSION['us_sds'])) {
    header("Location: gestion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (login($username, $password)) { // Ahora debería estar disponible
        header("Location: main/");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>
    <form method="POST" action="">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Iniciar Sesión</button>
    </form>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</body>
</html>
