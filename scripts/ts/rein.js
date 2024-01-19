tsRein = {
	states: {
		RG: {
			descr: "Registrado",
			color: "#CCC"
		},
		RB: {
			descr: "Recibido",
			color: "green"
		},
		X: {
			descr: "Anulado",
			color: "#000"
		},
		RE: {
			descr: "Registrado Libro Efectivo",
			color: "#01C3FF"
		},
		RC: {
			descr: "Registrado Libro Cta Cte",
			color: "#0082C0"
		}
	},
	modulo: {		
		"MH":"MOISES HERESI",
		"CM":"CEMENTERIO",
		"IN":"INMUEBLES"
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			modulo: item.modulo,
			cod: item.cod

		};
	},
	windowDetails: function(p){
		if(p.aprobar==null)
			p.buttons = {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			};
		else
			p.buttons = {
				"Aprobar": function(){
					K.sendingInfo();
					$.post('ts/rein/aprobar',{_id: K.tmp.data('id')},function(){
						K.clearNoti();
						K.notification({title: 'Recibo de Ingresos aprobado',text: 'La aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
						K.closeWindow(p.$w.attr('id'));
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			};
		new K.Window({
			id: 'windowDetailsRein'+p.id,
			title: 'Recibo de Ingresos: '+p.nomb,
			contentURL: 'ts/rein/details',
			icon: 'ui-icon-document',
			modal: p.modal?true:false,
			width: 650,
			height: 410,
			buttons: p.buttons,
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsRein'+p.id);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
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
				p.$w.find('.payment').eq(9).bind('scroll',function(){
					p.$w.find('.payment').eq(8).scrollLeft(p.$w.find('.payment').eq(9).scrollLeft());
				});
				p.$w.find('.payment').eq(11).bind('scroll',function(){
					p.$w.find('.payment').eq(10).scrollLeft(p.$w.find('.payment').eq(11).scrollLeft());
				});
				$.post('ts/rein/get','id='+p.id,function(data){
					p.$w.find('[name=num]').html(data.cod);
					p.$w.find('[name=fec]').html(ciHelper.dateFormatOnlyDay(data.fec));
					p.$w.find('[name=orga]').html(data.organizacion.nomb);
					p.$w.find('[name=respo]').html(ciHelper.enti.formatName(data.autor));
					for(var i=0,j=data.detalle.length; i<j; i++){
						var $row = p.$w.find('fieldset:eq(1) .gridReference').clone(),
						result = data.detalle[i];
						$row.find('li:eq(0)').html(result.cuenta.cod);
						$row.find('li:eq(1)').html(result.cuenta.descr);
						$row.find('li:eq(2)').html(result.comprobante.tipo+' '+result.comprobante.serie+' - '+result.comprobante.num);
						$row.find('li:eq(3)').html(result.concepto);
						$row.find('li:eq(4)').html(ciHelper.formatMon(result.monto));
						$row.wrapInner('<a class="item">');
						p.$w.find('fieldset:eq(1) .gridBody').append($row.children());
					}
					var $row = p.$w.find('fieldset:eq(1) .gridReference').clone();
					$row.find('li:eq(3)').html('Parcial').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
					$row.find('li:eq(4)').html(ciHelper.formatMon(data.total));
					$row.wrapInner('<a class="result">');
					p.$w.find('fieldset:eq(1) .gridBody').append($row.children());
					if(data.comprobantes_anulados!=null){
						for(var i=0,j=data.comprobantes_anulados.length; i<j; i++){
							var $row = p.$w.find('fieldset:eq(2) .gridReference').clone(),
							result = data.comprobantes_anulados[i];
							$row.find('li:eq(0)').html(result.tipo+' '+result.serie+' '+result.num);
							$row.wrapInner('<a class="item">');
							p.$w.find('fieldset:eq(2) .gridBody').append($row.children());
						}
					}
					var $row = p.$w.find('fieldset:eq(3) .gridReference').clone();
					$row.find('li:eq(0)').html(data.organizacion.funcion.cod);
					$row.find('li:eq(1)').html(data.organizacion.programa.cod);
					$row.find('li:eq(2)').html(data.organizacion.subprograma.cod);
					$row.find('li:eq(3)').html(data.organizacion.actividad.cod);
					$row.find('li:eq(4)').html(data.organizacion.componente.cod);
					$row.find('li:eq(5)').html(data.fuente.cod);
					$row.wrapInner('<a class="item">');
					p.$w.find('fieldset:eq(3) .gridBody').append($row.children());
					var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
					$row.find('li:eq(0)').html('Efectivo Soles');
					$row.find('li:eq(2)').html(ciHelper.formatMon(data.efectivos[0].monto));
					$row.find('li:eq(3)').html(ciHelper.formatMon(data.efectivos[0].monto));
					$row.wrapInner('<a class="item">');
					p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
					var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
					$row.find('li:eq(0)').html('Efectivo D&oacute;lares');
					if(parseFloat(data.efectivos[1].monto)==0) $row.find('li:eq(2)').html(ciHelper.formatMon(0));
					else $row.find('li:eq(2)').html(ciHelper.formatMon(data.efectivos[1].monto/data.efectivos[1].tc,'D'));
					$row.find('li:eq(3)').html(ciHelper.formatMon(data.efectivos[1].monto));
					$row.wrapInner('<a class="item">');
					p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
					if(data.vouchers!=null){
						for(var k=0,j=data.vouchers.length; k<j; k++){
							var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
							$row.find('li:eq(0)').html('Voucher - '+data.vouchers[k].num);
							$row.find('li:eq(1)').html(data.vouchers[k].cuenta_banco.nomb);
							$row.find('li:eq(2)').html(ciHelper.formatMon(data.vouchers[k].monto,data.vouchers[k].cuenta_banco.moneda));
							$row.find('li:eq(3)').html(ciHelper.formatMon((data.vouchers[k].cuenta_banco.moneda=='S')?data.vouchers[k].monto:parseFloat(data.vouchers[k].monto)*parseFloat(data.vouchers[k].tc)));
							$row.find('li:eq(4)').html(ciHelper.enti.formatName(data.vouchers[k].cliente));
							$row.wrapInner('<a class="item">');
							p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
						}
					}
					for(var i=0,j=data.cont_patrimonial.length; i<j; i++){
						var result = data.cont_patrimonial[i];
						if(result.tipo=='D'){
							var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
							$row.find('li:eq(0)').html(result.cuenta.cod);
							$row.find('li:eq(1)').html(result.cuenta.descr);
							$row.find('li:eq(2)').html(ciHelper.formatMon(result.monto,result.moneda));
							$row.wrapInner('<a class="item">');
							p.$w.find('.payment:eq(9) .gridBody').append($row.children());
						}else{
							var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
							$row.find('li:eq(0)').html(result.cuenta.cod);
							$row.find('li:eq(1)').html(result.cuenta.descr);
							$row.find('li:eq(2)').html(ciHelper.formatMon(result.monto,result.moneda));
							$row.wrapInner('<a class="item">');
							p.$w.find('.payment:eq(11) .gridBody').append($row.children());
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowRec: function(p){
		tsRein.windowDetails({
			id: p.id,
			nomb: p.nomb,
			aprobar: true,
			modal: true
		});
	},
	windowMov: function(p){
		new K.Window({
			id: 'windowMovRein'+p.id,
			title: 'Registrar en Libro Movimientos del Efectivo',
			contentURL: 'ts/rein/mov',
			icon: 'ui-icon-document',
			width: 650,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						cod: p.cod,
						fec: p.fec,
						items: []
					};
					for(var i=0,j=p.$w.find('[name=descr]').length; i<j; i++){
						if(p.$w.find('[name=descr]').eq(i).val()==''){
							p.$w.find('[name=descr]').eq(i).focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
						}
						var tmp = p.$w.find('.payment:eq(3) .item').eq(i).data('data');
						data.items.push({
							cuenta: {
								_id: tmp.cuenta._id.$id,
								cod: tmp.cuenta.cod,
								descr: tmp.cuenta.descr
							},
							monto: tmp.monto,
							descr: p.$w.find('[name=descr]').eq(i).val()
						});
					}
					K.sendingInfo();
					$.post('ts/rein/save_mov',data,function(rpta){
						K.clearNoti();
						if(rpta.error!=true){
							K.notification({title: 'Generaci&oacute;n de Movimiento Efectivo',text: 'La generaci&oacute;n de movimiento efectivo se realiz&oacute; con &eacute;xito!'});
							$('#pageWrapperLeft .ui-state-highlight').click();
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.closeWindow(p.$w.attr('id'));
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe crear un saldo inicial del Efectivo!',
								type: 'error'
							});
						}
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowMovRein'+p.id);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
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
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('.payment').eq(3).bind('scroll',function(){
					p.$w.find('.payment').eq(2).scrollLeft(p.$w.find('.payment').eq(3).scrollLeft());
				});
				$.post('ts/rein/get','id='+p.id,function(data){
					p.cod = data.cod;
					p.fec = ciHelper.dateFormatBDNotHour(data.fec);
					p.$w.find('[name=num]').html(data.cod);
					p.$w.find('[name=fec]').html(ciHelper.dateFormatOnlyDay(data.fec));
					p.$w.find('[name=orga]').html(data.organizacion.nomb);
					p.$w.find('[name=respo]').html(ciHelper.enti.formatName(data.autor));
					for(var i=0,j=data.cont_patrimonial.length; i<j; i++){
						var result = data.cont_patrimonial[i];
						if(result.tipo=='D'){
							var $row = p.$w.find('.payment:eq(1) .gridReference').clone();
							$row.find('li:eq(0)').html(result.cuenta.cod);
							$row.find('li:eq(1)').html(result.cuenta.descr);
							$row.find('li:eq(2)').html(ciHelper.formatMon(result.monto,result.moneda));
							$row.wrapInner('<a class="item">');
							$row.find('.item').data('data',result);
							p.$w.find('.payment:eq(1) .gridBody').append($row.children());
						}else{
							var $row = p.$w.find('.payment:eq(3) .gridReference').clone();
							$row.find('li:eq(0)').html(result.cuenta.cod);
							$row.find('li:eq(1)').html(result.cuenta.descr);
							$row.find('li:eq(2)').html(ciHelper.formatMon(result.monto,result.moneda));
							$row.find('li:eq(3)').html('<input type="text" name="descr" size="30"/>');
							$row.wrapInner('<a class="item">');
							$row.find('.item').data('data',result);
							p.$w.find('.payment:eq(3) .gridBody').append($row.children());
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowCta: function(p){
		new K.Window({
			id: 'windowCtaRein'+p.id,
			title: 'Generar Movimiento en Libro de Cta. Corriente',
			contentURL: 'ts/rein/cta',
			icon: 'ui-icon-document',
			width: 650,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						fecreg: ciHelper.date.format.dbNotHour(p.rein.fecreg),
						fecdep: p.$w.find('[name=fecdep]').val(),
						efectivo: [],
						cont_patrimonial: []
					};
					if(data.fecdep==''){
						p.$w.find('[name=fecdep]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de dep&oacute;sito!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.payment:eq(0) .gridBody .item').length; i<j; i++){
						var $row = p.$w.find('.payment:eq(0) .gridBody .item').eq(i),
						cban = $row.find('[name=ctban] option:selected').data('data'),
						tmed = $row.find('[name=tmed] option:selected').data('data'),
						tmp = {
							voucher: $row.find('[name=voucher]').val(),
							descr: $row.find('[name=descr]').val(),
							medio: {
								_id: tmed._id.$id,
								cod: tmed.cod,
								descr: tmed.descr
							},
							cuenta_banco: {
								_id: cban._id.$id,
								cod: cban.cod,
								nomb: cban.nomb
							},
							monto_original: p.rein.efectivos[i].monto,
							monto: (p.rein.efectivos[i].moneda=='D')?(parseFloat(p.rein.efectivos[1].monto)*parseFloat(p.rein.efectivos[i].tc)):p.rein.efectivos[i].monto
						};
						if(tmp.voucher==''){
							$row.find('[name=voucher]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un voucher!',type: 'error'});
						}
						if(tmp.descr==''){
							$row.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
						}
						if(i==0) tmp.tipo = 'S';
						else{
							data.tc = p.$w.find('[name=tc]').val();
							tmp.tipo = 'D';
						}
						data.efectivo.push(tmp);
					}
					for(var i=0,j=p.$w.find('.payment:eq(1) .gridBody .item').length; i<j; i++){
						var $row = p.$w.find('.payment:eq(1) .gridBody .item').eq(i),
						cuenta = $row.data('data');
						//tmed = $row.find('[name=tmed] option:selected').data('data'),
						if(cuenta!=null){
							cuenta = cuenta.cuenta;
							var tmp = {
								cuenta: {
									_id: cuenta._id.$id,
									cod: cuenta.cod,
									descr: cuenta.descr
								},
								monto: p.rein.efectivos[i].monto,
								descr: $row.find('[name=descr]').val()
							};
							if(tmp.descr==''){
								$row.find('[name=descr]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
							}
							data.cont_patrimonial.push(tmp);
						}
					}
					/*
					 * Se dise�a el array de vouchers
					 * */
					var cban = [],
					//voucher = null;//[];
					voucher = [];
					//console.log(voucher);
					for(var i=0,j=p.$w.find('.payment:eq(3) .gridBody .item').length; i<j; i++){
						var $row = p.$w.find('.payment:eq(3) .gridBody .item').eq(i),
						tmp = $row.data('data'),
						index = $.inArray(tmp.cuenta_banco._id,cban);
						/*console.info(tmp);
						console.log(cban);*/
						/*tmp.fecs = [$row.find('[name=fec]').val()];
						if(tmp.fecs[0]==''){
							$row.find('[name=fec]').datepicker('show');
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de detracci&oacute;n para el voucher!',type: 'error'});
						}*/
						//console.log('--');
						//var tmp2 = voucher;
						//console.log(tmp2);
						var tmed = p.$w.find('[name=section5] [name=tmed] option:selected').data('data'),
						cuenta = p.$w.find('[name=section5] [name=cuenta]').data('data');
						if(cuenta==null){
							p.$w.find('[name=section5] [name=btnCta]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar una cuenta contable para el voucher!',
								type: 'error'
							});
						}
						tmp.descr = p.$w.find('[name=section5] [name=descr]').val();
						tmp.medio = {
							_id: tmed._id.$id,
							cod: tmed.cod,
							descr: tmed.descr
						};
						tmp.cuenta = {
							_id: cuenta._id.$id,
							cod: cuenta.cod,
							descr: cuenta.descr
						};
						if(index==-1){
							cban.push(tmp.cuenta_banco._id);
							voucher.push(tmp);
						}else{
							voucher[index].docs.push(tmp.docs[0]);
							//voucher[index].fecs.push(tmp.fecs[0]);
							voucher[index].monto.push(tmp.monto[0]);
						}
						//console.log(voucher);
					}
					if(voucher.length!=0) data.vouchers = voucher;
					K.sendingInfo();
					//console.info(data);
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/rein/save_cta',data,function(rpta){
						K.clearNoti();
						if(rpta.error==null){
							K.notification({title: 'Generaci&oacute;n de Movimiento en Cta. Corriente',text: 'La generaci&oacute;n de movimiento en Cta. Corriente se realiz&oacute; con &eacute;xito!'});
							$('#pageWrapperLeft .ui-state-highlight').click();
							K.closeWindow(p.$w.attr('id'));
						}else if(rpta.error=='1'){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe crear un saldo inicial del Efectivo!',
								type: 'error'
							});
						}else if(rpta.error=='2'){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe crear un saldo inicial del Banco para la cuenta correspondiente al efectivo!',
								type: 'error'
							});
						}
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCtaRein'+p.id);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
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
				p.$w.find('.payment:eq(0) .grid').eq(1).bind('scroll',function(){
					p.$w.find('.payment:eq(0) .grid').eq(0).scrollLeft(p.$w.find('.payment:eq(0) .grid').eq(1).scrollLeft());
				});
				p.$w.find('.payment:eq(1) .grid').eq(1).bind('scroll',function(){
					p.$w.find('.payment:eq(1) .grid').eq(0).scrollLeft(p.$w.find('.payment:eq(1) .grid').eq(1).scrollLeft());
				});
				p.$w.find('.payment:eq(2) .grid').eq(1).bind('scroll',function(){
					p.$w.find('.payment:eq(2) .grid').eq(0).scrollLeft(p.$w.find('.payment:eq(2) .grid').eq(1).scrollLeft());
				});
				p.$w.find('.payment:eq(3) .grid').eq(1).bind('scroll',function(){
					p.$w.find('.payment:eq(3) .grid').eq(0).scrollLeft(p.$w.find('.payment:eq(3) .grid').eq(1).scrollLeft());
				});
				p.$w.find('[name=fecdep]').datepicker();
				$.post('ts/rein/get_cta','id='+p.id,function(data){
					p.rein = data.rein;
					p.tmed = data.tmed;
					p.ctban = data.ctban;
					if(p.rein.tc==null) p.rein.tc = parseFloat(data.tc.valor);
					if(p.ctban==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe existir al menos una cuenta bancaria!',type: 'info'});
					}
					if(p.tmed==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe existir al menos tipo de medio de pago!',type: 'info'});
					}
					p.$w.find('[name=fec]').html(ciHelper.dateFormat(p.rein.fec));
					p.$w.find('[name=orga]').html(p.rein.organizacion.nomb);
					p.$w.find('[name=respo]').html(ciHelper.enti.formatName(p.rein.autor));
					/*
					 * Comprobar si hay pagos en soles o dolares
					 * */
					var sol = false,dol = false;
					if(parseFloat(p.rein.efectivos[0].monto)!=0) sol = true;
					if(parseFloat(p.rein.efectivos[1].monto)!=0) dol = true;
					if(p.rein.vouchers!=null){
						for(var i=0,j=p.rein.vouchers.length; i<j; i++){
							if(p.rein.vouchers[i].cuenta_banco.moneda=='S') sol = true;
							else if(p.rein.vouchers[i].cuenta_banco.moneda=='D') dol = true;
						}
					}
					if(sol==true){
						var $row = p.$w.find('.payment:eq(0) .gridReference').clone(),
						$cbo = null;
						$row.find('li:eq(0)').html('Efectivo Soles');
						$row.find('li:eq(1)').html(ciHelper.formatMon(p.rein.efectivos[0].monto));
						$row.find('li:eq(2)').html(ciHelper.formatMon(p.rein.efectivos[0].monto));
						$row.find('li:eq(3)').html('<select name="ctban">');
						$row.find('li:eq(4)').html('<input type="text" name="voucher" size="22" />');
						$row.find('li:eq(5)').html('<select name="tmed">');
						$row.find('li:eq(6)').html('<input type="text" name="descr" size="22" />');
						$cbo = $row.find('[name=ctban]');
						for(var i=0,j=p.ctban.length; i<j; i++){
							if(p.ctban[i].moneda=='S'){
								$cbo.append('<option value="'+p.ctban[i]._id.$id+'">'+p.ctban[i].cod+'</option>');
								$cbo.find('option:last').data('data',p.ctban[i]);
							}
						}
						if($row.find('[name=ctban] option').length==0){
							K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe habilitar cuentas bancarias en nuevos soles!',type: 'info'});
							return K.closeWindow(p.$w.attr('id'));
						}
						$cbo = $row.find('[name=tmed]');
						for(var i=0,j=p.tmed.length; i<j; i++){
							$cbo.append('<option value="'+p.tmed[i]._id.$id+'">'+p.tmed[i].cod+'</option>');
							$cbo.find('option:last').data('data',p.tmed[i]);
						}
						$row.wrapInner('<a class="item">');
						p.$w.find('.payment:eq(0) .gridBody').append($row.children());
					}
					if(dol==true){
						var $row = p.$w.find('.payment:eq(0) .gridReference').clone(),
						$cbo = null;
						$row.find('li:eq(0)').html('Efectivo D&oacute;lares');
						$row.find('li:eq(1)').html(ciHelper.formatMon(p.rein.efectivos[1].monto,'D'));
						$row.find('li:eq(2)').html(ciHelper.formatMon(parseFloat(p.rein.efectivos[1].monto)*parseFloat(p.rein.tc)));
						$row.find('li:eq(3)').html('<select name="ctban">');
						$row.find('li:eq(4)').html('<input type="text" name="voucher" size="22" />');
						$row.find('li:eq(5)').html('<select name="tmed">');
						$row.find('li:eq(6)').html('<input type="text" name="descr" size="22" />');
						$cbo = $row.find('[name=ctban]');
						for(var i=0,j=p.ctban.length; i<j; i++){
							if(p.ctban[i].moneda=='D'){
								$cbo.append('<option value="'+p.ctban[i]._id.$id+'">'+p.ctban[i].cod+'</option>');
								$cbo.find('option:last').data('data',p.ctban[i]);
							}
						}
						if($row.find('[name=ctban] option').length==0){
							K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe habilitar cuentas bancarias en d&oacute;lares!',type: 'info'});
							return K.closeWindow(p.$w.attr('id'));
						}
						$cbo = $row.find('[name=tmed]');
						for(var i=0,j=p.tmed.length; i<j; i++){
							$cbo.append('<option value="'+p.tmed[i]._id.$id+'">'+p.tmed[i].cod+'</option>');
							$cbo.find('option:last').data('data',p.tmed[i]);
						}
						$row.wrapInner('<a class="item">');
						p.$w.find('.payment:eq(0) .gridBody').append($row.children());
					}
					p.$w.find('[name=section4]').hide();
					if(dol==false){
						var total = 0;
						p.$w.find('[name=section4]').hide();
						for(var i=0,j=p.rein.cont_patrimonial.length; i<j; i++){
							if(p.rein.cont_patrimonial[i].tipo=='H'){
								var $row = p.$w.find('.payment:eq(1) .gridReference').clone(),
								result = p.rein.cont_patrimonial[i],
								comps = [];
								$row.find('li:eq(0)').html(result.cuenta.cod);
								$row.find('li:eq(1)').html(result.cuenta.descr);
								$row.find('li:eq(2)').html(ciHelper.formatMon(result.monto));
								for(var k=0,l=p.rein.detalle.length; k<l; k++){
									var comp = p.rein.detalle[k].comprobante.tipo+' '+p.rein.detalle[k].comprobante.serie+'-'+p.rein.detalle[k].comprobante.num;
									if($.inArray(comp,comps)==-1){
										//if(p.rein.detalle[k].cuenta._id.$id==result.cuenta._id.$id||p.rein.detalle[k].cuenta.cod.indexOf(result.cuenta.cod)==0){
										if(p.rein.detalle[k].cuenta.cod.indexOf(result.cuenta.cod)==0){
											comps.push(comp);
											if($row.find('li:eq(3)').html()!='')
												$row.find('li:eq(3)').append(', ');
											$row.find('li:eq(3)').append(comp);
										}
									}
								}
								$row.find('li:eq(4)').html('<input type="text" name="descr" size="22" />');
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('total',parseFloat(result.monto)).data('data',result);
								p.$w.find('.payment:eq(1) .gridBody').append($row.children());
								total += parseFloat(result.monto);
							}
						}
						var $row = p.$w.find('.payment:eq(1) .gridReference').clone();
						$row.find('li:eq(2)').html(ciHelper.formatMon(total));
						$row.wrapInner('<a class="item total">');
						p.$w.find('.payment:eq(1) .gridBody').append($row.children());
					}else{
						for(var i=0,j=p.rein.cont_patrimonial.length; i<j; i++){
							if(p.rein.cont_patrimonial[i].tipo=='H'){
								var $row = p.$w.find('.payment:eq(1) .gridReference').clone(),
								result = p.rein.cont_patrimonial[i],
								comps = [];
								$row.find('li:eq(0)').html(result.cuenta.cod);
								$row.find('li:eq(1)').html(result.cuenta.descr);
								$row.find('li:eq(2)').html('<input type="text" name="monto" size="6" />');
								$row.find('[name=monto]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
									$(this).change();
								}}).change(function(){
									var total = 0;
									p.$w.find('.payment:eq(1) .gridBody [name=monto]').each(function(i){
										total += parseFloat($(this).val());
									});
									p.$w.find('.payment:eq(1) .total li:eq(2)').html(ciHelper.formatMon(total));
								}).change();
								$row.find('[name=monto]').parent().find('.ui-button').height('14px');
								for(var k=0,l=p.rein.detalle.length; k<l; k++){
									var comp = p.rein.detalle[k].comprobante.tipo+' '+p.rein.detalle[k].comprobante.serie+'-'+p.rein.detalle[k].comprobante.num;
									if($.inArray(comp,comps)==-1){
										//if(p.rein.detalle[k].cuenta._id.$id==result.cuenta._id.$id||p.rein.detalle[k].cuenta.cod.indexOf(result.cuenta.cod)==0){
										if(p.rein.detalle[k].cuenta.cod.indexOf(result.cuenta.cod)==0){
											comps.push(comp);
											if($row.find('li:eq(3)').html()!='')
												$row.find('li:eq(3)').append(', ');
											$row.find('li:eq(3)').append(comp);
										}
									}
								}
								$row.find('li:eq(4)').html('<input type="text" name="descr" size="22" />');
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',result);
								p.$w.find('.payment:eq(1) .gridBody').append($row.children());
							}
						}
						var $row = p.$w.find('.payment:eq(1) .gridReference').clone();
						$row.find('li:eq(2)').html(ciHelper.formatMon(0));
						$row.wrapInner('<a class="item total">');
						p.$w.find('.payment:eq(1) .gridBody').append($row.children());
						p.$w.find('[name=tc]').val(p.rein.tc).numeric().spinner({step: 0.1,min: 0,stop: function(){
							$(this).change();
						}}).change(function(){
							p.$w.find('.payment:eq(2) [name=monto]').change();
						}).change();
						p.$w.find('[name=tc]').parent().find('.ui-button').height('14px');
						for(var i=0,j=p.rein.cont_patrimonial.length; i<j; i++){
							if(p.rein.cont_patrimonial[i].tipo=='H'){
								var $row = p.$w.find('.payment:eq(2) .gridReference').clone(),
								result = p.rein.cont_patrimonial[i],
								comps = [];
								$row.find('li:eq(0)').html(result.cuenta.cod);
								$row.find('li:eq(1)').html(result.cuenta.descr);
								$row.find('li:eq(2)').html('<input type="text" name="monto" size="6" />');
								$row.find('[name=monto]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
									$(this).change();
								}}).change(function(){
									var $this = $(this),
									total = 0,
									$row = $this.closest('.item');
									$row.find('li:eq(3)').html(ciHelper.formatMon(parseFloat($this.val())*parseFloat(p.$w.find('[name=tc]').val())));
									p.$w.find('.payment:eq(2) .gridBody [name=monto]').each(function(i){
										total += parseFloat($(this).val());
									});
									p.$w.find('.payment:eq(2) .total li:eq(2)').html(ciHelper.formatMon(total,'D'));
									p.$w.find('.payment:eq(2) .total li:eq(3)').html(ciHelper.formatMon(total*parseFloat(p.$w.find('[name=tc]').val())));
								}).change();
								$row.find('[name=monto]').parent().find('.ui-button').height('14px');
								for(var k=0,l=p.rein.detalle.length; k<l; k++){
									var comp = p.rein.detalle[k].comprobante.tipo+' '+p.rein.detalle[k].comprobante.serie+'-'+p.rein.detalle[k].comprobante.num;
									if($.inArray(comp,comps)==-1){
										//if(p.rein.detalle[k].cuenta._id.$id==result.cuenta._id.$id||p.rein.detalle[k].cuenta.cod.indexOf(result.cuenta.cod)==0){
										if(p.rein.detalle[k].cuenta.cod.indexOf(result.cuenta.cod)==0){
											comps.push(comp);
											if($row.find('li:eq(4)').html()!='')
												$row.find('li:eq(4)').append(', ');
											$row.find('li:eq(4)').append(comp);
										}
									}
								}
								$row.find('li:eq(5)').html('<input type="text" name="descr" size="22" />');
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',result);
								p.$w.find('.payment:eq(2) .gridBody').append($row.children());
							}
						}
						var $row = p.$w.find('.payment:eq(2) .gridReference').clone();
						$row.find('li:eq(2)').html(ciHelper.formatMon(0,'D'));
						$row.find('li:eq(3)').html(ciHelper.formatMon(0));
						$row.wrapInner('<a class="item total">');
						p.$w.find('.payment:eq(2) .gridBody').append($row.children());
					}
					if(p.rein.vouchers!=null){
						if(p.rein.vouchers.length!=0){
							p.$w.find('[name=section5] .gridHeader li:eq(6),[name=section5] .gridHeader li:eq(5)').remove();
							p.$w.find('[name=section5] .gridReference li:eq(6),[name=section5] .gridReference li:eq(5)').remove();
							var $table = $('<table>');
							$table.append('<tr>');
							$table.find('tr:last').append('<td><span>Cuenta Contable</span></td>');
							$table.find('tr:last').append('<td><span name="cuenta"></span>&nbsp;<button name="btnCta">Seleccionar</button></td>');
							$table.find('[name=btnCta]').click(function(){
								var $this = $(this);
								ctPcon.windowSelect({callback: function(data){
									$this.closest('table').find('[name=cuenta]').html(data.cod).data('data',data);
									$this.closest('table').find('[name=btnCta]').button('option','text',false);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							$table.append('<tr>');
							$table.find('tr:last').append('<td><span>Tipo de Medio de Pago</span></td>');
							$table.find('tr:last').append('<td><select name="tmed"></select></td>');
							var $cbo = $table.find('[name=tmed]');
							for(var i=0,j=p.tmed.length; i<j; i++){
								$cbo.append('<option value="'+p.tmed[i]._id.$id+'">'+p.tmed[i].cod+'</option>');
								$cbo.find('option:last').data('data',p.tmed[i]);
							}
							$table.append('<tr>');
							$table.find('tr:last').append('<td><span>Descripci&oacute;n</span></td>');
							$table.find('tr:last').append('<td><textarea name="tmed" rows="2" cols="15"></textarea></td>');
							p.$w.find('[name=section5] legend').after($table);
							for(var i=0,j=p.rein.vouchers.length; i<j; i++){
								var $row = p.$w.find('.payment:eq(3) .gridReference').clone(),
								result = p.rein.vouchers[i],
								cuenta = null;
								console.log(result);
								$row.find('li:eq(0)').html('Voucher '+result.num);
								$row.find('li:eq(1)').html(result.cuenta_banco.nomb);
								$row.find('li:eq(2)').html(ciHelper.formatMon(result.monto,result.cuenta_banco.moneda));
								if(result.cuenta_banco.moneda=='D')
									$row.find('li:eq(3)').html(ciHelper.formatMon(parseFloat(result.monto)*parseFloat(result.tc)));
								else
									$row.find('li:eq(3)').html(ciHelper.formatMon(result.monto));
								$row.find('li:eq(4)').html(ciHelper.enti.formatName(result.cliente));
								//$row.find('li:eq(5)').html('<input type="text" name="fec" size="10" />');
								//$row.find('[name=fec]').datepicker();
								/*for(var k=0,l=p.ctban.length; k<l; k++){
									if(p.ctban[k]._id.$id==result.cuenta_banco._id.$id){
										$row.find('li:eq(6)').html(p.ctban[k].cuenta.cod);
										cuenta = p.ctban[k].cuenta;
										k = l;
									}
								}*/
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',{
									docs: [result.num],
									cliente: ciHelper.enti.dbRel(result.cliente),
									monto: [parseFloat(result.monto)],
									/*cuenta: {
										_id: cuenta._id.$id,
										cod: cuenta.cod,
										descr: cuenta.descr
									},*/
									cuenta_banco: {
										_id: result.cuenta_banco._id.$id,
										cod: result.cuenta_banco.cod,
										cod_banco: result.cuenta_banco.cod_banco,
										nomb: result.cuenta_banco.nomb,
										moneda: result.cuenta_banco.moneda
									}
								});
								p.$w.find('.payment:eq(3) .gridBody').append($row.children());
							}
						}
					}else p.$w.find('[name=section5]').hide();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			width: 510,
			height: 350,
			title: 'Seleccionar Recibo de Ingresos',
			buttons: {
				'Seleccionar': function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Recibo de Ingresos!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				var params = {};
				if(p.modulo!=null) params.modulo = p.modulo;
				p.$grid = new K.grid({
					$el: p.$w,
					cols: ['','Fecha','Organizaci&oacute;n'],
					data: 'cj/rein/lista',
					params: params,
					itemdescr: 'recibo(s) de ingresos',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						if(data.fecfin!=null){
							if(ciHelper.date.format.bd_ymd(data.fec)!=ciHelper.date.format.bd_ymd(data.fecfin)){
								$row.find('td:last').append(' - '+ciHelper.date.format.bd_ymd(data.fecfin));
							}
						}
						$row.append('<td>'+data.organizacion.nomb+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
						}).contextMenu('conMenListSel', {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								return menu;
							},
							bindings: {
								'conMenListSel_sel': function(t){
									p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowSelect_2: function(p){
		new K.Modal({
			id: 'windowSelect_2',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Recibo de Ingresos',
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
				p.$w = $('#windowSelect_2');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Codigo','Modulo'],
					data: 'cj/rein/lista',
					params: {},
					itemdescr: 'Recibo(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+tsRein.modulo[data.modulo]+'</td>');
						
						
						
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