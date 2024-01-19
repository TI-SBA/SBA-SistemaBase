agGuia = {
	states: {
		A: {
			descr: "Aprobado",
			color: "blue",
			label: '<span class="label label-primary">Aprobado</span>'
		},
		X:{
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-danger">Anulado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'ag',
			action: 'agGuia',
			titleBar: {
				title: 'Guias de Remisi&oacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado',{n:'N&uacute;mero',f:'num'},'Almacen Origen','Almacen Destino','Detalle',{n:'Registrado',f:'fecreg'}],
					data: 'ag/guia/lista',
					params: {},
					itemdescr: 'guia(s) de remision',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+220+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+agGuia.states[data.estado].label+'</td>');
						$row.append('<td>'+data.num+'</td>');
						var origen = '--';
						if(data.almacen_origen!=null){
							origen = data.almacen_origen.nomb;
						}
						var destino = '--';
						if(data.almacen_destino!=null){
							destino = data.almacen_destino.nomb;
						}
						$row.append('<td>'+origen+'</td>');
						$row.append('<td>'+destino+'</td>');
						if(data.items!=null){
							$row.append('<td>');
							for(var i=0; i<data.items.length; i++){
								if(i!=0)
									$row.find('td:last').append('<br />');
								$row.find('td:last').append(data.items[i].producto.nomb+', <kbd>cant: '+data.items[i].cant+'</kbd>');
							}
						}else{
							$row.append('<td>');
						}
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'+mgEnti.formatName(data.autor)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							agGuia.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(1)').html()});
						}).contextMenu("conMenLgGuia", {
							onShowMenu: function($row, menu) {
								$('#conMenLgGuia_edi, #conMenLgGuia_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgGuia_ver': function(t) {
									agGuia.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
								},
								'conMenLgGuia_anu': function(t) {
									ciHelper.confirm('&#191;Desea <b>Anular</b> La guia de remision <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;. <br/>Esta accion realizara la reposicion de stocks en sus respectivos almacenes',
									function(){
										K.sendingInfo();
										$.post('ag/guia/anular',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Guia Anulada',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											agGuia.init();
										});
									},function(){
										$.noop();
									},'Anulaci&oacute;n de Guia');
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
	['mg/enti','ag/prod'],
	function(mgEnti,agProd){
		return agGuia;
	}
);