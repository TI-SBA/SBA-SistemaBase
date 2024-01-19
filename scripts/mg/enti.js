/*******************************************************************************
Entidades */
mgEnti = {
	formatName: function(data){
		if(data==null) return '--';
		if(data.fullname!=null) return data.fullname;
		if(data.nomb==null) return '--';
		var nomb = data.nomb;
		if(data.tipo_enti=='P'){
			if(data.apmat==null||data.apmat=='') nomb = data.appat+', '+data.nomb;
			else nomb = data.appat+' '+data.apmat+', '+data.nomb;
		}
		return nomb;
	},
	formatNameNorm: function(data){
		if(data==null) return '--';
		if(data.nomb==null) return '--';
		var nomb = data.nomb;
		if(data.tipo_enti=='P'){
			if(data.apmat==null||data.apmat=='') nomb = data.nomb+' '+data.appat;
			else nomb = data.nomb+' '+data.appat+' '+data.apmat;
		}
		return nomb;
	},
	formatIden: function(data){
		if(data==null) return '--';
		if(data.docident==null) return '--';
		if(data.docident[0]==null) return '--';
		var tipo = data.docident[0].tipo;
		return tipo+' '+data.docident[0].num;
	},
	formatDNI: function(data){
		if(data==null) return '--';
		if(data.docident==null) return '--';
		for(var i=0; i<data.docident.length; i++){
			if(data.docident[i].tipo=='DNI')
				return data.docident[i].num;
		}
		return '--';
	},
	formatRUC: function(data){
		if(data==null) return '--';
		if(data.docident==null) return '--';
		for(var i=0; i<data.docident.length; i++){
			if(data.docident[i].tipo=='RUC')
				return data.docident[i].num;
		}
		return '--';
	},
	formatTelf: function(data){
		if(data==null) return '--';
		if(data.telefonos==null) return '--';
		return data.telefonos[0].num;
	},
	formatDire: function(data){
		if(data==null) return '--';
		if(data.domicilios==null) return '--';
		return data.domicilios[0].direccion;
	},
	dbRel: function(data){
		if(data==null) return false;
		var enti = {
			_id: data._id.$id || data._id,
			nomb: data.nomb.trim(),
			tipo_enti: data.tipo_enti
		};
		if(data.tipo_enti=='P'){
			enti.appat = data.appat.trim();
			enti.apmat = data.apmat.trim();
		}else if(data.tipo_enti==null){
			data.tipo_enti = 'E';
			if(data.appat!=null){
				data.tipo_enti = 'P';
			}
		}
		if(data.docident!=null){
			enti.docident = [];
			for(var i=0; i<data.docident.length; i++){
				enti.docident.push({
					tipo: data.docident[i].tipo,
					num: data.docident[i].num
				});
			}
		}
		if(data.domicilios!=null){
			enti.domicilios = [];
			for(var i=0; i<data.domicilios.length; i++){
				enti.domicilios.push({
					direccion: data.domicilios[i].direccion
				});
			}
		}
		return enti;
	},
	fillMini: function($w,data,tmp_image){
		if(data==null) return false;
		$w.data('data',data);
		$w.find('[name=nomb] td:eq(1)').html(mgEnti.formatName(data));
		if(data.imagen!=null){
			$w.find('img').parent().show();
			if(data.imagen.$id!=null)
				$w.find('img').attr('src','ci/files/get?id='+data.imagen.$id);
			else
				$w.find('img').attr('src',K.path_file+data.imagen);
			$w.find('.col-lg-12:first').removeClass('col-lg-12').addClass('col-lg-9');
		}else{
			if(tmp_image==null){
				$w.find('img').parent().hide();
				$w.find('.col-lg-9:first').removeClass('col-lg-9').addClass('col-lg-12');
			}else{
				$w.find('img').parent().show();
				$w.find('img').attr('src','images/usuario.jpg');
				$w.find('.col-lg-12:first').removeClass('col-lg-12').addClass('col-lg-9');
			}
		}
		$w.find('[name=dni],[name=ruc]').hide();
		if(data.docident!=null){
			for(var i=0; i<data.docident.length; i++){
				if(data.docident[i].tipo=='DNI'){
					$w.find('[name=dni]').show();
					$w.find('[name=dni] td:eq(1)').html(data.docident[i].num);
				}else if(data.docident[i].tipo=='RUC'){
					$w.find('[name=ruc]').show();
					$w.find('[name=ruc] td:eq(1)').html(data.docident[i].num);
				}
			}
		}
		$w.find('[name=telef] td:eq(1)').html('');
		if(data.telefonos!=null){
			for(var i=0; i<data.telefonos.length; i++){
				if($w.find('[name=telef] td:eq(1)').html()!='')
					$w.find('[name=telef] td:eq(1)').append('<br />');
				if(data.telefonos[i].num!='')
					$w.find('[name=telef] td:eq(1)').append('<a href="tel:'+data.telefonos[i].num+'">'+data.telefonos[i].num+'</a>');
			}
			if($w.find('[name=telef] td:eq(1)').html()!='') $w.find('[name=telef]').show();
		}else $w.find('[name=telef]').hide();
		$w.find('[name=email] td:eq(1)').html('');
		if(data.emails!=null){
			for(var i=0; i<data.emails.length; i++){
				if($w.find('[name=email] td:eq(1)').html()!='')
					$w.find('[name=email] td:eq(1)').append('<br />');
				if(data.emails[i].direc!='')
					$w.find('[name=email] td:eq(1)').append('<a href="mailto:'+data.emails[i].direc+'">'+data.emails[i].direc+'</a> ('+data.emails[i].descr+')');
			}
			if($w.find('[name=email] td:eq(1)').html()!='') $w.find('[name=email]').show();
		}else $w.find('[name=email]').hide();
		$w.find('[name=url] td:eq(1)').html('');
		if(data.urls!=null){
			for(var i=0; i<data.urls.length; i++){
				if($w.find('[name=url] td:eq(1)').html()!='')
					$w.find('[name=url] td:eq(1)').append('<br />');
				if(data.urls[i].direc!='')
					$w.find('[name=url] td:eq(1)').append('<a href="'+data.urls[i].direc+'" target="_blank">'+data.urls[i].direc+'</a>');
			}
			if($w.find('[name=url] td:eq(1)').html()!='') $w.find('[name=url]').show();
		}else $w.find('[name=url]').hide();
		$w.find('[name=direc] td:eq(1)').html('');
		if(data.domicilios!=null){
			for(var i=0; i<data.domicilios.length; i++){
				if($w.find('[name=direc] td:eq(1)').html()!='')
					$w.find('[name=direc] td:eq(1)').append('<br />');
				if(data.domicilios[i].direccion!='')
					$w.find('[name=direc] td:eq(1)').append(data.domicilios[i].direccion+'</a>');
			}
			if($w.find('[name=direc] td:eq(1)').html()!='') $w.find('[name=direc]').show();
		}else $w.find('[name=direc]').hide();
	},
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgEnti',
			titleBar: {
				title: 'Entidades'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'mg/enti',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de entidad' ).width('250');
				$mainPanel.find('[name=obj]').html( 'entidades' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').remove();
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13)
						$('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						mgEnti.loadData({page: 1,url: 'mg/enti/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						mgEnti.loadData({page: 1,url: 'mg/enti/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				mgEnti.loadData({page: 1,url: 'mg/enti/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $mainPanel.find('[name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(1).html( ciHelper.enti.formatName(result) );
                  	if(result.docident!=null)
                      	if(result.docident[0]!=null)
							$li.eq(2).html( result.docident[0].tipo+' '+result.docident[0].num );
					if(result.domicilios != null)
						if(result.domicilios.length > 0 )
							$li.eq(3).html( result.domicilios[0].direccion );
					if(result.telefonos != null)
						if(result.telefonos.length > 0 )						
							$li.eq(4).html( result.telefonos[0].num );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('tipo_enti',result.tipo_enti).dblclick(function(){
						ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),nomb: $(this).find('li:eq(1)').html()});
					}).click(function(event){
						K.gridButtons({
							$row: $(this),
							event: event,
							index: 0,
							buttons: [{
								label: "Editar",
								icon: "ui-icon-pencil",
								callback: function($row){
									$.post('mg/enti/get','_id='+$row.data('id'),function(data){
										var params = new Object;
										params.id = $row.data('id');
										params.nomb = $row.find('li:eq(1)').html();
										params.tipo_enti = $row.data('tipo_enti');
										params.data = data;
										params.callBack = function(data){
											$.post('cm/prop/save',data,function(rpta){
												K.notification({title: ciHelper.titleMessages.regiAct,text: 'Entidad actualizada!'});
												K.closeWindow('windowEntiEdit'+data._id);
												if($.cookie('action')=='mgEnti') mgEnti.loadData();
											});
										};
										ciEdit.windowEditEntidad(params);
									},'json');
								}
							}/*,
							{
								label: "Eliminar",
								icon: "ui-icon-closethick",
								callback: function($row){
									var params = new Object;
									params.id = $row.data('id');
									params.nomb = $row.find('li').eq(1).html();
									mgEnti.windowDel(params);
								}
							}*/]
						});
					}).contextMenu('conMenList', {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							$('#conMenList_eli,#conMenList_imp', menu).remove();
							return menu;
						},
						bindings: {
							'conMenList_edi': function(t) {
								$.post('mg/enti/get','_id='+K.tmp.data('id'),function(data){
									var params = new Object;
									params.id = K.tmp.data('id');
									params.nomb = K.tmp.find('li:eq(1)').html();
									params.tipo_enti = K.tmp.data('tipo_enti');
									params.data = data;
									params.callBack = function(data){
										$.post('cm/prop/save',data,function(rpta){
											K.notification({title: ciHelper.titleMessages.regiAct,text: 'Entidad actualizada!'});
											K.closeWindow('windowEntiEdit'+data._id);
											if($.cookie('action')=='mgEnti') mgEnti.loadData();
										});
									};
									ciEdit.windowEditEntidad(params);
								},'json');
							},
							'conMenList_eli': function(t) {
								var params = new Object;
								params.id = K.tmp.data('id');
								params.nomb = K.tmp.find('li').eq(1).html();
								mgEnti.windowDel(params);
							},
							'conMenList_about': function(t) {
								K.about();
							}
						}
					});
		        	$("#mainPanel .gridBody").append( $row.children() );
		        }
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results [name=showing]').html( count );
		        $('#Results [name=founded]').html( data.paging.total_items );
		        $('#Results').show();
		        
		        $moreresults = $("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						mgEnti.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
		        }else{
					$("#mainPanel .gridFoot").hide();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
		        }
	      } else {
	        $('#No-Results').show();
	        $('#Results').hide();
	        $( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowDel: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar entidad '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> la entidad <strong>'+p.nomb+'</strong>&#63;',
			type: 'modal',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function() {
					K.notification('Enviando informaci&oacute;n...');
					$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					$.post('ge/enti/delete_enti','id='+p.id,function(){
						K.notification({text: 'Entidad eliminada!',layout: 'topLeft'});
						K.closeWindow('windowDelete');
						if($('#windowDetailEnti'+p.id).length>0) K.closeWindow('windowDetailEnti'+p.id);
						if($.cookie('mode')=='mgEnti') mgEnti.init();
					});
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	},









	windowNew: function(p){
		new K.Modal({
			title: 'Ingrese el DNI de la persona',
			contentURL: ''
		});
	},
	windowNew_check: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowEdit',
			title: 'Crear Nueva Entidad',
			contentURL: 'mg/enti/edit',
			allScreen: true,
			buttons: {
				'Guardar y Actualizar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							tipo_enti: p.$w.find('[name=tipo_enti]:checked').val(),
							nomb: p.$w.find('[name=nomb]').val()
						};
						if(data.tipo_enti=='P'){
							$.extend(data,{
								appat: p.$w.find('[name=appat]').val(),
								apmat: p.$w.find('[name=apmat]').val()
							});
						}
						var tmp = p.$w.find('[name=dni]').val();
						if(tmp!=''){
							if(data.docident==null) data.docident = [];
							data.docident.push({tipo: 'DNI',num: tmp});
						}
						var tmp = p.$w.find('[name=ruc]').val();
						if(tmp!=''){
							if(data.docident==null) data.docident = [];
							data.docident.push({tipo: 'RUC',num: tmp});
						}
						var tmp = p.$w.find('[name=direc_fis]').val();
						if(tmp!=''){
							if(data.domicilios==null) data.domicilios = [];
							data.domicilios.push({tipo: 'FISCAL',direccion: tmp});
						}
						var tmp = p.$w.find('[name=direc_per]').val();
						if(tmp!=''){
							if(data.domicilios==null) data.domicilios = [];
							data.domicilios.push({tipo: 'PERSONAL',direccion: tmp});
						}
						for(var i=0; i<p.$w.find('[name=gridTele] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridTele] tbody .item').eq(i),
							tmp = {
								tipo: $row.find('[name=tipo] :selected').val(),
								num: $row.find('[name=num]').val()
							};
							if(tmp.num!=''){
								if(data.telefonos==null) data.telefonos = [];
								data.telefonos.push(tmp);
							}
						}
						for(var i=0; i<p.$w.find('[name=gridMail] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridMail] tbody .item').eq(i),
							tmp = {
								tipo: $row.find('[name=tipo] :selected').val(),
								direc: $row.find('[name=num]').val()
							};
							if(tmp.direc!=''){
								if(data.emails==null) data.emails = [];
								data.emails.push(tmp);
							}
						}
						for(var i=0; i<p.$w.find('[name=gridSiti] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridSiti] tbody .item').eq(i),
							tmp = {
								tipo: $row.find('[name=tipo] :selected').val(),
								direc: $row.find('[name=num]').val()
							};
							if(tmp.direc!=''){
								if(data.urls==null) data.urls = [];
								data.urls.push(tmp);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/enti/save",data,function(entidad){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Entidad creada!"});
							p.callback(entidad);
							K.closeWindow(p.$w.attr('id'));
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEdit');
				if(p.tipo_enti!=null){
					if(p.tipo_enti=='E'){
						p.$w.find('.form-group:eq(2),.form-group:eq(3),.form-group:eq(6)').hide();
					}
					p.$w.find('[name=tipo_enti]').attr('disabled','disabled');
					p.$w.find('.iradio').iCheck({
						checkboxClass: 'icheckbox_square-green',
						radioClass: 'iradio_square-green'
					});
				}
				p.$w.find('[name=btnFoto]').click(function(){
					mgMult.windowNewModule({
						ruta: '/Usuarios/Perfiles',
						nomb: p.id,
						callback: function(data){
							ext = data.file.substr(data.file.indexOf('.'));
							p.$w.find('img').attr('src',K.path_file+'/Usuarios/Perfiles/'+p.id+ext+'#'+new Date().getTime());
							$.post("mg/enti/save",{
								_id: p.id,
								imagen: '/Usuarios/Perfiles/'+p.id+ext
							},function(){
								K.clearNoti();
								K.notification({title: ciHelper.titleMessages.regiGua,text: "Foto de Entidad actualizada con &eacute;xito!"});
							});
						}
					});
				});
				new K.grid({
					$el: p.$w.find('[name=gridTele]'),
					search: false,
					pagination: false,
					cols: ['Tipo','N&uacute;mero',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Agregar Tel&eacute;fono</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
								'<option value="Celular">Celular</option>'+
								'<option value="Fijo">Fijo</option>'+
								'</select></td>');
							$row.append('<td><input type="text" name="num" class="form-control" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridTele] tbody').append($row);
			   			});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridMail]'),
					search: false,
					pagination: false,
					cols: ['Tipo','N&uacute;mero',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Agregar Correo Electr&oacute;nico</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
								'<option value="Personal">Personal</option>'+
								'<option value="Trabajo">Trabajo</option>'+
								'</select></td>');
							$row.append('<td><input type="text" name="num" class="form-control" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridMail] tbody').append($row);
			   			});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridSiti]'),
					search: false,
					pagination: false,
					cols: ['Tipo','N&uacute;mero',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Agregar Sitio Web</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
								'<option value="Personal">Personal</option>'+
								'<option value="Trabajo">Trabajo</option>'+
								'</select></td>');
							$row.append('<td><input type="text" name="num" class="form-control" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridSiti] tbody').append($row);
			   			});
					}
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({
			id: 'windowEdit',
			title: 'Editar Datos de Entidad',
			contentURL: 'mg/enti/edit_bootstrap',
			allScreen: true,
			buttons: {
				'Guardar y Actualizar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							tipo_enti: p.$w.find('[name=tipo_enti]:checked').val(),
							nomb: p.$w.find('[name=nomb]').val()
						};
						if(data.tipo_enti=='P'){
							$.extend(data,{
								appat: p.$w.find('[name=appat]').val(),
								apmat: p.$w.find('[name=apmat]').val()
							});
						}
						var tmp = p.$w.find('[name=dni]').val();
						if(tmp!=''){
							if(data.docident==null) data.docident = [];
							data.docident.push({tipo: 'DNI',num: tmp});
						}
						var tmp = p.$w.find('[name=ruc]').val();
						if(tmp!=''){
							if(data.docident==null) data.docident = [];
							data.docident.push({tipo: 'RUC',num: tmp});
						}
						var tmp = p.$w.find('[name=direc_fis]').val();
						if(tmp!=''){
							if(data.domicilios==null) data.domicilios = [];
							data.domicilios.push({tipo: 'FISCAL',direccion: tmp});
						}
						var tmp = p.$w.find('[name=direc_per]').val();
						if(tmp!=''){
							if(data.domicilios==null) data.domicilios = [];
							data.domicilios.push({tipo: 'PERSONAL',direccion: tmp});
						}
						for(var i=0; i<p.$w.find('[name=gridTele] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridTele] tbody .item').eq(i),
							tmp = {
								tipo: $row.find('[name=tipo] :selected').val(),
								num: $row.find('[name=num]').val()
							};
							if(tmp.num!=''){
								if(data.telefonos==null) data.telefonos = [];
								data.telefonos.push(tmp);
							}
						}
						for(var i=0; i<p.$w.find('[name=gridMail] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridMail] tbody .item').eq(i),
							tmp = {
								tipo: $row.find('[name=tipo] :selected').val(),
								direc: $row.find('[name=num]').val()
							};
							if(tmp.direc!=''){
								if(data.emails==null) data.emails = [];
								data.emails.push(tmp);
							}
						}
						for(var i=0; i<p.$w.find('[name=gridSiti] tbody .item').length; i++){
							var $row = p.$w.find('[name=gridSiti] tbody .item').eq(i),
							tmp = {
								tipo: $row.find('[name=tipo] :selected').val(),
								direc: $row.find('[name=num]').val()
							};
							if(tmp.direc!=''){
								if(data.urls==null) data.urls = [];
								data.urls.push(tmp);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/enti/save",data,function(entidad){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Entidad actualizada!"});
							p.callback(entidad);
							K.closeWindow(p.$w.attr('id'));
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEdit');
				K.block();
				p.$w.find('.iradio').iCheck({
					checkboxClass: 'icheckbox_square-green',
					radioClass: 'iradio_square-green'
				});
				p.$w.find('[name=btnFoto]').click(function(){
					mgMult.windowNewModule({
						ruta: '/Usuarios/Perfiles',
						nomb: p.id,
						callback: function(data){
							ext = data.file.substr(data.file.indexOf('.'));
							p.$w.find('img').attr('src',K.path_file+'/Usuarios/Perfiles/'+p.id+ext+'#'+new Date().getTime());
							$.post("mg/enti/save",{
								_id: p.id,
								imagen: '/Usuarios/Perfiles/'+p.id+ext
							},function(){
								K.clearNoti();
								K.notification({title: ciHelper.titleMessages.regiGua,text: "Foto de Entidad actualizada con &eacute;xito!"});
							});
						}
					});
				});
				new K.grid({
					$el: p.$w.find('[name=gridTele]'),
					search: false,
					pagination: false,
					cols: ['Tipo','N&uacute;mero',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Agregar Tel&eacute;fono</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
								'<option value="Celular">Celular</option>'+
								'<option value="Fijo">Fijo</option>'+
								'</select></td>');
							$row.append('<td><input type="text" name="num" class="form-control" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridTele] tbody').append($row);
			   			});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridMail]'),
					search: false,
					pagination: false,
					cols: ['Tipo','N&uacute;mero',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Agregar Correo Electr&oacute;nico</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
								'<option value="Personal">Personal</option>'+
								'<option value="Trabajo">Trabajo</option>'+
								'</select></td>');
							$row.append('<td><input type="text" name="num" class="form-control" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridMail] tbody').append($row);
			   			});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridSiti]'),
					search: false,
					pagination: false,
					cols: ['Tipo','N&uacute;mero',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-primary"><i class="fa fa-file-text-o"></i> Agregar Sitio Web</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
								'<option value="Personal">Personal</option>'+
								'<option value="Trabajo">Trabajo</option>'+
								'</select></td>');
							$row.append('<td><input type="text" name="num" class="form-control" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridSiti] tbody').append($row);
			   			});
					}
				});
				$.post('mg/enti/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					$('#tipo_enti_1, #tipo_enti_2').iCheck('uncheck');
					if(data.tipo_enti=='E'){
						$('#tipo_enti_2').iCheck('check');
						//p.$w.find('.form-group:eq(2),.form-group:eq(3),.form-group:eq(6)').hide();
					}else{
						$('#tipo_enti_1').iCheck('check');
						p.$w.find('[name=appat]').val(data.appat);
						p.$w.find('[name=apmat]').val(data.apmat);
					}
					//$('#tipo_enti_1, #tipo_enti_2').iCheck('disable');
					if(data.imagen!=null){
						if(data.imagen.$id!=null){
							p.$w.find('img').attr('src','ci/files/get?id='+data.imagen.$id);
						}else{
							p.$w.find('img').attr('src',K.path_file+data.imagen);
						}
					}
					if(data.docident!=null){
						for(var i=0; i<data.docident.length; i++){
							if(data.docident[i].tipo=='DNI'){
								p.$w.find('[name=dni]').val(data.docident[i].num);
							}else if(data.docident[i].tipo=='RUC'){
								p.$w.find('[name=ruc]').val(data.docident[i].num);
							}
						}
					}
					if(data.domicilios!=null){
						for(var i=0; i<data.domicilios.length; i++){
							if(data.domicilios[i].tipo==null){
								p.$w.find('[name=direc_per]').val(data.domicilios[i].direccion);
							}else if(data.domicilios[i].tipo=='FISCAL'){
								p.$w.find('[name=direc_fis]').val(data.domicilios[i].direccion);
							}else if(data.domicilios[i].tipo=='PERSONAL'){
								p.$w.find('[name=direc_per]').val(data.domicilios[i].direccion);
							}
						}
					}
					if(data.telefonos!=null){
						for(var i=0; i<data.telefonos.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
									'<option value="Celular">Celular</option>'+
									'<option value="Fijo">Fijo</option>'+
								'</select></td>');
							if(data.telefonos[i].tipo!=null)
								$row.find('select').selectVal(data.telefonos[i].tipo);
							$row.append('<td><input type="text" name="num" class="form-control" value="'+data.telefonos[i].num+'" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridTele] tbody').append($row);
						}
					}
					if(data.emails!=null){
						for(var i=0; i<data.emails.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
									'<option value="Personal">Personal</option>'+
									'<option value="Trabajo">Trabajo</option>'+
								'</select></td>');
							if(data.emails[i].tipo!=null)
								$row.find('select').selectVal(data.emails[i].tipo);
							$row.append('<td><input type="text" name="num" class="form-control" value="'+data.emails[i].direc+'" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridMail] tbody').append($row);
						}
					}
					if(data.urls!=null){
						for(var i=0; i<data.urls.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td><select name="tipo" class="form-control">'+
									'<option value="Personal">Personal</option>'+
									'<option value="Trabajo">Trabajo</option>'+
								'</select></td>');
							if(data.urls[i].tipo!=null)
								$row.find('select').selectVal(data.urls[i].tipo);
							$row.append('<td><input type="text" name="num" class="form-control" value="'+data.urls[i].direc+'" /></td>');
							$row.append('<td><button class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridSiti] tbody').append($row);
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
















	windowSelectOld: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowSelectEnti',
			title: 'Seleccionar Entidad',
			contentURL: 'mg/enti/select',
			icon: 'ui-icon-group',
			width: 560,
			height: 185,
			buttons: {
				'Seleccionar y Actualizar': function(){
						var ent = p.$w.find('[name=search]').data('data');
						if(ent!=null){
							var data = {
								_id: ent._id.$id,
								nomb: p.$w.find('[name=nomb]').val(),
								tipo_enti: ent.tipo_enti,
								telefonos: [{num: p.$w.find('[name=telf]').val()}],
								domicilios: [{direccion: p.$w.find('[name=direc]').val()}]
							};
							if(data.nomb==''){
								p.$w.find('[name=nomb]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
							}
							if(data.tipo_enti=='P'){
								data.appat = p.$w.find('[name=appat]').val();
								if(data.appat==''){
									p.$w.find('[name=appat]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'});
								}
								data.apmat = p.$w.find('[name=apmat]').val();
								if(data.apmat==''){
									p.$w.find('[name=apmat]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'});
								}
								data.fecnac = p.$w.find('[name=fecnac]').val();
								data.dni = p.$w.find('[name=dni]').val();
								if(data.dni==''){
									p.$w.find('[name=dni]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'});
								}
							}else{
								data.ruc = p.$w.find('[name=dni]').val();
								if(data.ruc==''){
									p.$w.find('[name=dni]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
								}
							}
							$.post('mg/enti/save',data,function(ent){
								p.callback(ent);
								K.closeWindow(p.$w.attr('id'));
							},'json');
						}else{
							var data = {
								nomb: p.$w.find('[name=nomb]').val(),
								tipo_enti: p.$w.find('[name=tipo] option:selected').val(),
								telefonos: [{num: p.$w.find('[name=telf]').val()}],
								domicilios: [{direccion: p.$w.find('[name=direc]').val()}]
							};
							if(data.nomb==''){
								p.$w.find('[name=nomb]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
							}
							if(data.tipo_enti=='P'){
								data.appat = p.$w.find('[name=appat]').val();
								if(data.appat==''){
									p.$w.find('[name=appat]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'});
								}
								data.apmat = p.$w.find('[name=apmat]').val();
								if(data.apmat==''){
									p.$w.find('[name=apmat]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'});
								}
								data.fecnac = p.$w.find('[name=fecnac]').val();
								data.dni = p.$w.find('[name=dni]').val();
								if(data.dni==''){
									p.$w.find('[name=dni]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'});
								}
							}else{
								data.ruc = p.$w.find('[name=dni]').val();
								if(data.ruc==''){
									p.$w.find('[name=dni]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
								}
							}
							if(p.rol!=null){
								data.rol = p.rol;
							}
							$.post('mg/enti/save',data,function(ent){
								p.callback(ent);
								K.closeWindow(p.$w.attr('id'));
							},'json');
						}
				},
				"Cancelar": function(pa){
						K.closeWindow(p.$w.attr('id'));
					}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectEnti');
				p.enti = [];
				p.$w.find('[name=dni]').numeric();
				p.$w.find('[name=telf]').numeric();
				p.$w.find('[name=btnFec]').click(function(){
					if(p.$w.find('[name=tipo] option:selected').val()=='P')
						ciHelper.datepicker({date: p.$w.find('[name=fecnac]').val(),callback:function(data){
							p.$w.find('[name=fecnac]').val(data);
						}});
				});
				p.$w.find('[name=tipo]').change(function(){
					if($(this).find('option:selected').val()=='P'){
			    		p.$w.find('[name=appat]').removeAttr('disabled');
			    		p.$w.find('[name=apmat]').removeAttr('disabled');
			    		p.$w.find('[name=fecnac]').removeAttr('disabled');
					}else{
			    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
			    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
			    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
			    	}
				});
				p.$w.find('[name=search]').autocomplete({
					delay: 500,
					change: function( event, ui ) {
						p.$w.find('[name=search]').autocomplete( "search", "" );
					},
					source: function(q, process) {
						var par = {
							texto: q.term,
							page_rows:8,
							page:1,
							rol: p.rol
						};
						if(p.filter!=null)
							par.filter = p.filter;
						$.post('mg/enti/search',par,function(data){
							p.enti = [];
							$.each(data.items,function(i,item){
								p.enti.push({
									item: item,
									label: item.fullname+' - '+item.docident[0].num,
									value: item.fullname+' - '+item.docident[0].num
								});
							});
							if(data.items!=null){
								process(p.enti);
							}else{
								process([]);
							}
						},'json');
				    },
					minLength: 3,
					select : function( event, ui) {
						var ent = ui.item.item;
						p.$w.find('[name=search]').data('data',ent);
					    if(ent!=null){
					    	if(ent.tipo_enti=='P'){
					    		p.$w.find('[name=tipo]').selectVal('P').attr('disabled','disabled');
					    		p.$w.find('[name=nomb]').val(ent.nomb);
					    		p.$w.find('[name=appat]').val(ent.appat).removeAttr('disabled');
					    		p.$w.find('[name=apmat]').val(ent.apmat).removeAttr('disabled');
					    		p.$w.find('[name=dni]').val(mgEnti.formatDNI(ent));
					    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
					    		if(ent.domicilios!=null)
					    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
					    		if(ent.fecnac!=null)
					    			p.$w.find('[name=fecnac]').val(ent.fecnac).removeAttr('disabled');
					    	}else{
					    		p.$w.find('[name=tipo]').selectVal('E').attr('disabled','disabled');
					    		p.$w.find('[name=nomb]').val(ent.nomb);
					    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
					    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
					    		p.$w.find('[name=dni]').val(mgEnti.formatRUC(ent));
					    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
					    		if(ent.domicilios!=null)
					    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
					    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
					    	}
					    }
					}
				}).blur(function(){
					var ent = p.$w.find('[name=search]').data('data'),
					nomb = p.$w.find('[name=search]').val();
					if(nomb==''){
						p.$w.find('[name=tipo]').selectVal('P').removeAttr('disabled');
						p.$w.find('[name=search]').removeData('data');
						p.$w.find('[name=nomb]').val('');
			    		p.$w.find('[name=appat]').val('').removeAttr('disabled');
			    		p.$w.find('[name=apmat]').val('').removeAttr('disabled');
			    		p.$w.find('[name=dni]').val('');
			    		p.$w.find('[name=telf]').val('');
			    		p.$w.find('[name=fecnac]').val('').removeAttr('disabled');
					}
				});
				var typingTimer; 					
				p.$w.find('[name=dni]').keyup(function(){
					$this = $(this);
					clearTimeout(typingTimer);
				    typingTimer = setTimeout(function(){
				    	//user is "finished typing," do something
				    	$.post('mg/enti/check','doc='+$this.val(),function(data){
				    		$('#windowSelectEnti').data('data',data.enti);
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
							    			if(p.callback!=null)
						    					p.callback($('#windowSelectEnti').data('data'));
							    			else
							    				$('#pageWrapperLeft .ui-state-highlight').click();
							    			K.closeWindow('windowDetailEnti'+$('#windowSelectEnti').data('data')._id.$id);
							    			K.closeWindow('windowSelectEnti');
							    		}
					    			}
					    		});
					    		$('#btnNotiSelect').click(function(){
					    			K.clearNoti();
					    			if(p.callback!=null)
				    					p.callback($('#windowSelectEnti').data('data'));
					    			else
					    				$('#pageWrapperLeft .ui-state-highlight').click();
					    			K.closeWindow('windowDetailEnti'+$('#windowSelectEnti').data('data')._id.$id);
					    			K.closeWindow('windowSelectEnti');
					    		}).button({icons: {primary: 'ui-icon-person'}});
				    		}
				    	},'json');
				    }, 2000);
				});
				//on keydown, clear the countdown 
				p.$w.find('[name=dni]').keydown(function(){
				    clearTimeout(typingTimer);
				});
				p.$w.find('[name=search]').focus();
			}
		});
	},
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	windowSelect: function(p){
		if(p==null) p = {};
		if(p.bootstrap!=null){
			new K.Modal({
				id: 'windowDocu',
				title: 'Buscar Documento',
				width: 650,
				height: 90,
				content: '<form class="form-horizontal" role="form">'+
					'<div class="form-group">'+
						'<label class="col-sm-4 control-label">Num. Documento</label>'+
						'<div class="col-sm-8 input-group">'+
							'<span class="input-group-addon"><i class="fa fa-credit-card fa-fw"></i></span>'+
							'<input type="text" class="form-control" name="doc">'+
						'</div>'+
					'</div>'+
				'</form>',
				buttons: {
					'Buscar': {
						icon: 'fa-search',
						type: 'success',
						f: function(){
							var valor = p.$w.find('[name=doc]').val();
							if(valor.length==8||valor.length==11){
								K.block();
								$.post('mg/enti/check',{doc:valor},function(data){
									K.unblock();
									if(data.check==true){
										p.callback(data.enti);
										K.closeWindow(p.$w.attr('id'));
									}else{
										K.msg({title: ciHelper.titles.infoReq,text: 'La entidad no existe en la RENIEC ni en la SUNAT',type: 'error'});
										mgEnti.windowSelectOld(p);
										K.closeWindow(p.$w.attr('id'));
									}
								},'json');
							}else{
								K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar 8 u 11 caracteres!',type: 'error'});
							}
						}
					},
					'Continuar sin documento': {
						icon: 'fa-refresh',
						type: 'info',
						f: function(){
							K.closeWindow(p.$w.attr('id'));
							mgEnti.windowSelectOld(p);
						}
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowDocu');
					p.$w.find('[name=doc]').keyup(function(e){
						if(e.keyCode == 13){
							p.$w.find('.modal-footer button:first').click();
						}
					});
				}
			});
		}else{
			new K.Modal({
				id: 'windowDocu',
				title: 'Buscar Documento',
				width: 650,
				height: 90,
				content: '<label class="col-sm-4 control-label">Num. Documento</label>'+
					'<input type="text" class="form-control" name="doc">',
				buttons: {
					'Buscar': function(){
						var valor = p.$w.find('[name=doc]').val();
						if(valor.length==8||valor.length==11){
							K.block();
							$.post('mg/enti/check',{doc:valor},function(data){
								K.unblock();
								if(data.check==true){
									if(data.enti.domicilios!=null){
										p.callback(data.enti);
									}else{
										ciEdit.windowEditEntidad({
											callBack: function(data){
												$.post('mg/enti/save',data,function(rpta){
													K.notification({title: ciHelper.titles.regiAct,text: 'Entidad actualizada!'});
													K.closeWindow('windowEntiEdit'+rpta._id.$id);
													p.callback(rpta);
												},'json');
											},
											id: data.enti._id.$id,
											nomb: data.enti.nomb,
											tipo_enti: data.enti.tipo_enti,
											data: data.enti
										});
									}
									K.closeWindow(p.$w.attr('id'));
								}else{
									K.notification({title: ciHelper.titles.infoReq,text: 'La entidad no existe en la RENIEC ni en la SUNAT',type: 'error'});
									mgEnti.windowSelectOld(p);
									K.closeWindow(p.$w.attr('id'));
								}
							},'json');
						}else{
							K.notification({title: ciHelper.titles.infoReq,text: 'Debe ingresar 8 u 11 caracteres!',type: 'error'});
						}
					},
					'Continuar sin documento': function(){
						K.closeWindow(p.$w.attr('id'));
						mgEnti.windowSelectOld(p);
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowDocu');
				}
			});
		}
	},
	windowSelectOld: function(p){
		if(p.bootstrap!=null){
			new K.Modal({
				id: 'windowSelectEnti',
				title: 'Seleccionar Entidad',
				contentURL: 'mg/enti/select_bootstrap',
				allScreen: true,
				width: 950,
				height: 500,
				buttons: {
					'Seleccionar y Actualizar': {
						icon: 'fa-check',
						type: 'info',
						f: function(){
							var ent = p.$w.find('[name=nomb]').data('data');
							if(ent!=null){
								var data = {
									_id: ent._id.$id,
									nomb: p.$w.find('[name=nomb]').val(),
									tipo_enti: ent.tipo_enti,
									telefonos: [{num: p.$w.find('[name=telf]').val()}],
									//domicilios: [{direccion: p.$w.find('[name=direc]').val()}]
								};
								if(data.nomb==''){
									p.$w.find('[name=nomb]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
								}
								if(data.tipo_enti=='P'){
									data.appat = p.$w.find('[name=appat]').val();
									/*if(data.appat==''){
										p.$w.find('[name=appat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'});
									}*/
									data.apmat = p.$w.find('[name=apmat]').val();
									/*if(data.apmat==''){
										p.$w.find('[name=apmat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'});
									}*/
									data.fecnac = p.$w.find('[name=fecnac]').val();
									data.dni = p.$w.find('[name=dni]').val();
									/*if(data.dni==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'});
									}*/
								}else{
									data.ruc = p.$w.find('[name=dni]').val();
									/*if(data.ruc==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
									}*/
								}
								$.post('mg/enti/save',data,function(ent){
									p.callback(ent);
									K.closeWindow(p.$w.attr('id'));
								},'json');
							}else{
								var data = {
									nomb: p.$w.find('[name=nomb]').val(),
									tipo_enti: p.$w.find('[name=tipo] option:selected').val(),
									telefonos: [{num: p.$w.find('[name=telf]').val()}],
									//domicilios: [{direccion: p.$w.find('[name=direc]').val()}]
								};
								if(data.nomb==''){
									p.$w.find('[name=nomb]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
								}
								if(data.tipo_enti=='P'){
									data.appat = p.$w.find('[name=appat]').val();
									/*if(data.appat==''){
										p.$w.find('[name=appat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'});
									}*/
									data.apmat = p.$w.find('[name=apmat]').val();
									/*if(data.apmat==''){
										p.$w.find('[name=apmat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'});
									}*/
									data.fecnac = p.$w.find('[name=fecnac]').val();
									data.dni = p.$w.find('[name=dni]').val();
									/*if(data.dni==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'});
									}*/
								}else{
									data.ruc = p.$w.find('[name=dni]').val();
									/*if(data.ruc==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
									}*/
								}
								if(p.rol!=null){
									data.rol = p.rol;
								}
								$.post('mg/enti/save',data,function(ent){
									p.callback(ent);
									K.closeWindow(p.$w.attr('id'));
								},'json');
							}
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(pa){
							K.closeWindow(p.$w.attr('id'));
						}
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelectEnti');
					p.enti = [];
					p.$w.find('[name=direc]').closest('.form-group').remove();
					/*p.$w.find('[name=dni]').numeric();
					p.$w.find('[name=telf]').numeric();*/
					p.$w.find('[name=btnFec]').click(function(){
						if(p.$w.find('[name=tipo] option:selected').val()=='P')
							ciHelper.datepicker({date: p.$w.find('[name=fecnac]').val(),callback:function(data){
								p.$w.find('[name=fecnac]').val(data);
							}});
					});
					p.$w.find('[name=tipo]').change(function(){
						if($(this).find('option:selected').val()=='P'){
				    		p.$w.find('[name=appat]').removeAttr('disabled');
				    		p.$w.find('[name=apmat]').removeAttr('disabled');
				    		p.$w.find('[name=fecnac]').removeAttr('disabled');
						}else{
				    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
				    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
				    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
				    	}
					});
					var typingTimer;
					p.$w.find('[name=dni]').keyup(function(){
						$this = $(this);
						clearTimeout(typingTimer);
					    typingTimer = setTimeout(function(){
					    	//user is "finished typing," do something
					    	valor = $this.val();
					    	if(valor.length==8||valor.length==11){
						    	$.post('mg/enti/check','doc='+$this.val(),function(data){
						    		$('#windowSelectEnti').data('data',data.enti);
						    		if(data.enti!=null){
						    			K.clearNoti();
						    			K.notification({text: 'El Documento de Identidad ya est&aacute; relacionado a una entidad registrada!<br /><button id="btnNotiSelect">Seleccionar</button>',hide: false});
						    			/*ciDetails.windowDetailsEnti({
											id: data.enti._id.$id,
											tipo_enti: data.enti.tipo_enti,
											nomb: ciHelper.enti.formatName(data.enti),
											modal: true,
											buttons: {
							    				"Seleccionar": function(){
								    				K.clearNoti();
									    			if(p.callback!=null)
								    					p.callback($('#windowSelectEnti').data('data'));
									    			else
									    				$('#pageWrapperLeft .ui-state-highlight').click();
									    			K.closeWindow('windowDetailEnti'+$('#windowSelectEnti').data('data')._id.$id);
									    			K.closeWindow('windowSelectEnti');
									    		}
							    			}
							    		});
							    		$('#btnNotiSelect').click(function(){
							    			K.clearNoti();
							    			if(p.callback!=null)
						    					p.callback($('#windowSelectEnti').data('data'));
							    			else
							    				$('#pageWrapperLeft .ui-state-highlight').click();
							    			K.closeWindow('windowDetailEnti'+$('#windowSelectEnti').data('data')._id.$id);
							    			K.closeWindow('windowSelectEnti');
							    		}).button({icons: {primary: 'ui-icon-person'}});*/
						    		}
						    	},'json');
							}
					    }, 2000);
					});
					//on keydown, clear the countdown 
					p.$w.find('[name=dni]').keydown(function(){
					    clearTimeout(typingTimer);
					});
					var params = {};
					if(p.rol)
						params.rol = p.rol;
					if(p.filter!=null)
						params.filter = p.filter;
					var $grid = new K.grid({
						$el: p.$w.find('.ui-layout-center'),
						cols: ['','Nombre','Documento de Identidad'],
						data: 'mg/enti/search',
						params: params,
						itemdescr: 'entidad(es)',
						toolbarHTML: '',
						onContentLoaded: function($el){
							$el.find('.search input').css('width','100%');
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+mgEnti.formatName(data)+'</td>');
							$row.append('<td>'+mgEnti.formatIden(data)+'</td>');
							$row.data('data',data).click(function(){
								var ent = $(this).data('data');
								p.$w.find('[name=nomb]').data('data',ent);
								if(ent.tipo_enti=='P'){
						    		p.$w.find('[name=tipo]').selectVal('P').attr('disabled','disabled');
						    		p.$w.find('[name=nomb]').val(ent.nomb);
						    		p.$w.find('[name=appat]').val(ent.appat).removeAttr('disabled');
						    		p.$w.find('[name=apmat]').val(ent.apmat).removeAttr('disabled');
						    		p.$w.find('[name=dni]').val(mgEnti.formatDNI(ent));
						    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
						    		if(ent.domicilios!=null){
						    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
						    		}
						    		if(ent.fecnac!=null)
						    			p.$w.find('[name=fecnac]').val(ent.fecnac).removeAttr('disabled');
						    	}else{
						    		p.$w.find('[name=tipo]').selectVal('E').attr('disabled','disabled');
						    		p.$w.find('[name=nomb]').val(ent.nomb);
						    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
						    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
						    		p.$w.find('[name=dni]').val(mgEnti.formatRUC(ent));
						    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
						    		if(ent.domicilios!=null)
						    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
						    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
						    	}
							}).dblclick(function(){
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
			new K.Modal({
				id: 'windowSelectEnti',
				title: 'Seleccionar Entidad',
				contentURL: 'mg/enti/select',
				store: false,
				icon: 'ui-icon-person',
				width: 560,
				height: 185,
				buttons: {
					'Seleccionar y Actualizar': function(){
							var ent = p.$w.find('[name=nomb]').data('data');
							if(ent!=null){
								var data = {
									_id: ent._id.$id,
									nomb: p.$w.find('[name=nomb]').val(),
									tipo_enti: ent.tipo_enti,
									telefonos: [{num: p.$w.find('[name=telf]').val()}],
									domicilios: [{direccion: p.$w.find('[name=direc]').val()}]
								};
								if(data.nomb==''){
									p.$w.find('[name=nomb]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
								}
								if(data.tipo_enti=='P'){
									data.appat = p.$w.find('[name=appat]').val();
									if(data.appat==''){
										p.$w.find('[name=appat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'});
									}
									data.apmat = p.$w.find('[name=apmat]').val();
									if(data.apmat==''){
										p.$w.find('[name=apmat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'});
									}
									data.fecnac = p.$w.find('[name=fecnac]').val();
									data.dni = p.$w.find('[name=dni]').val();
									if(data.dni==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'});
									}
								}else{
									data.ruc = p.$w.find('[name=dni]').val();
									if(data.ruc==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
									}
								}
								$.post('mg/enti/save',data,function(ent){
									p.callback(ent);
									K.closeWindow(p.$w.attr('id'));
								},'json');
							}else{
								var data = {
									nomb: p.$w.find('[name=nomb]').val(),
									tipo_enti: p.$w.find('[name=tipo] option:selected').val(),
									telefonos: [{num: p.$w.find('[name=telf]').val()}],
									domicilios: [{direccion: p.$w.find('[name=direc]').val()}]
								};
								if(data.nomb==''){
									p.$w.find('[name=nomb]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
								}
								if(data.tipo_enti=='P'){
									data.appat = p.$w.find('[name=appat]').val();
									if(data.appat==''){
										p.$w.find('[name=appat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido paterno!',type: 'error'});
									}
									data.apmat = p.$w.find('[name=apmat]').val();
									if(data.apmat==''){
										p.$w.find('[name=apmat]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un apellido materno!',type: 'error'});
									}
									data.fecnac = p.$w.find('[name=fecnac]').val();
									data.dni = p.$w.find('[name=dni]').val();
									if(data.dni==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un DNI!',type: 'error'});
									}
								}else{
									data.ruc = p.$w.find('[name=dni]').val();
									if(data.ruc==''){
										p.$w.find('[name=dni]').focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
									}
								}
								if(p.rol!=null){
									data.rol = p.rol;
								}
								$.post('mg/enti/save',data,function(ent){
									p.callback(ent);
									K.closeWindow(p.$w.attr('id'));
								},'json');
							}
					},
					"Cancelar": function(pa){
							K.closeWindow(p.$w.attr('id'));
						}
				},
				//onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelectEnti');
					$(window).resize(function(){
						var $this = $('#windowSelectEnti');
						$this.dialog( "option", "height", $(window).height() )
							.dialog( "option", "width", $(window).width() )
							.dialog( "option", "position", [ 0 , 0 ] )
							.dialog( "option", "draggable", false )
							.dialog( "option", "resizable", false );
						$this.height(($this.height()-0)+'px');
					}).resize();
					p.$mainPanel = p.$w.find('.ui-layout-center');
					p.$rightPanel = p.$w.find('.ui-layout-east');
					p.$w.layout({
						resizeWithWindow:	false,
						east__size:			300
					});
					p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
					p.$rightPanel.css('z-index',$.ui.dialog.maxZ);
					p.enti = [];
					p.$w.find('[name=dni]').numeric();
					p.$w.find('[name=telf]').numeric();
					p.$w.find('[name=btnFec]').click(function(){
						if(p.$w.find('[name=tipo] option:selected').val()=='P')
							ciHelper.datepicker({date: p.$w.find('[name=fecnac]').val(),callback:function(data){
								p.$w.find('[name=fecnac]').val(data);
							}});
					});
					p.$w.find('[name=tipo]').change(function(){
						if($(this).find('option:selected').val()=='P'){
				    		p.$w.find('[name=appat]').removeAttr('disabled');
				    		p.$w.find('[name=apmat]').removeAttr('disabled');
				    		p.$w.find('[name=fecnac]').removeAttr('disabled');
						}else{
				    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
				    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
				    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
				    	}
					});
					var typingTimer;
					p.$w.find('[name=dni]').keyup(function(){
						$this = $(this);
						clearTimeout(typingTimer);
					    typingTimer = setTimeout(function(){
					    	//user is "finished typing," do something
					    	$.post('mg/enti/check','doc='+$this.val(),function(data){
					    		$('#windowSelectEnti').data('data',data.enti);
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
								    			if(p.callback!=null)
							    					p.callback($('#windowSelectEnti').data('data'));
								    			else
								    				$('#pageWrapperLeft .ui-state-highlight').click();
								    			K.closeWindow('windowDetailEnti'+$('#windowSelectEnti').data('data')._id.$id);
								    			K.closeWindow('windowSelectEnti');
								    		}
						    			}
						    		});
						    		$('#btnNotiSelect').click(function(){
						    			K.clearNoti();
						    			if(p.callback!=null)
					    					p.callback($('#windowSelectEnti').data('data'));
						    			else
						    				$('#pageWrapperLeft .ui-state-highlight').click();
						    			K.closeWindow('windowDetailEnti'+$('#windowSelectEnti').data('data')._id.$id);
						    			K.closeWindow('windowSelectEnti');
						    		}).button({icons: {primary: 'ui-icon-person'}});
					    		}
					    	},'json');
					    }, 2000);
					});
					//on keydown, clear the countdown 
					p.$w.find('[name=dni]').keydown(function(){
					    clearTimeout(typingTimer);
					});
					var params = {};
					if(p.rol)
						params.rol = p.rol;
					if(p.filter!=null)
						params.filter = p.filter;
					var $grid = new K.grid({
						$el: p.$mainPanel,
						cols: ['','Nombre','Documento de Identidad'],
						width: [40,400,300],
						data: 'mg/enti/search',
						params: params,
						itemdescr: 'entidad(es)',
						toolbarHTML: '',
						onContentLoaded: function($el){
							p.$w.find('.search input').css('width','100%');
							if(p.entidad!=null){
								p.$w.find('.search input').val(p.entidad.fullname);
								p.$w.find('.search button:eq(0)').click();
								var ent = p.entidad;
								p.$w.find('[name=nomb]').data('data',ent);
								if(ent.tipo_enti=='P'){
						    		p.$w.find('[name=tipo]').selectVal('P').attr('disabled','disabled');
						    		p.$w.find('[name=nomb]').val(ent.nomb);
						    		p.$w.find('[name=appat]').val(ent.appat).removeAttr('disabled');
						    		p.$w.find('[name=apmat]').val(ent.apmat).removeAttr('disabled');
						    		p.$w.find('[name=dni]').val(mgEnti.formatDNI(ent));
						    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
						    		if(ent.domicilios!=null)
						    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
						    		if(ent.fecnac!=null)
						    			p.$w.find('[name=fecnac]').val(ent.fecnac).removeAttr('disabled');
						    	}else{
						    		p.$w.find('[name=tipo]').selectVal('E').attr('disabled','disabled');
						    		p.$w.find('[name=nomb]').val(ent.nomb);
						    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
						    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
						    		p.$w.find('[name=dni]').val(mgEnti.formatRUC(ent));
						    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
						    		if(ent.domicilios!=null)
						    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
						    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
						    	}
							}
						},
						fill: function(data,$row){
							$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+mgEnti.formatName(data)+'</td>');
							$row.append('<td>'+mgEnti.formatIden(data)+'</td>');
							$row.data('data',data).click(function(){
								var ent = $(this).data('data');
								p.$w.find('[name=nomb]').data('data',ent);
								if(ent.tipo_enti=='P'){
						    		p.$w.find('[name=tipo]').selectVal('P').attr('disabled','disabled');
						    		p.$w.find('[name=nomb]').val(ent.nomb);
						    		p.$w.find('[name=appat]').val(ent.appat).removeAttr('disabled');
						    		p.$w.find('[name=apmat]').val(ent.apmat).removeAttr('disabled');
						    		p.$w.find('[name=dni]').val(mgEnti.formatDNI(ent));
						    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
						    		if(ent.domicilios!=null)
						    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
						    		if(ent.fecnac!=null)
						    			p.$w.find('[name=fecnac]').val(ent.fecnac).removeAttr('disabled');
						    	}else{
						    		p.$w.find('[name=tipo]').selectVal('E').attr('disabled','disabled');
						    		p.$w.find('[name=nomb]').val(ent.nomb);
						    		p.$w.find('[name=appat]').val('').attr('disabled','disabled');
						    		p.$w.find('[name=apmat]').val('').attr('disabled','disabled');
						    		p.$w.find('[name=dni]').val(mgEnti.formatRUC(ent));
						    		p.$w.find('[name=telf]').val(mgEnti.formatTelf(ent));
						    		if(ent.domicilios!=null)
						    			p.$w.find('[name=direc]').val(ent.domicilios[0].direccion);
						    		p.$w.find('[name=fecnac]').val('').attr('disabled','disabled');
						    	}
							}).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',data).contextMenu('conMenListSel', {
								onShowMenu: function(e, menu) {
									$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
									$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
									$(e.target).closest('.item').click();
									K.tmp = $(e.target).closest('.item');
									return menu;
								},
								bindings: {
									'conMenListSel_sel': function(t){
										p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
									}
								}
							});
							return $row;
						}
					});
				}
			});
		}
	}
};
define(
	function(){
		return mgEnti;
	}
);