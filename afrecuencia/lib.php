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

function whe_frecuenciauso() {
	$sql = "";
	if ($_POST['fidpersona'])
		$sql .= " AND idpersona like '%".$_POST['fidpersona']."%'";
	if ($_POST['fdigita'])
		$sql .= " AND A.usu_creo ='".$_POST['fdigita']."' ";
	if ($_POST['festado']){
		$sql .= " AND A.estado = '".$_POST['festado']."' ";
	}else{
		$sql .= " AND A.realizada = 'NO' ";
	}
	if ($_POST['fdes']) {
		if ($_POST['fhas']) {
			$sql .= " AND A.fecha_create >='".$_POST['fdes']." 00:00:00' AND A.fecha_create <='".$_POST['fhas']." 23:59:59'";
		} else {
			$sql .= " AND A.fecha_create >='".$_POST['fdes']." 00:00:00' AND A.fecha_create <='". $_POST['fdes']." 23:59:59'";
		}
	}
	return $sql;
}

function lis_frecuenciauso(){
	$info=datos_mysql("SELECT COUNT(*) total FROM `frecuenciauso` A LEFT JOIN person P ON A.idpeople=P.idpeople left JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') ".whe_frecuenciauso());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-frecuenciauso']))? ($_POST['pag-frecuenciauso']-1)* $regxPag:0;
$sql="SELECT idfrecuencia ACCIONES,
`idpersona` Documento,FN_CATALOGODESC(1,tipo_doc) Tipo_Documento,FN_CATALOGODESC(274,`punto_atencion`) 'Punto de Control',FN_CATALOGODESC(275,tipo_cita) 'Tipo Cita',`realizada`,A.fecha_create,A.estado
from frecuenciauso A 
LEFT JOIN person P ON A.idpeople=P.idpeople 
left JOIN usuarios U ON A.usu_creo = U.id_usuario 
WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') ";
	$sql.=whe_frecuenciauso();
	$sql.="  ORDER BY 7 DESC";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"frecuenciauso",$regxPag);
}

function focus_frecuenciauso(){
 return 'frecuenciauso';
}

function men_frecuenciauso(){
 $rta=cap_menus('frecuenciauso','pro');
 return $rta;
}

function get_frecuenciauso(){
	if ($_POST['id']){
		// var_dump($_POST);
		$id=divide($_POST['id']);			
	$sql="SELECT T1.fecha_create,T2.idpersona id_persona,T2.tipo_doc,T2.nombre1,T2.nombre2,T2.apellido1,T2.apellido2,T2.fecha_nacimiento,T2.sexo genero,
	concat('AÑOS =',TIMESTAMPDIFF(YEAR, T2.fecha_nacimiento, CURDATE()),' Meses =',TIMESTAMPDIFF(MONTH, T2.fecha_nacimiento, CURDATE())-(TIMESTAMPDIFF(YEAR, T2.fecha_nacimiento, CURDATE()) * 12),'Dias =',DATEDIFF(CURDATE(),DATE_ADD(T2.fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, T2.fecha_nacimiento, CURDATE()) YEAR)) % 30) edad
	from frecuenciauso T1 
	left join person T2 ON T1.idpeople=T2.idpeople
	WHERE T1.idfrecuencia='{$id[0]}'";
	// echo $sql;
		$info=datos_mysql($sql);
		return $info['responseResult'][0];
	}
}

function get_persona(){
	if ($_REQUEST['id']){
		$id = divide($_REQUEST['id']);
		$sql = "SELECT T1.idpersona, T1.tipo_doc, T1.nombre1, T1.nombre2, T1.apellido1, T1.apellido2, T1.fecha_nacimiento, T1.sexo,
		concat('AÑOS =', TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()), ' Meses =', TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) - (TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) * 12), ' Dias =', DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) YEAR)) % 30) edad
		FROM person T1
		RIGHT JOIN hog_agen T2 ON T1.idpeople = T2.idpeople
		WHERE T1.idpersona = '{$id[0]}' AND T1.tipo_doc = UPPER('{$id[1]}')";
		$info = datos_mysql($sql);
		if (!$info['responseResult']) {
			$sql1 = "SELECT T1.idpersona, T1.tipo_doc, T1.nombre1, T1.nombre2, T1.apellido1, T1.apellido2, T1.fecha_nacimiento, T1.sexo,
			concat('AÑOS =', TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()), ' Meses =', TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) - (TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) * 12), ' Dias =', DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) YEAR)) % 30) edad
			FROM person T1
			WHERE T1.idpersona = '{$id[0]}' AND T1.tipo_doc = UPPER('{$id[1]}')";
			$info = datos_mysql($sql1);
			if (!$info['responseResult']) {
				return json_encode(new stdClass());
			} else {
				return json_encode($info['responseResult'][0]);
			}
		} else {
			return json_encode($info['responseResult'][0]);
		}
	}
	return json_encode(new stdClass());
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='frecuenciauso'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	// $rta .= "<li class='icono $a actualizar'    title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}

