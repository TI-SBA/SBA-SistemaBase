/*************************************************************************
  Convenios */
alConv = {
	init: function(){
		K.initMode({
			mode: 'al',
			action: 'alConv',
			titleBar: {
				title: 'Convenios'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Entidad',f:'entidad.fullname'},{n:'Fecha Inicio',f:'fecini'},{n:'Fecha Fin',f:'fecfin'},{n:'Aportes SBPA',f:'aportes.sbpa'},{n:'Aportes Entidad',f:'aportes.entidad'},{n:'Comisi&oacute;n',f:'comision'},{n:'Adenda',f:'adenda'}],
					data: 'al/conv/lista',
					params: {},
					itemdescr: 'convenio(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							alConv.windowNew();
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						if(data.file!=null){
							if(data.file!=''){
								$row.append('<td><button name="btnFile" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i></button></td>');
								$row.find('[name=btnFile]').click(function(){
									var data = $(this).closest('.item').data('data');
									K.windowPrint({
										id: 'window',
										title: "Convenio",
										url: K.path_file+data.file
									});
								});
							}else $row.append('<td><i class="fa fa-close"></i></td>');
						}else $row.append('<td><i class="fa fa-close"></i></td>');
						$row.append('<td>'+mgEnti.formatName(data.entidad)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecini)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
						$row.append('<td>'+(data.aportes.sbpa).substr(0,50)+'...'+'</td>');
						$row.append('<td>'+(data.aportes.entidad).substr(0,50)+'...'+'</td>');
						$row.append('<td>'+(data.comision).substr(0,50)+'...'+'</td>');
						$row.append('<td>'+(data.adenda).substr(0,50)+'...'+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							alConv.windowEdit({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenList", {
							onShowMenu: function($row, menu) {
								$('#conMenList_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									alConv.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenList_eli': function(t) {
									ciHelper.confirm('&#191;Esta seguro(a) de eliminar este convenio&#63;',
									function(){
										K.sendingInfo();
										$.post('al/conv/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Convenio Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											alConv.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Item');
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowDetails: function(p){
		new K.Panel({
			title: 'Contingencia N&deg;: '+p.num,
			contentURL: 'al/cont/details_cont',
			store: false,
			buttons: {
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						alConv.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				$.post('al/cont/get',{_id:p.id},function(data){
					
					
					
					
					
					
					
					
					
					K.unblock({$element: p.$w});
				},'json');
				
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Nueva Convenio',
			contentURL: 'al/conv/edit_conv',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							file: p.$w.find('[name=file]').html()
						};
						if(data.fecini==''){
							p.$w.find('[name=fecini]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin !',type: 'error'});
						}
						data.aportes = {};
						data.aportes.sbpa = p.$w.find('[name=aportesbene]').val();
						if(data.aportes.sbpa==''){
							p.$w.find('[name=aportesbene]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Aportes SBPA !',type: 'error'});
						}
						data.aportes.entidad = p.$w.find('[name=aportesenti]').val();
						if(data.aportes.entidad==''){
							p.$w.find('[name=aportesenti]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Aportes Entidad !',type: 'error'});
						}
						data.comision = p.$w.find('[name=comision]').val();
						if(data.comision==''){
							p.$w.find('[name=comision]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Comisi&oacute;n!',type: 'error'});
						}
						data.adenda = p.$w.find('[name=adenda]').val();
						var entidad = p.$w.find('[name=mini_enti]').data('data');
						if(entidad==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Entidad!',type: 'error'});
						}else data.entidad = mgEnti.dbRel(entidad);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('al/conv/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Convenio fue registrado con &eacute;xito!'});
							alConv.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						alConv.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=numero]').focus();
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnFile]').click(function(){
					var entidad = p.$w.find('[name=mini_enti]').data('data'),
					fecini = p.$w.find('[name=fecini]').val(),
					fecfin = p.$w.find('[name=fecfin]').val();
					if(entidad==null){
						p.$w.find('[name=btnSel]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una entidad',
							type: 'error'
						});
					}
					if(fecini==''){
						p.$w.find('[name=fecini]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar primero la fecha de inicio',
							type: 'error'
						});
					}
					if(fecfin==''){
						p.$w.find('[name=fecfin]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar primero la fecha de fin',
							type: 'error'
						});
					}
					mgMult.windowNewModule({
						multiple: true,
						pdf: true,
						ruta: '/Asesoria_Legal/Convenios',
						nomb: fecini+'<->'+fecfin+'__'+mgEnti.formatName(entidad),
						callback: function(data){
							p.$w.find('[name=file]').html('/Asesoria_Legal/Convenios/'+fec+'__'+mgEnti.formatName(entidad)+'.pdf');
							p.$w.find('object').attr('data',K.path_file+'Asesoria_Legal/Convenios/'+fecini+'<->'+fecfin+'__'+mgEnti.formatName(entidad)+'.pdf');
						}
					});
				});
				p.$w.find('[name=btnAct]').hide();
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Panel({
			title: 'Editar Convenio',
			contentURL: 'al/conv/edit_conv',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							file: p.$w.find('[name=file]').html()
						};
						if(data.fecini==''){
							p.$w.find('[name=fecini]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
						}
						if(data.fecfin==''){
							p.$w.find('[name=fecfin]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin !',type: 'error'});
						}
						data.aportes = {};
						data.aportes.sbpa = p.$w.find('[name=aportesbene]').val();
						if(data.aportes.sbpa==''){
							p.$w.find('[name=aportesbene]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Aportes SBPA !',type: 'error'});
						}
						data.aportes.entidad = p.$w.find('[name=aportesenti]').val();
						if(data.aportes.entidad==''){
							p.$w.find('[name=aportesenti]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Aportes Entidad !',type: 'error'});
						}
						data.comision = p.$w.find('[name=comision]').val();
						if(data.comision==''){
							p.$w.find('[name=comision]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Comisi&oacute;n!',type: 'error'});
						}
						data.adenda = p.$w.find('[name=adenda]').val();
						var entidad = p.$w.find('[name=mini_enti]').data('data');
						if(entidad==null){
							p.$w.find('[name=btnSel]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Entidad!',type: 'error'});
						}else data.entidad = mgEnti.dbRel(entidad);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('al/conv/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Convenio fue actualizado con &eacute;xito!'});
							alConv.init();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						alConv.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=numero]').focus();
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
				p.$w.find('[name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data,1);
					},bootstrap: true});
				});
				p.$w.find('[name=btnFile]').click(function(){
					var entidad = p.$w.find('[name=mini_enti]').data('data'),
					fecini = p.$w.find('[name=fecini]').val(),
					fecfin = p.$w.find('[name=fecfin]').val();
					if(entidad==null){
						p.$w.find('[name=btnSel]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una entidad',
							type: 'error'
						});
					}
					if(fecini==''){
						p.$w.find('[name=fecini]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar primero la fecha de inicio',
							type: 'error'
						});
					}
					if(fecfin==''){
						p.$w.find('[name=fecfin]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar primero la fecha de fin',
							type: 'error'
						});
					}
					mgMult.windowNewModule({
						multiple: true,
						pdf: true,
						ruta: '/Asesoria_Legal/Convenios',
						nomb: fecini+'<->'+fecfin+'__'+mgEnti.formatName(entidad),
						callback: function(data){
							p.$w.find('[name=file]').html('/Asesoria_Legal/Convenios/'+fec+'__'+mgEnti.formatName(entidad)+'.pdf');
							p.$w.find('object').attr('data',K.path_file+'Asesoria_Legal/Convenios/'+fecini+'<->'+fecfin+'__'+mgEnti.formatName(entidad)+'.pdf');
						}
					});
				});
				p.$w.find('[name=btnAct]').hide();
				$.post('al/conv/get','id='+p.id,function(data){
					p.$w.find('[name=fecini]').val( ciHelper.date.format.bd_ymd(data.fecini) );
					p.$w.find('[name=fecfin]').val( ciHelper.date.format.bd_ymd(data.fecfin) );
					p.$w.find('[name=aportesbene]').val(data.aportes.sbpa);
					p.$w.find('[name=aportesenti]').val(data.aportes.entidad);
					p.$w.find('[name=comision]').val(data.comision);
					p.$w.find('[name=adenda]').val(data.adenda);
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data.entidad);
					if(data.file!=null){
						p.$w.find('[name=file]').html(data.file);
						p.$w.find('object').attr('data',K.path_file+data.file);
					}
					K.unblock({$element: p.$w});
				},'json');
				
			}
		});
	}	
};
define(
	['mg/mult'],
	function(mgMult){
		return alConv;
	}
);