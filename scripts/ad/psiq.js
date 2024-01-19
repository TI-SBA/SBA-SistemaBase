adPsiq = {
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
			clini: item.moti,
			edad: item.edad,
			sexo: item.sexo,
			info: item.info,
			moti: item.moti,
			hien: item.hien,
			anpe: item.anpe,
			anfa: item.anfa,
			exmen:item.exmen,
			diag:item.diag,
			trat:item.trat
		};
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adPsiq',
			titleBar: {
				title: 'Ficha Psiquiatrica'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Paciente','Nro. de Historia','Ultima Modificacion'],
					data: 'ad/psiq/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> ',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adPsiq.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+data.paciente.roles.paciente.hist_cli+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"ad/psiq/if_fron?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenFPsico", {
							bindings: {
								'conMenFPsico_info': function(t) {
									adPsiq.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFPsico_edi': function(t) {
									adPsiq.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
								'conMenFPsico_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Psiquiatrica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/psiq/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'socialente Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adPsiq.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Psiquiatrica');
								},
								'conMenFPsico_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Psiquiatrica",
										url:"ad/psiq/print?_id="+K.tmp.data('id')
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
windowEdit: function(p){
		new K.Panel({ 
			title: 'Editar Ficha Psiquiatrica: ' +mgEnti.formatName(p.paciente),
			contentURL: 'ad/psiq/edit',
			width: 900,
			height: 900,
			store: false,
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
									clini:p.$w.find('[name=clini]').text(),
									info:p.$w.find('[name=info]').val(),
									moti:p.$w.find('[name=moti]').val(),
									hien:p.$w.find('[name=hien]').val(),
									anpe:p.$w.find('[name=anpe]').val(),
									anfa:p.$w.find('[name=anfa]').val(),
									exmen:p.$w.find('[name=exmen]').val(),
									diag:p.$w.find('[name=diag]').val(),
									trat:p.$w.find('[name=trat]').val(),
									evoluciones:[],
									receta:[]
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
								if ( p.$w.find('[name=gridReceta] tbody tr').length>0) {
									for(var i=0;i< p.$w.find('[name=gridReceta] tbody tr').length;i++){
										var $row = p.$w.find('[name=gridReceta] tbody tr').eq(i);
										var _recetas = {
											fecrec:$row.find('[name=fecrec]').val(),
											receta:$row.find('[name=receta]').val(),
											
											
										}
										data.receta.push(_recetas);
									}
								}
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/psiq/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psiquiatrica Agregada!"});
									adPsiq.init();
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
						adPsiq.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				//p.$w = $('#windowEditFichaSocial');
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
								$row.append('<td><input type="text" class="form-control" name="tipo" disabled="disabled" value="PSIQUIATRIA" /></td>');
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
				new K.grid({
						$el: p.$w.find('[name=gridReceta]'),
						cols: ['Fecha','Receta Medica','Eliminar'],
						stopLoad: true,
						pagination: false,
						search: false,
						store:false,
						toolbarHTML: '<button type = "button" name="btnAddReceta" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Receta Medica</button >',
						onContentLoaded: function($el){
							$el.find('button').click(function(){
								var $row = $('<tr class="item">');
								$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
								$row.find('[name=fecrec]').val(K.date()).datepicker();
								$row.append('<td><textarea type="text" class="form-control" name="receta"></textarea></td>');
								$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=user]').data('data',mgEnti.dbRel(K.session.enti));
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								p.$w.find('[name=gridEvol] tbody').append($row);
							});
							p.$w.find("[name=fecrec]").datepicker({
			   				format: 'mm/dd/yyyy',
			    			startDate: '-3d'
							});
						}
					});
					K.block();
					$.post('ad/psiq/get',{_id: p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente);
					p.$w.find('[name=clini]').text(data.paciente.roles.paciente.hist_cli);
					p.$w.find('[name=info]').val(data.info);
					p.$w.find('[name=moti]').val(data.moti);
					p.$w.find('[name=hien]').val(data.hien);
					p.$w.find('[name=anpe]').val(data.anpe);
					p.$w.find('[name=anfa]').val(data.anfa);
					p.$w.find('[name=exmen]').val(data.exmen);
					p.$w.find('[name=diag]').val(data.diag);
					p.$w.find('[name=trat]').val(data.trat);

					
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
							if(data.receta!=null){
							if(data.receta.length>0){
								for(var i = 0;i<data.receta.length;i++){
									p.$w.find('[name=btnAddReceta]').click();
									var $row = p.$w.find('[name=gridReceta] tbody tr:last');
									$row.find('[name=fecrec]').val(moment(data.receta[i].fecrec.sec,'X').format('YYYY-MM-DD'));
									$row.find('[name=receta]').val(data.receta[i].receta);
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
	['ad/paci'],
	function(adPaci ){
		return adPsiq;
	}
);