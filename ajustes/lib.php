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


function lis_ajustar(){
	// IF(A.tipodoc_old = '',IF(A.documento_old = '',A.fecha_old,A.documento_old),A.tipodoc_old) AS Anterior,IF(A.tipo_doc_new = '',IF(A.documento_new = '',A.fecha_new,A.documento_new),A.tipo_doc_new) AS Nuevo,
	$info=datos_mysql("SELECT COUNT(*) total from ajustes A LEFT JOIN personas P ON A.usu_creo = P.idpersona where 1 ".whe_ajustar());
	$total=$info['responseResult'][0]['total'];
	$regxPag=12;
	$pag=(isset($_POST['pag-ajustar']))? ($_POST['pag-ajustar']-1)* $regxPag:0;
	$sql="SELECT A.cod_pred predio,A.cod_fam Familia,FN_CATALOGODESC(302,A.accion) Accion,FN_CATALOGODESC(213,A.formulario) Formulario,
	IF(A.accion=2,A.documento_old,IF(A.cod_delete = '',A.cod_traslada,A.cod_delete)) AS Codigo,
	FN_CATALOGODESC(303,A.cmp_editar) Campo,
	IF(A.accion=2,IF(A.cmp_editar=1,A.tipodoc_old,IF(A.cmp_editar=2,A.documento_old,IF(A.cmp_editar=3,A.fecha_old,A.sexo_old))),'') Antes,
	IF(A.accion=2,IF(A.cmp_editar=1,A.tipo_doc_new,IF(A.cmp_editar=2,A.documento_new,IF(A.cmp_editar=3,A.fecha_new,A.sexo_new))),'') Nuevo,
	U.nombre Creo,respuesta,fecha_create Fecha
	FROM ajustes A 
	LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
	WHERE 1 ";
	$sql.=whe_ajustar();
	$sql.=" ORDER BY A.fecha_create DESC";
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"ajustar",$regxPag);	
}

function whe_ajustar() {
	$sql = "";
	if ($_POST['fpredio'])
		$sql .= " AND A.cod_pred like '%".$_POST['fpredio']."%'";
	if ($_POST['facci'])
		$sql .= " AND A.accion= '".$_POST['facci']."'";
	if ($_POST['fdigita'])
		$sql .= " AND A.usu_creo='".$_POST['fdigita']."'";
	if ($_POST['fcod'])
		$sql .= " AND A.documento_old= '".$_POST['fcod']."' OR A.cod_delete = '".$_POST['fcod']."' OR cod_traslada='".$_POST['fcod']."'";
	return $sql;
}

