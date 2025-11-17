<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'].'/libs/nav.php';
?>
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Información Geografica || <?php echo $APP; ?></title>
<link href="../libs/css/stylePop.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cabin+Sketch&family=Chicle&family=Merienda&family=Rancho&family=Boogaloo&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<script src="../libs/js/a.js?v=38"></script>
<script src="../libs/js/x.js?v=38"></script>
<script src="../libs/js/d.js"></script>
<script src="../libs/js/popup.js"></script>
<script>
var mod='homes';
var ruta_app='lib.php';

const editUsers = [
    /* { selector: '#regimen', func: enabAfil, params: ['regimen', 'eaf'] },
    { selector: '#etnia', func: enabEtni, params: ['etnia', 'ETn', 'idi'] },
    { selector: '#reside_localidad', func: enabLoca, params: ['reside_localidad', 'lochi'] },
    { selector: '#ocupacion', func: EditOcup, params: ['ocupacion', 'true'] },
    { selector: '#cuidador', func: hideCuida, params: ['cuidador', 'cUi'] } */
];

document.onkeyup=function(ev) {
	ev=ev||window.event;
/* 	if (ev.ctrlKey && ev.keyCode==46) ev.target.value='';
	if (ev.ctrlKey && ev.keyCode==45) ev.target.value=ev.target.placeholder; */
};

function actualizar(){
	act_lista(mod);
}

function fixRecord(a = '', id = '') {
  const fields = document.getElementById(`${a}-pro-con`)
    .querySelectorAll('select:not(.nFx), input:not(.nFx), textarea:not(.nFx)');

  try {
    const cod = id ? document.getElementById(id).value : null;

    fields.forEach(field => {
      if (field.tagName === 'SELECT') {
        field.selectedIndex = 0;
      } else if (field.tagName === 'INPUT') {
        if (field.type === 'checkbox') {
          field.checked = false;
          field.value = 'NO';
        } else {
          field.value = '';
        }
      } else if (field.tagName === 'TEXTAREA') {
        field.value = '';
      }
    });

    // Ejecutar `getJSON` una sola vez y procesar resultados
    if (id) {
      fix_Alertas(a, cod, 'alertas.php', fields);
    }
  } catch (error) {
    console.error('Error al procesar los campos:', error);
  }
}

async function fix_Alertas(frm, id, path = 'lib.php', fields) {
  try {
    const rta = await getJSON('fix', frm, id, path);

	const cmpDbl = ['cro_hiper', 'cro_diabe', 'cro_epoc', 'cro_sinctrl', 'evento_pf'];
    // Validar si los datos requeridos están disponibles
    if (!rta || !rta['sexo'] || rta['ano'] === undefined) {
      console.warn('Datos incompletos en la respuesta:', rta);
      return;
    }
    fields.forEach(cmp => {
		if (cmpDbl.includes(cmp.id)) {
        // Bloquear campos que siempre deben estar deshabilitados
        cmp.disabled = true;
      } else if (cmp.id === 'gestante'|| cmp.id==='etapgest'|| cmp.id==='ges_sinctrl') {
        cmp.disabled = !(rta['sexo'] === 'MUJER' && (rta['ano'] >= 9 && rta['ano'] <= 56));
      } else if (cmp.id === 'men_dnt' || cmp.id === 'men_sinctrl') {
        cmp.disabled = !(rta['ano'] <= 5);
      } else {
        // Bloqueo por defecto si no cumple ninguna regla
        cmp.disabled = false;
      }
    });
  } catch (error) {
    console.error('Error en fix_Alertas:', error);
  }
}

