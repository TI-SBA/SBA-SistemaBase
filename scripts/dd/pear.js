ddPear = {
		
dbRel: function(item){
		return {
			_id: item._id.$id,
			ndoc: item.ndoc,
			docu: item.docu,
			dire: item.dire,
			ofic: item.ofic,
			tipo: item.tipo

		};
	},
	init: function(){
		K.initMode({
			mode: 'dd',
			action: 'ddPear',
			titleBar: {
				title: 'Pedido de Documentos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Nro. Documento','Documento','Direccion','Oficina','Tipo de Documento'],
					data: 'dd/pear/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ddPear.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.ndoc+'</td>');
						$row.append('<td>'+data.docu+'</td>');
						$row.append('<td>'+data.dire+'</td>');
						$row.append('<td>'+data.ofic+'</td>');
						$row.append('<td>'+data.tipo+'</td>');
						//$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.modificado)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							ddPear.windowDetails({_id: $(this).data('id'),titu: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenPear", {
							onShowMenu: function($row, menu) {
								/*
								$('#conMenPear_ver',menu).remove();
								if($row.data('estado')=='D') $('#conMenPear_hab',menu).remove();
								else $('#conMenPear_edi,#conMenPear_des',menu).remove();
								return menu;
								*/
							},
							bindings: {
								'conMenPear_edi': function(t) {
									ddPear.windowEdit({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPear_print': function(t) {
									ddPear.windowDetails({id: K.tmp.data('id'),titu: K.tmp.find('td:eq(2)').html()});
								},
								'conMenPear_print':function(t){
									K.windowPrint({
										id:'windowPrint',
										title:"Archivo Pedido",
										url:"dd/pear/print?_id="+K.tmp.data("id")
									});
								},
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
		ddRegi.windowSelect({callback: function(regi){
			new K.Panel({
				title: 'Nueva Pedido',
				contentURL: 'dd/pear/edit',
				width: 500,
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
   										ndoc: p.$w.find('[name=ndoc]').text(),
										dire: p.$w.find('[name=dire]').text(),
										ofic: p.$w.find('[name=ofic]').text(),
										docu: p.$w.find('[name=docu]').text(),
										tipo: p.$w.find('[name=tipo]').text(),
										disol: p.$w.find('[name=disol]').text(),
										ofsol: p.$w.find('[name=ofsol]').text(),
										nsol: p.$w.find('[name=nsol]').val(),
										asun: p.$w.find('[name=asun]').val(),
									};
									if(data.disol==''){
										p.$w.find('[name=disol]').focus();
										return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Direccion Solicitante!',type: 'error'});
									}
									if(data.ofsol==''){
										p.$w.find('[name=ofsol]').focus();
										return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la Oficina Solicitante!',type: 'error'});
									}

									p.$w.find('#div_buttons button').attr('disabled','disabled');
									$.post("dd/pear/save",data,function(result){
											K.clearNoti();
											K.msg({title: ciHelper.titles.regiGua,text: "Pedido Agregada!"});
											ddPear.init();
										
									},'json');
								}
							}).submit();
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							ddPear.init();
						}
					}
				},
				onContentLoaded: function(){
					p.$w = $('#mainPanel');
					p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=disol]').html(data.nomb).data('data',data);
							},bootstrap: true});
					});

					p.$w.find("[name=btnOfic]").click(function(){
					mgOfic.windowSelect({callback: function(data){
						p.$w.find('[name=ofsol]').html(data.nomb).data('data',data);
							},bootstrap: true});
					});
					$.post('dd/pear/get_nro',function(data){
						var n=0;
						if(data==null){
							n=1;
						}else{
							n=parseFloat(data.nsol) + 1;
						}
						p.$w.find('[name=nsol]').val(n);
					},'json');

					p.$w.find('[name=ndoc]').html(regi.ndoc).data('data',regi);
					p.$w.find('[name=docu]').html(regi.titu).data('data',regi);
					p.$w.find('[name=dire]').html(regi.dire).data('data',regi);
					p.$w.find('[name=ofic]').html(regi.ofic).data('data',regi);
					p.$w.find('[name=tipo]').html(regi.docu).data('data',regi);
				}
			});
		}})
	},
	
};
define(
	['mg/ofic','dd/regi','mg/prog'],
	function(mgOfic,ddRegi,mgProg){
		return ddPear;
	}
);
