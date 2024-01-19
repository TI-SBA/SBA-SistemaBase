lgSoli = {
	states: {
		P: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
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
		},
		A:{
			descr: "Aprobados",
			color: "#CCCCCC",
			label: '<span class="label label-default">Aprobados</span>'
		}
	},
	contextMenu: {
		onShowMenu: function($row, menu) {
			/*$('#conMenLgCert_rev,#conMenLgCert_fin',menu).remove();
			if($row.data('data').estado!='P'){
				$('#conMenLgCert_edi',menu).remove();
			}*/
			//$('#conMenLgSoli_rec',menu).remove();
			//$('#conMenLgSoli_apr',menu).remove();
			switch($row.data('data').solicitud.estado){
				case "P":
					$('#conMenLgSoli_rec',menu).remove();
					$('#conMenLgSoli_apr',menu).remove();
					$('#conMenLgSoli_gen',menu).remove();
					break;
				case "E":
					$('#conMenLgSoli_edi',menu).remove();
					$('#conMenLgSoli_env',menu).remove();
					$('#conMenLgSoli_apr',menu).remove();
					$('#conMenLgSoli_gen',menu).remove();
					break;
				case "R":
					$('#conMenLgSoli_edi',menu).remove();
					$('#conMenLgSoli_env',menu).remove();
					$('#conMenLgSoli_rec',menu).remove();
					$('#conMenLgSoli_gen',menu).remove();
					break;
				case "A":
					$('#conMenLgSoli_edi',menu).remove();
					$('#conMenLgSoli_env',menu).remove();
					$('#conMenLgSoli_rec',menu).remove();
					$('#conMenLgSoli_apr',menu).remove();
					break;
			}
			return menu;
		},
		bindings: {
			'conMenLgSoli_ver': function(t) {
				lgOrde.windowDetails({id: K.tmp.data('id'), etapa: 'SOL', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgSoli.init_nue();
				}});
			},
			'conMenLgSoli_edi': function(t) {
				lgOrde.windowNew({id: K.tmp.data('id'), etapa: 'SOL', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgSoli.init_nue();
				}});
			},
			'conMenLgSoli_env': function(t) {
				ciHelper.confirm('&#191;Desea enviar la solicitud de credito presupuestario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'solicitud',
						'estado':'E'
					},function(data){
						lgSoli.init_env();	
					},'json');
				},function(){
					$.noop();
				},'Enviar Solicitud');
			},
			'conMenLgSoli_rec': function(t) {
				ciHelper.confirm('&#191;Desea recepcionar la solicitud de credito presupuestario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'solicitud',
						'estado':'R'
					},function(data){
						lgSoli.init_rec();	
					},'json');
				},function(){
					$.noop();
				},'Recepcionar Solicitud');	
			},
			'conMenLgSoli_apr': function(t) {
				ciHelper.confirm('&#191;Desea aprobar la solicitud de credito presupuestario <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
				function(){
					//K.sendingInfo();
					$.post('lg/orde/cambiar_estado',{
						'_id': K.tmp.data('id'),
						'etapa':'solicitud',
						'estado':'A'
					},function(data){
						lgSoli.init_apr();	
					},'json');
				},function(){
					$.noop();
				},'Aprobar Solicitud');	
			},
			'conMenLgSoli_gen': function(t) {
				lgOrde.windowNew({id: K.tmp.data('id'), reg_etapa:'CER', etapa: 'SOL', nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
					lgCert.init_nue();
				}});
			},
		}
	},
	init_nue: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgSoli_nue',
			titleBar: {
				title: 'Solicitud de certificacion presupuestaria: Nuevas'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Cotizacion','Asignacion','Registrado por',{n:'Fecha de registro',f:'fecreg'}],
					data: 'lg/orde/lista',
					params: {estado: 'P',etapa:'SOL'},
					itemdescr: 'solicitud(es) de certificacion(es) presupuestaria(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nueva Solicitud</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							lgOrde.windowNew({reg_etapa:'SOL',etapa:'SOL',goBack: function(){
								lgSoli.init_nue();
							}});
						});
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgSoli.states[data.solicitud.estado].label+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>'+data.cotizacion.cod+'</td>');
						var afectacion = '';
						if(data.solicitud.afectacion!=null){
							if(data.solicitud.afectacion.length>0){
								if(data.solicitud.afectacion.length==1){
									afectacion = data.solicitud.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgSoli", lgSoli.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_env: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgSoli_env',
			titleBar: {
				title: 'Solicitud de certificacion presupuestaria: Enviados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Cotizacion','Asignacion','Enviado por',{n:'Fecha de Envio',f:'fecenv'}],
					data: 'lg/orde/lista',
					params: {estado: 'E',etapa:'SOL'},
					itemdescr: 'solicitud(es) de certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgSoli.states[data.solicitud.estado].label+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>'+data.cotizacion.cod+'</td>');
						var afectacion = '';
						if(data.solicitud.afectacion!=null){
							if(data.solicitud.afectacion.length>0){
								if(data.solicitud.afectacion.length==1){
									afectacion = data.solicitud.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgSoli", lgSoli.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_rec: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgSoli_rec',
			titleBar: {
				title: 'Solicitud de certificacion presupuestaria: Recibidos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Cotizacion','Asignacion','Enviado por',{n:'Fecha de Envio',f:'fecenv'}],
					data: 'lg/orde/lista',
					params: {estado: 'R',etapa:'SOL'},
					itemdescr: 'solicitud(es) de certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgSoli.states[data.solicitud.estado].label+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>'+data.cotizacion.cod+'</td>');
						var afectacion = '';
						if(data.solicitud.afectacion!=null){
							if(data.solicitud.afectacion.length>0){
								if(data.solicitud.afectacion.length==1){
									afectacion = data.solicitud.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgSoli", lgSoli.contextMenu);
						return $row;
					}
				});
			}
		});
	},
	init_apr: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgSoli_apr',
			titleBar: {
				title: 'Solicitud de certificacion presupuestaria: Aprobados'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Estado','Cod.','Cotizacion','Asignacion','Enviado por',{n:'Fecha de Envio',f:'fecenv'}],
					data: 'lg/orde/lista',
					params: {estado: 'A',etapa:'SOL'},
					itemdescr: 'solicitud(es) de certificacion(es) presupuestaria(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+lgSoli.states[data.solicitud.estado].label+'</td>');
						$row.append('<td>'+data.solicitud.cod+'</td>');
						$row.append('<td>'+data.cotizacion.cod+'</td>');
						var afectacion = '';
						if(data.solicitud.afectacion!=null){
							if(data.solicitud.afectacion.length>0){
								if(data.solicitud.afectacion.length==1){
									afectacion = data.solicitud.afectacion[0].programa.nomb;
								}else{
									afectacion = 'VARIOS';
								}
							}
						}
						$row.append('<td>'+afectacion+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						$row.append('<td>'+moment(data.solicitud.fecreg.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenLgSoli", lgSoli.contextMenu);
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
		return lgSoli;
	}
);