hoTari = {
	tipo_hosp: {
		S: 'S/E',
		C: 'Completa',
		P: 'Parcial'
	},
	categoria: {
		10:'NUEVO',
		11: 'CONTINUADOR',
		8: 'INDIGENTE',
		9: 'PRIVADO',
		12: 'CATEGORIA B',
		13: 'CATEGORIA C',
		14: 'CATEGORIA A'
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'hoTari',
			titleBar: {
				title: 'Tarifa de Hospitalizaciones'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Tipo de Hospitalizaci&oacute;n',f:'tipo_hosp'},{n:'Categor&iacute;a',f:'categoria'},'Mensual','Diario',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'ho/tari/lista',
					params: {tipo: 'HO'},
					itemdescr: 'tarifa(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					pagination: false,
					load: function(data_all,$tbody){
						if(data_all.items!=null){
							for(var i=0; i<data_all.items.length; i++){
								var data = data_all.items[i],
								$row = $('<tr class="item">');
								$row.append('<td>');
								$row.append('<td>'+hoTari.tipo_hosp[data.tipo_hosp]+'</td>');
								$row.append('<td>'+hoTari.categoria[data.categoria]+'</td>');
								$row.append('<td>'+ciHelper.formatMon(data.mensual)+'</td>');
								$row.append('<td>'+ciHelper.formatMon(data.diario)+'</td>');
								$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
								$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
								$row.data('id',data._id.$id).dblclick(function(){
									hoTari.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
								}).data('estado',data.estado).contextMenu("conMenListEd", {
									onShowMenu: function($row, menu) {
										$('#conMenListEd_ver',menu).remove();
										if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
										else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
										return menu;
									},
									bindings: {
										'conMenListEd_ver': function(t) {
											hoTari.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
										},
										'conMenListEd_edi': function(t) {
											hoTari.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
										},
										'conMenListEd_hab': function(t) {
											ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('ho/tari/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
													K.clearNoti();
													K.notification({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													hoTari.init();
												});
											},function(){
												$.noop();
											},'Habilitaci&oacute;n de Tipo de Local');
										},
										'conMenListEd_des': function(t) {
											ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('ho/tari/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
													K.clearNoti();
													K.notification({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													hoTari.init();
												});
											},function(){
												$.noop();
											},'Deshabilitaci&oacute;n de Tipo de Local');
										}
									}
								});
								$tbody.append($row);
							}
						}
					}
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditTipo',
			title: 'Editar Tipo '+p.nomb,
			contentURL: 'ho/tari/edit',
			width: 450,
			height: 250,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							tipo_hosp: p.$w.find('[name=tipo_hosp] option:selected').val(),
							categoria: p.$w.find('[name=categoria] option:selected').val(),
							mensual: p.$w.find('[name=mensual]').val(),
							diario: p.$w.find('[name=diario]').val()
						};
						if(data.mensual==''){
							p.$w.find('[name=mensual]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el costo mensual!',type: 'error'});
						}
						if(data.diario==''){
							p.$w.find('[name=diario]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el costo diario!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ho/tari/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Tarifa actualizada!"});
							hoTari.init();
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
				//p.$w.find('[name=tipo_hosp]').attr('disabled','disabled');
				//p.$w.find('[name=categoria]').attr('disabled','disabled');
				p.$w.find('[name=mensual]').numeric();
				p.$w.find('[name=diario]').numeric();
				$.post('ho/tari/get',{_id: p.id},function(data){
					p.$w.find('[name=tipo_hosp]').selectVal(data.tipo_hosp);
					p.$w.find('[name=categoria]').selectVal(data.categoria);
					p.$w.find('[name=mensual]').val(data.mensual);
					p.$w.find('[name=diario]').val(data.diario);
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return hoTari;
	}
);