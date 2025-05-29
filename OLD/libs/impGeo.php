<?php
ini_set("display_errors", 1);
require_once "../libs/gestion.php";
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
$rta = array();
$perfil=datos_mysql("SELECT perfil FROM usuarios WHERE id_usuario='".$_SESSION["us_sds"]."'");
if ($perfil['responseResult'][0]['perfil'] != 'GEO' && $perfil['responseResult'][0]['perfil'] != 'ADM' && $perfil['responseResult'][0]['perfil'] != 'TECFAM' && $perfil['responseResult'][0]['perfil'] != 'SUPHOG') {
	$rta = array(
		'type' => 'Error',
    	'msj' => 'No tiene el perfil permitido para cargar el archivo Csv, por favor consulte al administrador del sistema ' . $perfil['responseResult'][0]['perfil']
	);
	response($rta);
} 
	$file = $_FILES['archivo']['tmp_name'];
	$name = $_FILES['archivo']['name'];
	$type = $_FILES['archivo']['type'];
	$size = $_FILES['archivo']['size'];
	$ext = explode(".", $name);
	$delimit = ",";
	if (strtolower(end($ext)) !== "csv") {
		$rta = array(
			'type' => 'Error','msj'=>'El archivo $name contiene una extensión invalida!'.strtolower(end($ext))
		);
		response ($rta);
	}
		if($_POST['tab']!=='geografico'){
			$rta = array(
				'type' => 'Error','msj'=>'La tabla '.$_POST["tab"].' no corresponde, consulte con el administrador' 
			);
			response ($rta);
		}
			$handle = fopen($file, "r");
			if ($handle === FALSE) {
				$rta = array(
					'type' => 'Error','msj'=>'No se pudo abrir el archivo'. $name
				);
				response ($rta);
			}
				$nFil = 1;
				$ok=0;
				$ncol=$_POST['ncol'];
				$tab='hog_geo';
				$ope=(isset($_POST['ope'])) ? $_POST['ope']:'insert';
				if($ncol!=30){
					$rta = array(
						'type' => 'Error','msj'=>'El número de campos no es valido por favor valide con el Administrador.'
					);
					response ($rta);
				}
				$sub=datos_mysql("SELECT subred FROM usuarios WHERE id_usuario='".$_SESSION["us_sds"]."'");
				$subred=$sub['responseResult'][0]['subred'];
				$estrategias=catalogo(42,1);
				$localidades=catalogo(2,2);
				$upzs=catalogo(7,3);
				// $estratos=catalogo(101,1);
				$estados=catalogo(44,1);
				$asignados=asignados();
				

				while (($campo = fgetcsv($handle, 1024, $delimit)) !== false) {
					if ($nFil === 1) {
						$campos = $campo;
					}else{
						$sql = "INSERT INTO " . $tab . " VALUES(";
				
						for ($i = 0; $i < $ncol; $i++) {
							//if ($i + 1 == $ncol) {
								/* if ($campo[$i] != 'NULL') {
										$sql .= "'" . trim($campo[$i]) . "'";
									} else {
										$sql .= "NULL";
									}
								} else { */
								if ($i === 0 || $i === 29 ||$i === 28) {//idgeo
									if ($i === 29){
										$sql .='NULL);';
									}else{
										$sql .= 'NULL,';
									}
								}
								if (isset($campo[$i])) {
									if ($i === 1) { //estrategia
										$valor = trim($campo[$i]);
										// $valor=trim($nFil);
										if (!in_array($valor, $estrategias) || strlen($valor) != 1) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' esta fuera del rango ('.$estrategias[0].' a '.count($estrategias).').'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 2) {//subred
										$valor = trim($campo[$i]);
										if ($valor!=$subred) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' esta fuera del rango (1-4) o no corresponde a su subred = '.trim($subred)
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 3) {//zona
										$valor = trim($campo[$i]);
										if ($valor < 1 || $valor > 2) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' no es (1 ó 2).'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 4) {//localidad
										$valor = trim($campo[$i]);
    									if (!in_array($valor, $localidades) || strlen($valor) != 2) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' debe contener 2 digitos y un rango de ('.$localidades[0].' a '.count($localidades).').' 
											);
											response ($rta);
    									}else{
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 5) {//upz
										$valor = trim($campo[$i]);
    									if (!in_array($valor, $upzs) || strlen($valor) != 3) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' debe contener 3 digitos y un rango de (001 a 117) o (R01 a R05).' 
											);
											response ($rta);
    									}else{
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 6 || $i===9) {//barrio,sector catastral
										$valor = trim($campo[$i]);
    									if (strlen($valor) != 6) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' debe contener 6 digitos.' 
											);
											response ($rta);
    									}else{
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 7) {//territorio
										$valor = trim($campo[$i]);
										if (is_string($valor) && (strlen($valor) === 6 || $valor === '0')) {
											$sql .= "'" . $valor . "',";
										} else {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' debe contener 6 caracteres ó 0 sino posee.'
											);
											response ($rta);
    									}
									}
									if ($i === 8) {//microterritorio- Manzana del cuidado
										$valor = trim($campo[$i]);
    									if (strlen($valor) != 2 || $valor === '0' && $valor>25) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en Manzana del Cuidado debe contener 2 digitos'
											);
											response ($rta);
    									}else{
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 10 || $i === 11 || $i === 15 || $i === 16 || $i === 19 || $i === 20 || $i === 23 || $i=== 25) {//direccion,vereda,vereda NUEVA,23Equipos
											$sql .= "'" . trim($campo[$i]) . "',";
									}
									if ($i === 12 ||$i === 13 || $i === 14) {//manzana,predio,unidad habitacional
										$valor = trim($campo[$i]);
    									if (strlen($valor) != 3 ) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' debe contener 3 digitos'
											);
											response ($rta);
    									}else{
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 17 || $i === 18 ) {
										$valor = trim($campo[$i]);
										if (substr_count($valor, '.') === 1) {
											$sql .= "'" . $valor . "',";
										} else {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' debe contener una coordenada correcta.'
											);
											response($rta);
										}
									}
									if ($i === 21) { //estrato
										$valor = trim($campo[$i]);
										if ($valor < 1 || $valor > 7) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' esta fuera del rango (1 a 7).'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 22) { //asignado
										$valor = trim($campo[$i]);
										if (!in_array($valor, $asignados)) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' No se encuentra dentro de los usuarios del sistema para su subred.'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 24) { //estados
										$valor = trim($campo[$i]);
										if (!in_array($valor, $estados)) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' esta fuera del rango permitido.'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
									if ($i === 26 ) {//USU_CREO
											$sql .= "'" . $_SESSION['us_sds'] . "',";
									}
									if ($i === 27 ) {//FECHA_CREO
											$sql .= "'".date(format: 'Y-m-d H:i:s')."',";
									}
								}else{
									$rta = array(
										'type' => 'Error','msj'=>'El archivo no se encuentra delimitado por "," (comas) realiza la conversión e intenta nuevamente.'
									);
									response ($rta);
								}
						}
						
						$r = dato_mysql($sql);				
						if (preg_match('/Error/i', $r)){
							$rta = array(
								'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' '.$r.'<br><br>Se insertaron ' . ($nFil - 2) . ' Registro(s) previamente'
							);
							response ($rta);
						}
					}
					$nFil++;
				}
				$total = $nFil - 2;
				$rta = array(
					'type' => 'OK','msj'=>'Se han insertado ' . ($nFil - 2) . ' Registro(s) de '.$total
				);
				response ($rta);
				// $rta .= "Se han insertado " . $ok . " Registro(s) de " . $total . " en total, Correctamente para la tabla ";
				
				fclose($handle);

function catalogo($id,$nd){
	$info=datos_mysql('select idcatadeta from catadeta where idcatalogo ='.$id.' ORDER BY cast(idcatadeta as signed)');
	$valores = array(); 
	if (is_array($info['responseResult'])) {
		foreach ($info['responseResult'] as $item) {
			if (isset($item['idcatadeta'])) {
				$valor = str_pad($item['idcatadeta'], $nd, '0', STR_PAD_LEFT);
				$valores[] = $valor;
			}
		}
		return $valores;
	}
}

function asignados(){
	$info=datos_mysql("SELECT id_usuario FROM usuarios WHERE componente IN(SELECT componente from usuarios where id_usuario='".$_SESSION['us_sds']."') and subred in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."') and estado='A' ");
	$valores = array(); 
	if (is_array($info['responseResult'])) {
		foreach ($info['responseResult'] as $item) {
			if (isset($item['id_usuario'])) {
				$valores[] = $item['id_usuario'];
			}
		}
		return $valores;
	}
}


function response($a){
	echo json_encode($a);
	exit;
}