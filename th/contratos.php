<?php
/**
 * Módulo de Contratos TH
 * Utiliza la función global idReal() definida en lib.php para evitar duplicación de código
 * en get_contratos() y gra_contratos()
 */
require_once "../libs/gestion.php";
require_once "lib.php";
ini_set('display_errors','1');
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

function lis_contratos(){
    $id_th = $_POST['id'] ?? '';
    $info = datos_mysql("SELECT COUNT(*) total FROM th_contratos TC WHERE TC.idth = '$id_th'");
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-contratos'])) ? ($_POST['pag-contratos'] - 1) * $regxPag : 0;
    $sql = "SELECT TC.id_thcon AS ACCIONES, 
                   TC.n_contrato AS 'N° Contrato', 
                   FN_CATALOGODESC(326, TC.tipo_cont) AS 'Tipo Vinculación',
                   TC.fecha_inicio AS 'Fecha Inicio', 
                   TC.fecha_fin AS 'Fecha Fin',
                   CONCAT('$ ', FORMAT(TC.valor_contrato, 0)) AS 'Valor Contrato',
                   FN_CATALOGODESC(323, TC.perfil_profesional) AS 'Perfil Profesional',
                   TC.estado AS 'Estado'
            FROM th_contratos TC  
            WHERE TC.idth = '$id_th'";    
    $sql .= " ORDER BY TC.fecha_create DESC";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "contratos", $regxPag, 'contratos.php');
}

function focus_contratos(){
    return 'contratos';
}

function men_contratos(){
    $rta = cap_menus_contratos('contratos','pro');
    return $rta;
}

function cap_menus_contratos($a,$b='cap',$con='con') {
    $rta = ""; 
    $acc=rol($a);
    if ($a=='contratos'  && isset($acc['crear']) && $acc['crear']=='SI'){   
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this,'contratos.php');\"></li>";
    return $rta;
}

