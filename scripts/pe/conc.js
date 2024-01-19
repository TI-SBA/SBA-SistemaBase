/*******************************************************************************
conceptos */
peConc = {
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
	tipo: {
		P: "Pago",
		D: "Descuento",
		A: "Aporte"
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peConc',
			titleBar: {
				title: 'Conceptos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},{n:'Cod.',f:'cod'},{n:'Tipo',f:'tipo'},{n:'Descripci&oacute;n',f:'descr'},{n:'Contrato',f:'contrato'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'pe/conc/lista',
					params: {tipo: $('#mainPanel [name=tipo] option:selected').val()},
					itemdescr: 'concepto(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>'+
						'<select style="width:auto" name="tipo" class="form-control"></select>'+
						'<button name="btnOrdenar" class="btn btn-info"><i class="fa fa-refresh"></i> Ordenar Conceptos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peConc.windowNew();
						});
						$el.find('[name=btnOrdenar]').click(function(){
							if($('#mainPanel [name=tipo] option:selected').val()==""){
								return K.notification({title: ciHelper.titles.infoReq,text: 'Debe Seleccionar un tipo de Contrato!',type: 'error'});
							}
							peConc.windowOrdenar({contrato:$('#mainPanel [name=tipo] option:selected').val()});
						});
						var $cbo = $el.find('[name=tipo]');
						$cbo.change(function(){
							$grid.reinit({params: {
								tipo: $('#mainPanel [name=tipo] option:selected').val()
							}});
						});
						$.post('pe/cont/all',function(data){
							$cbo.append('<option value="">Todos</option>');
							for(i=0;i<data.length;i++){
								$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
							}
							var data = $.jStorage.get('grid-pe/conc/lista');
							if(data!=null){
								$('#mainPanel [name=tipo]').val(data.tipo);
								$('#mainPanel .search input').val(data.texto);
								$('#mainPanel .search button').click();
							}else
								$cbo.change();
						},'json');
					},
					storeParam: function(data,q){
					},
					onLoading: function(){ 
						K.block();
					},
					onComplete: function(){ 
						K.unblock();
					},
					stopLoad: true,
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+peConc.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+peConc.tipo[data.tipo]+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						if(data.contrato==null) $row.append('<td>--</td>');
						else $row.append('<td>'+data.contrato.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peConc.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPeConc", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='H') $('#conMenPeConc_hab',menu).remove();
								else $('#conMenPeConc_edi,#conMenPeConc_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeConc_ver': function(t) {
									peConc.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeConc_edi': function(t) {
									peConc.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeConc_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Concepto <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/conc/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Concepto Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peConc.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Concepto');
								},
								'conMenPeConc_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Concepto <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('pe/conc/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Concepto Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peConc.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Concepto');
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
		K.block();
		new K.Panel({
			title: 'Nuevo Concepto',
			contentURL: 'pe/conc/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							descr: p.$w.find('[name=descr]').val(),
							cod: p.$w.find('[name=cod]').val().replace(/\s/g,"_"),
							imprimir: p.$w.find('[name=imprimir] :selected').val(),
							planilla: p.$w.find('[name=planilla] :selected').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar un nombre de Concepto!',type: 'error'});
						}
						if(data.cod_sunat==''){
							/*p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT!',type: 'error'});*/
						}/*else if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una descripci&oacute;n para el Concepto!',type: 'error'});
						}*/
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar un c&oacute;digo para el Concepto!',type: 'error'});
						}else if(data.cod.indexOf('__VALUE__')!=-1){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'El c&oacute;digo de concepto no puede contener la variable reservada <i>__VALUE__</i>!',type: 'error'});
						}
						if(p.$w.find('[name=clasif]').data('data')==null){
							/*p.$w.find('[name=btnClasi]').click();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un clasificador de gastos para el Concepto!',type: 'error'});*/
						}else{
							var tmp = p.$w.find('[name=clasif]').data('data');
							data.clasif = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb
							};
						}
						if(p.$w.find('[name=cuenta]').data('data')==null){
							/*p.$w.find('[name=btnCuenta]').click();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una cuenta contable para el Concepto!',type: 'error'});*/
						}else{
							var tmp = p.$w.find('[name=cuenta]').data('data');
							data.cuenta = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb
							};
						}
						if(p.$w.find('[name=form]').data('data')==null){
							p.$w.find('[name=btnForm]').click();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una f&oacute;rmula para el Concepto!',type: 'error'});
						}else data.formula = p.$w.find('[name=form]').data('data');
						var tmp = p.$w.find('[name=tipo_contrato] option:selected').data('data');
						if(tmp!=null){
							data.contrato = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								cod: tmp.cod
							};
						}
						if(p.$w.find('[name=chkBole]').is(':checked')) data.boleta = 1;
						else data.boleta = 0;
						if(p.$w.find('[name=chkComp]').is(':checked')) data.vacaciones = 1;
						else data.vacaciones = 0;
						if(p.$w.find('[name=chkCts]').is(':checked')) data.cts = 1;
						else data.cts = 0;
						if(p.$w.find('[name=chkGra]').is(':checked')) data.gravidez = 1;
						else data.gravidez = 0;
						if(p.$w.find('[name=chkEnf]').is(':checked')) data.enfermedad = 1;
						else data.enfermedad = 0;
						if(p.$w.find('[name=chkSub]').is(':checked')) data.sepelio = 1;
						else data.sepelio = 0;
						if(p.$w.find('[name=chkLut]').is(':checked')) data.luto = 1;
						else data.luto = 0;
						if(p.$w.find('[name=chkEda]').is(':checked')) data.quinquenio = 1;
						else data.quinquenio = 0;
						if(p.$w.find('[name=chkRen]').is(':checked')) data.renta = 1;
						else data.renta = 0;
						if(p.$w.find('[name=chkLiq]').is(':checked')) data.beneficios = 1;
						else data.beneficios = 0;
						if(data.tipo=='D'||data.tipo=='A'){
							data.beneficiario = p.$w.find('[name=cbo_bene] option:selected').val();
							if(data.beneficiario=='E'){
								if(p.$w.find('[name=bene]').data('data')==null){
									p.$w.find('[name=btnEnti]').click();
									return K.notification({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un beneficiario para el Concepto!',
										type: 'error'
									});
								}else{
									data.beneficiario = ciHelper.enti.dbRel(p.$w.find('[name=bene]').data('data'));
								}
							}
						}
						for(var i=0,j=p.$w.find('[name=gridFilt] tbody .item').length; i<j; i++){
							var $row = p.$w.find('[name=gridFilt] tbody .item').eq(i),
							filtro = $row.find('[name=filtro] option:selected').val();
							if(filtro!=''){
								var tmp;
								switch(filtro){
									case '1':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Nivel Designado!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '1',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												abrev: filt.abrev,
												salario: filt.salario,
												basica: filt.basica,
												reunificada: filt.reunificada
											}
										};
										break;
									case '2':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Nivel de Carrera!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '2',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												abrev: filt.abrev,
												salario: filt.salario,
												basica: filt.basica,
												reunificada: filt.reunificada
											}
										};
										break;
									case '3':
										tmp = {
											tipo: '3',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '4':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Cargo Clasificado!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '4',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												cod: filt.cod
											}
										};
										break;
									case '5':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Grupo Ocupacional!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '5',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												sigla: filt.sigla
											}
										};
										break;
									case '6':
										tmp = {
											tipo: '6',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '7':
										tmp = {
											tipo: '7',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '8':
										tmp = {
											tipo: '8',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '9':
										tmp = {
											tipo: '9',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
								}
								if(data.filtro==null) data.filtro = [];
								data.filtro.push(tmp);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/conc/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titles.regiGua,text: 'El Concepto fue registrado con &eacute;xito!'});
							peConc.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						peConc.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=cod]').bind('input', function (event) {
					$(this).val($(this).val().replace(/[^a-z0-9_]/gi, ''));
				});
				$.post('pe/cont/all',function(data){
					if(data==null){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Tipos de Contrato inv&aacute;lidos',text: 'Debe registrar primero tipos de contrato!',type: 'info'});
					}else{
						var $cbo = p.$w.find('[name=tipo_contrato]');
						$cbo.append('<option>--</option>');
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
					p.$w.find('[name=btnClasi]').click(function(){
						prClas.windowSelect({bootstrap: true,
						callback: function(data){
							p.$w.find('[name=clasif]').html(data.cod).data('data',data);
							p.$w.find('[name=btnClasi]').button('option','text','false');
						}});
					});
					p.$w.find('[name=btnClasi_clean]').click(function(){
						p.$w.find('[name=clasif]').html('').removeData('data');
					});
					p.$w.find('[name=btnCuenta]').click(function(){
						ctPcon.windowSelect({bootstrap: true,
						callback: function(data){
							p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
							p.$w.find('[name=btnCuenta]').button('option','text','false');
						}});
					});
					p.$w.find('[name=btnCuenta_clean]').click(function(){
						p.$w.find('[name=cuenta]').html('').removeData('data');
					});
					p.$w.find('[name=btnForm]').click(function(){
						if(p.$w.find('[name=tipo_contrato] option:selected').val()=='--'){
							return K.notification({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un tipo de contrato',
								type: 'error'
							});
						}
						peConc.windowForm({
							bootstrap: true,
							tipo: p.$w.find('[name=tipo_contrato] option:selected').val(),
							val: p.$w.find('[name=form]').data('data'),
							callback: function(data){
								p.$w.find('[name=form]').html(data).data('data',data);
							}
						});
					});
					p.$w.find('[name=btnBene]').click(function(){
						mgEnti.windowSelect({bootstrap: true,callback: function(data){
							p.$w.find('[name=bene]').html(mgEnti.formatName(data)).data('data',data);
						}});
					});
					p.$w.find('[name=tipo]').change(function(){
						var val = $(this).find('option:selected').val();
						if(val=='P')
							p.$w.find('[name=row_bene]').hide();
						else if(val=='D'||val=='A'){
							p.$w.find('[name=row_bene]').show();
							p.$w.find('[name=row_bene] [name=cbo_bene]').change();
						}
					}).change();
					p.$w.find('[name=row_bene]:first [name=cbo_bene]').change(function(){
						if($(this).val()=='E')
							p.$w.find('[name=row_bene]:eq(1)').show();
						else
							p.$w.find('[name=row_bene]:eq(1)').hide();
					});
					/*
					 * Se agregan los filtros
					 */
					new K.grid({
						$el: p.$w.find('[name=gridFilt]'),
						search: false,
						pagination: false,
						cols: ['Campo','Valor','&nbsp;'],
						onlyHtml: true,
						toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgregar"><i class="fa fa-plus"></i> Agregar</button>',
						onContentLoaded: function($el){
							$el.find('[name=btnAgregar]').click(function(){
								var $row = $('<tr class="item">');
								$row.append('<td><select style="width:auto" name="filtro" class="form-control">'+
									'<option value="">--</option>'+
									'<option value="1">Nivel Designado</option>'+
									'<option value="2">Nivel de Carrera</option>'+
									'<option value="3">Modalidad de Ingreso</option>'+
									'<option value="4">Cargo Clasificado</option>'+
									'<option value="5">Grupo Ocupacional</option>'+
									'<option value="6">Tipo de Trabajador</option>'+
									'<option value="7">Afiliaci&oacute;n EPS</option>'+
									'<option value="8">Sistema de Pension</option>'+
									'<option value="9">Tipo de Comisi&oacute;n</option>'+
								'</select></td>');
								$row.append('<td>');
								$row.append('<td><button class="btn btn-danger" type="button" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=filtro]').change(function(){
									$row = $(this).closest('.item');
									$row.find('td:eq(1)').empty();
									switch($(this).find('option:selected').val()){
										case '1':
											$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
											$row.find('[name=btnSel]').click(function(){
												peNive.windowSelect({callback: function(data){
													$row.find('[name=data]').html(data.nomb).data('data',data);
												}});
											});
											break;
										case '2':
											$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
											$row.find('[name=btnSel]').click(function(){
												peNive.windowSelect({callback: function(data){
													$row.find('[name=data]').html(data.nomb).data('data',data);
												}});
											});
											break;
										case '3':
											$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control">'+
													'<option value="CP">Convocatoria P&uacute;blica - CAS</option>'+
													'<option value="SP">Sustituci&oacute;n de Contrato</option>'+
													'<option value="DD">Designado Directamente</option>'+
													'<option value="OT">Otros</option>'+
												'</select>');
											break;
										case '4':
											$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
											$row.find('[name=btnSel]').click(function(){
												peClas.windowSelect({callback: function(data){
													p.$w.find('[name=data]').html(data.nomb+' - '+data.cod).data('data',data);
												}});
											});
											break;
										case '5':
											$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
											$row.find('[name=btnSel]').click(function(){
												peGrup.windowSelect({callback: function(data){
													p.$w.find('[name=data]').html(data.nomb+' - '+data.sigla).data('data',data);
												}});
											});
											break;
										case '6':
											$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control">'+
													'<option value="N">Nombrado</option>'+
													'<option value="C">Contratado</option>'+
													'<option value="SN">Salud Nombrado</option>'+
													'<option value="SC">Salud Contratado</option>'+
												'</select>');
											break;
										case '7':
											$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control">'+
													'<option value="1">Afiliado a EPS</option>'+
													'<option value="0">No Afiliado</option>'+
												'</select>');
											break;
										case '8':
											$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control"><option value="SNP">SNP</option><option value="SPP">SPP</option></select>');
											break;
										case '9':
											$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control"><option value="F">Comisi&oacute;n por Flujo</option><option value="M">Comisi&oacute;n Mixta</option></select>');
											break;
									}
								});
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
									if(p.$w.find('[name=gridFilt] .item').length==0){
										p.$w.find('[name=gridFilt] [name=btnAgregar]').click();
									}
								});
								p.$w.find('[name=gridFilt] tbody').append($row);
							}).click();
						}
					});
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		K.block();
		new K.Panel({
			title: 'Nuevo Concepto',
			contentURL: 'pe/conc/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cod_sunat: p.$w.find('[name=cod_sunat]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							descr: p.$w.find('[name=descr]').val(),
							imprimir: p.$w.find('[name=imprimir] :selected').val(),
							planilla: p.$w.find('[name=planilla] :selected').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar un nombre de Concepto!',type: 'error'});
						}
						if(data.cod_sunat==''){
							/*p.$w.find('[name=cod_sunat]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar un c&oacute;digo SUNAT!',type: 'error'});*/
						}/*else if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una descripci&oacute;n para el Concepto!',type: 'error'});
						}*/
						if(data.cod==''){
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar un c&oacute;digo para el Concepto!',type: 'error'});
						}
						if(p.$w.find('[name=clasif]').data('data')==null){
							if(data.tipo=='P'){
								/*p.$w.find('[name=btnClasi]').click();
								return K.notification({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un clasificador de gastos para el Concepto!',type: 'error'});*/
							}
						}else{
							var tmp = p.$w.find('[name=clasif]').data('data');
							data.clasif = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb
							};
						}
						if(p.$w.find('[name=cuenta]').data('data')==null){
							/*p.$w.find('[name=btnCuenta]').click();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe seleccionar una cuenta contable para el Concepto!',type: 'error'});*/
						}else{
							var tmp = p.$w.find('[name=cuenta]').data('data');
							data.cuenta = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb
							};
						}
						var tmp = p.$w.find('[name=tipo_contrato] option:selected').data('data');
						if(tmp!=null){
							data.contrato = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								cod: tmp.cod
							};
						}
						if(p.$w.find('[name=form]').data('data')==null){
							p.$w.find('[name=btnForm]').click();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una f&oacute;rmula para el Concepto!',type: 'error'});
						}else data.formula = p.$w.find('[name=form]').data('data');
						if(p.$w.find('[name=chkBole]').is(':checked')) data.boleta = 1;
						else data.boleta = 0;
						if(p.$w.find('[name=chkComp]').is(':checked')) data.vacaciones = 1;
						else data.vacaciones = 0;
						if(p.$w.find('[name=chkCts]').is(':checked')) data.cts = 1;
						else data.cts = 0;
						if(p.$w.find('[name=chkGra]').is(':checked')) data.gravidez = 1;
						else data.gravidez = 0;
						if(p.$w.find('[name=chkEnf]').is(':checked')) data.enfermedad = 1;
						else data.enfermedad = 0;
						if(p.$w.find('[name=chkSub]').is(':checked')) data.sepelio = 1;
						else data.sepelio = 0;
						if(p.$w.find('[name=chkLut]').is(':checked')) data.luto = 1;
						else data.luto = 0;
						if(p.$w.find('[name=chkEda]').is(':checked')) data.quinquenio = 1;
						else data.quinquenio = 0;
						if(p.$w.find('[name=chkRen]').is(':checked')) data.renta = 1;
						else data.renta = 0;
						if(p.$w.find('[name=chkLiq]').is(':checked')) data.beneficios = 1;
						else data.beneficios = 0;
						if(data.tipo=='A'||data.tipo=='D'){
							data.beneficiario = p.$w.find('[name=cbo_bene] option:selected').val();
							if(data.beneficiario=='E'){
								if(p.$w.find('[name=bene]').data('data')==null){
									p.$w.find('[name=btnEnti]').click();
									return K.notification({
										title: ciHelper.titles.infoReq,
										text: 'Debe seleccionar un beneficiario para el Concepto!',
										type: 'error'
									});
								}else{
									data.beneficiario = ciHelper.enti.dbRel(p.$w.find('[name=bene]').data('data'));
								}
							}
						}
						for(var i=0,j=p.$w.find('[name=gridFilt] tbody .item').length; i<j; i++){
							var $row = p.$w.find('[name=gridFilt] tbody .item').eq(i),
							filtro = $row.find('[name=filtro] option:selected').val();
							if(filtro!=''){
								var tmp;
								switch(filtro){
									case '1':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Nivel Designado!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '1',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												abrev: filt.abrev,
												salario: filt.salario,
												basica: filt.basica,
												reunificada: filt.reunificada
											}
										};
										break;
									case '2':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Nivel de Carrera!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '2',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												abrev: filt.abrev,
												salario: filt.salario,
												basica: filt.basica,
												reunificada: filt.reunificada
											}
										};
										break;
									case '3':
										tmp = {
											tipo: '3',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '4':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Cargo Clasificado!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '4',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												cod: filt.cod
											}
										};
										break;
									case '5':
										var filt = $row.find('[name=data]').data('data');
										if(filt==null){
											$row.find('[name=btnSel]').click();
											return K.notification({
												title: ciHelper.titles.infoReq,
												text: 'Debe seleccionar un Grupo Ocupacional!',
												type: 'error'
											});
										}
										tmp = {
											tipo: '5',
											valor: {
												_id: filt._id.$id,
												nomb: filt.nomb,
												sigla: filt.sigla
											}
										};
										break;
									case '6':
										tmp = {
											tipo: '6',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '7':
										tmp = {
											tipo: '7',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '8':
										tmp = {
											tipo: '8',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
									case '9':
										tmp = {
											tipo: '9',
											valor: $row.find('[name=data] option:selected').val()
										};
										break;
								}
								if(data.filtro==null) data.filtro = [];
								data.filtro.push(tmp);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/conc/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titles.regiAct,text: 'El Concepto fue actualizado con &eacute;xito!'});
							peConc.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						peConc.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnClasi]').click(function(){
					prClas.windowSelect({bootstrap: true,callback: function(data){
						p.$w.find('[name=clasif]').html(data.cod).data('data',data);
					}});
				});
				p.$w.find('[name=btnClasi_clean]').click(function(){
					p.$w.find('[name=clasif]').html('').removeData('data');
				});
				p.$w.find('[name=btnCuenta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				});
				p.$w.find('[name=btnCuenta_clean]').click(function(){
					p.$w.find('[name=cuenta]').html('').removeData('data');
				});
				p.$w.find('[name=btnForm]').click(function(){
					if(p.$w.find('[name=tipo_contrato] option:selected').val()=='--'){
						return K.notification({
							title: ciHelper.titles.infoReq,
							text: 'Debe seleccionar un tipo de contrato',
							type: 'error'
						});
					}
					peConc.windowForm({
						bootstrap: true,
						tipo: p.$w.find('[name=tipo_contrato] option:selected').val(),
						val: p.$w.find('[name=form]').data('data'),
						callback: function(data){
							p.$w.find('[name=form]').html(data).data('data',data);
						}
					});
				});
				p.$w.find('[name=btnBene]').click(function(){
					mgEnti.windowSelect({bootstrap: true,callback: function(data){
						p.$w.find('[name=bene]').html(ciHelper.enti.formatName(data)).data('data',data);
					}});
				});
				p.$w.find('[name=tipo]').change(function(){
					var val = $(this).find('option:selected').val();
					if(val=='P')
						p.$w.find('[name=row_bene]').hide();
					else if(val=='D'||val=='A'){
						p.$w.find('[name=row_bene]').show();
						p.$w.find('[name=row_bene] [name=cbo_bene]').change();
					}
				}).change();
				p.$w.find('[name=row_bene]:first [name=cbo_bene]').change(function(){
					if($(this).val()=='E')
						p.$w.find('[name=row_bene]:eq(1)').show();
					else
						p.$w.find('[name=row_bene]:eq(1)').hide();
				});
				new K.grid({
					$el: p.$w.find('[name=gridFilt]'),
					search: false,
					pagination: false,
					cols: ['Campo','Valor','&nbsp;'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgregar"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select style="width:auto" name="filtro" class="form-control">'+
								'<option value="">--</option>'+
								'<option value="1">Nivel Designado</option>'+
								'<option value="2">Nivel de Carrera</option>'+
								'<option value="3">Modalidad de Ingreso</option>'+
								'<option value="4">Cargo Clasificado</option>'+
								'<option value="5">Grupo Ocupacional</option>'+
								'<option value="6">Tipo de Trabajador</option>'+
								'<option value="7">Afiliaci&oacute;n EPS</option>'+
								'<option value="8">Sistema de Pension</option>'+
								'<option value="9">Tipo de Comisi&oacute;n</option>'+
							'</select></td>');
							$row.append('<td>');
							$row.append('<td><button class="btn btn-danger" type="button" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=filtro]').change(function(){
								$row = $(this).closest('.item');
								$row.find('td:eq(1)').empty();
								switch($(this).find('option:selected').val()){
									case '1':
										$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
										$row.find('[name=btnSel]').click(function(){
											peNive.windowSelect({callback: function(data){
												$row.find('[name=data]').html(data.nomb).data('data',data);
											}});
										});
										break;
									case '2':
										$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
										$row.find('[name=btnSel]').click(function(){
											peNive.windowSelect({callback: function(data){
												$row.find('[name=data]').html(data.nomb).data('data',data);
											}});
										});
										break;
									case '3':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control">'+
												'<option value="CP">Convocatoria P&uacute;blica - CAS</option>'+
												'<option value="SP">Sustituci&oacute;n de Contrato</option>'+
												'<option value="DD">Designado Directamente</option>'+
												'<option value="OT">Otros</option>'+
											'</select>');
										break;
									case '4':
										$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
										$row.find('[name=btnSel]').click(function(){
											peClas.windowSelect({callback: function(data){
												p.$w.find('[name=data]').html(data.nomb+' - '+data.cod).data('data',data);
											}});
										});
										break;
									case '5':
										$row.find('td:eq(1)').append('<span name="data"></span>&nbsp;<button class="btn btn-info" type="button" name="btnSel"><i class="fa fa-search"></i></button>');
										$row.find('[name=btnSel]').click(function(){
											peGrup.windowSelect({callback: function(data){
												p.$w.find('[name=data]').html(data.nomb+' - '+data.sigla).data('data',data);
											}});
										});
										break;
									case '6':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control">'+
												'<option value="N">Nombrado</option>'+
												'<option value="C">Contratado</option>'+
												'<option value="SN">Salud Nombrado</option>'+
												'<option value="SC">Salud Contratado</option>'+
											'</select>');
										break;
									case '7':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control">'+
												'<option value="1">Afiliado a EPS</option>'+
												'<option value="0">No Afiliado</option>'+
											'</select>');
										break;
									case '8':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control"><option value="SNP">SNP</option><option value="SPP">SPP</option></select>');
										break;
									case '9':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control"><option value="F">Comisi&oacute;n por Flujo</option><option value="M">Comisi&oacute;n Mixta</option></select>');
										break;
								}
							});
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
								if(p.$w.find('[name=gridFilt] .item').length==0){
									p.$w.find('[name=gridFilt] [name=btnAgregar]').click();
								}
							});
							p.$w.find('[name=gridFilt] tbody').append($row);
						});
					}
				});
				$.post('pe/cont/all',function(data){
					if(data==null){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Tipos de Contrato inv&aacute;lidos',text: 'Debe registrar primero tipos de contrato!',type: 'info'});
					}else{
						var $cbo = p.$w.find('[name=tipo_contrato]');
						$cbo.append('<option>--</option>');
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
					$.post('pe/conc/get',{_id: p.id},function(data){
						p.$w.find('[name=imprimir]').val(data.imprimir);
						p.$w.find('[name=planilla]').val(data.planilla);
						p.$w.find('[name=tipo]').selectVal(data.tipo).change();
						//p.$w.find('[name=tipo_contrato]').replaceWith('<span>'+data.contrato.nomb+'</span>');
						p.$w.find('[name=tipo_contrato]').selectVal(data.contrato.cod);
						p.$w.find('[name=nomb]').val(data.nomb);
						if(data.cod_sunat!=null)
							p.$w.find('[name=cod_sunat]').val(data.cod_sunat);
						p.$w.find('[name=descr]').val(data.descr);
						p.$w.find('[name=cod]').val(data.cod).attr('disabled','disabled');
						if(data.clasif!=null) p.$w.find('[name=clasif]').html(data.clasif.cod).data('data',data.clasif);
						if(data.cuenta!=null) p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
						p.$w.find('[name=form]').html(data.formula).data('data',data.formula);
						if(data.boleta==true) p.$w.find('[name=chkBole]').attr('checked',true);
						if(data.vacaciones==true) p.$w.find('[name=chkComp]').attr('checked',true);
						if(data.cts==true) p.$w.find('[name=chkCts]').attr('checked',true);
						if(data.gravidez==true) p.$w.find('[name=chkGra]').attr('checked',true);
						if(data.enfermedad==true) p.$w.find('[name=chkEnf]').attr('checked',true);
						if(data.subsidiado==true) p.$w.find('[name=chkSub]').attr('checked',true);
						if(data.luto==true) p.$w.find('[name=chkLut]').attr('checked',true);
						if(data.quinquenio==true) p.$w.find('[name=chkEda]').attr('checked',true);
						if(data.renta==true) p.$w.find('[name=chkRen]').attr('checked',true);
						if(data.beneficios==true) p.$w.find('[name=chkLiq]').attr('checked',true);
						if(data.beneficiario!=null){
							if(data.beneficiario._id!=null)
								p.$w.find('[name=bene]').html(ciHelper.enti.formatName(data.beneficiario)).data('data',data.beneficiario);
							else{
								p.$w.find('[name=cbo_bene]').selectVal(data.beneficiario);
								p.$w.find('[name=cbo_bene]').change();
							}
						}
						/*
						 * Se agregan los filtros en caso existan
						 */
						if(data.filtro!=null){
							for(var i=0,j=data.filtro.length; i<j; i++){
								p.$w.find('[name=gridFilt] [name=btnAgregar]').click();
								var $row = p.$w.find('[name=gridFilt] tbody .item:last');
								$row.find('[name=filtro]').selectVal(data.filtro[i].tipo);
								$row.find('[name=filtro]').change();
								switch(data.filtro[i].tipo){
									case '1':
										$row.find('[name=data]').html(data.filtro[i].valor.nomb).data('data',data.filtro[i].valor);
										break;
									case '2':
										$row.find('[name=data]').html(data.filtro[i].valor.nomb).data('data',data.filtro[i].valor);
										break;
									case '3':
										$row.find('[name=data]').selectVal(data.filtro[i].valor);
										break;
									case '4':
										$row.find('[name=data]').html(data.filtro[i].valor.nomb+' - '+data.filtro[i].valor.cod).data('data',data.filtro[i].valor);
										break;
									case '5':
										$row.find('[name=data]').html(data.filtro[i].valor.nomb+' - '+data.filtro[i].valor.sigla).data('data',data.filtro[i].valor);
										break;
									case '6':
										$row.find('[name=data]').selectVal(data.filtro[i].valor);
										break;
									case '7':
										$row.find('[name=data]').selectVal(data.filtro[i].valor);
										break;
									case '8':
										$row.find('[name=data]').selectVal(data.filtro[i].valor);
										break;
									case '9':
										$row.find('[name=data]').selectVal(data.filtro[i].valor);
										break;
								}
							}
						}else{
							p.$w.find('[name=gridFilt] [name=btnAgregar]').click();
						}
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = {};
		K.block();
		new K.Panel({
			title: 'Concepto: '+p.nomb,
			contentURL: 'pe/conc/details',
			buttons: {
				"Regresar": {
					icon: 'fa-refresh',
					type: 'warning',
					f: function(){
						peConc.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=tipo]').change(function(){
					var val = $(this).find('option:selected').val();
					if(val=='P')
						p.$w.find('[name=row_bene]').hide();
					else if(val=='D'||val=='A'){
						p.$w.find('[name=row_bene]').show();
						p.$w.find('[name=row_bene] [name=cbo_bene]').change();
					}
				}).change();
				p.$w.find('[name=row_bene]:first [name=cbo_bene]').change(function(){
					if($(this).val()=='E')
						p.$w.find('[name=row_bene]:eq(1)').show();
					else
						p.$w.find('[name=row_bene]:eq(1)').hide();
				});
				/*
				 * Se agregan los filtros
				 */
				new K.grid({
					$el: p.$w.find('[name=gridFilt]'),
					search: false,
					pagination: false,
					cols: ['Campo','Valor'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" type="button" name="btnAgregar" style="display:none;"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select style="width:auto" name="filtro" class="form-control" disabled="disabled">'+
								'<option value="">--</option>'+
								'<option value="1">Nivel Designado</option>'+
								'<option value="2">Nivel de Carrera</option>'+
								'<option value="3">Modalidad de Ingreso</option>'+
								'<option value="4">Cargo Clasificado</option>'+
								'<option value="5">Grupo Ocupacional</option>'+
								'<option value="6">Tipo de Trabajador</option>'+
								'<option value="7">Afiliaci&oacute;n EPS</option>'+
								'<option value="8">Sistema de Pension</option>'+
								'<option value="9">Tipo de Comisi&oacute;n</option>'+
							'</select></td>');
							$row.append('<td>');
							$row.find('[name=filtro]').change(function(){
								$row = $(this).closest('.item');
								$row.find('td:eq(1)').empty();
								switch($(this).find('option:selected').val()){
									case '1':
										$row.find('td:eq(1)').append('<span name="data"></span>');
										$row.find('[name=btnSel]').click(function(){
											peNive.windowSelect({callback: function(data){
												$row.find('[name=data]').html(data.nomb).data('data',data);
											}});
										});
										break;
									case '2':
										$row.find('td:eq(1)').append('<span name="data"></span>');
										$row.find('[name=btnSel]').click(function(){
											peNive.windowSelect({callback: function(data){
												$row.find('[name=data]').html(data.nomb).data('data',data);
											}});
										});
										break;
									case '3':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control" disabled="disabled">'+
												'<option value="CP">Convocatoria P&uacute;blica - CAS</option>'+
												'<option value="SP">Sustituci&oacute;n de Contrato</option>'+
												'<option value="DD">Designado Directamente</option>'+
												'<option value="OT">Otros</option>'+
											'</select>');
										break;
									case '4':
										$row.find('td:eq(1)').append('<span name="data"></span>');
										$row.find('[name=btnSel]').click(function(){
											peClas.windowSelect({callback: function(data){
												p.$w.find('[name=data]').html(data.nomb+' - '+data.cod).data('data',data);
											}});
										});
										break;
									case '5':
										$row.find('td:eq(1)').append('<span name="data"></span>');
										$row.find('[name=btnSel]').click(function(){
											peGrup.windowSelect({callback: function(data){
												p.$w.find('[name=data]').html(data.nomb+' - '+data.sigla).data('data',data);
											}});
										});
										break;
									case '6':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control" disabled="disabled">'+
												'<option value="N">Nombrado</option>'+
												'<option value="C">Contratado</option>'+
												'<option value="SN">Salud Nombrado</option>'+
												'<option value="SC">Salud Contratado</option>'+
											'</select>');
										break;
									case '7':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control" disabled="disabled">'+
												'<option value="1">Afiliado a EPS</option>'+
												'<option value="0">No Afiliado</option>'+
											'</select>');
										break;
									case '8':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control" disabled="disabled"><option value="SNP">SNP</option><option value="SPP">SPP</option></select>');
										break;
									case '9':
										$row.find('td:eq(1)').append('<select style="width:auto" name="data" class="form-control" disabled="disabled"><option value="F">Comisi&oacute;n por Flujo</option><option value="M">Comisi&oacute;n Mixta</option></select>');
										break;
								}
							});
							p.$w.find('[name=gridFilt] tbody').append($row);
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridHist]'),
					search: false,
					pagination: false,
					cols: ['Fecha','Trabajador','Nombre','Descripci&oacute;n','Valor'],
					onlyHtml: true
				});
				$.post('pe/conc/get',{_id: p.id,all: true},function(data){
					p.$w.find('[name=imprimir]').val(data.imprimir);
					p.$w.find('[name=planilla]').val(data.planilla);
					p.$w.find('[name=tipo]').append('<option value="'+data.tipo+'">'+data.tipo+'</option>');
					p.$w.find('[name=tipo]').selectVal(data.tipo).change();
					//p.$w.find('[name=tipo_contrato]').replaceWith('<span>'+data.contrato.nomb+'</span>');
					p.$w.find('[name=tipo_contrato]').append('<option value="'+data.contrato.cod+'">'+data.contrato.nomb+'</option>');
					p.$w.find('[name=tipo_contrato]').selectVal(data.contrato.cod);
					p.$w.find('[name=nomb]').html(data.nomb);
					if(data.cod_sunat!=null)
						p.$w.find('[name=cod_sunat]').html(data.cod_sunat);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=cod]').html(data.cod).attr('disabled','disabled');
					if(data.clasif!=null) p.$w.find('[name=clasif]').html(data.clasif.cod).data('data',data.clasif);
					if(data.cuenta!=null) p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
					p.$w.find('[name=form]').html(data.formula).data('data',data.formula);
					if(data.boleta==true) p.$w.find('[name=chkBole]').attr('checked',true);
					if(data.vacaciones==true) p.$w.find('[name=chkComp]').attr('checked',true);
					if(data.cts==true) p.$w.find('[name=chkCts]').attr('checked',true);
					if(data.gravidez==true) p.$w.find('[name=chkGra]').attr('checked',true);
					if(data.enfermedad==true) p.$w.find('[name=chkEnf]').attr('checked',true);
					if(data.subsidiado==true) p.$w.find('[name=chkSub]').attr('checked',true);
					if(data.luto==true) p.$w.find('[name=chkLut]').attr('checked',true);
					if(data.quinquenio==true) p.$w.find('[name=chkEda]').attr('checked',true);
					if(data.renta==true) p.$w.find('[name=chkRen]').attr('checked',true);
					if(data.beneficios==true) p.$w.find('[name=chkLiq]').attr('checked',true);
					if(data.beneficiario!=null){
						if(data.beneficiario._id!=null)
							p.$w.find('[name=bene]').html(ciHelper.enti.formatName(data.beneficiario)).data('data',data.beneficiario);
						else{
							p.$w.find('[name=cbo_bene]').selectVal(data.beneficiario);
							p.$w.find('[name=cbo_bene]').change();
						}
					}
					/*
					 * Se agregan los filtros en caso existan
					 */
					if(data.filtro!=null){
						for(var i=0,j=data.filtro.length; i<j; i++){
							p.$w.find('[name=gridFilt] [name=btnAgregar]').click();
							var $row = p.$w.find('[name=gridFilt] tbody .item:last');
							$row.find('[name=filtro]').selectVal(data.filtro[i].tipo);
							$row.find('[name=filtro]').change();
							switch(data.filtro[i].tipo){
								case '1':
									$row.find('[name=data]').html(data.filtro[i].valor.nomb).data('data',data.filtro[i].valor);
									break;
								case '2':
									$row.find('[name=data]').html(data.filtro[i].valor.nomb).data('data',data.filtro[i].valor);
									break;
								case '3':
									$row.find('[name=data]').selectVal(data.filtro[i].valor);
									break;
								case '4':
									$row.find('[name=data]').html(data.filtro[i].valor.nomb+' - '+data.filtro[i].valor.cod).data('data',data.filtro[i].valor);
									break;
								case '5':
									$row.find('[name=data]').html(data.filtro[i].valor.nomb+' - '+data.filtro[i].valor.sigla).data('data',data.filtro[i].valor);
									break;
								case '6':
									$row.find('[name=data]').selectVal(data.filtro[i].valor);
									break;
								case '7':
									$row.find('[name=data]').selectVal(data.filtro[i].valor);
									break;
								case '8':
									$row.find('[name=data]').selectVal(data.filtro[i].valor);
									break;
								case '9':
									$row.find('[name=data]').selectVal(data.filtro[i].valor);
									break;
							}
						}
					}
					if(data.historico){
						var $grid = p.$w.find('[name=gridHist] tbody');
						for(var i=0,j=data.historico.length; i<j; i++){
							var item = data.historico[i],
							$row = $('<tr class="item">');
							$row.append('<td>'+ciHelper.date.format.bd_ymdhi(item.fecreg)+'</td>');
							$row.append('<td>'+mgEnti.formatName(item.trabajador)+'</td>');
							$row.append('<td>'+item.nomb+'</td>');
							$row.append('<td>'+item.descr+'</td>');
							$row.append('<td>'+item.formula+'</td>');
							$grid.append($row);
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowForm: function(p){
		if(p.bootstrap!=null){
			var buttons = {
				"Actualizar": {
					icon: 'fa-refresh',
					type: 'success',
					f: function(){
						var val = p.$w.find('[name=cod]').val();
						p.callback(val);
						/*if(val!=''){
							if(p.$w.find('[name=confir]').data('check')==true){
								p.callback(val);
							}else{
								p.$w.find('[name=cod]').focus();
								return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una f&oacute;rmula v&aacute;lida!',type: 'error'});
							}
						}else{
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una f&oacute;rmula!',type: 'error'});
						}*/
						K.closeWindow(p.$w.attr('id'));
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			};
		}else{
			var buttons = {
				"Actualizar": function(){
					var val = p.$w.find('[name=cod]').val();
					if(val!=''){
						if(p.$w.find('[name=confir]').data('check')==true){
							p.callback(val);
						}else{
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una f&oacute;rmula v&aacute;lida!',type: 'error'});
						}
					}else{
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar una f&oacute;rmula!',type: 'error'});
					}
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			};
		}
		new K.Modal({
			id: 'windowCreateForm',
			title: 'Editor de F&oacute;rmulas',
			contentURL: 'pe/conc/form',
			allScreen: true,
			buttons: buttons,
			onContentLoaded: function(){
				p.$w = $('#windowCreateForm');
				var __VALUE__ = 42;
				if(p.val!=null) p.$w.find('[name=cod]').val(p.val);
				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridConc]'),
					search: false,
					pagination: false,
					cols: ['Concepto','C&oacute;digo'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridGlob]'),
					search: false,
					pagination: false,
					cols: ['Variable','C&oacute;digo'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridTrab]'),
					search: false,
					pagination: false,
					cols: ['Variable','C&oacute;digo'],
					onlyHtml: true
				});
				$.post('pe/conc/all_var','tipo='+p.tipo,function(data){
					if(data.conc!=null){
						for(p.i=0; p.i<data.conc.length; p.i++){
							//eval('var '+data[p.i].cod+' = function(){'+data[p.i].formula+'};');
							eval('var '+data.conc[p.i].cod+' = 10100;');
							var $row = $('<tr class="item">');
							$row.append('<td>'+data.conc[p.i].nomb+'</td>');
							$row.append('<td>'+data.conc[p.i].cod+'</td>');
							$row.dblclick(function(){
								p.$w.find('[name=cod]').val(p.$w.find('[name=cod]').val()+$(this).data('cod')).keyup();
							}).data('cod',data.conc[p.i].cod);
							p.$w.find('#tabs-1 tbody').append($row);
						}
					}
					if(data.vari!=null){
						for(p.i=0; p.i<data.vari.length; p.i++){
							eval('var '+data.vari[p.i].cod+' = 10100;');
							var $row = $('<tr class="item">');
							$row.append('<td>'+data.vari[p.i].nomb+'</td>');
							$row.append('<td>'+data.vari[p.i].cod+'</td>');
							$row.dblclick(function(){
								p.$w.find('[name=cod]').val(p.$w.find('[name=cod]').val()+$(this).data('cod')).keyup();
							}).data('cod',data.vari[p.i].cod);
							p.$w.find('#tabs-2 tbody').append($row);
						}
					}
					if(data.defs!=null){
						var TRAB = {};
						for(p.i=0; p.i<data.defs.length; p.i++){
							eval('TRAB_'+data.defs[p.i].cod+' = 10100;');
							var $row = $('<tr class="item">');
							$row.append('<td>'+data.defs[p.i].nomb+'</td>');
							$row.append('<td>TRAB_'+data.defs[p.i].cod+'</td>');
							$row.dblclick(function(){
								p.$w.find('[name=cod]').val(p.$w.find('[name=cod]').val()+$(this).data('cod')).keyup();
							}).data('cod','TRAB_'+data.defs[p.i].cod);
							p.$w.find('#tabs-3 tbody').append($row);
						}
					}
					p.$w.find('[name=cod]').keyup(function(){
						var val = $(this).val();
						if(val!=''){
							try{
								eval(val);
								p.$w.find('table:first th:eq(1)').removeClass('ui-state-error');
								p.$w.find('[name=rpta]').html('F&oacute;rmula correcta!');
								p.$w.find('[name=confir]').removeClass('fa-ban')
									.addClass('fa-check').data('check',true);
							}catch(mierror){
								//K.notification({text: "Error detectado: " + mierror,type: 'info'});
								p.$w.find('table:first th:eq(1)').addClass('ui-state-error');
								p.$w.find('[name=rpta]').html('Revise su f&oacute;rmula!');
								p.$w.find('[name=confir]').removeClass('fa-check')
							   		.addClass('fa-ban').data('check',false);
							}
						}
					}).keyup();
					K.unblock();
				},'json');
			}
		});
	},
	windowOrdenar: function(p){
		if(p==null) p = {};
		$.extend(p,{
			actButton: function($row){
				$row.find('button:eq(0)').click(function(){
					var $row = $(this).closest('.item');
					$row.insertBefore($row.prev());
				    for(i=0;i<p.$w.find('tbody .item').length;i++){
				    	p.$w.find('tbody .item').eq(i).find('td:eq(0)').html( i+1 );
				    }
				});
				$row.find('button:eq(1)').click(function(){
					var $row = $(this).closest('.item');
					$row.insertAfter($row.next());
				    for(i=0;i<p.$w.find('tbody .item').length;i++){
				    	p.$w.find('tbody .item').eq(i).find('td:eq(0)').html( i+1 );
				    }
				});
			},
		});
		new K.Panel({
			title: 'Ordenar Conceptos',
			content: '<div name=grid>',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						var data = {};
						data.items = [];
						if(p.$w.find('tbody .item').length>0){
							for(i=0;i<p.$w.find('tbody .item').length;i++){
								data.items.push({
									_id:p.$w.find('tbody .item').eq(i).data('id'),
									orden:i+1
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('pe/conc/save_order',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titles.regiAct,text: 'Los conceptos Fuer&oacute;n actualizado con &eacute;xito!'});
							peConc.init();
						});
					}
				},
				'Cancelar': {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						peConc.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
		   		var $grid = new K.grid({
		   			$el: p.$w.find('[name=grid]'),
					cols: ['','','Nombre','Cod.','Tipo','Descripci&oacute;n'],
					data: 'pe/conc/all_order',
					pagination: false,
					search: false,
					params: {contrato: p.contrato},
					itemdescr: 'concepto(s)',
					onLoading: function(){ K.block();},
					onComplete: function(){
						K.unblock();
						p.$w.find("tbody").sortable({
							stop : function(event, ui) {
							    for(i=0;i<p.$w.find('tbody .item').length;i++){
							    	p.$w.find('tbody .item').eq(i).find('td:eq(0)').html( i+1 );
							    }
							}
						});
					},
					fill: function(data,$row){
						$row.append('<td>'+data.orden+'</td>');
		   				$row.append('<td width="150"><div class="btn-group" role="group" aria-label="...">'+
							'<button type="button" class="btn btn-info"><i class="fa fa-arrow-up"></i></button>'+
							'<button type="button" class="btn btn-info"><i class="fa fa-arrow-down"></i></button>'+
						'</div></td>');
						p.actButton($row);
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+peConc.tipo[data.tipo]+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						$row.data('id',data._id.$id);
						return $row;
					}
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
			title: 'Seleccionar Concepto',
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
							return K.notification({
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
					cols: ['','Nombre','Cod.','Tipo'],
					data: 'pe/conc/lista',
					params: {},
					itemdescr: 'concepto(s)',
					onLoading: function(){ 
						K.block();
					},
					onComplete: function(){ 
						K.unblock();
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+peConc.tipo[data.tipo]+'</td>');
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
	['pe/clas','pe/nive','pe/grup','pr/clas','ct/pcon'],
	function(peClas,peNive,peGrup,prClas,ctPcon){
		return peConc;
	}
);