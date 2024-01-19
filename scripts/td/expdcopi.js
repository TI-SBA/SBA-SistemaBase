/*******************************************************************************
Expedientes Recibidos */
tdExpdCopi = {
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
				$p.find('[name=tdExpdCopi]').click(function(){ tdExpdCopi.init(); }).find('ul').addClass('ui-state-highlight');
				$p.find('[name=tdExpdVenc]').click(function(){ tdExpdVenc.init(); });
				$p.find('[name=tdExpdArch]').click(function(){ tdExpdArch.init(); });
				$p.find('[name=tdExpdPor]').click(function(){ tdExpdPor.init(); });
				$p.find('[name=tdExpdReci]').click(function(){ tdExpdReci.init(); });
			},'json');
		}
		K.initMode({
			mode: 'td',
			action: 'tdExpdCopi',
			titleBar: { title: 'Copias de Expedientes'}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'td/expd/por',
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
				$li.eq(1).remove();
				$('#mainPanel .gridReference li:eq(1)').remove();
				var col = "concepto";
				$li.eq(2).attr('filter','num');
				$li.eq(3).attr('filter','gestor');
				$li.eq(4).attr('filter','concepto');
				$li.eq(6).attr('filter','fecmod');
				$("#mainPanel .gridHeader").find('li:eq(1),li:eq(2),li:eq(3)').click( function(){
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
					tdExpdCopi.loadData({page: 1,url: 'td/expd/listaexpdcopi',filter: $(this).attr("filter"),order: order});
				});
				
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tdExpd.windowNewExp();
				}).button({icons: {primary: 'ui-icon-folder-collapsed'}});
				if(K.session.tasks["td.expd.area"]=="0"){
					$mainPanel.find('[name=btnAgregar]').remove();
				}
				$('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				}).attr('placeholder',' Buscar Expediente').width('250');
				$('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
			            $("#mainPanel .gridBody").empty();
			            tdExpdCopi.loadData({page: 1,url: 'td/expd/listaexpdcopi'});
					}else{
			            $("#mainPanel .gridBody").empty();
			            tdExpdCopi.loadData({page: 1,url: 'td/expd/listaexpdcopi',bus: $('.divSearch [name=buscar]').val(), ncol: col});
			            K.notification({text: 'B&uacute;squeda realizada!',layout:"topLeft"});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tdExpdCopi.loadData({page: 1,url: 'td/expd/listaexpdcopi'});
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
					$li.eq(0).css('background',tdExpd.states[result.estado].color).addClass('vtip').attr('title',tdExpd.states[result.estado].descr);
					$li.eq(1).html( result.num );
					if(result.gestor.appat == null)
						$li.eq(2).html( result.gestor.nomb );
					else
						$li.eq(2).html( result.gestor.nomb + ' ' + result.gestor.appat + ' ' + result.gestor.apmat);
					$li.eq(3).html( result.concepto );
					$li.eq(4).html( result.origen.nomb);
					$li.eq(5).html( result.observ_expd );
					$li.eq(6).html( ciHelper.dateFormat(result.fecenv) );
					$row.wrapInner('<a id="'+result._id.$id+'" class="item"  href="javascript: void(0);" />');
					$row.find('a').data('data',result).dblclick(function(){
						tdExpd.windowDetailsExpd({id: $(this).data('data')._id.$id, data: $(this).data('data'),readOnly: true});
					}).data('readOnly',true).contextMenu('conMenExpd', {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							$(/*'#conMenExpd_doc,*/'#conMenExpd_ret,#conMenExpd_env,#conMenExpd_acp,#conMenExpd_rec,#conMenExpd_acf,#conMenExpd_con,#conMenExpd_ach,#conMenExpd_rcd,#conMenExpd_ape,#conMenExpd_imp,#conMenExpd_ext', menu).remove();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenExpd_doc': function(t) {
								tdExpd.windowNewDoc({
									data: K.tmp.data('data'),
									callBack: tdExpd.saveDoc,
									num: K.tmp.data('data').num,
									concepto: K.tmp.data('data').concepto
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
				
				$moreresults = $("[name=moreresults]").unbind('click');
				if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						tdExpdCopi.loadData(params);
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
		K.unblock({$element: $('#pageWrapperMain')});
		}, 'json');
	}
};
define(
	['td/expd'],
	function(tdExpd){
		return tdExpdCopi;
	}
);