chCmmo = {
	estado:{
		"0":"VACIA",
		"1":"OCUPADA"
		
	},
	
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cama: item.cama,
			paciente: item.paciente,
			pabellon: item.pabellon,
			sala: item.sala,
			fecreg: item.fecreg,
			diag: item.diag,
			estado: item.estado
			
			
		};
	},
	init: function(){
		K.initMode({
			mode: 'ch',
			action: 'chCmmo',
			titleBar: {
				title: 'Movimientos de Camas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Numero de Cama','Paciente','Pabellon','Sala','Estado'],
					data: 'ch/cmmo/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							chCmmo.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cama.cama+'</td>');
						var paciente_ = '----';
						if(data.paciente!=null){
							if(data.paciente.paciente!=null){
								paciente_ = mgEnti.formatName(data.paciente.paciente);
							}
							else{
								paciente_ = mgEnti.formatName(data.paciente);
							}
						}
						$row.append('<td>'+paciente_+'</td>');
						$row.append('<td>'+data.pabellon+'</td>');
						$row.append('<td>'+data.sala+'</td>');
						$row.append('<td>'+chCmmo.estado[data.estado]+'</td>');
						
						$row.data('id',data._id.$id).dblclick(function(){
							chCmmo.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenMhMov", {
							bindings: {
								'conMenMhMov_ingr': function(t) {
									chCmmo.windowIngreso({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenMhMov_movi': function(t) {
									chCmmo.windowTraslado({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},							
								'conMenMhMov_tras': function(t) {
									chCmmo.windowTraslado({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowIngreso: function(p){
		new K.Modal({ 
			id: 'windowIngreso',
			title: 'Ingreso de Paciente a Cama',
			contentURL: 'ch/cmmo/edit',
			width: 700,
			height: 300,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								K.sendingInfo();
								var paciente = p.$w.find('[name=paciente]').data('data');
								var data = {
									//DATOS DEL cmmoENTE
									_id: p.id,
									paciente: chHospi.dbRel(p.$w.find('[name=paciente]').data('data')),
									cama:{
									_id: p.$w.find('[name=_id]').val(),	
									cama:p.$w.find('[name=cama]').val(),	
									},
									pabellon:p.$w.find('[name=pabellon]').val(),
									sala:p.$w.find('[name=sala]').val(),
									estado:p.$w.find('[name=estado]').val(),
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ch/cmmo/paciente",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Paciente Agregado a Cama!"});
									K.closeWindow(p.$w.attr('id'));
									chCmmo.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowIngreso');
				p.$w.find("[name=btnDiag]").click(function(){
					chHospi.windowSelect({callback: function(data){
						p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente)).data('data',data);				
					},bootstrap: true});
				});
				K.block();
				$.post('ch/cmmo/get',{_id: p.id},function(data){
					p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente)).data('data',data);
					
				    p.$w.find('[name=diag]').text(data.diag);
					p.$w.find('[name=cama]').val(data.cama.cama);
					p.$w.find('[name=_id]').val(data.cama._id);
					p.$w.find('[name=pabellon]').val(data.pabellon);
					p.$w.find('[name=sala]').val(data.sala);
					p.$w.find('[name=estado]').val(data.estado);

					K.unblock();
				},'json');
			}
		});
	},
	windowTraslado: function(p){
		new K.Modal({ 
			id: 'windowTraslado',
			title: 'Traslado de Pabellon de Paciente',
			contentURL: 'ch/cmmo/tras',
			width: 700,
			height: 300,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								K.sendingInfo();
								var paciente = p.$w.find('[name=paciente]').data('paciente');
								var data = {
									//DATOS DEL cmmoENTE
									_id: p.id,
									paciente: chHospi.dbRel(p.$w.find('[name=paciente]').data('data')),
									cama:{
									_id: p.$w.find('[name=_id]').val(),	
									cama:p.$w.find('[name=cama]').val(),	
									},
									cama_old:{
									_id: p.$w.find('[name=id_old]').text(),	
									},
									pabellon:p.$w.find('[name=pabellon]').val(),
									sala:p.$w.find('[name=sala]').val(),
									estado:p.$w.find('[name=estado]').val(),
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ch/cmmo/traslado",data,function(result){
									
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Paciente Agregado a Cama!"});
									K.closeWindow(p.$w.attr('id'));
									chCmmo.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowTraslado');
				p.$w.find("[name=btnDiag]").click(function(){
					chCama.windowSelect({callback: function(data){
						p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente)).data('data',data);				
						p.$w.find('[name=id_old]').html(data._id.$id).data('data',data);
						//p.$w.find('[name=id_docu]').html(data._id.$id).data('data',data);
						
					},bootstrap: true});
				});
				K.block();
				$.post('ch/cmmo/get',{_id: p.id},function(data){
					p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente)).data('data',data.paciente);
					p.$w.find('[name=cama]').val(data.cama.cama);
					p.$w.find('[name=_id]').val(data.cama._id.$id);
					p.$w.find('[name=pabellon]').val(data.pabellon);
					p.$w.find('[name=sala]').val(data.sala);
					p.$w.find('[name=estado]').val(data.estado);

					K.unblock();
					
				},'json');
			}
		});
	},

	
};
define(
	['ch/hospi','ch/cama'],
	function(chHospi,chCama){
		return chCmmo;
	}
);