<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
// print_r($_POST['a']);
if ($_POST['a']!='opc' && $_POST['tb']!='person') $perf=perfil($_POST['tb']);
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


function lis_tamBarthel(){
	$sql="SELECT ROW_NUMBER() OVER (ORDER BY 1) R,concat(barthel_idpersona,'_',barthel_tipodoc,'_',barthel_momento) ACCIONES,tam_barthel 'Cod Registro',barthel_idpersona Documento,FN_CATALOGODESC(1,barthel_tipodoc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres, 
	FN_CATALOGODESC(21,P.sexo) Sexo,FN_CATALOGODESC(116,barthel_momento) Momento,`barthel_total` Puntaje 
FROM hog_tam_barthel O
LEFT JOIN personas P ON O.barthel_idpersona = P.idpersona
		WHERE '1'='1'";
	$sql.=whe_tamBarthel();
	$sql.=" ORDER BY 1";

	 $sql1="SELECT * 
	  FROM `hog_tam_barthel` WHERE 1";
	$sql1.=whe_tamBarthel();	
	//echo $sql;
		$_SESSION['sql_tamBarthel']=$sql1;
		$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"tamBarthel",20);
}

function lis_barthel(){
	$id=divide($_POST['id']);//id_barthel ACCIONES,
	$sql="SELECT id_barthel 'Cod Registro',momento,interpretacion,total,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_tam_barthel A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"barthel-lis",5);
}

function whe_tamBarthel() {
	$sql = "";
	if ($_POST['fidentificacion'])
		$sql .= " AND barthel_idpersona like '%".$_POST['fidentificacion']."%'";
	if ($_POST['fsexo'])
		$sql .= " AND P.sexo ='".$_POST['fsexo']."' ";
	if ($_POST['fpersona']){
		if($_POST['fpersona'] == '2'){ //mayor de edad
			$sql .= " AND TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) <= 18 ";
		}else{ //menor de edad
			$sql .= " AND TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) > 18 ";
		}
	}
	return $sql;
}

