/*******************************************************************************
Cuentas contables */
ctPcon = {
	dbRel: function(data){
		return {
			_id: data._id.$id,
			cod: data.cod,
			descr: data.descr
		};
	},
	tipo: {
		A: "Activo",
		P: "Pasivo",
		PT: "Patrimonio",
		I: "Ingresos",
		G: "Gastos",
		R: "Resultados",
		PR: "Presupuesto",
		O: "Orden"
	},
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctPcon',
			titleBar: {
				title: 'Plan contable'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Codigo',f:'cod'},{n:'Descripción',f:'fecmod'}],
					data: 'ct/pcon/lista',
					params: {id:"", tipo:"A"},
					itemdescr: 'plan de cuenta(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> <select class="form-control" name="tipo">'
					+'<option value="A">Activo</option>'
					+'<option value="P">Pasivo</option>'
					+'<option value="PT">Patrimonio</option>'
					+'<option value="I">Ingresos</option>'
					+'<option value="G">Gastos</option>'
					+'<option value="R">Resultados</option>'
					+'<option value="PR">Presupuestos</option>'
					+'<option value="O">Orden</option>'
					+'</select>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ctPcon.windowNew();
						});
						$el.find('[name=tipo]').change(function(){
							$grid.reinit({params:{id:"",tipo:$el.find('[name=tipo] :selected').val()}});
						});
					},
					search: false,
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					load: function(data,$tbody){
						if(data!=null){
							for(var i in data.items){
								var $row = $('<tr class="item" />');
								$row.append('<td>');
								if(data.items[i].estado==null){
									data.items[i].estado = 'H';
								}
								$row.append('<td>'+ctPcon.states[data.items[i].estado].label+'</td>');
								$row.append('<td>'+data.items[i].cod+'</td>');
								$row.append('<td>'+data.items[i].descr+'</td>');
								$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.items[i].fecmod)+'</kbd><br />'+mgEnti.formatName(data.items[i].modificado)+'</td>');
								$row.data('id',data.items[i]._id.$id).data('estado',data.items[i].estado)
								.data('cod',data.items[i].cod).data('tipo',data.items[i].tipo).contextMenu("conMenCtPcon", {
									onShowMenu: function($row, menu) {
										if($row.data('estado')=='H') $('#conMenCtPcon_hab',menu).remove();
										if($row.data('estado')=='D') $('#conMenCtPcon_des',menu).remove();
										return menu;
									},
									bindings: {
										'conMenCtPcon_edi': function(t) {
											ctPcon.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
										},
										'conMenCtPcon_nue': function(t) {
											ctPcon.windowNew({id: K.tmp.data('id'),cod: K.tmp.data('cod'),tipo: K.tmp.data('tipo')});
										},
										'conMenCtPcon_hab': function(t) {
											ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
													K.clearNoti();
													K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													ctPcon.init();
												});
											},function(){
												$.noop();
											},'Habilitaci&oacute;n de Tipo de Local');
										},
										'conMenCtPcon_des': function(t) {
											ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
													K.clearNoti();
													K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													ctPcon.init();
												});
											},function(){
												$.noop();
											},'Deshabilitaci&oacute;n de Tipo de Local');
										}
									}
								});
								$tbody.append($row);
							}
						}
						return $tbody;
					}
				});
			}
		});
	},
	loadData: function(params){
		if(params==null){
			$("#mainPanel .gridBody").empty();
			params = {};
		}
	    $.extend(params,{
	    	//url: 'ct/pcon/lista',
	    	id: "",
			tipo: $('#mainPanel').find('[name=filTipo] :selected').val(),
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1
	    });
	    $.post(params.url, params, function(data){
			if( data.items!=null ){
				$mainPanel.data('items',data.items);
				$mainPanel.data('page',1);
				$mainPanel.data('total_items',data.items.length);
				$mainPanel.data('page_rows',20);
				$mainPanel.data('total_pages',Math.ceil(data.items.length/20));	
				ctPcon.renderData($mainPanel.data('items'),1);
			}else{
				$('#No-Results').show();
				$('#Results').hide();
				$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
			}
	    	$('#mainPanel').resize();
	    	K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNewCtCuen',
			title: 'Nueva Cuenta',
			contentURL: 'ct/pcon/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 350,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-ban',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							descr: p.$w.find('[name=nomb]').val()
						};
						if(p.cod) data.cod = p.cod+p.$w.find('[name=cod]').val();
						else data.cod = p.$w.find('[name=cod]').val();	
						if(p.tipo) data.tipo = p.tipo;
						else data.tipo = p.$w.find('[name=tipo] option:selected').val();
						if(p.$w.find('[name=cod]').data('dispo')==false){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'C&oacute;digo invalido!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la Cuenta!',type: 'error'});
						}
						if(p.id!=null)
							data.cuentas = {
								padre: p.id
							};
						K.sendingInfo();
						p.$w.find('.ui-dialog-buttonpane button').button('disable');
						$.post('ct/pcon/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Cuenta fue registrada con &eacute;xito!'});
							ctPcon.init();
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
				p.$w = $('#windowNewCtCuen');
				K.block();
				p.$w.find('[name=cod]').focus();
				p.$w.find('[name=cod]').keyup(function(){
					var texto = $(this).val();
					if(p.cod!=null) texto = p.cod+texto;
					$.post('ct/pcon/validar','cod='+texto,function(data){
						if(texto==''){
							p.$w.find('[name=confir]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
							p.$w.find('[name=confirtxt]').html('Ingrese un codigo!');
							p.$w.find('[name=cod]').data('dispo',false);
							return K.notification({text: 'Debe ingresar un codigo v&aacute;lido!',type: 'error'});
						}
						p.$w.find('[name=result]').addClass('ui-icon');
						if(data=="false"){
							p.$w.find('[name=confir]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
							p.$w.find('[name=confirtxt]').html('El Codigo esta disponible!');
							p.$w.find('[name=cod]').data('dispo',true);
						}else if(data=="true"){
							p.$w.find('[name=confir]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
							p.$w.find('[name=confirtxt]').html('El codigo no se encuentra disponible.<br/>Elija otro codigo!');
							p.$w.find('[name=cod]').data('dispo',false);
						}
					},'json');
				}).data('dispo',false);
				if(p.cod) p.$w.find('[name=parentcod]').html(p.cod+"");
				if(p.tipo) p.$w.find('[name=tipo]').replaceWith(ctPcon.tipo[p.tipo]);
				K.unblock();
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({
			id: 'windowEditCtPcon'+p.id,
			title: 'Editar Cuenta '+p.nomb,
			contentURL: 'ct/pcon/edit',
			icon: 'ui-icon-plusthick',
			width: 460,
			height: 350,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-ban',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cod:p.$w.find('[name=cod]').val()
						};
						if(data.cod==""){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un codigo!',type: 'error'});
						}
						data.descr = p.$w.find('[name=nomb]').val();
						if(data.descr==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la Cuenta!',type: 'error'});
						}					
						K.sendingInfo();
						p.$w.find('.ui-dialog-buttonpane button').button('disable');
						$.post('ct/pcon/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Cuenta fue actualizada con &eacute;xito!'});
							ctPcon.init();
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
				p.$w = $('#windowEditCtPcon'+p.id);
				K.block({$element: p.$w});
				$.post('ct/pcon/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=tipo]').replaceWith(ctPcon.tipo[data.tipo]);
					p.$w.find('[name=nomb]').val(data.descr);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelectOld: function(p){
		p.search = function(params){
			K.block({$element: p.$w});
			p.$w.find(".gridBody").empty();
			params.id = "";
			if(p.tipo==null) params.tipo = p.$w.find('[name=tipo] option:selected').val();
			else params.tipo = p.tipo;
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			p.$w.find("[name=moreresults]").remove();
			$.post('ct/pcon/search',params,function(data){
				if(data.items!=null){
					if ( data.items.length > 0 ) {
						for (i=0; i < data.items.length; i++) {
							var result = data.items[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							var estado = "H";
							if(result.estado)estado=result.estado;
							$li.eq(0).css('background', prClas.states[estado].color).addClass('vtip').attr('title',prClas.states[estado].descr);
							$li.eq(1).html( result.cod );
							$li.eq(2).html( result.descr );
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
				}				
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowSelectctCuent',
			title: 'Seleccionar Cuenta',
			contentURL: 'ct/pcon/select',
			icon: 'ui-icon-search',
			width: 615,
			height: 400,
			buttons: {
				"Seleccionar": function(){
					K.clearNoti();
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una cuenta!',type: 'error'});
					}
					var data = p.$w.find('.ui-state-highlight').closest('.item').data('data');
					if(p.last!=null){
						if(data.cuentas.hijos!=null){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una cuenta de &uacute;ltimo nivel!',type: 'error'});
						}
					}
					if(p.digit!=null){
						var substr_count = data.cod.split('.').length - 1,
						length = data.cod.length - substr_count;
						if(length!=p.digit){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable de '+p.digit+' d&iacute;gitos!',type: 'error'});
						}
					}
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectctCuent');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('370px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}}).hide();
				p.$w.find('[name=btnBuscar]').click(function(){
					p.search({page:1});
				}).button({icons:{primary:'ui-icon-search'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				}).focus();
				if(p.tipo==null){
					p.$w.find('[name=tipo]').change(function(){
						p.$w.find('.gridBody').empty();
						p.search({page: 1});
					});
				}else
					p.$w.find('[name=tipo]').remove();	
				p.$w.find('.gridBody').empty();
				/*p.search({page: 1});*/
				p.$w.find('[name=tr_cuenta]').hide();
				K.unblock({$element: p.$w});
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
				title: 'Seleccionar Cuenta Contable',
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
					var params = {};
					if(p.logistica!=null)
						params.logistica = true;
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','C&oacute;digo','Descripci&oacute;n'],
						data: 'ct/pcon/search',
						params: params,
						itemdescr: 'cuenta(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+data.cod+'</td>');
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
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Cuenta Contable',
				buttons: {
					'Seleccionar': function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un pabell&oacute;n!',type: 'error'});
						}
						p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
						tmp_lock = true;
						K.closeWindow(p.$w.attr('id'));
					},
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					var params = {};
					if(p.logistica!=null)
						params.logistica = true;
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','C&oacute;digo','Descripci&oacute;n'],
						data: 'ct/pcon/search',
						params: params,
						itemdescr: 'cuenta(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+data.cod+'</td>');
							$row.append('<td>'+data.descr+'</td>');
							$row.data('data',data).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',data).contextMenu('conMenListSel', {
								onShowMenu: function(e, menu) {
									$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
									$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
									$(e.target).closest('.item').click();
									K.tmp = $(e.target).closest('.item');
									return menu;
								},
								bindings: {
									'conMenListSel_sel': function(t){
										p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
									}
								}
							});
							return $row;
						}
					});
				}
			});
		}
	},
	windowOrder: function(p){
		new K.Modal({
			id: 'windowOrderCtPcon'+p.id,
			title: 'Ordenar Cuentas Hijas de '+p.nomb,
			contentURL: 'ct/pcon/order',
			icon: 'ui-icon-plusthick',
			width: 600,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					data._id = p.id;
					data.cuentas = [];
					for(i=0;i<p.$w.find('.gridBody .item').length;i++){
						data.cuentas.push(p.$w.find('.gridBody .item').eq(i).data('id'));
					}					
					if(p.$w.find('[name=eliminados]').data('data').length>0){
						data.eliminados = p.$w.find('[name=eliminados]').data('data');
					}
					K.sendingInfo();
					p.$w.parent().find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/pcon/save_order',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'Las Subcuentas fuer&oacute;n Ordenadas correctamente!'});
						ctPcon.loadData();
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
				p.$w = $('#windowOrderCtPcon'+p.id);
				K.block({$element: p.$w});
				$.post('ct/pcon/get','id='+p.id+'&datah=true',function(data){
					if(data.cuentas.hijos){
						if(data.cuentas.hijos.length>0){
							for(i=0;i<data.cuentas.hijos.length;i++){
								var $row = p.$w.find('.gridReference').clone();
								$li = $('li',$row);
								if(data.cuentas.hijos[i].cuentas.hijos){
									if(data.cuentas.hijos[i].cuentas.hijos.length>0){
										$li.eq(0).html('&nbsp;');
									}else{
										$li.eq(0).html('<button name="btnEli">Eliminar</button>');
									}
								}else{
									$li.eq(0).html('<button name="btnEli">Eliminar</button>');
								}								
								$li.eq(1).html(data.cuentas.hijos[i].cod);
								$li.eq(2).html(data.cuentas.hijos[i].descr);
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text:false});
								$row.wrapInner('<a class="item" href="javascript: void(0);" />');
								$row.find('a').data('id',data.cuentas.hijos[i]._id.$id);
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
	}
};
define(
	function(){
		return ctPcon;
	}
);