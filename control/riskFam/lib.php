<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Cargar config y funciones necesarias
require_once __DIR__ . '/../../libs/gestion.php';
// Obtener el documento desde la URL
$document = isset($_GET['document']) ? trim($_GET['document']) : null;
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : null;
if (empty($document) || empty($tipo)) {
    echo json_encode(["error" => "Documento o tipo no proporcionado."]);
    exit;
}
// Consultar datos personales desde la tabla person
$sql = "SELECT idpersona AS document,FN_CATALOGODESC(21,sexo) AS sex,FN_CATALOGODESC(19,genero) AS gender,  FN_CATALOGODESC(30,nacionalidad) AS nationality,fecha_nacimiento AS birthDate,TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS age, CASE WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 0 AND 5 THEN 'PRIMERA INFANCIA'  WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 6 AND 11 THEN 'INFANCIA'        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 12 AND 17 THEN 'ADOLESCENCIA'  WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 28 THEN 'JUVENTUD' WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 29 AND 59 THEN 'ADULTEZ' WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= 60 THEN 'VEJEZ' ELSE '' END AS lifestage, CONCAT_WS('-',G.localidad, FN_CATALOGODESC(2,G.localidad)) AS location,  G.upz,  G.direccion AS address,	NULLIF(TRIM(BOTH ' -' FROM CONCAT_WS(' - ',NULLIF(P.telefono1 COLLATE utf8mb4_unicode_ci, ''),NULLIF(P.telefono2 COLLATE utf8mb4_unicode_ci, ''),NULLIF(F.telefono1 COLLATE utf8mb4_unicode_ci, ''),NULLIF(F.telefono2 COLLATE utf8mb4_unicode_ci, ''))),'') AS phone 
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
$sql1="SELECT G.estrato AS 'Estrato',FN_CATALOGODESC(13,C.ingreso) AS 'Ingreso', ROUND(((CASE G.estrato  WHEN 1 THEN 6 WHEN 2 THEN 5 WHEN 3 THEN 4 WHEN 4 THEN 3 WHEN 5 THEN 2 WHEN 6 THEN 1 ELSE 0 END + CASE C.ingreso  WHEN 1 THEN 3 WHEN 2 THEN 2  WHEN 3 THEN 1 ELSE 0 END ) - 2) * 100 / 7,2) AS SE
FROM `person` P 
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN hog_carac C ON F.id_fam = C.idfam
WHERE C.fecha = (SELECT MAX(C2.fecha) FROM hog_carac C2 WHERE C2.idfam = C.idfam) 
AND P.idpersona = '$document'  AND P.tipo_doc='$tipo' LIMIT 1";

$res1 = datos_mysql($sql1);
if ($res1['code'] !== 0 || empty($res1['responseResult'])) {
    echo json_encode(["error" => "Datos socioeconómicos no encontrados", "document" => $document]);
    exit;
}
$socioEcono = $res1['responseResult'][0]['SE'];
$estrato= $res1['responseResult'][0]['Estrato'];
$ingreso= $res1['responseResult'][0]['Ingreso'];

//Riesgo Estructura Familiar
$sql2="SELECT A.descripcion AS 'Descripcion',ROUND(((CASE A.descripcion WHEN 'DISFUNCIÓN FAMILIAR SEVERA' THEN 4 WHEN 'DISFUNCIÓN FAMILIAR MODERADA' THEN 3  WHEN 'DISFUNCIÓN FAMILIAR LEVE' THEN 2  WHEN 'FUNCIÓN FAMILIAR NORMAL' THEN 1 ELSE 0 END - 1) * 100 / 3), 2) AS EF_porcentaje
FROM `person` P 
LEFT JOIN hog_tam_apgar A ON P.idpeople = A.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
WHERE A.fecha_toma = (SELECT MAX(A2.fecha_toma) FROM hog_tam_apgar A2  WHERE A2.idpeople = A.idpeople) AND A.descripcion IS NOT NULL 
AND P.idpersona = '$document'  AND P.tipo_doc='$tipo' LIMIT 1;";
$res2 = datos_mysql($sql2);
// var_dump($res2);
if ($res2['code'] !== 0 || empty($res2['responseResult'])) {
    echo json_encode(["error" => "Datos de estructura familiar no encontrados", "document" => $document]);
    exit;
}
$estruFamil = $res2['responseResult'][0]['EF_porcentaje'];
$apgar = $res2['responseResult'][0]['Descripcion'];

