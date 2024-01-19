/*******************************************************************************
vacaciones */
peVaca = {
	init: function(p){
		p = {
			date: new Date()
		};
		p.date.setHours(0);
		p.date.setMinutes(0);
		K.initMode({
			mode: 'pe',
			action: 'peVaca',
			titleBar: {
				title: 'Vacaciones'
			}
		});
		
		new K.Panel({
			id: 'westPanel',
			position: 'left',
			contentURL: 'pe/vaca',
			onContentLoaded: function(){
				p.$l = $('#westPanel');
				p.$l.find('[name=btnTra]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.enti = data;
						p.$l.find('[name=nomb]').html(ciHelper.enti.formatName(data));
						p.$l.find('[name=cargo]').html(data.roles.trabajador.cargo.nomb);
						p.$l.find('[name=tarjeta]').html(data.roles.trabajador.cod_tarjeta);
						p.$l.find('[name=local]').html(data.roles.trabajador.local.descr);
						$.post('pe/vaca/get','enti='+data._id.$id,function(data){
							if(data!=null){
								p.asig = [];
								for(var i=0,j=data.length; i<j; i++){
									p.asig.push({
										title: 'Vacaciones',
										allDay: true,
										start: new Date(data[i].fecini.sec*1000),
										end: new Date(data[i].fecfin.sec*1000),
										tipo: data[i].tipo,
										_mid: data[i]._id.$id,
										observ: data[i].observ
									});
								}
							}else p.asig = [];
							p.$cal.fullCalendar( 'refetchEvents' );
						},'json');
					},filter: [
						{nomb: 'tipo_enti',value: 'P'},
						{nomb: 'roles.trabajador',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$l.find('[name=btnGuardar]').click(function(){
					if(p.enti==null){
						p.$l.find('[name=btnTra]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
					}
					var events = p.$cal.fullCalendar( 'clientEvents'),
					data = [],
					trab = {
						_id: p.enti._id.$id,
						tipo_enti: p.enti.tipo_enti,
						nomb: p.enti.nomb,
						appat: p.enti.appat,
						apmat: p.enti.apmat
					};
					for(var i=0; i<events.length; i++){
						var tmp = {
							fecini: ciHelper.date.format.dayBD(events[i].start),
							fecfin: ciHelper.date.format.dayBD(events[i].end),
							observ: events[i].observ
						};
						tmp.tipo = {
							_id: events[i].tipo._id.$id,
							nomb: events[i].tipo.nomb,
							tipo: events[i].tipo.tipo,
							goce_haber: events[i].tipo.goce_haber,
							subsidiado: events[i].tipo.subsidiado
						};
						/*
						*Falta diferencia de dias
						*/
						if(events[i]._mid!=null) tmp._id = events[i]._mid;
						data.push(tmp);
					}
					K.sendingInfo();
					$(this).button('disable');
					$.post('pe/vaca/save',{
						trabajador: trab,
						data: data,
						del: p.$cal.data('del')
					},function(data){
						K.clearNoti();
						K.notification({title: 'Vacaciones guardadas!',text: 'Se realiz&oacute; con &eacute;xito el guardado de bloques programados para '+ciHelper.enti.formatName(p.enti)+'!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				}).button({icons: {primary: 'ui-icon-disk'}});
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		new K.Panel({
			id: 'mainPanel',
			content: '<div name="calendar"></div>',
			onContentLoaded: function(){
				p.$mainPanel = $('#mainPanel');
				p.$cal = p.$mainPanel.find('[name=calendar]').fullCalendar({
					slotMinutes: 1,
					editable: true,
					selectable: true,
					eventSources: [
						{
							events: function(start, end, callback) {
								if(p.enti!=null){
									callback(p.asig);
								}
							}
						}
					],
					select: function(start, end, allDay){
						if(ciHelper.date.compare(p.date,start)!=1){
							peVaca.edit({
								start: start,
								end: end,
								$cal: p.$cal
							});
						}
					},
					eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
				        if (ciHelper.date.compare(p.date,event.start)==1) {
				        	K.clearNoti();
				            K.notification({title: 'Evento inv&aacute;lido',text: 'El evento debe ser creado despu&eacute;s del d&iacute;a de hoy!',type: 'info'});
				            revertFunc();
				        }
				    },
					eventClick: function(calEvent, jsEvent, view) {
						peVaca.edit({
							event: calEvent,
							$cal: p.$cal
						});
					},
				    height: p.$mainPanel.height(),
					firstDay: 1
				});
				p.$mainPanel.resize(function(){
					p.$cal.fullCalendar('option', 'height', p.$mainPanel.height());
				});
				p.$cal.data('del',[]);
			}
		});
		$('#pageWrapperMain').layout({
			west__closable: false,
			west__resizable: false
		});
	},
	edit: function(p){
		if(p.event!=null){
			p.buttons = {
				"Actualizar": function(){
					K.clearNoti();
					p.event.tipo = p.$mod.find("[name=tipo] option:selected").data('data');
					p.event.observ = p.$mod.find("[name=observ]").val();
					p.event.start = p.$mod.find( "[name=ini]" ).datepicker( "getDate" );
					p.event.end = p.$mod.find( "[name=fin]" ).datepicker( "getDate" );
					var comp = ciHelper.date.compare(p.event.start,p.event.end);
					if(!(comp==-1||comp==0)){
						return K.notification({title: 'Formato inv&aacute;lido',text: 'La hora de inicio es mayor a la de fin!',type: 'info'});
					}
					p.$cal.fullCalendar('updateEvent', p.event);
					K.closeWindow(p.$mod.attr('id'));
				},
				"Eliminar": function(){
					if(p.event._mid!=null){
						var del = p.$cal.data('del');
						del.push(p.event._mid);
						p.$cal.data('del',del);
					}
					p.$cal.fullCalendar( 'removeEvents' , p.event._id );
					K.closeWindow(p.$mod.attr('id'));
				}
			};
		}else{
			p.buttons = {
				"Guardar": function(){
					var comp = ciHelper.date.compare(p.$mod.find( "[name=ini]" ).datepicker( "getDate" ),p.$mod.find( "[name=fin]" ).datepicker( "getDate" ));
					if(!(comp==-1||comp==0)){
						return K.notification({title: 'Formato inv&aacute;lido',text: 'La fecha de inicio es mayor a la de fin!',type: 'info'});
					}
					p.$cal.fullCalendar('renderEvent',{
						title: 'Vacaciones',
						start: p.$mod.find( "[name=ini]" ).datepicker( "getDate" ),
						end: p.$mod.find( "[name=fin]" ).datepicker( "getDate" ),
						allDay: true,
						tipo: p.$mod.find("[name=tipo] option:selected").data('data'),
						observ: p.$mod.find("[name=observ]").val()
					},true);
					K.closeWindow(p.$mod.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$mod.attr('id'));
				}
			};
		}
		new K.Modal({
			id: 'modalTurno',
			title: 'Turno',
			contentURL: 'pe/vaca/modal',
			height: 170,
			width: 390,
			icon: 'ui-icon-calendar',
			buttons: p.buttons,
			onClose: function(){ p.$mod = null; },
			onContentLoaded: function(){
				p.$mod = $('#modalTurno');
				K.block({$element: p.$mod});
				$.post('pe/tipo/vaca',function(data){
					p.$select = p.$mod.find('[name=tipo]');
					for(var i=0,j=data.length; i<j; i++){
						p.$select.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						p.$select.find('option:last').data('data',data[i]);
					}
					if(p.event!=null){
						var start = p.event.start;
						var end = p.event.end;
						p.$select.selectVal(p.event.tipo._id.$id);
						p.$mod.find('[name=observ]').val(p.event.observ);
					}else{
						var start = p.start;
						var end = p.end;
					}
					p.$mod.find( "[name=ini]" ).val(ciHelper.date.format.dayBDNotHour(start)).datepicker();
					p.$mod.find( "[name=fin]" ).val(ciHelper.date.format.dayBDNotHour(end)).datepicker();
					K.unblock({$element: p.$mod});
				},'json');
			}
		});
	}
};