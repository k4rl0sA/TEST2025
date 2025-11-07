<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once '../libs/gestion.php';

// Cargar PhpSpreadsheet manualmente en el ORDEN CORRECTO
// 1. Clases base y compartidas
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/IComparable.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/StringHelper.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/File.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/Date.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/Drawing.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/Font.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/PasswordHasher.php';

// 2. RichText (requerido por Cell)
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/RichText/ITextElement.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/RichText/TextElement.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/RichText/Run.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/RichText/RichText.php';

// 3. Style (IMPORTANTE: debe ir antes de Cell)
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Supervisor.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Color.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/NumberFormat.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Font.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Alignment.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Border.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Borders.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Fill.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Protection.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Style/Style.php';

// 4. Cell
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/DataType.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/IValueBinder.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/DefaultValueBinder.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/StringValueBinder.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/AdvancedValueBinder.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/Coordinate.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/AddressHelper.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/CellAddress.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/DataValidation.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/Hyperlink.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Cell/Cell.php';

// 5. Collection
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Collection/Cells.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Collection/CellsFactory.php';

// 6. Comment
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Comment.php';

// 7. Worksheet - ORDEN CRÍTICO: Dimension PRIMERO
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Dimension.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/ColumnDimension.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/RowDimension.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/BaseDrawing.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Drawing.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/MemoryDrawing.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/HeaderFooter.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/PageMargins.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/PageSetup.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Protection.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/SheetView.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Worksheet/Worksheet.php';

// 8. Spreadsheet principal
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/HashTable.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/ReferenceHelper.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/DefinedName.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/NamedRange.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/NamedFormula.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Document/Properties.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Document/Security.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php';

// 9. Writer - necesita clases adicionales
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Writer/IWriter.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Writer/BaseWriter.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Shared/XMLWriter.php';
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php';

// 10. IOFactory
require_once '../libs/phpspreadsheet/src/PhpSpreadsheet/IOFactory.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Validar sesión
if (!isset($_SESSION['us_sds'])) {
    die(json_encode(['success' => false, 'message' => 'Sesión no válida']));
}

// Obtener parámetros
$tipo = $_POST['tipo'] ?? '';
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

// Validar parámetros
if (empty($tipo) || empty($fecha_inicio) || empty($fecha_fin)) {
    die(json_encode(['success' => false, 'message' => 'Parámetros incompletos']));
}

// Validar formato de fechas
if (!validateDate($fecha_inicio) || !validateDate($fecha_fin)) {
    die(json_encode(['success' => false, 'message' => 'Formato de fecha inválido']));
}

// Obtener consultas
$queries = getQueries($tipo, $fecha_inicio, $fecha_fin);

if (empty($queries)) {
    die(json_encode(['success' => false, 'message' => 'Tipo de reporte no válido']));
}

