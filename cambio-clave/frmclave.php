<!DOCTYPE html>
<html>
<head>
	<title>Cambio de Clave || GIF-SDS</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
	<link rel="stylesheet" href="../libs/css/styleLogin.css">
	<link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="login,secretaria de salud,salud,subred,servicios,sur,norte,eac,occidente,medicina" />
<script>
	function getUser(a){
		var user=document.getElementById('user'); 
		user.value=a;
	}
</script>
</head>
<body>
<body onload="getUser('<?php echo $_SESSION['us_sds'];?>');">
<!-- <div id='mensaje'></div> -->
	<!-- <h1>SIGREV</h1><h2>Sistema de Información de Gestión del Riesgo de Espacio Vivienda</h2>
	<div class="w3layouts">
		<div class="signin-agile">
			<h2>Cambiar Contraseña</h2>
			<form action="#" method="post">
				<input type="hidden" name="username" class="user" id="user" required="">
				<input type="password" name="passwd" class="password" placeholder="Contraseña Nueva" required="">
				<input type="password" name="repasswd" class="password" placeholder="Confirmar Contraseña Nueva" required="">
				<div class="clear"></div>
				<input type="submit" value="Cambiar Contraseña">
			</form>
		</div>
		<div class="register-right">
			<img src="../libs/img/logoSsoAlfa.png" alt="images">
		</div>
		<div class="clear"></div>
	</div>
	<div class="footer-w3l">
		<p class="agileinf"> &copy; 2020 Todos los derechos reservados | Diseñado por <a href="http://w3layouts.com">W3layouts</a></p>
	</div>  -->
	

    <section class="main">
        <div class="login-container">
            <p class="title">GIF-SDS</p>
            <div class="separator"></div>
            <form class="login-form" method="POST">
                    <input type="text" name="username" id='user' class="form-control" placeholder="USUARIO" required="required">
				<div class="form-control">
					<input type="password" name="passwd" class="form-control" autocomplete="off" placeholder="CONTRASEÑA NUEVA" required="required" >
					<i class="fas fa-lock"></i>
				</div>
                <div class="form-control">
                    <input type="password" name="repasswd" class="form-control" placeholder="CONFIRMAR CONTRASEÑA" required="required">
                    <i class="fas fa-lock"></i>
                </div>
                <button class="submit">Cambiar Clave</button>
            </form>
        </div>
    </section>
	<section class="side">
        <img src="../libs/img/SDS.png" alt="">
    </section>	
<body>
</html>
