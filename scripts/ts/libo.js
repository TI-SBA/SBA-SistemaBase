tsLibo = {
	states: {
		A: {
			descr: "Abierto",
			color: "green",
			label: '<span class="label label-success">Abierto</span>'
		},
		C:{
			descr: "Cerrado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Cerrado</span>'
		}
	},
		
dbRel: function(item){
		var rpta = {
			_id: item._id.$id
			};
			if(item.Libro!=null){
				rpta.Libro = {
					_id: item.Libro._id,
					ano: item.Libro.ano,
					mes: item.Libro.mes,
					cuenta: item.Libro.cuenta,
					saldo_final: item.Libro.cuenta
				},
				rpta.comprobantes = {
					fec: item.comprobantes.fec,
					tipo: item.comprobantes.tipo,
					num: item.comprobantes.num,
					descr: item.comprobantes.descr,
					debe: item.comprobantes.debe,
					haber: item.comprobantes.haber,
					saldo: item.comprobantes.saldo
				},
				rpta.comprobantes.rec_ingreso = {
					recibo1: item.comprobantes.rec_ingreso.recibo1,
					recibo2: item.comprobantes.recibo2.recibo2
				}

			}
			if(item.Libro._id.$id!=null)
				rpta.Libro._id = item.Libro._id.$id;
			

		
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsLibo',
			titleBar: {
				title: 'Libros Banco'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Año','Mes','Cuenta','Debe','Haber','Saldo'],
					data: 'ts/libo/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Libro</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsLibo.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+tsLibo.states[data.estado].label+'</td>');
						$row.append('<td>'+data.Libro.ano+'</td>');
						$row.append('<td>'+data.Libro.mes+'</td>');
						$row.append('<td>'+data.Libro.cuenta.cod+'</td>');
						$row.append('<td>'+data.comprobantes[0].debe+'</td>');
						$row.append('<td>'+data.comprobantes[0].haber+'</td>');
						$row.append('<td>'+data.comprobantes[0].saldo+'</td>');
						
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							tsLibo.windowDetails({_id: $(this).data('id'),titu: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenTsLibo", {
							onShowMenu: function($row, menu) {
								
							},
							bindings: {
								'conMenTsLibo_add': function(t) {
									tsLibo.AgregarComprobante({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenTsLibo_repo': function(t) {
									tsLibo.windowDetails({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenTsLibo_cerrar': function(t) {
									tsLibo.windowSesion({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenTsLibo_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Libro:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ts/libo/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Libro Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tsLibo.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Libro-Bancos');
								},
								'conMenTsLibo_repo':function(t6){
 									var url = 'ts/libo/reporte?_id='+K.tmp.data("id");
									window.open(url);
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
			    p.calcularTotal = function(){
					      var total = 0;
					      var saldo_anterior = p.$w.find('[name=saldo_final]').val();
					      var saldo = parseFloat(saldo_anterior);

			  		    for(var i=0;i<p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
				      	  var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
				        //busca tu elemento y luego ir aumentando el contador
					        var debe = $row.find('[name=debe]').val();
				          if(i==0) $row.find('[name=saldo]').val(saldo);
					        if(!parseFloat(debe)){
					        	debe = 0;
					        }else{
					        	debe = parseFloat(debe);
					        }
					        var haber = $row.find('[name=haber]').val();
					        if(!parseFloat(haber)){
					        	haber = 0;
					        }else{
					        	haber = parseFloat(haber);
					        }
				          saldo=saldo+debe-haber;
				          $row.find('[name=saldo]').val(saldo);
			      }
			    };
			new K.Panel({
				title: 'Nuevo Libro',
				contentURL: 'ts/libo/edit',
				store:false,
				buttons: {
					"Guardar": {
						icon: 'fa-save',
						type: 'success',
						f: function(){
							K.clearNoti();
							var form = ciHelper.validator(p.$w.find('form'),{
								onSuccess: function(){
									K.sendingInfo();
									var data = {
   										Libro: {
   											ano:p.$w.find('[name=ano]').val(),
   											mes:p.$w.find('[name=mes]').val(),
   											cuenta:  tsCtban.dbRel(p.$w.find('[name=banco]').data('data')),
   											saldo_final:p.$w.find('[name=saldo_final]').val(),
   										},
   										comprobantes:[]	
									};
									if(p.$w.find('[name=gridComprobantes] tbody tr').length>0){
										for(var i=0;i< p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
											var _comprobante ={
												fec: $row.find('[name=fec]').val(),
												tipo: $row.find('[name=tipo]').val(),
												num: $row.find('[name=num]').val(),
												rec_ingreso:{
													recibo1: tsRein.dbRel($row.find('[name=recibo1]').data('data','cod')),
													recibo2: tsRein.dbRel($row.find('[name=recibo2]').data('data','cod')),
												},
												
												descr: $row.find('[name=descr]').val(),
												debe: $row.find('[name=debe]').val(),
												haber: $row.find('[name=haber]').val(),
												saldo: $row.find('[name=saldo]').val(),
											}
											data.comprobantes.push(_comprobante);
											
										}
									}

									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("ts/libo/save",data,function(result){
											K.clearNoti();
											K.msg({title: ciHelper.titles.regiGua,text: "Bancos Agregada!"});
											tsLibo.init();
										
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							tsLibo.init();
						}
					}
				},

				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					
					p.$w.find('[name=btnBanco]').click(function(){
						tsCtban.windowSelect({callback: function(data){
							p.$w.find('[name=banco]').html(data.cod).data('data',data);
						},bootstrap: true});
					});

					$.post('ts/libo/get_saldo',function(data){
						var _saldo=0;
						if(data == null){
							_saldo = 0;
						}else{
							_saldo = parseFloat(data.Libro.saldo_final);
						}
						p.$w.find('[name=saldo_final]').val(_saldo);
					},'json');
					
					new K.grid({
									$el: p.$w.find('[name=gridComprobantes]'),
									cols: ['Fecha','Tipo','Numero','Recibo','Recibo','Detalle','Debe','Haber','Saldo','Eliminar'],
									stopLoad: true,
									pagination: false,
									search: false,
									store:false,
									toolbarHTML: '<button type = "button" name="btnAddComp" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Comprobante</button >',
									onContentLoaded: function($el){
										$el.find('button').click(function(){
											var $row = $('<tr class="item">');
											$row.append('<td><input type="text" class="form-control" style="width:95px" name="fec" /></td>');
											$row.find('[name=fec]').val(K.date()).datepicker();
											$row.append('<td><select class="form-control" style="width:130px" name="tipo">'+
												'<option value="VH">VOUCHER</option>'+
												'<option value="CH">CHEQUE</option>'+
												'</select></td>');
											$row.append('<td><input type="text" class="form-control" style="width:90px" name="num" /></td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-3">'+
																	'<span class="form-control" name="recibo1" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-5">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo1" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-4">'+
																	'<span class="form-control" name="recibo2" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-6">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo2" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');

											$row.append('<td><textarea type="text" class="form-control" name="descr"></textarea></td>');
											$row.append('<td>S/.<input type="text" name="debe" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="haber" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="saldo" disabled="disabled"  size="7"/></td>');
											$row.find('[name=debe],[name=saldo],[name=haber]').keyup(function(){
												p.calcularTotal();
											});
											$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('[name=btnEli]').click(function(){
												$(this).closest('.item').remove();
											});
											$row.find('[name=btnRecibo1]').click(function(){
											tsRein.windowSelect_2({callback: function(data){
													$row.find('[name=recibo1]').html(data.cod).data('data',data);
												},bootstrap: true});
											});
											$row.find('[name=btnRecibo2]').click(function(){
											tsRein.windowSelect_2({callback: function(data){
													$row.find('[name=recibo2]').html(data.cod).data('data',data);
												},bootstrap: true});
											});

											p.$w.find('[name=gridComprobantes] tbody').append($row);
																						

										});
										p.$w.find("[name=fec]").datepicker({
						   				format: 'mm/dd/yyyy',
						    			startDate: '-3d'
										});
									
									}
					});
								
				}
			});

	},
	/*
	windowNew: function(p){
		if(p==null) p = {};
			    p.calcularTotal = function(){
					      var total = 0;
					      var saldo_anterior = p.$w.find('[name=saldo_final]').val();
					      var saldo = parseFloat(saldo_anterior);

			  		    for(var i=0;i<p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
				      	  var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
				        //busca tu elemento y luego ir aumentando el contador
					        var debe = $row.find('[name=debe]').val();
				          if(i==0) $row.find('[name=saldo]').val(saldo);
					        if(!parseFloat(debe)){
					        	debe = 0;
					        }else{
					        	debe = parseFloat(debe);
					        }
					        var haber = $row.find('[name=haber]').val();
					        if(!parseFloat(haber)){
					        	haber = 0;
					        }else{
					        	haber = parseFloat(haber);
					        }
				          saldo=saldo+debe-haber;
				          $row.find('[name=saldo]').val(saldo);
			      }
			    };
			new K.Panel({
				title: 'Nuevo Libro',
				contentURL: 'ts/libo/edit',
				store:false,
				buttons: {
					"Guardar": {
						icon: 'fa-save',
						type: 'success',
						f: function(){
							K.clearNoti();
							var form = ciHelper.validator(p.$w.find('form'),{
								onSuccess: function(){
									K.sendingInfo();
									var data = {
   										Libro: {
   											ano:p.$w.find('[name=ano]').val(),
   											mes:p.$w.find('[name=mes]').val(),
   											cuenta: tsCtban.dbRel(p.$w.find('[name=banco]').data('data')),
   											saldo_final:p.$w.find('[name=saldo_final]').val(),
   										},
   										comprobantes:[]	
									};
									if(p.$w.find('[name=gridComprobantes] tbody tr').length>0){
										for(var i=0;i< p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
											var _comprobante ={
												fec: $row.find('[name=fec]').val(),
												tipo: $row.find('[name=tipo]').val(),
												num: $row.find('[name=num]').val(),
												rec_ingreso:{
													recibo1: tsRein.dbRel($row.find('[name=recibo1]').data('data')),
													recibo2: tsRein.dbRel($row.find('[name=recibo2]').data('data')),
												},
												
												descr: $row.find('[name=descr]').val(),
												debe: $row.find('[name=debe]').val(),
												haber: $row.find('[name=haber]').val(),
												saldo: $row.find('[name=saldo]').val(),
											}
											data.comprobantes.push(_comprobante);
											
										}
									}

									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("ts/libo/save",data,function(result){
											K.clearNoti();
											K.msg({title: ciHelper.titles.regiGua,text: "Bancos Agregada!"});
											tsLibo.init();
										
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							tsLibo.init();
						}
					}
				},

				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					
					p.$w.find('[name=btnBanco]').click(function(){
						tsCtban.windowSelect({callback: function(data){
							p.$w.find('[name=banco]').html(data.cod).data('data',data);
						},bootstrap: true});
					});

					$.post('ts/libo/get_saldo',function(data){
						var _saldo=0;
						if(data == null){
							_saldo = 0;
						}else{
							_saldo = parseFloat(data.Libro.saldo_final);
						}
						p.$w.find('[name=saldo_final]').val(_saldo);
					},'json');
					
					new K.grid({
									$el: p.$w.find('[name=gridComprobantes]'),
									cols: ['Fecha','Tipo','Numero','Recibo','Recibo','Detalle','Debe','Haber','Saldo','Eliminar'],
									stopLoad: true,
									pagination: false,
									search: false,
									store:false,
									toolbarHTML: '<button type = "button" name="btnAddComp" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Comprobante</button >',
									onContentLoaded: function($el){
										$el.find('button').click(function(){
											var $row = $('<tr class="item">');
											$row.append('<td><input type="text" class="form-control" style="width:95px" name="fec" /></td>');
											$row.find('[name=fec]').val(K.date()).datepicker();
											$row.append('<td><select class="form-control" style="width:130px" name="tipo">'+
												'<option value="VH">VOUCHER</option>'+
												'<option value="CH">CHEQUE</option>'+
												'</select></td>');
											$row.append('<td><input type="text" class="form-control" style="width:90px" name="num" /></td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-3">'+
																	'<span class="form-control" name="recibo1" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-5">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo1" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-4">'+
																	'<span class="form-control" name="recibo2" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-6">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo2" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');

											$row.append('<td><textarea type="text" class="form-control" name="descr"></textarea></td>');
											$row.append('<td>S/.<input type="text" name="debe" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="haber" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="saldo" disabled="disabled"  size="7"/></td>');
											$row.find('[name=debe],[name=saldo],[name=haber]').keyup(function(){
												p.calcularTotal();
											});
											$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('[name=btnEli]').click(function(){
												$(this).closest('.item').remove();
											});
											$row.find('[name=btnRecibo1]').click(function(){
											tsRein.windowSelect_2({callback: function(data){
													$row.find('[name=recibo1]').html(data.cod).data('data',data);
												},bootstrap: true});
											});
											$row.find('[name=btnRecibo2]').click(function(){
											tsRein.windowSelect_2({callback: function(data){
													$row.find('[name=recibo2]').html(data.cod).data('data',data);
												},bootstrap: true});
											});

											p.$w.find('[name=gridComprobantes] tbody').append($row);
																						

										});
										p.$w.find("[name=fec]").datepicker({
						   				format: 'mm/dd/yyyy',
						    			startDate: '-3d'
										});
									
									}
					});
								
				}
			});

	},
	*/
	AgregarComprobante: function(p){
		if(p==null) p = {};
			    p.calcularTotal = function(){
					      var total = 0;
					      var saldo_anterior = p.$w.find('[name=saldo_final]').val();
					      var saldo = parseFloat(saldo_anterior);

			  		    for(var i=0;i<p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
				      	  var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
				        //busca tu elemento y luego ir aumentando el contador
					        var debe = $row.find('[name=debe]').val();
				          if(i==0) $row.find('[name=saldo]').val(saldo);
					        if(!parseFloat(debe)){
					        	debe = 0;
					        }else{
					        	debe = parseFloat(debe);
					        }
					        var haber = $row.find('[name=haber]').val();
					        if(!parseFloat(haber)){
					        	haber = 0;
					        }else{
					        	haber = parseFloat(haber);
					        }
				          saldo=saldo+debe-haber;
				          $row.find('[name=saldo]').val(saldo);
			      }
			    };
			new K.Panel({
				title: 'Nuevo Libro',
				contentURL: 'ts/libo/edit',
				store:false,
				buttons: {
					"Guardar": {
						icon: 'fa-save',
						type: 'success',
						f: function(){
							K.clearNoti();
							var form = ciHelper.validator(p.$w.find('form'),{
								onSuccess: function(){
									K.sendingInfo();

									var data = {

   										Libro: {
   											ano:p.$w.find('[name=ano]').val(),
   											mes:p.$w.find('[name=mes]').val(),
   											cuenta:  tsCtban.dbRel(p.$w.find('[name=banco]').data('data','cod')),
   											saldo_final:p.$w.find('[name=saldo_final]').val(),
   										},

   										comprobantes:[]	
									};
									
									if(p.$w.find('[name=gridComprobantes] tbody tr').length>0){
										for(var i=0;i< p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
											var _comprobante ={
												fec: $row.find('[name=fec]').val(),
												tipo: $row.find('[name=tipo]').val(),
												num: $row.find('[name=num]').val(),
												rec_ingreso:{
													recibo1: tsRein.dbRel($row.find('[name=recibo1]').data('data','cod')),
													recibo2: tsRein.dbRel($row.find('[name=recibo2]').data('data','cod')),
												},
												
												descr: $row.find('[name=descr]').val(),
												debe: $row.find('[name=debe]').val(),
												haber: $row.find('[name=haber]').val(),
												saldo: $row.find('[name=saldo]').val(),
											}
											data.comprobantes.push(_comprobante);
											
										}
									}

									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("ts/libo/save",data,function(result){

											K.clearNoti();
											K.msg({title: ciHelper.titles.regiGua,text: "Bancos Agregada!"});
											tsLibo.init();
										
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							tsLibo.init();
						}
					}
				},

				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					
					p.$w.find('[name=btnBanco]').click(function(){
						tsCtban.windowSelect({callback: function(data,cod){
							p.$w.find('[name=banco]').html(data.cod).data('data',data);
						},bootstrap: true});
					});
					new K.grid({
									$el: p.$w.find('[name=gridComprobantes]'),
									cols: ['Fecha','Tipo','Numero','Recibo','Recibo','Detalle','Debe','Haber','Saldo','Eliminar'],
									stopLoad: true,
									pagination: false,
									search: false,
									store:false,
									toolbarHTML: '<button type = "button" name="btnAddComp" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Comprobante</button >',
									onContentLoaded: function($el){
										$el.find('button').click(function(){
											var $row = $('<tr class="item">');
											$row.append('<td><input type="text" class="form-control" style="width:95px" name="fec" /></td>');
											$row.find('[name=fec]').val(K.date()).datepicker();
											$row.append('<td><select class="form-control" style="width:130px" name="tipo">'+
												'<option value="VH">VOUCHER</option>'+
												'<option value="CH">CHEQUE</option>'+
												'</select></td>');
											$row.append('<td><input type="text" class="form-control" style="width:90px" name="num" /></td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-3">'+
																	'<span class="form-control" name="recibo1" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-5">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo1" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');

											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-4">'+
																	'<span class="form-control" name="recibo2" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-6">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo2" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');
											$row.append('<td><textarea type="text" class="form-control" name="descr"></textarea></td>');
											$row.append('<td>S/.<input type="text" name="debe" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="haber" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="saldo" disabled="disabled"  size="7"/></td>');
											$row.find('[name=debe],[name=saldo],[name=haber]').keyup(function(){
												p.calcularTotal();
											});
											$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('[name=btnEli]').click(function(){
												$(this).closest('.item').remove();
											});
											$row.find('[name=btnRecibo1]').click(function(){
											tsRein.windowSelect_2({callback: function(data,cod){
													$row.find('[name=recibo1]').html(data.cod).data('data',data);
												},bootstrap: true});
											});
											$row.find('[name=btnRecibo2]').click(function(){
											tsRein.windowSelect_2({callback: function(data,cod){
													$row.find('[name=recibo2]').html(data.cod).data('data',data);
												},bootstrap: true});
											});

											p.$w.find('[name=gridComprobantes] tbody').append($row);
																						

										});
										p.$w.find("[name=fec]").datepicker({
						   				format: 'mm/dd/yyyy',
						    			startDate: '-3d'
										});
									
									}
					});
					$.post('ts/libo/get',{_id: p.id},function(data){
										p.$w.find('[name=ano]').val(data.Libro.ano);
   										p.$w.find('[name=mes]').val(data.Libro.mes);
   										p.$w.find('[name=banco]').text(data.Libro.cuenta.cod).data('data',data.Libro.cuenta);
   										p.$w.find('[name=saldo_final]').val(data.Libro.saldo_final);
   										if(data.comprobantes!=null){
   											if(data.comprobantes.length>0){
   												for(var i=0;i<data.comprobantes.length;i++){
   													p.$w.find('[name=btnAddComp]').click();
   													var $row = p.$w.find('[name=gridComprobantes] tbody tr:last');
   													$row.find('[name=fec]').val(data.comprobantes[i].fec);
   													$row.find('[name=tipo]').val(data.comprobantes[i].tipo);
   													$row.find('[name=num]').val(data.comprobantes[i].num);
   													$row.find('[name=descr]').val(data.comprobantes[i].descr);
   													$row.find('[name=debe]').val(data.comprobantes[i].debe);
   													$row.find('[name=haber]').val(data.comprobantes[i].haber);
   													$row.find('[name=saldo]').val(data.comprobantes[i].saldo);
   													$row.find('[name=recibo1]').text(data.comprobantes[i].rec_ingreso.recibo1.cod).data('data',data.comprobantes[i].rec_ingreso.recibo1.cod);
   													$row.find('[name=recibo2]').text(data.comprobantes[i].rec_ingreso.recibo2.cod).data('data',data.comprobantes[i].rec_ingreso.recibo2.cod);	
   													
   												}
   											}
   										}
   										K.unblock();	
									},'json');
								
				}
			});

	}
	/*
	AgregarComprobante: function(p){
		if(p==null) p = {};
			    p.calcularTotal = function(){
					      var total = 0;
					      var saldo_anterior = p.$w.find('[name=saldo_final]').val();
					      var saldo = parseFloat(saldo_anterior);

			      for(var i=0;i<p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
				      	  var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
				        //busca tu elemento y luego ir aumentando el contador
					        var debe = $row.find('[name=debe]').val();
				          if(i==0) $row.find('[name=saldo]').val(saldo);
					        if(!parseFloat(debe)){
					        	debe = 0;
					        }else{
					        	debe = parseFloat(debe);
					        }
					        var haber = $row.find('[name=haber]').val();
					        if(!parseFloat(haber)){
					        	haber = 0;
					        }else{
					        	haber = parseFloat(haber);
					        }
				          saldo=saldo+debe-haber;
				          $row.find('[name=saldo]').val(saldo);
			      }
			
			    };

			new K.Panel({
				title: 'Nuevo Libro',
				contentURL: 'ts/libo/edit',
				store:false,
				buttons: {
					"Guardar": {
						icon: 'fa-save',
						type: 'success',
						f: function(){
							K.clearNoti();
							var form = ciHelper.validator(p.$w.find('form'),{
								onSuccess: function(){
									K.sendingInfo();
									//console.log(p.$w.find('[name=banco]').data('data'));
									console.log(p.$w.find('[name=banco]').data('data'));
									var data = {
   										Libro: {
   											ano:p.$w.find('[name=ano]').val(),
   											mes:p.$w.find('[name=mes]').val(),
   											cuenta: tsCtban.dbRel(p.$w.find('[name=banco]').data('data')),
   											saldo_final:p.$w.find('[name=saldo_final]').val(),
   										},
   										comprobantes:[]	
									};

									


									if(p.$w.find('[name=gridComprobantes] tbody tr').length>0){
										for(var i=0;i< p.$w.find('[name=gridComprobantes] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridComprobantes] tbody tr').eq(i);
											var _comprobante ={
												fec: $row.find('[name=fec]').val(),
												tipo: $row.find('[name=tipo]').val(),
												num: $row.find('[name=num]').val(),
												rec_ingreso:{
													recibo1: tsRein.dbRel($row.find('[name=recibo1]').data('data')),
													recibo2: tsRein.dbRel($row.find('[name=recibo2]').data('data')),
												},
												
												descr: $row.find('[name=descr]').val(),
												debe: $row.find('[name=debe]').val(),
												haber: $row.find('[name=haber]').val(),
												saldo: $row.find('[name=saldo]').val(),
											}
											data.comprobantes.push(_comprobante);
											
										}
									}

									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("ts/libo/save",data,function(result){
											K.clearNoti();
											K.msg({title: ciHelper.titles.regiGua,text: "Bancos Agregada!"});
											tsLibo.init();
									},'json');
								}
							}).submit();

						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							tsLibo.init();
						}
					}
				},

				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					
					p.$w.find('[name=btnBanco]').click(function(){
						tsCtban.windowSelect({callback: function(data){
							p.$w.find('[name=banco]').html(data.cod).data('data',data);
						},bootstrap: true});
					});
					
					new K.grid({
									$el: p.$w.find('[name=gridComprobantes]'),
									cols: ['Fecha','Tipo','Numero','Recibo','Recibo','Detalle','Debe','Haber','Saldo','Eliminar'],
									stopLoad: true,
									pagination: false,
									search: false,
									store:false,
									toolbarHTML: '<button type = "button" name="btnAddComp" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Comprobante</button >',
									onContentLoaded: function($el){
										$el.find('button').click(function(){
											var $row = $('<tr class="item">');
											$row.append('<td><input type="text" class="form-control" style="width:95px" name="fec" /></td>');
											$row.find('[name=fec]').val(K.date()).datepicker();
											$row.append('<td><select class="form-control" style="width:130px" name="tipo">'+
												'<option value="VH">VOUCHER</option>'+
												'<option value="CH">CHEQUE</option>'+
												'</select></td>');
											$row.append('<td><input type="text" class="form-control" style="width:90px" name="num" /></td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-3">'+
																	'<span class="form-control" name="recibo1" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-5">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo1" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');
											$row.append('<td class="form-group">'+
															'<ddiv>'+
																'<div class="col-sm-4">'+
																	'<span class="form-control" name="recibo2" style="width:90px" ></span>'+
																'</div>'+
																'<div class="col-sm-6">'+
																	'<span class="input-group-btn">'+
																		'<button name="btnRecibo2" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>'+
																	'</span>'+
																'</div>'+
															'</ddiv>'+
														'</td>');

											$row.append('<td><textarea type="text" class="form-control" name="descr"></textarea></td>');
											$row.append('<td>S/.<input type="text" name="debe" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="haber" size="7"/></td>');
											$row.append('<td>S/.<input type="text" name="saldo" disabled="disabled"  size="7"/></td>');
											$row.find('[name=debe],[name=saldo],[name=haber]').keyup(function(){
												p.calcularTotal();
											});
											$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('[name=btnEli]').click(function(){
												$(this).closest('.item').remove();
											});
											$row.find('[name=btnRecibo1]').click(function(){
											tsRein.windowSelect_2({callback: function(data){
													$row.find('[name=recibo1]').html(data.cod).data('data',data);
												},bootstrap: true});
											});
											$row.find('[name=btnRecibo2]').click(function(){
											tsRein.windowSelect_2({callback: function(data){
													$row.find('[name=recibo2]').html(data.cod).data('data',data);
												},bootstrap: true});
											});

											p.$w.find('[name=gridComprobantes] tbody').append($row);
																						

										});
										p.$w.find("[name=fec]").datepicker({
						   				format: 'mm/dd/yyyy',
						    			startDate: '-3d'
										});
									}
								});
									
									$.post('ts/libo/get',{_id: p.id},function(data){
										

										//console.log(data.comprobantes[1].rec_ingreso.recibo1.cod);
   										p.$w.find('[name=ano]').val(data.Libro.ano);
   										p.$w.find('[name=mes]').val(data.Libro.mes);
   										p.$w.find('[name=banco]').text(data.Libro.cuenta.cod);
   										//tsCtban.dbRel(p.$w.find('[name=banco]').data('data'))
   										//tsCtban.dbRel(p.$w.find('[name=cuenta]'),data.Libro.cuenta);
   										p.$w.find('[name=saldo_final]').val(data.Libro.saldo_final);
   										if(data.comprobantes!=null){
   											if(data.comprobantes.length>0){
   												for(var i=0;i<data.comprobantes.length;i++){
   													p.$w.find('[name=btnAddComp]').click();
   													var $row = p.$w.find('[name=gridComprobantes] tbody tr:last');
   													$row.find('[name=fec]').val(data.comprobantes[i].fec);
   													$row.find('[name=tipo]').val(data.comprobantes[i].tipo);
   													$row.find('[name=num]').val(data.comprobantes[i].num);
   													$row.find('[name=descr]').val(data.comprobantes[i].descr);
   													$row.find('[name=debe]').val(data.comprobantes[i].debe);
   													$row.find('[name=haber]').val(data.comprobantes[i].haber);
   													$row.find('[name=saldo]').val(data.comprobantes[i].saldo);
   													$row.find('[name=recibo1]').text(data.comprobantes[i].rec_ingreso.recibo1.cod);
   													$row.find('[name=recibo2]').text(data.comprobantes[i].rec_ingreso.recibo2.cod);	
   													
   												}
   											}
   										}
   										K.unblock();	
									},'json');
								}
						});

				},
						
			};
			*/
},

define(
	['mg/ofic','ts/ctban','ts/rein'],
	function(mgOfic,tsCtban,tsRein){
		return tsLibo;
	}
);
