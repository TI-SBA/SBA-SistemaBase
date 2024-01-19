var timerMenu;
var timerMensaje;
var timeOut1 = 200;
var timeOut2 = 100;
var maxChildAccess = 5;
var fechaInicio = '1800-01-01';

var M_CONFIRM_DELETE= "<h2>¿Quiere eliminar este registro?</h2><p>A elegido eliminar este registro, si no lo quiere hacer haga clic sobre el botón \"No\"</p>";
var M_SERVER_ERROR = "Ocurrió un error al conectar con el servidor, por favor prueba recargar la página ó inténtalo en otro momento.";
var M_FIELD_LIST_ERROR = "Por favor revisa los campos marcados con rojo, existen campos vacíos ó con valores incorrectos";


var App = {
	$AUTOR:'Gerson D. Aduviri Paredes',
	$FECHA:'15-03-2010'
};
App.NuevoItemLista = listBlockAdd;  //Muestra el formulario para agregar un nuevo registro en un ListBLock
App.ChangeItemMode = changeItemMode;//Alterna entre modo edición y modo lectura los controles que tengan este estilo
App.RefreshLista = null;			//Actualiza datos de un ListBlock  inoperativo
App.IDS = null,						//Array para almacenar multiples ID's para trabajar
App.Editing = -1;					//Almacena el ID del elemento actual en edición
App.Mensaje = mostrarMensaje;		//Muestra un mensaje de sistema estilos: info, alerta, error
App.CerrarMensaje = ocultarMensaje;	//Fuerza el cierre del mensaje de sistema
App.initTabsProgramas = initUITabs;	//Inicializa tabs de Unidades con programas
App.initListaMenu = createSelectList;//Inicializa clasificación personalizada en tabs Unidades
App.SeBoxTitle = setBoxTitle;		//Modifica
App.Validate = validateForm;



jQuery.extend(jQuery.validator.messages, {
	  required: "Este campo es obligatorio.",
	  remote: "Por favor, rellena este campo.",
	  email: "Por favor, escribe una dirección de correo válida",
	  url: "Por favor, escribe una URL válida.",
	  date: "Por favor, escribe una fecha válida.",
	  dateISO: "Por favor, escribe una fecha (ISO) válida.",
	  number: "Por favor, escribe un número entero válido.",
	  digits: "Por favor, escribe sólo dígitos.",
	  creditcard: "Por favor, escribe un número de tarjeta válido.",
	  equalTo: "Por favor, escribe el mismo valor de nuevo.",
	  accept: "Por favor, escribe un valor con una extensión aceptada.",
	  maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
	  minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
	  rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
	  range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
	  max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
	  min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
});






