/*******************************************************************************
tipos de medio de pago */
tsTipo = {
	states: {
		H: {
			descr: "Habilitado",
			color: "#006532"
		},
		D: {
			descr: "Deshabilitado",
			color: "#CCCCCC"
		}
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsTipo',
			titleBar: {
				title: 'Tipos de Medio de Pago'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/tipo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de tipo de medio de pago' ).width('250');
				$mainPanel.find('[name=obj]').html( 'tipo(s) de medio de pago' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsTipo.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsTipo.loadData({page: 1,url: 'ts/tipo/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsTipo.loadData({page: 1,url: 'ts/tipo/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsTipo.loadData({page: 1,url: 'ts/tipo/lista'});
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
					$li.eq(0).css('background',tsTipo.states[result.estado].color).addClass('vtip').attr('title',tsTipo.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.cod );
					$li.eq(3).html( result.descr );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						tsTipo.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									tsTipo.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenListEd_edi': function(t) {
									tsTipo.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenListEd_hab': function(t) {
									K.sendingInfo();
									$.post('ts/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
										K.clearNoti();
										K.notification({title: 'Tipo de Medio de Pago Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenListEd_des': function(t) {
									K.sendingInfo();
									$.post('ts/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
										K.clearNoti();
										K.notification({title: 'Tipo de Medio de Pago Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						tsTipo.loadData(params);
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
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsTipo'+p.id,
			title: 'Tipo de Medio de Pago: '+p.nomb,
			contentURL: 'ts/tipo/details',
			icon: 'ui-icon-note',
			width: 360,
			height: 120,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsTipo'+p.id);
				K.block({$element: p.$w});
				$.post('ts/tipo/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').html(data.cod);
					p.$w.find('[name=descr]').html(data.descr);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNewTipo',
			title: 'Nuevo Tipo de Medio de Pago',
			contentURL: 'ts/tipo/edit',
			icon: 'ui-icon-plusthick',
			width: 360,
			height: 130,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						cod: p.$w.find('[name=cod]').val(),
						descr: p.$w.find('[name=descr]').val()
					};
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el Tipo de Medio de Pago!',type: 'error'});
					}
					if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el Tipo de Medio de Pago!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/tipo/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Tipo de Medio de Pago fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewTipo');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({
			id: 'windowEditTipo'+p.id,
			title: 'Editar Tipo de Medio de Pago: '+p.nomb,
			contentURL: 'ts/tipo/edit',
			icon: 'ui-icon-pencil',
			width: 360,
			height: 130,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						cod: p.$w.find('[name=cod]').val(),
						descr: p.$w.find('[name=descr]').val()
					};
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el Tipo de Medio de Pago!',type: 'error'});
					}
					if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el Tipo de Medio de Pago!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/tipo/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Tipo de Medio de Pago fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditTipo'+p.id);
				K.block({$element: p.$w});
				$.post('ts/tipo/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=descr]').val(data.descr);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.estado = 'H';
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('ts/tipo/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.cod );
						$li.eq(1).html( result.descr );
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						$row.find('a').data('id',result._id.$id).dblclick(function(){
							p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
						}).data('data',result);
						p.$w.find(".gridBody").append( $row.children() );
					}
					p.$w.find('[name=showing]').html( p.$w.find(".gridBody a").length );
					p.$w.find('[name=founded]').html( data.paging.total_items );
					
					$moreresults = p.$w.find("[name=moreresults]").unbind();
					if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
						$moreresults.click( function(){
							params.page = parseFloat(data.paging.page) + 1;
							p.search( params );
							//$(this).button( "option", "disabled", true );
						});
						$moreresults.button( "option", "disabled", false );
					}else
						$moreresults.button( "option", "disabled", true );
				} else {
					p.$w.find("[name=moreresults]").button( "option", "disabled", true );
					$('[name=showing]').html( 0 );
					$('[name=founded]').html( data.paging.total_items );
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowSelectTipo',
			title: 'Seleccionar Tipo de Medio de Pago',
			contentURL: 'ts/tipo/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Tipo de Medio de Pago!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectTipo');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('320px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.search({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	}
};