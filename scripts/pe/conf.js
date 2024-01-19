peConf = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'pe',
			action: 'peConf',
			titleBar: {
				title: 'Configuraci&oacute;n de Recursos Humanos'
			}
		});
		
		new K.Panel({
			contentURL: 'pe/conf',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnGenerar]').click(function(){
					var $w;
					new K.Modal({
						id: 'windowGenerar',
						title: 'Generar Horarios de Periodo',
						contentURL: 'pe/conf/generar_view',
						store: false,
						buttons: {
							'Generar': {
								icon: 'fa-gear',
								type: 'success',
								f: function(){
									var data = {
										ini: $w.find('[name=ini]').val(),
										fin: $w.find('[name=fin]').val()
									};
									if(data.ini==''){
										$w.find('[name=ini]').focus();
										return K.msg({
											title: ciHelper.titles.infoReq,
											text: 'Debe ingresar una fecha de inicio',
											type: 'error'
										});
									}
									if(data.fin==''){
										$w.find('[name=fin]').focus();
										return K.msg({
											title: ciHelper.titles.infoReq,
											text: 'Debe ingresar una fecha de fin',
											type: 'error'
										});
									}
									K.block();
									$.post('pe/conf/generar',data,function(){
										//
										K.unblock();
									},'json');
								}
							},
							'Cancelar': {
								icon: 'fa-close',
								type: 'danger',
								f: function(){
									K.closeWindow($w.attr('id'));
								}
							}
						},
						onContentLoaded: function(){
							$w = $('#windowGenerar');
							$w.find('[name=ini],[name=fin]').datepicker();
						}
					});
				});
				K.unblock();
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return peConf;
	}
);