function grabar(tb='',ev){
  if (tb=='' && ev.target.classList.contains(proc)) tb=proc;
  var f=document.getElementsByClassName('valido '+tb);
   for (i=0;i<f.length;i++) {
     if (!valido(f[i])) {f[i].focus(); return};
  }
  	const rutaMap = {
		'prinfancia':'prinfancia.php',
		'adolesce':'adolescencia.php',
		'infancia':'infancia.php',
		'admision':'admision.php',
		'pregnant':'gestantes.php',
		'prechronic':'cronicos.php',
		'statFam':'stateFami.php',
		'caract':'../crea-caract/lib.php',
		'planDcui':'plancui.php',
		'compConc':'plncon.php',
		'signos':'signos.php',
		'ambient':'amb.php',
		'alertas':'alertas.php',
    'vspeve':'vspeve.php',
    'acompsic':'../vsp/acompsic.php',
    'apopsicduel':'../vsp/apopsicduel.php',
    'bpnpret':'../vsp/bpnpret.php',
    'bpnterm':'../vsp/bpnterm.php',
    'cancinfa':'../vsp/cancinfa.php',
    'cronicos':'../vsp/cronicos.php',
    'eraira':'../vsp/eraira.php',
    'gestantes':'../vsp/gestantes.php',
    'hbgest':'../vsp/hbgest.php',
    'mnehosp':'../vsp/mnehosp.php',
    'mme':'../vsp/mme.php',
    'otroprio':'../vsp/otroprio.php',
    'saludoral':'../vsp/saludoral.php',
    'sificong':'../vsp/sificong.php',
    'sifigest':'../vsp/sifigest.php',
    'vihgest':'../vsp/vihgest.php',
    'violreite':'../vsp/violreite.php',
    'dntsevymod':'../vsp/dntsevymod.php',
    'condsuic':'../vsp/condsuic.php',
    'violgest':'../vsp/violgest.php',
    'tamApgar':'../tamizajes/apgar.php',
    'segComp':'plnsegcon.php',
    'tamfindrisc':'../tamizajes/findrisc.php',
    'tamoms':'../tamizajes/oms.php',
    'tamepoc':'../tamizajes/epoc.php',
    'tamcope':'../tamizajes/cope.php',
    'relevo':'../relevo/lib.php',
    'sesiones':'../relevo/sesiones.php',
    'psicologia':'../psicologia/lib.php',
    'sesion2':'../psicologia/lib.php',
    'sesiones_psi':'../psicologia/sesiones.php',
    'sesion_fin':'../psicologia/lib.php',
    'atencionM':'../atenciones/atencionMedi.php',
    'atencionO':'../atenciones/atencionOdon.php',
    'atencionP':'../atenciones/atencionPsic.php',
	'tamzung':'../tamizajes/zung.php',
	'tamhamilton':'../tamizajes/hamilton.php',
	'tamWhodas':'../tamizajes/whodas.php',
	'tamBarthel':'../tamizajes/barthel.php',
	'tamzarit':'../tamizajes/zarit.php',
  'etnias':'etnias.php',
  'ethnicity':'../etnias/tipoetn.php',
  'emb_Id':'../etnias/embid.php',
  'segnoreg':'../etnias/embsegnoreg.php',
  'seguim':'../etnias/embsegui.php',
  'uaic_id':'../etnias/uaicid.php',
  'uaic_seg':'../etnias/uaicseg.php',
 'servagen':'../agendamient/serage.php',
  'tamrqc':'../tamizajes/rqc.php',
  'tamsrq':'../tamizajes/srq.php',
  'validPerson':'../soporte/valperson.php',
  'medicamentctrl':'../servicios_complem/medicamentos.php',
  'traslados': '../soporte/trasladloc.php',
  'trasladint': '../soporte/interloc.php',
  'tamcarlos':'../tamizajes/carlos.php',
  'tamassist':'../tamizajes/assist.php',
  'tamvalories':'../tamizajes/valoriesg.php',
  'riesgomental':'../tamizajes/riesgomental.php',
  'laboratorios':'../servicios_complem/laboratorios.php',
  'unidadeshs': '../soporte/unidades.php',
  'resultLab': '../servicios_complem/laboratorios.php',
  'barreras':'../tamizajes/barreras.php',
  'tamsoledad':'../tamizajes/soledad.php',
  'educaMedi':'../vsp/educaMedi.php',
  'discapacidad':'../vsp/discapacidad.php',
  'discapacidad1':'../vsp/discapacidad1.php',
  'feminicidio':'../vsp/feminicidio.php',
  'feminicidio1':'../vsp/feminicidio1.php',
  'soledad':'../vsp/soledad.php'
 	};
   let ruta_app = rutaMap[tb] || 'lib.php';
  if(tb=='sesion2'){
    let mensaje = "Desea guardar la información. Recuerda que no se podrá editar posteriormente la \n CONTINUIDAD DEL CASO:\n" + document.getElementById('contin_caso').selectedOptions[0].text;
    let res = confirm(mensaje);
    if(res==true){
				myFetch(ruta_app,"a=gra&tb="+tb,mod);
        resetFrm();
			}
  }else if(tb=='validPerson'){
      fetch(ruta_app, {
        /*   setTimeout(function(){
            act_lista(tb, null, ruta_app);
          }, 800); */
      method: 'POST',
      headers: {'Content-type': 'application/x-www-form-urlencoded'},
      body: "a=gra&tb="+tb + form_input('fapp')
    })
    .then(response => response.text())
    .then(data => {
      let resp = data;
      try { resp = JSON.parse(data); } catch(e){}
      if(resp && resp.confirm){
        if(confirm(resp.msg)){
          // Si el usuario acepta, enviar de nuevo con flag de confirmación
          fetch(ruta_app, {
            method: 'POST',
            headers: {'Content-type': 'application/x-www-form-urlencoded'},
            body: "a=gra&tb="+tb+"&confirmado=1" + form_input('fapp')
          })
          .then(r => r.text())
          .then(d => {
            let rta = d;
            try { rta = JSON.parse(d); } catch(e){}
            if(rta && rta.success){
              ok(rta.msg+' Los cambios estarán disponibles en el sistema en un plazo de hasta 24 horas. Después de ese tiempo, repita el proceso de "Validar Usuario".');//mensaje de actualizacion
              // ok(rta.msg);
              // alert(rta.msg + " Estado: " + rta.estado);
            } else {
                 /*  setTimeout(function(){
                    act_lista(tb, null, ruta_app);
                  }, 800); */
              errors("Error al guardar: " + (rta.msg || "Respuesta no válida"));
            }
          });
        } else {
          warnin("Operación cancelada por el usuario.");
        }
      } else if(resp && resp.success){
        ok(resp.msg+' Información guardada correctamente. Haga clic en "Mostrar Integrantes" para ver los cambios.');
        // alert(resp.msg + " Estado: " + resp.estado);
          /*   setTimeout(function(){
              act_lista(tb, null, ruta_app);
            }, 800); */
      } else {
        // Si no es JSON, mostrar como antes
        errors(JSON.parse(data).msg);
      }
    });
  }else{
    myFetch(ruta_app,"a=gra&tb="+tb,mod);
    // Actualizar todas las tablas relevantes en tiempo real tras grabar
    // mostrar('homes1','fix',event,'','lib.php',0,'homes1');
    setTimeout(act_lista,1000,'famili',this,'lib.php');
    setTimeout(act_lista,1000,'homes1',this,'lib.php');
    setTimeout(act_lista,1000,'person1',this,'lib.php');
    // Si necesitas más tablas, agrega aquí:
    // setTimeout(act_lista,1000,'otraTabla',this,'lib.php');
    resetFrm();
  }
  // setTimeout(function(){act_lista(tb, null, ruta_app);}, 800);
}  

 let currentOpenMenu = null;

