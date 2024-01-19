/*******************************************************************************
comprobantes recibos */
cjCompRec = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cjCompRec',
			titleBar: {
				title: 'Comprobantes: Recibos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'',f:'num'},{n:'Cliente',f:'cliente.fullname'},'Total',{n:'Registrado',f:'fecreg'}],
					data: 'cj/comp/search',
					params: {
						tipo: 'R',
						modulo: 'CM'
					},
					itemdescr: 'recibo(s) de caja',
					toolbarHTML: '<button name="btnGen">Generar Recibo de Ingresos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnGen]').click(function(){
							cjComp.windowGen();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>&nbsp;&nbsp;</td>');
						$row.find('td:last').css('background',cjComp.states[data.estado].color).addClass('vtip').attr('title',cjComp.states[data.estado].descr);
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						var en_reemplazo_de = '';
						if(data.comprobante_cambiado){
							en_reemplazo_de = '('+data.comprobante_cambiado.serie+'-'+data.comprobante_cambiado.num+')';
						}
						$row.append('<td>'+data.serie+'-'+data.num+' '+en_reemplazo_de+'</td>');
						$row.append('<td>'+ciHelper.enti.formatName(data.cliente)+'</td>');
						if(data.cliente.doc!=null){
							$row.find('td:last').append('<br /><b>'+data.cliente.doc+'</b>');
						}else if(data.cliente.docident!=null){
							$row.find('td:last').append('<br /><b>'+mgEnti.formatIden(data.cliente)+'</b>');
						}
						$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'<br />'
							+ciHelper.enti.formatName(data.autor)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "cj/comp/print_reci?id="+$(this).data('id')
							});
						}).data('estado',data.estado).data('data',data).contextMenu("conMenCjComp", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('estado')=='X') $('#conMenCjComp_cam',menu).remove();
								if(K.tmp.data('estado')!='X') $('#conMenCjComp_eli',menu).remove();
								/*if(K.tmp.data('data').vouchers==null)
									$('#conMenCjComp_vou',menu).remove();*/
								return menu;
							},
							bindings: {
								'conMenCjComp_imp': function(t) {
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "cj/comp/print_reci?id="+K.tmp.data('id')
									});
								},
								'conMenCjComp_anu': function(t) {
									cjComp.windowAnular({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenCjComp_cam': function(t) {
									cjComp.windowCambiar({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
								},
								'conMenCjComp_vou': function(t) {
									cjComp.windowVoucher({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
								},
								'conMenCjComp_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('cj/comp/eliminar',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											cjCompRec.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Comprobante');
								}
							}
						});
						return $row;
					}
				});
			}
		});
	}
};