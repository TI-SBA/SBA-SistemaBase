adDoct = {
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
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ad',
			action: 'adDoct',
			titleBar: {
				title: 'Trabajadores'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Nombre',f:'nomb'},'Programa',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'mg/enti/lista',
					params: {rol:'medico'},
					itemdescr: 'doctor(es)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adDoct.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+mgEnti.formatName(data)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							peTrab.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								$('#conMenListEd_hab',menu).remove();
								$('#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									adDoct.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
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
			id: 'windowNewDoct',
			title: 'Nuevo Doctor',
			contentURL: 'ad/doct/edit',
			width: 450,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							colegiatura: p.$w.find('[name=colegiatura]').val(),
							medico: p.$w.find('[name=medico] [name=mini_enti]').data('data'),
						};
						if(data.medico==null){
							p.$w.find('[name=medico] [name=btnSel]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un medico!',
								type: 'error'
							});
						}else data.medico = mgEnti.dbRel(data.medico);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ad/doct/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Medico agregado!"});
							adDoct.init();
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
				p.$w = $('#windowNewDoct');
				p.$w.find('[name=medico] .panel-title').html('DATOS DEL MEDICO');
				p.$w.find('[name=medico] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
					},bootstrap: true});
				});
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowEditDoct',
			title: 'Modificar Doctor',
			contentURL: 'ad/doct/edit',
			width: 450,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							colegiatura: p.$w.find('[name=colegiatura]').val(),
							medico: p.$w.find('[name=medico] [name=mini_enti]').data('data'),
						};
						if(data.medico==null){
							p.$w.find('[name=medico] [name=btnSel]').click();
							return K.msg({
								title: ciHelper.titles.infoReq,
								text: 'Debe seleccionar un medico!',
								type: 'error'
							});
						}else data.medico = mgEnti.dbRel(data.medico);
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ad/doct/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Medico agregado!"});
							adDoct.init();
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
				p.$w = $('#windowEditDoct');
				K.block();
				p.$w.find('[name=medico] .panel-title').html('DATOS DEL PACIENTE');
				p.$w.find('[name=medico] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
					},bootstrap: true});
				});
				p.$w.find('[name=medico] [name=btnAct]').click(function(){
					if(p.$w.find('[name=medico] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe elegir una entidad!',
							type: 'error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=medico] [name=mini_enti]'),data);
						},id: p.$w.find('[name=medico] [name=mini_enti]').data('data')._id.$id});
					}
				});
				p.$w.find('[name=btnSel]').hide();
				$.get('mg/enti/get',{_id:p.id},function(data){
					mgEnti.fillMini(p.$w.find('[name=mini_enti]'),data);
					p.$w.find('[name=colegiatura]').val(data.roles.medico.colegiatura);
					K.unblock();
				},'json');
			}
		});
	}
}
define(
	['mg/enti',],
	function(mgEnti){
		return adDoct;
	}
);