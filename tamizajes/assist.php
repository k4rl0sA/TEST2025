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
    $tab='TaBa';
	$c[]=new cmp($o,'e',null,'Tabaco',$w);
	$c[]=new cmp('tconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('tfrecuencia','s',3,'',$w.'  '.$tab.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('tdeseo','s',3,'',$w.' '.$tab.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('tsalud','s',3,'',$w.' '.$tab.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('thabitual','s',3,'',$w.' '.$tab.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('tpreocupa','s',3,'',$w.' '.$tab.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('tcontrolar','s',3,'',$w.' '.$tab.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');
	
    $o='Bebidas';
    $be='BeB';
	$c[]=new cmp($o,'e',null,'Bebidas alcohólicas',$w);
	$c[]=new cmp('bconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('bfrecuencia','s',3,'',$w.' '.$be.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bdeseo','s',3,'',$w.' '.$be.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bsalud','s',3,'',$w.' '.$be.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bhabitual','s',3,'',$w.' '.$be.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('bpreocupa','s',3,'',$w.' '.$be.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('bcontrolar','s',3,'',$w.' '.$be.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');
    
    $o='Cannabis';
    $can='CaN';
	$c[]=new cmp($o,'e',null,'Cannabis',$w);
	$c[]=new cmp('cconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cfrecuencia','s',3,'',$w.' '.$can.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cdeseo','s',3,'',$w.' '.$can.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('csalud','s',3,'',$w.' '.$can.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('chabitual','s',3,'',$w.' '.$can.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cpreocupa','s',3,'',$w.' '.$can.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('ccontrolar','s',3,'',$w.' '.$can.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Cocaina';
    $co='CoC';
	$c[]=new cmp($o,'e',null,'Cocaína',$w);
	$c[]=new cmp('coconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('cofrecuencia','s',3,'',$w.' '.$co.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('codeseo','s',3,'',$w.' '.$co.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cosalud','s',3,'',$w.' '.$co.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('cohabitual','s',3,'',$w.' '.$co.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('copreocupa','s',3,'',$w.' '.$co.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('cocontrolar','s',3,'',$w.' '.$co.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Anfetaminas';
    $anf='AnF';
	$c[]=new cmp($o,'e',null,'Anfetaminas u otro tipo de estimulantes',$w);
	$c[]=new cmp('aconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('afrecuencia','s',3,'',$w.' '.$anf.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('adeseo','s',3,'',$w.' '.$anf.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('asalud','s',3,'',$w.' '.$anf.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ahabitual','s',3,'',$w.' '.$anf.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('apreocupa','s',3,'',$w.' '.$anf.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('acontrolar','s',3,'',$w.' '.$anf.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Inhalantes';
    $inh='InH';
	$c[]=new cmp($o,'e',null,'Inhalantes',$w);
	$c[]=new cmp('iconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('ifrecuencia','s',3,'',$w.' '.$inh.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ideseo','s',3,'',$w.' '.$inh.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('isalud','s',3,'',$w.' '.$inh.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ihabitual','s',3,'',$w.' '.$inh.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ipreocupa','s',3,'',$w.' '.$inh.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('icontrolar','s',3,'',$w.' '.$inh.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Tranquilizantes';
    $traq='TrA';
	$c[]=new cmp($o,'e',null,'Tranquilizantes o pastillas para dormir',$w);
	$c[]=new cmp('trconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('trfrecuencia','s',3,'',$w.' '.$traq.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trdeseo','s',3,'',$w.' '.$traq.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trsalud','s',3,'',$w.' '.$traq.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trhabitual','s',3,'',$w.' '.$traq.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('trpreocupa','s',3,'',$w.' '.$traq.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('trcontrolar','s',3,'',$w.' '.$traq.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Alucinogenos';
    $alu='AlU';
	$c[]=new cmp($o,'e',null,'Alucinógenos',$w);
	$c[]=new cmp('alconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('alfrecuencia','s',3,'',$w.' '.$alu.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('aldeseo','s',3,'',$w.' '.$alu.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('alsalud','s',3,'',$w.' '.$alu.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('alhabitual','s',3,'',$w.' '.$alu.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('alpreocupa','s',3,'',$w.' '.$alu.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('alcontrolar','s',3,'',$w.' '.$alu.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Opiaceos';
    $opi='OpI';
	$c[]=new cmp($o,'e',null,'Opiáceos',$w);
	$c[]=new cmp('oconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('ofrecuencia','s',3,'',$w.' '.$opi.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('odeseo','s',3,'',$w.' '.$opi.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('osalud','s',3,'',$w.' '.$opi.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('ohabitual','s',3,'',$w.' '.$opi.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('opreocupa','s',3,'',$w.' '.$opi.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('ocontrolar','s',3,'',$w.' '.$opi.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

	$o='Otros';
    $ot='OtR';
	$c[]=new cmp($o,'e',null,'Otros',$w);
	$c[]=new cmp('otconsumido','s',3,'',$w.' '.$o,'1. A lo largo de su vida, ¿cual de las siguientes sustancias ha consumido alguna vez? (SOLO PARA USOS NO‐ MÉDICOS)','rta',null,null,true,true,'','col-10');
	$c[]=new cmp('otfrecuencia','s',3,'',$w.' '.$ot.' '.$o,'2. ¿Con qué frecuencia ha consumido las sustancias que ha mencionado en los últimos tres meses, (PRIMERA DROGA,SEGUND A DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('otdeseo','s',3,'',$w.' '.$ot.' '.$o,'3. En los últimos tres meses, ¿con qué frecuencia ha tenido deseos fuertes o ansias de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('otsalud','s',3,'',$w.' '.$ot.' '.$o,'4. En los últimos tres meses, ¿con qué frecuencia le ha llevado su consumo de (PRIMERA DROGA SEGUNDA DROGA, ETC) a problemas de salud, sociales, legales o económicos?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('othabitual','s',3,'',$w.' '.$ot.' '.$o,'5. En los últimos tres meses, ¿con qué frecuencia dejó de hacer lo que se esperaba de usted habitualmente por el consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist',null,null,true,true,'','col-10');
	$c[]=new cmp('otpreocupa','s',3,'',$w.' '.$ot.' '.$o,'6. ¿Un amigo, un familiar o alguien más alguna vez ha mostrado preocupación por su consumo de (PRIMERA DROGA, SEGUNDA DROGA, ETC)?','assist2',null,null,true,true,'','col-10');
	$c[]=new cmp('otcontrolar','s',3,'',$w.' '.$ot.' '.$o,'7. ¿Ha intentado alguna vez controlar, reducir o dejar de consumir (PRIMERA DROGA, SEGUNDA DROGA, ETC) y no lo ha logrado?','assist2',null,null,true,true,'','col-10');

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
    if ($idpeople <= 0) {
        return "ID de persona no válido.";
    }

    // Mapas de equivalencia
    $mapas = [
        'frecuencia' => [1 => 0, 2 => 2, 3 => 3, 4 => 4, 5 => 6],
        'deseo'      => [1 => 0, 2 => 3, 3 => 4, 4 => 5, 5 => 6],
        'salud'      => [1 => 0, 2 => 4, 3 => 5, 4 => 6, 5 => 7],
        'habitual'   => [1 => 0, 2 => 5, 3 => 6, 4 => 7, 5 => 8],
        'preocupa'   => [1 => 0, 2 => 6, 3 => 3],
        'controlar'  => [1 => 0, 2 => 6, 3 => 3],
    ];

    // Prefijos de sustancias según los campos de la tabla
    $sustancias = [
        't'  => 'Tabaco',
        'b'  => 'Bebidas',
        'c'  => 'Cannabis',
        'co' => 'Cocaina',
        'a'  => 'Anfetaminas',
        'i'  => 'Inhalantes',
        'tr' => 'Tranquilizantes',
        'al' => 'Alucinogenos',
        'o'  => 'Opiaceos',
        'ot' => 'Otros',
    ];

    // Campos a mapear por sustancia
    $campos = ['frecuencia', 'deseo', 'salud', 'habitual', 'preocupa', 'controlar'];

    // Array para almacenar los valores mapeados
    $valores = [];

    // Procesar cada sustancia y sus campos
    foreach ($sustancias as $prefijo => $nombre) {
        foreach ($campos as $campo) {
            $post_key = $prefijo . $campo;
            if (isset($_POST[$post_key]) && isset($mapas[$campo])) {
                $valor = intval($_POST[$post_key]);
                $valores[$post_key] = isset($mapas[$campo][$valor]) ? $mapas[$campo][$valor] : $valor;
            } else {
                $valores[$post_key] = isset($_POST[$post_key]) ? $_POST[$post_key] : null;
            }
        }
        // Consumido no se mapea, solo se toma el valor
        $consumido_key = $prefijo . 'consumido';
        $valores[$consumido_key] = isset($_POST[$consumido_key]) ? $_POST[$consumido_key] : null;
    }

    // Calcular puntaje y nivel de riesgo para Tabaco (puedes hacer lo mismo para las demás sustancias si lo necesitas)
    $puntaje_tabaco = $valores['tfrecuencia'] + $valores['tdeseo'] + $valores['tsalud'] + $valores['thabitual'] + $valores['tpreocupa'] + $valores['tcontrolar'];
    $nivel_tabaco = '';
    if ($puntaje_tabaco <= 3) {
        $nivel_tabaco = 'BAJO';
    } elseif ($puntaje_tabaco <= 26) {
        $nivel_tabaco = 'MODERADO';
    } else {
        $nivel_tabaco = 'ALTO';
    }
	 $puntaje_bebidas = $valores['bfrecuencia'] + $valores['bdeseo'] + $valores['bsalud'] + $valores['bhabitual'] + $valores['bpreocupa'] + $valores['bcontrolar'];
    $nivel_bebidas = '';
    if ($puntaje_bebidas <= 10) {
        $nivel_bebidas = 'BAJO';
    } elseif ($puntaje_bebidas <= 26) {
        $nivel_bebidas = 'MODERADO';
    } else {
        $nivel_bebidas = 'ALTO';
    }
	 $puntaje_cannabis = $valores['cfrecuencia'] + $valores['cdeseo'] + $valores['csalud'] + $valores['chabitual'] + $valores['cpreocupa'] + $valores['ccontrolar'];
    $nivel_cannabis = '';
    if ($puntaje_cannabis <= 10) {
        $nivel_cannabis = 'BAJO';
    } elseif ($puntaje_cannabis <= 26) {
        $nivel_cannabis = 'MODERADO';
    } else {
        $nivel_cannabis = 'ALTO';
    }
	$puntaje_cocaina = $valores['cofrecuencia'] + $valores['codeseo'] + $valores['cosalud'] + $valores['cohabitual'] + $valores['copreocupa'] + $valores['cocontrolar'];
    $nivel_cocaina = '';
    if ($puntaje_cocaina <= 10) {
        $nivel_cocaina = 'BAJO';
    } elseif ($puntaje_cocaina <= 26) {
        $nivel_cocaina = 'MODERADO';
    } else {
        $nivel_cocaina = 'ALTO';
    }
	$puntaje_anfetaminas = $valores['afrecuencia'] + $valores['adeseo'] + $valores['asalud'] + $valores['ahabitual'] + $valores['apreocupa'] + $valores['acontrolar'];
    $nivel_anfetaminas = '';
    if ($puntaje_anfetaminas <= 10) {
        $nivel_anfetaminas = 'BAJO';
    } elseif ($puntaje_anfetaminas <= 26) {
        $nivel_anfetaminas = 'MODERADO';
    } else {
        $nivel_anfetaminas = 'ALTO';
    }
	$puntaje_inhalantes = $valores['ifrecuencia'] + $valores['ideseo'] + $valores['isalud'] + $valores['ihabitual'] + $valores['ipreocupa'] + $valores['icontrolar'];
    $nivel_inhalantes = '';
    if ($puntaje_inhalantes <= 10) {
        $nivel_inhalantes = 'BAJO';
    } elseif ($puntaje_inhalantes <= 26) {
        $nivel_inhalantes = 'MODERADO';
    } else {
        $nivel_inhalantes = 'ALTO';
    }
	$puntaje_tranquilizantes = $valores['trfrecuencia'] + $valores['trdeseo'] + $valores['trsalud'] + $valores['trhabitual'] + $valores['trpreocupa'] + $valores['trcontrolar'];
    $nivel_tranquilizantes = '';
    if ($puntaje_tranquilizantes <= 10) {
        $nivel_tranquilizantes = 'BAJO';
    } elseif ($puntaje_tranquilizantes <= 26) {
        $nivel_tranquilizantes = 'MODERADO';
    } else {
        $nivel_tranquilizantes = 'ALTO';
    }
	$puntaje_alucinogenos = $valores['alfrecuencia'] + $valores['aldeseo'] + $valores['alsalud'] + $valores['alhabitual'] + $valores['alpreocupa'] + $valores['alcontrolar'];
    $nivel_alucinogenos = '';
    if ($puntaje_alucinogenos <= 10) {
        $nivel_alucinogenos = 'BAJO';
    } elseif ($puntaje_alucinogenos <= 26) {
        $nivel_alucinogenos = 'MODERADO';
    } else {
        $nivel_alucinogenos = 'ALTO';
    }
	$puntaje_opiaceos = $valores['ofrecuencia'] + $valores['odeseo'] + $valores['osalud'] + $valores['ohabitual'] + $valores['opreocupa'] + $valores['tcontrolar'];
    $nivel_opiaceos = '';
    if ($puntaje_opiaceos <= 10) {
        $nivel_opiaceos = 'BAJO';
    } elseif ($puntaje_opiaceos <= 26) {
        $nivel_opiaceos = 'MODERADO';
    } else {
        $nivel_opiaceos = 'ALTO';
    }
	$puntaje_otros = $valores['otfrecuencia'] + $valores['otdeseo'] + $valores['otsalud'] + $valores['othabitual'] + $valores['otpreocupa'] + $valores['otcontrolar'];
    $nivel_otros = '';
    if ($puntaje_otros <= 10) {
        $nivel_otros = 'BAJO';
    } elseif ($puntaje_otros <= 26) {
        $nivel_otros = 'MODERADO';
    } else {
        $nivel_otros = 'ALTO';
    }

    $puntaje=$puntaje_bebidas+$puntaje_cannabis+$puntaje_cocaina+$puntaje_anfetaminas+$puntaje_inhalantes+$puntaje_tranquilizantes+$puntaje_alucinogenos+$puntaje_opiaceos+$puntaje_otros;
    // Preparar consulta y parámetros (solo ejemplo para los primeros campos, agrega los demás según tu tabla)
    $sql = "INSERT INTO tam_assist (
        idpeople, fecha_toma,
        tconsumido, tfrecuencia, tdeseo, tsalud, thabitual, tpreocupa, tcontrolar,
        bconsumido, bfrecuencia, bdeseo, bsalud, bhabitual, bpreocupa, bcontrolar,
        cconsumido, cfrecuencia, cdeseo, csalud, chabitual, cpreocupa, ccontrolar,
        coconsumido, cofrecuencia, codeseo, cosalud, cohabitual, copreocupa, cocontrolar,
        aconsumido, afrecuencia, adeseo, asalud, ahabitual, apreocupa, acontrolar,
        iconsumido, ifrecuencia, ideseo, isalud, ihabitual, ipreocupa, icontrolar,
        trconsumido, trfrecuencia, trdeseo, trsalud, trhabitual, trpreocupa, trcontrolar,
        alconsumido, alfrecuencia, aldeseo, alsalud, alhabitual, alpreocupa, alcontrolar,
        oconsumido, ofrecuencia, odeseo, osalud, ohabitual, opreocupa, ocontrolar,
        otconsumido, otfrecuencia, otdeseo, otsalud, othabitual, otpreocupa, otcontrolar,
        inyec, pts_tabaco, nvl_tabaco, pts_bebida, nvl_bebida,pts_cannabis, nvl_cannabis,pts_cocaina, nvl_cocaina,pts_anfetaminas, nvl_anfetaminas,pts_inhalantes, nvl_inhalantes,pts_tranqui, nvl_tranqui,pts_alucinog, nvl_alucinog,pts_opiaceos, nvl_opiaceos,pts_otros, nvl_otros,usu_creo, fecha_create,estado) VALUES (
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
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, NOW(), ?)";

    $params = [
        ['type' => 'i', 'value' => $idpeople],
        ['type' => 's', 'value' => $_POST['fecha_toma']],
        ['type' => 's', 'value' => $_POST['tconsumido']],
        ['type' => 'i', 'value' => $_POST['tfrecuencia']],
        ['type' => 'i', 'value' => $_POST['tdeseo']],
        ['type' => 'i', 'value' => $_POST['tsalud']],
        ['type' => 'i', 'value' => $_POST['thabitual']],
        ['type' => 'i', 'value' => $_POST['tpreocupa']],
        ['type' => 'i', 'value' => $_POST['tcontrolar']],
        ['type' => 's', 'value' => $_POST['bconsumido']],
        ['type' => 'i', 'value' => $_POST['bfrecuencia']],
        ['type' => 'i', 'value' => $_POST['bdeseo']],
        ['type' => 'i', 'value' => $_POST['bsalud']],
        ['type' => 'i', 'value' => $_POST['bhabitual']],
        ['type' => 'i', 'value' => $_POST['bpreocupa']],
        ['type' => 'i', 'value' => $_POST['bcontrolar']],
        ['type' => 's', 'value' => $_POST['cconsumido']],
        ['type' => 'i', 'value' => $_POST['cfrecuencia']],
        ['type' => 'i', 'value' => $_POST['cdeseo']],
        ['type' => 'i', 'value' => $_POST['csalud']],
        ['type' => 'i', 'value' => $_POST['chabitual']],
        ['type' => 'i', 'value' => $_POST['cpreocupa']],
        ['type' => 'i', 'value' => $_POST['ccontrolar']],
        ['type' => 's', 'value' => $_POST['coconsumido']],
        ['type' => 'i', 'value' => $_POST['cofrecuencia']],
        ['type' => 'i', 'value' => $_POST['codeseo']],
        ['type' => 'i', 'value' => $_POST['cosalud']],
        ['type' => 'i', 'value' => $_POST['cohabitual']],
        ['type' => 'i', 'value' => $_POST['copreocupa']],
        ['type' => 'i', 'value' => $_POST['cocontrolar']],
        ['type' => 's', 'value' => $_POST['aconsumido']],
        ['type' => 'i', 'value' => $_POST['afrecuencia']],
        ['type' => 'i', 'value' => $_POST['adeseo']],
        ['type' => 'i', 'value' => $_POST['asalud']],
        ['type' => 'i', 'value' => $_POST['ahabitual']],
        ['type' => 'i', 'value' => $_POST['apreocupa']],
        ['type' => 'i', 'value' => $_POST['acontrolar']],
        ['type' => 's', 'value' => $_POST['iconsumido']],
        ['type' => 'i', 'value' => $_POST['ifrecuencia']],
        ['type' => 'i', 'value' => $_POST['ideseo']],
        ['type' => 'i', 'value' => $_POST['isalud']],
        ['type' => 'i', 'value' => $_POST['ihabitual']],
        ['type' => 'i', 'value' => $_POST['ipreocupa']],
        ['type' => 'i', 'value' => $_POST['icontrolar']],
        ['type' => 's', 'value' => $_POST['trconsumido']],
        ['type' => 'i', 'value' => $_POST['trfrecuencia']],
        ['type' => 'i', 'value' => $_POST['trdeseo']],
        ['type' => 'i', 'value' => $_POST['trsalud']],
        ['type' => 'i', 'value' => $_POST['trhabitual']],
        ['type' => 'i', 'value' => $_POST['trpreocupa']],
        ['type' => 'i', 'value' => $_POST['trcontrolar']],
        ['type' => 's', 'value' => $_POST['alconsumido']],
        ['type' => 'i', 'value' => $_POST['alfrecuencia']],
        ['type' => 'i', 'value' => $_POST['aldeseo']],
        ['type' => 'i', 'value' => $_POST['alsalud']],
        ['type' => 'i', 'value' => $_POST['alhabitual']],
        ['type' => 'i', 'value' => $_POST['alpreocupa']],
        ['type' => 'i', 'value' => $_POST['alcontrolar']],
        ['type' => 's', 'value' => $_POST['oconsumido']],
        ['type' => 'i', 'value' => $_POST['ofrecuencia']],
        ['type' => 'i', 'value' => $_POST['odeseo']],
        ['type' => 'i', 'value' => $_POST['osalud']],
        ['type' => 'i', 'value' => $_POST['ohabitual']],
        ['type' => 'i', 'value' => $_POST['opreocupa']],
        ['type' => 'i', 'value' => $_POST['ocontrolar']],
        ['type' => 's', 'value' => $_POST['otconsumido']],
        ['type' => 'i', 'value' => $_POST['otfrecuencia']],
        ['type' => 'i', 'value' => $_POST['otdeseo']],
        ['type' => 'i', 'value' => $_POST['otsalud']],
        ['type' => 'i', 'value' => $_POST['othabitual']],
        ['type' => 'i', 'value' => $_POST['otpreocupa']],
        ['type' => 'i', 'value' => $_POST['otcontrolar']],
        ['type' => 's', 'value' => $_POST['inyec']],
        ['type' => 'i', 'value' => $puntaje_tabaco],
        ['type' => 's', 'value' => $nivel_tabaco],
		['type' => 'i', 'value' => $puntaje_bebidas],
        ['type' => 's', 'value' => $nivel_bebidas],
		['type' => 'i', 'value' => $puntaje_cannabis],
        ['type' => 's', 'value' => $nivel_cannabis],
		['type' => 'i', 'value' => $puntaje_cocaina],
		['type' => 's', 'value' => $nivel_cocaina],
		['type' => 'i', 'value' => $puntaje_anfetaminas],
		['type' => 's', 'value' => $nivel_anfetaminas],
		['type' => 'i', 'value' => $puntaje_inhalantes],
		['type' => 's', 'value' => $nivel_inhalantes],
		['type' => 'i', 'value' => $puntaje_tranquilizantes],
		['type' => 's', 'value' => $nivel_tranquilizantes],
		['type' => 'i', 'value' => $puntaje_alucinogenos],
		['type' => 's', 'value' => $nivel_alucinogenos],
		['type' => 'i', 'value' => $puntaje_opiaceos],
		['type' => 's', 'value' => $nivel_opiaceos],
		['type' => 'i', 'value' => $puntaje_otros],
		['type' => 's', 'value' => $nivel_otros],	
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