mhPsic = {
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
			mode: 'mh',
			action: 'mhPsic',
			titleBar: {
				title: 'Ficha Psicologica'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Historia Clinica','Paciente','Referencia Familiar','Referencia Paciente','Historia Individual','Ultima Modificacion'],
					data: 'mh/psic/lista',
					params: {modulo: 'MH'},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>'+
					'&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
						'</select>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhPsic.windowNew();
						});
						$el.find('[name=modulo]').change(function(){
							var modulo = $el.find('[name=modulo] option:selected').val();
							$grid.reinit({params: {modulo: modulo}});
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						var historia = '----';
						if(data.paciente.his_cli!=null){
							historia = data.paciente.his_cli;
						}else{
							historia = data.clin;
						}
						$row.append('<td>'+historia+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente.paciente)+'</td>');
						$row.append('<td>'+data.refe+'</td>');
						$row.append('<td>'+data.repa+'</td>');
						$row.append('<td>'+data.his+'</td>');
						
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
									mhPsic.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFPsico_edi': function(t) {
				 					mhPsic.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
				 				},
								'conMenFPsico_evol': function(t) {
									mhHist.windowsEvolucion({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFPsico_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Psicologica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/psic/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Ficha Psicologica Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhPsic.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Psicologica');
								},
								'conMenFPsico_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Psicologica",
										url:"mh/psic/print?_id="+K.tmp.data('id')
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
		new K.Modal({
			id: 'windowNewFichaPsicologica',
			title: 'Nueva Ficha Psicologica',
			contentURL: 'mh/psic/edit',
			width: 900,
			height: 900,
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
								var paciente = p.$w.find('[name=paci]').data('data');
								var data = {
									paciente: {
										_id: paciente._id.$id,
										his_cli: paciente.his_cli,
										paciente: mgEnti.dbRel(paciente.paciente),
										sexo: paciente.sexo,
										edad: paciente.edad
									},
									clin:p.$w.find('[name=clini]').text(),
									modulo:p.$w.find('[name=modulo]').text(),
									edad:p.$w.find('[name=edad]').text(),
									moti:p.$w.find('[name=moti]').val(),
									refe:p.$w.find('[name=refe]').val(),
									repa:p.$w.find('[name=repa]').val(),
									his:p.$w.find('[name=his]').val(),
									orga:p.$w.find('[name=orga]').val(),
									inte:p.$w.find('[name=inte]').val(),
									perso:p.$w.find('[name=perso]').val(),
									conclu:p.$w.find('[name=conclu]').val(),
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/psic/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psicologica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									mhPsic.init();
									
								},'json');
							}
						}).submit();
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
				p.$w = $('#windowNewFichaPsicologica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=clini]').html(data.his_cli).data('data',data);
						p.$w.find('[name=edad]').html(data.edad).data('data',data);
						p.$w.find('[name=modulo]').html(data.modulo).data('data',data);
						
					},bootstrap: true});
				});
			}
		});
	},

	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditFichaPsicologica',
			title: 'Editar Ficha Psicologica: ' + p.paci,
			contentURL: 'mh/psic/edit',
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
								var paciente = p.$w.find('[name=paci]').data('data');
								var data = {
									//DATOS DEL psicENTE
									_id: p.id,
									paciente: {
										_id: paciente._id.$id,
										his_cli: paciente.his_cli,
										paciente: mgEnti.dbRel(paciente.paciente),
										sexo: paciente.sexo,
										edad: paciente.edad
									},
									clin:p.$w.find('[name=clini]').text(),
									edad:p.$w.find('[name=edad]').text(),
									modulo:p.$w.find('[name=modulo]').text(),
									moti:p.$w.find('[name=moti]').val(),
									refe:p.$w.find('[name=refe]').val(),
									repa:p.$w.find('[name=repa]').val(),
									his:p.$w.find('[name=his]').val(),
									orga:p.$w.find('[name=orga]').val(),
									inte:p.$w.find('[name=inte]').val(),
									perso:p.$w.find('[name=perso]').val(),
									conclu:p.$w.find('[name=conclu]').val()
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/psic/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psicologica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									mhPsic.init();
								},'json');	
							}
						}).submit();
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
				p.$w = $('#windowEditFichaPsicologica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=clini]').html(data.his_cli).data('data',data);
						p.$w.find('[name=edad]').html(data.edad).data('data',data);
						p.$w.find('[name=modulo]').html(data.modulo).data('data',data);
						
					},bootstrap: true});
				});
				K.block();
				$.post('mh/psic/get',{_id: p.id},function(data){
					p.$w.find('[name=paci]').html(mgEnti.formatName(data.paciente.paciente)).data('data',data.paciente);
					
				    p.$w.find('[name=clini]').text(data.clin);
				    p.$w.find('[name=paci]').text(data.paci);
					p.$w.find('[name=edad]').text(data.edad);
					p.$w.find('[name=modulo]').text(data.modulo);
					p.$w.find('[name=moti]').val(data.moti);
					p.$w.find('[name=refe]').val(data.refe);
					p.$w.find('[name=repa]').val(data.repa);
					p.$w.find('[name=his]').val(data.his);
					p.$w.find('[name=orga]').val(data.orga);
					p.$w.find('[name=inte]').val(data.inte);
					p.$w.find('[name=perso]').val(data.perso);
					p.$w.find('[name=conclu]').val(data.conclu);

					K.unblock();
				},'json');
			}
		});
	},
	// ---------------------------EVOLUCION------------------------------------
	windowEvolucion: function(p){

		new K.Modal({
			id: 'windowsEvolucion',
			title: 'Agregar Evolucion de Historial Clinico: ' +mgEnti.formatName(p.paciente),
			contentURL: 'mh/hist/edit',
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
								var paciente = p.$w.find('[name=paci]').data('data');
								var data = {
									_id: p.id,
									paciente: mgEnti.dbRel(paciente),
									clin: p.$w.find('[name=clini]').text(),
									evoluciones:[]
								};
								if(p.$w.find('[gridEvol] tbody tr').length>0){
									for(var i=0;i< p.$w.find('[name=gridEvol] tbody tr').length;i++){
										var $row = p.$w.find('[name=gridEvol] tbody tr').eq(i);
										var _evolucion = {
											fec:$row.find('[name=fec]').val(),
											evol:$row.find('[name=evol]').val(),
											tipo:$row.find('[name=tipo]').val(),
											user:$row.find('[name=user]').data('data'),
										}
										data.evoluciones.push(_evolucion);
									}
								}
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/hist/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Historial Clinico Agregado!"});
									K.closeWindow(p.$w.attr('id'));
									mhHist.ini();
								},'json');
							}
						}).submit();
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
			onClose: function(){ p=null;},
			onContentLoaded: function(){
				p.$w = $('#windowsEvolucion');
				p.$w.find('[name=paci]').html(mgEnti.formatName(p.paciente)).data('data',p.paciente);
				p.$w.find('[name=clini]').html(p.clini);
				K.block();
				new K.grid({
					$el: p.$w.find('name=gridEvol'),
					cols: ['Fecha','Historial','Tipo de Evolucion','Usuario','Eliminar'],
					stopLoad: true,
					pagination: false,
					search: false,
					store: false,
					toolbarHTML: '<button type = "button" name="btnAddEvol" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Historial</button >',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
							$row.find('[name=fec]').val(K.date()).datepicker();
							$row.append('<td><textarea type="text" class="form-control" name="evol"></textarea></td>');
								$row.append('<td><select class="form-control" name="tipo">'+
								'<option value="0">Social</option>'+
								'<option value="1">Medica</option>'+
								'</select></td>');
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
				$.post('mh/hist/get',{_id: p.id},function(data){
					p.$w.find('[name=clini]').text(data.clini);
					p.$w.find('[name=paci]').text(data.paci);
					p.$w.find('[name=hist]').text(data.his);
					if(data.evoluciones!=null){
						if(data.evoluciones.length>0){
							for(var i=0;i<data.evoluciones.length;i++){
								p.$w.find('[name=btnAddEvol]').click();
								var $row = p.$w.find('[name=gridEvol] tbody tr:last');
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
	['mg/enti','ct/pcon','mh/paci','mh/hist'],
	function(mgEnti,ctPcon,mhpaci,mhhist){
		return mhPsic;
	}
);