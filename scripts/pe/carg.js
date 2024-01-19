/*******************************************************************************
cargos */
peCarg = {
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
			action: 'peCarg',
			titleBar: {
				title: 'Cargos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},{n:'Nombre',f:'nomb'},{n:'Programa',f:'programa.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/cargo/lista',
					params: {},
					itemdescr: 'grupo(s) ocupancional(es)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peCarg.windowNew();
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
						$row.append('<td>'+peCarg.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						var programa = '--';
						if(data.programa!=null){
							programa = data.programa.nomb;
						}
						$row.append('<td>'+programa+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peCarg.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peCarg.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peCarg.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Cargo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/cargo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Cargo Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peCarg.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Cargo');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Cargo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/cargo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Cargo Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peCarg.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Cargo');
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
		new K.Window({
			id: 'windowNewCarg',
			title: 'Nuevo Cargo',
			contentURL: 'pe/cargo/edit',
			icon: 'ui-icon-plusthick',
			width: 700,
			height: 350,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cod: p.$w.find('[name=cod]').val(),
							programa: {
								_id: p.$w.find('[name=programa] :selected').val(),
								nomb: p.$w.find('[name=programa] :selected').text(),
							}
						},
						tmp = p.$w.find('[name=organizacion]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnOrg]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe Seleccionar una Organizacion',
								type: 'error'
							});
						}
						data.organizacion = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							cod: tmp.cod
						},
						data.organizacion.componente = {
							_id: tmp.componente._id.$id,
							nomb: tmp.componente.nomb,
							cod: tmp.componente.cod
						},
						data.organizacion.actividad = {
							_id: tmp.actividad._id.$id,
							nomb: tmp.actividad.nomb,
							cod: tmp.actividad.cod
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de cargo!',type: 'error'});
						}else if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el cargo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/cargo/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El cargo fue registrado con &eacute;xito!'});
							peCarg.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowNewCarg');
				$.post('mg/prog/all',function(prog){
					var $cbo = p.$w.find('[name=programa]');
					if(prog!=null){
						for(var i in prog){
							$cbo.append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>');
							$cbo.find('option:last').data('data',prog[i]);
						}
					}
				},'json');
				p.$w.find('[name=btnOrg]').click(function(){
						require(['mg/orga'],function(mgOrga){
							mgOrga.windowSelect({bootstrap:true,callback: function(data){
								p.$w.find('[name=organizacion]').html(data.nomb)
									.data('data',data);
							}});
						});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditCarg'+p.id,
			title: 'Editar cargo: '+p.nomb,
			contentURL: 'pe/cargo/edit',
			icon: 'ui-icon-pencil',
			width: 700,
			height: 350,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							cod: p.$w.find('[name=cod]').val(),
							programa: {
								_id: p.$w.find('[name=programa] :selected').val(),
								nomb: p.$w.find('[name=programa] :selected').text(),
							}
						};

						tmp = p.$w.find('[name=organizacion]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnOrg]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe Seleccionar una Organizacion',
								type: 'error'
							});
						}
						data.organizacion = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							cod: tmp.cod
						},
						data.organizacion.componente = {
							_id: tmp.componente._id.$id,
							nomb: tmp.componente.nomb,
							cod: tmp.componente.cod
						},
						data.organizacion.actividad = {
							_id: tmp.actividad._id.$id,
							nomb: tmp.actividad.nomb,
							cod: tmp.actividad.cod
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de cargo!',type: 'error'});
						}else if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el cargo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/cargo/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El cargo fue actualizado con &eacute;xito!'});
							peCarg.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowEditCarg'+p.id);
				K.block();
				$.post('mg/prog/all',function(prog){
					var $cbo = p.$w.find('[name=programa]');
					if(prog!=null){
						for(var i in prog){
							$cbo.append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>');
							$cbo.find('option:last').data('data',prog[i]);
						}
					}
					$.post('pe/cargo/get','id='+p.id,function(data){
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=cod]').val(data.cod);
						if(data.programa!=null){
							p.$w.find('[name=programa]').val(data.programa._id.$id);
						}
						if(data.organizacion!=null){
							p.$w.find('[name=organizacion]').html(data.organizacion.nomb)
							.data('data',data.organizacion);
						}
						K.unblock();
					},'json');
				},'json');
				p.$w.find('[name=btnOrg]').click(function(){
						require(['mg/orga'],function(mgOrga){
							mgOrga.windowSelect({bootstrap:true,callback: function(data){
								p.$w.find('[name=organizacion]').html(data.nomb)
									.data('data',data);
							}});
						});
				});
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
				title: 'Seleccionar Cargo',
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
						cols: ['','Cod.','Nombre','Organizaci&oacute;n'],
						data: 'pe/cargo/lista',
						params: {},
						itemdescr: 'cargo(s)',
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
							$row.append('<td>'+data.organizacion.nomb+'</td>');
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
				title: 'Seleccionar Cargo',
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
						cols: ['','Cod.','Nombre','Organizaci&oacute;n'],
						data: 'pe/cargo/lista',
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
							$row.append('<td>'+data.organizacion.nomb+'</td>');
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
	['mg/enti','mg/orga'],
	function(mgEnti,mgOrga){
		return peCarg;
	}
);