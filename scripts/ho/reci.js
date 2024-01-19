hoReci = {
	states: {
		R: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		X:{
			descr: "Anulado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Anulado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'hoReci',
			titleBar: {
				title: 'Recibos de Caja'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'',f:'num'},{n:'Cliente',f:'cliente.fullname'},'Total',{n:'Registrado',f:'fecreg'}],
					data: 'ho/reci/lista',
					params: {modulo: 'MH'},
					itemdescr: 'recibo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Recibo</button>'+
						'<button name="btnGen" class="btn btn-success"><i class="fa fa-gears"></i> Ingresos Salud Mental</button>'+
						'<button name="btnGen_" class="btn btn-success"><i class="fa fa-gears"></i> Ingresos Adicciones</button>'+
						//'<button name="btnGen__" class="btn btn-success"><i class="fa fa-gears"></i> Ingresos Mu&ntilde;oz</button>'+
						//'<button name="btnGenTard" class="btn btn-success"><i class="fa fa-gears"></i> Ingresos Turno Tarde</button>'+
					'&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
							//'<option value="LM">Laboratorio Mu&ntilde;oz</option>'+
							//'<option value="TD">Turno Tarde</option>'+
						'</select>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							hoReci.windowNew();
						});
						$el.find('[name=btnGen]').click(function(){
							hoReci.windowGen();
						});
						$el.find('[name=btnGen_]').click(function(){
							haReci.windowGen();
						});
						$el.find('[name=btnGen__]').click(function(){
							hoReci.windowGen__();
						});
						$el.find('[name=btnGenTard]').click(function(){
							hoReci.btnGenTard();
						});
						$el.find('[name=modulo]').change(function(){
							var modulo = $el.find('[name=modulo] option:selected').val();
							$grid.reinit({params: {modulo: modulo}});
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+hoReci.states[data.estado].label+'</td>');
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
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'
							+ciHelper.enti.formatName(data.autor)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ho/reci/print?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).data('data',data).contextMenu("conMenMhComp", {
							onShowMenu: function($r, menu) {
								if($r.data('estado')=='X') $('#conMenCjComp_cam',menu).remove();
								/*if(K.tmp.data('data').vouchers==null)
									$('#conMenCjComp_vou',menu).remove();*/
								return menu;
							},
							bindings: {
								'conMenMhComp_imp': function(t) {
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "ho/reci/print?_id="+K.tmp.data('id')
									});
								},
								'conMenMhComp_anu': function(t) {
									ciHelper.confirm('&#191;Desea <b>Anular</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ho/reci/anular',{_id: K.tmp.data('data')._id.$id},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											hoReci.init();
										});
									},function(){
										$.noop();
									},'Anulaci&oacute;n de Comprobante');
								},
								'conMenMhComp_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Comprobante <b>'+K.tmp.find('td:eq(2)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ho/reci/eliminar',{_id: K.tmp.data('data')._id.$id},function(){
											K.clearNoti();
											K.notification({title: 'Comprobante Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											hoReci.init();
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
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Crear Nuevo Cobro',
			contentURL: 'ho/reci/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							num: p.$w.find('[name=num]').val(),
							modulo: p.$w.find('[name=modulo]').val(),
							fecemi: p.$w.find('[name=fecemi]').val(),
							observ: p.$w.find('[name=observ]').val(),
							cliente: p.$w.find('[name=cliente]').data('data'),
							total: 0,
							items: []
						};
						if(data.cliente==null){
							p.$w.find('[name=btnCli]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cliente!',type: 'error'});
						}else data.cliente = mgEnti.dbRel(data.cliente);
						if(p.$w.find('[name=grid] tbody .item').length==0){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar al menos un cobro!',
								type: 'error'
							});
						}else{
							for(var i=0,j=p.$w.find('[name=grid] tbody .item').length; i<j; i++){
								var $row = p.$w.find('[name=grid] tbody .item').eq(i);
								var item = {
									servicio: mgServ.dbRel($row.data('serv')),
									cuenta: ctPcon.dbRel($row.data('concep').cuenta),
									costo: $row.find('[name=costo]').val()
								};
								if($row.data('tari')!=null)
									item.tari = $row.data('tari');
								data.items.push(item);
								data.total += parseFloat($row.find('[name=costo]').val());
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ho/reci/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Recibo agregado!"});
							hoReci.init();
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Comprobante de Pago",
								url: "ho/reci/print?_id="+result._id.$id+"&print=1"
							});
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						hoReci.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=fecemi]').datepicker();
				p.$w.find('[name=btnCod]').click(function(){
					K.block();
					$.post('mh/paci/get_historia',{cod: p.$w.find('[name=cod]').val()},function(data){
						p.$w.find('[name=cliente]').html(mgEnti.formatName(data)).data('data',data);
						K.unblock();
					},'json');
				});
				p.$w.find('[name=btnCli]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						p.$w.find('[name=cliente]').html(mgEnti.formatName(data)).data('data',data);
					},bootstrap: true});
				});
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['Servicio','Costo',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgr"><i class="fa fa-search"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgr]').click(function(){
							mgServ.windowSelect_({params: {
								modulo: 'MH'
							},callback: function(serv){
								if(serv._id.$id==p.AGRI._id.$id){
									hoTara.windowSelect({
                                    	callback: function(tari){
                                        	hoReci.windowTari({
                                                data: tari,
                                            	callback: function(tara){
													$.post('cj/conc/get_serv',{id:serv._id.$id},function(data){
	                                                	var $row = $('<tr class="item">');
	                                                    $row.append('<td>'+tara.nomb+'('+tara.cant+' '+tara.unidad+')</td>');
	                                                    $row.append('<td><input type="number" name="costo" value="'+tara.total+'" /></td>');
	                                                    $row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
	                                                    $row.find('button').click(function(){
	                                                        $(this).closest('.item').remove();
	                                                    });
	                                                    $row.data('serv',serv).data('concep',data.serv[0]).data('tari',tara);
	                                                    p.$w.find('[name=grid] tbody').append($row);
	                                                    K.unblock();
	                                                },'json');
                                                }
                                            });
                                        }
                                    });
								}else if(serv._id.$id==p.GANA._id.$id){
									hoTarg.windowSelect({
                                    	callback: function(tari){
                                        	hoReci.windowTari({
                                                data: tari,
                                            	callback: function(tara){
													$.post('cj/conc/get_serv',{id:serv._id.$id},function(data){
	                                                	var $row = $('<tr class="item">');
	                                                    $row.append('<td>'+tara.nomb+'('+tara.cant+' '+tara.unidad+')</td>');
	                                                    $row.append('<td><input type="number" name="costo" value="'+tara.total+'" /></td>');
	                                                    $row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
	                                                    $row.find('button').click(function(){
	                                                        $(this).closest('.item').remove();
	                                                    });
	                                                    $row.data('serv',serv).data('concep',data.serv[0]).data('tari',tara);
	                                                    p.$w.find('[name=grid] tbody').append($row);
	                                                    K.unblock();
	                                                },'json');
                                                }
                                            });
                                        }
                                    });
								}else{
									K.block();
									$.post('cj/conc/get_serv',{id:serv._id.$id},function(data){
										var $row = $('<tr class="item">');
										$row.append('<td>'+serv.nomb+'</td>');
										$row.append('<td><input type="number" name="costo" value="'+parseFloat(data.serv[0].formula)+'" /></td>');
										$row.append('<td><button class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button').click(function(){
											$(this).closest('.item').remove();
										});
										$row.data('serv',serv).data('concep',data.serv[0]);
										p.$w.find('[name=grid] tbody').append($row);
										K.unblock();
									},'json');
								}
							},bootstrap: true});
						});
					}
				});
				K.block();
				$.post('ho/reci/next_num',{modulo:'MH'},function(data){
					p.num = data.num;
					p.$w.find('[name=num]').val(data.num);
					$.post('cj/cuen/get_config_hosp',function(data){
						p.AGRI = data.AGRI;
						p.GANA = data.GANA;
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	windowTari: function(p){
    	new K.Modal({
        	id: 'windowTari',
            title: 'Tarifas',
            contentURL: 'ho/reci/tari',
            width: 550,
            height: 200,
            buttons: {
            	'Aceptar': {
            		icon: 'fa-check',
            		type: 'success',
            		f: function(){
            			var data = {
            				_id: p.data._id.$id,
            				nomb: p.data.nomb,
            				unidad: p.data.unidad,
            				precio_base: p.data.precio,
            				cant: p.$w.find('[name=cant]').val(),
            				total: p.$w.find('[name=total]').html()
            			};
            			p.callback(data);
            			K.closeWindow(p.$w.attr('id'));
            		}
            	},
            	'Cancelar': {
            		icon: 'fa-close',
            		type: 'danger',
            		f: function(){
            			K.closeWindow(p.$w.attr('id'));
            		}
            	}
            },
            onContentLoaded: function(){
            	p.$w = $('#windowTari');
            	p.$w.find('[name=producto]').html(p.data.nomb);
            	p.$w.find('[name=unidad]').html(p.data.unidad);
            	p.$w.find('[name=precio]').html(p.data.precio);
            	p.$w.find('[name=cant]').val(1).keyup(function(){
            		var cant = parseFloat($(this).val()),
            		precio = parseFloat(p.data.precio);
            		if(isNaN(cant)) cant = 0;
            		p.$w.find('[name=total]').html(cant*precio);
            	}).keyup();
            }
        });
    },
	windowGen: function(p){
		if(p==null) p = {};
		$.extend(p,{
			fill: function(){
				if(p.$w.find('[name=fec]').val()>p.$w.find('[name=fecfin]').val()){
					p.$w.find('[name=fecfin]').datepicker('setValue',p.$w.find('[name=fec]').val());
				}
				var orga = p.$w.find('[name=orga]').data('data');
				/*if(p.$w.find('[name=fec]').datepicker('getDate')==null){
					p.$w.find('[name=fec]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
				}*/
				if(orga==null){
					p.$w.find('[name=btnOrga]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
				}
				K.block({$element: p.$w});
				$.post('cj/rein/get_rec_ho',{
					modulo: 'MH',
					tipo: p.$w.find('[name=tipo] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: '51a50e614d4a13c409000012',//orga._id.$id
					actividad: '51e9958c4d4a13440a00000d',//orga.actividad._id.$id
					componente: '51e99c964d4a13c404000015'//orga.componente._id.$id
				},function(data){
					var tmp_ctas_pat = [];
					p.$w.find('[name=gridComp] tbody').empty();
					p.$w.find('[name=gridAnu] tbody').empty();
					p.$w.find('[name=gridCod] tbody').empty();
					p.$w.find('[name=gridPag] tbody').empty();
					p.$w.find('[name=gridCont] tbody').empty();
					if(data.comp==null){
						K.unblock({$element: p.$w});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
					}
					p.$w.find('[name=planilla]').val(data.planilla);
					p.comp = data.comp;
					p.prog = data.prog;
					var $row = $('<tr class="item">');
					$row.append('<td>8501</td>');
					$row.append('<td>8201</td>');
					$row.append('<td>034</td>');
					$row.append('<td>001</td>');
					$row.append('<td>040</td>');
					$row.append('<td>0123</td>');
					$row.append('<td>30205</td>');
					$row.append('<td>1540</td>');
					$row.append('<td><select name="fuente"></td>');
					for(var k=0,l=p.fuen.length; k<l; k++){
						$row.find('select').append('<option value="'+p.fuen[k]._id.$id+'">'+p.fuen[k].cod+'</option>');
						$row.find('select option:last').data('data',p.fuen[k]);
					}
					p.$w.find('[name=gridCod] tbody').append($row);
					// Efectivo en pagos
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0,'D')+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					
					var tot_sol = 0,
					tot_dol = 0,
					tot_dol_sol = 0,
					total = 0;
					for(var i=0,j=data.comp.length; i<j; i++){
						var result = data.comp[i];
						if(result.estado=='X'){
							var $row = $('<tr class="item">');
							$row.append('<td>'+result.tipo+' '+result.serie+' '+result.num+'</td>');
							if(result.cliente._id!=null)
								$row.append('<td>'+mgEnti.formatName(result.cliente)+'</td>');
							else
								$row.append('<td>'+result.cliente+'</td>');
							$row.data('data',{
								_id: result._id.$id,
								tipo: result.tipo,
								serie: result.serie,
								num: result.num
							});
							p.$w.find('[name=gridAnu] tbody').append($row);
						}else{
							if(result.hospitalizacion!=null){
								result.total = parseFloat(result.total);
								var $row = $('<tr class="item">');
								$row.append('<td>'+result.cuenta.cod+' - '+result.cuenta.descr+'</td>');
								$row.append('<td>Recibo '+result.num+'</td>');
								$row.append('<td>'+'Hospitalizacion - '+mgEnti.formatName(result.cliente)+'</td>');
								$row.append('<td>'+ciHelper.formatMon(result.total,result.moneda)+'</td>');
								$row.data('data',{
									cuenta: {
										_id: result.cuenta._id.$id,
										cod: result.cuenta.cod,
										descr: result.cuenta.descr
									},
									comprobante: {
										_id: result._id.$id,
										tipo: result.tipo,
										serie: result.serie,
										num: result.num
									},
									concepto: 'Hospitalizacion - '+mgEnti.formatName(result.cliente)
								}).data('total',parseFloat(result.total));
								p.$w.find('[name=gridComp] tbody').append($row);
								tot_sol += parseFloat(result.total);
								var tmp_ctas_pat_i = -1;
								for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
									if(tmp_ctas_pat[tmp_i].cod==result.cuenta.cod.substr(0,9)){
										tmp_ctas_pat_i = tmp_i;
										tmp_i = tmp_j;
									}
								}
								if(tmp_ctas_pat_i==-1){
									tmp_ctas_pat.push({
										cod: result.cuenta.cod.substr(0,9),
										cuenta: result.cuenta,
										total: parseFloat(result.total)
									});
								}else{
									tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(result.total);
								}
								total += parseFloat(result.total);
							}else{
								result.total = parseFloat(result.total);
								for(var ii=0,jj=result.items.length; ii<jj; ii++){
									var $row = $('<tr class="item">'),
									item = result.items[ii];
									$row.append('<td>'+item.cuenta.cod+' - '+item.cuenta.descr+'</td>');
									$row.append('<td>Recibo '+result.num+'</td>');
									$row.append('<td>'+item.servicio.nomb+'</td>');
									$row.append('<td>'+ciHelper.formatMon(item.costo,result.moneda)+'</td>');
									$row.data('data',{
										cuenta: {
											_id: item.cuenta._id.$id,
											cod: item.cuenta.cod,
											descr: item.cuenta.descr
										},
										comprobante: {
											_id: result._id.$id,
											tipo: result.tipo,
											serie: result.serie,
											num: result.num
										},
										concepto: item.servicio.nomb
									}).data('total',item.costo);
									p.$w.find('[name=gridComp] tbody').append($row);
									tot_sol += parseFloat(item.costo);
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==item.cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: item.cuenta.cod.substr(0,9),
											cuenta: item.cuenta,
											total: parseFloat(item.costo)
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(item.costo);
									}
								}
								total += parseFloat(result.total);
							}
						}
					}
					// GENERACION AUTOMATICA DE CONTABILIDAD PATRIMONIAL
					var tmp_to = 0;
					for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
						var $row = $('<tr class="item">');
						$row.append('<td>'+tmp_ctas_pat[tmp_i].cod+'</td>');
						$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(tmp_ctas_pat[tmp_i].total)+'</td>');
						var tmp_cta_a = tmp_ctas_pat[tmp_i].cuenta;
						tmp_cta_a.tipo = 'D';
						$row.data('data',tmp_cta_a).data('total',tmp_ctas_pat[tmp_i].total).attr('name',tmp_ctas_pat[tmp_i].cuenta._id.$id);
						p.$w.find('[name=gridCont] tbody').append($row);
						tmp_to += parseFloat(tmp_ctas_pat[tmp_i].total);
					}
					tmp_to = parseFloat(K.round(tmp_to,2));
					var $row = $('<tr class="item">');
					$row.append('<td>1101.0101</td>');
					$row.append('<td>'+ciHelper.formatMon(tmp_to)+'</td>');
					$row.append('<td>');
					$row.data('data',{
						_id: '51a6473a4d4a13540a000009',
						cod: '1101.0101',
						descr: 'Caja M/N',
						tipo: 'H'
					}).data('total',tmp_to).attr('name','51a6473a4d4a13540a000009');
					p.$w.find('[name=gridCont] tbody .item:eq(0)').before($row);
					// TOTALES
					var $row = $('<tr class="item result">');
					$row.append('<td>');
					$row.append('<td>');
					$row.append('<td>Parcial</td>');
					$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
					$row.data('total',total);
					p.$w.find('[name=gridComp] tbody').append($row);
					p.$w.find('[name=gridPag] .item').eq(0).data('total',tot_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(0)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(1).data('total',tot_dol).data('total_sol',tot_dol_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_dol,'D'));
					p.$w.find('[name=gridPag] .item').eq(1)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_dol_sol));
					K.unblock({$element: p.$w});
				},'json');
			},
			calcDeb: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(9) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(9) [name=monto]').eq(i).val());
				}
				p.$w.find('.payment:eq(9) .result').remove();
				var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(9) .gridBody').append($row.children());
			},
			calcHab: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(11) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(11) .item').eq(i).data('total'));
				}
				p.$w.find('.payment:eq(11) .result').remove();
				var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(11) .gridBody').append($row.children());
			}
		});
		new K.Panel({
			title: 'Recibo de Ingresos',
			contentURL: 'ho/reci/generar',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'MH',
							cod: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							iniciales: p.$w.find('[name=iniciales]').val(),
							fec: p.$w.find('[name=fec]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							observ: p.$w.find('[name=observ]').val(),
							detalle: [],
							glosa: p.$w.find('[name=observ]').val(),
							cont_patrimonial: [],
							total: 0,
							efectivos: [],
							fuente: {
								_id: p.$w.find('[name=fuente] option:selected').data('data')._id.$id,
								cod: p.$w.find('[name=fuente] option:selected').data('data').cod,
								rubro: p.$w.find('[name=fuente] option:selected').data('data').rubro
							}
						},tot_deb=0,tot_hab=0,
						tmp = p.$w.find('[name=orga]').data('data');
						/*if(data.iniciales==''){
							p.$w.find('[name=iniciales]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar unas iniciales!',type: 'error'});
						}*/
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
						}
						if(tmp==null){
							p.$w.find('[name=btnOrga]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
						}
						
						for(var i=0,j=(p.$w.find('[name=gridComp] tbody .item').length-1); i<j; i++){
							var det = p.$w.find('[name=gridComp] tbody .item').eq(i).data('data');
							$.extend(det,{
								monto: p.$w.find('[name=gridComp] tbody .item').eq(i).data('total')
							});
							if(det.cuenta._id.$id!=null)
								det.cuenta._id = det.cuenta._id.$id;
							data.total += parseFloat(det.monto);
							data.detalle.push(det);
						}
						if(data.detalle.length==0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay ning&uacute;n comprobante para los filtros seleccionados!',type: 'error'});
						}
						for(var i=0,j=p.$w.find('[name=gridAnu] tbody .item').length; i<j; i++){
							if(data.comprobantes_anulados==null) data.comprobantes_anulados = [];
							data.comprobantes_anulados.push(p.$w.find('[name=gridAnu] tbody .item').eq(i).data('data'));
						}
						tot_deb = 0;
						tot_hab = 0;
						for(var i=0,j=p.$w.find('[name=gridCont] tbody .item').length; i<j; i++){
							var tmp = p.$w.find('[name=gridCont] tbody .item').eq(i).data('data');
							if(tmp!=null){
								if(tmp.tipo=='D')
									tot_deb += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								if(tmp.tipo=='H')
									tot_hab += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								data.cont_patrimonial.push({
									cuenta: {
										_id: tmp._id.$id,
										cod: tmp.cod,
										descr: tmp.descr
									},
									tipo: tmp.tipo,
									moneda: 'S',
									monto: parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'))
								});
							}
						}
						
						data.efectivos.push({
							moneda: 'S',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(0)').data('total'))
						});
						var tmp = {
							moneda: 'D',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'))
						};
						if(tmp.monto!=0) tmp.tc = parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total_sol'))/parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'));
						data.efectivos.push(tmp);
						for(var i=0,j=p.$w.find('[name=gridPag] tbody .vouc').length; i<j; i++){
							if(data.vouchers==null) data.vouchers = [];
							data.vouchers.push(p.$w.find('[name=gridPag] tbody .vouc').eq(i).data('data'));
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/comp/save_rein',data,function(rein){
							K.clearNoti();
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "in/comp/reci_ing2?_id="+rein._id.$id
							});
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue registrado con &eacute;xito!'});
							hoRein.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						hoReci.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				//p.$w.find('[name=div_ini]').hide();
				p.$w.find('[name=tipo]').change(function(){
					p.fill();
				});
				p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd());
				p.$w.find('[name=fecfin]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=fec]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=btnOrga]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
						p.$w.find('[name=fec]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=respo]').html(mgEnti.formatName(K.session.enti));
				new K.grid({
					$el: p.$w.find('[name=gridComp]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Comprobante','Concepto','Importe'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridAnu]'),
					search: false,
					pagination: false,
					cols: ['Comprobante','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCod]'),
					search: false,
					pagination: false,
					cols: ['Debe','Haber','Sector','Pliego','Programa','SubPrograma','Actividad','Componente','Fuente de Financiamiento'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['Pagos','Cuenta Bancaria','Monto','Monto en Soles','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber'],
					onlyHtml: true
				});
				//p.$w.find('[name^=grid] table').append('<tbody>');
				$.post('cj/rein/get_cod',function(data){
					console.log(data);
					p.cod = data.cod;
					p.fuen = data.fuen;
					p.$w.find('[name=num]').val(data.cod);
					if(K.session.enti.roles!=null){
						if(K.session.enti.roles.trabajador!=null){
							p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb)
								.data('data',K.session.enti.roles.trabajador.programa);
							p.$w.find('[name=btnOrga]').button('option','text',false);
							p.fill();
							
							
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowGen__: function(p){
		if(p==null) p = {};
		$.extend(p,{
			fill: function(){
				if(p.$w.find('[name=fec]').val()>p.$w.find('[name=fecfin]').val()){
					p.$w.find('[name=fecfin]').datepicker('setValue',p.$w.find('[name=fec]').val());
				}
				var orga = p.$w.find('[name=orga]').data('data');
				/*if(p.$w.find('[name=fec]').datepicker('getDate')==null){
					p.$w.find('[name=fec]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
				}*/
				if(orga==null){
					p.$w.find('[name=btnOrga]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
				}
				K.block({$element: p.$w});
				$.post('cj/rein/get_rec_ho',{
					modulo: 'LM',
					tipo: p.$w.find('[name=tipo] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: '51a50e614d4a13c409000012',//orga._id.$id
					actividad: '51e9958c4d4a13440a00000d',//orga.actividad._id.$id
					componente: '51e99c964d4a13c404000015'//orga.componente._id.$id
				},function(data){
					var tmp_ctas_pat = [];
					p.$w.find('[name=gridComp] tbody').empty();
					p.$w.find('[name=gridAnu] tbody').empty();
					p.$w.find('[name=gridCod] tbody').empty();
					p.$w.find('[name=gridPag] tbody').empty();
					p.$w.find('[name=gridCont] tbody').empty();
					if(data.comp==null){
						K.unblock({$element: p.$w});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
					}
					p.$w.find('[name=planilla]').val(data.planilla);
					p.comp = data.comp;
					p.prog = data.prog;
					var $row = $('<tr class="item">');
					$row.append('<td>8501</td>');
					$row.append('<td>8201</td>');
					$row.append('<td>034</td>');
					$row.append('<td>001</td>');
					$row.append('<td>040</td>');
					$row.append('<td>0123</td>');
					$row.append('<td>30205</td>');
					$row.append('<td>1540</td>');
					$row.append('<td><select name="fuente"></td>');
					for(var k=0,l=p.fuen.length; k<l; k++){
						$row.find('select').append('<option value="'+p.fuen[k]._id.$id+'">'+p.fuen[k].cod+'</option>');
						$row.find('select option:last').data('data',p.fuen[k]);
					}
					p.$w.find('[name=gridCod] tbody').append($row);
					// Efectivo en pagos
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0,'D')+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					
					var tot_sol = 0,
					tot_dol = 0,
					tot_dol_sol = 0,
					total = 0;
					for(var i=0,j=data.comp.length; i<j; i++){
						var result = data.comp[i];
						if(result.estado=='X'){
							var $row = $('<tr class="item">');
							$row.append('<td>'+result.tipo+' '+result.serie+' '+result.num+'</td>');
							if(result.cliente._id!=null)
								$row.append('<td>'+mgEnti.formatName(result.cliente)+'</td>');
							else
								$row.append('<td>'+result.cliente+'</td>');
							$row.data('data',{
								_id: result._id.$id,
								tipo: result.tipo,
								serie: result.serie,
								num: result.num
							});
							p.$w.find('[name=gridAnu] tbody').append($row);
						}else{
							if(result.hospitalizacion!=null){
								result.total = parseFloat(result.total);
								var $row = $('<tr class="item">');
								$row.append('<td>'+result.cuenta.cod+' - '+result.cuenta.descr+'</td>');
								$row.append('<td>Recibo '+result.num+'</td>');
								$row.append('<td>'+'Hospitalizacion - '+mgEnti.formatName(result.cliente)+'</td>');
								$row.append('<td>'+ciHelper.formatMon(result.total,result.moneda)+'</td>');
								$row.data('data',{
									cuenta: {
										_id: result.cuenta._id.$id,
										cod: result.cuenta.cod,
										descr: result.cuenta.descr
									},
									comprobante: {
										_id: result._id.$id,
										tipo: result.tipo,
										serie: result.serie,
										num: result.num
									},
									concepto: 'Hospitalizacion - '+mgEnti.formatName(result.cliente)
								}).data('total',parseFloat(result.total));
								p.$w.find('[name=gridComp] tbody').append($row);
								tot_sol += parseFloat(result.total);
								var tmp_ctas_pat_i = -1;
								for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
									if(tmp_ctas_pat[tmp_i].cod==result.cuenta.cod.substr(0,9)){
										tmp_ctas_pat_i = tmp_i;
										tmp_i = tmp_j;
									}
								}
								if(tmp_ctas_pat_i==-1){
									tmp_ctas_pat.push({
										cod: result.cuenta.cod.substr(0,9),
										cuenta: result.cuenta,
										total: parseFloat(result.total)
									});
								}else{
									tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(result.total);
								}
								total += parseFloat(result.total);
							}else{
								result.total = parseFloat(result.total);
								for(var ii=0,jj=result.items.length; ii<jj; ii++){
									var $row = $('<tr class="item">'),
									item = result.items[ii];
									$row.append('<td>'+item.cuenta.cod+' - '+item.cuenta.descr+'</td>');
									$row.append('<td>Recibo '+result.num+'</td>');
									$row.append('<td>'+item.servicio.nomb+'</td>');
									$row.append('<td>'+ciHelper.formatMon(item.costo,result.moneda)+'</td>');
									$row.data('data',{
										cuenta: {
											_id: item.cuenta._id.$id,
											cod: item.cuenta.cod,
											descr: item.cuenta.descr
										},
										comprobante: {
											_id: result._id.$id,
											tipo: result.tipo,
											serie: result.serie,
											num: result.num
										},
										concepto: item.servicio.nomb
									}).data('total',item.costo);
									p.$w.find('[name=gridComp] tbody').append($row);
									tot_sol += parseFloat(item.costo);
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==item.cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: item.cuenta.cod.substr(0,9),
											cuenta: item.cuenta,
											total: parseFloat(item.costo)
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(item.costo);
									}
								}
								total += parseFloat(result.total);
							}
						}
					}
					// GENERACION AUTOMATICA DE CONTABILIDAD PATRIMONIAL
					var tmp_to = 0;
					for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
						var $row = $('<tr class="item">');
						$row.append('<td>'+tmp_ctas_pat[tmp_i].cod+'</td>');
						$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(tmp_ctas_pat[tmp_i].total)+'</td>');
						var tmp_cta_a = tmp_ctas_pat[tmp_i].cuenta;
						tmp_cta_a.tipo = 'D';
						$row.data('data',tmp_cta_a).data('total',tmp_ctas_pat[tmp_i].total).attr('name',tmp_ctas_pat[tmp_i].cuenta._id.$id);
						p.$w.find('[name=gridCont] tbody').append($row);
						tmp_to += parseFloat(tmp_ctas_pat[tmp_i].total);
					}
					tmp_to = parseFloat(K.round(tmp_to,2));
					var $row = $('<tr class="item">');
					$row.append('<td>1101.0101</td>');
					$row.append('<td>'+ciHelper.formatMon(tmp_to)+'</td>');
					$row.append('<td>');
					$row.data('data',{
						_id: '51a6473a4d4a13540a000009',
						cod: '1101.0101',
						descr: 'Caja M/N',
						tipo: 'H'
					}).data('total',tmp_to).attr('name','51a6473a4d4a13540a000009');
					p.$w.find('[name=gridCont] tbody .item:eq(0)').before($row);
					// TOTALES
					var $row = $('<tr class="item result">');
					$row.append('<td>');
					$row.append('<td>');
					$row.append('<td>Parcial</td>');
					$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
					$row.data('total',total);
					p.$w.find('[name=gridComp] tbody').append($row);
					p.$w.find('[name=gridPag] .item').eq(0).data('total',tot_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(0)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(1).data('total',tot_dol).data('total_sol',tot_dol_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_dol,'D'));
					p.$w.find('[name=gridPag] .item').eq(1)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_dol_sol));
					/*p.calcHab();
					p.calcDeb();*/
					K.unblock({$element: p.$w});
				},'json');
			},
			calcDeb: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(9) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(9) [name=monto]').eq(i).val());
				}
				p.$w.find('.payment:eq(9) .result').remove();
				var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(9) .gridBody').append($row.children());
			},
			calcHab: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(11) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(11) .item').eq(i).data('total'));
				}
				p.$w.find('.payment:eq(11) .result').remove();
				var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(11) .gridBody').append($row.children());
			}
		});
		new K.Panel({
			title: 'Recibo de Ingresos',
			contentURL: 'ho/reci/generar',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'LM',
							cod: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							iniciales: p.$w.find('[name=iniciales]').val(),
							fec: p.$w.find('[name=fec]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							observ: p.$w.find('[name=observ]').val(),
							detalle: [],
							glosa: p.$w.find('[name=observ]').val(),
							cont_patrimonial: [],
							total: 0,
							efectivos: [],
							fuente: {
								_id: p.$w.find('[name=fuente] option:selected').data('data')._id.$id,
								cod: p.$w.find('[name=fuente] option:selected').data('data').cod,
								rubro: p.$w.find('[name=fuente] option:selected').data('data').rubro
							}
						},tot_deb=0,tot_hab=0,
						tmp = p.$w.find('[name=orga]').data('data');
						/*if(data.iniciales==''){
							p.$w.find('[name=iniciales]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar unas iniciales!',type: 'error'});
						}*/
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
						}
						if(tmp==null){
							p.$w.find('[name=btnOrga]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
						}
						
						for(var i=0,j=(p.$w.find('[name=gridComp] tbody .item').length-1); i<j; i++){
							var det = p.$w.find('[name=gridComp] tbody .item').eq(i).data('data');
							$.extend(det,{
								monto: p.$w.find('[name=gridComp] tbody .item').eq(i).data('total')
							});
							if(det.cuenta._id.$id!=null)
								det.cuenta._id = det.cuenta._id.$id;
							data.total += parseFloat(det.monto);
							data.detalle.push(det);
						}
						if(data.detalle.length==0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay ning&uacute;n comprobante para los filtros seleccionados!',type: 'error'});
						}
						for(var i=0,j=p.$w.find('[name=gridAnu] tbody .item').length; i<j; i++){
							if(data.comprobantes_anulados==null) data.comprobantes_anulados = [];
							data.comprobantes_anulados.push(p.$w.find('[name=gridAnu] tbody .item').eq(i).data('data'));
						}
						tot_deb = 0;
						tot_hab = 0;
						for(var i=0,j=p.$w.find('[name=gridCont] tbody .item').length; i<j; i++){
							var tmp = p.$w.find('[name=gridCont] tbody .item').eq(i).data('data');
							if(tmp!=null){
								if(tmp.tipo=='D')
									tot_deb += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								if(tmp.tipo=='H')
									tot_hab += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								data.cont_patrimonial.push({
									cuenta: {
										_id: tmp._id.$id,
										cod: tmp.cod,
										descr: tmp.descr
									},
									tipo: tmp.tipo,
									moneda: 'S',
									monto: parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'))
								});
							}
						}
						
						data.efectivos.push({
							moneda: 'S',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(0)').data('total'))
						});
						var tmp = {
							moneda: 'D',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'))
						};
						if(tmp.monto!=0) tmp.tc = parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total_sol'))/parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'));
						data.efectivos.push(tmp);
						for(var i=0,j=p.$w.find('[name=gridPag] tbody .vouc').length; i<j; i++){
							if(data.vouchers==null) data.vouchers = [];
							data.vouchers.push(p.$w.find('[name=gridPag] tbody .vouc').eq(i).data('data'));
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/comp/save_rein',data,function(rein){
							K.clearNoti();
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "in/comp/reci_ing2?_id="+rein._id.$id
							});
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue registrado con &eacute;xito!'});
							hoRein.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						hoReci.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				//p.$w.find('[name=div_ini]').hide();
				p.$w.find('[name=tipo]').change(function(){
					p.fill();
				});
				p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd());
				p.$w.find('[name=fecfin]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=fec]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=btnOrga]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
						p.$w.find('[name=fec]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=respo]').html(mgEnti.formatName(K.session.enti));
				new K.grid({
					$el: p.$w.find('[name=gridComp]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Comprobante','Concepto','Importe'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridAnu]'),
					search: false,
					pagination: false,
					cols: ['Comprobante','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCod]'),
					search: false,
					pagination: false,
					cols: ['Debe','Haber','Sector','Pliego','Programa','SubPrograma','Actividad','Componente','Fuente de Financiamiento'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['Pagos','Cuenta Bancaria','Monto','Monto en Soles','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber'],
					onlyHtml: true
				});
				//p.$w.find('[name^=grid] table').append('<tbody>');
				$.post('cj/rein/get_cod',function(data){
					p.cod = data.cod;
					p.fuen = data.fuen;
					p.$w.find('[name=num]').val(data.cod);
					if(K.session.enti.roles!=null){
						if(K.session.enti.roles.trabajador!=null){
							p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb)
								.data('data',K.session.enti.roles.trabajador.programa);
							p.$w.find('[name=btnOrga]').button('option','text',false);
							p.fill();
							
							
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	btnGenTard: function(p){
		if(p==null) p = {};
		$.extend(p,{
			fill: function(){
				if(p.$w.find('[name=fec]').val()>p.$w.find('[name=fecfin]').val()){
					p.$w.find('[name=fecfin]').datepicker('setValue',p.$w.find('[name=fec]').val());
				}
				var orga = p.$w.find('[name=orga]').data('data');
				/*if(p.$w.find('[name=fec]').datepicker('getDate')==null){
					p.$w.find('[name=fec]').focus();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha!',type: 'error'});
				}*/
				if(orga==null){
					p.$w.find('[name=btnOrga]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
				}
				K.block({$element: p.$w});
				$.post('cj/rein/get_rec_ho',{
					modulo: 'TD',
					tipo: p.$w.find('[name=tipo] option:selected').val(),
					fec: p.$w.find('[name=fec]').val(),
					fecfin: p.$w.find('[name=fecfin]').val(),
					orga: '51a50e614d4a13c409000012',//orga._id.$id
					actividad: '51e9958c4d4a13440a00000d',//orga.actividad._id.$id
					componente: '51e99c964d4a13c404000015'//orga.componente._id.$id
				},function(data){
					var tmp_ctas_pat = [];
					p.$w.find('[name=gridComp] tbody').empty();
					p.$w.find('[name=gridAnu] tbody').empty();
					p.$w.find('[name=gridCod] tbody').empty();
					p.$w.find('[name=gridPag] tbody').empty();
					p.$w.find('[name=gridCont] tbody').empty();
					if(data.comp==null){
						K.unblock({$element: p.$w});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
					}
					p.$w.find('[name=planilla]').val(data.planilla);
					p.comp = data.comp;
					p.prog = data.prog;
					var $row = $('<tr class="item">');
					$row.append('<td>8501</td>');
					$row.append('<td>8201</td>');
					$row.append('<td>034</td>');
					$row.append('<td>001</td>');
					$row.append('<td>040</td>');
					$row.append('<td>0123</td>');
					$row.append('<td>30205</td>');
					$row.append('<td>1540</td>');
					$row.append('<td><select name="fuente"></td>');
					for(var k=0,l=p.fuen.length; k<l; k++){
						$row.find('select').append('<option value="'+p.fuen[k]._id.$id+'">'+p.fuen[k].cod+'</option>');
						$row.find('select option:last').data('data',p.fuen[k]);
					}
					p.$w.find('[name=gridCod] tbody').append($row);
					// Efectivo en pagos
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo Soles</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					var $row = $('<tr class="item">');
					$row.append('<td>Efectivo D&oacute;lares</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(0,'D')+'</td>');
					$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
					$row.append('<td>');
					$row.data('total',0);
					p.$w.find('[name=gridPag] tbody').append($row);
					
					var tot_sol = 0,
					tot_dol = 0,
					tot_dol_sol = 0,
					total = 0;
					for(var i=0,j=data.comp.length; i<j; i++){
						var result = data.comp[i];
						if(result.estado=='X'){
							var $row = $('<tr class="item">');
							$row.append('<td>'+result.tipo+' '+result.serie+' '+result.num+'</td>');
							if(result.cliente._id!=null)
								$row.append('<td>'+mgEnti.formatName(result.cliente)+'</td>');
							else
								$row.append('<td>'+result.cliente+'</td>');
							$row.data('data',{
								_id: result._id.$id,
								tipo: result.tipo,
								serie: result.serie,
								num: result.num
							});
							p.$w.find('[name=gridAnu] tbody').append($row);
						}else{
							if(result.hospitalizacion!=null){
								result.total = parseFloat(result.total);
								var $row = $('<tr class="item">');
								$row.append('<td>'+result.cuenta.cod+' - '+result.cuenta.descr+'</td>');
								$row.append('<td>Recibo '+result.num+'</td>');
								$row.append('<td>'+'Hospitalizacion - '+mgEnti.formatName(result.cliente)+'</td>');
								$row.append('<td>'+ciHelper.formatMon(result.total,result.moneda)+'</td>');
								$row.data('data',{
									cuenta: {
										_id: result.cuenta._id.$id,
										cod: result.cuenta.cod,
										descr: result.cuenta.descr
									},
									comprobante: {
										_id: result._id.$id,
										tipo: result.tipo,
										serie: result.serie,
										num: result.num
									},
									concepto: 'Hospitalizacion - '+mgEnti.formatName(result.cliente)
								}).data('total',parseFloat(result.total));
								p.$w.find('[name=gridComp] tbody').append($row);
								tot_sol += parseFloat(result.total);
								var tmp_ctas_pat_i = -1;
								for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
									if(tmp_ctas_pat[tmp_i].cod==result.cuenta.cod.substr(0,9)){
										tmp_ctas_pat_i = tmp_i;
										tmp_i = tmp_j;
									}
								}
								if(tmp_ctas_pat_i==-1){
									tmp_ctas_pat.push({
										cod: result.cuenta.cod.substr(0,9),
										cuenta: result.cuenta,
										total: parseFloat(result.total)
									});
								}else{
									tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(result.total);
								}
								total += parseFloat(result.total);
							}else{
								result.total = parseFloat(result.total);
								for(var ii=0,jj=result.items.length; ii<jj; ii++){
									var $row = $('<tr class="item">'),
									item = result.items[ii];
									$row.append('<td>'+item.cuenta.cod+' - '+item.cuenta.descr+'</td>');
									$row.append('<td>Recibo '+result.num+'</td>');
									$row.append('<td>'+item.servicio.nomb+'</td>');
									$row.append('<td>'+ciHelper.formatMon(item.costo,result.moneda)+'</td>');
									$row.data('data',{
										cuenta: {
											_id: item.cuenta._id.$id,
											cod: item.cuenta.cod,
											descr: item.cuenta.descr
										},
										comprobante: {
											_id: result._id.$id,
											tipo: result.tipo,
											serie: result.serie,
											num: result.num
										},
										concepto: item.servicio.nomb
									}).data('total',item.costo);
									p.$w.find('[name=gridComp] tbody').append($row);
									tot_sol += parseFloat(item.costo);
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=1,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==item.cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: item.cuenta.cod.substr(0,9),
											cuenta: item.cuenta,
											total: parseFloat(item.costo)
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(item.costo);
									}
								}
								total += parseFloat(result.total);
							}
						}
					}
					// GENERACION AUTOMATICA DE CONTABILIDAD PATRIMONIAL
					var tmp_to = 0;
					for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
						var $row = $('<tr class="item">');
						$row.append('<td>'+tmp_ctas_pat[tmp_i].cod+'</td>');
						$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(tmp_ctas_pat[tmp_i].total)+'</td>');
						var tmp_cta_a = tmp_ctas_pat[tmp_i].cuenta;
						tmp_cta_a.tipo = 'D';
						$row.data('data',tmp_cta_a).data('total',tmp_ctas_pat[tmp_i].total).attr('name',tmp_ctas_pat[tmp_i].cuenta._id.$id);
						p.$w.find('[name=gridCont] tbody').append($row);
						tmp_to += parseFloat(tmp_ctas_pat[tmp_i].total);
					}
					tmp_to = parseFloat(K.round(tmp_to,2));
					var $row = $('<tr class="item">');
					$row.append('<td>1101.0101</td>');
					$row.append('<td>'+ciHelper.formatMon(tmp_to)+'</td>');
					$row.append('<td>');
					$row.data('data',{
						_id: '51a6473a4d4a13540a000009',
						cod: '1101.0101',
						descr: 'Caja M/N',
						tipo: 'H'
					}).data('total',tmp_to).attr('name','51a6473a4d4a13540a000009');
					p.$w.find('[name=gridCont] tbody .item:eq(0)').before($row);
					// TOTALES
					var $row = $('<tr class="item result">');
					$row.append('<td>');
					$row.append('<td>');
					$row.append('<td>Parcial</td>');
					$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
					$row.data('total',total);
					p.$w.find('[name=gridComp] tbody').append($row);
					p.$w.find('[name=gridPag] .item').eq(0).data('total',tot_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(0)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_sol));
					p.$w.find('[name=gridPag] .item').eq(1).data('total',tot_dol).data('total_sol',tot_dol_sol)
					.find('td:eq(2)').html(ciHelper.formatMon(tot_dol,'D'));
					p.$w.find('[name=gridPag] .item').eq(1)
					.find('td:eq(3)').html(ciHelper.formatMon(tot_dol_sol));
					/*p.calcHab();
					p.calcDeb();*/
					K.unblock({$element: p.$w});
				},'json');
			},
			calcDeb: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(9) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(9) [name=monto]').eq(i).val());
				}
				p.$w.find('.payment:eq(9) .result').remove();
				var $row = p.$w.find('.payment:eq(9) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(9) .gridBody').append($row.children());
			},
			calcHab: function(){
				var total = 0;
				for(var i=0,j=p.$w.find('.payment:eq(11) .item').length; i<j; i++){
					total += parseFloat(p.$w.find('.payment:eq(11) .item').eq(i).data('total'));
				}
				p.$w.find('.payment:eq(11) .result').remove();
				var $row = p.$w.find('.payment:eq(11) .gridReference').clone();
				$row.find('li:eq(0)').remove();
				$row.find('li:eq(0)').css('max-width','350px').css('min-width','350px')
				.html('Total').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="result">');
				$row.find('.item').data('total',total);
				p.$w.find('.payment:eq(11) .gridBody').append($row.children());
			}
		});
		new K.Panel({
			title: 'Recibo de Ingresos',
			contentURL: 'ho/reci/generar',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							modulo: 'TD',
							cod: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							iniciales: p.$w.find('[name=iniciales]').val(),
							fec: p.$w.find('[name=fec]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							observ: p.$w.find('[name=observ]').val(),
							detalle: [],
							glosa: p.$w.find('[name=observ]').val(),
							cont_patrimonial: [],
							total: 0,
							efectivos: [],
							fuente: {
								_id: p.$w.find('[name=fuente] option:selected').data('data')._id.$id,
								cod: p.$w.find('[name=fuente] option:selected').data('data').cod,
								rubro: p.$w.find('[name=fuente] option:selected').data('data').rubro
							}
						},tot_deb=0,tot_hab=0,
						tmp = p.$w.find('[name=orga]').data('data');
						/*if(data.iniciales==''){
							p.$w.find('[name=iniciales]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar unas iniciales!',type: 'error'});
						}*/
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
						}
						if(tmp==null){
							p.$w.find('[name=btnOrga]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
						}
						
						for(var i=0,j=(p.$w.find('[name=gridComp] tbody .item').length-1); i<j; i++){
							var det = p.$w.find('[name=gridComp] tbody .item').eq(i).data('data');
							$.extend(det,{
								monto: p.$w.find('[name=gridComp] tbody .item').eq(i).data('total')
							});
							if(det.cuenta._id.$id!=null)
								det.cuenta._id = det.cuenta._id.$id;
							data.total += parseFloat(det.monto);
							data.detalle.push(det);
						}
						if(data.detalle.length==0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay ning&uacute;n comprobante para los filtros seleccionados!',type: 'error'});
						}
						for(var i=0,j=p.$w.find('[name=gridAnu] tbody .item').length; i<j; i++){
							if(data.comprobantes_anulados==null) data.comprobantes_anulados = [];
							data.comprobantes_anulados.push(p.$w.find('[name=gridAnu] tbody .item').eq(i).data('data'));
						}
						tot_deb = 0;
						tot_hab = 0;
						for(var i=0,j=p.$w.find('[name=gridCont] tbody .item').length; i<j; i++){
							var tmp = p.$w.find('[name=gridCont] tbody .item').eq(i).data('data');
							if(tmp!=null){
								if(tmp.tipo=='D')
									tot_deb += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								if(tmp.tipo=='H')
									tot_hab += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
								data.cont_patrimonial.push({
									cuenta: {
										_id: tmp._id.$id,
										cod: tmp.cod,
										descr: tmp.descr
									},
									tipo: tmp.tipo,
									moneda: 'S',
									monto: parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'))
								});
							}
						}
						
						data.efectivos.push({
							moneda: 'S',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(0)').data('total'))
						});
						var tmp = {
							moneda: 'D',
							monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'))
						};
						if(tmp.monto!=0) tmp.tc = parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total_sol'))/parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'));
						data.efectivos.push(tmp);
						for(var i=0,j=p.$w.find('[name=gridPag] tbody .vouc').length; i<j; i++){
							if(data.vouchers==null) data.vouchers = [];
							data.vouchers.push(p.$w.find('[name=gridPag] tbody .vouc').eq(i).data('data'));
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/comp/save_rein',data,function(rein){
							K.clearNoti();
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "in/comp/reci_ing2?_id="+rein._id.$id
							});
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue registrado con &eacute;xito!'});
							hoRein.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						hoReci.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				//p.$w.find('[name=div_ini]').hide();
				p.$w.find('[name=tipo]').change(function(){
					p.fill();
				});
				p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd());
				p.$w.find('[name=fecfin]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=fec]').datepicker({format: 'yyyy-mm-dd'})
					.on('changeDate', function(ev){
						p.fill();
					});
				p.$w.find('[name=btnOrga]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
						p.$w.find('[name=fec]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=respo]').html(mgEnti.formatName(K.session.enti));
				new K.grid({
					$el: p.$w.find('[name=gridComp]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Comprobante','Concepto','Importe'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridAnu]'),
					search: false,
					pagination: false,
					cols: ['Comprobante','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCod]'),
					search: false,
					pagination: false,
					cols: ['Debe','Haber','Sector','Pliego','Programa','SubPrograma','Actividad','Componente','Fuente de Financiamiento'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridPag]'),
					search: false,
					pagination: false,
					cols: ['Pagos','Cuenta Bancaria','Monto','Monto en Soles','Cliente'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridCont]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber'],
					onlyHtml: true
				});
				//p.$w.find('[name^=grid] table').append('<tbody>');
				$.post('cj/rein/get_cod',function(data){
					p.cod = data.cod;
					p.fuen = data.fuen;
					p.$w.find('[name=num]').val(data.cod);
					if(K.session.enti.roles!=null){
						if(K.session.enti.roles.trabajador!=null){
							p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb)
								.data('data',K.session.enti.roles.trabajador.programa);
							p.$w.find('[name=btnOrga]').button('option','text',false);
							p.fill();
							
							
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','cj/comp','mg/serv','mg/orga','ho/tara','ho/targ','ho/rein','ha/reci','ha/rein'],
	function(mgEnti,ctPcon,cjComp,mgServ,mgOrga,hoTara,hoTarg,hoRein,haRein,haReci){
		return hoReci;
	}
);