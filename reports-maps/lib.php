<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');

// Verificar si las claves 'a' y 'tb' están definidas en $_POST
if (isset($_POST['a']) && isset($_POST['tb'])) {
    if ($_POST['a'] != 'opc') $perf = perfil($_POST['tb']);
    if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
    else {
        $rta = "";
        switch ($_POST['a']) {
            case 'csv':
                header_csv($_REQUEST['tb'] . '.csv');
                $rs = array('', '');
                echo csv($rs, '');
                die;
                break;
            default:
                eval('$rta=' . $_POST['a'] . '_' . $_POST['tb'] . '();');
                if (is_array($rta)) json_encode($rta);
                else echo $rta;
        }
    }
}else {
    // var_dump($_POST);
	 "Error: Parámetros 'a' y 'tb' no están definidos en la solicitud.";
}


/* function opc_3(){
    $title=['Coord. Y', 'Coord. X', 'Estado','Marker'];
        $sql= " select  hg.cordy,hg.cordx, ifnull(hc.fecha,'NO') as Caracterizado,
        'https://www.google.com/maps/',
         CASE
        WHEN hc.fecha IS NULL THEN 'red'
        ELSE 'blue'
    END AS color
         FROM hog_geo hg 
        left JOIN geo_gest g ON hg.idgeo=g.idgeo 
        LEFT JOIN hog_fam f ON hg.idgeo=f.idpre
        LEFT JOIN hog_carac hc ON f.id_fam=hc.idfam
        WHERE 1 ". whe_opc_3();

        $data = datos_mysql($sql);
        $json = $data['responseResult'];

        $rta = array();
        foreach ($json as $fila) {
            $row = array(
                floatval($fila['cordy']),
                floatval($fila['cordx']),
                $fila['Caracterizado'],
                $fila['color']
            );
            $rta[] = $row;
        }
        $out= array_merge([$title],$rta);
    // var_dump($sql);
    echo json_encode($out);
} */

function opc_3() {
    $title = ['Coord. Y', 'Coord. X', 'Estado', 'Marker', 'URL'];
    
    $sql = "SELECT 
        hg.cordy,
        hg.cordx, 
        IFNULL(hc.fecha, 'NO') as Caracterizado,
        CASE
            WHEN hc.fecha IS NULL THEN 'red'
            ELSE 'blue'
        END AS color,
        'https://www.google.com/maps/' as url
    FROM hog_geo hg 
    LEFT JOIN geo_gest g ON hg.idgeo = g.idgeo 
    LEFT JOIN hog_fam f ON hg.idgeo = f.idpre
    LEFT JOIN hog_carac hc ON f.id_fam = hc.idfam
    WHERE 1 " . whe_opc_3();

    $data = datos_mysql($sql);
    $json = $data['responseResult'];

    $rta = array();
    foreach ($json as $fila) {
        $row = array(
            floatval($fila['cordy']),  // Latitud
            floatval($fila['cordx']),  // Longitud
            $fila['Caracterizado'],    // Estado
            $fila['color'],            // Color del marcador
            $fila['url']              // URL genérica
        );
        $rta[] = $row;
    }
    
    $out = array_merge([$title], $rta);
    echo json_encode($out, JSON_NUMERIC_CHECK);
}

function whe_opc_3() {
	$sql = "";
	if ($_POST['floc'])
		$sql .= " AND localidad = '".$_POST['floc']."'";
	if ($_POST['fter'])
		$sql .= " AND territorio =(select descripcion from catadeta where idcatalogo=283 AND idcatadeta=".$_POST['fter'].")";
	if ($_POST['fest']){
		$sql .= " AND estado_v ='".$_POST['fest']."' ";
	}
	return $sql;
}


function opc_flocfter(){
    $id=divide($_REQUEST['id']);
    $sql="SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=283 and estado='A' and 
    valor=(select valor from catadeta where idcatalogo=2 AND  idcatadeta=$id[0]) ORDER BY CAST(idcatadeta AS UNSIGNED)";
    $info=datos_mysql($sql);		
    return json_encode($info['responseResult']);
    // return json_encode($sql);

}

