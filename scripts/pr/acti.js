/*******************************************************************************
Actividades y Componentes */
prActi = {
	states: ["AC","CO"],
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prActi',
			titleBar: {
				title: 'Actividades y Componentes'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/acti',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre' ).width('250');
				$mainPanel.find('[name=obj]').html( 'Actividades y Componente(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prActi.windowNewActi();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prActi.loadData({page: 1,url: 'pr/acti/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						prActi.loadData({page: 1,url: 'pr/acti/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prActi.loadData({page: 1,url: 'pr/acti/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
	    	if( data.items!=null ){
				$mainPanel.data('items',data.items);
				$mainPanel.data('page',1);
				$mainPanel.data('total_items',data.items.length);
				$mainPanel.data('page_rows',20);
				$mainPanel.data('total_pages',Math.ceil(data.items.length/20));	
				prActi.renderData($mainPanel.data('items'),1);
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
			var sangria = '';
			if(result.nivel=="CO") sangria = '<span class="ui-icon ui-icon-carat-1-sw"></span>';
			$li.eq(2).html( sangria+''+result.cod );
			$li.eq(3).html( result.nomb );
			$li.eq(4).html( prActi.states[result.nivel].descr );
			$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
			$row.wrapInner('<a class="item" href="javascript: void(0);" />');
			$row.find('a').data('id',result._id.$id).data('nivel',result.nivel).data('estado',estado)
			.contextMenu("conMenPrActi", {
					onShowMenu: function(e, menu) {
					    var excep = '';	
						$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
						$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
						$(e.target).closest('.item').click();
						K.tmp = $(e.target).closest('.item');
						var nivel = K.tmp.data('nivel');
						if(nivel=="AC"){
							excep+='#conMenPrActi_ediComp,';
							excep+='#conMenPrActi_habComp,#conMenPrActi_desComp,';
							if(K.tmp.data('estado')=="H")excep+='#conMenPrActi_habActi';
							else excep+='#conMenPrActi_desActi,#conMenPrActi_ediActi,#conMenPrActi_comp';								
						}else if(nivel=="CO"){
							excep+='#conMenPrActi_ediActi,#conMenPrActi_comp,';
							excep+='#conMenPrActi_habActi,#conMenPrActi_desActi,';
							if(K.tmp.data('estado')=="H")excep+='#conMenPrActi_habComp';
							else excep+='#conMenPrActi_desComp,#conMenPrActi_ediComp';	
						}else{
							excep+='#conMenPrActi_ediActi,#conMenPrActi_eliActi,#conMenPrActi_comp';
						}
						$(excep+',#conMenSpOrd_about',menu).remove();
						return menu;
					},
					bindings: {
						'conMenPrActi_ediActi': function(t) {
							prActi.windowEditActi({id: K.tmp.data('id')});
						},
						'conMenPrActi_comp': function(t) {
							prActi.windowNewComp({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
						},								
						'conMenPrActi_ediComp': function(t) {
							prActi.windowEditComp({id: K.tmp.data('id')});
						},
						'conMenPrActi_habActi': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "H"
								};
								K.sendingInfo();
								$.post('pr/acti/saveacti',data,function(){
									K.clearNoti();
									K.notification({title: 'Actividad Habilitada',text: 'La actividad seleccionada ha sido habilitada con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrActi_desActi': function(t) {
							ciHelper.confirm(
								'Esta seguro(a) de deshabilitar esta actividad?',
								function () {
									var data = {
										_id: K.tmp.data('id'),
										estado: "D"
									};
									K.sendingInfo();
									$.post('pr/acti/saveacti',data,function(){
										K.clearNoti();
										K.notification({title: 'Actividad Deshabilitada',text: 'La actividad seleccionada ha sido deshabilitada con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								function () {
									//nothing
								}										
							);							
						},
						'conMenPrActi_habComp': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "H"
								};
								K.sendingInfo();
								$.post('pr/acti/savecomp',data,function(){
									K.clearNoti();
									K.notification({title: 'Componente Habilitado',text: 'El componente seleccionado ha sido habilitado con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrActi_desComp': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "D"
								};
								K.sendingInfo();
								$.post('pr/acti/savecomp',data,function(){
									K.clearNoti();
									K.notification({title: 'Componente Desabilitado',text: 'El componente seleccionado ha sido deshabilitado con &eacute;xito!'});
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
				prActi.renderData(par_data,par_page);
				//$(this).button( "option", "disabled", true );
			});
			$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
        }else{
			$("#mainPanel .gridFoot").hide();
			$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
        }
	},
	windowNewActi: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewActiActi',
			title: 'Nueva Actividad',
			contentURL: 'pr/acti/editacti',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 100,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para la Actividad!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para la Actividad!',type: 'error'});
					}
					data.tipo = p.$w.find('[name=tipo]:checked').val();
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/acti/saveacti',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Actividad fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewActiActi');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('#radio').buttonset();
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditActi: function(p){
		new K.Window({
			id: 'windowEditActiActi'+p.id,
			title: 'Editar Actividad',
			contentURL: 'pr/acti/editacti',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 100,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para la Actividad!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para la Actividad!',type: 'error'});
					}
					data.tipo = p.$w.find('[name=tipo]:checked').val();
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/acti/saveacti',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Actividad fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditActiActi'+p.id);
				K.block({$element: p.$w});
				p.$w.find('#radio').buttonset();
				$.post('pr/acti/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod]').val(data.cod).numeric();
					p.$w.find('[name=tipo]').find('[value='+data.tipo+']').click();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewComp: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewActiComp',
			title: 'Nuevo Componente',
			contentURL: 'pr/acti/editcomp',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 350,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo del Componente!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre del Componente!',type: 'error'});
					}
					data.actividad = p.id;
					var subprograma = p.$w.find('[name=subprog]').data('data');
					if(subprograma==null){
						p.$w.find('[name=subprog]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Subprograma!',type: 'error'});
					}else{
						data.subprograma = new Object;
						data.subprograma.id = subprograma._id.$id;
					}
					var proyecto = p.$w.find('[name=proy]').data('data');
					if(proyecto==null){
						p.$w.find('[name=subprog]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Proyecto!',type: 'error'});
					}else{
						data.proyecto = new Object;
						data.proyecto.id = proyecto._id.$id;
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/acti/savecomp',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Componente fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewActiComp');
				K.block({$element: p.$w});
				p.$w.find('[name=btnSubp]').click(function(){
					prEstr.windowSelectSubprog({callback: function(data){
						if(data.nivel!="SP"){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Subprograma!!',type: 'error'});
						}else{
						p.$w.find('[name=subprog]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnProy]').click(function(){
					prEprog.windowSelectProy({callback: function(data){
						if(data.nivel!="PY"){
						return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Proyecto!!',type: 'error'});
						}else{
						p.$w.find('[name=proy]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=actinomb]').html(p.nomb);
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditComp: function(p){
		new K.Window({
			id: 'windowEditActiComp'+p.id,
			title: 'Editar Componente',
			contentURL: 'pr/acti/editcomp',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 350,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el Componente!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el Componente!',type: 'error'});
					}
					var subprograma = p.$w.find('[name=subprog]').data('data');
					if(subprograma==null){
						p.$w.find('[name=subprog]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Subprograma!',type: 'error'});
					}else{
						data.subprograma = new Object;
						data.subprograma.id = subprograma._id.$id;
					}
					var proyecto = p.$w.find('[name=proy]').data('data');
					if(proyecto==null){
						p.$w.find('[name=subprog]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Proyecto!',type: 'error'});
					}else{
						data.proyecto = new Object;
						data.proyecto.id = proyecto._id.$id;
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/acti/savecomp',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Componente fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditActiComp'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnSubp]').click(function(){
					prEstr.windowSelectSubprog({callback: function(data){
						if(data.nivel!="SP"){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Subprograma!!',type: 'error'});
						}else{
						p.$w.find('[name=subprog]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnProy]').click(function(){
					prEprog.windowSelectProy({callback: function(data){
						if(data.nivel!="PY"){
						return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Proyecto!!',type: 'error'});
						}else{
						p.$w.find('[name=proy]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('pr/acti/get','id='+p.id,function(data){
					$.post('pr/acti/get','id='+data.actividad,function(data4){					
						p.$w.find('[name=actinomb]').html(data4.nomb);
					},'json');
					p.$w.find('[name=cod]').val(data.cod).numeric();
					p.$w.find('[name=nomb]').val(data.nomb);				
					$.post('pr/estr/get','id='+data.subprograma.id,function(data2){					
						p.$w.find('[name=subprog]').val(data2.cod).data('data',data2);
					},'json');
					$.post('pr/eprog/get','id='+data.proyecto.id,function(data3){					
						p.$w.find('[name=proy]').val(data3.cod).data('data',data3);
						K.unblock({$element: p.$w});
					},'json');
					p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
				},'json');
			}
		});
	},		
	windowSelectComp: function(p){
		p.search = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('pr/acti/search',params,function(data){
				if ( data.items.length > 0 ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						var sangria = '';
						var nombre = result.nomb;
						if(result.nivel=="CO") {
							sangria = '&bull;';
						}
						var estado = "H";
						if(result.estado)estado=result.estado;
						$li.eq(0).css('background', prClas.states[estado].color).addClass('vtip').attr('title',prClas.states[estado].descr);
						$li.eq(1).html( sangria+''+result.cod );
						$li.eq(2).html( result.nomb );				
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
			id: 'windowSelectprComp',
			title: 'Seleccionar Actividad y Componente',
			contentURL: 'pr/acti/selectsubprog',
			icon: 'ui-icon-search',
			width: 500,
			height: 400,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Componente!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectprComp');
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
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Actividad',
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
					cols: ['','Codigo','Nombre','Actividad','Nivel'],
					data: 'pr/acti/search',
					itemdescr: 'actividad(s)',
					/*toolbarHTML: '&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
						'</select>',
					*/
					/*onContentLoaded: function($el){
							$el.find('[name=modulo]').change(function(){
								var modulo = $el.find('[name=modulo] option:selected').val();
								p.$grid.reinit({params: {modulo: modulo}});
							});
							
						},*/
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.actividad+'</td>');
						$row.append('<td>'+data.nivel+'</td>');
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

prActi.states['AC'] = {
		descr: 'Actividad'
	};
prActi.states['CO'] = {
		descr: 'Componente'
	};