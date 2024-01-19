ddDire = {
	/*
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
	*/
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb,
			desc: item.desc
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddDire',
			titleBar: {
				title: 'Configuracion de Descripcions'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nombre','Descripcion'],
					data: 'dd/dire/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddDire.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.desc+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddDire.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenAreas", {
							onShowMenu: function($row, menu) {
								$('#conMenAreas_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenAreas_hab',menu).remove();
								else $('#conMenAreas_edi,#conMenAreas_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenAreas_edi': function(t) {
									ddDire.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},							
								'conMenAreas_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la descripcion:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/dire/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Descripcion Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddDire.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Descripcion');
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
			id: 'windowNewDescripcion',
			title: 'Nueva Descripcion',
			contentURL: 'dd/dire/edit',
			width: 500,
			height: 300,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//tipo: p.$w.find('[name=tipo]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							desc: p.$w.find('[name=desc]').val(),
						};
						/*if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el tipo de Descripcion!',type: 'error'});
						}
						*/
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/dire/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Descripcion agregada!"});
							ddDire.init();
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
				p.$w = $('#windowNewDescripcion');
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						//p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditDescripcion',
			title: 'Editar Descripcion: '+p.nomb,
			contentURL: 'dd/dire/edit',
			width: 500,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							//tipo: p.$w.find('[name=tipo]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							desc: p.$w.find('[name=desc]').val(),
						};
						/*if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el tipo de Descripcion!',type: 'error'});
						}
						*/
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/dire/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Area actualizada!"});
							ddDire.init();
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
				p.$w = $('#windowEditDescripcion');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						//p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				$.post('dd/dire/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					//p.$w.find('[name=tipo]').val(data.tipo);
					p.$w.find('[name=desc]').val(data.desc);
//					p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr)
					//	.data('data',data.cuenta);
					K.unblock();
				},'json');
			}
		});
	},
	
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelectDireccion',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Direccion',
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
				p.$w = $('#windowSelectDireccion');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'dd/dire/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
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
		return ddDire;
	}
);