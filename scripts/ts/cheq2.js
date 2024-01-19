tsCheq2 = {
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
			action: 'tsCheq2',
			titleBar: { title: 'Cheques de Tesorer&iacute;a'}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
		   		var $grid = new K.grid({
		   			$el: p.$w,
					cols: ['','',{n:'Fecha',f:'fec'},{n:'Pagar a',f:'entidad.fullname'},'Monto'],
					data: 'ts/cheq/lista',
					params: {},
					itemdescr: 'cheque(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Cheque</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsCheq2.windowNew();
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
						$row.append('<td>'+tsCheq2.states[data.estado].label+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						if(data.entidad!=null)
							$row.append('<td>'+mgEnti.formatName(data.entidad)+'</td>');
						else
							$row.append('<td>'+data.descr+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.monto)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ts/cheq/print?_id="+$(this).data('id')
							});
						}).contextMenu('conMenListEd', {
							onShowMenu: function(e, menu) {
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "ts/cheq/print?_id="+K.tmp.data('id')
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
			title: 'Nuevo Cheque',
			contentURL: 'ts/cheq/edit',
			store: false,
			width: 600,
			height: 800,
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						var data = {
							fec: p.$w.find('[name=fec]').val(),
							monto: p.$w.find('[name=monto]').val(),
							//entidad: p.$w.find('[name=entidad]').data('data')
							entidad: p.$w.find('[name=beneficiario] [name = mini_enti]').data('data'),
							descr: p.$w.find('[name=descr]').val()
						};
						if(data.fec == ""){
							p.$w.find('[name=fec]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una fecha para el cheque!",type:"error"});
						}
						if(data.monto == ""){
							p.$w.find('[name=monto]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el monto del cheque!",type:"error"});
						}
						if(data.entidad==null){
							p.$w.find('[name=beneficiario] [name=btnSel]').click();
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe seleccionar una Entidad',
									type: 'error'
								});
						}else data.entidad = mgEnti.dbRel(data.entidad);
						/*if(data.entidad == null){
							p.$w.find('[name=btnEnti]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una entidad!",type:"error"});
						}else data.entidad = mgEnti.dbRel(data.entidad);*/
						if(data.descr == ""){
							p.$w.find('[name=descr]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese una descripcion para el cheque!",type:"error"});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ts/cheq/save",data,function(rpta){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Cheque agregado con &eacute;xito!"});
							tsCheq2.init();
							K.closeWindow(p.$w.attr('id'));
							K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ts/cheq/print?_id="+rpta._id.$id
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
				p.$w.find('[name=beneficiario] .panel-title').html('Datos del Beneficiario');
				p.$w.find('[name=beneficiario] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=beneficiario] [name=mini_enti]'),data);
						},bootstrap: true});
				});
				p.$w.find('[name=beneficiario] [name=btnAct]').click(function(){
					if(p.$w.find('[name=beneficiario] [name=mini_enti]').data('data')==null){
						K.msg({
							title: ciHelper.titles.infoReq,
							text: 'Debe Elegir una Cantidad',
							type: 'Error'
						});
					}else{
						mgEnti.windowEdit({callback: function(data){
							mgEnti.fillMini(p.$w.find('[name=beneficiario] [name=mini_enti]'),data);
						},id: p.$w.find('[name=beneficiario] [name=mini_enti]').data('data')._id.$id});
					}
				});
				/*p.$w.find('[name=btnEnti]').click(function(){
					mgEnti.windowSelect({
						callback: function(data){
							p.$w.find('[name=entidad]').html(mgEnti.formatName(data)).data('data',data);
						}
					});
				});*/
				//p.$w.find('.form-group:last').hide();
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return tsCheq2;
	}
);