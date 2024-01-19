adRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ad',
			action: 'adRepo',
			titleBar: {
				title: 'Reportes de Moises Heresi'
			}
		});
		
		new K.Panel({
			contentURL: 'ad/repo/index',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$section1 = p.$w.find('#section1');
				p.$section1.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section1.find('[name=ano] :selected').val();
					params.hasta_mes = p.$section1.find('[name=mes] :selected').val();
					var url = 'ct/repo/bala_cons?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section2 = p.$w.find('#section2');
				p.$section2.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section2.find('[name=ano] :selected').val();
					params.mes = p.$section2.find('[name=mes] :selected').val();
					var url = 'ad/psic/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section3 = p.$w.find('#section3');
				p.$section3.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section3.find('[name=ano] :selected').val();
					params.mes = p.$section3.find('[name=mes] :selected').val();
					var url = 'ad/psiq/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section4 = p.$w.find('#section4');
				p.$section4.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section4.find('[name=ano] :selected').val();
					params.mes = p.$section4.find('[name=mes] :selected').val();
					params.inyectables = p.$section4.find('[name=inyectables]').val();
					params.haldol_dosis = p.$section4.find('[name=haldol_dosis]').val();
					params.haldol_aplic = p.$section4.find('[name=haldol_aplic]').val();
					var url = 'ad/padi/get_doctor?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section5 = p.$w.find('#section5');
				p.$section5.find('[name=btnImprimir]').click(function(){	
					var params = new Object;
					params.ano = p.$section5.find('[name=ano]').val();
					params.mes = p.$section5.find('[name=mes] :selected').val();
					var url = 'ad/padi/get_mensual?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				p.$section6 = p.$w.find('#section6');
				p.$section6.find('[name=fecini]').datepicker();
				p.$section6.find('[name=fecfin]').datepicker();
				p.$section6.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section6.find('[name=fecini]').val();
					params.fecfin = p.$section6.find('[name=fecfin]').val();
					var url = 'ad/padi/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
			}
		});
	}
};
define(
	function(){
		return adRepo;
	}
);