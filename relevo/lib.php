<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
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

/*function lis_relevo(){
	$info=datos_mysql("SELECT COUNT(*) total FROM personas P LEFT JOIN personas_datocomp D ON idpersona=dc_documento AND tipo_doc=dc_tipo_doc
	LEFT JOIN rel_relevo R ON idpersona=rel_documento AND tipo_doc=rel_tipo_doc
	LEFT JOIN hog_viv V ON P.vivipersona=V.idviv
	left join hog_geo G ON V.idgeo=concat(estrategia,'_',sector_catastral,'_',nummanzana,'_',predio_num,'_',unidad_habit,'_',estado_v)	
	LEFT JOIN asigrelevo S ON P.idpersona = S.documento AND P.tipo_doc = S.tipo_doc
	LEFT JOIN usuarios U ON S.doc_asignado = U.id_usuario  WHERE P.cuidador='SI' ".whe_relevo());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-relevo']))? ($_POST['pag-relevo']-1)* $regxPag:0;

	// var_dump(rol('relevo'));
	$sql="SELECT ROW_NUMBER() OVER (ORDER BY 1) R,concat(P.tipo_doc,'_',P.idpersona) ACCIONES,
rel_validacion17 Relevo,`idpersona` AS 'N° Documento',concat_ws(' ',`nombre1`,`nombre2`,`apellido1`,`apellido2`) nombres,G.localidad,sector_catastral,nummanzana,direccion
 FROM `personas` P
  LEFT JOIN personas_datocomp D ON idpersona=dc_documento AND tipo_doc=dc_tipo_doc
  LEFT JOIN rel_relevo R ON idpersona=rel_documento AND tipo_doc=rel_tipo_doc
  LEFT JOIN hog_viv V ON P.vivipersona=V.idviv
  left join hog_geo G ON V.idgeo=concat(estrategia,'_',sector_catastral,'_',nummanzana,'_',predio_num,'_',unidad_habit,'_',estado_v)	
  LEFT JOIN asigrelevo S ON P.idpersona = S.documento AND P.tipo_doc = S.tipo_doc
  LEFT JOIN usuarios U ON S.doc_asignado = U.id_usuario 
  WHERE cuidador='SI'";
	$sql.=whe_relevo();
	$sql.=" ORDER BY P.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
// echo $sql;
 	// $sql1="SELECT ROW_NUMBER() OVER (ORDER BY 1) R,concat(tipo_doc,'_',idpersona) ACCIONES,
	// rel_validacion17 Relevo,`idpersona` AS 'N° Documento',concat_ws(' ',`nombre1`,`nombre2`,`apellido1`,`apellido2`) nombres,G.localidad,sector_catastral,nummanzana,direccion
	//  FROM `personas` P
	//   LEFT JOIN personas_datocomp D ON idpersona=dc_documento AND tipo_doc=dc_tipo_doc
	//   LEFT JOIN rel_relevo R ON idpersona=rel_documento AND tipo_doc=rel_tipo_doc
	//   LEFT JOIN hog_viv V ON P.vivipersona=V.idviv
	//   left join hog_geo G ON V.idgeo=concat(estrategia,'_',sector_catastral,'_',nummanzana,'_',predio_num,'_',unidad_habit,'_',estado_v)	
	//   WHERE cuidador='SI'";
		// $sql1.=whe_relevo();
// $_SESSION['sql_relevo']=$sql1;
 //echo $sql;
	
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"relevo",$regxPag);
}


function whe_relevo() {
	$sql = "";
	if ($_POST['fid'])
		$sql .= " AND P.idpersona like '%".$_POST['fid']."%'";
		$rta=datos_mysql("select FN_USUARIO('".$_SESSION['us_sds']."') as usu;");
		$usu=divide($rta["responseResult"][0]['usu']);
		$subred = ($usu[1]=='TSO') ? '1,2,3,4,5' : $usu[2] ;
		$sql.="  and U.componente in ('EAC','ADM') AND U.subred IN(".$subred.") AND S.doc_asignado='".$_SESSION['us_sds']."'";
	return $sql;
}
*/
function focus_relevo(){
	return 'relevo';
}

