lgProd = {
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
	aplicaciones: [
		{cod: 'LG',descr: 'LOGISTICA'},
		{cod: 'FA',descr: 'FARMACIA'},
		{cod: 'AG',descr: 'VENTA DE AGUA'},
		{cod: 'US',descr: 'UNIDAD DE SERVICIOS ALIMENTARIOS'},
		{cod: 'BJ',descr: 'BALNEARIO DE JESUS'},
	],
	tipo: {
		'P': 'Producto Simple',
		'A': 'Activo',
		'N': 'Bien No Depreciable',
		'U': 'Bien Auxiliar'
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgProd',
			titleBar: {
				title: 'Producto'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: [
						'',
						'',
						{n: 'Cod.',f: 'cod'},
						{n:'Nombre',f:'nomb'},
						{n:'Unidad',f:'unidad.nomb'},
						{n:'Tipo',f:'tipo'},
						{n:'Clasificador',f:'clasif.cod'},
						{n:'Cuenta Contable',f:'cuenta.cod'},
						{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},
						{n:'Modificado por',f:'trabajador.fullname'}
					],
					data: 'lg/prod/lista',
					params: {
						cuenta: $('#mainPanel [name=cuenta]').data('id'),
						clasif: $('#mainPanel [name=clasif]').data('id')
					},
					itemdescr: 'producto(s)',
					toolbarHTML: '<div class="row">'+
							'<div class="col-sm-12"><button name="btnAgregar" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Agregar Nuevo Producto</button></div>'+
							'<div class="col-sm-6">'+
	      						'<div class="input-group">'+
									'<span class="input-group-addon">Cuenta Contable</span>'+
									'<span name="cuenta" class="form-control"></span>'+
									'<span class="input-group-btn">'+
										'<button name="btnCta" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
									'</span>'+
									'<span class="input-group-btn">'+
										'<button name="btnEliCta" type="button" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>'+
									'</span>'+
								'</div>'+
	    					'</div>'+
							'<div class="col-sm-6">'+
	      						'<div class="input-group">'+
									'<span class="input-group-addon">Clasificador</span>'+
									'<span name="clasif" class="form-control"></span>'+
									'<span class="input-group-btn">'+
										'<button name="btnCla" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
									'</span>'+
									'<span class="input-group-btn">'+
										'<button name="btnEliCla" type="button" class="btn btn-warning"><i class="fa fa-trash-o"></i></button>'+
									'</span>'+
								'</div>'+
	    					'</div>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgProd.windowNew();
						});
						$el.find('[name=btnCta]').click(function(){
							ctPcon.windowSelect({
								bootstrap: true,
								logistica: true,
								callback: function(data){
									$('#mainPanel [name=cuenta]').html(data.cod).data('id',data._id.$id);
									$grid.reinit({params: {
										sort: 'fecreg',
										cuenta: $('#mainPanel [name=cuenta]').data('id'),
										clasif: $('#mainPanel [name=clasif]').data('id')
									}});
								}
							});
						});
						$el.find('[name=btnEliCta]').click(function(){
							$('#mainPanel [name=cuenta]').html('').removeData('id');
							$grid.reinit({params: {
								cuenta: $('#mainPanel [name=cuenta]').data('id'),
								clasif: $('#mainPanel [name=clasif]').data('id')
							}});
						});
						$el.find('[name=btnCla]').click(function(){
							prClas.windowSelect({
								bootstrap: true,
								callback: function(data){
									$('#mainPanel [name=clasif]').html(data.cod).data('id',data._id.$id);
									$grid.reinit({params: {
										cuenta: $('#mainPanel [name=cuenta]').data('id'),
										clasif: $('#mainPanel [name=clasif]').data('id')
									}});
								}
							});
						});
						$el.find('[name=btnEliCla]').click(function(){
							$('#mainPanel [name=clasif]').html('').removeData('id');
							$grid.reinit({params: {
								cuenta: $('#mainPanel [name=cuenta]').data('id'),
								clasif: $('#mainPanel [name=clasif]').data('id')
							}});
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					stopLoad: true,
					afterLoad: function($g){
						$g.reinit();
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgProd.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.unidad.nomb+'</td>');
						if(data.tipo_producto!=null) $row.append('<td>'+lgProd.tipo[data.tipo_producto]+'</td>');
						else $row.append('<td>');
						if(data.clasif!=null) $row.append('<td>'+data.clasif.cod+'</td>');
						else $row.append('<td>');
						if(data.cuenta!=null) $row.append('<td>'+data.cuenta.cod+'</td>');
						else $row.append('<td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgProd.windowDetails(
								{
									id: $(this).data('id'),
									nomb: $(this).find('td:eq(3)').html()
								}
							);
						}).data('estado',data.estado).data('data',data).contextMenu("conMenLgProd", {
							onShowMenu: function($row, menu) {
								$('#conMenLgProd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenLgProd_hab',menu).remove();
								else $('#conMenLgProd_edi,#conMenLgProd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgProd_ver': function(t) {
									lgProd.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenLgProd_edi': function(t) {
									lgProd.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenLgProd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Producto <b>'+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/prod/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Producto Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgProd.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Producto');
								},
								'conMenLgProd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Producto <b>'+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/prod/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Producto Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgProd.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Producto');
								},
								'conMenLgProd_verf': function(t) {
									lgProd.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenLgProd_elim': function(t) {
									lgProd.windowDel({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenLgProd_mov': function(t) {
									lgProd.windowMovi({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html(),data: K.tmp.data('data')});
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
		new K.Window({
			id: 'windowDetailsProd'+p.id,
			title: 'Producto: '+p.nomb,
			contentURL: 'lg/prod/details',
			icon: 'ui-icon-document',
			width: 500,
			height: 400,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsProd'+p.id);
				K.block({$element: p.$w});
				$.post('lg/prod/get_details','id='+p.id,function(data){
					p.$w.find('[name=nomb]').html(data.prod.nomb);
					p.$w.find('[name=fecreg]').html(ciHelper.date.format.bd_ymd(data.prod.fecreg));
					p.$w.find('[name=descr]').html(data.prod.descr);
					if(data.prod.clasif!=null)
						p.$w.find('[name=clasif]').html(data.prod.clasif.cod).click(function(){
							prClas.windowDetails({id: $(this).data('data')._id.$id,nomb: $(this).data('data').cod});
						}).data('data',data.prod.clasif);
					else
						p.$w.find('[name=clasif]').html('--');
					if(data.prod.cuenta!=null)
						p.$w.find('[name=cuenta]').html(data.prod.cuenta.cod).click(function(){
							ctPcon.windowDetails({id: $(this).data('data')._id.$id,nomb: $(this).data('data').cod});
						}).data('data',data.prod.cuenta);
					else
						p.$w.find('[name=cuenta]').html('--');
					p.$w.find('[name=cod]').html(data.prod.cod);
					p.$w.find('[name=unidad]').html(data.prod.unidad.nomb);
					if(data.prod.stock!=null){
						for(var i=0; i<data.prod.stock.length; i++){
							p.$w.find('table:last').append('<tr>');
							p.$w.find('tr:last').append('<td width="170px"><label>'+data.prod.stock[i].almacen.nomb+'</label></td>');
							p.$w.find('tr:last').append('<td><span>'+data.prod.stock[i].actual+' "'+data.prod.unidad.nomb+'"</span></td>');
							//p.$w.find('tr:last').append('<td><span>'+data.prod.stock[i].stock+' "'+data.prod.unidad.nomb+'"</span></td>');
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewProd',
			title: 'Nuevo Producto',
			contentURL: 'lg/prod/edit',
			store: false,
			width: 600,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.$w.find('[name=cod]').val()
						};
						var clasif = p.$w.find('[name=clasif]').data('data');
						if(clasif==null){
							p.$w.find('[name=btnClasi]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un clasificador de gastos!',type: 'error'});
						}else{
							data.clasif = {
								_id: clasif._id.$id,
								cod: clasif.cod,
								nomb: clasif.nomb
							};
						}
						var cuenta = p.$w.find('[name=cuenta]').data('data');
						if(cuenta==null){
							p.$w.find('[name=btnCuenta]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						}else{
							data.cuenta = {
								_id: cuenta._id.$id,
								cod: cuenta.cod,
								descr: cuenta.descr
							};
						}
						data.nomb = p.$w.find('[name=nomb]').val();
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de producto!',type: 'error'});
						}
						data.descr = p.$w.find('[name=descr]').val();
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el producto!',type: 'error'});
						}
						var unidad = p.$w.find('[name=unid]').data('data');
						if(unidad==null){
							p.$w.find('[name=unid]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una unidad para el producto!',type: 'error'});
						}else{
							data.unidad = {
								_id: unidad._id.$id,
								nomb: unidad.nomb
							};
						}
						if(p.$w.find('[name=precioref]').val()=='') data.precio = 0;
						else data.precio = p.$w.find('[name=precioref]').val();
						if(p.$w.find('[name=precioVenta]').val()==''){
							data.precio_venta = 0;
						}
						else{
							data.precio_venta = p.$w.find('[name=precioVenta]').val();
						} 

						data.tipo_producto = p.$w.find('[name=tipo] option:selected').val();

						data.modulo = p.$w.find('[name=modulo] option:selected').val();

						data.generico = p.$w.find('[name=generico]').val();

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/prod/save',data,function(prod){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El producto fue registrado con &eacute;xito!'});
							if(p.callback!=null){
								p.callback(prod);
							}else{
								lgProd.init();
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
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewProd');
				//p.$w.find('tr:last').remove();
				K.block({$element: p.$w});
				$.post('lg/prod/edit_data','id='+p.id,function(data){
					p.cod = data.cod;
					/*if(data.clasif==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear primero clasificadores de gasto en Contabilidad!',type: 'error'});
					}*/
					if(data.unid==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear primero unidades de medida!',type: 'error'});
					}
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=btnClasi]').click(function(){
						prClas.windowSelect({bootstrap: true,callback: function(data){
							p.$w.find('[name=clasif]').html(data.cod).data('data',data);
						}});
					});
					p.$w.find('[name=btnEliClasi]').click(function(){
						p.$w.find('[name=clasif]').html('').removeData('data');
					});
					p.$w.find('[name=btnCuenta]').click(function(){
						ctPcon.windowSelect({bootstrap: true,callback: function(data){
							p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
						}});
					});
					p.$w.find('[name=btnEliCuenta]').click(function(){
						p.$w.find('[name=cuenta]').html('').removeData('data');
					});
					for(var i=0; i<data.unid.length; i++){
						data.unid[i].label = data.unid[i].nomb;
					}
					p.$w.find('[name=btnUnid]').click(function(){
						lgUnid.windowSelect({callback: function(data){
							p.$w.find('[name=unid]').html(data.nomb).data('data',data);
						}});
					});
					p.$w.find('[name=precioref]').val(0).numeric();
					p.$w.find('[name=precioVenta]').val(0).numeric();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditProd'+p.id,
			title: 'Editar Producto: '+p.nomb,
			contentURL: 'lg/prod/edit',
			width: 600,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cod: p.$w.find('[name=cod]').val()
						};
						var clasif = p.$w.find('[name=clasif]').data('data');
						if(clasif==null){
							p.$w.find('[name=clasif]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un clasificador de gastos!',type: 'error'});
						}else{
							data.clasif = {
								_id: clasif._id.$id,
								cod: clasif.cod,
								nomb: clasif.nomb
							};
						}

						var cuenta = p.$w.find('[name=cuenta]').data('data');
						if(cuenta==null){
							p.$w.find('[name=cuenta]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						}else{
							data.cuenta = {
								_id: cuenta._id.$id,
								cod: cuenta.cod,
								descr: cuenta.descr
							};
						}

						data.nomb = p.$w.find('[name=nomb]').val();
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de producto!',type: 'error'});
						}
						data.descr = p.$w.find('[name=descr]').val();
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el producto!',type: 'error'});
						}
						var unidad = p.$w.find('[name=unid]').data('data');
						if(unidad==null){
							p.$w.find('[name=unid]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una unidad para el producto!',type: 'error'});
						}else{
							data.unidad = {
								_id: unidad._id.$id,
								nomb: unidad.nomb
							};
						}
						if(p.$w.find('[name=precioref]').val()==''){
							data.precio = 0;
						} 
						else{
							data.precio = p.$w.find('[name=precioref]').val();
						}

						if(p.$w.find('[name=precioVenta]').val()==''){
							data.precio_venta = 0;
						}
						else{
							data.precio_venta = p.$w.find('[name=precioVenta]').val();
						} 

						data.tipo_producto = p.$w.find('[name=tipo] option:selected').val();

						data.modulo = p.$w.find('[name=modulo] option:selected').val();

						data.generico = p.$w.find('[name=generico]').val();

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/prod/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El producto fue actualizado con &eacute;xito!'});
							
							//COMENTAR LA SIGUIENTE LINEA DE CODIGO
							//lgProd.init();
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
				p.$w = $('#windowEditProd'+p.id);
				K.block({$element: p.$w});
				$.post('lg/prod/edit_data','id='+p.id,function(data){
					p.$w.find('[name=btnClasi]').click(function(){
						prClas.windowSelect({bootstrap: true,callback: function(data){
							p.$w.find('[name=clasif]').html(data.cod).data('data',data);
						}});
					});
					p.$w.find('[name=btnEliClasi]').click(function(){
						p.$w.find('[name=clasif]').html('').removeData('data');
					});
					p.$w.find('[name=btnCuenta]').click(function(){
						ctPcon.windowSelect({bootstrap: true,callback: function(data){
							p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
						}});
					});
					p.$w.find('[name=btnEliCuenta]').click(function(){
						p.$w.find('[name=cuenta]').html('').removeData('data');
					});
					p.$w.find('[name=btnUnid]').click(function(){
						lgUnid.windowSelect({callback: function(data){
							p.$w.find('[name=unid]').html(data.nomb).data('data',data);
						}});
					});
					p.$w.find('[name=precioref]').numeric();
					p.$w.find('[name=precioVenta]').numeric();

					$.post('lg/prod/get','id='+p.id,function(data){
						p.$w.find('[name=cod]').val(data.cod);
						/*
						 * Quitar este if con el tiempo, es solamente ahora que se separan las cuentas de clasificadores
						 */
						if(data.clasif!=null){
							p.$w.find('[name=clasif]').html(data.clasif.cod).data('data',data.clasif);
						}
						/*
						 * Quitar este if con el tiempo, es solamente ahora que se separan las cuentas de clasificadores
						 */
						if(data.cuenta!=null){
							p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
						}
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=descr]').val(data.descr);
						p.$w.find('[name=unid]').html(data.unidad.nomb).data('data',data.unidad);
						p.$w.find('[name=precioref]').val(data.precio);
						if(data.precio_venta!=null)
							p.$w.find('[name=precioVenta]').val(data.precio_venta);
						else
							p.$w.find('[name=precioVenta]').val(0);
						
						if(data.tipo_producto!=null)
							p.$w.find('[name=tipo]').selectVal(data.tipo_producto);

						if(data.modulo!=null)
							p.$w.find('[name=modulo]').selectVal(data.modulo);

						if(data.generico!=null)
							p.$w.find('[name=generico]').val(data.generico);

						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowDel: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar producto '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> el producto <strong>'+p.nomb+'</strong>&#63;',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function() {
					K.sendingInfo();
					$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('lg/prod/delete','id='+p.id,function(){
						K.clearNoti();
						K.closeWindow('windowDelete');
						K.notification({title: ciHelper.titleMessages.regiEli,text: 'El producto fue eliminado con &eacute;xito!'});
						lgProd.init();
					});
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	},
	windowMovi: function(p){
		new K.Panel({
			contentURL: 'lg/prod/movi',
			buttons: {
				"Regresar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgProd.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').html(p.data.cod);
				p.$w.find('[name=nomb]').html(p.data.nomb);
				if(p.data.cuenta!=null){
					p.$w.find('[name=cod_cta]').html(p.data.cuenta.cod);
					p.$w.find('[name=descr_cta]').html(p.data.cuenta.descr);
				}
				if(p.data.unidad!=null){
					p.$w.find('[name=unidad]').html(p.data.unidad.nomb);
				}
				p.$w.find('[name=almacen]').change(function(){
					p.$grid.reinit({params: {producto: p.id,almacen: p.$w.find('[name=almacen] option:selected').val()}});
				});
				p.$grid = new K.grid({
					$el: p.$w.find('[name=gridMovi]'),
					cols: ['','Mes','D&iacute;a','Tipo Doc.','Num.','Programa','Entrada<br />F&iacute;sico','Salida<br />F&iacute;sico','Saldo<br />F&iacute;sico','Precio Unitario','Costo Promedio','Entrada<br />Valorado','Salida<br />Valorado','Saldo<br />Valorado'],
					data: 'lg/movi/lista',
					params: {producto: p.id},
					itemdescr: 'movimiento(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgProd.newMovi({
								id: p.id,
								producto: p.data,
								almacen: p.$w.find('[name=almacen] option:selected').data('data')
							});
						});
					},
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					stopLoad: true,
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+ciHelper.date.format.bd_m(data.fecreg)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_d(data.fecreg)+'</td>');
						if(data.tipo=='I'){
							$row.append('<td>Inventario</td>');
							$row.append('<td>');
						}else{
							$row.append('<td>'+data.documento.tipo+'</td>');
							$row.append('<td>'+data.documento.cod+'</td>');
						}
						$row.append('<td>'+data.organizacion.nomb+'</td>');
						if(data.tipo=='E'){
							$row.append('<td>'+data.cant+'</td>');
							$row.append('<td></td>');
						}else if(data.tipo=='S'){
							$row.append('<td></td>');
							$row.append('<td>'+data.cant+'</td>');
						}else{
							$row.append('<td>'+data.cant+'</td>');
							$row.append('<td></td>');
						}
						/*
						$row.append('<td>'+data.saldo+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total)+'</td>');
						if(data.tipo=='E'){
							$row.append('<td>'+data.precio_unitario+'</td>');
							$row.append('<td></td>');
						}else if(data.tipo=='S'){
							$row.append('<td></td>');
							$row.append('<td>'+data.precio_unitario+'</td>');
						}else{
							$row.append('<td>'+data.precio_unitario+'</td>');
							$row.append('<td></td>');
						}
						*/
						//$row.append('<td>'+ciHelper.formatMon(data.saldo_precio)+'</td>');
						//Modificado por Giancarlo 
						$row.append('<td>'+data.saldo+'</td>');

						if(data.tipo=='E'){
							$row.append('<td>'+ciHelper.formatMon(data.precio_unitario)+'</td>');
							$row.append('<td></td>');
							$row.append('<td>'+ciHelper.formatMon(data.total)+'</td>');
							$row.append('<td></td>');
						}else if(data.tipo=='S'){
							$row.append('<td></td>');
							$row.append('<td>'+ciHelper.formatMon(data.precio_unitario)+'</td>');
							$row.append('<td></td>');
							$row.append('<td>'+ciHelper.formatMon(data.total)+'</td>');
						}else{
							$row.append('<td>'+ciHelper.formatMon(data.precio_unitario)+'</td>');
							$row.append('<td></td>');
							$row.append('<td>'+ciHelper.formatMon(data.total)+'</td>');
							$row.append('<td></td>');
						}
						$row.append('<td>'+ciHelper.formatMon(data.saldo_imp)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inTipo.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									inTipo.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									inTipo.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inTipo.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inTipo.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
								}
							}
						});
						return $row;
					}
				});
				$.post('lg/alma/all',function(data){
					var $cbo = p.$w.find('[name=almacen]');
					for(var i=0,j=data.length; i<j; i++){
						$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						$cbo.find('option:last').data('data',data[i]);
					}
					$cbo.change();
				},'json');
			}
		});
	},
	newMovi: function(p){
		new K.Panel({
			contentURL: 'lg/prod/new_movi',
			store: false,
			buttons: {
				"Guardar Movimiento": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							fec: p.$w.find('[name=fec]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').attr('data-mov'),
							documento: {
								tipo: p.$w.find('[name=tipo] option:selected').val(),
								cod: p.$w.find('[name=num]').val()
							},
							almacen: p.almacen,
							producto: lgProd.dbRel(p.producto),
							cant: p.$w.find('[name=grid] tbody .item:eq(0) input').val(),
							total: p.$w.find('[name=grid] tbody .item:eq(1) input').val(),
							organizacion: p.$w.find('[name=organizacion]').data('data')
						};
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de documento!',type: 'error'});
						}
						if(data.organizacion==null){
							p.$w.find('[name=btnProg]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un programa!',type: 'error'});
						}else data.organizacion = {
							_id: data.organizacion._id.$id,
							nomb: data.organizacion.nomb
						};
						if(data.documento.cod==''){
							p.$w.find('[name=num]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero de documento!',type: 'error'});
						}
						if(p.$w.find('[name=grid] tbody .item:eq(0) input').val()==''){
							p.$w.find('[name=grid] tbody .item:eq(0) input').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
						}else data.cant = parseFloat(data.cant);
						if(p.$w.find('[name=grid] tbody .item:eq(1) input').val()==''){
							p.$w.find('[name=grid] tbody .item:eq(1) input').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor!',type: 'error'});
						}else data.total = parseFloat(data.total);
						data.precio_unitario = K.round(data.total/data.cant,5);
						data.almacen = {
							_id: data.almacen._id.$id,
							nomb: data.almacen.nomb,
							local: {
								_id: data.almacen.local._id.$id,
								direccion: data.almacen.local.direccion,
								descr: data.almacen.local.descr
							}
						};
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/movi/save_new',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El producto fue actualizado con &eacute;xito!'});
							lgProd.windowMovi({id: p.id,nomb: p.producto.nomb,data: p.producto});
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgProd.windowMovi({id: p.id,nomb: p.producto.nomb,data: p.producto});
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=fec]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
				p.$w.find('[name=btnProg]').click(function(){
					mgOrga.windowSelect({
						callback: function(data){
							p.$w.find('[name=organizacion]').html(data.nomb).data('data',data);
						}
					});
				});
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['','Entrada','Salida','Saldo'],
					onlyHtml: true,
					afterLoad: function(){
						var $row = $('<tr class="item">');
						$row.append('<td><b>F&Iacute;SICO</b></td>');
						$row.append('<td></td>');
						$row.append('<td></td>');
						$row.append('<td></td>');
						p.$w.find('[name=grid] tbody').append($row);
						var $row = $('<tr class="item">');
						$row.append('<td><b>VALORADO</b></td>');
						$row.append('<td></td>');
						$row.append('<td></td>');
						$row.append('<td></td>');
						p.$w.find('[name=grid] tbody').append($row);
						p.$w.find('[name=tipo]').change(function(){
							p.$w.find('[name=grid] tbody .item:eq(0) td:eq(1)').html('');
							p.$w.find('[name=grid] tbody .item:eq(1) td:eq(1)').html('');
							p.$w.find('[name=grid] tbody .item:eq(0) td:eq(2)').html('');
							p.$w.find('[name=grid] tbody .item:eq(1) td:eq(2)').html('');
							if($(this).find('option:selected').attr('data-mov')=='E'){
								p.$w.find('[name=grid] tbody .item:eq(0) td:eq(1)').append('<input type="number" value="0">');
								p.$w.find('[name=grid] tbody .item:eq(1) td:eq(1)').append('<input type="number" value="0">');
							}else{
								p.$w.find('[name=grid] tbody .item:eq(0) td:eq(2)').append('<input type="number" value="0">');
								p.$w.find('[name=grid] tbody .item:eq(1) td:eq(2)').append('<input type="number" value="0">');
							}
							p.$w.find('[name=grid] tbody input').keyup(function(){
								var cant = p.$w.find('[name=grid] tbody .item:eq(0) input').val(),
								valor = p.$w.find('[name=grid] tbody .item:eq(1) input').val();
								p.$w.find('[name=precio]').html(ciHelper.formatMon(valor/cant));
							});
						}).change();
					}
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 450,
			title: 'Seleccionar Producto',
			allScreen: true,
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
				"Crear Nuevo Producto": {
					icon: 'fa-plus',
					type: 'success',
					f: function(){
						lgProd.windowNew({
							callback: p.callback
						});
						K.closeWindow(p.$w.attr('id'));
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
					cols: ['','C&oacute;digo','Concepto','Unidad','Valor Unitario'],
					data: 'lg/prod/lista',
					params: {},
					itemdescr: 'producto(s)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.unidad.nomb+'</td>');
						$row.append('<td>'+data.precio+'</td>');
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
	},
	windowSelectProducto: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Productos a vender',
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
					cols: ['','Nombre','Cuenta','Stock'],
					data: 'lg/prod/lista',
					params: {almacen: p.almacen, modulo:p.modulo},
					itemdescr: 'producto(s)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.cuenta.cod+'</td>');
						var stock = '--';
						if(data.stock!=null){
							stock = data.stock.stock
						}
						$row.append('<td>'+stock+'</td>');
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
	['mg/enti','ct/pcon','pr/clas','lg/unid','mg/orga'],
	function(mgEnti,ctPcon,prClas,lgUnid,mgOrga){
		$.post('lg/alma/all',function(data){
			K.session.almacenes = data;
		},'json');
		$.post('mg/prog/all',function(data){
			K.session.programas = data;
		},'json');
		return lgProd;
	}
);