function cmp_ajustar(){
	$rta="";
	$t=['id_ajuste'=>'','cod_pred'=>'','cod_fam'=>'','cod_individuo'=>'','formulario'=>'','accion'=>'','cod_delete'=>'','tipo_doc_new'=>'','documento_new'=>'','fecha_new'=>'','sexo_new'=>'','respuesta'=>'','cod_traslada'=>'','cmp_editar'=>'']; 
	$w='ajustar';
	$d=get_ajustar(); 
	if ($d=="") {$d=$t;}
	$u = true ;
	$o='datos';
    $k='tOL';
    $l='AcT';
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);

	
	$c[]=new cmp('cod_pred','t',18,$d['cod_pred'],$w.' '.$o,'Codigo del predio','cod_pred',null,null,true,true,'','col-15',"changeSelect('cod_pred','cod_fam');");
	$c[]=new cmp('cod_fam','s',25,$d['cod_fam'],$w.' '.$o,'Codigo de la Familia','cod_fam',null,null,true,true,'','col-15',"changeSelect('cod_fam','cod_individuo');");
	$c[]=new cmp('cod_individuo','s',25,$d['cod_individuo'],$w.' '.$o,'N° Identificación del usuario','cod_individuo',null,null,true,true,'','col-3');
	$c[]=new cmp('accion','s',3,$d['accion'],$w.' '.$o,'Accion','accion',null,null,true,true,'','col-2',"enClSe('accion', 'tOL', [['DEl'], ['upD'], ['Tra']]);valSelDep('accion',2,'INt',6);");
	$c[]=new cmp('formulario','s',3,$d['formulario'],$w.' '.$k.' DEl upD INt '.$o,'Formulario','formulario',null,null,true,true,'','col-2');
	$c[]=new cmp('cod_delete','n',18,$d['cod_delete'],$w.' '.$k.' DEl '.$o,'Cod para Eliminar','cod_delete',null,null,false,false,'','col-2');
	$c[]=new cmp('cod_traslada','n',18,$d['cod_traslada'],$w.' '.$k.' Tra '.$o,'Cod de la Familia','cod_delete',null,null,false,false,'','col-2');
	$c[]=new cmp('cmp_editar','s',3,$d['cmp_editar'],$w.' '.$k.' upD '.$o,'Campo a editar','cmp_editar',null,null,false,false,'','col-2',"enClSe('cmp_editar', 'AcT', [['TpD'], ['DoC'], ['FEc'],['sEX']]);");

	$c[]=new cmp($o,'e',null,'INFORMACIÓN PARA EDITAR',$w);
	$c[]=new cmp('tipo_doc_new','s',3,$d['tipo_doc_new'],$w.' '.$k.' '.$l.' TpD '.$o,'Tipo de Documento','tipo_doc_new',null,null,false,false,'','col-2');
	$c[]=new cmp('documento_new','t',18,$d['documento_new'],$w.' '.$k.' '.$l.' DoC '.$o,'N° Identificación del usuario','documento_new',null,null,false,false,'','col-2');
	$c[]=new cmp('fecha_new','d',10,$d['fecha_new'],$w.' '.$k.' '.$l.' FEc '.$o,'Fecha de Nacimiento (Unicamente)','fecha_new',null,null,false,false,'','col-2');
	$c[]=new cmp('sexo_new','s',3,$d['sexo_new'],$w.' '.$k.' '.$l.' sEX '.$o,'Sexo','sexo_new',null,null,false,false,'','col-2');
	


	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
	return $rta;
   }

   function get_ajustar(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT idoms,O.`idpersona`,O.`tipodoc`,
		diabetes,fuma,tas,puntaje,descripcion,
		O.estado,P.idpersona,P.tipo_doc,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,sexo,P.fecha_nacimiento fechanacimiento,YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
		FROM `ajustes` O
		LEFT JOIN personas P ON O.idpersona = P.idpersona and O.tipodoc=P.tipo_doc
		WHERE O.idpersona ='{$id[0]}' AND O.tipodoc='{$id[1]}'";
		// echo $sql;
		$info=datos_mysql($sql);
				return $info['responseResult'][0];
		}
	} 


/* function get_person(){
	// print_r($_POST);
	$id=divide($_POST['id']);
$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,sexo ,fecha_nacimiento,YEAR(CURDATE())-YEAR(fecha_nacimiento) edad
from personas
WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."');";
	
	// return json_encode($sql);
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
return json_encode($info['responseResult'][0]);
}
 */
function focus_ajustar(){
	return 'ajustar';
   }
   
function men_ajustar(){
	$rta=cap_menus('ajustar','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='ajustar') {  
		$rta .= "<li class='icono $a  grabar' title='Grabar' Onclick=\"grabar('$a',this);\" ></li>";
	}
	return $rta;
  }
   
