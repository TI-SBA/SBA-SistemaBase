adCons = {
	
	dbRel: function(item){
		return {
			_id: item._id.$id,
			his:item.his,
			doct:item.doct,
			part:item.part,
			ap:item.ape,
			nom:item.nom,
			cate:item.cate,
			esta:item.esta,
			sexo:item.sexo,
			edad:item.edad,
			proce:item.proce,
			cie10:item.cie10
			
		};
	},
	init: function(){
		K.initMode({
			mode: 'ad',
			action: 'adCons',
			titleBar: {
				title: 'Consultaes'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','ID Parte','Historia Clinica','Paciente','Sexo','Edad','Estado','Diagnostico','Categoria','Proce','Ultima Modificacion'],
					data: 'ad/cons/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> ',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							adCons.windowNew();
						});
						
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.part+'</td>');
						$row.append('<td>'+data.his+'</td>');
						$row.append('<td>'+data.ap+ ',' + data.nom+'</td>');
						$row.append('<td>'+data.sexo+'</td>');
						$row.append('<td>'+data.edad+'</td>');
						$row.append('<td>'+data.esta+'</td>');
						$row.append('<td>'+data.cie10+'</td>');
						$row.append('<td>'+data.cate+'</td>');
						$row.append('<td>'+data.proce+'</td>');			
						
						
				$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.WindowPrint({
								id:'windowPrint',
								title:"Informe ",
								url:"ad/cons/if_fron?_id="+$(this).data('id')
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
									adCons.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									adCons.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
							
								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Consulta:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ad/cons/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Consulta Eliminada',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											adCons.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Consulta');
								},
								'conMenFconsca_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Consulta",
										url:"ad/cons/print?_id="+K.tmp.data('id')
									});
								},'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Consulta",
										url:"ad/cons/print?_id="+K.tmp.data('id')
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
			id: 'windowNewConsulta',
			title: 'Nueva Consulta',
			contentURL: 'ad/cons/edit',
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
												
												his:p.$w.find('[name=his]').text(),
												doct:p.$w.find('[name=doct]').text(),
												part:p.$w.find('[name=part]').text(),
												ap:p.$w.find('[name=ap]').text(),
												nom:p.$w.find('[name=nom]').text(),
												esta:p.$w.find('[name=esta]').val(),
												cate:p.$w.find('[name=cate]').val(),
												sexo:p.$w.find('[name=sexo]').text(),
												edad:p.$w.find('[name=edad]').text(),
												cie10:p.$w.find('[name=cie10]').text(),
												proce:p.$w.find('[name=proce]').text(),
												


											};
								p.$w.find('#div_buttons button').attr('disabled','disabled');
								$.post("ad/cons/save",data,function(result){
									K.clearNoti();
									K.msg({title: ciHelper.titles.regiGua,text: "Consulta Agregada!"});
									K.closeWindow(p.$w.attr('id'));
									adCons.init();

									K.unblock();
									
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
				p.$w = $('#windowNewConsulta');
				p.$w.find("[name=btnDiag]").click(function(){
					adPaci.windowSelect({callback: function(data){
						p.$w.find('[name=his_cli]').html(data.his_cli).data('data',data);
						p.$w.find('[name=ap]').html(data.ape).data('data',data);
						p.$w.find('[name=nom]').html(data.nom).data('data',data);
						p.$w.find('[name=sexo]').html(data.sexo).data('data',data);
						p.$w.find('[name=proce]').html(data.procede).data('data',data);
						p.$w.find('[name=edad]').html(data.edad).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDiagini]").click(function(){
					adDini.windowSelect({callback: function(data){
						p.$w.find('[name=cie10]').html(data.sigl + '-' + data.nomb).data('data',data);
					},bootstrap: true});
				});
				p.$w.find("[name=btnDoct]").click(function(){
					adPadi.windowSelect({callback: function(data){
						
						p.$w.find('[name=part]').html(moment(data.fech.sec,'X').format('YYYY-MM-DD')).data('data',data);
						p.$w.find('[name=doct]').html(data.doct).data('data',data);
					},bootstrap: true});
				});

				new K.grid({
					$el: p.$w.find('[name=gridList]'),
					search: false,
					data: 'ad/cons/lista',
					//pagination: false,
					cols: ['','ID Parte','Historia Clinica','Paciente','Sexo','Edad','Estado','Diagnostico','Categoria','Proce'],
					//onlyHtml:true,
					toolbarHTML: '<h3>Lista de Pacientes</h3>',
					onLoading: function(){ K.block(); },
					onComplete: function(){K.unblock(); 
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.part+'</td>');
						$row.append('<td>'+data.his+'</td>');
						$row.append('<td>'+data.ap+ ',' + data.nom+'</td>');
						$row.append('<td>'+data.sexo+'</td>');
						$row.append('<td>'+data.edad+'</td>');
						$row.append('<td>'+data.esta+'</td>');
						$row.append('<td>'+data.cie10+'</td>');
						$row.append('<td>'+data.cate+'</td>');
						$row.append('<td>'+data.proce+'</td>');	
						return $row;
					}
				});

			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditConsulta',
			title: 'Editar Consulta: ' + p.nom,
			contentURL: 'ad/cons/edit',
			width: 900,
			height: 900,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL Consulta
							_id: p.id,
												his:p.$w.find('[name=his]').text(),
												doct:p.$w.find('[name=doct]').text(),
												part:p.$w.find('[name=part]').text(),
												ap:p.$w.find('[name=ap]').text(),
												nom:p.$w.find('[name=nom]').text(),
												esta:p.$w.find('[name=esta]').val(),
												cate:p.$w.find('[name=cate]').val(),
												cie10:p.$w.find('[name=cie10]').text(),
												sexo:p.$w.find('[name=sexo]').text(),
												edad:p.$w.find('[name=edad]').text(),
												proce:p.$w.find('[name=proce]').text(),

												


												
						};
						if(data.his==''){
							p.$w.find('[name=his]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.doct==''){
							p.$w.find('[name=doct]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.ap==''){
							p.$w.find('[name=ap]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.nom==''){
							p.$w.find('[name=nom]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.esta==''){
							p.$w.find('[name=esta]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cate==''){
							p.$w.find('[name=cate]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						if(data.cie10==''){	
							p.$w.find('[name=cie10]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo!',type: 'error'});
						}
						

						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ad/cons/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Consulta Actualizada!"});
							adCons.init();
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
				p.$w = $('#windowEditConsulta');
				K.block();
				
				     $.post('ad/cons/get',{_id: p.id},function(data){

				     				p.$w.find('[name=his]').text(data.his),
									p.$w.find('[name=doct]').text(data.doct),
									p.$w.find('[name=part]').text(data.part),
									p.$w.find('[name=ap]').text(data.ap),
									p.$w.find('[name=nom]').text(data.nom),
									p.$w.find('[name=esta]').val(data.esta),
									p.$w.find('[name=cate]').val(data.cate),
									p.$w.find('[name=edad]').text(data.edad),
									p.$w.find('[name=sexo]').text(data.sexo),
									p.$w.find('[name=proce]').text(data.proce),

									


					K.unblock();
				},'json');
			}
		});
	},

};

define(
	['ad/paci','ad/dini','ad/padi'],
	function(adpaci,addini,adpadi ){
		return adCons;
	}
);