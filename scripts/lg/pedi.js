lgPedi = {
	states: {
		A: {
			descr: "Aprobado",
			color: "green",
			label: '<span class="label label-success">Aprobado</span>'
		},
		P:{
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-default">Pendiente</span>'
		},
		X:{
			descr: "Anulado",
			color: "#CCCCCC",
			label: '<span class="label label-danger">Anulado</span>'
		},
		R:{
			descr: "Rechazado",
			color: "#CCCCCC",
			label: '<span class="label label-danger">Rechazado</span>'
		}
	},
	states_revision:{
		A: {
			descr: "Aceptado",
			color: "green",
			label: '<span class="label label-success">Aceptado</span>'
		},
		R: {
			descr: "Rechazado",
			color: "green",
			label: '<span class="label label-danger">Rechazado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			tipo: item.tipo
		};
	},
	init_nuev: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPedi_nuev',
			titleBar: {
				title: 'Mis Requerimientos'
			}
		});
		if(K.session.enti.roles.trabajador.oficina!=null){
			new K.Panel({
				onContentLoaded: function(){
					var $grid = new K.grid({
						cols: ['','','Revision',{n:'Nro',f:'num'},{n:'Trabajador',f:'trabajador.appat'},{n:'Programa',f:'programa.nomb'},{n:'Oficina',f:'oficina.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
						data: 'lg/pedi/lista',
						params: {oficina: K.session.enti.roles.trabajador.oficina._id.$id, "tipo":"B"},
						itemdescr: 'requerimiento(s)',
						toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i>Crear Nuevo</button> <select name="tipo" class="form-control"><option value="B">Bienes</option><option value="S">Servicios</option><option value="L">Locacion</option></select>',
						onContentLoaded: function($el){
							$el.find('[name=tipo]').change(function(){
								$grid.reinit({params:{oficina: K.session.enti.roles.trabajador.oficina._id.$id, "tipo":$el.find('[name=tipo] :selected').val()}});
							});
							$el.find('[name=btnAgregar]').click(function(){
								lgPedi.windowNew({tipo: $el.find('[name=tipo] :selected').val(),goBack: function(){
									lgPedi.init_nuev();
								}});
							});
						},
						onLoading: function(){ K.block(); },
						onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+lgPedi.states[data.estado].label+'</td>');
							var revision = '--';
							if(data.revisiones!=null){
								if(data.revisiones.length>0){
									revision = lgPedi.states_revision[data.revisiones[data.revisiones.length-1].estado].label;
								}
							}
							$row.append('<td>'+revision+'</td>');
							$row.append('<td>'+data.cod+'</td>');
							$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
							$row.append('<td>'+data.programa.nomb+'</td>');
							$row.append('<td>'+data.oficina.nomb+'</td>');
							$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
							$row.data('id',data._id.$id).data('data',data).dblclick(function(){
								lgPedi.windowDetails({id: $(this).data('id'), tipo: $(this).data('data').tipo,nomb: $(this).find('td:eq(2)').html(),goBack: function(){
									lgPedi.init_nuev();
								}});
							}).data('estado',data.estado).contextMenu("conMenLgPedp", {
								onShowMenu: function($row, menu) {
									$('#conMenLgPedp_fin',menu).remove();
									if($row.data('data').estado!='P'){
										$('#conMenLgPedp_edi',menu).remove();
									}
									return menu;
								},
								bindings: {
									'conMenLgPedp_ver': function(t) {
										lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
											lgPedi.init_nuev();
										}});
									},
									'conMenLgPedp_edi': function(t) {
										lgPedi.windowEdit({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
											lgPedi.init_nuev();
										}});
									},
									'conMenLgPedp_rev': function(t) {
										lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
											lgPedi.init_nuev();
										},revisar: true});
									}
								}
							});
							return $row;
						}
					});
				}
			});
		}else{
			return K.notification({
				title: ciHelper.titleMessages.infoReq,
				text: 'Su usuario no cuenta con una oficina relacionada!',
				type: 'error'
			});	
		}
	},
	init_bien: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPedi_bien',
			titleBar: {
				title: 'Requerimientos Pendientes: Bienes'
			}
		});	
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Revision','Rev. Logistica',{n:'Nro',f:'num'},{n:'Trabajador',f:'trabajador.appat'},{n:'Programa',f:'programa.nomb'},{n:'Oficina',f:'oficina.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/pedi/lista',
					params: {estado: 'P', "tipo":"B"},
					itemdescr: 'requerimiento(s) de bienes',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgPedi.states[data.estado].label+'</td>');
						var revision = '--';
						if(data.revisiones!=null){
							if(data.revisiones.length>0){
								revision = lgPedi.states_revision[data.revisiones[data.revisiones.length-1].estado].label;
							}
						}
						$row.append('<td>'+revision+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+data.programa.nomb+'</td>');
						$row.append('<td>'+data.oficina.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgPedi.windowDetails({id: $(this).data('id'), tipo: $(this).data('data').tipo, nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgPedi.init_bien();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgPedp", {
							onShowMenu: function($row, menu) {
								$('#conMenLgPedp_rev',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgPedp_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgPedp_ver': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									}});
								},
								'conMenLgPedp_rev': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									},revisar: true});
								},
								'conMenLgPedp_fin': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo,nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									},finalizar: true});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	init_serv: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPedi_serv',
			titleBar: {
				title: 'Requerimientos Pendientes: Servicios'
			}
		});	
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Revision',{n:'Nro',f:'num'},{n:'Trabajador',f:'trabajador.appat'},{n:'Programa',f:'programa.nomb'},{n:'Oficina',f:'oficina.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/pedi/lista',
					params: {estado: 'P', "tipo":"S"},
					itemdescr: 'requerimiento(s) de servicios',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgPedi.states[data.estado].label+'</td>');
						var revision = '--';
						if(data.revisiones!=null){
							if(data.revisiones.length>0){
								revision = lgPedi.states_revision[data.revisiones[data.revisiones.length-1].estado].label;
							}
						}
						$row.append('<td>'+revision+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+data.programa.nomb+'</td>');
						$row.append('<td>'+data.oficina.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgPedi.windowDetails({id: $(this).data('id'), tipo: $(this).data('data').tipo,nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgPedi.init_serv();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgPedp", {
							onShowMenu: function($row, menu) {
								$('#conMenLgPedp_rev',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgPedp_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgPedp_ver': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									}});
								},
								'conMenLgPedp_rev': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									},revisar: true});
								},
								'conMenLgPedp_fin': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo,nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									},finalizar: true});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	init_loca: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPedi_loca',
			titleBar: {
				title: 'Requerimientos Pendientes: Locacion'
			}
		});	
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Revision',{n:'Nro',f:'num'},{n:'Trabajador',f:'trabajador.appat'},{n:'Programa',f:'programa.nomb'},{n:'Oficina',f:'oficina.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/pedi/lista',
					params: {estado: 'P', "tipo":"L"},
					itemdescr: 'requerimiento(s) de locacion',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgPedi.states[data.estado].label+'</td>');
						var revision = '--';
						if(data.revisiones!=null){
							if(data.revisiones.length>0){
								revision = lgPedi.states_revision[data.revisiones[data.revisiones.length-1].estado].label;
							}
						}
						var revision_logi = '--';
						if(data.revisiones!=null){
							if(data.revisiones.length>0){

								revision = lgPedi.states_revision[data.revisiones[data.revisiones.length-1].estado].label;
							}
						}
						$row.append('<td>'+revision+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+data.programa.nomb+'</td>');
						$row.append('<td>'+data.oficina.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgPedi.windowDetails({id: $(this).data('id'), tipo: $(this).data('data').tipo,nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgPedi.init_loca();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgPedp", {
							onShowMenu: function($row, menu) {
								$('#conMenLgPedp_rev',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgPedp_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgPedp_ver': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									}});
								},
								'conMenLgPedp_rev': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo, nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									},revisar: true});
								},
								'conMenLgPedp_fin': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo,nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.init_bien();
									},finalizar: true});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	init_todo: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPedi_todo',
			titleBar: {
				title: 'Requerimientos: Todos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nro',f:'num'},{n:'Trabajador',f:'trabajador.appat'},{n:'Programa',f:'programa.nomb'},{n:'Oficina',f:'oficina.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/pedi/lista',
					params: {},
					itemdescr: 'requerimiento(s)',
					toolbarHTML: '<select name="tipo"><option value="B">Bienes</option><option value="S">Servicios</option><option value="L">Personal de Locacion</option></select>',
					onContentLoaded: function($el){
						$el.find('[name=tipo]').change(function(){
							$grid.reinit({params:{tipo: $el.find('[name=tipo] :selected').val()}});
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						var programa = '--';
						if(data.programa!=null){
							programa = data.programa.nomb;
						}
						var oficina = '--';
						if(data.oficina!=null){
							oficina = data.oficina.nomb;
						}
						$row.append('<td>');
						$row.append('<td>'+lgPedi.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+programa+'</td>');
						$row.append('<td>'+oficina+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgPedi.windowDetails({id: $(this).data('id'),tipo:$(this).data('data').tipo,nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgPedi.initTodo();
							}});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenListEd_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									lgPedi.windowDetails({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo,nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPedi.initTodo();
									}});
								},
								'conMenListEd_edi': function(t) {
									lgPedi.windowEdit({id: K.tmp.data('id'), tipo: K.tmp.data('data').tipo,nomb: K.tmp.find('td:eq(2)').html()});
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
		if(p.goBack!=null) K.history.push({f: p.goBack});
		new K.Panel({
			contentURL: 'lg/pedi/edit?tipo='+p.tipo,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							tipo: p.tipo,
							programa: p.$w.find('[name=programa]').data('data'),
							oficina: p.$w.find('[name=oficina]').data('data'),
							expediente: p.$w.find('[name=expediente]').data('data'),
							//productos: []
						};
						switch(p.tipo){
							case "B":
								data.bien = {
									denominacion: p.$w.find('[name=denominacion]').val(),
									antecedentes: p.$w.find('[name=antecedentes]').val(),
									especificaciones: p.$w.find('[name=especificaciones]').val(),
									caracteristicas: p.$w.find('[name=antecedentes]').val(),
									normas: p.$w.find('[name=normas]').val(),
									acondicionamiento: p.$w.find('[name=acondicionamiento]').val(),
									lugar_entrega: p.$w.find('[name=lugar_entrega]').val(),
									plazo_entrega: p.$w.find('[name=plazo_entrega]').val(),
									forma_pago: p.$w.find('[name=forma_pago]').val(),
									garantia: p.$w.find('[name=garantia]').val(),
									visitas: p.$w.find('[name=visitas]').val(),
									capacitacion: p.$w.find('[name=capacitacion]').val()
								};
								break;
							case "S":
								data.servicio = {
									denominacion: p.$w.find('[name=denominacion]').val(),
									antecedentes: p.$w.find('[name=antecedentes]').val(),
									terminos: p.$w.find('[name=terminos]').val(),
									actividades: p.$w.find('[name=actividades]').val(),
									procedimiento: p.$w.find('[name=procedimiento]').val(),
									recursos_proveedor: p.$w.find('[name=recursos_proveedor]').val(),
									recursos_entidad: p.$w.find('[name=recursos_entidad]').val(),
									visitas: p.$w.find('[name=visitas]').val(),
									requisitos: p.$w.find('[name=requisitos]').val(),
									entregables: p.$w.find('[name=entregables]').val(),
									lugar_ejecucion: p.$w.find('[name=lugar_ejecucion]').val(),
									plazo_ejecucion: p.$w.find('[name=plazo_ejecucion]').val(),
									forma_pago: p.$w.find('[name=forma_pago]').val()
								};
								break;
							case "L":
								data.locacion = {
									justificacion_contrato: p.$w.find('[name=justificacion_contrato]').val(),
									descripcion: p.$w.find('[name=descripcion]').val(),
									grado_profesional: p.$w.find('[name=grado_profesional]').val(),
									grado_tecnico: p.$w.find('[name=grado_tecnico]').val(),
									profesion: p.$w.find('[name=profesion]').val(),
									capacitacion: p.$w.find('[name=capacitacion]').val(),
									experiencia: p.$w.find('[name=experiencia]').val(),
									duracion:{
										fecini:p.$w.find('[name=fecini]').val(),
										fecfin:p.$w.find('[name=fecfin]').val()
									},
									valor_referencial:{
										monto_total:p.$w.find('[name=monto_total]').val(),
										monto_mensual:p.$w.find('[name=monto_mensual]').val()
									}
								};
								break;
						}
						if(data.expediente==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar el expediente relacionado a la operacion!',
								type: 'error'
							});
						}
						if(data.programa==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'No se ha encontrado un programa relacionado a la entidad!',
								type: 'error'
							});
						}
						if(data.oficina==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'No se ha encontrado una oficina relacionada a la entidad!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/pedi/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Pedido Interno agregado!"});
							K.goBack();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=programa]').val(K.session.enti.roles.trabajador.programa.nomb).data('data', mgProg.dbRel(K.session.enti.roles.trabajador.programa));
				p.$w.find('[name=oficina]').val(K.session.enti.roles.trabajador.oficina.nomb).data('data', mgOfic.dbRel(K.session.enti.roles.trabajador.oficina));
				p.$w.find('[name=trabajador]').val(ciHelper.enti.formatName(K.session.enti)).data('data', mgEnti.dbRel(K.session.enti));;
				p.$w.find('[name=btnExp]').click(function(){
					tdExpd.windowSelect({callback:function(data){
						p.$w.find('[name=expediente]').val(data.num).data('data',tdExpd.dbRel(data));
					}});
				});
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		if(p.goBack!=null) K.history.push({f: p.goBack});
		new K.Panel({
			contentURL: 'lg/pedi/edit?tipo='+p.tipo,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							expediente: p.$w.find('[name=expediente]').data('data'),
							//productos: []
						};
						switch(p.tipo){
							case "B":
								data.bien = {
									denominacion: p.$w.find('[name=denominacion]').val(),
									antecedentes: p.$w.find('[name=antecedentes]').val(),
									especificaciones: p.$w.find('[name=especificaciones]').val(),
									caracteristicas: p.$w.find('[name=antecedentes]').val(),
									normas: p.$w.find('[name=normas]').val(),
									acondicionamiento: p.$w.find('[name=acondicionamiento]').val(),
									lugar_entrega: p.$w.find('[name=lugar_entrega]').val(),
									plazo_entrega: p.$w.find('[name=plazo_entrega]').val(),
									forma_pago: p.$w.find('[name=forma_pago]').val(),
									garantia: p.$w.find('[name=garantia]').val(),
									visitas: p.$w.find('[name=visitas]').val(),
									capacitacion: p.$w.find('[name=capacitacion]').val()
								};
								break;
							case "S":
								data.servicio = {
									denominacion: p.$w.find('[name=denominacion]').val(),
									antecedentes: p.$w.find('[name=antecedentes]').val(),
									terminos: p.$w.find('[name=terminos]').val(),
									actividades: p.$w.find('[name=actividades]').val(),
									procedimiento: p.$w.find('[name=procedimiento]').val(),
									recursos_proveedor: p.$w.find('[name=recursos_proveedor]').val(),
									recursos_entidad: p.$w.find('[name=recursos_entidad]').val(),
									visitas: p.$w.find('[name=visitas]').val(),
									requisitos: p.$w.find('[name=requisitos]').val(),
									entregables: p.$w.find('[name=entregables]').val(),
									lugar_ejecucion: p.$w.find('[name=lugar_ejecucion]').val(),
									plazo_ejecucion: p.$w.find('[name=plazo_ejecucion]').val(),
									forma_pago: p.$w.find('[name=forma_pago]').val()
								};
								break;
							case "L":
								data.locacion = {
									justificacion_contrato: p.$w.find('[name=justificacion_contrato]').val(),
									descripcion: p.$w.find('[name=descripcion]').val(),
									grado_profesional: p.$w.find('[name=grado_profesional]').val(),
									grado_tecnico: p.$w.find('[name=grado_tecnico]').val(),
									profesion: p.$w.find('[name=profesion]').val(),
									capacitacion: p.$w.find('[name=capacitacion]').val(),
									experiencia: p.$w.find('[name=experiencia]').val(),
									duracion:{
										fecini:p.$w.find('[name=fecini]').val(),
										fecfin:p.$w.find('[name=fecfin]').val()
									},
									valor_referencial:{
										monto_total:p.$w.find('[name=monto_total]').val(),
										monto_mensual:p.$w.find('[name=monto_mensual]').val()
									}
								};
								break;
						}
						if(data.expediente==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar el expediente relacionado a la operacion!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/pedi/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Pedido Interno actualizado!"});
							K.goBack();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnExp]').click(function(){
					tdExpd.windowSelect({callback:function(data){
						p.$w.find('[name=expediente]').val(data.num).data('data',tdExpd.dbRel(data));
					}});
				});
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
				$.post('lg/pedi/get',{_id: p.id},function(data){
					p.tipo = data.tipo;
					p.$w.find('[name=programa]').val(data.programa.nomb).data('data', mgProg.dbRel(data.programa));
					p.$w.find('[name=oficina]').val(data.oficina.nomb).data('data', mgOfic.dbRel(data.oficina));
					p.$w.find('[name=trabajador]').val(ciHelper.enti.formatName(data.trabajador)).data('data', mgEnti.dbRel(data.trabajador));;
					p.$w.find('[name=expediente]').val(data.expediente.num).data('data', tdExpd.dbRel(data.expediente));
					switch(data.tipo){
						case "B":
							p.$w.find('[name=denominacion]').val(data.bien.denominacion);
							p.$w.find('[name=antecedentes]').val(data.bien.antecedentes);
							p.$w.find('[name=especificaciones]').val(data.bien.especificaciones);
							p.$w.find('[name=caracteristicas]').val(data.bien.caracteristicas);
							p.$w.find('[name=normas]').val(data.bien.normas);
							p.$w.find('[name=acondicionamiento]').val(data.bien.acondicionamiento);
							p.$w.find('[name=lugar_entrega]').val(data.bien.lugar_entrega);
							p.$w.find('[name=plazo_entrega]').val(data.bien.plazo_entrega);
							p.$w.find('[name=forma_pago]').val(data.bien.forma_pago);
							p.$w.find('[name=garantia]').val(data.bien.garantia);
							p.$w.find('[name=visitas]').val(data.bien.visitas);
							p.$w.find('[name=capacitacion]').val(data.bien.capacitacion);
							break;
						case "S":
							p.$w.find('[name=denominacion]').val(data.servicio.denominacion);
							p.$w.find('[name=antecedentes]').val(data.servicio.antecedentes);
							p.$w.find('[name=terminos]').val(data.servicio.terminos);
							p.$w.find('[name=actividades]').val(data.servicio.actividades);
							p.$w.find('[name=procedimiento]').val(data.servicio.procedimiento);
							p.$w.find('[name=recursos_proveedor]').val(data.servicio.recursos_proveedor);
							p.$w.find('[name=recursos_entidad]').val(data.servicio.recursos_entidad);
							p.$w.find('[name=visitas]').val(data.servicio.visitas);
							p.$w.find('[name=requisitos]').val(data.servicio.requisitos);
							p.$w.find('[name=entregables]').val(data.servicio.entregables);
							p.$w.find('[name=lugar_ejecucion]').val(data.servicio.lugar_ejecucion);
							p.$w.find('[name=plazo_ejecucion]').val(data.servicio.plazo_ejecucion);
							p.$w.find('[name=forma_pago]').val(data.servicio.forma_pago);
							break;
						case "L":
							p.$w.find('[name=justificacion_contrato]').val(data.locacion.justificacion_contrato);
							p.$w.find('[name=descripcion]').val(data.locacion.descripcion);
							p.$w.find('[name=grado_profesional]').val(data.locacion.grado_profesional);
							p.$w.find('[name=grado_tecnico]').val(data.locacion.grado_tecnico);
							p.$w.find('[name=profesion]').val(data.locacion.profesion);
							p.$w.find('[name=capacitacion]').val(data.locacion.capacitacion);
							p.$w.find('[name=experiencia]').val(data.locacion.experiencia);
							p.$w.find('[name=fecini]').val(data.locacion.duracion.fecini);
							p.$w.find('[name=fecfin]').val(data.locacion.duracion.fecfin);
							p.$w.find('[name=monto_total]').val(data.locacion.valor_referencial.monto_total);
							p.$w.find('[name=monto_mensual]').val(data.locacion.valor_referencial.monto_mensual);
							break;
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		p.buttons = {
			"Imprimir": {
				icon: 'fa-refresh',
				type: 'info',
				f: function(){
					params = new Object;
					params.id = p.id;
					var url = 'lg/repo/pedi?'+$.param(params);
					K.windowPrint({
						id:'windowLgRepo',
						title: "Reporte / Informe",
						url: url
					});
				}
			},
			"Regresar": {
				icon: 'fa-refresh',
				type: 'warning',
				f: function(){
					K.goBack();
				}
			}
		};
		if(p.revisar!=null){
			p.buttons = {
				'Aceptar': {
					icon: 'fa-check',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							revision: {
								estado: 'A',
								estado_doc: p.estado,
								observ: ''
							}
						};
						K.sendingInfo();
						$('#mainPanel #div_buttons button').attr('disabled','disabled');
						$.post('lg/pedi/save',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: 'El pedido interno fue actualizado con &eacute;xito!'});
							K.goBack();
						});
					}
				},
				'Rechazar': {
					icon: 'fa-close',
					type: 'warning',
					f: function(){
						new K.Modal({
							id: 'windowObserv',
							title: 'Por que rechaza este documento',
							contentURL: 'ci/index/view_observ',
							width: 550,
							height: 200,
							buttons: {
								'Guardar': {
									icon: 'fa-check',
									type: 'success',
									f: function(){
										K.clearNoti();
										var data = {
											_id: p.id,
											revision: {
												estado: 'R',
												estado_doc: p.estado,
												observ: $('#windowObserv [name=observ]').val()
											}
										};
										K.sendingInfo();
										$('#mainPanel #div_buttons button').attr('disabled','disabled');
										$.post('lg/pedi/save',data,function(){
											K.clearNoti();
											K.closeWindow('windowObserv');
											K.msg({title: ciHelper.titles.regiAct,text: 'El pedido interno fue actualizado con &eacute;xito!'});
											K.goBack();
										});
									}
								},
								'Cancelar': {
									icon: 'fa-ban',
									type: 'danger',
									f: function(){
										K.closeWindow('windowObserv');
									}
								}
							}
						});
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			};
		}
		if(p.finalizar!=null){
			p.buttons = {
				'Aprobar': {
					icon: 'fa-check',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							estado:'A',
							revision: {
								estado: 'A',
								estado_doc: p.estado,
								observ: ''
							}
						};
						K.sendingInfo();
						$('#mainPanel #div_buttons button').attr('disabled','disabled');
						$.post('lg/pedi/save',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: 'El pedido interno fue actualizado con &eacute;xito!'});
							K.goBack();
						});
					}
				},
				'Rechazar': {
					icon: 'fa-close',
					type: 'warning',
					f: function(){
						new K.Modal({
							id: 'windowObserv',
							title: 'Por que rechaza este documento',
							contentURL: 'ci/index/view_observ',
							width: 550,
							height: 200,
							buttons: {
								'Guardar': {
									icon: 'fa-check',
									type: 'success',
									f: function(){
										K.clearNoti();
										var data = {
											_id: p.id,
											estado:'X',
											revision: {
												estado: 'R',
												estado_doc: p.estado,
												observ: $('#windowObserv [name=observ]').val()
											}
										};
										K.sendingInfo();
										$('#mainPanel #div_buttons button').attr('disabled','disabled');
										$.post('lg/pedi/save',data,function(){
											K.clearNoti();
											K.closeWindow('windowObserv');
											K.msg({title: ciHelper.titles.regiAct,text: 'El pedido interno fue actualizado con &eacute;xito!'});
											K.goBack();
										});
									}
								},
								'Cancelar': {
									icon: 'fa-ban',
									type: 'danger',
									f: function(){
										K.closeWindow('windowObserv');
									}
								}
							}
						});
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			};
		}
		new K.Panel({
			contentURL: 'lg/pedi/details?tipo='+p.tipo,
			store: false,
			buttons: p.buttons,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['&nbsp;','Item Solicitado','Unidad','Cantidad'],
					onlyHtml: true
				});
				$.post('lg/pedi/get',{_id: p.id},function(data){
					p.tipo = data.tipo;
					p.estado = data.estado;
					p.$w.find('[name=programa]').val(data.programa.nomb).data('data', mgProg.dbRel(data.programa));
					p.$w.find('[name=oficina]').val(data.oficina.nomb).data('data', mgOfic.dbRel(data.oficina));
					p.$w.find('[name=trabajador]').val(ciHelper.enti.formatName(data.trabajador)).data('data', mgEnti.dbRel(data.trabajador));;
					p.$w.find('[name=expediente]').val(data.expediente.num).data('data', tdExpd.dbRel(data.expediente));
					switch(data.tipo){
						case "B":
							p.$w.find('[name=denominacion]').val(data.bien.denominacion);
							p.$w.find('[name=antecedentes]').val(data.bien.antecedentes);
							p.$w.find('[name=especificaciones]').val(data.bien.especificaciones);
							p.$w.find('[name=caracteristicas]').val(data.bien.caracteristicas);
							p.$w.find('[name=normas]').val(data.bien.normas);
							p.$w.find('[name=acondicionamiento]').val(data.bien.acondicionamiento);
							p.$w.find('[name=lugar_entrega]').val(data.bien.lugar_entrega);
							p.$w.find('[name=plazo_entrega]').val(data.bien.plazo_entrega);
							p.$w.find('[name=forma_pago]').val(data.bien.forma_pago);
							p.$w.find('[name=garantia]').val(data.bien.garantia);
							p.$w.find('[name=visitas]').val(data.bien.visitas);
							p.$w.find('[name=capacitacion]').val(data.bien.capacitacion);
							break;
						case "S":
							p.$w.find('[name=denominacion]').val(data.servicio.denominacion);
							p.$w.find('[name=antecedentes]').val(data.servicio.antecedentes);
							p.$w.find('[name=terminos]').val(data.servicio.terminos);
							p.$w.find('[name=actividades]').val(data.servicio.actividades);
							p.$w.find('[name=procedimiento]').val(data.servicio.procedimiento);
							p.$w.find('[name=recursos_proveedor]').val(data.servicio.recursos_proveedor);
							p.$w.find('[name=recursos_entidad]').val(data.servicio.recursos_entidad);
							p.$w.find('[name=visitas]').val(data.servicio.visitas);
							p.$w.find('[name=requisitos]').val(data.servicio.requisitos);
							p.$w.find('[name=entregables]').val(data.servicio.entregables);
							p.$w.find('[name=lugar_ejecucion]').val(data.servicio.lugar_ejecucion);
							p.$w.find('[name=plazo_ejecucion]').val(data.servicio.plazo_ejecucion);
							p.$w.find('[name=forma_pago]').val(data.servicio.forma_pago);
							break;
						case "L":
							p.$w.find('[name=justificacion_contrato]').val(data.locacion.justificacion_contrato);
							p.$w.find('[name=descripcion]').val(data.locacion.descripcion);
							p.$w.find('[name=grado_profesional]').val(data.locacion.grado_profesional);
							p.$w.find('[name=grado_tecnico]').val(data.locacion.grado_tecnico);
							p.$w.find('[name=profesion]').val(data.locacion.profesion);
							p.$w.find('[name=capacitacion]').val(data.locacion.capacitacion);
							p.$w.find('[name=experiencia]').val(data.locacion.experiencia);
							p.$w.find('[name=fecini]').val(data.locacion.duracion.fecini);
							p.$w.find('[name=fecfin]').val(data.locacion.duracion.fecfin);
							p.$w.find('[name=monto_total]').val(data.locacion.valor_referencial.monto_total);
							p.$w.find('[name=monto_mensual]').val(data.locacion.valor_referencial.monto_mensual);
							break;
					}
					p.$w.find('input, textarea').attr('disabled','disabled');
					if(data.revisiones!=null){
						for(var i=0; i<data.revisiones.length; i++){
							var programa = "--";
							if(data.revisiones[i].programa!=null){
								programa = data.revisiones[i].programa.nomb;
							}
							$item = $('<div class="timeline-item">'+
								'<div class="row">'+
									'<div class="col-xs-5 date">'+
										'<i class="fa"></i>'+ciHelper.date.format.bd_ymdhi(data.revisiones[i].fec)+
									'</div>'+
									'<div class="col-xs-7 content no-top-border">'+
										'<p class="m-b-xs"><strong>'+programa+'</strong></p>'+
										'<p>'+mgEnti.formatName(data.revisiones[i].trabajador)+'</p>'+
										'<p>Estado del Documento: '+lgPedi.states[data.revisiones[i].estado_doc].label+'</p>'+
									'</div>'+
								'</div>'+
							'</div>');
							if(data.revisiones[i].observ!=null)
								$item.find('.content').append('<p class="text-warning">'+data.revisiones[i].observ+'</p>');
							if(data.revisiones[i].estado=='A'){
								$item.find('.date').append('<br />'+lgPedi.states[data.revisiones[i].estado].label);
								$item.find('.fa').addClass('fa-check');
							}else{
								$item.find('.date').append('<br />'+lgPedi.states[data.revisiones[i].estado].label);
								$item.find('.fa').addClass('fa-close');
							}
							p.$w.find('.inspinia-timeline').append($item);
						}
					}
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
			title: 'Seleccionar Pedido Interno',
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
				if(p.estado==null){
					p.estado = "";
				}
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','',{n:'Nro',f:'num'},{n:'Programa',f:'programa.nomb'},{n:'Oficina',f:'oficina.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/pedi/lista',
					params: {"estado":p.estado, tipo:"B"},
					itemdescr: 'requerimiento(s)',
					toolbarHTML: '<select name="tipo"><option value="B">Bienes</option><option value="S">Servicios</option></select>',
					onContentLoaded: function($el){
						$el.find('[name=tipo]').change(function(){
							p.$grid.reinit({params:{tipo: $el.find('[name=tipo] :selected').val(), estado:p.estado}});
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgPedi.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.programa.nomb+'</td>');
						$row.append('<td>'+data.oficina.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
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
	['mg/enti','ct/pcon','lg/unid','td/expd','mg/prog','mg/ofic'],
	function(mgEnti,ctPcon,lgUnid, tdExpd, mgProg, mgOfic){
		return lgPedi;
	}
);