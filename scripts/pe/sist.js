/*******************************************************************************
sistemas de pension */
peSist = {
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
	/*tipo: {
		PU: "D.L.19990",
		PR: "D.L.20530"
	},*/
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peSist',
			titleBar: {
				title: 'Sistemas de Pensi&oacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Sigla',f:'sigla'},{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/sist/lista',
					params: {},
					itemdescr: 'sistema(s) de pensi&oacute;n',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peSist.windowNew();
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
						$row.append('<td>'+peSist.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.tipo+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peSist.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPeSist", {
							onShowMenu: function($row, menu) {
								$('#conMenPeSist_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenPeSist_hab',menu).remove();
								else $('#conMenPeSist_edi,#conMenPeSist_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeSist_ver': function(t) {
									peSist.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeSist_edi': function(t) {
									peSist.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeSist_act': function(t) {
									peSist.windowAct({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeSist_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Sistema de Pensi&oacute;n <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/sist/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Sistema de Pensi&oacute;n Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peSist.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Sistema de Pensi&oacute;n');
								},
								'conMenPeSist_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Sistema de Pensi&oacute;n <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/sist/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Sistema de Pensi&oacute;n Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peSist.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Sistema de Pensi&oacute;n');
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
			id: 'windowNewSist',
			title: 'Nuevo Sistema de Pensi&oacute;n',
			contentURL: 'pe/sist/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							//tipo: p.$w.find('[name=tipo] option:selected').val()
							tipo: p.$w.find('[name=tipo]').val(),
							entidad: p.$w.find('[name=enti]').data('data')
						};
						if(data.cod_sunat==''){
							p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT para el tipo de contrato!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de sistema de pensi&oacute;n!',type: 'error'});
						}else if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el sistema de pensi&oacute;n!',type: 'error'});
						}else if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo para el sistema de pensi&oacute;n!',type: 'error'});
						}else if(data.entidad==null){
							K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo para el sistema de pensi&oacute;n!',type: 'error'});
							return p.$w.find('[name=btnSelect]').click();
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/sist/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El sistema de pensi&oacute;n fue registrado con &eacute;xito!'});
							peSist.init();
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
				p.$w = $('#windowNewSist');
				K.block({$element: p.$w});
				p.$w.find('[name=btnSelect]').click(function(){
					mgEnti.windowSelect({$window: p.$w,callback: function(data){
						p.$w.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',ciHelper.enti.dbRel(data));
						p.$w.find('[name=nomb]').val(ciHelper.enti.formatName(data));
					},filter: [
					    {nomb: 'tipo_enti',value: 'E'}
					],
					bootstrap: true});
				});
				p.$w.find('[name=cod_sunat]').focus();
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditSist'+p.id,
			title: 'Editar Sistema de Pensi&oacute;n: '+p.nomb,
			contentURL: 'pe/sist/edit',
			icon: 'ui-icon-pencil',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							//tipo: p.$w.find('[name=tipo] option:selected').val()
							tipo: p.$w.find('[name=tipo]').val(),
							entidad: p.$w.find('[name=enti]').data('data')
						};
						if(data.cod_sunat==''){
							p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT para el tipo de contrato!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de sistema de pensi&oacute;n!',type: 'error'});
						}else if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el sistema de pensi&oacute;n!',type: 'error'});
						}else if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo para el sistema de pensi&oacute;n!',type: 'error'});
						}else if(data.entidad==null){
							K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo para el sistema de pensi&oacute;n!',type: 'error'});
							return p.$w.find('[name=btnSelect]').click();
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/sist/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El sistema de pensi&oacute;n fue actualizado con &eacute;xito!'});
							peSist.init();
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
				p.$w = $('#windowEditSist'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnSelect]').click(function(){
					mgEnti.windowSelect({$window: p.$w,callback: function(data){
						p.$w.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',ciHelper.enti.dbRel(data));
						p.$w.find('[name=nomb]').val(ciHelper.enti.formatName(data));
					},filter: [
					    {nomb: 'tipo_enti',value: 'E'}
					],
					bootstrap: true});
				});
				$.post('pe/sist/get','id='+p.id,function(data){
					p.$w.find('[name=cod_sunat]').focus().val(data.cod_sunat);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=tipo]').val(data.tipo);
					if(data.entidad){
						p.$w.find('[name=enti]').html(ciHelper.enti.formatName(data.entidad)).data('data',ciHelper.enti.dbRel(data.entidad));
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsSist'+p.id,
			title: 'Sistema de Pensi&oacute;n: '+p.nomb,
			contentURL: 'pe/sist/details',
			icon: 'ui-icon-document',
			width: 550,
			height: 450,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsSist'+p.id);
				K.block({$element: p.$w});
				$.post('pe/sist/get','id='+p.id,function(data){
					p.$w.find('[name=cod_sunat]').html(data.cod_sunat);
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					//p.$w.find('[name=tipo]').html(peSist.tipo[data.tipo]);
					p.$w.find('[name=tipo]').html(data.tipo);
					if(data.porcentajes!=null){
						for(var i=0,j=data.porcentajes.length; i<j; i++){
							var $row = p.$w.find('.gridReference').clone();
							$row.find('li:eq(0)').html(data.porcentajes[i].descr);
							$row.find('li:eq(1)').html(data.porcentajes[i].cod);
							$row.find('li:eq(2)').html(data.porcentajes[i].val);
							$row.wrapInner('<a class="item">');
							p.$w.find('.gridBody').append($row.children());
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowAct: function(p){
		new K.Window({
			id: 'windowActSist'+p.id,
			title: 'Actualizar Porcentajes: '+p.nomb,
			//contentURL: 'pe/sist/porc',
			content: '<div name="grid"></div>',
			icon: 'ui-icon-refresh',
			width: 700,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							porcentajes: []
						};
						if(p.$w.find('.item').length!=0){
							for(var i=0,j=p.$w.find('[name=grid] tbody .item').length; i<j; i++){
								var $row = p.$w.find('[name=grid] tbody .item').eq(i),
								tmp = {
									descr: $row.find('[name=descr]').val(),
									cod: $row.find('[name=cod]').val(),
									val: $row.find('[name=val]').val()
								};
								if(tmp.descr==''){
									$row.find('[name=descr]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar la descripci&oacute;n del porcentaje!',
										type: 'error'
									});
								}
								if(tmp.cod==''){
									$row.find('[name=cod]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar el c&oacute;digo SUNAT del porcentaje!',
										type: 'error'
									});
								}
								if(tmp.val==''){
									$row.find('[name=val]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar el valor del porcentaje!',
										type: 'error'
									});
								}
								data.porcentajes.push(tmp);
							}
						}else{
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar al menos un porcentaje!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/sist/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El sistema de pensi&oacute;n fue actualizado con &eacute;xito!'});
							peSist.init();
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
			onClose: function(){
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowActSist'+p.id);
				K.block({$element: p.$w});
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','C&oacute;digo SUNAT','Porcentaje','&nbsp;'],
					onlyHtml: true,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							if(p.$w.find('[name=grid] tbody .item').length==10){
								K.clearNoti();
								return K.notification({
									title: 'M&aacute;ximo de Porcentajes alcanzado!',
									text: 'No se puede ingresar m&aacute;s de diez porcentajes para el sistema de pensi&oacute;n!',
									type: 'error'
								});
							}
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" name="descr"></td>');
							$row.append('<td><input type="text" name="cod"></td>');
							$row.append('<td><input type="number" name="val"></td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
								if(p.$w.find('[name=grid] tbody.item').length==0){
									p.$w.find('[name=btnAgregar]').click();
								}
							});
							p.$w.find('[name=grid] tbody').append($row);
						});
					}
				});
				$.post('pe/sist/get','id='+p.id,function(data){
					if(data.porcentajes!=null){
						for(var i=0,j=data.porcentajes.length; i<j; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" name="descr" value="'+data.porcentajes[i].descr+'"></td>');
							$row.append('<td><input type="text" name="cod" value="'+data.porcentajes[i].cod+'"></td>');
							$row.append('<td><input type="number" name="val" value="'+data.porcentajes[i].val+'"></td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
								if(p.$w.find('[name=grid] tbody.item').length==0){
									p.$w.find('[name=btnAgregar]').click();
								}
							});
							p.$w.find('[name=grid] tbody').append($row);
						}
					}else{
						p.$w.find('[name=btnAgregar]').click();
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
			title: 'Seleccionar Sistema de Pensi&oacute;n',
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
					data: 'pe/sist/lista',
					params: {},
					itemdescr: 'sistema(s) de pensi&oacute;n',
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
	['mg/enti'],
	function(mgEnti){
		return peSist;
	}
);