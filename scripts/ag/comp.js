agComp = {
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
			mode: 'ag',
			action: 'agComp',
			titleBar: {
				title: 'Comprobantes'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Tipo',{n:'Serie',f:'serie'},{n:'Num',f:'num'},{n:'Cliente',f:'cliente.fullname'},'Detalle','Subtotal','IGV','Total',{n:'Registrado',f:'fecreg'}],
					data: 'ag/comp/lista',
					params: {
						organizacion: '51a50edc4d4a13441100000e'
					},
					itemdescr: 'comprobante(s)',
					//toolbarHTML: '<button name="btnNew" class="btn btn-success"><i class="fa fa-plus"></i> Crear Nuevo Comprobante</button>&nbsp;'+
					toolbarHTML:
						'<button name="btnGen" class="btn btn-info"><i class="fa fa-gears"></i> Generar Recibo de Ingresos</button>',
					onContentLoaded: function($el){
						//Hasta revisar que es lo que hace, mejor se comenta
						//$el.find('[name=btnNew]').click(function(){
						//	agComp.windowNew();
						//});
						$el.find('[name=btnGen]').click(function(){
							agComp.windowGen();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+agComp.states[data.estado].label+'</td>');
						$row.append('<td>'+agComp.tipo[data.tipo]+'</td>');
						$row.append('<td>'+data.serie+'</td>');
						$row.append('<td>'+data.num+'</td>');
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
								url: "ag/comp/print?_id="+$row.data('id')
							});*/
							if($row.data('tipo')=='F'){
								K.windowPrint({
									id:'windowPrint',
									title: "Comprobante de Pago",
									url: "ag/comp/print?_id="+$row.data('id')
								});
							}else{
								window.open("ag/comp/print?_id="+$row.data('id')+'&xls=1');
							}
						}).data('estado',data.estado).contextMenu("conMenAgComp", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='X')
									$('#conMenAgComp_cam,#conMenAgComp_pag',menu).remove();
								else
									$('#conMenAgComp_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenAgComp_imp': function(t) {
									K.tmp.dblclick();
								},
								'conMenAgComp_anu': function(t) {
									ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ag/comp/anular',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											agComp.init();
										});
									},function(){
										$.noop();
									},'Anulaci&oacute;n de Comprobante');
								},
								'conMenAgComp_cam': function(t) {
									agComp.windowCambiar({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenAgComp_pag': function(t) {
									agComp.windowVoucher({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenAgComp_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ag/comp/eliminar',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Eliminado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											agComp.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Comprobante');
								},
								//Editar concepto del comprobante
								'conMenAgComp_edc': function(t) {
									ciHelper.confirm('&#191;Desea <b>Verificar editar el </b> Concepto del comprobante <b>'+K.tmp.find('td:eq(4)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										agComp.windowLgConcEdit({id:K.tmp.data('id')});
									},function(){
										$.noop();
									},'Editar Concepto de Comprobante');
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
			contentURL: 'ag/comp/edit_comp',
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
						$.post("ag/comp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Comprobante agregado!"});
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Comprobante de Pago",
								url: "ag/comp/print?_id="+result._id.$id
							});
							agComp.init();
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						agComp.init();
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
				$.post('ag/comp/get_info_comp',function(data){
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


					if(data.almacenes!=null){
						if(data.almacenes.length>0){
							var $cbo_alma = p.$w.find('[name=almacen]');
							for(var i=0;i<data.almacenes.length;i++){
								$cbo_alma.append('<option value="'+data.almacenes[i]._id.$id+'">'+data.almacenes[i].nomb+'</option>');
							}
						}
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
							agComp.init();
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
				//$.post('cj/comp/get',{id: p.id,forma:true},function(data){
				$.post('ag/comp/get',{id: p.id,forma:true},function(data){
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
	
	
	
	




















	
	
	
	




















	
	
	
	
	
	
	
	
	
	
	/*windowGen: function(p){
		if(p==null) p = {};
		$.extend(p,{
			fill: function(){
				if(p.$w.find('[name=fec]').val()>p.$w.find('[name=fecfin]').val()){
					p.$w.find('[name=fecfin]').datepicker('setValue',p.$w.find('[name=fec]').val());
				}
				var orga = p.$w.find('[name=orga]').data('data');
				//if(p.$w.find('[name=fec]').datepicker('getDate')==null){
				//	p.$w.find('[name=fec]').focus();
				//	return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
				//}
				if(orga==null){
					p.$w.find('[name=btnOrga]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
				}
				K.block({$element: p.$w});
				$.post('cj/rein/get_rec_ag',{
					modulo: 'AG',
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
					var cuenta_igv = {
						_id:{'$id':"51a8f88e4d4a13280700007f"},
						cod:"2101.0105",
						descr:"Impuesto General a las Ventas"
					};
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
					$row.append('<td>'+orga.actividad.cod+'</td>');
					$row.append('<td>'+orga.componente.cod+'</td>');
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
					
					
					
					
					
					
					//data.comp.sort(function(a,b){
					//	console.log(a);
					//	//items.conceptos.cuenta.cod
					//	if (a.cuenta.cod < b.cuenta.cod) //sort string ascending
					//		return -1;
					//	if (a.cuenta.cod > b.cuenta.cod)
					//		return 1;
					//	return 0;
					//});
					
					
					
					
					
					
					
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
							if(result.items){
								for(var k=0,l=result.items.length; k<l; k++){
									var item = result.items[k];
									console.log(item);
									for(var m=0,n=item.conceptos.length; m<n; m++){
										var conc = item.conceptos[m],
										tot_conc = (result.moneda=='S')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tc);
										var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-conc]');
										
										
										
										
										
										
										
										
										var tmp_ctas_pat_i = -1;
										for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
											if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
												tmp_ctas_pat_i = tmp_i;
												tmp_i = tmp_j;
											}
										}
										if(tmp_ctas_pat_i==-1){
											tmp_ctas_pat.push({
												cod: conc.cuenta.cod.substr(0,9),
												cuenta: conc.cuenta,
												total: parseFloat(conc.monto)
											});
										}else{
											tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(conc.monto);
										}
										//tmp_ctas_pat
										
										
										
										
										
										
										
										if($row.length>0){
											tot_conc_sec = tot_conc + parseFloat($row.data('total'));
											$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
											$row.data('total',tot_conc_sec);
										}else{
											var $row = $('<tr class="item" name="'+result._id.$id+'-conc">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.num+'</td>');
											if(item.cuenta_cobrar!=null)
												$row.append('<td>'+item.cuenta_cobrar.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')+'</td>');
											else
												$row.append('<td>Pago de Acta de Conciliaci&oacute;n - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')+'</td>');
											$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
											var tmp_row = {
												cuenta: {
													_id: conc.cuenta._id.$id,
													cod: conc.cuenta.cod,
													descr: conc.cuenta.descr
												},
												comprobante: {
													_id: result._id.$id,
													tipo: result.tipo,
													serie: result.serie,
													num: result.num
												},
												
											};
											if(item.cuenta_cobrar!=null){
												tmp_row.cuenta_cobrar = item.cuenta_cobrar._id.$id;
												tmp_row.concepto = item.cuenta_cobrar.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'');
											}else{
												tmp_row.concepto = 'Pago de Acta de Conciliaci&oacute;n - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')
											}
											$row.data('total',tot_conc).data('data',tmp_row);
											p.$w.find('[name=gridComp] tbody').append($row);
										}
										total += tot_conc;
									}
								}
								if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
								if(parseFloat(result.efectivos[1].monto)!=0){
									tot_dol += parseFloat(result.efectivos[1].monto);
									tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tc);
								}
								if(result.vouchers!=null){
									for(var k=0,l=result.vouchers.length; k<l; k++){
										var $row = $('<tr class="item vouc">');
										$row.append('<td>'+'Voucher - '+result.vouchers[k].num+'</td>');
										$row.append('<td>'+result.vouchers[k].cuenta_banco.nomb+'</td>');
										$row.append('<td>'+ciHelper.formatMon(result.vouchers[k].monto,result.vouchers[k].moneda)+'</td>');
										$row.append('<td>'+ciHelper.formatMon((result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tc))+'</td>');
										$row.append('<td>'+ciHelper.enti.formatName(result.cliente)+'</td>');
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
											cliente: ciHelper.enti.dbRel(result.cliente),
											tc: (result.tc!=null)?result.tc:0
										});
										$row.data('total',parseFloat(result.vouchers[k].monto));
										$row.data('moneda',parseFloat(result.vouchers[k].moneda));
										$row.data('total_sol',(result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tc));
										p.$w.find('[name=gridPag] tbody').append($row);
									}
								}
							}else if(result.conceptos!=null){
								for(var k=0,l=result.conceptos.length; k<l; k++){
									var item = result.conceptos[k];
										var conc = item.concepto,
										$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+']'),
										tot_conc = (result.moneda=='S')?parseFloat(item.monto):parseFloat(item.monto)*parseFloat(result.tc);
										
										
										
										
										
										
										
										var tmp_ctas_pat_i = -1;
										for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
											if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
												tmp_ctas_pat_i = tmp_i;
												tmp_i = tmp_j;
											}
										}
										if(tmp_ctas_pat_i==-1){
											tmp_ctas_pat.push({
												cod: conc.cuenta.cod.substr(0,9),
												cuenta: conc.cuenta,
												total: parseFloat(item.monto)
											});
										}else{
											tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(item.monto);
										}
										//tmp_ctas_pat
										
										
										
										
										
										
										
										if($row.length>0){
											tot_conc_sec = tot_conc + parseFloat($row.data('total'));
											$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
											$row.data('total',tot_conc_sec);
										}else{
											var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+'">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.num+'</td>');
											$row.append('<td>'+result.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')+'</td>');
											$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
											$row.data('total',tot_conc).data('data',{
												cuenta: {
													_id: conc.cuenta._id.$id,
													cod: conc.cuenta.cod,
													descr: conc.cuenta.descr
												},
												comprobante: {
													_id: result._id.$id,
													tipo: result.tipo,
													serie: result.serie,
													num: result.num
												},
												concepto: result.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')
											});
											p.$w.find('[name=gridComp] tbody').append($row);
										}
										total += tot_conc;
								}
								if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
								if(parseFloat(result.efectivos[1].monto)!=0){
									tot_dol += parseFloat(result.efectivos[1].monto);
									tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tc);
								}
								if(result.vouchers!=null){
									for(var k=0,l=result.vouchers.length; k<l; k++){
										var $row = $('<tr class="item vouc">');
										$row.append('<td>'+'Voucher - '+result.vouchers[k].num+'</td>');
										$row.append('<td>'+result.vouchers[k].cuenta_banco.nomb+'</td>');
										$row.append('<td>'+ciHelper.formatMon(result.vouchers[k].monto,result.vouchers[k].moneda)+'</td>');
										$row.append('<td>'+ciHelper.formatMon((result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tc))+'</td>');
										$row.append('<td>'+ciHelper.enti.formatName(result.cliente)+'</td>');
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
											cliente: ciHelper.enti.dbRel(result.cliente),
											tc: (result.tc!=null)?result.tc:0
										});
										$row.data('total',parseFloat(result.vouchers[k].monto));
										$row.data('moneda',parseFloat(result.vouchers[k].moneda));
										$row.data('total_sol',(result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tc));
										p.$w.find('[name=gridPag] tbody').append($row);
									}
								}
							}else{
								console.info(result);
							}
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
					//p.calcHab();
					//p.calcDeb();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
		new K.Panel({
			title: 'Recibo de Ingresos',
			contentURL: 'cj/comp/gen_bootstrap',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'FA',
							cod: p.$w.find('[name=num]').val(),
							planilla: p.$w.find('[name=planilla]').val(),
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
						//if(data.iniciales==''){
						//	p.$w.find('[name=iniciales]').focus();
						//	return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar unas iniciales!',type: 'error'});
						}//
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
						data.organizacion = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							componente: {
								_id: tmp.componente._id.$id,
								cod: tmp.componente.cod,
								nomb: tmp.componente.nomb
							},
							actividad: {
								_id: tmp.actividad._id.$id,
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
						};
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
						//if(data.cont_patrimonial.length==0){
						//	return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un registro de contabilidad patrimonial!',type: 'error'});
						//}
						//if(tot_deb!=tot_hab){
						//	return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El debe no coincide con el haber!',type: 'error'});
						//}
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
								url: "ag/comp/reci_ing?_id="+rein._id.$id
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
						agComp.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=div_ini]').hide();
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
				});
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
							p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.organizacion.nomb)
								.data('data',K.session.enti.roles.trabajador.organizacion);
							p.$w.find('[name=btnOrga]').button('option','text',false);
							
							
							
							
							//p.$w.find('[name=fec]').change();
							//p.$w.find('[name=fec]').datepicker('changeDate');
							
							//p.$w.find('[name=fec]').datepicker('setValue',ciHelper.date.get.now_ymd());
							p.fill();
							
							
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
	*/
	windowGen: function(p){
		K.incomplete();
	},
	windowLgConcEdit: function(p){
		new K.Panel({
			title: 'Editar concepto',
			contentURL: 'ag/comp/logi_conc_edit',
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					
					f: function(){
						K.clearNoti();

						var data = {
							_id: p.id,
							cuenta: '',
							concepto: '',
							valor_unitario: '',
							cant: '',
							monto: '',
							igv: '',
							total: '',
							producto: '',
							item_val: '',
						};

						var cuenta = p.$w.find('[name=cuenta]').data('data');
						if(cuenta==null){
							p.$w.find('[name=cuenta]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable en este concepto!',type: 'error'});
						}else{
							data.cuenta = {
								_id: cuenta._id.$id,
								cod: cuenta.cod,
								descr: cuenta.descr
							};
						}

						data.concepto = p.$w.find('[name=concepto]').html();
						if(data.concepto==''){
							p.$w.find('[name=concepto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un concepto!',type: 'error'});
						}
						data.monto = p.$w.find('[name=monto]').val();
						if(data.monto==''){
							p.$w.find('[name=monto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
						}
						data.igv = p.$w.find('[name=igv]').val();
						if(data.igv==''){
							p.$w.find('[name=igv]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV!',type: 'error'});
						}
						data.total = p.$w.find('[name=total]').val();
						if(data.igv==''){
							p.$w.find('[name=total]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un total!',type: 'error'});
						}
						data.cant = p.$w.find('[name=cant]').val();
						if(data.cant==''){
							p.$w.find('[name=cant]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad!',type: 'error'});
						}
						data.valor_unitario = p.$w.find('[name=valUnit]').val();
						if(data.valor_unitario==''){
							p.$w.find('[name=valUnit]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor unitario!',type: 'error'});
						}
						var producto = p.$w.find('[name=producto]').data('data');
						if(producto==null){
							p.$w.find('[name=producto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar producto en este concepto!',type: 'error'});
						}else{
							data.producto = {
								_id: producto._id.$id,
								cod: producto.cod,
								nomb: producto.nomb
							};
						}

						data.item_val = p.$w.find('[name=nitem]').val();
						if(data.item_val==''){
							p.$w.find('[name=nitem]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontro numero de Item!',type: 'error'});
						}
						data.conc_val = p.$w.find('[name=nconcepto]').val();
						if(data.conc_val==''){
							p.$w.find('[name=nconcepto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontro numero de Concepto!',type: 'error'});
						}

						if(data.total!=p.$w.find('[name=total_original]').val()){
							p.$w.find('[name=nconcepto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El concepto del total no coincide con el original!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('ag/comp/save_item',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El producto fue actualizado con &eacute;xito!'});
							agComp.init();
							
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						agComp.init();
					}
				}
			},
			onContentLoaded: function(){
				//p.$w = $('#windowLgConcEdit'+p.id);
				p.$w = $('#mainPanel');

				p.$w.find('[name=btnCuenta]').click(function(){
					ctPcon.windowSelect({bootstrap: true,callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				});

				p.$w.find('[name=btnProducto]').click(function(){
					lgProd.windowSelectProducto({
						modulo: p.$w.find('[name=modulo]').val(),
						almacen: p.$w.find('[name=almacen]').val(),
						callback: function(data){
							if(p.$w.find('[name='+data._id.$id+']').length==0){
								p.$w.find('[name=producto]').html(data.nomb).data('data',data).data('precio_venta',data.precio_venta);
								p.$w.find('[name=descr]').text(data.nomb);
								p.$w.find('[name=valUnit]').val(K.roundValorUnitario(data.precio_venta/1.18,10));
								p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
								p.$w.find('[name=monto]').val(K.round(K.roundValorUnitario(data.precio_venta/1.18,10)*p.$w.find('[name=cant]').val(),2));
								p.$w.find('[name=igv]').val(K.round(data.precio_venta*p.$w.find('[name=cant]').val()-p.$w.find('[name=monto]').val(),2));
								p.$w.find('[name=total]').val(K.round(parseFloat(p.$w.find('[name=monto]').val())+parseFloat(p.$w.find('[name=igv]').val()),2));
							}
						}
					});
				});

				p.$w.find('[name=cant]').keyup(function(){
					p.$w.find('[name=monto]').val(K.round(p.$w.find('[name=valUnit]').val()*p.$w.find('[name=cant]').val(),2));
					p.$w.find('[name=igv]').val(K.round(p.$w.find('[name=valUnit]').val()*p.$w.find('[name=cant]').val()*0.18,2));
					p.$w.find('[name=total]').val(K.round(parseFloat(p.$w.find('[name=monto]').val())+parseFloat(p.$w.find('[name=igv]').val()),2));
				});

				p.$w.find('[name=monto]').keyup(function(){
					p.$w.find('[name=valUnit]').val(K.roundValorUnitario(p.$w.find('[name=monto]').val()/p.$w.find('[name=cant]').val(),10));
					p.$w.find('[name=igv]').val(K.round(p.$w.find('[name=valUnit]').val()*p.$w.find('[name=cant]').val()*0.18,2));
					p.$w.find('[name=total]').val(K.round(parseFloat(p.$w.find('[name=monto]').val())+parseFloat(p.$w.find('[name=igv]').val()),2));
				});

				p.$w.find('[name=total]').keyup(function(){
					p.$w.find('[name=monto]').val(K.round(p.$w.find('[name=total]').val()/1.18,2));
					p.$w.find('[name=valUnit]').val(K.roundValorUnitario((p.$w.find('[name=total]').val()/1.18)/p.$w.find('[name=cant]').val(),10));
					p.$w.find('[name=igv]').val(K.round(p.$w.find('[name=valUnit]').val()*p.$w.find('[name=cant]').val()*0.18,2));
				});

				var almacenes = [];
				for (var i = K.session.almacenes.length - 1; i >= 0; i--) {
					almacenes.push(K.session.almacenes[i]);
				};
				var $cbo = p.$w.find('[name=almacen]');
				for(var i=0; i<almacenes.length; i++){
					$cbo.append('<option value="'+almacenes[i]._id.$id+'">'+almacenes[i].nomb+'</option>');
				}
					
				p.$w.find('[name=monto]').numeric();
				p.$w.find('[name=cant]').numeric();
				p.$w.find('[name=valUnit]').numeric();
				new K.grid({
					$el: p.$w.find('[name=gridList]'),
					search: false,
					data: 'ag/comp/get_items_conceptos',
					pagination: false,
					cols: ['','Nº Item','Nº Concepto','Cuenta','Concepto','Monto'],
					onlyHtml:true,
					toolbarHTML: '<h3>Lista de Conceptos (Internos del Sistema) </h3>',
					onLoading: function(){ K.block(); },
					onComplete: function(){
					},
				});
				
				/**
				*	GRILLA DE CONCEPTOS
				*/

				K.block();
				$.post('ag/comp/get_items_conceptos','_id='+p.id,function(data){
					p.conc=data;
					for (var i=0;i<p.conc.length;i++) {
						var $row = $('<tr class="item" />');
						$row.append('<td>--</td>');
						$row.append('<td>'+p.conc[i].raiz.val_item+'</td>');
						$row.append('<td>'+p.conc[i].raiz.val_conc+'</td>');
						$row.append('<td>'+p.conc[i].cuenta.cod+'</td>');
						$row.append('<td>'+p.conc[i].concepto+'</td>');
						$row.append('<td>'+p.conc[i].monto+'</td>');
						p.$w.find('[name=gridList] tbody').append($row);
					}
					K.unblock();
				},'json');

				new K.grid({
					$el: p.$w.find('[name=gridItemList]'),
					search: false,
					data: 'ag/comp/get_items',
					pagination: false,
					cols: ['','Nº Item','Producto','Cantidad','Monto',],
					onlyHtml:true,
					toolbarHTML: '<h3>Lista de Items Internos del sistema</h3>',
					onLoading: function(){ K.block(); },
					onComplete: function(){
					},
				});

				/**
				*	GRILLA DE ITEMS
				*/

				$.post('ag/comp/get_items','_id='+p.id,function(data){
					p.items=data['items'];
					p.$w.find('[name=modulo]').val(data['modulo']);
					p.$w.find('[name=almacen]').val(data['almacen']);
					for (var i=0;i<p.items.length;i++) {
						var $row = $('<tr class="item" />');
						$row.append('<td><button class="btn btn-info"><i class="fa fa-edit"></i></button></td>');
						$row.append('<td>'+p.items[i].raiz.val_item+'</td>');
						$row.append('<td>'+p.items[i].producto.nomb+'</td>');
						$row.append('<td>'+p.items[i].cant+'</td>');
						$row.append('<td>'+p.items[i].monto+'</td>');
						$row.data('data',p.items[i]).data('i', i);
						$row.find('button').eq(0).data('i',i).click(function(e){
							e.preventDefault();
							p.index = $(this).data('i');
							var $row = $(this).closest('.item');
							K.msg({text: 'Se editara el Item '+$row.find('td:eq(2)').html()});
							var data = $row.data('data');
							p.$w.find('[name=cuenta]').html(data.conceptos[0].cuenta.cod).data('data',data.conceptos[0].cuenta);
							p.$w.find('[name=concepto]').html(data.conceptos[0].concepto);
							p.$w.find('[name=producto]').html(data.producto.nomb).data('data',data.producto);
							p.$w.find('[name=cant]').val(data.cant);
							p.$w.find('[name=valUnit]').val(K.roundValorUnitario((data.conceptos[0].monto+data.conceptos[1].monto)/1.18,10));
							p.$w.find('[name=monto]').val(K.round(parseFloat(data.conceptos[0].monto)*parseFloat(data.cant),2));
							p.$w.find('[name=monto_original]').val(K.round(parseFloat(data.conceptos[0].monto)*parseFloat(data.cant),2));
							p.$w.find('[name=igv]').val(data.conceptos[1].monto*data.cant);
							p.$w.find('[name=igv_original]').val(data.conceptos[1].monto*data.cant);
							p.$w.find('[name=total_original]').val(K.round(data.monto*data.cant),2);
							p.$w.find('[name=total]').val(K.round(data.monto*data.cant),2);
							p.$w.find('[name=nitem]').val(data.raiz.val_item);
						});
						p.$w.find('[name=gridItemList] tbody').append($row);
					}
				},'json');
			}
		});
	},
};
define(
	['mg/enti','mg/orga','fa/rein','mg/serv','cj/talo'],
	function(mgEnti,mgOrga,faRein,mgServ,cjTalo){
		return agComp;
	}
);