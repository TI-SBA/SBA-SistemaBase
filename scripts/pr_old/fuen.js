/*******************************************************************************
Fuentes de Financiamiento */
prFuen = {
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prFuen',
			titleBar: {
				title: 'Fuentes de Financiamiento'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/fuen',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de clasificador' ).width('250');
				$mainPanel.find('[name=obj]').html( 'fuente(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prFuen.windowNewFuen();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prFuen.loadData({page: 1,url: 'pr/fuen/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						prFuen.loadData({page: 1,url: 'pr/fuen/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prFuen.loadData({page: 1,url: 'pr/fuen/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					var estado = "H";
					if(result.estado)estado=result.estado;
					$li.eq(0).css('background', prClas.states[estado].color).addClass('vtip').attr('title',prClas.states[estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.cod );
					$li.eq(3).html( result.rubro );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('estado',estado)
					.contextMenu("conMenPrFuen", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('estado')=="H") excep+='#conMenPrFuen_habFuen';
								else excep+='#conMenPrFuen_desFuen,#conMenPrFuen_ediFuen';
								$(excep+',#conMenSpOrd_about',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPrFuen_ediFuen': function(t) {
									prFuen.windowEditFuen({id: K.tmp.data('id')});
								},
								'conMenPrFuen_habFuen': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado: "H"
										};
										K.sendingInfo();
										$.post('pr/fuen/savefuen',data,function(){
											K.clearNoti();
											K.notification({title: 'Fuente Habilitada',text: 'La Fuente de Financiamiento seleccionada ha sido habilitada con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenPrFuen_desFuen': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado: "D"
										};
										K.sendingInfo();
										$.post('pr/fuen/savefuen',data,function(){
											K.clearNoti();
											K.notification({title: 'Fuente Deshabilitada',text: 'La Fuente de Financiamiento seleccionada ha sido deshabilitada con &eacute;xito!'});
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
						prFuen.loadData(params);
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
	},
	windowNewFuen: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewFuenFuen',
			title: 'Nueva Fuente',
			contentURL: 'pr/fuen/editfuen',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo!',type: 'error'});
					}
					data.rubro = p.$w.find('[name=rubro]').val();
					if(data.rubro==''){
						p.$w.find('[name=rubro]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/fuen/savefuen',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Fuente de financiamiento fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewFuenFuen');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric();		
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditFuen: function(p){
		new K.Window({
			id: 'windowEditFuenFuen'+p.id,
			title: 'Editar Fuente',
			contentURL: 'pr/fuen/editfuen',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo!',type: 'error'});
					}
					data.rubro = p.$w.find('[name=rubro]').val();
					if(data.rubro==''){
						p.$w.find('[name=rubro]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un rubro!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/fuen/savefuen',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La fuente de financiamiento fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditFuenFuen'+p.id);
				K.block({$element: p.$w});
				$.post('pr/fuen/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').val(data.cod).numeric();
					p.$w.find('[name=rubro]').val(data.rubro);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}	
};