mhDini = {
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
			nomb: item.nomb,
			sigl: item.sigl
		};
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhDini',
			titleBar: {
				title: 'Diagnostico de Diagnostico'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Siglas.',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'mh/dini/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhDini.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						var estado = '';
						if(data.estado!=null){
							estado = mhDini.states[data.estado].label
						}
						$row.append('<td>'+estado+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.sigl+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							mhDini.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenList", {
							onShowMenu: function($row, menu) {
								$('#conMenList_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenList_hab',menu).remove();
								else $('#conMenList_edi,#conMenList_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									mhDini.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},							
								'conMenList_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Medica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/dini/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Diagnostico Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhDini.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Diagnostico');
								},
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
			id: 'windowNewDiagnostico',
			title: 'Nuevo Diagnostico Medico',
			contentURL: 'mh/dini/edit',
			width: 600,
			height: 200,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							sigl: p.$w.find('[name=sigl]').val(),
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Diagnostico!',type: 'error'});
						}
						if(data.sigl==''){
							p.$w.find('[name=sigl]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la sigliatura de Diagnostico!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/dini/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Diagnostico agregado!"});
							mhDini.init();
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
				p.$w = $('#windowNewDiagnostico');
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditDiagnostico',
			title: 'Editar Diagnostico '+p.nomb,
			contentURL: 'mh/dini/edit',
			width: 380,
			height: 190,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							sigl: p.$w.find('[name=sigl]').val(),
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Diagnostico!',type: 'error'});
						}
						if(data.sigl==''){
							p.$w.find('[name=sigl]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la sigliatura de Diagnostico!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/dini/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Diagnostico actualizado!"});
							mhDini.init();
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
				p.$w = $('#windowEditDiagnostico');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				$.post('mh/dini/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=sigl]').val(data.sigl);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr)
						.data('data',data.cuenta);
					K.unblock();
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelectDiagnostico',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Diagnostico Medico',
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
				p.$w = $('#windowSelectDiagnostico');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Sigla','Nombre'],
					data: 'mh/dini/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.sigl+'</td>');
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
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return mhDini;
	}
);