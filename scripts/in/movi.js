/*
 * MOVIMIENTOS INMUEBLES
 */
inMovi = {
	meses_cobranza_dudosa: 12,
	situacion: [
		{
			cod: 'O',
			descr: 'OCUPADO'
		},
		{
			cod: 'U',
			descr: 'CESION EN USO'
		},
		{
			cod: 'C',
			descr: 'CONVENIO'
		},
		{
			cod: 'D',
			descr: 'DESOCUPADO'
		},
		{
			cod: 'M',
			descr: 'COMODATO'
		}
	],
	estado_pago: {
		P: {
			descr: "Pendiente",
			color: "green",
			label: '<span class="label label-default">Pendiente</span>'
		},
		C: {
			descr: "Cancelado",
			color: "green",
			label: '<span class="label label-success">Cancelado</span>'
		},
		X: {
			descr: "Anulado",
			color: "#CCCCCC",
			label: '<span class="label label-warning">Anulado</span>'
		}
	},
	tipo_doc_gar: {
		RI: 'Recibo de Ingresos',
		RC: 'Recibo de Caja',
		NC: 'Nota Contable',
		FACT: 'Factura',
		CP: 'Comprobante de Pago',
		BV: 'Boleta de Venta'
	},
	/**************************************************************************************************************************
	 *
	 * OBTENER GLOSA DE MOTIVO DE CONTRATO
	 *
	 **************************************************************************************************************************/
	get_motivo: function (motivo_id) {
		var texto_pago = '';
		switch (motivo_id) {
		case '55316577bc795ba80100003b':
			//SIN CONTRATO
			texto_pago = 'ALQUILER';
			break;
		case '55316565bc795ba801000037':
			//RENOVACION SIN CONTRATO
			texto_pago = 'ALQUILER';
			break;
		case '5531652fbc795ba80100002d':
			//ACTA DE CONCILIACION
			texto_pago = 'ALQUILER';
			break;
		case '5531656fbc795ba801000039':
			//RENOVACION
			texto_pago = 'ALQUILER';
			break;
		case '5531654cbc795ba801000033':
			//NUEVO
			texto_pago = 'ALQUILER';
			break;
		case '5540f3c3bc795b7801000029':
			//convenios
			texto_pago = 'ALQUILER';
			break;
		case '55316543bc795ba801000031':
			//convenios
			texto_pago = 'POR AUTORIZACION';
			break;
		case "55316553bc795ba801000035":
			texto_pago = 'PENALIDAD';
			break;
		case "5531656fbc795ba801000039":
			//RENOVACION
			texto_pago = 'ALQUILER';
			break;
		case "5531657bbc795ba80100003d":
			//TRANSPASO		
			texto_pago = 'ALQUILER';
			break;
		default:
			texto_pago = 'XXXXXXXXXXXXX';
		}
		return texto_pago;
	},
	/**************************************************************************************************************************
	 *
	 * VERIFICAR SI ES COBRANZA DUDOSA O NO
	 *
	 **************************************************************************************************************************/
	get_cobranza_dudosa: function (mes, ano) {
		var mes_hoy = ciHelper.date.get.now_m(),
			ano_hoy = ciHelper.date.get.now_y();
		if (ano < ano_hoy) {
			if ((ano_hoy - ano) == 1) {
				if (mes < mes_hoy) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	},
	/**************************************************************************************************************************
	 *
	 * VISTA INICIAL DE CONTRATOS
	 *
	 **************************************************************************************************************************/
	init: function (p) {
		if (p == null) p = {};
		K.initMode({
			mode: 'in',
			action: 'inMovi',
			titleBar: {
				title: 'Movimientos Diarios'
			}
		});

		new K.Panel({
			contentURL: 'in/movi',
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				//p.$w.find('[name=tipo],[name=sublocal],[name=inmueble]').chosen();
				p.$w.find('[name=tipo]').change(function () {
					var $this = $(this),
						val = p.tipo[$this.find('option:selected').val()]._id.$id,
						$cbo = p.$w.find('[name=sublocal]').empty();
					if (p.sublocal != null) {
						for (var i = 0, j = p.sublocal.length; i < j; i++) {
							if (p.sublocal[i].tipo._id.$id == val)
								$cbo.append('<option value="' + i + '">' + p.sublocal[i].nomb + '</option>');
						}
					}
					$cbo.chosen().trigger("chosen:updated");
					$cbo.change();
				});
				p.$w.find('[name=sublocal]').change(function () {
					p.$w.find('[name=inmueble]').empty();
					if ($(this).find('option').length == 0) {
						p.$w.find('[name=inmueble]').change();
						return K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe escoger un local v&aacute;lido!',
							type: 'error'
						});
					}
					K.block();
					$.post('in/inmu/get_all_sub', {
						_id: p.sublocal[$(this).find('option:selected').val()]._id.$id
					}, function (data) {
						var $cbo = p.$w.find('[name=inmueble]').empty();
						if (data != null) {
							for (var i = 0, j = data.length; i < j; i++) {
								$cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].abrev + '</option>');
								$cbo.find('option:last').data('data', data[i]);
							}
						}
						$cbo.chosen().trigger("chosen:updated");
						$cbo.change();
						K.unblock();
					}, 'json');
				});
				p.$w.find('[name=btnRefresh]').click(function (e) {
					e.preventDefault();
					p.$w.find('[name=inmueble]').change();
				});
				p.$w.find('[name=inmueble]').change(function () {
					var inmueble = $(this).find('option:selected').data('data');
					if (inmueble != null) {
						$.jStorage.set('in/movi/get_all_cont', inInmu.dbRel(inmueble));
						K.block();
						$.post('in/movi/get_all_cont', {
							_id: inmueble._id.$id,
							mostrar: p.$w.find('[name=mostrar] option:selected').val()
						}, function (data) {
							p.contratos = data.contratos;
							p.actas = data.actas;
							p.titulares = [];
							p.$w.find('[name=gridCont] tbody').empty();
							p.$w.find('[name=gridArre] tbody').empty();
							p.$w.find('[name=gridPag] tbody').empty();
							p.$w.find('[name=gridCta] tbody').empty();
							if (p.contratos != null) {
								for (var i = 0, j = p.contratos.length; i < j; i++) {
									var $row = $('<tr class="item" data-titu="' + p.contratos[i].titular._id.$id + '">');
									$row.append('<td>' + p.contratos[i].motivo.nomb +
										'<br /><button class="btn btn-warning btn-xs"><i class="fa fa-money"></i> Record de Pagos</button>' +
										'</td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.contratos[i].fecini) + '</td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.contratos[i].fecfin) + '</td>');
									if (p.contratos[i].fecdes.sec != 0 && p.contratos[i].fecdes.sec != -68400)
										$row.append('<td style="color:red">' + ciHelper.date.format.bd_ymd(p.contratos[i].fecdes) + '</td>');
									else
										$row.append('<td>');
									$row.append('<td>' + ciHelper.formatMon(p.contratos[i].importe, p.contratos[i].moneda) + '</td>');
									$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Editar</button></td>');
									$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');

									$row.find('button:eq(0)').click(function () {
										var params = {
											contrato: $(this).closest('.item').data('data')._id.$id
										};
										K.windowPrint({
											id: 'windowPrint',
											title: "Record de Pago",
											url: 'in/repo/record_pago?' + $.param(params)
										});
									});
									$row.find('button:eq(1)').click(function () {
										var contrato = $(this).closest('.item').data('data');
										inMovi.editContrato({
											id: contrato._id.$id
										});
									});
									$row.find('button:eq(2)').click(function () {
										var $row = $(this).closest('.item'),
											contrato = $row.data('data');
										ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Contrato <b>' + $row.find('td:eq(0)').html() + '</b>&#63;',
											function () {
												K.sendingInfo();
												$.post('in/movi/delete_contrato', {
													_id: contrato._id.$id
												}, function () {
													K.clearNoti();
													K.msg({
														title: 'Contrato Eliminado',
														text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
													});
													$row.remove();
												});
											},
											function () {
												$.noop();
											}, 'Eliminaci&oacute;n de Contrato de Alquiler');
									});



									$row.find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').click(function () {
										p.$w.find('[name=gridPag] tbody .item').remove();
										p.$w.find('[name=gridCta] tbody').empty();
										var contrato = $(this).closest('.item').data('data'),
												dia_ini_tmp = ciHelper.date.format.bd_d(contrato.fecini),
												dia_fin_tmp = ciHelper.date.format.bd_d(contrato.fecfin),
												tmp_desoc_d = parseInt(ciHelper.date.format.bd_d(contrato.fecdes)),
												tmp_desoc_m = ciHelper.date.format.bd_m(contrato.fecdes),
												tmp_ini_m = ciHelper.date.format.bd_m(contrato.fecini),
												tmp_fin_m = ciHelper.date.format.bd_m(contrato.fecfin),
												tmp_desoc_y = ciHelper.date.format.bd_y(contrato.fecdes),
												tmp_fin_y = ciHelper.date.format.bd_y(contrato.fecfin);
										console.log(contrato);
										if (contrato.pagos != null) {
											for (var i = 0; i < contrato.pagos.length; i++) {
												var tmp_impor = null;
												if (parseInt(dia_ini_tmp) == 1) {
													if (parseInt(tmp_desoc_m) == parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														tmp_impor = parseFloat(contrato.importe) / 30 * tmp_desoc_d;
														//asdasd
													} else if (parseInt(tmp_desoc_m) < parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) <= parseInt(contrato.pagos[i].ano)) {
														//
														break;
													}
												} else if(parseInt(dia_ini_tmp) == 15 && contrato._id.$id == '62475bdf3e6037b04f8b4567'){
													if (parseInt(tmp_desoc_m) == parseInt(contrato.pagos[i].mes-1) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														tmp_impor = parseFloat(contrato.importe) / 30 * tmp_desoc_d;
													} else if (parseInt(tmp_desoc_m) <= parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														tmp_impor = parseFloat(contrato.importe) / 30 * 20;
													}
												}else{
													if (parseInt(tmp_desoc_m) == (parseInt(contrato.pagos[i].mes) - 1) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														console.info(contrato.pagos[i]);
														console.log('tmp_impor');
														//asdasd
													} else if (parseInt(tmp_desoc_m) < parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														
														console.log('entro');
													} 
												}









												var $row = $('<tr class="item">');
												$row.append('<td>' + (parseInt(contrato.pagos[i].item)) + '</td>');
												if (parseInt(dia_ini_tmp) == 1) {
													$row.append('<td>' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano + '</td>');
												} else {
													var mes_n = parseInt(contrato.pagos[i].mes) - 1,
														ano_n = parseInt(contrato.pagos[i].ano);
													mes_n--;
													if (mes_n == -1) {
														mes_n = 11;
														ano_n--;
													}
													var tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[mes_n] + '-' + ano_n,
														tmp_fin = 'Al ' + (parseInt(dia_ini_tmp) - 1) + ' de ' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano;
													if (tmp_impor != null) {
														tmp_fin = 'Al ' + tmp_desoc_d + ' de ' + ciHelper.meses[parseInt(tmp_desoc_m) - 1] + '-' + tmp_desoc_y;
													}
													if(contrato.contrato_dias==1)
													{	var mes_n_temp = parseInt(contrato.pagos[i].mes) - 1;
														mes_n_temp--;
														if(mes_n_temp==-1)
														{
															ano_n++;
														}
														tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[tmp_ini_m-1] + '-' + ano_n,
														tmp_fin= 'Al ' + dia_fin_tmp + ' de ' + ciHelper.meses[parseInt(tmp_fin_m-1)] + '-' + tmp_fin_y;
													}
													$row.append('<td>' + tmp_ini + '<br />' + tmp_fin + '</td>');
												}
												if (tmp_impor != null) {
													$row.append('<td>' + ciHelper.formatMon(tmp_impor, contrato.moneda) + '</td>');
													$row.data('parcial', tmp_impor);
												} else
													$row.append('<td>' + ciHelper.formatMon(contrato.importe, contrato.moneda) + '</td>');
												if (contrato.pagos[i].estado == 'C') {
													if (contrato.pagos[i].detalle != null) {
														if (parseFloat(contrato.pagos[i].detalle.alquiler) != parseFloat(contrato.importe)) {
															contrato.pagos[i].estado = 'P';
															/*$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">'+ciHelper.formatMon(contrato.pagos[i].total,contrato.moneda)+'</label></span>'+
																'<br /><span>Falta: <label style="color: blue;">'+ciHelper.formatMon(parseFloat(contrato.importe)-parseFloat(contrato.pagos[i].total),contrato.moneda)+'</label></span>');*/
														}
													}
													if (contrato.pagos[i].historico != null) {
														var tmp_hist_tot = 0;
														for (var i_h = 0; i_h < contrato.pagos[i].historico.length; i_h++) {
															tmp_hist_tot += parseFloat(contrato.pagos[i].historico[i_h].total);
														}
														if (contrato.pagos[i].comprobantes != null) {
															for (var i_h = 0; i_h < contrato.pagos[i].comprobantes.length; i_h++) {
																tmp_hist_tot += parseFloat(contrato.pagos[i].comprobantes[i_h].detalle.alquiler);
															}
														}
														if (K.round(tmp_hist_tot, 2) != parseFloat(contrato.importe)) {
															contrato.pagos[i].estado = 'P';
															contrato.pagos[i].total = tmp_hist_tot;
														}
													}
												}
												if (contrato.pagos[i].estado == null) {
													$row.append('<td>' + inMovi.estado_pago['P'].label + '</td>');
													$row.append('<td>');
												} else {
													$row.append('<td>' + inMovi.estado_pago[contrato.pagos[i].estado].label + '</td>');
													if (contrato.pagos[i].comprobante != null) {

														/**
														 *	PARA PAGOS COMPLETOS ("PAGO_PARCIAL")
														 */
														if (contrato.pagos[i].estado == 'P') {
															if (contrato.pagos[i].detalle != null) {
																/*contrato.pagos[i].detalle = {
																	alquiler: 0
																	//
																};
																if(contrato.pagos[i].historico!=null){
																	for(var i_ho=0; i_ho<contrato.pagos[i].historico.length; i_ho++){
																		contrato.pagos[i].detalle.alquiler += parseFloat(contrato.pagos[i].historico[i_ho].total);
																	}
																}*/
															}
															
																$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">' + ciHelper.formatMon(contrato.pagos[i].detalle.alquiler, contrato.moneda) + '</label></span>' +
																'<br /><span>Falta: <label style="color: blue;">' + ciHelper.formatMon(parseFloat(contrato.importe) - parseFloat(contrato.pagos[i].detalle.alquiler), contrato.moneda) + '</label></span>');
															
														}
														$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> ' + contrato.pagos[i].comprobante.tipo + ' ' + contrato.pagos[i].comprobante.serie + '-' + contrato.pagos[i].comprobante.num + '</button></td>');
														/****************************************************************************************************
														 * IMPRESION DE COMPROBANTE ELECTRONICA Y MANUAL EN PAGOS REALIZADOS EN CONTRATOS
														 *****************************************************************************************************/
														$row.data('id', contrato.pagos[i].comprobante._id.$id).data('tipo', contrato.pagos[i].comprobante.tipo).data('serie', contrato.pagos[i].comprobante.serie);

														$row.find('button').click(function () {
															var $row = $(this).closest('.item');
															var firstvalue = $row.data('serie').substring(0, 1);
															console.log("PRIMERA LETRA DE LA SERIE:" + firstvalue);
															if (/^([0-9])*$/.test(firstvalue)) {
																/********************************************************************************************************
																 * IMPRESION DE COPROBANTE
																 ********************************************************************************************************/
																console.log("COMPROBANTE DE PAGO COMPLETO MANUAL ");
																K.windowPrint({
																	id: 'windowPrint',
																	title: "Comprobante de Pago",
																	url: "in/comp/print?_id=" + $row.data('id'),
																});
															} else {
																console.log("COMPROBANTE DE PAGO COMPLETO ELECTRONICO");
																K.windowPrint({
																	id: 'windowPrint',
																	title: "Comprobante de Pago Electronico",
																	url: "cj/ecom/print_preview?_id=" + $row.data('id'),
																});
															}
														});
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															contrato.pagos[i].total = parseFloat(contrato.pagos[i].detalle.alquiler);
															$row.find('td:last').append('<br /><button class="btn btn-xs btn-success"><i class="fa fa-money"></i> Generar Compensaci&oacute;n</button>');
															$row.find('button:last').click(function () {
																var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
																inMovi.newCompe({
																	contrato: contrato,
																	pago: $(this).data('pago')
																});
															}).data('pago', contrato.pagos[i]);
														}
													} else if (contrato.pagos[i].comprobantes != null) {
														/**
														 *	PARA PAGO PARCIAL ("PAGO_PARCIAL")
														 */
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															if (contrato.pagos[i].detalle != null) {
																contrato.pagos[i].detalle = {
																	alquiler: parseFloat(contrato.pagos[i].historico[i_h].total)
																};
															}

															$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">' + ciHelper.formatMon(contrato.pagos[i].total, contrato.moneda) + '</label></span>' +
																'<br /><span>Falta: <label style="color: blue;">' + ciHelper.formatMon(parseFloat(contrato.importe) - parseFloat(contrato.pagos[i].total), contrato.moneda) + '</label></span>');
														}
														$row.append('<td>');
														if (contrato.pagos[i].historico != null) {
															for (var ii = 0; ii < contrato.pagos[i].historico.length; ii++) {
																if ($row.find('td:last').html() != '') {
																	$row.find('td:last').append('<br />');
																}
																$row.find('td:last').append('<i style="color:blue;">' + contrato.pagos[i].historico[ii].tipo + ' ' + contrato.pagos[i].historico[ii].num +
																	'</i> por ' + ciHelper.formatMon(contrato.pagos[i].historico[ii].total, contrato.pagos[i].historico[ii].moneda) +
																	' del <i style="color:green;">' + ciHelper.date.format.bd_ymd(contrato.pagos[i].historico[ii].fec) + '</i>');
															}
														}
														for (var ik = 0; ik < contrato.pagos[i].comprobantes.length; ik++) {
															if (ik != 0)
																$row.find('td:last').append('<br />');
															$row.find('td:last').append('<button class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> ' + contrato.pagos[i].comprobantes[ik].tipo + ' ' + contrato.pagos[i].comprobantes[ik].serie + '-' + contrato.pagos[i].comprobantes[ik].num + '</button>');
															$row.find('button:last').click(function () {
																/****************************************************************************************************
																 * IMPRESION DE COMPROBANTE ELECTRONICA Y MANUAL EN PAGOS REALIZADOS EN PAGOS PARCIALES
																 *****************************************************************************************************/
																var firstvalue = $(this).data('serie').substring(0, 1);
																if (/^([0-9])*$/.test(firstvalue)) {
																	/********************************************************************************************************
																	 * IMPRESION DE COPROBANTE
																	 ********************************************************************************************************/
																	console.log("COMPROBANTE DE PAGO PARCIAL MANUAL");
																	K.windowPrint({
																		id: 'windowPrint',
																		title: "Comprobante de Pago",
																		url: "in/comp/print?_id=" + $(this).data('id')
																	});
																} else {
																	console.log("COMPROBANTE DE PAGO PARCIAL ELECTRONICO");
																	K.windowPrint({
																		id: 'windowPrint',
																		title: "Comprobante de Pago Electronico",
																		url: "cj/ecom/print_preview?_id=" + $(this).data('id')
																	});
																}
																//}).data('id',contrato.pagos[i].comprobantes[ik]._id.$id).data('tipo',contrato.pagos[i].comprobantes[ik].tipo);
															}).data('id', contrato.pagos[i].comprobantes[ik]._id.$id).data('tipo', contrato.pagos[i].comprobantes[ik].tipo).data('tipo', contrato.pagos[i].comprobantes[ik].tipo).data('serie', contrato.pagos[i].comprobantes[ik].serie);
															//$row.find('td:last').append('<br /><span>Base: ' + contrato.pagos[i].comprobantes[ik].detalle.alquiler + ', IGV: ' + contrato.pagos[i].comprobantes[ik].detalle.igv + '</span><br />');
														}
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															$row.find('td:last').append('<br /><button class="btn btn-xs btn-success"><i class="fa fa-money"></i> Generar Compensaci&oacute;n</button>');
															$row.find('button:last').click(function () {
																var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
																inMovi.newCompe({
																	contrato: contrato,
																	pago: $(this).data('pago')
																});
															}).data('pago', contrato.pagos[i]);
														}
													} else if (contrato.pagos[i].historico != null) {
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															/* PARCHE PARA MOSTRAR CONTRATO */
															if (contrato.pagos[i].total == null) {
																var tmp_hist_tot = 0;
																for (var i_h = 0; i_h < contrato.pagos[i].historico.length; i_h++) {
																	tmp_hist_tot += parseFloat(contrato.pagos[i].historico[i_h].total);
																}
																if (K.round(tmp_hist_tot, 2) != parseFloat(contrato.importe)) {
																	contrato.pagos[i].estado = 'P';
																	contrato.pagos[i].total = tmp_hist_tot;
																}
															}
															console.log(contrato.pagos[i]);
															$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">' + ciHelper.formatMon(contrato.pagos[i].total, contrato.moneda) + '</label></span>' +
																'<br /><span>Falta: <label style="color: blue;">' + ciHelper.formatMon(parseFloat(contrato.importe) - parseFloat(contrato.pagos[i].total), contrato.moneda) + '</label></span>');
														}
														$row.append('<td>');
														for (var ii = 0; ii < contrato.pagos[i].historico.length; ii++) {
															if ($row.find('td:last').html() != '') {
																$row.find('td:last').append('<br />');
															}
															$row.find('td:last').append('<i style="color:blue;">' + contrato.pagos[i].historico[ii].tipo + ' ' + contrato.pagos[i].historico[ii].num +
																'</i> por ' + ciHelper.formatMon(contrato.pagos[i].historico[ii].total, contrato.pagos[i].historico[ii].moneda) +
																' del <i style="color:green;">' + ciHelper.date.format.bd_ymd(contrato.pagos[i].historico[ii].fec) + '</i>');
														}
														if (contrato.pagos[i].estado == 'P') {
															$row.find('td:last').append('<br /><button class="btn btn-xs btn-success"><i class="fa fa-money"></i> Generar Compensaci&oacute;n</button>');
															$row.find('button:last').click(function () {
																var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
																inMovi.newCompe({
																	contrato: contrato,
																	pago: $(this).data('pago')
																});
															}).data('pago', contrato.pagos[i]);
														}
													}
												}
												$row.data('data', contrato.pagos[i]);
												p.$w.find('[name=gridPag] tbody').append($row);
											}
										}
										/*
										 * SE AÑADEN LOS COBROS ADICIONALES
										 */
										if (contrato.cobros != null) {
											for (var i = 0; i < contrato.cobros.length; i++) {
												var $row = $('<tr class="item">');
												$row.append('<td>' + contrato.cobros[i].servicio.nomb + '</td>');
												$row.append('<td>' + ciHelper.formatMon(contrato.cobros[i].total) + '</td>');
												if (contrato.cobros[i].estado == 'P') {
													$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-money"></i> Cobrar</button><br />' +
														'<button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');
													$row.data('data', contrato.cobros[i]);
													$row.find('button:first').click(function () {
														var $row = $(this).closest('.item');
														inMovi.newCobro({
															cobro: $row.data('data')
														});
													});
													$row.find('button:last').click(function () {
														var $row = $(this).closest('.item');
														ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Cobro de <b>' + $row.find('td:eq(0)').html() + '</b>&#63;',
															function () {
																K.sendingInfo();
																$.post('in/comp/delete_cobro', {
																	_id: $row.data('data')._id.$id
																}, function () {
																	K.clearNoti();
																	K.msg({
																		title: 'Cobro Eliminado',
																		text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
																	});
																	$row.remove();
																});
															},
															function () {
																$.noop();
															}, 'Eliminaci&oacute;n de Cobro de Servicios');
													});
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												} else {
													$row.append('<td>');
													for (var j = 0; j < contrato.cobros[i].comprobantes.length; j++) {
														if ($row.find('td:last').html() != '') $row.find('td:last').append('<br />');
														var comp = contrato.cobros[i].comprobantes[j];
														//$row.find('td:last').append('<button class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o"></i> '+comp.tipo+' '+comp.serie+'-'+comp.num+'</button>'+
														//	'<button class="btn btn-xs btn-danger"><i class="fa fa-close"></i> Anular '+comp.tipo+' '+comp.serie+'-'+comp.num+'</button></td>');
														var firstvalue = (comp.serie).substring(0, 1);
														console.log(firstvalue);
														if (!(/^([0-9])*$/.test(firstvalue))) {
															comp.num = comp.numero;
														}
														$row.find('td:last').append('<button class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o"></i> ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button>' +
															'<button class="btn btn-xs btn-danger"><i class="fa fa-close"></i> Anular ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button></td>');
														$row.find('button:first').data('id', comp._id.$id).data('serie', comp.serie)
															.click(function () {
																/****************************************************************************************************
																 * IMPRESION DE COMPROBANTE ELECTRONICA Y MANUAL EN COBROS ADICIONALES
																 *****************************************************************************************************/
																var firstvalue = $(this).data('serie').substring(0, 1);
																if (/^([0-9])*$/.test(firstvalue)) {
																	/********************************************************************************************************
																	 * IMPRESION DE COPROBANTE
																	 ********************************************************************************************************/
																	console.log("COMPROBANTE DE PAGO DE COBROS ADICIONALES MANUAL");
																	K.windowPrint({
																		id: 'windowPrint',
																		title: "Comprobante de Pago",
																		url: "in/comp/print?_id=" + $(this).data('id')
																	});
																} else {
																	console.log("COMPROBANTE DE COBROS ADICIONALES ELECTRONICO");
																	K.windowPrint({
																		id: 'windowPrint',
																		title: "Comprobante de Pago Electronico",
																		url: "cj/ecom/print_preview?_id=" + $(this).data('id')
																	});
																}
															});
														$row.find('button:last').data('id', comp._id.$id).data('nomb', comp.tipo + ' ' + comp.serie + '-' + comp.num).data('inmueble', contrato.inmueble).data('serie', comp.serie)
															.click(function () {
																var tmp = $(this);
																var firstvalue = $(this).data('serie').substring(0, 1);
																if (!(/^([0-9])*$/.test(firstvalue))) {
																	K.notification({
																		title: 'Mensaje del Sistema!',
																		type: 'error',
																		text: 'Los comprobantes electrónicos solo se pueden pedir de baja desde la interfaz de comprobantes electrónicos'
																	});
																} else {
																	ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>' + tmp.data('nomb') + '</b>&#63;',
																		function () {
																			K.sendingInfo();
																			$.post('in/comp/anular', {
																				_id: tmp.data('id')
																			}, function () {
																				K.clearNoti();
																				K.msg({
																					title: 'Comprobante Anulado',
																					text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'
																				});
																				inMovi.init({
																					inmueble: inInmu.dbRel(tmp.data('inmueble'))
																				});
																			});
																		},
																		function () {
																			$.noop();
																		}, 'Anulaci&oacute;n de Comprobante');
																}

															});
													}
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												}
											}
										}
									});
									$row.data('data', p.contratos[i]);
									p.$w.find('[name=gridCont] tbody').append($row);
									if (p.titulares.indexOf(p.contratos[i].titular) == -1) {
										p.titulares.push(p.contratos[i].titular);
									}
									if (p.$w.find('[name=' + p.contratos[i].titular._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + p.contratos[i].titular._id.$id + '">');
										$row.append('<td>' + mgEnti.formatName(p.contratos[i].titular) + '</td>');
										$row.append('<td>' + mgEnti.formatDNI(p.contratos[i].titular) + '</td>');
										$row.append('<td>' + mgEnti.formatRUC(p.contratos[i].titular) + '</td>');
										$row.append('<td><button class="btn btn-info btn-xs"><i class="fa fa-search"></i> Garant&iacute;as</button>&nbsp;' +
											'<button class="btn btn-success btn-xs"><i class="fa fa-search"></i> Liquidaciones</button></td>');
										$row.data('data', p.contratos[i].titular);
										$row.click(function () {
											var titular = $(this).closest('.item').data('data');
											p.$w.find('[name=gridCont] tbody tr[data-titu=' + titular._id.$id + ']').show();
											p.$w.find('[name=gridCont] tbody tr[data-titu!=' + titular._id.$id + ']').hide();
											p.$w.find('[name=gridCont] .item:visible:eq(0) td:first').click();
										});
										$row.find('button:first').click(function () {
											inMovi.windowGarantia({
												titular: $(this).closest('.item').data('data'),
												inmueble: $('#mainPanel [name=inmueble] option:selected').data('data')
											});
										});
										$row.find('button:last').click(function () {
											var params = {
												titular: $(this).closest('.item').data('data')._id.$id,
												inmueble: $('#mainPanel [name=inmueble] option:selected').data('data')._id.$id
											};
											K.windowPrint({
												id: 'windowPrint',
												title: "Record de Pago",
												url: 'in/repo/liquidaciones?' + $.param(params)
											});
										});
										p.$w.find('[name=gridArre] tbody').append($row);
									}
								}
							}
							if (p.actas != null) {
								for (var i = 0, j = p.actas.length; i < j; i++) {
									var $row = $('<tr class="item" data-titu="' + p.actas[i].arrendatario._id.$id + '">');
									$row.append('<td><kbd>' + p.actas[i].num + '</kbd></td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.actas[i].items[0].fecven) + '</td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.actas[i].items[p.actas[i].items.length - 1].fecven) + '</td>');
									$row.append('<td>--</td>');
									$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Editar</button></td>');
									$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');
									$row.find('button:first').click(function () {
										var acta = $(this).closest('.item').data('data');
										inActa.windowEdit({
											id: acta._id.$id
										});
									});
									$row.find('button:last').click(function () {
										var $row = $(this).closest('.item'),
											acta = $row.data('data');
										ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Acta de <b>' + acta.num + '</b>&#63;',
											function () {
												K.sendingInfo();
												$.post('in/acta/delete', {
													_id: acta._id.$id
												}, function () {
													K.clearNoti();
													$row.remove();
													K.msg({
														title: 'Acta de Conciliacion Eliminado',
														text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
													});
													p.$w.find('[name=gridArre] .item:eq(0)').click();
												});
											},
											function () {
												$.noop();
											}, 'Eliminaci&oacute;n de Acta de Conciliacion');
									});
									$row.find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').click(function () {
										p.$w.find('[name=gridPag] tbody .item').remove();
										p.$w.find('[name=gridCta] tbody').empty();
										var acta = $(this).closest('.item').data('data');
										if (acta.moneda == null) acta.moneda = 'S';
										if (acta.items != null) {
											for (var i = 0; i < acta.items.length; i++) {
												var $row = $('<tr class="item">');
												$row.append('<td>' + acta.items[i].num + '</td>');
												$row.append('<td>' + ciHelper.date.format.bd_ymd(acta.items[i].fecven) + '</td>');
												$row.append('<td>' + ciHelper.formatMon(acta.items[i].total, acta.moneda) + '</td>');
												if (acta.items[i].estado == null) {
													$row.append('<td>' + inMovi.estado_pago['P'].label + '</td>');
													$row.append('<td>');
												} else {
													$row.append('<td>' + inMovi.estado_pago[acta.items[i].estado].label + '</td>');
													if (acta.items[i].comprobante != null) {
														$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> ' + acta.items[i].comprobante.tipo + ' ' + acta.items[i].comprobante.serie + '-' + acta.items[i].comprobante.num + '</button></td>');
														$row.data('id', acta.items[i].comprobante._id.$id).data('tipo', acta.items[i].comprobante.tipo);
														$row.find('button').click(function () {
															var $row = $(this).closest('.item');
															/********************************************************************************************************
															 * IMPRESION DE BOLETA
															 ********************************************************************************************************/
															K.windowPrint({
																id: 'windowPrint',
																title: "Comprobante de Pago",
																url: "in/comp/print?_id=" + $row.data('id')
															});
														});
													} else if (acta.items[i].historico != null) {
														$row.append('<td>');
														for (var ii = 0; ii < acta.items[i].historico.length; ii++) {
															if ($row.find('td:last').html() != '') {
																$row.find('td:last').append('<br />');
															}
															$row.find('td:last').append('<i style="color:blue;">' + acta.items[i].historico[ii].tipo + ' ' + acta.items[i].historico[ii].num +
																'</i> por ' + ciHelper.formatMon(acta.items[i].historico[ii].total, acta.items[i].historico[ii].moneda) +
																' del <i style="color:green;">' + ciHelper.date.format.bd_ymd(acta.items[i].historico[ii].fec) + '</i>');
														}
													}
												}
												$row.data('data', acta.items[i]);
												p.$w.find('[name=gridPag] tbody').append($row);
												var $obsrv = $('[name="obsrv"]');
												$obsrv.empty();
												if(acta.observacion != null){
													$obsrv.append('<td><kbd>' + acta.observacion + '</kbd></td>');
												}
											}
										}
										/*
										 * SE AÑADEN LOS COBROS ADICIONALES
										 */
										if (acta.cobros != null) {
											for (var i = 0; i < contrato.cobros.length; i++) {
												var $row = $('<tr class="item">');
												$row.append('<td>' + contrato.cobros[i].servicio.nomb + '</td>');
												$row.append('<td>' + ciHelper.formatMon(contrato.cobros[i].total) + '</td>');
												if (contrato.cobros[i].estado == 'P') {
													$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-money"></i> Cobrar</button><br />' +
														'<button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');
													$row.data('data', contrato.cobros[i]);
													$row.find('button:first').click(function () {
														var $row = $(this).closest('.item');
														inMovi.newCobro({
															cobro: $row.data('data')
														});
													});
													$row.find('button:last').click(function () {
														var $row = $(this).closest('.item');
														ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Cobro de <b>' + $row.find('td:eq(0)').html() + '</b>&#63;',
															function () {
																K.sendingInfo();
																$.post('in/comp/delete_cobro', {
																	_id: $row.data('data')._id.$id
																}, function () {
																	K.clearNoti();
																	K.msg({
																		title: 'Cobro Eliminado',
																		text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
																	});
																	$row.remove();
																});
															},
															function () {
																$.noop();
															}, 'Eliminaci&oacute;n de Cobro de Servicios');
													});
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												} else {
													$row.append('<td>');
													for (var j = 0; j < contrato.cobros[i].comprobantes.length; j++) {
														if ($row.find('td:last').html() != '') $row.find('td:last').append('<br />');
														var comp = contrato.cobros[i].comprobantes[j];
														$row.find('td:last').append('<button class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o"></i> ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button>' +
															'<button class="btn btn-xs btn-danger"><i class="fa fa-close"></i> Anular ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button></td>');
														$row.find('button:first').data('id', comp._id.$id)
															.click(function () {
																K.windowPrint({
																	id: 'windowPrint',
																	title: "Comprobante de Pago",
																	url: "in/comp/print?_id=" + $(this).data('id')
																});
															});
														$row.find('button:last').data('id', comp._id.$id).data('nomb', comp.tipo + ' ' + comp.serie + '-' + comp.num).data('inmueble', contrato.inmueble)
															.click(function () {
																var tmp = $(this);
																ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>' + tmp.data('nomb') + '</b>&#63;',
																	function () {
																		K.sendingInfo();
																		$.post('in/comp/anular', {
																			_id: tmp.data('id')
																		}, function () {
																			K.clearNoti();
																			K.msg({
																				title: 'Comprobante Anulado',
																				text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'
																			});
																			inMovi.init({
																				inmueble: inInmu.dbRel(tmp.data('inmueble'))
																			});
																		});
																	},
																	function () {
																		$.noop();
																	}, 'Anulaci&oacute;n de Comprobante');
															});
													}
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												}
											}
										}
									});
									$row.data('data', p.actas[i]);
									p.$w.find('[name=gridCont] tbody').append($row);
									if (p.titulares.indexOf(p.actas[i].arrendatario) == -1) {
										p.titulares.push(p.actas[i].arrendatario);
									}
									if (p.$w.find('[name=' + p.actas[i].arrendatario._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + p.actas[i].arrendatario._id.$id + '">');
										$row.append('<td>' + mgEnti.formatName(p.actas[i].arrendatario) + '</td>');
										$row.append('<td>' + mgEnti.formatDNI(p.actas[i].arrendatario) + '</td>');
										$row.append('<td>' + mgEnti.formatRUC(p.actas[i].arrendatario) + '</td>');
										$row.data('data', p.actas[i].arrendatario);
										$row.click(function () {
											var titular = $(this).closest('.item').data('data');
											p.$w.find('[name=gridCont] tbody tr[data-titu=' + titular._id.$id + ']').show();
											p.$w.find('[name=gridCont] tbody tr[data-titu!=' + titular._id.$id + ']').hide();
											p.$w.find('[name=gridCont] .item:visible:eq(0) td:first').click();
										});
										p.$w.find('[name=gridArre] tbody').append($row);
									}
								}
							}
							p.$w.find('[name=gridArre] .item:eq(0)').click();
							K.unblock();
						}, 'json');
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridArre]'),
					search: false,
					pagination: false,
					cols: ['Raz&oacute;n Social / Nombres', 'DNI', 'RUC', ''],
					onlyHtml: true,
					toolbarHTML: '<h3>ARRENDATARIOS</h3>'
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Tipo de Contrato', 'Cnt. Inicial', 'Cnt. Final', 'Fec. Desoc.', 'Importe', '', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Nuevo Contrato</button>&nbsp;' +
						'<select class="form-control" name="mostrar">' +
						'<option value="0">Mostrar s&oacute;lo Contratos Activos</option>' +
						'<option value="1">Mostrar todos los Contratos</option>' +
						'</select>',
					onContentLoaded: function ($el) {
						$el.find('button').click(function () {
							var inmueble = p.$w.find('[name=inmueble] option:selected').data('data');
							if (inmueble == null)
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe escoger un inmueble!',
									type: 'error'
								});
							inMovi.newContrato({
								inmueble: inmueble
							});
						});
						$el.find('[name=mostrar]').change(function () {
							p.$w.find('[name=inmueble]').change();
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['', 'Periodo', 'Importe sin IGV o Moras', 'Estado', 'Comprobante'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-money"></i> Registrar Cobro de Cuota</button>&nbsp;' +
						'<button class="btn btn-info"><i class="fa fa-puzzle-piece"></i> Adelantar Pago Parcial</button>',
					onContentLoaded: function ($el) {
						/****************************************************************************************************************
						 * PAGO DE MENSUALIDAD
						 ****************************************************************************************************************/
						$el.find('button:first').click(function () {
							var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
							if (contrato == null)
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe escoger un contrato!',
									type: 'error'
								});
							if (contrato.contrato_dias == '1') {
								inMovi.newMes({
									contrato: contrato,
									dias: true,
									pagos: [contrato.pagos[0]]
								});
								return true;
							}
							/*
							 * EN CASO SEA CONTRATO NORMAL
							 */
							if (contrato.pagos != null) {
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar Meses a Pagar',
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un mes!',
														type: 'error'
													});
												} else {
													for (var i = 0, j = p.$tmp.find('[name=mes]:checked').length; i < j; i++) {
														tmp.push(p.$tmp.find('[name=mes]:checked').eq(i).closest('.item').data('data'));
													}
												}
												inMovi.newMes({
													contrato: contrato,
													pagos: tmp
												});
												K.closeWindow(p.$tmp.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Periodo'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										var dia_ini_tmp = ciHelper.date.format.bd_d(contrato.fecini);
										for (var i = 0, j = contrato.pagos.length; i < j; i++) {
											var tmp;
											if (contrato.pagos[i].estado != null) {
												tmp = false;
												if (contrato.pagos[i].estado == 'P') {
													if (contrato.pagos[i].comprobantes != null) {
														tmp = false;
													} else
														tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="checkbox" name="mes" /></td>');




												if (parseInt(dia_ini_tmp) == 1) {
													$row.append('<td>' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano + '</td>');
												} else {
													var mes_n = parseInt(contrato.pagos[i].mes) - 1,
														ano_n = parseInt(contrato.pagos[i].ano);
													mes_n--;
													if (mes_n == -1) {
														mes_n = 11;
														ano_n--;
													}
													var tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[mes_n] + '-' + ano_n,
														tmp_fin = 'Al ' + (parseInt(dia_ini_tmp) - 1) + ' de ' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano;
													$row.append('<td>' + tmp_ini + '<br />' + tmp_fin + '</td>');
												}



												//$row.append('<td>'+ciHelper.meses[parseInt(contrato.pagos[i].mes)-1]+'-'+contrato.pagos[i].ano+'</td>');
												$row.data('data', contrato.pagos[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:checkbox:first').attr('checked', 'checked');
										p.$tmp.find('[name=mes]').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
								/*
								 * EN CASO SEA UN ACTA DE CONCILIACION
								 */
							} else if (contrato.items != null) {
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar Cuotas a Pagar',
									width: 650,
									height: 550,
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un mes!',
														type: 'error'
													});
												} else {
													for (var i = 0, j = p.$tmp.find('[name=mes]:checked').length; i < j; i++) {
														tmp.push(p.$tmp.find('[name=mes]:checked').eq(i).closest('.item').data('data'));
													}
												}
												inMovi.newActa({
													contrato: contrato,
													pagos: tmp
												});
												K.closeWindow(p.$tmp.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Vencimiento', 'Total'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										for (var i = 0, j = contrato.items.length; i < j; i++) {
											var tmp;
											if (contrato.items[i].estado != null) {
												tmp = false;
												if (contrato.items[i].estado == 'P') {
													if (contrato.items[i].comprobantes != null) {
														tmp = false;
													} else
														tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="checkbox" name="mes" /></td>');
												$row.append('<td>' + ciHelper.date.format.bd_ymd(contrato.items[i].fecven) + '</td>');
												$row.append('<td>' + ciHelper.formatMon(contrato.items[i].total) + '</td>');
												$row.data('data', contrato.items[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:checkbox:first').attr('checked', 'checked');
										p.$tmp.find('[name=mes]').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
							} else {
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El contrato no tiene cobros programados',
									type: 'error'
								});
							}
						});
						/****************************************************************************************************************
						 * PAGAR PARCIAL
						 ****************************************************************************************************************/
						$el.find('button:last').click(function () {
							var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
							if (contrato == null)
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe escoger un contrato!',
									type: 'error'
								});
							if (contrato.contrato_dias == '1') {
								inMovi.newMes({
									contrato: contrato,
									dias: true,
									pagos: [contrato.pagos[0]]
								});
								return true;
							}
							/*C.I PENALIDADES NO SE DEBEN AGREGAR PAGOS PARCIALES
							IGV (PENALIDADES NO SE DEBEN AGREGAR PAGOS PARCIALES por C.I. Nº784-2017-SBPA-OGA )*/
							if (contrato.motivo._id.$id == '55316553bc795ba801000035') {
								return K.msg({
									title: 'INFORMACION',
									text: 'No se puede agregar pagos parciales por penalidades!',
									type: 'error'
								});
							}
							if (contrato.pagos != null) {
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar Meses a Pagar',
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un mes!',
														type: 'error'
													});
												}
												inMovi.newParcial({
													contrato: contrato,
													pago: p.$tmp.find('[name=mes]:checked').closest('.item').data('data')
												});
												K.closeWindow(p.$tmp.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Periodo'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										for (var i = 0, j = contrato.pagos.length; i < j; i++) {
											var tmp;
											if (contrato.pagos[i].estado != null) {
												tmp = false;
												if (contrato.pagos[i].estado == 'P') {
													tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="radio" name="mes" /></td>');
												$row.append('<td>' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano + '</td>');
												$row.data('data', contrato.pagos[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:radio:first').attr('checked', 'checked');
										p.$tmp.find('input:radio').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
							} else if (contrato.items != null) {
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'No se pueden hacer cobros parciales de Actas de Conciliaci&oacute;n',
									type: 'error'
								});
							} else {
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El contrato no tiene cobros programados',
									type: 'error'
								});
							}
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridCta]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n', 'Importe', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-cart-arrow-down"></i> Agregar Cobro Adicional (Luz, Agua, Derechos, etc.)</button>',
					onContentLoaded: function ($el) {
						$el.find('button').click(function () {
							var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
							inMovi.newCuenta({
								contrato: contrato
							});
						});
					}
				});
				$.post('in/movi/get_tipo_sub', function (data) {
					p.$w.find('[name=gridArre] .fuelux:first,[name=gridCont] .fuelux:first').css('max-height', '420px');
					p.tipo = data.tipo;
					p.sublocal = data.sublocal;
					var $cbo = p.$w.find('[name=tipo]');
					for (var i = 0, j = p.tipo.length; i < j; i++) {
						$cbo.append('<option value="' + i + '">' + p.tipo[i].nomb + '</option>');
					}
					var inmu_tmp = $.jStorage.get('in/movi/get_all_cont');
					if (p.inmueble == null) {
						if (inmu_tmp != null) p.inmueble = inmu_tmp;
					}
					if (p.inmueble == null) {
						p.$w.find('[name=tipo]').chosen();
						$cbo.change();
					} else {
						for (var i = 0, j = p.tipo.length; i < j; i++) {
							if (p.tipo[i]._id.$id == p.inmueble.tipo._id) {
								p.$w.find('[name=tipo] option').eq(i).attr('selected', 'selected');
								break;
							}
						}
						p.$w.find('[name=tipo]').chosen();
						var val = p.inmueble.tipo._id,
							$cbo = p.$w.find('[name=sublocal]').empty();
						if (p.sublocal != null) {
							for (var i = 0, j = p.sublocal.length; i < j; i++) {
								if (p.sublocal[i].tipo._id.$id == val) {
									$cbo.append('<option value="' + i + '">' + p.sublocal[i].nomb + '</option>');
									if (p.sublocal[i]._id.$id == p.inmueble.sublocal._id)
										$cbo.find('option:last').attr('selected', 'selected');
								}
							}
						}
						p.$w.find('[name=sublocal]').chosen();
						p.$w.find('[name=inmueble]').empty();
						$.post('in/inmu/get_all_sub', {
							_id: p.inmueble.sublocal._id
						}, function (data) {
							var $cbo = p.$w.find('[name=inmueble]').empty();
							if (data != null) {
								for (var i = 0, j = data.length; i < j; i++) {
									$cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].direccion + '</option>');
									$cbo.find('option:last').data('data', data[i]);
								}
							}
							$cbo.selectVal(p.inmueble._id);
							$cbo.chosen().change();
						}, 'json');
					}
				}, 'json');
			}
		});
	},




	/**************************************************************************************************************************
	 *
	 * MOVIMIENTOS MODAL
	 *
	 **************************************************************************************************************************/
	windowMovi: function (p) {
		if (p == null) p = {};
		new K.Modal({
			id: 'windowMovi',
			title: 'Seleccionar Alquileres',
			contentURL: 'in/movi',
			icon: 'ui-icon-pencil',
			width: $(window).width() - 60,
			height: 700,
			buttons: {
				"Cancelar": function () {
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function () {
				p.$w = $('#windowMovi');
				//p.$w.find('[name=tipo],[name=sublocal],[name=inmueble]').chosen();
				p.$w.find('[name=tipo]').change(function () {
					var $this = $(this),
						val = p.tipo[$this.find('option:selected').val()]._id.$id,
						$cbo = p.$w.find('[name=sublocal]').empty();
					if (p.sublocal != null) {
						for (var i = 0, j = p.sublocal.length; i < j; i++) {
							if (p.sublocal[i].tipo._id.$id == val)
								$cbo.append('<option value="' + i + '">' + p.sublocal[i].nomb + '</option>');
						}
					}
					$cbo.chosen().trigger("chosen:updated");
					$cbo.change();
				});
				p.$w.find('[name=sublocal]').change(function () {
					p.$w.find('[name=inmueble]').empty();
					if ($(this).find('option').length == 0) {
						p.$w.find('[name=inmueble]').change();
						return K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe escoger un local v&aacute;lido!',
							type: 'error'
						});
					}
					K.block();
					$.post('in/inmu/get_all_sub', {
						_id: p.sublocal[$(this).find('option:selected').val()]._id.$id
					}, function (data) {
						var $cbo = p.$w.find('[name=inmueble]').empty();
						if (data != null) {
							for (var i = 0, j = data.length; i < j; i++) {
								$cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].abrev + '</option>');
								$cbo.find('option:last').data('data', data[i]);
							}
						}
						$cbo.chosen().trigger("chosen:updated");
						$cbo.change();
						K.unblock();
					}, 'json');
				});
				p.$w.find('[name=btnRefresh]').click(function (e) {
					e.preventDefault();
					p.$w.find('[name=inmueble]').change();
				});
				p.$w.find('[name=inmueble]').change(function () {
					var inmueble = $(this).find('option:selected').data('data');
					if (inmueble != null) {
						$.jStorage.set('in/movi/get_all_cont', inInmu.dbRel(inmueble));
						K.block();
						$.post('in/movi/get_all_cont', {
							_id: inmueble._id.$id,
							mostrar: p.$w.find('[name=mostrar] option:selected').val()
						}, function (data) {
							p.contratos = data.contratos;
							p.actas = data.actas;
							p.titulares = [];
							p.$w.find('[name=gridCont] tbody').empty();
							p.$w.find('[name=gridArre] tbody').empty();
							p.$w.find('[name=gridPag] tbody').empty();
							p.$w.find('[name=gridCta] tbody').empty();
							if (p.contratos != null) {
								for (var i = 0, j = p.contratos.length; i < j; i++) {
									var $row = $('<tr class="item" data-titu="' + p.contratos[i].titular._id.$id + '">');
									$row.append('<td>' + p.contratos[i].motivo.nomb +
										'<br /><button class="btn btn-warning btn-xs"><i class="fa fa-money"></i> Record de Pagos</button>' +
										'</td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.contratos[i].fecini) + '</td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.contratos[i].fecfin) + '</td>');
									if (p.contratos[i].fecdes.sec != 0 && p.contratos[i].fecdes.sec != -68400)
										$row.append('<td style="color:red">' + ciHelper.date.format.bd_ymd(p.contratos[i].fecdes) + '</td>');
									else
										$row.append('<td>');
									$row.append('<td>' + ciHelper.formatMon(p.contratos[i].importe, p.contratos[i].moneda) + '</td>');
									$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Editar</button></td>');
									$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');

									$row.find('button:eq(0)').click(function () {
										var params = {
											contrato: $(this).closest('.item').data('data')._id.$id
										};
										K.windowPrint({
											id: 'windowPrint',
											title: "Record de Pago",
											url: 'in/repo/record_pago?' + $.param(params)
										});
									});
									$row.find('button:eq(1)').click(function () {
										var contrato = $(this).closest('.item').data('data');
										inMovi.editContrato({
											id: contrato._id.$id
										});
									});
									$row.find('button:eq(2)').click(function () {
										var $row = $(this).closest('.item'),
											contrato = $row.data('data');
										ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Contrato <b>' + $row.find('td:eq(0)').html() + '</b>&#63;',
											function () {
												K.sendingInfo();
												$.post('in/movi/delete_contrato', {
													_id: contrato._id.$id
												}, function () {
													K.clearNoti();
													K.msg({
														title: 'Contrato Eliminado',
														text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
													});
													$row.remove();
												});
											},
											function () {
												$.noop();
											}, 'Eliminaci&oacute;n de Contrato de Alquiler');
									});



									$row.find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').click(function () {
										p.$w.find('[name=gridPag] tbody .item').remove();
										p.$w.find('[name=gridCta] tbody').empty();
										var contrato = $(this).closest('.item').data('data'),
												dia_ini_tmp = ciHelper.date.format.bd_d(contrato.fecini),
												dia_fin_tmp = ciHelper.date.format.bd_d(contrato.fecfin),
												tmp_desoc_d = parseInt(ciHelper.date.format.bd_d(contrato.fecdes)),
												tmp_desoc_m = ciHelper.date.format.bd_m(contrato.fecdes),
												tmp_ini_m = ciHelper.date.format.bd_m(contrato.fecini),
												tmp_fin_m = ciHelper.date.format.bd_m(contrato.fecfin),
												tmp_desoc_y = ciHelper.date.format.bd_y(contrato.fecdes),
												tmp_fin_y = ciHelper.date.format.bd_y(contrato.fecfin);
										
										if (contrato.pagos != null) {
											for (var i = 0; i < contrato.pagos.length; i++) {
												var tmp_impor = null;
												if (parseInt(dia_ini_tmp) == 1) {
													if (parseInt(tmp_desoc_m) == parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														tmp_impor = parseFloat(contrato.importe) / 30 * tmp_desoc_d;
														//asdasd
													} else if (parseInt(tmp_desoc_m) < parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) <= parseInt(contrato.pagos[i].ano)) {
														//
														break;
													}
												} else if(parseInt(dia_ini_tmp) == 15 && contrato._id.$id == '62475bdf3e6037b04f8b4567'){
													if (parseInt(tmp_desoc_m) == parseInt(contrato.pagos[i].mes-1) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														tmp_impor = parseFloat(contrato.importe) / 30 * tmp_desoc_d;
													} else if (parseInt(tmp_desoc_m) <= parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														tmp_impor = parseFloat(contrato.importe) / 30 * 20;
													}
												}else{
													if (parseInt(tmp_desoc_m) == (parseInt(contrato.pagos[i].mes) - 1) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														console.info(contrato.pagos[i]);
														console.log('tmp_impor');
														//asdasd
													} else if (parseInt(tmp_desoc_m) < parseInt(contrato.pagos[i].mes) && parseInt(tmp_desoc_y) == parseInt(contrato.pagos[i].ano)) {
														
														console.log('entro');
													} 
												}
												var $row = $('<tr class="item">');
												$row.append('<td>' + (parseInt(contrato.pagos[i].item)) + '</td>');
												if (parseInt(dia_ini_tmp) == 1) {
													$row.append('<td>' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano + '</td>');
												} else {
													var mes_n = parseInt(contrato.pagos[i].mes) - 1,
														ano_n = parseInt(contrato.pagos[i].ano);
													mes_n--;
													if (mes_n == -1) {
														mes_n = 11;
														ano_n--;
													}
													var tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[mes_n] + '-' + ano_n,
														tmp_fin = 'Al ' + (parseInt(dia_ini_tmp) - 1) + ' de ' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano;
													if (tmp_impor != null) {
														tmp_fin = 'Al ' + tmp_desoc_d + ' de ' + ciHelper.meses[parseInt(tmp_desoc_m) - 1] + '-' + tmp_desoc_y;
													}
													if(contrato.contrato_dias==1)
													{	var mes_n_temp = parseInt(contrato.pagos[i].mes) - 1;
														mes_n_temp--;
														if(mes_n_temp==-1)
														{
															ano_n++;
														}
														tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[tmp_ini_m-1] + '-' + ano_n,
														tmp_fin= 'Al ' + dia_fin_tmp + ' de ' + ciHelper.meses[parseInt(tmp_fin_m-1)] + '-' + tmp_fin_y;
													}
													$row.append('<td>' + tmp_ini + '<br />' + tmp_fin + '</td>');
												}
												if (tmp_impor != null) {
													$row.append('<td>' + ciHelper.formatMon(tmp_impor, contrato.moneda) + '</td>');
													$row.data('parcial', tmp_impor);
												} else
													$row.append('<td>' + ciHelper.formatMon(contrato.importe, contrato.moneda) + '</td>');
												if (contrato.pagos[i].estado == 'C') {
													if (contrato.pagos[i].detalle != null) {
														if (parseFloat(contrato.pagos[i].detalle.alquiler) != parseFloat(contrato.importe)) {
															contrato.pagos[i].estado = 'P';
															/*$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">'+ciHelper.formatMon(contrato.pagos[i].total,contrato.moneda)+'</label></span>'+
																'<br /><span>Falta: <label style="color: blue;">'+ciHelper.formatMon(parseFloat(contrato.importe)-parseFloat(contrato.pagos[i].total),contrato.moneda)+'</label></span>');*/
														}
													}
													if (contrato.pagos[i].historico != null) {
														var tmp_hist_tot = 0;
														for (var i_h = 0; i_h < contrato.pagos[i].historico.length; i_h++) {
															tmp_hist_tot += parseFloat(contrato.pagos[i].historico[i_h].total);
														}
														if (contrato.pagos[i].comprobantes != null) {
															for (var i_h = 0; i_h < contrato.pagos[i].comprobantes.length; i_h++) {
																tmp_hist_tot += parseFloat(contrato.pagos[i].comprobantes[i_h].detalle.alquiler);
															}
														}
														if (K.round(tmp_hist_tot, 2) != parseFloat(contrato.importe)) {
															contrato.pagos[i].estado = 'P';
															contrato.pagos[i].total = tmp_hist_tot;
														}
													}
												}
												if (contrato.pagos[i].estado == null) {
													$row.append('<td>' + inMovi.estado_pago['P'].label + '</td>');
													$row.append('<td>');
												} else {
													$row.append('<td>' + inMovi.estado_pago[contrato.pagos[i].estado].label + '</td>');
													if (contrato.pagos[i].comprobante != null) {
														if (contrato.pagos[i].estado == 'P') {
															if (contrato.pagos[i].detalle != null) {
																/*contrato.pagos[i].detalle = {
																	alquiler: 0
																	//
																};
																if(contrato.pagos[i].historico!=null){
																	for(var i_ho=0; i_ho<contrato.pagos[i].historico.length; i_ho++){
																		contrato.pagos[i].detalle.alquiler += parseFloat(contrato.pagos[i].historico[i_ho].total);
																	}
																}*/
															}
															$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">' + ciHelper.formatMon(contrato.pagos[i].detalle.alquiler, contrato.moneda) + '</label></span>' +
																'<br /><span>Falta: <label style="color: blue;">' + ciHelper.formatMon(parseFloat(contrato.importe) - parseFloat(contrato.pagos[i].detalle.alquiler), contrato.moneda) + '</label></span>');
														}
														$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> ' + contrato.pagos[i].comprobante.tipo + ' ' + contrato.pagos[i].comprobante.serie + '-' + contrato.pagos[i].comprobante.num + '</button></td>');
														$row.data('id', contrato.pagos[i].comprobante._id.$id).data('tipo', contrato.pagos[i].comprobante.tipo);
														$row.find('button').click(function () {
															var $row = $(this).closest('.item');
															/********************************************************************************************************
															 * IMPRESION DE BOLETA
															 ********************************************************************************************************/
															K.windowPrint({
																id: 'windowPrint',
																title: "Comprobante de Pago",
																url: "in/comp/print?_id=" + $row.data('id')
															});
														});
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															contrato.pagos[i].total = parseFloat(contrato.pagos[i].detalle.alquiler);
															$row.find('td:last').append('<br /><button class="btn btn-xs btn-success"><i class="fa fa-money"></i> Generar Compensaci&oacute;n</button>');
															$row.find('button:last').click(function () {
																var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
																inMovi.newCompe({
																	contrato: contrato,
																	pago: $(this).data('pago')
																});
															}).data('pago', contrato.pagos[i]);
														}
													} else if (contrato.pagos[i].comprobantes != null) {
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															if (contrato.pagos[i].detalle != null) {
																contrato.pagos[i].detalle = {
																	alquiler: parseFloat(contrato.pagos[i].historico[i_h].total)
																};
															}
															$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">' + ciHelper.formatMon(contrato.pagos[i].total, contrato.moneda) + '</label></span>' +
																'<br /><span>Falta: <label style="color: blue;">' + ciHelper.formatMon(parseFloat(contrato.importe) - parseFloat(contrato.pagos[i].total), contrato.moneda) + '</label></span>');
														}
														$row.append('<td>');
														if (contrato.pagos[i].historico != null) {
															for (var ii = 0; ii < contrato.pagos[i].historico.length; ii++) {
																if ($row.find('td:last').html() != '') {
																	$row.find('td:last').append('<br />');
																}
																$row.find('td:last').append('<i style="color:blue;">' + contrato.pagos[i].historico[ii].tipo + ' ' + contrato.pagos[i].historico[ii].num +
																	'</i> por ' + ciHelper.formatMon(contrato.pagos[i].historico[ii].total, contrato.pagos[i].historico[ii].moneda) +
																	' del <i style="color:green;">' + ciHelper.date.format.bd_ymd(contrato.pagos[i].historico[ii].fec) + '</i>');
															}
														}
														for (var ik = 0; ik < contrato.pagos[i].comprobantes.length; ik++) {
															if (ik != 0)
																$row.find('td:last').append('<br />');
															$row.find('td:last').append('<button class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> ' + contrato.pagos[i].comprobantes[ik].tipo + ' ' + contrato.pagos[i].comprobantes[ik].serie + '-' + contrato.pagos[i].comprobantes[ik].num + '</button>');
															$row.find('button:last').click(function () {
																/********************************************************************************************************
																 * IMPRESION DE BOLETA
																 ********************************************************************************************************/
																K.windowPrint({
																	id: 'windowPrint',
																	title: "Comprobante de Pago",
																	url: "in/comp/print?_id=" + $(this).data('id')
																});
															}).data('id', contrato.pagos[i].comprobantes[ik]._id.$id).data('tipo', contrato.pagos[i].comprobantes[ik].tipo);
															$row.find('td:last').append('<br /><span>Base: ' + contrato.pagos[i].comprobantes[ik].detalle.alquiler + ', IGV: ' + contrato.pagos[i].comprobantes[ik].detalle.igv + '</span><br />');
														}
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															$row.find('td:last').append('<br /><button class="btn btn-xs btn-success"><i class="fa fa-money"></i> Generar Compensaci&oacute;n</button>');
															$row.find('button:last').click(function () {
																var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
																inMovi.newCompe({
																	contrato: contrato,
																	pago: $(this).data('pago')
																});
															}).data('pago', contrato.pagos[i]);
														}
													} else if (contrato.pagos[i].historico != null) {
														if (contrato.pagos[i].estado == 'P' && tmp_impor == null) {
															$row.find('td:last').append('<br /><span>Pagados: <label style="color: green;">' + ciHelper.formatMon(contrato.pagos[i].total, contrato.moneda) + '</label></span>' +
																'<br /><span>Falta: <label style="color: blue;">' + ciHelper.formatMon(parseFloat(contrato.importe) - parseFloat(contrato.pagos[i].total), contrato.moneda) + '</label></span>');
														}
														$row.append('<td>');
														for (var ii = 0; ii < contrato.pagos[i].historico.length; ii++) {
															if ($row.find('td:last').html() != '') {
																$row.find('td:last').append('<br />');
															}
															$row.find('td:last').append('<i style="color:blue;">' + contrato.pagos[i].historico[ii].tipo + ' ' + contrato.pagos[i].historico[ii].num +
																'</i> por ' + ciHelper.formatMon(contrato.pagos[i].historico[ii].total, contrato.pagos[i].historico[ii].moneda) +
																' del <i style="color:green;">' + ciHelper.date.format.bd_ymd(contrato.pagos[i].historico[ii].fec) + '</i>');
														}
														if (contrato.pagos[i].estado == 'P') {
															$row.find('td:last').append('<br /><button class="btn btn-xs btn-success"><i class="fa fa-money"></i> Generar Compensaci&oacute;n</button>');
															$row.find('button:last').click(function () {
																var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
																inMovi.newCompe({
																	contrato: contrato,
																	pago: $(this).data('pago')
																});
															}).data('pago', contrato.pagos[i]);
														}
													}
												}
												$row.data('data', contrato.pagos[i]);
												p.$w.find('[name=gridPag] tbody').append($row);
											}
										}
										/*
										 * SE AÑADEN LOS COBROS ADICIONALES
										 */
										if (contrato.cobros != null) {
											for (var i = 0; i < contrato.cobros.length; i++) {
												var $row = $('<tr class="item">');
												$row.append('<td>' + contrato.cobros[i].servicio.nomb + '</td>');
												$row.append('<td>' + ciHelper.formatMon(contrato.cobros[i].total) + '</td>');
												if (contrato.cobros[i].estado == 'P') {
													$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-money"></i> Cobrar</button><br />' +
														'<button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');
													$row.data('data', contrato.cobros[i]);
													$row.find('button:first').click(function () {
														var $row = $(this).closest('.item');
														/*inMovi.newCobro({
															cobro: $row.data('data')
														});*/
														p.callback({
															tipo: 'cuenta_cobrar',
															contrato: contrato,
															cuenta_cobrar: $row.data('data')
														});
														K.closeWindow(p.$w.attr('id'));
													});
													$row.find('button:last').click(function () {
														var $row = $(this).closest('.item');
														ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Cobro de <b>' + $row.find('td:eq(0)').html() + '</b>&#63;',
															function () {
																K.sendingInfo();
																$.post('in/comp/delete_cobro', {
																	_id: $row.data('data')._id.$id
																}, function () {
																	K.clearNoti();
																	K.msg({
																		title: 'Cobro Eliminado',
																		text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
																	});
																	$row.remove();
																});
															},
															function () {
																$.noop();
															}, 'Eliminaci&oacute;n de Cobro de Servicios');
													});
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												} else {
													$row.append('<td>');
													for (var j = 0; j < contrato.cobros[i].comprobantes.length; j++) {
														console.log(contrato.cobros[i].comprobantes[j]);
														if ($row.find('td:last').html() != '') $row.find('td:last').append('<br />');
														var comp = contrato.cobros[i].comprobantes[j];
														$row.find('td:last').append('<button class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o"></i> ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button>' +
															'<button class="btn btn-xs btn-danger"><i class="fa fa-close"></i> Anular ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button></td>');
														$row.find('button:first').data('id', comp._id.$id)
															.click(function () {
																K.windowPrint({
																	id: 'windowPrint',
																	title: "Comprobante de Pago",
																	url: "in/comp/print?_id=" + $(this).data('id')
																});
															});
														$row.find('button:last').data('id', comp._id.$id).data('nomb', comp.tipo + ' ' + comp.serie + '-' + comp.num).data('inmueble', contrato.inmueble)
															.click(function () {
																var tmp = $(this);
																ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>' + tmp.data('nomb') + '</b>&#63;',
																	function () {
																		K.sendingInfo();
																		$.post('in/comp/anular', {
																			_id: tmp.data('id')
																		}, function () {
																			K.clearNoti();
																			K.msg({
																				title: 'Comprobante Anulado',
																				text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'
																			});
																			p.$w.find('[name=btnRefresh]').click();
																			//inMovi.init({inmueble: inInmu.dbRel(tmp.data('inmueble'))});
																		});
																	},
																	function () {
																		$.noop();
																	}, 'Anulaci&oacute;n de Comprobante');
															});
													}
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												}
											}
										}
									});
									$row.data('data', p.contratos[i]);
									p.$w.find('[name=gridCont] tbody').append($row);
									if (p.titulares.indexOf(p.contratos[i].titular) == -1) {
										p.titulares.push(p.contratos[i].titular);
									}
									if (p.$w.find('[name=' + p.contratos[i].titular._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + p.contratos[i].titular._id.$id + '">');
										$row.append('<td>' + mgEnti.formatName(p.contratos[i].titular) + '</td>');
										$row.append('<td>' + mgEnti.formatDNI(p.contratos[i].titular) + '</td>');
										$row.append('<td>' + mgEnti.formatRUC(p.contratos[i].titular) + '</td>');
										$row.append('<td><button class="btn btn-info btn-xs"><i class="fa fa-search"></i> Garant&iacute;as</button>&nbsp;' +
											'<button class="btn btn-success btn-xs"><i class="fa fa-search"></i> Liquidaciones</button></td>');
										$row.data('data', p.contratos[i].titular);
										$row.click(function () {
											var titular = $(this).closest('.item').data('data');
											p.$w.find('[name=gridCont] tbody tr[data-titu=' + titular._id.$id + ']').show();
											p.$w.find('[name=gridCont] tbody tr[data-titu!=' + titular._id.$id + ']').hide();
											p.$w.find('[name=gridCont] .item:visible:eq(0) td:first').click();
										});
										$row.find('button:first').click(function () {
											inMovi.windowGarantia({
												titular: $(this).closest('.item').data('data'),
												inmueble: $('#mainPanel [name=inmueble] option:selected').data('data')
											});
										});
										$row.find('button:last').click(function () {
											var params = {
												titular: $(this).closest('.item').data('data')._id.$id,
												inmueble: $('#mainPanel [name=inmueble] option:selected').data('data')._id.$id
											};
											K.windowPrint({
												id: 'windowPrint',
												title: "Record de Pago",
												url: 'in/repo/liquidaciones?' + $.param(params)
											});
										});
										p.$w.find('[name=gridArre] tbody').append($row);
									}
								}
							}
							if (p.actas != null) {
								for (var i = 0, j = p.actas.length; i < j; i++) {
									var $row = $('<tr class="item" data-titu="' + p.actas[i].arrendatario._id.$id + '">');
									$row.append('<td><kbd>' + p.actas[i].num + '</kbd></td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.actas[i].items[0].fecven) + '</td>');
									$row.append('<td>' + ciHelper.date.format.bd_ymd(p.actas[i].items[p.actas[i].items.length - 1].fecven) + '</td>');
									$row.append('<td>--</td>');
									$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Editar</button></td>');
									$row.append('<td><button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');
									$row.find('button:first').click(function () {
										var acta = $(this).closest('.item').data('data');
										inActa.windowEdit({
											id: acta._id.$id
										});
									});
									$row.find('button:last').click(function () {
										var $row = $(this).closest('.item'),
											acta = $row.data('data');
										ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Acta de <b>' + acta.num + '</b>&#63;',
											function () {
												K.sendingInfo();
												$.post('in/acta/delete', {
													_id: acta._id.$id
												}, function () {
													K.clearNoti();
													$row.remove();
													K.msg({
														title: 'Acta de Conciliacion Eliminado',
														text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
													});
													p.$w.find('[name=gridArre] .item:eq(0)').click();
												});
											},
											function () {
												$.noop();
											}, 'Eliminaci&oacute;n de Acta de Conciliacion');
									});
									$row.find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').click(function () {
										p.$w.find('[name=gridPag] tbody .item').remove();
										p.$w.find('[name=gridCta] tbody').empty();
										var acta = $(this).closest('.item').data('data');
										if (acta.moneda == null) acta.moneda = 'S';
										if (acta.items != null) {
											for (var i = 0; i < acta.items.length; i++) {
												var $row = $('<tr class="item">');
												$row.append('<td>' + acta.items[i].num + '</td>');
												$row.append('<td>' + ciHelper.date.format.bd_ymd(acta.items[i].fecven) + '</td>');
												$row.append('<td>' + ciHelper.formatMon(acta.items[i].total, acta.moneda) + '</td>');
												if (acta.items[i].estado == null) {
													$row.append('<td>' + inMovi.estado_pago['P'].label + '</td>');
													$row.append('<td>');
												} else {
													$row.append('<td>' + inMovi.estado_pago[acta.items[i].estado].label + '</td>');
													if (acta.items[i].comprobante != null) {
														$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i> ' + acta.items[i].comprobante.tipo + ' ' + acta.items[i].comprobante.serie + '-' + acta.items[i].comprobante.num + '</button></td>');
														$row.data('id', acta.items[i].comprobante._id.$id).data('tipo', acta.items[i].comprobante.tipo);
														$row.find('button').click(function () {
															var $row = $(this).closest('.item');
															/********************************************************************************************************
															 * IMPRESION DE BOLETA
															 ********************************************************************************************************/
															K.windowPrint({
																id: 'windowPrint',
																title: "Comprobante de Pago",
																url: "in/comp/print?_id=" + $row.data('id')
															});
														});
													} else if (acta.items[i].historico != null) {
														$row.append('<td>');
														for (var ii = 0; ii < acta.items[i].historico.length; ii++) {
															if ($row.find('td:last').html() != '') {
																$row.find('td:last').append('<br />');
															}
															$row.find('td:last').append('<i style="color:blue;">' + acta.items[i].historico[ii].tipo + ' ' + acta.items[i].historico[ii].num +
																'</i> por ' + ciHelper.formatMon(acta.items[i].historico[ii].total, acta.items[i].historico[ii].moneda) +
																' del <i style="color:green;">' + ciHelper.date.format.bd_ymd(acta.items[i].historico[ii].fec) + '</i>');
														}
													}
												}
												$row.data('data', acta.items[i]);
												p.$w.find('[name=gridPag] tbody').append($row);
											}
										}
										/*
										 * SE AÑADEN LOS COBROS ADICIONALES
										 */
										if (acta.cobros != null) {
											for (var i = 0; i < contrato.cobros.length; i++) {
												var $row = $('<tr class="item">');
												$row.append('<td>' + contrato.cobros[i].servicio.nomb + '</td>');
												$row.append('<td>' + ciHelper.formatMon(contrato.cobros[i].total) + '</td>');
												if (contrato.cobros[i].estado == 'P') {
													$row.append('<td><button class="btn btn-xs btn-info"><i class="fa fa-money"></i> Cobrar</button><br />' +
														'<button class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> Eliminar</button></td>');
													$row.data('data', contrato.cobros[i]);
													$row.find('button:first').click(function () {
														var $row = $(this).closest('.item');
														inMovi.newCobro({
															cobro: $row.data('data')
														});
													});
													$row.find('button:last').click(function () {
														var $row = $(this).closest('.item');
														ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Cobro de <b>' + $row.find('td:eq(0)').html() + '</b>&#63;',
															function () {
																K.sendingInfo();
																$.post('in/comp/delete_cobro', {
																	_id: $row.data('data')._id.$id
																}, function () {
																	K.clearNoti();
																	K.msg({
																		title: 'Cobro Eliminado',
																		text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'
																	});
																	$row.remove();
																});
															},
															function () {
																$.noop();
															}, 'Eliminaci&oacute;n de Cobro de Servicios');
													});
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												} else {
													$row.append('<td>');
													for (var j = 0; j < contrato.cobros[i].comprobantes.length; j++) {
														if ($row.find('td:last').html() != '') $row.find('td:last').append('<br />');
														var comp = contrato.cobros[i].comprobantes[j];
														$row.find('td:last').append('<button class="btn btn-xs btn-success"><i class="fa fa-file-pdf-o"></i> ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button>' +
															'<button class="btn btn-xs btn-danger"><i class="fa fa-close"></i> Anular ' + comp.tipo + ' ' + comp.serie + '-' + comp.num + '</button></td>');
														$row.find('button:first').data('id', comp._id.$id)
															.click(function () {
																K.windowPrint({
																	id: 'windowPrint',
																	title: "Comprobante de Pago",
																	url: "in/comp/print?_id=" + $(this).data('id')
																});
															});
														$row.find('button:last').data('id', comp._id.$id).data('nomb', comp.tipo + ' ' + comp.serie + '-' + comp.num).data('inmueble', contrato.inmueble)
															.click(function () {
																var tmp = $(this);
																ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>' + tmp.data('nomb') + '</b>&#63;',
																	function () {
																		K.sendingInfo();
																		$.post('in/comp/anular', {
																			_id: tmp.data('id')
																		}, function () {
																			K.clearNoti();
																			K.msg({
																				title: 'Comprobante Anulado',
																				text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'
																			});
																			inMovi.init({
																				inmueble: inInmu.dbRel(tmp.data('inmueble'))
																			});
																		});
																	},
																	function () {
																		$.noop();
																	}, 'Anulaci&oacute;n de Comprobante');
															});
													}
													$row.data('data', contrato.cobros[i]);
													p.$w.find('[name=gridCta] tbody').append($row);
												}
											}
										}
									});
									$row.data('data', p.actas[i]);
									p.$w.find('[name=gridCont] tbody').append($row);
									if (p.titulares.indexOf(p.actas[i].arrendatario) == -1) {
										p.titulares.push(p.actas[i].arrendatario);
									}
									if (p.$w.find('[name=' + p.actas[i].arrendatario._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + p.actas[i].arrendatario._id.$id + '">');
										$row.append('<td>' + mgEnti.formatName(p.actas[i].arrendatario) + '</td>');
										$row.append('<td>' + mgEnti.formatDNI(p.actas[i].arrendatario) + '</td>');
										$row.append('<td>' + mgEnti.formatRUC(p.actas[i].arrendatario) + '</td>');
										$row.data('data', p.actas[i].arrendatario);
										$row.click(function () {
											var titular = $(this).closest('.item').data('data');
											p.$w.find('[name=gridCont] tbody tr[data-titu=' + titular._id.$id + ']').show();
											p.$w.find('[name=gridCont] tbody tr[data-titu!=' + titular._id.$id + ']').hide();
											p.$w.find('[name=gridCont] .item:visible:eq(0) td:first').click();
										});
										p.$w.find('[name=gridArre] tbody').append($row);
									}
								}
							}
							p.$w.find('[name=gridArre] .item:eq(0)').click();
							K.unblock();
						}, 'json');
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridArre]'),
					search: false,
					pagination: false,
					cols: ['Raz&oacute;n Social / Nombres', 'DNI', 'RUC', ''],
					onlyHtml: true,
					toolbarHTML: '<h3>ARRENDATARIOS</h3>'
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Tipo de Contrato', 'Cnt. Inicial', 'Cnt. Final', 'Fec. Desoc.', 'Importe', '', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Nuevo Contrato</button>&nbsp;' +
						'<select class="form-control" name="mostrar">' +
						'<option value="0">Mostrar s&oacute;lo Contratos Activos</option>' +
						'<option value="1">Mostrar todos los Contratos</option>' +
						'</select>',
					onContentLoaded: function ($el) {
						$el.find('button').click(function () {
							var inmueble = p.$w.find('[name=inmueble] option:selected').data('data');
							if (inmueble == null)
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe escoger un inmueble!',
									type: 'error'
								});
							inMovi.newContrato({
								inmueble: inmueble
							});
						});
						$el.find('[name=mostrar]').change(function () {
							p.$w.find('[name=inmueble]').change();
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['', 'Periodo', 'Importe sin IGV o Moras', 'Estado', 'Comprobante'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-money"></i> Registrar Cobro de Cuota</button>&nbsp;' +
						'<button class="btn btn-info"><i class="fa fa-puzzle-piece"></i> Adelantar Pago Parcial</button>',
					onContentLoaded: function ($el) {
						/****************************************************************************************************************
						 * PAGO DE MENSUALIDAD
						 ****************************************************************************************************************/
						$el.find('button:first').click(function () {
							var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
							if (contrato == null)
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe escoger un contrato!',
									type: 'error'
								});
							if (contrato.contrato_dias == '1') {
								/*
								inMovi.newMes({
									contrato: contrato,
									dias: true,
									pagos: [contrato.pagos[0]]
								});
								return true;
								*/
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar periodo de días',
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un periodo!',
														type: 'error'
													});
												} else {
													for (var i = 0, j = p.$tmp.find('[name=mes]:checked').length; i < j; i++) {
														tmp.push(p.$tmp.find('[name=mes]:checked').eq(i).closest('.item').data('data'));
													}
												}
												var data = {
													tipo: 'pago_meses',
													contrato: contrato,
													pagos: tmp
												};
												console.log("out seleccionar periodo "+JSON.stringify(data));
												p.callback(data);
												K.closeWindow(p.$tmp.attr('id'));
												K.closeWindow(p.$w.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										console.log("contrato="+JSON.stringify(contrato));
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Periodo'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										var dia_ini_tmp = ciHelper.date.format.bd_d(contrato.fecini);
										for (var i = 0, j = contrato.pagos.length; i < j; i++) {
											var tmp;
											if (contrato.pagos[i].estado != null) {
												tmp = false;
												if (contrato.pagos[i].estado == 'P') {
													if (contrato.pagos[i].comprobantes != null) {
														tmp = false;
													} else
														tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="checkbox" name="mes" /></td>');
												var day_start = ciHelper.date.format.bd_d(contrato.fecini),
													month_start = ciHelper.date.format.bd_m(contrato.fecini),
													year_start = ciHelper.date.format.bd_y(contrato.fecini),
													day_end = ciHelper.date.format.bd_d(contrato.fecfin),
													month_end = ciHelper.date.format.bd_m(contrato.fecfin),
													year_end = ciHelper.date.format.bd_y(contrato.fecfin);
												var tmp_ini = 'Del ' + day_start + ' de ' + ciHelper.meses[parseInt(month_start)-1] + '-' + year_start,
													tmp_fin = 'Al ' + day_end + ' de ' + ciHelper.meses[parseInt(month_end)-1] + '-' + year_end;
												$row.append('<td>' + tmp_ini + '<br />' + tmp_fin + '</td>');
												$row.data('data', contrato.pagos[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:checkbox:first').attr('checked', 'checked');
										p.$tmp.find('[name=mes]').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
								return true;
							}
							//console.log("other ifs");
							/*
							 * EN CASO SEA CONTRATO NORMAL
							 */
							if (contrato.pagos != null) {
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar Meses a Pagar',
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un mes!',
														type: 'error'
													});
												} else {
													for (var i = 0, j = p.$tmp.find('[name=mes]:checked').length; i < j; i++) {
														tmp.push(p.$tmp.find('[name=mes]:checked').eq(i).closest('.item').data('data'));
													}
												}
												var data = {
													tipo: 'pago_meses',
													contrato: contrato,
													pagos: tmp
												};
												p.callback(data);
												K.closeWindow(p.$tmp.attr('id'));
												K.closeWindow(p.$w.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Periodo'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										var dia_ini_tmp = ciHelper.date.format.bd_d(contrato.fecini);
										for (var i = 0, j = contrato.pagos.length; i < j; i++) {
											var tmp;
											if (contrato.pagos[i].estado != null) {
												tmp = false;
												if (contrato.pagos[i].estado == 'P') {
													if (contrato.pagos[i].comprobantes != null) {
														tmp = false;
													} else
														tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="checkbox" name="mes" /></td>');




												if (parseInt(dia_ini_tmp) == 1) {
													$row.append('<td>' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano + '</td>');
												} else {
													var mes_n = parseInt(contrato.pagos[i].mes) - 1,
														ano_n = parseInt(contrato.pagos[i].ano);
													mes_n--;
													if (mes_n == -1) {
														mes_n = 11;
														ano_n--;
													}
													var tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[mes_n] + '-' + ano_n,
														tmp_fin = 'Al ' + (parseInt(dia_ini_tmp) - 1) + ' de ' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano;
													$row.append('<td>' + tmp_ini + '<br />' + tmp_fin + '</td>');
												}



												//$row.append('<td>'+ciHelper.meses[parseInt(contrato.pagos[i].mes)-1]+'-'+contrato.pagos[i].ano+'</td>');
												$row.data('data', contrato.pagos[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:checkbox:first').attr('checked', 'checked');
										p.$tmp.find('[name=mes]').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
								/*
								 * EN CASO SEA UN ACTA DE CONCILIACION
								 */
							} else if (contrato.items != null) {
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar Cuotas a Pagar',
									width: 650,
									height: 550,
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un mes!',
														type: 'error'
													});
												} else {
													for (var i = 0, j = p.$tmp.find('[name=mes]:checked').length; i < j; i++) {
														tmp.push(p.$tmp.find('[name=mes]:checked').eq(i).closest('.item').data('data'));
													}
												}
												var data = {
													tipo: 'pago_acta',
													contrato: contrato,
													pagos: tmp
												};
												p.callback(data);
												K.closeWindow(p.$tmp.attr('id'));
												K.closeWindow(p.$w.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Vencimiento', 'Total'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										for (var i = 0, j = contrato.items.length; i < j; i++) {
											var tmp;
											if (contrato.items[i].estado != null) {
												tmp = false;
												if (contrato.items[i].estado == 'P') {
													if (contrato.items[i].comprobantes != null) {
														tmp = false;
													} else
														tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="checkbox" name="mes" /></td>');
												$row.append('<td>' + ciHelper.date.format.bd_ymd(contrato.items[i].fecven) + '</td>');
												$row.append('<td>' + ciHelper.formatMon(contrato.items[i].total) + '</td>');
												$row.data('data', contrato.items[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:checkbox:first').attr('checked', 'checked');
										p.$tmp.find('[name=mes]').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
							} else {
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El contrato no tiene cobros programados',
									type: 'error'
								});
							}
						});
						/****************************************************************************************************************
						 * PAGAR PARCIAL
						 ****************************************************************************************************************/
						$el.find('button:last').click(function () {
							var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
							if (contrato == null)
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe escoger un contrato!',
									type: 'error'
								});
							if (contrato.contrato_dias == '1') {
								inMovi.newMes({
									contrato: contrato,
									dias: true,
									pagos: [contrato.pagos[0]]
								});
								return true;
							}
							if (contrato.pagos != null) {
								new K.Modal({
									id: 'windowSelect',
									content: '<div name="tmp"></div>',
									title: 'Seleccionar Meses a Pagar',
									buttons: {
										"Seleccionar": {
											icon: 'fa-check',
											type: 'info',
											f: function () {
												var tmp = [];
												if (p.$tmp.find('[name=mes]:checked').length <= 0) {
													return K.msg({
														title: ciHelper.titles.infoReq,
														text: 'Debe seleccionar al menos un mes!',
														type: 'error'
													});
												}
												var data = {
													tipo: 'pago_parcial',
													contrato: contrato,
													pago: p.$tmp.find('[name=mes]:checked').closest('.item').data('data')
												};
												p.callback(data);
												K.closeWindow(p.$tmp.attr('id'));
												K.closeWindow(p.$w.attr('id'));
											}
										},
										"Cancelar": {
											icon: 'fa-ban',
											type: 'danger',
											f: function () {
												K.closeWindow(p.$tmp.attr('id'));
											}
										}
									},
									onContentLoaded: function () {
										p.$tmp = $('#windowSelect');
										new K.grid({
											$el: p.$tmp.find('[name=tmp]'),
											cols: ['', 'Periodo'],
											search: false,
											pagination: false,
											onlyHtml: true
										});
										for (var i = 0, j = contrato.pagos.length; i < j; i++) {
											var tmp;
											if (contrato.pagos[i].estado != null) {
												tmp = false;
												if (contrato.pagos[i].estado == 'P') {
													tmp = true;
												}
											} else {
												tmp = true;
											}
											if (tmp == true) {
												var $row = $('<tr class="item">');
												$row.append('<td><input type="radio" name="mes" /></td>');
												$row.append('<td>' + ciHelper.meses[parseInt(contrato.pagos[i].mes) - 1] + '-' + contrato.pagos[i].ano + '</td>');
												$row.data('data', contrato.pagos[i]);
												$row.find('td:not(:eq(0))').click(function () {
													$(this).closest('.item').find('input').iCheck('check');
												});
												p.$tmp.find('[name=tmp] tbody').append($row);
											}
										}
										p.$tmp.find('input:radio:first').attr('checked', 'checked');
										p.$tmp.find('input:radio').iCheck({
											checkboxClass: 'icheckbox_square-green',
											radioClass: 'iradio_square-green'
										});
									}
								});
							} else if (contrato.items != null) {
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'No se pueden hacer cobros parciales de Actas de Conciliaci&oacute;n',
									type: 'error'
								});
							} else {
								K.msg({
									title: ciHelper.titles.infoReq,
									text: 'El contrato no tiene cobros programados',
									type: 'error'
								});
							}
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridCta]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n', 'Importe', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-cart-arrow-down"></i> Agregar Cobro Adicional (Luz, Agua, Derechos, etc.)</button>',
					onContentLoaded: function ($el) {
						$el.find('button').click(function () {
							var contrato = p.$w.find('[name=gridCont] .highlights').data('data');
							inMovi.newCuentaWindow({
								contrato: contrato,
								callback: function () {
									p.$w.find('[name=sublocal]').change();
								}
							});
						});
					}
				});
				$.post('in/movi/get_tipo_sub', function (data) {
					p.$w.find('[name=gridArre] .fuelux:first,[name=gridCont] .fuelux:first').css('max-height', '420px');
					p.tipo = data.tipo;
					p.sublocal = data.sublocal;
					var $cbo = p.$w.find('[name=tipo]');
					for (var i = 0, j = p.tipo.length; i < j; i++) {
						$cbo.append('<option value="' + i + '">' + p.tipo[i].nomb + '</option>');
					}
					var inmu_tmp = $.jStorage.get('in/movi/get_all_cont');
					if (p.inmueble == null) {
						if (inmu_tmp != null) p.inmueble = inmu_tmp;
					}
					if (p.inmueble == null) {
						p.$w.find('[name=tipo]').chosen();
						$cbo.change();
					} else {
						for (var i = 0, j = p.tipo.length; i < j; i++) {
							if (p.tipo[i]._id.$id == p.inmueble.tipo._id) {
								p.$w.find('[name=tipo] option').eq(i).attr('selected', 'selected');
								break;
							}
						}
						p.$w.find('[name=tipo]').chosen();
						var val = p.inmueble.tipo._id,
							$cbo = p.$w.find('[name=sublocal]').empty();
						if (p.sublocal != null) {
							for (var i = 0, j = p.sublocal.length; i < j; i++) {
								if (p.sublocal[i].tipo._id.$id == val) {
									$cbo.append('<option value="' + i + '">' + p.sublocal[i].nomb + '</option>');
									if (p.sublocal[i]._id.$id == p.inmueble.sublocal._id)
										$cbo.find('option:last').attr('selected', 'selected');
								}
							}
						}
						p.$w.find('[name=sublocal]').chosen();
						p.$w.find('[name=inmueble]').empty();
						$.post('in/inmu/get_all_sub', {
							_id: p.inmueble.sublocal._id
						}, function (data) {
							var $cbo = p.$w.find('[name=inmueble]').empty();
							if (data != null) {
								for (var i = 0, j = data.length; i < j; i++) {
									$cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].direccion + '</option>');
									$cbo.find('option:last').data('data', data[i]);
								}
							}
							$cbo.selectVal(p.inmueble._id);
							$cbo.chosen().change();
						}, 'json');
					}
				}, 'json');
			}
		});
	},


	/**************************************************************************************************************************
	 *
	 * CREACION DE NUEVO CONTRATO
	 *
	 **************************************************************************************************************************/
	newContrato: function (p) {
		$.extend(p, {
			cbGarantia: function (data, $row) {
				if ($row != null) {
					$row.find('td:eq(0)').html(inMovi.tipo_doc_gar[data.tipo]);
					$row.find('td:eq(1)').html(data.num);
					$row.find('td:eq(2)').html(data.fec);
					$row.find('td:eq(3)').html(ciHelper.formatMon(data.importe, data.moneda));
					$row.data('data', data);
				} else {
					var $row = $('<tr class="item">');
					$row.append('<td>' + inMovi.tipo_doc_gar[data.tipo] + '</td>');
					$row.append('<td>' + data.num + '</td>');
					$row.append('<td>' + data.fec + '</td>');
					$row.append('<td>' + ciHelper.formatMon(data.importe, data.moneda) + '</td>');
					$row.append('<td>' +
						'<button name="btnEdi" type="button" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></button>' +
						'<button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>' +
						'</td>');
					$row.find('[name=btnEdi]').click(function () {
						inMovi.modalGarantia({
							callback: p.cbGarantia,
							data: $(this).closest('.item').data('data'),
							$row: $(this).closest('.item')
						});
					});
					$row.find('[name=btnEli]').click(function () {
						$(this).closest('tr').remove();
					});
					$row.data('data', data);
					p.$w.find('[name=gridGar] tbody').append($row);
				}
			},
			cbLetra: function (data, $row) {
				if ($row != null) {
					$row.find('td:eq(1)').html(data.fecven);
					$row.data('data', data);
				} else {
					var $row = $('<tr class="item">');
					$row.append('<td>');
					$row.append('<td>' + data.fecven + '</td>');
					$row.append('<td>');
					$row.append('<td>' +
						'<button name="btnEdi" type="button" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></button>' +
						'<button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>' +
						'</td>');
					$row.find('[name=btnEdi]').click(function () {
						inMovi.modalLetra({
							callback: p.cbLetra,
							data: $(this).closest('.item').data('data'),
							$row: $(this).closest('.item')
						});
					});
					$row.find('[name=btnEli]').click(function () {
						$(this).closest('tr').remove();
					});
					$row.data('data', data);
					p.$w.find('[name=gridLet] tbody').append($row);
				}
			},
			calcPag: function () {
				p.$w.find('[name=gridPag] tbody').empty();
				var ini = p.$w.find('[name=fecini]').val(),
					fin = p.$w.find('[name=fecfin]').val();
				if (ini != '' && fin != '') {
					var fecini = new Date(ini),
						fecfin = new Date(fin),
						total_meses = (fecfin - fecini) / 1000 / 60 / 60 / 24 / 30,
						mes_ini = parseInt(fecini.getMonth()) + 1,
						ano_ini = fecini.getFullYear(),
						tmp_ano = 0;
					for (var i = 0, j = total_meses; i < j; i++) {
						var $row = $('<tr class="item">');
						var mes = mes_ini + i,
							ano = ano_ini;
						if (mes >= 12) {
							mes = mes - (12 * tmp_ano);
							if (mes == 12) {
								mes = 0;
								ano_ini++;
								ano++;
								tmp_ano++;
							}
						}
						$row.append('<td>' + (i + 1) + '</td>');
						$row.append('<td>');
						var $cbo = $('<select name="mes">');
						for (var ii = 0; ii < ciHelper.meses.length; ii++) {
							$cbo.append('<option value="' + (ii + 1) + '">' + ciHelper.meses[ii] + '</option>');
						}
						$cbo.selectVal(mes + 1);
						$row.find('td:last').append($cbo);
						$row.find('select').selectVal('' + (mes + 1));
						$row.append('<td><input type="text" name="ano" value="' + ano + '" /></td>');
						$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
						$row.find('button:last').click(function () {
							$(this).closest('.item').remove();
							for (var i = 0, j = p.$w.find('[name=gridPag] tbody .item').length; i < j; i++) {
								var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
								$row.find('td:eq(0)').html(i + 1);
							}
						});
						p.$w.find('[name=gridPag] tbody').append($row);
						if (mes == 11) {
							mes = 12;
						}
						if ((mes == (parseInt(fecfin.getMonth()) + 1)) && (ano == (fecfin.getFullYear()))) {
							i = j;
							return 0;
						}
					}
				}
			}
		});
		new K.Panel({
			title: 'Nuevo Contrato',
			contentURL: 'in/movi/edit_cont',
			buttons: {
				'Guardar Contrato': {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							inmueble: inInmu.dbRel(p.inmueble),
							titular: p.$w.find('[name=arrendatario] [name=mini_enti]').data('data'),
							aval: p.$w.find('[name=aval] [name=mini_enti]').data('data'),
							situacion: p.$w.find('[name=situacion] option:selected').val(),
							moneda: p.$w.find('[name=moneda] option:selected').val(),
							importe: p.$w.find('[name=importe]').val(),
							fecdes: p.$w.find('[name=fecdes]').val(),
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							motivo: p.$w.find('[name=motivo] option:selected').data('data'),
							contrato_dias: 0,
							desalojo: 0,
							odsd: 0,
							infocorp: 0,
							as_externa: 0,
							nro_contrato: p.$w.find('[name=nro_contrato]').val(),
							sedapar: p.$w.find('[name=sedapar]').val(),
							seal: p.$w.find('[name=seal]').val(),
							arbitrios: p.$w.find('[name=arbitrios]').val(),
							con_mora: p.$w.find('[name=con_mora] option:selected').val(),
							compensacion: p.$w.find('[name=compensacion] option:selected').val(),
							porcentaje: p.$w.find('[name=porcentaje]').val()
						};
						if (data.titular == null) {
							p.$w.find('[name=arrendatario] [name=btnSel]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un titular!',
								type: 'error'
							});
						} else data.titular = mgEnti.dbRel(data.titular);
						if (data.aval == null) {
							/*p.$w.find('[name=btnAval]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un aval!',
								type: 'error'
							});*/
						} else data.aval = mgEnti.dbRel(data.aval);
						data.motivo = {
							_id: data.motivo._id.$id,
							nomb: data.motivo.nomb
						};
						if (data.importe == '') data.importe = 0;
						if (data.fecdes == null) {
							p.$w.find('[name=fecdes]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una fecha de desocupaci&oacute;n!',
								type: 'error'
							});
						}
						if (data.fecini == null) {
							p.$w.find('[name=fecini]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una fecha de inicio de vigencia!',
								type: 'error'
							});
						}
						if (data.fecfin == null) {
							p.$w.find('[name=fecfin]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una fecha de fin de vigencia!',
								type: 'error'
							});
						}
						/*
						 * representantes
						 */
						for (var i = 0; i < p.$w.find('[name=gridRep] tbody .item').length; i++) {
							if (data.representantes == null)
								data.representantes = [];
							data.representantes.push(mgEnti.dbRel(p.$w.find('[name=gridRep] tbody .item').eq(i).data('data')));
						}
						/*
						 * garantias
						 */
						/*for(var i=0; i<p.$w.find('[name=gridLet] tbody .item').length; i++){
							if(data.letras==null)
								data.letras = [];
							data.letras.push(p.$w.find('[name=gridLet] tbody .item').eq(i).data('data'));
						}*/
						/*
						 * Letras
						 */
						for (var i = 0; i < p.$w.find('[name=gridLet] tbody .item').length; i++) {
							if (data.letras == null)
								data.letras = [];
							data.letras.push(p.$w.find('[name=gridLet] tbody .item').eq(i).data('data'));
						}
						/*
						 * GARANTIAS
						 */
						for (var i = 0; i < p.$w.find('[name=gridGar] tbody .item').length; i++) {
							if (data.garantias == null)
								data.garantias = [];
							data.garantias.push(p.$w.find('[name=gridGar] tbody .item').eq(i).data('data'));
						}
						if (p.$w.find('[name=rbtnDias]').is(':checked'))
							data.contrato_dias = 1;
						if (p.$w.find('[name=rbtnDes]').is(':checked'))
							data.desalojo = 1;
						if (p.$w.find('[name=rbtnInf]').is(':checked'))
							data.infocorp = 1;
						if (p.$w.find('[name=rbtnOds]').is(':checked'))
							data.odsd = 1;
						if (p.$w.find('[name=rbtnAse]').is(':checked'))
							data.as_externa = 1;
						if (data.nro_contrato === '') {
							p.$w.find('[name=nro_contrato]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe escribir un numero de contrato!',
								type: 'error'
							});
						}
						/*
						 * PAGOS
						 */
						for (var i = 0; i < p.$w.find('[name=gridPag] tbody .item').length; i++) {
							if (data.pagos == null) {
								data.pagos = [];
							}
							var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
							data.pagos.push({
								item: i + 1,
								mes: $row.find('[name=mes] option:selected').val(),
								ano: $row.find('[name=ano]').val()
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("in/movi/save_cont", data, function (result) {
							K.clearNoti();
							K.msg({
								title: ciHelper.titles.regiGua,
								text: "Contrato creado!"
							});
							inMovi.init({
								inmueble: inInmu.dbRel(p.inmueble)
							});
						}, 'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						inMovi.init({
							inmueble: inInmu.dbRel(p.inmueble)
						});
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=tipo]').html(p.inmueble.tipo.nomb);
				p.$w.find('[name=sublocal]').html(p.inmueble.sublocal.nomb);
				p.$w.find('[name=inmueble]').html(p.inmueble.direccion);
				/*
				 * ARRENDATARIO
				 */
				p.$w.find('[name=arrendatario] .panel-title').html('ARRENDATARIO');
				p.$w.find('[name=arrendatario] [name=btnSel]').click(function () {
					mgEnti.windowSelect({
						callback: function (data) {
							mgEnti.fillMini(p.$w.find('[name=arrendatario] [name=mini_enti]'), data);
						},
						bootstrap: true
					});
				});
				p.$w.find('[name=arrendatario] [name=btnAct]').click(function () {
					if (p.$w.find('[name=arrendatario] [name=mini_enti]').data('data') == null) {
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					} else {
						mgEnti.windowEdit({
							callback: function (data) {
								mgEnti.fillMini(p.$w.find('[name=arrendatario] [name=mini_enti]'), data);
							},
							id: p.$w.find('[name=arrendatario] [name=mini_enti]').data('data')._id.$id
						});
					}
				});
				/*
				 * AVAL
				 */
				p.$w.find('[name=aval] .panel-title').html('AVAL');
				p.$w.find('[name=aval] [name=btnSel]').click(function () {
					mgEnti.windowSelect({
						callback: function (data) {
							mgEnti.fillMini(p.$w.find('[name=aval] [name=mini_enti]'), data);
						},
						bootstrap: true
					});
				});
				p.$w.find('[name=aval] [name=btnAct]').click(function () {
					if (p.$w.find('[name=aval] [name=mini_enti]').data('data') == null) {
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					} else {
						mgEnti.windowEdit({
							callback: function (data) {
								mgEnti.fillMini(p.$w.find('[name=aval] [name=mini_enti]'), data);
							},
							id: p.$w.find('[name=aval] [name=mini_enti]').data('data')._id.$id
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridRep]'),
					search: false,
					pagination: false,
					cols: ['Apellidos y Nombre', 'Documento', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgr"><i class="fa fa-user"></i> Agregar Representante Legal</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgr]').click(function () {
							mgEnti.windowSelect({
								callback: function (data) {
									if (p.$w.find('[name=rep' + data._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="rep' + data._id.$id + '">');
										$row.append('<td>' + mgEnti.formatName(data) + '</td>');
										$row.append('<td>' + mgEnti.formatIden(data) + '</td>');
										$row.append('<td><button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('[name=btnEli]').click(function () {
											$(this).closest('tr').remove();
										});
										$row.data('data', data);
										p.$w.find('[name=gridRep] tbody').append($row);
									} else {
										K.msg({
											title: ciHelper.titles.infoReq,
											text: 'La entidad seleccionada ya fue agregada!',
											type: 'error'
										});
									}
								},
								bootstrap: true
							});
						}).button({
							icons: {
								primary: 'ui-icon-search'
							}
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridGar]'),
					search: false,
					pagination: false,
					cols: ['Tip. Doc.', 'Nro', 'Fecha', 'Importe', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgr"><i class="fa fa-file-text-o"></i> Agregar Garant&iacute;a</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgr]').click(function () {
							inMovi.modalGarantia({
								callback: p.cbGarantia
							});
						});
					}
				});
				var $cbo = p.$w.find('[name=situacion]');
				for (var i = 0; i < inMovi.situacion.length; i++) {
					$cbo.append('<option value="' + inMovi.situacion[i].cod + '">' + inMovi.situacion[i].descr + '</option>');
				}
				new K.grid({
					$el: p.$w.find('[name=gridLet]'),
					search: false,
					pagination: false,
					cols: ['', 'Fecha', 'Protesto - Fecha de Pago', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary" type="button" name="btnAgr"><i class="fa fa-money"></i> Agregar Letra</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgr]').click(function () {
							inMovi.modalLetra({
								callback: p.cbLetra
							});
						});
					}
				});
				p.$w.find('[name=fecdes]').datepicker({
					format: 'yyyy-mm-dd'
				});
				p.$w.find('[name=fecini]').datepicker({
						format: 'yyyy-mm-dd'
					})
					.on('changeDate', function (ev) {
						p.calcPag();
					}).change(function () {
						p.calcPag();
					});
				p.$w.find('[name=fecfin]').datepicker({
						format: 'yyyy-mm-dd'
					})
					.on('changeDate', function (ev) {
						p.calcPag();
					}).change(function () {
						p.calcPag();
					});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['', 'Mes', 'A&ntilde;o', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-success"><i class="fa fa-plus"></i> Agregar Pago</button>',
					onContentLoaded: function ($el) {
						$el.find('button').click(function () {
							var $row = $('<tr class="item">');
							$row.append('<td>' + (p.$w.find('[name=gridPag] tbody .item').length + 1) + '</td>');
							$row.append('<td>');
							var $cbo = $('<select name="mes">');
							for (var i = 0; i < ciHelper.meses.length; i++) {
								$cbo.append('<option value="' + (i + 1) + '">' + ciHelper.meses[i] + '</option>');
							}
							$row.find('td:last').append($cbo);
							$row.append('<td><input type="text" name="ano" value="' + ciHelper.date.getYear() + '" /></td>');
							$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button:last').click(function () {
								$(this).closest('.item').remove();
								for (var i = 0, j = p.$w.find('[name=gridPag] tbody .item').length; i < j; i++) {
									var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
									$row.find('td:eq(0)').html(i + 1);
								}
							});
							p.$w.find('[name=gridPag] tbody').append($row);
						});
					}
				});
				p.$w.find('[name=compensacion]').change(function () {
					p.$w.find('[name=porcentaje_comp]').val(0);
					if ($(this).val() == '1') {
						p.$w.find('[name=porcentaje_comp]').closest('.col-sm-4').show();
					} else
						p.$w.find('[name=porcentaje_comp]').closest('.col-sm-4').hide();
				}).change();
				$.post('in/moti/all', function (data) {
					var $cbo = p.$w.find('[name=motivo]');
					if (data != null)
						for (var i = 0, j = data.length; i < j; i++) {
							$cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].nomb + '</option>');
							$cbo.find('option:last').data('data', data[i]);
						}
					else {
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe crear primero MOTIVOS DE CONTRATO',
							type: 'error'
						});
						return inMovi.init();
					}
					K.unblock();
				}, 'json');
			}
		});
	},
	/**************************************************************************************************************************
	 *
	 * VENTANA DE MODAL DE GARANTIA
	 *
	 **************************************************************************************************************************/
	modalGarantia: function (p) {
		new K.Modal({
			id: 'modGarantia',
			title: 'Edici&oacute;n de Garant&iacute;a',
			contentURL: 'in/movi/garantia',
			width: 750,
			height: 290,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							num: p.$w.find('[name=num]').val(),
							fec: p.$w.find('[name=fec]').val(),
							moneda: p.$w.find('[name=moneda]').val(),
							importe: p.$w.find('[name=importe]').val(),
							dev_importe: p.$w.find('[name=dev_importe]').val(),
							dev_fec: p.$w.find('[name=dev_fec]').val()
						};
						if (data.num == '') {
							p.$w.find('[name=num]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe ingresar el numero de documento!',
								type: 'error'
							});
						}
						if (data.fec == '') {
							p.$w.find('[name=fec]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe ingresar la fecha del documento!',
								type: 'error'
							});
						}
						if (data.importe == '') {
							p.$w.find('[name=importe]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe ingresar el importe de documento!',
								type: 'error'
							});
						}
						if (p.data != null) {
							if (p.data.oldid != null)
								data.oldid = p.data.oldid;
							if (p.data.traslados != null)
								data.traslados = p.data.traslados;
						}
						K.sendingInfo();
						if (p.$row != null)
							p.callback(data, p.$row);
						else
							p.callback(data);
						K.closeWindow(p.$w.attr('id'));
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#modGarantia');
				p.$w.find('[name=fec],[name=dev_fec]').datepicker({
					format: 'yyyy-mm-dd'
				});
				for (tipo in inMovi.tipo_doc_gar) {
					p.$w.find('[name=tipo]').append('<option value="' + tipo + '">' + inMovi.tipo_doc_gar[tipo] + '</option>');
				}
				if (p.data != null) {
					p.$w.find('[name=tipo]').selectVal(p.data.tipo);
					p.$w.find('[name=num]').val(p.data.num);
					p.$w.find('[name=fec]').val(p.data.fec);
					p.$w.find('[name=moneda]').selectVal(p.data.moneda);
					p.$w.find('[name=importe]').val(p.data.importe);
					if (p.data.devolucion != null) {
						p.$w.find('[name=dev_importe]').val(p.data.devolucion.dev_importe);
						p.$w.find('[name=dev_fec]').val(p.data.devolucion.dev_fec);
					}
				}
			}
		});
	},
	/**************************************************************************************************************************
	 *
	 * VENTANA DE MODAL DE LETRAS
	 *
	 **************************************************************************************************************************/
	modalLetra: function (p) {
		new K.Modal({
			id: 'modLetra',
			title: 'Edici&oacute;n de Letra',
			contentURL: 'in/movi/letra',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							fecven: p.$w.find('[name=fecven]').val(),
							protesto: false
						};
						if (data.fecven == '') {
							p.$w.find('[name=fecven]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe ingresar la fecha de vencimiento!',
								type: 'error'
							});
						}
						if (p.$w.find('[name=rbtnPro]').is(':checked'))
							data.protesto = true;
						if (p.$w.find('[name=fecpag]').val() != '')
							data.fecpag = p.$w.find('[name=fecpag]').val();
						K.sendingInfo();
						if (p.$row != null)
							p.callback(data, p.$row);
						else
							p.callback(data);
						K.closeWindow(p.$w.attr('id'));
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#modLetra');
				p.$w.find('[name=fecven]').datepicker({
					format: 'yyyy-mm-dd'
				});
				p.$w.find('[name=fecpag]').datepicker({
					format: 'yyyy-mm-dd'
				});
				if (p.data != null) {
					p.$w.find('[name=fecven]').val(p.data.fecven);
					if (p.data.protesto == '1')
						p.$w.find('[name=protesto]').attr('checked', 'checked');
					if (p.data.fecpag != null)
						p.$w.find('[name=fecpag]').val(p.data.fecpag);
				}
			}
		});
	},
	/**************************************************************************************************************************
	 *
	 * EDICION DE CONTRATOS
	 *
	 **************************************************************************************************************************/
	editContrato: function (p) {
		$.extend(p, {
			cbGarantia: function (data, $row) {
				if ($row != null) {
					$row.find('td:eq(0)').html(inMovi.tipo_doc_gar[data.tipo]);
					$row.find('td:eq(1)').html(data.num);
					$row.find('td:eq(2)').html(data.fec);
					$row.find('td:eq(3)').html(ciHelper.formatMon(data.importe, data.moneda));
					$row.data('data', data);
				} else {
					var $row = $('<tr class="item">');
					$row.append('<td>' + inMovi.tipo_doc_gar[data.tipo] + '</td>');
					$row.append('<td>' + data.num + '</td>');
					$row.append('<td>' + data.fec + '</td>');
					$row.append('<td>' + ciHelper.formatMon(data.importe, data.moneda) + '</td>');
					$row.append('<td>' +
						'<button name="btnEdi" type="button" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></button>' +
						'<button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>' +
						'</td>');
					$row.find('[name=btnEdi]').click(function () {
						inMovi.modalGarantia({
							callback: p.cbGarantia,
							data: $(this).closest('.item').data('data'),
							$row: $(this).closest('.item')
						});
					});
					$row.find('[name=btnEli]').click(function () {
						$(this).closest('tr').remove();
					});
					$row.data('data', data);
					p.$w.find('[name=gridGar] tbody').append($row);
				}
			},
			cbLetra: function (data, $row) {
				if ($row != null) {
					$row.find('td:eq(1)').html(data.fecven);
					$row.data('data', data);
				} else {
					var $row = $('<tr class="item">');
					$row.append('<td>');
					$row.append('<td>' + data.fecven + '</td>');
					$row.append('<td>');
					$row.append('<td>' +
						'<button name="btnEdi" type="button" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></button>' +
						'<button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>' +
						'</td>');
					$row.find('[name=btnEdi]').click(function () {
						inMovi.modalLetra({
							callback: p.cbLetra,
							data: $(this).closest('.item').data('data'),
							$row: $(this).closest('.item')
						});
					});
					$row.find('[name=btnEli]').click(function () {
						$(this).closest('tr').remove();
					});
					$row.data('data', data);
					p.$w.find('[name=gridLet] tbody').append($row);
				}
			},
			calcPag: function () {
				p.$w.find('[name=gridPag] tbody .item').each(function () {
					if ($(this).data('data') == null) {
						$(this).remove();
					}
				});
				var ini = p.$w.find('[name=fecini]').val(),
					fin = p.$w.find('[name=fecfin]').val();
				if (ini != '' && fin != '') {
					var fecini = new Date(ini),
						fecfin = new Date(fin),
						total_meses = (fecfin - fecini) / 1000 / 60 / 60 / 24 / 30,
						mes_ini = parseInt(fecini.getMonth()) + 1,
						ano_ini = fecini.getFullYear(),
						tmp_ano = 1;
					for (var i = (p.$w.find('[name=gridPag] tbody .item').length), j = total_meses; i < j; i++) {
						var $row = $('<tr class="item">');
						var mes = mes_ini + i,
							ano = ano_ini;
						if (mes >= 12) {
							mes = mes - (12 * tmp_ano);
							ano++;
							if (mes == 12) {
								mes = 0;
								ano_ini++;
								ano++;
								tmp_ano++;
							}
						}
						$row.append('<td>' + (i + 1) + '</td>');
						$row.append('<td>');
						var $cbo = $('<select name="mes">');
						for (var ii = 0; ii < ciHelper.meses.length; ii++) {
							$cbo.append('<option value="' + (ii + 1) + '">' + ciHelper.meses[ii] + '</option>');
						}
						$cbo.selectVal(mes + 1);
						$row.find('td:last').append($cbo);
						$row.find('select').selectVal('' + (mes + 1));
						$row.append('<td><input type="text" name="ano" value="' + ano + '" /></td>');
						$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
						$row.find('button:last').click(function () {
							$(this).closest('.item').remove();
							for (var i = 0, j = p.$w.find('[name=gridPag] tbody .item').length; i < j; i++) {
								var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
								$row.find('td:eq(0)').html(i + 1);
							}
						});
						p.$w.find('[name=gridPag] tbody').append($row);
						if (mes == 11) {
							mes = 12;
						}
						if ((mes == (parseInt(fecfin.getMonth()) + 1)) && (ano == (fecfin.getFullYear()))) {
							i = j;
							return 0;
						}
					}
				}
			}
		});
		new K.Panel({
			title: 'Editar Contrato',
			contentURL: 'in/movi/edit_cont',
			store: false,
			buttons: {
				'Guardar Contrato': {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							_id: p.id,
							titular: p.$w.find('[name=arrendatario] [name=mini_enti]').data('data'),
							aval: p.$w.find('[name=aval] [name=mini_enti]').data('data'),
							situacion: p.$w.find('[name=situacion] option:selected').val(),
							moneda: p.$w.find('[name=moneda] option:selected').val(),
							importe: p.$w.find('[name=importe]').val(),
							fecdes: p.$w.find('[name=fecdes]').val(),
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							motivo: p.$w.find('[name=motivo] option:selected').data('data'),
							contrato_dias: 0,
							desalojo: 0,
							odsd: 0,
							infocorp: 0,
							as_externa: 0,
							nro_contrato: p.$w.find('[name=nro_contrato]').val(),
							sedapar: p.$w.find('[name=sedapar]').val(),
							seal: p.$w.find('[name=seal]').val(),
							arbitrios: p.$w.find('[name=arbitrios]').val(),
							con_mora: p.$w.find('[name=con_mora] option:selected').val(),
							compensacion: p.$w.find('[name=compensacion] option:selected').val(),
							porcentaje: p.$w.find('[name=porcentaje_comp]').val(),
							pagos: []
						};
						if (data.titular == null) {
							p.$w.find('[name=arrendatario] [name=btnSel]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un titular!',
								type: 'error'
							});
						} else data.titular = mgEnti.dbRel(data.titular);
						if (data.aval == null) {
							/*p.$w.find('[name=btnAval]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un aval!',
								type: 'error'
							});*/
						} else data.aval = mgEnti.dbRel(data.aval);
						data.motivo = {
							_id: data.motivo._id.$id,
							nomb: data.motivo.nomb
						};
						if (data.importe == '') data.importe = 0;
						if (data.fecdes == null) {
							p.$w.find('[name=fecdes]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una fecha de desocupaci&oacute;n!',
								type: 'error'
							});
						}
						if (data.fecini == null) {
							p.$w.find('[name=fecini]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una fecha de inicio de vigencia!',
								type: 'error'
							});
						}
						if (data.fecfin == null) {
							p.$w.find('[name=fecfin]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar una fecha de fin de vigencia!',
								type: 'error'
							});
						}
						/*
						 * representantes
						 */
						for (var i = 0; i < p.$w.find('[name=gridRep] tbody .item').length; i++) {
							if (data.representantes == null)
								data.representantes = [];
							data.representantes.push(mgEnti.dbRel(p.$w.find('[name=gridRep] tbody .item').eq(i).data('data')));
						}
						/*
						 * GARANTIAS
						 */
						for (var i = 0; i < p.$w.find('[name=gridGar] tbody .item').length; i++) {
							if (data.garantias == null)
								data.garantias = [];
							data.garantias.push(p.$w.find('[name=gridGar] tbody .item').eq(i).data('data'));
						}
						/*
						 * Letras
						 */
						for (var i = 0; i < p.$w.find('[name=gridLet] tbody .item').length; i++) {
							if (data.letras == null)
								data.letras = [];
							data.letras.push(p.$w.find('[name=gridLet] tbody .item').eq(i).data('data'));
						}
						if (p.$w.find('[name=rbtnDias]').is(':checked'))
							data.contrato_dias = 1;
						if (p.$w.find('[name=rbtnDes]').is(':checked'))
							data.desalojo = 1;
						if (p.$w.find('[name=rbtnInf]').is(':checked'))
							data.infocorp = 1;
						if (p.$w.find('[name=rbtnOds]').is(':checked'))
							data.odsd = 1;
						if (p.$w.find('[name=rbtnAse]').is(':checked'))
							data.as_externa = 1;
						if (data.nro_contrato === '') {
							p.$w.find('[name=nro_contrato]').focus();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe escribir un numero de contrato!',
								type: 'error'
							});
						}
						/*
						 * PAGOS
						 */
						for (var i = 0; i < p.$w.find('[name=gridPag] tbody .item').length; i++) {
							if (data.pagos == null) {
								data.pagos = [];
							}
							var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
							if ($row.data('data') != null) {
								var tmp = $row.data('data');
								if (typeof tmp.comprobante != 'undefined') {
									tmp.comprobante._id = tmp.comprobante._id.$id;
								}
								if (typeof tmp.historico != 'undefined') {
									for (var ii = 0; ii < tmp.historico.length; ii++) {
										tmp.historico[ii].fec = ciHelper.date.format.bd_ymd(tmp.historico[ii].fec);
									}
								}
								if (typeof tmp.comprobantes != 'undefined') {
									for (var ii = 0; ii < tmp.comprobantes.length; ii++) {
										tmp.comprobantes[ii]._id = tmp.comprobantes[ii]._id.$id;
									}
								}
								data.pagos.push(tmp);
							} else {
								data.pagos.push({
									item: i + 1,
									mes: $row.find('[name=mes] option:selected').val(),
									ano: $row.find('[name=ano]').val()
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("in/movi/save_cont", data, function (result) {
							K.clearNoti();
							K.msg({
								title: ciHelper.titles.regiGua,
								text: "Contrato editado!"
							});
							inMovi.init({
								inmueble: inInmu.dbRel(p.inmueble)
							});
						}, 'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						inMovi.init({
							inmueble: inInmu.dbRel(p.inmueble)
						});
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block();
				/*
				 * ARRENDATARIO
				 */
				p.$w.find('[name=arrendatario] .panel-title').html('ARRENDATARIO');
				p.$w.find('[name=arrendatario] [name=btnSel]').click(function () {
					mgEnti.windowSelect({
						callback: function (data) {
							mgEnti.fillMini(p.$w.find('[name=arrendatario] [name=mini_enti]'), data);
						},
						bootstrap: true
					});
				});
				p.$w.find('[name=arrendatario] [name=btnAct]').click(function () {
					if (p.$w.find('[name=arrendatario] [name=mini_enti]').data('data') == null) {
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					} else {
						mgEnti.windowEdit({
							callback: function (data) {
								mgEnti.fillMini(p.$w.find('[name=arrendatario] [name=mini_enti]'), data);
							},
							id: p.$w.find('[name=arrendatario] [name=mini_enti]').data('data')._id.$id
						});
					}
				});
				/*
				 * AVAL
				 */
				p.$w.find('[name=aval] .panel-title').html('AVAL');
				p.$w.find('[name=aval] [name=btnSel]').click(function () {
					mgEnti.windowSelect({
						callback: function (data) {
							mgEnti.fillMini(p.$w.find('[name=aval] [name=mini_enti]'), data);
						},
						bootstrap: true
					});
				});
				p.$w.find('[name=aval] [name=btnEli]').click(function () {
					p.$w.find('[name=aval] [name=mini_enti] tr').each(function () {
						$(this).find('td:eq(1)').html('');
					});
					p.$w.find('[name=aval] [name=mini_enti]').removeData('data');
				}).show();
				p.$w.find('[name=aval] [name=btnAct]').click(function () {
					if (p.$w.find('[name=aval] [name=mini_enti]').data('data') == null) {
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					} else {
						mgEnti.windowEdit({
							callback: function (data) {
								mgEnti.fillMini(p.$w.find('[name=aval] [name=mini_enti]'), data);
							},
							id: p.$w.find('[name=aval] [name=mini_enti]').data('data')._id.$id
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridRep]'),
					search: false,
					pagination: false,
					cols: ['Apellidos y Nombre', 'Documento', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgr"><i class="fa fa-user"></i> Agregar Representante Legal</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgr]').click(function () {
							mgEnti.windowSelect({
								callback: function (data) {
									if (p.$w.find('[name=rep' + data._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="rep' + data._id.$id + '">');
										$row.append('<td>' + mgEnti.formatName(data) + '</td>');
										$row.append('<td>' + mgEnti.formatIden(data) + '</td>');
										$row.append('<td><button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('[name=btnEli]').click(function () {
											$(this).closest('tr').remove();
										});
										$row.data('data', data);
										p.$w.find('[name=gridRep] tbody').append($row);
									} else {
										K.msg({
											title: ciHelper.titles.infoReq,
											text: 'La entidad seleccionada ya fue agregada!',
											type: 'error'
										});
									}
								},
								bootstrap: true
							});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridGar]'),
					search: false,
					pagination: false,
					cols: ['Tip. Doc.', 'Nro', 'Fecha', 'Importe', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgr"><i class="fa fa-file-text-o"></i> Agregar Garant&iacute;a</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgr]').click(function () {
							inMovi.modalGarantia({
								callback: p.cbGarantia
							});
						});
					}
				});
				var $cbo = p.$w.find('[name=situacion]');
				for (var i = 0; i < inMovi.situacion.length; i++) {
					$cbo.append('<option value="' + inMovi.situacion[i].cod + '">' + inMovi.situacion[i].descr + '</option>');
				}
				new K.grid({
					$el: p.$w.find('[name=gridLet]'),
					search: false,
					pagination: false,
					cols: ['', 'Fecha', 'Protesto - Fecha de Pago', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary" type="button" name="btnAgr"><i class="fa fa-money"></i> Agregar Letra</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgr]').click(function () {
							inMovi.modalLetra({
								callback: p.cbLetra
							});
						});
					}
				});
				p.$w.find('[name=fecdes]').datepicker({
					format: 'yyyy-mm-dd'
				});
				p.$w.find('[name=fecini]').datepicker({
						format: 'yyyy-mm-dd'
					})
					.on('changeDate', function (ev) {
						p.calcPag();
					}).change(function () {
						p.calcPag();
					});
				p.$w.find('[name=fecfin]').datepicker({
						format: 'yyyy-mm-dd'
					})
					.on('changeDate', function (ev) {
						p.calcPag();
					}).change(function () {
						p.calcPag();
					});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['', 'Mes', 'A&ntilde;o', ''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-success"><i class="fa fa-plus"></i> Agregar Pago</button>',
					onContentLoaded: function ($el) {
						$el.find('button').click(function () {
							var $row = $('<tr class="item">');
							$row.append('<td>' + (p.$w.find('[name=gridPag] tbody .item').length + 1) + '</td>');
							$row.append('<td>');
							var $cbo = $('<select name="mes">');
							for (var i = 0; i < ciHelper.meses.length; i++) {
								$cbo.append('<option value="' + (i + 1) + '">' + ciHelper.meses[i] + '</option>');
							}
							$row.find('td:last').append($cbo);
							$row.append('<td><input type="text" name="ano" value="' + ciHelper.date.getYear() + '" /></td>');
							$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button:last').click(function () {
								$(this).closest('.item').remove();
								for (var i = 0, j = p.$w.find('[name=gridPag] tbody .item').length; i < j; i++) {
									var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
									$row.find('td:eq(0)').html(i + 1);
								}
							});
							p.$w.find('[name=gridPag] tbody').append($row);
						});
					}
				});
				p.$w.find('[name=compensacion]').change(function(){
					p.$w.find('[name=porcentaje_comp]').val(0);
					if($(this).val()=='1'){
						p.$w.find('[name=porcentaje_comp]').closest('.col-sm-4').show();
					}else
						p.$w.find('[name=porcentaje_comp]').closest('.col-sm-4').hide();
				}).change();
				$.post('in/moti/all',function(data){
					var $cbo = p.$w.find('[name=motivo]');
					if(data!=null)
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					else{
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe crear primero MOTIVOS DE CONTRATO',
							type: 'error'
						});
						return inMovi.init();
					}
					$.post('in/movi/get_cont',{_id: p.id},function(data){
						p.inmueble = data.inmueble;
						p.$w.find('[name=tipo]').html(p.inmueble.tipo.nomb);
						p.$w.find('[name=sublocal]').html(p.inmueble.sublocal.nomb);
						p.$w.find('[name=inmueble]').html(p.inmueble.direccion);
						K.TitleBar({title: 'Contrato de '+p.inmueble.direccion});
						mgEnti.fillMini(p.$w.find('[name=arrendatario] [name=mini_enti]'),data.titular);
						mgEnti.fillMini(p.$w.find('[name=aval] [name=mini_enti]'),data.aval);
						p.$w.find('[name=situacion]').selectVal(data.situacion);
						p.$w.find('[name=moneda]').selectVal(data.moneda);
						p.$w.find('[name=importe]').val(data.importe);
						p.$w.find('[name=fecdes]').val(ciHelper.date.format.bd_ymd(data.fecdes));
						p.$w.find('[name=fecini]').val(ciHelper.date.format.bd_ymd(data.fecini));
						p.$w.find('[name=fecfin]').val(ciHelper.date.format.bd_ymd(data.fecfin));
						p.$w.find('[name=motivo]').selectVal(data.motivo._id.$id);
						p.$w.find('[name=nro_contrato]').val(data.nro_contrato);
						p.$w.find('[name=sedapar]').val(data.sedapar);
						p.$w.find('[name=seal]').val(data.seal);
						p.$w.find('[name=arbitrios]').val(data.arbitrios);
						p.$w.find('[name=con_mora]').selectVal(data.con_mora);
						p.$w.find('[name=compensacion]').selectVal(data.compensacion);
						p.$w.find('[name=porcentaje_comp]').val(data.porcentaje);
						if(data.contrato_dias=='1')
							p.$w.find('[name=rbtnDias]').attr('checked','checked');
						if(data.desalojo=='1')
							p.$w.find('[name=desalojo]').attr('checked','checked');
						if(data.odsd=='1')
							p.$w.find('[name=odsd]').attr('checked','checked');
						if(data.infocorp=='1')
							p.$w.find('[name=infocorp]').attr('checked','checked');
						if(data.as_externa=='1')
							p.$w.find('[name=as_externa]').attr('checked','checked');
						if(data.representantes!=null){
							for(var i=0; i<data.representantes.length; i++){
								var $row = $('<tr class="item" name="rep'+data.representantes[i]._id.$id+'">');
								$row.append('<td>'+mgEnti.formatName(data.representantes[i])+'</td>');
								$row.append('<td>'+mgEnti.formatIden(data.representantes[i])+'</td>');
								$row.append('<td><button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('tr').remove();
								});
								$row.data('data',data.representantes[i]);
								p.$w.find('[name=gridRep] tbody').append($row);
							}
						}












						if(data.garantias!=null){
							for(var i=0; i<data.garantias.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+inMovi.tipo_doc_gar[data.garantias[i].tipo]+'</td>');
								$row.append('<td>'+data.garantias[i].num+'</td>');
								$row.append('<td>'+ciHelper.date.format.bd_ymd(data.garantias[i].fec)+'</td>');
								$row.append('<td>'+ciHelper.formatMon(data.garantias[i].importe,data.garantias[i].moneda)+'</td>');
								$row.append('<td>'+
									'<button name="btnEdi" type="button" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></button>'+
									'<button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>'+
									'</td>');
								$row.find('[name=btnEdi]').click(function(){
									inMovi.modalGarantia({
										callback: p.cbGarantia,
										data: $(this).closest('.item').data('data'),
										$row: $(this).closest('.item')
									});
								});
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('tr').remove();
								});
								data.garantias[i].fec = ciHelper.date.format.bd_ymd(data.garantias[i].fec);
								if(data.garantias[i].traslados!=null){
									for(var j=0; j<data.garantias[i].traslados.length; j++){
										data.garantias[i].traslados[j].contrato_anterior = data.garantias[i].traslados[j].contrato_anterior.$id;
									}
								}
								if(data.garantias[i].historia!=null){
									for(var j=0; j<data.garantias[i].historia.length; j++){
										data.garantias[i].historia[j]= data.garantias[i].historia[j].$id;
									}
								}
								$row.data('data',data.garantias[i]);
								p.$w.find('[name=gridGar] tbody').append($row);
							}
						}












						if(data.letras!=null){
							for(var i=0; i<data.letras.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>');
								$row.append('<td>'+data.letras[i].fecven+'</td>');
								$row.append('<td>');
								$row.append('<td>'+
									'<button name="btnEdi" type="button" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></button>'+
									'<button name="btnEli" type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></button>'+
									'</td>');
								$row.find('[name=btnEdi]').click(function(){
									inMovi.modalLetra({
										callback: p.cbLetra,
										data: $(this).closest('.item').data('data'),
										$row: $(this).closest('.item')
									});
								});
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('tr').remove();
								});
								$row.data('data',data.letras[i]);
								p.$w.find('[name=gridLet] tbody').append($row);
							}
						}
						if(data.pagos!=null){
							for(var i=0; i<data.pagos.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+(i+1)+'</td>');
								$row.append('<td>');
								var $cbo = $('<select name="mes">');
								for(var ii=0; ii<ciHelper.meses.length; ii++){
									$cbo.append('<option value="'+(ii+1)+'">'+ciHelper.meses[ii]+'</option>');
								}
								$row.find('td:last').append($cbo);
								$row.find('[name=mes]').selectVal(data.pagos[i].mes);
								$row.append('<td><input type="text" name="ano" value="'+data.pagos[i].ano+'" /></td>');
								if(data.pagos[i].estado==null&&data.pagos[i].historico==null){
									$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
									$row.find('button:last').click(function(){
										$(this).closest('.item').remove();
										for(var i=0,j=p.$w.find('[name=gridPag] tbody .item').length; i<j; i++){
											var $row = p.$w.find('[name=gridPag] tbody .item').eq(i);
											$row.find('td:eq(0)').html(i+1);
										}
									});
								}else{
									$row.find('input').attr('disabled','disabled');
									$row.find('select').attr('disabled','disabled');
									$row.append('<td>'+inMovi.estado_pago[data.pagos[i].estado].label+'</td>');
									if(data.pagos[i].comprobante!=null){
										$row.find('td:last').append('<br /><button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> '+data.pagos[i].comprobante.tipo+' '+data.pagos[i].comprobante.serie+'-'+data.pagos[i].comprobante.num+'</button>');
										$row.data('id',data.pagos[i].comprobante._id.$id);
										$row.find('button').click(function(){
											var $row = $(this).closest('.item');
											K.windowPrint({
												id:'windowPrint',
												title: "Comprobante de Pago",
												url: "in/comp/print?_id="+$row.data('id')
											});
										});
									}else if(data.pagos[i].historico!=null){
										for(var ii=0; ii<data.pagos[i].historico.length; ii++){
											if($row.find('td:last').html()!=''){
												$row.find('td:last').append('<br />');
											}
											$row.find('td:last').append('<i style="color:blue;">'+data.pagos[i].historico[ii].tipo+' '+data.pagos[i].historico[ii].num+
												'</i> por '+ciHelper.formatMon(data.pagos[i].historico[ii].total,data.pagos[i].historico[ii].moneda)+
												' del <i style="color:green;">'+ciHelper.date.format.bd_ymd(data.pagos[i].historico[ii].fec)+'</i>');
										}
									}
									$row.data('data',data.pagos[i]);
								}
								p.$w.find('[name=gridPag] tbody').append($row);
							}
						}
						p.$w.find('.i-checks').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green'
						});
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	/**************************************************************************************************************************
	*
	* CREACION DE NUEVA CUENTA POR COBRAR
	*
	**************************************************************************************************************************/
	newCuenta: function(p){
		if(p==null) p = {};
		$.extend(p,{
			loadConc: function(){
				K.block();
				var variables,SERV={},__VALUE__=0,cuotas=0;
				SERV = {
					FECVEN: 0
				};
				if(p.$w.find('[name=fecven]').val()==''){
					p.$w.find('[name=fecven]').focus();
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'Debe seleccionar una fecha de vencimiento!',
						type: 'error'
					});
				}
				var dateString = p.$w.find('[name=fecven]').val();
				var date_fecven = new Date(dateString.substring(0,4), (dateString.substring(5,7))-1, dateString.substring(8,10));
				SERV.FECVEN = ciHelper.date.diffDays(new Date(),date_fecven);
				if(SERV.FECVEN<0) SERV.FECVEN = 0;
				variables = p.$w.data('vars');
				if(variables==null){
					return K.msg({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
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
				var $table = p.$w.find('[name=gridConc]');
				$table.find("tbody").empty();
				servicio = p.$w.find('[name^=serv]').data('data');
				conceptos = p.$w.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.msg({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					var $row = $('<tr class="item" name="'+conceptos[i]._id.$id+'">');
					var monto = eval(conceptos[i].formula);
					$row.append('<td>'+conceptos[i].nomb+'</td>');
					if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
						eval('var '+conceptos[i].cod+' = 0;');
						var formula = conceptos[i].formula;
						formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
						$row.append('<td><input type="text" size="7" name="codform'+conceptos[i].cod+'"></td>');
						$row.find('[name^=codform]').val(0).change(function(){
							var val = parseFloat($(this).val()),
							formula = $(this).data('form'),
							cod = $(this).data('cod'),
							$row = $(this).closest('.item');
							eval("var __VALUE"+cod+"__ = "+val+";");
							var monto = eval(formula);
							$row.find('td:eq(2)').html(ciHelper.formatMon(monto));
							eval("var "+cod+" = "+monto+";");
							$row.data('monto',monto);
							for(var ii=0,jj=conceptos.length; ii<jj; ii++){
								var $table = p.$w.find('[name=gridConc]'),
								$row = $table.find('tbody .item').eq(ii),
								$cell = $row.find('li').eq(2),
								monto = eval($cell.data('formula'));
								if($cell.data('formula')!=null){
									$cell.html(ciHelper.formatMon(monto));
									$row.data('monto',monto);
								}
							}
							p.calcConc();
						}).data('form',formula).data('cod',conceptos[i].cod);
						$row.append('<td>');
					}else{
						$row.append('<td>');
						$row.append('<td>');
						$row.find('td:eq(2)').data('formula',conceptos[i].formula);
					}
					$row.find('td:eq(2)').html(ciHelper.formatMon(monto));
					$row.data('monto',monto);
					console.log(monto);
					$table.find("tbody").append( $row );
				}
				p.calcConc();
			},
			calcConc: function(){
				var $table, servicio, conceptos, total = 0, cuotas=0;
				$table = p.$w.find('[name=gridConc]');
				servicio = p.$w.find('[name^=serv]').data('data');
				conceptos = p.$w.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.msg({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					total += parseFloat($table.find('.item').eq(i).data('monto'));
				}
				if(conceptos.length!=$table.find('.item').length){
					$table.find('.item:last').remove();
				}
				var $row = $('<tr class="item">');
				$row.append('<td>');
				$row.append('<td>Total</td>');
				$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
				$row.data('total',total);
				$table.find("tbody").append( $row );
				K.unblock();
			}
		});
		new K.Panel({
			contentURL: 'in/movi/cuenta',
			store: false,
			buttons: {
				'Crear Cuenta por Cobrar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'IN',
							contrato: p.contrato._id.$id,
							inmueble: inInmu.dbRel(p.contrato.inmueble),
							cliente: mgEnti.dbRel(p.contrato.titular),
							servicio: p.$w.find('[name^=serv]').data('data'),
							fecven: p.$w.find('[name=fecven]').val(),
							observ: p.$w.find('[name=observ]').val(),
							conceptos: [],
				            total: parseFloat(p.$w.find('.item:last').data('total')),
				            saldo: parseFloat(p.$w.find('.item:last').data('total')),
				            moneda: 'S'
						};
						if(data.servicio==null){
							p.$w.find('[name=btnServ]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un servicio!',type: 'error'});
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
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
						}
				        var $table = p.$w.find('[name=gridConc]'),
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
				            tmp.monto = parseFloat($table.find('.item').eq(i).data('monto'));
				            tmp.saldo = tmp.monto;
				            data.conceptos.push(tmp);
				        }
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/cuen/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.msg({title: ciHelper.titles.regiGua,text: 'La Cuenta por cobrar fue registrada con &eacute;xito!'});
							inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
						});
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=cliente]').html(mgEnti.formatName(p.contrato.titular)).data('data',p.contrato.titular);
				p.$w.find('[name=fecven]').val(ciHelper.date.get.now_ymd()).change(function(){
					p.loadConc();
				}).datepicker({format: 'yyyy-mm-dd'}).on('changeDate',function(e){
					p.loadConc();
				});
				p.$w.find('[name=btnServ]').click(function(){
					mgServ.windowSelect({callback: function(data){
						p.$w.find('[name=servicio]').html('').removeData('data');
						p.$w.find('[name=gridConc] tbody').empty();
						K.block();
						$.post('cj/conc/get_serv',{id:data._id.$id},function(concs){
							if(concs.serv==null){
								return K.msg({
									title: 'Servicio inv&aacute;lido',
									text: 'El servicio seleccionado no tiene conceptos asociados!',
									type: 'error'
								});
							}
							p.$w.data('vars',concs.vars);
							p.$w.find('[name=servicio]').html(data.nomb).data('data',data).data('concs',concs.serv);
							p.loadConc();
						},'json');
					},bootstrap: true,modulo: 'IN'});
				}).click();
				new K.grid({
					$el: p.$w.find('[name=gridConc]'),
					search: false,
					pagination: false,
					cols: ['Concepto','','Precio'],
					onlyHtml: true
				});
			}
		});
	},
	newCuentaWindow: function(p){
		if(p==null) p = {};
		console.log(p);
		$.extend(p,{
			loadConc: function(){
				K.block();
				var variables,SERV={},__VALUE__=0,cuotas=0;
				SERV = {
					FECVEN: 0
				};
				if(p.$w.find('[name=fecven]').val()==''){
					p.$w.find('[name=fecven]').focus();
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'Debe seleccionar una fecha de vencimiento!',
						type: 'error'
					});
				}
				var dateString = p.$w.find('[name=fecven]').val();
				var date_fecven = new Date(dateString.substring(0,4), (dateString.substring(5,7))-1, dateString.substring(8,10));
				SERV.FECVEN = ciHelper.date.diffDays(new Date(),date_fecven);
				if(SERV.FECVEN<0) SERV.FECVEN = 0;
				variables = p.$w.data('vars');
				if(variables==null){
					return K.msg({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
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
				var $table = p.$w.find('[name=gridConc]');
				$table.find("tbody").empty();
				servicio = p.$w.find('[name^=serv]').data('data');
				conceptos = p.$w.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.msg({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					var $row = $('<tr class="item" name="'+conceptos[i]._id.$id+'">');
					var monto = eval(conceptos[i].formula);
					$row.append('<td>'+conceptos[i].nomb+'</td>');
					if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
						eval('var '+conceptos[i].cod+' = 0;');
						var formula = conceptos[i].formula;
						formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
						$row.append('<td><input type="text" size="7" name="codform'+conceptos[i].cod+'"></td>');
						$row.find('[name^=codform]').val(0).change(function(){
							var val = parseFloat($(this).val()),
							formula = $(this).data('form'),
							cod = $(this).data('cod'),
							$row = $(this).closest('.item');
							eval("var __VALUE"+cod+"__ = "+val+";");
							var monto = eval(formula);
							$row.find('td:eq(2)').html(ciHelper.formatMon(monto));
							eval("var "+cod+" = "+monto+";");
							$row.data('monto',monto);
							for(var ii=0,jj=conceptos.length; ii<jj; ii++){
								var $table = p.$w.find('[name=gridConc]'),
								$row = $table.find('tbody .item').eq(ii),
								$cell = $row.find('li').eq(2),
								monto = eval($cell.data('formula'));
								if($cell.data('formula')!=null){
									$cell.html(ciHelper.formatMon(monto));
									$row.data('monto',monto);
								}
							}
							p.calcConc();
						}).data('form',formula).data('cod',conceptos[i].cod);
						$row.append('<td>');
					}else{
						$row.append('<td>');
						$row.append('<td>');
						$row.find('td:eq(2)').data('formula',conceptos[i].formula);
					}
					$row.find('td:eq(2)').html(ciHelper.formatMon(monto));
					$row.data('monto',monto);
					console.log(monto);
					$table.find("tbody").append( $row );
				}
				p.calcConc();
			},
			calcConc: function(){
				var $table, servicio, conceptos, total = 0, cuotas=0;
				$table = p.$w.find('[name=gridConc]');
				servicio = p.$w.find('[name^=serv]').data('data');
				conceptos = p.$w.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.msg({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					total += parseFloat($table.find('.item').eq(i).data('monto'));
				}
				if(conceptos.length!=$table.find('.item').length){
					$table.find('.item:last').remove();
				}
				var $row = $('<tr class="item">');
				$row.append('<td>');
				$row.append('<td>Total</td>');
				$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
				$row.data('total',total);
				$table.find("tbody").append( $row );
				K.unblock();
			}
		});
		new K.Window({
			id:'newCuentaWindow',
			title:'Nueva Cuenta por Cobrar',
			contentURL: 'in/movi/cuenta',
			store: false,
			width:720,
			height:520,
			buttons: {
				'Crear Cuenta por Cobrar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'IN',
							contrato: p.contrato._id.$id,
							inmueble: inInmu.dbRel(p.contrato.inmueble),
							cliente: mgEnti.dbRel(p.contrato.titular),
							servicio: p.$w.find('[name^=serv]').data('data'),
							fecven: p.$w.find('[name=fecven]').val(),
							observ: p.$w.find('[name=observ]').val(),
							conceptos: [],
				            total: parseFloat(p.$w.find('.item:last').data('total')),
				            saldo: parseFloat(p.$w.find('.item:last').data('total')),
				            moneda: 'S'
						};
						if(data.servicio==null){
							p.$w.find('[name=btnServ]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un servicio!',type: 'error'});
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
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
						}
				        var $table = p.$w.find('[name=gridConc]'),
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
				            tmp.monto = parseFloat($table.find('.item').eq(i).data('monto'));
				            tmp.saldo = tmp.monto;
				            data.conceptos.push(tmp);
				        }
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/cuen/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.msg({title: ciHelper.titles.regiGua,text: 'La Cuenta por cobrar fue registrada con &eacute;xito!'});
							//inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
							p.callback();
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
				p.$w = $('#newCuentaWindow');
				p.$w.find('[name=cliente]').html(mgEnti.formatName(p.contrato.titular)).data('data',p.contrato.titular);
				p.$w.find('[name=fecven]').val(ciHelper.date.get.now_ymd()).change(function(){
					p.loadConc();
				}).datepicker({format: 'yyyy-mm-dd'}).on('changeDate',function(e){
					p.loadConc();
				});
				p.$w.find('[name=btnServ]').click(function(){
					mgServ.windowSelect({callback: function(data){
						p.$w.find('[name=servicio]').html('').removeData('data');
						p.$w.find('[name=gridConc] tbody').empty();
						K.block();
						$.post('cj/conc/get_serv',{id:data._id.$id},function(concs){
							if(concs.serv==null){
								return K.msg({
									title: 'Servicio inv&aacute;lido',
									text: 'El servicio seleccionado no tiene conceptos asociados!',
									type: 'error'
								});
							}
							p.$w.data('vars',concs.vars);
							p.$w.find('[name=servicio]').html(data.nomb).data('data',data).data('concs',concs.serv);
							p.loadConc();
						},'json');
					},bootstrap: true,modulo: 'IN'});
				}).click();
				new K.grid({
					$el: p.$w.find('[name=gridConc]'),
					search: false,
					pagination: false,
					cols: ['Concepto','','Precio'],
					onlyHtml: true
				});
			}
		});
	},
	/**************************************************************************************************************************
	*
	* EMISION DE COMPROBANTE Y PAGO DE CUOTA MENSUAL
	*
	**************************************************************************************************************************/
	newMes: function(p){
		//console.log(JSON.stringify(p, undefined, 2));
		$.extend(p,{
			moneda: p.contrato.moneda,
			renta: parseFloat(p.contrato.importe),
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.$w.find('[name=tasa]').val(),
				total_row = 0;
				if(tasa=='') tasa = p.tc;
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=serv]').length; i<j; i++){
					var renta = parseFloat(p.$w.find('[name=gridServ] [name^=conc_renta]').eq(i).data('monto'));
					total += renta;
					total_row += renta;
					//total += parseFloat(K.round(parseFloat(K.round((p.igv),2))*renta,2));
					//total_row += parseFloat(K.round(parseFloat(K.round((p.igv),2))*renta,2));
					//IGV (INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad)
					if(p.contrato.motivo._id.$id=='55316553bc795ba801000035'){
						total += parseFloat(K.round(0,2));
						total_row += parseFloat(K.round(0,2));
					}
					else{
						total += parseFloat(K.round(parseFloat(K.round((p.igv),2))*renta,2));
						total_row += parseFloat(K.round(parseFloat(K.round((p.igv),2))*renta,2));
					}
					if(p.contrato.con_mora=='1'){
						var mora = (parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').eq(i).val())/100)*renta;
						mora = parseFloat(K.round(mora,2));
						p.$w.find('[name=gridServ] tbody [name^=conc_mora]').eq(i).find('td:eq(3)').html(ciHelper.formatMon(mora,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_mora]').eq(i).data('monto',mora);
						total += mora;
						total_row += mora;
					}
					p.$w.find('[name=gridServ] tbody [name^=serv]').eq(i).find('td:eq(3)').html('<b>'+ciHelper.formatMon(total_row,p.moneda)+'</b>');
					total_row = 0;
				}
				total = K.round(total,2);
				p.$w.find('[name=totalGrid]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				if(p.total>700){
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
					p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion)+'</p>');
				}else{
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
				}
				p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(p.total,2));
				if(p.total>=700){
					var tmp_efec = total - (total*p.detraccion),
					tmp_detr = total*p.detraccion;
					if(p.$w.find('[name=comp] option:selected').val()=='F'){
						p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
						p.$w.find('[name=gridForm] [name=voucher]:eq(1)').val('XXX');
						p.$w.find('[name=gridForm] [name=tot]:eq(3)').val(K.round(tmp_detr,2));
					}else{
						p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(total,2));
						p.$w.find('[name=gridForm] [name=voucher]:eq(1)').val('');
						p.$w.find('[name=gridForm] [name=tot]:eq(3)').val(K.round(0,2));
					}
				}
				p.$w.find('[name=gridForm] [name=tot]').keyup();
			},
			save: function(){
				var data = {
					modulo: 'IN',
					contrato: p.contrato._id.$id,
					inmueble: {
						_id: p.contrato.inmueble._id.$id,
						direccion: p.contrato.inmueble.direccion
					},
					alquiler: true,
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
					dia_ini: ciHelper.date.format.bd_d(p.contrato.fecini),
					total: parseFloat(p.$w.find('[name=totalGrid]').data('monto')),
					sunat: {
						codigo:"",
	                    descr: p.$w.find('[name=texto_pago]').val(),
	                    cod_unidad:"NIU",
	                    unidad:"number of international units",
	                    precio_unitario:0,
	                    importe_total:0,
	                    valor_unitario:0,
	                    base:0,
	                    ope_inafectas:0,
	                    ope_gravadas:0,
	                    inafecto:false,
	                    cant:p.pagos.length,
	                    exonerada:false,
	                    gratuito:false,
	                    igv:"0.18",
	                    isc:"0.00",
	                    otros_imp:"0.00",
	                    ope_exonerada:"0.00",
	                    ope_gratuitas:"0.00",
	                    desc:"0.00",
	                    meta: item
	                }
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
					return K.msg({
						title: ciHelper.titles.infoReq,
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
				for(var i=0,j=p.pagos.length; i<j; i++){
					var item = {
						dia_ini: ciHelper.date.format.bd_d(p.contrato.fecini),
						pago: p.pagos[i],
						conceptos: []
					},tmp_cuenta = p.cuenta;
					//COBRANZA DUDOSA
					if(inMovi.get_cobranza_dudosa(p.pagos[i].mes,p.pagos[i].ano)==true){
						tmp_cuenta = p.cuenta_dudosa;
					}
					//					//ALQUILER
					//					var concepto = {
					//						concepto: p.$w.find('[name=serv'+i+'] td:eq(1)').html(),
					//						monto: parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)),
					//						cuenta: {
					//							_id: tmp_cuenta._id.$id,
					//							descr: tmp_cuenta.descr,
					//							cod: tmp_cuenta.cod
					//						}
					//					};
					//ALQUILER
					//IGV (PENALIDADES RECIBO DE INGRESO ya que se considera individuales por C.I. Nº784-2017-SBPA-OGA por penalidad)
					if(p.contrato.motivo._id.$id=='55316553bc795ba801000035'){
						var concepto = {
							concepto: p.$w.find('[name=serv'+i+'] td:eq(1)').html(),
							monto: parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)),
							cuenta: {
								_id: '5335a95bee6f96dc10000012',
								descr: 'penalidades',
								cod: '1202.0902.47.02'
							}
						};
					}
					else
					{
						var concepto = {
							concepto: p.$w.find('[name=serv'+i+'] td:eq(1)').html(),
							monto: parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)),
							cuenta: {
							_id: tmp_cuenta._id.$id,
								descr: tmp_cuenta.descr,
								cod: tmp_cuenta.cod
							}
						};
					}
					item.conceptos.push(concepto);
					//IGV
					var concepto = {
						concepto: 'IGV ('+((p.igv)*100)+'%)',
						monto: parseFloat(K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2)),
						cuenta: {
							_id: p.conf.IGV._id.$id,
							descr: p.conf.IGV.descr,
							cod: p.conf.IGV.cod
						}
					};
					item.conceptos.push(concepto);
					//MORA
					if(p.contrato.con_mora=='1'){
						var concepto = {
							concepto: 'Moras ('+p.$w.find('[name=conc_mora_'+i+'] [name=mora]').val()+'%)',
							monto: parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2)),
							cuenta: {
								_id: p.conf.MOR._id.$id,
								descr: p.conf.MOR.descr,
								cod: p.conf.MOR.cod
							}
						};
						item.conceptos.push(concepto);
					}
					data.items.push(item);
					//					console.log("ope_inafectas antes");
					//					console.log((p.$w.find('[name=conc_mora_'+i+']').data('monto')));
					//					console.log(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2));
					//					console.log(parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2)));
					//					console.log("importe_total antes");
					//					console.log((p.$w.find('[name=conc_renta_'+i+']').data('monto')));
					//					console.log((p.$w.find('[name=conc_igv_'+i+']').data('monto')));
					//					console.log((p.$w.find('[name=conc_renta_'+i+']').data('monto'))+(p.$w.find('[name=conc_igv_'+i+']').data('monto')));
					//					console.log(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)+K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2));
					//					console.log(parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2))+parseFloat(K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2)));
					//					console.log(parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2))+parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2)));

					data.sunat.precio_unitario = parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2));
	                //data.sunat.importe_total += parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2));
	                data.sunat.valor_unitario = parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2));
	                data.sunat.ope_gravadas += parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2));
	                //data.sunat.ope_inafectas += parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2));

	                /*solo cuando no haya mora, no agregue el campo de operaciones inafectas*/
					if(p.contrato.con_mora=='1'){
	                data.sunat.importe_total += parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2));
	                data.sunat.ope_inafectas += parseFloat(K.round(p.$w.find('[name=conc_mora_'+i+']').data('monto'),2));
	            	}
	            	else{
						data.sunat.importe_total += parseFloat(K.round(p.$w.find('[name=conc_renta_'+i+']').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name=conc_igv_'+i+']').data('monto'),2));
	            	}

	                if(data.sunat.ope_inafectas!=0){
	                	data.sunat.inafecto = true;
	                }

					//	                console.log("ope_inafectas variable");
					//					console.log(data.sunat.ope_inafectas);
					//					console.log("importe_total variable");
					//					console.log(data.sunat.importe_total);

				}
				if(parseFloat(data.total)==0){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El comprobante no puede tener como total 0!',
						type: 'error'
					});
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El total del comprobante no coincide con el total de la forma de pagar!',
						type: 'error'
					});
				}
				data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
				data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
				data.sunat = [data.sunat];
				K.sendingInfo();
				p.$w.find('#div_buttons button').attr('disabled','disabled');
				$.post('in/comp/save_comp',data,function(comp){
				//$.post('in/comp/save',data,function(){
					K.clearNoti();
					inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
					K.msg({title: ciHelper.titles.regiGua,text: 'Comprobante creado con &eacute;xito!'});
					/*
					 * AQUI ABRIR EL PDF
					 */
					K.windowPrint({
						id:'windowPrint',
						title: "Comprobante de Pago",
						url: "in/comp/print?print=1&_id="+comp._id.$id
					});
				},'json');
			},
			get_glosa: function(){
				/**********************************************************************************************************
				*<!-- TEXTO DE COMPROBANTE
				**********************************************************************************************************/
				var tmp_text_inmu = "INMUEBLE: ";
				if(p.contrato.inmueble.tipo.nomb=='EX NUEVO MILENIO'){
					tmp_text_inmu = "TERRENO: ";
				}
				if(p.contrato.inmueble.tipo.nomb=='LA CHOCITA'){
					tmp_text_inmu = "ESPACIO: ";
				}
				if(p.contrato.inmueble.tipo.nomb=='VARIOS'){
					tmp_text_inmu = "POR OCUPACION ";
				}
				p.$w.find('[name=texto_inmueble]').val(tmp_text_inmu+p.contrato.inmueble.direccion.toUpperCase()+' ('+p.contrato.inmueble.tipo.nomb.toUpperCase()+')');
				if(p.pagos.length==1){
					p.$w.find('[name=texto_pago]').val(p.$w.find('[name=serv0] td:eq(1)').html());
				}else{
					var texto_pago = inMovi.get_motivo(p.contrato.motivo._id.$id)+' DE ';
					dia_ini_tmp = ciHelper.date.format.bd_d(p.contrato.fecini),
					fin_pag = p.pagos.length-1;
					p.dia_ini = dia_ini_tmp;
					if(parseInt(dia_ini_tmp)==1){
						texto_pago += ciHelper.meses[parseInt(p.pagos[0].mes)-1].toUpperCase()+' - '+p.pagos[0].ano;
						texto_pago += ' A ';
						texto_pago += ciHelper.meses[parseInt(p.pagos[fin_pag].mes)-1].toUpperCase()+' - '+p.pagos[fin_pag].ano;
					}else{
						var mes_n = parseInt(p.pagos[0].mes)-1,
						ano_n = parseInt(p.pagos[0].ano);
						mes_n--;
						if(mes_n==-1){
							mes_n = 11;
							ano_n--;
						}
						var tmp_ini = dia_ini_tmp+' DE '+ciHelper.meses[mes_n].toUpperCase()+'-'+ano_n,
						tmp_fin = 'AL '+(parseInt(dia_ini_tmp)-1)+' DE '+ciHelper.meses[parseInt(p.pagos[fin_pag].mes)-1].toUpperCase()+'-'+p.pagos[fin_pag].ano;
						texto_pago += tmp_ini+' '+tmp_fin;
					}
					if(p.$w.find('[name=comp] option:selected').val()=='F'){
						texto_pago += ' C/M '+K.round(p.renta,2);
					}else{
						texto_pago += ' C/M '+K.round( p.renta+(p.igv)*p.renta ,2);
					}
					p.$w.find('[name=texto_pago]').val(texto_pago);
				}
				/**********************************************************************************************************
				* TEXTO DE COMPROBANTE -->
				**********************************************************************************************************/
			}
		});
		new K.Panel({
			contentURL: 'in/movi/comp_mes',
			buttons: {
				'Generar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						if(p.total>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
							var detrac = p.total*p.detraccion;
							ciHelper.confirm(
								'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
								function () {
									p.save();
								},
								function () { $.noop(); }
							);
						}else{
							p.save();
						}
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
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
					cols: ['N&deg;','Servicios','','Importe Total'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				$.post('in/movi/get_var_mes',{
					tipo: p.contrato.inmueble.tipo._id.$id,
					dia_ini: parseInt(ciHelper.date.format.bd_d(p.contrato.fecini)),
					pagos: p.pagos
				},function(data){
					p.tc = data.tc.valor;
					p.calf = data.calf;
					p.ctban = data.ctban;
					p.cuenta = data.cuenta;
					p.cuenta_dudosa = data.conf.COBRANZA;
					p.conf = data.conf;
					for(var i=0,j=data.vars.length; i<j; i++){
						if(data.vars[i].cod=='IGV')
							p.igv = (parseFloat(data.vars[i].valor)/100);
						else if(data.vars[i].cod=='MORA')
							p.mora = parseFloat(data.vars[i].valor);
						else if(data.vars[i].cod=='DETRACCION')
							p.detraccion = parseFloat(data.vars[i].valor);
					}
					p.$w.find('[name=tipo]').html(p.contrato.inmueble.tipo.nomb);
					p.$w.find('[name=sublocal]').html(p.contrato.inmueble.sublocal.nomb);
					p.$w.find('[name=inmueble]').html(p.contrato.inmueble.direccion);
					p.$w.find('[name=titular]').html(mgEnti.formatName(p.contrato.titular));
					p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
					p.$w.find('[name=btnSel]').click(function(){
						mgEnti.windowSelect({
							bootstrap: true,
							callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							}
						});
					});
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.contrato.titular);
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
					p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						inMovi.init();
						return K.msg({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
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
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
								p.get_glosa();
								p.calcTot();
							}).change();
						},'json');
					}).change();
					for(var i=0,j=p.pagos.length; i<j; i++){
						var texto_pago = inMovi.get_motivo(p.contrato.motivo._id.$id);
						var $row = $('<tr class="item" name="serv'+i+'">');
						$row.append('<td>'+(i+1)+'</td>');
						if(p.dias!=null){
							texto_pago += ' de '+
								ciHelper.date.format.bd_ymd(p.contrato.fecini)+' al '+ciHelper.date.format.bd_ymd(p.contrato.fecdes);
							//p.renta = (p.renta/30)*(1+parseInt(ciHelper.dateDifference(p.contrato.fecdes,p.contrato.fecini)));
							p.renta = p.contrato.importe;
						}else{
							var dia_ini_tmp = ciHelper.date.format.bd_d(p.contrato.fecini);
							p.dia_ini = dia_ini_tmp;
							if(parseInt(dia_ini_tmp)==1){
								texto_pago += ' de '+
									ciHelper.meses[parseInt(p.pagos[i].mes)-1]+' - '+p.pagos[i].ano;
							}else{
								var mes_n = parseInt(p.pagos[i].mes)-1,
								ano_n = parseInt(p.pagos[i].ano);
								mes_n--;
								if(mes_n==-1){
									mes_n = 11;
									ano_n--;
								}
								var tmp_ini = 'Del '+dia_ini_tmp+' de '+ciHelper.meses[mes_n]+'-'+ano_n,
								tmp_fin = 'Al '+(parseInt(dia_ini_tmp)-1)+' de '+ciHelper.meses[parseInt(p.pagos[i].mes)-1]+'-'+p.pagos[i].ano;
								//$row.append('<td>'+tmp_ini+'<br />'+tmp_fin+'</td>');
								texto_pago += ' '+
									tmp_ini+' '+tmp_fin;
							}
						}
						$row.append('<td>'+texto_pago.toUpperCase()+'</td>');
						$row.append('<td>');
						$row.append('<td>');
						p.$w.find('[name=gridServ] tbody').append($row);
						var $row1 = $('<tr class="item" name="conc_renta_'+i+'">');
						$row1.append('<td>');
						$row1.append('<td>&nbsp;&nbsp;&nbsp;<i>Renta del Inmueble</i></td>');
						$row1.append('<td>');
						$row1.append('<td>'+ciHelper.formatMon(p.renta,p.moneda)+'</td>');
						$row1.data('monto',p.renta);
						p.$w.find('[name=gridServ] tbody').append($row1);
						var $row2 = $('<tr class="item" name="conc_igv_'+i+'">');
						$row2.append('<td>');
						$row2.append('<td>&nbsp;&nbsp;&nbsp;<i>IGV</i></td>');
						$row2.append('<td>');
						//$row2.append('<td>'+ciHelper.formatMon((p.igv)*p.renta,p.moneda)+'</td>');
						//$row2.data('monto',(p.igv)*p.renta);
						//IGV (INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad)
						if(p.contrato.motivo._id.$id=='55316553bc795ba801000035'){
							$row2.append('<td>'+ciHelper.formatMon((p.igv)*0,p.moneda)+'</td>');
							$row2.data('monto',(p.igv)*0);
						}
						else{
							$row2.append('<td>'+ciHelper.formatMon((p.igv)*p.renta,p.moneda)+'</td>');
							$row2.data('monto',(p.igv)*p.renta);
						}
						p.$w.find('[name=gridServ] tbody').append($row2);
						if(p.contrato.con_mora=='1'){
							/************************************************************************************************
							* HALLAMOS LA MORA
							************************************************************************************************/
							//porc = inMovi.calculoMora(p,i);
							var porc = 0,
							mes_n = p.pagos[i].mes,
							ano_n = p.pagos[i].ano;
							if(parseInt(dia_ini_tmp)!=1){
								var mes_n = parseInt(p.pagos[i].mes)-1,
								ano_n = parseInt(p.pagos[i].ano);
								if(mes_n==0){
									mes_n = 12;
									ano_n--;
								}
							}
							$.post('in/movi/get_mora',{
								mes: mes_n,
								ano: ano_n,
								fecini: ciHelper.date.format.bd_ymd(p.contrato.fecini)
							},function(porc){
								p.$w.find('[name=mora][data-mes='+porc.mes+'][data-ano='+porc.ano+']').val(K.round(porc.mora,2));
								p.calcTot();
								K.unblock();
							},'json');
							var $row3 = $('<tr class="item" name="conc_mora_'+i+'">');
							$row3.append('<td>');
							$row3.append('<td>&nbsp;&nbsp;&nbsp;<i>Moras</i></td>');
							$row3.append('<td><input type="text" name="mora" size="7" value="'+porc+'" data-mes="'+mes_n+'" data-ano="'+ano_n+'"/>%</td>');
							$row3.append('<td>'+ciHelper.formatMon(p.renta*(porc/100),p.moneda)+'</td>');
							$row3.find('[name=mora]').val(porc).keyup(function(){
								p.calcTot();
							});
							p.$w.find('[name=gridServ] tbody').append($row3);
						}else
							K.unblock();
						var dia_ini_tmp = ciHelper.date.format.bd_d(p.contrato.fecini);
							/*if(parseInt(dia_ini_tmp)==1){
								//
							}else{
								p.pagos[i].mes = parseInt(p.pagos[i].mes);
								p.pagos[i].mes++;
								if(p.pagos[i].mes==13){
									p.pagos[i].mes = 1;
									p.pagos[i].ano++;
								}
							}*/

					}
					p.get_glosa();
					var $row = $('<tr class="item" name="totalGrid">');
					$row.append('<th colspan="3">Total</th>');
					$row.append('<th>');
					p.$w.find('[name=gridServ] table:last').append('<tfoot>');
					p.$w.find('[name=gridServ] tfoot').append($row);
					if(p.moneda=='D'){
						var $row = $('<tr class="item">');
						$row.append('<th colspan="3">Tasa de Cambio de Soles a D&oacute;lares</th>');
						$row.append('<th><input type="text" name="tasa" size="7" value="0"></th>');
						$row.find('[name=tasa]').val(p.tc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tfoot').append($row);
						var $row = $('<tr class="item" name="totalGridConv">');
						$row.append('<th colspan="3">Total en Soles</th>');
						$row.append('<th>');
						p.$w.find('[name=gridServ] tfoot').append($row);
					}
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td><div class="input-group col-sm-8">'+
						'<input class="form-control" name="tot" type="text" />'+
					'</div></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Efectivo Dolares*/
					var $row = $('<tr class="item" name="mon_dol">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td><div class="input-group col-sm-8">'+
						'<input class="form-control" name="tot" type="text" />'+
					'</div></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						if(i==0||i==2){
							var $row = $('<tr class="item" name="ctban">');
							$row.append('<td>Voucher '+
								'<input class="form-control" name="voucher" type="text" />'+
							'</td>');
							$row.append('<td>'+p.ctban[i].nomb+'</td>');
							$row.append('<td><div class="input-group col-sm-8">'+
								'<input class="form-control" name="tot" type="text" />'+
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
					}
					//p.calcTot();
					//K.unblock();
				},'json');
			}
		});
	},
	/**************************************************************************************************************************
	*
	* PAGO DE CUENTA POR COBRAR RELACIONADA A CONTRATO
	*
	**************************************************************************************************************************/
	newCobro: function(p){
		$.extend(p,{
			moneda: p.cobro.moneda,
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.tc,
				total_row = 0;
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=conc]').length; i<j; i++){
					var renta = parseFloat(p.$w.find('[name=gridServ] [name^=conc]').eq(i).data('monto'));
					total += renta;
					total_row += renta;
					p.$w.find('[name=gridServ] tbody [name^=conc]').eq(i).find('td:eq(3)').html('<b>'+ciHelper.formatMon(total_row,p.moneda)+'</b>');
					total_row = 0;
				}
				p.$w.find('[name=totalGrid]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				if(p.total>700&&p.$w.find('[name=comp] option:selected').val()=='F'){
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
					p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion)+'</p>');
				}else{
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
				}
				p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(p.total,2));
				if(p.total>=700){
					var tmp_efec = total - (total*p.detraccion),
					tmp_detr = total*p.detraccion;
					if(p.$w.find('[name=comp] option:selected').val()=='F'){
						p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
						p.$w.find('[name=gridForm] [name=voucher]:eq(1)').val('XXX');
						p.$w.find('[name=gridForm] [name=tot]:eq(3)').val(K.round(tmp_detr,2));
					}else{
						p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(total,2));
						p.$w.find('[name=gridForm] [name=voucher]:eq(1)').val('');
						p.$w.find('[name=gridForm] [name=tot]:eq(3)').val(K.round(0,2));
					}
				}
				p.$w.find('[name=gridForm] [name=tot]').keyup();
			},
			save: function(){
				var data = {
					modulo: 'IN',
					inmueble: {
						_id: p.cobro.inmueble._id.$id,
						direccion: p.cobro.inmueble.direccion
					},
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
					total: parseFloat(p.$w.find('[name=totalGrid]').data('monto'))
				};
				if(data.moneda=='D'){
					data.tc = parseFloat(p.$w.find('[name=tasa]').val());
					data.total_dolares = parseFloat(p.$w.find('[name=totalGrid]').data('monto'));
					data.total_soles = parseFloat(p.$w.find('[name=totalGridConv]').data('monto'));
					data.total = data.total*p.tc;
				}else{
					data.tc = p.tc;
				}
				data.cliente = mgEnti.dbRel(data.cliente);
				if(data.num==''){
					p.$w.find('[name=num]').focus();
					return K.msg({
						title: ciHelper.titles.infoReq,
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
				var cuenta_cobrar = {
					cuenta_cobrar: {
						_id: p.cobro._id.$id,
						servicio: mgServ.dbRel(p.cobro.servicio)
					},
					conceptos: []
				};
				for(var i=0,j=(p.$w.find('[name^=conc]').length); i<j; i++){
					var tmp = p.$w.find('[name^=conc]').eq(i).data('data');
					if(tmp!=null){
						cuenta_cobrar.conceptos.push({
							concepto: cjConc.dbRel(tmp.concepto),
							cuenta: ctPcon.dbRel(tmp.concepto.cuenta),
							monto: parseFloat(p.$w.find('[name^=conc]').eq(i).data('monto'))
						});
					}
				}
				if(data.tipo!='R'){
					var concepto = {
						concepto: 'IGV ('+((p.igv)*100)+'%)',
						monto: parseFloat(K.round(p.$w.find('[name=conc_igv_1]').data('monto'),2)),
						cuenta: {
							_id: p.conf.IGV._id.$id,
							descr: p.conf.IGV.descr,
							cod: p.conf.IGV.cod
						}
					};
					cuenta_cobrar.conceptos.push(concepto);
				}

				data.items.push(cuenta_cobrar);
				if(parseFloat(data.total)==0){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El comprobante no puede tener como total 0!',
						type: 'error'
					});
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El total del comprobante no coincide con el total de la forma de pagar!',
						type: 'error'
					});
				}
				data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
				data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
				K.sendingInfo();
				p.$w.find('#div_buttons button').attr('disabled','disabled');
				$.post('in/comp/save_comp',data,function(comp){
				//$.post('in/comp/save',data,function(){
					K.clearNoti();
					inMovi.init({inmueble: inInmu.dbRel(p.cobro.inmueble)});
					K.msg({title: ciHelper.titles.regiGua,text: 'Comprobante creado con &eacute;xito!'});



					/*
					 * AQUI ABRIR EL PDF
					 */

					K.windowPrint({
						id:'windowPrint',
						title: "Comprobante de Pago",
						url: "in/comp/print?print=1&_id="+comp._id.$id
					});


				},'json');
			}
		});
		new K.Panel({
			contentURL: 'in/movi/comp_cobro',
			buttons: {
				'Generar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						if(p.total>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
							var detrac = p.total*p.detraccion;
							ciHelper.confirm(
								'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
								function () {
									p.save();
								},
								function () { $.noop(); }
							);
						}else{
							p.save();
						}
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMovi.init({inmueble: inInmu.dbRel(p.cobro.inmueble)});
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
				$.post('in/movi/get_var_cobro',function(data){
					p.tc = data.tc.valor;
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
					p.$w.find('[name=tipo]').html(p.cobro.inmueble.tipo.nomb);
					p.$w.find('[name=sublocal]').html(p.cobro.inmueble.sublocal.nomb);
					p.$w.find('[name=inmueble]').html(p.cobro.inmueble.direccion);
					p.$w.find('[name=titular]').html(mgEnti.formatName(p.cobro.cliente));
					p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
					p.$w.find('[name=btnSel]').click(function(){
						mgEnti.windowSelect({
							bootstrap: true,
							callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							}
						});
					});
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.cobro.cliente);
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
					p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						inMovi.init();
						return K.msg({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
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
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
								p.calcTot();
							}).change();
						},'json');
					}).change();
					var $row = $('<tr class="item" name="serv">');
					$row.append('<td>');
					//$row.append('<td>'+p.cobro.servicio.nomb+' de '+p.cobro.inmueble.direccion+'</td>');
					if(p.cobro.observ!=null){
						if(p.cobro.observ!='')
							p.$w.find('[name=observ]').val(p.cobro.observ);
					}
					$row.append('<td>'+p.cobro.servicio.nomb+' de '+p.cobro.inmueble.direccion+'</td>');
					$row.append('<td>');
					p.$w.find('[name=gridServ] tbody').append($row);















					var tmp_tot = 0,
					tmp_ult = 0,
					tupa = false;
					//console.log(p.cobro);
					for(var i=0,j=p.cobro.conceptos.length; i<j; i++){
						if(p.cobro.conceptos[i].concepto.cod.substring(0,4)=='TUPA') tupa = true;
						p.cobro.conceptos[i].monto = parseFloat(p.cobro.conceptos[i].monto);
						var $row1 = $('<tr class="item" name="conc_'+i+'">');
						$row1.append('<td>');
						$row1.append('<td>&nbsp;&nbsp;&nbsp;<i>'+p.cobro.conceptos[i].concepto.nomb+'</i></td>');
						if(tupa==false){
							$row1.append('<td>'+ciHelper.formatMon(p.cobro.conceptos[i].monto/(1+p.igv),p.moneda)+'</td>');
							$row1.data('monto',K.round(p.cobro.conceptos[i].monto/(1+p.igv),2)).data('data',p.cobro.conceptos[i]);
							p.$w.find('[name=gridServ] tbody').append($row1);
							tmp_tot += K.round(p.cobro.conceptos[i].monto/(1+p.igv),2);
							tmp_ult += p.cobro.conceptos[i].monto-K.round(p.cobro.conceptos[i].monto/(1+p.igv),2);
						}else{
							$row1.append('<td>'+ciHelper.formatMon(p.cobro.conceptos[i].monto,p.moneda)+'</td>');
							$row1.data('monto',K.round(p.cobro.conceptos[i].monto,2)).data('data',p.cobro.conceptos[i]);
							p.$w.find('[name=gridServ] tbody').append($row1);
							tmp_tot += p.cobro.conceptos[i].monto;
							tmp_ult += p.cobro.conceptos[i].monto;
						}
					}
					if(tupa==false){
						var $row2 = $('<tr class="item" name="conc_igv_'+i+'">');
						$row2.append('<td>');
						$row2.append('<td>&nbsp;&nbsp;&nbsp;<i>IGV</i></td>');
						$row2.append('<td>'+ciHelper.formatMon(tmp_ult,p.moneda)+'</td>');
						$row2.data('monto',tmp_ult);
						p.$w.find('[name=gridServ] tbody').append($row2);
					}













					var $row = $('<tr class="item" name="totalGrid">');
					$row.append('<th colspan="2">Total</th>');
					$row.append('<th>');
					p.$w.find('[name=gridServ] table:last').append('<tfoot>');
					p.$w.find('[name=gridServ] tfoot').append($row);
					if(p.moneda=='D'){
						var $row = $('<tr class="item">');
						$row.append('<th colspan="3">Tasa de Cambio de Soles a D&oacute;lares</th>');
						$row.append('<th><input type="text" name="tasa" size="7" value="0"></th>');
						$row.find('[name=tasa]').val(p.tc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tfoot').append($row);
						var $row = $('<tr class="item" name="totalGridConv">');
						$row.append('<th colspan="2">Total en Soles</th>');
						$row.append('<th>');
						p.$w.find('[name=gridServ] tfoot').append($row);
					}
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
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
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban">');
						$row.append('<td>Voucher <input type="text" name="voucher" size="7"/></td>');
						$row.append('<td>'+p.ctban[i].nomb+'</td>');
						$row.append('<td>'+(data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="text" name="tot" size="7"/></td>');
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
					p.calcTot();
					K.unblock();
				},'json');
			}
		});
	},
	/**************************************************************************************************************************
	*
	* PAGO PARCIAL (ADELANTO) DE MENSUALIDAD
	*
	**************************************************************************************************************************/
	newParcial: function(p){
		p.total_max = parseFloat(p.contrato.importe);
		if(p.pago.total!=null) p.total_max = parseFloat(p.contrato.importe)-parseFloat(p.pago.total);
		$.extend(p,{
			moneda: p.contrato.moneda,
			importe: parseFloat(p.contrato.importe),
			renta: parseFloat(p.contrato.importe),
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.tc,
				total_row = 0;
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=serv]').length; i<j; i++){
					var renta = parseFloat(p.$w.find('[name=gridServ] [name^=conc_renta]').data('monto'));
					total += renta;
					total_row += renta;
					total += parseFloat(p.$w.find('[name=gridServ] [name^=conc_igv]').data('monto'));
					total_row += parseFloat(p.$w.find('[name=gridServ] [name^=conc_igv]').data('monto'));
					if(p.contrato.con_mora=='1'){
						total = p.$w.find('[name=gridServ] tbody [name^=serv] [name=pago]').val();
						var importe = K.round(total/(1+p.igv+(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100)),2),
						igv = total-importe;
						var mora = K.round(importe*(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100),2);
						if(!isNaN(mora)) igv = igv-mora;
						p.$w.find('[name=gridServ] tbody [name^=conc_renta]').find('td:eq(2)').html(ciHelper.formatMon(importe,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_renta]').data('monto',importe);
						p.$w.find('[name=gridServ] tbody [name^=conc_igv]').find('td:eq(2)').html(ciHelper.formatMon(igv,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_igv]').data('monto',igv);
						p.$w.find('[name=gridServ] tbody [name^=conc_mora]').find('td:eq(2)').html(ciHelper.formatMon(mora,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_mora]').data('monto',mora);
					}
				}
				p.$w.find('[name=totalGrid]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
				p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion)+'</p>');
				p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(p.total,2));
				p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(0);
				if(p.total>=700){
					var tmp_efec = total - (total*p.detraccion),
					tmp_detr = total*p.detraccion;
					p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
					p.$w.find('[name=gridForm] [name=voucher]:eq(2)').val('XXX');
					p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(K.round(tmp_detr,2));
				}
				p.$w.find('[name=gridForm] [name=tot]').keyup();
			},
			save: function(){
				var data = {
					tipo_pago: p.$w.find('[name=tipo_pago]').val(),
					parcial: true,
					modulo: 'IN',
					contrato: p.contrato._id.$id,
					inmueble: {
						_id: p.contrato.inmueble._id.$id,
						direccion: p.contrato.inmueble.direccion
					},
					alquiler: true,
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
					dia_ini: ciHelper.date.format.bd_d(p.contrato.fecini),
					total: parseFloat(p.$w.find('[name=totalGrid]').data('monto')),
					sunat: {
						codigo:"",
	                    descr: p.$w.find('[name=texto_pago]').val(),
	                    cod_unidad:"NIU",
	                    unidad:"number of international units",
	                    precio_unitario:0,
	                    importe_total:0,
	                    valor_unitario:0,
	                    base:0,
	                    ope_inafectas:0,
	                    ope_gravadas:0,
	                    inafecto:false,
	                    cant:"1.00",
	                    exonerada:false,
	                    gratuito:false,
	                    igv:"0.18",
	                    isc:"0.00",
	                    otros_imp:"0.00",
	                    ope_exonerada:"0.00",
	                    ope_gratuitas:"0.00",
	                    desc:"0.00",
	                    meta: item
	                }
				};
				if(data.moneda=='D'){
					data.tc = parseFloat(p.$w.find('[name=tasa]').val());
					data.total_dolares = parseFloat(p.$w.find('[name=totalGrid]').data('monto'));
					data.total_soles = parseFloat(p.$w.find('[name=totalGridConv]').data('monto'));
					data.total = data.total*p.tc;
				}else{
					data.tc = p.tc;
				}
				data.cliente = mgEnti.dbRel(data.cliente);
				if(data.num==''){
					p.$w.find('[name=num]').focus();
					return K.msg({
						title: ciHelper.titles.infoReq,
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
				/* PAGO */
				var item = {
					dia_ini: ciHelper.date.format.bd_d(p.contrato.fecini),
					pago: p.pago,
					conceptos: []
				};
				var tmp_cuenta = p.cuenta;
				//COBRANZA DUDOSA
				if(inMovi.get_cobranza_dudosa(p.pago.mes,p.pago.ano)==true){
					tmp_cuenta = p.cuenta_dudosa;
				}
				//ALQUILER
				var concepto = {
					concepto: p.$w.find('[name=serv] td:eq(0)').html(),
					monto: parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2)),
					cuenta: {
						_id: tmp_cuenta._id.$id,
						descr: tmp_cuenta.descr,
						cod: tmp_cuenta.cod
					}
				};
				item.conceptos.push(concepto);
				//IGV
				var concepto = {
					concepto: 'IGV ('+((p.igv)*100)+'%)',
					monto: parseFloat(K.round(p.$w.find('[name^=conc_igv]').data('monto'),2)),
					cuenta: {
						_id: p.conf.IGV._id.$id,
						descr: p.conf.IGV.descr,
						cod: p.conf.IGV.cod
					}
				};
				item.conceptos.push(concepto);
				//MORA
				if(p.contrato.con_mora=='1'){
					var concepto = {
						concepto: 'Moras ('+p.$w.find('[name^=conc_mora] [name=mora]').val()+'%)',
						monto: parseFloat(K.round(p.$w.find('[name^=conc_mora]').data('monto'),2)),
						cuenta: {
							_id: p.conf.MOR._id.$id,
							descr: p.conf.MOR.descr,
							cod: p.conf.MOR.cod
						}
					};
					item.conceptos.push(concepto);
				}
				data.items.push(item);
				data.sunat.precio_unitario += parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2));
                data.sunat.importe_total += parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name^=conc_mora]').data('monto'),2)) + parseFloat(K.round(p.$w.find('[name^=conc_igv]').data('monto'),2));
                data.sunat.valor_unitario += parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2));
                data.sunat.ope_gravadas += parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2));
                data.sunat.ope_inafectas += parseFloat(K.round(p.$w.find('[name^=conc_mora]').data('monto'),2));
                if(data.sunat.ope_inafectas!=0){
                	data.sunat.inafecto = true;
                }
				if(parseFloat(data.total)==0){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El comprobante no puede tener como total 0!',
						type: 'error'
					});
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El total del comprobante no coincide con el total de la forma de pagar!',
						type: 'error'
					});
				}
				data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
				data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
				console.log(p.total_max);
				console.info(parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2)));
				if(parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2))>K.round(p.total_max,2)){
					return K.msg({
						title: 'L&iacute;mite excedido',
						text: 'El monto a pagar supera lo adeudado!',
						type: 'error'
					});
				}
				if(parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2))==parseFloat(p.contrato.importe)){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El monto a pagar no puede ser igual a un mes entero!',
						type: 'error'
					});
				}
				data.sunat = [data.sunat];
				K.sendingInfo();
				p.$w.find('#div_buttons button').attr('disabled','disabled');
				$.post('in/comp/save_comp',data,function(comp){
				//$.post('in/comp/save',data,function(){
					K.clearNoti();
					inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
					K.msg({title: ciHelper.titles.regiGua,text: 'Comprobante creado con &eacute;xito!'});



					/*
					 * AQUI ABRIR EL PDF
					 */



					K.windowPrint({
						id:'windowPrint',
						title: "Comprobante de Pago",
						url: "in/comp/print?print=1&_id="+comp._id.$id
					});
				},'json');
			}
		});
		new K.Panel({
			contentURL: 'in/movi/comp_mes',
			buttons: {
				'Generar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						if(p.total>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
							var detrac = p.total*p.detraccion;
							ciHelper.confirm(
								'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
								function () {
									p.save();
								},
								function () { $.noop(); }
							);
						}else{
							p.save();
						}
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
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
					cols: ['Servicio','','Importe Total'],
					onlyHtml: true,
					toolbarHTML: '<select name="tipo_pago" class="form-control">'+
							'<option value="AN">Anticipo</option>'+
							'<option value="AC">A Cuenta</option>'+
							'<option value="CA">Cancelaci&oacute;n</option>'+
							'<option value="CO">Compensaci&oacute;n</option>'+
							'<option value="RE">Reintegro</option>'+
						'</select>'
				});
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				$.post('in/movi/get_var_mes',{
					tipo: p.contrato.inmueble.tipo._id.$id,
					dia_ini: parseInt(ciHelper.date.format.bd_d(p.contrato.fecini)),
					pagos: [p.pago]
				},function(data){
					p.tc = data.tc.valor;
					p.calf = data.calf;
					p.ctban = data.ctban;
					p.cuenta = data.cuenta;
					p.cuenta_dudosa = data.conf.COBRANZA;
					p.conf = data.conf;
					for(var i=0,j=data.vars.length; i<j; i++){
						if(data.vars[i].cod=='IGV')
							p.igv = (parseFloat(data.vars[i].valor)/100);
						else if(data.vars[i].cod=='MORA')
							p.mora = parseFloat(data.vars[i].valor);
						else if(data.vars[i].cod=='DETRACCION')
							p.detraccion = parseFloat(data.vars[i].valor);
					}
					p.$w.find('[name=tipo]').html(p.contrato.inmueble.tipo.nomb);
					p.$w.find('[name=sublocal]').html(p.contrato.inmueble.sublocal.nomb);
					p.$w.find('[name=inmueble]').html(p.contrato.inmueble.direccion);
					p.$w.find('[name=titular]').html(mgEnti.formatName(p.contrato.titular));
					p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
					p.$w.find('[name=btnSel]').click(function(){
						mgEnti.windowSelect({
							bootstrap: true,
							callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							}
						});
					});
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.contrato.titular);
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
					p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						inMovi.init();
						return K.msg({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
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
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					var $row = $('<tr class="item" name="serv">');
					$row.append('<td>Pago Parcial de Alquiler correspondiente a '+
						ciHelper.meses[p.pago.mes-1]+' - '+p.pago.ano+' de '+
						p.contrato.inmueble.direccion+' (Cuota '+(parseInt(p.pago.item)+1)+')</td>');
					$row.append('<td>');
					$row.append('<td><input type="text" name="pago" value="'+p.total_max+'" /></td>');
					$row.find('[name=pago]').keyup(function(){
						var total = $(this).val(),
						importe = total/(1+(p.igv)),
						igv = total-importe;
						p.importe = parseFloat(total);
						p.$w.find('[name=conc_renta] td:eq(2)').html(ciHelper.formatMon(importe,p.moneda));
						p.$w.find('[name=conc_renta]').data('monto',importe);
						p.$w.find('[name=conc_igv] td:eq(2)').html(ciHelper.formatMon(igv,p.moneda));
						p.$w.find('[name=conc_igv]').data('monto',igv);
						p.calcTot();
					});
					p.$w.find('[name=gridServ] tbody').append($row);
					var $row1 = $('<tr class="item" name="conc_renta">');
					$row1.append('<td>&nbsp;&nbsp;&nbsp;<i>Renta del Inmueble</i></td>');
					$row1.append('<td>'+ciHelper.formatMon(p.total_max,p.moneda)+'</td>');
					$row1.append('<td>'+ciHelper.formatMon(p.total_max,p.moneda)+'</td>');
					$row1.data('monto',p.importe);
					p.$w.find('[name=gridServ] tbody').append($row1);
					var $row2 = $('<tr class="item" name="conc_igv">');
					$row2.append('<td>&nbsp;&nbsp;&nbsp;<i>IGV</i></td>');
					$row2.append('<td>');
					$row2.append('<td>'+ciHelper.formatMon((p.igv)*p.importe,p.moneda)+'</td>');
					$row2.data('monto',(p.igv)*p.importe);
					p.$w.find('[name=gridServ] tbody').append($row2);
					if(p.contrato.con_mora=='1'){
						/************************************************************************************************
						* HALLAMOS LA MORA
						************************************************************************************************/
						//porc = inMovi.calculoMora(p,i);
						if(p.calf!=null){
							var dia = p.calf.dia;
						}else{
							var dia = moment().format('YYYY-MM-DD');
						}

						var dia_ini_tmp = ciHelper.date.format.bd_d(p.contrato.fecini);
						var porc = 0,
						mes_n = p.pago.mes,
						ano_n = p.pago.ano;
						if(parseInt(dia_ini_tmp)!=1){
							var mes_n = parseInt(p.pago.mes)-1,
							ano_n = parseInt(p.pago.ano);
							if(mes_n==0){
								mes_n = 12;
								ano_n--;
							}
						}
						$.post('in/movi/get_mora',{
							mes: mes_n,
							ano: ano_n,
							fecini: ciHelper.date.format.bd_ymd(p.contrato.fecini)
						},function(porc){
							p.$w.find('[name=mora][data-mes='+porc.mes+'][data-ano='+porc.ano+']').val(porc.mora);
							//p.calcTot();




							tmpk_base = p.total_max;
							tmpk_moras = tmpk_base*(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100);
							tmpk_igv = tmpk_base*p.igv;
							tmpk_total = tmpk_base+tmpk_moras+tmpk_igv;
							p.$w.find('[name=serv] [name=pago]').val(K.round(tmpk_total,2));
							p.calcTot();




							K.unblock();
						},'json');
						var $row3 = $('<tr class="item" name="conc_mora">');
						$row3.append('<td>&nbsp;&nbsp;&nbsp;<i>Moras (desde el '+dia+')</i></td>');
						$row3.append('<td><input type="text" name="mora" size="7" value="'+porc+'" data-mes="'+mes_n+'" data-ano="'+ano_n+'"/>%</td>');
						$row3.append('<td>'+ciHelper.formatMon(p.importe*(porc/100),p.moneda)+'</td>');
						$row3.find('[name=mora]').val(porc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tbody').append($row3);
					}














					/**********************************************************************************************************
					*<!-- TEXTO DE COMPROBANTE
					**********************************************************************************************************/
					var tmp_text_inmu = "INMUEBLE: ";
					if(p.contrato.inmueble.tipo.nomb=='EX NUEVO MILENIO'){
						tmp_text_inmu = "TERRENO: ";
					}
					if(p.contrato.inmueble.tipo.nomb=='LA CHOCITA'){
						tmp_text_inmu = "ESPACIO: ";
					}
					if(p.contrato.inmueble.tipo.nomb=='VARIOS'){
						tmp_text_inmu = "POR OCUPACION ";
					}
					p.$w.find('[name=texto_inmueble]').val(tmp_text_inmu+p.contrato.inmueble.direccion.toUpperCase()+' ('+p.contrato.inmueble.tipo.nomb.toUpperCase()+')');
					p.$w.find('[name=tipo_pago]').change(function(){
						var texto_pago = inMovi.get_motivo(p.contrato.motivo._id.$id)
						texto_pago += ' - '+$(this).find('option:selected').html().toUpperCase()+" DE ",
						dia_ini_tmp = ciHelper.date.format.bd_d(p.contrato.fecini);
						p.dia_ini = dia_ini_tmp;
						if(parseInt(dia_ini_tmp)==1){
							texto_pago += ciHelper.meses[parseInt(p.pago.mes)-1].toUpperCase()+' - '+p.pago.ano;
						}else{
							var mes_n = parseInt(p.pago.mes)-1,
							ano_n = parseInt(p.pago.ano);
							mes_n--;
							if(mes_n==-1){
								mes_n = 11;
								ano_n--;
							}
							var tmp_ini = dia_ini_tmp+' DE '+ciHelper.meses[mes_n].toUpperCase()+'-'+ano_n;
						}
						p.$w.find('[name=texto_pago]').val(texto_pago);
					}).change();
					/**********************************************************************************************************
					* TEXTO DE COMPROBANTE -->
					**********************************************************************************************************/


















					var $row = $('<tr class="item" name="totalGrid">');
					$row.append('<th colspan="2">Total</th>');
					$row.append('<th>');
					p.$w.find('[name=gridServ] table:last').append('<tfoot>');
					p.$w.find('[name=gridServ] tfoot').append($row);
					if(p.moneda=='D'){
						var $row = $('<tr class="item">');
						$row.append('<th colspan="2">Tasa de Cambio de Soles a D&oacute;lares</th>');
						$row.append('<th><input type="text" name="tasa" size="7" value="0"></th>');
						$row.find('[name=tasa]').val(p.tc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tfoot').append($row);
						var $row = $('<tr class="item" name="totalGridConv">');
						$row.append('<th colspan="2">Total en Soles</th>');
						$row.append('<th>');
						p.$w.find('[name=gridServ] tfoot').append($row);
					}
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
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
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban">');
						$row.append('<td>Voucher <input type="text" name="voucher" size="7"/></td>');
						$row.append('<td>'+p.ctban[i].nomb+'</td>');
						$row.append('<td>'+(data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="text" name="tot" size="7"/></td>');
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
					tmpk_base = p.total_max;
					tmpk_moras = tmpk_base*(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100);
					tmpk_igv = tmpk_base*p.igv;
					tmpk_total = tmpk_base+tmpk_moras+tmpk_igv;
					p.$w.find('[name=serv] [name=pago]').val(K.round(tmpk_total,2));
					p.calcTot();
					K.unblock();
				},'json');
			}
		});
	},
	/**************************************************************************************************************************
	*
	* PAGO DE COMPENSACION
	*
	**************************************************************************************************************************/
	newCompe: function(p){
		p.total_max = parseFloat(p.contrato.importe);
		if(p.pago.total!=null) p.total_max = parseFloat(p.contrato.importe)-parseFloat(p.pago.total);
		$.extend(p,{
			moneda: p.contrato.moneda,
			importe: parseFloat(p.contrato.importe),
			renta: parseFloat(p.contrato.importe),
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.tc,
				total_row = 0;
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=serv]').length; i<j; i++){
					var renta = parseFloat(p.$w.find('[name=gridServ] [name^=conc_renta]').data('monto'));
					total += renta;
					total_row += renta;
					total += parseFloat(p.$w.find('[name=gridServ] [name^=conc_igv]').data('monto'));
					total_row += parseFloat(p.$w.find('[name=gridServ] [name^=conc_igv]').data('monto'));
					if(p.contrato.con_mora=='1'){
						total = p.$w.find('[name=gridServ] tbody [name^=serv] [name=pago]').val();
						var importe = K.round(total/(1+p.igv+(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100)),2),
						mora = K.round(importe*(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100),2),
						igv = total-importe-mora;
						p.$w.find('[name=gridServ] tbody [name^=conc_renta]').find('td:eq(2)').html(ciHelper.formatMon(importe,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_renta]').data('monto',importe);
						p.$w.find('[name=gridServ] tbody [name^=conc_igv]').find('td:eq(2)').html(ciHelper.formatMon(igv,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_igv]').data('monto',igv);
						p.$w.find('[name=gridServ] tbody [name^=conc_mora]').find('td:eq(2)').html(ciHelper.formatMon(mora,p.moneda));
						p.$w.find('[name=gridServ] tbody [name^=conc_mora]').data('monto',mora);
					}
				}
				p.$w.find('[name=totalGrid]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				if(p.total>700){
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
					p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion)+'</p>');
				}else{
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
				}
			},
			save: function(){
				var data = {
					parcial: true,
					modulo: 'IN',
					contrato: p.contrato._id.$id,
					inmueble: {
						_id: p.contrato.inmueble._id.$id,
						direccion: p.contrato.inmueble.direccion
					},
					compensacion: true,
					alquiler: true,
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
					total: parseFloat(p.$w.find('[name=totalGrid]').data('monto'))
				};
				if(data.moneda=='D'){
					data.tc = parseFloat(p.$w.find('[name=tasa]').val());
					data.total_dolares = parseFloat(p.$w.find('[name=totalGrid]').data('monto'));
					data.total_soles = parseFloat(p.$w.find('[name=totalGridConv]').data('monto'));
					data.total = data.total*p.tc;
				}else{
					data.tc = p.tc;
				}
				data.cliente = mgEnti.dbRel(data.cliente);
				if(data.num==''){
					p.$w.find('[name=num]').focus();
					return K.msg({
						title: ciHelper.titles.infoReq,
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
				/* PAGO */
				var item = {
					pago: p.pago,
					conceptos: []
				};
				//ALQUILER
				var concepto = {
					concepto: p.$w.find('[name=serv] td:eq(1)').html(),
					monto: parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2)),
					cuenta: {
						_id: p.cuenta._id.$id,
						descr: p.cuenta.descr,
						cod: p.cuenta.cod
					}
				};
				item.conceptos.push(concepto);
				//IGV
				var concepto = {
					concepto: 'IGV ('+((p.igv)*100)+'%)',
					monto: parseFloat(K.round(p.$w.find('[name^=conc_igv]').data('monto'),2)),
					cuenta: {
						_id: p.conf.IGV._id.$id,
						descr: p.conf.IGV.descr,
						cod: p.conf.IGV.cod
					}
				};
				//				//IGV (INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad)
				//				if(p.contrato.motivo._id.$id=='55316553bc795ba801000035'){
				//					var concepto = {
				//						concepto: 'IGV ('+((p.igv)*100)+'%)',
				//						monto: parseFloat(0),
				//						cuenta: {
				//							_id: p.conf.IGV._id.$id,
				//							descr: p.conf.IGV.descr,
				//							cod: p.conf.IGV.cod
				//						}
				//					};
				//				}
				//				else{
				//					var concepto = {
				//						concepto: 'IGV ('+((p.igv)*100)+'%)',
				//						monto: parseFloat(K.round(p.$w.find('[name^=conc_igv]').data('monto'),2)),
				//						cuenta: {
				//							_id: p.conf.IGV._id.$id,
				//							descr: p.conf.IGV.descr,
				//							cod: p.conf.IGV.cod
				//						}
				//					};
				//				}
				item.conceptos.push(concepto);
				//MORA
				if(p.contrato.con_mora=='1'){
					var concepto = {
						concepto: 'Moras ('+p.$w.find('[name^=conc_mora] [name=mora]').val()+'%)',
						monto: parseFloat(K.round(p.$w.find('[name^=conc_mora]').data('monto'),2)),
						cuenta: {
							_id: p.conf.MOR._id.$id,
							descr: p.conf.MOR.descr,
							cod: p.conf.MOR.cod
						}
					};
					item.conceptos.push(concepto);
				}
				data.items.push(item);
				if(parseFloat(data.total)==0){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El comprobante no puede tener como total 0!',
						type: 'error'
					});
				}
				data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
				data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
				if(parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2))>p.total_max){
					return K.msg({
						title: 'L&iacute;mite excedido',
						text: 'El monto a pagar supera lo adeudado!',
						type: 'error'
					});
				}
				if(parseFloat(K.round(p.$w.find('[name^=conc_renta]').data('monto'),2))==parseFloat(p.contrato.importe)){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El monto a pagar no puede ser igual a un mes entero!',
						type: 'error'
					});
				}
				K.sendingInfo();
				p.$w.find('#div_buttons button').attr('disabled','disabled');
				$.post('in/comp/save_comp',data,function(comp){
				//$.post('in/comp/save',data,function(){
					K.clearNoti();
					inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
					K.msg({title: ciHelper.titles.regiGua,text: 'Comprobante creado con &eacute;xito!'});



					/*
					 * AQUI ABRIR EL PDF
					 */



					K.windowPrint({
						id:'windowPrint',
						title: "Comprobante de Pago",
						url: "in/comp/print?print=1&_id="+comp._id.$id
					});
				},'json');
			}
		});
		new K.Panel({
			contentURL: 'in/movi/comp_compen',
			buttons: {
				'Generar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						if(p.total>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
							var detrac = p.total*p.detraccion;
							ciHelper.confirm(
								'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
								function () {
									p.save();
								},
								function () { $.noop(); }
							);
						}else{
							p.save();
						}
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
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
					cols: ['Servicio','','Importe Total'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				$.post('in/movi/get_var_mes',{
					tipo: p.contrato.inmueble.tipo._id.$id,
					dia_ini: parseInt(ciHelper.date.format.bd_d(p.contrato.fecini)),
					pagos: [p.pago]
				},function(data){
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
					p.$w.find('[name=tipo]').html(p.contrato.inmueble.tipo.nomb);
					p.$w.find('[name=sublocal]').html(p.contrato.inmueble.sublocal.nomb);
					p.$w.find('[name=inmueble]').html(p.contrato.inmueble.direccion);
					p.$w.find('[name=titular]').html(mgEnti.formatName(p.contrato.titular));
					p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
					p.$w.find('[name=btnSel]').click(function(){
						mgEnti.windowSelect({
							bootstrap: true,
							callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							}
						});
					});
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.contrato.titular);
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
					p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						inMovi.init();
						return K.msg({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
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
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					var $row = $('<tr class="item" name="serv">');
					$row.append('<td>Pago de Alquiler correspondiente a '+
						ciHelper.meses[p.pago.mes]+' - '+p.pago.ano+' de '+
						p.contrato.inmueble.direccion+' (Cuota '+(parseInt(p.pago.item)+1)+')</td>');
					$row.append('<td>');
					$row.append('<td><input type="text" name="pago" value="'+p.total_max+'" disabled="disabled" /></td>');
					$row.find('[name=pago]').keyup(function(){
						var total = $(this).val(),
						importe = total/(1+(p.igv)),
						igv = total-importe;
						p.importe = parseFloat(total);
						p.$w.find('[name=conc_renta] td:eq(2)').html(ciHelper.formatMon(importe,p.moneda));
						p.$w.find('[name=conc_renta]').data('monto',importe);
						p.$w.find('[name=conc_igv] td:eq(2)').html(ciHelper.formatMon(igv,p.moneda));
						p.$w.find('[name=conc_igv]').data('monto',igv);
						p.calcTot();
					});
					p.$w.find('[name=gridServ] tbody').append($row);
					var $row1 = $('<tr class="item" name="conc_renta">');
					$row1.append('<td>&nbsp;&nbsp;&nbsp;<i>Renta del Inmueble</i></td>');
					$row1.append('<td>'+ciHelper.formatMon(p.total_max,p.moneda)+'</td>');
					$row1.append('<td>'+ciHelper.formatMon(p.total_max,p.moneda)+'</td>');
					$row1.data('monto',p.importe);
					p.$w.find('[name=gridServ] tbody').append($row1);
					var $row2 = $('<tr class="item" name="conc_igv">');
					$row2.append('<td>&nbsp;&nbsp;&nbsp;<i>IGV</i></td>');
					$row2.append('<td>');
					$row2.append('<td>'+ciHelper.formatMon((p.igv)*p.importe,p.moneda)+'</td>');
					$row2.data('monto',(p.igv)*p.importe);
					p.$w.find('[name=gridServ] tbody').append($row2);
					if(p.contrato.con_mora=='1'){
						if(p.calf==null){
							p.calf = {
								dia: p.pago.ano+'-'+parseInt(p.pago.mes)+'-05'
							};
						}
						var dia = p.calf.dia,
						extrac_dia = parseInt(p.calf.dia.substring(8)),
						mora = p.mora,
						diferencia = ciHelper.dateDiffNow_regular(new Date(p.calf.dia)),
						mes = ciHelper.date.getMonth(),
						ano = ciHelper.date.getYear(),
						porc = 0,
						lock = false;
						if(ano>(parseInt(p.pago.ano))){
							lock = true;
						}else{
							if(ano<(parseInt(p.pago.ano))){
								lock = false;
							}else{
								if(mes==(parseInt(p.pago.mes))){
									lock = false;
								}else{
									if(mes>(parseInt(p.pago.mes))){
										lock = true;
									}else{
										lock = false;
									}
								}
							}
						}
						if(lock==false){
							if((mes==(parseInt(p.pago.mes)))&&(ano==parseInt(p.pago.ano))){
								if(diferencia>0) porc = 0;
								else{
									/*if((-diferencia)>5){*/
										var dia_dif = ciHelper.date.getDay();
										porc += parseFloat(K.round((2/30)*(-diferencia),2));
									/*}*/
								}
							}
						}else{
							var tmp_l = false,
							mes_t = parseInt(p.pago.mes),
							ano_t = parseInt(p.pago.ano);
							porc -= 2;
							while(tmp_l==false){
								porc += 2;
								if(ano_t==ano){
									if(mes_t!=mes){
										mes_t++;
									}else{
										tmp_l = true;
									}
								}else{
									mes_t++;
									if(mes_t>=12){
										mes_t = 0;
										ano_t++;
									}
								}
							}
							var dia_dif = ciHelper.date.getDay();
							if(extrac_dia<parseInt(dia_dif)){
								porc += parseFloat(K.round((2/30)*(dia_dif),2));
								//porc += parseFloat(K.round((2/30)*(-diferencia),2));
							}
						}
						var $row3 = $('<tr class="item" name="conc_mora">');
						$row3.append('<td>&nbsp;&nbsp;&nbsp;<i>Moras (desde el '+dia+')</i></td>');
						$row3.append('<td><input type="text" name="mora" size="7" value="'+porc+'" />%</td>');
						$row3.append('<td>'+ciHelper.formatMon(p.importe*(porc/100),p.moneda)+'</td>');
						$row3.find('[name=mora]').val(porc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tbody').append($row3);
					}
					var $row = $('<tr class="item" name="totalGrid">');
					$row.append('<th colspan="2">Total</th>');
					$row.append('<th>');
					p.$w.find('[name=gridServ] table:last').append('<tfoot>');
					p.$w.find('[name=gridServ] tfoot').append($row);
					if(p.moneda=='D'){
						var $row = $('<tr class="item">');
						$row.append('<th colspan="2">Tasa de Cambio de Soles a D&oacute;lares</th>');
						$row.append('<th><input type="text" name="tasa" size="7" value="0"></th>');
						$row.find('[name=tasa]').val(p.tc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tfoot').append($row);
						var $row = $('<tr class="item" name="totalGridConv">');
						$row.append('<th colspan="2">Total en Soles</th>');
						$row.append('<th>');
						p.$w.find('[name=gridServ] tfoot').append($row);
					}
					tmpk_base = p.total_max;
					tmpk_moras = tmpk_base*(parseFloat(p.$w.find('[name=gridServ] tbody [name^=mora]').val())/100);
					tmpk_igv = tmpk_base*p.igv;
					tmpk_total = tmpk_base+tmpk_moras+tmpk_igv;
					p.$w.find('[name=serv] [name=pago]').val(K.round(tmpk_total,2));
					p.calcTot();
					K.unblock();
				},'json');
			}
		});
	},
	/**************************************************************************************************************************
	*
	* PAGO DE ACTA DE CONCILIACION
	*
	**************************************************************************************************************************/
	newActa: function(p){
		$.extend(p,{
			moneda: p.contrato.moneda,
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.$w.find('[name=tasa]').val(),
				total_row = 0;
				if(tasa=='') tasa = p.tc;
				for(var i=0,j=p.$w.find('[name=gridServ] tbody [name^=serv]').length; i<j; i++){
					for(var ii=0; ii<p.$w.find('[name=gridServ] tbody [name^=conc_'+i+'_acta]').length; ii++){
						var renta = parseFloat(p.$w.find('[name=gridServ] [name^=conc_'+i+'_acta]').eq(ii).data('monto'));
						total += renta;
						total_row += renta;
						p.$w.find('[name=gridServ] tbody [name^=serv]').eq(i).find('td:eq(3)').html('<b>'+ciHelper.formatMon(total_row,p.moneda)+'</b>');
					}
					total_row = 0;
				}
				if(p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val()!=''){
					if(parseFloat(p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val())!=0){
						var conc_mora_ = parseFloat(p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val());
						console.log('==========================================');
						console.log(total);
						p.$w.find('[name=gridServ] [name^=conc_mora]').find('td:eq(3)').html('<b>'+ciHelper.formatMon(conc_mora_,p.moneda)+'</b>');
						total += conc_mora_;
					}
				}else p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val(0);





				total = K.round(total,2);
				p.$w.find('[name=totalGrid]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',K.round(total,2))
					.find('th:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				if(p.total>700){
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
					p.$w.find('[name=gridForm]').before('<p class="bg-danger">Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion)+'</p>');
				}else{
					p.$w.find('[name=gridForm]').closest('fieldset').find('.bg-danger').remove();
				}
				p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(p.total,2));
				if(p.total>=700){
					var tmp_efec = total - (total*p.detraccion),
					tmp_detr = total*p.detraccion;
					p.$w.find('[name=gridForm] [name=tot]:first').val(K.round(tmp_efec,2));
					p.$w.find('[name=gridForm] [name=voucher]:eq(2)').val('XXX');
					p.$w.find('[name=gridForm] [name=tot]:eq(4)').val(K.round(tmp_detr,2));
				}
				p.$w.find('[name=gridForm] [name=tot]').keyup();
			},
			save: function(){
				var data = {
					modulo: 'IN',
					acta_conciliacion: p.contrato._id.$id,
					inmueble: {
						_id: p.contrato.inmueble._id.$id,
						direccion: p.contrato.inmueble.direccion
					},
					//alquiler: true,
					acta: true,
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
					total: parseFloat(p.$w.find('[name=totalGrid]').data('monto'))
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
					return K.msg({
						title: ciHelper.titles.infoReq,
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
				for(var i=0,j=p.pagos.length; i<j; i++){
					var item = {
						pago: {
							num: p.pagos[i].num,
							total: p.pagos[i].total,
							fecven: ciHelper.date.format.bd_ymd(p.pagos[i].fecven)
						},
						conceptos: []
					};
					for(var ii=0,jj=p.pagos[i].conceptos.length; ii<jj; ii++){
						var concepto = {
							concepto: p.pagos[i].conceptos[ii].descr,
							monto: parseFloat(p.pagos[i].conceptos[ii].monto),
							cuenta: {
								_id: p.cuenta._id.$id,
								descr: p.cuenta.descr,
								cod: p.cuenta.cod
							}
						};
						switch(p.pagos[i].conceptos[ii].tipo){
							case 'A':
								concepto.cuenta = {
									_id: p.cuenta._id.$id,
									descr: p.cuenta.descr,
									cod: p.cuenta.cod
								};
								break;
							case 'I':
								concepto.cuenta = {
									_id: p.conf.IGV._id.$id,
									descr: p.conf.IGV.descr,
									cod: p.conf.IGV.cod
								};
								break;
							case 'M':
								concepto.cuenta = {
									_id: p.conf.MOR._id.$id,
									descr: p.conf.MOR.descr,
									cod: p.conf.MOR.cod
								};
								break;
						}
						item.conceptos.push(concepto);
					}
					data.items.push(item);
				}
				if(p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val()!=''){
					if(parseFloat(p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val())!=0){
						var conc_mora_ = parseFloat(p.$w.find('[name=gridServ] [name^=conc_mora] [name=mora]').val());
						p.$w.find('[name=gridServ] [name^=conc_mora]').find('td:eq(3)').html('<b>'+ciHelper.formatMon(conc_mora_,p.moneda)+'</b>');
						var concepto = {
							concepto: 'COBRO DE MORAS',
							incumplimiento: true,
							monto: conc_mora_,
							cuenta: {
								_id: p.conf.MOR._id.$id,
								descr: p.conf.MOR.descr,
								cod: p.conf.MOR.cod
							}
						};
						data.items[data.items.length-1].conceptos.push(concepto);
					}
				}
				if(parseFloat(data.total)==0){
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El comprobante no puede tener como total 0!',
						type: 'error'
					});
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
					return K.msg({
						title: ciHelper.titles.infoReq,
						text: 'El total del comprobante no coincide con el total de la forma de pagar!',
						type: 'error'
					});
				}
				data.subtotal = K.round(parseFloat(data.total)/(1+(parseFloat(p.igv))),2);
				data.igv = K.round(parseFloat(data.total)-data.subtotal,2);
				K.sendingInfo();
				p.$w.find('#div_buttons button').attr('disabled','disabled');
				$.post('in/comp/save_comp',data,function(comp){
				//$.post('in/comp/save',data,function(){
					K.clearNoti();
					inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
					K.msg({title: ciHelper.titles.regiGua,text: 'Comprobante creado con &eacute;xito!'});
					/*
					 * AQUI ABRIR EL PDF
					 */
					K.windowPrint({
						id:'windowPrint',
						title: "Comprobante de Pago",
						url: "in/comp/print?print=1&_id="+comp._id.$id
					});
				},'json');
			}
		});
		new K.Panel({
			contentURL: 'in/movi/comp_mes',
			buttons: {
				'Generar Comprobante': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						if(p.total>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
							var detrac = p.total*p.detraccion;
							ciHelper.confirm(
								'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
								function () {
									p.save();
								},
								function () { $.noop(); }
							);
						}else{
							p.save();
						}
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMovi.init({inmueble: inInmu.dbRel(p.contrato.inmueble)});
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=texto_inmueble]').closest('.form-group').remove();
				p.$w.find('[name=texto_pago]').closest('.form-group').remove();
				new K.grid({
					$el: p.$w.find('[name=gridServ]'),
					search: false,
					pagination: false,
					cols: ['N&deg;','Servicios','','Importe Total'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				$.post('in/movi/get_var_mes',{
					tipo: p.contrato.inmueble.tipo._id.$id,
					dia_ini: parseInt(ciHelper.date.format.bd_d(p.contrato.fecini)),
					pagos: p.pagos
				},function(data){
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
					p.$w.find('[name=tipo]').html(p.contrato.inmueble.tipo.nomb);
					p.$w.find('[name=sublocal]').html(p.contrato.inmueble.sublocal.nomb);
					p.$w.find('[name=inmueble]').html(p.contrato.inmueble.direccion);
					p.$w.find('[name=titular]').html(mgEnti.formatName(p.contrato.arrendatario));
					p.$w.find('[name=mini_enti] .panel-title').html('PERSONA A QUIEN SE EMITE COMPROBANTE');
					p.$w.find('[name=btnSel]').click(function(){
						mgEnti.windowSelect({
							bootstrap: true,
							callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							}
						});
					});
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.contrato.arrendatario);
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
					p.$w.find('[name=fecemi]').val(K.date()).datepicker({format: 'yyyy-mm-dd'});
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						inMovi.init();
						return K.msg({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
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
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					for(var i=0,j=p.pagos.length; i<j; i++){
						var $row = $('<tr class="item" name="serv'+i+'">');
						$row.append('<td>'+(i+1)+'</td>');
						$row.append('<td>Pago de Cuota <b>'+p.pagos[i].num+'</b> correspondiente al Acta de Conciliaci&oacute;n N&deg;'+p.contrato.num+'</td>');
						$row.append('<td>');
						$row.append('<td>');
						p.$w.find('[name=gridServ] tbody').append($row);
						for(var ii=0; ii<p.pagos[i].conceptos.length; ii++){
							var tmp_conc = p.pagos[i].conceptos[ii];
							var $row = $('<tr class="item" name="conc_'+i+'_acta_'+ii+'">');
							$row.append('<td>');
							$row.append('<td>&nbsp;&nbsp;&nbsp;<i>'+tmp_conc.descr+'</i></td>');
							$row.append('<td>');
							$row.append('<td>'+ciHelper.formatMon(tmp_conc.monto,p.moneda)+'</td>');
							$row.data('monto',tmp_conc.monto).data('tipo',tmp_conc.tipo).data('data',tmp_conc);
							p.$w.find('[name=gridServ] tbody').append($row);
						}
					}
					i--;







					//



					var dia_ini_tmp = ciHelper.date.format.bd_d(p.pagos[i].fecven);
					var porc = 0,
					mes_n = ciHelper.date.format.bd_m(p.pagos[i].fecven),
					ano_n = ciHelper.date.format.bd_y(p.pagos[i].fecven);
					if(parseInt(dia_ini_tmp)!=1){
						var mes_n = parseInt(mes_n)-1,
						ano_n = parseInt(ano_n);
						if(mes_n==0){
							mes_n = 12;
							ano_n--;
						}
					}
					$.post('in/movi/get_mora',{
						mes: mes_n,
						ano: ano_n,
						fecini: ciHelper.date.format.bd_ymd(p.pagos[i].fecven)
					},function(porc){
						p.$w.find('[name=mora][data-mes='+porc.mes+'][data-ano='+porc.ano+']').val(porc.mora);
						p.calcTot();
						K.unblock();
					},'json');


					var $row = $('<tr class="item" name="conc_mora_">');
					$row.append('<td>');
					$row.append('<td>&nbsp;&nbsp;&nbsp;<i>Mora por incumplimiento</i></td>');
					$row.append('<td><input type="text" name="mora" size="7" value="0" data-mes="'+mes_n+'" data-ano="'+ano_n+'"/></td>');
					$row.append('<td>'+ciHelper.formatMon(0,p.moneda)+'</td>');
					$row.find('[name=mora]').val(porc).keyup(function(){
						p.calcTot();
					}).keyup();
					p.$w.find('[name=gridServ] tbody').append($row);




					//















					var $row = $('<tr class="item" name="totalGrid">');
					$row.append('<th colspan="3">Total</th>');
					$row.append('<th>');
					p.$w.find('[name=gridServ] table:last').append('<tfoot>');
					p.$w.find('[name=gridServ] tfoot').append($row);
					if(p.moneda=='D'){
						var $row = $('<tr class="item">');
						$row.append('<th colspan="3">Tasa de Cambio de Soles a D&oacute;lares</th>');
						$row.append('<th><input type="text" name="tasa" size="7" value="0"></th>');
						$row.find('[name=tasa]').val(p.tc).keyup(function(){
							p.calcTot();
						});
						p.$w.find('[name=gridServ] tfoot').append($row);
						var $row = $('<tr class="item" name="totalGridConv">');
						$row.append('<th colspan="3">Total en Soles</th>');
						$row.append('<th>');
						p.$w.find('[name=gridServ] tfoot').append($row);
					}
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td><div class="input-group col-sm-8">'+
						'<input class="form-control" name="tot" type="text" />'+
					'</div></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Efectivo Dolares*/
					var $row = $('<tr class="item" name="mon_dol">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td><div class="input-group col-sm-8">'+
						'<input class="form-control" name="tot" type="text" />'+
					'</div></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					});
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban">');
						$row.append('<td>Voucher '+
							'<input class="form-control" name="voucher" type="text" />'+
						'</td>');
						$row.append('<td>'+p.ctban[i].nomb+'</td>');
						$row.append('<td><div class="input-group col-sm-8">'+
							'<input class="form-control" name="tot" type="text" />'+
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
					p.calcTot();
					K.unblock();
				},'json');
			}
		});
	},
	/**************************************************************************************************************************
	*
	* DETALLE DE GARANTIAS DEL INQUILINO VS EL INMUEBLE
	*
	**************************************************************************************************************************/
	windowGarantia: function(p){
		if(p==null){ p = {}; }
		$.extend(p,{
			transferir: function(garantia){
				inMovi.windowSelectContrato({
					callback: function(data){
						ciHelper.confirm('&#191;Desea <b>Transferir</b> la garantia seleccionada <b>'+garantia.tipo+' '+garantia.num+'</b>&#63;',
						function(){
							K.sendingInfo();
							K.block();
							var new_cont = {
								destino: data._id.$id,
								origen: garantia.contrato,
								tipo: garantia.tipo,
								num: garantia.num,
								importe: garantia.importe
							};
							$.post('in/movi/save_trans',new_cont,function(){
								K.clearNoti();
								K.msg({title: 'Operaci&oacute;n realizada!',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
								K.unblock();
								K.closeWindow(p.$w.attr('id'));
								inMovi.windowGarantia(p);
							});
						},function(){
							$.noop();
						},'Transferencia de Garant&iacute;a');
					}
				});
			}
		});
		new K.Modal({
			id: 'windowDetails',
			title: 'Garantias de '+mgEnti.formatName(p.titular),
			content: '<div name="grid"></div>',
			allScreen: true,
			buttons: {
				'Generar Excel': {
					icon: 'fa-file-excel-o',
					type: 'info',
					f: function(){
						window.open('in/movi/print_garantias?titular='+p.titular._id.$id+'&inmueble='+p.inmueble._id.$id);
					}
				},
				'Cerrar': {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetails');
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['Fecha','Tipo de Doc.','Num.','Importe','Fecha de Devoluci&oacute;n','Importe de Devoluci&oacute;n','&nbsp;','&nbsp;'],
					onlyHtml: true,
					toolbarHTML: '<h3>GARANTIAS DEL INMUEBLE: '+p.inmueble.direccion+'</h3>',
					onContentLoaded: function(){
						$.post('in/movi/get_garantias',{
							titular: p.titular._id.$id,
							inmueble: p.inmueble._id.$id
						},function(data){
							if(data!=null){
								for(var i=0; i<data.length; i++){
									for(var j=0; j<data[i].garantias.length; j++){
										data[i].garantias[j].importe = data[i].garantias[j].importe.replace(/,/g , ".");
										var $row = $('<tr class="item">');
										$row.append('<td>'+ciHelper.date.format.bd_ymd(data[i].garantias[j].fec)+'</td>');
										$row.append('<td>'+inMovi.tipo_doc_gar[data[i].garantias[j].tipo]+'</td>');
										$row.append('<td>'+data[i].garantias[j].num+'</td>');
										$row.append('<td>'+ciHelper.formatMon(parseFloat(data[i].garantias[j].importe),data[i].garantias[j].moneda)+'</td>');
										if(data[i].garantias[j].dev_fec!=null)
											$row.append('<td>'+ciHelper.date.format.bd_ymd(data[i].garantias[j].dev_fec)+'</td>');
										else
											$row.append('<td>');
										$row.append('<td>'+data[i].garantias[j].dev_importe+'</td>');
										if(data[i].garantias[j].historia==null)
											$row.append('<td>&nbsp;</td>');
										else
											$row.append('<td><kbd>Fue Transferida</kbd></td>');
										$row.append('<td><button name="btnTrans" class="btn btn-info"><i class="fa fa-exchange"></i> Transferir</button></td>');
										$row.find('[name=btnTrans]').click(function(){
											var data = $(this).closest('.item').data('data');
											$.extend(data,{
												contrato: $(this).closest('.item').data('contrato')
											});
											p.transferir(data);
										});
										$row.data('data',data[i].garantias[j]).data('contrato',data[i]._id.$id);
										p.$w.find('[name=grid] tbody').append($row);
									}
								}
							}
						},'json');
					}
				});
			}
		});
	},
	/**************************************************************************************************************************
	*
	* SELECCION DE CONTRATO
	*
	**************************************************************************************************************************/
	windowSelectContrato: function(p){
		if(p==null){ p = {}; }
		new K.Modal({
			id: 'windowSelectCont',
			content: '<div name="tmp"></div>',
			allScreen: true,
			title: 'Seleccionar Contrato',
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
				p.$w = $('#windowSelectCont');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Inmueble','Titular','Inicio','Fin'],
					data: 'in/movi/lista_cont',
					params: {},
					itemdescr: 'contrato(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.inmueble.direccion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.titular)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecini)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
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
	['mg/serv','in/inmu','in/moti','cj/talo','cj/conc'],
	function(mgServ,inInmu,inMoti,cjTalo,cjConc){
		return inMovi;
	}
);
