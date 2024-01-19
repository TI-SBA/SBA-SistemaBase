/*******************************************************************************
control de horarios asistencia */
peCoasInci = {
	states: [
 		{
 			descr: "Asistido",
 			color: 'blue'
 		},
 		{
 			descr: "Incidencia clasificada",
 			color: 'green'
 		},
 		{
 			descr: "Incidencia sin clasificar",
 			color: 'red'
 		}
 	],
	init: function(p){
		p = {
			date: new Date(),
			feriados: [],
			turno: [],
			asig: [],
			del: [],
			dias: []
		};
		p.date.setHours(0);
		p.date.setMinutes(0);
		p.validarLastHour = function(hour_fin){
			if(hour_fin.getHours()=='0'&&hour_fin.getMinutes()=='0'){
				hour_fin.setHours('24');
				hour_fin.setMinutes('00');
			}
			return hour_fin;
		};
		p.calc = function(rpta){
			p.$l.find('[name=btnTra]').data('data',null);
			p.$l.find('[name=btnTra]').data('start',null);		
			p.inci = [];
			if(rpta.inci!=null){
				for(var i=0; i<rpta.inci.length; i++){
					p.inci.push({
						title: rpta.inci[i].tipo.nomb,
						allDay: false,
						start: new Date(rpta.inci[i].fecini.sec*1000),
						end: new Date(rpta.inci[i].fecfin.sec*1000),
						backgroundColor: 'green',
						tipo: 'R',
						mid: rpta.inci[i]._id.$id,
						data: rpta.inci[i]
					});
				}
			}
			p.dias = [];
			p.sec = false;
			data = rpta.asig;
			if(data!=null){
				p.asig = [];
				for(var i=0; i<data.length; i++){
					if(data[i].programado!=null) var daytmp = ciHelper.date.format.dayBDNotHour(new Date(data[i].programado.inicio.sec*1000));
					else var daytmp = ciHelper.date.format.dayBDNotHour(new Date(data[i].ejecutado.entrada.fecreg.sec*1000));
					if(p.dias[daytmp]==null)
						p.dias[daytmp] = {
							prog: [],
							asis: []
						};
					if(data[i].programado!=null){
						p.dias[daytmp].prog.push({
							inicio: new Date(data[i].programado.inicio.sec*1000),
							fin: p.validarLastHour(new Date(data[i].programado.fin.sec*1000))
						});
					}
					if(data[i].ejecutado!=null){
						var eje = {
							entrada: new Date(data[i].ejecutado.entrada.fecreg.sec*1000)
						};
						if(data[i].ejecutado.salida!=null)
							eje.salida = new Date(data[i].ejecutado.salida.fecreg.sec*1000);
						p.dias[daytmp].asis.push(eje);
						if(data[i].ejecutado.salida==null) color = 'gray';
						if(data[i].ejecutado.salida!=null){
							color = 'blue';
							var fin = new Date(data[i].ejecutado.salida.fecreg.sec*1000),
							title = data[i].ejecutado.entrada.equipo.local.descr;
						}else if(ciHelper.date.format.dayBDNotHour(new Date(data[i].ejecutado.entrada.fecreg.sec*1000))!=ciHelper.date.format.dayBDNotHour(new Date())){
							var ini = new Date(data[i].ejecutado.entrada.fecreg.sec*1000),
							fin = new Date(ini.getTime() + (3600 * 1000)),
							title = 'Sin Cierre';
						}else{
							var fin = new Date(),
							title = data[i].ejecutado.entrada.equipo.local.descr;
						}
						if(title!='Sin Cierre')
						p.asig.push({
							title: title,
							allDay: false,
							start: new Date(data[i].ejecutado.entrada.fecreg.sec*1000),
							end: p.validarLastHour(fin),
							backgroundColor: color
						});
					}
					p.sec = true;
				}
			}else p.asig = [];
			for(x in p.dias){
				p.dias[x].prog.sort(function(a,b){return (a.inicio== b.inicio ? 0 : (a.inicio > b.inicio) ? 1 : -1 );});
				p.dias[x].asis.sort(function(a,b){return (a.entrada== b.entrada ? 0 : (a.entrada > b.entrada) ? 1 : -1 );});
			}
			p.$l.find('[name=btnTra]').data('data',{asig:p.asig,sec:p.sec,dias:p.dias,inci:p.inci});
			p.$l.find('[name=btnTra]').data('start',rpta);			
		};
		if($('#pageWrapper [child=coas]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pe/navg/coas',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="coas" />');
					$p.find("[name=peCoas]").after( $row.children() );
				}
				$p.find('[name=peCoas]').data('coas',$('#pageWrapper [child=coas]:first').data('coas'));
				$p.find('[name=peCoasInci]').click(function(){ peCoasInci.init(); }).addClass('ui-state-highlight');
				$p.find('[name=peCoasHora]').click(function(){ peCoasHora.init(); });
				$p.find('[name=peCoasAsis]').click(function(){ peCoasAsis.init(); });
				$p.find('[name=peCoasProg]').click(function(){ peCoasProg.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'peCoasInci',
			titleBar: {
				title: 'Control de Asistencia: Incidencias'
			}
		});

		new K.Panel({
			id: 'westPanel',
			position: 'left',
			contentURL: 'pe/hora/calen',
			onContentLoaded: function(){
				p.$l = $('#westPanel');
				p.$l.find('[name=btnTra]').data('data',{asig:[],sec:false,dias:[],inci:[]});
				p.$l.find('[name=btnTra]').click(function(){
					K.notification({text: 'Debe seleccionar una entidad para ver sus incidencias!',hide: false,type: 'info'});
					ciSearch.windowSearchEnti({callback: function(data){
						K.clearNoti();
						p.$cal.fullCalendar( 'removeEvents' );
						p.enti = data;
						p.$l.find('[name=nomb]').html(ciHelper.enti.formatName(data));
						p.$l.find('[name=cargo]').html(data.roles.trabajador.cargo.nomb);
						p.$l.find('[name=tarjeta]').html(data.roles.trabajador.cod_tarjeta);
						p.$l.find('[name=local]').html(data.roles.trabajador.local.descr);
						p.$l.find('[name=fecref]').change();
					},filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.trabajador',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}}).click();
				p.$l.find('[name=btnGuardar]').remove();
				p.$l.find('.ui-layout-south fieldset').css('padding','0px');
				for(var i=0,j=peCoasInci.states.length; i<j; i++){
					var $row = p.$l.find('.gridReference:last').clone();
					$row.find('li:eq(0)').css('background',peCoasInci.states[i].color).addClass('vtip').attr('title',peCoasInci.states[i].descr);
					$row.find('li:eq(1)').html(peCoasInci.states[i].descr);
					$row.wrapInner('<a class="item" />');
		        	p.$l.find(".gridBody:last").append( $row.children() );
				}
				p.$l.find('[name=fecref]').val(ciHelper.dateFormatNowBDNotHour())
				.datepicker().change(function(){
			    	 p.$cal.fullCalendar('gotoDate', $(this).datepicker('getDate'));
			    	 if(p.enti!=null)
		    		 	$.post('pe/hora/all_periodo',{
							enti: p.enti._id.$id,
							ini: ciHelper.date.format.dayBD(p.$cal.fullCalendar('getView').start),
							fin: ciHelper.date.format.dayBD(p.$cal.fullCalendar('getView').end)
						},function(rpta){
							p.calc(rpta);
							p.$cal.fullCalendar( 'refetchEvents' );
						},'json');
			    });
			}
		});
		new K.Panel({
			id: 'mainPanel',
			content: '<div name="calendar"></div>',
			onContentLoaded: function(){
				p.$mainPanel = $('#mainPanel');
				p.$cal = p.$mainPanel.find('[name=calendar]').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek'
					},
					slotMinutes: 10,
					defaultView: 'month',
					eventSources: [
					    {
				            events: function(start, end, callback) {
				                //callback(p.asig);    
						if(p.$l!=null)
					            	if(p.$l.find('[name=btnTra]').data('data')!=null)
								callback(p.$l.find('[name=btnTra]').data('data').asig);
				            },
				            textColor: 'white'
				        },
					    {
				            events: function(start, end, callback) {
				                //callback(p.inci);
						if(p.$l!=null)
					                if(p.$l.find('[name=btnTra]').data('data')!=null)
								callback(p.$l.find('[name=btnTra]').data('data').inci);
				            }
				        },
					    {
				            events: function(start, end, callback, data, check) {
				            	//console.log('==>');
				            	//console.log(p.dias);
						if(p.$l==null) return false;
					        	data = [];
					        	check = function(inci){
					        		if(inci.start!=null&&inci.end!=null){
					        			if(ciHelper.date.format.dayBD(inci.start)!=ciHelper.date.format.dayBD(inci.end)){
						        			if(p.$l.find('[name=btnTra]').data('data').inci.length>0){
						        				sectmp = true;
				        						for(var ii=0; ii<p.$l.find('[name=btnTra]').data('data').inci.length; ii++){
				        							if(ciHelper.date.format.dayBD(inci.start)==ciHelper.date.format.dayBD(p.$l.find('[name=btnTra]').data('data').inci[ii].start)&&ciHelper.date.format.dayBD(inci.end)==ciHelper.date.format.dayBD(p.$l.find('[name=btnTra]').data('data').inci[ii].end)){
				        								sectmp = false;
				        								ii = p.$l.find('[name=btnTra]').data('data').inci.length;
				        							}
				        						}
				        						if(sectmp==true) data.push(inci);
				        					}else data.push(inci);
				        				}
			        				}
				        		};
					        	if(p.$l.find('[name=btnTra]').data('data').sec==true){
					        		var tmp_totdias = (parseFloat(ciHelper.date.diff(end,start))/60/60/24);
					        		for(var i=0; i<tmp_totdias; i++){
						        		var dia = new Date(start.getTime() + (i * 24 * 3600 * 1000)),
						        		diaForm = ciHelper.date.format.dayBDNotHour(dia);
						        		if(p.$l.find('[name=btnTra]').data('data').dias[diaForm]!=null){
						        			var tmp = p.$l.find('[name=btnTra]').data('data').dias[diaForm];
						        			if(tmp.asis.length<1){
						        				for(var j=0; j<tmp.prog.length; j++){
						        					check({
														title: 'Sin clasificar - Inasistencia',
														allDay: false,
														start: tmp.prog[j].inicio,
														end: tmp.prog[j].fin,
														tipo: 'I',
														backgroundColor: 'red'
													});
						        				}
						        			}else{
						        				if(tmp.prog.length<1){
						        					for(var j=0; j<tmp.asis.length; j++){
							        					check({
															title: 'Sin clasificar - Tiempo extra',
															allDay: false,
															start: tmp.asis[j].entrada,
															end: tmp.asis[j].salida,
															tipo: 'I',
															backgroundColor: 'red'
														});
							        				}
						        				}
						        				for(var j=0; j<tmp.asis.length; j++){
						        					var asis = tmp.asis[j];
						        					for(var k=0; k<tmp.prog.length; k++){
						        						switch(ciHelper.date.compare(asis.entrada,tmp.prog[k].inicio)){
						        							case 0:
						        								if(asis.salida!=null){
						        									if(ciHelper.date.compare(asis.salida,tmp.prog[k].fin)==1){
										        						if(k<(tmp.prog.length-1)){
								        									if(ciHelper.date.compare(tmp.prog[k+1].inicio,asis.salida)==1){
													        					check({
																					title: 'Sin clasificar - Exceso',
																					allDay: false,
																					end: tmp.prog[k+1].inicio,
																					start: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
													        					if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==1){
														        					check({
																						title: 'Sin clasificar - Salida temprana',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.prog[k+1].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
													        					}else if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==-1){
													        						check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: asis.salida,
																						start: tmp.prog[k+1].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
													        					}
													        					tmp.prog.splice(k,1);
								        									}else if(ciHelper.date.compare(tmp.prog[k+1].inicio,asis.salida)==-1){
								        										check({
																					title: 'Sin clasificar - Exceso',
																					allDay: false,
																					end: asis.salida,
																					start: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
								        									}
										        						}else{
												        					check({
																				title: 'Sin clasificar - Exceso',
																				allDay: false,
																				end: asis.salida,
																				start: tmp.prog[k].fin,
																				tipo: 'I',
																				backgroundColor: 'red'
																			});
										        						}
											        					k = tmp.prog.length;
						        									}else if(ciHelper.date.compare(asis.salida,tmp.prog[k].fin)==-1){
						        										if(j<(tmp.asis.length-1)){
								        									if(ciHelper.date.compare(tmp.prog[k].fin,tmp.asis[j+1].entrada)==1){
											        							check({
																					title: 'Sin clasificar - No trabajado',
																					allDay: false,
																					start: asis.salida,
																					end: tmp.asis[j+1].entrada,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
								        									}else{
											        							check({
																					title: 'Sin clasificar - Salida temprana',
																					allDay: false,
																					start: asis.salida,
																					end: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
								        									}
										        						}else{
										        							check({
																				title: 'Sin clasificar - Salida temprana',
																				allDay: false,
																				start: asis.salida,
																				end: tmp.prog[k].fin,
																				tipo: 'I',
																				backgroundColor: 'red'
																			});
										        						}
											        					k = tmp.prog.length;
						        									}
						        								}else{
										        					check({
																		title: 'Sin clasificar - Sin cerrar',
																		allDay: false,
																		start: asis.entrada,
																		end: new Date(asis.entrada.getTime() + (3600 * 1000)),
																		tipo: 'I',
																		backgroundColor: 'red'
																	});
						        								}
						        								break;
						        							case 1:
						        								if(asis.salida!=null){
						        									if(ciHelper.date.compare(asis.salida,tmp.prog[k].inicio)==1){
											        					check({
																			title: 'Sin clasificar - Tardanza',
																			allDay: false,
																			end: asis.entrada,
																			start: tmp.prog[k].inicio,
																			tipo: 'I',
																			backgroundColor: 'red'
																		});
											        					if(ciHelper.date.inRange(asis.salida,tmp.prog[k].inicio,tmp.prog[k].fin)==false){
											        						if(k<(tmp.prog.length-1)){
											        							if(ciHelper.date.compare(asis.salida,tmp.prog[k+1].inicio)==1){
											        								check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: tmp.prog[k+1].inicio,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
														        					if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==1){
															        					check({
																							title: 'Sin clasificar - Salida temprana',
																							allDay: false,
																							start: asis.salida,
																							end: tmp.prog[k+1].fin,
																							tipo: 'I',
																							backgroundColor: 'red'
																						});
														        					}else if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==-1){
														        						check({
																							title: 'Sin clasificar - Exceso',
																							allDay: false,
																							end: asis.salida,
																							start: tmp.prog[k+1].fin,
																							tipo: 'I',
																							backgroundColor: 'red'
																						});
														        					}
														        					tmp.prog.splice(k,1);
									        									}else{
									        										check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: asis.salida,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
													        					check({
																					title: 'Sin clasificar - Exceso',
																					allDay: false,
																					end: asis.salida,
																					start: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
											        						}
												        					tmp.prog.splice(k,1);
											        					}else{
											        						if(j<(tmp.asis.length-1)){
											        							if(ciHelper.date.compare(tmp.prog[k].fin,tmp.asis[j+1].entrada)==1){
												        							check({
																						title: 'Sin clasificar - No trabajado',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.asis[j+1].entrada,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}else{
												        							check({
																						title: 'Sin clasificar - Salida temprana',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
													        					check({
																					title: 'Sin clasificar - Salida temprana',
																					allDay: false,
																					start: asis.salida,
																					end: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
											        						}
											        					}
											        					k = tmp.prog.length;
						        									}else{
											        					check({
																			title: 'Sin clasificar - Hora extra',
																			allDay: false,
																			end: asis.salida,
																			start: asis.entrada,
																			tipo: 'I',
																			backgroundColor: 'red'
																		});
											        					k = tmp.prog.length;
						        									}
						        								}else{
										        					check({
																		title: 'Sin clasificar - Sin cerrar',
																		allDay: false,
																		start: asis.entrada,
																		end: new Date(asis.entrada.getTime() + (3600 * 1000)),
																		tipo: 'I',
																		backgroundColor: 'red'
																	});
						        								}
						        								break;
						        							case -1:
						        								if(asis.salida!=null){
						        									if(ciHelper.date.compare(asis.salida,tmp.prog[k].inicio)==1){
											        					check({
																			title: 'Sin clasificar - Adelanto',
																			allDay: false,
																			start: asis.entrada,
																			end: tmp.prog[k].inicio,
																			tipo: 'I',
																			backgroundColor: 'red'
																		});
											        					if(ciHelper.date.inRange(asis.salida,tmp.prog[k].inicio,tmp.prog[k].fin)==false){
											        						if(k<(tmp.prog.length-1)){
											        							if(ciHelper.date.compare(asis.salida,tmp.prog[k+1].inicio)==1){
											        								check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: tmp.prog[k+1].inicio,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
														        					k++;
														        					if(ciHelper.date.compare(tmp.prog[k].fin,asis.salida)==1){
															        					check({
																							title: 'Sin clasificar - Salida temprana',
																							allDay: false,
																							start: asis.salida,
																							end: tmp.prog[k].fin,
																							tipo: 'I',
																							backgroundColor: 'red'
																						});
														        					}else if(ciHelper.date.compare(tmp.prog[k].fin,asis.entrada)==-1){
														        						check({
																							title: 'Sin clasificar - Exceso',
																							allDay: false,
																							end: asis.salida,
																							start: tmp.prog[k].fin,
																							tipo: 'I',
																							backgroundColor: 'red'
																						});
														        					}
														        					k--;
														        					tmp.prog.splice(k,1);
									        									}else{
									        										check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: asis.salida,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
													        					check({
																					title: 'Sin clasificar - Exceso',
																					allDay: false,
																					end: asis.salida,
																					start: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
											        						}
											        					}else{
											        						if(j<(tmp.asis.length-1)){
											        							if(ciHelper.date.compare(tmp.prog[k].fin,tmp.asis[j+1].entrada)==1){
												        							check({
																						title: 'Sin clasificar - No trabajado',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.asis[j+1].entrada,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}else{
												        							check({
																						title: 'Sin clasificar - Salida temprana',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
													        					check({
																					title: 'Sin clasificar - Salida temprana',
																					allDay: false,
																					start: asis.salida,
																					end: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
											        						}
											        					}
											        					k = tmp.prog.length;
						        									}else{
											        					check({
																			title: 'Sin clasificar - Hora extra',
																			allDay: false,
																			end: asis.salida,
																			start: asis.entrada,
																			tipo: 'I',
																			backgroundColor: 'red'
																		});
											        					k = tmp.prog.length;
						        									}
						        								}else{
										        					check({
																		title: 'Sin clasificar - Sin cerrar',
																		allDay: false,
																		start: asis.entrada,
																		end: new Date(asis.entrada.getTime() + (3600 * 1000)),
																		tipo: 'I',
																		backgroundColor: 'red'
																	});
						        								}
						        								break;
						        						}
						        					}
						        				}
						        			}
						        		}
						        	}
					        	}
			            		callback(data);
				            },
				            color: 'green',
				            textColor: 'white'
				        }
					],
					eventClick: function(event){
						if(event.tipo=='I'){
							new K.Modal({
								id: 'modalTurno',
								title: 'Clasificar Incidencia',
								contentURL: 'pe/hora/sinclas',
								height: 120,
								width: 440,
								icon: 'ui-icon-calendar',
								buttons: {
									"Guardar": function(){
										var tipo = p.$mod.find('[name=tipo]').data('data');
										if(tipo==null){
											p.$mod.find('[name=btnTipo]').click();
											return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de incidencia!',type: 'error'});
										}
										var data = {
											fecini: ciHelper.date.format.dayBD(event.start),
											fecfin: ciHelper.date.format.dayBD(event.end),
											trabajador: {
												_id: p.enti._id.$id,
												tipo_enti: p.enti.tipo_enti,
												nomb: p.enti.nomb,
												appat: p.enti.appat,
												apmat: p.enti.apmat
											},
											tipo: {
												_id: tipo._id.$id,
												nomb: tipo.nomb,
												tipo: tipo.tipo,
												goce_haber: tipo.goce_haber,
												subsidiado: tipo.subsidiado,
												todo: tipo.todo
											},
											observ: p.$mod.find('[name=observ]').val()
										};
										K.sendingInfo();
										p.$mod.find('.ui-dialog-buttonpane button').button('disable');
										$.post('pe/hora/save_inci',data,function(json){
											K.clearNoti();
											K.closeWindow(p.$mod.attr('id'));
											K.notification({title: ciHelper.titleMessages.regiAct,text: 'La incidencia ha sido actualizada con &eacute;xito!'});
											/*p.$cal.fullCalendar( 'removeEvents' , event._id );
											p.inci.push({
												title: json.tipo.nomb,
												allDay: false,
												start: new Date(json.fecini.sec*1000),
												end: new Date(json.fecfin.sec*1000),
												backgroundColor: 'green',
												tipo: 'R',
												mid: json._id.$id,
												data: json
											});
											p.$cal.fullCalendar('refetchEvents');*/
											$('#westPanel').find('[name=fecref]').change();
										},'json');
									},
									"Cerrar": function(){
										K.closeWindow(p.$mod.attr('id'));
									}
								},
								onClose: function(){ p.$mod = null; },
								onContentLoaded: function(){
									p.$mod = $('#modalTurno');
									p.$mod.find('[name=btnTipo]').click(function(){
										peTipo.windowSelect({callback: function(data){
											p.$mod.find('[name=tipo]').html(data.nomb).data('data',data);
										}});
									}).button({icons: {primary: 'ui-icon-search'}});
								}
							});
						}else if(event.tipo=='R'){
							new K.Modal({
								id: 'modalTurno',
								title: 'Editar Incidencia',
								contentURL: 'pe/hora/sinclas',
								height: 120,
								width: 440,
								icon: 'ui-icon-calendar',
								buttons: {
									"Guardar": function(){
										var tipo = p.$mod.find('[name=tipo]').data('data');
										if(tipo==null){
											p.$mod.find('[name=btnTipo]').click();
											return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de incidencia!',type: 'error'});
										}
										var data = {
											_id: event.mid,
											fecini: ciHelper.date.format.dayBD(event.start),
											fecfin: ciHelper.date.format.dayBD(event.end),
											trabajador: {
												_id: p.enti._id.$id,
												tipo_enti: p.enti.tipo_enti,
												nomb: p.enti.nomb,
												appat: p.enti.appat,
												apmat: p.enti.apmat
											},
											tipo: {
												_id: tipo._id.$id,
												nomb: tipo.nomb,
												tipo: tipo.tipo,
												goce_haber: tipo.goce_haber,
												subsidiado: tipo.subsidiado,
												todo: tipo.todo
											},
											observ: p.$mod.find('[name=observ]').val()
										};
										K.sendingInfo();
										p.$mod.find('.ui-dialog-buttonpane button').button('disable');
										$.post('pe/hora/save_inci',data,function(json){
											K.clearNoti();
											K.closeWindow(p.$mod.attr('id'));
											K.notification({title: ciHelper.titleMessages.regiAct,text: 'La incidencia ha sido actualizada con &eacute;xito!'});
											for(var jj=0; jj<p.inci.length; jj++){
												if(event.mid==p.inci[jj].mid){
													p.inci[jj] = {
														title: json.tipo.nomb,
														allDay: false,
														start: new Date(json.fecini.sec*1000),
														end: new Date(json.fecfin.sec*1000),
														backgroundColor: 'green',
														tipo: 'R',
														mid: event.mid,
														data: json
													};
													jj = p.inci.length;
												}
											}
											p.$cal.fullCalendar('refetchEvents');
										},'json');
									},
									"Cerrar": function(){
										K.closeWindow(p.$mod.attr('id'));
									}
								},
								onClose: function(){ p.$mod = null; },
								onContentLoaded: function(){
									p.$mod = $('#modalTurno');
									p.$mod.find('[name=btnTipo]').click(function(){
										peTipo.windowSelect({callback: function(data){
											p.$mod.find('[name=tipo]').html(data.nomb).data('data',data);
										}});
									}).button({icons: {primary: 'ui-icon-search'}});
									p.$mod.find('[name=tipo]').html(event.data.tipo.nomb).data('data',event.data.tipo);
									p.$mod.find('[name=observ]').val(event.data.observ);
								}
							});
						}
					},
					eventMouseover: function( event, jsEvent, view ){
						//if(event.tipo=='R'){
							var xOffset = -10,
							yOffset = 10;
							this.top = (jsEvent.pageY + yOffset); this.left = (jsEvent.pageX + xOffset);
							if(event.data!=null) var descr = event.data.observ;
							else var descr = event.title;
							$('body').append( '<p id="vtip" class="ui-corner-all"><b>'+event.title+'</b></br>'+ciHelper.date.format.dayLong(event.start)+'</br>'+ciHelper.date.format.hour(event.start)+' - '+ciHelper.date.format.hour(event.end)+'</br><i>'+descr+'</i></p>' );
							$('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
						//}
					},
					eventMouseout: function(){
						$("p#vtip").fadeOut("slow").remove();
					},
				    height: p.$mainPanel.height(),
					firstDay: 1,
					viewDisplay: function (view) {
						$('.fc-header-left').hide();
				    }
				});		
				p.$mainPanel.resize(function(){
					p.$cal.fullCalendar('option', 'height', p.$mainPanel.height());
				});
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		
		$('#pageWrapperMain').layout({
			west__closable: false,
			west__resizable: false
		});
	}
};
require(['pe/tipo'],function(peTipo){
	$.noop();
});