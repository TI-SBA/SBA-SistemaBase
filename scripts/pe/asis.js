peAsis = {
	timezone: 0,
	timezoneChange: 5,
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peAsis',
			titleBar: {
				title: 'Control de Asistencia'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Trabajador',f:'fullname'},'Horario'],
					data: 'pe/trab/lista',
					params: {type_fields: 'trab_horarios'},
					itemdescr: 'trabajador(es)',
					toolbarHTML: '<button name="btnImas" class="btn btn-primary"><i class="fa fa-upload"></i> Importar Asistencias Reloj Antigüo</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnImas]').click(function(){
							require(['pe/imas'],function(peImas){
								peImas.init();
							});
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatName(data)+'</td>');
						if(data.roles.trabajador.turno!=null) $row.append('<td><kbd>'+data.roles.trabajador.turno.nomb+'</kbd></td>');
						else $row.append('<td><label class="label label-danger">No definido a&uacute;n</label></td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peAsis.windowAsistencia({id: $(this).data('id'),nomb: $(this).find('td:eq(1)').html(),data: $(this).data('data')});
						}).data('data',data).contextMenu("conMenPeAsis", {
							onShowMenu: function($row, menu) {
								return menu;
							},
							bindings: {
								'conMenPeAsis_hor': function(t) {
									peAsis.windowHorario({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html(),data: K.tmp.data('data')});
								},
								'conMenPeAsis_asi': function(t) {
									peAsis.windowAsistencia({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html(),data: K.tmp.data('data')});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowHorario: function(p){
		if(p==null) p = {};
		$.extend(p,{
			feriados: [],
			turno: [],
			asig: [],
			del: [],
			date: new Date()
		});
		new K.Panel({
			buttons: {
				'Guardar Semana': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						var events = p.$cal.fullCalendar( 'clientEvents'),
						data = [],
						trab = mgEnti.dbRel(p.data);
						for(var i=0; i<events.length; i++){
							if(events[i].tipo=='laborable'){
								if(events[i].end!=null){
									var tmp = {
										programado: {
											inicio: ciHelper.date.format.ymdhis(events[i].start._d),
											fin: ciHelper.date.format.ymdhis(events[i].end._d)
										}
									};
									if(events[i]._id!=null){
										if(events[i]._id.length>8){
											tmp._id = events[i]._id;
										}
									}
									data.push(tmp);
								}
							}
							if(events[i].tipo=='laborable2'){
								if(events[i].end!=null){
									var tmp = {
										programado: {
											inicio: ciHelper.date.format.ymdhis(events[i].start._d,peAsis.timezoneChange),
											fin: ciHelper.date.format.ymdhis(events[i].end._d,peAsis.timezoneChange)
										}
									};
									data.push(tmp);
								}
							}
						}
						K.sendingInfo();
						$.post('pe/hora/save_hora',{
							trabajador: trab,
							turno: peTurn.dbRel(p.data.roles.trabajador.turno)._id,
							data: data,
							del: p.del
						},function(data){
							p.$cal.fullCalendar( 'removeEvents' );
							p.del = [];
							K.clearNoti();
							K.notification({title: 'Horario actualizado!',text: 'Se realiz&oacute; con &eacute;xito el guardado de bloques programados para '+ciHelper.enti.formatName(p.enti)+'!'});
							if(data.turno!=null) p.turno = data.turno;
							else p.turno = [];
							p.$cal.fullCalendar( 'refetchEvents' );
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						peAsis.init();
					}
				}
			},
			contentURL: 'pe/asis',
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$cal = p.$w.find('[name=calendar]').fullCalendar({
					defaultView: 'agendaWeek',
					//editable: true,
					eventSources: [
						{
							events: function(start, end, timezone, callback){
								$.post('pe/asis/get_hora',{
									calendario: 1,
									trabajador: p.id,
									start: ciHelper.date.format.ymd(start._d),
									end: ciHelper.date.format.ymd(end._d),
									turno: p.data.roles.trabajador.turno._id.$id
								},function(data){
									if(data!=null){
										var events = [];
										if(data.asistencia!=null){
											for(var i=0; i<data.asistencia.length; i++){
												data.asistencia[i]._id = data.asistencia[i]._id.$id;
												$.extend(data.asistencia[i],{
													_mid: data.asistencia[i]._id.$id,
													color: 'green',
													textColor: 'white',
													tipo: 'laborable'
												});
												events.push(data.asistencia[i]);
											}
										}
										if(data.feriados!=null){
											for(var i=0; i<data.feriados.length; i++){
												$.extend(data.feriados[i],{
													allDay: true,
													tipo: 'feriado'
												});
												events.push(data.feriados[i]);
											}
										}
										if(data.turno!=null){
											for(var i=0; i<data.turno.length; i++){
												var tmp = true;
												var dif = parseInt(data.turno[i].dia),
												ini = new Date(start._d.getTime() + (dif * 24 * 3600 * 1000)),
												fin = new Date(start._d.getTime() + (dif * 24 * 3600 * 1000));
												if(dif==-1){
													ini = end;
													fin = end;
												}
												ini.setHours(data.turno[i].horas.ini.substring(0,2));
												ini.setMinutes(data.turno[i].horas.ini.substring(3,5));
												if(data.turno[i].horas.fin!='00:00'){
													fin.setHours(data.turno[i].horas.fin.substring(0,2));
													fin.setMinutes(data.turno[i].horas.fin.substring(3,5));
												}else{
													fin.setHours('24');
													fin.setMinutes('00');
												}
												if(data.feriados!=null){
													for(var j=0; j<data.feriados.length; j++){
														if(ciHelper.date.format.bd_ymd(data.feriados[j].start)==ciHelper.date.format.bd_ymd(ini)) tmp = false;
													}
												}
												if(data.asistencia!=null){
													for(var j=0; j<data.asistencia.length; j++){
														if(ciHelper.date.format.bd_ymd(data.asistencia[j].start)==ciHelper.date.format.bd_ymd(ini)) tmp = false;
													}
												}
												if(tmp==true){
													events.push({
														title: 'Turno Designado',
														start: ini,
														end: fin,
														allDay: false,
														backgroundColor: 'blue',
														tipo: 'turno'
													});
												}
											}
										}
										callback(events);
									}
								},'json');
							}
						}
					],
					selectable: true,
					select: function(start, end) {
						if(end.hasTime()==false) return p.$cal.fullCalendar('unselect');
						/*if(ciHelper.date.compare(p.date,start)==1){
							return K.notification({title: 'Evento inv&aacute;lido',text: 'El evento debe ser creado despu&eacute;s del d&iacute;a de hoy!',type: 'info'});
						}*/
						var events = p.$cal.fullCalendar( 'clientEvents');
						for(var i=0; i<events.length; i++){
							if(events[i].tipo=='turno'){
								if(ciHelper.date.format.ymd(events[i].start._d)==ciHelper.date.format.ymd(start._d)){
									p.$cal.fullCalendar( 'removeEvents' , events[i]._id );
								}
							}
						}
						p.$cal.fullCalendar('renderEvent',{
							title: 'Horas Laborables',
							start: start,
							end: end,
							allDay: false,
							backgroundColor: 'green',
							tipo: 'laborable'
						},true);
					},
					eventClick: function(calEvent, jsEvent, view) {
						//if(ciHelper.date.compare(p.date,calEvent.start)!=1){
							if(calEvent.tipo!='feriado'){
								new K.Modal({
									id: 'modalTurno',
									title: 'Turno',
									contentURL: 'pe/turn/modal',
									height: 400,
									width: 650,
									buttons: {
										"Actualizar": {
											icon: 'fa-save',
											type: 'success',
											f: function(){
												K.clearNoti();
												var day = calEvent.start._d.getDate();
												var start = p.$mod.find( "[name=ini]" ).val();
												var hor = start.substring(0,2);
												calEvent.start._d.setUTCHours(parseInt(hor));
												calEvent.start._d.setMinutes(start.substring(3,5));
												calEvent.start._d.setDate(day);
												var day = calEvent.start._d.getDate();
												var fin = p.$mod.find( "[name=fin]" ).val();
												var hor = fin.substring(0,2);
												calEvent.end._d.setUTCHours(parseInt(hor));
												calEvent.end._d.setMinutes(fin.substring(3,5));
												calEvent.end._d.setDate(day);
												var comp = ciHelper.date.compare(calEvent.start._d,calEvent.end._d);
												if(!(comp==-1)){
													//return K.notification({title: 'Formato inv&aacute;lido',text: 'La hora de inicio es mayor a la de fin!',type: 'info'});
													calEvent.end._d.setDate(day+1);
												}
												var events = p.$cal.fullCalendar( 'clientEvents');
												for(var i=0; i<events.length; i++){
													if(ciHelper.date.format.ymd(events[i].start._d)==ciHelper.date.format.ymd(calEvent.start._d)){
														events[i].backgroundColor = 'green';
														events[i].tipo = 'laborable';
														p.$cal.fullCalendar('updateEvent', events[i]);
													}
												}
												p.$cal.fullCalendar('updateEvent', calEvent);
												K.closeWindow(p.$mod.attr('id'));
											}
										},
										"Eliminar": {
											icon: 'fa-trash-o',
											type: 'warning',
											f: function(){
												if(calEvent._mid!=null) p.del.push(calEvent._mid);
												if(calEvent._id!=null){
													if(calEvent._id.length>8){
														p.del.push(calEvent._id);
													}
												}
												var events = p.$cal.fullCalendar( 'clientEvents');
												for(var i=0; i<events.length; i++){
													if(ciHelper.date.format.ymd(events[i].start._d)!=ciHelper.date.format.ymd(calEvent.start._d)){
														events[i].backgroundColor = 'green';
														events[i].tipo = 'laborable2';
														p.$cal.fullCalendar('updateEvent', events[i]);
													}
												}
												p.$cal.fullCalendar( 'removeEvents' , calEvent._id );
												K.closeWindow(p.$mod.attr('id'));
											}
										}
									},
									onClose: function(){ p.$mod = null; },
									onContentLoaded: function(){
										p.$mod = $('#modalTurno');
										p.$mod.find('[name=ini]').parent().datetimepicker({format: 'HH:mm'});
										p.$mod.find('[name=fin]').parent().datetimepicker({format: 'HH:mm'});
										p.$mod.find( "[name=ini]" ).val(ciHelper.date.format.hi(calEvent.start._d,peAsis.timezone));
										p.$mod.find( "[name=fin]" ).val(ciHelper.date.format.hi(calEvent.end._d,peAsis.timezone));
									}
								});
							}
						//}
					}
				});
				p.$w.find('[name=fecha]').val(ciHelper.date.get.now_ymd()).datepicker({format: 'yyyy-mm-dd'})
				.on('changeDate', function(ev){
					p.$cal.fullCalendar('gotoDate', $(this).val());
				}).change(function(){
					p.$cal.fullCalendar('gotoDate', $(this).val());
				});
				p.$w.find('[name=mini_enti] .panel-title').html('TRABAJADOR');
				p.$w.find('[name=mini_enti] [name=btnSel]').hide();
				p.$w.find('[name=mini_enti] [name=btnAct]').hide();
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.data);
				K.unblock();
			}
		});
	},
	windowAsistencia: function(p){
		if(p==null) p = {};
		$.extend(p,{
			debug: true,
			check: function(inci){
        		if(inci.start!=null&&inci.end!=null){
        			if(ciHelper.date.format.ymdhi(inci.start)!=ciHelper.date.format.ymdhi(inci.end)){
	        			if(p.inci.length>0){
	        				sectmp = true;
    						for(var ii=0; ii<p.inci.length; ii++){
    							if(ciHelper.date.format.ymdhi(inci.start)==ciHelper.date.format.ymdhi(p.inci[ii].start)&&ciHelper.date.format.ymdhi(inci.end)==ciHelper.date.format.ymdhi(p.inci[ii].end)){
    								sectmp = false;
    								ii = p.inci.length;
    							}
    						}
    						if(sectmp==true) p.asig.push(inci);
    					}else p.asig.push(inci);
    				}
				}
    		},
    		validarLastHour: function(hour_fin){
				if(hour_fin.getHours()=='0'&&hour_fin.getMinutes()=='0'){
					hour_fin.setHours('24');
					hour_fin.setMinutes('00');
				}
				return hour_fin;
			}
		});
		K.Panel({
			buttons: {
				'Cancelar': {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						peAsis.init();
					}
				}
			},
			contentURL: 'pe/asis',
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$cal = p.$w.find('[name=calendar]').fullCalendar({
					defaultView: 'agendaWeek',
					//editable: true,
					slotDuration: '00:05:00',
					eventSources: [
						{
							events: function(start, end, timezone, callback){
								//$.post('pe/hora/marc',{
								$.post('pe/hora/all_periodo',{
									enti: p.id,
									ini: ciHelper.date.format.ymd(start._d),
									fin: ciHelper.date.format.ymd(end._d)
								},function(data){
									if(data!=null){
										p.asig = [];
										p.inci = [];
										p.dias = [];
										for(var i=0; i<data.asig.length; i++){
											if(data.asig[i].programado!=null) var daytmp = ciHelper.date.format.bd_ymd(data.asig[i].programado.inicio);
											else var daytmp = ciHelper.date.format.bd_ymd(data.asig[i].ejecutado.entrada.fecreg);
											if(p.dias[daytmp]==null)
												p.dias[daytmp] = {
													prog: [],
													asis: []
												};
											if(data.asig[i].programado!=null){
												p.dias[daytmp].prog.push({
													inicio: new Date(data.asig[i].programado.inicio.sec*1000),
													fin: p.validarLastHour(new Date(data.asig[i].programado.fin.sec*1000))
												});
											}
											if(data.asig[i].ejecutado!=null){
												var tmp_event = {
													allDay: false,
													start: new Date(data.asig[i].ejecutado.entrada.fecreg.sec*1000),
													data: data[i]
												};
												var eje = {
													entrada: new Date(data.asig[i].ejecutado.entrada.fecreg.sec*1000)
												};
												if(data.asig[i].ejecutado.salida!=null)
													eje.salida = new Date(data.asig[i].ejecutado.salida.fecreg.sec*1000);
												p.dias[daytmp].asis.push(eje);
												if(data.asig[i].ejecutado.salida==null) color = 'gray';
												else color = 'blue';
												if(data.asig[i].ejecutado.salida!=null){
													var fin = new Date(data.asig[i].ejecutado.salida.fecreg.sec*1000),
													title = data.asig[i].ejecutado.entrada.equipo.local.descr+' ('+data.asig[i].ejecutado.entrada.equipo.cod+' - '+data.asig[i].ejecutado.salida.equipo.cod+')';
												}else if(ciHelper.date.format.bd_ymd(new Date(data.asig[i].ejecutado.entrada.fecreg.sec*1000))!=ciHelper.date.get.now_ymd()){
													var ini = new Date(data.asig[i].ejecutado.entrada.fecreg.sec*1000),
													fin = new Date(ini.getTime() + (3600 * 1000)),
													title = 'Sin Cierre';
													tmp_event.edit = true;
												}else{
													var fin = new Date(),
													title = data.asig[i].ejecutado.entrada.equipo.local.descr+' ('+data.asig[i].ejecutado.entrada.equipo.cod+')';
												}
												if(data.asig[i].manual!=null){
													tmp_event.edit = true;
													tmp_event.remove = true;
												}else{
													tmp_event.edit = false;
													tmp_event.remove = false;
												}
												tmp_event.title = title;
												tmp_event.end = fin;
												tmp_event.backgroundColor = color;
												p.asig.push(tmp_event);
											}
										}
										if(data.inci!=null){
											for(var i=0; i<data.inci.length; i++){
												var incidencia = {
													title: data.inci[i].tipo.nomb,
													allDay: false,
													start: new Date(data.inci[i].fecini.sec*1000),
													end: new Date(data.inci[i].fecfin.sec*1000),
													backgroundColor: 'green',
													tipo: 'R',
													mid: data.inci[i]._id.$id,
													data: data.inci[i]
												};
												p.inci.push(incidencia);
												p.asig.push(incidencia);
											}
										}
									}else{
										p.asig = [];
									}
									console.log(p.dias);
									var tmp_totdias = (parseFloat(ciHelper.date.diff(end._d,start._d))/60/60/24);
					        		for(var i=0; i<tmp_totdias; i++){
						        		var dia = new Date(start._d.getTime() + (i * 24 * 3600 * 1000)),
						        		diaForm = ciHelper.date.format.ymd(dia);
						        		console.log('DIA <=> '+diaForm);
						        		if(p.dias[diaForm]!=null){
						        			var tmp = p.dias[diaForm];
						        			if(tmp.asis.length<1){
						        				for(var j=0; j<tmp.prog.length; j++){
						        					if(p.debug) console.log('caso 1');
						        					p.check({
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
						        						if(p.debug) console.log('caso 2');
							        					p.check({
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
								        										if(p.debug) console.log('caso 3');
													        					p.check({
																					title: 'Sin clasificar - Exceso',
																					allDay: false,
																					end: tmp.prog[k+1].inicio,
																					start: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
													        					if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==1){
													        						if(p.debug) console.log('caso 4');
														        					p.check({
																						title: 'Sin clasificar - Salida temprana',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.prog[k+1].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
													        					}else if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==-1){
													        						if(p.debug) console.log('caso 5');
													        						p.check({
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
								        										if(p.debug) console.log('caso 6');
								        										p.check({
																					title: 'Sin clasificar - Exceso',
																					allDay: false,
																					end: asis.salida,
																					start: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
								        									}
										        						}else{
										        							if(p.debug) console.log('caso 7');
												        					p.check({
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
								        										if(p.debug) console.log('caso 8');
											        							p.check({
																					title: 'Sin clasificar - No trabajado',
																					allDay: false,
																					start: asis.salida,
																					end: tmp.asis[j+1].entrada,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
								        									}else{
								        										if(p.debug) console.log('caso 9');
											        							p.check({
																					title: 'Sin clasificar - Salida temprana',
																					allDay: false,
																					start: asis.salida,
																					end: tmp.prog[k].fin,
																					tipo: 'I',
																					backgroundColor: 'red'
																				});
								        									}
										        						}else{
										        							if(p.debug) console.log('caso 10');
										        							p.check({
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
						        									if(p.debug) console.log('caso 11');
										        					p.check({
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
						        										if(p.debug) console.log('caso 12');
											        					p.check({
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
											        								if(p.debug) console.log('caso 13');
											        								p.check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: tmp.prog[k+1].inicio,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
														        					if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==1){
														        						if(p.debug) console.log('caso 14');
															        					p.check({
																							title: 'Sin clasificar - Salida temprana',
																							allDay: false,
																							start: asis.salida,
																							end: tmp.prog[k+1].fin,
																							tipo: 'I',
																							backgroundColor: 'red'
																						});
														        					}else if(ciHelper.date.compare(tmp.prog[k+1].fin,asis.salida)==-1){
														        						if(p.debug) console.log('caso 15');
														        						p.check({
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
									        										if(p.debug) console.log('caso 16');
									        										p.check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: asis.salida,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
											        							if(p.debug) console.log('caso 17');
													        					p.check({
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
											        								if(p.debug) console.log('caso 18');
												        							p.check({
																						title: 'Sin clasificar - No trabajado',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.asis[j+1].entrada,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}else{
									        										if(p.debug) console.log('caso 19');
												        							p.check({
																						title: 'Sin clasificar - Salida temprana',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
											        							if(p.debug) console.log('caso 20');
													        					p.check({
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
						        										if(p.debug) console.log('caso 21');
											        					p.check({
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
						        									if(p.debug) console.log('caso 22');
										        					p.check({
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
						        										if(p.debug) console.log('caso 23');
											        					p.check({
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
											        								if(p.debug) console.log('caso 24');
											        								p.check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: tmp.prog[k+1].inicio,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
														        					k++;
														        					if(ciHelper.date.compare(tmp.prog[k].fin,asis.salida)==1){
														        						if(p.debug) console.log('caso 25');
															        					p.check({
																							title: 'Sin clasificar - Salida temprana',
																							allDay: false,
																							start: asis.salida,
																							end: tmp.prog[k].fin,
																							tipo: 'I',
																							backgroundColor: 'red'
																						});
														        					}else if(ciHelper.date.compare(tmp.prog[k].fin,asis.entrada)==-1){
														        						if(p.debug) console.log('caso 26');
														        						p.check({
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
									        										if(p.debug) console.log('caso 27');
									        										p.check({
																						title: 'Sin clasificar - Exceso',
																						allDay: false,
																						end: asis.salida,
																						start: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
											        							if(p.debug) console.log('caso 28');
													        					p.check({
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
											        								if(p.debug) console.log('caso 29');
												        							p.check({
																						title: 'Sin clasificar - No trabajado',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.asis[j+1].entrada,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}else{
									        										if(p.debug) console.log('caso 30');
												        							p.check({
																						title: 'Sin clasificar - Salida temprana',
																						allDay: false,
																						start: asis.salida,
																						end: tmp.prog[k].fin,
																						tipo: 'I',
																						backgroundColor: 'red'
																					});
									        									}
											        						}else{
											        							if(p.debug) console.log('caso 31');
													        					p.check({
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
						        										if(p.debug) console.log('caso 32');
											        					p.check({
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
						        									if(p.debug) console.log('caso 33');
										        					p.check({
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
									callback(p.asig);
								},'json');
							},
						}
					],
					selectable: true,
					select: function(start, end) {
						if(end.hasTime()==false) return p.$cal.fullCalendar('unselect');
						/*if(ciHelper.date.compare(p.date,start)==1){
							return K.notification({title: 'Evento inv&aacute;lido',text: 'El evento debe ser creado despu&eacute;s del d&iacute;a de hoy!',type: 'info'});
						}*/
						var events = p.$cal.fullCalendar( 'clientEvents');
						for(var i=0; i<events.length; i++){
							if(events[i].tipo=='turno'){
								if(ciHelper.date.format.ymd(events[i].start._d)==ciHelper.date.format.ymd(start._d)){
									p.$cal.fullCalendar( 'removeEvents' , events[i]._id );
								}
							}
						}
						p.$cal.fullCalendar('renderEvent',{
							title: 'Horas Laborables',
							start: start,
							end: end,
							allDay: false,
							backgroundColor: 'green',
							tipo: 'laborable'
						},true);
					},
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
									"Guardar": {
										icon: 'fa-save',
										type: 'success',
										f: function(){
											console.log(event);
											var tipo = p.$mod.find('[name=tipo]').data('data');
											if(tipo==null){
												p.$mod.find('[name=btnTipo]').click();
												return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de incidencia!',type: 'error'});
											}
											//fecini
											//var date_ini = new Date();
											//date_ini = event.start._d;
											//console.log(date_ini._d);
											//date_ini_month=date_ini.getMonth()+1;
											//console.log(date_ini_month);
											//var date_fecini = date_ini.getFullYear()+"-"+(date_ini_month<10?'0'+date_ini_month:date_ini_month)+'-'+(date_ini.getDate()<10?'0'+date_ini.getDate():date_ini.getDate())+' '+(date_ini.getHours()<10?'0'+date_ini.getHours():date_ini.getHours())+':'+(date_ini.getMinutes()<10?'0'+date_ini.getMinutes():date_ini.getMinutes())+':00';
											//console.log(date_fecini);
											//fecfin

											//console.log(p);

											var data = {
												fecini: ciHelper.date.format.dayBD(event.start._d),
												fecfin: ciHelper.date.format.dayBD(event.end._d),
												trabajador: {
													_id: p.data._id.$id,
													tipo_enti: p.data.tipo_enti,
													nomb: p.data.nomb,
													appat: p.data.appat,
													apmat: p.data.apmat
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
										}
									},
									"Cerrar": {
										icon: 'fa-close',
										type: 'danger',
										f: function(){
											K.closeWindow(p.$mod.attr('id'));
										}
									}
								},
								onClose: function(){ p.$mod = null; },
								onContentLoaded: function(){
									p.$mod = $('#modalTurno');
									p.$mod.find('[name=btnTipo]').click(function(){
										peTipo.windowSelect({callback: function(data){
											p.$mod.find('[name=tipo]').html(data.nomb).data('data',data);

										},bootstrap: true});
									});
								}
							});
						}else if(event.tipo=='R'){
							new K.Modal({
								id: 'modalTurno',
								title: 'Editar Incidencia',
								contentURL: 'pe/hora/sinclas',
								height: 200,
								width: 500,
								buttons: {
									"Guardar": {
										icon: 'fa-save',
										type: 'success',
										f: function(){
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
										}
									},
									"Eliminar": {
										icon: 'fa-trash-o',
										type: 'danger',
										f: function(){
											ciHelper.confirm('&#191;Desea <b>Eliminar</b> la incidencia seleccionada&#63;',
											function(){
												K.sendingInfo();
												$.post('pe/asis/delete_inci',{_id: event.data._id.$id},function(){
													K.clearNoti();
													K.notification({title: 'Acta de Conciliacion Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													p.$cal.fullCalendar( 'removeEvents' , event._id );
													K.closeWindow(p.$mod.attr('id'));
												});
											},function(){
												$.noop();
											},'Eliminaci&oacute;n de Incidencia');
										}
									},
									"Cerrar": {
										icon: 'fa-close',
										type: 'warning',
										f: function(){
											K.closeWindow(p.$mod.attr('id'));
										}
									}
								},
								onClose: function(){ p.$mod = null; },
								onContentLoaded: function(){
									p.$mod = $('#modalTurno');
									p.$mod.find('[name=btnTipo]').click(function(){
										peTipo.windowSelect({callback: function(data){
											p.$mod.find('[name=tipo]').html(data.nomb).data('data',data);
										},bootstrap: true});
									});
									p.$mod.find('[name=tipo]').html(event.data.tipo.nomb).data('data',event.data.tipo);
									p.$mod.find('[name=observ]').val(event.data.observ);
								}
							});
						}
					},
				});
				p.$w.find('[name=fecha]').val(ciHelper.date.get.now_ymd()).datepicker({format: 'yyyy-mm-dd'})
				.on('changeDate', function(ev){
					p.$cal.fullCalendar('gotoDate', $(this).val());
				}).change(function(){
					p.$cal.fullCalendar('gotoDate', $(this).val());
				});
				p.$w.find('[name=mini_enti] .panel-title').html('TRABAJADOR');
				p.$w.find('[name=mini_enti] [name=btnSel]').hide();
				p.$w.find('[name=mini_enti] [name=btnAct]').hide();
				mgEnti.fillMini(p.$w.find('[name=mini_enti]'),p.data);
				K.unblock();
			}
		});
	}
};
define(
	['mg/enti','pe/turn','pe/tipo','pe/equi'],
	function(mgEnti,peTurn,peTipo,peEqui){
		return peAsis;
	}
);