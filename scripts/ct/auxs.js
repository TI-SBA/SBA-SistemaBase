ctAuxs = {
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
			action: 'ctAuxs',
			titleBar: {
				title: 'Auxiliares Standard'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				$.post('mg/prog/all',function(prog){
					var $grid = new K.grid({
						cols: ['','Fecha','Comp. Clase','Comp. Num.','Detalle','Debe','Haber','Saldo'],
						data: 'ct/auxs/lista',
						params: {},
						itemdescr: 'auxiliar(es) standard',
						toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>'
						+'<form class="container-fluid">'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="form-group">'
										+'<label>Programa*</label>'
										+'<select class="form-control" name="programa" required>'
											+'<option value="">Seleccionar</option>'
										+'</select>'
									+'</div>'
									+'<div class="form-group">'
										+'<label>Cuenta*</label>'
										+'<select class="form-control" name="cuenta" style="display:block;width:100%;" required></select>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="form-group">'
										+'<label>Año*</label>'
										+'<select class="form-control" name="ano" required>'
											+'<option value="">Seleccionar</option>'
										+'</select>'
									+'</div>'
									+'<div class="form-group">'
										+'<label>Mes*</label>'
										+'<select class="form-control" name="mes" required>'
											+'<option value="">Seleccionar</option>'
										+'</select>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<div class="form-group">'
										+'<label>Titular</label>'
										+'<div class="input-group">'
											+'<input type="text" name="titular" class="form-control" disabled>'
											+'<span class="input-group-btn">'
												+'<button name="btnSelectEnti" class="btn btn-info">Seleccionar</button>'
												+'<button name="btnCleanEnti" class="btn btn-danger"><i class="fa fa-times"></i></button>'
											+'</span>'
										+'</div>'
									+'</div>'
								+'</div>'
								+'<div class="col-md-6">'
									+'<div class="form-group">'
										+'<label>Inmueble</label>'
										+'<select class="form-control" name="inmueble"></select>'
									+'</div>'
								+'</div>'
							+'</div>'
							+'<div class="row">'
								+'<div class="col-md-6">'
									+'<button name="btnCerrarPeriodo" class="btn btn-info">Cerrar Periodo</button>'
								+'</div>'
							+'</div>'
						+'</form>',
						params: {},
						onContentLoaded: function($el){
							$el.find('[name=btnAgregar]').click(function(){
								var form = ciHelper.validator($el.find('form'),{ onSuccess: function(){
									ctAuxs.windowNew({
										periodo: {
											mes: $el.find('[name=mes] :selected').val(),
											ano: $el.find('[name=ano] :selected').val(),
										},
										cuenta: $el.find('[name=cuenta]').data('data'),
										programa: {
											_id: $el.find('[name=programa] :selected').val(),
											nomb: $el.find('[name=programa] :selected').text()
										}
									});
								}}).submit();
							});
							// Fill years
							for(var i=2015;i<=2020;i++){
								$el.find('[name=ano]').append('<option value="'+i+'">'+i+'</option>');
							}
							// Fill Months
							var $cbo_meses = ciHelper.fillMeses($el.find('[name=mes]'));
							// Fill Programas
							if(prog!=null){
								for(var i in prog){
									$el.find('[name=programa]').append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>')
								}
							}
							$el.find('[name=cuenta]').select2({
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
								var data = $el.find('[name=cuenta]').data('select2').data()[0]
								if(data!=null){
									$el.find('[name=cuenta]').data('data',data.data);
								}
								$el.find('[name=programa]').change();
							});
							$el.find('[name=ano], [name=mes], [name=programa]').change(function(){
								var form = ciHelper.validator($el.find('form'),{ onSuccess: function(){
									var ano = $el.find('[name=ano] :selected').val(); 
									var mes = $el.find('[name=mes] :selected').val();
									var programa = $el.find('[name=programa] :selected').val();
									var cuenta = $el.find('[name=cuenta] :selected').val();
									$grid.reinit({params:{
										ano: ano,
										mes: mes,
										programa: programa,
										cusu: cuenta
									}});
								}}).submit();
							});
							$el.find('[name=btnSelectEnti]').click(function(){
								mgEnti.windowSelect({bootstrap:true, callback:function(data){
									$el.find('[name=titular]').val(mgEnti.formatName(data)).data('data',data);
									$el.find('[name=inmueble]').empty();
									K.block();
									$.post('in/movi/get_by_titu',{_id:data._id.$id},function(titu){
										for(i in titu)
										{
											$el.find('[name=inmueble]').append('<option value="'+i+'">'+titu[i].inmueble.direccion+'</option>');
										}
										K.unblock();
									},'json');
								}});
							});
							$el.find('[name=btnCleanEnti]').click(function(){
								$el.find('[name=titular]').val("").removeData('data');
								$el.find('[name=inmueble]').empty();
							});
							$el.find('[name=btnCerrarPeriodo]').click(function(){
								K.sendingInfo();
								ciHelper.confirm('&#191;Desea <b>Cerrar</b> este periodo para este cuenta&#63;',
								function(){
									K.sendingInfo();
									$.post('ct/auxs/cerrar',{
										mes: $el.find('[name=mes] :selected').val(),
										ano: $el.find('[name=ano] :selected').val(),
										programa : $el.find('[name=programa] :selected').val(),
										cuenta: $el.find('[name=cuenta] :selected').val()
									},function(){
										K.clearNoti();
										K.notification({title: 'Periodo cerrado',text: 'El cierre se realiz&oacute; con &eacute;xito!'});
										$el.find('[name=programa]').change();
									});
								},function(){
									$.noop();
								},'Cierre de periodo');
							});
						},
						onLoading: function(){ K.block(); },
						onComplete: function(){ K.unblock(); },
						search:false,
						load: function(data,$tbody){
							if(data.saldo!=null){
								$('[name=btnCerrarPeriodo]').removeAttr('disabled').data('data',data.saldo);
								if(data.saldo.estado=='C'){
									$('[name=btnAgregar]').attr('disabled','disabled');
								}else{
									$('[name=btnAgregar]').removeAttr('disabled');
								}
							}else{
								$('[name=btnAgregar]').removeAttr('disabled');
								$('[name=btnCerrarPeriodo]').attr('disabled','disabled').removeData('data');
							}
							var saldo = 0;
							var saldo_debe = 0;
							var saldo_haber = 0;
							if(data.saldo!=null){
								var $row = $('<tr class="item">');
								$row.append('<td colspan="5">Saldo Inicial</td>');
								$row.append('<td>'+K.round(data.saldo.debe_inicial,2)+'</td>');
								$row.append('<td>'+K.round(data.saldo.haber_inicial,2)+'</td>');
								saldo=saldo+parseFloat(data.saldo.debe_inicial)-parseFloat(data.saldo.haber_inicial);
								$row.append('<td>'+K.round(saldo,2)+'</td>');
								$tbody.append($row);
							}
							if(data.items!=null){
								for(var i in data.items){
									var $row = $('<tr class="item" />');
									$row.append('<td>');
									$row.append('<td>'+moment(data.items[i].fec.sec,"X").format('DD-MM')+'</td>');
									$row.append('<td>'+data.items[i].clase+'</td>');
									$row.append('<td>'+data.items[i].num+'</td>');
									$row.append('<td>'+data.items[i].detalle+'</td>');
									var debe = '', haber = '';
									if(data.items[i].tipo=='D'){
										debe = data.items[i].monto;
										haber = '';
										saldo_debe+=debe;
										saldo=saldo+debe;
									}else if(data.items[i].tipo=='H'){
										debe = '';
										haber = data.items[i].monto;
										saldo_haber+=haber;
										saldo=saldo-haber;
									}
									$row.append('<td>'+debe+'</td>');
									$row.append('<td>'+haber+'</td>');
									$row.append('<td>'+saldo+'</td>');
									$row.data('id',data.items[i]._id.$id).data('estado',data.items[i].estado).contextMenu("conMenListEli", {
										onShowMenu: function($row, menu) {
											$('#conMenListEli_ver, #conMenListEli_imp',menu).remove();
											if($row.data('estado')=='C') $('#conMenListEli_edi, #conMenListEli_eli',menu).remove();
											return menu;
										},
										bindings: {
											/*'conMenListEd_ver': function(t) {
												ctAuxs.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
											},*/
											'conMenListEli_edi': function(t) {
												ctAuxs.windowNew({id: K.tmp.data('id')});
											},
											'conMenListEli_eli': function(t) {
												ciHelper.confirm('&#191;Desea <b>Eliminar</b> el registro <b>'+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
												function(){
													K.sendingInfo();
													alert("Lo sentimos estamos trabajando en esta funcionalidad.");
													/*$.post('ct/auxs/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
														K.clearNoti();
														K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
														ctAuxs.init();
													});*/
												},function(){
													$.noop();
												},'Eliminaci&oacute;n de un registro auxiliar');
											}
										}
									});
									$tbody.append($row);
								}
							}
							var $row = $('<tr class="item">');
							$row.append('<td colspan="5">Saldo Final</td>');
							$row.append('<td>'+K.round(saldo_debe,2)+'</td>');
							$row.append('<td>'+K.round(saldo_haber,2)+'</td>');
							$row.append('<td>'+K.round(saldo,2)+'</td>');
							$tbody.append($row);
							return $tbody;
						}
					});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNewAuxs',
			title: 'Nuevo Registro',
			contentURL: 'ct/auxs/edit',
			width: 450,
			height: 450,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						if(p.id!=null) data._id = p.id;
						var form = ciHelper.validator(p.$w.find('form'),{onSuccess: function(){
							var data = {
								fec: p.$w.find('[name=fec]').val(),
								clase: p.$w.find('[name=clase]').val(),
								num: p.$w.find('[name=num]').val(),
								tipo: p.$w.find('[name=tipo] :selected').val(),
								monto: p.$w.find('[name=monto]').val(),
								detalle: p.$w.find('[name=detalle]').val(),
								tipo_saldo: 'O1',
								cuenta: ctPcon.dbRel(p.cuenta),
								programa: p.programa,
								periodo: p.periodo
							};
							K.sendingInfo();
							p.$w.find('#div_buttons button').attr('disabled','disabled');
							$.post("ct/auxs/save",data,function(result){
								K.clearNoti();
								K.msg({title: ciHelper.titles.regiGua,text: "Tipo agregado!"});
								ctAuxs.init();
								K.closeWindow(p.$w.attr('id'));
							},'json');
						}}).submit();
						/*console.log(data);
						return false;*/
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
				p.$w = $('#windowNewAuxs');
				p.$w.find('[name=fec]').datepicker({
					format:'yyyy-mm-dd'
				});
				if(p.id!=null)
				{
					K.block();
					$.post('ct/auxs/get',{id:p.id},function(data){
						p.$w.find('[name=fec]').val(moment(data.fec.sec,"X").format('YYYY-MM-DD'));
						p.$w.find('[name=clase]').val(data.clase);
						p.$w.find('[name=num]').val(data.num);
						p.$w.find('[name=tipo]').val(data.tipo);
						p.$w.find('[name=monto]').val(data.monto);
						p.$w.find('[name=detalle]').val(data.detalle);
						p.cuenta = data.saldos.cuenta;
						p.programa = data.saldos.programa._id.$id;
						p.periodo = data.saldos.periodo;
						p.$w.find('[name=cuenta]').html(p.cuenta.cod+' - '+p.cuenta.descr);
						p.$w.find('[name=periodo]').html(p.periodo.mes+'-'+p.periodo.ano);
						p.$w.find('[name=programa]').html(p.programa.nomb);
						K.unblock();
					},'json')
				}else{
					p.$w.find('[name=cuenta]').html(p.cuenta.cod+' - '+p.cuenta.descr);
					p.$w.find('[name=periodo]').html(p.periodo.mes+'-'+p.periodo.ano);
					p.$w.find('[name=programa]').html(p.programa.nomb);	
				}
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
					data: 'ct/auxs/lista',
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
		return ctAuxs;
	}
);