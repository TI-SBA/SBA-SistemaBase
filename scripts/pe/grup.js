/*******************************************************************************
grupos ocupacionales */
peGrup = {
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
	dbRel: function(data){
		return {
			_id: data._id.$id,
			nomb: data.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peGrup',
			titleBar: {
				title: 'Grupos Ocupacionales'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Sigla',f:'sigla'},{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/grup/lista',
					params: {},
					itemdescr: 'grupo(s) ocupancional(es)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peGrup.windowNew();
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
						$row.append('<td>'+peGrup.states[data.estado].label+'</td>');
						$row.append('<td>'+data.sigla+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peGrup.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peGrup.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peGrup.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Grupo Ocupacional <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/grup/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Grupo Ocupacional Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peGrup.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Grupo Ocupacional');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Grupo Ocupacional <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/grup/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Grupo Ocupacional Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peGrup.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Grupo Ocupacional');
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
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewGrup',
			title: 'Nuevo Grupo Ocupacional',
			contentURL: 'pe/grup/edit',
			icon: 'ui-icon-plusthick',
			width: 445,
			height: 160,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							sigla: p.$w.find('[name=sigla]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de Grupo Ocupacional!',type: 'error'});
						}else if(data.sigla==''){
							p.$w.find('[name=sigla]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una sigla para el Grupo Ocupacional!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/grup/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Grupo Ocupacional fue registrado con &eacute;xito!'});
							peGrup.init();
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
				p.$w = $('#windowNewGrup');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditGrup'+p.id,
			title: 'Editar Grupo Ocupacional: '+p.nomb,
			contentURL: 'pe/grup/edit',
			icon: 'ui-icon-pencil',
			width: 445,
			height: 160,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							sigla: p.$w.find('[name=sigla]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de Grupo Ocupacional!',type: 'error'});
						}else if(data.sigla==''){
							p.$w.find('[name=sigla]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una sigla para el Grupo Ocupacional!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/grup/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Grupo Ocupacional fue actualizado con &eacute;xito!'});
							peGrup.init();
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
				p.$w = $('#windowEditGrup'+p.id);
				K.block({$element: p.$w});
				$.post('pe/grup/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=sigla]').val(data.sigla);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap){
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Grupo Ocupacional',
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
						$el: p.$w.find('[name=tmp]'),
						cols: ['','Sigla','Nombre'],
						data: 'pe/grup/lista',
						params: {},
						itemdescr: 'grupo(s) ocupacional(es)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+data.sigla+'</td>');
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
		}else{
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Cuenta Contable',
				buttons: {
					'Seleccionar': function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un pabell&oacute;n!',type: 'error'});
						}
						p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
						tmp_lock = true;
						K.closeWindow(p.$w.attr('id'));
					},
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					var params = {};
					if(p.logistica!=null)
						params.logistica = true;
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','Sigla','Nombre'],
						data: 'pe/grup/lista',
						params: params,
						itemdescr: 'cuenta(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+data.sigla+'</td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.data('data',data).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',data).contextMenu('conMenListSel', {
								onShowMenu: function(e, menu) {
									$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
									$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
									$(e.target).closest('.item').click();
									K.tmp = $(e.target).closest('.item');
									return menu;
								},
								bindings: {
									'conMenListSel_sel': function(t){
										p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
									}
								}
							});
							return $row;
						}
					});
				}
			});
		}
	}
};
define(
	[],
	function(){
		return peGrup;
	}
);