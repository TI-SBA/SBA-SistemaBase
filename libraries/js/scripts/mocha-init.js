/* 

	In this file we setup our Windows, Columns and Panels,
	and then inititialize MochaUI.
	
	At the bottom of Core.js you can setup lazy loading for your
	own plugins.

*/

/*
  
INITIALIZE WINDOWS

	1. Define windows
	
		var myWindow = function(){ 
			new MUI.Window({
				id: 'mywindow',
				title: 'My Window',
				contentURL: 'libraries/pages/lipsum.html',
				width: 340,
				height: 150
			});
		}

	2. Build windows on onDomReady
	
		myWindow();
	
	3. Add link events to build future windows
	
		if ($('myWindowLink')){
			$('myWindowLink').addEvent('click', function(e) {
				new Event(e).stop();
				jsonWindows();
			});
		}

		Note: If your link is in the top menu, it opens only a single window, and you would
		like a check mark next to it when it's window is open, format the link name as follows:

		window.id + LinkCheck, e.g., mywindowLinkCheck

		Otherwise it is suggested you just use mywindowLink

	Associated HTML for link event above:

		<a id="myWindowLink" href="libraries/pages/lipsum.html">My Window</a>	


	Notes:
		If you need to add link events to links within windows you are creating, do
		it in the onContentLoaded function of the new window. 
 
-------------------------------------------------------------------- */

