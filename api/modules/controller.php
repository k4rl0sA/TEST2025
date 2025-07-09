<?php
// File: api/modules/controller.php
declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
defined('API_DIR') || exit(header('HTTP/1.1 403 Forbidden'));
require_once __DIR__ . '/../config.php';

function cambiarEstado(string $tabla, int $id, string $estado): array {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("UPDATE `$tabla` SET estado = :estado WHERE id = :id");
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        return ['success' => true, 'mensaje' => "Estado actualizado correctamente en $tabla."];
    }
    return ['success' => false, 'error' => 'No se pudo actualizar el estado'];
}

function crearRegistro(string $tabla, array $data): array {
    $pdo = Database::getConnection();
    $campos = array_keys($data);
    $sql = "INSERT INTO `$tabla` (" . implode(',', $campos) . ") VALUES (" . implode(',', array_map(fn($c) => ":$c", $campos)) . ")";
    $stmt = $pdo->prepare($sql);
    foreach ($data as $campo => $valor) {
        $stmt->bindValue(":$campo", $valor);
    }
    if ($stmt->execute()) {
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    }
    return ['success' => false, 'error' => 'Error al insertar el registro'];
}

function actualizarRegistro(string $tabla, int $id, array $data): array {
    $pdo = Database::getConnection();
    $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
    $sql = "UPDATE `$tabla` SET $set WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    foreach ($data as $campo => $valor) {
        $stmt->bindValue(":$campo", $valor);
    }
    $stmt->bindValue(':id', $id);
    if ($stmt->execute()) {
        return ['success' => true];
    }
    return ['success' => false, 'error' => 'Error al actualizar el registro'];
}

function listarRegistros(string $tabla, int $pagina = 1, int $limite = 10, array $filtros = [], string $ordenCampo = 'id', string $ordenDir = 'ASC'): array {
    $pdo = Database::getConnection();
    $offset = ($pagina - 1) * $limite;
    $where = [];
    $params = [];

    foreach ($filtros as $filtro) {
        if (count($filtro) !== 3) continue;
        [$campo, $operador, $valor] = $filtro;
        $paramKey = ":{$campo}_{$operador}";

        switch (strtoupper($operador)) {
            case 'LIKE':
                $where[] = "$campo LIKE $paramKey";
                $params[$paramKey] = "%$valor%";
                break;
            case '=':
            case '!=':
            case '<':
            case '>':
            case '<=':
            case '>=':
                $where[] = "$campo $operador $paramKey";
                $params[$paramKey] = $valor;
                break;
            case 'BETWEEN':
                if (is_array($valor) && count($valor) === 2) {
                    $where[] = "$campo BETWEEN :{$campo}_start AND :{$campo}_end";
                    $params[":{$campo}_start"] = $valor[0];
                    $params[":{$campo}_end"] = $valor[1];
                }
                break;
        }
    }

    $sql = "SELECT * FROM `$tabla`";
    $countSql = "SELECT COUNT(*) FROM `$tabla`";

    if (!empty($where)) {
        $whereSql = " WHERE " . implode(" AND ", $where);
        $sql .= $whereSql;
        $countSql .= $whereSql;
    }

    $sql .= " ORDER BY `$ordenCampo` $ordenDir LIMIT :offset, :limite";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    $stmt->execute();
    $datos = $stmt->fetchAll();

    $total = (int)$pdo->query($countSql)->fetchColumn();

    return [
        'success' => true,
        'data' => $datos,
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite,
        'paginas' => ceil($total / $limite)
    ];
}
