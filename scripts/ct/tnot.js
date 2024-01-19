ctTnot = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctTnot',
			titleBar: {
				title: 'Tipos de Nota'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Sigla.',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'ct/tnot/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ctTnot.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+ctTnot.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.sigla+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ctTnot.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									ctTnot.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									ctTnot.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ct/tnot/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ctTnot.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ct/tnot/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ctTnot.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
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
			id: 'windowNewTipo',
			title: 'Nuevo Tipo',
			contentURL: 'ct/tnot/edit',
			width: 380,
			height: 300,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=tipo]').val(),
							sigla: p.$w.find('[name=sigla]').val(),
							descr: p.$w.find('[name=observ]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=tipo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Tipo!',type: 'error'});
						}
						if(data.sigla==''){
							p.$w.find('[name=sigla]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo sigla!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('ct/tnot/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Tipo de nota fue registrado con &eacute;xito!'});
							ctTnot.init();
						});
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
				p.$w = $('#windowNewTipo');
				K.block( );
				p.$w.find('[name=cod]').focus().numeric();		
				K.unblock();
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'ct/tnot/edit',
			width: 380,
			height: 300,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=tipo]').val(),
							sigla: p.$w.find('[name=sigla]').val(),
							descr: p.$w.find('[name=observ]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=tipo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Tipo!',type: 'error'});
						}
						if(data.sigla==''){
							p.$w.find('[name=sigla]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo sigla!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('ct/tnot/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Tipo de nota fue actualizado con &eacute;xito!'});
							ctTnot.init();
						});
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
				K.block();
				$.post('ct/tnot/get','id='+p.id,function(data){
					p.$w.find('[name=tipo]').val(data.nomb);
					p.$w.find('[name=sigla]').val(data.sigla);
					p.$w.find('[name=observ]').val(data.descr);
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return ctTnot;
	}
);