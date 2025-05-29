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


function focus_uaic_id(){
	return 'uaic_id';
   }
      
   
  function men_uaic_id(){
	$rta=cap_menus('uaic_id','pro');
	return $rta;
   }
   
  
  function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='uaic_id'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	  }
    $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";  
  return $rta;
}


function cmp_uaic_id(){
  // $rta="<div class='encabezado'>TABLA SEGUIMIENTOS</div><div class='contenido' id='uaic_id-lis'>".lis_uaic_id()."</div></div>";
  $rta='';
  $w='uaic_id';
	$t=['iduaic'=>'','idpeople'=>'','fecha_seg'=>'','parentesco'=>'','nombre_cui'=>'','tipo_doc'=>'','num_doc'=>'','telefono'=>'','era'=>'','eda'=>'','dnt'=>'','des_sinto'=>'','aten_medi'=>'','aten_part'=>'','peri_cef'=>'','peri_bra'=>'','peso'=>'','talla'=>'','zcore'=>'','clasi_nut'=>'','tempe'=>'','frec_res'=>'','frec_car'=>'','satu'=>'','sales_reh'=>'','aceta'=>'','traslados_uss'=>'','educa'=>'','menor_hos'=>'','tempe2'=>'','frec_res2'=>'','frec_car2'=>'','satu2'=>'','seg_entmed'=>'','observacion'=>'','clasi_nutri'=>'']; 
	$o='modini';
  $e="";
  $d=get_uaic_id();
  $d=($d=="")?$d=$t:$d;
  $days=fechas_app('etnias');
  $id=$d['iduaic']===''?$_POST['id']:$d['iduaic'];
  $ke=divide($id);
  $up=count($ke)==2? true:false;
  $p=get_persona();
  // var_dump($_POST);
	$c[]=new cmp($o,'e',null,'MODULO INICIAL',$w);
  $c[]=new cmp('fechanacimiento','h','10',$p['fecha_nacimiento'],'zsc','fecha nacimiento','fechanacimiento',null,'',true,false,'','col-2');
  $c[]=new cmp('sexo','h',1,$p['sexo'],'zsc','sexo','sexo',null,'',false,false,'','col-1');

    $c[]=new cmp('iduaic','h',11,$id,$w.' '.$o,'iduaic',null,null,false,false,'','col-2');
    $c[]=new cmp('fecha_seg','d',10,$d['fecha_seg'],$w.' '.$o,'Fecha de Seguimiento','fecha_seg',null,null,true,$up,'','col-25',"validDate(this,$days,0);");
    $c[]=new cmp('parentesco','s',3,$d['parentesco'],$w.' '.$o,'Parentesco','paren',null,null,true,$up,'','col-25',"enabEmbPare('parentesco');");
    $c[]=new cmp('nombre_cui','t',50,$d['nombre_cui'],$w.' prT '.$o,'Nombre Completo del Cuidador','nombre_cui',null,null,true,false,'','col-5');
    $c[]=new cmp('tipo_doc','s',3,$d['tipo_doc'],$w.' prT '.$o,'Tipo De Documento ','tipo_doc',null,null,true,$up,'','col-3');
    $c[]=new cmp('num_doc','n',18,$d['num_doc'],$w.' prT '.$o,'Número De Documento','num_doc',null,null,true,$up,'','col-3');
    $c[]=new cmp('telefono','n',21,$d['telefono'],$w.' prT '.$o,'Teléfono De Contacto','telefono',null,null,true,$up,'','col-4');

    $o='moticons';
    $c[]=new cmp($o,'e',null,'MOTIVO DE LA CONSULTA',$w);
    $c[]=new cmp('era','s',3,$d['era'],$w.' '.$o,'Enfermedad Respiratoria Aguda (Era)','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('eda','s',3,$d['eda'],$w.' '.$o,'Enfermedad Diarreica Aguda (Eda)','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('dnt','s',3,$d['dnt'],$w.' '.$o,'Desnutrición (Dnt)','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('des_sinto','t',80,$d['des_sinto'],$w.' '.$o,'Descripcion De Sintomas','des_sinto',null,null,true,$up,'','col-4');

    $o='sigymed';
    $c[]=new cmp($o,'e',null,'SIGNOS VITALES Y MEDIDAS ANTROPOMETRICAS AL INGRESO DE LA ATENCIÓN',$w);
    $c[]=new cmp('aten_medi','s',3,$d['aten_medi'],$w.' '.$o,'Recibio Atención por Medico Ancestral','rta',null,null,true,$up,'','col-25');
    $c[]=new cmp('aten_part','s',3,$d['aten_part'],$w.' '.$o,'Recibio Atención por Partera','rta',null,null,true,$up,'','col-25');
    

    if($p['mes']<60){
      $c[]=new cmp('peri_cef','sd',4,$d['peri_cef'],$w.' '.$o,'Perimetro Cefalico (Cm)','peri_cefalico',null,null,true,$up,'','col-25');  
    }
    if($p['mes']>= 6 && $p['mes']< 60){
      $c[]=new cmp('peri_bra','sd',4,$d['peri_bra'],$w.' '.$o,'Perimetro Braquial  (Cm)','peri_braqueal',null,null,true,$up,'','col-25');
    }
    $c[]=new cmp('peso','sd',5,$d['peso'],$w.' '.$o,'Peso (Kg)','peso','rgxpeso',null,true,$up,'','col-2',"Zsco('zscore','../etnias/uaicid.php');");
    $c[]=new cmp('talla','sd',4,$d['talla'],$w.' '.$o,'Talla (Cm)','talla','rgxtalla',null,true,$up,'','col-2',"Zsco('zscore','../etnias/uaicid.php');");
    if($p['mes']<60){
      $c[]=new cmp('zscore','t',50,$d['zcore'],$w.' '.$o,'Zcore','zscore',null,null,false,false,'','col-35');
    }
    $c[]=new cmp('clasi_nut','s',3,$d['clasi_nut'],$w.' '.$o,'Clasificación Nutricional','clasi_nutri',null,null,true,$up,'','col-35');
  
    $c[]=new cmp('tempe','sd',3,$d['tempe'],$w.' '.$o,'Temperatura','tempe','rgxtemp',null,true,$up,'','col-2');
    $c[]=new cmp('frec_res','sd',3,$d['frec_res'],$w.' '.$o,'Frecuencia Respiratoria (15 a 60 x min)','frec_res','rgxfresp',null,true,$up,'','col-2');
    $c[]=new cmp('frec_car','sd',3,$d['frec_car'],$w.' '.$o,'Frecuencia Cardiaca (50 a 150 lt)','frec_car','rgxfcard',null,true,$up,'','col-2');
    $c[]=new cmp('satu','sd',3,$d['satu'],$w.' '.$o,'Saturación (40 a 99 %)','satu','rgxsatu',null,true,$up,'','col-2');

    
    $o='mane';
    $c[]=new cmp($o,'e',null,'MANEJO',$w);
    $c[]=new cmp('sales_reh','s',3,$d['sales_reh'],$w.' '.$o,'Sales De Rehidratación','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('aceta','s',3,$d['aceta'],$w.' '.$o,'Acetaminofen','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('traslados_uss','s',3,$d['traslados_uss'],$w.' '.$o,'Traslados de Uss','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('educa','s',3,$d['educa'],$w.' '.$o,'Educación','rta',null,null,true,$up,'','col-2');
    $c[]=new cmp('menor_hos','s',3,$d['menor_hos'],$w.' '.$o,'Menor Hospitalizado','rta',null,null,true,$up,'','col-2');

    $o='sigyman2';
    $c[]=new cmp($o,'e',null,'SIGNOS VITALES POSTERIOR AL PLAN DE MANEJO EN LA UAIC',$w);
    $c[]=new cmp('tempe2','sd',50,$d['tempe2'],$w.' '.$o,'Temperatura','tempe2',null,null,true,$up,'','col-2');
    $c[]=new cmp('frec_res2','sd',3,$d['frec_res2'],$w.' '.$o,'Frecuencia Respiratoria (15 a 60 x min)','frec_res2',null,null,true,$up,'','col-2');
    $c[]=new cmp('frec_car2','sd',60,$d['frec_car2'],$w.' '.$o,'Frecuencia Cardiaca (50 a 150 lt)','frec_car2',null,null,true,$up,'','col-2');
    $c[]=new cmp('satu2','sd',7,$d['satu2'],$w.' '.$o,'Saturación (40 a 99 %)','satu2',null,null,true,$up,'','col-2');
    $c[]=new cmp('seg_entmed','a',1,$d['seg_entmed'],$w.' '.$o,'Seguimiento A Entrega De Medicamentos','seg_entmed',null,null,false,$up,'','col-8');

    $o='aspe';
    $c[]=new cmp($o,'e',null,'ASPECTOS FINALES',$w);
    $c[]=new cmp('observacion','a',7,$d['observacion'],$w.' '.$o,'Observacion','observacion',null,null,true,true,'','col-10');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		$sql="SELECT sexo,fecha_nacimiento,TIMESTAMPDIFF(MONTH,fecha_nacimiento ,CURDATE() )  AS mes
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
	
			return json_encode($z." = ".$des);
		}
	}

function gra_uaic_id(){
	$id = divide($_POST['iduaic']);
  $usu = $_SESSION['us_sds'];
  if (($rtaFec = validFecha('etnias', $_POST['fecha_seg'] ?? '')) !== true) {return $rtaFec;}
    if(COUNT($id)==2){
      $equ=datos_mysql("select equipo from usuarios where id_usuario='".$_SESSION['us_sds']."'");
      $bina = isset($_POST['fequi'])?(is_array($_POST['fequi'])?implode("-", $_POST['fequi']):implode("-",array_map('trim',explode(",",str_replace("'","",$_POST['fequi']))))):'';
      $equi=$equ['responseResult'][0]['equipo'];
      $sql = "INSERT INTO uaic_ide VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,'A');";
      $params = [
['type' => 'i', 'value' => $id[0]],
['type' => 's', 'value' => $_POST['fecha_seg']??null],
['type' => 's', 'value' => $_POST['parentesco']??null],
['type' => 's', 'value' => $_POST['nombre_cui']??null],
['type' => 's', 'value' => $_POST['tipo_doc']??null],
['type' => 'i', 'value' => $_POST['num_doc']??null],
['type' => 'i', 'value' => $_POST['telefono']??null],
['type' => 's', 'value' => $_POST['era']??null],
['type' => 's', 'value' => $_POST['eda']??null],
['type' => 's', 'value' => $_POST['dnt']??null],
['type' => 's', 'value' => $_POST['des_sinto']??null],
['type' => 's', 'value' => $_POST['aten_medi']??null],
['type' => 's', 'value' => $_POST['aten_part']??null],
['type' => 's', 'value' => $_POST['peri_cef']??null],
['type' => 's', 'value' => $_POST['peri_bra']??null],
['type' => 's', 'value' => $_POST['peso']??null],
['type' => 's', 'value' => $_POST['talla']??null],
['type' => 's', 'value' => $_POST['zscore']??null],
['type' => 's', 'value' => $_POST['clasi_nut']??null],
['type' => 's', 'value' => $_POST['tempe']??null],
['type' => 's', 'value' => $_POST['frec_res']??null],
['type' => 's', 'value' => $_POST['frec_car']??null],
['type' => 's', 'value' => $_POST['satu']??null],
['type' => 's', 'value' => $_POST['sales_reh']??null],
['type' => 's', 'value' => $_POST['aceta']??null],
['type' => 's', 'value' => $_POST['traslados_uss']??null],
['type' => 's', 'value' => $_POST['educa']??null],
['type' => 's', 'value' => $_POST['menor_hos']??null],
['type' => 's', 'value' => $_POST['tempe2']??null],
['type' => 's', 'value' => $_POST['frec_res2']??null],
['type' => 's', 'value' => $_POST['frec_car2']??null],
['type' => 's', 'value' => $_POST['satu2']??null],
['type' => 's', 'value' => $_POST['seg_entmed']??null],
['type' => 's', 'value' => $_POST['observacion']??null],
['type' => 's', 'value' => $bina],
['type' => 's', 'value' => $equi],
['type' => 's', 'value' => $_SESSION['us_sds']],
['type' => 's', 'value' => NULL],
['type' => 's', 'value' => NULL]
      ];

    //  $rta = show_sql($sql, $params);
    $rta = mysql_prepd($sql, $params);
    }else{
   $sql="UPDATE uaic_ide SET observacion=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR), usu_update=? WHERE iduaic=?";
    $params = [
        ['type' => 's', 'value' => $_POST['observacion']],
        // ['type' => 's', 'value' => date("Y-m-d H:i:s")],
        ['type' => 'i', 'value' => $_SESSION['us_sds']],
        ['type' => 'i', 'value' => $id[0]]//IDACORDE AL NUMERO DEL INDICE
      ];
      //  $rta = show_sql($sql, $params);
     $rta = mysql_prepd($sql, $params);
    }
return $rta;
}

function get_uaic_id(){
  if($_POST['id']==''){
    return "";
  }else{
    // print_r($_POST);
    $id=divide($_POST['id']);
    // print_r($id);
    $sql="SELECT iduaic,fecha_seg,parentesco,nombre_cui,tipo_doc,num_doc,telefono,era,eda,dnt,des_sinto,aten_medi,aten_part,peri_cef,peri_bra,peso,talla,zcore,clasi_nut,tempe,frec_res,frec_car,satu,sales_reh,aceta,traslados_uss,educa,menor_hos,tempe2,frec_res2,frec_car2,satu2,seg_entmed,observacion
          FROM `uaic_ide` 
          WHERE idpeople='{$id[0]}'";
    $info=datos_mysql($sql);
    if (!$info['responseResult']) {
			return '';
		}else{
			return $info['responseResult'][0];
		}
      }
}

function opc_paren($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=263 and estado='A' ORDER BY 1",$id);
  }

  function opc_tipo_doc($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
  }

  function opc_rta($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
  }

  function opc_clasi_nutri($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=98 and estado='A' ORDER BY 1",$id);
    }

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
        // var_dump($a);
		if ($a=='uaic_id' && $b=='acciones'){
			$rta="<nav class='menu right'>";
				
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }