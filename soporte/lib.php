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

function lis_soporte() {
    $info = datos_mysql("SELECT COUNT(*) total FROM soporte WHERE 1 " . whe_soporte());
    $total = $info['responseResult'][0]['total'];
    $regxPag = 12;
    $pag = (isset($_POST['pag-soporte'])) ? ($_POST['pag-soporte']-1) * $regxPag : 0;
    $sql = "SELECT	idsoporte AS ACCIONES,idsoporte AS Ticket,	idpeople AS 'Cod Persona',	documento,	tipo_doc AS Tipo,	sexo,	fecha_nacio AS Nacio,	cod_predio AS Predio,	cod_familia AS Familia,	cod_registro AS Registro,	FN_CATALOGODESC(286,formulario) AS Accion,	error,	ok,	prioridad,	observaciones, aprueba,	s.usu_creo AS Creo,U1.subred AS Subred,s.fecha_create AS 'Fecha Creo',FN_CATALOGODESC(285,s.estado) AS Estado
    FROM	soporte s
	LEFT JOIN usuarios u ON s.aprueba = u.perfil
    LEFT JOIN usuarios U1 ON s.usu_creo = U1.id_usuario
WHERE 1";
    $sql .= whe_soporte();
    $sql .= " ORDER BY fecha_create DESC LIMIT $pag, $regxPag";
	// var_dump($sql);
    $datos = datos_mysql($sql);
    return create_table($total, $datos["responseResult"], "soporte", $regxPag);
}

function whe_soporte() {
    $sql = "";
    if (!empty($_POST['ftic'])) 
        $sql .= " AND idsoporte LIKE '%" . cleanTx($_POST['ftic']) . "%'";
    if (!empty($_POST['fpredio']))   
        $sql .= " AND cod_predio = '" . cleanTx($_POST['fpredio']) . "'";
    if (!empty($_POST['fuser']))   
        $sql .= " AND idpeople = '" . cleanTx($_POST['fuser']) . "'";
    if (!empty($_POST['fdigita']))	
        $sql .= " AND (usu_creo='" . cleanTx($_POST['fdigita']) . "' OR aprueba = 'PROAPO')";
    if (!empty($_POST['fest'])) 
        $sql .= " AND estado = '" . intval($_POST['fest']) . "'";
    if(!empty($_POST['fsubred']))
        $sql .= " AND U1.subred = '" . cleanTx($_POST['fsubred']) . "'";
    return $sql;
}

function cmp_soporte() {
    $rta = "";
    $t = ['idsoporte' => '', 'idpeople' => '', 'documento' => '', 'tipo_doc' => '', 'sexo' => '', 'fecha_nacio' => '',
        'cod_predio' => '', 'cod_familia' => '', 'cod_registro' => '', 'formulario' => '', 'error' => '', 'ok' => '',
        'prioridad' => '', 'observaciones' => '', 'rta' => '', 'usu_creo' => '', 'fecha_create' => '', 'usu_update' => '', 'fecha_update' => '', 'estado' => ''];
    $w = 'soporte';
    $d = get_soporte();
    if ($d == "") { $d = $t; }
    $c[] = new cmp('idsoporte', 'h', 15, $d['idsoporte'], $w, '', '', null, '####', false, false);
    $c[] = new cmp('idpeople', 'n', 20, $d['idpeople'], $w, 'ID People', 'idpeople');
    $c[] = new cmp('documento', 'n', 20, $d['documento'], $w, 'Documento', 'documento');
    $c[] = new cmp('tipo_doc', 't', 2, $d['tipo_doc'], $w, 'Tipo Doc', 'tipo_doc');
    $c[] = new cmp('sexo', 't', 1, $d['sexo'], $w, 'Sexo', 'sexo');
    $c[] = new cmp('fecha_nacio', 'd', 10, $d['fecha_nacio'], $w, 'Fecha Nacimiento', 'fecha_nacio');
    $c[] = new cmp('cod_predio', 'n', 11, $d['cod_predio'], $w, 'Cod Predio', 'cod_predio');
    $c[] = new cmp('cod_familia', 'n', 11, $d['cod_familia'], $w, 'Cod Familia', 'cod_familia');
    $c[] = new cmp('cod_registro', 'n', 11, $d['cod_registro'], $w, 'Cod Registro', 'cod_registro');
    $c[] = new cmp('formulario', 'n', 11, $d['formulario'], $w, 'Formulario', 'formulario');
    $c[] = new cmp('error', 't', 100, $d['error'], $w, 'Error', 'error');
    $c[] = new cmp('ok', 't', 100, $d['ok'], $w, 'OK', 'ok');
    $c[] = new cmp('prioridad', 't', 1, $d['prioridad'], $w, 'Prioridad', 'prioridad');
    $c[] = new cmp('observaciones', 'e', null, $d['observaciones'], $w, 'Observaciones', 'observaciones');
    $c[] = new cmp('rta', 'n', 1, $d['rta'], $w, 'RTA', 'rta');
    $c[] = new cmp('estado', 'n', 2, $d['estado'], $w, 'Estado', 'estado');
    for ($i = 0; $i < count($c); $i++) $rta .= $c[$i]->put();
    return $rta;
}

