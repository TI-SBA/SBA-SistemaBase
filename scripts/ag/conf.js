agConf = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ag',
			action: 'agConf',
			titleBar: {
				title: 'Configuraci&oacute;n de Agua Chapi'
			}
		});
		p.almacenes = agProd.almacenes();
		new K.Panel({
			contentURL: 'ag/conf',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: 'AG',
							series: []
						};
						var tmp = p.$w.find('[name=cuenta]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnCta]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Cuenta de Venta de Agua!',
								type: 'error'
							});
						}else
							data['cuenta'] = ctPcon.dbRel(tmp);
						var tmp = p.$w.find('[name=serv_igv]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnIgv]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Cuenta de IGV!',
								type: 'error'
							});
						}else
							data['IGV'] = ctPcon.dbRel(tmp);
						var $tmp = p.$w.find('[name=gridSeri]');
						if($tmp.find('tbody .item').length==0){
							$tmp.find('button').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar como m&iacute;nimo una unidad!',
								type: 'error'
							});
						}else{
							for(var i=0,j=$tmp.find('.item').length; i<j; i++){
								data.series.push({
									tipo: $tmp.find('.item').eq(i).find('[name=tipo] option:selected').val(),
									serie: $tmp.find('.item').eq(i).find('[name=serie]').val(),
									almacen: $tmp.find('.item').eq(i).find('[name=almacen]').val()
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('ag/conf/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Configuracion ha sido actualizada con &eacute;xito!'});
							p.$w.find('#div_buttons button').removeAttr('disabled');
						});
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnReset]').click(function(){
					ciHelper.confirm('&#191;Desea <b>Resetear</b> los stocks de productos&#63;',
					function(){
						K.block();
						K.sendingInfo();
						var almacen=$('#alma option:selected').val();
						$.post('ag/conf/reset','almacen='+almacen, function(){
							K.clearNoti();
							K.notification({title: 'Stocks actualizados',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
							K.unblock();
						});
					},function(){
						$.noop();
					},'Reset de Productos');
				});
				p.$w.find('[name=btnCta]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=cuenta]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnIgv]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=serv_igv]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('label').css('color','green');
				new K.grid({
					$el: p.$w.find('[name=gridSeri]'),
					search: false,
					pagination: false,
					cols: ['','Tipo de Comprobante','Serie','Local'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Item</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td><select name="tipo" class="form-control"></select></td>');
							var $cbo = $row.find('select:last');
							$cbo.append('<option value="R">Recibo de Caja</option>');
							$cbo.append('<option value="B">Boleta de Venta</option>');
							$cbo.append('<option value="F">Factura</option>');
							$row.append('<td><input type="text" name="serie" class="form-control" /></td>');
							$row.append('<td><select name="almacen" class="form-control"></select></td>');
							var $cbo = $row.find('[name=almacen]');
							for (var i = p.almacenes.length - 1; i >= 0; i--) {
								$cbo.append('<option value="'+p.almacenes[i]._id.$id+'">'+p.almacenes[i].nomb+'</option>');
							};
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridSeri] tbody').append($row);
						});
					}
				});
				$.post('ag/conf/get',function(data){
					if(data.IGV!=null){
						p.$w.find('[name=serv_igv]').html(data.IGV.cod).data('data',data.IGV);
						p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
					}
					if(data.series!=null){
						for(var i=0,j=data.series.length; i<j; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td><select name="tipo" class="form-control"></select></td>');
							var $cbo = $row.find('select:last');
							$cbo.append('<option value="R">Recibo de Caja</option>');
							$cbo.append('<option value="B">Boleta de Venta</option>');
							$cbo.append('<option value="F">Factura</option>');
							$cbo.selectVal(data.series[i].tipo);
							$row.append('<td><input type="text" name="serie" class="form-control" value="'+data.series[i].serie+'" /></td>');
							$row.append('<td><select name="almacen" class="form-control"></td>');
							var $cbo = $row.find('[name=almacen]');
							for (var l = p.almacenes.length - 1; l >= 0; l--) {
								$cbo.append('<option value="'+p.almacenes[l]._id.$id+'">'+p.almacenes[l].nomb+'</option>');
							};
							$cbo.selectVal(data.series[i].almacen.$id);
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridSeri] tbody').append($row);
						}
					}
					if(data.almacenes!=null){
						var $class = $('<div class="form-group" name="secButton">');
						var $select_row = $(".form-group #alma");
						for (var ld = data.almacenes.length - 1; ld >= 0; ld--) {
								$select_row.append('<option value="'+data.almacenes[ld]._id.$id+'">'+data.almacenes[ld].nomb+'</option>');
						};
						$select_row.selectVal(data.almacenes[0]._id.$id);
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['ct/pcon','ag/prod'],
	function(ctPcon,agProd){
		return agConf;
	}
);