/*******************************************************************************
feriados */
peFeri = {
	init: function(p){
		if(p==null){ p = {}; }
		$.extend(p,{
			date: new Date(),
			fer: [],
			save: function(){
				var events = p.$cal.fullCalendar('clientEvents'),
				data = [];
				for(var i=0; i<events.length; i++){
					if(ciHelper.date.compare(events[i].start._d,new Date(p.date.getFullYear(),0,1,0,0,0))!=-1 && ciHelper.date.compare(new Date(p.date.getFullYear(),11,31,23,59,59),events[i].start._d)!=-1){
						data.push({
							fec: events[i].start.format(),
							nomb: events[i].title
						});
					}
				}
				K.sendingInfo();
				K.block();
				$.post('pe/feri/save',{year: p.date.getFullYear(),data: data},function(){
					K.clearNoti();
					K.msg({title: 'Feriados almacenados!',text: 'Se realiz&oacute; con &eacute;xito el guardado de feriados para el '+p.date.getFullYear()+'!'});
					K.unblock();
					peFeri.init();
				});
			}
		});
		K.initMode({
			mode: 'pe',
			action: 'peFeri',
			titleBar: {
				title: 'Feriados'
			}
		});
		
		new K.Panel({
			contentURL: 'pe/feri',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n'],
					onlyHtml: true
				});
				p.$w.find('[name=year]').html(p.date.getFullYear());
				$.post('pe/feri/all',function(data){
					var events = [];
					if(data!=null){
						for(var i=0; i<data.length; i++){
							if(ciHelper.date.compare(new Date(data[i].fec.sec*1000),new Date(p.date.getFullYear(),0,1,0,0,0))!=-1&&ciHelper.date.compare(new Date(p.date.getFullYear(),11,31,23,59,59),new Date(data[i].fec.sec*1000))!=-1){
								var $row = $('<tr class="item" name="'+ciHelper.date.format.bd_ymd(data[i].fec)+'">');
								$row.append('<td>'+ciHelper.date.format.bd_ymd(data[i].fec)+' - <i>'+data[i].nomb+'</i></td>');
								p.$w.find('[name=grid] tbody').append( $row );
							}
							events.push({
								title: data[i].nomb,
								allDay: true,
								start: new Date(data[i].fec.sec*1000)
							});
						}
					}
					p.$cal = p.$w.find('[name=calendar]').fullCalendar({
						timezone: 'America/Lima',
						ignoreTimezone: false,
						editable: true,
						events: events,
						eventClick: function(calEvent, jsEvent, view) {
							if(ciHelper.date.compare(calEvent.start,new Date(p.date.getFullYear(),0,1,0,0,0))==-1){
								return K.msg({title: 'Fecha fuera de periodo v&aacute;lido',text: 'El feriado debe estar programado para el '+p.date.getFullYear(),type: 'info'});
							}else if(ciHelper.date.compare(new Date(p.date.getFullYear(),11,31,23,59,59),calEvent.start)==-1){
								return K.msg({title: 'Fecha fuera de periodo v&aacute;lido',text: 'El feriado debe estar programado para el '+p.date.getFullYear(),type: 'info'});
							}
							new K.Modal({
								id: 'modTitleFer',
								title: 'Modificar feriado',
								icon: 'ui-icon-calendar',
								content: '<div class="form-group">'+
									'<label class="col-sm-4 control-label">Nombre</label>'+
									'<div class="col-sm-8 input-group">'+
										'<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>'+
										'<input type="text" class="form-control" name="nomb">'+
									'</div>'+
								'</div>',
								height: 65,
								width: 450,
								buttons: {
									"Modificar": {
										icon: 'fa-save',
										type: 'success',
										f: function(){
											calEvent.title = p.$mod.find('[name=nomb]').val();
											p.$cal.fullCalendar('updateEvent', calEvent);
											p.$w.find('[name='+calEvent.start.format()+'] td:eq(0)').html(calEvent.start.format()+' - <i>'+p.$mod.find('[name=nomb]').val()+'</i>');
											p.save();
											K.closeWindow(p.$mod.attr('id'));
										}
									},
									"Eliminar": {
										icon: 'fa-trash',
										type: 'warning',
										f: function(){
											p.$w.find('[name='+calEvent.start.format()+']').remove();
											p.$cal.fullCalendar( 'removeEvents' , calEvent._id );
											p.save();
											K.closeWindow(p.$mod.attr('id'));
										}
									},
									"Cancelar": {
										icon: 'fa-close',
										type: 'danger',
										f: function(){
											K.closeWindow(p.$mod.attr('id'));
										}
									}
								},
								onClose: function(){ p.$mod = null; },
								onContentLoaded: function(){
									p.$mod = $('#modTitleFer');
									p.$mod.find('[name=nomb]').val(calEvent.title).focus();
								}
							});
					    },
						dayClick: function( date, allDay, jsEvent, view ){
							if(p.$w.find('[name='+date.format()+']').length>0){
								return K.msg({title: 'D&iacute;a ya seleccionado',text: 'Ya existe feriado en el d&iacute;a seleccionado!',type: 'error'});
							}else if(ciHelper.date.compare(date._d,new Date(p.date.getFullYear(),0,1,0,0,0))==-1){
								return K.msg({title: 'Fecha fuera de periodo v&aacute;lido',text: 'El feriado debe estar programado para el '+p.date.getFullYear(),type: 'info'});
							}else if(ciHelper.date.compare(new Date(p.date.getFullYear(),11,31,23,59,59),date._d)==-1){
								return K.msg({title: 'Fecha fuera de periodo v&aacute;lido',text: 'El feriado debe estar programado para el '+p.date.getFullYear(),type: 'info'});
							}
							new K.Modal({
								id: 'modTitleFer',
								title: 'Descripci&oacute;n de feriado',
								icon: 'ui-icon-calendar',
								content: '<div class="form-group">'+
									'<label class="col-sm-4 control-label">Nombre</label>'+
									'<div class="col-sm-8 input-group">'+
										'<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>'+
										'<input type="text" class="form-control" name="nomb">'+
									'</div>'+
								'</div>',
								height: 65,
								width: 400,
								buttons: {
									"Crear Feriado": {
										icon: 'fa-save',
										type: 'success',
										f: function(){
											p.$cal.fullCalendar('renderEvent',{
												title: p.$mod.find('[name=nomb]').val(),
												start: date,
												//end: date_end,
												allDay: true
											},true);
											var $row = $('<tr class="item" name="'+ciHelper.date.format.ymd(date._d)+'">');
											$row.append('<td>'+ciHelper.date.format.ymd(date._d)+' - <i>'+p.$mod.find('[name=nomb]').val()+'</i></td>');
											p.$w.find('[name=grid] tbody').append( $row );
											p.save();
											K.closeWindow(p.$mod.attr('id'));
										}
									},
									"Cancelar": {
										icon: 'fa-close',
										type: 'danger',
										f: function(){
											K.closeWindow(p.$mod.attr('id'));
										}
									}
								},
								onClose: function(){ p.$mod = null; },
								onContentLoaded: function(){
									p.$mod = $('#modTitleFer');
									p.$mod.find('[name=nomb]').focus();
								}
							});
						},
						firstDay: 1
					});
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	function(){
		return peFeri;
	}
);