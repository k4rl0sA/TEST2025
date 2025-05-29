<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
$perf=perfil($_POST['tb']);
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

function lis_session(){
	// print_r($_POST);
	// print_r($_REQUEST);
	$id=divide($_POST['id']);
	$info=datos_mysql("SELECT COUNT(*) total FROM `rel_sesion` WHERE id_people='{$id[0]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=3;
	$pag=(isset($_POST['pag-session']))? ($_POST['pag-session']-1)* $regxPag:0;

	$sql="SELECT idsesion ACCIONES,idsesion 'Cod Registro',`rel_validacion1` Sesion, `rel_validacion2` Fecha,rel_validacion3 perfil,FN_CATALOGODESC(301,`rel_validacion4`) Actividad,`rel_validacion5`
  	descripcion 
	FROM `rel_sesion`
	WHERE id_people='{$id[0]}'";
	$sql.=" ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
		//  echo $sql;
		$datos=datos_mysql($sql);
		return create_table($total,$datos["responseResult"],"session",$regxPag,'../relevo/sesiones.php');
}

function lis_medidaux(){
	// print_r($_POST);
	// print_r($_REQUEST);
	$id=divide($_POST['id']);
	$info=datos_mysql("SELECT COUNT(*) total FROM rel_signvitales WHERE tipo_doc='{$id[0]}' and idpersona='{$id[1]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=3;
	$pag=(isset($_POST['pag-session']))? ($_POST['pag-session']-1)* $regxPag:0;

	$sql="SELECT idsignos ACCIONES,tas , tad,frecard Frecuencia,satoxi Saturación,fecha_create 'Fecha de Ingreso'
	FROM `rel_signvitales`
	WHERE tipo_doc='{$id[0]}' and idpersona='{$id[1]}'";
	$sql.=" ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
		//  echo $sql;
		$datos=datos_mysql($sql);
		return create_table($total,$datos["responseResult"],"medidaux",$regxPag,'sesiones.php');
}

/* function lis_session() {
    $id = divide($_POST['id']);
    $regxPag = 3;
    $pag = isset($_POST['pag-session']) ? ($_POST['pag-session'] - 1) * $regxPag : 0;
    $sql = "SELECT idsesion ACCIONES, `rel_validacion1` Sesion, `rel_validacion2` Fecha, rel_validacion3 perfil, FN_CATALOGODESC(301, `rel_validacion4`) Actividad, `rel_validacion5` descripcion,
                (SELECT COUNT(*) FROM `rel_sesion` WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}') AS total
            FROM `rel_sesion`
            WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}'
            ORDER BY fecha_create
            LIMIT $pag, $regxPag";
            
    $datos = datos_mysql($sql);
    $total = $datos["responseResult"][0]['total'];
    return create_table($total, $datos["responseResult"], "session", $regxPag, 'sesiones.php');
} */


function cmp_sesiones() {
	$rta="";
	$rta .="<div class='encabezado placuifam'>TABLA DE COMPROMISOS CONCERTADOS</div>
	<div class='contenido' id='session-lis' >".lis_session()."</div></div>";
	$info=datos_mysql("SELECT FN_PERFIL('{$_SESSION['us_sds']}') perfil;");
	$per=$info['responseResult'][0]['perfil'];
	// var_dump($_POST);
	$w='sesiones';
	$d='';
	$o='infgen';
	$days=fechas_app('relevo');
	$c[]=new cmp($o,'e',null,'Sesion de intervencion y/o Relevos',$w);	
	$aux = ($per=='AUXRELEVO' || $per=='ADM') ? true : false ;//|| $per=='ADM'
	$c[]=new cmp('id','h','20',$_POST['id'],$w.' '.$o,'','',null,null,false,false,'','col-1');
	$c[]=new cmp('rel_validacion1','s','3',$d,$w.' '.$o,'Sesión','rel_sesiones',null,null,true,true,'','col-2');
	$c[]=new cmp('rel_validacion2','d','10',$d,$w.' '.$o,'Fecha de la sesion','rel_validacion2',null,null,true,true,'','col-3',"validDate(this,$days,0);");
	$c[]=new cmp('rel_validacion3','t','5',$per,$w.' '.$o,'Perfil','rel_validacion3',null,null,true,false,'','col-2');
	$c[]=new cmp('rel_validacion4','s','3',$d,$w.' act '.$o,'ACTIVIDAD DE RESPIRO','rel_validacion4',null,null,!$aux,!$aux,'','col-3');
	$c[]=new cmp('rel_validacion5','a','1500',$d,$w.' '.$o,'DESCRIPCION DE LA INTERVENCION','rel_validacion5',null,null,true,true,'','col-10');

	$o='infbit';
	$c[]=new cmp($o,'e',null,'BITACORA DE SESIÓN',$w);	
	$c[]=new cmp('autocuidado','s','3',$d,$w.' aux '.$o,'Autocuidado','autocuidado',null,null,$aux,$aux,'','col-3');
	$c[]=new cmp('activesparc','s','3',$d,$w.' aux '.$o,'Actividades de Esparcimiento','activesparc',null,null,$aux,$aux,'','col-3');
	$c[]=new cmp('infeducom','s','3',$d,$w.' aux '.$o,'Información, educación y Comunicación en salud','infeducom',null,null,$aux,$aux,'','col-4');

	/*if($aux===true || $per=='ADM'){
		$rta .="<div class='encabezado'>TABLA DE MEDIDAS AUXILIAR</div>
	<div class='contenido' id='medidaux-lis' >".lis_medidaux()."</div></div>";
	}*/
	//ACTIVIDAD DE RESPIRO SE HABILITA PARA LOS PERFILES (SE HABILITARA PARA LOS SIGUIENTES PERFILES, LARREL, TOPREL, LEFREL, TSOREL)
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function num_sessions(){
	if($_POST['idg']==''){
		return "";
	}else{
		$id=$_POST['idg'];
		$sql="SELECT max(numfam) nfam
		FROM  rel_sesion
		WHERE idpre=$id";
		// echo $sql;
		var_dump($_POST);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
		$nf = json_encode($info['responseResult'][0]['nfam']);
	if (is_null($nf)) {
		$numf = 1;
	} else {
		$nf_limpio = preg_replace('/\D/', '', $nf);
		if ($nf_limpio === '') {
			$n = 0;
		} else {
			$n = intval($nf_limpio);
		}
		$numf = $n + 1;
	}
	return $numf;
	} 
}

function get_sesiones(){
	// print_r($_POST);
	if($_REQUEST['id']==''){
		return "";
	}else{
		$id=divide($_REQUEST['id']);
		// print_r($_SESSION);
		$sql="SELECT idsesion,`rel_validacion1`, `rel_validacion2`,`rel_validacion3`,
		`rel_validacion4`,`rel_validacion5`,autocuidado,activesparc,infeducom,'' as momento,' ' as tas,' ',' ' as frecard,' ' as satoxi
		FROM rel_sesion WHERE idsesion='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
		return json_encode($info['responseResult'][0]);
	} 
}

function get_sesiones_info(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT rel_tipo_doc,rel_documento,rel_validacion1,rel_validacion2,rel_validacion3,rel_validacion4,estado
		FROM `rel_sesion` WHERE rel_tipo_doc='{$id[0]}' AND rel_documento='{$id[1]}'";
		$info=datos_mysql($sql);
    	// echo $sql."=>".$_POST['id'];
		return $info['responseResult'][0];
	} 
}

function focus_sesiones(){
	return 'sesiones';
}

function men_sesiones(){
 $rta=cap_menus('sesiones','pro');
 return $rta;
}
 
function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	if ($a=='sesiones' && isset($acc['crear']) && $acc['crear']=='SI'){  
		$rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  	}
  	$rta.= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"mostrar('sesiones','pro',event,'','sesiones.php',7);\"></li>";
	return $rta;
}

function gra_sesiones(){
// print_r($_POST);
	$idrel=divide($_POST['id']);
	if(COUNT($idrel)== 1){ 

		$sql = "update rel_sesion SET rel_validacion5=?,usu_update=?,fecha_update=DATE_SUB(NOW(),INTERVAL 5 HOUR) WHERE idsesion=?";
		$params = [
			['type' => 's', 'value' => $_POST['rel_validacion5']],
			['type' => 'i', 'value' => $_SESSION['us_sds']],
			['type' => 'i', 'value' => $idrel[0]]
		];
		return $rta = mysql_prepd($sql, $params);
	  //echo $x;
	//   echo $sql;
	} else {
		$sql = "INSERT INTO rel_sesion VALUES(?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?,'2')";
		$params = [
		['type' => 'i', 'value' => NULL],
		['type' => 's', 'value' => $idrel[0]],
		['type' => 's', 'value' => $_POST['rel_validacion1']],
		['type' => 's', 'value' => $_POST['rel_validacion2']],
		['type' => 's', 'value' => $_POST['rel_validacion3']],
		['type' => 's', 'value' => $_POST['rel_validacion4']],
		['type' => 's', 'value' => $_POST['rel_validacion5']],
		['type' => 's', 'value' => $_POST['autocuidado']],
		['type' => 's', 'value' => $_POST['activesparc']],
		['type' => 's', 'value' => $_POST['infeducom']],
		['type' => 'i', 'value' => $_SESSION['us_sds']],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => NULL]
		];
		return $rta = mysql_prepd($sql, $params);
	}
}

function opc_momento($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 58 and estado='A' ORDER BY 1",$id);
}
function opc_infeducom($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo =157  and estado='A' ORDER BY 1",$id);
}
function opc_activesparc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 194 and estado='A' ORDER BY 1",$id);
}
function opc_autocuidado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 103 and estado='A' ORDER BY 1",$id);
}
function opc_rel_sesiones($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 32 and estado='A' ORDER BY LPAD(idcatadeta,2,'0')",$id);
}
function opc_rel_validacion4($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 301 and estado='A' ORDER BY 1",$id);
}
function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}

function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);
	$rta=$c[$d];
	// $rta=iconv('UTF-8','ISO-8859-1',$rta);
	// var_dump($a);
	// var_dump($c);
	if ($a=='session' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
			$rta.="<li class='icono editar ' title='Editar Sesión' id='".$c['ACCIONES']."' Onclick=\"Color('session-lis');setTimeout(getData,300,'sesiones',event,this,['rel_validacion1','rel_validacion2','rel_validacion3','rel_validacion4'],'../relevo/sesiones.php');setTimeout(auxSign,500,'rel_validacion3','aux');\"></li>";  //getData('plancon',event,this,'id');   act_lista(f,this);
		}
return $rta;
}
