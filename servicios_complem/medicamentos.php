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

function focus_medicamentctrl(){
  return 'medicamentctrl';
}
 
function men_medicamentctrl(){
  $rta=cap_menus('medicamentctrl','pro');
  return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='medicamentctrl' && isset($acc['crear']) && $acc['crear']=='SI') {  
   $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
  }
  $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";  
  return $rta;
}

function lis_medicamentctrl(){
    $id=divide($_POST['id']);
    $total="SELECT COUNT(*) AS total FROM (
      SELECT id_medicam 'Cod Registro',fecha_orden, CONCAT(cantidad_prescrita, ' unidades') as cantidad_prescrita,fecha_entrega,FN_CATALOGODESC(88, numero_entrega) as numero_entrega,
      CONCAT(cantidad_entregada, ' unidades') as cantidad_entregada,FN_CATALOGODESC(89, tipo_medicamento) as tipo_medicamento,FN_CATALOGODESC(90, medicamento) as medicamento,
             FN_CATALOGODESC(91, estado_entrega) as estado_entrega
      FROM medicamentos_ctrl 
      WHERE idpeople='{$id[0]}' AND estado='A'
    ) AS Subquery";
    
    $info=datos_mysql($total);
    $total=$info['responseResult'][0]['total']; 
    $regxPag=5;
    $pag=(isset($_POST['pag-medicamentctrl']))? ($_POST['pag-medicamentctrl']-1)* $regxPag:0;

    $sql="SELECT id_medicam 'Cod Registro', fecha_orden, 
                 CONCAT(cantidad_prescrita, ' unidades') as cantidad_prescrita,
                 fecha_entrega,
                 FN_CATALOGODESC(88, numero_entrega) as numero_entrega,
                 CONCAT(cantidad_entregada, ' unidades') as cantidad_entregada,
                 FN_CATALOGODESC(89, tipo_medicamento) as tipo_medicamento,
                 FN_CATALOGODESC(90, medicamento) as medicamento,
                 FN_CATALOGODESC(91, estado_entrega) as estado_entrega
          FROM medicamentos_ctrl 
          WHERE idpeople='{$id[0]}' AND estado='A'";
    $sql.=" ORDER BY fecha_entrega DESC LIMIT $pag, $regxPag";
    
    $datos=datos_mysql($sql);
    return create_table($total,$datos["responseResult"],"medicamentctrl",$regxPag,'medicamentctrl.php');
}

