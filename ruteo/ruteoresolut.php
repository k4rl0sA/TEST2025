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
 $t=['id_ruteo'=>'','predio'=>'','famili'=>'','usuario'=>'','cod_admin'=>''];
 $w='ruteresol';
 $d=get_ruteresol(); 
 if ($d=="") {$d=$t;}
 $u=($d['predio']== NULL || $d['predio']== 0)?true:false;
//  var_dump($d);
 $o='gesres';
 $c[]=new cmp($o,'e',null,'PROCESO GESTIÓN RESOLUTIVA',$w);
 $c[]=new cmp('id','h','20',$d['id_ruteo'],$w.' '.$o,'','',null,null,true,$u,'','col-1');
 $c[]=new cmp('estado','s',3,$d['predio'],$w.' PuE '.$o,'estado','estado',null,null,true,$u,'','col-2',"changeSelect('estado','famili');enabDepeInner('estado','StG',['RECHAZADA','FALLIDO','NO RESIDENCIAL']);");
 $c[]=new cmp('famili','s',3,$d['famili'],$w.' PuE StG '.$o,'famili','famili',null,'',true, $u,'','col-15',"changeSelect('famili','usuario');");//N° FAMILIA
 $c[]=new cmp('usuario','s',3,$d['usuario'],$w.' PuE StG '.$o,'usuario','usuario',null,'',true,$u,'','col-25',"changeSelect('usuario','cod_admin');"); //TIPO_DOC,DOCUMENTO Y NOMBRE USUARIO
 $c[]=new cmp('cod_admin','s',3,$d['cod_admin'],$w.' PuE StG '.$o,'cod_admin','cod_admin',null,'',true,$u,'','col-4');//traer los codigos del usuario de atencion
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 return $rta;
}

function opc_idgeo($a){
	$id=divide($a);
	$sql="SELECT concat_ws('_',sector_catastral,nummanzana,predio_num,unidad_habit) cod
		FROM `eac_ruteo` WHERE id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		$cod= $info['responseResult'][0]['cod'];
	return $cod;
		 /* return	opc_sql("SELECT CONCAT_WS('_',idgeo,estado_v),FN_CATALOGODESC(44,estado_v)
			from hog_geo where 
			sector_catastral='$co[0]' AND nummanzana='$co[1]' AND predio_num='$co[2]' AND unidad_habit='$co[3]' AND estado_v>3",$id);  */
}

function opc_cod_predio($co=''){
	$sql="SELECT idgeo id ,FN_CATALOGODESC(44,estado_v) estado from hog_geo where idgeo=$co";
	$info=datos_mysql($sql);
	$cod= $info['responseResult'][0]['id'];
	return $cod;
}

function opc_estado($id=''){
	$cod=opc_idgeo($_REQUEST['id']);
		$co=divide($cod);
		// $cod=opc_cod_predio()
		// var_dump($_REQUEST['predio']);
		return	opc_sql("SELECT idgeo,FN_CATALOGODESC(44,estado_v) from hog_geo where sector_catastral='$co[0]' AND nummanzana='$co[1]' AND predio_num='$co[2]' AND unidad_habit='$co[3]' AND estado_v>3 and estrategia=2",$id);
}

function opc_estadofamili(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idviv 'id',concat(idviv,' - ','FAMILIA ',numfam) FROM hog_viv hv where idpre={$id[0]}";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 
}

function opc_famili($id=''){
	// var_dump($id);
	if ($id==''){

	}else{
		return opc_sql("SELECT idviv 'id',concat(idviv,' - ','FAMILIA ',numfam) FROM hog_viv hv where idviv='$id'", $id);
	}
}
function opc_usuario($id=''){
	// var_dump($id);
	if ($id==''){

	}else{
		$co=divide($id);
		// var_dump($sql);
		return opc_sql("SELECT CONCAT_WS('_',tipo_doc,idpersona),CONCAT_WS('-',idpersona,tipo_doc,CONCAT_WS(' ',nombre1,apellido1)) FROM personas p WHERE tipo_doc='$co[0]' AND p.idpersona='$co[1]'", $id);
	}
	// return opc_sql("SELECT CONCAT_WS('_',tipo_doc,idpersona),CONCAT_WS('-',idpersona,tipo_doc,CONCAT_WS(' ',nombre1,apellido1)) FROM personas p WHERE vivipersona={$id} ORDER BY 1", $id);
}





function opc_cod_admin($id=''){
	// var_dump($id);
	if ($id==''){

	}else{
		$co=divide($id);
		// var_dump($sql);
		return opc_sql("SELECT f.cod_admin cod,concat_ws('_',cod_admin,FN_CATALOGODESC(127,final_consul)) FROM adm_facturacion f WHERE cod_admin='$id'", $id);
	}
	//return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=0 AND estado='A' ORDER BY 1", $id);
}


function get_ruteresol(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		// var_dump($id);
		$sql="SELECT `id_ruteo`,predio,famili,usuario,cod_admin
		 FROM `eac_ruteo` WHERE  id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
    	// var_dump($info['responseResult'][0]);
		return $info['responseResult'][0];
	} 
}

 
function gra_ruteresol(){
$sql="UPDATE `eac_ruteo` SET 
famili=TRIM(UPPER('{$_POST['famili']}')),
usuario=TRIM(UPPER('{$_POST['usuario']}')),
`predio`=TRIM(UPPER('{$_POST['estado']}')),
`cod_admin`=TRIM(UPPER('{$_POST['cod_admin']}')),
`usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),
`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR),
estado='G'
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
