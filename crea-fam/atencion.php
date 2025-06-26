<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
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

function cmp_atencion(){
	$rta="";
	$rta .="<div class='encabezado atencion'>Consultas realizadas al paciente</div>
	<div class='contenido' id='atencion-lis' >".lis_atencion()."</div></div>";
	$hoy=date('Y-m-d');
	$t=['id'=>$_POST['id'],'idpersona'=>'','tipo_doc'=>'','nombres'=>'','fecha_atencion'=>'','tipo_consulta'=>'','cod_cups'=>'','fecha_nacimiento'=>'','sexo'=>'','genero'=>'','nacionalidad'=>''];
	$d=get_personas();
	$x="";
	if ($d==""){$d=$t;}
	$u=($d['idpersona']=='')?true:false;
	$w='atencion';		
	$o='datos';

	$fecha_actual = new DateTime();
	$fecha_nacimiento = new DateTime($d['fecha_nacimiento']);
	$edad = $fecha_nacimiento->diff($fecha_actual)->y;
	$adul = ($edad>=18) ? true : false;
	$adult = ($edad>=18) ? 'true' : 'false';
	$meno = ($edad<5) ? true : false;
	$gest = (($edad>=10 && $edad <= 54) && $d['sexo'] == 'M') ? true : false;
	
	$c[]=new cmp($o,'e',null,'Datos atención medica usuario',$w);
	$c[]=new cmp('ida','h',15,$d['id'],$w.' '.$o,'ida','ida',null,'####',false,false,'col-1');
	$c[]=new cmp('tipodoc','t','20',$d['tipo_doc'],$w.' '.$o,'Tipo','tipodoc',null,'',false,false,'','col-1');
	$c[]=new cmp('idpersona','t','20',$d['idpersona'],$w.' '.$o,'N° Identificación','idpersona',null,'',false,false,'','col-2');
	$c[]=new cmp('nombre1','t','20',$d['nombres'],$w.' '.$o,'Nombres','nombre1',null,'',false,false,'','col-3');
	$c[]=new cmp('fecha_nacimiento','t','20',$d['fecha_nacimiento'],$w.' '.$o,'fecha nacimiento','fecha_nacimiento',null,'',false,false,'','col-1','validDate');
	$c[]=new cmp('sexo','s','20',$d['sexo'],$w.' '.$o,'sexo','sexo',null,'',false,false,'','col-1');
	$c[]=new cmp('genero','s','20',$d['genero'],$w.' '.$o,'genero','genero',null,'',false,false,'','col-1');
	$c[]=new cmp('nacionalidad','s','20',$d['nacionalidad'],$w.' '.$o,'Nacionalidad','nacionalidad',null,'',false,false,'','col-1');

	$o='consulta';
	$c[]=new cmp($o,'e',null,'Datos de la atencion medica	',$w);
	$c[]=new cmp('idf','h',15,'',$w.' '.$o,'idf','idf',null,'####',false,false,'','col-1');
	$c[]=new cmp('fechaatencion','d',20,$x,$w.' '.$o,'Fecha de la consulta','fechaatencion',null,'',true,false,'','col-2');
	$c[]=new cmp('tipo_consulta','s',3,$x,$w.' '.$o,'Tipo de Consulta','tipo_consulta',null,'',true,false,'','col-2');
	$c[]=new cmp('codigocups','s',3,$x,$w.' '.$o,'Código CUPS','cups',null,'',true,false,'','col-3');
	$c[]=new cmp('finalidadconsulta','s',3,$x,$w.' '.$o,'Finalidad de la Consulta','consultamedica',null,'',true,false,'','col-3');

	$c[]=new cmp('letra1','s','3',$x,$w.' '.$o,'Letra CIE(1)','letra1',null,null,true,true,'','col-1',"valPyd(this,'tipo_consulta');valResol('tipo_consulta','letra1');selectDepend('letra1','rango1','atencion.php');");//,['rango1']
 	$c[]=new cmp('rango1','s','3',$x,$w.' '.$o,'Tipo1','rango1',null,null,true,true,'','col-45',"selectDepend('rango1','diagnostico1','atencion.php');");
 	$c[]=new cmp('diagnostico1','s','8',$x,$w.' '.$o,'Diagnostico Principal','diagnostico1',null,null,true,true,'','col-45');
	$c[]=new cmp('letra2','s','3',$x,$w.' '.$o,'Letra CIE(2)','letra2',null,null,false,true,'','col-1',"selectDepend('letra2','rango2','atencion.php');");
 	$c[]=new cmp('rango2','s','3',$x,$w.' '.$o,'Tipo2','rango2',null,null,false,true,'','col-45',"selectDepend('rango2','diagnostico2','atencion.php');");
 	$c[]=new cmp('diagnostico2','s','8',$x,$w.' '.$o,'Diagnostico 2','diagnostico2',null,null,false,true,'','col-45');
	$c[]=new cmp('letra3','s','3',$x,$w.' '.$o,'Letra CIE(3)','letra3',null,null,false,true,'','col-1',"selectDepend('letra3','rango3','atencion.php');");
 	$c[]=new cmp('rango3','s','3',$x,$w.' '.$o,'Tipo3','rango3',null,null,false,true,'','col-45',"selectDepend('rango3','diagnostico3','atencion.php');");
 	$c[]=new cmp('diagnostico3','s','8',$x,$w.' '.$o,'Diagnostico 3','diagnostico3',null,null,false,true,'','col-45');


$o='cronico';
	$c[]=new cmp($o,'e',null,'Condiciones',$w);


	$c[]=new cmp('fertil','s',3,$x,$w.' pre mef '.$o,'¿Mujer en Edad Fertil (MEF) con intención reproductiva?','aler',null,'',$gest,$gest,'','col-4',"enabFert(this,'fer','nfe');");
	$c[]=new cmp('preconcepcional','s',3,$x,$w.' pre nfe '.$o,'Tiene consulta preconcepcional','aler',null,'',$gest,false,'','col-2');
	$c[]=new cmp('metodo','s',3,$x,$w.' pre fer '.$o,'Uso actual de método anticonceptivo','aler',null,'',$gest,false,'','col-2','enabAlert(this,\'met\');');
	$c[]=new cmp('anticonceptivo','s',3,$x,$w.' pre fer met '.$o,'Metodo anticonceptivo','metodoscons',null,'',$gest,false,'','col-2');
	$c[]=new cmp('planificacion','s',3,$x,$w.' pre fer '.$o,'Tiene consulta de PF','aler',null,'',$gest,false,'','col-2');
	$c[]=new cmp('mestruacion','d',3,$x,$w.'  '.$o,'Fecha de ultima Mestruacion','mestruacion',null,'',false,true,'','col-2');	
// }	

$o='prurap';
	$c[]=new cmp($o,'e',null,'Aplicacion de Pruebas Rapidas',$w);
	$c[]=new cmp('vih','s',3,$x,$w.' '.$o,'Prueba Rapida Para VIH','aler',null,'',true,true,'','col-25',"enabTest(this,'vih');");
	$c[]=new cmp('resul_vih','s',3,$x,$w.' vih '.$o,'Resultado VIH','vih',null,'',true,false,'','col-25');
	$c[]=new cmp('hb','s',3,$x,$w.' '.$o,'Prueba Rapida Para Hepatitis B Antigeno de Superficie','aler',null,'',true,true,'','col-25',"enabTest(this,'hb');");
	$c[]=new cmp('resul_hb','s',3,$x,$w.' hb '.$o,'Resultado Hepatitis B Antigeno de Superficie','rep',null,'',true,false,'','col-25');
	$c[]=new cmp('trepo_sifil','s',3,$x,$w.' '.$o,'Prueba Rapida Treponémica Para Sifilis','aler',null,'',true,true,'','col-25',"enabTest(this,'sif');");
	$c[]=new cmp('resul_sifil','s',3,$x,$w.' sif '.$o,'Resultado Treponémica Para Sifilis','rep',null,'',true,false,'','col-25');
	$c[]=new cmp('pru_embarazo','s',3,$x,$w.' '.$o,'Prueba de Embarazo','aler',null,'',$gest,$gest,'','col-25',"enabTest(this,'pem');");
	$c[]=new cmp('resul_emba','s',3,$x,$w.' pem '.$o,'Resultado prueba de Embarazo','rep',null,'',$gest,false,'','col-25');
	$c[]=new cmp('pru_apetito','s',3,$x,$w.' '.$o,'Prueba de apetito','aler',null,'',$gest,$gest,'','col-25',"enabTest(this,'ape');");
	$c[]=new cmp('resul_apetito','s',3,$x,$w.' ape '.$o,'Resultado prueba de Apetito','rep',null,'',$gest,false,'','col-25');

 $o='plancuidado';
	$c[]=new cmp($o,'e',null,'Plan de Cuidado Individual',$w);
	$c[]=new cmp('eventointeres','o',3,$x,$w.' '.$o,'Notificacion de eventos de interés en salud pública','eventointeres	',null,'',false,$u,'','col-35','enabEven(this,\'even\',\'whic\');');//,'hidFieOpt(\'eventointeres\',\'event_hid\',this,true)'
	$c[]=new cmp('evento','s',3,$x,$w.' even '.$o,'Evento de Interes en Salud Publica','evento',null,'',false,false,'','col-4','cualEven(this,\'whic\');');//,'hidFieselet(\'evento\',\'hidd_aten\',this,true,\'5\')'
	$c[]=new cmp('cualevento','t',300,$x,$w.' whic '.$o,'Otro, Cual?','cualevento	',null,'',false,false,'','col-25');
	$c[]=new cmp('sirc','o',3,$x,$w.' '.$o,'Activación rutas SIRC (usuarios otras EAPB)','sirc	',null,'',false,true,'','col-5',"enabAlert(this,'sirc');");//,'hidFieOpt(\'sirc\',\'sirc\',this,true)'
	$c[]=new cmp('rutasirc[]','m',3,$x,$w.' sirc '.$o,'Rutas SIRC','rutapoblacion',null,'',false,false,'','col-5');
	$c[]=new cmp('remision','o',3,$x,$w.' '.$o,'Usuario que require control','remision	',null,'',false,true,'','col-5','enabAlert(this,\'rem\');');//,'hidFieOpt(\'remision\',\'espe_hid\',this,true)'
	$c[]=new cmp('cualremision[]','m',3,$x,$w.' rem '.$o,'Cuales?	','remision	',null,'',false,false,'','col-5');
	
	$c[]=new cmp('ordenvacunacion','o',3,$x,$w.' '.$o,'Orden Vacunación?','ordenvacunacion	',null,'',false,true,'','col-1','enabAlert(this,\'vac\');');//,'hidFieOpt(\'ordenvacunacion\',\'vacu_hid\',this,true)'
	$c[]=new cmp('vacunacion','s',3,$x,$w.' vac '.$o,'Vacunación	','vacunacion',null,'',false,false,'','col-2');
	
	$c[]=new cmp('ordenlaboratorio','o',3,$x,$w.' '.$o,'Ordena Laboratorio ?','ordenlaboratorio	',null,'',false,true,'','col-15','enabAlert(this,\'lab\');');//,'hidFieOpt(\'ordenlaboratorio\',\'lab_hid\',this,true)'
	$c[]=new cmp('laboratorios','s',3,$x,$w.' lab '.$o,'Laboratorio','solicitud',null,'',false,false,'','col-2');
	
	$c[]=new cmp('ordenmedicamentos','o',3,$x,$w.' '.$o,'Ordena Medicamentos ?','ordenmedicamentos	',null,'',false,true,'','col-15','enabAlert(this,\'med\');');//,'hidFieOpt(\'ordenmedicamentos\',\'medi_hid\',this,true)'
	$c[]=new cmp('medicamentos','s',3,$x,$w.' med '.$o,'Medicamentos','medicamentos',null,'',false,false,'','col-2');
	
	$c[]=new cmp('rutacontinuidad','o',3,$x,$w.' '.$o,'Remisión para continuidad a rutas integrales de atencion en salud por parte de la subred','prueba	',null,'',false,true,'','col-5',"enabAlert(this,'rut');");//,'hidFieOpt(\'rutacontinuidad\',\'cont_hid\',this,true)'
	$c[]=new cmp('continuidad[]','m',3,$x,$w.' rut '.$o,'.','rutapoblacion',null,'',false,false,'','col-5');
	$c[]=new cmp('ordenimagenes','o',3,$x,$w.' '.$o,'Ordena Imágenes Diagnósticas','ordenimagenes	',null,'',true,true,'','col-3');//,'hidFieOpt(\'ordenimagenes\',\'img_hid\',this,true)'
	$c[]=new cmp('ordenpsicologia','s',3,$x,$w.' '.$o,'Ordena Psicología','aler',null,'',true,true,'','col-3');
	$c[]=new cmp('relevo','s',3,$x,$w.' '.$o,'Cumple criterios Para relevo domiciliario a cuidadores','aler',null,'',true,true,'','col-4');
	$c[]=new cmp('estrategia','s',3,$x,$w.' '.$o,'Estrategia','estrategia',null,'',true,true,'','col-4',"enbValue('estrategia','eSt','3');");
	$c[]=new cmp('tipo_estrategia','s',3,$x,$w.' eSt '.$o,'Prioridad','prioridad',null,'',true,false,'','col-4');
	

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
   }

   function lis_atencion(){
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['ida']) ? divide($_POST['ida']) : null);
	// print_r($id);
	$info=datos_mysql("SELECT COUNT(*) total FROM adm_facturacion F WHERE F.idpeople ='{$id[0]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;

	$pag=(isset($_POST['pag-atencion']))? ($_POST['pag-atencion']-1)* $regxPag:0;
	$sql="SELECT  F.id_factura ACCIONES,F.cod_admin,F.fecha_consulta fecha,FN_CATALOGODESC(182,F.tipo_consulta) Consulta,
	FN_CATALOGODESC(126,F.cod_cups) 'Código CUPS',FN_CATALOGODESC(127,F.final_consul) Finalidad
	FROM adm_facturacion F
	WHERE F.idpeople ='{$id[0]}'";
		$sql.=" ORDER BY F.fecha_create";
		$sql.=' LIMIT '.$pag.','.$regxPag;
		// echo $sql;
			$datos=datos_mysql($sql);
			return create_table($total,$datos["responseResult"],"atencion",$regxPag,'atencion.php');
		// return panel_content($datos["responseResult"],"atencion-lis",5);
	}

	function get_personas(){
		//var_dump($_REQUEST);
		if($_REQUEST['id']==''){
			return "";
		}else{
			$id=divide($_REQUEST['id']);
			//  `fechaatencion`, `codigocups`, `finalidadconsulta`, `peso`, `talla`, `sistolica`, `diastolica`, `abdominal`, `brazo`, `diagnosticoprincipal`, `diagnosticorelacion1`, `diagnosticorelacion2`, `diagnosticorelacion3`, `fertil`, `preconcepcional`, `metodo`, `anticonceptivo`, `planificacion`, `mestruacion`, `gestante`, `gestaciones`, `partos`, `abortos`, `cesarias`, `vivos`, `muertos`, `vacunaciongestante`, `edadgestacion`, `ultimagestacion`, `probableparto`, `prenatal`, `fechaparto`, `rpsicosocial`, `robstetrico`, `rtromboembo`, `rdepresion`, `sifilisgestacional`, `sifiliscongenita`, `morbilidad`, `hepatitisb`, `vih`, `cronico`, `asistenciacronica`, `tratamiento`, `vacunascronico`, `menos5anios`, `esquemavacuna`, `signoalarma`, `cualalarma`, `dxnutricional`, `eventointeres`, `evento`, `cualevento`, `sirc`, `rutasirc`, `remision`, `cualremision`, `ordenpsicologia`, `ordenvacunacion`, `vacunacion`, `ordenlaboratorio`, `laboratorios`, `ordenimagenes`, `imagenes`, `ordenmedicamentos`, `medicamentos`, `rutacontinuidad`, `continuidad`, `relevo`  ON a.idpersona = b.idpersona AND a.tipodoc = b.tipo_doc
			$sql="SELECT  concat_ws('_',a.idpeople,b.id_factura) id,a.idpersona,tipo_doc,concat_ws(' ',a.nombre1,a.nombre2,a.apellido1,a.apellido2) nombres,a.fecha_nacimiento,a.sexo,a.genero,a.nacionalidad,
			b.fecha_consulta,b.tipo_consulta,cod_cups,fecha_consulta,tipo_consulta,final_consul
			FROM person a
			LEFT JOIN adm_facturacion b ON a.idpeople = b.idpeople 
			WHERE a.idpeople ='{$id[0]}'";
			// echo $sql;
			$info=datos_mysql($sql);
			return $info['responseResult'][0];			
		}
}