try {
    // Crear archivo Excel
    $spreadsheet = new Spreadsheet();
    $spreadsheet->removeSheetByIndex(0);
    
    $sheetIndex = 0;
    
    foreach ($queries as $nombreHoja => $query) {
        // Ejecutar consulta
        $result = datos_mysql($query);
        
        if (!$result || !isset($result['responseResult'])) {
            // Crear hoja vacía si no hay datos
            $sheet = $spreadsheet->createSheet($sheetIndex);
            $nombreHojaLimpio = substr(cleanTx($nombreHoja), 0, 31);
            $sheet->setTitle($nombreHojaLimpio);
            $sheet->setCellValue('A1', 'No hay datos disponibles');
            $sheetIndex++;
            continue;
        }
        
        // Crear nueva hoja
        $sheet = $spreadsheet->createSheet($sheetIndex);
        $nombreHojaLimpio = substr(cleanTx($nombreHoja), 0, 31);
        $sheet->setTitle($nombreHojaLimpio);
        
        $datos = $result['responseResult'];
        
        if (empty($datos)) {
            $sheet->setCellValue('A1', 'No hay datos disponibles');
        } else {
            // Escribir encabezados
            $col = 1;
            foreach (array_keys($datos[0]) as $header) {
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($columnLetter . '1', $header);
                $col++;
            }
            
            // Escribir datos
            $rowNum = 2;
            foreach ($datos as $row) {
                $col = 1;
                foreach ($row as $value) {
                    $columnLetter = Coordinate::stringFromColumnIndex($col);
                    $sheet->setCellValue($columnLetter . $rowNum, $value);
                    $col++;
                }
                $rowNum++;
            }
            
            // Autoajustar columnas
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }
        
        $sheetIndex++;
    }
    
    // Nombres de archivos según tipo
    $nombresArchivos = [
        '1' => 'TH',
        '2' => 'Tamizajes'
    ];
    
    $filename = ($nombresArchivos[$tipo] ?? 'reporte') . '_' . $fecha_inicio . '_a_' . $fecha_fin . '.xlsx';
    
    // Guardar archivo temporal
    $tempDir = sys_get_temp_dir();
    $filePath = $tempDir . DIRECTORY_SEPARATOR . $filename;
    
    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);
    
    // Leer archivo y convertir a base64
    $fileContent = file_get_contents($filePath);
    
    // Eliminar archivo temporal
    unlink($filePath);
    
    // Retornar respuesta exitosa
    echo json_encode([
        'success' => true,
        'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,' . base64_encode($fileContent),
        'filename' => $filename,
        'progreso' => 100,
        'message' => 'Archivo generado correctamente'
    ]);
    
} catch (Exception $e) {
    log_error("Error en descarga Excel: " . $e->getMessage() . " - Line: " . $e->getLine());
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar el archivo: ' . $e->getMessage(),
        'progreso' => 0
    ]);
}

exit;

/**
 * Función para obtener las consultas SQL según el tipo
 */
