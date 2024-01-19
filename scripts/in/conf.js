inConf = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'in',
			action: 'inConf',
			titleBar: {
				title: 'Configuraci&oacute;n de Caja Inmuebles'
			}
		});
		
		new K.Panel({
			contentURL: 'cj/cuen/conf_inmu',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: 'IN',
							TIEMPO_COBRANZA: p.$w.find('[name=tiempo_cobranza]').val()
						};
						var tmp = p.$w.find('[name=serv_mor]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnMor]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Cuenta de Mora!',
								type: 'error'
							});
						}else
							data['MOR'] = ctPcon.dbRel(tmp);
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
						var tmp = p.$w.find('[name=file_play]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnPla]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Archivo de Excel!',
								type: 'error'
							});
						}else
							data['FILE_PLAYA'] = mgMult.dbRel(tmp);
						var tmp = p.$w.find('[name=cobranza_dudosa]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnCob]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Cuenta de Cobranza Dudosa!',
								type: 'error'
							});
						}else
							data['COBRANZA'] = ctPcon.dbRel(tmp);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/cuen/save_config_inmu',data,function(){
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
				p.$w.find('[name=btnIgv]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=serv_igv]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnMor]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=serv_mor]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnPla]').click(function(){
					var $this = $(this);
					mgMult.selectFile({cbAdd: function(data){
						$this.closest('.input-group').find('[name=file_play]').html(data.filename).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnCob]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=cobranza_dudosa]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				$.post('cj/cuen/get_config_inmu',function(data){
					if(data.IGV!=null){
						p.$w.find('[name=serv_igv]').html(data.IGV.cod).data('data',data.IGV);
						p.$w.find('[name=serv_mor]').html(data.MOR.cod).data('data',data.MOR);
						p.$w.find('[name=file_play]').html(data.FILE_PLAYA.filename).data('data',data.FILE_PLAYA);
						p.$w.find('[name=cobranza_dudosa]').html(data.COBRANZA.cod).data('data',data.COBRANZA);
						p.$w.find('[name=tiempo_cobranza]').val(data.TIEMPO_COBRANZA);
					}
					K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
	}
};
define(
	['ct/pcon','mg/mult'],
	function(ctPcon,mgMult){
		return inConf;
	}
);