/*******************************************************************************
Notas Literales */
ctNotaLit = {
	init: function(){
		if($('#pageWrapper [child=nota]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ct/navg/nota',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="nota" />');
					$p.find("[name=ctNota]").after( $row.children() );
				}
				$p.find('[name=ctNota]').data('nota',$('#pageWrapper [child=nota]:first').data('nota'));
				$p.find('[name=ctNotaLit]').click(function(){ ctNotaLit.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctNotaNum]').click(function(){ ctNotaNum.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctNotaLit',
			titleBar: {
				title: 'Nota a los estados financieros: Literales'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/nota/index_lit',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el numero o nombre de la nota' ).width('250');
				$mainPanel.find('[name=obj]').html( 'tipos(s) de nota' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctNotaLit.windowNew({
						periodo:$mainPanel.find('[name=periodo]').val(),
						mes:$mainPanel.find('[name=mes] :selected').val()
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				/**** */
				$mainPanel.find('[name=numero]').spinner();
				var d = new Date();
				$mainPanel.find('[name=periodo]').spinner({stop: function(){ $(this).change(); }}).val(d.getFullYear()); 
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				$mainPanel.find('[name=periodo]').change(function(){
					$mainPanel.find('.gridBody').empty();
					ctNotaLit.loadData({page: 1,url: 'ct/nota/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('[name=mes]').change(function(){
					$mainPanel.find('.gridBody').empty();
					ctNotaLit.loadData({page: 1,url: 'ct/nota/lista'});
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						ctNotaLit.loadData({page: 1,url: 'ct/nota/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						ctNotaLit.loadData({page: 1,url: 'ct/nota/search_lit'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				ctNotaLit.loadData({page: 1,url: 'ct/nota/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.mes = $mainPanel.find('[name=mes] :selected').val();
		params.ano = $mainPanel.find('[name=periodo]').val();
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					var result = data.items[i],
					$row = $('.gridReference','#mainPanel').clone(),
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( result.num );
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( result.descr.substr(0,255)+"..." );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('numero',result.num)
					.contextMenu("conMenList", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(excep+',#conMenList_about,#conMenList_imp,#conMenList_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									ctNotaLit.windowEdit({id: K.tmp.data('id'),numero:K.tmp.data('numero')});
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
						ctNotaLit.loadData(params);
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
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewctNotaLit',
			title: 'Nueva Nota Literal',
			contentURL: 'ct/nota/lit_edit',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						periodo: {
							ano: p.periodo,
							mes: p.mes
						},
						num: p.$w.find('[name=numero]').val(),
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val()
					};
					if(data.num==''){
						p.$w.find('[name=numero]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero para la nota literal!',type: 'error'});
					}
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nombre para la nota literal!',type: 'error'});
					}
					K.sendingInfo();
					$.post('ct/nota/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La nota literal fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewctNotaLit');
				K.block({$element: p.$w});
				p.$w.find('[name=numero]').spinner();
				p.$w.find('[name=numero]').parent().find('.ui-button').css('height','14px');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditctNotaLit'+p.id,
			title: 'Editar Nota Literal N&deg;: '+p.numero,
			contentURL: 'ct/nota/lit_edit',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						periodo: {
							ano: p.periodo,
							mes: p.mes
						},
						num: p.$w.find('[name=numero]').val(),
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val()
					};
					if(data.num==''){
						p.$w.find('[name=numero]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero para la nota literal!',type: 'error'});
					}
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nombre para la nota literal!',type: 'error'});
					}
					K.sendingInfo();
					$.post('ct/nota/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La nota literal fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditctNotaLit'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=numero]').spinner();
				p.$w.find('[name=numero]').parent().find('.ui-button').css('height','14px');
				$.post('ct/nota/get','id='+p.id,function(data){
					p.$w.find('[name=numero]').val(data.num);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.mes = parseFloat(p.mes);
			params.ano = p.ano;
			params.estado = 'H';
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('ct/nota/search_lit',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.num );
						$li.eq(1).html( result.nomb );
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
			id: 'windowSelectNotaLit',
			title: 'Seleccionar Nota Literal',
			contentURL: 'ct/nota/select_lit',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una nota literal!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectNotaLit');
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