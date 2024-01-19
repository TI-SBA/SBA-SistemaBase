tiErro = {
	states: {
		S: {
			descr: "Solucionado",
			color: "green",
			label: '<span class="label label-success">Solucionado</span>'
		},
		O: {
			descr: "En observaci&oacute;n",
			color: "green",
			label: '<span class="label label-warning">En observaci&oacute;n</span>'
		},
		P:{
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-danger">Pendiente</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'ti',
			action: 'tiErro',
			titleBar: {
				title: 'Reporte de Errores del Sistema'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Error',{n:'Script',f:'url'},'Linea',{n:'Reporte del Error',f:'fecreg'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'ti/erro/lista',
					params: {},
					itemdescr: 'registro(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+tiErro.states[data.estado].label+'</td>');
						$row.append('<td><pre>'+data.error+'</pre></td>');
						$row.append('<td><samp>'+data.url+'</samp></td>');
						$row.append('<td>'+data.line+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</kbd><br />'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							//inTipo.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									//inTipo.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									//inTipo.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									/*ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inTipo.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');*/
								},
								'conMenListEd_des': function(t) {
									/*ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											inTipo.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');*/
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
		new K.Modal({ 
			id: 'windowNewTipo',
			title: 'Nuevo Tipo',
			contentURL: 'in/tipo/edit',
			width: 380,
			height: 190,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							cuenta: p.$w.find('[name=cuenta]').data('data')
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						if(data.abrev==''){
							p.$w.find('[name=abrev]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la abreviatura de Tipo!',type: 'error'});
						}
						if(data.cuenta==null){
							p.$w.find('[name=btnCta]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
						}else data.cuenta = ctPcon.dbRel(data.cuenta);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/tipo/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Tipo agregado!"});
							inTipo.init();
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
				p.$w = $('#windowNewTipo');
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'in/tipo/edit',
			width: 380,
			height: 190,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							abrev: p.$w.find('[name=abrev]').val(),
							cuenta: p.$w.find('[name=cuenta]').data('data')
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Tipo!',type: 'error'});
						}
						if(data.abrev==''){
							p.$w.find('[name=abrev]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la abreviatura de Tipo!',type: 'error'});
						}
						if(data.cuenta==null){
							p.$w.find('[name=btnCta]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la cuenta contable!',type: 'error'});
						}else data.cuenta = ctPcon.dbRel(data.cuenta);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("in/tipo/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Tipo actualizado!"});
							inTipo.init();
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
				p.$w = $('#windowEditTipo');
				K.block();
				p.$w.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod+' - '+data.descr).data('data',data);
					},bootstrap: true});
				});
				$.post('in/tipo/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=abrev]').val(data.abrev);
					p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr)
						.data('data',data.cuenta);
					K.unblock();
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
			title: 'Seleccionar Tipo de Local',
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
					data: 'in/tipo/lista',
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
	['mg/enti'],
	function(mgEnti){
		return tiErro;
	}
);