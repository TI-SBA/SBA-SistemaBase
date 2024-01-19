lgList = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'lg',
			action: 'lgList',
			titleBar: {
				title: 'Listados'
			}
		});
		
		new K.Panel({
			contentURL: 'lg/list',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=periodo]').datepicker( {
				    format: "mm-yyyy",
				    viewMode: "months", 
				    minViewMode: "months"
				});
				p.$w.find('[name=btnCta]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({
						bootstrap: true,
						logistica: true,
						callback: function(data){
							$this.closest('.form-group').find('[name=cuenta]').html(data.cod).data('data',data);
						}
					});
				});
				
				
				
				
				
				
				
				
				
				
				
				p.$w.find('.contact-box:eq(0) button').click(function(e){
					e.preventDefault();
					var valor = $(this).attr('data-type'),
					periodo = p.$w.find('.contact-box:eq(0) [name=periodo]').val(),
					tipo = p.$w.find('.contact-box:eq(0) [name=mostrar] option:selected').val();
					if(periodo==''){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un periodo!'
						});
					}
					var mes = periodo.substr(0,2),
					ano = periodo.substr(3,6);
					if(valor=='pdf'){
						var mes = periodo.substr(0,2),
						ano = periodo.substr(3,6);
						window.open('lg/list/resumen?type=pdf&mes='+mes+'&ano='+ano);
					}else{
						window.open('lg/list/resumen?type=xls&mes='+mes+'&ano='+ano);
					}
				});
				
				
				
				
				
				
				
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return lgList;
	}
);