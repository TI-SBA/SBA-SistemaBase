/*******************************************************************************
Expedientes Recibidos */
tdExpdHistAll = {
	init: function(){
		if($('#pageWrapper [child=his]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('td/navg/his',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="his" />');
					$p.find("[name=tdHis]").after( $row.children() );
				}
				$p.find('[name=tdHis]').data('his',$('#pageWrapper [child=his]:first').data('his'));
				$p.find('[name=tdExpdHistAll]').click(function(){ tdExpdHistAll.init(); }).find('ul').addClass('ui-state-highlight');
				$p.find('[name=tdExpdHistRebi]').click(function(){ tdExpdHistRebi.init(); });
				$p.find('[name=tdExpdHistEnvi]').click(function(){ tdExpdHistEnvi.init(); });
				$p.find('[name=tdExpdHistVenc]').click(function(){ tdExpdHistVenc.init(); });
			},'json');
		}
		K.initMode({
			mode: 'td',
			action: 'tdExpdHistAll',
			titleBar: { title: 'Lista de Todos los Expedientes'}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'td/expd/his',
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
				$mainPanel.find('[name=search_rango]').show();
				$mainPanel.find('[name=btnRango]').click(function(){
					var params = {callback: function(ini,fin){
						$mainPanel.find('[name=rango]').data('data',{
							ini: ini,
							fin: fin
						}).html(ini+' <-> '+fin);
						$("#mainPanel .gridBody").empty();
						tdExpdHistAll.loadData({page: 1,url: 'td/expd/lista'});
					}};
					if($mainPanel.find('[name=rango]').data('data')!=null){
						params.ini = $mainPanel.find('[name=rango]').data('data').ini;
						params.fin = $mainPanel.find('[name=rango]').data('data').fin;
					}
					ciHelper.selectIniFin(params);
				}).button({icons: {primary: 'ui-icon-calendar'}});
				$mainPanel.find('[name=btnDel]').click(function(){
					$mainPanel.find('[name=rango]').html('').removeData();
				}).button({icons: {primary: 'ui-icon-trash'},text: false});
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
					tdExpdHistAll.loadData({page: 1,url: 'td/expd/lista',filter: $(this).attr("filter"),order: order});
				});
				$mainPanel.find('.gridHeader li:eq(1),.gridReference li:eq(1)').remove();
				$('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				}).attr('placeholder',' Buscar Expediente').width('250');
				$('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
			            $("#mainPanel .gridBody").empty();
			            tdExpdHistAll.loadData({page: 1,url: 'td/expd/lista'});
					}else{
			            $("#mainPanel .gridBody").empty();
			            tdExpdHistAll.loadData({page: 1,url: 'td/expd/lista',texto: $('.divSearch [name=buscar]').val(),ncol: col});
			            K.notification({text: 'B&uacute;squeda realizada!',layout:"topLeft"});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tdExpdHistAll.loadData({page: 1,url: 'td/expd/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		if($('.divSearch [name=buscar]').val()!='')
			params.bus = $('.divSearch [name=buscar]').val();
		params.nomb = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
		params.page = (params.page) ? params.page : 1;
		if($mainPanel.find('[name=rango]').data('data')!=null){
			params.ini = $mainPanel.find('[name=rango]').data('data').ini;
			params.fin = $mainPanel.find('[name=rango]').data('data').fin;
		}
		$.post(params.url, params, function(data){
			if ( data.items != null ){
			if ( data.paging.total_page_items > 0 ) {
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tdExpd.states[result.estado].color).addClass('vtip').attr('title',tdExpd.states[result.estado].descr);
					if(result.tupa!=null){
						if(result.estado=='P')
							$li.eq(0).html('Pendiente (TUPA)').css('color','red');
						if(result.estado=='C')
							$li.eq(0).html('Concluido (TUPA)').css('color','white');
					}
					$li.eq(1).html( result.num );
          $li.eq(2).html( mgEnti.formatName(result.gestor) );
					$li.eq(3).html( result.concepto );
					$li.eq(4).html( result.traslados[result.traslados.length-1].origen.organizacion.nomb );
					$li.eq(5).html( result.observ_expd );
					$li.eq(6).html( ciHelper.dateFormat(result.fecreg) );
					if(result.feccon != null)
						$li.eq(7).html( ciHelper.dateFormat(result.feccon) );
					/*if(ciHelper.dateDiffNow(result.fecven)<=tdExpd.days){
						$li.eq(5).css('color','red');
					}*/
					$row.wrapInner('<a id="'+result._id.$id+'" class="item"  href="javascript: void(0);" />');
					$row.find('a').data('data',result).dblclick(function(){
						tdExpd.windowDetailsExpd({id: $(this).data('data')._id.$id, data: $(this).data('data'),readOnly: true});
					}).contextMenu('conMenExpd', {
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
				}
				count = $("#mainPanel .gridBody .item").length;
				$('#No-Results').hide();
				$('#Results [name=showing]').html( count );
				$('#Results [name=founded]').html( data.paging.total_items );
				$('#Results').show();
				
				$moreresults = $("[name=moreresults]").unbind('click');
				if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						tdExpdHistAll.loadData(params);
						$(this).button( "option", "disabled", true );
					});
		        }else{
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
					$("#mainPanel .gridFoot").hide();
		        }
			} else {
				$('#No-Results').show();
				$('#Results').hide();
				$("[name=moreresults]").button().button( "option", "disabled", true );
			}
		} else {
			$('#No-Results').show();
			$('#Results').hide();
			$("[name=moreresults]").button().button( "option", "disabled", true );
		}
		$('#mainPanel').resize();
		K.unblock({$element: $('#pageWrapperMain')});
		}, 'json');
	},
	windowEdit: function(p){
		K.notification({text: "Funcion no implementada"});
	},
	windowDelete: function(p){
		K.notification({text: "Funcion no implementada"});
	}
};
define(
	['td/expd'],
	function(tdExpd){
		return tdExpdHistAll;
	}
);