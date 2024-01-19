adCama = {
	estado:{
		"0":"VACIA",
		"1":"OCUPADA"
		
	},
	
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cama: item.cama,
			estado: item.estado
			
			
		};
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adCama',
			titleBar: {
				title: 'Cama de Cama'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Numero de cama','Paciente','Pabellon','Sala','Estado','fecmod'],
					data: 'ad/cama/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adCama.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cama+'</td>');
						var paciente_ = '----';
						if(data.paciente!=null){
							if(data.paciente.paciente!=null){
								paciente_ = mgEnti.formatName(data.paciente.paciente);
							}
							else{
								paciente_ = mgEnti.formatName(data.paciente);
							}
						}
						$row.append('<td>'+paciente_+'</td>');
						$row.append('<td>'+data.pabellon+'</td>');
						$row.append('<td>'+data.sala+'</td>');
						$row.append('<td>'+adCama.estado[data.estado]+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							adCama.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenMhCama", {
							onShowMenu: function($row, menu) {
								$('#conMenMhCama_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenMhCama_hab',menu).remove();
								else $('#conMenMhCama_edi,#conMenMhCama_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenMhCama_edi': function(t) {
									adCama.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},							
								'conMenMhCama_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Medica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/cama/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Cama Eliminada',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adCama.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Cama');
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
			id: 'windowNewCama',
			title: 'Nuevo Cama',
			contentURL: 'ad/cama/edit',
			width: 600,
			height: 300,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cama: p.$w.find('[name=cama]').val(),
							pabellon: p.$w.find('[name=pabellon]').val(),
							sala: p.$w.find('[name=sala]').val(),
							estado: p.$w.find('[name=estado]').val(),
							};
						if(data.cama==''){
							p.$w.find('[name=cama]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.pabellon==''){
							p.$w.find('[name=pabellon]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.sala==''){
							p.$w.find('[name=sala]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.estado==''){
							p.$w.find('[name=estado]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ad/cama/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Cama agregado!"});
							adCama.init();
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
				p.$w = $('#windowNewCama');
				
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditCama',
			title: 'Editar Cama '+p.nomb,
			contentURL: 'ad/cama/edit',
			width: 600,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cama: p.$w.find('[name=cama]').val(),
							pabellon: p.$w.find('[name=pabellon]').val(),
							sala: p.$w.find('[name=sala]').val(),
							estado: p.$w.find('[name=estado]').val(),
						};
						if(data.cama==''){
							p.$w.find('[name=cama]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.pabellon==''){
							p.$w.find('[name=pabellon]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.sala==''){
							p.$w.find('[name=sala]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.estado==''){
							p.$w.find('[name=estado]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ad/cama/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Cama Actualizada!"});
							adCama.init();
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
				p.$w = $('#windowEditCama');
				K.block();
				
				$.post('ad/cama/get',{_id: p.id},function(data){
					
					p.$w.find('[name=cama]').val(data.cama),
					p.$w.find('[name=pabellon]').val(data.pabellon),
					p.$w.find('[name=sala]').val(data.sala),
					p.$w.find('[name=estado]').val(data.estado),
					K.unblock();
				},'json');
			}
		});
	},
		windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Cama',
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
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Numero de Cama','Paciente','Estado'],
					data: 'ad/cama/lista',
					params: {},
					itemdescr: 'cama(s)',
					onLoading: function(){
					 K.block();
					 $.post('')
					},
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cama+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+adCama.estado[data.estado]+'</td>');
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
		return adCama;
	}
);