function men_relevo(){
 $rta=cap_menus('relevo','pro');
 return $rta;
}


function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	if($a=='relevo' && isset($acc['crear']) && $acc['crear']=='SI'){
		$rta.= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
		$rta.= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this);\"></li>";
	}
	if ($a=='sesiones' && isset($acc['crear']) && $acc['crear']=='SI'){  
		$rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  	}
	return $rta;
}


function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);
	$rta=$c[$d];
	// $rta=iconv('UTF-8','ISO-8859-1',$rta);
	// var_dump($a);
	// var_dump($c);
	if ($a=='relevo' && $b=='acciones'){
		$rta="<nav class='menu right'>";
		$rta.="<li class='icono mapa' title='Aceptación Relevos' id='".$c['ACCIONES']."' Onclick=\"mostrar('relevo','pro',event,'','lib.php',7);enabDateRel(this,['fre'])\"></li>";
		if($c['Relevo']=='SI'){
			// $perfiles=['LARREL','TOPREL','LEFREL','TSOREL'];
			$perfiles = ['ADM','LARREL', 'FISREL', 'LEFREL', 'TSOREL'];
			$rta .= "<li class='icono editar' title='Sesión' id='{$c['ACCIONES']}' onclick=\"mostrar('sesiones','pro',event,'','sesiones.php',7);setTimeout(chanActi,300,'rel_validacion3','act',['".implode("','",$perfiles)."']);\"></li>";
			// $rta.="<li class='icono editar' title='Sesión' id='".$c['ACCIONES']."' Onclick=\"mostrar('sesiones','pro',event,'','sesiones.php',7);setTimeout(chanActi,300,'rel_validacion3','act',['LARREL','TOPREL','LEFREL','TSOREL']);\"></li>";
			if(perfil1()=='AUXREL' || perfil1()=='ADM' ){		
					$rta.="<li class='icono medida' title='Crear Signos' id='".$c['ACCIONES']."' Onclick=\"mostrar('vitals_signs','pro',event,'','sigvital.php',7);\"></li>";  //getData('plancon',event,this,'id');   act_lista(f,this);
				}
		}
	}
	if ($a=='session' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
			$rta.="<li class='icono editar ' title='Editar Sesión' id='".$c['ACCIONES']."' Onclick=\"Color('session-lis');setTimeout(getData,300,'sesiones',event,this,['rel_validacion1','rel_validacion2','rel_validacion3','rel_validacion4'],'sesiones.php');\"></li>";  //getData('plancon',event,this,'id');   act_lista(f,this);
		}
	
 return $rta;
}

function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cmp_relevo() {
	$rta="";
	$hoy=date('Y-m-d');
	$t=['idpeople'=>'','idpersona'=>'','acep_rbc'=>'','fecha_acep'=>'','Persona_Cuidadora'=>'','ante_cuidador'=>'','otros_antecuidador'=>'','np_cuida'=>'','zarit_cuid'=>'',
	'cuidado_1'=>'','antecedentes_1'=>'','otro_1'=>'','whodas1'=>'','cert_disca1'=>'',
	'cuidado_2'=>'','antecedentes_2'=>'','otro_2'=>'','whodas2'=>'','cert_disca2'=>'',
	'cuidado_3'=>'','antecedentes_3'=>'','otro_3'=>'','whodas3'=>'','cert_disca3'=>'','fecha_create'=>'','usu_creo'=>'','fecha_update'=>'','usu_update'=>'','estado'=>''];
	$w='relevo';
	$j=get_relevo();$e=get_zarit();$p=get_personas();
	if ($j==""){$j=$t;}if($e==""){$e=$t;}if($p==""){$p=$t;}
	// $u=($j['Tipo_Doc']=='')?true:false;
	$o='infgen';
	// var_dump($_POST);
	$days=fechas_app('relevo');
	$c[]=new cmp($o,'e',null,'INFORMACIÓN DE LA PERSONA CUIDADORA',$w);	
	$c[]=new cmp('idrel','h','20',$_POST['id'],$w.' '.$o,'','',null,null,false,false,'','col-1');
	$c[]=new cmp('acep_rbc','s','3',$j['acep_rbc'],$w.' '.$o,'Acepta Participar en la Estrategia RBC','aler',null,null,true,true,'','col-25',"enbValue('acep_rbc','fre','SI');");
	$c[]=new cmp('fecha_acep','d','10',$j['fecha_acep'],$w.' fre '.$o,'Fecha de la Aceptacion','fecha_acep',null,null,false,false,'','col-25',"validDate(this,$days,0);");
	$c[]=new cmp('persona_cuidadora','t','50',$p['Persona_Cuidadora'],$w.' '.$o,'Nombre Cuidador','persona_cuidadora',null,'',false,false,'','col-5');
	$c[]=new cmp('ante_cuidador','s','2',$j['ante_cuidador'],$w.' fre '.$o,'ANTECEDENTES PATOLOGICOS DEL CUIDADOR','antecedentes',null,null,true,false,'','col-25',"othePath(this,'oth');");
	$c[]=new cmp('otros_antecuidador','t','50',$j['otros_antecuidador'],$w.' oth '.$o,'Otro, Cual','otros_antecuidador',null,null,false,false,'','col-25');
	$c[]=new cmp('np_cuida','s','3',$j['np_cuida'],$w.'  fre mod '.$o,'Número de personas al Cuidado','np_cuida',null,null,false,false,'','col-25',"enabCare(this,['cr1','cr2','cr3']);");
	$c[]=new cmp('zarit_cuid','t','2',$e['zarit_cuid'],$w.' '.$o,'ZARIT INTERPRETACIÓN - Inicial','zarit_cuid',null,null,false,false,'','col-25');

	$o='infgen_2';
	$c[]=new cmp($o,'e',null,'INFORMACION DE LA PERSONA QUE REQUIERE EL CUIDADO',$w);
	$c[]=new cmp('cuidado_1','s','18',$j['cuidado_1'],$w.' cr1 care '.$o,'Seleccione Usuario que requiere cuidado','rel_validacion13',null,null,false,false,'','col-3',"validCare('care');");
	$c[]=new cmp('antecedentes_1','s','3',$j['antecedentes_1'],$w.' cr1 '.$o,'ANTECEDENTES PATOLOGICOS','antecedentes',null,null,false,false,'','col-25',"othePath(this,'ot');");
	$c[]=new cmp('otro_1','t','50',$j['otro_1'],$w.' ot  '.$o,'Otro, Cual','otro_1',null,null,false,false,'','col-2');
	// $c[]=new cmp('whodas1','t','3',$j['whodas1'],$w.'  '.$o,'WHODAS INTERPRETACIÓN - Inicial','whodas1',null,null,false,false,'','col-15');
	$c[]=new cmp('cert_disca1','s','3',$j['cert_disca1'],$w.' cr1 '.$o,'Cuenta con Certificado de Discapacidad','aler',null,null,false,false,'','col-25');

	$c[]=new cmp('cuidado_2','s','18',$j['cuidado_2'],$w.' cr2 care '.$o,'Seleccione Segundo Usuario que requiere cuidado','rel_validacion13',null,null,false,false,'','col-3',"validCare('care');");
	$c[]=new cmp('antecedentes_2','s','3',$j['antecedentes_2'],$w.' cr2 '.$o,'ANTECEDENTES PATOLOGICOS','antecedentes',null,null,false,false,'','col-25',"othePath(this,'ot1');");
	$c[]=new cmp('otro_2','t','50',$j['otro_2'],$w.' ot1 '.$o,'Otro, Cual','otro_2',null,null,false,false,'','col-2');
	// $c[]=new cmp('whodas2','t','3',$j['whodas2'],$w.' '.$o,'WHODAS INTERPRETACIÓN - Inicial','whodas2',null,null,false,false,'','col-15');
	$c[]=new cmp('cert_disca2','s','3',$j['cert_disca2'],$w.' cr2 '.$o,'Cuenta con Certificado de Discapacidad','aler',null,null,false,false,'','col-25');
	
	$c[]=new cmp('cuidado_3','s','18',$j['cuidado_3'],$w.' cr3 care '.$o,'Seleccione Tercer Usuario que requiere cuidado','rel_validacion13',null,null,false,false,'','col-3',"validCare('care');");
	$c[]=new cmp('antecedentes_3','s','3',$j['antecedentes_3'],$w.' cr3 '.$o,'ANTECEDENTES PATOLOGICOS','antecedentes',null,null,false,false,'','col-25',"othePath(this,'ot2');");
	$c[]=new cmp('otro_3','t','50',$j['otro_3'],$w.' ot2 '.$o,'Otro, Cual','otro_3',null,null,false,false,'','col-2');
	// $c[]=new cmp('whodas3','t','3',$j['whodas3'],$w.' '.$o,'WHODAS INTERPRETACIÓN - Inicial','whodas3',null,null,false,false,'','col-15');
	$c[]=new cmp('cert_disca3','s','3',$j['cert_disca3'],$w.' cr3 '.$o,'Cuenta con Certificado de Discapacidad','aler',null,null,false,false,'','col-25');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	//  $rta .="<div class='encabezado integrantes'>TABLA DE INTEGRANTES DE LA FAMILIA</div><div class='contenido' id='integrantes-lis' >".lis_integrantes1()."</div></div>";
	return $rta;
}

