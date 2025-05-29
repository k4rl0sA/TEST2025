<?php
ini_set("display_errors", 1);
require_once "../gestion.php";
header('Content-Type: application/json');
date_default_timezone_set('America/Bogota');
$rta = array();
$perfil=datos_mysql("SELECT perfil FROM usuarios WHERE id_usuario='".$_SESSION["us_sds"]."'");
if ($perfil['responseResult'][0]['perfil'] != 'GEO' && $perfil['responseResult'][0]['perfil'] != 'ADM' && $perfil['responseResult'][0]['perfil'] != 'TEC' && $perfil['responseResult'][0]['perfil'] != 'SUPHOG') {
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
		if($_POST['tab']!=='geo_asig'){
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
				$tab='geo_asig';
				$ope=(isset($_POST['ope'])) ? $_POST['ope']:'insert';
				if($ncol!=8){
					$rta = array(
						'type' => 'Error','msj'=>'El número de campos no es valido por favor valide con el Administrador.'
					);
					response ($rta);
				}
				$asignado=asignados();
				while (($campo = fgetcsv($handle, 1024, $delimit)) !== false) {
					if ($nFil === 1) {
						$campos = $campo;
					}else{
						$sql = "INSERT INTO " . $tab . " VALUES(";				
						for ($i = 0; $i < $ncol; $i++) {
								if (isset($campo[$i])) {
                                    if ($i === 0 || $i === 5 ||$i === 6) {//id
                                        $sql .= 'NULL,';
                                    }
                                    if ($i === 1) { //Predio
                                        $sql .= "'" . $campo[$i] . "',";
									/* 	$valor = trim($campo[$i]);
                                        $predio=predios($valor);
										if (!in_array($valor, $predio)) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' no es valido.'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										} */
									}
                                    if ($i === 3 ) {//USU_CREO
                                        $sql .= "'" . $_SESSION['us_sds'] . "',";
                                    }
                                    if ($i === 4 ) {//FECHA_CREO
                                        $sql .= "'".date(format: 'Y-m-d H:i:s')."',";
                                    }
                                    if ($i === 7 ) {//estado
                                        $sql .= "'A');";
                                    }
									if ($i === 2) { //asignado
										$valor = trim($campo[$i]);
										if (!in_array($valor, $asignado)) {
											$rta = array(
												'type' => 'Error','msj'=>'Registro #' . ($nFil - 1) . ' - El valor "'.$valor.'" en '.$campos[$i].' no es valido debe estar Activo el usuario y ser de la subred correspondiente.'
											);
											response ($rta);
										} else {
											$sql .= "'" . $valor . "',";
										}
									}
                                    
								}else{
									$rta = array(
                                    'type' => 'Error','msj'=>'El archivo NO se encuentra delimitado por "," (comas) realiza la conversión e intenta nuevamente.'
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
					'type' => 'rtainfo','msj'=>'Se han insertado ' . ($nFil - 2) . ' Registro(s) de '.$total
				);
				response ($rta);
				// $rta .= "Se han insertado " . $ok . " Registro(s) de " . $total . " en total, Correctamente para la tabla ";
				
				fclose($handle);

function asignados(){
	$info=datos_mysql("SELECT id_usuario FROM usuarios WHERE componente IN(SELECT componente from usuarios where id_usuario='".$_SESSION['us_sds']."') and subred in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."') and estado='A';");
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

function predios($a){
	$info=datos_mysql("SELECT id_geo FROM hog_geo WHERE id_geo =$a and subred in (SELECT subred FROM usuarios where id_usuario='".$_SESSION['us_sds']."') and estado='A';");
	$valores = array(); 
	if (is_array($info['responseResult'])) {
		foreach ($info['responseResult'] as $item) {
			if (isset($item['id_geo'])) {
				$valores[] = $item['id_geo'];
			}
		}
		return $valores;
	}
}

function response($a){
	echo json_encode($a);
	exit;
}