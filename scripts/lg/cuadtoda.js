/*******************************************************************************
Cuadro de necesidades de todas las dependencias */
lgCuadToda = {
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCuadToda',
			titleBar: {
				title: 'Cuadro de Necesidades de Todas las Dependencias'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'A&ntilde;o',f:'periodo'},{n:'Modificado por',f:'trabajador.fullname'},{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'},''],
					data: 'lg/cuad/lista_all',
					params: {periodo: $('#mainPanel [name=periodo]').val()},
					itemdescr: 'cuadro(s)',
					toolbarHTML: '<input type="number" name="periodo" />&nbsp;<button name="btnVer" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Generar Consolidado</button>',
					onContentLoaded: function($el){
						$el.find('[name=periodo]').numeric().val(ciHelper.date.getYear())
							.change(function(){
								$grid.reinit({params:{periodo: $('#mainPanel [name=periodo]').val()}});
							}).keyup(function(){
								$(this).change();
							});
						$el.find('[name=btnVer]').click(function(){
							window.open("lg/cuad/consolidado?periodo="+$el.find('[name=periodo]').val());
							//lgCuad.windowCuadCons({periodo: $el.find('[name=periodo]').val()});
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					stopLoad: true,
					afterLoad: function(p){
						p.reinit({params:{periodo: $('#mainPanel [name=periodo]').val()}});
					},
					fill: function(data,$row){
						$row.append('<td>');
						if(data.estado=='A'&&data.vigente==true) $row.append('<td><span class="label label-success">Vigente</span></td>');
						else if(data.estado=='P'||data.estado=='E') $row.append('<td><span class="label label-default">Pendiente</span></td>');
						else $row.append('<td><span class="label label-warning">No Vigente</span></td>');
						$row.append('<td>'+data.periodo+'</td>');
						$row.append('<td>'+data.organizacion.nomb+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							if($(this).data('estado')!='A') lgCuad.windowDetails({id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html(),goBack: function(){
									lgCuadToda.init();
								}});
							else lgCuad.windowDetailsProg({id: $(this).data('id'),nomb: K.session.enti.roles.trabajador.organizacion.nomb,goBack: function(){
									lgCuadToda.init();
								}});
						}).data('estado',data.estado).data('data',data).contextMenu("conMenLgCuad", {
							onShowMenu: function($row, menu) {
								$('#conMenLgCuad_env,#conMenLgCuad_eli',menu).remove();
								if($row.data('estado')!='E') $('#conMenLgCuad_edi,#conMenLgCuad_apr',menu).remove();
								if($row.data('estado')!='A') $('#conMenLgCuad_amp,#conMenLgCuad_vig',menu).remove();
								if($row.data('estado')=='A'&&$row.data('data').vigente==true) $('#conMenLgCuad_vig',menu).remove();
								if($row.data('estado')=='A'&&$row.data('data').vigente==false) $('#conMenLgCuad_amp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenLgCuad_ver': function(t) {
									lgCuad.windowDetailsProg({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(4)').html(),goBack: function(){
										lgCuadToda.init();
									}});
								},
								'conMenLgCuad_edi': function(t) {
									lgCuad.windowEditProg({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(4)').html(),goBack: function(){
										lgCuadToda.init();
									}});
								},
								'conMenLgCuad_apr': function(t) {
									if(K.tmp.data('data').fecvig!=null){
										K.sendingInfo();
										var aprob = {
											trabajador: {
												_id: K.session.enti._id.$id,
												tipo_enti: K.session.enti.tipo_enti,
												nomb: K.session.enti.nomb
											}
										};
										if(aprob.trabajador.tipo_enti=='P'){
											aprob.trabajador.appat = K.session.enti.appat;
											aprob.trabajador.apmat = K.session.enti.apmat;
										}
										$.post('lg/cuad/save',{_id: K.tmp.data('id'),estado: 'A',aprobacion: aprob},function(){
											K.clearNoti();
											$grid.reinit();
											K.notification({title: 'Cuadro de Necesidades Aprobado',text: 'La aprobaci&oacute;n del cuadro se realiz&oacute; con &eacute;xito!'});
										});
										/*
										 * Se debe enviar una notificacion al personal de logistica que puede ver los cuadros
										 */
									}else
										K.notification({title: 'Cuadro no revisado',text: 'Debe editar el cuadro y revisarlo antes de su aprobaci&oacute;n!',type: 'error'});
								},
								'conMenLgCuad_vig': function(t) {
									K.sendingInfo();
									$.post('lg/cuad/vigente',{
										_id: K.tmp.data('id'),
										orga: K.tmp.data('data').organizacion._id.$id
									},function(){
										K.clearNoti();
										$grid.reinit();
										K.notification({title: 'Cuadro de Necesidades en Vigencia',text: 'El Cuadro seleccionado ahora est&aacute; vigente!'});
									});
								},
								'conMenLgCuad_amp': function(t) {
									lgCuad.windowAmpli({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(4)').html(),goBack: function(){
										lgCuadToda.init();
									}});
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
		return lgCuadToda;
	}
);