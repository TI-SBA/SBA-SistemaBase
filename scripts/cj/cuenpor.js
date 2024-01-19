/*******************************************************************************
cuentas por dependencia */
cjCuenPor = {
	init: function(){
		if($('#pageWrapper [child=cuen]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('cj/navg/cuen',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="cuen" />');
					$p.find("[name=cjCuen]").after( $row.children() );
				}
				$p.find('[name=cjCuen]').data('cuen',$('#pageWrapper [child=cuen]:first').data('cuen'));
				$p.find('[name=cjCuenPor]').click(function(){ cjCuenPor.init(); }).find('ul').addClass('ui-state-highlight');
				$p.find('[name=cjCuenTod]').click(function(){ cjCuenTod.init(); });
			},'json');
		}
		K.initMode({
			mode: 'cj',
			action: 'cjCuenPor',
			titleBar: {
				title: 'Cuentas por cobrar: Por dependencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/cuen',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de caja' ).width('250');
				$mainPanel.find('[name=obj]').html( 'caja(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					cjCuen.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjCuenPor.loadData({page: 1,url: 'cj/cuen/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjCuenPor.loadData({page: 1,url: 'cj/cuen/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=estado]').change(function(){
					$("#mainPanel .gridBody").empty();
					cjCuenPor.loadData({page: 1,url: 'cj/cuen/lista'});
				});
				$mainPanel.find('[name=orga]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				cjCuenPor.loadData({page: 1,url: 'cj/cuen/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1,
		    estado: $mainPanel.find('[name=estado] option:selected').val(),
		    orga: K.session.enti.roles.trabajador.organizacion._id.$id
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',cjCuen.states[result.estado].color).addClass('vtip').attr('title',cjCuen.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.servicio.nomb );
					$li.eq(3).html( result.servicio.organizacion.nomb );
					$li.eq(4).html( ciHelper.enti.formatName(result.cliente) );
					$li.eq(5).html( ciHelper.formatMon(result.total,result.moneda) );
					$li.eq(6).html( ciHelper.enti.formatName(result.autor) );
					$li.eq(7).html( ciHelper.dateFormat(result.fecreg) );
					$li.eq(8).html( ciHelper.dateFormat(result.fecven) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						cjCuen.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).data('data',result).contextMenu("conMenCjCuen", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').operacion==null) $('#conMenCjCuen_ori',menu).remove();
								if(K.tmp.data('estado')=='C'||K.tmp.data('estado')=='X') $('#conMenCjCuen_anu',menu).remove();
								return menu;
							},
							bindings: {
								'conMenCjCuen_ver': function(t) {
									cjCuen.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenCjCuen_ori': function(t) {
									var data = K.tmp.data('data');
									if(data.modulo=='IN'){
										inArre.windowDetails({id: data.operacion.$id});
									}else if(data.modulo=='CM'){
										$.post('cm/oper/get',{id: data.operacion.$id},function(oper){
											cmOper.showDetails({data: oper});
										},'json');
									}
								},
								'conMenCjCuen_anu': function(t) {
									K.sendingInfo();
									$.post('cj/cuen/save',{_id: K.tmp.data('id'),estado: 'X'},function(){
										K.clearNoti();
										K.notification({title: 'Cuenta Anulada',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						cjCuenPor.loadData(params);
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