
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


function focus_segComp(){
	return 'segComp';
   }
   
   
   function men_segComp(){
	$rta=cap_menus('segComp','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='segComp'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	   }
  return $rta;
}

function lis_seguim(){
  // print_r($_POST);
  $id = divide($_POST['id']);
  $info=datos_mysql("SELECT COUNT(*) total FROM hog_segcom WHERE id_con='".$id[1]."'");
  $total=$info['responseResult'][0]['total'];
  $regxPag=5;
  $pag=(isset($_POST['pag-seguiCom']))? ($_POST['pag-seguiCom']-1)* $regxPag:0;
  
      $sql="SELECT hs.fecha_seg,FN_CATALOGODESC(234,tipo_seg) tipo,FN_CATALOGODESC(170,estado_seg) cumplio,u.nombre,hs.equipo FROM 
            hog_segcom hs
        LEFT JOIN usuarios u ON hs.usu_create=u.id_usuario
        WHERE id_con='".$id[1];
          $sql.="' ORDER BY fecha_create";
          $sql.=' LIMIT '.$pag.','.$regxPag;
          //  echo $sql;
          $datos=datos_mysql($sql);
          return create_table($total,$datos["responseResult"],"seguiCom",$regxPag,'plnsegcon.php');
}

function cmp_segComp(){
  $rta ="<div class='encabezado seguiCom'>TABLA DE COMPROMISOS NO CUMPLIDOS</div>
	<div class='contenido' id='seguiCom-lis' >".lis_seguim()."</div></div>";
    $w="placuifam";
      $o='accide';
      $e="";
      $key='pln';
      $o='segComp';
    //   var_dump($_POST);
      $t=['compromiso'=>''];
	$d=get_compromiso();
	if ($d==""){$d=$t;}
      $c[]=new cmp($o,'e',null,'PLAN DE CUIDADO FAMILIAR CONCERTADO',$w);
        $c[]=new cmp('idcom','h',15,$_POST['id'],$w.' '.$key.' '.$o,'id','id',null,'####',false,false);
        $c[]=new cmp('compromiso','a',50,$d['compromiso'],$w.''.$o,'Compromisos concertados','observaciones',null,null,true,false,'','col-0');
        $c[]=new cmp('fecha','d','3',$e,$w.' '.$o,'Fecha de Seguimiento','fecha',null,null,true,true,'','col-1');
        $c[]=new cmp('tipo','s','2',$e,$w.' '.$o,'Tipo de Seguimiento','tipo',null,null,true,true,'','col-1');
        $c[]=new cmp('cumplio','s','2',$e,$w.' '.$o,'cumplio','cumplio',null,null,true,true,'','col-1',"enbValue('cumplio','rt',2);");
        $c[]=new cmp('observacion','a',50,'',$w.' rt '.$o,'Observación del Incumplimiento','observaciones',null,null,false,false,'','col-7');

      for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
      return $rta;
  }

  function get_compromiso(){
    // var_dump($_REQUEST);
    if($_REQUEST['id']==''){
        return "";
    }else{
        $id=divide($_REQUEST['id']);
        //  `fechaatencion`, `codigocups`, `finalidadconsulta`, `peso`, `talla`, `sistolica`, `diastolica`, `abdominal`, `brazo`, `diagnosticoprincipal`, `diagnosticorelacion1`, `diagnosticorelacion2`, `diagnosticorelacion3`, `fertil`, `preconcepcional`, `metodo`, `anticonceptivo`, `planificacion`, `mestruacion`, `gestante`, `gestaciones`, `partos`, `abortos`, `cesarias`, `vivos`, `muertos`, `vacunaciongestante`, `edadgestacion`, `ultimagestacion`, `probableparto`, `prenatal`, `fechaparto`, `rpsicosocial`, `robstetrico`, `rtromboembo`, `rdepresion`, `sifilisgestacional`, `sifiliscongenita`, `morbilidad`, `hepatitisb`, `vih`, `cronico`, `asistenciacronica`, `tratamiento`, `vacunascronico`, `menos5anios`, `esquemavacuna`, `signoalarma`, `cualalarma`, `dxnutricional`, `eventointeres`, `evento`, `cualevento`, `sirc`, `rutasirc`, `remision`, `cualremision`, `ordenpsicologia`, `ordenvacunacion`, `vacunacion`, `ordenlaboratorio`, `laboratorios`, `ordenimagenes`, `imagenes`, `ordenmedicamentos`, `medicamentos`, `rutacontinuidad`, `continuidad`, `relevo`  ON a.idpersona = b.idpersona AND a.tipodoc = b.tipo_doc
        $sql="SELECT  compromiso
        FROM hog_planconc
        WHERE idcon ='{$id[1]}'";
        // echo $sql;
        $info=datos_mysql($sql);
        return $info['responseResult'][0];			
    }
}

  function gra_segComp(){
	$id=divide($_POST['idcom']);
    // var_dump($id);
    $info=datos_mysql("select equipo from usuarios where id_usuario='{$_SESSION['us_sds']}'");
    if(isset($info['responseResult'][0])){ 
      $equipo=$info['responseResult'][0]['equipo'];
      $sql = "INSERT INTO hog_segcom VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
      $params = [
        ['type' => 'i', 'value' => NULL ],
        ['type' => 's', 'value' => $id[1]],
        ['type' => 's', 'value' => $_POST['fecha']],
        ['type' => 's', 'value' => $_POST['tipo']],
        ['type' => 's', 'value' => $_POST['cumplio']],
        ['type' => 's', 'value' => $_POST['observacion']],
        ['type' => 's', 'value' => $equipo],
        ['type' => 'i', 'value' => $_SESSION['us_sds']],
        ['type' => 's', 'value' => date("Y-m-d H:i:s")],
        ['type' => 's', 'value' => ''],
        ['type' => 's', 'value' => ''],
        ['type' => 's', 'value' => 'A']
      ];
      $rta = mysql_prepd($sql, $params);
      return $rta;
    }else{
      $rta="Error: msj['No existe un equipo actualmente para el usuario que realizo el seguimiento']";
    }
}


function opc_cumplio($id=''){
return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
}
function opc_tipo($id=''){
    return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=234 and estado='A' ORDER BY 1",$id);
    }

function formato_dato($a,$b,$c,$d){
    $b=strtolower($b);
    $rta=$c[$d];
    // var_dump($a);
    if ($a=='segComp' && $b=='acciones'){
        $rta="<nav class='menu right'>";
            $rta.="<li title='Ver Apgar'><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'compConc',event,this,'plncon.php',['obs','equipo']);\"></i></li>";  //   act_lista(f,this);
    $rta.="<li class='icono editar' title='Seguimiento a Compromisos' id='".$c['ACCIONES']."' Onclick=\"mostrar('compConc','pro',event,'','plnsegcon.php',7);\"></li>";
        }
    return $rta;
}

function bgcolor($a,$c,$f='c'){
    $rta="";
    return $rta;
   }
   
