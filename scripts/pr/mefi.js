/*******************************************************************************
Metas */
prMefi = {
	states: {
		H:{color:'green',descr:'Habilitado'},
		D:{color:'gray',descr:'Deshabilitado'}
	},
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prMefi',
			titleBar: {
				title: 'Metas Fisicas'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/mefi',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo de la meta' ).width('250');
				$mainPanel.find('[name=obj]').html( 'meta(s) fisica(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prMefi.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100}).change(function(){
					$('#mainPanel .gridBody').empty();
					prMefi.loadData({page: 1,url: 'pr/mefi/lista'});
			    });			
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear()); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					prMefi.loadData({page: 1,url: 'pr/mefi/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						prMefi.loadData({page: 1,url: 'pr/mefi/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						prMefi.loadData({page: 1,url: 'pr/mefi/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prMefi.loadData({page: 1,url: 'pr/mefi/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.periodo = $mainPanel.find('[name=periodo]').val();
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
					$li.eq(0).css('background', prMefi.states[estado].color).addClass('vtip').attr('title',prMefi.states[estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.componente.nomb );
					$li.eq(3).html( result.nomb );
					$li.eq(4).html( result.unidad.nomb );
					var prog = 0;
					var ejec = 0;
					for(j=0;j<12;j++){
						prog+=parseFloat(result.programado[j]);
						ejec+=parseFloat(result.ejecutado[j]);
					}
					$li.eq(5).html( prog );
					$li.eq(6).html( ejec );
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
									prMefi.windowEdit({id: K.tmp.data('id')});
								},
								'conMenPrMeta_hab': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de Habilitar esta meta?',
										function () {
											K.sendingInfo();
											$.post('pr/mefi/save',{_id: K.tmp.data('id'),estado:'H'},function(){
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
											$.post('pr/mefi/save',{_id: K.tmp.data('id'),estado:'D'},function(){
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
						prMefi.loadData(params);
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
			id: 'windowPrNewMefi',
			title: 'Nueva Meta',
			contentURL: 'pr/mefi/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 500,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.estado = "H";
					data.periodo = p.$w.find('[name=periodo]').val();
					data.cod = p.$w.find('[name=cod]').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.unidad = {
						_id:p.$w.find('[name=unid] :selected').val(),
						nomb:p.$w.find('[name=unid] :selected').text()
					};
					if(p.$w.find('[name=comp]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un componente!',type: 'error'});
					}
					data.componente = {
						_id: p.$w.find('[name=comp]').data('data')._id.$id,
						cod: p.$w.find('[name=comp]').data('data').cod,
						nomb: p.$w.find('[name=comp]').data('data').nomb
					}
					data.programado = [];
					data.ejecutado = [];
					for(i=0;i<12;i++){
						var $row = p.$w.find('[name=meses] tr').eq(i);
						var prog = "0";
						if($row.find('input:eq(0)').val()!="")prog=$row.find('input:eq(0)').val();
						var ejec = "0";
						if($row.find('input:eq(1)').val()!="")ejec=$row.find('input:eq(1)').val();
						data.programado[i] = prog;
						data.ejecutado[i] = ejec;
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/mefi/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La meta fisica fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrNewMefi');
				K.block({$element: p.$w});
				//p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=btnSelect]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="AC"){
							p.$w.find('[name=comp]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Componente!!',type: 'error'});
						}else{
							p.$w.find('[name=comp]').html(data.cod+" "+data.nomb).data('data',data);
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
			id: 'windowPrEditMefi'+p.id,
			title: 'Editar Meta',
			contentURL: 'pr/mefi/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 500,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.periodo = p.$w.find('[name=periodo]').val();
					data.cod = p.$w.find('[name=cod]').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre !',type: 'error'});
					}
					data.unidad = {
						_id:p.$w.find('[name=unid] :selected').val(),
						nomb:p.$w.find('[name=unid] :selected').text()
					};
					if(p.$w.find('[name=comp]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un componente!',type: 'error'});
					}
					data.componente = {
						_id: p.$w.find('[name=comp]').data('data')._id.$id,
						cod: p.$w.find('[name=comp]').data('data').cod,
						nomb: p.$w.find('[name=comp]').data('data').nomb
					}
					data.programado = [];
					data.ejecutado = [];
					for(i=0;i<12;i++){
						var $row = p.$w.find('[name=meses] tr').eq(i);
						var prog = "0";
						if($row.find('input:eq(0)').val()!="")prog=$row.find('input:eq(0)').val();
						var ejec = "0";
						if($row.find('input:eq(1)').val()!="")ejec=$row.find('input:eq(1)').val();
						data.programado[i] = prog;
						data.ejecutado[i] = ejec;
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/mefi/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La meta fisica fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrEditMefi'+p.id);
				K.block({$element: p.$w});
				//p.$w.find('[name=cod]').focus().numeric();
				p.$w.find('[name=btnSelect]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="AC"){
							p.$w.find('[name=comp]').html("").data('data',null);
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Componente!!',type: 'error'});
						}else{
							p.$w.find('[name=comp]').html(data.cod+" "+data.nomb).data('data',data);
						}
					}});
				}).button();
				
				$.post('pr/mefi/get',{id:p.id},function(data){
					p.$w.find('[name=periodo]').val(data.periodo);
					p.$w.find('[name=comp]').html(data.componente.cod+" "+data.componente.nomb).data('data',data.componente);
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=nomb]').val(data.nomb);
					for(i=0;i<12;i++){
						var $row = p.$w.find('[name=meses] tr').eq(i);
						$row.find('input:eq(0)').val(data.programado[i]);
						$row.find('input:eq(1)').val(data.ejecutado[i]);
					}
					$.post('pr/unid/all',function(rpta){
						var $cbo = p.$w.find('[name=unid]');
						if(rpta!=null){
							for(var i=0; i<rpta.length; i++){
								$cbo.append('<option value="'+rpta[i]._id.$id+'">'+rpta[i].nomb+'</option>');
							}
							$cbo.find('[value='+data.unidad._id+']').attr('selected','selected');
						}
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
			$.post('pr/mefi/search',params,function(data){
				if ( data.items!=null ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						var estado = "H";
						if(result.estado)estado=result.estado;
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).css('background', prMefi.states[estado].color).addClass('vtip').attr('title',prMefi.states[estado].descr);
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
			id: 'windowSelectprMefi',
			title: 'Seleccionar Meta',
			contentURL: 'pr/mefi/select',
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
				p.$w = $('#windowSelectprMefi');
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