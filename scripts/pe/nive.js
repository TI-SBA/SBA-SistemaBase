/*******************************************************************************
niveles remunerativos */
peNive = {
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
			action: 'peNive',
			titleBar: {
				title: 'Niveles Remunerativos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Abrev.','Salario','B&aacute;sica','Reunificada','Incentivo',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/nive/lista',
					params: {},
					itemdescr: 'grupo(s) ocupacional(es)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peNive.windowNew();
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
						$row.append('<td>'+peNive.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.abrev+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.salario)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.basica)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.reunificada)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.incentivo)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peNive.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peNive.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peNive.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Nivel Remunerativo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/nive/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Nivel Remunerativo Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peNive.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Nivel Remunerativo');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Nivel Remunerativo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/nive/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Nivel Remunerativo Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peNive.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Nivel Remunerativo');
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
			id: 'windowNewNive',
			title: 'Nuevo Nivel Remunerativo',
			contentURL: 'pe/nive/edit',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 360,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							salario: p.$w.find('[name=salario]').val(),
							basica: p.$w.find('[name=basica]').val(),
							reunificada: p.$w.find('[name=reunificada]').val(),
							incentivo: p.$w.find('[name=incentivo]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de Nivel Remunerativo!',type: 'error'});
						}else if(data.abrev==''){
							p.$w.find('[name=abrev]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una abreviatura para el Nivel Remunerativo!',type: 'error'});
						}else if(data.salario==''){
							p.$w.find('[name=salario]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un salario para el Nivel Remunerativo!',type: 'error'});
						}else if(data.basica==''){
							p.$w.find('[name=basica]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n b&aacute;sica para el Nivel Remunerativo!',type: 'error'});
						}else if(data.reunificada==''){
							p.$w.find('[name=reunificada]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n reunificada para el Nivel Remunerativo!',type: 'error'});
						}else if(data.incentivo==''){
							p.$w.find('[name=incentivo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un incentivo para el Nivel Remunerativo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/nive/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Nivel Remunerativo fue registrado con &eacute;xito!'});
							peNive.init();
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
				p.$w = $('#windowNewNive');
				p.$w.find('[name=salario]').numeric();
				p.$w.find('[name=basica]').numeric();
				p.$w.find('[name=reunificada]').numeric();
				p.$w.find('[name=incentivo]').numeric();
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditNive'+p.id,
			title: 'Editar Nivel Remunerativo: '+p.nomb,
			contentURL: 'pe/nive/edit',
			icon: 'ui-icon-pencil',
			width: 500,
			height: 360,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							salario: p.$w.find('[name=salario]').val(),
							basica: p.$w.find('[name=basica]').val(),
							reunificada: p.$w.find('[name=reunificada]').val(),
							incentivo: p.$w.find('[name=incentivo]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de Nivel Remunerativo!',type: 'error'});
						}else if(data.abrev==''){
							p.$w.find('[name=abrev]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una abreviatura para el Nivel Remunerativo!',type: 'error'});
						}else if(data.salario==''){
							p.$w.find('[name=salario]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un salario para el Nivel Remunerativo!',type: 'error'});
						}else if(data.basica==''){
							p.$w.find('[name=basica]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n b&aacute;sica para el Nivel Remunerativo!',type: 'error'});
						}else if(data.reunificada==''){
							p.$w.find('[name=reunificada]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n reunificada para el Nivel Remunerativo!',type: 'error'});
						}else if(data.incentivo==''){
							p.$w.find('[name=incentivo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un incentivo para el Nivel Remunerativo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/nive/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Nivel Remunerativo fue actualizado con &eacute;xito!'});
							peNive.init();
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
				p.$w = $('#windowEditNive'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=salario]').numeric();
				p.$w.find('[name=basica]').numeric();
				p.$w.find('[name=reunificada]').numeric();
				p.$w.find('[name=incentivo]').numeric();
				$.post('pe/nive/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=abrev]').val(data.abrev);
					p.$w.find('[name=salario]').val(data.salario);
					p.$w.find('[name=basica]').val(data.basica);
					p.$w.find('[name=reunificada]').val(data.reunificada);
					p.$w.find('[name=incentivo]').val(data.incentivo);
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
				title: 'Seleccionar Nivel Remunerativo',
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
						cols: ['','Nombre','Salario'],
						data: 'pe/nive/lista',
						params: {},
						itemdescr: 'nivel(es) remunerativo(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.append('<td>'+data.salario+'</td>');
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
						cols: ['','Nombre','Salario'],
						data: 'pe/nive/lista',
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
							$row.append('<td>'+data.nomb+'</td>');
							$row.append('<td>'+data.salario+'</td>');
							$row.data('data',data).dblclick(function(){
								p.$w.find('.modal-footer button:first').click();
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
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return peNive;
	}
);