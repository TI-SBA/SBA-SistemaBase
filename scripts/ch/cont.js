chCont = {
	tipoMov: {
		E: 'Estado',
		R: 'Receta',
		ER: 'Estado y Receta',
		D: 'Devoluci&oacute;n'
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'chCont',
			titleBar: {
				title: 'Control de Medicinas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Paciente',f:'paciente.nomb'},'Medicinas',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'ch/cont/lista',
					params: {modulo: 'CH'},
					itemdescr: 'paciente(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Lista de Medicinas para Paciente</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							chCont.windowNew();
						});
						$el.find('[name=modulo]').change(function(){
							var modulo = $el.find('[name=modulo] option:selected').val();
							$grid.reinit({params: {modulo: modulo}});
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock();
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>');
						for(var i=0; i<data.medicinas.length; i++){
							if(i!=0) $row.find('td:last').append('<br />');
							$row.find('td:last').append('<span>'+data.medicinas[i].med+' (stock: '+data.medicinas[i].stock+')'+'</span>');
						}
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							chCont.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(1)').html()});
						}).data('estado',data.estado).contextMenu("conMenHoCont", {
							onSchwMenu: function($row, menu) {
								//$('#conMenHoCont_des',menu).remove();
								if($row.data('estado')=='D')
									$('#conMenHoCont_aum,#conMenHoCont_dis,#conMenHoCont_dev,#conMenHoCont_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenHoCont_ver': function(t) {
									chCont.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
								},
								'conMenHoCont_aum': function(t) {
									chCont.windowAum({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
								},
								'conMenHoCont_dis': function(t) {
									chCont.windowDis({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
								},
								'conMenHoCont_dev': function(t){
									ciHelper.confirm('&#191;Desea <b>DEVOLVER</b> todas las medicinas del paciente <b>'+K.tmp.find('td:eq(1)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ch/cont/save_devolver',{_id: K.tmp.data('id')},function(){
											window.open("ch/cont/print?_id="+K.tmp.data('id'));
											K.clearNoti();
											K.notification({title: 'Medicinas Devueltas',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											chCont.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
								},
								'conMenHoCont_imp': function(){
									window.open("ch/cont/print?_id="+K.tmp.data('id'));
								},
								'conMenHoCont_des': function(t){
									chCont.windowDeshacer({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
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
		new K.Modal({ 
			id: 'windowNew',
			title: 'Nuevo Inventario de Paciente',
			contentURL: 'ch/cont/edit',
			store: false,
			allScreen: true,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod_hist: p.$w.find('[name=cod_hist]').data('data'),
							modulo: p.$w.find('[name=modulo]').val(),
							paciente: p.$w.find('[name=mini_enti]').data('data'),
							medicinas: []
						};
						if(data.paciente==null){
							p.$w.find('[name=cod_hist]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un paciente!',type: 'error'});
						}else data.paciente = mgEnti.dbRel(data.paciente);
						if(p.$w.find('[name=gridMed] tbody .item').length==0){
							p.$w.find('[name=gridMed] button').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar al menos una medicina!',
								type: 'error'
							});
						}
						for(var i=0; i<p.$w.find('[name=gridMed] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridMed] tbody .item').eq(i),
							tmp = {
								med: $row.find('[name=med]').val(),
								stock: $row.find('[name=stock]').val(),
								hist: [
									{fec: $row.find('[name=fec]').val(),cant: $row.find('[name=stock]').val(),stock: $row.find('[name=stock]').val()}
								]
							}
							if(tmp.med==''){
								$row.find('[name=med]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar la medicina!',
									type: 'error'
								});
							}
							if(tmp.stock==''){
								$row.find('[name=stock]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un stock!',
									type: 'error'
								});
							}
							if(parseInt(tmp.stock)<=0){
								$row.find('[name=stock]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un stock mayor a 0!',
									type: 'error'
								});
							}
							if(tmp.hist[0].fec==''){
								$row.find('[name=med]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar la fecha de ingreso!',
									type: 'error'
								});
							}
							tmp.hist[0].tipo = $row.find('[name=tipo] option:selected').val();
							data.medicinas.push(tmp);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ch/cont/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Control agregado!"});
							chCont.init();
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
				p.$w.find('[name=cod_hist]').keyup(function(e){
					e.preventDefault();
					if(e.keyCode == 13) p.$w.find('[name=btnHist]').click();
				});
				p.$w.find('[name=btnHist]').click(function(){
					K.block();
					$.post('ci/skmmh/search_hist_ch',{cod: p.$w.find('[name=cod_hist]').val()},function(data){
						if(data.paciente.nomb!=null){
							p.$w.find('[name=cod_hist]').data('data',p.$w.find('[name=cod_hist]').val());
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						}else{
							K.msg({
								text: 'No existe esa historia clinica!',
								type: 'error'
							});
						}
						K.unblock();
					},'json');
				});
				p.$w.find('[name=btnAct]').click(function(){
					if(p.$w.find('[name=mini_enti]').data('data')==null){
						K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						},id: p.$w.find('[name=mini_enti]').data('data')._id.$id});
					}
				});
				p.$w.find('[name=btnSel]').remove();
				new K.grid({
					$el: p.$w.find('[name=gridMed]'),
					cols: ['Fecha de Ingreso','Medicina','Stock Inicial','Realizado por',''],
					stopLoad: true,
					pagination: false,
					search: false,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Medicina</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" class="form-control" name="fec" value="'+ciHelper.date.get.now_ymd()+'" /></td>');
							$row.append('<td><input type="text" class="form-control" name="med" placechlder="Ejem: Clonazepan 2mg, Valprax 500mg, etc." /></td>');
							$row.append('<td><input type="text" class="form-control" name="stock" value="1" /></td>');
							$row.append('<td><select class="form-control" name="tipo">'+
								'<option value="E">Estado</option>'+
								'<option value="R">Receta</option>'+
								'<option value="ER">Estado y Receta</option>'+
							'</select></td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=fec]').datepicker();
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridMed] tbody').append($row);
						}).click();
					}
				});
			}
		});
	},
	windowDis: function(p){
		new K.Modal({ 
			id: 'windowEdit',
			title: 'Descontar Medicinas',
			contentURL: 'ch/cont/edit',
			allScreen: true,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							medicinas: []
						};
						for(var i=0; i<p.$w.find('[name=gridMed] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridMed] tbody .item').eq(i),
							tmp = {
								fec: $row.find('[name=fec]').val(),
								cant: $row.find('[name=cant]').val(),
							};
							if(tmp.cant==''){
								$row.find('[name=cant]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un stock!',
									type: 'error'
								});
							}
							if(parseInt(tmp.cant)<0){
								$row.find('[name=cant]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un stock mayor a 0!',
									type: 'error'
								});
							}
							if(tmp.fec==''){
								$row.find('[name=med]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar la fecha de ingreso!',
									type: 'error'
								});
							}
							tmp.tipo = $row.find('[name=tipo] option:selected').val();
							data.medicinas.push(tmp);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ch/cont/save_dis",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Control actualizado!"});
							chCont.init();
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
				p.$w = $('#windowEdit');
				new K.grid({
					$el: p.$w.find('[name=gridMed]'),
					cols: ['Medicina','Stock Actual','Fecha de Disminuci&oacute;n','Cantidad a Restar','Realizado por'],
					stopLoad: true,
					pagination: false,
					search: false
				});
				K.block();
				$.post('ch/cont/get',{_id: p.id},function(data){
					p.$w.find('[name=btnHist]').remove();
					p.$w.find('[name=btnAct]').remove();
					p.$w.find('[name=btnSel]').remove();
					p.$w.find('[name=cod_hist]').val(data.cod_hist).attr('disabled','disabled');
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.paciente);
					for(var i=0; i<data.medicinas.length; i++){
						var $row = $('<tr class="item">');
						$row.append('<td><code>'+data.medicinas[i].med+'</code></td>');
						$row.append('<td><kbd>'+data.medicinas[i].stock+'</kbd></td>');
						$row.append('<td><input type="text" class="form-control" name="fec" value="'+ciHelper.date.get.now_ymd()+'" /></td>');
						$row.append('<td><input type="text" class="form-control" name="cant" value="0" /></td>');
						$row.append('<td><select class="form-control" name="tipo">'+
							'<option value="E">Estado</option>'+
							'<option value="R">Receta</option>'+
							'<option value="ER">Estado y Receta</option>'+
						'</select></td>');
						$row.find('[name=fec]').datepicker();
						p.$w.find('[name=gridMed] tbody').append($row);
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowAum: function(p){
		new K.Modal({ 
			id: 'windowEdit',
			title: 'Aumentar Medicinas',
			contentURL: 'ch/cont/edit',
			allScreen: true,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							medicinas: []
						};
						for(var i=0; i<p.$w.find('[name=gridMed] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridMed] tbody .item').eq(i),
							tmp = {
								fec: $row.find('[name=fec]').val(),
								cant: $row.find('[name=cant]').val(),
							};
							if($row.find('[name=med]').length==1){
								if($row.find('[name=med]').val()==''){
									$row.find('[name=med]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar una medicina!',
										type: 'error'
									});
								}else tmp.med = $row.find('[name=med]').val();
							}
							if(tmp.cant==''){
								$row.find('[name=cant]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un stock!',
									type: 'error'
								});
							}
							if(parseInt(tmp.cant)<0){
								$row.find('[name=cant]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un stock mayor a 0!',
									type: 'error'
								});
							}
							if(tmp.fec==''){
								$row.find('[name=med]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar la fecha de ingreso!',
									type: 'error'
								});
							}
							tmp.tipo = $row.find('[name=tipo] option:selected').val();
							data.medicinas.push(tmp);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ch/cont/save_aum",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Control actualizado!"});
							chCont.init();
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
				p.$w = $('#windowEdit');
				new K.grid({
					$el: p.$w.find('[name=gridMed]'),
					cols: ['Medicina','Stock Actual','Fecha de Incremento','Cantidad a Aumentar','Realizado por',''],
					stopLoad: true,
					pagination: false,
					search: false,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Medicina</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" class="form-control" name="med" placechlder="Ejem: Clonazepan 2mg, Valprax 500mg, etc." /></td>');
							$row.append('<td><kbd>0</kbd></td>');
							$row.append('<td><input type="text" class="form-control" name="fec" value="'+ciHelper.date.get.now_ymd()+'" /></td>');
							$row.append('<td><input type="text" class="form-control" name="cant" value="1" /></td>');
							$row.append('<td><select class="form-control" name="tipo">'+
								'<option value="E">Estado</option>'+
								'<option value="R">Receta</option>'+
								'<option value="ER">Estado y Receta</option>'+
							'</select></td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=fec]').datepicker();
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridMed] tbody').append($row);
						});
					}
				});
				K.block();
				$.post('ch/cont/get',{_id: p.id},function(data){
					p.$w.find('[name=btnHist]').remove();
					p.$w.find('[name=btnAct]').remove();
					p.$w.find('[name=btnSel]').remove();
					p.$w.find('[name=cod_hist]').val(data.cod_hist).attr('disabled','disabled');
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.paciente);
					for(var i=0; i<data.medicinas.length; i++){
						var $row = $('<tr class="item">');
						$row.append('<td><code>'+data.medicinas[i].med+'</code></td>');
						$row.append('<td><kbd>'+data.medicinas[i].stock+'</kbd></td>');
						$row.append('<td><input type="text" class="form-control" name="fec" value="'+ciHelper.date.get.now_ymd()+'" /></td>');
						$row.append('<td><input type="text" class="form-control" name="cant" value="0" /></td>');
						$row.append('<td><select class="form-control" name="tipo">'+
							'<option value="E">Estado</option>'+
							'<option value="R">Receta</option>'+
							'<option value="ER">Estado y Receta</option>'+
						'</select></td>');
						$row.append('<td>');
						$row.find('[name=fec]').datepicker();
						p.$w.find('[name=gridMed] tbody').append($row);
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Panel({
			title: 'Control de Medicinas de Paciente '+p.nomb,
			contentURL: 'ch/cont/edit',
			buttons: {
				'Regresar': {
					icon: 'fa-rotate-left',
					type: 'warning',
					f: function(){
						chCont.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridMed]'),
					cols: ['Medicina','Stock Actual','Ver Historial de Movimientos'],
					stopLoad: true,
					pagination: false,
					search: false
				});
				$.post('ch/cont/get',{_id: p.id},function(data){
					p.$w.find('[name=btnHist]').remove();
					p.$w.find('[name=btnAct]').remove();
					p.$w.find('[name=btnSel]').remove();
					p.$w.find('[name=cod_hist]').val(data.cod_hist).attr('disabled','disabled');
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.paciente);
					for(var i=0; i<data.medicinas.length; i++){
						var $row = $('<tr class="item">');
						$row.append('<td><code>'+data.medicinas[i].med+'</code></td>');
						$row.append('<td><kbd>'+data.medicinas[i].stock+'</kbd></td>');
						$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-search"></i></button></td>');
						$row.find('button').click(function(){
							var med = $(this).closest('.item').data('data');
							new K.Modal({
								id: 'windowDetails',
								title: 'Historial de <kbd>'+med.med+'</kbd>',
								content: '<div name="grid"></div>',
								width: 700,
								height: 400,
								onContentLoaded: function(){
									new K.grid({
										$el: $('#windowDetails [name=grid]'),
										cols: ['Fecha','Cantidad movida','Stock posterior','Realizado por','Trabajador'],
										stopLoad: true,
										pagination: false,
										search: false
									});
									for(var i=0; i<med.hist.length; i++){
										var hist = med.hist[i],
										$row = $('<tr class="item">');
										$row.append('<td>'+ciHelper.date.format.bd_ymdhi(hist.fec)+'</td>');
										$row.append('<td>'+hist.cant+'</td>');
										$row.append('<td>'+hist.stock+'</td>');
										$row.append('<td>'+chCont.tipoMov[hist.tipo]+'</td>');
										$row.append('<td>'+mgEnti.formatName(hist.trabajador)+'</td>');
										$('#windowDetails [name=grid] tbody').append($row);
									}
								}
							});
						});
						$row.data('data',data.medicinas[i]);
						p.$w.find('[name=gridMed] tbody').append($row);
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowDeshacer: function(p){
		new K.Panel({
			title: 'Deshacer Medicinas de Paciente '+p.nomb,
			contentURL: 'ch/cont/edit',
			buttons: {
				'Regresar': {
					icon: 'fa-rotate-left',
					type: 'warning',
					f: function(){
						chCont.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridMed]'),
					cols: ['Medicina','Stock Actual','Deshacer &uacute;ltimo Movimiento'],
					stopLoad: true,
					pagination: false,
					search: false
				});
				$.post('ch/cont/get',{_id: p.id},function(data){
					p.$w.find('[name=btnHist]').remove();
					p.$w.find('[name=btnAct]').remove();
					p.$w.find('[name=btnSel]').remove();
					p.$w.find('[name=cod_hist]').val(data.cod_hist).attr('disabled','disabled');
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.paciente);
					for(var i=0; i<data.medicinas.length; i++){
						var $row = $('<tr class="item">');
						$row.append('<td><code>'+data.medicinas[i].med+'</code></td>');
						$row.append('<td><kbd>'+data.medicinas[i].stock+'</kbd></td>');
						$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-undo"></i> Deshacer ultimo Movimiento</button></td>');
						$row.find('button').click(function(){
							var med = $(this).closest('.item').data('data');
							console.log(med);
							ciHelper.confirm('&#191;Desea <b>DESHACER</b> el ultimo movimiento del paciente <b>'+K.tmp.find('td:eq(1)').html()+'</b>&#63;',
							function(){
								K.sendingInfo();
								$.post('ch/cont/save_deshacer',{_id: p.id,med: med.med},function(){
									K.clearNoti();
									K.notification({title: 'Medicinas Devueltas',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
									chCont.init();
								});
							},function(){
								$.noop();
							},'Deshacer Operaci&oacute;n');
						});
						$row.data('data',data.medicinas[i]);
						p.$w.find('[name=gridMed] tbody').append($row);
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return chCont;
	}
);