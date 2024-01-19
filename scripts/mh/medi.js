mhMedi = {
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
	dbRel: function(item){
		return {
			_id: item._id.$id,
			paci: item.paci,
			diag: item.diag,
			tra: item.tra,
			inter: item.inter,
			agres: item.agre,
			visi: item.visi,
			camb: item.camb,
			rein: item.rein,
			peso: item.peso,
			talla: item.talla,
			temp: item.temp,
			OD: item.OD,
			ID: item.ID,
			LO: item.LO,
			esc: item.esc,
			obs: item.obs,
			nomb: item.nomb,
			diagn: item.diagn,
			nom: item.nom,
			diagno: item.diagno,
			indi: item.indi
		};
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhMedi',
			titleBar: {
				title: 'Ficha Medica'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Paciente','Estado-Peso','Diagnostico','Responde a Tratamiento','Capacidad de Inter-relacionarse','Presenta Agresividad','Recive Visita','Presenta Cambios','Puede Reinsertarse','Ultima Modificacion'],
					data: 'mh/medi/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> <button name="btnInforme" class="btn btn-success"><i class="fa fa-plus"></i> INFORME</button>  ',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhMedi.windowNew();
						});
						$el.find('[name=btnInforme]').click(function(){
							
						});

						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.paci+'</td>');
						$row.append('<td>'+data.peso+'</td>');
						$row.append('<td>'+data.diag+'</td>');
						$row.append('<td>'+data.tra+'</td>');
						$row.append('<td>'+data.inter+'</td>');
						$row.append('<td>'+data.agres+'</td>');
						$row.append('<td>'+data.visi+'</td>');
						$row.append('<td>'+data.camb+'</td>');
						$row.append('<td>'+data.rein+'</td>');
						

						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"mh/medi/print?_id="+$(this).data('id')
							});
						}).data('estado',data.estado).contextMenu("conMenFMedica", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFMedica_info': function(t) {
									mhMedi.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									mhMedi.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Medica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/medi/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'mediente Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhMedi.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Medica');
								},
								'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Reporte de Ficha Medica",
										url:"mh/medi/get_reporte?_id="+K.tmp.data('id')
									});
								},
								'conMenListEd_edi':function(t){
									K.incomplete();
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
			id: 'windowNewFichaMedica',
			title: 'Nueva Ficha Medica',
			contentURL: 'mh/medi/edit',
			width: 900,
			height: 900,
			store:false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								K.sendingInfo();
								var data = {


								paci:p.$w.find('[name=paci]').text(),
								diag:p.$w.find('[name=diag]').val(),
								tra:p.$w.find('[name=tra]').val(),
								inter:p.$w.find('[name=inter]').val(),
								agres:p.$w.find('[name=agres]').val(),
								visi:p.$w.find('[name=visi]').val(),
								camb:p.$w.find('[name=camb]').val(),
								rein:p.$w.find('[name=rein]').val(),
								peso:p.$w.find('[name=peso]').val(),
								talla:p.$w.find('[name=talla]').val(),
								temp:p.$w.find('[name=temp]').val(),
								OD:p.$w.find('[name=OD]').val(),
								ID:p.$w.find('[name=ID]').val(),
								LO:p.$w.find('[name=LO]').val(),
								esc:p.$w.find('[name=esc]').val(),
								obs:p.$w.find('[name=obs]').val(),
								nomb:p.$w.find('[name=nomb]').val(),
								diagn:p.$w.find('[name=diagn]').val(),
								nom:p.$w.find('[name=nom]').val(),
								diagno:p.$w.find('[name=diagno]').val(),
								indi:p.$w.find('[name=indi]').val(),
								
								};
											
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/medi/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Medica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									mhMedi.init();
									
								},'json');	
							}
						}).submit();
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
				p.$w = $('#windowNewFichaMedica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(data.ape + ',' + data.nom).data('data',data);
					},bootstrap: true});
				});
				$.post("mh/medi/get_reporte",function(data){



				},'json');
			}
		});
	},




	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditFichaMedica',
			title: 'Editar Ficha Medica: ' + p.nom,
			contentURL: 'mh/medi/edit',
			width: 900,
			height: 900,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL mediENTE
							_id: p.id,
							
								paci:p.$w.find('[name=paci]').text(),
								diag:p.$w.find('[name=diag]').val(),
								tra:p.$w.find('[name=tra]').val(),
								inter:p.$w.find('[name=inter]').val(),
								agres:p.$w.find('[name=agres]').val(),
								visi:p.$w.find('[name=visi]').val(),
								camb:p.$w.find('[name=camb]').val(),
								rein:p.$w.find('[name=rein]').val(),
								peso:p.$w.find('[name=peso]').val(),
								talla:p.$w.find('[name=talla]').val(),
								temp:p.$w.find('[name=temp]').val(),
								OD:p.$w.find('[name=OD]').val(),
								ID:p.$w.find('[name=ID]').val(),
								LO:p.$w.find('[name=LO]').val(),
								esc:p.$w.find('[name=esc]').val(),
								obs:p.$w.find('[name=obs]').val(),
								nomb:p.$w.find('[name=nomb]').val(),
								diagn:p.$w.find('[name=diagn]').val(),
								nom:p.$w.find('[name=nom]').val(),
								diagno:p.$w.find('[name=diagno]').val(),
								indi:p.$w.find('[name=indi]').val(),

						};
						if(data.paci==''){
							p.$w.find('[name=paci]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.diag==''){
							p.$w.find('[name=diag]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.tra==''){
							p.$w.find('[name=tra]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.agres==''){
							p.$w.find('[name=agres]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.visi==''){
							p.$w.find('[name=visi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.camb==''){
							p.$w.find('[name=camb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.rein==''){	
							p.$w.find('[name=rein]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.peso==''){
							p.$w.find('[name=peso]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.talla==''){
							p.$w.find('[name=talla]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.temp==''){
							p.$w.find('[name=temp]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.OD==''){
							p.$w.find('[name=OD]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.ID==''){
							p.$w.find('[name=ID]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.LO==''){
							p.$w.find('[name=LO]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.esc==''){
							p.$w.find('[name=esc]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.obs==''){
							p.$w.find('[name=obs]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.diagn==''){
							p.$w.find('[name=diagn]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.nom==''){
							p.$w.find('[name=nom]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.diagno==''){
							p.$w.find('[name=diagno]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}
						if(data.indi==''){
							p.$w.find('[name=indi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la historia Clinica del mediente!',type: 'error'});
						}


						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/medi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Ficha Medica Actualizada!"});
							mhMedi.init();
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
				p.$w = $('#windowEditFichaMedica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(data.ape + ',' + data.nom).data('data',data);
					},bootstrap: true});
				});
				K.block();
				
				     $.post('mh/medi/get',{_id: p.id},function(data){
				    p.$w.find('[name=id]').val(data.id);
					p.$w.find('[name=paci]').text(data.paci);
					p.$w.find('[name=diag]').val(data.diag);
					p.$w.find('[name=tra]').val(data.tra);
					p.$w.find('[name=inter]').val(data.inter);
					p.$w.find('[name=agres]').val(data.agres);
					p.$w.find('[name=visi]').val(data.visi);
					p.$w.find('[name=camb]').val(data.camb);
					p.$w.find('[name=rein]').val(data.rein);
					p.$w.find('[name=peso]').val(data.peso);
					p.$w.find('[name=talla]').val(data.talla);
					p.$w.find('[name=temp]').val(data.temp);
					p.$w.find('[name=OD]').val(data.OD);
					p.$w.find('[name=ID]').val(data.ID);
					p.$w.find('[name=LO]').val(data.LO);
					p.$w.find('[name=esc]').val(data.esc);
					p.$w.find('[name=obs]').val(data.obs);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=diagn]').val(data.diagn);
					p.$w.find('[name=nom]').val(data.nom);
					p.$w.find('[name=diagno]').val(data.diagno);
					p.$w.find('[name=indi]').val(data.indi);

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
			title: 'Seleccionar Paciente',
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
					data: 'mh/paci/lista',
					params: {},
					itemdescr: 'tipo(s) de local',
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
	['mg/enti','ct/pcon','mh/paci'],
	function(mgEnti,ctPcon,mhpaci ){
		return mhMedi;
	}
);