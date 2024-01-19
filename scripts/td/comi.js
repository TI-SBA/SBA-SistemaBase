/*
 * COMITES
 */
tdComi = {
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
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'td',
			action: 'tdComi',
			titleBar: {
				title: 'Comites (Agrupaci&oacute;n de Oficinas)'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'}],
					data: 'td/comi/lista',
					params: {},
					itemdescr: 'comite(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tdComi.windowNew();
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
						$row.append('<td>'+tdComi.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							tdComi.windowEdit({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu('conMenListEd', {
							onShowMenu: function(e, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									tdComi.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Comite <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('td/comi/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Comite Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tdComi.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Comite');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Comite <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('td/comi/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Comite Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tdComi.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Comite');
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
			id: "windowNew",
			title: "Agregar Comite",
			contentURL: "td/comi/edit",
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							oficina: []
						};
						if(data.nomb == ""){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre del Comite!",type:"error"});
						}
						if(p.$w.find('[name=gridOfi] tbody .item').length==0){
							p.$w.find('[name=btnAgregar]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar al menos una oficina!',
								type: 'error'
							});
						}else{
							for(var i=0,j=p.$w.find('[name=gridOfi] tbody .item').length; i<j; i++){
								var ofi = p.$w.find('[name=gridOfi] tbody .item').eq(i).data('data');
								data.oficina.push({
									_id: ofi._id.$id,
									nomb: ofi.nomb
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("td/comi/save",data,function(rpta){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Comite agregado!"});
							tdComi.init();
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
			onContentLoaded: function(){
				p.$w = $("#windowNew");
				new K.grid({
					$el: p.$w.find('[name=gridOfi]'),
					search: false,
					pagination: false,
					cols: ['Oficina',''],
					onlyHtml: true,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mgOfic.windowSelect({
								bootstrap: true,
								multiple: true,
								callback: function(data){
									for(var i=0,j=data.length; i<j; i++){
										var ofic = data[i];
										if(p.$w.find('[name='+ofic._id.$id+']').length==0){
											var $row = $('<tr class="item" name="'+ofic._id.$id+'">');
											$row.append('<td>'+ofic.nomb+'</td>');
											$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('[name=btnEli]').click(function(){
												$(this).closest('.item').remove();
											});
											$row.data('data',ofic);
											p.$w.find('[name=gridOfi] tbody').append($row);
										}else{
											K.notification({
												title: ciHelper.titleMessages.infoReq,
												text: 'La Oficina <b>'+ofic.nomb+'</b> ya fue agregada!',
												type: 'error'
											});
										}
									}
								}
							});
						});
					}
				});
			}
		});
	},
	windowEdit: function(p){
		K.Window({
			id: "windowEdit"+p.id,
			title: "Actualizar Comite",
			contentURL: "td/comi/edit",
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							oficina: []
						};
						if(data.nomb == ""){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre del Comite!",type:"error"});
						}
						if(p.$w.find('[name=gridOfi] tbody .item').length==0){
							p.$w.find('[name=btnAgregar]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar al menos una oficina!',
								type: 'error'
							});
						}else{
							for(var i=0,j=p.$w.find('[name=gridOfi] tbody .item').length; i<j; i++){
								var ofi = p.$w.find('[name=gridOfi] tbody .item').eq(i).data('data');
								data.oficina.push({
									_id: ofi._id.$id,
									nomb: ofi.nomb
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("td/comi/save",data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Registro Guardado"});
							K.closeWindow("windowEdit"+p.id);
							tdComi.init();
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
				p.$w = $("#windowEdit"+p.id);
				new K.grid({
					$el: p.$w.find('[name=gridOfi]'),
					search: false,
					pagination: false,
					cols: ['Oficina',''],
					onlyHtml: true,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mgOfic.windowSelect({
								bootstrap: true,
								multiple: true,
								callback: function(data){
									for(var i=0,j=data.length; i<j; i++){
										var ofic = data[i];
										if(p.$w.find('[name='+ofic._id.$id+']').length==0){
											var $row = $('<tr class="item">');
											$row.append('<td>'+ofic.nomb+'</td>');
											$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('[name=btnEli]').click(function(){
												$(this).closest('.item').remove();
											});
											$row.data('data',ofic);
											p.$w.find('[name=gridOfi] tbody').append($row);
										}else{
											K.notification({
												title: ciHelper.titleMessages.infoReq,
												text: 'La Oficina seleccionada ya fue agregada!',
												type: 'error'
											});
										}
									}
								}
							});
						});
					}
				});
				$.post('td/comi/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					for(var i=0,j=data.oficina.length; i<j; i++){
						var ofic = data.oficina[i];
						var $row = $('<tr class="item">');
						$row.append('<td>'+ofic.nomb+'</td>');
						$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
						$row.find('[name=btnEli]').click(function(){
							$(this).closest('.item').remove();
						});
						$row.data('data',ofic);
						p.$w.find('[name=gridOfi] tbody').append($row);
					}
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
			title: 'Seleccionar Tipo de Local',
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
					cols: ['','Nombre'],
					data: 'in/tipo/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
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
	['mg/ofic'],
	function(mgOfic){
		return tdComi;
	}
);