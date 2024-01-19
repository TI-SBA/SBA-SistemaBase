haRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ad',
			action: 'haRepo',
			titleBar: {
				title: 'Reportes de Hospitalizaci&oacuten; - Mois&eacute;s Heresi'
			}
		});
		
		new K.Panel({
			contentURL: 'ha/repo',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=periodo]').datepicker( {
				    format: "mm-yyyy",
				    viewMode: "months", 
				    minViewMode: "months"
				});
				/**********************************************************************************
				 * REPORTE HOSPITALIZACIONES
				 *********************************************************************************/
				p.$w.find('.contact-box:eq(0) [name=ini]').datepicker();
				p.$w.find('.contact-box:eq(0) [name=fin]').datepicker();
				p.$w.find('.contact-box:eq(0) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					tipo = p.$w.find('.contact-box:eq(0) [name=tipo] option:selected').val(),
					ini = p.$w.find('.contact-box:eq(0) [name=ini]').val(),
					fin = p.$w.find('.contact-box:eq(0) [name=fin]').val();
					if(ini==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un inicio!',
							type: 'error'
						});
					}
					if(fin==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un fin!',
							type: 'error'
						});
					}
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Reporte de Hospitalizaciones",
							url: 'ha/repo/hospitalizaciones?type=pdf&ini='+ini+'&fin='+fin+'&tipo_hosp='+tipo
						});
					}else if(valor=='xls'){
						window.open('ha/repo/hospitalizaciones?type=xls&ini='+ini+'&fin='+fin+'&tipo_hosp='+tipo);
					}
				});
				/**********************************************************************************
				 * REPORTE ALTAS
				 *********************************************************************************/
				p.$w.find('.contact-box:eq(1) [name=ini]').datepicker();
				p.$w.find('.contact-box:eq(1) [name=fin]').datepicker();
				p.$w.find('.contact-box:eq(1) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					ini = p.$w.find('.contact-box:eq(1) [name=ini]').val(),
					fin = p.$w.find('.contact-box:eq(1) [name=fin]').val();
					if(ini==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una Fecha de Inicio!',
							type: 'error'
						});
					}
					if(fin==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un Fecha de Fin!',
							type: 'error'
						});
					}
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Reporte de Altas",
							url: 'ad/paho/get_altas?ini='+ini+'&fin='+fin
						});
					}
				});
				/**********************************************************************************
				 * REPORTE RECIBOS POR CONCEPTO
				 *********************************************************************************/
			 	p.$section1 = p.$w.find('#section1');
				p.$section1.find('[name=fecini]').datepicker();
				p.$section1.find('[name=fecfin]').datepicker();
				p.$section1.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section1.find('[name=fecini]').val();
					params.fecfin = p.$section1.find('[name=fecfin]').val();
					var url = 'cj/comp/get_conceptos?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
			
				/**********************************************************************************
				 * REPORTE REGISTRO DE VENTAS
				 *********************************************************************************/
				p.$w.find('.contact-box:eq(2) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					periodo = p.$w.find('.contact-box:eq(2) [name=periodo]').val();
					if(periodo==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo!',
							type: 'error'
						});
					}
					var mes = periodo.substr(0,2),
					ano = periodo.substr(3,6);
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Registro de Ventas",
							url: 'ha/repo/registro_ventas?type=pdf&mes='+mes+'&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('ha/repo/registro_ventas?type=xls&mes='+mes+'&ano='+ano);
					}
				});
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return haRepo;
	}
);