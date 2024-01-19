peTrab = {
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
	sexo: {
		F: "Femenino",
		M: 'Masculino'
	},
	tipo_superior: {
		T: "T&eacute;cnico",
		U: "Universitario",
		M: "Maestr&iacute;a",
		D: "Doctorado"
	},
	tipo: {
		N: 'Nombrado',
		C: 'Contratado',
		SN: 'Salud Nombrado',
		SC: 'Salud Contratado'
	},
	estado_civil: {
		SO: "Soltero",
		CA: "Casado",
		VI: "Viudo",
		DI: "Divorciado",
		SE: "Separado",
		CO: "Conviviente"
	},
	cese: {
		DS: 'Despido',
		RE: 'Renuncia',
		JU: 'Jubilaci&oacute;n',
		DF: 'Defunci&oacute;n'
	},
	modalidad: {
		CP: 'Convocatoria P&uacute;blica - CAS',
		SP: 'Sustituci&oacute;n de Contrato',
		DD: 'Designado Directamente',
		OT: 'Otros'
	},
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'pe',
			action: 'peTrab',
			titleBar: {
				title: 'Trabajadores'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Programa',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'pe/trab/lista',
					params: {type_fields: 'trab_lista'},
					itemdescr: 'trabajador(es)',
					toolbarHTML: '<select class="form-control col-sm-4" name="tipo"></select>&nbsp;'+
						'<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peTrab.windowNew({tipo: $el.find('[name=tipo] :selected').val()});
						});
						$el.find('[name=tipo]').change(function(){
							$grid.reinit({params: {
								tipo: $(this).val(),
								type_fields: 'trab_lista'
							}});
						});
						$.post('pe/cont/all',function(data){
							var $cbo = $el.find('[name=tipo]');
							for(var i=0; i<data.length; i++){
								$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
								$cbo.find('option:last').data('data',data[i]);
							}
							$cbo.change();
						},'json');
					},
					stopLoad: true,
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+peTrab.states[data.roles.trabajador.estado].label+'</td>');
						$row.append('<td>'+mgEnti.formatName(data)+'</td>');
						var programa = '--';
						if(data.roles.trabajador.programa!=null){
							programa = data.roles.trabajador.programa.nomb;
						}
						$row.append('<td>'+programa+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('tipo',data.roles.trabajador.contrato.cod).data('data',data).dblclick(function(){
							peTrab.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPeTrab", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peTrab.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeTrab_edi': function(t) {
									peTrab.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),tipo: K.tmp.data('data').roles.trabajador.contrato._id.$id});
									//peTrab.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeTrab_pad': function(t) {
									peTrab.windowTrab({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),tipo: K.tmp.data('data').roles.trabajador.contrato._id.$id});
								},
								'conMenPeTrab_ede': function(t){
									mgEnti.windowEdit({id: K.tmp.data('id')});
								},
								'conMenPeTrab_acf': function(t){
									peTrab.windowAct({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeTrab_des': function(t){
									peTrab.windowDes({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeTrab_veb': function(t){
									peTrab.windowDetailsBon({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeTrab_agr': function(t){
									peTrab.windowBon({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),tipo: K.tmp.data('tipo')});
								},
								'conMenPeTrab_iml': function(t) {
									var url = "pe/trab/print_legajo?id="+K.tmp.data('id');
									K.windowPrint({
										id:"windowPrintPeLegPrint",
										title:"Imprimir Legajo",
										url:url
									});
								},
								'conMenPeTrab_imf': function(t) {
									K.windowPrint({
										id:'windowPeFichaPrint',
										title: "Ficha del Trabajador",
										url: "pe/trab/print_ficha?id="+K.tmp.data('id')
									});
								},
								'conMenPeTrab_ret': function(t){
									peTrab.windowRetencion({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),tipo: K.tmp.data('tipo')});
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
			id: 'windowNew',
			title: 'Nuevo Trabajador',
			contentURL: 'pe/trab/edit',
			width: 720,
			height: 450,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							observ: p.$w.find('[name=observ]').val(),
							comision: p.$w.find('[name=comision] option:selected').val(),
							programa: {
								_id: p.$w.find('[name=programa] :selected').val(),
								nomb: p.$w.find('[name=programa] :selected').text(),
							}
						},
						flag_nivel = false,
						tmp = p.$w.find('[name=mini_enti]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnSelEnt]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar una entidad!',
								type: 'error'
							});
						}else{
							data._id = tmp._id.$id;
						}
						data.contrato = {
							_id: p.contrato._id.$id,
							nomb: p.contrato.nomb,
							cod: p.contrato.cod
						};
						tmp = p.$w.find('[name=oficina]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnSelOfi]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una oficina!',
								type: 'error'
							});
						}
						data.oficina = {
							_id: tmp._id.$id,
							nomb: tmp.nomb
						};
						tmp = p.$w.find('[name=actividad]').data('data');
						if(tmp==null){
							p.$W.find('[name=btnSelActi]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una Actividad!',
								type: 'error'
							});
						}
						data.actividad = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							cod: tmp.cod,
							actividad: tmp.actividad,
							nivel: tmp.nivel
						}
						for(var i=0,j=p.contrato.campos.length; i<j; i++){
							if(p.contrato.campos[i].name=="campo1"){
								data.ruc = p.$w.find('[name=ruc]:last').val();
								console.log('==>'+data.ruc);
								if(data.ruc==''){
									p.$w.find('[name=ruc]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un n&uacute;mero de RUC!',
										type: 'error'
									});
								}
							}else if(p.contrato.campos[i].name=="campo2"){
								tmp = p.$w.find('[name=cargo]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelCar]').click();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar un cargo!',
										type: 'error'
									});
								}else{
									data.cargo = {
										_id: tmp._id.$id,
										nomb: tmp.nomb
									};
								}
							}else if(p.contrato.campos[i].name=="campo3"){
								data.cargo = p.$w.find('[name=funcion]').val();
								if(data.cargo==''){
									p.$w.find('[name=funcion]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar la funci&oacute;n a desempe&ntilde;ar!',
										type: 'error'
									});
								}else{
									data.cargo = {
										funcion: data.cargo
									};
									tmp = p.$w.find('[name=orga_funcion]').data('data');
									if(tmp==null){
										/*p.$w.find('[name=btnSelOrgFunc]').click();
										return K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'Debe seleccionar una organizaci&oacute;n!',
											type: 'error'
										});*/
									}else{
										/*data.cargo.organizacion = {
											_id: tmp._id.$id,
											nomb: tmp.nomb,
											componente: {
												_id: tmp.componente._id.$id,
												nomb: tmp.componente.nomb,
												cod: tmp.componente.cod
											},
											actividad: {
												_id: tmp.actividad._id.$id,
												nomb: tmp.actividad.nomb,
												cod: tmp.actividad.cod
											}
										};*/
									}
								}
							}else if(p.contrato.campos[i].name=="campo4"){
								flag_nivel = true;
								tmp = p.$w.find('[name=nivel]').data('data');
								if(tmp!=null){
									data.nivel = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										abrev: tmp.abrev,
										salario: tmp.salario,
										basica: tmp.basica,
										reunificada: tmp.reunificada
									};
								}
							}else if(p.contrato.campos[i].name=="campo5"){
								flag_nivel = true;
								tmp = p.$w.find('[name=nivel2]').data('data');
								if(tmp!=null){
									data.nivel_carrera = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										abrev: tmp.abrev,
										salario: tmp.salario,
										basica: tmp.basica,
										reunificada: tmp.reunificada
									};
								}
							}else if(p.contrato.campos[i].name=="campo6"){
								data.salario = p.$w.find('[name=salario]').val();
								if(data.salario==''){
									p.$w.find('[name=salario]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un salario!',
										type: 'error'
									});
								}
							}else if(p.contrato.campos[i].name=="campo7"){
								data.modalidad = p.$w.find('[name=mod] option:selected').val();
							}else if(p.contrato.campos[i].name=="campo8"){
								tmp = p.$w.find('[name=descr]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelLoc]').click();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar un local!',
										type: 'error'
									});
								}else{
									data.local = {
										_id: tmp._id.$id,
										descr: tmp.descr,
										direccion: tmp.direccion
									};
								}
							}else if(p.contrato.campos[i].name=="campo9"){
								data.cod_tarjeta = p.$w.find('[name=tarjeta]').val();
							}else if(p.contrato.campos[i].name=="campo10"){
								tmp = p.$w.find('[name=turno]').data('data');
								if(tmp!=null){
									data.turno = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										tipo: tmp.tipo
									};
								}
							}else if(p.contrato.campos[i].name=="campo11"){
								tmp = p.$w.find('[name=clas]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelCla]').click();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cargo clasificado!',type: 'error'});
								}else{
									data.cargo_clasif = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										cod: tmp.cod
									};
								}
							}else if(p.contrato.campos[i].name=="campo12"){
								tmp = p.$w.find('[name=grup]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelGru]').click();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un grupo ocupacional!',type: 'error'});
								}else{
									data.grupo_ocup = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										sigla: tmp.sigla
									};
								}
							}else if(p.contrato.campos[i].name=="campo13"){
								data.essalud = p.$w.find('[name=essalud]').val();
							}else if(p.contrato.campos[i].name=="campo14"){
								var tmp = p.$w.find('[name=sist] option:selected').data('data');
								if(tmp!=null){
									data.pension = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										tipo: tmp.tipo,
										porcentajes: tmp.porcentajes
									};
									if(p.$w.find('[name=cod_apor]').val()!=''){
										data.cod_aportante = p.$w.find('[name=cod_apor]').val();
									}else{
										p.$w.find('[name=cod_apor]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de aportante!',type: 'error'});
									}
								}
							}else if(p.contrato.campos[i].name=="campo15"){
								data.tipo = p.$w.find('[name=rbtnTipTra] :selected').val();
							}else if(p.contrato.campos[i].name=="campo16"){
								data.eps = p.$w.find('[name=rbtnAfiEps] :selected').val();
							}
						}
						if(flag_nivel==true){
							if(data.nivel==null&&data.nivel_carrera==null){
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un nivel designado o uno de carrera!',
									type: 'error'
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("pe/trab/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Trabajador agregado correctamente!"});
							peTrab.init();
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
				p.$w = $('#windowNew');
				/*p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnSelNiv]').click(function(){
					peNive.windowSelect({callback: function(data){
						p.$w.find('[name=nivel]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name=btnSelNiv2]').click(function(){
					peNive.windowSelect({callback: function(data){
						p.$w.find('[name=nivel2]').html(data.nomb).data('data',data);
					}});
				});*/
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.trabajador',value: {$exists: false}}
					]});
				});
				p.$w.find('[name=btnAct]').click(function(){
					if(p.$w.find('[name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
						},id: p.$w.find('[name=mini_enti]').data('data')._id.$id});
					}
				});
				p.$w.find('[name=sist]').change(function(){
					var val = $(this).find('option:selected').html();
					console.log(val);
					if(val=='Sistema Nacional de Pensiones'){
						p.$w.find('[name=cod_apor]').val('--')
							.closest('.form-group').hide();
					}else{
						p.$w.find('[name=cod_apor]').val('')
							.closest('.form-group').show();
					}
				}).change();
				$.post('pe/trab/edit_new',function(data){
					for(var i=0,j=data.cont.length; i<j; i++){
						if(data.cont[i].cod==p.tipo)
							p.contrato = data.cont[i];
					}
					if(p.contrato.campos==null){
						K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El tipo de contrato seleccionado no tiene campos definidos!',
							type: 'error'
						});
						return K.closeWindow(p.$w.attr('id'));
					}
					p.$w.find('[name=btnSelEnt]').click(function(){
						ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbEnti,filter: [
						    {nomb: 'tipo_enti',value: 'P'},
						    {nomb: 'roles.trabajador',value: {$exists: false}}
						]});
					});
					p.$w.find('[name=btnSelOfi]').click(function(){
						require(['mg/ofic'],function(mgOfic){
							mgOfic.windowSelect({bootstrap:true,callback: function(data){
								p.$w.find('[name=oficina]').html(data.nomb)
									.data('data',data);
							}});
						});
					});
					/*p.$w.find('[name=btnSelActi]').click(function(){
						require(['pr/acti'],function(prActi){
							prActi.windowSelect({bootstrap:true,callback: function(data){
								p.$w.find('[name=actividad]').html(data.nomb)
									.data('data',data);
							}});
						});
					});*/
					
					p.$w.find('[name=btnSelActi]').click(function(){
						prActi.windowSelect({callback: function(data){
							p.$w.find('[name=actividad]').html(data.nomb).data('data',data);
						},bootstrap: true});
					});


					p.$w.find('[name=btnAgrEnt]').click(function(){
						ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbEnti,reqs: {tipo_enti: 'P'}});
					});
					p.$w.find('[name^=campo]').hide();
					console.info('----------');
					console.log(p.contrato);
					p.$w.find('fieldset:eq(3) td:eq(2)').html(p.contrato.cod);
					for(var i=0,j=p.contrato.campos.length; i<j; i++){
						if(p.contrato.campos[i].name=="campo1"){
							p.$w.find('[name=campo1]').show();
						}else if(p.contrato.campos[i].name=="campo2"){
							p.$w.find('[name=campo2]').show();
							p.$w.find('[name=btnSelCar]').click(function(){
								peCarg.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=cargo]').html(data.nomb).data('data',data);
									p.$w.find('[name=orga]').html(data.organizacion.nomb);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo3"){
							p.$w.find('[name=campo2]').show();
							var $tab = p.$w.find('[name=campo2] table').empty();
							$tab.append('<tr>');
							$tab.find('tr:last').append('<td><label>Funci&oacute;n a desempe&ntilde;ar</label></td>');
							$tab.find('tr:last').append('<td><input type="text" name="funcion" size="50"></td>');
							$tab.append('<tr>');
							$tab.find('tr:last').append('<td><label>Organizaci&oacute;n</label></td>');
							$tab.find('tr:last').append('<td><span name="orga_funcion"></span>&nbsp;<button name="btnSelOrgFunc">Seleccionar</button></td>');
							p.$w.find('[name=btnSelOrgFunc]').click(function(){
								ciSearch.windowSearchOrga({callback: function(data){
									p.$w.find('[name=orga_funcion]').html(data.nomb).data('data',data);
									p.$w.find('[name=btnSelOrgFunc]').button('option','text',false);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo4"){
							p.$w.find('[name=campo4]').show();
							p.$w.find('[name=btnSelNiv]').click(function(){
								peNive.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=nivel]').html(data.nomb).data('data',data);
								}});
							});
							//p.$w.find('[name=btnSelNiv]').after('<button name="btnSelNiv_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv_clean]').click(function(){
								p.$w.find('[name=nivel]').html('').removeData('data');
							});
						}else if(p.contrato.campos[i].name=="campo5"){
							p.$w.find('[name=campo5]').show();
							p.$w.find('[name=btnSelNiv2]').click(function(){
								peNive.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=nivel2]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							//p.$w.find('[name=btnSelNiv2]').after('<button name="btnSelNiv2_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv2_clean]').click(function(){
								p.$w.find('[name=nivel2]').html('').removeData('data');
							});
						}else if(p.contrato.campos[i].name=="campo6"){
							p.$w.find('[name=campo6]').show();
							//p.$w.find('[name=salario]').numeric().spinner({step: 0.1,min: 0});
							p.$w.find('[name=salario]').parent().find('.ui-button').css('height','14px');
						}else if(p.contrato.campos[i].name=="campo7"){
							p.$w.find('[name=campo7]').show();
						}else if(p.contrato.campos[i].name=="campo8"){
							p.$w.find('[name=campo8]').show();
							p.$w.find('[name=btnSelLoc]').click(function(){
								mgTitu.windowSelectLocal({bootstrap:true,callback: function(data){
									p.$w.find('[name=descr]').html(data.descr).data('data',data);
									p.$w.find('[name=direccion]').html(data.direccion);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo9"){
							p.$w.find('[name=campo9]').show();
						}else if(p.contrato.campos[i].name=="campo10"){
							p.$w.find('[name=campo10]').show();
							p.$w.find('[name=btnSelTur]').click(function(){
								peTurn.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=turno]').html(data.nomb).data('data',data);
								}});
							});
							//p.$w.find('[name=btnSelTur]').after('<button name="btnSelTur_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelTur_clean]').click(function(){
								p.$w.find('[name=turno]').html('').removeData('data');
							});
						}else if(p.contrato.campos[i].name=="campo11"){
							p.$w.find('[name=campo11]').show();
							p.$w.find('[name=btnSelCla]').click(function(){
								peClas.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=clas]').html(data.nomb).data('data',data);
									p.$w.find('[name=codclas]').html(data.cod);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo12"){
							p.$w.find('[name=campo12]').show();
							p.$w.find('[name=btnSelGru]').click(function(){
								peGrup.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=grup]').html(data.nomb).data('data',data);
									p.$w.find('[name=codgrup]').html(data.sigla);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo13"){
							p.$w.find('[name=campo13]').show();
							
						}else if(p.contrato.campos[i].name=="campo14"){
							p.$w.find('[name=campo14]').show();
							if(data.pension==null){
								K.closeWindow(p.$w.attr('id'));
								return K.notification({
									title: 'Sistemas de Pensi&oacute;n no especificados',
									text: 'Debe de ingresar al menos un Sistema de Pensi&oacute;n para poder crear una ficha de trabajador!',
									type: 'info'
								});
							}
							var $sel = p.$w.find('[name=sist]');
							$sel.append('<option>--</option>');
							if(data.pension!=null){
								for(var k=0; k<data.pension.length; k++){
									$sel.append('<option value="'+data.pension[k]._id.$id+'">'+data.pension[k].nomb+'</option>');
									$sel.find('option:last').data('data',data.pension[k]);
								}
							}
						}else if(p.contrato.campos[i].name=="campo15"){
							p.$w.find('[name=campo15]').show();
							//p.$w.find('[name=tipotrab]').buttonset();
						}else if(p.contrato.campos[i].name=="campo16"){
							p.$w.find('[name=campo16]').show();
							//p.$w.find('[name=afilia]').buttonset();
						}
					}
					$.post('mg/prog/all',function(prog){
						var $cbo = p.$w.find('[name=programa]');
						if(prog!=null){
							for(var i in prog){
								$cbo.append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>');
								$cbo.find('option:last').data('data',prog[i]);
							}
						}
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo: '+p.nomb,
			contentURL: 'pe/trab/edit',
			width: 720,
			height: 450,
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							observ: p.$w.find('[name=observ]').val(),
							comision: p.$w.find('[name=comision] option:selected').val(),
							contrato: {
								_id: p.contrato._id.$id,
								nomb: p.contrato.nomb,
								cod: p.contrato.cod
							},
							programa: {
								_id: p.$w.find('[name=programa] :selected').val(),
								nomb: p.$w.find('[name=programa] :selected').text(),
							}
						},
						flag_nivel = false;
						tmp = p.$w.find('[name=oficina]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnSelOfi]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una oficina!',
								type: 'error'
							});
						}
						data.oficina = {
							_id: tmp._id.$id,
							nomb: tmp.nomb
						};
						tmp = p.$w.find('[name=actividad]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnSelActi]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una Actividad!',
								type: 'error'
							});
						}else{
							console.log(tmp);
							data.actividad = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								cod: tmp.cod,
								actividad: tmp.actividad,
								nivel: tmp.nivel
							
						}
						} 
						
						for(var i=0,j=p.contrato.campos.length; i<j; i++){
							if(p.contrato.campos[i].name=="campo1"){
								data.ruc = p.$w.find('[name=ruc]:last').val();
								if(data.ruc==''){
									p.$w.find('[name=ruc]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un n&uacute;mero de RUC!',
										type: 'error'
									});
								}
							}else if(p.contrato.campos[i].name=="campo2"){
								tmp = p.$w.find('[name=cargo]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelCar]').click();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar un cargo!',
										type: 'error'
									});
								}else{
									data.cargo = {
										_id: tmp._id.$id,
										nomb: tmp.nomb
									};
								}
							}else if(p.contrato.campos[i].name=="campo3"){
								data.cargo = p.$w.find('[name=funcion]').val();
								if(data.cargo==''){
									p.$w.find('[name=funcion]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar la funci&oacute;n a desempe&ntilde;ar!',
										type: 'error'
									});
								}else{
									data.cargo = {
										funcion: data.cargo
									};
									/*tmp = p.$w.find('[name=orga_funcion]').data('data');
									if(tmp==null){
										p.$w.find('[name=btnSelOrgFunc]').click();
										return K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'Debe seleccionar una organizaci&oacute;n!',
											type: 'error'
										});
									}else{
										data.cargo.organizacion = {
											_id: tmp._id.$id,
											nomb: tmp.nomb,
											componente: {
												_id: tmp.componente._id.$id,
												nomb: tmp.componente.nomb,
												cod: tmp.componente.cod
											},
											actividad: {
												_id: tmp.actividad._id.$id,
												nomb: tmp.actividad.nomb,
												cod: tmp.actividad.cod
											}
										};
									}*/
								}
							}else if(p.contrato.campos[i].name=="campo4"){
								flag_nivel = true;
								tmp = p.$w.find('[name=nivel]').data('data');
								if(tmp!=null){
									data.nivel = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										abrev: tmp.abrev,
										salario: tmp.salario,
										basica: tmp.basica,
										reunificada: tmp.reunificada
									};
								}
							}else if(p.contrato.campos[i].name=="campo5"){
								flag_nivel = true;
								tmp = p.$w.find('[name=nivel2]').data('data');
								if(tmp!=null){
									data.nivel_carrera = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										abrev: tmp.abrev,
										salario: tmp.salario,
										basica: tmp.basica,
										reunificada: tmp.reunificada
									};
								}
							}else if(p.contrato.campos[i].name=="campo6"){
								data.salario = p.$w.find('[name=salario]').val();
								if(data.salario==''){
									p.$w.find('[name=salario]').focus();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un salario!',
										type: 'error'
									});
								}
							}else if(p.contrato.campos[i].name=="campo7"){
								data.modalidad = p.$w.find('[name=mod] option:selected').val();
							}else if(p.contrato.campos[i].name=="campo8"){
								tmp = p.$w.find('[name=descr]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelLoc]').click();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar un local!',
										type: 'error'
									});
								}else{
									data.local = {
										_id: tmp._id.$id,
										descr: tmp.descr,
										direccion: tmp.direccion
									};
								}
							}else if(p.contrato.campos[i].name=="campo9"){
								data.cod_tarjeta = p.$w.find('[name=tarjeta]').val();
							}else if(p.contrato.campos[i].name=="campo10"){
								tmp = p.$w.find('[name=turno]').data('data');
								if(tmp!=null){
									data.turno = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										tipo: tmp.tipo
									};
								}
							}else if(p.contrato.campos[i].name=="campo11"){
								tmp = p.$w.find('[name=clas]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelCla]').click();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cargo clasificado!',type: 'error'});
								}else{
									data.cargo_clasif = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										cod: tmp.cod
									};
								}
							}else if(p.contrato.campos[i].name=="campo12"){
								tmp = p.$w.find('[name=grup]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelGru]').click();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un grupo ocupacional!',type: 'error'});
								}else{
									data.grupo_ocup = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										sigla: tmp.sigla
									};
								}
							}else if(p.contrato.campos[i].name=="campo13"){
								data.essalud = p.$w.find('[name=essalud]').val();
							}else if(p.contrato.campos[i].name=="campo14"){
								var tmp = p.$w.find('[name=sist] option:selected').data('data');
								if(tmp!=null){
									data.pension = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										tipo: tmp.tipo,
										porcentajes: tmp.porcentajes
									};
									if(p.$w.find('[name=cod_apor]').val()!=''){
										data.cod_aportante = p.$w.find('[name=cod_apor]').val();
									}/*else{
										p.$w.find('[name=cod_apor]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de aportante!',type: 'error'});
									}*/
								}
							}else if(p.contrato.campos[i].name=="campo15"){
								data.tipo = p.$w.find('[name=rbtnTipTra] :selected').val();
							}else if(p.contrato.campos[i].name=="campo16"){
								data.eps = p.$w.find('[name=rbtnAfiEps] :selected').val();
							}
						}
						if(flag_nivel==true){
							if(data.nivel==null&&data.nivel_carrera==null){
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un nivel designado o uno de carrera!',
									type: 'error'
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("pe/trab/save_edit",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Tipo actualizado!"});
							peTrab.init();
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
				p.$w = $('#windowEditTipo');
				K.block();
				p.$w.find('[name=btnAct]').remove();
				p.$w.find('[name=btnSel]').remove();
				p.$w.find('[name=sist]').change(function(){
					var val = $(this).find('option:selected').html();
					console.log(val);
					if(val=='Sistema Nacional de Pensiones'){
						p.$w.find('[name=cod_apor]').val('--')
							.closest('.form-group').hide();
					}else{
						p.$w.find('[name=cod_apor]').val('')
							.closest('.form-group').show();
					}
				}).change();
				$.post('pe/trab/edit_new',function(data){
					for(var i=0,j=data.cont.length; i<j; i++){
						if(data.cont[i]._id.$id==p.tipo)
							p.contrato = data.cont[i];
					}
					if(p.contrato.campos==null){
						K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El tipo de contrato seleccionado no tiene campos definidos!',
							type: 'error'
						});
						return K.closeWindow(p.$w.attr('id'));
					}
					p.$w.find('[name=btnSelEnt]').click(function(){
						ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbEnti,filter: [
						    {nomb: 'tipo_enti',value: 'P'},
						    {nomb: 'roles.trabajador',value: {$exists: false}}
						]});
					});
					p.$w.find('[name=btnSelOfi]').click(function(){
						require(['mg/ofic'],function(mgOfic){
							mgOfic.windowSelect({bootstrap:true,callback: function(data){
								p.$w.find('[name=oficina]').html(data.nomb)
									.data('data',data);
							}});
						});
					});
					p.$w.find('[name=btnSelActi]').click(function(){
						prActi.windowSelect({callback: function(data){
							p.$w.find('[name=actividad]').html(data.nomb).data('data',data);
						},bootstrap: true});
					});
					p.$w.find('[name=btnAgrEnt]').click(function(){
						ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbEnti,reqs: {tipo_enti: 'P'}});
					});
					p.$w.find('[name^=campo]').hide();
					p.$w.find('fieldset:eq(3) td:eq(2)').html(p.contrato.cod);
					for(var i=0,j=p.contrato.campos.length; i<j; i++){
						if(p.contrato.campos[i].name=="campo1"){
							p.$w.find('[name=campo1]').show();
						}else if(p.contrato.campos[i].name=="campo2"){
							p.$w.find('[name=campo2]').show();
							p.$w.find('[name=btnSelCar]').click(function(){
								peCarg.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=cargo]').html(data.nomb).data('data',data);
									p.$w.find('[name=orga]').html(data.organizacion.nomb);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo3"){
							p.$w.find('[name=campo2]').show();
							var $tab = p.$w.find('[name=campo2]').empty();
							//$tab.append('<tr>');
							$tab.append('<label>Funci&oacute;n a desempe&ntilde;ar</label>');
							$tab.append('<input type="text" class="form-control" name="funcion" size="50">');
						}else if(p.contrato.campos[i].name=="campo4"){
							p.$w.find('[name=campo4]').show();
							p.$w.find('[name=btnSelNiv]').click(function(){
								peNive.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=nivel]').html(data.nomb).data('data',data);
								}});
							});
							//p.$w.find('[name=btnSelNiv]').after('<button name="btnSelNiv_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv_clean]').click(function(){
								p.$w.find('[name=nivel]').html('').removeData('data');
							});
						}else if(p.contrato.campos[i].name=="campo5"){
							p.$w.find('[name=campo5]').show();
							p.$w.find('[name=btnSelNiv2]').click(function(){
								peNive.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=nivel2]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							//p.$w.find('[name=btnSelNiv2]').after('<button name="btnSelNiv2_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv2_clean]').click(function(){
								p.$w.find('[name=nivel2]').html('').removeData('data');
							});
						}else if(p.contrato.campos[i].name=="campo6"){
							p.$w.find('[name=campo6]').show();
							//p.$w.find('[name=salario]').numeric().spinner({step: 0.1,min: 0});
							p.$w.find('[name=salario]').parent().find('.ui-button').css('height','14px');
						}else if(p.contrato.campos[i].name=="campo7"){
							p.$w.find('[name=campo7]').show();
						}else if(p.contrato.campos[i].name=="campo8"){
							p.$w.find('[name=campo8]').show();
							p.$w.find('[name=btnSelLoc]').click(function(){
								mgTitu.windowSelectLocal({bootstrap:true,callback: function(data){
									p.$w.find('[name=descr]').html(data.descr).data('data',data);
									p.$w.find('[name=direccion]').html(data.direccion);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo9"){
							p.$w.find('[name=campo9]').show();
						}else if(p.contrato.campos[i].name=="campo10"){
							p.$w.find('[name=campo10]').show();
							p.$w.find('[name=btnSelTur]').click(function(){
								peTurn.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=turno]').html(data.nomb).data('data',data);
								}});
							});
							//p.$w.find('[name=btnSelTur]').after('<button name="btnSelTur_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelTur_clean]').click(function(){
								p.$w.find('[name=turno]').html('').removeData('data');
							});
						}else if(p.contrato.campos[i].name=="campo11"){
							p.$w.find('[name=campo11]').show();
							p.$w.find('[name=btnSelCla]').click(function(){
								peClas.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=clas]').html(data.nomb).data('data',data);
									p.$w.find('[name=codclas]').html(data.cod);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo12"){
							p.$w.find('[name=campo12]').show();
							p.$w.find('[name=btnSelGru]').click(function(){
								peGrup.windowSelect({bootstrap:true,callback: function(data){
									p.$w.find('[name=grup]').html(data.nomb).data('data',data);
									p.$w.find('[name=codgrup]').html(data.sigla);
								}});
							});
						}else if(p.contrato.campos[i].name=="campo13"){
							p.$w.find('[name=campo13]').show();
							
						}else if(p.contrato.campos[i].name=="campo14"){
							p.$w.find('[name=campo14]').show();
							if(data.pension==null){
								K.closeWindow(p.$w.attr('id'));
								return K.notification({
									title: 'Sistemas de Pensi&oacute;n no especificados',
									text: 'Debe de ingresar al menos un Sistema de Pensi&oacute;n para poder crear una ficha de trabajador!',
									type: 'info'
								});
							}
							var $sel = p.$w.find('[name=sist]');
							$sel.append('<option>--</option>');
							if(data.pension!=null){
								for(var k=0; k<data.pension.length; k++){
									$sel.append('<option value="'+data.pension[k]._id.$id+'">'+data.pension[k].nomb+'</option>');
									$sel.find('option:last').data('data',data.pension[k]);
								}
							}
						}else if(p.contrato.campos[i].name=="campo15"){
							p.$w.find('[name=campo15]').show();
							//p.$w.find('[name=tipotrab]').buttonset();
						}else if(p.contrato.campos[i].name=="campo16"){
							p.$w.find('[name=campo16]').show();
							//p.$w.find('[name=afilia]').buttonset();
						}
					}
					$.post('mg/enti/get','_id='+p.id,function(data){
						p.data = data;
						if(data.roles.trabajador.oficina!=null){
							p.$w.find('[name=oficina]').html(data.roles.trabajador.oficina.nomb)
								.data('data',data.roles.trabajador.oficina);
						}
						if(data.roles.trabajador.actividad!=null){
							p.$w.find('[name=actividad]').html(data.roles.trabajador.actividad.nomb)
								.data('data',data.roles.trabajador.actividad);
						}
						p.$w.find('[name=regimen]').html(data.roles.trabajador.contrato.cod);
						if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
						else p.$w.find('[name=foto]').removeAttr('src');
						p.$w.find('[name=nomb]').data('data',data)
						.html(ciHelper.enti.formatName(data)).attr('title',ciHelper.enti.formatName(data)).tooltip();
						p.$w.find('[name=docident]').html( data.docident[0].num );
						if(data.domicilios!=null) p.$w.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
						else p.$w.find('[name=direc]').html('--');
						if(data.telefonos!=null) p.$w.find('[name=telf]').html( data.telefonos[0].num );
						else p.$w.find('[name=telf]').html('--');

						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);

						if(data.roles.trabajador.cargo!=null){
							if(data.roles.trabajador.cargo._id!=null){
								p.$w.find('[name=cargo]').html(data.roles.trabajador.cargo.nomb).data('data',data.roles.trabajador.cargo);
								if(data.roles.trabajador.cargo.organizacion!=null){
									p.$w.find('[name=orga]').html(data.roles.trabajador.cargo.organizacion.nomb);
								}
							}
							if(data.roles.trabajador.cargo._id==null){
								p.$w.find('[name=funcion]').val(data.roles.trabajador.cargo.funcion);
								if(data.roles.trabajador.cargo.organizacion!=null){
									p.$w.find('[name=orga_funcion]').html(data.roles.trabajador.cargo.organizacion.nomb).data('data',data.roles.trabajador.cargo.organizacion);
								}
							}
						}
						if(data.roles.trabajador.nivel!=null)
							p.$w.find('[name=nivel]').html(data.roles.trabajador.nivel.nomb).data('data',data.roles.trabajador.nivel);
						if(data.roles.trabajador.nivel_carrera!=null)
							p.$w.find('[name=nivel2]').html(data.roles.trabajador.nivel_carrera.nomb).data('data',data.roles.trabajador.nivel_carrera);
						for(var i=0,j=data.docident.length; i<j; i++){
							if(data.docident[i].tipo=='RUC'){
								p.$w.find('[name=ruc]').val(data.docident[i].num);
								//i++;
							}
						}
						if(data.roles.trabajador.salario!=null)
							p.$w.find('[name=salario]').val(data.roles.trabajador.salario);
						if(data.roles.trabajador.local!=null){
							p.$w.find('[name=descr]').html(data.roles.trabajador.local.descr).data('data',data.roles.trabajador.local);
							p.$w.find('[name=direccion]').html(data.roles.trabajador.local.direccion);
						}
						if(data.roles.trabajador.cod_tarjeta!=null)
							p.$w.find('[name=tarjeta]').val(data.roles.trabajador.cod_tarjeta);
						if(data.roles.trabajador.turno!=null)
							p.$w.find('[name=turno]').html(data.roles.trabajador.turno.nomb).data('data',data.roles.trabajador.turno);
						if(data.roles.trabajador.cargo_clasif!=null){
							p.$w.find('[name=clas]').html(data.roles.trabajador.cargo_clasif.nomb).data('data',data.roles.trabajador.cargo_clasif);
							p.$w.find('[name=codclas]').html(data.roles.trabajador.cargo_clasif.cod);
						}
						if(data.roles.trabajador.grupo_ocup!=null){
							p.$w.find('[name=grup]').html(data.roles.trabajador.grupo_ocup.nomb).data('data',data.roles.trabajador.grupo_ocup);
							p.$w.find('[name=codgrup]').html(data.roles.trabajador.grupo_ocup.sigla);
						}
						if(data.roles.trabajador.essalud!=null)
							p.$w.find('[name=essalud]').val(data.roles.trabajador.essalud);
						if(data.roles.trabajador.modalidad!=null)
							p.$w.find('[name=mod]').selectVal(data.roles.trabajador.modalidad);
						if(data.roles.trabajador.pension!=null){
							p.$w.find('[name=sist]').val(data.roles.trabajador.pension._id.$id);
							p.$w.find('[name=cod_apor]').val(data.roles.trabajador.cod_aportante);
						}
						if(data.roles.trabajador.tipo!=null){
							p.$w.find('[name=rbtnTipTra]').val([data.roles.trabajador.tipo]);
						}
						if(data.roles.trabajador.eps!=null){
							if(data.roles.trabajador.eps){
								p.$w.find('[name=rbtnAfiEps]').val(["1"]);
							}else{
								p.$w.find('[name=rbtnAfiEps]').val(["0"]);
							}
						}
						if(data.roles.trabajador.observ!=null)
							p.$w.find('[name=observ]').val(data.roles.trabajador.observ);
						if(data.roles.trabajador.comision!=null)
							p.$w.find('[name=comision]').selectVal(data.roles.trabajador.comision);
						if(data.roles.trabajador.programa!=null)
							p.$w.find('[name=programa]').selectVal(data.roles.trabajador.programa);
						K.unblock({$element: p.$w});
						$.post('mg/prog/all',function(prog){
							var $cbo = p.$w.find('[name=programa]');
							if(prog!=null){
								for(var i in prog){
									$cbo.append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>');
									$cbo.find('option:last').data('data',prog[i]);
								}
							}
							if(data.roles.trabajador.programa!=null){
								p.$w.find('[name=programa]').val(data.roles.trabajador.programa._id.$id);
							}
							K.unblock({$element: p.$w});
						},'json');
					},'json');
					//K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowTrab: function(p){
		new K.Panel({ 
			title: 'Ingresar Padres de: ' + p.nom,
			contentURL: 'pe/trab/pad',
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
									padre: p.$w.find('[name=padre] [name=mini_enti]').data('data'),
									madre: p.$w.find('[name=madre] [name=mini_enti]').data('data'),
								};
								if(data.padre==null){
									p.$w.find('[name=padre] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar una entidad!',
										type: 'error'
									});
								}else data.padre = mgEnti.dbRel(data.padre);
								if(data.madre==null){
									p.$w.find('[name=madre] [name=btnSel]').click();
									return K.msg({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar una entidad!',
										type: 'error'
									});
								}else data.madre = mgEnti.dbRel(data.madre);
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("pe/trab/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Padres agregados a Trabajador!"});
									peTrab.init();
								},'json');	
							}
						}).submit();
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						peTrab.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				
				p.$w.find('[name=madre] .panel-title').html('DATOS DE LA MADRE');
				p.$w.find('[name=madre] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=madre] [name=mini_enti]'),data);
					},bootstrap: true});
				});
				p.$w.find('[name=madre] [name=btnAct]').click(function(){
					if(p.$w.find('[name=madre] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=madre] [name=mini_enti]'),data);
						},id: p.$w.find('[name=madre] [name=mini_enti]').data('data')._id.$id});
					}
				});

				p.$w.find('[name=padre] .panel-title').html('DATOS DEL PADRE');
				p.$w.find('[name=padre] [name=btnAct]').click(function(){
					if(p.$w.find('[name=padre] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=padre] [name=mini_enti]'),data);
						},id: p.$w.find('[name=padre] [name=mini_enti]').data('data')._id.$id});
					}
				});
				$.post('pe/trab/get',{_id: p.id},function(data){
			  	    //mgEnti.fillMini(p.$w.find('[name=padre] [name=mini_enti]'),data.padre);
					//OTROS DATOS
					//INFORMACION madre
					p.$w.find('[name=madre] .panel-title').html('DATOS DE LA MADRE');
					p.$w.find('[name=madre] [name=btnSel]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=madre] [name=mini_enti]'),data);
						},bootstrap: true});
					});
					p.$w.find('[name=madre] [name=btnAct]').click(function(){
						if(p.$w.find('[name=madre] [name=mini_enti]').data('data')==null){
							K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe elegir una entidad!',
								type: 'error'
							});
						}else{
							mgEnti.windowEdit({callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=madre] [name=mini_enti]'),data);
							},id: p.$w.find('[name=madre] [name=mini_enti]').data('data')._id.$id});
						}
					});
					mgEnti.fillMini(p.$w.find('[name=madre] [name=mini_enti]'),data.madre);
					//p.$w.find('[name=padre] [name=btnSel]').hide();
					//PADRE
					//INFORMACION madre
					p.$w.find('[name=padre] .panel-title').html('DATOS DEL PADRE');
					p.$w.find('[name=padre] [name=btnSel]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=padre] [name=mini_enti]'),data);
						},bootstrap: true});
					});
					p.$w.find('[name=padre] [name=btnAct]').click(function(){
						if(p.$w.find('[name=padre] [name=mini_enti]').data('data')==null){
							K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe elegir una entidad!',
								type: 'error'
							});
						}else{
							mgEnti.windowEdit({callback: function(data){
								mgEnti.fillMini(p.$w.find('[name=padre] [name=mini_enti]'),data);
							},id: p.$w.find('[name=padre] [name=mini_enti]').data('data')._id.$id});
						}
					});
					mgEnti.fillMini(p.$w.find('[name=padre] [name=mini_enti]'),data.padre);
					
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
			title: 'Seleccionar Tipo de Local',
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
					cols: ['','Nombre'],
					data: 'pe/trab/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
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
	},
	windowAct: function(p){
		new K.Window({
			id: 'windowActTrab'+p.id,
			title: 'Actualizar ficha de trabajador: '+p.nomb,
			contentURL: 'pe/trab/ficha',
			icon: 'ui-icon-gear',
			width: 655,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						entidad: {
							_id: p.enti._id.$id,
							tipo_enti: p.enti.tipo_enti,
							nomb: p.enti.nomb,
							appat: p.enti.appat,
							apmat: p.enti.apmat
						}
					};
					if(p.$w.find('[name=sexo]').val()!='') data.sexo = p.$w.find('[name=sexo]').val();
					if(p.$w.find('[name=estado_civil]').val()!='') data.estado_civil = p.$w.find('[name=estado_civil]').val();
					if(p.$w.find('[name=subsidio]').val()!='') data.subsidio = p.$w.find('[name=subsidio]').val();
					if(p.$w.find('[name=grupo]').val()!='') data.sangre = p.$w.find('[name=grupo]').val();
					if(p.$w.find('[name=fecingadm]').val()!='') data.fec_adm_pub = p.$w.find('[name=fecingadm]').val();
					if(p.$w.find('[name=fecingben]').val()!='') data.fec_adm_sbpa = p.$w.find('[name=fecingben]').val();
					if(p.$w.find('[name=fecnac]').val()!='') data.fecnac = p.$w.find('[name=fecnac]').val();


					if(p.ficha!=null) data._id = p.ficha._id.$id;
					K.sendingInfo();
					$.post('pe/trab/save_ficha',data,function(){
						K.clearNoti();
						K.msg({
							title: 'Se ha actualizado correctamente la ficha del trabajador',
							text: 'Debe seleccionar un item!',
							type: 'error'
						});
						K.closeWindow(p.$w.attr('id'));
						peTrab.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowActTrab'+p.id);
				K.block({$element: p.$w});
				p.$w.find("[name=fecingben]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find("[name=fecingadm]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find("[name=fecnac]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
				p.$w.find('[name=fecingadm]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				p.$w.find('[name=fecingben]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				p.$w.find('[name=fecnac]').attr('placeholder','Ejem: 1980-05-09').datepicker();

				$.post('pe/trab/edit_ficha','id='+p.id,function(data){
					
					p.enti = data.enti;
					if(p.enti.subsidio!=null) p.$w.find('[name=subsidio]').val(p.enti.subsidio);
					if(data.ficha!=null){
						p.ficha = data.ficha;
						if(p.ficha.fec_adm_pub!=null) p.$w.find('[name=fecingadm]').val(moment(p.ficha.fec_adm_pub.sec,"X").format('YYYY-MM-DD'));
						if(p.ficha.fec_adm_sbpa!=null) p.$w.find('[name=fecingben]').val(moment(p.ficha.fec_adm_sbpa.sec,"X").format('YYYY-MM-DD'));
						if(p.ficha.fecnac!=null) p.$w.find('[name=fecnac]').val(moment(p.ficha.fecnac.sec,"X").format('YYYY-MM-DD'));
						if(p.enti.sangre!=null) p.$w.find('[name=grupo]').val(p.enti.sangre);
						if(p.enti.sexo!=null) p.$w.find('[name=sexo]').val(p.enti.sexo);
						if(p.enti.estado_civil!=null) p.$w.find('[name=estado_civil]').val(p.enti.estado_civil);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDes: function(p){
		new K.Modal({
			id: 'windowTrabDes'+p.id,
			title: 'Deshabilitar: '+p.nomb,
			contentURL: 'pe/trab/des',
			icon: 'ui-icon-circle-close',
			width: 350,
			height: 250,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: K.tmp.data('id'),
							estado: 'D',
							cese: {
								fec: p.$w.find('[name=fec]').val(),
								motivo: p.$w.find('[name=motivo] option:selected').val(),
								observ: p.$w.find('[name=observ]').val()
							}
						};
						K.sendingInfo();
						$.post('pe/trab/upd',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.msg({
								title: 'Se ha deshabilitado correctamente al trabajador',
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
							peTrab.init();
						});
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
			onContentLoaded: function(){
				p.$w = $('#windowTrabDes'+p.id);
				p.$w.find('[name=fec]').datepicker();
			}
		});
	},
	windowBon: function(p){
		new K.Modal({
			id: 'windowTrabBono'+p.id,
			title: 'Agregar Bono para '+p.nomb,
			contentURL: 'pe/trab/bono',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 250,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							tipo: p.$w.find('[name=tipo] :selected').val(),
							cod: p.$w.find('[name=cod]').val(),
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							descr: p.$w.find('[name=descr]').val(),
							formula: p.$w.find('[name=form]').data('data')
						};
						K.sendingInfo();
						p.$w.find('.ui-dialog-buttonpane button').button('disable');
						$.post('pe/trab/save_bono',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.msg({
								title: ciHelper.titleMessages.regiGua,
								text: 'El Bono fue registrado con &eacute;xito!',
								type: 'success'
							});
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Bono fue registrado con &eacute;xito!'});
						});
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
				p.$w = $('#windowTrabBono'+p.id);
				p.$w.find('[name=btnForm]').click(function(){
					peConc.windowForm({
						tipo: p.tipo,
						val: p.$w.find('[name=form]').data('data'),
						callback: function(data){
							p.$w.find('[name=form]').html(data).data('data',data);
						}
					});
				});
			}
		});
	},
	windowDetailsBon: function(p){
		new K.Modal({
			id: 'windowDetailsTrabBono'+p.id,
			title: 'Bonos de '+p.nomb,
			contentURL: 'pe/trab/details_bono',
			icon: 'ui-icon-gear',
			width: 400,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id
						};
						data.bonos = new Array;
						if(p.$w.find('[name=gridBody] .item').length>0){
							for(i=0;i<p.$w.find('[name=gridBody] .item').length;i++){
								var item = {
									tipo:p.$w.find('[name=gridBody] .item').eq(i).find('[name=tipo] :selected').val(),
									cod:p.$w.find('[name=gridBody] .item').eq(i).find('[name=codigo]').val(),
									cod_sunat:p.$w.find('[name=gridBody] .item').eq(i).find('[name=codigo_sunat]').val(),
									descr:p.$w.find('[name=gridBody] .item').eq(i).find('[name=descr]').val(),
									formula:p.$w.find('[name=gridBody] .item').eq(i).find('[name=formula]').val()
								};
								data.bonos.push(item);
							}
						}
						K.sendingInfo();
						console.log(data);
						$.post('pe/trab/update_bono',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.msg({
								title: ciHelper.titleMessages.regiGua,
								text: 'Los bonos fueron modificados con &eacute;xito!',
								type: 'success'
							});
						});
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
			onContentLoaded: function(){
				p.$w = $('#windowDetailsTrabBono'+p.id);
				K.block({$element: p.$w});
				p.$w.on('click','[name=btnEli]',function(){
					$(this).closest('fieldset').remove();
				});
				$.post('pe/trab/get','id='+p.id,function(data){
					if(data.roles.trabajador.bonos==null){
						K.unblock({$element: p.$w});
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'El trabajador no tiene bonos asignados',type: 'info'});
						K.closeWindow(p.$w.attr('id'));
					}else{
						for(var i=0,j=data.roles.trabajador.bonos.length; i<j; i++){
							var $row = p.$w.find('fieldset.item').eq(0).clone(),
							result = data.roles.trabajador.bonos[i];
							$row.find('legend').html(result.cod+' <button class="btn btn-danger btn-sm" name="btnEli">Eliminar</button>');
							$row.find('[name=tipo]').find('[value='+result.tipo+']').attr('selected','selected');
							$row.find('[name=codigo]').val(result.cod);
							$row.find('[name=codigo_sunat]').val(result.cod_sunat);
							$row.find('[name=formula]').val(result.formula);
							$row.find('[name=descr]').val(result.descr);
							//$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'}});
							p.$w.find('[name=gridBody]').append($row);
						}
						K.unblock({$element: p.$w});
					}
				},'json');
			}
		});
	},
	windowRetencion: function(p){
		$.extend(p,{
			enti: function(data){
				p.$row.find('[name=nomb]').html(ciHelper.enti.formatName(data));
				p.$row.data('data',ciHelper.enti.dbRel(data));
			}
		});
		new K.Window({
			id: 'windowRetencion'+p.id,
			title: 'Retenci&oacute;n Jur&iacute;dica de '+p.nomb,
			contentURL: 'pe/trab/reten',
			store: false,
			icon: 'ui-icon-refresh',
			width: 520,
			height: 180,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						retencion: []
					};
					if(p.$w.find('tbody tr').length!=0){
						for(var i=0,j=p.$w.find('tbody tr').length; i<j; i++){
							var $row = p.$w.find('tbody tr').eq(i),
							tmp = {
								entidad: mgEnti.dbRel($row.data('data')),
								val: $row.find('[name=val]').val()
							};
							if(tmp.entidad==null){
								$row.find('[name=btnSel]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una entidad!',
									type: 'error'
								});
							}
							if(tmp.val==''){
								$row.find('[name=val]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar el valor del porcentaje!',
									type: 'error'
								});
							}
							data.retencion.push(tmp);
						}
					}else{
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar al menos una entidad!',
							type: 'error'
						});
					}
					K.sendingInfo();
					$.post('pe/trab/save_retencion',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El trabajador fue actualizado con &eacute;xito!'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowRetencion'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnAgregar]').click(function(){
					var $row = $('<tr class="item" />');
					$row.append('<td><span name="nomb"></span>&nbsp;<button class="btn btn-success" name="btnSel"><i class="fa fa-search"></i></button></td>');
					$row.append('<input type="text" name="val" class="form-control" size="7">');
					$row.append('<button name="btnEliminar" class="btn btn-danger"><i class="fa fa-trash"></i></button>');
					$row.find('[name=btnSel]').click(function(){
						p.$row = $(this).closest('.item');
						mgEnti.windowSelect({bootstrap:true,callback: function(data){
							p.$row.find('[name=nomb]').html(data.nomb+' '+data.appat+' '+data.nomb).data('data',data);
							p.$row.data('data',data);
						}});
					});
					p.$w.find('tbody').append($row);
				});
				p.$w.find('[name=btnEliminar]').on('click',function(){
					$(this).closest('.item').remove();
				});
				
				$.post('pe/trab/get',{id: p.id},function(data){
					if(data.roles.trabajador.retencion!=null){
						for(var i=0,j=data.roles.trabajador.retencion.length; i<j; i++){
							var $row = $('<tr class="item" />'),
							item = data.roles.trabajador.retencion[i];
							$row.append('<td><span name="nomb">'+ciHelper.enti.formatName(item.entidad)+'</span>&nbsp;<button class="btn btn-success" name="btnSel"><i class="fa fa-search"></i></button></td>');
							$row.append('<td><input type="text" name="val" size="7" value="'+item.val+'" class="form-control"></td>');
							$row.append('<td><button name="btnEliminar" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>');
							$row.find('[name=btnSel]').click(function(){
								p.$row = $(this).closest('.item');
								mgEnti.windowSelect({bootstrap:true,callback: function(enti){
									p.$row.find('[name=nomb]').html(enti.nomb+' '+enti.appat+' '+enti.nomb).data('data',enti);
								}});
								p.$row.data('data',enti);
							});
							p.$w.find('tbody').append($row);
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['mg/enti','pe/nive','pe/grup','pe/carg','pe/turn','pe/clas','mg/titu','pe/conc' ,'pr/acti'],
	function(mgEnti, peNive, peGrup, peCarg, peTurn, peClas, mgTitu, peConc,prActi){
		return peTrab;
	}
);