document.body.addEventListener('click', function(event) {
  // Verifica si el click fue en un botón de menú
  if (event.target.classList.contains('icono') && event.target.classList.contains('menubtn')) {
    const id = event.target.id.split("_");
    crearMenu(id[1] + '_' + id[2]);
  }
});

function crearMenu(id) {
  const menuToggle = document.getElementById('menuToggle_' + id);
  const menuContainer = document.getElementById('menuContainer_' + id);

  // Si el menú ya está cargado, solo muestra/oculta
  if (menuContainer.innerHTML.trim() !== "") {
    toggleMenu(menuContainer, menuToggle);
    return;
  }

  // Hacer la solicitud al backend para obtener los datos de los botones
  fetch('lib.php', 
    { method: 'POST',  
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'a=opc&tb=menu'
    })
    .then(response => response.json())
    .then(buttonsData => {
      cargarRecursosCSSyFontAwesome(); // Función que carga los estilos necesarios

      // Crear el HTML del menú dinámicamente con los datos recibidos
      const html = generateMenuHTML(buttonsData);
      menuContainer.innerHTML = html;

      setupMenuBehavior(menuContainer, menuToggle);  // Configurar el comportamiento del menú
      toggleMenu(menuContainer, menuToggle);  // Mostrar el menú al cargarlo por primera vez
    })
    .catch(error => console.error('Error al cargar el menú:', error));
}

