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



function focus_dntsevymod(){
  return 'dntsevymod';
 }
 
 
 function men_dntsevymod(){
  $rta=cap_menus('dntsevymod','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
 if ($a=='dntsevymod'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
 }


 FUNCTION lis_dntsevymod(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_dntsevymod']) ? divide($_POST['id_dntsevymod']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_dntsevymod A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-dntsevymod']))? ($_POST['pag-dntsevymod']-1)* $regxPag:0;

  
	$sql="SELECT `id_dntsevymod` ACCIONES,id_dntsevymod  'Cod Registro',
P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra,
fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_dntsevymod A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"dntsevymod",$regxPag,'../vsp/dntsevymod.php');
   }


function cmp_dntsevymod(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='dntsevymod-lis'>".lis_dntsevymod()."</div></div>";
	$w='dntsevymod';
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
  $p=get_persona();

	$c[]=new cmp('id_dntsevymod','h','50',$_POST['id'],$w.' '.$o,'','id_dntsevymod',null,null,false,true,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
  $c[]=new cmp('sexo','h','50',$p['sexo'],$w.' '.$o,'sexo','sexo',null,'',false,false,'','col-1');
	$c[]=new cmp('fechanacimiento','h','10',$p['fecha_nacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-2');  

  $o='hab';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN ',$w);
    $c[]=new cmp('patolo_base','s','3',$d,$w.' '.$o,'Patologia de Base','patolo_base',null,null,false,$x,'','col-2');
    $c[]=new cmp('segui_medico','s','3',$d,$w.' '.$o,'Seguimiento Medico','segui_medico',null,null,false,$x,'','col-2');
    $c[]=new cmp('asiste_control','s','2',$d,$w.' '.$o,'Asiste a Controles','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('vacuna_comple','s','2',$d,$w.' '.$o,'Tiene Vacunas Completas','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('lacmate_exclu','s','3',$d,$w.' '.$o,'Lactancia materna Exclusiva','lacmate_exclu',null,null,false,$x,'','col-2');
    $c[]=new cmp('lacmate_comple','s','3',$d,$w.' '.$o,'Lactancia materna Complementaria','lacmate_comple',null,null,false,$x,'','col-2');
    $c[]=new cmp('alime_complemen','s','3',$d,$w.' '.$o,'Alimentación Complementaria','alime_complemen',null,null,false,$x,'','col-2');
    $c[]=new cmp('peso','sd','4',$d,$w.' '.$o,'Peso Gramos(0.82 = 820 Gramos)','peso','rgxpeso','##.#',false,$x,'','col-2',"Zsco('zscore','../vsp/dntsevymod.php');");
    $c[]=new cmp('talla','sd','5',$d,$w.' '.$o,'Talla (Cm) (75.2 =Cm,mm)','talla','rgxtalla','###.#',false,$x,'','col-2',"Zsco('zscore','../vsp/dntsevymod.php');");
    $c[]=new cmp('zscore','t','20',$d,$w.' '.$bl.' '.$o,'Zscore','zscore',null,null,false,false,'','col-2');
    $c[]=new cmp('clasi_nutri','s','3',$d,$w.' '.$o,'Clasificación Nutricional','clasi_nutri',null,null,false,false,'','col-2');//'.$ob.'
    $c[]=new cmp('gana_peso','s','2',$d,$w.' '.$o,'Ganancia de Peso','rta',null,null,false,$x,'','col-2');
    //$c[]=new cmp('trata_desnutri','s','3',$d,$w.' '.$o,'Tratamiento de Desnutrición','trata_desnutri',null,null,false,$x,'','col-2',"enabOthSi('trata_desnutri','tdnt');");
    $c[]=new cmp('trata_desnutri','s','3',$d,$w.' '.$o,'Tratamiento de Desnutrición','trata_desnutri',null,null,false,$x,'','col-2',"enbValue('trata_desnutri','tdnt','7');");
    $c[]=new cmp('tratamiento','t','500',$d,$w.' tdnt '.$no.' '.$bl.' '.$o,'Tratamiento','tratamiento',null,null,false,false,'','col-2');
    $c[]=new cmp('consume_fruyverd','s','2',$d,$w.' '.$o,'Consume Frutas y Verduras','rtaali',null,null,false,$x,'','col-2');
    $c[]=new cmp('consume_carnes','s','2',$d,$w.' '.$o,'Consume Carnes','rtaali',null,null,false,$x,'','col-2');
    $c[]=new cmp('consume_azucares','s','2',$d,$w.' '.$o,'Consume Azucar','rtaali',null,null,false,$x,'','col-2');
    $c[]=new cmp('actividad_fisica','s','2',$d,$w.' '.$o,'Realiza Actividad Fisica','rtaali',null,null,false,$x,'','col-2');
    $c[]=new cmp('apoyo_alimentario','s','2',$d,$w.' '.$o,'Apoyo Alimentario','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('signos_alarma','s','2',$d,$w.' '.$o,'Signos de Alarma','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('signos_alarma_seg','s','2',$d,$w.' '.$o,'Signos de Alarma Al Momento del Seguimiento','rta',null,null,false,$x,'','col-2');
    
    
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
    $c[]=new cmp('motivo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Motivo Cierre','motivo_cierre',null,null,false,$x,'','col-55');
    $c[]=new cmp('fecha_cierre','d','10',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Fecha de Cierre','fecha_cierre',null,null,false,$x,'','col-25',"validDate(this,$days,0);");
    $c[]=new cmp('redu_riesgo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'¿Reduccion del riesgo?','rta',null,null,false,$x,'','col-15');
    $c[]=new cmp('users_bina[]','m','60',$d,$w.' '.$ob.' '.$o,'Usuarios Equipo','bina',null,null,false,true,'','col-5');

	
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		$sql="SELECT FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,fecha, 
		FN_EDAD(fecha_nacimiento,CURDATE()),
		TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE() ) AS ano,
  		TIMESTAMPDIFF(MONTH,fecha_nacimiento ,CURDATE() ) % 12 AS mes,
  		DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento,INTERVAL TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) MONTH)) AS dia
		from person P left join hog_carac V ON vivipersona=id_viv 
		WHERE idpeople='".$id[0]."'";
		// echo $sql;
		$info=datos_mysql($sql);
				return $info['responseResult'][0];
		}
	} 


function get_zscore(){
	$id=divide($_POST['val']);
	 $fechaNacimiento = new DateTime($id[1]);
	 $fechaActual = new DateTime();
	 $diferencia = $fechaNacimiento->diff($fechaActual);
	 $edadEnDias = $diferencia->days;
	$ind = ($edadEnDias<=730) ? 'PL' : 'PT' ;
	$sex=$id[2];

$sql="SELECT (POWER(($id[0] / (SELECT M FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3])),
	(SELECT L FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3])) - 1) / 
	((SELECT L FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3]) *
 (SELECT S FROM tabla_zscore WHERE indicador = '$ind' AND sexo = '$sex[0]' AND edad_dias = $id[3])) as rta ";
  // echo $sql;
 $info=datos_mysql($sql);
 	if (!$info['responseResult']) {
		return '';
	}else{
		$z=number_format((float)$info['responseResult'][0]['rta'], 6, '.', '');
		switch ($z) {
			case ($z <=-3):
				$des=3;
				break;
			case ($z >-3 && $z <=-2):
				$des=2;
				break;
			case ($z >-2 && $z <=-1):
				$des=1;
				break;
			case ($z>-1 && $z <=1):
					$des=4;
				break;
			case ($z >1 && $z <=2):
					$des=5;
				break;
			case ($z >2 && $z <=3):
					$des=6;
				break;
				case ($z >3):
					$des=7;
				break;
			default:
				$des=8;
				break;
		}
    //return $sql;
		 return json_encode([$z,$des]);
	}
}
   
