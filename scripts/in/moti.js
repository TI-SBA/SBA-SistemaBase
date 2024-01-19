inMoti = {
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
			mode: 'in',
			action: 'inMoti',
			titleBar: {
				title: 'Motivos de Contrato'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'in/moti/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							inMoti.windowNew();
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
						$row.append('<td>'+inMoti.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inMoti.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									inMoti.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									inMoti.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Motivo de Contrato <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/moti/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Motivo de Contrato Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inMoti.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Motivo de Contrato');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Motivo de Contrato <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/moti/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Motivo de Contrato Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inMoti.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Motivo de Contrato');
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
			contentURL: 'in/moti/edit',
			store: false,
			icon: 'ui-icon-bookmark',
			width: 400,
			height: 70,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/moti/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Tipo agregado!"});
							inMoti.init();
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
				p.$w = $('#windowNewTipo');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'in/moti/edit',
			store: false,
			icon: 'ui-icon-pencil',
			width: 400,
			height: 70,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/moti/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Tipo actualizado!"});
							inMoti.init();
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
				K.block({$element: p.$w});
				$.post('in/moti/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 300,
			height: 350,
			title: 'Seleccionar Motivo de Contrato',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
						}
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
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w,
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'in/moti/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.find('.modal-footer button:first').click();
						}).contextMenu('conMenListSel', {
							bindings: {
								'conMenListSel_sel': function(t) {
									p.$w.find('.modal-footer button:first').click();
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
	function(){
		return inMoti;
	}
);