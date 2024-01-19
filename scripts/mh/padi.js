mhPadi = {
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
	estadoConsulta:{
		"1":"S/E",
		"2":"Nuevo",
		"3":"Continuador",
		"4":"Reingresante",
		"5":"Inter-Consulta",
	},
	categoria:{
		"10":"Nuevo",
		"11":"Continuador",
		"8":"Indigente",
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			num: item.num,
			doct: item.doct,
			fech: item.fech

			

			
		};
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhPadi',
			titleBar: {
				title: 'Parte Diario'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Numero de Parte','Doctor','Fecha de Parte','Ultima Modificacion'],
					data: 'mh/padi/lista',
					params: {modulo: 'MH'},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>'+
					'&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
						'</select>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhPadi.windowNew();
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
						$row.append('<td>'+data.num+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.medico)+'</td>');
						$row.append('<td>'+moment(data.fech.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"mh/padi/if_fron?_id="+$(tº).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenPDiario", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPDiario_info': function(t) {
									mhPadi.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenPDiario_edi': function(t) {
									mhPadi.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPDiario_cons': function(t) {
									mhPadi.windowConsultaMedica({id: K.tmp.data('id'),padi: K.tmp.data('data')});
								},
							
								'conMenPDiario_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Parte Diario:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/padi/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Parte Diario Eliminada',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhPadi.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Parte Diario');
								},
								'conMenFpadica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Parte Diario",
										url:"mh/padi/print?_id="+K.tmp.data('id')
									});
								},'conMenPDiario_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Parte Diario",
										url:"mh/padi/print?_id="+K.tmp.data('id')
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
			id: 'windowNewParteDiario',
			title: 'Nueva Parte Diario',
			contentURL: 'mh/padi/edit',
			width: 500,
			height: 450,
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
									num:p.$w.find('[name=num]').val(),
									medico: p.$w.find('[name=medico] [name=mini_enti]').data('data'),
									fech:p.$w.find('[name=fech]').val(),
									modulo:p.$w.find('[name=modulo]').val(),
								};
								if(data.medico==null){
									p.$w.find('[name=medico] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un medico!',
										type: 'error'
									});
								}else data.medico = mgEnti.dbRel(data.medico);
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/padi/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Parte Diario Agregado!"});
									K.closeWindow(p.$w.attr('id'));
									mhPadi.init();
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
				p.$w = $('#windowNewParteDiario');
				p.$w.find("[name=fech]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find('[name=fech]').val(ciHelper.date.get.now_ymd());

				p.$w.find('[name=medico] .panel-title').html('DATOS DEL MEDICO');
				p.$w.find('[name=medico] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.medico',value: {$exists: true}}
					]});
				});
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
				$.post("mh/padi/get_parte",function(data){

					var hist=0;
					if(data ==null){
						hist = 0;
					}else{
						hist = parseFloat(data.num) + 1;

					}
					p.$w.find('[name=num]').val(hist);
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditParteDiario',
			title: 'Editar Parte Diario de: ' + p.nom,
			contentURL: 'mh/padi/edit',
			width: 500,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL Parte Diario
							_id: p.id,
									num:p.$w.find('[name=num]').val(),
									doct:p.$w.find('[name=doct]').val(),
									modulo:p.$w.find('[name=modulo]').val(),
									fech:p.$w.find('[name=fech]').val(moment(data.fech.sec,'X').format('YYYY-MM-DD')),
									
						};
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.doct==''){
							p.$w.find('[name=doct]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.fech==''){
							p.$w.find('[name=fech]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/padi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Parte Diario Actualizado!"});
							mhPadi.init();
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
				p.$w = $('#windowEditParteDiario');
				K.block();
				p.$w.find("[name=fech]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find('[name=medico] .panel-title').html('DATOS DEL MEDICO');
				p.$w.find('[name=medico] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.medico',value: {$exists: true}}
					]});
				}).hide();
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
				$.post('mh/padi/get',{_id: p.id},function(data){
					p.$w.find('[name=num]').val(data.num);
					p.$w.find('[name=modulo]').val(data.modulo);
					//p.$w.find('[name=doct]').val(data.doct);
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.medico);
					p.$w.find('[name=fech]').val(moment(data.fech.sec,'X').format('YYYY-MM-DD'));
					K.unblock();
				},'json');
			}
		});
	},
	windowNew_: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNewParteDiario',
			title: 'Nueva Parte Diario',
			contentURL: 'mh/padi/edit',
			width: 500,
			height: 450,
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
									num:p.$w.find('[name=num]').val(),
									medico: p.$w.find('[name=medico] [name=mini_enti]').data('data'),
									fech:p.$w.find('[name=fech]').val(),
									modulo:p.$w.find('[name=modulo]').val(),
								};
								if(data.medico==null){
									p.$w.find('[name=medico] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un medico!',
										type: 'error'
									});
								}else data.medico = mgEnti.dbRel(data.medico);
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/padi/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Parte Diario Agregado!"});
									K.closeWindow(p.$w.attr('id'));
									mhPadi.init();
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
				p.$w = $('#windowNewParteDiario');
				p.$w.find("[name=fech]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find('[name=medico] .panel-title').html('DATOS DEL MEDICO');
				p.$w.find('[name=medico] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.medico',value: {$exists: true}}
					]});
				});
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
				$.post("mh/padi/get_parte",function(data){

					var hist=0;
					if(data ==null){
						hist = 0;
					}else{
						hist = parseFloat(data.num) + 1;

					}
					p.$w.find('[name=num]').val(hist);
				},'json');
			}
		});
	},
	windowEdit_: function(p){
		new K.Modal({ 
			id: 'windowEditParteDiario',
			title: 'Editar Parte Diario de: ' + p.nom,
			contentURL: 'mh/padi/edit',
			width: 500,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL Parte Diario
							_id: p.id,
									num:p.$w.find('[name=num]').val(),
									doct:p.$w.find('[name=doct]').val(),
									modulo:p.$w.find('[name=modulo]').val(),
									fech:p.$w.find('[name=fech]').val(moment(data.fech.sec,'X').format('YYYY-MM-DD')),
									
						};
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.doct==''){
							p.$w.find('[name=doct]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.fech==''){
							p.$w.find('[name=fech]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/padi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Parte Diario Actualizado!"});
							mhPadi.init();
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
				p.$w = $('#windowEditParteDiario');
				K.block();
				p.$w.find("[name=fech]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find('[name=medico] .panel-title').html('DATOS DEL MEDICO');
				p.$w.find('[name=medico] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.medico',value: {$exists: true}}
					]});
				}).hide();
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
				$.post('mh/padi/get',{_id: p.id},function(data){
					p.$w.find('[name=num]').val(data.num);
					p.$w.find('[name=modulo]').val(data.modulo);
					//p.$w.find('[name=doct]').val(data.doct);
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.medico);
					p.$w.find('[name=fech]').val(moment(data.fech.sec,'X').format('YYYY-MM-DD'));
					K.unblock();
				},'json');
			}
		});
	},
	windowConsultaMedica: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Nueva Consulta',
			contentURL: 'mh/cons/edit',
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
								var paciente = p.$w.find('[name=paciente]').data('data');
								
								if(paciente==null){
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un paciente!',
										type: 'error'
									});
								}
								
								var data = {
									_id:p.id,
									paciente: {
										_id: paciente._id.$id,
										his_cli: paciente.his_cli,
										paciente: mgEnti.dbRel(p.$w.find('[name=paciente]').data('data').paciente),
										sexo: paciente.sexo,
										edad: paciente.edad,
										proc: paciente.proc,
										modulo: paciente.modulo
									},
									
									esta:p.$w.find('[name=esta]').val(),
									cate:p.$w.find('[name=cate]').val(),
									cie10:p.$w.find('[name=cie10]').text(),
								};
								if(paciente!=null){
									data.paciente.fecha_na = paciente.fecha_na;
								}
								if(p.index!=null){
									data.index = p.index;
								}
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/padi/save_diario",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Consulta Agregada!"});
									mhPadi.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						mhPadi.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');

				p.$w.find('[name=medico] .panel-title').html('DATOS DEL MEDICO');
				p.$w.find('[name=medico] [name=btnSel]').hide();
				mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),p.padi.medico);
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
				p.$w.find('[name=part]').html(moment(p.padi.fech.sec,'X').format('YYYY-MM-DD'));
				p.$w.find('[name=btnSelPaci]').click(function(){
					mhPaci.windowSelect({callback:function(data){
						console.log(data);
						p.$w.find('[name=ap]').text(data.his_cli);
						p.$w.find('[name=modulo]').text(data.modulo);
						p.$w.find('[name=paciente]').text(mgEnti.formatName(data.paciente)).data('data',data);
						p.$w.find('[name=sexo]').text(data.sexo);
						p.$w.find('[name=edad]').text(data.edad);
						p.$w.find('[name=proce]').text(data.proce);
					}});
				});

				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=ap]').html(data.ape).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDiagini]").click(function(){
					mhDini.windowSelect({callback: function(data){
						p.$w.find('[name=cie10]').html(data.sigl).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDoct]").click(function(){
					mhPadi.windowSelect({callback: function(data){
						
						p.$w.find('[name=part]').html(moment(data.fech.sec,'X').format('YYYY-MM-DD')).data('data',data);
						p.$w.find('[name=doct]').html(data.doct).data('data',data);
					},bootstrap: true});
				});

				new K.grid({
					$el: p.$w.find('[name=gridList]'),
					search: false,
					data: 'mh/cons/lista',
					pagination: false,
					cols: ['','ID Parte','Historia Clinica','Paciente','Sexo','Edad','Estado','Diagnostico','Categoria','Proce'],
					onlyHtml:true,
					toolbarHTML: '<h3>Lista de Pacientes</h3>',
					onLoading: function(){ K.block(); },
					onComplete: function(){
					},
				});
				var nuevo = 0;
				var conti = 0;
				var reing = 0;
				var varon = 0;
				var mujer = 0;
				var total = 0;
				var pri = 0;
				var seg = 0;
				var ter = 0;
				var cuar = 0;
				K.block();
				$.post('mh/padi/get',{_id: p.id,all:true},function(data){
					p.padi = data;
					if(p.padi.consulta!=null){

					for(var i=0;i<p.padi.consulta.length;i++){
						//var edad = moment(p.padi.consulta[i].paciente.fecha_na.sec, "X").month(0).from(moment().month(0));
						var edad = -1;
						if(p.padi.consulta[i]._paciente!=null){
							if(p.padi.consulta[i]._paciente.fecha_na!=null){
								edad = moment().diff(moment(p.padi.consulta[i].paciente.fecha_na.sec,'X').format('YYYY-MM-DD'),'years');
							}	
						}
						var $row = $('<tr class="item" />');
						$row.append('<td><button class="btn btn-info"><i class="fa fa-edit"></i></button></td>');
						$row.append('<td>'+p.padi.num+'</td>');
						$row.append('<td>'+p.padi.consulta[i].paciente.his_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(p.padi.consulta[i].paciente.paciente)+'</td>');
						$row.append('<td>'+mhPaci.sexo[p.padi.consulta[i]._paciente.sexo]+'</td>');
						$row.append('<td>'+edad+'</td>');
						$row.append('<td>'+mhPadi.estadoConsulta[p.padi.consulta[i].esta]+'</td>');
						$row.append('<td>'+p.padi.consulta[i].cie10+'</td>');
						$row.append('<td>'+mhPaci.categoria[p.padi.consulta[i].cate]+'</td>');
						$row.append('<td>'+p.padi.consulta[i].paciente.proce+'</td>');
						//*******ELIMINAR******
						$row.append('<td><button class="btn btn-xs btn-danger" type="button" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
						$row.find('[name=btnEli]').click(function(){
							var $_row = $(this).closest('.item');
							ciHelper.confirm('&#191;Desea <b>Eliminar</b> esta consulta  medica?</b>&#63;',
							function(){
								K.sendingInfo();
								$.post('mh/padi/delete_consulta',{index:$_row.data('i'), _id: p.id}, function(rpta){
									$_row.remove();
								},'json')
							},function(){
								$.noop();
							},'Eliminací&oacute;n de Consulta');
							
						});
						//************************
						$row.find('button').eq(0).data('i',i).click(function(e){
							e.preventDefault();
							p.index = $(this).data('i');
							var $row = $(this).closest('.item');
							K.msg({text: 'Se editara el paciente '+$row.find('td:eq(3)').html()});
							var data = $row.data('data');
							p.$w.find('[name=ap]').text(data.paciente.his_cli);
							p.$w.find('[name=paciente]').text(mgEnti.formatName(data.paciente.paciente)).data('data',data.paciente);
							p.$w.find('[name=sexo]').text(data.paciente.sexo);
							p.$w.find('[name=edad]').text(data.paciente.edad);
							p.$w.find('[name=proce]').text(data.paciente.proce);
							p.$w.find('[name=modulo]').text(data.paciente.modulo);
							p.$w.find('[name=cate]').val(data.cate);
							p.$w.find('[name=esta]').val(data.esta);
							p.$w.find('[name=cie10]').html(data.cie10).data('data',data.cie10);
						});
						$row.data('data',p.padi.consulta[i]).data('i', i);
						p.$w.find('[name=gridList] tbody').append($row);
						if(p.padi.consulta[i].paciente.sexo =='1'){
							varon = varon + 1;
						}
						if(p.padi.consulta[i].paciente.sexo =='0'){
							mujer = mujer + 1;
						}
						if(p.padi.consulta[i].esta == '2'){
							nuevo = nuevo + 1 ;
						}
						if(p.padi.consulta[i].esta == '3'){
							conti = conti + 1 ;
						}
						if(p.padi.consulta[i].esta == '4'){
							reing = reing + 1 ;
						}
						if(parseFloat(edad) <= 10 ){
							pri = pri + 1 ;
						}
						if(parseFloat(edad) > 10 && parseFloat(edad) <= 17 ){
							seg = seg + 1 ;
						}
						if(parseFloat(edad) > 17 && parseFloat(edad) <= 60 ){
							ter = ter + 1 ;
						}
						if(parseFloat(edad) > 60 ){
							cuar = cuar + 1 ;
						}
						total = nuevo + conti+ reing;
					}
				}
				p.$w.find('[name=nuevo]').val(nuevo);
				p.$w.find('[name=conti]').val(conti);
				p.$w.find('[name=reing]').val(reing);
				p.$w.find('[name=total]').val(total);
				p.$w.find('[name=varon]').val(varon);
				p.$w.find('[name=mujer]').val(mujer);
				p.$w.find('[name=pri]').val(pri);
				p.$w.find('[name=seg]').val(seg);
				p.$w.find('[name=ter]').val(ter);
				p.$w.find('[name=cuar]').val(cuar);
				K.unblock();
				},'json');
			}
		});
	},

	windowEditConsultaMedica: function(p){
		new K.Modal({ 
			id: 'windowEditConsulta',
			title: 'Editar Consulta: ' + p.nom,
			contentURL: 'mh/cons/edit',
			width: 900,
			height: 900,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL Consulta
							_id: p.id,
							his:p.$w.find('[name=his]').text(),
							doct:p.$w.find('[name=doct]').text(),
							part:p.$w.find('[name=part]').text(),
							ap:p.$w.find('[name=ap]').text(),
							nom:p.$w.find('[name=nom]').text(),
							esta:p.$w.find('[name=esta]').val(),
							cate:p.$w.find('[name=cate]').val(),
							cie10:p.$w.find('[name=cie10]').text(),
							sexo:p.$w.find('[name=sexo]').text(),
							edad:p.$w.find('[name=edad]').text(),
							modulo:p.$w.find('[name=modulo]').text(),
							proce:p.$w.find('[name=proce]').text(),				
						};
						if(data.his==''){
							p.$w.find('[name=his]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.doct==''){
							p.$w.find('[name=doct]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.ap==''){
							p.$w.find('[name=ap]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.nom==''){
							p.$w.find('[name=nom]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.esta==''){
							p.$w.find('[name=esta]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cate==''){
							p.$w.find('[name=cate]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cie10==''){	
							p.$w.find('[name=cie10]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/cons/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Consulta Actualizada!"});
							mhCons.init();
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
				p.$w = $('#windowEditConsulta');
				K.block();
				
				     $.post('mh/cons/get',{_id: p.id},function(data){

									 p.$w.find('[name=modulo]').text(data.modulo),
									 p.$w.find('[name=his]').text(data.his),
									p.$w.find('[name=doct]').text(data.doct),
									p.$w.find('[name=part]').text(data.part),
									p.$w.find('[name=ap]').text(data.ap),
									p.$w.find('[name=nom]').text(data.nom),
									p.$w.find('[name=esta]').val(data.esta),
									p.$w.find('[name=cate]').val(data.cate),
									p.$w.find('[name=edad]').text(data.edad),
									p.$w.find('[name=sexo]').text(data.sexo),
									p.$w.find('[name=proce]').text(data.proce),

									


					K.unblock();
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Doctor',
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
					cols: ['','Doctor','Fecha'],
					data: 'mh/padi/lista',
					params: {},
					itemdescr: 'DOCTORES',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.doct+'</td>');
						$row.append('<td>'+moment(data.fech.sec,'X').format('YYYY-MM-DD'))+'</td>';
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
	['mh/paci','mh/dini','mh/cons'],
	function(mhpaci,mhdini,mhCons){
		return mhPadi;
	}
);