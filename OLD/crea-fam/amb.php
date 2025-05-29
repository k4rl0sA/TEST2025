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


function focus_ambient(){
	return 'ambient';
   }
   
   
   function men_ambient(){
	$rta=cap_menus('ambient','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='ambient'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	 
	   }
  return $rta;
}
FUNCTION lis_ambient(){
	// var_dump($_POST['id']);
	$id=divide($_POST['id']);
	$sql="SELECT `idamb` ACCIONES,idamb 'Cod Registro',`fecha`,FN_CATALOGODESC(34,tipo_activi) Tipo,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_amb A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idvivamb='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"ambient-lis",5);
   }


function cmp_ambient(){
	$rta="<div class='encabezado ambient'>TABLA AMBIENTAL</div>
	<div class='contenido' id='ambient-lis'>".lis_ambient()."</div></div>";
	$hoy=date('Y-m-d');
	$w='ambient';
	$d='';
	$o='rieamb';
	$days=fechas_app('vivienda');
	$c[]=new cmp($o,'e',null,'RIESGOS AMBIENTALES DE LA VIVIENDA',$w);
	$c[]=new cmp('idvivamb','h',15,$_POST['id'],$w.' '.$o,'id','idg',null,'####',false,false);
	$c[]=new cmp('fecha','d','10',$d,$w.' '.$o,'Fecha','fecha',null,null,true,true,'','col-5',"validDate(this,$days,0);");
	$c[]=new cmp('tipo_activi','s','3',$d,$w.' '.$o,'Tipo de Activi','tipo_activi',null,null,true,true,'','col-5');

	$o='espvit';
	$c[]=new cmp($o,'e',null,'ESPACIO VITAL',$w);
	$c[]=new cmp('seguro','s','3',$d,$w.' '.$o,'Vivienda en un lugar seguro (sin: remoción en masa, inundaciones - ronda hídrica, avalanchas)','seguro',null,null,true,true,'','col-10');
	$c[]=new cmp('grietas','s','3',$d,$w.' '.$o,'Paredes y techos sin grietas, huecos, humedades','grietas',null,null,true,true,'','col-10');
	$c[]=new cmp('combustible','s','3',$d,$w.' '.$o,'Adecuado manejo de combustibles (sólidos, líquidos, gaseosos)','combustible',null,null,true,true,'','col-10');
	$c[]=new cmp('separadas','s','3',$d,$w.' '.$o,'Las áreas habitacionales de la vivienda están separadas entre sí (baño, cocinas y habitaciones)','separadas',null,null,true,true,'','col-10');
	$c[]=new cmp('lena','s','3',$d,$w.' '.$o,'Preparación de alimentos con leña','lena',null,null,true,true,'','col-10');
	$c[]=new cmp('ilumina','s','3',$d,$w.' '.$o,'La vivienda tiene iluminación y ventilación adecuada','ilumina',null,null,true,true,'','col-10');
	$c[]=new cmp('fuma','s','3',$d,$w.' '.$o,'Se fuma en la vivienda','fuma',null,null,true,true,'','col-10');
	$c[]=new cmp('bano','s','3',$d,$w.' '.$o,'Las condiciones físicas y locativas del baño son adecuadas','bano',null,null,true,true,'','col-10');
	$c[]=new cmp('cocina','s','3',$d,$w.' '.$o,'Las condiciones físicas y locativas de la cocina son adecuadas (evitan la concentración de humo, chimeneas en buen estado (tubo extractor sin obstrucción, sin fisuras, con salida fuera de la vivienda y lavaplatos interno)','cocina',null,null,true,true,'','col-10');
	$c[]=new cmp('elevado','s','3',$d,$w.' '.$o,'Los sitios elevados están protegidos (Escaleras, ventanas, terrazas)','elevado',null,null,true,true,'','col-10');
	$c[]=new cmp('electrica','s','3',$d,$w.' '.$o,'Adecuadas instalaciones eléctricas y de gas (instalaciones seguras, sin recargar, fijas a paredes y techos)','electrica',null,null,true,true,'','col-10');
	$c[]=new cmp('elementos','s','3',$d,$w.' '.$o,'Los elementos del hogar están en lugares seguros (materas, cuchillos, tijeras, cuadros, utensilios, herramientas, agujas y muebles)','elementos',null,null,true,true,'','col-10');
	$c[]=new cmp('barreras','s','3',$d,$w.' '.$o,'Presencia de barreras físicas en la vivienda para el desplazamiento','barreras',null,null,true,true,'','col-10');
	$c[]=new cmp('zontrabajo','s','3',$d,$w.' '.$o,'Las zonas de trabajo se mantienen aisladas de las habitaciones','zontrabajo',null,null,true,true,'','col-10');

	$o='sorvid';
	$c[]=new cmp($o,'e',null,'SORBOS DE VIDA',$w);
	$c[]=new cmp('agua','s','3',$d,$w.' '.$o,'La vivienda cuenta con adecuado sistema de suministro de agua (acueducto, continuo, sin fugas, acometidas en buen estado, fuentes sin semovientes o libres de elementos extraños)','agua',null,null,true,true,'','col-10');
	$c[]=new cmp('tanques','s','3',$d,$w.' '.$o,'Los tanques o recipientes para el almacenamiento de agua están limpios, tapados y elevados del piso','tanques',null,null,true,true,'','col-10');
	$c[]=new cmp('adecagua','s','3',$d,$w.' '.$o,'Manipulación adecuada del agua para consumo (desinfección adecuada, uso seguro de utensilios)','adecagua',null,null,true,true,'','col-10');
	$c[]=new cmp('raciagua','s','3',$d,$w.' '.$o,'Uso racional y reutilización adecuada del agua','raciagua',null,null,true,true,'','col-10');
	

	$o='agures';
	$c[]=new cmp($o,'e',null,'MANEJO DE AGUAS RESIDUALES',$w);
	$c[]=new cmp('sanitari','s','3',$d,$w.' '.$o,'La familia cuenta con unidad sanitaria sin riesgo higiénico sanitario','sanitari',null,null,true,true,'','col-10');
	$c[]=new cmp('aguaresid','s','3',$d,$w.' '.$o,'Existe adecuada disposición de aguas residuales y grises (baño, cocina, lavadero, terraza, campo abierto, pozo séptico)','aguaresid',null,null,true,true,'','col-10');
	$c[]=new cmp('terraza','s','3',$d,$w.' '.$o,'El baño, terraza y lavadero se encuentran limpios y sin olores','terraza',null,null,true,true,'','col-10');

	$o='ressol';
	$c[]=new cmp($o,'e',null,'RESIDUOS SÓLIDOS',$w);
	$c[]=new cmp('recipientes','s','3',$d,$w.' '.$o,'Recipientes aseados, con tapa y bien ubicados','recipientes',null,null,true,true,'','col-10');
	$c[]=new cmp('vivaseada','s','3',$d,$w.' '.$o,'Vivienda aseada y ordenada','vivaseada',null,null,true,true,'','col-10');
	$c[]=new cmp('separesiduos','s','3',$d,$w.' '.$o,'Separación de residuos (basuras) en la fuente','separesiduos',null,null,true,true,'','col-10');
	$c[]=new cmp('reutresiduos','s','3',$d,$w.' '.$o,'Reutilización y prácticas de reducción de residuos sólidos','reutresiduos',null,null,true,true,'','col-10');
	$c[]=new cmp('noresiduos','s','3',$d,$w.' '.$o,'No hay presencia de residuos alrededor de la vivienda (focos o residuos dispersos)','noresiduos',null,null,true,true,'','col-10');
	$c[]=new cmp('adecresiduos','s','3',$d,$w.' '.$o,'La familia dispone los residuos sólidos adecuadamente (empresa de aseo, recolección de envases de agroquímicos vacíos y lavados, relleno sanitario casero, residuos sin quemar)','adecresiduos',null,null,true,true,'','col-10');
	$c[]=new cmp('horaresiduos','s','3',$d,$w.' '.$o,'La familia conoce y cumple el horario de recolección de residuos sólidos','horaresiduos',null,null,true,true,'','col-10');
      
	$o='manpla';
	$c[]=new cmp($o,'e',null,'MANEJO DE PLAGAS',$w);
	$c[]=new cmp('plagas','s','3',$d,$w.' '.$o,'No hay presencia de plagas en la vivienda (roedores, insectos, piojos, pulgas, palomas)','plagas',null,null,true,true,'','col-5');
	$c[]=new cmp('contplagas','s','3',$d,$w.' '.$o,'Se realiza adecuado control preventivo de plagas (químico o alternativo)','contplagas',null,null,true,true,'','col-5');
	$c[]=new cmp('pracsanitar','s','3',$d,$w.' '.$o,'Las prácticas higiénicos-sanitarias no fomentan la proliferación de vectores en la vivienda','pracsanitar',null,null,true,true,'','col-5');
	$c[]=new cmp('envaplaguicid','s','3',$d,$w.' '.$o,'Adecuada disposición de envases de plaguicidas y productos de uso veterinario','envaplaguicid',null,null,true,true,'','col-5');

	$o='alihig';
	$c[]=new cmp($o,'e',null,'ALIMENTOS E HIGIENE',$w);
	$c[]=new cmp('consealiment','s','3',$d,$w.' '.$o,'Almacenamiento y conservación adecuada de alimentos (lugar adecuado, alimentos tapados, recipientes limpios, refrigerados, ahumados, salados)','consealiment',null,null,true,true,'','col-10');
	$c[]=new cmp('limpcocina','s','3',$d,$w.' '.$o,'Adecuada limpieza de la cocina (orden, limpieza y desinfección)','limpcocina',null,null,true,true,'','col-10');
	$c[]=new cmp('cuidcuerpo','s','3',$d,$w.' '.$o,'Adecuado cuidado del cuerpo (uñas, manos, calzado, ropa limpia) a diario y para la manipulación de alimentos','cuidcuerpo',null,null,true,true,'','col-10');
	$c[]=new cmp('fechvencim','s','3',$d,$w.' '.$o,'Verificar etiquetas y fechas de vencimiento de alimentos','fechvencim',null,null,true,true,'','col-10');
	$c[]=new cmp('limputensilios','s','3',$d,$w.' '.$o,'Adecuado estado y limpieza de los utensilios utilizados','limputensilios',null,null,true,true,'','col-10');
	$c[]=new cmp('adqualime','s','3',$d,$w.' '.$o,'El sitio donde adquiere los alimentos es confiable (buenas condiciones de limpieza, mantiene refrigerada las carnes y lácteos, sus manipuladores están uniformados y en buenas condiciones de aseo y limpieza, tiene afecciones de la piel) y vende alimentos que cumplan con rotulado y fecha de vencimiento','adqualime',null,null,true,true,'','col-10');

	$o='riequi';
	$c[]=new cmp($o,'e',null,'RIESGOS QUÍMICOS-FÍSICOS Y DE CONSUMO EN LA VIVIENDA',$w);
	$c[]=new cmp('almaquimicos','s','3',$d,$w.' '.$o,'Los productos químicos están almacenados de forma segura (lugar ventilado, rotulados, separados de las áreas habitacionales)','almaquimicos',null,null,true,true,'','col-10');
	$c[]=new cmp('etiqprodu','s','3',$d,$w.' '.$o,'Se siguen las recomendaciones de uso de la etiqueta de los productos químicos','etiqprodu',null,null,true,true,'','col-10');
	$c[]=new cmp('juguetes','s','3',$d,$w.' '.$o,'Los juguetes de los niños en la vivienda son seguros, están en buen estado y limpios','juguetes',null,null,true,true,'','col-10');
	$c[]=new cmp('medicamalma','s','3',$d,$w.' '.$o,'Los medicamentos y dispositivos médicos están almacenados adecuadamente (rotulados, separados)','medicamalma',null,null,true,true,'','col-10');
	$c[]=new cmp('medicvenc','s','3',$d,$w.' '.$o,'Adecuada disposición de medicamentos vencidos (productos farmacéuticos)','medicvenc',null,null,true,true,'','col-10');
	$c[]=new cmp('adqumedicam','s','3',$d,$w.' '.$o,'Adquisición de medicamentos con fórmula médica','adqumedicam',null,null,true,true,'','col-10');
	$c[]=new cmp('medidaspp','s','3',$d,$w.' '.$o,'Se implementan medidas de protección personal para el manejo de químicos, incluido el estado y limpieza de equipo (guantes, peto, gafas, botas, careta )','medidaspp',null,null,true,true,'','col-10');
	$c[]=new cmp('radiacion','s','3',$d,$w.' '.$o,'Medidas de prevención frente a la exposición al aire libre a la radiación solar','radiacion',null,null,true,true,'','col-10');
	$c[]=new cmp('contamaire','s','3',$d,$w.' '.$o,'Medidas de prevención frente a la exposición a contaminación del aire','contamaire',null,null,true,true,'','col-10');
	$c[]=new cmp('monoxido','s','3',$d,$w.' '.$o,'Conocimiento sobre los signos de alarma para identificar la presencia de monóxido de carbono (llama amarilla, hollín) y gases de agroquímicos (olor a químicos, derrames, elementos impregnados con químicos)','monoxido',null,null,true,true,'','col-10');
	$c[]=new cmp('residelectri','s','3',$d,$w.' '.$o,'Adecuado disposición de residuos eléctricos (pilas, bombillas computadores, celulares y cargadores)','residelectri',null,null,true,true,'','col-10');
	$c[]=new cmp('duermeelectri','s','3',$d,$w.' '.$o,'Se duerme en cercanía de equipos eléctricos','duermeelectri',null,null,true,true,'','col-10');
	

	$o='mascot';
	$c[]=new cmp($o,'e',null,'NUESTRAS MASCOTAS',$w);
	$c[]=new cmp('vacunasmascot','s','3',$d,$w.' '.$o,'Las mascotas (Gato, Perro) cuenta con el esquema vacunal propio de la especie, se encuentran esterilizadas y son desparasitadas periódicamente','vacunasmascot',null,null,true,true,'','col-10');
	$c[]=new cmp('aseamascot','s','3',$d,$w.' '.$o,'Las mascotas son aseadas periódicamente','aseamascot',null,null,true,true,'','col-10');
	$c[]=new cmp('alojmascot','s','3',$d,$w.' '.$o,'Las mascotas u otras especies cuentan con alojamiento adecuado (aseado y separado)','alojmascot',null,null,true,true,'','col-10');
	$c[]=new cmp('excrmascot','s','3',$d,$w.' '.$o,'Las excretas se recogen y manejan adecuadamente','excrmascot',null,null,true,true,'','col-10');
	$c[]=new cmp('permmascot','s','3',$d,$w.' '.$o,'Permanencia de las mascotas u otras especies en un lugar adecuado','permmascot',null,null,true,true,'','col-10');
	$c[]=new cmp('salumascot','s','3',$d,$w.' '.$o,'Las mascotas se encuentran en buenas condiciones de salud y de calidad de vida','salumascot',null,null,true,true,'','col-10');

	$o='disres';
	$c[]=new cmp($o,'e',null,'DISPOSICIÓN DE RESIDUOS',$w);
	$c[]=new cmp('pilas','n','5',$d,$w.' '.$o,'Disposición De Pilas (Gramos)','pilas',null,null,true,true,'','col-5');
	$c[]=new cmp('dispmedicamentos','n','5',$d,$w.' '.$o,'Disposicion De Medicamentos (Gramos)','dispmedicamentos',null,null,true,true,'','col-5');
	$c[]=new cmp('dispcompu','n','5',$d,$w.' '.$o,'Disposicion De Computadores (Gramos), Perifericos Y Celulares','dispcompu',null,null,true,true,'','col-5');
	$c[]=new cmp('dispplamo','n','5',$d,$w.' '.$o,'Disposicion De Plomo Acido (Gramos)','dispplamo',null,null,true,true,'','col-5');
	$c[]=new cmp('dispbombill','n','5',$d,$w.' '.$o,'Disposicion De Bombillas (Gramos)','dispbombill',null,null,true,true,'','col-5');
	$c[]=new cmp('displlanta','n','5',$d,$w.' '.$o,'Disposicion De Llantas (Gramos)','displlanta',null,null,true,true,'','col-5');
	$c[]=new cmp('dispplaguic','n','5',$d,$w.' '.$o,'Disposicion De Envases De Plaguicida (Gramos)','dispplaguic',null,null,true,true,'','col-5');
	$c[]=new cmp('dispaceite','n','5',$d,$w.' '.$o,'Disposicion De Aceite Vegetal Usado (Gramos)','dispaceite',null,null,true,true,'','col-5');


	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}