function cmp_frecuenciauso(){
 $t=['id_persona'=>'','tipo_doc'=>'','nombre1'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'',
 'fecha_nacimiento'=>'','genero'=>'','fecha'=>'','etnia'=>'','nacionalidad'=>'','tipo_cita'=>'','tel1'=>'','tel2'=>'','fecha_create'=>'','punto_atencion'=>'','edad'=>''];
 $w='frecuencia';
  $d=get_frecuenciauso(); 
  //~ var_dump($d);
 if ($d=="") {$d=$t;}
 $u=($d['id_persona']=='')?true:false;
 $o='percit';
 $key='find';
 $rta=" <span class='mensaje' id='".$w."-msj' ></span>";
 $c[]=new cmp($o,'e',null,'FRECUENCIA DE USO DE USUARIOS',$w);
 $c[]=new cmp('key','h',50,$_POST['id'],$w.' '.$o,'',0,'','','',false,'','col-4');
  //~ $c[]=new cmp('ipe','h',10,$_POST['id'],$w,'','idp',null,'','',''); 
 $c[]=new cmp('idp','nu',99999999999999999,$d['id_persona'],$w.' '.$key.' '.$o,'N° Identificación',0,'rgxdfnum','#################',true,$u,'','col-4');
  $c[]=new cmp('tdo','s',3,$d['tipo_doc'],$w.' '.$key.' '.$o,'Tipo Documento','tipo_doc',null,null,true,$u,'','col-3',"getDatForm('find','persona','percit');setTimeout(custSeleDepend,1000,'tdo', 'cit', 'lib.php', {});");//'id_persona': 'idp'
 $c[]=new cmp('no1','t',20,$d['nombre1'],$w.' '.$o,'Primer Nombre','nombre1',null,null,false,false,'','col-3');
 $c[]=new cmp('no2','t',20,$d['nombre2'],$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,false,'','col-4');
 $c[]=new cmp('ap1','t',20,$d['apellido1'],$w.' '.$o,'Primer Apellido','apellido1',null,null,false,false,'','col-3');
 $c[]=new cmp('ap2','t',20,$d['apellido2'],$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,false,'','col-3');
 $c[]=new cmp('fen','d',10,$d['fecha_nacimiento'],$w.' '.$o,'Fecha de Nacimiento','fecha_nacimiento',null,null,false,false,'','col-4');
 $c[]=new cmp('gen','s',3,$d['genero'],$w.' '.$o,'Sexo','genero',null,null,false,false,'','col-3');
 $c[]=new cmp('edad','t',30,$d['edad'],$w.' '.$o,'edad en Años','edad',null,'',true,false,'','col-3');
//$c[]=new cmp('te1','t',10,$d['tel1'],$w.' '.$o,'Teléfono 1','eapb',null,null,true,false,'','col-3');
//$c[]=new cmp('te2','t',10,$d['tel2'],$w.' '.$o,'Teléfono 2','etnia',null,null,true,false,'','col-4');
//$c[]=new cmp('fec','d',10,$d['fecha'],$w.' '.$o,'Fecha de Caracterización','fecha',null,null,false,false,'','col-3');
 $c[]=new cmp('pun','s',3,'',$w.' '.$o,'Punto de Atención','punto_atenc',null,null,true,true,'','col-3');
 $c[]=new cmp('cit','s',3,'',$w.' '.$o,'Tipo de Cita','tipo_cita',null,null,true,true,'','col-7');
 for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
 $rta.="<div id='tblConsulta'>".lis_citasUsuario()."</div>";
//  $rta.="<div class='campo frecuencia percit col-10'><center><button style='background-color:#65cc67;border-radius:12px;color:white;padding:8px;text-align:center;cursor:pointer;' type='button' Onclick=\"grabar('frecuencia',this);\">Guardar</button></center></div>";
 return $rta;
}

function lis_citasUsuario(){
	 $id=divide($_POST['id']);
	$sql="SELECT idfrecuencia Cod_Registro,p.idpersona Documento,p.tipo_doc 'Tipo Documento',FN_CATALOGODESC(275,tipo_cita) `tipo de cita`
	FROM `frecuenciauso` f left join person p ON f.idpeople=p.idpeople 
	WHERE f.idfrecuencia='{$id[0]}' AND `realizada`='NO'";
// echo $sql;
	$datos=datos_mysql($sql);
return panel_content($datos["responseResult"],"citasUsuario",9);
}
function opc_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}

