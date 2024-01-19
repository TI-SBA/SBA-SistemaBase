/*******************************************************************************
Conceptos */
tsConc = {
	states: {
		'H': {
			descr: "Habilitado",
			color: "green"
		},
		'D': {
			descr: "Deshabilitado",
			color: "#CCC"
		}
	},
	tipo: {
		"P": {
			descr: "Pago"
		},
		"D": {
			descr: "Descuento"
		}
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsConc',
			titleBar: {
				title: 'Conceptos'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/conc',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre del concepto' ).width('250');
				$mainPanel.find('[name=obj]').html( 'conceptos(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsConc.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsConc.loadData({page: 1,url: 'ts/conc/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsConc.loadData({page: 1,url: 'ts/conc/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsConc.loadData({page: 1,url: 'ts/conc/lista'});
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
					$li.eq(0).css('background',tsConc.states[result.estado].color).addClass('vtip').attr('title',tsConc.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( tsConc.tipo[result.tipo].descr );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenTsConc", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').estado=="H") excep+='#conMenTsConc_hab';
								else if(K.tmp.data('data').estado=="D") excep+='#conMenTsConc_edi,#conMenTsConc_des';
								$(excep+',#conMenSpOrd_about,#conMenTsConc_ver',menu).remove();
								return menu;
							},
							bindings: {
								'conMenTsConc_edi': function(t) {
									tsConc.windowEdit({id: K.tmp.data('id')});
								},
								'conMenTsConc_hab': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado:"H"
										};
										K.sendingInfo();
										$.post('ts/conc/save',data,function(){
											K.clearNoti();
											K.notification({title: 'Concepto Habilitado',text: 'El Concepto seleccionado ha sido habilitado con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenTsConc_des': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado:"D"
										};
										K.sendingInfo();
										$.post('ts/conc/save',data,function(){
											K.clearNoti();
											K.notification({title: 'Concepto Deshabilitado',text: 'El Concepto seleccionado ha sido deshabilitado con &eacute;xito!'});
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
						tsConc.loadData(params);
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
			id: 'windowNewtsConc',
			title: 'Nuevo concepto',
			contentURL: 'ts/conc/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;	
					data.estado= "H";
					data.tipo = p.$w.find('[name=tipo] :selected').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					data.descr = p.$w.find('[name=descr]').val();
					if(p.$w.find('[name=RadEnlace] :checked').val()=="C"){
						var clas = p.$w.find('[name=clasif]').data('data');
						if(clas==null){
							p.$w.find('[name=clasif]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Clasificador!',type: 'error'});
						}else{
							data.clasificador = new Object;
							data.clasificador._id = clas._id.$id;
							data.clasificador.cod = clas.cod;
							data.clasificador.nomb = clas.nomb;
						}
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta==null){
						p.$w.find('[name=cuenta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else{
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
					}
					//data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/conc/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El concepto fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewtsConc');
				K.block({$element: p.$w});
				p.$w.find('[name=RadEnlace]').buttonset();
				p.$w.find('#rbtnEnlaceClas').click(function(){
					p.$w.find('[name=tr_cuen]').show();
					p.$w.find('[name=tr_clas]').show();
				});
				p.$w.find('#rbtnEnlaceCuen').click(function(){
					p.$w.find('[name=tr_cuen]').show();
					p.$w.find('[name=tr_clas]').hide();
				});
				p.$w.find('[name=btnClas]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
						p.$w.find('[name=clasif]').val(data.cod).data('data',data);
						p.$w.find('[name=result-clas]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});	
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEdittsConc',
			title: 'Editar concepto',
			contentURL: 'ts/conc/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;	
					data._id = p.id;
					data.estado= "H";
					data.tipo = p.$w.find('[name=tipo] :selected').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					data.descr = p.$w.find('[name=descr]').val();
					if(p.$w.find('[name=RadEnlace] :checked').val()=="C"){
						var clas = p.$w.find('[name=clasif]').data('data');
						if(clas==null){
							p.$w.find('[name=clasif]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Clasificador!',type: 'error'});
						}else{
							data.clasificador = new Object;
							data.clasificador._id = clas._id.$id;
							data.clasificador.cod = clas.cod;
							data.clasificador.nomb = clas.nomb;
						}
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta==null){
						p.$w.find('[name=cuenta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else{
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
					}
					//data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/conc/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El concepto fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEdittsConc');
				K.block({$element: p.$w});
				p.$w.find('[name=RadEnlace]').buttonset();
				p.$w.find('#rbtnEnlaceClas').click(function(){
					p.$w.find('[name=tr_cuen]').show();
					p.$w.find('[name=tr_clas]').show();
				});
				p.$w.find('#rbtnEnlaceCuen').click(function(){
					p.$w.find('[name=tr_cuen]').show();
					p.$w.find('[name=tr_clas]').hide();
				});
				p.$w.find('[name=btnClas]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
						p.$w.find('[name=clasif]').val(data.cod).data('data',data);
						p.$w.find('[name=result-clas]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});	
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('ts/conc/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=tipo]').selectVal(data.tipo);
					if(data.clasificador&&data.cuenta){
						p.$w.find('#rbtnEnlaceClas').click();
						p.$w.find('[name=clasif]').val(data.clasificador.cod).data('data',data.clasificador);
						p.$w.find('[name=result-clas]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						p.$w.find('[name=cuenta]').val(data.cuenta.cod).data('data',data.cuenta);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}else if(data.cuenta){
						p.$w.find('#rbtnEnlaceCuen').click();
						p.$w.find('[name=cuenta]').val(data.cuenta.cod).data('data',data.cuenta);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');							
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('ts/conc/lista',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
						$li.eq(1).html( tsConc.tipo[result.tipo].descr );
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
							p.searchEnti( params );
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
			id: 'windowSelecttsConc',
			title: 'Seleccionar Concepto',
			contentURL: 'ts/conc/select',
			icon: 'ui-icon-search',
			width: 500,
			height: 400,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Concepto!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelecttsConc');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('370px');
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