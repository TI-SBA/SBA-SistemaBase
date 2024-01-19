cmTerr = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function(item){
		var rpta = {
			_id: item._id.$id,
			nomb: item.nomb
		};
		
	},
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmTerr',
			titleBar: {
				title: 'Circuito de Terror'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Fecha','Nro. de Entradas',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'cm/terr/lista',
					params: {},
					itemdescr: 'Fecha(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							cmTerr.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+moment(data.fecve.sec,'X').format('LL')+'</td>');
						$row.append('<td>'+'----'+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							cmTerr.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenCmCir", {
							/*onShowMenu: function($row, menu) {
								$('#conMenCmCir_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenCmCir_hab',menu).remove();
								else $('#conMenCmCir_edi,#conMenCmCir_des',menu).remove();
								return menu;
							},*/
							bindings: {
								'conMenCmCir_sale': function(t) {
									cmTerr.windowSale({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenCmCir_edi': function(t) {
									cmTerr.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNew',
			title: 'Nueva Fecha de Evento',
			contentURL: 'cm/terr/edit',
			width: 550,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							fecve: p.$w.find('[name=fecve]').val(),
						};
						if(data.fecve==null){
							p.$w.find('[name=fecve]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("cm/terr/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Tipo agregado!"});
							cmTerr.init();
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
				p.$w = $('#windowNew');
				p.$w.find('[name=fecve]').datepicker({
					format: 'yyyy-mm-dd'
				});
			}
		});
	},
	windowSale: function(p){
		if(p==null) p={};
		p.calcular = function(){
			var total = 0;
			var mont = parseFloat(p.$w.find('[name=monto]').val());
			var cant = parseFloat(p.$w.find('[name=cant]').val());
			total = mont * cant;
			p.$w.find('[name=total]').val(total);
		}
		new K.Panel({ 
			title: 'Venta de Tickets',
			contentURL: 'cm/terr/ticket',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cliente: p.$w.find('[name=cliente] [name=mini_enti]').data('data'),
							fecven: p.$w.find('[name=fecven]').val(),
							cant: p.$w.find('[name=cant]').val(),
							monto: p.$w.find('[name=monto]').val(),
							observ: p.$w.find('[name=observ]').val(),
							fecve: p.$w.find('[name=fecve]').val(),
							num: p.$w.find('[name=num]').val(),
							serie: p.$w.find('[name=serie]').val(),
							tickets: [],
							items : [],
							total: p.$w.find('[name=total]').val(),
							moneda: 'S',
							tipo: 'R',
							modulo: 'CM',
							estado: 'R',
							periodo: "230800",
							caja: {
									_id: '51a755374d4a13540a000031',
									nomb: "Recaudación de Cementerio",
										local: {
										_id: '51a53a624d4a13441100001e',
										descr: 'Cementerio General',
										direccion: 'Plazoleta Cementerio S/N'
									}
								}
						}
						if(data.cliente==null){
							p.$w.find('[name=cliente] [name=btnSel]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un cliente!',
								type: 'error'
							});
						}else data.cliente = mgEnti.dbRel(data.cliente);
						var item = {
							cuenta_cobrar:{
								servicio: mgServ.dbRel(p.$w.find('[name=servicio]').data('data')),
								cuenta: ctPcon.dbRel(p.$w.data('concep').cuenta),
								costo: p.$w.find('[name=costo]').val()
							},
							conceptos: [
								{
								  concepto: {
									_id: "6356cd763e603727628b4567",
									nomb: 'Entrada para el evento “Mitos y Leyendas” General Mitos',
								  },
								  monto: p.$w.find('[name=monto]').val(),
								},
							  ],
						};
						data.items.push(item);

						if(p.$w.find('[name=gridTicket] tbody tr').length>0){
							for(var i=0;i< p.$w.find('[name=gridTicket] tbody tr').length;i++){
								var $row = p.$w.find('[name=gridTicket] tbody tr').eq(i);
								var _tickets ={
									ini: $row.find('[name=ini]').val(),
									fin: $row.find('[name=fin]').val(),
									
								}
								data.tickets.push(_tickets);
								
							}
						}


						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("cm/terr/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Ventas actualizadas!"});
							cmTerr.init();
							console.log(result);
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "cj/comp/print_reci?id="+result._id.$id
							});
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						cmTerr.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
					p.$w.find('[name=btnServ]').click(function(){
						mgServ.windowSelect_Circuito({params: {_id: '6356c8163e6037b9608b456a'
					},callback: function(serv){
							p.$w.find('[name=servicio]').html(serv.nomb).data('data',serv);
							$.post('cj/conc/get_serv',{id:serv._id.$id},function(data){
								console.log(data.serv[0].formula);
								var formula = parseFloat(data.serv[0].formula);
								p.$w.find('[name=monto]').val(formula);
								p.$w.data('serv',serv).data('concep',data.serv[0]);
							},'json');
						},bootstrap:true});
					});

					p.$w.find('[name=cliente] .panel-title').html('DATOS DEL CLIENTE');
					p.$w.find('[name=cliente] [name=btnSel]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=cliente] [name=mini_enti]'),data);
						},bootstrap: true});
					});

					p.$w.find('[name=cliente] [name=btnAct]').click(function(){
						if(p.$w.find('[name=cliente] [name=mini_enti]').data('data')==null){
							K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe elegir una entidad!',
								type: 'error'
							});
						}else{
							mgEnti.windowEdit({callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=cliente] [name=mini_enti]'),data);
							},id: p.$w.find('[name=cliente] [name=mini_enti]').data('data')._id.$id});
						}
					});
				
					new K.grid({
						$el: p.$w.find('[name=gridTicket]'),
						cols: ['Del','Al','Eliminar'],
						stopLoad: true,
						pagination: false,
						search: false,
						store:false,
						toolbarHTML: '<button type = "button" name="btnAddTicket" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</button >',
						onContentLoaded: function($el){
							$el.find('button').click(function(){
								var $row = $('<tr class="item">');
								$row.append('<td><input type="text" name="ini"/></td>');
								$row.append('<td><input type="text" name="fin"/></td>');
								$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								p.$w.find('[name=gridTicket] tbody').append($row);
								
							});
								
						}
					});
				
				p.$w.find("[name=fecven]").datepicker({
						format: 'yyyy/mm/dd',
					startDate: '-3d'
				});
				p.$w.find("[name=fecve]").datepicker({
						format: 'yyyy/mm/dd',
					startDate: '-3d'
			});
				p.$w.find('[name=fecven]').val(ciHelper.date.get.now_ymd());

				$.post("cm/terr/get_num",function(data){
					var num = 0;
					if(data == null){
						num = 0;
					}else{
						num = parseFloat(data.actual) + 1;
					}
					
					p.$w.find('[name=num]').val(num);
					p.$w.find('[name=serie]').val(data.serie);
					

				},'json');
				p.$w.find('[name=cant],[name=monto],[name=total]').keyup(function(){
					p.calcular();
				});
				$.post('cm/terr/get',{_id:p.id},function(data){
					p.$w.find('[name=fecve]').val(moment(data.fecve.sec,'X').format('YYYY-MM-DD'));
				},'json');
			}
		});
	},
	
};
define(
	['mg/enti','ct/pcon','mg/serv'],
	function(mgEnti,ctPcon,mgServ){
		return cmTerr;
	}
);