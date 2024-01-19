/*************************************************************************
  Cementario: Reportes */
cmRepo = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmRepo',
			titleBar: {
				title: 'Reportes'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cm/repo',
			store: false,
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('name')), 800 );
				}).eq(0).click().find('ul').addClass("ui-state-highlight");
				/*
				 * Traslados internos y externos
				 * */
				var $section1 = $mainPanel.find('#section1');
				$section1.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$section1.find('[name=ano]').val(d.getFullYear()); 
				$section1.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = $('#mainPanel').find('[name=ano]').val();
					params.mes = $('#mainPanel').find('[name=mes] :selected').val();
					var url = 'cm/repo/tras?'+$.param(params);
					K.windowPrint({
						id:'windowCmTrasPrint',
						title: "Reporte / Traslados Internos y Externos",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				/*
				 * ficha de inventario y mausoleo
				 * */
				var $section2 = $mainPanel.find('#section2');
				$section2.find('[name=btnSelEspa]').click(function(){
					//cmEspa.windowSelectMauso({callback: function(data){
					cmEspa.windowSelect({tipo: 'M',callback: function(data){
						$section2.find('[name=espa]').html(data.nomb).data('data',data);
						$section2.find('[name=btnSelEspa]').button('option','text',false);						
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section2.find('[name=btnImprimir]').click(function(){
					if($section2.find('[name=espa]').data('data')==null){
						$section2.find('[name=btnSelEspa]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un mausoleo!',
							type: 'error'
						});
					}
					var url = 'cm/repo/inventario?id='+$section2.find('[name=espa]').data('data')._id.$id;
					K.windowPrint({
						id:'windowCmInventPrint',
						title: "Reporte / Ficha Inventario de Mausoleos",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				var $section3 = $mainPanel.find('#section3');
				$section3.find('[name=btnSelEspa]').click(function(){
					//cmEspa.windowSelectMauso({callback: function(data){
					cmEspa.windowSelect({tipo: 'M',callback: function(data){
						$section3.find('[name=espa]').html(data.nomb).data('data',data);
						$section3.find('[name=btnSelEspa]').button('option','text',false);						
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section3.find('[name=btnImprimir]').click(function(){
					if($section3.find('[name=espa]').data('data')==null){
						$section3.find('[name=btnSelEspa]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un mausoleo!',
							type: 'error'
						});
					}
					var url = 'cm/repo/trabajo_in_situ?id='+$section3.find('[name=espa]').data('data')._id.$id;
					K.windowPrint({
						id:'windowCmTrabInPrint',
						title: "Reporte / Trabajo de Campo-Constatación in Situ",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				var $section4 = $mainPanel.find('#section4');
				$section4.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$section4.find('[name=ano]').val(d.getFullYear()); 
				$section4.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = $section4.find('[name=ano]').val();
					params.mes = $section4.find('[name=mes] :selected').val();
					var url = 'cm/repo/comp_sepelio?'+$.param(params);
					/*K.windowPrint({
						id:'windowCmCompSepePrint',
						title: "Reporte / Relación de Comprobantes de Sepelios Atendidos",
						url: url
					});*/
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				var $section5 = $mainPanel.find('#section5');
				$section5.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$section5.find('[name=ano]').val(d.getFullYear()); 
				$section5.find('[name=btnExportar]').click(function(){	
					params = new Object;
					params.ano = $section5.find('[name=ano]').val();
					params.mes = $section5.find('[name=mes] :selected').val();
					var url = 'cm/repo/inventario_estado_tramite?'+$.param(params);
					/*K.windowPrint({
						id:'windowCmCompSepePrint',
						title: "Reporte / Relación de Comprobantes de Sepelios Atendidos",
						url: url
					});*/
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				var $section6 = $mainPanel.find('#section6');
				$section6.find('[name=btnImprimir]').click(function(){	
					var url = 'cm/repo/estado_deudores';
					/*K.windowPrint({
						id:'windowCmCompSepePrint',
						title: "Reporte / Estado de Deudores",
						url: url
					});*/
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				var $section7 = $mainPanel.find('#section7');
				$section7.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$section7.find('[name=ano]').val(d.getFullYear()); 
				$section7.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = $section7.find('[name=ano]').val();
					params.mes = $section7.find('[name=mes] :selected').val();
					params.tipo = $section7.find('[name=tipo] :selected').val();
					var url = 'cm/repo/espa_vend?'+$.param(params);
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