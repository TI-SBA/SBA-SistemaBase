ddRped = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'dd',
			action: 'ddRped',
			titleBar: {
				title: 'Reportes de Pedidos'
			}
		});
		
		new K.Panel({
			contentURL: 'dd/rped/index',
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
					var url = 'dd/pedi/get_rpedrte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
				//
				p.$section2 = p.$w.find('#section2');
				p.$section2.find('[name=fecini]').datepicker();
				p.$section2.find('[name=fecfin]').datepicker();
				p.$section2.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section2.find('[name=fecini]').val();
					params.fecfin = p.$section2.find('[name=fecfin]').val();
					var url = 'dd/pedi/get_rpedrte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});

				//
				//
			

				//
				p.$section3 = p.$w.find('#section3');
				p.$section3.find('[name=fecini]').datepicker();
				p.$section3.find('[name=fecfin]').datepicker();
				p.$section3.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.fecini = p.$section3.find('[name=fecini]').val();
					params.fecfin = p.$section3.find('[name=fecfin]').val();
					var url = 'dd/pedi/get_rpedrte?'+$.param(params);
					window.open(url,'_blank');
				}).button({icons: {primary: 'ui-icon-print'}});
				//
				p.$section4 = p.$w.find('#section4');
				p.$section4.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section4.find('[name=ano] :selected').val();
					//params.hasta_mes = p.$section1.find('[name=mes] :selected').val();
					var url = 'ct/repo/bala_cons?'+$.param(params);
					window.open(url,'_blank');
				});

			}
		});
	}
};
define(
	function(){
		return ddRped;
	}
);