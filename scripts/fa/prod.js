faProd = {
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			nomb: item.nomb,
			generico: item.generico,
			unidad: {
				_id: item.unidad._id.$id,
				nomb: item.unidad.nomb
			}
		};
	},
	init: function(){
		K.initMode({
			mode: 'fa',
			action: 'faProd',
			titleBar: {
				title: 'Inventario de Productos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'C&oacute;digo',f:'cod'},{n:'Nombre',f:'nomb'},'Gen&eacute;rico','Stock','Precio',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					//data: 'fa/prod/lista',
					data: 'lg/prod/lista',
					//params: {},
					params: {
						almacen: "5894e68c3e6037e8798b4567",
						stock: "directo",
						modulo: "FA",
					},
					itemdescr: 'productos(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Nuevo Producto</button>&nbsp;'+
						'<button name="btnLote" class="btn btn-primary"><i class="fa fa-shopping-bag"></i> Agregar Ingreso de Productos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgProd.windowNew();
						});
						$el.find('[name=btnLote]').click(function(){
							faProd.windowLote();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){
						K.unblock();
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+220+'px');
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.generico+'</td>');
						//$row.append('<td>'+data.stock+'</td>');
						//if(data.stock.almacen._id.$id == "5894e68c3e6037e8798b4567")
						$row.append('<td>'+data.stock+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.precio_venta)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							faProd.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).contextMenu("conMenFaProd", {
							onShowMenu: function($row, menu) {
								$('#conMenFaProd_inc',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFaProd_ver': function(t) {
									//faProd.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
									lgProd.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
									//lgProd.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenFaProd_edi': function(t) {
									lgProd.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenFaProd_inc': function(t) {
									faProd.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
/*	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNew',
			title: 'Nuevo Producto',
			//contentURL: 'fa/prod/edit',
			contentURL: 'lg/prod/edit',
			store: false,
			width: 550,
			height: 400,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.$w.find('[name=cod]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							generico: p.$w.find('[name=generico]').val(),
							precio: p.$w.find('[name=precio]').val(),
							descr: p.$w.find('[name=descr]').val(),
							unidad: p.$w.find('[name=unidad]').data('data')
						};
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el c&oacute;digo del producto!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre del producto!',type: 'error'});
						}
						if(data.generico==''){
							p.$w.find('[name=generico]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre generico del producto!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el precio del producto!',type: 'error'});
						}
						if(data.unidad==null){
							p.$w.find('[name=btnUnid]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la unidad del producto!',type: 'error'});
						}else data.unidad = lgUnid.dbRel(data.unidad);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						//$.post("fa/prod/save",data,function(result){
						//	K.clearNoti();
						//	K.msg({title: ciHelper.titles.regiGua,text: "Producto agregado!"});
						//	faProd.init();
						//	K.closeWindow(p.$w.attr('id'));
						//},'json');
						//
						$.post("fa/prod/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Producto agregado!"});
							faProd.init();
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
				p.$w = $('#windowNew');
				p.$w.find('[name=btnUnid]').click(function(){
					lgUnid.windowSelect({callback: function(data){
						p.$w.find('[name=unidad]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},
*/
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
						data.tipo_producto = p.$w.find('[name=tipo] option:selected').val();
						data.modulo = "FA";
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/prod/save',data,function(prod){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El producto fue registrado con &eacute;xito!'});
							if(p.callback!=null){
								p.callback(prod);
							}else{
								faProd.init();
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
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
/*	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowNew',
			title: 'Editar Producto',
			contentURL: 'fa/prod/edit',
			store: false,
			width: 550,
			height: 400,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cod: p.$w.find('[name=cod]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							generico: p.$w.find('[name=generico]').val(),
							precio: p.$w.find('[name=precio]').val(),
							descr: p.$w.find('[name=descr]').val(),
							unidad: p.$w.find('[name=unidad]').data('data')
						};
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el c&oacute;digo del producto!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre del producto!',type: 'error'});
						}
						if(data.generico==''){
							p.$w.find('[name=generico]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre generico del producto!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el precio del producto!',type: 'error'});
						}
						if(data.unidad==null){
							p.$w.find('[name=btnUnid]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la unidad del producto!',type: 'error'});
						}else data.unidad = lgUnid.dbRel(data.unidad);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("fa/prod/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Producto actualizado!"});
							faProd.init();
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
				p.$w = $('#windowNew');
				K.block();
				p.$w.find('[name=btnUnid]').click(function(){
					lgUnid.windowSelect({callback: function(data){
						p.$w.find('[name=unidad]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				//$.post('fa/prod/get',{_id: p.id},function(data){
				$.post('lg/prod/getget',{_id: p.id},function(data){
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=generico]').val(data.generico);
					p.$w.find('[name=precio]').val(data.precio);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=unidad]').html(data.unidad.nomb).data('data',data.unidad);
					K.unblock();
				},'json');
			}
		});
	},
*/
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
						if(p.$w.find('[name=precioref]').val()=='') data.precio = 0;
						else data.precio = p.$w.find('[name=precioref]').val();
						data.tipo_producto = p.$w.find('[name=tipo] option:selected').val();
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/prod/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El producto fue actualizado con &eacute;xito!'});
							faProd.init();
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
						if(data.tipo_producto!=null)
							p.$w.find('[name=tipo]').selectVal(data.tipo_producto);
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},

	windowDetails: function(p){
		K.incomplete();
	},
	windowLote: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Ingresar nuevo lote de productos',
			contentURL: 'fa/prod/lote',
			store: false,
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							num: p.$w.find('[name=num]').val(),
							guia: p.$w.find('[name=guia]').val(),
							fec: p.$w.find('[name=fec]').val(),
							items: []
						};
						if(data.guia==''){
							p.$w.find('[name=guia]').focus();
							return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar una guia de remision!',type: 'error'});
						}
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar una fecha!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var $row = p.$w.find('[name=grid] tbody .item').eq(i),
							item = {
								producto: $row.find('[name=producto]').data('data'),
								cant: $row.find('[name=cant]').val(),
								proveedor: $row.find('[name=proveedor]').val(),
								laboratorio: $row.find('[name=laboratorio]').val(),
								fec: $row.find('[name=fec]').val()
							};
							if(item.producto==null){
								$row.find('button:first').click();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe seleccionar un producto!',type: 'error'});
							}else item.producto = faProd.dbRel(item.producto);
							if(item.cant==null){
								$row.find('[name=cant]').focus();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
							}
							if(item.proveedor==null){
								$row.find('[name=proveedor]').focus();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
							}
							if(item.laboratorio==null){
								$row.find('[name=laboratorio]').focus();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe seleccionar un laboratorio!',type: 'error'});
							}
							data.items.push(item);
						}
						if(data.items.length==0){
							return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar al menos un producto!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('fa/prod/save_lote',data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Lote ingresado!"});
							faProd.init();
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						faProd.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=fec]').datepicker();
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					cols: ['Producto','Unidad','Cantidad','Proveedor','Laboratorio','Fecha de Caducidad',''],
					onlyHtml: true,
					search: false,
					pagination: false,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><kbd name="producto"></kbd>&nbsp;<button class="btn btn-xs btn-info"><i class="fa fa-search"></i></button></td>');
							$row.find('button:last').click(function(){
								var $row = $(this).closest('.item');
								faProd.windowSelect({callback: function(data){
									if(p.$w.find('[name='+data._id.$id+']').length==0){
										$row.find('[name=producto]').html(data.cod+' - '+data.nomb).data('data',data);
										$row.find('[name=unidad]').html(data.unidad.nomb);
										$row.attr('name',data._id.$id);
									}else{
										K.msg({title: 'Registro ya incluido',text: 'El producto ya fue a&ntilde;adido!',type: 'error'});
									}
								}});
							});
							$row.append('<td><span name="unidad"></span></td>');
							$row.append('<td><input type="text" class="form-control" name="cant" /></td>');
							$row.append('<td><select class="form-control" name="proveedor"></select></td>');
							var $cbo = $row.find('[name=proveedor]');
							for(var i=0; i<p.proveedores.length; i++){
								$cbo.append('<option value="'+p.proveedores[i]+'">'+p.proveedores[i]+'</option>');
							}
							$row.append('<td><select class="form-control" name="laboratorio"></select></td>');
							var $cbo = $row.find('[name=laboratorio]');
							for(var i=0; i<p.laboratorios.length; i++){
								$cbo.append('<option value="'+p.laboratorios[i]+'">'+p.laboratorios[i]+'</option>');
							}
							$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
							$row.find('[name=fec]').datepicker();
							$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button:last').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=grid] tbody').append($row);
							p.$w.find('[name=grid] tbody [name=proveedor]:last').chosen();
							p.$w.find('[name=grid] tbody [name=laboratorio]:last').chosen();
							p.$w.find('[name=grid] tbody .item:last button:first').click();
						});
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+200+'px');
					}
				});
				$.post('fa/conf/get',function(data){
					p.proveedores = data.proveedores;
					p.laboratorios = data.laboratorios;
					K.unblock();
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.texto==null) p.texto = '';
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			allScreen: true,
			title: 'Seleccionar Producto',
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
								text: 'Debe seleccionar un item con stock!',
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
			onClose: function(){
				$('#mainPanel [name=prod]').focus();
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				var params = {
					modulo:"FA", 
					stock:"directo",
					almacen: "5894e68c3e6037e8798b4567",
				};
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Cod.','Nombre','Gen&eacute;rico','Precio','Stock'],
					data: 'lg/prod/lista',
					//data_stock: 'lg/stck/lista',
					params: params,
					itemdescr: 'producto(s)',
					forget: true,
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					toolbarHTML: '',
					onContentLoaded: function(){
						p.$w.find('[name=tmp] .datagrid-header-right .search input').val(p.texto);
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.generico+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.precio_venta)+'</td>');
						$row.append('<td>'+data.stock+'</td>');
						$row.dblclick(function(){
							p.$w.find('.modal-footer button:first').click();
						}).contextMenu('conMenListSel', {
							bindings: {
								'conMenListSel_sel': function(t) {
									p.$w.find('.modal-footer button:first').click();
								}
							}
						});
						if(p.stock==null){
							$row.data('data',data);
						}else{
							if(data.stock>0)
								$row.data('data',data);
						}
						return $row;
					}
				});
			}
		});
	}
};
define(
	['mg/enti','lg/unid'],
	function(mgEnti,lgUnid){
		return faProd;
	}
);