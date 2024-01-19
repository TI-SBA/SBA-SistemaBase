agRein = {
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
	init: function(){
		K.initMode({
			mode: 'ag',
			action: 'agRein',
			titleBar: {
				title: 'Recibo de Ingresos'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Recibo de Ingreso',f:'num'},'Planilla','Total',{n:'Fecha',f:'fec'}],
					data: 'ts/rein/lista',
					params: {
						modulo: 'AG'
					},
					itemdescr: 'recibo(s) de ingresos',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+agRein.states[data.estado].label+'</td>');
						$row.append('<td>Recibo de Ingresos N&deg'+data.cod+'</td>');
						$row.append('<td>N&deg'+data.planilla+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						if(ciHelper.date.format.bd_ymd(data.fec)==ciHelper.date.format.bd_ymd(data.fecfin))
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						else
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+' - '+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ag/comp/reci_ing?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenInRein", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='X'){
									$('#conMenInComp_cam,#conMenInComp_pag',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenInRein_imp': function(t) {
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "ag/comp/reci_ing?_id="+K.tmp.data('id')
									});
								},
								'conMenInRein_pla': function(t) {
									window.open("ag/comp/planilla?_id="+K.tmp.data('id'));
								},
								'conMenInRein_anu': function(t) {
									K.incomplete();
									return 0;
									agRein.windowAnular({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
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
		return agRein;
	}
);