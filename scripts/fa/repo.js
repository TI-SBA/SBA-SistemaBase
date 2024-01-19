faRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'fa',
			action: 'faRepo',
			titleBar: {
				title: 'Reportes de Farmacia - Mois&eacute;s Heresi'
			}
		});
		
		new K.Panel({
			contentURL: 'fa/repo',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name^=fec]').datepicker();
				p.$w.find('[name=periodo]').datepicker( {
				    format: "mm-yyyy",
				    viewMode: "months", 
				    minViewMode: "months"
				});
				/**********************************************************************************
				 * REPORTE MOVIMIENTOS
				 *********************************************************************************/
				p.$w.find('[name=repo1] [name=btnProd]').click(function(e){
					e.preventDefault();
					faProd.windowSelect({callback: function(data){
						p.$w.find('[name=repo1] [name=prod]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name=repo1] [name=btnGenerar]').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					prod = p.$w.find('[name=repo1] [name=prod]').data('data'),
					ini = p.$w.find('[name=repo1] [name=fecini]').val(),
					fin = p.$w.find('[name=repo1] [name=fecfin]').val();
					if(prod==null){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un producto!',
							type: 'error'
						});
					}else prod = prod._id.$id;
					if(ini==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de inicio!',
							type: 'error'
						});
					}
					if(fin==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de fin!',
							type: 'error'
						});
					}
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Reporte de Movimientos",
							url: 'fa/repo/reporte_movimientos?type=pdf&prod='+prod+'&ini='+ini+'&fin='+fin
						});
					}else if(valor=='xls'){
						window.open('fa/repo/reporte_movimientos?type=xls&prod='+prod+'&ini='+ini+'&fin='+fin);
					}
				});
				/**********************************************************************************
				 * REPORTE REGISTRO DE VENTAS
				 *********************************************************************************/
				p.$w.find('[name=repo2] button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					periodo = p.$w.find('[name=repo2] [name=periodo]').val();
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
							url: 'fa/repo/registro_ventas?type=pdf&mes='+mes+'&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('fa/repo/registro_ventas?type=xls&mes='+mes+'&ano='+ano);
					}
				});
				/**********************************************************************************
				 * REPORTE REGISTRO DE VENTAS
				 *********************************************************************************/
				p.$w.find('[name=repo3] button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type');
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Listado de Productos",
							url: 'fa/repo/listado_prod?type=pdf'
						});
					}else if(valor=='xls'){
						window.open('fa/repo/listado_prod?type=xls');
					}
				});
			}
		});
	}
};
define(
	['ct/pcon','fa/prod'],
	function(ctPcon,faProd){
		return faRepo;
	}
);