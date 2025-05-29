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


function focus_condsuic(){
  return 'condsuic';
 }
 
 
 function men_condsuic(){
  $rta=cap_menus('condsuic','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
if ($a=='condsuic'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
 }


 function lis_condsuic(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_condsuic']) ? divide($_POST['id_condsuic']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_condsuic A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-condsuic']))? ($_POST['pag-condsuic']-1)* $regxPag:0;


  
  
	$sql="SELECT `id_condsuic` ACCIONES,id_condsuic  'Cod Registro',
P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra,
fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_condsuic A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"condsuic",$regxPag,'../vsp/condsuic.php');
   }


function cmp_condsuic(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='condsuic-lis'>".lis_condsuic()."</div></div>";
	$w='condsuic';
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
  $gp='GyP';

  $c[]=new cmp('id_condsuic','h','50',$_POST['id'],$w.' '.$o,'','id_condsuic',null,null,false,true,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
  $c[]=new cmp('tipo_caso','s','2',$d,$w.' hab '.$o,'Tipo de Población','tipo_caso',null,null,false,$x,'','col-2',"enabOthSi('tipo_caso','GyP');");
  $c[]=new cmp('etapa','s','3',$d,$w.' GyP Tp '.$o,'Etapa','etapa',null,null,false,$x,'','col-2',"enabEtap('etapa',['pRe','PuE','PuE']);");//enbValsCls('etapa',['{$ge}','{$pu}','{$pu}']);
  $c[]=new cmp('sema_gest','s','3',$d,$w.' GyP Tp '.$o,'Semanas De Gestación/ Días Pos-Evento','sema_gest',null,null,false,$x,'','col-3');
 
  $o='hab';
  $c[]=new cmp($o,'e',null,'INFORMACIÓN GESTANTE',$w);
  $c[]=new cmp('asis_ctrpre','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$gp.' '.$o,'¿Asiste A Controles Prenatales?','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('exam_lab','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'¿Cuenta Con Exámenes De Laboratorio Al Día?','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('esqu_vacuna','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'¿Tiene Esquema De Vacunación Completo?','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('cons_micronutr','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'¿Consume Micronutrientes?','rta',null,null,false,$x,'','col-2');
   
    $o='hab';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN PUERPERIA',$w);
    $c[]=new cmp('fecha_obstetrica','d','10',$d,$w.' '.$bl.' '.$pu.' '.$gp.' '.$o,'Fecha Evento Obstetrico','fecha_obstetrica',null,null,false,$x,'','col-2');
    $c[]=new cmp('edad_gesta','s','3',$d,$w.' '.$bl.' '.$pu.' '.$gp.' '.$o,'Edad gestacional en el momento del evento obstetrico','edad_gesta',null,null,false,$x,'','col-2');
    $c[]=new cmp('resul_gest','s','2',$d,$w.' '.$bl.' '.$pu.' '.$gp.' '.$o,'Resultado de la gestación','resul_gest',null,null,false,$x,'','col-2',"enabOthSi('resul_gest','Rg');");
    $c[]=new cmp('meto_fecunda','s','2',$d,$w.' '.$bl.' '.$pu.' '.$gp.' '.$o,'¿Cuenta Con Método de Regulación de la fecundidad?','rta',null,null,false,$x,'','col-2',"enabOthSi('meto_fecunda','mF');");
    $c[]=new cmp('cual','s','3',$d,$w.' '.$bl.' '.$pu.' mF '.$gp.' '.$o,'¿Cuál?','cual',null,null,false,$x,'','col-2');
    $c[]=new cmp('peso_nacer','n','4',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'Peso del recién nacido al nacer (gr)','peso_nacer',null,null,false,$x,'','col-2');
    $c[]=new cmp('asiste_control','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'¿Asiste a Controles de Crecimiento y Desarrollo o plan canguro?','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('vacuna_comple','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'¿Tiene esquema de vacunación completo para la edad?','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('lacmate_exclu','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'¿Recibe lactancia materna exclusiva?','rta',null,null,false,$x,'','col-2');

    $o='hab';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN CONDUCTA SUICIDA',$w);
    $c[]=new cmp('persis_morir','s','2',$d,$w.' '.$o,'¿Persiste la idea de morir?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('proce_eapb','s','2',$d,$w.' '.$o,'¿Cuenta con proceso psicoterapéutico con su EAPB?','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('otra_conduc','s','2',$d,$w.' '.$o,'¿Se evidencia otra conducta suicida?','rta',null,null,false,$x,'','col-3',"enabOthNo('otra_conduc','cos');");
    $c[]=new cmp('cual_conduc','s','3',$d,$w.' cos '.$bl.' '.$o,'¿Cuál?','cual_conduc',null,null,false,$x,'','col-3');
    $c[]=new cmp('conduc_otrofam','s','2',$d,$w.' '.$o,'¿Se evidencia conducta suicida en algún otro miembro de la familia?','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('tam_cope','s','2',$d,$w.' '.$o,'Aplicación tamizaje COPE','rta',null,null,false,$x,'','col-2',"enabOthSi('tam_cope','cP');");
    $c[]=new cmp('total_afron','s','3',$d,$w.' cP '.$bl.' '.$o,'Total Afrontamiento','total_afron',null,null,false,$x,'','col-3');
    $c[]=new cmp('total_evita','s','3',$d,$w.' cP '.$bl.' '.$o,'Total Evitación','total_evita',null,null,false,$x,'','col-2');
    
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
    $c[]=new cmp('cierre_caso','s','2',$d,$w.' '.$ob.' '.$o,'Cierre de Caso','rta',null,null,true,true,'','col-2',"enabOthSi('cierre_caso','cc');");//disaOthNo('cierre_caso','Lk');disaOthNo('cierre_caso','cO');
    //igual
    $c[]=new cmp('motivo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Motivo Cierre','motivo_cierre',null,null,false,$x,'','col-55');    
    $c[]=new cmp('fecha_cierre','d','10',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Fecha de Cierre','fecha_cierre',null,null,false,$x,'','col-15',"validDate(this,$days,0);");
    $c[]=new cmp('aplica_tamiz','s','2',$d,$w.' cc '.$bl.' '.$o,'Aplica Tamizaje Cope','rta',null,null,false,$x,'','col-15',"enabOthSi('aplica_tamiz','cO');disaOthNo('aplica_tamiz','Lk');");
    $c[]=new cmp('liker_dificul','s','3',$d,$w.' Lk '.$bl.' '.$no.' '.$o,'Liker de Dificultades','liker_dificul',null,null,false,$x,'','col-2');
    $c[]=new cmp('liker_emocion','s','3',$d,$w.' Lk '.$bl.' '.$no.' '.$o,'Liker de Emociones','liker_emocion',null,null,false,$x,'','col-2');
    $c[]=new cmp('liker_decision','s','3',$d,$w.' Lk '.$bl.' '.$no.' '.$o,'Liker de Decisiones','liker_decision',null,null,false,$x,'','col-2');
    
    $c[]=new cmp('cope_afronta','s','3',$d,$w.' cO '.$bl.' '.$no.' '.$o,'Total Afrontamiento','cope_afronta',null,null,false,$x,'','col-2');
    $c[]=new cmp('cope_evitacion','s','3',$d,$w.' cO '.$bl.' '.$no.' '.$o,'Total Evitación','cope_evitacion',null,null,false,$x,'','col-2');
    $c[]=new cmp('incremen_afron','s','3',$d,$w.' cO '.$bl.' '.$no.' '.$o,'Se Evidencia Incremento Estrategias de Afrontamiento','incremen_afron',null,null,false,$x,'','col-2');
    $c[]=new cmp('incremen_evita','s','3',$d,$w.' cO '.$bl.' '.$no.' '.$o,'Se Evidencia Decremento Estrategias de Evitacion','incremen_evita',null,null,false,$x,'','col-2');
    $c[]=new cmp('redu_riesgo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'¿Reduccion del riesgo?','rta',null,null,false,$x,'','col-2');
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
function opc_resul_gest($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=193 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_caso($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=197 and estado='A' ORDER BY 1",$id);
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
function opc_esqu_vacuna($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo= and estado='A' ORDER BY 1",$id);
}
function opc_motivo_estado($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=74 and estado='A' ORDER BY 1",$id);
}
function opc_etapa($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=136 and estado='A' ORDER BY 1",$id);
}

function opc_sema_gest($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=137 and estado='A' ORDER BY LPAD(idcatadeta,2,'0')",$id);
}
  
function opc_edad_gesta($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=137 and estado='A' ORDER BY LPAD(idcatadeta,2,'0')",$id);
}
function opc_cual($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=138 and estado='A' ORDER BY 1",$id);
}
function opc_otra_conduc($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=   and estado='A' ORDER BY 1",$id);
}
function opc_cual_conduc($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=139 and estado='A' ORDER BY 1",$id);
}

function opc_total_afron($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=140 and estado='A' ORDER BY 1",$id);
}
function opc_total_evita($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=141 and estado='A' ORDER BY 1",$id);
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
function opc_cope_afronta($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=140 and estado='A' ORDER BY 1",$id);
}
function opc_cope_evitacion($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=141 and estado='A' ORDER BY 1",$id);
}
function opc_incremen_afron($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=142 and estado='A' ORDER BY 1",$id);
} 
function opc_incremen_evita($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=143 and estado='A' ORDER BY 1",$id);
}
function opc_equ(){
  $sql="SELECT equipo FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'";
  $info=datos_mysql($sql);		
  return $info['responseResult'][0]['equipo'];
}

function gra_condsuic(){
  // print_r($_POST);
  $id=divide($_POST['id_condsuic']);
  if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {$smbin = implode(",",str_replace("'", "", $smbina));}
  if(count($id)==4){
    $sql="UPDATE vsp_condsuic SET 
    etapa=trim(upper('{$_POST['etapa']}')),sema_gest=trim(upper('{$_POST['sema_gest']}')),asis_ctrpre=trim(upper('{$_POST['asis_ctrpre']}')),exam_lab=trim(upper('{$_POST['exam_lab']}')),esqu_vacuna=trim(upper('{$_POST['esqu_vacuna']}')),cons_micronutr=trim(upper('{$_POST['cons_micronutr']}')),fecha_obstetrica=trim(upper('{$_POST['fecha_obstetrica']}')),edad_gesta=trim(upper('{$_POST['edad_gesta']}')),resul_gest=trim(upper('{$_POST['resul_gest']}')),meto_fecunda=trim(upper('{$_POST['meto_fecunda']}')),cual=trim(upper('{$_POST['cual']}')),peso_nacer=trim(upper('{$_POST['peso_nacer']}')),asiste_control=trim(upper('{$_POST['asiste_control']}')),vacuna_comple=trim(upper('{$_POST['vacuna_comple']}')),lacmate_exclu=trim(upper('{$_POST['lacmate_exclu']}')),persis_morir=trim(upper('{$_POST['persis_morir']}')),proce_eapb=trim(upper('{$_POST['proce_eapb']}')),otra_conduc=trim(upper('{$_POST['otra_conduc']}')),cual_conduc=trim(upper('{$_POST['cual_conduc']}')),conduc_otrofam=trim(upper('{$_POST['conduc_otrofam']}')),tam_cope=trim(upper('{$_POST['tam_cope']}')),total_afron=trim(upper('{$_POST['total_afron']}')),total_evita=trim(upper('{$_POST['total_evita']}')),estrategia_1=trim(upper('{$_POST['estrategia_1']}')),estrategia_2=trim(upper('{$_POST['estrategia_2']}')),acciones_1=trim(upper('{$_POST['acciones_1']}')),desc_accion1=trim(upper('{$_POST['desc_accion1']}')),acciones_2=trim(upper('{$_POST['acciones_2']}')),desc_accion2=trim(upper('{$_POST['desc_accion2']}')),acciones_3=trim(upper('{$_POST['acciones_3']}')),desc_accion3=trim(upper('{$_POST['desc_accion3']}')),activa_ruta=trim(upper('{$_POST['activa_ruta']}')),ruta=trim(upper('{$_POST['ruta']}')),novedades=trim(upper('{$_POST['novedades']}')),signos_covid=trim(upper('{$_POST['signos_covid']}')),caso_afirmativo=trim(upper('{$_POST['caso_afirmativo']}')),otras_condiciones=trim(upper('{$_POST['otras_condiciones']}')),observaciones=trim(upper('{$_POST['observaciones']}')),cierre_caso=trim(upper('{$_POST['cierre_caso']}')),motivo_cierre=trim(upper('{$_POST['motivo_cierre']}')),fecha_cierre=trim(upper('{$_POST['fecha_cierre']}')),aplica_tamiz=trim(upper('{$_POST['aplica_tamiz']}')),liker_dificul=trim(upper('{$_POST['liker_dificul']}')),liker_emocion=trim(upper('{$_POST['liker_emocion']}')),liker_decision=trim(upper('{$_POST['liker_decision']}')),cope_afronta=trim(upper('{$_POST['cope_afronta']}')),cope_evitacion=trim(upper('{$_POST['cope_evitacion']}')),incremen_afron=trim(upper('{$_POST['incremen_afron']}')),incremen_evita=trim(upper('{$_POST['incremen_evita']}')),redu_riesgo_cierre=trim(upper('{$_POST['redu_riesgo_cierre']}')),
    `usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
    WHERE id_condsuic =TRIM(UPPER('{$id[0]}'))";
      // echo $sql;
  }else if(count($id)==3){
    $eq=opc_equ();
    $sql="INSERT INTO vsp_condsuic VALUES (NULL,trim(upper('{$id[0]}')),
    trim(upper('{$_POST['fecha_seg']}')),
    trim(upper('{$_POST['numsegui']}')),
    trim(upper('{$_POST['evento']}')),
    trim(upper('{$_POST['estado_s']}')),
    trim(upper('{$_POST['motivo_estado']}')),
    trim(upper('{$_POST['tipo_caso']}')),
    trim(upper('{$_POST['etapa']}')),
    trim(upper('{$_POST['sema_gest']}')),
    trim(upper('{$_POST['asis_ctrpre']}')),
    trim(upper('{$_POST['exam_lab']}')),
    trim(upper('{$_POST['esqu_vacuna']}')),
    trim(upper('{$_POST['cons_micronutr']}')),
    trim(upper('{$_POST['fecha_obstetrica']}')),
    trim(upper('{$_POST['edad_gesta']}')),
    trim(upper('{$_POST['resul_gest']}')),
    trim(upper('{$_POST['meto_fecunda']}')),
    trim(upper('{$_POST['cual']}')),
    trim(upper('{$_POST['peso_nacer']}')),
    trim(upper('{$_POST['asiste_control']}')),
    trim(upper('{$_POST['vacuna_comple']}')),
    trim(upper('{$_POST['lacmate_exclu']}')),
    trim(upper('{$_POST['persis_morir']}')),
    trim(upper('{$_POST['proce_eapb']}')),
    trim(upper('{$_POST['otra_conduc']}')),
    trim(upper('{$_POST['cual_conduc']}')),
    trim(upper('{$_POST['conduc_otrofam']}')),
    trim(upper('{$_POST['tam_cope']}')),
    trim(upper('{$_POST['total_afron']}')),
    trim(upper('{$_POST['total_evita']}')),
    trim(upper('{$_POST['estrategia_1']}')),
    trim(upper('{$_POST['estrategia_2']}')),
    trim(upper('{$_POST['acciones_1']}')),
    trim(upper('{$_POST['desc_accion1']}')),
    trim(upper('{$_POST['acciones_2']}')),
    trim(upper('{$_POST['desc_accion2']}')),
    trim(upper('{$_POST['acciones_3']}')),
    trim(upper('{$_POST['desc_accion3']}')),
    trim(upper('{$_POST['activa_ruta']}')),
    trim(upper('{$_POST['ruta']}')),
    trim(upper('{$_POST['novedades']}')),
    trim(upper('{$_POST['signos_covid']}')),
    trim(upper('{$_POST['caso_afirmativo']}')),
    trim(upper('{$_POST['otras_condiciones']}')),
    trim(upper('{$_POST['observaciones']}')),
    trim(upper('{$_POST['cierre_caso']}')),
    trim(upper('{$_POST['motivo_cierre']}')),
    trim(upper('{$_POST['fecha_cierre']}')),
    trim(upper('{$_POST['aplica_tamiz']}')),
    trim(upper('{$_POST['liker_dificul']}')),
    trim(upper('{$_POST['liker_emocion']}')),
    trim(upper('{$_POST['liker_decision']}')),
    trim(upper('{$_POST['cope_afronta']}')),
    trim(upper('{$_POST['cope_evitacion']}')),
    trim(upper('{$_POST['incremen_afron']}')),
    trim(upper('{$_POST['incremen_evita']}')),
    trim(upper('{$_POST['redu_riesgo_cierre']}')),
    TRIM(UPPER('{$smbin}')),    
    '{$eq}',TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
      //  echo $sql;
    }
      $rta=dato_mysql($sql);
      return $rta;
  } 

  function get_condsuic(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_condsuic,idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,tipo_caso,etapa,sema_gest,asis_ctrpre,exam_lab,esqu_vacuna,cons_micronutr,fecha_obstetrica,edad_gesta,resul_gest,meto_fecunda,cual,peso_nacer,asiste_control,vacuna_comple,lacmate_exclu,persis_morir,proce_eapb,otra_conduc,cual_conduc,conduc_otrofam,tam_cope,total_afron,total_evita,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,aplica_tamiz,liker_dificul,liker_emocion,liker_decision,cope_afronta,cope_evitacion,incremen_afron,incremen_evita,redu_riesgo_cierre
      FROM vsp_condsuic
      WHERE id_condsuic ='{$id[0]}'";
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
	if ($a=='condsuic' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
    $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'condsuic',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/condsuic.php');\"></li>";
	}
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }