mhEvol = {
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
			fevo: item.fevo,
			evol:item.fevo
		};
	},
	init: function(){
		K.initMode({
			mode: 'mh',
			action: 'mhEvol',
			titleBar: {
				title: 'Evolucion Medica'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Paciente','Fecha de Evolucion','Evolucion Medica',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'mh/Evol/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							mhEvol.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.paci+'</td>');
						$row.append('<td>'+data.evolucion[data.evolucion.length-1].fevo+'</td>');
						$row.append('<td>'+data.evolucion[data.evolucion.length-1].evol+'</td>');
						/*
						var fec;
						if(fec){
							if(fec.length>0){
								fec = data.evolucion[data.evolucion.length-1].fevo;
							}
						}
						
						*/
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							mhEvol.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenFMedica", {
							onShowMenu: function($row, menu) {
								$('#conMenFMedica_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenFMedica_hab',menu).remove();
								else $('#conMenFMedica_edi,#conMenFMedica_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenFMedica_info': function(t) {
									mhEvol.windowDetails({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},
				 				'conMenFMedica_edi': function(t) {
									mhEvol.windowEdit({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
								},

								'conMenFMedica_eli': function(t) {
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Ficha Medica:  <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('mh/Evol/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Evolucion Eliminada',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											mhEvol.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Evolucion');
								},'conMenFMedica_info':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Evolucion Medica",
										url:"mh/Evol/print?_id="+K.tmp.data('id')
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
			id: 'windowNewEvolucion',
			title: 'Nuevo Evolucion Medica',
			contentURL: 'mh/Evol/edit',
			width: 600,
			height: 300,
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
							paci: p.$w.find('[name=paci]').text(),
							evolucion:[]
					};

					if ( p.$w.find('[name=gridEvol] tbody tr').length>0) {
									for(var i=0;i< p.$w.find('[name=gridEvol] tbody tr').length;i++){
										var $row = p.$w.find('[name=gridEvol] tbody tr').eq(i);
										var _evolucion = {
											fevo:$row.find('[name=fevo]').val(),
											evol:$row.find('[name=evol]').val()
											
										}
										data.evolucion.push(_evolucion);
									}
								}

						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/evol/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Evolucion Agregada"});
							mhEvol.init();
							K.closeWindow(p.$w.attr('id'));
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
				p.$w = $('#windowNewEvolucion');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(data.ape + ',' + data.nom).data('data',data);
						
					},bootstrap: true});
				});
				p.$w.find("[name=fevo]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});

					new K.grid({
					$el: p.$w.find('[name=gridEvol]'),
					cols: ['Fecha de Evolucion','Evolucion Medica'],
					stopLoad: true,
					pagination: false,
					search: false,
					store:false,
					onlyHtml: true,
					toolbarHTML: '<button type = "button" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Evolucion</button >',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="fevo"/></td>');
							$row.find('[name=fevo]').val(K.date()).datepicker();
							$row.find('tbody').append($row);
							$row.append('<td><textarea class="form-control" type="text" name="evol"/> </textarea> </td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridEvol] tbody').append($row);
						});
					p.$w.find("[name=fevo]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
					

				}
			});



			}
		});
	},
windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditEvolucionMedica',
			title: 'Editar Evolucion Medica: ' + p.nomb,
			contentURL: 'mh/Evol/edit',
			width: 600,
			height: 300,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							//DATOS DEL EvolENTE
							_id: p.id,
							
								paci:p.$w.find('[name=paci]').text(),
								evolucion:[]

						};
						if ( p.$w.find('[name=gridEvol] tbody tr').length>0) {
							for(var i=0;i< p.$w.find('[name=gridEvol] tbody tr').length;i++){
								var $row = p.$w.find('[name=gridEvol] tbody tr').eq(i);
								var _evolucion = {
									fevo:$row.find('[name=fevo]').val(),
									evol:$row.find('[name=evol]').val()
									
								}
								data.evolucion.push(_evolucion);
							}
						}
						if(data.paci==''){
							p.$w.find('[name=paci]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar el campo faltante!',type: 'error'});
						}
						
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mh/evol/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Evolucion Medica Actualizada!"});
							mhEvol.init();
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
				p.$w = $('#windowEditEvolucionMedica');
				p.$w.find("[name=btnDiag]").click(function(){
					mhPaci.windowSelect({callback: function(data){
						p.$w.find('[name=paci]').html(data.ape + ',' + data.nom).data('data',data);
					},bootstrap: true});
				});

				K.block();
				new K.grid({
					$el: p.$w.find('[name=gridEvol]'),
					cols: ['Fecha de Evolucion','Evolucion Medica'],cols: ['Fecha de Evolucion','Evolucion Medica'],
					stopLoad: true,
					pagination: false,
					search: false,
					store:false,
					toolbarHTML: '<button type = "button" name="btnAddEvolucion" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Pariente</button >',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							var $row = $('<tr class="item">');
							$row.append('<td><input class="form-control" type="text" name="fevo"/></td>');
							$row.find('[name=fevo]').val(K.date()).datepicker();
							$row.find('tbody').append($row);
							$row.append('<td><textarea class="form-control" type="text" name="evol"/> </textarea> </td>');
							$row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							p.$w.find('[name=gridEvol] tbody').append($row);
						});
					p.$w.find("[name=fevo]").datepicker({
	   				format: 'yyyy/mm/dd',
	    			startDate: '-3d'
				});
			}
		});
			     $.post('mh/evol/get',{_id: p.id},function(data){
				    p.$w.find('[name=paci]').text(data.paci);
					

					if(data.evolucion!=null){
						if(data.evolucion.length>0){
							for(var i = 0;i<data.evolucion.length;i++){
								p.$w.find('[name=btnAddEvolucion]').click();
								var $row = p.$w.find('[name=gridEvol] tbody tr:last');
								$row.find('[name=fevo]').val(data.evolucion[i].fevo);
								$row.find('[name=evol]').val(data.evolucion[i].evol);
								
							}
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
};
define(
	['mh/paci'],
	function(mhPaci){
		return mhEvol;
	}
);