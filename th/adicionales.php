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
  case 'get':
    // Manejar solicitudes GET para cargar datos en formularios de edición
    $rta = get_adicionales();
    header('Content-Type: application/json');
    echo $rta;
    die;
    break;
  default:
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) json_encode($rta);
    else echo $rta;
  }
}

function lis_adicionales(){
     // Obtener el ID del empleado (th) para filtrar adicionales
    $hash_id = $_POST['id'] ?? '';
    $id_th = 0;

    // Buscar directamente en la sesión con el hash recibido
    $session_hash = $_SESSION['hash'] ?? [];

    // Probar diferentes sufijos en orden de prioridad
    $sufijos = ['_th', '_adicionales', '_editar'];

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

    // Contar total de adicionales
    $info = datos_mysql("SELECT COUNT(*) total FROM th_actiadic TA WHERE TA.estado = 'A' AND TA.idth = '$id_th'");
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-adicionales'])) ? ($_POST['pag-adicionales'] - 1) * $regxPag : 0;

    // SQL para obtener los adicionales con formato similar a la imagen
    $sql = "SELECT 
                CAST(TA.id_thadic AS CHAR) AS 'ACCIONES',
                CAST(TA.actividad AS CHAR) AS 'Codigo',
                CAST(SUBSTRING(TA.actbien, 1, 50) AS CHAR) AS 'Descripcion Actividad',
                CAST(TA.hora_act AS CHAR) AS 'Horas Actividad',
                CAST(CONCAT('$ ', FORMAT(TA.hora_th, 0)) AS CHAR) AS 'Valor Hora TH',
                CAST(TA.per_ano AS CHAR) AS 'Año',
                CAST(CASE TA.per_mes 
                    WHEN 1 THEN 'ENERO'
                    WHEN 2 THEN 'FEBRERO' 
                    WHEN 3 THEN 'MARZO'
                    WHEN 4 THEN 'ABRIL'
                    WHEN 5 THEN 'MAYO'
                    WHEN 6 THEN 'JUNIO'
                    WHEN 7 THEN 'JULIO'
                    WHEN 8 THEN 'AGOSTO'
                    WHEN 9 THEN 'SEPTIEMBRE'
                    WHEN 10 THEN 'OCTUBRE'
                    WHEN 11 THEN 'NOVIEMBRE'
                    WHEN 12 THEN 'DICIEMBRE'
                    ELSE CAST(TA.per_mes AS CHAR)
                END AS CHAR) AS 'Mes',
                CAST(TA.can_act AS CHAR) AS 'Cantidad',
                CAST(TA.total_horas AS CHAR) AS 'Total Horas',
                CAST(CONCAT('$ ', FORMAT(TA.total_valor, 0)) AS CHAR) AS 'Valor Total',
                CAST(TA.estado AS CHAR) AS 'Estado'
            FROM th_actiadic TA  
            WHERE TA.estado = 'A' AND TA.idth = '$id_th'
            
            UNION ALL
            
            SELECT 
                '' AS 'ACCIONES',
                '' AS 'Codigo', 
                'TOTAL GENERAL' AS 'Descripcion Actividad',
                '' AS 'Horas Actividad',
                '' AS 'Valor Hora TH',
                '' AS 'Año',
                'TOTAL GENERAL' AS 'Mes',
                CAST(SUM(TA2.can_act) AS CHAR) AS 'Cantidad',
                CAST(SUM(TA2.total_horas) AS CHAR) AS 'Total Horas',
                CAST(CONCAT('$ ', FORMAT(SUM(TA2.total_valor), 0)) AS CHAR) AS 'Valor Total',
                '' AS 'Estado'
            FROM th_actiadic TA2
            WHERE TA2.estado = 'A' AND TA2.idth = '$id_th'
            
            ORDER BY 
                CASE WHEN ACCIONES = '' THEN 1 ELSE 0 END,
                CAST(ACCIONES AS UNSIGNED) DESC
            LIMIT $pag, $regxPag";
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "adicionales", $regxPag, 'adicionales.php');
    }
}

function focus_adicionales(){
    return 'adicionales';
}

