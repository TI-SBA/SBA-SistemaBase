adSocial = {
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
			nmie: item.nmie,
			cate: item.cate,
			cfami: item.cfami,
			app: item.app,
			amp: item.amp,
			nomp: item.nom,
			docp: item.docp,
			paren:item.paren,
			civp: item.civp,
			edap: item.edap,
			cfami_p: item.cfami_p,
			ocup: item.ocup,
			ingr: item.ingr,
			nhab: item.nhab,
			tfam: item.tfam,
			pres: item.pres,
			dina: item.dina,
			tipo: item.tipo,
			vivi: item.vivi,
			reca: item.reca,
			p1: item.p1,
			p2: item.p2,
			p3: item.p3,
			p4: item.p4,
			p5: item.p5,
			es_civil: item.es_civil
			

			
		};
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adSocial',
			titleBar: {
				title: 'Ficha Social'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nombre','Documento','Ultima Modificacion'],
					data: 'ad/social/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> ',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adSocial.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+mgEnti.formatDNI(data.paciente)+'</td>');
						
						//$row.append('<td>'+data.nmie+'</td>');
						/*
						var cate = '--';
						if(data.categoria!=null){
							cate = adPaci.categoria[data.categoria];
						}
						*/
						//$row.append('<td>'+cate+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"ad/social/if_fron?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenFMedica", {
							bindings: {
								'conMenFMedica_info': function(t) {
									adSocial.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									adSocial.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
								/*
								'conMenFMedica_edi': function(t) {
				 					$.post('ad/social/permiso',{_id: K.tmp.data('id')},function(rpta){
				 						if(rpta.data.permiso==true){
				 							K.msg({title:'Mensaje del Sistema!',type: rpta.status,text: rpta.message});
				 							adSocial.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
				 						}else{
				 							K.msg({title:'Mensaje del Sistema!',type: rpta.status,text: rpta.message});
				 						}
				 						adSocial.init();
				 					},'json')
									
								},
								*/
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Social:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/social/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'socialente Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adSocial.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Social');
								},
								'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Social",
										url:"ad/social/print?_id="+K.tmp.data('id')
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
		adPaci.windowSelect({callback: function(paci){
			new K.Panel({
				title: 'Nueva Ficha Social',
				contentURL: 'ad/social/edit',
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
										apoderado: mgEnti.dbRel(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')),										paciente: mgEnti.dbRel(p.$w.find('[name=paciente] [name=mini_enti]').data('data')),
										nmie:p.$w.find('[name=nmie]').val(),
										tria:p.$w.find('[name=tria]').val(),
										fono:p.$w.find('[name=fono]').val(),
										cfami:p.$w.find('[name=cfami]').val(),
										ingr:p.$w.find('[name=ingr]').val(),
										nhab:p.$w.find('[name=nhab]').val(),
										tfam:p.$w.find('[name=tfam]').val(),
										dina:p.$w.find('[name=dina]').val(),
										pres:p.$w.find('[name=pres]').val(),
										tipo:p.$w.find('[name=tipo]').val(),
										vivi:p.$w.find('[name=vivi]').val(),
										cons:p.$w.find('[name=cons]').val(),
										tsoc:p.$w.find('[name=tsoc]').val(),
										dsoc:p.$w.find('[name=dsoc]').val(),
										rol:p.$w.find('[name=rol]').val(),
										psoc:p.$w.find('[name=psoc]').val(),
										pono:p.$w.find('[name=pono]').val(),
										his:p.$w.find('[name=his]').text(),
										edad:p.$w.find('[name=edad]').text(),
										sexo:p.$w.find('[name=sexo]').text(),
										grad:p.$w.find('[name=grad]').text(),
										domi:p.$w.find('[name=domi]').val(),
										p1:p.$w.find('[name=p1]').val(),
										p2:p.$w.find('[name=p2]').val(),
										p3:p.$w.find('[name=p3]').val(),
										p4:p.$w.find('[name=p4]').val(),
										p5:p.$w.find('[name=p5]').val(),

										//evoluciones:[],
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
												cfami_p:$row.find('[name=cfami_p]').val(),
												ocup:$row.find('[name=ocup]').val()
											}
											data.parientes.push(_pariente);
										}
									}
									/*if ( p.$w.find('[name=gridEvol] tbody tr').length>0) {
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
									*/
									//console.log(data);return false
									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("ad/social/save",data,function(result){
										K.clearNoti();
										K.msg({title: ciHelper.titles.regiGua,text: "Ficha Social Agregada!"});
										adSocial.init();
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){			
							adSocial.init();
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

					p.$w.find('[name=apoderado] .panel-title').html('DATOS DEL APODERADO');
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
					});
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
								$row.append('<td><input type="text" class="form-control" name="cfami_p" /></td>');
								$row.append('<td><input type="text" class="form-control" name="ocup" /></td>');
								$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								p.$w.find('[name=gridFami] tbody').append($row);
							});
						}
					});
					/*
					new K.grid({
									$el: p.$w.find('[name=gridEvol]'),
									cols: ['Fecha','Evolucion','Eliminar'],
									stopLoad: true,
									pagination: false,
									search: false,
									store:false,
									toolbarHTML: '<button type = "button" name="btnAddEvol" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Evolucion</button >',
									onContentLoaded: function($el){
										$el.find('button').click(function(){
											var $row = $('<tr class="item">');
											$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
											$row.find('[name=fec]').val(K.date()).datepicker();
											$row.append('<td><textarea type="text" class="form-control" name="evol"></textarea></td>');
											$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
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
					*/
					p.$w.find("[name=fec]").datepicker({
	   				format: 'mm/dd/yyyy',
	    			startDate: '-3d'
					});

					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),paci.paciente);
					mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),paci.apoderado);
					p.$w.find('[name=his]').html(paci.his_cli).data('data',paci);
					p.$w.find('[name=sexo]').html(paci.sexo).data('data',paci);
					p.$w.find('[name=edad]').html(paci.edad).data('data',paci);
					p.$w.find('[name=fena]').html(moment(paci.fecha_na.sec,'X').format('YYYY-MM-DD')).data('data',paci);
					//p.$w.find('[name=domi]').html(paci.domi).data('data',paci);
					p.$w.find('[name=grad]').html(paci.instr).data('data',paci);
					//p.$w.find('[name=tele]').html(paci.tele).data('data',paci);
				}
			});
		}});
	},
