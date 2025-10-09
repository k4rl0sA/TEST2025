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
function focus_seguiremoto(){
 return 'seguiremoto';
}

function men_seguiremoto(){
 $rta=cap_menus('seguiremoto','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  if ($a=='seguiremoto'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  }
  return $rta;
}

function lis_seguiremoto(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['idruteoclas']) ? divide($_POST['idruteoclas']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM ruteo_remoto A 
	left join eac_ruteo_clas  C ON A.idruteoclas=C.id_rutclas 
  left join eac_ruteo_ges G ON C.idrutges=G.id_rutges 
	LEFT JOIN eac_ruteo R ON G.idruteo=R.id_ruteo WHERE A.estado = 'A' AND A.idruteoclas='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-seguiremoto']))? ($_POST['pag-seguiremoto']-1)* $regxPag:0;

	$sql="SELECT `id_ruteoremoto` ACCIONES,id_ruteoremoto  'Cod Registro',R.documento ,R.nombres,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(73,estado_s) estado,continua_seguimiento Cierra
  FROM ruteo_remoto A
	left join eac_ruteo_clas  C ON A.idruteoclas=C.id_rutclas 
  left join eac_ruteo_ges G ON C.idrutges=G.id_rutges 
	LEFT JOIN eac_ruteo R ON G.idruteo=R.id_ruteo ";
	$sql.=" WHERE A.estado = 'A' AND A.idruteoclas='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"adoleMas",$regxPag,'../ruteo1/seguiRemoto.php');
}

