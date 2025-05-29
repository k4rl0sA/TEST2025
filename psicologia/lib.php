<?php
require_once '../libs/gestion.php';
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

/* function lis_psicologia(){
	$info=datos_mysql("SELECT COUNT(DISTINCT concat(P.tipo_doc,'_',idpersona)) total from personas P 
	LEFT JOIN eac_atencion A ON P.idpersona = A.atencion_idpersona AND P.tipo_doc = A.atencion_tipodoc 
	LEFT JOIN hog_viv V ON P.vivipersona=V.idviv 
	left join hog_geo G ON V.idpre=G.idgeo	
	LEFT JOIN asigpsico S ON P.idpersona = S.documento AND P.tipo_doc = S.tipo_doc	
	LEFT JOIN usuarios U ON S.doc_asignado = U.id_usuario 
	LEFT JOIN eac_rutpsico R ON S.documento = R.documento AND S.tipo_doc = R.tipo_doc
	WHERE (A.atencion_ordenpsicologia='SI' OR R.asigno='SI')".whe_psicologia());//
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-psicologia']))? ($_POST['pag-psicologia']-1)* $regxPag:0;
	
	$sql="SELECT DISTINCT concat(P.tipo_doc,'_',idpersona) ACCIONES, P.tipo_doc AS 'Tipo Documento',
	idpersona AS 'N° Documento', CONCAT(nombre1, ' ',apellido1) AS Nombre,
	sector_catastral 'sector catastral',nummanzana manzana,predio_num predio,
	S.fecha_create AS 'Fecha Asignado',P.estado
	FROM personas P
	LEFT JOIN eac_atencion A ON P.idpersona = A.atencion_idpersona AND P.tipo_doc = A.atencion_tipodoc
	LEFT JOIN hog_viv V ON P.vivipersona=V.idviv
	left join hog_geo G ON V.idpre=G.idgeo
	LEFT JOIN asigpsico S ON P.idpersona = S.documento AND P.tipo_doc = S.tipo_doc
	LEFT JOIN usuarios U ON S.doc_asignado = U.id_usuario
	LEFT JOIN eac_rutpsico R ON S.documento = R.documento AND S.tipo_doc = R.tipo_doc
	WHERE (A.atencion_ordenpsicologia='SI' OR R.asigno='SI' ) ";//
	$sql.=whe_psicologia();
	$sql.=" ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"psicologia",$regxPag);
}
 
function botones(){
	$btn="";
	return $btn;
}

function whe_psicologia() {
	$sql = "";
	if ($_POST['fseca'])
		$sql .= " AND G.sector_catastral = '".$_POST['fseca']."'";
	if ($_POST['fmanz'])
		$sql .= " AND G.nummanzana ='".$_POST['fmanz']."' ";
	if ($_POST['fid']){
		$sql .= " AND idpersona= '".$_POST['fid']."'";
	}else{
		$sql .= " and S.doc_asignado IN ('{$_SESSION['us_sds']}')";
		if ($_POST['fdes']) {
			if ($_POST['fhas']) {
				$sql .= " AND S.fecha_create >='".$_POST['fdes']." 00:00:00' AND S.fecha_create <='".$_POST['fhas']." 23:59:59'";
			} else {
				$sql .= " AND S.fecha_create >='".$_POST['fdes']." 00:00:00' AND S.fecha_create <='". $_POST['fdes']." 23:59:59'";
			}
		}
	}
		// $rta=datos_mysql("select FN_USUARIO('".$_SESSION['us_sds']."') as usu;");
		// $usu=divide($rta["responseResult"][0]['usu']);
		// $subred = ($usu[1]=='ADM') ? '1,2,3,4,5' : $usu[2] ;
		$rta=datos_mysql("select perfil,subred from usuarios where id_usuario='".$_SESSION['us_sds']."';");
		$perfil=divide($rta["responseResult"][0]['perfil']);
		$subred=divide($rta["responseResult"][0]['subred']);
		$sub = ($perfil[0]=='ADM') ? '1,2,3,4,5' : $subred[0] ; 
		$sql.="  and U.componente IN('EAC','ADM') AND U.subred IN($sub) ";
	return $sql;
}
 */
function focus_psicologia(){
	return 'psicologia';
}

function men_psicologia(){
 $rta=cap_menus('psicologia','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  $acc=rol($a);
  if ($a=='psicologia'){  $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	  $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this);\"></li>";
  }
  if ($a=='sesion2'){  $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	  $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this);\"></li>";
  }
  if ($a=='sesion_fin'){  $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	  $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}

/* function restDate($a,$b){
	$ahora = new DateTime();
	$dias = $b;
	$fech = clone $ahora;
	$fech->add(new DateInterval("P{$dias}D"));
	$feValida = new DateTime($a);
	if($fech>$feValida){
		return true;
	}else{
		return false;
	}
} */

