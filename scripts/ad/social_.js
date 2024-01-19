mhSocial = {
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
			nomb: item.nomb,
			ape: item.ape,
			docu: item.docu,
			antece: item.antece,
			cate: item.cate,
			instr: item.instr,
			app: item.app,
			amp: item.amp,
			nomp: item.nom,
			docp: item.docp,
			paren:item.paren,
			civp: item.civp,
			edap: item.edap,
			instr_p: item.instr_p,
			ocup: item.ocup,
			motivo: item.motivo,
			trata: item.trata,
			salud: item.salud,
			soporte: item.soporte,
			vivi: item.vivi,
			relato: item.relato,
			AODE: item.AODE,
			reca: item.reca,
			es_civil: item.es_civil
			

			
		};
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhSocial',
			titleBar: {
				title: 'Ficha Social'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nombre','Documento','Diagnostico','Categoria','Ultima Modificacion'],
					data: 'mh/social/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> ',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhSocial.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+mgEnti.formatDNI(data.paciente)+'</td>');
						$row.append('<td>'+data.antece+'</td>');
						var cate = '--';
						if(data.categoria!=null){
							cate = mhPaci.categoria[data.categoria];
						}
						$row.append('<td>'+cate+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"mh/social/if_fron?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenFMedica", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFMedica_info': function(t) {
									mhSocial.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									mhSocial.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Social:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/social/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'socialente Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhSocial.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Social');
								},
								'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Social",
										url:"mh/social/print?_id="+K.tmp.data('id')
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
		mhPaci.windowSelect({callback: function(paci){
			new K.Panel({
				title: 'Nueva Ficha Social',
				contentURL: 'mh/social/edit',
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
										paciente: mgEnti.dbRel(p.$w.find('[name=paciente] [name=mini_enti]').data('data')),
										//apoderado: mgEnti.dbRel(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')),
										antece:p.$w.find('[name=antece]').val(),
										categoria:p.$w.find('[name=cate]').val(),
										instr:p.$w.find('[name=instr]').text(),
										motivo:p.$w.find('[name=motivo]').val(),
										trata:p.$w.find('[name=trata]').val(),
										salud:p.$w.find('[name=salud]').val(),
										soporte:p.$w.find('[name=soporte]').val(),
										vivi:p.$w.find('[name=vivi]').val(),
										relato:p.$w.find('[name=relato]').val(),
										AODE:p.$w.find('[name=AODE]').val(),
										//es_civil:p.$w.find('[name=es_civil]').val(),
										//fena:p.$w.find('[name=fena]').text(),
										//domi:p.$w.find('[name=domi]').text(),
										//ocupa:p.$w.find('[name=ocupa]').text(),
										//es_civil:p.$w.find('[name=es_civil]').text(),
										//tele:p.$w.find('[name=tele]').text(),
										parientes:[]
									};

									if ( p.$w.find('[name=gridFami] tbody tr').length>0) {
										for(var i=0;i< p.$w.find('[name=gridFami] tbody tr').length;i++){
											var $row = p.$w.find('[name=gridFami] tbody tr').eq(i);
											var _pariente = {
												app:$row.find('[name=app]').val(),
												amp:$row.find('[name=amp]').val(),
												nomp:$row.find('[name=nomp]').val(),
												docp:$row.find('[name=docp]').val(),
												paren:$row.find('[name=paren]').val(),
												civp:$row.find('[name=civp]').val(),
												edap:$row.find('[name=edap]').val(),
												instr_p:$row.find('[name=instr_p]').val(),
												ocup:$row.find('[name=ocup]').val()
											}
											data.parientes.push(_pariente);
										}
									}
									//console.log(data);return false
									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("mh/social/save",data,function(result){
										K.clearNoti();
										K.msg({title: ciHelper.titles.regiGua,text: "Ficha Social Agregada!"});
										mhSocial.init();
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){			
							mhSocial.init();
						}
					}
				},
				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					p.$w.find('[name=paciente] .panel-title').html('DATOS DEL PACIENTE');
					p.$w.find('[name=paciente] [name=btnSel]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data);
						},bootstrap: true});
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

					/*p.$w.find('[name=apoderado] .panel-title').html('DATOS DEL APODERADO');
					p.$w.find('[name=apoderado] [name=btnSel]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data);
						},bootstrap: true});
					});
					p.$w.find('[name=apoderado] [name=btnAct]').click(function(){
						if(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')==null){
							K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe elegir una entidad!',
								type: 'error'
							});
						}else{
							mgEnti.windowEdit({callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data);
							},id: p.$w.find('[name=apoderado] [name=mini_enti]').data('data')._id.$id});
						}
					});*/
					new K.grid({
						$el: p.$w.find('[name=gridFami]'),
						cols: ['Apellido Paterno','Apellido Materno','Nombres','Parentesco','Est.Civil','Edad','Grado Instruccion','Ocupacion',''],
						stopLoad: true,
						pagination: false,
						search: false,
						store:false,
						toolbarHTML: '<button type = "button" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Pariente</button >',
						onContentLoaded: function($el){
							$el.find('button').click(function(){
								var $row = $('<tr class="item">');
								$row.append('<td><input type="text" class="form-control" name="app" /></td>');
								$row.append('<td><input type="text" class="form-control" name="amp" /></td>');
								$row.append('<td><input type="text" class="form-control" name="nomp" /></td>');
								//$row.append('<td><input type="text" class="form-control" name="docp" /></td>');
								$row.append('<td><select class="form-control" name="paren">'+
									'<option value="Padre">Padre</option>'+
									'<option value="Madre">Madre</option>'+
									'<option value="Hijo">Hijo(a)</option>'+
									'<option value="Hermano">Hermano(a)</option>'+
									'<option value="Tio">Tio(a)</option>'+
									'<option value="Sobrino">Sobrino(a)</option>'+
									'<option value="Primo">Primo(a)</option>'+
									'<option value="Otro">Otro</option>'+
								'</select></td>');
								$row.append('<td><input type="text" class="form-control" name="civp" /></td>');
								$row.append('<td><input type="text" class="form-control" name="edap" /></td>');
								$row.append('<td><input type="text" class="form-control" name="instr_p" /></td>');
								$row.append('<td><input type="text" class="form-control" name="ocup" /></td>');
								$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								p.$w.find('[name=gridFami] tbody').append($row);
							});
						}
					});
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),paci.paciente);
					mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),paci.apoderado);
					p.$w.find('[name=his]').html(paci.his_cli).data('data',paci);
					p.$w.find('[name=instr]').html(paci.instr).data('data',paci);
					p.$w.find('[name=es_civil]').html(paci.es_civil).data('data',paci);
					p.$w.find('[name=fena]').html(moment(paci.fecha_na.sec,'X').format('YYYY-MM-DD')).data('data',paci);
					p.$w.find('[name=domi]').html(paci.domi).data('data',paci);
					p.$w.find('[name=ocupa]').html(paci.ocupa).data('data',paci);
					p.$w.find('[name=tele]').html(paci.tele).data('data',paci);
				}
			});
		}});
	},
