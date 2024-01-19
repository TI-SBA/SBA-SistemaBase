inCalp = {
	init: function(){
		K.initMode({
			mode: 'in',
			action: 'inCalp',
			titleBar: {
				title: 'Calendario de Pagos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Periodo','Fecha',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'in/calp/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							inCalp.windowNew();
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
						$row.append('<td>'+data.ano+'-'+data.mes+'</td>');
						$row.append('<td>'+data.dia+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inCalp.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver,#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									inCalp.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNewCalp',
			title: 'Nueva Fecha de Pago',
			contentURL: 'in/calp/edit',
			width: 380,
			height: 200,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							ano: p.$w.find('[name=ano]').val(),
							mes: p.$w.find('[name=mes]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							dia: p.$w.find('[name=dia]').val()
						};
						if(data.ano==''){
							p.$w.find('[name=ano]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el a&ntilde;o!',type: 'error'});
						}
						if(data.mes==''){
							p.$w.find('[name=mes]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el mes!',type: 'error'});
						}
						if(data.dia==''){
							p.$w.find('[name=dia]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el &uacute;ltimo d&iacute;a de pago!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/calp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Fecha de Pago agregada!"});
							inCalp.init();
							K.closeWindow(p.$w.attr('id'));
						},'json');
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
				p.$w = $('#windowNewCalp');
				p.$w.find('[name=ano]').val(ciHelper.date.getYear());
				p.$w.find('[name=dia]').datepicker({format: 'yyyy-mm-dd'});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Fecha de Pago '+p.nomb,
			contentURL: 'in/calp/edit',
			width: 380,
			height: 200,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							ano: p.$w.find('[name=ano]').val(),
							mes: p.$w.find('[name=mes]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							dia: p.$w.find('[name=dia]').val()
						};
						if(data.ano==''){
							p.$w.find('[name=ano]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el a&ntilde;o!',type: 'error'});
						}
						if(data.mes==''){
							p.$w.find('[name=mes]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el mes!',type: 'error'});
						}
						if(data.dia==''){
							p.$w.find('[name=dia]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el &uacute;ltimo d&iacute;a de pago!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/calp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Fecha de Pago actualizada!"});
							inCalp.init();
							K.closeWindow(p.$w.attr('id'));
						},'json');
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
				p.$w = $('#windowEditTipo');
				p.$w.find('[name=dia]').datepicker({format: 'yyyy-mm-dd'});
				$.post('in/calp/get',{_id: p.id},function(data){
					p.$w.find('[name=ano]').val(data.ano);
					p.$w.find('[name=mes]').val(data.mes);
					p.$w.find('[name=dia]').val(data.dia);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return inCalp;
	}
);