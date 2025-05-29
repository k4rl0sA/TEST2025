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

function focus_asigpred(){
 return 'asigpred';
}

function men_asigpred(){
 $rta=cap_menus('asigpred','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='asigpred'  && isset($acc['crear']) && $acc['crear']=='SI'){  
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
	// $rta .= "<li class='icono $a crear'  title='Actualizar'   id='".print_r($_REQUEST)."'   Onclick=\"\"></li>";
  }
  return $rta;
}

function cmp_asigpred(){
	$rta="<div class='encabezado'>ESTADOS DEL PREDIO</div>
	<div class='contenido' id='predios-lis'>".lis_predios()."</div></div>";
	// $t=['id_deriva'=>'','cod_pre'=>'','zona'=>'','localidad'=>'','upz'=>'','barrio'=>'','sector_catastral'=>'','nummanzana'=>'','predio_num'=>'','unidad_habit'=>'','direccion'=>'','vereda'=>'','cordx'=>'','cordy'=>'','territorio'=>'','direccion_nueva'=>'','vereda_nueva'=>'','cordxn'=>'','cordxy'=>'','estado_v'=>'','motivo_estado'=>'','predio'=>'','family'=>'','rol'=>'','asignado'=>''];
	$d='';
	// if ($d==""){$d=$t;}
	$w='geoloc';
	$o='geo';
	$p='pre';
	$key='pRE';
	$esta = (perfil1()!=='GEO') ? true : false;
	// $c[]=new cmp($p,'e',null,'PREDIO',$w);
	$c[]=new cmp('cod_pre','n','10','',$w.' '.$key.' '.$o,'Codigo del Predio','cod_pre',null,'',true,true,'','col-25',"getDatForm('pRE','predio',['geo'],'../asignacion/lib.php');act_lista('predios');");
	$c[]=new cmp($o,'e',null,'DATOS DEL PREDIO',$w);
	//$c[]=new cmp('cod_pre','n','6','',$w.' '.$key.' '.$o,'Codigo del Predio','cod_pre',null,'',true,true,'','col-25',"getDatForm('pRE','predio',['geo']);");
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,' ','',null,'####',false,false);
	$c[]=new cmp('zona','s','3','',$w.' '.$o,'Zona','zona',null,'',false,false,'','col-25');
    $c[]=new cmp('localidad','s',3,'',$w.' '.$o,'Localidad','localidad',null,'',false,false,'','col-25');
	$c[]=new cmp('upz','s','3','',$w.' '.$o,'Upz','upz',null,'',false,false,'','col-25',false,['bar']);
    $c[]=new cmp('barrio','s','8','',$w.' '.$o,'Barrio','barrio',null,'',false,false,'','col-25');
    
    $c[]=new cmp('sector_catastral','n','6','',$w.' '.$o,'Sector Catastral (6)','sector_catastral',null,'',false,false,'','col-25');
    $c[]=new cmp('nummanzana','n','3','',$w.' '.$o,'Nummanzana (3)','nummanzana',null,'',false,false,'','col-25');
    $c[]=new cmp('predio_num','n','3','',$w.' '.$o,'Predio de Num (3)','predio_num',null,'',false,false,'','col-25');
    $c[]=new cmp('unidad_habit','n','4','',$w.' '.$o,'Unidad habitacional (3)','unidad_habit',null,'',false,false,'','col-25');
    
    $c[]=new cmp('direccion','t','50','',$w.' '.$o,'Direccion','direccion',null,'',false,false,'','col-25');
    $c[]=new cmp('vereda','t','50','',$w.' '.$o,'Vereda','vereda',null,'',false,false,'','col-25');
    $c[]=new cmp('cordx','t','15','',$w.' '.$o,'Cordx','cordx',null,'',false,false,'','col-25');
    $c[]=new cmp('cordy','t','15','',$w.' '.$o,'Cordy','cordy',null,'',false,false,'','col-25');
    
    $c[]=new cmp('territorio','t','6','',$w.' '.$o,'Territorio','territorio',null,'',false,false,'','col-2');
	
	$o='infasi';
	$c[]=new cmp($o,'e',null,'GESTIÓN PARA LA ASIGNACIÓN',$w);
	$c[]=new cmp('rol','s',3,'',$w.' '.$o,'rol','rol',null,'',true,true,'','col-2',false,['asignado']);
	$c[]=new cmp('asignado','s',3,'',$w.' '.$o,'Asignar A','asignado',null,'',true,true,'','col-5');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function lis_asigpred(){

}

