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


function focus_sifigest(){
  return 'sifigest';
 }
 
 
 function men_sifigest(){
  $rta=cap_menus('sifigest','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
 if ($a=='sifigest'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
 }


 FUNCTION lis_sifigest(){
	// var_dump($_POST['id']);
  $id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_sifigest']) ? divide($_POST['id_sifigest']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_sifigest A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-sifigest']))? ($_POST['pag-sifigest']-1)* $regxPag:0;



	$sql="SELECT `id_sifigest` ACCIONES,id_sifigest  'Cod Registro',
P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra,
    fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_sifigest A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	// return panel_content($datos["responseResult"],"cronicos-lis",5);
  return create_table($total,$datos["responseResult"],"sifigest",$regxPag,'../vsp/sifigest.php');
   }


function cmp_sifigest(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='sifigest-lis'>".lis_sifigest()."</div></div>";
	$w='sifigest';
  $d='';
	$o='inf';
  // $nb='disa oculto';
  $ob='Ob';
  $no='nO';
  $bl='bL';
  $x=false;
  $block=['hab','acc','infpue','infacc'];
  $event=divide($_POST['id']);
  $ev=$event[2];
  $days=fechas_app('vsp');

	$c[]=new cmp('id_sifigest','h','50',$_POST['id'],$w.' '.$o,'Id de sifigest','id_sifigest',null,null,false,false,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
  
  $c[]=new cmp('etapa','s','3',$d,$w.' hab '.$o,'Etapa','etapa',null,null,false,true,'','col-2',"enabEtap('etapa',['pRe','PuE','PYg']);weksEtap('etapa','PeT');");
  $c[]=new cmp('sema_gest','s','3',$d,$w.' PeT hab '.$o,'Semanas De Gestación/ Días Pos-Evento','sema_gest',null,null,false,true,'','col-3');
    

    $o='gest';
    $c[]=new cmp($o,'e',null,'GESTANTES ',$w);
    $c[]=new cmp('asis_ctrpre','s','2',$d,$w.' pRe '.$o,'¿Asiste A Controles Prenatales?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('exam_lab','s','2',$d,$w.' pRe '.$o,'¿Cuenta Con Exámenes De Laboratorio Al Día?','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('esqu_vacuna','s','3',$d,$w.' pRe '.$o,'¿Tiene Esquema De Vacunación Completo?','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('cons_micronutr','s','2',$d,$w.' pRe '.$o,'¿Consume Micronutrientes?','rta',null,null,false,$x,'','col-2');
    
    
    $o='infpue';
    $c[]=new cmp($o,'e',null,'DESPUES DE LA GESTACION (PUERPERIO Y/O POSTERIOR AL PUERPERIO) ',$w);
    $c[]=new cmp('fecha_obstetrica','d','10',$d,$w.' PuE '.$o,'Fecha Evento Obstetrico','fecha_obstetrica',null,null,false,$x,'','col-3');
    $c[]=new cmp('edad_gesta','s','3',$d,$w.' PuE '.$o,'Edad gestacional en el momento del evento obstetrico','edad_gesta',null,null,false,$x,'','col-4');
    $c[]=new cmp('resul_gest','s','3',$d,$w.' PuE '.$o,'Resultado de la gestación','resul_gest',null,null,false,$x,'','col-3',"enabClasValu('resul_gest',['ncvmor','mOr','NOm']);");
    $c[]=new cmp('meto_fecunda','s','2',$d,$w.' PuE '.$o,'¿Cuenta Con Método de Regulación de la fecundidad?','rta',null,null,false,$x,'','col-35',"enabOthSi('meto_fecunda','MFe');");
    $c[]=new cmp('cual','s','3',$d,$w.' PuE MFe '.$o,'¿Cuál?','cual',null,null,false,$x,'','col-3');
    
    $o='ncvmor';
    $c[]=new cmp($o,'e',null,'NACIDO VIVO Y/O MORTINATO',$w);
    $c[]=new cmp('confir_sificong','s','2',$d,$w.' PuE mOr '.$o,'¿Es un caso confirmado de sífilis congénita?','rta',null,null,false,$x,'','col-35',"enabOthSi('confir_sificong','siC');");
    $c[]=new cmp('resul_ser_recnac','s','3',$d,$w.' PuE mOr SiC '.$o,'Resultado de serológia del recién nacido','resultado',null,null,false,$x,'','col-3');


    $c[]=new cmp('trata_recnac','s','3',$d,$w.' PuE NOm '.$o,'Tratamiento Del Recién Nacido','trata_recnac',null,null,false,$x,'','col-35');
    $c[]=new cmp('serol_3meses','s','2',$d,$w.' PuE NOm '.$o,'¿Menor cuenta con serologia a los tres meses?','rta',null,null,false,$x,'','col-35',"enabOthSi('serol_3meses','sER');");
    $c[]=new cmp('fec_conser_1tri2','d','10',$d,$w.' PuE NOm sER '.$o,'Fecha Control Serológico 3 Meses','fec_conser_1tri2',null,null,false,$x,'','col-3');
    $c[]=new cmp('resultado','s','3',$d,$w.' PuE NOm sER '.$o,'Resultado','resultado',null,null,false,$x,'','col-35');
    
    $o='infacc';
    $c[]=new cmp($o,'e',null,'GESTANTE Y/O PUERPERA',$w);
    $c[]=new cmp('ctrl_serol1t','s','2',$d,$w.' PYg '.$o,'Control Serológico 1er Trimestre?','rta',null,null,false,$x,'','col-2',"enabOthSi('ctrl_serol1t','cT1');");
    $c[]=new cmp('fec_conser_1tri1','d','10',$d,$w.' PYg  cT1 '.$o,'Fecha Control Serológico 1er Trimestre','fec_conser_1tri1',null,null,false,$x,'','col-2');
    $c[]=new cmp('resultado_1','s','3',$d,$w.' PYg cT1 '.$o,'Resultado 1','resultado',null,null,false,$x,'','col-2');
    $c[]=new cmp('ctrl_serol2t','s','2',$d,$w.' PYg '.$o,'Control Serológico 2do Trimestre','rta',null,null,false,$x,'','col-2',"enabOthSi('ctrl_serol2t','cT2');");
    $c[]=new cmp('fec_conser_2tri','d','10',$d,$w.' PYg cT2 '.$o,'Fecha Control Serológico 2do Trimestre','fec_conser_2tri',null,null,false,$x,'','col-2');
    $c[]=new cmp('resultado_2','s','3',$d,$w.' PYg cT2 '.$o,'Resultado 2','resultado',null,null,false,$x,'','col-2');
    $c[]=new cmp('ctrl_serol3t','s','10',$d,$w.' PYg '.$o,'Control Serológico 3er Trimestre','rta',null,null,false,$x,'','col-3',"enabOthSi('ctrl_serol3t','cT3');");
    $c[]=new cmp('fec_conser_3tri','d','10',$d,$w.' PYg cT3 '.$o,'Fecha Control Serológico 3er Trimestre','fec_conser_3tri',null,null,false,$x,'','col-3');
    $c[]=new cmp('resultado_3','s','3',$d,$w.' PYg cT3 '.$o,'Resultado 3','resultado',null,null,false,$x,'','col-2');
    
    $o='sifges';
    $c[]=new cmp($o,'e',null,'SIFILIS GESTACIONAL',$w);
    $c[]=new cmp('initratasif','s','2',$d,$w.' PYg '.$o,'¿usuario inicia tratamiento para sifilis gestacional?','rta',null,null,false,$x,'','col-5',"enabOthSi('initratasif','SGs');");
    $c[]=new cmp('fec_1dos_trages1','d','10',$d,$w.' PYg SGs '.$o,'Fecha Primera Dosis De Tratamiento De La Gestante','fec_1dos_trages1',null,null,false,$x,'','col-5');
    $c[]=new cmp('fec_2dos_trages1','d','10',$d,$w.' PYg SGs '.$o,'Fecha Segunda Dosis De Tratamiento De La Gestante','fec_2dos_trages1',null,null,false,$x,'','col-5');
    $c[]=new cmp('fec_3dos_trages1','d','10',$d,$w.' PYg SGs '.$o,'Fecha Tercera Dosis De Tratamiento De La Gestante','fec_3dos_trages1',null,null,false,$x,'','col-5');

    $o='pricont';
    $c[]=new cmp($o,'e',null,'PRIMER CONTACTO',$w);
    $c[]=new cmp('pri_con_sex','s','3',$d,$w.' PYg '.$o,'Primer Contacto Sexual','con_sex',null,null,false,$x,'','col-2',"enbValue('pri_con_sex','iNI',6);");
    $c[]=new cmp('initratasif1','s','2',$d,$w.' PYg iNI '.$o,'¿Contacto Sexual inicia tratamiento para sifilis?','initratasif',null,null,false,$x,'','col-4',"enabOthSi('initratasif1','PcO');");
    $c[]=new cmp('fec_apl_tra_1dos1','d','10',$d,$w.' PYg PcO iNI '.$o,'Fecha Aplicación Tratamiento Primera Dosis','fec_apl_tra_1dos1',null,null,false,$x,'','col-4');
    $c[]=new cmp('fec_apl_tra_2dos1','d','10',$d,$w.' PYg PcO iNI '.$o,'Fecha Aplicación Tratamiento Segunda Dosis','fec_apl_tra_2dos1',null,null,false,$x,'','col-5');
    $c[]=new cmp('fec_apl_tra_3dos1','d','10',$d,$w.' PYg PcO iNI '.$o,'Fecha Aplicación Tratamiento Tercera Dosis','fec_apl_tra_3dos1',null,null,false,$x,'','col-5');

    $o='segcont';
    $c[]=new cmp($o,'e',null,'SEGUNDO CONTACTO',$w);
    $c[]=new cmp('seg_con_sex','s','3',$d,$w.' PYg '.$o,'Segundo Contacto Sexual','con_sex',null,null,false,$x,'','col-2',"enbValue('seg_con_sex','dOs',6);");
    $c[]=new cmp('initratasif2','s','2',$d,$w.' PYg dOs '.$o,'¿Contacto Sexual inicia tratamiento para sifilis?','initratasif',null,null,false,$x,'','col-4',"enabOthSi('initratasif2','ScO');");
    $c[]=new cmp('fec_apl_tra_1dos2','d','10',$d,$w.' PYg ScO dOs '.$o,'Fecha Aplicación  Tratamiento Primera Dosis4','fec_apl_tra_1dos2',null,null,false,$x,'','col-4');
    $c[]=new cmp('fec_apl_tra_2dos2','d','10',$d,$w.' PYg ScO dOs '.$o,'Fecha Aplicación Tratamiento Segunda Dosis5','fec_apl_tra_2dos2',null,null,false,$x,'','col-5');
    $c[]=new cmp('fec_apl_tra_3dos2','d','10',$d,$w.' PYg ScO dOs '.$o,'Fecha Aplicación Tratamiento Tercera Dosis6','fec_apl_tra_3dos2',null,null,false,$x,'','col-5');
    
    $o='prerei';
    $c[]=new cmp($o,'e',null,'PRESENTA REINFECCION',$w);
    $c[]=new cmp('prese_reinfe','s','2',$d,$w.' PYg '.$o,'¿Presenta Reinfección?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('initratasif3','s','2',$d,$w.' PYg '.$o,'¿usuario reinicia tratamiento para sifilis?','initratasif',null,null,false,$x,'','col-4',"enabOthSi('initratasif3','ReI');");
    $c[]=new cmp('fec_1dos_trages2','d','10',$d,$w.' PYg ReI '.$o,'Primera Dosis De Tratamiento De La Gestante7','fec_1dos_trages2',null,null,false,$x,'','col-4');
    $c[]=new cmp('fec_2dos_trages2','d','10',$d,$w.' PYg ReI '.$o,'Segunda Dosis De Tratamiento De La Gestante8','fec_2dos_trages2',null,null,false,$x,'','col-5');
    $c[]=new cmp('fec_3dos_trages2','d','10',$d,$w.' PYg ReI '.$o,'Tercera Dosis De Tratamiento De La Gestante9','fec_3dos_trages2',null,null,false,$x,'','col-5');

    $o='paract';
    $c[]=new cmp($o,'e',null,'PAREJA ACTUAL',$w);
    $c[]=new cmp('reinf_1con','s','3',$d,$w.' PYg '.$o,'Segundo Contacto Sexual','con_sex',null,null,false,$x,'','col-25');
    $c[]=new cmp('initratasif4','s','2',$d,$w.' PYg '.$o,'¿usuario reinicia tratamiento para sifilis?','initratasif',null,null,false,$x,'','col-35',"enabOthSi('initratasif4','pAc');");
    $c[]=new cmp('fec_1dos_trapar','d','10',$d,$w.' PYg pAc '.$o,'Fecha Primera Dosis De Tratamiento de la Pareja','fec_1dos_trapar',null,null,false,$x,'','col-35');
    $c[]=new cmp('fec_2dos_trapar','d','10',$d,$w.' PYg pAc '.$o,'Fecha Segunda Dosis De Tratamiento de la Pareja','fec_2dos_trapar',null,null,false,$x,'','col-35');
    $c[]=new cmp('fec_3dos_trapar','d','10',$d,$w.' PYg pAc '.$o,'Fecha Tercera Dosis De Tratamiento de la Pareja','fec_3dos_trapar',null,null,false,$x,'','col-3');
      
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
    $c[]=new cmp('cierre_caso','s','2',$d,$w.' '.$o,'Cierre de Caso','rta',null,null,false,$x,'','col-1','enabFincas(this,\'cc\');');
    //igual
    $c[]=new cmp('motivo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Motivo Cierre','motivo_cierre',null,null,false,$x,'','col-55');    
    $c[]=new cmp('fecha_cierre','d','10',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Fecha de Cierre','fecha_cierre',null,null,false,$x,'','col-15',"validDate(this,$days,0);");
    $c[]=new cmp('redu_riesgo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'¿Reduccion del riesgo?','rta',null,null,false,$x,'','col-15');
    $c[]=new cmp('users_bina[]','m','60',$d,$w.' '.$ob.' '.$o,'Usuarios Equipo','bina',null,null,false,true,'','col-5');
	
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}
function opc_bina($id=''){
  return opc_sql("SELECT id_usuario, nombre  from usuarios u WHERE equipo=(select equipo from usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A'  ORDER BY 2;",$id);
}
function opc_motivo_cierre($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=198 and estado='A'  ORDER BY 1 ",$id);
}
function opc_initratasif($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=207 and estado='A' ORDER BY 1",$id);
}
function opc_rta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_doc($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_numsegui($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY 1",$id);
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
function opc_etapa($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=136 and estado='A' ORDER BY 1",$id);
}
function opc_sema_gest($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=137 ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
}
function opc_con_sex($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=200 and estado='A' ORDER BY 1",$id);
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
function opc_trata_recnac($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=199 and estado='A' ORDER BY 1",$id);
}
function opc_resultado($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=94 and estado='A' ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
}
function opc_ruta($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=79 and estado='A' ORDER BY 1",$id);
}
function opc_novedades($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=77 and estado='A' ORDER BY 1",$id);
}
function opc_equ(){
  $sql="SELECT equipo FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'";
  $info=datos_mysql($sql);		
  return $info['responseResult'][0]['equipo'];
}

function gra_sifigest(){
  // print_r($_POST);
  $id=divide($_POST['id_sifigest']);
  if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {$smbin = implode(",",str_replace("'", "", $smbina));}
  if(count($id)==4){
    $sql="UPDATE vsp_sifigest SET 
    etapa=trim(upper('{$_POST['etapa']}')),sema_gest=trim(upper('{$_POST['sema_gest']}')),asis_ctrpre=trim(upper('{$_POST['asis_ctrpre']}')),exam_lab=trim(upper('{$_POST['exam_lab']}')),esqu_vacuna=trim(upper('{$_POST['esqu_vacuna']}')),cons_micronutr=trim(upper('{$_POST['cons_micronutr']}')),fecha_obstetrica=trim(upper('{$_POST['fecha_obstetrica']}')),edad_gesta=trim(upper('{$_POST['edad_gesta']}')),resul_gest=trim(upper('{$_POST['resul_gest']}')),meto_fecunda=trim(upper('{$_POST['meto_fecunda']}')),cual=trim(upper('{$_POST['cual']}')),confir_sificong=trim(upper('{$_POST['confir_sificong']}')),resul_ser_recnac=trim(upper('{$_POST['resul_ser_recnac']}')),trata_recnac=trim(upper('{$_POST['trata_recnac']}')),serol_3meses=trim(upper('{$_POST['serol_3meses']}')),fec_conser_1tri2=trim(upper('{$_POST['fec_conser_1tri2']}')),resultado=trim(upper('{$_POST['resultado']}')),ctrl_serol1t=trim(upper('{$_POST['ctrl_serol1t']}')),fec_conser_1tri1=trim(upper('{$_POST['fec_conser_1tri1']}')),resultado_1=trim(upper('{$_POST['resultado_1']}')),ctrl_serol2t=trim(upper('{$_POST['ctrl_serol2t']}')),fec_conser_2tri=trim(upper('{$_POST['fec_conser_2tri']}')),resultado_2=trim(upper('{$_POST['resultado_2']}')),ctrl_serol3t=trim(upper('{$_POST['ctrl_serol3t']}')),fec_conser_3tri=trim(upper('{$_POST['fec_conser_3tri']}')),resultado_3=trim(upper('{$_POST['resultado_3']}')),initratasif=trim(upper('{$_POST['initratasif']}')),fec_1dos_trages1=trim(upper('{$_POST['fec_1dos_trages1']}')),fec_2dos_trages1=trim(upper('{$_POST['fec_2dos_trages1']}')),fec_3dos_trages1=trim(upper('{$_POST['fec_3dos_trages1']}')),pri_con_sex=trim(upper('{$_POST['pri_con_sex']}')),initratasif1=trim(upper('{$_POST['initratasif1']}')),fec_apl_tra_1dos1=trim(upper('{$_POST['fec_apl_tra_1dos1']}')),fec_apl_tra_2dos1=trim(upper('{$_POST['fec_apl_tra_2dos1']}')),fec_apl_tra_3dos1=trim(upper('{$_POST['fec_apl_tra_3dos1']}')),seg_con_sex=trim(upper('{$_POST['seg_con_sex']}')),initratasif2=trim(upper('{$_POST['initratasif2']}')),fec_apl_tra_1dos2=trim(upper('{$_POST['fec_apl_tra_1dos2']}')),fec_apl_tra_2dos2=trim(upper('{$_POST['fec_apl_tra_2dos2']}')),fec_apl_tra_3dos2=trim(upper('{$_POST['fec_apl_tra_3dos2']}')),prese_reinfe=trim(upper('{$_POST['prese_reinfe']}')),initratasif3=trim(upper('{$_POST['initratasif3']}')),fec_1dos_trages2=trim(upper('{$_POST['fec_1dos_trages2']}')),fec_2dos_trages2=trim(upper('{$_POST['fec_2dos_trages2']}')),fec_3dos_trages2=trim(upper('{$_POST['fec_3dos_trages2']}')),reinf_1con=trim(upper('{$_POST['reinf_1con']}')),initratasif4=trim(upper('{$_POST['initratasif4']}')),fec_1dos_trapar=trim(upper('{$_POST['fec_1dos_trapar']}')),fec_2dos_trapar=trim(upper('{$_POST['fec_2dos_trapar']}')),fec_3dos_trapar=trim(upper('{$_POST['fec_3dos_trapar']}')),estrategia_1=trim(upper('{$_POST['estrategia_1']}')),estrategia_2=trim(upper('{$_POST['estrategia_2']}')),acciones_1=trim(upper('{$_POST['acciones_1']}')),desc_accion1=trim(upper('{$_POST['desc_accion1']}')),acciones_2=trim(upper('{$_POST['acciones_2']}')),desc_accion2=trim(upper('{$_POST['desc_accion2']}')),acciones_3=trim(upper('{$_POST['acciones_3']}')),desc_accion3=trim(upper('{$_POST['desc_accion3']}')),activa_ruta=trim(upper('{$_POST['activa_ruta']}')),ruta=trim(upper('{$_POST['ruta']}')),novedades=trim(upper('{$_POST['novedades']}')),signos_covid=trim(upper('{$_POST['signos_covid']}')),caso_afirmativo=trim(upper('{$_POST['caso_afirmativo']}')),otras_condiciones=trim(upper('{$_POST['otras_condiciones']}')),observaciones=trim(upper('{$_POST['observaciones']}')),cierre_caso=trim(upper('{$_POST['cierre_caso']}')),motivo_cierre=trim(upper('{$_POST['motivo_cierre']}')),fecha_cierre=trim(upper('{$_POST['fecha_cierre']}')),redu_riesgo_cierre=trim(upper('{$_POST['redu_riesgo_cierre']}')),
    `usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
    WHERE id_sifigest =TRIM(UPPER('{$id[0]}'))";
    // echo $sql;
  }else if(count($id)==3){
    $eq=opc_equ();
    $sql="INSERT INTO vsp_sifigest VALUES (NULL,trim(upper('{$id[0]}')),
    trim(upper('{$_POST['fecha_seg']}')),trim(upper('{$_POST['numsegui']}')),trim(upper('{$_POST['evento']}')),trim(upper('{$_POST['estado_s']}')),trim(upper('{$_POST['motivo_estado']}')),trim(upper('{$_POST['etapa']}')),trim(upper('{$_POST['sema_gest']}')),trim(upper('{$_POST['asis_ctrpre']}')),trim(upper('{$_POST['exam_lab']}')),trim(upper('{$_POST['esqu_vacuna']}')),trim(upper('{$_POST['cons_micronutr']}')),trim(upper('{$_POST['fecha_obstetrica']}')),trim(upper('{$_POST['edad_gesta']}')),trim(upper('{$_POST['resul_gest']}')),trim(upper('{$_POST['meto_fecunda']}')),trim(upper('{$_POST['cual']}')),trim(upper('{$_POST['confir_sificong']}')),trim(upper('{$_POST['resul_ser_recnac']}')),trim(upper('{$_POST['trata_recnac']}')),trim(upper('{$_POST['serol_3meses']}')),trim(upper('{$_POST['fec_conser_1tri2']}')),trim(upper('{$_POST['resultado']}')),trim(upper('{$_POST['ctrl_serol1t']}')),trim(upper('{$_POST['fec_conser_1tri1']}')),trim(upper('{$_POST['resultado_1']}')),trim(upper('{$_POST['ctrl_serol2t']}')),trim(upper('{$_POST['fec_conser_2tri']}')),trim(upper('{$_POST['resultado_2']}')),trim(upper('{$_POST['ctrl_serol3t']}')),trim(upper('{$_POST['fec_conser_3tri']}')),trim(upper('{$_POST['resultado_3']}')),trim(upper('{$_POST['initratasif']}')),trim(upper('{$_POST['fec_1dos_trages1']}')),trim(upper('{$_POST['fec_2dos_trages1']}')),trim(upper('{$_POST['fec_3dos_trages1']}')),trim(upper('{$_POST['pri_con_sex']}')),trim(upper('{$_POST['initratasif1']}')),trim(upper('{$_POST['fec_apl_tra_1dos1']}')),trim(upper('{$_POST['fec_apl_tra_2dos1']}')),trim(upper('{$_POST['fec_apl_tra_3dos1']}')),trim(upper('{$_POST['seg_con_sex']}')),trim(upper('{$_POST['initratasif2']}')),trim(upper('{$_POST['fec_apl_tra_1dos2']}')),trim(upper('{$_POST['fec_apl_tra_2dos2']}')),trim(upper('{$_POST['fec_apl_tra_3dos2']}')),trim(upper('{$_POST['prese_reinfe']}')),trim(upper('{$_POST['initratasif3']}')),trim(upper('{$_POST['fec_1dos_trages2']}')),trim(upper('{$_POST['fec_2dos_trages2']}')),trim(upper('{$_POST['fec_3dos_trages2']}')),trim(upper('{$_POST['reinf_1con']}')),trim(upper('{$_POST['initratasif4']}')),trim(upper('{$_POST['fec_1dos_trapar']}')),trim(upper('{$_POST['fec_2dos_trapar']}')),trim(upper('{$_POST['fec_3dos_trapar']}')),trim(upper('{$_POST['estrategia_1']}')),trim(upper('{$_POST['estrategia_2']}')),trim(upper('{$_POST['acciones_1']}')),trim(upper('{$_POST['desc_accion1']}')),trim(upper('{$_POST['acciones_2']}')),trim(upper('{$_POST['desc_accion2']}')),trim(upper('{$_POST['acciones_3']}')),trim(upper('{$_POST['desc_accion3']}')),trim(upper('{$_POST['activa_ruta']}')),trim(upper('{$_POST['ruta']}')),trim(upper('{$_POST['novedades']}')),trim(upper('{$_POST['signos_covid']}')),trim(upper('{$_POST['caso_afirmativo']}')),trim(upper('{$_POST['otras_condiciones']}')),trim(upper('{$_POST['observaciones']}')),trim(upper('{$_POST['cierre_caso']}')),trim(upper('{$_POST['motivo_cierre']}')),trim(upper('{$_POST['fecha_cierre']}')),trim(upper('{$_POST['redu_riesgo_cierre']}')),trim(upper('{$smbin}')),
    '{$eq}',TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
    // echo $sql;
  }
    $rta=dato_mysql($sql);
    return $rta;
  } 


  function get_sifigest(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_sifigest,idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,etapa,sema_gest,asis_ctrpre,exam_lab,esqu_vacuna,cons_micronutr,fecha_obstetrica,edad_gesta,resul_gest,meto_fecunda,cual,confir_sificong,resul_ser_recnac,trata_recnac,serol_3meses,fec_conser_1tri2,resultado,ctrl_serol1t,fec_conser_1tri1,resultado_1,ctrl_serol2t,fec_conser_2tri,resultado_2,ctrl_serol3t,fec_conser_3tri,resultado_3,initratasif,fec_1dos_trages1,fec_2dos_trages1,fec_3dos_trages1,pri_con_sex,initratasif1,fec_apl_tra_1dos1,fec_apl_tra_2dos1,fec_apl_tra_3dos1,seg_con_sex,initratasif2,fec_apl_tra_1dos2,fec_apl_tra_2dos2,fec_apl_tra_3dos2,prese_reinfe,initratasif3,fec_1dos_trages2,fec_2dos_trages2,fec_3dos_trages2,reinf_1con,initratasif4,fec_1dos_trapar,fec_2dos_trapar,fec_3dos_trapar,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,redu_riesgo_cierre,users_bina
      FROM vsp_sifigest
      WHERE id_sifigest ='{$id[0]}'";
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
	if ($a=='sifigest' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";
    $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'sifigest',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/sifigest.php');\"></li>";
	}
	
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }
