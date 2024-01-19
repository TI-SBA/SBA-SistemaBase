cmComp = {
	states: [ "C", "P", "A", "R" ],
	windowDetailsComp: function(p){
		new K.Window({
			id: 'windowDetailsComp'+p.id,
			title: 'Comprobante '+p.nomb,
			store: false,
			resizable: false,
			maximizable: false,
			contentURL: 'cm/comp/details',
			width: 660,
			height: 500,
			onContentLoaded: function(){
				p.$w = $('#windowDetailsComp'+p.id);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				});
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$.post('cm/comp/get','id='+p.id,function(data){
					p.data = data;
					p.$w.find('[name=serie]').html( data.serie );
					p.$w.find('[name=numero]').html( data.numero );
					if(data.entidad.tipo_enti=='E') p.$w.find('[name=entidad]').html( data.entidad.nomb );
					else p.$w.find('[name=entidad]').html( data.entidad.nomb+' '+data.entidad.appat+' '+data.entidad.apmat );
					p.$w.find('[name=entidad]').wrap('<a>');
					p.$w.find('[name=entidad]').closest('a').click(function(){
						ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
					}).data('id',p.data.entidad._id.$id).data('tipo_enti',p.data.entidad.tipo_enti).css('text-decoration','underline');
					for(var i=0; i<data.servicios.length; i++){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$li = $('li',$row);
						$li.eq(0).html( i+1 );
						$li.eq(1).html( data.servicios[i].descr );
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(1)").append( $row.children() );
					}
					p.$w.find('.grid:eq(1)').css('overflow','hidden');
					p.$w.find('.grid:eq(2)').scroll(function(){
						p.$w.find('.grid:eq(2)').scrollLeft($(this).scrollLeft());
					});
					cmOper.contextMenuOper({$this: p.$w.find('[name=btnOper]'),data: p.data});
					p.$w.find('[name=btnOper]').button({icons: {primary: 'ui-icon-plusthick'}});
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
cmComp.states["C"] = {
	descr: "Concluido",
	color: "#003265"
};
cmComp.states["P"] = {
	descr: "Pendiente",
	color: "#CCCCCC"
};
cmComp.states["A"] = {
	descr: "Aceptado",
	color: "#006532"
};
cmComp.states["R"] = {
	descr: "Rechazado",
	color: "#CC0000"
};