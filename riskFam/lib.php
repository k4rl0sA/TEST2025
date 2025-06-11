<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Cargar config y funciones necesarias
require_once __DIR__ . '/../libs/gestion.php';
// Obtener el documento desde la URL
$document = isset($_GET['document']) ? trim($_GET['document']) : null;
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : null;
if (empty($document) || empty($tipo)) {
    echo json_encode(["error" => "Documento o tipo no proporcionado."]);
    exit;
}
// Consultar datos personales desde la tabla person
$sql = "SELECT 
    idpersona AS document,
    FN_CATALOGODESC(21,sexo) AS sex,
    FN_CATALOGODESC(19,genero) AS gender,
    FN_CATALOGODESC(30,nacionalidad) AS nationality,
    fecha_nacimiento AS birthDate,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS age,
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 0 AND 5 THEN 'PRIMERA INFANCIA'
        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 6 AND 11 THEN 'INFANCIA'
        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 12 AND 17 THEN 'ADOLESCENCIA'
        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 28 THEN 'JUVENTUD'
        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 29 AND 59 THEN 'ADULTEZ'
        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= 60 THEN 'VEJEZ'
        ELSE ''
    END AS lifestage,
    CONCAT_WS('-',G.localidad, FN_CATALOGODESC(2,G.localidad)) AS location,
    G.upz,
    G.direccion AS address,
	NULLIF(TRIM(BOTH ' -' FROM CONCAT_WS(' - ',NULLIF(P.telefono1 COLLATE utf8mb4_unicode_ci, ''),NULLIF(P.telefono2 COLLATE utf8mb4_unicode_ci, ''),NULLIF(F.telefono1 COLLATE utf8mb4_unicode_ci, ''),NULLIF(F.telefono2 COLLATE utf8mb4_unicode_ci, ''))),'') AS phone
FROM person P
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam 
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
WHERE idpersona = '$document' and tipo_doc='$tipo'
        LIMIT 1";
$res = datos_mysql($sql);
if ($res['code'] !== 0 || empty($res['responseResult'])) {
    echo json_encode(["error" => "Documento no encontrado", "document" => $document]);
    exit;
}
// Datos de la persona
$datos = $res['responseResult'][0];

//Riesgo Socioeconómico
$sql1="SELECT 
  G.estrato AS 'Estrato',FN_CATALOGODESC(13,C.ingreso) AS 'Ingreso', ROUND(((CASE G.estrato  WHEN 1 THEN 6 WHEN 2 THEN 5 WHEN 3 THEN 4 WHEN 4 THEN 3 WHEN 5 THEN 2 WHEN 6 THEN 1 ELSE 0 END + CASE C.ingreso  WHEN 1 THEN 3 WHEN 2 THEN 2  WHEN 3 THEN 1 ELSE 0 END ) - 2) * 100 / 7,2) AS SE
FROM `person` P
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN hog_carac C ON F.id_fam = C.idfam
WHERE C.fecha = (SELECT MAX(C2.fecha) FROM hog_carac C2 WHERE C2.idfam = C.idfam)
AND P.idpersona = '$document'  AND P.tipo_doc='$tipo' LIMIT 1";

$res1 = datos_mysql($sql1);
$socioEcono = $res1['responseResult'][0]['SE'];
$estrato= $res1['responseResult'][0]['Estrato'];
$ingreso= $res1['responseResult'][0]['Ingreso'];

//Riesgo Estructura Familiar
$sql2="SELECT A.descripcion,ROUND(((CASE A.descripcion
    WHEN 'DISFUNCIÓN FAMILIAR SEVERA' THEN 4
    WHEN 'DISFUNCIÓN FAMILIAR MODERADA' THEN 3
    WHEN 'DISFUNCIÓN FAMILIAR LEVE' THEN 2
    WHEN 'FUNCIÓN FAMILIAR NORMAL' THEN 1
    ELSE 0
  END - 1) * 100 / 3), 2) AS EF_porcentaje
FROM `person` P
LEFT JOIN hog_tam_apgar A ON P.idpeople = A.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
-- Solo última medición por persona
WHERE A.fecha_toma = (
  SELECT MAX(A2.fecha_toma)
  FROM hog_tam_apgar A2
  WHERE A2.idpeople = A.idpeople
)
AND A.descripcion IS NOT NULL AND P.idpersona = '$document'  AND P.tipo_doc='$tipo' LIMIT 1;";
$res2 = datos_mysql($sql2);
var_dump($res2);
$estruFamil = $res2['responseResult'][0]['EF_porcentaje'];
$apgar = $res2['responseResult'][0]['descripcion'];

//Riesgo Vulnerabilidad Social
$sql3="SELECT 1 FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam";
$res3 = datos_mysql($sql3);
$vulnSocial = $res3['responseResult'][0];

//Riesgo Acceso a Servicios de Salud
$sql4="SELECT 1 FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam";
$res4 = datos_mysql($sql4);
$accesoSalud = $res4['responseResult'][0];

//Riesgo Entorno Habitacional
$sql5="SELECT 1 FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam";
$res5 = datos_mysql($sql5);
$entornoHab = $res5['responseResult'][0];

//Riesgo Características Demográficas
$sql6="SELECT 1 FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam";
$res6 = datos_mysql($sql6);
$caracDemo = $res6['responseResult'][0];

// Generar factores de riesgo aleatorios
$riesgos = [
    "socioeconomic" => [
        "name" => "Nivel Socioeconómico",
        "value" => $socioEcono,
        "weight" => 0.18,
        "estrato" => $estrato,
        "ingreso" => $ingreso,
        "description" => "Impacta directamente el acceso a bienes y servicios esenciales."
    ],
    "familyStructure" => [
        "name" => "Estructura Familiar",
        "value" => $estruFamil,
        "weight" => 0.20,
        // "puntaje" => $puntaje,
        "apgar" => $apgar,
        "description" => "Influye en el apoyo social, la funcionalidad y la estabilidad del hogar."
    ],
    "socialVulnerability" => [
        "name" => "Vulnerabilidad Social",
        "value" => rand(0, 100),
        "weight" => 0.12,
        "description" => "Considera factores como la violencia, el desplazamiento y la exclusión social."
    ],
    "accessToHealth" => [
        "name" => "Acceso a Servicios de Salud",
        "value" => rand(0, 100),
        "weight" => 0.10,
        "description" => "Clave para la prevención y el cuidado de enfermedades."
    ],
    "livingEnvironment" => [
        "name" => "Entorno Habitacional",
        "value" => rand(0, 100),
        "weight" => 0.10,
        "description" => "Evalúa las condiciones de la vivienda y su impacto en la salud."
    ],
    "demographics" => [
        "name" => "Características Demográficas",
        "value" => rand(0, 100),
        "weight" => 0.30,
        "description" => "Incluye edad, género y otras variables que influyen en la exposición al riesgo."
    ]
];
echo json_encode(array_merge($datos, ["riskFactors" => $riesgos]));