function cmp_medicamentctrl(){
    $rta="<div class='encabezado medid'>CONTROL DE ENTREGAS DE MEDICAMENTOS</div>
    <div class='contenido' id='medicamentctrl-lis'>".lis_medicamentctrl()."</div></div>";
    
    $t=['id_medicam'=>'','idpersona'=>'','nombre'=>'','tipodoc'=>'','fechanacimiento'=>'','edad'=>'','sexo'=>'',
        'cantidad_prescrita'=>'','fecha_entrega'=>'','numero_entrega'=>'','cantidad_entregada'=>'',
        'tipo_medicamento'=>'','medicamento'=>'','requiere_aprobacion'=>'','cantidadXaprobar'=>'',
        'estado_entrega'=>'','observaciones'=>''];
    
    $d=get_persona();
    if ($d==""){$d=$t;}
    $e="";
    $w='medicamentctrl';
    $o='datos';
    $key='med';
    $edad='AÑOS= '.$d['anos'].' MESES= '.$d['meses'].' DIAS= '.$d['dias'];
    $days=fechas_app('med');
    // var_dump($_REQUEST['id']);
    // Datos de identificación
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('idpersona','n','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-3');
    $c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-3');
    $c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
    $c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'Sexo','sexo',null,'',true,false,'','col-2');
    $c[]=new cmp('fechanacimiento','d',10,$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',true,false,'','col-3');
    $c[]=new cmp('edad','t',30,$edad,$w.' '.$o,'edad en Años','edad',null,'',true,false,'','col-3');
    
    // Datos de medicamentos
    $o='medicamentos';
    $c[]=new cmp($o,'e',null,'CONTROL DE MEDICAMENTOS',$w);
    $c[]=new cmp('fecha_orden','d',10,$e,$w.' '.$o,'Fecha de orden','fecha_orden',null,'',true,true,'','col-2',"validDate(this,-30,10);");
    $c[]=new cmp('cantidad_prescrita','nu',10,$e,$w.' '.$o,'Cantidad Prescrita','cantidad_prescrita',null,'',true,true,'','col-2');
    $c[]=new cmp('fecha_entrega','d',10,$e,$w.' '.$o,'Fecha de Entrega','fecha_entrega',null,'',true,true,'','col-2',"validDate(this,$days,0);");
    $c[]=new cmp('numero_entrega','s',2,$e,$w.' '.$o,'Número de Entrega','entrega',null,'',true,true,'','col-2');
    $c[]=new cmp('cantidad_entregada','nu',30,$e,$w.' '.$o,'Cantidad Entregada','cantidad_entregada',null,'',true,true,'','col-2');
    $c[]=new cmp('cantidadXaprobar','nu',30,$e,$w.' '.$o,'Pendiente por Entregar','cantidadXaprobar',null,'',false,false,'','col-2');
    $c[]=new cmp('tipo_medicamento','s',2,$e,$w.' '.$o,'Medicamento NO POS','tipo_medicamento',null,'',true,true,'','col-2');
    $c[]=new cmp('cant_ordenada','nu',30,$e,$w.' '.$o,'Cantidad Ordenada','cant_ordenada',null,'',true,true,'','col-2');  
    $c[]=new cmp('cod_admision','s',2,$e,$w.' '.$o,'Cod. Admisión','cod_admision',null,'',true,true,'','col-4');
    $c[]=new cmp('observaciones','a',255,$e,$w.' '.$o,'Observaciones','observaciones',null,'',true,true,'','col-12');
    
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

function get_persona(){
  if($_REQUEST['id']==''){
    return "";
  }else{
    $id=divide($_REQUEST['id']);
    $sql="SELECT P.idpeople,P.idpersona idpersona,P.tipo_doc tipodoc,CONCAT_WS(' ',nombre1,nombre2,apellido1,apellido2) nombre,P.fecha_nacimiento fechanacimiento,
		P.sexo sexo,
    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS anos,
    TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE())-(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) * 12) AS meses,
    DATEDIFF(CURDATE(),DATE_ADD(fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) YEAR)) % 30 AS dias
		FROM person P
		left join vspeve E ON P.idpeople = E.idpeople
    WHERE P.idpeople='{$id[0]}'"; 
    // echo $sql;
    // print_r($_REQUEST);
    $info=datos_mysql($sql);
    return $info['responseResult'][0];
  }
}


