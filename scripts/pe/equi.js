/*******************************************************************************
equipos */
peEqui = {
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
			cod: item.cod,
			nomb: item.nomb,
			local: {
				_id: item.local._id.$id,
				descr: item.local.descr,
				direccion: item.local.direccion,
			}
		};
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peEqui',
			titleBar: {
				title: 'Equipos de Asistencia'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'IP',f:'cod'},{n:'Nombre',f:'nomb'},'Local asociado',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/equi/lista',
					params: {},
					itemdescr: 'equipos(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peEqui.windowNew();
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
						$row.append('<td>'+peEqui.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.local.descr+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peEqui.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peEqui.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peEqui.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Equipo de Asistencia <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/equi/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Equipo de Asistencia Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peEqui.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Equipo de Asistencia');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Equipo de Asistencia <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/equi/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Equipo de Asistencia Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peEqui.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Equipo de Asistencia');
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
			id: 'windowNewEqui',
			title: 'Nuevo Equipo',
			contentURL: 'pe/equi/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 260,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cod: p.$w.find('[name=cod]').val(),
							descr: p.$w.find('[name=descr]').val(),
							programa: p.$w.find('[name=programa]').data('data')
						},tmp = p.$w.find('[name=local]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnLocal]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un local para el equipo!',type: 'error'});
						}else{
							data.local = {
								_id: tmp._id.$id,
								descr: tmp.descr,
								direccion: tmp.direccion
							};
						}
						if(data.programa==null){
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un programa!',type: 'error'});
						}else data.programa = mgProg.dbRel(data.programa);
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el equipo!',type: 'error'});
						}else if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de equipo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('.ui-dialog-buttonpane button').button('disable');
						$.post('pe/equi/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El equipo fue registrado con &eacute;xito!'});
							peEqui.init();
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
				p.$w = $('#windowNewEqui');
				p.$w.find('[name=btnLocal]').click(function(){
					mgTitu.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					}});
				});
				p.$w.find('[name=btnPro]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=programa]').html(data.nomb).data('data',data);
					}});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditEqui'+p.id,
			title: 'Editar equipo: '+p.nomb,
			contentURL: 'pe/equi/edit',
			icon: 'ui-icon-pencil',
			width: 550,
			height: 260,
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
							descr: p.$w.find('[name=descr]').val(),
							programa: p.$w.find('[name=programa]').data('data')
						},tmp = p.$w.find('[name=local]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnLocal]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un local para el equipo!',type: 'error'});
						}else{
							data.local = {
								_id: tmp._id.$id,
								descr: tmp.descr,
								direccion: tmp.direccion
							};
						}
						if(data.programa==null){
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un programa!',type: 'error'});
						}else data.programa = mgProg.dbRel(data.programa);
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el equipo!',type: 'error'});
						}else if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de equipo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('.ui-dialog-buttonpane button').button('disable');
						$.post('pe/equi/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El equipo fue actualizado con &eacute;xito!'});
							peEqui.init();
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
				p.$w = $('#windowEditEqui'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnLocal]').click(function(){
					mgTitu.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					}});
				});
				p.$w.find('[name=btnPro]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=programa]').html(data.nomb).data('data',data);
					}});
				});
				$.post('pe/equi/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=local]').html(data.local.descr).data('data',data.local);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsEqui'+p.id,
			title: p.nomb,
			contentURL: 'pe/equi/details',
			icon: 'ui-icon-gear',
			width: 445,
			height: 110,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsEqui'+p.id);
				K.block({$element: p.$w});
				$.post('pe/equi/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=cod]').html(data.cod);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=local]').html(data.local.descr);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap!=null){
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Equipo de Asistencia',
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
						data: 'pe/equi/lista',
						params: {},
						itemdescr: 'equipo(s)',
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
						cols: ['','Sigla','Nombre'],
						data: 'pe/equi/lista',
						params: params,
						itemdescr: 'equipo(s)',
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
	['mg/enti','mg/prog'],
	function(mgEnti,mgProg){
		return peEqui;
	}
);