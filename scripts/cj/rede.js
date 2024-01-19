/*******************************************************************************
Ocupantes */
cjRede = {
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjRede',
			titleBar: {
				title: 'Recibos Definitivos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','Fecha','Num','Concepto','Entidad','Total'],
					data: 'cj/rede/lista',
					params: {},
					itemdescr: 'recibo(s) definitivo(s)',
					toolbarHTML: '<button name="btnAgregar">Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							cjRede.windowNew();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.fec+'</td>');
						$row.append('<td>'+data.num+'</td>');
						$row.append('<td>'+data.concepto+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.entidad)+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.total)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							cjRede.windowDetails({
								id: $(this).data('id'),
								nomb: $(this).find('td:eq(2)').html()
							});
						}).data('data',data).contextMenu('conMenListEd', {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenListEd_edi,#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t){
									cjRede.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNew',
			title: 'Nuevo Recibo Definitivo',
			contentURL: 'cj/rede/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						fec: p.$w.find('[name=fec]').val(),
						moneda: 'S',
						entidad: p.$w.find('[name=entidad]').data('data'),
						cuenta: p.$w.find('[name=cuenta]').data('data'),
						num: p.$w.find('[name=num]').val(),
						concepto: p.$w.find('[name=concepto]').val(),
						total: p.$w.find('[name=total]').val(),
						origen: p.$w.find('[name=origen]').val()
					};
					if(data.num==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero!',type: 'error'});
					}
					if(data.entidad==null){
						p.$w.find('[name=btnEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad!',type: 'error'});
					}else{
						data.entidad = mgEnti.dbRel(data.entidad);
					}
					if(data.concepto==''){
						p.$w.find('[name=concepto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un concepto!',type: 'error'});
					}
					if(data.total==''){
						p.$w.find('[name=total]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un total!',type: 'error'});
					}
					if(data.cuenta==null){
						p.$w.find('[name=btnCta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta!',type: 'error'});
					}else{
						data.cuenta = ctPcon.dbRel(data.cuenta);
					}
					var tot = 0;
					tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
					tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())*p.tasa;
					data.tasa = p.tasa;
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
					if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El total del recibo definitivo no coincide con el total de la forma de pagar!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/rede/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo definitivo fue registrado con &eacute;xito!'});
						cjRede.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				K.block({$element: p.$w});
				p.$w.find('[name=fec]').val(K.date()).datepicker();
				p.$w.find('[name=btnEnti]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						p.$w.find('[name=entidad]').html(mgEnti.formatName(data)).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				$.post('cj/rede/get_info',function(data){
					p.tasa = parseFloat(data.tasa.valor);
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
						$row.find('[name=tot]').val(0).change(function(){
							var moneda = $(this).closest('.item').data('moneda'),
							tot = moneda=='S'?$(this).val():$(this).val()*p.tasa;
							$(this).closest('.item').find('li:eq(3)').html(ciHelper.formatMon(tot));
							$(this).closest('.item').find('[name=ctban]').data('total',parseFloat(tot));
						});
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
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowDetails'+p.id,
			title: 'Recibo Definitivo Nro '+p.nomb,
			contentURL: 'cj/rede/details',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 280,
			buttons: {
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetails'+p.id);
				K.block({$element: p.$w});
				$.post('cj/rede/get',{_id: p.id},function(data){
					p.$w.find('[name=fec]').html(data.fec);
					p.$w.find('[name=num]').html(data.num);
					p.$w.find('[name=entidad]').html(mgEnti.formatName(data.entidad));
					p.$w.find('[name=concepto]').html(data.concepto);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr);
					p.$w.find('[name=total]').html(ciHelper.formatMon(data.total));
					p.$w.find('[name=origen]').html(data.origen);
					p.$w.find('[name=fecreg]').html(ciHelper.date.format.bd_ymd(data.fecreg));
					p.$w.find('[name=trabajador]').html(mgEnti.formatName(data.trabajador));
					K.unblock({$element: p.$w});
				},'json');
			}	
		});
	}
};
define(
	function(){
		return cjRede;
	}
);