function opc_tipo_activi($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=34 and estado='A' ORDER BY 1",$id);
	}
	function opc_seguro($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_grietas($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_combustible($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_separadas($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_lena($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_ilumina($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_fuma($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_bano($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_cocina($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_elevado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_electrica($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_elementos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_barreras($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_zontrabajo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_agua($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_tanques($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_adecagua($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_sanitari($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_aguaresid($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_terraza($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_recipientes($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_vivaseada($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_separesiduos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_reutresiduos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_noresiduos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_adecresiduos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_horaresiduos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_plagas($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_contplagas($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_pracsanitar($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_envaplaguicid($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_consealiment($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_limpcocina($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_cuidcuerpo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_fechvencim($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_limputensilios($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_adqualime($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_almaquimicos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_etiqprodu($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_juguetes($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_medicamalma($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_medicvenc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_adqumedicam($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_medidaspp($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_radiacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_contamaire($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_monoxido($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_residelectri($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_duermeelectri($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_vacunasmascot($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_aseamascot($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_alojmascot($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_excrmascot($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_permmascot($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	function opc_salumascot($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
	}
	/* function opc_pilas($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_dispmedicamentos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_dispcompu($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_dispplamo($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_dispbombill($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_displlanta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_dispplaguic($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	}
	function opc_dispaceite($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=33 and estado='A' ORDER BY 1",$id);
	} */
	function opc_raciagua($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=192 and estado='A' ORDER BY 1",$id);
		}

	function gra_ambient(){
		// print_r($_POST);
		$id=divide($_POST['idvivamb']);
		if(count($id)==2){
			$sql = "UPDATE hog_amb SET 
            seguro = TRIM(UPPER('{$_POST['seguro']}')),
            grietas = TRIM(UPPER('{$_POST['grietas']}')),
            combustible = TRIM(UPPER('{$_POST['combustible']}')),
            separadas = TRIM(UPPER('{$_POST['separadas']}')),
            lena = TRIM(UPPER('{$_POST['lena']}')),
            ilumina = TRIM(UPPER('{$_POST['ilumina']}')),
            fuma = TRIM(UPPER('{$_POST['fuma']}')),
            bano = TRIM(UPPER('{$_POST['bano']}')),
            cocina = TRIM(UPPER('{$_POST['cocina']}')),
            elevado = TRIM(UPPER('{$_POST['elevado']}')),
            electrica = TRIM(UPPER('{$_POST['electrica']}')),
            elementos = TRIM(UPPER('{$_POST['elementos']}')),
            barreras = TRIM(UPPER('{$_POST['barreras']}')),
            zontrabajo = TRIM(UPPER('{$_POST['zontrabajo']}')),
            agua = TRIM(UPPER('{$_POST['agua']}')),
            tanques = TRIM(UPPER('{$_POST['tanques']}')),
            adecagua = TRIM(UPPER('{$_POST['adecagua']}')),
            raciagua = TRIM(UPPER('{$_POST['raciagua']}')),
            sanitari = TRIM(UPPER('{$_POST['sanitari']}')),
            aguaresid = TRIM(UPPER('{$_POST['aguaresid']}')),
            terraza = TRIM(UPPER('{$_POST['terraza']}')),
            recipientes = TRIM(UPPER('{$_POST['recipientes']}')),
            vivaseada = TRIM(UPPER('{$_POST['vivaseada']}')),
            separesiduos = TRIM(UPPER('{$_POST['separesiduos']}')),
            reutresiduos = TRIM(UPPER('{$_POST['reutresiduos']}')),
            noresiduos = TRIM(UPPER('{$_POST['noresiduos']}')),
            adecresiduos = TRIM(UPPER('{$_POST['adecresiduos']}')),
            horaresiduos = TRIM(UPPER('{$_POST['horaresiduos']}')),
            plagas = TRIM(UPPER('{$_POST['plagas']}')),
            contplagas = TRIM(UPPER('{$_POST['contplagas']}')),
            pracsanitar = TRIM(UPPER('{$_POST['pracsanitar']}')),
            envaplaguicid = TRIM(UPPER('{$_POST['envaplaguicid']}')),
            consealiment = TRIM(UPPER('{$_POST['consealiment']}')),
            limpcocina = TRIM(UPPER('{$_POST['limpcocina']}')),
            cuidcuerpo = TRIM(UPPER('{$_POST['cuidcuerpo']}')),
            fechvencim = TRIM(UPPER('{$_POST['fechvencim']}')),
            limputensilios = TRIM(UPPER('{$_POST['limputensilios']}')),
            adqualime = TRIM(UPPER('{$_POST['adqualime']}')),
            almaquimicos = TRIM(UPPER('{$_POST['almaquimicos']}')),
            etiqprodu = TRIM(UPPER('{$_POST['etiqprodu']}')),
            juguetes = TRIM(UPPER('{$_POST['juguetes']}')),
            medicamalma = TRIM(UPPER('{$_POST['medicamalma']}')),
            medicvenc = TRIM(UPPER('{$_POST['medicvenc']}')),
            adqumedicam = TRIM(UPPER('{$_POST['adqumedicam']}')),
            medidaspp = TRIM(UPPER('{$_POST['medidaspp']}')),
            radiacion = TRIM(UPPER('{$_POST['radiacion']}')),
            contamaire = TRIM(UPPER('{$_POST['contamaire']}')),
            monoxido = TRIM(UPPER('{$_POST['monoxido']}')),
            residelectri = TRIM(UPPER('{$_POST['residelectri']}')),
            duermeelectri = TRIM(UPPER('{$_POST['duermeelectri']}')),
            vacunasmascot = TRIM(UPPER('{$_POST['vacunasmascot']}')),
            aseamascot = TRIM(UPPER('{$_POST['aseamascot']}')),
            alojmascot = TRIM(UPPER('{$_POST['alojmascot']}')),
            excrmascot = TRIM(UPPER('{$_POST['excrmascot']}')),
            permmascot = TRIM(UPPER('{$_POST['permmascot']}')),
            salumascot = TRIM(UPPER('{$_POST['salumascot']}')),
            pilas = TRIM(UPPER('{$_POST['pilas']}')),
            dispmedicamentos = TRIM(UPPER('{$_POST['dispmedicamentos']}')),
            dispcompu = TRIM(UPPER('{$_POST['dispcompu']}')),
            dispplamo = TRIM(UPPER('{$_POST['dispplamo']}')),
            dispbombill = TRIM(UPPER('{$_POST['dispbombill']}')),
            displlanta = TRIM(UPPER('{$_POST['displlanta']}')),
            dispplaguic = TRIM(UPPER('{$_POST['dispplaguic']}')),
            dispaceite = TRIM(UPPER('{$_POST['dispaceite']}')),
            usu_update = TRIM(UPPER('{$_SESSION['us_sds']}')),
            fecha_update = DATE_SUB(NOW(), INTERVAL 5 HOUR)
        WHERE idamb = TRIM(UPPER('{$_POST['idvivamb']}'))";
			// echo $sql;
		}else if(count($id)==1){
		  $sql="INSERT INTO hog_amb VALUES (NULL,trim(upper('{$id[0]}')),trim(upper('{$_POST['fecha']}')),trim(upper('{$_POST['tipo_activi']}')),trim(upper('{$_POST['seguro']}')),trim(upper('{$_POST['grietas']}')),trim(upper('{$_POST['combustible']}')),trim(upper('{$_POST['separadas']}')),trim(upper('{$_POST['lena']}')),trim(upper('{$_POST['ilumina']}')),trim(upper('{$_POST['fuma']}')),trim(upper('{$_POST['bano']}')),trim(upper('{$_POST['cocina']}')),trim(upper('{$_POST['elevado']}')),trim(upper('{$_POST['electrica']}')),trim(upper('{$_POST['elementos']}')),trim(upper('{$_POST['barreras']}')),trim(upper('{$_POST['zontrabajo']}')),trim(upper('{$_POST['agua']}')),trim(upper('{$_POST['tanques']}')),trim(upper('{$_POST['adecagua']}')),trim(upper('{$_POST['raciagua']}')),trim(upper('{$_POST['sanitari']}')),trim(upper('{$_POST['aguaresid']}')),trim(upper('{$_POST['terraza']}')),trim(upper('{$_POST['recipientes']}')),trim(upper('{$_POST['vivaseada']}')),trim(upper('{$_POST['separesiduos']}')),trim(upper('{$_POST['reutresiduos']}')),trim(upper('{$_POST['noresiduos']}')),trim(upper('{$_POST['adecresiduos']}')),trim(upper('{$_POST['horaresiduos']}')),trim(upper('{$_POST['plagas']}')),trim(upper('{$_POST['contplagas']}')),trim(upper('{$_POST['pracsanitar']}')),trim(upper('{$_POST['envaplaguicid']}')),trim(upper('{$_POST['consealiment']}')),trim(upper('{$_POST['limpcocina']}')),trim(upper('{$_POST['cuidcuerpo']}')),trim(upper('{$_POST['fechvencim']}')),trim(upper('{$_POST['limputensilios']}')),trim(upper('{$_POST['adqualime']}')),trim(upper('{$_POST['almaquimicos']}')),trim(upper('{$_POST['etiqprodu']}')),trim(upper('{$_POST['juguetes']}')),trim(upper('{$_POST['medicamalma']}')),trim(upper('{$_POST['medicvenc']}')),trim(upper('{$_POST['adqumedicam']}')),trim(upper('{$_POST['medidaspp']}')),trim(upper('{$_POST['radiacion']}')),trim(upper('{$_POST['contamaire']}')),trim(upper('{$_POST['monoxido']}')),trim(upper('{$_POST['residelectri']}')),trim(upper('{$_POST['duermeelectri']}')),trim(upper('{$_POST['vacunasmascot']}')),trim(upper('{$_POST['aseamascot']}')),trim(upper('{$_POST['alojmascot']}')),trim(upper('{$_POST['excrmascot']}')),trim(upper('{$_POST['permmascot']}')),trim(upper('{$_POST['salumascot']}')),trim(upper('{$_POST['pilas']}')),trim(upper('{$_POST['dispmedicamentos']}')),trim(upper('{$_POST['dispcompu']}')),trim(upper('{$_POST['dispplamo']}')),trim(upper('{$_POST['dispbombill']}')),trim(upper('{$_POST['displlanta']}')),trim(upper('{$_POST['dispplaguic']}')),trim(upper('{$_POST['dispaceite']}')),
		  TRIM(UPPER('{$_SESSION['us_sds']}')),DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
			// echo $sql;
		}else{
			
		}
		$rta=dato_mysql($sql);
	  return $rta;
	}

	function get_ambient(){
		// var_dump($_POST);
		if($_REQUEST['id']==''){
			return "";
		  }else{
			$id=divide($_REQUEST['id']);
			$sql="SELECT concat_ws('_',idamb,idvivamb) idamb,fecha,tipo_activi,seguro,grietas,combustible,separadas,lena,ilumina,fuma,bano,cocina,elevado,electrica,elementos,barreras,zontrabajo,agua,tanques,adecagua,raciagua,sanitari,aguaresid,terraza,recipientes,vivaseada,separesiduos,reutresiduos,noresiduos,adecresiduos,horaresiduos,plagas,contplagas,pracsanitar,envaplaguicid,consealiment,limpcocina,cuidcuerpo,fechvencim,limputensilios,adqualime,almaquimicos,etiqprodu,juguetes,medicamalma,medicvenc,adqumedicam,medidaspp,radiacion,contamaire,monoxido,residelectri,duermeelectri,vacunasmascot,aseamascot,alojmascot,excrmascot,permmascot,salumascot,pilas,dispmedicamentos,dispcompu,dispplamo,dispbombill,displlanta,dispplaguic,dispaceite
			FROM hog_amb			
			WHERE idamb ='{$id[0]}'";
			// var_dump($sql);
			$info=datos_mysql($sql);
			return json_encode($info['responseResult'][0]);
		  } 
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
		if ($a=='ambient-lis' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'ambient',event,this,['fecha','tipo_activi'],'amb.php');\"></li>";  //   act_lista(f,this);
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	   }
	   