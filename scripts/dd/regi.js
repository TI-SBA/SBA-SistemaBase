ddRegi = {
		states: {
		H: {
			descr: "Digitalizado",
			color: "green",
			label: '<span class="label label-success">Digitalizado</span>'
		},
		D:{
			descr: "No Digitalizado",
			color: "#CCCCCC",
			label: '<span class="label label-default">No Digitalizado</span>'
		}
	},
	
		dbRel: function(item){
		return {
			_id: item._id.$id,
			titu: item.titu,
			ndoc: item.ndoc,
			desc: item.desc,
			dire: item.dire,
			id_dire: item.id_dire,
			ofic: item.ofic,
			id_ofic: item.id_ofic,
			docu: item.docu,
			id_docu: item.id_docu,
			femi: item.femi
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddRegi',
			titleBar: {
				title: 'Registro de Documentos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado de Documento','Nº. Documento','Documento','Direccion','Oficina','Tipo de Documento','Fecha de Registro'],
					data: 'dd/regi/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddRegi.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+ddRegi.states[data.estado].label+'</td>');
						$row.append('<td>'+data.ndoc+'</td>');
						$row.append('<td>'+data.titu+'</td>');
						$row.append('<td>'+data.dire+'</td>');
						$row.append('<td>'+data.ofic+'</td>');
						$row.append('<td>'+data.docu+'</td>');
						$row.append('<td>'+moment(data.femi.sec,'X').format('DD/MM/YYYY')+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddRegi.windowDetails({_id: $(this).data('id'),titu: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenRegistro", {
							onShowMenu: function($row, menu) {
								$('#conMenRegistro_ver',menu).remove();
								if($row.data('estado')=='D') $('#conMenRegistro_hab',menu).remove();
								else $('#conMenRegistro_edi,#conMenRegistro_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenRegistro_edi': function(t) {
									ddRegi.windowEdit({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenRegistro_sub': function(t) {
									ddRegi.windowSubirArchivo({id: K.tmp.data('id')});
								},
								'conMenRegistro_dow': function(t) {
									if(data.url_imagen!=null){
										window.open(data.url_imagen);	
									}else{
										K.notification({title: 'Documento no Digitalizado',text: 'El documento no fue digitalizado todavia!',type: 'error'});
										
									}
								},
								'conMenRegistro_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Registro:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/regi/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Registro Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddRegi.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Registro');
								},
								'conMenRegistro_des': function(t) {
									ciHelper.confirm('&#191;Esta <b>No Digitalizado</b> el Documento Nro:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/regi/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'El Documento no esta Digitalizado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddRegi.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Documentos');
								},							
								'conMenRegistro_hab': function(t) {
									ciHelper.confirm('&#191;Esta <b>Digitalizado</b> el Documento Nro: <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/regi/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Documento esta Digitalizado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddRegi.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Documentos');
								},
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
			id: 'windowNewRegistro',
			title: 'Nuevo Registro de Documento',
			contentURL: 'dd/regi/edit',
			width: 700,
			height: 500,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							titu: p.$w.find('[name=titu]').val(),
							ndoc: p.$w.find('[name=ndoc]').val(),
							desc: p.$w.find('[name=desc]').val(),
							dire: p.$w.find('[name=dire]').text(),
							id_dire: p.$w.find('[name=id_dire]').text(),
							ofic: p.$w.find('[name=ofic]').text(),
							id_ofic: p.$w.find('[name=id_ofic]').text(),
							docu: p.$w.find('[name=docu]').text(),
							id_docu: p.$w.find('[name=id_docu]').text(),
							femi: p.$w.find('[name=femi]').val(),
						};
						if(data.titu==''){
							p.$w.find('[name=titu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Titulo!',type: 'error'});
						}
						if(data.ndoc==''){
							p.$w.find('[name=ndoc]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Numero de Documento!',type: 'error'});
						}
						
						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Direccion!',type: 'error'});
						}

						if(data.ofic==''){
							p.$w.find('[name=ofic]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar La Oficina!',type: 'error'});
						}
						if(data.docu==''){
							p.$w.find('[name=docu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Tipo de Documento!',type: 'error'});
						}
						if(data.femi==''){
							p.$w.find('[name=femi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Fecha!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/regi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Registro de Documento agregado!"});
							ddRegi.init();
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
				p.$w = $('#windowNewRegistro');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			startDate: '-3d'
				});	
				p.$w.find('[name=btnOfic]').click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofic]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_ofic]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnTido]").click(function(){
					ddTido.windowSelect({callback: function(data){
						p.$w.find('[name=docu]').html(data.docu).data('data',data);
						p.$w.find('[name=id_docu]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
					p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=dire]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_dire]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				
				p.$w.find('[name=btnFile]').click(function(){
					mgMult.windowNewModule({
						ruta: '/Archivo_Digital',
						nomb: 'nombre',
						callback: function(data){
							p.$w.find('[name=foto]').html('/Archivo_Digital/'+data.file);
						}
					});
				});
			}
		});
	},
	windowSubirArchivo: function(p){
		new K.Modal({
			id: 'windowSubirArchivo',
			contentURL: 'dd/regi/upload',
			width: 750,
			height: 600,
			store: false,
			title: 'Subir Archivo',
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
				p.$w = $('#windowSubirArchivo');
				p.$w.find("#file_upload").fileinput({
					language: "es",
					uploadUrl: "dd/regi/subir",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function(){
						return{
							operacion: 'PE_PLAN_MAESTRO'
						};
					},
					allowedFileExtensions: ["JPG","PNG","JPEG","PDF"]
				});

				p.$w.find('#file_upload').on('fileuploaded', function(event,params){
					K.clearNoti();
					K.block();
					K.sendingInfo();

					var url_imagen = params.response.mediaLink;
					$.post('dd/regi/save',{_id:p.id,url_imagen:url_imagen},function(){
						K.unblock();
						K.closeWindow(p.$w.attr('id'));
					},'json');
					$.post('dd/regi/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
					K.clearNoti();
					K.notification({title: 'Documento esta Digitalizado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
					ddRegi.init();
					});
				});


				
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditHistorico',
			title: 'Editar Registro: '+p.titu,
			contentURL: 'dd/regi/edit',
			width: 700,
			height: 500,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							titu: p.$w.find('[name=titu]').val(),
							ndoc: p.$w.find('[name=ndoc]').val(),
							desc: p.$w.find('[name=desc]').val(),
							dire: p.$w.find('[name=dire]').text(),
							id_dire: p.$w.find('[name=id_dire]').text(),
							ofic: p.$w.find('[name=ofic]').text(),
							id_ofic: p.$w.find('[name=id_ofic]').text(),
							docu: p.$w.find('[name=docu]').text(),
							id_docu: p.$w.find('[name=id_docu]').text(),
							femi: p.$w.find('[name=femi]').val(),
						};
						if(data.titu==''){
							p.$w.find('[name=titu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el titulo!',type: 'error'});
						}
						if(data.metr==''){
							p.$w.find('[name=ndoc]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Metraje!',type: 'error'});
						}
						if(data.casa==''){
							p.$w.find('[name=docu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar las cajas o sacos!',type: 'error'});
						}
						/*						
						if(data.dire==''){
							p.$w.find('[name=femi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Direccion!',type: 'error'});
						}
						*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/regi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Registro actualizado!"});
							ddRegi.init();
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
				p.$w = $('#windowEditHistorico');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			startDate: '-3d'
				});	
					p.$w.find('[name=btnOfic]').click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofic]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_ofic]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnTido]").click(function(){
					ddTido.windowSelect({callback: function(data){
						p.$w.find('[name=docu]').html(data.docu).data('data',data);
						p.$w.find('[name=id_docu]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=dire]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_dire]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				K.block();
				$.post('dd/regi/get',{_id: p.id},function(data){
				p.$w.find('[name=titu]').val(data.titu);
				p.$w.find('[name=ndoc]').val(data.ndoc);
				p.$w.find('[name=desc]').val(data.desc);
				p.$w.find('[name=docu]').text(data.docu);
				p.$w.find('[name=dire]').text(data.dire);
				p.$w.find('[name=ofic]').text(data.ofic);
				p.$w.find('[name=femi]').val(moment(data.femi.sec,'X').format('DD/MM/YYYY'));				
				K.unblock();
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelectRegistro',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Registro',
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
				p.$w = $('#windowSelectRegistro');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'dd/regi/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.ndoc+'</td>');
						$row.append('<td>'+data.titu+'</td>');
						$row.append('<td>'+data.dire+'</td>');
						$row.append('<td>'+data.ofic+'</td>');
						$row.append('<td>'+data.docu+'</td>');
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
	['mg/enti','dd/tise','dd/tipo','dd/dire','mg/ofic','dd/tido','mg/mult','mg/prog'],
	function(mgEnti,ddTise,ddTipo,ddDire,mgOfic,ddTido,ddMult,ddProg){
		return ddRegi;
	}
);