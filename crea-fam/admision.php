<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
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



function focus_admision(){
	return 'admision';
   }
   
   
   function men_admision(){
	$rta=cap_menus('admision','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = "";
	 $acc=rol($a);
	 if ($a=='admision' && isset($acc['crear']) && $acc['crear']=='SI') {  
	 	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 return $rta;
	 }
   }

   function lis_admision(){
	// var_dump($_POST);
		// echo $sql;
	$id=divide($_POST['id']);
	// id_factura ACCIONES,
	$sql="SELECT `cod_admin` 'Codigo Ingreso', FN_CATALOGODESC(126,cod_cups) 'Codigo CUPS', FN_CATALOGODESC(127,final_consul) 'Finalidad de la Consulta' 
	FROM `adm_facturacion` WHERE idpeople ='{$id[0]}'";
	 //echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"admision-lis",10);
   }

function cmp_admision(){
	$rta="<div class='encabezado admisiones'>TABLA ADMISION</div>
	<div class='contenido' id='admision-lis'>".lis_admision()."</div></div>";
	$hoy=date('Y-m-d');
	$t=['idpersona'=>'','tipo_doc'=>'','nombres'=>'','fecha_nacimiento'=>'','sexo'=>'','genero'=>'','nacionalidad'=>''];
	$d=get_personas();
	if ($d==""){$d=$t;}
	$e="";
	$w='admision';
	$o='infusu';
	// var_dump($d);
	$perfil=datos_mysql("select FN_PERFIL({$_SESSION['us_sds']})");
	$p = ($perfil["responseResult"]!='FAC') ? false : true ;
	$c[]=new cmp($o,'e',null,'INFORMACIÓN DEL USUARIO',$w);
	 
	$c[]=new cmp('id_factura','h',15,$_POST['id'],$w.' '.$o,'id','idg',null,'####',false,false);
	$c[]=new cmp('tipo_doc','t','20',$d['tipo_doc'],$w.' '.$o,'Tipo Documento','atencion_tipo_doc',null,'',false,false,'','col-1');
	$c[]=new cmp('documento','t','20',$d['idpersona'],$w.' '.$o,'N° Identificación','atencion_idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('nombre1','t','20',$d['nombres'],$w.' '.$o,'primer nombres','nombre1',null,'',false,false,'','col-4');
	$c[]=new cmp('fecha_nacimiento','t','20',$d['fecha_nacimiento'],$w.' '.$o,'fecha nacimiento','fecha_nacimiento',null,'',false,false,'','col-15');
	$c[]=new cmp('sexo','s','20',$d['sexo'],$w.' '.$o,'sexo','sexo',null,'',false,false,'','col-15');
	$c[]=new cmp('genero','s','20',$d['genero'],$w.' '.$o,'genero','genero',null,'',false,false,'','col-3');
	$c[]=new cmp('nacionalidad','s','20',$d['nacionalidad'],$w.' '.$o,'Nacionalidad','nacionalidad',null,'',false,false,'','col-2'); 
	
 
	$o='admfac';
	$c[]=new cmp($o,'e',null,'ADMISIÓN Y FACTURACIÓN',$w);
	$c[]=new cmp('soli_admis','s','2',$e,$w.' '.$o,'¿Solicita Admisión?','soli_admis',null,null,true,true,'','col-2');
	$c[]=new cmp('fecha_consulta','d',20,$e,$w.' '.$o,'Fecha de la consulta','fecha_consulta',null,'',false,false,'','col-2');
	$c[]=new cmp('tipo_consulta','s',3,$e,$w.' '.$o,'Tipo de Consulta','tipo_consulta',null,'',false,false,'','col-2');
	$c[]=new cmp('cod_cups','s','3',$e,$w.' '.$o,'Codigo CUPS','cod_cups',null,null,false,false,'','col-2');
	$c[]=new cmp('final_consul','s','3',$e,$w.' '.$o,'Finalidad de la Consulta','final_consul',null,null,false,false,'','col-2');
	$c[]=new cmp('cod_admin','n','10',$e,$w.' '.$o,'Codigo Ingreso','cod_admin',null,null,false,$p,'','col-15');
	$c[]=new cmp('cod_factura','n','10',$e,$w.' '.$o,'Codigo de Factura','cod_factura',null,null,false,$p,'','col-15');
	$c[]=new cmp('estado_hist','s','3',$e,$w.' '.$o,'Estado Admision','estado_hist',null,null,false,false,'','col-2');
	 
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}


function get_personas(){
	if($_REQUEST['id']==''){
		return "";
	}else{
		 $id=divide($_REQUEST['id']);
		$sql="SELECT  tipo_doc,idpersona,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,fecha_nacimiento,sexo,genero,nacionalidad
			FROM person 
		WHERE idpeople ='{$id[0]}'";
		//echo $sql;
		$info=datos_mysql($sql);
		return $info['responseResult'][0];			
	} 
   }
function opc_soli_admis($id=''){
	return opc_sql("SELECT `descripcion`,descripcion,valor FROM `catadeta` WHERE idcatalogo=183 and estado='A'  ORDER BY 1 ",$id);
}
   function opc_tipo_consulta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=182 and estado='A'  ORDER BY 1 ",$id);
}
   function opc_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=19 and estado='A' ORDER BY 1",$id);
}
function opc_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY 1",$id);
}
function opc_cod_cups($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=126 and estado='A'  ORDER BY 1 ",$id);
}
function opc_final_consul($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=127 and estado='A'  ORDER BY 1 ",$id);
}
function opc_estado_hist($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=184 and estado='A' ORDER BY 1",$id);
}