//Riesgo Vulnerabilidad Social
$sql3="SELECT P.idpeople,  -- Puntaje por seguridad alimentaria
    (
        (CASE WHEN C.seg_pre1 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre2 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre3 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre4 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre5 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre6 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre7 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre8 = 'SI' THEN 5 ELSE 0 END)
    ) AS puntaje_seguridad_alimentaria,
    -- Puntaje por población diferencial
    CASE P.pobladifer
        WHEN 1 THEN 5
        WHEN 2 THEN 5
        WHEN 3 THEN 5
        WHEN 4 THEN 4
        WHEN 5 THEN 5
        WHEN 10 THEN 3
        WHEN 11 THEN 4
        WHEN 13 THEN 5
        ELSE 0
    END AS puntaje_pobladifer,
    -- Puntaje por inclusión por oficio
    CASE P.incluofici
        WHEN 1 THEN 5
        WHEN 3 THEN 4
        WHEN 4 THEN 3
        WHEN 5 THEN 5
        WHEN 6 THEN 3
        WHEN 7 THEN 4
        WHEN 8 THEN 5
        ELSE 0
    END AS puntaje_incluofici,
    -- Total de los tres factores
    (
        (CASE WHEN C.seg_pre1 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre2 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre3 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre4 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre5 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre6 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre7 = 'SI' THEN 5 ELSE 0 END) +
        (CASE WHEN C.seg_pre8 = 'SI' THEN 5 ELSE 0 END)
    ) +
    CASE P.pobladifer
        WHEN 1 THEN 5
        WHEN 2 THEN 5
        WHEN 3 THEN 5
        WHEN 4 THEN 4
        WHEN 5 THEN 5
        WHEN 10 THEN 3
        WHEN 11 THEN 4
        WHEN 13 THEN 5
        ELSE 0
    END +
    CASE P.incluofici
        WHEN 1 THEN 5
        WHEN 3 THEN 4
        WHEN 4 THEN 3
        WHEN 5 THEN 5
        WHEN 6 THEN 3
        WHEN 7 THEN 4
        WHEN 8 THEN 5
        ELSE 0
    END AS puntaje_total_vulnerabilidad_social,
    -- Porcentaje del total sobre 50
    ROUND((
        (
            (CASE WHEN C.seg_pre1 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre2 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre3 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre4 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre5 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre6 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre7 = 'SI' THEN 5 ELSE 0 END) +
            (CASE WHEN C.seg_pre8 = 'SI' THEN 5 ELSE 0 END)
        ) +
        CASE P.pobladifer
            WHEN 1 THEN 5
            WHEN 2 THEN 5
            WHEN 3 THEN 5
            WHEN 4 THEN 4
            WHEN 5 THEN 5
            WHEN 10 THEN 3
            WHEN 11 THEN 4
            WHEN 13 THEN 5
            ELSE 0
        END +
        CASE P.incluofici
            WHEN 1 THEN 5
            WHEN 3 THEN 4
            WHEN 4 THEN 3
            WHEN 5 THEN 5
            WHEN 6 THEN 3
            WHEN 7 THEN 4
            WHEN 8 THEN 5
            ELSE 0
        END
    ) * 100 / 50, 2) AS vulnerabilidad_social_porcentaje
FROM person P
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_carac C ON F.id_fam = C.idfam
WHERE C.fecha = (
    SELECT MAX(C2.fecha)
    FROM hog_carac C2
    WHERE C2.idfam = C.idfam
) AND P.idpersona = '$document' AND P.tipo_doc = '$tipo' LIMIT 1";
$res3 = datos_mysql($sql3);
$vulnSocial = $res3['responseResult'][0]['vulnerabilidad_social_porcentaje'];
$puntajeTotal = $res3['responseResult'][0]['puntaje_total_vulnerabilidad_social'];

