/*******************************************************************************
polizas por dependencia */
tsReinPor = {
	init: function(){
		if($('#pageWrapper [child=rein]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/rein',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="rein" />');
					$p.find("[name=tsRein]").after( $row.children() );
				}
				$p.find('[name=tsRein]').data('rein',$('#pageWrapper [child=rein]:first').data('rein'));
				$p.find('[name=tsReinPor]').click(function(){ tsReinPor.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsReinTod]').click(function(){ tsReinTod.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsReinPor',
			titleBar: {
				title: 'Recibo de Ingresos: Por Dependencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/rein',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo de recibo' ).width('250');
				$mainPanel.find('[name=obj]').html( 'recibo(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsReinPor.loadData({page: 1,url: 'ts/rein/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsReinPor.loadData({page: 1,url: 'ts/rein/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=orga]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				tsReinPor.loadData({page: 1,url: 'ts/rein/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			por: true,
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tsRein.states[result.estado].color).addClass('vtip').attr('title',tsRein.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( 'Recibo de Ingreso N&deg;'+result.cod );
					$li.eq(3).html( result.organizacion.nomb );
					$li.eq(4).html( ciHelper.formatMon(result.total) );
					$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						tsRein.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).contextMenu("conMenTsRein", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							$('#conMenTsRein_cta,#conMenTsRein_mov,#conMenTsRein_rec',menu).remove();
							return menu;
						},
						bindings: {
							'conMenTsRein_ver': function(t) {
								tsRein.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
							},
							'conMenTsRein_anu': function(t) {
								K.sendingInfo();
								$.post('ts/rein/anular',{_id: K.tmp.data('id'),estado: 'X'},function(){
									K.clearNoti();
									K.notification({title: 'Recibo de Ingresos anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
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
						tsReinPor.loadData(params);
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