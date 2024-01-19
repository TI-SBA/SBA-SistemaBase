/*******************************************************************************
Cuadro de necesidades por dependencia */
lgCuadPord = {
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCuadPord',
			titleBar: {
				title: 'Cuadro de Necesidades por Dependencia'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'A&ntilde;o',f:'periodo'},{n:'Modificado por',f:'trabajador.fullname'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'lg/cuad/lista',
					params: {},
					itemdescr: 'cuadro(s)',
					toolbarHTML: '<span name="dependencia"></span>&nbsp;<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Cuadro</button>',
					onContentLoaded: function($el){
						$el.find('[name=dependencia]').html(K.session.enti.roles.trabajador.oficina.nomb);
						$el.find('[name=btnAgregar]').click(function(){
							lgCuad.windowNew({goBack: function(){
								lgCuadPord.init();
							}});
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						if(data.estado=='A'&&data.vigente==true) $row.append('<td><span class="label label-success">Vigente</span></td>');
						else if(data.estado=='P'||data.estado=='E') $row.append('<td><span class="label label-default">Pendiente</span></td>');
						$row.append('<td>'+data.periodo+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							if($(this).data('estado')!='A'){
								lgCuad.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
									lgCuadPord.init();
								}});
							}else{
								lgCuad.windowDetailsProg({id: $(this).data('id'),nomb: K.session.enti.roles.trabajador.organizacion.nomb,goBack: function(){
									lgCuadPord.init();
								}});
							}
						}).data('estado',data.estado).contextMenu("conMenLgCuad", {
							onShowMenu: function($row, menu) {
								$('#conMenLgCuad_apr,#conMenLgCuad_vig',menu).remove();
								if($row.data('estado')!='P') $('#conMenLgCuad_amp,#conMenLgCuad_edi,#conMenLgCuad_env,#conMenLgCuad_eli',menu).remove();
								else if($row.data('estado')=='P') $('#conMenLgCuad_amp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgCuad_ver': function(t) {
									if(K.tmp.data('estado')!='A'){
										lgCuad.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
											lgCuadPord.init();
										}});
									}else{
										lgCuad.windowDetailsProg({id: K.tmp.data('id'),nomb: K.session.enti.roles.trabajador.organizacion.nomb,goBack: function(){
											lgCuadPord.init();
										}});
									}
								},
								'conMenLgCuad_edi': function(t) {
									lgCuad.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html(),goBack: function(){
										lgCuadPord.init();
									}});
								},
								'conMenLgCuad_env': function(t) {
									ciHelper.confirm('&#191;Desea <b>Enviar</b> el Cuadro de Necesidades del Periodo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/cuad/save',{_id: K.tmp.data('id'),estado: 'E'},function(){
											K.clearNoti();
											K.notification({title: 'Cuadro de Necesidades Enviado',text: 'El envio del cuadro se realiz&oacute; con &eacute;xito!'});
											lgCuadPord.init();
											/*
										 * Se debe enviar una notificacion al personal de logistica que puede ver los cuadros
										 */
										});
									},function(){
										$.noop();
									},'Envio a revisi&oacute;n');
								},
								'conMenLgCuad_xls': function(t) {
									window.open("lg/cuad/excel?_id="+K.tmp.data('id'));
								},
								'conMenLgCuad_eli': function(t){
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Cuadro <b>'+$row.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('lg/cuad/delete',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.msg({title: 'Cuadro Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											lgCuadPord.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de Cuadro de Alquiler');
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
	['lg/cuad'],
	function(lgCuad){
		return lgCuadPord;
	}
);