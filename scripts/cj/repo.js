/*************************************************************************
Caja: Reportes */
cjRepo = {
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjRepo',
			titleBar: {
				title: 'Reportes'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/repo',
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
				$section3 = $mainPanel.find('#section3');	
				$section3.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = $section3.find('[name=ano]').val();
					params.mes = $section3.find('[name=mes] :selected').val();
					var url = 'cj/repo/nivi?'+$.param(params);
					window.open(url);
					/*K.windowPrint({
						id:'windowCjNiviRepo',
						title: "Reporte / Informe",
						url: url
					});*/
				}).button({icons: {primary: 'ui-icon-print'}});
				$section4 = $mainPanel.find('#section4');	
				$section4.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = $section4.find('[name=ano]').val();
					var url = 'cj/repo/daot_cm?'+$.param(params);
					K.windowPrint({
						id:'windowCjDaotCmRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section5 = $mainPanel.find('#section5');	
				$section5.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = $section5.find('[name=ano]').val();
					var url = 'cj/repo/daot_in?'+$.param(params);
					K.windowPrint({
						id:'windowCjDaotInRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section7 = $mainPanel.find('#section7');	
				$section7.find('[name=btnImprimir1]').click(function(){	
					params = new Object;
					params.ano = $section7.find('[name=ano]').val();
					var url = 'cj/repo/ingr_anu?'+$.param(params);
					K.windowPrint({
						id:'windowCjIngrAnuInGraph',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section7.find('[name=ano1]').val(new Date().getFullYear());
				$section7.find('[name=ano2]').val(new Date().getFullYear()+1);
				$section7.find('[name=btnImprimir2]').click(function(){	
					params = new Object;
					params.ano1 = $section7.find('[name=ano1]').val();
					params.ano2 = $section7.find('[name=ano2]').val();
					var url = 'cj/repo/ingr_com?'+$.param(params);
					K.windowPrint({
						id:'windowCjIngrComInGraph',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section9 = $mainPanel.find('#section9');
				$section9.find('[name=btnSelect]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$section9.find('[name=orga]').html(data.nomb).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section9.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.organizacion = $section9.find('[name=orga]').data('data');
					var url = 'cj/repo/deud?'+$.param(params);
					K.windowPrint({
						id:'windowCjDeudores',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section10 = $mainPanel.find('#section10');
				$section10.find('[name=btnSelectEspa]').click(function(){
					//
					inLoca.selectEspAll({callback: function(data){
						$section10.find('[name=espa]').html(data.descr+" - "+data.ubic.ref).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section10.find('[name=btnSelectArre]').click(function(){
					ciSearch.windowSearchEnti({$window: $section9,callback: function(data){
						$section10.find('[name=arre]').html(ciHelper.enti.formatName(data)).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section10.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.espacio = $section10.find('[name=espa]').data('data');
					params.arrendatario = $section10.find('[name=arre]').data('data');
					var url = 'cj/repo/cont?'+$.param(params);
					K.windowPrint({
						id:'windowCjControlDeuda',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section11 = $mainPanel.find('#section11');
				$section11.find('[name=btnRec]').click(function(){
					tsRein.windowSelect({$window: $section11,modulo: 'CM',callback: function(data){
						$section11.find('[name=recibo]').html(ciHelper.date.format.bd_ymd(data.fec)).data('data',data._id.$id);
						if(data.fecfin!=null){
							if(ciHelper.date.format.bd_ymd(data.fec)!=ciHelper.date.format.bd_ymd(data.fecfin)){
								$section11.find('[name=recibo]').append(' - '+ciHelper.date.format.bd_ymd(data.fecfin));
							}
						}
						$section11.find('[name=btnImprimir]').click();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section11.find('[name=btnImprimir]').click(function(){
					K.windowPrint({
						id:'windowCjControlDeuda',
						title: "Reporte / Informe",
						url: 'cj/repo/reci_ing?_id='+$section11.find('[name=recibo]').data('data')
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section12 = $mainPanel.find('#section12');
				var tmp = new Date();
				$section12.find('[name=periodo]').datepicker( {
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[tmp.getMonth()]+' '+tmp.getFullYear())
			    .data('mes',tmp.getMonth()).data('ano',tmp.getFullYear());
				$section12.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$section12.find('[name=orga]').html(data.nomb).data('data',data._id.$id);
						$section12.find('[name=btnImprimir]').click();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section12.find('[name=btnImprimir]').click(function(){
					window.open('cj/repo/registro_ventas?orga='+$section12.find('[name=orga]').data('data')+
							'&mes='+(parseInt($section12.find('[name=periodo]').data('mes'))+1)+
							'&ano='+$section12.find('[name=periodo]').data('ano'));
				}).button({icons: {primary: 'ui-icon-print'}});
				if(K.session.enti.roles.trabajador.programa!=null)
					$section12.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb).data('data',K.session.enti.roles.trabajador.programa._id.$id);
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
		$('#pageWrapperMain').layout();
		K.unblock({$element: $('#pageWrapperMain')});
	}	
};