windowEdit: function(p){
		new K.Panel({ 
			//id: 'windowEditFichaSocial',
			title: 'Editar Ficha Social: ' + p.nomb,
			contentURL: 'mh/social/edit',
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
									//apoderado: mgEnti.dbRel(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')),
									antece:p.$w.find('[name=antece]').val(),
									categoria:p.$w.find('[name=cate]').val(),
									instr:p.$w.find('[name=instr]').text(),
									motivo:p.$w.find('[name=motivo]').val(),
									trata:p.$w.find('[name=trata]').val(),
									salud:p.$w.find('[name=salud]').val(),
									soporte:p.$w.find('[name=soporte]').val(),
									vivi:p.$w.find('[name=vivi]').val(),
									relato:p.$w.find('[name=relato]').val(),
									AODE:p.$w.find('[name=AODE]').val(),
									//es_civil:p.$w.find('[name=es_civil]').val(),
									//fena:p.$w.find('[name=fena]').text(),
									//domi:p.$w.find('[name=domi]').text(),
									//ocupa:p.$w.find('[name=ocupa]').text(),
									//es_civil:p.$w.find('[name=es_civil]').text(),
									//tele:p.$w.find('[name=tele]').text(),
									parientes:[],
									recategorizacion:[]
								};
								if ( p.$w.find('[name=gridReca] tbody tr').length>0) {
									for(var i=0;i< p.$w.find('[name=gridReca] tbody tr').length;i++){
										var $row = p.$w.find('[name=gridReca] tbody tr').eq(i);
										var _recategoria = {
											reca:$row.find('[name=reca]').val()
											//console.log();
											//reca:$row.find('[name=reca]').val(moment(data.reca.sec,'X').format('YYYY-MM-DD'))
											
										}
										data.recategorizacion.push(_recategoria);
									}
								}
								if ( p.$w.find('[name=gridFami] tbody tr').length>0) {
									for(var i=0;i< p.$w.find('[name=gridFami] tbody tr').length;i++){
										var $row = p.$w.find('[name=gridFami] tbody tr').eq(i);
										var _pariente = {
											app:$row.find('[name=app]').val(),
											amp:$row.find('[name=amp]').val(),
											nomp:$row.find('[name=nomp]').val(),
											docp:$row.find('[name=docp]').val(),
											paren:$row.find('[name=paren]').val(),
											civp:$row.find('[name=civp]').val(),
											edap:$row.find('[name=edap]').val(),
											instr_p:$row.find('[name=instr_p]').val(),
											ocup:$row.find('[name=ocup]').val()
										}
										data.parientes.push(_pariente);
									}
								}
								//console.log(data);return false
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/social/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Social Agregada!"});
									mhSocial.init();
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
						mhSocial.init();
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

				/*p.$w.find('[name=apoderado] .panel-title').html('DATOS DEL APODERADO');
				p.$w.find('[name=apoderado] [name=btnSel]').hide();
				p.$w.find('[name=apoderado] [name=btnAct]').click(function(){
					if(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data);
						},id: p.$w.find('[name=apoderado] [name=mini_enti]').data('data')._id.$id});
					}
				});
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente);
						mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data.apoderado);
						p.$w.find('[name=instr]').html(data.instr).data('data',data);
						p.$w.find('[name=es_civil]').html(data.es_civil).data('data',data);
						p.$w.find('[name=fena]').html(moment(data.fecha_na.sec,'X').format('YYYY-MM-DD')).data('data',data);
						p.$w.find('[name=domi]').html(data.domi).data('data',data);
						p.$w.find('[name=ocupa]').html(data.ocupa).data('data',data);
					},bootstrap: true});
				});*/

				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridReca]'),
					cols: ['Fecha de Recategorizacion',''],
					stopLoad: true,
					pagination: false,
					search: false,
					store:false,
					toolbarHTML: '<button type = "button" name="btnAddRecategoria" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Recategorizacion</button >',
					onContentLoaded: function($el){

						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="reca"  /></td>');
							$row.find('[name=reca]').val(K.date()).datepicker();

							$row.find('tbody').append($row);
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridReca] tbody').append($row);
						});
					p.$w.find("[name=reca]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});

					}
				});
				//COMPOSICION FAMILIAR\\
				new K.grid({
					$el: p.$w.find('[name=gridFami]'),
					cols: ['Apellido Paterno','Apellido Materno','Nombres','Parentesco','Est.Civil','Edad','Grado Instruccion','Ocupacion',''],
					stopLoad: true,
					pagination: false,
					search: false,
					store:false,
					toolbarHTML: '<button type = "button" name="btnAddPariente" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Pariente</button >',
					onContentLoaded: function($el){

						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" class="form-control" name="app" /></td>');
							$row.append('<td><input type="text" class="form-control" name="amp" /></td>');
							$row.append('<td><input type="text" class="form-control" name="nomp" /></td>');
							//$row.append('<td><input type="text" class="form-control" name="docp" /></td>');
							$row.append('<td><select class="form-control" name="paren">'+
								'<option value="Padre">Padre</option>'+
								'<option value="Madre">Madre</option>'+
								'<option value="Hijo">Hijo(a)</option>'+
								'<option value="Hermano">Hermano(a)</option>'+
								'<option value="Tio">Tio(a)</option>'+
								'<option value="Sobrino">Sobrino(a)</option>'+
								'<option value="Primo">Primo(a)</option>'+
								'<option value="Otro">Otro</option>'+
							'</select></td>');
							$row.append('<td><input type="text" class="form-control" name="civp" /></td>');
							$row.append('<td><input type="text" class="form-control" name="edap" /></td>');
							$row.append('<td><input type="text" class="form-control" name="instr_p" /></td>');
							$row.append('<td><input type="text" class="form-control" name="ocup" /></td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridFami] tbody').append($row);
						});
					}
				});
				$.post('mh/social/get',{_id: p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente.paciente);
					//mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data.apoderado);
					p.$w.find('[name=antece]').val(data.antece);
					p.$w.find('[name=cate]').val(data.categoria);
					p.$w.find('[name=instr]').text(data.instr);
					
					p.$w.find('[name=motivo]').val(data.motivo);
					p.$w.find('[name=trata]').val(data.trata);
					p.$w.find('[name=salud]').val(data.salud);
					p.$w.find('[name=soporte]').val(data.soporte);
					p.$w.find('[name=vivi]').val(data.vivi);
					p.$w.find('[name=relato]').val(data.relato);
					p.$w.find('[name=AODE]').val(data.AODE);
					//p.$w.find('[name=es_civil]').val(data.es_civil);
					p.$w.find('[name=fena]').text(moment(data.paciente.fecha_na.sec,'X').format('DD/MM/YYYY'));
					//p.$w.find('[name=domi]').text(data.domi);
					//p.$w.find('[name=ocupa]').text(data.ocupa);
					//p.$w.find('[name=es_civil]').text(data.es_civil);

					if(data.parientes!=null){
						if(data.parientes.length>0){
							for(var i = 0;i<data.parientes.length;i++){
								p.$w.find('[name=btnAddPariente]').click();
								var $row = p.$w.find('[name=gridFami] tbody tr:last');
								$row.find('[name=app]').val(data.parientes[i].app);
								$row.find('[name=amp]').val(data.parientes[i].amp);
								$row.find('[name=nomp]').val(data.parientes[i].nomp);
								$row.find('[name=docp]').val(data.parientes[i].docp);
								$row.find('[name=paren]').val(data.parientes[i].paren);
								$row.find('[name=civp]').val(data.parientes[i].civp);
								$row.find('[name=edap]').val(data.parientes[i].edap);
								$row.find('[name=instr_p]').val(data.parientes[i].instr_p);
								$row.find('[name=ocup]').val(data.parientes[i].ocup);
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
	['mh/paci'],
	function(mhpaci ){
		return mhSocial;
	}
);