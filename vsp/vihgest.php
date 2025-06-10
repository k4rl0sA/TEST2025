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



function focus_vihgest(){
  return 'vihgest';
 }
 
 
 function men_vihgest(){
  $rta=cap_menus('vihgest','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
if ($a=='vihgest'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
 }


 FUNCTION lis_vihgest(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_vihgestacio']) ? divide($_POST['id_vihgestacio']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_vihgest A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-vihgest']))? ($_POST['pag-vihgest']-1)* $regxPag:0;

  
	$sql="SELECT `id_vihgestacio` ACCIONES,id_vihgestacio 'Cod Registro',
P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra,
  fecha_cierre 'Fecha de Cierre',nombre Creó 
  FROM vsp_vihgest A
  LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"vihgest",$regxPag,'../vsp/vihgest.php');
   }


function cmp_vihgest(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='vihgest-lis'>".lis_vihgest()."</div></div>";
	$w='vihgest';
  $d='';
	$o='inf';
// $nb='disa oculto';
$ob='Ob';
  $no='nO';
  $bl='bL';
  $x=false;
   $block=['hab','acc'];
  $event=divide($_POST['id']);
  $ev=$event[2];
  $days=fechas_app('vsp');
  $ge='pRe';
  $pu='PuE';
  $pg='PYg';
  

	$c[]=new cmp('id_vihgestacio','h','50',$_POST['id'],$w.' '.$o,'Id de vihgest','id_vihgestacio',null,null,false,false,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
  $c[]=new cmp('etapa','s','3',$d,$w.' hab '.$o,'Etapa','etapa',null,null,false,$x,'','col-2',"enabEtap('etapa',['{$ge}','{$pu}','{$pg}']);weksEtap('etapa','PeT');");
  $c[]=new cmp('sema_gest','s','3',$d,$w.' hab PeT '.$o,'Semanas De Gestación/ Días Pos-Evento','sema_gest',null,null,false,$x,'','col-2');
  
    
  $o='hab';
  $c[]=new cmp($o,'e',null,'INFORMACIÓN GESTANTES',$w);
  $c[]=new cmp('asis_ctrpre','s','2',$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Asiste A Controles Prenatales?','rta',null,null,false,$x,'','col-25');
  $c[]=new cmp('exam_lab','s','2',$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Cuenta Con Exámenes De Laboratorio Al Día?','rta',null,null,false,$x,'','col-25');
  $c[]=new cmp('esqu_vacuna','s','3',$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Tiene Esquema De Vacunación Completo?','rta',null,null,false,$x,'','col-25');
  $c[]=new cmp('cons_micronutr','s','2',$d,$w.' '.$bl.' '.$ge.' '.$o,'¿Consume Micronutrientes?','rta',null,null,false,$x,'','col-25');
   
  $o='infpue';
  $c[]=new cmp($o,'e',null,'DESPUES DE LA GESTACION (PUERPERIO Y/O POSTERIOR AL PUERPERIO',$w);
  $c[]=new cmp('fecha_obstetrica','d','10',$d,$w.' '.$bl.' '.$pu.' '.$o,'Fecha Evento Obstetrico','fecha_obstetrica',null,null,false,$x,'','col-15',"validDate(0,-700);");
  $c[]=new cmp('edad_gesta','s','3',$d,$w.' '.$bl.' '.$pu.' '.$o,'Edad gestacional en el momento del evento obstetrico','edad_gesta',null,null,false,$x,'','col-3');
  $c[]=new cmp('resul_gest','s','3',$d,$w.' '.$bl.' '.$pu.' '.$o,'Resultado de la gestación','resul_gest',null,null,false,$x,'','col-15',"enabOthSi('resul_gest','Rg');");
  $c[]=new cmp('meto_fecunda','s','2',$d,$w.' '.$bl.' '.$pu.' '.$o,'¿Cuenta Con Método de Regulación de la fecundidad?','rta',null,null,false,$x,'','col-2',"enabOthSi('meto_fecunda','mF');");
  $c[]=new cmp('cual_metodo','s','3',$d,$w.' '.$bl.' mF '.$pu.' '.$o,'¿Cuál?','cual',null,null,false,false,' ','col-2');

    $c[]=new cmp($o,'e',null,'NACIDO VIVO',$w);
    $c[]=new cmp('asiste_control','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Asiste a Controles de C y D o plan canguro?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('vacuna_comple','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Tiene esquema de vacunación completo para la edad?','rta',null,null,false,$x,'','col-25');
    $c[]=new cmp('lacmate_comple','s','3',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Recibe lactancia materna exclusiva?','rta',null,null,false,$x,'','col-15');
    $c[]=new cmp('recnac_proxi','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿El recién nacido recibió profilaxis?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('formu_lact','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Recibe fórmula láctea?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('tarros_mes','s','3',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Cuántos tarros en el mes?','tarros',null,null,false,$x,'','col-2');
    $c[]=new cmp('caso_con_tmi','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Se trata de un caso confirmado de transmisión materno infantil?','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('asis_provih_rn','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Asiste a programa de VIH (R.N)?','rta',null,null,false,$x,'','col-25');
    $c[]=new cmp('cargaviral_1mes','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Cuenta con carga viral del primer mes?','rta',null,null,false,$x,'','col-25');
    $c[]=new cmp('fecha_carga1mes','d','10',$d,$w.' Rg '.$bl.' '.$pu.' PYg  Cv1 '.$o,'Fecha de carga viral primer mes','fec_conser_1tri1',null,null,false,$x,'','col-15');
    $c[]=new cmp('resul_carga1mes','s','3',$d,$w.' Rg '.$bl.' '.$pu.' PYg Cv1 '.$o,'Resultado','resultado',null,null,false,$x,'','col-2');
    $c[]=new cmp('cargaviral_4mes','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$o,'¿Cuenta con carga viral del cuarto mes?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('fecha_carga4mes','d','10',$d,$w.' Rg '.$bl.' '.$pu.' PYg  Cv1 '.$o,'Fecha de carga viral cuarto mes','fec_conser_1tri1',null,null,false,$x,'','col-2');
    $c[]=new cmp('resul_carga4mes','s','3',$d,$w.' Rg '.$bl.' '.$pu.' PYg Cv1 '.$o,'Resultado','resultado',null,null,false,$x,'','col-2');
    
    $o='infacc';
    $c[]=new cmp($o,'e',null,'GESTANTE Y/O PUERPERA',$w);
    $c[]=new cmp('prueba_rapida','s','2',$d,$w.' PYg '.$o,'¿Tiene prueba rapida?','rta',null,null,false,$x,'','col-2',"enabOthSi('prueba_rapida','cT1');");
    $c[]=new cmp('fec_pruerap1','d','10',$d,$w.' '.$bl.' PYg  cT1 '.$o,'Fecha de Prueba Rápida','fec_pruerap1',null,null,false,$x,'','col-2');
    $c[]=new cmp('carga_viral','s','2',$d,$w.' PYg '.$o,'¿Tiene carga viral?','rta',null,null,false,$x,'','col-2',"enabOthSi('carga_viral','cT2');");
    $c[]=new cmp('fec_cargaviral1','d','10',$d,$w.' PYg cT2 '.$o,'Fecha de carga viral','fec_cargaviral1',null,null,false,$x,'','col-2');
    $c[]=new cmp('resul_cargaviral1','s','3',$d,$w.' PYg cT2 '.$o,'Resultado de Carga Viral','resultado',null,null,false,$x,'','col-2');
    $c[]=new cmp('asis_provih1','s','10',$d,$w.' PYg '.$o,'¿Asiste a programa de VIH?','rta',null,null,false,$x,'','col-3',"enabOthSi('asis_provih1','cT3');");
    $c[]=new cmp('cual1','t','50',$d,$w.' PYg cT3 '.$o,'¿Cuál?','fec_conser_3tri',null,null,false,$x,'','col-3');
    $c[]=new cmp('adhe_tra_antirre1','s','3',$d,$w.' PYg '.$o,'¿Adherente al Tratamiento antirretroviral?','rta',null,null,false,$x,'','col-2');


   /*  $c[]=new cmp('antige_super1','s','2',$d,$w.' '.$pg.' '.$bl.' '.$o,'Antígeno de Superficie','rta',null,null,false,$x,'','col-25',"enabOthSi('antige_super1','A1');");
    $c[]=new cmp('resultado1','s','2',$d,$w.' A1 '.$pg.' '.$bl.' '.$o,'Resultado','rta1',null,null,false,$x,'','col-25');
    $c[]=new cmp('anticor_igm_hb1','s','2',$d,$w.' '.$pg.' '.$bl.' '.$o,'AntiCore Igm HB','rta',null,null,false,$x,'','col-25',"enabOthSi('anticor_igm_hb1','a2');");
    $c[]=new cmp('resultado2','s','2',$d,$w.' a2 '.$pg.' '.$bl.' '.$o,'Resultado','rta1',null,null,false,$x,'','col-25');
    $c[]=new cmp('anticor_toigm_hb1','s','2',$d,$w.' '.$pg.' '.$bl.' '.$o,'AntiCore Total Igm HB','rta',null,null,false,$x,'','col-25',"enabOthSi('anticor_toigm_hb1','A3');");
    $c[]=new cmp('resultado3','s','2',$d,$w.' A3 '.$pg.' '.$bl.' '.$o,'Resultado','rta1',null,null,false,$x,'','col-25');
    
     */
    
    
    $o='acc';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN ACCIONES',$w);
    $c[]=new cmp('estrategia_1','s','3',$d,$w.' '.$o,'Estrategia PF_1','estrategia_1',null,null,false,$x,'','col-5');
    $c[]=new cmp('estrategia_2','s','3',$d,$w.' '.$no.' '.$o,'Estrategia PF_2','estrategia_2',null,null,false,$x,'','col-5');
    $c[]=new cmp('acciones_1','s','3',$d,$w.' '.$o,'Accion 1','acciones_1',null,null,false,$x,'','col-5','selectDepend(\'acciones_1\',\'desc_accion1\',\'../vsp/acompsic.php\');');
    $c[]=new cmp('desc_accion1','s','3',$d,$w.' '.$o,'Descripcion Accion 1','desc_accion1',null,null,false,$x,'','col-5');
    $c[]=new cmp('acciones_2','s','3',$d,$w.' '.$no.' '.$o,'Accion 2','acciones_2',null,null,false,$x,'','col-5','selectDepend(\'acciones_2\',\'desc_accion2\',\'../vsp/acompsic.php\');');
    $c[]=new cmp('desc_accion2','s','3',$d,$w.' '.$no.' '.$o,'Descripcion Accion 2','desc_accion2',null,null,false,$x,'','col-5');
    $c[]=new cmp('acciones_3','s','3',$d,$w.' '.$no.' '.$o,'Accion 3','acciones_3',null,null,false,$x,'','col-5','selectDepend(\'acciones_3\',\'desc_accion3\',\'../vsp/acompsic.php\');');
    $c[]=new cmp('desc_accion3','s','3',$d,$w.' '.$no.' '.$o,'Descripcion Accion 3','desc_accion3',null,null,false,$x,'','col-5');
    $c[]=new cmp('activa_ruta','s','2',$d,$w.' '.$o,'Ruta Activada','rta',null,null,false,$x,'','col-3','enabRuta(this,\'rt\');');
    $c[]=new cmp('ruta','s','3',$d,$w.' '.$no.' rt '.$bl.' '.$o,'Ruta','ruta',null,null,false,$x,'','col-35');
    $c[]=new cmp('novedades','s','3',$d,$w.' '.$no.' '.$o,'Novedades','novedades',null,null,false,$x,'','col-35');
    $c[]=new cmp('signos_covid','s','2',$d,$w.' '.$o,'¿Signos y Síntomas para Covid19?','rta',null,null,false,$x,'','col-2','enabCovid(this,\'cv\');');
    $c[]=new cmp('caso_afirmativo','t','500',$d,$w.' cv '.$bl.' '.$no.' '.$o,'Relacione Cuales signos y sintomas, Y Atención Recibida Hasta el Momento','caso_afirmativo',null,null,false,$x,'','col-4');
    $c[]=new cmp('otras_condiciones','t','500',$d,$w.' cv '.$bl.' '.$no.' '.$o,'Otras Condiciones de Riesgo que Requieren una Atención Complementaria.','otras_condiciones',null,null,false,$x,'','col-4');
    $c[]=new cmp('observaciones','a','1500',$d,$w.' '.$ob.' '.$o,'Observaciones','observaciones',null,null,true,true,'','col-10');
    $c[]=new cmp('cierre_caso','s','2',$d,$w.' '.$ob.' '.$o,'Cierre de Caso','rta',null,null,true,true,'','col-2','enabFincas(this,\'cc\');');
    //igual
    $c[]=new cmp('motivo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Motivo Cierre','motivo_cierre',null,null,false,$x,'','col-55');    
    $c[]=new cmp('fecha_cierre','d','10',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Fecha de Cierre','fecha_cierre',null,null,false,$x,'','col-15',"validDate(this,$days,0);");
    $c[]=new cmp('redu_riesgo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'¿Reduccion del riesgo?','rta',null,null,false,$x,'','col-15');
    $c[]=new cmp('users_bina[]','m','60',$d,$w.' '.$ob.' '.$o,'Usuarios Equipo','bina',null,null,false,true,'','col-5');
	
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}
function opc_tarros($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=209 and estado='A' ORDER BY 1",$id);
}
function opc_resultado($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=208 and estado='A' ORDER BY 1",$id);
}
function opc_etapa($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=136 and estado='A' ORDER BY 1",$id);
}
function opc_edad_gesta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=137 and estado='A' ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
}
function opc_resul_gest($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=193 and estado='A' ORDER BY 1",$id);
}
function opc_cual($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=138 and estado='A' ORDER BY 1",$id);
}
function opc_sema_gest($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=137 ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
}
function opc_bina($id=''){
  return opc_sql("SELECT id_usuario, nombre  from usuarios u WHERE equipo=(select equipo from usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A' ORDER BY 2;",$id);
}
function opc_motivo_cierre($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=198 and estado='A'  ORDER BY 1 ",$id);
}

function opc_rta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
  }
function opc_tipo_doc($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_numsegui($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
}
function opc_evento($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 and estado='A' ORDER BY 1",$id);
}
function opc_estado_s($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=73 and estado='A' ORDER BY 1",$id);
}
function opc_motivo_estado($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=74 and estado='A' ORDER BY 1",$id);
}
function opc_acciones_1($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
}
function opc_desc_accion1($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
  }
function opc_estrategia_1($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=90 and estado='A' ORDER BY 1",$id);
}
function opc_estrategia_2($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=90 and estado='A' ORDER BY 1",$id);
}

function opc_acciones_1desc_accion1($id=''){
if($_REQUEST['id']!=''){
			$id=divide($_REQUEST['id']);
			$sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
			$info=datos_mysql($sql);		
			return json_encode($info['responseResult']);
    }
}
function opc_acciones_2desc_accion2($id=''){
  if($_REQUEST['id']!=''){
        $id=divide($_REQUEST['id']);
        $sql="SELECT idcatadeta,descripcion  FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
        $info=datos_mysql($sql);		
        return json_encode($info['responseResult']);
      }
  }
  function opc_acciones_3desc_accion3($id=''){
    if($_REQUEST['id']!=''){
          $id=divide($_REQUEST['id']);
          $sql="SELECT idcatadeta 'id',descripcion 'asc' FROM `catadeta` WHERE idcatalogo='75' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
          $info=datos_mysql($sql);		
          return json_encode($info['responseResult']);
        }
    }
function opc_acciones_2($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
}
function opc_desc_accion2($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
}
function opc_acciones_3($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
}
function opc_desc_accion3($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
}
function opc_ruta($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=79 and estado='A' ORDER BY 1",$id);
}
function opc_novedades($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=77 and estado='A' ORDER BY 1",$id);
}
function opc_liker_dificul($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=78 and estado='A' ORDER BY 1",$id);
}
function opc_liker_emocion($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=78 and estado='A' ORDER BY 1",$id);
}
function opc_liker_decision($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=78 and estado='A' ORDER BY 1",$id);
}
function opc_equ(){
  $sql="SELECT equipo FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'";
  $info=datos_mysql($sql);		
  return $info['responseResult'][0]['equipo'];
}

function gra_vihgest(){
  $id = divide($_POST['id_vihgestacio']);
  $eq = opc_equ();
  $smbin = null;
  if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {
    $smbin = implode(",", str_replace("'", "", $smbina));
  }

  // Orden de los campos según la tabla
  $campos = [
    'idpeople', 'fecha_seg', 'numsegui', 'evento', 'estado_s', 'motivo_estado', 'etapa', 'sema_gest',
    'asis_ctrpre', 'exam_lab', 'esqu_vacuna', 'cons_micronutr', 'fecha_obstetrica', 'edad_gesta', 'resul_gest', 'meto_fecunda', 'cual_metodo',
    'asiste_control', 'vacuna_comple', 'lacmate_comple', 'recnac_proxi', 'formu_lact', 'tarros_mes', 'caso_con_tmi', 'asis_provih_rn',
    'cargaviral_1mes', 'fecha_carga1mes', 'resul_carga1mes', 'cargaviral_4mes', 'fecha_carga4mes', 'resul_carga4mes',
    'prueba_rapida', 'fec_pruerap1', 'carga_viral', 'fec_cargaviral1', 'resul_cargaviral1', 'asis_provih1', 'cual1', 'adhe_tra_antirre1',
    'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
    'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
    'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'redu_riesgo_cierre', 'users_bina', 'equipo_bina',
    'usu_creo','usu_update', 'fecha_update', 'estado'
  ];
  // Campos fecha que pueden ser nulos
  $campos_fecha_null = [
    'fecha_obstetrica', 'fecha_carga1mes', 'fecha_carga4mes', 'fec_pruerap1', 'fec_cargaviral1', 'fecha_cierre', 'fecha_update', 'fecha_create'
  ];

  if(count($id)==4){
    // UPDATE
    $set = [
      'etapa', 'sema_gest', 'asis_ctrpre', 'exam_lab', 'esqu_vacuna', 'cons_micronutr', 'fecha_obstetrica', 'edad_gesta', 'resul_gest', 'meto_fecunda', 'cual_metodo',
      'asiste_control', 'vacuna_comple', 'lacmate_comple', 'recnac_proxi', 'formu_lact', 'tarros_mes', 'caso_con_tmi', 'asis_provih_rn',
      'cargaviral_1mes', 'fecha_carga1mes', 'resul_carga1mes', 'cargaviral_4mes', 'fecha_carga4mes', 'resul_carga4mes',
      'prueba_rapida', 'fec_pruerap1', 'carga_viral', 'fec_cargaviral1', 'resul_cargaviral1', 'asis_provih1', 'cual1', 'adhe_tra_antirre1',
      'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
      'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
      'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'redu_riesgo_cierre', 'users_bina', 'equipo_bina'
    ];
    $params = [];
    foreach ($set as $campo) {
      if ($campo == 'users_bina') {
        $params[] = ['type' => 's', 'value' => $smbin];
      } elseif ($campo == 'equipo_bina') {
        $params[] = ['type' => 's', 'value' => $eq];
      } elseif (in_array($campo, $campos_fecha_null)) {
        $val = $_POST[$campo] ?? null;
        $params[] = [
          'type' => ($val === '' || $val === null) ? 'z' : 's',
          'value' => ($val === '' || $val === null) ? null : $val
        ];
      } else {
        $params[] = ['type' => 's', 'value' => $_POST[$campo] ?? null];
      }
    }
    $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']]; // usu_update
    $sql = "UPDATE vsp_vihgest SET "
      . implode(' = ?, ', $set) . " = ?, usu_update = ?, fecha_update = DATE_SUB(NOW(), INTERVAL 5 HOUR) "
      . "WHERE id_vihgestacio = ?";
    $params[] = ['type' => 's', 'value' => $id[0]]; // id_vihgestacio
    $rta = mysql_prepd($sql, $params);

  } else if(count($id)==3){
    // INSERT
    $params = [];
    foreach ($campos as $campo) {
      if ($campo == 'idpeople') {
        $params[] = ['type' => 's', 'value' => $id[0]];
      } elseif ($campo == 'users_bina') {
        $params[] = ['type' => 's', 'value' => $smbin];
      } elseif ($campo == 'equipo_bina') {
        $params[] = ['type' => 's', 'value' => $eq];
      } elseif ($campo == 'usu_creo') {
        $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']];
      } elseif ($campo == 'usu_update' || $campo == 'fecha_update') {
        $params[] = ['type' => 'z', 'value' => null];
      } elseif ($campo == 'fecha_create') {
        $params[] = ['type' => 's', 'value' => date('Y-m-d H:i:s')];
      } elseif ($campo == 'estado') {
        $params[] = ['type' => 's', 'value' => 'A'];
      } elseif (in_array($campo, $campos_fecha_null)) {
        $val = $_POST[$campo] ?? null;
        $params[] = [
          'type' => ($val === '' || $val === null) ? 'z' : 's',
          'value' => ($val === '' || $val === null) ? null : $val
        ];
      } else {
        $params[] = ['type' => 's', 'value' => $_POST[$campo] ?? null];
      }
    }
    $placeholders = implode(', ', array_fill(0, count($params), '?'));
    $sql = "INSERT INTO vsp_vihgest (
      id_vihgestacio, " . implode(', ', $campos) . "
    ) VALUES (
      NULL, $placeholders
    )";
    $rta = mysql_prepd($sql, $params);
  } else {
    $rta = "Error: id_vihgestacio inválido";
  }
  return $rta;
}

  function get_vihgest(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_vihgestacio,idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,etapa,sema_gest,asis_ctrpre,exam_lab,esqu_vacuna,cons_micronutr,fecha_obstetrica,edad_gesta,resul_gest,meto_fecunda,cual_metodo,asiste_control,vacuna_comple,lacmate_comple,recnac_proxi,formu_lact,tarros_mes,caso_con_tmi,asis_provih_rn,cargaviral_1mes,fecha_carga1mes,resul_carga1mes,cargaviral_4mes,fecha_carga4mes,resul_carga4mes,prueba_rapida,fec_pruerap1,carga_viral,fec_cargaviral1,resul_cargaviral1,asis_provih1,cual1,adhe_tra_antirre1,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,redu_riesgo_cierre,users_bina
      FROM vsp_vihgest
      WHERE id_vihgestacio ='{$id[0]}'";
      // echo $sql;
      // print_r($id);
      $info=datos_mysql($sql);
      return json_encode($info['responseResult'][0]);
    } 
  }

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
// $rta=iconv('UTF-8','ISO-8859-1',$rta);
// var_dump($a);
// var_dump($rta);
	if ($a=='vihgest' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
    $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'vihgest',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/vihgest.php');\"></li>"; 
	}
	
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }