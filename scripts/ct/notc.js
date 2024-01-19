ctNotc = {
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
			nomb: item.nomb,
			cuenta: {
				_id: item.cuenta._id.$id,
				cod: item.cuenta.cod,
				descr: item.cuenta.descr
			}
		};
	},
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctNotc',
			titleBar: {
				title: 'Notas de Contabilidad'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				$.post('ct/tnot/all',function(tnots){
					var $grid = new K.grid({
						cols: ['','Numero','Tipo','Periodo','Debe','Haber','Registrado'],
						data: 'ct/notc/lista',
						params: {periodo:moment().format('YYYY'), mes: moment().format('M'), tipo:tnots[0]._id.$id},
						itemdescr: 'nota(s) de Contabilidad',
						toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>'
						+'<form class="container-fluid">'
							+'<div class="row">'
								+'<div class="col-md-4">'
									+'<div class="form-group">'
										+'<label>Tipo: </label>'
										+'<select name="tipo" class="form-control">'
										+'</select>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-4">'
									+'<div class="form-group">'
										+'<label>Tipo: </label>'
										+'<select name="ano" class="form-control">'
										+'<option value="2016">2016</option>'
										+'<option value="2017">2017</option>'
										+'<option value="2018">2018</option>'
										+'<option value="2019">2019</option>'
										+'<option value="2020">2020</option>'
										+'<option value="2021">2021</option>'
										+'</select>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-4">'
									+'<div class="form-group">'
										+'<label>Tipo: </label>'
										+'<select name="mes" class="form-control">'
										+'<option value="1">Enero</option>'
										+'<option value="2">Febrero</option>'
										+'<option value="3">Marzo</option>'
										+'<option value="4">Abril</option>'
										+'<option value="5">Mayo</option>'
										+'<option value="6">Junio</option>'
										+'<option value="7">Julio</option>'
										+'<option value="8">Agosto</option>'
										+'<option value="9">Setiembre</option>'
										+'<option value="10">Octubre</option>'
										+'<option value="11">Noviembre</option>'
										+'<option value="12">Diciembre</option>'
										+'</select>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'<form>',
						onContentLoaded: function($el){
							$el.find('[name=btnAgregar]').click(function(){
								ctNotc.windowNew({goBack: function(){
									ctNotc.init();
								}});
							});
							$el.find('[name=ano]').val(moment().format('YYYY'));
							$el.find('[name=mes]').val(moment().format('M'));
							var $cbo = $el.find('[name=tipo]');
							if(tnots!=null){
								for(var i=0; i<tnots.length; i++){
									$cbo.append('<option sigla="'+tnots[i].sigla+'" value="'+tnots[i]._id.$id+'" >'+tnots[i].nomb+'</option>');
								}
							}else{
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear tipos de notas!',type: 'error'});
							}
							$el.find('[name=ano], [name=mes], [name=tipo]').change(function(){
								$grid.reinit({params:{periodo:$el.find('[name=ano] :selected').val(), mes: $el.find('[name=mes] :selected').val(), tipo: $el.find('[name=tipo] :selected').val()}});
							});
						},
						search:false,
						onLoading: function(){ K.block(); },
						onComplete: function(){ K.unblock(); },
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+ciHelper.codigos(data.num,3)+'</td>');
							$row.append('<td>'+data.tipo.nomb+'</td>');
							$row.append('<td>'+data.periodo.mes+'-'+data.periodo.ano+'</td>');
							$row.append('<td>'+K.round(data.total_debe,2)+'</td>');
							$row.append('<td>'+K.round(data.total_haber,2)+'</td>');
							$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
							$row.data('id',data._id.$id).dblclick(function(){
								ctNotc.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
							}).data('estado',data.estado).contextMenu("conMenListEli", {
								onShowMenu: function($row, menu) {
									$('#conMenListEli_ver',menu).remove();
									
									return menu;
								},
								bindings: {
									'conMenListEli_ver': function(t) {
										ctNotc.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
									},
									'conMenListEli_edi': function(t) {
										ctNotc.windowNew({id: K.tmp.data('id'), goBack: function(){
											ctNotc.init();
										}});
									},
									'conMenListEli_eli': function(t) {
										
									},
									'conMenListEli_imp': function(t) {
										window.open('ct/repo/notc?id='+K.tmp.data('id'));
									}
								}
							});
							return $row;
						}
					});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		if(p.goBack!=null) K.history.push({f: p.goBack});
		p.calculate = function(){
			var total_debe = 0;
			var total_haber = 0;
			if(p.$w.find('[name=grid_detalle] tbody tr').length>0){
				for(var i=0;i<p.$w.find('[name=grid_detalle] tbody tr').length;i++){
					var $row = p.$w.find('[name=grid_detalle] tbody tr').eq(i);
					var _data = $row.find('[name=item_cuenta_name]').data('data');
					var item_debe = $row.find('[name=item_debe]').val();
					if(!parseFloat(item_debe)) item_debe = "0";
					item_debe = parseFloat(item_debe);
					var item_haber = $row.find('[name=item_haber]').val();
					if(!parseFloat(item_haber)) item_haber = "0";
					item_haber = parseFloat(item_haber);
					total_debe+=item_debe;
					total_haber+=item_haber;
				}
			}
			p.$w.find('[name=total_debe]').val(K.round(total_debe,2));
			p.$w.find('[name=total_haber]').val(K.round(total_haber,3));
		};
		new K.Panel({ 
			title: 'Nueva Nota de Contabilidad',
			contentURL: 'ct/notc/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							num: p.$w.find('[name=num]').val(),
							tipo: {
								_id:p.$w.find('[name=tipo] :selected').val(),
								nomb: p.$w.find('[name=tipo] :selected').text(),
								sigla: p.$w.find('[name=tipo] :selected').attr('sigla')
							},
							periodo: {
								mes: p.$w.find('[name=mes] :selected').val(),
								ano: p.$w.find('[name=ano] :selected').val()
							},
							total_debe: p.$w.find('[name=total_debe]').val(),
							total_haber: p.$w.find('[name=total_haber]').val(),
							cuentas: []
						};
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un numero!',type: 'error'});
						}
						if(p.$w.find('[name=grid_detalle] tbody tr').length>0){
							for(var i=0;i<p.$w.find('[name=grid_detalle] tbody tr').length;i++){
								var $row = p.$w.find('[name=grid_detalle] tbody tr').eq(i);
								//var _data = $row.find('[name=cuenta]').data('select2').data()[0].data;
								var _data = $row.find('[name=item_cuenta_name]').data('data');
								if(_data!=null){
									var item_debe = $row.find('[name=item_debe]').val();
									if(!parseFloat(item_debe)) item_debe = "0";
									item_debe = parseFloat(item_debe);
									var item_haber = $row.find('[name=item_haber]').val();
									if(!parseFloat(item_haber)) item_haber = "0";
									item_haber = parseFloat(item_haber);

									var monto = item_debe;
									var tipo = 'D';
									if(item_haber>0){
										monto = item_haber;
										tipo = 'H';
									}
									var _cuenta = {
										cuenta:ctPcon.dbRel(_data),
										tipo:tipo,
										monto:monto,
										ultimo:1
									};
									data.cuentas.push(_cuenta);
								}
							}
						}
						data.post_glosa_head = {
							col1:p.$w.find('[name=col_1]').val(),
							col2:p.$w.find('[name=col_2]').val(),
							col3:p.$w.find('[name=col_3]').val(),
							col4:p.$w.find('[name=col_4]').val(),
							col5:p.$w.find('[name=col_5]').val(),
							col6:p.$w.find('[name=col_6]').val(),
						};
						data.post_glosa_body = new Array;
						for(i=0;i<p.$w.find('[name=grid_observ] tbody tr').length;i++){
							var $pgrow = p.$w.find('[name=grid_observ] tbody tr').eq(i);
							data.post_glosa_body.push({
								col1:$pgrow.find('[name=col_b_1]').val(),
								col2:$pgrow.find('[name=col_b_2]').val(),
								col3:$pgrow.find('[name=col_b_3]').val(),
								col4:$pgrow.find('[name=col_b_4]').val(),
								col5:$pgrow.find('[name=col_b_5]').val(),
								col6:$pgrow.find('[name=col_b_6]').val(),
							});
						}
						if(p.id!=null){
							data._id = p.id;
						}
						/*console.log(data);
						return false;*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ct/notc/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "La nota de contabilidad se guardo con &eacute;xito!"});
							ctNotc.init();
							K.closeWindow(p.$w.attr('id'));
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
				new K.grid({
					$el: p.$w.find('[name=grid_detalle]'),
					search: false,
					pagination: false,
					cols: [{width:'150',descr:'Codigo'},{width:'250',descr:'Descripci&oacute;n'},{width:'100',descr:'Debe'},{width:'100',descr:'Debe'},''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar nueva Fila</button>',
					onContentLoaded: function($el){
						var $row = $('<tr class="item" />');
						$row.append('<td colspan="2" style="text-align:right;">Total</td>');
						$row.append('<td><input type="text" name="total_debe" value="0.00" class="form-control"></td>');
						$row.append('<td><input type="text" name="total_haber" value="0.00" class="form-control"></td>');
						$row.append('<td />');
						var $tfoot = $('<tfoot />');
						$tfoot.append($row);
						p.$w.find('[name=grid_detalle] tbody').after($tfoot);
						$el.find('button').click(function(){
							var $row = $('<tr class="item" />');
							$row.append('<td><select style="width:100%;" class="form-control" name="cuenta"></select></td>')
							$row.append('<td><span name="item_cuenta_name"></span></td>');
							$row.append('<td><input type="text" name="item_debe" value="0.00" class="form-control"></td>');
							$row.append('<td><input type="text" name="item_haber" value="0.00" class="form-control"></td>');
							$row.append('<td><button name="btnDeleteRow" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>')
							$row.find('[name=item_debe]').keyup(function(){
								p.calculate();
							});
							$row.find('[name=item_haber]').keyup(function(){
								p.calculate();
							});
							$row.find('[name=cuenta]').select2({
								ajax: {
									url: "ct/pcon/lista2",
									dataType: 'json',
									delay: 250,
									data: function (params) {
										return {
											texto: params.term,
											page_rows: 5,
											page: 1,
											autocomplete:'1'
										};
									},
									processResults: function (data, params) {
										var results = [];
										if(data.items!=null){
											$.each(data.items,function(i,item){
												results.push({
													id:item._id.$id,
													text:item.cod,
													data:item
												});
											})	
										}
										return {
											results: results,
										};
									},
									cache: true
								},
								escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
								minimumInputLength: 3,
								templateResult: function(item){
									return '<span>'+item.text+'</span>';
								},
								templateSelection: function(item){
									return '<span>'+item.data.cod+'</span>';
								}
							}).change(function(){
								//var data = $(this).closest('tr').find('[name=cuenta]').data('select2').data()[0];
								var _$row = $(this).closest('tr');
								var data = $(this).data('select2').data()[0];
								if(data!=null){
									_$row.find('[name=item_cuenta_name]').data('data',data.data).html(data.data.descr);
								}
							});
							$row.find('[name=btnDeleteRow]').click(function(){
								$(this).closest('tr').remove();
							});
							p.$w.find('[name=grid_detalle] tbody').append($row);
						});
					}
				});
				p.$w.find('[name=btnObservAddRow]').click(function(){
					var $row = $('<tr class="item" />');
					$row.append('<td><input type="text" name="col_b_1" class="form-control"></td>');
					$row.append('<td><input type="text" name="col_b_2" class="form-control"></td>');
					$row.append('<td><input type="text" name="col_b_3" class="form-control"></td>');
					$row.append('<td><input type="text" name="col_b_4" class="form-control"></td>');
					$row.append('<td><input type="text" name="col_b_5" class="form-control"></td>');
					$row.append('<td><input type="text" name="col_b_6" class="form-control"></td>');
					$row.append('<td><button class="btn btn-danger" name="btnObservDeleteRow"><i class="fa fa-trash"></i></button></td>');
					$row.find('[name=btnObservDeleteRow]').click(function(){
						$(this).closest('tr').remove();
					});
					p.$w.find('[name=grid_observ] tbody').append($row);
				});
				$.post('ct/tnot/all',function(tnots){
					var $cbo = p.$w.find('[name=tipo]');
					if(tnots!=null){
						for(var i=0; i<tnots.length; i++){
							$cbo.append('<option sigla="'+tnots[i].sigla+'" value="'+tnots[i]._id.$id+'" >'+tnots[i].nomb+'</option>');
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear tipos de notas!',type: 'error'});
					}
					p.$w.find('[name=periodo]').change(function(){
						p.$w.find('[name=tipo]').change();
					});
					/**** UPDATE CASE **/
					if(p.id!=null){
						$.post('ct/notc/get',{_id:p.id},function(data){
							p.$w.find('[name=num]').val(data.num);
							p.$w.find('[name=tipo]').val(data.tipo._id.$id);
							p.$w.find('[name=mes]').val(data.periodo.mes);
							p.$w.find('[name=ano]').val(data.periodo.ano),
							p.$w.find('[name=total_debe]').val(K.round(data.total_debe,2));
							p.$w.find('[name=total_haber]').val(K.round(data.total_haber,2));
							if(data.cuentas!=null){
								if(data.cuentas.length>0){
									for(var i=0;i<data.cuentas.length;i++){
										var $row = $('<tr class="item" />');
										$row.append('<td><select style="width:100%;" class="form-control" name="cuenta"></select></td>')
										$row.append('<td><span name="item_cuenta_name"></span></td>');
										$row.append('<td><input type="text" name="item_debe" value="0.00" class="form-control"></td>');
										$row.append('<td><input type="text" name="item_haber" value="0.00" class="form-control"></td>');
										$row.append('<td><button name="btnDeleteRow" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>')


										$row.find('[name=item_debe]').keyup(function(){
											p.calculate();
										});
										$row.find('[name=item_haber]').keyup(function(){
											p.calculate();
										});
										$row.find('[name=cuenta]').select2({
											placeholder:{
												id:data.cuentas[i].cuenta._id.$id,
												text:data.cuentas[i].cuenta.descr,
												data:data.cuentas[i].cuenta
											},
											ajax: {
												url: "ct/pcon/lista2",
												dataType: 'json',
												delay: 250,
												data: function (params) {
													return {
														texto: params.term,
														page_rows: 5,
														page: 1,
														autocomplete:'1'
													};
												},
												processResults: function (data, params) {
													var results = [];
													if(data.items!=null){
														$.each(data.items,function(i,item){
															results.push({
																id:item._id.$id,
																text:item.cod,
																data:item
															});
														})	
													}
													return {
														results: results,
													};
												},
												cache: true
											},
											escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
											minimumInputLength: 3,
											templateResult: function(item){
												return '<span>'+item.text+'</span>';
											},
											templateSelection: function(item){
												return '<span>'+item.data.cod+'</span>';
											}
										}).change(function(){
											//var data = $(this).closest('tr').find('[name=cuenta]').data('select2').data()[0];
											var _$row = $(this).closest('tr');
											var _data = $(this).data('select2').data()[0];
											if(_data!=null){
												_$row.find('[name=item_cuenta_name]').data('data',_data.data).html(_data.data.descr);
												//_$row.find('[name=item_cuenta_name]').html(data.data.descr);
											}
										});
										/*$row.find('[name=cuenta]').select2('val', {
											id:data.cuentas[i].cuenta._id.$id,
											text:data.cuentas[i].cuenta.descr,
											data:data.cuentas[i].cuenta
										});
										$row.find('[name=cuenta]').trigger('change.select2');*/
										$row.find('[name=btnDeleteRow]').click(function(){
											$(this).closest('tr').remove();
										});

										$row.find('[name=item_cuenta_name]').data('data',data.cuentas[i].cuenta).html(data.cuentas[i].cuenta.descr);
										if(data.cuentas[i].tipo=='D'){
											$row.find('[name=item_debe]').val(K.round(data.cuentas[i].monto,2));
										}else{
											$row.find('[name=item_haber]').val(K.round(data.cuentas[i].monto,2));
										}
										/** ./Fill item */

										p.$w.find('[name=grid_detalle] tbody').append($row);
									}
								}
							}
							if(data.post_glosa_head){
								p.$w.find('[name=col_1]').val(data.post_glosa_head.col1);
								p.$w.find('[name=col_2]').val(data.post_glosa_head.col2);
								p.$w.find('[name=col_3]').val(data.post_glosa_head.col3);
								p.$w.find('[name=col_4]').val(data.post_glosa_head.col4);
								p.$w.find('[name=col_5]').val(data.post_glosa_head.col5);
								p.$w.find('[name=col_6]').val(data.post_glosa_head.col6);
							}
							if(data.post_glosa_body){
								for(var i in data.post_glosa_body){
									var $row = $('<tr class="item" />');
									$row.append('<td><input type="text" name="col_b_1" class="form-control"></td>');
									$row.append('<td><input type="text" name="col_b_2" class="form-control"></td>');
									$row.append('<td><input type="text" name="col_b_3" class="form-control"></td>');
									$row.append('<td><input type="text" name="col_b_4" class="form-control"></td>');
									$row.append('<td><input type="text" name="col_b_5" class="form-control"></td>');
									$row.append('<td><input type="text" name="col_b_6" class="form-control"></td>');
									$row.append('<td><button class="btn btn-danger" name="btnObservDeleteRow"><i class="fa fa-trash"></i></button></td>');
									$row.find('[name=col_b_1]').val(data.post_glosa_body[i].col1);
									$row.find('[name=col_b_2]').val(data.post_glosa_body[i].col2);
									$row.find('[name=col_b_3]').val(data.post_glosa_body[i].col3);
									$row.find('[name=col_b_4]').val(data.post_glosa_body[i].col4);
									$row.find('[name=col_b_5]').val(data.post_glosa_body[i].col5);
									$row.find('[name=col_b_6]').val(data.post_glosa_body[i].col6);

									$row.find('[name=btnObservDeleteRow]').click(function(){
										$(this).closest('tr').remove();
									});
									p.$w.find('[name=grid_observ] tbody').append($row);
								}
							}
							K.unblock();
						},'json');
					}else{
						$cbo.change(function(){
							$.post('ct/notc/get_num',{
								periodo: p.$w.find('[name=ano] :selected').val(),
								tipo: p.$w.find('[name=tipo] option:selected').val()
							},function(data){
								p.$w.find('[name=num]').val(data.num);
								K.unblock();
							},'json');
						}).change();	
					}
					/**** ./UPDATE CASE */
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'ct/notc/edit',
			width: 380,
			height: 190,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							cuenta: p.$w.find('[name=cuenta]').data('data')
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						if(data.abrev==''){
							p.$w.find('[name=abrev]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la abreviatura de Tipo!',type: 'error'});
						}
						if(data.cuenta==null){
							p.$w.find('[name=btnCta]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
						}else data.cuenta = ctPcon.dbRel(data.cuenta);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ct/notc/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Tipo actualizado!"});
							ctNotc.init();
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
				p.$w = $('#windowEditTipo');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				$.post('ct/notc/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=abrev]').val(data.abrev);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr)
						.data('data',data.cuenta);
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
			title: 'Seleccionar Tipo de Local',
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
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'ct/notc/lista',
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
				});
			}
		});
	}
};
define(
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return ctNotc;
	}
);