function cmp_seguiremoto(){
	 $rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div><div class='contenido' id='seguiremoto-lis'>".lis_seguiremoto()."</div></div>";
	$w='seguiremoto';
  $d='';
	$o='inf';
  // $nb='disa oculto';
  $ob='Ob';
  $no='nO';
  $bl='bL';
  $u=false;
  $block=['hab','acc'];
  $days=fechas_app('vsp');
  $ge='pRe';
  $pu='PuE';
  $gp='GyP';
  $t=['priorizacion'=>'','tipo_prior'=>''];
 $e=get_ruteo();
 if ($e=="") {$e=$t;}
  $days=fechas_app('vsp');
  $c[]=new cmp('id','h','11',$_POST['id'],$w.'idruteoclas','ID Ruteo Clasificado','idruteoclas',null,null,false,$u,'','col-2');
  $c[]=new cmp('priorizacion','s','3',$e['priorizacion'],$w.' '.$o,'COHORTE DE RIESGO','priorizacion',null,null,false,false,'','col-3');
  $c[]=new cmp('tipo_prior','s','3',$e['tipo_prior'],$w.' '.$o,'GRUPO DE POBLACION PRIORIZADA','tipo_prior',null,null,false,false,'','col-4');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-15',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-15',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-25',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");//
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$u,'','col-2');
  
 
  $o='hab';
  $c[]=new cmp($o,'e',null,'CONDICIONES',$w);
  $c[] = new cmp('gestante','s','3',$d,$w.' '.$o,'Gestante','rta',null,null,false,$u,'','col-2',"enbRutRmtGes();");
  $c[] = new cmp('menor5','s','3',$d,$w.' '.$o,'Menor de 5 años','rta',null,null,false,$u,'','col-2',"enbRutRmtMen();");
  $c[] = new cmp('cronico','s','3',$d,$w.' '.$o,'Usuario crónico','rta',null,null,false,$u,'','col-2',"enbRutRmtCro();");
  $c[] = new cmp('general','s','3',$d,$w.' '.$o,'Usuario general','rta',null,null,false,$u,'','col-2',"enbRutRmtGrl();");

  $c[]=new cmp($o,'l',null,'GESTANTE',$w);
  $c[] = new cmp('nov_pri_fam1','s','50',$d,$w.' GeS '.$o,'Novedad Prioridad Gestante','novedad1',null,null,false,$u,'','col-2');
  $c[] = new cmp('gestante_cpn','s','3',$d,$w.' GeS '.$o,'Gestante asiste a CPN','rta',null,null,false,$u,'','col-2');
  
  $c[]=new cmp($o,'l',null,'MENOR DE 5 AÑOS',$w);
  $c[] = new cmp('nov_pri_fam2','s','50',$d,$w.' m5A '.$o,'Novedad Prioridad Menor de 5 años','novedad2',null,null,false,$u,'','col-2');
  $c[] = new cmp('menor5_rpms','s','3',$d,$w.' m5A '.$o,'Menor de 5 años asiste a controles de la RPMS','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('menor5_riesgo','s','3',$d,$w.' m5A '.$o,'Menor de 5 años con alteración nutricional asiste a controles de la Ruta de riesgo','rta',null,null,false,$u,'','col-2');
  
  $c[]=new cmp($o,'l',null,'CRONICOS',$w);
  $c[] = new cmp('nov_pri_fam3','s','50',$d,$w.' CRo '.$o,'Novedad Prioridad Cronico','novedad3',null,null,false,$u,'','col-2');
  $c[] = new cmp('inasistente_12m','s','3',$d,$w.' CRo '.$o,'Usuario inasistente a controles mayor a 12 meses','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('inasistente_6_12m','s','3',$d,$w.' CRo '.$o,'Usuario inasistente a controles entre 6 y 12  meses','rta',null,null,false,$u,'','col-2');

  $c[]=new cmp($o,'l',null,'POBLACION GENERAL',$w);
  $c[] = new cmp('persona_mayor','s','3',$d,$w.' GRl '.$o,'Persona Mayor','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('persona_discapacidad','s','3',$d,$w.' GRl '.$o,'Persona con discapacidad','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('salud_mental','s','3',$d,$w.' GRl '.$o,'Necesidad en Salud Mental','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('nuevo_diagnostico','s','3',$d,$w.' GRl '.$o,'Nuevo Diagnóstico Relevante en Salud','rta',null,null,false,$u,'','col-2');
   $c[] = new cmp('ningun_diagnostico','s','3',$d,$w.' GRl '.$o,'Nuevo Diagnóstico','rta',null,null,false,$u,'','col-2');
  
  $c[]=new cmp($o,'e',null,'INFORMACIÓN ADICIONAL',$w);
  $c[] = new cmp('vacunacion_incompleta','s','3',$d,$w.' '.$o,'Algun integrante de la familia se encuentren con esquema de vacunacion  incompleto','rta',null,null,false,$u,'','col-35');
  $c[] = new cmp('acepta_vacunacion','s','3',$d,$w.' '.$o,'Acepta abordaje presencial para vacunacion','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('barrera_salud','s','3',$d,$w.' '.$o,'Algun integrante de la familia presenta barrera para acceso a servicios de salud','rta',null,null,false,$u,'','col-35',"enabRutRmtBar();");
  $c[] = new cmp('agendamiento','s','3',$d,$w.' BaS '.$o,'Se realiza agendamiento','rta',null,null,false,$u,'','col-1');
  $c[] = new cmp('activacion_ruta','s','3',$d,$w.' BaS '.$o,'Se realiza activación de ruta a EAPB a traves del SIRC','rta',null,null,false,$u,'','col-25');
  $c[] = new cmp('sin_afiliacion','s','3',$d,$w.' '.$o,'Algun integrante de la familia se encuentra sin afiliacion al SGSS y cumple con criterios para afiliacion por oficio','rta',null,null,false,$u,'','col-45',"enabRutRmtAfi();");
  $c[] = new cmp('acepta_afiliacion','s','3',$d,$w.' SaF '.$o,'Acepta que lo contacten para realizar el tramite de afiliación por oficio','rta',null,null,false,$u,'','col-3');
  $c[] = new cmp('sujeto_abordaje','s','3',$d,$w.' '.$o,'Familia sujeto de abordaje presencial por gestor de Bienestar','rta',null,null,false,$u,'','col-25',"enbRutRmtAboSi();");
  $c[] = new cmp('acepta_abordaje','s','3',$d,$w.' FaP '.$o,'La familia acepta el abordaje presencial por gestor de Bienestar','rta',null,null,false,$u,'','col-25');
  $c[] = new cmp('deriva_perfil1','s','50',$d,$w.' FaP '.$o,'Perfil al que se deriva','perfil',null,null,false,$u,'','col-2');
  $c[] = new cmp('asignado_a1','s','50',$d,$w.' FaP '.$o,'Asignado A:','nombre',null,null,false,$u,'','col-3');
  $c[] = new cmp('sujeto_concertacion','s','3',$d,$w.' '.$o,'Familia sujeto de concertación de plan de bienestar','rta',null,null,false,$u,'','col-25',"enbRutRmtPln();");
  $c[] = new cmp('acepta_plan','s','3',$d,$w.' ApB '.$o,'La familia acepta el Plan de Bienestar','rta',null,null,false,$u,'','col-25');
  $c[] = new cmp('deriva_perfil2','s','50',$d,$w.' ApB '.$o,'Perfil al que se deriva','perfil',null,null,false,$u,'','col-2');
  $c[] = new cmp('asignado_a2','s','50',$d,$w.' ApB '.$o,'Asignado A:','nombre',null,null,false,$u,'','col-3');

  $o='acc';
  $c[]=new cmp($o,'e',null,'ACCIONES',$w);
  $c[] = new cmp('accion1','s','50',$d,$w.' '.$o,'Acción 1','accion1',null,null,false,$u,'','col-5','selectDepend(\'accion1\',\'desc_accion1\',\'../ruteo1/seguiRemoto.php\');');
  $c[] = new cmp('desc_accion1','s','50',$d,$w.' '.$o,'Descripción Acción 1','desc_accion1',null,null,false,false,'','col-5');
  $c[] = new cmp('accion2','s','50',$d,$w.' '.$no.' '.$o,'Acción 2','accion2',null,null,false,false,'','col-5','selectDepend(\'accion2\',\'desc_accion2\',\'../ruteo1/seguiRemoto.php\');');
  $c[] = new cmp('desc_accion2','s','50',$d,$w.' '.$no.' '.$o,'Descripción Acción 2','desc_accion2',null,null,false,false,'','col-5');
  $c[] = new cmp('accion3','s','50',$d,$w.' '.$no.' '.$o,'Acción 3','accion3',null,null,false,false,'','col-5','selectDepend(\'accion3\',\'desc_accion3\',\'../ruteo1/seguiRemoto.php\');');
  $c[] = new cmp('desc_accion3','s','50',$d,$w.' '.$no.' '.$o,'Descripción Acción 3','desc_accion3',null,null,false,false,'','col-5');

  $c[]=new cmp($o,'e',null,'INFORMACIÓN SEGUIMIENTO',$w);
  $c[] = new cmp('observaciones','a','7000',$d,$w.' '.$o,'Observaciones','observaciones',null,null,true,$u,'','col-10');
  $c[] = new cmp('continua_seguimiento','s','3',$d,$w.' '.$o,'¿Continua con seguimiento Remoto?','rta',null,null,true,$u,'','col-2',"enabRutRmtConSi();enabRutRmtConNo();");
  $c[] = new cmp('fecha_prox_seguimiento','d','10',$d,$w.' SsC '.$o,'Fecha estimada para el siguiente seguimiento remoto','fecha_prox_seguimiento',null,null,false,$u,'','col-2');
  $c[] = new cmp('motivo_no_continuidad','s','100',$d,$w.' SnC '.$o,'Motivo de la no continuidad de seguimientos remotos','motivo_no_remoto',null,null,false,$u,'','col-2');
     
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_ruteo(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT `priorizacion`,tipo_prior
 		FROM `eac_ruteo` R 
 		WHERE id_ruteo='{$id[0]}'";
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
	return $info['responseResult'][0];
	} 
}

function get_seguiremoto(){
	if($_POST['id']=='0'){
		return "";
	}else{
	 	$id=divide($_POST['id']);
		// var_dump($id);
		$sql="SELECT id_rutclas,idrutges,preclasif,clasifica,riesgo,accion,fecha,accion1,desc_accion1,accion2,desc_accion2,accion3,desc_accion3,profesional,solic_agend,ruta,sectorial,intsectorial,entornos,aseguram
		 FROM `eac_ruteo_clas` WHERE  idrutges='{$id[0]}'";
		$info=datos_mysql($sql);
    	// var_dump($info['responseResult'][0]);
      if(!empty($info['responseResult'])){
        return $info['responseResult'][0];
      }else {
        return '';
      } 
	} 
}

function gra_seguiremoto(){
  var_dump($_POST);
$id = divide($_POST['id']);

if($_POST['gestante']=='2' && $_POST['menor5']=='2' && $_POST['cronico']=='2' && $_POST['general']=='2'){
  return "msj['Error: Debe seleccionar al menos una condicion.']";
}
     $sql = "INSERT INTO ruteo_remoto (
        idruteoclas, fecha_seg, numsegui, estado_s, motivo_estado, gestante, menor5, cronico, general,
        nov_pri_fam1, gestante_cpn, nov_pri_fam2, menor5_rpms, menor5_riesgo, nov_pri_fam3,
        inasistente_12m, inasistente_6_12m, persona_mayor, persona_discapacidad, salud_mental,
        nuevo_diagnostico, ningun_diagnostico, vacunacion_incompleta, acepta_vacunacion, barrera_salud,
        agendamiento, activacion_ruta, sin_afiliacion, acepta_afiliacion, sujeto_abordaje, acepta_abordaje,
        deriva_perfil1, asignado_a1, sujeto_concertacion, acepta_plan, deriva_perfil2, asignado_a2,
        accion1, desc_accion1, accion2, desc_accion2, accion3, desc_accion3, observaciones,
        continua_seguimiento, fecha_prox_seguimiento, motivo_no_continuidad, usu_creo, fecha_create, estado
    ) VALUES (
        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_ADD(NOW(), INTERVAL 5 HOUR),?
    )";
     $params = [
        ['type' => 'i', 'value' => $id[0]], // idruteoclas
        ['type' => 's', 'value' => post_or_null('fecha_seg')],
        ['type' => 'i', 'value' => post_or_null('numsegui')],
        ['type' => 's', 'value' => post_or_null('estado_s')],
        ['type' => 's', 'value' => post_or_null('motivo_estado')],
        ['type' => 's', 'value' => post_or_null('gestante')],
        ['type' => 's', 'value' => post_or_null('menor5')],
        ['type' => 's', 'value' => post_or_null('cronico')],
        ['type' => 's', 'value' => post_or_null('general')],
        ['type' => 's', 'value' => post_or_null('nov_pri_fam1')],
        ['type' => 's', 'value' => post_or_null('gestante_cpn')],
        ['type' => 's', 'value' => post_or_null('nov_pri_fam2')],
        ['type' => 's', 'value' => post_or_null('menor5_rpms')],
        ['type' => 's', 'value' => post_or_null('menor5_riesgo')],
        ['type' => 's', 'value' => post_or_null('nov_pri_fam3')],
        ['type' => 's', 'value' => post_or_null('inasistente_12m')],
        ['type' => 's', 'value' => post_or_null('inasistente_6_12m')],
        ['type' => 's', 'value' => post_or_null('persona_mayor')],
        ['type' => 's', 'value' => post_or_null('persona_discapacidad')],
        ['type' => 's', 'value' => post_or_null('salud_mental')],
        ['type' => 's', 'value' => post_or_null('nuevo_diagnostico')],
        ['type' => 's', 'value' => post_or_null('ningun_diagnostico')],
        ['type' => 's', 'value' => post_or_null('vacunacion_incompleta')],
        ['type' => 's', 'value' => post_or_null('acepta_vacunacion')],
        ['type' => 's', 'value' => post_or_null('barrera_salud')],
        ['type' => 's', 'value' => post_or_null('agendamiento')],
        ['type' => 's', 'value' => post_or_null('activacion_ruta')],
        ['type' => 's', 'value' => post_or_null('sin_afiliacion')],
        ['type' => 's', 'value' => post_or_null('acepta_afiliacion')],
        ['type' => 's', 'value' => post_or_null('sujeto_abordaje')],
        ['type' => 's', 'value' => post_or_null('acepta_abordaje')],
        ['type' => 's', 'value' => post_or_null('deriva_perfil1')],
        ['type' => 's', 'value' => post_or_null('asignado_a1')],
        ['type' => 's', 'value' => post_or_null('sujeto_concertacion')],
        ['type' => 's', 'value' => post_or_null('acepta_plan')],
        ['type' => 's', 'value' => post_or_null('deriva_perfil2')],
        ['type' => 's', 'value' => post_or_null('asignado_a2')],
        ['type' => 's', 'value' => post_or_null('accion1')],
        ['type' => 's', 'value' => post_or_null('desc_accion1')],
        ['type' => 's', 'value' => post_or_null('accion2')],
        ['type' => 's', 'value' => post_or_null('desc_accion2')],
        ['type' => 's', 'value' => post_or_null('accion3')],
        ['type' => 's', 'value' => post_or_null('desc_accion3')],
        ['type' => 's', 'value' => post_or_null('observaciones')],
        ['type' => 's', 'value' => post_or_null('continua_seguimiento')],
        ['type' => 's', 'value' => post_or_null('fecha_prox_seguimiento')],
        ['type' => 's', 'value' => post_or_null('motivo_no_continuidad')],
        ['type' => 's', 'value' => $_SESSION['us_sds']], // usu_creo
        ['type' => 's', 'value' => 'A'] // estado
    ];

    $rta = mysql_prepd($sql, $params);
    return $rta;

}

function post_or_null($key) {
  return isset($_POST[$key]) && $_POST[$key] !== '' ? $_POST[$key] : null;
}
function opc_priorizacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=191 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_prior($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=235 and estado='A' ORDER BY 1",$id);
}
function opc_pre_clasifclasificacion($id=''){
  if($_REQUEST['id']!=''){
      $id=divide($_REQUEST['id']);
      $sql="SELECT idcatadeta ,descripcion FROM `catadeta` WHERE idcatalogo=235 and estado='A' and valor=".$id[0]." ORDER BY LENGTH(idcatadeta), idcatadeta;";
      // var_dump($sql);
      $info=datos_mysql($sql);
      return json_encode($info['responseResult']);
  }
}
 function opc_perfil_gest($id=''){
	return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=218 and estado="A" AND descripcion=(select perfil from usuarios where id_usuario='.$_SESSION['us_sds'].') ORDER BY 1',$id);
	}
  function opc_perfil_gestusuario_gest($id=''){
    if($_REQUEST['id']!=''){	
            $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios right join apro_terr at ON id_usuario=at.doc_asignado WHERE 
            perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
            and id_usuario ='{$_SESSION['us_sds']}' ORDER BY nombre";
            $info = datos_mysql($sql);		
		 //return json_encode($sql);	
           return json_encode($info['responseResult']);	
        }
}
function opc_nombre($id=''){
	if(!empty($_REQUEST['perfil'])){
		$perfil = $_REQUEST['perfil'];
		$sql = "SELECT id_usuario, CONCAT(id_usuario, ' - ', nombre) as descripcion 
				FROM usuarios 
				WHERE perfil = '$perfil' AND estado = 'A' 
				ORDER BY nombre";
		return opc_sql($sql, $id);
	}
	return "";
}
function opc_motivo_estado($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=74 and estado='A' ORDER BY 1",$id);
}
function opc_estado_s($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=73 and estado='A' ORDER BY 1",$id);
}
function opc_numsegui($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
}
function opc_usuario_gest($id=''){
	// return opc_sql("SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE estado = 'A'",$id);
}
function opc_rta($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=170 and estado="A" ORDER BY 1',$id);
}
function opc_novedad1($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=297 and estado="A" ORDER BY 1',$id);
}
function opc_novedad2($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=298 and estado="A" ORDER BY 1',$id);
}
function opc_novedad3($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=299 and estado="A" ORDER BY 1',$id);
}
function opc_motivo_no_remoto($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=300 and estado="A" ORDER BY 1',$id);
}
function opc_riesgo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=273 and estado='A' ORDER BY 1",$id);
}
function opc_clasificacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=235 and estado='A' ORDER BY 1",$id);
}
function opc_pre_clasif($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=191 and estado='A' ORDER BY 1",$id);
}
function opc_perfil($id=''){
  return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=218 and estado="A" ORDER BY 1',$id);
  }
  function opc_doc_asignado($id=''){
    $co=datos_mysql("select FN_USUARIO(".$_SESSION['us_sds'].") as co;");
    $com=divide($co['responseResult'][0]['co']);
    return opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE  subred='{$com[2]}' ORDER BY 1",$id);//`perfil` IN('MED','ENF')
  }
function opc_perfilnombre($id=''){
  if($_REQUEST['id']!=''){	
    $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios right join apro_terr at ON id_usuario=at.doc_asignado  WHERE 
    perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
    and subred=(SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}')   ORDER BY nombre";
    var_dump($sql);
    $info = datos_mysql($sql);		
  return json_encode($info['responseResult']);	
  }
}
function opc_perfil_alto($id=''){
  if($_REQUEST['id']!=''){	
      $sql = "SELECT *,id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE 
      perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
      and componente=(SELECT componente FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') 
      and subred=(SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') ORDER BY 1";
      $info = datos_mysql($sql);		
      return json_encode($info['responseResult']);	
  } 
}
function opc_usuario_alto($id=''){
	// return opc_sql("SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE estado = 'A'",$id);
}
function opc_perfil_altousuario_alto($id=''){
  if($_REQUEST['id']!=''){	
          $sql = "SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE 
          perfil=(select descripcion from catadeta c where idcatalogo=218 and idcatadeta='{$_REQUEST['id']}' and estado='A') 
          and subred=(SELECT subred FROM usuarios WHERE id_usuario ='{$_SESSION['us_sds']}') ORDER BY nombre";
          $info = datos_mysql($sql);		
          return json_encode($info['responseResult']);	
      }
}
function opc_accion($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=269 and estado="A" ORDER BY 1',$id);
}
function opc_accion1($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
  }
  function opc_desc_accion1($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
    }
  
  
  function opc_accion1desc_accion1($id=''){
  if($_REQUEST['id']!=''){
        $id=divide($_REQUEST['id']);
        $sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
        $info=datos_mysql($sql);		
        return json_encode($info['responseResult']);
      }
  }
  function opc_accion2desc_accion2($id=''){
    if($_REQUEST['id']!=''){
          $id=divide($_REQUEST['id']);
          $sql="SELECT idcatadeta,descripcion  FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
          $info=datos_mysql($sql);		
          return json_encode($info['responseResult']);
        }
    }
    function opc_accion3desc_accion3($id=''){
      if($_REQUEST['id']!=''){
            $id=divide($_REQUEST['id']);
            $sql="SELECT idcatadeta 'id',descripcion 'asc' FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
            $info=datos_mysql($sql);		
            return json_encode($info['responseResult']);
          }
      }
  function opc_accion2($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
  }
  function opc_desc_accion2($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
  }
  function opc_accion3($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
  }
  function opc_desc_accion3($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
  }
function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($rta);
 return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>