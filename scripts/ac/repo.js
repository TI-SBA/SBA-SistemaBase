/*************************************************************************
  Seguridad: Reportes */
acRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ac',
			action: 'acRepo',
			titleBar: {
				title: 'Reportes de Seguridad'
			}
		});
		
		new K.Panel({
			contentURL: 'ac/repo',
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=desde],[name=hasta]').datepicker( {
				    format: "yyyy-mm-dd"
				});
				p.$w.find('[name=btnSelect]').click(function(){
					var $this = $(this);
					mgEnti.windowSelect({callback: function(data){
						$this.closest('.contact-box').find('[name=trabajador]').html(mgEnti.formatName(data)).data('data',data._id.$id);
					},filter: [
						{nomb: 'tipo_enti',value: 'P'},
						{nomb: 'roles.trabajador',value: {$exists: true}}
					],bootstrap: true});
				});
				/**********************************************************************************
				 * REPORTE RESUMEN PLAYAS
				 *********************************************************************************/
				p.$w.find('.contact-box:eq(0) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					desde = p.$w.find('.contact-box:eq(0) [name=desde]').val(),
					hasta = p.$w.find('.contact-box:eq(0) [name=hasta]').val(),
					trabajador = p.$w.find('.contact-box:eq(0) [name=trabajador]').data('data'),
					modulo = p.$w.find('.contact-box:eq(0) [name=modulo] option:selected').val();
					if(desde==''){
						p.$w.find('.contact-box:eq(0) [name=desde]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo!',
							type: 'error'
						});
					}
					if(desde==''){
						p.$w.find('.contact-box:eq(0) [name=desde]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo!',
							type: 'error'
						});
					}
					if(valor=='pdf'){
						/*K.windowPrint({
							id:'windowcjFactPrint',
							title: "Resumen de Ingresos de Playas",
							url: 'in/repo/ingresos_playas?type=pdf&mes='+mes+'&ano='+ano
						});*/
					}else if(valor=='xls'){
						window.open('ac/repo/logs?type=xls&desde='+desde+'&hasta='+hasta+'&modulo='+modulo+'&trabajador='+trabajador);
					}else{
						/*p.$w.find('.contact-box:eq(0) #myChart').width(p.$w.find('.contact-box:eq(0)').width());
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
						},'json');*/
					}
				});
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return acRepo;
	}
);