// Función para generar el HTML del menú dinámicamente
function generateMenuHTML(buttonsData) {
    // Crear el div principal
    let html = `<div class="panel-acc">
                  <div class="ind-move"></div>
                  <span class="closePanelAcc">&times;</span>
                  <div class="toolbar">`;

    // Añadir los botones usando los datos del backend
    buttonsData.forEach(btn => {
        html += `<button class="action">
                   <i class="icon ${btn.iconClass}"></i>
                   <span class="actionTitle">${btn.title}</span>
                   <span class="shortcut">${btn.shortcut}</span>
                 </button>`;
    });

    // Cerrar el div 'toolbar' y 'panel-acc'
    html += `</div></div>`;
    return html;
}

// Función para configurar el comportamiento del menú
function setupMenuBehavior(menuContainer, menuToggle) {
  const contextMenu = menuContainer.querySelector('.panel-acc');
  const isMobile = window.innerWidth <= 768;

  // Prevenir que se añadan múltiples listeners al mismo toggle
  menuToggle.removeEventListener('click', menuToggleClickHandler);
  menuToggle.addEventListener('click', menuToggleClickHandler);

  function menuToggleClickHandler(e) {
    e.stopPropagation();
    toggleMenu(menuContainer, menuToggle);
  }

  // Botón de cierre del menú
  const closeButton = contextMenu.querySelector('.closePanelAcc');
  if (closeButton) {
    closeButton.addEventListener('click', () => {
      closeMenu(menuContainer);
    });
  }

  // Acciones dentro del menú
  const actions = contextMenu.querySelectorAll('.action');
  actions.forEach(action => {
    action.addEventListener('click', (event) => {
    const actionName = action.querySelector('.actionTitle').textContent;
    console.log(`Acción seleccionada: ${actionName}`);
    closeMenu(menuContainer);
    event.preventDefault();
  });
});

  // Cerrar el menú cuando se haga clic fuera de él
  document.addEventListener('click', (e) => {
    if (!contextMenu.contains(e.target) && e.target !== menuToggle) {
      closeMenu(menuContainer);
    }
  });

  // Deslizamiento táctil para cerrar el menú
  let touchStartY;
  contextMenu.addEventListener('touchstart', (e) => {
    touchStartY = e.touches[0].clientY;
  });

  contextMenu.addEventListener('touchmove', (e) => {
    const touchEndY = e.touches[0].clientY;
    const diff = touchEndY - touchStartY;
    if (diff > 50) {
      closeMenu(menuContainer);
    }
  });
}

function toggleMenu(menuContainer, menuToggle) {
  const contextMenu = menuContainer.querySelector('.panel-acc');
  const isMobile = window.innerWidth <= 768;

  // Si hay un menú actualmente abierto, lo cerramos antes de abrir el nuevo
  if (currentOpenMenu && currentOpenMenu !== menuContainer) {
    closeMenu(currentOpenMenu);
  }

  // Maneja la visibilidad del menú correctamente
  if (isMobile) {
    contextMenu.classList.toggle('show');
  } else {
    const rect = menuToggle.getBoundingClientRect();
    contextMenu.style.top = rect.bottom + 'px';  // Ajusta la posición top
    contextMenu.style.display = contextMenu.style.display === 'none' || contextMenu.style.display === '' ? 'block' : 'none';
  }

  // Actualiza la variable global para guardar el menú actualmente abierto
  if (contextMenu.style.display === 'block' || contextMenu.classList.contains('show')) {
    currentOpenMenu = menuContainer;
  } else {
    currentOpenMenu = null;  // Si el menú está cerrado, reseteamos la variable
  }
}

