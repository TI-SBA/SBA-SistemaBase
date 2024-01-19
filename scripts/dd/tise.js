ddTise = {
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
			//nomb: item.nomb,
			//tipo: item.tipo,
			tipo: item.tipo,
			desc: item.desc
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddTise',
			titleBar: {
				title: 'Configuracion de Tipos de Serie'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Tipo','Descripcion'],
					data: 'dd/tise/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddTise.windowNew();
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
							estado = ddTise.states[data.estado].label
						}
						*/
						$row.append('<td>'+data.tipo+'</td>');
						//$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.desc+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddTise.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenAreas", {
							onShowMenu: function($row, menu) {
								$('#conMenAreas_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenAreas_hab',menu).remove();
								else $('#conMenAreas_edi,#conMenAreas_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenAreas_edi': function(t) {
									ddTise.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},							
								'conMenAreas_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Tipo de Serie:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/tise/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Serie Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddTise.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Tipo de Serie');
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
			id: 'windowNewTise',
			title: 'Nueva Tipo de Serie',
			contentURL: 'dd/tise/edit',
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
							tipo: p.$w.find('[name=tipo]').val(),
							//nomb: p.$w.find('[name=tipo]').val(),
							desc: p.$w.find('[name=desc]').val(),
						};
						if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el tipo de Serie!',type: 'error'});
						}/*
						if(data.nomb==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						*/

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/tise/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Tipo de Serie agregada!"});
							ddTise.init();
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
				p.$w = $('#windowNewTise');
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
			id: 'windowEditTise',
			title: 'Editar Tipo de Serie: '+p.tipo,
			contentURL: 'dd/tise/edit',
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
							tipo: p.$w.find('[name=tipo]').val(),
							//nomb: p.$w.find('[name=tipo]').val(),
							desc: p.$w.find('[name=desc]').val(),
						};
						if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el tipo de Serie!',type: 'error'});
						}
						/*if(data.nomb==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						*/
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/tise/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Tipo de Serie actualizado!"});
							ddTise.init();
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
				p.$w = $('#windowEditTise');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						//p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				$.post('dd/tise/get',{_id: p.id},function(data){
					//p.$w.find('[name=tipo]').val(data.nomb);
					p.$w.find('[name=tipo]').val(data.tipo);
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
			id: 'windowSelectTise',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Tipo de Serie Documental',
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
				p.$w = $('#windowSelectTise');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'dd/tise/lista',
					params: {},
					itemdescr: 'tipo(s) de Oficina',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						//$row.append('<td>'+data.sigl+'</td>');
						$row.append('<td>'+data.tipo+'</td>');
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
		return ddTise;
	}
);