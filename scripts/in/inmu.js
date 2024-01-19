inInmu = {
	dbRel: function(item){
		return {
			_id: item._id.$id,
			direccion: item.direccion,
			sublocal: {
				_id: item.sublocal._id.$id,
				nomb: item.sublocal.nomb
			},
			tipo: {
				_id: item.tipo._id.$id,
				nomb: item.tipo.nomb
			}
		};
	},
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
	init: function(){
		K.initMode({
			mode: 'in',
			action: 'inInmu',
			titleBar: {
				title: 'Inmuebles'
			}
		});

		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Direcci&oacute;n',f:'direccion'},'Abrev.',{n:'SubLocal',f:'sublocal.nomb'},{n:'Inmueble',f:'sublocal.tipo.nomb'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'in/inmu/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							inInmu.windowNew();
						});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+inInmu.states[data.estado].label+'</td>');
						$row.append('<td>'+data.direccion+'</td>');
						$row.append('<td>'+data.abrev+'</td>');
						$row.append('<td>'+data.sublocal.nomb+'</td>');
						$row.append('<td>'+data.tipo.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							inInmu.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									inInmu.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									inInmu.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Inmueble <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/inmu/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Inmueble Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inInmu.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Inmueble');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Inmueble <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/inmu/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Inmueble Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inInmu.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Inmueble');
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
			title: 'Nuevo Inmueble',
			contentURL: 'in/inmu/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							tipo: p.$w.find('[name=tipo]').data('data'),
							abrev: p.$w.find('[name=abrev]').val(),
							direccion: p.$w.find('[name=direccion]').val(),
							distrito: p.$w.find('[name=distrito]').val(),
							provincia: p.$w.find('[name=provincia]').val(),
							departamento: p.$w.find('[name=departamento]').val(),
							partida_registral: p.$w.find('[name=partida_registral]').val(),
							area_terreno: p.$w.find('[name=area_terreno]').val(),
							area_construida: p.$w.find('[name=area_construida]').val(),
							precio: p.$w.find('[name=precio]').val(),
							moneda: p.$w.find('[name=moneda] option:selected').val(),
							autovaluo: p.$w.find('[name=autovaluo]').val()
						};
						if(data.tipo==null){
							p.$w.find('[name=btnTipo]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de local!',type: 'error'});
						}else data.tipo = inTipo.dbRel(data.tipo);
						if(p.$w.find('[name=sublocal] option').length==0){
							p.$w.find('[name=btnTipo]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de local con sublocales!',type: 'error'});
						}else{
							data.sublocal = inSubl.dbRel(p.$w.find('[name=sublocal] option:selected').data('data'));
						}
						if(data.direccion==''){
							p.$w.find('[name=direccion]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la direcci&oacute;n del Inmueble!',type: 'error'});
						}
						if(data.distrito==''){
							p.$w.find('[name=distrito]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el distrito del Inmueble!',type: 'error'});
						}
						if(data.provincia==''){
							p.$w.find('[name=provincia]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la provincia del Inmueble!',type: 'error'});
						}
						if(data.departamento==''){
							p.$w.find('[name=departamento]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el departamento del Inmueble!',type: 'error'});
						}
						if(data.partida_registral==''){
							p.$w.find('[name=partida_registral]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la partida registral del Inmueble!',type: 'error'});
						}
						if(data.area_terreno==''){
							p.$w.find('[name=area_terreno]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el area de terreno del Inmueble!',type: 'error'});
						}
						if(data.area_construida==''){
							p.$w.find('[name=area_construida]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el area construida del Inmueble!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el precio del Inmueble!',type: 'error'});
						}
						if(data.autovaluo==''){
							p.$w.find('[name=autovaluo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el autovaluo del Inmueble!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/inmu/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Tipo agregado!"});
							inInmu.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inInmu.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnTipo]').click(function(){
					inTipo.windowSelect({callback: function(data){
						p.$w.find('[name=sublocal]').empty();
						p.$w.find('[name=tipo]').html(data.nomb).data('data',data);
						K.block({$element: p.$w});
						$.post('in/subl/all_tipo',{_id: data._id.$id},function(data){
							if(data!=null){
								for(var i=0,j=data.length; i<j; i++){
									p.$w.find('[name=sublocal]').append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
									p.$w.find('[name=sublocal] option:last').data('data',data[i]);
								}
							}else{
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'El tipo seleccionado no cuenta con sublocales activos!',
									type: 'error'
								});
							}
							K.unblock({$element: p.$w});
						},'json');
					}});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Panel({
			title: 'Editar Inmueble: '+p.nomb,
			contentURL: 'in/inmu/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							tipo: p.$w.find('[name=tipo]').data('data'),
							abrev: p.$w.find('[name=abrev]').val(),
							direccion: p.$w.find('[name=direccion]').val(),
							distrito: p.$w.find('[name=distrito]').val(),
							provincia: p.$w.find('[name=provincia]').val(),
							departamento: p.$w.find('[name=departamento]').val(),
							partida_registral: p.$w.find('[name=partida_registral]').val(),
							area_terreno: p.$w.find('[name=area_terreno]').val(),
							area_construida: p.$w.find('[name=area_construida]').val(),
							precio: p.$w.find('[name=precio]').val(),
							moneda: p.$w.find('[name=moneda] option:selected').val(),
							autovaluo: p.$w.find('[name=autovaluo]').val()
						};
						if(data.tipo==null){
							p.$w.find('[name=btnTipo]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de local!',type: 'error'});
						}else data.tipo = inTipo.dbRel(data.tipo);
						if(p.$w.find('[name=sublocal] option').length==0){
							p.$w.find('[name=btnTipo]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un tipo de local con sublocales!',type: 'error'});
						}else{
							data.sublocal = inSubl.dbRel(p.$w.find('[name=sublocal] option:selected').data('data'));
						}
						if(data.direccion==''){
							p.$w.find('[name=direccion]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la direcci&oacute;n del Inmueble!',type: 'error'});
						}
						if(data.distrito==''){
							p.$w.find('[name=distrito]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el distrito del Inmueble!',type: 'error'});
						}
						if(data.provincia==''){
							p.$w.find('[name=provincia]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la provincia del Inmueble!',type: 'error'});
						}
						if(data.departamento==''){
							p.$w.find('[name=departamento]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el departamento del Inmueble!',type: 'error'});
						}
						if(data.partida_registral==''){
							p.$w.find('[name=partida_registral]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la partida registral del Inmueble!',type: 'error'});
						}
						if(data.area_terreno==''){
							p.$w.find('[name=area_terreno]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el area de terreno del Inmueble!',type: 'error'});
						}
						if(data.area_construida==''){
							p.$w.find('[name=area_construida]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el area construida del Inmueble!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el precio del Inmueble!',type: 'error'});
						}
						if(data.autovaluo==''){
							p.$w.find('[name=autovaluo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el autovaluo del Inmueble!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/inmu/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Tipo agregado!"});
							inInmu.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						inInmu.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=btnTipo]').click(function(){
					inTipo.windowSelect({callback: function(data){
						p.$w.find('[name=sublocal]').empty();
						p.$w.find('[name=tipo]').html(data.nomb).data('data',data);
						K.block({$element: p.$w});
						$.post('in/subl/all_tipo',{_id: data._id.$id},function(data){
							if(data!=null){
								for(var i=0,j=data.length; i<j; i++){
									p.$w.find('[name=sublocal]').append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
									p.$w.find('[name=sublocal] option:last').data('data',data[i]);
								}
							}else{
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'El tipo seleccionado no cuenta con sublocales activos!',
									type: 'error'
								});
							}
							K.unblock({$element: p.$w});
						},'json');
					}});
				});
				$.post('in/inmu/get',{_id: p.id},function(data){
					p.$w.find('[name=tipo]').html(data.tipo.nomb).data('data',data.tipo);
					p.$w.find('[name=abrev]').val(data.abrev);
					p.$w.find('[name=direccion]').val(data.direccion);
					p.$w.find('[name=distrito]').val(data.distrito);
					p.$w.find('[name=provincia]').val(data.provincia);
					p.$w.find('[name=departamento]').val(data.departamento);
					p.$w.find('[name=partida_registral]').val(data.partida_registral);
					p.$w.find('[name=area_terreno]').val(data.area_terreno);
					p.$w.find('[name=area_construida]').val(data.area_construida);
					p.$w.find('[name=precio]').val(data.precio);
					p.$w.find('[name=moneda]').selectVal(data.moneda);
					p.$w.find('[name=autovaluo]').val(data.autovaluo);
					var tmp = data.sublocal;
					$.post('in/subl/all_tipo',{_id: data.tipo._id.$id},function(data){
						for(var i=0,j=data.length; i<j; i++){
							p.$w.find('[name=sublocal]').append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
							p.$w.find('[name=sublocal] option:last').data('data',data[i]);
						}
						p.$w.find('[name=sublocal]').selectVal(tmp._id.$id);
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 510,
			height: 350,
			title: 'Seleccionar Inmueble',
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
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre','Tipo de Local'],
					data: 'in/inmu/lista',
					params: {},
					itemdescr: 'Inmueble(s)',
					onLoading: function(){
						K.block({$element: p.$w});
					},
					onComplete: function(){
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.direccion+'</td>');
						$row.append('<td>'+data.tipo.nomb+'</td>');
						$row.append('<td>'+data.sublocal.nomb+'</td>');
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
	['in/tipo','in/subl'],
	function(inTipo,inSubl){
		return inInmu;
	}
);