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



function focus_alertas(){
	return 'alertas';
}
   
   
function men_alertas(){
	$rta=cap_menus('alertas','pro');
	return $rta;
}
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = "";
	 $acc=rol($a);
	 if ($a=='alertas'  && isset($acc['crear']) && $acc['crear']=='SI'){
	 	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 return $rta;
	 }
   }

   function lis_alertas(){
		// var_dump($_POST['id']);
		$id=divide($_POST['id']);
		$sql="SELECT id_alert ACCIONES,id_alert AS Cod_Registro,`fecha`,FN_CATALOGODESC(34,tipo) Tipo,`nombre` Creó,`fecha_create` 'fecha Creó'
		FROM hog_alert P
		LEFT JOIN  usuarios U ON P.usu_creo=U.id_usuario ";
		$sql.="WHERE idpeople='".$id[0]."";
		$sql.="' ORDER BY fecha_create";
		// echo $sql;
		$datos=datos_mysql($sql);
		return panel_content($datos["responseResult"],"alertas-lis",5);
   }
 
   function cmp_alertas(){
	$rta="<div class='encabezado medid'>TABLA DE ALERTAS</div>
	<div class='contenido' id='alertas-lis'>".lis_alertas()."</div></div>";
	// $t=['nombres'=>'','fechanacimiento'=>'','edad'=>'','peso'=>'','talla'=>'','imc'=>'','tas'=>'','tad'=>'','glucometria'=>'','perime_braq'=>'','perime_abdom'=>'','percentil'=>'','zscore'=>'','findrisc'=>'','oms'=>'','alert1'=>'','alert2'=>'','alert3'=>'','alert4'=>'','alert5'=>'','alert6'=>'','alert7'=>'','alert8'=>'','alert9'=>'','alert10'=>'','select1'=>'','selmul1'=>'[]','selmul2'=>'[]','selmul3'=>'[]','selmul4'=>'[]','selmul5'=>'[]','selmul6'=>'[]','selmul7'=>'[]','selmul8'=>'[]','selmul9'=>'[]','selmul10'=>'[]','fecha'=>'','tipo'=>''];
	$p=get_persona();
	// if ($d==""){$d=$t;}
	// var_dump($_POST);
	$id=divide($_POST['id']);
	$d='';
    $w="alertas";
	$o='infbas';
	// var_dump($p);
	$gest = ($p['sexo']=='MUJER' && ($p['ano']>9 && $p['ano']<56 )) ? true : false ;
	$ocu= ($p['ano']>5) ? true : false ;
	$meses = $p['ano'] * 12 + $p['mes'];
	// $esc=($p['ano']>=5 && $p['ano']<18 ) ? true : false ;
	$ed=$p['ano'];
	switch (true) {
			case $ed>=0 && $ed<=5 :
				$curso=1;
				break;
			case $ed>=6 && $ed<=11 :
				$curso=2;
				break;
			case $ed>=12 && $ed <=17 :
				$curso=3;
				break;
			case $ed>=18 && $ed <=28 :
				$curso=4;
				break;
			case $ed>=29 && $ed <=59 :
				$curso=5;
				break;
			case $ed>=60 :
				$curso=6;
				break;
		default:
			$curso='';
			break;
	}

	$des='des';
	$z='zS';
	$days=fechas_app('vivienda');
	$c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'',false,false);
	$c[]=new cmp($o,'e',null,'INFORMACION DE alertas',$w); 
	$c[]=new cmp('idpersona','t','20',$p['idpersona'],$w.' '.$o,'N° Identificación','idpersona',null,'',true,false,'','col-1');
	$c[]=new cmp('tipodoc','t','3',$p['tipo_doc'],$w.' '.$o,'Tipo Identificación','tipodoc',null,'',true,false,'','col-1');
	$c[]=new cmp('nombre','t','50',$p['nombres'],$w.' '.$o,'nombres','nombre',null,'',true,false,'','col-3');
	$c[]=new cmp('sexo','t','50',$p['sexo'],$w.' '.$z.' '.$o,'sexo','sexo',null,'',false,false,'','col-1');
	$c[]=new cmp('fechanacimiento','d','10',$p['fecha_nacimiento'],$w.' '.$z.' '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-15');
    $c[]=new cmp('edad','n','3',' Años: '.$p['ano'].' Meses: '.$p['mes'].' Dias:'.$p['dia'],$w.' '.$o,'Edad (Abordaje)','edad',null,'',false,false,'','col-25');
	$c[]=new cmp('cursovida','s','3',$curso,$w.' '.$o,'Curso de Vida','cursovida',null,'',false,false,'','col-25');
	$c[]=new cmp('fecha','d','10',$d,$w.' '.$o,'fecha de la Toma','fecha',null,'',true,true,'','col-15',"validDate(this,$days,0);");
	$c[]=new cmp('tipo','s','3',$d,$w.' '.$o,'Tipo','complemento',null,'',true,true,'','col-15');
	$c[]=new cmp('crit_epi','s','3',$d,$w.' '.$o,'Criterio Epidemiológico','crit_epi',null,true,true,true,'','col-35');
	
	$o='infcom';
	$c[]=new cmp($o,'e',null,'DATOS COMPLEMENTARIOS',$w);
	
	$men5 = ($p['ano']<5) ? true : false ;
		$c[]=new cmp('men_dnt','s','2',$d,$w.' '.$o,'Menor de 5 años con DNT Aguda','rta',null,null,$men5,$men5,'','col-15', "fieldsValue('men_dnt','dNt','1',true);");
		$c[]=new cmp('men_sinctrl','s','2',$d,$w.' dNt '.$o,'Sin Atencion Ruta Alteracion Nutricional','rta',null,null,$men5,$men5,'','col-15');

	$gesta = ($p['sexo']=='MUJER') ? true : false ;
		$c[]=new cmp('gestante','s','2',$d,$w.' '.$o,'El usuario es gestante','rta',null,null,$gest,$gesta,'','col-2',"fieldsValue('gestante','eTp','1',true);");
		$c[]=new cmp('etapgest','s','3',$d,$w.' eTp '.$o,'Etapa Gestacional','etapgest',null,'',$gest,false,'','col-25');//true
		$c[]=new cmp('ges_sinctrl','s','3',$d,$w.' eTp '.$o,'Gestante Sin Control','rta',null,'',$gest,false,'','col-25');//true

	$c[]=new cmp('cronico','s','2',$d,$w.' '.$o,'El usuario es cronico','rta',null,null,true,true,'','col-2',"fieldsValue('cronico','cRo','1',true);");
	$c[]=new cmp('cro_hiper','s','2',$d,$w.' cRo '.$o,'Hipertension','rta',null,null,true,false,'','col-2');
	$c[]=new cmp('cro_diabe','s','2',$d,$w.' cRo '.$o,'Diabetes','rta',null,null,true,false,'','col-2');
	$c[]=new cmp('cro_epoc','s','2',$d,$w.' cRo '.$o,'Epoc','rta',null,null,true,false,'','col-2');
	$c[]=new cmp('cro_sinctrl','s','2',$d,$w.' cRo '.$o,'Cronico Sin Control','rta',null,null,true,false,'','col-2');
	$c[]=new cmp('esq_vacun','s','2',$d,$w.' '.$o,'Esquema de Vacunacion Completo','rta',null,null,true,true,'','col-2');
	
	$o='alert';
	$c[]=new cmp($o,'e',null,'ALERTAS',$w); 
	$c[]=new cmp('alert1','s',15,$d,$w.' '.$o,'Alerta N° 1','alert',null,null,true,true,'','col-1',"enabAlert(this,'cRoN');",['fselmul1'],false,'alertas.php');
	$c[]=new cmp('selmul1','m',3,$d,$w.' cRoN '.$o,'Descripcion Alerta N° 1','selmul1',null,'',false,false,'','col-4');
	$c[]=new cmp('alert2','s',15,$d,$w.' '.$o,'Alerta N° 2','alert',null,null,false,true,'','col-1',"enabAlert(this,'etv');",['fselmul2'],false,'alertas.php');
	$c[]=new cmp('selmul2','m',3,$d,$w.' etv '.$o,'Descripcion Alerta N° 2','selmul2',null,'',false,false,'','col-4');
	$c[]=new cmp('alert3','s',15,$d,$w.' '.$o,'Alerta N° 3','alert',null,null,false,true,'','col-1',"enabAlert(this,'nut');",['fselmul3'],false,'alertas.php');
	$c[]=new cmp('selmul3','m',3,$d,$w.' nut '.$o,'Descripcion Alerta N° 3','selmul3',null,'',false,false,'','col-4');
	$c[]=new cmp('alert4','s',15,$d,$w.' '.$o,'Alerta N° 4','alert',null,null,false,true,'','col-1',"enabAlert(this,'psi');",['fselmul4'],false,'alertas.php');
	$c[]=new cmp('selmul4','m',3,$d,$w.' psi '.$o,'Descripcion Alerta N° 4','selmul4',null,'',false,false,'','col-4');
	$c[]=new cmp('alert5','s',15,$d,$w.' '.$o,'Alerta N° 5','alert',null,null,false,true,'','col-1',"enabAlert(this,'inf');",['fselmul5'],false,'alertas.php');
	$c[]=new cmp('selmul5','m',3,$d,$w.' inf '.$o,'Descripcigon Alerta N° 5','selmul5',null,'',false,false,'','col-4');
	$c[]=new cmp('alert6','s',15,$d,$w.' '.$o,'Alerta N° 6','alert',null,null,false,true,'','col-1',"enabAlert(this,'muj');",['fselmul6'],false,'alertas.php');
	$c[]=new cmp('selmul6','m',3,$d,$w.' muj '.$o,'Descripcion Alerta N° 6','selmul6',null,'',false,false,'','col-4');
	
	$c[]=new cmp('agen_intra','s',15,$d,$w.' '.$o,'Agendamiento Intramural','rta',null,null,true,true,'','col-1',"fieldsValue('agen_intra','aIM','1',true);");
	$c[]=new cmp('servicio','t',15,$d,$w.' aIM '.$o,'Servicio Agendado','servicio',null,null,false,false,'','col-15');
	$c[]=new cmp('fecha_cita','d','10',$d,$w.' aIM '.$o,'Fecha de la Cita','fecha_cita',null,'',false,false,'','col-15',"validDate(this,0,60);");
	$c[]=new cmp('hora_cita','c','10',$d,$w.' aIM '.$o,'Hora de la Cita','hora_cita',null,'',false,false,'','col-15');
	$c[]=new cmp('lugar_cita','t',15,$d,$w.' aIM '.$o,'Lugar de la Cita','lugar_cita',null,null,false,false,'','col-15');
	
	$c[]=new cmp('deriva_pf','s',15,$d,$w.' '.$o,'Deriva a PCF','rta',null,null,true,true,'','col-1',"enabOthSi('deriva_pf','pCf');");
	$c[]=new cmp('evento_pf','s',15,$d,$w.' pCf '.$o,'Asigna a PCF','evento',null,null,false,false,'','col-5');
	// $c[]=new cmp('medico','s',15,$d,$w.' der '.$o,'Asignado','medico',null,null,false,false,'','col-5');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
   }


   
   
   function get_persona(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,fecha, 
		FN_EDAD(fecha_nacimiento,CURDATE()),
		TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE() ) AS ano,
  		TIMESTAMPDIFF(MONTH,fecha_nacimiento ,CURDATE() ) % 12 AS mes,
		DATEDIFF(CURDATE(), DATE_ADD(fecha_nacimiento,INTERVAL TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE()) MONTH)) AS dia
		from person P left join hog_carac V ON vivipersona=idfam
		WHERE P.idpeople='".$id[0]."'";
		// echo $sql;
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}else{
			return $info['responseResult'][0];
		}
		}
	} 


