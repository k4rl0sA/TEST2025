<?php
// File: api/controllers/CrudController.php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/controller.php';

class CrudController {
    private static $tablasConfig;

    public static function init() {
        self::$tablasConfig = require API_DIR . '/config/tablas.php';
    }

    public static function listar(string $tabla): void {
        try {
            $pdo = Database::getConnection();
            $config = self::getTablaConfig($tabla);

            $pagina = $_GET['pagina'] ?? 1;
            $limite = $_GET['limite'] ?? 10;
            $ordenCampo = $_GET['ordenCampo'] ?? $config['order'][0] ?? 'id';
            $ordenDir = strtoupper($_GET['ordenDir'] ?? 'ASC');
            $filtros = $_GET['filtros'] ?? [];

            // Validación y sanitización
            $ordenCampo = Security::sanitize($ordenCampo);
            $ordenDir = ($ordenDir === 'DESC') ? 'DESC' : 'ASC';

            $resultado = self::listarRegistros($tabla, $pagina, $limite, $filtros, $ordenCampo, $ordenDir);

            // Ocultar campos sensibles
            if (!empty($config['hidden'])) {
                foreach ($resultado['data'] as &$fila) {
                    foreach ($config['hidden'] as $campo) {
                        unset($fila[$campo]);
                    }
                }
            }

            echo json_encode($resultado);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private static function getTablaConfig(string $tabla): array {
        if (!isset(self::$tablasConfig[$tabla])) {
            throw new Exception('Tabla no configurada');
        }
        return self::$tablasConfig[$tabla];
    }

    private static function listarRegistros(
        string $tabla,
        int $pagina,
        int $limite,
        array $filtros,
        string $ordenCampo,
        string $ordenDir
    ): array {
        // Implementación completa con consultas preparadas
        // ... (similar a tu función original pero con PDO seguro)
    }
    // Implementar métodos restantes (crear, actualizar, etc.)
     public static function crear(string $tabla): void {
    try {
        $pdo = Database::getConnection();
        $config = self::getTablaConfig($tabla);
        $input = self::getSanitizedInput($config);

        // Validación
        self::validateInput($input, $config['validation']['crear'] ?? []);

        // Callbacks
        if (isset($config['callbacks']['before_create'])) {
            $input = self::executeCallback($config['callbacks']['before_create'], $input, $tabla);
        }

        // Construir consulta para clave primaria compuesta
        $columns = implode(', ', array_keys($input));
        $placeholders = ':' . implode(', :', array_keys($input));
        $sql = "INSERT INTO $tabla ($columns) VALUES ($placeholders)";

        // Ejecutar
        $stmt = $pdo->prepare($sql);
        $stmt->execute($input);

        // Obtener registro creado usando clave primaria compuesta
        $whereClause = [];
        $params = [];
        
        foreach ($config['primary_key'] as $pk) {
            $whereClause[] = "$pk = :$pk";
            $params[$pk] = $input[$pk];
        }
        
        $sql = "SELECT * FROM $tabla WHERE " . implode(' AND ', $whereClause);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $newRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode($newRecord);
    } catch (Throwable $e) {
        self::handleError($e);
    }
}

public static function actualizar(string $tabla, $id): void {
    try {
        $pdo = Database::getConnection();
        $config = self::getTablaConfig($tabla);
        $input = self::getSanitizedInput($config);

        // Validación
        self::validateInput($input, $config['validation']['actualizar'] ?? []);

        // Callbacks
        if (isset($config['callbacks']['before_update'])) {
            $input = self::executeCallback($config['callbacks']['before_update'], $input, $tabla);
        }

        // Construir consulta para clave primaria compuesta
        $setClause = [];
        foreach ($input as $column => $value) {
            $setClause[] = "$column = :$column";
        }
        
        $whereClause = [];
        $params = $input;
        
        // Parsear ID compuesto (formato: idgeo|estado_v|usu_creo)
        $idParts = explode('|', $id);
        if (count($idParts) !== count($config['primary_key'])) {
            throw new Exception("ID inválido para la tabla $tabla", 400);
        }
        
        foreach ($config['primary_key'] as $index => $pk) {
            $whereClause[] = "$pk = :pk_$index";
            $params["pk_$index"] = $idParts[$index];
        }
        
        $sql = "UPDATE $tabla SET " . implode(', ', $setClause) . 
               " WHERE " . implode(' AND ', $whereClause);

        // Ejecutar
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Obtener registro actualizado
        $sql = "SELECT * FROM $tabla WHERE " . implode(' AND ', $whereClause);
        $stmt = $pdo->prepare($sql);
        
        $pkParams = [];
        foreach ($config['primary_key'] as $index => $pk) {
            $pkParams[":pk_$index"] = $idParts[$index];
        }
        
        $stmt->execute($pkParams);
        $updatedRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($updatedRecord);
    } catch (Throwable $e) {
        self::handleError($e);
    }
}

public static function obtenerUno(string $tabla, $id): void {
    try {
        $pdo = Database::getConnection();
        $config = self::getTablaConfig($tabla);

        // Clave primaria compuesta
        $whereClause = [];
        $params = [];
        $idParts = explode('|', $id);
        if (count($idParts) !== count($config['primary_key'])) {
            throw new Exception("ID inválido para la tabla $tabla", 400);
        }
        foreach ($config['primary_key'] as $index => $pk) {
            $whereClause[] = "$pk = :pk_$index";
            $params[":pk_$index"] = $idParts[$index];
        }
        $sql = "SELECT * FROM $tabla WHERE " . implode(' AND ', $whereClause);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            http_response_code(404);
            echo json_encode(['error' => 'Registro no encontrado']);
            return;
        }

        foreach ($config['hidden'] ?? [] as $campo) {
            unset($record[$campo]);
        }

        echo json_encode($record);
    } catch (Throwable $e) {
        self::handleError($e);
    }
}

// Los métodos activar/inactivar serían similares a actualizar pero solo cambian el estado

    public static function actualizar(string $tabla, $id): void {
    try {
        $pdo = Database::getConnection();
        $config = self::getTablaConfig($tabla);
        $input = self::getSanitizedInput($config);

        // Validación
        self::validateInput($input, $config['validation']['actualizar'] ?? []);

        // Callbacks
        if (isset($config['callbacks']['before_update'])) {
            $input = self::executeCallback($config['callbacks']['before_update'], $input, $tabla);
        }

        // Construir consulta para clave primaria compuesta
        $setClause = [];
        foreach ($input as $column => $value) {
            $setClause[] = "$column = :$column";
        }
        
        $whereClause = [];
        $params = $input;
        
        // Parsear ID compuesto (formato: idgeo|estado_v|usu_creo)
        $idParts = explode('|', $id);
        if (count($idParts) !== count($config['primary_key'])) {
            throw new Exception("ID inválido para la tabla $tabla", 400);
        }
        
        foreach ($config['primary_key'] as $index => $pk) {
            $whereClause[] = "$pk = :pk_$index";
            $params["pk_$index"] = $idParts[$index];
        }
        
        $sql = "UPDATE $tabla SET " . implode(', ', $setClause) . 
               " WHERE " . implode(' AND ', $whereClause);

        // Ejecutar
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Obtener registro actualizado
        $sql = "SELECT * FROM $tabla WHERE " . implode(' AND ', $whereClause);
        $stmt = $pdo->prepare($sql);
        
        $pkParams = [];
        foreach ($config['primary_key'] as $index => $pk) {
            $pkParams[":pk_$index"] = $idParts[$index];
        }
        
        $stmt->execute($pkParams);
        $updatedRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($updatedRecord);
    } catch (Throwable $e) {
        self::handleError($e);
    }
}

    public static function activar(string $tabla, int $id): void {
        self::cambiarEstado($tabla, $id, 'A');
    }

    public static function inactivar(string $tabla, int $id): void {
        self::cambiarEstado($tabla, $id, 'I');
    }

    private static function cambiarEstado(string $tabla, int $id, string $estado): void {
        try {
            $pdo = Database::getConnection();
            $config = self::getTablaConfig($tabla);

            $sql = "UPDATE $tabla SET estado = :estado WHERE id_usuario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id, 'estado' => $estado]);

            // Obtener registro actualizado
            $updatedRecord = self::getById($tabla, $id, $config);

            echo json_encode($updatedRecord);
        } catch (Throwable $e) {
            self::handleError($e);
        }
    }

    // ===== HELPER METHODS =====
    
    private static function getSanitizedInput(array $config): array {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $input = Security::sanitizeArray($input);
        
        // Filtrar solo campos editables
        return array_intersect_key($input, array_flip($config['editable']));
    }

    private static function validateInput(array $input, array $rules): void {
        // Implementar lógica de validación basada en las reglas
        // Ejemplo simplificado:
        foreach ($rules as $field => $fieldRules) {
            if (!array_key_exists($field, $input)) continue;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($input[$field])) {
                    throw new Exception("El campo $field es requerido", 400);
                }
                // Implementar otras reglas (email, min, regex, etc.)
            }
        }
    }

    private static function executeCallback(string $callbackName, array $input, string $tabla): array {
        switch ($callbackName) {
            case 'hashPassword':
                if (isset($input['clave'])) {
                    $input['clave'] = password_hash($input['clave'], PASSWORD_DEFAULT);
                }
                return $input;
            
            case 'setGeoGestDefaults':
                $input['fecha_create'] = date('Y-m-d H:i:s');
                $input['estado'] = $input['estado'] ?? 'A';
                return $input;
        
            case 'setUpdateInfo':
                $input['fecha_update'] = date('Y-m-d H:i:s');
                
                // Obtener usuario actual del token JWT
                $headers = getallheaders();
                $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');

         try {
                $payload = Auth::isAuthorized($token);
                $input['usu_update'] = $payload['sub'] ?? null;
            } catch (Exception $e) {
                // Si falla, mantener el valor existente o null
                $input['usu_update'] = $input['usu_update'] ?? null;
            }
            
            return $input;
            
        default:
            return $input;
    }
    }

    private static function getById(string $tabla, int $id, array $config): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM $tabla WHERE id_usuario = ?");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ocultar campos sensibles
        foreach ($config['hidden'] as $campo) {
            unset($record[$campo]);
        }

        return $record;
    }

    private static function handleError(Throwable $e): void {
        $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }

}

CrudController::init();