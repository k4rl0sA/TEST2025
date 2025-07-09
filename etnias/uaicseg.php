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

function focus_uaic_seg(){
	return 'uaic_seg';
}
   
  function men_uaic_seg(){
	$rta=cap_menus('uaic_seg','pro');
	return $rta;
   }
   
function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='uaic_seg'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	  }
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";  
  return $rta;
}

function lis_uaic_seg(){
    // print_r($_POST);
$id = (isset($_POST['id'])) ? divide($_POST['id']) : (isset($_POST['iduaicseg']) ? divide($_POST['iduaicseg']) : null);
$info=datos_mysql("SELECT COUNT(*) total FROM vsp_mme A LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario 
  WHERE A.estado = 'A' AND A.idpeople='".$id[0]."'");
$total=$info['responseResult'][0]['total'];
$regxPag=5;
$pag=(isset($_POST['pag-uaic_seg']))? ($_POST['pag-uaic_seg']-1)* $regxPag:0;

    $sql="SELECT us.iduaicseg AS ACCIONES, us.iduaicseg 'Cod_Registro', us.fecha_seg 'Fecha', us.segui 'N Seguimiento', FN_CATALOGODESC(73,us.estado_seg)'Estado', u.nombre 
FROM uaic_seg us 
left join usuarios u ON us.usu_creo = u.id_usuario 
            WHERE idpeople='".$id[0];
        $sql.="' ORDER BY fecha_create";
        $sql.=' LIMIT '.$pag.','.$regxPag;
        //  echo $sql;
        $datos=datos_mysql($sql);
        return create_table($total,$datos["responseResult"],"uaic_seg",$regxPag,'uaicseg.php');
}

function cmp_uaic_seg(){
  $rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div><div class='contenido' id='uaic_seg-lis'>".lis_uaic_seg()."</div></div>";
  $w='uaic_seg';
	$t=['iduaicseg'=>'','idpeople'=>'','fecha_seg'=>'','segui'=>'','estado_seg'=>'','motivo_seg'=>'','at_medi'=>'','at_part'=>'','peso'=>'','talla'=>'','zcore'=>'','clasi_nutri'=>'','ftlc_apme'=>'','cual'=>'','cita_nutri7'=>'','cita_nutri15'=>'','cita_nutri30'=>'','observaciones'=>''];
	$o='modini';
  $ob='Ob';
  $no='nO';
  $bl='bL';
  $x=false;
  $d=get_uaic_seg();
  $d='';
  $d=($d=="")?$d=$t:$d;
  $days=fechas_app('etnias');
  $p=get_persona();
	  $c[]=new cmp($o,'e',null,'MODULO INICIAL',$w);
    $c[]=new cmp('fechanacimiento','h','10',$p['fecha_nacimiento'],'zsc','fecha nacimiento','fechanacimiento',null,'',true,false,'','col-2');
    $c[]=new cmp('sexo','h',1,$p['sexo'],'zsc','sexo','sexo',null,'',false,false,'','col-1');
    
    $c[]=new cmp('iduaicseg','h',11,$_POST['id'],$w.' '.$o,'Iduaicseg','iduaicseg',null,null,true,true,'','col-2');
    $c[]=new cmp('fecha_seg','d',10,$d['fecha_seg'],$w.' '.$o,'Fecha Seguimiento','fecha_seg',null,null,true,true,'','col-25',"validDate(this,$days,0);");
    $c[]=new cmp('segui','s',3,$d['segui'],$w.' '.$o,'Seguimiento N°','segui',null,null,true,true,'','col-25',"staEfe('segui','sta');EnabEfec(this,['segdnt','aspe'],['Ob'],['nO'],['bL'])");
    $c[]=new cmp('estado_seg','s',3,$d['estado_seg'],$w.' sTa '.$o,'Estado de Seguimiento','estado_seg',null,null,true,true,'','col-25',"enabFielSele(this,true,['motivo_seg'],['3']);EnabEfec(this,['segdnt','aspe'],['Ob'],['nO'],['bL']);");
    $c[]=new cmp('motivo_seg','s',3,$d['motivo_seg'],$w.' '.$o,'Motivo de Seguimiento','motivo_seg',null,null,false,$x,'','col-25');
    
    $o='segdnt';
    $c[]=new cmp($o,'e',null,'SEGUIMIENTO MENORES CON  DNT',$w);
    $c[]=new cmp('at_medi','s',3,$d['at_medi'],$w.' '.$o,'Recibio Atención por Medico Ancestral','rta',null,null,true,true,'','col-25');
    $c[]=new cmp('at_part','s',3,$d['at_part'],$w.' '.$o,'Recibio Atención por Partera','rta',null,null,true,true,'','col-25');
    $c[]=new cmp('peso','sd',5,$d['peso'],$w.' '.$o,'Peso (Kg)','peso','rgxpeso',null,true,true,'','col-2',"Zsco('zscore','../etnias/uaicseg.php');");
    $c[]=new cmp('talla','sd',4,$d['talla'],$w.' '.$o,'Talla (Cm)','talla','rgxtalla',null,true,true,'','col-2',"Zsco('zscore','../etnias/uaicseg.php');");
    // $men = (p['ano']<5) ? true : false;
    $c[]=new cmp('zscore','t',50,$d['zcore'],$w,'Zcore','zscore',null,null,false,false'','col-35');
    $c[]=new cmp('clasi_nutri','s',3,$d['clasi_nutri'],$w.' '.$o,'Clasificacion Nutricional','clasi_nutri',null,null,true,true,'','col-2');
    $c[]=new cmp('ftlc_apme','s',3,$d['ftlc_apme'],$w.' '.$o,'Tiene Ftlc U Otro Apme (Cual)','rta',null,null,true,true,'','col-2','ftlc();');
    $c[]=new cmp('cual','t',50,$d['cual'],$w.' '.$bl.' Ftl ','Cual','cual',null,null,false,true,'','col-3');
    $c[]=new cmp('cita_nutri7','s',3,$d['cita_nutri7'],$w.' '.$o,'Cita Con Nutricion O Pediatria A Los 7 Dias','rta',null,null,true,true,'','col-2');
    $c[]=new cmp('cita_nutri15','s',3,$d['cita_nutri15'],$w.' '.$o,'Cita Con Nutricion O Pediatria A Los 15 Dias','rta',null,null,true,true,'','col-25');
    $c[]=new cmp('cita_nutri30','s',5,$d['cita_nutri30'],$w.' '.$o,'Cita Con Nutricion O Pediatria A Los 30 Dias','rta',null,null,true,true,'','col-25');
    
    $o='aspe';
    $c[]=new cmp($o,'e',null,'ASPECTOS FINALES',$w);
    $c[]=new cmp('observaciones','a',7000,$d['observaciones'],$w.' '.$ob.' '.$o,'Observaciones','observaciones',null,null,true,true,'','col-10');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		$sql="SELECT FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE() ) AS ano,
  		TIMESTAMPDIFF(MONTH,fecha_nacimiento ,CURDATE() ) % 12 AS mes,
		DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento,INTERVAL TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) MONTH)) AS dia
		from person P WHERE P.idpeople='".$id[0]."'";
		// echo $sql;
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}else{
			return $info['responseResult'][0];
		}
		}
	}

  function get_zscore(){
		// var_dump($_POST);
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
	//   echo $sql;
	 $info=datos_mysql($sql);
		 if (!$info['responseResult']) {
			return '';
		}else{
			$z=number_format((float)$info['responseResult'][0]['rta'], 6, '.', '');
			switch ($z) {
				case ($z <=-3):
					$des='DESNUTRICIÓN AGUDA SEVERA';
					break;
				case ($z >-3 && $z <=-2):
					$des='DESNUTRICIÓN AGUDA MODERADA';
					break;
				case ($z >-2 && $z <=-1):
					$des='RIESGO DESNUTRICIÓN AGUDA';
					break;
				case ($z>-1 && $z <=1):
						$des='PESO ADECUADO PARA LA TALLA';
					break;
				case ($z >1 && $z <=2):
						$des='RIESGO DE SOBREPESO';
					break;
				case ($z >2 && $z <=3):
						$des='SOBREPESO';
					break;
					case ($z >3):
						$des='OBESIDAD';
					break;
				default:
					$des='Error en el rango, por favor valide';
					break;
			}
	      // var_dump($z." = ".$des);
			return json_encode($z." = ".$des);
		}
	}