/* function gra_admision(){
	// print_r($_POST);
	$id=divide($_POST['id_factura']);
	// var_dump($id);
	if(count($id)==4){
		$rta = "'NO ES POSIBLE ACTUALIZAR EL REGISTRO'";
	//    echo $sql;
	}elseif(count($id)==3){
		// $id=$id[0];
		if(get_admi($id[0])){
			$rta="Error: msj['No puedes realizar otra solicitud, ya fue enviada una al área encargada']";
		}else{
			  $sql="INSERT INTO adm_facturacion VALUES (NULL,$id[0],trim(upper('{$_POST['soli_admis']}')),
			  trim(upper('{$_POST['fecha_consulta']}')), trim(upper('{$_POST['tipo_consulta']}')),trim(upper('{$_POST['cod_admin']}')),trim(upper('{$_POST['cod_cups']}')),trim(upper('{$_POST['final_consul']}')),
			  trim(upper('{$_POST['cod_factura']}')),
			  TRIM(UPPER('{$_POST['estado_hist']}')),
			  TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
			 echo $sql;
			$rta=dato_mysql($sql);
		}
	}else{
		// $id=$id[0];
		if(get_admi($id[0])){
			$rta="Error: msj['No puedes realizar otra solicitud, ya fue enviada una al área encargada']";
		}else{
			$sql="INSERT INTO adm_facturacion VALUES (NULL,$id[0],trim(upper('{$_POST['soli_admis']}')),
		  trim(upper('{$_POST['fecha_consulta']}')), trim(upper('{$_POST['tipo_consulta']}')),trim(upper('{$_POST['cod_admin']}')),trim(upper('{$_POST['cod_cups']}')),trim(upper('{$_POST['final_consul']}')),
		  trim(upper('{$_POST['cod_factura']}')),
		  TRIM(UPPER('{$_POST['estado_hist']}')),
		  TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
		//    echo $sql;
		  $rta=dato_mysql($sql);
	  	}
	}
	 return $rta;
} */

function gra_admision(){
	// print_r($_POST);
	$id=divide($_POST['id_factura']);
	// var_dump($id);
	if(count($id)==4){
		$rta = "'NO ES POSIBLE ACTUALIZAR EL REGISTRO'";
	//    echo $sql;
	}elseif(count($id)==3){
		// $id=$id[0];
		if(get_admi($id[0])){
			$rta="Error: msj['No puedes realizar otra solicitud, ya fue enviada una al área encargada']";
		}else{
			$sql = "INSERT INTO adm_facturacion VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, NULL, 'A')";
			$params = [
				['type' => 'i', 'value' => $id[0]],
				['type' => 's', 'value' => $_POST['soli_admis']],
				['type' => 's', 'value' => ($_POST['fecha_consulta'] === '' ? NULL : $_POST['fecha_consulta'])],
				['type' => 's', 'value' => $_POST['tipo_consulta']],
				['type' => 's', 'value' => $_POST['cod_admin']],
				['type' => 's', 'value' => $_POST['cod_cups']],
				['type' => 's', 'value' => $_POST['final_consul']],
				['type' => 's', 'value' => $_POST['cod_factura']],
				['type' => 's', 'value' => $_POST['estado_hist']],
				['type' => 's', 'value' => $_SESSION['us_sds']]
			];
			$rta = mysql_prepd($sql, $params);
		}
	}else{
		// $id=$id[0];
		if(get_admi($id[0])){
			$rta="Error: msj['No puedes realizar otra solicitud, ya fue enviada una al área encargada']";
		}else{
			$sql = "INSERT INTO adm_facturacion VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, NULL, 'A')";
			$params = [
				['type' => 'i', 'value' => $id[0]],
				['type' => 's', 'value' => $_POST['soli_admis']],
				// ['type' => 's', 'value' => $_POST['fecha_consulta']],
				['type' => 's', 'value' => ($_POST['fecha_consulta'] === '' ? NULL : $_POST['fecha_consulta'])],
				['type' => 's', 'value' => $_POST['tipo_consulta']],
				['type' => 's', 'value' => $_POST['cod_admin']],
				['type' => 's', 'value' => $_POST['cod_cups']],
				['type' => 's', 'value' => $_POST['final_consul']],
				['type' => 's', 'value' => $_POST['cod_factura']],
				['type' => 's', 'value' => $_POST['estado_hist']],
				['type' => 's', 'value' => $_SESSION['us_sds']]
			];
			$rta = mysql_prepd($sql, $params);
	  	}
	}
	 return $rta;
}

function get_admi($id){
	$hoy=date('Y-m-d');
	$sql="select * from adm_facturacion 
	where usu_creo='{$_SESSION['us_sds']}' and DATE(fecha_create)='$hoy' and idpeople='$id'";
	// echo $sql;
	$info=datos_mysql($sql);
	if(isset($info['responseResult'][0])){ 
	  return true;
	}else{
	  return false;
	}
}

function get_admision(){
	if($_REQUEST['id']==''){
		return "";
	}else{
		// print_r($_POST);
		$id=divide($_REQUEST['id']);
		$sql="SELECT concat_ws('_',F.idpeople,P.vivipersona,id_factura) id,
		P.tipo_doc,P.idpersona,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombres,P.fecha_nacimiento,P.sexo,P.genero,P.nacionalidad,
		soli_admis,fecha_consulta,tipo_consulta,cod_cups,final_consul,cod_admin,cod_factura,estado_hist
		FROM `adm_facturacion` F
		LEFT JOIN person P ON F.idpeople=P.idpeople 
		WHERE id_factura='{$id[0]}'";
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
		return json_encode($info['responseResult'][0]);
	} 
}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
		// print_r($c);
		if ($a=='admision-lis' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'admision',event,this,['fecha','tipo_activi'],'../atencion/admision.php');\"></li>";  //   act_lista(f,this);
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }
	   