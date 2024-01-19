inComp = {
	tipo: {
		R: 'Recibo de Caja',
		RD: 'Recibo definitivo',
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
	init: function(p){
		if(p==null) p = {};
		$.extend(p,{
			load: function(data){
				p.$w.find('[name=cliente]').closest('.form-group').hide();
				p.$w.find('[name=tipo]').closest('.form-group').hide();
				p.$w.find('[name=inmueble]').closest('.form-group').hide();
				p.$w.find('[name=playa]').closest('.form-group').hide();
				p.$w.find('[name=subtotal]').closest('.form-group').hide();
				p.$w.find('[name=moras]').closest('.form-group').hide();
				p.$w.find('[name=igv]').closest('.form-group').hide();
				p.$w.find('[name=total]').closest('.form-group').hide();
				p.$w.find('[name=loading]').show();
				p.$w.find('iframe').hide();
				if(data!=null){
					if(data.cliente!=null){
						p.$w.find('[name=cliente]').closest('.form-group').show();
						if(data.cliente._id!=null)
							p.$w.find('[name=cliente]').html(mgEnti.formatName(data.cliente));
						else
							p.$w.find('[name=cliente]').html(data.cliente);
					}
					if(data.contrato!=null){
						p.$w.find('[name=tipo]').closest('.form-group').show();
						p.$w.find('[name=tipo]').html('PAGO DE ALQUILER');
					}else if(data.acta_conciliacion!=null){
						p.$w.find('[name=tipo]').closest('.form-group').show();
						p.$w.find('[name=tipo]').html('ACTA DE CONCILIACION');
					}else if(data.playa!=null){
						p.$w.find('[name=tipo]').closest('.form-group').show();
						p.$w.find('[name=tipo]').html('PLAYA DE ESTACIONAMIENTO');
						p.$w.find('[name=subtotal]').closest('.form-group').show();
						p.$w.find('[name=subtotal]').html(ciHelper.formatMon(data.subtotal));
						p.$w.find('[name=igv]').closest('.form-group').show();
						p.$w.find('[name=igv]').html(ciHelper.formatMon(data.igv));
						p.$w.find('[name=total]').closest('.form-group').show();
						p.$w.find('[name=total]').html(ciHelper.formatMon(data.total));
					}else{
						p.$w.find('[name=tipo]').closest('.form-group').show();
						p.$w.find('[name=tipo]').html('COBROS VARIOS');
					}
					if(data.inmueble!=null){
						p.$w.find('[name=inmueble]').closest('.form-group').show();
						p.$w.find('[name=inmueble]').html(data.inmueble.direccion);
					}
					if(data.total!=null){
						p.$w.find('[name=total]').closest('.form-group').show();
						p.$w.find('[name=total]').html(ciHelper.formatMon(data.total));
					}
					if(data.playa!=null){
						p.$w.find('iframe').attr('src','in/comp/iframe_playa');
					}else{
						p.$w.find('iframe').attr('src','in/comp/print?_id='+data._id.$id);
					}
				}else{
					p.$w.find('iframe').attr('src','in/comp/iframe');
				}
			}
		});
		K.initMode({
			mode: 'in',
			action: 'inComp',
			titleBar: {
				title: 'Comprobantes'
			}
		});
		new K.Panel({
			contentURL: 'in/comp',
			//store: false,
			//content:'<div name="grid" class="col-sm-4"></div><div class="col-sm-8" name="details"></div>',
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.load();
				p.$w.find('iframe').load(function() {
					p.$w.find('[name=loading]').hide();
					p.$w.find('iframe').show();
				});
				p.$w.find('.datagrid-header-left').removeClass('col-lg-4')
					.addClass('col-sm-12');
				p.$w.find('.datagrid-header-right').removeClass('col-lg-8')
					.addClass('col-sm-12');
		   		var $grid = new K.grid({
		   			$el: p.$w.find('[name=grid]'),
					cols: ['','','Tipo','','Total',{n:'Registrado',f:'fecreg'}],
					data: 'in/comp/lista',
					params: {
						organizacion: '51a50edc4d4a13441100000e'
					},
					itemdescr: 'comprobante(s)',
					toolbarHTML: '<button name="btnNew" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Comprobante</button>&nbsp;'+
						'<button name="btnCom" class="btn btn-primary"><i class="fa fa-compress"></i> Combinar Comprobantes</button>&nbsp;'+
						'<button name="btnGen" class="btn btn-info"><i class="fa fa-gears"></i> Generar Recibo de Ingresos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnNew]').click(function(){
							inComp.windowNew();
						});
						$el.find('[name=btnCom]').click(function(){
							inComp.windowCombinar();
						});
						$el.find('[name=btnGen]').click(function(){
							inComp.windowGen();
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
						$row.append('<td>'+inComp.states[data.estado].label+'</td>');
						$row.append('<td>'+inComp.tipo[data.tipo]+'</td>');
						$row.append('<td>'+data.serie+'-'+data.num+'</td>');
						/*if($.type(data.cliente)==='string'){
							$row.append('<td>'+data.cliente+'</td>');
						}else{
							$row.append('<td>'+mgEnti.formatName(data.cliente)+'</td>');
						}
						$row.append('<td>'+ciHelper.formatMon(data.subtotal,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.igv,data.moneda)+'</td>');*/
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecreg)+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('tipo',data.tipo).dblclick(function(){
							var data = $(this).closest('.item').data('data');
							p.load(data);
						}).click(function(){
							$(this).dblclick();
						}).data('estado',data.estado).contextMenu("conMenInComp", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='X')
									$('#conMenInComp_cam,#conMenInComp_pag',menu).remove();
								else
									$('#conMenInComp_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenInComp_imp': function(t) {
									K.windowPrint({
										id:'windowPrint',
										title: "Comprobante de Pago",
										url: "in/comp/print?_id="+K.tmp.data('id')
									});
								},
								'conMenInComp_eim': function(t) {
									window.open('in/ecom/print?_id='+K.tmp.data('id'));
								},
								'conMenInComp_anu': function(t) {
									ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/comp/anular',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inComp.init();
										});
									},function(){
										$.noop();
									},'Anulaci&oacute;n de Comprobante');
								},
								'conMenInComp_cam': function(t) {
									inComp.windowCambiar({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenInComp_pag': function(t) {
									inComp.windowVoucher({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenInComp_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/comp/eliminar',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Eliminado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inComp.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Comprobante');
								},
								'conMenInComp_eco': function(t) {
									window.open('in/ecom/generate_xml?_id='+K.tmp.data('id'));
								},
								'conMenInComp_sen': function(t) {
									window.open('in/ecom/send_sunat?_id='+K.tmp.data('id'));
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
			contentURL: 'in/comp/edit_comp',
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
							moneda: 'S',
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
				            data.total += tmp.monto;
				            /*modificación para aceptar inafectos de 1 solo item*/
				            //if(conceptos[i].igv=="inafecto"){
				            if(data.tipo=="R"){
				            	if(conceptos.length==1) tmp.monto = K.round(tmp.monto,2);
				            }
				            else{
				            	if(conceptos.length==1) tmp.monto = K.round(tmp.monto/(1+(p.igv/100)),2);
				            }
				            //if(conceptos.length==1) tmp.monto = K.round(tmp.monto/(1+(p.igv/100)),2);
				            tmp.saldo = tmp.monto;
				            data.conceptos.push(tmp);
				        }
				        if(data.conceptos.length==1){
				        	//if(conceptos[0].igv=="inafecto"){
				        	if(data.tipo=="R"){
								data.conceptos.push({
									concepto: 'IGV ('+((p.igv)*100)+'%)',
									monto: K.round(0,2),
									cuenta: {
										_id: p.conf.IGV._id.$id,
										descr: p.conf.IGV.descr,
										cod: p.conf.IGV.cod
									}
								});
							}
							else{
								data.conceptos.push({
									concepto: 'IGV ('+((p.igv)*100)+'%)',
									monto: K.round(data.total-(data.total/(1+(p.igv/100))),2),
									cuenta: {
										_id: p.conf.IGV._id.$id,
										descr: p.conf.IGV.descr,
										cod: p.conf.IGV.cod
									}
								});
							}
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
						//if(conceptos[0].igv=="inafecto"){
						//console.log(data);
						if(data.tipo=="R"){
							data.subtotal = K.round(parseFloat(data.total),2);
							data.igv = K.round(parseFloat(0),2);
						}
						else{
							data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv)/100)),2);
							data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
						}
						//data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv)/100)),2);
						//data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/comp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Comprobante agregado!"});
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Comprobante de Pago",
								url: "in/comp/print?_id="+result._id.$id
							});
							inComp.init();
						},'json');
					}
				},
				'Cancelar': {
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
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						}
					});
				});
				p.$w.find('[name=btnAct]').click(function(){
					if(p.$w.find('[name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						},id: p.$w.find('[name=mini_enti]').data('data')._id.$id});
					}
				});
				//p.$w.find('[name=btnAct]').hide();
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
				$.post('in/comp/get_info_comp',function(data){
					p.talo = data.talo;
					p.igv = parseFloat(data.igv.valor);
					p.conf = data.conf;
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
							inComp.init();
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
	
	
	
	




















	
	
	
	
	
	
	windowCombinar: function(p){
		if(p==null) p = {};
		mgEnti.windowSelect({
			bootstrap: true,
			callback: function(data){
				new K.Panel({
					content: '<div name="grid"></div>',
					buttons: {
						'Combinar Comprobantes': {
							icon: 'fa-compress',
							type: 'success',
							f: function(){
								var comprobantes = [],
								moneda = '';
								for(var i=0; i<p.$w.find('[name=rbtnComp]:checked').length; i++){
									if(moneda=='') moneda = p.$w.find('[name=rbtnComp]:checked').eq(i).closest('.item').data('data').moneda;
									if(p.$w.find('[name=rbtnComp]:checked').eq(i).closest('.item').data('data').moneda!=moneda){
										return K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'Debe elegir comprobantes de la misma moneda!',
											type: 'error'
										});
									}
									comprobantes.push(p.$w.find('[name=rbtnComp]:checked').eq(i).closest('.item').data('data'));
								}
								if(comprobantes.length<2){
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe elegir al menos dos comprobantes!',
										type: 'error'
									});
								}
								p.moneda = moneda;
								p.comprobantes = comprobantes;
								$.extend(p,{
									moneda: moneda
								});
								new K.Panel({
									contentURL: 'in/movi/comp_mes',
									buttons: {
										'Generar Comprobante': {
											icon: 'fa-save',
											type: 'success',
											f: function(){
												var data = {
													modulo: 'IN',
													combinacion_alq: true,
													cliente: p.$w.find('[name=mini_enti]').data('data'),
													caja: p.$w.find('[name=caja] option:selected').data('data'),
													tipo: p.$w.find('[name=comp] option:selected').val(),
													serie: p.$w.find('[name=serie] option:selected').html(),
													num: p.$w.find('[name=num]').val(),
													fecemi: p.$w.find('[name=fecemi]').val(),
													observ: p.$w.find('[name=observ]').val(),
													moneda: p.moneda,
													valor_igv: p.igv*100,
													items: [],
													total: parseFloat(p.$w.find('[name=totalGrid]').data('monto')),
													sunat: []
												};
												if(data.moneda=='D'){
													data.tc = parseFloat(p.$w.find('[name=tasa]').val());
													data.total_dolares = parseFloat(p.$w.find('[name=totalGrid]').data('monto'));
													data.total_soles = parseFloat(p.$w.find('[name=totalGridConv]').data('monto'));
													data.total = data.total_soles;
													p.tc = p.$w.find('[name=tasa]').val();
												}else{
													data.tc = p.tc;
												}
												data.cliente = mgEnti.dbRel(data.cliente);
												if(data.num==''){
													p.$w.find('[name=num]').focus();
													return K.notification({
														title: ciHelper.titleMessages.infoReq,
														text: 'Debe ingresar un n&uacute;mero de comprobante!',
														type: 'error'
													});
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
												for(var i=0,j=p.comprobantes.length; i<j; i++){
													var comp = p.comprobantes[i],
													item = {
														_id: comp._id.$id,
														total: parseFloat(comp.total),
														observ: comp.observ,
														items: comp.items
													};
													for(var ii=0;ii<item.items.length; ii++){
														if(item.items[ii].cuenta_cobrar!=null){
															if(item.items[ii].cuenta_cobrar._id!=null)
																item.items[ii].cuenta_cobrar._id = item.items[ii].cuenta_cobrar._id.$id;
															if(item.items[ii].cuenta_cobrar.servicio._id!=null)
																item.items[ii].cuenta_cobrar.servicio._id = item.items[ii].cuenta_cobrar.servicio._id.$id;
															if(item.items[ii].cuenta_cobrar.servicio.organizacion._id!=null)
																item.items[ii].cuenta_cobrar.servicio.organizacion._id = item.items[ii].cuenta_cobrar.servicio.organizacion._id.$id;
														}
														if(item.items[ii].conceptos!=null){
															for(var iii=0;iii<item.items[ii].conceptos.length; iii++){
																if(item.items[ii].conceptos[iii].cuenta!=null)
																	item.items[ii].conceptos[iii].cuenta._id = item.items[ii].conceptos[iii].cuenta._id.$id;
																if(item.items[ii].conceptos[iii].concepto!=null)
																	if(item.items[ii].conceptos[iii].concepto._id!=null)
																		item.items[ii].conceptos[iii].concepto._id = item.items[ii].conceptos[iii].concepto._id.$id;
															}
														}
													}
													if(comp.contrato!=null) item.contrato = comp.contrato.$id;
													if(comp.alquiler!=null) item.alquiler = true;
													if(comp.parcial!=null) item.parcial = true;
													if(comp.compensacion!=null) item.compensacion = true;
													if(comp.acta!=null) item.acta = true;
													if(comp.acta_conciliacion!=null) item.acta_conciliacion = comp.acta_conciliacion.$id;
													data.items.push(item);
													if(comp.sunat!=null){
														data.sunat.push(comp.sunat[0]);	
													}
												}
												var tot = 0;
												tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
												tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())*parseFloat(p.tc);
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
												if(parseFloat(data.total)==0){
													return K.notification({
														title: ciHelper.titleMessages.infoReq,
														text: 'El comprobante no puede tener como total 0!',
														type: 'error'
													});
												}
												if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
													return K.notification({
														title: ciHelper.titleMessages.infoReq,
														text: 'El total del comprobante no coincide con el total de la forma de pagar!',
														type: 'error'
													});
												}
												data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
												data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
												K.sendingInfo();
												p.$w.find('#div_buttons button').attr('disabled','disabled');
												$.post('in/comp/save_combinar',data,function(comp){
													K.clearNoti();
													inComp.init();
													K.notification({title: ciHelper.titleMessages.regiGua,text: 'Comprobantes combinados con &eacute;xito!'});
													
													
													
													K.windowPrint({
														id:'windowPrint',
														title: "Comprobante de Pago",
														url: "in/comp/print?print=1&_id="+comp._id.$id
													});
													
												},'json');
											}
										},
										'Cancelar': {
											icon: 'fa-ban',
											type: 'danger',
											f: function(){
												inComp.init();
											}
										}
									},
									onContentLoaded: function(){
										p.$w = $('#mainPanel');
										K.block();
										new K.grid({
											$el: p.$w.find('[name=gridServ]'),
											search: false,
											pagination: false,
											cols: ['N&deg;','Servicios','Importe Total'],
											onlyHtml: true
										});
										new K.grid({
											$el: p.$w.find('[name=gridForm]'),
											search: false,
											pagination: false,
											cols: ['Descripci&oacute;n','','Subtotal',''],
											onlyHtml: true
										});
										p.$w.find('[name=tipo]').closest('.form-group').remove();
										p.$w.find('[name=sublocal]').closest('.form-group').remove();
										p.$w.find('[name=inmueble]').closest('.form-group').remove();
										p.$w.find('[name=titular]').closest('.form-group').remove();
										p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
										p.$w.find('[name=btnSel]').click(function(){
											mgEnti.windowSelect({
												bootstrap: true,
												callback: function(data){
													mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
												}
											});
										});
										mgEnti.fillMini(p.$w.find('[name=mini_enti]'),comprobantes[0].cliente);
										p.$w.find('[name=btnAct]').click(function(){
											if(p.$w.find('[name=mini_enti]').data('data')==null){
												K.notification({
													title: ciHelper.titleMessages.infoReq,
													text: 'Debe elegir una entidad!',
													type: 'error'
												});
											}else{
												mgEnti.windowEdit({callback: function(data){
													mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
												},id: p.$w.find('[name=mini_enti]').data('data')._id.$id});
											}
										});
										p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
										$.post('in/movi/get_var_mes',function(data){
											p.tc = data.tc.valor;
											p.calf = data.calf;
											p.ctban = data.ctban;
											p.cuenta = data.cuenta;
											p.conf = data.conf;
											for(var i=0,j=data.vars.length; i<j; i++){
												if(data.vars[i].cod=='IGV')
													p.igv = (parseFloat(data.vars[i].valor)/100);
												else if(data.vars[i].cod=='MORA')
													p.mora = parseFloat(data.vars[i].valor);
												else if(data.vars[i].cod=='DETRACCION')
													p.detraccion = parseFloat(data.vars[i].valor);
											}
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
														if($select.find('[value='+data[i].tipo+']').length<=0)
															$select.append('<option value="'+data[i].tipo+'">'+cjTalo.types[data[i].tipo]+'</option>');
														if($select.find('option').length==3) i = j;
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
														var tmp_total = p.$w.find('[name=totalGrid]').data('monto');
														if(p.$w.find('[name=comp] option:selected').val()=='F'){
															var tmp_efec = tmp_total - (tmp_total*p.detraccion),
															tmp_detr = tmp_total*p.detraccion;
															p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
															p.$w.find('[name=gridForm] [name=voucher]:eq(2)').val('XXX');
															p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(K.round(tmp_detr,2));
														}else{
															p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_total,2));
															p.$w.find('[name=gridForm] [name=voucher]:eq(2)').val('');
															p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(K.round(0,2));
														}
														$sel.unbind('change').change(function(){
															p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
														});
														if(p.comprobantes!=null&&p.first==null){
															for(var jj=0; jj<$sel.find('option').length; jj++){
																if($sel.find('option').eq(jj).html()==p.comprobantes[0].serie){
																	$sel.val($sel.find('option').eq(jj).val());
																	p.$w.find('[name=num]').val(p.comprobantes[0].num);
																	p.first = true;
																	break;
																}
															}
														}else{
															$sel.change();
														}
													});
													if(p.comprobantes!=null&&p.first==null){
														p.$w.find('[name=comp]').selectVal(p.comprobantes[0].tipo);
														$select.change();
													}else{
														$select.change();
													}
												},'json');
											}).change();
											var total = 0,
											$form2 = p.$w.find('[name=texto_inmueble]').closest('form');
											$form2.find('[name=texto_inmueble]').closest('.form-group').remove();
											$form2.find('.control-label').html('IMPRESION DEL ITEM');
											var $form = $form2.html();
											$form2.hide();
											for(var i=0; i<p.comprobantes.length; i++){
												var comp = p.comprobantes[i],
												alquiler = false,
												acta = false,
												parcial = false;
												total += parseFloat(comp.total);
												console.info(comp);
												if(typeof comp.alquiler != 'undefined') alquiler = true;
												if(typeof comp.acta != 'undefined') acta = true;
												if(typeof comp.parcial != 'undefined') parcial = true;
												var $row = $('<tr class="item" name=descr_>');
												$row.append('<td colspan="4">'+$form+'</td>');
												if(comp.sunat!=null){
													$row.find('[name=texto_pago]').val(comp.sunat[0].descr);
												}
												p.$w.find('[name=gridServ] tbody').append($row);
												for(var j=0; j<comp.items.length; j++){
													var $row = $('<tr class="item" name="serv_">'),
													tot_tmp = 0;
													$row.append('<td>'+(i+1)+'.'+(j+1)+'</td>');
													if(comp.items[j].cuenta_cobrar!=null)
														$row.append('<td><i>'+comp.items[j].cuenta_cobrar.servicio.nomb+'</i></td>');
													else
														$row.append('<td><i>'+comp.items[j].conceptos[0].concepto+'</i></td>');
													$row.append('<td>');
													p.$w.find('[name=gridServ] tbody').append($row);
													for(var k=0; k<comp.items[j].conceptos.length; k++){
														if(parseFloat(comp.items[j].conceptos[k].monto)!=0){
															var $row = $('<tr class="item">');
															$row.append('<td>');
															if(comp.items[j].conceptos[k].concepto.nomb!=null)
																$row.append('<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+comp.items[j].conceptos[k].concepto.nomb+'</td>');
															else
																$row.append('<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+comp.items[j].conceptos[k].concepto+'</td>');
															$row.append('<td>'+ciHelper.formatMon(comp.items[j].conceptos[k].monto,comp.moneda)+'</td>');
															p.$w.find('[name=gridServ] tbody').append($row);
															tot_tmp += parseFloat(comp.items[j].conceptos[k].monto);
														}
													}
													p.$w.find('[name=serv_]:last td:eq(2)').html('<b>'+ciHelper.formatMon(tot_tmp,comp.moneda)+'</b>');
												}
											}
											if(p.moneda=='D'){
												total = total / p.tc;
												total_tmp = total;
											}
											p.total_tmp = total;
											p.total = total;
											var $row = $('<tr class="item info" name="totalGrid">');
											$row.append('<th colspan="2">Total</th>');
											$row.append('<th>'+ciHelper.formatMon(total,p.moneda));
											p.$w.find('[name=gridServ] table:last').append('<tfoot>');
											p.$w.find('[name=gridServ] tfoot').append($row);
											p.$w.find('[name=totalGrid]').data('monto',p.total)
											if(p.moneda=='D'){
												var $row = $('<tr class="item info">');
												$row.append('<th colspan="2">Tasa de Cambio de Soles a D&oacute;lares</th>');
												$row.append('<th><input type="number" name="tasa" size="7" value="0"></th>');
												$row.find('[name=tasa]').val(p.tc).keyup(function(){
													p.total = p.total * parseFloat($(this).val()!=''?$(this).val():0);
													p.$w.find('[name=totalGridConv]').data('monto',K.round(p.total,2))
														.find('th:eq(1)').html(ciHelper.formatMon(p.total));
												});
												p.$w.find('[name=gridServ] tfoot').append($row);
												var $row = $('<tr class="item info" name="totalGridConv">');
												$row.append('<th colspan="2">Total en Soles</th>');
												$row.append('<th>');
												p.$w.find('[name=gridServ] tfoot').append($row);
											}
											//Efectivo Soles
											var $row = $('<tr class="item" name="mon_sol">');
											$row.append('<td>Efectivo Soles</td>');
											$row.append('<td>');
											$row.append('<td><div class="input-group col-sm-8">'+
												'<input class="form-control" name="tot" type="number" />'+
											'</div></td>');
											$row.append('<td>S/.0.00</td>');
											$row.find('[name=tot]').val(0).keyup(function(){
												if($(this).val()=='')
													$(this).val(0);
												$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
												$(this).closest('.item').data('total',parseFloat($(this).val()));
											});
											p.$w.find('[name=gridForm] tbody').append($row);
											//Efectivo Dolares
											var $row = $('<tr class="item" name="mon_dol">');
											$row.append('<td>Efectivo D&oacute;lares</td>');
											$row.append('<td>');
											$row.append('<td><div class="input-group col-sm-8">'+
												'<input class="form-control" name="tot" type="number" />'+
											'</div></td>');
											$row.append('<td>S/.0.00</td>');
											$row.find('[name=tot]').val(0).keyup(function(){
												if($(this).val()=='')
													$(this).val(0);
												$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
												$(this).closest('.item').data('total',parseFloat($(this).val()));
											});
											p.$w.find('[name=gridForm] tbody').append($row);
											//Cuentas bancarios
											for(var i=0,j=p.ctban.length; i<j; i++){
												var $row = $('<tr class="item" name="ctban">');
												$row.append('<td>Voucher '+
													'<input class="form-control" name="voucher" type="text" />'+
												'</td>');
												$row.append('<td>'+p.ctban[i].nomb+'</td>');
												$row.append('<td><div class="input-group col-sm-8">'+
													'<input class="form-control" name="tot" type="number" />'+
												'</div></td>');
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
											p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(p.total,2));
											if(p.total>=700){
												p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
												p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(p.total*p.detraccion)+'</p>');
												var tmp_efec = total - (total*p.detraccion),
												tmp_detr = total*p.detraccion;
												p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
												p.$w.find('[name=gridForm] [name=voucher]:eq(2)').val('XXX');
												p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(K.round(tmp_detr,2));
											}
											p.$w.find('[name=gridForm] [name=tot]').keyup();
											if(p.moneda=='D')
												p.$w.find('[name=tasa]').keyup();
											K.unblock();
										},'json');
									}
								});
							}
						},
						'Regresar': {
							icon: 'fa-refresh',
							type: 'danger',
							f: function(){
								inComp.init();
							}
						}
					},
					onContentLoaded: function(){
						p.$w = $('#mainPanel');
						var $grid = new K.grid({
							$el: p.$w.find('[name=grid]'),
							cols: ['','','Tipo','','Subtotal','IGV','Total',{n:'Registrado',f:'fecreg'}],
							data: 'in/comp/lista',
							params: {
								organizacion: '51a50edc4d4a13441100000e',
								cliente: data._id.$id,
								//alquileres: true,
								estado: 'R'
							},
							itemdescr: 'comprobante(s)',
							search: false,
							pagination: false,
							onLoading: function(){
								K.block();
							},
							onComplete: function(){
								K.unblock();
							},
							fill: function(data,$row){
								if(data.estado!=null){
									$row.append('<td><input type="checkbox" name="rbtnComp" class="iCheck" /></td>');
									$row.append('<td>'+inComp.states[data.estado].label+'</td>');
									$row.append('<td>'+inComp.tipo[data.tipo]+'</td>');
									$row.append('<td>'+data.serie+'-'+data.num+'</td>');
									$row.append('<td>'+ciHelper.formatMon(data.subtotal,data.moneda)+'</td>');
									$row.append('<td>'+ciHelper.formatMon(data.igv,data.moneda)+'</td>');
									$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
									$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecreg)+'</td>');
									$row.data('id',data._id.$id).data('tipo',data.tipo).dblclick(function(){
										var $row = $(this).closest('.item');
										K.windowPrint({
											id:'windowPrint',
											title: "Comprobante de Pago",
											url: "in/comp/print?_id="+$row.data('id')
										});
									}).data('data',data);
									$row.iCheck({
										checkboxClass: 'icheckbox_square-green',
										radioClass: 'iradio_square-green'
									});
									return $row;
								}
							}
						});
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
				$.post('cj/rein/get_rec_in',{
					tipo_inm: p.$w.find('[name=tipo_inm] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: '51a50edc4d4a13441100000e',//orga._id.$id
					actividad: '51e995a64d4a13ac0b00000e',//orga.actividad._id.$id
					componente: '51e99cec4d4a13ac0b000011'//orga.componente._id.$id
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
					total_ = 0;
					// Comprobantes manuales
					if(data.comp!==null) for(var i=0,j=data.comp.length; i<j; i++){
						var result = data.comp[i];
						if(result.tipo_pago!=null){
							if(result.tipo_pago=='CO'){
								continue;
							}
						}
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
							if(result.playa!=null){
								/*
								 * SUBTOTAL
								 */
								result.subtotal = parseFloat(K.round(parseFloat(result.subtotal),2));
								result.igv = parseFloat(K.round(result.igv,2))
								var $row = $('<tr class="item">');
								$row.append('<td>'+result.playa.cuenta.cod+' - '+result.playa.cuenta.descr+'</td>');
								$row.append('<td>'+inComp.tipo[result.tipo]+' '+result.serie+'-'+result.num+'</td>');
								$row.append('<td>'+result.cliente+'</td>');
								$row.append('<td>'+ciHelper.formatMon(result.subtotal,result.moneda)+'</td>');
								$row.data('data',{
									cuenta: {
										_id: result.playa.cuenta._id.$id,
										cod: result.playa.cuenta.cod,
										descr: result.playa.cuenta.descr
									},
									comprobante: {
										_id: result._id.$id,
										tipo: result.tipo,
										serie: result.serie,
										num: result.num
									},
									concepto: result.cliente
								}).data('total',result.subtotal);
								p.$w.find('[name=gridComp] tbody').append($row);
								tot_sol += result.subtotal;
								var tmp_ctas_pat_i = -1;
								for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
									if(tmp_ctas_pat[tmp_i].cod==result.playa.cuenta.cod.substr(0,9)){
										tmp_ctas_pat_i = tmp_i;
										tmp_i = tmp_j;
									}
								}
								if(tmp_ctas_pat_i==-1){
									tmp_ctas_pat.push({
										cod: result.playa.cuenta.cod.substr(0,9),
										cuenta: result.playa.cuenta,
										total: result.subtotal
									});
								}else{
									tmp_ctas_pat[tmp_ctas_pat_i].total += result.subtotal;
								}
								total += parseFloat(K.round(result.subtotal,2));
								/*
								 * IGV
								 */
								var $row = $('<tr class="item">');
								$row.append('<td>'+cuenta_igv.cod+' - '+cuenta_igv.descr+'</td>');
								$row.append('<td>'+inComp.tipo[result.tipo]+' '+result.serie+'-'+result.num+'</td>');
								$row.append('<td>'+result.cliente+'</td>');
								$row.append('<td>'+ciHelper.formatMon(result.igv,result.moneda)+'</td>');
								$row.data('data',{
									cuenta: cuenta_igv,
									comprobante: {
										_id: result._id.$id,
										tipo: result.tipo,
										serie: result.serie,
										num: result.num
									},
									concepto: result.cliente+' - '+result.playa.nomb
								}).data('total',result.igv);
								p.$w.find('[name=gridComp] tbody').append($row);
								tot_sol += parseFloat(K.round(result.igv,2));
								tmp_ctas_pat[0].total += parseFloat(K.round(result.igv,2));
								total += parseFloat(K.round(result.igv,2));
							}else if(result.alquiler!=null){
								for(var k=0,l=result.items.length; k<l; k++){
									var item = result.items[k];
									for(var m=0,n=item.conceptos.length; m<n; m++){
										var conc = item.conceptos[m],
										$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
										tot_conc = (result.moneda=='S')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tc);
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
											var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.num+'</td>');
											$row.append('<td>'+conc.concepto+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')+'</td>');
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
												contrato: result.contrato.$id,
												concepto: mgEnti.formatName(result.cliente)+' - '+result.inmueble.direccion+' - '+item.pago.ano+'-'+item.pago.mes
											});
											p.$w.find('[name=gridComp] tbody').append($row);
										}
										total += parseFloat(K.round(tot_conc,2));
									}
								}
								if(result.efectivos!=null){
									if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
									if(parseFloat(result.efectivos[1].monto)!=0){
										tot_dol += parseFloat(result.efectivos[1].monto);
										tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tc);
									}
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
							}else if(result.items){
								for(var k=0,l=result.items.length; k<l; k++){
									var item = result.items[k];
									if(item.items!=null){


										/*PARA COMBINADOS*/
										for(var mn=0,nn=item.items.length; mn<nn; mn++){
											var itemm = item.items[mn];
											for(var m=0,n=itemm.conceptos.length; m<n; m++){
												var conc = itemm.conceptos[m],
												$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
												tot_conc = (result.moneda=='S')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tc);
												
												
												var tmp_ctas_pat_i = -1;
												for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
													if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
														tmp_ctas_pat_i = tmp_i;
														tmp_i = tmp_j;
													}
												}
												
												console.info(tmp_ctas_pat[tmp_ctas_pat_i]+'->'+conc.monto);

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
													var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
													$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
													$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.num+'</td>');
													$row.append('<td>'+conc.concepto+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')+'</td>');
													$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
													var temp = {
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
														}
													};
													if(item.contrato!=null){
														temp.contrato = item.contrato.$id;
													}
													if(result.inmueble!=null){
														temp.concepto = mgEnti.formatName(result.cliente)+' - '+result.inmueble.direccion+' - '+item.items[0].pago.ano+'-'+item.items[0].pago.mes;
													}else{
														temp.concepto = mgEnti.formatName(result.cliente);
													}
													$row.data('total',tot_conc).data('data',temp);
													p.$w.find('[name=gridComp] tbody').append($row);
												}
												total += parseFloat(K.round(tot_conc,2));
											}
										}







									}else{
										for(var m=0,n=item.conceptos.length; m<n; m++){
											var conc = item.conceptos[m],
											tot_conc = (result.moneda=='S')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tc);
											//tot_conc = (result.moneda=='S')?parseFloat(result.total):parseFloat(conc.total)*parseFloat(result.tc);
											if(conc.concepto._id!=null)
												var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc.concepto._id.$id+']');
											else
												var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-conc]');
											
											
											
											
											
											
											
											
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
											if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
												if(tmp_ctas_pat_i!=-1)
													console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
												else
													console.log(tmp_ctas_pat[tmp_ctas_pat.length-1].total);
											}
											//tmp_ctas_pat
											
											
											
											
											
											
											
											if($row.length>0){
												tot_conc_sec = tot_conc + parseFloat($row.data('total'));
												$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
												$row.data('total',tot_conc_sec);
											}else{
												if(conc.concepto._id!=null)
													var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc.concepto._id.$id+'">');
												else
													var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-conc">');
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
											total += parseFloat(K.round(tot_conc,2));
										}
									}
								}
								if(result.efectivos!=null){
									if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
									if(parseFloat(result.efectivos[1].monto)!=0){
										tot_dol += parseFloat(result.efectivos[1].monto);
										tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tc);
									}
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
								for(var k=0,l=result.conceptos.length; k<l; k++){
									var item = result.conceptos[k];
										var conc = item.concepto;
										if(conc._id==null){
											conc = {
												_id: {$id:'IGV'+result._id.$id},
												nomb: item.concepto
											}
										}
										if(conc.cuenta==null){
											if(item.cuenta._id.$id==null) item.cuenta._id = {
												$id: item.cuenta._id
											};
											conc.cuenta = item.cuenta;
										}
										var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+']'),
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
												total: parseFloat(K.round(item.monto,2))
											});
										}else{
											tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(item.monto,2));
										}
										/*if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
											console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
										}*/
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
										total += parseFloat(K.round(tot_conc,2));
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
							}
							total_ += parseFloat(K.round(parseFloat(result.total),2));
						}
					}
					// LOS COMPROBANTES ELECTRONICOS PERMITEN HASTA EL MOMENTO 3 TIPOS DE PAGOS:
					//	a) Pagos de alquileres mensuales enteros
					//	b) Pago de alquileres mensuales parciales
					//	c) Pago de Actas de conciliacion
					//	d) Pago de Playas
					//
					if(data.ecom!==null) for(var i=0,j=data.ecom.length; i<j; i++){
						var result = data.ecom[i];
						// AUN NO SE PARA QUE SIRVE ESTO (COMPENSACION?)
						if(result.tipo_pago!=null){
							if(result.tipo_pago=='CO'){
								continue;
							}
						}
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
								//PARA PAGOS MENSuALES COMPLETOS Y PARCIALES
								if(item.tipo=="pago_meses" || item.tipo=="pago_parcial"){
									for(var m=0,n=item.conceptos.length; m<n; m++){
										var conc = item.conceptos[m],
										$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
										tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
										if(conc.pago!=null){
											conc_pare_pago=conc.pago;
										}
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
											var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
											$row.append('<td>'+conc.descr+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
											$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
											//if(conc.pago != null){
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
													num: result.numero
												},
												contrato: conc.alquiler.contrato.$id,
												concepto: result.cliente_nomb+' - '+result.inmueble.direccion+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes
												//concepto: result.cliente_nomb+' - '+'DE UN INMUEBLE'+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes
											});
											//}
											p.$w.find('[name=gridComp] tbody').append($row);
										}
										total += parseFloat(K.round(tot_conc,2));
									}
								}//PARA PAGO DE ACTAS DE CONCILIACION
								else if(item.tipo=="pago_acta"){
									for(var m=0,n=item.conceptos.length; m<n; m++){
											var conc = item.conceptos[m];
											//if(conc._id==null){
											//	conc = {
											//		_id: {$id:'IGV'+result._id.$id},
											//		nomb: item.concepto
											//	}
											//}
											if(conc.cuenta==null){
												console.log("Concepto con cuenta null");
												console.log(conc);
												console.log("Fin cuenta null");
												if(item.cuenta._id.$id==null) item.cuenta._id = {
													$id: item.cuenta._id
												};
												conc.cuenta = item.cuenta;
											}
											//var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+']'),
											var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
											tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
											
											
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
													total: parseFloat(K.round(conc.monto,2))
												});
											}else{
												tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(conc.monto,2));
											}
											/*if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
												console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
											}*/
											//tmp_ctas_pat
											
											
											if($row.length>0){
												tot_conc_sec = tot_conc + parseFloat($row.data('total'));
												$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
												$row.data('total',tot_conc_sec);
											}else{
												var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
												//var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+'">');
												$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
												$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
												//$row.append('<td>'+result.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
												$row.append('<td>'+conc.descr+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
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
													//concepto: result.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')
													//concepto: result.cliente_nomb+' - '+result.inmueble.direccion+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes


													//concepto: result.cliente_nomb+' - '+"result.inmueble.direccion"+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes
													concepto: 'Pago de Acta de Conciliaci&oacute;n - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')
												});
												p.$w.find('[name=gridComp] tbody').append($row);
											}
											total += parseFloat(K.round(tot_conc,2));
									}
								}//PARA PAGO DE SERVICIOS DE CUeNTAS POR COBRAR COMO LUZ, GARANTIAS, ETC
								else if(item.tipo=="cuenta_cobrar"){
									for(var m=0,n=item.conceptos.length; m<n; m++){
										var conce = item.conceptos[m];
										if(conce.concepto!==null){
											var conc = conce.concepto;
											if(conc._id==null){
												conc = {
													_id: {$id:'IGV'+result._id.$id},
													nomb: item.concepto
												}
											}
											if(conc.cuenta==null){
												if(conce.cuenta._id.$id==null) conce.cuenta._id = {
													$id: conce.cuenta._id
												};
												conc.cuenta = conce.cuenta;
											}
											var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+']'),
											tot_conc = (result.moneda=='PEN')?parseFloat(conce.monto):parseFloat(conce.monto)*parseFloat(result.tipo_cambio);
											
											
											
											
											
											
											
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
													total: parseFloat(K.round(conce.monto,2))
												});
											}else{
												tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(conce.monto,2));
											}
											/*if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
												console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
											}*/
											//tmp_ctas_pat
											}
										
										
										
										
										
										
										if($row.length>0){
											tot_conc_sec = tot_conc + parseFloat($row.data('total'));
											$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
											$row.data('total',tot_conc_sec);
										}else{
											var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+'">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
											$row.append('<td>'+item.cuenta_cobrar.servicio.nomb+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
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
													num: result.numero
												},
												concepto: item.cuenta_cobrar.servicio.nomb+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')
											});
											p.$w.find('[name=gridComp] tbody').append($row);
										}
										total += parseFloat(K.round(tot_conc,2));
									}
								}//PARA PAGO DE PLAYAS (Por ahora)
								else if(item.tipo=="servicio"){
									for(var m=0,n=item.conceptos.length; m<n; m++){
										var conc = item.conceptos[m],
										$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
										tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
										
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
											var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
											$row.append('<td>'+result.cliente_nomb+'</td>');
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
													num: result.numero
												},
												concepto: result.cliente_nomb
											});
											p.$w.find('[name=gridComp] tbody').append($row);
										}
									}


