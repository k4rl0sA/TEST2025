<!-- <!DOCTYPE html>
<html lang="es"> -->
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menu Desplegable</title>
    <!-- <link rel="stylesheet" href="../libs/css/nav.css" /> -->
    <link rel="stylesheet" href="../libs/css/styleNav.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  </head>
  <!-- <body> -->
    <header>
			<div class="container">
			 <nav>
        <div class="menu-icons">
          <i class="icon icon-nav-menu material-icons s36">
            menu
          </i>
          <i class="icon icon-nav-close material-icons s36">
              close
          </i>
        </div>
				<a href="#" class="logo">
          <i class="icon-logo">
          </i>
          <i id="user" class="material-icons s48">person_outline</i>
        </a>
        <?php
          ini_set('display_errors','1');
          // echo "<script>alert('ok-NAV')</script>";
          require_once "../libs/gestion.php";
            // $dataUser=datos_mysql("SELECT nombre,perfil,proceso FROM usuarios WHERE id_usuario='".$_SESSION["us_riesgo"]."'");
            $sql="SELECT M.id id,U.nombre nombre,U.perfil perfil,M.link link,M.tipo tipo,M.enlace enlace,M.menu menu
            FROM menu_usuarios MU
            INNER JOIN usuarios U
            ON MU.perfil=U.perfil OR (MU.proceso='ALL' AND MU.perfil='ALL')
            INNER JOIN menu M
            ON MU.idmenu = M.id
            WHERE  U.id_usuario='".$_SESSION["us_eac"]."' AND U.estado='A' AND M.estado='A'";
            $rtaMenu=datos_mysql($sql);
          // echo $sql;

        $nav='<ul class="nav-list">';
        $total=count($rtaMenu['responseResult']);
      
        foreach ($rtaMenu['responseResult'] as $key => $menu) {
          if($menu['tipo']=="MEN" && $menu['menu']==0 ){
            $nav.='<li>
                 <a href="'.$menu['enlace'].'" 
                 class="eff-text-menu">'.$menu['link'].'
                   <i id="flecha" class="material-icons">expand_more</i>
                 </a><ul class="sub-menu">';
            foreach ($rtaMenu['responseResult'] as $key => $item){
              if($item['tipo']=="SUB" && $item['menu']==$menu['id']){
                $nav.='<li><a href="'.$item['enlace'].'"
                class="eff-text-menu">'.$item['link'].'</a></li>';
              }else {
                $item['tipo']."-".$item['enlace']."-".$item['link']."-".$menu['id'];
              }
            }
            $nav.="</ul>";
          }elseif($menu['tipo']=="MEN" && $menu['menu']==''){
            $nav.='<li><a href="'.$menu['enlace'].'" class="eff-text-menu" >'.$menu['link'].'</a></li>';
          }
        }
        ?>
            <?php echo $nav; ?>

					<li class="move-right btn">
            <select class="sm" id="theme" name="theme">
              <option value="dark">Dark</option>
              <option value="light">light</option>
              <option value="root" selected>Tema Predeterminado</option>
              <option value="Cepheus">Cepheus</option>
              <option value="Cygnus">Cygnus</option>
              <option value="Draco">Draco</option>
              <option value="Pyxis">Pyxis</option>
            </select>
          </li>
				</ul>
			</nav>
      <div class="lower"></div>
		</header>




<script src="../libs/js/a.js">
</script>

<!-- <iframe src="https://piensaendigital.es/" width="100%" height="700"></iframe> -->


  <!-- </body> -->
<!-- </html> -->










