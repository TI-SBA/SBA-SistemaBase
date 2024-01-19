haAlta = {
		categoria: {		
		"1":"S/E",
		"2":"PP",
		"3":"P",
		"4":"A",
		"5":"B",
		"6":"C",
		"7":"E",
		"8":"D",
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'haAlta',
			titleBar: {
				title: 'Dar de Alta a Pacientes'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Apellidos',f:'appat'},{n:'Nombres',f:'nomb'},{n:'N&deg; Historia',f:'roles.paciente.hist_cli'},{n:'Categor&iacute;a',f:'roles.paciente.categoria'}],
					data: 'ha/hosp/lista',
					params: {estado: 'P',alta: true},
					itemdescr: 'hospitalizacion(es)',
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						if(K.session.enti.roles.cajero==null){
							K.notification({
								title: 'Caja no asignada',
								text: 'El usuario no tiene una caja asignada!',
								type: 'error'
							});
						}
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.paciente.appat+' '+data.paciente.apmat+'</td>');
						$row.append('<td>'+data.paciente.nomb+'</td>');
						$row.append('<td>'+data.hist_cli+'</td>');
						var cate = '--';
						if(data.categoria!=null){
							cate = haAlta.categoria[data.categoria];
						}
						$row.append('<td>'+cate+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							hoPend.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).contextMenu("conMenHoHosp", {
							onShowMenu: function($row, menu) {
								$('#conMenHoHosp_edi',menu).remove();
								return menu;
							},
							bindings: {
								'conMenHoHosp_alt': function(t) {
									ciHelper.confirm('&#191;Desea <b>Dar de Alta</b> al Paciente <b>'+K.tmp.find('td:eq(1)').html()+', '+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ha/hosp/save_alta',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Paciente dado de Alta',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											haAlta.init();
										});
									},function(){
										$.noop();
									},'Alta de Paciente');
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
		K.TitleBar({title:'Alta de pacientes'});
		new K.Panel({ 
			title: 'Dar de alta a: ' + p.nom,
			contentURL: 'ha/hosp/edit_alta',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							autorizado: p.$w.find('[name=autorizado] [name=mini_enti]').data('data'),
							fecalt:p.$w.find('[name=fecalta]').val()
						};
						if(data.autorizado!=null){
							data.autorizado = mgEnti.dbRel(data.autorizado);
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ha/hosp/save_alta",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Paciente dado de alta con exito!"});
							K.goBack();
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
				p.$w.find('[name=paciente] .panel-title').html('DATOS DEL PACIENTE');
				p.$w.find('[name=autorizado] .panel-title').html('AUTORIZADO POR');
				p.$w.find('[name=autorizado] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=autorizado] [name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.medico',value: {$exists: true}}
					]});
				});
				p.$w.find('[name=autorizado] [name=btnAct]').click(function(){
					if(p.$w.find('[name=autorizado] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=autorizado] [name=mini_enti]'),data);
						},id: p.$w.find('[name=autorizado] [name=mini_enti]').data('data')._id.$id});
					}
				}).hide();
				p.$w.find('[name=fecalta]').datepicker();
				$.post('ad/hospi/get',{_id: p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente);
					p.$w.find('[name=fecini]').val(moment(data.fecini.sec,"X").format('YYYY-MM-DD'));
				},'json');
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return haAlta;
	}
);