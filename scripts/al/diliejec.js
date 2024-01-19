/*************************************************************************
  Diligencias : Ejecutadas */
alDiliEjec = {
	init: function(){
		if($('#pageWrapper [child=aldili]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('al/navg/dili',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="aldili" />');
					$p.find("[name=alDili]").after( $row.children() );
				}
				$p.find('[name=alDili]').data('aldili',$('#pageWrapper [child=aldili]:first').data('aldili'));
				$p.find('[name=alDiliProg]').click(function(){ alDiliProg.init(); });
				$p.find('[name=alDiliEjec]').click(function(){ alDiliEjec.init(); }).addClass('ui-state-highlight');
				$p.find('[name=alDiliSusp]').click(function(){ alDiliSusp.init(); });
			},'json');
		}
		K.initMode({
			mode: 'al',
			action: 'alDiliEjec',
			titleBar: {
				title: 'Diligencias Ejecutadas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/dili',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el numero del expediente asociado' ).width('250');
				$mainPanel.find('[name=obj]').html( 'diligencias(s) ejecutada(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					alDiliProg.windowNew({
						clasif:"C"
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=FilTipo]').buttonset();
				$mainPanel.find('#rbtnSelectJ').click(function(){
					$("#mainPanel .gridBody").empty();
					alDiliEjec.loadData({page: 1,url: 'al/dili/lista'});
				});
				$mainPanel.find('#rbtnSelectE').click(function(){
					$("#mainPanel .gridBody").empty();
					alDiliEjec.loadData({page: 1,url: 'al/dili/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alDiliEjec.loadData({page: 1,url: 'al/dili/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alDiliEjec.loadData({page: 1,url: 'al/dili/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alDiliEjec.loadData({page: 1,url: 'al/dili/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.estado = 'E';
		params.tipo = $('#mainPanel').find('[name=FilTipo] :checked').val();
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
					$li.eq(1).html( result.expediente.numero );
					$li.eq(2).html( result.asunto );
					$li.eq(3).html( ciHelper.dateFormat(result.fecha));
					$li.eq(4).html( result.lugar );
					$li.eq(5).html( result.observ );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('expdid',result.expediente._id.$id)
					.contextMenu("conMenAlDili", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(menu).remove();
								return menu;
							},
							bindings: {
								'conMenAlDili_edi': function(t) {
									alDiliProg.windowEdit({id: K.tmp.data('id'), num : K.tmp.find('li:eq(1)').html()});
								},
								'conMenAlDili_susp': function(t) {
									alDiliProg.windowSuspender({id: K.tmp.data('id')});
								},
								'conMenAlDili_verExpd': function(t) {
									alExpdActi.windowDetails({id: K.tmp.data('expdid'),numero : K.tmp.find('li:eq(1)').html()});
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
						alDiliEjec.loadData(params);
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