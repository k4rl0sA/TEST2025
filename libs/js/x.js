function hideCuida(act,clsCmp) {
	const cmpAct=document.getElementById(act);
	const cmps = document.querySelectorAll(`select.${clsCmp}, input.${clsCmp}`);
	if(cmpAct.value=='SI'){
		for(i=0;i<cmps.length;i++){
			enaFie(cmps[i],false);
		}
	}else{
		for(i=0;i<cmps.length;i++){
			enaFie(cmps[i],true);
		}
	}
}

function valSist(a){
	const sis=document.getElementById(a).value;
	if(parseInt(sis)<60 || parseInt(sis)>310){
	warnin('El Valor ingresado en la tension Arterial Sistolica, no cumple con el rango establecido');
	return true;
	}else{
		return false;
	}
}

function valDist(a){
	const dis=document.getElementById(a).value;
	if(parseInt(dis)<40 || parseInt(dis)>185){
	warnin('El Valor ingresado en la tension Arterial Diastolica, no cumple con el rango establecido');
	return true;
	}else{
		return false;
	}
}

function valGluco(a){
	const glu=document.getElementById(a).value;
	if(parseInt(glu)<5 || parseInt(glu)>600){
	warnin('El Valor ingresado en la Glucometria, no cumple con el rango establecido');
	return true;
	}else{
		return false;
	}
}

function valGluc(a){
	const glu=document.getElementById(a);
	if (glu!==null){
	const al1=document.getElementById('cronico').value;
	const ges=document.getElementById('gestante').value;
		if(al1=='1' || ges=='1'){
			enaFie(glu,false);
		}else{
			enaFie(glu,true);
		}
	}
}

function valTalla(a){
	const tal=document.getElementById(a).value;
	if(parseInt(tal)<20 || parseInt(tal)>210){
	warnin('El Valor ingresado en la Talla, no cumple con el rango establecido');
	return true;
	}else{
		return false;
	}
}
function valPeso(a){
	const pes=document.getElementById(a).value;
	if(parseInt(pes)<0.50 || parseInt(pes)>150){
	warnin('El Valor ingresado en el Peso, no cumple con el rango establecido');
	return true;
	}else{
		return false;
	}
}

  function getAge(a) {
	const born = document.getElementById(a);
	const dateBorn = new Date(born.value);
	const now = new Date();
  
	const milSeg = now - dateBorn;
	const age = new Date(milSeg);
  
	const años = age.getUTCFullYear() - 1970;
	const meses = age.getUTCMonth();
	const dias = age.getUTCDate() - 1;
  
	// resultado.textContent = `Edad: ${años} años, ${meses} meses, ${dias} días`;
	return {
		anios: años,
		meses: meses,
		dias: dias
	}

	console.log(anios);
  }
  
function DisableUpdate(act,clsCmp){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp+',#'+act);
	for (i = 0; i < ele.length; i++) {
				enaFie(ele[i],true);
	}
}

function disabledCmp(clsCmp){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp);
	for (i = 0; i < ele.length; i++){ 
		enaFie(ele[i],true);
	}
}

 
  function enabEtni(a, clsCmp, i) {
	const ele = document.querySelectorAll(`select.${clsCmp}, input.${clsCmp}`);
	const idi = document.querySelectorAll(`select.${i}, input.${i}`);
	const act = document.getElementById(a);
	const bloquearCampos = (bloquear) => {
	  for (let j = 0; j < ele.length; j++) {
		if (ele[j].classList.contains(clsCmp)) {
		  enaFie(ele[j], bloquear);
		}
	  }
	};
	const bloquearIdioma = (bloquear) => {
	  for (let j = 0; j < idi.length; j++) {
		enaFie(idi[j], bloquear);
	  }
	};
	bloquearCampos(true); 
	if (act.value === '2') {
	  bloquearCampos(false);
	}
	if (act.value !== '1' && act.value !== '3') {
	  bloquearIdioma(false);
	}
  }
	
function selDep(){

}
  
function enbValue(a,clsCmp,v){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp+',textarea.'+clsCmp);
	const act=document.getElementById(a);
	if (act.value==v){
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],false);
		}
	}else{
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],true);
		}	
	}
}