$(document).ready(function() {
	/* IE grrr */
	//SexyLightbox.initialize({color:'black', dir: 'http://192.168.1.100/erpeducativo/libraries/js/sexyimages'});
	/*************************************************************************** MENUS */
	//Ocultar grupo de menus
	$(".v_menu legend").click().toggle(	function(){	$(this).parent().find('ul').slideUp();	},function(){	$(this).parent().find('ul').slideDown();});
	//Manejo de menus superior
	//$("#top_menu .show_menu").focus(function(){alert("ddddd");});
	$("#top_menu .show_menu a").bind('click focus mouseenter', function(){
		var nombreIn = $(this).parent().attr('id').slice(2);
		//App.Mensaje("Sale: " + nombreIn);
		var left = $(this).parent().find('a').parent().offset().left;
		$(this).addClass("m_selected");
		left = left - ($("#" + nombreIn).width() - $(this).parent().width())/2;
		if((left + $("#" + nombreIn).width()) > $(document).width()){left = $(document).width() - ($("#" + nombreIn).width() + 5);}else if(left < 0){	left = 5;	}
		mostrarMenu(nombreIn);
		$("#" + nombreIn).offset({top:$(this).parent().find('a').offset().top + 33,left:left});
	});
	$("#top_menu .show_menu a").bind('blur mouseleave', function(){
		var nombreOut = $(this).parent().attr('id').slice(2);
		//App.Mensaje("Sale: " + nombreOut);
		timerMenu = setTimeout(function(){ocultarMenu(nombreOut);}, timeOut2);
	});

	try {
		$("#main_menu li").draggable({opacity: 0.7,helper: 'clone',revert: 'invalid'});
		$("#drop_accesos").droppable({
			activeClass: 'classDropActive',hoverClass: 'classDropOver',
			drop: function(event, ui){
				if ($("#drop_accesos li").length < maxChildAccess) {
					$(this).append('<li><a href="' + ui.draggable.find('a').attr('href') + '">' + ui.draggable.find('a').text() + '</a></li>');
					$("#m_accesos").append('<a href="Accesos" title="' + ui.draggable.find('a').attr('href') + '"><img src="images/address_16.png" alt="Adress" /></a>');
				}else {
					App.Mensaje("No se pueden mostrar mas de 5 accesos","alerta");
				}
			}
		});
	}catch(e){
		App.Mensaje("Ocurrió un error al cargar librerias, no estarán disponibles todas las funcionalidades", "error");
	}

	/*************************************************************************** TOOLTIP */
	$("#m_accesos a").mouseenter(function(event){
		var p = $(this).offset();
		$("#ttt").show();	$("#ttt").offset({top:p.top + 30, left:p.left});	$("#ttt").text($(this).attr('title'));
		$(this).attr('name',$(this).attr('title'));		$(this).attr('title','');
	});
	$("#m_accesos a").mouseleave(function(){	$(this).attr('title',$(this).attr('name'));		$(this).attr('name');	$("#ttt").hide();	});
	/*************************************************************************** FORMS */
	$("#msj .cerrar").click(ocultarMensaje);
	/*************************************************************************** LISTAS EDITABLES */
	initListBlock(".list_block");
	
	/*$.ajaxError(function(event, request, settings){
		App.Mensaje("Error AJAX: " + request, "error");
	});*/
});//End of (document.ready)

$(window).load(function() {
	
});//End of (window.load)

$(window).unload(function(event) {
	//alert(event.isDefaultPrevented());
	event.preventDefault();
	event.stopPropagation();
	/*if(App.Editing != -1){
		if(!confirm("Hay campos que se están editando, si ha realizado cambios y no han sido guardados estos se perderán")){
			return false;
		}
	}*/
	return false;
});


/** Functions  **/
function mostrarMenu(menuIn)	{	$("#" + menuIn).slideDown('fast'); $("#" + menuIn).mouseenter(tomarMenu); $("#" + menuIn).mouseleave(dejarMenu);	}
function ocultarMenu(menuOut)	{	$("#m_" + menuOut + " a").removeClass("m_selected"); $("#" + menuOut).slideUp('fast');	}
function tomarMenu()			{	clearTimeout(timerMenu); var position = $(this).position();	}
function dejarMenu()			{	var n = $(this).attr('id');	timerMenu = setTimeout(function leave(){ ocultarMenu(n);}, timeOut2);	$(this).unbind('mouseenter', tomarMenu, true);	$(this).unbind('mouseleave', dejarMenu, true);}

function listBlockAdd(lista)
{
	if(App.Editing != -1) return;
	$(lista).find("ul").prepend("<li>" + $(lista).find("span.dummy_nuevo").html() + "</li>");
	$(lista).scrollTop(0);
	var n = $(lista).find("ul li:first");
	n.find('span.controles').show();
	changeItemMode(n,true);
}
function initListBlock(target)
{
	$(target + " li").live('mouseenter',function(){
		$(this).find('span.controles').show();
	});
	
	$(target + " li").live('mouseleave', function(){
		$(this).find('span.controles').hide();
	});
	
	$(target + " li a").live('click', lisItemAccions);
	$(target).find("form").live('submit',submitList);
	
	$("#form_paginacion a").click(function(){
		$("#form_paginacion #page").val($(this).attr('href'));
		$("#form_paginacion").submit();
		return false;
	});
	//$("#form_paginacion").submit();
	$("select#cant").change(function(){
		//App.Mensaje("Select: " + $(this).val());
		//$("#form_paginacion #page").val($(this).attr('href'));
		$("#form_paginacion #page").val('1');
		$("#form_paginacion").submit();
	});
	/*$(target).find("form").each(function(){
		/*var itemRules = new Object();
			//	nomb:"required"	
		//};
		$(this).find(input).each(function(){
			//$(this).attr('id');
			itemRules[$(this).attr('id')] = $(this).attr('rel'); 
		});
		//alert("asdfadfasd fasd as");*/
		//App.Validate($(this));
		//$(this).validate();
		//$(this).submit(submitList);
	//});
	
	//$(target + " form input")
}

