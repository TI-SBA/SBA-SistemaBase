ddDepu = {
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
			nomb: item.nomb,
			//ndoc: item.ndoc,
			desc: item.desc,
			dire: item.dire,
			id_dire: item.id_dire,
			ofic: item.ofic,
			id_ofic: item.id_ofic,
			docu: item.docu,
			id_docu: item.id_docu,
			metr: item.metr,
			casa: item.casa,
			femi: item.femi
		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddDepu',
			titleBar: {
				title: 'Documentos Depurados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nombre','Direccion','Oficina','Tipo de Documento','Metraje','Cajas','Fecha de Depuracion'],
					data: 'dd/depu/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddDepu.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						//$row.append('<td>'+ddDepu.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.dire+'</td>');
						$row.append('<td>'+data.ofic+'</td>');
						$row.append('<td>'+data.docu+'</td>');
						$row.append('<td>'+data.metr+'</td>');
						$row.append('<td>'+data.casa+'</td>');
						$row.append('<td>'+moment(data.femi.sec,'X').format('DD/MM/YYYY')+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddDepu.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenAreas", {
							/*onShowMenu: function($row, menu) {
								$('#conMenAreas_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenAreas_hab',menu).remove();
								else $('#conMenAreas_edi,#conMenAreas_des',menu).remove();
								return menu;
							},
							*/
							bindings: {
								'conMenAreas_edi': function(t) {
									ddDepu.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenAreas_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Depuracion de Documento:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('dd/depu/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Depuracion de Documento Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											ddDepu.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Depuracion de Documento');
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
			id: 'windowNewDepuracion',
			title: 'Nueva Depuracion de Documento',
			contentURL: 'dd/depu/edit',
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
							nomb: p.$w.find('[name=nomb]').val(),
							//ndoc: p.$w.find('[name=ndoc]').val(),
							desc: p.$w.find('[name=desc]').val(),
							dire: p.$w.find('[name=dire]').text(),
							id_dire: p.$w.find('[name=id_dire]').text(),
							ofic: p.$w.find('[name=ofic]').text(),
							id_ofic: p.$w.find('[name=id_ofic]').text(),
							docu: p.$w.find('[name=docu]').text(),
							id_docu: p.$w.find('[name=id_docu]').text(),
							metr: p.$w.find('[name=metr]').val(),
							casa: p.$w.find('[name=casa]').val(),
							femi: p.$w.find('[name=femi]').val(),
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nomblo!',type: 'error'});
						}
						if(data.metr==''){
							p.$w.find('[name=metr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Metraje!',type: 'error'});
						}
						if(data.casa==''){
							p.$w.find('[name=casa]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar las cajas o sacos!',type: 'error'});
						}
												
						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Direccion!',type: 'error'});
						}

						if(data.ofic==''){
							p.$w.find('[name=ofic]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar La Oficina!',type: 'error'});
						}
						if(data.docu==''){
							p.$w.find('[name=docu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Tipo de Documento!',type: 'error'});
						}
						if(data.femi==''){
							p.$w.find('[name=femi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Fecha!',type: 'error'});
						}

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/depu/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Depuracion de Documento agregado!"});
							ddDepu.init();
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
				p.$w = $('#windowNewDepuracion');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			startDate: '-3d'
				});	
					p.$w.find('[name=btnOfic]').click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofic]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_ofic]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnTido]").click(function(){
					ddTido.windowSelect({callback: function(data){
						p.$w.find('[name=docu]').html(data.docu).data('data',data);
						p.$w.find('[name=id_docu]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=dire]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_dire]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});

			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditDepuracion',
			title: 'Editar Depuracion de Documento: '+p.nomb,
			contentURL: 'dd/depu/edit',
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
							nomb: p.$w.find('[name=nomb]').val(),
							//ndoc: p.$w.find('[name=ndoc]').val(),
							desc: p.$w.find('[name=desc]').val(),
							dire: p.$w.find('[name=dire]').text(),
							id_dire: p.$w.find('[name=id_dire]').text(),
							ofic: p.$w.find('[name=ofic]').text(),
							id_ofic: p.$w.find('[name=id_ofic]').text(),
							docu: p.$w.find('[name=docu]').text(),
							id_docu: p.$w.find('[name=id_docu]').text(),
							metr: p.$w.find('[name=metr]').val(),
							casa: p.$w.find('[name=casa]').val(),
							femi: p.$w.find('[name=femi]').val(),
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el nomblo!',type: 'error'});
						}
						if(data.metr==''){
							p.$w.find('[name=metr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Metraje!',type: 'error'});
						}
						if(data.casa==''){
							p.$w.find('[name=casa]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar las cajas o sacos!',type: 'error'});
						}
												
						if(data.dire==''){
							p.$w.find('[name=dire]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Direccion!',type: 'error'});
						}

						if(data.ofic==''){
							p.$w.find('[name=ofic]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar La Oficina!',type: 'error'});
						}
						if(data.docu==''){
							p.$w.find('[name=docu]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Tipo de Documento!',type: 'error'});
						}
						if(data.femi==''){
							p.$w.find('[name=femi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Fecha!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("dd/depu/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Depuracion de Documento actualizado!"});
							ddDepu.init();
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
				p.$w = $('#windowEditDepuracion');
				p.$w.find("[name=femi]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			startDate: '-3d'
				});	
				K.block();
				p.$w.find('[name=btnOfic]').click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofic]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_ofic]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnTido]").click(function(){
					ddTido.windowSelect({callback: function(data){
						p.$w.find('[name=docu]').html(data.docu).data('data',data);
						p.$w.find('[name=id_docu]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=dire]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_dire]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				$.post('dd/depu/get',{_id: p.id},function(data){
					
				p.$w.find('[name=nomb]').val(data.nomb);
				//p.$w.find('[name=ndoc]').val(data.ndoc);
				p.$w.find('[name=desc]').val(data.desc);
				p.$w.find('[name=dire]').text(data.dire);
				p.$w.find('[name=id_dire]').text(data.id_dire);
				p.$w.find('[name=ofic]').text(data.ofic);
				p.$w.find('[name=id_ofic]').text(data.id_ofic);
				p.$w.find('[name=docu]').text(data.docu);
				p.$w.find('[name=id_docu]').text(data.id_docu);
				p.$w.find('[name=femi]').val(moment(data.femi.sec,'X').format('DD/MM/YYYY'));
				p.$w.find('[name=casa]').val(data.casa);
				p.$w.find('[name=metr]').val(data.metr);
				K.unblock();
				},'json');
			}
		});
	},
};
define(
	['mg/enti','mg/ofic','dd/tido','dd/dire','mg/prog'],
	function(mgEnti,mgOfic,ddTido,ddDire,mgProg){
		return ddDepu;
	}
);