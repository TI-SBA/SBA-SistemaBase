tdExpd = {
	dbRel:function(data){
		return {
			_id: data._id.$id,
			num: data.num
		}
	},
	states: {
		C: {
			descr: "Concluido",
			color: "#003265"
		},
		P: {
			descr: "Pendiente",
			color: "#CCCCCC"
		},
		A: {
			descr: "Aceptado",
			color: "#006532"
		},
		R: {
			descr: "Rechazado",
			color: "#CC0000"
		},
		F: {
			descr: "Enviado a Entidad Externa",
			color: '#CE45FF'
		}
	},
	/* Dias para vencimiento */
	days: 5,
	tiempo_modificacion: 6000,
	contextMenu: function(p){
		p.$row.contextMenu('conMenExpd', {
			onShowMenu: function(e, menu) {
				$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
				$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
				$(e.target).closest('.item').click();
				/*
				 * El expediente esta recibido
				 */
				if(p.rec!=null){
					$('#conMenExpd_acp, #conMenExpd_rec, #conMenExpd_acf', menu).remove();
				}else if(p.por!=null){
				/*
				 * El expediente esta por recibir
				 */
					$(/*'#conMenExpd_doc,*/'#conMenExpd_env,#conMenExpd_rcd,#conMenExpd_ape,#conMenExpd_con,#conMenExpd_ach,#conMenExpd_imp,#conMenExpd_ext,#conMenExpd_ret', menu).remove();
				}
				if(p.data.estado=='F'){
					$(/*'#conMenExpd_doc,*/'#conMenExpd_env,#conMenExpd_acp,#conMenExpd_rec,#conMenExpd_acf,#conMenExpd_con,#conMenExpd_ach,#conMenExpd_rcd,#conMenExpd_ape,#conMenExpd_imp,#conMenExpd_ext', menu).remove();
				}else{
					$('#conMenExpd_ret', menu).remove();
				}
				if(p.readOnly!=true){
					if(p.data.estado!='P'){
						$(/*'#conMenExpd_doc, */'#conMenExpd_con', menu).remove();
						if(p.data.flujos.reconsideracion!=null){
							$('#conMenExpd_rcd', menu).remove();
							if(p.data.flujos.apelacion!=null){
								$('#conMenExpd_ape', menu).remove();
							}
						}else{
							if(p.data.flujos.apelacion!=null){
								$('#conMenExpd_rcd, #conMenExpd_ape', menu).remove();
							}
						}
					}else{
						$('#conMenExpd_rcd, #conMenExpd_ape', menu).remove();
					}
					if(p.data.traslados[p.data.traslados.length-1].destino!=null){
						$('#conMenExpd_env', menu).remove();
						if(p.data.traslados[p.data.traslados.length-1].destino.feccon==null){
							$('#conMenExpd_acf', menu).remove();
						}else{
							$('#conMenExpd_acp, #conMenExpd_rec', menu).remove();
						}
					}else{
						$('#conMenExpd_acp, #conMenExpd_rec, #conMenExpd_acf', menu).remove();
					}
					if(p.data.traslados[p.data.traslados.length-1].origen.archivado!=null)
						$('#conMenExpd_ach', menu).remove();
					$('#conMenExpd_imp', menu).remove();
				}else{
					$(/*'#conMenExpd_doc,*/'#conMenExpd_env,#conMenExpd_acp,#conMenExpd_rec,#conMenExpd_acf,#conMenExpd_con,#conMenExpd_ach,#conMenExpd_rcd,#conMenExpd_ape,#conMenExpd_imp, #conMenExpd_ext, #conMenExpd_ret', menu).remove();
				}
				if(p.band=='arch'){
					$('#conMenExpd_ach, #conMenExpd_acp, #conMenExpd_rec, #conMenExpd_acf', menu).remove();
				}
				K.tmp = $(e.target).closest('.item');
				return menu;
			},
			bindings: {
				'conMenExpd_doc': function(t) {
					tdExpd.windowNewDoc({
						data: K.tmp.data('data'),
						callBack: tdExpd.saveDoc,
						num: K.tmp.data('data').num,
						concepto: K.tmp.data('data').concepto
					});
				},
				'conMenExpd_env': function(t) {
					tdExpd.windowSend({
						id: K.tmp.data('data')._id.$id,
						num: K.tmp.data('data').num
					});
				},
				'conMenExpd_ext': function(t) {
					tdExpd.windowSendOut({
						id: K.tmp.data('data')._id.$id,
						num: K.tmp.data('data').num
					});
				},
				'conMenExpd_ret': function(t) {
					tdExpd.windowSendIn({
						id: K.tmp.data('data')._id.$id,
						num: K.tmp.data('data').num
					});
				},
				'conMenExpd_acp': function(t) {
					$.post("td/expd/expdestado",{
						id: K.tmp.data('data')._id.$id,
						num: K.tmp.data('data').num,
						origen: {
							_id: K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion._id.$id,
							nomb: K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion.nomb
						},
						estado: "A",
						descr: "0",
						fl: "0"
					},function(){
						K.notification({text: "El expediente ha sido aceptado!"});
						K.closeWindow('windowDetailsExpd'+p.id);
						$('#pageWrapperLeft .ui-state-highlight').click();
						if(socket_con!=null){
							if(socket_con!=false){
								if(K.tmp.data('data')!=null){
									if(K.tmp.data('data').traslados!=null){
										if(K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1]!=null){
											if(K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion._id!=null){
												socket.emit('expd_estado', {
													id: K.tmp.data('data')._id.$id,
													origen: {
														_id: K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion._id.$id,
														nomb: K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion.nomb
													},
													num: K.tmp.data('data').num,
													estado: "A"
												}, {},function(){});
											}
										}
									}
								}
							}
						}
					},'json');
				},
				'conMenExpd_rec': function(t) {
					tdExpd.windowRechazar({
						id: K.tmp.data('data')._id.$id,
						num: K.tmp.data('data').num,
						origen: {
							_id: K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion._id.$id,
							nomb: K.tmp.data('data').traslados[K.tmp.data('data').traslados.length-1].origen.organizacion.nomb
						},
						estado: "R"
					});
				},
				'conMenExpd_acf': function(t) {
					$.post("td/expd/expdestado",{
						id: K.tmp.data('data')._id.$id,
						num: K.tmp.data('data').num,
						descr: "0",
						fl: "1"
					},function(){
						K.notification({text: "El expediente ha sido recepcionado f&iacute;sicamente!"});
						K.closeWindow('windowDetailsExpd'+p.id);
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				'conMenExpd_con': function(t) {
					tdExpd.windowConcluir({
						id: K.tmp.data('data')._id.$id,
						data: K.tmp.data('data')
					});
				},
				'conMenExpd_ach': function(t) {
					ciHelper.confirm(
						'Archivar expediente',
						function () {
							/*var params = {
								id: K.tmp.data('data')._id.$id,
								num: K.tmp.data('data').num,
								observ_arch: $('[name=observ_expd]').val()
							};
							console.log(params);
							*/
							$.post("td/expd/expdarchivar",{
								id: K.tmp.data('data')._id.$id,
								num: K.tmp.data('data').num
							},function(){
								K.notification({title: "Expediente Archivado",text: "El expediente ahora puede ser consultado desde la bandeja de Archivados!"});
								K.closeWindow('windowDetailsExpd'+p.id);
								$('#pageWrapperLeft .ui-state-highlight').click();
							},'json');
						},
						function () {
							//nothing
						}
					);
				},
				'conMenExpd_rcd': function(t) {
					$.post("td/expd/expdopen",{
						id: K.tmp.data('data')._id.$id,
						fl: "R"
					},function(){
						K.notification({title: "Cambio de Estado",text: 'Expediente Reabierto!'});
						K.closeWindow('windowDetailsExpd'+p.id);
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				'conMenExpd_ape': function(t) {
					$.post("td/expd/expdopen",{
						id: K.tmp.data('data')._id.$id,
						fl: "A"
					},function(){
						K.notification({title: "Cambio de Estado",text: 'Expediente Reabierto!'});
						K.closeWindow('windowDetailsExpd'+p.id);
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				}
			}
		});
	},
	windowDetailsExpd: function(p){
		p.cbDocs = function(data){
			var datos = {
				_id: p.id,
				ubicacion: p.ubicacion,
				data: data.data
			};
			datos.data.flujo = 'Iniciado';
			if(p.data.flujos.reconsideracion!=null) datos.data.flujo = 'Reconsiderado';
			if(p.data.flujos.apelacion!=null) datos.data.flujo = 'Apelado';
			$.post("td/expd/save_doc",datos,function(rpta){
				K.clearNoti();
				K.notification({title: ciHelper.titleMessages.regiGua,text: "Documento Adjuntado!"});
				var result = data.data;
				var $t = p.$w.find('[name=docRef]').clone();
				$t.find('[name=tdoc]').html( result.tipo_documento.nomb+' '+result.num );
				$t.find('[name=fecha]').html( ciHelper.dateFormatNowLong() );
				$t.find('[name=origen]').html(p.data.ubicacion.nomb );
				$t.find('[name=folios]').html( result.folios );
				$t.find('[name=asunto]').html( result.asunto );
				p.$w.find("[name=section3] fieldset").append( $t.children() );
			},'json');
		};
		$.post('td/expd/get','_id='+p.id,function(data){
			p.data = data;
			var buttons = {};
			if(p.readOnly!=true){
				if(p.data.estado=='P'){
					$.extend(buttons,{
						"Agregar Documento": function(){
							var params = new Object;
							params.data = p.data;
							params.callBack = p.cbDocs;
							//params.callBack = tdExpd.saveDoc;
							params.num = p.data.num;
							params.concepto = p.data.concepto;
							tdExpd.windowNewDoc(params);
							//K.closeWindow(p.$w.attr('id'));
						},
						"Concluir": function(){
							var params = new Object;
							params.id = p.data._id.$id;
							params.data = p.data;
							tdExpd.windowConcluir(params);
						}
					});
				}else{
					if(p.data.flujos.reconsideracion==null){
						if(p.data.flujos.apelacion==null){
							$.extend(buttons,{
								"Reconsideraci\xf3n": function(){
									var da = new Object;
									da.id = p.data._id.$id;
									da.fl = "R";
									$('#windowDetailsExpd'+p.id).find('.ui-dialog-buttonpane button').button('disable');
									$.post("td/expd/expdopen",da,function(){
										K.notification({title: "Cambio de Estado",text: 'Expediente Reabierto!'});
										K.closeWindow('windowDetailsExpd'+p.id);
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								"Apelaci\xf3n": function(){
									var da = new Object;
									da.id = p.data._id.$id;
									da.fl = "A";
									$('#windowDetailsExpd'+p.id).find('.ui-dialog-buttonpane button').button('disable');
									$.post("td/expd/expdopen",da,function(){
										K.notification({title: "Cambio de Estado",text: 'Expediente Reabierto!'});
										K.closeWindow('windowDetailsExpd'+p.id);
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								}
							});
						}
					}else{
						if(p.data.flujos.apelacion==null){
							$.extend(buttons,{
								"Apelaci\xf3n": function(){
								var da = new Object;
								da.id = p.data._id.$id;
								da.fl = "A";
									$('#windowDetailsExpd'+p.id).find('.ui-dialog-buttonpane button').button('disable');
								$.post("td/expd/expdopen",da,function(){
									K.notification({title: "Cambio de Estado",text: 'Expediente Reabierto!'});
									K.closeWindow('windowDetailsExpd'+p.id);
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
								}
							});
						}
					}
				}
				if(p.data.traslados[p.data.traslados.length-1].destino==null){
					$.extend(buttons,{
						"Enviar": function(){
							var params = new Object;
							params.id = p.data._id.$id;
							params.num = p.data.num;
							tdExpd.windowSend(params);
						}
					});
				}else{
					if(p.data.traslados[p.data.traslados.length-1].destino.feccon==null){
						if(p.por==true)
						$.extend(buttons,{
							"Aceptar": function(){
								var params = new Object;
								params.id = p.data._id.$id;
								params.num = p.data.num;
								params.origen = new Object;
								params.origen._id = p.data.traslados[p.data.traslados.length-1].origen.organizacion._id.$id;
								params.origen.nomb = p.data.traslados[p.data.traslados.length-1].origen.organizacion.nomb;
								params.estado = "A";
								params.descr = "0";
								params.fl = "0";
									$('#windowDetailsExpd'+p.id).find('.ui-dialog-buttonpane button').button('disable');
								$.post("td/expd/expdestado",params,function(){
									K.notification({text: "El expediente ha sido aceptado!"});
									K.closeWindow('windowDetailsExpd'+p.id);
									$('#pageWrapperLeft .ui-state-highlight').click();
									if(socket_con!=null){
										if(socket_con!=false)
											socket.emit('expd_estado', params, {},function(){});
									}
								});
							},
							"Rechazar": function(){
								var params = new Object;
								params.id = p.data._id.$id;
								params.num = p.data.num;
								params.origen = new Object;
								params.origen._id = p.data.traslados[p.data.traslados.length-1].origen.organizacion._id.$id;
								params.origen.nomb = p.data.traslados[p.data.traslados.length-1].origen.organizacion.nomb;
								params.estado = "R";
								tdExpd.windowRechazar(params);
							}
						});
					}else if(p.data.traslados[p.data.traslados.length-1].destino.fecrec==null){
						if(p.por==true)
						$.extend(buttons,{
							"Recibido": function(){
								var params = new Object;
								params.id = p.data._id.$id;
								params.num = p.data.num;
								params.descr = "0";
								params.fl = "1";
									$('#windowDetailsExpd'+p.id).find('.ui-dialog-buttonpane button').button('disable');
								$.post("td/expd/expdestado",params,function(){
									K.notification({text: "El expediente ha sido recepcionado f&iacute;sicamente!"});
									K.closeWindow('windowDetailsExpd'+p.id);
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
							}
						});
					}
				}
				if(p.data.traslados[p.data.traslados.length-1].origen.archivado==null){
					$.extend(buttons,{
						"Archivar": function(){
							var params = new Object;
							params.id = p.data._id.$id;
							params.num = p.data.num;
									$('#windowDetailsExpd'+p.id).find('.ui-dialog-buttonpane button').button('disable');
							$.post("td/expd/expdarchivar",params,function(){
								K.notification({title: "Expediente Archivado",text: "El expediente ahora puede ser consultado desde la bandeja de Archivados!"});
								K.closeWindow('windowDetailsExpd'+p.id);
								$('#pageWrapperLeft .ui-state-highlight').click();
							});
						}
					});
				}
			}
			$.extend(buttons,{
				'Imprimir': function(){
					var url = 'td/expd/print?id='+p.id;
					K.windowPrint({
						id:'windowLgOrdePrint',
						title: p.nomb,
						url: url
					});
				}
			});
			if(p.rec!=null){
				delete buttons['Aceptar'];
				delete buttons['Rechazar'];
				delete buttons['Recibido'];
				//delete buttons['Imprimir'];
			}else if(p.por!=null){
				delete buttons['Agregar Documento'];
				delete buttons['Enviar'];
				delete buttons['Reconsideración'];
				delete buttons['Apelación'];
				delete buttons['Concluir'];
				delete buttons['Archivar'];
				//delete buttons['Imprimir'];
			}
			if(p.data.estado=='F'){
				buttons = {
					'Cerrar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				};
			}
			K.Window({
				id: 'windowDetailsExpd'+p.id,
				title: 'Expediente '+p.data.num,
				contentURL: 'td/expd/details',
				icon: 'ui-icon-folder-open',
				width: 750,
				height: 460,
				buttons: buttons,
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowDetailsExpd'+p.id);
					p.$w.find('label').css('color','#656565');
					p.$w.find('.ui-layout-west a').bind('click',function(event){
						event.preventDefault();
						var $anchor = $(this);
						p.$w.find('.ui-layout-center').scrollTo( p.$w.find('[name='+$anchor.attr('href')+']'), 800 );
					}).eq(0).click();
					p.$w.find('[name=num]').html(p.data.num);
					p.$w.find('[name=observ_expd]').html(p.data.observ_expd);
					if(p.data.observ_conc!=null){
						p.$w.find('[name=observ_conc]').closest('tr').show();
						p.$w.find('[name=observ_conc]').html(p.data.observ_conc);
					}
					p.$w.find('[name=fecreg]').html( ciHelper.dateFormatLong(p.data.fecreg) );
					if(p.data.gestor!=null){
						if(p.data.gestor.organizacion!=null){
							p.$w.find('[name=gestor]').closest('tr').after('<tr><td></td><td><span name="gestor2"></span></td></tr>');
							p.$w.find('[name=gestor]').attr('name','organizacion');
							p.$w.find('[name=gestor2]').attr('name','gestor');
							p.$w.find('[name=gestor]').html( mgEnti.formatName(p.data.gestor) );
							p.$w.find('[name=organizacion]').html( p.data.gestor.organizacion.nomb );
						}else{
							p.$w.find('[name=gestor]').html( mgEnti.formatName(p.data.gestor) );
						}
						p.$w.find('[name=gestor]').wrap('<a>');
						if(p.data.gestor._id!=null){
							p.$w.find('[name=gestor]').closest('a').click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',p.data.gestor._id.$id).data('tipo_enti',p.data.gestor.tipo_enti).css('text-decoration','underline');
						}
					}
					p.$w.find('[name=concepto]').html(p.data.concepto);
					if(p.data.tupa!=null){
						p.$w.find('[name=concepto]').closest('tr').after('<tr><td></td><td><span class="ui-icon ui-icon-note"></span>&nbsp;<span name="tupa"></span></td></tr>');
						p.$w.find('[name=tupa]').html('TUPA').click(function(){
							var data = $(this).data('tupa');
							tdTupa.windowDetails({id: data.procedimiento._id.$id,nomb: data.procedimiento.titulo,modal: true});
						}).data('tupa',p.data.tupa).wrap('<a>').css('text-decoration','underline');
					}
					var instancia = "";
					if(p.data.flujos.apelacion!=null) instancia = 'Apelaci&oacute;n';
					else if(p.data.flujos.reconsideracion!=null) instancia = "Reconsideraci&oacute;n";
					else instancia = "Inicio";
					p.$w.find('[name=ins]').html( instancia );
					p.$w.find('[name=estado]').html( '<b>'+tdExpd.states[p.data.estado].descr+'</b>' ).css('color',tdExpd.states[p.data.estado].color);
					if(p.data.estado=='F')
						p.$w.find('[name=ubicacion]').html( mgEnti.formatName(p.data.traslados[p.data.traslados.length-1].origen.entidad_ext) );
					else
						p.$w.find('[name=ubicacion]').html( p.data.traslados[p.data.traslados.length-1].origen.organizacion.nomb );
					if(p.data.estado=='P'){
						if(p.data.fecven!=null){
							p.$w.find('[name=fecven]').html( ciHelper.dateFormatLong(p.data.fecven) + ' <span>Faltan ' + ciHelper.dateDiffNow(p.data.fecven) + ' d&iacute;as</span>' );
							if(ciHelper.dateDiffNow(p.data.fecven)<=tdExpd.days){
								p.$w.find('[name=fecven] span').css('color','red');
							}
						}else{
							p.$w.find('[name=fecven]').closest('tr').remove();
						}
						p.$w.find('[name=fecharpta]').closest('tr').remove();
					}else if(p.data.estado!='F'){
						p.$w.find('[name=fecven]').closest('tr').remove();
						p.$w.find('[name=fecharpta]').html( ciHelper.dateFormatLong(p.data.feccon) );
						p.$w.find('[name=fecharpta]').closest('tr').after('<tr><td></td><td><span name="rpta"></span></td></tr>');
						p.$w.find('[name=rpta]').html( p.data.evaluacion + ' <b>' + p.data.respuesta + '</b>' );
						if(p.data.respuesta=='Positivo') p.$w.find('[name=rpta]').css('color','green');
						else p.$w.find('[name=rpta]').css('color','red');
					}
					var inicio = p.$w.find('[name=inicio]');
					inicio.find('[name=fecini]').html( ciHelper.dateFormatLong(p.data.flujos.iniciacion.fecini) );
					if(p.data.flujos.iniciacion.fecfin!=null){
						inicio.find('[name=resolucion]').html( p.data.flujos.iniciacion.evaluacion + ' <b>' + p.data.flujos.iniciacion.respuesta + '</b>' );
						inicio.find('[name=fecfin]').html( ciHelper.dateFormatLong(p.data.flujos.iniciacion.fecfin) );
						if(p.data.flujos.iniciacion.respuesta=='Positivo') inicio.find('[name=resolucion]').css('color','green');
						else inicio.find('[name=resolucion]').css('color','red');
					}else
						inicio.find('tr:eq(1)').remove();
					var reconsidera = p.$w.find('[name=reconsidera]');
					if(p.data.flujos.reconsideracion!=null){
						reconsidera.find('[name=fecini]').html( ciHelper.dateFormatLong(p.data.flujos.reconsideracion.fecini) );
						if(p.data.flujos.reconsideracion.fecfin!=null){
							reconsidera.find('[name=resolucion]').html( p.data.flujos.reconsideracion.evaluacion + ' <b>' + p.data.flujos.reconsideracion.respuesta + '</b>' );
							reconsidera.find('[name=fecfin]').html( ciHelper.dateFormatLong(p.data.flujos.reconsideracion.fecfin) );
							if(p.data.flujos.reconsideracion.respuesta=='Positivo') reconsidera.find('[name=resolucion]').css('color','green');
							else reconsidera.find('[name=resolucion]').css('color','red');
						}else
							reconsidera.find('tr:eq(1)').remove();
					}else
						reconsidera.remove();
					var apela = p.$w.find('[name=apela]');
					if(p.data.flujos.apelacion!=null){
						apela.find('[name=fecini]').html( ciHelper.dateFormatLong(p.data.flujos.apelacion.fecini) );
						if(p.data.flujos.apelacion.fecfin!=null){
							apela.find('[name=resolucion]').html( p.data.flujos.apelacion.evaluacion + ' <b>' + p.data.flujos.apelacion.respuesta + '</b>' );
							apela.find('[name=fecfin]').html( ciHelper.dateFormatLong(p.data.flujos.apelacion.fecfin) );
							if(p.data.flujos.apelacion.respuesta=='Positivo') apela.find('[name=resolucion]').css('color','green');
							else apela.find('[name=resolucion]').css('color','red');
						}else
							apela.find('tr:eq(1)').remove();
					}else
						apela.remove();
					for(var i=0; i<p.data.documentos.length; i++){
						var result = p.data.documentos[i];
						var $t = p.$w.find('[name=docRef]').clone();
						$t.find('[name=tdoc]').html( result.tipo_documento.nomb+' '+result.num );
						$t.find('[name=fecha]').html( ciHelper.dateFormatLong(result.fecreg) );
						$t.find('[name=origen]').html( result.organizacion.nomb );
						$t.find('[name=folios]').html( result.folios );
						$t.find('[name=asunto]').html( result.asunto );
						if(i==(p.data.documentos.length-1)||i==0){
							if(result.trabajador!=null||i==0){
								var tmp_doc_trab = false;
								if(result.trabajador!=null)
									if(result.trabajador._id.$id==K.session.enti._id.$id) tmp_doc_trab = true;
								if(tmp_doc_trab||i==0){
									var duration = moment.duration(moment().diff(moment( ciHelper.date.format.bd_ymdhi(result.fecreg) ))),
									tiempo = duration.asMinutes();
									if(tiempo<tdExpd.tiempo_modificacion){
										$t.append('<button>Modificar Documento</button>');
										$t.find('button:last').data('id',p.data._id.$id).data('index',i).click(function(){
											var index = $(this).data('index'),
											_id = $(this).data('id');
											tdExpd.windowNewDoc({
												index: index,
												data: p.data,
												callBack: tdExpd.saveDoc,
												num: p.data.num,
												concepto: p.data.concepto
											});
										}).button({icons: {primary: 'ui-icon-pencil'}});
										$t.append('<button>Eliminar Documento</button>');
										$t.find('button:last').data('id',p.data._id.$id).data('index',i).data('nombre',result.tipo_documento.nomb+' '+result.num).click(function(){
											var index = $(this).data('index'),
											doc = $(this).data('nombre'),
											_id = $(this).data('id');
											ciHelper.confirm('&#191;Desea <b>Eliminar</b> eliminar el documento <b>'+doc+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('td/expd/delete_doc',{_id: _id,index: index},function(){
													K.clearNoti();
													K.notification({title: 'Documento Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													$('#pageWrapperLeft .ui-state-highlight').click();
													K.closeWindow('windowDetailsExpd'+_id);
												});
											},function(){
												$.noop();
											},'Deshabilitaci&oacute;n de Tipo de Documento');
										}).button({icons: {primary: 'ui-icon-trash'}});
									}
								}
							}
						}
						p.$w.find("[name=section3] fieldset").append( $t.children() );
					}
					for(var i=0; i<p.data.traslados.length; i++){
						var result = p.data.traslados[i];
						var $t = p.$w.find('[name=trasRef]').clone();
						if(result.origen.entidad_ext==null)
							$t.find('[name=origen]').html( result.origen.organizacion.nomb );
						else
							$t.find('[name=origen]').html( mgEnti.formatName(result.origen.entidad_ext) ).click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: 'P',modal: true});
							}).data('id',result.origen.entidad_ext._id.$id).css('text-decoration','underline').wrap('<a>');
						$t.find('[name=recibido]').html( ciHelper.dateFormatLong(result.origen.fecreg) );
						if(result.origen.entidad_ext==null)
							$t.find('[name=entidad]').html( mgEnti.formatName(result.origen.entidad) ).click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: 'P',modal: true});
							}).data('id',result.origen.entidad._id.$id).css('text-decoration','underline').wrap('<a>');
						else
							$t.find('[name=entidad]').html( mgEnti.formatName(result.origen.entidad_ext) ).click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: 'P',modal: true});
							}).data('id',result.origen.entidad_ext._id.$id).css('text-decoration','underline').wrap('<a>');
						if(result.destino!=null){
							$t.find('[name=enviado]').html( ciHelper.dateFormatLong(result.destino.fecenv) );
							if(result.destino.organizacion!=null)
								$t.find('[name=destino]').html( result.destino.organizacion.nomb );
							else{
								if(result.destino.entidad!=null)
								$t.find('[name=destino]').html( mgEnti.formatName(result.destino.entidad) ).click(function(){
									ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: 'P',modal: true});
								}).data('id',result.destino.entidad._id.$id).css('text-decoration','underline').wrap('<a>');
							}
								
							if(result.destino.feccon!=null){
								$t.find('[name=estadoTras]').html( tdExpd.states[result.destino.estado].descr ).css('color',tdExpd.states[result.destino.estado].color);;
								$t.find('[name=fechaTras]').html( ciHelper.dateFormatLong(result.destino.feccon) ).css('color',tdExpd.states[result.destino.estado].color);;
								if(result.destino.estado=='R') $t.find('table').append('<tr><td><label style="color:#656565">Motivo de rechazo</label></td><td colspan="3"><span>'+result.destino.observ+'</span></td></tr>');
							}
							if(result.copias!=null){
								for(var j=0; j<result.copias.length; j++){
									$t.find('[name=copias]').append( result.copias[j].organizacion.nomb );
									if((result.copias.length-1)!=j) $t.find('[name=copias]').append(', ');
								}
							}else
								$t.find('tr:eq(3)').remove();
						}else{
							if(result.origen.archivado!=null){
								$t.find('label:eq(2)').html('Archivado');
								$t.find('[name=enviado]').html( ciHelper.dateFormatLong(result.origen.fecarc) );
							}else{
								$t.find('label:eq(2)').html('');
							}
							$t.find('tr:eq(2),tr:eq(3)').remove();
						}
						p.$w.find("[name=section4] fieldset").append( $t.children() );
					}
					p.$w.layout({
						west__size:			150,
						west__resizable:	false,
						west__slidable:		false,
						west__closable:		false
					});
					p.$w.find('.ui-layout-center').css('overflow','hidden');
				}
			});
		},'json');
	},
	windowNewExp: function(p){
		if(p==null) p = new Object;
		p.cbGestor = function(data){
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
				p.$w.find('[name=lblDocIdent]').html( data.docident[0].tipo );
				p.$w.find('[name=dni]').html( data.docident[0].num );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( '' );
				p.$w.find('[name=lblDocIdent]').html( data.docident[0].tipo );
				p.$w.find('[name=dni]').html( data.docident[0].num );
			}
		};
		p.cbTupa = function(data){
			p.$w.find('[name=proc]').data('data',data);
			p.$w.find('[name=proc]').html( data.data.titulo ).click(function(){				
				var data = $(this).data('data');
				tdTupa.windowDetails({id: data.data._id.$id,nomb: data.data.titulo,modal: true});
			}).css('text-decoration','underline').wrap('<a>');
			p.$w.find('[name=mod]').html( data.data.modalidades[data.index].descr );
			p.$w.find('[name=plazo_tupa]').html( data.data.modalidades[data.index].aprueba.plazo );
		};
		p.cbDoc = function(data){
			K.clearNoti();
			var $row = p.$w.find('.gridReference').clone();
			$li = $('li',$row);
			$li.eq(0).html('<button name="btnEliminar">Eliminar</button>');
			$li.eq(1).html( data.num );
			$li.eq(2).html( data.tipo_documento.nomb );
			$li.eq(3).html( data.folios );
			var d = new Date();
			var month = d.getMonth() + 1;
			$li.eq(4).html( d.getDate() + '/' + month + '/' + d.getFullYear() );
			$row.wrapInner('<a class="item" href="javascript: void(0);" />');
			$row.find('a').data('data',data);
			$row.find('[name=btnEliminar]').click(function(){
				$(this).closest('.item').remove();
				K.notification({text: 'Documento eliminado!'});
			}).button({icons: {primary: 'ui-icon-closethick'},text: false});
			p.$w.find(".gridBody").append( $row.children() );
		};
		p.saveExpd = function(opts){
			K.clearNoti();
			var datos = new Object;
			var data = new Object;
			data.tipo = p.$w.find('[name=tipo_expd] :selected').val();
			var tmp = p.$w.find('[name=nomb]').data('data');
			if(tmp==null) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un gestor!',type: 'error'});
			data.gestor = mgEnti.dbRel(tmp);
			if(p.$w.find('[name=nomb]').data('orga')!=null){
				data.gestor.organizacion = {
					_id: p.$w.find('[name=nomb]').data('orga')._id.$id,
					nomb: p.$w.find('[name=nomb]').data('orga').nomb
				};
			}
			data.observ_expd = p.$w.find('[name=observ_expd]').val();
			if(p.$w.find('fieldset:eq(2) div:first').tabs("option","selected")==0 && p.$w.find('#tabs-ExpdNew1').length>0){
				tmp = p.$w.find('[name=proc]').data('data');
				if(tmp==null) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un procedimiento!',type: 'error'});
				if(tmp.data.modalidades.length<=1) data.concepto = tmp.data.titulo;
				else data.concepto = tmp.data.titulo + ' - ' + tmp.data.modalidades[tmp.index].descr;
				data.tupa = {
					_id: tmp._id.$id,
					anio: tmp.anio
				};
				data.tupa.procedimiento = {
					_id: tmp.data._id.$id,
					titulo: tmp.data.titulo,
					modalidad: {
						_id: tmp.data.modalidades[tmp.index]._id.$id,
						descr: tmp.data.modalidades[tmp.index].descr,
						plazo: tmp.data.modalidades[tmp.index].aprueba.plazo
					}
				};
				datos.dias = tmp.data.modalidades[tmp.index].aprueba.plazo;
			}else{
				data.concepto = p.$w.find('[name=concepto]').val();
				if(data.concepto==''){
					p.$w.find('[name=concepto]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un concepto!',type: 'error'});
				}
				var dias = p.$w.find('[name=plazo]').val();
				/*if(dias == ''){
					p.$w.find('[name=plazo]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un plazo!',type: 'error'});
				}*/
				datos.dias = dias;
			}
			var $docs = p.$w.find('.gridBody a');
			if($docs.length<=0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un documento!',type: 'error'});
			data.documentos = new Array;
			for(var i=0; i<$docs.length; i++){
				var doc = $docs.eq(i).data('data');
				data.documentos.push(doc);
			}
			datos.data = data;
			K.clearNoti();
			K.sendingInfo();
			p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
			$.post("td/expd/save",datos,function(rpta){
				K.clearNoti();
				K.notification({title: ciHelper.titleMessages.regiGua,text: "Expediente creado satisfactoriamente!"});
				$('#pageWrapperLeft .ui-state-highlight').click();
				K.closeWindow(p.$w.attr('id'));
				opts.cbSave(rpta);
			},'json');
		};
		if(p.gestor!=null)
			p.windId = 'windowNewExpe'+p.gestor._id.$id;
		else if(p.externo==true)
			p.windId = 'windowNewExpe';
		else
			p.windId = 'windowNewExpe'+K.session.enti._id.$id;
		new K.Window({
			id: p.windId,
			title: 'Crear Nuevo Expediente',
			contentURL: 'td/expd/new',
			icon: 'ui-icon-folder-open',
			width: 700,
			height: 450,
			buttons: {
				/*"Guardar": function(){
					p.saveExpd({cbSave: function(){$.noop();}});
				},*/
				"Guardar y Enviar": function(){
					p.saveExpd({cbSave: function(nuevo){
						tdExpd.windowSend({
							id: nuevo._id.$id,
							num: nuevo.num
						});
					}});
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				p.$w.find('[name=plazo]').numeric().spinner({step: 1,min: 0,max: 2000});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnBusGestor]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewGestor]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbGestor});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				/*if(p.noTupa==null){
					p.$w.find('fieldset li:first').remove();
					p.$w.find('#tabs-ExpdNew1').remove();
				}*/
				p.$w.find('fieldset:eq(2) div:first').tabs({
					active: 1
				});
				p.$w.find('[name=btnTupa]').click(function(){
					ciSearch.windowSearchTupa({$window: p.$w,callback: p.cbTupa,textSearch:p.$w.find('[name=textPros]').val()});									
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=textPros]').keypress(function(e){
					  if (e.keyCode == 13){
						  ciSearch.windowSearchTupa({$window: p.$w,callback: p.cbTupa,textSearch:p.$w.find('[name=textPros]').val()});
					  }
				});
				p.$w.find('[name=btnAgregarDoc]').click(function(){
					if(p.$w.find('fieldset:eq(2) div:first').tabs("option","selected")==0){
						tm = p.$w.find('[name=proc]').data('data');
						if(tm==null) concepto = ' ';
						else concepto = tm.data.titulo + ' - ' + tm.data.modalidades[tm.index].descr;
						tdExpd.windowNewDoc({num: '',concepto: concepto,$window: p.$w,callBack: p.cbDoc});
					}else{
						tdExpd.windowNewDoc({num: '',concepto: p.$w.find('[name=concepto]').val(),$window: p.$w,callBack: p.cbDoc});
					}
				}).css('float','right').button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('.grid:eq(0)').css('overflow','hidden');
				p.$w.find('.grid:eq(1)').scroll(function(){
					p.$w.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				if(p.gestor!=null){
					p.$w.find('td:first').hide();
					p.cbGestor(p.gestor);
				}else if(p.externo==true){
					$.noop();
				}else{
					p.$w.find('td:first').hide();
					p.cbGestor(K.session.enti);
					p.$w.find('table:first').append('<tr><td><label>Organizaci&oacute;n</label></td><td><span name="orga">'+K.session.enti.roles.trabajador.oficina.nomb+'</span></td></tr>');
					p.$w.find('[name=nomb]').data('orga',K.session.enti.roles.trabajador.oficina);
				}
			}
		});
	},
	saveDoc: function(p){
		var datos = new Object;
		datos.data = p.data;
		datos.data.flujo = 'Iniciado';
		if(p.expd!=null){
			datos._id = p.expd._id.$id;
			if(p.expd.flujos!=null){
				if(p.expd.flujos.reconsideracion!=null) datos.data.flujo = 'Reconsiderado';
				if(p.expd.flujos.apelacion!=null) datos.data.flujo = 'Apelado';
			}
		}
		$.post("td/expd/save_doc",datos,function(){
			K.clearNoti();
			K.notification({title: ciHelper.titleMessages.regiGua,text: "Documento Adjuntado!"});
			if(p.expd!=null){
				if(p.expd.estado=='P'){
					if(p.expd.tupa!=null){
						ciHelper.confirm('&#191;Desea <b>Concluir</b> el Expediente <b>'+p.expd.num+'</b>&#63;',
						function(){
							tdExpd.windowConcluir({
								id: p.expd._id.$id,
								data: p.expd
							});
						},function(){
							$.noop();
						},'Concluir el Expediente');
					}
				}
			}
			if(p.callback!=null){
				p.callback();
			}
		},'json');
	},
	windowNewDoc: function(p){
		p.fn_auto = function(){
			p.$w.find('[name=tipo]').autocomplete({
				source: p.tipos,
				focus: function( event, ui ) {
					p.$w.find('[name=tipo]').val( ui.item.label );
					p.$w.find('[name=tipo]').data('data',ui.item);
					return false;
				},
				select: function( event, ui ) {
					p.$w.find('[name=tipo]').val( ui.item.label );
					p.$w.find('[name=tipo]').data('data',ui.item);
					return false;
				}
			});
		};
		p.cbNew = function(valor){
			$.post('td/tdoc/all',function(tipos){
				for(var i=0; i<tipos.length; i++){
					tipos[i].label = tipos[i].nomb;
					if(tipos[i].nomb==valor){
						p.$w.find('[name=tipo]').val(valor).data('data',tipos[i]);
					}
				}
				p.tipos = tipos;
				p.$w.find('[name=tipo]').val( valor.nomb );
				p.$w.find('[name=tipo]').data('data',valor);
				p.fn_auto();
			},'json');
		};
		K.Modal({
			id: "windowNewDoc",
			title: "Nuevo Documento para Expediente "+p.num,
			contentURL: "td/expd/editdocu",
			icon: 'ui-icon-document',
			width : 510,
			height : 243,
			buttons: {
				"Agregar": function(){
					K.clearNoti();
					var data = {
						num: p.$w.find('[name=num]').val()
					};
					var tdoc = p.$w.find('[name=tipo]').data('data');
					if(tdoc==null){
						p.$w.find('[name=tipo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Tipo de Documento!',type: 'error'});
					}
					data.tipo_documento = {
						_id: tdoc._id.$id,
						nomb: tdoc.nomb
					};
					data.asunto = p.$w.find('[name=asunto]').val();
					data.folios = p.$w.find('[name=folio]').val();
					if(data.asunto==''){
						p.$w.find('[name=asunto]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar el asunto!',
							type: 'error'
						});
					}
					if(data.folios==''){
						p.$w.find('[name=folio]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar el n&uacute;mero de folios!',
							type: 'error'
						});
					}
					if(p.index!=null){
						data.index = p.index;
					}
					K.clearNoti();
					K.sendingInfo();
					$('#'+p.$w.attr('id')).dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					if(p.data!=null){
						var datos = {
							expd: p.data,
							data: data
						};
						p.callBack(datos);
					}else{
						p.callBack(data);
					}
					K.closeWindow(p.$w.attr('id'));
				},
				"Agregar y Nuevo": function(){
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
					tdExpd.windowNewDoc(p);
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowNewDoc");
				p.$w.find('[name=tipo]').focus();
				p.$w.find('[name=asunto]').val(p.concepto);
				p.$w.find('[name=tipo]').keyup(function(e){
					if(e.keyCode != 13) p.$w.find('[name=tipo]').removeData('data');
				});
				p.$w.find('[name=btnAgregarDoc]').click(function(){
					tdTdocs.windowNew({cbOnClose: p.cbNew});
				}).button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$.post('td/tdoc/all',function(tipos){
					for(var i=0; i<tipos.length; i++){
						tipos[i].label = tipos[i].nomb;
					}
					p.tipos = tipos;
					p.fn_auto();
					if(p.index!=null){
						p.$w.find('[name=tipo]').val( p.data.documentos[p.index].tipo_documento.nomb );
						p.$w.find('[name=tipo]').data('data',p.data.documentos[p.index].tipo_documento);
						p.$w.find('[name=num]').val( p.data.documentos[p.index].num );
						p.$w.find('[name=folio]').val( p.data.documentos[p.index].folios );
						p.$w.find('[name=asunto]').val( p.data.documentos[p.index].asunto );
					}
				},'json');
			}
		});
	},
	windowSend: function(p){
		p.cbDesti = function(data){
			p.$w.find('[name=orga]').data('_id',data._id.$id);
			p.$w.find('[name=orga]').data('nomb',data.nomb);
			p.$w.find('[name=orga]').html( data.nomb);
		};
		p.cbCopias = function(datas){
			for(var i=0,j=datas.length; i<j; i++){
				var data = datas[i],
				$row = p.$w.find('.gridReference').clone();
				if(p.$w.find('[name='+data._id.$id+']').length==0){
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnEliminar">Eliminar</button>');
					$li.eq(1).html( data.nomb );
					$row.wrapInner('<a class="item" name="'+data._id.$id+'" href="javascript: void(0);" />');
					$row.find('a').data('data',data);
					$row.find('[name=btnEliminar]').click(function(){
						$(this).closest('.item').remove();
						K.notification({text: 'Organismo eliminado!'});
					}).button({icons: {primary: 'ui-icon-closethick'},text: false});
					p.$w.find(".gridBody").append( $row.children() );
				}
			}
		};
		new K.Modal({
			id: 'windowSendExpd',
			title: 'Envio de Expediente: '+p.num,
			contentURL: 'td/expd/send',
			icon: 'ui-icon-mail-closed',
			width: 600,
			height: 300,
			buttons: {
				"Enviar": function(){
					K.clearNoti();
					var datos = new Object;
					var data = new Object;
					//Datos Destino
					if(p.tipo=='C'&&p.circular_env==false){
						datos.expd = p.id;
						datos.destinos = new Object;
						for(i=0;i<p.$w.find(".gridBody").find('a').length;i++){
							$item = p.$w.find(".gridBody").find('a').eq(i).data('data');
							$item._id = $item._id.$id;
							datos.destinos[i] = $item;
						}
						$.post("td/expd/expdsend_circular",datos,function(){
							K.clearNoti();
							K.notification({title: 'Operaci&oacute;n exitosa',text: "Expediente enviado!"});
							K.closeWindow(p.$w.attr('id'));
							K.closeWindow('windowDetailsExpd'+p.id);
							$('#pageWrapperLeft .ui-state-highlight').click();
						},'json');
					}else{
						data.organizacion = new Object;
						data.organizacion._id = p.$w.find('[name=orga]').data('_id');
						data.organizacion.nomb = p.$w.find('[name=orga]').data('nomb');
						//Datos del Expediente 
						datos.data = data;
						datos.expd = p.id;
						if(data.organizacion._id == "" || data.organizacion._id==null){
							p.$w.find('[name=btnBusOrga]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese Destinatario",type:"error"});
						}
						//Copias a Organizaciones
						datos.copias = new Object;
						for(i=0;i<p.$w.find(".gridBody").find('a').length;i++){
							$item = p.$w.find(".gridBody").find('a').eq(i).data('data');
							$item._id = $item._id.$id;
							datos.copias[i] = $item;
						}
						K.clearNoti();
						K.sendingInfo();
						p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
						$.post("td/expd/expdsend",datos,function(){
							K.clearNoti();
							K.notification({title: 'Operaci&oacute;n exitosa',text: "Expediente enviado!"});
							K.closeWindow(p.$w.attr('id'));
							K.closeWindow('windowDetailsExpd'+p.id);
							$('#pageWrapperLeft .ui-state-highlight').click();
							if(socket_con!=null){
								if(socket_con!=false){
									socket.emit('expd_mov', datos, {},function(){});
								}
							}
						},'json');
					}
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSendExpd');
				$.post('td/expd/get',{_id:p.id},function(data){
					if(data!=null){
						p.tipo = data.tipo;
						p.circular_env = data.circular_env;
						if(data.tipo=='C'&&data.circular_env==false){
							//alert('ESTA ES UNA CIRCULAR');
							p.$w.find('#N').remove();
							p.$w.find('[name=btnAgregarOrga]').click(function(){
								//ciSearch.windowSearchOrga_tramite({$window: p.$w,callback: p.cbCopias,multiple: true});
								mgOfic.windowSelect({$window: p.$w,callback: p.cbCopias,multiple: true});
							}).css('float','right').button({icons: {primary: 'ui-icon-plusthick'}});
						}else{
							p.$w.find('#C').remove();
							p.$w.find('[name=btnBusOrga]').click(function(){
								mgOfic.windowSelect({$window: p.$w,callback: p.cbDesti});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnAgregarOrga]').click(function(){
								mgOfic.windowSelect({$window: p.$w,callback: p.cbCopias,multiple: true});
							}).css('float','right').button({icons: {primary: 'ui-icon-plusthick'}});
							p.$w.find('.grid:eq(0)').css('overflow','hidden');
							p.$w.find('.grid:eq(1)').scroll(function(){
								p.$w.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
							});
						}
					}else{
						alert("ha ocurrido un error, comuniquese con el area de sistemas");
					}
				},'json');
			}
		});
	},
	windowRechazar: function(p){
		new K.Modal({
			id: "windowDescrExpd",
			title: "Ingrese Motivos del Rechazo",
			content: "<br><table width='370px'><tr><td width='60px'><label><b>Ingrese Observaciones del Expediente:</b></label></td></tr><tr><td><textarea name='descr' rows='11' cols='63'></textarea></td></tr></table>",
			icon: 'ui-icon-folder-notice',
			width : 490,
			height : 240,
			buttons: {
				"Aceptar": function(){
					K.clearNoti();
					var data = new Object;
					data.id = p.id;
					data.num = p.num;
					data.origen = p.origen;
					data.estado = p.estado;
					data.fl = "0";
					data.descr = p.$w.find('[name=descr]').val(); 
					if(data.descr == ""){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el motivo de rechazo!",type:"error"});
					}
					K.clearNoti();
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("td/expd/expdestado",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.closeWindow('windowDetailsExpd'+p.id);
						$('#pageWrapperLeft .ui-state-highlight').click();
						if(socket_con!=null){
							if(socket_con!=false){
								socket.emit('expd_estado', data, {},function(){});
							}
						}
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowDescrExpd");
				p.$w.find('[name=descr]').focus();
			}
		});
	},
	windowConcluir: function(p){
		new K.Modal({
			id: "windowConExpd"+p.id,
			title: "Concluir Expediente",
			contentURL: 'td/expd/expdcon',
			icon: 'ui-icon-circle-check',
			width : 400,
			height : 200,
			buttons: {
				"Concluir": function(){
					K.clearNoti();
					var data = new Object;
					data.id = p.id;
					data.estado = "C";
					data.evaluacion = p.$w.find('[name=rbtnEva]:checked').val();
					data.respuesta = p.$w.find('[name=rbtnRpta]:checked').val();
					data.observ_conc = p.$w.find('[name=observ_conc]').val();
					data.flujos = p.data.flujos;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("td/expd/expdconcluir",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.closeWindow('windowDetailsExpd'+p.id);
						K.notification({title: 'Expediente Concluido',text: "El expediente cambi&oacute; su estado exitosamente!"});
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowConExpd'+p.id);
				p.$w.find('#rbtnRpta').buttonset();
				p.$w.find('#rbtnEva').buttonset();
			}
		});
	},
	windowSendOut: function(p){
		p.cbEntidad = function(data){
			p.$w.find('[name=destino]').html( mgEnti.formatName(data) ).data('data',data);
		};
		new K.Modal({
			id: 'windowSendOut'+p.id,
			title: 'Enviar Expediente '+p.num+' fuera de la SBPA',
			icon: 'ui-icon-extlink',
			contentURL: 'td/expd/send_out',
			store: false,
			width: 420,
			height: 220,
			buttons: {
				'Enviar': function(){
					var data = new Object;
					data._id = p.id;
					var entidad = p.$w.find('[name=destino]').data('data');
					if(entidad!=null){
						data.entidad = mgEnti.dbRel(entidad);
					}else{
						p.$w.find('[name=btnSel]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Seleccionar una entidad",type:"error"});
					}
					data.observ = p.$w.find('[name=observ]').val();
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("td/expd/expdsendout",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Expediente Enviado',text: "El expediente fue enviado exitosamente!"});
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSendOut'+p.id);
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbEntidad});
				}).button({icons: {primary: 'ui-icon-contact'}});
				p.$w.find('[name=btnAgr]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbEntidad});
				}).button({icons: {primary: 'ui-icon-plusthick'}});		
			}
		});
	},
	windowSendIn: function(p){
		p.cbEntidad = function(data){
			p.$w.find('[name=destino]').html( mgEnti.formatName(data) ).data('data',data);
		};
		new K.Modal({
			id: 'windowSendOut'+p.id,
			title: 'Recibir Expediente '+p.num+' enviado fuera de la SBPA',
			icon: 'ui-icon-extlink',
			contentURL: 'td/expd/send_out',
			store: false,
			width: 420,
			height: 200,
			buttons: {
				'Recibir': function(){
					var data = new Object;
					data._id = p.id;
					data.observ = p.$w.find('[name=observ]').val();
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("td/expd/expdsendin",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Expediente Recibido',text: "El expediente fue recibido exitosamente!"});
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSendOut'+p.id);
				p.$w.find('tr:eq(0)').hide();	
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Expediente Tramite documentario',
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
					cols: ['','Numero','Gestor','Ubicacion actual','fecha de registro'],
					data: 'td/expd/lista',
					params: {},
					itemdescr: 'expediente(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.num+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.gestor)+'</td>');
						$row.append('<td>'+data.traslados[data.traslados.length-1].origen.organizacion.nomb+'</td>');
						$row.append('<td>'+moment(data.fecreg.sec, "X").format("DD/MM/YYYY")+'</td>');
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
	['td/tdocs','mg/ofic','mg/enti'],
	function(tdTdocs,mgOfic, mgEnti){
		return tdExpd;
	}
);