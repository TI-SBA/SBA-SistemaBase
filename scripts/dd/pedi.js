ddPedi = {
	
	movimiento:{
		"0":"En Proceso",
		"1":"Documento Entregado",
		"2":"Devuelto a Archivo Central"
	},
	
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb,
			desc: item.desc
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddPedi',
			titleBar: {
				title: 'Lista de Pedidos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nro. de Pedido','Documento','Direccion','Oficina','Movimiento'],
					data: 'dd/pedi/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddPedi.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.num+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.disol+'</td>');
						$row.append('<td>'+data.ofsol+'</td>');
						$row.append('<td>'+ddPedi.movimiento[data.movi]+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddPedi.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPear", {
							bindings: {
								'conMenPear_edi': function(t) {
									ddPedi.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPear_print': function(t) {
									ddPedi.windowDetails({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPear_print':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Pedido de Archivo",
										url:"dd/pedi/print?_id="+K.tmp.data("id")
									});
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditPedido',
			title: 'Editar Pedido: ',
			contentURL: 'dd/pedi/edit',
			width: 500,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							num: p.$w.find('[name=num]').val(),
							dire: p.$w.find('[name=dire]').val(),
							ofic: p.$w.find('[name=ofic]').val(),
							movi: p.$w.find('[name=movi]').val(),
						};
						
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el item!',type: 'error'});
						}if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el item!',type: 'error'});
						}if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el item!',type: 'error'});
						}if(data.ofic==''){
							p.$w.find('[name=ofic]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el item!',type: 'error'});
						}if(data.movi==''){
							p.$w.find('[name=movi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el item!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/pedi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Area actualizada!"});
							ddPedi.init();
							K.closeWindow(p.$w.attr('id'));
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditPedido');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						
					},bootstrap: true});
				});
				$.post('dd/pedi/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=num]').val(data.num);
					p.$w.find('[name=dire]').val(data.dire);
					p.$w.find('[name=ofic]').val(data.ofic);
					p.$w.find('[name=movi]').val(data.movi);

					K.unblock();
				},'json');
			}
		});
	},
};
define(
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return ddPedi;
	}
);