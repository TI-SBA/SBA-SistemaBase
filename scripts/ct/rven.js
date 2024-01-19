/*******************************************************************************
Registro de Ventas */
ctRven = {
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctRven',
			titleBar: {
				title: 'Registro de Ventas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/rven',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'registro(s) de ventas' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctRven.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnCerrar]').click(function(){
					K.sendingInfo();
					$.post('ct/rven/cerrar',{
						mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						ano: $mainPanel.find('[name=periodo]').data('ano')
					},function(){
						K.clearNoti();
						K.notification({title: 'Periodo Cerrado',text: 'El cierre ha sido realizado con &eacute;xito!'});
						$mainPanel.find('[name=periodo]').change();
					});
				}).button({icons: {primary: 'ui-icon-circle-close'}});
				var date = new Date();
				$mainPanel.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        },
			        onChangeMonthYear: function(year,month,inst){
			            $(this).data('mes',month-1).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month-1, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[date.getMonth()]+' '+date.getFullYear())
			    .data('mes',+date.getMonth())
			    .data('ano',date.getFullYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
			    	ctRven.loadData({page: 1,url: 'ct/rven/lista'});
			    }).change();
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
			ano: $mainPanel.find('[name=periodo]').data('ano')
		});
	    $.post(params.url, params, function(data){
			if ( data.items!=null ) {
				for (i=0; i < data.items.length; i++) {
					result = data.items[i];
					if(result.estado_registro=='C') $mainPanel.find('[name^=btn]').hide();
					else $mainPanel.find('[name^=btn]').show();
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( result.num_correlativo );
					$li.eq(2).html( ciHelper.dateFormat(result.fecemi) );
					$li.eq(3).html( ciHelper.dateFormat(result.fecven) );
					$li.eq(4).html( result.tipo_comprobante.descr );
					$li.eq(5).html( result.serie_comprobante );
					$li.eq(6).html( result.num_comprobante );
					$li.eq(7).html( result.tipo_doc );
					$li.eq(8).html( result.num_doc );
					$li.eq(8).html( ciHelper.enti.formatName(result.proveedor) );
					$li.eq(10).html( ciHelper.formatMon(result.ticket) );
					$li.eq(11).html( ciHelper.formatMon(result.valor_facturado) );
					$li.eq(12).html( result.bi );
					$li.eq(13).html( ciHelper.formatMon(result.importe_exonerada) );
					$li.eq(14).html( ciHelper.formatMon(result.importe_inafecta) );
					$li.eq(15).html( result.isc );
					$li.eq(16).html( result.igv );
					$li.eq(17).html( ciHelper.formatMon(result.otros_tributos) );
					$li.eq(18).html( ciHelper.formatMon(result.importe_total) );
					$li.eq(19).html( result.bi_arroz );
					$li.eq(20).html( result.impuesto_arroz );
					$li.eq(21).html( result.tc );
					$li.eq(22).html( ciHelper.dateFormat(result.fecemi_mod) );
					$li.eq(23).html( result.tipo_doc_mod.descr );
					$li.eq(24).html( result.ser_doc_mod );
					$li.eq(25).html( result.num_doc_mod );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						ctRven.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(1)').html()});
					}).data('estado',result.estado_registro).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								if(K.tmp.data('estado')=='C') $('#conMenListEd_edi',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									ctRven.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(1)').html()});
								},
								'conMenListEd_edi': function(t) {
									ctRven.windowEdit({id: K.tmp.data('id')});
								}
							}
						});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
	      } else {
	    	  $mainPanel.find('[name^=btn]').show();
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
			id: 'windowNewRven',
			title: 'Nuevo Registro de Venta',
			contentURL: 'ct/rven/edit',
			icon: 'ui-icon-plusthick',
			width: 650,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						periodo: p.periodo,
						num_correlativo: p.cod,
						fecemi: p.$w.find('[name=fecemi]').val(),
						fecven: p.$w.find('[name=fecven]').val(),
						tipo_comprobante: p.$w.find('[name=tipo_doc] option:selected').data('data'),
						serie_comprobante: p.$w.find('[name=serie]').val(),
						num_comprobante: p.$w.find('[name=num]').val(),
						ticket: p.$w.find('[name=ticket]').val(),
						tipo_doc: p.$w.find('[name=tdoc] option:selected').val(),
						num_doc: p.$w.find('[name=docident]').html(),
						proveedor: p.$w.find('[name=proveedor]').data('data'),
						valor_facturado: p.$w.find('[name=valor_facturado]').val(),
						bi: p.$w.find('[name=bi]').val(),
						importe_exonerada: p.$w.find('[name=importe_exonerada]').val(),
						importe_inafecta: p.$w.find('[name=importe_inafecta]').val(),
						isc: p.$w.find('[name=isc]').val(),
						igv: p.$w.find('[name=igv]').val(),
						bi_arroz: p.$w.find('[name=bi_arroz]').val(),
						impuesto_arroz: p.$w.find('[name=impuesto_arroz]').val(),
						otros_tributos: p.$w.find('[name=otros_tributos]').val(),
						importe_total: p.$w.find('[name=importe_total]').val(),
						tc: p.$w.find('[name=tc]').val(),
						estado: p.$w.find('[name=rbtnEst]:checked').val(),
						fecemi_mod: p.$w.find('[name=fecemi_mod]').val(),
						tipo_doc_mod: p.$w.find('[name=tipo_doc_mod] option:selected').data('data'),
						ser_doc_mod: p.$w.find('[name=ser_doc_mod]').val(),
						num_doc_mod: p.$w.find('[name=num_doc_mod]').val()
					};
					data.tipo_comprobante = {
						_id: data.tipo_comprobante._id.$id,
						cod: data.tipo_comprobante.cod,
						descr: data.tipo_comprobante.descr
					};
					data.tipo_doc_mod = {
						_id: data.tipo_doc_mod._id.$id,
						cod: data.tipo_doc_mod.cod,
						descr: data.tipo_doc_mod.descr
					};
					if(data.proveedor==null){
						p.$w.find('[name=btnProv]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
					}else data.proveedor = ciHelper.enti.dbRel(data.proveedor);
					if(p.$w.find('[name=docident]').html()=='--'){
						p.$w.find('[name=tdoc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Tipo de Documento de Identidad v&aacute;lido!',type: 'error'});
					}
					if(data.fecemi==''){
						p.$w.find('[name=fecemi]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de emisi&oacute;n!',type: 'error'});
					}
					/*if(data.fecven==''){
						p.$w.find('[name=fecven]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
					}*/
					if(data.serie_comprobante==''){
						p.$w.find('[name=serie]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una serie!',type: 'error'});
					}
					if(data.num_comprobante==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.ticket==''){
						p.$w.find('[name=ticket]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					if(data.valor_facturado==''){
						p.$w.find('[name=valor_facturado]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor facturado!',type: 'error'});
					}
					if(data.bi==''){
						p.$w.find('[name=bi]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una base imponible de la operaci&oacute;n gravada!',type: 'error'});
					}
					if(data.importe_exonerada==''){
						p.$w.find('[name=importe_exonerada]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe exonerado!',type: 'error'});
					}
					if(data.importe_inafecta==''){
						p.$w.find('[name=importe_inafecta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe inafecto!',type: 'error'});
					}
					if(data.isc==''){
						p.$w.find('[name=isc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un ISC!',type: 'error'});
					}
					if(data.igv==''){
						p.$w.find('[name=igv]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV!',type: 'error'});
					}
					if(data.bi_arroz==''){
						p.$w.find('[name=bi_arroz]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una base imponible (arroz)!',type: 'error'});
					}
					if(data.impuesto_arroz==''){
						p.$w.find('[name=impuesto_arroz]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un impuesto al arroz!',type: 'error'});
					}
					if(data.otros_tributos==''){
						p.$w.find('[name=otros_tributos]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar otros tributos!',type: 'error'});
					}
					if(data.importe_total==''){
						p.$w.find('[name=importe_total]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe total!',type: 'error'});
					}
					if(data.tc==''){
						p.$w.find('[name=tc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo de cambio!',type: 'error'});
					}
					if(data.fecemi_mod==''){
						p.$w.find('[name=fecemi_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha!',type: 'error'});
					}
					if(data.ser_doc_mod==''){
						p.$w.find('[name=ser_doc_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una serie!',type: 'error'});
					}
					if(data.num_doc_mod==''){
						p.$w.find('[name=num_doc_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/rven/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Registro de Venta fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewRven');
				K.block({$element: p.$w});
				p.$w.find('[name=fecemi],[name=fecven],[name=fecemi_mod]').datepicker();
				p.$w.find('[name=ticket],[name=valor_facturado],[name=bi],[name=importe_exonerada],[name=importe_inafecta],[name=isc],[name=igv],[name=bi_arroz],[name=impuesto_arroz],[name=otros_tributos],[name=importe_total],[name=tc]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnProv]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnProv]').button('option','text',false);
						if(data.docident[0].tipo=='DNI'){
							p.$w.find('[name=tdoc]').selectVal('1');
							p.$w.find('[name=tdoc]').change();
						}else if(data.docident[0].tipo=='RUC'){
							p.$w.find('[name=tdoc]').selectVal('6');
							p.$w.find('[name=tdoc]').change();
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=tdoc]').change(function(){
					var tdocs = p.$w.find('[name=proveedor]').data('data').docident,
					val = $(this).find('option:selected').val();
					for(var i=0,j=tdocs.length; i<j; i++){
						if(tdocs[i].tipo=='RUC'&&val=='6'){
							p.$w.find('[name=docident]').html(tdocs[i].num);
							i = j;
						}else if(tdocs[i].tipo=='DNI'&&val=='1'){
							p.$w.find('[name=docident]').html(tdocs[i].num);
							i = j;
						}else
							p.$w.find('[name=docident]').html('--');
					}
				});
				p.$w.find('fieldset:eq(3) div:first').buttonset();
				$.post('ct/rven/get_edit',{
					mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
					ano: $mainPanel.find('[name=periodo]').data('ano')
				},function(data){
					p.cod = data.cod;
					p.periodo = data.periodo;
					if(data.tico==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay Tipos de Comprobante de Pago!',type: 'error'});
					}else{
						var $select = p.$w.find('[name^=tipo_doc]');
						for(var i=0,j=data.tico.length; i<j; i++){
							$select.append('<option value="'+data.tico[i]._id.$id+'">'+data.tico[i].cod+' - '+data.tico[i].descr+'</option>');
							$select.find('option:last').data('data',data.tico[i]);
						}
					}
					p.$w.find('[name=periodo]').html(data.periodo);
					p.$w.find('[name=cod]').html(data.cod);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditRven'+p.id,
			title: 'Editar Registro de Venta: '+p.nomb,
			contentURL: 'ct/rven/edit',
			icon: 'ui-icon-pencil',
			width: 650,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						fecemi: p.$w.find('[name=fecemi]').val(),
						fecven: p.$w.find('[name=fecven]').val(),
						tipo_comprobante: p.$w.find('[name=tipo_doc] option:selected').data('data'),
						serie_comprobante: p.$w.find('[name=serie]').val(),
						num_comprobante: p.$w.find('[name=num]').val(),
						ticket: p.$w.find('[name=ticket]').val(),
						tipo_doc: p.$w.find('[name=tdoc] option:selected').val(),
						num_doc: p.$w.find('[name=docident]').html(),
						proveedor: p.$w.find('[name=proveedor]').data('data'),
						valor_facturado: p.$w.find('[name=valor_facturado]').val(),
						bi: p.$w.find('[name=bi]').val(),
						importe_exonerada: p.$w.find('[name=importe_exonerada]').val(),
						importe_inafecta: p.$w.find('[name=importe_inafecta]').val(),
						isc: p.$w.find('[name=isc]').val(),
						igv: p.$w.find('[name=igv]').val(),
						bi_arroz: p.$w.find('[name=bi_arroz]').val(),
						impuesto_arroz: p.$w.find('[name=impuesto_arroz]').val(),
						otros_tributos: p.$w.find('[name=otros_tributos]').val(),
						importe_total: p.$w.find('[name=importe_total]').val(),
						tc: p.$w.find('[name=tc]').val(),
						estado: p.$w.find('[name=rbtnEst]:checked').val(),
						fecemi_mod: p.$w.find('[name=fecemi_mod]').val(),
						tipo_doc_mod: p.$w.find('[name=tipo_doc_mod] option:selected').data('data'),
						ser_doc_mod: p.$w.find('[name=ser_doc_mod]').val(),
						num_doc_mod: p.$w.find('[name=num_doc_mod]').val()
					};
					data.tipo_comprobante = {
						_id: data.tipo_comprobante._id.$id,
						cod: data.tipo_comprobante.cod,
						descr: data.tipo_comprobante.descr
					};
					data.tipo_doc_mod = {
						_id: data.tipo_doc_mod._id.$id,
						cod: data.tipo_doc_mod.cod,
						descr: data.tipo_doc_mod.descr
					};
					if(data.proveedor==null){
						p.$w.find('[name=btnProv]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un proveedor!',type: 'error'});
					}else data.proveedor = ciHelper.enti.dbRel(data.proveedor);
					if(data.fecemi==''){
						p.$w.find('[name=fecemi]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de emisi&oacute;n!',type: 'error'});
					}
					if(data.fecven==''){
						p.$w.find('[name=fecven]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
					}
					if(data.serie_comprobante==''){
						p.$w.find('[name=serie]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una serie!',type: 'error'});
					}
					if(data.num_comprobante==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.ticket==''){
						p.$w.find('[name=ticket]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					if(data.valor_facturado==''){
						p.$w.find('[name=valor_facturado]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor facturado!',type: 'error'});
					}
					if(data.bi==''){
						p.$w.find('[name=bi]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una base imponible de la operaci&oacute;n gravada!',type: 'error'});
					}
					if(data.importe_exonerada==''){
						p.$w.find('[name=importe_exonerada]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe exonerado!',type: 'error'});
					}
					if(data.importe_inafecta==''){
						p.$w.find('[name=importe_inafecta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe inafecto!',type: 'error'});
					}
					if(data.isc==''){
						p.$w.find('[name=isc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un ISC!',type: 'error'});
					}
					if(data.igv==''){
						p.$w.find('[name=igv]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV!',type: 'error'});
					}
					if(data.bi_arroz==''){
						p.$w.find('[name=bi_arroz]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una base imponible (arroz)!',type: 'error'});
					}
					if(data.impuesto_arroz==''){
						p.$w.find('[name=impuesto_arroz]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un impuesto al arroz!',type: 'error'});
					}
					if(data.otros_tributos==''){
						p.$w.find('[name=otros_tributos]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar otros tributos!',type: 'error'});
					}
					if(data.importe_total==''){
						p.$w.find('[name=importe_total]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe total!',type: 'error'});
					}
					if(data.tc==''){
						p.$w.find('[name=tc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo de cambio!',type: 'error'});
					}
					if(data.fecemi_mod==''){
						p.$w.find('[name=fecemi_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha!',type: 'error'});
					}
					if(data.ser_doc_mod==''){
						p.$w.find('[name=ser_doc_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una serie!',type: 'error'});
					}
					if(data.num_doc_mod==''){
						p.$w.find('[name=num_doc_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/rven/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Registro de Venta fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditRven'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=fecemi],[name=fecven],[name=fecemi_mod]').datepicker();
				p.$w.find('[name=ticket],[name=valor_facturado],[name=bi],[name=importe_exonerada],[name=importe_inafecta],[name=isc],[name=igv],[name=bi_arroz],[name=impuesto_arroz],[name=otros_tributos],[name=importe_total],[name=tc]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnProv]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						var ruc = false;
						for(var i=0,j=data.docident.length; i<j; i++){
							if(data.docident[i].tipo=='RUC') ruc = data.docident[i].num;
						}
						if(ruc==false){
							K.notification({title: 'RUC faltante',text: 'Debe agregar un RUC para la entidad seleccionada!',type: 'error'});
							ciEdit.windowAddDocEnti({
								doc: 'RUC',
								entidad: data,
								callback: function(doc){
									p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data)).data('data',data);
									p.$w.find('[name=docident]').html(doc.num);
									p.$w.find('[name=btnProv]').button('option','text',false);
								}
							});
						}else{
							p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data)).data('data',data);
							p.$w.find('[name=docident]').html(ruc);
							p.$w.find('[name=btnProv]').button('option','text',false);
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('fieldset:eq(3) div:first').buttonset();
				$.post('ct/rven/get_edit',function(data){
					p.cod = data.cod;
					p.periodo = data.periodo;
					if(data.tico==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay Tipos de Comprobante de Pago!',type: 'error'});
					}else{
						var $select = p.$w.find('[name^=tipo_doc]');
						for(var i=0,j=data.tico.length; i<j; i++){
							$select.append('<option value="'+data.tico[i]._id.$id+'">'+data.tico[i].cod+' - '+data.tico[i].descr+'</option>');
							$select.find('option:last').data('data',data.tico[i]);
						}
					}
					p.$w.find('[name=periodo]').html(data.periodo);
					p.$w.find('[name=cod]').html(data.cod);
					$.post('ct/rven/get','id='+p.id,function(data){
						p.cod = data.num_correlativo;
						p.periodo = data.periodo;
						p.$w.find('[name=periodo]').html(data.periodo);
						p.$w.find('[name=cod]').html(data.num_correlativo);
						p.$w.find('[name=fecemi]').val(ciHelper.dateFormatBDNotHour(data.fecemi));
						p.$w.find('[name=fecven]').val(ciHelper.dateFormatBDNotHour(data.fecven));
						p.$w.find('[name=tipo_doc]').selectVal(data.tipo_comprobante._id.$id);
						p.$w.find('[name=serie]').val(data.serie_comprobante);
						p.$w.find('[name=num]').val(data.num_comprobante);
						p.$w.find('[name=ticket]').val(data.ticket);
						p.$w.find('[name=tdoc]').selectVal(data.tipo_doc);
						p.$w.find('[name=docident]').html(data.num_doc);
						p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data.proveedor)).data('data',data.proveedor);
						p.$w.find('[name=valor_facturado]').val(data.valor_facturado);
						p.$w.find('[name=bi]').val(data.bi);
						p.$w.find('[name=importe_exonerada]').val(data.importe_exonerada);
						p.$w.find('[name=importe_inafecta]').val(data.importe_inafecta);
						p.$w.find('[name=isc]').val(data.isc);
						p.$w.find('[name=igv]').val(data.igv);
						p.$w.find('[name=bi_arroz]').val(data.bi_arroz);
						p.$w.find('[name=impuesto_arroz]').val(data.impuesto_arroz);
						p.$w.find('[name=otros_tributos]').val(data.otros_tributos);
						p.$w.find('[name=importe_total]').val(data.importe_total);
						p.$w.find('[name=tc]').val(data.tc);
						p.$w.find('fieldset:eq(3) div:first [value='+data.estado+']').attr('checked','checked');
						p.$w.find('[name=fecemi_mod]').val(ciHelper.dateFormatBDNotHour(data.fecemi_mod));
						p.$w.find('[name=tipo_doc_mod]').selectVal(data.tipo_doc_mod._id.$id);
						p.$w.find('[name=ser_doc_mod]').val(data.ser_doc_mod);
						p.$w.find('[name=num_doc_mod]').val(data.num_doc_mod);
						p.$w.find('fieldset:eq(3) div:first').buttonset();
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsRven'+p.id,
			title: 'Registro de Venta: '+p.nomb,
			contentURL: 'ct/rven/details',
			icon: 'ui-icon-pencil',
			width: 650,
			height: 410,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsRven'+p.id);
				K.block({$element: p.$w});
				$.post('ct/rven/get','id='+p.id,function(data){
					p.$w.find('[name=periodo]').html(data.periodo);
					p.$w.find('[name=cod]').html(data.num_correlativo);
					p.$w.find('[name=fecemi]').html(ciHelper.dateFormatBDNotHour(data.fecemi));
					p.$w.find('[name=fecven]').html(ciHelper.dateFormatBDNotHour(data.fecven));
					p.$w.find('[name=tipo_doc]').html(data.tipo_comprobante.descr);
					p.$w.find('[name=serie]').html(data.serie_comprobante);
					p.$w.find('[name=num]').html(data.num_comprobante);
					p.$w.find('[name=ticket]').html(data.ticket);
					p.$w.find('[name=tdoc]').html(data.tipo_doc);
					p.$w.find('[name=docident]').html(data.num_doc);
					p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data.proveedor)).data('data',data.proveedor);
					p.$w.find('[name=valor_facturado]').html(data.valor_facturado);
					p.$w.find('[name=bi]').html(data.bi);
					p.$w.find('[name=importe_exonerada]').html(data.importe_exonerada);
					p.$w.find('[name=importe_inafecta]').html(data.importe_inafecta);
					p.$w.find('[name=isc]').html(data.isc);
					p.$w.find('[name=igv]').html(data.igv);
					p.$w.find('[name=bi_arroz]').html(data.bi_arroz);
					p.$w.find('[name=impuesto_arroz]').html(data.impuesto_arroz);
					p.$w.find('[name=otros_tributos]').html(data.otros_tributos);
					p.$w.find('[name=importe_total]').html(data.importe_total);
					p.$w.find('[name=tc]').html(data.tc);
					p.$w.find('fieldset:eq(3) div:first [value='+data.estado+']').attr('checked','checked');
					p.$w.find('[name=fecemi_mod]').html(ciHelper.dateFormatBDNotHour(data.fecemi_mod));
					p.$w.find('[name=tipo_doc_mod]').html(data.tipo_doc_mod.descr);
					p.$w.find('[name=ser_doc_mod]').html(data.ser_doc_mod);
					p.$w.find('[name=num_doc_mod]').html(data.num_doc_mod);
					p.$w.find('fieldset:eq(3) div:first').buttonset();
					p.$w.find("fieldset:eq(3) div:first > input:radio").button({disabled:true});
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};