//Riesgo Acceso a Servicios de Salud
$sql4="SELECT 
    P.idpeople,
    -- Puntaje según el régimen
    CASE P.regimen
        WHEN 1 THEN 4
        WHEN 2 THEN 2
        WHEN 3 THEN 1
        WHEN 4 THEN 5
        WHEN 5 THEN 6
        ELSE 0
    END AS puntaje_regimen_salud,
    -- Porcentaje sobre 6 puntos
    ROUND(
        CASE P.regimen
            WHEN 1 THEN 4
            WHEN 2 THEN 2
            WHEN 3 THEN 1
            WHEN 4 THEN 5
            WHEN 5 THEN 6
            ELSE 0
        END * 100 / 6, 2
    ) AS acceso_salud_porcentaje,
    -- Ponderación al 10%
    ROUND(
        CASE P.regimen
            WHEN 1 THEN 4
            WHEN 2 THEN 2
            WHEN 3 THEN 1
            WHEN 4 THEN 5
            WHEN 5 THEN 6
            ELSE 0
        END * 10 / 6, 8
    ) AS acceso_salud_ponderado
FROM person P
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN hog_carac C ON F.id_fam = C.idfam
WHERE C.fecha = (
    SELECT MAX(C2.fecha)
    FROM hog_carac C2
    WHERE C2.idfam = C.idfam
) AND P.idpersona = '$document' AND P.tipo_doc = '$tipo' LIMIT 1";
$res4 = datos_mysql($sql4);

if ($res4['code'] !== 0 || empty($res4['responseResult'])) {
    echo json_encode(["error" => "Datos de acceso a servicios de salud no encontrados", "document" => $document]);
    exit;
}
$accesoSaludPorcentaje = $res4['responseResult'][0]['acceso_salud_porcentaje'];
$puntajeRegimenSalud = $res4['responseResult'][0]['puntaje_regimen_salud'];

