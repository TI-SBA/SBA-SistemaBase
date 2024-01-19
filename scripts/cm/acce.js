/*Accesorios*/
cmAcce = {
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
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmAcce',
			titleBar: { title: 'Accesorios' }
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','Estado','Nombre','Precio'],
					data: 'cm/acce/lista',
					params: {},
					itemdescr: 'accesorio(s)',
					//toolbarURL: '',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							cmAcce.windowNew();
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
						$row.append('<td>'+cmAcce.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.precio)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							cmAcce.windowEdit({id: $(this).data('data')._id.$id,data: $(this).data('data')});
						}).data('estado',data.estado).contextMenu('conMenListEd', {
							onShowMenu: function($r, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($r.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									cmAcce.windowEdit({id: K.tmp.data('id'),data: K.tmp.data('data')});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Accesorio <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('cm/acce/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Accesorio Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											cmAcce.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Accesorio');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Accesorio <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('cm/acce/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Accesorio Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											cmAcce.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Accesorio');
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
		K.Modal({
			id: "windowNewAcce",
			title: "Agregar Accesorio",
			contentURL: "cm/acce/edit",
			width : 400,
			height : 150,
			buttons: {
				"Agregar": function(){
					K.clearNoti();
					var data = {
						nomb: p.$w.find('[name=nomb]').val(),
						precio: p.$w.find('[name=precio]').val()
					};
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre de accesorio!",type:"error"});
					}
					if(data.precio == ""){
						p.$w.find('[name=precio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el precio de accesorio!",type:"error"});
					}
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post("cm/acce/save",data,function(rpta){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Accesorio agregado con &eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						if(p.callback==null){
							cmAcce.init();
						}else{
							p.callback(rpta);
						}
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowNewAcce");
				p.$w.find('[name=precio]').numeric();
			}
		});
	},
	windowEdit: function(p){
		K.Modal({
			id: "windowEdit"+p.id,
			title: "Actualizar Accesorio",
			contentURL: "cm/acce/edit",
			width : 400,
			height : 150,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						nomb: p.$w.find('[name=nomb]').val(),
						precio: p.$w.find('[name=precio]').val()
					};
					if(data.nomb == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el nombre de accesorio!",type:"error"});
					}
					if(data.precio == ""){
						p.$w.find('[name=precio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el precio de accesorio!",type:"error"});
					}
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post("cm/acce/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: "Accesorio actualizado con &eacute;xito!"});
						K.closeWindow("windowEdit"+p.id);
						cmAcce.init();
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow("windowEdit"+p.id);
				}
			},
			onContentLoaded: function(){
				p.$w = $("#windowEdit"+p.id);
				p.$w.find('[name=precio]').numeric();
				p.$w.find('[name=nomb]').val(p.data.nomb);
				p.$w.find('[name=precio]').val(p.data.precio);
			}
		});
	},
	windowDelete: function(p){
		ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Accesorio <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
		function(){
			K.sendingInfo();
			$.post('cm/acce/delete',{id: K.tmp.data('id')},function(){
				K.clearNoti();
				K.notification({title: 'Accesorio Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
				cmAcce.init();
			});
		},function(){
			$.noop();
		},'Eliminaci&oacute;n de Accesorio');
	}
};
define(
	function(){
		return cmAcce;
	}
);