function get_atencion(){
	// var_dump($_POST);
	if($_REQUEST['id']==''){
		return "";
	}else{
		 $id=$_REQUEST['id'];
			// print_r($id[0]);
			// print_r($_REQUEST['id']);
			$sql1="SELECT COUNT(*) rta
			FROM adm_facturacion a
			LEFT JOIN eac_atencion c ON a.idpeople = c.idpeople
			WHERE c.id_factura ='{$id}' and a.id_factura='{$id}'";
			$info=datos_mysql($sql1);
			$total=$info['responseResult'][0]['rta'];
			/* $info=datos_mysql($sql); */
			// return json_encode($info['responseResult'][0]);
			if ($total==1){		
				$sql="SELECT concat(a.idpeople) id,b.tipo_doc,b.idpersona,concat_ws(' ',b.nombre1,b.nombre2,b.apellido1,b.apellido2) nombres,
					b.fecha_nacimiento,b.sexo,b.genero,b.nacionalidad,a.id_factura,a.fecha_consulta,a.tipo_consulta,a.cod_cups,a.final_consul,
					letra1, rango1, diagnostico1, letra2, rango2, diagnostico2, letra3, rango3, 
					diagnostico3,fertil, preconcepcional, metodo, anticonceptivo, planificacion, 
					mestruacion,vih,resul_vih,hb,resul_hb,trepo_sifil,resul_sifil,pru_embarazo,resul_emba,
					pru_apetito,resul_apetito,evento_interes,evento,cuale_vento,sirc,ruta_sirc,remision,cual_remision, orden_vacunacion, vacunacion, 
					orden_laboratorio, laboratorios, orden_medicamentos,medicamentos, ruta_continuidad, continuidad, orden_imagenes, orden_psicologia, relevo,
					estrategia,motivo_estrategia
					FROM adm_facturacion a
					LEFT JOIN person b ON a.idpeople=b.idpeople
					LEFT JOIN eac_atencion c ON a.idpeople=c.idpeople 
					WHERE c.id_factura ='{$id}' and a.id_factura='{$id}'";
			 		// echo $sql;
					$info=datos_mysql($sql);
				return json_encode($info['responseResult'][0]);
			}else{
				$sql="SELECT concat(b.idpeople) id,
				b.tipo_doc,
				b.idpersona,
				concat_ws(' ',b.nombre1,b.nombre2,b.apellido1,b.apellido2) nombres,
				b.fecha_nacimiento,b.sexo,b.genero,b.nacionalidad, a.id_factura,a.fecha_consulta,a.tipo_consulta,a.cod_cups,a.final_consul,
				letra1,rango1,diagnostico1,letra2,rango2,diagnostico2,letra3,rango3,
				diagnostico3, fertil, preconcepcional,metodo,anticonceptivo,planificacion,
				mestruacion,vih,resul_vih,hb,resul_hb,trepo_sifil,resul_sifil,pru_embarazo,resul_emba,
				evento,cuale_vento,sirc,ruta_sirc,remision,cual_remision,orden_vacunacion,vacunacion,orden_laboratorio,laboratorios,orden_medicamentos,medicamentos,ruta_continuidad,continuidad,orden_imagenes,orden_psicologia,relevo,estrategia,motivo_estrategia
				FROM adm_facturacion a
				LEFT JOIN person b ON a.idpeople=b.idpeople 
				LEFT JOIN eac_atencion c ON a.idpeople=c.idpeople AND a.id_factura=c.id_factura
				WHERE a.id_factura='{$id}'";
			//  echo $sql;
			/*  */
			$info=datos_mysql($sql);
			return json_encode($info['responseResult'][0]);
			}
		 }
	}

