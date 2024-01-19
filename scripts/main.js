//print
function estasImprimiendo() { alert("Por favor, cancele la impresion y utilice el boton de Imprimir del Sistema (No utilice Ctrl + P)"); }
function guid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    s4() + '-' + s4() + s4() + s4();
}
window.onbeforeprint = estasImprimiendo;
// Config box
    // Enable/disable fixed top navbar
    $('#fixednavbar').click(function () {
        if ($('#fixednavbar').is(':checked')) {
            $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
            $("body").removeClass('boxed-layout');
            $("body").addClass('fixed-nav');
            $('#boxedlayout').prop('checked', false);
        } else {
            $(".navbar-fixed-top").removeClass('navbar-fixed-top').addClass('navbar-static-top');
            $("body").removeClass('fixed-nav');
        }
    });

    // Enable/disable fixed sidebar
    $('#fixedsidebar').click(function () {
        if ($('#fixedsidebar').is(':checked')) {
            $("body").addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9,
            });
        } else {
            $('.sidebar-collapse').slimscroll({destroy: true});
            $('.sidebar-collapse').attr('style', '');
            $("body").removeClass('fixed-sidebar');
        }
    });

    // Enable/disable collapse menu
    $('#collapsemenu').click(function () {
        if ($('#collapsemenu').is(':checked')) {
            $("body").addClass('mini-navbar');
            SmoothlyMenu();
        } else {
            $("body").removeClass('mini-navbar');
            SmoothlyMenu();
        }
    });

    // Enable/disable boxed layout
    $('#boxedlayout').click(function () {
        if ($('#boxedlayout').is(':checked')) {
            $("body").addClass('boxed-layout');
            $('#fixednavbar').prop('checked', false);
            $(".navbar-fixed-top").removeClass('navbar-fixed-top').addClass('navbar-static-top');
            $("body").removeClass('fixed-nav');
            $(".footer").removeClass('fixed');
            $('#fixedfooter').prop('checked', false);
        } else {
            $("body").removeClass('boxed-layout');
        }
    });

    // Enable/disable fixed footer
    $('#fixedfooter').click(function () {
        if ($('#fixedfooter').is(':checked')) {
            $('#boxedlayout').prop('checked', false);
            $("body").removeClass('boxed-layout');
            $(".footer").addClass('fixed');
        } else {
            $(".footer").removeClass('fixed');
        }
    });

$('body').on('click','.item',function(e){
	$(this).closest('tbody').find('.item').removeClass('success');
	$(this).addClass('success');
	$(this).closest('tbody').find('.item').removeClass('highlights');
	$(this).addClass('highlights');
}); 
$('body').on('click','.item-mult',function(e){
	if($(this).hasClass('success')) $(this).removeClass('success');
	else $(this).addClass('success');
	if($(this).hasClass('highlights')) $(this).removeClass('highlights');
	else $(this).addClass('highlights');
});
/*
 * 
 * Fixing for context menu
 * 
 */
