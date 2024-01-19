/*******************************************************************************
Cuentas contables */
prClas = {
	states: {
		"H":{color:"green",descr:"Habilitado"},
		"D":{color:"gray",descr:"Deshabilitado"}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			nomb: item.nomb,
			descr: item.descr
					
		};
	},
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prClas',
			titleBar: {
				title: 'Clasificadores'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/clas',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de clasificador' ).width('250');
				$mainPanel.find('[name=obj]').html( 'clasificadores(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prClas.windowNew({tipo: $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=FilTipo]').buttonset();
				$mainPanel.find('#rbtnTipoI').click(function(){
					$('#mainPanel .gridBody').empty();
					prClas.loadData({page: 1,url: 'pr/clas/lista',tipo : 'I'});
				});
				$mainPanel.find('#rbtnTipoG').click(function(){
					$('#mainPanel .gridBody').empty();
					prClas.loadData({page: 1,url: 'pr/clas/lista',tipo : 'G'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prClas.loadData({page: 1,url: 'pr/clas/lista',tipo : $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
					}else{
						$("#mainPanel .gridBody").empty();
						prClas.loadData({page: 1,url: 'pr/clas/search',tipo : $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prClas.loadData({page: 1,url: 'pr/clas/lista',tipo : $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.tipo = $('#mainPanel').find('[name=rbtnTipo]:checked').val();
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 100;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
	    	if( data.items!=null ){
				$mainPanel.data('items',data.items);
				$mainPanel.data('page',1);
				$mainPanel.data('total_items',data.items.length);
				$mainPanel.data('page_rows',20);
				$mainPanel.data('total_pages',Math.ceil(data.items.length/20));	
				prClas.renderData($mainPanel.data('items'),1);
			}else{
				$('#No-Results').show();
				$('#Results').hide();
				$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
			}
	    	$('#mainPanel').resize();
	    	K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	renderData: function(json,page){
		$mainPanel.data('page',page);
		recPerPage = 20,
	    startRec = Math.max(page - 1, 0) * 20,
	    endRec = startRec + recPerPage;
	    recordsToShow = json.splice(startRec, endRec);
		for (i=0; i < recordsToShow.length; i++) {
			result = recordsToShow[i];
			var $row = $('.gridReference','#mainPanel').clone();
			$li = $('li',$row);
			var estado = "H";
			if(result.estado)estado=result.estado;
			$li.eq(0).css('background', prClas.states[estado].color).addClass('vtip').attr('title',prClas.states[estado].descr);
			$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
			var code = result.cod.toString();;
			s = code.split(".");
			sangria = '';
			for (e=0;e<s.length;e++){
					sangria = '<span class="ui-icon ui-icon-carat-1-sw"></span>'+sangria;						
			}
			$li.eq(2).html( sangria+""+result.cod );
			$li.eq(3).html( result.nomb );
			$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
			$row.wrapInner('<a class="item" href="javascript: void(0);" />');
			$row.find('a').data('id',result._id.$id).data('cod',result.cod).data('data',result).data('estado',estado).dblclick(function(){
				prClas.windowDetails({id: $(this).data('id'),nomb: $(this).data('cod')});
			}).attr("name",result._id.$id).contextMenu("conMenPrClas", {
					onShowMenu: function(e, menu) {
						var excep = "";
						$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
						$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
						$(e.target).closest('.item').click();
						K.tmp = $(e.target).closest('.item');
						if(K.tmp.data('estado')=="H")excep +='#conMenPrClas_hab';
						if(K.tmp.data('estado')=="D")excep +='#conMenPrClas_des,#conMenPrClas_ediClas,#conMenPrClas_Clas';
						$('#conMenList_about,'+excep,menu).remove();
						return menu;
					},
					bindings: {
						'conMenPrClas_ord': function(t) {
							prClas.windowOrder({id: K.tmp.data('id'),nomb: K.tmp.data('cod')});
						},
						'conMenPrClas_ediClas': function(t) {
							prClas.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.data('cod')});
						},
						'conMenPrClas_Clas': function(t) {
							prClas.windowNew({id: K.tmp.data('id'),cod: K.tmp.data('cod'),tipo: $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
						},
						'conMenPrClas_verClas': function(t) {
							prClas.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.data('cod')});
						},
						'conMenPrClas_des': function(t) {
							ciHelper.confirm(
								'Esta seguro(a) de deshabilitar este clasificador?',
								function () {
									var data = {
										_id: K.tmp.data('id'),
										estado:"D"
									};
									K.sendingInfo();
									$.post('pr/clas/save',data,function(){
										K.clearNoti();
										K.notification({title: 'Clasificador Deshabilitado',text: 'El Clasificador seleccionado ha sido deshabilitado con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								function () {
									//nothing
								}										
							);
						},
						'conMenPrClas_hab': function(t) {
							var data = {
								_id: K.tmp.data('id'),
								estado:"H"
							};
							K.sendingInfo();
							$.post('pr/clas/save',data,function(){
								K.clearNoti();
								K.notification({title: 'Clasificador Habilitado',text: 'El Clasificador seleccionado ha sido habilitado con &eacute;xito!'});
								$('#pageWrapperLeft .ui-state-highlight').click();
							});
						}	
						
					}
				});
        	$("#mainPanel .gridBody").append( $row.children() );
			ciHelper.gridButtons($("#mainPanel .gridBody"));
        }
		$('#No-Results').hide();
        $('#Results [name=showing]').html( $mainPanel.find('.item').length );
        $('#Results [name=founded]').html( $mainPanel.data('total_items') );
        $('#Results').show();
		$moreresults = $("[name=moreresults]").unbind();
		if (parseFloat(page) < parseFloat($mainPanel.data('total_pages'))) {
			$("#mainPanel .gridFoot").show();
			$moreresults.click( function(){
				$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
				var par_page = parseFloat($mainPanel.data('page')) ;
				var par_data = $mainPanel.data('items');
				prClas.renderData(par_data,par_page);
				//$(this).button( "option", "disabled", true );
			});
			$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
        }else{
			$("#mainPanel .gridFoot").hide();
			$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
        }
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsClas'+p.id,
			title: 'Clasificador: '+p.nomb,
			contentURL: 'pr/clas/details',
			store: false,
			icon: 'ui-icon-document',
			width: 445,
			height: 200,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsClas'+p.id);
				K.block({$element: p.$w});
				$.post('pr/clas/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').html(data.cod);
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod);
					p.$w.find('[name=cuenta_descr]').html(data.cuenta.descr);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewClasGast',
			title: 'Nuevo Clasificador',
			contentURL: 'pr/clas/edit',
			icon: 'ui-icon-plusthick',
			width: 460,
			height: 200,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					if(p.cod){
						data.cod = p.cod+"."+p.$w.find('[name=cod]').val();
					}else{
						data.cod = p.$w.find('[name=cod]').val();
					}	
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de clasificador de gasto!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de clasificador de gasto!',type: 'error'});
					}
					data.descr = p.$w.find('[name=descr]').val();
					if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n de clasificador de gasto!',type: 'error'});
					}
					/*var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta==null){
						p.$w.find('[name=cuenta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else{
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
					}*/
					data.estado = "H";
					data.tipo = p.tipo;
					data.clasificadores = new Object();
					data.clasificadores.padre = p.id;
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/clas/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El clasificador de gasto fue registrado con &eacute;xito!'});
						$('#mainPanel .gridBody').empty();
						prClas.loadData({url:'pr/clas/lista'});
						//$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewClasGast');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric(false, function() { alert("Solo N&uacute;meros"); this.value = ""; this.focus(); });
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				if(p.cod){
					p.$w.find('[name=parentcod]').html(p.cod+".");
				}
				$.post('ct/pcon/all',function(data){
					p.clasi = data;
					if(data==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Para crear un nuevo clasificador es necesario un plan contable!',type: 'error'});	
					}
					for(var i=0; i<data.length; i++){
						data[i].label = data[i].cod;
					}
					p.$w.find('[name=cuenta]').autocomplete({
						source: data,
						open: function(){
							p.$w.find('[name=cuenta]').removeData('data');
							p.$w.find('[name=result]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						},
						focus: function( event, ui ) {
							p.$w.find('[name=cuenta]').val( ui.item.label );
							p.$w.find('[name=cuenta]').data('data',ui.item);
							p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
							return false;
						},
						select: function( event, ui ) {
							p.$w.find('[name=cuenta]').val( ui.item.label );
							p.$w.find('[name=cuenta]').data('data',ui.item);
							p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
							return false;
						}
					});
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditClasGast'+p.id,
			title: 'Editar Clasificador: '+p.nomb,
			contentURL: 'pr/clas/edit',
			icon: 'ui-icon-plusthick',
			width: 460,
			height: 200,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de clasificador de gasto!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de clasificador de gasto!',type: 'error'});
					}
					data.descr = p.$w.find('[name=descr]').val();
					if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n de clasificador de gasto!',type: 'error'});
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					/*if(cuenta==null){
						p.$w.find('[name=cuenta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else{
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
					}*/
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/clas/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El clasificador de gasto fue actualizado con &eacute;xito!'});
						$('#mainPanel .gridBody').empty();
						prClas.loadData({url:'pr/clas/lista'});
						//$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditClasGast'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus();
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('ct/pcon/all',function(data){
					for(var i=0; i<data.length; i++){
						data[i].label = data[i].cod;
					}
					p.$w.find('[name=cuenta]').autocomplete({
						source: data,
						open: function(){
							p.$w.find('[name=cuenta]').removeData('data');
							p.$w.find('[name=result]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						},
						focus: function( event, ui ) {
							p.$w.find('[name=cuenta]').val( ui.item.label );
							p.$w.find('[name=cuenta]').data('data',ui.item);
							p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
							return false;
						},
						select: function( event, ui ) {
							p.$w.find('[name=cuenta]').val( ui.item.label );
							p.$w.find('[name=cuenta]').data('data',ui.item);
							p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
							return false;
						}
					});
					$.post('pr/clas/get','id='+p.id,function(data){
						p.$w.find('[name=cod]').val(data.cod);
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=descr]').val(data.descr);
						p.$w.find('[name=cuenta]').val(data.cuenta.cod).data('data',data.cuenta);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap!=null){
			p.params = {};
			if(p.tipo!=null){
				p.params.tipo = p.tipo;
			}
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Clasificador',
				allScreen: true,
				buttons: {
					"Seleccionar": {
						icon: 'fa-check',
						type: 'info',
						f: function(){
							if(p.$w.find('.highlights').data('data')!=null){
								p.callback(p.$w.find('.highlights').data('data'));
								K.closeWindow(p.$w.attr('id'));
							}else{
								K.clearNoti();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un item!',
									type: 'error'
								});
							}
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							K.closeWindow(p.$w.attr('id'));
						}
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','Cod.','Nombre','Descripci&oacute;n'],
						data: 'pr/clas/search',
						params: p.params,
						itemdescr: 'clasificador(es)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+data.cod+'</td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.append('<td>'+data.descr+'</td>');
							$row.data('data',data).dblclick(function(){
								p.$w.find('.modal-footer button:first').click();
							}).contextMenu('conMenListSel', {
								bindings: {
									'conMenListSel_sel': function(t) {
										p.$w.find('.modal-footer button:first').click();
									}
								}
							});
							return $row;
						}
					});
				}
			});
		}else{
			p.search = function(params){
				K.block({$element: p.$w});
				p.$w.find('.gridBody').empty();
				params.tipo = p.$w.find('[name=rbtnTipo]:checked').val();
				params.texto = p.$w.find('[name=buscar]').val();
				params.page_rows = 20;
				params.page = (params.page) ? params.page : 1;
				$.post('pr/clas/search',params,function(data){
					if ( data.items!=null ) {
						for (i=0; i < data.items.length; i++) {
							var result = data.items[i];
							var estado = "H";
							if(result.estado)estado=result.estado;
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).css('background', prClas.states[estado].color).addClass('vtip').attr('title',prClas.states[estado].descr);
							$li.eq(1).html( result.cod );
							$li.eq(2).html( result.nomb );						
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',result);
							p.$w.find(".gridBody").append( $row.children() );
						}					
					} else {
						p.$w.find("[name=moreresults]").hide();					
					}
					K.unblock({$element: p.$w});
				},'json');
			};
			new K.Modal({
				id: 'windowSelectprClas',
				title: 'Seleccionar Clasificador',
				contentURL: 'pr/clas/select',
				icon: 'ui-icon-search',
				width: 550,
				height: 400,
				buttons: {
					"Seleccionar": function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Clasificador de Gastos!',type: 'error'});
						}
						p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
						K.closeWindow(p.$w.attr('id'));
					},
					"Cancelar": function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowSelectprClas');
					K.block({$element: p.$w});
					p.$w.find('.grid').height('370px');
					p.$w.find("[name=FilTipos]").buttonset();
					p.$w.find('#rbtnTipoIs').click(function(){
						p.search({page: 1,tipo : 'I'});
					});
					p.$w.find('#rbtnTipoGs').click(function(){
						p.search({page: 1,tipo : 'I'});
					});
					p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
					p.$w.find("[name=buscar]").keyup(function(e){
						if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
					});
					p.$w.find('[name=btnBuscar]').click(function(){
						p.search({page: 1});
					}).button({icons: {primary: 'ui-icon-search'},text: false});
					K.unblock({$element: p.$w});
				}
			});
		}
	},
	windowOrder: function(p){
		new K.Window({
			id: 'windowOrderPrClas'+p.id,
			title: 'Ordenar Clasificadores Hijos de '+p.nomb,
			contentURL: 'ct/pcon/order',
			icon: 'ui-icon-plusthick',
			width: 600,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					data._id = p.id;
					data.clasificadores = [];
					for(i=0;i<p.$w.find('.gridBody .item').length;i++){
						data.clasificadores.push(p.$w.find('.gridBody .item').eq(i).data('id'));
					}					
					if(p.$w.find('[name=eliminados]').data('data').length>0){
						data.eliminados = p.$w.find('[name=eliminados]').data('data');
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/clas/save_order',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'Las Subpartidas fuer&oacute;n Ordenadas correctamente!'});
						prClas.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose:function(){
				p.$w.find('[name=btnEli]').die('click');
			},
			onContentLoaded: function(){
				p.$w = $('#windowOrderPrClas'+p.id);
				K.block({$element: p.$w});
				$.post('pr/clas/get','id='+p.id+'&datah=true',function(data){
					if(data.clasificadores.hijos){
						if(data.clasificadores.hijos.length>0){
							for(i=0;i<data.clasificadores.hijos.length;i++){
								var $row = p.$w.find('.gridReference').clone();
								$li = $('li',$row);
								if(data.clasificadores.hijos[i].clasificadores.hijos){
									if(data.clasificadores.hijos[i].clasificadores.hijos.length>0){
										$li.eq(0).html('&nbsp;');
									}else{
										$li.eq(0).html('<button name="btnEli">Eliminar</button>');
									}
								}else{
									$li.eq(0).html('<button name="btnEli">Eliminar</button>');
								}								
								$li.eq(1).html(data.clasificadores.hijos[i].cod);
								$li.eq(2).html(data.clasificadores.hijos[i].nomb);
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text:false});
								$row.wrapInner('<a class="item" href="javascript: void(0);" />');
								$row.find('a').data('id',data.clasificadores.hijos[i]._id.$id);
								p.$w.find(".gridBody").append( $row.children() );
							}
						}
					}
					K.unblock({$element: p.$w});			
				},'json');
				p.$w.find('[name=eliminados]').data('data',[]);
				p.$w.find('[name=btnEli]').live('click',function(){
					var id = $(this).closest('.item').data('id');
					var data = p.$w.find('[name=eliminados]').data('data');
					data.push(id);
					p.$w.find('[name=eliminados]').data('data',data);
					$(this).closest('.item').remove();
				});
				p.$w.find(".gridBody").sortable();
			}
		});
	},
	init2: function(){
		K.initMode({
			mode: 'pr',
			action: 'prClas',
			titleBar: {
				title: 'Clasificadores'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/clas/index2',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$("#addtree").jqGrid({ 
					url: 'pr/clas/three',
					treedatatype: "xml", 
					mtype: "POST", 
					colNames:["id","Nombre"], 
					colModel:[ 
						{name:'id',index:'id', width:1,hidden:true,key:true}, 
						{name:'nomb',index:'nomb', width:180}
					], 
					height:'auto', 
					pager : "#paddtree", 
					treeGrid: true,
					ExpandColumn : 'nomb', 
					editurl:'server.php?q=dummy', 
					caption: "Add Tree node example"
				}); 
				$("#addtree").jqGrid('navGrid',"#paddtree");
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	}
	
};
define(
	function(){
		return prClas;
	}
);