<?php
// filepath: vscode-vfs://github/k4rl0sA/TEST2025/crea-fam/solicitudes.php
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
      if (is_array($rta)) echo json_encode($rta);
      else echo $rta;
  }
}

// Componente del formulario
function cmp_solicitudes(){
    $rta = "";
    $w = 'solicitudes';
    $o = 'inftrasint';
    $t = ['documento'=>'','tipo'=>''];
    $c[] = new cmp($o,'e',null,'SOLICITUDES APLICATIVO',$w);
    //incluir campos idpeople
    $c[]=new cmp('solicitud','s',3,'',$w.' '.$o,'Tipo de Solicitud','solicitud',null,'####',true,true,'','col-1');
    $c[]=new  cmp('descripcion','a',2,'',$w.' '.$o,'Descripción','tipo',null,null,true,true,'','col-9');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

// Enfocar el formulario
function focus_solicitudes(){
    return 'solicitudes';
}

// Menú de acciones
function men_solicitudes(){
    $rta = cap_menus('solicitudes','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc = rol($a);
    if ($a=='solicitudes' && isset($acc['crear']) && $acc['crear']=='SI') {  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    return $rta;
}
// Guardar traslado interlocalidad/subred
function gra_solicitudes() {
    $solicitud = $_POST['solicitud'];
    if($solicitud == ''){
        return "Error: El campo Tipo de Solicitud es obligatorio.";
    }else{
        if($solicitud == '1'){
            $prioridad = 'B';
        }elseif($solicitud == '2'){
            $prioridad = 'M';
        }elseif($solicitud == '3'){
            $prioridad = 'M';
        }else{
               return "msj['El campo Tipo de Solicitud tiene un valor inválido.']";
        }
    }
    $descripcion = $_POST['descripcion'];
    $usu_creo = $_SESSION['us_sds'];
    $creo = date('Y-m-d H:i:s', strtotime('-5 hours'));
    $estado = 2;

    // Insertar en soporte con formulario=3
    $sql = "INSERT INTO soporte (idsoporte, formulario,prioridad,observaciones,usu_creo,fecha_create,estado) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
    $params = [
        ['type' => 'i', 'value' => 7],           // formulario (7 para aplicativo)
        ['type' => 's', 'value' => $prioridad],         // prioridad
        ['type' => 's', 'value' => $descripcion],         // ok
        ['type' => 'i', 'value' => $usu_creo],   // usu_creo
        ['type' => 's', 'value' => $creo],       // fecha_create
        ['type' => 'i', 'value' => $estado]      // estado
    ];
    $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Si necesitas opciones para selects, puedes agregar funciones opc_solicitudes(), etc.
function opc_solicitud($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=315 and estado='A' ORDER BY 1",$id);
}

function formato_dato($a,$b,$c,$d){
    return $c[$d];
}
function bgcolor($a,$c,$f='c'){
    return "";
}