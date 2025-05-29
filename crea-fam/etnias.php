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

function focus_etnias(){
	return 'etnias';
   }
   
   function men_etnias(){
	$rta=cap_menus('etnias','pro');
	return $rta;
}
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='etnias'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	   }
  return $rta;
}

FUNCTION lis_etnias(){
	// var_dump($_POST['id']);
	$id=divide($_POST['id']);
	$sql="SELECT he.id_etnia AS 'Codigo Registro', he.fecha AS 'Fecha', u.nombre AS 'Creo', he.fecha_create AS 'Fecha de Creación'
    FROM hog_etnia he
    LEFT JOIN usuarios u ON he.usu_create = u.id_usuario";
	$sql.=" WHERE idpeople='".$id[0]."'";
	$sql.=" ORDER BY he.fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"etnias-lis",5);
   }

function cmp_etnias(){
	$rta="<div class='encabezado etnias'>TABLA ETNIAS</div>
	<div class='contenido' id='etnias-lis'>".lis_etnias()."</div></div>";
	$hoy=date('Y-m-d');
	$w='etnias';
	$t=['nombre'=>'','sexo'=>'','edad'=>'','fechanacimiento'=>''];
	$p=get_person();
	if ($p=="") {$p=$t;}
	$d='';
	$o='sesetn';
	$z='zS';
	$days=fechas_app('etnias');
	$o='infusu';
	$c[]=new cmp($o,'e',null,'INFORMACION USUARIO',$w); 
	$c[]=new cmp('nombre','t','80',$p['nombre'],$w.' '.$o,'Nombre','idpersona',null,'',true,false,'','col-4');
	$c[]=new cmp('sexo','t','50',$p['sexo'],$w.' '.$o,'sexo','sexo',null,'',false,false,'','col-15');
	$c[]=new cmp('edad','t','50',$p['edad'],$w.' '.$o,'edad','edad',null,'',false,false,'','col-25');
	$c[]=new cmp('fechanacimiento','d','10',$p['fecha_nacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-2');
    
	$c[]=new cmp($o,'e',null,'MODULO INICIAL',$w);
	$c[]=new cmp('idsesetn','h',15,$_POST['id'],$w.' '.$o,'id','idg',null,'####',false,false);
	$c[]=new cmp('fecha','d','10',$d,$w.' '.$o,'Fecha Sesion','fecha',null,null,true,true,'','col-15',"validDate(this,$days,0);");
	$c[]=new cmp('sesi_nu','s','3',$d,$w.' '.$o,'Sesion N°','sesi_nu',null,null,true,true,'','col-3');
	$c[]=new cmp('moti_con','s','3',$d,$w.' '.$o,'Motivo Consulta','moti_con',null,null,true,true,'','col-3');
	$c[]=new cmp('prio1','s','3',$d,$w.' '.$o,'Prioridad 1','prio',null,null,true,true,'','col-25');
	$c[]=new cmp('prio2','s','3',$d,$w.' '.$o,'Prioridad 2','prio',null,null,true,true,'','col-25');
	//$c[]=new cmp('des_sin','t','100',$d,$w.' '.$o,'Descripcion Sintoma','des_sin',null,null,true,true,'','col-10');

	$o='iderie';
	$c[]=new cmp($o,'e',null,'IDENTIFICACIÓN DEL RIESGO',$w);
	$c[]=new cmp('des_sin','t','100',$d,$w.' '.$o,'Lider Con El Que Se Identifica La Familia','des_sin',null,null,true,true,'','col-35');
	$c[]=new cmp('ser_edu','s','3',$d,$w.' '.$o,'Esta Vinculado (a) A Servicios De Educación','rta',null,null,true,true,'','col-3');
	$c[]=new cmp('ent_esp','s','3',$d,$w.' '.$o,'¿Entiende Castellano - Español?','rta',null,null,true,true,'','col-3');
	$c[]=new cmp('saberpro','s','3',$d,$w.' '.$o,'Saberes Propios','saberpro',null,null,true,true,'','col-3');
	$c[]=new cmp('enf_dif','s','3',$d,$w.' '.$o,'Enfoque Diferencial','enf_dif',null,null,true,true,'','col-2');
	$c[]=new cmp('tip_inv','s','3',$d,$w.' '.$o,'Tipo De Intervención','tip_inv',null,null,true,true,'','col-2');

	// Gesatntes
	$c[]=new cmp($o,'e',null,'IDENTIFICACION DEL RIESGO - GESTANTES',$w); 
	$c[]=new cmp('gestaciones','s','3',$d,$w.' '.$o,'Gestaciones','fobs',null,null,true,true,'','col-2');  
	$c[]=new cmp('partos','s','3',$d,$w.' '.$o,'Partos','fobs',null,null,true,true,'','col-2');  
	$c[]=new cmp('abortos','s','3',$d,$w.'  '.$o,'Abortos','fobs',null,null,true,true,'','col-2');
	$c[]=new cmp('cesareas','s','3',$d,$w.'  '.$o,'Cesareas','fobs',null,null,true,true,'','col-2');
	$c[]=new cmp('vivos','s','3',$d,$w.'  '.$o,'Vivos','fobs',null,null,true,true,'','col-2');
	$c[]=new cmp('muertos','s','3',$d,$w.'  '.$o,'Muertos','fobs',null,null,true,true,'','col-2');
	$c[]=new cmp('fecha_fum','d','10',$d,$w.' '.$o,'Fecha Fum','fecha_fum',null,null,true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('edad_gesta','s','3',$d,$w.'  '.$o,'Edad Gestacional Al Momento De Identificacion En Semanas','edad_gesta',null,null,true,true,'','col-3');
	$c[]=new cmp('resul_gest','s','3',$d,$w.' '.$o,'Resultado De La Gestación','resul_gest',null,null,true,true,'','col-3',"enabClasValu('resul_gest',['ncvmor','mOr','NOm']);");
	$c[]=new cmp('peso_nacer','sd','4',$d,$w.' '.$o,'Peso Al Nacer (Gr)','peso_nacer','rgxpeso','##.#',true,true,'','col-2');
	$c[]=new cmp('asis_ctrpre','s','2',$d,$w.'  '.$o,'¿Asiste A Controles Prenatales?','rta',null,null,true,true,'','col-2',"enabOthNo('asis_ctrpre','CtP');disaOthNo('asis_ctrpre','CPn');");
	$c[]=new cmp('exam_lab','s','2',$d,$w.'  '.$o,'¿Cuenta Con Exámenes De Laboratorio Al Día? Con Relación Al Trimestre Gestacional','rta',null,null,true,true,'','col-4');
    $c[]=new cmp('cons_micronutr','s','2',$d,$w.'  '.$o,'¿Consume Micronutrientes?','rta',null,null,true,true,'','col-3');
	$c[]=new cmp('esqu_vacuna','s','3',$d,$w.'  '.$o,'¿Tiene Esquema De Vacunacion Completo Para La Eg?','rta',null,null,true,true,'','col-3');
	$c[]=new cmp('signos_alarma','s','3',$d,$w.'  '.$o,'¿Presenta Signos De Alarma?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('diag_sifilis','s','3',$d,$w.'  '.$o,'¿Diagnosticada Con Sifilis Gestacional ?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('adhe_tto','s','3',$d,$w.'  '.$o,'¿Adherencia A Tratamiento?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('sifilis_cong','s','3',$d,$w.'  '.$o,'¿Sifilis Congenita?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('seg_partera','s','3',$d,$w.'  '.$o,'¿Le Ha Realizado Seguimiento Partera? ','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('seg_medanc','s','3',$d,$w.'  '.$o,'¿Le Ha Realizado Seguimiento El Médico Ancestral?','rta',null,null,true,true,'','col-3');

	// Cronicos
	$c[]=new cmp($o,'e',null,'IDENTIFICACION DEL RIESGO - CONDICIONES CRONICAS',$w); 
	$c[]=new cmp('dx_cronico','s','3',$d,$w.'  '.$o,'Diagnostico De Condicion Cronica ','dx_cronico',null,null,true,true,'','col-2');
	$c[]=new cmp('cual_dx','t','100',$d,$w.' '.$o,'¿Cual?','cual_dx',null,null,true,true,'','col-2');
	$c[]=new cmp('tto_cronico','s','3',$d,$w.'  '.$o,'Cuenta Con Tratamiento Para Su Enfermedad','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('ctrol_cronico','s','3',$d,$w.'  '.$o,'Asiste A Control De Cronicos ','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('signos_alarma','s','3',$d,$w.'  '.$o,'Presenta Signos De Alarma','rta',null,null,true,true,'','col-15');
	$c[]=new cmp('seg_med','s','3',$d,$w.'  '.$o,'¿Le Ha Realizado Seguimiento El Médico Ancestral?','rta',null,null,true,true,'','col-25');

	//menor de 5 años
	$c[]=new cmp($o,'e',null,'IDENTIFICACION DEL RIESGO - MENOR DE 5 AÑOS',$w);
	$c[]=new cmp('crec_desa','s','3',$d,$w.'  '.$o,'¿Asiste A Controles De Crecimiento Y Desarrollo?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('lact_mate','s','3',$d,$w.'  '.$o,'¿Recibe Lactancia Materna?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('esqvac_comple','s','3',$d,$w.'  '.$o,'¿Tiene Esquema De Vacunación Completo Para La Edad?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('sig_alar','s','3',$d,$w.'  '.$o,'Presenta Signos De Alarma En El Momento Del Seguimiento?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('seg_medance','s','3',$d,$w.'  '.$o,'¿Le Ha Realizado Seguimiento El Médico Ancestral?','rta',null,null,true,true,'','col-2');

	$o='seghos';
	$c[]=new cmp($o,'e',null,'SEGUIMIENTO HOSPITALIZADOS',$w);
	$c[]=new cmp('gestante','s','3',$d,$w.'  '.$o,'¿Gestante?','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('edad_gesta','s','3',$d,$w.'  '.$o,'Edad Gestacional (semanas)','edad_gesta',null,null,true,true,'','col-2');
	$c[]=new cmp('ubi_acom','s','3',$d,$w.'  '.$o,'Ubicación Del Acompañante O Acudiente','ubi_acom',null,null,true,true,'','col-2');
	

	$c[]=new cmp($o,'e',null,'SEGUIMIENTO HOSPITALIZADOS - INFORMACIÓN DE SERVICIO',$w);
	$c[]=new cmp('Serv_req','t','100',$d,$w.' '.$o,'Servicio Requerido','Serv_req',null,null,true,true,'','col-3');
	$c[]=new cmp('fecha_ing','d','10',$d,$w.' '.$o,'Fecha De Ingreso','fecha_ing',null,null,true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('Serv_salud','t','100',$d,$w.' '.$o,'Unidad De Servicio De Salud A La Que Ingresa','Serv_salud',null,null,true,true,'','col-25');
	$c[]=new cmp('Moti_cons','t','100',$d,$w.' '.$o,'Motivo De Consulta/Ingresó','Moti_cons',null,null,true,true,'','col-25');
	$c[]=new cmp('uss_tras','t','100',$d,$w.' '.$o,'Unidad De Servicio De Salud De Traslado','uss_tras',null,null,true,true,'','col-25');
	$c[]=new cmp('tipo_ing','t','100',$d,$w.' '.$o,'Tipo De Ingreso A La Unidad','tipo_ing',null,null,true,true,'','col-25');
	$c[]=new cmp('ant_salud','t','100',$d,$w.' '.$o,'Antecedentes En Salud','ant_salud',null,null,true,true,'','col-25');
	$c[]=new cmp('impdiag','t','100',$d,$w.' '.$o,'Impresión Diagnostica','impdiag',null,null,true,true,'','col-25');

	$c[]=new cmp($o,'e',null,'SEGUIMIENTO HOSPITALIZADOS - DETALLE DEL SEGUIMIENTO INTRA-HOSPITALARIO',$w);
	$c[]=new cmp('uss_encu','t','100',$d,$w.' '.$o,'Unidad De  Salud A La Que Se Encuentra','uss_encu',null,null,true,true,'','col-25');
	$c[]=new cmp('serv_encu','t','100',$d,$w.' '.$o,'Servicio De En El Que Se Encuentra','serv_encu',null,null,true,true,'','col-25');
	$c[]=new cmp('impdiag','t','100',$d,$w.' '.$o,'Impresión Diagnostica','impdiag',null,null,true,true,'','col-25');
	$c[]=new cmp('apoyo_inter','s','3',$d,$w.'  '.$o,'Necesidad De Apoyo Intersectorial','rta',null,null,true,true,'','col-25');


	$o='segpos';
	$c[]=new cmp($o,'e',null,'SEGUIMIENTO POSEGRESO',$w);
	$c[]=new cmp('fecha_ingpos','d','10',$d,$w.' '.$o,'Fecha De Ingreso','fecha_ingpos',null,null,true,true,'','col-25',"validDate(this,$days,0);");
	$c[]=new cmp('especi1','t','100',$d,$w.' '.$o,'Especialidad De Control 1','especi1',null,null,true,true,'','col-25');
	$c[]=new cmp('especi2','t','100',$d,$w.' '.$o,'Especialidad De Control 2','especi2',null,null,true,true,'','col-25');
	$c[]=new cmp('adhe_tto','s','3',$d,$w.'  '.$o,'Adherente Al Tratamiento','rta',null,null,true,true,'','col-25');

	
	$o='uaic';
	$c[]=new cmp($o,'e',null,'UAIC - MOTIVO DE CONSULTA',$w);
	$c[]=new cmp('era','s','3',$d,$w.'  '.$o,'ERA (Enfermedad Respitaria Aguda)','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('eda','s','3',$d,$w.'  '.$o,'EDA (Enfermedad Diarreica Aguda)','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('dnt','s','3',$d,$w.'  '.$o,'DNT (Desnutrición)','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('desc_sint','t','100',$d,$w.' '.$o,'Descripcion De  Sintomas','desc_sint',null,null,true,true,'','col-4');


	$c[]=new cmp($o,'e',null,'UAIC - MANEJO',$w);
	$c[]=new cmp('sales','s','3',$d,$w.'  '.$o,'Sales De Rehidratación','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('acetaminofen','s','3',$d,$w.'  '.$o,'Acetaminofen','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('tras_uss','s','3',$d,$w.'  '.$o,'Traslado Uss','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('educacion','s','3',$d,$w.'  '.$o,'Educación','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('hosp_menor','s','3',$d,$w.'  '.$o,'Menor Hospitalizado','rta',null,null,true,true,'','col-2');


	
	$o='sigvit';
	$c[]=new cmp($o,'e',null,'SIGNOS VITALES Y VALORACION ANTROPOMETRICA',$w);
	$c[]=new cmp('sistolica','sd','5',$d,$w.' '.$o,'Valor Sistolica','sistolica','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('diastolica','sd','5',$d,$w.' '.$o,'Valor Diastolica','diastolica','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('temperatura','sd','5',$d,$w.' '.$o,'Temperatura','temperatura','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('frec_car','sd','5',$d,$w.' '.$o,'Frecuencia Cardiaca','frec_car','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('frec_res','sd','5',$d,$w.' '.$o,'Frecuencia Respiratoria','frec_res','rgxpeso','##.#',false,true,'','col-2');
	$c[]=new cmp('oxige','sd','5',$d,$w.' '.$o,'Saturacion','oxige','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('gluco','sd','5',$d,$w.' '.$o,'Glucometria','gluco','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('peri_cef','sd','4',$d,$w.' '.$o,'Perimetro Cefalico','peri_cef','rgxpeso','##.#',false,true,'','col-2');
    $c[]=new cmp('peri_bra','sd','5',$d,$w.' '.$o,'Perimetro Braquial','peri_bra','rgxtalla','###.#',false,true,'','col-2');
	$c[]=new cmp('peso','sd',6, $d,$w.' '.$z.' '.$o,'Peso (Kg) Mín=0.50 - Máx=150.00','fpe','rgxpeso','###.##',true,true,'','col-2',"valPeso('peso');Zsco('zscore','etnias.php');calImc('peso','talla','imc');");
	$c[]=new cmp('talla','sd',5, $d,$w.' '.$z.' '.$o,'Talla (Cm) Mín=20 - Máx=210','fta','rgxtalla','###.#',true,true,'','col-2',"calImc('peso','talla','imc');Zsco('zscore','etnias.php');valTalla('talla');");
	$c[]=new cmp('imc','t',6, $d,$w.' '.$o,'IMC','imc','','',false,false,'','col-2');
	$c[]=new cmp('zscore','t',15,'',$w.' '.$o,'Z-score','des',null,null,false,false,'','col-35');
	$c[]=new cmp('clasi_nutri','s','3',$d,$w.' '.$o,'Clasificación Nutricional','clasi_nutri',null,null,true,true,'','col-2');
	

	$o='sigvitpos';
	$c[]=new cmp($o,'e',null,'SIGNOS VITALES POSTERIOR AL PLAN DE MANEJO EN LA UAIC',$w);
	$c[]=new cmp('tempeseg','sd','5',$d,$w.' '.$o,'Temperatura','tempeseg','rgxtalla','###.#',false,true,'','col-25');
	$c[]=new cmp('frec_carseg','sd','5',$d,$w.' '.$o,'Frecuencia Cardiaca','frec_carseg','rgxtalla','###.#',false,true,'','col-25');
	$c[]=new cmp('frec_resseg','sd','5',$d,$w.' '.$o,'Frecuencia Respiratoria','frec_resseg','rgxpeso','##.#',false,true,'','col-25');
	$c[]=new cmp('oxigeseg','sd','5',$d,$w.' '.$o,'Saturacion','oxigeseg','rgxtalla','###.#',false,true,'','col-25');
	$c[]=new cmp('entrega_med','t','100',$d,$w.' '.$o,'Seguimiento A A Entrega De Medicamentos','entrega_med',null,null,true,true,'','col-5');


	$o='seguaic';
	$c[]=new cmp($o,'e',null,'SEGUIMIENTO MENORES DNT - POS HOSPITALIZACIÓN UAIC',$w);
	$c[]=new cmp('Segdnt','s','3',$d,$w.'  '.$o,'Seguimiento Menores con  DNT','rta',null,null,true,true,'','col-15');
	$c[]=new cmp('segpeso','sd',6, $d,$w.' '.$z.' '.$o,'Peso (Kg) Mín=0.50 - Máx=150.00','fpe','rgxpeso','###.##',true,true,'','col-15',"valPeso('peso');Zsco('zscore','etnias.php');calImc('peso','talla','imc');");
	$c[]=new cmp('segtalla','sd',5, $d,$w.' '.$z.' '.$o,'Talla (Cm) Mín=20 - Máx=210','fta','rgxtalla','###.#',true,true,'','col-15',"calImc('peso','talla','imc');Zsco('zscore','etnias.php');valTalla('talla');");
	$c[]=new cmp('segzscore','t',15,'',$w.' '.$o,'Z-score Peso/Talla','des',null,null,false,false,'','col-2');
	$c[]=new cmp('segclasi_nutri','s','3',$d,$w.' '.$o,'Clasificación Nutricional','clasi_nutri',null,null,true,true,'','col-15');
	$c[]=new cmp('ftlc','t','100',$d,$w.' '.$o,'Tiene FTLC u otro APME (Cual)','ftlc',null,null,true,true,'','col-2');
	$c[]=new cmp('fecha_Nutri7','d','10',$d,$w.' '.$o,'Cita con Nutrición o Pediatria a los 7 Días','fecha_Nutri7',null,null,true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('fecha_Nutri15','d','10',$d,$w.' '.$o,'Cita con Nutrición o Pediatria a los 15 Días','fecha_Nutri15',null,null,true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('fecha_Nutri30','d','10',$d,$w.' '.$o,'Cita con Nutrición o Pediatria a los 30 Días','fecha_Nutri30',null,null,true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('est_pr1','t','100',$d,$w.' '.$o,'Estado Primer seguimiento','est_pr1',null,null,true,true,'','col-2');
	$c[]=new cmp('est_sg1','t','100',$d,$w.' '.$o,'Estado Segundo seguimiento','est_sg1',null,null,true,true,'','col-2');

	$c[]=new cmp('ctrlpos','s','3',$d,$w.'  '.$o,'Tiene ControlPost-Hospitalizacion','rta',null,null,true,true,'','col-2');
	$c[]=new cmp('ctrl','d','10',$d,$w.' '.$o,'Control','ctrl',null,null,true,true,'','col-2',"validDate(this,$days,0);");
	$c[]=new cmp('est_pr2','t','100',$d,$w.' '.$o,'Estado Primer seguimiento','est_pr2',null,null,true,true,'','col-2');
	$c[]=new cmp('est_sg2','t','100',$d,$w.' '.$o,'Estado Segundo seguimiento','est_sg2',null,null,true,true,'','col-2');
	$c[]=new cmp('est_tr1','t','100',$d,$w.' '.$o,'Estado Tercer seguimiento','est_tr1',null,null,true,true,'','col-2');
	$c[]=new cmp('est_pr3','t','100',$d,$w.' '.$o,'Estado Primer seguimiento','est_pr3',null,null,true,true,'','col-2');
	$c[]=new cmp('est_sg3','t','100',$d,$w.' '.$o,'Estado Segundo seguimiento','est_sg3',null,null,true,true,'','col-2');
	$c[]=new cmp('est_tr2','t','100',$d,$w.' '.$o,'Estado Tercer seguimiento','est_tr2',null,null,true,true,'','col-2');


	

	$c[]=new cmp($o,'e',null,'Aspectos Finales',$w);
	$c[]=new cmp('res_gest','s','3',$d,$w.'  '.$o,'Servicio De Remision Y/O Gestion','res_gest',null,null,true,true,'','col-2');
	$c[]=new cmp('obser','t','7000',$d,$w.' '.$o,'Observaciones','obser',null,null,true,true,'','col-8');
	$c[]=new cmp('equipo','m','60',$d,$w.' '.$o,'Usuarios Equipo','bina',null,null,false,true,'','col-4');
	
	
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_person(){
	// var_dump($_POST);
  $id=divide($_POST['id']);
    $sql="SELECT CONCAT_WS(' ',p.nombre1, p.apellido1) nombre,p.sexo,
	CONCAT('AÑOS: ',TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()),' MESES: ',
    TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE())- (TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) *12),' DIAS: ',
    DATEDIFF(CURDATE(),DATE_ADD(fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) YEAR)) %30) edad,p.fecha_nacimiento 
FROM person p 
    WHERE p.idpeople='".$id[0]."'";
      $info=datos_mysql($sql);
      return $info['responseResult'][0];
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

	function gra_etnias(){
		// print_r($_POST);
		// var_dump($_POST);
		$id=divide($_POST['idsesetn']);
		$zsco=explode("=",$_POST['zscore']?? null);
		$z1=$zsco[0]??null;
		$z2=$zsco[1]??null;
		$sql = "INSERT INTO hog_etnia VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
		$params =[
		['type' => 'i', 'value' => NULL],
		['type' => 'i', 'value' => $id[0]],
		['type' => 's', 'value' => $_POST['fecha']],
		['type' => 's', 'value' => $_POST['sesi_nu']],
		['type' => 's', 'value' => $_POST['moti_con']],
		['type' => 's', 'value' => $_POST['des_sin']],
		['type' => 's', 'value' => $_POST['peso']],
		['type' => 's', 'value' => $_POST['talla']],
		['type' => 's', 'value' => $_POST['imc']],
		['type' => 's', 'value' => $z2],
		['type' => 's', 'value' => $z1],
		['type' => 's', 'value' => $_POST['peri_cef']],
		['type' => 's', 'value' => $_POST['peri_bra']],
		['type' => 's', 'value' => $_POST['frec_res']],
		['type' => 's', 'value' => $_POST['frec_car']],
		['type' => 's', 'value' => $_POST['oxige']],
		['type' => 'i', 'value' => $_SESSION['us_sds']],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => NULL],
		['type' => 's', 'value' => 'A']
		];
		$rta = mysql_prepd($sql, $params);
      return $rta;
	}

	function get_etnias(){
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
		if ($a=='etnias-lis' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'etnias',event,this,['fecha','tipo_activi'],'amb.php');\"></li>";  //   act_lista(f,this);
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	 }	
	 
	function opc_sesi_nu($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=76 and estado='A' ORDER BY 1",$id);
	}
	function opc_rta($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
	  }
	  function opc_rta2($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=88 and estado='A' ORDER BY 1",$id);
	  }
	  function opc_enf_dif($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=250 and estado='A' ORDER BY 1",$id);
	  } 
	  function opc_tip_inv($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=251 and estado='A' ORDER BY 1",$id);
	  } 
	  function opc_saberpro($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=249 and estado='A' ORDER BY 1",$id);
	  }
	function opc_moti_con($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=247 and estado='A' ORDER BY 1",$id);
	}
	function opc_fobs($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=244 and estado='A' ORDER BY cast(idcatadeta AS UNSIGNED)",$id);
	  }
	function opc_prio($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=248 and estado='A' ORDER BY 1",$id);
	}
	function opc_des_sin($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}
	function opc_edad_gesta($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=137 and estado='A' ORDER BY LPAD(idcatadeta, 2, '0') ASC",$id);
	}
	function opc_resul_gest($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=193 and estado='A' ORDER BY 1",$id);
	}

	function opc_clasi_nutri($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=210 and estado='A' ORDER BY 1",$id);
	}
	
	function opc_dx_cronico($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=252 and estado='A' ORDER BY 1",$id);
	}
	function opc_ubi_acom($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=253 and estado='A' ORDER BY 1",$id);
	}
	function opc_res_gest($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=254 and estado='A' ORDER BY 1",$id);
	}
	function opc_bina($id=''){
		return opc_sql("SELECT id_usuario, nombre  from usuarios u WHERE equipo=(select equipo from usuarios WHERE id_usuario='{$_SESSION['us_sds']}') and estado='A'  ORDER BY 2;",$id);
	  }