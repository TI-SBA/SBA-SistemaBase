/*******************************************************************************
Estructura Funcional */
prEstr = {
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prEstr',
			titleBar: {
				title: 'Estructura Funcional'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/estr',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo o nombre' ).width('250');
				$mainPanel.find('[name=obj]').html( 'estructura(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prEstr.windowNewFunc();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prEstr.loadData({page: 1,url: 'pr/estr/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						prEstr.loadData({page: 1,url: 'pr/estr/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prEstr.loadData({page: 1,url: 'pr/estr/lista'});
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
				prEstr.renderData($mainPanel.data('items'),1);
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
			if(result.nivel=="PR") sangria = '<span class="ui-icon ui-icon-carat-1-sw"></span>';
			else if(result.nivel=="SP") sangria = '<span class="ui-icon ui-icon-carat-1-sw"></span><span class="ui-icon ui-icon-carat-1-sw"></span>';
			$li.eq(2).html( sangria+''+result.cod );
			$li.eq(3).html( result.nomb );
			$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
			$row.wrapInner('<a class="item" href="javascript: void(0);" />');
			$row.find('a').data('id',result._id.$id).data('nivel',result.nivel).data('data',result).data('estado',estado)
			.contextMenu("conMenPrEstr", {
					onShowMenu: function(e, menu) {
					    var excep = '';	
						$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
						$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
						$(e.target).closest('.item').click();
						K.tmp = $(e.target).closest('.item');
						var nivel = K.tmp.data('nivel');
						if(nivel=="FN"){ 
							excep+='#conMenPrEstr_ediProg,#conMenPrEstr_subProg,#conMenPrEstr_ediSubProg,';
							excep+='#conMenPrEstr_habProg,#conMenPrEstr_desProg,';
							excep+='#conMenPrEstr_habSubProg,#conMenPrEstr_desSubProg,';
							if(K.tmp.data('estado')=="H")excep+='#conMenPrEstr_habFunc';
							else excep+='#conMenPrEstr_desFunc,#conMenPrEstr_ediFunc,#conMenPrEstr_prog';
						}else if(nivel=="PR"){
							excep+='#conMenPrEstr_ediFunc,#conMenPrEstr_prog,#conMenPrEstr_ediSubProg,';
							excep+='#conMenPrEstr_habFunc,#conMenPrEstr_desFunc,';
							excep+='#conMenPrEstr_habSubProg,#conMenPrEstr_desSubProg,';
							if(K.tmp.data('estado')=="H")excep+='#conMenPrEstr_habProg';
							else excep+='#conMenPrEstr_desProg,#conMenPrEstr_ediProg,#conMenPrEstr_subProg';
						}else if(nivel=="SP"){
							excep+='#conMenPrEstr_ediProg,#conMenPrEstr_subProg,#conMenPrEstr_ediFunc,#conMenPrEstr_prog,';
							excep+='#conMenPrEstr_habFunc,#conMenPrEstr_desFunc,';
							excep+='#conMenPrEstr_habProg,#conMenPrEstr_desProg,';
							if(K.tmp.data('estado')=="H")excep+='#conMenPrEstr_habSubProg';
							else excep+='#conMenPrEstr_desSubProg,#conMenPrEstr_ediSubProg';
						}else{
							excep+='#conMenPrEstr_ediProg,#conMenPrEstr_subProg,#conMenPrEstr_ediFunc,#conMenPrEstr_prog,';
						}
						$(excep+',#conMenSpOrd_about',menu).remove();
						return menu;
					},
					bindings: {
						'conMenPrEstr_ediFunc': function(t) {
							prEstr.windowEditFunc({id: K.tmp.data('id'),cod:K.tmp.find('li:eq(2)').html(),nomb: K.tmp.find('li:eq(3)').html()});
						},
						'conMenPrEstr_prog': function(t) {
							prEstr.windowNewProg({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
						},
						'conMenPrEstr_ediProg': function(t) {
							prEstr.windowEditProg({id: K.tmp.data('id')});
						},
						'conMenPrEstr_subProg': function(t) {
							prEstr.windowNewSubProg({id: K.tmp.data('id'),pnomb: K.tmp.find('li:eq(3)').html(),funcion:K.tmp.data('data').funcion});
						},
						'conMenPrEstr_ediSubProg': function(t) {
							prEstr.windowEditSubProg({id: K.tmp.data('id')});
						},
						'conMenPrEstr_desSubProg': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "D"
								};
								K.sendingInfo();
								$.post('pr/estr/savesubprog',data,function(){
									K.clearNoti();
									K.notification({title: 'Subprograma Deshabilitado',text: 'El Subprograma seleccionado ha sido deshabilitado con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrEstr_habSubProg': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "H"
								};
								K.sendingInfo();
								$.post('pr/estr/savesubprog',data,function(){
									K.clearNoti();
									K.notification({title: 'Subprograma Habilitado',text: 'El Subprograma seleccionado ha sido habilitado con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrEstr_habProg': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "H"
								};
								K.sendingInfo();
								$.post('pr/estr/saveprog',data,function(){
									K.clearNoti();
									K.notification({title: 'Programa Habilitado',text: 'El Programa seleccionado ha sido habilitado con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrEstr_desProg': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "D"
								};
								K.sendingInfo();
								$.post('pr/estr/saveprog',data,function(){
									K.clearNoti();
									K.notification({title: 'Programa Deshabilitado',text: 'El Programa seleccionado ha sido deshabilitado con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrEstr_habFunc': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "H"
								};
								K.sendingInfo();
								$.post('pr/estr/savefunc',data,function(){
									K.clearNoti();
									K.notification({title: 'Funcion Habilitada',text: 'La Funci&oacute;n seleccionada ha sido habilitada con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
							});
						},
						'conMenPrEstr_desFunc': function(t) {
							var data = {
									_id: K.tmp.data('id'),
									estado: "D"
								};
								K.sendingInfo();
								$.post('pr/estr/savefunc',data,function(){
									K.clearNoti();
									K.notification({title: 'Funcion Dehabilitada',text: 'La Funci&oacute;n seleccionada ha sido deshabilitada con &eacute;xito!'});
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
				prEstr.renderData(par_data,par_page);
				//$(this).button( "option", "disabled", true );
			});
			$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
        }else{
			$("#mainPanel .gridFoot").hide();
			$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
        }
	},
	windowNewFunc: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewEstrFunc',
			title: 'Nueva Funci&oacute;n',
			contentURL: 'pr/estr/editfunc',
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para la Funci&oacute;n!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para la Funci&oacute;n!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/estr/savefunc',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Funci&oacute; fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewEstrFunc');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric();		
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditFunc: function(p){
		new K.Window({
			id: 'windowEditEstrFunc'+p.id,
			title: 'Editar Funcion',
			contentURL: 'pr/estr/editfunc',
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para la Funci&oacute;n!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para la Funci&oacute;n!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/estr/savefunc',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Funci&oacute;n fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditEstrFunc'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').val(p.cod).numeric();
				p.$w.find('[name=nomb]').val(p.nomb);
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNewProg: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewEstrProg',
			title: 'Nueva Divisi&oacute;n Funcional',
			contentURL: 'pr/estr/editprog',
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el programa!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el programa!',type: 'error'});
					}
					data.funcion = p.id;					
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/estr/saveprog',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Programa fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewEstrProg');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=funcnomb]').html(p.nomb);
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditProg: function(p){
		new K.Window({
			id: 'windowEditEstrProg'+p.id,
			title: 'Editar Divisi&oacute;n Funcional',
			contentURL: 'pr/estr/editprog',
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el programa!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el programa!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/estr/saveprog',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El programa fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditEstrProg'+p.id);
				K.block({$element: p.$w});
				$.post('pr/estr/get','id='+p.id,function(data){
					$.post('pr/estr/get','id='+data.funcion,function(data2){
						p.$w.find('[name=funcnomb]').html(data2.nomb);
					},'json');
					p.$w.find('[name=cod]').val(data.cod).numeric();
					p.$w.find('[name=nomb]').val(data.nomb);					
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},	
	windowNewSubProg: function(p){		
		new K.Window({
			id: 'windowNewEstrSubProg',
			title: 'Nuevo Grupo Funcional',
			contentURL: 'pr/estr/editsubprog',
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el subprograma!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el subprograma!',type: 'error'});
					}
					data.programa = p.id;					
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/estr/savesubprog',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El subprograma fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewEstrSubProg');
				K.block({$element: p.$w});
				p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=prognomb]').html(p.pnomb);
				$.post('pr/estr/get','id='+p.id,function(data){
					$.post('pr/estr/get','id='+data.funcion,function(data2){
						p.$w.find('[name=funcnomb]').html(data2.nomb);
						K.unblock({$element: p.$w});
						},'json');	
				},'json');				
			}
		});
	},
	windowEditSubProg: function(p){
		new K.Window({
			id: 'windowEditEstrSubProg'+p.id,
			title: 'Editar Grupo Funcional',
			contentURL: 'pr/estr/editsubprog',
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
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el subprograma!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el subprograma!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/estr/savesubprog',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Subprograma fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditEstrSubProg'+p.id);
				K.block({$element: p.$w});
				$.post('pr/estr/get','id='+p.id,function(data){
					$.post('pr/estr/get','id='+data.programa,function(data2){
						p.$w.find('[name=prognomb]').html(data2.nomb);
						$.post('pr/estr/get','id='+data2.funcion,function(data3){
							p.$w.find('[name=funcnomb]').html(data3.nomb);
							K.unblock({$element: p.$w});
						},'json');
					},'json');
					p.$w.find('[name=cod]').val(data.cod).numeric();
					p.$w.find('[name=nomb]').val(data.nomb);										
				},'json');
			}
		});
	},
	windowSelectSubprog: function(p){
		p.search = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('pr/estr/lista',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						var sangria = '';
						var nombre = result.nomb;
						if(result.nivel=="PR") {
							sangria = '&bull;';
						}
						else if(result.nivel=="SP") {
							sangria = '&bull;&bull;';
							nombre = '<b>'+nombre+'</b>';						
						}
						var estado = "H";
						if(result.estado)estado=result.estado;
						$li.eq(0).css('background', prClas.states[estado].color).addClass('vtip').attr('title',prClas.states[estado].descr);
						$li.eq(1).html( sangria+''+result.cod );
						$li.eq(2).html( nombre );
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
			id: 'windowSelectSubprog',
			title: 'Seleccionar Grupo Funcional',
			contentURL: 'pr/acti/selectsubprog',
			icon: 'ui-icon-search',
			width: 500,
			height: 400,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Subprograma!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));		
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectSubprog');
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