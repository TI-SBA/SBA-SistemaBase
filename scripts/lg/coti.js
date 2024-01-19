lgCoti = {
	dbRel: function(item){
		return {
			_id:item._id.$id,
			cod:item.cod
		};
	},
	states: {
		PE: {
			descr: "Pendiente",
			color: "green",
			label: '<span class="label label-default">Pendiente</span>'
		},
		PU:{
			descr: "Publicado",
			color: "#CCCCCC",
			label: '<span class="label label-primary">Publicado</span>'
		},
		CE:{
			descr: "Cerrado",
			color: "#CCCCCC",
			label: '<span class="label label-warning">Cerrado</span>'
		},
		FI:{
			descr: "Finalizado",
			color: "#CCCCCC",
			label: '<span class="label label-success">Finalizado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCoti',
			titleBar: {title: 'Cotizaciones / Proceso de Selecci&oacute;n de Proveedor'}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nro',f:'num'},{n:'Trabajador',f:'trabajador.appat'},'Propuestas','Publicado','Cierre',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/coti/lista',
					params: {trabajador: K.session.enti._id.$id},
					itemdescr: 'pedido(s) interno(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nuevo Concurso</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgCoti.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCoti.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						if(data.propuesta!=null) $row.append('<td>'+data.propuesta.length+'</td>');
						else $row.append('<td>0</td>');
						if(data.publicacion!=null) $row.append('<td>'+ciHelper.date.format.bd_ymd(data.publicacion.fec)+'</td>');
						else $row.append('<td>');
						if(data.cierre!=null) $row.append('<td>'+ciHelper.date.format.bd_ymd(data.cierre.fec)+'</td>');
						else $row.append('<td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'<br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							/*lgCoti.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
								lgCoti.init();
							}});*/
						}).data('estado',data.estado).contextMenu("conMenLgCoti", {
							onShowMenu: function($row, menu) {
								var estado = $row.data('estado');
								if(estado=='PE') $('#conMenLgCoti_ver,#conMenLgCoti_fin,#conMenLgCoti_cer,#conMenLgCoti_rev,#conMenLgCoti_ing',menu).remove();
								else if(estado=='PU') $('#conMenLgCoti_ver,#conMenLgCoti_fin,#conMenLgCoti_pub,#conMenLgCoti_edi',menu).remove();
								else if(estado=='CE') $('#conMenLgCoti_ver,#conMenLgCoti_cer,#conMenLgCoti_ing,#conMenLgCoti_pub,#conMenLgCoti_edi',menu).remove();
								else $('#conMenLgCoti_cer,#conMenLgCoti_ing,#conMenLgCoti_pub,#conMenLgCoti_edi,#conMenLgCoti_rev,#conMenLgCoti_fin',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgCoti_edi': function(t) {
									lgCoti.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
								},
								'conMenLgCoti_pub': function(t) {
									ciHelper.confirm('&#191;Desea <b>Publicar</b> el Concurso <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/coti/save',{_id: K.tmp.data('id'),estado: 'PU'},function(){
											K.clearNoti();
											K.msg({title: 'Concurso Publicado',text: 'La publicaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgCoti.init();
										});
									},function(){
										$.noop();
									},'Publicaci&oacute;n de Concurso');
								},
								'conMenLgCoti_ing': function(t) {
									lgCoti.windowIng({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgCoti_rev': function(t) {
									var estado = K.tmp.data('estado');
									if(estado=='PU') lgCoti.windowRev({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
									else if(estado=='CE') lgCoti.windowRevCer({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenLgCoti_cer': function(t) {
									ciHelper.confirm('&#191;Desea <b>Cerrar</b> el Concurso <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										var data = {
											_id: K.tmp.data('id'),
											estado: 'CE',
											cierre: true
										};
										K.sendingInfo();
										$.post('lg/coti/save',data,function(){
											K.clearNoti();
											K.msg({title: 'Concurso Cerrado',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgCoti.init();
										});
									},function(){
										$.noop();
									},'Cierre de Concurso');
								},
								'conMenLgCoti_fin': function(t) {
									ciHelper.confirm('&#191;Desea <b>Finalizar</b> el Concurso <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										var data = {
											_id: K.tmp.data('id'),
											estado: 'FI',
											fin: true
										};
										K.sendingInfo();
										$.post('lg/coti/save',data,function(){
											K.clearNoti();
											K.msg({title: 'Concurso Finalizado',text: 'La finalizaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgCoti.init();
										});
									},function(){
										$.noop();
									},'Finalizaci&oacute;n de Concurso');
								},
								'conMenLgCoti_ver': function(t) {
									lgCoti.windowRevCon({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
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
		new K.Panel({
			contentURL: 'lg/coti/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.cod,
							fecent: p.$w.find('[name=fecent]').val(),
							feccierre: p.$w.find('[name=feccie]').val(),
							requerimientos: [],
							productos: [],
							servicios: []
						};
						if(data.feccierre==''){
							p.$w.find('[name=feccie]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de cierre!',type: 'error'});
						}
						if(data.fecent==''){
							p.$w.find('[name=fecent]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de entrega!',type: 'error'});
						}
						if(p.$w.find('[name=grid_req] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_req] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_req] tbody .item').eq(i);
								var req = lgPedi.dbRel($row.data('data'));
								data.requerimientos.push(req);
							}
						}else{
							return K.msg({title: ciHelper.titles.infoReq,text: 'La cotizacion debe tener al menos un requerimiento asociado!',type: 'error'});
						}
						if(p.$w.find('[name=grid_prod] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_prod] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_prod] tbody .item').eq(i);
								var prod = {
									item: parseInt(i)+1,
									cant: parseFloat($row.find('[name=cant]').val()),
									producto: lgProd.dbRel($row.data('data'))
								};
								prod.producto.unidad = {
									_id: $row.data('data').unidad._id.$id,
									nomb: $row.data('data').unidad.nomb
								};
								if($row.data('data').clasif!=null){
									prod.producto.clasif = {
										_id: $row.data('data').clasif._id.$id,
										cod: $row.data('data').clasif.cod,
										descr: $row.data('data').clasif.descr
									};
								}
								if(isNaN(prod.cant)){
									$row.find('[name=cant]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad correcta!',type: 'error'});
								}
								data.productos.push(prod);
							}
						}
						if(p.$w.find('[name=grid_serv] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_serv] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_serv] tbody .item').eq(i);
								var prod = {
									item: parseInt(i)+1,
									cant: parseFloat($row.find('[name=cant]').val()),
									servicio: lgServ.dbRel($row.data('data'))
								};
								prod.servicio.unidad = {
									_id: $row.data('data').unidad._id.$id,
									nomb: $row.data('data').unidad.nomb
								};
								if($row.data('data').clasif!=null){
									prod.servicio.clasif = {
										_id: $row.data('data').clasif._id.$id,
										cod: $row.data('data').clasif.cod,
										descr: $row.data('data').clasif.descr
									};
								}
								if(isNaN(prod.cant)){
									$row.find('[name=cant]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad correcta!',type: 'error'});
								}
								data.servicios.push(prod);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/coti/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titles.regiGua,text: "Cotizacion agregada!"});
							lgCoti.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgCoti.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),K.session.titular);
				p.$w.find('[name=btnAct],[name=btnSel]').hide();
				p.$w.find('[name=btnAlm]').click(function(){
					lgAlma.windowSelect({callback: function(data){
						p.$w.find('[name=almacen]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name^=fec]').datepicker();
				new K.grid({
					$el: p.$w.find('[name=grid_req]'),
					search: false,
					pagination: false,
					cols: ['Num','Tipo','Oficina','Programa',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-commenting-o"></i> Requerimiento</button>',
					onContentLoaded: function($el){
						$el.find('button:last').click(function(){
							lgPedi.windowSelect({estado:'A', callback: function(data){
								var $row = $('<tr class="item">');
								$row.append('<td><span name="cod">'+data.cod+'</span></td>');
								$row.append('<td><span name="tipo">'+data.tipo+'</span></td>');
								$row.append('<td><span name="oficina">'+data.oficina.nomb+'</span></td>');
								$row.append('<td><span name="programa">'+data.programa.nomb+'</span></td>');
								$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
								$row.data('data',data);
								$row.find('button:last').click(function(){
									var $row = $(this).closest('.item');
									$row.remove();
								});
								p.$w.find('[name=grid_req] tbody').append($row);
							}});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_prod]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Producto','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Fila</button>&nbsp',
					onContentLoaded: function($el){
						$el.find('button:first').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=grid_prod] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=grid_prod] tbody .item').length+1)+'</td>');
										if(data.clasif!=null){
											$row.append('<td><kbd>'+data.clasif.cod+'</kbd><br />'+data.clasif.descr+'</td>');	
										}else{
											$row.append('<td>Pendiente de Seleccion</td>');
										}
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" style="width:40px;"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=grid_prod] tbody .item').length; i++){
												p.$w.find('[name=grid_prod] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=grid_prod] tbody').append($row);
										//p.$w.find('[name=grid_prod] tbody .item:last button:first').click();
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_serv]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Servicio','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Fila</button>&nbsp',
					onContentLoaded: function($el){
						$el.find('button:first').click(function(){
							lgServ.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=grid_serv] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=grid_serv] tbody .item').length+1)+'</td>');
										if(data.clasif!=null){
											$row.append('<td><kbd>'+data.clasif.cod+'</kbd><br />'+data.clasif.descr+'</td>');	
										}else{
											$row.append('<td>Pendiente de Seleccion</td>');
										}
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" style="width:40px;"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=grid_serv] tbody .item').length; i++){
												p.$w.find('[name=grid_serv] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=grid_serv] tbody').append($row);
										//p.$w.find('[name=grid_serv] tbody .item:last button:first').click();
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El servicio ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				$.post('lg/coti/cod',function(data){
					p.cod = data.cod;
					p.$w.find('[name=cod]').html(data.cod);
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Panel({
			contentURL: 'lg/coti/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id:p.id,
							fecent: p.$w.find('[name=fecent]').val(),
							feccierre: p.$w.find('[name=feccie]').val(),
							requerimientos: [],
							productos: [],
							servicios: []
						};
						if(data.feccierre==''){
							p.$w.find('[name=feccie]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de cierre!',type: 'error'});
						}
						if(data.fecent==''){
							p.$w.find('[name=fecent]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de entrega!',type: 'error'});
						}
						if(p.$w.find('[name=grid_req] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_req] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_req] tbody .item').eq(i);
								var req = lgPedi.dbRel($row.data('data'));
								data.requerimientos.push(req);
							}
						}else{
							return K.msg({title: ciHelper.titles.infoReq,text: 'La cotizacion debe tener al menos un requerimiento asociado!',type: 'error'});
						}
						if(p.$w.find('[name=grid_prod] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_prod] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_prod] tbody .item').eq(i);
								var prod = {
									item: parseInt(i)+1,
									cant: parseFloat($row.find('[name=cant]').val()),
									producto: lgProd.dbRel($row.data('data'))
								};
								prod.producto.unidad = {
									_id: $row.data('data').unidad._id.$id,
									nomb: $row.data('data').unidad.nomb
								};
								if($row.data('data').clasif!=null){
									prod.producto.clasif = {
										_id: $row.data('data').clasif._id.$id,
										cod: $row.data('data').clasif.cod,
										descr: $row.data('data').clasif.descr
									};
								}
								if(isNaN(prod.cant)){
									$row.find('[name=cant]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad correcta!',type: 'error'});
								}
								data.productos.push(prod);
							}
						}
						if(p.$w.find('[name=grid_serv] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_serv] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_serv] tbody .item').eq(i);
								var prod = {
									item: parseInt(i)+1,
									cant: parseFloat($row.find('[name=cant]').val()),
									servicio: lgServ.dbRel($row.data('data'))
								};
								prod.servicio.unidad = {
									_id: $row.data('data').unidad._id.$id,
									nomb: $row.data('data').unidad.nomb
								};
								if($row.data('data').clasif!=null){
									prod.servicio.clasif = {
										_id: $row.data('data').clasif._id.$id,
										cod: $row.data('data').clasif.cod,
										descr: $row.data('data').clasif.descr
									};
								}
								if(isNaN(prod.cant)){
									$row.find('[name=cant]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad correcta!',type: 'error'});
								}
								data.servicios.push(prod);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/coti/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titles.regiGua,text: "Cotizacion agregada!"});
							lgCoti.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgCoti.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),K.session.titular);
				p.$w.find('[name=btnAct],[name=btnSel]').hide();
				p.$w.find('[name=btnAlm]').click(function(){
					lgAlma.windowSelect({callback: function(data){
						p.$w.find('[name=almacen]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name^=fec]').datepicker();
				new K.grid({
					$el: p.$w.find('[name=grid_req]'),
					search: false,
					pagination: false,
					cols: ['Num','Tipo','Oficina','Programa',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-commenting-o"></i> Requerimiento</button>',
					onContentLoaded: function($el){
						$el.find('button:last').click(function(){
							lgPedi.windowSelect({estado:'A', callback: function(data){
								var $row = $('<tr class="item">');
								$row.append('<td><span name="cod">'+data.cod+'</span></td>');
								$row.append('<td><span name="tipo">'+data.tipo+'</span></td>');
								$row.append('<td><span name="oficina">'+data.oficina.nomb+'</span></td>');
								$row.append('<td><span name="programa">'+data.programa.nomb+'</span></td>');
								$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
								$row.data('data',data);
								$row.find('button:last').click(function(){
									var $row = $(this).closest('.item');
									$row.remove();
								});
								p.$w.find('[name=grid_req] tbody').append($row);
							}});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_prod]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Producto','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Fila</button>&nbsp',
					onContentLoaded: function($el){
						$el.find('button:first').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=grid_prod] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=grid_prod] tbody .item').length+1)+'</td>');
										if(data.clasif!=null){
											$row.append('<td><kbd>'+data.clasif.cod+'</kbd><br />'+data.clasif.descr+'</td>');	
										}else{
											$row.append('<td>Pendiente de Seleccion</td>');
										}
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" style="width:40px;"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=grid_prod] tbody .item').length; i++){
												p.$w.find('[name=grid_prod] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=grid_prod] tbody').append($row);
										//p.$w.find('[name=grid_prod] tbody .item:last button:first').click();
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El producto ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_serv]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Servicio','Unidad','Cantidad',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Fila</button>&nbsp',
					onContentLoaded: function($el){
						$el.find('button:first').click(function(){
							lgServ.windowSelect({
								callback: function(data){
									if(p.$w.find('[name=grid_serv] tbody [name='+data._id.$id+']').length==0){
										var $row = $('<tr class="item" name="'+data._id.$id+'">');
										$row.append('<td>'+(p.$w.find('[name=grid_serv] tbody .item').length+1)+'</td>');
										if(data.clasif!=null){
											$row.append('<td><kbd>'+data.clasif.cod+'</kbd><br />'+data.clasif.descr+'</td>');	
										}else{
											$row.append('<td>Pendiente de Seleccion</td>');
										}
										$row.append('<td><kbd>'+data.cod+'</kbd><br />'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										$row.append('<td><input type="text" name="cant" class="form-control" style="width:40px;"></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button:last').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=grid_serv] tbody .item').length; i++){
												p.$w.find('[name=grid_serv] tbody .item').eq(i).find('td:eq(0)').html(i+1);
											}
										});
										$row.data('data',data);
										p.$w.find('[name=grid_serv] tbody').append($row);
										//p.$w.find('[name=grid_serv] tbody .item:last button:first').click();
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'El servicio ya fue seleccionado!',type: 'error'});
									}
								}
							});
						});
					}
				});
				$.post('lg/coti/get',{_id: p.id},function(data){
					p.$w.find('[name=cod]').html(data.cod);
					//p.$w.find('[name=almacen]').html(data.almacen.nomb).data('data',data.almacen);
					p.$w.find('[name=feccie]').val(ciHelper.date.format.bd_ymd(data.feccierre));
					p.$w.find('[name=fecent]').val(ciHelper.date.format.bd_ymd(data.fecent));
					if(data.requerimientos!=null){
						if(data.requerimientos.length>0){
							for(var i=0;i<data.requerimientos.length;i++){
								var $row = $('<tr class="item">');
								$row.append('<td><span name="cod">'+data.requerimientos[i].cod+'</span></td>');
								$row.append('<td><span name="tipo">'+data.requerimientos[i].tipo+'</span></td>');
								$row.append('<td><span name="oficina">'+data.requerimientos[i].oficina.nomb+'</span></td>');
								$row.append('<td><span name="programa">'+data.requerimientos[i].programa.nomb+'</span></td>');
								$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
								$row.data('data',data.requerimientos[i]);
								$row.find('button:last').click(function(){
									var $row = $(this).closest('.item');
									$row.remove();
								});
								p.$w.find('[name=grid_req] tbody').append($row);
							}
						}
					}
					if(data.productos!=null){
						if(data.productos.length>0){
							for(var i=0;i<data.productos.length;i++){
								var $row = $('<tr class="item" name="'+data.productos[i].producto._id.$id+'">');
								$row.append('<td>'+(p.$w.find('[name=grid_prod] tbody .item').length+1)+'</td>');
								if(data.productos[i].producto.clasif!=null){
									$row.append('<td><kbd>'+data.productos[i].producto.clasif.cod+'</kbd><br />'+data.productos[i].producto.clasif.descr+'</td>');	
								}else{
									$row.append('<td>Pendiente de Seleccion</td>');
								}
								$row.append('<td><kbd>'+data.productos[i].producto.cod+'</kbd><br />'+data.productos[i].producto.nomb+'</td>');
								$row.append('<td>'+data.productos[i].producto.unidad.nomb+'</td>');
								$row.append('<td><input type="text" name="cant" class="form-control" style="width:40px;"></td>');
								$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=cant]').val(data.productos[i].cant);
								$row.find('button:last').click(function(){
									$(this).closest('.item').remove();
									for(var i=0; i<p.$w.find('[name=grid_prod] tbody .item').length; i++){
										p.$w.find('[name=grid_prod] tbody .item').eq(i).find('td:eq(0)').html(i+1);
									}
								});
								$row.data('data',data.productos[i].producto);
								p.$w.find('[name=grid_prod] tbody').append($row);
							}
						}
					}
					if(data.servicios!=null){
						if(data.servicios.length>0){
							for(var i=0;i<data.servicios.length;i++){
								var $row = $('<tr class="item" name="'+data.servicios[i].servicio._id.$id+'">');
								$row.append('<td>'+(p.$w.find('[name=grid_serv] tbody .item').length+1)+'</td>');
								if(data.servicios[i].servicio.clasif!=null){
									$row.append('<td><kbd>'+data.servicios[i].servicio.clasif.cod+'</kbd><br />'+data.servicios[i].servicio.clasif.descr+'</td>');	
								}else{
									$row.append('<td>Pendiente de Seleccion</td>');
								}
								$row.append('<td><kbd>'+data.servicios[i].servicio.cod+'</kbd><br />'+data.servicios[i].servicio.nomb+'</td>');
								$row.append('<td>'+data.servicios[i].servicio.unidad.nomb+'</td>');
								$row.append('<td><input type="text" name="cant" class="form-control" style="width:40px;"></td>');
								$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=cant]').val(data.servicios[i].cant);
								$row.find('button:last').click(function(){
									$(this).closest('.item').remove();
									for(var i=0; i<p.$w.find('[name=grid_serv] tbody .item').length; i++){
										p.$w.find('[name=grid_serv] tbody .item').eq(i).find('td:eq(0)').html(i+1);
									}
								});
								$row.data('data',data.servicios[i].servicio);
								p.$w.find('[name=grid_serv] tbody').append($row);
							}
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowIng: function(p){
		new K.Panel({
			contentURL: 'lg/coti/edit_pro',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						if(p.propuesta==null) var length = 1;
						else var length = parseInt(p.propuesta.length)+1;
						var data = {
							num: p.nomb+'-'+length,
							fecent: p.$w.find('[name=fecentofer]').val(),
							ref: p.$w.find('[name=ref]').val(),
							observ: p.$w.find('[name=observ]').val(),
							productos: [],
							servicios: []
						};
						var tmp = p.$w.find('[name=mini_enti]').data('data');
						if(tmp==null){
							return K.msg({title: ciHelper.titles.infoReq, text: 'Debe seleccionar un proveedor!',type: 'error'});
						}else data.proveedor = mgEnti.dbRel(tmp);
						if(data.fecent==''){
							p.$w.find('[name=fecentofer]').focus();
							return K.msg({title: ciHelper.titles.infoReq, text: 'Debe ingresar una fecha de entrega!',type: 'error'});
						}
						var total = 0;
						/*for(var i=0; i<p.$w.find('[name=grid] tbody [name=valunit]').length; i++){
							var tmp = p.$w.find('[name=grid] tbody .item').eq(i).data('data');
							var prod = {
								item: i+1,
								producto: p.$w.find('[name=grid] tbody [name=prod]').eq(i).val(),
								unidad: {
									_id: tmp.unidad._id.$id,
									nomb: tmp.unidad.nomb
								},
								cant: tmp.cant,
								precio_unit: p.$w.find('[name=grid] tbody [name=valunit]').eq(i).val()
							};
							if(prod.precio_unit==''){
								p.$w.find('[name=grid] tbody [name=valunit]').eq(i).focus();
								return K.msg({title: ciHelper.titles.infoReq, text: 'Debe ingresar un valor unitario!',type: 'error'});
							}
							prod.precio_total = K.round(parseFloat(prod.cant)*parseFloat(prod.precio_unit),2);
							total = total + parseFloat(prod.precio_total);
			        		data.productos.push(prod);
						}*/


						if(p.$w.find('[name=grid_prod] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_prod] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_prod] tbody .item').eq(i);
								var prod = {
									item: parseInt(i)+1,
									cant: parseFloat($row.find('[name=cant]').val()),
									producto: lgProd.dbRel($row.data('data')),
									precio_unit: p.$w.find('[name=grid_prod] tbody [name=valunit]').eq(i).val()
								};
								if(prod.precio_unit==''){
									p.$w.find('[name=grid_prod] tbody [name=valunit]').eq(i).focus();
									return K.msg({title: ciHelper.titles.infoReq, text: 'Debe ingresar un valor unitario!',type: 'error'});
								}
								prod.producto.unidad = {
									_id: $row.data('data').unidad._id.$id,
									nomb: $row.data('data').unidad.nomb
								};
								if(isNaN(prod.cant)){
									$row.find('[name=cant]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad correcta!',type: 'error'});
								}
								prod.precio_total = K.round(parseFloat(prod.cant)*parseFloat(prod.precio_unit),2);
								total = total + parseFloat(prod.precio_total);
								data.productos.push(prod);
							}
						}
						if(p.$w.find('[name=grid_serv] tbody .item').length>0){
							for(var i=0; i<p.$w.find('[name=grid_serv] tbody .item').length; i++){
								var $row = p.$w.find('[name=grid_serv] tbody .item').eq(i);
								var prod = {
									item: parseInt(i)+1,
									cant: parseFloat($row.find('[name=cant]').val()),
									servicio: lgServ.dbRel($row.data('data')),
									precio_unit: p.$w.find('[name=grid_serv] tbody [name=valunit]').eq(i).val()
								};
								if(prod.precio_unit==''){
									p.$w.find('[name=grid_serv] tbody [name=valunit]').eq(i).focus();
									return K.msg({title: ciHelper.titles.infoReq, text: 'Debe ingresar un valor unitario!',type: 'error'});
								}
								prod.servicio.unidad = {
									_id: $row.data('data').unidad._id.$id,
									nomb: $row.data('data').unidad.nomb
								};
								if(isNaN(prod.cant)){
									$row.find('[name=cant]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una cantidad correcta!',type: 'error'});
								}
								prod.precio_total = K.round(parseFloat(prod.cant)*parseFloat(prod.precio_unit),2);
								total = total + parseFloat(prod.precio_total);
								data.servicios.push(prod);
							}
						}


						data.precio_total = total;
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/coti/save_prop",{_id: p.id,data: data},function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titles.regiGua,text: "Porpuesta agregada!"});
							lgCoti.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						lgCoti.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true});
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
				new K.grid({
					$el: p.$w.find('[name=grid_prod]'),
					search: false,
					pagination: false,
					cols: ['Concepto','Cantidad','Unidad','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=grid_serv]'),
					search: false,
					pagination: false,
					cols: ['Concepto','Cantidad','Unidad','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				$.post('lg/coti/get',{_id:p.id},function(data){					
					if(data.propuesta==null) var length = 1;
					else{
						var length = parseInt(data.propuesta.length)+1;
						p.propuesta = data.propuesta;
					}
					p.$w.find('[name=cod]').html('Cotizaci&oacute;n N&deg;'+p.nomb+'-'+length);
					p.$w.find('[name=fecent]').html(ciHelper.date.format.bd_ymd(data.fecent));
					p.$w.find('[name=fecentofer]').datepicker();
					if(data.productos!=null){
						for(var i=0; i<data.productos.length; i++){
				        	var $row = $('<tr class="item">');
							$row.append('<td>'+data.productos[i].producto.nomb+'</td>');
							$row.append('<td><input type="text" name="cant" value="'+data.productos[i].cant+'" disabled="disabled"></td>');
							$row.append('<td><kbd name="unidad">'+data.productos[i].producto.unidad.nomb+'</kbd></td>');
							$row.append('<td><input type="text" class="form-control" name="valunit" value="1" style="width:80px;"/></td>');
							$row.append('<td><span name="tot"></span></td>');
							$row.find('[name=valunit]').keyup(function(){
								var $row = $(this).closest('.item'),
				        		cant = parseFloat($row.find('[name=cant]').val());
				        		$row.find('[name=tot]').html(K.round(cant*parseFloat($(this).val()),2));
				        		var total = 0;
				        		for(var i=0; i<p.$w.find('[name=grid_prod] tbody [name=valunit]').length; i++){
					        		total = total + parseFloat(p.$w.find('[name=grid_prod] tbody .item').eq(i).find('[name=tot]').html());
				        		}
				        		p.$w.find('[name=grid_prod] tbody [name=total]').html(K.round(total,2));
							});
							$row.data('data',data.productos[i].producto);
							p.$w.find('[name=grid_prod] tbody').append($row);
						}
						var $row = $('<tr>');
						$row.append('<td colspan="3"></td>');
						$row.append('<td><b>Total</b></td>');
						$row.append('<td><b><span name="total">0.00</span></b></td>');
			        	p.$w.find('[name=grid_prod] tbody').append($row);
			        	for(var i=0; i<p.$w.find('[name=grid_prod] tbody [name=valunit]').length; i++){
			        		p.$w.find('[name=grid_prod] tbody [name=valunit]').eq(i).keyup();
		        		}
					}
	        		/******************************
	        		Servicios
	        		******************************/
	        		if(data.servicios!=null){
	        			for(var i=0; i<data.servicios.length; i++){
				        	var $row = $('<tr class="item">');
							$row.append('<td>'+data.servicios[i].servicio.nomb+'</td>');
							$row.append('<td><input type="text" name="cant" value="'+data.servicios[i].cant+'" disabled="disabled"></td>');
							$row.append('<td><kbd name="unidad">'+data.servicios[i].servicio.unidad.nomb+'</kbd></td>');
							$row.append('<td><input type="text" class="form-control" name="valunit" value="1" style="width:80px;"/></td>');
							$row.append('<td><span name="tot"></span></td>');
							$row.find('[name=valunit]').keyup(function(){
								var $row = $(this).closest('.item'),
				        		cant = parseFloat($row.find('[name=cant]').val());
				        		$row.find('[name=tot]').html(K.round(cant*parseFloat($(this).val()),2));
				        		var total = 0;
				        		for(var i=0; i<p.$w.find('[name=grid_serv] tbody [name=valunit]').length; i++){
					        		total = total + parseFloat(p.$w.find('[name=grid_serv] tbody .item').eq(i).find('[name=tot]').html());
				        		}
				        		p.$w.find('[name=grid_serv] tbody [name=total]').html(K.round(total,2));
							});
							$row.data('data',data.servicios[i].servicio);
							p.$w.find('[name=grid_serv] tbody').append($row);
						}
						var $row = $('<tr>');
						$row.append('<td colspan="3"></td>');
						$row.append('<td><b>Total</b></td>');
						$row.append('<td><b><span name="total">0.00</span></b></td>');
			        	p.$w.find('[name=grid_serv] tbody').append($row);
			        	for(var i=0; i<p.$w.find('[name=grid_serv] tbody [name=valunit]').length; i++){
			        		p.$w.find('[name=grid_serv] tbody [name=valunit]').eq(i).keyup();
		        		}
	        		}
					K.unblock();
				},'json');
			}
		});
	},
	windowRev: function(p){
		new K.Panel({
			title: 'Cuadro Comparativo de Cotizaciones para Concurso '+p.nomb,
			contentURL: 'lg/coti/cuad_comp',
			buttons: {
				"Regresar": {
					icon: 'fa-refresh',
					type: 'warning',
					f: function(){
						lgCoti.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnSel],[name=btnAct]').hide();
				p.$w.find('[name=btnEli]').click(function(){
					var num = $(this).data('num');
					ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Propuesta <b>'+num+'</b>&#63;',
					function(){
						K.sendingInfo();
						$.post('lg/coti/del_coti',{_id: p.id,num: num},function(){
							K.clearNoti();
							K.notification({title: 'Cotizante eliminado',text: 'La cotizaci&oacute;n se elimin&oacute; del concurso!'});
							lgCoti.windowRev({id: p.id,nomb: p.nomb});
						});
					},function(){
						$.noop();
					},'Eliminaci&oacute;n de Propuesta');
				});
				new K.grid({
					$el: p.$w.find('[name=grid_prod]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Descripci&oacute;n','Cantidad','Unidad','Propuesta Proveedor','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=grid_serv]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Descripci&oacute;n','Cantidad','Unidad','Propuesta Proveedor','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				$.post('lg/coti/get',{_id: p.id,full: true},function(data){
					p.data = data;
					var $cbo = p.$w.find('[name=proveedor]');
					for(var i=0; i<data.propuesta.length; i++){
						$cbo.append('<option value="'+i+'">'+mgEnti.formatName(data.propuesta[i].proveedor)+'</option>');
						$cbo.find('option:last').data('data',data.propuesta[i]);
					}
					$cbo.change(function(){
						var data = $(this).find('option:selected').data('data');
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
						p.$w.find('[name=fecentofer]').html(ciHelper.date.format.bd_ymd(data.fecent));
						p.$w.find('[name=num]').html(data.num);
						p.$w.find('[name=ref]').html(data.ref);
						p.$w.find('[name=observ]').html(data.observ);
						var precio_total = 0;
						if(data.productos!=null){
							p.$w.find('[name=grid_prod] tbody').empty();
							for(var i=0; i<p.data.productos.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+data.productos[i].item+'</td>');
								$row.append('<td>'+p.data.productos[i].producto.nomb+'</td>');
								$row.append('<td>'+p.data.productos[i].cant+'</td>');
								$row.append('<td><kbd>'+p.data.productos[i].producto.unidad.nomb+'</kbd></td>');
								$row.append('<td>--</td>');
								$row.append('<td>'+data.productos[i].precio_unit+'</td>');
								$row.append('<td>'+data.productos[i].precio_total+'</td>');
								p.$w.find('[name=grid_prod] tbody').append($row);
								precio_total+=data.productos[i].precio_total;
							}
							var $row = $('<tr>');
							$row.append('<td colspan="5"></td>');
							$row.append('<td><b>Total</b></td>');
							$row.append('<td><b><span name="total">'+precio_total+'</span></b></td>');
							p.$w.find('[name=grid_prod] tbody').append($row);
						}
						var precio_total = 0;
						if(data.servicios!=null){
							p.$w.find('[name=grid_serv] tbody').empty();
							for(var i=0; i<p.data.servicios.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+data.servicios[i].item+'</td>');
								$row.append('<td>'+p.data.servicios[i].servicio.nomb+'</td>');
								$row.append('<td>'+p.data.servicios[i].cant+'</td>');
								$row.append('<td><kbd>'+p.data.servicios[i].servicio.unidad.nomb+'</kbd></td>');
								$row.append('<td>--</td>');
								$row.append('<td>'+data.servicios[i].precio_unit+'</td>');
								$row.append('<td>'+data.servicios[i].precio_total+'</td>');
								p.$w.find('[name=grid_serv] tbody').append($row);
								precio_total+=data.servicios[i].precio_total;
							}
							var $row = $('<tr>');
							$row.append('<td colspan="5"></td>');
							$row.append('<td><b>Total</b></td>');
							$row.append('<td><b><span name="total">'+precio_total+'</span></b></td>');
							p.$w.find('[name=grid_serv] tbody').append($row);
						}
			        	p.$w.find('[name=btnEli]').data('num',data.num);
					}).change();
					K.unblock();
				},'json');
			}
		});
	},
	windowRevCer: function(p){
		new K.Panel({
			title: 'Cuadro Comparativo de Cotizaciones para Concurso '+p.nomb,
			contentURL: 'lg/coti/cuad_comp_cer',
			buttons: {
				"Regresar": {
					icon: 'fa-refresh',
					type: 'warning',
					f: function(){
						lgCoti.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnSel],[name=btnAct]').hide();
				p.$w.find('[name=btnGan]').click(function(){
					var num = $(this).data('num');
					ciHelper.confirm('&#191;Desea elegir <b>Ganadora</b> la Propuesta <b>'+num+'</b>&#63;',
					function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							propuesta: p.prop
						};
						for(var i=0; i<p.prop.length; i++){
							data.propuesta[i].proveedor._id = data.propuesta[i].proveedor._id.$id;
							data.propuesta[i].fecent = ciHelper.date.format.bd_ymd(data.propuesta[i].fecent);
							
							if(data.propuesta[i].productos!=null){
								if(data.propuesta[i].productos.length>0){
									for(var j=0; j<data.propuesta[i].productos.length; j++){
										var tmp_prod = data.propuesta[i].productos[j];
										var unidad = lgUnid.dbRel(tmp_prod.producto.unidad);
										data.propuesta[i].productos[j].producto = lgProd.dbRel(tmp_prod.producto);
										data.propuesta[i].productos[j].producto.unidad = unidad;
									}
								}
							}

							if(data.propuesta[i].servicios!=null){
								if(data.propuesta[i].servicios.length>0){
									for(var j=0; j<data.propuesta[i].servicios.length; j++){
										var tmp_serv = data.propuesta[i].servicios[j];
										var unidad = lgUnid.dbRel(tmp_serv.servicio.unidad);
										data.propuesta[i].servicios[j].servicio = lgServ.dbRel(tmp_serv.servicio);
										data.propuesta[i].servicios[j].servicio.unidad = unidad;
									}
								}
							}
							data.propuesta[i].calificacion = {
								justif: '',
								observ: '',
								ganador: false
							};
							if(num==p.prop[i].num){
								data.propuesta[i].calificacion = {
									ganador: true,
									justif: p.$w.find('[name=justif]').val(),
									observ: p.$w.find('[name=observ_adi]').val()
								};
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						/*console.log(data);
						return false;*/
						$.post('lg/coti/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El concurso ha sido actualizado con &eacute;xito!'});
							lgCoti.init();
						},'json');
					},function(){
						$.noop();
					},'Elecci&oacute;n de Propuesta Ganadora');
				});
				new K.grid({
					$el: p.$w.find('[name=grid_prod]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Descripci&oacute;n','Cantidad','Unidad','Propuesta Proveedor','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=grid_serv]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Descripci&oacute;n','Cantidad','Unidad','Propuesta Proveedor','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				$.post('lg/coti/get',{_id: p.id,full: true},function(data){
					p.data = data;
					p.prop = data.propuesta;
					var $cbo = p.$w.find('[name=proveedor]');
					for(var i=0; i<data.propuesta.length; i++){
						$cbo.append('<option value="'+i+'">'+mgEnti.formatName(data.propuesta[i].proveedor)+'</option>');
						$cbo.find('option:last').data('data',data.propuesta[i]);
					}
					$cbo.change(function(){
						var data = $(this).find('option:selected').data('data');
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
						p.$w.find('[name=fecentofer]').html(ciHelper.date.format.bd_ymd(data.fecent));
						p.$w.find('[name=num]').html(data.num);
						p.$w.find('[name=ref]').html(data.ref);
						p.$w.find('[name=observ]').html(data.observ);
						p.$w.find('[name=grid] tbody').empty();
						var precio_total = 0;
						if(data.productos!=null){
							p.$w.find('[name=grid_prod] tbody').empty();
							for(var i=0; i<p.data.productos.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+data.productos[i].item+'</td>');
								$row.append('<td>'+p.data.productos[i].producto.nomb+'</td>');
								$row.append('<td>'+p.data.productos[i].cant+'</td>');
								$row.append('<td><kbd>'+p.data.productos[i].producto.unidad.nomb+'</kbd></td>');
								$row.append('<td>--</td>');
								$row.append('<td>'+data.productos[i].precio_unit+'</td>');
								$row.append('<td>'+data.productos[i].precio_total+'</td>');
								p.$w.find('[name=grid_prod] tbody').append($row);
								precio_total+=data.productos[i].precio_total;
							}
							var $row = $('<tr>');
							$row.append('<td colspan="5"></td>');
							$row.append('<td><b>Total</b></td>');
							$row.append('<td><b><span name="total">'+precio_total+'</span></b></td>');
							p.$w.find('[name=grid_prod] tbody').append($row);
						}
						var precio_total = 0;
						if(data.servicios!=null){
							p.$w.find('[name=grid_serv] tbody').empty();
							for(var i=0; i<p.data.servicios.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+data.servicios[i].item+'</td>');
								$row.append('<td>'+p.data.servicios[i].servicio.nomb+'</td>');
								$row.append('<td>'+p.data.servicios[i].cant+'</td>');
								$row.append('<td><kbd>'+p.data.servicios[i].servicio.unidad.nomb+'</kbd></td>');
								$row.append('<td>--</td>');
								$row.append('<td>'+data.servicios[i].precio_unit+'</td>');
								$row.append('<td>'+data.servicios[i].precio_total+'</td>');
								p.$w.find('[name=grid_serv] tbody').append($row);
								precio_total+=data.servicios[i].precio_total;
							}
							var $row = $('<tr>');
							$row.append('<td colspan="5"></td>');
							$row.append('<td><b>Total</b></td>');
							$row.append('<td><b><span name="total">'+precio_total+'</span></b></td>');
							p.$w.find('[name=grid_serv] tbody').append($row);
						}
			        	p.$w.find('[name=btnGan]').data('num',data.num);
			        	p.$w.find('[name=justif]').val('');
						p.$w.find('[name=observ_adi]').val('');
			        	p.$w.find('[name=ganador]').html('Esta propuesta no ha sido elegida como ganadora').addClass('bg-danger');
						if(data.calificacion!=null){
							if(data.calificacion.ganador==true) p.$w.find('[name=ganador]').html('Esta propuesta ha sido elegida como GANADORA').addClass('bg-success').removeClass('bg-danger');
							else p.$w.find('[name=ganador]').html('Esta propuesta no ha sido elegida como ganadora').addClass('bg-danger').removeClass('bg-success');
							p.$w.find('[name=justif]').val(data.calificacion.justif);
							p.$w.find('[name=observ_adi]').val(data.calificacion.observ);
						}
					}).change();
					K.unblock();
				},'json');
			}
		});
	},
	windowRevCon: function(p){
		new K.Panel({
			title: 'Cuadro Comparativo de Cotizaciones para Concurso '+p.nomb,
			contentURL: 'lg/coti/cuad_comp_cer',
			buttons: {
				"Regresar": {
					icon: 'fa-refresh',
					type: 'warning',
					f: function(){
						lgCoti.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnSel],[name=btnAct]').hide();
				new K.grid({
					$el: p.$w.find('[name=grid_prod]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Descripci&oacute;n','Cantidad','Unidad','Propuesta Proveedor','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=grid_serv]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Descripci&oacute;n','Cantidad','Unidad','Propuesta Proveedor','Valor Unitario','Subtotal'],
					onlyHtml: true
				});
				p.$w.find('[name=justif]').replaceWith('<span class="form-control" name="justif"></span>');
				p.$w.find('[name=observ_adi]').replaceWith('<span class="form-control" name="observ_adi"></span>');
				p.$w.find('[name=ganador]').remove();
				p.$w.find('[name=btnGan]').click(function(e){
					e.preventDefault();
				});
				$.post('lg/coti/get',{_id: p.id,full: true},function(data){
					p.data = data;
					p.prop = data.propuesta;
					var $cbo = p.$w.find('[name=proveedor]');
					for(var i=0; i<data.propuesta.length; i++){
						$cbo.append('<option value="'+i+'">'+mgEnti.formatName(data.propuesta[i].proveedor)+'</option>');
						$cbo.find('option:last').data('data',data.propuesta[i]);
					}
					$cbo.change(function(){
						var data = $(this).find('option:selected').data('data');
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
						p.$w.find('[name=fecentofer]').html(ciHelper.date.format.bd_ymd(data.fecent));
						p.$w.find('[name=num]').html(data.num);
						p.$w.find('[name=ref]').html(data.ref);
						p.$w.find('[name=observ]').html(data.observ);
						p.$w.find('[name=grid] tbody').empty();
						var precio_total = 0;
						if(data.productos!=null){
							p.$w.find('[name=grid_prod] tbody').empty();
							for(var i=0; i<p.data.productos.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+data.productos[i].item+'</td>');
								$row.append('<td>'+p.data.productos[i].producto.nomb+'</td>');
								$row.append('<td>'+p.data.productos[i].cant+'</td>');
								$row.append('<td><kbd>'+p.data.productos[i].producto.unidad.nomb+'</kbd></td>');
								$row.append('<td>--</td>');
								$row.append('<td>'+data.productos[i].precio_unit+'</td>');
								$row.append('<td>'+data.productos[i].precio_total+'</td>');
								p.$w.find('[name=grid_prod] tbody').append($row);
								precio_total+=data.productos[i].precio_total;
							}
							var $row = $('<tr>');
							$row.append('<td colspan="5"></td>');
							$row.append('<td><b>Total</b></td>');
							$row.append('<td><b><span name="total">'+precio_total+'</span></b></td>');
							p.$w.find('[name=grid_prod] tbody').append($row);
						}
						var precio_total = 0;
						if(data.servicios!=null){
							p.$w.find('[name=grid_serv] tbody').empty();
							for(var i=0; i<p.data.servicios.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+data.servicios[i].item+'</td>');
								$row.append('<td>'+p.data.servicios[i].servicio.nomb+'</td>');
								$row.append('<td>'+p.data.servicios[i].cant+'</td>');
								$row.append('<td><kbd>'+p.data.servicios[i].servicio.unidad.nomb+'</kbd></td>');
								$row.append('<td>--</td>');
								$row.append('<td>'+data.servicios[i].precio_unit+'</td>');
								$row.append('<td>'+data.servicios[i].precio_total+'</td>');
								p.$w.find('[name=grid_serv] tbody').append($row);
								precio_total+=data.servicios[i].precio_total;
							}
							var $row = $('<tr>');
							$row.append('<td colspan="5"></td>');
							$row.append('<td><b>Total</b></td>');
							$row.append('<td><b><span name="total">'+precio_total+'</span></b></td>');
							p.$w.find('[name=grid_serv] tbody').append($row);
						}
			        	p.$w.find('[name=btnGan]').data('num',data.num);
			        	p.$w.find('[name=justif]').html('');
						p.$w.find('[name=observ_adi]').html('');
			        	p.$w.find('[name=btnGan]').html('<i class="fa fa-close"></i> Esta propuesta no ha sido elegida como ganadora').addClass('btn-danger');
						if(data.calificacion!=null){
							if(data.calificacion.ganador==true) p.$w.find('[name=btnGan]').html('<i class="fa fa-trophy"></i> Esta propuesta ha sido elegida como GANADORA').addClass('btn-success').removeClass('btn-danger');
							else p.$w.find('[name=btnGan]').html('<i class="fa fa-close"></i> Esta propuesta no ha sido elegida como ganadora').addClass('btn-danger').removeClass('btn-success');
							p.$w.find('[name=justif]').html(data.calificacion.justif);
							p.$w.find('[name=observ_adi]').html(data.calificacion.observ);
						}
					}).change();
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
			title: 'Seleccionar Cotizacion',
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
					cols: ['','Estado','Numero','Requerimientos'],
					data: 'lg/coti/lista',
					params: {},
					itemdescr: 'cotizacion(es)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCoti.states[data.estado].label+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						var requerimientos = '';
						if(data.requerimientos!=null){
							if(data.requerimientos.length>0){
								for(var i=0;i<data.requerimientos.length;i++){
									requerimientos+=data.requerimientos[i].cod+' ';
								}
							}
						}
						$row.append('<td>'+requerimientos+'</td>');
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
	['mg/enti','ct/pcon','lg/unid','lg/pedi','lg/alma','lg/prod','lg/serv'],
	function(mgEnti,ctPcon,lgUnid,lgPedi,lgAlma, lgProd, lgServ){
		return lgCoti;
	}
);