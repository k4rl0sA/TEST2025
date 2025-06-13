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


function focus_cancinfa(){
  return 'cancinfa';
 }
 
 
 function men_cancinfa(){
  $rta=cap_menus('cancinfa','pro');
  return $rta;
 }
 
 
 function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
 if ($a=='cancinfa'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
 }


 FUNCTION lis_cancinfa(){
	// var_dump($_POST['id']);
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_cancinfa']) ? divide($_POST['id_cancinfa']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_cancinfa A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-cancinfa']))? ($_POST['pag-cancinfa']-1)* $regxPag:0;


  
	$sql="SELECT `id_cancinfa` ACCIONES,id_cancinfa  'Cod Registro',
P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra,
fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_cancinfa A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	// echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"cancinfa",$regxPag,'../vsp/cancinfa.php');
   }


function cmp_cancinfa(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div>
	<div class='contenido' id='cancinfa-lis'>".lis_cancinfa()."</div></div>";
	$w='cancinfa';
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
  

	  $c[]=new cmp('id_cancinfa','h','50',$_POST['id'],$w.' '.$o,'','id_cancinfa',null,null,false,true,'','col-2');
    
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');
    
    $o='hab';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN ',$w);
    $c[]=new cmp('diagnosticado','s','2',$d,$w.' '.$o,'¿DX Confirmado?','rta',null,null,false,$x,'','col-15',"enabOthSi('diagnosticado','cI');");
    $c[]=new cmp('fecha_dx','d','10',$d,$w.' cI '.$bl.' '.$bl.' '.$o,'Fecha de diagnóstico confirmado','fecha_dx',null,null,false,$x,'','col-2','validDate(-2500,0);');
    
    $c[]=new cmp('tratamiento','s','10',$d,$w.' cI '.$bl.' '.$o,'Cuenta con Tratamiento','rta',null,null,false,$x,'','col-2');
    $c[]=new cmp('asiste_control','s','2',$d,$w.' cI '.$bl.' '.$no.' '.$o,'¿Asiste a controles con especialista?','rta',null,null,false,$x,'','col-2',"enabOthSi('asiste_control','tO');");
    $c[]=new cmp('cual_espe','t','500',$d,$w.' tO '.$bl.' '.$no.' '.$o,'Cuál o Cuales especialistas','cual_espe',null,null,false,$x,'','col-2');
    $c[]=new cmp('trata_orde','s','3',$d,$w.' cI '.$bl.' '.$no.' '.$o,'Tratamiento ordenado','trata_orde',null,null,false,$x,'','col-2',"enbValsCls('trata_orde',['fC','Fq','fR','Fo']);");
    $c[]=new cmp('fecha_cirug','d','10',$d,$w.' fC '.$bl.' '.$no.' '.$o,'Fecha de Cirugía','fecha_cirug',null,null,false,$x,'','col-2','validDate(-2500,0);');
    $c[]=new cmp('fecha_quimio','d','10',$d,$w.' Fq '.$bl.' '.$no.' '.$o,'Fecha de inicio de Quimioterapia','fecha_quimio',null,null,false,$x,'','col-2','validDate(-2500,0);');
    $c[]=new cmp('fecha_radiote','d','10',$d,$w.' fR '.$bl.' '.$no.' '.$o,'Fecha de inicio de Radioterapia','fecha_radiote',null,null,false,$x,'','col-2','validDate(-2500,0);');
    $c[]=new cmp('fecha_otro','d','10',$d,$w.' Fo '.$bl.' '.$no.' '.$o,'Fecha de Otro','fecha_otro',null,null,false,$x,'','col-2','validDate(-2500,0);');
    $c[]=new cmp('otro_cual','t','500',$d,$w.' Fo '.$bl.' '.$no.' '.$o,'¿Otro Cuál?','otro_cual',null,null,false,$x,'','col-4'); 
    
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
    //igual
    
   
    $c[]=new cmp('supera_problema','s','2',$d,$w.' cc '.$no.' '.$o,'Se han superado las necesidades en la categoria problemas prácticos','rta',null,null,false,$x,'','col-35');
    $c[]=new cmp('supera_emocional','s','2',$d,$w.' cc '.$no.' '.$o,'Se han superado las necesidades en la categoria estado emocional','rta',null,null,false,$x,'','col-35');
    $c[]=new cmp('supera_dolor','s','2',$d,$w.' cc '.$no.' '.$o,'Se han superado las necesidades en la categoria Valoración del dolor','rta',null,null,false,$x,'','col-3');
    $c[]=new cmp('supera_funcional','s','2',$d,$w.' cc '.$no.' '.$o,'Se han superado las necesidades en la categoria valoracion funcional','rta',null,null,false,$x,'','col-35');
    $c[]=new cmp('supera_educacion','s','2',$d,$w.' cc '.$no.' '.$o,'Se han superado las necesidades en la categoria Educación','rta',null,null,false,$x,'','col-35');
    $c[]=new cmp('redu_riesgo_cierre','s','2',$d,$w.' cc '.$no.' '.$o,'¿Reduccion del riesgo?','rta',null,null,false,$x,'','col-3');
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
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
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
function opc_trata_orde($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=93 and estado='A' ORDER BY 1",$id);
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

function gra_cancinfa(){ 
  $id = divide($_POST['id_cancinfa']);
  $eq = opc_equ();
  $smbin = null;
  if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {
    $smbin = implode(",", str_replace("'", "", $smbina));
  }

  // Orden de los campos según la tabla
  $campos = [
    'idpeople', 'fecha_seg', 'numsegui', 'evento', 'estado_s', 'motivo_estado',
    'diagnosticado', 'fecha_dx', 'tratamiento', 'asiste_control', 'cual_espe', 'trata_orde',
    'fecha_cirug', 'fecha_quimio', 'fecha_radiote', 'fecha_otro', 'otro_cual',
    'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2',
    'acciones_3', 'desc_accion3', 'activa_ruta', 'ruta', 'novedades', 'signos_covid',
    'caso_afirmativo', 'otras_condiciones', 'observaciones', 'cierre_caso', 'motivo_cierre',
    'fecha_cierre', 'supera_problema', 'supera_emocional', 'supera_dolor', 'supera_funcional',
    'supera_educacion', 'redu_riesgo_cierre', 'users_bina', 'equipo_bina', 'usu_creo',
    'usu_update', 'fecha_update', 'estado'
  ];
  // Campos fecha que pueden ser nulos
  $campos_fecha_null = ['fecha_dx', 'fecha_cirug', 'fecha_quimio', 'fecha_radiote', 'fecha_otro', 'fecha_cierre', 'fecha_update'];

  if(count($id)==4){
    // UPDATE
    $set = [
      'diagnosticado', 'fecha_dx', 'tratamiento', 'asiste_control', 'cual_espe', 'trata_orde',
      'fecha_cirug', 'fecha_quimio', 'fecha_radiote', 'fecha_otro', 'otro_cual',
      'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2',
      'acciones_3', 'desc_accion3', 'activa_ruta', 'ruta', 'novedades', 'signos_covid',
      'caso_afirmativo', 'otras_condiciones', 'observaciones', 'cierre_caso', 'motivo_cierre',
      'fecha_cierre', 'supera_problema', 'supera_emocional', 'supera_dolor', 'supera_funcional',
      'supera_educacion', 'redu_riesgo_cierre', 'users_bina', 'equipo_bina'
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
    $sql = "UPDATE vsp_cancinfa SET "
      . implode(' = ?, ', $set) . " = ?, usu_update = ?, fecha_update = DATE_SUB(NOW(), INTERVAL 5 HOUR) "
      . "WHERE id_cancinfa = ?";
    $params[] = ['type' => 's', 'value' => $id[0]]; // id_cancinfa
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
    $sql = "INSERT INTO vsp_cancinfa (
      id_cancinfa, " . implode(', ', $campos) . "
    ) VALUES (
      NULL, $placeholders
    )";
    $rta = mysql_prepd($sql, $params);
  } else {
    $rta = "Error: id_cancinfa inválido";
  }
  return $rta;
}
  function get_cancinfa(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_cancinfa,idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,diagnosticado,fecha_dx,tratamiento,asiste_control,cual_espe,trata_orde,fecha_cirug,fecha_quimio,fecha_radiote,fecha_otro,otro_cual,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,supera_problema,supera_emocional,supera_dolor,supera_funcional,supera_educacion,redu_riesgo_cierre,users_bina
      FROM vsp_cancinfa
      WHERE id_cancinfa ='{$id[0]}'";
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
	if ($a=='cancinfa' && $b=='acciones'){//a mnombre del modulo
		$rta="<nav class='menu right'>";	
    $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'cancinfa',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/cancinfa.php');\"></li>"; 
	}
	
 return $rta;
}


function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
   }
