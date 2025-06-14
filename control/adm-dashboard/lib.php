<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar config y funciones necesarias
require_once __DIR__ . '/../../libs/gestion.php';

// Obtener y sanitizar parámetros
$fechadesde = $_POST['fecha_inicio'] ?? '';
$fechahasta = $_POST['fecha_fin'] ?? '';
$subred = $_POST['subred'] ?? '';
$territorio = $_POST['territorio'] ?? '';
$localidad = $_POST['localidad'] ?? '';


function build_where($params, $alias, $campo_fecha = null) {
    $where = [];
    if ($campo_fecha && $params['fechadesde'] && $params['fechahasta']) {
        $where[] = "$alias.$campo_fecha BETWEEN '{$params['fechadesde']}' AND '{$params['fechahasta']}'";
    }
    if ($params['subred'])      $where[] = "subred = '{$params['subred']}'";
    if ($params['territorio'])  $where[] = " territorio = '{$params['territorio']}'";
    if ($params['localidad'])   $where[] = " localidad = '{$params['localidad']}'";
    return $where ? 'WHERE ' . implode(' AND ', $where) : '';
}

$params = [
    'fechadesde' => $fechadesde,
    'fechahasta' => $fechahasta,
    'subred'     => $subred,
    'territorio' => $territorio,
    'localidad'  => $localidad
];

$where_sql = build_where($params, 'hc', 'fecha');

// Validar parámetros si es necesario
$where = [];
if ($fechadesde && $fechahasta) $where[] = "hc.fecha BETWEEN '$fechadesde' AND '$fechahasta'";
if ($subred) $where[] = "hg.subred = '$subred'";
if ($territorio) $where[] = "hg.territorio = '$territorio'";
if ($localidad) $where[] = "hg.localidad = '$localidad'";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Consultar datos 
$sql = "SELECT COUNT(*) AS total_caracterizaciones
FROM hog_carac hc
JOIN hog_fam hf ON hc.idfam = hf.id_fam
JOIN hog_geo hg ON hf.idpre = hg.idgeo
$where_sql;";


$caract = datos_mysql($sql);
$data['sql_debug']=$sql;
if ($caract['code'] !== 0 || empty($caract['responseResult'])) {
    echo json_encode(["error" => "Objeto no encontrado para total_caracterizaciones, por favor valide los filtros"]);
    exit;
}
$caracterizaciones = $caract['responseResult'][0]['total_caracterizaciones'] ?? 0;
if ($caracterizaciones === 0) {
    echo json_encode(["error" => "No se encontraron caracterizaciones en el rango de fechas especificado."]);
    exit;
}

$where_sql_fam = build_where($params, 'hf', 'fecha_create');
$params = [
    'fechadesde' => $fechadesde,
    'fechahasta' => $fechahasta,
    'subred'     => $subred,
    'territorio' => $territorio,
    'localidad'  => $localidad
];

$sql2= "SELECT COUNT(*) AS familia  FROM hog_fam hf LEFT JOIN hog_geo hg ON hf.idpre = hg.idgeo $where_sql_fam;";
$fam = datos_mysql($sql2);
if ($fam['code'] !== 0 || empty($fam['responseResult'])) {
    echo json_encode(["error" => "Objeto no encontrado para familias, por favor valide los filtros"]);
    exit;
}
$familias = $fam['responseResult'][0]['familia'] ?? 0;
if ($familias === 0) {
    echo json_encode(["error" => "No se encontraron familias en el rango de fechas especificado."]);
    exit;
}

// Filtros para hog_ind (individuos)
$where_sql_ind = build_where($params, 'P', 'fecha_create');
$params = [
    'fechadesde' => $fechadesde,
    'fechahasta' => $fechahasta,
    'subred'     => $subred,
    'territorio' => $territorio,
    'localidad'  => $localidad
];
// Consulta para contar individuos
$sql3 = "SELECT COUNT(*) as Individuos FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo $where_sql_ind;";
$ind = datos_mysql($sql3);
if ($ind['code'] !== 0 || empty($ind['responseResult'])) {
    echo json_encode(["error" => "Objeto no encontrado para individuos, por favor valide los filtros"]);
    exit;
}
$individuos = $ind['responseResult'][0]['Individuos'] ?? 0;
if ($individuos === 0) {
    echo json_encode(["error" => "No se encontraron individuos en el rango de fechas especificado."]);
    exit;
}

//Filtros para personas por edad
$where_sql_age = build_where($params, 'P', 'fecha_create');
$params = [
    'fechadesde' => $fechadesde,
    'fechahasta' => $fechahasta,
    'subred'     => $subred,
    'territorio' => $territorio,
    'localidad'  => $localidad
];
// Consulta para distribución por edad  
$sql4="SELECT CASE WHEN TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, P.fecha_create) BETWEEN 0 AND 5 THEN '1. Primera Infancia (0 a 5)' WHEN TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, P.fecha_create) BETWEEN 6 AND 11 THEN '2. Infancia (6 a 11)'  WHEN TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, P.fecha_create) BETWEEN 12 AND 17 THEN '3. Adolescencia (12 a 17)' WHEN TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, P.fecha_create) BETWEEN 18 AND 26 THEN '4 Juventud (18 a 26)' WHEN TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, P.fecha_create) BETWEEN 27 AND 59 THEN '5 Adultez (29 a 59)' ELSE '6 Vejez (60+)' END AS Rango_Edad,COUNT(*) AS Total 
FROM person P
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
$where_sql_age
AND P.fecha_nacimiento IS NOT NULL 
GROUP BY Rango_Edad ORDER BY Rango_Edad;";
$age = datos_mysql($sql4);
if ($age['code'] !== 0 || empty($age['responseResult'])) {
    echo json_encode(["error" => "Objeto no encontrado para distribución por edad, por favor valide los filtros"]);//.var_dump($sql4)
    exit;
}
$age_distribution = [];
foreach ($age['responseResult'] as $row) {
    $age_distribution['labels'][] = $row['Rango_Edad'];
    $age_distribution['values'][] = (int)$row['Total'];
}


