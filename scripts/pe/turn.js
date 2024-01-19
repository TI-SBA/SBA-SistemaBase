peTurn = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peTurn',
			titleBar: {
				title: 'Turnos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Programa',{n:'Nombre',f:'nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'pe/turn/lista',
					params: {},
					itemdescr: 'turno(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peTurn.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+peTurn.states[data.estado].label+'</td>');
						var programa = '--';
						if(data.programa!=null){
							programa = data.programa.nomb;
						}
						$row.append('<td>'+programa+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peTurn.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peTurn.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peTurn.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/turn/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peTurn.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/turn/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peTurn.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
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
		new K.Panel({ 
			title: 'Nuevo Turno',
			contentURL: 'pe/turn/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							tipo: p.$w.find('[name=tipo]').val(),
							programa: p.$w.find('[name=programa]').data('data'),
							dias: []
						},tmp = p.$cal.fullCalendar( 'clientEvents');
						if(data.programa==null){
							p.$w.find('[name=btnProg]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un programa!',type: 'error'});
						}else data.programa = mgProg.dbRel(data.programa);
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un nombre de turno!',type: 'error'});
						}else if(tmp.length<1){
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar al menos un periodo de trabajo por turno!',type: 'error'});
						}else{
							for(var i=0; i<tmp.length; i++){
								var per = {
									dia: tmp[i].start._d.getDay(),
									horas: {
										ini: ciHelper.date.format.hi(tmp[i].start._d),
										fin: ciHelper.date.format.hi(tmp[i].end._d)
									}
								};
								if(tmp[i].tipo!=null) per.tipo = tmp[i].tipo;
								data.dias.push(per);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/turn/save',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: 'El turno fue registrado con &eacute;xito!'});
							peTurn.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						peTurn.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnProg]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=programa]').html(data.nomb).data('data',data);
					}});
				});
				p.eventos = [];
				p.$cal = p.$w.find('[name=calendar]').fullCalendar({
					ignoreTimezone: false,
					defaultView: 'agendaWeek',
					header: false,
					allDaySlot: false,
					editable: true,
					selectable: true,
					selectHelper: true,
					events: p.eventos,
					select: function(start, end, allDay) {
				        var newevent = {
				        	title: 'Horas Laborables',
				            constraint: 'Horas Laborables',
				            start: start,
				            _start: start,
				            end: end,
				            _end: end,
							allDay: false,
				            editable: true,
							stick: true
				        };
				        p.$cal.fullCalendar('renderEvent', newevent, true);
				        p.$cal.fullCalendar('addEventSource', newevent);
				        p.$cal.fullCalendar('refetchEvents');
					},
					eventClick: function(calEvent, jsEvent, view) {
						peTurn.modifyPer({
							calEvent: calEvent,
							$cal: p.$cal
						});
				    },
					height: 460,
					firstDay: 0
				});
				var $head = p.$w.find('.fc-widget-header');
				$head.find('.fc-sun:first').html('Domingo');
				$head.find('.fc-mon:first').html('Lunes');
				$head.find('.fc-tue:first').html('Martes');
				$head.find('.fc-wed:first').html('Mi&eacute;rcoles');
				$head.find('.fc-thu:first').html('Jueves');
				$head.find('.fc-fri:first').html('Viernes');
				$head.find('.fc-sat:first').html('S&aacute;bado');
			}
		});
	},
	windowEdit: function(p){
		new K.Panel({
			title: 'Editar turno: '+p.nomb,
			contentURL: 'pe/turn/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							tipo: p.$w.find('[name=tipo]').val(),
							programa: p.$w.find('[name=programa]').data('data'),
							dias: []
						},tmp = p.$cal.fullCalendar( 'clientEvents');
						if(data.programa==null){
							p.$w.find('[name=btnProg]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un programa!',type: 'error'});
						}else data.programa = mgProg.dbRel(data.programa);
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un nombre de turno!',type: 'error'});
						}else if(tmp.length<1){
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar al menos un periodo de trabajo por turno!',type: 'error'});
						}else{
							for(var i=0; i<tmp.length; i++){
								console.log(tmp[i].start._d);
								var per = {
									dia: tmp[i].start._d.getDay(),
									horas: {}
								};
								if(tmp[i].constraint!=null){
									per.horas.ini = ciHelper.date.format.hi(tmp[i].start._d);
									per.horas.fin = ciHelper.date.format.hi(tmp[i].end._d);
								}else{
									per.horas.ini = ciHelper.date.format.hi(tmp[i].start._d,5);
									per.horas.fin = ciHelper.date.format.hi(tmp[i].end._d,5);
								}
								console.info(per);
								if(tmp[i].tipo!=null) per.tipo = tmp[i].tipo;
								data.dias.push(per);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/turn/save',data,function(){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: 'El turno fue actualizado con &eacute;xito!'});
							peTurn.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						peTurn.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnProg]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=programa]').html(data.nomb).data('data',data);
					}});
				});
				K.block();
				$.post('pe/turn/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=tipo]').val(data.tipo);
					if(data.programa!=null){
						p.$w.find('[name=programa]').html(data.programa.nomb).data('data',data.programa);
					}
					var date = new Date(),
					dd = date.getDay(),
					d = date.getDate(),
					m = date.getMonth(),
					y = date.getFullYear(),
					events = [];
					for(var i=0; i<data.dias.length; i++){
						var day = d;
						if(parseInt(data.dias[i].dia)>dd){
							day = d + (data.dias[i].dia-dd);
						}else if(parseInt(data.dias[i].dia)<dd){
							day = d - (dd-data.dias[i].dia);
						}
						if(data.dias[i].horas.fin=='00:00')
							data.dias[i].horas.fin = '24:00';
						/*var date_start = new Date(Date.UTC(y,m,day,data.dias[i].horas.ini.substring(0,2),data.dias[i].horas.ini.substring(3,5),0));
						var date_end = new Date(Date.UTC(y,m,day,data.dias[i].horas.fin.substring(0,2),data.dias[i].horas.fin.substring(3,5),0));*/
						var date_start = new Date(y,m,day,data.dias[i].horas.ini.substring(0,2),data.dias[i].horas.ini.substring(3,5),0);
						//date_start.setUTCHours(data.dias[i].horas.ini.substring(0,2));
						var date_end = new Date(y,m,day,data.dias[i].horas.fin.substring(0,2),data.dias[i].horas.fin.substring(3,5),0);
						//date_end.setUTCHours(data.dias[i].horas.ini.substring(0,2));
						if(date_start>date_end){
              				date_end.setDate(day+1);
						}
						var tmp_ev = {
							title: 'Horas Laborables',
							allDay: false,
							start: date_start,
							end: date_end
						};
						if(data.dias[i].tipo!=null) tmp_ev.tipo = data.dias[i].tipo;
						events.push(tmp_ev);
					}
					p.$cal = p.$w.find('[name=calendar]').fullCalendar({
						defaultView: 'agendaWeek',
						header: false,
						allDaySlot: false,
						editable: true,
						selectable: true,
						events: events,
						select: function(start, end, allDay) {
					        var newevent = {
					        	title: 'Horas Laborables',
					            constraint: 'Horas Laborables',
					            start: start,
					            _start: start,
					            end: end,
					            _end: end,
								allDay: false,
					            editable: true,
								stick: true
					        };
					        p.$cal.fullCalendar('renderEvent', newevent, true);
					        p.$cal.fullCalendar('addEventSource', newevent);
					        p.$cal.fullCalendar('refetchEvents');
						},
						eventClick: function(calEvent, jsEvent, view) {
							peTurn.modifyPer({
								calEvent: calEvent,
								$cal: p.$cal
							});
					    },
						height: 460,
						firstDay: 0
					});
					var $head = p.$w.find('.fc-widget-header');
					$head.find('.fc-sun:first').html('Domingo');
					$head.find('.fc-mon:first').html('Lunes');
					$head.find('.fc-tue:first').html('Martes');
					$head.find('.fc-wed:first').html('Mi&eacute;rcoles');
					$head.find('.fc-thu:first').html('Jueves');
					$head.find('.fc-fri:first').html('Viernes');
					$head.find('.fc-sat:first').html('S&aacute;bado');
					K.unblock();
				},'json');
			}
		});
	},
	modifyPer: function(p){
		if(p.calEvent.tipo!=null) p.tipo = true;
		else p.tipo = false;
		new K.Modal({
			id: 'modalTurno',
			title: 'Turno',
			contentURL: 'pe/turn/modal',
			height: 600,
			width: 450,
			buttons: {
				"Actualizar": {
					icon: 'fa-refresh',
					type: 'success',
					f: function(){
						K.clearNoti();
						var day = p.calEvent.start._d.getDate();
						var start = p.$mod.find( "[name=ini]" ).val();
						if(start.indexOf(':')==1) start = '0'+start;
						var hor = start.substring(0,2);
						/*if(hor=='12'){
							if(start.substring(6,8)=='AM') hor = '0'; 
						}else if(start.substring(6,8)=='PM'){
							hor = parseInt(hor)+12;
						};*/
						p.calEvent.start._d.setUTCHours(hor);
						p.calEvent.start._d.setMinutes(start.substring(3,5));
						p.calEvent.start._d.setDate(day);
						if(p.calEvent.constraint==null){
							p.calEvent.start._d.setUTCHours(parseInt(hor)+5);
						}
						if(p.calEvent.end==null)
	            var day = p.calEvent.end = new Date();
						var day = p.calEvent.end._d.getDate();
						var fin = p.$mod.find( "[name=fin]" ).val();
						if(fin.indexOf(':')==1) fin = '0'+fin;
						var hor = fin.substring(0,2);
						/*if(hor=='12'){
							if(fin.substring(6,8)=='AM') hor = '0'; 
						}else if(fin.substring(6,8)=='PM'){
							hor = parseInt(hor)+12;
						};*/
						p.calEvent.end._d.setUTCHours(hor);
						p.calEvent.end._d.setMinutes(fin.substring(3,5));
						p.calEvent.end._d.setDate(day);
						if(p.calEvent.constraint==null){
							p.calEvent.end._d.setUTCHours(parseInt(hor)+5);
						}
						var comp = ciHelper.date.compare(p.calEvent.start._d,p.calEvent.end._d);
						if(!(comp==-1)){
	            p.calEvent.end._d.setDate(day+1);
						}
						/*if(!(comp==-1)){
							return K.notification({title: 'Formato inv&aacute;lido',text: 'La hora de inicio es mayor a la de fin!',type: 'error'});
						}else{*/




						//if(p.tipo==true){
							p.calEvent.tipo = p.$mod.find('[name=tipo] option:selected').val();
						//}
						console.info(p.calEvent);




							p.$cal.fullCalendar('updateEvent', p.calEvent);
							K.closeWindow(p.$mod.attr('id'));
						//}
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'warning',
					f: function(){ K.closeWindow(p.$mod.attr('id')); }
				},
				"Eliminar": {
					icon: 'fa-trash',
					type: 'danger',
					f: function(){
						if(p.calEvent._mid!=null) p.del.push(p.calEvent._mid);
						var events = p.$cal.fullCalendar( 'clientEvents');
						p.$cal.fullCalendar( 'removeEvents' , p.calEvent._id );
						K.closeWindow(p.$mod.attr('id'));
					}
				}
			},
			onClose: function(){ p.$mod = null; },
			onContentLoaded: function(){
				console.log(p.calEvent);
				p.$mod = $('#modalTurno');
				if(p.tipo==false){
					//p.$mod.find('[name=tipo]').closest('.form-group').hide();
				}else{
					p.$mod.find('[name=tipo]').val(p.calEvent.tipo);
				}
				p.$mod.find( "[name=ini]" ).datetimepicker({
			        format: 'HH:mm'
				}).val(ciHelper.date.format.hi(p.calEvent.start._d,5));
				p.$mod.find( "[name=fin]" ).datetimepicker({
			        format: 'HH:mm'
				}).val(ciHelper.date.format.hi(p.calEvent.end._d,5));
				if(p.calEvent.constraint!=null){
					p.$mod.find( "[name=ini]" ).val(ciHelper.date.format.hi(p.calEvent.start._d));
					p.$mod.find( "[name=fin]" ).val(ciHelper.date.format.hi(p.calEvent.end._d));
				}else{
					p.$mod.find( "[name=ini]" ).val(ciHelper.date.format.hi(p.calEvent.start._d,5));
					p.$mod.find( "[name=fin]" ).val(ciHelper.date.format.hi(p.calEvent.end._d,5));
				}
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Turno',
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
					cols: ['','Nombre','Programa'],
					data: 'pe/turn/lista',
					params: {},
					itemdescr: 'turno(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.programa.nomb+'</td>');
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
	['mg/enti','mg/prog'],
	function(mgEnti,mgProg){
		return peTurn;
	}
);