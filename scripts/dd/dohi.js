ddDohi = {
	states: {
		D: {
			descr: "Digitalizado",
			color: "green",
			label: '<span class="label label-success">Digitalizado</span>'
		},
		N:{
			descr: "No Digitalizado",
			color: "#CCCCCC",
			label: '<span class="label label-default">No Digitalizado</span>'
		}
	},
	tipos_: {
		"0":"DOCUMENTOS HISTORICOS",
		"1":"ARCHIVO FOTOGRAFICO",
		"2":"PRINCIPALES ACTAS DE ASAMBLEA GENERAL",
		"3":"PRINCIPALES ACTAS DE DIRECTORIO"
	},
		dbRel: function(item){
		return {
			_id: item._id.$id,
			titu: item.titu,
			ndoc: item.ndoc,
			desc: item.desc,
			docu: item.docu,
			femi: item.femi
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddDohi',
			titleBar: {
				title: 'Documentos Historicos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Version Digital','Nro. Documento','Nombre','Tipo de Documento','Fecha de Emision'],
					data: 'dd/dohi/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddDohi.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+ddDohi.states[data.estado].label+'</td>');
						$row.append('<td>'+data.ndoc+'</td>');
						$row.append('<td>'+data.titu+'</td>');
						$row.append('<td>'+data.docu+'</td>');
						$row.append('<td>'+moment(data.femi.sec,'X').format('DD/MM/YYYY')+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddDohi.windowDetails({_id: $(this).data('id'),titu: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenRegistro", {
							onShowMenu: function($row, menu) {
								$('#conMenRegistro_ver',menu).remove();
								if($row.data('estado')=='N') $('#conMenRegistro_hab',menu).remove();
								else $('#conMenRegistro_edi,#conMenRegistro_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenRegistro_edi': function(t) {
									ddDohi.windowEdit({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenRegistro_sub': function(t) {
									ddDohi.windowSubirArchivo({id: K.tmp.data('id')});
								},
								'conMenRegistro_dow': function(t) {
									if(data.url_imagen!=null){
										window.open(data.url_imagen);	
									}else{
										K.notification({title: 'Documento no Digitalizado',text: 'El documento no fue digitalizado todavia!',type: 'error'});
										
									}
								},
								'conMenRegistro_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Documento Historico:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/dohi/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Documento Historico Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddDohi.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Documento Historico');
								},
								'conMenRegistro_des': function(t) {
									ciHelper.confirm('&#191;Esta <b>No Digitalizado</b> el Documento Nro:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/dohi/save',{_id: K.tmp.data('id'),estado: 'N'},function(){
											K.clearNoti();
											K.notification({title: 'El Documento no esta Digitalizado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddDohi.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Documentos');
								},							
								'conMenRegistro_hab': function(t) {
									ciHelper.confirm('&#191;Esta <b>Digitalizado</b> el Documento Nro: <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/dohi/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Documento esta Digitalizado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddDohi.init();
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
			id: 'windowNewHistorico',
			title: 'Nueva Documento Historico',
			contentURL: 'dd/dohi/edit',
			width: 750,
			height: 600,
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
							docu: p.$w.find('[name=docu]').text(),
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
												
						if(data.dire==''){
							p.$w.find('[name=femi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Direccion!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/dohi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.dohiGua,text: "Documento Historico agregado!"});
							ddDohi.init();
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
				p.$w = $('#windowNewHistorico');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			startDate: '-3d'
				});

				p.$w.find("[name=btnTido]").click(function(){
					ddTido.windowSelect({callback: function(data){
						p.$w.find('[name=docu]').html(data.docu).data('data',data);
				
					},bootstrap: true});
				});
				
			}
		});
	},
	windowSubirArchivo: function(p){
		new K.Modal({
			id: 'windowSubirArchivo',
			contentURL: 'dd/dohi/upload',
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
					uploadUrl: "dd/dohi/subir",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function(){
						return{
							operacion: 'PE_PLAN_MAESTRO'
						};
					},
					allowedFileExtensions: ["JPG","PNG","JPEG"]
				});

				p.$w.find('#file_upload').on('fileuploaded', function(event,params){
					K.clearNoti();
					K.block();
					K.sendingInfo();

					var url_imagen = params.response.mediaLink;
					$.post('dd/dohi/save',{_id:p.id,url_imagen:url_imagen},function(){
						K.unblock();
						K.closeWindow(p.$w.attr('id'));
					},'json');
					$.post('dd/dohi/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
						K.clearNoti();
						K.notification({title: 'Documento esta Digitalizado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						ddDohi.init();
					});	
				});


				
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditHistorico',
			title: 'Editar Documento Historico: '+p.titu,
			contentURL: 'dd/dohi/edit',
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
							docu: p.$w.find('[name=docu]').text(),
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
						$.post("dd/dohi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.dohiAct,text: "Documento Historico actualizado!"});
							ddDohi.init();
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
				
				K.block();
				$.post('dd/dohi/get',{_id: p.id},function(data){
				p.$w.find('[name=titu]').val(data.titu);
				p.$w.find('[name=ndoc]').val(data.ndoc);
				p.$w.find('[name=desc]').val(data.desc);
				p.$w.find('[name=docu]').text(data.docu);
				p.$w.find('[name=femi]').val(moment(data.femi.sec,'X').format('DD/MM/YYYY'));
				
				K.unblock();
				},'json');
			}
		});
	},
};
define(
	['mg/enti','dd/ofic','dd/tido','dd/dire'],
	function(mgEnti,ddOfic,ddTido,ddDire){
		return ddDohi;
	}
);