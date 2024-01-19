/*************************************************************************
Asesoría Legal: Reportes */
alRepo = {
	init: function(){
		K.initMode({
			mode: 'al',
			action: 'alRepo',
			titleBar: {
				title: 'Reportes'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/repo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('name')), 800 );
				}).eq(0).click().find('ul').addClass("ui-state-highlight");
				$mainPanel.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()); 
				$section1 = $mainPanel.find('#section1');				
				$section1.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.periodo = $section1.find('[name=ano]').val();
					params.estado = $section1.find('[name=estado] :selected').val();
					params.estadonomb = $section1.find('[name=estado] :selected').text();
					var url = 'al/repo/dili_prog?'+$.param(params);
					K.windowPrint({
						id:'windowAlDiliProgRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section2 = $mainPanel.find('#section2');
				$section2.find('[name=btnImprimir]').click(function(){	
					var url = 'al/repo/expd_activ';
					K.windowPrint({
						id:'windowAlExpdActivProgRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section3 = $mainPanel.find('#section3');
				$section3.find('[name=btnImprimir]').click(function(){	
					var url = 'al/repo/expd_arch';
					K.windowPrint({
						id:'windowAlExpdARchProgRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section4 = $mainPanel.find('#section4');
				$section4.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.periodo = $section4.find('[name=ano]').val();
					params.clasificacion = $section4.find('[name=clasificacion] :selected').val();
					params.clasifnomb = $section4.find('[name=clasificacion] :selected').text();
					var url = 'al/repo/cont?'+$.param(params);
					K.windowPrint({
						id:'windowAlContRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section5 = $mainPanel.find('#section5');
				$section5.find('[name=btnImprimir]').click(function(){	
					var url = 'al/repo/conv';
					K.windowPrint({
						id:'windowAlConvRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section6 = $mainPanel.find('#section6');
				$section6.find('[name=btnExportar]').click(function(){
					var params = new Object;
					params.tipo =  $section6.find('[name=tipo] :selected').val();
					params.encargado = $section6.find('[name=encargado] :selected').val();
					params.materia = $section6.find('[name=materia]').val();
					params.archivado = $section6.find('[name=archivado] :selected').val();
					var url = 'al/repo/expd?'+$.param(params);
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				$('#pageWrapperMain').layout();
				$mainPanel.layout({
					resizeWithWindow:	false,
					west__size:			200,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$mainPanel.find('fieldset').height($mainPanel.height());
			}
		});
		
		/*$(window).resize(function(){
			$('#mainPanel').height($('#pageWrapperMain')+'px');
		}).resize();*/
		$('#pageWrapperMain').layout();
		K.unblock({$element: $('#pageWrapperMain')});
	}	
};