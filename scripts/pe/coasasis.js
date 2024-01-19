/*******************************************************************************
control de horarios asistencia */
peCoasAsis = {
	states: [
 		{
 			descr: "Consistente",
			color: 'blue'
		},
		{
			descr: "Inconsistente",
			color: 'gray'
		}
	],
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peCoasAsis',
			titleBar: {
				title: 'Control de Asistencia: Asistencia'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','Nombre','Horas Trabajadas','Bloques sin cerrar'],
					data: 'pe/hora/lista',
					params: {fecini: '2016', fecfin:'2016'},
					itemdescr: 'concepto(s)',
					toolbarHTML: '<input type="text" name="fecini" style="width:100px"> <input type="text" name="fecfin" style="width:100px">',
					onContentLoaded: function($el){
						
					},
					onLoading: function(){ 
						K.block();
					},
					onComplete: function(){ 
						K.unblock();
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+ciHelper.enti.formatName(data)+'</td>');
						$row.append('<td>'+K.round(data.hora_trab,4)+'</td>');
						$row.append('<td>'+data.blo+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peCoasAsis.windowDetails({id: $(this).data('id'),enti: $(this).data('data')});
						}).data('estado',data.estado).data('data',data).contextMenu("conMenPeConc", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='H') $('#conMenPeConc_hab',menu).remove();
								else $('#conMenPeConc_edi,#conMenPeConc_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeAsis_con': function(t) {
									peCoasAsis.windowDetails({id: K.tmp.data('id'),enti: K.tmp.data('enti')});
								},
								'conMenPeAsis_mar': function(t) {
									peCoasAsis.windowDetails({id: K.tmp.data('id'),enti: K.tmp.data('enti'),edit: true});
								},
								'conMenPeAsis_reg': function(t) {
									peCoasAsis.windowRegi({id: K.tmp.data('id'),enti: K.tmp.data('enti')});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	loadData: function(params){
		if(params==null) params = {page: 1};
		$.extend(params,{
			tipo: $mainPanel.find('[name=tipo_cont] option:selected').val(),
			texto: $mainPanel.find('[name=buscar]').val(),
			ano: $mainPanel.find('[name=periodo]').data('ano'),
			mes: parseFloat($mainPanel.find('[name=periodo]').data('mes'))+1,
			page_rows: 20,
		    page: (params.page) ? params.page : 1
		});
	    $.post('pe/hora/lista', params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					var result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( ciHelper.enti.formatName(result) );
					$li.eq(2).html( K.round(result.hora_trab,4) );
					$li.eq(3).html( result.blo );
					$row.wrapInner('<a class="item" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						peCoasAsis.windowDetails({id: $(this).data('id'),enti: $(this).data('enti')});
					}).data('enti',result).data('data', result).contextMenu("conMenPeAsis", {
							onShowMenu: function($row, menu) {
								if(parseFloat($row.data('enti').blo)==0) $('#conMenPeAsis_mar',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeAsis_con': function(t) {
									peCoasAsis.windowDetails({id: K.tmp.data('id'),enti: K.tmp.data('enti')});
								},
								'conMenPeAsis_mar': function(t) {
									peCoasAsis.windowDetails({id: K.tmp.data('id'),enti: K.tmp.data('enti'),edit: true});
								},
								'conMenPeAsis_reg': function(t) {
									peCoasAsis.windowRegi({id: K.tmp.data('id'),enti: K.tmp.data('enti')});
								}
							}
						});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results [name=showing]').html( count );
		        $('#Results [name=founded]').html( data.paging.total_items );
		        $('#Results').show();
		        
		        $moreresults = $("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						peCoasAsis.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
		        }else{
					$("#mainPanel .gridFoot").hide();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
		        }
	      } else {
	        $('#No-Results').show();
	        $('#Results').hide();
	        $( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowDetails: function(p){
		$.extend(p,{
			date: new Date(),
			feriados: [],
			turno: [],
			asig: [],
			del: []
		});
		p.date.setHours(0);
		p.date.setMinutes(0);
		new K.Modal({
			id: 'windowAsis',
			title: 'Control de Asistencia: '+ciHelper.enti.formatName(p.enti),
			icon: 'ui-icon-calendar',
			width: 800,
			height: 550,
			contentURL: 'pe/hora/calen',
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$l = $('#windowAsis');
				$(window).resize(function(){
					var $this = $('#windowAsis');
					$this.dialog( "option", "height", $(window).height() )
						.dialog( "option", "width", $(window).width() )
						.dialog( "option", "position", [ 0 , 0 ] )
						.dialog( "option", "draggable", false )
						.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
				K.block({$element: p.$l});
				p.$l.find('[name=btnTra]').remove();
				p.$l.find('[name=btnGuardar]').remove();
				var tmp_ano = $mainPanel.find('[name=periodo]').data('ano');
				var tmp_mes = parseFloat($mainPanel.find('[name=periodo]').data('mes'))+1;
				tmp_mes = ''+tmp_mes;
				if(tmp_mes.length==1) tmp_mes = '0'+tmp_mes;
				p.$l.find('[name=fecref]').datepicker().change(function(){
			    	 p.$cal.fullCalendar('gotoDate', $(this).datepicker('getDate'));
			    	 if(p.enti!=null){
			    		 $.post('pe/hora/marc',{
							enti: p.enti._id.$id,
							ini: ciHelper.date.format.dayBD(p.$cal.fullCalendar('getView').start),
							fin: ciHelper.date.format.dayBD(p.$cal.fullCalendar('getView').end)
			    		 },function(data){
							if(data!=null){
								p.asig = [];
								for(var i=0; i<data.length; i++){
									if(data[i].ejecutado!=null){
										var tmp_event = {
											allDay: false,
											start: new Date(data[i].ejecutado.entrada.fecreg.sec*1000),
											data: data[i]
										};
										if(data[i].ejecutado.salida==null) color = 'gray';
										else color = 'blue';
										if(data[i].ejecutado.salida!=null){
											var fin = new Date(data[i].ejecutado.salida.fecreg.sec*1000),
											title = data[i].ejecutado.entrada.equipo.local.descr+' ('+data[i].ejecutado.entrada.equipo.cod+' - '+data[i].ejecutado.salida.equipo.cod+')';
										}else if(ciHelper.date.format.dayBDNotHour(new Date(data[i].ejecutado.entrada.fecreg.sec*1000))!=ciHelper.date.format.dayBDNotHour(new Date())){
											var ini = new Date(data[i].ejecutado.entrada.fecreg.sec*1000),
											fin = new Date(ini.getTime() + (3600 * 1000)),
											title = 'Sin Cierre';
											tmp_event.edit = true;
										}else{
											var fin = new Date(),
											title = data[i].ejecutado.entrada.equipo.local.descr+' ('+data[i].ejecutado.entrada.equipo.cod+')';
										}
										if(data[i].manual!=null){
											tmp_event.edit = true;
											tmp_event.remove = true;
										}
										tmp_event.title = title;
										tmp_event.end = fin;
										tmp_event.backgroundColor = color;
										p.asig.push(tmp_event);
									}
								}
							}else p.asig = [];
							p.$cal.fullCalendar( 'refetchEvents' );
						},'json');
			    	 }
			    }).datepicker("setDate",tmp_ano+'-'+tmp_mes+'-01');
				$.post('pe/feri/lista','year='+ciHelper.date.getYear(),function(data){
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
					p.$cal = p.$l.find('[name=calendar]').fullCalendar({
						defaultView: 'agendaWeek',
						slotMinutes: 5,
						eventSources: [
						    {
					            events: function(start, end, callback) {
					                callback(p.feriados);
					            }
					        },
						    {
					            events: function(start, end, callback) {
					                callback(p.asig);
					            },
					            color: 'green',
					            textColor: 'white'
					        }
						],
						eventClick: function(calEvent, jsEvent, view) {
							//K.notification(calEvent.title);
							//if(p.edit==true){
								//if(calEvent.edit==true){
									peCoasAsis.windowRegi({
										id: K.tmp.data('id'),
										enti: K.tmp.data('enti'),
										event: calEvent,
										callback: function(data){
											calEvent.start = new Date(data.ejecutado.entrada.fecreg.sec*1000);
											calEvent.end = new Date(data.ejecutado.salida.fecreg.sec*1000);
											calEvent.title = data.ejecutado.entrada.equipo.local.descr+' ('+data.ejecutado.entrada.equipo.cod+' - '+data.ejecutado.salida.equipo.cod+')';
											calEvent.backgroundColor = 'blue';
											p.$cal.fullCalendar('updateEvent', calEvent);
										},
										callbackRemove: function(data){
											p.asig_cop = p.asig;
											p.asig = [];
											for(var iii=0;iii<p.asig_cop.length; iii++){
												if(p.asig_cop[iii].data._id.$id!=data.data._id.$id){
													p.asig.push(p.asig_cop[iii]);
												}
											}
											p.$cal.fullCalendar( 'removeEvents' , data._id );
											p.$cal.fullCalendar( 'refetchEvents' );
											$.post('pe/hora/delete_asis',{_id: data.data._id.$id},function(){
												K.notification({
													title: 'Asistencia Manual Eliminada',
													text: 'Se elimin&oacute; con &eacute;xito la asistencia seleccionada!'
												});
											});
										}
									});
								//}
							//}
						},
					    height: p.$l.find('.ui-layout-center').height(),
						firstDay: 1
					});
					p.$cal.fullCalendar('gotoDate', p.$l.find('[name=fecref]').datepicker('getDate'));
					p.$l.find('.fc-button-prev').remove();
					p.$l.find('.fc-button-next').remove();
					p.$l.layout({
						west__closable: false,
						west__resizable: false
					});
					p.$l.find('[name=nomb]').html(ciHelper.enti.formatName(p.enti));
					p.$l.find('[name=cargo]').html(p.enti.roles.trabajador.cargo.nomb);
					p.$l.find('[name=tarjeta]').html(p.enti.roles.trabajador.cod_tarjeta);
					p.$l.find('[name=local]').html(p.enti.roles.trabajador.local.descr);
					p.$l.resize(function(){
						p.$cal.fullCalendar('option', 'height', p.$l.height()-5);
					}).resize();
					p.$l.find('.ui-layout-south fieldset').css('padding','0px');
					for(var i=0,j=peCoasAsis.states.length; i<j; i++){
						var $row = p.$l.find('.ui-layout-west .gridReference:last').clone();
						$row.find('li:eq(0)').css('background',peCoasAsis.states[i].color).addClass('vtip').attr('title',peCoasAsis.states[i].descr);
						$row.find('li:eq(1)').html(peCoasAsis.states[i].descr);
						$row.wrapInner('<a class="item" />');
			        	p.$l.find(".ui-layout-west .gridBody:last").append( $row.children() );
					}
					p.$l.find('.ui-layout-west').layout({
						south__size:		90,
						south__resizable:	false,
						south__slidable:	false
					});
					p.$l.find('[name=fecref]').change();
					K.unblock({$element: p.$l});
				},'json');
			}
		});
	},
	windowRegi: function(p){
		var buttons = {
			"Guardar": function(){
				K.clearNoti();
				var data = {
					enti: ciHelper.enti.dbTrabRel(p.enti),
					tarjeta: p.enti.roles.trabajador.cod_tarjeta
				},tmp = p.$w.find('[name=equipo]').data('data');
				if(tmp==null){
					p.$w.find('[name=btnEquipo]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un equipo!',type: 'error'});
				}else if(p.$w.find('[name=dia]').val()==''){
					p.$w.find('[name=dia]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un d&iacute;a!',type: 'error'});
				}else if(p.$w.find('[name=ini]').val()==''){
					p.$w.find('[name=ini]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un inicio!',type: 'error'});
				}else if(p.$w.find('[name=fin]').val()==''){
					p.$w.find('[name=fin]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un fin!',type: 'error'});
				}
				var dia = p.$w.find('[name=dia]').datepicker('getDate'),
				ini = p.$w.find('[name=dia]').datepicker('getDate'),
				fin = p.$w.find('[name=dia]').datepicker('getDate');
				var start = p.$w.find( "[name=ini]" ).val();
				if(start.indexOf(':')==1) start = '0'+start;
				var hor1 = start.substring(0,2);
				/*if(hor1=='12'){
					if(start.substring(6,8)=='AM') hor1 = '0'; 
				}else if(start.substring(6,8)=='PM'){
					if(hor1!='12')
						hor1 = parseInt(hor1)+12;
				};*/
				ini.setHours(hor1);
				ini.setMinutes(start.substring(3,5));
				var end = p.$w.find( "[name=fin]" ).val();
				if(end.indexOf(':')==1) end = '0'+end;
				var hor2 = end.substring(0,2);
				/*if(hor2=='12'){
					if(end.substring(6,8)=='AM') hor2 = '0'; 
				}else if(end.substring(6,8)=='PM'){
					if(hor2!='12')
						hor2 = parseInt(hor2)+12;
				};*/
				fin.setHours(hor2);
				fin.setMinutes(end.substring(3,5));
				var day = fin.getDate();
				var comp = ciHelper.date.compare(ini,fin);
				if(!(comp==-1)){
					//return K.notification({title: 'Formato inv&aacute;lido',text: 'La hora de inicio es mayor a la de fin!',type: 'info'});
					fin.setDate(day+1);
				}
				$.extend(data,{
					equipo: {
						_id: tmp._id.$id,
						nomb: tmp.nomb,
						cod: tmp.cod,
						local: {
							_id: tmp.local._id.$id,
							descr: tmp.local.descr,
							direccion: tmp.local.direccion
						}
					},
					inicio: ciHelper.date.format.dayBD(ini),
					fin: ciHelper.date.format.dayBD(fin)
				});
				if(p.event!=null) data._id = p.event.data._id.$id;
				data.manual = true;
				K.sendingInfo();
				p.$w.find('.ui-dialog-buttonpane button').button('disable');
				$.post('pe/hora/save_asis',data,function(data){
					K.clearNoti();
					K.notification({title: ciHelper.titleMessages.regiGua,text: 'La asistencia fue registrada con &eacute;xito!'});
					if(p.callback!=null) p.callback(data);
					else{
						$mainPanel.find('.gridBody').empty();
						peCoasAsis.loadData();
					}
					K.closeWindow(p.$w.attr('id'));
				},'json');
			},
			"Cancelar": function(){
				K.closeWindow(p.$w.attr('id'));
			}
		};
		if(p.event!=null)
			if(p.event.remove!=null){
				$.extend(buttons,{
					'Eliminar': function(){
						if(p.callbackRemove!=null)
							p.callbackRemove(p.event);
						K.closeWindow(p.$w.attr('id'));
					}
				});
			}
		K.Modal({
			id: 'windowRegiAsis',
			title: 'Registrar Asistencia',
			contentURL: 'pe/hora/regi',
			width: 340,
			height: 290,
			icon: 'ui-icon-pencil',
			buttons: buttons,
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowRegiAsis');
				p.$w.find('[name=nomb]').html(ciHelper.enti.formatName(p.enti));
				p.$w.find('[name=tarjeta]').html(p.enti.roles.trabajador.cod_tarjeta);
				/*p.$w.find('[name=ini]').timespinner();
				p.$w.find('[name=fin]').timespinner();
				p.$w.find('.ui-button').css('height','14px');*/
				p.$w.find('[name=btnEquipo]').click(function(){
					peEqui.windowSelect({callback: function(data){
						p.$w.find('[name=equipo]').html(data.nomb).data('data',data);
						p.$w.find('[name=local]').html(data.local.descr);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find( "[name=ini]" ).timepicker({
			        showOn: 'button',
			        button: '[name=btnIni]'
				});
				p.$w.find('[name=btnIni]').button({icons: {primary: 'ui-icon-clock'},text: false}).hide();
				p.$w.find( "[name=fin]" ).timepicker({
			        showOn: 'button',
			        button: '[name=btnFin]'
				});
				p.$w.find('[name=btnFin]').button({icons: {primary: 'ui-icon-clock'},text: false}).hide();
				p.$w.find('[name=dia]').datepicker();
				if(p.event!=null){
					p.$w.find('[name=equipo]').html(p.event.data.ejecutado.entrada.equipo.nomb).data('data',p.event.data.ejecutado.entrada.equipo);
					p.$w.find('[name=local]').html(p.event.data.ejecutado.entrada.equipo.local.descr);
					p.$w.find( "[name=dia]" ).val(ciHelper.date.format.dayBDNotHour(p.event.start));
					/*var hor = p.event.start.getHours();
					/*var tmp = 'AM';
					if(hor>=12){
						hor = hor - 12;
						tmp = 'PM';
					}*
					var start = hor+':'+(p.event.start.getMinutes()<10?'0'+p.event.start.getMinutes():p.event.start.getMinutes())+' '+tmp;
					var hor = p.event.end.getHours();
					/*var tmp = 'AM';
					if(hor>=12){
						hor = hor - 12;
						tmp = 'PM';
					}*
					var fin = hor+':'+(p.event.end.getMinutes()<10?'0'+p.event.end.getMinutes():p.event.end.getMinutes())+' '+tmp;
					p.$w.find( "[name=ini]" ).val(start);
					p.$w.find( "[name=fin]" ).val(fin);*/
					p.$w.find( "[name=ini]" ).timepicker('setTime',p.event.start);
					p.$w.find( "[name=fin]" ).timepicker('setTime',p.event.end);
				}
			}
		});
	}
};
define(
	['pe/clas','pe/nive','pe/grup','pr/clas','ct/pcon'],
	function(peClas,peNive,peGrup,prClas,ctPcon){
		return peCoasAsis;
	}
);