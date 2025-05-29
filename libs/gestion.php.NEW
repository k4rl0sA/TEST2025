<?php
ini_set('display_errors', 1);
require_once __DIR__ . '/libs/config.php';
require_once __DIR__ . '/libs/auth.php';

session_name(SESSION_NAME);
session_start(); // Llama a session_start() solo si es necesario

var_dump('ID de sesión en gestión: ', session_id());
var_dump('Contenido de la sesión en gestión: ', $_SESSION);

// Verificación de inicio de sesión
if (!is_logged_in()) {
    exit("Redireccionando a index.php debido a sesión inválida.");
}

// Conectar a la base de datos
conectarBD($dbConfig); // Llama a la conexión aquí también si es necesario

$req = isset($_REQUEST['a']) ? $_REQUEST['a'] : '';
switch ($req) {
    case '':
        break;

    case 'exportar':
        $now = date("ymd");
        header_csv($_REQUEST['b'] . '_' . $now . '.csv');
        $info = datos_mysql($_SESSION['tot_' . $_REQUEST['b']]);
        $total = $info['responseResult'][0]['total'];
        
        // Uso de sentencias preparadas para evitar inyecciones SQL
        $stmt = $pdo->prepare($_SESSION['sql_' . $_REQUEST['b']]);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            echo csv($result, $total);
        } else {
            echo "Error: No se encontraron resultados.";
        }
        die;

    case 'upload':
        $tb = $_POST['b'];
        $fe = strftime("%Y-%m-%d %H:%M");
        $ru = $ruta_upload . '/' . $tb . '/' . $_SESSION[SESSION_NAME] . '/';
        $fi = $ru . $fe . '.csv';
        if (!is_dir($ru)) {
            mkdir($ru, 0755, true);
        }
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $fi)) {
            echo "Error al mover el archivo: " . $_FILES['archivo']['error'];
        } else {
            echo "Archivo subido correctamente.";
        }
        break;

    default:
        echo "Solicitud no válida.";
        break;
}

/* function login($username, $password) {
    global $pdo;
    $sql = "SELECT id_usuario, nombre, clave FROM usuarios WHERE id_usuario = :username AND estado = 'A'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Verifica la contraseña usando password_verify
    if ($user && password_verify($password, $user['clave'])) {
        $_SESSION['us_sds'] = $user['id_usuario'];
        $_SESSION['nomb'] = $user['nombre'];
        return true;
    } else {
        return false;
    }
}*/


 function header_csv($filename) {
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: ".$now);
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header("Content-Transfer-Encoding: binary");
}

//Generar el contenido CSV
function csv($data, $total = null) {
    $df = fopen("php://output", 'w');
    ob_start();
    fwrite($df, "\xEF\xBB\xBF");
    // Si hay datos, agregar encabezados y filas
    if (!empty($data)) {
        fputcsv($df, array_keys($data[0]), '|');
        foreach ($data as $row) {
            fputcsv($df, $row, '|');
        }
    }
    if ($total !== null) {
        fwrite($df, "Total Registros: " . $total . PHP_EOL);
    }
    fclose($df);
    return ob_get_clean();
}

//limpiar texto
function cleanTx($val) {
    $val = trim($val);
    $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    $pattern = '/[^\w\s\.\-@:]/'; // Permitimos alfanuméricos, espacios, puntos, guiones, arroba y dos puntos
    $val = preg_replace('/\s+/', ' ', $val); // Remover múltiples espacios
    $val = preg_replace($pattern, '', $val); // Quitar caracteres no permitidos
    $val = str_replace(array("\n", "\r", "\t"), '', $val); // Eliminar saltos de línea y tabulaciones
    return strtoupper($val);
}