function get_soporte() {
    if ($_POST['id'] == 0) {
        return "";
    } else {
        $id = intval($_POST['id']);
        $sql = "SELECT * FROM soporte WHERE idsoporte = '$id'";
        $info = datos_mysql($sql);
        return $info['responseResult'][0];
    }
}

function focus_ajustar(){
	return 'ajustar';
   }
   
function men_ajustar(){
	$rta=cap_menus('ajustar','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='ajustar') {  
		$rta .= "<li class='icono $a  grabar' title='Grabar' Onclick=\"grabar('$a',this);\" ></li>";
	}
	return $rta;
  }
   
function gra_soporte() {
    $campos = [
        'idpeople', 'documento', 'tipo_doc', 'sexo', 'fecha_nacio', 'cod_predio', 'cod_familia', 'cod_registro',
        'formulario', 'error', 'ok', 'prioridad', 'observaciones', 'rta', 'usu_creo', 'fecha_create', 'estado'
    ];

    if ($_POST['id'] == 0) {
        // Insertar nuevo registro
        $sql = "INSERT INTO soporte (" . implode(',', $campos) . ")
                VALUES (" . implode(',', array_fill(0, count($campos), '?')) . ")";
        $params = [
            ['type' => 'i', 'value' => $_POST['idpeople']],
            ['type' => 'i', 'value' => $_POST['documento']],
            ['type' => 's', 'value' => $_POST['tipo_doc']],
            ['type' => 's', 'value' => $_POST['sexo']],
            ['type' => 's', 'value' => $_POST['fecha_nacio']],
            ['type' => 'i', 'value' => $_POST['cod_predio']],
            ['type' => 'i', 'value' => $_POST['cod_familia']],
            ['type' => 'i', 'value' => $_POST['cod_registro']],
            ['type' => 'i', 'value' => $_POST['formulario']],
            ['type' => 's', 'value' => $_POST['error']],
            ['type' => 's', 'value' => $_POST['ok']],
            ['type' => 's', 'value' => $_POST['prioridad']],
            ['type' => 's', 'value' => $_POST['observaciones']],
            ['type' => 'i', 'value' => $_POST['rta']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
            ['type' => 's', 'value' => date('Y-m-d H:i:s')],
            ['type' => 'i', 'value' => $_POST['estado']]
        ];
        $rta = mysql_prepd($sql, $params);
    } else {
        // Actualizar registro existente
        $id = intval($_POST['id']);
        $sql = "UPDATE soporte SET
                    idpeople = ?, documento = ?, tipo_doc = ?, sexo = ?, fecha_nacio = ?, cod_predio = ?, cod_familia = ?, cod_registro = ?,
                    formulario = ?, error = ?, ok = ?, prioridad = ?, observaciones = ?, rta = ?, usu_update = ?, fecha_update = ?, estado = ?
                WHERE idsoporte = ?";
        $params = [
            ['type' => 'i', 'value' => $_POST['idpeople']],
            ['type' => 'i', 'value' => $_POST['documento']],
            ['type' => 's', 'value' => $_POST['tipo_doc']],
            ['type' => 's', 'value' => $_POST['sexo']],
            ['type' => 's', 'value' => $_POST['fecha_nacio']],
            ['type' => 'i', 'value' => $_POST['cod_predio']],
            ['type' => 'i', 'value' => $_POST['cod_familia']],
            ['type' => 'i', 'value' => $_POST['cod_registro']],
            ['type' => 'i', 'value' => $_POST['formulario']],
            ['type' => 's', 'value' => $_POST['error']],
            ['type' => 's', 'value' => $_POST['ok']],
            ['type' => 's', 'value' => $_POST['prioridad']],
            ['type' => 's', 'value' => $_POST['observaciones']],
            ['type' => 'i', 'value' => $_POST['rta']],
            ['type' => 's', 'value' => $_SESSION['us_sds']],
            ['type' => 's', 'value' => date('Y-m-d H:i:s')],
            ['type' => 'i', 'value' => $_POST['estado']],
            ['type' => 'i', 'value' => $id]
        ];
        $rta = mysql_prepd($sql, $params);
    }
    return $rta;
}

function approve_interl_soporte(){
    $hash = $_REQUEST['id'];
    $accion = $_REQUEST['accion'] ?? '';
    $id = IdHash($hash,$accion); // Centralizado
    if (!$id) return "ID inválido o expirado";
    $sql="UPDATE soporte SET aprueba=?, usu_update=?, fecha_update=DATE_SUB(NOW(), INTERVAL 5 HOUR), estado=4 
          WHERE idsoporte=?";
    $params = [
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => $_SESSION['us_sds']],
        ['type' => 'i', 'value' => $id]
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}
function get_documento_soporte(){
    $id = IdHash($_REQUEST['id'], $_REQUEST['accion'] ?? ''); // <-- ahora sí busca con el sufijo correcto
    if (!$id) return "ID inválido o expirado";
    $sql = "SELECT p.idpersona 
            FROM person p 
            INNER JOIN soporte s ON p.idpeople = s.idpeople  
            WHERE s.idsoporte = $id";
    $info = datos_mysql($sql);
    $doc = isset($info['responseResult'][0]['idpersona']) ? $info['responseResult'][0]['idpersona'] : '';
    return json_encode(['doc' => $doc]);
}

function myhash($a){
    $hash = md5($a . $_SESSION['us_sds'] . 'D2AC5E5211884EA15F1E950D1445C5E8');
    return $hash;
}

function IdHash($hash, $accion = '') {
    $key = $accion ? $hash . '_' . $accion : $hash;
    if (isset($_SESSION['hash'][$hash])) {
        return $_SESSION['hash'][$hash];
    }
    if (isset($_SESSION['hash'][$key])) {
        return $_SESSION['hash'][$key];
    }
    return null;
}
function limpiar_hashes($max = 500) {
    if (!isset($_SESSION['hash']) || !is_array($_SESSION['hash'])) return;
    // Si hay más de $max hashes, elimina los más antiguos
    if (count($_SESSION['hash']) > $max) {
        // Mantén solo los últimos $max elementos
        $_SESSION['hash'] = array_slice($_SESSION['hash'], -$max, $max, true);
    }
}    
function formato_dato($a, $b, $c, $d) {
    $b = strtolower($b);
    $rta = $c[$d];
        if ($a == 'soporte' && $b == 'acciones') {
            $acciones=[];
            // Definición de acciones posibles
            $accionesDisponibles = [
                'INTERLOCAL' => ['condicion' => ($c['Accion'] === 'INTERLOCAL' && $c['Estado'] == 'POR APROBAR'),
                    'icono' => 'fa-solid fa-thumbs-up',
                    'clase' => 'ico',
                    'title' => 'Aprobar Interlocal',
                    'permiso' => acceso('soporte'),
                    'hash' => myhash($c['ACCIONES']),
                    'evento' => '',
                ],
              /*   // Puedes agregar más acciones aquí fácilmente
                'ELIMINAR' => [
                  'condicion' => ($c['Estado'] == 'POR APROBAR'),
                  'icono' => 'fa-solid fa-trash',
                  'clase' => 'ico',
                  'title' => 'Eliminar',
                  'permiso' => acceso('soporte'),
                  'hash' => myhash($c['ACCIONES'] . 'ELIMINAR'),
                  'evento' => '',
                ] */
            ];
        foreach ($accionesDisponibles as $key => $accion) {
            if ($accion['condicion'] && $accion['permiso']) {
                limpiar_hashes();
                $_SESSION['hash'][$accion['hash'] . '_' . $key] = $c['ACCIONES'];
                $acciones[] = "<li title='{$accion['title']}'><i class='{$accion['icono']} {$accion['clase']}' id='{$accion['hash']}' data-acc='{$key}'></i></li>";
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
		// return $rta;
	   }
