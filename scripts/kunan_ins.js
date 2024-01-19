var K = {
    version: '1.1.0',
    options: {
        nameApp:    'Proyecto Beneficencia',
        theme:      'kunanui',
	    error:      'ci/index/error',
	    about:      'ci/index/about'
	},
    path: {
	    source:  'scripts/',    /* Path to KunanUI source JavaScript */
	    themes:  'themes/',     /* Path to KunanUI Themes */
	    plugins: 'plugins/'     /* Path to Plugins */
    },
    /* Returns the path to the current theme directory */
    themePath: function(){
        return K.path.themes + K.options.theme + '/'; 
    },
    files: {
        css: [],
        js: []
    },
    instances: {
        window: [],
        windowTot: 0,
        panel: [],
        panelTot: 0
    },
    tmp: null,
    history: [],
    goBack: function(){
    	K.history[K.history.length-1].f();
    	K.history.splice(-1,1);
    },
    path_file: 'https://www.sbparequipa.gob.pe/files_sist/',
    movement: 0
};
/*
*  We add all the methods
*/
$.extend(K,{
/*
Function: updateContent
	Replace the content of a window or panel.

updateOptions:
	element - The parent window or panel.
	childElement - The child element of the window or panel recieving the content.
	method - ('get', or 'post') The way data is transmitted.
	data - (hash) Data to be transmitted
	title - (string) Change this if you want to change the title of the window or panel.
	content - (string or element) An html loadMethod option.
	loadMethod - ('html', 'xhr', or 'iframe')
	url - Used if loadMethod is set to 'xhr' or 'iframe'.
	scrollbars - (boolean)
	padding - (object)
	store - (boolean)
	onContentLoaded - (function)
*/
	updateContent: function(opts){
		var options = $.extend({},{
			element:      null,
			childElement: null,
			method:       null,
			data:         null,
			title:        null,
			content:      null,
			loadMethod:   null,
			url:          null,
			scrollbars:   null,
			padding:      null,
			store:		  true,
			require:      false,
			onContentLoaded: null
		}, opts);
		
		/* We check if there is an element */
		if (!options.element){
			K.notification({text: '"element" not defined for "K.updateContent"',type: 'error'});
			return console.warn('=>K: "element" not defined for "K.updateContent"');
		}
		
		/* The load method is defined */
		if (!options.loadMethod){
			if (!options.url) options.loadMethod = 'html';
			else options.loadMethod = 'xhr';
		}
		/* This method load the needed libraries */
		if($.type(options.require)=='object'){
			if($.type(options.require.css)=='array'){
				K.include.css(options.require.css);
			}
			if($.type(options.require.javascript)=='array'){
				K.include.javascript(options.require.javascript,options.require.onload,options);
			}
		}else
			this.loadSelect(options);
		return this;
	},
	/* Load new content. */
	loadSelect: function(opts){
		switch(opts.loadMethod){
			case 'xhr':
				this.updateContentXHR(opts);
				break;
			case 'iframe':
				this.updateContentIframe(opts);
				break;
            case 'json':
                this.updateContentJSON(opts);
                break;
			case 'html':
			default:
				this.updateContentHTML(opts);
				break;
		}
	},
	updateContentXHR: function(opts){
		if(opts.store)
			if($.jStorage.get(opts.url, false)){
				$.jStorage.get(opts.url);
	        	$('#'+opts.element).html($.jStorage.get(opts.url));
	        	if($.isFunction(opts.onContentLoaded))
					opts.onContentLoaded();
				return;
			}
		$.ajax({
	        url: opts.url,
	        data: opts.data,
	        error: function(e, xhr, opts, error){
	            $('#'+opts.element).html('<strong>Error:</strong><br />'+'La petici�n a la p�gina ' + opts.url + ' ha devuelto el siguiente error: ' + xhr.status + ' - ' + error);
	        },
	        success: function(rpta){
	        	if(opts.store) $.jStorage.set(opts.url, rpta);
	        	$('#spinner').hide();
	        	$('#'+opts.element).html(rpta);
            if($.isFunction(opts.onContentLoaded)) opts.onContentLoaded();
	        },
	        type: "POST"
		});
	},
	updateContentIframe: function(opts){
		opts.$element = $('#'+opts.element);
		var width = opts.$element.parent().width()-5;
		opts.$element.append('<iframe src="'+opts.url+'" border=0 width="'+width+'px" height="'+(opts.$element.parent().offset().height-10)+'px"></iframe>');
		opts.$iframe = opts.$element.find('iframe');
		opts.$element.resize(function(){
			opts.$iframe.width(($(this).width()-5)+'px');
			opts.$iframe.height(($(this).height()-10)+'px');
		}).resize();
		if($.isFunction(opts.onContentLoaded)) opts.onContentLoaded();
	},
	updateContentHTML: function(opts){
		$('#'+opts.element).empty().html(opts.content);
		if($.isFunction(opts.onContentLoaded)) opts.onContentLoaded();
	},
	include: {
		javascript: function(p,callback,options){
			for(contJs=0; contJs<p.length; contJs++){
				if($.inArray(p[contJs],K.files.js)==-1){
					var script = document.createElement( 'script' );
					script.type = 'text/javascript';
					script.src = p[contJs];
					document.getElementsByTagName('head')[0].appendChild(script);
					K.files.js.push(p[contJs]);
				}
			}
			if($.isFunction(callback)) callback();
			K.loadSelect(options);
		},
		css: function(p){
			for(contCss=0; contCss<p.length; contCss++){
				if($.inArray(p[contCss],K.files.css)==-1){
					var cssLink = $('<link href="'+p[contCss]+'" type="text/css" rel="Stylesheet" />');
					$("head").append(cssLink);
					K.files.css.push(p[contCss]);
				}
			}
		}
	},
	/*
	
	Function: notification
		Shows a notification to the user.
	
	notificationOptions:
		text - (string) Notification message
		textAlign - (CSS/Properties/text-align) Alignment of message
		layout - (top, topCenter, bottom, center, topLeft, topRight) Layout of notification. (Customizable with CSS)
		theme - (string) Theme of notification. (Customizable with CSS)
		type - (alert | error | success) Style of the notification. (Customizable with CSS)
		modal - (boolean) Adds modal layer when set to true.
		modalCss - (jQuery Css Properties) Set one or more CSS properties for the modal layer.
		speed - (integer (ms)) Speed of open and close animations.
		timeout - (integer (ms) | false) Duration of notification on screen. Set false for sticky notification.
		closable - (boolean) Enables the close button when set to true.
		closeOnSelfClick - (boolean) Close the noty on self click when set to true.
		force - (boolean) Adds notification to the beginning of queue when set to true.
		buttons - (false | array) An array of buttons.
			buttons: [
		      {type: 'button green', text: 'Ok', click: function() { // } },
		      {type: 'button pink', text: 'Cancel', click: function() { // } }
		    ]
		onShow - (false | function) onShow Callback
		onClose - (false | function) onClose Callback

	*/
	notification: function(options){
		if($.type(options)=='string'){
			var tmp = options;
			options = {
				text: tmp,
				type: 'success'
			};
		}
		var delay = 5000;
		switch(options.type){
			case "info":
				var type = 'notice';
				var icon = 'ui-icon ui-icon-locked';
				break;
			case "success":
				var type = 'success';
				var icon = 'ui-icon ui-icon-flag';
				break;
			case "error":
				var type = 'error';
				var icon = 'ui-icon ui-icon-signal-diag';
				break;
			default:
				var type = 'success';
				var icon = 'ui-icon ui-icon-mail-closed';
				break;
		}
		var params = {
			text: options.text,
			type: type,
			icon: icon,
			delay: delay,
			animation: {
				effect_in: 'show',
				effect_out: 'slide'
			}
		};
		if(options.title!=null) params.title = options.title;
		if(options.icon!=null) params.icon = 'ui-icon '+options.icon;
		if(options.delay!=null) params.delay = options.delay;
		if(options.hide!=null) params.hide = options.hide;
		/*toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            timeOut: 4000
        };*/
        if(params.title!=null)
        	params.title = params.title.toUpperCase();
        params.title = $('<div>').html(params.title).text();
        params.text = $('<div>').html(params.text).text();
        switch(params.type){
        	case 'success':
        		//K.nn(params.title,params.text,'images/check.png');
        		toastr.success(params.text, params.title);
        		break;
        	case 'error':
        		//K.nn(params.title,params.text,'images/error.png');
        		toastr.error(params.text, params.title);
        		break;
        	case 'warning':
        		//K.nn(params.title,params.text,'images/alerta.png');
        		toastr.warning(params.text, params.title);
        		break;
        	case 'info':
        		//K.nn(params.title,params.text,'images/info.png');
        		toastr.info(params.text, params.title);
        		break;
        	default:
        		//K.nn(params.title,params.text);
        		toastr.success(params.text, params.title);
        		break;
        }
	},
	clearNoti: function(){
		//$.pnotify_remove_all();
		//PNotify.removeAll();
	},
	sendingInfo: function(){
		K.notification({
			text: 'Enviando informaci&oacute;n...',
			hide: false,
			type: 'info'
		});
	},
	msg: function(opts){
		K.notification(opts);
	},
	Modal: function(opts){
		var options = $.extend({},{
			id				: null,
			title			: 'New window',
			header			: true,
			height			: 250,
			width			: 300,
			modal			: true,
			position		: 'center',
			loadMethod		: null,
			method			: 'post',
			content			: '',
			contentURL		: null,
			data			: null,
			store			: true,
			padding			: { top: 0, right: 0, bottom: 0, left: 0 },
			resizable		: false,
			draggable		: true,
			maximizable		: false,
			minimizable 	: false,
			buttons			: null,
			onResize		: null,
			onContentLoaded : null,
			onClose			: null
		}, opts);
		new K.Window(options);
	},
	TitleBar: function(opts){
		$('[name=titleBar]').html('<b style="text-transform: uppercase;">'+opts.title+'</b>');
		$('#toolBar').empty();
		if(opts.toolbar || opts.onContentLoaded!=null)
			K.updateContent({
				element:      'toolBar',
				method:       opts.method,
				data:         opts.data,
				title:        opts.title,
				content:      opts.content,
				loadMethod:   opts.loadMethod,
				url:          opts.url,
				padding:      opts.padding,
				store:		  opts.store,
				onContentLoaded: opts.onContentLoaded
			});
	},
	restaureWindow: function(id){
		$('#'+id).parent().find('.ui-icon-restore').click();
	},
	maximizeWindow: function(id){
		var $div = $('#'+id);
		tamano = [ $div.height() , $div.width() ];
		tamanowin = [ $div.dialog( "widget" ).height() , $div.dialog( "widget" ).width() ];
		positionwin = [ $div.dialog( "widget" ).position().left , $div.dialog( "widget" ).position().top ];
		$div.dialog( "option", "height", $(window).height() )
			.dialog( "option", "width", $(window).width() )
			.dialog( "option", "position", [ 0 , 0 ] )
			.dialog( "option", "draggable", false )
			.dialog( "option", "resizable", false );
		$('.ui-icon-max',$dialog).addClass('ui-icon-restore');
		$('.ui-icon-max',$dialog).removeClass('ui-icon-max');
	},
	minimizeWindow: function(id){
		$('#'+id).parent().find('.ui-dialog-minimize').click();
	},
	closeWindow: function(id){
		$('#'+id).modal('hide');
	},
	titleUpdate: function(id,title){
		var icon = '<span class="'+$('#'+id+'_dockTab').find('label .ui-icon').attr('class')+'"></span>';
		$('#'+id).dialog( "option", "title", icon+title);
		$('#'+id+'_dockTab').find('label').attr('title',icon+title);
		if(title.indexOf('&',0)>12){
			if(title.length>27) title = title.substring(0, (title.indexOf('&',0)+8))+'...';
		}else{
			if(title.length>20) title = title.substring(0, 20)+'...';
		}
		$('#'+id+'_dockTab').find('span').html(icon+title);
	},
	clickMenu: function(p){
		p.show = function($this){
			p.temp = $this.addClass('temp');
			$ul = $this.find('.inner:eq(0)');
			var left = $this.offset().left;
			if($("#"+p.id+' ul:first').css('float')=='right'){
				//left = $this.offset().left + ($ul.closest('.main').find('a:first').width()*2) - $ul.width();
				left = ($('.temp').offset().left + $ul.closest('.main').width()) - $ul.width() + 20;
			}
			$ul.show();
			$('#menFloat').append($ul).css({
				"display": "block",
				"position": "absolute",
				"z-index": "999999",
				"box-shadow": "2px 2px 5px #999",
				"top": $this.offset().top+$this.height()+"px",
				"left": left+"px"
			});
			p.$menu.data('visible',true);
		};
		p.hideAll = function(){
			$('.temp').append($('#menFloat').find('ul:eq(0)'));
			$('.temp').removeClass('temp');
			p.$menu.find('.main ul').hide();
		};
		if(p.$menu==null) p.$menu = $("#"+p.id+">ul");
		p.$menu.addClass('clickMenu');
		p.$menu.find('>li').each(function(){
			$this = $(this);
			$this.addClass('main');
			$this.find('ul').hide();
			$this.click(function(){
				$this = $(this);
				if(p.$menu.data('visible')==true){
					p.hideAll();
					p.$menu.data('visible',false);
				}else{
					if($this.find('ul').css('display')=='none'){
						p.show($this);
					}else{
						p.hideAll();
						p.$menu.data('visible',false);
					}
				}
			}).mouseenter(function(){
				p.hideAll();
				$this = $(this);
				if(p.$menu.data('visible')){
					p.show($this);
				}
			});
		});
		p.$menu.find('.main ul').each(function(){
			$this = $(this);
			if(!$this.hasClass('main')){
				$this.addClass('inner');
			}
		});
		p.$menu.find('.main>ul').each(function(){
			$this = $(this);
			if(!$this.hasClass('main')){
				$this.addClass('outter');
			}
		});
		p.$menu.find('.inner').width('160px');
		p.$menu.find('.inner ul').before('<img src="' + K.themePath() + 'images/right.gif" class="liArrow" />');
		$('body').mouseup(function(e){
			if($(e.target).closest("#menFloat").length<1)
				if($(e.target).closest("#"+p.id).length<1){
					p.hideAll();
					p.$menu.data('visible',false);
				}
		}).append('<div id="menFloat" class="clickMenu"></div>');
		$('#menFloat').delegate('li','click',function(){
			p.hideAll();
			p.$menu.data('visible',false);
		});
		$('#menFloat').delegate('li','mouseover mouseout',function(event){
			if (event.type == 'mouseover') { 
				$(this).find('ul:eq(0)').css({
					"display": "block",
					"position": "absolute",
					"z-index": "999999",
					"box-shadow": "2px 2px 5px #999",
					"left": "160px",
					"top": "0px"
				});
			}else
				$(this).find('ul').hide();
		});
	},
	/*
	
	Function: windowPrint
		Creates a window for a PDF document.
	
	windowPrintOptions:
		id - (string) Identification of the window.
		title - (string) Change this if you want to change the title of the window or panel.
		url - (string) The link to the PDF document to visualize.

	*/
	windowPrint: function(p){
		if(p.id==null) p.id = 'windowPrint';
		if(p.buttons==null){
			p.buttons = {
				"Cerrar": {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow(p.id);
					}
				}
			};
		}
		new K.Modal({
			id: p.id,
			title: p.title,
			allScreen: true,
			loadMethod: 'iframe',
			contentURL: '',
			buttons: p.buttons,
			onContentLoaded: function(){
				$('#'+p.id).find('iframe').attr('src',p.url)
					.css({
						'height':($(window).height()-200)+'px',
						'width':($(window).width()-90)+'px'
					});
			}
		});
	},
	windowExcel: function(p){
		K.msg({icon: 'ui-icon-print',delay: 10000,title: 'Generando Impresion',text: 'Espere un momento por favor'});
		$('[name=iframe]').remove();
		$('#mainPanel').append('<iframe name="iframe" src="'+p.url+'" style="width:10px;height:10px;"></iframe>');
	},
	/*
	
	Function: windowImage
		Creates a window for an image.
	
	options:
		id - (string) Identification of the window.
		title - (string) Change this if you want to change the title of the window or panel.
		url - (string) The link to the PDF document to visualize.

	*/
	windowImage: function(p){
		new K.Modal({
			id: 'windowImg',
			title: p.title,
			allScreen: true,
			content: '<div style="text-align:center;"><img src="'+p.ruta+'" class="img-thumbnail"></div>',
			buttons: {
				'Imprimir': {
					icon: 'fa-print',
					type: 'success',
					f: function(){
						window.frames["printf"].focus();
						window.frames["printf"].print();
					}
				},
				"Cerrar": {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow('windowImg');
					}
				}
			},
			onContentLoaded: function(){
				$('#windowImg').append('<iframe name="printf" id="printf" style="display:none;" src="mg/mult/print_img?img='+p.ruta+'"></iframe>');
			}
		});
	},
	/*
	
	Function: about
		Display a modal with "About Us" information.
		Modify "K.options.about" to the link you want to show.

	*/
	about: function(){
		new K.Modal({
			id: 'modAbout',
			title: 'Acerca de '+K.options.nameApp,
			contentURL: K.options.about,
			icon: 'ui-icon-info',
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			width: 350,
			height: 150
		});
	},
	/*
	
	Function: cleanTheme
		Reset the CSS theme

	*/
	cleanTheme: function(){
		$.cookie('jquery-ui-theme',null);
	},
	/*
	
	Function: cleanData
		Flush the Local Storage

	*/
	cleanData: function(){
		$.jStorage.flush();
	},
	/*
	
	Function: block
		Blocks the window or element till is needed.
	
	blockOptions:
		$element - Element you want to block. If you don't specify this, it will block the whole window.
		message - (string) Message you want to display while blocking.
		onUnblock - (function) Events to be launched after unblocking.

	*/
	block: function(p){
		if(p==null) p = {};
		if(p.text!=null){
			waitingDialog.show(p.text);
		}else{
			waitingDialog.show();
		}
		
		
		
		return 0;
		if(p==null){
			p = new Object;
			$.blockUI({
				message: '<img src="images/ajax-block.gif"><h2>Espere por favor...</h2>',
                css: {
		            border: 'none',
		            padding: '15px',
		            backgroundColor: '#000',
		            '-webkit-border-radius': '10px',
		            '-moz-border-radius': '10px',
		            opacity: .5,
		            color: '#fff'
	        	}
			});
		}else{
			if(p.message==null) p.message = '<img src="images/ajax-block.gif"><h2>Espere por favor...</h2>';
			if(p.$element!=null){
				p.$element.block({
					message: p.message,
	                css: {
			            border: 'none',
			            padding: '15px',
			            backgroundColor: '#000',
			            '-webkit-border-radius': '10px',
			            '-moz-border-radius': '10px',
			            opacity: .5,
			            color: '#fff'
		        	},
		        	onUnblock: function(){
		        		if($.isFunction(p.onUnblock)) p.onUnblock();
		        	}
	            });
			}else{
				$.blockUI({
					message: p.message,
	                css: {
			            border: 'none',
			            padding: '15px',
			            backgroundColor: '#000',
			            '-webkit-border-radius': '10px',
			            '-moz-border-radius': '10px',
			            opacity: .5,
			            color: '#fff'
		        	},
		        	onUnblock: function(){
		        		if($.isFunction(p.onUnblock)) p.onUnblock();
		        	}
				});
			}
		}
	},
	/*
	
	Function: unblock
		Unblocks the window or element till is needed.
	
	unblockOptions:
		$element - Element you want to block. If you don't specify this, it will block the whole window.

	*/
	unblock: function(p){
		
		waitingDialog.hide();
		
		
		
		return 0;
		if(p==null){
			$.unblockUI();
		}else{
			p.$element.unblock();
		}
	},
	/*
	
	Function: initMode
		Unblocks the window or element till is needed.
	
	initModeOptions:
		mode - (string) Name of the mode, menu and js script.
		titleBar - (object) Params to K.TitleBar

	*/
	initMode: function(p){
		if(p.mode!=null){
			K.selectMenu({id: p.mode});
			$.cookie('mode', p.mode);
		}
		$.cookie('action', p.action);
		$('#content').empty();
		/*if(p.message==null) K.block({$element: $('#pageWrapperMain')});
		else K.block({$element: $('#pageWrapperMain'),message: p.message});*/
		K.TitleBar(p.titleBar);
		$('#side-menu .active').removeClass('active');
		if(p.mode!=null){
			$('#side-menu').find('[name='+p.mode+']').closest('li').addClass('active');
			$('#side-menu').find('[name='+p.action+']').closest('li').addClass('active');
		}else{
			$('#side-menu').find('.in').removeClass('in');
		}
	},
	/*
	
	Function: filter
		Unblocks the window or element till is needed.
	
	filterOptions:
		id - (string) Name of the DOM object.
		height - (integet) Height of the filter.
		width - (integet) Width of the filter.
		top - (integet) Top of the filter.
		left - (integet) Left of the filter.
		onContentLoaded - (function) Function to be performed when all its done.

	*/
	filter: function(p){
		$filter = $(p.content);
		if($('#'+p.id).length<=0) $('body').append($filter);
		$filter = $('#'+p.id);
		$filter.addClass('kuiFilt ui-autocomplete ui-menu ui-widget ui-widget-content ui-corner-all');
		$filter.css({
			"display": "block",
			"z-index": "99999999",
			"height": p.height+"px",
			"width": p.width+"px",
			"top": p.top+"px",
			"left": p.left+"px"
		});
		$('body').mouseup(function(e){
			if($(e.target).closest('.kuiFilt').length<1) $filter.remove();
		});
		p.onContentLoaded();
	},
	/*
	
	Function: selectMenu
		Selects the menu to active.
	
	selectMenuOptions:
		id - Element you want to select.

	*/
	selectMenu: function(p){
		$('#menFloat,#NavBar').find('.ui-state-active').removeClass('ui-state-active');
		$('#'+p.id).addClass('ui-state-active');
	},
	/*
	
	Function: date
		Return a string with the current date in format 'YYYY-mm-dd'.

	*/
	date: function(){
		var myDate = new Date();
		var day = myDate.getDate();
		var month = myDate.getMonth() + 1;
		if(day<10) day = '0' + day;
		if(month<10) month = '0' + month;
		var prettyDate = myDate.getFullYear() + '-' + month + '-' + day;
		return prettyDate;
	},
	/*
	
	Function: dateTime
		Return a string with the current date in format 'YYYY-mm-dd hh:ii:ss'.

	*/
	dateTime: function(){
		var myDate = new Date();
		var day = myDate.getDate();
		var month = myDate.getMonth() + 1;
		var hours = myDate.getHours();
		var minutes = myDate.getMinutes();
		var seconds = myDate.getSeconds();
		if(day<10) day = '0' + day;
		if(month<10) month = '0' + month;
		if(hours<10) hours = '0' + hours;
		if(minutes<10) minutes = '0' + minutes;
		if(seconds<10) seconds = '0' + seconds;
		var prettyDate = myDate.getFullYear() + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
		return prettyDate;
	},