/* function valSelDep(a,val,clsCmp,v){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp);
	const act=document.getElementById(a);
	if (act.value==val){
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],true);
			ele[i].value=v;
		}
	}else{
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],false);
		}	
	}
} */
 function enbValsCls(a, ClsCmp) {
	const act = document.getElementById(a);
	const numValue = parseInt(act.value, 10);
  
	ClsCmp.forEach(cls => {
	  const elementsToDisable = document.querySelectorAll(`select.${cls}, input.${cls}`);
	  elementsToDisable.forEach(element => {
		enaFie(element, true); 
	  });
	});

	let adjustedIndex = numValue - 1;
  
	if (!isNaN(adjustedIndex) && adjustedIndex >= 0 && adjustedIndex < ClsCmp.length) {
	  const clsToEnable = ClsCmp[adjustedIndex];
	  const elementsToEnable = document.querySelectorAll(`select.${clsToEnable}, input.${clsToEnable}`);
  
	  elementsToEnable.forEach(element => {
		enaFie(element, false);
	  });
	}
  }
 	
function enabEapb(a,clsCmp){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp);
	const act=document.getElementById(a);
	if (act.value!='5'){
		for (i = 0; i < ele.length; i++){ 
				enaFie(ele[i],false);
		}
	}else{
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],true);
		}	
	}
}

function enabAfil(a,clsCmp){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp);
	const act=document.getElementById(a);
	if (act.value=='5'){
		for (i = 0; i < ele.length; i++){ 
				enaFie(ele[i],false);
		}
	}else{
		for (i = 0; i < ele.length; i++){ 
			enaFie(ele[i],true);
		}	
	}
}

function enabFielSele(a, b, c, d) {
	for (i = 0; i < c.length; i++) {
    	var ele = document.getElementById(c[i]);
    	enaFie(ele, !d.includes(a.value) || !b);
  	}
}

function addupd(act,clsCmp,b){
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp);
	const selectedDate = Date.parse(act.value);
	if (act.value!='') {
		if (isNaN(selectedDate)) {
			for (i = 0; i < ele.length; i++) {
				enaFie(ele[i], true);
			}
  		} else {
  			for (i = 0; i < ele.length; i++) {
				enaFie(ele[i], false);
			}
  		}	
	}else{
		for (i = 0; i < ele.length; i++) {
			enaFie(ele[i], true);
		}
	}
}