var url_base = 'http://' + location.hostname + '/dms/index.php';
initializeWindows = function(){
	MUI.eventsWindow = function(){
		new MUI.Window({
			id: 'view_config',
			title: 'Cuenta',	
			loadMethod: 'iframe',		
			contentURL: url_base + "/acl/start/config",
			width: 400,
			height: 270,
			scrollbars: false,
			maximizable: false,
			resizable: false
					
			
		});
	};
	if ($('config')){
		$('config').addEvent('click', function(e){
			new Event(e).stop();
			MUI.eventsWindow();
		});
	}
	// Examples
	/*
	MUI.htmlWindow = function(){
		new MUI.Window({
			id: 'htmlpage',
			content: 'Hello World',
			width: 340,
			height: 150
		});
	}	*/
	
	/*MUI.ajaxpageWindow = function(){
		new MUI.Window({
			id: 'ajaxpage',
			contentURL: url_base + '/libraries/pages/lipsum.html',
			width: 340,
			height: 150
		});
	};
	if ($('ajaxpageLinkCheck')){ 
		$('ajaxpageLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.ajaxpageWindow();
		});
	}	*/
/*	
	MUI.jsonWindows = function(){
		var request = new Request.JSON({
			url:url_base + '/data/json-windows-data.js',
			onComplete: function(properties) {
				MUI.newWindowsFromJSON(properties.windows);
			}
		}).send();
	};
	if ($('jsonLink')){
		$('jsonLink').addEvent('click', function(e) {
			new Event(e).stop();
			MUI.jsonWindows();
		});
	}*/
/*
	MUI.youtubeWindow = function(){
		new MUI.Window({
			id: 'youtube',
			title: 'YouTube in Iframe',
			loadMethod: 'iframe',
			contentURL: url_base + '/libraries/pages/youtube.html',
			width: 340,
			height: 280,
			resizeLimit: {'x': [330, 2500], 'y': [250, 2000]},
			toolbar: true,
			toolbarURL: url_base + '/libraries/pages/youtube-tabs.html',
			toolbarOnload: function(){
				MUI.initializeTabs('youtubeTabs');	

				$('youtube1Link').addEvent('click', function(){
					MUI.updateContent({
						'element':  $('youtube'),
						'url': url_base + '/libraries/pages/youtube.html'
					});
				});
	
				$('youtube2Link').addEvent('click', function(){
					MUI.updateContent({
						'element':  $('youtube'),
						'url':  url_base +    '/libraries/pages/youtube2.html'
					});
				});
	
				$('youtube3Link').addEvent('click', function(){
					MUI.updateContent({
						'element':  $('youtube'),	
						'url':  url_base +    '/libraries/pages/youtube3.html'
					});
				});	
			}
		});
	};
	if ($('youtubeLinkCheck')) {
		$('youtubeLinkCheck').addEvent('click', function(e){
		new Event(e).stop();
			MUI.youtubeWindow();
		});
	}
	*/
/*	MUI.clockWindow = function(){
		new MUI.Window({
			id: 'clock',
			title: 'Canvas Clock',
			addClass: 'transparent',
			contentURL:url_base +  MUI.path.plugins + '/coolclock/index.html',
			shape: 'gauge',
			headerHeight: 30,
			width: 160,
			height: 160,
			x: 570,
			y: 140,
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			require: {			
				js: [url_base + MUI.path.plugins + '/coolclock/scripts/coolclock.js'],
				onload: function(){
					if (CoolClock) new CoolClock();
				}	
			}				
		});	
	};
	if ($('clockLinkCheck')){
		$('clockLinkCheck').addEvent('click', function(e){	
			new Event(e).stop();
			MUI.clockWindow();
		});
	}	
	*/
/*	MUI.parametricsWindow = function(){	
		new MUI.Window({
			id: 'parametrics',
			title: 'Window Parametrics',			
			contentURL: url_base +'/'+ MUI.path.plugins + 'parametrics/index.html',
			width: 305,
			height: 110,
			x: 570,
			y: 160,
			padding: { top: 12, right: 12, bottom: 10, left: 12 },
			resizable: false,
			maximizable: false,
			require: {
				css: [MUI.path.plugins + '/parametrics/css/style.css'],
				js: [MUI.path.plugins + '/parametrics/scripts/parametrics.js'],
				onload: function(){	
					if (MUI.addRadiusSlider) MUI.addRadiusSlider();
					if (MUI.addShadowSlider) MUI.addShadowSlider();
				}		
			}				
		});
	};
	if ($('parametricsLinkCheck')){
		$('parametricsLinkCheck').addEvent('click', function(e){	
			new Event(e).stop();
			MUI.parametricsWindow();
		});
	}
	*/
/*	MUI.splitWindow = function(){
		new MUI.Window({
			id: 'splitWindow',
			title: 'Split Window',
			width: 600,
			height: 350,
			resizeLimit: {'x': [450, 2500], 'y': [300, 2000]},
			scrollbars: false, // Could make this automatic if a 'panel' method were created
			onContentLoaded: function(){	
		
				new MUI.Column({
					container: 'splitWindow_contentWrapper',
					id: 'splitWindow_sideColumn',
					placement: 'left',
					width: 170,
					resizeLimit: [100, 300]
				});
			
				new MUI.Column({
					container: 'splitWindow_contentWrapper',
					id: 'splitWindow_mainColumn',
					placement: 'main',
					width: null,
					resizeLimit: [100, 300]
				});
			
				new MUI.Panel({
					header: false,
					id: 'splitWindow_panel1',					
					contentURL:  url_base + '/license.html',
					column: 'splitWindow_mainColumn',
					panelBackground: '#fff'
				});
			
				new MUI.Panel({
					header: false,
					id: 'splitWindow_panel2',
					addClass: 'panelAlt',					
					contentURL: url_base + '/libraries/pages/lipsum.html',
					column: 'splitWindow_sideColumn'					
				});

			}			
		});
	};
	if ($('splitWindowLinkCheck')) {
		$('splitWindowLinkCheck').addEvent('click', function(e){
		new Event(e).stop();
			MUI.splitWindow();
		});
	}
	*/
/*	MUI.fxmorpherWindow = function(){
		new MUI.Window({
			id: 'fxmorpherExample',
			title: 'Path Animation Example',			
			contentURL: url_base + MUI.path.plugins + '/Fx.Morpher/example.html',
			width: 330,
			height: 330,
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			scrollbars: false,
			resizable: false,
			require: {
				css: [MUI.path.plugins + '/Fx.Morpher/css/style.css'],
				js: [MUI.path.plugins + '/Fx.Morpher/scripts/cbox.js', MUI.path.plugins + 'Fx.Morpher/scripts/example.js'],
				onload: function(){
					createCanvas();
					myAnim.delay(250);					
				} 	
			}			
		});	
	};
*/
	// Examples > Tests
/*	MUI.serverRepsonseWindow = function(response){
		new MUI.Window({
			id: 'serverResponse',
			content: response,
			width: 350,
			height: 350
		});
	};
*/	
/*	MUI.eventsWindow = function(){
		new MUI.Window({
			id: 'windowevents',
			title: 'Window Events',	
			loadMethod: 'iframe',		
			contentURL: url_base + "/ac/grupos",
			width: 600,
			height: 500,			
			onContentLoaded: function(windowEl){
				MUI.notification('El Contenido se Cargo Corectamente.');
			},
			onCloseComplete: function(){
				MUI.notification('Venta Cerrada.');
			},
			onMinimize: function(windowEl){
				MUI.notification('VEntana Minimizada.');
			},
			onMaximize: function(windowEl){
				MUI.notification('Ventana Maximizada.');
			},
			onRestore: function(windowEl){
				MUI.notification('Ventana Restaurada.');
			},
			onResize: function(windowEl){
				MUI.notification('Se Cambio el Tamaño de la Ventana.');
			},
			onFocus: function(windowEl){
				MUI.notification('Ventada Seleccionada.');
			},
			onBlur: function(windowEl){
				MUI.notification('Cambio de Ventana.');
			}
		});
	};
	if ($('windoweventsLinkCheck')){
		$('windoweventsLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.eventsWindow();
		});
	}
*/	
/*	MUI.containertestWindow = function(){ 
		new MUI.Window({
			id: 'containertest',
			title: 'Container Test',
			contentURL:url_base + '/libraries/pages/lipsum.html',
			container: 'pageWrapper',
			width: 340,
			height: 150,
			x: 100,
			y: 100
		});
	};
	if ($('containertestLinkCheck')) { 
		$('containertestLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.containertestWindow();
		});
	}
	*/
/*	MUI.iframetestsWindow = function() {
		new MUI.Window({
			id: 'iframetests',
			title: 'Iframe Tests',
			loadMethod: 'iframe',
			contentURL: url_base + '/libraries/pages/iframetests.html'
		});
	};
	if ($('iframetestsLinkCheck')) {
		$('iframetestsLinkCheck').addEvent('click', function(e){
		new Event(e).stop();
			MUI.iframetestsWindow();
		});
	}
	*/
/*	MUI.formtestsWindow = function() {
		new MUI.Window({
			id: 'formtests',
			title: 'Form Tests',			
			contentURL: url_base + '/libraries/pages/formtests.html',
			onContentLoaded: function(){
				document.testForm.focusTest.focus();
			}			
		});
	};
	if ($('formtestsLinkCheck')) {
		$('formtestsLinkCheck').addEvent('click', function(e){
		new Event(e).stop();
			MUI.formtestsWindow();
		});
	}	
*/
/*	MUI.accordiantestWindow = function() {
		var id = 'accordiantest';
		new MUI.Window({
			id: id,
			title: 'Accordian',			
			contentURL: url_base + '/libraries/pages/accordian-demo.html',
			width: 300,
			height: 200,
			scrollbars: false,
			resizable: false,
			maximizable: false,				
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			require: {
				css: [MUI.path.plugins + '/accordian/css/style.css'],
				onload: function(){
					this.windowEl = $(id);				
					new Accordion('#' + id + ' h3.accordianToggler', "#" + id + ' div.accordianElement',{
						opacity: false,
						alwaysHide: true,
						onActive: function(toggler, element){
							toggler.addClass('open');
						},
						onBackground: function(toggler, element){
							toggler.removeClass('open');
						},							
						onStart: function(toggler, element){
							this.windowEl.accordianResize = function(){
								MUI.dynamicResize($(id));
							}
							this.windowEl.accordianTimer = this.windowEl.accordianResize.periodical(10);
						}.bind(this),
						onComplete: function(){
							this.windowEl.accordianTimer = $clear(this.windowEl.accordianTimer);
							MUI.dynamicResize($(id)) // once more for good measure
						}.bind(this)
					}, $(id));
				}	
			}					
		});
	};
	if ($('accordiantestLinkCheck')) { 
		$('accordiantestLinkCheck').addEvent('click', function(e){	
			new Event(e).stop();
			MUI.accordiantestWindow();
		});
	}
*/
/*	MUI.noCanvasWindow = function() {
		new MUI.Window({
			id: 'nocanvas',
			title: 'No Canvas',			
			contentURL: url_base + '/libraries/pages/lipsum.html',
			addClass: 'no-canvas',
			width: 305,
			height: 175,
			shadowBlur: 0,
			resizeLimit: {'x': [275, 2500], 'y': [125, 2000]},
			useCanvas: false
		});
	};
	if ($('noCanvasLinkCheck')) {
		$('noCanvasLinkCheck').addEvent('click', function(e){	
			new Event(e).stop();
			MUI.noCanvasWindow();
		});
	}	
*/
	// View
/*	if ($('sidebarLinkCheck')) {
		$('sidebarLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.Desktop.sidebarToggle();
		});
	}
*/
/*	if ($('cascadeLink')) {
		$('cascadeLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.arrangeCascade();
		});
	}
*/
/*	if ($('tileLink')) {
		$('tileLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.arrangeTile();
		});
	}
*/
/*	if ($('closeLink')) {
		$('closeLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.closeAll();
		});
	}
*/
/*	if ($('minimizeLink')) {
		$('minimizeLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.minimizeAll();
		});
	}	
*/
	// Tools
/*	MUI.builderWindow = function() {	
		new MUI.Window({
			id: 'builder',
			title: 'Window Builder',
			icon: 'images/icons/16x16/page.gif',			
			contentURL: url_base + MUI.path.plugins + 'windowform/',
			width: 375,
			height: 420,
			maximizable: false,
			resizable: false,
			scrollbars: false,
			require: {
				css: [MUI.path.plugins + '/windowform/css/style.css'],			
				js: [MUI.path.plugins + '/windowform/scripts/Window-from-form.js'],
				onload: function(){
					$('newWindowSubmit').addEvent('click', function(e){
						new Event(e).stop();
						new MUI.WindowForm();
					});
				}	
			}			
		});
	};
	if ($('builderLinkCheck')) {
		$('builderLinkCheck').addEvent('click', function(e) {
			new Event(e).stop();
			MUI.builderWindow();
		});
	}
	*/
/*	if ($('toggleStandardEffectsLinkCheck')) {
		$('toggleStandardEffectsLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.toggleStandardEffects($('toggleStandardEffectsLinkCheck'));			
		});
		if (MUI.options.standardEffects == true) {
			MUI.toggleStandardEffectsLink = new Element('div', {
				'class': 'check',
				'id': 'toggleStandardEffects_check'
			}).inject($('toggleStandardEffectsLinkCheck'));
		}
	}	
	*/
/*	if ($('toggleAdvancedEffectsLinkCheck')) {
		$('toggleAdvancedEffectsLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.toggleAdvancedEffects($('toggleAdvancedEffectsLinkCheck'));			
		});
		if (MUI.options.advancedEffects == true) {
			MUI.toggleAdvancedEffectsLink = new Element('div', {
				'class': 'check',
				'id': 'toggleAdvancedEffects_check'
			}).inject($('toggleAdvancedEffectsLinkCheck'));
		}
	}	
	*/	
	// Workspaces
	
/*	if ($('saveWorkspaceLink')) {
		$('saveWorkspaceLink').addEvent('click', function(e) {
			new Event(e).stop();
			MUI.saveWorkspace();
		});
	}
*/
/*	if ($('loadWorkspaceLink')) {
		$('loadWorkspaceLink').addEvent('click', function(e) {
			new Event(e).stop();
			MUI.loadWorkspace();
		});
	}
	*/
	// Help	
/*	MUI.featuresWindow = function() {
		new MUI.Window({
			id: 'features',
			title: 'Features',			
			contentURL: url_base + '/libraries/pages/features-layout.html',
			width: 275,
			height: 250,
			resizeLimit: {'x': [275, 2500], 'y': [125, 2000]},
			require: {
				css: [MUI.themePath() + 'css/Tabs.css']
			},			
			toolbar: true,
			toolbarURL: url_base + '/libraries/pages/features-tabs.html',
			toolbarOnload: function(){
				MUI.initializeTabs('featuresTabs');

				$('featuresLayoutLink').addEvent('click', function(e){
					MUI.updateContent({
						'element':  $('features'),
						'url':  url_base +     '/libraries/pages/features-layout.html'
					});
				});

				$('featuresWindowsLink').addEvent('click', function(e){
					MUI.updateContent({
						'element':  $('features'),
						'url':  url_base +     '/libraries/pages/features-windows.html'
					});
				});

				$('featuresGeneralLink').addEvent('click', function(e){
					MUI.updateContent({
						'element':  $('features'),
						'url':   url_base +    '/libraries/pages/features-general.html'
					});
				});
			}
		});
	};
	if ($('featuresLinkCheck')) {
		$('featuresLinkCheck').addEvent('click', function(e){
			new Event(e).stop();
			MUI.featuresWindow();
		});
	}
*/
/*	MUI.aboutWindow = function() {
		new MUI.Modal({
			id: 'about',
			title: 'MUI',			
			contentURL: url_base + '/libraries/pages/about.html',
			type: 'modal2',
			width: 350,
			height: 195,
			padding: { top: 43, right: 12, bottom: 10, left: 12 },
			scrollbars: false
		});
	};
	if ($('aboutLink')) {
		$('aboutLink').addEvent('click', function(e){
			new Event(e).stop();
			MUI.aboutWindow();
		});
	}
*/	
	// Misc
/*	MUI.licenseWindow = function() {
		new MUI.Window({
			id: 'License',
			title: 'License',						
			contentURL: url_base + '/license.html',
			width: 375,
			height: 340
		});
	};
	if ($('licenseLink')){ 
		$('licenseLink').addEvent('click', function(e) {
			new Event(e).stop();
			MUI.licenseWindow();
		});
	}	
*/
	// Deactivate menu header links
/*	$$('a.returnFalse').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
		});
	});
	*/
	// Build windows onLoad
//	MUI.parametricsWindow();
//	MUI.myChain.callChain();
};

