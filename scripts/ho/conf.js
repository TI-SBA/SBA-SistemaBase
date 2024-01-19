hoConf = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'mh',
			action: 'hoConf',
			titleBar: {
				title: 'Configuraci&oacute;n de Hospitalizaci&oacute;n - Moises Heresi'
			}
		});
		
		new K.Panel({
			contentURL: 'cj/cuen/conf_hosp',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							cod: 'HO',
							rehab: []
						};
						var tmp = p.$w.find('[name=serv_hosp]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnHosp]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Cuenta de Hospitalizaci&oacute;n!',
								type: 'error'
							});
						}else
							data['HOSP'] = ctPcon.dbRel(tmp);
						var tmp = p.$w.find('[name=serv_agri]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnAgri]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Servicio de Agricultura!',
								type: 'error'
							});
						}else
							data['AGRI'] = mgServ.dbRel(tmp);
						var tmp = p.$w.find('[name=serv_gana]').data('data');
						if(tmp==null){
							p.$w.find('[name=btnGana]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger un Servicio de Ganaderia!',
								type: 'error'
							});
						}else
							data['GANA'] = mgServ.dbRel(tmp);
						if(p.$w.find('.col-sm-6:eq(3) .item').length==0){
							p.$w.find('.col-sm-6:eq(3) [name=btnAgregar]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe escoger como m&iacute;nimo una cuenta!',
								type: 'error'
							});
						}else{
							for(var i=0,j=p.$w.find('.col-sm-6:eq(3) .item').length; i<j; i++){
								data.rehab.push(ctPcon.dbRel(p.$w.find('.col-sm-6:eq(3) .item').eq(i).data('data')));
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('cj/cuen/save_config_hosp',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Configuracion ha sido actualizada con &eacute;xito!'});
							p.$w.find('#div_buttons button').removeAttr('disabled');
						});
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('label').css('color','green');
				p.$w.find('[name=btnHosp]').click(function(){
					var $this = $(this);
					ctPcon.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=serv_hosp]').html(data.cod).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnAgri]').click(function(){
					var $this = $(this);
					mgServ.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=serv_agri]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				p.$w.find('[name=btnGana]').click(function(){
					var $this = $(this);
					mgServ.windowSelect({callback: function(data){
						$this.closest('.input-group').find('[name=serv_gana]').html(data.nomb).data('data',data);
					},bootstrap: true});
				});
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['','Cod.','Descripci&oacute;n'],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info" name="btnAgregar"><i class="fa fa-plus"></i> Agregar Cuenta</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ctPcon.windowSelect({
								bootstrap: true,
								callback: function(data){
									var $row = $('<tr class="item" id="'+data._id.$id+'">');
									$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
									$row.append('<td>'+data.cod+'</td>');
									$row.append('<td>'+data.descr+'</td>');
									$row.find('[name=btnEli]').click(function(){
										$(this).closest('.item').remove();
									});
									$row.data('data',data);
									p.$w.find('[name=grid] tbody').append($row);
								}
							});
						});
					}
				});
				$.post('cj/cuen/get_config_hosp',function(data){
					if(data.HOSP!=null){
						p.$w.find('[name=serv_hosp]').html(data.HOSP.cod).data('data',data.HOSP);
					}
					if(data.AGRI!=null){
						p.$w.find('[name=serv_agri]').html(data.AGRI.nomb).data('data',data.AGRI);
					}
					if(data.GANA!=null){
						p.$w.find('[name=serv_gana]').html(data.GANA.nomb).data('data',data.GANA);
					}
					if(data.rehab!=null){
						for(var i=0,j=data.rehab.length; i<j; i++){
							var $row = $('<tr class="item" id="'+data.rehab[i]._id.$id+'">');
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							$row.append('<td>'+data.rehab[i].cod+'</td>');
							$row.append('<td>'+data.rehab[i].descr+'</td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							$row.data('data',data.rehab[i]);
							p.$w.find('[name=grid] tbody').append($row);
						}
					}
					K.unblock();
				},'json');
			}
		});
	}
};
define(
	['ct/pcon','mg/mult','mg/serv'],
	function(ctPcon,mgMult,mgServ){
		return hoConf;
	}
);