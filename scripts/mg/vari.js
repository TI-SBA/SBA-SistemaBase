/*******************************************************************************
variables */
mgVari = {
	init: function(){
		K.initMode({
			mode: 'mg',
			action: 'mgVari',
			titleBar: {
				title: 'Variables Globales'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'C&oacute;digo',f:'cod'},{n:'Nombre',f:'nomb'},'Valor.',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},{n:'Modificado por',f:'trabajador.fullname'}],
					data: 'mg/vari/lista',
					params: {},
					itemdescr: 'variable(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.valor+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							mgVari.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(1)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									mgVari.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
								},
								'conMenListEd_edi': function(t) {
									mgVari.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(1)').html()});
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
		new K.Modal({
			id: 'windowEdit',
			title: 'Editar Variable: '+p.nomb,
			contentURL: 'mg/vari/edit',
			width: 500,
			height: 170,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							valor: p.$w.find('[name=valor]').val()
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de variable!',type: 'error'});
						}
						if(data.valor==''){
							p.$w.find('[name=valor]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la variable!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("mg/vari/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Variable actualizada!"});
							mgVari.init();
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
			onContentLoaded: function(){
				p.$w = $('#windowEdit');
				K.block();
				$.post('mg/vari/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=cod]').val(data.cod).attr('disabled','disabled');
					p.$w.find('[name=valor]').val(data.valor);
					K.unblock();
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Panel({
			title: 'Detalles de Variable: '+p.nomb,
			contentURL: 'mg/vari/edit',
			buttons: {
				"Regresar": {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						mgVari.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('form').after('</hr>');
				p.$w.find('form').after('<div name="gridVal">');
				new K.grid({
					$el: p.$w.find('[name=gridVal]'),
					cols: ['Fecha','Valor'],
					search: false,
					onlyHTML: true,
					stopLoad: true
				});
				$.post('mg/vari/get',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb).attr('disabled','disabled');
					p.$w.find('[name=cod]').val(data.cod).attr('disabled','disabled');
					p.$w.find('[name=valor]').val(data.valor).attr('disabled','disabled');
					if(data.historico!=null){
						for(var i=0; i<data.historico.length; i++){
							var $row = $('<tr class="item">');
							$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.historico[i].fecreg)+'</td>');
							$row.append('<td>'+data.historico[i].valor+'</td>');
							p.$w.find('[name=gridVal] tbody').append($row);
						}
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return mgVari;
	}
);