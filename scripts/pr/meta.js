/*******************************************************************************
Metas */
prMeta = {
	states: {
		H:{color:'green',descr:'Habilitado'},
		D:{color:'gray',descr:'Deshabilitado'}
	},
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prMeta',
			titleBar: {
				title: 'Metas'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/meta',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo de la meta' ).width('250');
				$mainPanel.find('[name=obj]').html( 'unidad(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prMeta.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prMeta.loadData({page: 1,url: 'pr/meta/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						prMeta.loadData({page: 1,url: 'pr/meta/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prMeta.loadData({page: 1,url: 'pr/meta/lista'});
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
					var estado = "H";
					if(result.estado)estado=result.estado;
					$li.eq(0).css('background', prMeta.states[estado].color).addClass('vtip').attr('title',prMeta.states[estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.cod );
					$li.eq(3).html( result.nomb );
					$li.eq(4).html( result.actividad.cod+' '+result.actividad.nomb );
					$li.eq(5).html( result.cantidad );
					$li.eq(6).html( result.ubigeo );
					$li.eq(7).html( result.distrito );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('estado',estado)
					.contextMenu("conMenPrMeta", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								//excep += '#conMenList_imp,#conMenList_about';
								if(K.tmp.data('estado')=="H")excep = '#conMenPrMeta_hab';
								if(K.tmp.data('estado')=="D")excep = '#conMenPrMeta_des';
								$(excep,menu).remove();
								return menu;
							},
							bindings: {
								'conMenPrMeta_edi': function(t) {
									prMeta.windowEdit({id: K.tmp.data('id')});
								},
								'conMenPrMeta_hab': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de Habilitar esta meta?',
										function () {
											K.sendingInfo();
											$.post('pr/meta/save',{_id: K.tmp.data('id'),estado:'H'},function(){
												K.clearNoti();
												K.notification({title: 'Meta Habilitada',text: 'La meta seleccionada ha sido habilitada con &eacute;xito!'});
												$('#pageWrapperLeft .ui-state-highlight').click();
											});
										},
										function () {
											//nothing
										}										
									);	
								},
								'conMenPrMeta_des': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de Deshabilitar esta meta?',
										function () {
											K.sendingInfo();
											$.post('pr/meta/save',{_id: K.tmp.data('id'),estado:'D'},function(){
												K.clearNoti();
												K.notification({title: 'Meta Deshabilitada',text: 'La meta seleccionada ha sido deshabilitada con &eacute;xito!'});
												$('#pageWrapperLeft .ui-state-highlight').click();
											});
										},
										function () {
											//nothing
										}										
									);	
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
						prMeta.loadData(params);
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
			id: 'windowPrNewMeta',
			title: 'Nueva Meta',
			contentURL: 'pr/meta/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.estado = "H";
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.unidad = {
						_id:p.$w.find('[name=unid] :selected').val(),
						nomb:p.$w.find('[name=unid] :selected').text()
					};
					data.cantidad = p.$w.find('[name=cant]').val();
					data.ubigeo = p.$w.find('[name=ubig]').val();
					data.distrito = p.$w.find('[name=dist]').val();
					if(p.$w.find('[name=acti]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una actividad!',type: 'error'});
					}
					data.actividad = {
						_id: p.$w.find('[name=acti]').data('data')._id.$id,
						cod: p.$w.find('[name=acti]').data('data').cod,
						nomb: p.$w.find('[name=acti]').data('data').nomb
					}
					if(p.$w.find('[name=subp]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un subprograma!',type: 'error'});
					}
					data.subprograma = {
						_id: p.$w.find('[name=subp]').data('data')._id.$id,
						cod: p.$w.find('[name=subp]').data('data').cod,
						nomb: p.$w.find('[name=subp]').data('data').nomb
					};
					if(p.$w.find('[name=proy]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un producto/proyecto!',type: 'error'});
					}
					data.proyecto = {
						_id: p.$w.find('[name=proy]').data('data')._id.$id,
						cod: p.$w.find('[name=proy]').data('data').cod,
						nomb: p.$w.find('[name=proy]').data('data').nomb
					};
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/meta/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La meta fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrNewMeta');
				K.block({$element: p.$w});
				//p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=btnSelect]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="CO"){
							p.$w.find('[name=acti]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar una Actividad!!',type: 'error'});
						}else{
							p.$w.find('[name=acti]').html(data.cod+" "+data.nomb).data('data',data);
						}
					}});
				}).button();
				p.$w.find('[name=btnSelectSp]').click(function(){
					prEstr.windowSelectSubprog({callback: function(data){
						if(data.nivel!="SP"){
							p.$w.find('[name=subp]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Subprograma!!',type: 'error'});
						}else{
							p.$w.find('[name=subp]').html(data.cod+' '+data.nomb).data('data',data);
						}
					}});
				}).button();
				p.$w.find('[name=btnSelectPr]').click(function(){
					prEprog.windowSelectProy({callback: function(data){
						if(data.nivel!="PY"){
							p.$w.find('[name=proy]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Proyecto!!',type: 'error'});
						}else{
							p.$w.find('[name=proy]').html(data.cod+''+data.nomb).data('data',data);
						}
					}});
				}).button();
				$.post('pr/unid/all',function(data){
					var $cbo = p.$w.find('[name=unid]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						}
					}
					K.unblock({$element: p.$w});
				},'json');
				
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPrEditMeta'+p.id,
			title: 'Editar Meta',
			contentURL: 'pr/meta/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.cod = p.$w.find('[name=cod]').val();
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo!',type: 'error'});
					}
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.unidad = {
						_id:p.$w.find('[name=unid] :selected').val(),
						nomb:p.$w.find('[name=unid] :selected').text()
					};
					data.cantidad = p.$w.find('[name=cant]').val();
					data.ubigeo = p.$w.find('[name=ubig]').val();
					data.distrito = p.$w.find('[name=dist]').val();
					if(p.$w.find('[name=acti]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una actividad!',type: 'error'});
					}
					data.actividad = {
						_id :p.$w.find('[name=acti]').data('data')._id.$id,
						cod: p.$w.find('[name=acti]').data('data').cod,
						nomb: p.$w.find('[name=acti]').data('data').nomb
					}
					if(p.$w.find('[name=subp]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un subprograma!',type: 'error'});
					}
					data.subprograma = {
						_id: p.$w.find('[name=subp]').data('data')._id.$id,
						cod: p.$w.find('[name=subp]').data('data').cod,
						nomb: p.$w.find('[name=subp]').data('data').nomb
					};
					if(p.$w.find('[name=proy]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un producto/proyecto!',type: 'error'});
					}
					data.proyecto = {
						_id: p.$w.find('[name=proy]').data('data')._id.$id,
						cod: p.$w.find('[name=proy]').data('data').cod,
						nomb: p.$w.find('[name=proy]').data('data').nomb
					};
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/meta/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La meta fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrEditMeta'+p.id);
				K.block({$element: p.$w});
				//p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=btnSelect]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="CO"){
							p.$w.find('[name=acti]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar una Actividad!!',type: 'error'});
						}else{
							p.$w.find('[name=acti]').html(data.cod+" "+data.nomb).data('data',data);
						}
					}});
				}).button();
				p.$w.find('[name=btnSelectSp]').click(function(){
					prEstr.windowSelectSubprog({callback: function(data){
						if(data.nivel!="SP"){
							p.$w.find('[name=subp]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Subprograma!!',type: 'error'});
						}else{
							p.$w.find('[name=subp]').html(data.cod+' '+data.nomb).data('data',data);
						}
					}});
				}).button();
				p.$w.find('[name=btnSelectPr]').click(function(){
					prEprog.windowSelectProy({callback: function(data){
						if(data.nivel!="PY"){
							p.$w.find('[name=proy]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Proyecto!!',type: 'error'});
						}else{
							p.$w.find('[name=proy]').html(data.cod+''+data.nomb).data('data',data);
						}
					}});
				}).button();
				$.post('pr/unid/all',function(data){
					var $cbo = p.$w.find('[name=unid]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						}
					}
					$.post('pr/meta/get',{id:p.id},function(data){
						p.$w.find('[name=acti]').html(data.actividad.cod+' '+data.actividad.nomb).data('data',data.actividad);
						p.$w.find('[name=subp]').html(data.subprograma.cod+' '+data.subprograma.nomb).data('data',data.subprograma);
						p.$w.find('[name=proy]').html(data.proyecto.cod+' '+data.proyecto.nomb).data('data',data.proyecto);
						p.$w.find('[name=cod]').val(data.cod);
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=unid]').find('[value='+data.unidad._id+']').attr('selected','selected');
						p.$w.find('[name=cant]').val(data.cantidad);
						p.$w.find('[name=ubig]').val(data.ubigeo);
						p.$w.find('[name=dist]').val(data.distrito);
						K.unblock({$element: p.$w});
					},'json');
				},'json');			
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			K.block({$element: p.$w});
			p.$w.find('.gridBody').empty();
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 9999;
			params.page = (params.page) ? params.page : 1;
			$.post('pr/meta/search',params,function(data){
				if ( data.items!=null ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						var estado = "H";
						if(result.estado)estado=result.estado;
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).css('background', prMeta.states[estado].color).addClass('vtip').attr('title',prMeta.states[estado].descr);
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
			id: 'windowSelectprMeta',
			title: 'Seleccionar Meta',
			contentURL: 'pr/meta/select',
			icon: 'ui-icon-search',
			width: 550,
			height: 400,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar una Meta!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectprMeta');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('370px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.search({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
				K.unblock({$element: p.$w});
			}
		});
	}
};