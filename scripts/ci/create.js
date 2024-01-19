ciCreate = {
	windowNewEntidad: function(p){
		var dialogLayout;
		new K.Modal({
			id: 'winAgregEntidad',
			title: 'Agregar Entidad',
			width: 750,
			height: 400,
			icon: 'ui-icon-person',
			contentURL: 'mg/enti/edit',
			buttons: {
				"Guardar" : function(){
					K.clearNoti();
					var data = new Object();
					p.$section1 = p.$w.find('#section1');
					data = new Object();
					if(p.roles!=null) data.roles = p.roles;
					data.imagen = $section1.find('[name=foto]').data('id');
					if($("[name='rowNat']",p.$section1).css('display')=='none'){
						data.tipo_enti = "E";
						data.nomb = $('[name=rsocial]',p.$section1).val();
						data.docident = new Array();
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
						data.docident = new Array();
						data.docident[0] = new Object;
						data.docident[0].tipo = 'DNI';
						data.docident[0].num = $('[name=dni]',p.$section1).val();
						if(data.docident[0].num==''){ $('[name=dni]',p.$section1).focus(); return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'}); }
						if(data.nomb==''){ $('[name=nomb]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'}); }
						if(data.appat==''){ $('[name=appat]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'}); }
						//if(data.apmat==''){ $('[name=apmat]',p.$section1).focus(); return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'}); }
					}
					$section2 = p.$w.find('#section1');
					data.domicilios = new Array();
					for(var i=1; i<$('table',$section2).length; i++){
						var locales = new Object();
						locales.direccion = $('table',$section2).eq(i).find('[name=direc]').val();
						locales.ubig =  $('table',$section2).eq(i).find('[name=ubigeo]').val();
						locales.descr =  $('table',$section2).eq(i).find('[name=descr]').val();
						if(locales.direccion!=''){
	          				data.domicilios.push(locales);
						}
					}
					$section3 = p.$w.find('#section3');
					data.telefonos = new Array();
					for(var i=1; i<$('table',$section3).length; i++){
						var telefonos = new Object();
						telefonos.num = $('table',$section3).eq(i).find('[name=val]').val();
						telefonos.descr = $('table',$section3).eq(i).find('input[name=descr]:last').val();
						if(telefonos.num!='') data.telefonos.push(telefonos);
					}
					$section4 = p.$w.find('#section4');
					data.emails = new Array();
					for(var i=1; i<$('table',$section4).length; i++){
						var contactoInternet = new Object();
						contactoInternet.direc = $('table',$section4).eq(i).find('[name=val]').val();
						if(!($('table',$section4).eq(i).find('[name=val]').email())&&$('table',$section4).eq(i).find('[name=val]').val()!=''){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'E-mail no v&aacute;lido.',type: 'error'});
						}
						contactoInternet.tipo = 'email';
						contactoInternet.descr = $('table',$section4).eq(i).find('input[name=descr]:last').val();
						if(contactoInternet.direc!='') data.emails.push(contactoInternet);
					}
					$section5 = p.$w.find('#section5');
					data.urls = new Array();
					for(var i=1; i<$('table',$section5).length; i++){
						var contactoInternet = new Object();
						contactoInternet.direc = $('table',$section5).eq(i).find('[name=val]').val();
						if(!($('table',$section5).eq(i).find('[name=val]').url())&&$('table',$section5).eq(i).find('[name=val]').val()!=''){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'URL no v&aacute;lida.',type: 'error'});
						}
						contactoInternet.tipo = 'url';
						contactoInternet.descr = $('table',$section5).eq(i).find('input[name=descr]:last').val();
						if(contactoInternet.direc!='') data.urls.push(contactoInternet);
					}
					if(p.reqs!=null){
						if(p.reqs.domicilios!=null){
							if(data.domicilios.length<1){
								p.$w.find('.ui-layout-west [href=section2]:first').click();
								$section2.find('[name=descr]:first').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos una direcci&oacute;n!',type: 'error'});
							}
						}
						if(p.reqs.tipo_enti!=null){
							if(p.reqs.tipo_enti=='P'&&data.tipo_enti=='E'){
								p.$w.find('.ui-layout-west [href=section1]:first').click();
								$section2.find('input:first').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'La entidad debe ser una persona natural!',type: 'error'});
							}else if(p.reqs.tipo_enti=='E'&&data.tipo_enti=='P'){
								p.$w.find('.ui-layout-west [href=section1]:first').click();
								$section2.find('input:first').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'La entidad debe ser una persona jur&iacute;dica!',type: 'error'});
							}
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('mg/enti/save',data,function(rpta){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Entidad agregada!'});
						K.closeWindow(p.$w.attr('id'));
						p.callBack(rpta);
					},'json');
				},
				"Cancelar" : function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onResize: function(){ if (dialogLayout) dialogLayout.resizeAll(); },
			onContentLoaded: function(){
				p.$w = $('#winAgregEntidad');
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
				$section1 = p.$w.find('#section1');
				p.$w.find('#rbtnAgEntJur').click(function(){
					p.$w.find('[name=rowNat]').hide();
					p.$w.find('[name=rowJur]').show();
				});
				p.$w.find('#rbtnAgEntNat').click(function(){
					p.$w.find('[name=rowJur]').hide();
					p.$w.find('[name=rowNat]').show();
				}).click();
				p.$w.find('#rbtnTipo').buttonset();
				$section1.find('[name=dni],[name=ruc]').numeric();
				//setup before functions
				var typingTimer;                //timer identifier
				//on keyup, start the countdown
				$section1.find('[name=dni],[name=ruc]').keyup(function(){
					$this = $(this);
					clearTimeout(typingTimer);
				    typingTimer = setTimeout(function(){
				    	//user is "finished typing," do something
				    	$.post('mg/enti/check','doc='+$this.val(),function(data){
				    		$('#winAgregEntidad').data('data',data.enti);
				    		if(data.enti!=null){
				    			K.clearNoti();
				    			K.notification({text: 'El Documento de Identidad ya est&aacute; relacionado a una entidad registrada!<br /><button id="btnNotiSelect">Seleccionar</button>',hide: false});
				    			ciDetails.windowDetailsEnti({
									id: data.enti._id.$id,
									tipo_enti: data.enti.tipo_enti,
									nomb: ciHelper.enti.formatName(data.enti),
									modal: true,
									buttons: {
					    				"Seleccionar": function(){
						    				K.clearNoti();
							    			if(p.callBack!=null)
						    					p.callBack($('#winAgregEntidad').data('data'));
							    			else
							    				$('#pageWrapperLeft .ui-state-highlight').click();
							    			K.closeWindow('windowDetailEnti'+$('#winAgregEntidad').data('data')._id.$id);
							    			K.closeWindow('winAgregEntidad');
							    		}
					    			}
					    		});
					    		$('#btnNotiSelect').click(function(){
					    			K.clearNoti();
					    			if(p.callBack!=null)
				    					p.callBack($('#winAgregEntidad').data('data'));
					    			else
					    				$('#pageWrapperLeft .ui-state-highlight').click();
					    			K.closeWindow('windowDetailEnti'+$('#winAgregEntidad').data('data')._id.$id);
					    			K.closeWindow('winAgregEntidad');
					    		}).button({icons: {primary: 'ui-icon-person'}});
				    		}
				    	},'json');
				    }, 2000);
				});
				//on keydown, clear the countdown 
				$section1.find('[name=dni],[name=ruc]').keydown(function(){
				    clearTimeout(typingTimer);
				});
				/*var uploader = new qq.FileUploader({
					element: document.getElementById('buttonUpload'),
					action: 'ci/files/upload',
					debug: true,
					sizeLimit: 2097152,
					allowedExtensions: ['jpg','gif','png'],
					fieldFile: 'foto',
					onSubmit: function(){
				    	$section1.find('.img-picture').fadeTo("slow", 0.33);
					},
					onComplete: function(id, fileName, responseJSON){
						$section1.find('[name=foto]').val(responseJSON.file).data('id',responseJSON.id.$id);
						$section1.find('.img-picture').fadeTo("slow", 1).attr('src','ci/files/get?id='+responseJSON.id.$id);
					}
				});
				$section1.find('.picture-box').hover(function(){
				  $section1.find('.changepicture').show();
				},function(){
				  $section1.find('.changepicture').hide();
				}).click(function(){
				  $section1.find('[name=file]').click();
				});*/
				/* Locales */
				$section2 = p.$w.find('#section1');
				$section2.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',$section2).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",$section2).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$section2.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					var $table = $(this).closest('table');
					$table.remove();
					if($('table',$section2).length<2)
						$('table:last',$section2).find('[name=btnAgregar]').click();
					if($('[name=btnAgregar]',$section2).length<=1){
						$('table:last',$section2).find('[name=btnEliminar]').before("<button name='btnAgregar'>Agregar</button>&nbsp;");
						$('table:last',$section2).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				$section3 = p.$w.find('#section3');
				/* telefonos */
				$section3.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',$section3).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$row.find('[name=val]').numeric();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",$section3).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$section3.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					if($('table',$section3).length<=2)
						return 0;
					$(this).parents('table').remove();
					if($('[name=btnAgregar]',$section3).length<=1){
						$('table:last',$section3).find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last',$section3).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				$section4 = p.$w.find('#section4');
				/* Correos */
				$section4.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',$section4).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",$section4).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$section4.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					if($('table',$section4).length<=2)
						return 0;
					$(this).parents('table').remove();
					if($('[name=btnAgregar]',$section4).length<=1){
						$('table:last',$section4).find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last',$section4).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				$section5 = p.$w.find('#section5');
				/* Sitios */
				$section5.undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]',$section5).clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset",$section5).append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$section5.undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					if($('table',$section5).length<=2)
						return 0;
					$(this).parents('table').remove();
					if($('[name=btnAgregar]',$section5).length<=1){
						$('table:last',$section5).find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last',$section5).find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
					
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
	}
};