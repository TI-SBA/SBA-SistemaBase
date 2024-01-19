ddRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'dd',
			action: 'ddRepo',
			titleBar: {
				title: 'Reporte de Registros'
			}
		});
		
		new K.Panel({
			contentURL: 'dd/repo/index',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$section1 = p.$w.find('#section1');
				p.$section1.find('[name=fecini]').datepicker();
				p.$section1.find('[name=fecfin]').datepicker();
				p.$section1.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section1.find('[name=fecini]').val();
					params.fecfin = p.$section1.find('[name=fecfin]').val();
					var url = 'dd/regi/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});


				p.$section2 = p.$w.find('#section2');
				p.$section2.find('[name=fecini]').datepicker();
				p.$section2.find('[name=fecfin]').datepicker();
				p.$section2.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section2.find('[name=fecini]').val();
					params.fecfin = p.$section2.find('[name=fecfin]').val();
					var url = 'dd/redo/get_registro?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				p.$section5 = p.$w.find('#section5');
				p.$section5.find('[name=fecini]').datepicker();
				p.$section5.find('[name=fecfin]').datepicker();
				p.$section5.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section5.find('[name=fecini]').val();
					params.fecfin = p.$section5.find('[name=fecfin]').val();
					var url = 'dd/depu/get_depuracion?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
				p.$section7 = p.$w.find('#section7');
				p.$section7.find('[name=fecini]').datepicker();
				p.$section7.find('[name=fecfin]').datepicker();
				p.$section7.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section7.find('[name=fecini]').val();
					params.fecfin = p.$section7.find('[name=fecfin]').val();
					var url = 'dd/dohi/get_reporte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
			}
		});
	}
};
define(
	function(){
		return ddRepo;
	}
);