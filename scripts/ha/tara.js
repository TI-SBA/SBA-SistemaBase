haTara = {
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'haTara',
			titleBar: {
				title: 'Tarifario de Productos Agr&iacute;colas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Producto',f:'prod'},'Precio','Unidad',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'ho/tari/lista',
					params: {tipo: 'AG'},
					itemdescr: 'tarifa(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Producto</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							haTara.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.precio)+'</td>');
						$row.append('<td>'+data.unidad+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							haTara.windowNew({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									haTara.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Producto <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ho/tari/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Producto Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											haTara.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Producto');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Producto <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ho/tari/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Producto Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											haTara.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Producto');
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
			id: 'windowEdit',
			title: 'Nuevo Producto',
			contentURL: 'ho/tari/edit_agri',
			store: false,
			width: 450,
			height: 250,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							tipo: 'AG',
							nomb: p.$w.find('[name=nomb]').val(),
							precio: p.$w.find('[name=precio]').val(),
							unidad: p.$w.find('[name=unidad]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nombre del producto!',type: 'error'});
						}
						if(data.precio==''){
							p.$w.find('[name=precio]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el costo del producto!',type: 'error'});
						}
						if(data.unidad==''){
							p.$w.find('[name=unidad]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la unidad del producto!',type: 'error'});
						}
						if(p.id!=null) data._id = p.id;
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ho/tari/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Tarifa actualizada!"});
							haTara.init();
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
				p.$w = $('#windowEdit');
				K.block();
				$.post('lg/unid/all',function(tipos){
					var tipos_ = [];
					for (var i = tipos.length - 1; i >= 0; i--) {
						tipos_.push(tipos[i].nomb);
					};
					p.$w.find('[name=unidad]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'tipos',
						source: substringMatcher(tipos_)
					});
					if(p.id!=null){
						$.post('ho/tari/get',{_id: p.id},function(data){
							p.$w.find('[name=nomb]').val(data.nomb);
							p.$w.find('[name=precio]').val(data.precio);
							p.$w.find('[name=unidad]').val(data.unidad);
							K.unblock();
						},'json');
					}else
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
			title: 'Seleccionar Producto',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						var data = p.$w.find('.highlights').data('data');
						if(data!=null){
							K.closeWindow(p.$w.attr('id'));
							p.callback(data);
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
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre','Precio','Unidad'],
					data: 'ho/tari/lista',
					params: {tipo: 'AG'},
					itemdescr: 'tarifario(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.precio)+'</td>');
						$row.append('<td>'+data.unidad+'</td>');
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
		return haTara;
	}
);