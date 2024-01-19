/*******************************************************************************
polizas */
tsPoli = {
	states: {
		R: {
			descr: "Registrado",
			color: "#006532"
		},
		X: {
			descr: "Anulado",
			color: "#000000"
		}
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsPoli'+p.id,
			title: 'P&oacute;liza Contable: '+p.nomb,
			contentURL: 'ts/poli/details',
			icon: 'ui-icon-note',
			width: 510,
			height: 300,
			buttons: {
				"Imprimir": function(){
					var url = 'ts/poli/print?id='+p.id;
					K.windowPrint({
						id:'windowTsPoliPrint',
						title: "Reporte / Informe",
						url: url
					});
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsPoli'+p.id);
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				K.block({$element: p.$w});
				$.post('ts/poli/get','id='+p.id,function(data){
					p.$w.find('h3').append(data.cod);
					p.$w.find('[name=descr]').html(data.descr);
					for(var i=0,j=data.cont_patrimonial.length; i<j; i++){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.cont_patrimonial[i].cuenta.cod);
						$row.find('li:eq(1)').html(data.cont_patrimonial[i].cuenta.descr);
						if(data.cont_patrimonial[i].tipo=='D')
							$row.find('li:eq(2)').html(ciHelper.formatMon(data.cont_patrimonial[i].monto,data.cont_patrimonial[i].moneda));
						else
							$row.find('li:eq(3)').html(ciHelper.formatMon(data.cont_patrimonial[i].monto,data.cont_patrimonial[i].moneda));
						$row.wrapInner('<a class="item">');
						p.$w.find('.gridBody').append($row.children());
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewPoli',
			title: 'Nueva P&oacute;liza Contable',
			contentURL: 'ts/poli/new',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						cod: p.$w.find('[name=cod]').val(),
						descr: p.$w.find('[name=descr]').val(),
						cont_patrimonial: []
					},
					tot_d = 0,
					tot_h = 0;
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para la p&oacute;liza!',type: 'error'});
					}else if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la p&oacute;liza!',type: 'error'});
					}else if(p.$w.find('.gridBody .item').length==0){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un item de Contabilidad Patrimonial!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.gridBody .item').length; i<j; i++){
						var tmp = p.$w.find('.gridBody .item').eq(i).data('data');
						if(tmp.tipo=='D') tot_d += parseFloat(tmp.importe);
						else tot_h += parseFloat(tmp.importe);
						data.cont_patrimonial.push({
							tipo: tmp.tipo,
							cuenta: {
								_id: tmp.cuenta._id.$id,
								cod: tmp.cuenta.cod,
								descr: tmp.cuenta.descr
							},
							moneda: 'S',
							monto: tmp.importe
						});
					}
					if(tot_d!=tot_h){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total de Haberes no coincide con el total de Debes!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/poli/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La P&oacute;liza fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewPoli');
				p.$w.find('[name=importe]').val(0).numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnCuenta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('table:eq(1) td:eq(3)').buttonset();
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('[name=btnAgre]').click(function(){
					var tmp = {
						cuenta: p.$w.find('[name=cuenta]').data('data'),
						tipo: p.$w.find('[name=rbtnPoli]:checked').val(),
						importe: p.$w.find('[name=importe]').val()
					};
					if(tmp.cuenta==null){
						p.$w.find('[name=btnCuenta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else if(tmp.importe==''){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html(tmp.cuenta.cod);
					$row.find('li:eq(1)').html(tmp.cuenta.descr);
					if(tmp.tipo=='D') $row.find('li:eq(2)').html(ciHelper.formatMon(tmp.importe));
					else $row.find('li:eq(3)').html(ciHelper.formatMon(tmp.importe));
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('.item').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('.item').data('data',tmp);
		        	p.$w.find(".gridBody").append( $row.children() );
		        	p.$w.find('[name=cuenta]').html('').removeData('data')
					p.$w.find('[name=importe]').val(0);
				}).button({icons: {primary: 'ui-icon-plusthick'}});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({
			id: 'windowEditPoli'+p.id,
			title: 'Editar P&oacute;liza Contable: '+p.nomb,
			contentURL: 'ts/poli/new',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						cod: p.$w.find('[name=cod]').val(),
						descr: p.$w.find('[name=descr]').val(),
						cont_patrimonial: []
					},
					tot_d = 0,
					tot_h = 0;
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para la p&oacute;liza!',type: 'error'});
					}else if(data.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la p&oacute;liza!',type: 'error'});
					}else if(p.$w.find('.gridBody .item').length==0){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un item de Contabilidad Patrimonial!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.gridBody .item').length; i<j; i++){
						var tmp = p.$w.find('.gridBody .item').eq(i).data('data');
						if(tmp.tipo=='D') tot_d += parseFloat(tmp.importe);
						else tot_h += parseFloat(tmp.importe);
						data.cont_patrimonial.push({
							tipo: tmp.tipo,
							cuenta: {
								_id: tmp.cuenta._id.$id,
								cod: tmp.cuenta.cod,
								descr: tmp.cuenta.descr
							},
							moneda: 'S',
							monto: tmp.importe
						});
					}
					if(tot_d!=tot_h){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total de Haberes no coincide con el total de Debes!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/poli/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La P&oacute;liza fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditPoli'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=importe]').val(0).numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnCuenta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('table:eq(1) td:eq(3)').buttonset();
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('[name=btnAgre]').click(function(){
					var tmp = {
						cuenta: p.$w.find('[name=cuenta]').data('data'),
						tipo: p.$w.find('[name=rbtnPoli]:checked').val(),
						importe: p.$w.find('[name=importe]').val()
					};
					if(tmp.cuenta==null){
						p.$w.find('[name=btnCuenta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'error'});
					}else if(tmp.importe==''){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un importe!',type: 'error'});
					}
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html(tmp.cuenta.cod);
					$row.find('li:eq(1)').html(tmp.cuenta.descr);
					if(tmp.tipo=='D') $row.find('li:eq(2)').html(ciHelper.formatMon(tmp.importe));
					else $row.find('li:eq(3)').html(ciHelper.formatMon(tmp.importe));
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('.item').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('.item').data('data',tmp);
		        	p.$w.find(".gridBody").append( $row.children() );
		        	p.$w.find('[name=cuenta]').html('').removeData('data');
					p.$w.find('[name=importe]').val(0);
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$.post('ts/poli/get',{id: p.id},function(data){
					p.$w.find('[name=cod]').val(data.cod);
					p.$w.find('[name=descr]').val(data.descr);
					for(var i=0,j=data.cont_patrimonial.length; i<j; i++){
						var item = data.cont_patrimonial[i];
						var tmp = {
							cuenta: item.cuenta,
							tipo: item.tipo,
							importe: item.monto
						};
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(tmp.cuenta.cod);
						$row.find('li:eq(1)').html(tmp.cuenta.descr);
						if(tmp.tipo=='D') $row.find('li:eq(2)').html(ciHelper.formatMon(tmp.importe));
						else $row.find('li:eq(3)').html(ciHelper.formatMon(tmp.importe));
						$row.find('[name=btnEli]').click(function(){
							$(this).closest('.item').remove();
						}).button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						$row.find('.item').data('data',tmp);
			        	p.$w.find(".gridBody").append( $row.children() );
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.estado = 'H';
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('cj/poli/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
						$li.eq(1).html( result.local.descr );
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
			id: 'windowSelectPoli',
			title: 'Seleccionar P&oacute;liza',
			contentURL: 'cj/poli/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una p&oacute;liza!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectPoli');
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
	},
	windowCta: function(p){
		new K.Modal({
			id: 'windowPoliCta'+p.id,
			title: 'Generar Movimiento Cuenta Corriente',
			contentURL: 'ts/poli/cta',
			store: false,
			icon: 'ui-icon-gear',
			width: 530,
			height: 410,
			buttons: {
				'Guardar': function(){
					K.clearNoti();
					var data = {
						id_operacion: p.id,
						cod_operacion: p.cod,
						periodo: 0,
						fecreg: ciHelper.dateFormatBDNotHour(p.fec),
						cuenta_banco: p.$w.find('[name=ctban] option:selected').data('data'),
						medio_pago: p.$w.find('[name=tmed] option:selected').data('data'),
						descr: p.$w.find('[name=descr]').val(),
						entidades: [
						    {entidad: ciHelper.enti.dbRel(p.$w.find('[name=entidad]').data('data'))}
						]
					};
					data.cuenta_banco = {
						_id: data.cuenta_banco._id.$id,
						cod_banco: data.cuenta_banco.cod_banco,
						nomb: data.cuenta_banco.nomb,
						cod: data.cuenta_banco.cod,
						moneda: data.cuenta_banco.moneda,
						cuenta: {
							_id: data.cuenta_banco.cuenta._id.$id,
							cod: data.cuenta_banco.cuenta.cod,
							nomb: data.cuenta_banco.cuenta.nomb
						}
					};
					data.medio_pago = {
						_id: data.medio_pago._id.$id,
						cod: data.medio_pago.cod
					};
					if(p.$w.find('[name=entidad]').data('data')==null){
						p.$w.find('[name=btnEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('lg/peco/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La PECOSA fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
						tmp = null;
					});
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowPoliCta'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=fec]').html(ciHelper.dateFormatOnlyDay(p.fec));
				p.$w.find('[name=cod]').html('PCF '+p.cod);
				p.$w.find('[name=monto]').numeric().spinner({step: 0.1});
				p.$w.find('.ui-button').height('14px');
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=entidad]').html(ciHelper.enti.formatName(data)).data('data',data);
						var $cbo = p.$w.find('[name=tdoc]').empty();
						for(var i=0,j=data.docident.length; i<j; i++){
							$cbo.append('<option value="'+data.docident[i].num+'">'+data.docident[i].tipo+'</option>');
						}
						$cbo.change();
						p.$w.find('[name=btnEnti]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
						p.$w.find('[name=btnCta]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=tdoc]').change(function(){
					p.$w.find('[name=num]').html($(this).find('option:selected').val());
				});
				p.$w.find('td:eq(21)').buttonset();
				$.post('ts/poli/get_cta',function(data){
					if(data.tmed==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear tipos de medio de pago!',type: 'error'});
					}else{
						var $cbo = p.$w.find('[name=tmed]');
						for(var i=0,j=data.tmed.length; i<j; i++){
							$cbo.append('<option value="'+data.tmed[i]._id.$id+'">'+data.tmed[i].descr+'</option>');
							$cbo.find('option:last').data('data',data.tmed[i]);
						}
					}
					if(data.ctban==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear cuentas bancarias!',type: 'error'});
					}else{
						var $cbo = p.$w.find('[name=ctban]');
						for(var i=0,j=data.ctban.length; i<j; i++){
							$cbo.append('<option value="'+data.ctban[i]._id.$id+'">'+data.ctban[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data.ctban[i]);
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};