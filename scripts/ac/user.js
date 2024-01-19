/*******************************************************************************
Usuario */
acUser = {
	states: {
		Disabled: {
			descr: "Disabled",
			color: "green",
			label: '<span class="label label-danger">Deshabilitado</span>'
		},
		Enabled:{
			descr: "Enabled",
			color: "#CCCCCC",
			label: '<span class="label label-info">Habilitado</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'ac',
			action: 'acUser',
			titleBar: {
				title: 'Usuarios'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cuenta','Identidad'],
					data: 'ac/user/lista',
					params: {},
					itemdescr: 'usuario(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							acUser.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						if(data.status!=null){
							$row.append('<td>'+acUser.states[data.status].label+'</td>');
						}else{
							$row.append('<td>'+'----'+'</td>');
						}
						$row.append('<td>'+data.userid+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.owner)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							acUser.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPeConc", {
							onShowMenu: function($row, menu) {
								$('#conMenPeConc_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeConc_edi': function(t) {
									acUser.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPeConc_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Usuario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ac/user/save',{_id: K.tmp.data('id'),status: 'Enabled'},function(){
											K.clearNoti();
											K.msg({title: 'Usuario Habilitado',text: 'Se realiz&oacute; con &eacute;xito!'});
											acUser.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Usuario');
								},
								'conMenPeConc_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Usuario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ac/user/save',{_id: K.tmp.data('id'),status: 'Disabled'},function(){
											K.clearNoti();
											K.msg({title: 'Usuario Deshabilitado',text: 'Se realiz&oacute; con &eacute;xito!'});
											acUser.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Usuario');
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
			title: 'Nuevo Usuario',
			contentURL: 'ac/user/new',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							userid: p.$w.find('[name=userid]').val(),
							passwd: p.$w.find('[name=pass1]').val(),
							owner: p.$w.find('[name=mini_enti]').data('data'),
							organizacion: p.$w.find('[name=organizacion]').data('data'),
							oficina: p.$w.find('[name=oficina]').data('data'),
							programa: p.$w.find('[name=programa]').data('data'),
							funcion: p.$w.find('[name=funcion]').val(),
							groups: []
						};
						if(p.$w.find('[name=userid]').val()==''){
							p.$w.find('[name=userid]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese un nombre de cuenta!',type: 'error'});
						}
						if(!p.$w.find('[name=userid]').data('dispo')){
							p.$w.find('[name=userid]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese un nombre de cuenta v&aacute;lido!',type: 'error'});
						}
						if(p.$w.find('[name=pass1]').val()==''){
							p.$w.find('[name=pass1]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese una contrase&ntilde;a!',type: 'error'});
						}
						if(p.$w.find('[name=pass2]').val()==''){
							p.$w.find('[name=pass2]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese una contrase&ntilde;a!',type: 'error'});
						}
						if(!p.$w.find('[name=pass2]').data('dispo')){
							p.$w.find('[name=pass2]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese una contrase&ntilde;a v&aacute;lida!',type: 'error'});
						}
						if(data.owner==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Seleccione una entidad para el usuario!',type: 'error'}); 
						}else data.owner = mgEnti.dbRel(data.owner);
						if(data.organizacion==null){
							p.$w.find('[name=btnOrg]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Usuario debe tener una organizaci&oacute;n, complete la informaci&oacute;n!',type: 'error'}); 
						}else data.organizacion = mgOrga.dbRel(data.organizacion);
						if(data.oficina==null){
							p.$w.find('[name=btnOfi]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Usuario debe tener una oficina, complete la informaci&oacute;n!',type: 'error'}); 
						}else data.oficina = mgOfic.dbRel(data.oficina);
						if(data.programa==null){
							p.$w.find('[name=btnPro]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Usuario debe tener un programa, complete la informaci&oacute;n!',type: 'error'}); 
						}else data.programa = mgProg.dbRel(data.programa);
						if(data.funcion==""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Usuario debe tener una funcion, complete la informaci&oacute;n!',type: 'error'}); 
						}

						if(p.$w.find('[name=btnEli]').length<1)
							return K.notification({text: 'El usuario debe pertenecer al menos a un grupo!',type: 'error'});
						for(var i=0; i<p.$w.find('[name=gridGrup] tbody .item').length; i++){
							var tmp = p.$w.find('[name=gridGrup] tbody .item').eq(i).data('data');
							data.groups.push({
								_id: tmp._id.$id,
								groupid: tmp.groupid
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ac/user/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Usuario agregado!"});
							acUser.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						acUser.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=btnUser]').click(function(e){
					e.preventDefault();
				});
				p.$w.find('[name=btnPro]').click(function(){
					mgProg.windowSelect({callback:function(data){
						p.$w.find('[name=programa]').html(data.nomb).data('data',mgProg.dbRel(data));
					}});
				});
				p.$w.find('[name=btnOfi]').click(function(){
					mgOfic.windowSelect({bootstrap:true, callback:function(data){
						p.$w.find('[name=oficina]').html(data.nomb).data('data',mgOfic.dbRel(data));
					}});
				});
				p.$w.find('[name=userid]').blur(function(){
					var userid = $(this).val();
					if(userid==''){
						p.$w.find('[name=btnUser] i').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						p.$w.find('[name=cuenta]').data('dispo',false);
						return K.notification({text: 'Debe ingresar un nombre de usuario v&aacute;lido!',type: 'error'});
					}
					$.post('ac/user/validar',{userid: userid},function(data){
						if(data.msj){
							p.$w.find('[name=btnUser]').removeClass('btn-danger').addClass('btn-primary');
							p.$w.find('[name=btnUser] i').removeClass('fa-close').addClass('fa-check');
							K.notification('El nombre de usuario est&aacute; disponible!');
							p.$w.find('[name=userid]').data('dispo',true);
						}else{
							p.$w.find('[name=btnUser]').removeClass('btn-primary').addClass('btn-danger');
							p.$w.find('[name=btnUser] i').removeClass('fa-check').addClass('fa-close');
							K.notification({
								text: 'El nombre de usuario no se encuentra disponible.<br/>Elija otro nombre de usuario!',
								type: 'error'
							});
							p.$w.find('[name=userid]').data('dispo',false);
						}
					},'json');
				}).data('dispo',false);
				p.$w.find('[name=pass2]').blur(function(){
					var pass1 = p.$w.find('[name=pass1]').val(),
					pass2 = $(this).val();
					if(pass1==pass2){
						p.$w.find('[name=btnPass]').removeClass('btn-danger').addClass('btn-primary');
						p.$w.find('[name=btnPass] i').removeClass('fa-close').addClass('fa-check');
						K.notification('Ambas contrase&ntilde;as coinciden!');
						p.$w.find('[name=pass2]').data('dispo',true);
					}else{
						p.$w.find('[name=btnPass]').removeClass('btn-primary').addClass('btn-danger');
						p.$w.find('[name=btnPass] i').removeClass('fa-check').addClass('fa-close');
						K.notification({
							text: 'Las contrase&ntilde;as ingresadas no coinciden!',
							type: 'error'
						});
						p.$w.find('[name=pass2]').data('dispo',false);
					}
				}).data('dispo',false);
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({
						bootstrap: true,
						filter: [{nomb: 'tipo_enti',value: 'P'}],
						callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
							var tmp = false,
							userid = data.appat.trim();
							userid = userid.replace(/\s+/g, '');
							userid = (data.nomb.trim().substring(0,1)+userid);
							userid = userid.toLowerCase();
							p.$w.find('[name=userid]').val(userid).blur();
							if(data.roles){
								if(data.roles.trabajador){
									if(data.roles.trabajador.cargo){
										//if(data.roles.trabajador.cargo.organizacion!=null)
										//	p.$w.find('[name=organizacion]').html(data.roles.trabajador.cargo.organizacion.nomb).data('data',data.roles.trabajador.cargo.organizacion);
										if(data.roles.trabajador.oficina!=null)
											p.$w.find('[name=oficina]').html(data.roles.trabajador.oficina.nomb).data('data',mgOfic.dbRel(data.roles.trabajador.oficina));
										if(data.roles.trabajador.programa!=null)
											p.$w.find('[name=programa]').html(data.roles.trabajador.programa.nomb).data('data',mgOfic.dbRel(data.roles.trabajador.programa));
										if(data.roles.trabajador.cargo.funcion!='')
											p.$w.find('[name=funcion]').val(data.roles.trabajador.cargo.funcion);
										else if(data.roles.trabajador.cargo.nomb!='')
											p.$w.find('[name=funcion]').val(data.roles.trabajador.cargo.nomb);
										tmp = true;
									}
								}
							}
							if(tmp==false){
								p.$w.find('[name=btnOrg]').removeAttr('disabled');
								p.$w.find('[name=btnOfi]').removeAttr('disabled');
								p.$w.find('[name=funcion]').removeAttr('disabled');
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Esta entidad no tiene informaci&oacute;n laboral!',
									type: 'error'
								});
							}else{
								p.$w.find('[name=btnOrg]').attr('disabled','disabled');
								p.$w.find('[name=btnOfi]').attr('disabled','disabled');
								p.$w.find('[name=funcion]').attr('disabled','disabled');
							}
						}
					});
				});
				p.$w.find('[name=btnAct]').hide();
				p.$w.find('[name=btnOrg]').click(function(){
					mgOrga.windowSelect({callback: function(data){
						K.block({$element: p.$w});
						p.$w.find('[name=organizacion]').html(data.nomb).data('data',data);
						$.post('mg/ofic/lista',{texto: data.nomb,page: 1,page_rows: 1},function(ofic){
							if(ofic.items!=null){
								p.$w.find('[name=oficina]').html(ofic.items[0].nomb).data('data',ofic.items[0]);
							}
							K.unblock();
						},'json');
					},bootstrap: true});
				});
				p.$w.find('[name=btnOfi]').click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=oficina]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				new K.grid({
					$el: p.$w.find('[name=gridGrup]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n',''],
					onlyHtml: true,
					toolbarHTML: '<input type="text" placeholder="buscar grupo..." name="groupid" class="form-control">'
				});
				$.post('ac/grup/all',function(data){
					p.grup = $.each(data,function(i,item){
						item.id = item._id.$id;
						item.busqueda = item.groupid;
					});
					p.$w.find('[name=groupid]').typeaheadmap({ 
						source: p.grup,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.grup,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							var $row = $('<tr class="item">');
							$row.append('<td>'+cuenta.groupid+'</td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							$row.data('data',cuenta);
							p.$w.find('[name=gridGrup] tbody').append($row);
						}
					});
					p.$w.find('[name=btnSel]').click();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Editar Usuario '+p.nomb,
			contentURL: 'ac/user/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							owner: p.$w.find('[name=mini_enti]').data('data'),
							oficina: p.$w.find('[name=oficina]').data('data'),
							programa: p.$w.find('[name=programa]').data('data'),
							funcion: p.$w.find('[name=funcion]').val(),
							groups: []
						};
						if(data.owner==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Seleccione una entidad para el usuario!',type: 'error'}); 
						}else data.owner = mgEnti.dbRel(data.owner);
						if(data.oficina==null){
							p.$w.find('[name=btnOfi]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Usuario debe tener una oficina, complete la informaci&oacute;n!',type: 'error'}); 
						}
						if(data.programa==null){
							p.$w.find('[name=btnPro]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Usuario debe tener un programa, complete la informaci&oacute;n!',type: 'error'}); 
						}
						if(p.$w.find('[name=iCheck]').is(':checked')){
							data.passwd = p.$w.find('[name=pass1]').val();
							if(p.$w.find('[name=pass1]').val()==''){
								p.$w.find('[name=pass1]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese una contrase&ntilde;a!',type: 'error'});
							}
							if(p.$w.find('[name=pass2]').val()==''){
								p.$w.find('[name=pass2]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese una contrase&ntilde;a!',type: 'error'});
							}
							if(!p.$w.find('[name=pass2]').data('dispo')){
								p.$w.find('[name=pass2]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese una contrase&ntilde;a v&aacute;lida!',type: 'error'});
							}
						}
						if(p.$w.find('[name=btnEli]').length<1)
							return K.notification({text: 'El usuario debe pertenecer al menos a un grupo!',type: 'error'});
						for(var i=0; i<p.$w.find('[name=gridGrup] tbody .item').length; i++){
							var tmp = p.$w.find('[name=gridGrup] tbody .item').eq(i).data('data');
							data.groups.push({
								_id: tmp._id.$id,
								groupid: tmp.groupid
							});
						}
						/*console.log(data);
						return false;*/
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ac/user/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Usuario actualizado!"});
							acUser.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						acUser.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=btnPro]').click(function(){
					mgProg.windowSelect({callback:function(data){
						p.$w.find('[name=programa]').html(data.nomb).data('data',mgProg.dbRel(data));
					}});
				});
				p.$w.find('[name=btnOfi]').click(function(){
					mgOfic.windowSelect({bootstrap:true, callback:function(data){
						p.$w.find('[name=oficina]').html(data.nomb).data('data',mgOfic.dbRel(data));
					}});
				});
				p.$w.find('[name=iCheck]').iCheck({
					checkboxClass: 'icheckbox_square-green',
					radioClass: 'iradio_square-green'
				});
				p.$w.find('[name=iCheck]').on('ifToggled', function(event){
					if($(this).is(':checked')){
						p.$w.find('.form-group').eq(2).show();
						p.$w.find('.form-group').eq(3).show();
					}else{
						p.$w.find('.form-group').eq(2).hide();
						p.$w.find('.form-group').eq(3).hide();
					}
				});
				p.$w.find('[name=pass2]').blur(function(){
					var pass1 = p.$w.find('[name=pass1]').val(),
					pass2 = $(this).val();
					if(pass1==pass2){
						p.$w.find('[name=btnPass]').removeClass('btn-danger').addClass('btn-primary');
						p.$w.find('[name=btnPass] i').removeClass('fa-close').addClass('fa-check');
						K.notification('Ambas contrase&ntilde;as coinciden!');
						p.$w.find('[name=pass2]').data('dispo',true);
					}else{
						p.$w.find('[name=btnPass]').removeClass('btn-primary').addClass('btn-danger');
						p.$w.find('[name=btnPass] i').removeClass('fa-check').addClass('fa-close');
						K.notification({
							text: 'Las contrase&ntilde;as ingresadas no coinciden!',
							type: 'error'
						});
						p.$w.find('[name=pass2]').data('dispo',false);
					}
				}).data('dispo',false);
				p.$w.find('[name=btnSel]').hide();
				p.$w.find('[name=btnAct]').hide();
				new K.grid({
					$el: p.$w.find('[name=gridGrup]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n',''],
					onlyHtml: true,
					toolbarHTML: '<input type="text" placeholder="buscar grupo..." name="groupid" class="form-control">'
				});
				$.post('ac/grup/all',function(data){
					p.grup = $.each(data,function(i,item){
						item.id = item._id.$id;
						item.busqueda = item.groupid;
					});
					p.$w.find('[name=groupid]').typeaheadmap({ 
						source: p.grup,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.grup,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							var $row = $('<tr class="item">');
							$row.append('<td>'+cuenta.groupid+'</td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							$row.data('data',cuenta);
							p.$w.find('[name=gridGrup] tbody').append($row);
						}
					});
					$.post('ac/user/get',{_id: p.id},function(data){
						p.$w.find('[name=userid]').html(data.userid);
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.owner);
						/*if(data.organizacion==null)
							data.organizacion = data.owner.roles.trabajador.organizacion;
						p.$w.find('[name=organizacion]').html(data.organizacion.nomb);*/
						if(data.oficina==null)
							data.oficina = data.owner.roles.trabajador.oficina;
						if(data.oficina!=null)
							p.$w.find('[name=oficina]').html(data.oficina.nomb).data('data',mgOfic.dbRel(data.oficina));
						if(data.programa==null)
							data.programa = data.owner.roles.trabajador.programa;
						if(data.programa!=null)
							p.$w.find('[name=programa]').html(data.programa.nomb).data('data',mgProg.dbRel(data.programa));
						if(data.funcion==null)
							data.funcion = data.owner.roles.trabajador.cargo.funcion;
						p.$w.find('[name=funcion]').html(data.funcion);
						for(var i=0; i<data.groups.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td>'+data.groups[i].groupid+'</td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							$row.data('data',data.groups[i]);
							p.$w.find('[name=gridGrup] tbody').append($row);
						}
						K.unblock();
					},'json');
				},'json');
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','mg/orga','mg/ofic','mg/prog'],
	function(mgEnti,ctPcon,mgOrga,mgOfic,mgProg){
		return acUser;
	}
);