<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
$perf=perfil($_POST['tb']);
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

function cmp_signos(){
	$rta="<div class='encabezado medid'>TABLA DE TOMAS DE MEDIDA</div>
	<div class='contenido' id='signos-lis'>".lis_signos()."</div></div>";
	// $t=['nombres'=>'','fechanacimiento'=>'','edad'=>'','peso'=>'','talla'=>'','imc'=>'','tas'=>'','tad'=>'','glucometria'=>'','perime_braq'=>'','perime_abdom'=>'','percentil'=>'','zscore'=>'','findrisc'=>'','oms'=>'','alert1'=>'','alert2'=>'','alert3'=>'','alert4'=>'','alert5'=>'','alert6'=>'','alert7'=>'','alert8'=>'','alert9'=>'','alert10'=>'','select1'=>'','selmul1'=>'[]','selmul2'=>'[]','selmul3'=>'[]','selmul4'=>'[]','selmul5'=>'[]','selmul6'=>'[]','selmul7'=>'[]','selmul8'=>'[]','selmul9'=>'[]','selmul10'=>'[]','fecha'=>'','tipo'=>''];
	$p=get_persona();
	// if ($d==""){$d=$t;}
	$id=divide($_POST['id']);
	$d='';
    $w="signos";
	$o='infbas';
	$gest = ($p['sexo']=='MUJER' && ($p['ano']>9 && $p['ano']<56 )) ? true : false ;
	$ocu= ($p['ano']>5) ? true : false ;
	$meses = $p['ano'] * 12 + $p['mes'];
	$adul = ($p['ano']>=18) ? true : false;
	$ed=$p['ano'];
	$des='des';
	$z='zS';
	$days=fechas_app('vivienda');
	$c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'',false,false);
	$c[]=new cmp($o,'e',null,'INFORMACION DE signos',$w); 
	$c[]=new cmp('idpersona','t','20',$p['idpersona'],$w.' '.$o,'N° Identificación','idpersona',null,'',true,false,'','col-2');
	$c[]=new cmp('sexo','t','50',$p['sexo'],$w.' '.$z.' '.$o,'sexo','sexo',null,'',false,false,'','col-1');
	$c[]=new cmp('fechanacimiento','d','10',$p['fecha_nacimiento'],$w.' '.$z.' '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-2');
    $c[]=new cmp('edad','n','3',' Años: '.$p['ano'].' Meses: '.$p['mes'].' Dias:'.$p['dia'],$w.' '.$o,'Edad (Abordaje)','edad',null,'',false,false,'','col-2');
	$c[]=new cmp('fecha_toma','d','10',$d,$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-15',"validDate(this,$days,0);");
	

	$o='med';
	$c[]=new cmp($o,'e',null,'TOMA DE SIGNOS Y signos ANTROPOMÉTRICAS',$w);
	$c[]=new cmp('peso','sd',6, $d,$w.' '.$z.' '.$o,'Peso (Kg) Mín=0.50 - Máx=150.00','fpe','rgxpeso','###.##',true,true,'','col-2',"valPeso('peso');Zsco('zscore','signos.php');calImc('peso','talla','imc');");
	$c[]=new cmp('talla','sd',5, $d,$w.' '.$z.' '.$o,'Talla (Cm) Mín=20 - Máx=210','fta','rgxtalla','###.#',true,true,'','col-2',"calImc('peso','talla','imc');Zsco('zscore','signos.php');valTalla('talla');valGluc('glucometria');");
	$c[]=new cmp('imc','t',6, $d,$w.' '.$o,'IMC','imc','','',false,false,'','col-1');
		
	if($p['ano']>=18){
		$c[]=new cmp('tas','n',3, $d,$w.' '.$o,'Tensión Sistolica Mín=60 - Máx=310','tas','rgxsisto','###',true,true,'','col-2',"valSist('tas');");
		$c[]=new cmp('tad','n',3, $d,$w.' '.$o,'Tensión Diastolica Mín=40 - Máx=185','tad','rgxdiast','##',true,true,'','col-2',"ValTensions('tas',this);valDist('tad');");
		$c[]=new cmp('frecard','n',3, $d,$w.' '.$o,'Frecuencia Cardiaca Mín=60 - Máx=120','frecard',null,'##',true,true,'','col-2');
	    $c[]=new cmp('satoxi','n',3, $d,$w.' '.$o,'saturación de Oxigeno Mín=60 - Máx=100','satoxi',null,'##',true,true,'','col-2'); 
        $c[]=new cmp('peri_abdomi','n',4,$d,$w.' AbD '.$o,'Perímetro Abdominal (Cm) Mín=50 - Máx=150','peri_abdomi','rgxperabd','###',$adul,$adul,'','col-3');
		$c[]=new cmp('glucometria','n',4, $d,$w.' gL '.$o,'Glucometría Mín=5 - Máx=600','glu','','###',false,true,'','col-2',"valGluco('glucometria');");
    }
	//var_dump($meses);
    if($meses>= 6 && $meses < 60){
		$c[]=new cmp('peri_braq','sd',4, $d,$w.' '.$o,'Perimetro Braquial (Cm)',0,null,'#,#',true,true,'','col-15');
	}
    if($p['ano']<5){
		$c[]=new cmp('zscore','t',15,'',$w.' '.$o,'Z-score','des',null,null,false,false,'','col-35');
	}
    
	

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
   }

   function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		//  var_dump($id);
		$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,fecha, 
		FN_EDAD(fecha_nacimiento,CURDATE()),
		TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS ano,
    	TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) % 12 AS mes,
    	DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento,INTERVAL TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) MONTH)) AS dia
		from person P left join hog_carac V ON P.vivipersona=V.idfam
		WHERE idpeople='".$id[0]."'";
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
 
   function lis_signos(){
	$id=divide($_POST['id']);
    $total = "SELECT COUNT(*) AS total FROM (
		SELECT S.id_signos AS Cod_Registro, S.peso, S.talla, S.imc, S.zscore, U.nombre AS Colaborador, S.fecha_toma,S.fecha_create Creo ,U.perfil AS Perfil 
		FROM hog_signos S
		LEFT JOIN usuarios U ON S.usu_create = U.id_usuario 
		WHERE S.idpeople = $id[0]
	) AS Subquery";
	$info=datos_mysql($total);
	$total=$info['responseResult'][0]['total']; 
	$regxPag=5;
	
	$pag=(isset($_POST['pag-homes']))? ($_POST['pag-homes']-1)* $regxPag:0;

    $sql="SELECT S.id_signos AS Cod_Registro,S.peso,S.talla,S.imc,S.zscore,U.nombre AS Colaborador,S.fecha_toma,S.fecha_create Creo,U.perfil AS Perfil 
        FROM `hog_signos` S
		LEFT JOIN usuarios U ON S.usu_create = U.id_usuario 
		WHERE idpeople='".$id[0]."' 
		ORDER BY S.fecha_create";
       // echo $sql;
      $datos=datos_mysql($sql);
    return create_table($total,$datos["responseResult"],"homes",$regxPag);
}

