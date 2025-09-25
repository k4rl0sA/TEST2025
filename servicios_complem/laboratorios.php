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

function focus_laboratorios(){
  return 'laboratorios';
}
 
function men_laboratorios(){
  $rta=cap_menus('laboratorios','pro');
  return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
  $rta = "";
  $acc=rol($a);
  if ($a=='laboratorios' && isset($acc['crear']) && $acc['crear']=='SI') {  
   $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
  }
  $rta .= "<li class='icono $a actualizar'  title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";  
  return $rta;
}

function lis_laboratorios(){
    $id=divide($_POST['id']);
    $total="SELECT COUNT(*) AS total FROM (
      SELECT id_lab 'Cod Laboratorio',FN_CATALOGODESC(307, tipo_lab) Laboratorio, otro_lab,fecha_orden,FN_CATALOGODESC(170,lab_tomado) Tomado, fecha_toma,FN_CATALOGODESC(170,cuenta_resul) Resultado, fecha_resul,FN_CATALOGODESC(170,dato_crit) Daño_Critico, gestion, gest_cump, obs AS Observaciones
      FROM hog_laboratorios 
      WHERE idpeople='{$id[0]}' AND estado='A'
    ) AS Subquery";
    $info=datos_mysql($total);
    $total=$info['responseResult'][0]['total']; 
    $regxPag=5;
    $pag=(isset($_POST['pag-laboratorios']))? ($_POST['pag-laboratorios']-1)* $regxPag:0;
    $sql="SELECT id_lab 'Cod Laboratorio',FN_CATALOGODESC(307, tipo_lab) Laboratorio, otro_lab Otro,fecha_orden,FN_CATALOGODESC(170,lab_tomado) Tomado, fecha_toma,FN_CATALOGODESC(170,cuenta_resul) Resultado, fecha_resul,FN_CATALOGODESC(170,dato_crit) Daño_Critico, gestion, gest_cump Gestionado
          FROM hog_laboratorios 
          WHERE idpeople='{$id[0]}' AND estado='A'";
    $sql.=" ORDER BY fecha_orden DESC LIMIT $pag, $regxPag";
    $datos=datos_mysql($sql);
    return create_table($total,$datos["responseResult"],"laboratorios",$regxPag,'laboratorios.php');
}

