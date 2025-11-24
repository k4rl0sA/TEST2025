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

function lis_adm(){
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_factura']) ? divide($_POST['id_factura']) : null);
	// $id=divide($_POST['id']);
 	$info=datos_mysql("SELECT  COUNT(DISTINCT concat_WS('_',idpeople,'_',id_factura)) total
	 FROM `adm_facturacion` F WHERE idpeople ='{$id[0]}' and estado!='I'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=3;
	$pag=(isset($_POST['pag-adm']))? ($_POST['pag-adm']-1)* $regxPag:0;	
	$sql="SELECT DISTINCT concat_WS('_',idpeople,id_factura) ACCIONES,`cod_admin` 'Cod. Ingreso', FN_CATALOGODESC(126,cod_cups) 'Cod. CUPS', FN_CATALOGODESC(127,final_consul) 'Consulta'
	FROM `adm_facturacion` F WHERE idpeople ='{$id[0]}' and estado!='I' ";
	$sql.=" ORDER BY F.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	//  echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"adm",$regxPag,'lib.php');
}

function lis_admision(){
	$info=datos_mysql("SELECT COUNT(*) total FROM `adm_facturacion` A 
	LEFT JOIN person P ON A.idpeople=P.idpeople 
	left JOIN usuarios U ON A.usu_creo = U.id_usuario 
	WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}')   AND componente=(select componente from usuarios where id_usuario='{$_SESSION['us_sds']}') AND soli_admis='SI' ".whe_admision());
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
	
	$pag=(isset($_POST['pag-admision']))? ($_POST['pag-admision']-1)* $regxPag:0;
	$sql="SELECT CONCAT_WS('_',P.idpeople,id_factura) ACCIONES, 
	`cod_admin` 'Cod. Ingreso',P.idpersona documento,A.fecha_create AS Fecha_Solicitud,U.nombre Creó,U.perfil Perfil, FN_CATALOGODESC(184,A.estado_hist) Estado 
	FROM `adm_facturacion` A 
	LEFT JOIN person P ON A.idpeople=P.idpeople
	left JOIN usuarios U ON A.usu_creo = U.id_usuario
	WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}')  AND soli_admis='SI' ";//AND componente=(select componente from usuarios where id_usuario='{$_SESSION['us_sds']}') 2025-10-16 OSCAR
	$sql.=whe_admision();
	$sql.=" ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	//  echo $sql;
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"admision",$regxPag);
	}

function whe_admision() {
	$sql = "";
	 if ($_POST['fdocumento'])
		$sql .= " AND P.idpersona = '".$_POST['fdocumento']."'";
	if ($_POST['fcod_admin'])
		$sql .= " AND cod_admin ='".$_POST['fcod_admin']."' ";
	if($_POST['fdigita']) 
	    $sql .= " AND A.usu_creo ='".$_POST['fdigita']."'";
	if (isset($_POST['festado_hist']))
		$estado = ($_POST['festado_hist']==7) ? '' : $_POST['festado_hist'];
		$sql .= " AND estado_hist ='".$estado."'";
	return $sql;
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
  if ($a=='admision'  && isset($acc['crear']) && $acc['crear']=='SI'){  
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
	// $rta .= "<li class='icono $a crear'  title='Actualizar'   id='".print_r($_REQUEST)."'   Onclick=\"\"></li>";
  }
  return $rta;
}