function gra_uaic_seg(){
	$id=divide($_POST['iduaicseg']);
  if (($rtaFec = validFecha('ETNIAS', $_POST['fecha_seg'] ?? '')) !== true) {return $rtaFec;}
    if(COUNT($id)==2){
      $equ=datos_mysql("select equipo from usuarios where id_usuario=".$_SESSION['us_sds']);
      $bina = isset($_POST['fequi'])?(is_array($_POST['fequi'])?implode("-", $_POST['fequi']):implode("-",array_map('trim',explode(",",str_replace("'","",$_POST['fequi']))))):'';
      $equi=$equ['responseResult'][0]['equipo'];
      $sql = "INSERT INTO uaic_seg VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),NULL,NULL,'A');";
      $params = [
        ['type' => 'i', 'value' => $id[0]],
        ['type' => 's', 'value' => $_POST['fecha_seg']],
        ['type' => 's', 'value' => $_POST['segui']],
        ['type' => 's', 'value' => $_POST['estado_seg']],
        ['type' => 's', 'value' => $_POST['motivo_seg']],
        ['type' => 's', 'value' => $_POST['at_medi']??''],
        ['type' => 's', 'value' => $_POST['at_part']??''],
        ['type' => 'i', 'value' => $_POST['peso']],
        ['type' => 'i', 'value' => $_POST['talla']],
        ['type' => empty($_POST['zscore']) ? 'z' : 's', 'value' => empty($_POST['zscore']) ? null : $_POST['zscore']],
        ['type' => 's', 'value' => $_POST['clasi_nutri']],
        ['type' => 's', 'value' => $_POST['ftlc_apme']],
        ['type' => 's', 'value' => $_POST['cual']],
        ['type' => 's', 'value' => $_POST['cita_nutri7']],
        ['type' => 's', 'value' => $_POST['cita_nutri15']],
        ['type' => 's', 'value' => $_POST['cita_nutri30']],
        ['type' => 's', 'value' => $_POST['observaciones']],
        ['type' => 's', 'value' => $bina],
        ['type' => 's', 'value' => $equi],
        ['type' => 's', 'value' => $_SESSION['us_sds']]
      ];
// var_dump($sql);
      //$rta = show_sql($sql, $params);
   $rta = mysql_prepd($sql, $params);
    }else{
   $sql="UPDATE uaic_seg SET observaciones=?,fecha_update=DATE_SUB(NOW(),INTERVAL 5 HOUR),usu_update=? WHERE iduaicseg=?"; //  compromiso=?, equipo=?, 
    $params = [
        ['type' => 's', 'value' => $_POST['observaciones']],
        ['type' => 'i', 'value' => $_SESSION['us_sds']],
        ['type' => 'i', 'value' => $id[0]]
      ];
      $rta = mysql_prepd($sql, $params);
    }
return $rta;
}

