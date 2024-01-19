/*******************************************************************************
Cuentas por Pagar : Todos */
tsCtppAll= {
	states:["P","C","X"],
	init: function(){
		if($('#pageWrapper [child=ctpp]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/ctpp',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="ctpp" />');
					$p.find("[name=tsCtpp]").after( $row.children() );
				}
				$p.find('[name=tsCtpp]').data('ctpp',$('#pageWrapper [child=ctpp]:first').data('ctpp'));
				$p.find('[name=tsCtppPen]').click(function(){ tsCtppPen.init(); });
				$p.find('[name=tsCtppAll]').click(function(){ tsCtppAll.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsCtppAll',
			titleBar: {
				title: 'Todas las Cuentas por pagar'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/ctpp/index_all',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el motivo de cuenta por pagar' ).width('250');
				$mainPanel.find('[name=obj]').html( 'cuenta(s) por pagar' );
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
						tsCtppAll.loadData({page: 1,url: 'ts/ctpp/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsCtppAll.loadData({page: 1,url: 'ts/ctpp/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsCtppAll.loadData({page: 1,url: 'ts/ctpp/lista'});
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
					$li.eq(0).css('background',tsConc.states[result.estado].color).addClass('vtip').attr('title',tsConc.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					if(result.beneficiario)$li.eq(2).html( ciHelper.enti.formatName(result.beneficiario) );
					else $li.eq(2).html("--");$li.eq(3).html( result.motivo );
					if(result.afectacion){
						if(result.afectacion.length>1){
							$li.eq(4).html("Varios");
						}else{
							if(result.afectacion[0].organizacion == null)
								$li.eq(4).html("---");
							else{
								$li.eq(4).html(result.afectacion[0].organizacion.nomb);
							}
						}
					}else{
						$li.eq(4).html("--");
					}
					$li.eq(5).html( ciHelper.formatMon(result.total_pago) );
					$li.eq(6).html( ciHelper.formatMon(result.total_desc) );
					$li.eq(7).html( ciHelper.formatMon(result.total) );
					$li.eq(8).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenTsCtpp", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(excep+',#conMenSpOrd_about,#conMenTsCtpp_anu',menu).remove();
								return menu;
							},
							bindings: {
								'conMenTsCtpp_ver': function(t) {
									tsCtppPen.windowDetails({id: K.tmp.data('id')});
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
						tsCtppAll.loadData(params);
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
tsConc.states["C"] = {
		descr: "Cancelada",
		color: "green"
};
tsConc.states["P"] = {
		descr: "Pendiente",
		color: "gray"
};
tsConc.states["X"] = {
		descr: "Anulada",
		color: "black"
};