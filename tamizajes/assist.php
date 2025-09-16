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

function lis_assist(){
	$id=divide($_POST['id']);
	$sql="SELECT id_assist 'Cod Registro',fecha_toma,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM tam_assist A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"valories-lis",5);
}

function cmp_tamassist(){
	$rta="<div class='encabezado valories'>TABLA valories</div><div class='contenido' id='valories-lis'>".lis_assist()."</div></div>";
	$t=['idpersona'=>'','tipodoc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>''];
	$w='tamassist';
	$d=get_tamassist(); 
	if ($d=="") {$d=$t;}
	$o='datos';
    $key='srch';
	$days=fechas_app('psicologia');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('idpersona','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-25','getDatForm(\'srch\',\'person\',[\'datos\']);');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-1');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
	
	$o='Tabaco';
	$c[]=new cmp($o,'e',null,'Tabaco',$w);
	$c[]=new cmp('tconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('tdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('tsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('thabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('tpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('tcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');
	
    $o='Bebidas';
	$c[]=new cmp($o,'e',null,'Bebidas alcohólicas',$w);
	$c[]=new cmp('bconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bhabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('bcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');
    
    $o='Cannabis';
	$c[]=new cmp($o,'e',null,'Cannabis',$w);
	$c[]=new cmp('cconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('csalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('chabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('ccontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Cocaina';
	$c[]=new cmp($o,'e',null,'Cocaína',$w);
	$c[]=new cmp('coconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cofrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('codeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cosalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cohabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('copreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('cocontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Anfetaminas';
	$c[]=new cmp($o,'e',null,'Anfetaminas u otro tipo de estimulantes',$w);
	$c[]=new cmp('aconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('afrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('adeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('asalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ahabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('apreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('acontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Inhalantes';
	$c[]=new cmp($o,'e',null,'Inhalantes',$w);
	$c[]=new cmp('iconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('ifrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ideseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('isalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ihabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ipreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('icontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Tranquilizantes';
	$c[]=new cmp($o,'e',null,'Tranquilizantes o pastillas para dormir',$w);
	$c[]=new cmp('trconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('trfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trhabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('trcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Alucinogenos';
	$c[]=new cmp($o,'e',null,'Alucinógenos',$w);
	$c[]=new cmp('alconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('alfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('aldeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('alsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('alhabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('alpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('alcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Opiaceos';
	$c[]=new cmp($o,'e',null,'Opiáceos',$w);
	$c[]=new cmp('oconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('ofrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('odeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('osalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ohabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('opreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('ocontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Otros';
	$c[]=new cmp($o,'e',null,'Otros',$w);
	$c[]=new cmp('otconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('otfrecuencia','s',3,'',$w.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('otdeseo','s',3,'',$w.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('otsalud','s',3,'',$w.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('othabitual','s',3,'',$w.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('otpreocupa','s',3,'',$w.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('otcontrolar','s',3,'',$w.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

$o='inyectada';
	$c[]=new cmp($o,'e',null,'droga por vía inyectada',$w);
	$c[]=new cmp('inyec','s',3,'',$w.' '.$o,' 8 - ¿Ha consumido alguna vez alguna droga por vía inyectada? (ÚNICAMENTE PARA USOS NO MÉDICOS)','assist2',null,null,true,true,'','col-10');


	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

	function get_tamassist(){
		if($_POST['id']==0){
			return "";
		}else{
			 $id=divide($_POST['id']);
			// print_r($_POST);
			$sql="SELECT P.idpeople,P.idpersona idpersona,P.tipo_doc tipodoc,
			concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,P.fecha_nacimiento fechanacimiento,
			TIMESTAMPDIFF(YEAR, P.fecha_nacimiento, CURDATE()) AS edad
			FROM person P
			WHERE P.idpeople ='{$id[0]}'";
			// echo $sql; 
			$info=datos_mysql($sql);
					return $info['responseResult'][0];
			}
		} 

function focus_tamassist(){
	return 'tamassist';
   }
   
function men_tamassist(){
	$rta=cap_menus('tamassist','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamassist'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }
   
function gra_tamassist() {
	$id = divide($_POST['id']);
	$idpeople = isset($id[0]) ? intval($id[0]) : 0;
	// Validar que se recibe la edad por POST
	if ($idpeople <= 0) {
		return "ID de persona no válido.";
	}
	// Preparar consulta y parámetros
	 $sql = "INSERT INTO tam_assist (
        idpeople, fecha_toma, tconsumido, tfrecuencia, tdeseo, tsalud, thabitual, tpreocupa, tcontrolar,
        bconsumido, bfrecuencia, bdeseo, bsalud, bhabitual, bpreocupa, bcontrolar,
        cconsumido, cfrecuencia, cdeseo, csalud, chabitual, cpreocupa, ccontrolar,
        coconsumido, cofrecuencia, codeseo, cosalud, cohabitual, copreocupa, cocontrolar,
        aconsumido, afrecuencia, adeseo, asalud, ahabitual, apreocupa, acontrolar,
        iconsumido, ifrecuencia, ideseo, isalud, ihabitual, ipreocupa, icontrolar,
        trconsumido, trfrecuencia, trdeseo, trsalud, trhabitual, trpreocupa, trcontrolar,
        alconsumido, alfrecuencia, aldeseo, alsalud, alhabitual, alpreocupa, alcontrolar,
        oconsumido, ofrecuencia, odeseo, osalud, ohabitual, opreocupa, ocontrolar,
        otconsumido, otfrecuencia, otdeseo, otsalud, othabitual, otpreocupa, otcontrolar,
        assist2, usu_creo, fecha_create, estado
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, NOW(), ?
    )";
	
    $params = [
        ['type' => 'i', 'value' => $idpeople],
        ['type' => 's', 'value' => $_POST['fecha_toma']],
        ['type' => 's', 'value' => $_POST['tconsumido']],
        ['type' => 's', 'value' => $_POST['tfrecuencia']],
        ['type' => 's', 'value' => $_POST['tdeseo']],
        ['type' => 's', 'value' => $_POST['tsalud']],
        ['type' => 's', 'value' => $_POST['thabitual']],
        ['type' => 's', 'value' => $_POST['tpreocupa']],
        ['type' => 's', 'value' => $_POST['tcontrolar']],
        ['type' => 's', 'value' => $_POST['bconsumido']],
        ['type' => 's', 'value' => $_POST['bfrecuencia']],
        ['type' => 's', 'value' => $_POST['bdeseo']],
        ['type' => 's', 'value' => $_POST['bsalud']],
        ['type' => 's', 'value' => $_POST['bhabitual']],
        ['type' => 's', 'value' => $_POST['bpreocupa']],
        ['type' => 's', 'value' => $_POST['bcontrolar']],
        ['type' => 's', 'value' => $_POST['cconsumido']],
        ['type' => 's', 'value' => $_POST['cfrecuencia']],
        ['type' => 's', 'value' => $_POST['cdeseo']],
        ['type' => 's', 'value' => $_POST['csalud']],
        ['type' => 's', 'value' => $_POST['chabitual']],
        ['type' => 's', 'value' => $_POST['cpreocupa']],
        ['type' => 's', 'value' => $_POST['ccontrolar']],
        ['type' => 's', 'value' => $_POST['coconsumido']],
        ['type' => 's', 'value' => $_POST['cofrecuencia']],
        ['type' => 's', 'value' => $_POST['codeseo']],
        ['type' => 's', 'value' => $_POST['cosalud']],
        ['type' => 's', 'value' => $_POST['cohabitual']],
        ['type' => 's', 'value' => $_POST['copreocupa']],
        ['type' => 's', 'value' => $_POST['cocontrolar']],
        ['type' => 's', 'value' => $_POST['aconsumido']],
        ['type' => 's', 'value' => $_POST['afrecuencia']],
        ['type' => 's', 'value' => $_POST['adeseo']],
        ['type' => 's', 'value' => $_POST['asalud']],
        ['type' => 's', 'value' => $_POST['ahabitual']],
        ['type' => 's', 'value' => $_POST['apreocupa']],
        ['type' => 's', 'value' => $_POST['acontrolar']],
        ['type' => 's', 'value' => $_POST['iconsumido']],
        ['type' => 's', 'value' => $_POST['ifrecuencia']],
        ['type' => 's', 'value' => $_POST['ideseo']],
        ['type' => 's', 'value' => $_POST['isalud']],
        ['type' => 's', 'value' => $_POST['ihabitual']],
        ['type' => 's', 'value' => $_POST['ipreocupa']],
        ['type' => 's', 'value' => $_POST['icontrolar']],
        ['type' => 's', 'value' => $_POST['trconsumido']],
        ['type' => 's', 'value' => $_POST['trfrecuencia']],
        ['type' => 's', 'value' => $_POST['trdeseo']],
        ['type' => 's', 'value' => $_POST['trsalud']],
        ['type' => 's', 'value' => $_POST['trhabitual']],
        ['type' => 's', 'value' => $_POST['trpreocupa']],
        ['type' => 's', 'value' => $_POST['trcontrolar']],
        ['type' => 's', 'value' => $_POST['alconsumido']],
        ['type' => 's', 'value' => $_POST['alfrecuencia']],
        ['type' => 's', 'value' => $_POST['aldeseo']],
        ['type' => 's', 'value' => $_POST['alsalud']],
        ['type' => 's', 'value' => $_POST['alhabitual']],
        ['type' => 's', 'value' => $_POST['alpreocupa']],
        ['type' => 's', 'value' => $_POST['alcontrolar']],
        ['type' => 's', 'value' => $_POST['oconsumido']],
        ['type' => 's', 'value' => $_POST['ofrecuencia']],
        ['type' => 's', 'value' => $_POST['odeseo']],
        ['type' => 's', 'value' => $_POST['osalud']],
        ['type' => 's', 'value' => $_POST['ohabitual']],
        ['type' => 's', 'value' => $_POST['opreocupa']],
        ['type' => 's', 'value' => $_POST['ocontrolar']],
        ['type' => 's', 'value' => $_POST['otconsumido']],
        ['type' => 's', 'value' => $_POST['otfrecuencia']],
        ['type' => 's', 'value' => $_POST['otdeseo']],
        ['type' => 's', 'value' => $_POST['otsalud']],
        ['type' => 's', 'value' => $_POST['othabitual']],
        ['type' => 's', 'value' => $_POST['otpreocupa']],
        ['type' => 's', 'value' => $_POST['otcontrolar']],
        ['type' => 's', 'value' => $_POST['assist2']],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => 'A']
    ];
	$rta = mysql_prepd($sql, $params);
	return $rta;
}

function opc_rta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
	}

    function opc_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}
  function opc_assist($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=291 and estado='A' ORDER BY 1",$id);
	}
  function opc_assist2($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=292 and estado='A' ORDER BY 1",$id);
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamassist' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamassist','pro',event,'','lib.php',7,'tamassist');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }