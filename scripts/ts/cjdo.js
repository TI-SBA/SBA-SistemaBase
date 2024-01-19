tsCjdo = {
			states: {
		A: {
			descr: "Aprobado",
			color: "green",
			label: '<span class="label label-success">Aprobado</span>'
		},
		C: {
			descr: "Anulado",
			color: "#FF0000",
			label: '<span class="label label-danger">Anulado</span>'
		},
		P:{
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-default">Pendiente</span>'
		}
	},
		dbRel: function(item){
		return {
			_id: item._id.$id,
			beneficiario: item.beneficiario,
			fecdoc: item.fecdoc,
			tidoc: item.tidoc,
			num: item.num,
			prog: item.prog,
			ofic: item.ofic,
			conce: item.conce,
			mont: item.mont,
			papre: item.papre,
			sesion: item.sesion,
			
		};
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsCjdo',
			titleBar: {
				title: 'Caja Chica'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Beneficiario','Caja Asignada','Programa','Oficina','Partida','Monto','Fecha de Modificacion'],
					data: 'ts/cjdo/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsCjdo.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+tsCjdo.states[data.estado].label+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.beneficiario)+'</td>');
						$row.append('<td>'+data.sesion.caja.nomb+'</td>');
						$row.append('<td>'+data.programa.nomb+'</td>');
						$row.append('<td>'+data.oficina.nomb+'</td>');
						$row.append('<td>'+data.partida.cod+'</td>');
						$row.append('<td>'+data.mont+'</td>');
						
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data', data).data('estado',data.estado).contextMenu("conMenTsCjdo", {
							onShowMenu: function($row, menu) {
								//NO PUEDES ELIMINAR UN DOCUMENTO PENDIENTE
								if($row.data('data').estado == 'P'){
									$('#conMenTsCjdo_anul',menu).remove();
								}
								//NO PUEDES EDITAR NI ELIMINAR UN DOCUMENTO APROBADO
								if($row.data('data').estado == 'A'){
									$('#conMenTsCjdo_edi,#conMenTsCjdo_eli,#conMenTsCjdo_apro',menu).remove();
								}
								//NO PUEDES EDITAR NI APROBAR UN DOCUMENTO CANCELADO
								if($row.data('data').estado == 'C'){
									$('#conMenTsCjdo_anul,#conMenTsCjdo_apro,#conMenTsCjdo_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenTsCjdo_edi': function(t) {
									tsCjdo.windowEdit({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenTsCjdo_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Caja Chica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ts/cjdo/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Caja Chica Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tsCjdo.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Caja Chica');
								},
								'conMenTsCjdo_apro': function(t) {
									ciHelper.confirm('&#191;Esta <b>Aprobado</b> el Documento Nro: <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										//$.post('ts/cjdo/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
											//K.clearNoti();
											//K.notification({title: 'Documento esta Aprobado',text: 'La Aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										//	tsCjdo.init();
										$.post('ts/cjdo/estado',{_id: K.tmp.data('id'),estado: 'A'},function(rpta){
												K.clearNoti();
												K.notification({title: 'Mensaje del Sistema!', type: rpta.status,text: rpta.message});
												K.msg({title: 'Mensaje del Sistema!', type: rpta.state,text: rpta.message});
												tsCjdo.init();
										},'json');
										//});
									},function(){
										$.noop();
									},'Aprobaci&oacute;n de Documentos');
								},	
								'conMenTsCjdo_anul': function(t) {
									ciHelper.confirm('&#191;Esta <b>Anulado</b> el Documento Nro: <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										//$.post('ts/cjdo/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
										//K.clearNoti();
										//K.notification({title: 'Documento esta Aprobado',text: 'La Aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										//tsCjdo.init();
										$.post('ts/cjdo/estado',{_id: K.tmp.data('id'),estado: 'C'},function(rpta){
											K.clearNoti();
											K.notification({title: 'Mensaje del Sistema!', type: rpta.status,text: rpta.message});
											K.msg({title: 'Mensaje del Sistema!', type: rpta.state,text: rpta.message});
											tsCjdo.init();
										},'json');
									//});
									},function(){
										$.noop();
									},'Anulaci&oacute;n de Documentos');
								},	
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
			id: 'windowNewCajaChica',
			title: 'Nueva Caja Chica',
			contentURL: 'ts/cjdo/edit',
			width: 700,
			height: 500,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							beneficiario: p.$w.find('[name=beneficiario] [name = mini_enti]').data('data'),
							fecdoc: p.$w.find('[name=fecdoc]').val(),
							tidoc: p.$w.find('[name=tidoc]').val(),
							num: p.$w.find('[name=num]').val(),
							programa: mgProg.dbRel(p.$w.find('[name=prog]').data('data')),
							oficina:  mgOfic.dbRel(p.$w.find('[name=ofic]').data('data')),
							conce: p.$w.find('[name=conce]').val(),
							mont: p.$w.find('[name=mont]').val(),
							partida: prClas.dbRel(p.$w.find('[name=papre]').data('data')),
							//sesion: tsCjse.dbRel(p.$w.find('[name=sesion]').data('data')),
							sesion: {
								_id: tsCjse.dbRel(p.$w.find('[name=sesion]').data('data'))._id,
								caja: {
									_id: tsCjse.dbRel(p.$w.find('[name=sesion]').data('data')).caja._id.$id,
									nomb: tsCjse.dbRel(p.$w.find('[name=sesion]').data('data')).caja.nomb,
								},
							}

						};
						if(data.beneficiario==null){
							p.$w.find('[name=beneficiario] [name=btnSel]').click();
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe seleccionar una Entidad',
									type: 'error'
								});
							}else data.beneficiario = mgEnti.dbRel(data.beneficiario);

						
						/*
							if(data.titu==''){
								p.$w.find('[name=titu]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo2!',type: 'error'});
							}
							if(data.cant==''){
								p.$w.find('[name=cant]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo3!',type: 'error'});
							}
							if(data.nro==''){
								p.$w.find('[name=nro]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo4!',type: 'error'});
							}
													
							if(data.remi==''){
								p.$w.find('[name=remi]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo5!',type: 'error'});
							}

							if(data.dire==''){
								p.$w.find('[name=dire]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!6',type: 'error'});
							}
							if(data.tise==''){
								p.$w.find('[name=tise]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!7',type: 'error'});
							}
							if(data.tipo_==''){
								p.$w.find('[name=tipo_]').focus();
								return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!8',type: 'error'});
							}
						
						*/
						

						//K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/cjdo/save",data,function(result){
							K.clearNoti();
							K.msg({title: 'Mensaje del Sistema!', type: result.state,text: result.message});
							tsCjdo.init();
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
				p.$w = $('#windowNewCajaChica');
				p.$w.find("[name=fecdoc]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			fecDate: '-3d'
				});	
				
				//BENEFICIARIO
				p.$w.find('[name=beneficiario] .panel-title').html('Datos del Beneficiario');
				p.$w.find('[name=beneficiario] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=beneficiario] [name=mini_enti]'),data);
						},bootstrap: true});
				});
				p.$w.find('[name=beneficiario] [name=btnAct]').click(function(){
					if(p.$w.find('[name=beneficiario] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe Elegir una Cantidad',
							type: 'Error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=beneficiario] [name=mini_enti]'),data);
						},id: p.$w.find('[name=beneficiario] [name=mini_enti]').data('data')._id.$id});
					}
				});

				//}else data.beneficiario = mgEnti.dbRel(data.beneficiario);
				p.$w.find('[name=btnProg]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=prog]').html(data.nomb).data('data',data);