/* function gra_alertas(){
		
	$sm1 = isset($_POST['fselmul1']) ? str_replace(["'", '"'], '', $_POST['fselmul1']) : '';
    $sm2 = isset($_POST['fselmul2']) ? str_replace(["'", '"'], '', $_POST['fselmul2']) : '';
    $sm3 = isset($_POST['fselmul3']) ? str_replace(["'", '"'], '', $_POST['fselmul3']) : '';
    $sm4 = isset($_POST['fselmul4']) ? str_replace(["'", '"'], '', $_POST['fselmul4']) : '';
    $sm5 = isset($_POST['fselmul5']) ? str_replace(["'", '"'], '', $_POST['fselmul5']) : '';
    $sm6 = isset($_POST['fselmul6']) ? str_replace(["'", '"'], '', $_POST['fselmul6']) : '';
    
    $dnt = $_POST['men_dnt'] ?? null;
    $dnt_sinctrl = $_POST['men_sinctrl'] ?? null;
    $gest = $_POST['gestante'] ?? null;
    $etapa = $_POST['etapgest'] ?? null;
    $ges_sinctrl = $_POST['ges_sinctrl'] ?? null;

    $id = divide($_POST['idp']);

    // Construir la consulta SQL
    $sql = "INSERT INTO hog_alert VALUES (NULL,
        trim(upper('{$id[0]}')), trim(upper('{$_POST['cursovida']}')), trim(upper('{$_POST['fecha']}')),
        trim(upper('{$_POST['tipo']}')), trim(upper('{$_POST['crit_epi']}')),
        trim(upper('{$dnt}')), trim(upper('{$dnt_sinctrl}')),
        trim(upper('{$gest}')), trim(upper('{$etapa}')),
        trim(upper('{$ges_sinctrl}')),
        trim(upper('{$_POST['cronico']}')), trim(upper('{$_POST['cro_hiper']}')),
        trim(upper('{$_POST['cro_diabe']}')), trim(upper('{$_POST['cro_epoc']}')),
        trim(upper('{$_POST['cro_sinctrl']}')), trim(upper('{$_POST['esq_vacun']}')),
        trim(upper('{$_POST['alert1']}')), trim(upper('{$sm1}')), trim(upper('{$_POST['alert2']}')), trim(upper('{$sm2}')),
        trim(upper('{$_POST['alert3']}')), trim(upper('{$sm3}')), trim(upper('{$_POST['alert4']}')), trim(upper('{$sm4}')),
        trim(upper('{$_POST['alert5']}')), trim(upper('{$sm5}')), trim(upper('{$_POST['alert6']}')), trim(upper('{$sm6}')),
        trim(upper('{$_POST['agen_intra']}')), trim(upper('{$_POST['servicio']}')),
        trim(upper('{$_POST['fecha_cita']}')), trim(upper('{$_POST['hora_cita']}')),
        trim(upper('{$_POST['lugar_cita']}')),
        trim(upper('{$_POST['deriva_pf']}')), trim(upper('{$_POST['evento_pf']}')),
        DATE_SUB(NOW(), INTERVAL 5 HOUR), TRIM(UPPER('{$_SESSION['us_sds']}')), NULL, NULL, 'A')";
    // Echo para revisar la consulta
    // echo $sql;
    // Ejecutar la consulta
    $rta = dato_mysql($sql);
    return $rta;
} */


