ctNota = {
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
			action: 'ctNota',
			titleBar: {
				title: 'Notas de Contabilidad'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Numero','Tipo','Periodo','Debe','Haber','Registrado'],
					data: 'ct/nota/lista',
					params: {},
					itemdescr: 'nota(s) de Contabilidad',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ctNota.windowNew({goBack: function(){
								cmHope.init();
							}});
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.numero+'</td>');
						$row.append('<td>'+data.tipo+'</td>');
						$row.append('<td>'+data.periodo.mes+'-'+data.periodo.ano+'</td>');
						$row.append('<td>'+K.round(data.total_debe,2)+'</td>');
						$row.append('<td>'+K.round(data.total_haber,2)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ctNota.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									ctNota.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									ctNota.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ct/nota/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ctNota.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ct/nota/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ctNota.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
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
		if(p.goBack!=null) K.history.push({f: p.goBack});
		new K.Panel({ 
			title: 'Nueva Nota de Contabilidad',
			contentURL: 'ct/nota/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							num: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=tipo] :selected').val(),
							periodo: {
								mes: p.$w.find('[name=mes] :selected').val(),
								ano: p.$w.find('[nane=ano] :selected').val()
							},
							cuentas: []
						};
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un numero!',type: 'error'});
						}
						if(p.$w.find('[name=grid_detalle] tbody tr').length>0){
							for(var i=0;i<p.$w.find('[name=grid_detalle] tbody tr').length;i++){
								var $row = p.$w.find('[name=grid_detalle] tbody tr').eq(i);
								var _data = $row.find('[name=cuenta]').data('select2').data()[0].data;
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
									monto:monto
								};
								data.cuentas.push(_cuenta);
							}
						}
						console.log(data);
						return false;
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ct/nota/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "El plan contable fue agregado0 con &eacute;xito!"});
							ctNota.init();
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
				new K.grid({
					$el: p.$w.find('[name=grid_detalle]'),
					search: false,
					pagination: false,
					cols: [{width:'150',descr:'Codigo'},{width:'250',descr:'Descripci&oacute;n'},{width:'100',descr:'Debe'},{width:'100',descr:'Debe'},''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar nueva Fila</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item" />');
							$row.append('<td><select style="width:100%;" class="form-control" name="cuenta"></select></td>')
							$row.append('<td><span name="item_cuenta_name"></span></td>');
							$row.append('<td><input type="text" name="item_debe" value="0.00" class="form-control"></td>');
							$row.append('<td><input type="text" name="item_haber" value="0.00" class="form-control"></td>');
							$row.append('<td><button name="btnDeleteRow" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>')
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
									_$row.find('[name=item_cuenta_name]').html(data.data.descr);
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
					$row.append('<td><input type="text" name="observ_body_1" class="form-control"></td>');
					$row.append('<td><input type="text" name="observ_body_2" class="form-control"></td>');
					$row.append('<td><input type="text" name="observ_body_3" class="form-control"></td>');
					$row.append('<td><input type="text" name="observ_body_4" class="form-control"></td>');
					$row.append('<td><input type="text" name="observ_body_5" class="form-control"></td>');
					$row.append('<td><input type="text" name="observ_body_6" class="form-control"></td>');
					$row.append('<td><button class="btn btn-danger" name="btnObservDeleteRow"><i class="fa fa-trash"></i></button></td>');
					$row.find('[name=btnObservDeleteRow]').click(function(){
						$(this).closest('tr').remove();
					});
					p.$w.find('[name=grid_observ] tbody').append($row);
				});
				$.post('ct/tnot/all',function(data){
					var $cbo = p.$w.find('[name=tipo]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							$cbo.append('<option sigla="'+data[i].sigla+'" value="'+data[i]._id.$id+'" >'+data[i].nomb+'</option>');
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear tipos de notas!',type: 'error'});
					}
					p.$w.find('[name=periodo]').change(function(){
						p.$w.find('[name=tipo]').change();
					});
					$cbo.change(function(){
						$.post('ct/notc/get_num',{
							periodo: p.$w.find('[name=ano] :selected').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val()
						},function(data){
							p.$w.find('[name=num]').val(data.num);
							K.unblock();
						},'json');
					}).change();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'ct/nota/edit',
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
						$.post("ct/nota/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Tipo actualizado!"});
							ctNota.init();
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
				$.post('ct/nota/get',{_id: p.id},function(data){
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
					data: 'ct/nota/lista',
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
		return ctNota;
	}
);