//						p.$w.find('[name=id_prog]').html(data.id_prog.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnOfic]").click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofic]').html(data.nomb).data('data',data);
					//	p.$w.find('[name=id_ofic]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnParti]").click(function(){
					prClas.windowSelect({callback: function(data){
						p.$w.find('[name=papre]').html(data.cod).data('data',data);
					//	p.$w.find('[name=id_papre]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnSesion]").click(function(){
					tsCjse.windowSelect({callback: function(data){
						p.$w.find('[name=sesion]').html(data.caja.nomb).data('data',data);
					//	p.$w.find('[name=id_papre]').html(data._id.$id).data('data',data);
					},bootstrap: true,estado: 'A'});
				});

			}

		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowNewCajaChica',
			title: 'Editar Caja Chica: '+p.nro,
			contentURL: 'ts/cjdo/edit',
			width: 700,
			height: 500,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							beneficiario: p.$w.find('[name=beneficiario] [name = mini_enti]').data('data'),
							fecdoc: p.$w.find('[name=fecdoc]').val(),
							tidoc: p.$w.find('[name=tidoc]').val(),
							num: p.$w.find('[name=num]').val(),
							programa: mgProg.dbRel(p.$w.find('[name=prog]').data('data')),
							oficina:  mgOfic.dbRel(p.$w.find('[name=ofic]').data('data')),
							conce: p.$w.find('[name=conce]').val(),
							mont: p.$w.find('[name=mont]').val(),
							partida: prClas.dbRel(p.$w.find('[name=papre]').data('data')),
							sesion: tsCjse.dbRel(p.$w.find('[name=sesion]').data('data')),
							
						};

						
						/*
						if(data.titu==''){
							p.$w.find('[name=titu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cant==''){
							p.$w.find('[name=cant]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.nro==''){
							p.$w.find('[name=nro]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
												
						if(data.remi==''){
							p.$w.find('[name=remi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}

						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.tise==''){
							p.$w.find('[name=tise]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.tipo_==''){
							p.$w.find('[name=tipo_]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/cjdo/save",data,function(result){
							K.clearNoti();
							K.msg({title: 'Mensaje del Sistema!', type: result.state,text: result.message});
							tsCjdo.init();
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
				p.$w = $('#windowNewCajaChica');
				p.$w.find("[name=fecdoc]").datepicker({
		   				format: 'yyyy-mm-ts',
		    			fecDate: '-3d'
				});	
				
				p.$w.find('[name=btnProg]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=prog]').html(data.nomb).data('data',data);
						//p.$w.find('[name=id_prog]').html(data.id_prog.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnOfic]").click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofic]').html(data.nomb).data('data',data);
						//p.$w.find('[name=id_ofic]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnParti]").click(function(){
					prClas.windowSelect({callback: function(data){
						p.$w.find('[name=papre]').html(data.cod).data('data',data);
						//p.$w.find('[name=id_papre]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnSesion]").click(function(){
					tsCjse.windowSelect({callback: function(data){
						p.$w.find('[name=sesion]').html(data.caja.nomb).data('data',data);
						//p.$w.find('[name=id_papre]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				K.block();
			

				$.post('ts/cjdo/get',{_id: p.id},function(data){
							mgEnti.fillMini(p.$w.find('[name=beneficiario] [name = mini_enti]'),data.beneficiario);
							p.$w.find('[name=fecdoc]').val(data.fecdoc);
							p.$w.find('[name=tidoc]').val(data.tidoc);
							p.$w.find('[name=num]').val(data.num);
							p.$w.find('[name=prog]').text(data.prog);
							p.$w.find('[name=ofic]').text(data.ofic);
							p.$w.find('[name=conce]').val(data.conce);
							p.$w.find('[name=mont]').val(data.mont);
							p.$w.find('[name=papre]').text(data.papre);
							p.$w.find('[name=sesion]').text(data.sesion);
							

				K.unblock();
				},'json');
			}
		});
	},
};
define(
	['mg/enti','mg/ofic','mg/prog','pr/clas','ts/cjse'],
	function(mgEnti,mgOfic,mgProg,prClas,tsCjse){
		return tsCjdo;
	}
);

