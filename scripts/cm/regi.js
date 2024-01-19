/*******************************************************************************
Registros Historicos */
cmRegi = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmRegi',
			titleBar: {
				title: 'Registro Hist&oacute;rico'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: [
						'',
						/*{n:'Ocupante',f:'ocupante'},
						{n:'Espacio',f:'espacio'},
						{n:'Tipo',f:'tipo'},
						{n:'Propietario',f:'propietario'},
						{n:'Recibo',f:'recibo'},
						'Fecha'*/
						
						'Ejecutado',
						'Operacion',
						{n:'Recibo',f:'recibo'},
						{n:'Ocupante',f:'ocupante'},
						{n:'Espacio',f:'espacio.nomb'},
						{n:'Fecha de Emisi&oacute;n del Recibo',f:'fecoper'}
					],
					data: 'cm/oper/lista_hist',
					params: {},
					itemdescr: 'registro(s)',
					toolbarHTML: '<select name="tipo"></select>',
					onContentLoaded: function($el){
						$cbo = $el.find('[name=tipo]');
						$cbo.append('<option value="">Todas las operaciones</option>');
						for(var i=0; i<cmOper.tipo_hist.length; i++){
							$cbo.append('<option value="'+cmOper.tipo_hist[i].cod+'">'+cmOper.tipo_hist[i].label+'</option>');
						}
						$cbo.change(function(){
							$grid.reinit({params: {tipo: $(this).find('option:selected').val()}});
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						if(data.executed==false) $row.append('<td style="color:red;">No ejecutado</td>');
						else $row.append('<td style="color: green;">Ejecutado</td>');
						$row.append('<td>'+cmEspa.tipo_oper[data.tipo]+'</td>');
						if(data.recibo!=null)
							$row.append('<td>'+data.recibo+'</td>');
						else
							$row.append('<td>');
						if(data.ocupante!=null)
							$row.append('<td>'+data.ocupante+'</td>');
						else
							$row.append('<td>');
						if(data.espacio!=null)
							$row.append('<td>'+data.espacio.nomb+'</td>');
						else
							$row.append('<td>--</td>');
						
						
						
						
						
						/*if(data.tipo=='CS'){
							$row.find('td:last').append(' <b>'+data.condicion+'</b>');
						}
						
						
						
						
						
						
						
						$row.append('<td>'+data.propietario+'</td>');
						if(data.recibo!=null)
							$row.append('<td>'+data.recibo+'</td>');
						else
							$row.append('<td>');*/
						if(data.fecoper!=null){
							if($.type(data.fecoper)=='string')
								$row.append('<td>'+data.fecoper+'</td>');
							else	
								$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecoper)+'</td>');
						}else
							$row.append('<td>--</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							cmOper.windowHistDetails({id: $(this).data('id'),goBack: function(){
								cmRegi.init();
							}});
						}).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t){
									cmOper.windowHistDetails({id: K.tmp.data('id'),goBack: function(){
										cmRegi.init();
									}});
								},
								'conMenListEd_edi': function(t){
									cmOper.windowEditHist({id: K.tmp.data('id'),goBack: function(){
										cmRegi.init();
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
	['cm/espa','cm/oper'],
	function(cmEspa,cmOper){
		return cmRegi;
	}
);