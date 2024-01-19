tiEdit = {
	home: '/',
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ti',
			action: 'tiEdit',
			titleBar: { title: 'Editor de C&oacute;digo' }
		});		
		new K.Panel({
			contentURL: 'ti/edit',
			store: false,
			onContentLoaded: function(){
				$mainPanel = $('#mainPanel');
				p.$grid = new K.grid({
					$el: $('#mainPanel [name=grid]'),
					cols: [
						'',
						'',
					    'Nombre'
					],
					data: 'ti/edit/lista',
					pagination:false,
					search:false,
					itemdescr: 'archivo(s)',
					//toolbarURL: '',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Archivo</button>&nbsp;'+
						'<button name="btnCarpeta" class="btn btn-info"><i class="fa fa-folder-o"></i> Nueva Carpeta</button>&nbsp;'+
						'<button name="btnRetroceder" class="btn btn-warning"><i class="fa fa-undo"></i> Regresar</button>&nbsp;',
					onContentLoaded: function($el){	
						var arr = [];
						$el.find('[name=btnCarpeta]').data('last',arr).hide();
						$el.find('[name=btnAgregar]').click(function(){
							tiEdit.windowNewFile({$grid:p.$grid});
						}).hide();
						$el.find('[name=btnCarpeta]').click(function(){
							tiEdit.windowNewFolder({$grid:p.$grid});
						}).hide();
						$el.find('[name=btnRetroceder]').click(function(){
							tiEdit.reinit({retro: true,$grid:p.$grid,title: function(title){
								K.TitleBar({title: title});
							}});
						}).attr('disabled','disabled');
						$mainPanel.find('.datagrid-header-left').removeClass('col-md-8');
						$mainPanel.find('.datagrid-header-left').addClass('col-sm-12');
						$mainPanel.find('.datagrid-header-right').remove();
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					params: {},
					fill: function(data,$row){
						var ok = true;
						if(data.tipo=='F'){
							$row.append('<td>');
							$row.append('<td><span class="icon"><i class="fa fa-folder"></i></span></td>');
							$row.append('<td colspan="2">'+data.item+'</td>');
							$row.data('data',data.item).data('tipo',data.tipo).dblclick(function(){
								tiEdit.reinit({
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
									var ruta = tiEdit.home,
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
										tiEdit.windowEditFolder({id:K.tmp.data('data')._id.$id,$grid: p.$grid});
									},
									'conMenMgMult_eli': function(t) {
										K.sendingInfo();
										var ruta = tiEdit.home,
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
											tiEdit.reinit({$grid:p.$grid,title: function(title){
												K.TitleBar({title: title});
											}});
										});
									}
								}
							});
						}else{
							data.ext = data.item.split('.').pop();
							data.ext = data.ext.toLowerCase();
							$row.append('<td>');
							$row.append('<td>');
							if(data.ext!='css'&&data.ext!='js'&&data.ext!='php') ok = false;
							if(data.ext=='css'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-css3"></i></span>');
							}else if(data.ext=='js'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-code"></i></span>');
							}else if(data.ext=='php'){
								$row.find('td:last').append('<span class="icon"><i class="fa fa-file-code-o"></i></span>');
							}else{
								$row.find('td:last').append('<span class="icon"><i class="fa fa-question"></i></span>');
							}
							$row.append( '<td>'+data.item+'</td>' );
							$row.data('data',data.item).data('tipo',data.tipo).data('ext',data.ext).dblclick(function(){
								var ruta = tiEdit.home,
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
									var ruta = tiEdit.home,
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
										tiEdit.windowEdit({id:K.tmp.data('data')._id.$id,$grid: p.$grid});
									},
									'conMenMgMult_eli': function(t) {
										K.sendingInfo();
										var ruta = tiEdit.home,
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
											tiEdit.reinit({$grid:p.$grid,title: function(title){
												K.TitleBar({title: title});
											}});
										});
									},
									'conMenMgMult_des': function(t) {
										var ruta = tiEdit.home,
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
							var ruta = tiEdit.home,
							arr = $mainPanel.find('[name=btnCarpeta]').data('last');
							for(var i=0; i<arr.length; i++){
								if(arr[i].id!=null)
									ruta += arr[i].id+'/';
							}
							if($mainPanel.find('[name=btnCarpeta]').data('id')!=null)
								ruta += $mainPanel.find('[name=btnCarpeta]').data('id');
							ruta += '/'+$(this).data('data');
							tiEdit.preview({
								ext: $(this).data('ext'),
								ruta: K.path_file+ruta,
								path: ruta
							},$mainPanel);
						});
						if(ok==true)
							return $row;
						else
							return null;
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
				p.title('Editor de C&oacute;digo: /');
			}else if(tmp[tmp.length-1]!=null){
				if(tmp[tmp.length-1].id!=null){
					p.$grid.$el.find('[name=btnCarpeta]').data('id',tmp[tmp.length-1].id);
					K.TitleBar({title: tmp[tmp.length-1].title});
				}
			}else{
				p.$grid.$el.find('[name=btnCarpeta]').removeData('id');
				p.title('Editor de C&oacute;digo: /');
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
		var ruta = tiEdit.home,
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
		var editor;
		file.path = file.path.substring(1);
		$w.find('[name=preview]').empty();
		if(file.ext==null){
			$w.find('[name=preview]').append('<h3>Seleccione un archivo!</h3>');
		}else{
			K.block();
			$w.find('[name=preview]').append('<textarea cols="30" rows="4" name="codigo" id="codigo"></textarea>'+
				'<input type="hidden" name="path" value="'+file.path+'" />'+
				'<button name="btnSave" class="btn btn-success"><i class="fa fa-save"></i> Guardar C&oacute;digo</button>');
			if(file.ext=='css'){
				$.get('ti/edit/get',{url: file.path},function(data){
		   		//$.get('ti/edit/get',{url: 'scripts/kunan_ins.js'},function(data){
	   				$mainPanel.find('[name=codigo]').val(data);
	   				editor = CodeMirror.fromTextArea(document.getElementById("codigo"), {
						lineNumbers: true,
						matchBrackets: true,
						styleActiveLine: true,
						theme:"ambiance",
						mode: "css"
					});
	   				K.unblock();
	   			});
			}else if(file.ext=='php'){
				$.get('ti/edit/get',{url: file.path},function(data){
		   		//$.get('ti/edit/get',{url: 'scripts/kunan_ins.js'},function(data){
	   				$mainPanel.find('[name=codigo]').val(data);
	   				editor = CodeMirror.fromTextArea(document.getElementById("codigo"), {
						lineNumbers: true,
						matchBrackets: true,
						styleActiveLine: true,
						theme:"ambiance",
						mode: "php"
					});
	   				K.unblock();
	   			});
			}else if(file.ext=='js'){
				$.get('ti/edit/get',{url: file.path},function(data){
		   		//$.get('ti/edit/get',{url: 'scripts/kunan_ins.js'},function(data){
	   				$mainPanel.find('[name=codigo]').val(data);
	   				editor = CodeMirror.fromTextArea(document.getElementById("codigo"), {
						lineNumbers: true,
						matchBrackets: true,
						styleActiveLine: true,
						theme:"ambiance",
						mode: "javascript"
					});
	   				K.unblock();
	   			});
			}
			$mainPanel.find('[name=btnSave]').click(function(){
				editor.save();
				var content = editor.getValue();
				var path = $mainPanel.find("[name=path]").val();
				$.ajax({
					type: "POST",
					url: "ti/edit/save",
					data: {c:content,p:path},
					dataType: 'text',
					success: function(){
						K.msg({title: ciHelper.titles.regiGua,text:"Archivo Guardado!"}); 
					}
				});
	   		});
		}
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return tiEdit;
	}
);