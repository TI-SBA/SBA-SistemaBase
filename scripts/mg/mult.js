/*Gestor Multimedia */
mgMult = {
	dbRel: function(mult){
		return {
			_id: mult._id.$id,
			filename: mult.filename,
			nomb: mult.metadata.nomb,
			ext: mult.metadata.ext
		};
	},
	home: '/',
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'mg',
			action: 'mgMult',
			titleBar: { title: 'Gestor Multimedia: /'}
		});		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'mg/mult',
			store: false,
			onContentLoaded: function(){
				$mainPanel = $('#mainPanel');
				p.$grid = new K.grid({
					$el: $('#mainPanel [name=grid]'),
					cols: [
						'',
						'',
					    'Nombre',
					    'Tama&ntilde;o',
					    'Fecha de Modificaci&oacute;n'
					],
					data: 'mg/mult/lista',
					pagination:false,
					search:false,
					itemdescr: 'producto(s)',
					//toolbarURL: '',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Archivo</button>&nbsp;'+
						'<button name="btnCarpeta" class="btn btn-info"><i class="fa fa-folder-o"></i> Nueva Carpeta</button>&nbsp;'+
						'<button name="btnRetroceder" class="btn btn-warning"><i class="fa fa-undo"></i> Regresar</button>&nbsp;',
					onContentLoaded: function($el){	
						var arr = [];
						$el.find('[name=btnCarpeta]').data('last',arr);
						$el.find('[name=btnAgregar]').click(function(){
							mgMult.windowNewFile({$grid:p.$grid});
						});
						$el.find('[name=btnCarpeta]').click(function(){
							mgMult.windowNewFolder({$grid:p.$grid});
						});
						$el.find('[name=btnRetroceder]').click(function(){
							mgMult.reinit({retro: true,$grid:p.$grid,title: function(title){
								K.TitleBar({title: title});
							}});
						}).attr('disabled','disabled');
					},
					onLoading: function(){ 
						K.block();
					},
					onComplete: function(){ 
						K.unblock();
					},
					params: {},
					fill: function(data,$row){
						if(data.tipo=='F'){
							$row.append('<td>');
							$row.append('<td><span class="icon"><i class="fa fa-folder"></i></span></td>');
							$row.append('<td colspan="4">'+data.item+'</td>');
							$row.data('data',data.item).data('tipo',data.tipo).dblclick(function(){
								mgMult.reinit({
									descr: $(this).data('data'),
									$grid: p.$grid,
									next:true,
									title: function(title){
										K.TitleBar({title: title});
									}
								});
							}).contextMenu('conMenMgMult', {
								onShowMenu: function($r, menu) {
									var result = $r.data('data'),
									excep = '#conMenMgMult_edi,#conMenMgMult_des';
									var ruta = mgMult.home,
									arr = $mainPanel.find('[name=btnCarpeta]').data('last');
									for(var i=0; i<arr.length; i++){
										if(arr[i].id!=null)
											ruta += arr[i].id+'/';
									}
									if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
										ruta += $mainPanel.find('[name=btnCarpeta]').data('id')+'/';
									ruta += $r.data('data');
									if(ruta=='/Cementerio'||ruta=='/Inmuebles'||ruta=='/tmp'||ruta=='/Inmuebles/Plantillas'||ruta=='/Cementerio/Registro_Historico'||ruta=='/Asesoria_Legal/Convenios'){
										excep += ',#conMenMgMult_eli';
									}
									$(excep,menu).remove();
								},
								bindings: {
									'conMenMgMult_abr': function(t) {
										K.tmp.dblclick();
									},
									'conMenMgMult_edi': function(t) {
										mgMult.windowEditFolder({id:K.tmp.data('data')._id.$id,$grid: p.$grid});
									},
									'conMenMgMult_eli': function(t) {
										K.sendingInfo();
										var ruta = mgMult.home,
										arr = $mainPanel.find('[name=btnCarpeta]').data('last');
										for(var i=0; i<arr.length; i++){
											if(arr[i].id!=null)
												ruta += arr[i].id+'/';
										}
										if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
											ruta += $mainPanel.find('[name=btnCarpeta]').data('id')+'/';
										ruta += K.tmp.data('data');
										$.post('mg/mult/delete_folder',{dir: ruta},function(){
											K.clearNoti();
											K.notification({title: ciHelper.titleMessages.regiGua,text: 'La carpeta ha sido eliminada con &eacute;xito!'});
											mgMult.reinit({$grid:p.$grid,title: function(title){
												K.TitleBar({title: title});
											}});
										});
									}
								}
							});
						}else{
							$row.append('<td>');
							$row.append('<td>');
							data.ext = data.item.split('.').pop();
							data.ext = data.ext.toLowerCase();
							if(data.ext=='xlsx'||data.ext=='xls'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-file-excel-o"></i></span>');
							}else if(data.ext=='jpg'||data.ext=='gif'||data.ext=='bmp'||data.ext=='png'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-picture-o"></i></span>');
							}else if(data.ext=='mp4'||data.ext=='wmv'||data.ext=='avi'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-film"></i></span>');
							}else if(data.ext=='pdf'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-file-pdf-o"></i></span>');
							}else if(data.ext=='doc'||data.ext=='docx'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-file-word-o"></i></span>');
							}else{
								$row.find('td:last').append('<span class="icon"><i class="fa fa-question"></i></span>');
							}
							$row.append( '<td>'+data.item+'</td>' );
							//$row.append('<td>'+result.metadata.mime+'</td>' );
							$row.append('<td>'+data.tamano+'</td>' );
							$row.append('<td>'+data.fecha+'</td>' );
							$row.data('data',data.item).data('tipo',data.tipo).data('ext',data.ext).dblclick(function(){
								var ruta = mgMult.home,
								arr = $mainPanel.find('[name=btnCarpeta]').data('last');
								for(var i=0; i<arr.length; i++){
									if(arr[i].id!=null)
										ruta += arr[i].id+'/';
								}
								if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
									ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
								ruta += '/'+$(this).data('data');
								window.open(K.path_file+ruta);
							}).contextMenu('conMenMgMult', {
								onShowMenu: function($r, menu) {
									var result = $r.data('data'),
									excep = '#conMenMgMult_abr,#conMenMgMult_edi';
									var ruta = mgMult.home,
									arr = $mainPanel.find('[name=btnCarpeta]').data('last');
									for(var i=0; i<arr.length; i++){
										if(arr[i].id!=null)
											ruta += arr[i].id+'/';
									}
									if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
										ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
									if(ruta=='/Inmuebles/Plantillas'||ruta=='/Cementerio/Registro_Historico'||ruta=='/Asesoria_Legal/Convenios'){
										excep += ',#conMenMgMult_eli';
									}
									$(excep,menu).remove();
								},
								bindings: {
									'conMenMgMult_edi': function(t) {
										mgMult.windowEdit({id:K.tmp.data('data')._id.$id,$grid: p.$grid});
									},
									'conMenMgMult_eli': function(t) {
										K.sendingInfo();
										var ruta = mgMult.home,
										arr = $mainPanel.find('[name=btnCarpeta]').data('last');
										for(var i=0; i<arr.length; i++){
											if(arr[i].id!=null)
												ruta += arr[i].id+'/';
										}
										if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
											ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
										ruta += '/'+K.tmp.data('data');
										$.post('mg/mult/delete_folder',{dir: ruta},function(){
											K.clearNoti();
											K.notification({title: ciHelper.titleMessages.regiGua,text: 'La carpeta ha sido eliminada con &eacute;xito!'});
											mgMult.reinit({$grid:p.$grid,title: function(title){
												K.TitleBar({title: title});
											}});
										});
									},
									'conMenMgMult_des': function(t) {
										var ruta = mgMult.home,
										arr = $mainPanel.find('[name=btnCarpeta]').data('last');
										for(var i=0; i<arr.length; i++){
											if(arr[i].id!=null)
												ruta += arr[i].id+'/';
										}
										if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
											ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
										ruta += '/'+K.tmp.data('data');
										window.open(K.path_file+ruta);
									}
								}
							});
						}
						$row.click(function(){
							var ruta = mgMult.home,
							arr = $mainPanel.find('[name=btnCarpeta]').data('last');
							for(var i=0; i<arr.length; i++){
								if(arr[i].id!=null)
									ruta += arr[i].id+'/';
							}
							if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
								ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
							ruta += '/'+$(this).data('data');
							mgMult.preview({
								ext: $(this).data('ext'),
								ruta: K.path_file+ruta
							},$mainPanel);
						});
						return $row;
					}
				});
			}
		});
	},
	reinit: function(p){
		if(p!=null){
			if(p.title==null) p.title = function(title){
				$.noop();
			};
		}
		if(p==null){
			p = {};
		}else if(p.retro!=null){
			var tmp = p.$grid.$el.find('[name=btnCarpeta]').data('last');
			if(tmp.length==1){
				p.$grid.$el.find('[name=btnCarpeta]').removeData('id');
				p.title('Gestor Multimedia: /');
			}else if(tmp[tmp.length-1]!=null){
				if(tmp[tmp.length-1].id!=null){
					p.$grid.$el.find('[name=btnCarpeta]').data('id',tmp[tmp.length-1].id);
					K.TitleBar({title: tmp[tmp.length-1].title});
				}
			}else{
				p.$grid.$el.find('[name=btnCarpeta]').removeData('id');
				p.title('Gestor Multimedia: /');
				p.$grid.$el.find('[name=btnRetroceder]').attr('disabled','disabled');
			}
			var arr = tmp;
			arr.splice(tmp.length-1,1);
			p.$grid.$el.find('[name=btnCarpeta]').data('last',arr);
		}else if(p.next){
			var tmp = p.$grid.$el.find('[name=btnCarpeta]').data('id');
			var arr = p.$grid.$el.find('[name=btnCarpeta]').data('last');
			arr.push({
				id: tmp,
				title: $('[name=titleBar] b:first').html()
			});
			p.$grid.$el.find('[name=btnCarpeta]').data('last',arr);
			p.title($('[name=titleBar] b:first').html()+p.descr+'/');
			p.$grid.$el.find('[name=btnCarpeta]').data('id',p.descr);
			p.$grid.$el.find('[name=btnRetroceder]').removeAttr('disabled');
		}
		$("#mainPanel .gridBody").empty();
		var ruta = mgMult.home,
		arr = p.$grid.$el.find('[name=btnCarpeta]').data('last');
		for(var i=0; i<arr.length; i++){
			if(arr[i].id!=null)
				ruta += arr[i].id+'/';
		}
		if(p.$grid.$el.find('[name=btnCarpeta]').data('id')!=null)
			ruta += p.$grid.$el.find('[name=btnCarpeta]').data('id');
		else
			p.$grid.$el.find('[name=btnRetroceder]').attr('disabled','disabled');
		p.$grid.reinit({params:{ruta: ruta,dir: p.$grid.$el.find('[name=btnCarpeta]').data('id')}});
	},
	preview: function(file,$w){
		
		
		
		
	
	
	
		$w.find('[name=preview]').empty();
		if(file.ext==null){
			
		}else{
			if(file.ext=='jpg'||file.ext=='gif'||file.ext=='bmp'||file.ext=='png'){
				$w.find('[name=preview]').append('<img src="'+file.ruta+'" class="img-thumbnail">');
			}else if(file.ext=='pdf'){
				$w.find('[name=preview]').append('<object width="100%" height="500px" type="application/pdf" data="'+file.ruta+'">'
				);
			}
		}
		
		
		
		
		
		
		
		
		
		
		
	},
	windowNewFolder: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNewFolder',
			title: 'Crear Nueva Carpeta',
			contentURL: 'mg/mult/new_folder',
			width: 410,
			height: 100,
			buttons: {
				"Guardar": {
					type: 'success',
					icon: 'fa-save',
					f: function(){
						var $mainPanel = $('#mainPanel');
						var data = {};
						var ruta = mgMult.home,
						arr = $mainPanel.find('[name=btnCarpeta]').data('last');
						for(var i=0; i<arr.length; i++){
							if(arr[i].id!=null)
								ruta += arr[i].id+'/';
						}
						if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
							ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
						data.dir = ruta;
						data.descr = p.$w.find('[name=descr]').val();
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de carpeta!',type: 'error'});
						}
						K.sendingInfo();
						K.block();
						$.post('mg/mult/save_folder',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'La carpeta ha sido creada con &eacute;xito!'});
							K.closeWindow(p.$w.attr('id'));
							if(p.cb==null) mgMult.reinit({$grid:p.$grid});
							else p.cb({$grid:p.$grid});
						},'json');
					}
				},
				"Cancelar": {
					type: 'danger',
					icon: 'fa-ban',
					f: function(pa){
						pa.close();
					}
				}
			},
			close: function(){
				K.closeWindow(p.$w.attr('id'));
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewFolder');
				p.$w.find('[name=descr]').focus();
			}
		});
	},
	windowNewFile: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNew',
			title: 'Crear Nuevo Archivo',
			contentURL: 'mg/mult/new_file',
			allScreen: true,
			buttons: {
				"Cancelar": {
					type: 'danger',
					icon: 'fa-ban',
					f: function(pa){
						pa.close();
					}
				}
			},
			close: function(){
				K.closeWindow(p.$w.attr('id'));
			},
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				$("#file_upload").fileinput({
					language: "es",
					uploadUrl: "ci/upload/tmp",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					/*uploadExtraData: function() {
						return {
							operacion: 'IN_PLAYAS'
						};
					},
					allowedFileExtensions: ["xls","xlsx"]*/
				});
				$('#file_upload').on('fileuploaded', function(event, params) {
					K.clearNoti();
					K.sendingInfo();
					var data = {file: params.files[0].name},
					ruta = mgMult.home,
					arr = $mainPanel.find('[name=btnCarpeta]').data('last');
					for(var i=0; i<arr.length; i++){
						if(arr[i].id!=null)
							ruta += arr[i].id+'/';
					}
					if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
						ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
					data.dir = ruta;
					$.post('mg/mult/upload',data,function(){
						K.clearNoti();
						K.notification({
							title: ciHelper.titleMessages.regiGua,
							text: 'Archivo subido con &eacute;xito!'
						});
						K.closeWindow(p.$w.attr('id'));
						mgMult.reinit({$grid:p.$grid});
					});
				});
			}
		});
	},
	windowNewModule: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNew',
			title: 'Crear Nuevo Archivo',
			contentURL: 'mg/mult/new_file',
			allScreen: true,
			buttons: {
				"Cancelar": {
					type: 'danger',
					icon: 'fa-ban',
					f: function(pa){
						pa.close();
					}
				}
			},
			close: function(){
				K.closeWindow(p.$w.attr('id'));
			},
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				if(p.multiple!=null){
					p.sec = 0;
					p.$w.find('#file_upload').attr('multiple','');
				}
				$("#file_upload").fileinput({
					language: "es",
					uploadUrl: "ci/upload/tmp",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					/*uploadExtraData: function() {
						return {
							operacion: 'IN_PLAYAS'
						};
					},
					allowedFileExtensions: ["xls","xlsx"]*/
				});
				$('#file_upload').on('fileuploaded', function(event, params) {
					if(p.pdf!=null){
						p.sec++;
						if(p.sec==params.files.length){
							K.clearNoti();
							K.sendingInfo();
							var data = {
								files: [],
								dir: p.ruta,
								nomb: p.nomb
							};
							for(var i=0; i<params.files.length; i++){
								data.files.push(params.files[i].name);
							}
							$.post('mg/mult/upload_module_pdf',data,function(){
								K.clearNoti();
								K.notification({
									title: ciHelper.titleMessages.regiGua,
									text: 'Archivo subido con &eacute;xito!'
								});
								K.closeWindow(p.$w.attr('id'));
								p.callback(data);
							});
						}
					}else{
						K.clearNoti();
						K.sendingInfo();
						var data = {
							file: params.files[0].name,
							dir: p.ruta,
							nomb: p.nomb
						};
						$.post('mg/mult/upload_module',data,function(){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: 'Archivo subido con &eacute;xito!'
							});
							K.closeWindow(p.$w.attr('id'));
							p.callback(data);
						});
					}
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
			title: 'Subir Archivo',
			buttons: {
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
				p.$w = $('#windowSubirArchivo');
				var params = {
					language: "es",
					uploadUrl: "mg/mult/subir",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>"
				};
				if(p.ruta!=''){
					if(params.uploadExtraData==null){
						params.uploadExtraData = {};
					}
					params.uploadExtraData.folder = p.ruta;
				}
				if(p.nomb!=''){
					if(params.uploadExtraData==null){
						params.uploadExtraData = {};
					}
					params.uploadExtraData.nomb = p.nomb;
				}
				if(p.ext!=''){
					params.allowedFileExtensions = p.ext;
				}
				p.$w.find("#file_upload").fileinput(params);
				p.$w.find('#file_upload').on('fileuploaded', function(event,params){
					K.clearNoti();
					K.unblock();
					K.sendingInfo();
					p.callback({file: params.files[0].name});
					K.closeWindow(p.$w.attr('id'));
				});
			}
		});
	},
	windowSelect: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowSelect',
			title: 'Seleccionar Archivo',
			contentURL: 'mg/mult',
			store: false,
			allScreen: true,
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							if(p.$w.find('.highlights').data('tipo')=='F'){
								K.clearNoti();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un archivo!',
									type: 'error'
								});
							}
							K.tmp = p.$w.find('.highlights');
							var ruta = '',
							arr = p.$w.find('[name=btnCarpeta]').data('last');
							for(var i=0; i<arr.length; i++){
								if(arr[i].id!=null)
									ruta += arr[i].id+'/';
							}
							if(p.$w.find('[name=btnCarpeta]').data('id')!=null)
								ruta += p.$w.find('[name=btnCarpeta]').data('id')+'/';
							ruta += K.tmp.data('data');
							p.callback(ruta);
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
					type: 'danger',
					icon: 'fa-ban',
					f: function(pa){
						pa.close();
					}
				}
			},
			close: function(){
				K.closeWindow(p.$w.attr('id'));
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					cols: [
						'',
						'',
					    'Nombre',
					    'Tama&ntilde;o',
					    'Fecha de Modificaci&oacute;n'
					],
					data: 'mg/mult/lista',
					pagination:false,
					search:false,
					itemdescr: 'archivo(s)',
					//toolbarURL: '',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Archivo</button>&nbsp;'+
						'<button name="btnCarpeta" class="btn btn-info"><i class="fa fa-folder-o"></i> Nueva Carpeta</button>&nbsp;'+
						'<button name="btnRetroceder" class="btn btn-warning"><i class="fa fa-undo"></i> Regresar</button>&nbsp;',
					onContentLoaded: function($el){	
						var arr = [];
						$el.find('[name=btnCarpeta]').data('last',arr);
						$el.find('[name=btnAgregar]').click(function(){
							mgMult.windowNewFile({$grid:p.$grid});
						});
						$el.find('[name=btnCarpeta]').click(function(){
							mgMult.windowNewFolder({$grid:p.$grid});
						});
						$el.find('[name=btnRetroceder]').click(function(){
							mgMult.reinit({retro: true,$grid:p.$grid});
						}).attr('disabled','disabled');
						if(this.params.dir==null) $el.find('[name=btnRetroceder]').button({disabled:true});
						else $el.find('[name=btnRetroceder]').button({disabled:false});
					},
					params: {},
					fill: function(data,$row){
						if(data.tipo=='F'){
							$row.append('<td>');
							$row.append('<td><span class="icon"><i class="fa fa-folder-open"></i></span></td>');
							$row.append('<td colspan="4">'+data.item+'</td>');
							$row.data('data',data.item).data('tipo',data.tipo).dblclick(function(){
								mgMult.reinit({
									descr: $(this).data('data'),
									$grid: p.$grid,
									next:true
								});
							});
						}else{
							$row.append('<td>');
							$row.append('<td>');
							data.ext = data.item.split('.').pop();
							data.ext = data.ext.toLowerCase();
							if(data.ext=='xlsx'||data.ext=='xls'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-file-excel-o"></i></span>');
							}else if(data.ext=='jpg'||data.ext=='gif'||data.ext=='bmp'||data.ext=='png'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-picture-o"></i></span>');
							}else if(data.ext=='mp4'||data.ext=='wmv'||data.ext=='avi'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-film"></i></span>');
							}else if(data.ext=='pdf'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-pdf-o"></i></span>');
							}else{
								$row.find('td:last').append('<span class="icon"><i class="fa fa-file"></i></span>');
							}
							$row.append( '<td>'+data.item+'</td>' );
							//$row.append('<td>'+result.metadata.mime+'</td>' );
							$row.append('<td>'+data.tamano+'</td>' );
							$row.append('<td>'+data.fecha+'</td>' );
							$row.data('data',data.item).data('tipo',data.tipo).data('ext',data.ext).dblclick(function(){
								p.$w.find('.modal-footer button:first').click();
							}).contextMenu('conMenListSel', {
								bindings: {
									'conMenListSel_sel': function(t) {
										p.$w.find('.modal-footer button:first').click();
									}
								}
							});
						}
						$row.click(function(){
							var ruta = mgMult.home,
							arr = p.$w.find('[name=btnCarpeta]').data('last');
							for(var i=0; i<arr.length; i++){
								if(arr[i].id!=null)
									ruta += arr[i].id+'/';
							}
							if(p.$w.find('[name=btnCarpeta]').data('id')!=null)
								ruta += p.$w.find('[name=btnCarpeta]').data('id');
							ruta += '/'+$(this).data('data');
							mgMult.preview({
								ext: $(this).data('ext'),
								ruta: K.path_file+ruta
							},p.$w);
						});
						return $row;
					}
				});
			}
		});
		
	}
};
define(
	function(){
		return mgMult;
	}
);