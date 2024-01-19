mhPsiq = {
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
			moti: item.moti,
			desc: item.desc,
			diag: item.diag,
			infor: item.infor,
			sin: item.sin,
			hist: item.hist,
			desa: item.desa,
			educ: item.educ,
			ocup: item.ocup,
			pisco: item.pisco,
			mari: item.mari,
			recre: item.recre,
			habi: item.habi,
			reli: item.reli,
			mili: item.mili,
			movi: item.movi,
			deli: item.deli,
			enfe: item.enfe,
			perso: item.perso,
			ante: item.ante,
			parip: item.parip,
			parim: item.parim,
			padr: item.padr,
			herm: item.herm,
			hish: item.hish,
			apar: item.apar,
			aten: item.aten,
			cur: item.cur,
			efec: item.efec,
			cont: item.cont,
			memo: item.memo,
			compre: item.compre,
			diagn: item.diagn,
			doc: item.doc
		};
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhPsiq',
			titleBar: {
				title: 'Ficha Psiquiatrica'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Historia Clinica','Paciente' ,'Ultima Modificacion'],
					data: 'mh/psiq/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhPsiq.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.paciente.his_cli+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.paciente.paciente)+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenFMedica", {
							bindings: {
								'conMenFMedica_info': function(t) {
									mhPsiq.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									$.post('mh/psiq/permiso',{_id: K.tmp.data('id')},function(rpta){
				 						if(rpta.data.permiso==true){
				 							K.msg({title:'Mensaje del Sistema!',type: rpta.status,text: rpta.message});
				 							mhPaci.windowEdit({_id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
				 						}else{
				 							K.msg({title:'Mensaje del Sistema!',type: rpta.status,text: rpta.message});
				 						}
				 						mhPsiq.init();
				 					},'json')
								},
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Psiquiatrica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/psiq/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Ficha Psiquiatrica Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhPsiq.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Ficha Psiquiatrica');
								},
								'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Ficha Psiquiatrica",
										url:"mh/psiq/print?_id="+K.tmp.data('id')
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
			id: 'windowNewFichaPsiquiatrica',
			title: 'Nueva Ficha Psiquiatrica',
			contentURL: 'mh/psiq/edit',
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
								var paciente = p.$w.find('[name=paci]').data('data');
								var data = {
									paciente: {
										_id: paciente._id.$id,
										his_cli: paciente.his_cli,
										paciente: mgEnti.dbRel(paciente.paciente),
										sexo: paciente.sexo,
										edad: paciente.edad
									},
									moti:p.$w.find('[name=moti]').val(),
									desc:p.$w.find('[name=desc]').val(),
									diag:p.$w.find('[name=diag]').val(),
									infor:p.$w.find('[name=infor]').val(),
									sin:p.$w.find('[name=sin]').val(),
									hist:p.$w.find('[name=hist]').val(),
									desa:p.$w.find('[name=desa]').val(),
									educ:p.$w.find('[name=educ]').val(),
									ocup:p.$w.find('[name=ocup]').val(),
									pisco:p.$w.find('[name=pisco]').val(),
									mari:p.$w.find('[name=mari]').val(),
									recre:p.$w.find('[name=recre]').val(),
									habi:p.$w.find('[name=habi]').val(),
									reli:p.$w.find('[name=reli]').val(),
									mili:p.$w.find('[name=mili]').val(),
									movi:p.$w.find('[name=movi]').val(),
									deli:p.$w.find('[name=deli]').val(),
									enfe:p.$w.find('[name=enfe]').val(),
									perso:p.$w.find('[name=perso]').val(),
									ante:p.$w.find('[name=ante]').val(),
									parip:p.$w.find('[name=parip]').val(),
									parim:p.$w.find('[name=parim]').val(),
									padr:p.$w.find('[name=padr]').val(),
									herm:p.$w.find('[name=herm]').val(),
									hish:p.$w.find('[name=hish]').val(),
									apar:p.$w.find('[name=apar]').val(),
									aten:p.$w.find('[name=aten]').val(),
									cur:p.$w.find('[name=cur]').val(),
									efec:p.$w.find('[name=efec]').val(),
									cont:p.$w.find('[name=cont]').val(),
									memo:p.$w.find('[name=memo]').val(),
									compre:p.$w.find('[name=compre]').val(),
									diagn:p.$w.find('[name=diagn]').val(),
									doc:p.$w.find('[name=doc]').val()
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/psiq/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psiquiatrica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									mhPsiq.init();
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
				p.$w = $('#windowNewFichaPsiquiatrica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(mgEnti.formatName(data.paciente)).data('data',data);
					},bootstrap: true});
				});
			}
		});
	},

	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditFichaPsiquiatrica',
			title: 'Editar Ficha Psiquiatrica: ' +mgEnti.formatName(p.paciente),
			contentURL: 'mh/psiq/edit',
			width: 900,
			height: 900,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var form = ciHelper.validator(p.$w.find('form'),{
							onSuccess: function(){
								K.sendingInfo();
								var paciente = p.$w.find('[name=paci]').data('data');
								var data = {
									_id: p.id,
									paciente: {
										_id: paciente._id.$id,
										his_cli: paciente.his_cli,
										paciente: mgEnti.dbRel(paciente.paciente),
										sexo: paciente.sexo,
										edad: paciente.edad
									},
									moti:p.$w.find('[name=moti]').val(),
									desc:p.$w.find('[name=desc]').val(),
									diag:p.$w.find('[name=diag]').val(),
									infor:p.$w.find('[name=infor]').val(),
									sin:p.$w.find('[name=sin]').val(),
									hist:p.$w.find('[name=hist]').val(),
									desa:p.$w.find('[name=desa]').val(),
									educ:p.$w.find('[name=educ]').val(),
									ocup:p.$w.find('[name=ocup]').val(),
									pisco:p.$w.find('[name=pisco]').val(),
									mari:p.$w.find('[name=mari]').val(),
									recre:p.$w.find('[name=recre]').val(),
									habi:p.$w.find('[name=habi]').val(),
									reli:p.$w.find('[name=reli]').val(),
									mili:p.$w.find('[name=mili]').val(),
									movi:p.$w.find('[name=movi]').val(),
									deli:p.$w.find('[name=deli]').val(),
									enfe:p.$w.find('[name=enfe]').val(),
									perso:p.$w.find('[name=perso]').val(),
									ante:p.$w.find('[name=ante]').val(),
									parip:p.$w.find('[name=parip]').val(),
									parim:p.$w.find('[name=parim]').val(),
									padr:p.$w.find('[name=padr]').val(),
									herm:p.$w.find('[name=herm]').val(),
									hish:p.$w.find('[name=hish]').val(),
									apar:p.$w.find('[name=apar]').val(),
									aten:p.$w.find('[name=aten]').val(),
									cur:p.$w.find('[name=cur]').val(),
									efec:p.$w.find('[name=efec]').val(),
									cont:p.$w.find('[name=cont]').val(),
									memo:p.$w.find('[name=memo]').val(),
									compre:p.$w.find('[name=compre]').val(),
									diagn:p.$w.find('[name=diagn]').val(),
									doc:p.$w.find('[name=doc]').val(),
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("mh/psiq/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Ficha Psiquiatrica Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									mhPsiq.init();
									
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
				p.$w = $('#windowEditFichaPsiquiatrica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(data.ape + ',' + data.nom).data('data',data);
					},bootstrap: true});
				});
				K.block();
				
				$.post('mh/psiq/get',{_id: p.id},function(data){
					p.$w.find('[name=paci]').text(mgEnti.formatName(data.paciente.paciente)).data('data',data.paciente);
					p.$w.find('[name=moti]').val(data.moti);
					p.$w.find('[name=desc]').val(data.desc);
					p.$w.find('[name=diag]').val(data.diag);
					p.$w.find('[name=infor]').val(data.infor);
					p.$w.find('[name=sin]').val(data.sin);
					p.$w.find('[name=hist]').val(data.hist);
					p.$w.find('[name=desa]').val(data.desa);
					p.$w.find('[name=educ]').val(data.educ);
					p.$w.find('[name=ocup]').val(data.ocup);
					p.$w.find('[name=mari]').val(data.mari);
					p.$w.find('[name=pisco]').val(data.pisco);
					p.$w.find('[name=recre]').val(data.recre);
					p.$w.find('[name=habi]').val(data.habi);
					p.$w.find('[name=reli]').val(data.reli);
					p.$w.find('[name=mili]').val(data.mili);
					p.$w.find('[name=movi]').val(data.movi);
					p.$w.find('[name=deli]').val(data.deli);
					p.$w.find('[name=enfe]').val(data.enfe);
					p.$w.find('[name=perso]').val(data.perso);
					p.$w.find('[name=ante]').val(data.ante);
					p.$w.find('[name=parip]').val(data.parip);
					p.$w.find('[name=parim]').val(data.parim);
					p.$w.find('[name=padr]').val(data.padr);
					p.$w.find('[name=herm]').val(data.herm);
					p.$w.find('[name=hish]').val(data.hish);
					p.$w.find('[name=apar]').val(data.apar);
					p.$w.find('[name=aten]').val(data.aten);
					p.$w.find('[name=cur]').val(data.cur);
					p.$w.find('[name=efec]').val(data.efec);
					p.$w.find('[name=cont]').val(data.cont);
					p.$w.find('[name=memo]').val(data.memo);
					p.$w.find('[name=compre]').val(data.compre);
					p.$w.find('[name=diagn]').val(data.diagn);
					p.$w.find('[name=doc]').val(data.doc);
					K.unblock();
				},'json');
			}
		});
	},

};

define(
	['mg/enti','ct/pcon','mh/paci'],
	function(mgEnti,ctPcon,mhpaci){
		return mhPsiq;
	}
);