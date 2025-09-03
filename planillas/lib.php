<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
if ($_POST['a']!='opc') $perf=perfil($_POST['tb']);
if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
else {
  $rta="";
  switch ($_POST['a']){
  case 'csv': 
    header_csv ($_REQUEST['tb'].'.csv');
    $rs=array('','');    
    echo csv($rs,'');
    die;
    break;
  default:
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) json_encode($rta);
    else echo $rta;
  }   
}

// Listado principal de planillas
function lis_planillas(){
    $info = datos_mysql("SELECT COUNT(*) total FROM `planillas` P WHERE estado='A' " . whe_planillas());
    $total = $info['responseResult'][0]['total'] ?? 0;
    $regxPag = 10;
    $pag = (isset($_POST['pag-planillas'])) ? ($_POST['pag-planillas']-1)* $regxPag : 0;
    $sql = "SELECT CONCAT_WS('_',P.id_planilla,P.idpeople) ACCIONES, P.id_planilla 'ID', P.idpeople 'Cod Persona', P.cod_fam 'Código Familia', P.tipo 'Tipo', P.evento 'Evento', P.seguimiento 'Seguimiento', P.colaborador 'Colaborador', P.estado_planilla 'Estado', P.carpeta 'Carpeta', P.caja 'Caja', P.caracterizacion 'Caracterizacion', P.fecha_formato 'Fecha Formato', P.fecha_create 'Fecha Creación', P.usu_create 'Creó', P.usu_update 'Modificó', P.fecha_update 'Fecha Modificación' FROM `planillas` P WHERE estado='A' ";
    $sql .= whe_planillas();
    $sql .= " ORDER BY P.fecha_create DESC";
    $sql .= ' LIMIT '.$pag.','.$regxPag;
    $datos = datos_mysql($sql);
    $no = ['ID'];
    return create_table($total, $datos["responseResult"], "planillas", $regxPag, 'lib.php', $no);
}

// Filtros para planillas
function whe_planillas() {
    $sql = "";
    if (!empty($_POST['fidpeople']))
        $sql .= " AND P.idpeople = '".cleanTx($_POST['fidpeople'])."'";
    if (!empty($_POST['fcod_fam']))
        $sql .= " AND P.cod_fam ='".cleanTx($_POST['fcod_fam'])."' ";
    if (!empty($_POST['ftipo']))
        $sql .= " AND P.tipo ='".cleanTx($_POST['ftipo'])."' ";
    if (!empty($_POST['festado_planilla']))
        $sql .= " AND P.estado_planilla ='".cleanTx($_POST['festado_planilla'])."' ";
    if (!empty($_POST['fevento']))
        $sql .= " AND P.evento ='".cleanTx($_POST['fevento'])."' ";
    if (!empty($_POST['fcarpeta']))
        $sql .= " AND P.carpeta ='".cleanTx($_POST['fcarpeta'])."' ";
    if (!empty($_POST['fcaja']))
        $sql .= " AND P.caja ='".cleanTx($_POST['fcaja'])."' ";
    return $sql;
}

function focus_planillas(){
 return 'planillas';
}

function men_planillas(){
 $rta=cap_menus('planillas','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='planillas'  && isset($acc['crear']) && $acc['crear']=='SI'){  
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
    $rta .= "<li class='icono $a crear'  title='Actualizar'      Onclick=\"ocultar('$a','pro');mostrar('$a','pro');\"></li>";//mostrar(mod,'pro');
  }
  return $rta;
}

