arDocu = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'ar',
			action: 'arDocu',
			titleBar: {
				title: 'Archivo Digital'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Num',f:'num'},'T&iacute;tulo','Previa',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'ar/docu/lista',
					params: {},
					itemdescr: 'documento(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							arDocu.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+arDocu.states[data.estado].label+'</td>');
						$row.append('<td>'+data.tipo+' '+data.num+'</td>');
						$row.append('<td>'+data.titulo+'</td>');
						$row.append('<td><button class="btn btn-sm btn-info"><i class="fa fa-picture-o"></i></button></td>');
						$row.find('button:last').click(function(){
							var data = $(this).closest('.item').data('data');
							K.windowPrint({
								title: data.tipo+' '+data.num,
								url: K.path_file+data.file
							});
						});
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							arDocu.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('data',data).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									arDocu.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									arDocu.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ar/docu/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											arDocu.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ar/docu/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											arDocu.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
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
		$.extend(p,{
			after: function(docu){
				new K.Panel({ 
					title: 'Nuevo Documento',
					contentURL: 'ar/docu/edit',
					store: false,
					buttons: {
						"Guardar": {
							icon: 'fa-save',
							type: 'success',
							f: function(){
								K.clearNoti();
								var data = {
									titulo: p.$w.find('[name=titulo]').val(),
									num: p.$w.find('[name=num]').val(),
									tipo: p.$w.find('[name=tipo]').val(),
									descr: p.$w.find('[name=descr]').val(),
									ubicacion: p.$w.find('[name=ubicacion]').val(),
									file: p.$w.find('[name=file]').html(),
									oficina: p.$w.find('[name=oficina]').data('data')
								};
								if(data.titulo==''){
									p.$w.find('[name=titulo]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
								}
								if(data.num==''){
									p.$w.find('[name=num]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la abreviatura de Tipo!',type: 'error'});
								}
								if(data.tipo==''){
									p.$w.find('[name=tipo]').focus();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la abreviatura de Tipo!',type: 'error'});
								}
								if(data.oficina==null){
									p.$w.find('[name=btnCta]').click();
									return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
								}else data.oficina = mgOfic.dbRel(data.oficina);
								K.sendingInfo();
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ar/docu/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Documento agregado!"});
									arDocu.init();
								},'json');
							}
						},
						"Cancelar": {
							icon: 'fa-ban',
							type: 'danger',
							f: function(){
								arDocu.init();
							}
						}
					},
					onClose: function(){ p = null; },
					onContentLoaded: function(){
						p.$w = $('#mainPanel');
						K.block();
						p.$w.find('[name=num]').val(p.num);
						p.$w.find('[name=btnFile]').click(function(){
							mgMult.windowNewModule({
								multiple: true,
								pdf: true,
								ruta: '/Archivo_Digital',
								nomb: p.num,
								callback: function(data){
									p.$w.find('[name=file]').html('/Archivo_Digital/'+p.num+'.pdf');
									p.$w.find('object').attr('data',K.path_file+'Archivo_Digital/'+p.num+'.pdf');
									p.$w.find('iframe').attr('src',K.path_file+'Archivo_Digital/'+p.num+'.pdf');
								}
							});
						});
						p.$w.find('[name=btnOfi]').click(function(){
							mgOfic.windowSelect({
								bootstrap: true,
								callback: function(data){
									p.$w.find('[name=oficina]').html(data.nomb).data('data',data);
								}
							});
						});
						$.post('td/tdoc/all',function(tipos){
							var tipos_ = [];
							for (var i = tipos.length - 1; i >= 0; i--) {
								tipos_.push(tipos[i].nomb);
							};
							p.$w.find('[name=tipo]').typeahead({
								hint: true,
								highlight: true,
								minLength: 1
							},
							{
								name: 'tipos',
								source: substringMatcher(tipos_)
							});
							p.$w.find('[name=tipo]').focus();
							K.unblock();
						},'json');
					}
				});
			}
		});
		K.msg({title: 'Verificacion de Documento',text: 'Ingrese el numero de documento a Ingresar (Ejem: 1625-2016-SBPA-GG)'});
		new K.Modal({
			id: 'windowSelect',
			title: 'Nuevo Documento',
			content: '<div class="form-group">'+
					'<label class="col-sm-4 control-label">N&uacute;mero de Documento</label>'+
					'<div class="col-sm-8">'+
						'<input type="text" class="form-control" name="num">'+
					'</div>'+
				'</div>',
			height: 75,
			width: 400,
			buttons: {
				'Ingresar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						p.num = $('#windowSelect [name=num]').val();
						if(p.num!=''){
							K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar un numero!',type: 'error'});
						}
						$.post('ar/docu/verify',{num: p.num},function(docu){
							if(docu!=null){
								return K.msg({title: 'El Documento ya est&aacute; registrado!'});
							}
							p.after(docu);
							K.closeWindow('windowSelect');
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow('windowSelect');
						p = null;
					}
				}
			},
			onContentLoaded: function(){
				$('#windowSelect [name=num]').keyup(function(e){
					if(e.keyCode == 13)
						$('#windowSelect #div_buttons button:first').click();
				}).focus();
			}
		});
	},
	windowEdit: function(p){
		new K.Panel({ 
			title: 'Nuevo Documento',
			contentURL: 'ar/docu/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							titulo: p.$w.find('[name=titulo]').val(),
							tipo: p.$w.find('[name=tipo]').val(),
							descr: p.$w.find('[name=descr]').val(),
							ubicacion: p.$w.find('[name=ubicacion]').val(),
							file: p.$w.find('[name=file]').html(),
							oficina: p.$w.find('[name=oficina]').data('data')
						};
						if(data.titulo==''){
							p.$w.find('[name=titulo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						if(data.tipo==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la abreviatura de Tipo!',type: 'error'});
						}
						if(data.oficina==null){
							p.$w.find('[name=btnCta]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
						}else data.oficina = mgOfic.dbRel(data.oficina);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ar/docu/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Documento actualizado!"});
							arDocu.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						arDocu.init();
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=num]').val(p.num);
				p.$w.find('[name=btnFile]').click(function(){
					mgMult.windowNewModule({
						multiple: true,
						pdf: true,
						ruta: '/Archivo_Digital',
						nomb: p.num,
						callback: function(data){
							p.$w.find('[name=file]').html('/Archivo_Digital/'+p.num+'.pdf');
							p.$w.find('object').attr('data',K.path_file+'Archivo_Digital/'+p.num+'.pdf');
							p.$w.find('iframe').attr('src',K.path_file+'Archivo_Digital/'+p.num+'.pdf');
						}
					});
				});
				p.$w.find('[name=btnOfi]').click(function(){
					mgOfic.windowSelect({
						bootstrap: true,
						callback: function(data){
							p.$w.find('[name=oficina]').html(data.nomb).data('data',data);
						}
					});
				});
				$.post('td/tdoc/all',function(tipos){
					var tipos_ = [];
					for (var i = tipos.length - 1; i >= 0; i--) {
						tipos_.push(tipos[i].nomb);
					};
					p.$w.find('[name=tipo]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'tipos',
						source: substringMatcher(tipos_)
					});
					$.post('ar/docu/get',{_id: p.id},function(data){
						p.$w.find('[name=titulo]').val(data.titulo);
						p.$w.find('[name=num]').val(data.num);
						p.$w.find('[name=tipo]').val(data.tipo);
						p.$w.find('[name=descr]').val(data.descr);
						p.$w.find('[name=ubicacion]').val(data.ubicacion);
						p.$w.find('[name=oficina]').html(data.oficina.nomb).data('data',data.oficina);
						p.$w.find('[name=file]').html(data.file).data('data',data.file);
						p.$w.find('iframe').attr('src',K.path_file+'Archivo_Digital/'+data.num+'.pdf');
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Documento',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.clearNoti();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
						}
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
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'ar/docu/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('data',data).dblclick(function(){
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
	}
};
define(
	['mg/enti','mg/mult','mg/ofic'],
	function(mgEnti,mgMult,mgOfic){
		return arDocu;
	}
);