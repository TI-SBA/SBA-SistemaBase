agVent = {
	init: function(p){
		K.initMode({
			mode: 'ag',
			action: 'agVent',
			titleBar: {
				title: 'Venta de Productos'
			}
		});
		/*return $.post('cj/cuen/get_config_agua',function(data){
			cjEcom.boleta({
				config: data,
				producto: 'AG',
				caja: 'AG',
				goBack: agProd.init
			});
		},'json');*/
		if(p==null) p = {};
		$.extend(p,{
			moneda: 'S',
			recal: function(){
				p.$w.find('[name=gridServ] tbody').empty();
				K.msg({text: 'Local modificado, elija nuevamente sus productos!'});
			},
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.$w.find('[name=tasa]').val(),
				total_row = 0;
				if(tasa=='') tasa = p.tc;
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=conc]').length; i<j; i++){
					var monto = parseFloat(p.$w.find('[name=gridServ] [name^=conc]').eq(i).find('[name=precio]').val());
					monto = monto * p.$w.find('[name=gridServ] [name^=conc]').eq(i).find('[name=cant]').val();
					if(isNaN(p.$w.find('[name=gridServ] [name^=conc]').eq(i).find('[name=cant]').val()))
						monto = 0;
					total += monto;
					p.$w.find('[name=gridServ] tbody [name^=conc]').eq(i).data('monto',monto);

					//p.$w.find('[name=gridServ] tbody [name^=conc]').eq(i).find('td:eq(3)').html('<b>'+ciHelper.formatMon(monto)+'</b>');
				}
				total = K.round(total,2);
				p.$w.find('[name=totalGrid]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				if(p.total>700){
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
					p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion)+'</p>');
				}else{
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
				}
				p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(p.total,2));
				if(p.total>=700){
					var tmp_efec = total - (total*p.detraccion),
					tmp_detr = total*p.detraccion;
					p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
					p.$w.find('[name=gridForm] [name=voucher]:eq(2)').val('XXX');
					p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(K.round(tmp_detr,2));
				}
				p.$w.find('[name=gridForm] [name=tot]').keyup();
			},
			save: function(){
				var data = {
					modulo: 'AG',
					cliente: p.$w.find('[name=mini_enti]').data('data'),
					caja: p.$w.find('[name=caja] option:selected').data('data'),
					tipo: p.$w.find('[name=comp] option:selected').val(),
					serie: p.$w.find('[name=serie] option:selected').html(),
					num: p.$w.find('[name=num]').val(),
					fecemi: p.$w.find('[name=fecemi]').val(),
					observ: p.$w.find('[name=observ]').val(),
					almacen: {
						_id: p.$w.find('[name=almacen] :selected').val(),
						nomb: p.$w.find('[name=almacen] :selected').text()
					},
					moneda: p.moneda,
					local: '100',//p.local,
					valor_igv: p.igv*100,
					items: [],
					total: parseFloat(p.$w.find('[name=totalGrid]').data('monto'))
				};
				if(data.moneda=='D'){
					data.tc = parseFloat(p.$w.find('[name=tasa]').val());
					data.total_dolares = parseFloat(p.$w.find('[name=totalGrid]').data('monto'));
					data.total_soles = parseFloat(p.$w.find('[name=totalGridConv]').data('monto'));
					data.total = data.total_soles;
					p.tc = p.$w.find('[name=tasa]').val();
				}else{
					data.tc = p.tc;
				}
				data.cliente = mgEnti.dbRel(data.cliente);
				if(data.num==''){
					p.$w.find('[name=num]').focus();
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'Debe ingresar un n&uacute;mero de comprobante!',
						type: 'error'
					});
				}
				data.caja = {
					_id: data.caja._id.$id,
					nomb: data.caja.nomb,
					local: {
						_id: data.caja.local._id.$id,
						descr: data.caja.local.descr,
						direccion: data.caja.local.direccion
					}
				};
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=conc]').length; i<j; i++){
					console.info(p.$w.find('[name=gridServ] tbody [name^=conc]').eq(i).data('data'));
					var $row = p.$w.find('[name=gridServ] tbody [name^=conc]').eq(i),
					item = {
						producto: lgProd.dbRel($row.data('data')),
						monto: $row.find('[name=precio]').val(),
						cant: $row.find('[name=cant]').val(),
						conceptos: []
					};
					if(!parseFloat(item.monto)){
						return K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Se ha encontrado un precio ingresado incorrectamente',
							type: 'error'
						});	
					}
					//PRODUCTO
					var concepto = {
						concepto: $row.find('td:eq(1)').html(),
						monto: parseFloat(K.round(parseFloat(item.monto)/(1+p.igv),2)),
						cuenta: {
							_id: p.cuenta._id.$id,
							descr: p.cuenta.descr,
							cod: p.cuenta.cod
						}
					};
					item.conceptos.push(concepto);
					//IGV
					var concepto = {
						concepto: 'IGV ('+((p.igv)*100)+'%)',
						monto: parseFloat(K.round(item.monto-(parseFloat(item.monto)/(1+p.igv)),2)),
						cuenta: {
							_id: p.conf.IGV._id.$id,
							descr: p.conf.IGV.descr,
							cod: p.conf.IGV.cod
						}
					};
					item.conceptos.push(concepto);
					data.items.push(item);
				}
				if(parseFloat(data.total)==0){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El comprobante no puede tener como total 0!',
						type: 'error'
					});
				}
				var tot = 0;
				tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
				tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())*parseFloat(p.tc);
				data.efectivos = [
				     {
				    	 moneda: 'S',
				    	 monto: parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val())
				     },
				     {
				    	 moneda: 'D',
				    	 monto: parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())
				     }
				];
				for(var i=0,j=p.$w.find('[name=ctban]').length; i<j; i++){
					var tmp = {
						num: p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').val(),
						monto: parseFloat(p.$w.find('[name=ctban]').eq(i).find('[name=tot]').val()),
						moneda: p.$w.find('[name=ctban]').eq(i).data('moneda'),
						cuenta_banco: p.$w.find('[name=ctban]').eq(i).data('data')
					};
					if(tmp.monto>0){
						if(tmp.num==''){
							p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe ingresar un n&uacute;mero de voucher!',
								type: 'error'
							});
						}
						if(data.vouchers==null) data.vouchers = [];
						data.vouchers.push(tmp);
						tot += (tmp.moneda=='S')?tmp.monto:tmp.monto*p.tasa;
					}
				}
				/*if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El total del comprobante no coincide con el total de la forma de pagar!',
						type: 'error'
					});
				}*/
				data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
				data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
				K.sendingInfo();
				p.$w.find('#div_buttons button').attr('disabled','disabled');
				$.post('ag/comp/save_comp',data,function(comp){
					if(comp.error!=null){
						p.$w.find('#div_buttons button').removeAttr('disabled');
						return K.msg({title: ciHelper.titles.infoReq,text: 'El comprobante seleccionado ya existe!',type: 'error'});
					}
					K.clearNoti();
					agVent.init();
					K.msg({title: ciHelper.titles.regiGua,text: 'Comprobante creado con &eacute;xito!'});
					
					
					
					/*
					 * AQUI ABRIR EL PDF
					 */
					/*K.windowPrint({
						id:'windowPrint',
						title: "Comprobante de Pago",
						url: "ag/comp/print?print=1&_id="+comp._id.$id
					});*/
				},'json');
			}
		});
		new K.Panel({
			contentURL: 'ag/comp/venta',
			store: false,
			buttons: {
				'Generar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						ciHelper.confirm('Se descontaran productos del almacen <b>'+p.$w.find('[name=almacen] :selected').text()+'</b>. &#191;desea continuar&#63;',
						function(){
							if(p.total>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
								var detrac = p.total*p.detraccion;
								ciHelper.confirm(
									'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
									function () {
										p.save();
									},
									function () { $.noop(); }
								);
							}else{
								p.save();
							}
						},function(){
							$.noop();
						},'Kardex - Almacen');
					}
				},
				'Limpiar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						agVent.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridServ]'),
					search: false,
					pagination: false,
					cols: ['','Servicios','','Precio unitario'],
					onlyHtml: true,
					toolbarHTML: '<div class="input-group">'+
						'<input type="text" name="prod" class="form-control" placeholder="Ejem: aspir, curi (no necesita escribir el nombre completo del producto)" />'+
							'<span class="input-group-btn">'+
								'<button class="btn btn-info" type="button" name="btnProd"><i class="fa fa-search"></i></button>'+
							'</span>'+
						'</div>',
					onContentLoaded: function($el){
						$el.removeClass('col-md-8');
						$el.find('[name=prod]').unbind('keyup').keyup(function(e){
							if(e.keyCode == 13) p.$w.find('[name=btnProd]').click();
						});
						$el.find('[name=btnProd]').click(function(){
							var texto = p.$w.find('[name=prod]').val();
							p.$w.find('[name=prod]').val('');
							lgProd.windowSelectProducto({
								texto: texto,
								//stock: true,
								modulo:'AG',
								almacen: p.$w.find('[name=almacen] :selected').val(),
								callback: function(data){
									//if(p.$w.find('#'+data._id.$id).length==0){
										data.precio_venta = parseFloat(data.precio_venta);
										var comp_sto = false;
										var stock = 0;
										if(data.stock!=null){
											stock = data.stock.stock;
										}
										//if(comp_sto){
											var $row = $('<tr class="item" name="conc" id="'+data._id.$id+'">');
											$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('button').click(function(){
												$(this).closest('.item').remove();
												p.calcTot();
											});
											$row.append('<td><h3><u>'+data.nomb+'</u></h3> <kbd>'+ciHelper.formatMon(data.precio_venta)+'</kbd></td>');
											$row.append('<td><div class="form-group has-success">'+
													'<input type="text" class="form-control" aria-describedby="helpBlock2" name="cant" value="1" placeholder="0"/>'+
													'<span id="helpBlock2" class="help-block">Stock M&aacute;ximo: '+stock+' '+data.unidad.nomb+'.</span>'+
												'</div>'+
											'</td>');
											$row.find('[name=cant]').keyup(function(e){
												var $this = $(this),
												max = $this.closest('.item').data('stock');
												/*if($this.val()>max){
													$this.val(max);
												}*/
												p.calcTot();
											});
											$row.append('<td><input type="text" name="precio" value="'+K.round(data.precio_venta,2)+'" class="form-control"></td>');
											$row.find('[name=precio]').blur(function(e){
												var $this = $(this);
												if(!parseFloat($this.val())){
													$this.val("0.00");
												}
												p.calcTot();
											});
											$row.data('data',data).data('precio',data.precio_venta).data('stock',stock);
											$row.click(function(){
												var $row = $(this);
												setTimeout(function(){
													$row.removeClass('highlights');
												}, 200);
											});
											p.$w.find('[name=gridServ] tbody').append($row);
											p.$w.find('[name=prod]').focus();
											p.calcTot();
										/*}else{
											K.msg({text: 'Elija un producto con stock!'});
										}*/
									/*}else{
										K.msg({text: 'El producto ya fue seleccionado!',type: 'error'});
									}*/
								}
							});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				$.post('ag/comp/get_var_comp',function(data){
					p.tc = data.tc.valor;
					p.ctban = data.ctban;
					p.cuenta = data.cuenta;
					p.conf = data.conf;
					for(var i=0,j=data.vars.length; i<j; i++){
						if(data.vars[i].cod=='IGV')
							p.igv = (parseFloat(data.vars[i].valor)/100);
						else if(data.vars[i].cod=='MORA')
							p.mora = parseFloat(data.vars[i].valor);
						else if(data.vars[i].cod=='DETRACCION')
							p.detraccion = parseFloat(data.vars[i].valor);
					}
					p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
					p.$w.find('[name=btnSel]').click(function(){
						mgEnti.windowSelect({
							bootstrap: true,
							callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							}
						});
					});
					if(data.almacenes!=null){
						if(data.almacenes.length>0){
							var $cbo_alma = p.$w.find('[name=almacen]');
							for(var i=0;i<data.almacenes.length;i++){
								$cbo_alma.append('<option value="'+data.almacenes[i]._id.$id+'">'+data.almacenes[i].nomb+'</option>');
							}
						}
					}
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),{
						_id: {
							$id: '572b6ade0121122c0900003a'
						},
						nomb: 'CLIENTES VARIOS',
						appat: '',
						apmat: '',
						tipo_enti: 'P',
						docident: [{num: '00000000',tipo: 'DNI'}]
					});
					p.$w.find('[name=btnAct]').click(function(){
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
					p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						K.unblock();
						return K.msg({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
					}
					for(var i=0,j=data.cajas.length; i<j; i++){
						$select.append('<option value="'+data.cajas[i]._id.$id+'">'+data.cajas[i].nomb+'</option>')
						.find('option:last').data('data',data.cajas[i]);
					}
					$select.change(function(){
						$.post('cj/talo/get_caja','caja='+$(this).find('option:selected').val(),function(data){
							var $select = p.$w.find('[name=comp]').data('data',data).empty();
							for(var i=0,j=data.length; i<j; i++){
								if($select.find('[value='+data[i].tipo+']').length<=0)
									$select.append('<option value="'+data[i].tipo+'">'+cjTalo.types[data[i].tipo]+'</option>');
								if($select.find('option').length==3) i = j;
							}
							$select.unbind('change').change(function(){
								var $sel = p.$w.find('[name=serie]').empty(),
								talos = p.$w.find('[name=comp]').data('data'),
								$this = $(this);
								for(var i=0,j=talos.length; i<j; i++){
									if($this.find('option:selected').val()==talos[i].tipo){
										$sel.append('<option value="'+talos[i]._id.$id+'">'+talos[i].serie+'</option>')
											.find('option:last').data('data',talos[i]);
										for(var ii=0; ii<p.conf.series.length; ii++){
											if(talos[i].serie==p.conf.series[ii].serie && talos[i].tipo==p.conf.series[ii].tipo){
												$sel.find('option:last').data('local',p.conf.series[ii].local);
												break;
											}
										}
									}
								}
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
									p.local = $(this).find('option:selected').data('local');
									p.recal();
								}).change();
							}).change();
						},'json');
					}).change();
					var $row = $('<tr class="item info" name="totalGrid">');
					$row.append('<th colspan="3">Total</th>');
					$row.append('<th>');
					p.$w.find('[name=gridServ] table:last').append('<tfoot>');
					p.$w.find('[name=gridServ] tfoot').append($row);
					if(p.moneda=='D'){
						var $row = $('<tr class="item info">');
						$row.append('<th colspan="3">Tasa de Cambio de Soles a D&oacute;lares</th>');
						$row.append('<th><input type="text" name="tasa" size="7" value="0"></th>');
						$row.find('[name=tasa]').val(p.tc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tfoot').append($row);
						var $row = $('<tr class="item info" name="totalGridConv">');
						$row.append('<th colspan="3">Total en Soles</th>');
						$row.append('<th>');
						p.$w.find('[name=gridServ] tfoot').append($row);
					}
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td><div class="input-group col-sm-8">'+
						'<input class="form-control" name="tot" type="text" />'+
					'</div></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Efectivo Dolares*/
					var $row = $('<tr class="item" name="mon_dol">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td><div class="input-group col-sm-8">'+
						'<input class="form-control" name="tot" type="text" />'+
					'</div></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban">');
						$row.append('<td>Voucher '+
							'<input class="form-control" name="voucher" type="text" />'+
						'</td>');
						$row.append('<td>'+p.ctban[i].nomb+'</td>');
						$row.append('<td><div class="input-group col-sm-8">'+
							'<input class="form-control" name="tot" type="text" />'+
						'</div></td>');
						$row.append('<td>S/.0.00</td>');
						$row.find('[name=tot]').val(0).keyup(function(){
							if($(this).val()=='')
								$(this).val(0);
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon(tot));
							$(this).closest('.item').data('total',parseFloat(tot));
						});
						$row.data('moneda',p.ctban[i].moneda).data('data',{
							_id: p.ctban[i]._id.$id,
							cod: p.ctban[i].cod,
							nomb: p.ctban[i].nomb,
							moneda: p.ctban[i].moneda,
							cod_banco: p.ctban[i].cod_banco
						});
						p.$w.find('[name=gridForm] tbody').append($row);
					}
					p.calcTot();
					K.unblock();
					p.$w.find('[name=prod]').focus();
				},'json');
			}
		});
	}
};
define(
	['mg/enti','mg/orga','fa/rein','mg/serv','cj/talo','lg/prod'],
	function(mgEnti,mgOrga,faRein,mgServ,cjTalo,lgProd){
		return agVent;
	}
);