function gra_alertas() {
     $campos = [
        'idpeople', 'cursovida', 'fecha', 'tipo', 'crit_epi', 'men_dnt', 'men_sinctrl', 'gestante', 'etapgest', 'ges_sinctrl',
        'cronico', 'cro_hiper', 'cro_diabe', 'cro_epoc', 'cro_sinctrl', 'esq_vacun',
        'alert1', 'selmul1', 'alert2', 'selmul2', 'alert3', 'selmul3', 'alert4', 'selmul4',
        'alert5', 'selmul5', 'alert6', 'selmul6', 'agen_intra', 'servicio', 'fecha_cita', 'hora_cita',
        'lugar_cita', 'deriva_pf', 'evento_pf', 'fecha_create', 'usu_creo', 'fecha_update', 'usu_update', 'estado'
    ];
	 // Campos de tipo fecha que pueden ser nulos
      $campos_fecha_null = ['fecha_cita', 'fecha_update'];

    $id = divide($_POST['idp']);
    $params = [
        ['type' => 's', 'value' => $id[0]]
    ];
  // Resto de campos
    foreach ($campos as $i => $campo) {
    if ($campo == 'idpeople') continue; // ya agregado
    if (in_array($campo, ['fecha_create'])) {
        $params[] = ['type' => 's', 'value' => date('Y-m-d H:i:s')];
    } elseif ($campo == 'usu_creo') {
        $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']];
    } elseif ($campo == 'fecha_update' || $campo == 'usu_update') {
        $params[] = ['type' => 'z', 'value' => null];
    } elseif ($campo == 'estado') {
        $params[] = ['type' => 's', 'value' => 'A'];
    } elseif (in_array($campo, $campos_fecha_null)) {
        $valor = $_POST[$campo] ?? null;
        $params[] = [
            'type' => ($valor === '' || $valor === null) ? 'z' : 's',
            'value' => ($valor === '' || $valor === null) ? null : $valor
        ];
    } elseif (strpos($campo, 'selmul') === 0) {
        // Usar el string de IDs de los select múltiples
        $fsel = 'f' . $campo;
        $valor = isset($_POST[$fsel]) ? str_replace(["'", '"'], '', $_POST[$fsel]) : null;
        $params[] = ['type' => 's', 'value' => $valor];
    } else {
        $valor = $_POST[$campo] ?? null;
        $params[] = ['type' => 's', 'value' => $valor];
    }
}

    $placeholders = implode(', ', array_fill(0, count($params), '?'));
    $sql = "INSERT INTO hog_alert (
        id_alert, " . implode(', ', $campos) . "
    ) VALUES (
        NULL, $placeholders
    )";
    $rta = mysql_prepd($sql, $params);
    return $rta;
} 