//Riesgo Entorno Habitacional
$sql5="SELECT  P.idpersona,P.tipo_doc,FN_CATALOGODESC(3,G.zona) AS Zona,-- Zona y su puntaje
  CASE WHEN G.zona = 1 THEN 1 WHEN G.zona = 2 THEN 2 ELSE NULL END AS Puntaje_Zona, FN_CATALOGODESC(4,C.tipo_vivienda) AS 'Tipo de Vivienda',  CASE C.tipo_vivienda   WHEN 1 THEN 1 WHEN 2 THEN 1 WHEN 3 THEN 2  WHEN 4 THEN 3 WHEN 5 THEN 4 WHEN 6 THEN 4 ELSE NULL END AS Puntaje_Tipo_Vivienda,-- Tipo de vivienda y puntaje
  FN_CATALOGODESC(8,C.tenencia) AS 'Tenencia de la Vivienda',  CASE C.tenencia    WHEN 1 THEN 1 WHEN 2 THEN 2 WHEN 3 THEN 3   WHEN 4 THEN 4 WHEN 5 THEN 5 ELSE NULL END AS Puntaje_Tenencia,  -- Tenencia de vivienda y puntaje
  C.actividad_economica AS 'Uso para actividad económicas',  CASE WHEN LOWER(C.actividad_economica) = 'si' THEN 2  WHEN LOWER(C.actividad_economica) = 'no' THEN 1 ELSE NULL END AS Puntaje_Actividad_Economica, -- Actividad económica 
  C.energia AS 'Energía Eléctrica', CASE WHEN LOWER(C.energia) = 'si' THEN 1 WHEN LOWER(C.energia) = 'no' THEN 3 ELSE NULL END AS Puntaje_Energia, C.gas AS 'Gas natural de red pública',  CASE WHEN LOWER(C.gas) = 'si' THEN 1  WHEN LOWER(C.gas) = 'no' THEN 3 ELSE NULL END AS Puntaje_Gas, C.acueducto AS 'Acueducto',  CASE WHEN LOWER(C.acueducto) = 'si' THEN 1  WHEN LOWER(C.acueducto) = 'no' THEN 3 ELSE NULL END AS Puntaje_Acueducto,  C.alcantarillado AS 'Alcantarillado', CASE WHEN LOWER(C.alcantarillado) = 'si' THEN 1  WHEN LOWER(C.alcantarillado) = 'no' THEN 3 ELSE NULL END AS Puntaje_Alcantarillado, C.basuras AS 'Recolección de basuras', -- Servicios públicos (sí = 1, no = 3)
  CASE WHEN LOWER(C.basuras) = 'si' THEN 1 WHEN LOWER(C.basuras) = 'no' THEN 3 ELSE NULL END AS Puntaje_Basuras, -- Fuentes de agua (sí = 2, no = 1)
  C.pozo AS 'Pozo', CASE WHEN LOWER(C.pozo) = 'si' THEN 2 WHEN LOWER(C.pozo) = 'no' THEN 1 ELSE NULL END AS Puntaje_Pozo,  C.aljibe AS 'Aljibe', CASE WHEN LOWER(C.aljibe) = 'si' THEN 2     WHEN LOWER(C.aljibe) = 'no' THEN 1 ELSE NULL END AS Puntaje_Aljibe,  -- Factores ambientales (sí = 3, no = 1) excepto facamb3
  C.facamb1 AS 'Tráfico pesado cercano', CASE WHEN LOWER(C.facamb1) = 'si' THEN 3 WHEN LOWER(C.facamb1) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb1, C.facamb2 AS 'Vías sin pavimentar o en construcción cercanas', CASE WHEN LOWER(C.facamb2) = 'si' THEN 3  WHEN LOWER(C.facamb2) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb2, C.facamb3 AS 'Cercanía a zonas verdes y recreativas', CASE WHEN LOWER(C.facamb3) = 'si' THEN 1  -- INVERSO
  WHEN LOWER(C.facamb3) = 'no' THEN 3 ELSE NULL END AS Puntaje_facamb3,C.facamb4 AS 'Cercanía a fuentes contaminantes',CASE WHEN LOWER(C.facamb4) = 'si' THEN 3 WHEN LOWER(C.facamb4) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb4, C.facamb5 AS 'Conserva alimentos adecuadamente',CASE WHEN LOWER(C.facamb5) = 'si' THEN 3  WHEN LOWER(C.facamb5) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb5, C.facamb6 AS 'Manipula correctamente el agua', CASE WHEN LOWER(C.facamb6) = 'si' THEN 3  WHEN LOWER(C.facamb6) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb6, C.facamb7 AS 'Adquiere medicamentos con fórmula médica', CASE WHEN LOWER(C.facamb7) = 'si' THEN 3 WHEN LOWER(C.facamb7) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb7, C.facamb8 AS 'Almacena químicos de forma segura', CASE WHEN LOWER(C.facamb8) = 'si' THEN 3 WHEN LOWER(C.facamb8) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb8, C.facamb9 AS 'Manejo adecuado de residuos sólidos', CASE WHEN LOWER(C.facamb9) = 'si' THEN 3 WHEN LOWER(C.facamb9) = 'no' THEN 1 ELSE NULL END AS Puntaje_facamb9, -- Puntaje total bruto
  (CASE WHEN G.zona = 1 THEN 1 WHEN G.zona = 2 THEN 2 ELSE 0 END + CASE C.tipo_vivienda WHEN 1 THEN 1 WHEN 2 THEN 1 WHEN 3 THEN 2 WHEN 4 THEN 3 WHEN 5 THEN 4 WHEN 6 THEN 4 ELSE 0 END + CASE C.tenencia WHEN 1 THEN 1 WHEN 2 THEN 2 WHEN 3 THEN 3 WHEN 4 THEN 4 WHEN 5 THEN 5 ELSE 0 END + CASE WHEN LOWER(C.actividad_economica) = 'si' THEN 2 WHEN LOWER(C.actividad_economica) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.energia) = 'si' THEN 1 WHEN LOWER(C.energia) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.gas) = 'si' THEN 1 WHEN LOWER(C.gas) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.acueducto) = 'si' THEN 1 WHEN LOWER(C.acueducto) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.alcantarillado) = 'si' THEN 1 WHEN LOWER(C.alcantarillado) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.basuras) = 'si' THEN 1 WHEN LOWER(C.basuras) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.pozo) = 'si' THEN 2 WHEN LOWER(C.pozo) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.aljibe) = 'si' THEN 2 WHEN LOWER(C.aljibe) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb1) = 'si' THEN 3 WHEN LOWER(C.facamb1) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb2) = 'si' THEN 3 WHEN LOWER(C.facamb2) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb3) = 'si' THEN 1 WHEN LOWER(C.facamb3) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.facamb4) = 'si' THEN 3 WHEN LOWER(C.facamb4) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb5) = 'si' THEN 3 WHEN LOWER(C.facamb5) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb6) = 'si' THEN 3 WHEN LOWER(C.facamb6) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb7) = 'si' THEN 3 WHEN LOWER(C.facamb7) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb8) = 'si' THEN 3 WHEN LOWER(C.facamb8) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb9) = 'si' THEN 3 WHEN LOWER(C.facamb9) = 'no' THEN 1 ELSE 0 END ) AS Puntaje_EH_Bruto, -- Puntaje escalado 0 a 100
  ROUND(((CASE WHEN G.zona = 1 THEN 1 WHEN G.zona = 2 THEN 2 ELSE 0 END + CASE C.tipo_vivienda WHEN 1 THEN 1 WHEN 2 THEN 1 WHEN 3 THEN 2 WHEN 4 THEN 3 WHEN 5 THEN 4 WHEN 6 THEN 4 ELSE 0 END + CASE C.tenencia WHEN 1 THEN 1 WHEN 2 THEN 2 WHEN 3 THEN 3 WHEN 4 THEN 4 WHEN 5 THEN 5 ELSE 0 END + CASE WHEN LOWER(C.actividad_economica) = 'si' THEN 2 WHEN LOWER(C.actividad_economica) = 'no' THEN 1 ELSE 0 END  + CASE WHEN LOWER(C.energia) = 'si' THEN 1 WHEN LOWER(C.energia) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.gas) = 'si' THEN 1 WHEN LOWER(C.gas) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.acueducto) = 'si' THEN 1 WHEN LOWER(C.acueducto) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.alcantarillado) = 'si' THEN 1 WHEN LOWER(C.alcantarillado) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.basuras) = 'si' THEN 1 WHEN LOWER(C.basuras) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.pozo) = 'si' THEN 2 WHEN LOWER(C.pozo) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.aljibe) = 'si' THEN 2 WHEN LOWER(C.aljibe) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb1) = 'si' THEN 3 WHEN LOWER(C.facamb1) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb2) = 'si' THEN 3 WHEN LOWER(C.facamb2) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb3) = 'si' THEN 1 WHEN LOWER(C.facamb3) = 'no' THEN 3 ELSE 0 END + CASE WHEN LOWER(C.facamb4) = 'si' THEN 3 WHEN LOWER(C.facamb4) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb5) = 'si' THEN 3 WHEN LOWER(C.facamb5) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb6) = 'si' THEN 3 WHEN LOWER(C.facamb6) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb7) = 'si' THEN 3 WHEN LOWER(C.facamb7) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb8) = 'si' THEN 3 WHEN LOWER(C.facamb8) = 'no' THEN 1 ELSE 0 END + CASE WHEN LOWER(C.facamb9) = 'si' THEN 3 WHEN LOWER(C.facamb9) = 'no' THEN 1 ELSE 0 END) - 13) * 100.0 / (35 - 13), 2) AS EH_Valor_0_100 
 FROM person P
 LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
 LEFT JOIN hog_geo G ON F.idpre = G.idgeo
 LEFT JOIN (SELECT hc.* FROM hog_carac hc  INNER JOIN (SELECT idfam, MAX(fecha) AS max_fecha FROM hog_carac  GROUP BY idfam) ult ON hc.idfam = ult.idfam AND hc.fecha = ult.max_fecha) C ON P.vivipersona = C.idfam;";