/*
	
	Function: dateTime
		Return a string with the current date in format 'YYYY-mm-dd hh:ii:ss'.

	*/
	dateTimeFormat: function(myDate){
		var day = myDate.getDate();
		var month = myDate.getMonth() + 1;
		var hours = myDate.getHours();
		var minutes = myDate.getMinutes();
		var seconds = myDate.getSeconds();
		if(day<10) day = '0' + day;
		if(month<10) month = '0' + month;
		if(hours<10) hours = '0' + hours;
		if(minutes<10) minutes = '0' + minutes;
		if(seconds<10) seconds = '0' + seconds;
		var prettyDate = myDate.getFullYear() + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
		return prettyDate;
	},
	/*
	
	Function: dateComp
		Compare two dates and return false if first date is less than the last one.
	
	dateCompOptions:
		fecha - (string) first date to compare on format "yyyy-mm-dd hh:ii:ss".
		fecha2 - (string) second date to compare on format "yyyy-mm-dd hh:ii:ss".

	*/
	dateComp: function(fecha, fecha2){
		var xMes=fecha.substr(5, 2);
		var xDia=fecha.substr(8, 2);
		var xAnio=fecha.substr(0,4);
		var yMes=fecha2.substr(5, 2);
		var yDia=fecha2.substr(8, 2);
		var yAnio=fecha2.substr(0,4);
		if (xAnio > yAnio){
			return(true);
		}else{
			if (xAnio == yAnio){
				if (xMes > yMes){
		      		return(true);
				}
		 		if (xMes == yMes){
					if (xDia > yDia){
						return(true);
					}else{
						return(false);
					}
				}else{
					return(false);
				}
			}else{
				return(false);
			}
		}
	},
	/*
	
	Function: round
		Round numbers to a specific amount of decimals.
	
	roundOptions:
		n - (float) Number to be format.
		dec - (integer) Amount of decimals you want to show.

	*/
	round: function(n,dec){
		/*n = parseFloat(n);
		if(!isNaN(n)){
			if(!dec) var dec= 0;
			var factor= Math.pow(10,dec);
			return Math.floor(n*factor+((n*factor*10)%10>=5?1:0))/factor;
		}else{
			return n;
		}*/
		n = parseFloat(n);
		if(!isNaN(n)){
			n = Math.round(n * 100) / 100;
			return n.toFixed(dec);
		}else return n;
	},
	
	roundValorUnitario: function(n,dec){
		n = parseFloat(n);
		if(!isNaN(n)){
			n = K.Decimar(n,dec);
			return n.toFixed(dec);
		}else return n;
	},
	Decimar: function(n,dec){
		hundreds=Math.pow(10, dec)
		n = Math.round(n * hundreds) / hundreds;
		return n
	},
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//MAIL
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	
	Function: gridButtons
		Append a group of button to the selected row.
	
	gridButtonsOptions:
		$row - (DOM) Row to insert buttons.
		event - (event) Click Event,
		index - (integer) Index of the column to insert buttons,
		buttons - (array) Array of objects.
			[
				{
					label - (String) Label for the button.
					icon - (String) Icon for the button.
					callback - (function) Callback for clicking.
				}
			]

	*/
	gridButtons: function(p){
		$target = $(p.event.target);
    	if($target.closest('[name^=btnGrid]').length>0){
    		$.noop();
    	}else{
    		p.$row.data('p',p.data);
			$mainGrid = p.$row.closest('.gridBody');
			$mainGrid.find('ul').each(function(){
				$(this).find('li').eq(p.index).html('');
			});
			for(var i = 0; i<p.buttons.length; i++){
				$mainGrid.find('[name=btnGrid'+p.buttons[i].label+']').button("destroy");
				p.$row.find('li').eq(p.index).append( '<button name="btnGrid'+p.buttons[i].label+'">'+p.buttons[i].label+'</button>' );
				p.$row.find('[name=btnGrid'+p.buttons[i].label+']').button({
		            icons: { primary: p.buttons[i].icon },
		            text: false
		        }).mouseup(function(e){
		        	e.preventDefault();
		        	var tmp = $(this).data('p');
		        	tmp.callback($(this).closest('.item'));
				}).data('p',p.buttons[i]);
			}
    	}
	}
});
K.Desktop = {
	menuInitialize: function(){
		var $table = $('#desktopTitlebar table');
		var $menUser = $('#menUser');
		K.clickMenu({id: 'NavBar'});
		K.clickMenu({id: 'menUser'});
		$menUser.find('a').css('color','#ffffff');
		$menUser.find('li li').css('background','#000');
		$table.find('td').eq(0).width($table.find('td').eq(2).width()+'px');
		porcent = (($table.width()-($table.find('td').eq(2).width()*2))*100)/$table.width();
		$table.find('td').eq(1).width(porcent+'%');
	},
};
K.Panel = function(opts){
	var options = $.extend({},{
		id:                 'mainPanel',
		title:              '',
		container:          'content',
		loadMethod:         null,
		contentURL:         null,

		/* xhr options */
		method:				'post',
		data:               null,
		store:				true,

		/* html options */
		content:            '',
		buttons:			{},

		header:             true,
		headerToolbox:      false,
		headerToolboxURL:   null,
		headerToolboxContent: false,
		headerToolboxOnload: null,

		/* Style options: */
		scrollbars:         true,
		padding:   		    { top: 8, right: 8, bottom: 8, left: 8 },

		/* Events */
		onBeforeBuild:       null,
		onContentLoaded:     null,
		close:				function(){ $.noop(); }

	},opts);
	if(options.title!='')
		K.TitleBar({title: options.title});
	//if(options.id==null) options.id = 'kpanel'+(panelTot+1);
	if($('#'+options.id).length>0){
		index = $.inArray(options.id,K.instances.panel);
		K.instances.panel.splice(index,1);
	}
	/*if(K.instances.panel.indexOf(options.id)!=-1) return K.notification('Panel instance "'+options.id+'" already declared.','error');*/
	K.instances.panel.push(options.id);
	K.instances.panelTot++;
	
	var $container = $('#'+options.container);
	if(options.container == 'body') $container = $('body');
	
	$panel = $('<div id="'+options.id+'" class="widget-content"></div>');
	/*if(opts.buttons!=null){
		$footer = $('<div class="navbar navbar-inverse"></div>');
		$.each(opts.buttons, function(id, func) {
			$footer.append('<button type="button" class="btn btn-success">'+id+'</button>')
				.find('button:last').click(function(){ func(); });
	    });
	    $footer.wrap('<div style="position:fixed;bottom:-5px;width:100%;">');
		$('#content .container-fluid').append($footer);
	}*/
	$container.append($panel);
	K.updateContent({
		element: 			options.id,
		loadMethod:			options.loadMethod,
		method: 			options.method,
		data:				options.data,
		content:			options.content,
		url:				options.contentURL,
		store:				options.store,
		require:			options.require,
		onContentLoaded:	function(){
			if(typeof opts.buttons != 'undefined'){
				$footer = $('<div class="navbar navbar-inverse" style="margin-bottom: -3px;"></div>');
				$.each(opts.buttons, function(id, func) {
					//console.log($.type(func));
					if($.type(func)=='object'){
						$footer.append('<button type="button" class="btn dim btn-'+func.type+'">'+
							'<i class="fa '+func.icon+'"></i> '+id+'</button>&nbsp;')
							.find('button:last').click(function(){ func.f(options.close); });
							//.button({icon: func.icon,type: func.type});
					}
					if($.type(func)=='function'){
						$footer.append('<button type="button" class="btn dim btn-success">'+id+'</button>&nbsp;')
							.find('button:last').click(function(){ func(options.close); });
					}
			    });
			    /*$footer.wrap*/
			   	$tmp = $('<div id="div_buttons" style="position:fixed;bottom:-10px;width:100%;z-index:1000;"></div>');
			   	$tmp.append($footer);
				//$panel.append($tmp);
				$('#'+options.id).append($tmp);
				//$('#'+options.id).append($footer);
			}
			options.onContentLoaded(options.close);
		}
	});
	return this;
};
K.Window = function( params ){
	if($('#'+params.id).length>0) var $modal = $('#'+params.id);
	else{
		var $modal = $('#ajax-modal').clone();
		$modal.append('<div class="modal-dialog">');
		$modal.find('.modal-dialog').append('<div id="lux-modal" class="modal-content">');
		var $modal_main = $modal.find('#lux-modal');
		if(params.width){
			
			
			
			/*
			 * ESTA ES LA LINEA DEL ANCHO
			 */
			$modal.find('.modal-dialog').css({'width':params.width+'px'});
			if(($(window).width()-50)<params.width){
				$modal.find('.modal-dialog').css({'width':($(window).width()-20)+'px'});
				$modal.find('.modal-dialog').css({'height':($(window).height()-150)+'px'});
			}
			
			
			
			
			//$modal.attr('data-width',params.width);
			//var left = ($(window).width()-params.width)/2;
			//$modal_main.css({'width':params.width+'px','left':left+'px'});
		}
		
		
		
		if(params.allScreen!=null){
			if(params.allScreen==true){
				$modal.find('.modal-dialog').css({'width':($(window).width()-50)+'px'});
				$modal.find('.modal-dialog').css({'height':($(window).height()-50)+'px'});
				$modal.find('.modal-body').css({'height':($(window).height()-150)+'px'});
			}
		}
		
		
		
		
		
		$modal.appendTo('body');
	}
	var $buttons = '';
	var icon_content = '';
	if(params.icon!=null){
		icon_content = '<i class="fa '+params.icon+'"></i>';
	}
	var $modal_main = $modal.find('#lux-modal');
	$modal_main.empty()
		.append('<div class="modal-header">'+
			'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
			//icon_content+'<h4 class="modal-title">'+params.title+'</h4>')
			'<h4 class="modal-title">'+params.title+'</h4>')
		.append('<div class="modal-body padded widget-content" id="'+params.id+'-body">');
	$modal.on('hidden.bs.modal',function(){
			if($.isFunction(params.onClose)) params.onClose();
			if(params.retainStore==null){
				//console.log($modal);
				//$modal.off('hide');
				$modal.remove();
			}
			if($('#lux-modal').length>0){
				$('body').addClass('modal-open');
			}
			if(params.unique){
				socket.emit('closeW',params.id);
			}
		});
	K.updateContent({
		element:		params.id+'-body',
		method:			params.method,
		data:			params.data,
		content:		params.content,
		loadMethod:		params.loadMethod,
		url:			params.contentURL,
		store:			params.store,
		onContentLoaded: function(){
			var $footer = $('<div id="div_buttons" class="modal-footer">');
			if(params.buttons){
				$.each(params.buttons, function(id, func) {
					/*$footer.append('<button type="button" class="btn btn-default">'+id+'</button>')
						.find('button:last').click(function(){ func(); });*/
					if($.type(func)=='function'){
						$footer.append('<button type="button" class="btn dim btn-default">'+id+'</button>&nbsp;')
							.find('button:last').click(function(){ func(params); });
					}else{
						var size='sm';
						if(func.size) size=func.size;
						$footer.append('<button type="button" class="btn dim btn-'+func.type+'"><i class="fa '+func.icon+'"></i> '+id+'</button>&nbsp;')
							.find('button:last').click(function(){ func.f(params); });
					}
		    	});
			}else{
				if(params.noButtons==null){
					$footer.append( '<button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-danger"><i class="fa fa-close"></i> Cerrar</button>' );
				}else{
					$modal.find('.modal-footer').remove();
				}
			}
			//$modal.append($footer).modal({backdrop:false}).attr('id',params.id);			
			/*$modal.append($footer).modal().attr('id',params.id);*/
			$modal_main.append($footer);
			if(params.noButtons!=null){
				$modal_main.find('.modal-footer').remove();
			}
			$modal.attr('id',params.id);
			if($.isFunction(params.onContentLoaded)) params.onContentLoaded();
			if(params.height){
				//$modal.find('.widget-content').css({'height':params.height,'overflow-y':'auto'});
				$modal.find('#'+this.element).css({'height':params.height,'overflow-y':'auto'});
				if(($(window).height()-150)<params.height){
					$modal.find('#'+this.element).css({'height':($(window).height()-150)+'px'});
				}
				if(params.allScreen!=null){
					if(params.allScreen==true){
						$modal.find('#'+this.element).css({'height':($(window).height()-150)+'px'});
					}
				}
			}
			$modal.modal({backdrop: 'static', keyboard: false});
			/*$modal.modal().css(
            {
                'margin-top': function () {
                    return ($(window).height()-$(this).height())/2;
                }
            });*/
			//$modal.modal().css({'margin-top':'0px'});
			//$modal.find('.btn-close').focus();
		}
	});
};
$.extend(K,{
	menu: function(p){
		p.$men = $('<ul>');
		for(var i=0; i<p.items.length; i++){
			p.$men.append('<li><a i="'+i+'"><span class="ui-icon '+p.items[i].icon+'"></span>'+p.items[i].lab+'</a></li>');
			p.$men.find('a:last').mouseup(function(){
				p.items[$(this).attr('i')].click();
			});
		}
		p.$men.css({
			zIndex: $.ui.dialog.maxZ,
			position: 'absolute',
			top: p.top,
			left: p.left
		}).menu();
		$('body').append(p.$men);
		$('body').one('mouseup',function(){
			p.$men.remove();
		});
	}
});
$.extend(K,{
	loadMode: function(p){
		//K.selectMenu({id: p.mode});
		$.cookie('mode', p.mode);
		$.cookie('action', p.action);
		//$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
		//$('#pageWrapperLeft').find('[name='+p.action+']').find('ul').addClass('ui-state-highlight');
		$('#mainPanel').empty();
		/*if(p.message==null) K.block({$element: $('#pageWrapperMain')});
		else K.block({$element: $('#pageWrapperMain'),message: p.message});*/
		//K.TitleBar(p.titleBar);
		K.updateContent({
			element: 'mainPanel',
			mode: 'xhr',
			url: p.url,
			store: (p.store!=null)?p.store:true,
			onContentLoaded: p.onContentLoaded
		});
	}
});
K.grid = function(p){
	if(p==null) return false;
	p.$content = '<div name="grid" class="fuelux table-responsive">'+
		'<span class="datagrid-header-title"></span>'+
		'<div class="">'+
			'<div class="col-lg-8 datagrid-header-left controls form-inline">'+
	    	'</div>'+
	    	'<div class="col-lg-4 datagrid-header-right controls form-inline">'+
				'<div class="input-group search">'+
			      	'<input type="text" placeholder="Buscar" class="form-control">'+
			      	'<span class="input-group-btn">'+
			        	'<button name="btnSearch" class="btn btn-info" type="button">Buscar!</button>'+
			      	'</span>'+
			      	
			      	
			      	
			      	
			      	
			      	/*******************************************************************************
			      	 * AQUI SE HARA LA CONFIGURACION DE BUSQUEDA
			      	 * confSearch: {}
			      	*******************************************************************************/
			      	
			      	'<span class="input-group-btn">'+
			        	'<button name="btnConfig" class="btn btn-primary" type="button"><i class="fa fa-gears"></i></button>'+
			      	'</span>'+
			      	
			      	
			      	
			      	
			      	
			      	
			      	
			      	
			    '</div>'+
			'</div>'+
		'</div>'+
	    '<table name="grid" class="table table-striped table-bordered table-condensed table-hover datagrid table-fixed-header">'+
	    	'<thead class="table-header">'+
	    	'</thead>'+
	    	'<tfoot>'+
	    		'<tr class="danger">'+
	    			'<th class="row">'+
	    				'<div class="col-md-6 datagrid-footer-left" style="display:none;">'+
	    					'<div class="grid-controls">'+
	    						'<span><span class="grid-start"></span> - <span class="grid-end"></span> de <span class="grid-count"></span></span>'+
	    						'<select class="grid-pagesize input-small"><option value="10">10</option><option value="20" selected>20</option><option value="50">50</option><option value="100">100</option></select>'+
	    						'<span class="hidden-phone">Por P&aacute;gina</span>'+
	    					'</div>'+
	    				'</div>'+
	    				'<div class="col-md-6 datagrid-footer-right" style="display:none;">'+
	    					'<div class="grid-pager">'+
	    						'<button class="btn btn-danger btn-xs grid-prevpage"><i class="fa fa-chevron-left"></i></button>'+
	    						'<span class="hidden-phone">P&aacute;gina</span>'+
	    						'<select class="grid-pages-all input-small"></select>'+
	    						'<span class="hidden-phone">de <span class="grid-pages"></span></span>'+
	    						'<button class="btn btn-danger btn-xs grid-nextpage"><i class="fa fa-chevron-right"></i></button>'+
	    					'</div>'+
	    				'</div>'+
	    			'</th>'+
	    		'</tr>'+
	    	'</tfoot>'+
	    '</table>'+
	'</div>';
	if(p.$el==null)
		p.$el = $('#mainPanel');
	if(p.height!=null){
		p.$el.addClass('table_height');
		p.$el.find('tbody').css('height',p.height+'px');
	}
	p.$el.empty().append(p.$content);
	if(p.height!=null){
		p.$el.find('.table-responsive')
			.css('overflow-x','scroll');
	}
	/*if(K.screen==true){
		p.$el.find('.table-responsive')
			.removeClass('table-responsive');
	}*/
	p.$el.find('tbody').remove();
	if(p.cols==null) p.cols = [];
	if(p.pagination==null) p.pagination = true;
	if(p.stopLoad==null) stopLoad = false;
	if(p.search==null) p.search = true;
	if(p.confSearch==null) p.confSearch = false;
	if(p.headfixed==null) p.headfixed = true;
	if(p.params==null) p.params = {};
	p.$thead = p.$el.find('thead');
	p.$theadset = p.$el.find('.datagrid-header-left');
	p.$tfoot = p.$el.find('tfoot');
	p.$footer = p.$el.find('tfoot th');
	p.$footerchildren = p.$footer.children().show().css('visibility', 'hidden');
	p.$topheader = p.$el.find('thead th');
	p.$searchcontrol = p.$el.find('.search');
	/*this.$filtercontrol = this.$element.find('.filter');*/
	p.$pagesize = p.$el.find('.grid-pagesize');
	p.$pagedropdown = p.$el.find('.grid-pages-all');
	p.$prevpagebtn = p.$el.find('.grid-prevpage');
	p.$nextpagebtn = p.$el.find('.grid-nextpage');
	p.$pageslabel = p.$el.find('.grid-pages');
	p.$countlabel = p.$el.find('.grid-count');
	p.$startlabel = p.$el.find('.grid-start');
	p.$endlabel = p.$el.find('.grid-end');
	p.$tbody = $('<tbody style="height: '+p.$height+'px; overflow: auto">').insertAfter(p.$thead);
	if(p.height!=null){
		p.$tbody.css('height',p.height+'px');
	}
	if(p.$thead.find('tr').length>1)
		p.$thead.find('tr:last').remove();
	p.$colheader = $('<tr class="warning">').appendTo(p.$thead);
	p.$footer.attr('colspan', p.cols.length);
	p.$topheader.attr('colspan', p.cols.length);

	var colHTML = '',
	width_total = 0;
	
	$.each(p.cols, function (index, column) {
		if($.type(column)=='string'){
			var width = "";
			if(p.widths!=null){
				width=' width="'+p.widths[index]+'px"';
				width_total += p.widths[index];
			}
			colHTML += '<th style="text-align:center;"'+width+'>' + column + '</th>';
		}else{
			if(p.widths!=null){
				width=' width="'+p.widths[index]+'px"';
				width_total += p.widths[index];
			}else{
				if(column.width!=null) width=' width="'+column.width+'px"';
				else width = "";
			}
			if(column.n!=null)
				descr = column.n;
			if(column.descr!=null)
				descr = column.descr;
			if(column.f!=null){
				sort = ' class="grid-sort" data-sort="'+column.f+'"';
			}else sort = "";
			colHTML += '<th style="text-align:center;"'+width+sort+'>' + descr + '</th>';
		}
	});
	if(width_total!=0){
		p.$el.find('table')
			.css('max-width',width_total+20)
			.css('width',width_total+20);
	}
	
	p.$colheader.empty().append(colHTML);
	if(p.toolbarURL!=null||p.toolbarHTML!=null){
		if(p.toolbarURL!=null){
			$.post(p.toolbarURL,function(html){
				p.$theadset.empty().append(html);
				if($.isFunction(p.onContentLoaded))
					p.onContentLoaded(p.$theadset);
			});
		}else{
			p.$theadset.empty().append(p.toolbarHTML);
			if($.isFunction(p.onContentLoaded))
				p.onContentLoaded(p.$theadset);
		}
	}else{
		p.$colheader.find('tr:eq(0)').remove();
		p.$searchcontrol.parent().removeClass('col-md-4');
		p.$theadset.remove();
	}
	if(p.headfixed){
		var headHTML = '<table id="table-fixed" style="position:fixed;top:0;width:'+(p.$el.find('.fuelux').width()-15)+'px;z-index:2000;display:none;" class="table table-bordered table-hover datagrid table-fixed-header"><thead>'+colHTML+'</thead></table>';		
		/*$('#mainPanel .fuelux')//.empty()
			.prepend(headHTML);*/
		p.$el.find('.fuelux')//.empty()
			.prepend(headHTML);
		$('section').scroll(function(){
			var sc = window.pageYOffset; 
			if(sc>p.$thead.find('tr').eq(1).position().top){
				$('#table-fixed').show();
			}else{
				$('#table-fixed').hide();
			}
		});
	}
	$.extend(p,{
		updatePageDropdown: function(data){
			K.clearNoti();
			if(data==null){
				K.notification({
					title: 'Items no encontrados',
					text: 'No hay items para la b&uacute;squeda seleccionada!',
					type: 'error'
				});
				return false;
			}else{
				if(parseInt(data.total_items)==0){
					K.notification({
						title: 'Items no encontrados',
						text: 'No hay items para la b&uacute;squeda seleccionada!',
						type: 'error'
					});
					return false;
				}
			}	
			p.$pagedropdown.empty();
			var pageHTML = '';
			for (var i = 1; i <= data.total_pages; i++) {
				pageHTML += '<option value="'+i+'">' + i + '</option>';
			}
			p.$pagedropdown.html(pageHTML);
			p.$pagedropdown.find('option[value='+data.page+']').attr('selected','selected');
			p.$pageslabel.text(data.total_pages);
			p.$countlabel.html(data.total_items + ' ' + p.itemdescr);
			var ini = 1 + (data.page-1)*data.items_page;
			p.$startlabel.text(ini);
			p.$endlabel.text(ini+parseInt(data.total_page_items)-1);
		
			p.$prevpagebtn.removeAttr('disabled');
			p.$nextpagebtn.removeAttr('disabled');
			if(parseInt(data.page)==1){
				p.$prevpagebtn.attr('disabled',"disabled");
			}
			if(parseInt(data.page)==parseInt(data.total_pages)){
				p.$nextpagebtn.attr('disabled',"disabled");
			}
		},
		renderData: function(page){
			if($.isFunction(p.checkRender)){
				if(p.checkRender(p.$theadset)==false) return p;
			}
			if(p.pagination==true){
				$.extend(p.params,{
					page_rows: p.$pagesize.find('option:selected').val(),
					page: (page) ? page : (p.$pagedropdown.find('option:selected').val()?p.$pagedropdown.find('option:selected').val():1)
				});
			}
			if(p.search==true){
				$.extend(p.params,{
					texto: p.$searchcontrol.find('input').val()
				});
			}
			if(p.$colheader.find('.grid-sort-true').length!=0){
				$.extend(p.params,{
					sort: p.$colheader.find('.grid-sort-true').attr('data-sort'),
					sort_i: p.$colheader.find('.grid-sort-true').attr('data-sort-i')
				});
			}
			p.$tbody.empty();
			if($.isFunction(p.onLoading)){
				p.onLoading();
			}
			if(p.params.sort!=null){
				if(p.params.sort_i==null)
					p.params.sort_i = 1;
			}
			$.jStorage.set('grid-'+p.data,p.params);
			$.post(p.data,p.params,function(data){
				if(p.pagination==true){
					if(data.paging!=null){
						p.$footerchildren.css('visibility', function () {
							return (data.paging.total_items > 0) ? 'visible' : 'hidden';
						});
						p.updatePageDropdown(data.paging);
					}
				}
				if($.isFunction(p.fill)){
					if(data.paging!=null){
						if(data.items!=null)
							for(var i=0,j=data.items.length; i<j; i++){
								p.$tbody.append(p.fill(data.items[i],$('<tr class="item">'),data));
							}
					}else{
						if(data!=null){
							if(data.items!=null){
								for(var i=0,j=data.items.length; i<j; i++){
									p.$tbody.append(p.fill(data.items[i],$('<tr class="item">'),data));
								}
							}else
								p.fill(data,p.$tbody,data);
						}
					}
				}else p.load(data,p.$tbody);
				$.each(p.cols, function (index, col) {
					var width = p.$el.find('theader tr th').eq(index).width();
					p.$tbody.find('tr td').eq(index).css('width',width+'px');
				});
				//K.resetModals();
				if($.isFunction(p.onComplete)){
					p.onComplete();
				}
			},'json');
		},
		reinit: function(newP){
			$.extend(p,newP);
			p.renderData();
		}
	});
	if(p.pagination==true){
		p.$prevpagebtn.click(function(){
			var val = parseInt(p.$pagedropdown.find('option:selected').val());
			p.$pagedropdown.find('option[value='+(val--)+']').attr('selected','selected');
			p.renderData(val);
		});
		p.$nextpagebtn.click(function(){
			var val = parseInt(p.$pagedropdown.find('option:selected').val());
			p.$pagedropdown.find('option:selected').removeAttr('selected');
			p.$pagedropdown.find('option[value='+(val++)+']').attr('selected','selected');
			p.renderData(val);
		});
		p.$pagesize.unbind('change').change(function(){ p.renderData(); });
		p.$pagedropdown.unbind('change').change(function(){ p.renderData(); });
	}else{
		p.$tfoot.remove();
	}
	if(p.search==true){
		p.$searchcontrol.find('input').unbind('keyup').keyup(function(e){
			if(e.keyCode == 13) p.$searchcontrol.find('[name=btnSearch]').click();
		});
		p.$searchcontrol.find('[name=btnSearch]').unbind('click').click(function(){
			p.renderData(1);
		});
		if(p.confSearch==true){
			
			
			
			
			//
			
			
			
			
			
		}else{
			p.$searchcontrol.parent().removeClass('col-md-8');
			p.$searchcontrol.find('[name=btnConfig]').parent().remove();
		}
	}else{
		
		p.$searchcontrol.remove();
		p.$theadset.removeClass('col-md-8');
	}
	p.$colheader.find('.grid-sort').unbind('click').bind('click',function(){
		if($(this).hasClass('grid-sort-true')){
			$(this).attr('data-sort-i',-parseFloat($(this).attr('data-sort-i')));
			if($(this).find('.fa-angle-down').length==0){
				p.$colheader.find('.fa').remove();
				$(this).append('<i class="fa fa-angle-down"></i>');
			}else{
				p.$colheader.find('.fa').remove();
				$(this).append('<i class="fa fa-angle-up"></i>');
			}
		}else{
			p.$colheader.find('.fa').remove();
			p.$colheader.find('.grid-sort-true').removeClass('grid-sort-true');
			$(this).addClass('grid-sort-true');
			$(this).attr('data-sort-i',1);
			$(this).append('<i class="fa fa-angle-up"></i>');
		}
		p.renderData(1);
	});
	if(p.onlyHtml!=null){
		if(p.onlyHtml==true){
			p.$tbody.on('click','.item',function(e){
				$(this).closest('tbody').find('.item').removeClass('highlights');
				$(this).addClass('highlights');
			}); 
			p.$tbody.on('click','.item-mult',function(e){
				if($(this).hasClass('highlights')) $(this).removeClass('highlights');
				else $(this).addClass('highlights');
			});
			if($.isFunction(p.afterLoad)){
				p.afterLoad(p);
				return p;
			}else
				return p;
		}
	}
	if(p.stopLoad==true){
		if($.isFunction(p.afterLoad)){
			p.afterLoad(p);
			return p;
		}
		if($.isFunction(p.onComplete)){
			p.onComplete();
		}
		return p;
	}else{
		if(p.forget==null){
			var pregrid = $.jStorage.get('grid-'+p.data);
			if(pregrid!=null){
				if(pregrid.texto!=null){
					if(pregrid.texto!='')
						p.$searchcontrol.find('input').val(pregrid.texto);
				}
				if(typeof pregrid.page != 'undefined'){
					if(parseInt(pregrid.page)!=1){
						p.$tfoot.find('.grid-pages-all').selectVal(""+pregrid.page);
					}
				}
				if(typeof pregrid.page_rows != 'undefined')
					if(parseInt(pregrid.page_rows)!=20)
						p.$tfoot.find('.grid-pagesize').selectVal(pregrid.page_rows);
				//
			}
		}
		if($.isFunction(p.storeParam)){
			p.storeParam(pregrid,p);
		}else{
			//p.$tbody.css({height:$(window).height()-p.$thead.height()-p.$tfoot.height()-$('#titleBar').height()-$('#dock').height()-10});
			if(p.forget==null)
				if(pregrid==null)
					p.renderData();
				else
					p.renderData(pregrid.page);
			else
				p.renderData();
		}
		return p;
	}
};
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
K.nn = function(title, content, img_uri){
	if(img_uri==null) img_uri = 'images/logo.jpg';
	function checkPermission(){
		if(window.webkitNotifications && window.webkitNotifications.checkPermission() == 0){
			return true;
		}else if(Notification && Notification.permission == 'granted'){
			return true;
		}
		return false;
	}
	
	function requestPermission(){
		//pedimos permiso para mostrar notificaciones
		if(window.webkitNotifications && window.webkitNotifications.checkPermission() != 0){//Chrome
			window.webkitNotifications.requestPermission();
		}else if(Notification && Notification.permission != 'granted'){//Firefox
			Notification.requestPermission();
		}
	}
	//Con estas tres funciones podemos crear un enlace con id="activar" y crear un evento para él:
	if(!checkPermission()){
		requestPermission();
	}
	if(window.webkitNotifications && checkPermission()){
		var notification = window.webkitNotifications.createNotification(
			img_uri,
			title,
			content
		);
		return notification;
	}else if(Notification && checkPermission()){
		return new Notification( title,
		{
			body: content,
			//iconUrl: img_uri
			icon: img_uri
		});
	}
};
K.nn('Notificaciones','Sistema conectado!');