function cmp_admision(){
	$rta="<div class='encabezado adm'>TABLA ADMISION</div>
	<div class='contenido' id='adm-lis'>".lis_adm()."</div></div>";
	$hoy=date('Y-m-d');
	$t=['idpersona'=>'','tipo_doc'=>'','nombre1'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'','fecha_nacimiento'=>'','sexo'=>'','genero'=>'','nacionalidad'=>'','estado_civil'=>'','niveduca'=>'','ocupacion'=>'','regimen'=>'','eapb'=>'','localidad'=>'','barrio'=>'','direccion'=>'','telefono1'=>'','telefono2'=>'','telefono3'=>''];
	$d=get_personas();
	if ($d==""){$d=$t;}
	$e="";
	$w='admision';
	$o='infusu';
	// var_dump($_POST);
	$c[]=new cmp($o,'e',null,'INFORMACIÓN DEL USUARIO',$w);
	$c[]=new cmp('id_factura','h',15,$_POST['id'],$w.' '.$o,'id','idg',null,'####',false,false);
	$c[]=new cmp('tipo_doc','t','20',$d['tipo_doc'],$w.' '.$o,'Tipo Documento','atencion_tipo_doc',null,'',true,false,'','col-5');
	$c[]=new cmp('documento','t','20',$d['idpersona'],$w.' '.$o,'N° Identificación','atencion_idpersona',null,'',true,false,'','col-5');
	$c[]=new cmp('nombre1','t','20',$d['nombre1'],$w.' '.$o,'primer nombres','nombre1',null,'',false,false,'','col-3');
	$c[]=new cmp('nombre2','t','20',$d['nombre2'],$w.' '.$o,'segundo nombres','nombre2',null,'',false,false,'','col-2');
	$c[]=new cmp('apellido1','t','20',$d['apellido1'],$w.' '.$o,'primer apellido','apellido1',null,'',false,false,'','col-3');
	$c[]=new cmp('apellido2','t','20',$d['apellido2'],$w.' '.$o,'segundo apellido','apellido2',null,'',false,false,'','col-2');
	$c[]=new cmp('fecha_nacimiento','t','20',$d['fecha_nacimiento'],$w.' '.$o,'fecha nacimiento','fecha_nacimiento',null,'',false,false,'','col-3');
	$c[]=new cmp('sexo','s','20',$d['sexo'],$w.' '.$o,'sexo','sexo',null,'',false,false,'','col-2');
	$c[]=new cmp('genero','s','20',$d['genero'],$w.' '.$o,'genero','genero',null,'',false,false,'','col-3');
	$c[]=new cmp('nacionalidad','s','20',$d['nacionalidad'],$w.' '.$o,'Nacionalidad','nacionalidad',null,'',false,false,'','col-2');
	$c[]=new cmp('estado_civil','s','3',$d['estado_civil'],$w.' '.$o,'Estado Civil','estado_civil',null,'',false,false,'','col-15');
	$c[]=new cmp('niveduca','s','3',$d['niveduca'],$w.' '.$o,'Nivel Educativo','niveduca',null,'',false,false,'','col-2');
	$c[]=new cmp('ocupacion','s','3',$d['ocupacion'],$w.' '.$o,'Ocupacion','ocupacion',null,'',false,false,'','col-2');
	$c[]=new cmp('regimen','s','20',$d['regimen'],$w.' '.$o,'Regimen','regimen',null,'',false,false,'','col-2');
	$c[]=new cmp('eapb','s','20',$d['eapb'],$w.' '.$o,'EAPB','eapb',null,'',false,false,'','col-25');
	$c[]=new cmp('localidad','t','20',$d['localidad'],$w.' '.$o,'Localidad','localidad',null,'',false,false,'','col-35');
	$c[]=new cmp('barrio','t','20',$d['barrio'],$w.' '.$o,'Barrio','barrio',null,'',false,false,'','col-35');
	$c[]=new cmp('direccion','t','20',$d['direccion'],$w.' '.$o,'Direccion','direccion',null,'',false,false,'','col-3');
 	$c[]=new cmp('telefono1','n','10',$d['telefono1'],$w.' '.$o,'Telefono 1','telefono1',null,'',false,false,'','col-3');
	$c[]=new cmp('telefono2','n','10',$d['telefono2'],$w.' '.$o,'Telefono 2','telefono2',null,'',false,false,'','col-3');
	$c[]=new cmp('telefono3','n','10',$d['telefono3'],$w.' '.$o,'Telefono 3','telefono3',null,'',false,false,'','col-3');
	
	$o='admfac';
	$c[]=new cmp($o,'e',null,'ADMISIÓN Y FACTURACIÓN',$w);
	$c[]=new cmp('fecha_consulta','d',20,$e,$w.' '.$o,'Fecha de la consulta','fecha_consulta',null,'',true,true,'','col-15','validDate(this,-140,0)');
	$c[]=new cmp('tipo_consulta','s',3,$e,$w.' '.$o,'Tipo de Consulta','tipo_consulta',null,'',true,true,'','col-15');
	$c[]=new cmp('cod_cups','s','3',$e,$w.' '.$o,'Codigo CUPS','cod_cups',null,null,true,true,'','col-35');
	$c[]=new cmp('final_consul','s','3',$e,$w.' '.$o,'Finalidad de la Consulta','final_consul',null,null,true,true,'','col-35');
	$c[]=new cmp('cod_admin','n','12',$e,$w.' '.$o,'Codigo ingreso','cod_admin',null,null,true,true,'','col-15');
	$c[]=new cmp('cod_factura','n','12',$e,$w.' '.$o,'Codigo de Factura','cod_factura',null,null,false,true,'','col-15');
	$c[]=new cmp('estado_hist','s','3',$e,$w.' '.$o,'Estado Admision','estado_hist',null,null,true,true,'','col-2');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function new_Admision(){
    
  /*   $sql="INSERT INTO adm_facturacion VALUES (NULL,
	trim(upper('{$id[0]}')),
	trim(upper('{$id[1]}')),
	trim(upper('SI')),'','','','','','','','','',
	TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')"; */

	$id=divide($_REQUEST['id']);
	$sql = "INSERT INTO adm_facturacion VALUES(?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
	$params = [
	['type' => 'i', 'value' => NULL],
	['type' => 's', 'value' => $id[0]],
	['type' => 's', 'value' => 'SI'],
	['type' => 'z', 'value' => NULL],
	['type' => 's', 'value' => ''],
	['type' => 'i', 'value' => NULL],
	['type' => 's', 'value' => ''],
	['type' => 's', 'value' => ''],
	['type' => 's', 'value' => ''],
	['type' => 's', 'value' => ''],
	['type' => 'i', 'value' => $_SESSION['us_sds']],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => 'A']
	];
	return $rta=mysql_prepd($sql, $params);
	/* $rta=dato_mysql($sql);
	//echo $sql;
	return $rta; */
}