function opc_tipodoc($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_sexo($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY 1",$id);
}


// Funciones para opciones de select
function opc_numero_entrega($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=305 AND estado='A' ORDER BY 1",$id);
}

function opc_tipo_medicamento($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 AND estado='A' ORDER BY 1",$id);
}

function opc_medicamento($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=90 AND estado='A' ORDER BY 1",$id);
}

function opc_requiere_aprobacion($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=92 AND estado='A' ORDER BY 1",$id);
}

function opc_estado_entrega($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=91 AND estado='A' ORDER BY 1",$id);
}

function opc_entrega($id=''){
  $idp=divide($_REQUEST['id']);
  $sql="SELECT count(*) as total FROM `medicamentos_ctrl` WHERE  idpeople='{$idp[0]}' AND estado='A' AND idatencion=";

  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=305 AND VALOR=0 AND estado='A' ORDER BY 1",$id);

  /* $id=divide($a);
  $sql="SELECT idcatadeta, descripcion FROM `catadeta` WHERE idcatalogo=305 AND estado='A' AND VALOR=0 ORDER BY 2";
		 FROM `eac_ruteo` WHERE  id_ruteo='{$id[0]}'";
		 $info=datos_mysql($sql);
		 $cod= $info['responseResult'][0]['cod'];
		 return $cod; */
}

function opc_cod_admision($id=''){
	$cod=divide($_REQUEST['id']);
  return opc_sql("SELECT a.id_aten,CONCAT_WS(' - ',f.cod_admin,FN_CATALOGODESC(127,f.final_consul)) AS descripcion FROM eac_atencion a LEFT JOIN adm_facturacion f ON a.id_factura=f.id_factura WHERE a.idpeople='{$cod[0]}' AND a.medicamentos=1", $id);
}

/* function gra_medicamentctrl(){
  // Validación de campos obligatorios
  $required = [
    'fecha_orden', 'cantidad_prescrita', 'fecha_entrega', 'numero_entrega',
    'cantidad_entregada', 'tipo_medicamento', 'medicamento', 'requiere_aprobacion',
    'cantidadXaprobar', 'cant_ordenada', 'cod_admision', 'estado_entrega', 'observaciones'
  ];

  foreach ($required as $field) {
    if (isset($_POST[$field]) && trim($_POST[$field]) === '' && $field !== 'observaciones') {
      return ['error' => 'El campo '.$field.' es obligatorio.'];
    }
  }

  // Validar formato de fecha (YYYY-MM-DD)
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha_entrega'])) {
    return ['error' => 'El formato de la fecha de entrega es inválido.'];
  }
  if (!empty($_POST['fecha_orden']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha_orden'])) {
    return ['error' => 'El formato de la fecha de orden es inválido.'];
  }

  // Validar valores numéricos
  if (!is_numeric($_POST['cantidad_prescrita']) || $_POST['cantidad_prescrita'] <= 0) {
    return ['error' => 'La cantidad prescrita debe ser un número positivo.'];
  }
  if (!empty($_POST['cantidad_entregada']) && 
    (!is_numeric($_POST['cantidad_entregada']) || $_POST['cantidad_entregada'] < 0)) {
    return ['error' => 'La cantidad entregada debe ser un número positivo.'];
  }
  if (!empty($_POST['cant_ordenada']) && 
    (!is_numeric($_POST['cant_ordenada']) || $_POST['cant_ordenada'] < 0)) {
    return ['error' => 'La cantidad ordenada debe ser un número positivo.'];
  }
  if (!empty($_POST['cantidadXaprobar']) && 
    (!is_numeric($_POST['cantidadXaprobar']) || $_POST['cantidadXaprobar'] < 0)) {
    return ['error' => 'La cantidad pendiente por entregar debe ser un número positivo.'];
  }

  $id = divide($_POST['id']);

  echo "OK JHDSFJKHJKHJKHDGSKJGSDHJK";
  if (count($id) == 1) {
    // Actualización
    $sql = "UPDATE medicamentos_ctrl SET 
        fecha_orden=?, cantidad_prescrita=?, fecha_entrega=?, numero_entrega=?, 
        cantidad_entregada=?, tipo_medicamento=?, medicamento=?, requiere_aprobacion=?, 
        cantidadXaprobar=?, cant_ordenada=?, cod_admision=?, estado_entrega=?, observaciones=?, 
        usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
        WHERE id_medicam=?";
    $params = [
      ['type' => 's', 'value' => trim($_POST['fecha_orden'])],
      ['type' => 'i', 'value' => intval($_POST['cantidad_prescrita'])],
      ['type' => 's', 'value' => trim($_POST['fecha_entrega'])],
      ['type' => 's', 'value' => trim($_POST['numero_entrega'])],
      ['type' => 'i', 'value' => intval($_POST['cantidad_entregada'] ?? 0)],
      ['type' => 's', 'value' => trim($_POST['tipo_medicamento'])],
      ['type' => 's', 'value' => trim($_POST['medicamento'])],
      ['type' => 's', 'value' => trim($_POST['requiere_aprobacion'] ?? '')],
      ['type' => 'i', 'value' => intval($_POST['cantidadXaprobar'] ?? 0)],
      ['type' => 'i', 'value' => intval($_POST['cant_ordenada'] ?? 0)],
      ['type' => 's', 'value' => trim($_POST['cod_admision'] ?? '')],
      ['type' => 's', 'value' => trim($_POST['estado_entrega'])],
      ['type' => 's', 'value' => trim($_POST['observaciones'] ?? '')],
      ['type' => 's', 'value' => $_SESSION['us_sds']],
      ['type' => 'i', 'value' => intval($id[0])]
    ];
    var_dump($show_sql($sql, $params));
    $rta = mysql_prepd($sql, $params);
  } else if (count($id) == 2) {
    // Inserción
    $sql = "INSERT INTO medicamentos_ctrl (
        id_medicam, idpeople, fecha_orden, cantidad_prescrita, fecha_entrega, numero_entrega, cantidad_entregada, tipo_medicamento, medicamento, requiere_aprobacion, cantidadXaprobar, cant_ordenada, cod_admision, estado_entrega, observaciones, usu_create, usu_update, fecha_update, estado, fecha_create
      ) VALUES (
        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(), INTERVAL 5 HOUR)
      )";
    $params = [
      ['type' => 'i', 'value' => null], // id_medicam auto-increment
      ['type' => 's', 'value' => $id[0]], // idpeople
      ['type' => 's', 'value' => trim($_POST['fecha_orden'])],
      ['type' => 'i', 'value' => intval($_POST['cantidad_prescrita'])],
      ['type' => 's', 'value' => trim($_POST['fecha_entrega'])],
      ['type' => 's', 'value' => trim($_POST['numero_entrega'])],
      ['type' => 'i', 'value' => intval($_POST['cantidad_entregada'] ?? 0)],
      ['type' => 's', 'value' => trim($_POST['tipo_medicamento'])],
      ['type' => 's', 'value' => trim($_POST['medicamento'])],
      ['type' => 's', 'value' => trim($_POST['requiere_aprobacion'] ?? '')],
      ['type' => 'i', 'value' => intval($_POST['cantidadXaprobar'] ?? 0)],
      ['type' => 'i', 'value' => intval($_POST['cant_ordenada'] ?? 0)],
      ['type' => 's', 'value' => trim($_POST['cod_admision'] ?? '')],
      ['type' => 's', 'value' => trim($_POST['estado_entrega'])],
      ['type' => 's', 'value' => trim($_POST['observaciones'] ?? '')],
      ['type' => 's', 'value' => $_SESSION['us_sds']], // usu_create
      ['type' => 's', 'value' => null], // usu_update
      ['type' => 's', 'value' => null], // fecha_update
      ['type' => 's', 'value' => 'A'] // estado
    ];
    var_dump($show_sql($sql, $params));
    $rta = mysql_prepd($sql, $params);
  } else {
    var_dump('FLKUHJLKHGJGLKJ');
    $rta="Error: msj['No existe un equipo actualmente para el usuario que realizo el seguimiento']";
  }
  return 'HOLA MUNDO';
} */

function gra_medicamentctrl(){
   // Validación de campos obligatorios
  $required = [
    'fecha_orden', 'cantidad_prescrita', 'fecha_entrega', 'numero_entrega',
    'cantidad_entregada', 'tipo_medicamento', 'medicamento', 'requiere_aprobacion',
    'cantidadXaprobar', 'cant_ordenada', 'cod_admision', 'estado_entrega', 'observaciones'
  ];

  foreach ($required as $field) {
    if (isset($_POST[$field]) && trim($_POST[$field]) === '' && $field !== 'observaciones') {
      return ['error' => 'El campo '.$field.' es obligatorio.'];
    }
  }
}

function get_medicamentctrl(){
    if($_REQUEST['id']==''){
        return "";
    } else {
        $id=divide($_REQUEST['id']);
        $sql="SELECT id_medicam, idpeople, cantidad_prescrita, fecha_entrega,numero_entrega, cantidad_entregada, tipo_medicamento,medicamento, requiere_aprobacion, cantidadXaprobar,estado_entrega, observaciones
              FROM medicamentos_ctrl 
              WHERE id_medicam='{$id[0]}'";
        $info=datos_mysql($sql);
        return json_encode($info['responseResult'][0]);
    } 
}

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    
    if ($a=='medicamentctrl-lis' && $b=='acciones'){
        $rta="<nav class='menu right'>";    
        $rta.="<li class='icono editar' title='Editar' id='".$c['Cod Registro']."' Onclick=\"setTimeout(getData,500,'medicamentctrl',event,this,['cantidad_prescrita','fecha_entrega','numero_entrega','cantidad_entregada','tipo_medicamento','medicamento','requiere_aprobacion','cantidadXaprobar','estado_entrega','observaciones'],'medicamentctrl.php');\"></li>";
        $rta.="</nav>";
    }
    return $rta;
}

function bgcolor($a,$c,$f='c'){
    $rta="";
    return $rta;
}