// Componente principal para mostrar datos de una planilla
function cmp_planillas(){
    $rta = "";
    $c = [];
    $w='planillas';
	$o='infplan';
    $edit = (empty($id[0])) ? true : (isset($_POST['edit']) && $_POST['edit']=='true');
    $d = get_planilla();
    $t=['tipo'=>'','evento'=>'','seguimiento'=>'','idpersona'=>'','tipo_doc'=>'','nombre_completo'=>'','perfil'=>'','colaborador'=>'','estado_planilla'=>'','carpeta'=>'','caja'=>'','fecha_formato'=>''];
    if ($d==""){$d=$t;}
    $key='pEr';
    $days=fechas_app('vivienda');
    $c[]= new cmp($o,'e',null,'INFORMACIÓN GENERAL',$w);
    $c[]= new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
    $c[]= new cmp('idpersona','nu','9999999999999999',$d['idpersona'],$w.' '.$key.' '.$o,'Identificación <a href="https://www.adres.gov.co/consulte-su-eps" target="_blank">     Abrir ADRES</a>','idpersona',null,null,true,$edit,'','col-2');
	$c[]= new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$key.' '.$o,'Tipo documento','tipo_doc',null,null,true,$edit,'','col-3',"getDatKey('pEr','personOld','infplan',['idpersona','tipo_doc'],'lib.php');");//getData('pEr','personOld',['infplan']);
    // $c[]= new cmp('idpeop','t',18,'',$w.' IPe '.$o, 'Código Persona','','','',true,true);
    $c[]= new cmp('nombre','t',50,'',$w.' IPe '.$o, 'Nombre Completo','','','',false,false,'','col-5');
    
    $o='infubi';
    $c[]= new cmp('fecha_formato','d','',$d['fecha_formato'],$w.' '.$o,'Fecha del Formato','fecha_formato',null,null,true,$edit,'','col-2',"validDate(this,$days,0);");
    $c[]= new cmp('tipo','s',3,$d['tipo'] ,$w.' '.$o, 'Tipo Planilla', 'tipo_planilla','','',true,true,'','col-2',"typeSheet();");
    $c[]= new cmp('evento','s',3,$d['evento'] ,$w.' Pcf '.$o, 'Evento','evento','','',true,false,'','col-4');
    $c[]= new cmp('seguimiento','nu',50,$d['seguimiento'] ,$w.' Pcf '.$o, 'Seguimiento','','','',true,false,'','col-2','',[],false,'lib.php',1);
    $c[]= new cmp('perfil','s',3,'',$w.' '.$o,'Perfil','perfil',null,'',true,true,'','col-15',"changeSelect('perfil','colaborador');");//  ,"enabDepeValu('perfil','uSR');
    $c[]= new cmp('colaborador','s',20,$d['colaborador'] ,$w.' uSR '.$o, 'Colaborador','colaborador','','',false,true,'','col-35',"cargarResumenFamiliar();cargarResumenIndivi();");//  ,"enabDepeValu('perfil','uSR');
    $c[]= new cmp('estado_planilla','s',3,$d['estado_planilla'] ,$w.' '.$o, 'Estado Planilla', 'estado_planilla','','',true,true,'','col-2');
    $c[]= new cmp('carpeta','nu',50,$d['carpeta'] ,$w.' '.$o, 'Carpeta','','','',true,true,'','col-15');
    $c[]= new cmp('caja','nu',50,$d['caja'] ,$w.' '.$o, 'Caja','','','',true,true,'','col-15');
    foreach ($c as $cmp) $rta .= $cmp->put();
    $rta .= "<div class='padre' style='display: flex; width: 100%; gap: 10px;'>
        <div id='valida-family' style='flex: 1 1 0; min-width: 0;'></div>
        <div id='valida-indivi' style='flex: 1 1 0; min-width: 0;'></div>
    </div>";
    return $rta;
}

