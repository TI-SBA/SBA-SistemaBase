/*******************************************************************************
espacios */
mgOfic = {
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
		var ret ={
			_id: item._id.$id,
			cod: item.cod,
			nomb: item.nomb
		};
		if(item.meta!=null){
			ret.meta = {
				cod: item.meta.cod
			};
		}
		return ret;
	},
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgOfic',
			titleBar: {
				title: 'Oficinas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','','Codigo',{n:'Nombre',f:'nomb'}],
					data: 'mg/ofic/lista',
					params: {},
					itemdescr: 'oficina(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success" type="button">Nueva Oficina</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mgOfic.windowNew();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgOfic.states[data.estado].label+'</td>');
						$row.append('<td>'+data.codigo+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							mgOfic.windowEdit({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
								
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									mgOfic.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> la Oficina <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mg/ofic/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Oficina Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mgOfic.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Oficina');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> la Oficina <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mg/ofic/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Oficina Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mgOfic.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Oficina');
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
			id: 'windowNew',
			title: 'Nueva Oficina',
			contentURL: 'mg/ofic/edit',
			width: 350,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cod: p.$w.find('[name=cod]').val(),
							meta: {
								cod: p.$w.find('[name=meta]').val()
							}
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un nombre!',
								type: 'error'
							});
						}
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/ofic/save",data,function(rpta){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Oficina agregada con &eacute;xito!"
							});
							K.closeWindow(p.$w.attr('id'));
							mgOfic.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowNew');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNew',
			title: 'Editar Oficina',
			contentURL: 'mg/ofic/edit',
			width: 350,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							cod: p.$w.find('[name=cod]').val(),
							meta: {
								cod: p.$w.find('[name=meta]').val()
							}
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un nombre!',
								type: 'error'
							});
						}
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/ofic/save",data,function(rpta){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: "Oficina actualizada con &eacute;xito!"
							});
							K.closeWindow(p.$w.attr('id'));
							mgOfic.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				K.block({$element: p.$w});
				$.post('mg/ofic/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod]').val(data.cod);
					if(data.meta!=null){
						p.$w.find('[name=meta]').val(data.meta.cod);	
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap==null){
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Oficina',
				buttons: {
					'Seleccionar': function(){
						if(p.multiple!=null){
							var orgas = [];
							for(var i=0,j=p.$w.find('[name=orga]:checked').length; i<j; i++){
								orgas.push(p.$w.find('[name=orga]:checked').eq(i).closest('.item').data('data'));
							}
							if(orgas.length==0)
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger al menos una oficina!',
									type: 'error'
								});
							p.callback(orgas);
							K.closeWindow(p.$w.attr('id'));
						}else{
							if(p.$w.find('.ui-state-highlight').length<=0){
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una Organizacion!',type: 'error'});
							}
							p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}
					},
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','Nombre'],
						data: 'mg/ofic/lista',
						params: {},
						itemdescr: 'oficina(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							if(p.multiple!=null)
								$row.append('<td><input type="checkbox" name="orga" value="'+data._id.$id+'"></td>');
							else
								$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.data('data',data).dblclick(function(){
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
							$row.find('td:eq(1)').click(function(){
								var $check = $(this).closest('.item').find('[name=orga]');
								if($check.is(':checked')==false) $check.attr('checked','checked');
								else $check.removeAttr('checked');
							});
							return $row;
						}
					});
				}
			});
		}else{
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				height: 550,
				width: 550,
				title: 'Seleccionar Oficina',
				buttons: {
					"Seleccionar": {
						icon: 'fa-check',
						type: 'info',
						f: function(){
							if(p.multiple!=null){
								var orgas = [];
								for(var i=0,j=p.$w.find('[name=orga]:checked').length; i<j; i++){
									orgas.push(p.$w.find('[name=orga]:checked').eq(i).closest('.item').data('data'));
								}
								if(orgas.length==0)
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe escoger al menos una oficina!',
										type: 'error'
									});
								p.callback(orgas);
								K.closeWindow(p.$w.attr('id'));
							}else{
								if(p.$w.find('.highlights').data('data')!=null){
									p.callback(p.$w.find('.highlights').data('data'));
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
				//onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','Nombre'],
						data: 'mg/ofic/lista',
						params: {},
						itemdescr: 'oficina(s)',
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							if(p.multiple!=null)
								$row.append('<td><input type="checkbox" name="orga" value="'+data._id.$id+'"></td>');
							else
								$row.append('<td>');
							$row.append('<td>'+data.nomb+'</td>');
							$row.data('data',data).dblclick(function(){
								p.$w.find('.modal-footer button:first').click();
							});
							if(p.multiple!=null){
								$row.find('td:eq(1)').click(function(){
									var $check = $(this).closest('.item').find('[name=orga]');
									if($check.is(':checked')==false) $check.attr('checked','checked');
									else $check.removeAttr('checked');
								});
							}else{
								$row.contextMenu('conMenListSel', {
									bindings: {
										'conMenListSel_sel': function(t) {
											p.$w.find('.modal-footer button:first').click();
										}
									}
								});
							}
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
		return mgOfic;
	}
);