function cmp_laboratorios(){
    $rta="<div class='encabezado labid'>CONTROL DE LABORATORIOS</div>
    <div class='contenido' id='laboratorios-lis'>".lis_laboratorios()."</div></div>";
    $t=['id_lab'=>'','idpersona'=>'','nombre'=>'','tipodoc'=>'','fechanacimiento'=>'','edad'=>'','tipo_lab'=>'','otro_lab'=>'','fecha_orden'=>'','lab_tomado'=>'','fecha_toma'=>'','cuenta_resul'=>'','fecha_resul'=>'','dato_crit'=>'','gestion'=>'','gest_cump'=>'','obs'=>''];
    $d=get_persona();
    if ($d==""){$d=$t;}
    $e="";
    $w='laboratorios';
    $o='datos';
    $key='lab';
    $edad='AÑOS= '.$d['anos'].' MESES= '.$d['meses'].' DIAS= '.$d['dias'];
    $days=fechas_app('lab');
    // Datos de identificación
    $c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
    $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
    $c[]=new cmp('idpersona','n','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-3');
    $c[]=new cmp('tipodoc','s','3',$d['tipodoc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-3');
    $c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
    $c[]=new cmp('fechanacimiento','d',10,$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-3');
    $c[]=new cmp('edad','t',30,$edad,$w.' '.$o,'edad en Años','edad',null,'',false,false,'','col-3');
    $o='laboratorio';
    $c[]=new cmp($o,'e',null,'LABORATORIOS',$w);
    $c[]=new cmp('cod_admision','s',2,$e,$w.' '.$o,'Cod. Admisión','cod_admision',null,'',true,true,'','col-4');
    $c[]=new cmp('tipo_lab','s',3,$e,$w.' '.$o,'Tipo Laboratorio','tipo_lab',null,'',true,true,'','col-2',"enabOthLaborat();");
    $c[]=new cmp('otro_lab','t',255,$e,$w.' oTH '.$o,'Otro Laboratorio, ¿Cuál?','otro_lab',null,'',false,false,'','col-4');
    $c[]=new cmp('fecha_orden','d',10,$e,$w.' '.$o,'Fecha de orden','fecha_orden',null,'',true,true,'','col-2',"validDate(this,-30,0);");
    $o='ctrlLab';
    $c[]=new cmp($o,'e',null,'CONTROL DE LABORATORIOS',$w);
    $c[]=new cmp('lab_tomado','s',3,$e,$w.' '.$o,'¿Laboratorio tomado?','lab_tomado',null,'',true,true,'','col-2',"enabFechaTomaLab();enabTomaLab();");
    $c[]=new cmp('fecha_toma','d',10,$e,$w.' ToM '.$o,'Fecha de Toma','fecha_toma',null,'',false,false,'','col-2',"validDate(this,-30,0);");
    /* $c[]=new cmp('cuenta_resul','s',3,$e,$w.' ToM '.$o,'¿Cuenta con resultado?','cuenta_resul',null,'',true,true,'','col-2',"enabFechaResulLab();");    
    $c[]=new cmp('fecha_resul','d',10,$e,$w.' RTa  '.$o,'Fecha de Resultado','fecha_resul',null,'',false,false,'','col-2');
    $c[]=new cmp('dato_crit','s',10,$e,$w.' ToM '.$o,'Dato Crítico','dato_crit',null,'',true,true,'','col-2',"enabGestionLab();");
    $c[]=new cmp('gestion','s',3,$e,$w.' dCR '.$o,'Cita de Control','gestion',null,'',false,false,'','col-2');
    $c[]=new cmp('gest_cump','d',3,$e,$w.' dCR '.$o,'Fecha Gestión','gest_cump',null,'',false,false,'','col-2'); */
    $c[]=new cmp('obs','a',255,$e,$w.' '.$o,'Observaciones','obs',null,'',true,true,'','col-12');
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
    $info=datos_mysql($sql);
    return $info['responseResult'][0];
  }
}

function gra_laboratorios(){
    // Validación de campos obligatorios
     $required = ['cod_admision','tipo_lab','fecha_orden','lab_tomado'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            return ['error' => 'El campo '.$field.' es obligatorio.'];
        }
    }
    // Validación de fechas si lab_tomado=1 fecha_toma debe ser una fecha valida, tambien para cuenta_resul=1 fecha_resul debe ser una fecha valida, si dato_crit=1 gestion debe ser obligatorio y si gestion=1 gest_cump debe ser obligatorio
    if ($_POST['lab_tomado'] == '1' && (empty($_POST['fecha_toma']) || !validateDate($_POST['fecha_toma']))) {
      return "msj['Error: La fecha de toma es obligatoria y debe ser una fecha válida cuando el laboratorio ha sido tomado.']";
    } 
    if ($_POST['cuenta_resul'] == '1' && (empty($_POST['fecha_resul']) || !validateDate($_POST['fecha_resul']))) {
      return "msj['Error: La fecha de resultado es obligatoria y debe ser una fecha válida cuando se cuenta con resultado.']";
    }
    if ($_POST['dato_crit'] == '1' && empty($_POST['gestion'])) {
      return "msj['Error: La gestión es obligatoria cuando hay dato crítico.']";
    }
 

    $id = divide($_POST['id']);
    if (count($id) == 1) {
        // Actualización
  $sql = "UPDATE hog_laboratorios SET lab_tomado=?, fecha_toma=?, cuenta_resul=?, fecha_resul=?, dato_crit=?, gestion=?, gest_cump=?, obs=?, usu_update= {$_SESSION['us_sds']}, fecha_update=SUB_DATE(NOW(),INTERVAL 5 HOUR) WHERE id_lab=?";
        $params = [
            ['type' => 's', 'value' => trim($_POST['lab_tomado'] ?? '')],
            ['type' => 's', 'value' => trim($_POST['fecha_toma'] ?? NULL)],
            ['type' => 's', 'value' => trim($_POST['cuenta_resul'] ?? '')],
            ['type' => 's', 'value' => trim($_POST['fecha_resul'] ?? null)],
            ['type' => 'i', 'value' => intval($_POST['dato_crit'] ?? 0)],
            ['type' => 's', 'value' => trim($_POST['gestion'] ?? '')],
            ['type' => 's', 'value' => trim($_POST['gest_cump'] ?? '')],
            ['type' => 's', 'value' => trim($_POST['obs'] ?? NULL)],
            ['type' => 'i', 'value' => intval($id[0])]
        ];
    // Mostrar la consulta generada para depuración
    $debug_sql = show_sql($sql, $params);
    log_error('SQL DEBUG: ' . $debug_sql);
    } else if (count($id) == 2) {
        // Inserción
        $sql = "INSERT INTO hog_laboratorios VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";
        $params = [
            ['type' => 'i', 'value' => null], // id_lab
            ['type' => 'i', 'value' => intval($_POST['cod_admision'])], // id_aten
            ['type' => 'i', 'value' => intval($id[0])], // idpeople
            ['type' => 's', 'value' => trim($_POST['tipo_lab'])],
            ['type' => 's', 'value' => trim($_POST['otro_lab'])],
            ['type' => 's', 'value' => trim($_POST['fecha_orden'])],
            ['type' => 's', 'value' => trim($_POST['lab_tomado'] ?? null)],
            ['type' => 's', 'value' => trim($_POST['fecha_toma'] ?? '0000-00-00')],
            ['type' => 's', 'value' => trim($_POST['cuenta_resul'] ?? null)],
            ['type' => 's', 'value' => trim($_POST['fecha_resul'] ?? '0000-00-00')],
            ['type' => 'i', 'value' => intval($_POST['dato_crit'] ?? null)],
            ['type' => 's', 'value' => trim($_POST['gestion'] ?? null)],
            ['type' => 's', 'value' => trim($_POST['gest_cump'] ?? null)],
            ['type' => 's', 'value' => trim($_POST['obs'] ?? null)],
            ['type' => 's', 'value' => $_SESSION['us_sds']], // usu_create
            ['type' => 's', 'value' => null], // usu_update
            ['type' => 's', 'value' => null], // fecha_update
            ['type' => 's', 'value' => 'A'] // estado
        ];
    } else {
        return ['error' => 'ID inválido.'];
    }
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

function get_laboratorios(){
    if($_REQUEST['id']==''){
        return "";
    } else {
        $id=divide($_REQUEST['id']);
        $sql="SELECT id_lab,P.idpersona idpersona,P.tipo_doc tipodoc,CONCAT_WS(' ',nombre1,nombre2,apellido1,apellido2) nombre,
         P.fecha_nacimiento fechanacimiento,CONCAT(
    'AÑOS= ', TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()),
    ' MESES= ', TIMESTAMPDIFF(MONTH, fecha_nacimiento, CURDATE())-(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) * 12),
    ' DIAS= ', DATEDIFF(CURDATE(),DATE_ADD(fecha_nacimiento, INTERVAL TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) YEAR)) % 30
  ) AS edad,idatencion,tipo_lab, otro_lab, fecha_orden, lab_tomado, fecha_toma, cuenta_resul, fecha_resul, dato_crit, gestion, gest_cump, obs