/*************INICIO MENU***********************/
function men_atencion(){
	$rta=cap_menus('atencion','pro');
	return $rta;
   }
   function focus_atencion(){
	return 'atencion';
   }


/****************FIN MENU*****************+*****/
/*************INICIO DESPLEGABLES***********************/
function opc_sexo($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}
function opc_genero($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=19 and estado='A' ORDER BY 1",$id);
}
function opc_nacionalidad($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=30 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_consulta($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=182 and estado='A'  ORDER BY 1 ",$id);
}
function opc_cups($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=126 and estado='A'  ORDER BY 1 ",$id);
}
function opc_consultamedica($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=127 and estado='A'  ORDER BY 1 ",$id);
}
function opc_aler($id=''){
	return opc_sql("SELECT `descripcion`,descripcion,valor FROM `catadeta` WHERE idcatalogo=170 and estado='A'  ORDER BY 1 ",$id);
}
function opc_alarma5($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='1' and estado='A' ORDER BY 2 ",$id);
}
function opc_letra1($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='1' and estado='A' ORDER BY 2 ",$id);
}
function opc_rango1($id=''){
	 	/*print_r($_REQUEST);
		print_r($_POST);*/
		if (count(divide($_POST['id']))==2){
			return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='2' and estado='A' ORDER BY 1 ",$id);
		} 
	}
function opc_diagnostico1($id=''){
	/* 	print_r($_POST);*/
		if (count(divide($_POST['id']))==2){
			return opc_sql("SELECT `iddiagnostico`,descripcion FROM `diagnosticos` WHERE `iddiag`='3' and estado='A'  ORDER BY descripcion ",$id);
		} 
}
function opc_letra2($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='1' and estado='A' ORDER BY 2 ",$id);
}
function opc_rango2($id=''){
	 if (count(divide($_POST['id']))==2){
		return opc_sql("SELECT iddiagnostico,concat(iddiagnostico,'-',descripcion) FROM `diagnosticos` WHERE `iddiag`='2' and estado='A' ORDER BY 1 ",$id);
	} 
}
function opc_diagnostico2($id=''){
	 if (count(divide($_POST['id']))==2){
		return opc_sql("SELECT `iddiagnostico`,concat(iddiagnostico,'-',descripcion) FROM `diagnosticos` WHERE `iddiag`='3' and estado='A'  ORDER BY descripcion ",$id);
	}
}
function opc_letra3($id=''){
	return opc_sql("SELECT iddiagnostico,descripcion FROM `diagnosticos` WHERE `iddiag`='1' and estado='A' ORDER BY 2 ",$id);
}
function opc_rango3($id=''){
	 if (count(divide($_POST['id']))==2){
		return opc_sql("SELECT iddiagnostico,concat(iddiagnostico,'-',descripcion) FROM `diagnosticos` WHERE `iddiag`='2' and estado='A' ORDER BY 1 ",$id);
	}
}
function opc_diagnostico3($id=''){
	if (count(divide($_POST['id']))==2){
		return opc_sql("SELECT `iddiagnostico`,concat(iddiagnostico,'-',descripcion) FROM `diagnosticos` WHERE `iddiag`='3' and estado='A'  ORDER BY descripcion ",$id);
	}
}

function opc_letra1rango1(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='2' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}

function opc_rango1diagnostico1(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='3' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		// echo $_REQUEST['id'];
		return json_encode($info['responseResult']);
	} 
}

