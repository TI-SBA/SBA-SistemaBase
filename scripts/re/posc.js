/*******************************************************************************
Puntos de Servicio de Compra */
rePosc = {
	types: {
		V: 'POS VisaNet',
	},
	dbRel: function(talon){
		return {
			_id: talon._id.$id,
			serie: talon.serie,
			tipo: talon.tipo
		};
	},
	init: function(){
		K.initMode({
			mode: 're',
			action: 'rePosc',
			titleBar: {
				title: 'Configuraci&oacute;n de los POS en Cajas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','Proveedor','Nombre','Caja','Cuenta Contable','Cuenta Bancaria','Comisión','Fecha de registro'],
					data: 're/posc/lista',
					params: {},
					itemdescr: 'POS(s)',
					toolbarHTML: '<button type="button" class="btn btn-primary" name="btnAgregar">Nuevo POS</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							rePosc.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+((rePosc.types[data.proveedor]!=null)?rePosc.types[data.proveedor]:'--')+'</td>');
						$row.append('<td>'+data.nombre+'</td>');
						$row.append('<td>'+((data.caja!=null)?data.caja.nomb:'--')+'</td>');
						$row.append('<td>'+((data.ccontable!=null)?data.ccontable.descr:'--')+'</td>');
						$row.append('<td>'+((data.cbancaria!=null)?data.cbancaria.cod:'--')+'</td>');
						$row.append('<td>'+data.comision+'</td>');
						$row.append('<td>'+moment(data.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenList_edi,#conMenList_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									rePosc.windowEdit({id:K.tmp.data('id')});
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
		new K.Window({
			id: 'windowNewPOS',
			title: 'Nuevo POS',
			contentURL: 're/posc/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var caja = p.$w.find('[name=caja] :selected').data('data'),
						ccontable = ctPcon.dbRel(p.$w.find('[name=cuenta_contable]').data('data')),
						cbancaria = tsCtban.dbRel(p.$w.find('[name=cuenta_bancaria]').data('data')),
						cdiverso = cjConc.dbRel(p.$w.find('[name=concepto_ingdiv]').data('concs')),
						sdiverso = mgServ.dbRel(p.$w.find('[name=concepto_ingdiv]').data('serv')),
						data = {
							proveedor: p.$w.find('[name=proveedor] option:selected').val(),
							nombre: p.$w.find('[name=nombre]').val(),
							comision: p.$w.find('[name=comision]').val(),
							terminal: p.$w.find('[name=terminal]').val(),
							modelo: p.$w.find('[name=modelo]').val(),
						};
						if(caja!=null){
							data.caja = {
								_id: caja._id.$id,
								nomb: caja.nomb,
								local: {
									_id: caja.local._id.$id,
									descr: caja.local.descr,
									direccion: caja.local.direccion
								}
							};
						}
						if(ccontable==null){
							p.$w.find('[name=cuenta_contable]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cuenta contable donde se registrará la comision!',type: 'error'});
						}else data.ccontable = ccontable;
						if(cbancaria==null){
							p.$w.find('[name=cuenta_bancaria]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cuenta bancaria donde se registrará el monto original!',type: 'error'});
						}else data.cbancaria = cbancaria;
						if(cdiverso==null){
							p.$w.find('[name=concepto_ingdiv]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cuenta bancaria donde se registrará el monto original!',type: 'error'});
						}else data.cdiverso = cdiverso;
						if(sdiverso==null){
							p.$w.find('[name=concepto_ingdiv]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una concepto!',type: 'error'});
						}else data.sdiverso = sdiverso;
						if(data.proveedor==''){
							p.$w.find('[name=proveedor]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
						}else if(data.nombre==''){
							p.$w.find('[name=nombre]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre del POS!',type: 'error'});
						}else if(data.comision==''){
							p.$w.find('[name=comision]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar valor de comision!',type: 'error'});
						}
						if(isNaN(parseFloat(data.comision))){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'El monto indicado de comisión no es un valor numerico valido',
								type: 'error'
							});
						}else{
							data.comision = parseFloat(data.comision);
						}
						K.sendingInfo();
						$.post('re/posc/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El POS fue registrado con &eacute;xito!'});
							rePosc.init();
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
				p.$w = $('#windowNewPOS');
				K.block();
				$.post('cj/caja/all',function(caja){
					p.$w.find('[name=caja]').append('<option value="">Seleccionar Caja</option>');
					if(caja!=null){
						for(var i=0;i<caja.length;i++){
							p.$w.find('[name=caja]').append('<option value="'+caja[i]._id.$id+'">'+caja[i].nomb+'</option>');
							p.$w.find('[name=caja]').find('option:last').data('data',caja[i]);
						}
					}
					K.unblock();
				},'json');
				p.$w.find('[name=btnCC]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=cuenta_contable]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnCB]').click(function(){
					var $this = $(this);
					tsCtban.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=cuenta_bancaria]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnCI]').click(function(){
					var $this = $(this);
					mgServ.windowSelect({callback: function(data){
						$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
							if(concs.serv==null){
								return K.notification({
									title: 'Servicio inv&aacute;lido',
									text: 'El servicio seleccionado no tiene conceptos asociados!',
									type: 'error'
								});
							}
							$.post('mg/serv/get','_id='+data._id.$id,function(serv){
								if(serv==null){
									return K.notification({
										title: 'Servicio inv&aacute;lido',
										text: 'No existe el servicio seleccionado!',
										type: 'error'
									});
								}
								$this.closest('.input-group').find('[name=concepto_ingdiv]').html(concs.serv[0].nomb).data('concs',concs.serv[0]).data('serv',serv);
							},'json');
						},'json');

					},bootstrap: true});
				});
				p.$w.find('[name=comision]').numeric();
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowEditTalo'+p.id,
			title: 'Editar Talonario',
			contentURL: 're/posc/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var caja = p.$w.find('[name=caja] :selected').data('data'),
						ccontable = ctPcon.dbRel(p.$w.find('[name=cuenta_contable]').data('data')),
						cbancaria = tsCtban.dbRel(p.$w.find('[name=cuenta_bancaria]').data('data')),
						cdiverso = cjConc.dbRel(p.$w.find('[name=concepto_ingdiv]').data('concs')),
						sdiverso = mgServ.dbRel(p.$w.find('[name=concepto_ingdiv]').data('serv')),
						data = {
							_id: p.id,
							proveedor: p.$w.find('[name=proveedor] option:selected').val(),
							nombre: p.$w.find('[name=nombre]').val(),
							comision: p.$w.find('[name=comision]').val(),
							terminal: p.$w.find('[name=terminal]').val(),
							modelo: p.$w.find('[name=modelo]').val(),

						};
						if(caja!=null){
							data.caja = {
								_id: caja._id.$id,
								nomb: caja.nomb,
								local: {
									_id: caja.local._id.$id,
									descr: caja.local.descr,
									direccion: caja.local.direccion
								}
							};
						}
						if(ccontable==null){
							p.$w.find('[name=cuenta_contable]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cuenta contable donde se registrará la comision!',type: 'error'});
						}else data.ccontable = ccontable;
						if(cbancaria==null){
							p.$w.find('[name=cuenta_bancaria]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cuenta bancaria donde se registrará el monto original!',type: 'error'});
						}else data.cbancaria = cbancaria;
						if(cdiverso==null){
							p.$w.find('[name=concepto_ingdiv]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un concepto donde se registrará el monto original!',type: 'error'});
						}else data.cdiverso = cdiverso;
						if(sdiverso==null){
							p.$w.find('[name=concepto_ingdiv]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una concepto!',type: 'error'});
						}else data.sdiverso = sdiverso;
						if(data.proveedor==''){
							p.$w.find('[name=proveedor]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
						}else if(data.nombre==''){
							p.$w.find('[name=nombre]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre del POS!',type: 'error'});
						}else if(data.comision==''){
							p.$w.find('[name=comision]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar valor de comision!',type: 'error'});
						}
						if(isNaN(parseFloat(data.comision))){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'El monto indicado de comisión no es un valor numerico valido',
								type: 'error'
							});
						}else{
							data.comision = parseFloat(data.comision);
						}
						K.sendingInfo();
						$.post('re/posc/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El POS fue registrado con &eacute;xito!'});
							rePosc.init();
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
				p.$w = $('#windowEditTalo'+p.id);
				K.block();
				$.post('cj/caja/all',function(caja){
					p.$w.find('[name=caja]').append('<option value="">Seleccionar Caja</option>');
					if(caja!=null){
						for(var i=0;i<caja.length;i++){
							p.$w.find('[name=caja]').append('<option value="'+caja[i]._id.$id+'">'+caja[i].nomb+'</option>');
							p.$w.find('[name=caja]').find('option:last').data('data',caja[i]);
						}
					}
					p.$w.find('[name=btnCC]').click(function(){
						var $this = $(this);
						ctPcon.windowSelect({callback: function(data){
							$this.closest('.input-group').find('[name=cuenta_contable]').html(data.cod).data('data',data);
						},bootstrap: true});
					});
					p.$w.find('[name=btnCB]').click(function(){
						var $this = $(this);
						tsCtban.windowSelect({callback: function(data){
							$this.closest('.input-group').find('[name=cuenta_bancaria]').html(data.cod).data('data',data);
						},bootstrap: true});
					});
					p.$w.find('[name=btnCI]').click(function(){
						var $this = $(this);
						mgServ.windowSelect({callback: function(data){
							$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
								if(concs.serv==null){
									return K.notification({
										title: 'Servicio inv&aacute;lido',
										text: 'El servicio seleccionado no tiene conceptos asociados!',
										type: 'error'
									});
								}
								$.post('mg/serv/get','_id='+data._id.$id,function(serv){
									if(serv==null){
										return K.notification({
											title: 'Servicio inv&aacute;lido',
											text: 'No existe el servicio seleccionado!',
											type: 'error'
										});
									}
									$this.closest('.input-group').find('[name=concepto_ingdiv]').html(concs.serv[0].nomb).data('concs',concs.serv[0]).data('serv',serv);
								},'json');
							},'json');

						},bootstrap: true});
					});
					p.$w.find('[name=comision]').numeric();
					$.post('re/posc/get','id='+p.id+'&edit=1',function(data){
						if(data.caja!=null){
							p.$w.find('[name=caja]').val(data.caja._id.$id);
						}
						if(data.ccontable!=null){
							p.$w.find('[name=cuenta_contable]').html(data.ccontable.cod).data('data',data.ccontable);
						}
						if(data.cbancaria!=null){
							p.$w.find('[name=cuenta_bancaria]').html(data.cbancaria.cod).data('data',data.cbancaria);
						}
						if(data.cdiverso!=null){
							p.$w.find('[name=concepto_ingdiv]').html(data.cdiverso.nomb).data('concs',data.cdiverso);
						}
						if(data.sdiverso!=null){
							p.$w.find('[name=concepto_ingdiv]').data('serv',data.sdiverso);
						}
						p.$w.find('[name=proveedor]').val(data.proveedor);
						p.$w.find('[name=nombre]').val(data.nombre);
						p.$w.find('[name=comision]').val(data.comision);
						p.$w.find('[name=terminal]').val(data.terminal);
						p.$w.find('[name=modelo]').val(data.modelo);

						K.unblock();
					},'json');
				},'json');
			}
		});
	},
};
define(
	['ts/ctban'],
	function(tsCtban){
		return rePosc;
	}
);