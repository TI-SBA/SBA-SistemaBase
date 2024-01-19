inPlay = {
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
			mode: 'in',
			action: 'inPlay',
			titleBar: {
				title: 'Playas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'in/play/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							inPlay.windowNew();
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
						$row.append('<td>'+inPlay.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inPlay.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									inPlay.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									inPlay.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Playa <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/play/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Playa Habilitada',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inPlay.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Playa');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Playa <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/play/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Playa Deshabilitada',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inPlay.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Playa');
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
			id: 'windowNewPlay',
			title: 'Nueva Playa',
			contentURL: 'in/play/edit',
			width: 380,
			height: 270,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cuenta: p.$w.find('[name=cuenta]').data('data'),
							talonario_bol: p.$w.find('[name=tal_bol]').data('data'),
							talonario_fac: p.$w.find('[name=tal_fac]').data('data')
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Playa!',type: 'error'});
						}
						if(data.cuenta==null){
							p.$w.find('[name=btnCta]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
						}else data.cuenta = ctPcon.dbRel(data.cuenta);
						if(data.talonario_bol==null){
							p.$w.find('[name=btnBol]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el talonario para boletas!',type: 'error'});
						}else data.talonario_bol = cjTalo.dbRel(data.talonario_bol);
						if(data.talonario_fac==null){
							p.$w.find('[name=btnFac]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el talonario para facturas!',type: 'error'});
						}else data.talonario_fac = cjTalo.dbRel(data.talonario_fac);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/play/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Playa agregada!"});
							inPlay.init();
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
				p.$w = $('#windowNewPlay');
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnBol]').click(function(){
					cjTalo.windowSelect({callback: function(data){
						p.$w.find('[name=tal_bol]').html(cjTalo.types[data.tipo]+' '+data.serie).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnFac]').click(function(){
					cjTalo.windowSelect({callback: function(data){
						p.$w.find('[name=tal_fac]').html(cjTalo.types[data.tipo]+' '+data.serie).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'in/play/edit',
			width: 380,
			height: 270,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							cuenta: p.$w.find('[name=cuenta]').data('data'),
							talonario_bol: p.$w.find('[name=tal_bol]').data('data'),
							talonario_fac: p.$w.find('[name=tal_fac]').data('data')
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						if(data.cuenta==null){
							p.$w.find('[name=btnCta]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
						}else data.cuenta = ctPcon.dbRel(data.cuenta);
						if(data.talonario_bol==null){
							p.$w.find('[name=btnBol]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el talonario para boletas!',type: 'error'});
						}else data.talonario_bol = cjTalo.dbRel(data.talonario_bol);
						if(data.talonario_fac==null){
							p.$w.find('[name=btnFac]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el talonario para facturas!',type: 'error'});
						}else data.talonario_fac = cjTalo.dbRel(data.talonario_fac);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/play/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Tipo actualizado!"});
							inPlay.init();
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
				p.$w = $('#windowEditTipo');
				K.block({$element: p.$w});
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnBol]').click(function(){
					cjTalo.windowSelect({callback: function(data){
						p.$w.find('[name=tal_bol]').html(cjTalo.types[data.tipo]+' '+data.serie).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnFac]').click(function(){
					cjTalo.windowSelect({callback: function(data){
						p.$w.find('[name=tal_fac]').html(cjTalo.types[data.tipo]+' '+data.serie).data('data',data);
					},bootstrap: true});
				});
				$.post('in/play/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr)
						.data('data',data.cuenta);
					if(data.talonario_bol!=null){
						p.$w.find('[name=tal_bol]').html(cjTalo.types[data.talonario_bol.tipo]+' '+data.talonario_bol.serie)
							.data('data',data.talonario_bol);
					}
					if(data.talonario_fac!=null){
						p.$w.find('[name=tal_fac]').html(cjTalo.types[data.talonario_fac.tipo]+' '+data.talonario_fac.serie)
							.data('data',data.talonario_fac);
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
			title: 'Seleccionar Playa',
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
					data: 'in/play/lista',
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
	['mg/enti','ct/pcon','cj/talo'],
	function(mgEnti,ctPcon,cjTalo){
		return inPlay;
	}
);