lgConf = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'lg',
			action: 'lgConf',
			titleBar: {
				title: 'Configuraci&oacute;n de Log&iacute;stica'
			}
		});
		
		new K.Panel({
			contentURL: 'cj/cuen/conf_logi',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: 'LG',
							//TIEMPO_COBRANZA: p.$w.find('[name=tiempo_cobranza]').val()
						};
						var tmp = p.$w.find('[name=alma_agua]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnAgua]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un almacen para la farmacia!',
								type: 'error'
							});
						}else
							data['AGUA'] = lgAlma.dbRel(tmp);
						var tmp = p.$w.find('[name=alma_farm]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnFarm]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un almacen para la farmacia!',
								type: 'error'
							});
						}else
							data['FARM'] = lgAlma.dbRel(tmp);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/cuen/save_config_logi',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Configuracion ha sido actualizada con &eacute;xito!'});
						});
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: $('#pageWrapperMain')});
				p.$w.find('label').css('color','green');
				p.$w.find('[name=btnAgua]').click(function(){
					var $this = $(this);
					lgAlma.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=alma_agua]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnFarm]').click(function(){
					var $this = $(this);
					lgAlma.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=alma_farm]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				$.post('cj/cuen/get_config_logi',function(data){
					if(data.AGUA!=null){
						p.$w.find('[name=alma_agua]').html(data.AGUA.nomb).data('data',data.AGUA);
						p.$w.find('[name=alma_farm]').html(data.FARM.nomb).data('data',data.FARM);
					}
					K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
	}
};
define(
	['lg/alma'],
	function(lgAlma){
		return lgConf;
	}
);