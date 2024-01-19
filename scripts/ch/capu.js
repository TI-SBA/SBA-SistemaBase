chCapu = {
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
			fec: item.fec
			
		};
	},
	init: function(){
		K.initMode({
			mode: 'ch',
			action: 'chCapu',
			titleBar: {
				title: 'Cuentas Corrientes de Pacientes'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Historia Clinica','Paciente','Estado'],
					data: 'ch/capu/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							chCapu.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.estado+'</td>');
						$row.append('<td>'+data.clin+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');				
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenMhEvol", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenMhEvol_evol': function(t) {
									chCapu.windowEdit({id: K.tmp.data('id'),paciente: K.tmp.data('data').paciente, clin:K.tmp.data('data').clin});
								},
								'conMenMhEvol_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Historia Clinica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ch/capu/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Historia Clinica Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											chCapu.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Historia Clinica');
								},
								'conMenMhEvol_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Historia Clinica",
										url:"ch/capu/print?_id="+K.tmp.data('id')
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
			id: 'windowNewHistoriaClinica',
			title: 'Nueva Historia Clinica',
			contentURL: 'ch/capu/edit',
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
								//var paciente = p.$w.find('[name=paci]').data('data');
								var data = {
									
									clin:p.$w.find('[name=clini]').text(),
									
									
								};
								
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ch/capu/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Historia Clinica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									chCapu.init();
									
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
				p.$w = $('#windowNewHistoriaClinica');
				p.$w.find("[name=btnDiag]").click(function(){
					chPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=clini]').html(data.his_cli).data('data',data);
						
						
					},bootstrap: true});
				});
			}
		});
	},
	
	
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditHistoriaClinica',
			title: 'Agregar Evolucion de Historia Clinica: ' +mgEnti.formatName(p.paciente),
			contentURL: 'ch/capu/edit',
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
									paciente: mgEnti.dbRel(paciente),
									clin:p.$w.find('[name=clini]').text(),
									evoluciones:[]
									
								};
									if ( p.$w.find('[name=gridEvol] tbody tr').length>0) {
										for(var i=0;i< p.$w.find('[name=gridEvol] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridEvol] tbody tr').eq(i);
											var _evolucion = {
												fec:$row.find('[name=fec]').val(),
												evol:$row.find('[name=evol]').val(),
												tipo:$row.find('[name=tipo]').val(),
												user:$row.find('[name=user]').data("data"),
											}
											data.evoluciones.push(_evolucion);
										}
									}
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ch/capu/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Historia Clinica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									chCapu.init();
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
				p.$w = $('#windowEditHistoriaClinica');
				p.$w.find("[name=btnDiag]").click(function(){
					chPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=clini]').html(data.his_cli);
						
						
					},bootstrap: true});
				});
				
				p.$w.find('[name=paci]').html(mgEnti.formatName(p.paciente)).data('data',p.paciente);
				p.$w.find('[name=clini]').html(p.clin);
				K.block();
				new K.grid({
						$el: p.$w.find('[name=gridEvol]'),
						cols: ['Fecha','Historial','Tipo de Evolucion','Usuario','Eliminar'],
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
				$.post('ch/capu/get',{_id: p.id},function(data){
				    p.$w.find('[name=clini]').text(data.clin);
				    p.$w.find('[name=paci]').text(data.paci);
				    p.$w.find('[name=his]').val(data.his);
				    if(data.evoluciones!=null){
						if(data.evoluciones.length>0){
							for(var i = 0;i<data.evoluciones.length;i++){
								p.$w.find('[name=btnAddEvol]').click();
								var $row = p.$w.find('[name=gridEvol] tbody tr:last');
								$row.find('[name=fec]').val(moment(data.evoluciones[i].fec.sec,'X').format('YYYY-MM-DD'));
								//$row.find('[name=fec]').val(data.evoluciones[i].fec);
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
	['ch/paci'],
	function(chpaci){
		return chCapu;
	}
);