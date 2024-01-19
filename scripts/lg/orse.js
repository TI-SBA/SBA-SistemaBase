lgOrse = {
	states: {
		P: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		A:{
			descr: "Aprobados",
			color: "#CCCCCC",
			label: '<span class="label label-default">Aprobados</span>'
		},
		E:{
			descr: "Enviado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Enviado</span>'
		},
		R:{
			descr: "Recepcionado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Recepcionado</span>'
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
	contextMenu: {
		onShowMenu: function($row, menu) {
			switch($row.data('data').orden.estado){
				case "P":
					//$('#conMenLgOrde_apr',menu).remove();
					$('#conMenLgOrde_env',menu).remove();
					$('#conMenLgOrde_rec',menu).remove();
					break;
				case "A":
					$('#conMenLgOrde_edi',menu).remove();
					$('#conMenLgOrde_apr',menu).remove();
					$('#conMenLgOrde_rec',menu).remove();
					break;
				case "E":
					$('#conMenLgOrde_edi',menu).remove();
					$('#conMenLgOrde_apr',menu).remove();
					$('#conMenLgOrde_env',menu).remove();
					//$('#conMenLgOrde_rec',menu).remove();
					break;
				case "R":
					$('#conMenLgOrde_edi',menu).remove();
					$('#conMenLgOrde_apr',menu).remove();
					$('#conMenLgOrde_env',menu).remove();
					$('#conMenLgOrde_rec',menu).remove();
					break;
			}
			return menu;
		},
		bindings: {
			'conMenLgOrde_ver': function(t) {
				lgOrde.windowDetails({id: K.tmp.data('id'), etapa: 'ORD', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgOrde.init_nue();
				}});
			},
			'conMenLgOrde_edi': function(t) {
				lgOrde.windowNew({id: K.tmp.data('id'), etapa: 'ORD', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgOrde.init_nue();
				}});

			},
			'conMenLgOrde_imp': function(t) {
				window.open('lg/repo/orse?id='+K.tmp.data('data')._id.$id);
			},
		}
	},
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrsn',
			titleBar: {
				title: 'Nuevas Ordenes de Servicio'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Proveedor',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/orse/lista',
					params: {estado: 'P',etapa:'ORD'},
					itemdescr: 'orden(es) de servicio',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nueva Orden de Servicio</button>&nbsp;'+
						'<button name="btnCert" class="btn btn-primary"><i class="fa fa-file-text-o"></i> Crear Nueva Orden de Servicio desde Certificaci&oacute;n Presupuestaria</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgOrse.windowNew();
						});
						$el.find('[name=btnCert]').click(function(){
							lgCert.windowSelect({callback: function(data){
								lgOrse.windowNew({certificacion: data});
							}})
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrse.states[data.orden.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgOrse.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenLgOrde", {
							onShowMenu: function($row, menu) {
								$('#conMenLgOrde_fin,#conMenLgOrde_rev,#conMenLgOrde_con',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgOrde_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgOrde_ver': function(t) {
									lgOrse.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_edi': function(t){
									lgOrse.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_apr': function(t) {
									ciHelper.confirm('&#191;Desea aprobar la orden de Servicio <b>' +K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										fecha = new Date();
										K.sendingInfo();
										$.post('lg/orse/cambiar_estado',{_id: K.tmp.data('id'),'estado': 'A','etapa':'orden',},function(){
											K.clearNoti();
											K.notification({title: 'Orden de Servicio Aprobada',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgOrse.init_apr();
										});
									},function(){
										$.noop();
									},'Orden de Servicio');
								},
								'conMenLgOrde_imp': function(t) {
									window.open('lg/repo/orse?id='+K.tmp.data('data')._id.$id);
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
				p.$w.find("[name=gridPres] tbody").empty();
				for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
					console.log('Entro al FOR de Afectacion Presupuestaria')
					var servi = p.$w.find('[name=gridProd] tbody .item').eq(i).data('data'),
					tmp = p.$w.find('[name=gridProd] tbody .item').eq(i).data('asig');
					console.log(tmp);
					if(tmp!=null){
						console.log('Entro a if de tmp!=null')
						for(var j=0; j<tmp.length; j++){
							console.log('entro a for de j');
							if($.type(tmp[j].organizacion._id)=='object') 
								var _id = tmp[j].organizacion._id.$id;
							else var _id = tmp[j].organizacion._id;
							if(p.$w.find('[name=gridPres] tbody [name='+_id+']').length>0){
								var tot = parseFloat(p.$w.find('[name=gridPres] tbody [name='+_id+'] td:eq(3)').data('monto'));
								console.log(tot);
								p.$w.find('[name=gridPres] tbody [name='+_id+'] td:eq(3)').html(ciHelper.formatMon(tot+parseFloat(tmp[j].monto))).data('monto',tot+parseFloat(tmp[j].monto));
							}else{
								console.log(tmp[j]);
								var $row = $('<tr class="item" name="'+_id+'">');
								$row.append('<td>'+tmp[j].organizacion.nomb+'</td>');
								$row.append('<td>'+tmp[j].organizacion.componente.cod+'</td>');
								$row.append('<td>'+tmp[j].organizacion.actividad.cod+'</td>');
								$row.append('<td>'+ciHelper.formatMon(tmp[j].monto)+'</td>')
								$row.find('td:eq(3)').data('monto',tmp[j].monto);
					        	p.$w.find("[name=gridPres] tbody").append( $row );
					        	p.$w.find('[name=gridPres] tbody [name='+_id+']').data('orga',tmp[j].organizacion).data('gastos',[]);
							}
							var gastos = p.$w.find('[name=gridPres] tbody [name='+_id+']').data('gastos');
							var sec = false;
							for(var k=0; k<gastos.length; k++){
								if(gastos[k].clasif._id.$id==servi.clasif._id.$id){
									gastos[k].monto = parseFloat(gastos[k].monto) + parseFloat(tmp[j].monto);
									sec = true;
								}
							}
							if(sec==false){
								gastos.push({
									clasif: servi.clasif,
									monto: parseFloat(tmp[j].monto)
								});
							}
  						p.$w.find('[name=gridPres] tbody [name='+_id+']').data('gastos',gastos);
						}
					}
				}
				p.$w.find('[name=total]:eq(1)').html(p.$w.find('[name=total]:eq(0)').html());
			}
		});
		new K.Panel({
			title: 'Nueva Orden de Servicio',
			contentURL: 'lg/orse/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.cod,
							ref: p.$w.find('[name=ref]').val(),
							fuente: p.$w.find('[name=fuente] option:selected').data('data'),
							observ: p.$w.find('[name=observ]').val(),
							feceje: p.$w.find('[name=feceje]').val(),
							lugar: p.$w.find('[name=lugar]').val(),
							servicios: [],
							precio_total: 0
						};
						var tmp = p.$w.find('[name=mini_enti]').data('data');
						console.log(tmp);
						if(tmp==null){
							p.$w.find('[name=btnSel]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
						}else{
							data.proveedor = mgEnti.dbRel(tmp);
							data.proveedor.domicilios = tmp.domicilios;
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
								servicio: {
									_id: dat._id.$id,
									nomb: dat.nomb,
									cod: dat.cod,
									unidad: {
										_id: dat.unidad._id.$id,
										nomb: dat.unidad.nomb
									},
									clasif: {
										_id: dat.clasif._id.$id,
										cod: dat.clasif.cod,
										descr: dat.clasif.descr
									}
								},
								subtotal: parseFloat($row.data('total')),
								asignacion: []
							};
							data.precio_total = data.precio_total + prod.subtotal;
							var asigg = $row.data('asig');
							if(asigg==null){
								$row.find('button:first').click();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe especificar una asignaci&oacute;n!',type: 'error'});
							}
							for(var j=0; j<asigg.length; j++){
								var ac = asigg[j];
								asigg[j].organizacion = {
									_id: ac.organizacion._id,
									nomb: ac.organizacion.nomb,
									actividad: {
										_id: ac.organizacion.actividad._id,
										nomb: ac.organizacion.actividad.nomb,
										cod: ac.organizacion.actividad.cod
									},
									componente: {
										_id: ac.organizacion.componente._id,
										nomb: ac.organizacion.componente.nomb,
										cod: ac.organizacion.componente.cod
									}
								};
								if($.type(ac.organizacion._id)=='object') asigg[j].organizacion._id = ac.organizacion._id.$id;
								if($.type(ac.organizacion.actividad._id)=='object') asigg[j].organizacion.actividad._id = ac.organizacion.actividad._id.$id;
								if($.type(ac.organizacion.componente._id)=='object') asigg[j].organizacion.componente._id = ac.organizacion.componente._id.$id;
								prod.asignacion.push(asigg[j]);
							}
							data.servicios.push(prod);
						}
						if(data.fuente==null){
							p.$w.find('[name=fuente]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una fuente!',type: 'error'});
						}else{
							data.fuente._id = data.fuente._id.$id;
						}
						/*var tmp = p.$w.find('[name=almacen]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnAlm]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un almac&eacute;n!',type: 'error'});
						}else{
							data.almacen = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								local: {
									_id: tmp.local._id.$id,
									descr: tmp.local.descr,
									direccion: tmp.local.direccion
								}
							};
						}*/
						if(data.feceje==''){
							p.$w.find('[name=feceje]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha estimada de entrega!',type: 'error'});
						}
						data.afectacion = [];
						for(var i=0; i<p.$w.find('[name=gridPres] tbody .item').length; i++){
							var afect = [];
							var or = p.$w.find('[name=gridPres] tbody .item').eq(i).data('orga');
							var tt = p.$w.find('[name=gridPres] tbody .item').eq(i).data('gastos');
							for(var j=0; j<tt.length; j++){
								afect.push({
									clasif: {
										_id: tt[j].clasif._id.$id,
										cod: tt[j].clasif.cod,
										descr: tt[j].clasif.descr
									},
									monto: tt[j].monto
								});
							}
							var orggg = {
								nomb: or.nomb,
								actividad: {
									_id: or.actividad._id.$id,
									nomb: or.actividad.nomb,
									cod: or.actividad.cod
								},
								componente: {
									_id: or.componente._id.$id,
									nomb: or.componente.nomb,
									cod: or.componente.cod
								}
							};
							if($.type(or._id)=='object') orggg._id = or._id.$id;
							else orggg._id = or._id;
							data.afectacion.push({
								organizacion: orggg,
								gasto: afect
							});
						}
						if(p.id!=null){
							data._id = p.id;
							delete data.cod;
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/orse/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Orden de Servicio agregada!"});
							lgOrse.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgOrse.init();
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

				/*p.$w.find('[name=btnAlm]').click(function(){
					lgAlma.windowSelect({callback: function(data){
						p.$w.find('[name=almacen]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});*/
				p.$w.find('[name^=fec]').datepicker();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Producto','Unidad','Distribuci&oacute;n','SubTotal',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							lgServ.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=gridProd] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
										$row.append('<td><kbd>'+data.clasif.cod+'</kbd><br />'+data.clasif.descr+'</td>');
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><span name="asignar"></span><button class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Asignar Distribuci&oacute;n</button></td>');
										$row.find('button:last').click(function(){
											var $row = $(this).closest('.item'),
											data = $row.data('data'),
											tmp_asig = $row.data('asig');
											lgOrse.windowAsignar({
												precio: $row.data('precio'),
												data: $row.data('asig'),
												prod: data,
												tmp_asig: tmp_asig,
												callback: function(asig,cant,total,precio){console.log(asig);
													//$row.find('[name=cant]').html(cant);
													//$row.find('[name=precio]').html('S/.'+precio);
													$row.find('[name=subtotal]').html('S/.'+total);
													$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
													$row.find('[name=asignar]').empty();
													for(var i=0; i<asig.length; i++){
														var $ul = $('<ul><li>'+asig[i].organizacion.nomb+': S/.'+asig[i].monto+'</li></ul>');
														$row.find('[name=asignar]').append($ul);
													}
													p.calcTot();
													p.calcOrga();
												}
											});
										});
										$row.append('<td><span name="subtotal"></span></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=gridProd] tbody').append($row);
										p.calcTot();
										p.$w.find('[name=gridProd] tbody .item:last button:first').click();
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
						p.$w.find('[name=gridProd] table:last').append('<tfoot>');
						var $row = $('<tr class="item">');
						$row.append('<td colspan="5">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						$row.append('<td>');
						p.$w.find('[name=gridProd] tfoot').append($row);
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridPres]'),
					search: false,
					pagination: false,
					cols: ['Organizaci&oacute;n','Actividad','Componente','Monto'],
					onlyHtml: true,
					onContentLoaded: function($el){
						var $row = $('<tr class="item">');
						$row.append('<td colspan="2">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						p.$w.find('[name=gridPres] tfoot').append($row);
					}
				});
				$.post('lg/orse/edit_data',function(data){
					console.log(data);
					p.cod = data.cod;
					var $select = p.$w.find('[name=fuente]');
					console.log($select);
					for(var k=0,l=data.fuen.length; k<l; k++){
						$select.append('<option value="'+data.fuen[k]._id.$id+'">'+data.fuen[k].cod+' - '+data.fuen[k].rubro+'</option>');
						$select.find('option:last').data('data',data.fuen[k]);
					}
					//SE DEBE AGREGAR UN INDICE MAS
					if(p.certificacion!=null){
						console.log('Certificacion es diferente a null')
						var cuenta_cert = 0;
						for(var i=0; i<p.certificacion.certificacion.productos.length; i++){
							console.log('entro al FOR')
							if(p.certificacion.certificacion.productos[i].tipo=='S'){
								console.log('tipo de producto de certificacion es igual S');
								cuenta_cert++;
								var data = p.certificacion.certificacion.productos[i].servicio,
								$row = $('<tr class="item" name="'+data._id.$id+'">');
								$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
								$row.append('<td><kbd>'+data.clasif.cod+'</kbd><br />'+data.clasif.descr+'</td>');
								$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
								$row.append('<td>'+data.unidad.nomb+'</td>');
								$row.append('<td><span name="asignar"></span><button class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Asignar Distribuci&oacute;n</button></td>');
								$row.find('button:last').click(function(){
									var $row = $(this).closest('.item'),
									data = $row.data('data'),
									tmp_asig = $row.data('asig');
									lgOrse.windowAsignar({
										precio: $row.data('precio'),
										data: $row.data('asig'),
										prod: data,
										tmp_asig: tmp_asig,
										callback: function(asig,cant,total,precio){console.log(asig);
											$row.find('[name=subtotal]').html('S/.'+total);
											$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
											$row.find('[name=asignar]').empty();
											for(var i=0; i<asig.length; i++){
												var $ul = $('<ul><li>'+asig[i].organizacion.nomb+': S/.'+asig[i].monto+'</li></ul>');
												$row.find('[name=asignar]').append($ul);
											}
											p.calcTot();
											p.calcOrga();
										}
									});
								});
								$row.append('<td><span name="subtotal"></span></td>');
								$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('button:last').click(function(){
									$(this).closest('.item').remove();
									for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
										p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
									}
								});
								$row.data('data',data);
								$row.find('[name=cant]').html(p.certificacion.certificacion.productos[i].cant);
								$row.find('[name=precio]').html('S/.'+(parseFloat(p.certificacion.certificacion.productos[i].subtotal)/parseFloat(p.certificacion.certificacion.productos[i].cant)));
								$row.find('[name=subtotal]').html('S/.'+parseFloat(p.certificacion.certificacion.productos[i].subtotal));
								console.log('subtotal');
								console.log(p);
								$row.data('asig',[{
									monto: K.round(parseFloat(p.certificacion.certificacion.productos[i].subtotal),4),
									organizacion: p.certificacion.certificacion.organizacion,
									cantidad: p.certificacion.certificacion.productos[i].cant
								}]).data('total',parseFloat(p.certificacion.certificacion.productos[i].subtotal)).data('cant',p.certificacion.certificacion.productos[i].cant)
									.data('precio',(parseFloat(p.certificacion.certificacion.productos[i].subtotal)/parseFloat(p.certificacion.certificacion.productos[i].cant)));
								$row.find('[name=asignar]').empty();
								var $ul = $('<ul><li>'+p.certificacion.certificacion.organizacion.nomb+': cant. '+p.certificacion.certificacion.productos[i].cant+' S/.'+p.certificacion.certificacion.productos[i].subtotal+'</li></ul>');
								$row.find('[name=asignar]').append($ul);
								p.$w.find('[name=gridProd] tbody').append($row);
							}
						}
						p.calcTot();
						p.calcOrga();
						if(cuenta_cert==0){
							console.log('no entro');
							K.msg({title: ciHelper.titles.infoReq,text: 'No se han encontrado servicios en la certificacion presupuestaria seleccionada!',type: 'error'});
						}
					}
					/*p.$w.find('[name=almacen]').html('Almacén Central').data('data',{
						_id: {'$id':'51a79e6a4d4a13280700003e'},
						nomb: 'Almacén Central',
						descr: 'Almacén para útiles, viveres secos y otros',
						local: {
							_id: {'$id': '519d35d29c7684f0050000c2'},
							direccion: 'Av. Piérola 201 - Arequipa, Arequipa',
							descr: 'Administración Central'
						}
					});*/
					if(p.id!=null){
						$.post('lg/orse/get',{_id: p.id},function(data){
							console.log(data);
							p.$w.find('[name=ref]').val(data.ref);
							p.$w.find('[name=fuente]').selectVal(data.fuente._id.$id);
							p.$w.find('[name=observ]').val(data.observ);
							p.$w.find('[name=feceje]').val(ciHelper.date.format.bd_ymd(data.feceje));
							p.$w.find('[name=lugar]').val(data.lugar);
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
							for(var i=0; i<data.servicios.length; i++){
								var data_prod = data.servicios[i].servicio,
								$row = $('<tr class="item" name="'+data_prod._id.$id+'">');
								$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
								$row.append('<td><kbd>'+data_prod.clasif.cod+'</kbd><br />'+data_prod.clasif.descr+'</td>');
								$row.append('<td><kbd>'+data_prod.cod+'</kbd><br />'+data_prod.nomb+'</td>');
								$row.append('<td>'+data_prod.unidad.nomb+'</td>');
								$row.append('<td><span name="asignar"></span><button class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Asignar Distribuci&oacute;n</button></td>');
								$row.find('button:last').click(function(){
									var $row = $(this).closest('.item'),
									data = $row.data('data'),
									tmp_asig = $row.data('asig');
									lgOrse.windowAsignar({
										precio: $row.data('precio'),
										data: $row.data('asig'),
										prod: data,
										tmp_asig: tmp_asig,
										callback: function(asig,cant,total,precio){console.log(asig);
											$row.find('[name=subtotal]').html('S/.'+total);
											$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
											$row.find('[name=asignar]').empty();
											for(var i=0; i<asig.length; i++){
												var $ul = $('<ul><li>'+asig[i].organizacion.nomb+': S/.'+asig[i].monto+'</li></ul>');
												$row.find('[name=asignar]').append($ul);
											}
											p.calcTot();
											p.calcOrga();
										}
									});
								});
								$row.append('<td><span name="subtotal"></span></td>');
								$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('button:last').click(function(){
									$(this).closest('.item').remove();
									for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
										p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
									}
								});
								$row.data('data',data_prod);
								$row.find('[name=subtotal]').html('S/.'+parseFloat(data.servicios[i].subtotal));
								$row.data('asig',data.servicios[i].asignacion).data('total',parseFloat(data.servicios[i].subtotal)).data('cant',data.servicios[i].cant)
									.data('precio',parseFloat(data.servicios[i].subtotal));
								$row.find('[name=asignar]').empty();
								for(var ii=0; ii<data.servicios[i].asignacion.length; ii++){
									var $ul = $('<ul><li>'+data.servicios[i].asignacion[ii].organizacion.nomb+': S/.'+data.servicios[i].subtotal+'</li></ul>');
									$row.find('[name=asignar]').append($ul);
								}
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
	windowAsignar: function(p){
		$.extend(p,{
			calcTot: function(){
				/*var precio = K.round(parseFloat(p.$w.find('[name=precio]').val()),4);
				p.$w.find('[name=subtotal]').html(K.round(precio,2));*/
				var total = 0;
				for(var i=0;i<p.$w.find('[name=subtotal]').length;i++){
					total+=parseFloat(p.$w.find('[name=subtotal]').eq(i).val());
				}
				p.$w.find('[name=total]').html(K.round(total,2));
				/*p.$w.find('[name=precio]').val(precio);*/
				/*var cant = 1;
				
				p.$w.find('[name^=total]:first').html(cant);
				p.$w.find('[name=total]:last').html(K.round(cant*precio,4));*/
			}
		});
		if(p.view==null){
			var buttons = {
				'Actualizar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var cant_tot = 0,
						total = 0,
						data = [],
						$items = p.$w.find('[name=grid] tbody .item');
						for(var i=0; i<$items.length; i++){
							var precio = parseFloat($items.eq(i).find('[name=subtotal]').val());
							var orga = $items.eq(i).data('data'),
							cant = parseFloat($items.eq(i).find('[name=cant]').val());
							if(orga==null){
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar al menos una organizaci&oacute;n!',type: 'error'});
							}else if($items.eq(i).find('[name=cant]').val()==''||parseFloat(cant)==0){
								$items.eq(i).find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
							}
							total+=precio;
							data.push({
								monto: K.round(precio,4),
								organizacion: orga,
								cantidad: cant
							});
						}
						p.callback(data,1,total,total);
						K.closeWindow(p.$w.attr('id'));
					}
				},
				'Cancelar': {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			};
		}else{
			var buttons = {
				'Cerrar': {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			}
		}
		new K.Modal({
			id: 'windowAsig',
			title: 'Asignaci&oacute;n del gasto',
			contentURL: 'lg/orse/asig',
			height: 450,
			width: 850,
			buttons: buttons,
			onContentLoaded: function(){
				p.$w = $('#windowAsig');
				p.$w.find('[name=producto]').html(p.prod.nomb);
				p.$w.find('[name=unidad]').html(p.prod.unidad.nomb);
				p.$w.find('[name=precio]').val(p.prod.precio);
				p.$w.find('[name=precio]').keyup(function(){
					p.calcTot();
				});
				var cols = ['N&deg;','Organizaci&oacute;n','SubTotal'];
				if(p.view==null) cols.push('');
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					cols: cols,
					pagination: false,
					search: false,
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-sitemap"></i> Agregar Organizaci&oacute;n</button>',
					onContentLoaded: function($el){
						if(p.view==null){
							$el.find('button').click(function(){
								mgOrga.windowSelect({
									callback: function(data){
										if(data.actividad!=null){
											if(data.componente!=null){
												var $row = $('<tr class="item">');
												$row.append('<td>'+(p.$w.find('[name=grid] tbody .item').length+1)+'</td>');
												$row.append('<td>'+data.nomb+'</td>');
												$row.append('<td><input type="text" name="subtotal" class="form-control"></td>');
												if(p.view==null){
													$row.find('[name=subtotal]').keyup(function(){
														p.calcTot();
													});
													$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
													$row.find('button').click(function(){
														$(this).closest('.item').remove();
														for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
															p.$w.find('[name=grid] tbody .item').eq(i).find('td:eq(0)').html(i+1);
														}
													});
												}
												$row.data('data',data);
												p.$w.find('[name=grid] tbody').append($row);
											}else{
												K.msg({type:'error',title: ciHelper.titles.regiAct,text: 'La organizacion seleccionada no tiene relacionado un componente!'});
											}
										}else{
											K.msg({type:'error',title: ciHelper.titles.regiAct,text: 'La organizacion seleccionada no tiene relacionado una actividad!'});
										}
									}
								});
							});
						}else{
							$el.find('button').remove();
						}
					}
				});
				p.$w.find('[name=grid] table:last').append('<tfoot>');
				var $row = $('<tr class="item">');
				$row.append('<td>');
				$row.append('<td>');
				$row.append('<td><span name="total"></span></td>');
				if(p.view==null){
					$row.append('<td>');
				}
				p.$w.find('[name=grid] tfoot').append($row);
				if(p.precio!=null) p.$w.find('[name=precio]').val(p.precio);
				if(p.tmp_asig!=null){
					for(var i=0; i<p.tmp_asig.length; i++){
						var $row = $('<tr class="item">'),
						data = p.tmp_asig[i];
						$row.append('<td>'+(p.$w.find('[name=grid] tbody .item').length+1)+'</td>');
						$row.append('<td>'+data.organizacion.nomb+'</td>');
						$row.append('<td><input type="text" name="subtotal" class="form-control" value="'+data.monto+'"></td>');
						$row.find('[name=subtotal]').keyup(function(){
							p.calcTot();
						});
						if(p.view==null){
							$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
									p.$w.find('[name=grid] tbody .item').eq(i).find('td:eq(0)').html(i+1);
								}
							});
						}
						$row.data('data',data.organizacion);
						p.$w.find('[name=grid] tbody').append($row);
					}
				}
			}
		});
	},
	init_apr: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrse_apr',
			titleBar: {
				title: 'Ordenes de Servicio Aprobadas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Proveedor',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/orse/lista',
					params: {estado:'A',etapa:'ORD'},
					itemdescr: 'orden(es) de servicio',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nueva Orden de Servicio</button>&nbsp;'+
						'<button name="btnCert" class="btn btn-primary"><i class="fa fa-file-text-o"></i> Crear Nueva Orden de Servicio desde Certificaci&oacute;n Presupuestaria</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgOrse.windowNew();
						});
						$el.find('[name=btnCert]').click(function(){
							lgCert.windowSelect({callback: function(data){
								lgOrse.windowNew({certificacion: data});
							}})
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrse.states[data.orden.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgOrse.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenLgOrde", {
							onShowMenu: function($row, menu) {
								$('#conMenLgOrde_fin,#conMenLgOrde_rev,#conMenLgOrde_con',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgOrde_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgOrde_ver': function(t) {
									lgOrse.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_edi': function(t){
									lgOrse.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_env': function(t) {
									ciHelper.confirm('&#191;Desea Enviar la orden de Servicio <b>' +K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										fecha = new Date();
										K.sendingInfo();
										$.post('lg/orse/cambiar_estado',{_id: K.tmp.data('id'),'estado': 'E','etapa':'orden',},function(){
											K.clearNoti();
											K.notification({title: 'Orden de Servicio Enviada',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgOrse.init_env();
										});
									},function(){
										$.noop();
									},'Orden de Servicio');
								},
								'conMenLgOrde_imp': function(t) {
									window.open('lg/repo/orse?id='+K.tmp.data('data')._id.$id);
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
	init_env: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrse_env',
			titleBar: {
				title: 'Ordenes de Servicio Enviadas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Proveedor',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/orse/lista',
					params: {estado: 'E',etapa:'ORD'},
					itemdescr: 'orden(es) de servicio',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nueva Orden de Servicio</button>&nbsp;'+
						'<button name="btnCert" class="btn btn-primary"><i class="fa fa-file-text-o"></i> Crear Nueva Orden de Servicio desde Certificaci&oacute;n Presupuestaria</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgOrse.windowNew();
						});
						$el.find('[name=btnCert]').click(function(){
							lgCert.windowSelect({callback: function(data){
								lgOrse.windowNew({certificacion: data});
							}})
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrse.states[data.orden.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgOrse.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenLgOrde", {
							onShowMenu: function($row, menu) {
								$('#conMenLgOrde_fin,#conMenLgOrde_rev,#conMenLgOrde_con',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgOrde_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgOrde_ver': function(t) {
									lgOrse.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_edi': function(t){
									lgOrse.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_rec': function(t) {
									ciHelper.confirm('&#191;Desea Recepcionar la orden de Servicio <b>' +K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										fecha = new Date();
										K.sendingInfo();
										$.post('lg/orse/cambiar_estado',{_id: K.tmp.data('id'),'estado': 'R','etapa':'orden',},function(){
											K.clearNoti();
											K.notification({title: 'Orden de Servicio Enviada',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgOrse.init_rec();
										});
									},function(){
										$.noop();
									},'Orden de Servicio');
								},
								'conMenLgOrde_imp': function(t) {
									window.open('lg/repo/orse?id='+K.tmp.data('data')._id.$id);
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
	init_rec: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrse_rec',
			titleBar: {
				title: 'Ordenes de Servicio Recepcionadas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Cod',f:'cod'},'Proveedor',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/orse/lista',
					params: {estado:'R',etapa:'ORD'},
					itemdescr: 'orden(es) de servicio',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nueva Orden de Servicio</button>&nbsp;'+
						'<button name="btnCert" class="btn btn-primary"><i class="fa fa-file-text-o"></i> Crear Nueva Orden de Servicio desde Certificaci&oacute;n Presupuestaria</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgOrse.windowNew();
						});
						$el.find('[name=btnCert]').click(function(){
							lgCert.windowSelect({callback: function(data){
								lgOrse.windowNew({certificacion: data});
							}})
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrse.states[data.orden.estado].label+'</td>');
						$row.append('<td>N&deg;'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							lgOrse.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenLgOrde", {
							onShowMenu: function($row, menu) {
								$('#conMenLgOrde_fin,#conMenLgOrde_rev,#conMenLgOrde_con',menu).remove();
								if($row.data('data').estado!='P'){
									$('#conMenLgOrde_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenLgOrde_ver': function(t) {
									lgOrse.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_edi': function(t){
									lgOrse.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgOrde_imp': function(t) {
									window.open('lg/repo/orse?id='+K.tmp.data('data')._id.$id);
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
};
define(
	['mg/enti','ct/pcon','lg/alma','lg/prod','lg/cert'],
	function(mgEnti,ctPcon,lgAlma,lgProd,lgCert){
		return lgOrse;
	}
);