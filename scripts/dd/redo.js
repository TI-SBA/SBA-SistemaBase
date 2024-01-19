ddRedo = {
		states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Digitalizado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">No Digitalizado</span>'
		}
	},
		dbRel: function(item){
		return {
			_id: item._id.$id,
			titu: item.titu,
			cant: item.cant,
			nro: item.nro,
			remi: item.remi,
			dire: item.dire,
			id_dire: item.id_dire,
			tise: item.tise,
			id_tise: item.id_tise,
			tipo_: item.tipo_,
			id_tipo_: item.id_tipo_,
			ubic: item.ubic,
			obse: item.obse,
			fec: item.fec
			//ofic: item.ofic
			
			//femi: item.femi
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddRedo',
			titleBar: {
				title: 'Recepcion de Documentos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nro','Documento','Remitente','Programa','Tipo de Serie','Tipo','Fechas Extremas'],
					data: 'dd/redo/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddRedo.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nro+'</td>');
						$row.append('<td>'+data.titu+'</td>');
						$row.append('<td>'+data.remi+'</td>');
						$row.append('<td>'+data.dire+'</td>');
						$row.append('<td>'+data.tise+'</td>');
						$row.append('<td>'+data.tipo_+'</td>');
						var year_0 = '';
						var year_1 = '';
						if(data.year!=null){
							year_0 = data.year[0].fec;
							if(data.year[1]!=null) year_1 = data.year[1].fec;
						}
						$row.append('<td>'+year_0+' - '+year_1+ '</td>');
//						$row.append('<td>'+data.year[0].fec+'</td>');
						//$row.find('[name=fec]').val(data.year[i].fec);
						//$row.append('<td>'+data.year[0].fec+' - '+data.year[1]+ '</td>');

						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddRedo.windowDetails({_id: $(this).data('id'),titu: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenRedo", {
							bindings: {
								'conMenRedo_inf': function(t) {
									ddRedo.windowDetails({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								/*
								'conMenRedo_sub': function(t) {
									ddRedo.windowSubirArchivo({id: K.tmp.data('id')});
								},
								'conMenRedo_dow': function(t) {
									if(data.url_imagen!=null){
										window.open(data.url_imagen);	
									}else{
										K.notification({title: 'Documento no Digitalizado',text: 'El documento no fue digitalizado todavia!',type: 'error'});
										
									}
									
								},
								*/				
								'conMenRedo_edi': function(t) {
									ddRedo.windowEdit({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenRedo_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Recepcion de Documentos:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/redo/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Recepcion de Documentos Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddRedo.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Recepcion de Documentos');
								},
								'conMenRedo_inf':function(t6){
									K.windowPrint({
										id:'windowPrint',
										title:"Recepcion Documentaria",
										url:"dd/redo/print?_id="+K.tmp.data("id")
									});
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
			id: 'windowNewRecepcion',
			title: 'Nueva Recepcion de Documentos',
			contentURL: 'dd/redo/edit',
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
							cant: p.$w.find('[name=cant]').val(),
							nro: p.$w.find('[name=nro]').val(),
						//	ofic:p.$w.find('[name=ofic]').val(),
							remi: p.$w.find('[name=remi]').val(),
							dire: p.$w.find('[name=dire]').text(),
							id_dire: p.$w.find('[name=id_dire]').text(),
							tise: p.$w.find('[name=tise]').text(),
							id_tise: p.$w.find('[name=id_tise]').text(),
							tipo_: p.$w.find('[name=tipo_]').text(),
							id_tipo_: p.$w.find('[name=id_tipo_]').text(),
							ubic: p.$w.find('[name=ubic]').val(),
							obse: p.$w.find('[name=obse]').val(),
							
							year:[]
							
						};
						if(p.$w.find('[name=gridYear] tbody tr').length>0){
							for( var i=0;i< p.$w.find('[name=gridYear] tbody tr').length;i++){
								 var $row = p.$w.find('[name=gridYear] tbody tr').eq(i);
								 var _year = {
								 	
								 	fec: $row.find('[name=fec]').val()
								 }
								 data.year.push(_year);
							}
						}
						
						if(data.titu==''){
							p.$w.find('[name=titu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo2!',type: 'error'});
						}
						if(data.cant==''){
							p.$w.find('[name=cant]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo3!',type: 'error'});
						}
						if(data.nro==''){
							p.$w.find('[name=nro]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo4!',type: 'error'});
						}
												
						if(data.remi==''){
							p.$w.find('[name=remi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo5!',type: 'error'});
						}

						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!6',type: 'error'});
						}
						if(data.tise==''){
							p.$w.find('[name=tise]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!7',type: 'error'});
						}
						if(data.tipo_==''){
							p.$w.find('[name=tipo_]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!8',type: 'error'});
						}
						
						
						

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/redo/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Recepcion de Documentos agregado!"});
							ddRedo.init();
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
				p.$w = $('#windowNewRecepcion');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			fecDate: '-3d'
				});	
				
				p.$w.find('[name=btnTise]').click(function(){
					ddTise.windowSelect({callback: function(data){
						p.$w.find('[name=tise]').html(data.tipo).data('data',data);
						p.$w.find('[name=id_tise]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnTipo]").click(function(){
					ddTipo.windowSelect({callback: function(data){
						p.$w.find('[name=tipo_]').html(data.tipo).data('data',data);
						p.$w.find('[name=id_tipo_]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=dire]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_dire]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
			
				$.post("dd/redo/get_nro",function(data){
					var n=0;
					if(data ==null){
						n = 1;
					}else{
						n = parseFloat(data.nro) + 1;
					}
					p.$w.find('[name=nro]').val(n);
				},'json');

				new K.grid({
					$el: p.$w.find('[name=gridYear]'),
					cols: ['Fechas Externas'],
					stopLoad: true,
					pagination: false,
					search: false,
					store: false,
							toolbarHTML: '<button type = "button" name="btnAddFecha" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Fecha Externa</button >',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridYear] tbody').append($row);
						});
					}
				});

			}

		});
	},
	windowSubirArchivo: function(p){
		new K.Modal({
			id: 'windowSubirArchivo',
			contentURL: 'dd/redo/upload',
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
					uploadUrl: "dd/redo/subir",
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
					$.post('dd/redo/save',{_id:p.id,url_imagen:url_imagen},function(){
						K.unblock();
						K.closeWindow(p.$w.attr('id'));
					},'json');
				});
				
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowNewRecepcion',
			title: 'Editar Recepcion de Documentos: '+p.nro,
			contentURL: 'dd/redo/edit',
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
							//ofic: p.$w.find('[name=ofic]').val(),
							titu: p.$w.find('[name=titu]').val(),
							cant: p.$w.find('[name=cant]').val(),
							nro: p.$w.find('[name=nro]').val(),
							remi: p.$w.find('[name=remi]').val(),
							dire: p.$w.find('[name=dire]').text(),
							id_dire: p.$w.find('[name=id_dire]').text(),
							tise: p.$w.find('[name=tise]').text(),
							id_tise: p.$w.find('[name=id_tise]').text(),
							tipo_: p.$w.find('[name=tipo_]').text(),
							id_tipo_: p.$w.find('[name=id_tipo_]').text(),
							ubic: p.$w.find('[name=ubic]').val(),
							obse: p.$w.find('[name=obse]').val(),
							
							year:[]
						};

						if(p.$w.find('[name=gridYear] tbody tr').length>0){
							for( var i=0;i< p.$w.find('[name=gridYear] tbody tr').length;i++){
								 var $row = p.$w.find('[name=gridYear] tbody tr').eq(i);
								 var _year = {
								 	
								 	fec: $row.find('[name=fec]').val()
								 }
								 data.year.push(_year);
							}
						}
						
						if(data.titu==''){
							p.$w.find('[name=titu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cant==''){
							p.$w.find('[name=cant]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.nro==''){
							p.$w.find('[name=nro]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
												
						if(data.remi==''){
							p.$w.find('[name=remi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}

						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.tise==''){
							p.$w.find('[name=tise]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.tipo_==''){
							p.$w.find('[name=tipo_]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/redo/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Recepcion de Documentos actualizado!"});
							ddRedo.init();
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
				p.$w = $('#windowNewRecepcion');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			fecDate: '-3d'
				});	
				K.block();
			p.$w.find('[name=btnTise]').click(function(){
					ddTise.windowSelect({callback: function(data){
						p.$w.find('[name=tise]').html(data.tipo).data('data',data);
						p.$w.find('[name=id_tise]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnTipo]").click(function(){
					ddTipo.windowSelect({callback: function(data){
						p.$w.find('[name=tipo_]').html(data.tipo).data('data',data);
						p.$w.find('[name=id_tipo_]').html(data._id.$id).data('data',data);
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
							p.$w.find('[name=file]').html('/Archivo_Digital/'+data.file);
						}
					});
				});

				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridYear]'),
					cols: ['Fechas Externas'],
					stopLoad: true,
					pagination: false,
					search: false,
					store: false,
					toolbarHTML: '<button type = "button" name="btnAddFecha" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Fecha Externa</button >',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input type="text" class="form-control" name="fec" /></td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridYear] tbody').append($row);
						});
					}
				});


				$.post('dd/redo/get',{_id: p.id},function(data){
				
				p.$w.find('[name=titu]').val(data.titu);
				//p.$w.find('[name=ofic]').val(data.ofic);
				p.$w.find('[name=cant]').val(data.cant);
				p.$w.find('[name=nro]').val(data.nro);
				p.$w.find('[name=remi]').val(data.remi);
				p.$w.find('[name=dire]').text(data.dire),
				p.$w.find('[name=id_dire]').text(data.id_dire),
				p.$w.find('[name=tise]').text(data.tise);
				p.$w.find('[name=id_tise]').text(data.id_tise);
				p.$w.find('[name=tipo_]').text(data.tipo_);
				p.$w.find('[name=id_tipo_]').text(data.id_tipo_);
				p.$w.find('[name=ubic]').val(data.ubic);
				p.$w.find('[name=obse]').val(data.obse);
				

				if(data.year!=null){
					if(data.year.length>0){
						for(var i= 0;i<data.year.length;i++){
							p.$w.find('[name=btnAddFecha]').click();
							var $row = p.$w.find('[name=gridYear] tbody tr:last');
							
							$row.find('[name=fec]').val(data.year[i].fec);
						}
					}
				}
				
				K.unblock();
				},'json');
			}
		});
	},
};
define(
	['mg/enti','dd/tise','dd/tipo','dd/dire','mg/prog','dd/tido','mg/mult'],
	function(mgEnti,ddTise,ddTipo,ddDire,mgProg,ddTido,ddMult){
		return ddRedo;
	}
);
