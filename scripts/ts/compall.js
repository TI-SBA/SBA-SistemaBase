/*******************************************************************************
Comprobantes */
tsCompAll = {
	states : {
		"R":{
			color:"gray",
			descr:"Registrado"
		},
		"C":{
			color:"green",
			descr:"Cancelado"
		},
		"X":{
			color:"black",
			descr:"Anulado"
		}
	},
	init: function(){
		if($('#pageWrapper [child=comp]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/comp',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="comp" />');
					$p.find("[name=tsComp]").after( $row.children() );
				}
				$p.find('[name=tsComp]').data('comp',$('#pageWrapper [child=comp]:first').data('comp'));
				$p.find('[name=tsCompNue]').click(function(){ tsCompNue.init(); });
				$p.find('[name=tsCompAll]').click(function(){ tsCompAll.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsCompAll',
			titleBar: {
				title: 'Todos los Comprobantes de Pago'
			}
		});		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/comp/index_all',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de un comprobante' ).width('250');
				$mainPanel.find('[name=obj]').html( 'comprobante(s)' );
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
						tsCompAll.loadData({page: 1,url: 'ts/comp/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsCompAll.loadData({page: 1,url: 'ts/comp/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsCompAll.loadData({page: 1,url: 'ts/comp/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.estado = "";
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);	
					$li.eq(0).css('background',tsCompAll.states[result.estado].color).addClass('vtip').attr('title',tsCompAll.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( "Comprobante de Pago "+ciHelper.codigos(result.cod,6) );
					$li.eq(3).html( result.nomb );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('estado',result.estado)
					.contextMenu("conMenTsComp", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(excep+',#conMenSpOrd_about,#conMenTsComp_edi,#conMenTsComp_pag,#conMenTsComp_anu',menu).remove();
								return menu;
							},
							bindings: {
								'conMenTsComp_ver': function(t) {
									tsCompNue.windowDetails({id: K.tmp.data('id')});
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
						tsCompAll.loadData(params);
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