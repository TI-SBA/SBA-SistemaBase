hoRein = {
	states: {
		RG: {
			descr: "Registrado",
			color: "#CCC",
			label: '<span class="label label-primary">Registrado</span>'
		},
		RB: {
			descr: "Recibido",
			color: "black",
			label: '<span class="label label-danger">Anulado</span>'
		},
		X: {
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-warning">Pendiente</span>'
		},
		RE: {
			descr: "Registrado Libro Efectivo",
			color: "blue",
			label: '<span class="label label-info">Reemplazado</span>'
		},
		RC: {
			descr: "Registrado Libro Cta Cte",
			color: "black",
			label: '<span class="label label-info">Reemplazado</span>'
		}
	},
	modul: {
		LM: {
			descr: "LABORATORIO",
			color: "#32a817",
			label: '<span class="label label-info">Laboratorio</span>'
		},
		AD:{
			descr: "ADICCIONES",
			color: "#283387",
			label: '<span class="label label-warning">Adicciones</span>'
		},
		MH:{
			descr: "MOISES HERESI",
			color: "black",
			label: '<span class="label label-success">Moises Heresi</span>'
		},
		TD:{
			descr: "TURNO TARDE",
			color: "black",
			label: '<span class="label label-danger">Turno Tarde</span>'
		}
	},
	tipo: {
		N: 'Normal',
		R: 'Rehabitalitaci&oacute;n'
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'hoRein',
			titleBar: {
				title: 'Recibo de Ingresos'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Modulo',{n:'Recibo de Ingreso',f:'num'},'Tipo','Total',{n:'Fecha',f:'fec'}],
					data: 'ho/rein/lista',
					/*params: {modulo: 'MH'},
					params: {modulo: 'AD'},
					params: {modulo: 'LM'},*/
					itemdescr: 'recibo(s) de ingresos',
					/*toolbarHTML: '&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
							'<option value="LM">Laboratorio Mu&ntilde;oz</option>'+
						'</select>',*/
					onContentLoaded: function($el){
							$el.find('[name=modulo]').change(function(){
								var modulo = $el.find('[name=modulo] option:selected').val();
								$grid.reinit({params: {modulo: modulo}});
							});
							
						},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+hoRein.modul[data.modulo].label+'</td>');
						//$row.append('<td>'+hoRein.states[data.estado].label+'</td>');
						$row.append('<td>Recibo de Ingresos N&deg'+data.cod+'</td>');
						$row.append('<td>'+hoRein.tipo[data.tipo]+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						if(ciHelper.date.format.bd_ymd(data.fec)==ciHelper.date.format.bd_ymd(data.fecfin))
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						else
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+' - '+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Ingresos",
								url: "in/comp/reci_ing2?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenInRein", {
							onShowMenu: function($row, menu) {
								$('#conMenInRein_pla',menu).remove();
								if($row.data('estado')=='X'){
									$('#conMenInComp_cam,#conMenInComp_pag',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenInRein_imp': function(t) {
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Ingresos",
										url: "in/comp/reci_ing2?_id="+K.tmp.data('id')
									});
								},
								'conMenInRein_anu': function(t) {
									K.incomplete();
									return 0;
									hoRein.windowAnular({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	}
};
define(
	['mg/enti','mg/orga'],
	function(mgEnti,mgOrga){
		return hoRein;
	}
);