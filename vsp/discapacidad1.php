<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
//MOSTRAR TODOS LOS ERRORES Y WARNINGS
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

function focus_discapacidad1(){
  return 'discapacidad1';
}

function men_discapacidad1(){
  $rta=cap_menus('discapacidad1','pro');
  return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
   $rta = ""; 
   $acc=rol($a);
   	if ($a=='discapacidad1'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
  return $rta;
}

FUNCTION lis_discapacidad1(){
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['id_otroprio']) ? divide($_POST['id_otroprio']) : null);
  $info=datos_mysql("SELECT COUNT(*) total FROM vsp_discapacidad A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;
  $pag=(isset($_POST['pag-discapacidad1']))? ($_POST['pag-discapacidad1']-1)* $regxPag:0;

	$sql="SELECT `id_otroprio` ACCIONES,id_otroprio 'Cod Registro',
  P.tipo_doc,P.idpersona,fecha_seg Fecha,numsegui Seguimiento,FN_CATALOGODESC(87,evento) EVENTO,FN_CATALOGODESC(73,estado_s) estado,cierre_caso Cierra, 
fecha_cierre 'Fecha de Cierre',nombre Creó 
FROM vsp_discapacidad A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  LEFT JOIN   person P ON A.idpeople=P.idpeople";
	$sql.=" WHERE A.estado = 'A' AND A.idpeople='".$id[0]; 
	$sql.="' ORDER BY A.fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"discapacidad1",$regxPag,'../vsp/discapacidad1.php');
}