function focus_signos(){
	return 'signos';
}
     
function men_signos(){
	$rta=cap_menus('signos','pro');
	return $rta;
}
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = "";
	 $acc=rol($a);
	 if ($a=='signos'  && isset($acc['crear']) && $acc['crear']=='SI'){
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	
	 return $rta;
	 }
   }

	/* function gra_signos(){
		//var_dump($_POST);
		$id=divide($_POST['idp']);
		$tas=$_POST['tas'] ?? null;
		$tad=$_POST['tad'] ?? null;
		$fre=$_POST['frecard']?? null;
		$sat=$_POST['satoxi']?? null;
		$abd=$_POST['peri_abdomi']?? null;
		$bra=$_POST['perime_braq']?? null;
		$zsco=explode("=",$_POST['zscore']?? null);
		$z1=$zsco[0]??null;
		$z2=$zsco[1]??null;
		$glu=$_POST['glucometria'] ?? null;
		

		$sql = "INSERT INTO hog_signos VALUES (NULL,
        trim(upper('{$id[0]}')), 
		trim(upper('{$_POST['fecha_toma']}')),
		trim(upper('{$_POST['peso']}')),
        trim(upper('{$_POST['talla']}')), 
		trim(upper('{$_POST['imc']}')),
        trim(upper('{$tas}')),
		trim(upper('{$tad}')),
        trim(upper('{$fre}')),
		trim(upper('{$sat}')),
        trim(upper('{$abd}')),
        trim(upper('{$bra}')),
		trim(upper('{$z1}')),
        trim(upper('{$z2}')),
		trim(upper('{$glu}')),
		TRIM(UPPER('{$_SESSION['us_sds']}')),
        DATE_SUB(NOW(), INTERVAL 5 HOUR),
		 NULL, NULL, 'A')";
		// echo $sql;
		$rta = dato_mysql($sql);
		return $rta;
   	} */ 
 function gra_signos(){
    $id = divide($_POST['idp']);
    $tas = $_POST['tas'] ?? null;
    $tad = $_POST['tad'] ?? null;
    $fre = $_POST['frecard'] ?? null;
    $sat = $_POST['satoxi'] ?? null;
    $abd = $_POST['peri_abdomi'] ?? null;
    $bra = $_POST['peri_braq'] ?? null;
    $zsco = explode("=", $_POST['zscore'] ?? null);
    $z1 = $zsco[0] ?? null;
    $z2 = $zsco[1] ?? null;
    $glu = $_POST['glucometria'] ?? null;
    $campos = [
        'idpeople', 'fecha_toma', 'peso', 'talla', 'imc', 'tas', 'tad', 'frecard', 'satoxi',
        'peri_abdomi', 'peri_braq','res_zscore', 'zscore', 'glucometria',
        'usu_create', 'fecha_create', 'usu_update', 'fecha_update', 'estado'
    ];
    $params = [
        ['type' => 's', 'value' => $id[0]],
        ['type' => 's', 'value' => $_POST['fecha_toma'] ?? null],
        ['type' => 's', 'value' => $_POST['peso'] ?? null],
        ['type' => 's', 'value' => $_POST['talla'] ?? null],
        ['type' => 's', 'value' => $_POST['imc'] ?? null],
        ['type' => 's', 'value' => $tas],
        ['type' => 's', 'value' => $tad],
        ['type' => 's', 'value' => $fre],
        ['type' => 's', 'value' => $sat],
        ['type' => 's', 'value' => $abd],
        ['type' => 's', 'value' => $bra],
        ['type' => 's', 'value' => $z1],
        ['type' => 's', 'value' => $z2],
        ['type' => 's', 'value' => $glu],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => date('Y-m-d H:i:s')],
        ['type' => 'z', 'value' => null],
        ['type' => 'z', 'value' => null],
        ['type' => 's', 'value' => 'A']
    ];
    $placeholders = implode(', ', array_fill(0, count($params), '?'));
    $sql = "INSERT INTO hog_signos (
        id_signos, " . implode(', ', $campos) . "
    ) VALUES (
        NULL, $placeholders
    )";
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

    function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
/*    var_dump($c);
   var_dump($a);
   var_dump($b);  
		if ($a=='signos' && $b=='acciones'){
			$rta="<nav class='menu right'>";
			// $rta.="<li class='icono editar ' title='Editar Signos' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'caract',event,this,'../crea-caract/lib.php',['fecha','motivoupd','eventoupd','fechanot']);Color('caracteriza-lis');\"></li>";
			// $rta.="<li class='icono editar' title='Editar Información de Facturación' id='".$c['ACCIONES']."' Onclick=\"getData('admision','pro',event,'','lib.php',7);\"></li>"; //setTimeout(hideExpres,1000,'estado_v',['7']);
		}
			*/
   return $rta;
   }
 




function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}
	   
