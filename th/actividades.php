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
    $hash_id = $_POST['id'] ?? '';
    $session_hash = $_SESSION['hash'] ?? [];
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
    $id_th = intval($id_th);
    $info = datos_mysql("SELECT COUNT(*) total FROM th_actividades TA WHERE TA.estado = 'A' AND TA.idth = '$id_th'");
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-actividades'])) ? ($_POST['pag-actividades'] - 1) * $regxPag : 0;
    $sql = "SELECT CONCAT_WS('_',TA.id_thact,TA.idth) AS ACCIONES, 
                CAST(TA.actividad AS CHAR) AS 'Codigo Registro',
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
                CAST(TA.can_act AS DECIMAL(4,2)) AS 'Cantidad',
                CAST(TA.total_horas AS CHAR) AS 'Total Horas',
                CAST(CONCAT('$ ', FORMAT(TA.total_valor, 0)) AS CHAR) AS 'Valor Total',
                CAST(TA.estado AS CHAR) AS 'Estado'
              FROM th_actividades TA  
            WHERE TA.estado = 'A' AND TA.idth = '$id_th'
            UNION ALL
            SELECT 
                '' AS 'ACCIONES',
                '' AS 'Codigo', 
                '' AS 'Descripcion Actividad',
                '' AS 'Horas Actividad',
                '' AS 'Valor Hora TH',
                '' AS 'Año',
                'TOTAL GENERAL' AS 'Mes',
                CAST(SUM(TA2.can_act) AS DECIMAL(4,2)) AS 'Cantidad',
                CAST(SUM(TA2.total_horas) AS CHAR) AS 'Total Horas',
                CAST(CONCAT('$ ', FORMAT(SUM(TA2.total_valor), 0)) AS CHAR) AS 'Valor Total',
                '' AS 'Estado'
            FROM th_actividades TA2
            WHERE TA2.estado = 'A' AND TA2.idth = '$id_th'
            ORDER BY 
                CASE WHEN ACCIONES = '' THEN 1 ELSE 0 END,
                CAST(ACCIONES AS UNSIGNED) DESC";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "actividades", $regxPag, 'actividades.php');    
    }
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
    $w = 'actividades';
    $o = 'actividadinfo';
    $key= $_POST['id'] ?? '';
     $c[] = new cmp($o,'e',null,'INFORMACIÓN DE LA ACTIVIDAD',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'] ?? '',$w.' '.$o,'id','id',null,'####',false,false);

    $o = 'tipoactividad';
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('actividad','nu','999','',$w.' aCT '.$o,'Actividad/Intervención','actividad',null,null,true,true,'','col-2',"getDatForm('aCT','activiValores',['tipoactividad'],this,'actividades.php');");
    $c[] = new cmp('perreq','s','3','',$w.' '.$o,'Perfil Requerido','perreq',null,null,false,false,'','col-4');
    $c[] = new cmp('rol','s','3','',$w.' '.$o,'Rol','rol',null,null,false,false,'','col-4');
    $c[] = new cmp('acbi','nu','99','',$w.' '.$o,'Acción de Bienestar','acbi',null,null,false,false,'','col-15');
    $c[] = new cmp('sudacbi','nu','99','',$w.' '.$o,'Sub Acción de Bienestar','sudacbi',null,null,false,false,'','col-15');
    $c[] = new cmp('actbien','t','3000','',$w.' '.$o,'Descripción de la Actividad','actbien',null,null,false,false,'','col-7');
    $c[] = new cmp('hora_act','nu','99999','',$w.' '.$o,'Horas por Actividad','hora_act',null,null,false,false,'','col-25',"calcularTotales();");
    $c[] = new cmp('hora_th','nu','999999','',$w.' '.$o,'Valor Hora TH','hora_th',null,null,false,false,'','col-25',"calcularTotales();");

    $o = 'horasvalor'; 
    $c[] = new cmp($o,'e',null,'PERIODO POR ACTIVIDAD',$w);
    $c[] = new cmp('per_ano','s','3','',$w.' '.$o,'Año Período','per_ano',null,null,true,true,'','col-35');
    $c[] = new cmp('per_mes','s','3','',$w.' '.$o,'Mes Período','per_mes',null,null,true,true,'','col-35');
    $c[] = new cmp('can_act','sd','12','',$w.' '.$o,'Cantidad Realizada','can_act',null,null,true,true,'','col-3',"calcularTotales();");
    $c[] = new cmp('total_horas','nu','9999.9','',$w.' '.$o,'Total Horas Realizadas','total_horas',null,null,false,false,'','col-3');
    $c[] = new cmp('total_valor','nu','99999999','',$w.' '.$o,'Valor Total','total_valor',null,null,false,false,'','col-4');
    
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    return $rta;
}

