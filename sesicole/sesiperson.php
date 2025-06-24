<?php
require_once '../libs/gestion.php';
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

function focus_sespers(){
	return 'sespers';
}
   
function men_sespers(){
	$rta=cap_menus('sespers','pro');
	return $rta;
}
   
function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='sespers' && isset($acc['crear']) && $acc['crear']=='SI'){  
		$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
		$rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
	}
	return $rta;
}

   FUNCTION lis_perses(){
	// var_dump($_POST['id']);
  $id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['ids']) ? divide($_POST['ids']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM persescol A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND sesion='".$id[0]."'");  // CAMBIO 
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-perses']))? ($_POST['pag-perses']-1)* $regxPag:0;

//   `id_person` ACCIONES,
	$sql="SELECT  id_person 'Cod Registro',sesion 'Sesion',
A.tipo_doc,A.idpersona,concat_ws(' ',A.nombre1,A.apellido1) Nombre,A.estado,fecha_create 'Fecha de Creación',nombre Creó,perfil  
FROM persescol A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario";
	$sql.=" WHERE A.estado = 'A' AND A.sesion='".$id[0]; // CAMBIO 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"perses",$regxPag,'sesiperson.php');
   }

   
function cmp_sespers(){
	$rta="";
	$t=['idpersona'=>'','tipo_doc'=>'','nombre1'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'','fecha_nacimiento'=>'','sexo'=>'','genero'=>'','etnia'=>'','pueblo'=>'','nacionalidad'=>'','regimen'=>'','eapb'=>''];
	$d=get_sespers();
	if ($d==""){$d=$t;}
	// var_dump($_POST);
	$id=divide($_POST['id']);
    $w="sespers";
	$o='infbas';
	$key='pEr';
	// var_dump($p);
	$days=fechas_app('vivienda');
		
	$o='Sesper';
	$c[]=new cmp($o,'e',null,'IDENTIFICACIÓN DE PERSONAS',$w);
	$c[]=new cmp('ids','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',true,false);
	$c[]=new cmp('idpersona','n',18,'',$w.' '.$key.' '.$o,'Identificación <a href="https://www.adres.gov.co/consulte-su-eps" target="_blank">     Abrir ADRES</a>','idpersona',null,null,true,true,'','col-4');
	$c[]=new cmp('tipo_doc','s',3,'',$w.' '.$key.' '.$o,'Tipo documento','tipo_doc',null,null,true,true,'','col-4',"getDatKey('pEr','personOld','sespers',['tipo_doc'],'sesiperson.php');");
	$c[]=new cmp('nombre1','t',30,'',$w.' '.$o,'Primer Nombre','nombre1',null,null,true,true,'','col-2');
	$c[]=new cmp('nombre2','t',30,'',$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,true,'','col-2');
	$c[]=new cmp('apellido1','t',30,'',$w.' '.$o,'Primer Apellido','apellido1',null,null,true,true,'','col-2');
	$c[]=new cmp('apellido2','t',30,'',$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,true,'','col-2');
	$c[]=new cmp('fecha_nacimiento','d',10,'',$w.' '.$o,'Fecha de nacimiento','fecha_nacimiento',null,null,true,true,'','col-2',"validDate(this,-43800,0);");
	$c[]=new cmp('sexo','s',3,'',$w.' '.$o,'Sexo','sexo',null,null,true,true,'','col-2');
	$c[]=new cmp('genero','s',3,'',$w.' '.$o,'Genero','genero',null,null,true,true,'','col-2');
	$c[]=new cmp('etnia','s',3,'',$w.' '.$o,'Pertenencia Etnica','etnia',null,null,true,true,'','col-2',"enabEtni('etnia','ETn','idi');");
	$c[]=new cmp('pueblo','s',50,'',$w.' ETn cmhi '.$o,'pueblo','pueblo',null,null,false,true,'','col-2');
	$c[]=new cmp('nacionalidad','s',3,'',$w.' '.$o,'nacionalidad','nacionalidad',null,null,true,true,'','col-2');
	$c[]=new cmp('regimen','s',3,'',$w.' '.$o,'regimen','regimen',null,null,true,true,'','col-2',"enabAfil('regimen','eaf');enabEapb('regimen','rgm');");
	$c[]=new cmp('eapb','s',3,'',$w.' rgm '.$o,'eapb','eapb',null,null,true,true,'','col-2');

	// $c[]=new cmp('medico','s',15,$d,$w.' der '.$o,'Asignado','medico',null,null,false,false,'','col-5');
	$rta.="<div class='encabezado'>TABLA USUARIOS DE LA SESIÓN</div><div class='contenido' id='perses-lis'>".lis_perses()."</div></div>";
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_personOld(){
	// print_r($_POST);
	$id=divide($_POST['id']);

		$sql="SELECT idpeople,idpersona,tipo_doc,nombre1,nombre2,apellido1,apellido2,fecha_nacimiento,
		sexo,genero,etnia,pueblo,nacionalidad,regimen,eapb
		FROM `personas` 
   	WHERE idpersona ='".$id[0]."' AND tipo_doc='".$id[1]."'";
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}else{
		return json_encode($info['responseResult'][0]);
	}
} 

