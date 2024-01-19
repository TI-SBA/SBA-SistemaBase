ctCntg = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ct',
			action: 'ctCntg',
			titleBar: {
				title: 'Comprobantes'
			}
		});
		new K.Panel({
			contentURL: 'ct/cntg',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnMacro]').click(function(){
					window.open('ayuda/formato-contingencia-sunat-05-01-2017.zip');
				});
			}
		});
	}
};
define(
	function(){
		return ctCntg;
	}
);