jQuery.fn.contextMenu = function(menu,actions,text){
    this.each( function(){
        var $this = $(this),
        //$menu = $('#'+menu).clone().removeAttr('id').removeClass('contextMenu');
        items = contextMenu[menu],
        $men = $('<ul class="dropdown-menu">');
        if(items!=null)
	        for(var i=0; i<items.length; i++){
	        	$men.append('<li id="'+items[i].n+'"><i class="fa '+items[i].i+'"></i> '+items[i].t+'</li>');
	        }
        var $menu = $('<div>');
        $menu.append($men);
        $menu.addClass('btn-group').find('ul').addClass('dropdown-menu').before('<button data-toggle="dropdown" class="btn btn-danger btn-xs dropdown-toggle"><span class="fa fa-plus"></span></button>');
        $menu.find('li').each(function(){
        	$(this).wrapInner('<a href="javascript:void(0);">');
        });
        //$this.find('td:eq(0)').css({'width':30+'px'});
        if($this.find('td').length!=0)
        	$this.find('td:eq(0)').empty().append($menu);
        else{
        	$menu.find('button').html('<span class="fa fa-plus"></span>&nbsp;'+text);
        	$this.replaceWith($menu);
        }
        if(actions.onShowMenu!=null)
        	actions.onShowMenu($this,$menu);
        
        $.each(actions.bindings, function(id, func) {
            $('#'+id, $menu).bind('click', function(e) {
            	K.tmp = $(this).closest('tr');
            	func();
            });
    	});
    	
    	
    	
        /*$menu.on('hide.bs.dropdown',function(){
        	$menu.find('ul').removeAttr('style');
        });*/
        $this.bind("contextmenu", function(e) {
        	//$menu.find('ul').css({'position':'absolute','left':(e.clientX-20)+'px','top':(e.clientY-200)+'px'});
            $menu.find('ul').dropdown('toggle');
        	e.preventDefault();
            //$this.find('.btn').click();
         });
    });
    return this;
};
jQuery.fn.selectVal = function(val){
	val = ''+val;
	this.each( function(){
		var $this = jQuery(this),
		arrayTmp= [];
		//$this.find('option:selected').removeAttr("selected");
		for(var contador=0; contador<$this.find('option').length; contador++){
			arrayTmp.push($this.find('option').eq(contador).val());
		}
		index = jQuery.inArray(val,arrayTmp);
		if(index!=-1) $this.find('option').eq(index).attr("selected", "selected");
	});
	return this;
};
jQuery.fn.layout = function(){
    return this;
};
/*
 * FUNCION PARA AUTOCOMPLETADO
 */
var substringMatcher = function(strs) {
	return function findMatches(q, cb) {
		var matches, substringRegex;
		// an array that will be populated with substring matches
		matches = [];
		// regex used to determine if a string contains the substring `q`
		substrRegex = new RegExp(q, 'i');
		// iterate through the pool of strings and for any string that
		// contains the substring `q`, add it to the `matches` array
		$.each(strs, function(i, str) {
			if (substrRegex.test(str)) {
				matches.push(str);
			}
		});
		cb(matches);
	};
};
/**
 * Module for displaying "Waiting for..." dialog using Bootstrap
 *
 * @author Eugene Maslovich
 */

var waitingDialog = waitingDialog || (function ($) {
    'use strict';

	// Creating modal dialog's DOM
	var $dialog = $(
		'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;z-index: 999999999;">' +
		'<div class="modal-dialog modal-m">' +
		'<div class="modal-content">' +
			'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
			'<div class="modal-body">' +
				'<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
			'</div>' +
		'</div></div></div>');

	return {
		/**
		 * Opens our dialog
		 * @param message Custom message
		 * @param options Custom options:
		 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
		 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
		 */
		show: function (message, options) {
			// Assigning defaults
			if (typeof options === 'undefined') {
				options = {};
			}
			if (typeof message === 'undefined') {
				message = 'Cargando...';
			}
			var settings = $.extend({
				dialogSize: 'm',
				progressType: '',
				onHide: null // This callback runs after the dialog was hidden
			}, options);

			// Configuring dialog
			$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
			$dialog.find('.progress-bar').attr('class', 'progress-bar');
			if (settings.progressType) {
				$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
			}
			$dialog.find('h3').html(message);
			// Adding callbacks
			if (typeof settings.onHide === 'function') {
				$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
					settings.onHide.call($dialog);
				});
			}
			// Opening dialog
			$dialog.modal();
		},
		/**
		 * Closes dialog
		 */
		hide: function () {
			$dialog.modal('hide');
		}
	};

})(jQuery);

