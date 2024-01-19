/*******************************************************************************
Expedientes Recibidos */
tdExpdReci = {
	init: function(){
		if($('#pageWrapper [child=exps]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('td/navg/exps',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="exps" />');
					$p.find("[name=tdExp]").after( $row.children() );
				}
				$p.find('[name=tdExp]').data('exps',$('#pageWrapper [child=exps]:first').data('exps'));
				$p.find('[name=tdExpdReci]').click(function(){ tdExpdReci.init(); }).find('ul').addClass('ui-state-highlight');
				$p.find('[name=tdExpdVenc]').click(function(){ tdExpdVenc.init(); });
				$p.find('[name=tdExpdArch]').click(function(){ tdExpdArch.init(); });
				$p.find('[name=tdExpdPor]').click(function(){ tdExpdPor.init(); });
				$p.find('[name=tdExpdCopi]').click(function(){ tdExpdCopi.init(); });
			},'json');
		}
		K.initMode({
			mode: 'td',
			action: 'tdExpdReci',
			titleBar: { title: 'Expedientes Nuevos / Recibidos'}
		});
		
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: [
						'',
						'',
						{n:'N&uacute;mero',f:'num'},
						{n:'Gestor',f:'gestor.fullname'},
						'Primer Envio',
						{n:'Asunto',f:'concepto'},
						'Observaciones',
						{n:'Registrado',f:'fecreg'},
						{n:'Vencimiento',f:'fecven'}
					],
					data: 'td/expd/listaexpdreci',
					params: {},
					itemdescr: 'espacio(s)',
					toolbarHTML: '<button name="btnAgregar">Nuevo Expediente</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tdExpd.windowNewExp();
						}).button({icons: {primary: 'ui-icon-folder-collapsed'}});
						if(K.session.tasks["td.expd.area"]=="0"){
							$el.find('[name=btnAgregar]').remove();
						}
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>&nbsp;&nbsp;</td>');
						$row.find('td:last').css('background',tdExpd.states[data.estado].color).addClass('vtip').attr('title',tdExpd.states[data.estado].descr);
						if(data.tupa!=null){
							if(data.estado=='P')
								$row.find('td:last').html('Pendiente (TUPA)').css('color','red');
							if(data.estado=='C')
								$row.find('td:last').html('Concluido (TUPA)').css('color','white');
						}
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.num+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.gestor)+'</td>');
						if(data.traslados[0].destino!=null){
							if(data.traslados[0].destino.organizacion!=null)
								$row.append('<td>'+data.traslados[0].destino.organizacion.nomb+'</td>');
							else
								$row.append('<td>'+data.traslados[0].destino.entidad.nomb+'</td>');
						}else
							$row.append('<td>');
						$row.append('<td>'+data.concepto+'</td>');
						$row.append('<td>'+data.observ_expd+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</td>');
						if(data.fecven!=null){
							$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecven)+'</td>');
							if(ciHelper.dateDiffNow(data.fecven)<=tdExpd.days){
								$row.find('td:last').css('color','red');
							}
						}else
							$row.append('<td>');
						$row.attr('id',data._id.$id);
						$row.data('data',data).dblclick(function(){
							tdExpd.windowDetailsExpd({id: $(this).data('data')._id.$id});
						});
						tdExpd.contextMenu({$row: $row,data: $row.data('data'),rec: true});
						return $row;
					}
				});
			}
		});
	}
};
define(
	['td/expd'],
	function(tdExpd){
		return tdExpdReci;
	}
);