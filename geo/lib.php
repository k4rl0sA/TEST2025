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


function lis_geoloc(){
}

function focus_geoloc(){
 return 'geoloc';
}


function men_geoloc(){
 $rta=cap_menus('geoloc','pro');
 return $rta;
}


function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='geoloc'  && isset($acc['crear']) && $acc['crear']=='SI'){  
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
	// $rta .= "<li class='icono $a crear'  title='Actualizar'   id='".print_r($_REQUEST)."'   Onclick=\"\"></li>";
  }
  return $rta;
}

function cmp_geoloc(){
	/* $rta=""; */
	$rta="<div class='encabezado'>ESTADOS DEL PREDIO</div>
	<div class='contenido' id='predios-lis'>".lis_predios()."</div></div>";
	// $t=['id_deriva'=>'','cod_pre'=>'','zona'=>'','localidad'=>'','upz'=>'','barrio'=>'','sector_catastral'=>'','nummanzana'=>'','predio_num'=>'','unidad_habit'=>'','direccion'=>'','vereda'=>'','cordx'=>'','cordy'=>'','territorio'=>'','direccion_nueva'=>'','vereda_nueva'=>'','cordxn'=>'','cordxy'=>'','estado_v'=>'','motivo_estado'=>'','predio'=>'','family'=>'','rol'=>'','asignado'=>''];
	$d='';
	// if ($d==""){$d=$t;}
	$w='geoloc';
	$o='geo';
	$p='pre';
	$key='pRE';
	// $c[]=new cmp($p,'e',null,'PREDIO',$w);
	$c[]=new cmp('cod_pre','n','7','',$w.' '.$key.' '.$o,'Codigo del Predio','cod_pre',null,'',true,true,'','col-25',"getDatForm('pRE','predio',['geo'],this);act_lista('predios');");
	$c[]=new cmp($o,'e',null,'DATOS DEL PREDIO',$w);
	//$c[]=new cmp('cod_pre','n','6','',$w.' '.$key.' '.$o,'Codigo del Predio','cod_pre',null,'',true,true,'','col-25',"getDatForm('pRE','predio',['geo']);");
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,' ','',null,'####',false,false);
	$c[]=new cmp('zona','s','3','',$w.' '.$o,'Zona','zona',null,'',true,false,'','col-25');
    $c[]=new cmp('localidad','s',3,'',$w.' '.$o,'Localidad','localidad',null,'',true,false,'','col-25');
	$c[]=new cmp('upz','s','3','',$w.' '.$o,'Upz','upz',null,'',true,false,'','col-25',false,['bar']);
    $c[]=new cmp('barrio','s','8','',$w.' '.$o,'Barrio','barrio',null,'',true,false,'','col-25');
    
    $c[]=new cmp('sector_catastral','n','6','',$w.' '.$o,'Sector Catastral (6)','sector_catastral',null,'',true,false,'','col-25');
    $c[]=new cmp('nummanzana','n','3','',$w.' '.$o,'Nummanzana (3)','nummanzana',null,'',true,false,'','col-25');
    $c[]=new cmp('predio_num','n','3','',$w.' '.$o,'Predio de Num (3)','predio_num',null,'',true,false,'','col-25');
    $c[]=new cmp('unidad_habit','n','4','',$w.' '.$o,'Unidad habitacional (3)','unidad_habit',null,'',true,false,'','col-25');
    
    $c[]=new cmp('direccion','t','50','',$w.' '.$o,'Direccion','direccion',null,'',false,false,'','col-25');
    $c[]=new cmp('vereda','t','50','',$w.' '.$o,'Vereda','vereda',null,'',false,false,'','col-25');
    $c[]=new cmp('cordx','t','15','',$w.' '.$o,'Cordx','cordx',null,'',false,false,'','col-25');
    $c[]=new cmp('cordy','t','15','',$w.' '.$o,'Cordy','cordy',null,'',false,false,'','col-25');
    
    $c[]=new cmp('territorio','s','6','',$w.' '.$o,'Territorio','territorio',null,'',false,false,'','col-2');
	
	$o='infasi';
	$c[]=new cmp($o,'e',null,'GESTIÓN DEL PREDIO',$w);
	$c[]=new cmp('edi','o',2,'',$w.' '.$o,'Actualiza Dirección ?','edi',null,'',false,true,'','col-1','enableAddr(this,\'adur\',\'adru\',\'zona\');');//enabFiel(this,true,[adi]);updaAddr(this,false,[\'zona\',\'direccion_nueva\',\'vereda_nueva\',\'cordxn\',\'cordyn\'])
	$c[]=new cmp('direccion_nueva','t','50','',$w.' adur '.$o,'Direccion Nueva','direccion_nueva',null,'',false,false,'','col-25');
    $c[]=new cmp('vereda_nueva','t','50','',$w.' adru '.$o,'Vereda Nueva','vereda_nueva',null,'',false,false,'','col-25');
    $c[]=new cmp('cordxn','t','15','',$w.' adru '.$o,'Cordx Nueva','cordx',null,'',false,false,'','col-2');
    $c[]=new cmp('cordyn','t','15','',$w.' adru '.$o,'Cordy Nueva','cordy',null,'',false,false,'','col-2');
	
	$c[]=new cmp('estado_v','s',2,'',$w.' '.$o,'estado','estado',null,'',true,true,'','col-3',"enabFielSele(this,true,['motivo_estado'],['5']);");//hideExpres(\'estado_v\',[\'7\']);
    $c[]=new cmp('motivo_estado','s','3','',$w.' '.$o,'Motivo de Estado','motivo_estado',null,'',false,false,'','col-3');

 
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}