function cmp_tamBarthel(){
	$rta="<div class='encabezado barthel'>TABLA BARTHEL</div><div class='contenido' id='barthel-lis'>".lis_barthel()."</div></div>";
	$t=['tam_barthel'=>'','barthel_tipodoc'=>'','barthel_idpersona'=>'','barthel_total'=>'','barthel_momento'=>'','barthel_edad'=>'','barthel_nombre'=>'','barthel_lugarnacimiento'=>'',
	'barthel_psicologico'=>'','barthel_social'=>'','barthel_manejo'=>'']; 
	
	$w='tamBarthel';
	$d=get_tamBarthel(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$days=fechas_app('PSICOLOGIA');
	$c[]=new cmp('idbarthel','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('barthel_idpersona','n','20',$d['barthel_idpersona'],$w.' '.$o.' '.$key,'N° Identificación','barthel_idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('barthel_tipodoc','s','3',$d['barthel_tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','barthel_tipodoc',null,'',false,false,'','col-2','getDatForm(\'srch\',\'person\',[\'datos\']);');
	$c[]=new cmp('barthel_nombre','t','50',$d['barthel_nombre'],$w.' '.$o,'nombres','barthel_nombre',null,'',false,false,'','col-3');
	$c[]=new cmp('barthel_fechanacimiento','d','10',$d['barthel_fechanacimiento'],$w.' '.$o,'fecha nacimiento','barthel_fechanacimiento',null,'',false,false,'','col-2');
    $c[]=new cmp('barthel_edad','n','3',$d['barthel_edad'],$w.' '.$o,'edad','barthel_edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
   

	$o='identidad';
	$c[]=new cmp($o,'e',null,'ACTIVIDAD ',$w);

	$c[]=new cmp('comer','s',3,'',$w.' '.$o,'Comer ','comer',null,null,true,true,'','col-10');
	$c[]=new cmp('lavarse','s',3,'',$w.' '.$o,'Lavarse ','lavarse',null,null,true,true,'','col-10');
	$c[]=new cmp('vestirse','s',3,'',$w.' '.$o,'Vestirse ','vestirse',null,null,true,true,'','col-10');
	$c[]=new cmp('arreglarse','s',3,'',$w.' '.$o,'Arreglarse ','arreglarse',null,null,true,true,'','col-10');
	$c[]=new cmp('deposicion','s',3,'',$w.' '.$o,'Deposiciones (Según Semana Anterior) ','deposicion',null,null,true,true,'','col-10');
	$c[]=new cmp('miccion','s',3,'',$w.' '.$o,'Micción (Según Semana Anterior) ','miccion',null,null,true,true,'','col-10');
	$c[]=new cmp('sanitario','s',3,'',$w.' '.$o,'Usar El Sanitario ','sanitario',null,null,true,true,'','col-10');
	$c[]=new cmp('trasladarse','s',3,'',$w.' '.$o,'Trasladarse','trasladarse',null,null,true,true,'','col-10');
	$c[]=new cmp('deambular','s',3,'',$w.' '.$o,'Deambular','deambular',null,null,true,true,'','col-10');
	$c[]=new cmp('escalones','s',3,'',$w.' '.$o,'Escalones ','escalones',null,null,true,true,'','col-10');

	$o='totales';
	$c[]=new cmp($o,'e',null,'Resultado ',$w);
	$c[]=new cmp('momento','t',20,'',$w.' '.$o,'Momento','barthel_momento',null,'',false,false,'','col-6');
	$c[]=new cmp('total','t',3,'',$w.' '.$o,'Puntaje','barthel_total',null,'',false,false,'','col-4');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
	return $rta;
   }

   /* function get_tamBarthel(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT `tam_barthel`,`barthel_idpersona`,`barthel_tipodoc`,
		FN_CATALOGODESC(116,barthel_momento) barthel_momento,
		`barthel_comer`,`barthel_lavarse`,`barthel_vestirse`,
		`barthel_arreglarse`,`barthel_deposicion`,`barthel_miccion`,
		`barthel_sanitario`,`barthel_trasladarse`,`barthel_deambular`,
		`barthel_escalones`,`barthel_total`,`barthel_interpretacion`,O.estado,P.idpersona,P.tipo_doc,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) barthel_nombre,P.fecha_nacimiento barthel_fechanacimiento,YEAR(CURDATE())-YEAR(P.fecha_nacimiento) barthel_edad 
		FROM `hog_tam_barthel` O
		LEFT JOIN personas P ON O.barthel_idpersona = P.idpersona and O.barthel_tipodoc=P.tipo_doc
		WHERE barthel_idpersona ='{$id[0]}' AND barthel_tipodoc='{$id[1]}' AND barthel_momento = '{$id[2]}'  ";
		// echo $sql;
		$info=datos_mysql($sql);
				return $info['responseResult'][0];
		}
	} 


function get_person(){
	// print_r($_POST);
	$id=divide($_POST['id']);
$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,fecha_nacimiento,YEAR(CURDATE())-YEAR(fecha_nacimiento) Edad
FROM personas 
	WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."')";
	// echo $sql;
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return '';
	}
return json_encode($info['responseResult'][0]);
} */

function get_tamBarthel(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT P.idpeople,P.idpersona barthel_idpersona,P.tipo_doc barthel_tipodoc,
        concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) barthel_nombre,P.fecha_nacimiento barthel_fechanacimiento,
        TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS barthel_edad
		FROM person P
		WHERE P.idpeople ='{$id[0]}'";
		// echo $sql; 
		$info=datos_mysql($sql);
				return $info['responseResult'][0];
		}
	} 

function focus_tamBarthel(){
	return 'tamBarthel';
   }
   
function men_tamBarthel(){
	$rta=cap_menus('tamBarthel','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamBarthel') {  
		$rta .= "<li class='icono $a  grabar' title='Grabar' Onclick=\"grabar('$a',this);\" ></li>";
	}
	return $rta;
  }
   
function gra_tamBarthel(){
	$id=divide($_POST['idbarthel']);
	if(count($id)!= "2"){
		return "No es posible actualizar el tamizaje";
	}else{
		$data=datos_mysql("select count(Z.momento) as moment from hog_tam_barthel Z  where Z.idpeople='{$id[0]}'");
		$momen=$data['responseResult'][0]['moment'];
		if($momen=='0'){
			$idmomento = 1;
		}elseif($momen=='1'){
			$idmomento = 2;
		}else{
			return "Ya se realizo los dos momentos";
		}
	
	$suma_iden = (
		$_POST['comer']+
		$_POST['lavarse']+
		$_POST['vestirse']+
		$_POST['arreglarse']+
		$_POST['deposicion']+
		$_POST['miccion']+
		$_POST['sanitario']+
		$_POST['trasladarse']+
		$_POST['deambular']+
		$_POST['escalones']
	);

	$suma_barthel = ($suma_iden);

		$sql="INSERT INTO hog_tam_barthel VALUES (null,
		$id[0],
		TRIM(UPPER('{$_POST['fecha_toma']}')),
		TRIM(UPPER('{$idmomento}')),
		TRIM(UPPER('{$_POST['comer']}')),
		TRIM(UPPER('{$_POST['lavarse']}')),
		TRIM(UPPER('{$_POST['vestirse']}')),
		TRIM(UPPER('{$_POST['arreglarse']}')),
		TRIM(UPPER('{$_POST['deposicion']}')),
		TRIM(UPPER('{$_POST['miccion']}')),
		TRIM(UPPER('{$_POST['sanitario']}')),
		TRIM(UPPER('{$_POST['trasladarse']}')),
		TRIM(UPPER('{$_POST['deambular']}')),
		TRIM(UPPER('{$_POST['escalones']}')),
		'{$suma_barthel}',
		TRIM(UPPER('')),
		TRIM(UPPER('{$_SESSION['us_sds']}')),
		DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
		// echo $sql;
	}
	  $rta=dato_mysql($sql);
	  return $rta; 
	}


	function opc_barthel_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}
	function opc_sexo($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
	}
	function opc_momento($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=116 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_departamento($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=105 and estado='A' ORDER BY 1",$id);
	}
	function opc_salud_mental($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=104 and estado='A' ORDER BY 1",$id);
	}
	function opc_estado_civil($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=47 and estado='A' ORDER BY 1",$id);
	}
	function opc_niv_educativo($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=52 and estado='A' ORDER BY 1",$id);
	}
	function opc_comer($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=106 and estado='A' ORDER BY 1 ",$id);
	}
	function opc_lavarse($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=107 and estado='A' ORDER BY 1 ",$id);
	}
	function opc_vestirse($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=108 and estado='A' ORDER BY 1 ",$id);
	}
	function opc_arreglarse($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=109 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_deposicion($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=110 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_miccion($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=111 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_sanitario($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=112 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_trasladarse($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=113 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_deambular($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=114 and estado='A'  ORDER BY 1 ",$id);
	}
	function opc_escalones($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=115 and estado='A'  ORDER BY 1 ",$id);
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamBarthel' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamBarthel','pro',event,'','lib.php',7,'tamBarthel');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
	