function cmp_discapacidad1(){
	$rta="<div class='encabezado'>TABLA SEGUIMIENTOS DISCAPACIDAD</div>
	<div class='contenido' id='discapacidad1-lis'>".lis_discapacidad1()."</div></div>";
	$w='discapacidad1';
  $d='';
	$o='inf';
  $ob='Ob';
  $no='nO';
  $bl='bL';
  $x=false;
  $block=['hab','acc'];
  $event=divide($_POST['id']);
  $ev=$event[2];
  $days=fechas_app('vsp');
  $c[]=new cmp('id_otroprio','h','50',$_POST['id'],$w.' '.$o,'Id de Discapacidad','id_otroprio',null,null,false,false,'','col-2');
  $c[]=new cmp('fecha_seg','d','10',$d,$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-2',"validDate(this,$days,0);");
  $c[]=new cmp('numsegui','s','3',$d,$w.' '.$o,'Seguimiento N°','numsegui',null,null,true,true,'','col-2',"staEfe('numsegui','sta');EnabEfec(this,['cuid','disc','estado','hab','acc'],['Ob'],['nO'],['bL'])");
  $c[]=new cmp('evento','s','3',$ev,$w.' '.$o,'Evento','evento',null,null,false,false,'','col-2');
  $c[]=new cmp('estado_s','s','3',$d,$w.' sTa '.$o,'Estado','estado_s',null,null,true,true,'','col-2',"enabFielSele(this,true,['motivo_estado'],['3']);EnabEfec(this,['cuid','disc','estado','hab','acc'],['Ob'],['nO'],['bL']);");
  $c[]=new cmp('motivo_estado','s','3',$d,$w.' '.$o,'Motivo de Estado','motivo_estado',null,null,false,$x,'','col-2');

     $o='estado';
    $c[]=new cmp($o,'e',null,'ESTADO ACTUAL',$w);
    $c[]=new cmp('encuentra','s','3',$d,$w.' '.$o,'¿Cómo se encuentra hoy?','encuentra',null,null,false,$x,'','col-2');
    $c[]=new cmp('facial','s','3',$d,$w.' '.$o,'Estado de ánimo','facial',null,null,false,$x,'','col-2');
    $c[]=new cmp('corporal','s','3',$d,$w.' '.$o,'Estado físico','corporal',null,null,false,$x,'','col-2');
    $c[]=new cmp('respiracion','s','3',$d,$w.' '.$o,'Signos de respiración','respiracion',null,null,false,$x,'','col-2');
    $c[]=new cmp('cuidado','s','3',$d,$w.' '.$o,'Autocuidado','cuidado',null,null,false,$x,'','col-2');
    $c[]=new cmp('esparcimiento','s','3',$d,$w.' '.$o,'Esparcimiento','esparcimiento',null,null,false,$x,'','col-2');
    $c[]=new cmp('comunicacion','s','3',$d,$w.' '.$o,'Comunicación','comunicacion',null,null,false,$x,'','col-2'); 

    $o='acc';
    $c[]=new cmp($o,'e',null,'INFORMACIÓN ACCIONES',$w);
    $c[]=new cmp('estrategia_1','s','3',$d,$w.' '.$o,'Estrategia PF_1','estrategia_1',null,null,false,$x,'','col-5');
    $c[]=new cmp('estrategia_2','s','3',$d,$w.' '.$no.' '.$o,'Estrategia PF_2','estrategia_2',null,null,false,$x,'','col-5');
    $c[]=new cmp('acciones_1','s','3',$d,$w.' '.$o,'Acción 1','acciones_1',null,null,false,$x,'','col-5','selectDepend(\'acciones_1\',\'desc_accion1\',\'../vsp/discapacidad1.php\');');
    $c[]=new cmp('desc_accion1','s','3',$d,$w.' '.$o,'Descripción Acción 1','desc_accion1',null,null,false,$x,'','col-5');
    $c[]=new cmp('acciones_2','s','3',$d,$w.' '.$no.' '.$o,'Acción 2','acciones_2',null,null,false,$x,'','col-5','selectDepend(\'acciones_2\',\'desc_accion2\',\'../vsp/discapacidad1.php\');');
    $c[]=new cmp('desc_accion2','s','3',$d,$w.' '.$no.' '.$o,'Descripción Acción 2','desc_accion2',null,null,false,$x,'','col-5');
    $c[]=new cmp('acciones_3','s','3',$d,$w.' '.$no.' '.$o,'Acción 3','acciones_3',null,null,false,$x,'','col-5','selectDepend(\'acciones_3\',\'desc_accion3\',\'../vsp/discapacidad1.php\');');
    $c[]=new cmp('desc_accion3','s','3',$d,$w.' '.$no.' '.$o,'Descripción Acción 3','desc_accion3',null,null,false,$x,'','col-5');
    $c[]=new cmp('activa_ruta','s','2',$d,$w.' '.$o,'Ruta Activada','rta',null,null,false,$x,'','col-3','enabRuta(this,\'rt\');');
    $c[]=new cmp('ruta','s','3',$d,$w.' '.$no.' rt '.$bl.' '.$o,'Ruta','ruta',null,null,false,$x,'','col-35');
    $c[]=new cmp('novedades','s','3',$d,$w.' '.$no.' '.$o,'Novedades','novedades',null,null,false,$x,'','col-35');
    $c[]=new cmp('signos_covid','s','2',$d,$w.' '.$o,'¿Signos y Síntomas para Covid19?','rta',null,null,false,$x,'','col-2','enabCovid(this,\'cv\');');
    $c[]=new cmp('caso_afirmativo','t','500',$d,$w.' cv '.$bl.' '.$no.' '.$o,'Relacione Cuales signos y síntomas, Y Atención Recibida Hasta el Momento','caso_afirmativo',null,null,false,$x,'','col-4');
    $c[]=new cmp('otras_condiciones','t','500',$d,$w.' cv '.$bl.' '.$no.' '.$o,'Otras Condiciones de Riesgo que Requieren una Atención Complementaria.','otras_condiciones',null,null,false,$x,'','col-4');
    $c[]=new cmp('observaciones','a','500',$d,$w.' '.$ob.' '.$o,'Observaciones','observaciones',null,null,true,true,'','col-10');
    $c[]=new cmp('cierre_caso','s','2',$d,$w.' '.$ob.' '.$o,'Cierre de Caso','rta',null,null,true,true,'','col-2','enabFincas(this,\'cc\');');
    $c[]=new cmp('motivo_cierre','s','3',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Motivo Cierre','motivo_cierre',null,null,false,$x,'','col-55');
    $c[]=new cmp('fecha_cierre','d','10',$d,$w.' cc '.$bl.' '.$no.' '.$o,'Fecha de Cierre','fecha_cierre',null,null,false,$x,'','col-25',"validDate(this,$days,0);");
    $c[]=new cmp('redu_riesgo_cierre','s','2',$d,$w.' cc '.$bl.' '.$no.' '.$o,'¿Reducción del riesgo?','rta',null,null,false,$x,'','col-15');
    $c[]=new cmp('users_bina[]','m','18',$d,$w.' '.$ob.' '.$o,'Usuarios Equipo','bina',null,null,false,true,'','col-5');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function gra_discapacidad1() {
    $id = divide($_POST['id_otroprio']);
    $eq = opc_equ();
    $smbin = null;
    if (($smbina = $_POST['fusers_bina'] ?? null) && is_array($smbina)) {
        $smbin = implode(",", str_replace("'", "", $smbina));
    }
    
    $campos = [
        'idpeople', 'fecha_seg', 'numsegui', 'evento', 'estado_s', 'motivo_estado',
        'encuentra', 'facial', 'corporal', 'respiracion', 'cuidado', 'esparcimiento', 'comunicacion',
        'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
        'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
        'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'redu_riesgo_cierre',
        'users_bina', 'equipo_bina',
        'usu_creo', 'usu_update', 'fecha_update', 'estado'
    ];
    
    $campos_fecha_null = ['fecha_cierre'];

    if (count($id) == 4) { // UPDATE
        $set = [
            'encuentra', 'facial', 'corporal', 'respiracion', 'cuidado', 'esparcimiento', 'comunicacion',
            'estrategia_1', 'estrategia_2', 'acciones_1', 'desc_accion1', 'acciones_2', 'desc_accion2', 'acciones_3', 'desc_accion3',
            'activa_ruta', 'ruta', 'novedades', 'signos_covid', 'caso_afirmativo', 'otras_condiciones', 'observaciones',
            'cierre_caso', 'motivo_cierre', 'fecha_cierre', 'redu_riesgo_cierre',
            'users_bina', 'equipo_bina'
        ];
        $params = [];
        foreach ($set as $campo) {
            if ($campo == 'equipo_bina') {
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
        $sql = "UPDATE vsp_discapacidad SET "
            . implode(' = ?, ', $set) . " = ?, usu_update = ?, fecha_update = NOW() "
            . "WHERE id_otroprio = ?";
        $params[] = ['type' => 's', 'value' => $id[0]]; // id_otroprio
        $rta = mysql_prepd($sql, $params);
    } else if (count($id) == 3) { // INSERT
        $params = [];
        foreach ($campos as $campo) {
            if ($campo == 'idpeople') {
                $params[] = ['type' => 's', 'value' => $id[0]];
            } elseif ($campo == 'equipo_bina') {
                $params[] = ['type' => 's', 'value' => $eq];
            } elseif ($campo == 'usu_creo') {
                $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']];
            } elseif ($campo == 'usu_update' || $campo == 'fecha_update') {
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
        $sql = "INSERT INTO vsp_discapacidad (
            id_otroprio, " . implode(', ', $campos) . "
        ) VALUES (
            NULL, $placeholders
        )";
        $rta = mysql_prepd($sql, $params);
    } else {
        $rta = "Error: id_otroprio inválido";
    }
    return $rta;
}

function get_discapacidad1(){
    if($_REQUEST['id']==''){
      return "";
    }else{
      $id=divide($_REQUEST['id']);
      $sql="SELECT concat_ws('_',id_otroprio,idpeople,numsegui,evento),
      fecha_seg,numsegui,evento,estado_s,motivo_estado,encuentra,facial,corporal,respiracion,cuidado,esparcimiento,comunicacion,estrategia_1,estrategia_2,acciones_1,desc_accion1,acciones_2,desc_accion2,acciones_3,desc_accion3,activa_ruta,ruta,novedades,signos_covid,caso_afirmativo,otras_condiciones,observaciones,cierre_caso,motivo_cierre,fecha_cierre,redu_riesgo_cierre,users_bina
      FROM vsp_discapacidad
      WHERE id_otroprio ='{$id[0]}'";
      $info=datos_mysql($sql);
      return json_encode($info['responseResult'][0]);
    } 
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
function opc_encuentra($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=316 and estado='A' ORDER BY 1",$id);
}
function opc_facial($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=317 and estado='A' ORDER BY 1",$id);
}
function opc_corporal($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=319 and estado='A' ORDER BY 1",$id);
}
function opc_respiracion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=320 and estado='A' ORDER BY 1",$id);
}
function opc_cuidado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=103 and estado='A' ORDER BY 1",$id);
}
function opc_esparcimiento($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=194 and estado='A' ORDER BY 1",$id);
}
function opc_comunicacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=157 and estado='A' ORDER BY 1",$id);
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
// Funciones opc para selects específicos de discapacidad
/* function opc_bina($id=''){
  return opc_sql("SELECT id_usuario, nombre  from usuarios u WHERE equipo=(select equipo from usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A'  ORDER BY 2;",$id);
}
function opc_cuantos_cuidadores($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=91 and estado='A' AND valor =1 ORDER BY 1",$id);
}
function opc_ante_cuidador($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=28 and estado='A' ORDER BY 1",$id);
}
function opc_num_pers($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=91 and estado='A' ORDER BY 1",$id);
}
function opc_cat_ayudastec($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=321 and estado='A' ORDER BY 1",$id);
}
function opc_ayuda_tecnica($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=322 and estado='A' ORDER BY 1",$id);
}



// Funciones opc comunes (ya existentes en acompsic.php)
function opc_motivo_cierre($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=198 and estado='A'  ORDER BY 1 ",$id);
}
function opc_rta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=170 and estado='A'  ORDER BY 1 ",$id);
}
function opc_tipo_doc($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}





function opc_cod_cuidador($id=''){
//  return opc_sql("SELECT p.idpersona,concat_ws(' - ',p.tipo_doc,p.documento,p.nombre1) descripcion FROM person p 
// WHERE p.estado='A' AND p.idpeople IN (SELECT DISTINCT idpeople FROM person WHERE estado='A') ORDER BY p.nombre1",$id);
 
	// var_dump($_REQUEST);
	$idp = trim($_REQUEST['id']);
	$idp=divide($idp);
	$idp = trim((string)$idp[0]);
	// $id= trim((string)$idp);	
		return	opc_sql("SELECT idpeople,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) 'Nombres' 
			from person 
			where vivipersona=(select vivipersona from person where idpeople='$idp') and idpeople<>'$idp'",$id);
}
function opc_cat_ayudastecayuda_tecnica($id=''){
    if($_REQUEST['id']!=''){
        $id=divide($_REQUEST['id']);
        $sql="SELECT idcatadeta ,descripcion  FROM `catadeta` WHERE idcatalogo='322' and estado='A' and valor='".$id[0]."' ORDER BY LENGTH(idcatadeta), idcatadeta;";
        $info=datos_mysql($sql);        
        return json_encode($info['responseResult']);
    }
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
} */

function formato_dato($a,$b,$c,$d){
 $b=strtolower($b);
 $rta=$c[$d];
	if ($a=='discapacidad1' && $b=='acciones'){
		$rta="<nav class='menu right'>";	
		$rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'discapacidad1',event,this,['fecha_seg','numsegui','evento','estado_s','motivo_estado','cierre_caso'],'../vsp/discapacidad1.php');\"></li>";
		$rta.="</nav>";
	}
 return $rta;
}

function bgcolor($a,$c,$f='c'){
  $rta="";
  return $rta;
}
?>