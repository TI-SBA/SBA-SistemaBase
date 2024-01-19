/*************************************************************************
 * Tipos de Expedientes */
alExpdTipos = {
	init: function(){
		K.initMode({
			mode: 'al',
			action: 'alExpdTipos',
			titleBar: {
				title: 'Tipos de Expedientes'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/expdtipos',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre del Tipo de expediente' ).width('250');
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
					alExpdTipos.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alExpdTipos.loadData({page: 1,url: 'al/expdtipos/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alExpdTipos.loadData({page: 1,url: 'al/expdtipos/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alExpdTipos.loadData({page: 1,url: 'al/expdtipos/lista'});
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
					$li.eq(1).html( result.nomb );
					$li.eq(2).html( result.obsv );
					$li.eq(3).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id)
					.contextMenu("conMenList", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$("#conMenList_imp,#conMenList_about",menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									alExpdTipos.windowEdit({id: K.tmp.data('id'), nomb : K.tmp.find('li:eq(1)').html(),obsv : K.tmp.find('li:eq(2)').html()});
								},
								'conMenList_eli': function(t) {
									var data = {
											id: K.tmp.data('id')
										};
										K.sendingInfo();
										$.post('al/expdtipos/delete',data,function(){
											K.clearNoti();
											K.notification({title: 'Tipo de expediente eliminado',text: 'El Tipo de expediente seleccionado ha sido eliminado con &eacute;xito!'});
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
						prExpdTipo.loadData(params);
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
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewAlTipoExpd',
			title: 'Nuevo Tipo',
			contentURL: 'al/expdtipos/edit_expd_tipos',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.obsv = p.$w.find('[name=obsv]').val();
					if(data.obsv==''){
						p.$w.find('[name=obsv]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una observaci&oacute;n !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/expdtipos/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Tl tipo de ezpediente fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewAlTipoExpd');
				K.block({$element: p.$w});
				p.$w.find('[name=nomb]').focus();		
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditAlTipoExpd'+p.id,
			title: 'Editar Tipo de Expediente',
			contentURL: 'al/expdtipos/edit_expd_tipos',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.obsv = p.$w.find('[name=obsv]').val();
					if(data.obsv==''){
						p.$w.find('[name=obsv]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una observaci&oacute;n !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/expdtipos/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El tipo de expediente fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditAlTipoExpd'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=nomb]').val(p.nomb);
				p.$w.find('[name=obsv]').val(p.obsv);
				K.unblock({$element: p.$w});
			}
		});
	}	
};