function getQueries($tipo, $fecha_inicio, $fecha_fin) {
    $sql_subred = "SELECT subred FROM usuarios WHERE id_usuario = '" . $_SESSION['us_sds'] . "'";
    $info_subred = datos_mysql($sql_subred);
    $subred = $info_subred['responseResult'][0]['subred'] ?? 3;
    $queries = [];
    
    switch ($tipo) {
        case '1': // TH
            $queries['TH'] = "SELECT 
                T.id_th AS 'ID',
                T.tipo_doc AS 'Tipo Documento',
                T.n_documento AS 'N° Documento',
                CONCAT(T.nombre1, ' ', T.nombre2, ' ', T.apellido1, ' ', T.apellido2) AS 'Nombres Completos',
                T.fecha_nacimiento AS 'Fecha Nacimiento',
                FN_CATALOGODESC(21, T.sexo) AS 'Sexo',
                T.n_contacto AS 'N° Contacto',
                T.correo AS 'Correo',
                FN_CATALOGODESC(67, T.subred) AS 'Subred',
                T.fecha_create AS 'Fecha Creación',
                CASE T.estado 
                    WHEN 'A' THEN 'ACTIVO'
                    WHEN 'I' THEN 'INACTIVO'
                    ELSE T.estado
                END AS 'Estado'
            FROM th T
            WHERE T.fecha_create >= '$fecha_inicio' 
                AND T.fecha_create <= '$fecha_fin 23:59:59'
                AND T.subred = $subred
            ORDER BY T.fecha_create DESC";
            
            $queries['Contratos'] = "SELECT 
                T.n_documento AS 'N° Documento',
                CONCAT(T.nombre1, ' ', T.apellido1) AS 'Nombre TH',
                TC.n_contrato AS 'N° Contrato',
                FN_CATALOGODESC(326, TC.tipo_cont) AS 'Tipo Contrato',
                TC.fecha_inicio AS 'Fecha Inicio',
                TC.fecha_fin AS 'Fecha Fin',
                CONCAT('$ ', FORMAT(TC.valor_contrato, 0)) AS 'Valor Contrato',
                FN_CATALOGODESC(323, TC.perfil_profesional) AS 'Perfil Profesional',
                FN_CATALOGODESC(308, TC.perfil_contratado) AS 'Perfil Contratado',
                FN_CATALOGODESC(324, TC.rol) AS 'Rol',
                TC.fecha_create AS 'Fecha Creación'
            FROM th_contratos TC
            INNER JOIN th T ON TC.idth = T.id_th
            WHERE TC.fecha_create >= '$fecha_inicio' 
                AND TC.fecha_create <= '$fecha_fin 23:59:59'
                AND T.subred = $subred
            ORDER BY TC.fecha_create DESC";
            
            $queries['Actividades'] = "SELECT 
                T.n_documento AS 'N° Documento',
                CONCAT(T.nombre1, ' ', T.apellido1) AS 'Nombre TH',
                TA.actividad AS 'Código Actividad',
                SUBSTRING(TA.actbien, 1, 100) AS 'Descripción',
                TA.hora_act AS 'Horas Actividad',
                CONCAT('$ ', FORMAT(TA.hora_th, 0)) AS 'Valor Hora',
                TA.per_ano AS 'Año',
                CASE TA.per_mes
                    WHEN 1 THEN 'ENERO' WHEN 2 THEN 'FEBRERO'
                    WHEN 3 THEN 'MARZO' WHEN 4 THEN 'ABRIL'
                    WHEN 5 THEN 'MAYO' WHEN 6 THEN 'JUNIO'
                    WHEN 7 THEN 'JULIO' WHEN 8 THEN 'AGOSTO'
                    WHEN 9 THEN 'SEPTIEMBRE' WHEN 10 THEN 'OCTUBRE'
                    WHEN 11 THEN 'NOVIEMBRE' WHEN 12 THEN 'DICIEMBRE'
                END AS 'Mes',
                TA.can_act AS 'Cantidad',
                TA.total_horas AS 'Total Horas',
                CONCAT('$ ', FORMAT(TA.total_valor, 0)) AS 'Valor Total'
            FROM th_actividades TA
            INNER JOIN th T ON TA.idth = T.id_th
            WHERE TA.fecha_create >= '$fecha_inicio' 
                AND TA.fecha_create <= '$fecha_fin 23:59:59'
                AND T.subred = $subred
            ORDER BY TA.fecha_create DESC";
            break;
            
        case '2': // Tamizajes
            $queries['EPOC'] = "SELECT 
                G.idgeo AS 'Cod_Predio',
                F.id_fam AS 'Cod_Familia',
                A.id_epoc AS 'Cod_Registro',
                FN_CATALOGODESC(67, G.subred) AS 'Subred',
                FN_CATALOGODESC(3, G.zona) AS 'Zona',
                G.localidad AS 'Localidad',
                P.idpeople AS 'Cod_Usuario',
                P.tipo_doc AS 'Tipo_Documento',
                P.idpersona AS 'N°_Documento',
                CONCAT(P.nombre1, ' ', P.nombre2) AS 'Nombres_Usuario',
                CONCAT(P.apellido1, ' ', P.apellido2) AS 'Apellidos_Usuario',
                P.fecha_nacimiento AS 'Fecha_Nacimiento',
                FN_CATALOGODESC(21, P.sexo) AS 'Sexo',
                A.fecha_toma AS 'Fecha_Toma',
                A.puntaje AS 'Puntaje',
                A.descripcion AS 'Clasificacion',
                U.nombre AS 'Usuario_Creo',
                A.fecha_create AS 'Fecha_Creacion'
            FROM hog_tam_epoc A
            LEFT JOIN person P ON A.idpeople = P.idpeople
            LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
            LEFT JOIN hog_geo G ON F.idpre = G.idgeo
            LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
            WHERE A.fecha_toma >= '$fecha_inicio' 
                AND A.fecha_toma <= '$fecha_fin'
                AND G.subred = $subred";
            break;
            
        default:
            return [];
    }
    return $queries;
}
?>