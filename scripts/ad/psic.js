adPsic = {
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
		return {
			_id: item._id.$id,
			paci: item.paci,
			moti: item.moti,
			refe: item.refe,
			repa: item.repa,
			his: item.his,
			orga: item.orga,
			inte: item.inte,
			perso: item.perso,
			conclu: item.conclu,
			fevo:item.fevo,
			evol:item.evol,
			edad:item.edad,
			clin:item.clin
		};
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adPsic',
			titleBar: {
				title: 'Ficha Psicologica'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Historia Clinica','Paciente','Ultima Modificacion'],
					data: 'ad/psic/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adPsic.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.his_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenFPsico", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFPsico_info': function(t) {
									adPsic.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFPsico_edi': function(t) {
									adPsic.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
								'conMenFPsico_evol': function(t) {
									adHist.windowsEvolucion({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFPsico_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Psicologica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/psic/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Ficha Psicologica Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adPsic.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Psicologica');
								},
								'conMenFPsico_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Psicologica",
										url:"ad/psic/print?_id="+K.tmp.data('id')
									});
								},
								'conMenListEd_edi':function(t){
									K.incomplete();
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
			title:'Nueva Ficha Psicologica',
			contentURL: 'ad/psic/edit',
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								K.sendingInfo();
								var data = {
									paciente: p.$w.find('[name=paciente] [name=mini_enti]').data('data'),
									his_cli:p.$w.find('[name=his_cli]').text(),
									moti:p.$w.find('[name=moti]').val(),
									refe:p.$w.find('[name=refe]').val(),
									repa:p.$w.find('[name=repa]').val(),
									his:p.$w.find('[name=his]').val(),
									orga:p.$w.find('[name=orga]').val(),
									inte:p.$w.find('[name=inte]').val(),
									perso:p.$w.find('[name=perso]').val(),
									conclu:p.$w.find('[name=conclu]').val(),
								};
								if(data.paciente==null){
									p.$w.find('[name=paciente] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un Paciente!',
										type: 'error'
									});
								}else data.paciente = mgEnti.dbRel(data.paciente);
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/psic/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psicologica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									adPsic.init();
									
								},'json');
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						adPsic.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=paciente] .panel-title').html('DATOS DEL PACIENTE');
				p.$w.find('[name=paciente] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						p.$w.find('[name=his_cli]').html(data.roles.paciente.hist_cli).data('data',data);
					},bootstrap: true,filter: [
					    {nomb: 'roles.paciente.centro',value: 'AD'},
					]});
				});
				p.$w.find('[name=paciente] [name=btnAct]').click(function(){
					if(p.$w.find('[name=paciente] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data);

						},id: p.$w.find('[name=paciente] [name=mini_enti]').data('data')._id.$id});
					}
				});

			}
		});
	},
	windowEdit: function(p){
		new K.Panel({ 
			title: 'Editar Ficha Psicologica: ' +mgEnti.formatName(p.paciente),
			contentURL: 'ad/psic/edit',
			store: false,
			width: 900,
			height: 900,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								K.sendingInfo();
								var data = {
									_id: p.id,
									paciente: mgEnti.dbRel(p.$w.find('[name=paciente] [name=mini_enti]').data('data')),
									his_cli:p.$w.find('[name=his_cli]').text(),
									moti:p.$w.find('[name=moti]').val(),
									refe:p.$w.find('[name=refe]').val(),
									repa:p.$w.find('[name=repa]').val(),
									his:p.$w.find('[name=his]').val(),
									orga:p.$w.find('[name=orga]').val(),
									inte:p.$w.find('[name=inte]').val(),
									perso:p.$w.find('[name=perso]').val(),
									conclu:p.$w.find('[name=conclu]').val(),
									
									evoluciones:[]
									
								};
									if ( p.$w.find('[name=gridEvol] tbody tr').length>0) {
										for(var i=0;i< p.$w.find('[name=gridEvol] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridEvol] tbody tr').eq(i);
											var _evolucion = {
												fec:$row.find('[name=fec]').val(),
												evol:$row.find('[name=evol]').val(),
												tipo:$row.find('[name=tipo]').val(),
												user:$row.find('[name=user]').data("data")
												
											}
											data.evoluciones.push(_evolucion);
										}
									}
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/psic/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psicologica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									adPsic.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						//K.closeWindow(p.$w.attr('id'));
						adPsic.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=paciente] .panel-title').html('DATOS DEL PACIENTE');
				p.$w.find('[name=paciente] [name=btnSel]').hide();
				p.$w.find('[name=paciente] [name=btnAct]').click(function(){
					if(p.$w.find('[name=paciente] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data);
						},id: p.$w.find('[name=paciente] [name=mini_enti]').data('data')._id.$id});
					}
				});
				
				K.block();
				new K.grid({
						$el: p.$w.find('[name=gridEvol]'),
						cols: ['Fecha','Evolucion','Tipo de Evolucion','Usuario','Eliminar'],
						stopLoad: true,
						pagination: false,
						search: false,
						store:false,
						toolbarHTML: '<button type = "button" name="btnAddEvol" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Historial</button >',
						onContentLoaded: function($el){
							$el.find('button').click(function(){
								var $row = $('<tr class="item">');
								$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
								$row.find('[name=fec]').val(K.date()).datepicker();
								$row.append('<td><textarea type="text" class="form-control" name="evol"></textarea></td>');
								$row.append('<td><input type="text" class="form-control" name="tipo" disabled="disabled" value="PSICOLOGICA" /></td>');
								$row.append('<td><input type="text" class="form-control" name="user" disabled="disabled" value="'+mgEnti.formatName(K.session.enti)+'" /></td>');
								$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=user]').data('data',mgEnti.dbRel(K.session.enti));
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								p.$w.find('[name=gridEvol] tbody').append($row);
							});
							p.$w.find("[name=fec]").datepicker({
			   				format: 'mm/dd/yyyy',
			    			startDate: '-3d'
							});
						}
					});
				K.block();
				$.post('ad/psic/get',{_id: p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente);
				  	p.$w.find('[name=his_cli]').text(data.his_cli);
				  	p.$w.find('[name=moti]').val(data.moti);
					p.$w.find('[name=refe]').val(data.refe);
					p.$w.find('[name=repa]').val(data.repa);
					p.$w.find('[name=his]').val(data.his);
					p.$w.find('[name=orga]').val(data.orga);
					p.$w.find('[name=inte]').val(data.inte);
					p.$w.find('[name=perso]').val(data.perso);
					p.$w.find('[name=conclu]').val(data.conclu);
					  if(data.evoluciones!=null){
							if(data.evoluciones.length>0){
								for(var i = 0;i<data.evoluciones.length;i++){
									p.$w.find('[name=btnAddEvol]').click();
									var $row = p.$w.find('[name=gridEvol] tbody tr:last');
									$row.find('[name=fec]').val(moment(data.evoluciones[i].fec.sec,'X').format('YYYY-MM-DD'));
									$row.find('[name=evol]').val(data.evoluciones[i].evol);
									$row.find('[name=user]').val(mgEnti.formatName(data.evoluciones[i].user)).data('data',mgEnti.dbRel(data.evoluciones[i].user));
									$row.find('[name=tipo]').val(data.evoluciones[i].tipo);
									
									
									}
								}
							}

						K.unblock();
					},'json');
				}
			});
		},
};
define(
	['ad/paci','mg/enti'],
	function(adpaci,mgEnti){
		return adPsic;
	}
);