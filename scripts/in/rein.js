inRein = {
	states: {
		RG: {
			descr: "Registrado",
			color: "#CCC",
			label: '<span class="label label-primary">Registrado</span>'
		},
		RB: {
			descr: "Recibido",
			color: "black",
			label: '<span class="label label-danger">Recibido</span>'
		},
		X: {
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-warning">Anulado</span>'
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
	tipo_inm: {
		A: 'Alquileres',
		P: 'Playas',
		PA: 'Playas Azules'
	},
	Modulo: {
		CM: 'Cementerio',
		MH: 'Moises Heresi',
		IN: 'Inmuebles'
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			modulo: item.modulo,
			tipo_inm: item.tipo_inm

		};
	},
	init: function(){
		K.initMode({
			mode: 'in',
			action: 'inRein',
			titleBar: {
				title: 'Recibo de Ingresos'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Recibo de Ingreso',f:'num'},'Planilla','Tipo','Total',{n:'Fecha',f:'fec'}],
					data: 'ts/rein/lista',
					params: {
						//organizacion: '51a50edc4d4a13441100000e'
						modulo: 'IN'
					},
					itemdescr: 'recibo(s) de ingresos',
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+inRein.states[data.estado].label+'</td>');
						$row.append('<td>Recibo de Ingresos N&deg'+data.cod+'</td>');
						$row.append('<td>N&deg'+data.planilla+'</td>');
						$row.append('<td>'+inRein.tipo_inm[data.tipo_inm]+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						if(ciHelper.date.format.bd_ymd(data.fec)==ciHelper.date.format.bd_ymd(data.fecfin))
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						else
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+' - '+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "in/comp/reci_ing2?_id="+$(this).data('id')
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
										url: "in/comp/reci_ing2?_id="+K.tmp.data('id')
									});
								},
								'conMenInRein_pla': function(t) {
									window.open("in/comp/planilla?_id="+K.tmp.data('id'));
								},
								'conMenInRein_anu': function(t) {
									K.incomplete();
									return 0;
									inRein.windowAnular({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
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
		return inRein;
	}
);