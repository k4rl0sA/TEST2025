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

function lis_th(){
$info = datos_mysql("SELECT COUNT(*) total FROM th T WHERE " . whe_th());
    $total = $info['responseResult'][0]['total'];
    $regxPag = 20;
    $pag = (isset($_POST['pag-th'])) ? ($_POST['pag-th'] - 1) * $regxPag : 0;

    $sql = "SELECT T.id_th AS ACCIONES, T.tipo_doc AS 'Tipo Documento', T.n_documento AS 'N° Documento', concat (T.nombre1, ' ', T.nombre2, ' ', T.apellido1, ' ', T.apellido2) AS 'Nombres y Apellidos del Colaborador', T.n_contacto AS 'N° Contacto', T.estado AS 'Estado Usuario' 
	        FROM th T  
            WHERE " . whe_th();    
    $sql .= " ORDER BY T.fecha_create";
    $sql .= ' LIMIT ' . $pag . ',' . $regxPag;
	/* var_dump($sql); */
		$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"th",$regxPag);
}

function whe_th() {
    $sql2 = " SELECT subred FROM usuarios where id_usuario='" . $_SESSION['us_sds'] . "'";
	$info=datos_mysql($sql2);
    $subred = $info['responseResult'][0]['subred'];
	//var_dump($sql2);
    $sql = " T.subred = " . intval($subred);
    if ($_POST['fusu']) {
        $sql .= " AND n_documento ='" . $_POST['fusu'] . "'";
    }
	//var_dump($sql1);
	return $sql;
}


function focus_th(){
 return 'th';
}

function men_th(){
 $rta=cap_menus('th','pro');
 return $rta;
}


function cap_menus($a,$b='cap',$con='con') {
  $rta = ""; 
  if ($a=='th'){  
	$rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	// $rta .= "<li class='icono $a actualizar'    title='Actualizar'      Onclick=\"act_lista('$a',this);\"></li>";
  }
  return $rta;
}

function cmp_th(){
$rta="";
$t=['tipo_doc'=>'','documento'=>'','nombre1'=>'','nuc2'=>'','nombre2'=>'','apellido1'=>'','apellido2'=>'','fecha_nacimiento'=>'','sexo'=>'','contacto'=>'','email'=>''];
$d = get_th();
if ($d==""){$d=$t;}
$edt = !empty($d) && isset($d['tipo_doc']) && $d['tipo_doc'] != '';
 $w='th';
 $o='infobasica';
 $c[]=new cmp($o,'l',null,'',$w);
 $c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
 $c[]=new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$o,'Tipo documento','tipo_doc',null,null,true,!$edt,'','col-4');
 $c[]=new cmp('documento','nu','999999999999999999',$d['documento'],$w.' '.$o,'NÚMERO DE DOCUMENTO','documento',null,null,true,!$edt,'','col-3');

 $c[]=new cmp($o,'l',null,'',$w);
 $c[]=new cmp('nombre1','t','30',$d['nombre1'],$w.' '.$o,'Primer Nombre','nombre1',null,null,true,true,'','col-25');
 $c[]=new cmp('nombre2','t','30',$d['nombre2'],$w.' '.$o,'Segundo Nombre','nombre2',null,null,false,true,'','col-25');
 $c[]=new cmp('apellido1','t','30',$d['apellido1'],$w.' '.$o,'Primer Apellido','apellido1',null,null,true,true,'','col-25');
 $c[]=new cmp('apellido2','t','30',$d['apellido2'],$w.' '.$o,'Segundo Apellido','apellido2',null,null,false,true,'','col-25');
 $c[]=new cmp('fecha_nacimiento','d','',$d['fecha_nacimiento'],$w.' '.$o,'Fecha de nacimiento','fecha_nacimiento',null,null,true,true,'','col-25',"validDate(this,-43800,0);");
 $c[]=new cmp('sexo','s','3',$d['sexo'],$w.' '.$o,'Sexo','sexo',null,null,true,true,'','col-25');
 $c[]=new cmp('contacto','nu','9999999999',$d['contacto'],$w.' '.$o,'N° Contacto','contacto','rgxphone',null,true,true,'','col-25');
 $c[]=new cmp('email','em','50',$d['email'],$w.' '.$o,'Correo Electronico','email','rgxmail',null,true,true,'','col-25'); 
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}


function get_th(){
	if($_POST['id']=='0'){
		return "";
	}else{
		// Validar hash para editar
		$hash = $_POST['id'] ?? '';
		$real_id = null;
		
		// Buscar el ID real usando el hash
		if (isset($_SESSION['hash'])) {
			foreach ($_SESSION['hash'] as $key => $value) {
				if (strpos($key, $hash . '_editar') !== false) {
					$real_id = $value;
					break;
				}
			}
		}
		
		// Si no encontró el hash, intentar con el ID directo (modo compatibilidad)
		if (!$real_id) {
			$id = divide($_POST['id']);
			$real_id = $id[0];
		}
		
		$sql="SELECT `id_th`, `tipo_doc`, `n_documento`, `nombre1`, `nombre2`, `apellido1`, `apellido2`, `fecha_nacimiento`, `sexo`, `n_contacto`, `correo`, `subred`, `estado`
 		FROM `th` 
 		WHERE id_th='$real_id'";
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return '';
		}
		// Mapear campos para el formulario
		$row = $info['responseResult'][0];
		$row['documento'] = $row['n_documento'];
		$row['contacto'] = $row['n_contacto'];
		$row['email'] = $row['correo'];
		return $row;
	} 
}

