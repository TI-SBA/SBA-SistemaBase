lgOrde = {
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
			/*$('#conMenLgCert_rev,#conMenLgCert_fin',menu).remove();
			if($row.data('data').estado!='P'){
				$('#conMenLgCert_edi',menu).remove();
			}*/
			//$('#conMenLgSoli_rec',menu).remove();
			//$('#conMenLgSoli_apr',menu).remove();
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
			'conMenLgOrde_env': function(t) {
				ciHelper.confirm('&#191;Desea enviar la orden de compra <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'orden',
						'estado':'E'
					},function(data){
						lgCert.init_env();
					},'json');
				},function(){
					$.noop();
				},'Enviar Orden de compra');
			},
			'conMenLgOrde_apr': function(t) {
				ciHelper.confirm('&#191;Desea aprobar la orden de compra <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'orden',
						'estado':'A'
					},function(data){
						lgOrde.init_apr();
					},'json');
				},function(){
					$.noop();
				},'Aprobar Orden de compra');
			},
			'conMenLgOrde_rec': function(t) {
				ciHelper.confirm('&#191;Desea recepcionar la orden de compra <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'orden',
						'estado':'R'
					},function(data){
						lgOrde.init_rec();
					},'json');
				},function(){
					$.noop();
				},'Recepcionar Orden de compra');
			},
			'conMenLgOrde_imp': function(t) {
				window.open('lg/repo/orde?id='+K.tmp.data('data')._id.$id);
			}
		}
	},
	init_nue: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrde_nue',
			titleBar: {
				title: 'Ordenes de compra: Nuevas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Certificacion','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'P',etapa:'ORD'},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '<button class="btn btn-success" name="btnAgregar">Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgCert.windowSelect({callback:function(cert){
								lgOrde.windowNew({id: cert._id.$id, reg_etapa:'ORD', etapa: 'CER', nomb: cert.certificacion.cod,goBack: function(){
									lgOrde.init_nue();
								}, padre:{
									_id:cert._id.$id,
									solicitud:cert.solicitud.cod,
									certificacion:cert.certificacion.cod
								}});
							}});
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrde.states[data.orden.estado].label+'</td>');
						$row.append('<td>'+data.orden.cod+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						var afectacion = '--';
						if(data.certificacion.afectacion!=null){
							if(data.certificacion.afectacion.length>0){
								if(data.certificacion.afectacion.length==1){
									afectacion = data.certificacion.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgOrde", lgOrde.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_apr: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrde_apr',
			titleBar: {
				title: 'Ordenes de compra: Aprobados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Certificacion','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'A',etapa:'ORD'},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrde.states[data.orden.estado].label+'</td>');
						$row.append('<td>'+data.orden.cod+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						var afectacion = '';
						if(data.certificacion.afectacion!=null){
							if(data.certificacion.afectacion.length>0){
								if(data.certificacion.afectacion.length==1){
									afectacion = data.certificacion.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgOrde", lgOrde.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_env: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrde_env',
			titleBar: {
				title: 'Ordenes de compra: Enviados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Certificacion','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'E',etapa:'ORD'},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrde.states[data.orden.estado].label+'</td>');
						$row.append('<td>'+data.orden.cod+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						var afectacion = '';
						if(data.certificacion.afectacion!=null){
							if(data.certificacion.afectacion.length>0){
								if(data.certificacion.afectacion.length==1){
									afectacion = data.certificacion.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgOrde", lgOrde.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_rec: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgOrde_rec',
			titleBar: {
				title: 'Ordenes de compra: Recepcionados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Certificacion','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'R',etapa:'ORD'},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrde.states[data.orden.estado].label+'</td>');
						$row.append('<td>'+data.orden.cod+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						var afectacion = '';
						if(data.certificacion.afectacion!=null){
							if(data.certificacion.afectacion.length>0){
								if(data.certificacion.afectacion.length==1){
									afectacion = data.certificacion.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgOrde", lgOrde.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
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
				p.$w.find("[name=gridPres] tbody").empty();
				for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
					var servi = p.$w.find('[name=gridProd] tbody .item').eq(i).data('data'),
					clasif = p.$w.find('[name=gridProd] tbody .item').eq(i).data('gasto'),
					tmp = p.$w.find('[name=gridProd] tbody .item').eq(i).data('asig');
					console.log(servi);
					if(tmp!=null){
						for(var j=0; j<tmp.length; j++){
							if($.type(tmp[j].programa._id)=='object') var _id = tmp[j].programa._id.$id;
							else var _id = tmp[j].programa._id;
							if(p.$w.find('[name=gridPres] tbody [name='+_id+']').length>0){
								var tot = parseFloat(p.$w.find('[name=gridPres] tbody [name='+_id+'] td:eq(3)').data('monto'));
								p.$w.find('[name=gridPres] tbody [name='+_id+'] td:eq(3)').html(ciHelper.formatMon(tot+parseFloat(tmp[j].monto))).data('monto',tot+parseFloat(tmp[j].monto));
							}else{
								console.log(tmp[j]);
								var $row = $('<tr class="item" name="'+_id+'">');
								$row.append('<td>'+tmp[j].programa.nomb+'</td>');
								$row.append('<td>'+tmp[j].programa.actividad.cod+'</td>');
								$row.append('<td>'+tmp[j].programa.componente.cod+'</td>');
								$row.append('<td>'+ciHelper.formatMon(tmp[j].monto)+'</td>')
								$row.find('td:eq(3)').data('monto',tmp[j].monto);
					        	p.$w.find("[name=gridPres] tbody").append( $row );
					        	p.$w.find('[name=gridPres] tbody [name='+_id+']').data('orga',tmp[j].programa).data('gastos',[]);
							}

							var gastos = p.$w.find('[name=gridPres] tbody [name='+_id+']').data('gastos');
							var sec = false;
							for(var k=0; k<gastos.length; k++){
								if(gastos[k].clasif._id.$id==clasif._id.$id){
									gastos[k].monto = parseFloat(gastos[k].monto) + parseFloat(tmp[j].monto);
									sec = true;
								}
							}
							if(sec==false){
								gastos.push({
									clasif: clasif,
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
			title: 'Nueva Orden de Compra',
			contentURL: 'lg/orde/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						if(p.id!=null){
							//data = p._data;
							var data = {};
						}else{
							var data = {};
						}
						data.etapa = p.reg_etapa;
						data.nueva_etapa = p.nueva_etapa;
						data.observ = p.$w.find('[name=observ]').val();
						data.fecent = p.$w.find('[name=fecent]').val();
						data.cotizacion = p.$w.find('[name=cotizacion]').data('data');
						data.solicitud_observ = p.$w.find('[name=solicitud_observ]').val();
						data.certificacion_observ = p.$w.find('[name=certificacion_observ]').val();
						data.orden_observ = p.$w.find('[name=orden_observ]').val();
						data.orden_servicio_observ = p.$w.find('[name=orden_servicio_observ]').val();
						data.recepcion_observ = p.$w.find('[name=recepcion_observ]').val();
						delete data.padre;
						delete data.trabajador;
						delete data.autor;
						if(p.padre!=null){
							data.padre = p.padre;
						}
						if(data.productos==null) data.productos = [];
						
						if(p.$w.find('[name=talonario] :selected').val()!=null){
							data.talonario = p.$w.find('[name=talonario] :selected').val();
						}
						if(data.cotizacion==null){
							p.$w.find('[name=btnCot]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar la cotizacion de referencia!',type: 'error'});
						}
						var precio_total = 0;
						var tmp = p.$w.find('[name=mini_enti]').data('data');
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
						
						/*if(data.fuente==null){
							p.$w.find('[name=fuente]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una fuente!',type: 'error'});
						}else{
							data.fuente._id = data.fuente._id.$id;
						}*/
						if(data.fecent==''){
							p.$w.find('[name=fecent]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha estimada de entrega!',type: 'error'});
						}
						//data.afectacion = [];
						var afectacion = [];
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
								actividad:{
									cod:or.actividad.cod
								},
								componente:{
									cod:or.componente.cod
								}
							};
							if($.type(or._id)=='object') orggg._id = or._id.$id;
							else orggg._id = or._id;
							afectacion.push({
								programa: orggg,
								gasto: afect
							});
						}
						var _productos = [];
						for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridProd] tbody .item').eq(i),
							dat = $row.data('data');
							if(dat==null){
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar al menos un producto!',type: 'error'});
							}
							var prod = {
								item: i+1,
								/*producto: {
									_id: dat._id.$id,
									nomb: dat.nomb,
									cod: dat.cod,
									unidad: {
										_id: dat.unidad._id.$id,
										nomb: dat.unidad.nomb
									}
								},*/
								precio: parseFloat($row.data('precio')),
								cant: parseFloat($row.data('cant')),
								subtotal: parseFloat($row.data('total')),
								asignacion: []
							};
							precio_total+=prod.subtotal;
							if($row.data('producto')!=null){
								prod.producto = {
									_id: $row.data('producto')._id.$id,
									nomb: $row.data('producto').nomb,
									cod: $row.data('producto').cod,
									unidad: {
										_id: $row.data('producto').unidad._id.$id,
										nomb: $row.data('producto').unidad.nomb
									}
								};
								if($row.data('gasto')!=null){
									prod.producto.clasif = {
										_id: $row.data('gasto')._id.$id,
										cod: $row.data('gasto').cod,
										descr: $row.data('gasto').descr
									}
								}
								if($row.data('cuenta')!=null){
									prod.producto.cuenta = {
										_id: $row.data('cuenta')._id.$id,
										cod: $row.data('cuenta').cod,
										descr: $row.data('cuenta').descr
									}
								}
							}
							if($row.data('servicio')!=null){
								prod.servicio = {
									_id: $row.data('servicio')._id.$id,
									nomb: $row.data('servicio').nomb,
									cod: $row.data('servicio').cod,
									unidad: {
										_id: $row.data('servicio').unidad._id.$id,
										nomb: $row.data('servicio').unidad.nomb
									}
								};
								if($row.data('gasto')!=null){
									prod.servicio.clasif = {
										_id: $row.data('gasto')._id.$id,
										cod: $row.data('gasto').cod,
										descr: $row.data('gasto').descr
									}
								}
								if($row.data('cuenta')!=null){
									prod.servicio.cuenta = {
										_id: $row.data('cuenta')._id.$id,
										cod: $row.data('cuenta').cod,
										descr: $row.data('cuenta').descr
									}
								}
							}

							if($row.data('asig_edit')!=null) prod.asignacion = $row.data('asig_edit');
							var asigg = $row.data('asig');
							if(asigg==null){
								$row.find('[name=btnAsig]').click();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe especificar una asignaci&oacute;n!',type: 'error'});
							}
							for(var j=0; j<asigg.length; j++){
								var ac = asigg[j];
								asigg[j].programa = {
									_id: ac.programa._id,
									nomb: ac.programa.nomb,
									actividad:{
										cod: ac.programa.actividad.cod
									},
									componente:{
										cod: ac.programa.componente.cod
									}
								};
								if($.type(ac.programa._id)=='object') asigg[j].programa._id = ac.programa._id.$id;
								prod.asignacion.push(asigg[j]);
							}
							_productos.push(prod);
						}
						switch(p.reg_etapa){
							case "SOL":
								if(p._data!=null){
									if(p._data.solicitud!=null) data.solicitud = p._data.solicitud;
									else data.solicitud = {};
								}else{
									if(data.solicitud==null) data.solicitud = {};
								}
								//if(data.solicitud==null) data.solicitud = {};
								data.solicitud.estado = 'P';
								data.solicitud.afectacion = afectacion;
								data.solicitud.precio_total = precio_total;
								data.solicitud.productos = _productos;
								break;
							case "CER":
								if(p._data!=null){
									if(p._data.certificacion!=null) data.certificacion = p._data.certificacion;
									else data.certificacion = {};
								}
								//if(data.certificacion==null) data.certificacion = {};
								data.certificacion.estado = 'P';
								data.certificacion.afectacion = afectacion;
								data.certificacion.precio_total = precio_total;
								data.certificacion.productos = _productos;
								var fuente = p.$w.find('[name=fuente] :selected').data('data');
								if(fuente==null){
									//p.$w.find('[name=fuente]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una fuente!',type: 'error'});
								}else{
									fuente._id = fuente._id.$id;
									data.fuente = fuente;
								}
								break;
							case "ORD":
								if(p._data!=null){
									if(p._data.orden!=null) data.orden = p._data.orden;
									else data.orden = {};
								}
								//if(data.orden==null) data.orden = {};
								data.orden.estado = 'P';
								data.orden.afectacion = afectacion;
								data.orden.precio_total = precio_total;
								data.orden.productos = _productos;
								var tmp = p.$w.find('[name=almacen]').data('data');
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
								}
								break;
							case "ORS":
								if(p._data!=null){
									if(p._data.orden_servicio!=null) data.orden_servicio = p._data.orden_servicio;
									else data.orden_servicio = {};
								}
								//if(data.orden_servicio==null) data.orden_servicio = {};
								data.orden_servicio.estado = 'P';
								data.orden_servicio.afectacion = afectacion;
								data.orden_servicio.precio_total = precio_total;
								data.orden_servicio.productos = _productos;
								break;
							case "REC":
								if(p._data!=null){
									if(p._data.recepcion!=null) data.recepcion = p._data.recepcion;
									else data.recepcion = {};
								}
								//if(data.recepcion==null) data.recepcion = {};
								data.recepcion.estado = 'P';
								data.recepcion.afectacion = afectacion;
								data.recepcion.precio_total = precio_total;
								data.recepcion.productos = _productos;
								break;
						}
						if(p.id!=null){
							data._id = p.id;
							delete data.cod;
						}



						//console.log(data);
						//return false;
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/orde/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Orden de Compra agregada!"});
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
				p.$w.find('[name=btnCot]').click(function(){
					lgCoti.windowSelect({callback:function(data){
						//p.$w.find('[name=gridProd]').empty();
						if(data.propuesta!=null){
							for(var i=0;i<data.propuesta.length;i++){
								if(data.propuesta[i].calificacion!=null){
									if(data.propuesta[i].calificacion.ganador==true){
										p.$w.find('[name=cotizacion]').html(data.cod).data('data', lgCoti.dbRel(data));
										p.$w.find('[name=gridProd] tbody').empty();
										if(data.propuesta[i].productos!=null){
											mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.propuesta[i].proveedor);
											for(var j=0;j<data.propuesta[i].productos.length;j++){
												var $row = $('<tr class="item" name="'+data._id.$id+'">');
												$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
												if(data.propuesta[i].productos[j].producto.clasif!=null){
													$row.append('<td><span name="clasificador">'+data.propuesta[i].productos[j].producto.clasif.cod+'</span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}else{
													$row.append('<td><span name="clasificador"></span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}

												if(data.propuesta[i].productos[j].producto.cuenta!=null){
													$row.append('<td><span name="cuenta">'+data.propuesta[i].productos[j].producto.cuenta.cod+'</span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}else{
													$row.append('<td><span name="cuenta"></span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}
												$row.append('<td><kbd>'+data.propuesta[i].productos[j].producto.cod+'</kbd><br />'+data.propuesta[i].productos[j].producto.nomb+'</td>');
												$row.append('<td>'+data.propuesta[i].productos[j].producto.unidad.nomb+'</td>');
												$row.append('<td><span name="cant"></span></td>');
												$row.append('<td><span name="precio">'+data.propuesta[i].productos[j].precio_unit+'</span></td>');
												$row.append('<td><span name="asignar"></span><button name="btnAsig" class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Asignar Distribuci&oacute;n</button></td>');
												$row.find('button:last').click(function(){
													var $row = $(this).closest('.item'),
													data = $row.data('data'),
													tmp_asig = $row.data('asig');
													if($row.data('gasto')!=null){
														lgOrde.windowAsignar({
															precio: $row.data('precio'),
															data: $row.data('asig'),
															prod: data,
															tmp_asig: tmp_asig,
															callback: function(asig,cant,total,precio){
																$row.find('[name=cant]').html(cant);
																$row.find('[name=precio]').html('S/.'+precio);
																$row.find('[name=subtotal]').html('S/.'+total);
																$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
																$row.find('[name=asignar]').empty();
																var $ul = $('<ul />');
																for(var i=0; i<asig.length; i++){
																	$ul.append('<li>'+asig[i].programa.nomb+': cant. '+asig[i].cantidad+' S/.'+asig[i].monto+'</li>');
																}
																$row.find('[name=asignar]').append($ul);
																p.calcTot();
																p.calcOrga();
															}
														});
													}else{
														return K.msg({title: ciHelper.titles.infoReq,text: 'Debe asignar un clasificador de gasto al producto antes de asignar su distribucion.!',type: 'error'});
													}
												});
												$row.append('<td><span name="subtotal"></span></td>');
												$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
												$row.find('button:last').click(function(){
													$(this).closest('.item').remove();
													for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
														p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
													}
												});
												$row.data('producto',data.propuesta[i].productos[j].producto)
													.data('data',data.propuesta[i].productos[j].producto)
													.data('precio',data.propuesta[i].productos[j].precio_unit)
													.data('gasto',data.propuesta[i].productos[j].producto.clasif)
													.data('cuenta',data.propuesta[i].productos[j].producto.cuenta);
												p.$w.find('[name=gridProd] tbody').append($row);
												p.calcTot();
											}
										}

										if(data.propuesta[i].servicios!=null){
											mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.propuesta[i].proveedor);
											for(var j=0;j<data.propuesta[i].servicios.length;j++){
												var $row = $('<tr class="item" name="'+data._id.$id+'">');
												$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
												if(data.propuesta[i].servicios[j].servicio.clasif!=null){
													$row.append('<td><span name="clasificador">'+data.propuesta[i].servicios[j].servicio.clasif.cod+'</span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}else{
													$row.append('<td><span name="clasificador"></span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}
												if(data.propuesta[i].servicios[j].servicio.cuenta!=null){
													$row.append('<td><span name="cuenta">'+data.propuesta[i].servicios[j].servicio.cuenta.cod+'</span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}else{
													$row.append('<td><span name="cuenta"></span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
												}
												$row.append('<td><kbd>'+data.propuesta[i].servicios[j].servicio.cod+'</kbd><br />'+data.propuesta[i].servicios[j].servicio.nomb+'</td>');
												$row.append('<td>'+data.propuesta[i].servicios[j].servicio.unidad.nomb+'</td>');
												$row.append('<td><span name="cant"></span></td>');
												$row.append('<td><span name="precio">'+data.propuesta[i].servicios[j].precio_unit+'</span></td>');
												$row.append('<td><span name="asignar"></span><button name="btnAsig" class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Asignar Distribuci&oacute;n</button></td>');
												$row.find('button:last').click(function(){
													var $row = $(this).closest('.item'),
													data = $row.data('data'),
													tmp_asig = $row.data('asig');
													if($row.data('gasto')!=null){
														lgOrde.windowAsignar({
															precio: $row.data('precio'),
															data: $row.data('asig'),
															prod: data,
															tmp_asig: tmp_asig,
															callback: function(asig,cant,total,precio){
																$row.find('[name=cant]').html(cant);
																$row.find('[name=precio]').html('S/.'+precio);
																$row.find('[name=subtotal]').html('S/.'+total);
																$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
																$row.find('[name=asignar]').empty();
																var $ul = $('<ul />');
																for(var i=0; i<asig.length; i++){
																	$ul.append('<li>'+asig[i].programa.nomb+': cant. '+asig[i].cantidad+' S/.'+asig[i].monto+'</li>');
																}
																$row.find('[name=asignar]').append($ul);
																p.calcTot();
																p.calcOrga();
															}
														});
													}else{
														return K.msg({title: ciHelper.titles.infoReq,text: 'Debe asignar un clasificador de gasto al producto antes de asignar su distribucion.!',type: 'error'});
													}
												});
												$row.append('<td><span name="subtotal"></span></td>');
												$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
												$row.find('button:last').click(function(){
													$(this).closest('.item').remove();
													for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
														p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
													}
												});
												$row.data('servicio',data.propuesta[i].servicios[j].servicio)
													.data('data',data.propuesta[i].servicios[j].servicio)
													.data('precio',data.propuesta[i].servicios[j].precio_unit)
													.data('gasto',data.propuesta[i].servicios[j].servicio.clasif)
													.data('cuenta',data.propuesta[i].servicios[j].servicio.cuenta);
												p.$w.find('[name=gridProd] tbody').append($row);
												p.calcTot();
											}
										}
									}
								}
							}
						}
					}});
				});
				p.$w.on('click','[name=btnClas]', function(){
					var $row = $(this).closest('tr');
					prClas.windowSelect({
						bootstrap: true,
						logistica: true,
						callback: function(data){
							var _data = $row.data('data');
							if(_data.producto!=null){
								_data.producto.clasif = data;
							}else if(_data.servicio!=null){
								_data.servicio.clasif = data;
							}
							$row.data('gasto',data)
							$row.find('[name=clasificador]').html(data.cod).data('data',data);
							p.calcTot();
						}
					});
				});

				p.$w.on('click','[name=btnCuen]', function(){
					var $row = $(this).closest('tr');
					ctPcon.windowSelect({
						bootstrap: true,
						logistica: true,
						callback: function(data){
							var _data = $row.data('data');
							if(_data.producto!=null){
								_data.producto.cuenta = data;
							}else if(_data.servicio!=null){
								_data.servicio.cuenta = data;
							}
							$row.data('cuenta',data);
							$row.find('[name=cuenta]').html(data.cod).data('data',data);
							p.calcTot();
						}
					});
				});

				p.$w.find('[name=mini_enti] [name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						}
					});
				});
				p.$w.find('[name=btnAlm]').click(function(){
					lgAlma.windowSelect({callback: function(data){
						p.$w.find('[name=almacen]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name^=fec]').datepicker();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Cuenta','Producto','Unidad','Cantidad','Precio','Distribuci&oacute;n','SubTotal',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						p.$w.find('[name=gridProd] table:last').append('<tfoot>');
						var $row = $('<tr>');
						$row.append('<td colspan="7">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						$row.append('<td>');
						p.$w.find('[name=gridProd] tfoot').append($row);
					}
				});
				if(p.reg_etapa!=null){
					switch(p.reg_etapa){
						case "SOL":
							p.$w.find('[rel=solicitud]').show();
							break;
						case "CER":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							break;
						case "ORD":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							p.$w.find('[rel=orden]').show();
						case "ORS":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							p.$w.find('[rel=orden_servicio]').show();
							break;
						case "REC":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							p.$w.find('[rel=orden]').show();
							p.$w.find('[rel=recepcion]').show();
							break;
					}
				}else{
					switch(p.etapa){
						case "SOL":
							p.$w.find('[rel=solicitud]').show();
							break;
						case "CER":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							break;
						case "ORD":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							p.$w.find('[rel=orden]').show();
							break;
						case "ORS":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							p.$w.find('[rel=orden_servicio]').show();
							break;
						case "REC":
							p.$w.find('[rel=solicitud]').show();
							p.$w.find('[rel=certificacion]').show();
							p.$w.find('[rel=orden]').show();
							p.$w.find('[rel=recepcion]').show();
							break;
					}
				}
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
				$.post('lg/orde/edit_data',function(data){
					//p.cod = data.cod;
					var $select = p.$w.find('[name=fuente]');
					for(var k=0,l=data.fuen.length; k<l; k++){
						$select.append('<option value="'+data.fuen[k]._id.$id+'">'+data.fuen[k].cod+' - '+data.fuen[k].rubro+'</option>');
						$select.find('option:last').data('data',data.fuen[k]);
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
					p.nueva_etapa = 0;
					if(p.id!=null){
						$.post('lg/orde/get',{_id: p.id},function(data){
							p._data = data;
							p.$w.find('[name=ref]').val(data.ref);
							if(data.fuente!=null){
								p.$w.find('[name=fuente]').selectVal(data.fuente._id.$id);	
							}
							if(data.cotizacion!=null){
								p.$w.find('[name=cotizacion]').html(data.cotizacion.cod).data('data', lgCoti.dbRel(data.cotizacion));
							}
							//p.$w.find('[name=observ]').val(data.observ);
							p.$w.find('[rel=observ]').attr('disabled','disabled');
							p.$w.find('[name=solicitud_observ]').val(data.solicitud_observ);
							p.$w.find('[name=certificacion_observ]').val(data.certificacion_observ);
							p.$w.find('[name=orden_observ]').val(data.orden_observ);
							p.$w.find('[name=orden_servicio_observ]').val(data.orden_servicio_observ);
							p.$w.find('[name=recepcion_observ]').val(data.recepcion_observ);
							p.$w.find('[name=fecent]').val(ciHelper.date.format.bd_ymd(data.fecent));
							if(data.almacen!=null){
								p.$w.find('[name=almacen]').html(data.almacen.nomb).data('data',data.almacen);
							}
							if(data.proveedor!=null){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
							}
							var etapa = '';
							if(p.reg_etapa==null){
							//if(p.etapa==data.etapa){
								p.reg_etapa = p.etapa;
								switch(p.etapa){
									case "SOL":
										etapa = 'solicitud';
										p.$w.find('[name=solicitud_observ]').removeAttr('disabled');
										break;
									case "CER":
										etapa = 'certificacion';
										p.$w.find('[name=certificacion_observ]').removeAttr('disabled');
										break;
									case "ORD":
										etapa = 'orden';
										p.$w.find('[name=orden_observ]').removeAttr('disabled');
										break;
									case "ORS":
										etapa = 'orden_servicio';
										p.$w.find('[name=orden_servicio_observ]').removeAttr('disabled');
										break;
									case "REC":
										etapa = 'recepcion';
										p.$w.find('[name=recepcion_observ]').removeAttr('disabled');
										break;
								}
							}else{
								p.nueva_etapa = 1;
								switch(p.etapa){
									case "SOL":
										etapa = 'solicitud';
										break;
									case "CER":
										etapa = 'certificacion';
										break;
									case "ORD":
										etapa = 'orden';
										break;
									case "ORS":
										etapa = 'orden_servicio';
										break;
									case "REC":
										etapa = 'recepcion';
										break;
								}

								switch(p.reg_etapa){
									case "SOL":
										p.$w.find('[name=solicitud_observ]').removeAttr('disabled');
										break;
									case "CER":
										p.$w.find('[name=certificacion_observ]').removeAttr('disabled');
										break;
									case "ORD":
										p.$w.find('[name=orden_observ]').removeAttr('disabled');
										break;
									case "ORS":
										p.$w.find('[name=orden_servicio_observ]').removeAttr('disabled');
										break;
									case "REC":
										p.$w.find('[name=recepcion_observ]').removeAttr('disabled');
										break;
								}
							}
							if(data.solicitud!=null){
								if(data.solicitud.cod!=null){
									p.$w.find('[name=solicitud_num]').val(data.solicitud.cod);
								}
								if(data.solicitud.fecreg!=null){
									p.$w.find('[name=solicitud_fecreg]').val(moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=solicitud_autreg]').val(mgEnti.formatName(data.solicitud.autreg));
								}
								if(data.solicitud.fecenv!=null){
									p.$w.find('[name=solicitud_fecenv]').val(moment(data.solicitud.fecenv.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=solicitud_autenv]').val(mgEnti.formatName(data.solicitud.autenv));
								}
								if(data.solicitud.fecrec!=null){
									p.$w.find('[name=solicitud_fecrec]').val(moment(data.solicitud.fecrec.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=solicitud_autrec]').val(mgEnti.formatName(data.solicitud.autrec));
								}	
							}

							if(data.certificacion!=null){
								if(data.certificacion.cod!=null){
									p.$w.find('[name=certificacion_num]').val(data.certificacion.cod);
								}
								if(data.certificacion.fecreg!=null){
									p.$w.find('[name=certificacion_fecreg]').val(moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=certificacion_autreg]').val(mgEnti.formatName(data.certificacion.autreg));
								}
								if(data.certificacion.fecenv!=null){
									p.$w.find('[name=certificacion_fecenv]').val(moment(data.certificacion.fecenv.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=certificacion_autenv]').val(mgEnti.formatName(data.certificacion.autenv));
								}
								if(data.certificacion.fecrec!=null){
									p.$w.find('[name=certificacion_fecrec]').val(moment(data.certificacion.fecrec.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=certificacion_autrec]').val(mgEnti.formatName(data.certificacion.autrec));
								}
							}

							if(data.orden!=null){
								if(data.orden.cod!=null){
									p.$w.find('[name=orden_num]').val(data.orden.cod);
								}
								if(data.orden.fecreg!=null){
									p.$w.find('[name=orden_fecreg]').val(moment(data.orden.fecreg.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=orden_autreg]').val(mgEnti.formatName(data.orden.autreg));
								}
								if(data.orden.fecapr!=null){
									p.$w.find('[name=orden_fecapr]').val(moment(data.orden.fecapr.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=orden_autapr]').val(mgEnti.formatName(data.orden.autapr));
								}
								if(data.orden.fecenv!=null){
									p.$w.find('[name=orden_fecenv]').val(moment(data.orden.fecenv.sec,'X').format('DD/MM/YYYY h:mm:ss'));
									p.$w.find('[name=orden_autenv]').val(mgEnti.formatName(data.orden.autenv));
								}
							}
							
							if(data[etapa].productos!=null){
								if(data[etapa].productos.length>0){
									for(var i=0;i<data[etapa].productos.length;i++){
										if(p.reg_etapa=='ORD'){
											if(data.certificacion.productos[i].servicio!=null){
												continue;
											}
										}
										if(p.reg_etapa=='ORS'){
											if(data.certificacion.productos[i].producto!=null){
												continue;
											}
										}
										if(data[etapa].productos[i].producto!=null){
											//console.log(data.productos[i].producto);
											var $row = $('<tr class="item" name="'+data[etapa].productos[i].producto._id.$id+'">');
											$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
											if(data[etapa].productos[i].producto.clasif!=null){
												$row.append('<td><span name="clasificador">'+data[etapa].productos[i].producto.clasif.cod+'</span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}else{
												$row.append('<td><span name="clasificador"></span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}
											console.log('-------------------------------');
											console.log(data[etapa].productos[i].producto);
											if(data[etapa].productos[i].producto.cuenta!=null){
												$row.append('<td><span name="cuenta">'+data[etapa].productos[i].producto.cuenta.cod+'</span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}else{
												$row.append('<td><span name="cuenta"></span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}
											$row.append('<td><kbd>'+data[etapa].productos[i].producto.cod+'</kbd><br />'+data[etapa].productos[i].producto.nomb+'</td>');
											$row.append('<td>'+data[etapa].productos[i].producto.unidad.nomb+'</td>');
										}else{
											//console.log(data.productos[i].servicio);
											var $row = $('<tr class="item" name="'+data[etapa].productos[i].servicio._id.$id+'">');
											$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
											if(data[etapa].productos[i].servicio.clasif!=null){
												$row.append('<td><span name="clasificador">'+data[etapa].productos[i].servicio.clasif.cod+'</span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}else{
												$row.append('<td><span name="clasificador"></span> <button name="btnClas" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}
											if(data[etapa].productos[i].servicio.cuenta!=null){
												$row.append('<td><span name="cuenta">'+data[etapa].productos[i].servicio.cuenta.cod+'</span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}else{
												$row.append('<td><span name="cuenta"></span> <button name="btnCuen" class="btn btn-success"><i class="fa fa-search"></i></button></td>');
											}
											$row.append('<td><kbd>'+data[etapa].productos[i].servicio.cod+'</kbd><br />'+data[etapa].productos[i].servicio.nomb+'</td>');
											$row.append('<td>'+data[etapa].productos[i].servicio.unidad.nomb+'</td>');
										}

										$row.append('<td><span name="cant">'+data[etapa].productos[i].cant+'</span></td>');
										$row.append('<td><span name="precio">'+data[etapa].productos[i].precio+'</span></td>');
										$row.append('<td><span name="asignar"></span><button name="btnAsig" class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Asignar Distribuci&oacute;n</button></td>');
										$row.find('button:last').click(function(){
											var $row = $(this).closest('.item'),
											data = $row.data('data'),
											tmp_asig = $row.data('asig');
											if($row.data('gasto')!=null){
												lgOrde.windowAsignar({
													precio: $row.data('precio'),
													data: $row.data('asig'),
													prod: data,
													tmp_asig: tmp_asig,
													callback: function(asig,cant,total,precio){
														$row.find('[name=cant]').html(cant);
														$row.find('[name=precio]').html('S/.'+precio);
														$row.find('[name=subtotal]').html('S/.'+total);
														$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
														$row.find('[name=asignar]').empty();
														for(var i=0; i<asig.length; i++){
															var $ul = $('<ul><li>'+asig[i].programa.nomb+': cant. '+asig[i].cantidad+' S/.'+asig[i].monto+'</li></ul>');
															$row.find('[name=asignar]').append($ul);
														}
														p.calcTot();
														p.calcOrga();
													}
												});
											}else{
												return K.msg({title: ciHelper.titles.infoReq,text: 'Debe asignar un clasificador de gasto al producto antes de asignar su distribucion.!',type: 'error'});
											}
										});
										$row.append('<td><span name="subtotal">'+data[etapa].productos[i].subtotal+'</span></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
												p.$w.find('[name=gridProd] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										if(data[etapa].productos[i].asignacion!=null){
											if(data[etapa].productos[i].asignacion.length>0){
												var $ul = $('<ul />');
												for(var k=0; k<data[etapa].productos[i].asignacion.length; k++){
													$ul.append('<li>'+data[etapa].productos[i].asignacion[k].programa.nomb+': cant. '+data[etapa].productos[i].asignacion[k].cantidad+' S/.'+data[etapa].productos[i].asignacion[k].monto+'</li>');
												}
												$row.find('[name=asignar]').append($ul);
											}
										}

										if(data[etapa].productos[i].producto!=null){
											$row.data('data',data[etapa].productos[i].producto);
											$row.data('producto',data[etapa].productos[i].producto);
											$row.data('gasto',data[etapa].productos[i].producto.clasif);
											$row.data('cuenta',data[etapa].productos[i].producto.cuenta);
										}else{
											$row.data('data',data[etapa].productos[i].servicio);
											$row.data('servicio',data[etapa].productos[i].servicio);
											$row.data('gasto',data[etapa].productos[i].servicio.clasif);
											$row.data('cuenta',data[etapa].productos[i].servicio.cuenta);
										}
										$row.data('precio',data[etapa].productos[i].precio)
											.data('asig',data[etapa].productos[i].asignacion)
											.data('total',data[etapa].productos[i].subtotal)
											.data('cant',data[etapa].productos[i].cant);
										p.$w.find('[name=gridProd] tbody').append($row);
									}
								}
							}

							p.$w.find('[name=etapa]').val(p.reg_etapa);

							p.calcTot();
							p.calcOrga();
							if(p.nueva_etapa>0){
								$.post('cj/talo/all',{tipo:'LOG_'+p.reg_etapa}, function(data){
									if(data!=null){
										for(var i=0;i<data.length;i++){
											p.$w.find('[name=talonario]').append('<option value="'+data[i]._id.$id+'">'+data[i].prefijo+'-XXX-'+data[i].sufijo+'</option>');
										}
									}else{
										return K.msg({title: ciHelper.titles.infoReq,text: 'No se ha encontrado un talonario disponible para esta operacion!',type: 'error'});
									}
									K.unblock();
								},'json');
							}
							K.unblock();
						},'json');
					}else{
						p.nueva_etapa = 1;
						$.post('cj/talo/all',{tipo:'LOG_SOL'}, function(data){
							if(data!=null){
								for(var i=0;i<data.length;i++){
									p.$w.find('[name=talonario]').append('<option value="'+data[i]._id.$id+'">'+data[i].prefijo+'-XXX-'+data[i].sufijo+'</option>');
								}
							}else{
								return K.msg({title: ciHelper.titles.infoReq,text: 'No se ha encontrado un talonario disponible para esta operacion!',type: 'error'});
							}
							K.unblock();
						},'json');
					}
				},'json');
			}
		});
	},
	windowAsignar: function(p){
		$.extend(p,{
			calcTot: function(){
				var precio = K.round(parseFloat(p.$w.find('[name=precio]').val()),4);
				/*p.$w.find('[name=precio]').val(precio);*/
				var cant = 0;
				for(var i=0; i<p.$w.find('[name=cant]').length; i++){
					if(p.$w.find('[name=cant]').eq(i).val()=='') var val = 0;
					else{
						var val = parseFloat(p.$w.find('[name=cant]').eq(i).val());
					}
					cant = cant + val;
					p.$w.find('[name=grid] tbody .item:eq('+i+') td:eq(3)').html(precio);
					p.$w.find('[name=grid] tbody .item:eq('+i+') td:eq(4)').html(K.round(val*precio,4));
				}
				p.$w.find('[name^=total]:first').html(cant);
				p.$w.find('[name=total]:last').html(K.round(cant*precio,4));
			}
		});
		if(p.view==null){
			var buttons = {
				'Actualizar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var precio = parseFloat(p.$w.find('[name=precio]').val()),
						cant_tot = 0,
						data = [],
						$items = p.$w.find('[name=grid] tbody .item');
						if(p.$w.find('[name=precio]:first').val()=='' || precio==0){
							p.$w.find('[name=precio]:first').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un precio!',type: 'error'});
						}
						for(var i=0; i<$items.length; i++){
							var orga = $items.eq(i).data('data'),
							cant = parseFloat($items.eq(i).find('[name=cant]').val());
							if(orga==null){
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar al menos un programa!',type: 'error'});
							}
							if($items.eq(i).find('[name=cant]').val()==''||parseFloat(cant)==0){
								$items.eq(i).find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
							}
							cant_tot = cant_tot + cant;
							data.push({
								monto: K.round(precio*cant,4),
								programa: orga,
								cantidad: cant
							});
						}
						p.callback(data,cant_tot,/*total*/K.round(cant_tot*precio,2),precio);
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
			contentURL: 'lg/orde/asig',
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
				var cols = ['N&deg;','Programa','Cantidades<br/>Parciales','Precio','SubTotal'];
				if(p.view==null) cols.push('');
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					cols: cols,
					pagination: false,
					search: false,
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-sitemap"></i> Agregar Programas</button>',
					onContentLoaded: function($el){
						if(p.view==null){
							$el.find('button').click(function(){
								mgProg.windowSelect({
									callback: function(data){
										if(data.actividad==null){
											return K.msg({title: ciHelper.titles.infoReq,text: 'El progrmaa seleccionado no tiene una actividad vinculada!',type: 'error'});
										}
										if(data.componente==null){
											return K.msg({title: ciHelper.titles.infoReq,text: 'El progrmaa seleccionado no tiene un componente vinculada!',type: 'error'});
										}
										var $row = $('<tr class="item">');
										$row.append('<td>'+(p.$w.find('[name=grid] tbody .item').length+1)+'</td>');
										$row.append('<td>'+data.nomb+'</td>');
										$row.append('<td><input type="text" class="form-control" name="cant"></td>');
										$row.find('[name=cant]').keyup(function(){
											p.calcTot();
										});
										$row.append('<td><span name="precio"></span></td>');
										$row.append('<td><span name="subtotal"></span></td>');
										if(p.view==null){
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
				$row.append('<td>Total</td>');
				$row.append('<td><span name="total_cant"></span></td>');
				$row.append('<td>Total</td>');
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
						$row.append('<td>'+data.programa.nomb+'</td>');
						if(p.view==null){
							$row.append('<td><input type="text" class="form-control" name="cant" value="'+data.cantidad+'"></td>');
							$row.find('[name=cant]').keyup(function(){
								p.calcTot();
							});
						}else{
							$row.append('<td>'+data.cantidad+'<input type="hidden" class="form-control" name="cant" value="'+data.cantidad+'"></td>');
						}
						$row.append('<td><span name="precio"></span></td>');
						$row.append('<td><span name="subtotal"></span></td>');
						if(p.view==null){
							$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
									p.$w.find('[name=grid] tbody .item').eq(i).find('td:eq(0)').html(i+1);
								}
							});
						}
						$row.data('data',data.programa);
						p.$w.find('[name=grid] tbody').append($row);
					}
				}
				p.calcTot();
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
				p.$w.find("[name=gridPres] tbody").empty();
				for(var i=0; i<p.$w.find('[name=gridProd] tbody .item').length; i++){
					var servi = p.$w.find('[name=gridProd] tbody .item').eq(i).data('data'),
					tmp = p.$w.find('[name=gridProd] tbody .item').eq(i).data('asig');
					if(tmp!=null){
						for(var j=0; j<tmp.length; j++){
							if($.type(tmp[j].programa._id)=='object') var _id = tmp[j].programa._id.$id;
							else var _id = tmp[j].programa._id;
							if(p.$w.find('[name=gridPres] tbody [name='+_id+']').length>0){
								var tot = parseFloat(p.$w.find('[name=gridPres] tbody [name='+_id+'] td:eq(3)').data('monto'));
								p.$w.find('[name=gridPres] tbody [name='+_id+'] td:eq(3)').html(ciHelper.formatMon(tot+parseFloat(tmp[j].monto))).data('monto',tot+parseFloat(tmp[j].monto));
							}else{
								console.log(tmp[j]);
								var $row = $('<tr class="item" name="'+_id+'">');
								$row.append('<td>'+tmp[j].programa.nomb+'</td>');
								$row.append('<td>'+tmp[j].programa.actividad.cod+'</td>');
								$row.append('<td>'+tmp[j].programa.componente.cod+'</td>');
								$row.append('<td>'+ciHelper.formatMon(tmp[j].monto)+'</td>')
								$row.find('td:eq(3)').data('monto',tmp[j].monto);
					        	p.$w.find("[name=gridPres] tbody").append( $row );
					        	p.$w.find('[name=gridPres] tbody [name='+_id+']').data('orga',tmp[j].programa).data('gastos',[]);
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
		p.buttons = {
			"Imprimir": {
				icon: 'fa-refresh',
				type: 'success',
				f: function(){
					params = new Object;
					params.id = p.id;
					var url = 'lg/repo/orde?'+$.param(params);
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
						$.post('lg/orde/save',data,function(){
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
										$.post('lg/orde/save',data,function(){
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
						$.post('lg/orde/save',data,function(){
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
										$.post('lg/orde/save',data,function(){
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
						$.post('lg/orde/entrega',data,function(){
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
			contentURL: 'lg/orde/details',
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
					cols: ['','Clasificador','Producto','Unidad','Cantidad','Precio','Distribuci&oacute;n','SubTotal'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						p.$w.find('[name=gridProd] table:last').append('<tfoot>');
						var $row = $('<tr class="item">');
						$row.append('<td colspan="6">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						p.$w.find('[name=gridProd] tfoot').append($row);
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridPres]'),
					search: false,
					pagination: false,
					cols: ['Organizaci&oacute;n','Actividad','Componente','Monto'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						var $row = $('<tr class="item">');
						$row.append('<td colspan="2">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						p.$w.find('[name=gridPres] tfoot').append($row);
					}
				});
				$.post('lg/orde/get',{_id: p.id},function(data){
					if(data.ref!=null) p.$w.find('[name=ref]').html(data.ref);
					p.$w.find('[name=fuente]').html(data.fuente.cod+' '+data.fuente.rubro);
					p.$w.find('[name=observ]').html(data.observ);
					p.$w.find('[name=fecent]').html(ciHelper.date.format.bd_ymd(data.fecent));
					p.$w.find('[name=almacen]').html(data.almacen.nomb);
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
					for(var i=0; i<data.productos.length; i++){
						var data_prod = data.productos[i].producto,
						$row = $('<tr class="item" name="'+data_prod._id.$id+'">');
						$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
						$row.append('<td><kbd>'+data_prod.clasif.cod+'</kbd><br />'+data_prod.clasif.descr+'</td>');
						$row.append('<td><kbd>'+data_prod.cod+'</kbd><br />'+data_prod.nomb+'</td>');
						$row.append('<td>'+data_prod.unidad.nomb+'</td>');
						$row.append('<td><span name="cant">'+data.productos[i].cant+'</span></td>');
						$row.append('<td><span name="precio">'+(parseFloat(data.productos[i].cred_solic)/parseFloat(data.productos[i].cant))+'</span></td>');
						$row.append('<td><span name="asignar"></span><button class="btn btn-info btn-sm"><i class="fa fa-sitemap"></i> Revisar Distribuci&oacute;n</button></td>');
						$row.find('button:last').click(function(){
							var $row = $(this).closest('.item'),
							data = $row.data('data'),
							tmp_asig = $row.data('asig');
							lgOrde.windowAsignar({
								view: true,
								precio: $row.data('precio'),
								data: $row.data('asig'),
								prod: data,
								tmp_asig: tmp_asig,
								callback: function(asig,cant,total,precio){console.log(asig);
									$row.find('[name=cant]').html(cant);
									$row.find('[name=precio]').html('S/.'+precio);
									$row.find('[name=subtotal]').html('S/.'+total);
									$row.data('asig',asig).data('total',total).data('cant',cant).data('precio',precio);
									$row.find('[name=asignar]').empty();
									for(var i=0; i<asig.length; i++){
										var $ul = $('<ul><li>'+asig[i].programa.nomb+': cant. '+asig[i].cantidad+' S/.'+asig[i].monto+'</li></ul>');
										$row.find('[name=asignar]').append($ul);
									}
									p.calcTot();
									p.calcOrga();
								}
							});
						});
						$row.append('<td><span name="subtotal"></span></td>');
						$row.data('data',data_prod);
						$row.find('[name=cant]').html(data.productos[i].cant);
						$row.find('[name=precio]').html('S/.'+parseFloat(data.productos[i].precio));
						$row.find('[name=subtotal]').html('S/.'+parseFloat(data.productos[i].subtotal));
						$row.data('asig',data.productos[i].asignacion).data('total',parseFloat(data.productos[i].subtotal)).data('cant',data.productos[i].cant)
							.data('precio',parseFloat(data.productos[i].precio));
						$row.find('[name=asignar]').empty();
						for(var ii=0; ii<data.productos[i].asignacion.length; ii++){
							var $ul = $('<ul><li>'+data.productos[i].asignacion[ii].programa.nomb+': cant. '+data.productos[i].asignacion[ii].cantidad+' S/.'+data.productos[i].subtotal+'</li></ul>');
							$row.find('[name=asignar]').append($ul);
						}
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
										'<p class="m-b-xs"><strong>'+data.revisiones[i].trabajador.programa.nomb+'</strong></p>'+
										'<p>'+mgEnti.formatName(data.revisiones[i].trabajador)+'</p>'+
										'<p>Estado del Documento: '+lgOrde.states[data.revisiones[i].estado_doc].label+'</p>'+
									'</div>'+
								'</div>'+
							'</div>');
							if(data.revisiones[i].observ!=null)
								$item.find('.content').append('<p class="text-warning">'+data.revisiones[i].observ+'</p>');
							if(data.revisiones[i].estado=='A'){
								$item.find('.date').append('<br />'+lgOrde.statesRev[data.revisiones[i].estado].label);
								$item.find('.fa').addClass('fa-check');
							}else{
								$item.find('.date').append('<br />'+lgOrde.statesRev[data.revisiones[i].estado].label);
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
				/*p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'lg/orde/lista',
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
				});*/
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Estado','Cod.','Certificacion','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'R',etapa:'CER'},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrde.states[data.orden.estado].label+'</td>');
						$row.append('<td>'+data.orden.cod+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						var afectacion = '';
						if(data.certificacion.afectacion!=null){
							if(data.certificacion.afectacion.length>0){
								if(data.certificacion.afectacion.length==1){
									afectacion = data.certificacion.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgOrde", lgOrde.contextMenu);
						return $row;
					}
				});
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','pr/clas','lg/alma','lg/prod','lg/cert','lg/coti'],
	function(mgEnti,ctPcon, prClas,lgAlma,lgProd,lgCert,lgCoti){
		return lgOrde;
	}
);