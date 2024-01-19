mgProg = {
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
		var ret = {
			_id: item._id.$id,
			nomb: item.nomb
		};
		if(item.actividad!=null){
			ret.actividad={
				cod:item.actividad.cod
			};
		}
		if(item.componente!=null){
			ret.componente={
				cod:item.componente.cod
			};
		}
		return ret;
	},
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgProg',
			titleBar: {
				title: 'Programas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','',{n:'Codigo',f:'cod'},{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'mg/prog/lista',
					itemdescr: 'Programa(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nueva Programa</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mgProg.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgProg.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							mgProg.windowEdit({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($r, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($r.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									mgProg.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Programa <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mg/prog/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Programa Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mgProg.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Programa');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Programa <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mg/prog/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Programa Deshabilitada',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mgProg.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Programa');
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
			id: 'windowNew',
			title: 'Nueva Programa',
			contentURL: 'mg/prog/edit',
			width: 450,
			height: 200,
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.$w.find('[name=cod]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							actividad: {
								cod: p.$w.find('[name=actividad]').val()
							},
							componente: {
								cod: p.$w.find('[name=componente]').val()
							}
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un nombre!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/prog/save",data,function(rpta){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Programa agregado con &eacute;xito!"
							});
							K.closeWindow(p.$w.attr('id'));
							mgProg.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowNew');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNew',
			title: 'Editar Programa',
			contentURL: 'mg/prog/edit',
			width: 450,
			height: 200,
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						var data = {
							_id: p.id,
							cod: p.$w.find('[name=cod]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							actividad: {
								cod: p.$w.find('[name=actividad]').val()
							},
							componente: {
								cod: p.$w.find('[name=componente]').val()
							}
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un nombre!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/prog/save",data,function(rpta){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: "Programa actualizada con &eacute;xito!"
							});
							K.closeWindow(p.$w.attr('id'));
							mgProg.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				K.block({$element: p.$w});
				$.post('mg/prog/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod]').val(data.cod);
					if(data.actividad!=null){
						p.$w.find('[name=actividad]').val(data.actividad.cod);
					}
					if(data.componente!=null){
						p.$w.find('[name=componente]').val(data.componente.cod);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			height: 550,
			width: 550,
			title: 'Seleccionar Programa',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.multiple!=null){
							var orgas = [];
							for(var i=0,j=p.$w.find('[name=orga]:checked').length; i<j; i++){
								orgas.push(p.$w.find('[name=orga]:checked').eq(i).closest('.item').data('data'));
							}
							if(orgas.length==0)
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger al menos una Programa!',
									type: 'error'
								});
							p.callback(orgas);
							K.closeWindow(p.$w.attr('id'));
						}else{
							if(p.$w.find('.highlights').data('data')!=null || p.$w.find('.ui-state-highlight').data('data')!=null){
								var _data = p.$w.find('.highlights').data('data');
								if(_data==null) _data = p.$w.find('.ui-state-highlight').data('data');
								p.callback(_data);
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
			//onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'mg/prog/lista',
					params: {},
					itemdescr: 'Programa(s)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						if(p.multiple!=null)
							$row.append('<td><input type="checkbox" name="orga" value="'+data._id.$id+'" class="iCheck"></td>');
						else
							$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.find('.modal-footer button:first').click();
						});
						if(p.multiple!=null){
							$row.find('td:eq(1)').click(function(){
								var $check = $(this).closest('.item').find('[name=orga]');
								if($check.is(':checked')==false){
									//$check.attr('checked','checked');
									$check.iCheck('check');
								}else{
									//$check.removeAttr('checked');
									$check.iCheck('uncheck');
								}
							});
							$row.iCheck({
								checkboxClass: 'icheckbox_square-green',
								radioClass: 'iradio_square-green'
							});
						}else{
							$row.contextMenu('conMenListSel', {
								bindings: {
									'conMenListSel_sel': function(t) {
										p.$w.find('.modal-footer button:first').click();
									}
								}
							});
						}
						return $row;
					}
				});
			}
		});
	}
};
define(
	function(){
		return mgProg;
	}
);