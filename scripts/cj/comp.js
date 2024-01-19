/*******************************************************************************
								comprobantes
*******************************************************************************/
cjComp = {
	tipo: {
	    R: 'Recibo de Caja',
	    B: 'Boleta de Venta',
	    F: 'Factura'
	},
	states: {
		R: {
			descr: "Registrado",
			color: "blue"
		},
		X: {
			descr: "Anulado",
			color: "black"
		},
		P: {
			descr: "Pendiente",
			color: "gray"
		},
		C: {
			descr: "Reemplazado",
			color: "yellow"
		}
	},
	windowAnular: function(p){
		new K.Modal({
			id: 'windowCompAnul'+p.id,
			title: 'Anular Comprobante '+p.nomb,
			contentURL: 'cj/comp/anul',
			icon: 'ui-icon-circle-close',
			width: 675,
			height: 410,
			buttons: {
				"Anular": function(){
					K.clearNoti();
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/comp/save_anul',{_id: p.id},function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Comprobante fue anulado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCompAnul'+p.id);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
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
				$.post('cj/comp/get','id='+p.id,function(data){
					data.tc = parseFloat(data.tc);
					/*Cliente*/
					p.$w.find('[name=nomb]').html(data.cliente.nomb);
					if(data.cliente.tipo_enti=='E') p.$w.find('[name=apell]').closest('tr').hide();
					else p.$w.find('[name=apell]').html(data.cliente.appat+' '+data.cliente.apmat);
					p.$w.find('[name=dni]').html(data.cliente.docident[0].num);
					if(data.cliente.domicilios!=null) p.$w.find('[name=direc]').html(data.cliente.domicilios[0].direccion);
					/*Cajas*/
					p.$w.find('[name=caja]').html(data.caja.nomb);
					p.$w.find('[name=comp]').html(cjTalo.types[data.tipo]);
					p.$w.find('[name=serie]').html(data.serie);
					p.$w.find('[name=num]').html(data.num);
					/*Servicios*/
					var total = 0;
					for(var i=0,j=data.items.length; i<j; i++){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(0)').html(i+1);
						$row.find('li:eq(1)').html(data.items[i].cuenta_cobrar.servicio.nomb);
						$row.find('li:eq(2)').html(ciHelper.formatMon(data.items[i].cuenta_cobrar.total,p.moneda));
						$row.wrapInner('<a class="item" name="'+data.items[i].cuenta_cobrar._id.$id+'" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						var tot = 0;
						for(var k=0,l=data.items[i].conceptos.length; k<l; k++){
							var $row = p.$w.find('.gridReference:eq(0)').clone();
							$row.find('li:eq(1)').html('&nbsp;&nbsp;&nbsp;<i>'+data.items[i].conceptos[k].concepto.nomb+'</i>');
							$row.find('li:eq(2)').html(ciHelper.formatMon(data.items[i].cuenta_cobrar.conceptos[k].monto,data.items[i].cuenta_cobrar.moneda));
							$row.find('li:eq(3)').html(ciHelper.formatMon(data.items[i].conceptos[k].monto*(data.items[i].cuenta_cobrar.moneda=='S'?1:data.tc)));
							$row.wrapInner('<a class="item" />');
							p.$w.find('.gridBody:eq(1)').append($row.children());
							tot += data.items[i].conceptos[k].monto*(data.items[i].cuenta_cobrar.moneda=='S'?1:data.tc);
							total += data.items[i].conceptos[k].monto*(data.items[i].cuenta_cobrar.moneda=='S'?1:data.tc);
						}
						p.$w.find('[name='+data.items[i].cuenta_cobrar._id.$id+'] li:eq(3)').html(ciHelper.formatMon(tot));
					}
					var $row = p.$w.find('.gridReference:eq(0)').clone();
					$row.find('li:eq(1),li:eq(2)').remove();
					$row.find('li:eq(0)').html('Total&nbsp;').css('min-width','350px').css('max-width','350px')
					.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
					$row.find('li:eq(1)').html(ciHelper.formatMon(total,data.moneda));
					$row.wrapInner('<a class="item" />');
					p.$w.find('.gridBody:eq(1)').append($row.children());
					if(p.moneda=='D'){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1),li:eq(2)').remove();
						$row.find('li:eq(0)').html('Tasa de Cambio de Soles a D&oacute;lares&nbsp;').css('min-width','350px').css('max-width','350px')
						.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
						$row.find('li:eq(1)').html(ciHelper.formatMon(data.tc));
						$row.wrapInner('<a class="item" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(1),li:eq(2)').remove();
						$row.find('li:eq(0)').html('Total en Nuevos Soles').css('min-width','350px').css('max-width','350px')
						.css('text-align','right').addClass('ui-button ui-widget ui-state-default');
						$row.find('li:eq(1)').html(ciHelper.formatMon(total*data.tc));
						$row.wrapInner('<a class="item" />');
						p.$w.find('.gridBody:eq(1)').append($row.children());
					}
					/*Efectivo Soles*/
					if(parseFloat(data.efectivos[0].monto)!=0){
						var $row = p.$w.find('.gridReference:last').clone();
						$row.find('li:eq(0)').html('Efectivo Soles');
						$row.find('li:eq(2)').html(ciHelper.formatMon(data.efectivos[0].monto));
						$row.find('li:eq(3)').html(ciHelper.formatMon(data.efectivos[0].monto));
						$row.wrapInner('<a class="item" />');
						p.$w.find('.gridBody:last').append($row.children());
					}
					/*Efectivo Dolares*/
					if(parseFloat(data.efectivos[1].monto)!=0){
						var $row = p.$w.find('.gridReference:last').clone();
						$row.find('li:eq(0)').html('Efectivo D&oacute;lares');
						$row.find('li:eq(2)').html(ciHelper.formatMon(data.efectivos[1].monto,'D'));
						$row.find('li:eq(3)').html(ciHelper.formatMon(parseFloat(data.efectivos[1].monto)*data.tc));
						$row.wrapInner('<a class="item" />');
						p.$w.find('.gridBody:last').append($row.children());
					}
					/*Cuentas bancarios*/
					if(data.vouchers!=null){
						for(var i=0,j=data.vouchers.length; i<j; i++){
							var $row = p.$w.find('.gridReference:last').clone();
							$row.find('li:eq(0)').html('Voucher - '+data.vouchers[i].num);
							$row.find('li:eq(1)').html(data.vouchers[i].cuenta_banco.nomb);
							$row.find('li:eq(2)').html(ciHelper.formatMon(data.vouchers[i].monto,data.vouchers[i].moneda));
							$row.find('li:eq(3)').html(ciHelper.formatMon(parseFloat(data.vouchers[i].monto)*(data.vouchers[i].moneda=='S'?1:data.tc)));
							$row.wrapInner('<a class="item" />');
							p.$w.find('.gridBody:last').append($row.children());
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowGen: function(p){
		if(p==null) p = {};
		$.extend(p,{
			calcDeb: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(9) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(9) [name=monto]').eq(i).val());
				}
				p.$w.find('.payment:eq(9) .result').remove();
				var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(9) .gridBody').append($row.children());
			},
			calcHab: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(11) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(11) .item').eq(i).data('total'));
				}
				p.$w.find('.payment:eq(11) .result').remove();
				var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(11) .gridBody').append($row.children());
			}
		});
		new K.Modal({
			id: 'windowCompGen',
			title: 'Recibo de Ingresos',
			contentURL: 'cj/comp/gen',
			icon: 'ui-icon-plusthick',
			width: 650,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						modulo: 'CM',
						cod: p.cod,
						iniciales: p.$w.find('[name=iniciales]').val(),
						fec: p.$w.find('[name=fec]').val(),
						fecfin: p.$w.find('[name=fecfin]').val(),
						observ: p.$w.find('[name=observ]').val(),
						detalle: [],
						glosa: p.$w.find('[name=observ]').val(),
						cont_patrimonial: [],
						total: 0,
						efectivos: [],
						fuente: {
							_id: p.$w.find('[name=fuente] option:selected').data('data')._id.$id,
							cod: p.$w.find('[name=fuente] option:selected').data('data').cod,
							rubro: p.$w.find('[name=fuente] option:selected').data('data').rubro
						}
					},tot_deb=0,tot_hab=0,
					tmp = p.$w.find('[name=orga]').data('data');
					if(data.iniciales==''){
						p.$w.find('[name=iniciales]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar unas iniciales!',type: 'error'});
					}
					if(data.fec==''){
						p.$w.find('[name=fec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
					}
					if(data.fecfin==''){
						p.$w.find('[name=fecfin]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
					}
					if(tmp==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}
					data.organizacion = {
						_id: '51a50f0f4d4a13c409000013',
						nomb: 'Cementerio',
						componente: {
							_id: '51e99d7a4d4a13c404000016',
/*							cod: tmp.componente.cod,
							nomb: tmp.componente.nomb*/
						},
						actividad: {
							_id: '51e996044d4a13440a00000e',
/*							cod: tmp.actividad.cod,
							nomb: tmp.actividad.nomb*/
						},
						subprograma: {
							_id: p.prog.subprograma._id.$id,
							cod: p.prog.subprograma.cod
						},
						programa: {
							_id: p.prog.programa._id.$id,
							cod: p.prog.programa.cod
						},
						funcion: {
							_id: p.prog.pliego._id.$id,
							cod: p.prog.pliego.cod
						}
					};
					for(var i=0,j=p.$w.find('fieldset:eq(1) .gridBody .item').length; i<j; i++){
						var det = p.$w.find('fieldset:eq(1) .gridBody .item').eq(i).data('data');
						$.extend(det,{
							monto: p.$w.find('fieldset:eq(1) .gridBody .item').eq(i).data('total')
						});
						data.total += parseFloat(det.monto);
						data.detalle.push(det);
					}
					if(data.detalle.length==0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay ning&uacute;n comprobante para los filtros seleccionados!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('fieldset:eq(2) .gridBody .item').length; i<j; i++){
						if(data.comprobantes_anulados==null) data.comprobantes_anulados = [];
						data.comprobantes_anulados.push(p.$w.find('fieldset:eq(2) .gridBody .item').eq(i).data('data'));
					}
					for(var i=0,j=p.$w.find('.payment:eq(9) .gridBody .item').length; i<j; i++){
						var tmp = p.$w.find('.payment:eq(9) .gridBody .item').eq(i).data('data');
						console.info(i);
						console.log(tmp);
						if(tmp!=null){
							tot_deb += parseFloat(p.$w.find('.payment:eq(9) .gridBody [name=monto]').eq(i).val());
							data.cont_patrimonial.push({
								cuenta: {
									_id: tmp._id.$id,
									cod: tmp.cod,
									descr: tmp.descr
								},
								tipo: 'D',
								moneda: 'S',
								monto: parseFloat(p.$w.find('.payment:eq(9) .gridBody [name=monto]').eq(i).val())
							});
						}
					}
					if(data.cont_patrimonial.length==0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un registro de contabilidad patrimonial!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('.payment:eq(11) .gridBody .item').length; i<j; i++){
						var tmp = p.$w.find('.payment:eq(11) .gridBody .item').eq(i).data('data');
						if(tmp!=null){
							//tot_hab += parseFloat(p.$w.find('.payment:eq(11) .gridBody .item').eq(i).data('total'));
							tot_hab += parseFloat(K.round(parseFloat(p.$w.find('.payment:eq(11) .gridBody .item').eq(i).data('total')),2));
							console.info(parseFloat(K.round(parseFloat(p.$w.find('.payment:eq(11) .gridBody .item').eq(i).data('total')),2)));
							console.log('tot=>'+tot_hab);
							data.cont_patrimonial.push({
								cuenta: {
									_id: tmp._id.$id,
									cod: tmp.cod,
									descr: tmp.descr
								},
								tipo: 'H',
								moneda: 'S',
								monto: parseFloat(p.$w.find('.payment:eq(11) .gridBody .item').eq(i).data('total'))
							});
						}
					}
					tot_hab = parseFloat(K.round(tot_hab,2));
					console.log('Debe: '+tot_deb);
					console.log('Haber: '+tot_hab);
					if(tot_deb!=tot_hab){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El debe no coincide con el haber!',type: 'error'});
					}
					data.efectivos.push({
						moneda: 'S',
						monto: parseFloat(p.$w.find('fieldset:eq(4) .gridBody .item:eq(0)').data('total'))
					});
					var tmp = {
						moneda: 'D',
						monto: parseFloat(p.$w.find('fieldset:eq(4) .gridBody .item:eq(1)').data('total'))
					};
					if(tmp.monto!=0) tmp.tc = parseFloat(p.$w.find('fieldset:eq(4) .gridBody .item:eq(1)').data('total_sol'))/parseFloat(p.$w.find('fieldset:eq(4) .gridBody .item:eq(1)').data('total'));
					data.efectivos.push(tmp);
					for(var i=0,j=p.$w.find('fieldset:eq(4) .gridBody .vouc').length; i<j; i++){
						if(data.vouchers==null) data.vouchers = [];
						data.vouchers.push(p.$w.find('fieldset:eq(4) .gridBody .vouc').eq(i).data('data'));
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/comp/save_rein',data,function(rein){
						K.clearNoti();
						K.windowPrint({
							id:'windowCjControlDeuda',
							title: "Reporte / Informe",
							url: 'cj/repo/reci_ing?_id='+rein._id.$id
						});
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnEliDeb]').die('click');
				p.$w.find('[name=btnAgrDeb]').die('click');
				p.$w.find('[name=btnEliHab]').die('click');
				p.$w.find('[name=btnAgrHab]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowCompGen');
				$(window).resize(function(){
					var $this = $('#windowCompGen');
					$this.dialog( "option", "height", $(window).height() )
						.dialog( "option", "width", $(window).width() )
						.dialog( "option", "position", [ 0 , 0 ] )
						.dialog( "option", "draggable", false )
						.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
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
				p.$w.find('[name=fecfin]').datepicker().change(function(){
					p.$w.find('[name=fec]').change();
				});
				p.$w.find('[name=fec]').datepicker().change(function(){
					if($(this).val()>p.$w.find('[name=fecfin]').val()){
						p.$w.find('[name=fecfin]').val($(this).val());
					}
					var orga = p.$w.find('[name=orga]').data('data');
					if($(this).datepicker('getDate')==null){
						$(this).focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
					}
					if(orga==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}
					K.block({$element: p.$w});
					$.post('cj/rein/get_rec',{
						fec: $(this).val(),
						fecfin: p.$w.find('[name=fecfin]').val(),
						orga: '51a50f0f4d4a13c409000013',//orga._id.$id,
						actividad: '51e996044d4a13440a00000e',//orga.actividad._id.$id,
						componente: '51e99d7a4d4a13c404000016'//orga.componente._id.$id
					},function(data){
						
						
						
						
						
						
						
						
						var tmp_ctas_pat = [];
						
						
						
						
						
						
						
						
						
						p.$w.find('fieldset:eq(1) .gridBody').empty();
						p.$w.find('fieldset:eq(3) .gridBody').empty();
						p.$w.find('.payment:eq(11) .gridBody').empty();
						var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
						$row.find('li:eq(3)').html('<button name="btnEliHab">Eliminar</button>&nbsp;<button name="btnAgrHab">Agregar</button>');
						$row.find('[name=btnEliHab]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgrHab]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						p.$w.find('.payment:eq(11) .gridBody').append($row.children());
						p.$w.find('fieldset:eq(2) .gridBody,fieldset:eq(3) .gridBody').empty();
						if(data.comp==null){
							K.unblock({$element: p.$w});
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
						}
						p.comp = data.comp;
						p.prog = data.prog;
						var $row = p.$w.find('fieldset:eq(3) .gridReference').clone();
						$row.find('li:eq(0)').html(data.prog.pliego.cod);
						$row.find('li:eq(1)').html(data.prog.programa.cod);
						$row.find('li:eq(2)').html(data.prog.subprograma.cod);
						$row.find('li:eq(3)').html(data.prog.proyecto.cod);
						$row.find('li:eq(4)').html(data.prog.obra.cod);
						//$row.find('li:eq(5)').html(orga.actividad.cod);
						//$row.find('li:eq(6)').html(orga.componente.cod);
						$row.find('li:eq(7)').append('<select name="fuente">');
						for(var k=0,l=p.fuen.length; k<l; k++){
							$row.find('select').append('<option value="'+p.fuen[k]._id.$id+'">'+p.fuen[k].cod+'</option>');
							$row.find('select option:last').data('data',p.fuen[k]);
						}
						$row.wrapInner('<a class="item">');
						p.$w.find('fieldset:eq(3) .gridBody').append($row.children());
						/* Efectivo en pagos */
						p.$w.find('fieldset:eq(4) .gridBody').empty();
						var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
						$row.find('li:eq(0)').html('Efectivo Soles');
						$row.find('li:eq(2)').html(ciHelper.formatMon(0));
						$row.find('li:eq(3)').html(ciHelper.formatMon(0));
						$row.wrapInner('<a class="item">');
						$row.find('.item').data('total',0);
						p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
						var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
						$row.find('li:eq(0)').html('Efectivo D&oacute;lares');
						$row.find('li:eq(2)').html(ciHelper.formatMon(0,'D'));
						$row.find('li:eq(3)').html(ciHelper.formatMon(0));
						$row.wrapInner('<a class="item">');
						$row.find('.item').data('total',0);
						p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						/*data.comp.sort(function(a,b){
							console.log(a);
							//items.conceptos.cuenta.cod
							if (a.cuenta.cod < b.cuenta.cod) //sort string ascending
								return -1;
							if (a.cuenta.cod > b.cuenta.cod)
								return 1;
							return 0;
						});*/
						
						
						
						
						
						
						
						/* Bucle de comprobantes */
						var tot_sol = 0,
						tot_dol = 0,
						tot_dol_sol = 0,
						total = 0;
						var _ids = [];
						for(var i=0,j=data.comp.length; i<j; i++){
							var result = data.comp[i];
							if(result.estado=='X'){
								var $row = p.$w.find('fieldset:eq(2) .gridReference').clone();
								$row.find('li:eq(0)').html(result.tipo+' '+result.serie+' '+result.num);
								$row.find('li:eq(1)').html(ciHelper.enti.formatName(result.cliente));
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',{
									_id: result._id.$id,
									tipo: result.tipo,
									serie: result.serie,
									num: result.num
								});
								p.$w.find('fieldset:eq(2) .gridBody').append($row.children());
							}else{
								if(result.items!=null){
									
									if(_ids.indexOf(result._id.$id)==-1){
										_ids.push(result._id.$id);
										for(var k=0,l=result.items.length; k<l; k++){
												var item = result.items[k];
												for(var m=0,n=item.conceptos.length; m<n; m++){
													var conc = item.conceptos[m],
													$row = p.$w.find('fieldset:eq(1) [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc.concepto._id.$id+']'),
													tot_conc = (result.moneda=='S')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tc);
													console.info(result.tipo+' '+result.serie+' - '+result.num+'===>'+tot_conc);
										
										
										
										
										
										
													var tmp_ctas_pat_i = -1;
													for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
														if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
															tmp_ctas_pat_i = tmp_i;
															tmp_i = tmp_j;
														}
													}
													if(tmp_ctas_pat_i==-1){
														tmp_ctas_pat.push({
															cod: conc.cuenta.cod.substr(0,9),
															cuenta: conc.cuenta,
															total: parseFloat(conc.monto)
														});
													}else{
														tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(conc.monto);
													}
													//tmp_ctas_pat
										
										
										
										
										
										
										
													if($row.length>0){
														tot_conc_sec = tot_conc + parseFloat($row.data('total'));
														$row.find('li:eq(4)').html(ciHelper.formatMon(tot_conc_sec));
														$row.data('total',tot_conc_sec);
														console.log('a');
													}else{
														var $row = p.$w.find('fieldset:eq(1) .gridReference').clone();
														$row.find('li:eq(0)').html(conc.cuenta.cod);
														$row.find('li:eq(1)').html(conc.cuenta.descr);
														$row.find('li:eq(2)').html(result.tipo+' '+result.serie+' - '+result.num);
														$row.find('li:eq(3)').html(item.cuenta_cobrar.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):''));
														$row.find('li:eq(4)').html(ciHelper.formatMon(tot_conc));
														$row.wrapInner('<a class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc.concepto._id.$id+'">');
														$row.find('.item').data('total',tot_conc).data('data',{
															cuenta: {
																_id: conc.cuenta._id.$id,
																cod: conc.cuenta.cod,
																descr: conc.cuenta.descr
															},
															comprobante: {
																_id: result._id.$id,
																tipo: result.tipo,
																serie: result.serie,
																num: result.num
															},
															cuenta_cobrar: item.cuenta_cobrar._id.$id,
															concepto: item.cuenta_cobrar.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tc):'')
														});
														p.$w.find('fieldset:eq(1) .gridBody').append($row.children());
														console.log('b');
													}
													console.log(result.tipo+' '+result.serie+' - '+result.num+'===>'+tot_conc);
													total += tot_conc;
												}
											}
										}
									if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
									if(parseFloat(result.efectivos[1].monto)!=0){
										tot_dol += parseFloat(result.efectivos[1].monto);
										tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tc);
									}
									if(result.vouchers!=null){
										for(var k=0,l=result.vouchers.length; k<l; k++){
											var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
											$row.find('li:eq(0)').html('Voucher - '+result.vouchers[k].num);
											$row.find('li:eq(1)').html(result.vouchers[k].cuenta_banco.nomb);
											$row.find('li:eq(2)').html(ciHelper.formatMon(result.vouchers[k].monto,result.vouchers[k].moneda));
											$row.find('li:eq(3)').html(ciHelper.formatMon((result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tc)));
											$row.find('li:eq(4)').html(ciHelper.enti.formatName(result.cliente));
											$row.wrapInner('<a class="item vouc">');
											$row.find('.item').data('data',{
												num: result.vouchers[k].num,
												cuenta_banco: {
													_id: result.vouchers[k].cuenta_banco._id.$id,
													nomb: result.vouchers[k].cuenta_banco.nomb,
													cod_banco: result.vouchers[k].cuenta_banco.cod_banco,
													cod: result.vouchers[k].cuenta_banco.cod,
													moneda: result.vouchers[k].cuenta_banco.moneda
												},
												monto: parseFloat(result.vouchers[k].monto),
												cliente: ciHelper.enti.dbRel(result.cliente),
												tc: (result.tc!=null)?result.tc:0
											});
											$row.find('.item').data('total',parseFloat(result.vouchers[k].monto));
											$row.find('.item').data('moneda',parseFloat(result.vouchers[k].moneda));
											$row.find('.item').data('total_sol',(result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tc));
											p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
										}
									}
								}
							}
						}
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						/*
						 * AQUI LOS RECIBOS DEFINITIVOS
						 */
						//if(data.rede!=null&&p.$w.find('[name=orga]').data('data')._id.$id=='51a50f0f4d4a13c409000013'){
						if(data.rede!=null){
							for(var i=0,j=data.rede.length; i<j; i++){
								var result = data.rede[i];
								
								
								
								
								var $row = p.$w.find('fieldset:eq(1) [name='+result._id.$id+'-'+result.cuenta._id.$id+']'),
								tot_conc = (result.moneda=='S')?parseFloat(result.total):parseFloat(result.total)*parseFloat(result.tasa);
								
								
								
								
								
								
								
								var tmp_ctas_pat_i = -1;
								for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
									if(tmp_ctas_pat[tmp_i].cod==result.cuenta.cod.substr(0,9)){
										tmp_ctas_pat_i = tmp_i;
										tmp_i = tmp_j;
									}
								}
								if(tmp_ctas_pat_i==-1){
									tmp_ctas_pat.push({
										cod: result.cuenta.cod.substr(0,9),
										cuenta: result.cuenta,
										total: parseFloat(K.round(parseFloat(result.total),2))
									});
								}else{
									tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(parseFloat(result.total),2));
								}
								//tmp_ctas_pat
										
										
										
										
										
										
										
								if($row.length>0){
									tot_conc_sec = tot_conc + parseFloat($row.data('total'));
									$row.find('li:eq(4)').html(ciHelper.formatMon(tot_conc_sec));
									$row.data('total',tot_conc_sec);
								}else{
									var $row = p.$w.find('fieldset:eq(1) .gridReference').clone();
									$row.find('li:eq(0)').html(result.cuenta.cod);
									$row.find('li:eq(1)').html(result.cuenta.descr);
									$row.find('li:eq(2)').html('Tec. '+result.num);
									$row.find('li:eq(3)').html(result.concepto+' - '+ciHelper.enti.formatName(result.entidad)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tasa):''));
									$row.find('li:eq(4)').html(ciHelper.formatMon(tot_conc));
									$row.wrapInner('<a class="item" name="'+result._id.$id+'-'+result.cuenta._id.$id+'">');
									$row.find('.item').data('total',tot_conc).data('data',{
										cuenta: {
											_id: result.cuenta._id.$id,
											cod: result.cuenta.cod,
											descr: result.cuenta.descr
										},
										recibo_definitivo: {
											_id: result._id.$id,
											num: result.num
										},
										concepto: result.concepto+' - '+ciHelper.enti.formatName(result.entidad)+((result.moneda=='D')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tasa):'')
									});
									p.$w.find('fieldset:eq(1) .gridBody').append($row.children());
								}
								total += tot_conc;
										
										
										
										
										
										
										
										
										
										
								if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
								if(parseFloat(result.efectivos[1].monto)!=0){
									tot_dol += parseFloat(result.efectivos[1].monto);
									tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tc);
								}
								if(result.vouchers!=null){
									for(var k=0,l=result.vouchers.length; k<l; k++){
										var $row = p.$w.find('fieldset:eq(4) .gridReference').clone();
										$row.find('li:eq(0)').html('Voucher - '+result.vouchers[k].num);
										$row.find('li:eq(1)').html(result.vouchers[k].cuenta_banco.nomb);
										$row.find('li:eq(2)').html(ciHelper.formatMon(result.vouchers[k].monto,result.vouchers[k].moneda));
										$row.find('li:eq(3)').html(ciHelper.formatMon((result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tasa)));
										$row.find('li:eq(4)').html(ciHelper.enti.formatName(result.entidad));
										$row.wrapInner('<a class="item vouc">');
										$row.find('.item').data('data',{
											num: result.vouchers[k].num,
											cuenta_banco: {
												_id: result.vouchers[k].cuenta_banco._id.$id,
												nomb: result.vouchers[k].cuenta_banco.nomb,
												cod_banco: result.vouchers[k].cuenta_banco.cod_banco,
												cod: result.vouchers[k].cuenta_banco.cod,
												moneda: result.vouchers[k].cuenta_banco.moneda
											},
											monto: parseFloat(result.vouchers[k].monto),
											cliente: ciHelper.enti.dbRel(result.entidad),
											tc: (result.tasa!=null)?result.tasa:0
										});
										$row.find('.item').data('total',parseFloat(result.vouchers[k].monto));
										$row.find('.item').data('moneda',parseFloat(result.vouchers[k].moneda));
										$row.find('.item').data('total_sol',(result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tasa));
										p.$w.find('fieldset:eq(4) .gridBody').append($row.children());
									}
								}
							}
						}
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						p.$w.find('.payment:eq(9) .item').remove();
						p.$w.find('.payment:eq(11) .item').remove();
						
						/*
						 * ***************************************************************
						 * GENERACION AUTOMATICA DE CONTABILIDAD PATRIMONIAL
						 * ***************************************************************
						 */
						p.$w.find('.payment:eq(11) .gridBody ul:first').remove();
						var tmp_to = 0;
						for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
							p.$w.find('.payment:eq(11) .gridBody [name=btnAgrHab]').remove();
							var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
							$row.find('li:eq(0)').html(tmp_ctas_pat[tmp_i].cod);
							$row.find('li:eq(1)').html(tmp_ctas_pat[tmp_i].descr);
							$row.find('li:eq(2)').html(ciHelper.formatMon(tmp_ctas_pat[tmp_i].total));
							$row.find('li:eq(3)').html('<button name="btnEliHab">Eliminar</button>&nbsp;<button name="btnAgrHab">Agregar</button>');
							$row.find('[name=btnEliHab]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.find('[name=btnAgrHab]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							$row.wrapInner('<a class="item">');
							$row.find('.item').data('data',tmp_ctas_pat[tmp_i].cuenta).data('total',tmp_ctas_pat[tmp_i].total).attr('name',tmp_ctas_pat[tmp_i].cuenta._id.$id);
							p.$w.find('.payment:eq(11) .gridBody').append($row.children());
							tmp_to += parseFloat(tmp_ctas_pat[tmp_i].total);
						}
						tmp_to = parseFloat(K.round(tmp_to,2));
				
						
						
						
						
						
						
						
						
						
						
						
						
						
						p.$w.find('.payment:eq(9) .gridBody [name=btnAgrDeb]').remove();
						p.$w.find('.payment:eq(9) .gridBody ul:first').remove();
						var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
						$row.find('li:eq(0)').html('1101.0101');
						$row.find('li:eq(1)').html('Caja M/N');
						$row.find('li:eq(2)').html('<input type="text" name="monto" size="8"/>');
						$row.find('li:eq(3)').html('<button name="btnEliDeb">Eliminar</button>&nbsp;<button name="btnAgrDeb">Agregar</button>');
						$row.find('[name=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){
							$(this).change();
						}}).val(tmp_to).change(function(){
							p.calcDeb();
						}).closest('li').find('.ui-button').css('height','14px');
						$row.find('[name=btnEliDeb]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgrDeb]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						$row.find('.item').data('data',{
							_id: {
								$id: '51a6473a4d4a13540a000009',
							},
							cod: '1101.0101',
							descr: 'Caja M/N'
						}).attr('name','51a6473a4d4a13540a000009');
						p.$w.find('.payment:eq(9) .gridBody').append($row.children());
						
						/*
						 * ***************************************************************
						 * TOTALES
						 * ***************************************************************
						 */
						var $row = p.$w.find('fieldset:eq(1) .gridReference').clone();
						$row.find('li:eq(3)').html('Parcial').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
						$row.find('li:eq(4)').html(ciHelper.formatMon(total));
						$row.wrapInner('<a class="result">');
						$row.find('.result').data('total',total);
						p.$w.find('fieldset:eq(1) .gridBody').append($row.children());
						p.$w.find('fieldset:eq(4) .item').eq(0).data('total',tot_sol)
						.find('li:eq(2)').html(ciHelper.formatMon(tot_sol));
						p.$w.find('fieldset:eq(4) .item').eq(0)
						.find('li:eq(3)').html(ciHelper.formatMon(tot_sol));
						p.$w.find('fieldset:eq(4) .item').eq(1).data('total',tot_dol).data('total_sol',tot_dol_sol)
						.find('li:eq(2)').html(ciHelper.formatMon(tot_dol,'D'));
						p.$w.find('fieldset:eq(4) .item').eq(1)
						.find('li:eq(3)').html(ciHelper.formatMon(tot_dol_sol));
						p.calcHab();
						p.calcDeb();
						K.unblock({$element: p.$w});
					},'json');
				});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
						p.$w.find('[name=fec]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=respo]').html(ciHelper.enti.formatName(K.session.enti));
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
				var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
				$row.find('li:eq(2)').html('<input type="text" name="monto" size="8"/>');
				$row.find('li:eq(3)').html('<button name="btnEliDeb">Eliminar</button>&nbsp;<button name="btnAgrDeb">Agregar</button>');
				$row.find('[name=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){
					$(this).change();
				}}).val(0).change(function(){
					p.calcDeb();
				}).closest('li').find('.ui-button').css('height','14px');
				$row.find('[name=btnEliDeb]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.find('[name=btnAgrDeb]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.wrapInner('<a class="item">');
				p.$w.find('.payment:eq(9) .gridBody').append($row.children());
				p.$w.find('[name=btnEliDeb]').live('click',function(){
					$(this).closest('.item').remove();
					p.$w.find('.payment:eq(9) .gridBody [name=btnAgrDeb]').remove();
					p.$w.find('.payment:eq(9) .gridBody .item:last li:eq(3)').append('<button name="btnAgrDeb">Agregar</button>');
					p.$w.find('.payment:eq(9) .gridBody .item:last [name=btnAgrDeb]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					if(p.$w.find('.payment:eq(9) .gridBody .item').length==0){
						var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
						$row.find('li:eq(2)').html('<input type="text" name="monto" size="8"/>');
						$row.find('li:eq(3)').html('<button name="btnEliDeb">Eliminar</button>&nbsp;<button name="btnAgrDeb">Agregar</button>');
						$row.find('[name=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){
							$(this).change();
						}}).val(0).change(function(){
							p.calcDeb();
						}).closest('li').find('.ui-button').css('height','14px');
						$row.find('[name=btnEliDeb]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgrDeb]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						p.$w.find('.payment:eq(9) .gridBody').append($row.children());
					}
					p.calcDeb();
				});
				p.$w.find('[name=btnAgrDeb]').live('click',function(){
					ctPcon.windowSelect({callback: function(data){
						if(p.$w.find('.payment:eq(9) .gridBody [name='+data._id.$id+']').length<=0){
							if(p.$w.find('.payment:eq(9) .gridBody .item:last').data('data')==null){
								var $row = p.$w.find('.payment:eq(9) .gridBody .item:eq(0)');
								$row.find('li:eq(0)').html(data.cod);
								$row.find('li:eq(1)').html(data.descr);
								$row.find('[name=monto]').val('0');
								$row.data('data',data);
								$row.attr('name',data._id.$id);
							}else{
								p.$w.find('.payment:eq(9) .gridBody [name=btnAgrDeb]').remove();
								var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
								$row.find('li:eq(0)').html(data.cod);
								$row.find('li:eq(1)').html(data.descr);
								$row.find('li:eq(2)').html('<input type="text" name="monto" size="8"/>');
								$row.find('li:eq(3)').html('<button name="btnEliDeb">Eliminar</button>&nbsp;<button name="btnAgrDeb">Agregar</button>');
								$row.find('[name=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){
									$(this).change();
								}}).val(0).change(function(){
									p.calcDeb();
								}).closest('li').find('.ui-button').css('height','14px');
								$row.find('[name=btnEliDeb]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.find('[name=btnAgrDeb]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',data).attr('name',data._id.$id);
								p.$w.find('.payment:eq(9) .gridBody').append($row.children());
							}
							p.calcDeb();
						}else{
							return K.notification({title: 'Informaci&oacute;n repetida',text: 'El registro ya fue seleccionado!',type: 'info'});
						}
					}});
				});
				var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
				$row.find('li:eq(3)').html('<button name="btnEliHab">Eliminar</button>&nbsp;<button name="btnAgrHab">Agregar</button>');
				$row.find('[name=btnEliHab]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.find('[name=btnAgrHab]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.wrapInner('<a class="item">');
				p.$w.find('.payment:eq(11) .gridBody').append($row.children());
				p.$w.find('[name=btnEliHab]').live('click',function(){
					$(this).closest('.item').remove();
					p.$w.find('.payment:eq(11) .gridBody [name=btnAgrHab]').remove();
					p.$w.find('.payment:eq(11) .gridBody .item:last li:eq(3)').append('<button name="btnAgrHab">Agregar</button>');
					p.$w.find('.payment:eq(11) .gridBody .item:last [name=btnAgrHab]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					if(p.$w.find('.payment:eq(11) .gridBody .item').length==0){
						var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
						$row.find('li:eq(3)').html('<button name="btnEliHab">Eliminar</button>&nbsp;<button name="btnAgrHab">Agregar</button>');
						$row.find('[name=btnEliHab]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgrHab]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						p.$w.find('.payment:eq(11) .gridBody').append($row.children());
					}
					p.calcHab();
				});
				p.$w.find('[name=btnAgrHab]').live('click',function(){
					if(p.$w.find('[name=fec]').datepicker('getDate')==null){
						p.$w.find('[name=fec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
					}
					if(p.$w.find('[name=orga]').data('data')==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}
					ctPcon.windowSelect({callback: function(data){
						if(p.$w.find('.payment:eq(11) .gridBody [name='+data._id.$id+']').length<=0){
							var total = 0;
							for(var i=0,j=p.comp.length; i<j; i++){
								if(p.comp[i].estado!='X'){
									for(var k=0,l=p.comp[i].items.length; k<l; k++){
										for(var m=0,n=p.comp[i].items[k].conceptos.length; m<n; m++){
											var conc = p.comp[i].items[k].conceptos[m];
											if(conc.cuenta.cod.length==data.cod.length){
												if(conc.cuenta.cod==data.cod){
													total += parseFloat(conc.monto);
												}
											}else{
												if(conc.cuenta.cod.indexOf(data.cod+'.')==0){
													total += parseFloat(conc.monto);
												}
											}
										}
									}
								}
							}
							if(total==0){
								return K.notification({title: 'Cuenta no relacionada',text: 'La cuenta seleccionada no est&aacute; relacionada a ning&uacute;n comprobante del periodo seleccionado!',type: 'error'});
							}
							if(p.$w.find('.payment:eq(11) .gridBody .item:last').data('data')==null){
								var $row = p.$w.find('.payment:eq(11) .gridBody .item:eq(0)');
								$row.find('li:eq(0)').html(data.cod);
								$row.find('li:eq(1)').html(data.descr);
								$row.find('li:eq(2)').html(ciHelper.formatMon(total));
								$row.data('data',data).data('total',total).attr('name',data._id.$id);
							}else{
								p.$w.find('.payment:eq(11) .gridBody [name=btnAgrHab]').remove();
								var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
								$row.find('li:eq(0)').html(data.cod);
								$row.find('li:eq(1)').html(data.descr);
								$row.find('li:eq(2)').html(ciHelper.formatMon(total));
								$row.find('li:eq(3)').html('<button name="btnEliHab">Eliminar</button>&nbsp;<button name="btnAgrHab">Agregar</button>');
								$row.find('[name=btnEliHab]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.find('[name=btnAgrHab]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',data).data('total',total).attr('name',data._id.$id);
								p.$w.find('.payment:eq(11) .gridBody').append($row.children());
							}
							p.calcHab();
						}else{
							return K.notification({title: 'Informaci&oacute;n repetida',text: 'El registro ya fue seleccionado!',type: 'info'});
						}
					}});
				});
				p.$w.find('.payment').eq(11).bind('scroll',function(){
					p.$w.find('.payment').eq(10).scrollLeft(p.$w.find('.payment').eq(11).scrollLeft());
				});
				$.post('cj/rein/get_cod',function(data){
					p.cod = data.cod;
					p.fuen = data.fuen;
					p.$w.find('[name=num]').html(data.cod);
					p.$w.find('[name=fec]').val(ciHelper.dateFormatNowBDNotHour());
					if(K.session.enti.roles!=null){
						if(K.session.enti.roles.trabajador!=null){
							p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb)
								.data('data',K.session.enti.roles.trabajador.programa);
							p.$w.find('[name=btnOrga]').button('option','text',false);
							p.$w.find('[name=fec]').change();
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowPrint: function(p){
		K.incomplete();
	},
	windowCambiar: function(p){
		$.extend(p,{
			cbCli: function(data){
				p.$w.find('[name=section2] [name=nomb]').html(data.nomb).data('data',data);
				if(data.tipo_enti=='E')
					p.$w.find('[name=section2] [name=apel]').closest('tr').hide();
				else{
					p.$w.find('[name=section2] [name=apel]').closest('tr').show();
					p.$w.find('[name=section2] [name=apel]').html(data.appat+' '+data.apmat);
				}
				p.$w.find('[name=section2] [name=dni]').html(data.docident[0].num);
				if(data.domicilios!=null) p.$w.find('[name=section2] [name=direc]').html(data.domicilios[0].direccion);
				else p.$w.find('[name=section2] [name=direc]').html('--');
			},
			loadConc: function(){
				var $table,espacio,conceptos,variables,servicio,SERV={},__VALUE__=0,cuotas=0;
				SERV = {
					CM_PREC_PERP: 0,
					CM_PREC_TEMP: 0,
					CM_PREC_VIDA: 0,
					CM_ACCE_PREC: 0,
					CM_TIPO_ESPA: 0
				};
				if(p.getEspa!=null){
					espacio = p.getEspa();
					if(espacio!=null){
						if(espacio.precio_perp!=null) SERV.CM_PREC_PERP = espacio.precio_perp;
						if(espacio.precio_temp!=null) SERV.CM_PREC_TEMP = espacio.precio_temp;
						if(espacio.precio_vida!=null) SERV.CM_PREC_VIDA = espacio.precio_vida;
						if(espacio.nicho) SERV.CM_TIPO_ESPA = 1;
						else if(espacio.mausoleo) SERV.CM_TIPO_ESPA = 2;
						else if(espacio.tumba) SERV.CM_TIPO_ESPA = 3;
						else SERV.CM_TIPO_ESPA = 4;
					}
				}
				if(p.getAcce!=null){
					SERV.CM_ACCE_PREC = p.getAcce();
					if(SERV.CM_ACCE_PREC==null){
						return K.notification({title: 'Accesorios no seleccionados',text: 'Debe seleccionar accesorios para poder realizar los c&aacute;lculos!',type: 'error'});
					}
				}
				variables = p.$w.data('vars');
				if(variables==null){
					return K.notification({title: 'Servicio no seleccionado1',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=variables.length; i<j; i++){
					try{
						if(variables[i].valor=='true') eval('var '+variables[i].cod+' = true;');
						else if(variables[i].valor=='false') eval('var '+variables[i].cod+' = false;');
						else eval('var '+variables[i].cod+' = '+variables[i].valor+';');
					}catch(e){
						console.warn('error en carga de variables');
					}
				}
				$table = p.$w.find('fieldset:eq(2)');
				$table.find(".gridBody").empty();
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({title: 'Servicio no seleccionado2',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					var $row = $table.find('.gridReference').clone();
					var monto = eval(conceptos[i].formula);
					$row.find('li:eq(0)').html(conceptos[i].nomb);
					if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
						eval('var '+conceptos[i].cod+' = 0;');
						var formula = conceptos[i].formula;
						formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
						$row.find('li:eq(1)').html('<input type="text" size="7" name="codform'+conceptos[i].cod+'">');
						$row.find('[name^=codform]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
							$(this).change();
						}}).change(function(){
							var val = parseFloat($(this).val()),
							formula = $(this).data('form'),
							cod = $(this).data('cod'),
							$row = $(this).closest('.item');
							eval("var __VALUE"+cod+"__ = "+val+";");
							var monto = eval(formula);
							$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
							eval("var "+cod+" = "+monto+";");
							$row.data('monto',monto);
							for(var ii=0,jj=conceptos.length; ii<jj; ii++){
								var $table = p.$w.find('fieldset:eq(2)'),
								$row = $table.find('.gridBody .item').eq(ii),
								$cell = $row.find('li').eq(2),
								monto = eval($cell.data('formula'));
								if($cell.data('formula')!=null){
									$cell.html(ciHelper.formatMon(monto));
									$row.data('monto',monto);
								}
							}
							p.calcConc();
						}).data('form',formula).data('cod',conceptos[i].cod);
						$row.find('li:eq(1) .ui-button').css('height','14px');
					}else{
						$row.find('li:eq(2)').data('formula',conceptos[i].formula);
					}
					$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
					$row.wrapInner('<a class="item" name="'+conceptos[i]._id.$id+'" />');
					$row.find('.item').data('monto',monto);
					$table.find(".gridBody").append( $row.children() );
				}
				p.calcConc();
			},
			calcConc: function(){
				var $table, servicio, conceptos, total = 0, cuotas=0;
				$table = p.$w.find('fieldset:eq(2)');
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({title: 'Servicio no seleccionado3',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					total += parseFloat($table.find('.item').eq(i).data('monto'));
				}
				if(conceptos.length!=$table.find('.item').length){
					$table.find('.item:last').remove();
				}
				var $row = $table.find('.gridReference').clone();
				$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default');
				$row.find('li:eq(1)').html('Total');
				$row.find('li:eq(2)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="item" />');
				$row.find('.item').data('total',total);
				$table.find(".gridBody").append( $row.children() );
			}
		});
		new K.Window({
			id: 'windowCambiar'+p.id,
			title: 'Cambiar',
			contentURL: 'cj/comp/cambiar',
			store: false,
			icon: 'ui-icon-pencil',
			width: 710,
			height: 400,
			buttons: {
				'Actualizar': function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						cliente: p.$w.find('[name=section2] [name=nomb]').data('data'),
						servicio: p.$w.find('[name^=serv]').data('data'),
						fecven: p.$w.find('[name=fecven]').val(),
						conceptos: [],
			            total: parseFloat(p.$w.find('[name=section3] .item:last').data('total')),
			            saldo: parseFloat(p.$w.find('[name=section3] .item:last').data('total')),
			            moneda: 'S'
					};
					if(data.cliente==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un nuevo Cliente!',
							type: 'error'
						});
					}else data.cliente = ciHelper.enti.dbRel(data.cliente);
					if(data.servicio==null){
						p.$w.find('[name^=btnServ]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un servicio!',
							type: 'error'
						});
					}else data.servicio = {
						_id: data.servicio._id.$id,
						nomb: data.servicio.nomb,
						organizacion: {
			                _id: data.servicio.organizacion._id.$id,
			                nomb: data.servicio.organizacion.nomb
						}
					};
					if(data.fecven==''){
						p.$w.find('[name=fecven]').datepicker('show');
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar una fecha de vencimiento!',
							type: 'error'
						});
					}
					var $table = p.$w.find('[name=section3]'),
			        conceptos = $table.find('[name^=serv]').data('concs');
			        for(var i=0,j=conceptos.length; i<j; i++){
			            var tmp = {
			            	concepto: {
				                _id: conceptos[i]._id.$id,
				                cod: conceptos[i].cod,
				                nomb: conceptos[i].nomb,
				                formula: conceptos[i].formula
				            }
			            };
			            if(conceptos[i].clasificador!=null){
			                tmp.concepto.clasificador = {
				                _id: conceptos[i].clasificador._id.$id,
				                nomb: conceptos[i].clasificador.nomb,
				                cod: conceptos[i].clasificador.cod
			              };
			            }
			            if(conceptos[i].cuenta!=null){
			              tmp.concepto.cuenta = {
			                _id: conceptos[i].cuenta._id.$id,
			                descr: conceptos[i].cuenta.descr,
			                cod: conceptos[i].cuenta.cod
			              };
			            }
			            tmp.monto = parseFloat($table.find('.item').eq(i).data('monto'));
			            tmp.saldo = tmp.monto;
			            data.conceptos.push(tmp);
			        }
			        /*
			         * Se arma la informacion del nuevo comprobante
			         */
			        data.comp = {
			        	modulo: p.modulo,
						cliente: data.cliente,
						caja: p.$w.find('[name=section4] [name=caja] option:selected').data('data'),
						tipo: p.$w.find('[name=section4] [name=comp] option:selected').val(),
						serie: p.$w.find('[name=section4] [name=serie] option:selected').html(),
						num: p.$w.find('[name=section4] [name=num]').val(),
						fecreg: p.$w.find('[name=section4] [name=fecreg]').val(),
						moneda: 'S',
						total: data.total
					};
					if(data.comp.num==''){
						p.$w.find('[name=num]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar un n&uacute;mero de comprobante!',
							type: 'error'
						});
					}
					data.comp.tc = p.tasa;
					data.comp.caja = {
						_id: data.comp.caja._id.$id,
						nomb: data.comp.caja.nomb,
						local: {
							_id: data.comp.caja.local._id.$id,
							descr: data.comp.caja.local.descr,
							direccion: data.comp.caja.local.direccion
						}
					};
					if(parseFloat(data.comp.total)==0){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El comprobante no puede tener como total 0!',
							type: 'error'
						});
					}
					var tot = 0;
					tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
					tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())*p.tasa;
					data.comp.efectivos = [
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
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un n&uacute;mero de voucher!',
									type: 'error'
								});
							}
							if(data.comp.vouchers==null) data.comp.vouchers = [];
							data.comp.vouchers.push(tmp);
							tot += (tmp.moneda=='S')?tmp.monto:tmp.monto*p.tasa;
						}
					}
					console.log(data.comp.total);
					console.log(tot);
					if(parseFloat(data.comp.total)!=tot){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El total del comprobante no coincide con el total de la forma de pagar!',
							type: 'error'
						});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/comp/save_cambiar',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({
							title: ciHelper.titleMessages.regiAct,
							text: 'El comprobante fue actualizado con &eacute;xito!'
						});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCambiar'+p.id);
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
				p.$w.find('[name=btnSelEnt]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbCli});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgrEnt]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbCli});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('[name^=btnServ]').click(function(){
					var $row = $(this).closest('tr');
					mgServ.windowSelect({callback: function(data){
						$row.find('[name^=serv]').html('').removeData('data');
						p.$w.find('[id^=tabsConcPayment] .gridBody').empty();
						$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
							if(concs.serv==null){
								return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
							}
							p.$w.data('vars',concs.vars);
							$row.find('[name^=serv]').html(data.nomb).data('data',data).data('concs',concs.serv);
							p.loadConc();
						},'json');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=section3] [name=fecven]').val(ciHelper.dateFormatNowBDNotHour()).datepicker();
				p.$w.find('[name=section4] [name=num]').replaceWith('<input type="text" name="num" size="7"/>');
				p.$w.find('[name=section4] [name=fecreg]').val(K.date()).datepicker();
				p.$w.find('.payment').eq(3).bind('scroll',function(){
					p.$w.find('.payment').eq(2).scrollLeft(p.$w.find('.payment').eq(3).scrollLeft());
				});
				$.post('cj/comp/get_info_comp_cambiar',{id: p.id},function(data){
					K.titleUpdate(p.$w.attr('id'),'Cambiar Nombre de Cliente en '+cjComp.tipo[data.comp.tipo]+' '+p.nomb);
					data.comp.tc = parseFloat(data.comp.tc);
					p.modulo = data.comp.modulo;
					p.tasa = parseFloat(data.tasa.valor);
					p.$w.find('[name=section1] [name=nomb]').html(data.comp.cliente.nomb);
					if(data.comp.cliente.tipo_enti=='E') p.$w.find('[name=section1] [name=apell]').closest('tr').hide();
					else p.$w.find('[name=section1] [name=apell]').html(data.comp.cliente.appat+' '+data.comp.cliente.apmat);
					p.$w.find('[name=section1] [name=dni]').html(data.comp.cliente.docident[0].num);
					if(data.comp.cliente.domicilios!=null) p.$w.find('[name=section1] [name=direc]').html(data.comp.cliente.domicilios[0].direccion);
					p.$w.find('[name=section1] [name=caja]').html(data.comp.caja.nomb);
					p.$w.find('[name=section1] [name=comp]').html(cjTalo.types[data.comp.tipo]);
					p.$w.find('[name=section1] [name=serie]').html(data.comp.serie);
					p.$w.find('[name=section1] [name=num]').html(data.comp.num);
					/*Cajas*/
					var $select = p.$w.find('[name=section4] [name=caja]');
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
							var $select = p.$w.find('[name=section4] [name=comp]').data('data',data).empty();
							for(var i=0,j=data.length; i<j; i++){
								if($select.find('[value='+data[i].tipo+']').length<=0)
									$select.append('<option value="'+data[i].tipo+'">'+cjTalo.types[data[i].tipo]+'</option>');
								if($select.find('option').length==3) i = j;
							}
							$select.unbind('change').change(function(){
								var $sel = p.$w.find('[name=section4] [name=serie]').empty(),
								talos = p.$w.find('[name=section4] [name=comp]').data('data'),
								$this = $(this);
								for(var i=0,j=talos.length; i<j; i++){
									if($this.find('option:selected').val()==talos[i].tipo)
										$sel.append('<option value="'+talos[i]._id.$id+'">'+talos[i].serie+'</option>')
										.find('option:last').data('data',talos[i]);
								}
								$sel.unbind('change').change(function(){
									p.$w.find('[name=section4] [name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					/*Efectivo Soles*/
					var $row = p.$w.find('.gridReference:last').clone();
					$row.find('li:eq(0)').html('Efectivo Soles');
					$row.find('li:eq(2)').html('S/.<input type="text" name="tot" size="6"/>');
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
					$row.find('li:eq(2)').html('$<input type="text" name="tot" size="6"/>');
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
						$row.find('li:eq(0)').html('Voucher <input type="text" name="voucher" size="6"/>');
						$row.find('li:eq(1)').html(data.ctban[i].nomb);
						$row.find('li:eq(2)').html((data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="text" name="tot" size="6"/>');
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
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowConfirmar: function(p){
		new K.Window({
			id: 'windowCambiar'+p.id,
			title: 'Cambiar',
			contentURL: 'cj/comp/confirmar',
			store: false,
			icon: 'ui-icon-pencil',
			width: 710,
			height: 400,
			buttons: {
				'Actualizar': function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						cliente: p.$w.find('[name=section2] [name=nomb]').data('data'),
						fecven: p.$w.find('[name=fecven]').val(),
			            total: parseFloat(p.$w.find('[name=section3] .item:last').data('total')),
			            saldo: parseFloat(p.$w.find('[name=section3] .item:last').data('total')),
			            moneda: 'S'
					};
					if(data.cliente==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un nuevo Cliente!',
							type: 'error'
						});
					}else data.cliente = ciHelper.enti.dbRel(data.cliente);
					if(data.fecven==''){
						p.$w.find('[name=fecven]').datepicker('show');
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar una fecha de vencimiento!',
							type: 'error'
						});
					}
			        /*
			         * Se arma la informacion del nuevo comprobante
			         */
			        data.comp = {
						cliente: data.cliente,
						caja: p.$w.find('[name=section4] [name=caja] option:selected').data('data'),
						tipo: p.$w.find('[name=section4] [name=comp] option:selected').val(),
						serie: p.$w.find('[name=section4] [name=serie] option:selected').html(),
						num: p.$w.find('[name=section4] [name=num]').val(),
						fecreg: p.$w.find('[name=section4] [name=fecreg]').val(),
						moneda: 'S',
						total: data.total
					};
					if(data.comp.num==''){
						p.$w.find('[name=num]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar un n&uacute;mero de comprobante!',
							type: 'error'
						});
					}
					data.comp.tc = p.tasa;
					data.comp.caja = {
						_id: data.comp.caja._id.$id,
						nomb: data.comp.caja.nomb,
						local: {
							_id: data.comp.caja.local._id.$id,
							descr: data.comp.caja.local.descr,
							direccion: data.comp.caja.local.direccion
						}
					};
					if(parseFloat(data.comp.total)==0){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El comprobante no puede tener como total 0!',
							type: 'error'
						});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/comp/save_confirmar_camb',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({
							title: ciHelper.titleMessages.regiAct,
							text: 'El comprobante fue actualizado con &eacute;xito!'
						});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCambiar'+p.id);
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
				p.$w.find('[name=section3] [name=fecven]').val(ciHelper.dateFormatNowBDNotHour()).datepicker();
				p.$w.find('[name=section4] [name=num]').replaceWith('<input type="text" name="num" size="7"/>');
				p.$w.find('[name=section4] [name=fecreg]').val(K.date()).datepicker();
				p.$w.find('.payment').eq(3).bind('scroll',function(){
					p.$w.find('.payment').eq(2).scrollLeft(p.$w.find('.payment').eq(3).scrollLeft());
				});
				$.post('cj/comp/get_info_comp_cambiar',{id: p.id},function(data){
					K.titleUpdate(p.$w.attr('id'),'Cambiar Nombre de Cliente en '+cjComp.tipo[data.comp.tipo]+' '+p.nomb);
					data.comp.tc = parseFloat(data.comp.tc);
					p.tasa = parseFloat(data.tasa.valor);
					p.$w.find('[name=section1] [name=nomb]').html(data.comp.cliente.nomb);
					if(data.comp.cliente.tipo_enti=='E') p.$w.find('[name=section1] [name=apell]').closest('tr').hide();
					else p.$w.find('[name=section1] [name=apell]').html(data.comp.cliente.appat+' '+data.comp.cliente.apmat);
					p.$w.find('[name=section1] [name=dni]').html(data.comp.cliente.docident[0].num);
					if(data.comp.cliente.domicilios!=null) p.$w.find('[name=section1] [name=direc]').html(data.comp.cliente.domicilios[0].direccion);
					if(data.comp.cliente_nuevo!=null){
						p.$w.find('[name=section2] [name=nomb]').html(data.comp.cliente_nuevo.nomb)
							.data('data',data.comp.cliente_nuevo);
						if(data.comp.cliente_nuevo.tipo_enti=='E') p.$w.find('[name=section2] [name=apel]').closest('tr').hide();
						else p.$w.find('[name=section2] [name=apel]').html(data.comp.cliente_nuevo.appat+' '+data.comp.cliente_nuevo.apmat);
						p.$w.find('[name=section2] [name=dni]').html(data.comp.cliente_nuevo.docident[0].num);
						if(data.comp.cliente_nuevo.domicilios!=null) p.$w.find('[name=section2] [name=direc]').html(data.comp.cliente_nuevo.domicilios[0].direccion);
					}
					p.$w.find('[name=section1] [name=caja]').html(data.comp.caja.nomb);
					p.$w.find('[name=section1] [name=comp]').html(cjTalo.types[data.comp.tipo]);
					p.$w.find('[name=section1] [name=serie]').html(data.comp.serie);
					p.$w.find('[name=section1] [name=num]').html(data.comp.num);
					/*Cajas*/
					var $select = p.$w.find('[name=section4] [name=caja]');
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
							var $select = p.$w.find('[name=section4] [name=comp]').data('data',data).empty();
							for(var i=0,j=data.length; i<j; i++){
								if($select.find('[value='+data[i].tipo+']').length<=0)
									$select.append('<option value="'+data[i].tipo+'">'+cjTalo.types[data[i].tipo]+'</option>');
								if($select.find('option').length==3) i = j;
							}
							$select.unbind('change').change(function(){
								var $sel = p.$w.find('[name=section4] [name=serie]').empty(),
								talos = p.$w.find('[name=section4] [name=comp]').data('data'),
								$this = $(this);
								for(var i=0,j=talos.length; i<j; i++){
									if($this.find('option:selected').val()==talos[i].tipo)
										$sel.append('<option value="'+talos[i]._id.$id+'">'+talos[i].serie+'</option>')
										.find('option:last').data('data',talos[i]);
								}
								$sel.unbind('change').change(function(){
									p.$w.find('[name=section4] [name=num]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
								}).change();
							}).change();
						},'json');
					}).change();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	/*
	windowVoucher: function(p){
		new K.Modal({
			id: 'windowVoucher'+p.id,
			title: 'Cambiar Datos de Voucher',
			//contentURL: 'cj/comp/voucher',
			//store: false,
			contentHTML: '<div name=grid></div>',
			icon: 'ui-icon-pencil',
			width: 600,
			height: 300,
			buttons: {
				'Actualizar': function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						vouchers: []
					};
					for(var i=0, j=p.$w.find('.item').length; i<j; i++){
						var tmp = p.$w.find('.item').eq(i).data('data');
						tmp.num = p.$w.find('.item').eq(i).find('[name=voucher]').val();
						tmp.cuenta_banco._id = tmp.cuenta_banco._id.$id;
						data.vouchers.push(tmp);
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/comp/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({
							title: ciHelper.titleMessages.regiAct,
							text: 'El comprobante fue actualizado con &eacute;xito!'
						});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowVoucher'+p.id);
				K.block({$element: p.$w});
				$.post('cj/comp/get',{id: p.id},function(data){
					for(var i=0,j=data.vouchers.length; i<j; i++){
						var voucher = data.vouchers[i],
						$row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html('<input type="text" name="voucher" value="'+voucher.num+'"/>');
						$row.find('li:eq(1)').html(voucher.cuenta_banco.nomb);
						$row.find('li:eq(2)').html(ciHelper.formatMon(voucher.monto,voucher.moneda));
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						$row.find('a').data('data',voucher);
						p.$w.find('.gridBody').append($row.children());
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
	 */
	windowVoucher: function(p){
		new K.Modal({
			id: 'windowVoucher'+p.id,
			title: 'Cambiar Datos de Voucher',
			//contentURL: 'cj/comp/voucher',
			//store: false,
			content: '<div name="gridForm"></div>',
			icon: 'ui-icon-pencil',
			width: 600,
			height: 300,
			buttons: {
				'Actualizar': function(){
					K.clearNoti();
					var data = {
						_id: p.id
					};
					var tmp_total = 0;
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
					tmp_total += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
					tmp_total += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val());
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
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un n&uacute;mero de voucher!',
									type: 'error'
								});
							}
							if(data.vouchers==null) data.vouchers = [];
							data.vouchers.push(tmp);
							tmp_total += parseFloat(tmp.monto);
						}
					}
					if(tmp_total!=p.total){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El total del comprobante <b>('+ciHelper.formatMon(p.total)+')</b> no coincide con la forma de pago!',
							type: 'error'
						});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/comp/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({
							title: ciHelper.titleMessages.regiAct,
							text: 'El comprobante fue actualizado con &eacute;xito!'
						});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowVoucher'+p.id);
				new K.grid({
					$el: p.$w.find('[name=gridForm]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','','Subtotal',''],
					onlyHtml: true
				});
				K.block({$element: p.$w});
				$.post('cj/comp/get',{id: p.id,forma:true},function(data){
					p.total = parseFloat(data.total);
					p.ctban = data.ctban;
					/*Efectivo Soles*/
					var $row = $('<tr class="item" name="mon_sol">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').numeric().keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					}).val(data.efectivos[0].monto);
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Efectivo Dolares*/
					var $row = $('<tr class="item" name="mon_dol">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
					$row.append('<td>S/.0.00</td>');
					$row.find('[name=tot]').val(0).numeric().keyup(function(){
						if($(this).val()=='')
							$(this).val(0);
						$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
						$(this).closest('.item').data('total',parseFloat($(this).val()));
					}).val(data.efectivos[1].monto);
					p.$w.find('[name=gridForm] tbody').append($row);
					/*Cuentas bancarios*/
					for(var i=0,j=p.ctban.length; i<j; i++){
						var $row = $('<tr class="item" name="ctban" data-ctban="'+p.ctban[i]._id.$id+'">');
						$row.append('<td>Voucher <input type="text" name="voucher" size="7"/></td>');
						$row.append('<td>'+p.ctban[i].nomb+'</td>');
						$row.append('<td>'+(data.ctban[i].moneda=='S'?'S/.':'$')+'<input type="text" name="tot" size="7"/></td>');
						$row.append('<td>S/.0.00</td>');
						$row.find('[name=tot]').val(0).numeric().keyup(function(){
							if($(this).val()=='')
								$(this).val(0);
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon(tot));
							$(this).closest('.item').data('total',parseFloat(tot));
						});
						$row.data('moneda',p.ctban[i].moneda).data('data',{
							_id: p.ctban[i]._id.$id,
							cod: p.ctban[i].cod,
							nomb: p.ctban[i].nomb,
							moneda: p.ctban[i].moneda,
							cod_banco: p.ctban[i].cod_banco
						});
						p.$w.find('[name=gridForm] tbody').append($row);
					}
					if(data.vouchers!=null){
						for(var i=0,j=data.vouchers.length; i<j; i++){
							var voucher = data.vouchers[i];
							for(var i=0,j=p.ctban.length; i<j; i++){
								if(voucher.cuenta_banco._id.$id==p.ctban[i]._id.$id){
									var $row = p.$w.find('[data-ctban='+p.ctban[i]._id.$id+']');
									$row.find('[name=voucher]').val(voucher.num);
									$row.find('[name=tot]').val(voucher.monto);
								}
							}
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};