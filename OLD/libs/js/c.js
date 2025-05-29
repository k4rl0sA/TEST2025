var rgxtxt="[a-zA-Z0-9]{0,99}";
var rgxdfnum = "[0-9]{0,18}";
var rgxmail = "^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$";
var rgxphone = "[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]";
var rgxdatehms = "([12][0-9][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0123]):([0-5][0-9]):([0-5][0-9])";
var rgxdatehm = "([12][0-9][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]) ([01][0-9]|2[0123]):([0-5][0-9])";
var rgxdate = "([12][0-9][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])";
var rgxtime = "([01][0-9]|2[0123]):([0-5][0-9])";
var rgxl3 = "([A-Z])([A-Z])([A-Z])";
var rgxn3 = "([0-9])([0-9])([0-9])";
var rgxn4 = "([0-9])([0-9])([0-9])";
document.addEventListener('contextmenu', function (ev) {
//	ev.target.title=ev.target.parentElement.id;
//   window.parent.document.getElementById('btn-oferta').click();  
//   window.parent.document.getElementById('oferta').contentWindow.document.getElementById('oferta')=23456;       
	//ev.preventDefault();
}, false);
document.onkeyup = function (ev) {
	ev = ev || window.event;
	ev.target.style.background = '';
	var panel = ev.target.id.replace('[]', '').split('-');
	if (panel[0] != undefined) {
		if (panel[1] == 'tab' && ev.altKey && ev.ctrlKey && ev.keyCode == 78)
			document.getElementById('btn-crear-' + panel[0]).click();
		if (panel[1] == 'tab' && ev.altKey && ev.ctrlKey && ev.keyCode == 67)
			document.getElementById('btn-oculta-' + panel[0] + '-tab').click();
		if (panel[1] == 'cap' && ev.keyCode == 13)
			document.getElementById('btn-grabar-' + panel[0]).click();
		if (panel[1] == 'cap' && ev.keyCode == 45)
			document.getElementById('btn-crear-' + panel[0]).click();//20180420JGOR
		if (panel[1] == 'cap' && ev.altKey && ev.ctrlKey && ev.keyCode == 71)
			document.getElementById('btn-grabar-' + panel[0]).click();
		if (panel[1] == 'cap' && ev.altKey && ev.ctrlKey && ev.keyCode == 78)
			document.getElementById('btn-crear-' + panel[0]).click();
		if (panel[1] == 'cap' && ev.altKey && ev.ctrlKey && ev.keyCode == 67)
			document.getElementById('btn-oculta-' + panel[0] + '-cap').click();
		if (panel[1] == 'cap' && ev.altKey && ev.ctrlKey && ev.keyCode == 80)
			document.getElementById('btn-imprimir-' + panel[0]).click();
		if (panel[1] == 'cap')
			document.getElementById(panel.join('-') + '-error').innerHTML = "";
	}
};
document.onmouseover = function (ev) {
	ev = ev || window.event;
	if (ev.target.nodeName == 'TD')
		ev.target.classList.add('actual');
	if (ev.target.nodeName == 'TH')
		ev.target.classList.add('actual');
	if (ev.target.nodeName == 'LI')
		ev.target.classList.add('actual');
	if (ev.target.classList.contains('campo'))
		ev.target.classList.add('actual');
};
document.onmouseout = function (ev) {
	ev = ev || window.event;
	if (ev.target.nodeName == 'TD')
		ev.target.classList.remove('actual');
	if (ev.target.nodeName == 'TH')
		ev.target.classList.remove('actual');
	if (ev.target.nodeName == 'LI')
		ev.target.classList.remove('actual');
	if (ev.target.classList.contains('campo'))
		ev.target.classList.remove('actual');
};
document.onclick = function (ev) {
	ev = ev || window.event;
	if (ev.target.nodeName == 'TD') {
		var a = ev.target.parentNode.id.split('-');
		act_actual(a[0], a[1]);
	}
	if (ev.target.classList.contains('titulo')) {
		var a = ev.target.id.split('-');
		sobreponer(a[0], a[1]);
	}
	if (ev.target.classList.contains('contenido')) {
		var a = ev.target.id.split('-');
		sobreponer(a[0], a[1]);
	}
	if (ev.target.classList.contains('filtro')) {
		var a = ev.target.id.split('-');
		sobreponer(a[0], 'tab', ev.target);
	}
	if (ev.target.classList.contains('captura')) {
		var a = ev.target.id.split('-');
		sobreponer(a[0], 'cap', ev.target);
	}
	if (ev.target.classList.contains('auditoria')) {
		var a = ev.target.id.split('-');
		param_tab['auditoria'] = '&auditoria-tab-objeto=' + a[0];
		param_tab['auditoria'] += '&auditoria-tab-campo=' + a[1];
		param_tab['auditoria'] += '&auditoria-tab-indice=' + valor(a[0] + '-cap-' + a[0] + '_id');
		mostrar('auditoria', 'tab');
	}
};
function valor(a, b) {
	var x = document.getElementById(a);
	if (x == undefined)
		var x = parent.document.getElementById(a);
	if (b != undefined)
		x.value = b;
	if (x != undefined) {
		if (x.value == '')
			return x.value;
		if (!isNaN(x.value))
			return parseInt(x.value);
		else
			return x.value;
	}
}

