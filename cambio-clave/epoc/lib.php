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


function lis_tamepoc(){
	if (!empty($_POST['fidentificacion']) || !empty($_POST['ffam'])) {
		$info=datos_mysql("SELECT COUNT(*) total from hog_tam_epoc O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		where ".whe_tamepoc());
		$total=$info['responseResult'][0]['total'];
		$regxPag=12;
		$pag=(isset($_POST['pag-taepoc']))? (intval($_POST['pag-taepoc'])-1)* $regxPag:0;

		$sql="SELECT O.idpeople ACCIONES,id_epoc 'Cod Registro',V.id_fam 'Cod Familia',P.idpersona Documento,FN_CATALOGODESC(1,P.tipo_doc) 'Tipo de Documento',CONCAT_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) Nombres,`puntaje` Puntaje,`descripcion` Descripcion, U.nombre Creo,U.subred,U.perfil perfil
	FROM hog_tam_epoc O
		LEFT JOIN person P ON O.idpeople = P.idpeople
		LEFT JOIN hog_fam V ON P.vivipersona = V.id_fam
		LEFT JOIN hog_geo G ON V.idpre = G.idgeo
		LEFT JOIN usuarios U ON O.usu_creo=U.id_usuario
		WHERE ";
	$sql.=whe_tamepoc();
	$sql.=" ORDER BY O.fecha_create DESC";
	//echo $sql;
	$datos=datos_mysql($sql);
	return create_table($total,$datos["responseResult"],"taepoc",$regxPag);
	}else{
		return "<div class='error' style='padding: 12px; background-color:#00a3ffa6;color: white; border-radius: 25px; z-index:100; top:0;text-transform:none'>
                <strong style='text-transform:uppercase'>NOTA:</strong>Por favor Ingrese el numero de documento ó familia a Consultar
                <span style='margin-left: 15px; color: white; font-weight: bold; float: right; font-size: 22px; line-height: 20px; cursor: pointer; transition: 0.3s;' onclick=\"this.parentElement.style.display='none';\">&times;</span>
            </div>";
	}
	
}

function whe_tamepoc() {
	$sql = '1';
    if (!empty($_POST['fidentificacion'])) {
        $sql .= " AND P.idpersona = '".$_POST['fidentificacion']."'";
    }
    if (!empty($_POST['ffam'])) {
        $sql .= " AND V.id_fam = '".$_POST['ffam']."'";
    }
    return $sql;
}

function lis_epoc(){
	// var_dump($_POST['id']);
	$id=divide($_POST['id']);
	$sql="SELECT id_epoc ACCIONES,
	id_epoc 'Cod Registro',fecha_toma,descripcion,`nombre` Creó,`fecha_create` 'fecha Creó'
	FROM hog_tam_epoc A
	LEFT JOIN  usuarios U ON A.usu_creo=U.id_usuario ";
	$sql.="WHERE idpeople='".$id[0];
	$sql.="' ORDER BY fecha_create";
	// echo $sql;
	$datos=datos_mysql($sql);
	return panel_content($datos["responseResult"],"epoc-lis",5);
}

