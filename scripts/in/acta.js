inActa = {
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
	dbRel: function(item){
		return {
			_id: item._id.$id,
			num: item.num,
			arrendatario: mgEnti.dbRel(item.arrendatario),
			inmueble: inInmu.dbRel(item.inmueble)
		};
	},
	init: function(){
		K.initMode({
			mode: 'in',
			action: 'inActa',
			titleBar: {
				title: 'Actas de Conciliaci&oacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Abrev.',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'in/acta/lista',
					params: {},
					itemdescr: 'acta(s) de conciliaci&oacute;n',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							inActa.windowNew();
						});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+inActa.states[data.estado].label+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.arrendatario)+'</td>');
						$row.append('<td>'+data.inmueble.direccion+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inActa.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEli", {
							onShowMenu: function($row, menu) {
								$('#conMenListEli_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEli_ver': function(t) {
									inActa.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEli_edi': function(t) {
									inActa.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEli_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Acta de <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/acta/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Acta de Conciliacion Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inActa.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Acta de Conciliacion');
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
			title: 'Nueva Acta de Conciliaci&oacute;n',
			contentURL: 'in/acta/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cuotas: p.$w.find('[name=cuotas]').val(),
							num: p.$w.find('[name=num]').val(),
							arrendatario: p.$w.find('[name=arrendatario]').data('data'),
							inmueble: p.$w.find('[name=inmueble]').data('data'),
							observacion: p.$w.find('[name=obsrv]').val(),
							items: []
						};
						if(data.arrendatario==null){
							p.$w.find('[name=btnArre]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el arrendatario!',type: 'error'});
						}else data.arrendatario = mgEnti.dbRel(data.arrendatario);
						if(data.inmueble==null){
							p.$w.find('[name=btnInm]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un inmueble!',type: 'error'});
						}else data.inmueble = inInmu.dbRel(data.inmueble);
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						if(data.cuotas==''){
							p.$w.find('[name=cuotas]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el total de cuotas de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						for(var i=0,j=parseInt(data.cuotas); i<j; i++){
							var $table = p.$w.find('[name=grillas] tbody').eq(i),
							item = {
								num: i+1,
								fecven: $table.find('[name=fecven]').val(),
								conceptos: [],
								total: $table.find('[name=total]').data('data')
							};
							if(item.fecven==''){
								p.$w.find('[name=fecven]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de vencimiento!',type: 'error'});
							}
							for(var ii=0,jj=$table.find('[name=monto]').length; ii<jj; ii++){
								if($table.find('[name=descr]').eq(ii).val()==''){
									$table.find('[name=descr]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
								}
								if($table.find('[name=monto]').eq(ii).val()==''){
									$table.find('[name=monto]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
								}
								item.conceptos.push({
									monto: parseFloat($table.find('[name=monto]').eq(ii).val()),
									descr: $table.find('[name=descr]').eq(ii).val(),
									tipo: $table.find('[name=tipo] option:selected').eq(ii).val()
								});
							}
							data.items.push(item);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/acta/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Acta agregada!"});
							inActa.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inActa.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnArre]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data)).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnInm]').click(function(){
					inInmu.windowSelect({callback: function(data){
						p.$w.find('[name=inmueble]').html(data.direccion).data('data',data);
					}});
				});
				p.$w.find('[name=cuotas]').change(function(){
					var cuotas = $(this).val();
					if(cuotas=='') cuotas = 0;
					else cuotas = parseInt(cuotas);
					p.$w.find('[name=grillas]').empty();
					for(var i=0,j=cuotas; i<j; i++){
						p.$w.find('[name=grillas]').append('<div>');
						var $tmp = p.$w.find('[name=grillas] div:last');
						new K.grid({
							$el: $tmp,
							search: false,
							pagination: false,
							cols: ['Concepto a Cobrar','Tipo','Total',''],
							onlyHtml: true
						});
						/*
						 * fecha de vencimiento
						 */
						var $row = $('<tr class="item">');
						$row.append('<td>Fecha de Vencimiento</td>');
						$row.append('<td colspan="3"><input class="form-control" type="text" name="fecven"/></td>');
						$row.find('[name=fecven]').val(K.date()).datepicker();
						$tmp.find('tbody').append($row);
						/*
						 * TOTAL
						 */
						var $row = $('<tr class="item">');
						$row.append('<td>Total Cuota '+(i+1)+'</td>');
						$row.append('<td colspan="2"><span class="form-control" name="total">0</span></td>');
						$row.append('<td><button class="btn btn-info" name="btnAgr"><i class="fa fa-plus"></i></button></td>');
						$row.find('[name=btnAgr]').click(function(){
							var $row = $('<tr class="item">'),
							$table = $(this).closest('table');
							$row.append('<td><input class="form-control" type="text" name="descr"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="0"/></td>');
							$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric().change();
							$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								var $row = $(this).closest('.item');
								$row.remove();
								p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
								p.$w.find('[name=monto]').change();
							});
							$table.find('tbody').append($row);
						});
						$tmp.find('tbody').append($row);
					}
					$(this).val(cuotas);
					p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
					p.$w.find('[name=btnAgr]').click();
				}).keyup(function(){
					$(this).change();
				}).numeric().change();
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Panel({ 
			title: 'Editar Acta de Conciliaci&oacute;n',
			contentURL: 'in/acta/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cuotas: p.$w.find('[name=cuotas]').val(),
							num: p.$w.find('[name=num]').val(),
							arrendatario: p.$w.find('[name=arrendatario]').data('data'),
							inmueble: p.$w.find('[name=inmueble]').data('data'),
							observacion: p.$w.find('[name=obsrv]').val(),
							items: []
						};
						if(data.arrendatario==null){
							p.$w.find('[name=btnArre]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el arrendatario!',type: 'error'});
						}else data.arrendatario = mgEnti.dbRel(data.arrendatario);
						if(data.inmueble==null){
							p.$w.find('[name=btnInm]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un inmueble!',type: 'error'});
						}else data.inmueble = inInmu.dbRel(data.inmueble);
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						if(data.cuotas==''){
							p.$w.find('[name=cuotas]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el total de cuotas de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						for(var i=0,j=parseInt(data.cuotas); i<j; i++){
							var $table = p.$w.find('[name=grillas] tbody').eq(i),
							item = {
								num: i+1,
								fecven: $table.find('[name=fecven]').val(),
								conceptos: [],
								total: $table.find('[name=total]').data('data')
							};
							if(item.fecven==''){
								p.$w.find('[name=fecven]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de vencimiento!',type: 'error'});
							}
							for(var ii=0,jj=$table.find('[name=monto]').length; ii<jj; ii++){
								if($table.find('[name=descr]').eq(ii).val()==''){
									$table.find('[name=descr]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
								}
								if($table.find('[name=monto]').eq(ii).val()==''){
									$table.find('[name=monto]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
								}
								item.conceptos.push({
									monto: parseFloat($table.find('[name=monto]').eq(ii).val()),
									descr: $table.find('[name=descr]').eq(ii).val(),
									tipo: $table.find('[name=tipo] option:selected').eq(ii).val()
								});
							}
							data.items.push(item);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/acta/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Acta actualizada!"});
							inActa.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inActa.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=btnArre]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data)).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnInm]').click(function(){
					inInmu.windowSelect({callback: function(data){
						p.$w.find('[name=inmueble]').html(data.direccion).data('data',data);
					}});
				});
				p.$w.find('[name=cuotas]').change(function(){
					var cuotas = $(this).val();
					if(cuotas=='') cuotas = 0;
					else cuotas = parseInt(cuotas);
					p.$w.find('[name=grillas]').empty();
					for(var i=0,j=cuotas; i<j; i++){
						p.$w.find('[name=grillas]').append('<div>');
						var $tmp = p.$w.find('[name=grillas] div:last');
						new K.grid({
							$el: $tmp,
							search: false,
							pagination: false,
							cols: ['Concepto a Cobrar','Tipo','Total',''],
							onlyHtml: true
						});
						/*
						 * fecha de vencimiento
						 */
						var $row = $('<tr class="item">');
						$row.append('<td>Fecha de Vencimiento</td>');
						$row.append('<td colspan="3"><input class="form-control" type="text" name="fecven"/></td>');
						$row.find('[name=fecven]').val(K.date()).datepicker();
						$tmp.find('tbody').append($row);
						/*
						 * TOTAL
						 */
						var $row = $('<tr class="item">');
						$row.append('<td>Total Cuota '+(i+1)+'</td>');
						$row.append('<td colspan="2"><span class="form-control" name="total">0</span></td>');
						$row.append('<td><button class="btn btn-info" name="btnAgr"><i class="fa fa-plus"></i></button></td>');
						$row.find('[name=btnAgr]').click(function(){
							var $row = $('<tr class="item">'),
							$table = $(this).closest('table');
							$row.append('<td><input class="form-control" type="text" name="descr"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="0"/></td>');
							$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric().change();
							$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								var $row = $(this).closest('.item');
								$row.remove();
								p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
								p.$w.find('[name=monto]').change();
							});
							$table.find('tbody').append($row);
						});
						$tmp.find('tbody').append($row);
					}
					$(this).val(cuotas);
					p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
					p.$w.find('[name=btnAgr]').click();
				}).keyup(function(){
					$(this).change();
				}).numeric().change();
				$.post('in/acta/get',{_id: p.id},function(data){
					p.$w.find('[name=cuotas]').val(data.cuotas).change();
					p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data.arrendatario)).data('data',data.arrendatario);
					p.$w.find('[name=inmueble]').html(data.inmueble.direccion).data('data',data.inmueble);
					p.$w.find('[name=num]').val(data.num);
					p.$w.find('[name=obsrv]').val(data.observacion);
					for(var i=0,j=data.items.length; i<j; i++){
						var $table = p.$w.find('.fuelux').eq(i),
						result = data.items[i];
						$table.find('[name=fecven]').val(ciHelper.date.format.bd_ymd(result.fecven));
						$table.find('[name=total]').html(ciHelper.formatMon(result.total));
						$table.find('tbody tr:last').remove();
						for(var ii=0,jj=result.conceptos.length; ii<jj; ii++){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="descr" value="'+result.conceptos[ii].descr+'"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="'+result.conceptos[ii].monto+'"/></td>');
							$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric();
							$row.find('[name=tipo]').selectVal(result.conceptos[ii].tipo);
							$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								var $row = $(this).closest('.item');
								$row.remove();
								p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
								p.$w.find('[name=monto]').change();
							});
							$table.find('tbody').append($row);
							$table.find('[name=monto]:last').change();
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = {};
		new K.Panel({ 
			title: 'Acta de Conciliaci&oacute;n',
			contentURL: 'in/acta/edit',
			buttons: {
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inActa.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnArre]').remove();
				p.$w.find('[name=btnInm]').remove();
				p.$w.find('[name=num]').attr('disabled','disabled');
				p.$w.find('[name=cuotas]').change(function(){
					var cuotas = $(this).val();
					if(cuotas=='') cuotas = 0;
					else cuotas = parseInt(cuotas);
					p.$w.find('[name=grillas]').empty();
					for(var i=0,j=cuotas; i<j; i++){
						p.$w.find('[name=grillas]').append('<div>');
						var $tmp = p.$w.find('[name=grillas] div:last');
						new K.grid({
							$el: $tmp,
							search: false,
							pagination: false,
							cols: ['Concepto a Cobrar','Tipo','Total'],
							onlyHtml: true
						});
						/*
						 * fecha de vencimiento
						 */
						var $row = $('<tr class="item">');
						$row.append('<td>Fecha de Vencimiento</td>');
						$row.append('<td colspan="3"><input class="form-control" type="text" name="fecven" disabled="disabled"/></td>');
						$row.find('[name=fecven]').val(K.date());
						$tmp.find('tbody').append($row);
						/*
						 * TOTAL
						 */
						var $row = $('<tr class="item">');
						$row.append('<td>Total Cuota '+(i+1)+'</td>');
						$row.append('<td colspan="2"><span class="form-control" name="total">0</span></td>');
						$row.append('<td><button class="btn btn-info" name="btnAgr"><i class="fa fa-plus"></i></button></td>');
						$row.find('[name=btnAgr]').click(function(){
							var $row = $('<tr class="item">'),
							$table = $(this).closest('table');
							$row.append('<td><input class="form-control" type="text" name="descr" disabled="disabled"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="0" disabled="disabled"/></td>');
							$row.append('<td><select class="form-control" name="tipo" disabled="disabled"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric().change();
							$table.find('tbody').append($row);
						}).hide();
						$tmp.find('tbody').append($row);
					}
					$(this).val(cuotas);
					p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
					p.$w.find('[name=btnAgr]').click();
				}).hide();
				$.post('in/acta/get',{_id: p.id},function(data){
					p.$w.find('[name=cuotas]').val(data.cuotas).change();
					p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data.arrendatario)).data('data',data.arrendatario);
					p.$w.find('[name=inmueble]').html(data.inmueble.direccion).data('data',data.inmueble);
					p.$w.find('[name=num]').val(data.num);
					for(var i=0,j=data.items.length; i<j; i++){
						var $table = p.$w.find('.fuelux').eq(i),
						result = data.items[i];
						$table.find('[name=fecven]').val(ciHelper.date.format.bd_ymd(result.fecven));
						$table.find('[name=total]').html(ciHelper.formatMon(result.total));
						$table.find('tbody tr:last').remove();
						for(var ii=0,jj=result.conceptos.length; ii<jj; ii++){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="descr" disabled="disabled" value="'+result.conceptos[ii].descr+'"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" disabled="disabled" value="'+result.conceptos[ii].monto+'"/></td>');
							$row.append('<td><select class="form-control" disabled="disabled" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric();
							$row.find('[name=tipo]').val(result.conceptos[ii].tipo);
							$table.find('tbody').append($row);
							$table.find('[name=monto]:last').change();
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','in/inmu'],
	function(mgEnti,ctPcon,inInmu){
		return inActa;
	}
);