/*operesiones Recientes */
cmOperAll = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmOperAll',
			titleBar: { title: 'Todas las Operaciones'}
		});

		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','','Operaci&oacute;n',{n:'Recibo',f:'recibo.cliente.fullname'},{n:'Espacio',f:'espacio.nomb'},{n:'Registrado',f:'fecreg'},/*{n:'Programado',f:'programacion.fecprog'},{n:'Ejecutado',f:'ejecucion.fecini'},'Actualizado por'*/],
					width: [10,40,300,300,300,50],
					data: 'cm/oper/lista',
					params: {oper:$('#mainPanel [name=oper] option:selected').val()},
					itemdescr: 'operacion(es)',
					toolbarHTML: '<button name="btnOperacion">Nueva Operaci&oacute;n</button>'+
						'<br />'+
						'<select name="progra"><option value="2">Todos</option><option value="0">Creadas</option><option value="1">Programadas</option></select>'+
						'<select name="oper">'+
							'<option value="">--</option>'+
							'<option value="CN">Concesi&oacute;n</option>'+
							'<option value="IN">Inhumaci&oacute;n</option>'+
							'<option value="TR">Traslado</option>'+
							'<option value="AD">Adjuntaci&oacute;n</option>'+
							'<option value="CO">Construcci&oacute;n</option>'+
							'<option value="CL">Colocaci&oacute;n</option>'+
						'</select>'+
						'Mostrar operaciones <span name="divOpers">'+
							'<input type="radio" id="rbtnOperT" name="rbtnOper" value="T" checked="checked"><label for="rbtnOperT">Todas</label>'+
							'<input type="radio" id="rbtnOperF" name="rbtnOper" value="F"><label style="display: none;" name="labelrbtnOperF" for="rbtnOperF">Filtrar por d&iacute;a</label>'+
						'</span>'+
						'<span name="spanfecprog"></span><input type="text" name="buscar">',
					onContentLoaded: function($el){
						$el.find('[name=oper]').change(function(){
							$el.find('[name=spanfecprog],[name=buscar],img:eq(0)').hide();
							$grid.reinit({params:{
								oper:$(this).find('option:selected').val(),
								progra: $el.find('[name=progra] option:selected').val()
								}
							});
						});
						$el.find('[name=btnOperacion]').button({icons: {primary: 'ui-icon-carat-1-s'}});
						cmOper.contextMenuOper({$this: $el.find('[name=btnOperacion]')});
						$el.find('[name=divOpers]').buttonset();
						$el.find('#rbtnOperT').click(function(){
							$el.find('[name=spanfecprog],[name=progra],[name=buscar],img:eq(0)').hide();
							$grid.reinit({params: {
								fecbus: $el.find('[name=buscar]').val(),
								progra: $el.find('[name=progra] option:selected').val(),
								oper: $el.find('[name=oper] option:selected').val()
							}});
						});
						$el.find('#rbtnOperF').click(function(){
							$el.find('[name=spanfecprog],[name=progra],[name=buscar],img:eq(0)').show();
							$el.find('[name=buscar]').change();
						});
						var date = new Date();
						$el.find('[name=spanfecprog]').html(' Filtrar por d&iacute;a: ');
						$el.find('[name=buscar]').datepicker({
							dateFormat: 'yy-mm-dd'
						}).val(date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()).change(function(){
							$grid.reinit({params:{
							 	progra: $el.find('[name=progra] option:selected').val(),
								fecbus:$(this).val(),
								oper:$el.find('[name=oper] option:selected').val()
							}});
						});
						$el.find('[name=spanfecprog],[name=buscar],img:eq(0)').hide();
						$el.find('[name=progra]').change(function(){
							$el.find('[name=oper]').val("");
							$el.find('[name=spanfecprog],[name=buscar],img:eq(0)').hide();
							if($el.find('[name=progra] option:selected').val()==2)
								$el.find('[name=labelrbtnOperF').hide();
							else
								$el.find('[name=labelrbtnOperF]').show();

							$grid.reinit({params: {
								//fecbus: $el.find('[name=buscar]').val(),
								progra: $el.find('[name=progra] option:selected').val(),
								//oper: $el.find('[name=oper] option:selected').val()
							}});
						});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>&nbsp;&nbsp;</td>');
						if(data.ejecucion==null && data.programacion==null)
							$row.find('td:last').css('background','blue').addClass('vtip').attr('title','Registrado');
						else if(data.programacion!=null && data.ejecucion==null){
							$row.find('td:last').css('background','orange').addClass('vtip').attr('title','Pendiente');
						}else{
							if(data.recibos!=null)
								$row.find('td:last').css('background','blue').addClass('vtip').attr('title','Ejecutado');
							else{
								if(data.ocupante_anterior==null)
									$row.find('td:last').css('background','orange').addClass('vtip').attr('title','Pendiente');
								else
									$row.find('td:last').css('background','blue').addClass('vtip').attr('title','Ejecutado');
							}
						}
						if(data.anulacion!=null)
							$row.find('td:last').css('background','black').addClass('vtip').attr('title','Anulado');
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');

						if(data.concesion != null)
							$row.append('<td>Concesi&oacute;n</td>');
						if(data.construccion != null)
							$row.append('<td>Construcci&oacute;n</td>');
						if(data.asignacion != null)
							$row.append('<td>Asignaci&oacute;n</td>');
						if(data.inhumacion != null)
							$row.append('<td>Inhumaci&oacute;n</td>');
						if(data.traspaso != null)
							$row.append('<td>Traspaso</td>');
						if(data.traslado != null)
							$row.append('<td>Traslado</td>');
						if(data.colocacion != null)
							$row.append('<td>Colocaci&oacute;n</td>');
						if(data.adjuntacion != null)
							$row.append('<td>Adjuntaci&oacute;n</td>');
						if(data.ampliacion != null)
							$row.append('<td>Ampliaci&oacute;n</td>');
						if(data.conversion != null)
							$row.append('<td>Conversi&oacute;n</td>');


						if(data.ocupante!=null)
							$row.find('td:last').append('<br /><b>'+mgEnti.formatName(data.ocupante)+'</b>');
						if(data.colocacion!=null){
							for(var ta=0,tb=data.colocacion.accesorios.length; ta<tb; ta++){
								$row.find('td:last').append('<br /><b>'+data.colocacion.accesorios[ta].nomb+'</b>');
							}
						}
						if(data.concesion!=null){
							if(data.concesion.condicion=='P')
								$row.find('td:last').append(' <b>Permanente</b>');
							else
								$row.find('td:last').append(' <b>Temporal ('+ciHelper.date.format.bd_ymd(data.concesion.fecven)+')</b>');
						}
						if(data.programacion!=null){
							if(data.ejecucion!=null)
								$row.find('td:last').append('<br />Programado para <b style="color:blue;text-decoration: underline;">'+ciHelper.date.format.bd_ymdhi(data.programacion.fecprog)+'</b>');
							else
								$row.find('td:last').append('<br />Programado para <b style="color:orange;text-decoration: underline;">'+ciHelper.date.format.bd_ymdhi(data.programacion.fecprog)+'</b>');
						}
						if(data.inhumacion!=null){
							if(data.inhumacion.puerta!=null){
								$row.find('td:last').append('<br />Ingreso por la puerta <b>'+data.inhumacion.puerta+'</b>');
							}
						}

						if(data.recibo!=null){
							$row.append('<td>RC '+data.recibo.serie+'-'+data.recibo.num+'<br />'+
								'<b>'+mgEnti.formatName(data.recibo.cliente)+'</b>');
						}else if(data.recibos!=null){
							if(data.recibos.length>0){
								if(data.recibos[0].total!=null)
									$row.append('<td>RC '+data.recibos[0].serie+'-'+data.recibos[0].num+'<br />'+
										'Por un Total de <i>'+ciHelper.formatMon(data.recibos[0].total)+'</i><br />'+
										'<b>'+mgEnti.formatName(data.recibos[0].cliente)+'</b>');
								else
									$row.append('<td>RC '+data.recibos[0].serie+'-'+data.recibos[0].num+'<br />'+
									'<b>'+mgEnti.formatName(data.recibos[0].cliente)+'</b>');
							}else{
								$row.append('<td />');
							}
						}else if(data.ocupante_anterior!=null){
							$row.append('<td>Registrada por <b>Ocupante Anterior</b></td>');
						}else
							$row.append('<td>');

						if(data.propietario.doc!=null){
								if(typeof data.espacio ==='undefined' || data.espacio ==null){
									var nameEspacio="No definido";
								}
								else {
									var nameEspacio=data.espacio.nomb;
								}
								if(typeof data.propietario ==='undefined' || data.propietario ==null)
								{
									var namePropietario="Sin propietario";
								}
								else {
									var namePropietario=mgEnti.formatName(data.propietario);
								}
								if(typeof data.propietario.doc ==='undefined' || data.propietario.doc ==null)
								{
									var dataDoc="Documento indefinido";
								}
								else {
									var dataDoc=data.propietario.doc;
								}
							$row.append('<td>'+nameEspacio+
								'<br />Propietario: <b>'+namePropietario+
								' ('+dataDoc+')'+
								'</b></td>');

						} else{
							if(typeof data.espacio ==='undefined' || data.espacio ==null)
							{
								var nameEspacio="No definido";
							}
							else {
								var nameEspacio=data.espacio.nomb;
							}
							if(typeof data.propietario ==='undefined' || data.propietario ==null)
							{
								var namePropietario="Sin propietario";
							}
							else {
								var namePropietario=mgEnti.formatName(data.propietario);
							}
							$row.append('<td>'+nameEspacio+
							'<br />Propietario: <b>'+namePropietario+
							'</b></td>');

						}

						$row.append('<td>'+ciHelper.dateFormat(data.fecreg)+'</td>');
						/*if(data.programacion!=null)
							$row.append('<td>'+ciHelper.dateFormat(data.programacion.fecprog)+'</td>');
						else $row.append('<td></td>');
						if(data.ejecucion!=null)
							$row.append('<td>'+ciHelper.dateFormat(data.ejecucion.fecini)+'</td>');
						else $row.append('<td></td>');
						//$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');*/
						$row.attr('id',data._id.$id);
						$row.data('data',data).dblclick(function(){
							var result = $(this).data('data');
							cmOper.showDetails({data: result});
						}).contextMenu('conMenCmOperlist', {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').programacion==null){
									$('#conMenCmOperExe_eje,#conMenCmOperExe_amp,#conMenCmOperExe_cons',menu).remove();
								}else{
									if(K.tmp.data('data').ejecucion!=null){
										$('#conMenCmOperExe_eje,#conMenCmOperExe_amp,#conMenCmOperExe_cons',menu).remove();
									}else{
										if(K.tmp.data('data').construccion!=null){
											$('#conMenCmOperExe_eje,#conMenCmOperExe_amp',menu).remove();
										}else if(K.tmp.data('data').ampliacion!=null){
											$('#conMenCmOperExe_cons,#conMenCmOperExe_eje',menu).remove();
										}else{
											$('#conMenCmOperExe_cons,#conMenCmOperExe_amp',menu).remove();
										}
									}
								}

								if(K.tmp.data('data').estado!='X'){
									$('#conMenCmOperList_eli',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenCmOperList_Det': function(t) {
									cmOper.showDetails({data: K.tmp.data('data')});
								},
								'conMenCmOperExe_eje': function(t) {
									cmOper.executeOper({id: K.tmp.data('data')._id.$id});
								},
								'conMenCmOperExe_cons': function(t) {
									cmOper.executeCons({id: K.tmp.data('data')._id.$id,data: K.tmp.data('data')});
								},
								'conMenCmOperExe_amp': function(t) {
									cmOper.executeAmp({id: K.tmp.data('data')._id.$id,data: K.tmp.data('data')});
								},
								'conMenCmOperList_ope': function(){
									cmOper.newOper({data: K.tmp.data('data').espacio});
								},
								'conMenCmOperList_eli': function(){
									ciHelper.confirm('&#191;Desea <b>Eliminar</b> la operaci&oacute;n <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('cm/oper/eliminar',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Operaci&oacute;n Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											cjCompRec.init();
										});
									},function(){
										$.noop();
									},'Eliminaci&oacute;n de operaci&oacute;n');
								},
							}
						});
						return $row;
					}
				});
			}
		});
	},
};
define(
	['cm/operpro','cm/oper'],
	function(cmOperPro,cmOper){
		return cmOperAll;
	}
);