function cmp_tamepoc(){
	$rta="<div class='encabezado epoc'>TABLA EPOC</div><div class='contenido' id='taepoc-lis'>".lis_epoc()."</div></div>";
	$a=['id_epoc'=>'','tose_muvedias'=>'','tiene_flema'=>'','aire_facil'=>'','mayor'=>'','fuma'=>'','puntaje'=>'','descripcion'=>''];
	$p=['id_epoc'=>'','idpersona'=>'','tipo_doc'=>'','nombre'=>'','fechanacimiento'=>'','edad'=>'','puntaje'=>'','descripcion'=>'']; //CAMBIO ADD LINEA
	$w='tamepoc';
	$d=get_tepoc();
	// var_dump($d);
	if (!isset($d['id_epoc'])) {
		$d = array_merge($d,$a);
	}
	//CAMBIO HASTA AQUI
	$o='datos';
    $key='epo';
	$days=fechas_app('vivienda');
	$c[]=new cmp($o,'e',null,'DATOS DE IDENTIFICACIÓN',$w);
	$c[]=new cmp('id','h',15,$_POST['id'],$w.' '.$o,'','',null,'####',false,false);
	$c[]=new cmp('documento','t','20',$d['idpersona'],$w.' '.$o.' '.$key,'N° Identificación','documento',null,'',false,false,'','col-2');
	$c[]=new cmp('tipo_doc','s','3',$d['tipo_doc'],$w.' '.$o.' '.$key,'Tipo Identificación','tipo_doc',null,'',false,false,'','col-25');//,'getDatForm(\'epo\',\'person\',[\'datos\']);setTimeout(hiddxedad,500,\'edad\',\'cuestionario1\',\'cuestionario2\');
	$c[]=new cmp('nombre','t','50',$d['nombre'],$w.' '.$o,'nombres','nombre',null,'',false,false,'','col-4');
	$c[]=new cmp('fechanacimiento','d','10',$d['fechanacimiento'],$w.' '.$o,'fecha nacimiento','fechanacimiento',null,'',false,false,'','col-15');
    $c[]=new cmp('edad','n','3',$d['edad'],$w.' '.$o,'edad','edad',null,'',true,false,'','col-3');
	$c[]=new cmp('fecha_toma','d','10','',$w.' '.$o,'fecha de la Toma','fecha_toma',null,'',true,true,'','col-2',"validDate(this,$days,0);");
    //$c[]=new cmp('act','o','3','',$w.' '.$o,'Desea continuar','act',null,'',true,$u,'','col-3');//,'hiddxedad(\'edad\',\'cuestionario1\',\'cuestionario2\');'
	$o=' cuestionario1';
	$c[]=new cmp($o,'e',null,'TAMIZAJE DE EPOC',$w);
	
	$c[]=new cmp('tose_muvedias','s','3','',$w.' '.$o,'¿Tose muchas veces la mayoria de los días?','respuesta',null,null,true,true,'','col-10');
	$c[]=new cmp('tiene_flema','s','3','',$w.' '.$o,'¿tiene flemas o mocos la mayoria de los días?','respuesta',null,null,true,true,'','col-10');
	$c[]=new cmp('aire_facil','s','3','',$w.' '.$o,'¿Se queda sin aire mas facilmente que otras personas de su edad?','respuesta',null,null,true,true,'','col-10');
	$c[]=new cmp('mayor','s','3','1',$w.' '.$o,'¿Es mayor de 40 años?	','respuesta',null,null,false,false,'','col-10');
	$c[]=new cmp('fuma','s','3','',$w.' '.$o,'¿Actualmente fuma o es un exfumador?','respuesta',null,null,true,true,'','col-10');

	$o='totalresul';
	$c[]=new cmp($o,'e',null,'TOTAL',$w);
	$c[]=new cmp('puntaje','t','2','',$w.' '.$o,'Puntaje','puntaje',null,null,false,false,'','col-5');
	$c[]=new cmp('descripcion','t','3','',$w.' '.$o,'Descripcion','descripcion',null,null,false,false,'','col-5');

	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	
	return $rta;
   }

   function get_tamepoc() {
    if (empty($_REQUEST['id'])) {
        return "";
    }

    $id = divide($_REQUEST['id']);
    $sql = "SELECT A.id_epoc, P.idpersona, P.tipo_doc,
            concat_ws(' ', P.nombre1, P.nombre2, P.apellido1, P.apellido2) AS nombre,
            P.fecha_nacimiento AS fechanacimiento,
            YEAR(CURDATE()) - YEAR(P.fecha_nacimiento) AS edad,
            A.fecha_toma, 
			A.tose_muvedias,A.tiene_flema,A.aire_facil,A.mayor,A.fuma,
            A.puntaje, A.descripcion
            FROM hog_tam_epoc A
            LEFT JOIN person P ON A.idpeople = P.idpeople
            WHERE A.id_epoc = '{$id[0]}'";

    $info = datos_mysql($sql);
    $data = $info['responseResult'][0];

    // Datos básicos
    $baseData = [
        'id_epoc' => $data['id_epoc'],
        'idpersona' => $data['idpersona'],
        'tipo_doc' => $data['tipo_doc'],
        'nombre' => $data['nombre'],
        'fechanacimiento' => $data['fechanacimiento'],
        'edad' => $data['edad'],
        'fecha_toma' => $data['fecha_toma'] ?? null, // Valor por defecto null si no está definido
    ];
    // Campos adicionales específicos del tamizaje Findrisc
    $edadCampos = [
		'tose_muvedias','tiene_flema','aire_facil','mayor','fuma','puntaje','descripcion'
    ];
    foreach ($edadCampos as $campo) {
        $baseData[$campo] = $data[$campo];
    }
    return json_encode($baseData);
}


   function get_tepoc(){
	if($_POST['id']==0){
		return "";
	}else{
		 $id=divide($_POST['id']);
		// print_r($_POST);
		$sql="SELECT id_epoc,O.idpeople,
		tose_muvedias,tiene_flema,aire_facil,mayor,fuma,puntaje,descripcion,
		O.estado,P.idpersona,P.tipo_doc,P.sexo,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,P.fecha_nacimiento fechanacimiento,YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
		FROM `hog_tam_epoc` O
		LEFT JOIN person P ON O.idpeople = P.idpeople
			WHERE P.idpeople ='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
			if (!$info['responseResult']) {
				$sql="SELECT P.idpersona,P.tipo_doc,P.sexo,concat_ws(' ',P.nombre1,P.nombre2,P.apellido1,P.apellido2) nombre,
				P.fecha_nacimiento fechanacimiento,
				YEAR(CURDATE())-YEAR(P.fecha_nacimiento) edad
				FROM person P
				WHERE P.idpeople ='{$id[0]}'";
				// echo $sql;
				$info=datos_mysql($sql);
			return $info['responseResult'][0];
			}
		return $info['responseResult'][0];
	}
	} 


	function get_person(){
		// print_r($_POST);
		$id=divide($_POST['id']);
		$sql="SELECT idpersona,tipo_doc,concat_ws(' ',nombre1,nombre2,apellido1,apellido2) nombres,sexo ,fecha_nacimiento,TIMESTAMPDIFF(YEAR,fecha_nacimiento, CURDATE()) edad
	from person
	WHERE idpersona='".$id[0]."' AND tipo_doc=upper('".$id[1]."');";
	
		// return json_encode($sql);
		$info=datos_mysql($sql);
		if (!$info['responseResult']) {
			return json_encode (new stdClass);
		}
	return json_encode($info['responseResult'][0]);
	}

