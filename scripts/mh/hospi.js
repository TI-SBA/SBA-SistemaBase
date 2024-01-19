mhHospi = {
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
	tipos: {
		"":"S/E",
		"C":"Completa",
		"P":"Parcial"
	},
	dbRel: function(item){
		var _item = {
			_id: item._id.$id,
			paciente: item.paciente,
			cie10: item.cie10,
		}
		if(_item.paciente._id!=null){
			if(_item.paciente._id.$id!=null){
				_item.paciente._id = _item.paciente._id.$id;
			}
		}
		return _item;
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhHospi',
			titleBar: {
				title: 'Hospitalizaciones'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Historia Clinica','Paciente','CIE.10','Sintomas','Fecha de Ingreso','Fecha de Egreso','Nro. de Ingreso','Tipo de Hospitalizacion','Pabellon','Ultima Modificacion'],
					data: 'mh/hospi/lista',
					params: {modulo: 'MH'},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>'+
					'&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
						'</select>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhHospi.windowNew();
						});
						$el.find('[name=modulo]').change(function(){
							var modulo = $el.find('[name=modulo] option:selected').val();
							$grid.reinit({params: {modulo: modulo}});
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.hist_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
						$row.append('<td>'+data.cie10+'</td>');
						$row.append('<td>'+data.diag+'</td>');
						var fecini = '--';
						if(data.fecini!=null){
							fecini = moment(data.fecini.sec,'X').format('DD/MM/YYYY');
						}
						$row.append('<td>'+fecini+'</td>');
						var fecegr = '--';
						if(data.fecegr!=null){
							fecegr = moment(data.fecegr.sec,'X').format('DD/MM/YYYY');
						}
						var tipo = '--';
						if(data.tipo_hosp){
							tipo = mhHospi.tipos[data.tipo_hosp];
						}
						$row.append('<td>'+fecegr+'</td>');
						//$row.append('<td>'+moment(data.fecegr.sec,'X').format('DD/MM/YYYY')+'</td>');
						$row.append('<td>'+data.ning+'</td>');
						$row.append('<td>'+tipo+'</td>');
						$row.append('<td>'+data.pabe+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"mh/hospi/if_fron?_id="+$(this).data('id')
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
									mhHospi.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									mhHospi.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Hospitalizacion:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/hospi/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Hospitalizacion Eliminada',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhHospi.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Hospitalizacion');
								},
								'conMenFhospica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Hospitalizacion",
										url:"mh/hospi/print?_id="+K.tmp.data('id')
									});
								},'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Hospitalizacion",
										url:"mh/hospi/print?_id="+K.tmp.data('id')
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
		mhPaci.windowSelect({callback: function(paci){
			new K.Panel({
				title: 'Nueva Hospitalizacion',
				contentURL: 'mh/hospi/edit',
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
   										estado: 'P',
   										//centro: 'MH',
										paciente: mgEnti.dbRel(p.$w.find('[name=paciente] [name=mini_enti]').data('data')),
										apoderado: mgEnti.dbRel(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')),
										hist_cli:p.$w.find('[name=his]').text(),
										cie10:p.$w.find('[name=cie10]').text(),
										modulo:p.$w.find('[name=modulo]').text(),
										pabe:p.$w.find('[name=pabe]').val(),
										ning:p.$w.find('[name=ning]').val(),
										fecini:p.$w.find('[name=fecini]').val(),
										tipo_hosp:p.$w.find('[name=tipo]').val(),
										diag:p.$w.find('[name=diag]').text(),
										moti:p.$w.find('[name=moti]').val(),
										fecfin:p.$w.find('[name=fegr]').val(),
										//--------------------------------//
										fena:p.$w.find('[name=fena]').text(),
										domi:p.$w.find('[name=domi]').text(),
										reli:p.$w.find('[name=reli]').text(),
										es_civil:p.$w.find('[name=es_civil]').text(),
										ocup:p.$w.find('[name=ocup]').text(),
										deso:p.$w.find('[name=deso]').text(),
										resi:p.$w.find('[name=resi]').text(),
										instr:p.$w.find('[name=instr]').text(),
										idio:p.$w.find('[name=idio]').text(),
										refe:p.$w.find('[name=refe]').text(),
										dire:p.$w.find('[name=dire]').text(),
										tele:p.$w.find('[name=tele]').text(),

												};
									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("mh/hospi/save",data,function(result){
										if(result.error!=null){
											K.clearNoti();
											K.msg({title: ciHelper.titles.infoReq,text: "Debe existir una ficha social antes!",type: 'error'});
										}else{
											K.clearNoti();
											K.msg({title: ciHelper.titles.regiGua,text: "Hospitalizacion Agregada!"});
											mhHospi.init();
										}
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							mhHospi.init();
						}
					}
				},
				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					p.$w.find("[name=fecini],[name=fegr]").datepicker({
		   				format: 'yyyy-mm-dd',
		    			startDate: '-3d'
					});
					$.post('mh/hospi/get_ingreso',{_id:paci.paciente._id.$id}, function(data){
						var ing = 1;
						if(data!=null){
							ing = parseFloat(data.ning)+1;
						}
						p.$w.find('[name=ning]').val(ing);
					},'json');
					p.$w.find("[name=btnDiagini]").click(function(){
						mhDini.windowSelect({callback: function(data){
							p.$w.find('[name=diag]').html(data.nomb).data('data',data);
							p.$w.find('[name=cie10]').html(data.sigl).data('data',data);
						},bootstrap: true});
					});
					mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),paci.paciente);
					mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),paci.apoderado);
					p.$w.find('[name=his]').html(paci.his_cli).data('data',paci);
					p.$w.find('[name=modulo]').html(paci.modulo).data('data',paci);
					p.$w.find('[name=fena]').html(moment(paci.fecha_na.sec,'X').format('YYYY-MM-DD')).data('data',paci);
					p.$w.find('[name=domi]').html(paci.domi).data('data',paci);
					p.$w.find('[name=reli]').html(paci.reli).data('data',paci);
					p.$w.find('[name=es_civil]').html(paci.es_civil).data('data',paci);
					p.$w.find('[name=ocup]').html(paci.ocupa).data('data',paci);
					p.$w.find('[name=deso]').html(paci.t_deso).data('data',paci);
					p.$w.find('[name=resi]').html(paci.m_resi).data('data',paci);
					p.$w.find('[name=instr]').html(paci.instr).data('data',paci);
					p.$w.find('[name=idio]').html(paci.idio).data('data',paci);
					p.$w.find('[name=refe]').html(paci.refe).data('data',paci);
					p.$w.find('[name=moti]').html(paci.m_consu).data('data',paci);
				}
			});
		}})
	},
	windowEdit: function(p){
		new K.Panel({ 
			title: 'Editar Hospitalizacion: ' + p.nom,
			contentURL: 'mh/hospi/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL Hospitalizacion
							_id: p.id,
							paciente: mgEnti.dbRel(p.$w.find('[name=paciente] [name=mini_enti]').data('data')),
							apoderado: mgEnti.dbRel(p.$w.find('[name=apoderado] [name=mini_enti]').data('data')),
							hist_cli:p.$w.find('[name=his]').text(),
							cie10:p.$w.find('[name=cie10]').text(),
							pabe:p.$w.find('[name=pabe]').val(),
							ning:p.$w.find('[name=ning]').val(),
							fecini:p.$w.find('[name=fecini]').val(),
							tipo_hosp:p.$w.find('[name=tipo]').val(),
							diag:p.$w.find('[name=diag]').text(),
							moti:p.$w.find('[name=moti]').val(),
							fecfin:p.$w.find('[name=fegr]').val(),
							fena:p.$w.find('[name=fena]').text(),
							domi:p.$w.find('[name=domi]').text(),
							reli:p.$w.find('[name=reli]').text(),
							es_civil:p.$w.find('[name=es_civil]').text(),
							ocup:p.$w.find('[name=ocup]').text(),
							trab:p.$w.find('[name=trab]').text(),
							deso:p.$w.find('[name=deso]').text(),
							resi:p.$w.find('[name=resi]').text(),
							instr:p.$w.find('[name=instr]').text(),
							idio:p.$w.find('[name=idio]').text(),
							refe:p.$w.find('[name=refe]').text(),
							dire:p.$w.find('[name=apod]').text(),
							tele:p.$w.find('[name=tele]').text(),
							modulo:p.$w.find('[name=modulo]').text(),
						};
						
						if(data.his==''){
							p.$w.find('[name=his]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Historia Clinica!',type: 'error'});
						}
						if(data.doc==''){
							p.$w.find('[name=doc]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Documento!',type: 'error'});
						}
						if(data.pabe==''){
							p.$w.find('[name=pabe]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Pabellon!',type: 'error'});
						}
						if(data.ning==''){
							p.$w.find('[name=ning]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Numero de Ingreso!',type: 'error'});
						}
						if(data.fecini==''){	
							p.$w.find('[name=fecini]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Fecha de Inicio!',type: 'error'});
						}
						if(data.tipo_hosp==''){
							p.$w.find('[name=tipo]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Tipo de Hospitalizacion!',type: 'error'});
						}
						if(data.diag==''){
							p.$w.find('[name=diag]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Diagnostico!',type: 'error'});
						}
						if(data.moti==''){
							p.$w.find('[name=moti]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el Motivo!',type: 'error'});
						}
						if(data.cie10==''){
							p.$w.find('[name=cie10]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el CIE10!',type: 'error'});
						}
							if(data.fena==''){
							p.$w.find('[name=fena]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Fecna de Nacimiento!',type: 'error'});
						}	
						if(data.refe==''){
							p.$w.find('[name=refe]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Referencia!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/hospi/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Hospitalizacion Actualizada!"});
							mhHospi.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						mhHospi.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find("[name=fing],[name=fegr]").datepicker({
		   			format: 'yyyy-mm-dd',
	    			startDate: '-3d'
				});
				p.$w.find("[name=btnDiagini]").click(function(){
					mhDini.windowSelect({callback: function(data){
						p.$w.find('[name=diag]').html(data.nomb).data('data',data);
						p.$w.find('[name=cie10]').html(data.sigl).data('data',data);
					},bootstrap: true});
				});
				K.block();
			    $.post('mh/hospi/get',{_id: p.id},function(data){
						let fecfin = ""
						if(typeof data.fecfin.sec !== "undefined")
							fecfin=moment(data.fecfin.sec,'X').format('YYYY-MM-DD');
						else
							fecfin=new Date(data.fecfin).toDateString("yyyy-MM-dd");
						mgEnti.fillMini(p.$w.find('[name=paciente] [name=mini_enti]'),data.paciente);
						mgEnti.fillMini(p.$w.find('[name=apoderado] [name=mini_enti]'),data.apoderado);
    				p.$w.find('[name=his]').text(data.hist_cli);
						p.$w.find('[name=pabe]').val(data.pabe);
						p.$w.find('[name=cie10]').text(data.cie10);
						p.$w.find('[name=ning]').val(data.ning);
						p.$w.find('[name=fecini]').val(moment(data.fecini.sec,'X').format('YYYY-MM-DD'));
						p.$w.find('[name=tipo]').val(data.tipo_hosp);
						p.$w.find('[name=diag]').text(data.diag);
						p.$w.find('[name=moti]').val(data.moti);
						p.$w.find('[name=fegr]').val(moment(fecfin).format("YYYY-MM-DD"));
						p.$w.find('[name=fena]').text(data.fena);
						p.$w.find('[name=domi]').text(data.domi);
						p.$w.find('[name=reli]').text(data.reli);
						p.$w.find('[name=es_civil]').text(data.es_civil);
						p.$w.find('[name=ocup]').text(data.ocup);
						p.$w.find('[name=trab]').text(data.trab);
						p.$w.find('[name=deso]').text(data.deso);
						p.$w.find('[name=resi]').text(data.resi);
						p.$w.find('[name=instr]').text(data.instr);
						p.$w.find('[name=idio]').text(data.idio);
						p.$w.find('[name=refe]').text(data.refe);
						p.$w.find('[name=dire]').text(data.dire);
						p.$w.find('[name=tele]').text(data.tele);
						p.$w.find('[name=modulo]').text(data.modulo);
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
			title: 'Seleccionar Paciente a Hospitalizar',
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
					cols: ['','Historia Clinica','Nombre de Paciente'],
					data: 'mh/hospi/lista',
					params: {modulo:'MH'},
					itemdescr: 'paciente(s)',
					toolbarHTML: '&nbsp;<select class="form-control" name="modulo">'+
							'<option value="MH">Salud Mental</option>'+
							'<option value="AD">Adicciones</option>'+
						'</select>',
					onContentLoaded: function($el){
							$el.find('[name=modulo]').change(function(){
								var modulo = $el.find('[name=modulo] option:selected').val();
								p.$grid.reinit({params: {modulo: modulo}});
							});
							
						},
					onLoading: function(){
					 K.block();
					 $.post('')
					},
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.hist_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente)+'</td>');
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
	['mh/paci','mh/dini'],
	function(mhpaci,mhdini){
		return mhHospi;
	}
);