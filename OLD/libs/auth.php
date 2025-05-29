<?php
function is_logged_in() {
    return isset($_SESSION[SESSION_NAME]) && !empty($_SESSION[SESSION_NAME]);
}

// Función para iniciar sesión
function login($username, $password) {
    global $pdo; // Asegúrate de que $pdo esté definido y conectado a la base de datos
    $sql = "SELECT id_usuario, nombre, clave FROM usuarios WHERE id_usuario = :username AND estado = 'A'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['clave'])) {
        $_SESSION['us_sds'] = $user['id_usuario'];
        $_SESSION['nomb'] = $user['nombre'];
        return true;
    } else {
        return false;
    }
}

// Función de conexión a la base de datos
function conectarBD($dbConfig) {
    global $pdo; // Asegúrate de que $pdo esté accesible globalmente
    $dsn = "mysql:host={$dbConfig['s']};dbname={$dbConfig['bd']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
    try {
        $pdo = new PDO($dsn, $dbConfig['u'], $dbConfig['p']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log("Conexión fallida: " . $e->getMessage());
        die('No se pudo conectar a la base de datos.');
    }
}
?>