function closeMenu(menuContainer) {
  const contextMenu = menuContainer.querySelector('.panel-acc');
  const isMobile = window.innerWidth <= 768;

  if (isMobile) {
    contextMenu.classList.remove('show');
  } else {
    contextMenu.style.display = 'none';
  }

  // Resetea la variable para indicar que no hay menú abierto
  currentOpenMenu = null;
}


function validardias(a) {
      var numero = parseInt(a.value);
      if (isNaN(numero) || numero < 1 || numero > 30) {
		errors(`Por favor, ingresa un número válido de 1 a 30 dias`);
      }
}

/*******************INICIO RELEVO*************************/
function enabCare(a, b) {
  for (let j = 0; j < b.length; j++) {
    const elements = document.querySelectorAll('select.' + b[j] + ', input.' + b[j]);
    elements.forEach(item => enaFie(item, true)); // Desactivar todos inicialmente
  }
  if (a.value >= '1') {
    const cr1Elements = document.querySelectorAll('select.cr1, input.cr1');
    cr1Elements.forEach(item => enaFie(item, false));
  }
  if (a.value >= '2') {
    const cr2Elements = document.querySelectorAll('select.cr2, input.cr2');
    cr2Elements.forEach(item => enaFie(item, false));
  }
  if (a.value == '3') {
    const cr3Elements = document.querySelectorAll('select.cr3, input.cr3');
    cr3Elements.forEach(item => enaFie(item, false));
  }
}

function othePath(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	// const act = document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(a.value!='12'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}

function validCare(a) {
  const el = document.querySelectorAll('select.' + a + ', input.' + a);
  if (el.length < 3) {
    console.warn("Se necesitan al menos tres elementos para la validación");
    return;
  }
  // Obtener los valores, ignorando espacios en blanco
  const values = [el[0]?.value.trim(), el[1]?.value.trim(), el[2]?.value.trim()];
  const nonEmptyValues = values.filter(val => val !== "");
  const uniqueValues = new Set(nonEmptyValues);
  if (uniqueValues.size !== nonEmptyValues.length) {
    warnin("EL CUIDADOR NO PUEDE SER LA MISMA PERSONA CUIDADA EN DIFERENTES CAMPOS");
  }
}

//HABILITAR ACTIVIDAD DE RESPIRO
function chanActi(a,c,d) {
	const ele = document.querySelectorAll('select.'+c+',input.'+c);
	const act = document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(!d.includes(act.value) ){
			enaFie(ele[i],!d.includes(act.value));
  		}
	}
}

function auxSign(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act = document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value!='AUXREL'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}
/*******************FIN RELEVO*************************/

/*******************INICIO PSICOLOGIA*************************/
function hidOpt(act,clsCmp,clsCmp1,valid){
	const cmpAct=document.getElementById(act);
	const cmps = document.querySelectorAll(`.${clsCmp}`);
	const x = document.querySelectorAll(`.${clsCmp1}`);
	if(cmpAct.value=='SI'){
		for(i=0;i<cmps.length;i++){
			hidFie(cmps[i],valid);
		}
		for(i=0;i<x.length;i++){
			hidFie(x[i],!valid);
		}
	}else{
		for(i=0;i<cmps.length;i++){
			hidFie(cmps[i],!valid);
		}
		for(i=0;i<x.length;i++){
			hidFie(x[i],valid);
		}
	}
}

function hidFieOld(act,clsCmp,valid) {
	const cmpAct=document.getElementById(act);
	const cmps = document.querySelectorAll(`.${clsCmp}`);
	if(cmpAct.value=='SI'){
		for(i=0;i<cmps.length;i++){
			hidFie(cmps[i],valid);
		}
	}else{
		for(i=0;i<cmps.length;i++){
			hidFie(cmps[i],!valid);
		}
	}
}

