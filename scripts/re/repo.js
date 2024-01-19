reRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 're',
			action: 'reRepo',
			titleBar: {
				title: 'Reportes de Recursos Economicos'
			}
		});
		
		new K.Panel({
			contentURL: 're/repo/index2',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');

				p.$section1 = p.$w.find('#section1');
				p.$section1.find('[name=fecha]').datepicker();
				p.$section1.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.fecha = p.$section1.find('[name=fecha]').val();
					var url = 'in/repo/generar_resumen_contingencia_pei?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
			}

		});
	}
};
define(
	
	function(){
		return reRepo;
	}
);