function enableDog(a,b){
	const ele = document.querySelectorAll('input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='SI'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enableCat(a,b){
	const ele = document.querySelectorAll('input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='SI'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enabLoca(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='SI'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}
// periAbd('gestante','AbD',1)
function periAbd(a,b,c){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='SI' ){
			enaFie(ele[i],true);
		}else if(c!==true){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}



function timeDesem(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value==5){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function disaLoca(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='SI'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}

function tipVivi(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='1' || act.value=='2' || act.value=='3'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enabOthNo(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='2'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}

function disaOthNo(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='2'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enabOthSi(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='1'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enabYes(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	const act=document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		if(act.value=='SI'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}
// Al menos un elemento tiene valor SI
function min1ElmSi(a,ClsCmp) {
	const ele = document.querySelectorAll('select.'+ClsCmp+',input.'+ClsCmp+',textarea.'+ClsCmp);
	const est=document.getElementById('estado_s');
	for (const elm of ele) {
	  if (elm.value === '1' || (est.value==='10' || est.value==='3' || est.value==='5' || est.value==='6'|| est.value==='7'|| est.value==='8'|| est.value==='9')){
		return true;
	  }
	}
	return false;
  }
  

function enabAlert(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='NO'){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}

function ValTensions(a,b){
	const sis=document.getElementById(a).value;
	const dis=b.value;
	if(sis!='' && dis!=''){
		if(Math.floor(dis)>Math.floor(sis)){
			return inform('Recuerde que el valor de la tensión arterial diastolica, no puede ser mayor a la tensión arterial sistolica');
		}
	}
}

function enabDesEsc(a,clsCmp,e){
	const edad=getAge(e.id);
	const ele = document.querySelectorAll('select.'+clsCmp+',input.'+clsCmp);
	const act=document.getElementById(a);
	if((edad['anios']<5 || edad['anios']>17) && act.value=='13'){
		act.value='';
	}else{
		if (act.value=='13'){
			for (i = 0; i < ele.length; i++){ 
					enaFie(ele[i],false);
			}
		}else{
			for (i = 0; i < ele.length; i++){ 
				enaFie(ele[i],true);
			}	
		}
	}
}

/* function EnabEfec(a, b) {
	const clas = b.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	elems = [...document.querySelectorAll(clas)];
  
	elems.forEach(element => {
	  const flag = (a.value !== '1');
	  enaFie(element, flag);
	});
  } */

  function child14(a,b){
	rta =getAge(a);
	ano=rta['anios'];
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(ano<14){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}

function Ocup5(a,b){
	rta =getAge(a);
	ano=rta['anios'];
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(ano<6){
			enaFie(ele[i],true);
  		}else{
			enaFie(ele[i],false);
		}
	}
}

function EditOcup(a,b){
	rta =getAge('fecha_nacimiento');
	ano=rta['anios'];
	let el=document.getElementById(a);
	if(ano<6){
		lockeds(el,true);
	  }else{
		lockeds(el, false);
	}
}

function staEfe(a,b){
	const act=document.getElementById(a);
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
	for (i=0; i<ele.length;i++) {
		if(act.value=='1'){
			enaFie(ele[i],true);
			ele[i].value='1';
  		}else{
			enaFie(ele[i],false);
			ele[i].value='';
		}
	}
}

function enabEtap(a, b) {
	const act = document.getElementById(a);
  
	const selector = b.map(clase => `select.${clase}, input.${clase}, textarea.${clase}`).join(', ');
  
	const elementos = [...document.querySelectorAll(selector)];
  
	elementos.forEach(elemento => {
	  const valorA = parseInt(act.value);
	  let habilitar = false;
  
	  switch (valorA) {
		case 1:
			habilitar = elemento.classList.contains('PuE');
		  break;
		case 2:
			habilitar = elemento.classList.contains('pRe');
		  break;
		case 3:
			habilitar = elemento.classList.contains('pRe');
		  break;
	  }
  
	  enaFie(elemento, habilitar);
	});
  }

  function enabClasValu(a, b) {
    const act = document.getElementById(a);
    const selector = b.map(clase => `select.${clase}, input.${clase}, textarea.${clase}`).join(', ');
    const elementos = [...document.querySelectorAll(selector)];
    elementos.forEach(elemento => {
        const valorA = parseInt(act.value);
        let bloquea = true; 

        switch (valorA) {
            case 1:
                if (elemento.classList.contains('mOr') || elemento.classList.contains('NOm')) {
                    bloquea = false;
                }
                break;
            case 2:
                if (elemento.classList.contains('mOr')) {
                    bloquea = false;
                } else {
                    bloquea = true;
                }
                break;
            default:
                if (elemento.classList.contains('mOr') || elemento.classList.contains('NOm')) {
                    bloquea = true;
                }
                break;
        }
        enaFie(elemento, bloquea);
    });
}


function enClSeDe(ac,act, clin, claf){
	if (document.getElementById(ac).value==3) enClSe(act, clin, claf);
}

function enClSe(act, clin, claf) {
    const ac = document.getElementById(act);
    const els = [...document.querySelectorAll(`select.${clin}, input.${clin}, textarea.${clin}`)];
    const valor = parseInt(ac.value);

    els.forEach(elm => {
        const index = Math.min(valor - 1, claf.length - 1);
        const clase = claf[index][0];

        const bloquea = elm.classList.contains(clase);
        enaFie(elm,!bloquea);
    });
}

//enClSe('accion', 'tOL', [['mOr'], ['NOm'], ['ANr']]);


function weksEtap(a,b){
	const act = document.getElementById(a);
	const ele = document.querySelectorAll('select.'+b+',input.'+b);
		if(act.value=='3'){
			enaFie(ele[0],true);
			ele[0].value='43';
  		}else{
			enaFie(ele[0],false);
		}
  }


function Zsco(a,b='../vivienda/medidas.php'){
    // doc=a.split('_');
	const glu=document.getElementById(a);
	if (glu!==null){
	const pes=document.getElementById('peso').value;
	const fec=document.getElementById('fechanacimiento').value;
	const sex=document.getElementById('sexo').value;
	const tal=document.getElementById('talla').value;

	if (loader !== undefined) loader.style.display = 'block';
		if (window.XMLHttpRequest)
			xmlhttp = new XMLHttpRequest();
		else
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			xmlhttp.onreadystatechange = function () {
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
				data =xmlhttp.responseText;
				if (loader != undefined) loader.style.display = 'none';
					console.log(data)
			}}
			xmlhttp.open("POST",b,false);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send('a=get&tb=zscore&val='+pes+'_'+fec+'_'+sex+'_'+tal);
			var rta =data;
			if(b=='../vivienda/medidas.php'){
				glu.value=JSON.parse(rta);
			}else{
				val=JSON.parse(rta);
				document.getElementById('zscore').value=val;
			}
			
		}
}

/* async function searchUsu(a,b,c){
	try {
		const id=a.value;
		const rta = await getJSON('opc','usuario',id);
	} catch (error) {
		errors();
		console.error("Error al ejecutar la función", error);
	}
} */



  async function searchUsu(a) {
	try {
	  const info = a.value;
	  console.log("Datos a enviar al servidor:", info);
  
	  const data = await getJSON("opc", "usuario", info);
	  console.log("Datos recibidos del servidor:", data);
  
	  // Resto del código...
	} catch (error) {
	  console.error(error);
	  errors("No se pudo realizar la Busqueda.");	}
  }
  
  

  function handleResponse(responseData) {
	const resultadoElement = document.getElementById("resultado");
	const errorMessageElement = document.getElementById("error-message");
	if (responseData && responseData.sector_catastral) {
	  ok(`Sector Catastral: ${responseData.sector_catastral}, NumManzana: ${responseData.nummanzana}, Predio Num: ${responseData.predio_num}, Unidad Habit: ${responseData.unidad_habit}`);
	} else {
		warnin("No se encontraron resultados.");
	}
  }

/*************************INICIO TAMIZAJES*************************************/
function hiddxTamiz(a, b,e) {
	const cmpAct = document.getElementById(a);
	const cmps = document.querySelectorAll(`.${b}`);
	const edad = parseInt(cmpAct.value) > e;
  
	for (let i = 0; i < cmps.length; i++) {
		hidFie(cmps[i], true);
	  }
	for (let i = 0; i < cmps.length; i++) {
	  hidFie(cmps[i], !edad);
	}
  }
  
  function TamizxApgar(a) {
	const cmpAct = document.getElementById(a);
	const men = document.querySelectorAll('.cuestionario1');
	const may = document.querySelectorAll('.cuestionario2');
	const edad = parseInt(cmpAct.value);
  
	for (let i = 0; i < men.length; i++) {
	  hidFie(men[i], true);
	}
	for (let i = 0; i < may.length; i++) {
	  hidFie(may[i], true);
	}
	if (edad > 17) {
	  for (let i = 0; i < may.length; i++) {
		hidFie(may[i], false);
	  }
	} else if (edad > 6 && edad < 18) {
	  for (let i = 0; i < men.length; i++) {
		hidFie(men[i], false);
	  }
	}
  }

  function hiddxdiab(diab,cls) {
	const dbts=document.getElementById(diab);
	const cmpHid1 = document.querySelectorAll(`.${cls}`);
	if(dbts.value== 1 ){
		for(i=0;i<cmpHid1.length;i++){
			hidFie(cmpHid1[i],true);
		}
	}else{
		for(i=0;i<cmpHid1.length;i++){
			hidFie(cmpHid1[i],false);
		}
	}
}

/*************************FIN TAMIZAJES*************************************/
  



function ZscoAte(a){
	const sco=document.getElementById('dxnutricional');
	const anos=getAge('fecha_nacimiento');
	if (sco!==null || anos['anios']>4){
	const pes=document.getElementById('atencion_peso').value;
	const fec=document.getElementById('fecha_nacimiento').value;
	const sex=document.getElementById('sexo').value;
	const tal=document.getElementById('atencion_talla').value;

	if (loader !== undefined) loader.style.display = 'block';
		if (window.XMLHttpRequest)
			xmlhttp = new XMLHttpRequest();
		else
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			xmlhttp.onreadystatechange = function () {
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
				data =xmlhttp.responseText;
				if (loader != undefined) loader.style.display = 'none';
					console.log(data)
			}}
			xmlhttp.open("POST",'lib.php',false);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send('a=get&tb=zscore&val='+pes+'_'+fec+'_'+sex+'_'+tal);
			var rta =data;
			sco.value=JSON.parse(rta);
	}
}


//EnabEfec(this,['hab','acc'],['Ob'],['nO'],['bL']);
function EnabEfec(a,b,c,d,e) {
	const clas = b.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cls = c.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cla = d.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cl = e.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');

	elems = [...document.querySelectorAll(clas)];
	el = [...document.querySelectorAll(cla)];
	ele = [...document.querySelectorAll(cls)];
	elm = [...document.querySelectorAll(cl)];

	elems.forEach(element => {
		const flag = (a.value >2);
		enaFie(element, flag);
	});

	//no obligatorio
	el.forEach(elm => {
		const flag = true;
		if(elm.classList.contains('nO')){
			noRequired(elm, flag);
		}
	});

	//obligatorio
		ele.forEach(el => {
			const flag = false;
			if(el.classList.contains('Ob')){
				enaFie(el,flag);
			}
		});

	//bloqueados
	elm.forEach(elms => {
		const flag = true;
		if(elms.classList.contains('bL')){
			lockeds(elms,flag);
		}
	});

	if(a.value === '1'){
		enaFie(document.getElementById('condi_diag'),false);
	}else{
		enaFie(document.getElementById('condi_diag'),true);
	}
	
}

function EnabCron(a,b,c,d,e) {
	const clas = b.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cls = c.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cla = d.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cl = e.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');

	elems = [...document.querySelectorAll(clas)];
	el = [...document.querySelectorAll(cla)];
	ele = [...document.querySelectorAll(cls)];
	elm = [...document.querySelectorAll(cl)];

	if(a.value!=1){

	elems.forEach(element => {
		const flag = (a.value !== '1');
		enaFie(element, flag);
	});

	//no obligatorio
	el.forEach(elm => {
		const flag = true;
		if(elm.classList.contains('nO')){
			noRequired(elm, flag);
		}
	});

	//obligatorio
		ele.forEach(el => {
			const flag = false;
			if(el.classList.contains('Ob')){
				enaFie(el,flag);
			}
		});

	//bloqueados
	elm.forEach(elms => {
		const flag = true;
		if(elms.classList.contains('bL')){
			lockeds(elms,flag);
		}
	});
		enaFie(a,false);
		a.value=2;	
	}else{
		elems.forEach(element => {
			const flag = (a.value === '1');
			enaFie(element, flag);
		});
	
		//no obligatorio
		el.forEach(elm => {
			const flag = true;
			if(elm.classList.contains('nO')){
				noRequired(elm, flag);
			}
		});
	
		//obligatorio
			ele.forEach(el => {
				const flag = false;
				if(el.classList.contains('Ob')){
					enaFie(el,flag);
				}
			});
	
		//bloqueados
		elm.forEach(elms => {
			const flag = true;
			if(elms.classList.contains('bL')){
				lockeds(elms,flag);
			}
		});

		enaFie(a,false);
		a.value=1;
	}
}


function enabRuta(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b+',textarea.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='1'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enabCovid(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b+',textarea.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='1'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}

function enabFincas(a,b){
	const ele = document.querySelectorAll('select.'+b+',input.'+b+',textarea.'+b);
	for (i=0; i<ele.length;i++) {
		if(a.value=='1'){
			enaFie(ele[i],false);
  		}else{
			enaFie(ele[i],true);
		}
	}
}


function stateVisit(a, b,c) {
	const clas = b.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');
	const cls = c.map(clase => `select.${clase}, input.${clase},textarea.${clase}`).join(', ');

	elems = [...document.querySelectorAll(clas)];
	ele = [...document.querySelectorAll(cls)];

	elems.forEach(element => {
	  const flag = (a.value !== '5');
	  enaFie(element, flag);
	});

	ele.forEach(el => {
		const flag = true;
		if(el.classList.contains('ne')){
			noRequired(el, flag);
		}else{
			enaFie(el,flag);
		}
		
	  });
  }

  
function enabDepeValu(a,b,c,d){
	const ele = document.querySelectorAll('select.'+b+',input.'+b+',textarea.'+b);
	const act = document.getElementById(a);
	for (i=0; i<ele.length;i++) {
		for (j=0; j<c.length;j++) {
			if(act.value==c[j]){
				enaFie(ele[i],d);
				break;
  			}else{
				enaFie(ele[i],!d);
			}
		}
	}
}

function enabDepeInner(a, b, c) {
    const ele = document.querySelectorAll('select.' + b + ', input.' + b + ', textarea.' + b);
    const act = document.getElementById(a);
    const options = act.querySelectorAll('option');
    const selectedValues = Array.from(options).map(option => option.innerText);
    for (let i = 0; i < ele.length; i++) {
        let enableElement = false;
        for (let j = 0; j < c.length; j++) {
            if (selectedValues.includes(c[j])) {
                enableElement = true;
                break;
            }
        }
        enaFie(ele[i], enableElement);
    }
}

function enabStatPreAdm(a, b,c=true) {
	var ele = document.getElementById(b);
	if(a.value==3 || a.value==6 && c===true){
		enaFie(ele,false);
	}else{
		enaFie(ele,true);
	}
	if(a.value==6){
		const c = document.querySelectorAll('.dir input');
		for (i = 0; i < c.length; i++) {
			var el = document.getElementById(c[i].id);
			enaFie(el, false);		
		}
	}else{
		const d = document.querySelectorAll('.dir input');
		for (i = 0; i < d.length; i++) {
			var e = document.getElementById(d[i].id);
			enaFie(e, true);		
		}
	}
}

function valTel(a){
const ele=document.getElementById(a).value;
if (ele.length!=7 && ele.length!=10){
		warnin('El Valor ingresado en el número de telefono, no es valido');
		return true;
	}else{
		return false;
	}
}

function enabSelMulSel(act, sel, val) {
    const selMul = document.getElementById(act);
    const selSim = document.getElementById(sel);
	let selectedOptions = Array.from(selMul.selectedOptions).map(option => option.value);
	let shouldBeEnabled = selectedOptions.some(option => val.includes(option));
	if(shouldBeEnabled){
		selSim.value=1;
		enabOthSi('deriva_eac','eAc');
	}else{
		selSim.value=2;
		enabOthSi('deriva_eac','eAc');
	}
}

// Función para habilitar/deshabilitar campos dependiendo de un valor
function fieldsValue(a, b, value, flag = true) {
    const elem = document.querySelectorAll('select.' + b + ',input.' + b + ',textarea.' + b);
    const act = document.getElementById(a);
    
    for (let i = 0; i < elem.length; i++) {
        // Si el valor del activador coincide con el valor esperado
        if (act.value == value) {
            enaFie(elem[i], !flag); // Si flag es true, habilita los campos, si es false, los deshabilita
        } else {
            enaFie(elem[i], flag);  // Caso contrario, invierte la acción
        }
    }
}

function EnabDepe2fiel(cls, a, b, fa, fb, cmpA = true, cmpB = true) {
    const eleA = document.getElementById(fa);
    const eleB = document.getElementById(fb);
    
    // Verificar las condiciones con base en los parámetros compareA y compareB
    const isNSegVal = cmpA ? eleA.value === a : eleA.value !== a;
    const isEtpVal = cmpB ? eleB.value === b : eleB.value !== b;
    
    // Obtener todos los elementos de la clase
    const elements = document.querySelectorAll('select.' + cls + ',input.' + cls + ',textarea.' + cls);
    
    // Activar o desactivar los elementos según las condiciones
    elements.forEach(element => {
        enaFie(element, !(isNSegVal && isEtpVal));
    });
}
/* function EnabDepeDynamic(cls, conditions) {
	const allConditionsMet = conditions.every(condition => {
		const element = document.getElementById(condition.id);
		if (!element) {
			console.error(`Elemento con ID ${condition.id} no encontrado.`);
			return false;
		}
		return condition.compare ? element.value === condition.value : element.value !== condition.value;
	});
	const elements = document.querySelectorAll(`select.${cls}, input.${cls}, textarea.${cls}`);
	elements.forEach(element => {
		enaFie(element, !allConditionsMet);
	});
} */
function EnabDepeDynamic(classes, conditions) {
	const allConditionsMet = conditions.every(condition => {
		const element = document.getElementById(condition.id);
		if (!element) {
			console.error(`Elemento con ID ${condition.id} no encontrado.`);
			return false;
		}
		return condition.compare ? element.value === condition.value : element.value !== condition.value;
	});
	const selector = classes.map(cls => `select.${cls}, input.${cls}, textarea.${cls}`).join(', ');
	const elements = document.querySelectorAll(selector);
	elements.forEach(element => {
		enaFie(element, !allConditionsMet);
	});
}
function valiEgreHosp() {
	const conditions = [
		{ id: 'numsegui', value: '1', compare: true },
		{ id: 'tiposeg', value: '2', compare: true },
		{ id: 'etapa', value: '1', compare: true }
	];
	EnabDepeDynamic(['HOs'], conditions);
}
function enabPrioEtn() {
	const prioridad = document.getElementById('prioridad').value;
	const enabledClasses = {};
	const conditions = [
		{ value: '1', classes: ['mE5'] },
		{ value: '2', classes: ['GEs'] },
		{ value: '3', classes: ['CrO'] },
		{ value: '16', classes: ['CrO', 'GEs'] },
		{ value: '17', classes: ['CrO', 'mE5'] },
		{ value: '19', classes: ['GEs'] },
		{ value: '13', classes: ['mE5'] },
		{ value: '14', classes: ['mE5'] },
		{ value: '7', classes: ['mE5'] },
		{ value: '8', classes: ['mE5'] },
		{ value: '9', classes: ['mE5'] },
		{ value: '27', classes: ['mE5'] },
	];
	
	
	conditions.forEach(condition => {
		const isConditionMet = prioridad === condition.value;
		if (isConditionMet) {
			condition.classes.forEach(cls => {
				enabledClasses[cls] = true;
			});
		}
	});
	conditions.forEach(condition => {
		condition.classes.forEach(cls => {
			const selector = `select.${cls}, input.${cls}, textarea.${cls}`;
			const elements = document.querySelectorAll(selector);
			elements.forEach(element => {
				enaFie(element, !enabledClasses[cls]);
			});
		});
	});
}
function enCroGes(){
	const prioridad = document.getElementById('prioridad').value;
	const enabledClasses = {};
	const conditions = [
		{ value: '2', classes: ['GlU'] },
		{ value: '3', classes: ['GlU'] },
		{ value: '16', classes: ['GlU'] },
		{ value: '17', classes: ['GlU'] },
		{ value: '19', classes: ['GlU'] }
	];
	conditions.forEach(condition => {
		const isConditionMet = prioridad === condition.value;
		if (isConditionMet) {
			condition.classes.forEach(cls => {
				enabledClasses[cls] = true;
			});
		}
	});
	conditions.forEach(condition => {
		condition.classes.forEach(cls => {
			const selector = `select.${cls}, input.${cls}, textarea.${cls}`;
			const elements = document.querySelectorAll(selector);
			elements.forEach(element => {
				enaFie(element, !enabledClasses[cls]);
			});
		});
	});
}

function diagCroEtn() {
	const conditions = [
		{ id: 'diag_cronico', value: '4', compare: true }
	];
	EnabDepeDynamic(['dAG'], conditions);
}

function ftlc() {
	const conditions = [
		{ id: 'ftlc_apme', value: '1', compare: true }
	];
	EnabDepeDynamic(['Ftl'], conditions);
}

function enabNV() {
	const conditions = [
		{ id: 'resul_gest', value: '1', compare: true }
	];
	EnabDepeDynamic(['Nav'], conditions);
}
function enabSegEmb() {
	const prioridad = document.getElementById('interven').value;
	const enabledClasses = {};
	const conditions = [
		{ value: '1', classes: ['datiden','infoserv','detsegh']},
		{ value: '2', classes: ['detsegp']},
		
	];
	conditions.forEach(condition => {
		const isConditionMet = prioridad === condition.value;
		if (isConditionMet) {
			condition.classes.forEach(cls => {
				enabledClasses[cls] = true;
			});
		}
	});
	conditions.forEach(condition => {
		condition.classes.forEach(cls => {
			const selector = `select.${cls}, input.${cls}, textarea.${cls}`;
			const elements = document.querySelectorAll(selector);
			elements.forEach(element => {
				enaFie(element, !enabledClasses[cls]);
			});
		});
	});
}
function enabEmbInt(){
	const conditions = [
		{ id: 'estado_seg', value: '1', compare: true }
	];
	EnabDepeDynamic(['iNt'], conditions);
}
function enabEmbGes(){
	const conditions = [
		{ id: 'sexo', value: 'M', compare: true },
		{ id: 'interven', value: '1', compare: true }
	];
	EnabDepeDynamic(['GeS'], conditions);
}
function enabEmbPare(a){
	const conditions = [
		{ id: a, value: '8', compare: false },
	];
	EnabDepeDynamic(['prT'], conditions);
}
function enabEmbEdGes(){
	const conditions = [
		{ id: 'gestante', value: '1', compare: true }
	];
	EnabDepeDynamic(['EGe'], conditions);
}
function enabEspe2(){
	const inte=document.getElementById('interven').value;
	if(inte=='2'){
		const ele=document.getElementById('espe2');
		noRequired(ele,true);
		NoObligat(ele,true);
	}	
}
function noObliFecEtni(){
		const ele=document.getElementById('fecha_obs');
		NoObligat(ele,true);
}
function NoObligat(a,flag){
	a.classList.toggle('valido', !flag);
    a.classList.toggle('captura', flag);
    a.classList.toggle('bloqueo', !flag);
    flag ? a.removeAttribute('readonly'): a.setAttribute('readonly', true);
	flag ? a.removeAttribute('disabled'): a.setAttribute('disabled', true);
}
function enabRutGest(){
	const conditions = [
		{ id: 'estado_llamada', value: '1', compare: true },
	];
	EnabDepeDynamic(['sTA'], conditions);
}
function enabRutOthSub() {
    const element = document.getElementById('estado_agenda');
    if (!element) {
        console.error('Elemento con ID estado_agenda no encontrado.');
        return;
    }
    const conditionMet = element.value === '4' || element.value === '7';
    const elements = document.querySelectorAll('select.dir, input.dir, textarea.dir');
    elements.forEach(element => {
        enaFie(element, !conditionMet);
    });
}
function enabRutAgen() {
	const conditions = [
		{ id: 'estado_agenda', value: '1', compare: true }
	];
EnabDepeDynamic(['AGe'], conditions);
}
/* function enabRutAgen2() {
	const estadoAgenda = document.getElementById('estado_agenda');
	const isEnabled = estadoAgenda && (estadoAgenda.value === '1' || estadoAgenda.value === '11');
	const elements = document.querySelectorAll('select.AGe, input.AGe, textarea.AGe');
	elements.forEach(element => {
		enaFie(element, !isEnabled);
	});
} */
function enabRutRech() {
	const conditions = [
		{ id: 'estado_agenda', value: '2', compare: true }
	];
	EnabDepeDynamic(['ReC'], conditions);
}
function enabEmbRGes() {
	const conditions = [
		{ id: 'edad_gest', value: '44', compare: true }
	];
	EnabDepeDynamic(['Pue'], conditions);
}
function enabEmbPRg() {
	const conditions = [
		{ id: 'edad_gest', value: '44', compare: false }
	];
	EnabDepeDynamic(['PrG'], conditions);
}
function enabEmbSif() {
	const conditions = [
		{ id: 'diag_sifigest', value: '1', compare: true }
	];
	EnabDepeDynamic(['SiF'], conditions);
}
function rutRiskHig(){
	const conditions = [
		{ id: 'riesgo', value: '1', compare: true }
	];
	EnabDepeDynamic(['alto'], conditions);
}
function rutRisklow(){
	const conditions = [
		{ id: 'riesgo', value: '2', compare: true }
	];
	EnabDepeDynamic(['bajo'], conditions);
}
function rutRute(){
	const conditions = [
		{ id: 'activa_ruta', value: '1', compare: true }
	];
	EnabDepeDynamic(['ruta'], conditions);
}
function enabRutVisit() {
	const est = document.getElementById('estado_llamada');
	const agen = document.getElementById('estado_agenda');
	if (est.value === "5" || est.value === "6") {
		agen.value = 1;
		const conditions = [
			{ id: 'estado_llamada', value: '5', compare: true },
			{ id: 'estado_llamada', value: '6', compare: true }
		];
		// Habilita los campos de la clase 'AGe' si estado_llamada es 5 o 6
		EnabDepeDynamic(['AGe'], [
			{ id: 'estado_llamada', value: est.value, compare: true }
		]);
	}else{
		EnabDepeDynamic(['AGe'], [
			{ id: 'estado_llamada', value: est.value, compare: false }
		]);
	}
}

function EnabFall(){
		const conditions = [
		{ id: 'estado_seg', value: '1', compare: true }
	];
	EnabDepeDynamic(['ges','cronicos','menor5','signosV','antrop'], conditions);
}
function custSeleDepend(a, b, c = ruta_app, extraParams = {}) {
    try {
        const originSelect = document.getElementById(a);
        const targetSelect = document.getElementById(b);
        const idpElement = document.getElementById('idp');
        if (!originSelect || !targetSelect || !idpElement) {
            console.error('Elementos requeridos no encontrados');
            return;
        }
        targetSelect.innerHTML = '';
        targetSelect.add(new Option('SELECCIONE', ''));
        const selectedValue = originSelect.value;
        if (!selectedValue) return;
        const idPersona = idpElement.value;
        if (!idPersona) {
            console.error('ID no disponible');
            targetSelect.add(new Option('ID requerido', ''));
            return;
        }
        const idCompuesto = `${idPersona}_${selectedValue}`;
        const params = new URLSearchParams();
        params.append('a', 'opc');
        params.append('tb', `${a}${b}`);
        params.append('id', idCompuesto);
        Object.entries(extraParams).forEach(([key, elementId]) => {
            const element = document.getElementById(elementId);
            if (element?.value) params.append(key, element.value);
        });
        fetch(c, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data)) throw new Error('Respuesta no es un array');
            targetSelect.innerHTML = '';
            targetSelect.add(new Option('SELECCIONE', ''));
            data.forEach(item => {
                targetSelect.add(new Option(
                    item.descripcion || item.text || '',
                    item.idcatadeta || item.value || ''
                ));
            });
        })
        .catch(error => {
            console.error('Error:', error);
            targetSelect.innerHTML = '';
            targetSelect.add(new Option('Error al cargar', ''));
        });
    } catch (error) {
        console.error('Error en custSeleDepend:', error);
    }
}

function stateRutEnd(){
	const conditions = [
		{ id: 'est', value: '1', compare: true }
	];
	EnabDepeDynamic(['RuE'], conditions);
}
function stateRutFam(){
	const conditions = [
		{ id: 'estado', value: '3', compare: true }
	];
	EnabDepeDynamic(['StG'], conditions);
}
function validarPorTexto(selectElement) {
    const textoSeleccionado = selectElement.options[selectElement.selectedIndex].text;
    const habilitar = textoSeleccionado === "EFECTIVA";
    const elementosStG = document.querySelectorAll('select.StG, input.StG');
    elementosStG.forEach(elemento => {
        enaFie(elemento, !habilitar);
        const divPadre = elemento.closest('div.StG');
        if (divPadre) {
            if (habilitar) {
                divPadre.classList.remove('bloqueo');
            } else {
                divPadre.classList.add('bloqueo');
            }
        }
    });
}