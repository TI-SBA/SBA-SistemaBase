ctRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ct',
			action: 'ctRepo',
			titleBar: {
				title: 'Reportes de Contabilidad'
			}
		});
		
		new K.Panel({
			contentURL: 'ct/repo/index2',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$section1 = p.$w.find('#section1');
				p.$section1.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.ano = p.$section1.find('[name=ano] :selected').val();
					params.hasta_mes = p.$section1.find('[name=mes] :selected').val();
					var url = 'ct/repo/bala_cons?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section2 = p.$w.find('#section2');
				p.$section2.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.ano = p.$section2.find('[name=ano] :selected').val();
					params.hasta_mes = p.$section2.find('[name=mes] :selected').val();
					var url = 'ct/repo/bala_comp?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section3 = p.$w.find('#section3');
				p.$section3.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.ano = p.$section3.find('[name=ano]').val();
					params.hasta_mes = p.$section3.find('[name=mes] :selected').val();
					params.tipo = p.$section3.find('[name=tipo] :selected').val();
					var url = 'ct/repo/movi_cuen?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				p.$section4 = p.$w.find('#section4');
				p.$section4.find('[name=fecha]').datepicker();
				p.$section4.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.fecha = p.$section4.find('[name=fecha]').val();
					var url = 'in/repo/generar_resumen_contingencia_pei?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return ctRepo;
	}
);