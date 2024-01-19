lgPeco = {
	states: {
		P: {
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-default">Pendiente</span>'
		},
		A: {
			descr: "Aprobado",
			color: "#003265",
			label: '<span class="label label-success">Aprobado</span>'
		},
		X: {
			descr: "Anulado",
			color: "#FF1F0F",
			label: '<span class="label label-danger">Anulado</span>'
		},
		E: {
			descr: "Entregado",
			color: "blue",
			label: '<span class="label label-warning">Entregado</span>'
		},
		R: {
			descr: "Recibido",
			color: "#2E6EAD",
			label: '<span class="label label-info">Recibido</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPeco',
			titleBar: {
				title: 'Pedido Comprobante de Salida - PECOSAs'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'C&oacute;digo',f:'cod'},'Solicitante','Solicita Entregar a',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/peco/lista',
					params: {autor: 1},
					itemdescr: 'PECOSA(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Crear PECOSA</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgPeco.windowNew();
						});
					},
					/*onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },*/
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgPeco.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.solicitante)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.responsable)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgPeco.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgPeco.init();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgPeco", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='P'){
									$('#conMenLgPeco_con',menu).remove();
								}else{
									$('#conMenLgPeco_anu',menu).remove();
								}
								$('#conMenLgPeco_rev,#conMenLgPeco_fin,#conMenLgPeco_dar,#conMenLgPeco_def',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgPeco_ver': function(t) {
									lgPeco.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgPeco_edi': function(t) {
									lgPeco.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgPeco_con': function(t) {
									lgPeco.windowConfirmar({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgPeco_anu': function(t) {
									ciHelper.confirm('&#191;Desea <b>ANULAR</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/peco/save',{_id: K.tmp.data('id'),estado: 'X'},function(){
											K.clearNoti();
											K.msg({title: 'PECOSA anulada',text: 'La PECOSA se anulo con &eacute;xito!'});
											lgPeco.init();
										});
									},function(){
										$.noop();
									},'Anjulaci&oacute;n de Tipo de Local');
								},
								'conMenLgPeco_imp': function(t) {
									var url = 'lg/repo/peco?id='+K.tmp.data('id');
									K.windowPrint({
										id:'windowLgRepo',
										title: "Reporte / Informe",
										url: url
									});
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
	initAlma: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgPecl',
			titleBar: {
				title: 'PECOSAs - Almac&eacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'C&oacute;digo',f:'cod'},'Solicitante','Solicita Entregar a',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/peco/lista',
					itemdescr: 'PECOSA(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgPeco.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgPeco.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.solicitante)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.responsable)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgPeco.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgPeco.initAlma();
							}});
						}).data('estado',data.estado).contextMenu("conMenLgPeco", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')!='P'){
									$('#conMenLgPeco_def',menu).remove();
								}
								$('#conMenLgPeco_edi,#conMenLgPeco_rev,#conMenLgPeco_fin,#conMenLgPeco_dar,#conMenLgPeco_con,#conMenLgPeco_anu',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgPeco_ver': function(t) {
									lgPeco.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgPeco.initAlma();
									}});
								},
								'conMenLgPeco_def': function(t) {
									lgPeco.windowDefinir({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowDetails: function(p){
		if(p.goBack==null) p.goBack = null;
		if(p==null) p = {};
		new K.Panel({
			title: 'PECOSA '+p.nomb,
			contentURL: 'lg/peco/details',
			store: false,
			buttons: {
				"Regresar": {
					icon: 'fa-close',
					type: 'warning',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				var $cbo = p.$w.find('[name=almacen]');
				for(var i=0; i<K.session.almacenes.length; i++){
					$cbo.append('<option value="'+K.session.almacenes[i]._id.$id+'">'+K.session.almacenes[i].nomb+'</option>');
					$cbo.find('option:last').data('data',K.session.almacenes[i]);
				}
				p.$w.find('[name^=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				if(K.session.enti.roles.trabajador.oficina!=null)
					p.$w.find('[name=destino_a]').val(K.session.enti.roles.trabajador.oficina.nomb);
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),K.session.enti);
				p.$w.find('[name=mini_enti] [name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						filter: [{nomb: 'tipo_enti',value: 'P'}],
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
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Producto','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=gridProd] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" value="0"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=gridProd] tbody').append($row);
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						}).click();
					}
				});
				K.unblock();
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Solicitud de productos - Nueva PECOSA',
			contentURL: 'lg/peco/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var tmp = p.$w.find('[name=almacen] option:selected').data('data'),
						data = {
							fec: p.$w.find('[name=fec]').val(),
							destino: p.$w.find('[name=destino_a]').val(),
							ref: p.$w.find('[name=ref]').val(),
							almacen: {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								local: {
									_id: tmp.local._id.$id,
									descr: tmp.local.descr,
									direccion: tmp.local.direccion
								}
							},
							productos: []
						};
						var tmp = p.$w.find('[name=mini_enti]').data('data');
						if(tmp==null){
							p.$w.find('[name=mini_enti] [name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un responsable!',type: 'error'});
						}else data.responsable = mgEnti.dbRel(tmp);
						if(data.destino==''){
							p.$w.find('[name=destino_a]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un destino!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
							var prod = {
								item: parseInt(i)+1
							};
							var tmp = p.$w.find('[name=gridProd] tbody .item').eq(i).data('data');
							prod.producto = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb,
								unidad: {
									_id: tmp.unidad._id.$id,
									nomb: tmp.unidad.nomb
								}
							};
							if(tmp.clasif!=null){
								prod.producto.clasif = {
									_id: tmp.clasif._id.$id,
									cod: tmp.clasif.cod,
									nomb: tmp.clasif.nomb
								};
							}
							prod.solicitado = p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=cant]').val();
							if(prod.solicitado==''||parseFloat(prod.solicitado)<=0){ 
								p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
							}
							data.productos.push(prod);
						}
						if(p.id!=null){
							data._id = p.id;
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/peco/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "PECOSA guardada!"});
							lgPeco.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgPeco.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name^=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				if(K.session.enti.roles.trabajador.oficina!=null)
					p.$w.find('[name=destino_a]').val(K.session.enti.roles.trabajador.oficina.nomb);
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),K.session.enti);
				p.$w.find('[name=mini_enti] [name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						filter: [{nomb: 'tipo_enti',value: 'P'}],
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
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Producto','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=gridProd] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" value="0"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=gridProd] tbody').append($row);
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				$.post('lg/alma/all',function(data){
					var $cbo = p.$w.find('[name=almacen]');
					for(var i=0; i<data.length; i++){
						$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						$cbo.find('option:last').data('data',data[i]);
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Solicitud de productos - Editar PECOSA',
			contentURL: 'lg/peco/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var tmp = p.$w.find('[name=almacen] option:selected').data('data'),
						data = {
							_id:p.id,
							fec: p.$w.find('[name=fec]').val(),
							destino: p.$w.find('[name=destino_a]').val(),
							ref: p.$w.find('[name=ref]').val(),
							almacen: {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								local: {
									_id: tmp.local._id.$id,
									descr: tmp.local.descr,
									direccion: tmp.local.direccion
								}
							},
							productos: []
						};
						var tmp = p.$w.find('[name=mini_enti]').data('data');
						if(tmp==null){
							p.$w.find('[name=mini_enti] [name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un responsable!',type: 'error'});
						}else data.responsable = mgEnti.dbRel(tmp);
						if(data.destino==''){
							p.$w.find('[name=destino_a]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un destino!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
							var prod = {
								item: parseInt(i)+1
							};
							var tmp = p.$w.find('[name=gridProd] tbody .item').eq(i).data('data');
							prod.producto = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb,
								unidad: {
									_id: tmp.unidad._id.$id,
									nomb: tmp.unidad.nomb
								}
							};
							if(tmp.clasif!=null){
								prod.producto.clasif = {
									_id: tmp.clasif._id.$id,
									cod: tmp.clasif.cod,
									nomb: tmp.clasif.nomb
								};
							}
							prod.solicitado = p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=cant]').val();
							if(prod.solicitado==''||parseFloat(prod.solicitado)<=0){ 
								p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
							}
							data.productos.push(prod);
						}
						if(p.id!=null){
							data._id = p.id;
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/peco/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "PECOSA guardada!"});
							lgPeco.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgPeco.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name^=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				if(K.session.enti.roles.trabajador.oficina!=null)
					p.$w.find('[name=destino_a]').val(K.session.enti.roles.trabajador.oficina.nomb);
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),K.session.enti);
				p.$w.find('[name=mini_enti] [name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						filter: [{nomb: 'tipo_enti',value: 'P'}],
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
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Producto','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=gridProd] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" value="0"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=gridProd] tbody').append($row);
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				$.post('lg/alma/all',function(alma){
					var $cbo = p.$w.find('[name=almacen]');
					for(var i=0; i<alma.length; i++){
						$cbo.append('<option value="'+alma[i]._id.$id+'">'+alma[i].nomb+'</option>');
						$cbo.find('option:last').data('data',alma[i]);
					}
					$.post('lg/peco/get',{_id:p.id},function(data){
						p.$w.find('[name=fec]').val(moment(data.fec.sec,'X').format('YYYY-MM-DD'));
						p.$w.find('[name=destino_a]').val(data.destino);
						p.$w.find('[name=ref]').val(data.ref);
						p.$w.find('[name=almacen]').val(data.almacen._id.$id);

						if(data.productos!=null){
							if(data.productos.length>0){
								for(var i=0;i<data.productos.length;i++){
									var $row = $('<tr class="item" name="'+data.productos[i].producto._id.$id+'">');
									$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
									$row.append('<td><kbd>'+data.productos[i].producto.cod+'</kbd><br />'+data.productos[i].producto.nomb+'</td>');
									$row.append('<td>'+data.productos[i].producto.unidad.nomb+'</td>');
									$row.append('<td><input type="text" name="cant" class="form-control" value="'+data.productos[i].solicitado+'"></td>');
									$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
									$row.find('button:last').click(function(){
										$(this).closest('.item').remove();
										for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
											p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
										}
									});
									$row.data('data',data.productos[i].producto);
									p.$w.find('[name=gridProd] tbody').append($row);
								}
							}
						}
					},'json')
					K.unblock();
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Solicitud de productos - Ver PECOSA',
			contentURL: 'lg/peco/edit',
			buttons: {
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgPeco.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name^=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				if(K.session.enti.roles.trabajador.oficina!=null)
					p.$w.find('[name=destino_a]').val(K.session.enti.roles.trabajador.oficina.nomb);
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),K.session.enti);
				p.$w.find('[name=mini_enti] [name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						filter: [{nomb: 'tipo_enti',value: 'P'}],
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
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Producto','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=gridProd] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" value="0"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=gridProd] tbody').append($row);
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				$.post('lg/alma/all',function(alma){
					var $cbo = p.$w.find('[name=almacen]');
					for(var i=0; i<alma.length; i++){
						$cbo.append('<option value="'+alma[i]._id.$id+'">'+alma[i].nomb+'</option>');
						$cbo.find('option:last').data('data',alma[i]);
					}
					$.post('lg/peco/get',{_id:p.id},function(data){
						p.$w.find('[name=fec]').val(moment(data.fec.sec,'X').format('YYYY-MM-DD'));
						p.$w.find('[name=destino_a]').val(data.destino);
						p.$w.find('[name=ref]').val(data.ref);
						p.$w.find('[name=almacen]').val(data.almacen._id.$id);

						if(data.productos!=null){
							if(data.productos.length>0){
								for(var i=0;i<data.productos.length;i++){
									var $row = $('<tr class="item" name="'+data.productos[i].producto._id.$id+'">');
									$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
									$row.append('<td><kbd>'+data.productos[i].producto.cod+'</kbd><br />'+data.productos[i].producto.nomb+'</td>');
									$row.append('<td>'+data.productos[i].producto.unidad.nomb+'</td>');
									$row.append('<td><input type="text" name="cant" class="form-control" value="'+data.productos[i].solicitado+'"></td>');
									$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
									$row.find('button:last').click(function(){
										$(this).closest('.item').remove();
										for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
											p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
										}
									});
									$row.data('data',data.productos[i].producto);
									p.$w.find('[name=gridProd] tbody').append($row);
								}
							}
						}
						p.$w.find('input, select, textarea').attr('disabled','disabled');
					},'json');
					K.unblock();
				},'json');
			}
		});
	},
	windowDefinir: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Definir entrega de PECOSA',
			contentURL: 'lg/peco/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							productos: []
						};
						for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
							var prod = {
								item: parseInt(i)+1
							};
							prod.solicitado = p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=cant]').val();
							if(prod.solicitado==''||parseFloat(prod.solicitado)<=0){ 
								/*p.$w.find('[name=gridProd] tbody .item').eq(i).find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});*/
								prod.solicitado = 0;
							}
							data.productos.push(prod);
						}
						//return console.log(data);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/peco/definir",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Entrega definida para la PECOSA!"});
							lgPeco.initAlma();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgPeco.initAlma();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=fec]').attr('disabled','disabled');
				p.$w.find('[name=almacen]').replaceWith('<input type="text" class="form-control" name="almacen" disabled="disabled" />');
				p.$w.find('[name=destino_a]').attr('disabled','disabled');
				p.$w.find('[name=ref]').attr('disabled','disabled');
				p.$w.find('[name=mini_enti] [name=btnSel],[name=mini_enti] [name=btnAct]').remove();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Producto','Unidad','Cantidad solicitada','Stock en Almac&eacute;n','Cantidad atendida'],
					onlyHtml: true
				});
				$.post('lg/peco/get',{_id: p.id,all: true},function(data){
					if(data.fec!=null)
						p.$w.find('[name=fec]').val(ciHelper.date.format.bd_ymd(data.fec));
					p.$w.find('[name=almacen]').val(data.almacen.nomb);
					p.$w.find('[name=destino_a]').val(data.destino);
					p.$w.find('[name=ref]').val(data.ref);
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.responsable);
					for(var i=0, j=data.productos.length; i<j; i++){
						var $row = $('<tr class="item">');
						$row.append('<td>'+data.productos[i].item+'</td>');
						$row.append('<td>'+data.productos[i].producto.nomb+'</td>');
						$row.append('<td>'+data.productos[i].producto.unidad.nomb+'</td>');
						$row.append('<td>'+data.productos[i].solicitado+'</td>');
						var stock = 0,
						label = ' (No hay stock en el almac&eacute;n!)',
						cant = data.productos[i].solicitado,
						clase = 'success',
						adi = '';
						if(data.productos[i].stock==null){
							stock = 0;
						}else{
							stock = data.productos[i].stock;
							label = '';
						}
						if(stock==data.productos[i].solicitado){
							clase = 'warning'
							label = ' (Se agotar&aacute; el stock!)';
						}else if(stock<data.productos[i].solicitado){
							clase = 'danger';
							label = ' (No hay suficiente para lo solicitado!)';
							cant = stock;
						}
						$row.append('<td><label class="label label-'+clase+'">'+stock+label+'</label></td>');
						$row.append('<td><input type="number" class="form-control" name="cant" value="'+cant+'" '+adi+' max="'+stock+'" /></td>');
						p.$w.find('[name=gridProd] tbody').append($row);
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowConfirmar: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'PECOSA '+p.nomb,
			contentURL: 'lg/peco/details',
			store: false,
			buttons: {
				'Confirmar recepci&oacute;n': {
					icon: 'fa-check',
					type: 'success',
					f: function(){
						K.clearNoti();
						K.block();
						$.post('lg/peco/confirmar',{_id: p.id},function(){
							K.unblock();
							K.msg({title: ciHelper.titles.regiGua,text: 'Se confirmo la entrega de la PECOSA'});
							lgPeco.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						lgPeco.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Producto','Unidad','Solicitado','Atendido'],
					onlyHtml: true
				});
				$.post('lg/peco/get',{_id: p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.responsable);
					p.$w.find('[name=fec]').val(ciHelper.date.format.bd_ymd(data.fec));
					p.$w.find('[name=destino]').val(data.destino);
					p.$w.find('[name=almacen]').val(data.almacen._id.$id);
					p.$w.find('[name=ref]').val(data.ref);
					for(var i=0; i<data.productos.length; i++){
						var $row = $('<tr class="item">');
						$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
						$row.append('<td><kbd>'+data.productos[i].producto.cod+'</kbd><br />'+data.productos[i].producto.nomb+'</td>');
						$row.append('<td>'+data.productos[i].producto.unidad.nomb+'</td>');
						$row.append('<td>'+data.productos[i].solicitado+'</td>');
						$row.append('<td>'+data.productos[i].despachado+'</td>');
						p.$w.find('[name=gridProd] tbody').append($row);
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','lg/prod'],
	function(mgEnti,ctPcon,lgProd){
		return lgPeco;
	}
);