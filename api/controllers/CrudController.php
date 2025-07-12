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

    public static function obtenerUno(string $tabla, int $id): void {
        try {
            $config = self::getTablaConfig($tabla);
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM $tabla WHERE id = ?");
            $stmt->execute([$id]);
            $resultado = $stmt->fetch();

            if (!$resultado) {
                http_response_code(404);
                echo json_encode(['error' => 'Registro no encontrado']);
                return;
            }

            // Ocultar campos sensibles
            foreach ($config['hidden'] ?? [] as $campo) {
                unset($resultado[$campo]);
            }

            echo json_encode($resultado);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Implementar métodos restantes (crear, actualizar, etc.)
}

CrudController::init();