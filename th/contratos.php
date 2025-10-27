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

function lis_contratos(){
    $id_th = $_POST['id'] ?? '';
    $info = datos_mysql("SELECT COUNT(*) total FROM th_contratos TC WHERE TC.thcon = '$id_th'");
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-contratos'])) ? ($_POST['pag-contratos'] - 1) * $regxPag : 0;
    $sql = "SELECT TC.id_thcon AS ACCIONES, 
                   TC.n_contrato AS 'N° Contrato', 
                   FN_CATALOGODESC(450, TC.tipo_cont) AS 'Tipo Vinculación',
                   TC.fecha_inicio AS 'Fecha Inicio', 
                   TC.fecha_fin AS 'Fecha Fin',
                   CONCAT('$ ', FORMAT(TC.valor_contrato, 0)) AS 'Valor Contrato',
                   FN_CATALOGODESC(451, TC.perfil_profesional) AS 'Perfil Profesional',
                   TC.estado AS 'Estado'
            FROM th_contratos TC  
            WHERE TC.id_thcon = '$id_th'";    
    $sql .= " ORDER BY TC.fecha_create DESC";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "contratos", $regxPag, 'contratos.php');
}

function focus_contratos(){
    return 'contratos';
}

function men_contratos(){
    $rta = cap_menus('contratos','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = ""; 
    if ($a=='contratos'){  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
        $rta .= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"act_lista('$a',this,'contratos.php');\"></li>";
    }
    return $rta;
}

function cmp_contratos(){
    $rta = "";
    $t = [
        'id_thcon' => '', 'n_contrato' => '', 'tipo_cont' => '', 'fecha_inicio' => '', 
        'fecha_fin' => '', 'valor_contrato' => '', 'perfil_profesional' => '', 
        'perfil_contratado' => '', 'rol' => '', 'tipo_expe' => '', 'fecha_expe' => '', 'semestre' => ''
    ];
    
    $d = get_contratos();
    if ($d == "") { $d = $t; }
    
    // Detectar si es edición
    $es_edicion = !empty($d) && isset($d['n_contrato']) && $d['n_contrato'] != '';
    
    $w = 'contratos';
    $o = 'contratoinfo';
    
    // Información del contrato
    $c[] = new cmp($o,'e',null,'INFORMACIÓN DEL CONTRATO',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('id_th','h',15,$_POST['id_thcon'] ?? '',$w.' '.$o,'id_th','id_th',null,'####',false,false);
    $c[] = new cmp('n_contrato','nu','11',$d['n_contrato'],$w.' '.$o,'N° Contrato','n_contrato',null,null,true,true,'','col-3');
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('tipo_cont','s','3',$d['tipo_cont'],$w.' '.$o,'Tipo de Contrato','tipo_cont',null,null,true,true,'','col-25');
    $c[] = new cmp('fecha_inicio','d','',$d['fecha_inicio'],$w.' '.$o,'Fecha Inicio','fecha_inicio',null,null,true,true,'','col-25',"validDate(this,-30,365);");
    $c[] = new cmp('fecha_fin','d','',$d['fecha_fin'],$w.' '.$o,'Fecha Fin','fecha_fin',null,null,true,true,'','col-25',"validDate(this,1,730);");
    $c[] = new cmp('valor_contrato','nu','11',$d['valor_contrato'],$w.' '.$o,'Valor Total Contrato','valor_contrato',null,null,true,true,'','col-25');
    
    // Perfiles profesionales
    $o2 = 'perfilinfo';
    $c[] = new cmp($o2,'l',null,'',$w);
    $c[] = new cmp('perfil_profesional','s','3',$d['perfil_profesional'],$w.' '.$o2,'Perfil Profesional','perfil_profesional',null,null,true,true,'','col-35');
    $c[] = new cmp('perfil_contratado','s','3',$d['perfil_contratado'],$w.' '.$o2,'Perfil Contratado Requerido','perfil_contratado',null,null,true,true,'','col-35');
    $c[] = new cmp('rol','s','3',$d['rol'],$w.' '.$o2,'Rol Contratado','rol',null,null,true,true,'','col-3');
    
    // Experiencia
    $o3 = 'experiencia';
    $c[] = new cmp($o3,'l',null,'',$w);
    $c[] = new cmp('tipo_expe','s','3',$d['tipo_expe'],$w.' '.$o3,'¿Bachiller con experiencia o formación en salud/social?','tipo_expe',null,null,true,true,'','col-5');
    $c[] = new cmp('fecha_expe','d','',$d['fecha_expe'],$w.' '.$o3,'Fecha del Certificado','fecha_expe',null,null,false,true,'','col-3',"validDate(this,-3650,0);");
    $c[] = new cmp('semestre','nu','1',$d['semestre'],$w.' '.$o3,'Semestres Cursados','semestre',null,null,false,true,'','col-2');
    
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    return $rta;
}

function get_contratos(){
    if($_POST['id'] == '0'){
        return "";
    } else {
        // Validar hash para editar
        $hash = $_POST['id'] ?? '';
        $real_id = null;
        
        // Buscar el ID real usando el hash
        if (isset($_SESSION['hash'])) {
            foreach ($_SESSION['hash'] as $key => $value) {
                if (strpos($key, $hash . '_contratos') !== false) {
                    $real_id = $value;
                    break;
                }
            }
        }
        
        // Si no encontró el hash, intentar con el ID directo
        if (!$real_id) {
            $id = divide($_POST['id']);
            $real_id = $id[0];
        }
        
        $sql = "SELECT `id_thcon`,`n_contrato`, `tipo_cont`, `fecha_inicio`, `fecha_fin`, 
                       `valor_contrato`, `perfil_profesional`, `perfil_contratado`, `rol`,`tipo_expe`, 
                       `fecha_expe`, `semestre`, `estado`
                FROM `th_contratos` 
                WHERE id_thcon = '$real_id'";
        
        $info = datos_mysql($sql);
        if (!$info['responseResult']) {
            return '';
        }
        
        return $info['responseResult'][0];
    } 
}

function gra_contratos(){
    $usu = $_SESSION['us_sds'];
    
    // Validar si es inserción o actualización
    $hash = $_POST['id'] ?? '';
    $real_id = null;
    
    // Buscar el ID real usando el hash para actualización
    if ($hash != '0' && isset($_SESSION['hash'])) {
        foreach ($_SESSION['hash'] as $key => $value) {
            if (strpos($key, $hash . '_contratos') !== false) {
                $real_id = $value;
                break;
            }
        }
    }
    
    // Si no encontró el hash y no es nuevo registro, intentar con el ID directo
    if (!$real_id && $hash != '0') {
        $id = divide($_POST['id']);
        $real_id = $id[0];
    }
    
    if($hash == '0' || !$real_id) {
        // INSERT - Nuevo contrato
        $sql = "INSERT INTO th_contratos (id_th, n_contrato, tipo_cont, fecha_inicio, fecha_fin, valor_contrato, perfil_profesional, perfil_contratado, rol, tipo_expe, fecha_expe, semestre, usu_create, fecha_create, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'A')";
        $params = [
            ['type' => 'i', 'value' => $_POST['id_th'] ?? ''],
            ['type' => 'i', 'value' => $_POST['n_contrato'] ?? ''],
            ['type' => 's', 'value' => $_POST['tipo_cont'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_inicio'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_fin'] ?? ''],
            ['type' => 'i', 'value' => $_POST['valor_contrato'] ?? ''],
            ['type' => 's', 'value' => $_POST['perfil_profesional'] ?? ''],
            ['type' => 's', 'value' => $_POST['perfil_contratado'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['tipo_expe'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_expe'] ?? ''],
            ['type' => 'i', 'value' => $_POST['semestre'] ?? ''],
            ['type' => 's', 'value' => $usu]
        ];
    } else {
        // UPDATE - Actualizar contrato existente
        $sql = "UPDATE th_contratos SET n_contrato=?, tipo_cont=?, fecha_inicio=?, fecha_fin=?, valor_contrato=?, perfil_profesional=?, perfil_contratado=?, rol=?, tipo_expe=?, fecha_expe=?, semestre=?, usu_update=?, fecha_update=NOW() 
                WHERE id_thcon=?";
        $params = [
            ['type' => 'i', 'value' => $_POST['n_contrato'] ?? ''],
            ['type' => 's', 'value' => $_POST['tipo_cont'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_inicio'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_fin'] ?? ''],
            ['type' => 'i', 'value' => $_POST['valor_contrato'] ?? ''],
            ['type' => 's', 'value' => $_POST['perfil_profesional'] ?? ''],
            ['type' => 's', 'value' => $_POST['perfil_contratado'] ?? ''],
            ['type' => 's', 'value' => $_POST['rol'] ?? ''],
            ['type' => 's', 'value' => $_POST['tipo_expe'] ?? ''],
            ['type' => 's', 'value' => $_POST['fecha_expe'] ?? ''],
            ['type' => 'i', 'value' => $_POST['semestre'] ?? ''],
            ['type' => 's', 'value' => $usu],
            ['type' => 'i', 'value' => $real_id]
        ];
    }
    
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Funciones para opciones de selects
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

function formato_dato($a, $b, $c, $d) {
    $b = strtolower($b);
    $rta = $c[$d];
    if ($a == 'contratos' && $b == 'acciones') {
        $acciones = [];
        $hash_id = myhash($c['ACCIONES']);
        $accionesDisponibles = [
            'contratos' => [
                'icono' => 'fa-solid fa-edit',
                'clase' => 'ico',
                'title' => 'Editar Contrato',
                'permiso' => acceso('th'),
                'hash' => $hash_id,
                'evento' => "mostrar('contratos','pro',event,'','contratos.php',7,'contratos','{$hash_id}');"
            ],
            'ver' => [
                'icono' => 'fa-solid fa-eye',
                'clase' => 'ico',
                'title' => 'Ver Detalles',
                'permiso' => acceso('th'),
                'hash' => $hash_id,
                'evento' => "mostrar('contratos','{$c['ACCIONES']}',this,'contratos.php',1);"
            ]
        ];
        
        foreach ($accionesDisponibles as $key => $accion) {
            if ($accion['permiso']) {
                limpiar_hashes();
                $_SESSION['hash'][$accion['hash'] . '_' . $key] = $c['ACCIONES'];
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