windowEdit: function(p){
		new K.Panel({ 
			//id: 'windowEditFichaSocial',
			title: 'Editar Ficha Social: ' + p.nomb,
			contentURL: 'ad/social/edit',
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
										apoderado: mgEnti.dbRel(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')),
										nmie:p.$w.find('[name=nmie]').val(),
										tria:p.$w.find('[name=tria]').val(),
										cfami:p.$w.find('[name=cfami]').val(),
										ingr:p.$w.find('[name=ingr]').val(),
										nhab:p.$w.find('[name=nhab]').val(),
										tfam:p.$w.find('[name=tfam]').val(),
										pres:p.$w.find('[name=pres]').val(),
										fono:p.$w.find('[name=fono]').val(),
										dina:p.$w.find('[name=dina]').val(),
										tipo:p.$w.find('[name=tipo]').val(),
										vivi:p.$w.find('[name=vivi]').val(),
										cons:p.$w.find('[name=cons]').val(),
										tsoc:p.$w.find('[name=tsoc]').val(),
										dsoc:p.$w.find('[name=dsoc]').val(),
										psoc:p.$w.find('[name=psoc]').val(),
										pono:p.$w.find('[name=pono]').val(),
										rol:p.$w.find('[name=rol]').val(),
										his:p.$w.find('[name=his]').text(),
										edad:p.$w.find('[name=edad]').text(),
										sexo:p.$w.find('[name=sexo]').text(),
										grad:p.$w.find('[name=grad]').text(),
										domi:p.$w.find('[name=domi]').val(),
										p1:p.$w.find('[name=p1]').val(),
										p2:p.$w.find('[name=p2]').val(),
										p3:p.$w.find('[name=p3]').val(),
										p4:p.$w.find('[name=p4]').val(),
										p5:p.$w.find('[name=p5]').val(),
									parientes:[],
									//evoluciones:[]
								};
								/*
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
								*/
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
											cfami_p:$row.find('[name=cfami_p]').val(),
											ocup:$row.find('[name=ocup]').val()
										}
										data.parientes.push(_pariente);
									}
								}
								/*
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
								*/
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/social/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Social Agregada!"});
									adSocial.init();
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
						adSocial.init();
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
				p.$w.find('[name=apoderado] .panel-title').html('DATOS DEL APODERADO');
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
					});
				K.block();
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
							$row.append('<td><input type="text" class="form-control" name="cfami_p" /></td>');
							$row.append('<td><input type="text" class="form-control" name="ocup" /></td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridFami] tbody').append($row);
						});
					}
				});
				/*
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
								$row.append('<td><input type="text" class="form-control" name="tipo" disabled="disabled" value="SOCIAL" /></td>');
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
				*/
				$.post('ad/social/get',{_id: p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente.paciente);
					mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data.paciente.apoderado);
					p.$w.find('[name=nmie]').val(data.nmie);
					p.$w.find('[name=tria]').val(data.tria);
					p.$w.find('[name=cfami]').val(data.cfami);
					p.$w.find('[name=ingr]').val(data.ingr);
					p.$w.find('[name=nhab]').val(data.nhab);
					p.$w.find('[name=fono]').val(data.fono);
					p.$w.find('[name=tfam]').val(data.tfam);
					p.$w.find('[name=pres]').val(data.pres);
					p.$w.find('[name=dina]').val(data.dina);
					p.$w.find('[name=tipo]').val(data.tipo);
					p.$w.find('[name=vivi]').val(data.vivi);
					p.$w.find('[name=cons]').val(data.cons);
					p.$w.find('[name=tsoc]').val(data.tsoc);
					p.$w.find('[name=dsoc]').val(data.dsoc);
					p.$w.find('[name=psoc]').val(data.psoc);
					p.$w.find('[name=pono]').val(data.pono);
					p.$w.find('[name=his]').text(data.his);
					p.$w.find('[name=edad]').text(data.edad);
					p.$w.find('[name=sexo]').text(data.sexo);
					p.$w.find('[name=grad]').text(data.grad);
					p.$w.find('[name=domi]').val(data.domi);
					p.$w.find('[name=rol]').val(data.rol);
					p.$w.find('[name=p1]').val(data.p1);
					p.$w.find('[name=p2]').val(data.p2);
					p.$w.find('[name=p3]').val(data.p3);
					p.$w.find('[name=p4]').val(data.p4);
					p.$w.find('[name=p5]').val(data.p5);
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
								$row.find('[name=cfami_p]').val(data.parientes[i].cfami_p);
								$row.find('[name=ocup]').val(data.parientes[i].ocup);
							}
						}
					}
					/*
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
					*/
					K.unblock();
				},'json');
			}
		});
	
	},

};

define(
	['ad/paci'],
	function(adPaci ){
		return adSocial;
	}
);