/*******************************************************************************
Unidades de Medida */
prUnid = {
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prUnid',
			titleBar: {
				title: 'Unidades de Medida'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/unid',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de unidad' ).width('250');
				$mainPanel.find('[name=obj]').html( 'unidad(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prUnid.windowNewUnid();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prUnid.loadData({page: 1,url: 'pr/unid/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						prUnid.loadData({page: 1,url: 'pr/unid/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prUnid.loadData({page: 1,url: 'pr/unid/lista'});
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
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( result.cod );
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( result.abrev );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id)
					.contextMenu("conMenPrUnid", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(excep,menu).remove();
								return menu;
							},
							bindings: {
								'conMenPrUnid_ediUnid': function(t) {
									prUnid.windowEditUnid({id: K.tmp.data('id')});
								},
								'conMenPrUnid_eliUnid': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de eliminar esta unidad de medida ?',
										function () {
											var data = {
												id: K.tmp.data('id')
											};
											K.sendingInfo();
											$.post('pr/unid/deleteunid',data,function(){
												K.clearNoti();
												K.notification({title: 'Unidad Eliminada',text: 'La Unidad seleccionada ha sido eliminado con &eacute;xito!'});
												$('#pageWrapperLeft .ui-state-highlight').click();
											});
										},
										function () {
											//nothing
										}										
									);
									
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
						prUnid.loadData(params);
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
	windowNewUnid: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewUnid',
			title: 'Nueva Unidad',
			contentURL: 'pr/unid/editunid',
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
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.abrev = p.$w.find('[name=abrev]').val();
					if(data.abrev==''){
						p.$w.find('[name=abrev]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una abreviatura !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/unid/saveunid',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La unidad de medida fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewUnid');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric();		
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditUnid: function(p){
		new K.Window({
			id: 'windowEditUnid'+p.id,
			title: 'Editar Unidad',
			contentURL: 'pr/unid/editunid',
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
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.abrev = p.$w.find('[name=abrev]').val();
					if(data.abrev==''){
						p.$w.find('[name=abrev]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una abreviatura !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/unid/saveunid',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La unidad de medida fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditUnid'+p.id);
				K.block({$element: p.$w});
				$.post('pr/unid/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').val(data.cod).numeric();
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=abrev]').val(data.abrev);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}	
};