hoHosp = {
	states: {
		A: {
			descr: "Alta",
			color: "green",
			label: '<span class="label label-success">Paciente dado de Alta</span>'
		},
		P:{
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-default">Pendiente</span>'
		}
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'hoHosp',
			titleBar: {
				title: 'Hospitalizaci&oacute;n'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Apellidos',f:'appat'},{n:'Nombres',f:'nomb'},{n:'N&deg; Historia',f:'roles.paciente.hist_cli'},{n:'Fec. Inicial',f:'roles.paciente.fecini'},{n:'Fec. Final',f:'roles.paciente.fecfin'},'Importe Tarifa',{n:'Categor&iacute;a',f:'roles.paciente.categoria'}],
					data: 'ho/hosp/lista',
					params: {modulo: 'MH'},
					itemdescr: 'hospitalizacion(es)',
					toolbarHTML: '&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
						'</select>',
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
						
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+hoHosp.states[data.estado].label+'</td>');
						$row.append('<td>'+data.paciente.appat+' '+data.paciente.apmat+'</td>');
						$row.append('<td>'+data.paciente.nomb+'</td>');
						$row.append('<td>'+data.hist_cli+'</td>');
						$row.append('<td>'+data.categoria+'</td>');
						if(data.fecini!=null){
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecini)+'</td>');
							$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
							$row.append('<td>'+ciHelper.formatMon(data.importe)+'</td>');
						}else{
							$row.append('<td>');
							$row.append('<td>');
							$row.append('<td>');
						}
						
						var tipo = 'P';
						if(data.recibo!=null) tipo = 'R';
						$row.data('id',data._id.$id).dblclick(function(){
							hoPend.windowEdit({id: $(this).closest('.item').data('id'),nomb: $(this).closest('.item').find('td:eq(2)').html(),goBack: function(){
								hoPend.init();
							}});
						}).data('tipo',tipo).data('data',data).contextMenu("conMenHoHosp", {
							onShowMenu: function($row, menu) {
								if($row.data('data').estado!='P')
									$('#conMenHoHosp_eli',menu).remove();
								$('#conMenHoHosp_alt',menu).remove();
								return menu;
							},
							bindings: {
								'conMenHoHosp_edi': function(t) {
									if(K.tmp.data('tipo')=='R'){
										return K.notification({
											title: 'Recibo Emitido',
											text: 'El Paciente no puede ser modificado porque ya se emiti&oacute; un recibo!',
											type: 'error'
										});
									}else{
										hoPend.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
											hoPend.init();
										}});
									}
								},
								'conMenHoHosp_eli': function(){
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Registro de <b>'+mgEnti.formatName(K.tmp.data('data'))+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ho/hosp/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.msg({title: 'Registro Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											hoHosp.init();
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
	}
};
define(
	['mg/enti','mh/paci'],
	function(mgEnti, mhPaci){
		return hoHosp;
	}
);