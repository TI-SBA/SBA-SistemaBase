/*************************************************************************
  Logistica: Reportes */

lgRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'lg',
			action: 'lgRepo',
			titleBar: {
				title: 'Reportes de Logistica'
			}
		});
		
		new K.Panel({
			contentURL: 'lg/repo/index2',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				var $mainPanel = p.$w;
				
				var $section1 = $mainPanel.find('#section1');
				$.post('lg/alma/all',function(data){
					var $almacen = $section1.find('[name=almacen]');
					if(data!=null){
						for(var i=0;i<data.length;i++){
							$almacen.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						}
					}
				},'json');
				$section1.find('[name=btnSelectClasif]').click(function(){
					prClas.windowSelect({
						bootstrap: true,
						logistica: true,
						callback: function(data){
							$section1.find('[name=clasif]').html(data.cod).data('data',data);
						}
					});
				});

				$section1.find('[name=btnSelectCuenta]').click(function(){
					ctPcon.windowSelect({
						bootstrap: true,
						logistica: true,
						callback: function(data){
							$section1.find('[name=cuenta]').html(data.cod).data('data',data);
						}
					});
				});
				$section1.find('[name=fecfin]').datepicker({
					format:'yyyy-mm-dd'
				});
				$section1.find('[name=btnImprimir]').click(function(){
					params = new Object;
					params.fecfin = $section1.find('[name=fecfin]').val();
					params.cuenta = $section1.find('[name=cuenta]').data('data')._id.$id;
					params.clasif = $section1.find('[name=clasif]').data('data')._id.$id;
					params.almacen = $section1.find('[name=almacen] :selected').val();
					var url = 'lg/repo/listado_saldos?'+$.param(params);
					K.windowPrint({
						id:'windowlgRepo1',
						title: "Reporte / Informe",
						url: url
					});
				});
			}
		});
	}
};
define(
	['ct/pcon','pe/conc','mg/enti'],
	function(ctPcon, peConc, mgEnti){
		return lgRepo;
	}
);