function opc_bina($id=''){
  return opc_sql("SELECT id_usuario, nombre  from usuarios u WHERE equipo=(select equipo from usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A'  ORDER BY 2;",$id);
}
function opc_motivo_cierre($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=198 and estado='A'  ORDER BY 1 ",$id);
}

function opc_rtaali($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=196 and estado='A' ORDER BY LPAD(idcatadeta,2,'0')",$id);
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
function opc_even_prio($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=92 and estado='A' ORDER BY 1",$id);
}
function opc_patolo_base($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=156 and estado='A' ORDER BY 1",$id);
}
function opc_segui_medico($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=195 and estado='A' ORDER BY 1",$id);
}
function opc_lacmate_exclu($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=88 and estado='A' ORDER BY 1",$id);
}
function opc_lacmate_comple($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=88 and estado='A' ORDER BY 1",$id);
}
function opc_alime_complemen($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=88 and estado='A' ORDER BY 1",$id);
}
function opc_clasi_nutri($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=98 and estado='A' ORDER BY 1",$id);
}
function opc_trata_desnutri($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=158 and estado='A' ORDER BY 1",$id);
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
function opc_acciones_1($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=22 and estado='A' ORDER BY 1",$id);
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
function opc_equ(){
  $sql="SELECT equipo FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'";
  $info=datos_mysql($sql);		
  return $info['responseResult'][0]['equipo'];
}

function gra_dntsevymod(){
    $id = divide($_POST['id_dntsevymod']);
    $eq = opc_equ();
    $smbin = null;
    if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {
        $smbin = implode(",", str_replace("'", "", $smbina));
    }

    // Orden de los campos según la tabla
    $campos = [
        'idpeople', 'fecha_seg', 'numsegui', 'evento', 'estado_s', 'motivo_estado',
        'patolo_base', 'segui_medico', 'asiste_control', 'vacuna_comple', 'lacmate_exclu', 'lacmate_comple', 'alime_complemen',
        'peso', 'talla', 'zscore', 'clasi_nutri', 'gana_peso', 'trata_desnutri', 'tratamiento',
        'consume_fruyverd', 'consume_carnes', 'consume_azucares', 'actividad_fisica', 'apoyo_alimentario',
        'signos_alarma', 'signos_alarma_seg', 'estrategia_1', 'estrategia_2',
        'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
        'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
        'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'redu_riesgo_cierre',
        'users_bina', 'equipo_bina', 'usu_creo', 'usu_update', 'fecha_update', 'estado'
    ];
    // Campos fecha que pueden ser nulos
    $campos_fecha_null = ['fecha_cierre', 'fecha_update'];

    if(count($id)==4){
        // UPDATE
        $set = [
            'patolo_base', 'segui_medico', 'asiste_control', 'vacuna_comple', 'lacmate_exclu', 'lacmate_comple', 'alime_complemen',
            'peso', 'talla', 'zscore', 'clasi_nutri', 'gana_peso', 'trata_desnutri', 'tratamiento',
            'consume_fruyverd', 'consume_carnes', 'consume_azucares', 'actividad_fisica', 'apoyo_alimentario',
            'signos_alarma', 'signos_alarma_seg', 'estrategia_1', 'estrategia_2',
            'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
            'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
            'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'redu_riesgo_cierre',
            'users_bina', 'equipo_bina'
        ];

    $zscore_val = $_POST['zscore'] ?? null;
    $zscore_part = null;
    $clasi_nutri_part = null;
    if($zscore_val && strpos($zscore_val, ',') !== false) {
      list($zscore_part, $clasi_nutri_part) = explode(',', $zscore_val, 2);
    }else{
      $zscore_part = $zscore_val;
    } 
    $params = [];
        foreach ($set as $campo) {
          if ($campo == 'zscore') {
            $params[] = ['type' => 's', 'value' => $zscore_part];
          }elseif (campo == 'users_bina') {
            $params[] = ['type' => 's', 'value' => $smbin];
          }elseif ($campo == 'equipo_bina') {
            $params[] = ['type' => 's', 'value' => $eq];
          }elseif (in_array($campo, $campos_fecha_null)) {
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
        $sql = "UPDATE vsp_dntsevymod SET "
            . implode(' = ?, ', $set) . " = ?, usu_update = ?, fecha_update = DATE_SUB(NOW(), INTERVAL 5 HOUR) "
            . "WHERE id_dntsevymod = ?";
        $params[] = ['type' => 's', 'value' => $id[0]]; // id_dntsevymod
        $rta = mysql_prepd($sql, $params);

    } else if(count($id)==3){
        // INSERT
        $zscore_val = $_POST['zscore'] ?? null;
        $zscore_part = null;
        $clasi_nutri_part = null;
        if ($zscore_val && strpos($zscore_val, ',') !== false) {
            list($zscore_part, $clasi_nutri_part) = explode(',', $zscore_val, 2);
        } else {
            $zscore_part = $zscore_val;
        }
        $params = [];
        foreach ($campos as $campo) {
            if ($campo == 'zscore') {
            $params[] = ['type' => 's', 'value' => $zscore_part];
        }elseif ($campo == 'idpeople') {
                $params[] = ['type' => 's', 'value' => $id[0]];
            } elseif ($campo == 'users_bina') {
                $params[] = ['type' => 's', 'value' => $smbin];
            } elseif ($campo == 'equipo_bina') {
                $params[] = ['type' => 's', 'value' => $eq];
            } elseif ($campo == 'usu_creo') {
                $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']];
            } elseif ($campo == 'usu_update' || $campo == 'fecha_update' || $campo == 'fecha_create') {
                $params[] = ['type' => 'z', 'value' => null];
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
        $sql = "INSERT INTO vsp_dntsevymod (
            id_dntsevymod, " . implode(', ', $campos) . "
        ) VALUES (
            NULL, $placeholders
        )";
        //$rta=show_sql($sql, $params);
        $rta = mysql_prepd($sql, $params);
    } else {
        $rta = "Error: id_dntsevymod inválido";
    }
    return $rta;
}

  function get_dntsevymod(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_dntsevymod,D.idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,
      FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,
      patolo_base,segui_medico,asiste_control,vacuna_comple,lacmate_exclu,lacmate_comple,alime_complemen,peso,talla,zscore,clasi_nutri,gana_peso,trata_desnutri,tratamiento,consume_fruyverd,consume_carnes,consume_azucares,actividad_fisica,apoyo_alimentario,signos_alarma,signos_alarma_seg,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,redu_riesgo_cierre,users_bina
      FROM vsp_dntsevymod D
      LEFT JOIN person P ON D.idpeople=P.idpeople
      WHERE id_dntsevymod ='{$id[0]}'";
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
	if ($a=='dntsevymod' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
    $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'dntsevymod',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/dntsevymod.php');\"></li>"; //CAMBIO tener en cuenta el evento
	}
	
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }
