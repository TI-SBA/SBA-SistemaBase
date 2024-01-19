agProd = {
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			nomb: item.nomb,
			generico: item.generico,
			unidad: {
				cod: item.unidad.cod,
				nomb: item.unidad.nomb
			}
		};
	},
	almacenes: function(){
		var almacenes = [];
		for (var i = K.session.almacenes.length - 1; i >= 0; i--) {
			if(K.session.almacenes[i].aplicacion=='AG' || K.session.almacenes[i].aplicacion=='LG'){
				almacenes.push(K.session.almacenes[i]);
			}
		};
		return almacenes;
	},
	init: function(p){
		if(p==null) p = {};
		p.almacenes = agProd.almacenes();
		var out = 0;
		if(p.almacenes.length==0){
			ciHelper.confirm('Se ha detectado que no cuenta con almacenes asignados. &#191;desea cambiar usuario&#63;',
			function(){
				location.href = 'ci/index/logout';
			},function(){
				out = 1;
			},'Sin almacenes');
		}
		if(out>0) return false;
		K.initMode({
			mode: 'ag',
			action: 'agProd',
			titleBar: {
				title: 'Inventario de Productos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){

		   		var $grid = new K.grid({
					cols: ['',{n:'C&oacute;digo',f:'cod'},{n:'Nombre',f:'nomb'},'Stock','Precio Venta',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'ag/prod/lista',
					params: {modulo:'AG', almacen: p.almacenes[0]._id.$id},
					itemdescr: 'productos(s)',
					toolbarHTML: '<button name="btnLote" class="btn btn-primary"><i class="fa fa-shopping-bag"></i> Agregar Ingreso de Productos</button>'+
						'<select name="almacen" class="form-control"></select>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							agProd.windowNew();
						});
						$el.find('[name=btnLote]').click(function(){
							agProd.windowLote();
						});
						/**
						*	SELECTOR DE ALMACENES
						*/
						$el.find('[name=almacen]').change(function(){
							var almacen = $(this).find('option:selected').val();
							$grid.reinit({params: {
								modulo: 'AG',
								almacen: almacen
							}});
						});
						var $cbo = $el.find('[name=almacen]');
						for(var i=0; i<p.almacenes.length; i++){
							$cbo.append('<option value="'+p.almacenes[i]._id.$id+'">'+p.almacenes[i].nomb+'</option>');
						}
						//$cbo.change();
					},
					//onLoading: function(){ K.block(); },
					onComplete: function(){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+220+'px');
						K.unblock();
					},
					fill: function(data,$row){
						//console.log($(this));
						//console.log(data.producto);
						$row.append('<td>');
						$row.append('<td>'+data.producto.cod+'</td>');
						$row.append('<td>'+data.producto.nomb+'</td>');
						var stock = '--';
						if(data.stock!=null){
							//stock = data.stock.stock;
							stock = data.stock;
						}
						$row.append('<td><kbd>'+stock+'</kbd></td>');
						//$row.append('<td>'+ciHelper.formatMon(data.precio)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.producto.precio_venta)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.producto.fecmod)+'</kbd><br />'+mgEnti.formatName(data.producto.trabajador)+'</td>');
						//$row.data('id',data._id.$id).dblclick(function(){
						$row.data('id',data.producto._id.$id).dblclick(function(){
							console.log(data.producto._id);
							lgProd.windowDetails(
								{
									id: $(this).data('id'),
									nomb: $(this).find('td:eq(2)').html()
								}
						);
						}).contextMenu("conMenFaProd", {
							onShowMenu: function($row, menu) {
								$('#conMenFaProd_inc',menu).remove();
								//$('#conMenFaProd_ver,#conMenFaProd_edi,#conMenFaProd_inc',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFaProd_ver': function(t) {
									lgProd.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenFaProd_edi': function(t) {
									lgProd.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenFaProd_inc': function(t) {
									lgProd.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
				/*$grid.reinit({params: {
					almacen: $('[name=grid] [name=almacen] option:selected').val()
				}});*/
			}
		});
	},
	windowNew: function(p){
		/**
		*	Se desactiva los productos
		*/
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNew',
			title: 'Nuevo Producto',
			contentURL: 'ag/prod/edit',
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
							marca: {
								cod: p.$w.find('[name=marca] option:selected').val(),
								nomb: p.$w.find('[name=marca] option:selected').html()
							},
							precio: p.$w.find('[name=precio]').val(),
							descr: p.$w.find('[name=descr]').val(),
							unidad: {
								cod: p.$w.find('[name=unidad] option:selected').val(),
								nomb: p.$w.find('[name=unidad] option:selected').html()
							}
						};
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el c&oacute;digo del producto!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre del producto!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el precio del producto!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ag/prod/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Producto agregado!"});
							agProd.init();
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
				$.post('ag/conf/get',function(data){
					var $cbo = p.$w.find('[name=marca]');
					for(var i=0; i<data.marcas.length; i++){
						$cbo.append('<option value="'+data.marcas[i].cod+'">'+data.marcas[i].nomb+'</option>');
					}
					var $cbo = p.$w.find('[name=unidad]');
					for(var i=0; i<data.unidades.length; i++){
						$cbo.append('<option value="'+data.unidades[i].cod+'">'+data.unidades[i].nomb+'</option>');
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowNew',
			title: 'Editar Producto',
			contentURL: 'ag/prod/edit',
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
							marca: {
								cod: p.$w.find('[name=marca] option:selected').val(),
								nomb: p.$w.find('[name=marca] option:selected').html()
							},
							precio: p.$w.find('[name=precio]').val(),
							descr: p.$w.find('[name=descr]').val(),
							unidad: {
								cod: p.$w.find('[name=unidad] option:selected').val(),
								nomb: p.$w.find('[name=unidad] option:selected').html()
							}
						};
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el c&oacute;digo del producto!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre del producto!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el precio del producto!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ag/prod/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Producto actualizado!"});
							agProd.init();
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
				$.post('ag/conf/get',function(data){
					var $cbo = p.$w.find('[name=marca]');
					for(var i=0; i<data.marcas.length; i++){
						$cbo.append('<option value="'+data.marcas[i].cod+'">'+data.marcas[i].nomb+'</option>');
					}
					var $cbo = p.$w.find('[name=unidad]');
					for(var i=0; i<data.unidades.length; i++){
						$cbo.append('<option value="'+data.unidades[i].cod+'">'+data.unidades[i].nomb+'</option>');
					}
					$.post('ag/prod/get',{_id: p.id},function(data){
						p.$w.find('[name=cod]').val(data.cod);
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=marca]').selectVal(data.marca.cod);
						p.$w.find('[name=precio]').val(data.precio);
						p.$w.find('[name=descr]').val(data.descr);
						p.$w.find('[name=unidad]').selectVal(data.unidad.cod);
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	windowLote: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Ingresar nuevo lote de productos',
			contentURL: 'ag/prod/lote',
			store: false,
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							tipo: p.$w.find('[name=tipo_doc]').val(),
							num: p.$w.find('[name=num]').val(),
							fec: p.$w.find('[name=fec]').val(),
							local: {
								cod: p.$w.find('[name=local] option:selected').val(),
								nomb: p.$w.find('[name=local] option:selected').html()
							},
							almacen_origen: {
								_id: p.$w.find('[name=almacen_origen] :selected').val(),
								nomb: p.$w.find('[name=almacen_origen] :selected').text()
							},
							almacen_destino: {
								_id: p.$w.find('[name=almacen_destino] :selected').val(),
								nomb: p.$w.find('[name=almacen_destino] :selected').text()
							},
							items: []
						};
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar un numero!',type: 'error'});
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
								fec: $row.find('[name=fec]').val()
							};
							if(item.producto==null){
								$row.find('button:first').click();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe seleccionar un producto!',type: 'error'});
							}else item.producto = agProd.dbRel(item.producto);
							if(item.cant==null){
								$row.find('[name=cant]').focus();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
							}
							if(item.proveedor==null){
								$row.find('[name=proveedor]').focus();
								return K.msg({title:ciHelper.titles.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
							}
							data.items.push(item);
						}
						if(data.items.length==0){
							return K.msg({title:ciHelper.titles.infoReq,text: 'Debe ingresar al menos un producto!',type: 'error'});
						}
						//if(data.tipo=='B' || data.tipo=='F'){
						//	data.comp=p.$w.find('[name=comp_num]').val();
						//}
						//else if(data.tipo=='PEB' || data.tipo=='PSB'){
						//	data.pecosa=p.$w.find('[name=pecosa_num]').val();
						//}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('ag/prod/save_lote',data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Lote ingresado!"});
							agProd.init();
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						agProd.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=fec]').datepicker();
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					cols: ['Producto','Unidad','Cantidad','Proveedor','Fecha de Caducidad',''],
					onlyHtml: true,
					search: false,
					pagination: false,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var almacen = p.$w.find('[name=almacen_origen] :selected').val();
							lgProd.windowSelectProducto({
								modulo:'AG',
								almacen: almacen,
								callback: function(data){
									if(p.$w.find('[name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item">');
										$row.append('<td><kbd name="producto"></kbd></td>');
										$row.append('<td><span name="unidad"></span></td>');
										$row.append('<td><input type="text" class="form-control" name="cant" /></td>');
										$row.append('<td><select class="form-control" name="proveedor"></select></td>');
										var $cbo = $row.find('[name=proveedor]');
										for(var i=0; i<p.proveedores.length; i++){
											$cbo.append('<option value="'+p.proveedores[i]+'">'+p.proveedores[i]+'</option>');
										}
										$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
										$row.find('[name=fec]').datepicker();
										$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).remove();
										});
										$row.find('[name=producto]').html(data.cod+' - '+data.nomb).data('data',data);
										$row.find('[name=unidad]').html(data.unidad.nomb);
										$row.attr('name',data._id.$id);
										p.$w.find('[name=grid] tbody').append($row);
										p.$w.find('[name=grid] tbody [name=proveedor]:last').chosen();
										p.$w.find('[name=grid] tbody .item:last button:first').click();
									}else{
										K.msg({title: 'Registro ya incluido',text: 'El producto ya fue a&ntilde;adido!',type: 'error'});
									}
								}
							});
						});
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+200+'px');
					}
				});
				$.post('ag/conf/get',function(data){
					p.proveedores = data.proveedores;
					p.locales = data.locales;
					var $cbo = p.$w.find('[name=local]');
					for(var i=0; i<data.locales.length; i++){
						$cbo.append('<option value="'+data.locales[i].cod+'">'+data.locales[i].nomb+'</option>');
					}

					var $cbo_alma_orig = p.$w.find('[name=almacen_origen]');
					var $cbo_alma_dest = p.$w.find('[name=almacen_destino]');
					$cbo_alma_orig.append('<option value="0">PRODUCCION</option>');
					$cbo_alma_dest.append('<option value="0">DONACION</option>');
					for(var i=0; i<data.almacenes.length; i++){
						$cbo_alma_orig.append('<option value="'+data.almacenes[i]._id.$id+'">'+data.almacenes[i].nomb+'</option>');
						$cbo_alma_dest.append('<option value="'+data.almacenes[i]._id.$id+'">'+data.almacenes[i].nomb+'</option>');
					}
					K.unblock();
				},'json');
				//CUANDO SE HACE CLICK EN EL EVENTO DEL TIPO
				//p.$w.find('[name=tipo_doc]').click(function(){
				//	var tipo_comp = p.$w.find('[name=tipo_doc] :selected').val();
				//	if(tipo_comp != 'GR'){
				//		p.$w.find('[name=comp]').show();
				//	}else{
				//		p.$w.find('[name=comp]').hide();
				//	}
				//});
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
					modulo:p.modulo,
					almacen:p.almacen,
				};
				//var params = {stock:"directo"};
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Cod.','Nombre','Marca','Precio','Stock'],
					data: 'ag/prod/lista',
					//data: 'lg/prod/lista',
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
						$row.append('<td>'+data.marca.nomb+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.precio)+'</td>');
						$row.append('<td>');
						if(data.stock!=null){
							for(var i=0; i<data.stock.length; i++){
								$row.find('td:last').append(data.stock[i].cant);
							}
						}
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
							//if(data.stock>0)
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
	['mg/enti','lg/unid','lg/prod'],
	function(mgEnti,lgUnid, lgProd){
		return agProd;
	}
);