/*************************************************************************************************
 * FUNCIONES PARA AVISAR DE ERRORES AJAX
*************************************************************************************************/
$( document ).ajaxError(function( event, request, settings ) {
	console.info('*********************************************************************');
	console.info('Evento');
	console.log(event);
	console.info('Peticion');
	console.log(request);
	console.info('Parametros');
	console.log(settings);
	console.info('*********************************************************************');
	K.unblock();
	$('.modal').modal('hide');
	var $w;
	new K.Modal({
		id: 'windowError',
		title: 'Advertencia de Sistema',
		allScreen: true,
		content: '<div class="icon warning pulseWarning" style="display: block;text-align: center;">'+
				'<button class="btn btn-danger" style="cursor:default;"><i class="fa fa-exclamation-triangle fa-5x"></i></button>'+
			'</div>'+
			'<h2 style="text-align:center;">Error al acceder al Servidor</h2>'+
			'<p class="lead text-muted" style="display: block;">Se present&oacute; un error <i style="font-weight:bold;">'+request.status+'</i> al acceder a la direcci&oacute;n <i style="font-weight:bold;">'+settings.url+'</i> a trav&eacute;s del m&oacute;dulo <i style="font-weight:bold;">'+$.cookie('action')+'</i>.</p>'+
			'<p class="lead text-muted" style="display: block;">Refresque la ventana para solucionar este problema. Si el error persiste, comun&iacute;quese con la <i style="font-weight:bold;">Oficina de Inform&aacute;tica</i> mostr&aacute;ndole una captura de esta pantalla.</p>'+
			'<p style="text-align: center;"><button tabindex="1" class="confirm btn btn-lg btn-success"><i class="fa fa-refresh"></i> Refrescar</button></p>',
		noButtons: true,
		onContentLoaded: function(){
			$w = $('#windowError');
			$w.attr('data-backdrop','static')
				.attr('data-keyboard','false');
			$w.find('.close').remove();
			$w.find('button').click(function(){
				$.cookie('action',null);
				location.reload();
			});
			/*$.post('ci/index/mail',{data: $w.html()},function(){
				//
			},'json');*/
		}
	});
});
/*************************************************************************************************
 * FUNCIONES PARA AVISAR DE ERRORES JAVASCRIPT
*************************************************************************************************/
window.onerror = function(error,url,line,column,obj) {
	console.info('*********************************************************************');
	console.info('Error');
	console.log(error);
	console.info('URL');
	console.log(url);
	console.info('Linea');
	console.log(line);
	console.info('Columna');
	console.log(column);
	console.info('Objeto Error');
	console.log(obj);
	console.info('*********************************************************************');
	K.unblock();
	$('.modal').modal('hide');
	K.msg({text: 'Error: '+error});
	/*$.post('ac/error/save',{
		error: error,
		url: url,
		line: line,
		column: column,
		obj: obj
	});
    var tmp = true;
    switch(url){
        case '': tmp = false; break;
        case 'http://sistemas.sbparequipa.gob.pe/scripts/plugins/socket.io-1.2.0.js': tmp = false; break;
        case 'http://200.10.77.59/scripts/plugins/socket.io-1.2.0.js': tmp = false; break;
        case 'http://200.10.77.59/themes/inspinia/js/socket.io-1.2.0.js': tmp = false; break;
        case 'http://sistemas.sbparequipa.gob.pe/themes/inspinia/js/socket.io-1.2.0.js': tmp = false; break;
        case 'http://sistemas.sbparequipa.gob.pe/scripts/ac/chat.js': tmp = false; break;
        case 'http://200.10.77.59/scripts/ac/chat.js': tmp = false; break;
        default: tmp = true; break;
    }
    if(tmp==true){
		var $w;
		new K.Modal({
			id: 'windowError',
			title: 'Advertencia de Sistema',
			allScreen: true,
			content: '<div class="icon warning pulseWarning" style="display: block;text-align: center;">'+
					'<button class="btn btn-danger" style="cursor:default;"><i class="fa fa-exclamation-triangle fa-5x"></i></button>'+
				'</div>'+
				'<h2 style="text-align:center;">Error al ejecutar Script</h2>'+
				'<p class="lead text-muted" style="display: block;">Se present&oacute; un error interno al trabajar en el m&oacute;dulo <i style="font-weight:bold;">'+$.cookie('action')+'</i>.</p>'+
				'<p class="lead text-muted" style="display: block;">Descripci&oacute;n: <i style="font-weight:bold;">'+error+'</i></p>'+
				'<p class="lead text-muted" style="display: block;">Script: <i style="font-weight:bold;">'+url+'</i></p>'+
				'<p class="lead text-muted" style="display: block;">L&iacute;nea: <i style="font-weight:bold;">'+line+'</i></p>'+
				'<p class="lead text-muted" style="display: block;">Refresque la ventana para solucionar este problema. Si el error persiste, comun&iacute;quese con la <i style="font-weight:bold;">Oficina de Inform&aacute;tica</i> mostr&aacute;ndole una captura de esta pantalla.</p>'+
				'<p style="text-align: center;"><button tabindex="1" class="confirm btn btn-lg btn-success"><i class="fa fa-refresh"></i> Refrescar</button></p>',
			noButtons: true,
			onContentLoaded: function(){
				$w = $('#windowError');
				$w.attr('data-backdrop','static')
					.attr('data-keyboard','false');
				$w.find('.close').remove();
				$w.find('button').click(function(){
					K.cleanData();
					$.cookie('mode',null);
					$.cookie('action',null);
					location.reload();
				});
			}
		});
	}*/
};
/*************************************************************************************************
 * FUNCIONES SOCKET
*************************************************************************************************/
//var socket = io.connect( 'https://146.148.98.178:3000');
var socket = undefined;
socket_con = false;
if(typeof socket != 'undefined'){
//if(socket!=null){
	socket.on('connect', function () {
		K.unblock();
		K.clearNoti();
		K.notification({title: 'Servidor conectado!',type: 'info'});
		socket_con = true;
		var tmp_user = {
			_id: K.session.user._id.$id,
			userid: K.session.user.userid,
			user: K.session.user,
			enti: K.session.enti,
			orga: ''
		};
		if(K.session.enti.roles!=null){
			if(K.session.enti.roles.trabajador!=null){
				tmp_user.orga = K.session.enti.roles.trabajador.organizacion._id.$id;
			}
		}
		
		var user_tmp = {
			_id: K.session.user._id,
			groups: K.session.user.groups,
			online: K.session.user.online,
			owner: K.session.user.owner,
			passwd: K.session.user.passwd,
			userid: K.session.user.userid
		};
		user_tmp.oficina = {
			_id: K.session.enti.roles.trabajador.oficina._id.$id,
			nomb: K.session.enti.roles.trabajador.oficina.nomb
		};
		
		socket.emit('adduser', user_tmp, location.hostname,function(data){
			if(data){
				KChat.init();
				//acNoti.init();
				socket.on('votar',function(callback){
					callback(true);
					new K.Modal({
						id: 'modAdv',
						content: '<h4>Otro Usuario Esta ingresando desde otro lugar con esta Cuenta</h2><p>Esta Sesi&oacute;n se cerrara en <div id="counter" style="margin-left:37%;"></div> segundos</4>',
					});
					/*$('#counter').countdown({
				          image: 'images/digits.png',
				          startTime: '20',
				          timerEnd: function(){ document.location.href = "ci/index/logout"; },
				          format: 'ss'
				    });
					setTimeout(function(){
						document.location.href = "ci/index/logout";
				    }, 15000);//10segundos*/
				});
				socket.on('notification', function (data) {
					K.notification({
						title: data.title,
						text: data.msg,
						type: data.type
					});
					$.ajax({
						url: "ac/noti/last",
						success: function(response){
							K.unblock();
							if(response!=null){
								$('#iconNoty label').html(response.count);
							}
						},
						dataType: 'json'
					});
				});
			} else{
				console.log('Ha ocurrido un problema!  Refresca la Pagina(F5).');
			}
		});
	});
}
$(window).resize(function(){
	var width = $(window).width(); 
	var height = $(window).height(); 
	
	if((width >= 1024  ) && (height>=768)){
		K.screen = true;
	}else{
		K.screen = false;
	}
}).resize();
var vStorage = $.jStorage.get('vStorage');
var fecha = '2017-06-21 13:30';
if(vStorage==null){
	K.cleanData();
    $.jStorage.set('vStorage',fecha);
}else{
    if(vStorage!=fecha){
    	K.cleanData();
		$.jStorage.set('vStorage',fecha);
    }
}
$(document).ready(function() {
	$('#mainPanel').resize(function(){
		$('#mainPanel').height($('body').height()+'px');
	});
	/*var func_rel;
	$(window).click(function(){
		clearTimeout(func_rel);
		reloj_mov();
	});
	$(window).keyup(function(){
		clearTimeout(func_rel);
		reloj_mov();
	});
	function reloj_mov(){
		clearTimeout(func_rel);
		func_rel = setTimeout(function(){
			console.info('aaaa');
			alert(32434);
			reloj_mov();
		},5000);
	}
	clearTimeout(func_rel);
	setTimeout(function(){
		reloj_mov();
	},5000);*/
});