$res5 = datos_mysql($sql5);
$puntajeEHBruto = $res5['responseResult'][0]['Puntaje_EH_Bruto'];
$zona= $res5['responseResult'][0]['Zona'];
$tipoVivienda = $res5['responseResult'][0]['Tipo de Vivienda'];
$tenencia = $res5['responseResult'][0]['Tenencia de la Vivienda'];
$actividadEconomica = $res5['responseResult'][0]['Uso para actividad económicas'];
$energia = $res5['responseResult'][0]['Energía Eléctrica'];
$gas = $res5['responseResult'][0]['Gas natural de red pública'];
$acueducto = $res5['responseResult'][0]['Acueducto'];
$alcantarillado = $res5['responseResult'][0]['Alcantarillado'];
$basuras = $res5['responseResult'][0]['Recolección de basuras'];
$pozo = $res5['responseResult'][0]['Pozo'];
$aljibe = $res5['responseResult'][0]['Aljibe'];
$facamb1 = $res5['responseResult'][0]['Tráfico pesado cercano'];
$facamb2 = $res5['responseResult'][0]['Vías sin pavimentar o en construcción cercanas'];
$facamb3 = $res5['responseResult'][0]['Cercanía a zonas verdes y recreativas'];
$facamb4 = $res5['responseResult'][0]['Cercanía a fuentes contaminantes'];
$facamb5 = $res5['responseResult'][0]['Conserva alimentos adecuadamente'];
$facamb6 = $res5['responseResult'][0]['Manipula correctamente el agua'];
$facamb7 = $res5['responseResult'][0]['Adquiere medicamentos con fórmula médica'];
$facamb8 = $res5['responseResult'][0]['Almacena químicos de forma segura'];
$facamb9 = $res5['responseResult'][0]['Manejo adecuado de residuos sólidos'];  
// Entorno Habitacional
$entornoHab = $res5['responseResult'][0];

