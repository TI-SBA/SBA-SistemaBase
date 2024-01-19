agRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ag',
			action: 'agRepo',
			titleBar: {
				title: 'Reportes de Venta de Agua'
			}
		});
		
		new K.Panel({
			contentURL: 'ag/repo',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name^=fec]').datepicker();
				p.$w.find('[name=periodo]').datepicker( {
				    format: "mm-yyyy",
				    viewMode: "months", 
				    minViewMode: "months"
				});

				p.$section1 = p.$w.find('#section1');
				p.$section1.find('[name=fecini]').datepicker();
				p.$section1.find('[name=fecfin]').datepicker();
				p.$section1.find('[name=btnSelectProd]').click(function(){
					lgProd.windowSelectProducto({
						//stock: true,
						modulo:'AG',
						almacen: p.$w.find('[name=almacen] :selected').val(),
						callback: function(data){
							p.$section1.find('[name=producto]').val(data.nomb).data('data', data);
						}
					});
				});

				$.post('lg/alma/all',function(data){
					if(data!=null){
						for(var i=0;i<data.length;i++){
							p.$section1.find('[name=almacen]').append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>')
						}
					}
				},'json');

				/*p.$section1.find('[name=btnImprimir]').click(function(){
					var params = $.param({
						producto:p.$section1.find('[name=producto]').data('data')._id.$id,
						almacen:p.$section1.find('[name=almacen] :selected').val(),
						fecini: p.$section1.find('[name=fecini]').val(),
						fecfin: p.$section1.find('[name=fecfin]').val()
					});
					//window.open('lg/repo/kardex?'+params);
					K.windowPrint({
						id:'windowlgRepo1',
						title: "Reporte / Informe",
						url: 'lg/repo/kardex?'+params
					});
					//window.open('lg/repo/kardex?'+$.param(params));
				});*/

				p.$section1.find('[name=btnKardex]').click(function(e){
					e.preventDefault();
					producto = p.$section1.find('[name=producto]').data('data'),
					almacen = p.$section1.find('[name=almacen] :selected').val(),
					fecini = p.$section1.find('[name=fecini]').val(),
					fecfin = p.$section1.find('[name=fecfin]').val()
					if(producto==null){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un producto!',
							type: 'error'
						});
					}
					if(almacen==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un almacen del kardex!',
							type: 'error'
						});
					}
					if(fecini==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de inicio del kardex!',
							type: 'error'
						});
					}
					if(fecfin==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de fin del kardex!',
							type: 'error'
						});
					}
					var params = $.param({
						producto:p.$section1.find('[name=producto]').data('data')._id.$id,
						almacen:p.$section1.find('[name=almacen] :selected').val(),
						fecini: p.$section1.find('[name=fecini]').val(),
						fecfin: p.$section1.find('[name=fecfin]').val()
					});
					//window.open('lg/repo/kardex?'+params);
					K.windowPrint({
						id:'windowagRepo1',
						title: "Reporte / Informe",
						url: 'lg/repo/kardex?'+params
					});
					//window.open('lg/repo/kardex?'+$.param(params));
				});


				/**********************************************************************************
				 * REPORTE MOVIMIENTOS
				 *********************************************************************************/

				//p.$w.find('.contact-box:eq(0) button').click(function(e){
				//p.$w.find('.contact-box:eq(1) button').click(function(e){
				// p.$w.find('[name=btnMovimientos]').click(function(e){
				// 	e.preventDefault();
				// 	var valor = $(this).attr('data-type'),
				// 	ini = p.$w.find('.contact-box:eq(1) [name=fecini]').val();
				// 	fin = p.$w.find('.contact-box:eq(1) [name=fecfin]').val();
				// 	if(ini==''){
				// 		return K.notification({
				// 			title: ciHelper.titleMessages.infoReq,
				// 			text: 'Debe seleccionar una fecha de inicio!',
				// 			type: 'error'
				// 		});
				// 	}
				// 	if(fin==''){
				// 		return K.notification({
				// 			title: ciHelper.titleMessages.infoReq,
				// 			text: 'Debe seleccionar una fecha de fin!',
				// 			type: 'error'
				// 		});
				// 	}
				// 	if(valor=='pdf'){
				// 		K.windowPrint({
				// 			id:'windowcjFactPrint',
				// 			title: "Registro de Movimientos",
				// 			url: 'ag/repo/reporte_movimientos?type=pdf&ini='+ini+'&fin='+fin
				// 		});
				// 	}else if(valor=='xls'){
				// 		window.open('ag/repo/reporte_movimientos?type=xls&ini='+ini+'&fin='+fin);
				// 	}
				// });
				
				/**********************************************************************************
				 * REPORTE REGISTRO DE VENTAS
				 *********************************************************************************/
				//p.$w.find('.contact-box:eq(1) button').click(function(e){
				//p.$w.find('.contact-box:eq(2) button').click(function(e){
				p.$w.find('[name=btnVentas]').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					//periodo = p.$w.find('.contact-box:eq(2) [name=periodo]').val();
					periodo = p.$w.find('[name=periodo]').val();
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
							url: 'ag/repo/registro_ventas?type=pdf&mes='+mes+'&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('ag/repo/registro_ventas?type=xls&mes='+mes+'&ano='+ano);
					}
				});
			}
		});
	}
};
define(
	['ct/pcon','lg/prod'],
	function(ctPcon, lgProd){
		return agRepo;
	}
);