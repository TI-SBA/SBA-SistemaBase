/*******************************************************************************
Comprobantes pendientes */
cmCompAll = {
	init: function(){
		if($('#pageWrapper [child=comp]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('cm/navg/comp',function(data){
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
					$p.find("[name=cmComp]").after( $row.children() );
				}
				$p.find('[name=cmComp]').data('comp',$('#pageWrapper [child=comp]:first').data('comp'));
				$p.find('[name=cmCompAll]').click(function(){ cmCompAll.init(); }).addClass('ui-state-highlight');
				$p.find('[name=cmCompPen]').click(function(){ cmCompPen.init(); });
			},'json');
		}
		K.initMode({
			mode: 'cm',
			action: 'cmCompAll',
			titleBar: { title: 'Comprobantes'}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cm/comp',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'expedientes' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				var $li = $("#mainPanel .gridHeader li");
				var col = "concepto";
				$li.eq(2).attr('filter','num');
				$li.eq(3).attr('filter','gestor');
				$li.eq(4).attr('filter','concepto');
				$li.eq(5).attr('filter','ubicacion.nomb');
				$li.eq(6).attr('filter','fecreg');
				//$li.eq(7).attr('filter','feccon');
				$("#mainPanel .gridHeader").find('li:eq(2),li:eq(3),li:eq(4), li:eq(5), li:eq(6)').click( function(){
					$this = $(this);
					var order = 1;
					col = $(this).attr("filter");
					if($this.find('.ui-sorter').length>0){
						if($this.find('.ui-asc').length>0){
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
						}else{
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-n ui-asc" style="float:right"></span>');
							order = -1;
						}
					}else{
						$this.closest('.gridHeader').find('.ui-sorter').remove();
						$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
					}
					$("#mainPanel .gridBody").empty();
					tdExpdHistAll.loadData({page: 1,url: 'cm/comp/lista',filter: $(this).attr("filter"),order: order});
				});
				$('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				}).attr('placeholder',' Buscar Expediente').width('250');
				$('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
			            $("#mainPanel .gridBody").empty();
			            cmCompAll.loadData({page: 1,url: 'cm/comp/lista_all'});
					}else{
			            $("#mainPanel .gridBody").empty();
			            cmCompAll.loadData({page: 1,url: 'cm/comp/lista',bus: $('.divSearch [name=buscar]').val(),ncol: col});
			            K.notification({text: 'B&uacute;squeda realizada!',layout:"topLeft"});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				cmCompAll.loadData({page: 1,url: 'cm/comp/lista_all'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.nomb = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
		params.page = (params.page) ? params.page : 1;
		$.post(params.url, params, function(data){
			if ( data.items != null ){
			if ( data.paging.total_page_items > 0 ) {
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',cmComp.states[result.estado].color).addClass('vtip').attr('title',cmComp.states[result.estado].descr);
					$li.eq(1).html( '<button name="btnGrid">M&aacute;s Acciones</button>' );
					$li.eq(2).html( result.serie+'-'+result.num );
					$li.eq(3).html( result.emitido );
					if(result.entidad.appat == null)
						$li.eq(4).html( result.entidad.nomb );
					else
						$li.eq(4).html( result.entidad.nomb + ' ' + result.entidad.appat + ' ' + result.entidad.apmat);
					$li.eq(5).html( result.caja );
					$li.eq(6).html( result.cajero );
					$row.wrapInner('<a id="'+result._id.$id+'" class="item"  href="javascript: void(0);" />');
					$row.find('a').data('data',result).dblclick(function(){
						cmComp.windowDetailsComp({id: $(this).data('data')._id.$id, nomb: $(this).closest('.item').find('li:eq(2)').html()});
					}).contextMenu('conMenExpd', {
						onShowMenu: function(e, menu) {
						$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
						$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
						$(e.target).closest('.item').click();
						K.tmp = $(e.target).closest('.item');
						return menu;
					},
					bindings: {
						'conMenExpd_doc': function(t) {
							var params = new Object;
							params.data = K.tmp.data('data');
							params.callBack = tdExpd.saveDoc;
							params.num = K.tmp.data('data').num;
							params.concepto = K.tmp.data('data').concepto;
							tdExpd.windowNewDoc(params);
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
				
				$moreresults = $("[name=moreresults]").unbind('click');
				if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						cmCompAll.loadData(params);
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
				$("[name=moreresults]").button( "option", "disabled", true );
			}
		} else {
			$('#No-Results').show();
			$('#Results').hide();
			$("[name=moreresults]").button( "option", "disabled", true );
		}
		$('#mainPanel').resize();
		K.unblock({$element: $('#pageWrapperMain')});
		}, 'json');
	}
};