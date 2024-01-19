tsLiba = {
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
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsLiba',
			titleBar: {
				title: 'Libro Bancos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Periodo','Cuenta Corriente','Saldo deudor inicial','Saldo acreedor inicial','Saldo deudor final','Saldo acreedor final','Registrado'],
					data: 'ts/liba/lista',
					params: {
						tipo: 'B'
					},
					itemdescr: 'saldo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Generar Libro Bancos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsLiba.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.periodo+'</td>');
						$row.append('<td>'+data.cuenta_banco.cod+'</td>');
						$row.append('<td>'+data.saldo_deudor_inicial+'</td>');
						$row.append('<td>'+data.saldo_acreedor_inicial+'</td>');
						$row.append('<td>'+data.saldo_deudor_final+'</td>');
						$row.append('<td>'+data.saldo_acreedor_final+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.apertura.fec)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							tsLiba.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									tsLiba.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									tsLiba.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tsLiba.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tsLiba.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
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
		new K.Panel({
			/*contentURL: 'ts/liba/edit',
			store: false,*/
			content: '<div name="grid"></div>',
			buttons: {
				'Cerrar Libro': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.incomplete();
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						tsLiba.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					onlyHtml: true,
					pagination: false,
					search: false,
					cols: ['Fecha','Documento Sustentatorio','Documento Originario','Detalle','Debe','Haber','Saldo'],
					toolbarHTML: 'Cuenta Bancaria: <select class="form-control" name="cuenta"></select>&nbsp;'+
						'<input type="text" class="form-control" name="periodo" />',
					onContentLoaded: function($el){
						$el.find('[name=periodo]').datepicker( {
							format: "mm-yyyy",
							viewMode: "months", 
							minViewMode: "months"
						}).val(ciHelper.date.get.now_per())
							.on('changeDate', function(ev){
							$(this).change();
						}).change(function(){
							K.block();
							K.incomplete();
							var tmp = $(this).val(),
							periodo = tmp.substr(3,7)+tmp.substr(0,2)+'00';
							$.post('ts/liba/lista_mov',{periodo: periodo,ctban: p.$w.find('[name=cuenta] option:selected').val()},function(data){
								//
								K.unblock();
							},'json');
						});
						$.post('ts/ctban/all',function(data){
							var $cbo = $el.find('[name=cuenta]');
							for(var i=0; i<data.length; i++){
								$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].cod+' - '+data[i].nomb+' ('+data[i].moneda+')</option>');
							}
							$el.find('[name=periodo]').change();
						},'json');
					}
				});
			}
		});
	}
};
define(
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return tsLiba;
	}
);