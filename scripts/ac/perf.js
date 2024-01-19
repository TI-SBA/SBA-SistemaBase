menPerfil = {
	init: function(p){
		if(p==null) p = {};
		
		K.initMode({
			action: 'menPerfil',
			titleBar: {
				title: 'Perfil del Usuario'
			}
		});
		
		new K.Panel({
			contentURL: 'ac/user/main',
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$el: p.$w});
				p.$w.find('[name=btnPass]').click(function(){
					menPerfil.newPass();
				});
				p.$w.find('[name=btnImg]').click(function(){
					var params = {id: p.enti._id.$id};
					if(p.enti.imagen!=null)
						params.img = p.enti.imagen.$id;
					mgMult.windowNewModule({
						ruta: '/Usuarios/Perfiles',
						nomb: K.session.enti._id.$id,
						callback: function(data){
							ext = data.file.substr(data.file.indexOf('.'));
							$('.img-circle').attr('src',K.path_file+'/Usuarios/Perfiles/'+K.session.enti._id.$id+ext+'#'+new Date().getTime());
							$('.img-responsive').attr('src',K.path_file+'/Usuarios/Perfiles/'+K.session.enti._id.$id+ext+'#'+new Date().getTime());
							$.post("mg/enti/save",{
								_id: p.enti._id.$id,
								imagen: '/Usuarios/Perfiles/'+K.session.enti._id.$id+ext
							},function(){
								K.clearNoti();
								K.notification({title: ciHelper.titleMessages.regiGua,text: "Foto de Perfil actualizada con &eacute;xito!"});
								menPerfil.init();
							});
						}
					});
				});
				$.post('ac/user/get',{_id: K.session.user._id.$id},function(data){
					p.user = data;
					p.enti = p.user.owner;
					p.$w.find('[name=fullname]').html(mgEnti.formatNameNorm(p.enti));
					p.$w.find('[name=dni]').html(mgEnti.formatIden(p.enti));
					if(p.enti.imagen!=null){
						if(p.enti.imagen.$id!=null)
							p.$w.find('[name=img]').attr('src','ci/files/get?id='+p.enti.imagen.$id+'#'+new Date().getTime());
						else
							p.$w.find('[name=img]').attr('src',K.path_file+p.enti.imagen+'#'+new Date().getTime());
					}
					p.$w.find('[name=oficina]').html(p.enti.roles.trabajador.oficina.nomb);
					if(p.enti.roles.trabajador.cargo.nomb!=null)
						p.$w.find('[name=cargo]').html(p.enti.roles.trabajador.cargo.nomb);
					else
						p.$w.find('[name=cargo]').html(p.enti.roles.trabajador.cargo.funcion);
					var $grid = new K.grid({
						$el: p.$w.find('[name=gridActi]'),
						cols: ['M&oacute;dulo','Bandeja','Descripci&oacute;n','Trabajador','Registrado'],
						data: 'ac/log/Lista',
						params: {usuario: K.session.enti._id.$id},
						itemdescr: 'registro(s)',
						onLoading: function(){ 
							K.block({$element: $('#pageWrapperMain')});
						},
						onComplete: function(){ 
							K.unblock({$element: $('#pageWrapperMain')});
						},
						fill: function(data,$row){
							$row.append('<td>'+acLogs.modulos[data.modulo]+'</td>');
							$row.append('<td>'+data.bandeja+'</td>');
							$row.append('<td>'+data.descr+'</td>');
							$row.append('<td>'+mgEnti.formatName(data.entidad)+'</td>');
							$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</td>');
							return $row;
						}
					});
					K.unblock({$el: p.$w});
				},'json');
			}
		});
	},
	newPass: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowPass',
			title: 'Generaci&oacute;n de Nueva Contrase&ntilde;a Personal',
			contentURL: 'ac/user/new_pass',
			width: 750,
			height: 350,
			buttons: {
				'Guardar Contrase&ntilde;a': {
					type: 'success',
					icon: 'fa-save',
					f: function(){
						K.clearNoti();
						var data = {
							_id: K.session.user._id.$id,
							old: p.$w.find('[name=pwd_old]').val(),
							pwd: p.$w.find('[name=pwd]').val(),
							conf: p.$w.find('[name=pwd_conf]').val()
						};
						if(data.old==''){
							p.$w.find('[name=pwd_old]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar su contrase&ntilde;a antigua!',
								type: 'error'
							});
						}
						if(data.pwd==''){
							p.$w.find('[name=pwd]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar su nueva contrase&ntilde;a!',
								type: 'error'
							});
						}
						if(data.conf==''){
							p.$w.find('[name=pwd_conf]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe confirmar su nueva contrase&ntilde;a!',
								type: 'error'
							});
						}
						if(data.pwd!=data.conf){
							p.$w.find('[name=pwd_conf]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'La contrase&ntilde;a ingresada no coincide!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ac/user/save_pass",data,function(result){
							K.clearNoti();
							if(result.error==null){
								K.notification({title: ciHelper.titleMessages.regiGua,text: "Contrase&ntilde;a actualizada con &eacute;xito!"});
								K.closeWindow(p.$w.attr('id'));
							}else{
								p.$w.find('#div_buttons button').removeAttr('disabled');
								p.$w.find('[name=pwd_old]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Su contrase&ntilde;a antigua no coincide!',
									type: 'error'
								});
							}
						},'json');
					}
				},
				'Cancelar': {
					type: 'danger',
					icon: 'fa-ban',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPass');
				
			}
		});
	},
	newImg: function(p){
		new K.Modal({
			id: 'windowImg',
			title: 'Nueva Imagen de Perfil',
			contentURL: 'ci/upload',
			allScreen: true,
			store: false,
			noButtons: true,
			onContentLoaded: function(){
				p.$w = $('#windowImg');
				$("#file_upload").fileinput({
					language: "es",
					uploadUrl: "ci/upload/img",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function() {
						return {
							operacion: 'AC_FOTO'
						};
					},
					allowedFileExtensions: ["jpg","jpeg","png","gif"]
				});
				$('#file_upload').on('fileuploaded', function(event, params) {
					K.clearNoti();
					$.post("mg/enti/save",{
						_id: p.id,
						imagen: params.response.$id
					},function(result){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Foto de Perfil actualizada con &eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#side-menu .img-circle').attr('src','ci/files/get?id='+params.response.$id);
						menPerfil.init();
					},'json');
				});
			}
		});
	}
};
define(
	['ac/logs','mg/mult'],
	function(acLogs,mgMult){
		return menPerfil;
	}
);