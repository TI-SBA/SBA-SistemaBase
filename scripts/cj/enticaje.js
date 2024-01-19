cjEntiCaje = {
	init: function(){
		if($('#pageWrapper [child=enti]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('cj/navg/enti',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="enti" />');
					$p.find("[name=cjEnti]").after( $row.children() );
				}
				$p.find('[name=cjEnti]').data('ctas',$('#pageWrapper [child=enti]:first').data('enti'));
				$p.find('[name=cjEntiCaje]').click(function(){ cjEntiCaje.init(); }).addClass('ui-state-highlight');
				$p.find('[name=cjEntiClie]').click(function(){ cjEntiClie.init(); });
			},'json');
		}
		K.initMode({
			mode: 'cj',
			action: 'cjEntiCaje',
			titleBar: {
				title: 'Cuentas: Cajeros'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/caje',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre' ).width('250');
				$mainPanel.find('[name=obj]').html( 'cajeros' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('[name=btnAgregar]').click(function(){
					cjEntiCaje.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13)
						$('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjEntiCaje.loadData({page: 1,url: 'cj/caje/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjEntiCaje.loadData({page: 1,url: 'cj/caje/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				cjEntiCaje.loadData({page: 1,url: 'cj/caje/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.nomb = $('.divSearch [name=buscar]').val();
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
					$li.eq(1).html( ciHelper.enti.formatName(result) );
					$li.eq(2).html( result.docident[0].num );
					if(result.domicilios != null)
					if(result.domicilios.length > 0 )
						$li.eq(3).html( result.domicilios[0].direccion );
					if(result.telefonos != null)
					if(result.telefonos.length > 0 )						
						$li.eq(4).html( result.telefonos[0].num );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('tipo_enti',result.tipo_enti).dblclick(function(){
						cjEntiClie.windowDetails({
							id: $(this).data('id'),
							nomb: $(this).find('li:eq(1)').html()
						});
					}).data('data',result).contextMenu('conMenList', {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							$('#conMenList_imp,#conMenList_about',menu).remove();
							return menu;
						},
						bindings: {
							'conMenList_edi': function(t) {
								K.closeWindow('windowDetailEnti'+$this.data('id'));
								ciEdit.windowEditEntidad({
				                    id: K.tmp.data('data')._id.$id,
				                    nomb: K.tmp.find('li:eq(1)').html(),
				                    tipo_enti: K.tmp.data('data').tipo_enti,
				                    data: K.tmp.data('data'),
				                    callBack: function(data){
				                      $.post('cj/caje/save',data,function(rpta){
				                        K.notification({title: ciHelper.titleMessages.regiAct,text: 'Cajero actualizado!'});
				                        K.closeWindow('windowEntiEdit'+data._id);
				                        if($.cookie('action')=='cjEntiCaje') cjEntiCaje.init();	
				                      });
				                    }
								});									
							},
							'conMenList_eli': function(t){
								K.sendingInfo();
								$.post('cj/caje/remove',{_id: K.tmp.data('id')},function(rpta){
									K.clearNoti();
									K.notification({title: ciHelper.titleMessages.regiAct,text: 'Cajero eliminado!'});
									cjEntiCaje.init();
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
						cjEntiClie.loadData(params);
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
		p = {};
		new K.Modal({
			id: 'windowNewCaje',
			title: 'Crear Nuevo Cajero',
			contentURL: 'cj/caje/edit',
			store: false,
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 260,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var cajas = [],
					enti = p.$w.find('[name=nomb]').data('data');
					if(enti==null){
						p.$w.find('[name=btnSel]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
					}
					if(p.$w.find('.gridBody .item').length<=0){
						p.$w.find('[name=btnCaj]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos una caja!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.gridBody .item').length; i<j; i++){
						cajas.push(p.$w.find('.gridBody .item').eq(i).data('id'));
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/caje/save',{_id: enti._id.$id,cajas: cajas},function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El cajero fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewCaje');
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: function(data){
						p.$w.find('[name=nomb]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnSel]').button( "option", "text", false );
					},filter: [
  					    {nomb: 'tipo_enti',value: 'P'},
 					    {nomb: 'roles.trabajador',value: {$exists: true}},
 					    {nomb: 'roles.cajero',value: {$exists: false}}
 					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCaj]').click(function(){
					cjCaja.windowSelect({callback: function(data){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.nomb);
						$row.find('li:eq(1)').html(data.local.direccion);
						$row.find('button').click(function(){
							$(this).closest('.item').remove();
						}).button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item">');
						$row.find('.item').data('id',data._id.$id);
						p.$w.find('.gridBody').append($row.children());
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
			}
		});
	}
};