//print
function estasImprimiendo() { alert("Por favor, cancele la impresion y utilice el boton de Imprimir del Sistema (No utilice Ctrl + P)"); }
window.onbeforeprint = estasImprimiendo;
jQuery.fn.selectVal = function(val){
    this.each( function(){
        $this = $(this);
        var arrayTmp= new Array;
        for(contador=0; contador<$this.find('option').length; contador++){
            arrayTmp.push($this.find('option').eq(contador).val());
        }
        index = $.inArray(val,arrayTmp);
        if(index!=-1) $this.find('option').eq(index).attr("selected", "selected");
    });
    return this;
};
$.widget( "ui.timespinner", $.ui.spinner, {
    options: {
        // seconds
        step: 60 * 1000,
        // hours
        page: 60
    },

    _parse: function( value ) {
        if ( typeof value === "string" ) {
            // already a timestamp
            if ( Number( value ) == value ) {
                return Number( value );
            }
            return +Globalize.parseDate( value );
        }
        return value;
    },

    _format: function( value ) {
        return Globalize.format( new Date(value), "t" );
    }
});
K.incomplete = function(){
	/*K.notification({
		text: 'En Implementaci&oacute;n<br/><img src="images/yao.jpg" width="270px" height="270px">',
		delay: 2000
	});*/
	K.notification({
		icon: 'ui-icon-wrench',
		title: 'En construcci&oacute;n',
		text: '<img src="images/en_construccion.jpg" width="270px" height="260px">',
		type: 'info',
		delay: 2000
	});
};
K.loading = function(){
	K.notification({
		text: 'Cargando...<img src="images/ajax-loader_noti.gif" />',
		hide: false,
		type: 'info'
	}); 
};
$.maxZIndex = $.fn.maxZIndex = function(opt) {
    /// <summary>
    /// Returns the max zOrder in the document (no parameter)
    /// Sets max zOrder by passing a non-zero number
    /// which gets added to the highest zOrder.
    /// </summary>    
    /// <param name="opt" type="object">
    /// inc: increment value, 
    /// group: selector for zIndex elements to find max for
    /// </param>
    /// <returns type="jQuery" />
    var def = { inc: 10, group: "*" };
    $.extend(def, opt);
    var zmax = 0;
    $(def.group).each(function() {
        var cur = parseInt($(this).css('z-index'));
        zmax = cur > zmax ? cur : zmax;
    });
    if (!this.jquery)
        return zmax;

    return this.each(function() {
        zmax += def.inc;
        $(this).css("z-index", zmax);
    });
}
//moment.js language configuration
//language : spanish (es)
//author : Julio Napurí : https://github.com/julionc