function opc_evento($id=''){
	$d=get_persona();
	if($d['sexo']=='MUJER'){
	  if($d['ano']<6){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,1,2,3) and estado='A' ORDER BY 2",$id);
	  }elseif($d['ano']>5 && $d['ano']<10){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,2,3) and estado='A' ORDER BY 2",$id); 
	  }elseif($d['ano']>9 && $d['ano']<18){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,2,3,4) and estado='A' ORDER BY 2",$id); 
	  }elseif($d['ano']>17 && $d['ano']<55){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,2,4) and estado='A' ORDER BY 2",$id); 
	  }elseif($d['ano']>54){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5) and estado='A' ORDER BY 2",$id); 
	  }
	}else{
	  if($d['ano']<6){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,1,2,3) and estado='A' ORDER BY 2",$id);
	  }elseif($d['ano']>5 && $d['ano']<18){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5,2,3) and estado='A' ORDER BY 2",$id); 
	  }elseif($d['ano']>17 && $d['ano']<55){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5) and estado='A' ORDER BY 2",$id); 
	  }elseif($d['ano']>54){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=87 AND valor IN(5) and estado='A' ORDER BY 2",$id); 
	  }
	}
}

function get_alertas(){
	// print_r($_POST);
	if($_POST['id']==''){
		return '';
	}else{
		$id=divide($_POST['id']);
		// print_r($id);
		$sql1="SELECT TIMESTAMPDIFF(YEAR,fecha_nacimiento, fecha ) AS ano,TIMESTAMPDIFF(MONTH,fecha_nacimiento ,fecha ) % 12 AS mes 
		from person P 
		left join hog_alert D ON P.idpeople=D.idpeople WHERE id_alert='{$id[0]}'";
		$data=datos_mysql($sql1);
		$edad=$data['responseResult'][0];



	$sql="SELECT  concat_ws('_', D.idpeople,P.vivipersona) as id,P.idpersona,P.tipo_doc,
		concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,FN_CATALOGODESC(21,sexo) sexo,fecha_nacimiento,
		FN_EDAD(fecha_nacimiento,V.fecha),
		cursovida,D.fecha, tipo,D.crit_epi, men_dnt, men_sinctrl, gestante, etapgest, ges_sinctrl, cronico, cro_hiper, cro_diabe, cro_epoc, cro_sinctrl, esq_vacun, alert1, selmul1, alert2, selmul2, alert3, selmul3, alert4, selmul4, alert5, selmul5, alert6, selmul6, agen_intra, servicio, fecha_cita, hora_cita, lugar_cita, deriva_pf, evento_pf";
		$sql.=" FROM hog_alert D
				LEFT JOIN person P ON D.idpeople=P.idpeople
				LEFT JOIN hog_carac V ON P.vivipersona=V.idfam
				WHERE id_alert ='{$id[0]}'" ;
	 	$info = datos_mysql($sql);
		// echo $sql; 
		// print_r($info['responseResult'][0]);
		if (!$info['responseResult']) {
			return '';
		}else{
			return json_encode($info['responseResult'][0]);
		}
		
	}
}

