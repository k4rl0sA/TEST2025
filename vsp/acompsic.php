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



function focus_acompsic(){
  return 'acompsic';
 }
 
 
 function men_acompsic(){
  $rta=cap_menus('acompsic','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
   	if ($a=='acompsic'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
 }


 FUNCTION lis_acompsic(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_acompsic']) ? divide($_POST['id_acompsic']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_acompsic A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");  // CAMBIO 
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-acompsic']))? ($_POST['pag-acompsic']-1)* $regxPag:0;

  // CAMBIO P.tipo_doc,P.idpersona
	$sql="SELECT `id_acompsic` ACCIONES,id_acompsic 'Cod Registro',
  P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra, 
fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_acompsic A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";// CAMBIO AGREGAR ESTA LINEA
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; // CAMBIO  AGREGAR ESTA LINEA 
	$sql.="' ORDER BY A.fecha_create"; // CAMBIO  AGREGAR ESTA LINEA
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"acompsic",$regxPag,'../vsp/acompsic.php');
   }


function cmp_acompsic(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='acompsic-lis'>".lis_acompsic()."</div></div>";
	$w='acompsic';
  $d='';
	$o='inf';
//agregar en todas
  $ob='Ob';
  $no='nO';
  $bl='bL';
  $x=false;
  $block=['hab','acc'];
  $event=divide($_POST['id']);
  $ev=$event[2];//CAMBIAO 3 A 2
  $days=fechas_app('vsp');
  $c[]=new cmp('id_acompsic','h','50',$_POST['id'],$w.' '.$o,'Id de Acompsic','id_acompsic',null,null,false,false,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");//
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
//fin agregar en todas
    
    $o='hab';
    $c[]=new cmp($o,'e',null,'HABILIDADES ',$w);
    $c[]=new cmp('autocono','s','2',$d,$w.' hL '.$o,'Autoconocimiento','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('cumuni_aser','s','2',$d,$w.' hL '.$o,'Comunicación Asertiva','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('toma_decis','s','2',$d,$w.' hL '.$o,'Toma de decisiones','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('pensa_crea','s','2',$d,$w.' hL '.$o,'Pensamiento creativo','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('manejo_emo','s','2',$d,$w.' hL '.$o,'Manejo de emociones y sentimientos','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('rela_interp','s','2',$d,$w.' hL '.$o,'Relaciones interpersonales','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('solu_prob','s','2',$d,$w.' hL '.$o,'Solución de problemas y conflictos','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('pensa_critico','s','2',$d,$w.' hL '.$o,'Pensamiento crítico','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('manejo_tension','s','2',$d,$w.' hL '.$o,'Manejo de tensiones y estrés','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('empatia','s','2',$d,$w.' hL '.$o,'Empatia','rta',null,null,false,$x,'','col-2');

    //igual
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
    $c[]=new cmp('fecha_cierre','d','10',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Fecha de Cierre','fecha_cierre',null,null,false,$x,'','col-25',"validDate(this,$days,0);");
    $c[]=new cmp('liker_dificul','s','3',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Liker de Dificultades','liker_dificul',null,null,false,$x,'','col-3');
    $c[]=new cmp('liker_emocion','s','3',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Liker de Emociones','liker_emocion',null,null,false,$x,'','col-3');
    $c[]=new cmp('liker_decision','s','3',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Liker de Decisiones','liker_decision',null,null,false,$x,'','col-25');
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
function opc_rta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=170 and estado='A'  ORDER BY 1 ",$id);
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

/* function gra_acompsic(){
  // print_r($_POST);
  $id=divide($_POST['id_acompsic']);
  if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {$smbin = implode(",",str_replace("'", "", $smbina));}
  if(count($id)==4){//CAMBIO 5 por 4
    $sql="UPDATE vsp_acompsic SET 
            autocono = TRIM(UPPER('{$_POST['autocono']}')),
            cumuni_aser = TRIM(UPPER('{$_POST['cumuni_aser']}')),
            toma_decis = TRIM(UPPER('{$_POST['toma_decis']}')),
            pensa_crea = TRIM(UPPER('{$_POST['pensa_crea']}')),
            manejo_emo = TRIM(UPPER('{$_POST['manejo_emo']}')),
            rela_interp = TRIM(UPPER('{$_POST['rela_interp']}')),
            solu_prob = TRIM(UPPER('{$_POST['solu_prob']}')),
            pensa_critico = TRIM(UPPER('{$_POST['pensa_critico']}')),
            manejo_tension = TRIM(UPPER('{$_POST['manejo_tension']}')),
            empatia = TRIM(UPPER('{$_POST['empatia']}')),
            estrategia_1 = TRIM(UPPER('{$_POST['estrategia_1']}')),
            estrategia_2 = TRIM(UPPER('{$_POST['estrategia_2']}')),
            acciones_1 = TRIM(UPPER('{$_POST['acciones_1']}')),
            desc_accion1 = TRIM(UPPER('{$_POST['desc_accion1']}')),
            acciones_2 = TRIM(UPPER('{$_POST['acciones_2']}')),
            desc_accion2 = TRIM(UPPER('{$_POST['desc_accion2']}')),
            acciones_3 = TRIM(UPPER('{$_POST['acciones_3']}')),
            desc_accion3 = TRIM(UPPER('{$_POST['desc_accion3']}')),
            activa_ruta = TRIM(UPPER('{$_POST['activa_ruta']}')),
            ruta = TRIM(UPPER('{$_POST['ruta']}')),
            novedades = TRIM(UPPER('{$_POST['novedades']}')),
            signos_covid = TRIM(UPPER('{$_POST['signos_covid']}')),
            caso_afirmativo = TRIM(UPPER('{$_POST['caso_afirmativo']}')),
            otras_condiciones = TRIM(UPPER('{$_POST['otras_condiciones']}')),
            observaciones = TRIM(UPPER('{$_POST['observaciones']}')),
            cierre_caso = TRIM(UPPER('{$_POST['cierre_caso']}')),
	          motivo_cierre = TRIM(UPPER('{$_POST['motivo_cierre']}')),
            fecha_cierre = TRIM(UPPER('{$_POST['fecha_cierre']}')),
            liker_dificul = TRIM(UPPER('{$_POST['liker_dificul']}')),
            liker_emocion = TRIM(UPPER('{$_POST['liker_emocion']}')),
            liker_decision = TRIM(UPPER('{$_POST['liker_decision']}')),
            redu_riesgo_cierre = TRIM(UPPER('{$_POST['redu_riesgo_cierre']}')),
	    
    `usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
    WHERE id_acompsic =TRIM(UPPER('{$id[0]}'))";
    //  echo $sql;
  }else if(count($id)==3){//CAMBIO 4 por 3
    $eq=opc_equ();//CAMBIO ABAJO  ELIMINAR ID[1] [trim(upper('{$id[1]}')),]
    $sql="INSERT INTO vsp_acompsic VALUES (NULL,trim(upper('{$id[0]}')),
    trim(upper('{$_POST['fecha_seg']}')),trim(upper('{$_POST['numsegui']}')),trim(upper('{$_POST['evento']}')),trim(upper('{$_POST['estado_s']}')),trim(upper('{$_POST['motivo_estado']}')),trim(upper('{$_POST['autocono']}')),trim(upper('{$_POST['cumuni_aser']}')),trim(upper('{$_POST['toma_decis']}')),trim(upper('{$_POST['pensa_crea']}')),trim(upper('{$_POST['manejo_emo']}')),trim(upper('{$_POST['rela_interp']}')),trim(upper('{$_POST['solu_prob']}')),trim(upper('{$_POST['pensa_critico']}')),trim(upper('{$_POST['manejo_tension']}')),trim(upper('{$_POST['empatia']}')),trim(upper('{$_POST['estrategia_1']}')),trim(upper('{$_POST['estrategia_2']}')),trim(upper('{$_POST['acciones_1']}')),trim(upper('{$_POST['desc_accion1']}')),trim(upper('{$_POST['acciones_2']}')),trim(upper('{$_POST['desc_accion2']}')),trim(upper('{$_POST['acciones_3']}')),trim(upper('{$_POST['desc_accion3']}')),trim(upper('{$_POST['activa_ruta']}')),trim(upper('{$_POST['ruta']}')),trim(upper('{$_POST['novedades']}')),trim(upper('{$_POST['signos_covid']}')),trim(upper('{$_POST['caso_afirmativo']}')),trim(upper('{$_POST['otras_condiciones']}')),trim(upper('{$_POST['observaciones']}')),trim(upper('{$_POST['cierre_caso']}')),trim(upper('{$_POST['motivo_cierre']}')),trim(upper('{$_POST['fecha_cierre']}')),trim(upper('{$_POST['liker_dificul']}')),trim(upper('{$_POST['liker_emocion']}')),trim(upper('{$_POST['liker_decision']}')),trim(upper('{$_POST['redu_riesgo_cierre']}')),TRIM(UPPER('{$smbin}')),
    '{$eq}',TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
     //echo $sql;
  }
    $rta=dato_mysql($sql);
    return $rta;
  } */
 function gra_acompsic() {
    $id = divide($_POST['id_acompsic']);
    $eq = opc_equ();
    $smbin = null;
    if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {
        $smbin = implode(",", str_replace("'", "", $smbina));
    }
     $campos = [
        'idpeople', 'fecha_seg', 'numsegui', 'evento', 'estado_s', 'motivo_estado',
        'autocono', 'cumuni_aser', 'toma_decis', 'pensa_crea', 'manejo_emo', 'rela_interp', 'solu_prob', 'pensa_critico', 'manejo_tension', 'empatia',
        'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
        'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
        'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'liker_dificul', 'liker_emocion', 'liker_decision', 'redu_riesgo_cierre',
        'users_bina', 'equipo_bina',
        'usu_creo', 'usu_update', 'fecha_update', 'estado'
    ];
    if (count($id) == 4) { // UPDATE
          $set = [
            'autocono', 'cumuni_aser', 'toma_decis', 'pensa_crea', 'manejo_emo', 'rela_interp', 'solu_prob', 'pensa_critico', 'manejo_tension', 'empatia',
            'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
            'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
            'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'liker_dificul', 'liker_emocion', 'liker_decision', 'redu_riesgo_cierre',
            'users_bina', 'equipo_bina'
        ];
        $params = [];
        foreach ($set as $campo) {
            if ($campo == 'users_bina') {
                $params[] = ['type' => 's', 'value' => $smbin];
            } elseif ($campo == 'equipo_bina') {
                $params[] = ['type' => 's', 'value' => $eq];
            } else {
                $params[] = ['type' => 's', 'value' => $_POST[$campo] ?? null];
            }
        }
        $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']]; // usu_update
        $sql = "UPDATE vsp_acompsic SET "
            . implode(' = ?, ', $set) . " = ?, usu_update = ?, fecha_update = DATE_SUB(NOW(), INTERVAL 5 HOUR) "
            . "WHERE id_acompsic = ?";
        $params[] = ['type' => 's', 'value' => $id[0]]; // id_acompsic
        $rta = mysql_prepd($sql, $params);
    } else if (count($id) == 3) {
       $params = [
            ['type' => 's', 'value' => $id[0]], // idpeople
            ['type' => 's', 'value' => $_POST['fecha_seg'] ?? null],
            ['type' => 's', 'value' => $_POST['numsegui'] ?? null],
            ['type' => 's', 'value' => $_POST['evento'] ?? null],
            ['type' => 's', 'value' => $_POST['estado_s'] ?? null],
            ['type' => 's', 'value' => $_POST['motivo_estado'] ?? null],
            ['type' => 's', 'value' => $_POST['autocono'] ?? null],
            ['type' => 's', 'value' => $_POST['cumuni_aser'] ?? null],
            ['type' => 's', 'value' => $_POST['toma_decis'] ?? null],
            ['type' => 's', 'value' => $_POST['pensa_crea'] ?? null],
            ['type' => 's', 'value' => $_POST['manejo_emo'] ?? null],
            ['type' => 's', 'value' => $_POST['rela_interp'] ?? null],
            ['type' => 's', 'value' => $_POST['solu_prob'] ?? null],
            ['type' => 's', 'value' => $_POST['pensa_critico'] ?? null],
            ['type' => 's', 'value' => $_POST['manejo_tension'] ?? null],
            ['type' => 's', 'value' => $_POST['empatia'] ?? null],
            ['type' => 's', 'value' => $_POST['estrategia_1'] ?? null],
            ['type' => 's', 'value' => $_POST['estrategia_2'] ?? null],
            ['type' => 's', 'value' => $_POST['acciones_1'] ?? null],
            ['type' => 's', 'value' => $_POST['desc_accion1'] ?? null],
            ['type' => 's', 'value' => $_POST['acciones_2'] ?? null],
            ['type' => 's', 'value' => $_POST['desc_accion2'] ?? null],
            ['type' => 's', 'value' => $_POST['acciones_3'] ?? null],
            ['type' => 's', 'value' => $_POST['desc_accion3'] ?? null],
            ['type' => 's', 'value' => $_POST['activa_ruta'] ?? null],
            ['type' => 's', 'value' => $_POST['ruta'] ?? null],
            ['type' => 's', 'value' => $_POST['novedades'] ?? null],
            ['type' => 's', 'value' => $_POST['signos_covid'] ?? null],
            ['type' => 's', 'value' => $_POST['caso_afirmativo'] ?? null],
            ['type' => 's', 'value' => $_POST['otras_condiciones'] ?? null],
            ['type' => 's', 'value' => $_POST['observaciones'] ?? null],
            ['type' => 's', 'value' => $_POST['cierre_caso'] ?? null],
            ['type' => 's', 'value' => $_POST['motivo_cierre'] ?? null],
            ['type' => 's', 'value' => $_POST['fecha_cierre'] ?? null],
            ['type' => 's', 'value' => $_POST['liker_dificul'] ?? null],
            ['type' => 's', 'value' => $_POST['liker_emocion'] ?? null],
            ['type' => 's', 'value' => $_POST['liker_decision'] ?? null],
            ['type' => 's', 'value' => $_POST['redu_riesgo_cierre'] ?? null],
            ['type' => 's', 'value' => $smbin], // users_bina
            ['type' => 's', 'value' => $eq],    // equipo_bina
            ['type' => 's', 'value' => $_SESSION['us_sds']], // usu_creo
            ['type' => 'z', 'value' => null], // usu_update
            ['type' => 'z', 'value' => null], // fecha_update
            ['type' => 's', 'value' => 'A']   // estado
        ];
        $placeholders = implode(', ', array_fill(0, count($params), '?'));
        $sql = "INSERT INTO vsp_acompsic (
            id_acompsic, " . implode(', ', $campos) . "
        ) VALUES (
            NULL, $placeholders
        )";
        $rta = mysql_prepd($sql, $params);
    } else {
        $rta = "Error: id_acompsic inválido";
    }
    return $rta;
}

  function get_acompsic(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);//CAMBIO ABAJO tener en cuenta el evento
      $sql="SELECT concat_ws('_',id_acompsic,idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,autocono,cumuni_aser,toma_decis,pensa_crea,manejo_emo,rela_interp,solu_prob,pensa_critico,manejo_tension,empatia,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,liker_dificul,liker_emocion,liker_decision,redu_riesgo_cierre,users_bina
      FROM vsp_acompsic
      WHERE id_acompsic ='{$id[0]}'";
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
	if ($a=='acompsic' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class=getData'menu right'>";	
		$rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'acompsic',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/acompsic.php');\"></li>"; //CAMBIO tener en cuenta el evento
	}
	
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }
