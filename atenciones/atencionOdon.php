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

function cmp_atencionO(){
	$rta="";
	$rta .="<div class='encabezado atencion'>Consultas realizadas al paciente</div>
	<div class='contenido' id='atencionO-lis' >".lis_atencionO()."</div></div>";
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
	$c[]=new cmp('fechaatencion','d',20,$x,$w.' '.$o,'Fecha de la Admision','fechaatencion',null,'',true,false,'','col-15');
	$c[]=new cmp('tipo_consulta','s',3,$x,$w.' '.$o,'Tipo de Consulta','tipo_consulta',null,'',true,false,'','col-15');
	$c[]=new cmp('codigocups','s',3,$x,$w.' '.$o,'Código CUPS','cups',null,'',true,false,'','col-2');
	$c[]=new cmp('finalidadconsulta','s',3,$x,$w.' '.$o,'Finalidad de la Consulta','consultamedica',null,'',true,false,'','col-2');
	$c[]=new cmp('fechaingreso','d',20,$x,$w.' '.$o,'Fecha de la consulta','fechaingreso',null,'',true,true,'','col-15');
	$c[]=new cmp('tipo_estrategia','s',3,$x,$w.' eSt '.$o,'Fuente','prioridad',null,'',true,true,'','col-15');

	$c[]=new cmp('letra1','s','3',$x,$w.' '.$o,'Letra CIE(1)','letra1',null,null,true,true,'','col-1',"valPyd(this,'tipo_consulta');valResol('tipo_consulta','letra1','../atenciones/atencionOdon.php');selectDepend('letra1','rango1','../atenciones/atencionOdon.php');");//,['rango1']
 	$c[]=new cmp('rango1','s','3',$x,$w.' '.$o,'Tipo1','rango1',null,null,true,true,'','col-45',"selectDepend('rango1','diagnostico1','../atenciones/atencionOdon.php');");
 	$c[]=new cmp('diagnostico1','s','8',$x,$w.' '.$o,'Diagnostico Principal','diagnostico1',null,null,true,true,'','col-45');
	$c[]=new cmp('letra2','s','3',$x,$w.' '.$o,'Letra CIE(2)','letra2',null,null,false,true,'','col-1',"selectDepend('letra2','rango2','../atenciones/atencionOdon.php');");
 	$c[]=new cmp('rango2','s','3',$x,$w.' '.$o,'Tipo2','rango2',null,null,false,true,'','col-45',"selectDepend('rango2','diagnostico2','../atenciones/atencionOdon.php');");
 	$c[]=new cmp('diagnostico2','s','8',$x,$w.' '.$o,'Diagnostico 2','diagnostico2',null,null,false,true,'','col-45');
	$c[]=new cmp('letra3','s','3',$x,$w.' '.$o,'Letra CIE(3)','letra3',null,null,false,true,'','col-1',"selectDepend('letra3','rango3','../atenciones/atencionOdon.php');");
 	$c[]=new cmp('rango3','s','3',$x,$w.' '.$o,'Tipo3','rango3',null,null,false,true,'','col-45',"selectDepend('rango3','diagnostico3','../atenciones/atencionOdon.php');");
 	$c[]=new cmp('diagnostico3','s','8',$x,$w.' '.$o,'Diagnostico 3','diagnostico3',null,null,false,true,'','col-45');

	$o='Odontologia';
	$c[]=new cmp($o,'e',null,'Acciones Odontologia',$w);
	$c[]=new cmp('n_superficie','nu',3,$x,$w.' lab '.$o,'N° Superficies','solicitud',null,'',false,true,'','col-2','riskPlacaAtenOdon();');
	$c[]=new cmp('n_placa_superf','nu',3,$x,$w.' med '.$o,'N° Superficies con Placa','medicamentos',null,'',false,true,'','col-2','riskPlacaAtenOdon();');
	$c[]=new cmp('riesgo','s',3,$x,$w.' med '.$o,'Riesgo','riesgoOdon',null,'',false,false,'','col-2');	

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
   }

   function lis_atencionO(){
	$id = isset($_POST['id']) ? divide($_POST['id']) : (isset($_POST['ida']) ? divide($_POST['ida']) : null);
	// print_r($id);
	$info=datos_mysql("SELECT COUNT(*) total FROM adm_facturacion F WHERE F.idpeople ='{$id[0]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=4;

	$pag=(isset($_POST['pag-atencionO']))? ($_POST['pag-atencionO']-1)* $regxPag:0;
	$sql="SELECT  F.id_factura ACCIONES,F.cod_admin,F.fecha_consulta fecha,FN_CATALOGODESC(182,F.tipo_consulta) Consulta,
	FN_CATALOGODESC(126,F.cod_cups) 'Código CUPS',FN_CATALOGODESC(127,F.final_consul) Finalidad
	FROM adm_facturacion F
	WHERE F.idpeople ='{$id[0]}'";
		$sql.=" ORDER BY F.fecha_create";
		$sql.=' LIMIT '.$pag.','.$regxPag;
		// echo $sql;
			$datos=datos_mysql($sql);
			return create_table($total,$datos["responseResult"],"atencionO",$regxPag,'../atenciones/atencionOdon.php');
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

function get_atencionO(){
	if($_REQUEST['id']==''){
		return "";
	}else{
		$id=$_REQUEST['id'];
		$sql1="SELECT COUNT(*) rta
		FROM adm_facturacion a
		LEFT JOIN eac_atencion c ON a.idpeople = c.idpeople AND a.id_factura = c.id_factura
		WHERE a.id_factura ='{$id}'";
		$info=datos_mysql($sql1);
		$total=$info['responseResult'][0]['rta'];
		
		if ($total==1){		
			$sql="SELECT concat(a.idpeople) id, b.tipo_doc, b.idpersona, concat_ws(' ',b.nombre1,b.nombre2,b.apellido1,b.apellido2) nombres,
				b.fecha_nacimiento, b.sexo, b.genero, b.nacionalidad, a.id_factura, a.fecha_consulta fechaatencion, a.tipo_consulta, a.cod_cups codigocups, a.final_consul finalidadconsulta,
				c.fecha_atencion, c.codigo_cups, c.finalidad_consulta, c.fuente, c.fecha_ingr fechaingreso,
				c.letra1, c.rango1, c.diagnostico1, c.letra2, c.rango2, c.diagnostico2, c.letra3, c.rango3, c.diagnostico3,
				c.n_superficie, c.n_placa_superf, c.resultado_placa riesgo
				FROM adm_facturacion a
				LEFT JOIN person b ON a.idpeople=b.idpeople
				LEFT JOIN eac_atencion c ON a.idpeople=c.idpeople AND a.id_factura=c.id_factura
				WHERE a.id_factura ='{$id}'";
				$info=datos_mysql($sql);
			return json_encode($info['responseResult'][0]);
		}else{
			$sql="SELECT concat(b.idpeople) id,
			b.tipo_doc,
			b.idpersona,
			concat_ws(' ',b.nombre1,b.nombre2,b.apellido1,b.apellido2) nombres,
			b.fecha_nacimiento, b.sexo, b.genero, b.nacionalidad, a.id_factura, a.fecha_consulta fechaatencion, a.tipo_consulta, a.cod_cups codigocups, a.final_consul finalidadconsulta
			FROM adm_facturacion a
			LEFT JOIN person b ON a.idpeople=b.idpeople 
			WHERE a.id_factura='{$id}'";
			$info=datos_mysql($sql);
			return json_encode($info['responseResult'][0]);
			}
		 }
	}

/*************INICIO MENU***********************/
function men_atencionO(){
	$rta=cap_menus('atencionO','pro');
	return $rta;
   }
   function focus_atencionO(){
	return 'atencionO';
   }

/****************FIN MENU*****************+*****/
/*************INICIO DESPLEGABLES***********************/
function opc_riesgoOdon($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion,valor FROM `catadeta` WHERE idcatalogo=101 and estado='A'  ORDER BY 1 ",$id);
}
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
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=131 and estado='A'  ORDER BY 1 ",$id);
}
function opc_remision($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=132 and estado='A'  ORDER BY 1 ",$id);
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

function gra_atencionO() {
    // Mapeo: campo_formulario => campo_bd (solo campos que existen en eac_atencion)
    $map = [
        'idpeople' => 'idpeople',
        'idf' => 'id_factura', 
        'fechaatencion' => 'fecha_atencion',
        'tipo_consulta' => 'tipo_consulta',
        'codigocups' => 'codigo_cups',
        'finalidadconsulta' => 'finalidad_consulta',
        'fechaingreso' => 'fecha_ingr',
        'letra1' => 'letra1',
        'rango1' => 'rango1', 
        'diagnostico1' => 'diagnostico1',
        'letra2' => 'letra2',
        'rango2' => 'rango2',
        'diagnostico2' => 'diagnostico2',
        'letra3' => 'letra3',
        'rango3' => 'rango3',
        'diagnostico3' => 'diagnostico3',
        'n_superficie' => 'n_superficie',
        'n_placa_superf' => 'n_placa_superf',
        'riesgo' => 'resultado_placa'
    ];

    $id = divide($_POST['ida']);
    if (count($id) != 1 || empty($id[0])) return "Error: idpeople es obligatorio y no puede ser nulo.";
    
    // Validar campos obligatorios (campos NOT NULL en la tabla)
    $obligatorios = ['idf', 'fechaatencion', 'tipo_consulta', 'codigocups', 'finalidadconsulta', 'fechaingreso', 'letra1', 'rango1', 'diagnostico1'];
    foreach ($obligatorios as $campo) {
        $valor = $_POST[$campo] ?? null;
        if ($valor === null || $valor === '') {
			 return "msj['Error: El campo '$campo' es obligatorio y no puede ser nulo o vacío.']";
        }
    }

    $params = [];
    $cols = [];
    
    // Agregar campos del mapeo
    foreach ($map as $form => $col) {
        $cols[] = $col;
        if ($form == 'idpeople') {
            $params[] = ['type' => 'i', 'value' => $id[0]];
        } else {
            $valor = $_POST[$form] ?? null;
            if ($valor === '' || $valor === null) {
                $params[] = ['type' => 'z', 'value' => null];
            } else {
                $params[] = ['type' => 's', 'value' => $valor];
            }
        }
    }

    // Agregar campo fuente (requerido en BD pero no en formulario)
    $cols[] = 'fuente';
    $params[] = ['type' => 's', 'value' => '1']; // Valor por defecto

    // Campos de auditoría
    $cols[] = 'usu_creo';
    $cols[] = 'fecha_create';
    $cols[] = 'estado';
    
    $params[] = ['type' => 's', 'value' => $_SESSION['us_sds']];
    $params[] = ['type' => 's', 'value' => date('Y-m-d H:i:s', strtotime('-5 hours'))]; // Fecha menos 5 horas
    $params[] = ['type' => 's', 'value' => 'A'];

    $placeholders = implode(', ', array_fill(0, count($params), '?'));
    $sql = "INSERT INTO eac_atencion (
        " . implode(', ', $cols) . "
    ) VALUES (
        $placeholders
    )";
    
    $rta = mysql_prepd($sql, $params);
    return $rta;
}


function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	  if($a=='atencionO' && isset($acc['crear']) && $acc['crear']=='SI'){
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
	   if($a=='atencionO' && $b=='acciones'){
		   $rta="<nav class='menu right'>";
		   $rta.="<li class='icono editar ' title='Generar Consulta' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,1000,'atencionO',event,this,['idpersona','tipo_doc'],'../atenciones/atencionOdon.php');Color('datos-lis');\"></li>";
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