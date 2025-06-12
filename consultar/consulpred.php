<?php
ini_set('display_errors','1');
require_once "../libs/gestion.php";
$perf=perfil($_POST['tb']);
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


function focus_predios(){
	return 'predios';
   }
   
   
   function men_predios(){
	$rta=cap_menus('predios','pro');
	return $rta;
   }
   
   function cap_menus($a,$b='cap',$con='con') {
	 $rta = ""; 
	 $acc=rol($a);
	   if ($a=='predios'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	//  $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
	   }
  return $rta;
}

function lis_predios(){
    $filtro = $_REQUEST['filtro'] ?? '';
    $codpre = $_REQUEST['codpre'] ?? '';
    $docume = $_REQUEST['documento'] ?? '';

    switch ($filtro) {
        case '1':
            // Código para el caso 1, si es necesario agregarlo aquí
            break;

        case '2':
            if (trim($codpre) !== '') {
                $codpre = intval($codpre);
                $sql = "SELECT 
                            FN_CATALOGODESC(72, hg.subred) AS Subred,
                            direccion,
                            gg.fecha_create AS Creado,
                            u.nombre AS Creo,
                            u.perfil AS Perfil,
                            u.equipo AS Equipo,
                            FN_CATALOGODESC(44, gg.estado_v) AS Estado
                        FROM hog_geo hg
                        LEFT JOIN geo_gest gg ON hg.idgeo = gg.idgeo
                        LEFT JOIN usuarios u ON gg.usu_creo = u.id_usuario
                        WHERE hg.idgeo = $codpre
                        ORDER BY gg.estado_v, gg.fecha_create";
                $datos = datos_mysql($sql);

                if (empty($datos["responseResult"])) {
                    return getErrorMessage("No se encontraron registros para el código ingresado. Verifique el código e intente nuevamente.", '#ff0909a6');
                } else {
                    return panel_content($datos["responseResult"], "predios-lis", 10);
                }
            } else {
                return getErrorMessage("Por favor, ingrese un código de predio válido para realizar la búsqueda.", '#ff9700');
            }
            break;

        case '3':
            if (trim($docume) !== '') {
                $docume = intval($docume);
                $sql = "SELECT 
                            hg.idgeo AS 'Cod Predio',
                            hf.id_fam AS 'Cod Familia',
                            p.idpeople 'Cod Persona',
                            FN_CATALOGODESC(72, hg.subred) AS Subred,
                            hg.direccion AS Direccion,
                            u.nombre AS Creo,
                            u.perfil,
                            u.equipo,
                            p.fecha_create AS 'Fecha Creo',
                             FN_CATALOGODESC(44, gg.estado_v) AS Estado
                        FROM hog_fam hf
                        LEFT JOIN hog_geo hg ON hf.idpre = hg.idgeo
                        LEFT JOIN person p ON hf.id_fam = p.vivipersona
			 LEFT JOIN geo_gest gg ON hg.idgeo = gg.idgeo
                        LEFT JOIN usuarios u ON p.usu_creo = u.id_usuario
                        WHERE p.idpersona = $docume";
                $datos = datos_mysql($sql);

                if (empty($datos["responseResult"])) {
                    return getErrorMessage("No se encontró ningún predio asociado al documento ingresado. Verifique el documento e intente nuevamente.", '#ff0909a6');
                } else {
                    return panel_content($datos["responseResult"], "predios-lis", 10);
                }
            } else {
                return getErrorMessage("El campo de documento no puede estar vacío. Ingrese un número de documento válido para la búsqueda.", '#ff9700');
            }
            break;

        default:
            return getErrorMessage("Por favor, seleccione un tipo de filtro válido para proceder.", '#00a3ffa6');
            break;
    }
}

function getErrorMessage($message, $color) {
    return "<div class='error' style='padding: 12px; background-color:$color;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong> $message
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
}



function cmp_predios(){
	$rta="<div class='encabezado predios'>TABLA ESTADOS DEL PREDIO</div>
	<div class='contenido' id='predios-lis'>".lis_predios()."</div></div>";
	$hoy=date('Y-m-d');
	$w='predios';
	$d='';
	$o='pred';
	$c[]=new cmp($o,'e',null,'CODIGOS DE PREDIO',$w);
	$c[]=new cmp('filtro','s',3,$d,$w.' '.$o,'Buscar Por','filtro',null,null,true,true,'','col-0',"enClSe('filtro','flT',[['IDc'],['cOP'],['DoC']]);");
	/* $c[]=new cmp('sector','n',6,$d,$w.' flT IDc '.$o,'sector','sector',null,'123456',true,false,'','col-2');
	$c[]=new cmp('manzana','n',3,$d,$w.' flT IDc '.$o,'manzana','manzana',null,'123',true,false,'','col-1');
	$c[]=new cmp('predio','n',3,$d,$w.' flT IDc '.$o,'predio','predio',null,'123',true,false,'','col-1'); 
	$c[]=new cmp('unidad','n',3,$d,$w.' flT IDc '.$o,'unidad','unidad',null,'123',true,false,'','col-1'); */
	$c[]=new cmp('codpre','n',15,$d,$w.' flT cOP '.$o,'Codigo del Predio','codpre',null,'#####',true,false,'','col-2');
	$c[]=new cmp('documento','n',21,$d,$w.' flT DoC '.$o,'Documento del Usuario','documento',null,'##########',true,false,'','col-2');
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	$rta.="<center><button style='background-color:#4d4eef;border-radius:12px;color:white;padding:12px;text-align:center;cursor:pointer;' type='button' Onclick=\"act_lista('predios','','../consultar/consulpred.php');\">Buscar</button></center>";
	return $rta;
}

function opc_filtro($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=221 and estado='A' ORDER BY 1",$id);
}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
		if ($a=='ambient-lis' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getData,500,'ambient',event,this,['fecha','tipo_activi'],'../vivienda/amb.php');\"></li>";  //   act_lista(f,this);
			}
		return $rta;
	}

	function bgcolor($a,$c,$f='c'){
		$rta="";
		return $rta;
	}
	   
