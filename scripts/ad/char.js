adChar = {
	
	meses:{
		"01":"ENERO",
		"02":"FEBRERO",
		"03":"MARZO",
		"04":"ABRIL",
		"05":"MAYO",
		"06":"JUNIO",
		"07":"JULIO",
		"08":"AGOSTO",
		"09":"SETIEMBRE",
		"10":"OCTUBRE",
		"11":"NOVIEMBRE",
		"12":"DICIEMBRE"
	},

	dbRel: function(item){
		return {
			_id: item._id.$id,
				};
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adChar',
			titleBar: {
				title: 'Charlas'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Titulo','Mes','Año','Ultima Modificacion'],
					data: 'ad/char/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> ',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adChar.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append(' INFORME DE CHARLAS Y ATENCIÓN DE PACIENTES EN CONSULTORIO EXTERNO');
						var tipo = '--';
						if(data.mes){
							mes_ = adChar.meses[data.mes];
						}
						$row.append('<td>'+mes_+'</td>');
						$row.append('<td>'+data.año+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenFMedica", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFMedica_info': function(t) {
									adChar.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									adChar.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Charla:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/char/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Charlas Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adChar.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Charlas');
								},
								'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Charlas",
										url:"ad/char/print?_id="+K.tmp.data('id')
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
			id: 'windowNewCharlas',
			title: 'Nueva Charlas',
			contentURL: 'ad/char/edit',
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

								seso:p.$w.find('[name=seso]').val(),
								psic:p.$w.find('[name=psic]').val(),
								enfe:p.$w.find('[name=enfe]').val(),
								psiq:p.$w.find('[name=psiq]').val(),
								vchi:p.$w.find('[name=vchi]').val(),
								mchi:p.$w.find('[name=mchi]').val(),
								vpau:p.$w.find('[name=vpau]').val(),
								mpau:p.$w.find('[name=mpau]').val(),
								vros:p.$w.find('[name=vros]').val(),
								mros:p.$w.find('[name=mros]').val(),
								valf:p.$w.find('[name=valf]').val(),
								malf:p.$w.find('[name=malf]').val(),
								vgon:p.$w.find('[name=vgon]').val(),
								mgon:p.$w.find('[name=mgon]').val(),
								vjos:p.$w.find('[name=vjos]').val(),
								mjos:p.$w.find('[name=mjos]').val(),
								vmar:p.$w.find('[name=vmar]').val(),
								mmar:p.$w.find('[name=mmar]').val(),
								vcar:p.$w.find('[name=vcar]').val(),
								mcar:p.$w.find('[name=mcar]').val(),
								vasi:p.$w.find('[name=vasi]').val(),
								masi:p.$w.find('[name=masi]').val(),
								vfam:p.$w.find('[name=vfam]').val(),
								mfam:p.$w.find('[name=mfam]').val(),
								vfis:p.$w.find('[name=vfis]').val(),
								mfis:p.$w.find('[name=mfis]').val(),
								vmoq:p.$w.find('[name=vmoq]').val(),
								mmoq:p.$w.find('[name=mmoq]').val(),
								vcam:p.$w.find('[name=vcam]').val(),
								mcam:p.$w.find('[name=mcam]').val(),
								tppd:p.$w.find('[name=tppd]').val(),
								tpid:p.$w.find('[name=tpid]').val(),
								tpif:p.$w.find('[name=tpif]').val(),
								obse:p.$w.find('[name=obse]').val(),
								mes:p.$w.find('[name=mes]').val(),
								año:p.$w.find('[name=año]').val(),
								firm:p.$w.find('[name=firm]').val(),
								toin:p.$w.find('[name=toin]').val(),
								vsoa:p.$w.find('[name=vsoa]').val(),
								msoa:p.$w.find('[name=msoa]').val(),
								ttsoa:p.$w.find('[name=ttsoa]').val(),
								ttcef:p.$w.find('[name=ttcef]').val(),
								vcef:p.$w.find('[name=vcef]').val(),
								mcef:p.$w.find('[name=mcef]').val(),
								//CAMANA
								mran1:p.$w.find('[name=mran1]').val(),
								mran2:p.$w.find('[name=mran2]').val(),
								mran3:p.$w.find('[name=mran3]').val(),
								mran4:p.$w.find('[name=mran4]').val(),
								mran5:p.$w.find('[name=mran5]').val(),
								mran6:p.$w.find('[name=mran6]').val(),
								mran7:p.$w.find('[name=mran7]').val(),
								mran8:p.$w.find('[name=mran8]').val(),
								vran1:p.$w.find('[name=vran1]').val(),
								vran2:p.$w.find('[name=vran2]').val(),
								vran3:p.$w.find('[name=vran3]').val(),
								vran4:p.$w.find('[name=vran4]').val(),
								vran5:p.$w.find('[name=vran5]').val(),
								vran6:p.$w.find('[name=vran6]').val(),
								vran7:p.$w.find('[name=vran7]').val(),
								vran8:p.$w.find('[name=vran8]').val(),
								trva:p.$w.find('[name=trva]').val(),
								trmu:p.$w.find('[name=trmu]').val(),
								//CHILPINILLA
								mchi1:p.$w.find('[name=mchi1]').val(),
								mchi2:p.$w.find('[name=mchi2]').val(),
								mchi3:p.$w.find('[name=mchi3]').val(),
								mchi4:p.$w.find('[name=mchi4]').val(),
								mchi5:p.$w.find('[name=mchi5]').val(),
								mchi6:p.$w.find('[name=mchi6]').val(),
								mchi7:p.$w.find('[name=mchi7]').val(),
								mchi8:p.$w.find('[name=mchi8]').val(),
								vchi1:p.$w.find('[name=vchi1]').val(),
								vchi2:p.$w.find('[name=vchi2]').val(),
								vchi3:p.$w.find('[name=vchi3]').val(),
								vchi4:p.$w.find('[name=vchi4]').val(),
								vchi5:p.$w.find('[name=vchi5]').val(),
								vchi6:p.$w.find('[name=vchi6]').val(),
								vchi7:p.$w.find('[name=vchi7]').val(),
								vchi8:p.$w.find('[name=vchi8]').val(),
								tcva:p.$w.find('[name=tcva]').val(),
								tcmu:p.$w.find('[name=tcmu]').val(),
								
								
								};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/char/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Charlas Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									adChar.init();
									
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
				p.$w = $('#windowNewCharlas');
			
				
			}
		});
	},




	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditCharlas',
			title: 'Editar Charlas: ' + p.paci,
			contentURL: 'ad/char/edit',
			width: 900,
			height: 900,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL charENTE
							_id: p.id,
								
								seso:p.$w.find('[name=seso]').val(),
								psic:p.$w.find('[name=psic]').val(),
								enfe:p.$w.find('[name=enfe]').val(),
								psiq:p.$w.find('[name=psiq]').val(),
								vchi:p.$w.find('[name=vchi]').val(),
								mchi:p.$w.find('[name=mchi]').val(),
								vpau:p.$w.find('[name=vpau]').val(),
								mpau:p.$w.find('[name=mpau]').val(),
								vros:p.$w.find('[name=vros]').val(),
								mros:p.$w.find('[name=mros]').val(),
								valf:p.$w.find('[name=valf]').val(),
								malf:p.$w.find('[name=malf]').val(),
								vgon:p.$w.find('[name=vgon]').val(),
								mgon:p.$w.find('[name=mgon]').val(),
								vjos:p.$w.find('[name=vjos]').val(),
								mjos:p.$w.find('[name=mjos]').val(),
								vmar:p.$w.find('[name=vmar]').val(),
								mmar:p.$w.find('[name=mmar]').val(),
								vcar:p.$w.find('[name=vcar]').val(),
								mcar:p.$w.find('[name=mcar]').val(),
								vasi:p.$w.find('[name=vasi]').val(),
								masi:p.$w.find('[name=masi]').val(),
								vfam:p.$w.find('[name=vfam]').val(),
								mfam:p.$w.find('[name=mfam]').val(),
								vfis:p.$w.find('[name=vfis]').val(),
								mfis:p.$w.find('[name=mfis]').val(),
								vmoq:p.$w.find('[name=vmoq]').val(),
								mmoq:p.$w.find('[name=mmoq]').val(),
								vcam:p.$w.find('[name=vcam]').val(),
								mcam:p.$w.find('[name=mcam]').val(),
								tppd:p.$w.find('[name=tppd]').val(),
								tpid:p.$w.find('[name=tpid]').val(),
								tpif:p.$w.find('[name=tpif]').val(),
								obse:p.$w.find('[name=obse]').val(),
								mes:p.$w.find('[name=mes]').val(),
								año:p.$w.find('[name=año]').val(),
								firm:p.$w.find('[name=firm]').val(),
								toin:p.$w.find('[name=toin]').val(),
								vsoa:p.$w.find('[name=vsoa]').val(),
								msoa:p.$w.find('[name=msoa]').val(),
								ttsoa:p.$w.find('[name=ttsoa]').val(),
								ttcef:p.$w.find('[name=ttcef]').val(),
								vcef:p.$w.find('[name=vcef]').val(),
								mcef:p.$w.find('[name=mcef]').val(),
								//CAMANA
								mran1:p.$w.find('[name=mran1]').val(),
								mran2:p.$w.find('[name=mran2]').val(),
								mran3:p.$w.find('[name=mran3]').val(),
								mran4:p.$w.find('[name=mran4]').val(),
								mran5:p.$w.find('[name=mran5]').val(),
								mran6:p.$w.find('[name=mran6]').val(),
								mran7:p.$w.find('[name=mran7]').val(),
								mran8:p.$w.find('[name=mran8]').val(),
								vran1:p.$w.find('[name=vran1]').val(),
								vran2:p.$w.find('[name=vran2]').val(),
								vran3:p.$w.find('[name=vran3]').val(),
								vran4:p.$w.find('[name=vran4]').val(),
								vran5:p.$w.find('[name=vran5]').val(),
								vran6:p.$w.find('[name=vran6]').val(),
								vran7:p.$w.find('[name=vran7]').val(),
								vran8:p.$w.find('[name=vran8]').val(),
								trva:p.$w.find('[name=trva]').val(),
								trmu:p.$w.find('[name=trmu]').val(),
								//CHILPINILLA
								mchi1:p.$w.find('[name=mchi1]').val(),
								mchi2:p.$w.find('[name=mchi2]').val(),
								mchi3:p.$w.find('[name=mchi3]').val(),
								mchi4:p.$w.find('[name=mchi4]').val(),
								mchi5:p.$w.find('[name=mchi5]').val(),
								mchi6:p.$w.find('[name=mchi6]').val(),
								mchi7:p.$w.find('[name=mchi7]').val(),
								mchi8:p.$w.find('[name=mchi8]').val(),
								vchi1:p.$w.find('[name=vchi1]').val(),
								vchi2:p.$w.find('[name=vchi2]').val(),
								vchi3:p.$w.find('[name=vchi3]').val(),
								vchi4:p.$w.find('[name=vchi4]').val(),
								vchi5:p.$w.find('[name=vchi5]').val(),
								vchi6:p.$w.find('[name=vchi6]').val(),
								vchi7:p.$w.find('[name=vchi7]').val(),
								vchi8:p.$w.find('[name=vchi8]').val(),
								tcva:p.$w.find('[name=tcva]').val(),
								tcmu:p.$w.find('[name=tcmu]').val(),




						};
							if(data.seso==''){
							p.$w.find('[name=seso]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.psic==''){
							p.$w.find('[name=psic]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.enfe==''){
							p.$w.find('[name=enfe]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.psiq==''){
							p.$w.find('[name=psiq]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
					
						if(data.vpau==''){
							p.$w.find('[name=vpau]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mpau==''){
							p.$w.find('[name=mpau]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vros==''){
							p.$w.find('[name=vros]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mros==''){
							p.$w.find('[name=mros]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.valf==''){
							p.$w.find('[name=valf]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.malf==''){
							p.$w.find('[name=malf]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vgon==''){
							p.$w.find('[name=vgon]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mgon==''){
							p.$w.find('[name=mgon]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vjos==''){
							p.$w.find('[name=vjos]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mjos==''){
							p.$w.find('[name=mjos]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vmar==''){
							p.$w.find('[name=vmar]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mmar==''){
							p.$w.find('[name=mmar]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vcar==''){
							p.$w.find('[name=vcar]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mcar==''){
							p.$w.find('[name=mcar]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vasi==''){
							p.$w.find('[name=vasi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.masi==''){
							p.$w.find('[name=masi]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vfam==''){
							p.$w.find('[name=vfam]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mfam==''){
							p.$w.find('[name=mfam]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vfis==''){
							p.$w.find('[name=vfis]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mfis==''){
							p.$w.find('[name=mfis]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.vmoq==''){
							p.$w.find('[name=vmoq]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mmoq==''){
							p.$w.find('[name=mmoq]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.mes==''){
							p.$w.find('[name=mes]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.año==''){
							p.$w.find('[name=año]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.firm==''){
							p.$w.find('[name=firm]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						if(data.toin==''){
							p.$w.find('[name=toin]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo requerido!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ad/char/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Charlas Actualizada!"});
							adChar.init();
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
				p.$w = $('#windowEditCharlas');
				p.$w.find("[name=btnDiag]").click(function(){
					adPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(data.ape + ',' + data.nom).data('data',data);
						p.$w.find('[name=clini]').html(data.his_Cli).data('data',data);
					},bootstrap: true});
				});
				K.block();
				
				    $.post('ad/char/get',{_id: p.id},function(data){
								seso:p.$w.find('[name=seso]').val(data.seso);
								psic:p.$w.find('[name=psic]').val(data.psic);
								enfe:p.$w.find('[name=enfe]').val(data.enfe);
								psiq:p.$w.find('[name=psiq]').val(data.psiq);
								vchi:p.$w.find('[name=vchi]').val(data.vchi);
								mchi:p.$w.find('[name=mchi]').val(data.mchi);
								vpau:p.$w.find('[name=vpau]').val(data.vpau);
								mpau:p.$w.find('[name=mpau]').val(data.mpau);
								vros:p.$w.find('[name=vros]').val(data.vros);
								mros:p.$w.find('[name=mros]').val(data.mros);
								valf:p.$w.find('[name=valf]').val(data.valf);
								malf:p.$w.find('[name=malf]').val(data.malf);
								vgon:p.$w.find('[name=vgon]').val(data.vgon);
								mgon:p.$w.find('[name=mgon]').val(data.mgon);
								vjos:p.$w.find('[name=vjos]').val(data.vjos);
								mjos:p.$w.find('[name=mjos]').val(data.mjos);
								vmar:p.$w.find('[name=vmar]').val(data.vmar);
								mmar:p.$w.find('[name=mmar]').val(data.mmar);
								vcar:p.$w.find('[name=vcar]').val(data.vcar);
								mcar:p.$w.find('[name=mcar]').val(data.mcar);
								vasi:p.$w.find('[name=vasi]').val(data.vasi);
								masi:p.$w.find('[name=masi]').val(data.masi);
								vfam:p.$w.find('[name=vfam]').val(data.vfam);
								mfam:p.$w.find('[name=mfam]').val(data.mfam);
								vfis:p.$w.find('[name=vfis]').val(data.vfis);
								mfis:p.$w.find('[name=mfis]').val(data.mfis);
								vmoq:p.$w.find('[name=vmoq]').val(data.vmoq);
								mmoq:p.$w.find('[name=mmoq]').val(data.mmoq);
								topa:p.$w.find('[name=topa]').val(data.topa);
								atad:p.$w.find('[name=atad]').val(data.atad);
								atch:p.$w.find('[name=atch]').val(data.atch);
								vcam:p.$w.find('[name=vcam]').val(data.vcam);
								mcam:p.$w.find('[name=mcam]').val(data.mcam);
								tppd:p.$w.find('[name=tppd]').val(data.tppd);
								tpid:p.$w.find('[name=tpid]').val(data.tpid);
								tpif:p.$w.find('[name=tpif]').val(data.tpif);
								obse:p.$w.find('[name=obse]').val(data.obse);
								mes:p.$w.find('[name=mes]').val(data.mes);
								año:p.$w.find('[name=año]').val(data.año);
								firm:p.$w.find('[name=firm]').val(data.firm);
								toin:p.$w.find('[name=toin]').val(data.toin);
								vsoa:p.$w.find('[name=vsoa]').val(data.vsoa);
								msoa:p.$w.find('[name=msoa]').val(data.masi);
								ttsoa:p.$w.find('[name=ttsoa]').val(data.ttsoa);
								ttcef:p.$w.find('[name=ttcef]').val(data.ttcef);
								vcef:p.$w.find('[name=vcef]').val(data.vcef);
								mcef:p.$w.find('[name=mcef]').val(data.mcef);
								//CAMANA
								mran1:p.$w.find('[name=mran1]').val(data.mran1);
								mran2:p.$w.find('[name=mran2]').val(data.mran2);
								mran3:p.$w.find('[name=mran3]').val(data.mran3);
								mran4:p.$w.find('[name=mran4]').val(data.mran4);
								mran5:p.$w.find('[name=mran5]').val(data.mran5);
								mran6:p.$w.find('[name=mran6]').val(data.mran6);
								mran7:p.$w.find('[name=mran7]').val(data.mran7);
								mran8:p.$w.find('[name=mran8]').val(data.mran8);
								vran1:p.$w.find('[name=vran1]').val(data.vran1);
								vran2:p.$w.find('[name=vran2]').val(data.vran2);
								vran3:p.$w.find('[name=vran3]').val(data.vran3);
								vran4:p.$w.find('[name=vran4]').val(data.vran4);
								vran5:p.$w.find('[name=vran5]').val(data.vran5);
								vran6:p.$w.find('[name=vran6]').val(data.vran6);
								vran7:p.$w.find('[name=vran7]').val(data.vran7);
								vran8:p.$w.find('[name=vran8]').val(data.vran8);
								trva:p.$w.find('[name=trva]').val(data.trva);
								trmu:p.$w.find('[name=trmu]').val(data.trmu);
								//CHILPINILLA
								mchi1:p.$w.find('[name=mchi1]').val(data.mchi1);
								mchi2:p.$w.find('[name=mchi2]').val(data.mchi2);
								mchi3:p.$w.find('[name=mchi3]').val(data.mchi3);
								mchi4:p.$w.find('[name=mchi4]').val(data.mchi4);
								mchi5:p.$w.find('[name=mchi5]').val(data.mchi5);
								mchi6:p.$w.find('[name=mchi6]').val(data.mchi6);
								mchi7:p.$w.find('[name=mchi7]').val(data.mchi7);
								mchi8:p.$w.find('[name=mchi8]').val(data.mchi8);
								vchi1:p.$w.find('[name=vchi1]').val(data.vchi1);
								vchi2:p.$w.find('[name=vchi2]').val(data.vchi2);
								vchi3:p.$w.find('[name=vchi3]').val(data.vchi3);
								vchi4:p.$w.find('[name=vchi4]').val(data.vchi4);
								vchi5:p.$w.find('[name=vchi5]').val(data.vchi5);
								vchi6:p.$w.find('[name=vchi6]').val(data.vchi6);
								vchi7:p.$w.find('[name=vchi7]').val(data.vchi7);
								vchi8:p.$w.find('[name=vchi8]').val(data.vchi8);
								tcva:p.$w.find('[name=tcva]').val(data.tcva);
								tcmu:p.$w.find('[name=tcmu]').val(data.tcmu);



							K.unblock();
						},'json');
					}
				});
			},

		};

	define(
		['mg/enti','ct/pcon','ad/paci'],
		function(mgEnti,ctPcon,adpaci ){
			return adChar;
		}
);