function family_planillas(){
    $id=divide($_POST['id']);
    $info = datos_mysql("SELECT P.idpeople idpeople, P.vivipersona idfam FROM person P WHERE P.idpersona = $id[0] AND P.tipo_doc = '$id[1]'");
    $row = $info['responseResult'][0];
    $idp=$row['idpeople'];
    $idfam = $row['idfam'];
    $items = [
        'Caracterización' => "SELECT CASE WHEN EXISTS (
            SELECT 1 FROM hog_carac C WHERE C.idfam = $idfam AND C.fecha = '$id[2]' AND C.usu_create = $id[3] AND C.estado = 'A'
        ) THEN 'Completado' ELSE 'Validar' END AS Estado,
          C2.id_viv AS id
        ( SELECT MAX(C2.fecha) FROM hog_carac C2 WHERE C2.idfam = $idfam AND C2.usu_create = $id[3] AND C2.estado = 'A' AND C2.fecha >= (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01') END) AND C2.fecha < (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(CURDATE(), '%Y-%m-01') ELSE DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') END)) AS fecha_ultima;",
        'Plan de Cuidado Familiar' => "SELECT CASE WHEN EXISTS (
            SELECT 1 FROM hog_plancuid C WHERE C.idviv = $idfam AND C.fecha = '$id[2]' AND C.usu_creo = $id[3] AND C.estado = 'A'
        ) THEN 'Completado' ELSE 'Validar' END AS Estado,
        ( SELECT MAX(C2.fecha) FROM hog_plancuid C2 WHERE C2.idviv = $idfam AND C2.usu_creo = $id[3] AND C2.estado = 'A' AND C2.fecha >= (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01') END) AND C2.fecha < (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(CURDATE(), '%Y-%m-01') ELSE DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') END)) AS fecha_ultima;",
        'Compromisos Concertados' => "SELECT CASE WHEN EXISTS (SELECT 1 FROM hog_planconc C WHERE C.idviv = $idfam AND C.fecha = '$id[2]' AND C.usu_creo = $id[3] AND C.estado = 'A'
        ) THEN 'Completado' ELSE 'Validar' END AS Estado,( SELECT MAX(C2.fecha) FROM hog_planconc C2 WHERE C2.idviv = $idfam AND C2.usu_creo = $id[3] AND C2.estado = 'A' AND C2.fecha >= (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01') END) AND C2.fecha < (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(CURDATE(), '%Y-%m-01') ELSE DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') END)) AS fecha_ultima;",
        'Tamizaje Apgar' => "SELECT CASE WHEN EXISTS (SELECT 1 FROM hog_carac C WHERE C.idfam = $idfam AND C.fecha = '$id[2]' AND C.usu_create = $id[3] AND C.estado = 'A' AND EXISTS (SELECT 1 FROM hog_tam_apgar A INNER JOIN person P ON A.idpeople = P.idpeople WHERE P.vivipersona = C.idfam AND C.fecha = A.fecha_toma AND C.usu_create = A.usu_creo)) THEN 'Completado'  ELSE 'Validar' END AS Estado,
        (SELECT MAX(A.fecha_toma) FROM hog_tam_apgar A INNER JOIN person P ON A.idpeople = P.idpeople WHERE P.vivipersona = $idfam AND A.fecha_toma >= (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01') END) AND A.fecha_toma < ( CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(CURDATE(), '%Y-%m-01') ELSE DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') END)) AS fecha_ultima;"
    ];
    // var_dump($items);
    $result = [];
    foreach ($items as $nombre => $sql) {
        $info = datos_mysql($sql);
        $estado = $info['responseResult'][0]['Estado'] ?? 'Validar';
        $idcara=$info['responseResult'][0]['id'] ?? '';
        $fecha_ultima = isset($info['responseResult'][0]['fecha_ultima']) ? $info['responseResult'][0]['fecha_ultima'] : '';
        // Mostrar siempre la fecha_ultima si existe
        $result[] = ['nombre' => $nombre, 'estado' => $estado,'fecha_ultima'=>$fecha_ultima,'id'=>$idcara];
    }
    echo json_encode($result);
    die;
}

