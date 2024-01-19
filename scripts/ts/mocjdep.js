/*******************************************************************************
Cajas chicas */
tsMocjDep = {
	types: {
		F: 'Factura',
		R: 'Recibo de Caja',
		B: 'Boleta de Venta',
		T: 'Ticket',
		H: 'Recibo por Honorarios'
	},
	init: function(){
		if($('#pageWrapper [child=mocj]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/mocj',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="mocj" />');
					$p.find("[name=tsMocj]").after( $row.children() );
				}
				$p.find('[name=tsMocj]').data('mocj',$('#pageWrapper [child=mocj]:first').data('mocj'));
				$p.find('[name=tsMocjDep]').click(function(){ tsMocjDep.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsMocjAll]').click(function(){ tsMocjAll.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsMocjDep',
			titleBar: {
				title: 'Movimientos de Caja Chica por dependencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/mocj',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'movimiento(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsMocjDep.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnGenerar]').click(function(){
					tsMocjDep.windowRendir();
				}).button({icons: {primary: 'ui-icon-gear'}});
				$mainPanel.find('[name=btnRendicion]').click(function(){
					tsMocjDep.windowVer();
				}).button({icons: {primary: 'ui-icon-document'}});
				if($mainPanel.find('[name=organizacion]').val()==""){
					$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				}
				$mainPanel.find('[name=btnOrga]').hide();
				$mainPanel.find('[name=btnRendicion]').hide();
				$mainPanel.find('[name=numero]').change(function(){
					$mainPanel.find('.gridBody').empty();
					if($(this).find('option:selected').attr('estado')=='A'){
						$mainPanel.find('[name=btnGenerar]').show();
						$mainPanel.find('[name=btnAgregar]').show();
						$mainPanel.find('[name=btnRendicion]').hide();
					}else{
						$mainPanel.find('[name=btnGenerar]').hide();
						$mainPanel.find('[name=btnAgregar]').hide();
						$mainPanel.find('[name=btnRendicion]').show();
					}
					tsMocjDep.loadData({page: 1,url: 'ts/mocj/lista'});
				});
				K.unblock({$element: $('#pageWrapperMain')});
				$.post('ts/cjch/by_orga',function(data){
					var $select = $mainPanel.find('[name=caja]').change(function(){
						var $this = $(this),
						$select = $mainPanel.find('[name=numero]').empty();
						K.block({$element: $('#pageWrapperMain')});
						$mainPanel.find('.gridBody').empty();
						$.post('ts/mocj/get_saldos','id='+$this.find('option:selected').val(),function(data){
							if(data!=null){
								for(var i=0,j=data.length; i<j; i++){
									$select.append('<option value="'+data[i]._id.$id+'" num="'+data[i].cod+'" estado="'+data[i].estado+'">'+data[i].cod+' ('+ciHelper.dateFormatOnlyDay(data[i].fecreg)+')'+'</option>');
									if(data[i].estado=='A'){
										$select.find('option:last').html(data[i].cod+' (Actual)');
									}
								}
							}
							$select.change();
						},'json');
					});
					if(data==null){
						var error = 'Su organizaci&oacute;n no tiene cajas chicas asignadas!';
						K.block({$element: $('#pageWrapperMain'),message: error});
						return K.notification({title: 'Caja Chicas no encontradas',text: error,type: 'info'});
					}
					for(var i=0,j=data.length; i<j; i++){
						$select.append('<option value="'+data[i]._id.$id+'" actual="'+data[i].cod+'">'+data[i].nomb+'</option>');
					}
					$select.change();
				},'json');
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			caja: $mainPanel.find('[name=caja] option:selected').val(),
			num: $mainPanel.find('[name=numero] option:selected').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html( result.item );
					$li.eq(1).html( ciHelper.dateFormat(result.fecreg) );
					$li.eq(2).html( tsMocjDep.types[result.documento] );
					$li.eq(3).html( result.num_doc );
					$li.eq(4).html( ciHelper.enti.formatName(result.beneficiario) );
					$li.eq(5).html( result.concepto );
					$li.eq(6).html( result.organizacion.nomb );
					$li.eq(7).html( ciHelper.formatMon(result.monto) );
					$li.eq(8).html( result.clasificador.cod );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							if($mainPanel.find('[name=numero] option:selected').attr('estado')!='A')
								$('#conMenListEd_edi',menu).remove();
							$('#conMenListEd_ver,#conMenListEd_hab,#conMenListEd_des',menu).remove();
							return menu;
						},
						bindings: {
							'conMenListEd_edi': function(t) {
								tsMocjDep.windowEdit({id: K.tmp.data('id')});
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
		        
		        $moreresults = $("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						tsMocjDep.loadData(params);
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
			id: 'windowNewtsMocj',
			title: 'Nuevo movimiento de Caja Chica',
			contentURL: 'ts/mocj/edit',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 250,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					if($.cookie('action')!='tsMocjDep'){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe regresar a la bandeja Movimientos de Caja Chica para seleccionar la Caja correspondiente!',type: 'error'});
					}
					var data = {
						caja_chica: {
							_id: $mainPanel.find('[name=caja] option:selected').val(),
							nomb: $mainPanel.find('[name=caja] option:selected').html()
						},
						cod: $mainPanel.find('[name=numero] option:selected').attr('num'),
						saldo: $mainPanel.find('[name=numero] option:selected').val(),
						documento: p.$w.find('[name=doc] option:selected').val(),
						num_doc: p.$w.find('[name=num]').val(),
						concepto: p.$w.find('[name=conc_g]').val(),
						organizacion: p.$w.find('[name=orga]').data('data'),
						monto: p.$w.find('[name=monto]').val(),
						fecreg: p.$w.find('[name=fecreg]').val(),
						beneficiario: p.$w.find('[name=beneficiario]').data('data'),
						clasificador: p.$w.find('[name=clasif]').data('data'),
						cuenta: p.$w.find('[name=cuenta]').data('data')
					};
					if(data.num_doc==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.concepto==''){
						p.$w.find('[name=conc_g]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un concepto!',type: 'error'});
					}
					if(data.organizacion==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = {
							_id: data.organizacion._id.$id,
							nomb: data.organizacion.nomb,
							actividad: {
								_id: data.organizacion.actividad._id.$id,
								cod: data.organizacion.actividad.cod,
								nomb: data.organizacion.actividad.nomb
							},
							componente: {
								_id: data.organizacion.componente._id.$id,
								cod: data.organizacion.componente.cod,
								nomb: data.organizacion.componente.nomb
							}
						};
					}
					if(data.monto==''){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
					}
					if(data.beneficiario==null){
						p.$w.find('[name=btnEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad!',type: 'error'});
					}else data.beneficiario = ciHelper.enti.dbRel(data.beneficiario);
					if(data.clasificador==null){
						p.$w.find('[name=btnClas]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un clasificador!',type: 'error'});
					}else{
						data.clasificador = {
							_id: data.clasificador._id.$id,
							nomb: data.clasificador.nomb,
							cod: data.clasificador.cod
						};
					}
					if(data.cuenta==null){
						/*p.$w.find('[name=btnCta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta!',type: 'error'});*/
					}else{
						data.cuenta = {
							_id: data.cuenta._id.$id,
							descr: data.cuenta.descr,
							cod: data.cuenta.cod
						};
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/mocj/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El movimiento fue registrado con &eacute;xito!'});
						if($.cookie('action')=='tsMocjDep') $mainPanel.find('[name=numero]').change();
						else $('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewtsMocj');
				K.block({$element: p.$w});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnClas]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
							p.$w.find('[name=clasif]').html(data.cod).data('data',data);
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						/*if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{*/
							p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
						//}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=beneficiario]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrEnti]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}})
					.after('&nbsp;<button name="btnAgrEnti">Agregar</button>');
				p.$w.find('[name=btnAgrEnti]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.$w.find('[name=beneficiario]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrEnti]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}}).css('float','left');
				/*p.$w.find('[name=num]').val(1).numeric().spinner({step: 1,min: 1})
				.parent().find('.ui-button').css('height','14px');*/
				p.$w.find('[name=num]').attr('size',10);
				p.$w.find('[name=monto]').val(1).numeric().spinner({step: 1,min: 1})
				.parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=fecreg]').datepicker({ dateFormat: "dd-mm-yy" });
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({
			id: 'windowEdittsMocj',
			title: 'Editar movimiento de Caja Chica',
			contentURL: 'ts/mocj/edit',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 250,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					if($.cookie('action')!='tsMocjDep'){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe regresar a la bandeja Movimientos de Caja Chica para seleccionar la Caja correspondiente!',type: 'error'});
					}
					var data = {
						_id: p.id,
						caja_chica: {
							_id: $mainPanel.find('[name=caja] option:selected').val(),
							nomb: $mainPanel.find('[name=caja] option:selected').html()
						},
						cod: $mainPanel.find('[name=numero] option:selected').attr('num'),
						saldo: $mainPanel.find('[name=numero] option:selected').val(),
						documento: p.$w.find('[name=doc] option:selected').val(),
						num_doc: p.$w.find('[name=num]').val(),
						concepto: p.$w.find('[name=conc_g]').val(),
						fecreg: p.$w.find('[name=fecreg]').val(),
						organizacion: p.$w.find('[name=orga]').data('data'),
						monto: p.$w.find('[name=monto]').val(),
						beneficiario: p.$w.find('[name=beneficiario]').data('data'),
						clasificador: p.$w.find('[name=clasif]').data('data'),
						cuenta: p.$w.find('[name=cuenta]').data('data')
					};
					if(data.num_doc==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.concepto==''){
						p.$w.find('[name=conc_g]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un concepto!',type: 'error'});
					}
					if(data.organizacion==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = {
							_id: data.organizacion._id.$id,
							nomb: data.organizacion.nomb,
							actividad: {
								_id: data.organizacion.actividad._id.$id,
								cod: data.organizacion.actividad.cod,
								nomb: data.organizacion.actividad.nomb
							},
							componente: {
								_id: data.organizacion.componente._id.$id,
								cod: data.organizacion.componente.cod,
								nomb: data.organizacion.componente.nomb
							}
						};
					}
					if(data.monto==''){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
					}
					if(data.beneficiario==null){
						p.$w.find('[name=btnEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad!',type: 'error'});
					}else data.beneficiario = ciHelper.enti.dbRel(data.beneficiario);
					if(data.clasificador==null){
						p.$w.find('[name=btnClas]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un clasificador!',type: 'error'});
					}else{
						data.clasificador = {
							_id: data.clasificador._id.$id,
							nomb: data.clasificador.nomb,
							cod: data.clasificador.cod
						};
					}
					if(data.cuenta==null){
						/*p.$w.find('[name=btnCta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta!',type: 'error'});*/
					}else{
						data.cuenta = {
							_id: data.cuenta._id.$id,
							descr: data.cuenta.descr,
							cod: data.cuenta.cod
						};
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/mocj/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El movimiento fue actualizado con &eacute;xito!'});
						if($.cookie('action')=='tsMocjDep') $mainPanel.find('[name=numero]').change();
						else $('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEdittsMocj');
				K.block({$element: p.$w});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnClas]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
							p.$w.find('[name=clasif]').html(data.cod).data('data',data);
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						/*if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{*/
							p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
						//}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=beneficiario]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrEnti]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}})
					.after('&nbsp;<button name="btnAgrEnti">Agregar</button>');
				p.$w.find('[name=btnAgrEnti]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.$w.find('[name=beneficiario]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrEnti]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}}).css('float','left');
				/*p.$w.find('[name=num]').val(1).numeric().spinner({step: 1,min: 1})
				.parent().find('.ui-button').css('height','14px');*/
				p.$w.find('[name=num]').attr('size',10);
				p.$w.find('[name=monto]').val(1).numeric().spinner({step: 1,min: 1})
				.parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=fecreg]').datepicker({ dateFormat: "dd-mm-yy" });
				$.post('ts/mocj/get',{id: p.id},function(data){
					p.$w.find('[name=doc]').selectVal(data.documento);
					p.$w.find('[name=num]').val(data.num_doc);
					p.$w.find('[name=fecreg]').val(ciHelper.dateFormatBDNotHour(data.fecreg));
					p.$w.find('[name=beneficiario]').html(ciHelper.enti.formatName(data.beneficiario)).data('data',data.beneficiario);
					p.$w.find('[name=conc_g]').val(data.concepto);
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=monto]').val(data.monto);
					p.$w.find('[name=clasif]').html(data.clasificador.cod).data('data',data.clasificador);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowRendir: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewRendi',
			title: 'Rendici&oacute;n del Fondo para Pagos en Efectivo',
			contentURL: 'ts/mocj/rendi',
			icon: 'ui-icon-document',
			width: 650,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						caja: p.data[0].caja_chica._id.$id,
						saldo: p.data[0]._id.$id,
						patri: [],
						presu: []
					},
					debe = 0,haber = 0;
					if(p.data[0].afectacion==null||p.data[0].afectacion.length==0){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se puede realizar una rendici&oacute;n sin movimientos de la caja chica!',type: 'error'});
					}
					if(p.$w.find('.gridBody:eq(3) .item').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar data para Contabilidad Patrimonial!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.gridBody:eq(3) .item').length; i<j; i++){
						var tmp = p.$w.find('.gridBody:eq(3) .item').eq(i).data('data');
						data.patri.push({
							cuenta: {
								_id: tmp.cuenta._id.$id,
								cod: tmp.cuenta.cod,
								descr: tmp.cuenta.descr
							},
							tipo: tmp.tipo,
							monto: tmp.importe
						});
						if(tmp.tipo=='D') debe += parseFloat(tmp.importe);
						else haber += parseFloat(tmp.importe);
					}
					if(debe!=haber){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total de Haberes no coincide con el total de Debes en la data para la Contabilidad Patrimonial!',type: 'error'});
					}
					debe = 0;
					haber = 0;
					if(p.$w.find('.gridBody:eq(4) .item').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar data para Contabilidad Presupuestal',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.gridBody:eq(4) .item').length; i<j; i++){
						var tmp = p.$w.find('.gridBody:eq(4) .item').eq(i).data('data');
						data.presu.push({
							cuenta: {
								_id: tmp.cuenta._id.$id,
								cod: tmp.cuenta.cod,
								descr: tmp.cuenta.descr
							},
							tipo: tmp.tipo,
							monto: tmp.importe
						});
						if(tmp.tipo=='D') debe += parseFloat(tmp.importe);
						else haber += parseFloat(tmp.importe);
					}
					if(debe!=haber){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total de Haberes no coincide con el total de Debes en la data para la Contabilidad Presupuestal!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/mocj/save_rendi',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La rendici&oacute;n fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewRendi');
				K.block({$element: p.$w});
				p.$w.find('[name=importe]').val(0).numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnCuenta]').click(function(){
					var $table = $(this).closest('fieldset');
					ctPcon.windowSelect({callback: function(data){
						$table.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('fieldset:eq(2) td:eq(3),fieldset:eq(3) td:eq(3)').buttonset();
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('.payment').eq(3).bind('scroll',function(){
					p.$w.find('.payment').eq(2).scrollLeft(p.$w.find('.payment').eq(3).scrollLeft());
				});
				p.$w.find('.payment').eq(5).bind('scroll',function(){
					p.$w.find('.payment').eq(4).scrollLeft(p.$w.find('.payment').eq(5).scrollLeft());
				});
				p.$w.find('.payment').eq(7).bind('scroll',function(){
					p.$w.find('.payment').eq(6).scrollLeft(p.$w.find('.payment').eq(7).scrollLeft());
				});
				p.$w.find('[name=btnAgre]').click(function(){
					var $table = $(this).closest('fieldset'),
					tmp = {
						cuenta: $table.find('[name=cuenta]').data('data'),
						tipo: $table.find('[name^=rbtnPoli]:checked').val(),
						importe: $table.find('[name=importe]').val()
					};
					if(tmp.cuenta==null){
						$table.find('[name=btnCuenta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else if(tmp.importe==''){
						$table.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					var $row = $table.find('.gridReference').clone();
					$row.find('li:eq(0)').html(tmp.cuenta.cod);
					$row.find('li:eq(1)').html(tmp.cuenta.descr);
					if(tmp.tipo=='D') $row.find('li:eq(2)').html(ciHelper.formatMon(tmp.importe));
					else $row.find('li:eq(3)').html(ciHelper.formatMon(tmp.importe));
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('.item').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('.item').data('data',tmp);
		        	$table.find(".gridBody").append( $row.children() );
		        	$table.find('[name=cuenta]').html('').removeData('data');
					$table.find('[name=importe]').val(0);
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$.post('ts/mocj/get_rendi','id='+$mainPanel.find('[name=caja] option:selected').val(),function(data){
					p.data = data.saldos;
					if(data.saldos[0].afectacion==null) data.saldos[0].afectacion = [];
					if(data.movs==null) data.movs = [];
					var $total = p.$w.find('.grid:eq(2)'),
					$ul_orga = p.$w.find('[name=ul_orga]'),
					$table_afec = p.$w.find('fieldset:eq(1)'),
					width = data.saldos[0].afectacion.length * 200;
					if(data.saldos.length==1){
						p.$w.find('.gridBody:eq(1) ul:eq(0),.gridBody:eq(1) ul:eq(1),.gridBody:eq(1) ul:eq(2)').hide();
					}else{
						$total.find('ul:eq(0) li:eq(1)').html(ciHelper.formatMon(data.saldos[1].gasto));
						$total.find('ul:eq(1) li:eq(1)').html(ciHelper.formatMon(data.saldos[1].saldo));
						$total.find('ul:eq(2) li:eq(1)').html(ciHelper.formatMon(data.saldos[1].monto));
					}
					p.$w.find('[name=num]').html( data.saldos[0].cod );
					p.$w.find('[name=caja]').html( data.saldos[0].caja_chica.nomb );
					p.total_act = 0;
					for (var i=0,j=data.movs.length; i<j; i++) {
						var result = data.movs[i];
						var $row = p.$w.find('.gridReference:first').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.item );
						$li.eq(1).html( ciHelper.dateFormat(result.fecreg) );
						$li.eq(2).html( tsMocjDep.types[result.documento] );
						$li.eq(3).html( result.num_doc );
						$li.eq(4).html( ciHelper.enti.formatName(result.beneficiario) );
						$li.eq(5).html( result.concepto );
						$li.eq(6).html( result.organizacion.nomb );
						$li.eq(7).html( ciHelper.formatMon(result.monto) );
						$li.eq(8).html( result.clasificador.cod );
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody:first").append( $row.children() );
			        	p.total_act += parseFloat(result.monto);
			        }
					var $row = p.$w.find('.gridReference:first').clone();
					$li = $('li',$row);
					$li.eq(0).html( 'Total' ).css('max-width','950px').css('min-width','950px').css('text-align','right')
					.addClass('ui-button ui-widget ui-state-default');
					$li.eq(8).html( ciHelper.formatMon(p.total_act) );
					$row.find('li:eq(1),li:eq(2),li:eq(3),li:eq(4),li:eq(5),li:eq(6),li:eq(7)').remove();
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody:first").append( $row.children() );
					$total.find('ul:eq(3) li:eq(1)').html(ciHelper.formatMon(p.total_act));
					$total.find('ul:eq(4) li:eq(1)').html(ciHelper.formatMon(parseFloat(data.saldos[0].monto)-p.total_act));
					width = 0;
					for(var i=0,j=data.saldos[0].afectacion.length; i<j; i++){
						if($table_afec.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').length==0){
							var result = data.saldos[0].afectacion[i];
							$ul_orga.find('ul:last').append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">'+result.organizacion.actividad.nomb+'</li>');
							$ul_orga.find('li:last').data('data',result);
							$table_afec.find('.gridReference ul').append('<li style="max-width:200px;min-width:200px;" name="'+result.organizacion.actividad._id.$id+'"></li>');
							width += 200;
						}
					}
					//$ul_orga.css('max-width',width+'px').css('min-width',width+'px');
					$ul_orga.find('li:eq(0)').css('max-width',width+'px').css('min-width',width+'px');
					for(var i=0,j=data.saldos[0].afectacion.length; i<j; i++){
						var result = data.saldos[0].afectacion[i];
						for(var k=0,l=data.saldos[0].afectacion[i].gasto.length; k<l; k++){
							if($table_afec.find('[name='+data.saldos[0].afectacion[i].gasto[k].clasificador._id.$id+']').length<=0){
								var $row = $table_afec.find('.gridReference').clone();
								$row.find('li:eq(0)').html(data.saldos[0].afectacion[i].gasto[k].clasificador.cod);
								$row.find('li:eq(1)').html(data.saldos[0].afectacion[i].gasto[k].clasificador.nomb);
								$row.find('[name='+result.organizacion.actividad._id.$id+']').html(ciHelper.formatMon(data.saldos[0].afectacion[i].gasto[k].monto));
								$row.wrapInner('<a class="item" name="'+data.saldos[0].afectacion[i].gasto[k].clasificador._id.$id+'" />');
								$table_afec.find(".gridBody").append( $row.children() );
							}else{
								$table_afec.find('[name='+data.saldos[0].afectacion[i].gasto[k].clasificador._id.$id+'] [name='+result.organizacion.actividad._id.$id+']').html(ciHelper.formatMon(data.saldos[0].afectacion[i].gasto[k].monto));
							}
						}
					}
					var $row = $table_afec.find('.gridReference').clone();
					$row.find('li:eq(0)').html( 'Total' ).css('max-width','350px').css('min-width','350px').css('text-align','right')
					.addClass('ui-button ui-widget ui-state-default');
					$row.find('li:eq(1)').remove();
					for(var i=0,j=data.saldos[0].afectacion.length; i<j; i++){
						var anterior = 0;
						if($row.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').data('monto')!=null)
							anterior = parseFloat($row.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').data('monto'));
						var monto = parseFloat(data.saldos[0].afectacion[i].monto)+anterior;
						$row.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').html(ciHelper.formatMon(parseFloat(monto))).data('monto',monto);
					}
					$table_afec.find(".gridBody").append( $row.children() );
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowVer: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowVerRendi',
			title: 'Rendici&oacute;n del Fondo para Pagos en Efectivo',
			contentURL: 'ts/mocj/rendi',
			icon: 'ui-icon-document',
			width: 650,
			height: 410,
			buttons: {
				"Imprimir": function(){
					var url = 'ts/repo/rendi?id='+$mainPanel.find('[name=numero] option:selected').val();
					K.windowPrint({
						id:'windowTsRebdiRepo',
						title: "Reporte / Informe",
						url: url
					});
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowVerRendi');
				K.block({$element: p.$w});
				p.$w.find('fieldset:eq(2) td:eq(3),fieldset:eq(3) td:eq(3)').buttonset();
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('.payment').eq(3).bind('scroll',function(){
					p.$w.find('.payment').eq(2).scrollLeft(p.$w.find('.payment').eq(3).scrollLeft());
				});
				p.$w.find('.payment').eq(5).bind('scroll',function(){
					p.$w.find('.payment').eq(4).scrollLeft(p.$w.find('.payment').eq(5).scrollLeft());
				});
				p.$w.find('.payment').eq(7).bind('scroll',function(){
					p.$w.find('.payment').eq(6).scrollLeft(p.$w.find('.payment').eq(7).scrollLeft());
				});
				$.post('ts/mocj/get_rendi_custom','id='+$mainPanel.find('[name=numero] option:selected').val(),function(data){
					p.data = data.saldos;
					if(data.saldos[0].afectacion==null) data.saldos[0].afectacion = [];
					if(data.movs==null) data.movs = [];
					var $total = p.$w.find('.grid:eq(2)'),
					$ul_orga = p.$w.find('[name=ul_orga]'),
					$table_afec = p.$w.find('fieldset:eq(1)'),
					width = data.saldos[0].afectacion.length * 200;
					if(data.saldos.length==1){
						p.$w.find('.gridBody:eq(1) ul:eq(0),.gridBody:eq(1) ul:eq(1),.gridBody:eq(1) ul:eq(2)').hide();
					}else{
						$total.find('ul:eq(0) li:eq(1)').html(ciHelper.formatMon(data.saldos[1].gasto));
						$total.find('ul:eq(1) li:eq(1)').html(ciHelper.formatMon(data.saldos[1].saldo));
						$total.find('ul:eq(2) li:eq(1)').html(ciHelper.formatMon(data.saldos[1].monto));
					}
					p.$w.find('[name=num]').html( data.saldos[0].cod );
					p.$w.find('[name=caja]').html( data.saldos[0].caja_chica.nomb );
					p.total_act = 0;
					for (var i=0,j=data.movs.length; i<j; i++) {
						var result = data.movs[i];
						var $row = p.$w.find('.gridReference:first').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.item );
						$li.eq(1).html( ciHelper.dateFormat(result.fecreg) );
						$li.eq(2).html( tsMocjDep.types[result.documento] );
						$li.eq(3).html( result.num_doc );
						$li.eq(4).html( ciHelper.enti.formatName(result.beneficiario) );
						$li.eq(5).html( result.concepto );
						$li.eq(6).html( result.organizacion.nomb );
						$li.eq(7).html( ciHelper.formatMon(result.monto) );
						$li.eq(8).html( result.clasificador.cod );
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody:first").append( $row.children() );
			        	p.total_act += parseFloat(result.monto);
			        }
					var $row = p.$w.find('.gridReference:first').clone();
					$li = $('li',$row);
					$li.eq(0).html( 'Total' ).css('max-width','950px').css('min-width','950px').css('text-align','right')
					.addClass('ui-button ui-widget ui-state-default');
					$li.eq(8).html( ciHelper.formatMon(p.total_act) );
					$row.find('li:eq(1),li:eq(2),li:eq(3),li:eq(4),li:eq(5),li:eq(6),li:eq(7)').remove();
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody:first").append( $row.children() );
					$total.find('ul:eq(3) li:eq(1)').html(ciHelper.formatMon(p.total_act));
					$total.find('ul:eq(4) li:eq(1)').html(ciHelper.formatMon(parseFloat(data.saldos[0].monto)-p.total_act));
					width = 0;
					for(var i=0,j=data.saldos[0].afectacion.length; i<j; i++){
						if($table_afec.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').length==0){
							var result = data.saldos[0].afectacion[i];
							$ul_orga.find('ul:last').append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">'+result.organizacion.actividad.nomb+'</li>');
							$ul_orga.find('li:last').data('data',result);
							$table_afec.find('.gridReference ul').append('<li style="max-width:200px;min-width:200px;" name="'+result.organizacion.actividad._id.$id+'"></li>');
							width += 200;
						}
					}
					//$ul_orga.css('max-width',width+'px').css('min-width',width+'px');
					$ul_orga.find('li:eq(0)').css('max-width',width+'px').css('min-width',width+'px');
					for(var i=0,j=data.saldos[0].afectacion.length; i<j; i++){
						var result = data.saldos[0].afectacion[i];
						for(var k=0,l=data.saldos[0].afectacion[i].gasto.length; k<l; k++){
							if($table_afec.find('[name='+data.saldos[0].afectacion[i].gasto[k].clasificador._id.$id+']').length<=0){
								var $row = $table_afec.find('.gridReference').clone();
								$row.find('li:eq(0)').html(data.saldos[0].afectacion[i].gasto[k].clasificador.cod);
								$row.find('li:eq(1)').html(data.saldos[0].afectacion[i].gasto[k].clasificador.nomb);
								$row.find('[name='+result.organizacion.actividad._id.$id+']').html(ciHelper.formatMon(data.saldos[0].afectacion[i].gasto[k].monto));
								$row.wrapInner('<a class="item" name="'+data.saldos[0].afectacion[i].gasto[k].clasificador._id.$id+'" />');
								$table_afec.find(".gridBody").append( $row.children() );
							}else{
								$table_afec.find('[name='+data.saldos[0].afectacion[i].gasto[k].clasificador._id.$id+'] [name='+result.organizacion.actividad._id.$id+']').html(ciHelper.formatMon(data.saldos[0].afectacion[i].gasto[k].monto));
							}
						}
					}
					var $row = $table_afec.find('.gridReference').clone();
					$row.find('li:eq(0)').html( 'Total' ).css('max-width','350px').css('min-width','350px').css('text-align','right')
					.addClass('ui-button ui-widget ui-state-default');
					$row.find('li:eq(1)').remove();
					for(var i=0,j=data.saldos[0].afectacion.length; i<j; i++){
						var anterior = 0;
						if($row.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').data('monto')!=null)
							anterior = parseFloat($row.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').data('monto'));
						var monto = parseFloat(data.saldos[0].afectacion[i].monto)+anterior;
						$row.find('[name='+data.saldos[0].afectacion[i].organizacion.actividad._id.$id+']').html(ciHelper.formatMon(parseFloat(monto))).data('monto',monto);
					}
					$table_afec.find(".gridBody").append( $row.children() );
					p.$w.find('fieldset:eq(2) table,fieldset:eq(3) table').remove();
					p.$w.find('fieldset:eq(2) .gridHeader li:last,fieldset:eq(3) .gridHeader li:last').remove();
					p.$w.find('fieldset:eq(2) .gridReference li:last,fieldset:eq(3) .gridReference li:last').remove();
					for(var i=0,j=data.saldos[0].cont_patrimonial.length; i<j; i++){
						var $row = p.$w.find('fieldset:eq(2) .gridReference').clone(),
						$li = $row.find('li'),
						result = data.saldos[0].cont_patrimonial[i];
						$li.eq(0).html( result.cuenta.cod );
						$li.eq(1).html( result.cuenta.descr );
						if(result.tipo=='D') $li.eq(2).html( ciHelper.formatMon(result.monto) );
						else $li.eq(3).html( ciHelper.formatMon(result.monto) );
						$row.wrapInner('<a class="item">');
						p.$w.find('fieldset:eq(2) .gridBody').append($row.children());
					}
					for(var i=0,j=data.saldos[0].cont_presupuestal.length; i<j; i++){
						var $row = p.$w.find('fieldset:eq(3) .gridReference').clone(),
						$li = $row.find('li'),
						result = data.saldos[0].cont_presupuestal[i];
						$li.eq(0).html( result.cuenta.cod );
						$li.eq(1).html( result.cuenta.descr );
						if(result.tipo=='D') $li.eq(2).html( ciHelper.formatMon(result.monto) );
						else $li.eq(3).html( ciHelper.formatMon(result.monto) );
						$row.wrapInner('<a class="item">');
						p.$w.find('fieldset:eq(3) .gridBody').append($row.children());
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};