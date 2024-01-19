/*************************************************************************
  Contingencias : A Favor */
alContCont = {
	init: function(){
		if($('#pageWrapper [child=alcont]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('al/navg/cont',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="alcont" />');
					$p.find("[name=alCont]").after( $row.children() );
				}
				$p.find('[name=alCont]').data('alcont',$('#pageWrapper [child=alexpd]:first').data('alcont'));
				$p.find('[name=alContFav]').click(function(){ alContFav.init(); });
				$p.find('[name=alContCont]').click(function(){ alContCont.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'al',
			action: 'alContCont',
			titleBar: {
				title: 'Contigencias en Contra'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/cont/index_cont',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el numero de la contingencia' ).width('250');
				$mainPanel.find('[name=obj]').html( 'contigencia(s) en contra' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					alContFav.windowNew({
						clasif:"C"
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=tipo]').change(function(){
					$("#mainPanel .gridBody").empty();
					alContCont.loadData({page: 1,url: 'al/cont/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alContCont.loadData({page: 1,url: 'al/cont/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alContCont.loadData({page: 1,url: 'al/cont/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alContCont.loadData({page: 1,url: 'al/cont/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.clasificacion = 'C';
		params.tipo = $('#mainPanel').find('[name=tipo] :selected').val();
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( result.numero );
					$li.eq(2).html( result.demandante );
					$li.eq(3).html( result.demandado );	
					$li.eq(4).html( result.materia);
					$li.eq(5).html( result.monto.soles);
					$li.eq(6).html( result.monto.dolares);
					$li.eq(7).html( result.costo);
					$li.eq(8).html( result.fecha);
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenAlContFav", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								//$(menu).remove();
								return menu;
							},
							bindings: {
								'conMenAlContFav_edi': function(t) {
									alContFav.windowEdit({id: K.tmp.data('id'), num : K.tmp.find('li:eq(1)').html(), clasif: K.tmp.data('data').clasificacion});
								},
								'conMenAlContFav_eli': function(t) {
									var data = {
											id: K.tmp.data('id')
										};
										K.sendingInfo();
										$.post('al/cont/delete',data,function(){
											K.clearNoti();
											K.notification({title: 'Contingencia eliminada',text: 'La Contingencia seleccionada ha sido eliminada con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenAlContFav_ver': function(t) {
									alContFav.windowDetails({id: K.tmp.data('id')});
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
						alContCont.loadData(params);
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