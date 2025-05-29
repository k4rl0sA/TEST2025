 <?php
include ('claves.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login || EBEH</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="./libs/css/styleLogin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="login,secretaria de salud,salud,subred,servicios,sur,occidente,medicina" />
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $claves['publica'];?>"></script>
</head>
<body>
    <section class="side">
        <img src="./libs/img/SDS.png" alt="">
    </section>

    <section class="main">
        <div class="login-container">
            <p class="title">GTAPS-SDS</p>
            <div class="separator"></div>
            <form class="login-form" method="POST">
                <div class="form-control">
                    <input type="text" name="username"  class="form-control" placeholder="USUARIO" required="required">
                    <i class="fas fa-user"></i>
                </div>
                <div class="form-control">
                    <input type="password" name="passwd" class="form-control" placeholder="CONTRASEÑA" required="required" autocomplete="off">
                    <i class="fas fa-lock"></i>
                </div>
                <button  class='submit' id='btn'>Ingresar</button>
                <input type='hidden' name='token' id='token' />
            </form>
        </div>
    </section>
    <script>
        grecaptcha.ready(function(){
            grecaptcha.execute(
                '<?php echo $claves['publica'];?>',
                {action:'formulario'}
            ).then(function(rta_token){
                const tkn=document.getElementById('token');
                const btn=document.getElementById('btn');
                tkn.value=rta_token;
                btn.disabled=false;
            })
        });
    </script>
    </body>
</html>