/*PORSIAC SE COMENTA ESTO
									$row.append('<td>'+ciHelper.formatMon(result.subtotal,result.moneda)+'</td>');
									$row.data('data',{
										cuenta: {
											_id: result.playa.cuenta._id.$id,
											cod: result.playa.cuenta.cod,
											descr: result.playa.cuenta.descr
										},
										comprobante: {
											_id: result._id.$id,
											tipo: result.tipo,
											serie: result.serie,
											num: result.numero
										},
										concepto: result.cliente
									}).data('total',result.subtotal);
									p.$w.find('[name=gridComp] tbody').append($row);
									tot_sol += result.subtotal;
									var tmp_ctas_pat_i = -1;
									
									total += parseFloat(K.round(result.subtotal,2));
									//
									// IGV
									//
									var $row = $('<tr class="item">');
									$row.append('<td>'+cuenta_igv.cod+' - '+cuenta_igv.descr+'</td>');
									$row.append('<td>'+inComp.tipo[result.tipo]+' '+result.serie+'-'+result.num+'</td>');
									$row.append('<td>'+result.cliente+'</td>');
									$row.append('<td>'+ciHelper.formatMon(result.igv,result.moneda)+'</td>');
									$row.data('data',{
										cuenta: cuenta_igv,
										comprobante: {
											_id: result._id.$id,
											tipo: result.tipo,
											serie: result.serie,
											num: result.numero
										},
										concepto: result.cliente+' - '+result.playa.nomb
									}).data('total',result.igv);
									p.$w.find('[name=gridComp] tbody').append($row);
									tot_sol += parseFloat(K.round(result.igv,2));
									tmp_ctas_pat[0].total += parseFloat(K.round(result.igv,2));
									total += parseFloat(K.round(result.igv,2));
*/										
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
							/*for(var m=0,n=item.conceptos.length; m<n; m++){
								var conc = item.conceptos[m],
								tot_conc = (result.moneda=='S')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tc);
								//tot_conc = (result.moneda=='S')?parseFloat(result.total):parseFloat(conc.total)*parseFloat(result.tc);
								//if(conc.concepto._id!=null)
								//	var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc.concepto._id.$id+']');
								//else
									var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-conc]');
								
								
								
								
								
								
								
								
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
								if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
									if(tmp_ctas_pat_i!=-1)
										console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
									else
										console.log(tmp_ctas_pat[tmp_ctas_pat.length-1].total);
								}
								//tmp_ctas_pat
								
								
								
								
								
								
								
								if($row.length>0){
									tot_conc_sec = tot_conc + parseFloat($row.data('total'));
									$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
									$row.data('total',tot_conc_sec);
								}else{
									//if(conc.concepto._id!=null)
									//	var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc.concepto._id.$id+'">');
									//else
										var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-conc">');
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
								total += parseFloat(K.round(tot_conc,2));
							}*/				
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
			contentURL: 'cj/comp/gen_bootstrap',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: p.$w.find('[name=num]').val(),
							modulo: 'IN',
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
						/*data.organizacion = {
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
						};*/
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
							inRein.init();
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
};
define(
	['mg/enti','mg/orga','in/rein','mg/serv','cj/talo'],
	function(mgEnti,mgOrga,inRein,mgServ,cjTalo){
		return inComp;
	}
);