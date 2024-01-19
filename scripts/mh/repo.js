mhRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'mh',
			action: 'mhRepo',
			titleBar: {
				title: 'Reportes de Moises Heresi'
			}
		});
		
		new K.Panel({
			contentURL: 'mh/repo/index',
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
					var url = 'mh/psic/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section3 = p.$w.find('#section3');
				p.$section3.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section3.find('[name=ano] :selected').val();
					params.mes = p.$section3.find('[name=mes] :selected').val();
					var url = 'mh/psiq/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section4 = p.$w.find('#section4');
				p.$section4.find('[name=btnImprimir_MH]').click(function(){
					var params = new Object;
					params.ano = p.$section4.find('[name=ano] :selected').val();
					params.mes = p.$section4.find('[name=mes] :selected').val();
					params.inyectables_s = p.$section4.find('[name=inyectables_s]').val();
					params.haldol_dosis_s = p.$section4.find('[name=haldol_dosis_s]').val();
					params.haldol_aplic_s = p.$section4.find('[name=haldol_aplic_s]').val();
					var url = 'mh/padi/get_doctor_MH?'+$.param(params);
					window.open(url,'_blank');
				});
				/*------------------------------------------------------------------------*/
				p.$section4.find('[name=btnImprimir_AD]').click(function(){
					var params = new Object;
					params.ano = p.$section4.find('[name=ano] :selected').val();
					params.mes = p.$section4.find('[name=mes] :selected').val();
					params.inyectables_a = p.$section4.find('[name=inyectables_a]').val();
					params.haldol_dosis_a = p.$section4.find('[name=haldol_dosis_a]').val();
					params.haldol_aplic_a = p.$section4.find('[name=haldol_aplic_a]').val();
					var url = 'mh/padi/get_doctor_AD?'+$.param(params);
					window.open(url,'_blank');
				});

				p.$section5 = p.$w.find('#section5');
				p.$section5.find('[name=btnImprimir]').click(function(){	
					var params = new Object;
					params.ano = p.$section5.find('[name=ano]').val();
					params.mes = p.$section5.find('[name=mes] :selected').val();
					var url = 'mh/padi/get_mensual?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				p.$section6 = p.$w.find('#section6');
				p.$section6.find('[name=fecini]').datepicker();
				p.$section6.find('[name=fecfin]').datepicker();
				p.$section6.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section6.find('[name=fecini]').val();
					params.fecfin = p.$section6.find('[name=fecfin]').val();
					var url = 'mh/padi/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				p.$section7 = p.$w.find('#section7');
				p.$section7.find('[name=fecini]').datepicker();
				p.$section7.find('[name=fecfin]').datepicker();
				p.$section7.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section7.find('[name=fecini]').val();
					params.fecfin = p.$section7.find('[name=fecfin]').val();
					var url = 'mh/paho/get_reporte_hospitalizados?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				p.$section8 = p.$w.find('#section8');
				p.$section8.find('[name=fecini]').datepicker();
				p.$section8.find('[name=fecfin]').datepicker();
				p.$section8.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section8.find('[name=fecini]').val();
					params.fecfin = p.$section8.find('[name=fecfin]').val();
					var url = 'mh/padi/get_reporte_cubanas?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
			}
		});
	}
};
define(
	function(){
		return mhRepo;
	}
);