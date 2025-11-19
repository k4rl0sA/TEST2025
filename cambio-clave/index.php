<?php
session_start();
ini_set('display_errors','1');
if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
include_once('./frmclave.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = $_POST['username'];
		$pwd =$_POST['passwd'];
		$repwd =$_POST['repasswd'];
		$patron = "/^(?=.*[0-9])(?=.*[A-Z])(?=.*[#$%&\'*+\-.\/<=>?@\\\\^])[0-9A-Za-z#$%&\'*+\-.\/<=>?@\\\\^]{8,}$/";
		//   /^(?=.*[0-9])(?=.*[A-Z])(?=.*[#$%&\'*+\-.\/<=>?@\\\\^])[0-9A-Za-z#$%&\'*+\-.\/<=>?@\\\\^]{8,}$/';
	if(preg_match($patron, $pwd)){
		if($pwd==$repwd){
			$valida=changePwd($name,$pwd);
			if ($valida[0] == 0){
				$link="../";
				// echo "<script>alert('".json_encode($_POST)."');</script>";//VALIDA
				echo "<script>alert('".json_encode($valida[1])."');window.location.replace('".$link."');</script>";
			}else{
				echo "<div class='error'>
				<span class='closebtn' onclick=\"this.parentElement.style.display='none';\">&times;</span> 
				<strong>Error!</strong> Ouch, No se puede cambiar la contraseña, consulte el administrador de la aplicación
				</div>";
			}
		}else{
			echo "<div class='error'>
				<span class='closebtn' onclick=\"this.parentElement.style.display='none';\">&times;</span> 
				<strong>Error!</strong> Ouch, Las contraseñas no coinciden.".$pwd." - ".$repwd."
				</div>";
		}
	}else{
		echo "<div class='error'>
						<span class='closebtn' onclick=\"this.parentElement.style.display='none';\">&times;</span> 
						<strong>Error!</strong> La contraseña no cumple con los requisitos,<br> 
						   Tener una longitud mínima de 8 caracteres.<br>
						   Incluir 1 dígito numérico (del 0 al 9).<br>
						   Minimo 1 letra mayúscula (de la A a la Z).<br>
						   Minimo 1 carácter especial (ejemplo: # $ % & ' * + - . / < = > ? @ \ ^).<br>
						Por favor valide.
						</div>";
	}
}


function db_connect(){
	$dominio = $_SERVER['HTTP_HOST'];
$comy = array(
  'pruebasiginf.site' => [
      's' => 'localhost',
      'u' => 'u470700275_17',
      'p' => 'z9#KqH!YK2VEyJpT',
      'bd' => 'u470700275_17'
  ],
  'gitapps.site' => [
      's' => 'localhost',
      'u' => 'u470700275_08',
      'p' => 'z9#KqH!YK2VEyJpT',
      'bd' => 'u470700275_08'
  ],
'gtaps.saludcapital.gov.co' => [
      's' => '10.234.8.132',
      'u' => 'u470700275_08',
      'p' => 'z9#KqH!YK2VEyJpT',
      'bd' => 'saludencasa_migrada'
  ]
);
	if (array_key_exists($dominio, $comy)) {
	  $dbConfig = $comy[$dominio];
	} else {
	  die('Dominio no reconocido.');
	}
	$con = new mysqli($dbConfig['s'], $dbConfig['u'],$dbConfig['p'],$dbConfig['bd']);
  if( !$con ){
    throw new Exception('No se ha podido conectar a la base de datos');
	die();
  } else {
    return $con;
  }
}

function changePwd($username,$password){
	$con = db_connect();
	$name = filter_var($username, FILTER_SANITIZE_STRING);
    $pwd = filter_var($password, FILTER_SANITIZE_STRING);
    $clave=password_hash($pwd,PASSWORD_DEFAULT);
		if(!($rta = $con->prepare("UPDATE `usuarios` SET `clave`= ?  WHERE `id_usuario`=? AND estado='A'"))){
			return $rta=['cod'=>$con->errno,'msj'=>'Prepare failed'];
			}
			//~ break;
		if(!$rta->bind_param('ss', $clave,$name)){
			return $rta=['cod'=>$con->errno,'msj'=>'Bind failed'];
		}
			 //~ exit();}
		if(!$rta->execute()){
			return $rta=['cod'=>$con->errno,'msj'=>'Execute failed'];
			//~ exit();
		}else{
				$cod=0;
				$msj = ($rta->affected_rows ==1) ? 'Se ha actualizado la Clave correctamente, recuerde que su clave es personal e intransferible' : 'No se pudo actualizar la contraseña, consulte con el administrador del sistema';
			}
	$con->close();
	return $rta=[$cod,$msj];
}	 

?>