function hidFieselet(act,clsCmp,b,valid,valor) {
	const cmpAct=document.getElementById(act);
	const cmpHid = document.querySelectorAll(`.${clsCmp}`);

	if(valid){
		if(!cmpAct){
			for(i=0;i<cmpHid.length;i++){
				cmpHid[i].classList.add('oculto');
			}
		}else if(cmpAct.value==valor){
			for(i=0;i<cmpHid.length;i++){
				cmpHid[i].classList.remove('oculto');
			}
		}else{
			for(i=0;i<cmpHid.length;i++){
				cmpHid[i].classList.add('oculto');
				cmpHid[i].value='';
			}
		}
	}else{
		if(!cmpAct){
			for(i=0;i<cmpHid.length;i++){
				cmpHid[i].classList.add('oculto');
			}
		}else if(cmpAct.value==valor){
			for(i=0;i<cmpHid.length;i++){
				cmpHid[i].classList.add('oculto');
				cmpHid[i].value='';
			}
		}else{
			for(i=0;i<cmpHid.length;i++){
				cmpHid[i].classList.remove('oculto');
			}
		}
	}
}
function hidPlan(a,clsCmp){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp+',textarea.'+clsCmp);
	const act=document.getElementById(a);
	if (act.value=='1'||act.value=='3' ){
		for (i = 0; i < ele.length; i++){ 
				enaFie(ele[i],false);
		}
	}else{
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],true);
		}	
	}
}
function hidFieOpt(a,clsCmp){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp+',textarea.'+clsCmp);
	const act=document.getElementById(a);
	if (act.value=='SI'){
		for (i = 0; i < ele.length; i++){ 
				enaFie(ele[i],false);
		}
	}else{
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],true);
		}	
	}
}

function sumPsy1(){
	const   afec1=document.getElementById('psi_validacion5').value;
	const  afec2=document.getElementById('psi_validacion7').value;
	const  afec3=document.getElementById('psi_validacion9').value;
	document.getElementById('psi_validacion14').value=parseInt(afec1)+parseInt(afec2)+parseInt(afec3);
}
/*******************FIN PSICOLOGIA*************************/
/*******************INICIO ATENCION*************************/
function valPyd(act,el){
	const ele = document.getElementById(el);
	if (act.value=='25' && ele.value==2){
		act.value='';
	}
}
function valResol(a,el,path){
	const act = document.getElementById(a);
	const ele = document.getElementById(el);
	if (act.value=='1'){
		ele.value=25;
		ele.disabled = true;
    	ele.required = true;
    	ele.classList.toggle('valido', true);
    	ele.classList.toggle('captura', true);
    	ele.classList.toggle('bloqueo', true);
    	ele.setAttribute('readonly', true);
		selectDepend('letra1','rango1',path);
	}else{
		if(ele.value==25){// if(ele.value==25 || ele.value==18){
			ele.value='';
			ele.disabled = false;
			ele.required = true;
			ele.classList.toggle('valido', true);
    		ele.classList.toggle('bloqueo', false);
			ele.removeAttribute('readonly');
			document.getElementById('rango1').value='';
			document.getElementById('diagnostico1').value='';
		}
	}
}
function enabFert(a,b,c){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const nle = document.querySelectorAll('select.'+c+',input.'+c);
		if(a.value=='SI'){
			for (i=0; i<ele.length;i++) {
				enaFie(ele[i],true);
			}
			for (i=0; i<nle.length;i++) {
				enaFie(nle[i],false);
			}
  		}else{
			for (i=0; i<nle.length;i++) {
				enaFie(nle[i],true);
			}
			for (i=0; i<ele.length;i++) {
				enaFie(ele[i],false);
			}
		}
}
function enabTest(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='NO'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}
function enabEven(a,b,c){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const elm = document.querySelectorAll('select.'+c+',input.'+c);
		if(a.value=='NO'){
			for (i=0; i<ele.length;i++) {
				enaFie(ele[i],true);
			}
			for (i=0; i<elm.length;i++) {
				enaFie(elm[i],true);
			}
  		}else{
			for (i=0; i<ele.length;i++) {
				enaFie(ele[i],false);
			}
		}
}
function cualEven(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='5'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}
/*******************FIN ATENCION*************************/