function submitList()
{
	App.Mensaje("Submit list");
	if(!$(this).valid()){
		App.Mensaje(M_FIELD_LIST_ERROR, "alerta",{time:6000});
		return false;
	}
	
	App.Mensaje("Guardando cambios...","info", {time:0});
	var item = $(this).parent();
	var url = item.parent().parent().find('input#path_url').val() + "save";
	var params = $(this).serialize();
	var lista = "";
	var error = false;
		
	if(error){
		App.Mensaje("Existen errores en el formulario", "error", {time:6000})
		return false;
	}
	
	$.post(url, params, function(data){
		if(data == 'false'){
			App.Mensaje("Ha ocurrido un error, el registro no ha sido guardado",'error',{time:6000});
			if(item.find('#id').val() == 0)
				item.slideUp();
			return false;
		}
		item.find("input").each(function(){
			$(this).removeClass('valid');
		});
		item.find("input#check_id").val(data);
		item.find("input#check_id").removeAttr('disabled');
		item.find("input#id").val(data);
		App.Mensaje("Registro guardado");
	});
	changeItemMode(item,false);
	return false;
}

function lisItemAccions(){
	var item = $(this).parent().parent();
	switch($(this).attr('class')){
		case 'tool_quickedit':
		case 'tool_editar':		/******************************************************  Editar   */
			if($(this).attr('id') == "largeedit_item") break;
			App.Validate(item.find("form"));
			changeItemMode(item,true);
			break;
		case 'tool_cancelar':	/******************************************************  Cancelar edición */
			changeItemMode(item,false);
			item.find("form").validate().resetForm();
			if(item.find("input#id").val() == 0) item.slideUp();
			break;
		case 'tool_eliminar':   /******************************************************  Eliminar */
			Sexy.confirm(M_CONFIRM_DELETE,{onComplete:function(res){
				if(!res) return;
				App.Mensaje("Eliminando local...","info", {time:0});
				var url = item.parent().parent().find('input#path_url').val() + "delete";
				$.post(url, {id:item.find("input#id").val()}, function(data){
					if (data == 'false'){
						App.Mensaje("Ocurrio un error al eliminar el registro, no se realizaron cambios",'error',{time:6000});
						return;
					}
					App.Mensaje("Registro eliminado");
					item.find('#check_id').removeAttr('checked');
					item.slideUp('fast');
				});
			}});
			break;
		case 'tool_guardar':	/******************************************************* Guardar */
			item.find("form").submit();
			break;
	}
}

function setAsPagination($divPag, $IDlista)
{
	
}

function changeItemMode(item, edit, restore)
{
	if(edit){
		//if(App.Editing != -1)	return;
		// Label's
		item.find('label').each(function(){
			if($(this).attr('for') == '' ) return; //Si el label no tiene asignado un input no hace nada
			$(this).hide();
			$(this).parent().find("#" + $(this).attr('for')).show();
		});
		
		// Input's
		item.find("input").each(function (){
			if($(this).attr('id') == '' || !$(this).hasClass('inputHide') ) return;
			$(this).addClass('inputHide_focus').removeAttr('readonly');
		});
		
		//fechas
		item.find("input.inputDate").each(function(){
			if ($(this).attr('id') == '') {	var d = new Date();	$(this).val(d.getFullYear() + " - " + (d.getMonth()+1) + " - " + d.getDate());	return;	}
			$(this).addClass('inputDate_focus').datePicker({
				startDate: fechaInicio,verticalOffset:30,clickInput:true,createButton: false
			}).dpSetSelected($(this).val()).dpSetDisabled(false);
		});
		
		item.find('a#eliminar_item').removeClass().addClass('tool_cancelar');
		item.find('a#editar_item').removeClass().addClass('tool_guardar');
		item.find('a#quickedit_item').removeClass().addClass('tool_guardar');
		App.Editing = item.find("input#id").val();
	}else{
		item.find("label").each(function(){
			if($(this).attr('for') == '' ) return; //Si el label no tiene asignado un input no hace nada
			var itemx = item;
			var select = $(this).parent().find("#" + $(this).attr('for') + " option:selected").text();
			$(this).show();
			$(this).text(select);
			$(this).parent().find("#" + $(this).attr('for')).hide();
		});
		item.find("input").removeClass('inputHide_focus inputDate_focus').attr('readonly','readonly');
		item.find('a#eliminar_item').removeClass().addClass('tool_eliminar');
		item.find('a#editar_item').removeClass().addClass('tool_editar');
		item.find('a#quickedit_item').removeClass().addClass('tool_quickedit');
		App.Editing = -1;
	}
}