/*
  
INITIALIZE COLUMNS AND PANELS  

	Creating a Column and Panel Layout:
	 
	 - If you are not using panels then these columns are not required.
	 - If you do use panels, the main column is required. The side columns are optional.
	 
	 Columns
	 - Create your columns from left to right.
	 - One column should not have it's width set. This column will have a fluid width.
	 
	 Panels
	 - After creating Columns, create your panels from top to bottom, left to right.
	 - One panel in each column should not have it's height set. This panel will have a fluid height.	 
	 - New Panels are inserted at the bottom of their column. 
 
-------------------------------------------------------------------- */


initializeColumns = function() {

	new MUI.Column({
		id: 'sideColumn1',
		placement: 'left',
		width: 355,
		resizeLimit: [400, 355]
	});
	
	new MUI.Column({
		id: 'mainColumn',
		placement: 'main',
		resizeLimit: [100, 300]
	});
	
	// Add panels to first side column
	new MUI.Panel({
		id: 'files-panel',
		title: '<b>Tipos de Documentos</b>',		
		contentURL: url_base + "/ci/documentos",
		height: 230,
		column: 'sideColumn1',
			onContentLoaded: function(){		
			$('btn_contratos').addEvent('click', function(e){
				MUI.updateContent({
					element: $('mainPanel'),
					url: url_base + '/docs/contratos',
					title: '<b>Documentos Encontrados</b>',
				//	padding: { top: 8, right: 8, bottom: 8, left: 8 }
				});
			});
			$('btn_rb_pago').addEvent('click', function(e){
				MUI.updateContent({
					element: $('mainPanel'),
					url: url_base + '/docs/recibos',
					title: '<b>Documentos Encontrados</b>',
			//		padding: { top: 8, right: 8, bottom: 8, left: 8 }
				});
			});
		
		}
	});
	
	new MUI.Panel({
		id: 'panel2',
		title: '<b>Filtrar Documentos</b>',
		contentURL: url_base + "/docs/filtros",
		column: 'sideColumn1',
		height: 130,
		onContentLoaded: function(){
		
		}
	});
	
	// Add panels to main column	
	new MUI.Panel({
		id: 'mainPanel',
		title: '<b>Documentos Encontrados</b>',
		contentURL: url_base + "/docs/contratos",
		column: 'mainColumn',
		headerToolbox: true,
		headerToolboxURL:  url_base + "/ci/documentos/titulo",
		headerToolboxOnload: function(){
			$('divbtn').hide();
			if ($('demoSearch')) {
				$('demoSearch').addEvent('submit', function(e){
					e.stop();
					$('spinner').setStyle('visibility', 'visible');
					if ($('postContent') && MUI.options.standardEffects == true) {
						$('postContent').setStyle('opacity', 0);
					}
					else {
						$('mainPanel_pad').empty();
					}
					this.set('send', {
						onComplete: function(response){
							MUI.updateContent({
								'element': $('mainPanel'),
								'content': response,
								'title': 'Ajax Response',
								'padding': {
									top: 8,
									right: 8,
									bottom: 8,
									left: 8
								}
							});
						},
						onSuccess: function(){
							if ($('postContent') && MUI.options.standardEffects == true) {								
								$('postContent').setStyle('opacity', 0).get('morph').start({'opacity': 1});
							}
						}
					});
					this.send();
					
				});
			}
		}		
	});
	

	// Add panels to second side column
	
/*
	MUI.splitPanelPanel = function() {
		if ($('mainPanel')) {			
		
		}
	};
	*/
	MUI.myChain.callChain();
};

// Initialize MochaUI when the DOM is ready
window.addEvent('load', function(){ //using load instead of domready for IE8

	MUI.myChain = new Chain();
	MUI.myChain.chain(
		function(){MUI.Desktop.initialize();},
		function(){MUI.Dock.initialize();},
		function(){initializeColumns();},		
		function(){initializeWindows();}		
	).callChain();	

});

//$('btn_documentos').addEvent('click', function(e){documentos();});
