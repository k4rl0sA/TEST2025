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
 
  $days=fechas_app('vsp');
  $c[]=new cmp('idruteoclas','h','11',$_POST['id'],$w.'idruteoclas','ID Ruteo Clasificado','idruteoclas',null,null,false,$u,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");//
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$u,'','col-2');
  
 
  $o='hab';
  $c[]=new cmp($o,'e',null,'SEGUIMIENTO REMOTO',$w);
  $c[] = new cmp('gestante','s','3',$d,$w.' '.$o,'Gestante','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('menor5','s','3',$d,$w.' '.$o,'Menor de 5 años','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('cronico','s','3',$d,$w.' '.$o,'Usuario crónico','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('general','s','3',$d,$w.' '.$o,'Usuario general','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('nov_pri_fam1','s','50',$d,$w.' '.$o,'Novedad Prioridad Familiar 1','novedad1',null,null,false,$u,'','col-2');
  $c[] = new cmp('gestante_cpn','s','3',$d,$w.' '.$o,'Gestante asiste a CPN','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('nov_pri_fam2','s','50',$d,$w.' '.$o,'Novedad Prioridad Familiar 2','novedad2',null,null,false,$u,'','col-2');
  
  $c[] = new cmp('menor5_rpms','s','3',$d,$w.' '.$o,'Menor 5 RPMS','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('menor5_riesgo','s','3',$d,$w.' '.$o,'Menor 5 Riesgo','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('nov_pri_fam3','s','50',$d,$w.' '.$o,'Novedad Prioridad Familiar 3','novedad3',null,null,false,$u,'','col-2');
  $c[] = new cmp('inasistente_12m','s','3',$d,$w.' '.$o,'Inasistente 12 meses','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('inasistente_6_12m','s','3',$d,$w.' '.$o,'Inasistente 6-12 meses','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('persona_mayor','s','3',$d,$w.' '.$o,'Persona Mayor','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('persona_discapacidad','s','3',$d,$w.' '.$o,'Persona con discapacidad','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('salud_mental','s','3',$d,$w.' '.$o,'Salud Mental','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('nuevo_diagnostico','s','3',$d,$w.' '.$o,'Nuevo Diagnóstico','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('ningun_diagnostico','s','3',$d,$w.' '.$o,'Ningún Diagnóstico','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('vacunacion_incompleta','s','3',$d,$w.' '.$o,'Vacunación Incompleta','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('acepta_vacunacion','s','3',$d,$w.' '.$o,'Acepta Vacunación','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('barrera_salud','s','3',$d,$w.' '.$o,'Barrera Salud','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('agendamiento','s','3',$d,$w.' '.$o,'Agendamiento','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('activacion_ruta','s','3',$d,$w.' '.$o,'Activación Ruta','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('sin_afiliacion','s','3',$d,$w.' '.$o,'Sin Afiliación','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('acepta_afiliacion','s','3',$d,$w.' '.$o,'Acepta Afiliación','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('sujeto_abordaje','s','3',$d,$w.' '.$o,'Sujeto Abordaje','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('acepta_abordaje','s','3',$d,$w.' '.$o,'Acepta Abordaje','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('deriva_perfil1','s','50',$d,$w.' '.$o,'Perfil Derivado 1','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('asignado_a1','s','50',$d,$w.' '.$o,'Asignado a 1','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('sujeto_concertacion','s','3',$d,$w.' '.$o,'Sujeto Concertación','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('acepta_plan','s','3',$d,$w.' '.$o,'Acepta Plan','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('deriva_perfil2','s','50',$d,$w.' '.$o,'Perfil Derivado 2','rta',null,null,false,$u,'','col-2');
  $c[] = new cmp('asignado_a2','s','50',$d,$w.' '.$o,'Asignado a 2','rta',null,null,false,$u,'','col-2');

  $o='acc';
  $c[]=new cmp($o,'e',null,'INFORMACIÓN ACCIONES',$w);
  $c[] = new cmp('accion1','s','50',$d,$w.' '.$o,'Acción 1','accion1',null,null,false,$u,'','col-5','selectDepend(\'accion1\',\'desc_accion1\',\'../ruteo1/seguiRemoto.php\');');
  $c[] = new cmp('desc_accion1','s','50',$d,$w.' '.$o,'Descripción Acción 1','desc_accion1',null,null,false,$u,'','col-5');
  $c[] = new cmp('accion2','s','50',$d,$w.' '.$o,'Acción 2','accion2',null,null,false,$u,'','col-5','selectDepend(\'accion2\',\'desc_accion2\',\'../ruteo1/seguiRemoto.php\');');
  $c[] = new cmp('desc_accion2','s','50',$d,$w.' '.$o,'Descripción Acción 2','desc_accion2',null,null,false,$u,'','col-5');
  $c[] = new cmp('accion3','s','50',$d,$w.' '.$o,'Acción 3','accion3',null,null,false,$u,'','col-5','selectDepend(\'accion3\',\'desc_accion3\',\'../ruteo1/seguiRemoto.php\');');
  $c[] = new cmp('desc_accion3','s','50',$d,$w.' '.$o,'Descripción Acción 3','desc_accion3',null,null,false,$u,'','col-5');
  $c[] = new cmp('observaciones','a','7000',$d,$w.' '.$o,'Observaciones','observaciones',null,null,true,$u,'','col-12');
  $c[] = new cmp('continua_seguimiento','s','3',$d,$w.' '.$o,'¿Continúa Seguimiento?','rta',null,null,true,$u,'','col-2');
  $c[] = new cmp('fecha_prox_seguimiento','d','10',$d,$w.' '.$o,'Fecha Próximo Seguimiento','fecha_prox_seguimiento',null,null,false,$u,'','col-2');
  $c[] = new cmp('motivo_no_continuidad','s','100',$d,$w.' '.$o,'Motivo No Continuidad','motivo_no_remoto',null,null,false,$u,'','col-2');
     
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
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
function opc_accion1($id=''){
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