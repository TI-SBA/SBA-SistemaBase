acNoti = {
	init: function(p){
		if(p==null) p = {};
		$('#iconNoty').click(function(){
			p = {};
			$('#iconNoty label').html(0);
			new K.Modal({
				id: 'windowSelect',
				width: 510,
				height: 350,
				title: 'Ver Notificaciones',
				buttons: {
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					p.$grid = new K.grid({
						$el: p.$w,
						cols: ['Mensaje','Hace'],
						data: 'ac/noti/lista',
						params: {},
						itemdescr: 'notificacion(es)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td>'+data.message+'</td>');
							var secs = ciHelper.dateDiffNowSec(data.fecreg);
							style = "";
							if(secs<60){
								$row.append('<td><label'+style+'>Hace '+secs+' segundos</label></td>');
							}else if((secs/60)<60){
								$row.append('<td><label'+style+'>Hace '+parseInt(secs/60)+' minutos</label></td>');
							}else if((secs/60/60)<24){
								$row.append('<td><label'+style+'>Hace '+parseInt(secs/60/60)+' horas</label></td>');
							}else{
								$row.append('<td><label'+style+'>Hace '+parseInt(secs/60/60/24)+' d&iacute;as</label></td>');
							}
							$row.click(function(){
								eval($(this).data('data').action);
								K.closeWindow(p.$w.attr('id'));
							}).data('data',data);
							if(data.readed==false)
								$row.addClass('ui-state-highlight');
							return $row;
						}
					});
				}
			});
		});
		//acNoti.get();
	},
	get: function(){
		$.ajax({
			url: "ac/noti/last",
			success: acNoti.getComplete,
			dataType: 'json',
			error: function(){
				K.block({onUnblock: function(){
					K.clearNoti();
					K.notification({title: 'Servidor conectado!',icon: 'ui-icon-check',type: 'info'});
				}});
				K.clearNoti();
				K.notification({title: 'Error de conexi&oacute;n',text: 'El servidor del sistema no responde!',type: 'error'});
				var notice = $.pnotify({
			        title: "Por favor espere",
			        type: 'info',
			        icon: 'ui-icon-clock',
			        hide: false,
			        closer: false,
			        sticker: false,
			        opacity: .75,
			        shadow: false,
			        width: "150px"
			    });
			    setTimeout("acNoti.get();",5000);
			}
		});
	},
	getComplete: function(response){
		K.unblock();
		if(response!=null) acNoti.show(response);
		setTimeout("acNoti.get();",12000);
	},
	show: function(data){
		$('#iconNoty label').html(data.count);
		K.notification({text: 'Tiene '+data.count+' notificaciones pendientes!',type: 'info'});
	},
	loadData: function(p){
		$('#iconNoty label').html(0);
		p.$f = $('#filNotif');
	    $.post('ac/noti/lista', {
	    	page_rows: 20,
	    	page: (p.page) ? p.page : 1
	    }, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = p.$f.find('.gridReference').clone();
					$li = $('li',$row);
					$li.eq(0).html( '<span class="ui-icon '+result.icon+'" style="position: relative;"></span>'+result.message+'<br />' );
					var secs = ciHelper.dateDiffNowSec(result.fecreg);
					var style = ' style="float: right;font-size: 10px;color: gray;padding-right: 3px;"';
					if(secs<60){
						$li.eq(0).append('<label'+style+'>Hace '+secs+' segundos</label>');
					}else if((secs/60)<60){
						$li.eq(0).append('<label'+style+'>Hace '+parseInt(secs/60)+' minutos</label>');
					}else if((secs/60/60)<24){
						$li.eq(0).append('<label'+style+'>Hace '+parseInt(secs/60/60)+' horas</label>');
					}else{
						$li.eq(0).append('<label'+style+'>Hace '+parseInt(secs/60/60/24)+' d&iacute;as</label>');
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').click(function(){
						eval($(this).data('data').action);
						p.$f.remove();
					}).data('data',result);
					if(result.readed==false) $row.find('a').addClass('ui-state-highlight');
					p.$f.find(".gridBody").append( $row.children() );
		        }
		        count = p.$f.find(".gridBody .item").length;
		        
		        $moreresults = p.$f.find("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$moreresults.click( function(){
						p.$f.find('.grid').scrollTo( p.$f.find(".gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						acNoti.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					p.$f.find( "[name=moreresults]").button( "option", "disabled", false );
		        }else{
					p.$f.find( "[name=moreresults]").button( "option", "disabled", true );
		        }
	      } else {
	    	  p.$f.find( "[name=moreresults]").button( "option", "disabled", true );
	      }
	      K.unblock({$element: $('#filNotif .gridBody')});
	    }, 'json');
	}
};
acNoti.init();