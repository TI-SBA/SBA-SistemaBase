/*******************************************************************************
control de horarios */
peCoasHora = {
	states: [
		{
			descr: "Programado",
			color: 'green'
		},
		{
			descr: "Turno",
			color: 'blue'
		}
	],
	init: function(p){
		p = {
			date: new Date(),
			feriados: [],
			turno: [],
			asig: [],
			del: [],
			load: function(tipo){
				var data = {
					filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.trabajador',value: {$exists: true}},
					    {nomb: 'roles.trabajador.estado',value: 'H'},
					    {nomb: 'roles.trabajador.turno',value: {$exists: true}},
					    {nomb: 'roles.trabajador.contrato.cod',value: tipo}
					],
					fields: {
						nomb: 1,
						appat: 1,
						apmat: 1,
						tipo_enti: 1,
						'roles.trabajador.turno._id': 1
					}
				};
				$.post('mg/enti/search_all',data,function(data){
					p.$l.find('.gridBody:first').empty();
					if(data!=null){
						for(var i=0,j=data.length; i<j; i++){
							var $row = p.$l.find('.gridReference:first').clone();
							$row.find('li').html(ciHelper.enti.formatName(data[i]));
							$row.wrapInner('<a class="item" />');
							$row.find('.item').click(function(){
								p.$cal.fullCalendar( 'removeEvents' );
								p.enti = $(this).data('enti');
								$.post('pe/hora/asis_hor','enti='+$(this).data('id')+'&turno='+$(this).data('turno'),function(data){
									if(data.turno!=null) p.turno = data.turno;
									else p.turno = [];
									if(data.asig!=null){
										p.asig = [];
										for(var i=0,j=data.asig.length; i<j; i++){
											if(data.asig[i].programado!=null){
												if(ciHelper.date.compare(p.date,new Date(data.asig[i].programado.inicio.sec*1000))==1) color = 'gray';
												else color = 'green';
												var hour_end = new Date(data.asig[i].programado.fin.sec*1000);
												if(hour_end.getHours()=='0'&&hour_end.getMinutes()=='0'){
													hour_end.setHours('24');
													hour_end.setMinutes('00');
												}
												p.asig.push({
													title: 'Turno Programado',
													allDay: false,
													start: new Date(data.asig[i].programado.inicio.sec*1000),
													end: hour_end,
													tipo: 'laborable',
													_mid: data.asig[i]._id.$id,
													backgroundColor: color
												});
											}
										}
									}else p.asig = [];
									p.$cal.fullCalendar( 'refetchEvents' );
								},'json');
							}).data('id',data[i]._id.$id).data('turno',data[i].roles.trabajador.turno._id.$id)
							.data('enti',{
								_id: data[i]._id.$id,
								tipo_enti: data[i].tipo_enti,
								nomb: data[i].nomb,
								appat: data[i].appat,
								apmat: data[i].apmat,
								turno: data[i].roles.trabajador.turno._id.$id
							});
				        	p.$l.find(".gridBody:first").append( $row.children() );
						}
						p.$l.find(".gridBody:first .item:first").click()
						.find('ul').addClass('ui-state-highlight');
					}
				},'json');
			}
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
				$p.find('[name=peCoasHora]').click(function(){ peCoasHora.init(); }).addClass('ui-state-highlight');
				$p.find('[name=peCoasAsis]').click(function(){ peCoasAsis.init(); });
				$p.find('[name=peCoasInci]').click(function(){ peCoasInci.init(); });
				$p.find('[name=peCoasProg]').click(function(){ peCoasProg.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'peCoasHora',
			titleBar: {
				title: 'Control de Asistencia: Horarios'
			}
		});

		new K.Panel({
			id: 'westPanel',
			position: 'left',
			//contentURL: 'pe/hora',
			contentURL: 'pe/hora/calen',
			onContentLoaded: function(){
				p.$l = $('#westPanel');
				p.$l.find('.ui-layout-south fieldset').css('padding','0px');
				for(var i=0,j=peCoasHora.states.length; i<j; i++){
					var $row = p.$l.find('.gridReference:last').clone();
					$row.find('li:eq(0)').css('background',peCoasHora.states[i].color).addClass('vtip').attr('title',peCoasHora.states[i].descr);
					$row.find('li:eq(1)').html(peCoasHora.states[i].descr);
					$row.wrapInner('<a class="item" />');
		        	p.$l.find(".gridBody:last").append( $row.children() );
				}
				p.$l.find('[name=btnGuardar]').click(function(){
					if(p.enti==null) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
					var events = p.$cal.fullCalendar( 'clientEvents'),
					data = [],
					trab = ciHelper.enti.dbRel(p.enti);
					for(var i=0; i<events.length; i++){
						if(events[i].tipo=='laborable'){
							if(events[i].end!=null){
								var tmp = {
									programado: {
										inicio: ciHelper.date.format.dayBD(events[i].start),
										fin: ciHelper.date.format.dayBD(events[i].end)
									}
								};
								if(events[i]._mid!=null) tmp._id = events[i]._mid;
								data.push(tmp);
							}
						}
					}
					K.sendingInfo();
					$(this).button('disable');
					$.post('pe/hora/save_hora',{
						trabajador: trab,
						turno: p.enti.turno,
						data: data,
						del: p.del
					},function(data){
						p.$cal.fullCalendar( 'removeEvents' );
						p.del = [];
						K.clearNoti();
						p.$l.find('[name=btnGuardar]').button('enable');
						K.notification({title: 'Horario actualizado!',text: 'Se realiz&oacute; con &eacute;xito el guardado de bloques programados para '+ciHelper.enti.formatName(p.enti)+'!'});
						if(data.turno!=null) p.turno = data.turno;
						else p.turno = [];
						if(data.asig!=null){
							p.asig = [];
							for(var i=0; i<data.asig.length; i++){
								if(data.asig[i].programado!=null){
									if(ciHelper.date.compare(p.date,new Date(data.asig[i].programado.inicio.sec*1000))==1) color = 'gray';
									else color = 'green';
								}else{
									if(ciHelper.date.compare(p.date,new Date(data.asig[i].ejecutado.entrada.fecreg.sec*1000))==1) color = 'gray';
									else color = 'green';
								}
								p.asig.push({
									title: 'Turno Programado',
									allDay: false,
									start: new Date(data.asig[i].programado.inicio.sec*1000),
									end: new Date(data.asig[i].programado.fin.sec*1000),
									tipo: 'laborable',
									_mid: data.asig[i]._id.$id,
									backgroundColor: color
								});
							}
						}else p.asig = [];
						p.$cal.fullCalendar( 'refetchEvents' );
					},'json');
				}).button({icons: {primary: 'ui-icon-disk'}});
				$.post('pe/feri/lista','year='+p.date.getFullYear(),function(data){
					if(data!=null){
						for(var i=0; i<data.length; i++){
							p.feriados.push({
								title: data[i].nomb,
								allDay: true,
								start: new Date(data[i].fec.sec*1000),
								tipo: 'feriado'
							});
						}
					}
					p.$cal = p.$mainPanel.find('[name=calendar]').fullCalendar({
						defaultView: 'agendaWeek',
						editable: true,
						eventSources: [
						    {
					            events: function(start, end, callback) {
					                callback(p.feriados);
					            }
					        },
						    {
					            events: function(start, end, callback) {
			            			var data = [],tmp = true;
					            	if(p.turno!=null){
					            		if(ciHelper.date.inRange(p.date,start,end)==true) tmp = true;
										else if(ciHelper.date.compare(start,p.date)==1) tmp = true;
										else tmp = false;
					            		if(tmp==true){
											for(var i=0; i<p.turno.length; i++){
												var tmp = true;
												if(ciHelper.date.inRange(p.date,start,end)==true){
													if(parseInt(p.turno[i].dia)<p.date.getDay()) tmp = false;
												}
												var dif = parseInt(p.turno[i].dia) - 1,
												ini = new Date(start.getTime() + (dif * 24 * 3600 * 1000)),
												fin = new Date(start.getTime() + (dif * 24 * 3600 * 1000));
												if(dif==-1){
													ini = end;
													fin = end;
												}
												ini.setHours(p.turno[i].horas.ini.substring(0,2));
												ini.setMinutes(p.turno[i].horas.ini.substring(3,5));
												if(p.turno[i].horas.fin!='00:00'){
													fin.setHours(p.turno[i].horas.fin.substring(0,2));
													fin.setMinutes(p.turno[i].horas.fin.substring(3,5));
												}else{
													fin.setHours('24');
													fin.setMinutes('00');
												}
												if(p.feriados!=null){
													for(var j=0; j<p.feriados.length; j++){
														if(ciHelper.date.format.dayBDNotHour(p.feriados[j].start)==ciHelper.date.format.dayBDNotHour(ini)) tmp = false;
													}
												}
												if(p.asig!=null){
													for(var j=0; j<p.asig.length; j++){
														if(ciHelper.date.format.dayBDNotHour(p.asig[j].start)==ciHelper.date.format.dayBDNotHour(ini)) tmp = false;
													}
												}
												if(tmp==true){
													data.push({
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
						    		}
				            		callback(data);
					            },
					            color: 'blue',
					            textColor: 'white'
					        },
						    {
					            events: function(start, end, callback) {
					                callback(p.asig);
					            },
					            color: 'green',
					            textColor: 'white'
					        }
						],
						selectable: true,
						select: function(start, end, allDay) {
							if(allDay==true) return 0;
							/*if(ciHelper.date.compare(p.date,start)==1){
								return K.notification({title: 'Evento inv&aacute;lido',text: 'El evento debe ser creado despu&eacute;s del d&iacute;a de hoy!',type: 'info'});
							}*/
							var events = p.$cal.fullCalendar( 'clientEvents');
							for(var i=0; i<events.length; i++){
								if(events[i].tipo=='turno'){
									if(ciHelper.date.format.dayBDNotHour(events[i].start)==ciHelper.date.format.dayBDNotHour(start)){
										p.$cal.fullCalendar( 'removeEvents' , events[i]._id );
									}
								}
							}
							p.$cal.fullCalendar('renderEvent',{
								title: 'Horas Laborables',
								start: start,
								end: end,
								allDay: allDay,
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
										height: 45,
										icon: 'ui-icon-calendar',
										buttons: {
											"Actualizar": function(){
												K.clearNoti();
												var day = calEvent.start.getDate();
												var start = p.$mod.find( "[name=ini]" ).val();
												var hor = start.substring(0,2);
												calEvent.start.setHours(hor);
												calEvent.start.setMinutes(start.substring(3,5));
												calEvent.start.setDate(day);
												var day = calEvent.start.getDate();
												var fin = p.$mod.find( "[name=fin]" ).val();
												var hor = fin.substring(0,2);
												calEvent.end.setHours(hor);
												calEvent.end.setMinutes(fin.substring(3,5));
												calEvent.end.setDate(day);
												var comp = ciHelper.date.compare(calEvent.start,calEvent.end);
												if(!(comp==-1)){
													//return K.notification({title: 'Formato inv&aacute;lido',text: 'La hora de inicio es mayor a la de fin!',type: 'info'});
													calEvent.end.setDate(day+1);
												}
												var events = p.$cal.fullCalendar( 'clientEvents');
												for(var i=0; i<events.length; i++){
													if(ciHelper.date.format.dayBDNotHour(events[i].start)==ciHelper.date.format.dayBDNotHour(calEvent.start)){
														events[i].backgroundColor = 'green';
														events[i].tipo = 'laborable';
														p.$cal.fullCalendar('updateEvent', events[i]);
													}
												}
												p.$cal.fullCalendar('updateEvent', calEvent);
												K.closeWindow(p.$mod.attr('id'));
											},
											"Eliminar": function(){
												if(calEvent._mid!=null) p.del.push(calEvent._mid);
												var events = p.$cal.fullCalendar( 'clientEvents');
												for(var i=0; i<events.length; i++){
													if(ciHelper.date.format.dayBDNotHour(events[i].start)==ciHelper.date.format.dayBDNotHour(calEvent.start)){
														events[i].backgroundColor = 'green';
														events[i].tipo = 'laborable';
														p.$cal.fullCalendar('updateEvent', events[i]);
													}
												}
												p.$cal.fullCalendar( 'removeEvents' , calEvent._id );
												K.closeWindow(p.$mod.attr('id'));
											}
										},
										onClose: function(){ p.$mod = null; },
										onContentLoaded: function(){
											p.$mod = $('#modalTurno');
											p.$mod.find( "[name=ini]" ).timepicker({
										        showOn: 'button',
										        button: '[name=btnIni]'
											}).timepicker('setTime',calEvent.start);
											p.$mod.find('[name=btnIni]').button({icons: {primary: 'ui-icon-clock'},text: false}).hide();
											p.$mod.find( "[name=fin]" ).timepicker({
										        showOn: 'button',
										        button: '[name=btnFin]'
											}).timepicker('setTime',calEvent.end);
											p.$mod.find('[name=btnFin]').button({icons: {primary: 'ui-icon-clock'},text: false}).hide();
										}
									});
								}
							//}
						},
						eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
					        /*if (ciHelper.date.compare(p.date,event.start)==1) {
					        	K.clearNoti();
					            K.notification({title: 'Evento inv&aacute;lido',text: 'El evento debe ser creado despu&eacute;s del d&iacute;a de hoy!',type: 'info'});
					            revertFunc();
					        }*/
					    },
					    height: p.$mainPanel.height(),
						firstDay: 1,
						viewDisplay: function (view) {
							$('.fc-header-left').hide();
					    }
					});
					p.$mainPanel.resize(function(){
						p.$cal.fullCalendar('option', 'height', p.$mainPanel.height());
					}).resize();
					p.$cal.find('.fc-button').click(function(){
						p.$cal.find('.fc-feriado').removeClass('fc-feriado');
						var date = p.$cal.fullCalendar( 'getDate' ),
						day = date.getDay();
						if(day!=1){
							date = new Date(date.getTime() - ((day-1) * 24 * 3600 * 1000));
						}
						var ini = ciHelper.date.format.dayBDNotHour(date),
						fin = ciHelper.date.format.dayBDNotHour(new Date(date.getTime() + (7 * 24 * 3600 * 1000)));
						for(var i=0; i<p.feriados.length; i++){
							if(ciHelper.date.inRange(p.feriados[i].start,ini,fin)==true){
								p.$cal.find('tbody .fc-col'+(p.feriados[i].start.getDay()-1)).addClass('fc-feriado');
							}
						}
					}).click();
					p.$l.layout({
						south__size:		90,
						south__resizable:	false,
						south__slidable:	false
					});
					p.$l.css({'overflow-x':'hidden','overflow-y':'hidden'});
					p.$l.find('.ui-layout-center').css({'overflow-x':'hidden','overflow-y':'hidden'});
					p.$l.find('.ui-layout-center .grid:first').css({'overflow-x':'hidden','overflow-y':'hidden'});
					p.$l.find('.ui-layout-center .grid:last').height((p.$l.find('.ui-layout-center').height()-60-p.$l.find('.ui-layout-center .grid:first').height())+'px')
					p.$l.find('[name=btnTra]').data('data',{asig:[],sec:false,dias:[],inci:[]});
					p.$l.find('[name=btnTra]').click(function(){
						K.notification({text: 'Debe seleccionar una entidad para ver su horario!',hide: false,type: 'info'});
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
						    {nomb: 'roles.trabajador',value: {$exists: true}},
						    {nomb: 'roles.trabajador.turno',value: {$exists: true}}
						]});
					}).button({icons: {primary: 'ui-icon-search'}}).click();
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
				    	 if(p.enti!=null){
							p.$cal.fullCalendar( 'removeEvents' );
							$.post('pe/hora/asis_hor','enti='+p.enti._id.$id+'&turno='+p.enti.roles.trabajador.turno._id.$id,function(data){
								if(data.turno!=null) p.turno = data.turno;
								else p.turno = [];
								if(data.asig!=null){
									p.asig = [];
									for(var i=0,j=data.asig.length; i<j; i++){
										if(data.asig[i].programado!=null){
											if(ciHelper.date.compare(p.date,new Date(data.asig[i].programado.inicio.sec*1000))==1) color = 'gray';
											else color = 'green';
											var hour_end = new Date(data.asig[i].programado.fin.sec*1000);
											if(hour_end.getHours()=='0'&&hour_end.getMinutes()=='0'){
												hour_end.setHours('24');
												hour_end.setMinutes('00');
											}
											p.asig.push({
												title: 'Turno Programado',
												allDay: false,
												start: new Date(data.asig[i].programado.inicio.sec*1000),
												end: hour_end,
												tipo: 'laborable',
												_mid: data.asig[i]._id.$id,
												backgroundColor: color
											});
										}
									}
								}else p.asig = [];
								p.$cal.fullCalendar( 'refetchEvents' );
							},'json');
						}
				    });
					K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
		new K.Panel({
			id: 'mainPanel',
			content: '<div name="calendar"></div>',
			onContentLoaded: function(){
				p.$mainPanel = $('#mainPanel');
			}
		});
		
		$('#pageWrapperMain').layout({
			west__closable: false,
			west__resizable: false
		});
	}
};