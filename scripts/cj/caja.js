/*******************************************************************************
cajas */
cjCaja = {
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
			mode: 'cj',
			action: 'cjCaja',
			titleBar: {
				title: 'Cajas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/caja',
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
					cjCaja.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjCaja.loadData({page: 1,url: 'cj/caja/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjCaja.loadData({page: 1,url: 'cj/caja/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				cjCaja.loadData({page: 1,url: 'cj/caja/lista'});
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
					$li.eq(0).css('background',cjCaja.states[result.estado].color).addClass('vtip').attr('title',cjCaja.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( result.local.descr );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						cjCaja.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
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
									cjCaja.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenListEd_edi': function(t) {
									cjCaja.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenListEd_hab': function(t) {
									K.sendingInfo();
									$.post('cj/caja/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
										K.clearNoti();
										K.notification({title: 'Caja Habilitada',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenListEd_des': function(t) {
									K.sendingInfo();
									$.post('cj/caja/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
										K.clearNoti();
										K.notification({title: 'Caja Deshabilitada',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						cjCaja.loadData(params);
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
			id: 'windowDetailsCaja'+p.id,
			title: 'Caja: '+p.nomb,
			contentURL: 'cj/caja/details',
			icon: 'ui-icon-note',
			width: 390,
			height: 190,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsCaja'+p.id);
				K.block({$element: p.$w});
				$.post('cj/caja/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=local_descr]').html(data.local.descr);
					p.$w.find('[name=local_direc]').html(data.local.direccion);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewCaja',
			title: 'Nueva Caja',
			contentURL: 'cj/caja/edit',
			icon: 'ui-icon-plusthick',
			width: 390,
			height: 130,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						local: p.$w.find('[name=local]').data('data'),
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val(),
						est_anex: p.$w.find('[name=est_anex]').val(),
						modulo: p.$w.find('[name=modulo]').val()
					};
					if(data.local==null){
						p.$w.find('[name=btnSel]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un local de Caja!',type: 'error'});
					}else{
						data.local = {
							_id: data.local._id.$id,
							descr: data.local.descr,
							direccion: data.local.direccion
						};
					}
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para la Caja!',type: 'error'});
					}else if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la Caja!',type: 'error'});
					}else if(data.est_anex==''){
						p.$w.find('[name=est_anex]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de establecimiento anexo (ficha RUC) para el local de esta caja',type: 'error'});
					}
					if(data.modulo==''){
						p.$w.find('[name=modulo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de modulo ej. IN de inmuebles!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/caja/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Caja fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewCaja');
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditCaja'+p.id,
			title: 'Editar Caja: '+p.nomb,
			contentURL: 'cj/caja/edit',
			icon: 'ui-icon-pencil',
			width: 390,
			height: 130,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						local: p.$w.find('[name=local]').data('data'),
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val(),
						est_anex: p.$w.find('[name=est_anex]').val(),
						modulo: p.$w.find('[name=modulo]').val()
					};
					if(data.nomb==null){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un local de Caja!',type: 'error'});
					}else{
						data.local = {
							_id: data.local._id.$id,
							descr: data.local.descr,
							direccion: data.local.direccion
						};
					}
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para la Caja!',type: 'error'});
					}else if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la Caja!',type: 'error'});
					}else if(data.est_anex==''){
						p.$w.find('[name=est_anex]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de establecimiento anexo (ficha RUC) para el local de esta caja',type: 'error'});
					}
					if(data.modulo==''){
						p.$w.find('[name=modulo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de modulo ej. IN de inmuebles!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/caja/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Caja fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditCaja'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				$.post('cj/caja/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=est_anex]').val(data.est_anex);
					p.$w.find('[name=modulo]').val(data.modulo);
					p.$w.find('[name=local]').html(data.local.descr).data('data',data.local);
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
			$.post('cj/caja/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
						$li.eq(1).html( result.local.descr );
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
			id: 'windowSelectCaja',
			title: 'Seleccionar Caja',
			contentURL: 'cj/caja/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una Caja!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectCaja');
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