function cmp_contratos(){
    $rta = "";
    $t = ['id_thcon' => '', 'n_contrato' => '', 'tipo_cont' => '', 'fecha_inicio' => '','fecha_fin' => '', 'valor_contrato' => '', 'perfil_profesional' => '','perfil_contratado' => '', 'rol' => '', 'tipo_expe' => '', 'fecha_expe' => '', 'semestre' => ''];
    $d = get_contratos();
    if ($d == "") { $d = $t; }
    $w = 'contratos';
    $o = 'contratoinfo';
    $c[] = new cmp($o,'e',null,'INFORMACIÓN DEL CONTRATO',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'] ?? '',$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('id_thcon','h',15,$d['id_thcon'] ?? '',$w.' '.$o,'id_thcon','id_thcon',null,'####',false,false);
    $c[] = new cmp('n_contrato','nu','11',$d['n_contrato'],$w.' '.$o,'N° Contrato','n_contrato',null,null,true,true,'','col-3');
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('tipo_cont','s','3',$d['tipo_cont'],$w.' '.$o,'Tipo de Contrato','tipo_cont',null,null,true,true,'','col-25');
    $c[] = new cmp('fecha_inicio','d','',$d['fecha_inicio'],$w.' '.$o,'Fecha Inicio','fecha_inicio',null,null,true,true,'','col-25',"validDate(this,-30,730);");
    $c[] = new cmp('fecha_fin','d','',$d['fecha_fin'],$w.' '.$o,'Fecha Fin','fecha_fin',null,null,true,true,'','col-25',"validDate(this,1,730);");
    $c[] = new cmp('valor_contrato','nu','11',$d['valor_contrato'],$w.' '.$o,'Valor Total Contrato','valor_contrato',null,null,true,true,'','col-25');
    
    $o2 = 'perfilinfo';
    $c[] = new cmp($o2,'l',null,'',$w);
    $c[] = new cmp('perfil_profesional','s','3',$d['perfil_profesional'],$w.' '.$o2,'Perfil Profesional','perfil_profesional',null,null,true,true,'','col-35');
    $c[] = new cmp('perfil_contratado','s','3',$d['perfil_contratado'],$w.' '.$o2,'Perfil Contratado Requerido','perfil_contratado',null,null,true,true,'','col-35');
    $c[] = new cmp('rol','s','3',$d['rol'],$w.' '.$o2,'Rol Contratado','rol',null,null,true,true,'','col-3',"glineTH();");
    
    $o3 = 'experiencia';
    $c[] = new cmp($o3,'l',null,'',$w);
    $c[] = new cmp('tipo_expe','s','3',$d['tipo_expe'],$w.' GlIn '.$o3,'¿Bachiller con experiencia o formación en salud/social?','tipo_expe',null,null,false,false,'','col-5');
    $c[] = new cmp('fecha_expe','d','',$d['fecha_expe'],$w.' GlIn '.$o3,'Fecha del Certificado','fecha_expe',null,null,false,true,'','col-3',"validDate(this,-3650,0);");
    $c[] = new cmp('semestre','nu','1',$d['semestre'],$w.' GlIn '.$o3,'Semestres Cursados','semestre',null,null,false,true,'','col-2');
    
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    return $rta;
}

function get_contratos(){
    // Usar la función global idReal para obtener el ID del contrato
    $real_id = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_contratos');
    
    // Si no hay ID real, es un nuevo registro
    if (!$real_id) {
        return "";
    }
    
    // Usar datos_mysql en lugar de mysql_prepd para consistencia
    $sql = "SELECT `id_thcon`,`n_contrato`, `tipo_cont`, `fecha_inicio`, `fecha_fin`,`valor_contrato`, `perfil_profesional`, `perfil_contratado`, `rol`,`tipo_expe`,`fecha_expe`, `semestre`, `estado`
            FROM `th_contratos` WHERE id_thcon = '" . intval($real_id) . "'";
    
    $info = datos_mysql($sql);
    
    // Validar que la respuesta sea válida
    if (!$info || !isset($info['responseResult']) || !is_array($info['responseResult'])) {
        return '';
    }
    
    // Verificar que hay resultados
    if (empty($info['responseResult'])) {
        return '';
    }
    
    return $info['responseResult'][0];
}
function gra_contratos(){
    $usu = $_SESSION['us_sds'];
    
    // Obtener el idth (ID del empleado) real desde el hash de sesión
    $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_th');
    
    // Obtener el id_thcon (ID del contrato) para determinar si es INSERT o UPDATE
    $id_thcon = $_POST['id_thcon'] ?? '';
    $es_nuevo = empty($id_thcon);
    
    if($es_nuevo) {        
        $sql = "INSERT INTO th_contratos (idth, n_contrato, tipo_cont, fecha_inicio, fecha_fin, valor_contrato, perfil_profesional, perfil_contratado, rol, tipo_expe, fecha_expe, semestre, usu_create, fecha_create, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), 'A')";// INSERT - Nuevo contrato
        $params = [
            ['type' => 'i', 'value' => intval($idth)],
            ['type' => 'i', 'value' => intval($_POST['n_contrato'] ?? 0)],
            ['type' => 's', 'value' => $_POST['tipo_cont'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_inicio'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_fin'] ?? ''],
            ['type' => 'i', 'value' => intval($_POST['valor_contrato'] ?? 0)],
            ['type' => 's', 'value' => $_POST['perfil_profesional'] ?? ''],
            ['type' => 's', 'value' => $_POST['perfil_contratado'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['tipo_expe'] ?? ''],
            ['type' => 's', 'value' => !empty($_POST['fecha_expe']) ? $_POST['fecha_expe'] : null],
            ['type' => 'i', 'value' => !empty($_POST['semestre']) ? intval($_POST['semestre']) : null],
            ['type' => 's', 'value' => $usu]
        ];
    } else {
        // UPDATE - Actualizar contrato existente
        $sql = "UPDATE th_contratos SET n_contrato=?, tipo_cont=?, fecha_inicio=?, fecha_fin=?, valor_contrato=?, perfil_profesional=?, perfil_contratado=?, rol=?, tipo_expe=?, fecha_expe=?, semestre=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
                WHERE id_thcon=?";
        $params = [
            ['type' => 'i', 'value' => intval($_POST['n_contrato'] ?? 0)],
            ['type' => 's', 'value' => $_POST['tipo_cont'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_inicio'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_fin'] ?? ''],
            ['type' => 'i', 'value' => intval($_POST['valor_contrato'] ?? 0)],
            ['type' => 's', 'value' => $_POST['perfil_profesional'] ?? ''],
            ['type' => 's', 'value' => $_POST['perfil_contratado'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['tipo_expe'] ?? ''],
            ['type' => 's', 'value' => !empty($_POST['fecha_expe']) ? $_POST['fecha_expe'] : null],
            ['type' => 'i', 'value' => !empty($_POST['semestre']) ? intval($_POST['semestre']) : null],
            ['type' => 's', 'value' => $usu],
            ['type' => 'i', 'value' => intval($id_thcon)]
        ];
    }
    
    // return json_encode(['sql' => show_sql($sql, $params), 'idth' => $idth, 'hash' => $hash_id, 'session' => $_SESSION['hash']]);
    
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

function opc_tipo_cont($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=326 and estado='A' ORDER BY LENGTH(idcatadeta), idcatadeta",$id);
}

function opc_perfil_profesional($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=323 and estado='A' ORDER BY 2",$id);
}

function opc_perfil_contratado($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=308 and estado='A' ORDER BY 2",$id);
}

function opc_rol($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=324 and estado='A' ORDER BY 2",$id);
}

function opc_tipo_expe($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=325 and estado='A' ORDER BY 2",$id);
}

?>