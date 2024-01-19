ciEdit = {
	windowEditEntidad: function(p){
		var dialogLayout;
		new K.Modal({
			id: 'windowEntiEdit'+p.id,
			title: 'Editar '+p.nomb,
			width: 750,
			height: 400,
			icon: 'ui-icon-person',
			contentURL: 'td/gest/edit',
			buttons: {
				"Guardar" : function(){
					var data = new Object();
					p.$section1 = p.$w.find('#section1');
					data = new Object();
					data._id = p.id;
					//data.imagen = p.$section1.find('[name=foto]').data('id');
					if(p.tipo_enti=='E'){
						data.tipo_enti = "E";
						data.nomb = $('[name=rsocial]',p.$section1).val();
						data.docident = p.data.docident;
						data.docident[0] = new Object;
						data.docident[0].tipo = 'RUC';
						data.docident[0].num = $('[name=ruc]',p.$section1).val();
						if(data.docident[0].num==''){ $('[name=ruc]',p.$section1).focus(); return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'}); }
						if(data.nomb==''){ $('[name=rsocial]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'}); }
					}else{
						data.tipo_enti = "P";
						data.nomb = $('[name=nomb]',p.$section1).val();
						data.appat = $('[name=appat]',p.$section1).val();
						data.apmat = $('[name=apmat]',p.$section1).val();
						data.docident = p.data.docident;
						data.docident[0] = new Object;
						data.docident[0].tipo = 'DNI';
						data.docident[0].num = $('[name=dni]',p.$section1).val();
						if(data.docident[0].num==''){ $('[name=dni]',p.$section1).focus(); return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'}); }
						if(data.nomb==''){ $('[name=nomb]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'}); }
						if(data.appat==''){ $('[name=appat]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'}); }
						//if(data.apmat==''){ $('[name=apmat]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'}); }
					}
					p.$section2 = p.$w.find('#section1');
					data.domicilios = new Array();
					for(var i=1; i<$('table',p.$section2).length; i++){
						var locales = new Object();
						locales.direccion = $('table',p.$section2).eq(i).find('[name=direc]').val();
						locales.ubig =  $('table',p.$section2).eq(i).find('[name=ubigeo]').val();
						locales.descr =  $('table',p.$section2).eq(i).find('[name=descr]').val();
						if(locales.direccion!=''){
	          				data.domicilios.push(locales);
						}
					}
					p.$section3 = p.$w.find('#section3');
					data.telefonos = new Array();
					for(var i=1; i<$('table',p.$section3).length; i++){
						var telefonos = new Object();
						telefonos.num = $('table',p.$section3).eq(i).find('[name=val]').val();
						telefonos.descr = $('table',p.$section3).eq(i).find('input[name=descr]:last').val();
						if(telefonos.num!='') data.telefonos.push(telefonos);
					}
					p.$section4 = p.$w.find('#section4');
					data.emails = new Array();
					for(var i=1; i<$('table',p.$section4).length; i++){
						var contactoInternet = new Object();
						contactoInternet.direc = $('table',p.$section4).eq(i).find('[name=val]').val();
						if(!($('table',p.$section4).eq(i).find('[name=val]').email())&&$('table',p.$section4).eq(i).find('[name=val]').val()!=''){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'E-mail no v&aacute;lido.',type: 'error'});
						}
						contactoInternet.tipo = 'email';
						contactoInternet.descr = $('table',p.$section4).eq(i).find('input[name=descr]:last').val();
						if(contactoInternet.direc!='') data.emails.push(contactoInternet);
					}
					p.$section5 = p.$w.find('#section5');
					data.urls = new Array();
					for(var i=1; i<$('table',p.$section5).length; i++){
						var contactoInternet = new Object();
						contactoInternet.direc = $('table',p.$section5).eq(i).find('[name=val]').val();
						if(!($('table',p.$section5).eq(i).find('[name=val]').url())&&$('table',p.$section5).eq(i).find('[name=val]').val()!=''){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'URL no v&aacute;lida.',type: 'error'});
						}
						contactoInternet.tipo = 'url';
						contactoInternet.descr = $('table',p.$section5).eq(i).find('input[name=descr]:last').val();
						if(contactoInternet.direc!='') data.urls.push(contactoInternet);
					}
					K.notification({text: 'Enviando informaci&oacute;n...'});
					$('#'+p.$w.attr('id')).dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					p.callBack(data);
				},
				"Cancelar" : function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onResize: function(){ if (dialogLayout) dialogLayout.resizeAll(); },
			onContentLoaded: function(){
				p.$w = $('#windowEntiEdit'+p.id);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$w.find('.ui-layout-west a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					p.$w.find('.ui-layout-center').scrollTo( $('#'+$anchor.attr('href')), 800 );
				}).eq(0).click();
				p.$section1 = p.$w.find('#section1');
				p.$section1.find('table tr:first').remove();
				if(p.tipo_enti=='P') p.$section1.find('[name=rowJur]').remove();
				else p.$section1.find('[name=rowNat]').remove();
				p.$section1.find('[name=dni],[name=ruc]').numeric();
				/*var uploader = new qq.FileUploader({
					element: document.getElementById('buttonUpload'),
					action: 'ci/files/upload',
					debug: true,
					sizeLimit: 2097152,
					allowedExtensions: ['jpg','gif','png'],
					fieldFile: 'foto',
					onSubmit: function(){
				    	p.$section1.find('.img-picture').fadeTo("slow", 0.33);
					},
					onComplete: function(id, fileName, responseJSON){
						p.$section1.find('[name=foto]').val(responseJSON.file).data('id',responseJSON.id.$id);
						p.$section1.find('.img-picture').fadeTo("slow", 1).attr('src','ci/files/get?id='+responseJSON.id.$id);
					}
				});*/
				p.$section1.find('.picture-box').hover(function(){
				  p.$section1.find('.changepicture').show();
				},function(){
				  p.$section1.find('.changepicture').hide();
				}).click(function(){
				  p.$section1.find('[name=file]').click();
				});
				/* Locales */
				p.$section2 = p.$w.find('#section1');
				p.$section2.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',p.$section2).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",p.$section2).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				p.$section2.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					var $table = $(this).closest('table');
					$table.remove();
					if($('table',p.$section2).length<2)
						$('table:last',p.$section2).find('[name=btnAgregar]').click();
					if($('[name=btnAgregar]',p.$section2).length<=1){
						$('table:last',p.$section2).find('[name=btnEliminar]').before("<button name='btnAgregar'>Agregar</button>&nbsp;");
						$('table:last',p.$section2).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				p.$section3 = p.$w.find('#section3');
				/* telefonos */
				p.$section3.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',p.$section3).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$row.find('[name=val]').numeric();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",p.$section3).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				p.$section3.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					if($('table',p.$section3).length<=2)
						return 0;
					$(this).parents('table').remove();
					if($('[name=btnAgregar]',p.$section3).length<=1){
						$('table:last',p.$section3).find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last',p.$section3).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				p.$section4 = p.$w.find('#section4');
				/* Correos */
				p.$section4.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',p.$section4).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",p.$section4).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				p.$section4.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					if($('table',p.$section4).length<=2)
						return 0;
					$(this).parents('table').remove();
					if($('[name=btnAgregar]',p.$section4).length<=1){
						$('table:last',p.$section4).find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last',p.$section4).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				p.$section5 = p.$w.find('#section5');
				/* Sitios */
				p.$section5.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',p.$section5).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",p.$section5).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				p.$section5.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					if($('table',p.$section5).length<=2)
						return 0;
					$(this).parents('table').remove();
					if($('[name=btnAgregar]',p.$section5).length<=1){
						$('table:last',p.$section5).find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last',p.$section5).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				/****       CARGAR DATA      ****/
				if(p.tipo_enti=='P'){
					p.$section1.find("[name=nomb]").val(p.data.nomb);
					p.$section1.find("[name=appat]").val(p.data.appat);
					p.$section1.find("[name=apmat]").val(p.data.apmat);
					p.$section1.find("[name=dni]").val(p.data.docident[0].num);
				}else{
					p.$section1.find("[name=ruc]").val(p.data.docident[0].num);
					p.$section1.find("[name=rsocial]").val(p.data.nomb);
				}
				p.docs = p.data.docident;
				if(p.data.imagen!=null){
					p.$section1.find(".img-picture").attr('src','ci/files/get?id='+p.data.imagen.$id);
				}
				if(p.data.domicilios!=null)
				for(var i=0; i<p.data.domicilios.length; i++){
					if(i>0){ p.$section2.find('[name=btnAgregar]:last').click();}
					p.$section2.find('[name=direc]:last').val(p.data.domicilios[i].direccion);
					p.$section2.find('[name=descr]:last').val(p.data.domicilios[i].descr);
					p.$section2.find('[name=ubigeo]:last').val(p.data.domicilios[i].ubig);
				}
				if(p.data.telefonos!=null)
				for(var i=0; i<p.data.telefonos.length; i++){
					if(i>0){ p.$section3.find('[name=btnAgregar]:last').click();}
					p.$section3.find('[name=val]:last').val(p.data.telefonos[i].num);
					p.$section3.find('select[name=descr]:last option').each(function(){
						if($(this).val()==p.data.telefonos[i].tipo){
							p.$section3.find('input[name=descr]:last').val($(this).text());
							p.$section3.find('input[name=descr]:last').data('id',$(this).val());
							return 0;
						}
					});
				}
				if(p.data.emails!=null)
				for(var i=0; i<p.data.emails.length; i++){
					if(p.$section4.find('[name=val]:last').val()!='') p.$section4.find('[name=btnAgregar]:last').click();
					p.$section4.find('[name=val]:last').val(p.data.emails[i].val);
					p.$section4.find('select[name=descr]:last option').each(function(){
						if($(this).val()==p.data.emails[i].tipo){
							p.$section4.find('input[name=descr]:last').val($(this).text());
							p.$section4.find('input[name=descr]:last').data('id',$(this).val());
							return 0;
						}
					});
				}
				if(p.data.urls!=null)
				for(var i=0; i<p.data.urls.length; i++){
					if(i>0){ p.$section5.find('[name=btnAgregar]:last').click();}
					p.$section5.find('[name=val]:last').val(p.data.urls[i].num);
					p.$section5.find('select[name=descr]:last option').each(function(){
						if($(this).val()==p.data.urls[i].tipo){
							p.$section5.find('input[name=descr]:last').val($(this).text());
							p.$section5.find('input[name=descr]:last').data('id',$(this).val());
							return 0;
						}
					});
				}
				dialogLayout = p.$w.layout({
					resizeWithWindow:	false,
					west__size:			140,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				p.$w.find('.ui-layout-center').css('overflow','hidden');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowAddDocEnti: function(p){
		$.extend(p,{
			types: {
				'OTRO': {
					key: '0',
					descr: 'Otros tipos de Documentos'
				},
				'DNI': {
					key: '1',
					descr: 'Documento Nacional de Identidad'
				},
				'CE': {
					key: '4',
					descr: 'Carnet de Extranjer&iacute;a'
				},
				'RUC': {
					key: '6',
					descr: 'Registro &Uacute;nico de Contribuyentes'
				},
				'PS': {
					key: '7',
					descr: 'Pasaporte'
				},
				'CDI': {
					key: 'A',
					descr: 'C&eacute;dula Diplom&aacute;tica de Identidad'
				}
			}
		});
		new K.Modal({
			id: 'windowAddDocEnti',
			title: 'Agregar '+p.types[p.doc].descr,
			contentURL: 'mg/enti/add_doc',
			icon: 'ui-icon-plusthick',
			width: 350,
			height: 100,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						entidad: p.entidad._id.$id,
						tipo: p.doc,
						num: p.$w.find('[name=num]').val()
					};
					if(data.num==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero para el documento de identidad!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('mg/enti/save_add_doc',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El documento de identidad fue agregado con &eacute;xito!'});
						p.callback(data);
						K.closeWindow(p.$w.attr('id'));
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowAddDocEnti');
				p.$w.find('[name=nomb]').html(ciHelper.enti.formatName(p.entidad));
				p.$w.find('label').html(p.types[p.doc].descr);
			}
		});
	}
};