ddOfic = {
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
			action: 'ddOfic',
			titleBar: {
				title: 'Configuracion de Oficinas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nombre','Descripcion'],
					data: 'dd/ofic/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddOfic.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						/*
						var estado = '';
						if(data.estado!=null){
							estado = ddOfic.states[data.estado].label
						}
						*/
						//$row.append('<td>'+data.tipo+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.desc+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddOfic.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenAreas", {
							onShowMenu: function($row, menu) {
								$('#conMenAreas_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenAreas_hab',menu).remove();
								else $('#conMenAreas_edi,#conMenAreas_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenAreas_edi': function(t) {
									ddOfic.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},							
								'conMenAreas_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la oficina:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/ofic/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Area Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddOfic.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Oficina');
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
			id: 'windowNewOficina',
			title: 'Nueva Oficina',
			contentURL: 'dd/ofic/edit',
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
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el tipo de Oficina!',type: 'error'});
						}
						*/
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/ofic/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Oficina agregada!"});
							ddOfic.init();
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
				p.$w = $('#windowNewOficina');
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
			id: 'windowEditOficina',
			title: 'Editar Oficina: '+p.nomb,
			contentURL: 'dd/ofic/edit',
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
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el tipo de Oficina!',type: 'error'});
						}
						*/
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/ofic/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Area actualizada!"});
							ddOfic.init();
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
				p.$w = $('#windowEditOficina');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						//p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				$.post('dd/ofic/get',{_id: p.id},function(data){
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
			id: 'windowSelectOficina',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Oficina',
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
				p.$w = $('#windowSelectOficina');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'dd/ofic/lista',
					params: {},
					itemdescr: 'tipo(s) de Oficina',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						//$row.append('<td>'+data.sigl+'</td>');
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
		return ddOfic;
	}
);