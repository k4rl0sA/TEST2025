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
function focus_seguiremot(){
 return 'seguiremot';
}

function men_seguiremot(){
 $rta=cap_menus('seguiremot','pro');
 return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='seguiremot'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  }
  return $rta;
}


 function lis_adoleMas(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_adoleMas']) ? divide($_POST['id_adoleMas']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_adoleMas A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-adoleMas']))? ($_POST['pag-adoleMas']-1)* $regxPag:0;


  
  
	$sql="SELECT `id_adoleMas` ACCIONES,id_adoleMas  'Cod Registro',
P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra,
fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_adoleMas A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"adoleMas",$regxPag,'../vsp/adolmasbien.php');
   }


function cmp_seguiremoto(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='seguiremoto-lis'>".lis_seguiremoto()."</div></div>";
	$w='seguiremoto';
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

  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
  $c[]=new cmp('tipo_caso','s','2',$d,$w.' hab '.$o,'Tipo de Población','tipo_caso',null,null,false,$x,'','col-2',"enbValsCls('tipo_caso',['{$ge}','{$pu}']);"); //enabOthSi('tipo_caso','GyP');
  $c[]=new cmp('etapa','s','3',$d,$w.' GyP Tp '.$o,'Etapa','etapa',null,null,false,$x,'','col-2');//enbValsCls('etapa',['{$ge}','{$pu}','{$pu}']);enabEtap('etapa',['pRe','PuE']);
  
 
  $o='hab';
  $c[]=new cmp($o,'e',null,'ADOLESCENTES ENTRE 12 Y 17 AÑOS, DISFUNCIÓN FAMILIAR Y CONSUMO DE SPA',$w);
  $c[]=new cmp('asis_ctrpre','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$gp.' '.$o,'Entrevista motivacional','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('exam_lab','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'Apropiación de prácticas saludables','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('esqu_vacuna','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'Involucramiento parental','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('cons_micronutr','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'Fortalecimiento de autonomía Reproductiva','rta',null,null,false,$x,'','col-2');
  $c[]=new cmp('cons_micronutr','s','2',$d,$w.' '.$bl.' '.$ge.' '.$gp.' '.$o,'Se identifica avance en el fortalecimiento de habilidades socio emocionales.','rta',null,null,false,$x,'','col-2');
   
    $o='hab';
    $c[]=new cmp($o,'e',null,'ADOLESCENTES ENTRE 12 Y 17 AÑOS, DISFUNCIÓN FAMILIAR Y PREVENCIÓN EN SSR',$w);
    $c[]=new cmp('asiste_control','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'Educación integral para la sexualidad en el adolescente','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('vacuna_comple','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'Dialogo interfamiliar ','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('lacmate_exclu','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'Fortalecimiento de autonomía Reproductiva','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('lacmate_exclu','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'Seguimiento a acceso a método de planificación familiar ','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('lacmate_exclu','s','2',$d,$w.' Rg '.$bl.' '.$pu.' '.$gp.' '.$o,'Se identifican otros riesgos en SM','rta',null,null,false,$x,'','col-2');
    
    
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

function get_seguiremot(){
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

function gra_seguiremot(){

  $id=divide($_POST['id']);
  $sql = "INSERT INTO eac_ruteo_clas VALUES(NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),NULL,NULL,'A')";
  $params = [
  ['type' => 's', 'value' => $id[0]],
  ['type' => 's', 'value' => $_POST['pre_clasif']],
  ['type' => 's', 'value' => $_POST['clasificacion']],
  ['type' => 's', 'value' => $_POST['riesgo']],
  ['type' => 's', 'value' => $_POST['accion']],
  ['type' => 's', 'value' => $_POST['fecha']],
  ['type' => 's', 'value' => $_POST['acciones_1']],
  ['type' => 's', 'value' => $_POST['desc_accion1']],
  ['type' => 's', 'value' => $_POST['acciones_2']],
  ['type' => 's', 'value' => $_POST['desc_accion2']],
  ['type' => 's', 'value' => $_POST['acciones_3']],
  ['type' => 's', 'value' => $_POST['desc_accion3']],
  ['type' => 's', 'value' => $_POST['nombre']],
  ['type' => 's', 'value' => $_POST['solici_agenda']],
  ['type' => 's', 'value' => $_POST['activa_ruta']],
  ['type' => 's', 'value' => $_POST['sectorial']],
  ['type' => 's', 'value' => $_POST['intersectorial']],
  ['type' => 's', 'value' => $_POST['entornos']],
  ['type' => 's', 'value' => $_POST['aseguramiento']],
  ['type' => 'i', 'value' => $_SESSION['us_sds']]
  ];
  $rta = mysql_prepd($sql, $params);
  return $rta;

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

function opc_usuario_gest($id=''){
	// return opc_sql("SELECT id_usuario id,CONCAT(id_usuario,'-',nombre) usuario FROM usuarios WHERE estado = 'A'",$id);
}
function opc_rta($id=''){
return opc_sql('SELECT idcatadeta,descripcion FROM catadeta WHERE idcatalogo=170 and estado="A" ORDER BY 1',$id);
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
    // var_dump($sql);
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
function opc_acciones_1($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
  }
  function opc_desc_accion1($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=75 and estado='A' ORDER BY 1",$id);
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