function get_relevo(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT R.acep_rbc,R.fecha_acep,R.ante_cuidador,R.otros_antecuidador,R.np_cuida,R.cuidado_1,
		R.antecedentes_1,R.otro_1,R.whodas1,R.cert_disca1,R.cuidado_2,R.antecedentes_2,R.otro_2,R.whodas2,
		R.cert_disca2,R.cuidado_3,R.antecedentes_3,R.otro_3,R.whodas3,R.cert_disca3,P.tipo_doc AS Tipo_Doc,
		P.idpersona AS Documento,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) AS Persona_Cuidadora,
		P.fecha_nacimiento,P.sexo,P.genero,P.etnia,P.nacionalidad,P.regimen,P.eapb
		FROM rel_relevo R 
		LEFT JOIN person P ON R.id_people=P.idpeople
		WHERE R.id_people='{$id[0]}'";
		$info=datos_mysql($sql);
		if ($info['responseResult']){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} 
}

function get_personas(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) AS Persona_Cuidadora
		FROM person P 
		WHERE idpeople='{$id[0]}'";
		$info=datos_mysql($sql);
		// var_dump($sql);
		if ($info['responseResult']){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} 
}
function get_hamilton(){
	return "";
	/* if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT hamilton_total rel_validacion4,hamilton_analisis rel_validacion8 
		FROM hog_tam_hamilton H
		LEFT JOIN rel_relevo R ON H.hamilton_idpersona=R.rel_documento  AND  H.hamilton_tipodoc=R.rel_tipo_doc
		WHERE
			(H.hamilton_momento = 2 OR 
			(H.hamilton_idpersona IS NULL AND H.hamilton_tipodoc IS NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_hamilton WHERE hamilton_idpersona = R.rel_documento AND hamilton_tipodoc = R.rel_tipo_doc
			)) OR 
			(H.hamilton_idpersona IS NOT NULL AND H.hamilton_tipodoc IS NOT NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_hamilton WHERE hamilton_idpersona = R.rel_documento AND hamilton_tipodoc = R.rel_tipo_doc AND hamilton_momento = 2
			))) AND hamilton_tipodoc='{$id[0]}' AND hamilton_idpersona='{$id[1]}'";

		$info=datos_mysql($sql);
		if ($info['responseResult']){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	}  */
}

