/*******************************************************************************
Unidades */
cjInmu = {
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjInmu',
			titleBar: {
				title: 'Caja Inmuebles'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/cuen/inmu',
			store: false,
			onContentLoaded: function(){
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=btnEspa]').click(function(){
					inLoca.selectEspAll({callback: function(data){
						console.info(data);
						$mainPanel.find('[name=arre]').empty();
						$mainPanel.find('[name=espacio]').html(data.descr+' - '+data.ubic.local.direc)
							.data('data',data);
						var $arre = $mainPanel.find('[name=arre]');
						K.block({$element: $('#pageWrapperMain')});
						$.post('in/arre/get_arre_all',{_id: data._id.$id},function(data){
							if(data!=null){
								for(var i=0; i<data.length; i++){
									var arre = data[i].arrendatario;
									$arre.append('<option value="'+arre._id.$id+'">'+mgEnti.formatName(arre)+'</option>');
								}
							}
							$arre.unbind('change').change(function(){
								$grid.reinit({params: {
									orga: '51a50edc4d4a13441100000e',
									cliente: $(this).find('option:selected').val()
								}});
							}).change();
							K.unblock({$element: $('#pageWrapperMain')});
						},'json');
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				var $grid = K.grid({
					$el: $mainPanel.find('[name=grid]'),
					cols: ['','','Arrendatario','Cuota','Detalle','Total','Vencimiento','Registrado por','Registrado'],
					data: 'cj/cuen/lista_all',
					params: {
						orga: '51a50edc4d4a13441100000e'
					},
					itemdescr: 'recibo(s) definitivo(s)',
					toolbarHTML: '<button name="btnEmitir">Emitir Comprobante</button>&nbsp;<button name="btnConfig">Configurar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnEmitir]').click(function(){
							K.clearNoti();
							var $row_f = $mainPanel.find('[name=cuenta]:checked').eq(0).closest('.item'),
							cuentas = [],
							tipo = $row_f.data('tipo'),
							cliente = $row_f.data('cliente'),
							orga = $row_f.data('orga'),
							moneda = $row_f.data('moneda');
							if($mainPanel.find('[name=cuenta]:checked').length<=0){
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos una cuenta!',type: 'error'});
							}
							var tmp_tipo = true;
							if(tipo=='') tmp_tipo = false;
							for(var i=0,j=$mainPanel.find('[name=cuenta]:checked').length; i<j; i++){
								var $row_t = $mainPanel.find('[name=cuenta]:checked').eq(i).closest('.item');
								if($row_t.data('tipo')==''&&tmp_tipo==true){
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar cuentas del mismo tipo!',type: 'error'});
								}
								if($row_t.data('cliente')!=cliente){
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar cuentas del mismo cliente!',type: 'error'});
								}
								if($row_t.data('moneda')!=moneda){
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar cuentas con la misma moneda!',type: 'error'});
								}
								cuentas.push($mainPanel.find('[name=cuenta]:checked').eq(i).val());
							}
							if(tipo!='')
								cjInmu.windowNewCompr({cuentas: cuentas,cliente: cliente});
							else
								cjCuen.windowNewCompr({cuentas: cuentas,cliente: cliente});
						}).button({icons: {primary: 'ui-icon-plusthick'}});
						$el.find('[name=btnConfig]').click(function(){
							cjInmu.windowConfig();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.find('td:last').css('background',cjCuen.states[data.estado].color).addClass('vtip').attr('title',cjCuen.states[data.estado].descr);
						if(data.estado=='P')
							$row.append('<td><input type="checkbox" name="cuenta" value="'+data._id.$id+'"></td>');
						else
							$row.append('<td>');
						$row.append('<td>'+ciHelper.enti.formatName(data.cliente)+'</td>');
						if(data.observ!=null){
							var tmp = parseInt(data.observ.substr(33,2));
							if(isNaN(tmp)) tmp = '';
						}else
							tmp = '';
						$row.append('<td>'+tmp+'</td>');
						if(data.observ!=null)
							$row.append('<td>'+data.observ+'</td>');
						else
							$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.dateFormat(data.fecven)+'</td>');
						$row.append('<td>'+ciHelper.enti.formatName(data.autor)+'</td>');
						$row.append('<td>'+ciHelper.dateFormat(data.fecreg)+'</td>');
						var orga = data.servicio.organizacion._id;
						if(orga.$id!=null)
							orga = data.servicio.organizacion._id.$id;
						$row.data('id',data._id.$id).dblclick(function(){
							cjCuen.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(3)').html()});
						}).data('estado',data.estado).data('orga',orga).data('cliente',data.cliente._id.$id)
						.data('tipo',tmp).data('data',data).contextMenu("conMenCjCuen", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').operacion==null) $('#conMenCjCuen_ori',menu).remove();
								$('#conMenCjCuen_anu',menu).remove();
								return menu;
							},
							bindings: {
								'conMenCjCuen_ver': function(t) {
									cjCuen.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(3)').html()});
								},
								'conMenCjCuen_ori': function(t) {
									var data = K.tmp.data('data');
									if(data.modulo=='IN'){
										inArre.windowDetails({id: data.operacion.$id});
									}else if(data.modulo=='CM'){
										$.post('cm/oper/get',{id: data.operacion.$id},function(oper){
											cmOper.showDetails({data: oper});
										},'json');
									}
								},
								'conMenCjCuen_anu': function(t) {
									K.sendingInfo();
									$.post('cj/cuen/save',{_id: K.tmp.data('id'),estado: 'X'},function(){
										K.clearNoti();
										K.notification({title: 'Cuenta Anulada',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								}
							}
						});
						$row.find('td:eq(0),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7)')
						.click(function(){
							var $check = $(this).closest('.item').find('[name=cuenta]');
							if($check.is(':checked')==false) $check.attr('checked','checked');
							else $check.removeAttr('checked');
							if($(this).closest('.item').data('estado')=='I'){
								K.clearNoti();
								$check.removeAttr('checked');
								K.notification({title: 'Letra Protestada',text: 'Para pagar esta cuenta debe primero levantar la letra en Inmuebles!',type: 'info'});
							}
						});
						return $row;
					}
				});
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	windowNewCompr: function(p){
		console.log(p.cliente+'--');
		$.extend(p,{
			calcTot: function(){
				if(p.$w.find('[name=totalGrid]').length<=0){
					return false;
				}
				var total = 0,
				tasa = p.$w.find('[name=tasa]').val();
				for(var i=0,j=p.$w.find('.gridBody:eq(1) [name^=cuenta_]').length; i<j; i++){
					var renta = parseFloat(p.$w.find('.gridBody:eq(1) [name^=cuenta_]').eq(i).data('monto'));
					total += renta;
					total += parseFloat(K.round((p.igv),2))*renta;
					var mora = (parseFloat(p.$w.find('.gridBody:eq(1) [name^=mora]').eq(i).val())/100)*renta;
					p.$w.find('.gridBody:eq(1) [name^=mora]').eq(i).closest('.item').find('li:eq(3)').html(ciHelper.formatMon(mora,p.moneda));
					total += mora;
				}
				p.$w.find('[name=totalGrid]').data('monto',total)
				.find('li:eq(1)').html(ciHelper.formatMon(total,p.moneda));
				if(p.moneda=='D'){
					total = total * parseFloat(tasa!=''?tasa:0);
				}
				p.total = total;
				p.$w.find('[name=totalGridConv]').data('monto',total)
				.find('li:eq(1)').html(ciHelper.formatMon(total));
				/* DETRACCION */
				if(p.total>700)
					p.$w.find('[name=tmp_det]').html('Si se fuera a emitir una <b>FACTURA</b>, la detracci&oacute;n ser&aacute; de '+ciHelper.formatMon(total*p.detraccion));
				else
					p.$w.find('[name=tmp_det]').html('');
			},
			save: function(){
				var data = {
					cliente: ciHelper.enti.dbRel(p.cliente),
					caja: p.$w.find('[name=caja] option:selected').data('data'),
					tipo: p.$w.find('[name=comp] option:selected').val(),
					serie: p.$w.find('[name=serie] option:selected').html(),
					num: p.$w.find('[name=num]').val(),
					fecreg: p.$w.find('[name=fecreg]').val(),
					observ: p.$w.find('[name=observ]').val(),
					moneda: p.moneda,
					items: [],
					total: p.$w.find('[name=totalGrid]').data('monto')
				};
				/*if(data.moneda=='D'){
					data.total = data.total*p.tasa;
				}*/
				if(data.num==''){
					p.$w.find('[name=num]').focus();
					return K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe ingresar un n&uacute;mero de comprobante!',
						type: 'error'
					});
				}
				if(data.moneda=='D'){
					data.tc = p.$w.find('[name=tasa]').val();
					data.total_dolares = p.$w.find('[name=totalGrid]').data('monto');
					data.total_soles = p.$w.find('[name=totalGridConv]').data('monto');
				}else{
					data.tc = p.tasa;
				}
				data.caja = {
					_id: data.caja._id.$id,
					nomb: data.caja.nomb,
					local: {
						_id: data.caja.local._id.$id,
						descr: data.caja.local.descr,
						direccion: data.caja.local.direccion
					}
				};
				for(var i=0,j=p.cuentas.length; i<j; i++){
					var item = {
						cuenta_cobrar: {
							_id: p.cuentas[i]._id.$id,
							servicio: {
								_id: p.cuentas[i].servicio._id.$id,
								nomb: p.cuentas[i].servicio.nomb,
								organizacion: {
									_id: p.cuentas[i].servicio.organizacion._id.$id,
									nomb: p.cuentas[i].servicio.organizacion.nomb
								}
							}
						},
						total: p.cuentas[i].total,
						conceptos: []
					};
					for(var k=0,l=p.cuentas[i].conceptos.length; k<l; k++){
						var concepto = {
							concepto: {
								_id: p.cuentas[i].conceptos[k].concepto._id.$id,
								nomb: p.cuentas[i].conceptos[k].concepto.nomb
							},
							monto: p.$w.find('.gridBody [name='+p.cuentas[i]._id.$id+']').eq(k).find('[name=sub]').val()
						};
						item.conceptos.push(concepto);
					}
					data.items.push(item);
				}
				/*if(parseFloat(data.total)==0){
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El comprobante no puede tener como total 0!',type: 'error'});
				}*/
				var tot = 0;
				tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
				tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())*p.tasa;
				data.efectivos = [
				     {
				    	 moneda: 'S',
				    	 monto: p.$w.find('[name=mon_sol] [name=tot]').val()
				     },
				     {
				    	 moneda: 'D',
				    	 monto: p.$w.find('[name=mon_dol] [name=tot]').val()
				     }
				];
				for(var i=0,j=p.$w.find('[name=ctban]').length; i<j; i++){
					var tmp = {
						num: p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').val(),
						monto: parseFloat(p.$w.find('[name=ctban]').eq(i).find('[name=tot]').val()),
						moneda: p.$w.find('[name=ctban]').eq(i).data('moneda'),
						cuenta_banco: p.$w.find('[name=ctban]').eq(i).data('data')
					};
					if(tmp.monto>0){
						if(tmp.num==''){
							p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero de voucher!',type: 'error'});
						}
						if(data.vouchers==null) data.vouchers = [];
						data.vouchers.push(tmp);
						tot += (tmp.moneda=='S')?tmp.monto:tmp.monto*p.tasa;
					}
				}
				console.log(data);
				if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total del comprobante no coincide con el total de la forma de pagar!',type: 'error'});
				}
				
				
				
				
				
				
				
				return 0;
				
				
				
				
				
				
				K.sendingInfo();
				p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
				$.post('cj/cuen/save_comp',data,function(){
					K.clearNoti();
					K.closeWindow(p.$w.attr('id'));
					K.notification({title: ciHelper.titleMessages.regiGua,text: 'Comprobante creado con &eacute;xito!'});
					$('#pageWrapperLeft .ui-state-highlight').click();
				});
			}
		});
		new K.Modal({
			id: 'windowNewCompr',
			title: 'Nuevo Comprobante',
			contentURL: 'cj/cuen/new_comp_inmu',
			icon: 'ui-icon-plusthick',
			width: 705,
			height: 410,
			buttons: {
				"Guardar": function(){
					if(p.total_all_cuentas>=700&&p.$w.find('[name=comp] option:selected').val()=="F"){
						var detrac = p.total_all_cuentas*0.12;
						ciHelper.confirm(
							'Usted va a emitir una factura que supera los S/. 700.00. No se olvide haber ingreso el boucher de detraccion por un monto de S/.'+K.round(detrac,2)+' ¿Desea Continuar?',
							function () {
								p.save();
							},
							function () {
								//nothing
							}										
						);
					}else{
						p.save();
					}										
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewCompr');
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$w.find('[name=fecreg]').val(K.date()).datepicker();
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: function(data){
						p.cliente = data;
						p.$w.find('[name=nomb]').html(data.nomb);
						if(data.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
						else p.$w.find('[name=apell]').html(data.appat+' '+data.apmat);
						p.$w.find('[name=dni]').html(data.docident[0].num);
						if(data.domicilios!=null) p.$w.find('[name=direc]').html(data.domicilios[0].direccion);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgr]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.cliente = data;
						p.$w.find('[name=nomb]').html(data.nomb);
						if(data.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
						else p.$w.find('[name=apell]').html(data.appat+' '+data.apmat);
						p.$w.find('[name=dni]').html(data.docident[0].num);
						if(data.domicilios!=null) p.$w.find('[name=direc]').html(data.domicilios[0].direccion);
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				});
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$.post('cj/cuen/get_info_comp',{cuentas: p.cuentas,cliente: p.cliente},function(data){
					/*Cliente*/
					p.tasa = parseFloat(data.tasa.valor);
					for(var i=0,j=data.vars.length; i<j; i++){
						if(data.vars[i].cod=='IGV')
							p.igv = (parseFloat(data.vars[i].valor)/100);
						else if(data.vars[i].cod=='MORA')
							p.mora = parseFloat(data.vars[i].valor);
						else if(data.vars[i].cod=='DETRACCION')
							p.detraccion = parseFloat(data.vars[i].valor);
					}
					p.cliente = data.cliente;
					p.$w.find('[name=nomb]').html(data.cliente.nomb);
					if(data.cliente.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
					else p.$w.find('[name=apell]').html(data.cliente.appat+' '+data.cliente.apmat);
					p.$w.find('[name=dni]').html(data.cliente.docident[0].num);
					if(data.cliente.domicilios!=null) p.$w.find('[name=direc]').html(data.cliente.domicilios[0].direccion);
					/*Cajas*/
					var $select = p.$w.find('[name=caja]');
					if(data.cajas.length==0){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Rol no asignado',text: 'El trabajador no tiene cajas asignadas!',type: 'error'});
					}
					for(var i=0,j=data.cajas.length; i<j; i++){
						$select.append('<option value="'+data.cajas[i]._id.$id+'">'+data.cajas[i].nomb+'</option>')
						.find('option:last').data('data',data.cajas[i]);
					}
					$select.change(function(){
						$.post('cj/talo/get_caja','caja='+$(this).find('option:selected').val(),function(data){
							var $select = p.$w.find('[name=comp]').data('data',data).empty();
							for(var i=0,j=data.length; i<j; i++){
								if($select.find('[value='+data[i].tipo+']').length<=0)
									$select.append('<option value="'+data[i].tipo+'">'+cjTalo.types[data[i].tipo]+'</option>');
								if($select.find('option').length==3) i = j;
							}
							$select.unbind('change').change(function(){
								var $sel = p.$w.find('[name=serie]').empty(),
								talos = p.$w.find('[name=comp]').data('data'),
								$this = $(this);
								for(var i=0,j=talos.length; i<j; i++){
									if($this.find('option:selected').val()==talos[i].tipo)
										$sel.append('<option value="'+talos[i]._id.$id+'">'+talos[i].serie+'</option>')
										.find('option:last').data('data',talos[i]);
								}
								$sel.unbind('change').change(function(){
									p.$w.find('[name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					/*Servicios*/
					p.moneda = data.cuentas[0].moneda;
					p.total_all_cuentas = 0;
					p.total = 0;
					for(var i=0,j=data.cuentas.length; i<j; i++){
						var cuota_n = parseInt(data.cuentas[i].observ.substr(33,2));
						var renta = data.cuentas[i].operacion.arrendamiento.renta;
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(0)').html(i+1);
						$row.find('li:eq(1)').html(data.cuentas[i].servicio.nomb+' - Cuota '+cuota_n);
						//$row.find('li:eq(2)').html(ciHelper.formatMon(data.cuentas[i].saldo,p.moneda));
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="cuenta_'+data.cuentas[i]._id.$id+'" />');
						$row.find('.item').data('data',data.cuentas[i]).data('monto',data.cuentas[i].operacion.arrendamiento.renta);
						p.$w.find('.gridBody:eq(1)').append($row.children());
						/* RENTA */
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1)').html('&nbsp;&nbsp;&nbsp;<i>Renta del Inmueble</i>');
						$row.find('li:eq(3)').html(ciHelper.formatMon(renta,p.moneda));
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="'+data.cuentas[i]._id.$id+'" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						/* IGV */
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1)').html('&nbsp;&nbsp;&nbsp;<i>IGV</i>');
						$row.find('li:eq(3)').html(ciHelper.formatMon(p.igv*renta,p.moneda));
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="'+data.cuentas[i]._id.$id+'" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						/* MORA */
						var mora = p.mora,
						diferencia = ciHelper.dateDiffNow(data.cuentas[i].fecven);
						if(diferencia>0) porc = 0;
						else{
							var porc = (K.round(-diferencia/30,0)*2);
						}
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1)').html('&nbsp;&nbsp;&nbsp;<i>Moras</i>');
						$row.find('li:eq(2)').html('<input type="text" name="mora" size="7" value="'+porc+'"/>%');
						$row.find('[name=mora]').spinner({step: 2,min: 0,stop: function(){ $(this).change(); }})
						.val(porc).numeric().change(function(){
							p.calcTot();
						}).parent().find('.ui-button').css('height','14px');
						$row.find('li:eq(3)').html(ciHelper.formatMon(renta*(porc/100),p.moneda));
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="'+data.cuentas[i]._id.$id+'" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						/* TOTALES */
						p.total += renta + parseFloat(K.round(p.igv*renta,2)) + parseFloat(renta*(porc/100));
						p.total_all_cuentas+=p.total;
					}
					var $row = p.$w.find('.gridReference:eq(0)').clone();
					$row.find('li:eq(1),li:eq(2)').remove();
					$row.find('li:eq(0)').html('Total&nbsp;').css('min-width','350px').css('max-width','350px')
					.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
					$row.wrapInner('<a class="item" href="javascript: void(0);" name="totalGrid" />');
					p.$w.find('.gridBody:eq(1)').append($row.children());
					if(p.moneda=='D'){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1),li:eq(2)').remove();
						$row.find('li:eq(0)').html('Tasa de Cambio de Soles a D&oacute;lares&nbsp;').css('min-width','350px').css('max-width','350px')
						.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
						$row.find('li:eq(1)').html('<input type="text" name="tasa" size="7" value="0">');
						$row.find('[name=tasa]').spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }})
						.val(p.tasa).numeric().change(function(){
							p.calcTot();
						}).parent().find('.ui-button').css('height','14px');
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1),li:eq(2)').remove();
						$row.find('li:eq(0)').html('Total en Nuevos Soles').css('min-width','350px').css('max-width','350px')
						.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
						$row.wrapInner('<a class="item" href="javascript: void(0);" name="totalGridConv" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
					}
					p.calcTot();
					p.cuentas = data.cuentas;
					/*Efectivo Soles*/
					var $row = p.$w.find('.gridReference:last').clone();
					$row.find('li:eq(0)').html('Efectivo Soles');
					$row.find('li:eq(2)').html('S/.<input type="text" name="tot" size="7"/>');
					$row.find('[name=tot]').spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }})
					.val(0).numeric().change(function(){
						$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					}).parent().find('.ui-button').css('height','14px');
					$row.wrapInner('<a class="item" name="mon_sol" />');
					p.$w.find('.gridBody:last').append($row.children());
					/*Efectivo Dolares*/
					var $row = p.$w.find('.gridReference:last').clone();
					$row.find('li:eq(0)').html('Efectivo D&oacute;lares');
					$row.find('li:eq(2)').html('$<input type="text" name="tot" size="7"/>');
					$row.find('[name=tot]').spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }})
					.val(0).numeric().change(function(){
						$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon($(this).val()*p.tasa));
						$(this).closest('.item').data('total',parseFloat($(this).val())*p.tasa);
					}).parent().find('.ui-button').css('height','14px');
					$row.wrapInner('<a class="item" name="mon_dol" />');
					p.$w.find('.gridBody:last').append($row.children());
					/*Cuentas bancarios*/
					for(var i=0,j=data.ctban.length; i<j; i++){
						var $row = p.$w.find('.gridReference:last').clone();
						$row.find('li:eq(0)').html('Voucher <input type="text" name="voucher" size="7"/>');
						$row.find('li:eq(1)').html(data.ctban[i].nomb);
						$row.find('li:eq(2)').html((data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="text" name="tot" size="7"/>');
						$row.find('[name=tot]').spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }})
						.val(0).numeric().change(function(){
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon(tot));
							$(this).closest('.item').data('total',parseFloat(tot));
						}).parent().find('.ui-button').css('height','14px');
						$row.wrapInner('<a class="item" name="ctban" />');
						$row.find('.item').data('moneda',data.ctban[i].moneda).data('data',{
							_id: data.ctban[i]._id.$id,
							cod: data.ctban[i].cod,
							nomb: data.ctban[i].nomb,
							moneda: data.ctban[i].moneda,
							cod_banco: data.ctban[i].cod_banco
						});
						p.$w.find('.gridBody:last').append($row.children());
					}
					p.$w.find('.gridBody:last [name=tot]').change();
					if(p.total_all_cuentas>=700){
						K.notification({title:'Para Facturas',text:'Este documento excede los S/. 700'});
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowConfig: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowCjInmuConfig',
			title: 'Configurar Caja Inmuebles',
			contentURL: 'cj/cuen/conf_inmu',
			store: false,
			icon: 'ui-icon-gears',
			width: 510,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						cod: 'IN',
						ALQ: p.$w.find('[name=serv_alq]').data('data'),
						MOR: p.$w.find('[name=serv_mor]').data('data'),
						IGV: p.$w.find('[name=serv_igv]').data('data')
					};
					
					
					
					
					for(var i=0,j=p.$w.find('fieldset').length; i<j; i++){
						var $tmp = p.$w.find('fieldset').eq(i);
						eval('data.');
					}
					
					
					
					T0_ALQ
					
					
					
					
					
					if(data.ALQ==null){
						p.$w.find('[name=btnAlq]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe escoger un Concepto de Alquiler!',
							type: 'error'
						});
					}else
						data.ALQ = cjConc.dbRel(data.ALQ);
					if(data.MOR==null){
						p.$w.find('[name=btnMor]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe escoger un Concepto de Mora!',
							type: 'error'
						});
					}else
						data.MOR = cjConc.dbRel(data.MOR);
					if(data.IGV==null){
						p.$w.find('[name=btnIgv]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe escoger un Concepto de IGV!',
							type: 'error'
						});
					}else
						data.IGV = cjConc.dbRel(data.IGV);
					
					
					
					
					
					
					
					
					
					
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/cuen/save_config_inmu',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Configuracion ha sido actualizada con &eacute;xito!'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCjInmuConfig');
				K.block({$element: p.$w});
				p.$w.find('label').css('color','green');
				p.$w.find('[name=btnAlq]').click(function(){
					var $this = $(this);
					cjConc.windowSelect({callback: function(data){
						$this.closest('fieldset').find('[name=serv_alq]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnIgv]').click(function(){
					var $this = $(this);
					cjConc.windowSelect({callback: function(data){
						$this.closest('fieldset').find('[name=serv_igv]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMor]').click(function(){
					var $this = $(this);
					cjConc.windowSelect({callback: function(data){
						$this.closest('fieldset').find('[name=serv_mor]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('cj/cuen/get_config_inmu',function(data){
					if(data.T0_ALQ!=null){
						p.$w.find('fieldset:eq(0) [name=serv_alq]').html(data.T0_ALQ.nomb).data('data',data.T0_ALQ);
						p.$w.find('fieldset:eq(0) [name=serv_igv]').html(data.T0_IGV.nomb).data('data',data.T0_IGV);
						p.$w.find('fieldset:eq(0) [name=serv_mor]').html(data.T0_MOR.nomb).data('data',data.T0_MOR);
						p.$w.find('fieldset:eq(1) [name=serv_alq]').html(data.T1_ALQ.nomb).data('data',data.T1_ALQ);
						p.$w.find('fieldset:eq(1) [name=serv_igv]').html(data.T1_IGV.nomb).data('data',data.T1_IGV);
						p.$w.find('fieldset:eq(1) [name=serv_mor]').html(data.T1_MOR.nomb).data('data',data.T1_MOR);
						p.$w.find('fieldset:eq(2) [name=serv_alq]').html(data.T2_ALQ.nomb).data('data',data.T2_ALQ);
						p.$w.find('fieldset:eq(2) [name=serv_igv]').html(data.T2_IGV.nomb).data('data',data.T2_IGV);
						p.$w.find('fieldset:eq(2) [name=serv_mor]').html(data.T2_MOR.nomb).data('data',data.T2_MOR);
						p.$w.find('fieldset:eq(3) [name=serv_alq]').html(data.T3_ALQ.nomb).data('data',data.T3_ALQ);
						p.$w.find('fieldset:eq(3) [name=serv_igv]').html(data.T3_IGV.nomb).data('data',data.T3_IGV);
						p.$w.find('fieldset:eq(3) [name=serv_mor]').html(data.T3_MOR.nomb).data('data',data.T3_MOR);
						p.$w.find('fieldset:eq(4) [name=serv_alq]').html(data.T4_ALQ.nomb).data('data',data.T4_ALQ);
						p.$w.find('fieldset:eq(4) [name=serv_igv]').html(data.T4_IGV.nomb).data('data',data.T4_IGV);
						p.$w.find('fieldset:eq(4) [name=serv_mor]').html(data.T4_MOR.nomb).data('data',data.T4_MOR);
						p.$w.find('fieldset:eq(5) [name=serv_alq]').html(data.T5_ALQ.nomb).data('data',data.T5_ALQ);
						p.$w.find('fieldset:eq(5) [name=serv_igv]').html(data.T5_IGV.nomb).data('data',data.T5_IGV);
						p.$w.find('fieldset:eq(5) [name=serv_mor]').html(data.T5_MOR.nomb).data('data',data.T5_MOR);
						p.$w.find('fieldset:eq(6) [name=serv_alq]').html(data.T6_ALQ.nomb).data('data',data.T6_ALQ);
						p.$w.find('fieldset:eq(6) [name=serv_igv]').html(data.T6_IGV.nomb).data('data',data.T6_IGV);
						p.$w.find('fieldset:eq(6) [name=serv_mor]').html(data.T6_MOR.nomb).data('data',data.T6_MOR);
					}
					K.unblock({$element: p.$w});
				});
			}
		});
	}
};
define(
	function(){
		return cjInmu;
	}
);