/*******************************************************************************
Registro de Compras */
ctRcom = {
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctRcom',
			titleBar: {
				title: 'Registro de Compras'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/rcom',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'fuente(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctRcom.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnCerrar]').click(function(){
					K.sendingInfo();
					$.post('ct/rcom/cerrar',{
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
			    	ctRcom.loadData({page: 1,url: 'ct/rcom/lista'});
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
					$li.eq(6).html( result.anio_DUA_DSI );
					$li.eq(7).html( result.num_comprobante );
					$li.eq(8).html( result.tipo_doc );
					$li.eq(9).html( result.num_doc );
					$li.eq(10).html( ciHelper.enti.formatName(result.proveedor) );
					$li.eq(11).html( result.ref );
					if(result.bi_cfog!=null) $li.eq(12).html( result.bi_cfog );
					else $li.eq(12).html( '0.00' );
					if(result.igv_cfog!=null) $li.eq(13).html( result.igv_cfog );
					else $li.eq(13).html( '0.00' );
					if(result.bi_cfong!=null) $li.eq(14).html( result.bi_cfong );
					else $li.eq(14).html( '0.00' );
					if(result.igv_cfong!=null) $li.eq(15).html( result.igv_cfong );
					else $li.eq(15).html( '0.00' );
					if(result.bi_scf!=null) $li.eq(16).html( result.bi_scf );
					else $li.eq(16).html( '0.00' );
					if(result.igv_scf!=null) $li.eq(17).html( result.igv_scf );
					else $li.eq(17).html( '0.00' );
					$li.eq(18).html( result.valor_no_grav );
					$li.eq(19).html( result.isc );
					$li.eq(20).html( result.otros_tributos );
					$li.eq(21).html( result.importe_cp );
					$li.eq(22).html( result.num_cp );
					$li.eq(23).html( result.num_detrac );
					$li.eq(24).html( result.fecemi_detrac );
					$li.eq(25).html( result.tc );
					$li.eq(26).html( ciHelper.dateFormat(result.fecemi_mod) );
					$li.eq(27).html( result.tipo_doc_mod.descr );
					$li.eq(28).html( result.ser_doc_mod );
					$li.eq(29).html( result.num_doc_mod );
					$li.eq(30).html( result.programa.nomb );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						ctRcom.windowDetails({id: $(this).data('id')});
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
								ctRcom.windowDetails({id: K.tmp.data('id')});
							},
							'conMenListEd_edi': function(t) {
								ctRcom.windowEdit({id: K.tmp.data('id')});
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
			id: 'windowNewRcom',
			title: 'Nuevo Registro de Compra',
			contentURL: 'ct/rcom/edit',
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
						tipo_comprobante: p.$w.find('[name=tico]:eq(0) option:selected').data('data'),
						serie_comprobante: p.$w.find('[name=serie]').val(),
						anio_DUA_DSI: p.$w.find('[name=ano]').val(),
						num_comprobante: p.$w.find('[name=num]').val(),
						imp_od: p.$w.find('[name=importe]').val(),
						ref: p.$w.find('[name=docref]').val(),
						tipo_doc: p.$w.find('[name=tdoc] option:selected').val(),
						num_doc: p.$w.find('[name=docident]').html(),
						proveedor: p.$w.find('[name=proveedor]').data('data'),
						tipo_adquisicion: p.$w.find('[name=rbtnAdq]:checked').val(),
						bi_cfog: '0.00',
						igv_cfog: '0.00',
						bi_cfong: '0.00',
						igv_cfong: '0.00',
						bi_scf: '0.00',
						igv_scf: '0.00',
						valor_no_grav: p.$w.find('[name=valor_adq]').val(),
						isc: p.$w.find('[name=monto_isc]').val(),
						otros_tributos: p.$w.find('[name=otros]').val(),
						importe_cp: p.$w.find('[name=importe_tot]').val(),
						num_cp: p.$w.find('[name=num_cp]').val(),
						tc: p.$w.find('[name=tc]').val(),
						fecemi_detrac: p.$w.find('[name=feccon]').val(),
						num_detrac: p.$w.find('[name=num_cons]').val(),
						marca_comp: p.$w.find('[name=rbtnMar]:checked').val(),
						estado: p.$w.find('[name=rbtnEst]:checked').val(),
						fecemi_mod: p.$w.find('[name=fecemi_mod]').val(),
						tipo_doc_mod: p.$w.find('[name=tico]:eq(1) option:selected').data('data'),
						ser_doc_mod: p.$w.find('[name=num_ser]').val(),
						num_doc_mod: p.$w.find('[name=num_mod]').val(),
						programa: p.$w.find('[name=orga]').data('data')
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
					if(data.programa==null){
						p.$w.find('[name=btnOrg]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.programa = {
							_id: data.programa._id.$id,
							nomb: data.programa.nomb,
							cod: data.programa.cod,
							actividad: data.programa.actividad,
							componente: data.programa.componente
						};
						data.programa.actividad._id = data.programa.actividad._id.$id;
						data.programa.componente._id = data.programa.componente._id.$id;
					}
					switch(data.tipo_adquisicion){
						case "1":
							data.bi_cfog = p.$w.find('[name=adq1]').val();
							data.igv_cfog = p.$w.find('[name=igv1]').val();
							if(data.bi_cfog==''){
								p.$w.find('[name=adq1]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Base imponible de adquisici&oacute;n!',type: 'error'});
							}
							if(data.igv_cfog==''){
								p.$w.find('[name=igv1]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV/IPM adquisici&oacute;n!',type: 'error'});
							}
							break;
						case "2":
							data.bi_cfong = p.$w.find('[name=adq2]').val();
							data.igv_cfong = p.$w.find('[name=igv2]').val();
							if(data.bi_cfong==''){
								p.$w.find('[name=adq2]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Base imponible de adquisici&oacute;n!',type: 'error'});
							}
							if(data.igv_cfong==''){
								p.$w.find('[name=igv2]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV/IPM adquisici&oacute;n!',type: 'error'});
							}
							break;
						case "3":
							data.bi_scf = p.$w.find('[name=adq3]').val();
							data.igv_scf = p.$w.find('[name=igv3]').val();
							if(data.bi_scf==''){
								p.$w.find('[name=adq3]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Base imponible de adquisici&oacute;n!',type: 'error'});
							}
							if(data.igv_scf==''){
								p.$w.find('[name=igv3]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV/IPM adquisici&oacute;n!',type: 'error'});
							}
							break;
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
					if(data.anio_DUA_DSI==''){
						p.$w.find('[name=ano]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un a&ntilde;o!',type: 'error'});
					}
					if(data.num_comprobante==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.imp_od==''){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					if(data.ref==''){
						p.$w.find('[name=docref]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un documento de referencia!',type: 'error'});
					}
					if(data.valor_no_grav==''){
						p.$w.find('[name=valor_adq]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor!',type: 'error'});
					}
					if(data.isc==''){
						p.$w.find('[name=monto_isc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
					}
					if(data.otros_tributos==''){
						p.$w.find('[name=otros]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para otros!',type: 'error'});
					}
					if(data.importe_cp==''){
						p.$w.find('[name=importe_tot]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					if(data.tc==''){
						p.$w.find('[name=tc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo de cambio!',type: 'error'});
					}
					if(data.num_cp==''){
						p.$w.find('[name=num_cp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.fecemi_detrac==''){
						p.$w.find('[name=feccon]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de detracci&oacute;n!',type: 'error'});
					}
					if(data.num_detrac==''){
						p.$w.find('[name=num_cons]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero de detracci&oacute;n!',type: 'error'});
					}
					if(data.fecemi_mod==''){
						p.$w.find('[name=fecemi_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha!',type: 'error'});
					}
					if(data.ser_doc_mod==''){
						p.$w.find('[name=num_ser]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una serie!',type: 'error'});
					}
					if(data.num_doc_mod==''){
						p.$w.find('[name=num_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/rcom/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Registro de Compra fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewRcom');
				K.block({$element: p.$w});
				p.$w.find('[name=fecemi],[name=fecven],[name=fecemi_mod]').datepicker();
				p.$w.find('[name=feccon]').datepicker({ dateFormat: 'dd/mm/yy'});
				p.$w.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100});
				p.$w.find('[name=importe],[name=valor_adq],[name=monto_isc],[name=otros],[name=importe_tot],[name=tc],[name=num_cp]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('[name^=adq],[name^=igv]').numeric().spinner({step: 0.1,min: 0});
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
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				p.$w.find('[name=btnAgrProv]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
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
					},reqs: {domicilios: true},roles: {proveedor: true}});
				}).button({icons: {primary: 'ui-icon-plusthick'},text: false});
				p.$w.find('[name=btnOrg]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('fieldset:eq(2) div:first,fieldset:eq(4) div:first,fieldset:eq(5) div:first').buttonset();
				p.$w.find('[id=rbtnAdq1]').click(function(){
					p.$w.find('fieldset:eq(2) table div').hide();
					p.$w.find('fieldset:eq(2) table [name=def]').show();
					p.$w.find('fieldset:eq(2) table tr:eq(0) [name=def]').hide();
					p.$w.find('fieldset:eq(2) table tr:eq(0) div').show();
				}).click();
				p.$w.find('[id=rbtnAdq2]').click(function(){
					p.$w.find('fieldset:eq(2) table div').hide();
					p.$w.find('fieldset:eq(2) table [name=def]').show();
					p.$w.find('fieldset:eq(2) table tr:eq(1) [name=def]').hide();
					p.$w.find('fieldset:eq(2) table tr:eq(1) div').show();
				});
				p.$w.find('[id=rbtnAdq3]').click(function(){
					p.$w.find('fieldset:eq(2) table div').hide();
					p.$w.find('fieldset:eq(2) table [name=def]').show();
					p.$w.find('fieldset:eq(2) table tr:eq(2) [name=def]').hide();
					p.$w.find('fieldset:eq(2) table tr:eq(2) div').show();
				});
				$.post('ct/rcom/get_edit',{
					mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
					ano: $mainPanel.find('[name=periodo]').data('ano')
				},function(data){
					p.cod = data.cod;
					p.periodo = data.periodo;
					if(data.tico==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay Tipos de Comprobante de Pago!',type: 'error'});
					}else{
						var $select = p.$w.find('[name=tico]');
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
			id: 'windowEditRcom'+p.id,
			title: 'Editar Registro de Compra',
			contentURL: 'ct/rcom/edit',
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
						tipo_comprobante: p.$w.find('[name=tico]:eq(0) option:selected').data('data'),
						serie_comprobante: p.$w.find('[name=serie]').val(),
						anio_DUA_DSI: p.$w.find('[name=ano]').val(),
						num_comprobante: p.$w.find('[name=num]').val(),
						imp_od: p.$w.find('[name=importe]').val(),
						ref: p.$w.find('[name=docref]').val(),
						tipo_doc: p.$w.find('[name=tdoc] option:selected').val(),
						num_doc: p.$w.find('[name=docident]').html(),
						proveedor: p.$w.find('[name=proveedor]').data('data'),
						tipo_adquisicion: p.$w.find('[name=rbtnAdq]:checked').val(),
						bi_cfog: '0.00',
						igv_cfog: '0.00',
						bi_cfong: '0.00',
						igv_cfong: '0.00',
						bi_scf: '0.00',
						igv_scf: '0.00',
						valor_no_grav: p.$w.find('[name=valor_adq]').val(),
						isc: p.$w.find('[name=monto_isc]').val(),
						otros_tributos: p.$w.find('[name=otros]').val(),
						importe_cp: p.$w.find('[name=importe_tot]').val(),
						num_cp: p.$w.find('[name=num_cp]').val(),
						tc: p.$w.find('[name=tc]').val(),
						fecemi_detrac: p.$w.find('[name=feccon]').val(),
						num_detrac: p.$w.find('[name=num_cons]').val(),
						marca_comp: p.$w.find('[name=rbtnMar]:checked').val(),
						estado: p.$w.find('[name=rbtnEst]:checked').val(),
						fecemi_mod: p.$w.find('[name=fecemi_mod]').val(),
						tipo_doc_mod: p.$w.find('[name=tico]:eq(1) option:selected').data('data'),
						ser_doc_mod: p.$w.find('[name=num_ser]').val(),
						num_doc_mod: p.$w.find('[name=num_mod]').val(),
						programa: p.$w.find('[name=orga]').data('data')
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
					if(data.programa==null){
						p.$w.find('[name=btnOrg]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.programa = {
							_id: data.programa._id.$id,
							nomb: data.programa.nomb,
							cod: data.programa.cod,
							actividad: data.programa.actividad,
							componente: data.programa.componente
						};
						data.programa.actividad._id = data.programa.actividad._id.$id;
						data.programa.componente._id = data.programa.componente._id.$id;
					}
					switch(data.tipo_adquisicion){
						case "1":
							data.bi_cfog = p.$w.find('[name=adq1]').val();
							data.igv_cfog = p.$w.find('[name=igv1]').val();
							if(data.bi_cfog==''){
								p.$w.find('[name=adq1]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Base imponible de adquisici&oacute;n!',type: 'error'});
							}
							if(data.igv_cfog==''){
								p.$w.find('[name=igv1]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV/IPM adquisici&oacute;n!',type: 'error'});
							}
							break;
						case "2":
							data.bi_cfong = p.$w.find('[name=adq2]').val();
							data.igv_cfong = p.$w.find('[name=igv2]').val();
							if(data.bi_cfong==''){
								p.$w.find('[name=adq2]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Base imponible de adquisici&oacute;n!',type: 'error'});
							}
							if(data.igv_cfong==''){
								p.$w.find('[name=igv2]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV/IPM adquisici&oacute;n!',type: 'error'});
							}
							break;
						case "3":
							data.bi_scf = p.$w.find('[name=adq3]').val();
							data.igv_scf = p.$w.find('[name=igv3]').val();
							if(data.bi_scf==''){
								p.$w.find('[name=adq3]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Base imponible de adquisici&oacute;n!',type: 'error'});
							}
							if(data.igv_scf==''){
								p.$w.find('[name=igv3]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un IGV/IPM adquisici&oacute;n!',type: 'error'});
							}
							break;
					}
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
					if(data.anio_DUA_DSI==''){
						p.$w.find('[name=ano]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un a&ntilde;o!',type: 'error'});
					}
					if(data.num_comprobante==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.imp_od==''){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					if(data.ref==''){
						p.$w.find('[name=docref]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un documento de referencia!',type: 'error'});
					}
					if(data.valor_no_grav==''){
						p.$w.find('[name=valor_adq]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor!',type: 'error'});
					}
					if(data.isc==''){
						p.$w.find('[name=monto_isc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
					}
					if(data.otros_tributos==''){
						p.$w.find('[name=otros]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para otros!',type: 'error'});
					}
					if(data.importe_cp==''){
						p.$w.find('[name=importe_tot]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					if(data.num_cp==''){
						p.$w.find('[name=num_cp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					if(data.tc==''){
						p.$w.find('[name=tc]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un tipo de cambio!',type: 'error'});
					}
					if(data.fecemi_detrac==''){
						p.$w.find('[name=feccon]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de detracci&oacute;n!',type: 'error'});
					}
					if(data.num_detrac==''){
						p.$w.find('[name=num_cons]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero de detracci&oacute;n!',type: 'error'});
					}
					if(data.fecemi_mod==''){
						p.$w.find('[name=fecemi_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha!',type: 'error'});
					}
					if(data.ser_doc_mod==''){
						p.$w.find('[name=num_ser]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una serie!',type: 'error'});
					}
					if(data.num_doc_mod==''){
						p.$w.find('[name=num_mod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/rcom/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Registro de Compra fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditRcom'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=fecemi],[name=fecven],[name=fecemi_mod]').datepicker();
				p.$w.find('[name=feccon]').datepicker({ dateFormat: 'dd/mm/yy'});
				p.$w.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100});
				p.$w.find('[name=importe],[name=valor_adq],[name=monto_isc],[name=otros],[name=importe_tot],[name=tc],[name=num_cp]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('[name^=adq],[name^=igv]').numeric().spinner({step: 0.1,min: 0});
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
				p.$w.find('[name=btnOrg]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[id=rbtnAdq1]').click(function(){
					p.$w.find('fieldset:eq(2) table div').hide();
					p.$w.find('fieldset:eq(2) table [name=def]').show();
					p.$w.find('fieldset:eq(2) table tr:eq(0) [name=def]').hide();
					p.$w.find('fieldset:eq(2) table tr:eq(0) div').show();
				}).click();
				p.$w.find('[id=rbtnAdq2]').click(function(){
					p.$w.find('fieldset:eq(2) table div').hide();
					p.$w.find('fieldset:eq(2) table [name=def]').show();
					p.$w.find('fieldset:eq(2) table tr:eq(1) [name=def]').hide();
					p.$w.find('fieldset:eq(2) table tr:eq(1) div').show();
				});
				p.$w.find('[id=rbtnAdq3]').click(function(){
					p.$w.find('fieldset:eq(2) table div').hide();
					p.$w.find('fieldset:eq(2) table [name=def]').show();
					p.$w.find('fieldset:eq(2) table tr:eq(2) [name=def]').hide();
					p.$w.find('fieldset:eq(2) table tr:eq(2) div').show();
				});
				$.post('ct/rcom/get_edit',function(data){
					if(data.tico==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay Tipos de Comprobante de Pago!',type: 'error'});
					}else{
						var $select = p.$w.find('[name=tico]');
						for(var i=0,j=data.tico.length; i<j; i++){
							$select.append('<option value="'+data.tico[i]._id.$id+'">'+data.tico[i].cod+' - '+data.tico[i].descr+'</option>');
							$select.find('option:last').data('data',data.tico[i]);
						}
					}
					$.post('ct/rcom/get','id='+p.id,function(data){
						p.cod = data.num_correlativo;
						p.periodo = data.periodo;
						p.$w.find('[name=periodo]').html(data.periodo);
						p.$w.find('[name=cod]').html(data.num_correlativo);
						p.$w.find('[name=fecemi]').val(ciHelper.dateFormatBDNotHour(data.fecemi));
						p.$w.find('[name=fecven]').val(ciHelper.dateFormatBDNotHour(data.fecven));
						p.$w.find('[name=tico]:eq(0)').selectVal(data.tipo_comprobante._id.$id);
						p.$w.find('[name=serie]').val(data.serie_comprobante);
						p.$w.find('[name=ano]').val(data.anio_DUA_DSI);
						p.$w.find('[name=num]').val(data.num_comprobante);
						p.$w.find('[name=importe]').val(data.imp_od);
						p.$w.find('[name=docref]').val(data.ref);
						p.$w.find('[name=tdoc]').selectVal(data.tipo_doc);
						p.$w.find('[name=docident]').html(data.num_doc);
						p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data.proveedor)).data('data',data.proveedor);
						p.$w.find('[name=rbtnAdq]').removeAttr('checked');
						p.$w.find('[name=rbtnAdq]').eq(parseFloat(data.tipo_adquisicion)-1).attr('checked','checked');
						if(data.bi_cfog!=null) p.$w.find('[name=adq1]').val(data.bi_cfog);
						if(data.igv_cfog!=null) p.$w.find('[name=igv1]').val(data.igv_cfog);
						if(data.bi_cfong!=null) p.$w.find('[name=adq2]').val(data.bi_cfong);
						if(data.igv_cfong!=null) p.$w.find('[name=igv2]').val(data.igv_cfong);
						if(data.bi_scf!=null) p.$w.find('[name=adq3]').val(data.bi_scf);
						if(data.igv_scf!=null) p.$w.find('[name=igv3]').val(data.igv_scf);
						p.$w.find('[name=valor_adq]').val(data.valor_no_grav);
						p.$w.find('[name=monto_isc]').val(data.isc);
						p.$w.find('[name=otros]').val(data.otros_tributos);
						p.$w.find('[name=importe_tot]').val(data.importe_cp);
						p.$w.find('[name=num_cp]').val(data.num_cp);
						p.$w.find('[name=tc]').val(data.tc);
						p.$w.find('[name=feccon]').val(data.fecemi_detrac);
						p.$w.find('[name=num_cons]').val(data.num_detrac);
						p.$w.find('[name=rbtnMar]').removeAttr('checked');
						p.$w.find('fieldset:eq(4) div:first [value='+data.marca_comp+']').attr('checked','checked');
						p.$w.find('[name=rbtnEst]').removeAttr('checked');
						p.$w.find('fieldset:eq(5) div:first [value='+data.estado+']').attr('checked','checked');
						p.$w.find('[name=fecemi_mod]').val(ciHelper.dateFormatBDNotHour(data.fecemi_mod));
						p.$w.find('[name=tico]:eq(1)').selectVal(data.tipo_doc_mod._id.$id);
						p.$w.find('[name=num_ser]').val(data.ser_doc_mod);
						p.$w.find('[name=num_mod]').val(data.num_doc_mod);
						p.$w.find('[name=orga]').html(data.programa.nomb).data('data',data.programa);
						p.$w.find('fieldset:eq(2) div:first,fieldset:eq(4) div:first,fieldset:eq(5) div:first').buttonset();
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsRcom'+p.id,
			title: 'Registro de Compra',
			contentURL: 'ct/rcom/details',
			icon: 'ui-icon-document',
			width: 650,
			height: 410,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsRcom'+p.id);
				K.block({$element: p.$w});
				$.post('ct/rcom/get','id='+p.id,function(data){
					p.$w.find('[name=periodo]').html(data.periodo);
					p.$w.find('[name=cod]').html(data.num_correlativo);
					p.$w.find('[name=fecemi]').html(ciHelper.dateFormatBDNotHour(data.fecemi));
					p.$w.find('[name=fecven]').html(ciHelper.dateFormatBDNotHour(data.fecven));
					p.$w.find('[name=tico]:eq(0)').html(data.tipo_comprobante.descr);
					p.$w.find('[name=serie]').html(data.serie_comprobante);
					p.$w.find('[name=ano]').html(data.anio_DUA_DSI);
					p.$w.find('[name=num]').html(data.num_comprobante);
					p.$w.find('[name=importe]').html(data.imp_od);
					p.$w.find('[name=docref]').html(data.ref);
					p.$w.find('[name=tdoc]').html(data.tipo_doc);
					p.$w.find('[name=docident]').html(data.num_doc);
					p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data.proveedor));
					p.$w.find('[name=rbtnAdq]').removeAttr('checked');
					p.$w.find('[name=rbtnAdq]').eq(parseFloat(data.tipo_adquisicion)-1).attr('checked','checked');
					if(data.bi_cfog!=null) p.$w.find('[name=adq1]').html(data.bi_cfog);
					if(data.igv_cfog!=null) p.$w.find('[name=igv1]').html(data.igv_cfog);
					if(data.bi_cfong!=null) p.$w.find('[name=adq2]').html(data.bi_cfong);
					if(data.igv_cfong!=null) p.$w.find('[name=igv2]').html(data.igv_cfong);
					if(data.bi_scf!=null) p.$w.find('[name=adq3]').html(data.bi_scf);
					if(data.igv_scf!=null) p.$w.find('[name=igv3]').html(data.igv_scf);
					p.$w.find('[name=valor_adq]').html(data.valor_no_grav);
					p.$w.find('[name=monto_isc]').html(data.isc);
					p.$w.find('[name=otros]').html(data.otros_tributos);
					p.$w.find('[name=importe_tot]').html(data.importe_cp);
					p.$w.find('[name=num_cp]').html(data.num_cp);
					p.$w.find('[name=tc]').html(data.tc);
					p.$w.find('[name=feccon]').html(data.fecemi_detrac);
					p.$w.find('[name=num_cons]').html(data.num_detrac);
					p.$w.find('[name=rbtnMar]').removeAttr('checked');
					p.$w.find('fieldset:eq(4) div:first [value='+data.marca_comp+']').attr('checked','checked');
					p.$w.find('[name=rbtnEst]').removeAttr('checked');
					p.$w.find('fieldset:eq(5) div:first [value='+data.estado+']').attr('checked','checked');
					p.$w.find('[name=fecemi_mod]').html(ciHelper.dateFormatBDNotHour(data.fecemi_mod));
					p.$w.find('[name=tico]:eq(1)').html(data.tipo_doc_mod.descr);
					p.$w.find('[name=num_ser]').html(data.ser_doc_mod);
					p.$w.find('[name=num_mod]').html(data.num_doc_mod);
					p.$w.find('[name=orga]').html(data.programa.nomb);
					p.$w.find('fieldset:eq(2) div:first,fieldset:eq(4) div:first,fieldset:eq(5) div:first').buttonset();
					$("fieldset:eq(2) div:first > input:radio").button({disabled:true});
					$("fieldset:eq(4) div:first > input:radio").button({disabled:true});
					$("fieldset:eq(5) div:first > input:radio").button({disabled:true});
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};