//Riesgo Características Demográficas
$sql6="SELECT 1 FROM person P LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam";
$res6 = datos_mysql($sql6);
$caracDemo = $res6['responseResult'][0];

// Generar factores de riesgo aleatorios
$riesgos = [
    "socioeconomic" => [
        "name" => "Status Socioeconómico",
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
        "zona-Apgar" => $zona,
        "description" => "Influye en el apoyo social, la funcionalidad y la estabilidad del hogar."
    ],
    "socialVulnerability" => [
        "name" => "Vulnerabilidad Social",
        "value" => $vulnSocial,
        "weight" => 0.12,
        "puntajeTotal" => $puntajeTotal,
        "description" => "Considera factores como la violencia, el desplazamiento y la exclusión social."
    ],
    "accessToHealth" => [
        "name" => "Acceso a Servicios de Salud",
        "value" => $accesoSaludPorcentaje,
        "weight" => 0.10,
        "puntajeRegimen" => $puntajeRegimenSalud,
        "description" => "Clave para la prevención y el cuidado de enfermedades."
    ],
    "livingEnvironment" => [
        "name" => "Entorno Habitacional",
        "value" => $puntajeEHBruto,
        "weight" => 0.10,
        "zona" => $zona,
        "tipoVivienda" => $tipoVivienda,
        "tenencia" => $tenencia,
        "actividadEconomica" => $actividadEconomica,
        "energia" => $energia,
        "gas" => $gas,
        "acueducto" => $acueducto,
        "alcantarillado" => $alcantarillado,
        "basuras" => $basuras,
        "pozo" => $pozo,
        "aljibe" => $aljibe,
        "facamb1" => $facamb1,
        "facamb2" => $facamb2,
        "facamb3" => $facamb3,
        "facamb4" => $facamb4,
        "facamb5" => $facamb5,
        "facamb6" => $facamb6,
        "facamb7" => $facamb7,
        "facamb8" => $facamb8,
        "facamb9" => $facamb9,
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