/**
 * @param mensaje
 * @param tipo
 * @param opciones
 * @return
 */
function mostrarMensaje(mensaje, tipo, opciones)
{
	$("#msj #text_content").html(mensaje);	$("#msj").fadeIn();
	//var topMsj = 35;
	var tiempo = 4000;
	if(opciones != undefined){
		for(op in opciones){
			switch(op){
			case "to":	topMsj = $(opciones[op]).offset().top;	break;
			case "position":
				if(opciones.to == undefined) break;
				if(opciones[op] == "bottom"){topMsj += $(opciones.to).height() + 5;}
				else if(opciones[op] == "top"){topMsj -= $("#msj").height() + 30;}
				break;
			case "time":	tiempo = opciones[op];	break;
			}
		}
	}
	if(tipo == undefined){	$("#msj").removeClass();	$("#msj").addClass("msj_info");	}else{	$("#msj").removeClass();	$("#msj").addClass("msj_" + tipo);	}
	clearTimeout(timerMensaje);
	if(tiempo > 0){	timerMensaje = setTimeout(function(){$("#msj").fadeOut();}, tiempo);	}
	centro = ($("#msj").width()/2);   
	$("#msj").css('margin-left', -centro);
}

/**
 * @return
 */
function ocultarMensaje(){
	$("#msj").fadeOut();
}

/**
 * function iniUITabs
 * @param {string} selector
 * @param {function} callback
 * @callback {idProg, idUnidad, numPeriodos} 
 * @return
 */
function initUITabs(selector, callback){
	//App.Mensaje('Tabs cargados');
	var unidad = '';
	//$(selector).animate({opacity:0.25}).delay(5000);
	$(selector).tabs({
		show: function(event,ui) {
			//String().slice(start, end)
			unidad = $(ui.tab).attr('href').slice(1);
			$(ui.panel).find('div[id^=lista_programas] a').first().click();
			//callback(id_prog, unidad);
		}
	});
	$(selector + " div[id^=lista_programas] a").click(function(){
		$(selector + ' a[class=item_selected]').removeClass('item_selected');
		$(this).addClass('item_selected');
		callback($(this).attr('id'), unidad, $(this).find('span').text());
	});
	$(selector).mouseenter(function(){$(this).find('div[id^=lista_programas] li').show();});
	$(selector).mouseleave(function(){$(this).find('div[id^=lista_programas] a[class!=item_selected]').parent().hide();});
}

function createSelectList(selector, onSelect){
	$(selector + " a").live('click', function(){
		$(selector + ' a[class=item_selected]').removeClass('item_selected');
		$(this).addClass('item_selected');
		onSelect($(this).attr('id'), $(this));
	});
	$(selector + " li:lt(1) a").click();
}

function setBoxTitle(mensaje)
{
	$("#cboxTitle").html(mensaje);
}

/*    Validate   */
function validateForm($form, $rules){
	$form.find("input").focus(function(){
		if($(this).hasClass('error')){
			$(this).tooltip ($(this).attr('title'), {mouse: false,sticky:true}).show();
		}
	});

	$form.find("input").blur(function(){
		$(this).tooltip_hide();
	});
	
	$form.validate({
		rules:$rules,
		errorPlacement: function(error, element){
			if(error.text() == ""){
				element.attr('title',"");
			}else{
				element.attr('title',error.text());
			}
			return false;
		}
	});
	

}




