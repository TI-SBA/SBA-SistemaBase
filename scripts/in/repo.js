inRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'in',
			action: 'inRepo',
			titleBar: {
				title: 'Reportes de Inmuebles y Playas'
			}
		});
		
		new K.Panel({
			contentURL: 'in/repo',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=periodo]').datepicker( {
				    format: "mm-yyyy",
				    viewMode: "months", 
				    minViewMode: "months"
				});
				/**********************************************************************************
				 * REPORTE RESUMEN PLAYAS
				 *********************************************************************************/
				p.$w.find('.contact-box:eq(0) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					periodo = p.$w.find('.contact-box:eq(0) [name=periodo]').val(),
					tipo = p.$w.find('.contact-box:eq(0) [name=mostrar] option:selected').val();
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
							title: "Resumen de Ingresos de Playas",
							url: 'in/repo/ingresos_playas?type=pdf&mes='+mes+'&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('in/repo/ingresos_playas?type=xls&mes='+mes+'&ano='+ano);
					}else{
						p.$w.find('.contact-box:eq(0) #myChart').width(p.$w.find('.contact-box:eq(0)').width());
						$.post('in/repo/ingresos_playas',{
							type:valor,
							mes:mes,
							ano:ano
						},function(playas){
							var ctx = document.getElementById("myChart").getContext("2d");
							var data = {
								labels: [],
							    datasets: [
							        {
							            label: "Playas",
							            fillColor: "rgba(151,187,205,0.5)",
							            strokeColor: "rgba(151,187,205,0.8)",
							            highlightFill: "rgba(151,187,205,0.75)",
							            highlightStroke: "rgba(151,187,205,1)",
										data: []
							        }
							    ]
							};
							for(var i=0; i<playas.length; i++){
								var play = playas[i];
								data.labels.push(play.nomb);
								data.datasets[0].data.push(play.total);
							}
							new Chart(ctx).Bar(data,{
								 showTooltips: false,
								onAnimationComplete: function () {
									var ctx = this.chart.ctx;
									ctx.font = this.scale.font;
									ctx.fillStyle = this.scale.textColor;
									ctx.textAlign = "center";
									ctx.textBaseline = "bottom";
									this.datasets.forEach(function(dataset){
										dataset.bars.forEach(function(bar) {
											ctx.fillText(ciHelper.formatMon(bar.value), bar.x, bar.y - 5);
										});
									});
							    }
							});
						},'json');
					}
				});
				/**********************************************************************************
				 * REPORTE LISTADO PLAYAS
				 *********************************************************************************/
				p.$w.find('.contact-box:eq(1) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					periodo = p.$w.find('.contact-box:eq(1) [name=periodo]').val(),
					tipo = p.$w.find('.contact-box:eq(1) [name=mostrar] option:selected').val();
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
							title: "Listado de Playas",
							url: 'in/repo/listado_playas?type=pdf&mes='+mes+'&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('in/repo/listado_playas?type=xls&mes='+mes+'&ano='+ano);
					}
				});


				/***********************************************************************************
				* REPORTE DE LIQUIDACIONES
				***********************************************************************************/
				p.$section3 = p.$w.find('#section3');
				p.$section3.find('[name=fecini_pagos]').datepicker( {
					format: "yyyy-mm-dd"
				});
				p.$section3.find('[name=fecfin_pagos]').datepicker( {
					format: "yyyy-mm-dd"
				});
				p.$section3.find('[name=fecfin_mora]').datepicker( {
					format: "yyyy-mm-dd"
				});
				p.$section3.find('[name=btnSelectEnti]').click(function(){
					mgEnti.windowSelect({bootstrap:true, callback:function(data){
						p.$section3.find('[name=arrendatario]').val(mgEnti.formatName(data)).data('data',data);
						p.$section3.find('[name=inmueble]').empty();
						K.block();
						$.post('in/movi/get_by_titu',{_id:data._id.$id},function(titu){
							for(i in titu)
							{
								p.$section3.find('[name=inmueble]').append('<option value="'+i+'">'+titu[i].inmueble.direccion+'</option>');
							}
							K.unblock();
						},'json');
					}});
				});
				p.$section3.find('[name=btnGeneratePDF]').click(function(){
					var params = {
						titular: p.$section3.find('[name=arrendatario]').data('data')._id.$id,
						inmueble: p.$section3.find('[name=inmueble] :selected').val(),
						fecini_pagos: p.$section3.find('[name=fecini_pagos]').val(),
						fecfin_pagos: p.$section3.find('[name=fecfin_pagos]').val(),
						fecfin_mora: p.$section3.find('[name=fecfin_mora]').val(),
					};
					if(params.fecfin_pagos==''){
						delete params['fecfin_pagos'];
					}
					if(params.fecini_pagos==''){
						delete params['fecini_pagos'];
					}
					if(params.fecfin_mora==''){
						delete params['fecfin_mora'];
					}
					window.open('in/repo/liquidaciones?'+$.param(params));
				});
				/***********************************************************************************
				* RECORD DE PAGOS
				***********************************************************************************/
				p.$section4 = p.$w.find('#section4');
				p.$section4.find('[name=inmueble]').change(function(){
					p.$section4.find('[name=contrato]').empty();
					K.block();
					$.post('in/movi/all_contratos',{titular: p.$section4.find('[name=arrendatario]').data('data')._id.$id,inmueble: p.$section4.find('[name=inmueble] :selected').val()},function(conts){
						for(i in conts)
						{
							p.$section4.find('[name=contrato]').append('<option value="'+conts[i]._id.$id+'">'+moment(conts[i].fecini.sec,"X").format('DD/MM/YYYY')+' - '+moment(conts[i].fecfin.sec,"X").format('DD/MM/YYYY')+'</option>');
						}
						K.unblock();
					},'json');
				});
				p.$section4.find('[name=btnSelectEnti]').click(function(){
					mgEnti.windowSelect({bootstrap:true, callback:function(data){
						p.$section4.find('[name=arrendatario]').val(mgEnti.formatName(data)).data('data',data);
						p.$section4.find('[name=inmueble]').empty();
						//p.$section4.find('[name=contrato]').empty();
						K.block();
						$.post('in/movi/get_by_titu',{_id:data._id.$id},function(titu){
							for(i in titu)
							{
								p.$section4.find('[name=inmueble]').append('<option value="'+i+'">'+titu[i].inmueble.direccion+'</option>');
							}
							p.$section4.find('[name=inmueble]').change();
							K.unblock();
						},'json');
					}});
				});
				p.$section4.find('[name=btnGeneratePDF]').click(function(){
					var params = {
						contrato: p.$section4.find('[name=contrato] :selected').val(),
						formato: p.$section4.find('[name=formato] :selected').val()
					};
					window.open('in/repo/record_pago?'+$.param(params));
				});
				/***********************************************************************************
				* RECORD DE PAGOS (ACTAS)
				***********************************************************************************/
				p.$section4_1 = p.$w.find('#section4_1');
				p.$section4_1.find('[name=btnSelectEnti]').click(function(){
					mgEnti.windowSelect({bootstrap:true, callback:function(data){
						p.$section4_1.find('[name=arrendatario]').val(mgEnti.formatName(data)).data('data',data);
						p.$section4_1.find('[name=contrato]').empty();
						K.block();
						$.post('in/acta/get_by_titu',{_id:data._id.$id},function(acta){
							for(i in acta)
							{
								p.$section4_1.find('[name=contrato]').append('<option value="'+acta[0]._id.$id+'">'+acta[0].inmueble.tipo.nomb+' - '+acta[0].inmueble.direccion+'</option>');
							}
							//p.$section4.find('[name=contrato]').change();
							K.unblock();
						},'json');
					}});
				});
				p.$section4_1.find('[name=btnGeneratePDF]').click(function(){
					var params = {
						contrato: p.$section4_1.find('[name=contrato] :selected').val(),
						formato: p.$section4_1.find('[name=formato] :selected').val()
					};
					window.open('in/repo/record_pago_actas?'+$.param(params));
				});



				/***********************************************************************************
				* SITUACION ACTUAL DE LOS INMUEBLES
				***********************************************************************************/
				p.$section5 = p.$w.find('#section5');
				p.$section5.find('[name=tipo]').change(function(){
					var $cbo = p.$section5.find('[name=sublocal]').empty();
					$.post('in/subl/all_tipo',{_id: p.$section5.find('[name=tipo] option:selected').val()},function(conts){
						for(i in conts){
							$cbo.append('<option value="'+conts[i]._id.$id+'">'+conts[i].nomb+'</option>');
						}
					},'json');
				});
				p.$section5.find('[name=btnGeneratePDF]').click(function(){
					var params = {
						sublocal: p.$section5.find('[name=sublocal] :selected').val()
					};
					K.windowPrint({
						id:'windowPrint',
						title: "Situaci&oacute;n Actual de los Inmuebles",
						url: 'in/repo/situacion_actual?type=pdf&'+$.param(params)
					});
				});
				p.$section5.find('[name=btnGenerateXLS]').click(function(){
					var params = {
						sublocal: p.$section5.find('[name=sublocal] :selected').val()
					};
					window.open('in/repo/situacion_actual?type=xls&'+$.param(params));
				});
				/***********************************************************************************
				* DEUDORES ACTUALES
				***********************************************************************************/
				p.$section6 = p.$w.find('#section6');
				p.$section6.find('[name=btnGeneratePDF]').click(function(){
					//var periodo = p.$section6.find('[name=periodo]').val(),
					//mes = periodo.substr(0,2),
					//ano = periodo.substr(3,6);
					//window.open('in/repo/deudores?ano='+ano+'&mes='+mes);
					window.open('in/repo/deudores');
				});
				$.post('in/tipo/all',function(data){
					var $cbo = p.$section5.find('[name=tipo]');
					for(var i=0; i<data.length; i++){
						$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
					}
					$cbo.change();
					K.unblock();
				},'json');
				/***********************************************************************************
				* CONTRATOS POR VENCER
				***********************************************************************************/
				p.$section7 = p.$w.find('#section7');
				//p.$section7.find('[name=btnGenerarReporte]').click(function(){
					var $grid = new K.grid({
						$el: p.$w.find('#grid_cont_x_venc'),
						cols: ['Titular','Inmueble'],
						data: 'in/repo/cont_por',
						params: {},
						itemdescr: 'contratos por vencer(s)',
						toolbarHTML: 'Periodo: <input type="text" class="form-control" name="periodo">',
						onContentLoaded: function($el){
							p.$section7.find('[name=periodo]').datepicker( {
								format: "mm-yyyy",
								viewMode: "months",
								minViewMode: "months"
							}).on('changeDate', function(ev){
								console.log(ev);
								var periodo = moment(ev.date).format('MM-YYYY');
								if(periodo==''){
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar un periodo!',
										type: 'error'
									});
								}
								var mes = periodo.substr(0,2);
								var ano = periodo.substr(3,6);
								$grid.reinit({params:{ano:ano,mes:mes}});
							});
							/*$el.find('[name=periodo]').change(function(){

							});*/
						},
						onLoading: function(){ K.block(); },
						onComplete: function(){ K.unblock(); },
						pagination: false,
						search: false,
						load: function(data,$tbody){
							if(data!=null){
								if(data.length>0){
									for(var i=0;i<data.length;i++){
										var $row = $('<tr />');
										$row.append('<td>'+mgEnti.formatName(data[i].titular)+'</td>');
										$row.append('<td>'+data[i].inmueble.direccion+'</td>');
										$tbody.append($row);
									}
								}
							}
							return $tbody;
						}
					});
				//});

				/**********************************************************************************
				 * REPORTE REGISTRO DE VENTAS
				 *********************************************************************************/
				p.$w.find('[name=repoRegiVentas] [name=periodo]').datepicker( {
				    format: "mm-yyyy",
				    viewMode: "months", 
				    minViewMode: "months"
				});
				p.$w.find('[name=repoRegiVentas] button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					periodo = p.$w.find('[name=repoRegiVentas] [name=periodo]').val();
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
							url: 'in/repo/registro_ventas?type=pdf&mes='+mes+'&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('in/repo/registro_ventas?type=xls&mes='+mes+'&ano='+ano);
					}
				});
				/**********************************************************************************
				 * REPORTE LISTADO INMUEBLES
				 *********************************************************************************/
				p.$w.find('[name=repoListInmu] button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type');
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Listado de Inmuebles",
							url: 'in/repo/listado_inmuebles?type=pdf&mes'
						});
					}else if(valor=='xls'){
						window.open('in/repo/listado_inmuebles?type=xls');
					}
				});
				/**********************************************************************************
				 * REPORTE LISTADO INMUEBLES
				 *********************************************************************************/
				p.$w.find('[name=repoListArreInmu] button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type');
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Listado de Inmuebles",
							url: 'in/repo/listado_inmuebles?type=pdf&mes'
						});
					}else if(valor=='xls'){
						window.open('in/repo/listado_arrendatarios?type=xls');
					}
				});
				/**********************************************************************************
				 * REPORTE DAOT
				 *********************************************************************************/
				p.$w.find('[name=repoDaot] [name=ano]').datetimepicker( {
				    format: "YYYY",
				    viewMode: "years",
				}).val(ciHelper.date.get.now_y());
				p.$w.find('[name=repoDaot] button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					ano = p.$w.find('[name=repoDaot] [name=ano]').val();
					if(ano==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo!',
							type: 'error'
						});
					}
					if(valor=='pdf'){
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Registro de Ventas",
							url: 'in/repo/daot?type=pdf&ano='+ano
						});
					}else if(valor=='xls'){
						window.open('in/repo/daot?type=xls&ano='+ano);
					}
				});
				/***********************************************************************************
				* REPORTE DE CONTINUIDAD
				***********************************************************************************/
				p.$continuidad = p.$w.find('#section_continuidad');
				p.$continuidad.find('[name=fecini_comp]').datepicker( {
					format: "yyyy-mm-dd"
				});
				p.$continuidad.find('[name=fecfin_comp]').datepicker( {
					format: "yyyy-mm-dd"
				});
				/*
					p.$continuidad.find('[name=btnSelectEnti]').click(function(){
						mgEnti.windowSelect({bootstrap:true, callback:function(data){
							p.$continuidad.find('[name=arrendatario]').val(mgEnti.formatName(data)).data('data',data);
							p.$continuidad.find('[name=inmueble]').empty();
							K.block();
							$.post('in/movi/get_by_titu',{_id:data._id.$id},function(titu){
								for(i in titu)
								{
									p.$continuidad.find('[name=inmueble]').append('<option value="'+i+'">'+titu[i].inmueble.direccion+'</option>');
								}
								K.unblock();
							},'json');
						}});
					});
				*/
				p.$continuidad.find('[name=btnGenerateXLS]').click(function(e){
					e.preventDefault();
					var params = {
						fecini: p.$continuidad.find('[name=fecini_comp]').val(),
						fecfin: p.$continuidad.find('[name=fecfin_comp]').val(),
						tipo_inm: p.$continuidad.find('[name=tipo_caja]').val(),
						type: $(this).attr('data-type'),
					};

					// diferencia en dias entre 2 fechas
					//var d1 = p.$continuidad.find('[name=fecini_comp]').datepicker('getDate'),
			        //    d2 = p.$continuidad.find('[name=fecfin_comp]').datepicker('getDate'),
			        var   diff = 0;

					//if (d1 && d2) {
            		//	diff = Math.floor((d2.getTime() - d1.getTime()) / 86400000); // ms por dia
					//}
					
					if(params.fecini==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo de inicio!',
							type: 'error'
						});
					}
					if(params.fecfin==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo de fin!',
							type: 'error'
						});
					}
					if(params.tipo==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un tipo de caja!',
							type: 'error'
						});
					}
					if(params.tipo==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un tipo de caja!',
							type: 'error'
						});
					}
					if(diff>7){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Solo se puede revisar la diferencia en un periodo de 7 d�as!',
							type: 'error'
						});
					}
					window.open('in/repo/continuidad_comprobantes?'+$.param(params));
				});
				/*****************************************************************************
				* REPORTE DE CONTINGENCIA PEI
				*****************************************************************************/
				p.$contingencia = p.$w.find('#section_contingencia');
				p.$contingencia.find('[name=fecha]').datepicker();
				p.$contingencia.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.fecha = p.$contingencia.find('[name=fecha]').val();
					var url = 'in/repo/generar_resumen_contingencia_pei?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
				/*****************************************************************************
				* REPORTE DE ESTADO DE DEUDORES 
				*****************************************************************************/
				p.$estadodeudor = p.$w.find('#section_estadodeudor');
				//p.$estadodeudor.find('[name=fecha]').datepicker();
				p.$estadodeudor.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					//params.fecha = p.$estadodeudor.find('[name=fecha]').val();
					var url = '/freporte/estado_deudores';
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
				
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return inRepo;
	}
);