function opc_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_punto_atenc($id=''){
	return opc_sql("SELECT `idcatadeta`,concat(idcatadeta,' - ',descripcion) FROM `catadeta` WHERE idcatalogo=274 and estado='A'  AND (valor=(select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') OR valor=0) ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
}
function opc_tipo_cita($id=''){
	return opc_sql("SELECT `idcatadeta`,concat(idcatadeta,' - ',descripcion) FROM `catadeta` WHERE idcatalogo=275 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);	
}
function get_person($a,$b){
	$sql="SELECT P.sexo sexo,TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS anos
			  FROM person P
	WHERE P.idpersona='{$a}' and tipo_doc='{$b}'"; 
	// echo $sql;
	// print_r($_REQUEST);
	$info=datos_mysql($sql);
	// var_dump($sql);
	return ($info['responseResult'][0] ?? null);

}
function opc_tdocit($id=''){
	// var_dump($_REQUEST);
	$id = divide($_REQUEST['id']);
	$persona = get_person($id[0],$id[1]);
	$edad = $persona['anos'];
	$sexo = $persona['sexo'];
	$edad_categoria = [
		[0, 5, 1],
		[6, 11, 2],
		[12, 17, 3],
		[21, 26, 5],
		[29, 59, 4],
		[60, 999, 6]
	];
	$categorias = [];
	foreach ($edad_categoria as [$min, $max, $cat_id]) {
		if ($edad >= $min && $edad <= $max) {
			$categorias[] = $cat_id;
			break;
		}
	}
	//aplica para ambos sexos
	if ($edad >= 0 && $edad <6){
		$categorias[] = 35;
		$categorias[] = 36;
	  }
		if ($edad >= 0 && $edad <18) $categorias[] = 24;
		if ($edad >= 50)$categorias[] = 16;
		if ($edad > 18)$categorias[] = 20;
	  if ($edad >=10 && $edad <= 59)$categorias[] = 11;
		
	  if ($sexo === 'M') { // Mujer
		if ($edad >= 25 && $edad <= 69) $categorias[] = 14;
		if ($edad >= 50 && $edad <= 69) $categorias[] = 12;
		if ($edad >= 10 && $edad <= 59) {
		  $categorias[] = 7;
		  $categorias[] = 8;
		  $categorias[] = 19;
		  $categorias[] = 30;
		}
	  } elseif ($sexo === 'H') { // Hombre
		if ($edad >= 50 && $edad <= 75) $categorias[] = 13;
	  }
		
		$categorias_comunes = [9,10,15,17,18,21,22,23,25,26,27,28,29,31,32,33,34,37,38,39,40,41,42,43,44];
	$categorias = array_unique(array_merge($categorias, $categorias_comunes));
	$lista = implode(',', $categorias);
	$sql = "SELECT idcatadeta, descripcion
			FROM catadeta
			WHERE idcatalogo = 275
			  AND idcatadeta IN ($lista)
			  AND estado = 'A'
			ORDER BY LENGTH(idcatadeta), idcatadeta";
	$info = datos_mysql($sql);
	//  return $sql;
	return json_encode($info['responseResult']);
}
function opc_estados($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=11 and estado='A' ORDER BY 1",$id);
}
function opc_upzbar(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idcatadeta 'id',CONCAT(idcatadeta,'-',descripcion) 'desc' FROM `catadeta` WHERE idcatalogo=20 and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}



function gra_frecuenciauso(){
 if ($_POST['key']){
	// var_dump($_POST);
	 $id=divide($_POST['key']);
	$sql="UPDATE frecuenciauso SET punto_atencion='{$_POST['pun']}', tipo_cita='{$_POST['cit']}',usu_update='".$_SESSION['us_sds']."',fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
 WHERE idfrecuencia={$id[0]}";
 }else{
	$sql="SELECT idpeople from person where idpersona='".$_POST['idp']."' AND tipo_doc='".$_POST['tdo']."'";
	$id=datos_mysql($sql);
	$id=$id['responseResult'][0]['idpeople'];
	 $sql="INSERT INTO frecuenciauso VALUES (NULL,{$id},'{$_POST['pun']}','{$_POST['cit']}','NO','{$_SESSION['us_sds']}',DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A');";
 }
	//~ echo $sql;
	$rta=dato_mysql($sql);
return $rta;
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
  if ($a=='frecuenciauso'&& $b=='id'){$rta= "<div class='txt-center'>".$c['ID']."</div>";}
  //~ var_dump($c);
 if (($a=='frecuenciauso') && ($b=='acciones'))    {
		$rta="<nav class='menu right'>";
	if ($c['realizada']=='NO'){
		$rta.="<li class='icon editar min' title='Editar Frecuencia' id='".$c['ACCIONES']."' Onclick=\"mostrar('frecuenciauso','pro',event,'','lib.php',4);hideMotiv();loadMotiv();\"></li>"; //hideMotiv();
	}
		$rta.="</nav>";
	}    
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
