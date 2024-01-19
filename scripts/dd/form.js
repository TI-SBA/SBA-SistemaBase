ddForm = {
	
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb,
			desc: item.desc
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddForm',
			titleBar: {
				title: 'Lista de Formatos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Formato de Documento','Descripcion'],
					data: 'dd/form/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddForm.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.desc+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddForm.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenForm", {
							/*onShowMenu: function($row, menu) {
								$('#conMenForm_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenForm_hab',menu).remove();
								else $('#conMenForm_edi,#conMenForm_des',menu).remove();
								return menu;
							},
							*/
							bindings: {
								'conMenForm_edi': function(t) {
									ddForm.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},			
								'conMenForm_sub': function(t) {
									ddForm.windowSubirArchivo({id: K.tmp.data('id')});
								},		
								'conMenForm_dow': function(t) {
									if(data.url_imagen!=null){
										window.open(data.url_imagen);	
									}else{
										K.notification({title: 'Documento no Digitalizado',text: 'El documento no fue digitalizado todavia!',type: 'error'});
										
									}
								},		
								'conMenForm_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Formato:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/form/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Formato Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddForm.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Formato');
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
			id: 'windowNewFormato',
			title: 'Nuevo Formato',
			contentURL: 'dd/form/edit',
			width: 500,
			height: 300,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							desc: p.$w.find('[name=desc]').val(),
							//file: p.$w.find('[name=file]').text(),
						};
						
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/form/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Formato agregada!"});
							ddForm.init();
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
				p.$w = $('#windowNewFormato');
				p.$w.find("#file_upload").fileinput({
					
					uploadExtraData: function(){
						return{
							operacion: 'DD_FORMATOS'
						};
					},
					allowedFileExtensions: ["JPG","PNG"]
				});
				
			}
		});
	},
	windowSubirArchivo: function(p){
		new K.Modal({
			id: 'windowSubirArchivo',
			contentURL: 'dd/form/upload',
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
					uploadUrl: "dd/form/subir",
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
					$.post('dd/form/save',{_id:p.id,url_imagen:url_imagen},function(){
						K.unblock();
						K.closeWindow(p.$w.attr('id'));
					},'json');
					/*
					$.post('dd/form/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
					K.clearNoti();
					K.notification({title: 'Documento esta Digitalizado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
					ddRegi.init();
					});
					*/
				});


				
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditFormato',
			title: 'Editar Formato: '+p.nomb,
			contentURL: 'dd/form/edit',
			width: 500,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							desc: p.$w.find('[name=desc]').val(),
							file: p.$w.find('[name=file]').text(),
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/form/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Formato actualizada!"});
							ddForm.init();
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
				p.$w = $('#windowEditFormato');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
					},bootstrap: true});
				});
				p.$w.find('[name=btnFile]').click(function(){
					mgMult.windowNewModule({
						ruta: '/Archivo_Digital',
						nomb: 'nombre',
						callback: function(data){
							p.$w.find('[name=file]').html('/Archivo_Digital/'+data.file);
						}
						});
				});
				$.post('dd/form/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=desc]').val(data.desc);
					p.$w.find('[name=file]').text(data.file);
				},'json');
			}
		});
	},
	
	
};
define(
	['mg/enti','ct/pcon','mg/mult'],
	function(mgEnti,ctPcon,ddMult){
		return ddForm;
	}
);