function men_adicionales(){
    $rta = cap_menus('adicionales','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc=rol($a);
    if ($a=='adicionales'  && isset($acc['crear']) && $acc['crear']=='SI'){
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this,'adicionales.php');\"></li>";
    return $rta;
}

function cmp_adicionales(){
    $rta = "";
	$rta .="<div class='encabezado vivienda'>ACTIVIDADES ADICIONALES</div><div class='contenido' id='adicionales-lis' >".lis_adicionales()."</div></div>";
     $t = ['id_thadic' => '', 'actividad' => '','perreq' => '' ,'rol' => '', 'acbi' => '','sudacbi' => '', 'actbien' => '', 'hora_act' => '', 'hora_th' => '','per_ano' => '', 'per_mes' => '', 'can_act' => '', 'total_horas' => '', 'total_valor' => '', 'ajustar' => ''];
    // $d='';
    $d = get_adicionales();
    if ($d == "") { $d = $t; }
    $w = 'adicionales';
    $o = 'adicionalinfo';
    $c[] = new cmp($o,'e',null,'INFORMACIÓN DEL ADICIONAL',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'] ?? '',$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('id_thadic','h',15,$d['id_thadic'] ?? '',$w.' '.$o,'id_thadic','id_thadic',null,'####',false,false);

    $o = 'tipoadicional';
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('actividad','nu','999',$d['actividad'],$w.' aCT '.$o,'Actividad/Intervención','actividad',null,null,true,true,'','col-2',"getDatForm('aCT','activiValores',['tipoadicional'],this,'adicionales.php');");
    $c[] = new cmp('perreq','s','3',$d['perreq'],$w.' '.$o,'Perfil Requerido','perreq',null,null,false,false,'','col-4');
    $c[] = new cmp('rol','s','3',$d['rol'],$w.' '.$o,'Rol','rol',null,null,false,false,'','col-4');
    $c[] = new cmp('acbi','nu','99',$d['acbi'],$w.' '.$o,'Acción de Bienestar','acbi',null,null,false,false,'','col-15');
    $c[] = new cmp('sudacbi','nu','99',$d['sudacbi'],$w.' '.$o,'Sub Acción de Bienestar','sudacbi',null,null,false,false,'','col-15');
    $c[] = new cmp('actbien','t','3000',$d['actbien'],$w.' '.$o,'Descripción de la Actividad','actbien',null,null,false,false,'','col-7');
    $c[] = new cmp('hora_act','nu','99999',$d['hora_act'],$w.' '.$o,'Horas por Actividad','hora_act',null,null,false,false,'','col-25',"calcularTotales();");
    $c[] = new cmp('hora_th','nu','999999',$d['hora_th'],$w.' '.$o,'Valor Hora TH','hora_th',null,null,false,false,'','col-25',"calcularTotales();");

    $o = 'horasvalor'; 
    $c[] = new cmp($o,'e',null,'PERIODO POR ADICIONAL',$w);
    $c[] = new cmp('per_ano','s','3',$d['per_ano'],$w.' '.$o,'Año Período','per_ano',null,null,true,true,'','col-35');
    $c[] = new cmp('per_mes','s','3',$d['per_mes'],$w.' '.$o,'Mes Período','per_mes',null,null,true,true,'','col-35');
    $c[] = new cmp('can_act','sd','4',$d['can_act'],$w.' '.$o,'Cantidad Realizada','can_act',null,null,true,true,'','col-3',"calcularTotales();");
    
    $c[] = new cmp('total_horas','nu','9999.9',$d['total_horas'],$w.' '.$o,'Total Horas Realizadas','total_horas',null,null,false,false,'','col-3');
    $c[] = new cmp('total_valor','nu','99999999',$d['total_valor'],$w.' '.$o,'Valor Total','total_valor',null,null,false,false,'','col-4');
    $c[] = new cmp('ajustar','s','1',$d['ajustar'],$w.' '.$o,'Ajustar','ajustar',null,null,false,false,'','col-25');

    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();

    return $rta;
}

 function get_activiValores(){
	// print_r($_REQUEST);
    
   $sql="SELECT id_actividad,cod_perreq perreq, cod_rol rol, cod_acbi acbi, sud_acbi sudacbi, actividad actbien, hora_act, hora_th  FROM th_acti_bien
    WHERE id_actividad ='".$_REQUEST['id']."'"; 
    // var_dump($sql);
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
	return json_encode($info['responseResult'][0]);
   
}

function get_adicionales(){
    // Verificar si se está solicitando datos para edición
    if (!isset($_POST['id']) || $_POST['id'] == '' || $_POST['id'] == '0') {
        return "";
    }

    // Usar la función global idReal para obtener el ID del adicional
    $real_id = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_adicionales');

    // Si no hay ID real, es un nuevo registro
    if (!$real_id) {
        return "";
    }

    // Usar datos_mysql para obtener el adicional
    $sql = "SELECT `id_thadic`, `actividad`, `perreq`, `rol`, `acbi`, `sudacbi`, `actbien`, `hora_act`, `hora_th`, `per_ano`, `per_mes`, `can_act`, `total_horas`, `total_valor`, `ajustar`, `estado`
            FROM `th_actiadic` WHERE id_thadic = '" . intval($real_id) . "'";

    $info = datos_mysql($sql);

    // Validar que la respuesta sea válida
    if (!$info || !isset($info['responseResult']) || !is_array($info['responseResult'])) {
        return json_encode([]);
    }

    // Verificar que hay resultados
    if (empty($info['responseResult'])) {
        return json_encode([]);
    }

    // Para compatibilidad con getData de a.js, devolver JSON si es una petición AJAX
    if (isset($_POST['a']) && $_POST['a'] == 'get') {
        return json_encode($info['responseResult'][0]);
    }

    return $info['responseResult'][0];
}

function gra_adicionales(){
     $usu = $_SESSION['us_sds'];

    // Obtener el idth (ID del empleado) real desde el hash de sesión
    $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_adicionales');

    // Si no se encuentra con _adicionales, intentar con otros sufijos
    if (!$idth) {
        $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_th');
    }
    if (!$idth) {
        $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_editar');
    }

    // Debug: agregar logging para verificar qué ID se está obteniendo
    if (function_exists('log_error')) {
        log_error("ADICIONALES gra_adicionales(): POST[id]=" . ($_POST['id'] ?? 'NO_SET') . ", idth obtenido=" . ($idth ?? 'NULL'));
    }

    // Verificar que tenemos un ID válido del empleado
    if (!$idth) {
        return "Error: No se pudo obtener el ID del empleado (TH)";
    }

    // Obtener el id_thadic (ID del adicional) para determinar si es INSERT o UPDATE
    $id_thadic = $_POST['id_thadic'] ?? '';
    
    // Si no hay id_thadic, verificar si se puede obtener desde el hash de la sesión
    if (empty($id_thadic)) {
        $id_thadic_from_hash = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_adicionales');
        if ($id_thadic_from_hash) {
            $id_thadic = $id_thadic_from_hash;
        }
    }
    
    $es_nuevo = empty($id_thadic);

    if($es_nuevo) {

        $idth=intval($idth);
        $ano=intval($_POST['per_ano']);
        $mes=intval($_POST['per_mes']);

        // Validación: verificar si ya existe un registro con la misma clave primaria
        $sql_check = "SELECT COUNT(*) as existe FROM th_actiadic 
                      WHERE idth = ? AND actividad = ? AND per_ano = ? AND per_mes = ? AND estado = 'A'";
        $params_check = [
            ['type' => 'i', 'value' => $idth],
            ['type' => 's', 'value' => $_POST['actividad'] ?? ''],
            ['type' => 'i', 'value' => $ano],
            ['type' => 'i', 'value' => $mes]
        ];
        
        $info_check = mysql_prepd($sql_check, $params_check);
        if ($info_check['responseResult'][0]['existe'] > 0) {
            return "msj['Error: Ya existe un adicional para esta actividad en el período seleccionado.']";
        }

        // Generar nuevo ID para id_thadic (autoincremental manual)
        $sql_max = "SELECT COALESCE(MAX(id_thadic), 0) + 1 as nuevo_id FROM th_actiadic";
        $info_max = datos_mysql($sql_max);
        $nuevo_id = $info_max['responseResult'][0]['nuevo_id'];

        $sql = "INSERT INTO th_actiadic (id_thadic, idth, actividad, perreq, rol, acbi, sudacbi, actbien, hora_act, hora_th, per_ano, per_mes, can_act, total_horas, total_valor, ajustar, usu_create, fecha_create, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), 'A')";
        $params = [
            ['type' => 'i', 'value' => $nuevo_id],
            ['type' => 'i', 'value' => $idth],
            ['type' => 's', 'value' => $_POST['actividad'] ?? ''],
            ['type' => 's', 'value' => $_POST['perreq'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['acbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['sudacbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['actbien'] ?? ''],
            ['type' => 's', 'value' => $_POST['hora_act'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['hora_th'] ?? 0)],
            ['type' => 'i', 'value' => $ano],
            ['type' => 'i', 'value' => $mes],
            ['type' => 's', 'value' => $_POST['can_act'] ?? '0'],
            ['type' => 's', 'value' => $_POST['total_horas'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['total_valor'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['ajustar'] ?? 0)],
            ['type' => 's', 'value' => $usu]
        ];
    } else {
        // UPDATE - Actualizar adicional existente
        $sql = "UPDATE th_actiadic SET actividad=?, perreq=?, rol=?, acbi=?, sudacbi=?, actbien=?, hora_act=?, hora_th=?, per_ano=?, per_mes=?, can_act=?, total_horas=?, total_valor=?, ajustar=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR)
                WHERE id_thadic=?";
        $params = [
            ['type' => 's', 'value' => $_POST['actividad'] ?? ''],
            ['type' => 's', 'value' => $_POST['perreq'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['acbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['sudacbi'] ?? ''],
            ['type' => 's', 'value' => $_POST['actbien'] ?? ''],
            ['type' => 's', 'value' => $_POST['hora_act'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['hora_th'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['per_ano'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['per_mes'] ?? 0)],
            ['type' => 's', 'value' => $_POST['can_act'] ?? '0'],
            ['type' => 's', 'value' => $_POST['total_horas'] ?? '0'],
            ['type' => 'i', 'value' => intval($_POST['total_valor'] ?? 0)],
            ['type' => 'i', 'value' => intval($_POST['ajustar'] ?? 0)],
            ['type' => 's', 'value' => $usu],
            ['type' => 'i', 'value' => intval($id_thadic)]
        ];
    }

    $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Funciones para opciones de select
function opc_perreq($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=308 and estado='A' ORDER BY 2",$id);
}
function opc_rol($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=324 and estado='A' ORDER BY 2",$id);
}
function opc_per_mes($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=327 and estado='A' ORDER BY 1",$id);
}
function opc_per_ano($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=328 and estado='A' ORDER BY 1",$id);
}
function opc_ajustar($id=''){
    return opc_sql("SELECT 0 as idcatadeta, 'NO' as descripcion UNION SELECT 1 as idcatadeta, 'SI' as descripcion ORDER BY 1",$id);
}

function formato_dato($a, $b, $c, $d){
    $b = strtolower($b);
    $rta = $c[$d];
    if ($a == 'adicionales' && $b == 'acciones') {
        $acciones = [];
        // Definición de acciones posibles para adicionales
        $hash_id = myhash($c['ACCIONES']);
        $accionesDisponibles = [
            'editar' => [
                'icono' => 'fa-solid fa-edit',
                'clase' => 'ico',
                'title' => 'Editar Adicional',
                'permiso' => true,
                'hash' => $hash_id,
                'evento' => "mostrar('adicionales','pro',event,'{$hash_id}','adicionales.php',7).then(() => getData('adicionales', event, this, ['actividad'], 'adicionales.php'));"
            ]
        ];

        foreach ($accionesDisponibles as $key => $accion) {
            if ($accion['permiso']) {
                limpiar_hashes();
                $_SESSION['hash'][$accion['hash'] . '_adicionales'] = $c['ACCIONES'];
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