FUNCTION lis_predios(){
	//var_dump($_POST['id']);
	$id =isset($_POST['cod_pre']) ? divide($_POST['cod_pre']) : json_encode (new stdClass);
$info=datos_mysql("SELECT COUNT(*) total FROM geo_gest WHERE estado_v!=1 AND idgeo='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-predios']))? ($_POST['pag-predios']-1)* $regxPag:0;

  
	$sql="SELECT  id_ges 'Cod Registro',idgeo 'Codigo Predio', FN_CATALOGODESC(44,estado_v) Estado,FN_CATALOGODESC(5,motivo_estado) Motivo,nombre Creó,U.perfil Perfil,fecha_create 'Fecha de Creación'
	FROM geo_gest A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
$sql.="WHERE estado_v!=1 AND idgeo='".$id[0];
$sql.="' ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"predios",$regxPag);
   }

/*function get_predio(){
	//  print_r($_POST);
	$id=divide($_POST['id']);
	$sql="SELECT G.idgeo,G.zona, G.localidad, G.upz, G.barrio, G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.direccion, G.vereda, G.cordx, G.cordy, G.territorio 
 	FROM `geo_asig` A 
  	LEFT JOIN hog_geo G ON A.idgeo=G.idgeo 
  	LEFT JOIN apro_terr AP ON G.territorio = AP.territorio
   	WHERE A.estado='A' AND A.idgeo ='".$id[0]."'"; 
	$perfil=perfil1($_SESSION['us_sds']);
	if($perfil!='ADM'){
		$sql.=" AND AP.doc_asignado ='".$_SESSION['us_sds']."'";	
	}
   	// $sql.=" AND AP.doc_asignado ='".$_SESSION['us_sds']."'";
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
return json_encode($info['responseResult'][0]);
}*/

function get_predio(){
	$id = divide($_POST['id']);
	$sql = "SELECT G.idgeo, G.zona, G.localidad, G.upz, G.barrio, G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit, G.direccion, G.vereda, G.cordx, G.cordy, G.territorio 
	FROM `geo_asig` A 
	LEFT JOIN hog_geo G ON A.idgeo = G.idgeo";
	$perfil = perfil1($_SESSION['us_sds']);
	/* if ($perfil == 'AUXHOG') {
		$sql .= " LEFT JOIN apro_terr AP ON G.territorio = AP.territorio";
	} */
	$sql .= " WHERE A.estado = 'A' AND A.idgeo = '" . $id[0] . "'";
	/* if ($perfil == 'AUXHOG') {
		$sql .= " AND AP.doc_asignado = '" . $_SESSION['us_sds'] . "'";
	} */
	$info = datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode(new stdClass);
	}
	return json_encode($info['responseResult'][0]);
}

function opc_zona($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=3 and estado='A' ORDER BY 1",$id);
}

function opc_upz($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=7 and estado='A' ORDER BY 1",$id);
}
function opc_barrio($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=20 and estado='A' ORDER BY 1",$id);
}
function opc_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=44 and estado='A' and idcatadeta!=1 ORDER BY 1",$id);
}
function opc_motivo_estado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=5 and estado='A' ORDER BY 1",$id);
}
function opc_territorio($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=283 and estado='A' ORDER BY 1",$id);
}



function opc_rol($id=''){
	return opc_sql("SELECT distinct perfil,perfil FROM `usuarios` WHERE  subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') AND componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A' ORDER BY 1",$id);
}
function opc_asignado($id=''){
	return opc_sql("SELECT id_usuario,nombre FROM `usuarios` WHERE  subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') AND componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') AND estado='A' ORDER BY 1",$id);
}
function opc_rolasignado(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT id_usuario,CONCAT(id_usuario,'-',nombre) FROM usuarios WHERE subred in(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and componente IN(SELECT componente FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}')  AND estado='A' and perfil='".$id[0]."' ORDER BY 1 ASC";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}

function opc_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}

function opc_localidad($id=''){
	return opc_sql("SELECT `idcatadeta`,CONCAT(idcatadeta,'-',descripcion) FROM `catadeta` WHERE idcatalogo=2 ORDER BY cast(idcatadeta as signed)",$id);
}


function gra_geoloc(){
	$id=divide($_POST['cod_pre']);
	$sql = "INSERT INTO geo_gest VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$params = array(
	  array('type' => 'i', 'value' => NULL),
	  array('type' => 's', 'value' => $id[0]),
	  array('type' => 's', 'value' => $_POST['direccion_nueva']),
	  array('type' => 's', 'value' => $_POST['vereda_nueva']),
	  array('type' => 's', 'value' => $_POST['cordxn']),
	  array('type' => 's', 'value' => $_POST['cordyn']),
	  array('type' => 's', 'value' => $_POST['estado_v']),
	  array('type' => 's', 'value' => $_POST['motivo_estado']),
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
	if ($a=='geoloc' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono admsi1' title='Información de la Facturación' id='".$c['ACCIONES']."' Onclick=\"mostrar('geoloc','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
		$rta.="<li class='icono crear' title='Nueva Admisión' id='".$c['ACCIONES']."' Onclick=\"newAdmin('{$c['ACCIONES']}');\"></li>";
	}
	if ($a=='adm-lis' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono editar ' title='Editar ' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'geoloc',event,this,'lib.php');Color('adm-lis');\"></li>";  //act_lista(f,this);
	}
	
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
