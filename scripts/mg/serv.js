mgServ = {
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
	dbRel: function(serv){
		return {
			_id: serv._id.$id,
			nomb: serv.nomb,
			organizacion: {
				_id: serv.organizacion._id.$id,
				nomb: serv.organizacion.nomb
			}
		};
	},
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgServ',
			titleBar: {
				title: 'Servicios'
			}
		});

		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Organizaci&oacute;n',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'mg/serv/lista',
					params: {},
					itemdescr: 'Servicio(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mgServ.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgServ.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.organizacion.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							mgServ.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									mgServ.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									mgServ.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Servicio de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mg/serv/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Servicio de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mgServ.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Servicio de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Servicio de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mg/serv/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Servicio de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mgServ.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Servicio de Local');
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowDetails: function(p){
		new K.Modal({
			id: 'windowServ',
			title: 'Detalles de Servicio',
			contentURL: 'mg/serv/edit',
			width: 620,
			height: 350,
			onContentLoaded: function(){
				p.$w = $('#windowServ');
				K.block();
				p.$w.find('[name=btnSel]').remove();
				$.post('mg/serv/get',{_id: p.id},function(data){
					$.extend(data.organizacion,{
						actividad: data.actividad,
						componente: data.componente
					});
					p.$w.find('[name=orga]').html(data.organizacion.nomb).attr('disabled','disabled');
					p.$w.find('[name=nomb]').val(data.nomb).attr('disabled','disabled');
					p.$w.find('[name=descr]').val(data.descr).attr('disabled','disabled');
					p.$w.find('[name=aplicacion]').selectVal(data.aplicacion).attr('disabled','disabled');
					p.$w.find('[name=tipo]').selectVal(data.tipo).attr('disabled','disabled');
					K.unblock();
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Nuevo Servicio',
			contentURL: 'mg/serv/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							organizacion: p.$w.find('[name=orga]').data('data'),
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							modulo: p.$w.find('[name=modulo] option:selected').val(),
							aplicacion: p.$w.find('[name=aplicacion] option:selected').val()
						};
						if(data.organizacion==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
						}else{
							data.actividad = {
								_id: data.organizacion.actividad._id.$id,
								cod: data.organizacion.actividad.cod,
								nomb: data.organizacion.actividad.nomb
							};
							data.componente = {
								_id: data.organizacion.componente._id.$id,
								cod: data.organizacion.componente.cod,
								nomb: data.organizacion.componente.nomb
							};
							data.organizacion = {
								_id: data.organizacion._id.$id,
								nomb: data.organizacion.nomb
							};
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el servicio!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el servicio!',type: 'error'});
						}
						if(data.modulo==''){
							p.$w.find('[name=modulo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe un modulo para el servicio!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/serv/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Servicio agregado!"});
							mgServ.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						mgServ.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				//p.$w.find('[name=tipo]').closest('.form-group').hide();
				p.$w.find('[name=btnSel]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						K.block();
						$.post('mg/serv/get_orga',{id: data._id.$id},function(exdata){
							p.$w.find('[name=orga]').html(exdata.nomb).data('data',exdata);
							K.unblock();
						},'json');
					}});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Panel({
			title: 'Editar Servicio '+p.nomb,
			contentURL: 'mg/serv/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							organizacion: p.$w.find('[name=orga]').data('data'),
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							aplicacion: p.$w.find('[name=aplicacion] option:selected').val(),
							modulo: p.$w.find('[name=modulo] option:selected').val()
						};
						if(data.organizacion==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
						}else{
							data.actividad = {
								_id: data.organizacion.actividad._id.$id,
								cod: data.organizacion.actividad.cod,
								nomb: data.organizacion.actividad.nomb
							};
							data.componente = {
								_id: data.organizacion.componente._id.$id,
								cod: data.organizacion.componente.cod,
								nomb: data.organizacion.componente.nomb
							};
							data.organizacion = {
								_id: data.organizacion._id.$id,
								nomb: data.organizacion.nomb
							};
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre para el servicio!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para el servicio!',type: 'error'});
						}
						if(data.modulo==''){
							p.$w.find('[name=modulo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe un modulo para el servicio!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/serv/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Servicio actualizado!"});
							mgServ.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						mgServ.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				//p.$w.find('[name=tipo]').closest('.form-group').hide();
				p.$w.find('[name=btnSel]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						K.block();
						$.post('mg/serv/get_orga',{id: data._id.$id},function(exdata){
							p.$w.find('[name=orga]').html(exdata.nomb).data('data',exdata);
							K.unblock();
						},'json');
					}});
				});
				$.post('mg/serv/get',{_id: p.id},function(data){
					$.extend(data.organizacion,{
						actividad: data.actividad,
						componente: data.componente
					});
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=abrev]').val(data.abrev);
					p.$w.find('[name=aplicacion]').selectVal(data.aplicacion);
					p.$w.find('[name=modulo]').selectVal(data.modulo);
					p.$w.find('[name=tipo]').selectVal(data.tipo);
					K.unblock();
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap!=null){
		var params = {};
		if(p.params!=null){
			params = p.params;
		}
		if(p.modulo!=null){
			if(p.modulo=='IN') params.organizacion = '51a50edc4d4a13441100000e';
		}
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Servicio de Local',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							var data = p.$w.find('.highlights').data('data');
							K.closeWindow(p.$w.attr('id'));
							p.callback(data);
						}else{
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
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
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'mg/serv/lista',
					params: params,
					itemdescr: 'Servicio(s)',
					onLoading: function(){
						K.block({$element: p.$w});
					},
					onComplete: function(){
						K.unblock({$element: p.$w});
					},
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
		}else{
			if(p.tipo==null) p.tipo = 'I';

			p.search = function(params){
				$.extend(params,{
					estado: 'H',
					orga: p.$w.find('[name=orga] option:selected').val(),
					tipo: p.tipo,

				});
				if(p.aplicacion!=null){
					//p.aplicacion = 'O';
					params.aplicacion = p.aplicacion;
				}
				$.post('mg/serv/search',params,function(data){
					if ( data !=null ) {
						for (i=0,j=data.items.length; i<j; i++) {
							var result = data.items[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( result.nomb );
							$li.eq(1).html( result.organizacion.nomb );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',result);
							p.$w.find(".gridBody").append( $row.children() );
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			};
			new K.Modal({
				id: 'windowSelectServ',
				title: 'Seleccionar Servicio',
				contentURL: 'mg/serv/select',
				icon: 'ui-icon-search',
				width: 510,
				height: 350,
				buttons: {
					"Seleccionar": function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un servicio!',type: 'error'});
						}
						var data = p.$w.find('.ui-state-highlight').closest('.item').data('data');
						K.closeWindow(p.$w.attr('id'));
						p.callback(data);
					},
					"Cancelar": function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowSelectServ');
					K.block({$element: p.$w});
					p.$w.find('.grid').height('320px');
					$.post('mg/serv/alll_orga',function(data){
						if(data==null){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({title: 'Servicios no encontrados',text: 'Debe ingresar antes servicios para poder seleccionar uno!', type:'info'});
						}
						var $select = p.$w.find('[name=orga]');
						var unique ={};
						for(var i=0,j=data.length; i<j; i++){
							//$select.append('<option value="'+data[i].organizacion._id.$id+'">'+data[i].organizacion.nomb+'</option>');
							//console.log('<option value="'+data[i].organizacion._id.$id+'">'+data[i].organizacion.nomb+'</option>');
							unique[data[i].organizacion._id.$id]=data[i].organizacion.nomb;
						}
						for(var key in unique){
							$select.append('<option value="'+key+'">'+unique[key]+'</option>');
						}
						$select.change(function(){
							p.$w.find('.gridBody').empty();
							p.search({});
						}).change();
					},'json');
				}
			});
		}
	},
	windowSelect_: function(p){
		if(p.bootstrap!=null){
		var params = {};
		if(p.params!=null){
			params = p.params;
		}
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Servicio de Local',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							var data = p.$w.find('.highlights').data('data');
							K.closeWindow(p.$w.attr('id'));
							p.callback(data);
						}else{
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
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
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Codigo','Nombre'],
					data: 'mg/serv/lista_mh',
					params: {modulo:"MH",estado:"H"},
					itemdescr: 'Servicio(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
							$el.find('[name=modulo]').change(function(){
								var modulo = $el.find('[name=modulo] option:selected').val();
								p.$grid.reinit({params: {modulo: modulo}});
							});

						},
					onLoading: function(){
						K.block({$element: p.$w});
					},
					onComplete: function(){
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.codigo+'</td>');
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
		}else{
			if(p.tipo==null) p.tipo = 'I';

			p.search = function(params){
				$.extend(params,{
					estado: 'H',
					orga: p.$w.find('[name=orga] option:selected').val(),
					tipo: p.tipo,

				});
				if(p.aplicacion!=null){
					//p.aplicacion = 'O';
					params.aplicacion = p.aplicacion;
				}
				$.post('mg/serv/search',params,function(data){
					if ( data !=null ) {
						for (i=0,j=data.items.length; i<j; i++) {
							var result = data.items[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( result.nomb );
							$li.eq(1).html( result.organizacion.nomb );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',result);
							p.$w.find(".gridBody").append( $row.children() );
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			};
			new K.Modal({
				id: 'windowSelectServ',
				title: 'Seleccionar Servicio',
				contentURL: 'mg/serv/select',
				icon: 'ui-icon-search',
				width: 510,
				height: 350,
				buttons: {
					"Seleccionar": function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un servicio!',type: 'error'});
						}
						var data = p.$w.find('.ui-state-highlight').closest('.item').data('data');
						K.closeWindow(p.$w.attr('id'));
						p.callback(data);
					},
					"Cancelar": function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowSelectServ');
					K.block({$element: p.$w});
					p.$w.find('.grid').height('320px');
					$.post('mg/serv/all_orga',function(data){
						if(data==null){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({title: 'Servicios no encontrados',text: 'Debe ingresar antes servicios para poder seleccionar uno!', type:'info'});
						}
						var $select = p.$w.find('[name=orga]');
						for(var i=0,j=data.length; i<j; i++){
							$select.append('<option value="'+data[i].organizacion._id.$id+'">'+data[i].organizacion.nomb+'</option>');
						}
						$select.change(function(){
							p.$w.find('.gridBody').empty();
							p.search({});
						}).change();
					},'json');
				}
			});
		}
	},
	windowSelect_Circuito: function(p){
		if(p.bootstrap!=null){
		var params = {};
		if(p.params!=null){
			params = p.params;
		}
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Servicio',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							var data = p.$w.find('.highlights').data('data');
							K.closeWindow(p.$w.attr('id'));
							p.callback(data);
						}else{
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
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
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Codigo','Nombre'],
					data: 'mg/serv/lista_cm',
					params: {_id:"6356c8163e6037b9608b456a"},
					itemdescr: 'Servicio(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
							$el.find('[name=modulo]').change(function(){
								var modulo = $el.find('[name=modulo] option:selected').val();
								p.$grid.reinit({params: {modulo: modulo}});
							});

						},
					onLoading: function(){
						K.block({$element: p.$w});
					},
					onComplete: function(){
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.codigo+'</td>');
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
		}else{
			if(p.tipo==null) p.tipo = 'I';

			p.search = function(params){
				$.extend(params,{
					estado: 'H',
					orga: p.$w.find('[name=orga] option:selected').val(),
					tipo: p.tipo,

				});
				if(p.aplicacion!=null){
					//p.aplicacion = 'O';
					params.aplicacion = p.aplicacion;
				}
				$.post('mg/serv/search',params,function(data){
					if ( data !=null ) {
						for (i=0,j=data.items.length; i<j; i++) {
							var result = data.items[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( result.nomb );
							$li.eq(1).html( result.organizacion.nomb );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',result);
							p.$w.find(".gridBody").append( $row.children() );
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			};
			new K.Modal({
				id: 'windowSelectServ',
				title: 'Seleccionar Servicio',
				contentURL: 'mg/serv/select',
				icon: 'ui-icon-search',
				width: 510,
				height: 350,
				buttons: {
					"Seleccionar": function(){
						if(p.$w.find('.ui-state-highlight').length<=0){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un servicio!',type: 'error'});
						}
						var data = p.$w.find('.ui-state-highlight').closest('.item').data('data');
						K.closeWindow(p.$w.attr('id'));
						p.callback(data);
					},
					"Cancelar": function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowSelectServ');
					K.block({$element: p.$w});
					p.$w.find('.grid').height('320px');
					$.post('mg/serv/all_orga',function(data){
						if(data==null){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({title: 'Servicios no encontrados',text: 'Debe ingresar antes servicios para poder seleccionar uno!', type:'info'});
						}
						var $select = p.$w.find('[name=orga]');
						for(var i=0,j=data.length; i<j; i++){
							$select.append('<option value="'+data[i].organizacion._id.$id+'">'+data[i].organizacion.nomb+'</option>');
						}
						$select.change(function(){
							p.$w.find('.gridBody').empty();
							p.search({});
						}).change();
					},'json');
				}
			});
		}
	}
};
define(
	['mg/enti','mg/orga'],
	function(mgEnti,mgOrga){
		return mgServ;
	}
);
