faRein = {
	states: {
		RG: {
			descr: "Registrado",
			color: "#CCC",
			label: '<span class="label label-primary">Registrado</span>'
		},
		RB: {
			descr: "Recibido",
			color: "black",
			label: '<span class="label label-danger">Anulado</span>'
		},
		X: {
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-warning">Pendiente</span>'
		},
		RE: {
			descr: "Registrado Libro Efectivo",
			color: "blue",
			label: '<span class="label label-info">Reemplazado</span>'
		},
		RC: {
			descr: "Registrado Libro Cta Cte",
			color: "black",
			label: '<span class="label label-info">Reemplazado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'fa',
			action: 'faRein',
			titleBar: {
				title: 'Recibo de Ingresos'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Recibo de Ingreso',f:'num'},'Planilla','Total',{n:'Fecha',f:'fec'}],
					data: 'ts/rein/lista',
					params: {
						modulo: 'FA'
					},
					itemdescr: 'recibo(s) de ingresos',
					toolbarHTML: '<button name="btnGen" class="btn btn-primary"><i class="fa fa-plus"></i> Generar Recibo de Ingresos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnGen]').click(function(){
							faRein.windowGen();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+faRein.states[data.estado].label+'</td>');
						$row.append('<td>Recibo de Ingresos N&deg'+data.cod+'</td>');
						$row.append('<td>N&deg'+data.planilla+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						if(ciHelper.date.format.bd_ymd(data.fec)==ciHelper.date.format.bd_ymd(data.fecfin))
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						else
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+' - '+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "in/comp/reci_ing2?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenInRein", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='X'){
									$('#conMenInComp_cam,#conMenInComp_pag',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenInRein_imp': function(t) {
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "in/comp/reci_ing2?_id="+K.tmp.data('id')
									});
								},
								'conMenInRein_pla': function(t) {
									window.open("fa/comp/planilla?_id="+K.tmp.data('id'));
								},
								'conMenInRein_anu': function(t) {
									K.incomplete();
									return 0;
									faRein.windowAnular({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowGen: function(p){
		if(p==null) p = {};
		$.extend(p,{
			fill: function(){
				if(p.$w.find('[name=fec]').val()>p.$w.find('[name=fecfin]').val()){
					p.$w.find('[name=fecfin]').datepicker('setValue',p.$w.find('[name=fec]').val());
				}
				var orga = p.$w.find('[name=orga]').data('data');
				/*if(p.$w.find('[name=fec]').datepicker('getDate')==null){
					p.$w.find('[name=fec]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
				}*/
				if(orga==null){
					p.$w.find('[name=btnOrga]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
				}
				K.block({$element: p.$w});
				$.post('cj/rein/get_rec_fa',{
					serie: 'B002',
					tipo: p.$w.find('[name=tipo] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: '5b3e57523e603798188b4569',
					actividad: '5b8ef2313e6037120b8b4567',
					componente: '5ced5b203e603708148b4568'
				},function(data){
					var tmp_ctas_pat = [];
					p.$w.find('[name=gridComp] tbody').empty();
					p.$w.find('[name=gridAnu] tbody').empty();
					p.$w.find('[name=gridCod] tbody').empty();
					p.$w.find('[name=gridPag] tbody').empty();
					p.$w.find('[name=gridCont] tbody').empty();
					if(data.comp==null){
						K.unblock({$element: p.$w});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
					}
					p.$w.find('[name=planilla]').val(data.planilla);
					p.comp = data.comp;
					p.prog = data.prog;
					var $row = $('<tr class="item">');
					$row.append('<td>8501</td>');
					$row.append('<td>8201</td>');
					$row.append('<td>034</td>');
					$row.append('<td>001</td>');
					$row.append('<td>040</td>');
					$row.append('<td>0123</td>');
					$row.append('<td>30205</td>');
					$row.append('<td>1540</td>');
					$row.append('<td><select name="fuente"></td>');
					for(var k=0,l=p.fuen.length; k<l; k++){
						$row.find('select').append('<option value="'+p.fuen[k]._id.$id+'">'+p.fuen[k].cod+'</option>');
						$row.find('select option:last').data('data',p.fuen[k]);
					}
					p.$w.find('[name=gridCod] tbody').append($row);
					// Efectivo en pagos
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0,'D')+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					var tot_sol = 0,
					tot_dol = 0,
					tot_dol_sol = 0,
					total = 0;
					for(var i=0,j=data.comp.length; i<j; i++){
						var result = data.comp[i];
						if(result.estado=='X'){
							var $row = $('<tr class="item">');
							$row.append('<td>'+result.tipo+' '+result.serie+' '+result.num+'</td>');
							if(result.cliente._id!=null)
								$row.append('<td>'+mgEnti.formatName(result.cliente)+'</td>');
							else
								$row.append('<td>'+result.cliente+'</td>');
							$row.data('data',{
								_id: result._id.$id,
								tipo: result.tipo,
								serie: result.serie,
								num: result.num
							});
							p.$w.find('[name=gridAnu] tbody').append($row);
						}else{
								result.total = parseFloat(result.total);
								for(var ii=0,jj=result.items.length; ii<jj; ii++){
									var $row = $('<tr class="item">'),
									item = result.items[ii].conceptos;
									//console.log(item[jj]);
									/*
									$row.append('<td>'+item.cuenta.cod+' - '+item.cuenta.descr+'</td>');
									$row.append('<td>Recibo '+result.num+'</td>');
									$row.append('<td>'+item.servicio.nomb+'</td>');
									$row.append('<td>'+ciHelper.formatMon(item.costo,result.moneda)+'</td>');
									*/
									$row.data('data',{
										cuenta: {
											_id: item[jj].cuenta._id.$id,
											cod: item[jj].cuenta.cod,
											descr: item[jj].cuenta.descr
										},
										comprobante: {
											_id: result._id.$id,
											tipo: result.tipo,
											serie: result.serie,
											num: result.num
										},
										concepto: item[jj].descr,
									}).data('total',item[jj].monto);
									p.$w.find('[name=gridComp] tbody').append($row);
									tot_sol += parseFloat(item[jj].monto);
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==item[jj].cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: item.cuenta.cod.substr(0,9),
											cuenta: item.cuenta,
											total: parseFloat(item[jj].monto)
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(item[jj].monto);
									}
								}
								total += parseFloat(result.total);
						}
					}
					// GENERACION AUTOMATICA DE CONTABILIDAD PATRIMONIAL
					var tmp_to = 0;
					for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
						var $row = $('<tr class="item">');
						$row.append('<td>'+tmp_ctas_pat[tmp_i].cod+'</td>');
						$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(tmp_ctas_pat[tmp_i].total)+'</td>');
						var tmp_cta_a = tmp_ctas_pat[tmp_i].cuenta;
						tmp_cta_a.tipo = 'D';
						$row.data('data',tmp_cta_a).data('total',tmp_ctas_pat[tmp_i].total).attr('name',tmp_ctas_pat[tmp_i].cuenta._id.$id);
						p.$w.find('[name=gridCont] tbody').append($row);
						tmp_to += parseFloat(tmp_ctas_pat[tmp_i].total);
					}
					tmp_to = parseFloat(K.round(tmp_to,2));
					var $row = $('<tr class="item">');
					$row.append('<td>1101.0101</td>');
					$row.append('<td>'+ciHelper.formatMon(tmp_to)+'</td>');
					$row.append('<td>');
					$row.data('data',{
						_id: '51a6473a4d4a13540a000009',
						cod: '1101.0101',
						descr: 'Caja M/N',
						tipo: 'H'
					}).data('total',tmp_to).attr('name','51a6473a4d4a13540a000009');
					p.$w.find('[name=gridCont] tbody .item:eq(0)').before($row);
					// TOTALES
					var $row = $('<tr class="item result">');
					$row.append('<td>');
					$row.append('<td>');
					$row.append('<td>Parcial</td>');
					$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
					$row.data('total',total);
					p.$w.find('[name=gridComp] tbody').append($row);
					p.$w.find('[name=gridPag] .item').eq(0).data('total',tot_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(0)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(1).data('total',tot_dol).data('total_sol',tot_dol_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_dol,'D'));
					p.$w.find('[name=gridPag] .item').eq(1)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_dol_sol));
					/*p.calcHab();
					p.calcDeb();*/
					K.unblock({$element: p.$w});
				},'json');
			},
			calcDeb: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(9) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(9) [name=monto]').eq(i).val());
				}
				p.$w.find('.payment:eq(9) .result').remove();
				var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(9) .gridBody').append($row.children());
			},
			calcHab: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(11) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(11) .item').eq(i).data('total'));
				}
				p.$w.find('.payment:eq(11) .result').remove();
				var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(11) .gridBody').append($row.children());
			}
		});
		new K.Panel({
			title: 'Recibo de Ingresos',
			contentURL: 'ha/reci/generar',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'AD',
							cod: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							iniciales: p.$w.find('[name=iniciales]').val(),
							fec: p.$w.find('[name=fec]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							observ: p.$w.find('[name=observ]').val(),
							detalle: [],
							glosa: p.$w.find('[name=observ]').val(),
							cont_patrimonial: [],
							total: 0,
							efectivos: [],
							fuente: {
								_id: p.$w.find('[name=fuente] option:selected').data('data')._id.$id,
								cod: p.$w.find('[name=fuente] option:selected').data('data').cod,
								rubro: p.$w.find('[name=fuente] option:selected').data('data').rubro
							}
						},tot_deb=0,tot_hab=0,
						tmp = p.$w.find('[name=orga]').data('data');
						/*if(data.iniciales==''){
							p.$w.find('[name=iniciales]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar unas iniciales!',type: 'error'});
						}*/
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
						}
						if(tmp==null){
							p.$w.find('[name=btnOrga]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
						}
						/*data.organizacion = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							componente: {
								_id: '51e99c964d4a13c404000015',//tmp.componente._id.$id
								cod: tmp.componente.cod,
								nomb: tmp.componente.nomb
							},
							actividad: {
								_id: '51e9958c4d4a13440a00000d',//tmp.actividad._id.$id
								cod: tmp.actividad.cod,
								nomb: tmp.actividad.nomb
							},
							subprograma: {
								_id: p.prog.subprograma._id.$id,
								cod: p.prog.subprograma.cod
							},
							programa: {
								_id: p.prog.programa._id.$id,
								cod: p.prog.programa.cod
							},
							funcion: {
								_id: p.prog.pliego._id.$id,
								cod: p.prog.pliego.cod
							}
						};*/
						for(var i=0,j=(p.$w.find('[name=gridComp] tbody .item').length-1); i<j; i++){
							var det = p.$w.find('[name=gridComp] tbody .item').eq(i).data('data');
							$.extend(det,{
								monto: p.$w.find('[name=gridComp] tbody .item').eq(i).data('total')
							});
							if(det.cuenta._id.$id!=null)
								det.cuenta._id = det.cuenta._id.$id;
							data.total += parseFloat(det.monto);
							data.detalle.push(det);
						}
						if(data.detalle.length==0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay ning&uacute;n comprobante para los filtros seleccionados!',type: 'error'});
						}
						for(var i=0,j=p.$w.find('[name=gridAnu] tbody .item').length; i<j; i++){
							if(data.comprobantes_anulados==null) data.comprobantes_anulados = [];
							data.comprobantes_anulados.push(p.$w.find('[name=gridAnu] tbody .item').eq(i).data('data'));
						}
						tot_deb = 0;
						tot_hab = 0;
						for(var i=0,j=p.$w.find('[name=gridCont] tbody .item').length; i<j; i++){
							var tmp = p.$w.find('[name=gridCont] tbody .item').eq(i).data('data');
							if(tmp!=null){
								if(tmp.tipo=='D')
									tot_deb += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								if(tmp.tipo=='H')
									tot_hab += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								data.cont_patrimonial.push({
									cuenta: {
										_id: tmp._id.$id,
										cod: tmp.cod,
										descr: tmp.descr
									},
									tipo: tmp.tipo,
									moneda: 'S',
									monto: parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'))
								});
							}
						}
						/*if(data.cont_patrimonial.length==0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un registro de contabilidad patrimonial!',type: 'error'});
						}
						if(tot_deb!=tot_hab){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El debe no coincide con el haber!',type: 'error'});
						}*/
						data.efectivos.push({
							moneda: 'S',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(0)').data('total'))
						});
						var tmp = {
							moneda: 'D',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'))
						};
						if(tmp.monto!=0) tmp.tc = parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total_sol'))/parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'));
						data.efectivos.push(tmp);
						for(var i=0,j=p.$w.find('[name=gridPag] tbody .vouc').length; i<j; i++){
							if(data.vouchers==null) data.vouchers = [];
							data.vouchers.push(p.$w.find('[name=gridPag] tbody .vouc').eq(i).data('data'));
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/comp/save_rein',data,function(rein){
							K.clearNoti();
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "in/comp/reci_ing2?_id="+rein._id.$id
							});
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue registrado con &eacute;xito!'});
							hoRein.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						haReci.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				//p.$w.find('[name=div_ini]').hide();
				p.$w.find('[name=tipo]').change(function(){
					p.fill();
				});
				p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd());
				p.$w.find('[name=fecfin]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=fec]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=btnOrga]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
						p.$w.find('[name=fec]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=respo]').html(mgEnti.formatName(K.session.enti));
				new K.grid({
					$el: p.$w.find('[name=gridComp]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Comprobante','Concepto','Importe'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridAnu]'),
					search: false,
					pagination: false,
					cols: ['Comprobante','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCod]'),
					search: false,
					pagination: false,
					cols: ['Debe','Haber','Sector','Pliego','Programa','SubPrograma','Actividad','Componente','Fuente de Financiamiento'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['Pagos','Cuenta Bancaria','Monto','Monto en Soles','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber'],
					onlyHtml: true
				});
				//p.$w.find('[name^=grid] table').append('<tbody>');
				$.post('cj/rein/get_cod',function(data){
					p.cod = data.cod;
					p.fuen = data.fuen;
					p.$w.find('[name=num]').val(data.cod);
					if(K.session.enti.roles!=null){
						if(K.session.enti.roles.trabajador!=null){
							p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb)
								.data('data',K.session.enti.roles.trabajador.programa);
							p.$w.find('[name=btnOrga]').button('option','text',false);
							
							
							//p.$w.find('[name=fec]').change();
							//p.$w.find('[name=fec]').datepicker('changeDate');
							
							//p.$w.find('[name=fec]').datepicker('setValue',ciHelper.dateFormatNowBDNotHour());
							p.fill();
							
							
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','cj/ecom','mg/serv','mg/orga'],
	function(mgEnti,ctPcon,cjEcom,mgServ,mgOrga){
		return faRein;
	}
);