<?php
require_once "../libs/gestion.php";
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

function lis_actividades(){
    // Obtener el ID del empleado (th) para filtrar actividades
    $hash_id = $_POST['id'] ?? '';
    $id_th = 0;
    
    // Buscar directamente en la sesión con el hash recibido
    $session_hash = $_SESSION['hash'] ?? [];
    
    // Probar diferentes sufijos en orden de prioridad
    $sufijos = ['_th', '_actividades', '_editar'];
    
    foreach ($sufijos as $sufijo) {
        $key = $hash_id . $sufijo;
        if (isset($session_hash[$key])) {
            $id_th = intval($session_hash[$key]);
            break;
        }
    }
    
    // Si aún no hay ID válido, mostrar tabla vacía
    if (empty($id_th)) {
        return create_table(0, [], "actividades", 10, 'actividades.php');
    }
    
    // Sanitizar el ID
    $id_th = intval($id_th);
    
    // Contar total de actividades
    $info = datos_mysql("SELECT COUNT(*) total FROM th_actividades TA WHERE TA.estado = 'A' AND TA.idth = '$id_th'");
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-actividades'])) ? ($_POST['pag-actividades'] - 1) * $regxPag : 0;

    // SQL para obtener las actividades
    $sql = "SELECT TA.id_thact AS ACCIONES, 
                   FN_CATALOGODESC(327, TA.actividad) AS 'Actividad',
                   FN_CATALOGODESC(324, TA.rol) AS 'Rol', 
                   FN_CATALOGODESC(328, TA.acbi) AS 'Acción Bienestar',
                   FN_CATALOGODESC(329, TA.sudacbi) AS 'Sub Acción Bienestar',
                   SUBSTRING(TA.actbien, 1, 50) AS 'Descripción Actividad',
                   TA.hora_act AS 'Horas Actividad',
                   CONCAT('$ ', FORMAT(TA.hora_th, 0)) AS 'Valor Hora TH',
                   CONCAT(TA.per_ano, '-', LPAD(TA.per_mes, 2, '0')) AS 'Período',
                   TA.can_act AS 'Cantidad',
                   TA.total_horas AS 'Total Horas',
                   CONCAT('$ ', FORMAT(TA.total_valor, 0)) AS 'Valor Total',
                   TA.estado AS 'Estado'
            FROM th_actividades TA  
            WHERE TA.estado = 'A' AND TA.idth = '$id_th'";
    $sql .= " ORDER BY TA.fecha_create DESC";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
    
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "actividades", $regxPag, 'actividades.php');
}

function focus_actividades(){
    return 'actividades';
}

function men_actividades(){
    $rta = cap_menus_actividades('actividades','pro');
    return $rta;
}

function cap_menus_actividades($a,$b='cap',$con='con') {
    $rta = ""; 
    $acc=rol($a);
    if ($a=='actividades'  && isset($acc['crear']) && $acc['crear']=='SI'){   
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this,'actividades.php');\"></li>";
    return $rta;
}

