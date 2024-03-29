chSocial = {
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
			mode: 'ch',
			action: 'chSocial',
			titleBar: {
				title: 'Ficha Social'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nombre','Documento','Ultima Modificacion'],
					data: 'ch/social/lista',
					params: {modulo: 'CH'},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							chSocial.windowNew();
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
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+mgEnti.formatDNI(data.paciente)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"ch/social/if_fron?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenFMedica", {
							bindings: {
								'conMenFMedica_info': function(t) {
									chSocial.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									chSocial.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Social:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ch/social/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'socialente Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											chSocial.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Social');
								},
								'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Social",
										url:"ch/social/print?_id="+K.tmp.data('id')
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
		chPaci.windowSelect({callback: function(paci){
			new K.Panel({
				title: 'Nueva Ficha Social',
				contentURL: 'ch/social/edit',
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
										modulo:p.$w.find('[name=modulo]').text(),

										
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
									//console.log(data);return false
									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("ch/social/save",data,function(result){
										K.clearNoti();
										K.msg({title: ciHelper.titles.regiGua,text: "Ficha Social Agregada!"});
										chSocial.init();
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){			
							chSocial.init();
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
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),paci.paciente);
					mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),paci.apoderado);
					p.$w.find('[name=his]').html(paci.his_cli).data('data',paci);
					p.$w.find('[name=sexo]').html(paci.sexo).data('data',paci);
					p.$w.find('[name=modulo]').html(paci.modulo).data('data',paci);
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
			contentURL: 'ch/social/edit',
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
										modulo:p.$w.find('[name=modulo]').text(),
									parientes:[],
									//recategorizacion:[]
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
								//console.log(data);return false
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ch/social/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Social Agregada!"});
									chSocial.init();
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
						chSocial.init();
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
				$.post('ch/social/get',{_id: p.id},function(data){
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
					p.$w.find('[name=modulo]').text(data.modulo);
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
					K.unblock();
				},'json');
			}
		});
	
	},

};

define(
	['ch/paci'],
	function(chpaci ){
		return chSocial;
	}
);