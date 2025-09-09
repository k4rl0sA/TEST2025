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

function perfilUsu(){
	$perfi=datos_mysql("SELECT perfil FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'");
	$perfil = (!$perfi['responseResult']) ? '' : $perfi['responseResult'][0]['perfil'] ;
	return $perfil; 
}


function cmp_gestionusu(){
	$rta="";
	$hoy=date('Y-m-d');
	$t=['gestion'=>'','perfil'=>'','documento'=>'','usuarios'=>'','nombre'=>'','correo'=>'','subred'=>'','bina'=>'','territorio'=>'','perfiln'=>''];
	$d='';
	if ($d==""){$d=$t;}
	$w='adm_usuarios';
	$o='infusu';
	$c[]=new cmp($o,'e',null,'GESTIÓN DE USUARIOS',$w);
	$c[]=new cmp('gestion','s','3',$d['gestion'],$w.' '.$o,'Acción','gestion',null,'',true,true,'','col-2',"enabLoca('gestion','GsT');enClSe('gestion','GsT',[['Rpw'],['Rpw'],['cUS'],['cRL']]);");
	$c[]=new cmp('perfil','s','3',$d['perfil'],$w.' '.$o,'Perfil','perfil',null,'',true,true,'','col-1',"enClSeDe('gestion','perfil','prF',[[],['TEr'],[],['bIN'],[]]);",['usuarios']);
	$c[]=new cmp('documento','t','20',$d['documento'],$w.' GsT cUS '.$o,'N° Documento','documento',null,'',false,false,'','col-15');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' GsT cUS '.$o,'Nombres y Apellidos','nombre',null,'',false,false,'','col-3');
	$c[]=new cmp('correo','t','30',$d['correo'],$w.' GsT cUS '.$o,'Correo','correo',null,'',false,false,'','col-25');
	$c[]=new cmp('bina','s','3',$d['bina'],$w.'  prF bIN '.$o,'bina','bina',null,'',false,false,'','col-2');
	$c[]=new cmp('territorio','s','3',$d['territorio'],$w.' prF TEr '.$o,'territorio','territorio',null,'',false,false,'','col-2');
	$c[]=new cmp('usuarios','s','20',$d['usuarios'],$w.' cRL Rpw  GsT '.$o,'Usuarios','usuarios',null,'',false,false,'','col-4');
	$c[]=new cmp('perfiln','s','3',$d['perfiln'],$w.' GsT cRL '.$o,'Perfil Nuevo','Perfil',null,'',true,false,'','col-15',"enClSe('perfiln','prF',[['bIN'],['TEr']]);");
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	$rta.="<center><button style='background-color:#4d4eef;border-radius:12px;color:white;padding:12px;text-align:center;cursor:pointer;' type='button' Onclick=\"grabar('adm_usuarios','adm_usuarios');\">Guardar</button></center>";
	return $rta;
}


function lis_adm_usuarios(){
	/* $info=datos_mysql("SELECT COUNT(*) total FROM `adm_usuarios` C 
	JOIN usuarios U ON C.usu_creo = U.id_usuario 
	WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}') AND usu_creo='{$_SESSION['us_sds']}'".whe_adm_usuarios());
	$total=$info['responseResult'][0]['total'];
	$regxPag=5;
	$pag=(isset($_POST['pag-adm_usuarios']))? ($_POST['pag-adm_usuarios']-1)* $regxPag:0;
	
	$sql="SELECT id_gestusu ACCIONES, 
	accion,documento,C.nombres,C.correo,C.perfil,C.subred,bina_territorio,C.componente,respuesta,U.nombre,fecha_create,C.estado
	FROM `adm_usuarios` C 
	JOIN usuarios U ON C.usu_creo = U.id_usuario
	WHERE U.subred IN (select subred from usuarios where id_usuario='{$_SESSION['us_sds']}')  AND usu_creo='{$_SESSION['us_sds']}'";
	$sql.=whe_adm_usuarios();
	$sql.=" ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sq;
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"adm_usuarios",$regxPag); */
}

function whe_adm_usuarios() {
	$sql = "";
	 if ($_POST['fcaso'])
		$sql .= " AND id_gestusu = '".$_POST['fcaso']."'";
	if ($_POST['fdes']) {
		if ($_POST['fhas']) {
			$sql .= " AND fecha_create >='".$_POST['fdes']." 00:00:00' AND fecha_create <='".$_POST['fhas']." 23:59:59'";
		} else {
			$sql .= " AND fecha_create >='".$_POST['fdes']." 00:00:00' AND fecha_create <='". $_POST['fdes']." 23:59:59'";
		}
	}
	return $sql;
}

function cmp_planos(){
	$rta="";
	$until_day_open=14;//dia del mes fecha abierta
	$ini = (date('d')>$until_day_open) ? -date('d')-31:-date('d')-29 ;//fechas abiertas hasta un determinado dia -41
	//$ini=date('d')<11 ?-date('d')-31:-date('d');//normal
	$t=['proceso'=>'','rol'=>'','documento'=>'','usuarios'=>'','descarga'=>'','fechad'=>'','fechah'=>''];
	$d='';
	if ($d==""){$d=$t;}
	$w='gestion';
	$o='infusu';
	$c[]=new cmp($o,'e',null,'DESCARGA DE PLANOS',$w);
	$c[]=new cmp('proceso','s',3,$d['proceso'],$w.' DwL '.$o,'Proceso','proceso',null,'',true,true,'','col-35');
	$c[]=new cmp('fechad','d',10,$d['fechad'],$w.' DwL '.$o,'Desde','proceso',null,'',true,true,'','col-2',"validDate(this,$ini,0)");
	$c[]=new cmp('fechah','d',10,$d['fechah'],$w.' DwL '.$o,'Hasta','proceso',null,'',true,true,'','col-2',"validDate(this,$ini,0)");
	// $c[]=new cmp('descarga','t',100,$d['descarga'],$w.' '.$o,'Ultima Descarga','rol',null,'',false,false,'','col-5');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	$rta.="<center><button style='background-color:#4d4eef;border-radius:12px;color:white;padding:12px;text-align:center;cursor:pointer;' type='button' Onclick=\"DownloadCsv('lis','planos','fapp');grabar('gestion',this);\">Descargar</button></center>";//DownloadCsv('lis','plano','DwL
	return $rta;
}

function gra_gestion(){
	/* $name=get_tabla($_POST['proceso']);
	if($name!=='[]'){
		return "Error: msj['Ya se realizo la descarga por el usuario $name']";
		exit;
	}else{ */
	$sql="INSERT INTO planos 
	VALUES(NULL,(SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."'),'1',trim(upper('{$_POST['proceso']}')),
	'{$_POST['fechad']}','{$_POST['fechah']}',TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR))";
	/* 
	1=descargar
	2=actualizar
	3=restaurar
	4=crear
	5=rol
	6=adscripcion 
	*/
		// echo $sq;
		$rta=dato_mysql($sql);
  return $rta;
	// }
}

