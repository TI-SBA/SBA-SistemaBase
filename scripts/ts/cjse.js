tsCjse = {
			states: {
		A: {
			descr: "Aperturado",
			color: "green",
			label: '<span class="label label-success">Aperturado</span>'
		},
		C:{
			descr: "Cerrado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Cerrado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			caja: item.caja
			
			};
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsCjse',
			titleBar: {
				title: 'Caja Chica - Sesiones'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Responsable','Caja','Saldo Inicial','Saldo Final','Fecha de Modificacion'],
					data: 'ts/cjse/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsCjse.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+tsCjse.states[data.estado].label+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.responsable)+'</td>');
						$row.append('<td>'+data.caja.nomb+'</td>');
						$row.append('<td>'+data.saldo_inicial+'</td>');
						$row.append('<td>'+data.saldo_final+'</td>');
						
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data', data).dblclick(function(){
							tsCjse.windowDetails({_id: $(this).data('id'),titu: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenTsCjse", {
							onShowMenu: function($row, menu) {
								// LAS SESIONES NO SE PUEDEN EDITAR
								$('#conMenTsCjse_edi',menu).remove();
								//NO PUEDES ELIMINAR UN DOCUMENTO APROBADO 
								if($row.data('data').estado == 'A'){
									$('#conMenTsCjse_eli',menu).remove();
								}
								//NO PUEDES CERRAR UN DOCUMENTO CANCELADO
								if($row.data('data').estado == 'C'){
									$('#conMenTsCjse_cerrar',menu).remove();
								}
								return menu;
							},
							bindings: {	
								'conMenTsCjse_edi': function(t) {
									tsCjse.windowEdit({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenTsCjse_repoaux': function(t) {
									var url = 'ts/cjse/reporte_auxiliar_estandar?_id='+K.tmp.data("id");
									window.open(url);
								},
								'conMenTsCjse_repogaspu': function(t) {
									var url = 'ts/cjse/reporte_gastos_publicos?_id='+K.tmp.data("id");
									window.open(url);
								},
								'conMenTsCjse_cerrar': function(t) {
									tsCjse.windowSesion({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenTsCjse_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Caja Chica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ts/cjse/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Caja Chica Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tsCjse.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Caja Chica');
								},
								'conMenTsCjse_repo':function(t6){
 									var url = 'ts/cjse/reporte?_id='+K.tmp.data("id");
									window.open(url);
								},
								'conMenTsCjse_reset': function(t) {
									ciHelper.confirm('&#191;Desea <b>Recalcular</b> los movimientos de Caja Chica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ts/cjse/reset',{sesion: K.tmp.data('id')},function(result){
											K.clearNoti();
											K.notification({title: 'Mensaje del Sistema!', type: result.states,text: result.message});
											//K.notification({title: 'Caja Chica Recalculada',text: 'Se recalculo los movimientos de la caja chica con exito'});
											tsCjse.init();
										});
									},function(){
										$.noop();
									},'Recalculo de Caja Chica');
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
			id: 'windowNewCajaSesion',
			title: 'Nueva Sesion de Caja Chica',
			contentURL: 'ts/cjse/edit',
			width: 700,
			height: 500,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						if(p.$w.find('[name=caja]').data('data') == null){
							p.$w.find('[name=caja] [name=btnSel]').click();
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe seleccionar una Caja',
									type: 'error'
								});
						}

						var data = {
							
							responsable: 	p.$w.find('[name=responsable] [name = mini_enti]').data('data'),
							//estado: 		p.$w.find('[name=estado]').val(),
							caja: 			tsCjch.dbRel(p.$w.find('[name=caja]').data('data')),
							deber_anterior: parseFloat(p.$w.find('[name=deber_anterior]').val()),
							haber_anterior: parseFloat(p.$w.find('[name=haber_anterior]').val()),
							saldo_anterior: parseFloat(p.$w.find('[name=saldo_anterior]').val()),
							rendicion:{
								fecren: 	p.$w.find('[name=rendicion_fec]').val(),
								tipo: 		p.$w.find('[name=rendicion_tipo]').val(),
								numero: 	p.$w.find('[name=rendicion_num]').val(),
								monto: 		parseFloat(p.$w.find('[name=rendicion_monto]').val()),
							},
							saldo_inicial: 	p.$w.find('[name=saldo_inicial]').val(),
							haber_final: 	parseFloat(p.$w.find('[name=haber_anterior]').val()) + parseFloat(p.$w.find('[name=rendicion_monto]').val()),
							deber_final: 	parseFloat(p.$w.find('[name=deber_anterior]').val()),
							saldo_final: 	parseFloat(p.$w.find('[name=saldo_inicial]').val()),

						};
						if(data.responsable==null){
							p.$w.find('[name=responsable] [name=btnSel]').click();
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe seleccionar una Entidad',
									type: 'error'
								});
						}else {
							data.responsable = mgEnti.dbRel(data.responsable);
						}
						if(data.caja==null){
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una Caja',
								type: 'error'
							});
						}
						if(data.saldo_inicial==null){
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'No se encuentra el saldo Inicial',
								type: 'error'
							});
						}else{
							if(data.saldo_inicial==''){
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El saldo inicial esta vacio',
									type: 'error'
								});
							} else if (!$.isNumeric(data.saldo_inicial)) {
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El saldo inicial no es un numero',
									type: 'error'
								});
							}
						}
						if(data.deber_anterior==null){
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'No se encuentra el saldo Inicial',
								type: 'error'
							});
						}else{
							if(data.deber_anterior==''){
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El saldo inicial esta vacio',
									type: 'error'
								});
							} else if (!$.isNumeric(data.deber_anterior)) {
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El saldo inicial no es un numero',
									type: 'error'
								});
							}
						}
						if(data.rendicion==null){
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'No se detecta la rencion en general',
								type: 'error'
							});
						}else{
							if(data.rendicion.fecren==''){
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'La fecha de rendicion esta vacio',
									type: 'error'
								});
							} else if (data.rendicion.tipo=='') {
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El tipo de rendicion esta vacio',
									type: 'error'
								});
							} else if (data.rendicion.numero=='') {
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El numero de rendicion esta vacio',
									type: 'error'
								});
							} else if (data.rendicion.monto=='') {
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El monto de rendicion esta vacio',
									type: 'error'
								});
							} else if (!$.isNumeric(data.rendicion.monto)) {
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El monto de rendicion no es numerico',
									type: 'error'
								});
							}
						}
						console.log("SALDO ANTERIOR:"+data.saldo_anterior);
						console.log("RESTA: "+( data.deber_anterior-data.haber_anterior))
						if(data.saldo_anterior != K.round((data.deber_anterior - data.haber_anterior),2)){
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'El saldo anterior no coincide con la diferencia del haber anterior y el deber anterior',
								type: 'error'
							});
						}
						if(data.saldo_inicial != K.round((data.saldo_anterior + data.rendicion.monto),2)){
							console.log(data.saldo_inicial);
							console.log(data.saldo_anterior+data.rendicion.monto);
							console.log(data.saldo_anterior);
							console.log(data.rendicion.monto);
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'El saldo inicial no coincide con la suma del saldo anterior mas el monto de la rendicion',
								type: 'error'
							});
						}

						//K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/cjse/save",data,function(result){
							K.clearNoti();
							K.msg({title: 'Mensaje del Sistema!', type: result.states,text: result.message});
							tsCjse.init();
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
				p.$w = $('#windowNewCajaSesion');
				p.$w.find("[name=rendicion_fec]").datepicker({
					format: 'yyyy-mm-dd',
		    		fecDate: '-3d'
				});	
				
				// Inicializar Monto
				p.$w.find('[name=deber_anterior]').val(parseFloat(0));
				p.$w.find('[name=haber_anterior]').val(parseFloat(0));
				p.$w.find('[name=saldo_anterior]').val(parseFloat(0));
				p.$w.find('[name=rendicion_monto]').val(parseFloat(0));
				p.$w.find('[name=saldo_inicial]').val(parseFloat(0));

				// Responsable
				p.$w.find('[name=responsable] .panel-title').html('Datos del responsable');
				p.$w.find('[name=responsable] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=responsable] [name=mini_enti]'),data);
						},bootstrap: true});
				});

				p.$w.find('[name=responsable] [name=btnAct]').click(function(){
					if(p.$w.find('[name=responsable] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe Elegir una Cantidad',
							type: 'Error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=responsable] [name=mini_enti]'),data);
						},id: p.$w.find('[name=responsable] [name=mini_enti]').data('data')._id.$id});
					}
				});

				//}else data.responsable = mgEnti.dbRel(data.responsable);

				p.$w.find('[name=deber_anterior]').keyup(function(){
					p.$w.find('[name=saldo_anterior]').val(K.round(parseFloat(p.$w.find('[name=deber_anterior]').val())-parseFloat(p.$w.find('[name=haber_anterior]').val()),2));
					p.$w.find('[name=saldo_inicial]').val(K.round(parseFloat(p.$w.find('[name=saldo_anterior]').val())+parseFloat(p.$w.find('[name=rendicion_monto]').val()),2));
				});

				p.$w.find('[name=haber_anterior]').keyup(function(){
					p.$w.find('[name=saldo_anterior]').val(K.round(parseFloat(p.$w.find('[name=deber_anterior]').val())-parseFloat(p.$w.find('[name=haber_anterior]').val()),2));
					p.$w.find('[name=saldo_inicial]').val(K.round(parseFloat(p.$w.find('[name=saldo_anterior]').val())+parseFloat(p.$w.find('[name=rendicion_monto]').val()),2));
				});

				p.$w.find('[name=rendicion_monto]').keyup(function(){
					p.$w.find('[name=saldo_inicial]').val(K.round(parseFloat(p.$w.find('[name=saldo_anterior]').val())+parseFloat(p.$w.find('[name=rendicion_monto]').val()),2));
				});
				
				p.$w.find("[name=btnCaja]").click(function(){
					tsCjch.windowSelect({callback: function(data){
						p.$w.find('[name=caja]').html(data.nomb).data('data',data);
						//p.$w.find('[name=id_caja]').html(data._id.$id).data('data',data);
						var var_caja = {
							caja: data._id.$id
						};
						$.post("ts/cjse/get_last",var_caja,function(result){
							if (result.deber_final != null) p.$w.find('[name=deber_anterior]').val(parseFloat(result.deber_final));
							if (result.haber_final != null) p.$w.find('[name=haber_anterior]').val(parseFloat(result.haber_final));
							if (result.saldo_final != null) p.$w.find('[name=saldo_anterior]').val(parseFloat(result.saldo_final));
							if (result.saldo_final != null) p.$w.find('[name=saldo_inicial]').val(K.round(parseFloat(result.saldo_final)+parseFloat(p.$w.find('[name=rendicion_monto]').val()),2));
						},'json');
					},bootstrap: true});
				});

			}

		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowNewCajaSesion',
			title: 'Editar Caja Chica: '+p.nro,
			contentURL: 'ts/cjse/edit',
			width: 700,
			height: 500,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							responsable: p.$w.find('[name=responsable] [name = mini_enti]').data('data'),
							//estado: p.$w.find('[name=estado]').val(),
							saldo_inicial: p.$w.find('[name=saldo_inicial]').val(),
							caja: tsCjch.dbRel(p.$w.find('[name=caja]').data('data')),
						};

						if(data.caja==''){
							p.$w.find('[name=caja]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo Caja!',type: 'error'});
						}

						if(data.responsable==null){
							p.$w.find('[name=responsable] [name=btnSel]').click();
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe seleccionar una Entidad',
									type: 'error'
								});
						}else {
							data.responsable = mgEnti.dbRel(data.responsable);
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/cjse/save",data,function(result){
							K.clearNoti();
							K.msg({title: 'Mensaje del Sistema!', type: result.states,text: result.message});
							tsCjse.init();
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
				p.$w = $('#windowNewCajaSesion');
				p.$w.find("[name=fecdoc]").datepicker({
		   				format: 'yyyy-mm-ts',
		    			fecDate: '-3d'
				});	
				p.$w.find("[name=btnCaja]").click(function(){
					tsCjse.windowSelect({callback: function(data){
						p.$w.find('[name=caja]').html(data.nomb).data('data',data);
						//p.$w.find('[name=id_caja]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				K.block();
			

				$.post('ts/cjse/get',{_id: p.id},function(data){
							mgEnti.fillMini(p.$w.find('[name=responsable] [name = mini_enti]'),data.responsable);
							//p.$w.find('[name=estado]').val(data.estado);
							p.$w.find('[name=saldo_inicial]').val(data.saldo_inicial);
							p.$w.find('[name=caja]').html(data.caja.nomb).data('data',data.caja);

							

				K.unblock();
				},'json');
			}
		});
	},
	windowSesion: function(p){
		new K.Modal({ 
			id: 'windowNewCerrar',
			title: 'Cerrar Sesion de Caja Chica: '+p.nro,
			contentURL: 'ts/cjse/sesion',
			width: 500,
			height: 150,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							feccie: p.$w.find('[name=feccie]').val(),
							estado: p.$w.find('[name=estado]').val(),
							
						};


						/*
						if(data.titu==''){
							p.$w.find('[name=titu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cant==''){
							p.$w.find('[name=cant]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.nro==''){
							p.$w.find('[name=nro]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
												
						if(data.remi==''){
							p.$w.find('[name=remi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}

						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.tise==''){
							p.$w.find('[name=tise]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.tipo_==''){
							p.$w.find('[name=tipo_]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/cjse/cierre",data,function(result){
							K.clearNoti();
							K.msg({title: 'Mensaje del Sistema!', type: result.states,text: result.message});
							tsCjse.init();
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
				p.$w = $('#windowNewCerrar');
				p.$w.find("[name=feccie]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			fecDate: '-3d'
				});	
				K.block();
				$.post('ts/cjse/get',{_id: p.id},function(data){
							
							p.$w.find('[name=feccie]').val(data.feccie);
							

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
			title: 'Seleccionar Sesion de Caja',
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
				var params = {};
				if( p.estado!= null ) if( p.estado === 'A' ) params.estado = 'A';
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'ts/cjse/lista',
					params: params,
					itemdescr: 'Caja(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.caja.nomb+'</td>');
						
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
	['mg/enti','mg/ofic','mg/prog','ts/cjch'],
	function(mgEnti,mgOfic,mgProg,tsCjch){
		return tsCjse;
	}
);