function opc_letra2rango2(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='2' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}

function opc_rango2diagnostico2(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='3' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		// echo $sql;
		return json_encode($info['responseResult']);
	} 
}

	function opc_letra3rango3(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='2' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		return json_encode($info['responseResult']);
	} 
}

function opc_rango3diagnostico3(){
	if($_REQUEST['id']!=''){
		$id=divide($_REQUEST['id']);
		$sql="SELECT iddiagnostico 'id',descripcion 'asc' FROM `diagnosticos` WHERE iddiag='3' and estado='A' and valor='".$id[0]."' ORDER BY 1";
		$info=datos_mysql($sql);		
		// echo $sql;
		return json_encode($info['responseResult']);
	} 
}
function opc_metodoscons($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=129 and estado='A'  ORDER BY 1 ",$id);
}
function opc_vih($id=''){
	return opc_sql("SELECT idcatadeta,descripcion,valor FROM `catadeta` WHERE idcatalogo=187 and estado='A'  ORDER BY 1 ",$id);
}
function opc_rep($id=''){
	return opc_sql("SELECT idcatadeta,descripcion,valor FROM `catadeta` WHERE idcatalogo=188 and estado='A'  ORDER BY 1 ",$id);
}
function opc_evento($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=134 and estado='A'  ORDER BY 1 ",$id);
}
function opc_rutapoblacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=131 and estado='A'  ORDER BY 1 ",$id);
}
function opc_remision($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=132 and estado='A'  ORDER BY 1 ",$id);
}
function opc_vacunacion($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=185 and estado='A'  ORDER BY 1 ",$id);
}
function opc_solicitud($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=133 and estado='A'  ORDER BY 1 ",$id);
}
function opc_medicamentos($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=186 and estado='A'  ORDER BY 1 ",$id);
}
function opc_prioridad($id=''){
	return opc_sql("SELECT idcatadeta,descripcion,valor FROM `catadeta` WHERE idcatalogo=236 and estado='A'  ORDER BY 1 ",$id);
}
function opc_estrategia($id=''){
	return opc_sql("SELECT idcatadeta,descripcion,valor FROM `catadeta` WHERE idcatalogo=203 and estado='A'  ORDER BY 1 ",$id);
}
/****************FIN DESPLEGABLES*****************+*****/
function gra_atencion(){
	// var_dump($_POST);
		$id=divide($_POST['ida']);
	// print_r($_POST['ida']);
	if(count($id)==1){
		$fertil = isset($_POST['fertil']) ? trim($_POST['fertil']) : '';
		$preconcepcional = isset($_POST['preconcepcional']) ? trim($_POST['preconcepcional']) : '';
		$metodo = isset($_POST['metodo']) ? trim($_POST['metodo']) : '';
		$anticonceptivo = isset($_POST['anticonceptivo']) ? trim($_POST['anticonceptivo']) : '';
		$planificacion = isset($_POST['planificacion']) ? trim($_POST['planificacion']) : '';
		$mestruacion = ['type' => empty($_POST['motivo_estado']) ? 'z' : 'i', 'value' => empty($_POST['motivo_estado']) ? null : $_POST['motivo_estado']];
		$gestante = isset($_POST['gestante']) ? trim($_POST['gestante']) : '';

		if (($smu2 = $_POST['rutasirc'] ?? null) && is_array($smu2)){$rutasirc = implode(",", array_map('trim', $smu2));}
		if (($smu1 = $_POST['continuidad'] ?? null) && is_array($smu1)){$contin = implode(",", array_map('trim', $smu1));}
		if (($smu3 = $_POST['cualremision'] ?? null) && is_array($smu3)){$remisi = implode(",", array_map('trim', $smu3));}

	  $sql = "INSERT INTO eac_atencion VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
	  									     	  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
												  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
												   DATE_SUB(NOW(), INTERVAL 5 HOUR),NULL,NULL,'A')";
	  $params=[
		// id_aten
		['type'=>'i', 'value'=>$id[0]], // idpeople
		['type'=>'s', 'value'=>$_POST['idf']], // id_factura
		['type'=>'s', 'value'=>$_POST['fechaatencion']], // fechaatencion
		['type'=>'s', 'value'=>$_POST['tipo_consulta']], // tipo_consulta
		['type'=>'s', 'value'=>$_POST['codigocups']], // codigocups
		['type'=>'s', 'value'=>$_POST['finalidadconsulta']], // finalidadconsulta
		['type'=>'s', 'value'=>$_POST['letra1']], // letra1
		['type'=>'s', 'value'=>$_POST['rango1']], // rango1
		['type'=>'s', 'value'=>$_POST['diagnostico1']], // diagnostico1
		['type'=>'s', 'value'=>$_POST['letra2']], // letra2
		['type'=>'s', 'value'=>$_POST['rango2']], // rango2
		['type'=>'s', 'value'=>$_POST['diagnostico2']], // diagnostico2
		['type'=>'s', 'value'=>$_POST['letra3']], // letra3
		['type'=>'s', 'value'=>$_POST['rango3']], // rango3
		['type'=>'s', 'value'=>$_POST['diagnostico3']], // diagnostico3
		['type'=>'s', 'value'=>$fertil], // fertil
		['type'=>'s', 'value'=>$preconcepcional], // preconcepcional
		['type'=>'s', 'value'=>$metodo], // metodo
		['type'=>'s', 'value'=>$anticonceptivo], // anticonceptivo
		['type'=>'s', 'value'=>$planificacion], // planificacion
		$mestruacion, // mestruacion
		['type'=>'s', 'value'=>$_POST['vih']], // vih
		['type'=>'s', 'value'=>$_POST['resul_vih']], // resul_vih
		['type'=>'s', 'value'=>$_POST['hb']], // hb
		['type'=>'s', 'value'=>$_POST['resul_hb']], // resul_hb
		['type'=>'s', 'value'=>$_POST['trepo_sifil']], // trepo_sifil
		['type'=>'s', 'value'=>$_POST['resul_sifil']], // resul_sifil
		['type'=>'s', 'value'=>$_POST['pru_embarazo']], // pru_embarazo
		['type'=>'s', 'value'=>$_POST['resul_emba']], // resul_emba
		['type'=>'s', 'value'=>$_POST['pru_apetito']], // pru_apetito
		['type'=>'s', 'value'=>$_POST['resul_apetito']], // resul_apetito
		['type'=>'s', 'value'=>$_POST['eventointeres']], // evento_interes
		['type'=>'s', 'value'=>$_POST['evento']], // evento
		['type'=>'s', 'value'=>$_POST['cualevento']], // cualevento
		['type'=>'s', 'value'=>$_POST['sirc']], // sirc
		['type'=>'s', 'value'=>$rutasirc], // ruta_sirc
		['type'=>'s', 'value'=>$_POST['remision']], // remision
		['type'=>'s', 'value'=>$remisi], // cual_remision
		['type'=>'s', 'value'=>$_POST['ordenvacunacion']], // orden_vacunacion
		['type'=>'s', 'value'=>$_POST['vacunacion']], // vacunacion
		['type'=>'s', 'value'=>$_POST['ordenlaboratorio']], // orden_laboratorio
		['type'=>'s', 'value'=>$_POST['laboratorios']], // laboratorios
		['type'=>'s', 'value'=>$_POST['ordenmedicamentos']], // orden_medicamentos
		['type'=>'s', 'value'=>$_POST['medicamentos']], // medicamentos
		['type'=>'s', 'value'=>$_POST['rutacontinuidad']], // ruta_continuidad
		['type'=>'s', 'value'=>$contin], // continuidad
		['type'=>'s', 'value'=>$_POST['ordenimagenes']], // orden_imagenes
		['type'=>'s', 'value'=>$_POST['ordenpsicologia']], // orden_psicologia
		['type'=>'s', 'value'=>$_POST['relevo']], // relevo
		['type'=>'s', 'value'=>$_POST['estrategia']], // estrategia
		['type'=>'s', 'value'=>$_POST['tipo_estrategia']], // motivo_estrategia
		['type'=>'s', 'value'=>$_SESSION['us_sds']] // usu_creo
	];
	return show_sql($sql,$params);
	// return $rta=mysql_prepd($sql, $params);
	}elseif(count($id)==0){
		return "No es posible actualizar consulte con el administrador";
	}
}

function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	  if($a=='atencion' && isset($acc['crear']) && $acc['crear']=='SI'){
		  $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
		  $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  
	  }
	return $rta;
  }

  function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);
	$rta=$c[$d];
   // print_r($c);
   // var_dump($a);   
	   if($a=='atencion' && $b=='acciones'){
		   $rta="<nav class='menu right'>";
		   $rta.="<li class='icono editar ' title='Generar Consulta' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,1000,'atencion',event,this,['idpersona','tipo_doc'],'atencion.php');Color('datos-lis');\"></li>";	//setTimeout(selectDepend,1100,'letra1','rango1','atencion.php');setTimeout(selectDepend,1150,'letra2','rango2','atencion.php');setTimeout(selectDepend,1280,'letra3','rango3','atencion.php');setTimeout(selectDepend,1385,'rango1','diagnostico1','atencion.php');setTimeout(selectDepend,1385,'rango2','diagnostico2','atencion.php');setTimeout(selectDepend,1385,'rango3','diagnostico3','atencion.php');
	   }
	return $rta;
   }
   

  function bgcolor($a,$c,$f='c'){
	$rta = 'red';
	if ($a=='datos-lis'){
		if($c['Cronico']==='SIN'){
			return ($rta !== '') ? "style='background-color: $rta;'" : '';
		}
		if($c['Gestante']==='SIN'){
			return ($rta !== '') ? "style='background-color: $rta;'" : '';
		}
	}
}