</script>
</head>
<body Onload="actualizar();">
<?php
	require_once "../libs/gestion.php";
if (!isset($_SESSION["us_sds"])){ die("<script>window.top.location.href = '/';</script>");}

$mod='homes';

$digitadores=opc_sql("SELECT `id_usuario`,nombre FROM `usuarios` 
WHERE`perfil` IN('ADM','AUXHOG','PROFAM','MEDICINA','ENFERMERIA','PSICOLOGIA','NUTRICION','TERAPEUTA','AMBIENTAL','ODONTOLOGIA','AGCAMBIO','AUXRELEVO','PSICLINICOS','PROAPO','EMBERA','GEO','TECNICO','GLINE','TSOCIAL','AUXPPL','AUXPAG','AUXCDC','AUXCOMP') and subred=(SELECT subred FROM usuarios where id_usuario='{$_SESSION['us_sds']}')  ORDER BY 2",$_SESSION['us_sds']);
$perfi=datos_mysql("SELECT perfil as perfil FROM usuarios WHERE id_usuario='{$_SESSION['us_sds']}'");
$perfil = (!$perfi['responseResult']) ? '' : $perfi['responseResult'][0]['perfil'] ;
?>
<form method='post' id='fapp' >
<div class="col-2 menu-filtro" id='<?php echo$mod; ?>-fil'>

	<!-- <div class="campo"><div>Documento Usuario</div><input class="captura"  size=20 id="fusu" name="fusu" OnChange="searPers(this);"></div> -->
	<div class="campo"><div>Codigo del Predio</div><input class="captura" type="number" size=20 id="fpred" name="fpred" OnChange="actualizar();"></div>
  <?php
    $filtro = in_array($perfil, ['ADM', 'SUPHOG','PROAPO','GEO','TECNICO']);
    $enab = $filtro ? '' : 'disabled';
    $rta = '<div class="campo"><div>Colaborador</div>
            <select class="captura" id="fdigita" name="fdigita" onChange="actualizar();" ' . $enab . '>' . $digitadores . '</select>
            </div>';
    echo $rta;
	?>
 </div>
 <div class='col-8 panel' id='<?php echo $mod; ?>'>
      <div class='titulo' > CREACIÓN DE FAMILIAS
		<nav class='menu left' >
			<li class='icono listado' title='Ver Listado' onclick="desplegar(mod+'-lis');" ></li>
			<!-- <li class='icono exportar'      title='Exportar Información General'    Onclick="csv(mod);"></li> -->
			<li class='icono actualizar'    title='Actualizar'      Onclick="actualizar();">
			<li class='icono filtros'    title='Filtros'      Onclick="showFil(mod);">
			<li class='icono lupa' title='Consultar Predio' Onclick="mostrar('predios','pro',event,'','../consultar/consulpred.php',7);">
		</nav>
		<nav class='menu right' >
		<li class='icono ayuda'      title='Necesitas Ayuda'            Onclick=" window.open('https://drive.google.com/drive/folders/1JGd31V_12mh8-l2HkXKcKVlfhxYEkXpA', '_blank');"></li>
            <li class='icono cancelar'      title='Salir'            Onclick="location.href='../main/'"></li>
        </nav>               
      </div>
      <div>
		</div>
		<span class='mensaje' id='<?php echo $mod; ?>-msj' ></span>
     <div class='contenido' id='<?php echo $mod; ?>-lis' ></div>
	 <div class='contenido' id='cmprstss' ></div>
</div>			
		
<div class='load' id='loader' z-index='0' ></div>
</form>
<div class="overlay" id="overlay" onClick="closeModal();">
	<div class="popup" id="popup" z-index="0" onClick="closeModal();">
		<div class="btn-close-popup" id="closePopup" onClick="closeModal();">&times;</div>
		<h3><div class='image' id='<?php echo$mod; ?>-image'></div></h3>
		<h4><div class='message' id='<?php echo$mod; ?>-modal'></div></h4>
	</div>			
</div>
</body>