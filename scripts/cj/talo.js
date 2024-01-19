/*******************************************************************************
talonarios */
cjTalo = {
	types: {
		F: 'Factura',
		R: 'Recibo de Caja',
		B: 'Boleta de Venta',
		ECOM_FACT: 'Factura Electronica',
		ECOM_BOLE: 'Boleta Electronica',
		ECOM_NOCR: 'Nota de Credito Electronica',
		ECOM_NODE: 'Nota de Debito Electronica'
	},
	dbRel: function(talon){
		return {
			_id: talon._id.$id,
			serie: talon.serie,
			tipo: talon.tipo
		};
	},
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjTalo',
			titleBar: {
				title: 'Talonarios'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','Tipo','Prefijo','Sufijo','Numero actual','Limite','Caja','Fecha de registro'],
					data: 'cj/talo/lista',
					params: {},
					itemdescr: 'Comprobantes(s)',
					toolbarHTML: '<button type="button" class="btn btn-primary" name="btnAgregar">Nuevo Talonario</button>',
					onContentLoaded: function($el){
						//$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						$el.find('[name=btnAgregar]').click(function(){
							cjTalo.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						var prefijo = '';
						var sufijo = '';
						if(data.prefijo!=null) prefijo = data.prefijo;
						if(data.sufijo!=null) sufijo = data.sufijo;
						if(data.serie!=null) prefijo = data.serie;
						$row.append('<td>');
						$row.append('<td>'+((cjTalo.types[data.tipo]!=null)?cjTalo.types[data.tipo]:'--')+'</td>');
						$row.append('<td>'+prefijo+'</td>');
						$row.append('<td>'+sufijo+'</td>');
						$row.append('<td>'+data.actual+'</td>');
						$row.append('<td>'+data.fin+'</td>');
						$row.append('<td>'+((data.caja!=null)?data.caja.nomb:'--')+'</td>');
						$row.append('<td>'+moment(data.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenList_edi,#conMenList_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									cjTalo.windowEdit({id:K.tmp.data('id')});
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
	/*init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjTalo',
			titleBar: {
				title: 'Talonarios'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/talo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de talonario' ).width('250');
				$mainPanel.find('[name=obj]').html( 'talonario(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					cjTalo.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjTalo.loadData({page: 1,url: 'cj/talo/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjTalo.loadData({page: 1,url: 'cj/talo/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				cjTalo.loadData({page: 1,url: 'cj/talo/lista'});
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
					$li.eq(1).html( cjTalo.types[result.tipo] );
					$li.eq(2).html( result.serie );
					$li.eq(3).html( result.inicio+'-'+result.fin );
					$li.eq(4).html( result.caja.nomb );
					$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						cjTalo.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									cjTalo.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									cjTalo.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
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
						cjTalo.loadData(params);
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
			id: 'windowDetailsTalo'+p.id,
			title: 'Talonario: '+p.nomb,
			contentURL: 'cj/talo/details',
			icon: 'ui-icon-note',
			width: 390,
			height: 240,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsTalo'+p.id);
				K.block({$element: p.$w});
				$.post('cj/talo/get','id='+p.id+'&edit=1',function(data){
					p.$w.find('[name=nomb]').html(data.caja.nomb);
					p.$w.find('[name=local]').html(data.caja.local.descr);
					p.$w.find('[name=tipo]').html(cjTalo.types[data.tipo]);
					p.$w.find('[name=serie]').html(data.serie);
					p.$w.find('[name=ini]').html(data.inicio);
					p.$w.find('[name=fin]').html(data.fin);
					p.$w.find('[name=actual]').html(data.actual);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},*/
	windowNew: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewTalo',
			title: 'Nuevo Talonario',
			contentURL: 'cj/talo/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var caja = p.$w.find('[name=caja] :selected').data('data'),
						data = {
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							serie: p.$w.find('[name=serie]').val(),
							inicio: p.$w.find('[name=ini]').val(),
							fin: p.$w.find('[name=fin]').val(),
							actual: p.$w.find('[name=actual]').val(),
							prefijo: p.$w.find('[name=prefijo]').val(),
							sufijo: p.$w.find('[name=sufijo]').val()
						};
						if(caja!=null){
							data.caja = {
								_id: caja._id.$id,
								nomb: caja.nomb,
								local: {
									_id: caja.local._id.$id,
									descr: caja.local.descr,
									direccion: caja.local.direccion
								}
							};
						}

						if(data.inicio==''){
							p.$w.find('[name=ini]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un inicio para talonario!',type: 'error'});
						}else if(data.fin==''){
							p.$w.find('[name=fin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un final para talonario!',type: 'error'});
						}else if(data.actual==''){
							p.$w.find('[name=actual]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un actual para talonario!',type: 'error'});
						}
						K.sendingInfo();
						$.post('cj/talo/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Talonario fue registrado con &eacute;xito!'});
							cjTalo.init();
						});
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
			onContentLoaded: function(){
				p.$w = $('#windowNewTalo');
				K.block();
				p.$w.find('[name=serie]').closest('.form-group').hide();
				$.post('cj/caja/all',function(caja){
					p.$w.find('[name=caja]').append('<option value="">Seleccionar Caja</option>');
					if(caja!=null){
						for(var i=0;i<caja.length;i++){
							p.$w.find('[name=caja]').append('<option value="'+caja[i]._id.$id+'">'+caja[i].nomb+'</option>');
							p.$w.find('[name=caja]').find('option:last').data('data',caja[i]);
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowEditTalo'+p.id,
			title: 'Editar Talonario',
			contentURL: 'cj/talo/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var caja = p.$w.find('[name=caja] :selected').data('data'),
						data = {
							_id:p.id,
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							serie: p.$w.find('[name=serie]').val(),
							inicio: p.$w.find('[name=ini]').val(),
							fin: p.$w.find('[name=fin]').val(),
							actual: p.$w.find('[name=actual]').val(),
							prefijo: p.$w.find('[name=prefijo]').val(),
							sufijo: p.$w.find('[name=sufijo]').val()
						};
						if(caja!=null){
							data.caja = {
								_id: caja._id.$id,
								nomb: caja.nomb,
								local: {
									_id: caja.local._id.$id,
									descr: caja.local.descr,
									direccion: caja.local.direccion
								}
							};
						}

						if(data.inicio==''){
							p.$w.find('[name=ini]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un inicio para talonario!',type: 'error'});
						}else if(data.fin==''){
							p.$w.find('[name=fin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un final para talonario!',type: 'error'});
						}else if(data.actual==''){
							p.$w.find('[name=actual]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un actual para talonario!',type: 'error'});
						}
						K.sendingInfo();
						$.post('cj/talo/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Talonario fue registrado con &eacute;xito!'});
							cjTalo.init();
						});
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
			onContentLoaded: function(){
				p.$w = $('#windowEditTalo'+p.id);
				K.block();
				$.post('cj/caja/all',function(caja){
					p.$w.find('[name=caja]').append('<option value="">Seleccionar Caja</option>');
					if(caja!=null){
						for(var i=0;i<caja.length;i++){
							p.$w.find('[name=caja]').append('<option value="'+caja[i]._id.$id+'">'+caja[i].nomb+'</option>');
							p.$w.find('[name=caja]').find('option:last').data('data',caja[i]);
						}
					}
					$.post('cj/talo/get','id='+p.id+'&edit=1',function(data){
						if(data.caja!=null){
							p.$w.find('[name=caja]').val(data.caja._id.$id);
						}
						p.$w.find('[name=tipo]').val(data.tipo);
						p.$w.find('[name=serie]').val(data.serie);
						p.$w.find('[name=ini]').val(data.inicio);
						p.$w.find('[name=fin]').val(data.fin);
						p.$w.find('[name=actual]').val(data.actual);
						p.$w.find('[name=prefijo]').val(data.prefijo);
						p.$w.find('[name=sufijo]').val(data.sufijo);
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap!=null){
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Talonario',
				buttons: {
					'Seleccionar': function(){
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
					},
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','Documento','Serie','Caja'],
						data: 'cj/talo/lista',
						params: {},
						itemdescr: 'talonario(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+cjTalo.types[data.tipo]+'</td>');
							$row.append('<td>'+data.serie+'</td>');
							$row.append('<td>'+data.caja.nomb+'</td>');
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
				$.extend(params,{
					estado: 'H',
					texto: p.$w.find('[name=buscar]').val(),
					page_rows: 20,
					page: (params.page) ? params.page : 1
				});
				$.post('cj/talo/search',params,function(data){
					if ( data.paging.total_page_items > 0 ) {
						for (i=0; i < data.paging.total_page_items; i++) {
							var result = data.items[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( cjTalo.types[result.tipo] );
							$li.eq(1).html( result.serie );
							$li.eq(2).html( result.caja.nomb );
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
				id: 'windowSelectNive',
				title: 'Seleccionar Talonario',
				contentURL: 'cj/talo/select',
				icon: 'ui-icon-search',
				width: 710,
				height: 350,
				buttons: {
					"Seleccionar": function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un talonario!',type: 'error'});
						}
						p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
						K.closeWindow(p.$w.attr('id'));
					},
					"Cancelar": function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowSelectNive');
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
	}
};
define(
	function(){
		return cjTalo;
	}
);