//Filtros para personas por sexo
$where_sql_sexo = build_where($params, 'P', 'fecha_create');
$params = [
    'fechadesde' => $fechadesde,
    'fechahasta' => $fechahasta,
    'subred'     => $subred,
    'territorio' => $territorio,
    'localidad'  => $localidad
];
// Consulta para distribución por sexo
$sql5="SELECT FN_CATALOGODESC(21,P.sexo) AS sexos,COUNT(*) AS Total 
FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
$where_sql_sexo AND  P.fecha_nacimiento IS NOT NULL
GROUP BY P.sexo;";
$sexo = datos_mysql($sql5);
if ($sexo['code'] !== 0 || empty($sexo['responseResult'])) {
    echo json_encode(["error" => "Objeto no encontrado para distribución por sexo"]);
    exit;
}
$gender_distribution = [];
foreach ($sexo['responseResult'] as $row) {
    $gender_distribution['labels'][] = $row['sexos'];
    $gender_distribution['values'][] = (int)$row['Total'];
}

// Simulación de datos, reemplaza por tus consultas reales
$data = [
    "totalFamilies" => $caracterizaciones,
     "famCreate"=>$familias,
     "totalPeople" => $individuos,
    "lastUpdate" => "hace 1 hora",
    // Distribución por edad (ejemplo)
    "ageDistribution" => [
        "labels" => $age_distribution['labels'],//["0-5", "6-11", "12-17", "18-29", "30-59", "60+"],
        "values" => $age_distribution['values'] //  [341866, 800000, 900000, 700000, 600000, 659383]
    ],
    // Distribución por género (ejemplo)
    "genderDistribution" => [
        "labels" => $gender_distribution['labels'], //["Femenino", "Masculino", "Otro"], 
        "values" => $gender_distribution['values'] //[1800000, 1750000, 53128]
    ],
    // Consultas por especialidad (ejemplo)
    // "specialtyConsultations" => [
        /* "labels" => ["Acompañamiento Psicosocial", "Duelo", "Cancer", "Cronicos", "Desnutrición", "Gestantes"],
        "values" => [40000, 15000, 12000, 8000, 14456,75000],
        "percentages" => [25, 10, 8, 5, 2.5,20] */
    "Vsp"=>[
        "1"=> [
            "evento" => "Acompañamiento Psicosocial",
            "labels" => ["Total Casos", "Abiertos", "Cerrados"],
            "totales" => [500, 100, 400],
            "abiertos" => [120, 20, 168],
            "cerrados" => [380, 80, 232],
            "porcentaje" => [76]
        ],
        "2"=> [
            "evento" => "Duelo",
            "labels" => ["Total Casos", "Abiertos", "Cerrados"],
            "totales" => [200, 80, 620],
            "abiertos" => [220, 200, 620],
            "cerrados" => [-20, -120, 0],
            "porcentaje" => [44]            
        ],
        "3"=> [
            "evento" => "Cancer",
            "labels" => ["Total Casos", "Abiertos", "Cerrados"],
            "totales" => [300, 150, 150],
            "abiertos" => [100, 50, 50],
            "cerrados" => [200, 100, 100],
            "porcentaje" => [66.67]
        ]
    ],
    // Indicadores de salud
    "healthIndicators" => [
        "vacunacionCompleta" => 87,
        "controlPrenatal" => 92,
        "tamizajeCancer" => 74,
        "hipertensionControlada" => 68
    ],
    // Personas con discapacidad
    "disability" => [
        "total" => 155409,
        "percentage" => 4.37,
        "distribution" => [
            "labels" => ["Visual", "Auditiva", "Motora", "Cognitiva", "Otra"],
            "values" => [40000, 30000, 50000, 20000, 15409]
        ]
    ],
    // Menores de 5 años
    "under5" => [
        "total" => 341866,
        "percentage" => 9.62,
        "breakdown" => [
            ["label" => "0-1 años", "value" => 68373],
            ["label" => "2-3 años", "value" => 136746],
            ["label" => "4-5 años", "value" => 136747]
        ]
    ],
    // Mayores de 60 años
    "elderly" => [
        "total" => 659383,
        "percentage" => 18.55,
        "distribution" => [
            "labels" => ["60-69", "70-79", "80+"],
            "values" => [300000, 250000, 109383]
        ]
    ],
    // Actividad reciente
    "recentActivity" => [
        [
            "type" => "success",
            "title" => "Campaña de vacunación completada",
            "time" => "Hace 2 horas"
        ],
        [
            "type" => "warning",
            "title" => "Alerta: Aumento de casos respiratorios",
            "time" => "Hace 4 horas"
        ],
        [
            "type" => "info",
            "title" => "Nuevo reporte mensual disponible",
            "time" => "Hace 6 horas"
        ]
    ],
    // Agrega la consulta SQL para depuración
    "sql_debug" => $sql
];

echo json_encode($data);