function gra_th(){
	$usu = $_SESSION['us_sds'];
	
	// Obtener subred del usuario
	$sql_subred = "SELECT subred FROM usuarios WHERE id_usuario = '$usu'";
	$info_subred = datos_mysql($sql_subred);
	$subred = $info_subred['responseResult'][0]['subred'];
	
	// Validar si es inserción o actualización
	$hash = $_POST['id'] ?? '';
	$real_id = null;
	
	// Buscar el ID real usando el hash para actualización
	if ($hash != '0' && isset($_SESSION['hash'])) {
		foreach ($_SESSION['hash'] as $key => $value) {
			if (strpos($key, $hash . '_editar') !== false) {
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
		// INSERT - Nuevo registro
		$sql = "INSERT INTO th (tipo_doc, n_documento, nombre1, nombre2, apellido1, apellido2, fecha_nacimiento, sexo, n_contacto, correo, subred, usu_create, fecha_create, estado) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'A')";
		$params = [
			['type' => 's', 'value' => $_POST['tipo_doc'] ?? ''],
			['type' => 'i', 'value' => $_POST['documento'] ?? ''],
			['type' => 's', 'value' => $_POST['nombre1'] ?? ''],
			['type' => 's', 'value' => $_POST['nombre2'] ?? ''],
			['type' => 's', 'value' => $_POST['apellido1'] ?? ''],
			['type' => 's', 'value' => $_POST['apellido2'] ?? ''],
			['type' => 's', 'value' => $_POST['fecha_nacimiento'] ?? ''],
			['type' => 's', 'value' => $_POST['sexo'] ?? ''],
			['type' => 's', 'value' => $_POST['contacto'] ?? ''],
			['type' => 's', 'value' => $_POST['email'] ?? ''],
			['type' => 'i', 'value' => $subred],
			['type' => 's', 'value' => $usu]
		];
	} else {
		// UPDATE - Actualizar registro existente
		$sql = "UPDATE th SET tipo_doc=?, n_documento=?, nombre1=?, nombre2=?, apellido1=?, apellido2=?, fecha_nacimiento=?, sexo=?, n_contacto=?, correo=?, usu_update=?, fecha_update=NOW() 
		        WHERE id_th=?";
		$params = [
			['type' => 's', 'value' => $_POST['tipo_doc'] ?? ''],
			['type' => 'i', 'value' => $_POST['documento'] ?? ''],
			['type' => 's', 'value' => $_POST['nombre1'] ?? ''],
			['type' => 's', 'value' => $_POST['nombre2'] ?? ''],
			['type' => 's', 'value' => $_POST['apellido1'] ?? ''],
			['type' => 's', 'value' => $_POST['apellido2'] ?? ''],
			['type' => 's', 'value' => $_POST['fecha_nacimiento'] ?? ''],
			['type' => 's', 'value' => $_POST['sexo'] ?? ''],
			['type' => 's', 'value' => $_POST['contacto'] ?? ''],
			['type' => 's', 'value' => $_POST['email'] ?? ''],
			['type' => 's', 'value' => $usu],
			['type' => 'i', 'value' => $real_id]
		];
	}
	
	$rta = mysql_prepd($sql, $params);
	return $rta;
}
 

function opc_tipo_doc($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 2",$id);
    }
function opc_sexo($id=''){
	    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=21 and estado='A' ORDER BY CAST(idcatadeta AS UNSIGNED)",$id);
	}	
/***************************************************************************/
function formato_dato($a, $b, $c, $d) {
    $b = strtolower($b);
    $rta = $c[$d];
    if ($a == 'th' && $b == 'acciones') {
        $acciones = [];
        // Definición de acciones posibles
        $hash_id = myhash($c['ACCIONES']);
        $accionesDisponibles = [
            'editar' => [
                'icono' => 'fa-solid fa-edit',
                'clase' => 'ico',
                'title' => 'Editar Colaborador',
                'permiso' => acceso('th'),
                'hash' => $hash_id,
                'evento' => "mostrar('th','pro',event,'','lib.php',7,'th','{$hash_id}');"
            ],
            'contratos' => [
                'icono' => 'fa-regular fa-id-badge',
                'clase' => 'ico',
                'title' => 'Contratos',
                'permiso' => acceso('th'),
                'hash' => $hash_id,
                'evento' => "mostrar('contratos','pro',event,'','contratos.php',7,'contratos','0'); document.getElementById('id_th').value='{$c['ACCIONES']}';"
            ],
            'rev' => [
                'icono' => 'fa-regular fa-calendar-check',
                'clase' => 'ico',
                'title' => 'Ver Detalles',
                'permiso' => acceso('th'),
                'hash' => $hash_id,
                'evento' => "mostrar('th','{$c['ACCIONES']}',this,'lib.php',1);"
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