tdExpdGest = {
	init: function(){
		if($('#pageWrapper [child=exps]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('td/navg/exps',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="exps" />');
					$p.find("[name=tdExp]").after( $row.children() );
				}
				$p.find('[name=tdExp]').data('exps',$('#pageWrapper [child=exps]:first').data('exps'));
				$p.find('[name=tdExpdReci]').click(function(){ tdExpdReci.init(); });
				$p.find('[name=tdExpdVenc]').click(function(){ tdExpdVenc.init(); });
				$p.find('[name=tdExpdArch]').click(function(){ tdExpdArch.init(); });
				$p.find('[name=tdExpdPor]').click(function(){ tdExpdPor.init(); });
				$p.find('[name=tdExpdCopi]').click(function(){ tdExpdCopi.init(); });
				$p.find('[name=tdExpdGest]').click(function(){ tdExpdGest.init(); }).find('ul').addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'td',
			action: 'tdExpdGest',
			titleBar: { title: 'Expedientes por Gestor'}
		});
		K.unblock({$element: $('#pageWrapperMain')});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'td/expd',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'documentos' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tdExpd.windowNewExp();
				}).button({icons: {primary: 'ui-icon-folder-collapsed'}});
				$('.divSearch [name=buscar]').attr('placeholder',' Buscar Gestor').width('350px').attr('readonly','readonly');
				$('.divSearch [name=btnBuscar]').click(function(){
					ciSearch.windowSearchEnti({callback: tdExpdGest.cbGestor});
				}).button({icons: {primary: 'ui-icon-search'}});
				K.block({$element: $mainPanel.find('[name=mainGrid]'),message: 'Debe seleccionar una entidad!'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.gestor = $('.divSearch [name=buscar]').data('data')._id.$id;
		params.page_rows = 20;
		params.page = (params.page) ? params.page : 1;
		$.post(params.url, params, function(data){
			if ( data.items != null ){
			if ( data.paging.total_page_items > 0 ) {
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tdExpd.states[result.estado].color).addClass('vtip').attr('title',tdExpd.states[result.estado].descr);
					$li.eq(1).html( '<button name="btnGrid">M&aacute;s Acciones</button>' );
					$li.eq(2).html( result.num );
					$li.eq(3).html(result.gestor.nomb );
					$li.eq(4).html( result.concepto );
					$li.eq(5).html( result.observ_expd );
					$li.eq(6).html( ciHelper.dateFormat(result.fecreg) );
					$li.eq(7).html( ciHelper.dateFormat(result.fecven) );
					$row.wrapInner('<a id="'+result._id.$id+'" class="item"  href="javascript: void(0);" />');
					$row.find('a').data('data',result).dblclick(function(){
						tdExpd.windowDetailsExpd({id: $(this).data('data')._id.$id, data: $(this).data('data')});
					});
					tdExpd.contextMenu({$row: $row.find('a'),data: $row.find('a').data('data')});
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
						tdExpdReci.loadData(params);
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
		}else{
			$('#No-Results').show();
			$('#Results').hide();
			$("[name=moreresults]").button( "option", "disabled", true );
		}
		$('#mainPanel').resize();
		K.unblock({$element: $('#mainPanel [name=mainGrid]')});
		}, 'json');
	},
	cbGestor: function(data){
		$('#mainPanel .gridBody').empty();
		$('#mainPanel .divSearch [name=buscar]').data('data',data);
		if(data.tipo_enti=='P') $('#mainPanel .divSearch [name=buscar]').val(data.nomb + ' ' + data.appat + ' ' + data.apmat);
		else $('#mainPanel .divSearch [name=buscar]').val(data.nomb);
		tdExpdGest.loadData({page: 1,url: 'td/expd/listaexpdgest'});
	}
};
define(
	['td/expd'],
	function(tdExpd){
		return tdExpdGest;
	}
);