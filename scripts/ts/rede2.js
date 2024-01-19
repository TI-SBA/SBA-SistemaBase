tsRede2 = {
	states: {
		R: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		X:{
			descr: "Anulado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Anulado</span>'
		}
	},
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ts',
			action: 'tsRede2',
			titleBar: { title: 'Recibos Definitivos de Tesorer&iacute;a'}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
		   		var $grid = new K.grid({
		   			$el: p.$w,
					cols: ['','',{n:'Fecha',f:'fec'},{n:'Pagar a',f:'entidad.fullname'},'Concepto','Monto'],
					data: 'ts/rede/lista',
					params: {},
					itemdescr: 'recibo(s) definitivo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Recibos Definitivos</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsRede2.windowNew();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+tsRede2.states[data.estado].label+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						if(data.entidad!=null)
							$row.append('<td>'+mgEnti.formatName(data.entidad)+'</td>');
						else
							$row.append('<td>'+data.descr+'</td>');
						$row.append('<td>'+data.concepto+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.monto)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.windowPrint({
								id:'windowPrint',
								title: "Recibo Definitivo",
								url: "ts/rede/print?_id="+$(this).data('id')
							});
						}).contextMenu('conMenListEd', {
							onShowMenu: function(e, menu) {
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									K.windowPrint({
										id:'windowPrint',
										title: "Recibo Definitivo",
										url: "ts/rede/print?_id="+K.tmp.data('id')
									});
								},
								'conMenListEd_edi': function(t) {
									K.incomplete();
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
		new K.Modal({
			id: 'windowNew',
			title: 'Nuevo Recibo Definitivo',
			contentURL: 'ts/rede/edit',
			width: 450,
			height: 350,
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						var data = {
							fec: p.$w.find('[name=fec]').val(),
							monto: p.$w.find('[name=monto]').val(),
							//entidad: p.$w.find('[name=entidad]').data('data')
							descr: p.$w.find('[name=descr]').val(),
							dni: p.$w.find('[name=dni]').val(),
							concepto: p.$w.find('[name=concepto]').val()
						};
						if(data.fec == ""){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una fecha para el cheque!",type:"error"});
						}
						if(data.monto == ""){
							p.$w.find('[name=monto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el monto del cheque!",type:"error"});
						}
						/*if(data.entidad == null){
							p.$w.find('[name=btnEnti]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una entidad!",type:"error"});
						}else data.entidad = mgEnti.dbRel(data.entidad);*/
						if(data.descr == ""){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese una persona para el cheque!",type:"error"});
						}
						if(data.dni == ""){
							p.$w.find('[name=dni]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese un DNI de la persona!",type:"error"});
						}
						if(data.concepto == ""){
							p.$w.find('[name=concepto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese una descripcion para el cheque!",type:"error"});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/rede/save",data,function(rpta){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Recibo Definitivo agregado con &eacute;xito!"});
							tsRede2.init();
							K.closeWindow(p.$w.attr('id'));
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ts/rede/print?_id="+rpta._id.$id
							});
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
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				p.$w.find('[name=monto]').numeric();
				p.$w.find('[name=fec]').val(K.date()).datepicker();
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return tsRede2;
	}
);