<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
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

function focus_ruteresol(){
 return 'ruteresol';
}

function men_ruteresol(){
 $rta=cap_menus('ruteresol','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='ruteresol'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  }
  return $rta;
}

function cmp_ruteresol(){
 $rta="";
 $t=['id_ruteo'=>'','estado_ruteo'=>'','fecha'=>'','estado_rut'=>'','famili'=>'','usuario'=>''];
 $w='ruteresol';
 $d=get_ruteresol(); 
 if ($d=="") {$d=$t;}
 $u=($d['fecha']== NULL)?true:false;
//  var_dump($d);
 $o='gesres';
 $days=fechas_app('ruteo');
 $c[]=new cmp($o,'e',null,'PROCESO GESTIÓN RESOLUTIVA',$w);
 $c[]=new cmp('id','h','20',$_POST['id'],$w.' '.$o,'','',null,null,true,$u,'','col-1');
 $c[]=new cmp('est','s',3,$d['estado_ruteo'],$w.' '.$o,'Estado Ruteo','estado_ruteo',null,null,true,$u,'','col-2',"stateRutEnd();");
 $c[]=new cmp('fecha','d',3,$d['fecha'],$w.' RuE '.$o,'Fecha','fecha',null,null,true,false,'','col-2',"validDate(this,$days,7);");
 $c[]=new cmp('estado','s',3,$d['estado_rut'],$w.' RuE '.$o,'Estado Predio','estado',null,null,true,false,'','col-2',"changeSelect('estado','famili');validarPorTexto(this);");
 $c[]=new cmp('famili','s',3,$d['famili'],$w.' StG RuE '.$o,'famili','famili',null,'',true, false,'','col-15',"changeSelect('famili','usuario');");//N° FAMILIA
 $c[]=new cmp('usuario','s',3,$d['usuario'],$w.' StG  RuE '.$o,'usuario','usuario',null,'',true,false,'','col-45'); //TIPO_DOC,DOCUMENTO Y NOMBRE USUARIO
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 return $rta;
}

function opc_idgeo($a){
	$id=divide($a);
	$sql="SELECT idgeo AS cod
		FROM `eac_ruteo` WHERE id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		$cod= $info['responseResult'][0]['cod'];
	return $cod;
}

function opc_cod_predio($co=''){
	$sql="SELECT idgeo AS Cod_Predio ,FN_CATALOGODESC(44,estado_v) estado from geo_gest where idgeo=$co";
	$info=datos_mysql($sql);
	$cod= $info['responseResult'][0]['id'];
	return $cod;
}

function opc_estado($id=''){
	$cod=opc_idgeo($_REQUEST['id']);
	// var_dump($_REQUEST);
		return	opc_sql("SELECT DISTINCT(idgeo) AS Cod_Predio, FN_CATALOGODESC(44,estado_v) from geo_gest where idgeo='$cod' AND estado_v>3",$id);
}

function opc_estadofamili(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT id_fam AS 'Cod_Familia', concat(id_fam,' - ','FAMILIA ',numfam) FROM hog_fam hv where idpre={$id[0]}";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 
}

function opc_familiusuario($id=''){
	// var_dump($id);
	if ($id==''){

	}else{
		$co=divide($id);
		// var_dump($sql);
		return opc_sql("SELECT idpeople AS 'usuario', concat_ws('-',idpeople,idpersona) FROM person p where vivipersona='$co[0]'", $id);
	}
}

function opc_famili($id=''){
	// var_dump($id);
	if ($id==''){

	}else{
		return opc_sql("SELECT id_fam AS 'Cod_Familia', concat(id_fam,' - ','FAMILIA ',numfam) FROM hog_fam hv where id_fam='$id'", $id);
	}
}

function opc_usuario($id=''){
	// var_dump($id);
	if ($id==''){

	}else{
		$co=divide($id);
		// var_dump($id);
		return opc_sql("SELECT idpeople,CONCAT_WS('-',idpersona,tipo_doc,CONCAT_WS(' ',nombre1,apellido1)) FROM person p 
		WHERE idpeople='$co[0]'", $id);
	}
	// return opc_sql("SELECT CONCAT_WS('_',tipo_doc,idpersona),CONCAT_WS('-',idpersona,tipo_doc,CONCAT_WS(' ',nombre1,apellido1)) FROM personas p WHERE vivipersona={$id} ORDER BY 1", $id);
}



function opc_estado_ruteo($id=''){
	return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=278 and estado="A" ORDER BY 1',$id);
	}

function get_ruteresol(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		// var_dump($id);
		$sql="SELECT id_ruteo,estado_ruteo,fecha,estado_rut,famili,usuario FROM `eac_ruteo` WHERE  id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
    	// var_dump($info['responseResult'][0]);
		return $info['responseResult'][0];
	} 
}

function gra_ruteresol(){
$sql="UPDATE `eac_ruteo` SET 
estado_ruteo=TRIM(UPPER('{$_POST['est']}')),
estado_rut=TRIM(UPPER('{$_POST['estado']}')),
famili=TRIM(UPPER('{$_POST['famili']}')),
usuario=TRIM(UPPER('{$_POST['usuario']}')),
fecha=TRIM(UPPER('{$_POST['fecha']}')),
usuario=TRIM(UPPER('{$_POST['usuario']}')),
`usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),
`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR)
	WHERE id_ruteo='{$_POST['id']}'";
	//echo $sql;
  $rta=dato_mysql($sql);
  return $rta;
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($rta);
	if ($a=='ruteresol' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono mapa' title='Ruteo' id='".$c['ACCIONES']."' Onclick=\"mostrar('ruteresol','pro',event,'','lib.php',7);\"></li>";
	}
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