function indivi_planillas(){
    $id = divide($_POST['id']); // Espera: idpersona_tipo_doc_fecha_usuario
    // Obtener idfam desde la persona
    $info = datos_mysql("SELECT P.idpeople idpeople, P.vivipersona idfam FROM person P WHERE P.idpersona = $id[0] AND P.tipo_doc = '$id[1]'");
    $row = $info['responseResult'][0];
    $idfam = $row['idfam'];

    $sql = "SELECT P.idpeople, CASE WHEN MAX(A.id_alert) IS NOT NULL THEN 'Completado' ELSE 'Validar' END AS estado_alerta, 
    COALESCE(MAX(A.fecha), MAX(Aw.fecha)) AS fecha_alerta_ultima, 
    CASE WHEN MAX(S.id_signos) IS NOT NULL THEN 'Completado' ELSE 'Validar' END AS estado_signos, 
    COALESCE(MAX(S.fecha_toma), MAX(Sw.fecha_toma)) AS fecha_signos_ultima 
    FROM person P LEFT JOIN hog_alert A ON P.idpeople = A.idpeople AND A.fecha = '$id[2]' AND A.usu_creo = $id[3] AND A.estado = 'A'
    LEFT JOIN hog_signos S ON P.idpeople = S.idpeople AND S.fecha_toma = '$id[2]' AND S.usu_create = $id[3] AND S.estado = 'A'
    LEFT JOIN hog_alert Aw ON P.idpeople = Aw.idpeople AND Aw.usu_creo = $id[3] AND Aw.estado = 'A' AND Aw.fecha BETWEEN (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE()) - 1 DAY), INTERVAL 1 MONTH) ELSE DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE()) - 1 DAY) END) AND (CASE WHEN DAY(CURDATE()) <= 5 THEN LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) ELSE LAST_DAY(CURDATE()) END) 
    LEFT JOIN hog_signos Sw ON P.idpeople = Sw.idpeople AND Sw.usu_create = $id[3] AND Sw.estado = 'A' AND Sw.fecha_toma BETWEEN (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_SUB(DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE()) - 1 DAY), INTERVAL 1 MONTH) ELSE DATE_SUB(CURDATE(), INTERVAL DAY(CURDATE()) - 1 DAY) END) AND (CASE WHEN DAY(CURDATE()) <= 5 THEN LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) ELSE LAST_DAY(CURDATE()) END) 
    WHERE P.vivipersona = $idfam GROUP BY P.idpeople;";

    // var_dump($sql);
    $info = datos_mysql($sql);
    $result = [];
    foreach ($info['responseResult'] as $row) {
        $result[] = [
            'idpeople' => $row['idpeople'],
            'estado_alerta' => $row['estado_alerta'],
            'fecha_alerta' => $row['fecha_alerta_ultima'] ?? '',
            'fecha_signos' => $row['fecha_signos_ultima'] ?? '',
            'estado_signos' => $row['estado_signos']
        ];
    }
    echo json_encode($result);
    die;
}

function get_planilla() {
    $id = divide($_POST['id'] ?? '');
    if (empty($id[0])) return "";
    $sql = "SELECT P.*, CONCAT_WS(' ',pe.nombre1,pe.nombre2,pe.apellido1,pe.apellido2) AS nombre_completo, pe.tipo_doc, pe.idpersona, P.fecha_formato
            FROM planillas P
            INNER JOIN person pe ON P.idpeople = pe.idpeople
            WHERE P.id_planilla = $id[0] AND P.estado = 'A'";
    $info = datos_mysql($sql, $params);
    if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
} 

function get_personOld(){
	// print_r($_REQUEST);
	$id=divide($_POST['id']);
    //,idpeople,vivipersona
		$sql="SELECT idpeople,idpersona,tipo_doc,CONCAT_WS(' ',nombre1,nombre2,apellido1,apellido2) as nombre
		FROM `person` 
   	WHERE idpersona ='".$id[0]."' AND tipo_doc='".$id[1]."'";
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
        return json_encode (new stdClass);
	}else{
		return json_encode($info['responseResult'][0]);
	}
}

    function adm(){
        $info = datos_mysql("SELECT perfil FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'");
        $adm = $info['responseResult'][0]['perfil'];
        return $adm;
    }

