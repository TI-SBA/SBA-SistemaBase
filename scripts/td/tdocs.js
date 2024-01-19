/*Tipos de Documentos*/
tdTdocs = {
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
			action: 'tdTdocs',
			titleBar: {
				title: 'Tipo de Documentos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'}],
					data: 'td/tdoc/lista',
					params: {},
					itemdescr: 'tipo(s) de documento',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tdTdocs.windowNew();
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
						$row.append('<td>'+tdTdocs.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							tdTdocs.windowEdit({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),data: $(this).data('data')});
						}).data('estado',data.estado).contextMenu('conMenListEd', {
							onShowMenu: function(e, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									tdTdocs.windowEdit({id: K.tmp.data('id'),data: K.tmp.data('data')});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Documento <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('td/tdoc/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Documento Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tdTdocs.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Documento');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Documento <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('td/tdoc/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Documento Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tdTdocs.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Documento');
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
		$.extend(p,{
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val()
						};
						if(data.nomb == ""){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre del tipo de documento!",type:"error"});
						}
						K.clearNoti();
						K.sendingInfo();
						if(p.$w.find('#div_buttons').length>0)
							p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("td/tdoc/save",data,function(rpta){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Tipo de Documento agregado!"});
							//tdTdocs.init();
							if(p.cbOnClose!=null){
								p.cbOnClose();
							}
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
			}
		});
		if(p.bootstrap==null){
			p.buttons = {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						nomb: p.$w.find('[name=nomb]').val()
					};
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre del tipo de documento!",type:"error"});
					}
					K.clearNoti();
					K.sendingInfo();
					if(p.$w.find('#div_buttons').length>0)
						p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post("td/tdoc/save",data,function(rpta){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Tipo de Documento agregado!"});
						//tdTdocs.init();
						if(p.cbOnClose!=null){
							p.cbOnClose(rpta);
						}
						K.closeWindow(p.$w.attr('id'));
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			};
		}
		new K.Window({
			id: "windowNew",
			title: "Agregar Tipo de Documento",
			contentURL: "td/tdoc/edit",
			icon: 'ui-icon-document',
			width : 400,
			height : 70,
			buttons: p.buttons,
			onContentLoaded: function(){
				p.$w = $("#windowNew");
				p.$w.find('[name=nomb]').focus();
			}
		});
	},
	windowEdit: function(p){
		K.Window({
			id: "windowEdit"+p.id,
			title: "Actualizar Tipo de Documento",
			contentURL: "td/tdoc/edit",
			icon: 'ui-icon-document',
			width : 400,
			height : 80,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val()
						};
						if(data.nomb == ""){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre del tipo de documento!",type:"error"});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("td/tdoc/save",data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Registro Guardado"});
							K.closeWindow("windowEdit"+p.id);
							tdTdocs.init();
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
				p.$w.find('[name=nomb]').val(p.data.nomb);
				p.$w.find('[name=nomb]').focus();
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
	function(){
		return tdTdocs;
	}
);