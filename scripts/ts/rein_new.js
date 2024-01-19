tsRein = {
	states: {
		RG: {
			descr: "Registrado",
			color: "#CCC",
			label: '<span class="label label-primary">Registrado</span>'
		},
		RB: {
			descr: "Recibido",
			color: "black",
			label: '<span class="label label-warning">Recibido</span>'
		},
		X: {
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-danger">Anulado</span>'
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
	modul: {
		LM: {
			descr: "LABORATORIO",
			color: "#B8A629",
			label: '<span class="label label-info">Laboratorio</span>'
		},
		AD:{
			descr: "ADICCIONES",
			color: "#AB1FC2",
			label: '<span class="label label-warning">Adicciones</span>'
		},
		MH:{
			descr: "MOISES HERESI",
			color: "#1E4EB8",
			label: '<span class="label label-success">Moises Heresi</span>'
		},
		IN: {
			descr: "INMUEBLES",
			color: "#C25B1C",
			label: '<span class="label label-error">Inmuebles</span>'
		},
		CM:{
			descr: "CEMENTERIO",
			color: "#1BAB46",
			label: '<span class="label label-primary">Cementerio</span>'
		},
	},
	tipo_inm: {
		A: 'Alquileres',
		P: 'Playas'
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsRein',
			titleBar: {
				title: 'Recibo de Ingresos'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Recibo de Ingreso',f:'num'},'Tipo','&Aacute;rea','Total',{n:'Fecha',f:'fec'}],
					data: 'ts/rein/lista',
					params: {short: true},
					itemdescr: 'recibo(s) de ingresos',
					onLoading: function(){ 
						K.block();
					},
					onComplete: function(){ 
						K.unblock();
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+tsRein.modul[data.modulo].label+'</td>');
						//$row.append('<td>'+tsRein.states[data.estado].label+'</td>');
						$row.append('<td>Recibo de Ingresos N&deg'+data.cod+'</td>');
						/*
						$row.append('<td>'+data.organizacion.nomb+'</td>');
						if(data.organizacion._id.$id == '51a50edc4d4a13441100000e' )
							$row.append('<td>'+tsRein.tipo_inm[data.tipo_inm]+'</td>');
						else
							$row.append('<td>--</td>');
						*/
					
						if( data.tipo_inm == null)
							$row.append('<td>--</td>');
						else
							$row.append('<td>'+tsRein.tipo_inm[data.tipo_inm]+'</td>');

						if( data.organizacion == null)
							$row.append('<td>--</td>');
						else
							$row.append('<td>'+data.organizacion.nomb+'</td>');


						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						if(ciHelper.date.format.bd_ymd(data.fec)==ciHelper.date.format.bd_ymd(data.fecfin))
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						else
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+' - '+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							tsRein.windowPrint({data: $(this).closest('.item').data('data')});
						}).data('data',data).contextMenu("conMenTsRein", {
							onShowMenu: function($row, menu) {
								if($row.data('data').estado=='RE'||$row.data('data').estado=='RC'||$row.data('data').estado=='X'){
									$('#conMenTsRein_rec',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenTsRein_ver': function(t) {
									tsRein.windowPrint({data: K.tmp.data('data')});
								},
								'conMenTsRein_rec': function(t) {
									tsRein.windowAprobar({id: K.tmp.data('data')._id.$id});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowPrint: function(p){
		var url = "";
		switch(p.data.organizacion._id.$id){
			case '51a50edc4d4a13441100000e': url = "in/comp/reci_ing?_id="; break;
			case '51a50f0f4d4a13c409000013': url = "cj/repo/reci_ing?_id="; break;
			case 'HO': url = "ho/comp/reci_ing?_id="; break;
			case '51a50e614d4a13c409000012': url = "fa/comp/reci_ing?_id="; break;
			case 'AG': url = "ag/comp/reci_ing?_id="; break;
			default: url = "cj/repo/reci_ing?_id="; break;
		}
		var params = {
			id:'windowcjFactPrint',
			title: "Recibo de Caja",
			url: url+p.data._id.$id
		};
		if(p.buttons!=null){
			params.buttons = {
				'Recepcionado y Agregado a Libro Bancos': {
					icon: 'fa-check',
					type: 'success',
					f: function(){
						K.incomplete();
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow('windowcjFactPrint');
					}
				}
			};
		}
		K.windowPrint(params);
	},
	windowAprobar: function(p){
		$.extend(p,{
			fill: function(){
				$.post('cj/rein/get_rec_ho',{
					modulo: 'MH',
					tipo: p.$w.find('[name=tipo] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: orga._id.$id,
					actividad: orga.actividad._id.$id,
					componente: orga.componente._id.$id
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
					
					
					
					
					
					
					/*data.comp.sort(function(a,b){
						console.log(a);
						//items.conceptos.cuenta.cod
						if (a.cuenta.cod < b.cuenta.cod) //sort string ascending
							return -1;
						if (a.cuenta.cod > b.cuenta.cod)
							return 1;
						return 0;
					});*/
					
					
					
					
					
					
					
					// Bucle de comprobantes
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
			}
		});
		new K.Panel({
			title: 'Registrar Recibo de Ingresos',
			contentURL: 'ts/rein/registrar',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							items:[],
							efectivo:[],
							//vouchers:[]
						};
						if(p.$w.find('[name=gridCont] tbody tr').length>0){
							for(var i=0;i<p.$w.find('[name=gridCont] tbody tr').length;i++){
								var $row = p.$w.find('[name=gridCont] tbody tr').eq(i);
								var _item = {
									descr: $row.find('[name=descr]').val(),
									monto: $row.data('data').monto,
									cuenta: {
										'_id': $row.data('data').cuenta._id.$id,
										'cod': $row.data('data').cuenta.cod,
										'descr': $row.data('data').cuenta.descr
									}
								};
								data.items.push(_item);
							}
						}
						var cban = [],
						//voucher = null;//[];
						voucher = [];
						//console.log(voucher);
						if(p.$w.find('[name=gridPag] tbody tr').length>0){
							for(var i=0;i<p.$w.find('[name=gridPag] tbody tr').length;i++){
								var $row = p.$w.find('[name=gridPag] tbody tr').eq(i);
								if(i<2){
									var _item = {
										monto: $row.data('data').monto,
										voucher: $row.find('[name=voucher]').val(),
										cuenta_banco: {
											'_id': $row.find('[name=cuenta] :selected').data('data')._id.$id,
											'cod_banco': $row.find('[name=cuenta] :selected').data('data').cod_banco,
											'nomb': $row.find('[name=cuenta] :selected').data('data').nomb,
											'cod': $row.find('[name=cuenta] :selected').data('data').cod,
											'moneda': $row.find('[name=cuenta] :selected').data('data').moneda,
										},
										medio: {
											_id: $row.find('[name=medio] :selected').data('data')._id.$id,
											cod: $row.find('[name=medio] :selected').data('data').cod,
											descr: $row.find('[name=medio] :selected').data('data').descr
										} 
									};
									data.efectivo.push(_item);
								}else{
									tmp = $row.data('data'),
									index = $.inArray(tmp.cuenta_banco._id,cban);
									var tmed = $row.find('[name=medio] :selected').data('data'),
									cuenta = $row.data('data').cuenta_banco.cuenta;
									if(cuenta==null){
										return K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'Debe seleccionar una cuenta contable para el voucher!',
											type: 'error'
										});
									}
									//tmp.descr = p.$w.find('[name=section5] [name=descr]').val();
									tmp.medio = {
										_id: tmed._id.$id,
										cod: tmed.cod,
										descr: tmed.descr
									};
									tmp.cuenta = {
										_id: cuenta._id,
										cod: cuenta.cod,
										descr: cuenta.descr
									};
									if(index==-1){
										cban.push(tmp.cuenta_banco._id);
										voucher.push(tmp);
									}else{
										voucher[index].docs.push(tmp.docs[0]);
										//voucher[index].fecs.push(tmp.fecs[0]);
										voucher[index].monto.push(tmp.monto[0]);
									}
									/*var _item = {
										fec: p.$w.find('[name=fec]').val(),
										cuenta_banco: {
											_id:$row.data('data').cuenta_banco._id.$id,
											nomb:$row.data('data').cuenta_banco.nomb,
											cod_banco:$row.data('data').cuenta_banco.cod_banco,
											cod:$row.data('data').cuenta_banco.cod,
										}
									}
									data.vouchers.push(_item);*/
								}
							}
						}
						if(voucher.length!=0) data.vouchers = voucher;
						//return console.log(data);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('ts/rein/aprobar',data,function(rein){
							K.clearNoti();
							/*K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ho/rein/print?_id="+rein._id.$id
							});*/
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue Recibido con exito!'});
							tsRein.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						tsRein.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
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
					cols: ['Comprobante'],
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
					cols: ['Pagos','Cuenta Bancaria','Medio de Pago','Monto','Monto en Soles','Descripción - Libro Bancos'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber','Descripción - Libro Movimientos del Efectivo','Descripción - Libro Movimientos Cta. Cte.'],
					onlyHtml: true
				});
				$.post('ts/rein/get',{_id: p.id,cuentas_banco: true},function(data){
					p.cuentas_banco = data.cuentas_banco;
					p.medios_pago = data.tmed;
					p.$w.find('[name=num]').html(data.cod);
					p.$w.find('[name=iniciales]').html(data.iniciales);
					if(data.tipo!=null||data.tipo_inm!=null){
						if(data.tipo_inm=='A') p.$w.find('[name=tipo]').html('Alquileres');
						else p.$w.find('[name=tipo]').html('Playas');
						if(data.tipo=='H') p.$w.find('[name=tipo]').html('Hospitalizaci&oacute;n');
						else p.$w.find('[name=tipo]').html('Rehabilitaci&oacute;n');
					}else p.$w.find('[name=tipo]').closest('.col-sm-6').hide();
					p.$w.find('[name=fec]').html(ciHelper.date.format.bd_ymd(data.fec));
					p.$w.find('[name=fecfin]').html(ciHelper.date.format.bd_ymd(data.fecfin));
					p.$w.find('[name=orga]').html(data.organizacion.nomb);
					p.$w.find('[name=respo]').html(mgEnti.formatName(data.autor));
					p.$w.find('[name=observ]').html(data.observ);
					if(data.detalle!=null){
						for(var i=0; i<data.detalle.length; i++){
							var item = data.detalle[i],
							$row = $('<tr class="item">');
							$row.append('<td>'+item.cuenta.cod+' - '+item.cuenta.descr+'</td>')
							if(item.comprobante!=null)
								$row.append('<td>'+cjComp.tipo[item.comprobante.tipo]+' '+item.comprobante.num+'</td>');
							else
								$row.append('<td>Recibo Definitivo  '+item.recibo_definitivo.num+'</td>');
							$row.append('<td>'+item.concepto+'</td>');
							$row.append('<td>'+ciHelper.formatMon(item.monto)+'</td>');
							p.$w.find('[name=gridComp] tbody').append($row);
						}
					}
					var $row = $('<tr class="item">');
					$row.append('<td>');
					$row.append('<td>');
					$row.append('<td>Parcial</td>');
					$row.append('<td>'+data.total+'</td>');
					p.$w.find('[name=gridComp] tbody').append($row);
					if(data.comprobantes_anulados){
						for(var i=0; i<data.comprobantes_anulados.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td>'+cjComp.tipo[data.comprobantes_anulados[i].tipo]+' '+data.comprobantes_anulados[i].num+'</td>');
						}
					}
					if(data.cont_patrimonial!=null){
						for(var i=0; i<data.cont_patrimonial.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td>'+data.cont_patrimonial[i].cuenta.cod+'</td>');
							if(data.cont_patrimonial[i].tipo=='D'){
								$row.append('<td>'+ciHelper.formatMon(data.cont_patrimonial[i].monto)+'</td>');
								$row.append('<td>');
								$row.append('<td>');
								$row.append('<td><input type="text" name="descr" class="form-control" /></td>');
							}else{
								$row.append('<td>');
								$row.append('<td>'+ciHelper.formatMon(data.cont_patrimonial[i].monto)+'</td>');
								$row.append('<td><input type="text" name="descr" class="form-control" /></td>');
								$row.append('<td>');
							}
							$row.data('data',data.cont_patrimonial[i]);
							p.$w.find('[name=gridCont] tbody').append($row);
						}
					}
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo Soles<br /><input type="" name="voucher" class="form-control" placeholder="Ingrese el numero del voucher" /></td>');
					$row.append('<td><select name="cuenta" class="form-control"></select></td>');
					for(var i=0; i<p.cuentas_banco.length; i++){
						$row.find('[name=cuenta]').append('<option value="'+p.cuentas_banco[i]._id.$id+'">'+p.cuentas_banco[i].descr+' - '+p.cuentas_banco[i].cod+'</option>');
						$row.find('[name=cuenta]').find('option:last').data('data',p.cuentas_banco[i]);
					}
					$row.append('<td><select name="medio" class="form-control"></select></td>');
					for(var i=0; i<p.medios_pago.length; i++){
						$row.find('[name=medio]').append('<option value="'+p.medios_pago[i]._id.$id+'">'+p.medios_pago[i].cod+'</option>');
						$row.find('[name=medio]').find('option:last').data('data',p.medios_pago[i]);
					}
					$row.append('<td>'+ciHelper.formatMon(data.efectivos[0].monto)+'</td>');
					$row.append('<td>'+ciHelper.formatMon(data.efectivos[0].monto)+'</td>');
					$row.append('<td><input type="text" class="form-control" name="descr"/></td>');
					$row.data('data',data.efectivos[0]);
					p.$w.find('[name=gridPag] tbody').append($row);
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo D&oacute;lares<br /><input type="" name="voucher" class="form-control" placeholder="Ingrese el numero del voucher" /></td>');
					$row.append('<td><select name="cuenta" class="form-control"></select></td>');
					for(var i=0; i<p.cuentas_banco.length; i++){
						$row.find('[name=cuenta]').append('<option value="'+p.cuentas_banco[i]._id.$id+'">'+p.cuentas_banco[i].descr+' - '+p.cuentas_banco[i].cod+'</option>');
						$row.find('[name=cuenta]').find('option:last').data('data',p.cuentas_banco[i]);
					}
					$row.append('<td><select name="medio" class="form-control"></select></td>');
					for(var i=0; i<p.medios_pago.length; i++){
						$row.find('[name=medio]').append('<option value="'+p.medios_pago[i]._id.$id+'">'+p.medios_pago[i].cod+'</option>');
						$row.find('[name=medio]').find('option:last').data('data',p.medios_pago[i]);
					}
					$row.append('<td>'+ciHelper.formatMon(data.efectivos[1].monto,'D')+'</td>');
					$row.append('<td>'+ciHelper.formatMon(data.efectivos[1].monto)+'</td>');
					$row.append('<td><input type="text" class="form-control" name="descr"/></td>');
					$row.data('data',data.efectivos[1]);
					p.$w.find('[name=gridPag] tbody').append($row);
					if(data.vouchers!=null){
						for(var i=0; i<data.vouchers.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td>Voucher '+data.vouchers[i].num+'</td>');
							$row.append('<td>'+data.vouchers[i].cuenta_banco.nomb+'</td>');
							$row.append('<td><select name="medio" class="form-control"></select></td>');
							for(var ii=0; ii<p.medios_pago.length; ii++){
								$row.find('[name=medio]').append('<option value="'+p.medios_pago[ii]._id.$id+'">'+p.medios_pago[ii].cod+'</option>');
								$row.find('[name=medio]').find('option:last').data('data',p.medios_pago[ii]);
							}
							$row.append('<td>'+ciHelper.formatMon(data.vouchers[i].monto)+'</td>');
							$row.append('<td>'+ciHelper.formatMon(data.vouchers[i].monto)+'</td>');
							$row.append('<td><input type="text" class="form-control" name="descr" value="'+mgEnti.formatName(data.vouchers[i].cliente)+'" /></td>');
							var _data = {
								docs: [data.vouchers[i].num],
								cliente: ciHelper.enti.dbRel(data.vouchers[i].cliente),
								monto: [parseFloat(data.vouchers[i].monto)],
								cuenta_banco: {
									_id: data.vouchers[i].cuenta_banco._id.$id,
									cod: data.vouchers[i].cuenta_banco.cod,
									cod_banco: data.vouchers[i].cuenta_banco.cod_banco,
									nomb: data.vouchers[i].cuenta_banco.nomb,
									moneda: data.vouchers[i].cuenta_banco.moneda,
									cuenta: {
										_id: data.vouchers[i].cuenta_banco.cuenta._id.$id,
										cod: data.vouchers[i].cuenta_banco.cuenta.cod,
										descr: data.vouchers[i].cuenta_banco.cuenta.descr,
									}
								}
							};
							$row.data('data', _data);
							p.$w.find('[name=gridPag] tbody').append($row);
						}
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['mg/enti','mg/orga','cj/comp'],
	function(mgEnti,mgOrga,cjComp){
		return tsRein;
	}
);
