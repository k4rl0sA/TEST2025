<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- <meta http-equiv="Content-Security-Policy" content="img-src 'self' https://drive.google.com"> -->
<!--
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
-->
<style>
body {
  font-family: Arial;
  margin: 0;
}

* {
  box-sizing: border-box;
}

img {
  vertical-align: middle;
}

/* Position the image container (needed to position the left and right arrows) */
.container {
  position: relative;
  max-width: 100vw;
  overflow: hidden;
}

/* Hide the images by default */
.mySlides {
  display: none;
}

.mySlides img {
  width: 100%;
  height: auto;
  object-fit: cover;
  /*max-height: 100vh; */
}

.column img {
  width: 100%; 
  height: auto; 
  object-fit: cover;
  cursor: pointer;
}

/* Add a pointer when hovering over the thumbnail images */
.cursor {
  cursor: pointer;
}

/* Next & previous buttons */
.prev,
.pause,
.next {
  cursor: pointer;
  position: absolute;
  top: 40%;
  width: auto;
  padding: 16px;
  margin-top: -50px;
  color: white;
  font-weight: bold;
  font-size: 20px;
  border-radius: 0 3px 3px 0;
  user-select: none;
  -webkit-user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

.pause {
  right: 50%;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover,
.pause:hover,
.next:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* Container for image text */
.caption-container {
  text-align: center;
  background-color: #222;
  padding: 2px 16px;
  color: white;
}

.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Six columns side by side */
.column {
  float: left;
  width: 16.66%;
}

/* Add a transparency effect for thumnbail images */
.demo {
  opacity: 0.6;
}

.active,
.demo:hover {
  opacity: 1;
}

.active {
  background-color: #717171;
}

/* Fading animation */
.fade {
  -webkit-animation-name: fade;
  -webkit-animation-duration: 1.5s;
  animation-name: fade;
  animation-duration: 1.5s;
}

@-webkit-keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .text {font-size: 11px}
}

@media (max-width: 768px) {
  .column {
    width: 50%; 
  }
}

@media (max-width: 480px) {
  .column {
    width: 100%;
  }
}
</style>
<body>
<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
//echo "<script>alert('ok-MAIN')</script>";
// if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}
require_once "../libs/gestion.php";
$img=datos_mysql("SELECT `id_key` FROM `adm_medios` WHERE `modulo`= 'MAI' AND `estado`='A' ORDER BY `id`");
$vid="https://drive.google.com/file/d/";
$drv="https://drive.google.com/thumbnail?id=";
//$dr="https://drive.google.com/thumbnail?id=1c79WTsxeuzdyAL3xMn9kTKuHwnQjYS6A"
//~ var_dump($datos["responseResult"]);
?>
<!--

<h2 style="text-align:center">Galeria</h2>
-->
<div class="container">
  
  <div class="mySlides fade">
    <div class="numbertext">1 / 7</div>
    <a href="https://www.saludcapital.gov.co/Paginas2/Noticia_Portal_Detalle.aspx?IP=2440" target="_blank" alt="Haz click para ver la fueunte">
    <img src=<?php echo "'".$drv.$img['responseResult'][0]['id_key']."&sz=w1920-h1080'"; ?> alt="Haz click para ver la fueunte">
    </a>
  </div>

  <div class="mySlides fade">
    <div class="numbertext">2 / 7</div>
    <img src=<?php echo "'".$drv.$img['responseResult'][1]['id_key']."&sz=w1920-h1080'"; ?>>
  </div>

  <div class="mySlides fade">
    <div class="numbertext">3 / 7</div>
    <img src=<?php echo "'".$drv.$img['responseResult'][2]['id_key']."&sz=w1920-h1080'"; ?>>
  </div>
    
  <div class="mySlides fade">
    <div class="numbertext">4 / 7</div>
    <iframe id="inlineFrameExample"
			title="Inline Frame Example"
			width="100%"
			height="700"
			src="https://www.saludcapital.gov.co/Paginas2/Noticia_Portal_Detalle.aspx?IP=2440">
		</iframe>

</div>
  <div class="mySlides fade">
    <div class="numbertext">5 / 7</div>
    <img src=<?php echo "'".$drv.$img['responseResult'][3]['id_key']."&sz=w1920-h1080'"; ?>>
  </div>
    
  <div class="mySlides fade">
    <div class="numbertext">6 / 7</div>
    <img src=<?php echo "'".$drv.$img['responseResult'][4]['id_key']."&sz=w1920-h1080'"; ?>>
  </div>

  <div class="mySlides fade">
    <div class="numbertext" id='video1'>7 / 7<div id='duration'></div></div>
    <iframe src=<?php echo "'".$vid.$img['responseResult'][5]['id_key']."/preview'"; ?> width="100%" allow="autoplay"></iframe>
		<!-- <video class="demo cursor" autoplay muted loop controls preload onclick="currentSlide(4)" alt="Video 4" width="100%">
			<source src=<?php /* echo "'".$drv.$img['responseResult'][0]['id_key']."/view'"; */ ?> type="video/mp4">		
		</video> -->
  </div>
    
  <a class="prev" onclick="plusSlides(-1)">❮</a>
<!--
  <a class="pause" onclick="plusSlides(0,false)">II</a>
-->
  <a class="next" onclick="plusSlides(1)">❯</a>

  <div class="caption-container">
    <p id="caption"></p>
  </div>

  <div class="row">
    <div class="column">
      <img class="demo cursor" src=<?php echo "'".$drv.$img['responseResult'][0]['id_key']."'"; ?> style="width:100%" onclick="currentSlide(1)" alt="Imagen 1" height="95px" width="233px">
    </div>
    <div class="column">
      <img class="demo cursor" src=<?php echo "'".$drv.$img['responseResult'][1]['id_key']."'"; ?> style="width:100%" onclick="currentSlide(2)" alt="Imagen 2" height="95px" width="233px">
    </div>
    <div class="column">
      <img class="demo cursor" src=<?php echo "'".$drv.$img['responseResult'][2]['id_key']."'"; ?> style="width:100%" onclick="currentSlide(3)" alt="Imagen 3" height="95px" width="233px">
    </div>
    <div class="column">
		<img class="demo cursor" src=<?php echo "'".$drv.$img['responseResult'][3]['id_key']."'"; ?> style="width:100%" onclick="currentSlide(4)" alt="Imagen 4" height="95px" width="233px">
    </div>
    <div class="column">
      <img class="demo cursor" src=<?php echo "'".$drv.$img['responseResult'][4]['id_key']."'"; ?> style="width:100%" onclick="currentSlide(5)" alt="Imagen 5" height="95px" width="233px">
    </div>    
    <div class="column">
      <img class="demo cursor" src=<?php echo "'".$drv.$img['responseResult'][5]['id_key']."'"; ?> style="width:100%" onclick="currentSlide(6)" alt="Imagen 6" height="95px" width="233px">
    </div>
    <div class="column">
      <video class="demo cursor" autoplay muted loop onclick="currentSlide(7)" title="Video 7" height="95px" width="233px">
			  <source src=<?php echo "'".$drv.$img['responseResult'][6]['id_key']."/view'"; ?> type="video/mp4">		
		  </video>
      </div>
  </div>
</div>

<script>
	//~ var slides = document.getElementsByClassName("mySlides");
	
	 var slides = document.getElementsByClassName("mySlides");
	 var slideIndex = 1;
	 showSlides(slideIndex);
	//~ showSlides(slides.length+1,true);
	

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  if(slideIndex==7)slideIndex=1;
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;
  //~ if (ini==true){
	  //~ pause(true);
  //~ }else{
	  //~ ini==false;
	  //~ pause(false);
  //~ }
  //~ pause(true);
  //~ setTimeout(showSlides, 2000,slideIndex++);
}

var v = document.getElementById("video1");
v.addEventListener("loadeddata",function(ev){
   document.getElementById("duration").innerHTML = v.duration;		
},true);


//~ window.setInterval(function(t){
	//~ if (video.readyState > 0) {
//~ var duration = $('#duration').get(0);
//~ var vid_duration = Math.round(video.duration);
//~ duration.firstChild.nodeValue = vid_duration;
//~ clearInterval(t);
//~ },500);}



function pause(p=false){
	if (p==true){
		setTimeout(showSlides,9000,slideIndex++,true);
	}else{
		ini=false;
	}
	//~ else{showSlides(slideIndex,false);}
}
</script>
    


</body>
</html>
