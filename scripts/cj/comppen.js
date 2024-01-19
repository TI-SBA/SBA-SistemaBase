/*******************************************************************************
comprobantes pendientes */
cjCompPen = {
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjCompPen',
			titleBar: {
				title: 'Comprobantes: Pendientes de Cambio de Nombre'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/comp',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de comprobante' ).width('250');
				$mainPanel.find('[name=obj]').html( 'comprobante(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').outerHeight()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnGen]').remove();
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjCompPen.loadData({page: 1,url: 'cj/comp/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjCompPen.loadData({page: 1,url: 'cj/comp/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				cjCompPen.loadData({page: 1,url: 'cj/comp/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    params.estado = 'P';
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',cjComp.states[result.estado].color).addClass('vtip').attr('title',cjComp.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( cjComp.tipo[result.tipo]+' '+result.serie+'-'+result.num );
					$li.eq(3).html( ciHelper.enti.formatName(result.cliente) );
					$li.eq(4).html( ciHelper.formatMon(result.total,result.moneda) );
					$li.eq(5).html( ciHelper.enti.formatName(result.autor) );
					$li.eq(6).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						cjCompPen.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).contextMenu("conMenCjComp", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							$('#conMenCjComp_imp,#conMenCjComp_anu',menu).remove();
							return menu;
						},
						bindings: {
							'conMenCjComp_cam': function(t) {
								cjComp.windowConfirmar({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
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
						cjCompPen.loadData(params);
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
	}
};