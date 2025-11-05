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
    $hash_id = $_POST['id'] ?? '';
    $session_hash = $_SESSION['hash'] ?? [];
    $sufijos = ['_th', '_contratos', '_editar'];
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
    $info = datos_mysql("SELECT COUNT(*) total FROM th_contratos TC WHERE TC.estado = 'A' AND TC.idth = '$id_th'");
    $total = $info['responseResult'][0]['total'];
    $regxPag = 10;
    $pag = (isset($_POST['pag-contratos'])) ? ($_POST['pag-contratos'] - 1) * $regxPag : 0;
    $sql = "SELECT CONCAT_WS('_',TC.id_thcon,TC.idth) AS ACCIONES, 
                    TC.id_thcon AS 'Cod Registro',
                   TC.n_contrato AS 'N° Contrato', 
                   FN_CATALOGODESC(326, TC.tipo_cont) AS 'Tipo Vinculación',
                   TC.fecha_inicio AS 'Fecha Inicio', 
                   TC.fecha_fin AS 'Fecha Fin',
                   CONCAT('$ ', FORMAT(TC.valor_contrato, 0)) AS 'Valor Contrato',
                   FN_CATALOGODESC(323, TC.perfil_profesional) AS 'Perfil Profesional',
                   TC.estado AS 'Estado'
            FROM th_contratos TC  
            WHERE TC.estado = 'A' AND TC.idth = '$id_th'";
    $sql .= " ORDER BY TC.fecha_create DESC";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "contratos", $regxPag, 'contratos.php');    
    }
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
	$rta .="<div class='encabezado vivienda'>CONTRATOS REALIZADOS</div><div class='contenido' id='gestion-lis' >".lis_contratos()."</div></div>";
    $w = 'contratos';
    $o = 'contratoinfo';
    $c[] = new cmp($o,'e',null,'INFORMACIÓN DEL CONTRATO',$w);
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('id','h',15,$_POST['id'] ?? '',$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('n_contrato','nu','11','',$w.' '.$o,'N° Contrato','n_contrato',null,null,true,true,'','col-3');
    $c[] = new cmp($o,'l',null,'',$w);
    $c[] = new cmp('tipo_cont','s','3','',$w.' '.$o,'Tipo de Contrato','tipo_cont',null,null,true,true,'','col-25');
    $c[] = new cmp('fecha_inicio','d','','',$w.' '.$o,'Fecha Inicio','fecha_inicio',null,null,true,true,'','col-25',"validDate(this,-730,362);");
    $c[] = new cmp('fecha_fin','d','','',$w.' '.$o,'Fecha Fin','fecha_fin',null,null,true,true,'','col-25');
    $c[] = new cmp('valor_contrato','nu','11','',$w.' '.$o,'Valor Total Contrato','valor_contrato',null,null,true,true,'','col-25');
    
    $o2 = 'perfilinfo';
    $c[] = new cmp($o2,'l',null,'',$w);
    $c[] = new cmp('perfil_profesional','s','3','',$w.' '.$o2,'Perfil Profesional','perfil_profesional',null,null,true,true,'','col-35');
    $c[] = new cmp('perfil_contratado','s','3','',$w.' '.$o2,'Perfil Contratado Requerido','perfil_contratado',null,null,true,true,'','col-35',"selectDepend('perfil_contratado','rol','contratos.php');");
    $c[] = new cmp('rol','s','3','',$w.' '.$o2,'Rol Contratado','rol',null,null,true,true,'','col-3',"glineTH();");
    
    $o3 = 'experiencia';
    $c[] = new cmp($o3,'l',null,'',$w);
    $c[] = new cmp('tipo_expe','s','3','',$w.' GlIn '.$o3,'¿Bachiller con experiencia o formación en salud/social?','tipo_expe',null,null,false,false,'','col-5',"certTH();");
    $c[] = new cmp('fecha_expe','d','','',$w.' CeRt '.$o3,'Fecha del Certificado','fecha_expe',null,null,false,false,'','col-3',"validDate(this,-3650,0);");
    $c[] = new cmp('semestre','nu','1','',$w.' CeRt '.$o3,'Semestres Cursados','semestre',null,null,false,false,'','col-2');
    
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    return $rta;
}

function get_contratos(){
    // Usar la función global idReal para obtener el ID del contrato
    $real_id = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_contratos');
  // Usar datos_mysql en lugar de mysql_prepd para consistencia
  $sql = "SELECT CONCAT_WS('_',id_thcon,idth),`n_contrato`, `tipo_cont`, `fecha_inicio`, `fecha_fin`,`valor_contrato`, `perfil_profesional`, `perfil_contratado`, `rol`,`tipo_expe`,`fecha_expe`, `semestre`, `estado`
            FROM `th_contratos` WHERE id_thcon = '" . intval($real_id) . "'";
    $info = datos_mysql($sql);
    return json_encode($info['responseResult'][0]);
}

function gra_contratos(){
    $usu = $_SESSION['us_sds'];
    $id=divide($_POST['id']); 
    
    $idth = idReal($_POST['id'] ?? '', $_SESSION['hash'] ?? [], '_contratos');

    if(count($id) == 1) {        
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
        $sql = "UPDATE th_contratos SET n_contrato=?, tipo_cont=?, fecha_inicio=?, fecha_fin=?, valor_contrato=?, perfil_profesional=?, perfil_contratado=?, rol=?, tipo_expe=?, fecha_expe=?, semestre=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR),ajustar=0
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
            ['type' => 'i', 'value' => intval($id[0])]
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

function ajustar($id){
    $hash = $id ?? '';
    $session_hash = $_SESSION['hash'] ?? [];
    $suffixes = ['_contratos'];
    $idth = null;

    // Intentar resolver el id real probando varios sufijos
    foreach ($suffixes as $sufijo) {
        $res = idReal($hash, $session_hash, $sufijo);
        if (!empty($res)) {
            $idth = $res;
            break;
        }
    }

    $id_thcon = intval($idth);
    $sql = "SELECT COUNT(*) AS total FROM th_contratos WHERE id_thcon = $id_thcon AND ajustar = 1 AND estado = 'A'";
    $info = datos_mysql($sql);

    return (!empty($info['responseResult'][0]['total']) && $info['responseResult'][0]['total'] > 0);
}

function formato_dato($a, $b, $c, $d){
      $b = strtolower($b);
    $rta = $c[$d];
/*     var_dump($c);
   var_dump($a);
   var_dump($b); */
     if ($a == 'contratos' && $b == 'acciones') {
        $acciones = [];
        // Definición de acciones posibles para contratos
        $hash_id = myhash($c['ACCIONES']);
        $accionesDisponibles = [
            'editar' => [
                'icono' => 'fa-solid fa-edit',
                'clase' => 'ico',
                'title' => 'Editar Contrato',
                'permiso' => true,
                'hash' => $hash_id,
                'evento' => "setTimeout(getDataFetch,500,'contratos',event,this,'../th/contratos.php',[]);"
            ]
        ];
        
        foreach ($accionesDisponibles as $key => $accion) {
            if (ajustar($accion['hash'])) {
                if ($accion['permiso']) {
                    limpiar_hashes();
                    $_SESSION['hash'][$accion['hash'] . '_contratos'] = $c['ACCIONES'];
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