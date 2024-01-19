faLote = {
	init: function(){
		K.initMode({
			mode: 'fa',
			action: 'faLote',
			titleBar: {
				title: 'Guias de Remisi&oacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Guia de Remision',f:'guia.num'},{n:'Producto',f:'producto.nomb'},'Stock Inicial','Stock Actual',{n:'Fecha de Vencimiento',f:'fecven'},{n:'Registrado',f:'fecreg'}],
					data: 'fa/lote/lista',
					params: {},
					itemdescr: 'lote(s)',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+220+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.guia.num+'</td>');
						$row.append('<td>'+data.producto.nomb+'</td>');
						$row.append('<td>'+data.cant_ini+'</td>');
						$row.append('<td>'+data.cant+'</td>');
						if(data.fecven!=null){
							$row.append('<td><label class="label label-warning">'+ciHelper.date.format.bd_ymdhi(data.fecven)+'</label></td>');
							/* AQUI LA VALIDACION PARA VER SI ESTA CERCANO EL VENCIMIENTO */
						}else
							$row.append('<td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'+mgEnti.formatName(data.autor)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							faLote.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(1)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_edi,#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									faLote.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowDetails: function(p){
		K.incomplete();
	}
};
define(
	['mg/enti','fa/prod'],
	function(mgEnti,faProd){
		return faLote;
	}
);