// Obtener datos de BD
function datos_mysql($sql, $params = [], $resulttype = PDO::FETCH_ASSOC) {
    global $pdo;
    $arr = ['code' => 0, 'message' => '', 'responseResult' => []];
    if (!$pdo) {
        return json_encode(['code' => 30, 'message' => 'Connection error']);
    }
    try {
        $stmt = $pdo->prepare($sql);
        // Limpiar y unir parámetros si los hay
        foreach ($params as $key => $param) {
            if ($param['value'] === NULL) {
                $stmt->bindValue($key + 1, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($key + 1, cleanTx($param['value']), $param['type'] === 'i' ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        $arr['responseResult'] = $stmt->fetchAll($resulttype);
        $arr['code'] = 0;
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, $error_log_path);
        $arr = [
            'code' => 30,
            'message' => 'Error BD',
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]
        ];
    }
    return $arr;
}

//Tomar y preparar parametros
function params($campos) {
    $params = [];
    foreach ($campos as $campo) {
        if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
            $params[] = [
                'type' => is_numeric($_POST[$campo]) ? 'i' : 's', 
                'value' => $_POST[$campo]
            ];
        } else {
            $params[] = ['type' => 's', 'value' => NULL];
        }
    }
    return $params;
}

//Preparar y ejecutar consultas SQL
function mysql_prepd($sql, $params) {
    global $pdo; // Usar PDO globalmente
    $arr = ['code' => 0, 'message' => '', 'responseResult' => []];
    if (!$pdo) {
        return json_encode(['code' => 30, 'message' => 'Connection error']);
    }
    try {
        $stmt = $pdo->prepare($sql);
        $types = '';
        $values = [];
        // Limpiar y preparar parámetros
        foreach ($params as $param) {
            $type = $param['type'];
            $value = ($type === 's') ? cleanTx($param['value']) : $param['value'];
            $types .= $type === 'i' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $values[] = $value;
        }
        // Binding de parámetros
        foreach ($values as $key => $value) {
            $stmt->bindValue($key + 1, $value, $types[$key]);
        }
        if ($stmt->execute()) {
            $affected = $stmt->rowCount();
            $op = 'Operación desconocida';
            // Determinar la operación realizada
            $sqlType = strtoupper(trim(strtok($sql, " ")));
            if ($sqlType === 'DELETE') {
                $op = 'Eliminado';
            } elseif ($sqlType === 'INSERT') {
                $op = 'Insertado';
            } elseif ($sqlType === 'UPDATE') {
                $op = 'Actualizado';
            }
            if ($affected > 0) {
                $arr['message'] = "Se ha " . $op . ": " . $affected . " registro(s) correctamente.";
            } else {
                $arr['message'] = "No se afectaron registros con la operación: " . $op;
            }
        } else {
            $arr['message'] = "Error al ejecutar la consulta.";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, $error_log_path);
        $arr = [
            'code' => 30,
            'message' => 'Error',
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]
        ];
    }
    return $arr;
}

//crear desplegables con BD
function opc_sql($sql, $val, $str = true) {
    $rta = "<option value='' class='alerta'>SELECCIONE</option>";
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_NUM);
        foreach ($result as $r) {
            $selected = ($r[0] == $val) ? " selected" : "";
            $rta .= "<option value='" . htmlentities($r[0], ENT_QUOTES) . "'$selected>" . htmlentities($r[1], ENT_QUOTES) . "</option>";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, $error_log_path);
    }
    return $rta;
}

//crear desplegables de un array
function opc_arr($a = [], $b = "", $c = true) {
    $rta = "<option value='' class='alerta'>SELECCIONE</option>";
    if (!empty($a)) {
        foreach ($a as $item) {
            $on = "";
            if (is_array($item) && isset($item['v']) && isset($item['l'])) {
                $valor = strtoupper($item['v']);
                $label = strtoupper($item['l']);
                $on = ($valor == strtoupper($b) || $label == strtoupper($b)) ? " selected='selected'" : ($c === false ? " disabled='disabled'" : "");
                $rta .= "<option $on value='" . htmlentities($item['v'], ENT_QUOTES) . "'>" . htmlentities($item['l'], ENT_QUOTES) . "</option>\n";
            } elseif (!is_array($item)) {
                $on = strtoupper($item) == strtoupper($b) ? " selected='selected'" : ($c === false ? " disabled='disabled'" : "");
                $rta .= "<option $on value='" . htmlentities($item, ENT_QUOTES) . "'>" . htmlentities($item, ENT_QUOTES) . "</option>\n";
            }
        }
    }
    return $rta;
}

function perfil($modulo) {
    $perf = rol($modulo);
    //  var_dump($perf);  
    if (empty($perf['perfil'])) {
        echo '<div class="lock">
            <i class="fas fa-lock fa-5x lock-icon"></i>
            <h2>Acceso No Autorizado</h2>
            Lo siento, no tienes permiso para acceder a esta área.
            </div>';
        exit();
    }
}

function rol($modulo) {
    $rta = array();
    global $pdo;
    try {
        $usuario = $_SESSION[SESSION_NAME] ?? '';
        $sql = "SELECT perfil, componente, crear, editar, consultar, exportar, importar 
                FROM adm_roles 
                WHERE modulo = :modulo 
                  AND perfil = FN_PERFIL(:perfil) 
                  AND componente = FN_COMPONENTE(:componente) 
                  AND estado = 'A'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':modulo' => $modulo,
            ':perfil' => $usuario,
            ':componente' => $usuario
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $rta = $data;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '../errors.log');
    }
    return $rta;
}
 

?>
