tiBack = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ti',
			action: 'tiBack',
			titleBar: {
				title: 'Copia de Seguridad'
			}
		});
		
		new K.Panel({
			contentURL: 'ti/back',
			store: false,
			onContentLoaded: function(){
		   		p.$w = $('#mainPanel');
		   		p.$w.find('[name=btnSave]').click(function(){
		   			K.block();
		   			$.post('ti/back/save',function(){
		   				//
		   				K.unblock();
		   			});
		   		});
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return tiBack;
	}
);