FUNCTION lis_predios(){
	// var_dump($_POST);
	$id =isset($_POST['cod_pre']) ? divide($_POST['cod_pre']) : json_encode (new stdClass);
$info=datos_mysql("SELECT COUNT(*) total FROM geo_asig WHERE idgeo='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-predios']))? ($_POST['pag-predios']-1)* $regxPag:0;

  
	$sql="SELECT  A.id_asig 'Cod Registro',A.idgeo 'Cod Predio', A.fecha_create 'Fecha de Asignación', U.nombre 'Colaborador Asignado', concat_ws(' - ',U1.perfil,U1.nombre) 'Quien Asignó'
	FROM geo_asig A
	LEFT JOIN  usuarios U ON A.doc_asignado=U.id_usuario
	LEFT JOIN  usuarios U1 ON A.usu_create=U1.id_usuario ";
$sql.="WHERE idgeo='".$id[0];
$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"predios",$regxPag);
   }


function get_predio(){
	// print_r($_POST);
	$id=divide($_POST['id']);
	$sql="SELECT G.idgeo,G.zona, G.localidad, G.upz, G.barrio, G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.direccion, G.vereda, G.cordx, G.cordy, G.manz_cuidado 
 	FROM `hog_geo` G 
  		WHERE G.idgeo ='".$id[0]."'";
	// var_dump($sql);
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
return json_encode($info['responseResult'][0]);
}

function opc_rolasignado(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT id_usuario,CONCAT(id_usuario,'-',nombre) FROM usuarios WHERE subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}')  AND estado='A' and perfil='".$id[0]."' ORDER BY 1 ASC";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}
function opc_asignado($id=''){
	return opc_sql("SELECT id_usuario,nombre FROM `usuarios` WHERE  subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') AND componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') AND estado='A' ORDER BY 1",$id);
}
function opc_rol($id=''){
	return opc_sql("SELECT distinct perfil,perfil FROM `usuarios` WHERE  subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') AND componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A' ORDER BY 1",$id);
}
function opc_barrio($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=20 and estado='A' ORDER BY 1",$id);
}
function opc_localidad($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=2 ORDER BY cast(idcatadeta as signed)",$id);
}
function opc_zona($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=3 and estado='A' ORDER BY 1",$id);
}
function opc_upz($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=7 and estado='A' ORDER BY 1",$id);
}
function opc_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=44 and estado='A' ORDER BY 1",$id);
}
function opc_motivo_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=5 and estado='A' ORDER BY 1",$id);
}

function gra_asigpred(){
	$id=divide($_POST['cod_pre']);
	$sql = "INSERT INTO geo_asig VALUES(?,?,?,?,?,?,?,?)";
	$params = array(
	array('type' => 'i', 'value' => NULL),
	array('type' => 's', 'value' => $id[0]),
	array('type' => 's', 'value' => $_POST['asignado']),
	array('type' => 'i', 'value' => $_SESSION['us_sds']),
	array('type' => 's', 'value' => date("Y-m-d H:i:s")),
	array('type' => 's', 'value' => NULL),
	array('type' => 's', 'value' => NULL),
	array('type' => 's', 'value' => 'A')
	);
	$rta = mysql_prepd($sql, $params);
	return $rta;
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($c);
	if ($a=='asigpred' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono admsi1' title='Información de la Facturación' id='".$c['ACCIONES']."' Onclick=\"mostrar('asigpred','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
		$rta.="<li class='icono crear' title='Nueva Admisión' id='".$c['ACCIONES']."' Onclick=\"newAdmin('{$c['ACCIONES']}');\"></li>";
	}
	if ($a=='adm-lis' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono editar ' title='Editar ' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'asigpred',event,this,'lib.php');Color('adm-lis');\"></li>";  //act_lista(f,this);
	}
	
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>