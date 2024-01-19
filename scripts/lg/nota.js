lgNota = {
	states: {
		"P": {
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-default">Pendiente</span>'
		},
		"A": {
			descr: "Aprobado",
			color: "#003265",
			label: '<span class="label label-success">Aprobado</span>'
		},
		"X": {
			descr: "Anulado",
			color: "#FF1F0F",
			label: '<span class="label label-danger">Anulado</span>'
		},
		"R": {
			descr: "Recibido",
			color: "blue",
			label: '<span class="label label-primary">Recibido</span>'
		}
	},
	statesRev: {
		"A": {
			descr: "Aprobado",
			color: "#003265",
			label: '<span class="label label-success">Aprobado</span>'
		},
		"R": {
			descr: "Rechazado",
			color: "#FF1F0F",
			label: '<span class="label label-danger">Rechazado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgNotn',
			titleBar: {
				title: 'Nuevas Notas de entrada'
			}
		});

		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Procedencia','Destino','Motivo',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/nota/lista',
					itemdescr: 'nota(s) de entrada',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nueva Nota de Entrada</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgNota.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgNota.states[data.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.procedencia)+'</td>');
						$row.append('<td>'+data.destino_a.nomb+'</td>');
						$row.append('<td>'+data.motivo+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgNota.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgNota.init();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgNota", {
							onShowMenu: function($row, menu) {
								$('#conMenLgNota_fin,#conMenLgNota_rev,#conMenLgNota_con,#conMenLgNota_edi',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgNota_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgNota_ver': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.init();
									}});
								},
								'conMenLgNota_edi': function(t) {
									lgNota.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	initPend: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgNotp',
			titleBar: {
				title: 'Notas de entrada Pendientes'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Procedencia','Destino','Motivo',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/nota/lista',
					params: {estado: 'P'},
					itemdescr: 'nota(s) de entrada',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgNota.states[data.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td>'+data.destino_a.nomb+'</td>');
						$row.append('<td>'+data.motivo+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgNota.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgNota.initPend();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgNota", {
							onShowMenu: function($row, menu) {
								$('#conMenLgNota_con,#conMenLgNota_edi',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgNota_ver': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.initPend();
									}});
								},
								'conMenLgNota_rev': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.initPend();
									},revisar: true});
								},
								'conMenLgNota_fin': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.initPend();
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
	initApro: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgNota',
			titleBar: {
				title: 'Notas de entrada Aprobadas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Procedencia','Destino','Motivo',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/nota/lista',
					params: {estado: 'A'},
					itemdescr: 'nota(s) de entrada',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgNota.states[data.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td>'+data.destino_a.nomb+'</td>');
						$row.append('<td>'+data.motivo+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgNota.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgNota.initApro();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgNota", {
							onShowMenu: function($row, menu) {
								$('#conMenLgNota_fin,#conMenLgNota_edi,#conMenLgNota_rev',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgNota_ver': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.initApro();
									}});
								},
								'conMenLgNota_con': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.initApro();
									},confirmar: true});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	initTodo: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgNott',
			titleBar: {
				title: 'Todas las Notas de entrada'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Procedencia','Destino','Motivo',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/nota/lista',
					params: {},
					itemdescr: 'nota(s) de entrada',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgNota.states[data.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td>'+data.destino_a.nomb+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgNota.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenLgNota", {
							onShowMenu: function($row, menu) {
								$('#conMenLgNota_fin,#conMenLgNota_rev,#conMenLgNota_con,#conMenLgNota_edi',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgNota_ver': function(t) {
									lgNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgNota.initTodo();
									}});
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
			calcTot: function(){
				var total = 0;
				for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
					if(parseFloat(p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=subtotal]').html()))
						total+=parseFloat(p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=subtotal]').html());
				}
				p.$w.find('[name=total]').html(ciHelper.formatMon(total));
			},
			calcOrga: function(){
				
			}
		});
		new K.Panel({
			title: 'Nueva Nota de Entrada',
			contentURL: 'lg/nota/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.cod,
							fec: p.$w.find('[name=fec]').val(),
							ref: p.$w.find('[name=ref]').val(),
							segun: p.$w.find('[name=segun]').val(),
							motivo: p.$w.find('[name=motivo] :selected').val(),
							productos: [],
							precio_total: 0
						};
						var tmp = p.$w.find('[name=mini_enti]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnSel]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una procedencia!',type: 'error'});
						}else{
							data.procedencia = mgEnti.dbRel(tmp);
							data.procedencia.domicilios = tmp.domicilios;
						}
						if(data.ref==''){
							p.$w.find('[name=ref]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una referencia!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridProd] tbody .item').eq(i),
							dat = $row.data('data');
							if(dat==null){
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar al menos un producto!',type: 'error'});
							}
							var prod = {
								item: i+1,
								producto: {
									_id: dat._id.$id,
									nomb: dat.nomb,
									cod: dat.cod,
									unidad: {
										_id: dat.unidad._id.$id,
										nomb: dat.unidad.nomb
									}
								},
								precio: parseFloat($row.find('[name=precio]').val()),
								cant: parseFloat($row.find('[name=cant]').val()),
								subtotal: parseFloat($row.find('[name=subtotal]').html())
							};
							if(dat.cuenta!=null){
								prod.producto.cuenta = {
									_id: dat.cuenta._id.$id,
									cod: dat.cuenta.cod,
									descr: dat.cuenta.descr
								};
							}
							if($row.find('[name=cant]').val()==''||prod.cant<=0){
								$row.find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad valida!',type: 'error'});
							}
							if($row.find('[name=precio]').val()==''||prod.precio<=0){
								$row.find('[name=precio]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un precio valido!',type: 'error'});
							}
							data.precio_total+=prod.subtotal;
							data.productos.push(prod);
						}
						var tmp = p.$w.find('[name=almacen] option:selected').data('data');
						data.destino_a = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							local: {
								_id: tmp.local._id.$id,
								descr: tmp.local.descr,
								direccion: tmp.local.direccion
							}
						};
						var tmp = p.$w.find('[name=programa] option:selected').data('data');
						data.programa = mgProg.dbRel(tmp);
						if(p.id!=null){
							data._id = p.id;
							delete data.cod;
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/nota/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Nota de entrada agregada!"});
							lgNota.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgNota.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=mini_enti] [name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						}
					});
				});
				p.$w.find('[name=mini_enti] [name=btnAct]').click(function(){
					if(p.$w.find('[name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						},id: p.$w.find('[name=mini_enti]').data('data')._id.$id});
					}
				});
				var $cbo = p.$w.find('[name=almacen]');
				for(var i=K.session.almacenes.length-1; i>=0; i--){
					$cbo.append('<option value="'+K.session.almacenes[i]._id.$id+'">'+K.session.almacenes[i].nomb+'</option>');
					$cbo.find('option:last').data('data',K.session.almacenes[i]);
				}
				var $cbo = p.$w.find('[name=programa]');
				for(var i=K.session.programas.length-1; i>=0; i--){
					$cbo.append('<option value="'+K.session.programas[i]._id.$id+'">'+K.session.programas[i].nomb+'</option>');
					$cbo.find('option:last').data('data',K.session.programas[i]);
				}
				if(K.session.enti.roles.trabajador.programa!=null){
					$cbo.val(K.session.enti.roles.trabajador.programa._id.$id);
				}
				p.$w.find('[name^=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Cuenta contable','Producto','Unidad','Cantidad','Precio','SubTotal',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=gridProd] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
										$row.append('<td><kbd>'+data.cuenta.cod+'</kbd><br />'+data.cuenta.descr+'</td>');
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" value="0"></td>');
										$row.append('<td><input type="text" name="precio" class="form-control" value="0"></td>');
										$row.append('<td><span name="subtotal"></span></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.find('[name=cant], [name=precio]').keyup(function(){
											var $this = $(this).closest('tr');
											var cant = $this.find('[name=cant]').val();
											var precio = $this.find('[name=precio]').val();
											if(!parseFloat(cant)){
												cant = 0; $this.find('[name=cant]').val("0");
											}
											if(!parseFloat(precio)){
												precio = 0; $this.find('[name=precio]').val("0");
											}
											var ptotal = parseFloat(cant)*parseFloat(precio);
											$this.find('[name=subtotal]').html(K.round(ptotal,2));
											p.calcTot();
										});
										$row.data('data',data);
										p.$w.find('[name=gridProd] tbody').append($row);
										$row.find('[name=precio]').keyup();
										p.calcTot();
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						}).click();
						p.$w.find('[name=gridProd] table:last').append('<tfoot>');
						var $row = $('<tr class="item">');
						$row.append('<td colspan="5">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						$row.append('<td>');
						p.$w.find('[name=gridProd] tfoot').append($row);
					}
				});
				$.post('lg/nota/edit_data',function(data){
					p.cod = data.cod;
					if(p.id!=null){
						$.post('lg/nota/get',{_id: p.id},function(data){
							p.$w.find('[name=fec]').val(ciHelper.date.format.bd_ymd(data.fec));
							p.$w.find('[name=segun]').val(data.segun);
							p.$w.find('[name=motivo]').val(data.motivo);
							p.$w.find('[name=almacen]').val(data.destino_a.nomb).data('data',data.destino_a);
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
							for(var i=0; i<data.productos.length; i++){
								var data_prod = data.productos[i].producto,
								$row = $('<tr class="item" name="'+data_prod._id.$id+'">');
								$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
								$row.append('<td><kbd>'+data_prod.cuenta.cod+'</kbd><br />'+data_prod.cuenta.descr+'</td>');
								$row.append('<td><kbd>'+data_prod.cod+'</kbd><br />'+data_prod.nomb+'</td>');
								$row.append('<td>'+data_prod.unidad.nomb+'</td>');
								$row.append('<td><span name="cant">'+data.productos[i].cant+'</span></td>');
								$row.append('<td><span name="precio">'+(parseFloat(data.productos[i].cred_solic)/parseFloat(data.productos[i].cant))+'</span></td>');
								$row.append('<td></td>');
								$row.append('<td><span name="subtotal"></span></td>');
								$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('button:last').click(function(){
									$(this).closest('.item').remove();
									for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
										p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
									}
								});
								$row.data('data',data_prod);
								$row.find('[name=cant]').html(data.productos[i].cant);
								$row.find('[name=precio]').html('S/.'+parseFloat(data.productos[i].precio));
								$row.find('[name=subtotal]').html('S/.'+parseFloat(data.productos[i].subtotal));
								$row.data('total',parseFloat(data.productos[i].subtotal)).data('cant',data.productos[i].cant)
									.data('precio',parseFloat(data.productos[i].precio));
								
								p.$w.find('[name=gridProd] tbody').append($row);
							}
							p.calcTot();
							p.calcOrga();
							K.unblock();
						},'json');
					}else
						K.unblock();
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		$.extend(p,{
			calcTot: function(){
				var total = 0;
				for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
					if(p.$w.find('[name=gridProd] tbody .item').eq(i).data('total')!=null)
						total = parseFloat(total) + parseFloat(p.$w.find('[name=gridProd] tbody .item').eq(i).data('total'));
				}
				p.$w.find('[name=total]').html(ciHelper.formatMon(total));
			},
			calcOrga: function(){
				
			}
		});
		p.buttons = {
			"Imprimir": {
				icon: 'fa-refresh',
				type: 'info',
				f: function(){
					params = new Object;
					params.id = p.id;
					var url = 'lg/repo/nota?'+$.param(params);
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
						$.post('lg/nota/save',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: 'La orden de compra fue actualizada con &eacute;xito!'});
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
										$.post('lg/nota/save',data,function(){
											K.clearNoti();
											K.closeWindow('windowObserv');
											K.msg({title: ciHelper.titles.regiAct,text: 'La orden de compra fue actualizada con &eacute;xito!'});
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
							estado: 'A',
							revision: {
								estado: 'A',
								estado_doc: p.estado,
								observ: ''
							}
						};
						K.sendingInfo();
						$('#mainPanel #div_buttons button').attr('disabled','disabled');
						$.post('lg/nota/finalizar',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: 'La orden de compra fue actualizada con &eacute;xito!'});
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
										$.post('lg/nota/finalizar',data,function(){
											K.clearNoti();
											K.closeWindow('windowObserv');
											K.msg({title: ciHelper.titles.regiAct,text: 'La orden de compra fue actualizado con &eacute;xito!'});
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
		if(p.confirmar!=null){
			p.buttons = {
				'Confirmar': {
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
						$.post('lg/nota/entrega',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: 'La orden de compra fue actualizada con &eacute;xito!'});
							K.goBack();
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
			contentURL: 'lg/nota/details',
			store: false,
			buttons: p.buttons,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=mini_enti] [name=btnSel],[name=mini_enti] [name=btnAct]').remove();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Cuenta','Producto','Unidad','Cantidad','Precio','SubTotal'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						p.$w.find('[name=gridProd] table:last').append('<tfoot>');
						var $row = $('<tr class="item">');
						$row.append('<td colspan="5">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						p.$w.find('[name=gridProd] tfoot').append($row);
					}
				});

				$.post('lg/nota/get',{_id: p.id},function(data){
					p.$w.find('[name=segun]').html(data.segun);
					p.$w.find('[name=motivo]').val(data.motivo);
					p.$w.find('[name=almacen]').html(data.destino_a.nomb);
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.procedencia);
					for(var i=0; i<data.productos.length; i++){
						var data_prod = data.productos[i].producto,
						$row = $('<tr class="item" name="'+data_prod._id.$id+'">');
						$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
						$row.append('<td><kbd>'+data_prod.cuenta.cod+'</kbd><br />'+data_prod.cuenta.descr+'</td>');
						$row.append('<td><kbd>'+data_prod.cod+'</kbd><br />'+data_prod.nomb+'</td>');
						$row.append('<td>'+data_prod.unidad.nomb+'</td>');
						$row.append('<td><span name="cant">'+data.productos[i].cant+'</span></td>');
						$row.append('<td><span name="precio">'+(parseFloat(data.productos[i].cred_solic)/parseFloat(data.productos[i].cant))+'</span></td>');
						$row.append('<td><span name="subtotal"></span></td>');
						$row.data('data',data_prod);
						$row.find('[name=cant]').html(data.productos[i].cant);
						$row.find('[name=precio]').html('S/.'+parseFloat(data.productos[i].precio));
						$row.find('[name=subtotal]').html('S/.'+parseFloat(data.productos[i].subtotal));
						$row.data('total',parseFloat(data.productos[i].subtotal)).data('cant',data.productos[i].cant)
							.data('precio',parseFloat(data.productos[i].precio));

						p.$w.find('[name=gridProd] tbody').append($row);
					}
					p.calcTot();
					p.calcOrga();
					K.unblock();
					if(data.revisiones!=null){
						for(var i=0; i<data.revisiones.length; i++){
							if(data.revisiones[i].estado_doc==null) data.revisiones[i].estado_doc = 'P';
							$item = $('<div class="timeline-item">'+
								'<div class="row">'+
									'<div class="col-xs-5 date">'+
										'<i class="fa"></i>'+ciHelper.date.format.bd_ymdhi(data.revisiones[i].fec)+
									'</div>'+
									'<div class="col-xs-7 content no-top-border">'+
										'<p class="m-b-xs"><strong>'+data.revisiones[i].trabajador.cargo.organizacion.nomb+'</strong></p>'+
										'<p>'+mgEnti.formatName(data.revisiones[i].trabajador)+'</p>'+
										'<p>Estado del Documento: '+lgNota.states[data.revisiones[i].estado_doc].label+'</p>'+
									'</div>'+
								'</div>'+
							'</div>');
							if(data.revisiones[i].observ!=null)
								$item.find('.content').append('<p class="text-warning">'+data.revisiones[i].observ+'</p>');
							if(data.revisiones[i].estado=='A'){
								$item.find('.date').append('<br />'+lgNota.statesRev[data.revisiones[i].estado].label);
								$item.find('.fa').addClass('fa-check');
							}else{
								$item.find('.date').append('<br />'+lgNota.statesRev[data.revisiones[i].estado].label);
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
			title: 'Seleccionar Orden de Compra',
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
					cols: ['','Nombre'],
					data: 'lg/nota/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
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
	['mg/enti','ct/pcon','lg/alma','lg/prod','lg/cert','mg/prog'],
	function(mgEnti,ctPcon,lgAlma,lgProd,lgCert,mgProg){
		return lgNota;
	}
);