function get_actividades(){
    // Usar la función global idReal para obtener el ID de la actividad
    $real_id = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_actividades');
    
    // Usar datos_mysql en lugar de mysql_prepd para consistencia
    $sql = "SELECT CONCAT_WS('_',id_thact,idth), `actividad`, `perreq`, `rol`, `acbi`, `sudacbi`, `actbien`, `hora_act`, `hora_th`, `per_ano`, `per_mes`, `can_act`, `total_horas`, `total_valor`, `estado`
            FROM `th_actividades` WHERE id_thact = '" . intval($real_id) . "'";
    $info = datos_mysql($sql);
    return json_encode($info['responseResult'][0]);
}

function gra_actividades(){
    $usu = $_SESSION['us_sds'];
    $id = divide($_POST['id']); 
    
    $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_actividades');

    if(count($id) == 1) {        
        $idth = intval($idth);
        $ano = intval($_POST['per_ano']);
        $mes = intval($_POST['per_mes']);

        $sql1 = "SELECT sum(total_horas) totalh FROM th_actividades WHERE idth=$idth and per_ano=$ano and per_mes=$mes";
        $info_horas = datos_mysql($sql1);

        if($info_horas['responseResult'][0]['totalh'] + intval($_POST['total_horas'] ?? 0) > 184){
            return "msj['Error:La suma de horas totales excede el límite permitido de 184 horas para el período seleccionado.']";
        }

        $sql = "INSERT INTO th_actividades (idth, actividad, perreq, rol, acbi, sudacbi, actbien, hora_act, hora_th, per_ano, per_mes, can_act, total_horas, total_valor, usu_create, fecha_create, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), 'A')";
        $params = [
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
            ['type' => 's', 'value' => $usu]
        ];
    } else {
        // UPDATE - Actualizar actividad existente
        $sql = "UPDATE th_actividades SET actividad=?, perreq=?, rol=?, acbi=?, sudacbi=?, actbien=?, hora_act=?, hora_th=?, per_ano=?, per_mes=?, can_act=?, total_horas=?, total_valor=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR), ajustar=0
                WHERE id_thact=?";
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
            ['type' => 's', 'value' => $usu],
            ['type' => 'i', 'value' => intval($id[0])]
        ];
    }
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

function get_activiValores(){
    $sql = "SELECT id_actividad,cod_perreq perreq, cod_rol rol, cod_acbi acbi, sud_acbi sudacbi, actividad actbien, hora_act, hora_th  FROM th_acti_bien
    WHERE id_actividad ='".$_REQUEST['id']."'"; 
    $info = datos_mysql($sql);
    if (!$info['responseResult']) {
        return json_encode (new stdClass);
    }
    return json_encode($info['responseResult'][0]);
}

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

function ajustar($acc){
    $id=divide($acc);
    $sql = "SELECT COUNT(*) AS total FROM th_actividades WHERE id_thact = $id[0] AND ajustar = 1 AND estado = 'A'";
    $info = datos_mysql($sql);
    // var_dump($sql);
    return (!empty($info['responseResult'][0]['total']) && $info['responseResult'][0]['total'] > 0);
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
                'evento' => "setTimeout(getDataFetch,500,'actividades',event,this,'../th/actividades.php',[]);"
            ]
        ];
        
        foreach ($accionesDisponibles as $key => $accion) {
            if (ajustar($c['ACCIONES'])) {
                if ($accion['permiso']) {
                    limpiar_hashes();
                    $_SESSION['hash'][$accion['hash'] . '_actividades'] = $c['ACCIONES'];
                    $acciones[] = "<li title='{$accion['title']}'><i class='{$accion['icono']} {$accion['clase']}' id='{$accion['hash']}' onclick=\"{$accion['evento']}\" data-acc='{$key}'></i></li>";
                }
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