// Opciones para selects si tienes catálogos
function opc_tipo_planilla($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=287 and estado='A' ORDER BY 1",$id);
}
function opc_estado_planilla($id=''){
    return opc_sql("SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=2 and estado='A' ORDER BY 1",$id);
}
function opc_perfil($id=''){
    if (adm()=='ADM') {
        return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo = 218 AND estado = 'A'",$id);
    } else {
		$comp = '1,2';
        return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo = 218 AND estado = 'A' AND valor in($comp)",$id);
    }
}
function opc_colaborador($id=''){
	return opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` where estado='A' ORDER BY 1",$id);
}
function opc_evento($id=''){
    return opc_sql("SELECT `idcatadeta`,concat_ws(' - ',idcatadeta,descripcion) FROM `catadeta` WHERE idcatalogo=87 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_perfilcolaborador($id=''){
    if($_REQUEST['id']!=''){	
        if(adm()=='ADM'){	
            $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE 
            perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') and estado='A'
            ORDER BY nombre";
            $info = datos_mysql($sql);		
            return json_encode($info['responseResult']);	
        } else {
            $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE 
            perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
            and subred=(SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') and estado='A' ORDER BY nombre";
            $info = datos_mysql($sql);		
            return json_encode($info['responseResult']);	
        }
    } 
}

function opc_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
// Grabar/actualizar planilla
function gra_planillas(){
    $sql1="SELECT idpeople,vivipersona
		FROM `person` 
   	WHERE idpersona ='".$_POST['idpeople']."' AND tipo_doc='".$_POST['tipo']."' AND estado='A'";
	$info=datos_mysql($sql1);
    if ($info['responseResult']) {
        $codfam=$info['responseResult'][0]['vivipersona'];
        $idpeople=$info['responseResult'][0]['idpeople'];
    }

    $sql2 = "SELECT CASE WHEN EXISTS (SELECT 1 FROM hog_carac C WHERE C.idfam = $codfam AND C.fecha = '$id[2]' AND C.usu_create = $id[3]) THEN 0 ELSE 1 END AS Estado,
      C2.id_viv,C2.fecha AS fecha_ultima
FROM hog_carac C2
JOIN (SELECT MAX(fecha) AS max_fecha FROM hog_carac WHERE idfam = $codfam AND usu_create = $id[3]AND fecha >= (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') ELSE DATE_FORMAT(CURDATE(), '%Y-%m-01') END) AND fecha < (CASE WHEN DAY(CURDATE()) <= 5 THEN DATE_FORMAT(CURDATE(), '%Y-%m-01') ELSE DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') END) F ON C2.fecha = F.max_fecha
WHERE C2.idfam = $codfam AND C2.usu_create = $id[3] LIMIT 1;";
    $info=datos_mysql($sql2);
    if ($info['responseResult']) {
        $idcara=$info['responseResult'][0]['id_viv'];
        $estado_carac=$info['responseResult'][0]['Estado'];
   }
     


    $id = divide($_POST['id_planilla']);
    $isNew = empty($id[0]);
    if ($isNew) {
        $sql = "INSERT INTO planillas (idpeople,cod_fam,tipo,evento,seguimiento,fecha_formato,colaborador,estado_planilla,carpeta,caja,caracterizacion,pcf,comp,apgar,usu_create,fecha_create,estado) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? DATE_SUB(NOW(),INTERVAL 5 HOUR), 'A')";
        
        $params = [
            ['type' => 'i', 'value' => $idpeople],
            ['type' => 'i', 'value' => $codfam],
            ['type' => 'i', 'value' => $_POST['tipo']],
            ['type' => 'i', 'value' => $_POST['evento']],
            ['type' => 'i', 'value' => $_POST['seguimiento']],
            ['type' => 's', 'value' => $_POST['fecha_formato']],
            ['type' => 'i', 'value' => $_POST['colaborador']],
            ['type' => 'i', 'value' => $_POST['estado_planilla']],
            ['type' => 's', 'value' => $_POST['carpeta']],
            ['type' => 's', 'value' => $_POST['caja']],
            ['type' => 's', 'value' => $_POST['caracterizacion']],
            ['type' => 's', 'value' => $_POST['pcf']],
            ['type' => 's', 'value' => $_POST['comp']],
            ['type' => 's', 'value' => $_POST['apgar']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
        ];
    } else {
        $sql = "UPDATE planillas SET idpeople=?, cod_fam=?, tipo=?, evento=?, seguimiento=?, colaborador=?, estado_planilla=?, carpeta=?, caja=?, caracterizacion=?, fecha_formato=?, usu_update=?, fecha_update=NOW() WHERE id_planilla=?";
        $params = [
            ['type' => 'i', 'value' => $_POST['idpeople']],
            ['type' => 'i', 'value' => $_POST['cod_fam']],
            ['type' => 's', 'value' => $_POST['tipo']],
            ['type' => 's', 'value' => $_POST['evento']],
            ['type' => 's', 'value' => $_POST['seguimiento']],
            ['type' => 'i', 'value' => $_POST['colaborador']],
            ['type' => 's', 'value' => $_POST['estado_planilla']],
            ['type' => 's', 'value' => $_POST['carpeta']],
            ['type' => 's', 'value' => $_POST['caja']],
            ['type' => 's', 'value' => $_POST['caracterizacion']],
            ['type' => 's', 'value' => $_POST['fecha_formato']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
            ['type' => 'i', 'value' => $id[0]],
        ];
    }
    return mysql_prepd($sql, $params);
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($rta);
	if ($a=='family' && $b=='acciones'){//a mnombre del modulo
		
	}
 return $rta;
}


function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