function get_sespers(){
	return '';
}

function gra_sespers(){

	// var_dump($_POST);
	$id=divide($_POST['ids']);
	$sql = "INSERT INTO persescol VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
	$pueblo=['type' => ($_POST['pueblo']==='') ? 'z' : 's',	'value' => ($_POST['pueblo'] === '') ? null : $_POST['pueblo']];
	$params =[
	['type' => 'i', 'value' => NULL],//id_person
	['type' => 'i', 'value' => $id[0]],//sesion
	['type' => 'i', 'value' => $_POST['idpersona']],
	['type' => 's', 'value' => $_POST['tipo_doc']],
	['type' => 's', 'value' => $_POST['nombre1']],
	['type' => 's', 'value' => $_POST['nombre2']],
	['type' => 's', 'value' => $_POST['apellido1']],
	['type' => 's', 'value' => $_POST['apellido2']],
	['type' => 's', 'value' => $_POST['fecha_nacimiento']],
	['type' => 's', 'value' => $_POST['sexo']],
	['type' => 'i', 'value' => $_POST['genero']],
	['type' => 'i', 'value' => $_POST['etnia']],
	$pueblo,
	['type' => 's', 'value' => $_POST['nacionalidad']],
	['type' => 'i', 'value' => $_POST['regimen']],
	['type' => 'i', 'value' => $_POST['eapb']],
	['type' => 'i', 'value' => $_SESSION['us_sds']],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => 'A']
	];
	return show_sql($sql, $params);
	//return  $rta= mysql_prepd($sql, $params);
}

function get_person(){
	//  print_r($_REQUEST);
	 $id=divide($_REQUEST['id']);
	if($_REQUEST['id']=='' || count($id)!=2){
		return "";
	}else{
		$sql="SELECT concat_ws('_',idpeople,vivipersona),idpersona,tipo_doc,nombre1,nombre2,
		apellido1,apellido2,fecha_nacimiento,sexo,genero,etnia,pueblo,nacionalidad,regimen,eapb
		FROM `person`
		WHERE idpeople ='{$id[0]}'" ;
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}


function opc_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 2",$id);
}
function opc_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_etnia($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=16 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_regimen($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=17 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_eapb($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=19 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_pueblo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=15 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}

function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);			
	$rta=$c[$d];
   // $rta=iconv('U	TF-8','ISO-8859-1',$rta);
   // var_dump($c);
	   if ($a=='persescol' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
    //$rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'apopsicduel',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado'],'apopsicduel.php');\"></li>";
    // $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'apopsicduel',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/apopsicduel.php');\"></li>"; //CAMBIO tener en cuenta el evento
  }
	return $rta;
}

function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
   }
