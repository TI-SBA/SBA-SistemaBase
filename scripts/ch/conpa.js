chConpa = {
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
			paciente: item.paciente,
			fec_alta: item.fec_alta,
			fec_tras: item.fec_tras,
			pabellon: item.pabellon,
			sala: item.sala,
			saldo: item.saldo, // MONTO QUE PAGO POR LOS DIAS DE LA HOSPITALIZACION
			pendiente: item.pendiente, // LO QUE FALTA PARA PAGAR LA HOSPITALIZACION
			total: item.total, //TOTAL DE LA HOSPITALIZACION,
			categoria: item.categoria,
			modalidad: item.modalidad,
			tipo_hosp: item.tipo_hos


		};
	},
	init: function(){
		K.initMode({
			mode: 'ch',
			action: 'chConpa',
			titleBar: {
				title: 'Control de Pacientes'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Historia Clinica','Paciente','Pabellon','Sala','Categoria','Ultima Modificacion'],
					data: 'ch/conpa/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							chConpa.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.paciente.his_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente.paciente)+'</td>');
						$row.append('<td>'+data.pabellon+'</td>');
						$row.append('<td>'+data.salsa+'</td>');
						$row.append('<td>'+data.categoria+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenCon_Paciente", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenCon_Paciente_info': function(t) {
									chConpa.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenCon_Paciente_edi': function(t) {
									chConpa.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
								'conMenCon_Paciente_evol': function(t) {
									chHist.windowsEvolucion({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenCon_Paciente_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Control de Pacientes:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ch/conpa/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Control de Pacientes Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											chConpa.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Control de Pacientes');
								},
								'conMenCon_Paciente_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Control de Pacientes",
										url:"ch/conpa/print?_id="+K.tmp.data('id')
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
		//chPaci.windowSelect({callback: function(paciente){
			new K.Panel({
			title: 'Nueva Hospitalizacion de Pacientes',
			contentURL: 'ch/conpa/edit',
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								var paciente = p.$w.find('[name=paciente]').data('data');
								K.sendingInfo();
								var data = {
									paciente: {
										_id: paciente._id.$id,
										his_cli: paciente.his_cli,
										paciente: mgEnti.dbRel(p.$w.find('[name=paciente]').data('data').paciente),
										sexo: paciente.sexo
									},
									cie10:p.$W.find('[name=cie10]').text(),
									diag:p.$W.find('[name=diag]').text(),
									fec_inicio:p.$w.find('[name=fec_inicio]').val(),
									fec_alta:p.$w.find('[name=fec_alta]').val(),
									fec_tras:p.$w.find('[name=fec_tras]').val(),
									pabellon:p.$w.find('[name=pabellon]').text(),
									sala:p.$w.find('[name=sala]').val(),
									tipo_hosp:p.$w.find('[name=tipo_hosp]').text(),
									saldo:p.$w.find('[name=saldo]').val(),
									pendiente:p.$w.find('[name=pendiente]').val(),
									categoria:p.$w.find('[name=categoria]').text(),
									modalidad:p.$w.find('[name=modalidad]').text(),
									total:p.$w.find('[name=total]').val(),
									
									
								};
									
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ch/conpa/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Control de Pacientes Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									chConpa.init();
									
								},'json');
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						chConpa.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find("[name=btnDiag]").click(function(){
					hoHosp.windowSelect({callback: function(data){
						p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=clini]').html(data.his_cli).data('data',data);
						p.$w.find('[name=cie10]').html(data.cie10).data('data',data);
						p.$w.find('[name=diag]').html(data.diag).data('data',data);
						p.$w.find('[name=pabellon]').html(data.pabe).data('data',data);
						p.$w.find('[name=categoria]').html(data.categoria).data('data',data);
						p.$w.find('[name=tipo_hosp]').html(data.tipo_hosp).data('data',data);
						
						
					},bootstrap: true});
				});
				
					p.$w.find("[name=fec_inicio]").datepicker({
	   				format: 'mm/dd/yyyy',
	    			startDate: '-3d'
					});
					p.$w.find("[name=fec_tras]").datepicker({
	   				format: 'mm/dd/yyyy',
	    			startDate: '-3d'
					});
					p.$w.find("[name=fec_alta]").datepicker({
	   				format: 'mm/dd/yyyy',
	    			startDate: '-3d'
					});
				}
			});
		//}});
	},
	/*
	windowEdit: function(p){
		
		new K.Panel({ 
			//id: 'windowEditFichaPsicologica',
			title: 'Editar Control de Pacientes: ' + p.paciente,
			contentURL: 'ch/conpa/edit',
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
								var paciente = p.$w.find('[name=paciente]').data('data');
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
									moti:p.$w.find('[name=moti]').val(),
									refe:p.$w.find('[name=refe]').val(),
									repa:p.$w.find('[name=repa]').val(),
									his:p.$w.find('[name=his]').val(),
									orga:p.$w.find('[name=orga]').val(),
									inte:p.$w.find('[name=inte]').val(),
									perso:p.$w.find('[name=perso]').val(),
									
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
								$.post("ch/conpa/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Control de Pacientes Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									chConpa.init();
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
						chConpa.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find("[name=btnDiag]").click(function(){
					chPaci.windowSelect({callback: function(data){
							p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=clini]').html(data.his_cli).data('data',data);
						p.$w.find('[name=edad]').html(data.edad).data('data',data);
						
					},bootstrap: true});
				});
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
								/*$row.append('<td><select class="form-control" name="tipo">'+
								'<option value="0">Social</option>'+
								'<option value="1">Medica</option>'+
								'</select></td>');
								
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
				$.post('ch/conpa/get',{_id: p.id},function(data){
					p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente.paciente)).data('data',data.paciente);
					
				    p.$w.find('[name=clini]').text(data.clin);
				    p.$w.find('[name=paciente]').text(data.paciente);
				    p.$w.find('[name=edad]').text(data.edad);
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
		*/

};
define(
	['ch/conpa'],
	function(chConpa){
		return chConpa;
	}
);