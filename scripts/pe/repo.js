/*************************************************************************
  Personal: Reportes */
peRepo2 = {
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peRepoGene',
			titleBar: {
				title: 'Reportes'
			}
		});
		if($('#pageWrapper [child=repo]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pe/navg/repo',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="repo" />');
					$p.find("[name=peRepo]").after( $row.children() );
				}
				$p.find('[name=peRepo]').data('repo',$('#pageWrapper [child=repo]:first').data('repo'));
				$p.find('[name=peRepoInci]').click(function(){ peRepoInci.init(); });
				$p.find('[name=peRepoGene]').click(function(){ peRepo.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/repo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('name')), 800 );
				}).eq(0).click().find('ul').addClass("ui-state-highlight");
				$section1 = $mainPanel.find('#section1');
				$mainPanel.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear());
				$.post('pe/cont/all',function(data){
					var $cbo = $section1.find('[name=tipo]');
					for(i=0;i<data.length;i++){
						$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
					}
				},'json');
				$section1.find('[name=btnExportar]').click(function(){	
					window.open('pe/repo/conc?tipo='+$section1.find('[name=tipo] :selected').val());
				}).button({icons: {primary: 'ui-icon-print'}});		
				$section2 = $mainPanel.find('#section2');
				$section2.find('[name=btnConc]').click(function(){
					peConc.windowSelect({callback:function(data){
						$section2.find('[name=conc]').html(data.nomb).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section2.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.ano = $section2.find('[name=ano]').val();
					params.mes = $section2.find('[name=mes] :selected').val();
					params._id = $section2.find('[name=conc]').data('data');
					if(params._id==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar un Concepto!',type: 'error'});
					}
					var url = 'pe/repo/conc_orga?'+$.param(params);
					K.windowPrint({
						id:'windowpeConcOrga',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section3 = $mainPanel.find('#section3');
				$section3.find('[name=btnExportar]').click(function(){	
					window.open('pe/repo/trabs');
				}).button({icons: {primary: 'ui-icon-print'}});
				$section5 = $mainPanel.find('#section5');
				$section5.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section5.find('[name=mes] :selected').val(),
						ano:$section5.find('[name=ano]').val()
					};
					//window.open('pe/repo/esco?'+$.param(params));
					var url = 'pe/repo/esco?'+$.param(params);
					K.windowPrint({
						id:'windowpeEsco',
						title: "Reporte / Informe",
						url: url
					});
				});
				$section6 = $mainPanel.find('#section6');
				$section6.find('[name=btnExportar]').click(function(){
					var params = {
						mes:$section6.find('[name=mes] :selected').val(),
						ano:$section6.find('[name=ano]').val()
					};
					window.open('pe/repo/pdt601?'+$.param(params));
				});
				$section7 = $mainPanel.find('#section7');
				$section7.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section7.find('[name=mes] :selected').val(),
						ano:$section7.find('[name=ano]').val()
					};
					window.open('pe/repo/pdt601?'+$.param(params));
				});
				$section9 = $mainPanel.find('#section9');
				$section9.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section9.find('[name=mes] :selected').val(),
						ano:$section9.find('[name=ano]').val(),
						tipo:$section9.find('[name=tipo] :selected').val()
					};
					K.windowPrint({
						id:'windowpeLeyes',
						title: "Reporte / Informe",
						url: 'pe/repo/leyes?'+$.param(params)
					});
				}).button({icons: {primary: 'ui-icon-print'}});
					
						
				$section10 = $mainPanel.find('#section10');
				$section10.find('[name=btnConc]').click(function(){
					ciSearch.windowSearchOrga({callback:function(data){
						$section10.find('[name=conc]').html(data.nomb).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section10.find('[name=btnExportar]').click(function(){
					params = new Object;
					params.ano = $section10.find('[name=ano]').val();
					params.mes = $section10.find('[name=mes] :selected').val();
					params.orga = $section10.find('[name=conc]').data('data');
					if(params.orga==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar un Concepto!',type: 'error'});
					}
					K.windowPrint({
						id:'windowpePlani',
						title: "Reporte / Informe",
						url: 'pe/repo/planilla_cas_excel?'+$.param(params)
					});
					
				}).button({icons: {primary: 'ui-icon-print'}});
				
				$section10 = $mainPanel.find('#section10');
				$section10.find('[name=btnConc]').click(function(){
					ciSearch.windowSearchOrga({callback:function(data){
						$section10.find('[name=conc]').html(data.nomb).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section10.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.ano = $section10.find('[name=ano]').val();
					params.mes = $section10.find('[name=mes] :selected').val();
					params.orga = $section10.find('[name=conc]').data('data');
					if(params.orga==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar una Organizacion!',type: 'error'});
					}
					K.windowPrint({
						id:'windowpePlan',
						title: "Reporte / Informe",
						url: 'pe/repo/planilla_cas_pdf?'+$.param(params)
					});
				}).button({icons: {primary: 'ui-icon-print'}});
								
				
				$section11 = $mainPanel.find('#section11');
				$section11.find('[name=btnImprimir]:eq(0)').click(function(){
					var params = {
						mes:$section11.find('[name=mes]:eq(0) :selected').val(),
						ano:$section11.find('[name=ano]:eq(0)').val()
					};
					K.windowPrint({
						id:'windowpeGenerePPFT',
						title: "Reporte / Informe",
						url: 'pe/repo/desc?'+$.param(params)
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section11.find('[name=btnImprimir]:eq(1)').click(function(){
					var params = {
						mes:$section11.find('[name=mes]:eq(1) :selected').val(),
						ano:$section11.find('[name=ano]:eq(1)').val(),
						cond:$section11.find('[name=cond] :selected').val()
					};
					K.windowPrint({
						id:'windowpeGenereOtrosDesc',
						title: "Reporte / Informe",
						url: 'pe/repo/desc_otros?'+$.param(params)
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				///
				$section13 = $mainPanel.find('#section13');
				$section13.find('[name=btnExportar]').click(function(){	
					window.open('pe/turn/reporte');
				}).button({icons: {primary: 'ui-icon-print'}});
				////
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

peRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'pe',
			action: 'peRepo',
			titleBar: {
				title: 'Reportes de Personal'
			}
		});
		
		new K.Panel({
			contentURL: 'pe/repo/index2',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				var $mainPanel = p.$w;
				
				$section1 = $mainPanel.find('#section1');
				//$mainPanel.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear());
				$.post('pe/cont/all',function(data){
					var $cbo = $section1.find('[name=tipo]');
					for(i=0;i<data.length;i++){
						$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
					}
				},'json');
				$section1.find('[name=btnExportar]').click(function(){	
					window.open('pe/repo/conc?tipo='+$section1.find('[name=tipo] :selected').val());
				});
				$section2 = $mainPanel.find('#section2');
				$section2.find('[name=btnConc]').click(function(){
					peConc.windowSelect({callback:function(data){
						$section2.find('[name=conc]').html(data.nomb).data('data',data._id.$id);
					}});
				});
				$section2.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.ano = $section2.find('[name=ano]').val();
					params.mes = $section2.find('[name=mes] :selected').val();
					params._id = $section2.find('[name=conc]').data('data');
					if(params._id==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar un Concepto!',type: 'error'});
					}
					var url = 'pe/repo/conc_orga?'+$.param(params);
					K.windowPrint({
						id:'windowpeConcOrga',
						title: "Reporte / Informe",
						url: url
					});
				});
				$section3 = $mainPanel.find('#section3');
				$section3.find('[name=btnExportar]').click(function(){	
					window.open('pe/repo/trabs');
				}).button({icons: {primary: 'ui-icon-print'}});
				$section5 = $mainPanel.find('#section5');
				$section4 = $mainPanel.find('#section4');
				$section4.find('[name=btnExportar]').click(function(){
					var params = {
						mes:$section4.find('[name=mes] :selected').val(),
						ano:$section4.find('[name=ano]').val()
					};
					window.open('pe/repo/afpnet?'+$.param(params));
				});
				$section5.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section5.find('[name=mes] :selected').val(),
						ano:$section5.find('[name=ano]').val()
					};
					//window.open('pe/repo/esco?'+$.param(params));
					var url = 'pe/repo/esco?'+$.param(params);
					K.windowPrint({
						id:'windowpeEsco',
						title: "Reporte / Informe",
						url: url
					});
				});
				$section6 = $mainPanel.find('#section6');
				$section6.find('[name=btnExportar]').click(function(){
					var params = {
						mes:$section6.find('[name=mes] :selected').val(),
						ano:$section6.find('[name=ano]').val()
					};
					window.open('pe/repo/pdt601?'+$.param(params));
				});
				$section7 = $mainPanel.find('#section7');
				$section7.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section7.find('[name=mes] :selected').val(),
						ano:$section7.find('[name=ano]').val()
					};
					window.open('pe/repo/planilla_cas_excel?'+$.param(params));
				});
				$section8 = $mainPanel.find('#section8');
				$section8.find('[name=btnImprimir]').click(function(){
					var params = {
						tipo:$section8.find('[name=tipo] :selected').val(),
						mes:$section8.find('[name=mes] :selected').val(),
						ano:$section8.find('[name=ano]').val()
					};
					window.open('pe/repo/leyes?'+$.param(params));
				});
				$section9 = $mainPanel.find('#section9');
				$section9.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section9.find('[name=mes] :selected').val(),
						ano:$section9.find('[name=ano]').val()
					};
					K.windowPrint({
						id:'windowpeLeyes',
						title: "Reporte / Descuentos",
						url: 'pe/repo/desc?'+$.param(params)
					});
				});
					
						
				$section10 = $mainPanel.find('#section10');
				$section10.find('[name=btnImprimir]').click(function(){
					var params = {
						mes:$section10.find('[name=mes] :selected').val(),
						ano:$section10.find('[name=ano]').val(),
						cond:$section10.find('[name=cond] :selected').val(),
					};
					K.windowPrint({
						id:'windowpeLeyes',
						title: "Reporte / Otros Descuentos",
						url: 'pe/repo/desc_otros?'+$.param(params)
					});
				});
				
				/*$section10 = $mainPanel.find('#section10');
				$section10.find('[name=btnConc]').click(function(){
					ciSearch.windowSearchOrga({callback:function(data){
						$section10.find('[name=conc]').html(data.nomb).data('data',data._id.$id);
					}});
				});
				$section10.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.ano = $section10.find('[name=ano]').val();
					params.mes = $section10.find('[name=mes] :selected').val();
					params.orga = $section10.find('[name=conc]').data('data');
					if(params.orga==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar una Organizacion!',type: 'error'});
					}
					K.windowPrint({
						id:'windowpePlan',
						title: "Reporte / Informe",
						url: 'pe/repo/planilla_cas_pdf?'+$.param(params)
					});
				});*/
								
				
				$section11 = $mainPanel.find('#section11');
				$section11.find('[name=fecini]').datepicker({
					format:'yyyy-mm-dd'
				});
				$section11.find('[name=fecfin]').datepicker({
					format:'yyyy-mm-dd'
				});
				$section11.find('[name=btnImprimir]').click(function(){
					var params = {
						fecini:$section11.find('[name=fecini]').val(),
						fecfin:$section11.find('[name=fecfin]').val()
					};
					K.windowPrint({
						id:'windowpeGenerePPFT',
						title: "Reporte / Informe",
						url: 'pe/repo/asistencia_contratados?'+$.param(params)
					});
				});
				$section11.find('[name=btnImprimir2]').click(function(){
					var params = {
						fecini:$section11.find('[name=fecini]').val(),
						fecfin:$section11.find('[name=fecfin]').val()
					};
					K.windowPrint({
						id:'windowpeGenerePPFT',
						title: "Reporte / Informe",
						url: 'pe/repo/asistencia_marcacion?'+$.param(params)
					});
				});
				$section12 = $mainPanel.find('#section12');
				$section12.find('[name=btnExportar]').click(function(){
					var params = {
						mes:$section12.find('[name=mes] :selected').val(),
						ano:$section12.find('[name=ano]').val()
					};
					K.windowPrint({
						id:'windowpeGenereOtrosDesc',
						title: "Reporte / Informe",
						url: 'pe/repo/afpnet?'+$.param(params)
					});
				});
				///
				$section13 = $mainPanel.find('#section13');
				$section13.find('[name=btnExportar]').click(function(){	
					window.open('pe/turn/reporte');
				}).button({icons: {primary: 'ui-icon-print'}});
				////
			}
		});
	}
};
define(
	['ct/pcon','pe/conc','mg/enti'],
	function(ctPcon, peConc, mgEnti){
		return peRepo;
	}
);