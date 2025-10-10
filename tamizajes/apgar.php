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


function lis_tamApgar(){ //CAMBIO EN LIS TABLA PERSON RELACIONES  (TODOS LOS LEFT JOIN), cambiar el id de acciones en el sql
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_apgar O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamApgar());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-tamApgar']))? (intval($_POST['pag-tamApgar'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,id_apgar 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje` Puntaje,`descripcion` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_apgar O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamApgar();
	$sql.=" ORDER BY O.fecha_create DESC";
	//echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"tamApgar",$regxPag);
	}else{
		return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
	}
}

function whe_tamApgar() { //CAMBIO FILTROS DEJAR ESTOS
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}

function lis_apgar(){
	// var_dump($_POST['id']);
	$id=divide($_POST['id']);
	$sql="SELECT id_apgar ACCIONES,id_apgar 'Cod Registro',fecha_toma,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_tam_apgar A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"apgar-lis",5);
}

function cmp_tamApgar(){
	$rta="<div class='encabezado apgar'>TABLA APGAR</div>
	<div class='contenido' id='apgar-lis'>".lis_apgar()."</div></div>";
	$a=['id_apgar'=>'','ayuda_fam'=>'','fam_comprobl'=>'','fam_percosnue'=>'','fam_feltrienf'=>'','fam_comptiemjun'=>'','sati_famayu'=>'','sati_famcompro'=>'','sati_famapoemp'=>'','sati_famemosion'=>'','sati_famcompar'=>'','puntaje'=>'','descripcion'=>'']; //CAMBIO con relacion a los campos de la bd
	$p=['id_apgar'=>'','idpersona'=>'','tipo_doc'=>'','apgar_nombre'=>'','apgar_fechanacimiento'=>'','apgar_edad'=>'','sati_famayu'=>'','sati_famcompro'=>'','sati_famapoemp'=>'','sati_famemosion'=>'','sati_famcompar'=>'','puntaje'=>'','descripcion'=>'']; //CAMBIO ADD LINEA
	$w='tamapgar';
	$d=get_tapgar();
	// var_dump($d);
	if (!isset($d['id_apgar'])) {
		$d = array_merge($d,$a);
	}
	$u = ($d['id_apgar']!='') ? false : true ;
	$o='datos';
    $key='apg';
	// var_dump($d);
	// var_dump($_POST);
	$days=fechas_app('vivienda');//CAMBIO DE ADD ESTA LINEA
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('idpersona','n','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','idpersona',null,'',false,false,'','col-2');//CAMBIO CAMBIAR  T POR N
	$c[]=new cmp('tipodoc','s','3',$d['tipo_doc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipodoc',null,'',false,false,'','col-25');
	$c[]=new cmp('nombre','t','50',$d['apgar_nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('fechanacimiento','d','10',$d['apgar_fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['apgar_edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-3');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);"); //CAMBIO SE ADD ESTA LINEA

	$ed=false;
	if($d['apgar_edad']>6 && $d['apgar_edad']<18){
		$o=' cuestionario1';
		$ed=true;
		$c[]=new cmp($o,'e',null,'APGAR FAMILIAR 7 A 17 AÑOS',$w);
		$c[]=new cmp('ayuda_fam','s','3','',$w.' '.$o,'Cuando algo le preocupa, puede pedir ayuda a su familia','respmenor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('fam_comprobl','s','3','',$w.' '.$o,'Le gusta la manera como su familia habla y comparte los problemas','respmenor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('fam_percosnue','s','3','',$w.' '.$o,'Le gusta como su familia le permite hacer las cosas nuevas que quiere hacer','respmenor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('fam_feltrienf','s','3','',$w.' '.$o,'Le gusta lo que su familia hace cuando está feliz, triste, enfadado','respmenor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('fam_comptiemjun','s','3','',$w.' '.$o,'Le gusta como su familia y él comparten tiempo juntos','respmenor',null,null,$ed,true,'','col-10');
	}
	

	if($d['apgar_edad']>17){
		$o=' cuestionario20';
		$ed=true;
		$c[]=new cmp($o,'e',null,'APGAR FAMILIAR 18 AÑOS EN ADELANTE',$w);
		$c[]=new cmp('sati_famayu','s','3','',$w.' '.$o,'Me siento satisfecho con la ayuda que recibo de mi familia cuando tengo algún problema o necesidad','respmayor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('sati_famcompro','s','3','',$w.' '.$o,'Me siento satisfecho con la forma en que mi familia habla de las cosas y comparte los problemas conmigo','respmayor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('sati_famapoemp','s','3','',$w.' '.$o,'Me siento satisfecho con la forma como mi familia acepta y apoya mis deseos de emprender nuevas actividades','respmayor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('sati_famemosion','s','3','',$w.' '.$o,'Me siento satisfecho con la forma como mi familia expresa afecto y responde a mis emociones como rabia, tristeza o amor','respmayor',null,null,$ed,true,'','col-10');
		$c[]=new cmp('sati_famcompar','s','3','',$w.' '.$o,'Me siento satisfecho con la manera como compartimos en mi familia el tiempo para estar juntos, los espacios en la casa o el dinero ','respmayor',null,null,$ed,true,'','col-10');
	}
	

	$o='totalresul';
		$c[]=new cmp($o,'e',null,'TOTAL',$w);
		$c[]=new cmp('puntaje','t','2','',$w.' '.$o,'Puntaje','puntaje',null,null,false,false,'','col-5');
		$c[]=new cmp('descripcion','t','3','',$w.' '.$o,'Descripcion','descripcion',null,null,false,false,'','col-5');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
   }

   function get_tamApgar() {//CAMBIO FUNCION NUEVA
    if (empty($_REQUEST['id'])) {
        return "";
    }
    $id = divide($_REQUEST['id']);
    $sql = "SELECT A.id_apgar, P.idpersona, P.tipo_doc,
            concat_ws(' ', P.nombre1, P.nombre2, P.apellido1, P.apellido2) apgar_nombre,
            P.fecha_nacimiento apgar_fechanacimiento, 
            YEAR(CURDATE()) - YEAR(P.fecha_nacimiento) AS apgar_edad,
            A.fecha_toma, A.ayuda_fam, A.fam_comprobl, A.fam_percosnue, 
            A.fam_feltrienf, A.fam_comptiemjun, A.sati_famayu, A.sati_famcompro, 
            A.sati_famapoemp, A.sati_famemosion, A.sati_famcompar,
			A.puntaje, A.descripcion
            FROM hog_tam_apgar A
            LEFT JOIN person P ON A.idpeople = P.idpeople
            WHERE A.id_apgar='{$id[0]}'";
    $info = datos_mysql($sql);
    $data = $info['responseResult'][0];
    // Campos a mostrar según la edad
    $baseData = [
        'id_apgar' => $data['id_apgar'],
        'idpersona' => $data['idpersona'],
        'tipo_doc' => $data['tipo_doc'],
        'apgar_nombre' => $data['apgar_nombre'],
        'apgar_fechanacimiento' => $data['apgar_fechanacimiento'],
        'apgar_edad' => $data['apgar_edad'],
        'fecha_toma' => $data['fecha_toma']
    ];
    $edadCampos = ($data['apgar_edad'] < 18) 
        ? ['ayuda_fam', 'fam_comprobl', 'fam_percosnue', 'fam_feltrienf', 'fam_comptiemjun','puntaje','descripcion'] //MENORES DE 18
        : ['sati_famayu', 'sati_famcompro', 'sati_famapoemp', 'sati_famemosion', 'sati_famcompar','puntaje','descripcion'];//MAYORES DE 18

    foreach ($edadCampos as $campo) {
        $baseData[$campo] = $data[$campo];
    }
    return json_encode($baseData);
}



function get_tapgar(){//CAMBIO function nueva
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT `id_apgar`,O.idpeople,`ayuda_fam`,`fam_comprobl`,`fam_percosnue`,`fam_feltrienf`,`fam_comptiemjun`,`sati_famayu`,`sati_famcompro`,`sati_famapoemp`,`sati_famemosion`,`sati_famcompar`,`puntaje`,`descripcion`,
        O.estado,P.idpersona,P.tipo_doc,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) apgar_nombre,P.fecha_nacimiento apgar_fechanacimiento,YEAR(CURDATE())-YEAR(P.fecha_nacimiento) apgar_edad
		FROM `hog_tam_apgar` O
		LEFT JOIN person P ON O.idpeople = P.idpeople
			WHERE P.idpeople ='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
			if (!$info['responseResult']) {
				$sql="SELECT P.idpersona,P.tipo_doc,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) apgar_nombre,
				P.fecha_nacimiento apgar_fechanacimiento,
				YEAR(CURDATE())-YEAR(P.fecha_nacimiento) apgar_edad
				FROM person P 
				WHERE P.idpeople ='{$id[0]}'";
				echo $sql;
				$info=datos_mysql($sql);
			return $info['responseResult'][0];
			}
		return $info['responseResult'][0];
	}
}


function get_person(){//CAMBIO TABLA PERSON DEL FROM LINEA 136
	//  print_r($_POST);
	$id=divide($_POST['id']);
$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,fecha_nacimiento,YEAR(CURDATE())-YEAR(fecha_nacimiento) Edad
from person
	WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."')";
	$info=datos_mysql($sql);
	if (!$info['responseResult']) {
		return json_encode (new stdClass);
	}
return json_encode($info['responseResult'][0]);
}

function focus_tamApgar(){
	return 'tamApgar';
   }

function men_tamApgar(){
	$rta=cap_menus('tamApgar','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	if ($a=='tamApgar'  && isset($acc['crear']) && $acc['crear']=='SI'){  
	 $rta .= "<li class='icono $a grabar'      title='Grabar'          OnClick=\"grabar('$a',this);\"></li>";
    }
	return $rta;
  }

function gra_tamApgar(){
	$id=divide($_POST['id']);
	// print_r($_POST);
	if(count($id)!==2){
		return "No es posible actualizar el tamizaje";
	}else{
		// echo $sql;
		$idp=datos_mysql("SELECT idpeople FROM person 
		WHERE idpersona = {$_POST['idpersona']} AND tipo_doc ='{$_POST['tipodoc']}'");//CAMBIO ADD linea
		if (isset($idp['responseResult'][0])){//CAMBIO ADD linea
			$idper = $idp['responseResult'][0]['idpeople'];//CAMBIO ADD linea
		}//CAMBIO ADD linea

if (isset($_POST['fam_comprobl']) && $_POST['fam_comprobl'] !== '' || isset($_POST['sati_famcompro']) && $_POST['sati_famcompro'] !== '') {
    $pre1 = isset($_POST['ayuda_fam']) ? $_POST['ayuda_fam'] : 0;
    $pre2 = isset($_POST['fam_comprobl']) ? $_POST['fam_comprobl'] : 0;
    $pre3 = isset($_POST['fam_percosnue']) ? $_POST['fam_percosnue'] : 0;
    $pre4 = isset($_POST['fam_feltrienf']) ? $_POST['fam_feltrienf'] : 0;
    $pre5 = isset($_POST['fam_comptiemjun']) ? $_POST['fam_comptiemjun'] : 0;
    $pre6 = isset($_POST['sati_famayu']) ? $_POST['sati_famayu'] : 0;
    $pre7 = isset($_POST['sati_famcompro']) ? $_POST['sati_famcompro'] : 0;
    $pre8 = isset($_POST['sati_famapoemp']) ? $_POST['sati_famapoemp'] : 0;
    $pre9 = isset($_POST['sati_famemosion']) ? $_POST['sati_famemosion'] : 0;
    $pre10 = isset($_POST['sati_famcompar']) ? $_POST['sati_famcompar'] : 0;


			$suma_apgar = ($pre1+$pre2+$pre3+$pre4+$pre5+$pre6+$pre7+$pre8+$pre9+$pre10);


			$ed=$_POST['edad'];
			if($ed>17){
				switch ($suma_apgar) {
					case ($suma_apgar >= 0 && $suma_apgar <=9 ):
						$des='DISFUNCIÓN FAMILIAR SEVERA';
						break;
					case ($suma_apgar >= 10 && $suma_apgar <= 12):
					$des='DISFUNCIÓN FAMILIAR MODERADA';
					break;
				case ($suma_apgar >= 13 && $suma_apgar <= 16):
					$des='DISFUNCIÓN FAMILIAR LEVE';
					break;
				case ($suma_apgar >= 17 && $suma_apgar <= 20):
						$des='FUNCIÓN FAMILIAR NORMAL';
					break;
				default:
					$des='Error en el rango, por favor valide';
					break;
			}
			}else{
				switch ($suma_apgar) {
					case ($suma_apgar >= 0 && $suma_apgar <=3 ):
						$des='DISFUNCIÓN FAMILIAR SEVERA';
						break;
					case ($suma_apgar >= 4 && $suma_apgar <= 6):
						$des='DISFUNCIÓN FAMILIAR MODERADA';
						break;
					case ($suma_apgar >= 7 && $suma_apgar <= 10):
						$des='FUNCIÓN FAMILIAR NORMAL';
						break;
					default:
						$des='Error en el rango, por favor valide';
						break;
				}
				// echo "ES MENOR DE EDAD ".$ed.' '.print_r($_POST);
			}

			$tas=$_POST['ayuda_fam'] ?? null;
			$cop=$_POST['fam_comprobl']??null;
			$per=$_POST['fam_percosnue']??null;
			$fel=$_POST['fam_feltrienf']??null;
			$cti=$_POST['fam_comptiemjun']??null;
			$ayu=$_POST['sati_famayu']??null;
			$com=$_POST['sati_famcompro']??null;
			$apo=$_POST['sati_famapoemp']??null;
			$emo=$_POST['sati_famemosion']??null;
			$cmr=$_POST['sati_famcompar']??null;

			$sql = "INSERT INTO hog_tam_apgar VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR), ?, ?, ?)";
			$params = [
			    ['type' => 'i', 'value' => NULL],  // ID autoincremental
			    ['type' => 'i', 'value' => $idper],
			    ['type' => 's', 'value' => $_POST['fecha_toma']],
			    ['type' => 's', 'value' => $tas],
			    ['type' => 's', 'value' => $cop],
			    ['type' => 's', 'value' => $per],
			    ['type' => 's', 'value' => $fel],
			    ['type' => 's', 'value' => $cti],
			    ['type' => 's', 'value' => $ayu],
			    ['type' => 's', 'value' => $com],
			    ['type' => 's', 'value' => $apo],
			    ['type' => 's', 'value' => $emo],
			    ['type' => 's', 'value' => $cmr],
			    ['type' => 'i', 'value' => $suma_apgar],
			    ['type' => 's', 'value' => $des],
			    ['type' => 's', 'value' => $_SESSION['us_sds']],
			    ['type' => 's', 'value' => NULL],
			    ['type' => 's', 'value' => NULL],
				['type' => 's', 'value' => 'A']
			];
		return $rta = mysql_prepd($sql, $params);
		}else{
			// print_r($_POST);
			return 'TAMIZAJE NO APLICA PARA LA EDAD';
		}
	}
  return $sql;
}


	function opc_tipodoc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}

	function opc_respmenor($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=37 and estado='A' ORDER BY 1",$id);
	}
	function opc_respmayor($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=173 and estado='A' ORDER BY 1",$id);
	}

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamApgar' && $b=='acciones'){
			$rta="<nav class='menu right'>";
				$rta.="<li title='Ver Apgar'  Onclick=\"mostrar('tamApgar','pro',event,'','../tamizajes/apgar.php',7,'tamApgar');setTimeout(hiddxedad,300,'edad','cuestionario1','cuestionario2');\"><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."'></i></li>";  //act_lista(f,this);
			}
			if ($a=='apgar-lis' && $b=='acciones'){
				$rta="<nav class='menu right'>";		
				$rta.="<li title='Ver Apgar'><i class='fa-solid fa-eye ico' id='".$c['ACCIONES']."' Onclick=\"setTimeout(getDataFetch,500,'tamApgar',event,this,'../tamizajes/apgar.php',['puntaje','descripcion']);\"></i></li>";  //   act_lista(f,this);
			}
		return $rta;
	   }

	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