function opc_alert1fselmul1(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT idcatadeta 'id',descripcion FROM `catadeta` WHERE idcatalogo=233 and estado='A' and valor='".$id[0]."' ORDER BY CAST(idcatadeta AS UNSIGNED)";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}

function opc_servicio(){
}
function opc_lugar_cita(){
}
function opc_hora_cita(){
}
function opc_alert2fselmul2(){
	return opc_alert1fselmul1();
}
function opc_alert3fselmul3(){
	return opc_alert1fselmul1();
}
function opc_alert4fselmul4(){
	return opc_alert1fselmul1();
}
function opc_alert5fselmul5(){
	return opc_alert1fselmul1();
}
function opc_alert6fselmul6(){
	return opc_alert1fselmul1();
}

function opc_necesidad($id=''){
	return opc_sql("SELECT `idcatadeta`, descripcion FROM `catadeta` WHERE idcatalogo=225 AND estado='A' ORDER BY LPAD(idcatadeta, 2, '0')", $id);
}
function opc_rta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_crit_epi($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=166 and estado='A' ORDER BY LPAD(idcatadeta, 2, '0')",$id);
}
function opc_etapgest($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=177 and estado='A' ORDER BY 1",$id);
}
function opc_cursovida($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=176 and estado='A' ORDER BY 1",$id);
}
function opc_medico($id=''){
	return opc_sql("SELECT
	`id_usuario`,
	CONCAT(nombre, ' - ', LEFT(perfil, 3))
FROM
	`usuarios` U
	RIGHT JOIN adscrip A ON U.id_usuario= A.doc_asignado
WHERE
	`perfil` IN('MEDATE', 'ENFATE')
	AND U.subred = (SELECT subred from usuarios where id_usuario={$_SESSION['us_sds']})
	AND	U.id_usuario IN (SELECT doc_asignado FROM adscrip where territorio in (select territorio from adscrip a where doc_asignado={$_SESSION['us_sds']})) 
	AND U.estado = 'A'
ORDER BY
	perfil,2",$id);
}
function opc_pcf($id=''){
	return opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` WHERE `perfil` IN('APYFAM')  AND subred=FN_SUBRED({$_SESSION['us_sds']}) AND estado ='A' ORDER BY 2",$id);
}
function  opc_des($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=0 and estado='A' ORDER BY 1",$id);
}
function  opc_fin($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=39 and estado='A' ORDER BY 1",$id);
}
function opc_oms($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=40 and estado='A' ORDER BY 1",$id);
}
function opc_epoc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=117 and estado='A' ORDER BY 1",$id);
}
function opc_complemento($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=34 and estado='A' ORDER BY 1",$id);
}
function opc_alert($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=231 and estado='A' ORDER BY 1",$id);
}
function opc_selmul1($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=144 and estado='A' ORDER BY 1",$id);
}
function opc_selmul2($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=145 and estado='A' ORDER BY 1",$id);
}
function opc_selmul3($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=146 and estado='A' ORDER BY 1",$id);
}
function opc_selmul4($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=147 and estado='A' ORDER BY 1",$id);
}
function opc_selmul5($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=148 and estado='A' ORDER BY 1",$id);
}
function opc_selmul6($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=149 and estado='A' ORDER BY 1",$id);
}
function opc_selmul7($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=150 and estado='A' ORDER BY 1",$id);
}
function opc_selmul8($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=151 and estado='A' ORDER BY 1",$id);
}
function opc_selmul9($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=152 and estado='A' ORDER BY 1",$id);
}
function opc_selmul10($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=153 and estado='A' ORDER BY 1",$id);
}


function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
	$rta=$c[$d];
	if ($a=='alertas-lis' && $b=='acciones'){
		$rta="<nav class='menu right'>";	
		$rta.="<li title='Ver Alertas'><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'alertas',event,this,['fecha','tipo_activi','tipo','crit_epi','gestante','cronico','esq_vacun','men_dnt','men_sinctrl','alert1','alert2','alert3','alert4','alert5','alert6','agen_intra','deriva_pf'],'../crea-fam/alertas.php');\"></i></li>";  //   act_lista(f,this);
	}
return $rta;
}

function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}
	   
