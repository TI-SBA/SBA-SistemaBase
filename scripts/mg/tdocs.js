/*Tipos de Documentos*/
mgTdocs = {
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgTdocs',
			titleBar: { title: 'Tipos de Documentos' }
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'mg/tdoc',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'documentos' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				var $li = $("#mainPanel .gridHeader li");
				$li.eq(0).attr('filter','_id');
				$li.eq(1).attr('filter','nomb');
				$li.click( function(){
					$this = $(this);
					var order = 1;
					if($this.find('.ui-sorter').length>0){
						if($this.find('.ui-asc').length>0){
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
						}else{
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-n ui-asc" style="float:right"></span>');
							order = -1;
						}
					}else{
						$this.closest('.gridHeader').find('.ui-sorter').remove();
						$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
					}
					$("#mainPanel .gridBody").empty();
					mgTdocs.loadData({page: 1,url: 'mg/tdoc/lista',filter: $(this).attr("filter"),order: order});
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					mgTdocs.windowNew({tdoc: $(this).attr('tdoc'),oper: $(this).attr('oper')});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				}).attr('placeholder',' Buscar Tipo de Documento').width('250');
				$('.divSearch [name=btnBuscar]').click(function(){
					console.log($('.divSearch [name=buscar]').val().length);
					if($('.divSearch [name=buscar]').val().length<=0){
			            $("#mainPanel .gridBody").empty();
			            mgTdocs.loadData({page: 1,url: 'mg/tdoc/lista'});
					}else{
			            $("#mainPanel .gridBody").empty();
			            mgTdocs.loadData({page: 1,url: 'mg/tdoc/search'});
			            K.notification({text: 'B&uacute;squeda realizada!',layout:"topLeft"});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				mgTdocs.loadData({page: 1,url: 'mg/tdoc/lista'});
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.nomb = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
		params.page = (params.page) ? params.page : 1;
		$.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) {
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(1).html( result.nomb );
					$row.wrapInner('<a id="'+result._id.$id+'" class="item" />');
					$row.find('a').data('data',result).dblclick(function(){
						mgTdocs.windowEdit({id: $(this).data('data')._id.$id,data: $(this).data('data')});
					}).click(function(event){
						K.gridButtons({
							$row: $(this),
							event: event,
							index: 0,
							buttons: [{
								label: "Editar",
								icon: "ui-icon-pencil",
								callback: function($row){
									var params = new Object;
									params.id = $row.data('data')._id.$id;
									params.data = $row.data('data');
									mgTdocs.windowEdit(params);
								}
							},
							{
								label: "Eliminar",
								icon: "ui-icon-closethick",
								callback: function($row){
									var params = new Object;
									params.id = $row.data('data')._id.$id;
									params.nomb = $row.find('li').eq(1).html();
									mgTdocs.windowDelete(params);
								}
							}]
						});
					}).contextMenu('conMenList', {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							$('#conMenList_imp', menu).remove();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenList_edi': function(t) {
								var params = new Object;
								params.id = K.tmp.data('data')._id.$id;
								params.data = K.tmp.data('data');
								mgTdocs.windowEdit(params);
							},
							'conMenList_eli': function(t) {
								var params = new Object;
								params.id = K.tmp.data('data')._id.$id;
								params.nomb = K.tmp.find('li').eq(1).html();
								mgTdocs.windowDelete(params);
							},
							'conMenList_about': function(t) {
								K.about();
							}
						}
					});					
					$("#mainPanel .gridBody").append( $row.children() );
				}
				count = $("#mainPanel .gridBody .item").length;
				$('#No-Results').hide();
				$('#Results [name=showing]').html( count );
				$('#Results [name=founded]').html( data.paging.total_items );
				$('#Results').show();
				
				$moreresults = $("[name=moreresults]").unbind('click');
				if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						mgTdocs.loadData(params);
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
				$("[name=moreresults]").button( "option", "disabled", true );
			}
			$mainPanel.resize();
			K.unblock({$element: $('#pageWrapper')});
		}, 'json');
	},
	windowNew: function(p){
		p = new Object;
		K.Window({
			id: "windowNew",
			title: "Agregar Tipo de Documento",
			contentURL: "mg/tdoc/edit",
			width : 400,
			height : 70,
			maximizable : false,
			buttons: {
				"Agregar": function(){
					var data = new Object;
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({text: "Ingrese Nombre",type:"error"});
					}
					$('#'+p.$w.attr('id')).dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					$.post("mg/tdoc/save",data,
							function(){
								K.notification({text: "Registro Guardado",layout:"topLeft"});
								$("#mainPanel .gridBody").empty();
								mgTdocs.loadData({page: 1,url: 'mg/tdoc/lista'});
								K.closeWindow("windowNew");
								}
							);
				},
				"Cancelar": function(){
					K.closeWindow("windowNew");
				}
			},
			onContentLoaded: function(){
			p.$w = $("#windowNew");
			p.$w.find('[name=nomb]').focus();
			}
		});
	},
	windowEdit: function(p){
		K.Window({
			id: "windowEdit"+p.id,
			title: "Actualizar Tipo de Documento",
			contentURL: "mg/tdoc/edit",
			width : 400,
			height : 80,
			maximizable : false,
			buttons: {
				"Guardar": function(){
					var data = new Object;
					data._id = p.id;
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({text: "Ingrese Nombre",type:"error"});
					}
					$('#'+p.$w.attr('id')).dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					$.post("mg/tdoc/update",data,function(){
						K.notification({text: "Registro Guardado",layout:"topLeft"});
						K.closeWindow("windowEdit"+p.id);
						$("#mainPanel .gridBody").empty();
						mgTdocs.loadData({page: 1,url: 'mg/tdoc/lista'});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow("windowEdit"+p.id);
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowEdit"+p.id);
				p.$w.find('[name=nomb]').val(p.data.nomb);
				p.$w.find('[name=nomb]').focus();
			}
		});
	},
	windowDelete: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar tipo de documento '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> tipo de documento <strong>'+p.nomb+'</strong>&#63;',
			type: 'modal',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function(){
				K.notification({text: 'Enviando informaci&oacute;n...'});
				$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
				$.post("mg/tdoc/delete",'id='+p.id,function(){
							K.notification({text: "Grupo Eliminado!!",layout:"topLeft"});
							K.closeWindow("windowDelete");
							$("#mainPanel .gridBody").empty();
							mgTdocs.loadData({page: 1,url: 'mg/tdoc/lista'});
							}
						);
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	}
};