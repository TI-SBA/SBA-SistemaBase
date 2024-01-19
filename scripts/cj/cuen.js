/*******************************************************************************
cuentas por cobrar */
cjCuen = {
	states: {
		P: {
			descr: "Pendiente",
			color: "orange"
		},
		C: {
			descr: "Cancelada",
			color: "blue"
		},
		V: {
			descr: "Vencida",
			color: "#CC0000"
		},
		X: {
			descr: "Anulada",
			color: "#000000"
		},
		I: {
			descr: "Letra Protestada",
			color: "#E17009"
		}
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsCuen'+p.id,
			title: 'Cuenta por Pagar: '+p.nomb,
			contentURL: 'cj/cuen/details',
			store: false,
			icon: 'ui-icon-note',
			width: 540,
			height: 410,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsCuen'+p.id);
				K.block({$element: p.$w});
				$.post('cj/cuen/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').html(ciHelper.enti.formatName(data.cliente));
					p.$w.find('[name=dni]').html(data.cliente.docident[0].num);
					if(data.cliente.domicilios!=null)
						p.$w.find('[name=direc]').html(data.cliente.domicilios[0].direccion);
					p.$w.find('[name=serv]').html(data.servicio.nomb);
					p.$w.find('[name=fecven]').html(ciHelper.dateFormatOnlyDay(data.fecven));
					for(var i=0,j=data.conceptos.length; i<j; i++){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.conceptos[i].concepto.nomb);
						$row.find('li:eq(1)').html(ciHelper.formatMon(data.conceptos[i].monto));
						p.$w.find('.gridBody').append($row.children());
					}
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html('Total')
						.addClass('ui-button ui-widget ui-state-default');
					$row.find('li:eq(1)').html(ciHelper.formatMon(data.total));
					$row.wrapInner('<a class="item" />');
					p.$w.find(".gridBody").append( $row.children() );
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		$.extend(p,{
			cbCli: function(data){
				p.$w.find('[name=nomb]').html(data.nomb).data('data',data);
				if(data.tipo_enti=='E')
					p.$w.find('[name=apel]').closest('tr').hide();
				else{
					p.$w.find('[name=apel]').closest('tr').show();
					p.$w.find('[name=apel]').html(data.appat+' '+data.apmat);
				}
				p.$w.find('[name=dni]').html(data.docident[0].num);
				if(data.domicilios!=null) p.$w.find('[name=direc]').html(data.domicilios[0].direccion);
				else p.$w.find('[name=direc]').html('--');
			},
			loadConc: function(){
				var $table,espacio,conceptos,variables,servicio,SERV={},__VALUE__=0,cuotas=0;
				SERV = {
					FECVEN: 0,
					CM_PREC_PERP: 0,
					CM_PREC_TEMP: 0,
					CM_PREC_VIDA: 0,
					CM_ACCE_PREC: 0,
					CM_TIPO_ESPA: 0
				};
				if(p.$w.find('[name=fecven]').val()==''){
					p.$w.find('[name=fecven]').focus();
					return K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe seleccionar una fecha de vencimiento!',
						type: 'error'
					});
				}
				SERV.FECVEN = ciHelper.date.diffDays(new Date(),p.$w.find('[name=fecven]').datepicker('getDate'));
				if(SERV.FECVEN<0) SERV.FECVEN = 0;
				if(p.getEspa!=null){
					espacio = p.getEspa();
					if(espacio!=null){
						if(espacio.precio_perp!=null) SERV.CM_PREC_PERP = espacio.precio_perp;
						if(espacio.precio_temp!=null) SERV.CM_PREC_TEMP = espacio.precio_temp;
						if(espacio.precio_vida!=null) SERV.CM_PREC_VIDA = espacio.precio_vida;
						if(espacio.nicho) SERV.CM_TIPO_ESPA = 1;
						else if(espacio.mausoleo) SERV.CM_TIPO_ESPA = 2;
						else if(espacio.tumba) SERV.CM_TIPO_ESPA = 3;
						else SERV.CM_TIPO_ESPA = 4;
					}
				}
				if(p.getAcce!=null){
					SERV.CM_ACCE_PREC = p.getAcce();
					if(SERV.CM_ACCE_PREC==null){
						return K.notification({title: 'Accesorios no seleccionados',text: 'Debe seleccionar accesorios para poder realizar los c&aacute;lculos!',type: 'error'});
					}
				}
				variables = p.$w.data('vars');
				if(variables==null){
					return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=variables.length; i<j; i++){
					try{
						if(variables[i].valor=='true') eval('var '+variables[i].cod+' = true;');
						else if(variables[i].valor=='false') eval('var '+variables[i].cod+' = false;');
						else eval('var '+variables[i].cod+' = '+variables[i].valor+';');
					}catch(e){
						console.warn('error en carga de variables');
					}
				}
				$table = p.$w.find('fieldset:last');
				$table.find(".gridBody").empty();
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					var $row = $table.find('.gridReference').clone();
					var monto = eval(conceptos[i].formula);
					$row.find('li:eq(0)').html(conceptos[i].nomb);
					if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
						eval('var '+conceptos[i].cod+' = 0;');
						var formula = conceptos[i].formula;
						formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
						$row.find('li:eq(1)').html('<input type="text" size="7" name="codform'+conceptos[i].cod+'">');
						$row.find('[name^=codform]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
							$(this).change();
						}}).change(function(){
							var val = parseFloat($(this).val()),
							formula = $(this).data('form'),
							cod = $(this).data('cod'),
							$row = $(this).closest('.item');
							eval("var __VALUE"+cod+"__ = "+val+";");
							var monto = eval(formula);
							$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
							eval("var "+cod+" = "+monto+";");
							$row.data('monto',monto);
							for(var ii=0,jj=conceptos.length; ii<jj; ii++){
								var $table = p.$w.find('fieldset:last'),
								$row = $table.find('.gridBody .item').eq(ii),
								$cell = $row.find('li').eq(2),
								monto = eval($cell.data('formula'));
								if($cell.data('formula')!=null){
									$cell.html(ciHelper.formatMon(monto));
									$row.data('monto',monto);
								}
							}
							p.calcConc();
						}).data('form',formula).data('cod',conceptos[i].cod);
						$row.find('li:eq(1) .ui-button').css('height','14px');
					}else{
						$row.find('li:eq(2)').data('formula',conceptos[i].formula);
					}
					$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
					$row.wrapInner('<a class="item" name="'+conceptos[i]._id.$id+'" />');
					$row.find('.item').data('monto',monto);
					$table.find(".gridBody").append( $row.children() );
				}
				p.calcConc();
			},
			calcConc: function(){
				var $table, servicio, conceptos, total = 0, cuotas=0;
				$table = p.$w.find('fieldset:last');
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					total += parseFloat($table.find('.item').eq(i).data('monto'));
				}
				if(conceptos.length!=$table.find('.item').length){
					$table.find('.item:last').remove();
				}
				var $row = $table.find('.gridReference').clone();
				$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default');
				$row.find('li:eq(1)').html('Total');
				$row.find('li:eq(2)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="item" />');
				$row.find('.item').data('total',total);
				$table.find(".gridBody").append( $row.children() );
			}
		});
		new K.Modal({
			id: 'windowNewCuen',
			title: 'Nueva Cuenta por Cobrar',
			contentURL: 'cj/cuen/edit',
			icon: 'ui-icon-plusthick',
			width: 540,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						cliente: p.$w.find('[name=nomb]').data('data'),
						servicio: p.$w.find('[name^=serv]').data('data'),
						fecven: p.$w.find('[name=fecven]').val(),
						conceptos: [],
			            total: parseFloat(p.$w.find('.item:last').data('total')),
			            saldo: parseFloat(p.$w.find('.item:last').data('total')),
			            moneda: 'S'
					};
					if(data.cliente==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cliente!',type: 'error'});
					}else data.cliente = ciHelper.enti.dbRel(data.cliente);
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
					if(data.fecven==''){
						p.$w.find('[name=fecven]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
					}
			        var $table = p.$w.find('fieldset:last'),
			        conceptos = $table.find('[name^=serv]').data('concs');
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
			            tmp.monto = parseFloat($table.find('.item').eq(i).data('monto'));
			            tmp.saldo = tmp.monto;
			            data.conceptos.push(tmp);
			        }
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/cuen/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Cuenta por cobrar fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewCuen');
				p.$w.find('[name=btnSelEnt]').click(function(){
					mgEnti.windowSelect({$window: p.$w,callback: p.cbCli});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgrEnt]').click(function(){
					//ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbCli});
				}).button({icons: {primary: 'ui-icon-plusthick'}}).remove();
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('[name^=btnServ]').click(function(){
					var $row = $(this).closest('tr');
					mgServ.windowSelect({aplicacion: 'A',
						callback: function(data){
							$row.find('[name^=serv]').html('').removeData('data');
							p.$w.find('[id^=tabsConcPayment] .gridBody').empty();
							$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
								if(concs.serv==null){
									return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
								}
								p.$w.data('vars',concs.vars);
								$row.find('[name^=serv]').html(data.nomb).data('data',data).data('concs',concs.serv);
								p.loadConc();
							},'json');
						}
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=fecven]').val(ciHelper.dateFormatNowBDNotHour()).change(function(){
					p.loadConc();
				}).datepicker();
				p.$w.find('[name^=btnServ]').click();
			}
		});
	},
	windowNewCompr: function(p){
		$.extend(p,{
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.$w.find('[name=tasa]').val();
				for(var i=0,j=p.$w.find('.gridBody:eq(1) [name^=cuenta_]').length; i<j; i++){
					total += parseFloat(p.$w.find('.gridBody:eq(1) [name^=cuenta_]').eq(i).data('monto'));
				}
				p.$w.find('[name=totalGrid]').data('monto',total)
				.find('li:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.$w.find('[name=totalGridConv]').data('monto',total)
				.find('li:eq(1)').html(ciHelper.formatMon(total));
			}
		});
		p.save = function(){
			var data = {
				cliente: ciHelper.enti.dbRel(p.cliente),
				caja: p.$w.find('[name=caja] option:selected').data('data'),
				tipo: p.$w.find('[name=comp] option:selected').val(),
				serie: p.$w.find('[name=serie] option:selected').html(),
				num: p.$w.find('[name=num]').val(),
				fecreg: p.$w.find('[name=fecreg]').val(),
				observ: p.$w.find('[name=observ]').val(),
				moneda: p.moneda,
				items: [],
				total: p.$w.find('[name=totalGrid]').data('monto')
			};
			if(data.moneda=='D'){
				data.total = data.total*p.tasa;
			}
			if(data.num==''){
				p.$w.find('[name=num]').focus();
				return K.notification({
					title: ciHelper.titleMessages.infoReq,
					text: 'Debe ingresar un n&uacute;mero de comprobante!',
					type: 'error'
				});
			}
			if(data.moneda=='D'){
				data.tc = p.$w.find('[name=tasa]').val();
				data.total_soles = p.$w.find('[name=totalGridConv]').data('monto');
			}else{
				data.tc = p.tasa;
			}
			data.caja = {
				_id: data.caja._id.$id,
				nomb: data.caja.nomb,
				local: {
					_id: data.caja.local._id.$id,
					descr: data.caja.local.descr,
					direccion: data.caja.local.direccion
				}
			};
			for(var i=0,j=p.cuentas.length; i<j; i++){
				var item = {
					cuenta_cobrar: {
						_id: p.cuentas[i]._id.$id,
						servicio: {
							_id: p.cuentas[i].servicio._id.$id,
							nomb: p.cuentas[i].servicio.nomb,
							organizacion: {
								_id: p.cuentas[i].servicio.organizacion._id.$id,
								nomb: p.cuentas[i].servicio.organizacion.nomb
							}
						}
					},
					total: p.cuentas[i].total,
					conceptos: []
				};
				for(var k=0,l=p.cuentas[i].conceptos.length; k<l; k++){
					var concepto = {
						concepto: {
							_id: p.cuentas[i].conceptos[k].concepto._id.$id,
							nomb: p.cuentas[i].conceptos[k].concepto.nomb
						},
						monto: p.$w.find('.gridBody [name='+p.cuentas[i]._id.$id+']').eq(k).find('[name=sub]').val()
					};
					item.conceptos.push(concepto);
				}
				data.items.push(item);
			}
			/*if(parseFloat(data.total)==0){
				return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El comprobante no puede tener como total 0!',type: 'error'});
			}*/
			var tot = 0;
			tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
			tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())*p.tasa;
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero de voucher!',type: 'error'});
					}
					if(data.vouchers==null) data.vouchers = [];
					data.vouchers.push(tmp);
					tot += (tmp.moneda=='S')?tmp.monto:tmp.monto*p.tasa;
				}
			}
			if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
				return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total del comprobante no coincide con el total de la forma de pagar!',type: 'error'});
			}
			K.sendingInfo();
			p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
			$.post('cj/cuen/save_comp',data,function(comp){
				K.clearNoti();
				K.closeWindow(p.$w.attr('id'));
				K.notification({title: ciHelper.titleMessages.regiGua,text: 'Comprobante creado con &eacute;xito!'});
				$('#pageWrapperLeft .ui-state-highlight').click();
				K.windowPrint({
					id:'windowcjFactPrint',
					title: "Recibo de Caja",
					url: "cj/comp/print_reci?id="+comp._id.$id
				});
			},'json');
		};
		K.Modal({
			id: 'windowNewCompr',
			title: 'Nuevo Comprobante',
			contentURL: 'cj/cuen/new_comp',
			icon: 'ui-icon-plusthick',
			width: 705,
			height: 410,
			buttons: {
				"Guardar": function(){
					if(p.total_all_cuentas>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
						var detrac = p.total_all_cuentas*0.12;
						ciHelper.confirm(
							'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
							function () {
								p.save();
							},
							function () {
								//nothing
							}										
						);
					}else{
						p.save();
					}										
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewCompr');
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$w.find('[name=num]').replaceWith('<input type="text" name="num" size="7"/>');
				p.$w.find('[name=fecreg]').val(K.date()).change(function(){
					if(p.calcular!=null){
						for(var i=0,j=p.cuentas.length; i<j; i++){
							if(p.cuentas[i].calcular!=null){
								var total_tmp = 0;
								var $table,espacio,conceptos,variables,servicio,SERV={},__VALUE__=0;
								SERV = {
									FECVEN: 0,
									CM_PREC_PERP: 0,
									CM_PREC_TEMP: 0,
									CM_PREC_VIDA: 0,
									CM_ACCE_PREC: 0,
									CM_TIPO_ESPA: 0
								};
								__VALUE__ = parseFloat(p.cuentas[i].saldo);
								SERV.SALDO = parseFloat(p.cuentas[i].saldo);
								if(p.$w.find('[name=fecreg]').val()==''){
									p.$w.find('[name=fecreg]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar una fecha de emisi&oacute;n para el c&aacute;lculo de intereses!',
										type: 'error'
									});
								}
								SERV.FECVEN = ciHelper.date.diffDays(p.$w.find('[name=fecreg]').datepicker('getDate'),new Date(p.cuentas[i].fecven.sec*1000));
								if(SERV.FECVEN<0) SERV.FECVEN = 0;
								if(p.cuentas[i].operacion!=null){
									if(p.cuentas[i].modulo=='CM'){
										if(p.cuentas[i].operacion.espacio!=null){
											if(p.cuentas[i].operacion.espacio.precio_perp!=null)
												SERV.CM_PREC_PERP = p.cuentas[i].operacion.espacio.precio_perp;
											if(p.cuentas[i].operacion.espacio.precio_temp!=null)
												SERV.CM_PREC_TEMP = p.cuentas[i].operacion.espacio.precio_temp;
											if(p.cuentas[i].operacion.espacio.precio_vida!=null)
												SERV.CM_PREC_VIDA = p.cuentas[i].operacion.espacio.precio_vida;
											if(p.cuentas[i].operacion.espacio.nicho) SERV.CM_TIPO_ESPA = 1;
											else if(p.cuentas[i].operacion.espacio.mausoleo)
												SERV.CM_TIPO_ESPA = 2;
											else if(p.cuentas[i].operacion.espacio.tumba)
												SERV.CM_TIPO_ESPA = 3;
											else
												SERV.CM_TIPO_ESPA = 4;
										}
										if(p.cuentas[i].operacion.colocacion!=null){
											for(var ii=0; ii<p.cuentas[i].operacion.colocacion.accesorios.length; ii++){
												SERV.CM_ACCE_PREC += parseFloat(p.cuentas[i].operacion.colocacion.accesorios[ii].precio);
											}
										}
									}
									if(p.cuentas[i].modulo=='IN'){
										SERV.IN_PREC = p.cuentas[i].operacion.arrendamiento.renta;
										SERV.IN_MONEDA = p.cuentas[i].operacion.arrendamiento.moneda;
										SERV.IN_OCUP_INI = p.cuentas[i].operacion.arrendamiento.fecini;
										SERV.IN_OCUP_FIN = p.cuentas[i].operacion.arrendamiento.fecfin;
									}
								}
								variables = p.vars;
								for(var iii=0; iii<variables.length; iii++){
									try{
										if(variables[iii].valor=='true') eval('var '+variables[iii].cod+' = true;');
										else if(variables[iii].valor=='false') eval('var '+variables[iii].cod+' = false;');
										else eval('var '+variables[iii].cod+' = '+variables[iii].valor+';');
									}catch(e){
										console.warn('error en carga de variables');
									}
								}
								conceptos = p.cuentas[i].conceptos;
								for(var ii=0; ii<conceptos.length; ii++){
									eval('var '+conceptos[ii].concepto.cod+' = '+parseFloat(conceptos[ii].monto)+';');
									if(conceptos[ii].calcular!=null){
										eval('var '+conceptos[ii].concepto.cod+' = '+eval(conceptos[ii].concepto.formula)+';');
										if(p.cuentas[i].conceptos[ii].monto==p.cuentas[i].conceptos[ii].saldo){
											p.cuentas[i].conceptos[ii].monto = eval(conceptos[ii].concepto.formula);
											p.cuentas[i].conceptos[ii].saldo = eval(conceptos[ii].concepto.formula);
										}else{
											var monto_tmp = eval(conceptos[ii].formula);
											p.cuentas[i].conceptos[ii].saldo += monto_tmp-parseFloat(p.cuentas[i].conceptos[ii].monto);
											p.cuentas[i].conceptos[ii].monto = eval(conceptos[ii].concepto.formula);
										}
										total_tmp += eval(conceptos[ii].concepto.formula);
									}else
										total_tmp += parseFloat(conceptos[ii].monto);
								}
								p.cuentas[i].saldo += total_tmp-parseFloat(p.cuentas[i].total);
								p.cuentas[i].total = total_tmp;
								/*
								 * Se actualiza la grilla donde esta presente
								 */
								p.$w.find('.gridBody:eq(1) [name=cuenta_'+p.cuentas[i]._id.$id+']').data('data',p.cuentas[i]);
								p.$w.find('.gridBody:eq(1) [name=cuenta_'+p.cuentas[i]._id.$id+'] li:eq(2)').html(ciHelper.formatMon(p.cuentas[i].saldo,p.moneda));
								for(var k=0,l=p.cuentas[i].conceptos.length; k<l; k++){
									p.$w.find('.gridBody:eq(1) [name='+p.cuentas[i]._id.$id+']').eq(k).find('li:eq(2)').html(ciHelper.formatMon(p.cuentas[i].conceptos[k].saldo,p.moneda));
								}
							}
						}
					}
				}).datepicker();
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({
						//entidad: p.cliente,
						callback: function(data){
							p.cliente = data;
							p.$w.find('[name=nomb]').html(data.nomb);
							if(data.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
							else p.$w.find('[name=apell]').html(data.appat+' '+data.apmat);
							p.$w.find('[name=dni]').html(data.docident[0].num);
							if(data.domicilios!=null) p.$w.find('[name=direc]').html(data.domicilios[0].direccion);
							if(data.telefonos!=null) p.$w.find('[name=telef]').html(data.telefonos[0].num);
						}
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgr]').html('Editar');
				p.$w.find('[name=btnAgr]').click(function(){
					var id_tmp = p.cliente._id.$id;
					ciEdit.windowEditEntidad({
						id: p.cliente._id.$id,
						nomb: p.cliente.fullname,
						tipo_enti: p.cliente.tipo_enti,
						data: p.cliente,
						$window: p.$w,callBack: function(data){
							
							
							
							
							
							
							
							p.cliente = data;
							p.$w.find('[name=nomb]').html(data.nomb);
							if(data.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
							else p.$w.find('[name=apell]').html(data.appat+' '+data.apmat);
							p.$w.find('[name=dni]').html(data.docident[0].num);
							if(data.domicilios!=null) p.$w.find('[name=direc]').html(data.domicilios[0].direccion);
							K.closeWindow('windowEntiEdit'+id_tmp);
						}
					});
				}).button({icons: {primary: 'ui-icon-pencil'}});
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				});
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$.post('cj/cuen/get_info_comp',{cuentas: p.cuentas,cliente: p.cliente},function(data){
					/*Cliente*/
					p.tasa = parseFloat(data.tasa.valor);
					p.cliente = data.cliente;
					p.$w.find('[name=nomb]').html(data.cliente.nomb);
					if(data.cliente.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
					else p.$w.find('[name=apell]').html(data.cliente.appat+' '+data.cliente.apmat);
					p.$w.find('[name=dni]').html(data.cliente.docident[0].num);
					if(data.cliente.domicilios!=null) p.$w.find('[name=direc]').html(data.cliente.domicilios[0].direccion);
					if(data.cliente.telefonos!=null) p.$w.find('[name=telef]').html(data.cliente.telefonos[0].num);
					/*Cajas*/
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
					}
					for(var i=0,j=data.cajas.length; i<j; i++){
						$select.append('<option value="'+data.cajas[i]._id.$id+'">'+data.cajas[i].nomb+'</option>')
						.find('option:last').data('data',data.cajas[i]);
					}
					$select.change(function(){
						$.post('cj/talo/get_caja','caja='+$(this).find('option:selected').val(),function(data){
							var $select = p.$w.find('[name=comp]').data('data',data).empty();
							for(var i=0,j=data.length; i<j; i++){
								
								if(data[i].tipo=='R'){
									if($select.find('[value='+data[i].tipo+']').length<=0)
										$select.append('<option value="'+data[i].tipo+'">'+cjTalo.types[data[i].tipo]+'</option>');
									if($select.find('option').length==3) i = j;
								}
							}
							$select.unbind('change').change(function(){
								var $sel = p.$w.find('[name=serie]').empty(),
								talos = p.$w.find('[name=comp]').data('data'),
								$this = $(this);
								for(var i=0,j=talos.length; i<j; i++){
									if($this.find('option:selected').val()==talos[i].tipo)
										$sel.append('<option value="'+talos[i]._id.$id+'">'+talos[i].serie+'</option>')
										.find('option:last').data('data',talos[i]);
								}
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					/*Servicios*/
					p.moneda = data.cuentas[0].moneda;
					p.total_all_cuentas = 0;
					for(var i=0,j=data.cuentas.length; i<j; i++){
						p.total_all_cuentas+=parseFloat(data.cuentas[i].total);
						if(data.cuentas[i].calcular!=null){
							var total_tmp = 0;
							p.calcular = true;
							var $table,espacio,conceptos,variables,servicio,SERV={},__VALUE__=0;
							SERV = {
								FECVEN: 0,
								CM_PREC_PERP: 0,
								CM_PREC_TEMP: 0,
								CM_PREC_VIDA: 0,
								CM_ACCE_PREC: 0,
								CM_TIPO_ESPA: 0
							};
							__VALUE__ = parseFloat(data.cuentas[i].saldo);
							if(p.$w.find('[name=fecreg]').val()==''){
								p.$w.find('[name=fecreg]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una fecha de emisi&oacute;n para el c&aacute;lculo de intereses!',
									type: 'error'
								});
							}
							SERV.FECVEN = ciHelper.date.diffDays(p.$w.find('[name=fecreg]').datepicker('getDate'),new Date(data.cuentas[i].fecven.sec*1000));
							if(SERV.FECVEN<0) SERV.FECVEN = 0;
							console.log(SERV.FECVEN);
							if(data.cuentas[i].operacion!=null){
								if(data.cuentas[i].modulo=='CM'){
									if(data.cuentas[i].operacion.espacio!=null){
										if(data.cuentas[i].operacion.espacio.precio_perp!=null)
											SERV.CM_PREC_PERP = data.cuentas[i].operacion.espacio.precio_perp;
										if(data.cuentas[i].operacion.espacio.precio_temp!=null)
											SERV.CM_PREC_TEMP = data.cuentas[i].operacion.espacio.precio_temp;
										if(data.cuentas[i].operacion.espacio.precio_vida!=null)
											SERV.CM_PREC_VIDA = data.cuentas[i].operacion.espacio.precio_vida;
										if(data.cuentas[i].operacion.espacio.nicho) SERV.CM_TIPO_ESPA = 1;
										else if(data.cuentas[i].operacion.espacio.mausoleo)
											SERV.CM_TIPO_ESPA = 2;
										else if(data.cuentas[i].operacion.espacio.tumba)
											SERV.CM_TIPO_ESPA = 3;
										else
											SERV.CM_TIPO_ESPA = 4;
									}
									if(data.cuentas[i].operacion.colocacion!=null){
										for(var ii=0; ii<data.cuentas[i].operacion.colocacion.accesorios.length; ii++){
											SERV.CM_ACCE_PREC += parseFloat(data.cuentas[i].operacion.colocacion.accesorios[ii].precio);
										}
									}
								}
								if(data.cuentas[i].modulo=='IN'){
									SERV.IN_PREC = data.cuentas[i].operacion.arrendamiento.renta;
									SERV.IN_MONEDA = data.cuentas[i].operacion.arrendamiento.moneda;
									SERV.IN_OCUP_INI = data.cuentas[i].operacion.arrendamiento.fecini;
									SERV.IN_OCUP_FIN = data.cuentas[i].operacion.arrendamiento.fecfin;
								}
							}
							variables = data.vars;
							p.vars = data.vars;
							for(var iii=0; iii<variables.length; iii++){
								try{
									if(variables[iii].valor=='true') eval('var '+variables[iii].cod+' = true;');
									else if(variables[iii].valor=='false') eval('var '+variables[iii].cod+' = false;');
									else eval('var '+variables[iii].cod+' = '+variables[iii].valor+';');
								}catch(e){
									console.warn('error en carga de variables');
								}
							}
							conceptos = data.cuentas[i].conceptos;
							for(var ii=0; ii<conceptos.length; ii++){
								eval('var '+conceptos[ii].concepto.cod+' = '+parseFloat(conceptos[ii].monto)+';');
								if(conceptos[ii].calcular!=null){
									eval('var '+conceptos[ii].concepto.cod+' = '+eval(conceptos[ii].concepto.formula)+';');
									if(data.cuentas[i].conceptos[ii].monto==data.cuentas[i].conceptos[ii].saldo){
										data.cuentas[i].conceptos[ii].monto = eval(conceptos[ii].concepto.formula);
										data.cuentas[i].conceptos[ii].saldo = eval(conceptos[ii].concepto.formula);
									}else{
										var monto_tmp = eval(conceptos[ii].formula);
										data.cuentas[i].conceptos[ii].saldo += monto_tmp-parseFloat(data.cuentas[i].conceptos[ii].monto);
										data.cuentas[i].conceptos[ii].monto = eval(conceptos[ii].concepto.formula);
									}
									total_tmp += eval(conceptos[ii].concepto.formula);
								}else
									total_tmp += parseFloat(conceptos[ii].monto);
							}
							data.cuentas[i].saldo += total_tmp-parseFloat(data.cuentas[i].total);
							data.cuentas[i].total = total_tmp;
						}
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(0)').html(i+1);
						$row.find('li:eq(1)').html(data.cuentas[i].servicio.nomb);
						$row.find('li:eq(2)').html(ciHelper.formatMon(data.cuentas[i].saldo,p.moneda));
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="cuenta_'+data.cuentas[i]._id.$id+'" />');
						$row.find('.item').data('data',data.cuentas[i]).data('monto',0);
						p.$w.find('.gridBody:eq(1)').append($row.children());
						for(var k=0,l=data.cuentas[i].conceptos.length; k<l; k++){
							var $row = p.$w.find('.gridReference:eq(0)').clone();
							$row.find('li:eq(1)').html('&nbsp;&nbsp;&nbsp;<i>'+data.cuentas[i].conceptos[k].concepto.nomb+'</i>');
							$row.find('li:eq(2)').html(ciHelper.formatMon(data.cuentas[i].conceptos[k].saldo,p.moneda));
							$row.find('li:eq(3)').html('<input type="text" name="sub" size="7" value="'+K.round(data.cuentas[i].conceptos[k].saldo,2)+'">');
							$row.find('[name=sub]').numeric().change(function(){
								var $this = $(this),
								val = 0,
								total = 0,
								cuenta = $this.closest('.item').attr('name');
								for(var i=0,j=p.$w.find('.gridBody:eq(1) [name='+cuenta+']').length; i<j; i++){
									val = p.$w.find('.gridBody:eq(1) [name='+cuenta+']').eq(i).find('[name=sub]').val();
									total += parseFloat((val!='')?val:0);
								}
								p.$w.find('[name=cuenta_'+cuenta+']').data('monto',total)
								.find('li:eq(3)').html(ciHelper.formatMon(total,p.moneda));
								p.calcTot();
							});
							$row.wrapInner('<a class="item" href="javascript: void(0);" name="'+data.cuentas[i]._id.$id+'" />');
							p.$w.find('.gridBody:eq(1)').append($row.children());
							p.$w.find('[name=sub]:last').change();
						}
					}
					var $row = p.$w.find('.gridReference:eq(0)').clone();
					$row.find('li:eq(1),li:eq(2)').remove();
					$row.find('li:eq(0)').html('Total&nbsp;').css('min-width','350px').css('max-width','350px')
					.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
					$row.wrapInner('<a class="item" href="javascript: void(0);" name="totalGrid" />');
					p.$w.find('.gridBody:eq(1)').append($row.children());
					if(p.moneda=='D'){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1),li:eq(2)').remove();
						$row.find('li:eq(0)').html('Tasa de Cambio de Soles a D&oacute;lares&nbsp;');
						$row.find('li:eq(1)').html('<input type="text" name="tasa" size="7" value="0.00">');
						$row.find('[name=tasa]').val(p.tasa).numeric().change(function(){
							p.calcTot();
						});
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1),li:eq(2)').remove();
						$row.find('li:eq(0)').html('Total en Nuevos Soles').css('min-width','350px').css('max-width','350px')
						.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="totalGridConv" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
					}
					p.calcTot();
					p.cuentas = data.cuentas;
					/*Efectivo Soles*/
					var $row = p.$w.find('.gridReference:last').clone();
					$row.find('li:eq(0)').html('Efectivo Soles');
					$row.find('li:eq(2)').html('S/.<input type="text" name="tot" size="7" value="'+K.round(p.total_all_cuentas,2)+'"/>');
					$row.find('[name=tot]').numeric().change(function(){
						$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					$row.wrapInner('<a class="item" name="mon_sol" />');
					p.$w.find('.gridBody:last').append($row.children());
					/*Efectivo Dolares*/
					var $row = p.$w.find('.gridReference:last').clone();
					$row.find('li:eq(0)').html('Efectivo D&oacute;lares');
					$row.find('li:eq(2)').html('$<input type="text" name="tot" size="7"/>');
					$row.find('[name=tot]').val('0.00').numeric().change(function(){
						$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon($(this).val()*p.tasa));
						$(this).closest('.item').data('total',parseFloat($(this).val())*p.tasa);
					});
					$row.wrapInner('<a class="item" name="mon_dol" />');
					p.$w.find('.gridBody:last').append($row.children());
					/*Cuentas bancarios*/
					for(var i=0,j=data.ctban.length; i<j; i++){
						var $row = p.$w.find('.gridReference:last').clone();
						$row.find('li:eq(0)').html('Voucher <input type="text" name="voucher" size="7"/><br /><input type="text" name="fecvou" size="10"/>');
						$row.find('li:eq(1)').html(data.ctban[i].nomb);
						$row.find('li:eq(2)').html((data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="text" name="tot" size="7"/>');
						$row.find('[name=tot]').val('0.00').numeric().change(function(){
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon(tot));
							$(this).closest('.item').data('total',parseFloat(tot));
						});
						$row.find('[name=fecvou]').datepicker();
						$row.wrapInner('<a class="item" name="ctban" />');
						$row.find('.item').data('moneda',data.ctban[i].moneda).data('data',{
							_id: data.ctban[i]._id.$id,
							cod: data.ctban[i].cod,
							nomb: data.ctban[i].nomb,
							moneda: data.ctban[i].moneda,
							cod_banco: data.ctban[i].cod_banco
						});
						p.$w.find('.gridBody:last').append($row.children());
					}
					p.$w.find('.gridBody:last [name=tot]').change();
					if(p.total_all_cuentas>=700){
						K.notification({title:'Para Facturas',text:'Este documento excede los S/. 700'});
					}
					p.$w.find('[name=fecreg]').val(K.date());
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};