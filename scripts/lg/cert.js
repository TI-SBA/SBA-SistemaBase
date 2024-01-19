lgCert = {
	states: {
		P: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		A: {
			descr: "Aprobado",
			color: "green",
			label: '<span class="label label-success">Aprobado</span>'
		},
		E:{
			descr: "Enviado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Enviado</span>'
		},
		R:{
			descr: "Recepcionado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Recepcionado</span>'
		}
	},
	contextMenu: {
		onShowMenu: function($row, menu) {
			switch($row.data('data').certificacion.estado){
				case "P":
					$('#conMenLgCert_env',menu).remove();
					$('#conMenLgCert_rec',menu).remove();
					$('#conMenLgCert_ord',menu).remove();
					$('#conMenLgCert_ors',menu).remove();
					break;
				case "A":
					$('#conMenLgCert_edi',menu).remove();
					$('#conMenLgCert_apr',menu).remove();
					//$('#conMenLgCert_env',menu).remove();
					$('#conMenLgCert_rec',menu).remove();
					$('#conMenLgCert_ord',menu).remove();
					$('#conMenLgCert_ors',menu).remove();
					break;
				case "E":
					$('#conMenLgCert_edi',menu).remove();
					$('#conMenLgCert_apr',menu).remove();
					$('#conMenLgCert_env',menu).remove();
					//$('#conMenLgCert_rec',menu).remove();
					$('#conMenLgCert_ord',menu).remove();
					$('#conMenLgCert_ors',menu).remove();
					break;
				case "R":
					$('#conMenLgCert_edi',menu).remove();
					$('#conMenLgCert_apr',menu).remove();
					$('#conMenLgCert_env',menu).remove();
					$('#conMenLgCert_rec',menu).remove();
					break;
			}
			console.log($row.data('data'));
			if($row.data('data').orden!=null){
				$('#conMenLgCert_ord',menu).remove();
			}
			if($row.data('data').orden_servicio!=null){
				$('#conMenLgCert_ors',menu).remove();
			}
			return menu;
		},
		bindings: {
			'conMenLgCert_ver': function(t) {
				lgOrde.windowDetails({id: K.tmp.data('id'), etapa: 'CER', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgCert.init_nue();
				}});
			},
			'conMenLgCert_edi': function(t) {
				lgOrde.windowNew({id: K.tmp.data('id'), etapa: 'CER', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgCert.init_nue();
				}});
			},
			'conMenLgCert_env': function(t) {
				ciHelper.confirm('&#191;Desea enviar la certificacion de credito presupuestario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'certificacion',
						'estado':'E'
					},function(data){
						lgCert.init_env();	
					},'json');
				},function(){
					$.noop();
				},'Enviar Certificacion');
			},
			'conMenLgCert_rec': function(t) {
				ciHelper.confirm('&#191;Desea recepcionar la certificacion de credito presupuestario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'certificacion',
						'estado':'R'
					},function(data){
						lgCert.init_rec();
					},'json');
				},function(){
					$.noop();
				},'Certificacion Certificacion');
			},
			'conMenLgCert_apr': function(t) {
				ciHelper.confirm('&#191;Desea aprobar la certificacion de credito presupuestario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'certificacion',
						'estado':'A'
					},function(data){
						lgCert.init_apr();
					},'json');
				},function(){
					$.noop();
				},'Aprobar Certificacion');
			},
			'conMenLgCert_ord': function(t) {
				lgOrde.windowNew({id: K.tmp.data('id'), reg_etapa:'ORD', etapa: 'CER', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgOrde.init_nue();
				}});
			},
			'conMenLgCert_ors': function(t) {
				lgOrse.windowNew({id: K.tmp.data('id'), reg_etapa:'ORS', etapa: 'CER', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgOrse.init_nue();
				}});
			},
			'conMenLgCert_imp': function(t) {
				window.open('lg/repo/cert?id='+K.tmp.data('id'));
			}
		}
	},
	init_nue: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCert_nue',
			titleBar: {
				title: 'Certificaci&oacute;n Presupuestaria: Nuevas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Solicitud','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'P',etapa:'CER'},
					itemdescr: 'certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCert.states[data.certificacion.estado].label+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>--</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgCert", lgCert.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_apr: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCert_apr',
			titleBar: {
				title: 'Certificaci&oacute;n Presupuestaria: Aprobados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Solicitud','Asignacion','Aprobado por',{n:'Fecha de aprobaci&oacute;n',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'A',etapa:'CER'},
					itemdescr: 'certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCert.states[data.certificacion.estado].label+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>--</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgCert", lgCert.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_env: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCert_env',
			titleBar: {
				title: 'Certificaci&oacute;n Presupuestaria: Enviados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Solicitud','Asignacion','Aprobado por',{n:'Fecha de aprobaci&oacute;n',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'E',etapa:'CER'},
					itemdescr: 'certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCert.states[data.certificacion.estado].label+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>--</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgCert", lgCert.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_rec: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCert_rec',
			titleBar: {
				title: 'Certificaci&oacute;n Presupuestaria: Recepcionados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Solicitud','Asignacion','Aprobado por',{n:'Fecha de aprobaci&oacute;n',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'R',etapa:'CER'},
					itemdescr: 'certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCert.states[data.certificacion.estado].label+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>--</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgCert", lgCert.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Certificacion presupuestal',
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
					cols: ['','Estado','Cod.','Solicitud','Asignacion','Aprobado por',{n:'Fecha de recepci&oacute;n',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'R',etapa:'CER'},
					itemdescr: 'orden(es) de compra',
					toolbarHTML: '',
					onContentLoaded: function($el){
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgCert.states[data.certificacion.estado].label+'</td>');
						$row.append('<td>'+data.certificacion.cod+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>--</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.certificacion.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgCert", lgCert.contextMenu);
						return $row;
					}
				});
			}
		});
	}
};
define(
	['lg/orde','mg/enti'],
	function(lgOrde, mgEnti){
		return lgCert;
	}
);