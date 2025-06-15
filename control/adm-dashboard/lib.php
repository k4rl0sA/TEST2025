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
$where_sql AND hc.estado='A';";


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

$sql2= "SELECT COUNT(*) AS familia  FROM hog_fam hf LEFT JOIN hog_geo hg ON hf.idpre = hg.idgeo $where_sql_fam AND hf.estado='A';";
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
$sql3 = "SELECT COUNT(*) as Individuos FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo $where_sql_ind AND P.estado='A';";
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
AND P.estado='A' AND P.fecha_nacimiento IS NOT NULL 
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
$where_sql_sexo AND P.estado='A' AND  P.fecha_nacimiento IS NOT NULL
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

// Filtros para VSP
$where_sql_vsp = build_where($params,'A','fecha_seg')
$params = [
    'fechadesde' => $fechadesde,
    'fechahasta' => $fechahasta,
    'subred'     => $subred,
    'territorio' => $territorio,
    'localidad'  => $localidad
];
/* $sql6="SELECT A.evento id,FN_CATALOGODESC(87, A.evento) AS evento,COUNT(A.evento) AS total_casos,SUM(CASE WHEN A.estado_s = 1 THEN 1 ELSE 0 END) AS abiertos,SUM(CASE WHEN A.cierre_caso = 1 THEN 1 ELSE 0 END) AS cerrados,(SUM(CASE WHEN A.cierre_caso = 1 THEN 1 ELSE 0 END) * 100.0 / NULLIF(SUM(CASE WHEN A.estado_s = 1 THEN 1 ELSE 0 END), 0)) AS vspPercen
FROM `vsp_acompsic` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
$where_sql_vsp AND A.estado='A' GROUP BY FN_CATALOGODESC(87, A.evento);"; */
$sql6="SELECT subred, localidad, territorio, fecha_seg, id AS evento_id, evento AS evento_descripcion,COUNT(cierre_caso) AS total_casos, 
SUM(CASE WHEN estado_s = 1 THEN 1 ELSE 0 END) AS abiertos, SUM(CASE WHEN cierre_caso = 1 THEN 1 ELSE 0 END) AS cerrados, 
CAST(SUM(CASE WHEN cierre_caso = 1 THEN 1 ELSE 0 END) AS DECIMAL(10,2)) * 100.0 / NULLIF(SUM(CASE WHEN estado_s = 1 THEN 1 ELSE 0 END), 0) AS vspPercen 
FROM (
SELECT G.subred, G.localidad, G.territorio, A.fecha_seg, A.evento AS id, FN_CATALOGODESC(87, A.evento) AS evento, A.estado_s, A.cierre_caso 
FROM `vsp_apopsicduel` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, B.fecha_seg, B.evento AS id, FN_CATALOGODESC(87, B.evento) AS evento, B.estado_s, B.cierre_caso 
FROM `vsp_violreite` B LEFT JOIN person P ON B.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, C.fecha_seg, C.evento AS id, FN_CATALOGODESC(87, C.evento) AS evento, C.estado_s, C.cierre_caso 
FROM `vsp_bpnpret` C LEFT JOIN person P ON C.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, D.fecha_seg, D.evento AS id, FN_CATALOGODESC(87, D.evento) AS evento, D.estado_s, D.cierre_caso 
FROM `vsp_bpnpret` D LEFT JOIN person P ON D.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, E.fecha_seg, E.evento AS id, FN_CATALOGODESC(87, E.evento) AS evento, E.estado_s, E.cierre_caso 
FROM `vsp_bpnpret` E LEFT JOIN person P ON E.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, Y.fecha_seg, Y.evento AS id, FN_CATALOGODESC(87, Y.evento) AS evento, Y.estado_s, Y.cierre_caso 
FROM `vsp_bpnpret` Y LEFT JOIN person P ON Y.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, H.fecha_seg, H.evento AS id, FN_CATALOGODESC(87, H.evento) AS evento, H.estado_s, H.cierre_caso 
FROM `vsp_bpnpret` H LEFT JOIN person P ON H.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, I.fecha_seg, I.evento AS id, FN_CATALOGODESC(87, I.evento) AS evento, I.estado_s, I.cierre_caso 
FROM `vsp_bpnpret` I LEFT JOIN person P ON I.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, J.fecha_seg, J.evento AS id, FN_CATALOGODESC(87, J.evento) AS evento, J.estado_s, J.cierre_caso 
FROM `vsp_bpnpret` J LEFT JOIN person P ON J.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, K.fecha_seg, K.evento AS id, FN_CATALOGODESC(87, K.evento) AS evento, K.estado_s, K.cierre_caso 
FROM `vsp_bpnpret` K LEFT JOIN person P ON K.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, L.fecha_seg, L.evento AS id, FN_CATALOGODESC(87, L.evento) AS evento, L.estado_s, L.cierre_caso 
FROM `vsp_bpnpret` L LEFT JOIN person P ON L.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, M.fecha_seg, M.evento AS id, FN_CATALOGODESC(87, M.evento) AS evento, M.estado_s, M.cierre_caso 
FROM `vsp_bpnpret` M LEFT JOIN person P ON M.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, N.fecha_seg, N.evento AS id, FN_CATALOGODESC(87, N.evento) AS evento, N.estado_s, N.cierre_caso 
FROM `vsp_bpnpret` N LEFT JOIN person P ON N.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, O.fecha_seg, O.evento AS id, FN_CATALOGODESC(87, O.evento) AS evento, O.estado_s, O.cierre_caso 
FROM `vsp_bpnpret` O LEFT JOIN person P ON O.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, Q.fecha_seg, Q.evento AS id, FN_CATALOGODESC(87, Q.evento) AS evento, Q.estado_s, Q.cierre_caso 
FROM `vsp_bpnpret` Q LEFT JOIN person P ON Q.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, R.fecha_seg, R.evento AS id, FN_CATALOGODESC(87, R.evento) AS evento, R.estado_s, R.cierre_caso 
FROM `vsp_bpnpret` R LEFT JOIN person P ON R.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, S.fecha_seg, S.evento AS id, FN_CATALOGODESC(87, S.evento) AS evento, S.estado_s, S.cierre_caso 
FROM `vsp_bpnpret` S LEFT JOIN person P ON S.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, T.fecha_seg, T.evento AS id, FN_CATALOGODESC(87, T.evento) AS evento, T.estado_s, T.cierre_caso 
FROM `vsp_bpnpret` T LEFT JOIN person P ON T.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, U.fecha_seg, U.evento AS id, FN_CATALOGODESC(87, U.evento) AS evento, U.estado_s, U.cierre_caso 
FROM `vsp_bpnpret` U LEFT JOIN person P ON U.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, V.fecha_seg, V.evento AS id, FN_CATALOGODESC(87, V.evento) AS evento, V.estado_s, V.cierre_caso 
FROM `vsp_bpnpret` V LEFT JOIN person P ON V.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, W.fecha_seg, W.evento AS id, FN_CATALOGODESC(87, W.evento) AS evento, W.estado_s, W.cierre_caso 
FROM `vsp_bpnpret` W LEFT JOIN person P ON W.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
UNION ALL SELECT G.subred, G.localidad, G.territorio, X.fecha_seg, X.evento AS id, FN_CATALOGODESC(87, X.evento) AS evento, X.estado_s, X.cierre_caso 
FROM `vsp_bpnpret` X LEFT JOIN person P ON X.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo  
) AS combined_data 
$where_sql_vsp AND A.estado='A' GROUP BY subred, localidad, territorio, fecha_seg, id, evento;";
$vsp = datos_mysql($sql6);
if ($vsp['code'] !== 0 || empty($vsp['responseResult'])) {
    echo json_encode(["error" => "Objeto no encontrado para VSP, por favor valide los filtros"]);
    exit;
}
$vsp_data = [];
foreach ($vsp['responseResult'] as $row) {
    $vsp_data[$row['id']] = [
        "evento" => $row['evento'],
        "labels" => ["Total Casos", "Abiertos", "Cerrados"],
        "totales" => [$row['total_casos'], $row['abiertos'], $row['cerrados']],
        "abiertos" => [$row['abiertos']],
        "cerrados" => [$row['cerrados']],
        "vspPercen" => [$row['vspPercen']]
    ];
}
// Agregar datos de VSP al array principal
$data['Vsp'] = $vsp_data;


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
    /* "specialtyConsultations" => [
        "vspPercentage"=> 25,
        "labels" => ["Total Casos", "Abiertos", "Cerrados"],
        "totales" => [500, 100, 400],
        "abiertos" => [120, 20, 168],
        "cerrados" => [380, 80, 232],
        ],  */
        "Vsp"=>[
            $data['Vsp'] ?? []
        ],
        ]
     /* "Vsp"=>[
        "1"=> [
            "evento" => "Acompañamiento Psicosocial",
            "labels" => ["Total Casos", "Abiertos", "Cerrados"],
            "totales" => [500, 100, 400],
            "abiertos" => [120, 20, 168],
            "cerrados" => [380, 80, 232],
            "vspPercen" => [76]
        ],
        "2"=> [
            "evento" => "Duelo",
            "labels" => ["Total Casos", "Abiertos", "Cerrados"],
            "totales" => [200, 80, 620],
            "abiertos" => [220, 200, 620],
            "cerrados" => [-20, -120, 0],
            "vspPercen" => [44]            
        ],
        "20"=> [
            "evento" => "Cancer",
            "labels" => ["Total Casos", "Abiertos", "Cerrados"],
            "totales" => [300, 150, 150],
            "abiertos" => [100, 50, 50],
            "cerrados" => [200, 100, 100],
            "vspPercen" => [66.67]
        ]
    ], */
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