function gra_ajustar(){
	if($_POST['id']==0){
			// echo "ES MENOR DE EDAD ".$ed.' '.print_r($_POST);
		$sql1="SELECT p.tipo_doc tipodoc_old,p.idpersona documento_old,p.fecha_nacimiento fecha_old,
		p.sexo sexo_old
		FROM ajustes x LEFT JOIN usuarios u ON x.usu_creo=u.id_usuario
		LEFT JOIN personas p ON x.cod_individuo=p.idpeople
		where x.cod_individuo='{$_POST['cod_individuo']}'";
		$info=datos_mysql($sql1);

		$cod_pred = cleanTxt($_POST['cod_pred']);
		$cod_fam = cleanTxt($_POST['cod_fam']);
		$cod_individuo = cleanTxt($_POST['cod_individuo']);
		$formulario = cleanTxt($_POST['formulario']);
		$accion = cleanTxt($_POST['accion']);
		$cod_delete = cleanTxt($_POST['cod_delete']);
		$cod_traslada = cleanTxt($_POST['cod_traslada']);
		$cmp_editar = cleanTxt($_POST['cmp_editar']);
		$tipo_doc_new = cleanTxt($_POST['tipo_doc_new']);
		$documento_new = cleanTxt($_POST['documento_new']);
		$fecha_new = cleanTxt($_POST['fecha_new']);
		$sexo_new = cleanTxt($_POST['sexo_new']);

		var_dump($info['responseResult']);
		$doc_old=$info['responseResult'][0]['documento_old'];
		$tipodoc_old=$info['responseResult'][0]['tipodoc_old'];
		$fecha_old=$info['responseResult'][0]['fecha_old'];
		$sexo_old=$info['responseResult'][0]['sexo_old'];

		$sql="INSERT INTO ajustes VALUES (null,
		'$cod_pred','$cod_fam','$cod_individuo','$formulario','$accion','$cod_delete','$cod_traslada','$cmp_editar',
		'$tipodoc_old','$tipo_doc_new','$doc_old','$documento_new','$fecha_old','$fecha_new','$sexo_old','$sexo_new','',
		'{$_SESSION['us_sds']}',DATE_SUB(NOW(), INTERVAL 5 HOUR),'',NULL,'A');";
		//echo $sql;
		$rta=dato_mysql($sql);
		// print_r($_POST);
		// return 'TAMIZAJE NO APLICA PARA LA EDAD';
	}else{
		/* $id=divide($_POST['id']);
		$sql="UPDATE ajustes SET  
		diabetes=trim(upper('{$_POST['diabetes']}')),fuma=trim(upper('{$_POST['fuma']}')),tas=trim(upper('{$_POST['tas']}')),puntaje=trim(upper('{$_POST['puntaje']}')),descripcion=trim(upper('{$_POST['descripcion']}')),
		usu_update=TRIM(UPPER('{$_SESSION['us_sds']}')),fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR)
		where tipodoc='{$id[0]}' AND idpersona='$id[1]'";
		$rta=dato_mysql($sql); */
	}
  return $rta; 
}

function opc_cod_predcod_fam(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idviv 'id',idviv 'cod' FROM hog_viv hv where idpre={$id[0]} ORDER BY 1";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 
}

function opc_cod_famcod_individuo(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idpeople,CONCAT_WS('-',idpersona,tipo_doc,CONCAT_WS(' ',nombre1,apellido1)) FROM personas p WHERE vivipersona={$id[0]} ORDER BY 1";
		$info=datos_mysql($sql);
		// print_r($sql);
		return json_encode($info['responseResult']);
	} 					
}

	function opc_tipo_doc_new($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}
	function opc_sexo_new($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
	}
	function opc_accion($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=302 and estado='A' ORDER BY 1",$id);
	}
	function opc_cmp_editar($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=303 and estado='A' ORDER BY 1",$id);
	}
	function opc_cod_fam($id=''){
		// return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
	}
	function opc_cod_individuo($id=''){
		// return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
	}
	function opc_formulario($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=213 and estado='A' ORDER BY 2",$id);
	}
	


	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		//    if ($a=='ajustar' && $b=='acciones'){
			// $rta="<nav class='menu right'>";		
				// $rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('ajustar','pro',event,'','lib.php',7,'ajustar');setTimeout(hiddxedad,300,'edad','prufin');\"></li>";  //act_lista(f,this);
			// }
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
	