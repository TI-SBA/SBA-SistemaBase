/*******************************************************************************
tipo de incidencias */
peTipo = {
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
	tipo: {
		'VA': 'Vacaciones',
		'LI': 'Licencia',
		'PE': 'Permiso',
		'TO': 'Tolerancia',
		'TA': 'Tardanza',
		'IN': 'Inasistencia',
		'CO': 'Compensaci&oacute;n',
		'TE': 'Tiempo Extra',
		'JO': 'Jornada Normal',
		'SU': 'Suspensi&oacute;n'
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb,
			tipo: item.tipo,
			goce_haber: item.goce_haber,
			subsidiado: item.subsidiado,
			todo: item.todo

		};
		
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peTipo',
			titleBar: {
				title: 'Tipo de Incidencia'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Tipo','Con Goce de Haber','A cuenta de Vacaciones','Subsidiado',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/tipo/lista',
					params: {},
					itemdescr: 'tipo(s) de incidencia',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peTipo.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+peTipo.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+peTipo.tipo[data.tipo]+'</td>');
						if(data.goce_haber==true) $row.append('<td>Si</td>');
						else $row.append('<td>No</td>');
						if(data.cuenta_vacaciones==true) $row.append('<td>Si</td>');
						else $row.append('<td>No</td>');
						if(data.subsidiado==true) $row.append('<td>Si</td>');
						else $row.append('<td>No</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peTipo.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peTipo.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peTipo.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Incidencia <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Incidencia Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peTipo.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Incidencia');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Incidencia <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Incidencia Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peTipo.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Incidencia');
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
			id: 'windowNewTipo',
			title: 'Nuevo Tipos de Incidencia',
			contentURL: 'pe/tipo/edit',
			icon: 'ui-icon-plusthick',
			width: 445,
			height: 310,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de tipo de incidencia!',type: 'error'});
						}
						/*if(data.cod_sunat==''){
							p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT para el tipo de contrato!',type: 'error'});
						}*/
						if(p.$w.find('[name=cuenta]').attr('checked')) data.cuenta_vacaciones = true;
						else data.cuenta_vacaciones = false;
						if(p.$w.find('[name=goce]').attr('checked')) data.goce_haber = true;
						else data.goce_haber = false;
						if(p.$w.find('[name=subsi]').attr('checked')) data.subsidiado = true;
						else data.subsidiado = false;
						if(p.$w.find('[name=todo]').attr('checked')) data.todo = true;
						else data.todo = false;
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/tipo/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El tipo de incidencia fue registrado con &eacute;xito!'});
							peTipo.init();
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
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditTipo'+p.id,
			title: 'Editar tipo de incidencia: '+p.nomb,
			contentURL: 'pe/tipo/edit',
			icon: 'ui-icon-pencil',
			width: 445,
			height: 310,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de tipo de incidencia!',type: 'error'});
						}
						/*if(data.cod_sunat==''){
							p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT para el tipo de contrato!',type: 'error'});
						}*/
						if(p.$w.find('[name=cuenta]').attr('checked')) data.cuenta_vacaciones = true;
						else data.cuenta_vacaciones = false;
						if(p.$w.find('[name=goce]').attr('checked')) data.goce_haber = true;
						else data.goce_haber = false;
						if(p.$w.find('[name=subsi]').attr('checked')) data.subsidiado = true;
						else data.subsidiado = false;
						if(p.$w.find('[name=todo]').attr('checked')) data.todo = true;
						else data.todo = false;
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/tipo/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El tipo de incidencia fue actualizado con &eacute;xito!'});
							peTipo.init();
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
				p.$w = $('#windowEditTipo'+p.id);
				K.block({$element: p.$w});
				$.post('pe/tipo/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod_sunat]').val(data.cod_sunat);
					p.$w.find('[name=tipo]').selectVal(data.tipo);
					if(data.cuenta_vacaciones==true) p.$w.find('[name=cuenta]').attr('checked',true);
					if(data.goce_haber==true) p.$w.find('[name=goce]').attr('checked',true);
					if(data.subsidiado==true) p.$w.find('[name=subsi]').attr('checked',true);
					if(data.todo==true) p.$w.find('[name=todo]').attr('checked',true);
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
				title: 'Seleccionar Tipo de Incidencia',
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
						cols: ['','Nombre','Tipo','Con Goce de Haber','A cuenta de Vacaciones','Subsidiado'],
						data: 'pe/tipo/lista',
						params: {},
						itemdescr: 'tipo(s) de incidencia',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.append('<td>'+peTipo.tipo[data.tipo]+'</td>');
							if(data.goce_haber==true) $row.append('<td>Si</td>');
							else $row.append('<td>No</td>');
							if(data.cuenta_vacaciones==true) $row.append('<td>Si</td>');
							else $row.append('<td>No</td>');
							if(data.subsidiado==true) $row.append('<td>Si</td>');
							else $row.append('<td>No</td>');
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
				title: 'Seleccionar Tipo de Incidencia',
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
						cols: ['','Nombre','Tipo','Con Goce de Haber','A cuenta de Vacaciones','Subsidiado'],
						data: 'pe/tipo/lista',
						params: params,
						itemdescr: 'cuenta(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.append('<td>'+peTipo.tipo[data.tipo]+'</td>');
							if(data.goce_haber==true) $row.append('<td>Si</td>');
							else $row.append('<td>No</td>');
							if(data.cuenta_vacaciones==true) $row.append('<td>Si</td>');
							else $row.append('<td>No</td>');
							if(data.subsidiado==true) $row.append('<td>Si</td>');
							else $row.append('<td>No</td>');
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
		return peTipo;
	}
);