function is_option(a) {
	for (var i = 0; i < a.list.options.length; i++)
		if (a.list.options[i].value == a.value)
			return true;
	return false;
}

function valido(a) {
    a.classList.remove('alerta','invalid');
	if (a.value == '') a.classList.add('alerta','invalid');
	if (a.list != undefined && a.value != '' && !is_option(a)) a.classList.add('alerta','invalid');
	if (a.type=='date' || a.type=='time' || a.type=='datetime-local' || a.type=='datetime' ){ if ((a.min!='' || a.max!='') && (a.value<a.min || a.value>a.max)){ alert('El valor del campo debe ser igual o Posterior a ('+a.min+') ó igual o Inferior a ('+a.max+'), por favor valide para continuar.');a.classList.add('alerta','invalid');}}
	if (!a.classList.contains('alerta','invalid')) return true;
	else return false;
}
function pajax(path, data, callback, method = "POST", headers = null) {
	var req = new XMLHttpRequest();
	a = document.getElementById('loader');
	if (a != undefined)
		a.style.display = 'block';
	req.onreadystatechange = function () {
		if (this.readyState === 4) {
			if (this.status === 200) {
				try {
					callback.apply(this);
				} catch (e) {
					console.log(e);
				}
			}
			a = document.getElementById('loader');
			if (a != undefined)
				a.style.display = 'none';
		}
	};
	req.open(method, path);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	for (var i in headers) {
		req.setRequestHeader(i, headers[i]);
	}
	req.send(data);
}
function ajax(b, c, d) {//b=url,c=query,d=asyncron
	var a;
	if (window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest();
	else
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttp.onreadystatechange = function () {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
			a = xmlhttp.responseText;
			console.log(a)
		}
	}
	xmlhttp.open("POST", b, d);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(c + form_input('fapp'));
	return a;
}
function act_html(a, b, c, d = false) {
	if (document.getElementById(a) != undefined) {
		pajax(b, c + form_input('fapp'), function () {
			document.getElementById(a).innerHTML = this.responseText;
			if (d != false)
				d.apply('a');
		});
}
}
function act_html1(a, b, c, d) { //a=tag,b=url,c=query,d=asyncron
	if (window.XMLHttpRequest)
		xmlhttp = new XMLHttpRequest();
	else
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttp.onreadystatechange = function () {
		if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
			// console.log(this.responseText);
			if (document.getElementById(a) != undefined)
				document.getElementById(a).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", b, d);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(c + form_input('fapp'));
}
function form_input(a) {
	var d = "";
	var frm = document.getElementById(a);
	for (i = 0; i < frm.elements.length; i++) {
		if (frm.elements[i].tagName = "select" && frm.elements[i].multiple) {
			var vl = [];
			for (var o = 0; o < frm.elements[i].options.length; o++) {
				if (frm.elements[i].options[o].selected) {
					vl.push("'" + frm.elements[i].options[o].value + "'");
				}
			}
			d += "&" + frm.elements[i].id + "=" + vl.join(",");
		} else {
			d += "&" + frm.elements[i].id + "=" + frm.elements[i].value.toString();
		}
	}
	return d;
}
function sel_actual(a, b) {
	if (b == '1')
		a.classList.add('actual');
	else
		a.classList.remove('actual');
}
function gra_actual(tb) {
	var frm = document.getElementsByClassName('captura');
	for (i = 0; i < frm.length; i++) {
		if (frm[i].id.indexOf(tb) != -1) {
			if (document.getElementById(frm[i].id + '-error') != undefined) {
				document.getElementById(frm[i].id + '-error').innerHTML = "";
			}
			if (!valide_campo(frm[i]))
				return false;
		}
	}
	var x = ajax(ruta_app, 'tb=' + tb + '&a=gra', false);
	if (document.getElementById(tb + '-cap-msj') != undefined)
		document.getElementById(tb + '-cap-msj').innerHTML = x;
	act_lista(tb);
	return x;
}
function can_actual(tb, a, b) {
	can_children(tb, 'cap');
	if (document.getElementById(tb + '-cap') != undefined)
		document.getElementById(tb + '-cap-con').innerHTML = "";
	document.getElementById(tb + '-cap').style.display = "none";
	act_lista(tb);
}
function act_lista(tb, b, lib = ruta_app) {
	if (document.getElementById(tb + '-msj') != undefined)
		valor(tb + '-msj', '...');
	if (document.getElementById(tb + '-lis') != undefined)
		act_html(tb + '-lis', lib, 'tb=' + tb + '&a=lis', false);
	if (document.getElementById(tb + '-tot') != undefined)
		act_html(tb + '-tot', lib, 'tb=' + tb + '&a=tot', false);
	if (document.getElementById('indicador-indicador') != undefined)
		if (document.getElementById('grafica_gra') != undefined)
			graficar();
	if (parent.document.getElementById(tb + '-frm- ') != undefined)
		resizeIframe(parent.document.getElementById(tb + '-frm-con').childNodes[0]);
	
}
function act_actual(tb, x = '0', ev, b = 4) {
	if (tb == "")
		return;
	var a = 'cap';
	var id = tb + '-' + a;
	if (document.getElementById(id) == undefined) {
		var p = document.createElement('div');
		p.id = id;
		p.className = a + ' panel movil col-' + b;
		var txt = "<div id='" + id + "-tit' class='titulo'><span id='" + id + "-tit' >" + tb + " DETALLE </span>";
		txt += "<span id='" + id + "-foco' class='oculto'></span>";
		txt += "<nav class='right'><ul class='menu' id='" + id + "-menu'></ul></nav></div>";
		txt += "<span id='" + id + "-msj' class='mensaje' ></span><div class='contenido' id='" + id + "-con' ></div></div>";
		p.innerHTML = txt;
		document.getElementById('fapp').appendChild(p);
		Drag.init(document.getElementById(id + '-tit'), p);
		document.getElementById(id).style.top = (document.getElementsByClassName("panel").length) * 20;
		document.getElementById(id).style.left = (document.getElementsByClassName("panel").length) * 20;
	}
	document.getElementById(id).style.display = "block";
	act_html(id + '-menu', ruta_app, 'tb=' + tb + '&a=men&b=' + a, false);
	act_html(id + '-foco', ruta_app, 'tb=' + tb + '&a=focus&b=' + a, false);
	if (document.getElementById(id + '-msj') != undefined)
		document.getElementById(id + '-msj').innerHTML = "";
	act_html(id + '-con', ruta_app, 'tb=' + tb + '&a=cap&b=' + x, false);
	act_children(tb);
	sobreponer(tb, a);
}
function crear_panel(tb, a, b = 7, lib = ruta_app, tit = '') {
	var id = tb + '-' + a;
	if (document.getElementById(id) == undefined) {
		var p = document.createElement('div');
		p.id = id;
		p.className = a + ' panel' + (a == 'frm' ? ' col-0' : ' movil col-' + b);
		var txt = "<div id='" + id + "-tit' class='titulo'><span>" + (tit == '' ? tb.replace('_', ' ') : tit) + "</span>";
		txt += "<span id='" + id + "-foco' class='oculto'></span>";
		txt += "<input id='" + id + "-file' type=hidden readonly style='background:none;color:white;' >";
		txt += "<nav class='right'><ul class='menu' id='" + id + "-menu'></ul></nav></div>";
		txt += "<span id='" + id + "-msj' class='mensaje' ></span><div class='contenido' id='" + id + "-con' ></div>";
		p.innerHTML = txt;
		document.getElementById('fapp').appendChild(p);
		Drag.init(document.getElementById(id + '-tit'), p);
		document.getElementById(id).style.top = (document.getElementsByClassName("panel").length) * 20;
		document.getElementById(id).style.left = (document.getElementsByClassName("panel").length) * 20;
	}
	document.getElementById(id).style.display = "block";
	act_html(id + '-menu', lib, 'tb=' + tb + '&a=men&b=' + a, false);
	act_html(id + '-foco', lib, 'tb=' + tb + '&a=focus&b=' + a, false);
	if (document.getElementById(tb + '-msj') != undefined)
		document.getElementById(tb + '-msj').innerHTML = "";
}
function mostrar(tb, a, ev, m = '', lib = ruta_app, w=7, tit='', k='0') {
	var id = tb + '-' + a;
	if (a == 'pro') {
        if (ev!=undefined) {tit=ev.currentTarget.title;k=ev.target.id;}
		crear_panel(tb, a, w, lib, tit);
        act_html(id+'-con',lib,'a=cmp&tb='+tb+'&id='+k);        
	}
	if (a == 'lib') {
		crear_panel(tb, a, 7, lib, ev.currentTarget.title);
		captura.init(tb, eval(ajax(lib, 'a=cmp&tb=' + tb, false)), a);
		captura.head(tb, eval(ajax(lib, 'a=cmp&tb=' + tb + 'filtro', false)), 'filtro');
	}
	if (a == 'tab') {
		crear_panel(tb, a);
		act_html(id + '-con', lib, 'tb=' + tb + '&a=tab&m=' + m, false);
		act_lista(tb, lib);
		sobreponer(tb, a);
	}
	if (a == 'gra') {
		crear_panel(tb, a);
		document.getElementById(id + '-con').innerHTML = '<div id="chart_div"></div>';
		mostrar(document.getElementById('indicador-objeto').value.toLowerCase(), 'tab');
	}
	if (a == 'frm') {
		crear_panel(tb, a);
		document.getElementById(id + '-con').innerHTML = '<iframe id=' + tb + ' onload="resizeIframe(this)" ></iframe>';
		document.getElementById(tb).src = m + '/' + tb + '/';
}
}
function ocultar(tb, a) {
	if (document.getElementById(tb + '-' + a) != undefined) {
		can_children(tb, a);
		if (a == 'tab')
			ocultar(tb, 'cap');
		if (a != 'panel')
			if (document.getElementById(tb + '-' + a + '-con') != undefined)
				document.getElementById(tb + '-' + a + '-con').innerHTML = "";
		document.getElementById(tb + '-' + a).style.display = "none";
	}
}
function desplegar(a) {
	if (document.getElementById(a) != undefined) {
		var b = document.getElementById(a);
		if (b.style.display == 'none')
			b.style.display = 'block';
		else
			b.style.display = 'none';
	}
}

function plegarPanel(t,a,ed=false){
	var d=document.getElementsByClassName(t+' '+a);
	for (i=0;i<d.length;i++) {
		if(d[i].style.display == 'none'){
			d[i].style.display = 'block';
		}else{
			d[i].style.display = 'none';
		}
	} 
	var icono=document.getElementById(a);
	if(icono.classList.contains('desplegar-panel')){
		icono.classList.remove('desplegar-panel');
		icono.classList.add('plegar-panel');
		icono.title='Mostrar';
	}else{
		icono.classList.remove('plegar-panel');
		icono.classList.add('desplegar-panel');
		icono.title='Ocultar';
	}
}

function act_children(tb) {
	var a = document.getElementById(tb + '-cap-menu');
	if (a != undefined) {
		for (var b = 0; b < a.children.length; b++) {
			if (a.children[b].id.indexOf('mostrar') >= 0) {
				var c = a.children[b].id.substr(a.children[b].id.indexOf('mostrar') + 8);
				if (document.getElementById(c + '-tab') != undefined)
					if (document.getElementById(c + '-tab').style.display == 'block') {
						act_html(c + '_tab-con', ruta_app, 'tb=' + c + '&a=tab', false);
						act_lista(c);
					}
				if (document.getElementById(c + '-cap') != undefined)
					ocultar(c, 'cap');
			}
		}
	}
}
function can_children(tb, a) {
	if (a == 'cap') {
		var id = tb + '-' + a;
		var c = document.getElementById(id + '-menu');
		if (c != undefined) {
			for (var b = 0; b < c.children.length; b++) {
				if (c.children[b].id.indexOf('mostrar') >= 0) {
					var d = c.children[b].id.substr(c.children[b].id.indexOf('mostrar') + 8);
					ocultar(d, 'tab');
					ocultar(d, 'gra');
				}
			}
		}
		if (document.getElementById('indicador-objeto') != undefined)
			ocultar(document.getElementById('indicador-objeto').value.toLowerCase(), 'tab');
	}
}
function lista(a, b = '', c = '', d = '') {
	a.list.innerHTML = ajax(ruta_app, 'a=opc&tb=' + b + '&c=' + c + "&d=" + d, false);
}
function cerrar() {
	act_html('fapp', 'g.php', 'a=cerrar', function () {
		location.href = '';
	});
}
function solo_numero(e) {
	var unicode = e.charCode ? e.charCode : e.keyCode
	if (unicode != 8 & unicode != 9) {
		if ((unicode < 48 || unicode > 57))
			return false
	}
}

function solo_numeroFloat(e) {
	var unicode = e.charCode ? e.charCode : e.keyCode
	if (unicode != 8 & unicode != 9) {
		if ((unicode < 48 || unicode > 57))
			return false
	}
}

function solo_fecha(e) {
	var unicode = e.charCode ? e.charCode : e.keyCode
	if (unicode != 8 && unicode != 9) {
		if ((unicode < 48 || unicode > 57) && (unicode != 45) && (unicode != 32) && (unicode != 58))
			return false
	}
}
function solo_hora(e) {
	var unicode = e.charCode ? e.charCode : e.keyCode
	if (unicode != 8 && unicode != 9) {
		if ((unicode < 48 || unicode > 58))
			return false
	}
}
function solo_reg(a, b = '[A..Z]') {
	var r = RegExp(b);
	if (!r.test(a.value))
		a.classList.add('alerta');
}
function checkon(a) {
	if (a.value == 'NO')
		a.value = 'SI';
	else
		a.value = 'NO';
}
function graficar() {
	var tit = document.getElementById('indicador-indicador').options[document.getElementById('indicador-indicador').selectedIndex].text;
	var tv = document.getElementById('indicador-agrupar').value;
	var th = document.getElementById('indicador-columna').value;
	var tb = document.getElementById('indicador-objeto').value;
	var tg = document.getElementById('indicador-tipo_grafico').value;
	var options = {title: tit, vAxis: {title: tv}, hAxis: {title: th}, legend: {position: 'none'}, pieHole: 0.4, };
	switch (tg) {
		case 'AREA':
			var graf = new google.visualization.AreaChart(document.getElementById('chart_div'));
			break;
		case 'PIE':
			var graf = new google.visualization.PieChart(document.getElementById('chart_div'));
			break;
		case 'BAR':
			var graf = new google.visualization.BarChart(document.getElementById('chart_div'));
			break;
		case 'COLUMN':
			var graf = new google.visualization.ColumnChart(document.getElementById('chart_div'));
			break;
		case 'LINE':
			var graf = new google.visualization.LineChart(document.getElementById('chart_div'));
			break;
		case 'STEP':
			var graf = new google.visualization.SteppedAreaChart(document.getElementById('chart_div'));
			break;
		case 'DONUT':
			var graf = new google.visualization.PieChart(document.getElementById('chart_div'));
			options = {title: tit, vAxis: {title: tv}, hAxis: {title: th}, legend: {position: 'none'}, pieHole: 0.4, };
			break;
	}
	var rows = JSON.parse(ajax(ruta_app, 'a=ind&tb=' + tb.toLowerCase(), false));
	var data = new google.visualization.DataTable();
	data.addColumn('string', tv);
	data.addColumn('number', th);
	data.addRows(rows);
	graf.draw(data, options);
	sobreponer('grafica', 'gra');
}
function upload(a, b, c, d = 'con') {
	var myWindow = window.open("/sigrev/libs/upload.php?a=" + a + "&b=" + b + "&c=" + c + "&d=" + d + "&lib=" + ruta_app, "", "width=600,height=240,location=no,");
	act_lista(a);
}
function retornar(a, b) {
	this.opener.document.getElementById(a).value = b;
	this.opener.document.getElementById(a.replace('file', 'msj')).innerHTML = 'Archivo cargado';
	this.close();
}
function set_texto(a, b) {
	if (document.getElementById(a))
		document.getElementById(a).value = b;
}
function filtrar(tb, a) {
	mostrar(tb, 'tab', event);
	//act_html(tb+'_lis', ruta_app, 'tb='+tb+'&a=lis', false);
	aplicar(a);
	act_lista(tb);
}
function procesar(tb, a) {
	document.getElementById(tb + '-' + a + '-msj').innerHTML = '';
	act_html(tb + '-msj', ruta_app, 'tb=' + tb + '&a=imp', false);
	act_lista(tb);
}
function exportar(b, c) {
	var myWindow = window.open(dir + "g.php?a=exportar&b=" + b + "&c=" + c, "Descargar archivo");
}
function imprimir(fecha, oficina) {
	console.log(document.getElementById('a').value);
	var myWindow = window.open("../../tfpdf/Print.php?f=" + document.getElementById(fecha).value + "&o=" + document.getElementById(oficina).value + "&tipo=" + document.getElementById('a').value, 'Fin dia', "width=600,height=800");
}
function aplicar(a) {
	var b = a.split('&');
	for (c = 0; c < b.length; c++) {
		var d = b[c].split('=');
		if (document.getElementById(d[0]) != undefined) {
			document.getElementById(d[0]).value = d[1];
			document.getElementById(d[0]).readOnly = true;
			document.getElementById(d[0]).className = 'nofiltro';
		}
	}
}
function resizeIframe(a, b = 'h', c = '') {
	// a.style.height = a.contentWindow.document.body.scrollHeight + 'px';
	if (b == 'h')
		a.style.height = c;
	if (b == 'w')
		a.style.widtch = c;
}
function blanco(a) {
	if (a.value == '') {
		a.classList.add('invalid');
		a.blur();
		a.focus();
		return true;
	} else {
		a.classList.remove('invalid');
		return false;
	}
}
function sobreponer(a, b = '', c = null) {
	var id = a + (b != '' ? '-' + b : b);
	if (document.getElementById(id) != undefined) {
		var x = document.getElementsByClassName("movil");
		for (i = 0; i < x.length; i++)
			x[i].style.zIndex = 0;
		if (document.getElementById(id) != undefined)
			document.getElementById(id).style.zIndex = 1;
		if (c == null) {
			if (document.getElementById(id + '-foco') != undefined) {
				var foco = document.getElementById(id + '-foco').innerHTML;
				if (document.getElementById(foco) != undefined)
					c = document.getElementById(foco);
			}
		}
		if (c != undefined)
			c.focus();
}
}
function error(a, m) {
	document.getElementById(a.id + '-error').innerHTML = m;
	document.getElementById(a.id).focus();
	return false;
}
function prefijo(a, b) {
	if (b == undefined)
		b = "-";
	return a.substr(0, a.indexOf(b));
}
function sufijo(a, b) {
	if (b == undefined)
		b = "-";
	return a.substr(a.lastIndexOf(b) + 1);
}
function ir_pagina(tb, p, t) {
	if ((p > 0) && (p <= t))
		document.getElementById('pag-' + tb).value = p;
	act_lista(tb, document.getElementById('pag-' + tb));
}
function inArray(needle, haystack) {
	var length = haystack.length;
	for (var i = 0; i < length; i++) {
		if (haystack[i] == needle)
			return true;
	}
	return false;
}
function panel_ayuda() {
	var con = "<div class=panel id='help'>";
	con += "<b>Tab</b>: Siguiente Campo<br><b>Mayus Tab</b>: Anterior Campo</b><br><b>Ctrl Supr</b>: Limpiar Campo<br>";
	con += "<b>Ctrl Enter</b>: Grabar Registro<br><b>Ctrl +</b>: Limpiar Registro<br>";
	return con;
}

var captura = {
	init: function (n, c = '', a = 'tab') {
		var con = "";
		if (a == 'lib')
			con += "<div id='" + n + "-filtro' class='col-2 menu-filtro'></div><div class='col-8 panel'>";
		con += "<div id='" + n + "-captura' class='contenido col-7'></div>";
		con += "<div class='col-3 padding10'><span class='mensaje' id='" + n + "-msj' ></span><br>"+panel_ayuda()+"</div></div>";//CARLOS ACEVEDO  QUITAR border: 1px solid #666;
		con += "<div id='" + n + "-tot' class='col-0 contenido' ></div>";
		con += "<div id='" + n + "-lis' class='col-0 contenido'></div>";
		if (a == 'lib')
			con += "</div>";
		document.getElementById(n + '-' + a + '-con').innerHTML = con;
		if (c != '')
			this.head(n, c, 'captura');
		if (a != 'lib')
			act_lista(n, a);
	},
	head: function (n, c, p = 'captura') {
		var cab = "";
		var cmp = "";
		var foc = c[0].n;
		for (var i = 0; i < c.length; i++) {
			if (c[i].t == undefined)
				c[i].t = 't';    //tipo
			if (c[i].x == undefined)
				c[i].x = '';     //regexp  
			if (c[i].h == undefined)
				c[i].h = '';     //holder
			if (c[i].p == undefined)
				c[i].p = '';     //parent
			if (c[i].d == undefined)
				c[i].d = '';	 //default
			if (c[i].s == undefined)
				c[i].s = 8;	     //size
			if (c[i].v == undefined)
				c[i].v = true;   //valid	  
			if (c[i].i == undefined)
				c[i].i = false;  //insert
			if (c[i].c == undefined)
				c[i].c = c[i].n; //clase
			if (c[i].f == undefined)
				c[i].f = false;  //focus 
			switch (c[i].t) {
				case 's':
					cmp = textsel(n, c, i, p);
					break;
				default:
					cmp = textbox(n, c, i, p);
			}
			document.getElementById(n + '-' + p).innerHTML += "<div class='col-10 campo borde1 oscuro'><div>" + c[i].l + "</div>" + cmp + "</div>";
			if (c[i].t == 's')
				act_html(c[i].n, ruta_app, 'tb=' + c[i].c + '&a=opc&id=' + c[i].d, false);
		}
		if (p == 'filtro')
			act_lista(n);
		document.getElementById(foc).focus();
	},
	mod: function (n, c, r, keys = false) {
		var foc = c[0].n;
		if (keys) {
			console.log(r);
			var i = 0;
			for (var key in r) {
				var cmp = document.getElementById(c[i].n);
				if (c[i].u == undefined)
					c[i].u = true;
				if (c[i].f == undefined)
					c[i].f = false;
				if (c[i].d == undefined)
					c[i].d = '';
				if (c[i].u) {
					if (cmp.classList.contains('captura')) {
						cmp.classList.remove('captura');
						cmp.classList.add('bloqueo');
					}
				}
				document.getElementById(c[i].n).readOnly = c[i].u;
				document.getElementById(c[i].n).value = r[key];
				if (c[i].f)
					foc = c[i].n;
				i++;
			}
		} else {
			for (i = 0; i < c.length; i++) {
				var cmp = document.getElementById(c[i].n);
				if (c[i].u == undefined)
					c[i].u = true;
				if (c[i].f == undefined)
					c[i].f = false;
				if (c[i].d == undefined)
					c[i].d = '';
				if (c[i].u) {
					cmp.disabled = true;
					if (cmp.classList.contains('captura')) {
						cmp.classList.remove('captura');
						cmp.classList.add('bloqueo');
					}
				}
				document.getElementById(c[i].n).readOnly = c[i].u;
				document.getElementById(c[i].n).value = r[i];
				if (c[i].f)
					foc = c[i].n;
			}
		}
		document.getElementById(foc).focus();
	},
	lim: function (n, c) {
		var foc = c[0].n;
		for (i = 0; i < c.length; i++) {
			var cmp = document.getElementById(c[i].n);
			if (c[i].i == undefined)
				c[i].i = false;
			if (c[i].p == undefined)
				c[i].p = '';
			if (c[i].d == undefined)
				c[i].d = '';
			if (c[i].f == undefined)
				c[i].f = false;
			cmp.disabled = false;
			if (cmp.classList.contains('bloqueo')) {
				cmp.classList.remove('bloqueo');
				cmp.classList.add('captura');
			}
			if (c[i].i) {
				if (cmp.classList.contains('captura')) {
					cmp.classList.remove('captura');
					cmp.classList.add('bloqueo');
				}
			}
			cmp.readOnly = c[i].i;
			cmp.value = (c[i].p != '' ? valor(c[i].p) + c[i].d : c[i].d);
			if (c[i].f)
				foc = c[i].n;
		}
		document.getElementById(foc).focus();
	}
};
function textbox(n, c, i, p) {
	var inp = "";
	inp += " <input autocomplete='off' ";
	inp += " type='text'";
	inp += " name='" + c[i].n + "' id='" + c[i].n + "'";
	inp += " maxlength='" + c[i].s + "' size='" + c[i].s + "'";
	inp += " class='" + p + ' ' + n;
	if (c[i].i)
		inp += " bloqueo ";
	if (c[i].v)
		inp += " valido ";
	if (c[i].t == 't')
		inp += " '";
	if (c[i].t == 'n')
		inp += " txt-right' onkeypress='return solo_numero(event);'";
	if (c[i].t == 'd')
		inp += " txt-right' onkeypress='return solo_fecha(event);'";
	if (c[i].i)
		inp += " readonly ";
	if (c[i].h != '')
		inp += " placeholder='" + c[i].h + "'";
	if (c[i].x != '')
		inp += " onblur=\"solo_reg(this,'" + c[i].x + "');\" ";
	if (document.getElementById('lista_' + c[i].n) != undefined)
		inp += " list='lista_" + c[i].n + "'";
	else {
		if (document.getElementById('lista_' + c[i].c) != undefined)
			inp += " list='lista_" + c[i].c + "'";
	}
	inp += " value='" + (c[i].p == '' ? '' : valor(c[i].p)) + c[i].d + "' ";
	if (c[i].f)
		foc = c[i].n;
	inp += ">";
	return inp;
}
function textsel(n, c, i, p) {
	var inp = "";
	inp += "<select ";
	inp += " name='" + c[i].n + "' id='" + c[i].n + "'";
	inp += " class='" + p + ' ' + n;
	if (c[i].i)
		inp += " bloqueo ";
	if (c[i].v)
		inp += " valido '";
	inp += " text='" + c[i].d + "'";
	inp += "></select>";
	return inp;
}
function isJson(item) {
	item = typeof item !== "string"
					? JSON.stringify(item)
					: item;
	try {
		item = JSON.parse(item);
	} catch (e) {
		return false;
	}

	if (typeof item === "object" && item !== null) {
		return true;
	}
	return false;
}

function wait(ms) {
	var start = new Date().getTime();
	var end = start;
	while (end < start + ms) {
		end = new Date().getTime();
	}
}

function validaMultiplo(a, b) {
	c = document.getElementById(a);
	c.classList.remove('alerta');
	if (parseInt(c.value) % b != 0 && c.value != '') {
		c.classList.add('alerta');
		c.focus();
		return false;
	}
	return true;
}

/*+++++++++++++++++++++++++++++++++++++PROCESO CON FETCH API++++++++++++++++++++++++++++++++++++++++++++++ */

function myFetch(b, c, d) {
    const loader = document.getElementById('loader');
	if (loader?.style) loader.style.display = 'block';
  
    return fetch(b, {
      method: 'POST',
      headers: {
        'Content-type': 'application/x-www-form-urlencoded'
      },
      body: c + form_input('fapp')
    })
      .then(response => {
        if (response.ok) {
          return response.text();
        } else {
          throw new Error(`Error ${response.status}: ${response.statusText}`);
          document.getElementById(d+'-modal').innerHTML=`Error ${response.status} : ${response.statusText}`;
          document.getElementById(d+'-image').innerHTML='<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
        }
      })
      .then(data => {
        if (loader?.style) loader.style.display = 'none';
        console.log(data);
        document.getElementById(d+'-modal').innerHTML=data;	
		if(data.includes('Error')){
			document.getElementById(`${d}-image`).innerHTML = '<svg class="icon-popup"><use xlink:href="#bad"/></svg>';
		  }else{
			document.getElementById(`${d}-image`).innerHTML = '<svg class="icon-popup"><use xlink:href="#ok"/></svg>';
		  }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById(`${d}-modal`).innerHTML=`Error: ${error}`;
        document.getElementById(`${d}-image`).innerHTML='<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
      });
  }

  function getJSON(action, table, id, customHeaders = {}) {
	const headers = {
	  "Content-type": "application/x-www-form-urlencoded",
	  ...customHeaders,
	};
	const body = `a=${action}&tb=${table}&id=${id}`;
	const url = ruta_app;
  
	return fetch(url, { method: "POST", headers, body })
	  .then((response) => {
		if (!response.ok) {
		  throw new Error("Network response was not ok");
		}
		return response.json();
	  })
	  .catch((error) => {
		handleRequestError(error);
	  });
  }
  
  function handleRequestError(error) {
	document.getElementById(mod + "-modal").innerHTML =
	  "Error al realizar la solicitud";
	document.getElementById(mod + "-image").innerHTML =
	  '<svg class="icon-popup" ><use xlink:href="#bad"/></svg>';
	openModal();
  }

  /* +++++++++++++++++++++++++++++++++++++++++++++SELECT MULTIPLE+++++++++++++++++++++++++++++++++++++++++++++++ */

	window.onmousedown = function (e) {
		let el = e.target;
		if (el.tagName.toLowerCase() == 'option' && el.parentNode.hasAttribute('multiple')) {
			e.preventDefault();
			selectedMultiple(el);
		}
	}
	
	function selectedMultiple(a){
		let out='';
		const mul=document.getElementById(a.parentNode.id).parentNode.childNodes[1];
		let selOpt=a.parentNode;
	
	
			if (a.hasAttribute('selected')) a.removeAttribute('selected');
			else a.setAttribute('selected', '');
	
			var sel = selOpt.cloneNode(true);
			selOpt.parentNode.replaceChild(sel, selOpt);
				
			if(sel.selectedIndex >=0){
				if(sel.options[sel.selectedIndex].value==""){
					if(sel.selectedOptions[0].text=='Todos'){
						sel.selectedOptions[0].text='Ninguno';
						sel.selectedOptions[0].removeAttribute('selected');
						selectedAll(document.getElementById(sel.id),true);
					}else{
						sel.selectedOptions[0].text='Todos';
						sel.selectedOptions[0].removeAttribute('selected');
						selectedAll(document.getElementById(sel.id),false);
					}
				}
			}          
			for (let i=0; i<sel.selectedOptions.length; i++) {
				out += sel.selectedOptions[i].label;
				if (sel.length-1==sel.selectedOptions.length) {
					  out='Selecciono Todos';
				}else if(sel.selectedOptions.length>3){
					  out='Selecciono '+sel.selectedOptions.length;
				}
				if (i === (sel.selectedOptions.length - 2)) {
				  out +=  ", ";
				} else if (i < (sel.selectedOptions.length - 2)) {
				  out += ", ";
				}
			  }
	  mul.value = out;
	  sel.focus();
	}
	
	function showMult(a,b){
		let x= 'f'+a.id
		let s=document.getElementById(x);
		if(s != null ){
			if (s.classList.contains('close') && b===true) {
				s.classList.remove('close');
				s.focus();
				let sHei= document.getElementById(a.id).clientWidth + 1 + "px";
				document.getElementById(x).style.width=sHei;
			} else {
				s.classList.add('close');
			}
		}else{
			if (a!=null)document.getElementById(a.id).classList.add('close');
		}
	}
	
	function searchMult(a){
		let selmul=a.nextElementSibling;
		var sel=document.getElementById(selmul.id);
	
		if (a.value==''){
			selectedAll(sel,false);
		}
	}
	
	function selectedAll(a,b=true){
		if (b!==true){
			for(var i=0;i<a.length;i++){
				if( a.options[i].hasAttribute('selected')){
					a.options[i].removeAttribute('selected');
				}
			}
		}else{
			for(var i=1;i<a.length;i++){
					a.options[i].setAttribute('selected','selected');
				}
			}
		}
