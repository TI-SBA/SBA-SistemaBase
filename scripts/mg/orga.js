mgOrga = {
	dbRel: function(item){
		var tmp = {
			_id: item._id.$id,
			nomb: item.nomb
		};
		if(item.componente){
			tmp.componente = item.componente;
			if(tmp.componente._id!=null)
				if(tmp.componente._id.$id!=null)
					tmp.componente._id = tmp.componente._id.$id;
		}
		if(item.actividad){
			tmp.actividad = item.actividad;
			if(tmp.actividad._id!=null)
				if(tmp.actividad._id.$id!=null)
					tmp.actividad._id = tmp.actividad._id.$id;
		}
		return tmp;
	},
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgOrga',
			titleBar: { title: 'Estructura Organizacional' }
		});
		//Contenidos
		new K.Panel({
			id: 'treePanel',
			position: 'left',
			title: 'Estructura',
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			content: "<div class='gridHeader ui-state-default ui-jqgrid-hdiv' style='height:27px' ><label style='align: left;width: 164px;'>Saltar a...</label></div>" +
					"<div id='container'><p>&nbsp;</p><div class='demo' id='demo'></div>",
			onContentLoaded: function(){
				$.post("mg/orga/lis", function(json){
					if(json!=null){
						tree1 = new tree_component();
						tree1.init($("#demo"), {
							data : {
				    	  		type: "json",
				    	  		json: json
							}
						});
				      var clicking = function(){
				    	  $("#demo a").unbind('click').click(function () { 
					    	  var data = new Object;
					    	  data.id = $(this).closest('li').attr("id");
					    	  data.name = $(this).closest('li').attr("name");
					    	  $("#demo").data("id", $(this).closest('li').attr("id"));
								$.post("mg/orga/lisnodos", data , function(json){
									if(json.length > 0 && json!=null){
										var html = "<a style=\'background-image:url(\"images/n1.png\");\' href='#'>"+data.name+"</a><ul>";
										for (var x = 0; x<json.length; x++){
											html += tree1.parseJSON(json[x]);
										}
										$('#'+data.id).html(html+"</ul>");
										$('#'+data.id).removeClass("closed").addClass("open");
										clicking();
									}
								},'json');
								$("#mainPanel .gridBody").empty();
								mgOrga.loadData({page: 1,url: 'mg/orga/listanodos',ide: data.id});
							});
				      };
				      clicking();
					}
				},'json');
			}
		});
		new K.Panel({
			id: 'mainPanel',
			title: 'Organizaciones',
			padding: { top: 0, right: 0, bottom: 0, left: 0 },
			contentURL: 'mg/orga',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'organizacion(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').css('float','center').click(function(){
					mgOrga.windowNew({orga: $(this).attr('orga'),padre: $("#demo").data("id")});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				}).attr('placeholder',' Buscar Organizaci\xf3n').width('250');
				$('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
			            $("#mainPanel .gridBody").empty();
			            mgOrga.loadData({page: 1,url: 'mg/orga/listanodos',ide: "0"});
					}else{
			            K.notification({text: 'B&uacute;squeda realizada!'});
			            $("#mainPanel .gridBody").empty();
			            mgOrga.loadData({page: 1,url: 'mg/orga/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				mgOrga.loadData({page: 1,url: 'mg/orga/listanodos',ide: "0"});
				
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
					$li.eq(0).html(" ");
					$li.eq(1).html( result.nomb );
					$li.eq(2).html( result.sigla );
					$row.wrapInner('<a id="'+result._id.$id+'" class="item"  href="javascript: void(0);" />');
					$row.find('a').data('data',result).dblclick(function(){
						mgOrga.windowEdit({id: $(this).data('data')._id.$id, data: $(this).data('data'), padre: $("#demo").data("id")});
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
									params.padre = $("#demo").data("id");
									mgOrga.windowEdit(params);
								}
							}/*,
							{
								label: "Eliminar",
								icon: "ui-icon-closethick",
								callback: function($row){
									var params = new Object;
									params.id = $row.data('data')._id.$id;
									params.nomb = $row.find('li').eq(1).html();
									mgOrga.windowDelete(params);
								}
							}*/]
						});
					}).contextMenu('conMenList', {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							$('#conMenList_eli,#conMenList_imp', menu).remove();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenList_edi': function(t) {
								var params = new Object;
								params.id = K.tmp.data('data')._id.$id;
								params.data = K.tmp.data('data');
								params.padre = $("#demo").data("id");
								mgOrga.windowEdit(params);
							},
							/*'conMenList_eli': function(t) {
								var params = new Object;
								params.id = K.tmp.data('data')._id.$id;
								params.nomb = K.tmp.find('li').eq(1).html();
								mgOrga.windowDelete(params);
							},*/
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
						mgOrga.loadData(params);
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
			$('#mainPanel').resize();
			K.unblock({$element: $('#pageWrapperMain')});
		}, 'json');
	},
	windowNew: function(p){
		if(p.padre==null) p.padre=0;
		K.Window({
			id: "windowNew"+p.padre,
			title: "Agregar Organizaci&oacute;n",
			contentURL: "mg/orga/edit",
			icon: 'ui-icon-note',
			width : 480,
			height : 220,
			buttons: {
				"Agregar": function(){
					K.clearNoti();
					var datos = new Object;
					var data = new Object;
					data.oficina = p.$w.find('[name=oficina] option:selected').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					data.sigla = p.$w.find('[name=sigla]').val();
					data.cod = p.$w.find('[name=cod]').val();
					data.objetivo = p.$w.find('[name=objetivo]').val();
					datos.padre = p.padre;
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre de organizaci&oacute;n!",type:"error"});
					}
					if(data.sigla == ""){
						p.$w.find('[name=sigla]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese la sigla de organizaci&oacute;n!",type:"error"});
					}
					if(data.cod == ""){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el c&oacute;digo de organizaci&oacute;n!",type:"error"});
					}
					/*data.actividad = p.$w.find('[name=act]').data('acti');
					data.componente = p.$w.find('[name=act]').data('comp');
					if(data.actividad == null){
						p.$w.find('[name=btnSel]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe seleccionar la actividad y componente de la organizaci&oacute;n!",type:"error"});
					}*/
					datos.data = data;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("mg/orga/save",datos,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Organizaci&oacute;n agregada!"});
						$("#mainPanel .gridBody").empty();
						mgOrga.loadData({page: 1,url: 'mg/orga/listanodos',ide: p.padre});
						K.closeWindow(p.$w.attr('id'));
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowNew"+p.padre);
				p.$w.find('[name=nomb]').focus();
				p.$w.find('[name=btnSel]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="AC")
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un componente!!',type: 'error'});
						K.block({$element: p.$w});
						$.post('pr/acti/get','id='+data.actividad,function(acti){
							p.$w.find('[name=act]').data('acti',{
								_id: acti._id.$id,
								nomb: acti.nomb,
								cod: acti.cod
							}).data('comp',{
								_id: data._id.$id,
								nomb: data.nomb,
								cod: data.cod
							}).html(acti.cod+' / '+data.cod);
							K.unblock({$element: p.$w});
						},'json');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
			}
		});
	},
	windowEdit: function(p){
		K.Window({
			id: "windowEdit"+p.id,
			title: "Actualizar Organizaci&oacute;n",
			contentURL: "mg/orga/edit",
			icon: 'ui-icon-note',
			width : 480,
			height : 220,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					if(p.padre==null) p.padre=0;
					var data = new Object;
					var datos = new Object;
					datos._id = p.id;
					data.oficina = p.$w.find('[name=oficina] option:selected').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					data.sigla = p.$w.find('[name=sigla]').val();
					data.cod = p.$w.find('[name=cod]').val();
					data.objetivo = p.$w.find('[name=objetivo]').val();
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre de organizaci&oacute;n!",type:"error"});
					}
					if(data.sigla == ""){
						p.$w.find('[name=sigla]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese la sigla de organizaci&oacute;n!",type:"error"});
					}
					if(data.cod == ""){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el c&oacute;digo de organizaci&oacute;n!",type:"error"});
					}
					data.actividad = p.$w.find('[name=act]').data('acti');
					data.componente = p.$w.find('[name=act]').data('comp');
					if(data.actividad == null){
						p.$w.find('[name=btnSel]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe seleccionar la actividad y componente de la organizaci&oacute;n!",type:"error"});
					}
					datos.data = data;
					K.clearNoti();
					K.sendingInfo();
					$('#'+p.$w.attr('id')).dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("mg/orga/update",datos,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: "Organizaci&oacute;n actualizada!"});
						K.closeWindow(p.$w.attr('id'));
						$("#mainPanel .gridBody").empty();
						mgOrga.loadData({page: 1,url: 'mg/orga/listanodos',ide: p.padre});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowEdit"+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnSel]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="AC")
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un componente!!',type: 'error'});
						K.block({$element: p.$w});
						$.post('pr/acti/get','id='+data.actividad,function(acti){
							p.$w.find('[name=act]').data('acti',{
								_id: acti._id.$id,
								nomb: acti.nomb,
								cod: acti.cod
							}).data('comp',{
								_id: data._id.$id,
								nomb: data.nomb,
								cod: data.cod
							}).html(acti.cod+' / '+data.cod);
							K.unblock({$element: p.$w});
						},'json');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('mg/orga/get','id='+p.id,function(data){
					p.data = data;
					p.$w.find('[name=nomb]').val(p.data.nomb);
					p.$w.find('[name=oficina]').selectVal(p.data.oficina);
					p.$w.find('[name=sigla]').val(p.data.sigla);
					p.$w.find('[name=cod]').val(p.data.cod);
					p.$w.find('[name=objetivo]').val(p.data.objetivo);
					if(data.actividad!=null){
						if(data.actividad.cod!=null){
							p.$w.find('[name=act]').data('acti',{
								_id: data.actividad._id.$id,
								nomb: data.actividad.nomb,
								cod: data.actividad.cod
							}).data('comp',{
								_id: data.componente._id.$id,
								nomb: data.componente.nomb,
								cod: data.componente.cod
							}).html(data.actividad.cod+' / '+data.componente.cod);
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDelete: function(p){
		K.notification({text: "Funcion no implementada"});
		/*new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar Organizacion '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> organizacion <strong>'+p.nomb+'</strong>&#63;',
			type: 'modal',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function(){
				$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
				$.post("mg/orga/delete",'id='+p.id,function(){
							K.notification({text: "Grupo Eliminado!!",layout:"topLeft"});
							$("#mainPanel .gridBody").empty();
							mgOrga.loadData({page: 1,url: 'mg/orga/lista'});
							K.closeWindow("windowDelete");
							}
						);
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});*/
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Organizaci&oacute;n',
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
				//parametros: {};
				//if(p.params != null) parametros = p.params;
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'mg/orga/lista',
					params: {actividad:'{$exists:true}'},
					//params: parametros,
					itemdescr: 'organizacion(es)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+'</td>');
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
	}
};
define(
	function(){
		return mgOrga;
	}
);