function cmp_actividades(){
    $rta = "";
	$rta .="<div class='encabezado vivienda'>ACTIVIDADES</div><div class='contenido' id='gestion-lis' >".lis_actividades()."</div></div>";
    $t = ['id_thcon' => '', 'n_contrato' => '', 'tipo_cont' => '', 'fecha_inicio' => '','fecha_fin' => '', 'valor_contrato' => '', 'perfil_profesional' => '','perfil_contratado' => '', 'rol' => '', 'tipo_expe' => '', 'fecha_expe' => '', 'semestre' => ''];
    $d = get_actividades();
    if ($d == "") { $d = $t; }
    $w = 'actividades';
    $o = 'contratoinfo';
    $c[] = new cmp($o,'e',null,'INFORMACIÓN DEL CONTRATO',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'] ?? '',$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('id_thcon','h',15,$d['id_thcon'] ?? '',$w.' '.$o,'id_thcon','id_thcon',null,'####',false,false);
    $c[] = new cmp('n_contrato','nu','11',$d['n_contrato'],$w.' '.$o,'N° Contrato','n_contrato',null,null,true,true,'','col-3');
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('tipo_cont','s','3',$d['tipo_cont'],$w.' '.$o,'Tipo de Contrato','tipo_cont',null,null,true,true,'','col-25');
    $c[] = new cmp('fecha_inicio','d','',$d['fecha_inicio'],$w.' '.$o,'Fecha Inicio','fecha_inicio',null,null,true,true,'','col-25',"validDate(this,-730,362);");
    $c[] = new cmp('fecha_fin','d','',$d['fecha_fin'],$w.' '.$o,'Fecha Fin','fecha_fin',null,null,true,true,'','col-25',"validDate(this,1,730);");
    $c[] = new cmp('valor_contrato','nu','11',$d['valor_contrato'],$w.' '.$o,'Valor Total Contrato','valor_contrato',null,null,true,true,'','col-25');
    
    $o2 = 'perfilinfo';
    $c[] = new cmp($o2,'l',null,'',$w);
    $c[] = new cmp('perfil_profesional','s','3',$d['perfil_profesional'],$w.' '.$o2,'Perfil Profesional','perfil_profesional',null,null,true,true,'','col-35');
    $c[] = new cmp('perfil_contratado','s','3',$d['perfil_contratado'],$w.' '.$o2,'Perfil Contratado Requerido','perfil_contratado',null,null,true,true,'','col-35',"selectDepend('perfil_contratado','rol','actividades.php');");
    $c[] = new cmp('rol','s','3',$d['rol'],$w.' '.$o2,'Rol Contratado','rol',null,null,true,true,'','col-3',"glineTH();");
    
    $o3 = 'experiencia';
    $c[] = new cmp($o3,'l',null,'',$w);
    $c[] = new cmp('tipo_expe','s','3',$d['tipo_expe'],$w.' GlIn '.$o3,'¿Bachiller con experiencia o formación en salud/social?','tipo_expe',null,null,false,false,'','col-5',"certTH();");
    $c[] = new cmp('fecha_expe','d','',$d['fecha_expe'],$w.' CeRt '.$o3,'Fecha del Certificado','fecha_expe',null,null,false,false,'','col-3',"validDate(this,-3650,0);");
    $c[] = new cmp('semestre','nu','1',$d['semestre'],$w.' CeRt '.$o3,'Semestres Cursados','semestre',null,null,false,false,'','col-2');
    
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    return $rta;
}

function get_actividades(){
    // Usar la función global idReal para obtener el ID del contrato
    $real_id = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_actividades');
    
    // Si no hay ID real, es un nuevo registro
    if (!$real_id) {
        return "";
    }
    
    // Usar datos_mysql en lugar de mysql_prepd para consistencia
    $sql = "SELECT `id_thcon`,`n_contrato`, `tipo_cont`, `fecha_inicio`, `fecha_fin`,`valor_contrato`, `perfil_profesional`, `perfil_contratado`, `rol`,`tipo_expe`,`fecha_expe`, `semestre`, `estado`
            FROM `th_actividades` WHERE id_thcon = '" . intval($real_id) . "'";
    
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
function gra_actividades(){
    $usu = $_SESSION['us_sds'];
    
    // Obtener el idth (ID del empleado) real desde el hash de sesión
    // Necesitamos buscar con diferentes sufijos porque puede venir de TH principal
    $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_actividades');
    
    // Si no se encuentra con _actividades, intentar con otros sufijos
    if (!$idth) {
        $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_th'); 
    }
    if (!$idth) {
        $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_editar');
    }
    
    // Verificar que tenemos un ID válido del empleado
    if (!$idth) {
        return "Error: No se pudo obtener el ID del empleado (TH)";
    }
    
    // Obtener el id_thcon (ID del contrato) para determinar si es INSERT o UPDATE
    $id_thcon = $_POST['id_thcon'] ?? '';
    $es_nuevo = empty($id_thcon);
    
    if($es_nuevo) {        
        $sql = "INSERT INTO th_actividades (idth, n_contrato, tipo_cont, fecha_inicio, fecha_fin, valor_contrato, perfil_profesional, perfil_contratado, rol, tipo_expe, fecha_expe, semestre, usu_create, fecha_create, estado) 
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
        $sql = "UPDATE th_actividades SET n_contrato=?, tipo_cont=?, fecha_inicio=?, fecha_fin=?, valor_contrato=?, perfil_profesional=?, perfil_contratado=?, rol=?, tipo_expe=?, fecha_expe=?, semestre=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
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

function opc_perfil_contratadorol($id=''){
  if($_REQUEST['id']!=''){	
    $sql="SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=324 and estado='A' and valor='{$_REQUEST['id']}' ORDER BY 1";
    $info = datos_mysql($sql);		
  return json_encode($info['responseResult']);	
  }
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

function formato_dato($a, $b, $c, $d){
      $b = strtolower($b);
    $rta = $c[$d];
     if ($a == 'actividades' && $b == 'acciones') {
        $acciones = [];
        // Definición de acciones posibles para actividades
        $hash_id = myhash($c['ACCIONES']);
        $accionesDisponibles = [
            'editar' => [
                'icono' => 'fa-solid fa-edit',
                'clase' => 'ico',
                'title' => 'Editar Contrato',
                'permiso' => true,
                'hash' => $hash_id,
                'evento' => "mostrar('actividades','pro',event,'{$hash_id}','actividades.php',7);"
            ]
        ];
        
        foreach ($accionesDisponibles as $key => $accion) {
            if ($accion['permiso']) {
                limpiar_hashes();
                $_SESSION['hash'][$accion['hash'] . '_actividades'] = $c['ACCIONES'];
                $acciones[] = "<li title='{$accion['title']}'><i class='{$accion['icono']} {$accion['clase']}' id='{$accion['hash']}' onclick=\"{$accion['evento']}\" data-acc='{$key}'></i></li>";
            }
        }
        
        if (count($acciones)) {
            $rta = "<nav class='menu right'>" . implode('', $acciones) . "</nav>";
        } else {
            $rta = "";
        }
    }
    return $rta;
}

function bgcolor($a,$c,$f='c'){
 $rta="";
 return $rta;
}
?>