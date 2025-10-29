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
    /* // Obtener el ID del empleado (th) para filtrar actividades
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
    if (!empty($id_th)) {
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
    } */
}

function focus_actividades(){
    return 'actividades';
}

function men_actividades(){
    $rta = cap_menus('actividades','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
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
	$rta .="<div class='encabezado vivienda'>ACTIVIDADES REALIZADAS</div><div class='contenido' id='gestion-lis' >".lis_actividades()."</div></div>";
    $t = ['id_thact' => '', 'actividad' => '','perreq' => '' ,'rol' => '', 'acbi' => '','sudacbi' => '', 'actbien' => '', 'hora_act' => '', 'hora_th' => '','per_ano' => '', 'per_mes' => '', 'can_act' => '', 'total_horas' => '', 'total_valor' => ''];
    $d = get_actividades();
    if ($d == "") { $d = $t; }
    $w = 'actividades';
    $o = 'actividadinfo';
    $c[] = new cmp($o,'e',null,'INFORMACIÓN DE LA ACTIVIDAD',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'] ?? '',$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('id_thact','h',15,$d['id_thact'] ?? '',$w.' '.$o,'id_thact','id_thact',null,'####',false,false);
    
    $o2 = 'tipoactividad';
    $c[] = new cmp($o2,'l',null,'',$w);
    $c[] = new cmp('actividad','s','3',$d['actividad'],$w.' '.$o2,'Actividad/Intervención','actividad',null,null,true,true,'','col-4');
    $c[] = new cmp('perreq','s','3',$d['perreq'],$w.' '.$o2,'Perfil Requerido','perreq',null,null,false,false,'','col-35');
    $c[] = new cmp('rol','s','3',$d['rol'],$w.' '.$o2,'Rol','rol',null,null,false,false,'','col-4');
    $c[] = new cmp('acbi','t','3',$d['acbi'],$w.' '.$o2,'Acción de Bienestar','acbi',null,null,false,false,'','col-4');
    $c[] = new cmp('sudacbi','t','3',$d['sudacbi'],$w.' '.$o2,'Sub Acción de Bienestar','sudacbi',null,null,false,false,'','col-4');
    
    $o3 = 'descripcion';
    $c[] = new cmp($o3,'l',null,'',$w);
    $c[] = new cmp('actbien','a','3000',$d['actbien'],$w.' '.$o3,'Descripción de la Actividad','actbien',null,null,false,false,'','col-10');
    
    $o4 = 'horasvalor';
    $c[] = new cmp($o4,'l',null,'',$w);
    $c[] = new cmp('hora_act','nu','999.9',$d['hora_act'],$w.' '.$o4,'Horas por Actividad','hora_act',null,null,false,false,'','col-25',"calcularTotales();");
    $c[] = new cmp('hora_th','nu','999999',$d['hora_th'],$w.' '.$o4,'Valor Hora TH','hora_th',null,null,false,false,'','col-25',"calcularTotales();");
    $c[] = new cmp('per_ano','nu','99',$d['per_ano'],$w.' '.$o4,'Año Período','per_ano',null,null,true,true,'','col-25');
    $c[] = new cmp('per_mes','nu','12',$d['per_mes'],$w.' '.$o4,'Mes Período','per_mes',null,null,true,true,'','col-25');
    
    $o5 = 'cantidad';
    $c[] = new cmp($o5,'l',null,'',$w);
    $c[] = new cmp('can_act','nu','999',$d['can_act'],$w.' '.$o5,'Cantidad Realizada','can_act',null,null,true,true,'','col-3',"calcularTotales();");
    $c[] = new cmp('total_horas','nu','9999.9',$d['total_horas'],$w.' '.$o5,'Total Horas','total_horas',null,null,false,false,'','col-3');
    $c[] = new cmp('total_valor','nu','99999999',$d['total_valor'],$w.' '.$o5,'Valor Total','total_valor',null,null,false,false,'','col-4');
    
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    
    // Agregar JavaScript para cálculos automáticos
    $rta .= "<script>
    function calcularTotales() {
        var hora_act = parseFloat(document.getElementById('hora_act').value) || 0;
        var can_act = parseInt(document.getElementById('can_act').value) || 0;
        var hora_th = parseInt(document.getElementById('hora_th').value) || 0;
        
        var total_horas = hora_act * can_act;
        var total_valor = total_horas * hora_th;
        
        document.getElementById('total_horas').value = total_horas.toFixed(1);
        document.getElementById('total_valor').value = Math.round(total_valor);
    }
    </script>";
    
    return $rta;
}

function get_actividades(){
    // Usar la función global idReal para obtener el ID de la actividad
    $real_id = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_actividades');
    
    // Si no hay ID real, es un nuevo registro
    if (!$real_id) {
        return "";
    }
    
    // Usar datos_mysql para obtener la actividad
    $sql = "SELECT `id_thact`, `actividad`, `rol`, `acbi`, `sudacbi`, `actbien`, `hora_act`, `hora_th`, `per_ano`, `per_mes`, `can_act`, `total_horas`, `total_valor`, `estado`
            FROM `th_actividades` WHERE id_thact = '" . intval($real_id) . "'";
    
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
    $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_actividades');
    
    // Si no se encuentra con _actividades, intentar con otros sufijos
    if (!$idth) {
        $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_th'); 
    }
    if (!$idth) {
        $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_editar');
    }
    
    // Debug: agregar logging para verificar qué ID se está obteniendo
    if (function_exists('log_error')) {
        log_error("ACTIVIDADES gra_actividades(): POST[id]=" . ($_POST['id'] ?? 'NO_SET') . ", idth obtenido=" . ($idth ?? 'NULL'));
    }
    
    // Verificar que tenemos un ID válido del empleado
    if (!$idth) {
        return "Error: No se pudo obtener el ID del empleado (TH)";
    }
    
    // Obtener el id_thact (ID de la actividad) para determinar si es INSERT o UPDATE
    $id_thact = $_POST['id_thact'] ?? '';
    $es_nuevo = empty($id_thact);
    
    if($es_nuevo) {        
        $sql = "INSERT INTO th_actividades (idth, actividad, rol, acbi, sudacbi, actbien, hora_act, hora_th, per_ano, per_mes, can_act, total_horas, total_valor, usu_create, fecha_create, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), 'A')";
        $params = [
            ['type' => 'i', 'value' => intval($idth)],
            ['type' => 's', 'value' => $_POST['actividad'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['acbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['sudacbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['actbien'] ?? ''],
            ['type' => 's', 'value' => $_POST['hora_act'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['hora_th'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['per_ano'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['per_mes'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['can_act'] ?? 0)],
            ['type' => 's', 'value' => $_POST['total_horas'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['total_valor'] ?? 0)],
            ['type' => 's', 'value' => $usu]
        ];
    } else {
        // UPDATE - Actualizar actividad existente
        $sql = "UPDATE th_actividades SET actividad=?, rol=?, acbi=?, sudacbi=?, actbien=?, hora_act=?, hora_th=?, per_ano=?, per_mes=?, can_act=?, total_horas=?, total_valor=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
                WHERE id_thact=?";
        $params = [
            ['type' => 's', 'value' => $_POST['actividad'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['acbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['sudacbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['actbien'] ?? ''],
            ['type' => 's', 'value' => $_POST['hora_act'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['hora_th'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['per_ano'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['per_mes'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['can_act'] ?? 0)],
            ['type' => 's', 'value' => $_POST['total_horas'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['total_valor'] ?? 0)],
            ['type' => 's', 'value' => $usu],
            ['type' => 'i', 'value' => intval($id_thact)]
        ];
    }
    
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Funciones para opciones de select
function per_mes(($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=327 and estado='A' ORDER BY 1",$id);
}

function opc_rol($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=324 and estado='A' ORDER BY 2",$id);
}

function opc_perreq($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=308 and estado='A' ORDER BY 2",$id);
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
                'title' => 'Editar Actividad',
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