function focus_tamepoc(){
	return 'tamepoc';
   }
   
function men_tamepoc(){
	$rta=cap_menus('tamepoc','pro');
	return $rta;
   }

   function cap_menus($a,$b='cap',$con='con') {
	$rta = ""; 
	$acc=rol($a);
	if ($a=='tamepoc') {  
		$rta .= "<li class='icono $a  grabar' title='Grabar' Onclick=\"grabar('$a',this);\" ></li>";
		
	}
	return $rta;
  }
   
function gra_tamepoc(){
	$id=divide($_POST['id']);
	//print_r($_POST);
	if(count($id)!==2){
		return "No es posible actualizar el tamizaje";
	}else{
	$suma_epoc = (
		    intval($_POST['tose_muvedias'])+
			intval($_POST['tiene_flema'])+
			intval($_POST['aire_facil'])+
			intval($_POST['mayor'])+
			intval($_POST['fuma'])
		);

		switch ($suma_epoc) {
				case $suma_epoc == 0:
					$des='RIESGO BAJO';
					break;
				case ($suma_epoc >0 && $suma_epoc < 3):
					$des='RIESGO BAJO';
					break;
				case ($suma_epoc > 2 ):
						$des='RIESGO ALTO';
					break;
				default:
					$des='Error en el rango, por favor valide';
					break;
			}
		
			$sql = "INSERT INTO hog_tam_epoc VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(),INTERVAL 5 HOUR),?,?,?)";

		$params = [
			['type' => 'i', 'value' => NULL],
			['type' => 'i', 'value' => $id[0]],
			['type' => 's', 'value' => $_POST['fecha_toma']],
			['type' => 's', 'value' => $_POST['tose_muvedias']],
			['type' => 's', 'value' => $_POST['tiene_flema']],
			['type' => 's', 'value' => $_POST['aire_facil']],
			['type' => 's', 'value' => $_POST['mayor']],
			['type' => 's', 'value' => $_POST['fuma']],
			['type' => 'i', 'value' => $suma_epoc],
			['type' => 's', 'value' => $des],
			['type' => 's', 'value' => $_SESSION['us_sds']],
			['type' => 's', 'value' => NULL],
			['type' => 's', 'value' => NULL],
			['type' => 's', 'value' => 'A']
		];
		// print_r($_POST);
		// return 'TAMIZAJE NO APLICA PARA LA EDAD';
	return $rta = mysql_prepd($sql, $params);
 
}
}

	function opc_tipo_doc($id=''){
		return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo=1 and estado='A' ORDER BY 1",$id);
	}

	function opc_respuesta($id=''){
	return opc_sql("SELECT `valor`,descripcion FROM `catadeta` WHERE idcatalogo=170 and estado='A' ORDER BY 1",$id);
	}
	

	function formato_dato($a,$b,$c,$d){
		$b=strtolower($b);
		$rta=$c[$d];
	   // $rta=iconv('UTF-8','ISO-8859-1',$rta);
	   // var_dump($a);
	   // var_dump($rta);
		   if ($a=='tamepoc' && $b=='acciones'){
			$rta="<nav class='menu right'>";		
				$rta.="<li class='icono editar ' title='Editar' id='".$c['ACCIONES']."' Onclick=\"mostrar('tamepoc','pro',event,'','lib.php',7,'tamepoc');setTimeout(hiddxedad,300,'edad','cuestionario1','cuestionario2');\"></li>";  //act_lista(f,this);
			}
		return $rta;
	   }
	   
	   function bgcolor($a,$c,$f='c'){
		// return $rta;
	   }
	