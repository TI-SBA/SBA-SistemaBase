pePlan = {
	states: {
		C: {
			descr: "Creado",
			color: "green",
			label: '<span class="label label-success">Creado</span>'
		},
		G: {
			descr: "Generado",
			color: "green",
			label: '<span class="label label-success">Generado</span>'
		},
		BG: {
			descr: "Boletas Generadas",
			color: "green",
			label: '<span class="label label-success">Boletas Generadas</span>'
		},
		X:{
			descr: "Anulado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Anulado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'pePlan',
			titleBar: {
				title: 'Planillas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Nombre','Contrato','Periodo','Fecha inicio','Fecha fin','&Uacute;ltima modificaci&oacute;n'],
					data: 'pe/plan/lista',
					params: {},
					itemdescr: 'planilla(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							pePlan.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock();
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+pePlan.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						if(data.contrato!=null){
							$row.append('<td>'+data.contrato.cod+'</td>');	
						}else{
							$row.append('<td>--</td>');
						}
						
						$row.append('<td>'+data.periodo.mes+'-'+data.periodo.ano+'</td>');
						$row.append('<td>'+moment(data.fecini.sec,"X").format("DD/MM/YYYY")+'</td>');
						$row.append('<td>'+moment(data.fecfin.sec,"X").format("DD/MM/YYYY")+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('data',data).data('id',data._id.$id).dblclick(function(){
							pePlan.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPePlan", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								/*if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();*/
								return menu;
							},
							bindings: {
								'conMenPePlan_edi': function(t) {
									pePlan.windowEdit({id: K.tmp.data('id')});
								},
								'conMenPePlan_tra': function(t) {
									pePlan.windowSelectTrabajadores({id: K.tmp.data('id')});
								},
								'conMenPePlan_gen': function(t) {
									/****************************************************************************************
									 * GENERAR LOTE DE PLANILLAS
									 ****************************************************************************************/
									ciHelper.confirm('&#191;Desea iniciar una generaci&oacute;n en Lote<b></b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/bole/all_planilla',{
											_id: K.tmp.data('id')
										},function(data){
											var trabs = [];
											for (var i in data){
												trabs.push(data[i]);
											}
											peBole.windowNew({
												trabs: trabs,
												planilla: K.tmp.data('data')
											});
										},'json');
									},function(){
										$.noop();
									},'Lote de Planillas');
								},
								'conMenPePlan_dma': function(t) {
									window.open('pe/plan/download_formato_maestro?_id='+K.tmp.data('id'));
								},
								'conMenPePlan_sma': function(t) {
									pePlan.windowSubirFormatoMaestro({id: K.tmp.data('id')});
								},
								'conMenPePlan_ibp': function(t) {
									K.msg({icon: 'ui-icon-print',delay: 10000,title: 'Generando Impresion',text: 'Espere un momento por favor'});
									$('[name=iframe]').remove();
									$('#mainPanel').append('<iframe name="iframe" src="pe/bole/print_bole_lote?print=1&_id='+K.tmp.data('id')+'" style="width:10px;height:10px;"></iframe>');
								},
								'conMenPePlan_ibo': function(t) {
									K.windowPrint({
										id:'windowpeGenerePPFT',
										title: "PDF "+K.tmp.find('td:eq(2)').html(),
										url: 'pe/bole/print_bole_lote?_id='+K.tmp.data('id')
									});
								},
								'conMenPePlan_gpe': function(t) {
									window.open('pe/plan/repo_elec_export?pdf=1&_id='+K.tmp.data('id'));
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
			id: 'windowNewPlan',
			title: 'Nuev Planilla',
			contentURL: 'pe/plan/edit',
			store:false,
			width: 380,
			height: 320,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							contrato: {
								_id:p.$w.find('[name=contrato] :selected').data('data')._id.$id,
								cod:p.$w.find('[name=contrato] :selected').data('data').cod,
								nomb:p.$w.find('[name=contrato] :selected').data('data').nomb
							},
							periodo: {
								mes: p.$w.find('[name=mes] :selected').val(),
								ano: p.$w.find('[name=ano]').val(),
								sort: p.$w.find('[name=ano]').val()+''+p.$w.find('[name=mes] :selected').val()
							},
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val()
						};
						/*if(data.fecini==''){
							p.$w.find('[name=fecini]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresarla fecha de inicio del periodo!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresarla fecha de fin del periodo!',type: 'error'});
						}*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("pe/plan/save",data,function(result){
							K.clearNoti();
							K.msg({title: "Mensaje del Sistema",text: result.message, type: result.status});
							if(result.status=='success'){
								pePlan.init();
								K.closeWindow(p.$w.attr('id'));
							}else{
								p.$w.find('#div_buttons button').removeAttr('disabled');
							}
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
				p.$w = $('#windowNewPlan');
				K.block();
				p.$w.find('[name=ano]').val(ciHelper.date.get.now_y());
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
				$.post('pe/cont/all',function(cont){
					if(cont==null){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Tipos de Contrato inv&aacute;lidos',text: 'Debe registrar primero tipos de contrato!',type: 'info'});
					}else{
						var $cbo = p.$w.find('[name=contrato]');
						for(var i=0,j=cont.length; i<j; i++){
							$cbo.append('<option value="'+cont[i].cod+'">'+cont[i].nomb+'</option>');
							$cbo.find('option:last').data('data',cont[i]);
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditPlan',
			title: 'Editar Planilla',
			contentURL: 'pe/plan/edit',
			store:false,
			width: 380,
			height: 320,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							contrato: {
								_id:p.$w.find('[name=contrato] :selected').data('data')._id.$id,
								cod:p.$w.find('[name=contrato] :selected').data('data').cod,
								nomb:p.$w.find('[name=contrato] :selected').data('data').nomb
							},
							periodo: {
								mes: p.$w.find('[name=mes] :selected').val(),
								ano: p.$w.find('[name=ano]').val(),
								sort: p.$w.find('[name=ano]').val()+''+p.$w.find('[name=mes] :selected').val()
							},
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val()
						};
						/*if(data.fecini==''){
							p.$w.find('[name=fecini]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresarla fecha de inicio del periodo!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresarla fecha de fin del periodo!',type: 'error'});
						}*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("pe/plan/save",data,function(result){
							K.clearNoti();
							K.msg({title: "Mensaje del Sistema",text: result.message, type: result.status});
							if(result.status=='success'){
								pePlan.init();
								K.closeWindow(p.$w.attr('id'));
							}else{
								p.$w.find('#div_buttons button').removeAttr('disabled');
							}
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
				p.$w = $('#windowEditPlan');
				K.block();
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
				$.post('pe/cont/all',function(cont){
					if(cont==null){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Tipos de Contrato inv&aacute;lidos',text: 'Debe registrar primero tipos de contrato!',type: 'info'});
					}else{
						var $cbo = p.$w.find('[name=contrato]');
						for(var i=0,j=cont.length; i<j; i++){
							$cbo.append('<option value="'+cont[i].cod+'">'+cont[i].nomb+'</option>');
							$cbo.find('option:last').data('data',cont[i]);
						}
						$.post('pe/plan/get',{_id: p.id},function(data){
							p.$w.find('[name=nomb]').val(data.nomb);
							p.$w.find('[name=mes]').val(data.periodo.mes);
							p.$w.find('[name=ano]').val(data.periodo.ano);
							p.$w.find('[name=fecini]').val(moment(data.fecini.sec,"X").format('YYYY-MM-DD'));
							p.$w.find('[name=fecfin]').val(moment(data.fecfin.sec,"X").format('YYYY-MM-DD'));
							K.unblock();
						},'json');
					}
				},'json');
			}
		});
	},
	windowSelectTrabajadores: function(p){
		new K.Modal({
			id: 'windowSelectTrab',
			content: '<div class="row"><div class="col-md-6"><h3>TRABAJADORES</h3><div name="tmp1"></div></div> <div class="col-md-6"><h3>TRABAJADORES SELECCIONADOS</h3><div name="tmp2"></div></div> </div>',
			width: 1000,
			height: 600,
			title: 'Seleccionar Trabajadores',
			buttons: {
				"Guardar": {
					icon: 'fa-check',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							trabajadores: []
						};
						var $items = p.$w.find('[name=tmp2] tbody').find('tr');
						if($items!=null){
							for(var i=0;i<$items.length;i++){
								var $item = $items.eq(i).attr('id');
								data.trabajadores.push($item);
							}
						}
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("pe/plan/save_trabajadores",data,function(result){
							K.clearNoti();
							K.msg({title: "Mensaje del Sistema",text: result.message, type: result.status});
							if(result.status=='success'){
								pePlan.init();
								K.closeWindow(p.$w.attr('id'));
							}else{
								p.$w.find('#div_buttons button').removeAttr('disabled');
							}
						},'json');
					}
				},
				"Cerrar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectTrab');
				p.$w.on('click','[name=btnDeleteRow]',function(){
					$(this).closest('tr').remove();
				});
				$.post('pe/cont/all',function(contratos){
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp1]'),
						cols: ['<input type="checkbox" name="select_trab_all">','APELLIDOS Y NOMBRES'],
						data: 'pe/trab/all_tipo',
						params: {tipo: contratos[0].cod},
						itemdescr: 'trabajador(es)',
						toolbarHTML: '<button name="btnAddTrab" class="btn btn-success">></button><select class="form-control col-sm-4" name="tipo"></select>',
						onContentLoaded: function($el){
							p.$w.find('[name=select_trab_all]').change(function(){
								if(p.$w.find('[name=select_trab_all]').is(':checked')){
									p.$w.find('[name=select_trab]').val(["1"]);
								}else{
									p.$w.find('[name=select_trab]').val(["0"]);
								}
							});
							var $cbo = $el.find('[name=tipo]');
							for(var i=0; i<contratos.length; i++){
								$cbo.append('<option value="'+contratos[i].cod+'">'+contratos[i].nomb+'</option>');
								$cbo.find('option:last').data('data',contratos[i]);
							}
							$el.find('[name=tipo]').change(function(){
								p.$grid.reinit({params: {
									tipo: $(this).val()
								}});
							});
							$el.find('[name=btnAddTrab]').click(function(){
								var $items = p.$w.find('[name=select_trab]:checked');
								if($items!=null){
									for(var i=0;i<$items.length;i++){
										var $item = $items.eq(i).closest('tr').data('data');
										if(p.$w.find('[id="'+$item._id.$id+'"]').length==0){
											var $row = $('<tr class="item" id="'+$item._id.$id+'" />');
											$row.append('<td><button name="btnDeleteRow" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>');
											$row.append('<td>'+$item.appat+' '+$item.apmat+' '+$item.nomb+'</td>');
											p.$w.find('[name=tmp2] tbody').append($row);	
										}
									}
								}
							});
						},
						onLoading: function(){ K.block(); },
						onComplete: function(){ K.unblock(); },
						pagination:false,
						search:false,
						load: function(data,$tbody){
							if(data!=null){
								for(var i in data){
									var $row = $('<tr class="item">');
									$row.append('<td><input type="checkbox" id="chk_'+data[i]._id.$id+'" name="select_trab" value="1"></td>')
									$row.append('<td><label for="chk_'+data[i]._id.$id+'">'+data[i].appat+' '+data[i].apmat+' '+data[i].nomb+'</label></td>');
									$row.data('data',data[i]);
									$tbody.append($row);
								}
							}
							return $tbody;
						}
					});
				},'json');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp2]'),
					cols: ['','APELLIDOS Y NOMBRES'],
					data: 'pe/trab/all_tipo',
					params: {_id:p.id, tipo:'asdasd'},
					itemdescr: 'trabajador(es)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					pagination:false,
					search:false,
					load: function(data,$tbody){
						if(data!=null){
							for(var i in data){
								var $row = $('<tr class="item">');
								$row.append('<td><input type="checkbox" id="'+data[i]._id.$id+'" name="select_trab" value="1"></td>')
								$row.append('<td><label for="'+data[i]._id.$id+'">'+data[i].appat+' '+data[i].apmat+' '+data[i].nomb+'</label></td>');
								$row.data('data',data[i]);
								$tbody.append($row);
							}
						}
						return $tbody;
					}
				});
			}
		});
	},
	windowSubirFormatoMaestro: function(p){
		new K.Modal({
			id: 'windowSelectUpload',
			contentURL: 'pe/plan/import_maestro_main',
			width: 750,
			height: 600,
			store: false,
			title: 'Subir Formato de maestro planilla',
			buttons: {
				"Cerrar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectUpload');
				p.$w.find("#file_upload").fileinput({
					language: "es",
					uploadUrl: "ci/upload/pe_asistencia",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function() {
						return {
							operacion: 'PE_PLAN_MAESTRO'
						};
					},
					allowedFileExtensions: ["XLSX","XLS"]
				});
				p.$w.find('#file_upload').on('fileuploaded', function(event, params) {
					K.clearNoti();
					K.block();
					K.sendingInfo();
					$.post('pe/plan/import_maestro',{file: params.files[0].name},function(){
						K.clearNoti();
						K.unblock();
						K.notification({
							title: ciHelper.titleMessages.regiGua,
							text: 'Registros importados con &eacute;xito!'
						});
						K.closeWindow(p.$w.attr('id'));
					});
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
					data: 'pe/plan/lista',
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
	}
};
define(
	['mg/enti','pe/trab','pe/bole'],
	function(mgEnti,peTrab, peBole){
		return pePlan;
	}
);