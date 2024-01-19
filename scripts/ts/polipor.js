/*******************************************************************************
polizas por dependencia */
tsPoliPor = {
	init: function(){
		if($('#pageWrapper [child=poli]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/poli',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="poli" />');
					$p.find("[name=tsPoli]").after( $row.children() );
				}
				$p.find('[name=tsPoli]').data('poli',$('#pageWrapper [child=poli]:first').data('poli'));
				$p.find('[name=tsPoliPor]').click(function(){ tsPoliPor.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsPoliTod]').click(function(){ tsPoliTod.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsPoliPor',
			titleBar: {
				title: 'P&oacute;lizas Contables: Por Dependencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/poli',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo de poliza' ).width('250');
				$mainPanel.find('[name=obj]').html( 'poliza(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsPoli.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsPoliPor.loadData({page: 1,url: 'ts/poli/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsPoliPor.loadData({page: 1,url: 'ts/poli/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=orga]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				tsPoliPor.loadData({page: 1,url: 'ts/poli/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    /***************************************************************************************
	    params.orga = K.session.enti.roles.trabajador.organizacion._id.$id;
	    ***************************************************************************************/
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tsPoli.states[result.estado].color).addClass('vtip').attr('title',tsPoli.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.cod );
					$li.eq(3).html( result.descr.substring(0,150)+'...' );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						tsPoli.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).data('estado',result.estado).data('cta',{
						fec: result.fecreg,
						cod: result.cod
					}).contextMenu("conMenTsPoli", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							if(K.tmp.data('estado')!='R') $('#conMenTsPoli_cta',menu).remove();
							if(K.tmp.data('estado')=='X') $('#conMenTsPoli_edi,#conMenTsPoli_anu',menu).remove();
							return menu;
						},
						bindings: {
							'conMenTsPoli_ver': function(t) {
								tsPoli.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenTsPoli_edi': function(t) {
								tsPoli.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenTsPoli_cta': function(t) {
								tsPoli.windowCta({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(2)').html(),
									cod: K.tmp.data('cta').cod,
									fec: K.tmp.data('cta').fec
								});
							},
							'conMenTsPoli_anu': function(t) {
								K.sendingInfo();
								$.post('ts/poli/save',{_id: K.tmp.data('id'),estado: 'X'},function(){
									K.clearNoti();
									K.notification({title: 'P&oacute;liza Anulada',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						tsPoliPor.loadData(params);
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