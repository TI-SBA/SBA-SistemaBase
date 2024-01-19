ctVeos = {
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
			_id: item._id.$id
		};
	},
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctVeos',
			titleBar: {
				title: 'Orden(es) de servicio'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Orden',{n:'Proveedor',f:'proveedor'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/orse/lista_cont',
					params: {},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgOrse.states[data.estado_cont].label+'</td>');
						$row.append('<td>'+'Orden de Servicio N&deg;'+data.cod +'</td>');
						$row.append('<td>'+mgEnti.formatName(data.proveedor)+'</td>');
						//$row.append('<td>'+data.almacen.nomb+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'+mgEnti.formatName(data.autor)+'</td>');
						$row.data('id',data._id.$id).data('estado',data.estado).data('data', data).contextMenu("conMenCtVeoc", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('data').estado_cont=='A') $('#conMenCtVeoc_ing, #conMenCtVeoc_apr',menu).remove();
								//else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenCtVeoc_ver': function(t) {
									//ctVeos.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
									lgOrse.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										ctVeos.init(); 
									}});
								},
								'conMenCtVeoc_ing': function(t) {
									ctVeos.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenCtVeoc_apr': function(t) {
									ciHelper.confirm('&#191;Desea <b>Aprobar y Crear</b> auxiliares <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/orse/apro_cont',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.msg({title: 'Auxiliares creados',text: 'Los auxiliares fueron creados con &eacute;xito!'});
											ctVeos.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Ingresar información contable',
			contentURL: 'lg/orse/edit_veos',
			width: 750,
			height: 500,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {};
						data._id = p.id;
						data.auxs = new Array;
						if(p.$w.find('[name=gridAuxi] tbody tr').length){
							for(i=0;i<p.$w.find('[name=gridAuxi] tbody tr').length;i++){
								var $row = p.$w.find('[name=gridAuxi] tbody tr').eq(i);
								var item = {
									monto:$row.find('[name=monto]').val(),
									tipo:$row.find('[name=tipo] :selected').val(),
									saldo:'O1',
									programa: {
										_id: $row.find('[name=programa] :selected').val(),
										nomb: $row.find('[name=programa] :selected').text(),
									}
								};
								if($row.find('[name=cuenta]').data('data')!=null){
									item.cuenta = {
										_id:$row.find('[name=cuenta]').data('data')._id.$id,
										cod:$row.find('[name=cuenta]').data('data').cod,
										descr:$row.find('[name=cuenta]').data('data').descr,
										tipo:$row.find('[name=cuenta]').data('data').tipo
									};
								}else{
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Es necesario seleccionar una cuenta contable!',type: 'error'});
								}
								if(item.monto==""){
									K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
									return $row.find('[name=monto]').focus();
								}
								data.auxs.push(item);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/orse/save_cont',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Tipo de nota fue actualizado con &eacute;xito!'});
							ctVeos.init();
						});
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
				p.$w.find('[name=mini_enti] [name=btnSel],[name=mini_enti] [name=btnAct]').remove();
				new K.grid({
					$el: p.$w.find('[name=gridProd]'),
					search: false,
					pagination: false,
					cols: ['','Clasificador','Producto','Unidad','Distribuci&oacute;n','SubTotal',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
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
					toolbarHTML: '',
					onContentLoaded: function($el){
						var $row = $('<tr class="item">');
						$row.append('<td colspan="2">');
						$row.append('<td>Total</td>');
						$row.append('<td><span name="total"></span></td>');
						p.$w.find('[name=gridPres] tfoot').append($row);
					}
				});

				$.post('mg/prog/all',function(prog){
					new K.grid({
						$el: p.$w.find('[name=gridAuxi]'),
						search: false,
						pagination: false,
						cols: [{descr:'Programa', width:200},{descr:'Cuenta', width:200},{descr:'Tipo', width:100},{descr:'Monto', width:120},''],
						onlyHtml: true,
						toolbarHTML: '<button class="btn btn-success" name="btnAgregarFila">Agregar Fila</button>',
						onContentLoaded: function($el){
							$el.find('[name=btnAgregarFila]').click(function(){
								var $row = $('<tr class="item">');
								$row.append('<td><select name="programa" class="form-control" style="width:100%;"></select></td>');
								$row.append('<td><select name="cuenta" class="form-control" style="width:100%;"></select></td>');
								$row.append('<td><select name="tipo" class="form-control" style="width:100%;"><option value="D">Debe</option><option value="H">Haber</option></select></td>');
								$row.append('<td><input type="text" name="monto" class="form-control" style="width:100%;"></td>');
								$row.append('<td><button class="btn btn-danger" name="btnDeleteRow"><i class="fa fa-trash"></i></button></td>');
								if(prog!=null){
									for(var i in prog){
										$row.find('[name=programa]').append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>')
									}
								}
								$row.find('[name=programa]').select2();
								$row.find('[name=tipo]').select2();
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
										_$row.find('[name=cuenta]').data('data',data.data).html(data.data.descr);
									}
								});
								$row.find('[name=btnDeleteRow]').click(function(){
									$(this).closest('tr').remove();
								});
								p.$w.find('[name=gridAuxi] tbody').append($row);
							});

						}
					});
				},'json');
				$.post('mg/prog/all',function(prog){
					$.post('lg/orse/get',{_id: p.id},function(data){
						p.$w.find('[name=ref]').html(data.ref);
						p.$w.find('[name=fuente]').html(data.fuente.cod+' '+data.fuente.rubro);
						p.$w.find('[name=observ]').html(data.observ);
						p.$w.find('[name=feceje]').html(ciHelper.date.format.bd_ymd(data.feceje));
						p.$w.find('[name=lugar]').html(data.lugar);
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.proveedor);
						for(var i=0; i<data.servicios.length; i++){
							var data_prod = data.servicios[i].servicio,
							$row = $('<tr class="item" name="'+data_prod._id.$id+'">');
							$row.append('<td>'+(p.$w.find('[name=gridProd] tbody .item').length+1)+'</td>');
							$row.append('<td><kbd>'+data_prod.clasif.cod+'</kbd><br />'+data_prod.clasif.nomb+'</td>');
							$row.append('<td><kbd>'+data_prod.cod+'</kbd><br />'+data_prod.nomb+'</td>');
							$row.append('<td>'+data_prod.unidad.nomb+'</td>');
							$row.append('<td><span name="asignar"></span></td>');
							$row.append('<td><span name="subtotal"></span></td>');
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
						//p.calcTot();
						//p.calcOrga();
						//K.unblock();
						if(data.revisiones!=null){
							for(var i=0; i<data.revisiones.length; i++){
								if(data.revisiones[i].estado_doc==null) data.revisiones[i].estado_doc = 'P';
								$item = $('<div class="timeline-item">'+
									'<div class="row">'+
										'<div class="col-xs-5 date">'+
											'<i class="fa"></i>'+ciHelper.date.format.bd_ymdhi(data.revisiones[i].fec)+
										'</div>'+
										'<div class="col-xs-7 content no-top-border">'+
											'<p class="m-b-xs"><strong>'+data.revisiones[i].trabajador.cargo.organizacion.nomb+'</strong></p>'+
											'<p>'+mgEnti.formatName(data.revisiones[i].trabajador)+'</p>'+
											'<p>Estado del Documento: '+lgOrse.states[data.revisiones[i].estado_doc].label+'</p>'+
										'</div>'+
									'</div>'+
								'</div>');
								if(data.revisiones[i].observ!=null)
									$item.find('.content').append('<p class="text-warning">'+data.revisiones[i].observ+'</p>');
								if(data.revisiones[i].estado=='A'){
									$item.find('.date').append('<br />'+lgOrse.statesRev[data.revisiones[i].estado].label);
									$item.find('.fa').addClass('fa-check');
								}else{
									$item.find('.date').append('<br />'+lgOrse.statesRev[data.revisiones[i].estado].label);
									$item.find('.fa').addClass('fa-close');
								}
								p.$w.find('.inspinia-timeline').append($item);
							}
						}
						if(data.auxs){
							for(i=0;i<data.auxs.length;i++){
								var $row = $('<tr class="item">');
								$row.append('<td><select name="programa" class="form-control" style="width:100%;"></select></td>');
								$row.append('<td><select name="cuenta" class="form-control" style="width:100%;"></select></td>');
								$row.append('<td><select name="tipo" class="form-control" style="width:100%;"><option value="D">Debe</option><option value="H">Haber</option></select></td>');
								$row.append('<td><input type="text" name="monto" class="form-control" style="width:100%;"></td>');
								$row.append('<td><button class="btn btn-danger" name="btnDeleteRow"><i class="fa fa-trash"></i></button></td>');

								if(prog!=null){
									for(var j in prog){
										$row.find('[name=programa]').append('<option value="'+prog[j]._id.$id+'">'+prog[j].nomb+'</option>')
									}
								}
								$row.find('[name=monto]').val(K.round(data.auxs[i].monto,2));
								$row.find('[name=programa]').val(data.auxs[i].programa._id.$id).select2();
								$row.find('[name=tipo]').val(data.auxs[i].tipo).select2();
								$row.find('[name=cuenta]').select2({
									placeholder:{
										id: data.auxs[i].cuenta._id.$id,
										text: data.auxs[i].cuenta.descr,
										data: data.auxs[i].cuenta
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
									var data = $(this).data('select2').data()[0];
									if(data!=null){
										_$row.find('[name=cuenta]').data('data',data.data).html(data.data.descr);
									}
								});
								$row.find('[name=cuenta]').data('data', data.auxs[i].cuenta)
								$row.find('[name=btnDeleteRow]').click(function(){
									$(this).closest('tr').remove();
								});
								p.$w.find('[name=gridAuxi] tbody').append($row);
							}
						}
						K.unblock();
					},'json');
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','lg/orse'],
	function(mgEnti,ctPcon, lgOrse){
		return ctVeos;
	}
);