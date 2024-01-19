ciSearch = {
	windowSearchEnti: function(p){
		p.searchEnti = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			if(p.filter!=null){
				params.filter = p.filter;
			}
			if(p.reqs!=null){
				if(p.reqs.domicilios!=null){
					/*
					 * Comentado para agregar cuadro de agregar domicilio
					 *
					if(params.filter==null) params.filter = [];
					params.filter.push({nomb: 'domicilios',value: {$exists: true}});*/
					K.notification({title: 'Domicilio requerido',text: 'Solamente podr&aacute; elegir una entidad con direcci&oacute;n registrada!',type: 'info'});
				}
			}
			$.post('mg/enti/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						var $li = $('li',$row);
						$li.eq(0).html( ciHelper.enti.formatName(result) );
						if(result.docident!=null)
							if(result.docident.length!=0)
								$li.eq(1).html( result.docident[0].tipo + ' ' + result.docident[0].num );
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
			id: 'windowSearchEntidad',
			title: 'Seleccionar Entidad',
			contentURL: 'mg/enti/view_search',
			height: 350,
			width: 620,
			icon: 'ui-icon-search',
			buttons: {
				"Seleccionar": function(){
					K.clearNoti();
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una entidad!',type: 'error'});
					}
					var data = p.$w.find('.ui-state-highlight').closest('.item').data('data');
					if(p.reqs!=null){
						if(p.reqs.domicilios!=null){
							var params = {
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar una entidad con direcci&oacute;n!<br />Puede agregarle una direcci&oacute;n haciendo click aqu&iacute;:<br /><button id="btnAgregarDirec">Agregar Direcci&oacute;n</button>',
								type: 'error'
							},
							tmp = false;
							if(data.domicilios!=null){
								if(data.domicilios.length<1){
									K.notification(params);
									tmp = true;
								}
							}else{
								K.notification(params);
								tmp = true;
							}
							if(tmp==true){
								$('#btnAgregarDirec').click(function(){
									new K.Modal({
										id: 'windowAgregarDirec',
										contentURL: 'mg/enti/add_domi',
										title: 'Agregar Domicilio a '+ciHelper.enti.formatName(data),
										icon: 'ui-icon-plusthick',
										width: 570,
										height: 150,
										buttons: {
											"Agregar": function(){
												K.clearNoti();
												var dataDom = {
													_id: data._id.$id,
													descr: $('#windowAgregarDirec [name=descr]').val(),
													direccion: $('#windowAgregarDirec [name=direc]').val()
												}
												if(dataDom.descr==''){
													$('#windowAgregarDirec [name=descr]').focus();
													return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
												}
												if(dataDom.direccion==''){
													$('#windowAgregarDirec [name=direc]').focus();
													return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una direcci&oacute;n!',type: 'error'});
												}
												$.extend(data,{
													domicilios: [{
														descr: dataDom.descr,
														direccion: dataDom.direccion
													}]
												});
												K.sendingInfo();
												p.$w.find('.ui-dialog-buttonpane button').button('disable');
												$.post('mg/enti/save_add_domi',dataDom,function(){
													K.closeWindow('windowAgregarDirec');
													p.callback(data);
													K.closeWindow(p.$w.attr('id'));
												});
											},
											"Cancelar": function(){
												K.closeWindow('windowAgregarDirec');
											}
										}
									});
								}).button({icons: {priimary: 'ui-icon-plusthick'}});
								return false;
							}
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
				p.$w = $('#windowSearchEntidad');
				K.block({$element: p.$w});
				//p.$w.find('.grid').height('320px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.searchEnti({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	},
	windowSearchTrabajador: function(p){
		p.searchEnti = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('mg/enti/search_tra',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( ciHelper.enti.formatName(result) );
						$li.eq(1).html( result.docident[0].tipo + ' ' + result.docident[0].num );
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
			id: 'windowSearchEntidad',
			title: 'Seleccionar Trabajador',
			contentURL: 'mg/enti/view_search',
			height: 350,
			width: 600,
			icon: 'ui-icon-search',
			buttons: {
				"Seleccionar": function(){
					K.clearNoti();
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un trabajador!',type: 'error'});
					}
					if(p.reqs!=null){
						var data = p.$w.find('.ui-state-highlight').closest('.item').data('data');
						if(p.reqs.domicilios!=null){
							if(data.domicilios!=null){
								if(data.domicilios.length<1){
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad con direcci&oacute;n!',type: 'error'});
								}
							}else return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad con direcci&oacute;n!',type: 'error'});
						}
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSearchEntidad');
				K.block({$element: p.$w});
				//p.$w.find('.grid').height('320px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.searchEnti({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	},
	windowSearchTupa: function(p){
		new K.Modal({
			id: 'windowSearchTupa',
			title: 'Seleccionar Procedimiento del TUPA',
			contentURL: 'td/tupa/view_search',
			height: 400,
			width: 960,
			icon: 'ui-icon-search',
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-layout-center .ui-state-highlight').length<=0){
						return K.notification({text: 'Debe escoger un procedimiento!',type: 'error',layout: 'topLeft'});
					}
					var $row = p.$w.find('.ui-layout-center .ui-state-highlight').closest('.item');
					var data = new Object;
					data.index = $row.data('index');
					data.anio = $row.data('anio');
					data._id = $row.data('_id');
					data.data = $row.data('data');
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSearchTupa');
				dialogLayout = p.$w.layout({
					resizeWithWindow:	false,
					west__size:			170,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				p.$w.resize(function(){
					p.$w.find('.grid:eq(3)').height((p.$w.height()-30)+'px');
					p.$w.find('[name=orga]').find('.grid:eq(1)').height((p.$w.find('[name=orga]').height()-p.$w.find('[name=orga]').find('.grid:eq(0)').height())+'px');
				}).resize();
				p.$w.find('.grid:eq(0),.grid:eq(2)').css('overflow','hidden');

				p.$w.find('.gridReference:eq(1) li:eq(4)').remove();
				p.$w.find().width('790px');
				p.$w.find('[name=orga]').find('.grid:eq(0)').css('overflow','hidden');
				p.$w.find('[name=orga]').find('.grid:eq(1)').css("overflow-x","hidden");
				p.$w.find('.grid:eq(3)').scroll(function(){
					p.$w.find('.grid:eq(2)').scrollLeft($(this).scrollLeft());
				});
				$.post("td/tupa/vigente", {page: 1,textSearch:p.textSearch}, function(data){
					if ( data.items != null ) {
						var orga = p.$w.find('[name=orga]').data('orga');
						for (i=0; i < data.items.length; i++) {
							var $row = p.$w.find('.ui-layout-center .gridReference').clone();
							$li = $('li',$row).addClass('ui-button ui-widget ui-state-default ui-button-text-only');
							$li.eq(0).html( data.orga[i].nomb ).css({
								'min-width': '950px',
								'max-width': '950px',
								'cursor': 'auto'
							});
							$li.eq(1).remove();
							$li.eq(2).remove();
							$li.eq(3).remove();
							$li.eq(4).remove();
							$row.find('ul').attr('name',data.orga[i]._id.$id);
							p.$w.find('.ui-layout-center .gridBody').append( $row.children() );
							$panel = p.$w.find('[name=orga]');
							var $row = $panel.find('.gridReference').clone();
							$row.find('li:eq(0)').html( data.orga[i].nomb );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id', data.orga[i]._id.$id ).click(function(event){
								event.preventDefault();
								p.$w.find('.grid:eq(3)').scrollTo( $('[name='+$(this).data('id')+']'), 800 );
							});
							$panel.find(".gridBody").append( $row.children() );
							for (j=0; j < data.items[i].length; j++) {
								result = data.items[i][j];
								for(var k=0; k<result.modalidades.length; k++){
									var $row = p.$w.find('.ui-layout-center .gridReference').clone();
									$li = $('li',$row);
									$li.eq(0).html( result.titulo );
									$li.eq(1).html( result.modalidades[k].descr );
									$li.eq(2).html( result.modalidades[k].aprueba.plazo );
									if(result.modalidades[k].reqs!=null){
										if(result.modalidades[k].reqs[0].soles!=null)
											$li.eq(3).html( result.modalidades[k].reqs[0].soles );
										else if(result.modalidades[k].reqs[0].uit!=null)
											$li.eq(3).html( result.modalidades[k].reqs[0].uit );
									}
									$row.wrapInner('<a class="item" href="javascript: void(0);" />');
									$row.find('a').data('data',result).data('index',k).dblclick(function(){
										p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
									}).data('_id',data._id).data('anio',data.anio);
									p.$w.find('.ui-layout-center .gridBody').append( $row.children() );
								}
							}
				        }
			      }else
			    	  K.notification({text: 'No hay procedimientos en el TUPA vigente!',type: 'error'});
			      $('#mainPanel').resize();
			      K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
	},
	windowSearchOrga: function(p){
		new K.Modal({
			id: "windowSearchOrga",
			title: "Seleccionar Organizaci&oacute;n",
			width : 500,
			height : 350,
			resizable: false,
			icon: 'ui-icon-search',
			content: "<div class='gridHeader ui-state-default ui-jqgrid-hdiv' style='height:27px' ><label style='align: left;width: 164px;'>Organizaciones</label></div><div class='demo' name='orga'></div>",
			buttons: {
				"Seleccionar": function(){
					K.clearNoti();
					if(p.$w.find("[name=orga]").data("id")==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una organizaci&oacute;n!',type: 'error'});
					}
					$.post('mg/orga/get','id='+p.$w.find("[name=orga]").data("id"),function(data){
						data.id = p.$w.find("[name=orga]").data("id");
						p.callback(data);
					},'json');
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSearchOrga');
				$.post("mg/orga/lis", function(json){
					if(json!=null){
						tree1 = new tree_component();
						tree1.init(p.$w.find("[name=orga]"), {
							data : {
								type: "json",
								json: json
							}
						});
						var clicking = function(){
							p.$w.find("[name=orga] a").unbind('click').click(function () { 
						    	var data = new Object;
						    	data.id = $(this).closest('li').attr("id");
						    	data.name = $(this).closest('li').attr("name");
						    	p.$w.find("[name=orga]").data("id", $(this).closest('li').attr("id"));
						    	p.$w.find("[name=orga]").data("name", $(this).closest('li').attr("name"));
								$.post("mg/orga/lisnodos", data , function(json){
									if(json.length > 0 && json!=null){
										var html = "<a style=\'background-image:url(\"images/n1.png\");\' href='#'>"+data.name+"</a><ul>";
										for (var x = 0; x<json.length; x++){
											html += tree1.parseJSON(json[x]);
										}
										p.$w.find('#'+data.id).html(html+"</ul>");
										p.$w.find('#'+data.id).removeClass("closed").addClass("open");
										clicking();
									}
								},'json');
							});
						};
						clicking();
					}
				},'json');
			}
		});
	},
	/*windowSearchOrga_tramite: function(p){
		p.search = function(params){
			params.oficina = '1';
			params.estado = 'H';
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('mg/orga/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
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
			id: 'windowSearchOrga',
			title: 'Seleccionar Organizaci&oacute;n',
			contentURL: 'mg/orga/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una organizaci&oacute;n!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSearchOrga');
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
	},*/
	
	
	
	
	
	
	
	
	
	windowSearchOrga_tramite: function(p){
		new K.Modal({
			id: 'windowSelect',
			width: 510,
			height: 350,
			title: 'Seleccionar Organizaciones',
			buttons: {
				'Seleccionar': function(){
					if(p.multiple!=null){
						var orgas = [];
						for(var i=0,j=p.$w.find('[name=orga]:checked').length; i<j; i++){
							orgas.push(p.$w.find('[name=orga]:checked').eq(i).closest('.item').data('data'));
						}
						if(orgas.length==0)
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger al menos una oficina!',
								type: 'error'
							});
						p.callback(orgas);
					}else{
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una Organizacion!',type: 'error'});
						}
						p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					}
					K.closeWindow(p.$w.attr('id'));
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w,
					cols: ['','Nombre'],
					data: 'mg/orga/search',
					params: {
						oficina: '1',
						estado: 'H'
					},
					itemdescr: 'organizacion(es)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						if(p.multiple!=null)
							$row.append('<td><input type="checkbox" name="orga" value="'+data._id.$id+'"></td>');
						else
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
						}).contextMenu('conMenListSel', {
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
						$row.find('td:eq(1)').click(function(){
							var $check = $(this).closest('.item').find('[name=orga]');
							if($check.is(':checked')==false) $check.attr('checked','checked');
							else $check.removeAttr('checked');
						});
						return $row;
					}
				});
			}
		});
	},
	
	
	
	
	
	
	
	
	
	
	windowSearchEmpresa: function(p){
		p.searchEnti = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('mg/enti/search_empresas',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
						if(result.docident!=null)
							if(result.docident[0]!=null)
								$li.eq(1).html( result.docident[0].tipo + ' ' + result.docident[0].num );
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
			id: 'windowSearchEmpresa',
			title: 'Seleccionar Empresa',
			contentURL: 'mg/enti/view_search',
			height: 350,
			width: 600,
			icon: 'ui-icon-search',
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({text: 'Debe escoger una empresa!',type: 'error',layout: 'topLeft'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSearchEmpresa');
				K.block({$element: p.$w});
				//p.$w.find('.grid').height('320px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.searchEnti({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	},
	windowSearchAcce: function(p){
		p.searchAcce = function(params){
			params.nomb = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('cm/acce/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
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
							p.searchAcce( params );
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
			id: 'windowSearchAcce',
			title: 'Seleccionar Accesorio',
			contentURL: 'cm/acce/view_search',
			height: 350,
			width: 600,
			icon: 'ui-icon-search',
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({text: 'Debe escoger un accesorio!',type: 'error',layout: 'topLeft'});
					}
					p.callBack(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSearchAcce');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('320px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.searchAcce({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	},
	windowSelectLocal: function(p){
		new K.Modal({
			id: 'windowSelectLocal',
			title: 'Seleccionar Local',
			contentURL: 'mg/titu/select',
			icon: 'ui-icon-search',
			width: 600,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un local!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectLocal');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('350px');
				var params = new Object;
				params.page_rows = 20;
				params.page = (params.page) ? params.page : 1;
				$.post('mg/titu/locales',function(data){
					if ( data.length > 0 ) {
						for (i=0; i < data.length; i++) {
							var result = data[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( result.descr );
							$li.eq(1).html( result.direccion );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',result);
							p.$w.find(".gridBody").append( $row.children() );
						}
					} else {
						K.notification({title: 'Locales no creados',text: 'Debe crear locales para el titular.'});
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};