function formato_dato($a,$b,$c,$d){
/* 	$b=strtolower($b);
	$rta=$c[$d];
	// $rta=iconv('UTF-8','ISO-8859-1',$rta);
	// var_dump($a);
	// var_dump($c);
	$dateMax=0;
	if ($a=='psicologia' && $b=='acciones'){
		$rta="<nav class='menu right'>";
			$rta.="<li class='icono admsi1' title='Crear Admisión' id='".$c['N° Documento']."_".$c['Tipo Documento']."' Onclick=\"mostrar('admision','pro',event,'','../atencion/admision.php',7,'admision');Color('datos-lis');\"></li>";
			$rta.="<li class='icono mapa' title='Sesión 1' id='".$c['ACCIONES']."' Onclick=\"mostrar('psicologia','pro',event,'','lib.php',7);setTimeout(hidFieOpt,700,'psi_validacion1','ter_hide',false);setTimeout(hidOpt,700,'psi_validacion13','plan_hide','plan_show',false);\"></li>";
		$id = divide($c['ACCIONES']);

		$sql = "SELECT psi_tipo_doc, psi_documento, DATEDIFF(NOW(), fecha_ses1) AS dias
		 FROM `psi_psicologia` WHERE psi_tipo_doc='{$id[0]}' AND psi_documento='{$id[1]}' AND psi_validacion13 = 'SI'";
		$info = datos_mysql($sql);

		if (isset($info['responseResult'][0]) && $info['responseResult'][0]['dias']>$dateMax){
			$rta.="<li class='icono editar' title='Sesión 2' id='".$c['ACCIONES']."' Onclick=\"mostrar('sesion2','pro',event,'','lib.php',7);\"></li>";
		}

		$sql_sesion2 = "SELECT psi_tipo_doc, psi_documento, DATEDIFF(NOW(), psi_fecha_sesion) AS dias
		FROM `psi_sesion2` WHERE psi_tipo_doc='{$id[0]}' AND psi_documento='{$id[1]}' AND contin_caso='4'";
		$info_sesion2 = datos_mysql($sql_sesion2);

		if (isset($info_sesion2['responseResult'][0]) && $info['responseResult'][0]['dias']>$dateMax){
			$rta.="<li class='icono familia' title='Sesión 3, 4, 5, 6' id='".$c['ACCIONES']."' Onclick=\"mostrar('sesiones_psi','pro',event,'','sesiones.php',7);setTimeout(hidPlan,700,'psi_validacion10','duda_com',false);setTimeout(hidFieOpt,700,'psi_validacion7','sem_hide',false);\"></li>";
		}
		$sql_sesiones = "SELECT psi_tipo_doc, psi_documento, COUNT(*) AS total
		FROM `psi_sesiones` WHERE psi_tipo_doc='{$id[0]}' AND psi_documento='{$id[1]}' AND psi_sesion IN (1, 2, 3, 4)";
		$info_sesion2 = datos_mysql($sql_sesiones);

		$sql_sesiones_fin = "SELECT psi_tipo_doc, psi_documento
		FROM `psi_sesiones` WHERE psi_tipo_doc='{$id[0]}' AND psi_documento='{$id[1]}' AND psi_validacion17 = '5'";
		$info_sesion_fin = datos_mysql($sql_sesiones_fin);

		$valores = $info_sesion2['responseResult'][0]['total'];

		if ($valores == 3 || isset($info_sesion_fin['responseResult'][0])) {
			$rta.="<li class='icono crear' title='Sesión final' id='".$c['ACCIONES']."' Onclick=\"mostrar('sesion_fin','pro',event,'','lib.php',7);\"></li>";
		}
	}

 return $rta; */
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cmp_psicologia() {
	//error_reporting(0);
	$rta="";
	$hoy=date('Y-m-d');
	$t=['id_people'=>'','psi_tipo_doc'=>'','psi_documento'=>'','tipo_caso'=>'','cod_admin'=>'','psi_validacion1'=>'','psi_validacion2'=>'','psi_validacion3'=>'','psi_validacion4'=>'','psi_validacion5'=>'','psi_validacion6'=>'','psi_validacion7'=>'','psi_validacion8'=>'','psi_validacion9'=>'','psi_validacion10'=>'','psi_validacion11'=>'','letra1'=>'','rango1'=>'','psi_validacion12'=>'','psi_validacion13'=>'','psi_validacion14'=>'','psi_validacion15'=>'','fecha_create'=>'','usu_creo'=>'','fecha_update'=>'','usu_update'=>'','estado'=>'',
	'zung_puntaje'=>'','hamilton_total'=>'','psi_diag12'=>'','otro'=>'','numsesi'=>'','eva_chips'=>'','fecha_ses1'=>'','zung_analisis'=>'','hamilton_analisis'=>'','whodas_analisis'=>''];
	$w='psicologia';
	$d=get_psicologia();
	$j=get_persona();
	$k=get_DataPersonas();
	if ($d=="") {$d=$t;}
	if ($j=="") {$j=$t;}
	if ($k=="") {$k=$t;}
	$ad=($j['edad']>17)?true:false;
	// var_dump($j);
	$u=($d['id_people']=='')?true:false;
	$o='infgen';
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$key=' srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp('idpsi','h','20', $_POST['id'] ,$w.' '.$o,'','',null,null,true,$u,'','col-1');
	$c[]=new cmp('psi_documento','t','20',$j['idpersona'],$w.' '.$o,'Numero Documento','psi_documento',null,null,true,false,'','col-5');
	$c[]=new cmp('psi_tipo_doc','s','3',$j['tipo_doc'],$w.' '.$o,'Tipo documento','psi_tipo_doc',null,null,true,false,'','col-5');//,'getDatForm(\'srch\',\'DataPersonas\',[\'infgen\']);'
	$c[]=new cmp('psi_nombre1','t','20',$j['nombre1'],$w.' '.$o,'Primer nombre','psi_nombre1',null,'',false,false,'','col-2');
	$c[]=new cmp('psi_nombre2','t','20',$j['nombre2'],$w.' '.$o,'Segundo nombre','psi_nombre2',null,'',false,false,'','col-3');
	$c[]=new cmp('psi_apellido1','t','20',$j['apellido1'],$w.' '.$o,'Primer apellido','psi_apellido1',null,'',false,false,'','col-2');
	$c[]=new cmp('psi_apellido2','t','20',$j['apellido2'],$w.' '.$o,'Segundo apellido','psi_apellido2',null,'',false,false,'','col-3');
	$c[]=new cmp('psi_fecha_nacimiento','d','20',$j['fecha_nacimiento'],$w.' '.$o,'Fecha nacimiento','psi_fecha_nacimiento',null,'',false,false,'','col-2');
	$c[]=new cmp('psi_sexo','s','20',$j['sexo'],$w.' '.$o,'Sexo','psi_sexo',null,'',false,false,'','col-3');
	$c[]=new cmp('psi_genero','s','20',$j['genero'],$w.' '.$o,'Genero','genero',null,'',false,false,'','col-2');
	$c[]=new cmp('psi_orientacion','s','20',$j['oriensexual'],$w.' '.$o,'Orientacion sexual','oriensexual',null,'',false,false,'','col-3');
	$c[]=new cmp('psi_nacionalidad','s','20',$j['nacionalidad'],$w.' '.$o,'Nacionalidad','psi_nacionalidad',null,'',false,false,'','col-3');
	$c[]=new cmp('psi_regimen','s','20',$j['regimen'],$w.' '.$o,'Regimen','psi_regimen',null,'',false,false,'','col-3');
	$c[]=new cmp('psi_eapb','s','20',$j['eapb'],$w.' '.$o,'eapb','psi_eapb',null,'',false,false,'','col-4');
	$c[]=new cmp('psicologia_tam_zung','t','20', $k['zung_analisis'],$w.' '.$o,'RESULTADO TAMIZAJE ZUNG INICIAL','psicologia_tam_zung',null,'',$ad,false,'','col-3');
	$c[]=new cmp('psicologia_tam_hamilton','t','20', $k['hamilton_analisis'],$w.' '.$o,'RESULTADO TAMIZAJE HAMILTON INICIAL','psicologia_tam_hamilton',null,'',$ad,false,'','col-3');
	$c[]=new cmp('psicologia_funcionamiento','t','20',$k['whodas_analisis'],$w.' '.$o,'RESULTADO MEDICIÓN DEL FUNCIONAMIENTO (WHODAS 2.0) INICIAL','psicologia_funcionamiento',null,'',$ad,false,'','col-4');
	$c[]=new cmp('evachips','a','1500',$d['eva_chips'],$w.' '.$o,'EVALUACION DE RIESGO PARA NIÑOS NIÑAS Y ADOLESCENTES - ChiPS','psicologia_evaluacion',null,'',!$ad,!$ad,'','col-10');
	$c[]=new cmp('fecha_ses1','d','10',$d['fecha_ses1'],$w.' '.$o,'Fecha','fecha_ses1',null,'',true,$u,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('tipo_caso','s','20',$d['tipo_caso'],$w.' '.$o,'Tipo de Caso','tipo_caso',null,'',true,$u,'','col-3');

	$c[]=new cmp('cod_admin','s',3,$d['cod_admin'],$w.' '.$o,'Codigo Admisión','cod_admin',null,'',true,$u,'','col-4');
	// $c[]=new cmp('cod_admin','n','12',$d['cod_admin'],$w.' '.$o,'Codigo Admisión','cod_admin',null,null,true,true,'','col-2');
	
	$o='pensui';
	$c[]=new cmp($o,'e',null,'EVALUACIÓN DE PENSAMIENTOS SUICIDAS',$w);
	
	$c[]=new cmp('psi_validacion1','o','2',$d['psi_validacion1'],$w.' '.$o,'1. Durante el último mes, ¿ha tenido usted serios pensamientos o un plan para terminar con su vida?','psi_validacion1',null,null,true,true,'','col-10',"hidFieOld('psi_validacion1','ter_hide',false);");
	$c[]=new cmp('psi_validacion2','a','1500',$d['psi_validacion2'],$w.' ter_hide oculto '.$o,'2. ¿Qué acciones realizó para terminar con su vida?','psi_validacion2',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion3','s','3',$d['psi_validacion3'],$w.' ter_hide oculto '.$o,'3. ¿Tiene planes de terminar con su vida dentro de las siguientes dos semanas?','psi_validacion3',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion4','a','1500',$d['psi_validacion4'],$w.' ter_hide oculto '.$o,'4. Descipcion de la Evaluacion','psi_validacion4',null,null,false,true,'','col-10');

	$o='afetra';
	$c[]=new cmp($o,'e',null,'AFECTACIONES POSIBLES CAUSADOS POR SERIOS TRANSTORNOS MENTALES, NEUROLOGICOS O POR USO DE SUSTANCIAS',$w);
	

	$c[]=new cmp('psi_validacion5','o','2',$d['psi_validacion5'],$w.' '.$o,'1 ¿La persona le entiende (aun cuando habla el mismo idioma o dialecto que usted)?','psi_validacion5',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion6','o','2',$d['psi_validacion6'],$w.' '.$o,'2 ¿Es capaz la persona de acompañar lo que sucede en la evaluación en un grado razonable?','psi_validacion6',null,null,true,true,'','col-10');
	$c[]=new cmp('psi_validacion7','o','2',$d['psi_validacion7'],$w.' '.$o,'3 ¿Las respuestas de la persona son raras y/o sumamente inusuales?','psi_validacion7',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion8','o','2',$d['psi_validacion8'],$w.' '.$o,'4 Por las respuestas y el comportamiento de la persona, ¿parece que no está en contacto con la realidad o con lo que está sucediendo en la evaluación?','psi_validacion8',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion9','a','1500',$d['psi_validacion9'],$w.' '.$o,'5. Descipcion de los posibles transtornos','psi_validacion9',null,null,true,true,'','col-10');

	$o='proter';
	$c[]=new cmp($o,'e',null,'DEFINICION DE PROCESO TERAPEUTICO',$w);
	$c[]=new cmp('psi_validacion10','o','2',$d['psi_validacion10'],$w.' '.$o,'1. ¿Tiene la persona un plan para terminar con su vida dentro de las siguientes dos semanas?','psi_validacion10',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion11','o','2',$d['psi_validacion11'],$w.' '.$o,'2. ¿La persona posiblemente tiene un serio trastorno mental, neurológico o por uso de sustancias?','psi_validacion11',null,null,false,true,'','col-10');

	$c[]= new cmp('letra1','s','3',$d['letra1'],$w.' '.$o,'Letra CIE(1)','letra1',null,null,true,true,'','col-2',"selectDepend('letra1','rango1','../psicologia/lib.php');");
 	$c[]=new cmp('rango1','s','3',$d['rango1'],$w.' '.$o,'Tipo1','rango1',null,null,true,true,'','col-4',"selectDepend('rango1','psi_diag12','../psicologia/lib.php');");
 	$c[]=new cmp('psi_diag12','s','5',$d['psi_diag12'],$w.' '.$o,'3. Impresión diagnostica','psi_diag12',null,null,false,true,'','col-4');
	$c[]=new cmp('psi_validacion13','o','2',$d['psi_validacion13'],$w.' '.$o,'4. Requiere plan de manejo terapeutico','psi_validacion13',null,null,false,true,'','col-10','hidOpt(\'psi_validacion13\',\'plan_hide\',\'plan_show\',false);');
	$c[]=new cmp('psi_validacion14','s','3',$d['psi_validacion14'],$w.' plan_show '.$o,'5. Motivo de NO generaciôn de plan de manejo','psi_validacion14',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion15','a','1500',$d['psi_validacion15'],$w.' plan_hide  '.$o,'6. Descripcion de Plan de Manejo','psi_validacion15',null,null,false,true,'','col-10');
	$c[]=new cmp('otro','a','1500',$d['otro'],$w.' plan_show '.$o,'5.1 Observación del NO plan de manejo ','otro',null,null,false,true,'','col-10');
	$c[]=new cmp('numsesi','n','2',$d['numsesi'],$w.' plan_hide '.$o,'Número de sesiones proyectadas','numsesi',null,null,false,true,'','col-10');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	//  $rta .="<div class='encabezado integrantes'>TABLA DE INTEGRANTES DE LA FAMILIA</div><div class='contenido' id='integrantes-lis' >".lis_integrantes1()."</div></div>";
	return $rta;
}



function get_DataPersonas(){
	$id=divide($_POST['id']);
		$sql="SELECT P.idpeople,`nombre1`, `nombre2`, `apellido1`, `apellido2`, `fecha_nacimiento`,`sexo`,`genero`,`oriensexual`,`nacionalidad`,`regimen`,`eapb`,Z.analisis zung_analisis,H.analisis hamilton_analisis,W.analisis whodas_analisis
				FROM person P
				LEFT JOIN hog_tam_zung Z ON P.idpeople= Z.idpeople
				LEFT JOIN hog_tam_hamilton H ON P.idpeople= H.idpeople
				LEFT JOIN hog_tam_whodas W ON P.idpeople= W.idpeople
				WHERE P.idpeople='".$id[0]."' AND Z.momento=1 AND H.momento=1 AND W.momento=1";
	   $datos=datos_mysql($sql);
	   if (!$datos['responseResult']) {
		return '';
	}
return $datos['responseResult'][0];
}

function get_psicologia(){
	if($_POST['id']=='0'){
		return "";
	}else{	
		$id=divide($_POST['id']);
		$sql="SELECT id_people,eva_chips,fecha_ses1,tipo_caso,cod_admin,psi_validacion1,psi_validacion2,psi_validacion3,psi_validacion4,psi_validacion5,psi_validacion6,psi_validacion7,psi_validacion8,psi_validacion9,psi_validacion10,psi_validacion11,letra1,rango1,psi_diag12,psi_validacion13,psi_validacion14,otro,psi_validacion15,numsesi,estado
		FROM `psi_psicologia` WHERE id_people='{$id[0]}'";
		$info=datos_mysql($sql);
		if (isset($info['responseResult'][0])){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} 
}

function get_persona(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT tipo_doc,idpersona,nombre1,nombre2,apellido1,apellido2,fecha_nacimiento,sexo,genero,oriensexual,nacionalidad,regimen,eapb,TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
		FROM `person` WHERE idpeople='{$id[0]}'";

// sector_catastral,'_',nummanzana,'_',predio_num,'_',estrategia,'_',estado_v
		$info=datos_mysql($sql);
    	// echo $sql."=>".$_POST['id'];
		if (isset($info['responseResult'][0])){
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} 
}
 
function gra_psicologia(){
	// var_dump($_POST);
	$idpsi=divide($_POST['idpsi']);
	if(count($idpsi)==2){ 

		$sql = "INSERT INTO psi_psicologia VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
		$params = [
		['type' => 'i', 'value' => NULL],
		['type' => 'i', 'value' => $idpsi[0]],
		['type' => 's', 'value' => $_POST['fecha_ses1']],
		['type' => 's', 'value' => $_POST['tipo_caso']],
		['type' => 's', 'value' => $_POST['cod_admin']],
		['type' => 's', 'value' => $_POST['evachips']],
		['type' => 's', 'value' => $_POST['psi_validacion1']],
		['type' => 's', 'value' => $_POST['psi_validacion2']],
		['type' => 's', 'value' => $_POST['psi_validacion3']],
		['type' => 's', 'value' => $_POST['psi_validacion4']],
		['type' => 's', 'value' => $_POST['psi_validacion5']],
		['type' => 's', 'value' => $_POST['psi_validacion6']],
		['type' => 's', 'value' => $_POST['psi_validacion7']],
		['type' => 's', 'value' => $_POST['psi_validacion8']],
		['type' => 's', 'value' => $_POST['psi_validacion9']],
		['type' => 's', 'value' => $_POST['psi_validacion10']],
		['type' => 's', 'value' => $_POST['psi_validacion11']],
		['type' => 's', 'value' => $_POST['letra1']],
		['type' => 's', 'value' => $_POST['rango1']],
		['type' => 's', 'value' => $_POST['psi_diag12']],
		['type' => 's', 'value' => $_POST['psi_validacion13']],
		['type' => 's', 'value' => $_POST['psi_validacion14']],
		['type' => 's', 'value' => $_POST['otro']],
		['type' => 's', 'value' => $_POST['psi_validacion15']],
		['type' => 'i', 'value' => $_POST['numsesi']],
		['type' => 'i', 'value' => $_SESSION['us_sds']],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => 'A']
		];
		return $rta = mysql_prepd($sql, $params);
		/* $sql="INSERT INTO psi_psicologia VALUES (
			NULL,			
			$idpsi[0],
			trim(upper('{$_POST['fecha_ses1']}')),
			trim(upper('{$_POST['tipo_caso']}')),
			trim(upper('{$_POST['cod_admin']}')),
			trim(upper('{$_POST['evachips']}')),
			trim(upper('{$_POST['psi_validacion1']}')),
			trim(upper('{$_POST['psi_validacion2']}')),
			trim(upper('{$_POST['psi_validacion3']}')),
			trim(upper('{$_POST['psi_validacion4']}')),
			trim(upper('{$_POST['psi_validacion5']}')),
			trim(upper('{$_POST['psi_validacion6']}')),
			trim(upper('{$_POST['psi_validacion7']}')),
			trim(upper('{$_POST['psi_validacion8']}')),
			trim(upper('{$_POST['psi_validacion9']}')),
			trim(upper('{$_POST['psi_validacion10']}')),
			trim(upper('{$_POST['psi_validacion11']}')),
			TRIM(upper('{$_POST['letra1']}')),
			TRIM(upper('{$_POST['rango1']}')),
			trim(upper('{$_POST['psi_diag12']}')),
			trim(upper('{$_POST['psi_validacion13']}')),
			trim(upper('{$_POST['psi_validacion14']}')),
			trim(upper('{$_POST['otro']}')),
			trim(upper('{$_POST['psi_validacion15']}')),
			trim(upper('{$_POST['numsesi']}')),
			{$_SESSION['us_sds']},
			DATE_SUB(NOW(), INTERVAL 5 HOUR),
			NULL,
			NULL,
			'A')"; */
		//die();
	  //echo $x;
	//   echo $sql;
	} else {
		/* $sql="UPDATE `psi_psicologia` SET
				eva_chips=TRIM(upper('{$_POST['evachips']}')),
				psi_validacion1= TRIM(upper('{$_POST['psi_validacion1']}')),
				psi_validacion2= TRIM(upper('{$_POST['psi_validacion2']}')),
				psi_validacion3= TRIM(upper('{$_POST['psi_validacion3']}')),
				psi_validacion4= TRIM(upper('{$_POST['psi_validacion4']}')),
				psi_validacion5= TRIM(upper('{$_POST['psi_validacion5']}')),
				psi_validacion6= TRIM(upper('{$_POST['psi_validacion6']}')),
				psi_validacion7= TRIM(upper('{$_POST['psi_validacion7']}')),
				psi_validacion8= TRIM(upper('{$_POST['psi_validacion8']}')),
				psi_validacion9= TRIM(upper('{$_POST['psi_validacion9']}')),
				psi_validacion10 = TRIM(upper('{$_POST['psi_validacion10']}')),
				psi_validacion11 = TRIM(upper('{$_POST['psi_validacion11']}')),
				letra1 = TRIM(upper('{$_POST['letra1']}')),
				rango1 = TRIM(upper('{$_POST['rango1']}')),
				psi_diag12 = TRIM(upper('{$_POST['psi_diag12']}')),
				psi_validacion13 = TRIM(upper('{$_POST['psi_validacion13']}')),
				psi_validacion14 = TRIM(upper('{$_POST['psi_validacion14']}')),
				otro = TRIM(upper('{$_POST['otro']}')),
				psi_validacion15 = TRIM(upper('{$_POST['psi_validacion15']}')),
				numsesi = TRIM(upper('{$_POST['numsesi']}')),
		`usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),
		`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
		WHERE psi_tipo_doc='$idpsi[0]' AND psi_documento='$idpsi[1]'"; 	
			//  echo $sql;
			$rta=dato_mysql($sql);
			return $rta;  */
	}

	
	//return "correctamente";
	
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cmp_sesion2() {
	$rta="";
	$hoy=date('Y-m-d');
	$t=['id_people'=>'','psi_tipo_doc'=>'','psi_documento'=>'','psi_fecha_sesion'=>'','psi_sesion'=>'','cod_admin2'=>'','psi_validacion1'=>'','psi_validacion2'=>'','psi_validacion3'=>'','psi_validacion4'=>'','psi_validacion5'=>'','psi_validacion6'=>'','psi_validacion7'=>'','psi_validacion8'=>'','psi_validacion9'=>'','psi_validacion10'=>'','fecha_create'=>'','usu_creo'=>'','fecha_update'=>'','usu_update'=>'','estado'=>'','contin_caso'=>''];
	$w='sesion2';
	$d=get_sesion2();
	// $j=get_sesion2_info();
	if ($d=="") {$d=$t;}
	// if ($j=="") {$j=$t;}
	$u=($d['id_people']=='')?true:false;
	$o='infgen';
	$c[]=new cmp($o,'e',null,'Sesion 2',$w);	
	$id=divide($_POST['id']);
	$sql="SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, IFNULL(psi_fecha_sesion, CURDATE())) AS edad  
	FROM person  P  LEFT JOIN psi_sesion2 S2 ON P.idpeople=S2.id_people WHERE P.idpeople='{$id[0]}'";
	// echo $sql;
	$info=datos_mysql($sql);
	$edad=$info['responseResult'][0]['edad'];
	$dis = ($edad < 18) ? false : ($d['psi_fecha_sesion'] == '' ? true : false);
	$days=fechas_app('psicologia');
	$c[]=new cmp('idpsi','h','20',$_POST['id'],$w.' '.$o,'','',null,null,true,$u,'','col-1');
	// $c[]=new cmp('','h','3',$d['psi_tipo_doc'],$w.' '.$o,'Tipo documento','psi_tipo_doc',null,null,true,true,'','col-5');
	// $c[]=new cmp('psi_documento','h','20',$d['psi_documento'],$w.' '.$o,'Numero Documento','psi_documento',null,null,true,true,'','col-5');

	$c[]=new cmp('psi_fecha_sesion','d','10',$d['psi_fecha_sesion'],$w.' '.$o,'Fecha de la Sesion','psi_fecha_sesion',null,null,true,$u,'','col-4',"validDate(this,$days,0);");
	$c[]=new cmp('edad','t','5',$edad,$w.' '.$o,'Edad en Años','edad',null,null,true,false,'','col-4');
	$c[]=new cmp('cod_admin2','s',2,$d['cod_admin2'],$w.' cA2 '.$o,'Codigo Admisión','cod_admin2',null,null,$dis,$u,'','col-4');
	// $c[]=new cmp('psi_sesion','s','3',$j['psi_sesion'],$w.' '.$o,'Sesion','psi_sesion',null,null,true,true,'','col-5');

	$o='infgen_2';
	$c[]=new cmp($o,'e',null,'RESULTADO DE EVALUACION pre EP+',$w);

	$c[]=new cmp('psi_validacion1','a','1500',$d['psi_validacion1'],$w.' '.$o,'1. Elija el problema que más le aflige.','psi_validacion1',null,null,$dis,$dis,'','col-10');

	$c[]=new cmp('psi_validacion2','s','3',$d['psi_validacion2'],$w.' '.$o,'1.1 ¿Cuánto le afectó durante la última semana?','psi_validacion2',null,null,$dis,$dis,'','col-10');
	$c[]=new cmp('psi_validacion3','a','1500',$d['psi_validacion3'],$w.' '.$o,'2 ¿Escoja otro problema que le aflige?','psi_validacion2',null,null,$dis,$dis,'','col-10');
	$c[]=new cmp('psi_validacion4','s','3',$d['psi_validacion4'],$w.' '.$o,'2.2 ¿Cuánto le afectó durante la última semana?','psi_validacion4',null,null,$dis,$dis,'','col-10');
	$c[]=new cmp('psi_validacion5','a','1500',$d['psi_validacion5'],$w.' '.$o,'3. Escoja una cosa que le resulta difícil de hacer por causa de su problema (o problemas).','psi_validacion5',null,null,$dis,$dis,'','col-10');
	$c[]=new cmp('psi_validacion6','s','3',$d['psi_validacion6'],$w.' '.$o,'3.3 ¿Cuán difícil le ha resultado hacer esto durante la última semana?','psi_validacion6',null,null,$dis,$dis,'','col-10');
	$c[]=new cmp('psi_validacion7','s','3',$d['psi_validacion7'],$w.' '.$o,'4. ¿Cómo se sintió durante la última semana?','psi_validacion7',null,null,$dis,$dis,'','col-10');
	$c[]=new cmp('psi_validacion8','a','1500',$d['psi_validacion8'],$w.' '.$o,'Actividad A Desarrollar 1','psi_validacion8',null,null,true,true,'','col-10');
	$c[]=new cmp('psi_validacion9','a','1500',$d['psi_validacion9'],$w.' '.$o,'Actividad A Desarrollar 2','psi_validacion9',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion10','a','1500',$d['psi_validacion10'],$w.' '.$o,'Actividad A Desarrollar 3','psi_validacion10',null,null,false,true,'','col-10');
	$c[]=new cmp('contin_caso','s','3',$d['contin_caso'],$w.' '.$o,'Continuidad del caso','contin_caso',null,null,$u,$u,'','col-10',"enabDepeValu('contin_caso','cA2',['4','5'],false);");
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_sesion2(){
	// print_r($_POST);
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT CONCAT(id_people,'_',id_sesion2) id,id_people,`psi_fecha_sesion`,cod_admin2 ,`psi_validacion1`, `psi_validacion2`, `psi_validacion3`, `psi_validacion4`, `psi_validacion5`, `psi_validacion6`, `psi_validacion7`, `psi_validacion8`, `psi_validacion9`, `psi_validacion10`
		,TIMESTAMPDIFF(YEAR, fecha_nacimiento, IFNULL(psi_fecha_sesion, CURDATE())) AS edad, contin_caso 
		FROM psi_sesion2 S2
		LEFT JOIN person P ON  S2.id_people=P.idpeople
		WHERE P.idpeople='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
		return $info['responseResult'][0];
	} 
}

function get_sesion2_info(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT id_people,psi_fecha_sesion,psi_sesion,cod_admin2,psi_validacion1,psi_validacion2,psi_validacion3,psi_validacion4,psi_validacion5,psi_validacion6,psi_validacion7,psi_validacion8,psi_validacion9,psi_validacion10,estado
		FROM `psi_sesion2` WHERE id_people='{$id[0]}'";
		$info=datos_mysql($sql);
    	// echo $sql."=>".$_POST['id'];
		return $info['responseResult'][0];
	} 
}

function focus_sesion2(){
	return 'sesion2';
}

function men_sesion2(){
 $rta=cap_menus('sesion2','pro');
 return $rta;
}
 
function gra_sesion2(){
// print_r($_POST);
	$idpsi=divide($_POST['idpsi']);
	if(count($idpsi)==2){ 
		$sql = "INSERT INTO psi_sesion2 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
		$params = [
		['type' => 'i', 'value' => NULL],
		['type' => 'i', 'value' => $idpsi[0]],
		['type' => 's', 'value' => $_POST['psi_fecha_sesion']],
		['type' => 'i', 'value' => $_POST['cod_admin2']],
		['type' => 's', 'value' => $_POST['psi_validacion1']],
		['type' => 's', 'value' => $_POST['psi_validacion2']],
		['type' => 's', 'value' => $_POST['psi_validacion3']],
		['type' => 's', 'value' => $_POST['psi_validacion4']],
		['type' => 's', 'value' => $_POST['psi_validacion5']],
		['type' => 's', 'value' => $_POST['psi_validacion6']],
		['type' => 's', 'value' => $_POST['psi_validacion7']],
		['type' => 's', 'value' => $_POST['psi_validacion8']],
		['type' => 's', 'value' => $_POST['psi_validacion9']],
		['type' => 's', 'value' => $_POST['psi_validacion10']],
		['type' => 's', 'value' => $_POST['contin_caso']],
		['type' => 'i', 'value' => $_SESSION['us_sds']],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => NULL],
		['type' => 'i', 'value' => 2]
		];
		return $rta = mysql_prepd($sql, $params);
	
	} else {
		/* $sql="UPDATE psi_sesion2 SET 
				psi_validacion1 = TRIM(upper('{$_POST['psi_validacion1']}')),
				psi_validacion5 = TRIM(upper('{$_POST['psi_validacion5']}')),
				psi_validacion8 = TRIM(upper('{$_POST['psi_validacion8']}')),
				psi_validacion9 = TRIM(upper('{$_POST['psi_validacion9']}')),
				psi_validacion10 = TRIM(upper('{$_POST['psi_validacion10']}')),
		usu_update =TRIM(UPPER('{$_SESSION['us_sds']}')),
		fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
		WHERE id_sesion2='$idpsi[2]'";
	  //echo $x;
	//   echo $sql; 	
	return $rta=dato_mysql($sql);
		 
		/* $sql="INSERT INTO psi_sesion2 VALUES (
					NULL,
					'$idpsi[0]',
					'$idpsi[1]',
					trim(upper('{$_POST['psi_fecha_sesion']}')),
					trim(upper('{$_POST['cod_admin2']}')),
					trim(upper('{$_POST['psi_validacion1']}')),
					trim(upper('{$_POST['psi_validacion2']}')),
					trim(upper('{$_POST['psi_validacion3']}')),
					trim(upper('{$_POST['psi_validacion4']}')),
					trim(upper('{$_POST['psi_validacion5']}')),
					trim(upper('{$_POST['psi_validacion6']}')),
					trim(upper('{$_POST['psi_validacion7']}')),
					trim(upper('{$_POST['psi_validacion8']}')),
					trim(upper('{$_POST['psi_validacion9']}')),
					trim(upper('{$_POST['psi_validacion10']}')),
					trim(upper('{$_POST['contin_caso']}')),
					DATE_SUB(NOW(), INTERVAL 5 HOUR),
					{$_SESSION['us_sds']},
					NULL,
					NULL,
					'2')"; */
		// echo $sql;
	}
	//return "correctamente";
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////




//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cmp_sesion_fin() {
	$rta="";

	$hoy=date('Y-m-d');

	$t=['tipo_doc'=>'','documento'=>'','psi_fecha_sesion'=>'','psi_validacion1'=>'','psi_validacion2'=>'','psi_validacion3'=>'','cod_admisfin'=>'','psi_validacion4'=>'','psi_validacion5'=>'','psi_validacion6'=>'','psi_validacion7'=>'','psi_validacion8'=>'','psi_validacion9'=>'','psi_validacion10'=>'','psi_validacion11'=>'','psi_validacion12'=>'','psi_validacion13'=>'','psi_validacion14'=>'','psi_validacion15'=>'','psi_validacion17'=>'','psi_validacion18'=>'','psi_validacion19'=>'','zung1'=>'','ham1'=>'','who1'=>'','zung2'=>'','ham2'=>'','who2'=>''];

	$w='sesion_fin';
	$d=get_sesion_fin();
	$j=get_sesion_fin_info();
	$r=get_Moment1();
	$q=get_Moment2();
	$total=psyPrevia();
	
	if ($d=="") {$d=$t;}
	if ($j=="") {$j=$t;}
	if ($r=="") {$r=$t;}
	if ($q=="") {$q=$t;}
	$ed = ($d['edad']<18) ? false :true;
	$u=($d['id_people']=='')?true:false;
	$o='infgen';
	$days=fechas_app('psicologia');
	$ob=($j['psi_validacion14']=='')?true:false;
	$c[]=new cmp($o,'e',null,'Sesion Final',$w);	
	$c[]=new cmp('idpsi','h','20', $_POST['id'],$w.' '.$o,'','',null,null,true,$u,'','col-1');
	$c[]=new cmp('psi_fecha_sesion','d','10',$j['psi_fecha_sesion'],$w.' '.$o,'Fecha de la Sesion','psi_fecha_sesion',null,null,true,$ob,'','col-10',"validDate(this,$days,0);");
	//$c[]=new cmp('psi_sesion','t','50',$j['psi_sesion'],$w.' '.$o,'Sesion','psi_sesion',null,null,true,true,'','col-5');
	$c[]=new cmp('zung_ini','a','1500',$r['zung1'],$w.' '.$o,'RESULTADO TAMIZAJE ZUNG INICIAL','zung_ini',null,null,false,false,'','col-3');
	$c[]=new cmp('hamilton_ini','a','1500',$r['ham1'],$w.' '.$o,'RESULTADO TAMIZAJE HAMILTON INICIAL','hamilton_ini',null,null,false,false,'','col-3');
	$c[]=new cmp('whodas_ini','a','1500',$r['who1'],$w.' '.$o,'RESULTADO TAMIZAJE (WHODAS 2.0) INICIAL','whodas_ini',null,null,false,false,'','col-4');

	$c[]=new cmp('psi_validacion1','a','1500',$q['zung2'],$w.' '.$o,'RESULTADO TAMIZAJE ZUNG FINAL','psi_validacion1',null,null,false,false,'','col-3');
	$c[]=new cmp('psi_validacion2','a','1500',$q['ham2'],$w.' '.$o,'RESULTADO TAMIZAJE HAMILTON FINAL','psi_validacion2',null,null,false,false,'','col-3');
	$c[]=new cmp('psi_validacion3','a','1500',$q['who2'],$w.' '.$o,'RESULTADO TAMIZAJE (WHODAS 2.0) FINAL','psi_validacion3',null,null,false,false,'','col-4');

	$o='infgen_2';
	$c[]=new cmp($o,'e',null,'RESULTADO DE EVALUACION pre EP+',$w);
	$c[]=new cmp('cod_admisfin','s',2,$j['cod_admisfin'],$w.' '.$o,'Codigo Admisión','cod_admisfin',null,null,true,true,'','col-4');
	
	/* $sql="SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad
		FROM person WHERE id_people='{$_POST['id']}'";
		$info=datos_mysql($sql);
		$edad=$info['responseResult'][0]['edad'];
		$ed = ($edad<18) ? false :true; */


	$c[]=new cmp('psi_validacion4','a','1500',$j['psi_validacion4'],$w.' '.$o,'1. Éste es el problema que le preocupaba más, según usted nos dijo cuando le preguntamos al principio.','psi_validacion4',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion5','s','3',$j['psi_validacion5'],$w.' '.$o,'1,1. ¿Cuánto le afectó durante la última semana?','psi_validacion5',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion6','a','1500',$j['psi_validacion6'],$w.' '.$o,'2. Éste es el otro problema que le preocupa, según usted nos dijo cuando le preguntamos al principio.','psi_validacion6',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion7','s','3',$j['psi_validacion7'],$w.' '.$o,'2,1. ¿Cuánto le afectó durante la última semana?','psi_validacion7',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion8','a','1500',$j['psi_validacion8'],$w.' '.$o,'3. Esto es lo que le ha costado hacer, según usted nos dijo cuando le preguntamos al principio.','psi_validacion8',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion9','s','3',$j['psi_validacion9'],$w.' '.$o,'3,1. ¿Cuán difícil le ha resultado hacer esto durante la última semana?','psi_validacion9',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion10','s','3',$j['psi_validacion10'],$w.' '.$o,'3,2. ¿Cómo se sintió durante la última semana?','psi_validacion10',null,null,$ed,$ed,'','col-10',"sumPsy1();");
	$c[]=new cmp('psi_validacion11','a','1500',$j['psi_validacion11'],$w.' '.$o,'4. Durante la intervención, tal vez usted descubrió que otros problemas se volvieron importantes. Si es así, ¿cuánto le han afectado estos problemas durante la última semana?','psi_validacion11',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion12','s','3',$j['psi_validacion12'],$w.' '.$o,'4,1. ¿Cuánto le afectó durante la última semana?','psi_validacion12',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion13','s','3',$j['psi_validacion13'],$w.' '.$o,'4,2. En comparación con lo que sentía cuando comenzó con la intervención, ¿cómo se siente ahora?','psi_validacion13',null,null,$ed,$ed,'','col-10');
	$c[]=new cmp('psi_validacion14','t','3',$j['psi_validacion14'],$w.' '.$o,'Puntuación total de PSYCHLOPS:','psi_validacion14',null,null,false,false,'','col-5');
	$c[]=new cmp('psi_validacion15','t','3',$total,$w.' '.$o,'Puntuación total de PSYCHLOPS previa a la intervención:','psi_validacion15',null,null,false,false,'','col-5');
	
	$c[]=new cmp('psi_validacion17','o','2',$j['psi_validacion17'],$w.' '.$o,'Termino Tratamiento','psi_validacion17',null,null,true,true,'','col-10');
	$c[]=new cmp('psi_validacion18','o','2',$j['psi_validacion18'],$w.' oculto '.$o,'Disentimiento','psi_validacion18',null,null,false,true,'','col-10');
	$c[]=new cmp('psi_validacion19','o','2',$j['psi_validacion19'],$w.' '.$o,'Se logro fortalecimiento herramientas de afrontamiento','psi_validacion19',null,null,false,true,'','col-10');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function psyPrevia(){
	$id=divide($_POST['id']);
	$sql1="SELECT (psi_validacion2+psi_validacion4+psi_validacion6+psi_validacion7)  suma1 
	from psi_sesion2 where id_people='{$id[0]}'";	
	$sum=datos_mysql($sql1);
	return $sum['responseResult'][0]['suma1'];
	// return print_r($_POST);
}

function get_Moment1(){
	$id=divide($_POST['id']);
		$sql="SELECT Z.analisis zung1,H.analisis ham1,W.analisis who1
		FROM hog_tam_zung Z 
		LEFT JOIN hog_tam_hamilton H ON Z.idpeople=H.idpeople
		LEFT JOIN hog_tam_whodas W ON Z.idpeople=W.idpeople
				WHERE Z.idpeople ='".$id[0]."' AND H.momento=1 AND W.momento=1";//

	  $datos=datos_mysql($sql);
	  if (isset($datos['responseResult'][0])) {
			return $datos['responseResult'][0];
		} else {
			return "";
		}
}

function get_Moment2(){
	$id=divide($_POST['id']);
		$sql="SELECT Z.analisis zung2,H.analisis ham2,W.analisis who2
		FROM hog_tam_zung Z 
		LEFT JOIN hog_tam_hamilton H ON Z.idpeople=H.idpeople
		LEFT JOIN hog_tam_whodas W ON Z.idpeople=W.idpeople
				WHERE Z.idpeople ='".$id[0]."' AND Z.momento=2 AND H.momento=2 AND W.momento=2";//

	  $datos=datos_mysql($sql);
	  if (isset($datos['responseResult'][0])) {
			return $datos['responseResult'][0];
		} else {
			return "";
		}
}

function get_DataWhodas() {
	$id=divide($_POST['id']);
		$sql="SELECT porcentaje_total
				FROM hog_tam_whodas
				WHERE idpeople ='".$id[0]."'";

	  $datos=datos_mysql($sql);
	  if (isset($datos['responseResult'][0])) {
			return $datos['responseResult'][0];
		} else {
			return "";
		}
}

function get_sesion_fin(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,P.idpeople id_people,P.tipo_doc tipo_doc,P.idpersona documento,psi_validacion1,psi_validacion2,psi_validacion3,psi_validacion4,psi_validacion5,psi_validacion6,psi_validacion7,psi_validacion8,psi_validacion9,psi_validacion10,PF.estado
		FROM `psi_psicologia` PF
		left join person P ON  PF.id_people=P.idpeople
		WHERE id_people='{$id[0]}'";

// sector_catastral,'_',nummanzana,'_',predio_num,'_',estrategia,'_',estado_v
		$info=datos_mysql($sql);
    	// echo $sql."=>".$_POST['id'];
		return $info['responseResult'][0];
	} 
}

function get_sesion_fin_info(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT psi_fecha_sesion,cod_admisfin,psi_validacion1,psi_validacion2,psi_validacion3,psi_validacion4,psi_validacion5,psi_validacion6,psi_validacion7,psi_validacion8,psi_validacion9,psi_validacion10,psi_validacion11,psi_validacion12,psi_validacion13,psi_validacion14,psi_validacion15,psi_validacion17,psi_validacion18,psi_validacion19,estado
		FROM `psi_sesion_fin` WHERE id_people='{$id[0]}'";
		$info=datos_mysql($sql);
		if (isset($info['responseResult'][0])) {
			return $info['responseResult'][0];
		} else {
			return "";
		}
	} 
}

function focus_sesion_fin(){
	return 'sesion_fin';
}

function men_sesion_fin(){
 $rta=cap_menus('sesion_fin','pro');
 return $rta;
}
 
function gra_sesion_fin(){

	$idpsi=divide($_POST['idpsi']);
	$sql = "INSERT INTO psi_sesion_fin VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?,?)";
	$params = [
	['type' => 'i', 'value' => NULL],
	['type' => 's', 'value' => $idpsi[0]],
	['type' => 's', 'value' => $_POST['psi_fecha_sesion']],
	['type' => 'i', 'value' => $_POST['cod_admisfin']],
	['type' => 's', 'value' => $_POST['zung_ini']],
	['type' => 's', 'value' => $_POST['hamilton_ini']],
	['type' => 's', 'value' => $_POST['whodas_ini']],
	['type' => 's', 'value' => $_POST['psi_validacion1']],
	['type' => 's', 'value' => $_POST['psi_validacion2']],
	['type' => 's', 'value' => $_POST['psi_validacion3']],
	['type' => 's', 'value' => $_POST['psi_validacion4']],
	['type' => 's', 'value' => $_POST['psi_validacion5']],
	['type' => 's', 'value' => $_POST['psi_validacion6']],
	['type' => 's', 'value' => $_POST['psi_validacion7']],
	['type' => 's', 'value' => $_POST['psi_validacion8']],
	['type' => 's', 'value' => $_POST['psi_validacion9']],
	['type' => 's', 'value' => $_POST['psi_validacion10']],
	['type' => 's', 'value' => $_POST['psi_validacion11']],
	['type' => 's', 'value' => $_POST['psi_validacion12']],
	['type' => 's', 'value' => $_POST['psi_validacion13']],
	['type' => 's', 'value' => $_POST['psi_validacion14']],
	['type' => 's', 'value' => $_POST['psi_validacion15']],
	['type' => 's', 'value' => $_POST['psi_validacion17']],
	['type' => 's', 'value' => $_POST['psi_validacion18']],
	['type' => 's', 'value' => $_POST['psi_validacion19']],
	['type' => 'i', 'value' => $_SESSION['us_sds']],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => NULL],
	['type' => 's', 'value' => 'A']
	];
	return $rta = mysql_prepd($sql, $params);
	
	
	/* $sql="INSERT INTO psi_sesion_fin VALUES (
		NULL,
					$idpsi[0],
					trim(upper('{$_POST['psi_fecha_sesion']}')),
					trim(upper('{$_POST['cod_admisfin']}')),
					trim(upper('{$_POST['zung_ini']}')),
					trim(upper('{$_POST['hamilton_ini']}')),
					trim(upper('{$_POST['whodas_ini']}')),
					trim(upper('{$_POST['psi_validacion1']}')),
					trim(upper('{$_POST['psi_validacion2']}')),
					trim(upper('{$_POST['psi_validacion3']}')),
					trim(upper('{$_POST['psi_validacion4']}')),
					trim(upper('{$_POST['psi_validacion5']}')),
					trim(upper('{$_POST['psi_validacion6']}')),
					trim(upper('{$_POST['psi_validacion7']}')),
					trim(upper('{$_POST['psi_validacion8']}')),
					trim(upper('{$_POST['psi_validacion9']}')),
					trim(upper('{$_POST['psi_validacion10']}')),
					trim(upper('{$_POST['psi_validacion11']}')),
					trim(upper('{$_POST['psi_validacion12']}')),
					trim(upper('{$_POST['psi_validacion13']}')),
					trim(upper('{$_POST['psi_validacion14']}')),
					trim(upper('{$_POST['psi_validacion15']}')),
					trim(upper('{$_POST['psi_validacion17']}')),
					trim(upper('{$_POST['psi_validacion18']}')),
					trim(upper('{$_POST['psi_validacion19']}')),
					DATE_SUB(NOW(), INTERVAL 5 HOUR),
					{$_SESSION['us_sds']},
					NULL,
					NULL,
					'A')";
					return $rta=dato_mysql($sql); */

}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function opc_cod_admisfin($id=''){
	$cod=divide($_REQUEST['id']);
	return opc_sql("SELECT cod_admin,CONCAT_WS(' - ',cod_admin,FN_CATALOGODESC(127,final_consul))  from adm_facturacion af WHERE af.idpeople='".$cod[0]."' AND cod_cups IN(9,16) AND final_consul=25 ORDER BY 1", $id);
}

function opc_cod_admin($id=''){
	$cod=divide($_REQUEST['id']);
	return opc_sql("SELECT cod_admin,CONCAT_WS(' - ',cod_admin,FN_CATALOGODESC(127,final_consul))  from adm_facturacion af WHERE af.idpeople='".$cod[0]."' AND cod_cups=8 AND final_consul=15 ORDER BY 1", $id);
}

function opc_cod_admin2($id=''){
	$cod=divide($_REQUEST['id']);
	return opc_sql("SELECT cod_admin,CONCAT_WS(' - ',cod_admin,FN_CATALOGODESC(127,final_consul))  from adm_facturacion af WHERE af.idpeople='".$cod[0]."' AND cod_cups IN(9,16) AND final_consul=16 ORDER BY 1", $id);
}
function opc_genero($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=19 and estado='A' ORDER BY 1",$id);
	}
	function opc_oriensexual($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=49 and estado='A' ORDER BY 1",$id);
	}
function opc_contin_caso($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 160 and estado='A' ORDER BY 1",$id);
}
function opc_letra1($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='1' and estado='A' ORDER BY 2 ",$id);
}
function opc_rango1($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='2' and estado='A' ORDER BY 1 ",$id);
}
function opc_psi_diag12($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='3' and estado='A' ORDER BY 1 ",$id);
}

function opc_letra1rango1(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='2' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}

function opc_rango1psi_diag12(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='3' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		// echo $sql;
		return json_encode($info['responseResult']);
	} 
}

function opc_psi_tipo_doc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 1 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_caso($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 159 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion3($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 122 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion5($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion13($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion14($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 163 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion2($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion4($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion6($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion7($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion9($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion10($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}
function opc_psi_validacion12($id='') {
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 124 and estado='A' ORDER BY 1",$id);
}

function opc_psi_sexo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}

function opc_psi_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=49 and estado='A' ORDER BY 1",$id);
}

function opc_psi_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY 1",$id);
}

function opc_psi_regimen($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=17 and estado='A' ORDER BY 1",$id);
}

function opc_psi_eapb($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=18 and estado='A' ORDER BY 1",$id);
}

?>