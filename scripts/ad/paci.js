adPaci = {
	sexo:{
		'0':'Femenino',
		'1':'Masculino'
	},
	categoria: {		
		"10":"Nuevo",
		"11":"Continuador",
		"8":"[D] Indigente",
		"9":"Privado"
	},
	meses: {		
		"1":"ENERO",
		"2":"FEBRERO",
		"3":"MARZO",
		"4":"ABRIL",
		"5":"MAYO",
		"6":"JUNIO",
		"7":"JULIO",
		"8":"AGOSTO",
		"9":"SETIEMBRE",
		"10":"OCTUBRE",
		"11":"NOVIEMBRE",
		"12":"DICIEMBRE"
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adPaci',
			titleBar: {
				title: 'Pacientes - Ficha Frontal'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nro. De Documento','Historia Clinica','categoria','Apellidos y Nombres','Direccion','Fecha de Registro','Ultima Modificacion'],
					data: 'ad/paci/lista',
					params: {},
					itemdescr: 'paciente(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adPaci.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ 
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatDNI(data.paciente)+'</td>');
						$row.append('<td>'+data.his_cli+'</td>');
						$row.append('<td>'+adPaci.categoria[data.categoria]+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+mgEnti.formatDire(data.paciente)+'</td>');
						var fe_regi = '--';
						if(data.fe_regi!=null){
							moment(data.fe_regi.sec,'X').format('YYYY-MM-DD')
						}
						$row.append('<td>'+fe_regi+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe Frontal",
								url:"ad/paci/print?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenMheresi", {
							onShowMenu: function($row, menu) {
							
						},
							bindings: {
								'conMenMheresi_fron': function(t) {
									adPaci.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenMheresi_edi': function(t) {
									adPaci.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenMheresi_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Paciente:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/paci/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Paciente Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adPaci.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Paciente');
								},
								'conMenMheresi_fron':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Informe Frontal",
										url:"ad/paci/print?_id="+K.tmp.data('id')
									});
								},
								'conMenMheresi_soci':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha de Salud Publica",
										url:"ad/paci/get_fichasalud?_id="+K.tmp.data('id')
									});
								},
								'conMenMheresi_tarje':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Tarjeta de Admicion",
										url:"ad/paci/get_tarjeta?_id="+K.tmp.data('id')
									});
								},
								'conMenMheresi_carnet':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Carnet de Paciente",
										url:"ad/paci/get_carnet?_id="+K.tmp.data('id')
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
			title: 'Nuevo Paciente',
			contentURL: 'ad/paci/edit',
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


									his_cli: p.$w.find('[name=his_Cli]').val(),
									estado: p.$w.find('[name=estado]').val(),
									fe_regi: p.$w.find('[name=fe_regi]').val(),
									categoria: p.$w.find('[name=categoria]').val(),
									/*ti_doc: p.$w.find('[name=ti_doc]').val(),
									doc: p.$w.find('[name=doc]').val(),
									ape: p.$w.find('[name=ape]').val(),
									nom: p.$w.find('[name=nom]').val(),*/
									paciente: p.$w.find('[name=paciente] [name=mini_enti]').data('data'),
									pais: p.$w.find('[name=pais]').val(),
									sexo: p.$w.find('[name=sexo]').val(),
									domi: p.$w.find('[name=domi]').val(),
									edad: p.$w.find('[name=edad]').val(),
									procedencia: {
										departamento: p.$w.find('[name=procede_depa] :selected').val(),
										provincia: p.$w.find('[name=procede_prov] :selected').val(),
										distrito: p.$w.find('[name=procede_dist] :selected').val(),
									},
									lugar_nacimiento: {
										departamento: p.$w.find('[name=luna_depa] :selected').val(),
										provincia: p.$w.find('[name=luna_prov] :selected').val(),
										distrito: p.$w.find('[name=luna_dist] :selected').val(),
									},
									//LUGAR Y FECHA DE NACIMIENTO
									fecha_na: p.$w.find('[name=fecha_na]').val(),
									//OTROS DATOS
									es_civil: p.$w.find('[name=es_civil]').val(),
									reli: p.$w.find('[name=reli]').val(),
									idio: p.$w.find('[name=idio]').val(),
									instr: p.$w.find('[name=instr]').val(),
									refe: p.$w.find('[name=refe]').val(),
									tele: p.$w.find('[name=tele]').val(),
									ocupa: p.$w.find('[name=ocupa]').val(),
									t_deso: p.$w.find('[name=t_deso]').val(),
									m_resi: p.$w.find('[name=m_resi]').val(),
									//INFORMACION APODERADO
									apoderado: p.$w.find('[name=apoderado] [name=mini_enti]').data('data'),
									/*apa: p.$w.find('[name=apa]').val(),
									ama: p.$w.find('[name=ama]').val(),
									nomb_a: p.$w.find('[name=nomb_a]').val(),
									tel_apo: p.$w.find('[name=tel_apo]').val(),
									di_apo: p.$w.find('[name=di_apo]').val(),*/
									//DIAGNOSTICO
									d_ini: p.$w.find('[name=d_ini]').text(),
									m_consu: p.$w.find('[name=m_consu]').val(),

								};
								if(data.paciente==null){
									p.$w.find('[name=paciente] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un paciente!',
										type: 'error'
									});
								}else data.paciente = mgEnti.dbRel(data.paciente);
								if(data.apoderado==null){
									p.$w.find('[name=apoderado] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un apoderado!',
										type: 'error'
									});
								}else data.apoderado = mgEnti.dbRel(data.apoderado);
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/paci/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Paciente agregado!"});
									adPaci.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						adPaci.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				var ubig = new Ubig();

				//PROVEDENCIA
				var $cbo_depa = p.$w.find('[name=procede_depa]');
				var $cbo_prov = p.$w.find('[name=procede_prov]');
				var $cbo_dist = p.$w.find('[name=procede_dist]');
				$cbo_depa.change(function(){
					$cbo_prov.empty();
					ubig.get_pro(""+p.$w.find('[name=procede_depa] :selected').val(), function(list_pro){
						list_pro.forEach(function(prov){
							if(prov.provincia!='00')
								$cbo_prov.append('<option value="'+prov.provincia+'">'+prov.nombre+'</option>');
						});
						$cbo_prov.change();	
					});
				});
				$cbo_prov.change(function(){
					$cbo_dist.empty();
					ubig.get_dis(""+p.$w.find('[name=procede_depa] :selected').val(), ""+p.$w.find('[name=procede_prov] :selected').val(), function(list_dis){
						list_dis.forEach(function(dist){
							if(dist.distrito!='00')
								$cbo_dist.append('<option value="'+dist.distrito+'">'+dist.nombre+'</option>');
						});
					});
				});
				ubig.get_dep(function(list_dep){

					list_dep.forEach(function(depa){
						if(depa.departamento!='00')
							$cbo_depa.append('<option value="'+depa.departamento+'">'+depa.nombre+'</option>');
					});
					$cbo_prov.change();
				});

				//NACIMIENTO
				var $cbo_depa2 = p.$w.find('[name=luna_depa]');
				var $cbo_prov2 = p.$w.find('[name=luna_prov]');
				var $cbo_dist2 = p.$w.find('[name=luna_dist]');
				$cbo_depa2.change(function(){
					$cbo_prov2.empty();
					ubig.get_pro(""+p.$w.find('[name=luna_depa] :selected').val(), function(list_pro){
						list_pro.forEach(function(prov){
							if(prov.provincia!='00')
								$cbo_prov2.append('<option value="'+prov.provincia+'">'+prov.nombre+'</option>');
						});
						$cbo_prov2.change();	
					});
				});
				$cbo_prov2.change(function(){
					$cbo_dist2.empty();
					ubig.get_dis(""+p.$w.find('[name=luna_depa] :selected').val(), ""+p.$w.find('[name=luna_prov] :selected').val(), function(list_dis){
						list_dis.forEach(function(dist){
							if(dist.distrito!='00')
								$cbo_dist2.append('<option value="'+dist.distrito+'">'+dist.nombre+'</option>');
						});
					});
				});
				ubig.get_dep(function(list_dep){

					list_dep.forEach(function(depa){
						if(depa.departamento!='00')
							$cbo_depa2.append('<option value="'+depa.departamento+'">'+depa.nombre+'</option>');
					});
					$cbo_prov2.change();
				});


				p.$w.find('[name=paciente] .panel-title').html('DATOS DEL PACIENTE');
				p.$w.find('[name=paciente] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data);
						p.$w.find('[name=his_Cli]').removeAttr('disabled');
						if(data.roles!=null){
							if(data.roles.paciente!=null){
								if(data.roles.paciente.centro=='AD'){
									p.$w.find('[name=his_Cli]').val(data.roles.paciente.hist_cli).attr('disabled','disabled');
								}
							}
						}
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






				p.$w.find("[name=fe_regi]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find("[name=fecha_na]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find("[name=btnDiag]").click(function(){
					adDini.windowSelect({callback: function(data){
						p.$w.find('[name=d_ini]').html(data.sigl + '-' + data.nomb).data('data',data);
					},bootstrap: true});
				});

				//$.post("ad/paci/save",data,function(result)
				$.post("ad/paci/get_historial",function(data){

					var his=0;
					if(data ==null){
						his = 1;
					}else{
						his = parseFloat(data.his_cli) + 1;

					}
					p.$w.find('[name=his_Cli]').val(his);
				},'json');
			}
				
				
		});
	},
	windowEdit: function(p){
		new K.Panel({ 
			title: 'Editar Pacient: ' + p.nom,
			contentURL: 'ad/paci/edit',
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
									_id:p.id,
									his_cli: p.$w.find('[name=his_Cli]').val(),
									estado: p.$w.find('[name=estado]').val(),
									fe_regi: p.$w.find('[name=fe_regi]').val(),
									categoria: p.$w.find('[name=categoria]').val(),
									/*ti_doc: p.$w.find('[name=ti_doc]').val(),
									doc: p.$w.find('[name=doc]').val(),
									ape: p.$w.find('[name=ape]').val(),
									nom: p.$w.find('[name=nom]').val(),*/
									paciente: p.$w.find('[name=paciente] [name=mini_enti]').data('data'),
									pais: p.$w.find('[name=pais]').val(),
									sexo: p.$w.find('[name=sexo]').val(),
									domi: p.$w.find('[name=domi]').val(),
									edad: p.$w.find('[name=edad]').val(),
									procedencia: {
										departamento: p.$w.find('[name=procede_depa] :selected').val(),
										provincia: p.$w.find('[name=procede_prov] :selected').val(),
										distrito: p.$w.find('[name=procede_dist] :selected').val(),
									},
									lugar_nacimiento: {
										departamento: p.$w.find('[name=luna_depa] :selected').val(),
										provincia: p.$w.find('[name=luna_prov] :selected').val(),
										distrito: p.$w.find('[name=luna_dist] :selected').val(),
									},
									//LUGAR Y FECHA DE NACIMIENTO
									fecha_na: p.$w.find('[name=fecha_na]').val(),
									//OTROS DATOS
									es_civil: p.$w.find('[name=es_civil]').val(),
									reli: p.$w.find('[name=reli]').val(),
									idio: p.$w.find('[name=idio]').val(),
									instr: p.$w.find('[name=instr]').val(),
									refe: p.$w.find('[name=refe]').val(),
									tele: p.$w.find('[name=tele]').val(),
									ocupa: p.$w.find('[name=ocupa]').val(),
									t_deso: p.$w.find('[name=t_deso]').val(),
									m_resi: p.$w.find('[name=m_resi]').val(),
									//INFORMACION APODERADO
									apoderado: p.$w.find('[name=apoderado] [name=mini_enti]').data('data'),
									/*apa: p.$w.find('[name=apa]').val(),
									ama: p.$w.find('[name=ama]').val(),
									nomb_a: p.$w.find('[name=nomb_a]').val(),
									tel_apo: p.$w.find('[name=tel_apo]').val(),
									di_apo: p.$w.find('[name=di_apo]').val(),*/
									//DIAGNOSTICO
									d_ini: p.$w.find('[name=d_ini]').text(),
									m_consu: p.$w.find('[name=m_consu]').val(),

								};
								if(data.paciente==null){
									p.$w.find('[name=paciente] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un paciente!',
										type: 'error'
									});
								}else data.paciente = mgEnti.dbRel(data.paciente);
								if(data.apoderado==null){
									p.$w.find('[name=apoderado] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un apoderado!',
										type: 'error'
									});
								}else data.apoderado = mgEnti.dbRel(data.apoderado);
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/paci/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Paciente agregado!"});
									adPaci.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						adPaci.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				var ubig = new Ubig();
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
				$.post('ad/paci/get',{_id: p.id},function(data){
			  	    
			  	    p.$w.find('[name=his_Cli]').val(data.his_cli).attr('disabled','disabled');
					p.$w.find('[name=estado]').val(data.estado);
					p.$w.find('[name=fe_regi]').val(moment(data.fe_regi.sec,'X').format('YYYY-MM-DD')).attr('disabled','disabled');
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente);
					
					p.$w.find('[name=pais]').val(data.pais);
					p.$w.find('[name=sexo]').val(data.sexo);
					p.$w.find('[name=domi]').val(data.domi);
					p.$w.find('[name=edad]').val(data.edad);
					p.$w.find('[name=categoria]').val(data.categoria),
					//p.$w.find('[name=procede]').val(data.procede);
					//LUGAR Y FECHA DE NACIMIENTO
					p.$w.find('[name=fecha_na]').val(moment(data.fecha_na.sec,'X').format('YYYY-MM-DD'));
					//p.$w.find('[name=lugar_na]').val(data.lu_na);
					//OTROS DATOS
					p.$w.find('[name=es_civil]').val(data.es_civil);
					p.$w.find('[name=reli]').val(data.reli);
					p.$w.find('[name=idio]').val(data.idio);
					p.$w.find('[name=instr]').val(data.instr);
					p.$w.find('[name=refe]').val(data.refe);
					p.$w.find('[name=tele]').val(data.tele);
					p.$w.find('[name=ocupa]').val(data.ocupa);
					p.$w.find('[name=t_deso]').val(data.t_deso);
					p.$w.find('[name=m_resi]').val(data.m_resi);
					//INFORMACION APODERADO
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
					mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data.apoderado);
					p.$w.find('[name=d_ini]').text(data.d_ini);
					p.$w.find('[name=m_consu]').val(data.m_consu);
					p.$w.find('[name=paciente] [name=btnSel]').hide();
					//PROVEDENCIA
					var $cbo_depa = p.$w.find('[name=procede_depa]');
					var $cbo_prov = p.$w.find('[name=procede_prov]');
					var $cbo_dist = p.$w.find('[name=procede_dist]');
					$cbo_depa.change(function(){
						$cbo_prov.empty();
						ubig.get_pro(""+p.$w.find('[name=procede_depa] :selected').val(), function(list_pro){
							list_pro.forEach(function(prov){
								if(prov.provincia!='00')
									$cbo_prov.append('<option value="'+prov.provincia+'">'+prov.nombre+'</option>');
							});
							$cbo_prov.change();
						});
					});
					$cbo_prov.change(function(){
						$cbo_dist.empty();
						ubig.get_dis(""+p.$w.find('[name=procede_depa] :selected').val(), ""+p.$w.find('[name=procede_prov] :selected').val(), function(list_dis){
							list_dis.forEach(function(dist){
								if(dist.distrito!='00')
									$cbo_dist.append('<option value="'+dist.distrito+'">'+dist.nombre+'</option>');
							});
						});
					});
					ubig.get_dep(function(list_dep){

						list_dep.forEach(function(depa){
							if(depa.departamento!='00')
								$cbo_depa.append('<option value="'+depa.departamento+'">'+depa.nombre+'</option>');
						});
						$cbo_prov.change();
					});

					//NACIMIENTO
					var $cbo_depa2 = p.$w.find('[name=luna_depa]');
					var $cbo_prov2 = p.$w.find('[name=luna_prov]');
					var $cbo_dist2 = p.$w.find('[name=luna_dist]');
					$cbo_depa2.change(function(){
						$cbo_prov2.empty();
						ubig.get_pro(""+p.$w.find('[name=luna_depa] :selected').val(), function(list_pro){
							list_pro.forEach(function(prov){
								if(prov.provincia!='00')
									$cbo_prov2.append('<option value="'+prov.provincia+'">'+prov.nombre+'</option>');
							});
							$cbo_prov2.change();	
						});
					});
					$cbo_prov2.change(function(){
						$cbo_dist2.empty();
						ubig.get_dis(""+p.$w.find('[name=luna_depa] :selected').val(), ""+p.$w.find('[name=luna_prov] :selected').val(), function(list_dis){
							list_dis.forEach(function(dist){
								if(dist.distrito!='00')
									$cbo_dist2.append('<option value="'+dist.distrito+'">'+dist.nombre+'</option>');
							});
						});
					});
					ubig.get_dep(function(list_dep){

						list_dep.forEach(function(depa){
							if(depa.departamento!='00')
								$cbo_depa2.append('<option value="'+depa.departamento+'">'+depa.nombre+'</option>');
						});
						$cbo_prov2.change();
					});

					p.$w.find('[name=procede_depa]').val(("00"+data.procedencia.departamento).slice(-2)).change();
					p.$w.find('[name=procede_prov]').val(("00"+data.procedencia.provincia).slice(-2));
					p.$w.find('[name=procede_dist]').val(("00"+data.procedencia.distrito).slice(-2));
					p.$w.find('[name=luna_depa]').val(("00"+data.lugar_nacimiento.departamento).slice(-2)).change();
					p.$w.find('[name=luna_prov]').val(("00"+data.lugar_nacimiento.provincia).slice(-2));
					p.$w.find('[name=luna_dist]').val(("00"+data.lugar_nacimiento.distrito).slice(-2));
					K.unblock();
				},'json');

				     p.$w.find("[name=fe_regi]").datepicker({
	   				format: 'mm/dd/yyyy',
	    			startDate: '-3d'
				});
				p.$w.find("[name=fecha_na]").datepicker({
	   				format: 'mm/dd/yyyy',
	    			startDate: '-3d'
				});
				p.$w.find("[name=btnDiag]").click(function(){
					adDini.windowSelect({callback: function(data){
						p.$w.find('[name=d_ini]').html(data.sigl + '-' + data.nomb).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Paciente',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.clearNoti();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
						}
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
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Historia Clinica','Nombre de Paciente'],
					data: 'ad/paci/lista',
					params: {},
					itemdescr: 'paciente(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.his_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.find('.modal-footer button:first').click();
						}).contextMenu('conMenListSel', {
							bindings: {
								'conMenListSel_sel': function(t) {
									p.$w.find('.modal-footer button:first').click();
								}
							}
						});
						return $row;
					}
				});
			}
		});
	}
};

define(
	['mg/enti','ct/pcon','ad/dini','mg/ubig'],
	function(mgEnti,ctPcon,addini, Ubig){
		return adPaci;
	}
);