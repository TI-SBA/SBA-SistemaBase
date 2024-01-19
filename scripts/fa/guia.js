faGuia = {
	init: function(){
		K.initMode({
			mode: 'fa',
			action: 'faGuia',
			titleBar: {
				title: 'Guias de Remisi&oacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Gu&iacute;a de Remisi&oacute;n',{n:'N&uacute;mero',f:'num'},{n:'Registrado',f:'fecreg'}],
					data: 'fa/guia/lista',
					params: {},
					itemdescr: 'guia(s) de remision',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+220+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						if(data.guia!=null)
							$row.append('<td>'+data.guia+'</td>');
						else
							$row.append('<td>--</td>');
						$row.append('<td>'+data.num+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'+mgEnti.formatName(data.autor)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							faGuia.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(1)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_edi,#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									faGuia.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
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
		return faGuia;
	}
);