/*************************************************************************
  Diligencias : Programadas */
alDiliProg = {
	init: function(){
		if($('#pageWrapper [child=aldili]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('al/navg/dili',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="aldili" />');
					$p.find("[name=alDili]").after( $row.children() );
				}
				$p.find('[name=alDili]').data('aldili',$('#pageWrapper [child=aldili]:first').data('aldili'));
				$p.find('[name=alDiliProg]').click(function(){ alDiliProg.init(); }).addClass('ui-state-highlight');
				$p.find('[name=alDiliEjec]').click(function(){ alDiliEjec.init(); });
				$p.find('[name=alDiliSusp]').click(function(){ alDiliSusp.init(); });
			},'json');
		}
		K.initMode({
			mode: 'al',
			action: 'alDiliProg',
			titleBar: {
				title: 'Diligencias Programadas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/dili',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el numero del expediente asociado' ).width('250');
				$mainPanel.find('[name=obj]').html( 'diligencia(s) programada(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					alDiliProg.windowNew({
						clasif:"C"
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=FilTipo]').buttonset();
				$mainPanel.find('#rbtnSelectJ').click(function(){
					$("#mainPanel .gridBody").empty();
					alDiliProg.loadData({page: 1,url: 'al/dili/lista'});
				});
				$mainPanel.find('#rbtnSelectE').click(function(){
					$("#mainPanel .gridBody").empty();
					alDiliProg.loadData({page: 1,url: 'al/dili/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alDiliProg.loadData({page: 1,url: 'al/dili/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alDiliProg.loadData({page: 1,url: 'al/dili/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alDiliProg.loadData({page: 1,url: 'al/dili/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.estado = 'P';
		params.tipo = $('#mainPanel').find('[name=FilTipo] :checked').val();
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
	    	//K.clearNoti();
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var today = new Date();
					//var fecha = new Date(result.fecha);
					/** fechas, notificaction */
					var Hrs = Math.round((-ciHelper.dateDiffNowSec(result.fecha)/(3600))*10/10);
					var Min = Math.round((-ciHelper.dateDiffNowSec(result.fecha)/(60))*10/10);
					if(Hrs<48 && Hrs>-1){
						if(Hrs<1){
							K.notification({title: 'Expediente N&deg;:'+result.expediente.numero,text: 'Faltan '+Min+' Minutos para cumplirse la fecha y hora programada',delay:10000});
						}else{
							K.notification({title: 'Expediente N&deg;:'+result.expediente.numero,text: 'Faltan '+Hrs+' horas para cumplirse la fecha y hora programada',delay:10000});
						}
					}
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');				
					$li.eq(1).html( result.expediente.numero );
					$li.eq(2).html( result.asunto );
					$li.eq(3).html( ciHelper.dateFormat(result.fecha));
					$li.eq(4).html( result.lugar );
					$li.eq(5).html( result.observ );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('expdid',result.expediente._id.$id)
					.contextMenu("conMenAlDili", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								//$(menu).remove();
								return menu;
							},
							bindings: {
								'conMenAlDili_eli': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de suspender esta diligencia ?',
										function () {
											$.post('al/dili/delete',{id:K.tmp.data('id')},function(){
												alDiliProg.init();
											});
										},
										function () {
											//nothing
										}										
									);		
								},
								'conMenAlDili_edi': function(t) {
									alDiliProg.windowEdit({id: K.tmp.data('id'), num : K.tmp.find('li:eq(1)').html()});
								},
								'conMenAlDili_susp': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de suspender esta diligencia ?',
										function () {
											alDiliProg.windowSuspender({id: K.tmp.data('id')});
										},
										function () {
											//nothing
										}										
									);
								},
								'conMenAlDili_verExpd': function(t) {
									alExpdActi.windowDetails({id: K.tmp.data('expdid'),numero : K.tmp.find('li:eq(1)').html()});
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
						alDiliProg.loadData(params);
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
			id: 'windowNewAlDili',
			title: 'Nueva Diligencia',
			contentURL: 'al/dili/edit_dili',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 200,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.asunto = p.$w.find('[name=asunto]').val();
					if(data.asunto==''){
						p.$w.find('[name=asunto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Asunto !',type: 'error'});
					}
					data.tipo = p.$w.find('[name=FilTipo2] :checked').val();
					data.fecha = p.$w.find('[name=fecha]').val();
					if(data.fecha==''){
						p.$w.find('[name=fecha]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Fecha!',type: 'error'});
					}
					data.lugar = p.$w.find('[name=lugar]').val();
					if(data.lugar==''){
						p.$w.find('[name=lugar]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Lugar!',type: 'error'});
					}
					data.observ = p.$w.find('[name=observ]').val();
					var exp = p.$w.find('[name=expediente]').data('data');
					if(exp==null){
						//p.$w.find('[name=expediente]').focus();
						//return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Expediente!',type: 'error'});
						data.expediente = new Object;
						data.expediente._id = null;
						data.expediente.numero = p.$w.find('[name=expediente]').val();
					}else{
						data.expediente = new Object;
						data.expediente._id = exp._id.$id;
						data.expediente.numero = exp.numero;
					}
					data.estado = 'P';
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/dili/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Diligencia fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewAlDili');
				K.block({$element: p.$w});
				p.$w.find('[name=tipo]').focus();
				p.$w.find('[name=fecha]').datetimepicker({
					timeFormat: 'hh:mm',
                    dateFormat:'yy-mm-dd'
				});
				p.$w.find('[name=btnExpediente]').click(function(){
					alExpdActi.windowSelect({callback: function(data){
						p.$w.find('[name=expediente]').val(data.numero).data('data',data);
						p.$w.find('[name=resultexpediente]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=FilTipo2]').buttonset();
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditAlDili',
			title: 'Editar Diligencia',
			contentURL: 'al/dili/edit_dili',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 200,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.asunto = p.$w.find('[name=asunto]').val();
					if(data.asunto==''){
						p.$w.find('[name=asunto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Asunto !',type: 'error'});
					}
					data.tipo = p.$w.find('[name=FilTipo2] :checked').val();
					data.fecha = p.$w.find('[name=fecha]').val();
					if(data.fecha==''){
						p.$w.find('[name=fecha]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Fecha!',type: 'error'});
					}
					data.lugar = p.$w.find('[name=lugar]').val();
					if(data.lugar==''){
						p.$w.find('[name=lugar]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Lugar!',type: 'error'});
					}
					data.observ = p.$w.find('[name=observ]').val();
					var exp = p.$w.find('[name=expediente]').data('data');
					if(exp==null){
						//p.$w.find('[name=expediente]').focus();
						//return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Expediente!',type: 'error'});
						data.expediente = new Object;
						data.expediente._id = null;
						data.expediente.numero = p.$w.find('[name=expediente]').val();
					}else{
						data.expediente = new Object;
						data.expediente._id = exp._id.$id;
						data.expediente.numero = exp.numero;
					}
					data.estado = 'P';
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/dili/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Diligencia fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditAlDili');
				K.block({$element: p.$w});
				p.$w.find('[name=tipo]').focus();
				p.$w.find('[name=fecha]').datetimepicker({
					timeFormat: 'hh:mm',
                    dateFormat:'yy-mm-dd'
				});
				p.$w.find('[name=btnExpediente]').click(function(){
					alExpdActi.windowSelect({callback: function(data){
						p.$w.find('[name=expediente]').val(data.numero).data('data',data);
						p.$w.find('[name=resultexpediente]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=FilTipo2]').buttonset();
				$.post('al/dili/get','id='+p.id,function(data){
					p.$w.find('[name=expediente]').val(data.expediente.numero).data('data',data.expediente);
					if(data.tipo=="E"){
						p.$w.find('[name=FilTpo2]').buttonset('refresh');
						p.$w.find('#rbtnSelect2E').attr("checked","checked");
					}
					p.$w.find('[name=asunto]').val(data.asunto);
					p.$w.find('[name=lugar]').val(data.lugar);
					p.$w.find('[name=fecha]').val(ciHelper.dateFormatBD(data.fecha));
					p.$w.find('[name=observ]').val(data.observ);
					K.unblock({$element: p.$w});
				},'json');			
			}
		});
	},
	windowSuspender: function(p){
		new K.Window({
			id: 'windowSuspAlDili',
			title: 'Suspender Diligencia',
			contentURL: 'al/dili/susp_dili',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 60,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.motivo = p.$w.find('[name=motivo]').val();
					if(data.motivo==''){
						p.$w.find('[name=motivo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe un Motivo para poder Suspender la Diligencia!',type: 'error'});
					}
					data.estado = 'S';
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/dili/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Diligencia fue suspendida con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSuspAlDili');
				K.block({$element: p.$w});
				p.$w.find('[name=motivo]').focus();	
				K.unblock({$element: p.$w});
			}
		});
	}
};