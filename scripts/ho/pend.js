hoPend = {
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'hoPend',
			titleBar: {
				title: 'Hospitalizaciones Pendientes'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Apellidos',f:'appat'},{n:'Nombres',f:'nomb'},{n:'N&deg; Historia',f:'roles.paciente.hist_cli'},{n:'Categor&iacute;a',f:'roles.paciente.categoria'},'&uacute;ltima Fecha pagada'],
					data: 'ho/hosp/lista',
					params: {estado: 'P',pend: true,modulo:"MH"},
					itemdescr: 'hospitalizacion(es)',
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-refresh"></i> Sincronizar Pacientes</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							return K.msg({text: 'El sistema no se encuentra disponible por el momento!'});
							K.block();
							$.post('skm_mh/listener.php',function(){
								$grid.reinit();
								K.unblock();
							});
						}).remove();
					},
					onLoading: function(){ 
						K.block();
					},
					onComplete: function(){
						if(K.session.enti.roles.cajero==null){
							K.notification({
								title: 'Caja no asignada',
								text: 'El usuario no tiene una caja asignada!',
								type: 'error'
							});
						}
						K.unblock();
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.paciente.appat+' '+data.paciente.apmat+'</td>');
						$row.append('<td>'+data.paciente.nomb+'</td>');
						$row.append('<td>'+data.hist_cli+'</td>');
						$row.append('<td>'+mhPaci.categoria[data.categoria]+'</td>');
						if(data.recibo!=null)
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.recibo.fecfin)+'</td>');
						else
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecini)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							hoPend.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('data',data).contextMenu("conMenHoHosp", {
							onShowMenu: function($row, menu) {
								//$('#conMenHoHosp_alt',menu).remove();
								return menu;
							},
							bindings: {
								'conMenHoHosp_edi': function(t) {
									if(K.session.enti.roles.cajero==null){
										K.notification({
											title: 'Caja no asignada',
											text: 'El usuario no tiene una caja asignada!',
											type: 'error'
										});
									}else{
										hoPend.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
											hoPend.init();
										}});
									}
								},
								'conMenHoHosp_alt': function(t) {
									hoAlta.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										hoPend.init();
									}});
								},
								'conMenHoHosp_eli': function(){
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Registro de <b>'+mgEnti.formatName(K.tmp.data('data'))+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ho/hosp/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.msg({title: 'Registro Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											hoPend.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Registro');
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowEdit: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		K.TitleBar({title:'Datos de Paciente'});
		new K.Panel({
			contentURL: 'ho/hosp/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							estado: 'P',
							paciente: mgEnti.dbRel(p.data.paciente),
							hist_cli: p.data.hist_cli,
							categoria: p.data.categoria,
							modalidad: p.$w.find('[name=modalidad] option:selected').val(),
							cant: p.$w.find('[name=cant]').val(),
							tipo_hosp: p.$w.find('[name=tipo_hosp] option:selected').val(),
							fecini: p.$w.find('[name=fecini]').val(),
							fecfin: p.$w.find('[name=fecfin]').val(),
							//fecalt: p.$w.find('[name=fecalt]').val(),
							importe: p.$w.find('[name=importe]').html(),
							fecpag: p.$w.find('[name=fecpag]').val(),
							num: p.$w.find('[name=num]').val(),
						};
						if(data.cant==''){
							p.$w.find('[name=cant]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la cantidad!',type: 'error'});
						}
						if(data.fecini==''){
							p.$w.find('[name=fecini]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de inicio!',type: 'error'});
						}
						if(data.fecpag==''){
							p.$w.find('[name=fecpag]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de emisi&oacute;n del Recibo!',type: 'error'});
						}
						if(data.num==''){
							p.$w.find('[name=num]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de Recibo!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ho/hosp/save_rec",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Tipo actualizado!"});
							K.goBack();
							//window.open('cj/comp/print_reci?id='+result.recibo);
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=cant]');
				p.$w.find('[name=num]');
				p.$w.find('[name^=fec]').val(K.date());
				p.$w.find('[name^=fec]').datepicker();
				p.$w.find('[name=modalidad]').change(function(){
					p.$w.find('[name=cant]').change();
				});
				p.$w.find('[name=tipo_hosp]').change(function(){
					p.$w.find('[name=cant]').change();
				});
				p.$w.find('[name=cant]').keyup(function(){
					$(this).change();
				}).change(function(){
					var val = $(this).val(),

					cat = p.data.categoria,
					//cat = p.$w.find('[name=categoria]').text();
					tipo_hosp = p.$w.find('[name=tipo_hosp] option:selected').val(),
					tipo = 0;
					if(val!=''){
						val = parseFloat(val);
						console.log(val);
						for(var i=0,j=p.tarifas.length; i<j; i++){
							if(cat==p.tarifas[i].categoria&&tipo_hosp==p.tarifas[i].tipo_hosp){
								p.tmp = p.tarifas[i];
								if(p.$w.find('[name=modalidad] option:selected').val()=='M')
									tipo = parseFloat(p.tmp.mensual);
								else
									tipo = parseFloat(p.tmp.diario);
								break;
							}
						}
						p.importe = val*tipo;
						p.$w.find('[name=importe]').html(K.round(val*tipo,2));
					}
				});
				$.post('ho/hosp/get',{_id: p.id,tarifa: true,recibo: true},function(data){
					p.data = data;
					if(data.categoria==null || data.categoria==''){
						if(data.ficha_social!=null){
							p.data.categoria = data.ficha_social.categoria;
						}
					}
					p.tarifas = data.tarifas;
					p.$w.find('[name=paciente]').html(mgEnti.formatName(data.paciente));
					p.$w.find('[name=hist_cli]').html(data.hist_cli);
					var categoria = 'A';
					if(mhPaci.categoria[p.data.categoria]!=null){
						categoria = mhPaci.categoria[p.data.categoria];
					}
					p.$w.find('[name=categoria]').html(categoria);
					p.$w.find('[name=num]').val(data.recibo);
					p.$w.find('[name=tipo_hosp]').val(data.tipo_hosp);
					/*if(data.recibos!=null){
						p.$w.find('[name=fecini]').val(ciHelper.date.format.bd_ymd(data.recibos[data.recibos.length-1].fecfin));
					}else{
						p.$w.find('[name=fecini]').val(ciHelper.date.format.bd_ymd(data.fecini));
					}*/
					if(data.fecini!=null){
						if(data.fecini.sec!=null){
							p.$w.find('[name=fecini]').val(ciHelper.date.format.bd_ymd(data.fecini));
						}
						/*if(data.fecini!=""){
							p.$w.find('[name=fecini]').val(ciHelper.date.format.bd_ymd(data.fecini));
						}*/
					}

					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['mg/enti','mh/paci','ho/alta'],
	function(mgEnti, mhPaci,hoAlta){
		return hoPend;
	}
);