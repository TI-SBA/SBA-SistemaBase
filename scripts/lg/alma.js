lgAlma = {
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
	aplicaciones: [
		{cod: 'LG',descr: 'LOGISTICA'},
		{cod: 'FA',descr: 'FARMACIA'},
		{cod: 'AG',descr: 'VENTA DE AGUA'},
		{cod: 'US',descr: 'UNIDAD DE SERVICIOS ALIMENTARIOS'},
		{cod: 'BJ',descr: 'BALNEARIO DE JESUS'},
	],
	dbRel: function(item){
		var rpta = {
			_id: item._id.$id,
			nomb: item.nomb
		};
		if(item.local!=null){
			rpta.local = {
				_id: item.local._id,
				direccion: item.local.direccion,
				descr: item.local.descr
			}
			if(item.local._id.$id!=null)
				rpta.local._id = item.local._id.$id;
		}
		return rpta;
	},
	get_aplicaciones: function(app){
		for(var i=0; i<lgAlma.aplicaciones.length; i++){
			if(lgAlma.aplicaciones[i].cod==app){
				return lgAlma.aplicaciones[i].descr;
			}
		}
		return false;
	},
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgAlma',
			titleBar: {
				title: 'Almacenes'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Aplicaci&oacute;n','Descr.','Local',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/alma/lista',
					params: {},
					itemdescr: 'almacen(es)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgAlma.windowNew();
						});
					},
					//onLoading: function(){ K.block(); },
					//onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgAlma.states[data.estado].label+'</td>');
						$row.append('<td>'+lgAlma.get_aplicaciones(data.aplicacion)+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						$row.append('<td>'+data.local.descr+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							lgAlma.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									lgAlma.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									lgAlma.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Almacen <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/alma/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Almacen Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgAlma.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Almacen');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> elAlmacen <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/alma/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Almacen Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgAlma.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Almacen');
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
			title: 'Nuevo Almacen',
			contentURL: 'lg/alma/edit',
			width: 550,
			height: 260,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							aplicacion: p.$w.find('[name=aplicacion]').val(),
							local: p.$w.find('[name=local]').data('data')
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la descripcion!',type: 'error'});
						}
						if(data.local==null){
							p.$w.find('[name=btnLocal]').click();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar el local!',type: 'error'});
						}else data.local = {
							_id: data.local._id.$id,
							direccion: data.local.direccion,
							descr: data.local.descr
						};
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/alma/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Tipo agregado!"});
							lgAlma.init();
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
				p.$w.find('[name=btnLocal]').click(function(){
					mgTitu.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					},bootstrap: true});
				});
				var $cbo = p.$w.find('[name=aplicacion]');
				for(var i=0,j=lgAlma.aplicaciones.length; i<j; i++){
					$cbo.append('<option value="'+lgAlma.aplicaciones[i].cod+'">'+lgAlma.aplicaciones[i].descr+'</option>');
				}
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Almacen '+p.nomb,
			contentURL: 'lg/alma/edit',
			width: 550,
			height: 260,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							aplicacion: p.$w.find('[name=aplicacion]').val(),
							descr: p.$w.find('[name=descr]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nombre!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la descripcion!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("lg/alma/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Tipo actualizado!"});
							lgAlma.init();
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
				var $cbo = p.$w.find('[name=aplicacion]');
				for(var i=0,j=lgAlma.aplicaciones.length; i<j; i++){
					$cbo.append('<option value="'+lgAlma.aplicaciones[i].cod+'">'+lgAlma.aplicaciones[i].descr+'</option>');
				}
				$.post('lg/alma/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=local]').html(data.local.descr)
						.data('data',data.local);
					if(data.aplicacion!=null)
						p.$w.find('[name=aplicacion]').val(data.aplicacion);
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
			title: 'Seleccionar Almacen',
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
					data: 'lg/alma/lista',
					params: {},
					itemdescr: 'almacen(es)',
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
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return lgAlma;
	}
);