(function (factory) {
 factory(moment);
}(function (moment) {
 return moment.lang('es', {
     months : "enero_febrero_marzo_abril_mayo_junio_julio_agosto_septiembre_octubre_noviembre_diciembre".split("_"),
     monthsShort : "ene._feb._mar._abr._may._jun._jul._ago._sep._oct._nov._dic.".split("_"),
     weekdays : "domingo_lunes_martes_miércoles_jueves_viernes_sábado".split("_"),
     weekdaysShort : "dom._lun._mar._mié._jue._vie._sáb.".split("_"),
     weekdaysMin : "Do_Lu_Ma_Mi_Ju_Vi_Sá".split("_"),
     longDateFormat : {
         LT : "H:mm",
         L : "DD/MM/YYYY",
         LL : "D [de] MMMM [de] YYYY",
         LLL : "D [de] MMMM [de] YYYY LT",
         LLLL : "dddd, D [de] MMMM [de] YYYY LT"
     },
     calendar : {
         sameDay : function () {
             return '[hoy a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
         },
         nextDay : function () {
             return '[ma&ntilde;na a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
         },
         nextWeek : function () {
             return 'dddd [a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
         },
         lastDay : function () {
             return '[ayer a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
         },
         lastWeek : function () {
             return '[el] dddd [pasado a la' + ((this.hours() !== 1) ? 's' : '') + '] LT';
         },
         sameElse : 'L'
     },
     relativeTime : {
         future : "en %s",
         past : "hace %s",
         s : "unos segundos",
         m : "un minuto",
         mm : "%d minutos",
         h : "una hora",
         hh : "%d horas",
         d : "un d&iacute;a",
         dd : "%d d&iacute;as",
         M : "un mes",
         MM : "%d meses",
         y : "un a&ntilde;o",
         yy : "%d a&ntilde;s"
     },
     ordinal : '%dº',
     week : {
         dow : 1, // Monday is the first day of the week.
         doy : 4  // The week that contains Jan 4th is the first week of the year.
     }
 });
}));
jQuery(document).ready( function($){

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
        var $w;
        new K.Modal({
            id: 'windowError',
            title: 'Advertencia de Sistema',
            height: 500,
            width: 650,
            content: '<div style="text-align:center;"><img src="images/alerta.png"></div>'+
                '<h1 style="text-align:center;">Error al acceder al Servidor</h1><br />'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">Se present&oacute; un error <i style="font-weight:bold;">'+request.status+'</i> al acceder a la direcci&oacute;n <i style="font-weight:bold;">'+settings.url+'</i> a trav&eacute;s del m&oacute;dulo <i style="font-weight:bold;">'+$.cookie('action')+'</i>.</p>'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">Refresque la ventana para solucionar este problema. Si el error persiste, comun&iacute;quese con la <i style="font-weight:bold;">Oficina de Inform&aacute;tica</i> mostr&aacute;ndole una captura de esta pantalla.</p>'+
                '<p style="text-align: center;"><button tabindex="1" class="confirm btn btn-lg btn-success"><i class="fa fa-refresh"></i> Refrescar</button></p>',
            noButtons: true,
            onContentLoaded: function(){
                $w = $('#windowError');
                $w.parent().find('.ui-icon-closethick').remove();
                $w.find('button').click(function(){
                    $.cookie('action',null);
                    location.reload();
                }).button({icons:{primary:'ui-icon-refresh'}});
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
    	$.post('ac/error/save',{
    		error: error,
    		url: url,
    		line: line,
    		column: column,
    		obj: obj
    	});
        var tmp = true;
        switch(url){
            case '': tmp = false; break;
            //case 'http://sistemas.sbparequipa.gob.pe/scripts/plugins/socket.io-1.2.0.js': tmp = false; break;
            //case 'http://sistemas.sbparequipa.gob.pe/scripts/ac/chat.js': tmp = false; break;
            default: tmp = true; break;
        }
        if(tmp==true){
            var $w;
            new K.Modal({
                id: 'windowError',
                title: 'Advertencia de Sistema',
                height: 500,
                width: 650,
                content: '<div style="text-align:center;"><img src="images/alerta.png"></div>'+
                '<h1 style="text-align:center;">Error al ejecutar Script</h1><br />'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">Se present&oacute; un error interno al trabajar en el m&oacute;dulo <i style="font-weight:bold;">'+$.cookie('action')+'</i>.</p>'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">Descripci&oacute;n: <i style="font-weight:bold;">'+error+'</i></p>'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">Script: <i style="font-weight:bold;">'+url+'</i></p>'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">L&iacute;nea: <i style="font-weight:bold;">'+line+'</i></p>'+
                '<p class="lead text-muted" style="display: block;font-size: 18px;">Refresque la ventana para solucionar este problema. Si el error persiste, comun&iacute;quese con la <i style="font-weight:bold;">Oficina de Inform&aacute;tica</i> mostr&aacute;ndole una captura de esta pantalla.</p>'+
                '<p style="text-align: center;"><button tabindex="1" class="confirm btn btn-lg btn-success"><i class="fa fa-refresh"></i> Refrescar</button></p>',
                noButtons: true,
                onContentLoaded: function(){
                    $w = $('#windowError');
                    $w.parent().find('.ui-icon-closethick').remove();
                    $w.find('button').click(function(){
                        $.cookie('mode',null);
                        $.cookie('action',null);
                        K.cleanData();
                        location.reload();
                    }).button({icons:{primary:'ui-icon-refresh'}});
                }
            });
        }
    };
	$.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
	$.datepicker.setDefaults({
	    dateFormat: 'yy-mm-dd',
	    changeMonth: true,
	    changeYear: true,
        //showOn: "button",
	    showOn: "both",
        buttonImage: "images/calendar.gif",
        buttonImageOnly: true,
        showButtonPanel: true,
        beforeShow: function(input){
			//$('#ui-datepicker-div').maxZIndex();
			//$(input).maxZIndex();
			$(input).css({
	            "position": "relative",
	            "z-index": 999999
	        });
		}
	});
	//$('#switcher').themeswitcher();
	//$('#switcher').find('.jquery-ui-themeswitcher-title').css('color','#000');
	$('#switcher').closest('li').remove();
	
	if($.jStorage.get('theme')!=null){
		$('head [name=css_jquery_ui]').remove();
		$('head [name=css_kunan]').remove();
		$('head').append('<link name="css_jquery_ui" href="/themes/kunanui/css/'+ $.jStorage.get('theme') +'" rel="Stylesheet" type="text/css" />');
		$('head').append('<link name="css_kunan" href="/themes/kunanui/css/kunan.css" rel="Stylesheet" type="text/css" />');
	}
	if(K.Desktop.initialize()){
		//Verifica si el storage es actual
		var vStorage = $.jStorage.get('vStorage');
		var fecha = '2015-11-02 12:30';
		if(vStorage==null){
			K.cleanData();
		    $.jStorage.set('vStorage',fecha);
		}else{
		    if(vStorage!=fecha){
		    	K.cleanData();
				$.jStorage.set('vStorage',fecha);
		    }
		}
		
		$("#mg").live('click',function(){ mgNavg(); });
        $("#po").live('click',function(){
            $.cookie('action','poVisi');
            window.location.replace('?new=1');
        });
		$("#td").live('click',function(){ tdNavg(); });
		//$("#cm").live('click',function(){ cmNavg(); });
		$("#cm").live('click',function(){
			$.cookie('action','cmOper');
			window.location.replace('?new=1');
		});
		$("#in").live('click',function(){
			$.cookie('action','inMovi');
			window.location.replace('?new=1');
		});
		$("#lg").live('click',function(){
			$.cookie('action','lgProd');
			window.location.replace('?new=1');
		});
        $("#dd").live('click',function(){
            $.cookie('action','ddPear');
            window.location.replace('?new=1');
        });
		$("#us").live('click',function(){
			$.cookie('action','usIngr');
			window.location.replace('?new=1');
		});
		$("#pr").live('click',function(){ prNavg(); });
		$("#pe").live('click',function(){
            $.cookie('action','peConc');
            window.location.replace('?new=1');
        });
		$("#al").live('click',function(){ alNavg(); });
		$("#cj").live('click',function(){ cjNavg(); });
		$("#cjcm").live('click',function(){ cjcmNavg(); });
		//$("#ct").live('click',function(){ ctNavg(); });
        $("#ct").live('click',function(){
            $.cookie('action','ctPcon');
            window.location.replace('?new=1');
        });
		$("#ac").live('click',function(){
			$.cookie('action','acNavg');
			window.location.replace('?new=1');
		});
		$("#ts").live('click',function(){
			$.cookie('action','tsCheq');
			window.location.replace('?new=1');
		});
		$("#ho").live('click',function(){
			$.cookie('action','hoPend');
			window.location.replace('?new=1');
		});
        $("#ch").live('click',function(){
			$.cookie('action','menPerfil');
			window.location.replace('?new=1');
		});
        $("#fa").live('click',function(){
            $.cookie('action','faVent');
            window.location.replace('?new=1');
        });
        $("#ag").live('click',function(){
            $.cookie('action','agVent');
            window.location.replace('?new=1');
        });
        $("#re").live('click',function(){
            $.cookie('action','reRepo');
            window.location.replace('?new=1');
        });
        $("#ar").live('click',function(){
            $.cookie('action','arDocu');
            window.location.replace('?new=1');
        });
        $("#ti").live('click',function(){
            $.cookie('action','tiComp');
            window.location.replace('?new=1');
        });
		$("#cleanData").live('click',function(){ K.cleanData(); K.cleanTheme(); });		//Clean local storage and jqueryui theme
		$("#menUserDocs").live('click',function(){ 
            acUser.windowEditProf({id:K.session.user._id.$id,userid:K.session.user.userid}) 
        });
		/*$.ajaxSetup({
			contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1"
		});*/
	}
});
(function ($) {
        $.fn.styleTable = function (options) {
            var defaults = {
                css: 'styleTable'
            };
            options = $.extend(defaults, options);

            return this.each(function () {

                input = $(this);
                input.addClass(options.css);

                input.find("tr").live('mouseover mouseout', function (event) {
                    if (event.type == 'mouseover') {
                        $(this).children("td").addClass("ui-state-hover");
                    } else {
                        $(this).children("td").removeClass("ui-state-hover");
                    }
                });

                input.find("th").addClass("ui-state-default");
                input.find("td").addClass("ui-widget-content");

                input.find("tr").each(function () {
                    $(this).children("td:not(:first)").addClass("first");
                    $(this).children("th:not(:first)").addClass("first");
                });
            });
        };
    })(jQuery);