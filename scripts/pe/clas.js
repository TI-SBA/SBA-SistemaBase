/*******************************************************************************
cargos */
peClas = {
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
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peClas',
			titleBar: {
				title: 'Cargos Clasificados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod.',f:'cod'},{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/clas/lista',
					params: {},
					itemdescr: 'cargo(s) clasificado(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peClas.windowNew();
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
						$row.append('<td>'+peClas.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peClas.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peClas.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peClas.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Cargo Clasificado <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/clas/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Cargo Clasificado Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peClas.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Cargo Clasificado');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Cargo Clasificado <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/clas/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Cargo Clasificado Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peClas.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Cargo Clasificado');
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
			id: 'windowNewClas',
			title: 'Nuevo Cargo Clasificado',
			contentURL: 'pe/clas/edit',
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
							cod: p.$w.find('[name=cod]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de cargo clasificado!',type: 'error'});
						}else if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el cargo clasificado!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/clas/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El cargo clasificado fue registrado con &eacute;xito!'});
							peClas.init();
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
				p.$w = $('#windowNewClas');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditClas'+p.id,
			title: 'Editar cargo clasificado: '+p.nomb,
			contentURL: 'pe/clas/edit',
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
							cod: p.$w.find('[name=cod]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de cargo clasificado!',type: 'error'});
						}else if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el cargo clasificado!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/clas/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El cargo clasificado fue actualizado con &eacute;xito!'});
							peClas.init();
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
				p.$w = $('#windowEditClas'+p.id);
				K.block({$element: p.$w});
				$.post('pe/clas/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod]').val(data.cod);
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
				title: 'Seleccionar Cargo Clasificado',
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
						cols: ['','Cod.','Nombre'],
						data: 'pe/clas/lista',
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
							$row.append('<td>'+data.cod+'</td>');
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
				title: 'Seleccionar Cargo Clasificado',
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
						cols: ['','Cod.','Nombre'],
						data: 'pe/clas/lista',
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
							$row.append('<td>'+data.cod+'</td>');
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
	['mg/enti'],
	function(mgEnti){
		return peClas;
	}
);