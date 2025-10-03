<?php
require_once "../libs/seguiremot.php";
ini_set('display_errors','1');
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

function cmp_seguiremoto(){
 $rta="";
 $t=['id_rutclas'=>'', 'id_seguimiento'=>'','fecha_seguimiento'=>'','prioridad'=>'','tipo_prioridad'=>'','num_seguimiento'=>'','estado_seg'=>'','motivo_estado'=>'','nov_pri_fam1'=>'','nov_pri_fam2'=>'','nov_pri_fam3'=>'','gestante_cpn'=>'','inasistente_12m'=>'','inasistente_6_12m'=>'','menor5_rpms'=>'','menor5_riesgo'=>'','vacunacion_incompleta'=>'','acepta_vacunacion'=>'','barrera_salud'=>'','agendamiento'=>'','activacion_ruta'=>'','sin_afiliacion'=>'','acepta_afiliacion'=>'','sujeto_abordaje'=>'','acepta_abordaje'=>'','deriva_perfil1'=>'','asignado_a1'=>'','sujeto_concertacion'=>'','acepta_plan'=>'','deriva_perfil2'=>'','asignado_a2'=>'','accion1'=>'','desc_accion1'=>'','accion2'=>'','desc_accion2'=>'','accion3'=>'','desc_accion3'=>'','observaciones'=>'','continua_seguimiento'=>'','fecha_prox_seguimiento'=>'','motivo_no_continuidad'=>''];
 $w='seguiremot';
 $d=get_seguiremot();
 if ($d=="") {$d=$t;}
 $u=($d['idrutges']== NULL || $d['idrutges']== '')?true:false;
 $days=fechas_app('ruteo');
 $o='seguimiento';
 $mykey=$_POST['id'].'_'.$d['id_rutclas'];
 $c[]=new cmp($o,'e',null,'SEGUIMIENTOS REMOTO',$w);
 $c[]=new cmp('id','h','20',$mykey,$w.' '.$o,'','',null,null,true,$u,'','col-1');

 /*  $c[] = new cmp('fecha_seguimiento','d','10',$d['fecha_seguimiento'],$w.' fecha','Fecha de Seguimiento','fecha_seguimiento',null,null,true,$u,'','col-3');
  $c[] = new cmp('prioridad','s','3',$d['prioridad'],$w.' prioridad','Prioridad','prioridad',null,null,true,$u,'','col-3');
  $c[] = new cmp('tipo_prioridad','s','3',$d['tipo_prioridad'],$w.' tipo_prioridad','Tipo Prioridad','tipo_prioridad',null,null,true,$u,'','col-3');
  $c[] = new cmp('num_seguimiento','nu','11',$d['num_seguimiento'],$w.' num_seguimiento','N° Seguimiento','num_seguimiento',null,null,true,$u,'','col-3');
  $c[] = new cmp('estado_seg','s','3',$d['estado_seg'],$w.' estado_seg','Estado Seguimiento','estado_seg',null,null,true,$u,'','col-3');
  $c[] = new cmp('motivo_estado','s','3',$d['motivo_estado'],$w.' motivo_estado','Motivo Estado','motivo_estado',null,null,false,$u,'','col-3');

  $o='novedades';
  $c[]=new cmp($o,'e',null,'NOVEDADES',$w);
  $c[] = new cmp('nov_pri_fam1','s','3',$d['nov_pri_fam1'],$w.' nov_pri_fam1','Novedad Prioridad Familiar 1','nov_pri_fam1',null,null,false,$u,'','col-3');
  $c[] = new cmp('nov_pri_fam2','s','3',$d['nov_pri_fam2'],$w.' nov_pri_fam2','Novedad Prioridad Familiar 2','nov_pri_fam2',null,null,false,$u,'','col-3');
  $c[] = new cmp('nov_pri_fam3','s','3',$d['nov_pri_fam3'],$w.' nov_pri_fam3','Novedad Prioridad Familiar 3','nov_pri_fam3',null,null,false,$u,'','col-3');
  $c[] = new cmp('gestante_cpn','s','3',$d['gestante_cpn'],$w.' gestante_cpn','Gestante CPN','gestante_cpn',null,null,false,$u,'','col-3');
  $c[] = new cmp('inasistente_12m','s','3',$d['inasistente_12m'],$w.' inasistente_12m','Inasistente 12 meses','inasistente_12m',null,null,false,$u,'','col-3');
  $c[] = new cmp('inasistente_6_12m','s','3',$d['inasistente_6_12m'],$w.' inasistente_6_12m','Inasistente 6-12 meses','inasistente_6_12m',null,null,false,$u,'','col-3');
  $c[] = new cmp('menor5_rpms','s','3',$d['menor5_rpms'],$w.' menor5_rpms','Menor 5 RPMS','menor5_rpms',null,null,false,$u,'','col-3');
  $c[] = new cmp('menor5_riesgo','s','3',$d['menor5_riesgo'],$w.' menor5_riesgo','Menor 5 Riesgo','menor5_riesgo',null,null,false,$u,'','col-3');
  $c[] = new cmp('vacunacion_incompleta','s','3',$d['vacunacion_incompleta'],$w.' vacunacion_incompleta','Vacunación Incompleta','vacunacion_incompleta',null,null,false,$u,'','col-3');
  $c[] = new cmp('acepta_vacunacion','s','3',$d['acepta_vacunacion'],$w.' acepta_vacunacion','Acepta Vacunación','acepta_vacunacion',null,null,false,$u,'','col-3');
  $c[] = new cmp('barrera_salud','s','3',$d['barrera_salud'],$w.' barrera_salud','Barrera Salud','barrera_salud',null,null,false,$u,'','col-3');
  $c[] = new cmp('agendamiento','s','3',$d['agendamiento'],$w.' agendamiento','Agendamiento','agendamiento',null,null,false,$u,'','col-3');
  $c[] = new cmp('activacion_ruta','s','3',$d['activacion_ruta'],$w.' activacion_ruta','Activación Ruta','activacion_ruta',null,null,false,$u,'','col-3');
  $c[] = new cmp('sin_afiliacion','s','3',$d['sin_afiliacion'],$w.' sin_afiliacion','Sin Afiliación','sin_afiliacion',null,null,false,$u,'','col-3');
  $c[] = new cmp('acepta_afiliacion','s','3',$d['acepta_afiliacion'],$w.' acepta_afiliacion','Acepta Afiliación','acepta_afiliacion',null,null,false,$u,'','col-3');
  $c[] = new cmp('sujeto_abordaje','s','3',$d['sujeto_abordaje'],$w.' sujeto_abordaje','Sujeto Abordaje','sujeto_abordaje',null,null,false,$u,'','col-3');
  $c[] = new cmp('acepta_abordaje','s','3',$d['acepta_abordaje'],$w.' acepta_abordaje','Acepta Abordaje','acepta_abordaje',null,null,false,$u,'','col-3');
  $c[] = new cmp('deriva_perfil1','s','3',$d['deriva_perfil1'],$w.' deriva_perfil1','Deriva Perfil 1','deriva_perfil1',null,null,false,$u,'','col-3');
  $c[] = new cmp('asignado_a1','s','3',$d['asignado_a1'],$w.' asignado_a1','Asignado a 1','asignado_a1',null,null,false,$u,'','col-3');
  $c[] = new cmp('sujeto_concertacion','s','3',$d['sujeto_concertacion'],$w.' sujeto_concertacion','Sujeto Concertación','sujeto_concertacion',null,null,false,$u,'','col-3');
  $c[] = new cmp('acepta_plan','s','3',$d['acepta_plan'],$w.' acepta_plan','Acepta Plan','acepta_plan',null,null,false,$u,'','col-3');
  $c[] = new cmp('deriva_perfil2','s','3',$d['deriva_perfil2'],$w.' deriva_perfil2','Deriva Perfil 2','deriva_perfil2',null,null,false,$u,'','col-3');
  $c[] = new cmp('asignado_a2','s','3',$d['asignado_a2'],$w.' asignado_a2','Asignado a 2','asignado_a2',null,null,false,$u,'','col-3');

$o='acciones';
 $c[]=new cmp($o,'e',null,'ACCIONES Y ESTRATEGIAS',$w);
  $c[] = new cmp('accion1','s','3',$d['accion1'],$w.' accion1','Acción 1','accion1',null,null,false,$u,'','col-3');
  $c[] = new cmp('desc_accion1','s','3',$d['desc_accion1'],$w.' desc_accion1','Descripción Acción 1','desc_accion1',null,null,false,$u,'','col-3');
  $c[] = new cmp('accion2','s','3',$d['accion2'],$w.' accion2','Acción 2','accion2',null,null,false,$u,'','col-3');
  $c[] = new cmp('desc_accion2','s','3',$d['desc_accion2'],$w.' desc_accion2','Descripción Acción 2','desc_accion2',null,null,false,$u,'','col-3');
  $c[] = new cmp('accion3','s','3',$d['accion3'],$w.' accion3','Acción 3','accion3',null,null,false,$u,'','col-3');
  $c[] = new cmp('desc_accion3','s','3',$d['desc_accion3'],$w.' desc_accion3','Descripción Acción 3','desc_accion3',null,null,false,$u,'','col-3');
  $c[] = new cmp('observaciones','s','7000',$d['observaciones'],$w.' observaciones','Observaciones','observaciones',null,null,false,$u,'','col-12');
  $c[] = new cmp('continua_seguimiento','s','3',$d['continua_seguimiento'],$w.' continua_seguimiento','¿Continúa Seguimiento?','continua_seguimiento',null,null,false,$u,'','col-3');
  $c[] = new cmp('fecha_prox_seguimiento','d','10',$d['fecha_prox_seguimiento'],$w.' fecha_prox_seguimiento','Fecha Próximo Seguimiento','fecha_prox_seguimiento',null,null,false,$u,'','col-3');
  $c[] = new cmp('motivo_no_continuidad','s','3',$d['motivo_no_continuidad'],$w.' motivo_no_continuidad','Motivo No Continuidad','motivo_no_continuidad',null,null,false,$u,'','col-3');
 */


 
 /* $c[]=new cmp('accion','s','10',$d['accion'],$w.' '.$o,'Definir Acción','clasificacion',null,null,true,false,'','col-5');
 $c[]=new cmp('fecha','d','10',$d['fecha'],$w.' '.$o,'Fecha de Programación','fecha',null,null,true,false,'','col-5','validDate(this,-2,0);'); 
 $c[]=new cmp('acciones_1','s','3',$d['accion1'],$w.' '.$o,'Accion 1','acciones_1',null,null,true,$u,'','col-5',"selectDepend('acciones_1','desc_accion1','clasifica.php');");
 $c[]=new cmp('desc_accion1','s','3',$d['desc_accion1'],$w.' '.$o,'Descripcion Accion 1','desc_accion1',null,null,true,$u,'','col-5');
 $c[]=new cmp('acciones_2','s','3',$d['accion2'],$w.' '.$o,'Accion 2','acciones_2',null,null,false,$u,'','col-5',"selectDepend('acciones_2','desc_accion2','clasifica.php');");
 $c[]=new cmp('desc_accion2','s','3',$d['desc_accion2'],$w.' '.$o,'Descripcion Accion 2','desc_accion2',null,null,false,$u,'','col-5');
 $c[]=new cmp('acciones_3','s','3',$d['accion3'],$w.' '.$o,'Accion 3','acciones_3',null,null,false,$u,'','col-5',"selectDepend('acciones_3','desc_accion3','clasifica.php');");
 $c[]=new cmp('desc_accion3','s','3',$d['desc_accion3'],$w.' '.$o,'Descripcion Accion 3','desc_accion3',null,null,false,$u,'','col-5');
 
 $c[]=new cmp($o,'l',null,'Programación',$w);
 $c[]=new cmp('fecha','d','10',$d['fecha'],$w.' '.$o,'Fecha de Programación','fecha',null,null,true,$u,'','col-5',"validDate(this,$days,20);");
 $c[]=new cmp('solici_agenda','s',3,$d['solic_agend'],$w.' AGe '.$o,'Solicito Servicio Agendamiento','rta',null,'',true,$u,'','col-2');
 $c[]=new cmp('activa_ruta','s','10',$d['ruta'],$w.' AGe '.$o,'Activó Ruta','rta',null,null,true,$u,'','col-2','rutRute();');

 $cl='ruta';
 $c[]=new cmp($cl,'l',null,'ACTIVACIÓN DE RUTA',$w);
 $c[]=new cmp('sectorial','s','2',$d['sectorial'],$w.' '.$cl,'¿Sectorial?','rta',null,null,true,false,'','col-25');
 $c[]=new cmp('intersectorial','s','2',$d['intsectorial'],$w.' '.$cl,'¿Intersectorial?','rta',null,null,true,false,'','col-25');
 $c[]=new cmp('entornos','s','2',$d['entornos'],$w.' '.$cl,'¿Entornos?','rta',null,null,true,false,'','col-25');
 $c[]=new cmp('aseguramiento','s','2',$d['aseguram'],$w.' '.$cl,'Aseguramiento','rta',null,null,true,false,'','col-25');

 
 /*$o='bajo';
 $c[]=new cmp($o,'e',null,'RIESGO BAJO',$w);
  $o='alto';
 $c[]=new cmp($o,'e',null,'RIESGO ALTO',$w); 
//  $c[]=new cmp('perfil','s','90','',$w.' dir '.$o,'Perfil A Asignar','perfil',null,null,false,false,'','col-25',"selectDepend('perfil','nombre','clasifica.php');");
//  $c[]=new cmp('nombre','s','6',$d['profesional'],$w.' dir '.$o,'Profesional Asignado','doc_asignado',null,null,false,false,'','col-25');


 $o='gesefc';
 $c[]=new cmp($o,'e',null,'PROCESO DE GESTIÓN',$w);
 $c[]=new cmp('accion','s','10',$d['accion'],$w.' '.$o,'Definir Acción','accion',null,null,true,true,'','col-5');
 $c[]=new cmp('fecha_gestion','d','10','',$w.' AGe '.$o,'Fecha de Agenda','fecha_gestion',null,null,false,true,'','col-2',"validDate(this,$days,30);");
 $c[]=new cmp('docu_confirm','nu','999999999999999999','',$w.' AGe '.$o,'Documento Confirmado  del Usuario','docu_confirm',null,null,false,true,'','col-2');
 //$c[]=new cmp('perfil_gest','s',3,'',$w.' AGe '.$o,'Perfil que Gestiona','perfil_gest',null,'',false,true,'','col-2',"selectDepend('perfil_gest','usuario_gest','clasifica.php');");
 //$c[]=new cmp('usuario_gest','s','10','',$w.' AGe '.$o,'Usuario que Gestiona','usuario_gest',null,null,false,true,'','col-2');
 $c[]=new cmp('perfil','s','90','',$w.' dir '.$o,'Perfil A Asignar','perfil',null,null,false,true,'','col-25',"selectDepend('perfil','nombre','clasifica.php');");
 $c[]=new cmp('nombre','s','6',$d['profesional'],$w.' dir '.$o,'Profesional Asignado','doc_asignado',null,null,false,true,'','col-25');

 $o='acciones';
 $c[]=new cmp($o,'e',null,'ACCIONES Y ESTRATEGIAS',$w);*/

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