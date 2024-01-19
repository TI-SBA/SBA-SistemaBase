/*operesiones Recientes */
cmOperPro = {
	init: function(){
		if($('#pageWrapper [child=oper]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('cm/navg/oper',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="oper" />');
					$p.find("[name=cmOper]").after( $row.children() );
				}
				$p.find('[name=cmOper]').data('oper',$('#pageWrapper [child=oper]:first').data('oper'));
				$p.find('[name=cmOperPro]').click(function(){ cmOperPro.init(); }).find('ul').addClass('ui-state-highlight');
				$p.find('[name=cmOperAll]').click(function(){ cmOperAll.init(); });
			},'json');
		}
		K.initMode({
			mode: 'cm',
			action: 'cmOperPro',
			titleBar: { title: 'Operaciones Programadas'}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Espacio',f:'espacio.nomb'},'Operaci&oacute;n',{n:'Datos de Pago',f:'recibo.cliente.fullname'},{n:'Registrado',f:'fecreg'},{n:'Hora',f:'programacion.fecprog'},'Actualizado por'],
					width: [40,300,300,300,100,50],
					data: 'cm/oper/listaprog',
					params: {
						all:1
					},
					itemdescr: 'operacion(es)',
					toolbarHTML: '<button name="btnOperacion">Nueva Operaci&oacute;n</button>'+
						'Mostrar operaciones <span name="divOpers">'+
							'<input type="radio" id="rbtnOperT" name="rbtnOper" value="T"><label for="rbtnOperT">Todas</label>'+
							'<input type="radio" id="rbtnOperF" name="rbtnOper" value="F" checked="checked"><label for="rbtnOperF">Filtrar por d&iacute;a</label>'+
						'</span>'+
						'<span name="spanfecprog"></span><input type="text" name="buscar">'+
						'<select name="oper">'+
							'<option value="">--</option>'+
							'<option value="IN">Inhumaci&oacute;n</option>'+
							'<option value="TR">Traslado</option>'+
							'<option value="AD">Adjuntaci&oacute;n</option>'+
							'<option value="CO">Construcci&oacute;n</option>'+
						'</select>',
					onContentLoaded: function($el){
						cmOper.contextMenuOper({$this: $el.find('[name=btnOperacion]')});
						$el.find('[name=btnOperacion]').button({icons: {primary: 'ui-icon-carat-1-s'}});
						$el.find('[name=divOpers]').buttonset();
						$el.find('#rbtnOperT').click(function(){
							$el.find('[name=spanfecprog],[name=buscar]').hide();
							$grid.reinit({params: {all:true,oper:$el.find('[name=oper] option:selected').val()}});
						});
						$el.find('#rbtnOperF').click(function(){
							$el.find('[name=spanfecprog],[name=buscar]').show();
						});
						//$el.find('[name=spanfecprog],[name=buscar]').hide();
						$el.find('[name=obj]').html( 'operaciones programadas' );
						var date = new Date();
						$el.find('[name=spanfecprog]').html(' Filtrar por d&iacute;a: ');
						$el.find('[name=buscar]').datepicker({
							dateFormat: 'yy-mm-dd'
						}).val(date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()).change(function(){
							$grid.reinit({params:{fecbus:$(this).val(),oper:$el.find('[name=oper] option:selected').val()}});
							//$grid.reinit();
						});
						$el.find('[name=oper]').change(function(){
							if($el.find('[name=rbtnOper]:checked').val()=='F')
								$grid.reinit({params:{fecbus:$el.find('[name=buscar]').val(),oper:$(this).find('option:selected').val()}});
							else
								$grid.reinit({params:{all:1,oper:$(this).find('option:selected').val()}});
						});
						//$grid.reinit({params:{fecbus:$el.find('[name=buscar]').val()}});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					stopLoad: true,
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						//$row.append('<td>'+data.espacio.nomb+'</td>');
						$row.append('<td>'+data.espacio.nomb+
							'<br />Propietario: <b>'+mgEnti.formatName(data.propietario)+'</b></td>');
						if(data.concesion != null)
							$row.append('<td>Concesi&oacute;n</td>');
						if(data.construccion != null)
							$row.append('<td>Construcci&oacute;n</td>');
						if(data.asignacion != null)
							$row.append('<td>Asignaci&oacute;n</td>');
						if(data.inhumacion != null)
							$row.append('<td>Inhumaci&oacute;n</td>');
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
							$row.find('td:last').append('<br />Ocupante: <b>'+mgEnti.formatName(data.ocupante)+'</b>');
						else if(data.colocacion!=null){
							for(var ta=0,tb=data.colocacion.accesorios.length; ta<tb; ta++){
								$row.find('td:last').append('<br /><b>'+data.colocacion.accesorios[ta].nomb+'</b>');
							}
						}else if(data.concesion!=null){
							if(data.concesion.condicion=='P')
								$row.find('td:last').append('<br />Tipo: <b>Permanente</b>');
							else
								$row.find('td:last').append('<br />Tipo: <b>Temporal ('+ciHelper.date.format.bd_ymd(data.concesion.fecven)+')</b>');
						}
						if(data.recibo!=null){
							$row.append('<td>RC '+data.recibo.serie+'-'+data.recibo.num+'<br />'+
								'<b>'+mgEnti.formatName(data.recibo.cliente)+'</b>');
						}else if(data.recibos!=null){
							$row.append('<td>RC '+data.recibos[0].serie+'-'+data.recibos[0].num+'<br />'+
								'<b>'+mgEnti.formatName(data.recibos[0].cliente)+'</b>');
						}else
							$row.append('<td>');
						$row.append('<td>'+ciHelper.dateFormat(data.fecreg)+'</td>');
						$row.append('<td>'+ciHelper.dateFormat(data.programacion.fecprog)+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.attr('id',data._id.$id);
						$row.data('data',data).dblclick(function(){
							if($(this).data('data').construccion!=null){
								cmOper.executeCons({id: $(this).data('data')._id.$id,data: $(this).data('data')});
							}else{
								cmOper.executeOper({id: $(this).data('data')._id.$id});
							}
						}).contextMenu('conMenCmOperExe', {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').construccion!=null){
									$('#conMenCmOperExe_eje,#conMenCmOperExe_amp',menu).remove();
								}else if(K.tmp.data('data').ampliacion!=null){
									$('#conMenCmOperExe_cons,#conMenCmOperExe_eje',menu).remove();
								}else{
									$('#conMenCmOperExe_cons,#conMenCmOperExe_amp',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenCmOperExe_eje': function(t) {
									cmOper.executeOper({id: K.tmp.data('data')._id.$id});
								},
								'conMenCmOperExe_cons': function(t) {
									cmOper.executeCons({id: K.tmp.data('data')._id.$id,data: K.tmp.data('data')});
								},
								'conMenCmOperExe_amp': function(t) {
									cmOper.executeAmp({id: K.tmp.data('data')._id.$id,data: K.tmp.data('data')});
								}
							}
						});
						return $row;
					}
				});
				$grid.reinit({params:{fecbus:$('#mainPanel [name=buscar]').val()}});
				//$grid.reinit();
			}
		});
	}
};
define(
	['cm/oper','cm/operall'],
	function(cmOper,cmOperAll){
		return cmOperPro;
	}
);