function get_zarit(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT analisis zarit_cuid 
		FROM hog_tam_zarit H
		LEFT JOIN rel_relevo R ON H.idpeople=R.id_people
		WHERE
		H.momento=1 AND H.idpeople='{$id[0]}'";
			/* (H.zarit_momento = 2 OR 
			(H.zarit_idpersona IS NULL AND H.zarit_tipodoc IS NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_zarit WHERE zarit_idpersona = R.rel_documento AND zarit_tipodoc = R.rel_tipo_doc
			)) OR 
			(H.zarit_idpersona IS NOT NULL AND H.zarit_tipodoc IS NOT NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_zarit WHERE zarit_idpersona = R.rel_documento AND zarit_tipodoc = R.rel_tipo_doc AND zarit_momento = 2
			))) AND zarit_tipodoc='{$id[0]}' AND zarit_idpersona='{$id[1]}'"; */

		$info=datos_mysql($sql);
		if ($info['responseResult']){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	}
}
 
function get_apgar(){
	/* if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT  rel_validacion7,apgar_analisis rel_validacion11 
		FROM hog_tam_apgar H
		LEFT JOIN rel_relevo R ON H.apgar_idpersona=R.rel_documento  AND  H.apgar_tipodoc=R.rel_tipo_doc
		WHERE
			(H.apgar_momento = 2 OR 
			(H.apgar_idpersona IS NULL AND H.apgar_tipodoc IS NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_apgar WHERE apgar_idpersona = R.rel_documento AND apgar_tipodoc = R.rel_tipo_doc
			)) OR 
			(H.apgar_idpersona IS NOT NULL AND H.apgar_tipodoc IS NOT NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_apgar WHERE apgar_idpersona = R.rel_documento AND apgar_tipodoc = R.rel_tipo_doc AND apgar_momento = 2
			))) AND apgar_tipodoc='{$id[0]}' AND apgar_idpersona='{$id[1]}'";

		$info=datos_mysql($sql);
		if ($info['responseResult']){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	}  */
}

function get_zung(){
	return "";
	/*
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT zung_puntaje rel_validacion9,zung_analisis rel_validacion5 
		FROM hog_tam_zung H
		LEFT JOIN rel_relevo R ON H.zung_idpersona=R.rel_documento  AND  H.zung_tipodoc=R.rel_tipo_doc
		WHERE  
			(H.zung_momento = 2 OR 
			(H.zung_idpersona IS NULL AND H.zung_tipodoc IS NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_zung WHERE zung_idpersona = R.rel_documento AND zung_tipodoc = R.rel_tipo_doc
			)) OR 
			(H.zung_idpersona IS NOT NULL AND H.zung_tipodoc IS NOT NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_zung WHERE zung_idpersona = R.rel_documento AND zung_tipodoc = R.rel_tipo_doc AND zung_momento = 2
			))) AND zung_tipodoc='{$id[0]}' AND zung_idpersona='{$id[1]}'";

		$info=datos_mysql($sql);
		if ($info['responseResult']){
			// print_r($info);
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} */
}

function get_ophi(){
	return "";
	/*
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT ophi_puntaje rel_validacion12 
		FROM hog_tam_ophi H
		LEFT JOIN rel_relevo R ON H.ophi_idpersona=R.rel_documento  AND  H.ophi_tipodoc=R.rel_tipo_doc
		WHERE 
			(H.ophi_momento = 2 OR 
			(H.ophi_idpersona IS NULL AND H.ophi_tipodoc IS NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_ophi WHERE ophi_idpersona = R.rel_documento AND ophi_tipodoc = R.rel_tipo_doc
			)) OR 
			(H.ophi_idpersona IS NOT NULL AND H.ophi_tipodoc IS NOT NULL AND NOT EXISTS (
				SELECT 1 FROM hog_tam_ophi WHERE ophi_idpersona = R.rel_documento AND ophi_tipodoc = R.rel_tipo_doc AND ophi_momento = 2
			))) AND ophi_tipodoc='{$id[0]}' AND ophi_idpersona='{$id[1]}'";
		$info=datos_mysql($sql);
		if ($info['responseResult']){
			// echo $sql;
			// var_dump($info['responseResult']);
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} */
}

