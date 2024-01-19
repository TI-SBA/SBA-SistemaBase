faConf = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'fa',
			action: 'faConf',
			titleBar: {
				title: 'Configuraci&oacute;n de Farmacia'
			}
		});
		
		new K.Panel({
			contentURL: 'fa/conf',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: 'FA',
							proveedores: [],
							laboratorios: []
						};
						var tmp = p.$w.find('[name=cuenta]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnCta]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Cuenta de Medicinas!',
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
						var $tmp = p.$w.find('.col-sm-6:eq(2)');
						if($tmp.find('tbody .item').length==0){
							$tmp.find('button').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar como m&iacute;nimo un proveedor!',
								type: 'error'
							});
						}else{
							for(var i=0,j=$tmp.find('.item').length; i<j; i++){
								data.proveedores.push($tmp.find('.item').eq(i).find('[name=nomb]').val());
							}
						}
						var $tmp = p.$w.find('.col-sm-6:eq(3)');
						if($tmp.find('tbody .item').length==0){
							$tmp.find('button').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar como m&iacute;nimo un laboratorio!',
								type: 'error'
							});
						}else{
							for(var i=0,j=$tmp.find('.item').length; i<j; i++){
								data.laboratorios.push($tmp.find('.item').eq(i).find('[name=nomb]').val());
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('fa/conf/save',data,function(){
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
						$.post('fa/conf/reset',function(){
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
					$el: p.$w.find('[name=gridPro]'),
					search: false,
					pagination: false,
					cols: ['','Nombre de Proveedor'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Proveedor</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td><input type="text" name="nomb" class="form-control" /></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridPro] tbody').append($row);
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridLab]'),
					search: false,
					pagination: false,
					cols: ['','Nombre de Laboratorio'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Agregar Laboratorio</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td><input type="text" name="nomb" class="form-control" /></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridLab] tbody').append($row);
						});
					}
				});
				$.post('fa/conf/get',function(data){
					if(data.IGV!=null){
						p.$w.find('[name=serv_igv]').html(data.IGV.cod).data('data',data.IGV);
						p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
					}
					if(data.proveedores!=null){
						for(var i=0,j=data.proveedores.length; i<j; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td><input type="text" name="nomb" class="form-control" value="'+data.proveedores[i]+'" /></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridPro] tbody').append($row);
						}
					}
					if(data.laboratorios!=null){
						for(var i=0,j=data.laboratorios.length; i<j; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td><input type="text" name="nomb" class="form-control" value="'+data.laboratorios[i]+'" /></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridLab] tbody').append($row);
						}
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return faConf;
	}
);