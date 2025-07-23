<?php
// filepath: vscode-vfs://github/k4rl0sA/TEST2025/crea-fam/traslados.php
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
function cmp_traslados(){
    $rta = "";
    $w = 'traslados';
    $o = 'inftras';
    $t = ['idpeople'=>'','cod_familia'=>''];
    $c[] = new cmp($o,'e',null,'TRASLADO DE FAMILIA',$w);
    $c[]=new cmp('idp','h',15,$_POST['id'],$w.' '.$o,'id','id',null,'####',false,false);
    $c[] = new cmp('cod_familia','nu','999999999',$t['cod_familia'],$w.' '.$o,'Código Familia a donde desea trasladar dentro de la misma subred','cod_familia',null,null,true,true,'','col-4');
    for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
    return $rta;
}

// Enfocar el formulario
function focus_traslados(){
    return 'traslados';
}

// Menú de acciones
function men_traslados(){
    $rta = cap_menus('traslados','pro');
    return $rta;
}

function cap_menus($a,$b='cap',$con='con') {
    $rta = "";
    $acc = rol($a);
    if ($a=='traslados' && isset($acc['crear']) && $acc['crear']=='SI') {  
        $rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>";
    }
    return $rta;
}

// Guardar traslado
function gra_traslados() {
    $id = divide($_POST['idp']);
    $familia = intval($_POST['cod_familia']);
    $usu_creo = $_SESSION['us_sds'];
    $creo = date('Y-m-d H:i:s', strtotime('-5 hours'));
    $estado = 2;

    // Insertar en soporte
    $sql = "INSERT INTO soporte (idsoporte, idpeople, cod_familia, formulario, prioridad, usu_creo, fecha_create, estado) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        ['type' => 'i', 'value' => $id[0]],      // idpeople
        ['type' => 'i', 'value' => $familia],   // cod_familia
        ['type' => 'i', 'value' => 2],              // formulario
        ['type' => 's', 'value' => 'A'],            // prioridad
        ['type' => 's', 'value' => $usu_creo],      // usu_creo
        ['type' => 's', 'value' => $creo],  // fecha_create
        ['type' => 'i', 'value' => $estado]         // estado
    ];
   $rta = mysql_prepd($sql, $params);
    return $rta;
}

// Si necesitas opciones para selects, puedes agregar funciones opc_traslados(), etc.
// Puedes agregar funciones como formato_dato y bgcolor si tu sistema las requiere.
function formato_dato($a,$b,$c,$d){
    return $c[$d];
}
function bgcolor($a,$c,$f='c'){
    return "";
}