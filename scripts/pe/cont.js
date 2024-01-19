/*******************************************************************************
tipo de contrato */
peCont = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D: {
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peCont',
			titleBar: {
				title: 'Tipos de Contrato'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod.',f:'cod'},{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/cont/lista',
					params: {},
					itemdescr: 'tipo(s) de contrato',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peCont.windowNew();
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
						$row.append('<td>'+peCont.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peCont.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPeCont", {
							onShowMenu: function($row, menu) {
								$('#conMenPeCont_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenPeCont_hab',menu).remove();
								else $('#conMenPeCont_edi,#conMenPeCont_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeCont_ver': function(t) {
									peCont.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeCont_edi': function(t) {
									peCont.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeCont_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Contrato <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/cont/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Contrato Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peCont.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Contrato');
								},
								'conMenPeCont_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Contrato <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/cont/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Contrato Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peCont.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Contrato');
								},
								'conMenPeCont_def': function(t) {
									peCont.windowDefinir({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
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
			id: 'windowNewCont',
			title: 'Nuevo Tipo de Contrato',
			contentURL: 'pe/cont/edit',
			icon: 'ui-icon-plusthick',
			width: 445,
			height: 250,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.$w.find('[name=cod]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							cod_sunat: p.$w.find('[name=cod_sunat]').val()
						};
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de tipo de contrato!',type: 'error'});
						}else if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de tipo de contrato!',type: 'error'});
						}else if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el tipo de contrato!',type: 'error'});
						}
						if(data.cod_sunat==''){
							p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT para el tipo de contrato!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/cont/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El tipo de contrato fue registrado con &eacute;xito!'});
							peCont.init();
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
				p.$w = $('#windowNewCont');
				p.$w.find('[name=nomb]').focus();
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditCont'+p.id,
			title: 'Editar Tipo de Contrato: '+p.nomb,
			contentURL: 'pe/cont/edit',
			icon: 'ui-icon-pencil',
			width: 445,
			height: 250,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							descr: p.$w.find('[name=descr]').val(),
							cod_sunat: p.$w.find('[name=cod_sunat]').val()
						};
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el tipo de contrato!',type: 'error'});
						}
						if(data.cod_sunat==''){
							p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT para el tipo de contrato!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/cont/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El tipo de contrato fue actualizado con &eacute;xito!'});
							peCont.init();
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
				p.$w = $('#windowEditCont'+p.id);
				K.block({$element: p.$w});
				$.post('pe/cont/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').replaceWith(data.cod);
					p.$w.find('[name=nomb]').replaceWith(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=cod_sunat]').val(data.cod_sunat);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsCont'+p.id,
			title: 'Tipo de Contrato: '+p.nomb,
			contentURL: 'pe/cont/edit',
			icon: 'ui-icon-document',
			width: 445,
			height: 100,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsCont'+p.id);
				K.block({$element: p.$w});
				$.post('pe/cont/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').replaceWith(data.cod);
					p.$w.find('[name=nomb]').replaceWith(data.nomb);
					p.$w.find('[name=descr]').replaceWith(data.descr);
					p.$w.find('[name=cod_sunat]').replaceWith(data.cod_sunat);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDefinir: function(p){
		new K.Modal({
			id: 'windowDefinir'+p.id,
			title: 'Definir campos para '+p.nomb,
			contentURL: 'pe/cont/definir',
			icon: 'ui-icon-gear',
			width: 500,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							campos: []
						};
						for(var i=0; i<(p.$w.find('input:checked').length); i++){						
							var $radio = p.$w.find('input:checked').eq(i);
							data.campos.push({
								name: $radio.attr('name'),
								valid: true
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/cont/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El tipo de contrato fue actualizado con &eacute;xito!'});
							peCont.init();
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
				p.$w = $('#windowDefinir'+p.id);
				K.block({$element: p.$w});
				$.post('pe/cont/get',{id: p.id},function(data){
					if(data.campos!=null){
						for(var i=0,j=data.campos.length; i<j; i++){
							p.$w.find('[name='+data.campos[i].name+']').attr('checked','checked');
						}
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
			width: 750,
			height: 400,
			title: 'Seleccionar Tipo de Contrato',
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
					cols: ['','Cod.','Nombre'],
					data: 'pe/cont/lista',
					params: {},
					itemdescr: 'grupo(s) ocupacional(es)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.cod+'</td>');
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
	['mg/enti'],
	function(mgEnti){
		return peCont;
	}
);