function get_personas(){
	if($_REQUEST['id']==''){
		return "";
	}else{
		 $id=divide($_REQUEST['id']);
		//  print_r($id);
		$sql="SELECT P.vivipersona,P.tipo_doc,P.idpersona,P.nombre1,P.nombre2,P.apellido1,P.apellido2,P.fecha_nacimiento,P.sexo,P.genero,P.nacionalidad,P.estado_civil,P.niveduca,P.ocupacion,P.regimen,P.eapb,FN_CATALOGODESC(2,G.localidad) localidad,FN_CATALOGODESC(20,G.barrio) barrio,G.direccion,H.telefono1,H.telefono2,H.telefono3
			FROM adm_facturacion F
			LEFT JOIN person P ON F.idpeople = P.idpeople 
			LEFT JOIN hog_fam H ON P.vivipersona = H.id_fam
			LEFT JOIN hog_geo G ON H.idpre = G.idgeo
			/*LEFT JOIN ( SELECT idgeo AS geo, direccion, localidad, barrio
        			FROM hog_geo ) AS G ON H.idpre = G.idgeo*/
		WHERE  P.idpeople='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
				return '';
			}else{
				return $info['responseResult'][0];
			}
	} 
   }

function get_admision(){
	if($_REQUEST['id']==''){
		return "";
	}else{
		// print_r($_POST);
		$id=divide($_REQUEST['id']);
		// print_r($id);
		$sql="SELECT concat(P.idpersona,'_',P.tipo_doc,'_',P.vivipersona,'_',id_factura) id,
		P.tipo_doc,P.idpersona,P.nombre1,P.nombre2,P.apellido1,P.apellido2,P.fecha_nacimiento,P.sexo,P.genero,P.nacionalidad,P.estado_civil,P.niveduca,P.ocupacion,P.regimen,P.eapb,G.localidad,G.barrio,G.direccion,H.telefono1,H.telefono2,H.telefono3,fecha_consulta,tipo_consulta,
		cod_cups,final_consul,cod_admin,cod_factura,estado_hist
		FROM `adm_facturacion` F
		LEFT JOIN person P ON F.idpeople=P.idpeople 
		LEFT JOIN hog_fam H ON P.vivipersona = H.id_fam
		LEFT JOIN ( SELECT idgeo AS geo, direccion, localidad, barrio
        			FROM hog_geo ) AS G ON H.idpre = G.geo
		WHERE id_factura='{$id[1]}'";
		// echo $sql;
		// print_r($id);
		$info=datos_mysql($sql);
        /*if (!$info['responseResult']) {
				return '';
			}else{
				return json_encode($info['responseResult'][0]);
			}*/
		 return json_encode($info['responseResult'][0]);
	    } 
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
function opc_regimen($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=17 and estado='A' ORDER BY 1",$id);
}
function opc_eapb($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY 1",$id);
}
function opc_estado_civil($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=47 and estado='A' ORDER BY 1",$id);
}
function opc_niveduca($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=180 and estado='A' ORDER BY 1",$id);
}
function opc_ocupacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=175 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_consulta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=182 and estado='A'  ORDER BY 1 ",$id);
}
function opc_cod_cups($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=126 and estado='A'  ORDER BY 1 ",$id);
}
function opc_final_consul($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=127 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta;",$id);
}
function opc_estado_hist($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=184 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_docnew($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
    }



function gra_admision(){
	$rtaF='';
	$id=divide($_POST['id_factura']);
	if(count($id)==4){
		if (isset($_POST['cod_factura']) && $_POST['cod_factura']!='' && isset($_POST['cod_admin'])){
			$estado='F';	
		}else{
			$estado='E';
		}
		$sql = "UPDATE `adm_facturacion` SET fecha_consulta = ?,tipo_consulta = ?,`cod_admin` = ?,`cod_cups` = ?,`final_consul` = ?,`cod_factura` = ?,`estado_hist` = ?,`usu_update` = ?,fecha_update = NOW(),`estado` = ?
            WHERE id_factura = ?";
        $params = [
            ['type' => 's', 'value' => $_POST['fecha_consulta']],
            ['type' => 's', 'value' => $_POST['tipo_consulta']],
            ['type' => 's', 'value' => $_POST['cod_admin']],
            ['type' => 's', 'value' => $_POST['cod_cups']],
            ['type' => 's', 'value' => $_POST['final_consul']],
            ['type' => 's', 'value' => $_POST['cod_factura']],
            ['type' => 's', 'value' => $_POST['estado_hist']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
            ['type' => 's', 'value' => $estado],
            ['type' => 's', 'value' => $id[3]],
        ];
		$rtaF .= mysql_prepd($sql, $params);
	}else if(count($id)==3){
		$rtaF.= "NO HA SELECIONADO LA ADMISION A EDITAR";
	}
	// echo $sql;
  return $rtaF;
}


function fac($id){
	// var_dump($id);
	$id=divide($id);
	$sql="SELECT fecha_consulta fecha
			FROM adm_facturacion F
			WHERE  F.id_factura='{$id[1]}'";
	// echo $sql;
	$info=datos_mysql($sql);
	return $f=$info['responseResult'][0]['fecha'];
	// var_dump($f);
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($c);
	if ($a=='admision' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono admsi1' title='Información de la Facturación' id='".$c['ACCIONES']."' Onclick=\"mostrar('admision','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
		$rta.="<li class='icono crear' title='Nueva Admisión' id='".$c['ACCIONES']."' Onclick=\"newAdmin('{$c['ACCIONES']}');\"></li>";
	}
	if ($a=='adm' && $b=='acciones'){
		$rta="<nav class='menu right'>";
		$fecha = fac($c['ACCIONES']);
		$blo = ($fecha == '0000-00-00' || $fecha == null) ? 'false' : 'true';
		// $cmps ='';
		$rta.="<li class='icono editar ' title='Editar ' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'admision',event,this,'','lib.php');setTimeout(bloqElem,700,['fecha_consulta','tipo_consulta'],$blo);Color('adm-lis');\"></li>";  //act_lista(f,this);
		// $rta.="<li class='icono editar' title='Editar Información de Facturación' id='".$c['ACCIONES']."' Onclick=\"getData('admision','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
	}
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