FROM hog_laboratorios 
              LEFT JOIN person P ON hog_laboratorios.idpeople=P.idpeople
              WHERE id_lab='{$id[0]}'";
        $info=datos_mysql($sql);
        return json_encode($info['responseResult'][0]);
    } 
}

function opc_tipodoc($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
}
function opc_tipo_lab($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=307 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
}
function opc_lab_tomado($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_cuenta_resul($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_dato_crit($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_gestion($id=''){
  return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_cod_admision($id=''){
	$cod=divide($_REQUEST['id']);
  return opc_sql("SELECT a.id_aten,CONCAT_WS(' - ',f.cod_admin,FN_CATALOGODESC(127,f.final_consul)) AS descripcion FROM eac_atencion a LEFT JOIN adm_facturacion f ON a.id_factura=f.id_factura WHERE a.idpeople='{$cod[0]}' AND a.laboratorios=1", $id);
}


/*  function cmp_resultLab(){
  $rta="";
  $t=['id_lab'=>'','cuenta_resul'=>'','fecha_resul'=>'','dato_crit'=>'','gestion'=>'','gest_cump'=>'','obs'=>''];
  $d=get_respuesta();
  if ($d==""){$d=$t;}
  $e="";
  $w='respuestas';
  $o='rtas';
  $key='lab';
  $days=fechas_app('lab');
  $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
  $c[]=new cmp('cuenta_resul','s',3,$e,$w.' ToM '.$o,'¿Cuenta con resultado?','cuenta_resul',null,'',true,true,'','col-2',"enabFechaResulLab();");    
    $c[]=new cmp('fecha_resul','d',10,$e,$w.' RTa  '.$o,'Fecha de Resultado','fecha_resul',null,'',false,false,'','col-2');
    $c[]=new cmp('dato_crit','s',10,$e,$w.' ToM '.$o,'Dato Crítico','dato_crit',null,'',true,true,'','col-2',"enabGestionLab();");
    $c[]=new cmp('gestion','s',3,$e,$w.' dCR '.$o,'Cita de Control','gestion',null,'',false,false,'','col-2');
    $c[]=new cmp('gest_cump','d',3,$e,$w.' dCR '.$o,'Fecha Gestión','gest_cump',null,'',false,false,'','col-2');
 }
*/
 function get_respuesta(){
    if($_REQUEST['id']==''){
        return "";
    } else {
        $id=divide($_REQUEST['id']);
        $sql="SELECT  cuenta_resul, fecha_resul, dato_crit, gestion, gest_cump, obs
FROM hog_laboratorios 
              LEFT JOIN person P ON hog_laboratorios.idpeople=P.idpeople
              WHERE id_lab='{$id[0]}'";
        $info=datos_mysql($sql);
        return json_encode($info['responseResult'][0]);
    } 
}


function focus_resultLab(){
  return 'resultLab';
}

function men_resultLab(){
  $rta=cap_menus('resultLab','pro');
  return $rta;
}
 






/* function focus_resultLab(){
	return 'resultLab';
   }
   
   
   function men_resultLab(){
	$rta=cap_menus('resultLab','pro');
	return $rta;
   } */

function cmp_resultLab(){
  $rta ="";
    $w="placuifam";
      $o='accide';
      $e="";
      $key='pln';
      $o='resultLab';
    //   var_dump($_POST);
      $t=['compromiso'=>''];
	$d=get_compromiso();
	if ($d==""){$d=$t;}
	$days=fechas_app('vivienda');
      $c[]=new cmp($o,'e',null,'PLAN DE CUIDADO FAMILIAR CONCERTADO',$w);
      $c[]=new cmp('idrta','h',15,$_POST['id'],$w.' '.$key.' '.$o,'id','id',null,'####',false,false);
      
      $c[]=new cmp('cuenta_resul','s',3,$e,$w.' ToM '.$o,'¿Cuenta con resultado?','cuenta_resul',null,'',true,true,'','col-2',"enabFechaResulLab();");    
      $c[]=new cmp('fecha_resul','d',10,$e,$w.' RTa  '.$o,'Fecha de Resultado','fecha_resul',null,'',false,false,'','col-2');
      $c[]=new cmp('dato_crit','s',10,$e,$w.' ToM '.$o,'Dato Crítico','dato_crit',null,'',true,true,'','col-2',"enabGestionLab();");
      $c[]=new cmp('gestion','s',3,$e,$w.' dCR '.$o,'Cita de Control','gestion',null,'',false,false,'','col-2');
      $c[]=new cmp('gest_cump','d',3,$e,$w.' dCR '.$o,'Fecha Gestión','gest_cump',null,'',false,false,'','col-2');

      for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
      return $rta;
  }

  function get_compromiso(){
    // var_dump($_REQUEST);
    if($_REQUEST['id']==''){
        return "";
    }else{
        $id=divide($_REQUEST['id']);
        //  `fechaatencion`, `codigocups`, `finalidadconsulta`, `peso`, `talla`, `sistolica`, `diastolica`, `abdominal`, `brazo`, `diagnosticoprincipal`, `diagnosticorelacion1`, `diagnosticorelacion2`, `diagnosticorelacion3`, `fertil`, `preconcepcional`, `metodo`, `anticonceptivo`, `planificacion`, `mestruacion`, `gestante`, `gestaciones`, `partos`, `abortos`, `cesarias`, `vivos`, `muertos`, `vacunaciongestante`, `edadgestacion`, `ultimagestacion`, `probableparto`, `prenatal`, `fechaparto`, `rpsicosocial`, `robstetrico`, `rtromboembo`, `rdepresion`, `sifilisgestacional`, `sifiliscongenita`, `morbilidad`, `hepatitisb`, `vih`, `cronico`, `asistenciacronica`, `tratamiento`, `vacunascronico`, `menos5anios`, `esquemavacuna`, `signoalarma`, `cualalarma`, `dxnutricional`, `eventointeres`, `evento`, `cualevento`, `sirc`, `rutasirc`, `remision`, `cualremision`, `ordenpsicologia`, `ordenvacunacion`, `vacunacion`, `ordenlaboratorio`, `laboratorios`, `ordenimagenes`, `imagenes`, `ordenmedicamentos`, `medicamentos`, `rutacontinuidad`, `continuidad`, `relevo`  ON a.idpersona = b.idpersona AND a.tipodoc = b.tipo_doc
        $sql="SELECT  compromiso
        FROM hog_planconc
        WHERE idcon ='{$id[1]}'";
        // echo $sql;
        $info=datos_mysql($sql);
        return $info['responseResult'][0];			
    }
}

  function gra_resultLab(){
	$id=divide($_POST['idcom']);
    // var_dump($id);
    $info=datos_mysql("select equipo from usuarios where id_usuario='{$_SESSION['us_sds']}'");
    if(isset($info['responseResult'][0])){ 
      $equipo=$info['responseResult'][0]['equipo'];
      $sql = "INSERT INTO hog_segcom VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
      $params = [
        ['type' => 'i', 'value' => NULL ],
        ['type' => 's', 'value' => $id[1]],
        ['type' => 's', 'value' => $_POST['fechac']],
        ['type' => 's', 'value' => $_POST['tipo']],
        ['type' => 's', 'value' => $_POST['cumplio']],
        ['type' => 's', 'value' => $_POST['observacion']],
        ['type' => 's', 'value' => $equipo],
         ['type' => 'i', 'value' => $_SESSION['us_sds']], // usu_create
        ['type' => 's', 'value' => date("Y-m-d H:i:s")], // fecha_create
        ['type' => 'i', 'value' => $_SESSION['us_sds']], // usu_update (ajustado)
        ['type' => 's', 'value' => date("Y-m-d H:i:s")], // fecha_update (ajustado)
        ['type' => 's', 'value' => 'A']
      ];
      $rta = mysql_prepd($sql, $params);
      return $rta;
    }else{
      $rta="Error: msj['No existe un equipo actualmente para el usuario que realizo el seguimiento']";
    }
}


function opc_cumplio($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_tipo($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=234 and estado='A' ORDER BY 1",$id);
    }


function formato_dato($a, $b, $c, $d) {
  $b = strtolower($b);
  // var_dump($a); //laboratorios
  // var_dump($b);
  // var_dump($c);
  // var_dump($d);
  $rta = $c[$d];
  if ($a == 'laboratorios' && $b == 'cod laboratorio') {
    $rta = "<nav class='menu right'>";
    $rta .= "<li class='icono editar' title='Editar' id='{$c['Cod Laboratorio']}' onclick=\"setTimeout(getData,500,'laboratorios',event,this,['cod_admision','tipo_lab','otro_lab','fecha_orden','fecha_toma','cuenta_resul','fecha_resul','dato_crit','gestion','gest_cump'],'../servicios_complem/laboratorios.php');\"></li>";
    
     $rta .= $c['Tomado']=='SI' ? "<li><i class='fa-solid fa-file-waveform ico' title='Resultado de laboratorios' id='{$c['Cod Laboratorio']}' Onclick=\"mostrar('resultLab','pro',event,'','../servicios_complem/laboratorios.php',3,'Resultado de laboratorios');\"></i></li>":'';

     //$rta.="<li ><i class='fa-solid fa-file-waveform ico' title='Seguimiento a Compromisos' id='{$c['Cod Laboratorio']}' Onclick=\"mostrar(mostrar('resultLab','pro',event,'','../servicios_complem/laboratorios.php',3,'resultLab');\"></i></li>"

    //$rta.="<li ><i class='fa-solid fa-house-medical-circle-check ico' title='Seguimiento a Compromisos' id='".$c['Cod Laboratorio']."' Onclick=\"mostrar('resultLab','pro',event,'','../servicios_complem/laboratorios.php',7,'Seguimiento a Compromisos');\"></i></li>";
    

    // $rta .= "<li title=\"Resultado Laboratorios\" onclick=\"mostrar('resultados','pro',event,'','../servicios_complem/laboratorios.php',4,'resultLab');Color('datos-lis');\"><i class=\"fa-solid fa-file-waweform ico\" id=\"{$c['Cod Laboratorio']}\"></i></li>";
    // $c['Tomado']=='SI' ?
    $rta .= "</nav>";
  }
  return $rta;
}

function bgcolor($a,$c,$f='c'){
    $rta="";
    return $rta;
}