function gra_relevo(){

	$id=divide($_POST['idrel']);
	
		/* $val5 = $_POST['rel_validacion5'] ?? null;
		$val7 = $_POST['rel_validacion7'] ?? null;
		$val8 = $_POST['rel_validacion8'] ?? null;
		$val10 = $_POST['rel_validacion10'] ?? null;
		$val11 = $_POST['rel_validacion11'] ?? null; */

		$sql = "INSERT INTO rel_relevo VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
		$params = [
		['type' => 'i', 'value' => NULL],
		['type' => 'i', 'value' => $id[0]],
		['type' => 's', 'value' => $_POST['acep_rbc']],
		['type' => 's', 'value' => $_POST['fecha_acep']],
		['type' => 's', 'value' => $_POST['ante_cuidador']],
		['type' => 's', 'value' => $_POST['otros_antecuidador']],
		['type' => 's', 'value' => $_POST['np_cuida']],
		['type' => 's', 'value' => $_POST['zarit_cuid']],
		['type' => 's', 'value' => $_POST['cuidado_1']],
		['type' => 's', 'value' => $_POST['antecedentes_1']],
		['type' => 's', 'value' => $_POST['otro_1']],
		['type' => 's', 'value' => ''],
		['type' => 's', 'value' => $_POST['cert_disca1']],
		['type' => 's', 'value' => $_POST['cuidado_2']],
		['type' => 's', 'value' => $_POST['antecedentes_2']],
		['type' => 's', 'value' => $_POST['otro_2']],
		['type' => 's', 'value' => ''],
		['type' => 's', 'value' => $_POST['cert_disca2']],
		['type' => 's', 'value' => $_POST['cuidado_3']],
		['type' => 's', 'value' => $_POST['antecedentes_3']],
		['type' => 's', 'value' => $_POST['otro_3']],
		['type' => 's', 'value' => ''],
		['type' => 's', 'value' => $_POST['cert_disca3']],
		['type' => 'i', 'value' => $_SESSION['us_sds']],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => 'A']
		];
		
		return $rta = mysql_prepd($sql, $params);
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function opc_np_cuida($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 189 and estado='A' ORDER BY 1",$id);
}
function opc_rel_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 1 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion3($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 29 and estado='A' ORDER BY 1",$id);
}
function opc_antecedentes($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 28 and estado='A' ORDER BY 1",$id);
}
function opc_rel_sesion($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 125 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion2($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion5($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion6($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion16($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 14 and estado='A' ORDER BY 1",$id);
}
function opc_aler($id=''){
	return opc_sql("SELECT `descripcion`,descripcion,valor FROM `catadeta` WHERE idcatalogo=170 and estado='A'  ORDER BY 1 ",$id);
}
function opc_rel_validacion7($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion9($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion10($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion12($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_rel_validacion13($id='') {
	// var_dump($_REQUEST);
	$idp = trim($_REQUEST['id']);
	$idp=divide($idp);
	$idp = trim((string)$idp[0]);
	// $id= trim((string)$idp);	
		return	opc_sql("SELECT idpeople,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) 'Nombres' 
			from person 
			where vivipersona=(select vivipersona from person where idpeople='$idp') and idpeople<>'$idp'",$id);
}

function opc_rel_validacion1($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 28 and estado='A' ORDER BY 1",$id);
}
function opc_rel_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_rel_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=49 and estado='A' ORDER BY 1",$id);
}
function opc_rel_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY 1",$id);
}
function opc_rel_regimen($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=56 and estado='A' ORDER BY 1",$id);
}
function opc_rel_eapb($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY 1",$id);
}
function opc_en_duda($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=130 and estado='A' ORDER BY 1",$id);
}
function opc_rel_etnia($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=16 and estado='A' ORDER BY 1",$id);
}
?>