function get_uaic_seg(){
  if($_REQUEST['id']==''){
    return "";
  }else{
    // print_r($_POST);
    $id=divide($_REQUEST['id']);
    // print_r($id);
    $sql="SELECT P.fecha_nacimiento,P.sexo,iduaicseg, fecha_seg,segui,estado_seg,motivo_seg,at_medi,at_part,peso,talla,zcore,clasi_nutri,ftlc_apme,cual,cita_nutri7,cita_nutri15,cita_nutri30,observaciones
          FROM `uaic_seg` S
           left join person P ON S.idpeople=P.idpeople
          WHERE iduaicseg='{$id[0]}'";
    $info=datos_mysql($sql);
    if (!empty($info['responseResult'])) return json_encode($info['responseResult'][0]);
}
}

  function opc_rta($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
  }

  function opc_clasi_nutri($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=98 and estado='A' ORDER BY 1",$id);
    }

  function opc_segui($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
    }
      
  function opc_estado_seg($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=73 and estado='A' ORDER BY 1",$id);
    }
      
  function opc_motivo_seg($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=265 and estado='A' ORDER BY 1",$id);
  }

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
        // var_dump($a);
		if ($a=='uaic_seg' && $b=='acciones'){
			$rta="<nav class='menu right'>";
      $rta.="<li class='icono editar' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'uaic_seg',event,this,['fecha_seg','at_medi','at_part','peso','talla','clasi_nutri','ftlc_apme','cual','cita_nutri7','cita_nutri15','cita_nutri30'],'../etnias/uaicseg.php');enbValue('iduaicseg','modini','".$c['ACCIONES']."');enaFie(document.getElementById('observaciones'),false);\"></li>";	
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }