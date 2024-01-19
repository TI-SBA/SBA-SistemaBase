inMarq = {
	states: {
		A: {
			descr: "Abierto",
			color: "green",
			label: '<span class="label label-success">Abierto</span>'
		},
		C:{
			descr: "Cerrado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Cerrado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			num: item.num,
			arrendatario: mgEnti.dbRel(item.arrendatario),
			inmueble: inInmu.dbRel(item.inmueble)
		};
	},
	init: function(){
		K.initMode({
			mode: 'in',
			action: 'inMarq',
			titleBar: {
				title: 'Marquesí de Inmuebles'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Abrev.',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'in/marq/lista',
					params: {},
					itemdescr: 'contrato(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Contrato</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							inMarq.windowNew();
						});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
						$.getScript( "scripts/plugins/jquery.steps.js", function( data, textStatus, jqxhr ) {
						  console.log( textStatus ); // Success
						  console.log( jqxhr.status ); // 200
						  console.log( "Se cargo el JquerySteps." );
						});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+inMarq.states[data.estado].label+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.arrendatario)+'</td>');
						$row.append('<td>'+data.inmueble.direccion+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inMarq.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEli", {
							onShowMenu: function($row, menu) {
								$('#conMenListEli_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEli_ver': function(t) {
									inMarq.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEli_edi': function(t) {
									inMarq.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEli_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Acta de <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/acta/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Acta de Conciliacion Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inMarq.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Acta de Conciliacion');
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
			title: 'Nuevo contrato',
			contentURL: 'in/marq/edit_marq',
			store: false,
			buttons: {
				/*
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cuotas: p.$w.find('[name=cuotas]').val(),
							num: p.$w.find('[name=num]').val(),
							arrendatario: p.$w.find('[name=arrendatario]').data('data'),
							inmueble: p.$w.find('[name=inmueble]').data('data'),
							items: []
						};
						if(data.arrendatario==null){
							p.$w.find('[name=btnArre]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el arrendatario!',type: 'error'});
						}else data.arrendatario = mgEnti.dbRel(data.arrendatario);
						if(data.inmueble==null){
							p.$w.find('[name=btnInm]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un inmueble!',type: 'error'});
						}else data.inmueble = inInmu.dbRel(data.inmueble);
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						if(data.cuotas==''){
							p.$w.find('[name=cuotas]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el total de cuotas de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						for(var i=0,j=parseInt(data.cuotas); i<j; i++){
							var $table = p.$w.find('[name=grillas] tbody').eq(i),
							item = {
								num: i+1,
								fecven: $table.find('[name=fecven]').val(),
								conceptos: [],
								total: $table.find('[name=total]').data('data')
							};
							if(item.fecven==''){
								p.$w.find('[name=fecven]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de vencimiento!',type: 'error'});
							}
							for(var ii=0,jj=$table.find('[name=monto]').length; ii<jj; ii++){
								if($table.find('[name=descr]').eq(ii).val()==''){
									$table.find('[name=descr]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
								}
								if($table.find('[name=monto]').eq(ii).val()==''){
									$table.find('[name=monto]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
								}
								item.conceptos.push({
									monto: parseFloat($table.find('[name=monto]').eq(ii).val()),
									descr: $table.find('[name=descr]').eq(ii).val(),
									tipo: $table.find('[name=tipo] option:selected').eq(ii).val()
								});
							}
							data.items.push(item);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/marq/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Acta agregada!"});
							inMarq.init();
						},'json');
					}
				},
				*/
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inMarq.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				var ubig = new Ubig();
				//$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height('1200px'))+240+'px');
				p.$w.find('#form').steps({
					bodyTag: "fieldset",
					// Desabilita el boton de finalizar (requerido si la paginacion esta habilitada)
					enableFinishButton: false, 
					// Habilita los botones de antes y despues (opcional)
					enablePagination: true, 
					onStepChanging: function (event, currentIndex, newIndex)
					{
						// Siempre permitir ir atras incluso cuando los campos actuales tengan campos incorrectos!
						if (currentIndex > newIndex)
						{
							return true;
						}
						// Forbid suppressing "Warning" step if the user is to young
						//if (newIndex === 3 && Number($("#age").val()) < 18)
						//{
						//	return false;
						//}
						var form = $(this);
						// Limpiar si el usuario retrocedio antes
						if (currentIndex < newIndex)
						{
							// Para remover error de estilo
							$(".body:eq(" + newIndex + ") label.error", form).remove();
							$(".body:eq(" + newIndex + ") .error", form).removeClass("error");
						}
				 
						// Deshabilita la validacion de los campos que estan deshabilitados o escondidos.
						form.validate().settings.ignore = ":disabled,:hidden";
				 
						// Comenzar la validacion; Prevenir ir hacia delante si es falso
						return form.valid();
					},
					onStepChanged: function (event, currentIndex, priorIndex)
					{
						// Suppress (skip) "Warning" step if the user is old enough and wants to the previous step.
						if (currentIndex === 5 && priorIndex === 6)
						{
							$(this).steps("previous");
							return;
						}
				 
						// Suppress (skip) "Warning" step if the user is old enough.
						if (currentIndex === 2 && Number($("#age").val()) >= 18)
						{
							$(this).steps("next");
						}
					},
					onFinishing: function (event, currentIndex)
					{
						var form = $(this);
				 
						// Disable validation on fields that are disabled.
						// At this point it's recommended to do an overall check (mean ignoring only disabled fields)
						form.validate().settings.ignore = ":disabled";
				 
						// Start validation; Prevent form submission if false
						return form.valid();
					},
					onFinished: function (event, currentIndex)
					{
						var form = $(this);
						// Submit form input
						form.submit();
					}
				}).validate({
					errorPlacement: function (error, element)
					{
						element.before(error);
					},
					rules: {
						confirm: {
							equalTo: "#password"
						}
					}
				});
				//MODIFICACION DEL FORMULARIO
				//DESTINO
				var $cbo_depa = p.$w.find('#direccion_depa');
				var $cbo_prov = p.$w.find('#direccion_prov');
				var $cbo_dist = p.$w.find('#direccion_dist');
				$cbo_depa.change(function(){
					$cbo_prov.empty();
					ubig.get_pro(""+p.$w.find('[name=direccion_depa] :selected').val(), function(list_pro){
						list_pro.forEach(function(prov){
							if(prov.provincia!='00')
								$cbo_prov.append('<option value="'+prov.provincia+'">'+prov.nombre+'</option>');
						});
						$cbo_prov.change();	
					});
				});
				$cbo_prov.change(function(){
					$cbo_dist.empty();
					ubig.get_dis(""+p.$w.find('[name=direccion_depa] :selected').val(), ""+p.$w.find('[name=direccion_prov] :selected').val(), function(list_dis){
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

				p.$w.find('#tipo').change(function(){
	   				var $this = $(this),
	   				val = p.tipo[$this.find('option:selected').val()]._id.$id,
	   				$cbo = p.$w.find('#sublocal').empty();
	   				if(p.sublocal!=null){
			   			for(var i=0,j=p.sublocal.length; i<j; i++){
			   				if(p.sublocal[i].tipo._id.$id==val)
			   					$cbo.append('<option value="'+i+'">'+p.sublocal[i].nomb+'</option>');
			   			}
			   		}
			   		$cbo.chosen().trigger("chosen:updated");
		   			$cbo.change();
	   			});
	   			p.$w.find('#sublocal').change(function(){
	   				p.$w.find('#inmueble').empty();
	   				if($(this).find('option').length==0){
	   					p.$w.find('#inmueble').change();
	   					return K.msg({
	   						title: ciHelper.titles.infoReq,
	   						text: 'Debe escoger un local v&aacute;lido!',
	   						type: 'error'
	   					});
	   				}
	   				K.block();
				});
				new K.grid({
					$el: p.$w.find('#gridTienda'),
					search: false,
					pagination: false,
					cols: ['','Estado de Conservaci&oacute;n','Estado','Situaci&oacute;n Legal','Estado de saneamiento','Situaci&oacute;n no legal de saneeamiento'],
					onlyHtml:true,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(e){
							e.preventDefault();
							inMarq.windowNewSubinmueble(p);
						});
					},
					onLoading: function(){ K.block(); },
					onLoaded: function(){ K.unblock(); },
					onComplete: function(){ },

				});
				

				new K.grid({
					$el: p.$w.find('#gridOficina'),
					search: false,
					pagination: false,
					cols: ['','Estado de Conservaci&oacute;n','Estado','Situaci&oacute;n Legal','Estado de saneamiento','Situaci&oacute;n no legal de saneeamiento'],
					onlyHtml:true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Nueva Oficina</button>',
					onLoading: function(){ /* K.block(); */ },
					onComplete: function(){
					},
				});

				new K.grid({
					$el: p.$w.find('#gridCasaHabitacion'),
					search: false,
					pagination: false,
					cols: ['','Estado de Conservaci&oacute;n','Estado','Situaci&oacute;n Legal','Estado de saneamiento','Situaci&oacute;n no legal de saneeamiento'],
					onlyHtml:true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Nueva Casa Habitacion</button>',
					onLoading: function(){ /* K.block(); */ },
					onComplete: function(){

					},
				});
				new K.grid({
					$el: p.$w.find('#gridStands'),
					search: false,
					pagination: false,
					cols: ['','Estado de Conservaci&oacute;n','Estado','Situaci&oacute;n Legal','Estado de saneamiento','Situaci&oacute;n no legal de saneeamiento'],
					onlyHtml:true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Nuevo Stand</button>',
					onLoading: function(){ /* K.block(); */ },
					onComplete: function(){

					},
				});
				// POSTS
				/*
				*	MOTIVOS
				*/
				/*
				$.post('in/inmu/get_all_sub',{_id: p.sublocal[$(this).find('option:selected').val()]._id.$id},function(data){
	   					var $cbo = p.$w.find('#inmueble').empty();
	   					if(data!=null){
				   			for(var i=0,j=data.length; i<j; i++){
				   				$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].abrev+'</option>');
				   				$cbo.find('option:last').data('data',data[i]);
				   			}
				   		}
			   			$cbo.chosen().trigger("chosen:updated");
			   			$cbo.change();
	   					K.unblock();
	   			},'json');
				$.post('in/moti/all',function(data){
					var $cbo = p.$w.find('#motivo');
					if(data!=null)
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					else{
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe crear primero MOTIVOS DE CONTRATO',
							type: 'error'
						});
						return inMovi.init();
					}
					K.unblock();
				},'json');
				*/
				/*
				* ARRENDATARIO
				*/
				/*
					p.$w.find('#arrendatario .panel-title').html('ARRENDATARIO');
					p.$w.find('#arrendatario [name=btnSel]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=arrendatario] [name=mini_enti]'),data);
						},bootstrap: true});
					});
					p.$w.find('#arrendatario [name=btnAct]').click(function(){
						if(p.$w.find('#arrendatario [name=mini_enti]').data('data')==null){
							K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe elegir una entidad!',
								type: 'error'
							});
						}else{
							mgEnti.windowEdit({callback: function(data){
								mgEnti.fillMini(p.$w.find('#arrendatario [name=mini_enti]'),data);
							},id: p.$w.find('#arrendatario [name=mini_enti]').data('data')._id.$id});
						}
					});
				*/

				/*
				* AVAL
				*/
				/*
				p.$w.find('#aval .panel-title').html('AVAL');
				p.$w.find('#aval [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('#aval [name=mini_enti]'),data);
					},bootstrap: true});
				});
				*/
				/*
					p.$w.find('#aval [name=btnAct]').click(function(){
						if(p.$w.find('#aval [name=mini_enti]').data('data')==null){
							K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe elegir una entidad!',
								type: 'error'
							});
						}else{
							mgEnti.windowEdit({callback: function(data){
								mgEnti.fillMini(p.$w.find('#aval [name=mini_enti]'),data);
							},id: p.$w.find('#aval [name=mini_enti]').data('data')._id.$id});
						}
					});
				*/
				/*
					$.post('in/movi/get_tipo_sub',function(data){
			   			//p.$w.find('[name=gridArre] .fuelux:first,[name=gridCont] .fuelux:first').css('max-height','420px');
						//$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height('auto'))+240+'px');
			   			p.tipo = data.tipo;
			   			p.sublocal = data.sublocal;
			   			var $cbo = p.$w.find('#tipo');
			   			for(var i=0,j=p.tipo.length; i<j; i++){
			   				$cbo.append('<option value="'+i+'">'+p.tipo[i].nomb+'</option>');
			   			}
			   			var inmu_tmp = $.jStorage.get('in/movi/get_all_cont');
			   			if(p.inmueble==null){
			   				if(inmu_tmp!=null) p.inmueble = inmu_tmp;
			   			}
			   			if(p.inmueble==null){
			   				p.$w.find('#tipo').chosen();
			   				$cbo.change();
			   			}else{
			   				for(var i=0,j=p.tipo.length; i<j; i++){
			   					if(p.tipo[i]._id.$id==p.inmueble.tipo._id){
			   						p.$w.find('#tipo option').eq(i).attr('selected','selected');
			   						break;
			   					}
			   				}
					   		p.$w.find('#tipo').chosen();
			   				var val = p.inmueble.tipo._id,
			   				$cbo = p.$w.find('#sublocal').empty();
			   				if(p.sublocal!=null){
					   			for(var i=0,j=p.sublocal.length; i<j; i++){
					   				if(p.sublocal[i].tipo._id.$id==val){
					   					$cbo.append('<option value="'+i+'">'+p.sublocal[i].nomb+'</option>');
					   					if(p.sublocal[i]._id.$id==p.inmueble.sublocal._id)
					   						$cbo.find('option:last').attr('selected','selected');
					   				}
					   			}
					   		}
					   		p.$w.find('#sublocal').chosen();
			   				p.$w.find('#inmueble').empty();
			   				$.post('in/inmu/get_all_sub',{_id: p.inmueble.sublocal._id},function(data){
			   					var $cbo = p.$w.find('#inmueble').empty();
			   					if(data!=null){
						   			for(var i=0,j=data.length; i<j; i++){
						   				$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].direccion+'</option>');
						   				$cbo.find('option:last').data('data',data[i]);
						   			}
						   		}
			   					$cbo.selectVal(p.inmueble._id);
					   			$cbo.chosen().change();
			   				},'json');
			   			}
			   		},'json');
		   		*/
					/*
					p.$w.find('[name=btnArre]').click(function(){
						mgEnti.windowSelect({callback: function(data){
							p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data)).data('data',data);
						},bootstrap: true});
					});
					p.$w.find('[name=btnInm]').click(function(){
						inInmu.windowSelect({callback: function(data){
							p.$w.find('[name=inmueble]').html(data.direccion).data('data',data);
						}});
					});
					p.$w.find('[name=cuotas]').change(function(){
						var cuotas = $(this).val();
						if(cuotas=='') cuotas = 0;
						else cuotas = parseInt(cuotas);
						p.$w.find('[name=grillas]').empty();
						for(var i=0,j=cuotas; i<j; i++){
							p.$w.find('[name=grillas]').append('<div>');
							var $tmp = p.$w.find('[name=grillas] div:last');
							new K.grid({
								$el: $tmp,
								search: false,
								pagination: false,
								cols: ['Concepto a Cobrar','Tipo','Total',''],
								onlyHtml: true
							});
							//
							// fecha de vencimiento
							//
							var $row = $('<tr class="item">');
							$row.append('<td>Fecha de Vencimiento</td>');
							$row.append('<td colspan="3"><input class="form-control" type="text" name="fecven"/></td>');
							$row.find('[name=fecven]').val(K.date()).datepicker();
							$tmp.find('tbody').append($row);
							//
							// TOTAL
							//
							var $row = $('<tr class="item">');
							$row.append('<td>Total Cuota '+(i+1)+'</td>');
							$row.append('<td colspan="2"><span class="form-control" name="total">0</span></td>');
							$row.append('<td><button class="btn btn-info" name="btnAgr"><i class="fa fa-plus"></i></button></td>');
							$row.find('[name=btnAgr]').click(function(){
								var $row = $('<tr class="item">'),
								$table = $(this).closest('table');
								$row.append('<td><input class="form-control" type="text" name="descr"/></td>');
								$row.append('<td><input class="form-control" type="text" name="monto" value="0"/></td>');
								$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
								$row.find('[name=monto]').change(function(){
									var $table = $(this).closest('table'),
									total = 0;
									for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
										total += parseFloat($table.find('[name=monto]').eq(i).val());
									}
									$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
								}).keyup(function(){
									$(this).change();
								}).numeric().change();
								$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									var $row = $(this).closest('.item');
									$row.remove();
									p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
									p.$w.find('[name=monto]').change();
								});
								$table.find('tbody').append($row);
							});
							$tmp.find('tbody').append($row);
						}
						$(this).val(cuotas);
						p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
						p.$w.find('[name=btnAgr]').click();
					}).keyup(function(){
						$(this).change();
					}).numeric().change();
					*/
			},
		});
	},
	windowNewSubinmueble: function(p){
		//if(p.texto==null) p.texto = '';
		if(p==null) p = {};
		else console.log(p);
		new K.Modal({
			id: 'windowNewSubinmueble',
			content: '<div name="tmp"></div>',
			//allScreen: true,
			title: 'Editar Subinmueble',
			contentURL: 'in/marq/edit_marq_tienda',
			width: 550,
			height: 260,
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						K.clearNoti();
						/* VARIABLE INICIAL */
						var excepcion = {
							mensaje: "ERROR NO ESPECIFICO",
							dato: [],
						}
						var _subinmueble = {
							tipo: '',
							numero: '',
							estado_conservacion: '',
							ocupacion: {
								estado_subinmueble: 'DES',
								estado_ocupacion: '--',
							},
							situacion_legal: {
								estado: 'DES',
								asiento: '--',
								hoja: '--',
								tomo: '--',
								ficha_partida: '--',
								modalidad: '--',
							},
							metainformacion: {
								fecreg: '',
								fecmod: '',
								autor: '',
								trabajador: '',
								historico: [],
							},
						};
						try {
							/* MODIFICACION */
							numero = p.$w.find('#numero').val();
							tipo = p.$w.find('#tipo_subinmueble').val();

							/* VALIDACION */
							if (typeof tipo == "undefined" || tipo == null){
								excepcion.mensaje = "El campo tipo no esta seteado";
								excepcion.dato = tipo;
								throw excepcion;
							}
							if (tipo == ''){
								excepcion.mensaje = "El campo tipo esta vacio";
								excepcion.dato = tipo;
								throw excepcion;
							}
							if (typeof numero == "undefined" || numero == null){
								excepcion.mensaje = "El campo numero no esta seteado";
								excepcion.dato = numero;
								throw excepcion;
							}
							if (numero == ''){
								excepcion.mensaje = "El campo numero esta vacio";
								excepcion.dato = numero;
								throw excepcion;
							}
							if(isNaN(numero)){
								excepcion.mensaje = "El campo numero no es un numero";
								excepcion.dato = numero;
								throw excepcion;
							}
							

							p.callback(_subinmueble);

						} catch (excepcion) {
						  	K.clearNoti();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: excepcion.mensaje,
								type: 'error'
							});
							console.log("excepcion:")
							console.log(excepcion);
						}


						/*if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}
						if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}else{
							
						}*/
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
			onClose: function(){
				//$('#mainPanel [name=prod]').focus();
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewSubinmueble');
				/*	INICIALIZACION */
				p.$w.find('#select_ocup').hide();
					p.$w.find('#modalidad').removeAttr('required');
				p.$w.find('#form_sane').hide();
					p.$w.find('#asiento').attr('removeAttr', '');
					p.$w.find('#hoja').attr('removeAttr', '');
					p.$w.find('#tomo').attr('removeAttr', '');
					p.$w.find('#ficha_partida').removeAttr('required', '');
				p.$w.find('#form_legal').show()
					p.$w.find('#modalidad').attr('required');

				/*	MODIFICACION POR EVENTOS */
				p.$w.find('#estado').change(function(){
					if(p.$w.find('#estado').val()=="OCU") {
						p.$w.find('#select_ocup').show();
							//p.$w.find('#estado_ocup').attr('required', '');
					} else if(p.$w.find('#estado').val()=="DES") {
						p.$w.find('#select_ocup').hide();
							//p.$w.find('#estado_ocup').removeAttr('required', '');
					} else {
						K.clearNoti();
						return K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Se encontro un nuevo estado de ocupacion no contemplado en el diseño!',
							type: 'error'
						});
					}
				});
				p.$w.find('#estado_situ').change(function(){
					if(p.$w.find('#estado_situ').val() == "SAN") {
						p.$w.find('#form_sane').show();
							p.$w.find('#asiento').attr('required', '');
							p.$w.find('#hoja').attr('required', '');
							p.$w.find('#tomo').attr('required', '');
							p.$w.find('#ficha_partida').attr('required', '');
						p.$w.find('#form_legal').hide();
							p.$w.find('#modalidad').removeAttr('required');
					} else if(p.$w.find('#estado_situ').val() == "NOS") {
						p.$w.find('#form_sane').hide();
							p.$w.find('#asiento').removeAttr('required');
							p.$w.find('#hoja').removeAttr('required');
							p.$w.find('#tomo').removeAttr('required');
							p.$w.find('#ficha_partida').removeAttr('required');
						p.$w.find('#form_legal').show();
							p.$w.find('#modalidad').attr('required', '');
					} else {
						K.clearNoti();
						return K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Se encontro un nuevo estado de ocupacion no contemplado en el diseño!',
							type: 'error'
						});
					}
				});
				/*
					$.post('in/acta/get',{_id: p.id},function(data){
						p.$w.find('[name=cuotas]').val(data.cuotas).change();
						p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data.arrendatario)).data('data',data.arrendatario);
						p.$w.find('[name=inmueble]').html(data.inmueble.direccion).data('data',data.inmueble);
						p.$w.find('[name=num]').val(data.num);
						for(var i=0,j=data.items.length; i<j; i++){
							var $table = p.$w.find('.fuelux').eq(i),
							result = data.items[i];
							$table.find('[name=fecven]').val(ciHelper.date.format.bd_ymd(result.fecven));
							$table.find('[name=total]').html(ciHelper.formatMon(result.total));
							$table.find('tbody tr:last').remove();
							for(var ii=0,jj=result.conceptos.length; ii<jj; ii++){
								var $row = $('<tr class="item">');
								$row.append('<td><input class="form-control" type="text" name="descr" value="'+result.conceptos[ii].descr+'"/></td>');
								$row.append('<td><input class="form-control" type="text" name="monto" value="'+result.conceptos[ii].monto+'"/></td>');
								$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
								$row.find('[name=monto]').change(function(){
									var $table = $(this).closest('table'),
									total = 0;
									for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
										total += parseFloat($table.find('[name=monto]').eq(i).val());
									}
									$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
								}).keyup(function(){
									$(this).change();
								}).numeric();
								$row.find('[name=tipo]').selectVal(result.conceptos[ii].tipo);
								$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									var $row = $(this).closest('.item');
									$row.remove();
									p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
									p.$w.find('[name=monto]').change();
								});
								$table.find('tbody').append($row);
								$table.find('[name=monto]:last').change();
							}
						}
						K.unblock({$element: p.$w});
					},'json');
				*/
			}
		});
	}
	/*
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Panel({ 
			title: 'Editar Acta de Conciliaci&oacute;n',
			contentURL: 'in/acta/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							cuotas: p.$w.find('[name=cuotas]').val(),
							num: p.$w.find('[name=num]').val(),
							arrendatario: p.$w.find('[name=arrendatario]').data('data'),
							inmueble: p.$w.find('[name=inmueble]').data('data'),
							items: []
						};
						if(data.arrendatario==null){
							p.$w.find('[name=btnArre]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el arrendatario!',type: 'error'});
						}else data.arrendatario = mgEnti.dbRel(data.arrendatario);
						if(data.inmueble==null){
							p.$w.find('[name=btnInm]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un inmueble!',type: 'error'});
						}else data.inmueble = inInmu.dbRel(data.inmueble);
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						if(data.cuotas==''){
							p.$w.find('[name=cuotas]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el total de cuotas de Acta de Conciliaci&oacute;n!',type: 'error'});
						}
						for(var i=0,j=parseInt(data.cuotas); i<j; i++){
							var $table = p.$w.find('[name=grillas] tbody').eq(i),
							item = {
								num: i+1,
								fecven: $table.find('[name=fecven]').val(),
								conceptos: [],
								total: $table.find('[name=total]').data('data')
							};
							if(item.fecven==''){
								p.$w.find('[name=fecven]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de vencimiento!',type: 'error'});
							}
							for(var ii=0,jj=$table.find('[name=monto]').length; ii<jj; ii++){
								if($table.find('[name=descr]').eq(ii).val()==''){
									$table.find('[name=descr]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
								}
								if($table.find('[name=monto]').eq(ii).val()==''){
									$table.find('[name=monto]').eq(ii).focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
								}
								item.conceptos.push({
									monto: parseFloat($table.find('[name=monto]').eq(ii).val()),
									descr: $table.find('[name=descr]').eq(ii).val(),
									tipo: $table.find('[name=tipo] option:selected').eq(ii).val()
								});
							}
							data.items.push(item);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/acta/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Acta actualizada!"});
							inActa.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inActa.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=btnArre]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data)).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnInm]').click(function(){
					inInmu.windowSelect({callback: function(data){
						p.$w.find('[name=inmueble]').html(data.direccion).data('data',data);
					}});
				});
				p.$w.find('[name=cuotas]').change(function(){
					var cuotas = $(this).val();
					if(cuotas=='') cuotas = 0;
					else cuotas = parseInt(cuotas);
					p.$w.find('[name=grillas]').empty();
					for(var i=0,j=cuotas; i<j; i++){
						p.$w.find('[name=grillas]').append('<div>');
						var $tmp = p.$w.find('[name=grillas] div:last');
						new K.grid({
							$el: $tmp,
							search: false,
							pagination: false,
							cols: ['Concepto a Cobrar','Tipo','Total',''],
							onlyHtml: true
						});
						//
						// fecha de vencimiento
						//
						var $row = $('<tr class="item">');
						$row.append('<td>Fecha de Vencimiento</td>');
						$row.append('<td colspan="3"><input class="form-control" type="text" name="fecven"/></td>');
						$row.find('[name=fecven]').val(K.date()).datepicker();
						$tmp.find('tbody').append($row);
						//
						// TOTAL
						//
						var $row = $('<tr class="item">');
						$row.append('<td>Total Cuota '+(i+1)+'</td>');
						$row.append('<td colspan="2"><span class="form-control" name="total">0</span></td>');
						$row.append('<td><button class="btn btn-info" name="btnAgr"><i class="fa fa-plus"></i></button></td>');
						$row.find('[name=btnAgr]').click(function(){
							var $row = $('<tr class="item">'),
							$table = $(this).closest('table');
							$row.append('<td><input class="form-control" type="text" name="descr"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="0"/></td>');
							$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric().change();
							$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								var $row = $(this).closest('.item');
								$row.remove();
								p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
								p.$w.find('[name=monto]').change();
							});
							$table.find('tbody').append($row);
						});
						$tmp.find('tbody').append($row);
					}
					$(this).val(cuotas);
					p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
					p.$w.find('[name=btnAgr]').click();
				}).keyup(function(){
					$(this).change();
				}).numeric().change();
				$.post('in/acta/get',{_id: p.id},function(data){
					p.$w.find('[name=cuotas]').val(data.cuotas).change();
					p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data.arrendatario)).data('data',data.arrendatario);
					p.$w.find('[name=inmueble]').html(data.inmueble.direccion).data('data',data.inmueble);
					p.$w.find('[name=num]').val(data.num);
					for(var i=0,j=data.items.length; i<j; i++){
						var $table = p.$w.find('.fuelux').eq(i),
						result = data.items[i];
						$table.find('[name=fecven]').val(ciHelper.date.format.bd_ymd(result.fecven));
						$table.find('[name=total]').html(ciHelper.formatMon(result.total));
						$table.find('tbody tr:last').remove();
						for(var ii=0,jj=result.conceptos.length; ii<jj; ii++){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="descr" value="'+result.conceptos[ii].descr+'"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="'+result.conceptos[ii].monto+'"/></td>');
							$row.append('<td><select class="form-control" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric();
							$row.find('[name=tipo]').selectVal(result.conceptos[ii].tipo);
							$row.append('<td><button class="btn btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								var $row = $(this).closest('.item');
								$row.remove();
								p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
								p.$w.find('[name=monto]').change();
							});
							$table.find('tbody').append($row);
							$table.find('[name=monto]:last').change();
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = {};
		new K.Panel({ 
			title: 'Acta de Conciliaci&oacute;n',
			contentURL: 'in/acta/edit',
			buttons: {
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inActa.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnArre]').remove();
				p.$w.find('[name=btnInm]').remove();
				p.$w.find('[name=num]').attr('disabled','disabled');
				p.$w.find('[name=cuotas]').change(function(){
					var cuotas = $(this).val();
					if(cuotas=='') cuotas = 0;
					else cuotas = parseInt(cuotas);
					p.$w.find('[name=grillas]').empty();
					for(var i=0,j=cuotas; i<j; i++){
						p.$w.find('[name=grillas]').append('<div>');
						var $tmp = p.$w.find('[name=grillas] div:last');
						new K.grid({
							$el: $tmp,
							search: false,
							pagination: false,
							cols: ['Concepto a Cobrar','Tipo','Total'],
							onlyHtml: true
						});
						//
						// fecha de vencimiento
						//
						var $row = $('<tr class="item">');
						$row.append('<td>Fecha de Vencimiento</td>');
						$row.append('<td colspan="3"><input class="form-control" type="text" name="fecven" disabled="disabled"/></td>');
						$row.find('[name=fecven]').val(K.date());
						$tmp.find('tbody').append($row);
						//
						// TOTAL
						//
						var $row = $('<tr class="item">');
						$row.append('<td>Total Cuota '+(i+1)+'</td>');
						$row.append('<td colspan="2"><span class="form-control" name="total">0</span></td>');
						$row.append('<td><button class="btn btn-info" name="btnAgr"><i class="fa fa-plus"></i></button></td>');
						$row.find('[name=btnAgr]').click(function(){
							var $row = $('<tr class="item">'),
							$table = $(this).closest('table');
							$row.append('<td><input class="form-control" type="text" name="descr" disabled="disabled"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" value="0" disabled="disabled"/></td>');
							$row.append('<td><select class="form-control" name="tipo" disabled="disabled"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric().change();
							$table.find('tbody').append($row);
						}).hide();
						$tmp.find('tbody').append($row);
					}
					$(this).val(cuotas);
					p.$w.find('[name=total]').html(ciHelper.formatMon(0)).data('data',0);
					p.$w.find('[name=btnAgr]').click();
				}).hide();
				$.post('in/acta/get',{_id: p.id},function(data){
					p.$w.find('[name=cuotas]').val(data.cuotas).change();
					p.$w.find('[name=arrendatario]').html(mgEnti.formatName(data.arrendatario)).data('data',data.arrendatario);
					p.$w.find('[name=inmueble]').html(data.inmueble.direccion).data('data',data.inmueble);
					p.$w.find('[name=num]').val(data.num);
					for(var i=0,j=data.items.length; i<j; i++){
						var $table = p.$w.find('.fuelux').eq(i),
						result = data.items[i];
						$table.find('[name=fecven]').val(ciHelper.date.format.bd_ymd(result.fecven));
						$table.find('[name=total]').html(ciHelper.formatMon(result.total));
						$table.find('tbody tr:last').remove();
						for(var ii=0,jj=result.conceptos.length; ii<jj; ii++){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="descr" disabled="disabled" value="'+result.conceptos[ii].descr+'"/></td>');
							$row.append('<td><input class="form-control" type="text" name="monto" disabled="disabled" value="'+result.conceptos[ii].monto+'"/></td>');
							$row.append('<td><select class="form-control" disabled="disabled" name="tipo"><option value="A">Alquiler</option><option value="I">IGV</option><option value="M">Moras</option></select></td>');
							$row.find('[name=monto]').change(function(){
								var $table = $(this).closest('table'),
								total = 0;
								for(var i=0,j=$table.find('[name=monto]').length; i<j; i++){
									total += parseFloat($table.find('[name=monto]').eq(i).val());
								}
								$table.find('[name=total]').html(ciHelper.formatMon(total)).data('data',total);
							}).keyup(function(){
								$(this).change();
							}).numeric();
							$row.find('[name=tipo]').val(result.conceptos[ii].tipo);
							$table.find('tbody').append($row);
							$table.find('[name=monto]:last').change();
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
*/
};
define(
	['mg/enti','ct/pcon','in/inmu','mg/ubig'],
	function(mgEnti,ctPcon,inInmu,Ubig){
		return inMarq;
	}
);