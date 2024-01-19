/*******************************************************************************
Reservas Presupuestal */
prRese = {
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prRese',
			titleBar: {
				title: 'Reservas Presupuestarias'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/rese',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				//$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de unidad' ).width('250');
				$mainPanel.find('[name=obj]').html( 'reserva(s) presupuestal(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100}).change(function(){
					$('#mainPanel .gridBody').empty();
					prRese.loadData({page: 1,url: 'pr/rese/lista'});
			    });
				$mainPanel.find('.ui-spinner-button').click(function(){
					$('#mainPanel .gridBody').empty();
					prRese.loadData({page: 1,url: 'pr/rese/lista'});
				});
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear());
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					prRese.loadData({page: 1,url: 'pr/rese/lista'});
				});
				prRese.loadData({page: 1,url: 'pr/rese/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.ano = $mainPanel.find('[name=periodo]').val();
		params.mes = $mainPanel.find('[name=mes] :selected').val();
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
					$li.eq(1).html( ciHelper.meses[(parseFloat(result.periodo.mes)-1)]+' - '+result.periodo.ano );
					$li.eq(2).html( result.cant+' ['+result.producto.cod+'] '+result.producto.nomb+' <i>('+result.producto.unidad.nomb+')</i>' );
					$li.eq(3).html( result.organizacion.nomb );
					$li.eq(4).html( result.clasificador.cod );
					$li.eq(5).html( result.fuente.cod );
					$li.eq(6).html( result.monto );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('data',result)
					.contextMenu("conMenPrRese", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenPrRese_ver': function(t) {
								lgCert.windowDetails({id: K.tmp.data('data').certificacion._id.$id,nomb: K.tmp.data('data').certificacion.cod});
							},
							'conMenPrRese_eli': function(t) {
								ciHelper.confirm(
									'Esta seguro(a) de eliminar la reserva presupuestal?',
									function () {
										var data = {
											_id: K.tmp.data('data')._id.$id,
										};
										K.sendingInfo();
										$.post('pr/rese/delete',data,function(){
											K.clearNoti();
											K.notification({title: 'Reserva Eliminada',text: 'La Reserva Presupuestal fue eliminada con &eacute;xito!'});
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
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPrNewRese',
			title: 'Nueva Reserva Presupuestal',
			contentURL: 'pr/rese/edit',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 250,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.ano = p.$w.find('[name=ano]').val();
					if(data.ano==''){
						p.$w.find('[name=ano]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un A&ntilde;o!',type: 'error'});
					}
					data.organizacion = p.$w.find('[name=orga]').data('data');
					if(data.organizacion==null){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar una Organizacion!',type: 'error'});
						return p.$w.find('[name=btnSelectOrga]').click();
					}
					data.clasificador = p.$w.find('[name=clas]').data('data');
					if(data.clasificador==null){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar un Clasificador!',type: 'error'});
						return p.$w.find('[name=btnSelectClas]').click();
					}
					data.monto = p.$w.find('[name=monto]').val();
					if(data.monto==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Ingresar un Monto!',type: 'error'});
					}
					data.glosa = p.$w.find('[name=glosa]').val();
					if(data.glosa==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Ingresar una Glosa!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/rese/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Reserva presupuestal fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrNewRese');
				K.block({$element: p.$w});
				p.$w.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100});			
				p.$w.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				p.$w.find('[name=ano]').val(d.getFullYear()); 
				p.$w.find('[name=btnSelectOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
				p.$w.find('[name=btnSelectClas]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							p.$w.find('[name=clas]').val("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
							p.$w.find('[name=clas]').html(data.cod).data('data',data);
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'},text:false});	
				K.unblock({$element: p.$w});
			}
		});
	},
};