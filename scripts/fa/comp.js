faComp = {
	tipo: {
		R: 'Recibo de Caja',
		B: 'Boleta de Venta',
		F: 'Factura'
	},
	states: {
		R: {
			descr: "Registrado",
			color: "blue",
			label: '<span class="label label-primary">Registrado</span>'
		},
		X:{
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-danger">Anulado</span>'
		},
		P:{
			descr: "Pendiente",
			color: "black",
			label: '<span class="label label-warning">Pendiente</span>'
		},
		C:{
			descr: "Reemplazado",
			color: "black",
			label: '<span class="label label-info">Reemplazado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'fa',
			action: 'faComp',
			titleBar: {
				title: 'Comprobantes'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Tipo','',{n:'Cliente',f:'cliente.fullname'},'Detalle','Subtotal','IGV','Total',{n:'Registrado',f:'fecreg'}],
					data: 'fa/comp/lista',
					params: {
						organizacion: '51a50edc4d4a13441100000e'
					},
					itemdescr: 'comprobante(s)',
					toolbarHTML: '<button name="btnNew" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nuevo Comprobante</button>&nbsp;'+
						'<button name="btnGen" class="btn btn-info"><i class="fa fa-gears"></i> Generar Recibo de Ingresos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnNew]').click(function(){
							faComp.windowNew();
						}).hide();
						$el.find('[name=btnGen]').click(function(){
							faComp.windowGen();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+faComp.states[data.estado].label+'</td>');
						$row.append('<td>'+faComp.tipo[data.tipo]+'</td>');
						$row.append('<td>'+data.serie+'-'+data.num+'</td>');
						if($.type(data.cliente)==='string'){
							$row.append('<td>'+data.cliente+'</td>');
						}else{
							$row.append('<td>'+mgEnti.formatName(data.cliente)+'</td>');
						}
						if(data.items!=null){
							$row.append('<td>');
							for(var i=0; i<data.items.length; i++){
								if(i!=0)
									$row.find('td:last').append('<br />');
								$row.find('td:last').append(data.items[i].producto.nomb+', <kbd>cant: '+data.items[i].cant+'</kbd>');
							}
						}else{
							$row.append('<td>');
						}
						$row.append('<td>'+ciHelper.formatMon(data.subtotal,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.igv,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecreg)+'</td>');
						$row.data('id',data._id.$id).data('tipo',data.tipo).dblclick(function(){
							var $row = $(this).closest('.item');
							/*K.windowPrint({
								id:'windowPrint',
								title: "Comprobante de Pago",
								url: "fa/comp/print?_id="+$row.data('id')
							});*/
							if($row.data('tipo')=='F'){
								K.windowPrint({
									id:'windowPrint',
									title: "Comprobante de Pago",
									url: "fa/comp/print?_id="+$row.data('id')
								});
							}else{
								window.open("fa/comp/print?_id="+$row.data('id')+'&xls=1');
							}
						}).data('estado',data.estado).contextMenu("conMenInComp", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='X')
									$('#conMenInComp_cam,#conMenInComp_pag',menu).remove();
								return menu;
							},
							bindings: {
								'conMenInComp_imp': function(t) {
									K.tmp.dblclick();
								},
								'conMenInComp_anu': function(t) {
									ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('fa/comp/anular',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											faComp.init();
										});
									},function(){
										$.noop();
									},'Anulaci&oacute;n de Comprobante');
								},
								'conMenInComp_cam': function(t) {
									faComp.windowCambiar({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenInComp_pag': function(t) {
									faComp.windowVoucher({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenInComp_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('fa/comp/eliminar',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Eliminado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											faComp.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Comprobante');
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
		$.extend(p,{
			loadConc: function(){
				var $table,espacio,conceptos,variables,servicio,SERV={},__VALUE__=0,cuotas=0;
				SERV = {
					SALDO: 0,
					FECVEN: 0,
					CM_PREC_PERP: 0,
					CM_PREC_TEMP: 0,
					CM_PREC_VIDA: 0,
					CM_ACCE_PREC: 0,
					CM_TIPO_ESPA: 0
				};
				variables = p.$w.data('vars');
				if(variables==null){
					return K.notification({
						title: 'Servicio no seleccionado',
						text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',
						type: 'error'
					});
				}else{
					for(var i=0,j=variables.length; i<j; i++){
						try{
							if(variables[i].valor=='true') eval('var '+variables[i].cod+' = true;');
							else if(variables[i].valor=='false') eval('var '+variables[i].cod+' = false;');
							else eval('var '+variables[i].cod+' = '+variables[i].valor+';');
						}catch(e){
							console.warn('error en carga de variables');
						}
					}
				}
				p.$w.find('[name=gridCob] tbody').empty();
				$table = p.$w;
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				SERV.FECVEN = 0;
				if($table.find('[name^=fecven]').length>0){
					if($table.find('[name^=fecven]').val()==''){
						$table.find('[name^=fecven]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de vencimiento!',
							type: 'error'
						});
					}
					SERV.FECVEN = ciHelper.date.diffDays(new Date(),$table.find('[name^=fecven]:eq(0)').data("DateTimePicker").date());
				}
				if(SERV.FECVEN<0) SERV.FECVEN = 0;
				p.conceptos = conceptos;
				for(var i=0,j=conceptos.length; i<j; i++){
					var $row = $('<tr class="item" name="'+conceptos[i]._id.$id+'">');
					var monto = eval(conceptos[i].formula);
					eval("var "+conceptos[i].cod+" = "+monto+";");
					$row.append('<td>'+conceptos[i].nomb+'</td>');
					$row.append('<td>');
					$row.append('<td>');
					if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
						var formula = conceptos[i].formula;
						formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
						$row.find('td:eq(1)').html('<input type="number" size="7" name="codform'+conceptos[i].cod+'">');
						$row.find('[name^=codform]').val(0).change(function(){
							var val = parseFloat($(this).val()),
							formula = $(this).data('form'),
							cod = $(this).data('cod'),
							$row = $(this).closest('.item');
							eval("var __VALUE"+cod+"__ = "+val+";");
							var monto = eval(formula);
							$row.find('td:eq(2)').html(ciHelper.formatMon(monto));
							$row.data('monto',monto);
							eval('var '+cod+' = '+monto);
							for(var ii=0,jj=p.conceptos.length; ii<jj; ii++){
								var $row = $table.find('.gridBody .item').eq(ii),
								$cell = $row.find('li').eq(2),
								monto = eval($cell.data('formula'));
								if($cell.data('formula')!=null){
									$cell.html(ciHelper.formatMon(monto));
									$row.data('monto',monto);
								}
							}
							p.calcConc();
						}).data('form',formula).data('cod',conceptos[i].cod);
					}else{
						eval('var '+conceptos[i].cod+' = '+monto+';');
						$row.find('td:eq(2)').data('formula',conceptos[i].formula);
					}
					$row.find('td:eq(2)').html(ciHelper.formatMon(monto));
					$row.data('monto',monto);
					$table.find("[name=gridCob] tbody").append($row);
				}
				p.calcConc();
			},
			calcConc: function(){
				K.clearNoti();
				var $table, servicio, conceptos, total = 0, cuotas=0;
				$table = p.$w;
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					total += parseFloat($table.find('.item').eq(i).data('monto'));
				}
				if(conceptos.length!=p.$w.find('[name=gridCob] tbody .item').length){
					p.$w.find('[name=gridCob] tbody .item:last').remove();
				}
				var $row = $('<tr class="item">');
				$row.append('<td colspan="2" style="text-align:right">Total</td>');
				$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
				$row.data('total',total);
				$table.find("[name=gridCob] tbody").append($row);
				p.$w.find('[name=gridPag] [name=tot]').val('0');
				p.$w.find('[name=gridPag] [name=tot]:first').val(total);
			}
		});
		new K.Panel({
			title: 'Nuevo Cobro',
			contentURL: 'fa/comp/edit_comp',
			store: false,
			buttons: {
				'Guardar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cliente: p.$w.find('[name=mini_enti]').data('data'),
							fec: p.$w.find('[name=fec]').val(),
							tipo: p.$w.find('[name=moneda] option:selected').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							serie: p.$w.find('[name=serie] option:selected').html(),
							num: p.$w.find('[name=num]').val(),
							servicio: p.$w.find('[name=servicio]').data('data'),
							observ: p.$w.find('[name=observ]').val(),
							conceptos: [],
							total: 0
						};
						if(data.cliente==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cliente!',type: 'error'});
						}else{
							data.cliente = mgEnti.dbRel(data.cliente);
						}
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha del comprobante!',type: 'error'});
						}
						if(data.num==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha del comprobante!',type: 'error'});
						}
						if(data.servicio==null){
							p.$w.find('[name=btnServ]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un servicio!',type: 'error'});
						}else{
							data.servicio = {
								_id: data.servicio._id.$id,
								nomb: data.servicio.nomb,
								organizacion: {
					                _id: data.servicio.organizacion._id.$id,
					                nomb: data.servicio.organizacion.nomb
								}
							};
						}
						/*
						 * CONCEPTOS
						 */
						var $table = p.$w.find('[name=gridCob]'),
				        conceptos = p.$w.find('[name^=serv]').data('concs');
				        for(var i=0,j=conceptos.length; i<j; i++){
				            var tmp = {
				            	concepto: {
					                _id: conceptos[i]._id.$id,
					                cod: conceptos[i].cod,
					                nomb: conceptos[i].nomb,
					                formula: conceptos[i].formula
				            	}
				            };
				            if(conceptos[i].clasificador!=null){
				            	tmp.concepto.clasificador = {
					                _id: conceptos[i].clasificador._id.$id,
					                nomb: conceptos[i].clasificador.nomb,
					                cod: conceptos[i].clasificador.cod
				            	};
				            }
				            if(conceptos[i].cuenta!=null){
				            	tmp.concepto.cuenta = {
					                _id: conceptos[i].cuenta._id.$id,
					                descr: conceptos[i].cuenta.descr,
					                cod: conceptos[i].cuenta.cod
				            	};
				            }
				            tmp.monto = parseFloat($table.find('tbody .item').eq(i).data('monto'));
				            tmp.saldo = tmp.monto;
				            data.total += tmp.monto;
				            data.conceptos.push(tmp);
				        }
						var tot = 0;
						data.efectivos = [
							{
						    	moneda: 'S',
						    	monto: parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val())
						    },
						    {
						    	moneda: 'D',
						    	monto: parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())
						    }
						];
						tot += data.efectivos[0].monto + data.efectivos[1].monto;
						for(var i=0,j=p.$w.find('[name=ctban]').length; i<j; i++){
							var tmp = {
								num: p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').val(),
								monto: parseFloat(p.$w.find('[name=ctban]').eq(i).find('[name=tot]').val()),
								moneda: p.$w.find('[name=ctban]').eq(i).data('moneda'),
								cuenta_banco: p.$w.find('[name=ctban]').eq(i).data('data')
							};
							if(tmp.monto>0){
								if(tmp.num==''){
									p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un n&uacute;mero de voucher!',
										type: 'error'
									});
								}
								if(data.vouchers==null) data.vouchers = [];
								data.vouchers.push(tmp);
								tot += (tmp.moneda=='S')?tmp.monto:tmp.monto*p.tasa;
							}
						}
						if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'El total del comprobante no coincide con el total de la forma de pagar!',
								type: 'error'
							});
						}
						data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv)/100)),2);
						data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						/*$.post("fa/comp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Comprobante agregado!"});
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Comprobante de Pago",
								url: "fa/comp/print?_id="+result._id.$id
							});
							faComp.init();
						},'json');*/
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						faComp.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						}
					});
				});
				p.$w.find('[name=btnAct]').hide();
				p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				p.$w.find('[name=btnServ]').click(function(){
					mgServ.windowSelect({callback: function(data){
						p.$w.find('[name=servicio]').html('').removeData('data');
						p.$w.find('[id^=tabsConcPayment] .gridBody').empty();
						$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
							if(concs.serv==null){
								return K.notification({
									title: 'Servicio inv&aacute;lido',
									text: 'El servicio seleccionado no tiene conceptos asociados!',
									type: 'error'
								});
							}
							p.$w.data('vars',concs.vars);
							p.$w.find('[name=servicio]').html(data.nomb).data('data',data).data('concs',concs.serv);
							p.loadConc();
							if(p.$w.find('[name=mini_enti]').data('data')==null){
								p.$w.find('[name=btnSel]').click();
							}
						},'json');
					},bootstrap: true,modulo: 'IN'});
				});
				new K.grid({
					$el: p.$w.find('[name=gridCob]'),
					search: false,
					pagination: false,
					cols: ['Concepto','','Precio'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['Forma de Pago','','Abono'],
					onlyHtml: true
				});
				$.post('fa/comp/get_info_comp',function(data){
					p.talo = data.talo;
					p.igv = parseFloat(data.igv.valor);
					/*TALONARIOS*/
					p.$w.find('[name=tipo]').change(function(){
						p.$w.find('[name=serie]').empty();
						for(var i=0,j=p.talo.length; i<j; i++){
							if(p.talo[i].tipo==p.$w.find('[name=tipo] option:selected').val())
								p.$w.find('[name=serie]').append('<option value="'+p.talo[i].actual+'">'+p.talo[i].serie+'</option>');
						}
						p.$w.find('[name=serie]').change();
					});
					p.$w.find('[name=serie]').change(function(){
						p.$w.find('[name=num]').val(parseFloat($(this).find('option:selected').val())+1);
					});
					p.$w.find('[name=tipo]').change();
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="number" name="tot" size="7"/></td>');
					$row.find('[name=tot]').val(0).change(function(){
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridPag] tbody').append($row);
					/*Efectivo Dolares*/
					var $row = $('<tr class="item" name="mon_dol">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>$<input type="number" name="tot" size="7"/></td>');
					$row.find('[name=tot]').val(0).change(function(){
						$(this).closest('.item').data('total',parseFloat($(this).val())*p.tasa);
					});
					p.$w.find('[name=gridPag] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=data.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban">');
						$row.append('<td>Voucher <input type="text" name="voucher" size="7"/><br /><input type="text" name="fecvou" size="10"/></td>');
						$row.append('<td>'+data.ctban[i].nomb+'</td>');
						$row.append('<td>'+(data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="number" name="tot" size="7"/></td>');
						$row.find('[name=tot]').val(0).change(function(){
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').data('total',parseFloat(tot));
						});
						$row.data('moneda',data.ctban[i].moneda).data('data',{
							_id: data.ctban[i]._id.$id,
							cod: data.ctban[i].cod,
							nomb: data.ctban[i].nomb,
							moneda: data.ctban[i].moneda,
							cod_banco: data.ctban[i].cod_banco
						});
						p.$w.find('[name=gridPag] tbody').append($row);
					}
					p.$w.find('[name=fecvou]').val(ciHelper.date.get.now_ymd())
						.datepicker();
					p.$w.find('[name=btnServ]').click();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowVoucher: function(p){
		new K.Modal({
			id: 'windowVoucher'+p.id,
			title: 'Cambiar Datos de Voucher',
			//contentURL: 'cj/comp/voucher',
			//store: false,
			content: '<div name="gridForm"></div>',
			width: 650,
			height: 450,
			buttons: {
				'Actualizar': {
					icon: 'save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id
						};
						var tmp_total = 0;
						data.efectivos = [
						     {
						    	 moneda: 'S',
						    	 monto: p.$w.find('[name=mon_sol] [name=tot]').val()
						     },
						     {
						    	 moneda: 'D',
						    	 monto: p.$w.find('[name=mon_dol] [name=tot]').val()
						     }
						];
						tmp_total += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
						tmp_total += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val());
						for(var i=0,j=p.$w.find('[name=ctban]').length; i<j; i++){
							var tmp = {
								num: p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').val(),
								monto: parseFloat(p.$w.find('[name=ctban]').eq(i).find('[name=tot]').val()),
								moneda: p.$w.find('[name=ctban]').eq(i).data('moneda'),
								cuenta_banco: p.$w.find('[name=ctban]').eq(i).data('data')
							};
							if(tmp.monto>0){
								if(tmp.num==''){
									p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un n&uacute;mero de voucher!',
										type: 'error'
									});
								}
								if(data.vouchers==null) data.vouchers = [];
								data.vouchers.push(tmp);
								tmp_total += parseFloat(tmp.monto);
							}
						}
						if(tmp_total!=p.total){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'El total del comprobante <b>('+ciHelper.formatMon(p.total)+')</b> no coincide con la forma de pago!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/comp/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: 'El comprobante fue actualizado con &eacute;xito!'
							});
							faComp.init();
						});
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowVoucher'+p.id);
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				K.block({$element: p.$w});
				$.post('cj/comp/get',{id: p.id,forma:true},function(data){
					p.total = parseFloat(data.total);
					p.ctban = data.ctban;
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					}).val(data.efectivos[0].monto);
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Efectivo Dolares*/
					var $row = $('<tr class="item" name="mon_dol">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					}).val(data.efectivos[1].monto);
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban" data-ctban="'+p.ctban[i]._id.$id+'">');
						$row.append('<td>Voucher <input type="text" name="voucher" size="7"/></td>');
						$row.append('<td>'+p.ctban[i].nomb+'</td>');
						$row.append('<td>'+(data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="number" name="tot" size="7"/></td>');
						$row.append('<td>S/.0.00</td>');
						$row.find('[name=tot]').val(0).keyup(function(){
							if($(this).val()=='')
								$(this).val(0);
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon(tot));
							$(this).closest('.item').data('total',parseFloat(tot));
						});
						$row.data('moneda',p.ctban[i].moneda).data('data',{
							_id: p.ctban[i]._id.$id,
							cod: p.ctban[i].cod,
							nomb: p.ctban[i].nomb,
							moneda: p.ctban[i].moneda,
							cod_banco: p.ctban[i].cod_banco
						});
						p.$w.find('[name=gridForm] tbody').append($row);
					}
					if(data.vouchers!=null){
						for(var i=0,j=data.vouchers.length; i<j; i++){
							var voucher = data.vouchers[i];
							for(var i=0,j=p.ctban.length; i<j; i++){
								if(voucher.cuenta_banco._id.$id==p.ctban[i]._id.$id){
									var $row = p.$w.find('[data-ctban='+p.ctban[i]._id.$id+']');
									$row.find('[name=voucher]').val(voucher.num);
									$row.find('[name=tot]').val(voucher.monto);
								}
							}
						}
					}
					K.unblock({$element: p.$w});
				},'json');
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
				if(orga==null){
					p.$w.find('[name=btnOrga]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
				}
				K.block({$element: p.$w});
				$.post('cj/rein/get_rec_fa',{
					tipo_inm: p.$w.find('[name=tipo_inm] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: '51a50b3a4d4a134411000001',//orga._id.$id
					actividad: '51e9958c4d4a13440a00000d',//orga.actividad._id.$id
					componente: '51e99c964d4a13c404000015'//orga.componente._id.$id
				},function(data){
					p.conf = data.conf;
					var tmp_ctas_pat = [];
					p.$w.find('[name=gridComp] tbody').empty();
					p.$w.find('[name=gridAnu] tbody').empty();
					p.$w.find('[name=gridCod] tbody').empty();
					p.$w.find('[name=gridPag] tbody').empty();
					p.$w.find('[name=gridCont] tbody').empty();
					//if(data.comp==null){
					if(data.comp==null && data.ecom==null){
						K.unblock({$element: p.$w});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
					}
					p.$w.find('[name=planilla]').val(data.planilla);
					p.comp = data.comp;
					p.prog = data.prog;
					var cuenta_igv = p.conf.IGV;
					tmp_ctas_pat.push({
						cod: cuenta_igv.cod.substr(0,9),
						cuenta: cuenta_igv,
						total: 0
					});
					var $row = $('<tr class="item">');
					$row.append('<td>'+data.prog.pliego.cod+'</td>');
					$row.append('<td>'+data.prog.programa.cod+'</td>');
					$row.append('<td>'+data.prog.subprograma.cod+'</td>');
					$row.append('<td>'+data.prog.proyecto.cod+'</td>');
					$row.append('<td>'+data.prog.obra.cod+'</td>');
					//$row.append('<td>'+orga.actividad.cod+'</td>');
					//$row.append('<td>'+orga.componente.cod+'</td>');
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
					// Bucle de comprobantes
					var tot_sol = 0,
					tot_dol = 0,
					tot_dol_sol = 0,
					total = 0;
					total_ = 0;
					// LOS COMPROBANTES ELECTRONICOS PERMITEN HASTA EL MOMENTO 3 TIPOS DE PAGOS:
					//	a) Pagos de alquileres mensuales enteros
					//	b) Pago de alquileres mensuales parciales
					//	c) Pago de Actas de conciliacion
					//	d) Pago de Playas
					//
					if(data.ecom!==null) for(var i=0,j=data.ecom.length; i<j; i++){
						var result = data.ecom[i];
						// CUANDO EL COMPROBANTE SE ENCUENTRE ANULADO
						if(result.estado=='X'){
							var $row = $('<tr class="item">');
							$row.append('<td>'+result.tipo+' '+result.serie+' '+result.numero+'</td>');
							if(result.cliente_nomb!=null)
								$row.append('<td>'+result.cliente_nomb+'</td>');
							$row.data('data',{
								_id: result._id.$id,
								tipo: result.tipo,
								serie: result.serie,
								num: result.numero
							});
							p.$w.find('[name=gridAnu] tbody').append($row);
						}else{
							
							for(var k=0,l=result.items.length; k<l; k++){
								var item = result.items[k];
								if(result.items[k].tipo=="farmacia"){
									for(var m=0,n=result.items[k].conceptos.length; m<n; m++){
										console.log(result.items[k].conceptos[m].descr+'-'+result.numero);
										//console.log(result._id.$id+'-'+result.items[k].conceptos[m].cuenta._id.$id);
										var conc = result.items[k].conceptos[m],
										$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+result.items[k].conceptos[m].cuenta._id.$id+']'),
										tot_conc = (result.moneda=='PEN')?parseFloat(result.items[k].conceptos[m].monto):parseFloat(result.items[k].conceptos[m].monto)*parseFloat(result.tipo_cambio);
										
										var tmp_ctas_pat_i = -1;
										for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
											if(tmp_ctas_pat[tmp_i].cod==result.items[k].conceptos[m].cuenta.cod.substr(0,9)){
												tmp_ctas_pat_i = tmp_i;
												tmp_i = tmp_j;
											}
										}
										if(tmp_ctas_pat_i==-1){
											tmp_ctas_pat.push({
												cod: result.items[k].conceptos[m].cuenta.cod.substr(0,9),
												cuenta: result.items[k].conceptos[m].cuenta,
												total: parseFloat(K.round(tot_conc,2))
											});
										}else{
											tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(tot_conc,2));
										}
										if($row.length>0){
											tot_conc_sec = tot_conc + parseFloat($row.data('total'));
											$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
											$row.data('total',tot_conc_sec);
										}else{
											//var $row = $('<tr class="item" name="'+result._id.$id+'-'+result.items[k].conceptos[m].cuenta._id.$id+'">');
											var $row = $('<tr class="item">');
											$row.append('<td>'+result.items[k].conceptos[m].cuenta.cod+' - '+result.items[k].conceptos[m].cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
											$row.append('<td>'+result.items[k].conceptos[m].descr+'</td>');	
											$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
											$row.data('total',tot_conc).data('data',{
												cuenta: {
													_id: result.items[k].conceptos[m].cuenta._id.$id,
													cod: result.items[k].conceptos[m].cuenta.cod,
													descr: result.items[k].conceptos[m].cuenta.descr
												},
												comprobante: {
													_id: result._id.$id,
													tipo: result.tipo,
													serie: result.serie,
													num: result.numero
												},
												concepto: result.items[k].conceptos[m].descr
											});
											p.$w.find('[name=gridComp] tbody').append($row);
										}
									}						
								}
							}
							
							//suma de EFECTIVOS como soles y dolares
							if(result.efectivos!=null){
								if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
								if(parseFloat(result.efectivos[1].monto)!=0){
									tot_dol += parseFloat(result.efectivos[1].monto);
									tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tipo_cambio);
								}
							}
							//suma de VOUCHERS como cuentas de banco y detracciones
							if(result.vouchers!=null){
								for(var k=0,l=result.vouchers.length; k<l; k++){
									var $row = $('<tr class="item vouc">');
									$row.append('<td>'+'Voucher - '+result.vouchers[k].num+'</td>');
									$row.append('<td>'+result.vouchers[k].cuenta_banco.nomb+'</td>');
									$row.append('<td>'+ciHelper.formatMon(result.vouchers[k].monto,result.vouchers[k].moneda)+'</td>');
									$row.append('<td>'+ciHelper.formatMon((result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tipo_cambio))+'</td>');
									$row.append('<td>'+result.cliente_nomb+'</td>');
									$row.data('data',{
										num: result.vouchers[k].num,
										cuenta_banco: {
											_id: result.vouchers[k].cuenta_banco._id.$id,
											nomb: result.vouchers[k].cuenta_banco.nomb,
											cod_banco: result.vouchers[k].cuenta_banco.cod_banco,
											cod: result.vouchers[k].cuenta_banco.cod,
											moneda: result.vouchers[k].cuenta_banco.moneda
										},
										monto: parseFloat(result.vouchers[k].monto),
										cliente: result.cliente_nomb,
										tc: (result.tipo_cambio!=null)?result.tipo_cambio:0
									});
									$row.data('total',parseFloat(result.vouchers[k].monto));
									$row.data('moneda',parseFloat(result.vouchers[k].moneda));
									$row.data('total_sol',(result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tipo_cambio));
									p.$w.find('[name=gridPag] tbody').append($row);
								}
							}
							total_ += parseFloat(K.round(parseFloat(result.total),2));
										
						}
					}
					total = total_;
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
			title: 'Recibo de Ingresos',
			contentURL: 'cj/comp/gen_farmacia',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.$w.find('[name=num]').val(),
							modulo: 'FA',
							planilla: p.$w.find('[name=planilla]').val(),
							iniciales: p.$w.find('[name=iniciales]').val(),
							fec: p.$w.find('[name=fec]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							tipo_inm: p.$w.find('[name=tipo_inm] option:selected').val(),
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
						
						for(var i=0,j=(p.$w.find('[name=gridComp] tbody .item').length-1); i<j; i++){
							var det = p.$w.find('[name=gridComp] tbody .item').eq(i).data('data');
							$.extend(det,{
								monto: parseFloat(K.round(p.$w.find('[name=gridComp] tbody .item').eq(i).data('total'),2))
							});
							if(det.cuenta._id.$id!=null)
								det.cuenta._id = det.cuenta._id.$id;
							data.total += parseFloat(K.round(parseFloat(det.monto),2));
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
									monto: K.round(parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total')),2)
								});
							}
						}
						
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
							faRein.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inComp.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				//p.$w.find('[name=div_ini]').hide();
				p.$w.find('[name=tipo_inm]').change(function(){
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
					cols: ['Pliego','Programa','SubPrograma','Proyecto','Obra','Actividad','Componente','Fuente de Financiamiento'],
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
	['mg/enti','mg/orga','fa/rein','mg/serv','cj/talo'],
	function(mgEnti,mgOrga,faRein,mgServ,cjTalo){
		return faComp;
	}
);