function gra_adm_usuarios(){
$gestion = cleanTxt($_POST['gestion']);
$documento = cleanTxt($_POST['documento']);
$nombre = cleanTxt($_POST['nombre']);
$correo = cleanTxt($_POST['correo']);
$perfil = cleanTxt($_POST['perfil']);
$perfiln = cleanTxt($_POST['perfiln']);
$bina_territorio = cleanTxt($_POST['bina_territorio']);
$componente = cleanTxt($_POST['componente']);

	$sql="INSERT INTO adm_usuarios 
	VALUES(NULL,$gestion,$documento,$nombre,$correo,$perfil,(SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."'),
	$bina_territorio,$componente,$perfiln,{$_SESSION['us_sds']},DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
	$rta=dato_mysql($sql);
	return $rta;
}

function get_tabla($a){
	//`atencion_fechaatencion`, `atencion_codigocups`, `atencion_finalidadconsulta`, `atencion_peso`, `atencion_talla`, `atencion_sistolica`, `atencion_diastolica`, `atencion_abdominal`, `atencion_brazo`, `atencion_diagnosticoprincipal`, `atencion_diagnosticorelacion1`, `atencion_diagnosticorelacion2`, `atencion_diagnosticorelacion3`, `atencion_fertil`, `atencion_preconcepcional`, `atencion_metodo`, `atencion_anticonceptivo`, `atencion_planificacion`, `atencion_mestruacion`, `atencion_gestante`, `atencion_gestaciones`, `atencion_partos`, `atencion_abortos`, `atencion_cesarias`, `atencion_vivos`, `atencion_muertos`, `atencion_vacunaciongestante`, `atencion_edadgestacion`, `atencion_ultimagestacion`, `atencion_probableparto`, `atencion_prenatal`, `atencion_fechaparto`, `atencion_rpsicosocial`, `atencion_robstetrico`, `atencion_rtromboembo`, `atencion_rdepresion`, `atencion_sifilisgestacional`, `atencion_sifiliscongenita`, `atencion_morbilidad`, `atencion_hepatitisb`, `atencion_vih`, `atencion_cronico`, `atencion_asistenciacronica`, `atencion_tratamiento`, `atencion_vacunascronico`, `atencion_menos5anios`, `atencion_esquemavacuna`, `atencion_signoalarma`, `atencion_cualalarma`, `atencion_dxnutricional`, `atencion_eventointeres`, `atencion_evento`, `atencion_cualevento`, `atencion_sirc`, `atencion_rutasirc`, `atencion_remision`, `atencion_cualremision`, `atencion_ordenpsicologia`, `atencion_ordenvacunacion`, `atencion_vacunacion`, `atencion_ordenlaboratorio`, `atencion_laboratorios`, `atencion_ordenimagenes`, `atencion_imagenes`, `atencion_ordenmedicamentos`, `atencion_medicamentos`, `atencion_rutacontinuidad`, `atencion_continuidad`, `atencion_relevo`  ON a.atencion_idpersona = b.idpersona AND a.atencion_tipodoc = b.tipo_doc
	$hoy=date('Y-m-d');
	$sql="SELECT u.nombre nombre FROM planos m left join usuarios u ON m.usu_creo=u.id_usuario
 	where	accion=1 
	AND m.subred=(SELECT subred FROM usuarios where id_usuario='{$_SESSION['us_sds']}') 
	AND tabla='$a' AND DATE(fecha_create)='$hoy'";
	// echo $sq;
	$info=datos_mysql($sql);
	// echo $info;
	if (isset($info['responseResult'][0])) {
		return $info['responseResult'][0]['nombre'];
	}else{
		return '[]';
	}
}

function lis_planos() {
	$clave = random_bytes(32);
    switch ($_REQUEST['proceso']) {
        case '1':
			$tab = "Asignacion Predios";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_asigpre($tab);
		break;
        case '2':
			$tab = "Gestion Predios";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_gestpre($tab);
           break;
        case '3':
			$tab = "Caracterizaciones";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_caract($tab);
			break;
		case '4':
			$tab = "Plan_de_Cuidado_Familiar";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_plancui($tab);
            break;	
        case '5':
			$tab = "Compromisos_Plan_de_Cuidado_Familiar";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_plancomp($tab);
            break;
        case '6':
			$tab = "Toma_de_Medidas_y_Signos";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_signos($tab);
            break;	
        case '7':
			$tab = "Toma_de_Alertas";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_alertas($tab);
            break;	
        case '8':
			$tab = "Riesgos_Ambientales";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_riesamb($tab);
            break;	
        case '9':
			$tab = "Eventos_VSP_Generados";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_eventos($tab);
            break;	
        case '10':
			$tab = "VSP_Acompañamiento_Psicosocial";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_acompsic($tab);
            break;
        case '11':
			$tab = "VSP_Apoyo_Psicosocial_En_Duelo";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_apopsicduel($tab);
            break;
        case '12':
			$tab = "VSP_BPN_Pretermino";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_bpnpret($tab);
            break;
        case '13':
			$tab = "VSP_BPN_a_Termino";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_bpnterm($tab);
            break;
        case '14':
			$tab = "VSP_Cancer_Infantil";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_cancinfa($tab);
            break;
        case '15':
			$tab = "VSP_Conducta_Suicida";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_condsuic($tab);
            break;
        case '16':
			$tab = "VSP_Cronicos";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_cronicos($tab);
            break;
        case '17':
			$tab = "VSP_DNT_Severa_y_Moderada";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_dntsevymod($tab);
            break;
        case '18':
			$tab = "VSP_Era_Ira";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_eraira($tab);
            break;
        case '19':
			$tab = "VSP_Gestantes";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_Gestantes($tab);
            break;
        case '20':
			$tab = "VSP_HB_Gestacional";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_hbgest($tab);
            break;
        case '21':
			$tab = "VSP_Morbilidad_Materna_Extrema";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_mme($tab);
            break;
        case '22':
			$tab = "VSP_Hipotiroidismo";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_mnehosp($tab);
            break;
        case '23':
			$tab = "VSP_Otros_Casos_Priorizados";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_otroprio($tab);
            break;
        case '24':
			$tab = "VSP_Salud_Oral";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_saludoral($tab);
            break;
        case '25':
			$tab = "VSP_Sifilis_Congenita";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_sificong($tab);
            break;
        case '26':
			$tab = "VSP_Sifilis_Gestacional";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_sifigest($tab);
            break;
        case '27':
			$tab = "VSP_VIH_Gestacional";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_vihgest($tab);
            break;
        case '28':
			$tab = "VSP_Violencia_En_Gestantes";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_violges($tab);
            break;
        case '29':
			$tab = "VSP_Violencia_Reiterada";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_violreite($tab);
            break;
        case '30':
			$tab = "Usuarios_Caracterizados";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_usercreate($tab);
            break;
        case '31':
			$tab = "Tamizaje_Apgar_Familiar";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_apgar($tab);
            break;
        case '32':
			$tab = "Tamizaje_Cope";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_cope($tab);
            break;
         case '33':
			$tab = "Tamizaje_Epoc";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_epoc($tab);
            break;    
        case '34':
			$tab = "Tamizaje_Findrisc";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_findrisc($tab);
            break;
        case '35':
			$tab = "Tamizaje_OMS";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_oms($tab);
            break;    
        case '36':
			$tab = "Admision";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_admisi($tab);
            break;
        case '37':
			$tab = "Atenciones";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_atencion($tab);
            break;
        case '38':
			$tab = "Plan_de_cuidado_Individual";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_pcindi($tab);
            break;
		case '39':
			$tab = "Familias_Creadas";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_famcrea($tab);
			break;
		case '40':
			$tab = "Gestion_Ruteo";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_gesrut($tab);
			break;
		case '41':
			$tab = "Identificacion_RBC";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_rbc($tab);
			break;
		case '42':
			$tab = "Tamizaje_Epoc";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_e($tab);
			break;
		case '43':
			$tab = "Sesiones_RBC";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_rbc_ses($tab);
			break;
		case '44':
			$tab = "Sesiones_Colectivas";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_sescole($tab);
			break;
		case '45':
			$tab = "Psicologia_Sesion1";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_psicoses1($tab);
			break;
		case '46':
			$tab = "Psicologia_Sesion2";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_psicoses2($tab);
			break;
		case '47':
			$tab = "Psicologia_Sesion_3_a_la_10";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_psicosesiones($tab);
			break;
		case '48':
			$tab = "Tamizaje_Barthel";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_barthel($tab);
			break;
		case '49':
			$tab = "Tamizaje_Hamilton";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_hamilton($tab);
			break;
		case '50':
			$tab = "Tamizaje_Whodas";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_whodas($tab);
			break;
		case '51':
			$tab = "Tamizaje_Zarit";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_zarit($tab);
			break;
		case '52':
			$tab = "Tamizaje_Zung";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_zung($tab);
			break;
		case '53':
			$tab = "Psicologia_Sesion_Final";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_psifin($tab);
			break;
		case '54':
			$tab = "Servicios_Agendamiento";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_servicAgend($tab);
			break;
		case '55':
			$tab = "Identificación UAIC";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_idenUAIC($tab);
			break;
		case '56':
			$tab = "Seguimientos UAIC";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_SeguimientosUAIC($tab);
			break;
		case '57':
			$tab = "Ruteo_Gestionados";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_ruteoGestionados($tab);
			($tab);
			break;
	    case '58':
			$tab = "Predios_Distrito";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_predios($tab);//SOLO ADM INACTIVO EN CATADETA
			break;
		case '59':
			$tab = "Identificación Embera";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_identEmb($tab);
			break;
		case '60':
			$tab = "Seguimiento Rutinario Embera";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_SeguiRutEmb($tab);
			break;
		case '61':
			$tab = "Seguimiento Hospitalario Embera";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_SeguiHosEmb($tab);
			break;
		case '62':
			$tab = "Frecuencia De Uso";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_frecuencia($tab);
			break;
		case '63':
			$tab = "Agendamiento";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_agendamiento($tab);
			break;
		case '64':
			$tab = "Seguimiento a Compromisos PCF";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_segcomp($tab);
			break;
		case '65':
			$tab = "GESTION PREDIOS";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_gestPredios($tab);
			break;
		case '66':
			$tab = "USUARIOS";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_usuarios($tab);
			break;
		case '67':
			$tab = "Tamizaje RQC";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_tamrqc($tab);
			break;
		case '68':
			$tab = "Tamizaje SRQ";
			$encr = encript($tab, $clave);
			if($tab=decript($encr,$clave))lis_tamsrq($tab);
			break;
	
		default:
        break;    
    }
}



function lis_asigpre($txt){
	$sql="SELECT G.subred AS Subred, G.idgeo AS Cod_Predio, G.localidad AS Localidad, CONCAT('_', G.sector_catastral, G.nummanzana, G.predio_num, G.unidad_habit) AS Cod_Sector_Catastral, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
U.id_usuario AS Cod_Asignado, U.nombre AS Nombre_Asignado, U.perfil AS Perfil_Asignado, A.fecha_create AS Fecha_Asignacion, 
U1.id_usuario AS Cod_Quien_Asigno, U1.nombre AS Nombre_Quien_Asigno, U1.perfil AS Perfil_Quien_Asigno, A.estado AS Estado_Registro
FROM `geo_asig` A
LEFT JOIN hog_geo G ON A.idgeo=G.idgeo
LEFT JOIN usuarios U ON A.doc_asignado=U.id_usuario
LEFT JOIN usuarios U1 ON A.usu_create=U1.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred();
	$sql.=whe_date();
	
	$tot="SELECT count(*) as total FROM `geo_asig` A  LEFT JOIN hog_geo G ON A.idgeo=G.idgeo LEFT JOIN usuarios U ON A.doc_asignado=U.id_usuario LEFT JOIN usuarios U1 ON A.usu_create=U1.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred();
	$tot.=whe_date();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_gestpre($txt){
	$sql="SELECT G.idgeo AS Cod_Predio, A.id_ges AS Cod_Registro, G.subred AS Cod_Subred, FN_CATALOGODESC(72,G.subred) AS Subred, G.zona AS Zona, G.localidad AS Cod_Localidad, FN_CATALOGODESC(2,G.localidad) AS Localidad, G.upz AS Cod_Upz, FN_CATALOGODESC(7,G.upz) AS Upz, G.barrio AS Cod_Barrio, C.descripcion AS Barrio, CONCAT('_', G.sector_catastral, G.nummanzana, G.predio_num) AS Cod_Sector, G.sector_catastral AS Sector_catastral, G.nummanzana AS N°_Manzana, G.predio_num AS N°_Predio, G.unidad_habit AS Unidad_Habitacional, G.direccion AS Direccion, G.vereda AS Vereda, G.cordx AS Coordenada_X, G.cordy AS Coordenada_Y, G.estrato AS Estrato, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
A.direccion_nueva AS Direccion_Nueva, A.vereda_nueva AS Vereda_Nueva, A.cordxn AS Coordenada_X_Nueva, A.cordyn AS Coordenada_Y_Nueva, FN_CATALOGODESC(44,A.estado_v) AS Estado_Visita, FN_CATALOGODESC(5,A.motivo_estado) AS Motivo_Estado, A.usu_creo AS Cod_Usuario, U.nombre AS Nombre_Usuario, U.perfil AS Perfil_Usuario, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro 
FROM `geo_gest` A
LEFT JOIN hog_geo G ON A.idgeo=G.idgeo
LEFT JOIN catadeta C ON G.barrio = C.idcatadeta
LEFT JOIN usuarios U ON A.usu_creo=U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred();
	$sql.=whe_date();
	
	$tot="SELECT count(*) as total FROM `geo_gest` A  LEFT JOIN hog_geo G ON A.idgeo=G.idgeo LEFT JOIN catadeta C ON G.barrio = C.idcatadeta LEFT JOIN usuarios U ON A.usu_creo=U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred();
	$tot.=whe_date();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_caract($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,V.id_viv AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, FN_CATALOGODESC(7,G.upz) AS Upz, G.barrio AS Barrio, G.direccion AS Direccion, G.cordx AS Cordenada_X, G.cordy AS Cordenada_Y, G.estrato AS Estrato, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

F.numfam AS Familia_N°,concat(F.complemento1,' ',F.nuc1,' ',F.complemento2,' ',F.nuc2,' ',F.complemento3,' ',F.nuc3) AS Complementos,F.telefono1 AS Telefono_1,F.telefono2 AS Telefono_2,F.telefono3 AS Telefono_3,

V.fecha AS Fecha_Caracterizacion,FN_CATALOGODESC(215,V.motivoupd) AS Motivo_Caracterizacion, FN_CATALOGODESC(87,V.eventoupd) AS Evento_Notificado, V.fechanot AS Fecha_Notificacion ,V.equipo AS Equipo_Caracterizacion,

FN_CATALOGODESC(166,V.crit_epi) AS CRITERIO_EPIDE,FN_CATALOGODESC(167,V.crit_geo) AS CRITERIO_GEO,FN_CATALOGODESC(168,V.estr_inters) AS ESTRATEGIAS_INTERSEC,FN_CATALOGODESC(169,V.fam_peretn) AS FAM_PERTEN_ETNICA,FN_CATALOGODESC(170,V.fam_rurcer) AS FAMILIAS_RURALIDAD_CER,

FN_CATALOGODESC(4,V.tipo_vivienda) AS TIPO_VIVIENDA,FN_CATALOGODESC(8,V.tenencia) AS TENENCIA_VIVIENDA,V.dormitorios AS DORMITORIOS,V.actividad_economica AS USO_ACTIVIDAD_ECONO, FN_CATALOGODESC(10,V.tipo_familia) AS TIPO_FAMILIA, V.personas AS N°_PERSONAS, FN_CATALOGODESC(13,V.ingreso) AS INGRESO_ECONOMICO_FAM,

V.seg_pre1 AS SEGURIDAD_ALIMEN_PREG1,V.seg_pre2 AS SEGURIDAD_ALIMEN_PREG2,V.seg_pre3 AS SEGURIDAD_ALIMEN_PREG3,V.seg_pre4 AS SEGURIDAD_ALIMEN_PREG4,V.seg_pre5 AS SEGURIDAD_ALIMEN_PREG5,V.seg_pre6 AS SEGURIDAD_ALIMEN_PREG6,V.seg_pre7 AS SEGURIDAD_ALIMEN_PREG7,V.seg_pre8 AS SEGURIDAD_ALIMEN_PREG8,

V.subsidio_1 AS SUBSIDIO_SDIS1,V.subsidio_2 AS SUBSIDIO_SDIS2,V.subsidio_3 AS SUBSIDIO_SDIS3,V.subsidio_4 AS SUBSIDIO_SDIS4,V.subsidio_5 AS SUBSIDIO_SDIS5,V.subsidio_6 AS SUBSIDIO_SDIS6,V.subsidio_7 AS SUBSIDIO_SDIS7,V.subsidio_8 AS SUBSIDIO_SDIS8,V.subsidio_9 AS SUBSIDIO_SDIS9,V.subsidio_10 AS SUBSIDIO_SDIS10,V.subsidio_11 AS SUBSIDIO_SDIS11,V.subsidio_12 AS SUBSIDIO_SDIS12,V.subsidio_13 AS SUBSIDIO_ICBF1,V.subsidio_14 AS SUBSIDIO_ICBF2,V.subsidio_15 AS SUBSIDIO15_SECRE_HABIT,V.subsidio_16 AS SUBSIDIO_CONSEJERIA,V.subsidio_17 AS SUBSIDIO_ONGS, V.subsidio_18 AS SUBSIDIO_FAMILIAS_ACCION,V.subsidio_19 AS SUBSIDIO_RED_UNIDOS,V.subsidio_20 AS SUBSIDIO_SECADE,

V.energia AS SERVICIO_ENERGIA,V.gas AS SERVICIO_GAS_NATURAL,V.acueducto AS SERVICIO_ACUEDUCTO,V.alcantarillado AS SERVICIO_ALCANTAR,V.basuras AS SERVICIO_BASURAS,V.pozo AS POZO,V.aljibe AS ALJIBE,
V.perros AS ANIMALES_PERROS,V.numero_perros AS N°_PERROS,V.perro_vacunas AS N°_PERROS_NOVACU,V.perro_esterilizado AS N°_PERROS_NOESTER,V.gatos AS ANIMALES_GATOS,V.numero_gatos AS N°_GATOS,V.gato_vacunas AS N°_GATOS_NOVACU,V.gato_esterilizado AS N°_GATOS_NOESTER,V.otros AS OTROS_ANIMALES,

V.facamb1 AS FACTORES_AMBIEN_PRE1,V.facamb2 AS FACTORES_AMBIEN_PRE2,V.facamb3 AS FACTORES_AMBIEN_PRE3,V.facamb4 AS FACTORES_AMBIEN_PRE4,V.facamb5 AS FACTORES_AMBIEN_PRE5,V.facamb6 AS FACTORES_AMBIEN_PRE6,V.facamb7 AS FACTORES_AMBIEN_PRE7,V.facamb8 AS FACTORES_AMBIEN_PRE8,V.facamb9 AS FACTORES_AMBIEN_PRE9,V.observacion AS OBSERVACIONES,

U.id_usuario AS Cod_Usuario, U.nombre AS Nombre_Usuario, U.perfil AS Perfil_Usuario, V.fecha_create AS Fecha_Creacion, V.estado AS Estado_Registro
FROM `hog_carac` V
LEFT JOIN hog_fam F ON V.idfam = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON V.usu_create=U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred1();
	$sql.=whe_date1();
	
	$tot="SELECT count(*) as total FROM `hog_carac` V LEFT JOIN hog_fam F ON V.idfam = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON V.usu_create=U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred1();
	$tot.=whe_date1();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_plancui($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,C.idviv AS Cod_Familia,C.id AS Cod_Registro,G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', C.fecha AS Fecha_Caracterizacion,
FN_CATALOGODESC(22,C.accion1) AS Accion_1,FN_CATALOGODESC(75,C.desc_accion1) AS Descipcion_Accion1,
FN_CATALOGODESC(22,C.accion2) AS Accion_2,FN_CATALOGODESC(75,C.desc_accion2) AS Descipcion_Accion2,
FN_CATALOGODESC(22,C.accion3) AS Accion_3,FN_CATALOGODESC(75,C.desc_accion3) AS Descipcion_Accion3,
FN_CATALOGODESC(22,C.accion4) AS Accion_4,FN_CATALOGODESC(75,C.desc_accion4) AS Descipcion_Accion4,
C.observacion AS Obervaciones, C.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, C.fecha_create AS Fecha_Creacion, C.estado AS Estado_Registro

FROM `hog_plancuid` C
LEFT JOIN hog_fam F ON C.idviv = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON C.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred2();
	$sql.=whe_date2();
	
	$tot="SELECT count(*) as total FROM `hog_plancuid` C  LEFT JOIN hog_fam F ON C.idviv = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON C.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred2();
	$tot.=whe_date2();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_plancomp($txt){
	$sql="SELECT
G.idgeo Cod_Predio, C.idviv AS Cod_Familia, C.idcon AS Cod_Registro, G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', C.fecha AS Fecha, C.compromiso AS Compromiso_Concertado, FN_CATALOGODESC(26,C.equipo) AS 'Perfil que Concerta', 
C.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, C.fecha_create AS Fecha_Creacion, C.estado AS Estado_Registro
FROM `hog_planconc` C
LEFT JOIN hog_plancuid P ON P.idviv = C.idviv
LEFT JOIN hog_fam F ON C.idviv = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON C.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred3();
	$sql.=whe_date3();
	
	$tot="SELECT count(*) as total FROM `hog_planconc` C  LEFT JOIN hog_plancuid P ON P.idviv = C.idviv LEFT JOIN hog_fam F ON C.idviv = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON C.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred3();
	$tot.=whe_date3();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_segcomp($txt){
	$sql="SELECT  
S.id_segcom AS 'Cod Registro', C.idviv AS 'Cod Familia', C.idcon AS 'Cod Compromiso', G.subred AS Subred, FN_CATALOGODESC(3,G.zona) AS Zona, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
S.fecha_seg AS 'Fecha Seguimiento',FN_CATALOGODESC(234,S.tipo_seg) AS 'Tipo Seguimiento', FN_CATALOGODESC(170,S.estado_seg) AS '¿Seguimiento Cumplido?', S.obs_seg AS 'Observacion Seguimiento',
S.usu_create AS 'Documento Colaborador', U.nombre AS 'Nombre Colaborador', U.perfil AS 'Perfil Colaborador', U.equipo AS 'Equipo Colaborador', C.fecha_create AS 'Fecha Creacion Registro'
FROM `hog_segcom` S
LEFT JOIN hog_planconc C ON S.id_con = C.idcon
LEFT JOIN hog_plancuid P ON C.idviv = P.idviv
LEFT JOIN hog_fam F ON P.idviv = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON S.usu_create = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred27();
	$sql.=whe_date27();
	
	$tot="SELECT count(*) as total FROM `hog_segcom` S  LEFT JOIN hog_planconc C ON S.id_con = C.idcon LEFT JOIN hog_plancuid P ON C.idviv = P.idviv LEFT JOIN hog_fam F ON P.idviv = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON S.usu_create = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred27();
	$tot.=whe_date27();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_signos($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,S.id_signos AS Cod_Registro,G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento, P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo,
S.fecha_toma AS Fecha_Toma, S.peso AS PESO, S.talla AS TALLA, S.imc AS IMC, S.tas AS Tension_Sistolica, S.tad AS Tension_Diastolica, S.frecard AS Frecuencia_Cardiaca, S.satoxi AS Saturacion_Oxigeno, S.peri_abdomi AS Perimetro_Abdominal, S.peri_braq AS Perimetro_Braquial, S.zscore AS ZSCORE, S.glucom AS Glucometria,

S.usu_create AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, S.fecha_create AS Fecha_Creacion, S.estado AS Estado_Registro
FROM `hog_signos` S
LEFT JOIN person P ON S.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON S.usu_create = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred4();
	$sql.=whe_date4();
	
	$tot="SELECT count(*) as total FROM `hog_signos` S LEFT JOIN person P ON S.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON S.usu_create = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred4();
	$tot.=whe_date4();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_alertas($txt){
	$sql="SELECT G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_alert AS Cod_Registro,G.subred AS Subred, G.zona AS Zona, G.localidad AS Localidad, G.barrio AS Barrio, G.manz_cuidado AS Manzana_Cuidado, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', P.idpeople AS Cod_Persona,
P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento, P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(19,P.genero) AS Genero, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad, FN_CATALOGODESC(16,P.etnia) AS Etnia, FN_CATALOGODESC(178,P.pobladifer) AS Poblacion_Diferencial, FN_CATALOGODESC(14,P.discapacidad) AS Tipo_Discapacidad, FN_CATALOGODESC(175,P.ocupacion) AS Ocupacion, FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,
FN_CATALOGODESC(176,A.cursovida) AS Curso_de_Vida, A.fecha AS Fecha, FN_CATALOGODESC(34,A.tipo) AS Tipo_Intervencion,FN_CATALOGODESC(166,A.crit_epi) AS Criterio_Epidemiologico,  

FN_CATALOGODESC(170,A.men_dnt) AS Menor_Con_DNT, FN_CATALOGODESC(170,A.men_sinctrl) AS Menor_Sin_Control, FN_CATALOGODESC(170,A.gestante) AS Usuaria_Gestante, FN_CATALOGODESC(177,A.etapgest) AS Etapa_Gestacional, FN_CATALOGODESC(170,A.ges_sinctrl) AS Gestante_Sin_Control, FN_CATALOGODESC(170,A.cronico) AS Usuario_Cronico, FN_CATALOGODESC(170,A.cro_hiper) AS Dx_Hipertencion, FN_CATALOGODESC(170,A.cro_diabe) AS Dx_Diabetes, FN_CATALOGODESC(170,A.cro_epoc) AS Dx_Epoc, FN_CATALOGODESC(170,A.cro_sinctrl) AS Cronico_Sin_Control, FN_CATALOGODESC(170,A.esq_vacun) AS Esquema_de_vacunacion_Completo, 

A.alert1 AS Alerta_N°_1, A.selmul1 AS  Descripcion_Alerta_N°_1, A.alert2 AS Alerta_N°_2, A.selmul2 AS  Descripcion_Alerta_N°_2, A.alert3 AS Alerta_N°_3,A.selmul3 AS  Descripcion_Alerta_N°_3, A.alert4 AS Alerta_N°_4,A.selmul4 AS  Descripcion_Alerta_N°_4, A.alert5 AS Alerta_N°_5,A.selmul5 AS  Descripcion_Alerta_N°_5,A.alert6 AS Alerta_N°_6,A.selmul6 AS  Descripcion_Alerta_N°_6,

FN_CATALOGODESC(170,A.agen_intra) AS Agendamiento_Promotor, A.servicio AS Serivicio_Agendado, A.fecha_cita AS Fecha_de_la_Cita, A.hora_cita AS Hora_de_la_Cita, A.lugar_cita AS Lugar_de_la_Cita, FN_CATALOGODESC(170,A.deriva_pf) AS Derivacion_a_PCF,FN_CATALOGODESC(87,A.evento_pf) AS Evento_PCF,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `hog_alert` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred5();
	$sql.=whe_date5();
	
	$tot="SELECT count(*) as total FROM `hog_alert` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred5();
	$tot.=whe_date5();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_riesamb($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.idamb AS Cod_Registro,G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
A.fecha AS Fecha_Seguimiento,FN_CATALOGODESC(34,A.tipo_activi) AS Tipo_Seguimiento,
A.seguro AS seguro,A.grietas AS grietas,A.combustible AS combustible,A.separadas AS separadas,A.lena AS lena,A.ilumina AS ilumina,A.fuma AS fuma,A.bano AS bano,A.cocina AS cocina,
A.elevado AS elevado,A.electrica AS electrica,A.elementos AS elementos,A.barreras AS barreras,A.zontrabajo AS zontrabajo,A.agua AS agua,A.tanques AS tanques,A.adecagua AS adecagua,
A.raciagua AS raciagua,A.sanitari AS sanitari,A.aguaresid AS aguaresid,A.terraza AS terraza,A.recipientes AS recipientes,A.vivaseada AS vivaseada,A.separesiduos AS separesiduos,A.reutresiduos AS reutresiduos,
A.noresiduos AS noresiduos,A.adecresiduos AS adecresiduos,A.horaresiduos AS horaresiduos,A.plagas AS plagas,A.contplagas AS contplagas,A.pracsanitar AS pracsanitar,A.envaplaguicid AS envaplaguicid,A.consealiment AS consealiment,A.limpcocina AS limpcocina,A.cuidcuerpo AS cuidcuerpo,A.fechvencim AS fechvencim,A.limputensilios AS limputensilios,A.adqualime AS adqualime,A.almaquimicos AS almaquimicos,A.etiqprodu AS etiqprodu,A.juguetes AS juguetes,A.medicamalma AS medicamalma,A.medicvenc AS medicvenc,A.adqumedicam AS adqumedicam,A.medidaspp AS medidaspp,A.radiacion AS radiacion,A.contamaire AS contamaire,A.monoxido AS monoxido,A.residelectri AS residelectri,A.duermeelectri AS duermeelectri,A.vacunasmascot AS vacunasmascot,A.aseamascot AS aseamascot,A.alojmascot AS alojmascot,A.excrmascot AS excrmascot,A.permmascot AS permmascot,A.salumascot AS salumascot,A.pilas AS pilas,A.dispmedicamentos AS dispmedicamentos,A.dispcompu AS dispcompu,A.dispplamo AS dispplamo,A.dispbombill AS dispbombill,A.displlanta AS displlanta,
A.dispplaguic AS dispplaguic,A.dispaceite AS dispaceite,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

 FROM `hog_amb` A
LEFT JOIN hog_fam F ON A.idvivamb = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
 WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred6();
	$sql.=whe_date6();
	
	$tot="SELECT count(*) as total FROM `hog_amb` A LEFT JOIN hog_fam F ON A.idvivamb = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred6();
	$tot.=whe_date6();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_eventos($txt){
	$sql="SELECT
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_eve AS Cod_Registro,G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento, P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo, A.docum_base AS Documento_de_Base,FN_CATALOGODESC(87,A.evento) AS Evento_PCF,A.fecha_even AS Fecha_Generacion_Evento,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `vspeve` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred7();
	$sql.=whe_date7();
	
	$tot="SELECT count(*) as total FROM `vspeve` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred7();
	$tot.=whe_date7();
	// echo $sql;
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_acompsic($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_acompsic AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(170,A.autocono) AS Preg_1,FN_CATALOGODESC(170,A.cumuni_aser) AS Preg_2,FN_CATALOGODESC(170,A.toma_decis) AS Preg_3,FN_CATALOGODESC(170,A.pensa_crea) AS Preg_4,FN_CATALOGODESC(170,A.manejo_emo) AS Preg_5,FN_CATALOGODESC(170,A.rela_interp) AS Preg_6,FN_CATALOGODESC(170,A.solu_prob) AS Preg_7,FN_CATALOGODESC(170,A.pensa_critico) AS Preg_8,FN_CATALOGODESC(170,A.manejo_tension) AS Preg_9,FN_CATALOGODESC(170,A.empatia) AS Preg_10,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,

FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,


FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,
A.liker_dificul AS Liker_Dificultad,A.liker_emocion AS Liker_Emocion,A.liker_decision AS Liker_Decision,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,A.users_bina AS Usuarios_Equipo,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_acompsic` A
 
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_acompsic` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_apopsicduel($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_psicduel AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(80,A.causa_duelo) AS Causa_Duelo,A.fecha_defun AS Fecha_Defuncion,FN_CATALOGODESC(81,A.parent_fallec) AS Parentesco_Fallecido,FN_CATALOGODESC(82,A.lugar_defun) AS Lugar_defuncion,FN_CATALOGODESC(83,A.vincu_afect) AS Vinculo_Afectivo,FN_CATALOGODESC(84,A.senti_ident_1) AS Sentimientos_Emosiones_1,FN_CATALOGODESC(84,A.senti_ident_2) AS Sentimientos_Emosiones_2,FN_CATALOGODESC(84,A.senti_ident_3) AS Sentimientos_Emosiones_3,FN_CATALOGODESC(85,A.etapa_duelo) AS Etapa_Duelo,FN_CATALOGODESC(86,A.sintoma_duelo_1) AS Sintomas_Malestar_Duelo1,FN_CATALOGODESC(86,A.sintoma_duelo_2) AS Sintomas_Malestar_Duelo2,FN_CATALOGODESC(86,A.sintoma_duelo_3) AS Sintomas_Malestar_Duelo3,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre, FN_CATALOGODESC(78,A.liker_dificul) AS Liker_Dificultad,FN_CATALOGODESC(78,A.liker_emocion) AS Liker_Emocion,FN_CATALOGODESC(78,A.liker_decision) AS Liker_Decision,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_apopsicduel` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
$sql.=whe_date8();
// echo $sql;
$tot="SELECT COUNT(*) total FROM `vsp_apopsicduel` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
$tot.=whe_date8();
$_SESSION['sql_'.$txt]=$sql;
$_SESSION['tot_'.$txt]=$tot;
$rta = array('type' => 'OK','file'=>$txt);
echo json_encode($rta);
}


function lis_bpnpret($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_bpnpret AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento, FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento, FN_CATALOGODESC(87,A.evento) AS Evento, FN_CATALOGODESC(73,A.estado_s) AS Estado, FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,
FN_CATALOGODESC(95,A.sem_ges) AS Semanas_Gestacion, FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD, FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo, FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva, A.peso AS 'Peso_(Kg)', A.talla AS 'Talla (cm)', FN_CATALOGODESC(96,A.edad_ges) AS Edad_Gestacional, FN_CATALOGODESC(97,A.diag_nutri) AS Dx_Nutricional_Fenton, A.zscore AS Zscore, FN_CATALOGODESC(98,A.clasi_nutri) AS Clasificacion_Nutricional, FN_CATALOGODESC(170,A.gana_peso) AS Evidencia_Ganancia_Peso, FN_CATALOGODESC(99,A.gana_peso_dia) AS Ganancia_Peso_Diaria, FN_CATALOGODESC(170,A.signos_alarma) AS Signos_Alarma, FN_CATALOGODESC(170,A.signos_alarma_seg) AS Signos_Alarma_Seguimiento,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.desc_accion2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_3,FN_CATALOGODESC(75,A.acciones_3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `vsp_bpnpret` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_bpnpret` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_bpnterm($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_bpnterm AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,A.peso AS 'Peso_(Kg)',A.talla AS 'Talla (cm)',A.zscore AS Zscore,FN_CATALOGODESC(98,A.clasi_nutri) AS Clasificacion_Nutricional,FN_CATALOGODESC(170,A.gana_peso) AS Evidencia_Ganancia_Peso,FN_CATALOGODESC(99,A.gana_peso_dia) AS Ganancia_Peso_Diaria,FN_CATALOGODESC(170,A.signos_alarma) AS Signos_Alarma,FN_CATALOGODESC(170,A.signos_alarma_seg) AS Signos_Alarma_Seguimiento,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `vsp_bpnterm` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_bpnterm` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_cancinfa($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_cancinfa AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,FN_CATALOGODESC(170,A.diagnosticado) AS Dx_Confirmado,A.fecha_dx AS Fecha_Dx_Confirmado,FN_CATALOGODESC(170,A.tratamiento) AS Cuenta_Tratamiento,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_Especialista,A.cual_espe AS Cual_Especilista,FN_CATALOGODESC(93,A.trata_orde) AS Tratamiento_Ordenado,A.fecha_cirug AS Fecha_Cirugia,A.fecha_quimio AS Fecha_Quimioterapia,A.fecha_radiote AS Fecha_Radioterapia,A.fecha_otro AS Fecha_Otro,A.otro_cual AS Otro_Cual,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,
A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.supera_problema) AS Supera_Problemas_Practicos,
FN_CATALOGODESC(170,A.supera_emocional) AS Supera_Estado_Emocional,FN_CATALOGODESC(170,A.supera_dolor) AS Supera_Valoracion_Dolor,FN_CATALOGODESC(170,A.supera_funcional) AS Supera_Valoracion_Funcional,FN_CATALOGODESC(170,A.supera_educacion) AS Supera_Necesidades_Educacion,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_cancinfa` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_cancinfa` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_condsuic($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_condsuic AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,FN_CATALOGODESC(197,A.tipo_caso) AS Tipo_Poblacion,FN_CATALOGODESC(136,A.etapa) AS Etapa,FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento,

FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal,FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio,FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes,

A.fecha_obstetrica AS Fecha_Evento_Obstetrico,FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento,FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion,FN_CATALOGODESC(170,A.meto_fecunda) AS Cuenta_Metodo_Fecundidad,FN_CATALOGODESC(130,A.cual) AS Cual_Metodo,A.peso_nacer AS Peso_RN_Nacer,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,

FN_CATALOGODESC(170,A.persis_morir) AS Persiste_Idea_Morir,FN_CATALOGODESC(170,A.proce_eapb) AS Proceso_Psicoterapéutico,FN_CATALOGODESC(170,A.otra_conduc) AS Otra_Conducta_Suicida,FN_CATALOGODESC(139,A.cual_conduc) AS Cual_Conducta,FN_CATALOGODESC(170,A.conduc_otrofam) ASConducta_Suicida_OtroFam,FN_CATALOGODESC(170,A.tam_cope) AS Tamizaje_Cope,FN_CATALOGODESC(140,A.total_afron) AS Cope_Afrontamiento,FN_CATALOGODESC(141,A.total_evita) AS Cope_Evitacion,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.aplica_tamiz) AS Aplica_Tamizaje_Cope,FN_CATALOGODESC(78,A.liker_dificul) AS Liker_Dificultades,FN_CATALOGODESC(78,A.liker_emocion) AS Liker_Emociones,FN_CATALOGODESC(78,A.liker_decision) AS Liker_Decisiones,FN_CATALOGODESC(140,A.cope_afronta) AS Cope_Afrontamiento,FN_CATALOGODESC(141,A.cope_evitacion) AS Cope_Evitacion,FN_CATALOGODESC(142,A.incremen_afron) AS Estrategia_Afrontamiento,FN_CATALOGODESC(143,A.incremen_evita) AS Estrategia_Evitacion,
FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_condsuic` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_condsuic` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_cronicos($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_cronicos AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(170,A.condi_diag) AS Cronico_Diagnosticado,FN_CATALOGODESC(170,A.dx1) AS Hipertension,FN_CATALOGODESC(170,A.dx2) AS Diabetes,FN_CATALOGODESC(170,A.dx3) AS Epoc,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_Controles_Cronico,FN_CATALOGODESC(170,A.trata_farma) AS Tratamiento_Farmacologico,FN_CATALOGODESC(170,A.adhere_tratami) AS Adherente_al_Tratamiento,FN_CATALOGODESC(170,A.mantien_dieta) AS Mantiene_Dieta_Recomendada,FN_CATALOGODESC(155,A.actividad_fisica) AS Actividad_Fisica,FN_CATALOGODESC(170,A.metodo_fecun) AS Cuenta_Metodo_Fecundidad,FN_CATALOGODESC(138,A.cual) AS Cual_Metodo,FN_CATALOGODESC(170,A.hemoglobina) AS Hemoglobina,A.fecha_hemo AS Fecha_Hemoglobina,A.valor_hemo AS Valor_Hemoglobina,A.tas AS Tension_Arterial_Sistolica,A.tad AS Tension_Arterial_Diastolica,A.glucometria AS Glucometria,A.peso AS 'Peso_(Kg)',A.talla AS 'Talla_(Cm)',A.imc AS Imc,A.peri_cintura AS Perimetro_Cintura,FN_CATALOGODESC(170,A.fuma) AS '¿Fuma?',

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_cronicos` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_cronicos` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_dntsevymod($txt){
	$sql="SELECT
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_dntsevymod AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(156,A.patolo_base) AS Patologia_de_Base,FN_CATALOGODESC(195,A.segui_medico) AS Seguimiento_Medico,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_Controles_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(88,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(88,A.lacmate_comple) AS Lactancia_Materna_Complementaria,FN_CATALOGODESC(88,A.alime_complemen) AS Alimentacion_Complementaria,A.peso AS 'Peso_(Kg)',A.talla AS 'Talla_(Cm)',A.zscore AS Zscore,FN_CATALOGODESC(98,A.clasi_nutri) AS Clasificacion_Nutricional,FN_CATALOGODESC(170,A.gana_peso) AS Ganancia_Peso,FN_CATALOGODESC(158,A.trata_desnutri) AS Tratamiento_Desnutricion,A.tratamiento AS Tratamiento,FN_CATALOGODESC(196,A.consume_fruyverd) AS Come_Frutas_Verduras,FN_CATALOGODESC(196,A.consume_carnes) AS Consume_Carnes,FN_CATALOGODESC(196,A.consume_azucares) AS Consume_Azucar,FN_CATALOGODESC(196,A.actividad_fisica) AS Realiza_Actividad_Fisica,
FN_CATALOGODESC(170,A.apoyo_alimentario) AS Apoyo_Alimentario,FN_CATALOGODESC(170,A.signos_alarma) AS Signos_Alarma,FN_CATALOGODESC(170,A.signos_alarma_seg) AS Signos_Alarma_Seguimiento,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_dntsevymod` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_dntsevymod` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_eraira($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_eraira AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,


A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(170,A.asiste_control) AS Asiste_Controles_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(88,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(88,A.lacmate_comple) AS Lactancia_Materna_Complementaria,FN_CATALOGODESC(88,A.alime_complemen) AS Alimentacion_Complementaria,FN_CATALOGODESC(88,A.adecua_oxi) AS 'Administracion_Inhalador/Oxigeno',FN_CATALOGODESC(88,A.adhe_tratam) AS Adherencia_al_Tratamiento,FN_CATALOGODESC(170,A.signos_alarma) AS Signos_Alarma,FN_CATALOGODESC(170,A.signos_alarma_seg) AS Signos_Alarma_Seguimiento,FN_CATALOGODESC(170,A.adhe_lavamano) AS Tecnica_Lavado_Manos,FN_CATALOGODESC(170,A.reing_hospita) AS Reingreso_Hospitalario,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
 
FROM `vsp_eraira` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_eraira` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_gestantes($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_gestante AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,FN_CATALOGODESC(136,A.etapa) AS Etapa,FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento,

FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal,FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio,FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes,A.peso AS 'Peso_(Kg)',A.talla AS 'Talla_(Cm)',A.imc AS Imc,FN_CATALOGODESC(210,A.clas_nutri) AS Clasificacion_Nutricional,FN_CATALOGODESC(170,A.gana_peso) AS Evidencia_Ganancia_Peso,FN_CATALOGODESC(205,A.cant_ganapesosem) AS Ganancia_Peso_Semanal,FN_CATALOGODESC(204,A.ante_patogest) AS Antecedentes_Patologicos,FN_CATALOGODESC(196,A.num_frutas) AS Come_Frutas_Verduras,FN_CATALOGODESC(196,A.num_carnes) AS Consume_Carnes,FN_CATALOGODESC(196,A.num_azucar) AS Consume_Azucar,
FN_CATALOGODESC(196,A.cant_actifisica) AS Realiza_Actividad_Fisica,FN_CATALOGODESC(170,A.adop_recomenda) AS Adopta_Recomendaciones_Nt,FN_CATALOGODESC(170,A.apoy_alim) AS Apoyo_Alimentario,A.fecha_obstetrica AS Fecha_Evento_Obstetrico,FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento,FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion,FN_CATALOGODESC(170,A.meto_fecunda) AS Cuenta_Metodo_Fecundidad,
FN_CATALOGODESC(138,A.cual) AS Cual_Metodo,A.peso_nacer AS Peso_RN_Nacer,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,
FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `vsp_gestantes` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_gestantes` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_hbgest($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_hbgestacio AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,FN_CATALOGODESC(136,A.etapa) AS Etapa,FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento,

FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal,FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio,FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes,A.fecha_obstetrica AS Fecha_Evento_Obstetrico,FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento,FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion,FN_CATALOGODESC(170,A.meto_fecunda) AS Cuenta_Metodo_Fecundidad,FN_CATALOGODESC(130,A.cual) AS Cual_Metodo,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.lacmate_comple) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(170,A.vacuna_hb) AS Rn_con_Vacuna_HB,A.fec_hb_recnac AS Fecha_Vacuna_HB,FN_CATALOGODESC(170,A.reci_inmunoglo) AS Recibe_Inmunoglobulina,FN_CATALOGODESC(170,A.seg_eps) AS Seguimiento_EPS,FN_CATALOGODESC(170,A.antige_super1) AS Antigeno_de_Superficie,FN_CATALOGODESC(187,A.resultado1) AS Resultado_Antigeno,FN_CATALOGODESC(170,A.anticor_igm_hb1) AS AntiCore_Igm_HB,FN_CATALOGODESC(187,A.resultado2) AS Resultado_AntiCore,FN_CATALOGODESC(170,A.anticor_toigm_hb1) AS AntiCore_Total_Igm_HB,FN_CATALOGODESC(187,A.resultado3) AS Resultado_AntiCore_Total,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `vsp_hbgest` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_hbgest` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_mme($txt){
	$sql="SELECT G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_mme AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,
A.fecha_seg AS Fecha_Seguimiento, FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento, FN_CATALOGODESC(87,A.evento) AS Evento, FN_CATALOGODESC(243,A.tiposeg) AS Tipo_Seguimiento, FN_CATALOGODESC(73,A.estado_s) AS Estado, FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado, FN_CATALOGODESC(136,A.etapa) AS Etapa, FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento, FN_CATALOGODESC(244,A.gestaciones) AS Gestaciones, FN_CATALOGODESC(244,A.partos) AS Partos, FN_CATALOGODESC(244,A.abortos) AS Abortos, FN_CATALOGODESC(244,A.cesareas) AS Cesareas, FN_CATALOGODESC(244,A.vivos) AS Vivos, FN_CATALOGODESC(244,A.muertos) AS Muertos, A.fecha_egre AS Fecha_Egreso_Hospitalario, A.edad_padre AS Edad_del_Padre, FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal, FN_CATALOGODESC(170,A.ing_ctrpre) AS 'Ingreso_Control_<Sem10', FN_CATALOGODESC(245,A.cpn) AS Cuantos_CPN, A.porque_no AS Porque, FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio_al_Dia, FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo, FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes, FN_CATALOGODESC(170,A.trata_farma) AS Tratamiento_Farmacologico, A.tipo_tratafarma AS Tipo_Tratamiento, A.cualtra AS Otro_Cual, FN_CATALOGODESC(170,A.adhe_tratafarma) AS Adherente_al_Tratamiento, A.porque_noadh AS Porque, A.peso AS 'Peso_(Kg)', A.talla AS 'Talla_(Cm)', A.imc AS Imc, FN_CATALOGODESC(210,A.clasi_nutri) AS Clasificacion_Nutricional, FN_CATALOGODESC(170,A.signos_alarma_seg) AS Signos_de_Alarma, A.descr_sigalarma AS Descripcion_Signo_Alarma, A.entrega_medic_labo AS Entrega_Medicamen_Laborator_Casa, A.fecha_obstetrica AS Fecha_Evento_Obstetrico, FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento, FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion, FN_CATALOGODESC(170,A.meto_fecunda) AS Metodo_Regulacion, FN_CATALOGODESC(138,A.cualmet) AS Cual_Metodo, A.otro_cual AS Otro_Metodo_Cual, A.motivo_nofecund AS Motivo_No_Acceso, FN_CATALOGODESC(170,A.control_mac) AS Control_MAC,
A.fecha_control_mac AS Fecha_Control_MAC, FN_CATALOGODESC(170,A.ctrl_postpar_espe) AS Control_Post_Parto_Especialista, A.fecha_postpar_espe AS Fecha_Control_Post_Parto_Espec, FN_CATALOGODESC(88,A.asis_ctrl_postpar_espe) AS Asistio_Control_Post_Parto, A.porque_no_postpar AS Porque_No_Asist, FN_CATALOGODESC(170,A.consul_apoy_lacmater) AS Consulta_Apoyo_Lactancia, FN_CATALOGODESC(170,A.signos_alarma) AS Signos_Alarma_Seguimiento, A.desc_sigala AS Descripcion_Signos_Alarma, FN_CATALOGODESC(170,A.disc_ges) AS Getante_Discapacidad_O_Secuela, A.cual_disc_ges AS Cual, A.fecha_apoy_lacmater AS Fecha_Consulta_Apoyo_Lactancia, A.peso_rcnv AS Peso_RN, FN_CATALOGODESC(88,A.ctrl_recinac) AS Asiste_Control_RN, A.fecha_ctrl_nac AS Fecha_Control_RN, FN_CATALOGODESC(88,A.asis_ctrl_recinac) AS Asistio_Control_RN, A.porque_norec AS Porque, A.ult_peso AS Ultimo_Peso_Regis, FN_CATALOGODESC(170,A.consul_lacmate) AS Consulta_Apoyo_Lactancia_Mater, A.porque_nolact AS Porque_No, A.fecha_consul_lacmate AS Fecha_Consulta_LM, FN_CATALOGODESC(88,A.asiste_ctrl_cyd) AS Asiste_CYD, FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo_Edad, FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva, FN_CATALOGODESC(170,A.signos_alarma_lac) AS Madre_Identifica_Signos_Alarma, FN_CATALOGODESC(170,A.cam_sign) AS Considera_Cambios_Significativos, FN_CATALOGODESC(170,A.qui_vida) AS Quitarse_la_Vida, FN_CATALOGODESC(170,A.viv_malt) AS Violencia_o_Maltrato, FN_CATALOGODESC(170,A.adec_red) AS Red_Apoyo, A.fecha_egreopost AS Fecha_Post_Egreso_Hospitalario, 

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1, FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2, FN_CATALOGODESC(22,A.acciones_1) AS Accion_1, FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1, FN_CATALOGODESC(22,A.acciones_2) AS Accion_2, FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2, FN_CATALOGODESC(22,A.acciones_3) AS Accion_3, FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3, FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta, FN_CATALOGODESC(79,A.ruta) AS Ruta, FN_CATALOGODESC(77,A.novedades) AS Novedades, A.otras_condiciones AS Otras_Condiciones, A.observaciones AS Observaciones, FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso, FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre, A.fecha_cierre AS Fecha_Cierre, FN_CATALOGODESC(170,A.conti_segespecial) AS Continua_Seg_Especialista, A.cual_segespecial AS Cual_Seguimiento, A.recomen_cierre AS Recomendación_Cierre, FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `vsp_mme` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_mme` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_mnehosp($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_mnehosp AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',


P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(92,A.even_prio) AS Evento_Priorizado,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_Controles_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(88,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(88,A.lacmate_comple) AS Lactancia_Materna_Complementaria,FN_CATALOGODESC(88,A.alime_complemen) AS Alimentacion_Complementaria,FN_CATALOGODESC(170,A.adhe_tratam) AS Adherencia_al_tratamiento,FN_CATALOGODESC(170,A.ira_eda) AS Presenta_IRA_o_EDA,FN_CATALOGODESC(170,A.signos_alarma_seg) AS Signos_Alarma_Seguimiento,FN_CATALOGODESC(170,A.reing_hospita) AS Reingreso_Hospitalario,FN_CATALOGODESC(170,A.signos_alarma) AS Signos_Alarma,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_mnehosp` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_mnehosp` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_otroprio($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_otroprio AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_otroprio` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_otroprio` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_saludoral($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_saludoral AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(91,A.clasi_riesgo) AS Clasificacion_Riesgo,FN_CATALOGODESC(170,A.sangra_cepilla) AS Sangrado_Cepillado_Dental,FN_CATALOGODESC(170,A.evide_anormal) AS Evidencia_Autoexamen_Anormal,
A.explica_breve AS Explique_Brevemente,FN_CATALOGODESC(170,A.urg_odonto) AS Urgencia_Odontologica,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,

FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,
A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,A.mejora_practica AS Mejora_Practicas,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_saludoral` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_saludoral` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_sificong($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_sificong AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(170,A.asiste_control) AS Asiste_Controles_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(170,A.altera_desarr) AS Alteraciones_del_Desarrollo,FN_CATALOGODESC(170,A.serologia) AS Primera_Serologia,A.fecha_serolo AS Fecha_Serologia,FN_CATALOGODESC(94,A.resul_ser) AS Resultado_Serologia,FN_CATALOGODESC(170,A.trata_rn) AS Tratamiento_RN,FN_CATALOGODESC(170,A.ctrl_serolo) AS Control_Serologia,A.fecha_controlser AS Fecha_Control_Serologia,FN_CATALOGODESC(94,A.resul_controlser) AS Resultado_Control_Serologia,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_sificong` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_sificong` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_sifigest($txt){
	$sql="SELECT 
	G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_sifigest AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
	
	P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,
	A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,FN_CATALOGODESC(136,A.etapa) AS Etapa,FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento,
	
	FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal,FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio,FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes,A.fecha_obstetrica AS Fecha_Evento_Obstetrico,FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento,FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion,FN_CATALOGODESC(170,A.meto_fecunda) AS Cuenta_Metodo_Fecundidad,FN_CATALOGODESC(138,A.cual) AS Cual_Metodo,FN_CATALOGODESC(170,A.confir_sificong) AS RN_Confir_Sífilis_Congénita,FN_CATALOGODESC(94,A.resul_ser_recnac) AS Resultado_Serologia_RN,FN_CATALOGODESC(199,A.trata_recnac) AS Tratamiento_RN,FN_CATALOGODESC(70,A.serol_3meses) AS RN_Serologia_3meses,A.fec_conser_1tri2 AS Fecha_Serologia_3meses,FN_CATALOGODESC(94,A.resultado) AS Resultado_Serologia_3meses,FN_CATALOGODESC(170,A.ctrl_serol1t) AS Control_Serologia_1Trimestre,A.fec_conser_1tri1 AS Fecha_Serologia_1Trimestre,FN_CATALOGODESC(94,A.resultado_1) AS Resultado_Serologia_1Trimestre,FN_CATALOGODESC(170,A.ctrl_serol2t) AS Control_Serologia_2Trimestre,A.fec_conser_2tri AS Fecha_Serologia_2Trimestre,FN_CATALOGODESC(94,A.resultado_2) AS Resultado_Serologia_2Trimestre,FN_CATALOGODESC(170,A.ctrl_serol3t) AS Control_Serologia_3Trimestre,A.fec_conser_3tri AS Fecha_Serologia_3Trimestre,FN_CATALOGODESC(94,A.resultado_3) AS Resultado_Serologia_3Trimestre,
	
	FN_CATALOGODESC(170,A.initratasif) AS Inicio_Tratamiento_Sifilis_Ges,A.fec_1dos_trages1 AS 1Dosis,A.fec_2dos_trages1 AS 2Dosis,A.fec_3dos_trages1 AS 3Dosis,
	
	FN_CATALOGODESC(200,A.pri_con_sex) AS Primer_Contact_Sexual,
FN_CATALOGODESC(207,A.initratasif1) AS Contacto_Sexual_Inicial_Tratamiento,A.fec_apl_tra_1dos1 AS Fecha1_Primera_Dosis,A.fec_apl_tra_2dos1 AS Fecha1_Segunda_Dosis,A.fec_apl_tra_3dos1 AS Fecha1_Tercera_Dosis,
FN_CATALOGODESC(200,A.seg_con_sex) AS Segundo_Contacto_Sexual,FN_CATALOGODESC(207,A.initratasif2) AS Contacto2_Sexual_Inicia_Tratamiento,A.fec_apl_tra_1dos2 AS Fecha2_Primera_Dosis,A.fec_apl_tra_2dos2 AS Fecha2_Segunda_Dosis,A.fec_apl_tra_3dos2 AS Fecha2_Tercera_Dosis,FN_CATALOGODESC(170,A.prese_reinfe) AS Presenta_Reinfeccion,FN_CATALOGODESC(207,A.initratasif3) AS Tratamiento_Reinfeccion,A.fec_1dos_trages2 AS Fecha3_Primera_Dosis,A.fec_2dos_trages2 AS Fecha3_Segunda_Dosis,A.fec_3dos_trages2 AS Fecha3_Tercera_Dosis,
FN_CATALOGODESC(200,A.reinf_1con) AS Primer_Contacto_Sexual,FN_CATALOGODESC(207,A.initratasif4) AS Contacto_Sexual_Inicia_Tratamiento,A.fec_1dos_trapar AS Fecha4_Primera_Dosis,A.fec_2dos_trapar AS Fecha4_Segunda_Dosis,A.fec_3dos_trapar AS Fecha4_Tercera_Dosis,
FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_sifigest` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_sifigest` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_vihgest($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_vihgestacio AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,FN_CATALOGODESC(136,A.etapa) AS Etapa,FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento,

FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal,FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio,FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes,A.fecha_obstetrica AS Fecha_Evento_Obstetrico,FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento,FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion,FN_CATALOGODESC(170,A.meto_fecunda) AS Cuenta_Metodo_Fecundidad,FN_CATALOGODESC(138,A.cual_metodo) AS Cual_Metodo,FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD,FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(170,A.lacmate_comple) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(170,A.recnac_proxi) AS Rn_Recibio_Profilaxis,FN_CATALOGODESC(170,A.formu_lact) AS Recibe_Formula_Lactea,FN_CATALOGODESC(209,A.tarros_mes) AS Tarros_Mes,FN_CATALOGODESC(170,A.caso_con_tmi) AS Caso_Conf_Transmisi_Mater_Infa,FN_CATALOGODESC(170,A.asis_provih_rn) AS RN_Asiste_Programa_VIH,FN_CATALOGODESC(170,A.cargaviral_1mes) AS Carga_Viral_1Mes,A.fecha_carga1mes AS Fecha_Carga_Viral_1Mes,FN_CATALOGODESC(208,A.resul_carga1mes) AS Resultado_Carga_Viral_1Mes,FN_CATALOGODESC(170,A.cargaviral_4mes) AS Carga_Viral_4Mes,A.fecha_carga4mes AS Fecha_Carga_Viral_4Mes,FN_CATALOGODESC(208,A.resul_carga4mes) AS Resultado_Carga_Viral_4Mes,FN_CATALOGODESC(170,A.prueba_rapida) AS Tiene_Prueba_Rapida,A.fec_pruerap1 AS Fecha_Prueba_Rapida,FN_CATALOGODESC(170,A.carga_viral) AS Carga_Viral,A.fec_cargaviral1 AS Fecha_Carga_Viral,FN_CATALOGODESC(208,A.resul_cargaviral1) AS Resultado_Carga_Viral,FN_CATALOGODESC(170,A.asis_provih1) AS Asiste_Programa_VIH,A.cual1 AS Cual,FN_CATALOGODESC(170,A.adhe_tra_antirre1) AS Adherente_Antirretroviral,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,

FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_vihgest` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_vihgest` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_violges($txt){
	$sql="SELECT
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_gestante AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,
A.fecha_seg AS Fecha_Seguimiento, FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento, FN_CATALOGODESC(87,A.evento) AS Evento, FN_CATALOGODESC(73,A.estado_s) AS Estado,
FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado, FN_CATALOGODESC(136,A.etapa) AS Etapa, FN_CATALOGODESC(137,A.sema_gest) AS Semanas_Gestacion_Posevento,
FN_CATALOGODESC(170,A.asis_ctrpre) AS Asiste_control_Prenatal, FN_CATALOGODESC(170,A.exam_lab) AS Examenes_Laboratorio, FN_CATALOGODESC(170,A.esqu_vacuna) AS Esquema_Vacuna_Completo, FN_CATALOGODESC(170,A.cons_micronutr) AS Consume_Micronutrientes, 
A.fecha_obstetrica AS Fecha_Evento_Obstetrico, FN_CATALOGODESC(137,A.edad_gesta) AS Edad_Gestacional_Evento, FN_CATALOGODESC(193,A.resul_gest) AS Resultado_Gestacion,
FN_CATALOGODESC(170,A.meto_fecunda) AS Cuenta_Metodo_Fecundidad, FN_CATALOGODESC(138,A.cual) AS Cual_Metodo, FN_CATALOGODESC(170,A.peso_nacer) AS Peso_RN_Nacer,
FN_CATALOGODESC(170,A.asiste_control) AS Asiste_control_CYD, FN_CATALOGODESC(170,A.vacuna_comple) AS Esquema_Vacuna_Completo, FN_CATALOGODESC(170,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva, FN_CATALOGODESC(170,A.persis_riesgo) AS Persisten_Riesgos_Asociados, FN_CATALOGODESC(170,A.apoy_sector) AS Apoyo_Otro_Sector,
FN_CATALOGODESC(89,A.cual_sec) AS Cual_Sector, FN_CATALOGODESC(170,A.tam_cope) AS Aplica_Tamizaje_Cope, FN_CATALOGODESC(140,A.total_afron) AS Cope_Afrontamiento,
FN_CATALOGODESC(141,A.total_evita) AS Cope_Evitacion,
FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta, FN_CATALOGODESC(79,A.ruta) AS Ruta, FN_CATALOGODESC(77,A.novedades) AS Novedades, FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid, A.caso_afirmativo AS Relacione_Cuales, A.otras_condiciones AS Otras_Condiciones, A.observaciones AS Observaciones,
FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,
FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre, A.fecha_cierre AS Fecha_Cierre, FN_CATALOGODESC(170,A.aplica_tamiz) AS Aplica_Tamizaje_Cope, FN_CATALOGODESC(78,A.liker_dificul) AS Liker_Dificultades, FN_CATALOGODESC(78,A.liker_emocion) AS Liker_Emociones, FN_CATALOGODESC(78,A.liker_decision) AS Liker_Decisiones, FN_CATALOGODESC(140,A.cope_afronta) AS Cope_Afrontamiento, FN_CATALOGODESC(141,A.cope_evitacion) AS Cope_Evitacion, FN_CATALOGODESC(142,A.incremen_afron) AS Estrategia_Afrontamiento, FN_CATALOGODESC(143,A.incremen_evita) AS Estrategia_Evitacion, FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_violges` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_violges` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario  WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}


function lis_violreite($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_violreite AS Cod_Registro,G.subred AS Subred,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,

A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.numsegui) AS N°_Seguimiento,FN_CATALOGODESC(87,A.evento) AS Evento,FN_CATALOGODESC(73,A.estado_s) AS Estado,FN_CATALOGODESC(74,A.motivo_estado) AS Motivo_Estado,

FN_CATALOGODESC(88,A.asiste_control) AS Asiste_Controles_CYD,FN_CATALOGODESC(88,A.vacuna_comple) AS Esquema_Vacuna_Completo,FN_CATALOGODESC(88,A.lacmate_exclu) AS Lactancia_Materna_Exclusiva,FN_CATALOGODESC(88,A.lacmate_comple) AS Lactancia_Materna_Complementaria,FN_CATALOGODESC(88,A.alime_complemen) AS Alimentacion_Complementaria,FN_CATALOGODESC(170,A.riesgo_violen) AS Persisten_Riesgos_Violencia,FN_CATALOGODESC(170,A.apoyo_sector) AS Apoyo_Otro_Sector,FN_CATALOGODESC(89,A.cual_sector) AS Cual_Sector,

FN_CATALOGODESC(90,A.estrategia_1) AS Estrategia_Plan_1,FN_CATALOGODESC(90,A.estrategia_2) AS Estrategia_Plan_2,
FN_CATALOGODESC(22,A.acciones_1) AS Accion_1,FN_CATALOGODESC(75,A.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,A.acciones_2) AS Accion_2,FN_CATALOGODESC(75,A.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,A.acciones_3) AS Accion_3,FN_CATALOGODESC(75,A.desc_accion3) AS Descripcion_Accion_3,
FN_CATALOGODESC(170,A.activa_ruta) AS Activacion_Ruta,FN_CATALOGODESC(79,A.ruta) AS Ruta,FN_CATALOGODESC(77,A.novedades) AS Novedades,FN_CATALOGODESC(170,A.signos_covid) AS Signos_Sintomas_Covid,A.caso_afirmativo AS Relacione_Cuales,A.otras_condiciones AS Otras_Condiciones,A.observaciones AS Observaciones,

FN_CATALOGODESC(170,A.cierre_caso) AS Cierre_de_Caso,FN_CATALOGODESC(198,A.motivo_cierre) AS Motivo_cierre,A.fecha_cierre AS Fecha_Cierre,FN_CATALOGODESC(78,A.liker_dificul) AS Liker_Dificultades,FN_CATALOGODESC(78,A.liker_emocion) AS Liker_Emociones,FN_CATALOGODESC(78,A.liker_decision) AS Liker_Decisiones,FN_CATALOGODESC(170,A.redu_riesgo_cierre) AS Reduccion_de_Riesgo,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `vsp_violreite` A

LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `vsp_violreite` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_usercreate($txt){
	$sql="SELECT 
G.subred AS Subred,F.idpre AS Cod_Predio, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', F.id_fam AS Cod_Familia,P.idpeople AS Cod_Persona, TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS Edad_Actual, FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(19,P.genero) AS Genero, FN_CATALOGODESC(49,P.oriensexual) AS Orientacion_Sexual, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad, FN_CATALOGODESC(16,P.etnia) AS Etnia, FN_CATALOGODESC(15,P.pueblo) AS Pueblo_Indigena, FN_CATALOGODESC(178,P.pobladifer) AS Poblacion_Diferencial, FN_CATALOGODESC(14,P.discapacidad) AS Tipo_Discapacidad, FN_CATALOGODESC(175,P.ocupacion) AS Ocupacion, FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,
P.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, P.fecha_create AS Fecha_Creacion,FN_CATALOGODESC(170,P.encuentra) Se_Encuentra
FROM `person` P
LEFT JOIN hog_fam F ON P.vivipersona=F.id_fam
LEFT JOIN hog_geo G ON F.idpre=G.idgeo
LEFT JOIN usuarios U ON P.usu_creo=U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred9();
	$sql.=whe_date9();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `person` P LEFT JOIN hog_fam F ON P.vivipersona=F.id_fam LEFT JOIN hog_geo G ON F.idpre=G.idgeo LEFT JOIN usuarios U ON P.usu_creo=U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred9();
	$tot.=whe_date9();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_apgar($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_apgar AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma,
FN_CATALOGODESC(37,A.ayuda_fam) AS Apgar_7_A_17_Años_Preg_1, FN_CATALOGODESC(37,A.fam_comprobl) AS Apgar_7_A_17_Años_Preg_2, FN_CATALOGODESC(37,A.fam_percosnue) AS Apgar_7_A_17_Años_Preg_3, FN_CATALOGODESC(37,A.fam_feltrienf) AS Apgar_7_A_17_Años_Preg_4, FN_CATALOGODESC(37,A.fam_comptiemjun) AS Apgar_7_A_17_Años_Preg_5,
FN_CATALOGODESC(137,A.sati_famayu) AS Apgar_Mayor_de_18_Años_Preg_1, FN_CATALOGODESC(137,A.sati_famcompro) AS Apgar_Mayor_de_18_Años_Preg_2, FN_CATALOGODESC(137,A.sati_famapoemp) AS Apgar_Mayor_de_18_Años_Preg_3, FN_CATALOGODESC(137,A.sati_famemosion) AS Apgar_Mayor_de_18_Años_Preg_4, FN_CATALOGODESC(137,A.sati_famcompar) AS Apgar_Mayor_de_18_Años_Preg_5, 
A.puntaje, A.descripcion,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_apgar` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred10();
	$sql.=whe_date10();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_apgar` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario  WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred10();
	$tot.=whe_date10();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}




function lis_epoc($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_epoc AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma,

IF(A.tose_muvedias = 0, 'NO', 'SI') AS Tose_Muchas_Veces,IF(A.tiene_flema = 0, 'NO', 'SI') AS Tiene_Flemas_Mayoria_Dias,IF(A.aire_facil = 0, 'NO', 'SI') AS Se_Queda_Sin_Aire_Facilmente,IF(A.mayor = 0, 'NO', 'SI') AS Mayor_40_Años,IF(A.fuma = 0, 'NO', 'SI') AS Fuma_o_Exfumador,
A.puntaje AS Puntaje,A.descripcion AS Clasificacion_Puntaje,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_epoc` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred10();
	$sql.=whe_date10();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_epoc` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario  WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred10();
	$tot.=whe_date10();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}


function lis_findrisc($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_findrisc AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma,

A.peso AS Peso,A.talla AS Talla,A.imc AS Imc,A.perimcint AS Perimetro_Cintura,
FN_CATALOGODESC(43,A.actifisica) AS Actividad_Fisica,FN_CATALOGODESC(46,A.verduras) AS Consumo_Verduras_Frutas,FN_CATALOGODESC(56,A.hipertension) AS Toma_Medicamento_Hiper,
FN_CATALOGODESC(57,A.glicemia) AS Valores_Altos_Glucosa,FN_CATALOGODESC(41,A.diabfam) AS Diabetes_Familiares,
A.puntaje AS Puntaje,A.descripcion AS Clasificacion_Puntaje,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_findrisc` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred10();
	$sql.=whe_date10();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_findrisc` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario  WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred10();
	$tot.=whe_date10();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_oms($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.idoms AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma,

FN_CATALOGODESC(170,A.diabetes) AS Tiene_Diabetes, FN_CATALOGODESC(170,A.fuma) AS Fuma, A.tas AS Tension_Arterial_Sistolica, REPLACE(REPLACE(A.puntaje, 'LT', '<'), 'GT', '>') AS Puntaje, A.descripcion AS Clasificacion_Puntaje,

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_oms` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred10();
	$sql.=whe_date10();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_oms` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario  WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred10();
	$tot.=whe_date10();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_admisi($txt){
	$sql="SELECT 
G.subred AS Subred, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', G.idgeo AS Cod_predio, F.id_fam AS Cod_Familia,
P.idpeople AS Cod_Persona, P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Docuumento, CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad, FN_CATALOGODESC(16,P.etnia) AS Etnia,FN_CATALOGODESC(15,P.pueblo) AS Pueblo_Etnia, FN_CATALOGODESC(14,P.discapacidad) AS Tipo_Discapacidad, FN_CATALOGODESC(17,P.regimen) AS Regimen, FN_CATALOGODESC(18,P.eapb) AS Eapb,
A.id_factura Cod_Registro, A.soli_admis AS Solicitud_Admision, A.fecha_consulta AS Fecha_Consulta, FN_CATALOGODESC(182,A.tipo_consulta) AS Tipo_Consulta, A.cod_admin AS Cod_Admision, FN_CATALOGODESC(126,A.cod_cups) AS Codigo_CUPS, FN_CATALOGODESC(127,A.final_consul) AS Finalidad_Consulta, A.cod_factura AS Cod_Factura, FN_CATALOGODESC(184,A.estado_hist) AS Estado_Admision,
A.fecha_create AS Fecha_Creacion, A.usu_creo AS Cod_Usuario_Crea, U.nombre AS Nombre_Usuario_Crea, U.perfil AS Perfil_Usuario_Crea, 
A.fecha_update AS Fecha_Edicion, A.usu_update AS Cod_Usuario_Edita, U1.nombre AS Nombre_Usuario_Edita, U1.perfil AS Perfil_Usuario_Edita, FN_CATALOGODESC(284,A.estado) AS 'Estado Registro'
FROM `adm_facturacion` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona =  F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
LEFT JOIN usuarios U1 ON A.usu_update = U1.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred11();
	$sql.=whe_date11();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `adm_facturacion` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona =  F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario LEFT JOIN usuarios U1 ON A.usu_update = U1.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred11();
	$tot.=whe_date11();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_atencion($txt){
	$sql="SELECT 
G.subred AS Subred, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', G.idgeo AS Cod_predio, F.id_fam AS Cod_Familia,
P.idpeople AS Cod_Persona, P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Docuumento, CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad, FN_CATALOGODESC(16,P.etnia) AS Etnia,FN_CATALOGODESC(15,P.pueblo) AS Pueblo_Etnia, FN_CATALOGODESC(14,P.discapacidad) AS Tipo_Discapacidad, FN_CATALOGODESC(17,P.regimen) AS Regimen, FN_CATALOGODESC(18,P.eapb) AS Eapb,
A.id_aten AS Cod_Registro, A.id_factura AS Cod_Admision, A.fecha_atencion AS Fecha_Consulta, FN_CATALOGODESC(182,A.tipo_consulta) AS Tipo_Consulta, FN_CATALOGODESC(126,A.codigo_cups) AS Codigo_CUPS, FN_CATALOGODESC(127,A.finalidad_consulta) AS Finalidad_Consulta, FN_DESC(3,A.diagnostico1) AS DX1,FN_DESC(3,A.diagnostico2) AS DX2, FN_DESC(3,A.diagnostico3) AS DX3, A.fertil AS '¿Mujer_Edad_Fertil?', A.preconcepcional AS '¿Consulta_Preconsecional?', A.metodo AS '¿Metodo_Planificacion?', FN_CATALOGODESC(129,A.anticonceptivo) AS '¿Cua_Metodo?', A.planificacion AS Planificacion,A.mestruacion AS Fur,
A.vih AS Prueba_VIH, FN_CATALOGODESC(187,A.resul_vih) AS Resultado_VIH, A.hb AS Prueba_HB, FN_CATALOGODESC(188,A.resul_hb) AS Resultado_HB, A.trepo_sifil AS Trepomina_Sifilis, FN_CATALOGODESC(188,A.resul_sifil) AS Resultado_Trepo_Sifilis, A.pru_embarazo AS Prueba_Embarazo, FN_CATALOGODESC(88,A.resul_emba) AS Resultado_Embarazo, A.pru_apetito AS Prueba_Apetito, A.resul_apetito AS Resultado_Apetito,
A.orden_psicologia AS Orden_Psicologia, A.relevo AS Aplica_Relevo, FN_CATALOGODESC(203,A.estrategia) AS Estrategia, FN_CATALOGODESC(236,A.motivo_estrategia) AS Motivo_Estrategia,
A.usu_creo AS Cod_Usuario, U.nombre AS Nombre_Usuario, U.perfil AS Perfil_Usuario, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro

FROM `eac_atencion` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona =  F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred12();
	$sql.=whe_date12();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `eac_atencion` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona =  F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred12();
	$tot.=whe_date12();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_pcindi($txt){
	$sql="SELECT 
G.subred AS Subred, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', G.idgeo AS Cod_predio, F.id_fam AS Cod_Familia,
P.idpeople AS Cod_Persona, P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Docuumento, CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad, FN_CATALOGODESC(16,P.etnia) AS Etnia,FN_CATALOGODESC(15,P.pueblo) AS Pueblo_Etnia, FN_CATALOGODESC(14,P.discapacidad) AS Tipo_Discapacidad, FN_CATALOGODESC(17,P.regimen) AS Regimen, FN_CATALOGODESC(18,P.eapb) AS Eapb,
A.id_aten AS Cod_Registro, A.fecha_atencion AS Fecha_Consulta, A.evento_interes AS Notificacion_Evento_Interes_SP, FN_CATALOGODESC(134,A.evento) AS Cual_Evento_SP, A.cuale_vento AS Otro_Evento_SP, A.sirc AS SIRC_usuarios_otras_EAPB, A.ruta_sirc AS Ruta_Sirc, A.remision AS Usuario_requiere_control, A.cual_remision AS Cuales, A.orden_vacunacion AS Orden_Vacunacion, FN_CATALOGODESC(185,A.vacunacion) AS Vacunacion, A.orden_laboratorio AS Orden_Laboratorio, FN_CATALOGODESC(133,A.laboratorios) AS Laboratorios, A.orden_medicamentos AS Orden_Medicamentos, FN_CATALOGODESC(186,A.medicamentos) AS Medicamentos, A.ruta_continuidad AS Activacion_Ruta, A.continuidad AS Cual_Ruta, A.orden_imagenes AS Ordena_Imágenes_Diagnósticas, A.orden_psicologia AS Orden_Psicologia, A.relevo AS Aplica_RBC, A.estrategia AS Estrategia, A.motivo_estrategia AS Motivo_Estrategia,
A.usu_creo AS Cod_Usuario, U.nombre AS Nombre_Usuario, U.perfil AS Perfil_Usuario, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `eac_atencion` A
LEFT JOIN person P ON A.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona =  F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred12();
	$sql.=whe_date12();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `eac_atencion` A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona =  F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred12();
	$tot.=whe_date12();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_famcrea($txt){
	$sql="SELECT 
G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', F.idpre AS Cod_Predio, F.id_fam AS Cod_Familia, 
F.usu_create, U.nombre AS Nombre_Usuario, U.perfil AS Perfil_Usuario, F.fecha_create AS Fecha_Creacion
FROM `hog_fam` F
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON F.usu_create = U.id_usuario
WHERE F.usu_create NOT IN (1022358140) ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred13();
	$sql.=whe_date13();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_fam` F  LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON F.usu_create = U.id_usuario WHERE F.usu_create NOT IN (1022358140) ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred13();
	$tot.=whe_date13();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_gesrut($txt){
 	$sql="SELECT G.subred AS 'Subred', G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', G.idgeo AS 'Cod Predio', RC.id_rutclas AS 'Cod Registro', RC.idrutges AS 'Cod Ruteo', FN_CATALOGODESC(191,RC.preclasif) AS 'Cohorte de Riesgo', FN_CATALOGODESC(235,RC.clasifica) AS 'Grupo De Población Priorizada', FN_CATALOGODESC(273,RC.riesgo) AS 'Riesgo', 

FN_CATALOGODESC(22,RC.accion1) AS Accion_1, FN_CATALOGODESC(75,RC.desc_accion1) AS Descripcion_Accion_1,
FN_CATALOGODESC(22,RC.accion2) AS Accion_2, FN_CATALOGODESC(75,RC.desc_accion2) AS Descripcion_Accion_2,
FN_CATALOGODESC(22,RC.accion3) AS Accion_3, FN_CATALOGODESC(75,RC.desc_accion3) AS Descripcion_Accion_3,
RC.fecha AS 'Fecha de Programación', FN_CATALOGODESC(170,RC.solic_agend) AS '¿Solicito Servicio Agendamiento?', FN_CATALOGODESC(170,RC.ruta) AS '¿Activo Ruta?', FN_CATALOGODESC(170,RC.sectorial) AS '¿Sectorial?', FN_CATALOGODESC(170,RC.intsectorial) AS '¿Intersectorial?', FN_CATALOGODESC(170,RC.entornos) AS '¿Entorno?', FN_CATALOGODESC(170,RC.aseguram) AS '¿Aseguramiento?',
FN_CATALOGODESC(269,RC.accion) AS 'Definir Acción', RC.profesional AS 'Documento Colaborador Asignado', U.nombre AS 'Nombre Colaborador Asignado', U.perfil AS 'Perfil Colaborador Asignado', 

RC.usu_creo AS 'Documento Colaborador', U1.nombre AS 'Nombre Colaborador', U1.perfil AS 'Perfil Colaborador'
FROM `eac_ruteo_clas` RC
LEFT JOIN eac_ruteo_ges RG ON RC.idrutges = RG.id_rutges
LEFT JOIN eac_ruteo R ON RC.idrutges = R.id_ruteo
LEFT JOIN hog_geo G ON R.idgeo = G.idgeo
LEFT JOIN usuarios U ON RC.profesional = U.id_usuario
LEFT JOIN usuarios U1 ON RC.usu_creo = U1.id_usuario WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred14();
	$sql.=whe_date14();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `eac_ruteo_clas` RC LEFT JOIN eac_ruteo_ges RG ON RC.idrutges = RG.id_rutges LEFT JOIN eac_ruteo R ON RC.idrutges = R.id_ruteo LEFT JOIN hog_geo G ON R.idgeo = G.idgeo LEFT JOIN usuarios U ON RC.profesional = U.id_usuario LEFT JOIN usuarios U1 ON RC.usu_creo = U1.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred14();
	$tot.=whe_date14();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta); 
}

function lis_rbc($txt){
	$sql="SELECT 
G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, R.idrelevo AS Cod_Registro, G.subred AS Subred,  G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
R.acep_rbc AS Aceptacion_estrategia_RBC, R.fecha_acep AS Fecha_Aceptacion,

P.tipo_doc AS Tipo_Documento_Cuidador, P.idpersona AS N°_Documento_Cuidador, CONCAT(P.nombre1,' ',P.nombre2,' ',P.apellido1,' ',P.apellido2) AS Nombres_Cuidador, P.fecha_nacimiento AS Fecha_Nacimiento_Cuidador, FN_CATALOGODESC(21,P.sexo) AS Sexo_Cuidador, FN_CATALOGODESC(17,P.regimen) AS Regimen_Cuidador,FN_CATALOGODESC(18,P.eapb) AS Eapb_Cuidador, FN_CATALOGODESC(28,R.ante_cuidador) AS Antec_Patolo_Cuidador, R.otros_antecuidador AS Otro_Antec_Patolo_Cuidador, R.np_cuida AS Personas_al_Cuidado, 

P1.tipo_doc AS Tipo_Documento_Percuida1, P1.idpersona AS N°_Documento_Percuida1, CONCAT(P1.nombre1,' ',P1.nombre2,' ',P1.apellido1,' ',P1.apellido2) AS Nombres_Percuida1, P1.fecha_nacimiento AS Fecha_Nacimiento_Percuida1, FN_CATALOGODESC(21,P1.sexo) AS Sexo_Percuida1, FN_CATALOGODESC(17,P1.regimen) AS Regimen_Percuida1, FN_CATALOGODESC(18,P1.eapb) AS Eapb_Percuida1, FN_CATALOGODESC(28,R.antecedentes_1) AS Antec_Patolo_Percuida1, R.otro_1 AS Otro_Antec_Patolo_Percuida1, R.cert_disca1 AS Certificado_Disca_Percuida1,

P2.tipo_doc AS Tipo_Documento_Percuida2, P2.idpersona AS N°_Documento_Percuida2, CONCAT(P2.nombre1,' ',P2.nombre2,' ',P2.apellido1,' ',P2.apellido2) AS Nombres_Percuida2, P2.fecha_nacimiento AS Fecha_Nacimiento_Percuida2, FN_CATALOGODESC(21,P2.sexo) AS Sexo_Percuida2, FN_CATALOGODESC(17,P2.regimen) AS Regimen_Percuida2, FN_CATALOGODESC(18,P2.eapb) AS Eapb_Percuida2, FN_CATALOGODESC(28,R.antecedentes_2) AS Antec_Patolo_Percuida2, R.otro_2 AS Otro_Antec_Patolo_Percuida2, R.cert_disca2 AS Certificado_Disca_Percuida2,

P3.tipo_doc AS Tipo_Documento_Percuida3, P3.idpersona AS N°_Documento_Percuida3, CONCAT(P3.nombre1,' ',P3.nombre2,' ',P3.apellido1,' ',P3.apellido2) AS Nombres_Percuida3, P3.fecha_nacimiento AS Fecha_Nacimiento_Percuida3, FN_CATALOGODESC(21,P3.sexo) AS Sexo_Percuida3, FN_CATALOGODESC(17,P3.regimen) AS Regimen_Percuida3, FN_CATALOGODESC(18,P3.eapb) AS Eapb_Percuida3, FN_CATALOGODESC(28,R.antecedentes_3) AS Antec_Patolo_Percuida3, R.otro_3 AS Otro_Antec_Patolo_Percuida3, R.cert_disca3 AS Certificado_Disca_Percuida3


,R.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, R.estado AS Estado_Registro
 
FROM `rel_relevo` R
LEFT JOIN person P ON R.id_people = P.idpeople
LEFT JOIN person P1 ON R.cuidado_1 = P1.idpeople
LEFT JOIN person P2 ON R.cuidado_2 = P2.idpeople
LEFT JOIN person P3 ON R.cuidado_3 = P3.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo 

LEFT JOIN usuarios U ON R.usu_creo= U.id_usuario

WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred15();
	$sql.=whe_date15();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `rel_relevo` R LEFT JOIN person P ON R.id_people = P.idpeople LEFT JOIN person P1 ON R.cuidado_1 = P1.idpeople LEFT JOIN person P2 ON R.cuidado_2 = P2.idpeople LEFT JOIN person P3 ON R.cuidado_3 = P3.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred15();
	$tot.=whe_date15();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_cope($txt){
	$sql="SELECT 
G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, G.subred AS Subred,G.localidad AS Localidad,FN_CATALOGODESC(3,G.zona) AS Zona, FN_CATALOGODESC(7,G.upz) AS Upz, G.barrio AS Barrio, G.direccion AS Direccion, G.cordx AS Cordenada_X, G.cordy AS Cordenada_Y, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento, P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(19,P.genero) AS Genero, FN_CATALOGODESC(49,P.oriensexual) AS Orientacion_Sexual, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad ,FN_CATALOGODESC(16,P.etnia) AS ETNIA, FN_CATALOGODESC(15,P.pueblo) AS Pueblo, P.idioma AS Habla_Español, FN_CATALOGODESC(178,P.pobladifer) AS Poblacion_Diferencial, FN_CATALOGODESC(14,P.discapacidad) AS Tipo_Discapacidad, FN_CATALOGODESC(54,P.vinculo_jefe) AS Vinculo_Jefe_Hogar, FN_CATALOGODESC(175,P.ocupacion) AS Ocupacion,FN_CATALOGODESC(17,P.regimen) AS Regimen, FN_CATALOGODESC(18,P.eapb) AS Eapb, P.afiliaoficio AS Afiliacon_por_Oficio, FN_CATALOGODESC(180,P.niveduca) AS Nivel_Educativo, P.abanesc AS Razón_Abandono_Escolar, P.tiemdesem AS Tiempo_Desempleo,

C.fecha_toma AS Fecha,
FN_CATALOGODESC(120,C.reporta)  AS Caso_Reportado,
FN_CATALOGODESC(135,C.pregunta1)  AS Pregunta_1,
FN_CATALOGODESC(135,C.pregunta2)  AS Pregunta_2,
FN_CATALOGODESC(135,C.pregunta3)  AS Pregunta_3,
FN_CATALOGODESC(135,C.pregunta4)  AS Pregunta_4,
FN_CATALOGODESC(135,C.pregunta5)  AS Pregunta_5,
FN_CATALOGODESC(135,C.pregunta6)  AS Pregunta_6,
FN_CATALOGODESC(135,C.pregunta7)  AS Pregunta_7,
FN_CATALOGODESC(135,C.pregunta8)  AS Pregunta_8,
FN_CATALOGODESC(135,C.pregunta9)  AS Pregunta_9,
FN_CATALOGODESC(135,C.pregunta10)  AS Pregunta_10,
FN_CATALOGODESC(135,C.pregunta11)  AS Pregunta_11,
FN_CATALOGODESC(135,C.pregunta12)  AS Pregunta_12,
FN_CATALOGODESC(135,C.pregunta13)  AS Pregunta_13,
FN_CATALOGODESC(135,C.pregunta14)  AS Pregunta_14,
FN_CATALOGODESC(135,C.pregunta15)  AS Pregunta_15,
FN_CATALOGODESC(135,C.pregunta16)  AS Pregunta_16,
FN_CATALOGODESC(135,C.pregunta17)  AS Pregunta_17,
FN_CATALOGODESC(135,C.pregunta18)  AS Pregunta_18,
FN_CATALOGODESC(135,C.pregunta19)  AS Pregunta_19,
FN_CATALOGODESC(135,C.pregunta20)  AS Pregunta_20,
FN_CATALOGODESC(135,C.pregunta21)  AS Pregunta_21,
FN_CATALOGODESC(135,C.pregunta22)  AS Pregunta_22,
FN_CATALOGODESC(135,C.pregunta23)  AS Pregunta_23,
FN_CATALOGODESC(135,C.pregunta24)  AS Pregunta_24,
FN_CATALOGODESC(135,C.pregunta25)  AS Pregunta_25,
FN_CATALOGODESC(135,C.pregunta26)  AS Pregunta_26,
FN_CATALOGODESC(135,C.pregunta27)  AS Pregunta_27,
FN_CATALOGODESC(135,C.pregunta28)  AS Pregunta_28,

C.puntajea  AS Puntaje_Afrontamiento,
C.descripciona  AS Descipcion_Afrontamiento,
C.puntajee  AS Puntaje_Evitacion,
C.descripcione  AS Descipcion_Evitacion

 FROM `hog_tam_cope` C
 
LEFT JOIN person P ON C.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo	
 WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred16();
	$sql.=whe_date16();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_cope` C LEFT JOIN person P ON C.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred16();
	$tot.=whe_date16();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
} 

function lis_rbc_ses($txt){
	$sql="SELECT
	G.subred AS Subred, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, R.id_people AS Cod_Persona, R.idsesion AS Cod_Registro,  
R.rel_validacion1 AS N°_Sesion, R.rel_validacion2 AS Fecha_Sesion, R.rel_validacion3 AS Perfil, FN_CATALOGODESC(301,R.rel_validacion4) AS Actividad_Respiro, R.rel_validacion5 AS Descripcion_Intervencion,
FN_CATALOGODESC(103,R.autocuidado) AS Autocuidado, FN_CATALOGODESC(194,R.activesparc) AS Actividades_Esparcimiento, FN_CATALOGODESC(157,R.infeducom) AS Inf_Educa_Comuni_salud,
R.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, R.estado AS Estado_Registro
FROM
`rel_sesion` R
LEFT JOIN person P ON R.id_people = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON R.usu_creo= U.id_usuario

WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred17();
	$sql.=whe_date17();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `rel_sesion` R LEFT JOIN person P ON R.id_people = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON R.usu_creo= U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred17();
	$tot.=whe_date17();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_sescole($txt){
	$sql="SELECT
	G.idgeo AS Cod_Predio, G.subred AS Subred, G.zona AS Zona, G.localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio', C.id_cole AS Cod_Registro, C.fecha AS Fecha_Sesion, FN_CATALOGODESC(239,C.tipo_activ) AS Tipo_Actividad, C.lugar AS Lugar_Actividad, FN_CATALOGODESC(242,C.jornada) AS Jornada_Actividad, C.equipo AS Equipo_Realiza_Actividad, 
FN_CATALOGODESC(237,C.tematica1) AS Actividad_Tematica_1, FN_CATALOGODESC(238,C.des_temati1) AS Descrip_Tematica_1,
FN_CATALOGODESC(237,C.tematica2) AS Actividad_Tematica_2, FN_CATALOGODESC(238,C.des_temati2) AS Descrip_Tematica_2,
FN_CATALOGODESC(237,C.tematica3) AS Actividad_Tematica_3, FN_CATALOGODESC(238,C.des_temati3) AS Descrip_Tematica_3,
FN_CATALOGODESC(237,C.tematica4) AS Actividad_Tematica_4, FN_CATALOGODESC(238,C.des_temati4) AS Descrip_Tematica_4,
FN_CATALOGODESC(237,C.tematica5) AS Actividad_Tematica_5, FN_CATALOGODESC(238,C.des_temati5) AS Descrip_Tematica_5,
FN_CATALOGODESC(237,C.tematica6) AS Actividad_Tematica_6, FN_CATALOGODESC(238,C.des_temati6) AS Descrip_Tematica_6,
FN_CATALOGODESC(237,C.tematica7) AS Actividad_Tematica_7, FN_CATALOGODESC(238,C.des_temati7) AS Descrip_Tematica_7,
FN_CATALOGODESC(237,C.tematica8) AS Actividad_Tematica_8, FN_CATALOGODESC(238,C.des_temati8) AS Descrip_Tematica_8,
P.id_person AS Cod_Persona, P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento, P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo, FN_CATALOGODESC(19,P.genero) AS Genero, FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad, FN_CATALOGODESC(16,P.etnia) AS Etnia, FN_CATALOGODESC(15,P.pueblo) AS Pueblo_Etnia, FN_CATALOGODESC(17,P.regimen) AS Regimen,FN_CATALOGODESC(18,P.eapb) AS Eapb,
C.usu_create AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, C.estado AS 'Estado Registro'
FROM `persescol` P
LEFT JOIN hog_sescole C ON P.sesion = C.id_cole
LEFT JOIN hog_geo G ON C.idpre = G.idgeo
LEFT JOIN usuarios U ON C.usu_create = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred18();
	$sql.=whe_date18();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `persescol` P  LEFT JOIN hog_sescole C ON P.sesion = C.id_cole LEFT JOIN hog_geo G ON C.idpre = G.idgeo LEFT JOIN usuarios U ON C.usu_create = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred18();
	$tot.=whe_date18();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_psicoses1($txt){
	$sql="SELECT
	G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, A.idpsi AS Cod_Registro, G.subred AS Subred, FN_CATALOGODESC(3,G.zona) AS Zona, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',

P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS Nombres,concat(P.apellido1,' ',P.apellido2) AS Apellidos,P.fecha_nacimiento AS Fecha_Nacimiento,FN_CATALOGODESC(21,P.sexo) AS Sexo,FN_CATALOGODESC(19,P.genero) AS Genero,FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad,FN_CATALOGODESC(16,P.etnia) AS Etnia,FN_CATALOGODESC(15,P.pueblo) AS Pueblo,
A.fecha_ses1 AS Fecha_Sesion, A.tipo_caso AS Tipo_de_Caso, A.cod_admin AS Cod_Admision, 
H.analisis AS Hamilton_Inicial, Z.analisis AS Zung_Inicial, W.analisis AS Whodas_Inicial, A.eva_chips AS Resultado_Eva_Chips, 
A.psi_validacion1 AS Pensamiento_Termina_Vida, A.psi_validacion2 AS Accion_Termina_Vida, A.psi_validacion3 AS Plan_termina_Vida_Sem, A.psi_validacion4 AS Descripcion_Evaluacion, A.psi_validacion5 AS Persona_Le_Entiende, A.psi_validacion6 AS Persona_Acompaña_Razonable, A.psi_validacion7 AS Respuestas_Raras_Inusuales,
A.psi_validacion8 AS No_Contacto_Realidad, A.psi_validacion9 AS Posibles_Transtornos, A.psi_validacion10 AS Plan_Termina_Vida, A.psi_validacion11 AS Posible_Transtorno_Mental, FN_DESC(3,A.psi_diag12) as Impresion_DX, A.psi_validacion13 AS Plan_Menejo_Terapeutico, A.psi_validacion14 AS No_Plan_Manejo_Terapeutico, A.otro AS Otro, A.psi_validacion15 AS Descripcion_Plan_Manejo, A.numsesi AS N°_Sesiones,
A.usu_creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, A.fecha_create, A.estado AS Estado_Registro

FROM `psi_psicologia` A
LEFT JOIN person P ON A.id_people = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN hog_tam_hamilton H ON A.id_people = H.idpeople AND H.momento = 1  
LEFT JOIN hog_tam_zung Z ON A.id_people = Z.idpeople AND Z.momento = 1
LEFT JOIN hog_tam_whodas W ON A.id_people = W.idpeople AND W.momento = 1
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred19();
	$sql.=whe_date19();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `psi_psicologia` A LEFT JOIN person P ON A.id_people = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN hog_tam_hamilton H ON A.id_people = H.idpeople AND H.momento = 1 LEFT JOIN hog_tam_zung Z ON A.id_people = Z.idpeople AND Z.momento = 1 LEFT JOIN hog_tam_whodas W ON A.id_people = W.idpeople AND W.momento = 1 LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred19();
	$tot.=whe_date19();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_psicoses2($txt){
	$sql="SELECT
	G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, A.id_sesion2 AS Cod_Registro, G.subred AS Subred, FN_CATALOGODESC(3,G.zona) AS Zona, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS Nombres,concat(P.apellido1,' ',P.apellido2) AS Apellidos,P.fecha_nacimiento AS Fecha_Nacimiento,FN_CATALOGODESC(21,P.sexo) AS Sexo,FN_CATALOGODESC(19,P.genero) AS Genero,FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad,FN_CATALOGODESC(16,P.etnia) AS Etnia,FN_CATALOGODESC(15,P.pueblo) AS Pueblo,

A.psi_fecha_sesion AS Fecha_Sesion, A.cod_admin2 AS Cod_Admision, A.psi_validacion1 AS Problema_Que_Aflije, FN_CATALOGODESC(124,A.psi_validacion2) AS Cuanto_Afecto_Semana, A.psi_validacion3 AS Otro_Problema_Aflije, FN_CATALOGODESC(124,A.psi_validacion4) AS Otro_Cuanto_Afecto_Semana, A.psi_validacion5 AS Causa_Problema, FN_CATALOGODESC(124,A.psi_validacion6) AS Cuan_Dificil_Resultado, FN_CATALOGODESC(124,A.psi_validacion7) AS Como_Se_Sintio_Semana, A.psi_validacion8 AS Actividad_Desarrollar_1, A.psi_validacion9 AS Actividad_Desarrollar_2, A.psi_validacion10 AS Actividad_Desarrollar_3, (FN_CATALOGODESC(124,A.psi_validacion2)+FN_CATALOGODESC(124,A.psi_validacion4)+FN_CATALOGODESC(124,A.psi_validacion6)+FN_CATALOGODESC(124,A.psi_validacion7)) AS Resultado_Evaluacion, FN_CATALOGODESC(160,A.contin_caso) AS Continuidad_Caso,
A.usu_creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, A.fecha_create, A.estado AS Estado_Registro
FROM `psi_sesion2` A
LEFT JOIN person P ON A.id_people = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred20();
	$sql.=whe_date20();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `psi_sesion2` A LEFT JOIN person P ON A.id_people = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred20();
	$tot.=whe_date20();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_psicosesiones($txt){
	$sql="SELECT
	G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, A.idsesipsi AS Cod_Registro, G.subred AS Subred, FN_CATALOGODESC(3,G.zona) AS Zona, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,concat(P.nombre1,' ',P.nombre2) AS Nombres,concat(P.apellido1,' ',P.apellido2) AS Apellidos,P.fecha_nacimiento AS Fecha_Nacimiento,FN_CATALOGODESC(21,P.sexo) AS Sexo,FN_CATALOGODESC(19,P.genero) AS Genero,FN_CATALOGODESC(30,P.nacionalidad) AS Nacionalidad,FN_CATALOGODESC(16,P.etnia) AS Etnia,FN_CATALOGODESC(15,P.pueblo) AS Pueblo,

A.psi_fecha_sesion AS Fecha_Sesion, FN_CATALOGODESC(125,A.psi_sesion) AS N°_Sesion, A.cod_admin4 AS Cod_Admision, A.psi_validacion1 AS Problema_Preocupa_Principio, FN_CATALOGODESC(124,A.psi_validacion2) AS Cuanto_Afecto_Semana, A.psi_validacion3 AS Otro_Problema_Aflije_Principio, FN_CATALOGODESC(124,A.psi_validacion4) AS Otro_Cuanto_Afecto_Semana, A.psi_validacion5 AS Le_Ha_Costado_Hacer_Principio,FN_CATALOGODESC(124,A.difhacer) AS Cuan_Dificil_Resultado,FN_CATALOGODESC(124,A.psi_validacion6) AS Como_Se_Sintio_Semana, A.psi_validacion7 AS Plan_Para_Terminar_Con_Su_Vida, A.psi_validacion8 AS Describa_Pensamientos_Planes, A.psi_validacion9 AS Acciones_Terminar_Su_Vida,FN_CATALOGODESC(130,A.psi_validacion10) AS Plan_Terminar_Con_Su_Vida_Prox_Semana, A.psi_validacion11 AS Describa_Su_Plan, A.psi_validacion12 AS Otro_Problema_Importante, FN_CATALOGODESC(124,A.psi_validacion13) AS Afectado_Otros_Problemas,A.psi_validacion14 AS Actividad_Desarrollar_1,A.psi_validacion15 AS Actividad_Desarrollar_2,A.psi_validacion16 AS Actividad_Desarrollar_3,(FN_CATALOGODESC(124,A.psi_validacion2)+FN_CATALOGODESC(124,A.psi_validacion4)+FN_CATALOGODESC(124,A.psi_validacion6)+FN_CATALOGODESC(124,A.difhacer)+FN_CATALOGODESC(124,A.psi_validacion13)) AS Resultado_Evaluacion,FN_CATALOGODESC(160,A.psi_validacion17) AS Continuidad_Caso,
A.usu_creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, A.fecha_create, A.estado AS Estado_Registro
FROM `psi_sesiones` A
LEFT JOIN person P ON A.id_people = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred21();
	$sql.=whe_date21();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `psi_sesion2` A LEFT JOIN person P ON A.id_people = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred21();
	$tot.=whe_date21();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_barthel($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_barthel AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma, FN_CATALOGODESC(116,A.momento) AS Momento, FN_CATALOGODESC(106,A.comer) AS Comer, FN_CATALOGODESC(107,A.lavarse) AS Lavarse, FN_CATALOGODESC(108,A.vestirse) AS Vestirse, FN_CATALOGODESC(109,A.arreglarse) AS Arreglarse, FN_CATALOGODESC(110,A.deposicion) AS 'Deposiciones_(Según Semana Anterior)', FN_CATALOGODESC(111,A.miccion) AS Miccion, FN_CATALOGODESC(112,A.sanitario) as sanitario ,FN_CATALOGODESC(113, A.trasladarse) AS trasladarse,FN_CATALOGODESC(114, A.deambular) AS deambular,FN_CATALOGODESC(115, A.escalones) AS escalones, A.total AS Total, 
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion
FROM `hog_tam_barthel` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred22();
	$sql.=whe_date22();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_barthel` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred22();
	$tot.=whe_date22();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_hamilton($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia, A.id_hamilton AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma, FN_CATALOGODESC(116,A.momento) AS Momento, FN_CATALOGODESC(117,A.sintoma1) AS '1. Estado de ánimo ansioso.', FN_CATALOGODESC(117,A.sintoma2) AS '2. Tensión.Sensación de tensión', FN_CATALOGODESC(117,A.sintoma3) AS '3. Temores.A la oscuridad', FN_CATALOGODESC(117,A.sintoma4) AS '4. Insomnio.Dificultad para dormirse', FN_CATALOGODESC(117,A.sintoma5) AS '5. Intelectual (cognitivo)', FN_CATALOGODESC(117,A.sintoma6) AS '6. Estado de ánimo deprimido.', FN_CATALOGODESC(117,A.sintoma7) AS '7. Síntomas somáticos generales', FN_CATALOGODESC(117,A.sintoma8) AS '8. Síntomas somáticos generales', FN_CATALOGODESC(117,A.sintoma9) AS '9. Síntomas cardiovasculares.', FN_CATALOGODESC(117,A.sintoma10) AS '10. Síntomas respiratorios.', FN_CATALOGODESC(117,A.sintoma11) AS '11. Síntomas gastrointestinales.', FN_CATALOGODESC(117,A.sintoma12) AS '12. Síntomas genitourinarios.', FN_CATALOGODESC(117,A.sintoma13) AS '13. Síntomas autónomos.', FN_CATALOGODESC(117,A.sintoma14) AS '14. Comportamiento en la entrevista.', A.psiquica AS 'Ansiedad psíquica', A.somatica AS 'Ansiedad somática', A.total AS Puntuacion, A.analisis AS Descripcion_Puntuacion
FROM `hog_tam_hamilton` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred22();
	$sql.=whe_date22();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_hamilton` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred22();
	$tot.=whe_date22();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_whodas($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_whodas AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma, FN_CATALOGODESC(116,A.momento) AS Momento, A.comprension1, A.comprension2, A.comprension3, A.comprension4, A.comprension5, A.comprension6, A.moverse1, A.moverse2, A.moverse3, A.moverse4, A.moverse5, A.cuidado1, A.cuidado2, A.cuidado3, A.cuidado4, A.relacionarce1, A.relacionarce2, A.relacionarce3, A.relacionarce4, A.relacionarce5, A.actividades1, A.actividades2, A.actividades3, A.actividades4, A.actividades5, A.actividades6, A.actividades7, A.actividades8, A.participacion1, A.participacion2, A.participacion3, A.participacion4, A.participacion5, A.participacion6, A.participacion7, A.participacion8, A.whodas_dias1, A.whodas_dias2, A.whodas_dias3, A.porcentaje_comprension, A.porcentaje_moverse, A.porcentaje_cuidado, A.porcentaje_relacionarce, A.porcentaje_actividades, A.porcentaje_participacion, A.porcentaje_total, A.analisis
FROM `hog_tam_whodas` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred22();
	$sql.=whe_date22();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_hamilton` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred22();
	$tot.=whe_date22();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_zarit($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_zarit AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma, FN_CATALOGODESC(116,A.momento) AS Momento,
FN_CATALOGODESC(118,A.valor1) AS 'Piensa que su familiar / persona cuidada le pide más ayuda de la realmente necesita', 
FN_CATALOGODESC(118,A.valor2) AS 'Piensa que debido al tiempo que dedica a su familiar no tiene suficiente tiempo para usted', 
FN_CATALOGODESC(118,A.valor3) AS 'Se siente agobiado por intentar compatibilizar el cuidado de su familiar / persona cuidada con otras responsabilidades (trabajo, familia)', 
FN_CATALOGODESC(118,A.valor4) AS 'Siente vergüenza por la conducta de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor5) AS 'Se siente enfadado cuando está cerca de su familiar/ persona cuidada', 
FN_CATALOGODESC(118,A.valor6) AS 'Piensa que el cuidar de su familiar / persona cuidada afecta negativamente la relación que usted tiene con otros miembros de su familia', 
FN_CATALOGODESC(118,A.valor7) AS 'Tiene miedo por el futuro de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor8) AS 'Piensa que su familiar / persona cuidada depende de usted', 
FN_CATALOGODESC(118,A.valor9) AS 'Se siente tenso cuando está cerca de su familiar', 
FN_CATALOGODESC(118,A.valor10) AS 'Piensa que su salud ha empeorado debido a tener que cuidar de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor11) AS 'Piensa que no tiene tanta intimidad como le gustaría debido a tener que cuidar de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor12) AS 'Piensa que su vida social se ha visto afectada negativamente por tener que cuidar de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor13) AS 'Se siente incómodo por distanciarse de sus amistades debido a tener que cuidar de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor14) AS 'Piensa que su familiar le considera a Usted la única persona que le puede cuidar', 
FN_CATALOGODESC(118,A.valor15) AS 'Piensa que no tiene suficientes ingresos económicos para los gastos de cuidar a su familiar / persona cuidada, además de sus otros gastos', 
FN_CATALOGODESC(118,A.valor16) AS 'Piensa que no será capaz de cuidar a su familiar / persona cuidada por mucho más tiempo', 
FN_CATALOGODESC(118,A.valor17) AS 'Siente que ha perdido el control de su vida desde que comenzó la enfermedad de su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor18) AS 'Desearía poder dejar el cuidado de su familiar / persona cuidada a otra persona', 
FN_CATALOGODESC(118,A.valor19) AS 'Se siente indeciso sobre qué hacer con su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor20) AS 'Piensa que debería hacer más por su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor21) AS 'Piensa que podría cuidar mejor a su familiar / persona cuidada', 
FN_CATALOGODESC(118,A.valor22) AS 'En general, se siente cargado por el hecho de cuidar a su familiar / persona cuidada (grado de carga)', 
A.puntaje AS Puntaje, A.analisis AS Descripcion_Puntaje,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_zarit` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred22();
	$sql.=whe_date22();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_zarit` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred22();
	$tot.=whe_date22();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}


function lis_zung($txt){
	$sql="SELECT  
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.id_zung AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma, FN_CATALOGODESC(116,A.momento) AS Momento,
FN_CATALOGODESC(119, A.anuncio1) AS '1. Me Siento Triste Y Deprimido.',
FN_CATALOGODESC(119, A.anuncio2) AS '2. Por Las Mañanas Me Siento Mejor Que Por Las Tardes.',
FN_CATALOGODESC(119, A.anuncio3) AS '3. Frecuentemente Tengo Ganas De Llorar Y A Veces Lloro.',
FN_CATALOGODESC(119, A.anuncio4) AS '4. Me Cuesta Mucho Dormir O Duermo Mal Por Las Noches.',
FN_CATALOGODESC(119, A.anuncio5) AS '5. Ahora Tengo Tanto Apetito Como Antes.',
FN_CATALOGODESC(119, A.anuncio6) AS '6. Todavía Me Siento Atraído Por El Sexo Opuesto.',
FN_CATALOGODESC(119, A.anuncio7) AS '7. Creo Que Estoy Adelgazando.',
FN_CATALOGODESC(119, A.anuncio8) AS '8. Estoy Estreñido.',
FN_CATALOGODESC(119, A.anuncio9) AS '9. Tengo Palpitaciones.',
FN_CATALOGODESC(119, A.anuncio10) AS '10. Me Canso Por Cualquier Cosa.',
FN_CATALOGODESC(119, A.anuncio11) AS '11. Mi Cabeza Está Tan Despejada Como Antes.',
FN_CATALOGODESC(119, A.anuncio12) AS '12. Hago Las Cosas Con La Misma Facilidad Que Antes.',
FN_CATALOGODESC(119, A.anuncio13) AS '13. Me Siento Agitado E Intranquilo Y No Puedo Estar Quieto.',
FN_CATALOGODESC(119, A.anuncio14) AS '14. Tengo Esperanza Y Confío En El Futuro.',
FN_CATALOGODESC(119, A.anuncio15) AS '15. Me Siento Más Irritable Que Habitualmente.',
FN_CATALOGODESC(119, A.anuncio16) AS '16. Encuentro Fácil Tomar Decisiones.',
FN_CATALOGODESC(119, A.anuncio17) AS '17. Me Creo Útil Y Necesario Para La Gente.',
FN_CATALOGODESC(119, A.anuncio18) AS '18. Encuentro Agradable Vivir, Mi Vida Es Plena.',
FN_CATALOGODESC(119, A.anuncio19) AS '19. Creo Que Sería Mejor Para Los Demás Si Me Muriera.',
FN_CATALOGODESC(119, A.anuncio20) AS '20. Me Gustan Las Mismas Cosas Que Solían Agradarme.',
 
A.puntaje AS Puntaje, A.analisis AS Descripcion_Puntaje,
A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_zung` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred22();
	$sql.=whe_date22();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_zung` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred22();
	$tot.=whe_date22();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}


function lis_psifin($txt){
	$sql="SELECT 
G.idgeo Cod_Predio, F.id_fam AS Cod_Familia, A.idpsifin AS Cod_Registro, G.subred AS Subred, FN_CATALOGODESC(3,G.zona) AS Zona, G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.tipo_doc,P.idpersona,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(19,P.genero) AS GENERO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(16,P.etnia) AS ETNIA,FN_CATALOGODESC(15,P.pueblo) AS PUEBLO,

A.psi_fecha_sesion,A.cod_admisfin,A.zung_ini,A.hamilton_ini,A.whodas_ini,A.psi_validacion1,A.psi_validacion2,A.psi_validacion3,A.psi_validacion4,A.psi_validacion5,A.psi_validacion6,A.psi_validacion7,
A.psi_validacion8,A.psi_validacion9,A.psi_validacion10,A.psi_validacion11,A.psi_validacion12,A.psi_validacion13,A.psi_validacion14,A.psi_validacion15,A.psi_validacion17,A.psi_validacion18,A.psi_validacion19,
A.usu_creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, A.fecha_create, A.estado AS Estado_Registro

FROM `psi_sesion_fin` A
LEFT JOIN person P ON A.id_people = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred23();
	$sql.=whe_date23();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `psi_sesion_fin` A LEFT JOIN person P ON A.id_people = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred23();
	$tot.=whe_date23();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_servicAgend($txt){
	$sql="SELECT A.id_agen 'Cod Servicio',P.tipo_doc 'Tipo Documento',P.idpersona Documento,fecha_solici 'Fecha Solicitud',FN_CATALOGODESC(275, servicio) Servicio,U.nombre 'Solicito',f2.realizada 
FROM hog_agen A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario LEFT JOIN (SELECT idpeople, realizada FROM frecuenciauso WHERE idfrecuencia IN (SELECT MIN(idfrecuencia) FROM frecuenciauso GROUP BY idpeople)) f2 ON A.idpeople = f2.idpeople
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred();
	$sql.=whe_date();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM hog_agen A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario LEFT JOIN (SELECT idpeople, realizada FROM frecuenciauso WHERE idfrecuencia IN (SELECT MIN(idfrecuencia) FROM frecuenciauso GROUP BY idpeople)) f2 ON A.idpeople = f2.idpeople WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred();
	$tot.=whe_date();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_idenUAIC($txt){
	$sql="SELECT G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.iduaic AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
	P.tipo_doc,P.idpersona,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,
	FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(19,P.genero) AS GENERO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(16,P.etnia) AS ETNIA,FN_CATALOGODESC(15,P.pueblo) AS PUEBLO,
	A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(263,A.parentesco) AS Parentesco,A.nombre_cui AS Cuidador,A.tipo_doc AS Tipo_Documento,A.num_doc AS Documento,A.telefono ,
	FN_CATALOGODESC(170,A.era) AS 'Enfermedad Respiratoria Aguda',FN_CATALOGODESC(170,A.eda) AS 'Enfermedad Diarreica Aguda',FN_CATALOGODESC(170,A.dnt) Desnutrición,
	A.des_sinto AS 'Descripcion De Sintomas',FN_CATALOGODESC(170,A.aten_medi) AS 'Recibio Atención por Medico Ancestral',FN_CATALOGODESC(170,A.aten_part) AS 'Recibio Atención por Partera',A.peri_cef AS 'Perimetro Cefalico (Cm)',
	A.peri_bra AS 'Perimetro Braquial (Cm)',A.peso,A.talla,A.zcore,FN_CATALOGODESC(98,A.clasi_nut) AS 'Clasificación Nutricional',A.tempe 'Temperatura',A.frec_res 'Frecuencia Respiratoria (min)',
	A.frec_car 'Frecuencia Cardiaca',A.satu 'Saturación',FN_CATALOGODESC(170,A.sales_reh) AS 'Sales De Rehidratación',FN_CATALOGODESC(170,A.aceta) AS 'Acetaminofen',
	FN_CATALOGODESC(170,A.traslados_uss) AS 'Traslados de Uss',FN_CATALOGODESC(170,A.educa) 'Educación',FN_CATALOGODESC(170,A.menor_hos) AS 'Menor Hospitalizado',A.tempe2 'Temperatura 2',A.frec_res2 'Frecuencia Respiratoria 2',A.frec_car2 'Frecuencia Cardiaca 2',
	A.satu2 'Saturación 2',A.seg_entmed 'Seguimiento A Entrega De Medicamentos',A.observacion,A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
	FROM uaic_ide A
	LEFT JOIN person P ON A.idpeople=P.idpeople
	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
	LEFT JOIN hog_geo G ON F.idpre = G.idgeo
	LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM uaic_ide A LEFT JOIN person P ON A.idpeople=P.idpeople	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam	LEFT JOIN hog_geo G ON F.idpre = G.idgeo	LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario 
	 WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_SeguimientosUAIC($txt){
	$sql="SELECT G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.iduaicseg AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
	P.tipo_doc,P.idpersona,concat(P.nombre1,' ',P.nombre2) AS NOMBRES,concat(P.apellido1,' ',P.apellido2) AS APELLIDOS,P.fecha_nacimiento AS FECHA_NACIMIENTO,
	FN_CATALOGODESC(21,P.sexo) AS SEXO,FN_CATALOGODESC(19,P.genero) AS GENERO,FN_CATALOGODESC(30,P.nacionalidad) AS NACIONALIDAD,FN_CATALOGODESC(16,P.etnia) AS ETNIA,FN_CATALOGODESC(15,P.pueblo) AS PUEBLO,
	A.fecha_seg AS Fecha_Seguimiento,FN_CATALOGODESC(76,A.segui) 'Seguimiento',FN_CATALOGODESC(73,A.estado_seg) 'Estado',FN_CATALOGODESC(265,A.motivo_seg) 'Motivo',
	FN_CATALOGODESC(170,A.at_medi) AS 'Recibio Atención por Medico Ancestral',FN_CATALOGODESC(170,A.at_part) AS 'Recibio Atención por Partera',
	A.peso,A.talla,A.zcore,FN_CATALOGODESC(98,A.clasi_nutri) AS 'Clasificación Nutricional',FN_CATALOGODESC(170,A.ftlc_apme) 'Tiene Ftlc U Otro Apme',A.cual,FN_CATALOGODESC(170,A.cita_nutri7)'Cita Con Nutricion O Pediatria A Los 7 Dias'
	,FN_CATALOGODESC(170,A.cita_nutri15)'Cita Con Nutricion O Pediatria A Los 15 Dias',FN_CATALOGODESC(170,A.cita_nutri30)'Cita Con Nutricion O Pediatria A Los 30 Dias',A.observaciones, 
	A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
	FROM uaic_seg A
	LEFT JOIN person P ON A.idpeople=P.idpeople
	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
	LEFT JOIN hog_geo G ON F.idpre = G.idgeo
	LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
	WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred8();
	$sql.=whe_date8();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM uaic_seg A LEFT JOIN person P ON A.idpeople=P.idpeople	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam	LEFT JOIN hog_geo G ON F.idpre = G.idgeo	LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario 
	 WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred8();
	$tot.=whe_date8();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_ruteoGestionados($txt){
	$sql="SELECT R.id_ruteo AS Codigo_Registro, FN_CATALOGODESC(33,R.fuente) AS 'Fuente O Remitente', R.fecha_asig AS 'Fecha Asignación SDS', FN_CATALOGODESC(191,R.priorizacion) AS 'Cohorte De Riesgo', FN_CATALOGODESC(235,R.tipo_prior) AS 'Grupo De Población Priorizada', R.tipo_doc AS 'Tipo De Documento', R.documento AS 'Número De Documento', R.nombres AS 'Nombres Y Apellidos Del Usuario', FN_CATALOGODESC(21,R.sexo) AS 'Sexo',G.subred AS Subred, G.idgeo AS Cod_Predio, G.direccion AS Direccion, R.telefono1 AS Telefono_1, R.telefono2 AS Telefono_2, R.telefono3 AS Telefono_3,R.actividad1 AS 'Cod Usuario ASignado', U.nombre AS 'Nombre Colaborador',RG.id_rutges AS Cod_Registro, RG.fecha_llamada AS 'Fecha Llamada', FN_CATALOGODESC(270,RG.estado_llamada) AS 'Estado Contacto Telefonico',RG.estado,RG.observaciones AS Observaciones, FN_CATALOGODESC(271,RG.estado_agenda) AS 'Estado Gestion', FN_CATALOGODESC(272,RG.motivo_estado) AS 'Motivo Estado Gestion', RG.fecha_gestion AS 'Fecha Programacion Visita', RG.docu_confirm AS 'Documento Confirmado Usuario', RG.usuario_gest AS 'Cod Colaborador Asignado', RG.direccion_n AS 'Direccion Nueva', RG.sector_n AS 'Sector Catastral', RG.manzana_n AS 'N° Manzana', RG.predio_n AS 'N° Predio',FN_CATALOGODESC(191,preclasif) 'Preclasificacion',FN_CATALOGODESC(235,clasifica) 'Clasificacion Promotor',FN_CATALOGODESC(273,riesgo) 'Riesgo',U1.nombre 'Derivado a',U1.perfil 'Perfil Derivado',R.idgeo 'Predio',R.fecha 'Fecha Gestion Final',FN_CATALOGODESC(278,R.estado_ruteo) Estado,R.famili 'Familia',P.idpeople 'Cod Persona',CONCAT(P.idpersona,'-',CONCAT(P.nombre1,' ',P.apellido1)) 'Usuario Final'
	 FROM eac_ruteo_ges RG 
	 LEFT JOIN eac_ruteo R ON RG.idruteo = R.id_ruteo 
	 LEFT JOIN hog_geo G ON R.idgeo = G.idgeo 
	 LEFT JOIN usuarios U ON R.actividad1 = U.id_usuario 
	 LEFT JOIN eac_ruteo_clas C ON RG.id_rutges = C.idrutges
	 LEFT JOIN usuarios U1 ON C.profesional= U1.id_usuario
	 LEFT JOIN person P ON R.usuario=P.idpeople 
	 WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred14();
	$sql.=whe_date14();
	 //echo $sql;
	$tot="SELECT COUNT(*) total FROM eac_ruteo_ges RG LEFT JOIN eac_ruteo R ON RG.idruteo = R.id_ruteo 
	 LEFT JOIN hog_geo G ON R.idgeo = G.idgeo 
	 LEFT JOIN usuarios U ON R.actividad1 = U.id_usuario 
	 LEFT JOIN eac_ruteo_clas C ON RG.id_rutges = C.idrutges
	 LEFT JOIN usuarios U1 ON C.profesional= U1.id_usuario
	 LEFT JOIN person P ON R.usuario=P.idpeople WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred14();
	$tot.=whe_date14();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_predios($txt){
	$sql="SELECT idgeo 'Cod_predio', FN_CATALOGODESC(72,subred),sector_catastral,nummanzana,predio_num,unidad_habit,cordx,cordy FROM hog_geo 
	 WHERE 1";
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM hog_geo WHERE 1 ";	
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_usuarios($txt){
	$sql="SELECT p.subred AS Subred,p.idgeo AS Cod_Predio,t.territorio AS Cod_Territorio,FN_CATALOGODESC(283,t.territorio) AS Nombre_Territorio,f.idfam AS Cod_Familia,per.idpeople AS Cod_Persona,p.sector_catastral AS Sector_Catastral,p.direccion AS Dirección,p.upz,p.localidad,p.zona,IFNULL(per.telefono1,F.telefono1) AS Telefono1,IFNULL(per.telefono2,F.telefono2) AS Telefono2,F.telefono3 AS Telefono3,per.tipo_doc,per.idpersona,per.nombre1,per.nombre2,per.apellido1,per.apellido2,per.fecha_nacimiento,TIMESTAMPDIFF(YEAR, per.fecha_nacimiento, CURDATE()) AS edad_en_años,FN_CATALOGODESC(21,per.sexo) Sexo,FN_CATALOGODESC(19,per.genero) Genero,FN_CATALOGODESC(49,per.oriensexual) Orientacion, FN_CATALOGODESC(30,per.nacionalidad) Nacionalidad,FN_CATALOGODESC(16,per.etnia) Etnia,FN_CATALOGODESC(15,per.pueblo) Pueblo,FN_CATALOGODESC(178,per.pobladifer) Poblacion_Diferencial,FN_CATALOGODESC(14,per.discapacidad) Discapacidad,FN_CATALOGODESC(175,per.ocupacion) Ocupacion,FN_CATALOGODESC(17,per.regimen) Regimen,FN_CATALOGODESC(18,per.eapb) EAPB,per.usu_creo AS Usuario_Creo,MAX(us.nombre) AS Nombre_Creo,MAX(us.perfil) AS Perfil_Creo,MAX(us.equipo) AS Equipo_Creo,per.fecha_create AS Fecha_Creo,FN_CATALOGODESC(170,per.encuentra) Encuentra  
	FROM person per	LEFT JOIN hog_fam F ON per.vivipersona = F.id_fam LEFT JOIN hog_geo p ON F.idpre = p.idgeo LEFT JOIN apro_terr t ON p.territorio = t.territorio LEFT JOIN hog_carac f ON per.vivipersona = f.idfam LEFT JOIN usuarios us ON per.usu_creo = us.id_usuario WHERE 1 ";
	$sql.=whe_date30();
	$sql.=" GROUP BY per.idpersona";
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM person per	LEFT JOIN hog_fam F ON per.vivipersona = F.id_fam  LEFT JOIN hog_geo p ON F.idpre = p.idgeo	LEFT JOIN apro_terr t ON p.territorio = t.territorio LEFT JOIN hog_carac f ON per.vivipersona = f.idfam LEFT JOIN usuarios us ON per.usu_creo = us.id_usuario WHERE 1 ";
	$tot.=whe_date30();
	$tot.=" GROUP BY per.idpersona";
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}
function lis_identEmb($txt){
	$sql="SELECT G.idgeo Cod_Predio,F.id_fam AS Cod_Familia, V.idriesgo AS Cod_Registro, G.subred AS Subred,V.idpeople AS Cod_Persona, 
P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento,P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo,	V.fechavisi AS Fecha_Visita, V.lider AS Lider, V.educacion AS Educación,V.espanol AS Habla_Español, FN_CATALOGODESC(256,V.saberes) AS Saberes, FN_CATALOGODESC(257,V.enfoque) AS Enfoque, FN_CATALOGODESC(266,V.pueblo) AS Pueblo,	V.usu_creo,	U.nombre AS Nombre_Creo,V.fecha_create,	V.usu_update, V.fecha_update, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, V.estado AS Estado_Registro
FROM `etn_identi` V
LEFT JOIN person P ON V.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON V.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred24();
	$sql.=whe_date24();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM etn_identi V LEFT JOIN person P ON V.idpeople = P.idpeople	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam	LEFT JOIN hog_geo G ON F.idpre = G.idgeo	LEFT JOIN usuarios U ON V.usu_creo = U.id_usuario 
	 WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred24();
	$tot.=whe_date24();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_SeguiRutEmb($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia, ES.idsegnoreg AS Cod_Registro, G.subred AS Subred,ES.idpeople AS Cod_Persona, 
P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento,P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo,
ES.fecha_seg AS Fecha_Seguimiento, FN_CATALOGODESC(76,ES.segui) AS Número_Seguimiento, FN_CATALOGODESC(73,ES.estado_seg) AS Estado, FN_CATALOGODESC(265,ES.motivo) AS Motivo_Fallido, FN_CATALOGODESC(258,ES.prioridad) AS Prioridad, ES.gestaciones As Gestaciones, ES.partos AS Partos, ES.abortos AS Abortos, ES.cesareas AS Cesareas, ES.vivos AS Vivos, ES.muertos AS Muertos, ES.fum AS Fecha_Ultima_Regla, ES.edad_gest AS Edad_Gestacional, ES.fecha_obs AS Fecha_Eve_Obs, FN_CATALOGODESC(259,ES.resul_gest) AS Resultado_Gestación, ES.peso_nacer AS Peso_al_Nacer, FN_CATALOGODESC(170,ES.asist_controles) AS Asiste_Control, FN_CATALOGODESC(170,ES.exa_labo) AS Examen_Laboratorios, FN_CATALOGODESC(170,ES.cons_micronutri) AS Micronutrientes, FN_CATALOGODESC(170,ES.esq_vacu) AS Esquema_Vacunación, FN_CATALOGODESC(170,ES.signos_alarma1) AS Signos_Alarma, FN_CATALOGODESC(170,ES.diag_sifigest) AS Diag_Sifilis_Gestacional, FN_CATALOGODESC(170,ES.adhe_tto) AS Adherencia_Tratamiento, FN_CATALOGODESC(170,ES.diag_sificong) AS Diag_Sifilis_Congenita, FN_CATALOGODESC(170,ES.seg_partera) AS Seguimiento_Partera, FN_CATALOGODESC(170,ES.seg_med_ancestral1) AS Segumiento_Medico_Ancestral, 
FN_CATALOGODESC(252,ES.diag_cronico) AS Diagnosico_Cronico, ES.cual AS Cual, FN_CATALOGODESC(170,ES.tto_enf) AS Cuenta_Tratamiento_Enfermera, FN_CATALOGODESC(170,ES.ctrl_cronico) AS Control_Cronicos, FN_CATALOGODESC(170,ES.signos_alarma2) AS Presenta_Signos_Alarma, FN_CATALOGODESC(170,ES.seg_med_ancestral2) AS Segumiento_Medico_Ancestral, 
ES.doc_madre AS Docuemnto_Madre, FN_CATALOGODESC(170,ES.ctrl_cyd) AS Control_CyD, FN_CATALOGODESC(170,ES.lactancia_mat) AS Recibe_Lactancia_Materna, FN_CATALOGODESC(170,ES.esq_vacunacion) AS Vacunación, FN_CATALOGODESC(170,ES.sig_alarma_seg) AS Signos_Alarma,	FN_CATALOGODESC(170,ES.seg_med_ancestral3) AS Seguimiento_Medico_Ancestral,
FN_CATALOGODESC(170,ES.at_med) AS Atención_Medico_Ancestral, FN_CATALOGODESC(170,ES.at_partera) AS Atención_Partera, ES.sistolica AS Sistolica,	ES.diastolica AS Diastolica, ES.frec_cardiaca AS Frecuencia_Cardiaca, ES.frec_respiratoria AS Frecuencia_Respiratoria, ES.saturacion AS Saturación, ES.gluco AS Glucometria, ES.peri_cefalico AS Perimetro_Cefalico, ES.peri_braqueal AS Perimetro_Braqueal, ES.peso AS Peso, ES.talla, ES.imc,	ES.zcore, FN_CATALOGODESC(260,ES.clasi_nutri) AS Clasificación_Nutricional,	FN_CATALOGODESC(261,ES.ser_remigesti) AS Remision_Gestion, ES.observaciones AS Observaciones, ES.usu_creo ,U.nombre AS Nombre_Creo,
U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, ES.estado AS Estado_Registro
FROM emb_segreg ES
LEFT JOIN person P ON ES.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON ES.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred25();
	$sql.=whe_date25();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM emb_segreg ES LEFT JOIN person P ON ES.idpeople = P.idpeople	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam	LEFT JOIN hog_geo G ON F.idpre = G.idgeo	LEFT JOIN usuarios U ON ES.usu_creo = U.id_usuario 
	 WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred25();
	$tot.=whe_date25();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_SeguiHosEmb($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia, ES.idseg AS Cod_Registro, G.subred AS Subred,ES.idpeople AS Cod_Persona, 
P.tipo_doc AS Tipo_Documento, P.idpersona AS N°_Documento,P.nombre1 AS Primer_Nombre, P.nombre2 AS Segundo_Nombre, P.apellido1 AS Primer_Apellido, P.apellido2 AS Seundo_Apellido, P.fecha_nacimiento AS Fecha_Nacimiento, FN_CATALOGODESC(21,P.sexo) AS Sexo,
ES.fecha_seg AS Fecha_Seguimiento, FN_CATALOGODESC(76,ES.segui) AS Número_Seguimiento, FN_CATALOGODESC(73,ES.estado_seg) AS Estado, FN_CATALOGODESC(265,ES.motivo) AS Motivo_Fallido, FN_CATALOGODESC(262,ES.interven) AS Intervención, FN_CATALOGODESC(170,ES.gestante) AS Gestante, ES.edad_gest AS Edad_gestacional,	FN_CATALOGODESC(263,ES.paren) AS Parentesco, ES.Nom_fami AS Nombre_familiar, FN_CATALOGODESC(1,ES.tipo_doc) AS Tipo_Documento, ES.num_doc AS Número_Documento, ES.tel_conta AS Telefono_Contacto, FN_CATALOGODESC(264,ES.ubi) AS Ubicación, ES.ser_req AS Servcio_Rquereido, ES.fecha_ing AS Fecha_Ingreso,	ES.uss_ing AS USS_Ingreso, ES.motivo_cons AS Motivo_Consulta, ES.uss_tras AS USS_Traslado,	ES.ing_unidad AS Ingrseo_Unidad, ES.ante_salud AS Antecedentes_Salud, ES.imp_diag AS Impresion_Diagnostica,	
ES.uss_encu AS USS_Encuentra, ES.servicio_encu AS Servicio_Encuentra, ES.imp_diag2 AS Impresion_Diagnostica, FN_CATALOGODESC(170,ES.nece_apoy) AS Apoyo_Intersectorial, 
ES.fecha_egreso, ES.espe1 AS Especialidad_1, ES.espe2 AS Especialidad_2, FN_CATALOGODESC(170,ES.adh_tto) AS Adherente_Tratamiento, ES.observaciones AS Observaciones, ES.usu_creo, U.nombre AS Nombre_Creo,
U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, ES.estado AS Estado_Registro
FROM emb_segui ES
LEFT JOIN person P ON ES.idpeople = P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON ES.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred26();
	$sql.=whe_date26();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM emb_segui ES LEFT JOIN person P ON ES.idpeople = P.idpeople	LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam	LEFT JOIN hog_geo G ON F.idpre = G.idgeo	LEFT JOIN usuarios U ON ES.usu_creo = U.id_usuario 
	 WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred26();
	$tot.=whe_date26();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_frecuencia($txt){
	$sql="SELECT G.subred,G.idgeo 'Cod Predio',FN_CATALOGODESC(283,G.territorio) 'Territorio',H.id_fam 'Cod Familia',F.idfrecuencia 'Cod Registro',F.idpeople 'Cod Persona',P.idpersona 'Documento',FN_CATALOGODESC(1,P.tipo_doc) 'Tipo Documento',FN_CATALOGODESC(274,`punto_atencion`) 'Punto de Control',FN_CATALOGODESC(275,tipo_cita) 'Tipo Cita',F.fecha_create 'Creada',F.usu_creo 'id Creo',U.nombre 'nombre Creo',F.fecha_update 'Editado',F.usu_update 'Edito',U1.nombre 'Nombre Edito',F.realizada,F.estado
		from frecuenciauso F 
		LEFT JOIN person P ON F.idpeople = P.idpeople
		LEFT JOIN usuarios U ON F.usu_creo = U.id_usuario
		LEFT JOIN usuarios U1 ON F.usu_update = U1.id_usuario
		LEFT JOIN hog_fam H ON P.vivipersona = H.id_fam
		LEFT JOIN hog_geo G ON H.idpre = G.idgeo
		WHERE 1 ";
		if (perfilUsu()!=='ADM')	$sql.=whe_subred();
		$sql.=whe_date13();
		// echo $sql;
		$tot="SELECT COUNT(*) total from frecuenciauso F LEFT JOIN person P ON F.idpeople = P.idpeople LEFT JOIN usuarios U ON F.usu_creo = U.id_usuario LEFT JOIN usuarios U1 ON F.usu_update = U1.id_usuario LEFT JOIN hog_fam H ON P.vivipersona = H.id_fam LEFT JOIN hog_geo G ON H.idpre = G.idgeo WHERE 1 ";	
		if (perfilUsu()!=='ADM')	$tot.=whe_subred();
		$tot.=whe_date13();
		$_SESSION['sql_'.$txt]=$sql;
		$_SESSION['tot_'.$txt]=$tot;
		$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_agendamiento($txt){
	$sql="SELECT G.subred,G.idgeo 'Cod Predio',FN_CATALOGODESC(283,G.territorio) 'Territorio', H.id_fam 'Cod Familia',A.idpeople 'Cod Persona',P.idpersona 'Documento',FN_CATALOGODESC(1,P.tipo_doc) 'Tipo Documento',
	P.fecha_nacimiento 'Fecha de Nacimiento',P.sexo, A.idagendamiento AS 'Cos Registro', FN_CATALOGODESC(274,A.punto_atencion ) AS 'Punto de Atención',FN_CATALOGODESC(275,A.tipo_cita) 'Tipo de Cita',
	A.fecha_create 'Fecha de Asignación',A.fecha_cita 'Fecha de la Cita',A.hora_cita 'Hora de la Cita',A.nombre_atendio 'Nombre quien Atendió Llamada',A.usu_creo 'Digitador',A.observac_cita 'Observación Cita',IFNULL(A.fecha_llamada,'00-00-0000') 'Fecha Recordación',
	ifnull(A.nombre_llamada,'-') 'Nombre quien Recibió Llamada' ,ifnull(A.confirma_cita,'-') 'Confirmo Cita',ifnull(A.msjtxt,'-') 'Desea Envio de Msj',
	ifnull(A.usu_update,'-') 'Digitador1',ifnull(A.observac_llamadas,'-') 'Observaciones de Recordación',ifnull(A.fecha_llamada2,'-') 'Fecha Llamada por Efectividad',ifnull(A.nombre_llamada2,'-') 'Nombre quien Contesto Llamada',ifnull(FN_CATALOGODESC(41,A.motivo_inasistencia),'-') 'Motivo de la Inasistencia',ifnull(A.reasigno,'-') 'Se reasigno la Cita',ifnull(A.usu_update,'-') 'Digitador2',ifnull(A.observac_llamada2,'-') 'Observaciones de Inasistencia',FN_CATALOGODESC(276,A.estado) 'Estado'
FROM agendamiento A 
	LEFT JOIN person P ON A.idpeople = P.idpeople
	LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
	LEFT JOIN usuarios U1 ON A.usu_update = U1.id_usuario
	LEFT JOIN hog_fam H ON P.vivipersona = H.id_fam
	LEFT JOIN hog_geo G ON H.idpre = G.idgeo 
	 WHERE 1 ";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred();
	$sql.=whe_date();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM agendamiento A LEFT JOIN person P ON A.idpeople = P.idpeople LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario LEFT JOIN usuarios U1 ON A.usu_update = U1.id_usuario LEFT JOIN hog_fam H ON P.vivipersona = H.id_fam LEFT JOIN hog_geo G ON H.idpre = G.idgeo WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred();
	$tot.=whe_date();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_tamrqc($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.tam_srq AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma,
FN_CATALOGODESC(170,A.sintoma1) AS '1. ¿El Lenguaje Del Niño(A) Es Anormal En Alguna Forma?',
FN_CATALOGODESC(170,A.sintoma2) AS '2. ¿El Niño(A) Duere Mal?',
FN_CATALOGODESC(170,A.sintoma3) AS '3. ¿Ha Tenido El Niño(A) En Algunas Ocasiones Convulsiones O Caídas Al Suelo Sin Razón?',
FN_CATALOGODESC(170,A.sintoma4) AS '4. ¿Sufre El Niño(A) De Dolores Frecuentes De Cabeza?',
FN_CATALOGODESC(170,A.sintoma5) AS '5. ¿El Niño(A) Ha Huido De La Casa Frecuentemente?',
FN_CATALOGODESC(170,A.sintoma6) AS '6. ¿Ha Robado Cosas De La Casa?',
FN_CATALOGODESC(170,A.sintoma7) AS '7. ¿Se Asusta O Se Pone Nervioso(A) Sin Razón?',
FN_CATALOGODESC(170,A.sintoma8) AS '8. ¿Parece Como Retardado(A) O Lento(A) Para Aprender?',
FN_CATALOGODESC(170,A.sintoma9) AS '9. ¿El (La) Niño(A) Casi Nunca Juega Con Otros Niños(As)?',
FN_CATALOGODESC(170,A.sintoma10) AS '10. ¿El Niño(A) Se Orina O Defeca En La Ropa?',
A.totalsi AS 'Total Respuesta Afirmativas',
A.totalno AS 'Total Respuesta Negativas',
A.descripcion AS 'Descripcion Puntaje',

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_rqc` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred28();
	$sql.=whe_date28();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_rqc` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred28();
	$tot.=whe_date28();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function lis_tamsrq($txt){
	$sql="SELECT 
G.idgeo Cod_Predio,F.id_fam AS Cod_Familia,A.tam_srq AS Cod_Registro,G.subred AS Subred,FN_CATALOGODESC(3,G.zona) AS Zona,G.localidad AS Localidad, G.territorio AS 'Cod Territorio', FN_CATALOGODESC(283,G.territorio) AS 'Nombre Territorio',
P.idpeople AS Cod_Usuario,P.tipo_doc AS Tipo_Documento,P.idpersona AS N°_Documento,CONCAT(P.nombre1, ' ', P.nombre2) AS Nombres_Usuario,CONCAT(P.apellido1, ' ', P.apellido2) AS Apellidos_Usuario,P.fecha_nacimiento AS Fecha_Nacimiento,  FN_CATALOGODESC(21,P.sexo) AS Sexo,
A.fecha_toma AS Fecha_Toma,
FN_CATALOGODESC(170,A.pregunta1) AS '1.¿Tiene Frecuentes Dolores De Cabeza?',
FN_CATALOGODESC(170,A.pregunta2) AS '2.¿Tiene Mal Apetito?',
FN_CATALOGODESC(170,A.pregunta3) AS '3.¿Duerme Mal?',
FN_CATALOGODESC(170,A.pregunta4) AS '4.¿Se Asusta Con Facilidad?',
FN_CATALOGODESC(170,A.pregunta5) AS '5.¿Sufre De Temblor En Las Manos?',
FN_CATALOGODESC(170,A.pregunta6) AS '6.¿Se Siente Nervioso, Tenso O Aburrido?',
FN_CATALOGODESC(170,A.pregunta7) AS '7.¿Sufre De Mala Digestión?',
FN_CATALOGODESC(170,A.pregunta8) AS '8.¿No Puede Pensar Con Claridad?',
FN_CATALOGODESC(170,A.pregunta9) AS '9.¿Se Siente Triste?',
FN_CATALOGODESC(170,A.pregunta10) AS '10. ¿Llora Usted Con Mucha Frecuencia?',
FN_CATALOGODESC(170,A.pregunta11) AS '11. ¿Tiene Dificultad De Disfrutar Sus Actividades Diarias?',
FN_CATALOGODESC(170,A.pregunta12) AS '12. ¿Tiene Dificultad Para Tomar Decisiones?',
FN_CATALOGODESC(170,A.pregunta13) AS '13. ¿Tiene Dificultad En Hacer Su Trabajo? (¿Sufre Usted Con Su Trabajo?)',
FN_CATALOGODESC(170,A.pregunta14) AS '14. ¿Es Incapaz De Desempeñar Un Papel Útil En Su Vida?',
FN_CATALOGODESC(170,A.pregunta15) AS '15. ¿Ha Perdido Interés En Las Cosas?',
FN_CATALOGODESC(170,A.pregunta16) AS '16. ¿Siente Que Usted Es Una Persona Inútil?',
FN_CATALOGODESC(170,A.pregunta17) AS '17. ¿Ha Tenido La Idea De Acabar Con Su Vida?',
FN_CATALOGODESC(170,A.pregunta18) AS '18. ¿Si Siente Cansado Todo El Tiempo?',
FN_CATALOGODESC(170,A.pregunta19) AS '19. ¿Tiene Sensaciones Desagradables En Su Estómago?',
FN_CATALOGODESC(170,A.pregunta20) AS '20. ¿Se Cansa Con Facilidad?',
FN_CATALOGODESC(170,A.pregunta21) AS '21. ¿Siente Usted Que Alguien Ha Tratado De Herirlo En Alguna Forma?',
FN_CATALOGODESC(170,A.pregunta22) AS '22. ¿Es Usted Una Persona Mucho Más Importante De Lo Que Piensan Los Demás?',
FN_CATALOGODESC(170,A.pregunta23) AS '23. ¿Ha Notado Interferencias O Algo Raro En Su Pensamiento?',
FN_CATALOGODESC(170,A.pregunta24) AS '24. ¿Oye Voces Sin Saber De Dónde Vienen O Que Otras Personas No Puede Oir?',
FN_CATALOGODESC(170,A.pregunta25) AS '25. ¿Ha Tenido Convulsiones, Ataques O Caídas Al Suelo, Con Movimientos De Brazos Y Piernas; Con Mordedura De La Lengua O Pérdida Del Conocimiento?',
FN_CATALOGODESC(170,A.pregunta26) AS '26. ¿Alguna Vez Le Ha Parecido A Su Familia, Sus Amigos, Su Médico O A Su Sacerdote Que Usted Estaba Bebiendo Demasiado Licor?',
FN_CATALOGODESC(170,A.pregunta27) AS '27. ¿Alguna Vez Ha Querido Dejar De Beber, Pero No Ha Podido?',
FN_CATALOGODESC(170,A.pregunta28) AS '28. ¿Ha Tenido Alguna Vez Dificultades En El Trabajo (O Estudio) A Causa De La Bebida, Como Beber En El Trabajo O En El Colegio, O Faltar A Ellos?',
FN_CATALOGODESC(170,A.pregunta29) AS '29. ¿Ha Estado En Riñas O La Han Detenido Estando Borracho?',
FN_CATALOGODESC(170,A.pregunta30) AS '30. ¿Le Ha Parecido Alguna Vez Que Usted Bebía Demasiado?',
A.ansiedad AS 'Resultado Ansiedad',
A.suicida AS 'Resultado Suicida',
A.psicosis AS 'Resultado Psicosis',
A.epilepsia AS 'Resultado Epilepsia',
A.alcoholismo AS 'Resultado Alcoholismo',

A.usu_creo AS Usuario_Creo, U.nombre AS Nombre_Creo, U.perfil AS Perfil_Creo, U.equipo AS Equipo_Creo, A.fecha_create AS Fecha_Creacion, A.estado AS Estado_Registro
FROM `hog_tam_srq` A
LEFT JOIN person P ON A.idpeople=P.idpeople
LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam
LEFT JOIN hog_geo G ON F.idpre = G.idgeo
LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario
WHERE 1";
	if (perfilUsu()!=='ADM')	$sql.=whe_subred29();
	$sql.=whe_date29();
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM `hog_tam_srq` A LEFT JOIN person P ON A.idpeople=P.idpeople LEFT JOIN hog_fam F ON P.vivipersona = F.id_fam LEFT JOIN hog_geo G ON F.idpre = G.idgeo LEFT JOIN usuarios U ON A.usu_creo = U.id_usuario WHERE 1 ";	
	if (perfilUsu()!=='ADM')	$tot.=whe_subred29();
	$tot.=whe_date29();
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}


function lis_gestPredios($txt){
	$sql="SELECT G.idgeo,U.nombre,FN_CATALOGODESC(44,ga.estado_v) ESTADO  FROM geo_gest ga 
		LEFT JOIN hog_geo G ON ga.idgeo = G.idgeo
		LEFT JOIN usuarios U ON ga.usu_creo = U.id_usuario ORDER BY 1";
	// echo $sql;
	$tot="SELECT COUNT(*) total FROM hog_geo WHERE 1 ";	
	$_SESSION['sql_'.$txt]=$sql;
	$_SESSION['tot_'.$txt]=$tot;
	$rta = array('type' => 'OK','file'=>$txt);
	echo json_encode($rta);
}

function whe_subred() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_create) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred1() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date1(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(V.fecha) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
} 

function whe_subred2() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date2(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(C.fecha) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred3() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date3(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(C.fecha) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred4() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date4(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(S.fecha_toma) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred5() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date5(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred6() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date6(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred7() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date7(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_even) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}


function whe_subred8() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date8(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_seg) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred9() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date9(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(P.fecha_create) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred10() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date10(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_toma) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred11() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date11(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_create) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}


function whe_subred12() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date12(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_atencion) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}


function whe_subred13() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date13(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(F.fecha_create) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}


function whe_subred14() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date14(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND RG.fecha_llamada>='{$_POST['fechad']}' AND RG.fecha_llamada<='{$_POST['fechah']}'";
	return $sql;
}

function whe_subred15() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date15(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(R.fecha_acep) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred16() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date16(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(C.fecha_toma) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred17() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date17(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(R.rel_validacion2) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred18() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date18(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(C.fecha) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred19() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date19(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_ses1) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}


function whe_subred20() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date20(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.psi_fecha_sesion) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred21() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date21(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.psi_fecha_sesion) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred22() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date22(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_toma) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred23() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date23(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.psi_fecha_sesion) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}
function whe_subred24() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}
function whe_date24(){
	$sql= " AND date(V.fechavisi) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred25() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}
function whe_date25(){
	$sql= " AND date(ES.fecha_seg) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred26() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}
function whe_date26(){
	$sql= " AND date(ES.fecha_seg) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred27() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date27(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(S.fecha_seg) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred28() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date28(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_toma) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_subred29() {
	$sql= " AND (G.subred) in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."')";
	return $sql;
}

function whe_date29(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(A.fecha_toma) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function whe_date30(){
	$dia=date('d');
	$mes=date('m');
	$ano=date('Y');
	$sql= " AND date(per.fecha_create) BETWEEN '{$_POST['fechad']}' AND '{$_POST['fechah']}'";
	return $sql;
}

function encript($texto, $clave) {
    $txtcript = openssl_encrypt($texto, 'aes-256-ecb', $clave, 0);
    return base64_encode($txtcript);
}

function decript($txtcript, $clave) {
    $txtcript = base64_decode($txtcript);
    $texto = openssl_decrypt($txtcript, 'aes-256-ecb', $clave, 0);
    return $texto;
}

function lis_homes(){
/* $sql1="SELECT * FROM CARACTERIZACION C"; 
$sql1.=whe_data();
	$sql1.=" ORDER BY 1 ASC;";
	$_SESSION['sql_caracterizacion']=$sql1;
	$rta = array(
		'type' => 'OK','msj'=>$sql1
	);
	echo json_encode($rta); */
}

function opc_bina($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=217 and estado='A' and valor=(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}') ORDER BY 1",$id);
}
function opc_territorio($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=202 and estado='A' and valor=(SELECT subred FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}')  ORDER BY 1",$id);
}
function opc_subred($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=72 and estado='A' and idcatadeta in(1,2,4,3) ORDER BY 1",$id);
}
function opc_perfil($id=''){
	$com=datos_mysql("SELECT CASE WHEN componente = 'EAC' THEN 2 WHEN componente = 'HOG' THEN 1 END as componente FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}'");
	$comp = $com['responseResult'][0]['componente'] ;
	// return $comp;
	return opc_sql("SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo = 218 AND estado = 'A' AND valor='1'",$id);
}
function opc_gestion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=216 and estado='A' ORDER BY 1",$id);
}

function opc_proceso($id=''){
	$com=datos_mysql("SELECT  perfil  FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}'");
	$perfil = $com['responseResult'][0]['perfil'] ;
if ($perfil=='ADM'){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=206 ORDER BY LPAD(idcatadeta,2,'0')",$id);
}else{
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=206 and estado='A' ORDER BY LPAD(idcatadeta,2,'0')",$id);
}
}

function opc_tarea($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}

function opc_rol($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}

function opc_usuarios($id=''){
	return opc_sql("SELECT id_usuario,concat_ws(' - ',id_usuario,nombre,perfil) FROM usuarios WHERE  estado='A' AND componente=(SELECT componente FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') ORDER BY 1",$id);
}

function opc_perfilusuarios($id=''){
	
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT id_usuario,concat_ws(' - ',id_usuario,nombre,perfil) FROM usuarios WHERE estado='A' AND componente=(SELECT componente FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') AND perfil=(SELECT descripcion FROM `catadeta` WHERE idcatalogo=218 AND idcatadeta=$id[0]) ORDER BY 1";
		$info=datos_mysql($sql);		
		// var_dump($sql);
		return json_encode($info['responseResult']);
	} 
}


function focus_administracion(){
 return 'administracion';
}


function men_administracion(){
 $rta=cap_menus('administracion','pro');
 return $rta;
}

function focus_gestionusu(){
	return 'homes1';
}
function men_gestionusu(){
	$rta=cap_menus('homes','pro');
	return $rta;
}
function focus_planos(){
	return 'homes1';
}
function men_planos(){
	$rta=cap_menus('homes','pro');
	return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='administracion'  && isset($acc['crear']) && $acc['crear']=='SI'){  
    $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
	// $rta .= "<li class='icono $a crear'  title='Actualizar'   id='".print_r($_POST)."'   Onclick=\"mostrar('administracion','pro',event,'','lib.php',7);\"></li>";
  }
  return $rta;
}

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($rta);
	if ($a=='administracion' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono admsi1' title='Información de la Facturación' id='".$c['ACCIONES']."' Onclick=\"mostrar('administracion','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
	}
	if ($a=='adm-lis' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
		$rta.="<li class='icono editar ' title='Editar ' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'administracion',event,this,'lib.php');\"></li>";  //act_lista(f,this);
		// $rta.="<li class='icono editar' title='Editar Información de Facturación' id='".$c['ACCIONES']."' Onclick=\"getData('administracion','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
	}
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>
