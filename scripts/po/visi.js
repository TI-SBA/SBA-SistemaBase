poVisi = {
	states: {
		E: {
			descr: "Entrada",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		S:{
			descr: "Salida",
			color: "#CCCCCC",
			label: '<span class="label label-default">Anulado</span>'
		}
	},
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'po',
			action: 'poVisi',
			titleBar: { title: 'Visitas al local'}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
		   		var $grid = new K.grid({
		   			$el: p.$w,
					cols: ['',{n:'Fecha',f:'fecent'},{n:'Visitante',f:'entidad.fullname'},'DOC. Identidad',{n:'Entidad',f:'entidad.fullname'},'Motivo',{n:'Empleado Público',f:'entidad.fullname'},{n:'OFICINA/CARGO',f:'oficina.nomb'},'Lugar de Reuni&oacute;n',{n:'Hora de ingreso',f:'fecent'},{n:'Hora de salida',f:'fecsal'}],
					data: 'po/visi/lista',
					params: {},
					itemdescr: 'visitante(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Nueva Visita</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							poVisi.windowNew();
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
						$row.find('td:eq(0)').css('background',poVisi.states[data.estado].color).addClass('vtip').attr('title',poVisi.states[data.estado].descr);
						$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fecent)+'</td>');
						if(data.visitante!=null){
							$row.append('<td>'+mgEnti.formatName(data.visitante)+'</td>');
							$row.append('<td>'+data.visitante.docident[0].num+'</td>');
						}else{
							$row.append('<td>'+"ERROR DE VISITANTE"+'</td>');
							$row.append('<td>'+"ERROR DE VISITANTE"+'</td>');
						}
						if(data.entidad.es_empresa == false){
							$row.append('<td>'+data.entidad.entidad+'</td>');
						}else{
							$row.append('<td>'+mgEnti.formatName(data.entidad.entidad)+'</td>');
						}
						$row.append('<td>'+data.motivo+'</td>');
						if(data.funcionario!=null){
							$row.append('<td>'+mgEnti.formatName(data.funcionario)+'</td>');
						}else{
							$row.append('<td>'+"ERROR DE FUNCIONARIO"+'</td>');
						}
						if(data.oficina!=null && data.cargo!=null ){
							$row.append('<td>'+data.oficina.nomb+'</br>/'+data.cargo.funcion+'</td>');
						}else{
							$row.append('<td>'+"ERROR DE OFICINA"+'</td>');
						}
						$row.append('<td>'+data.lugar_reunion+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_his(data.fecent)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_his(data.fecsal)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							/*K.windowPrint({
								id:'windowcjFactPrint',
								title: "Recibo de Caja",
								url: "ts/cheq/print?_id="+$(this).data('id')
							});*/
						}).contextMenu('conMenListEd', {
							onShowMenu: function(e, menu) {
								$target = $(e.target);
								$target.closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$target.closest('.item').find('ul').addClass('ui-state-highlight');
								$target.closest('.item').click();
								K.tmp = $target.closest('.item');
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									/*K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "ts/cheq/print?_id="+K.tmp.data('id')
									})*/;
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
			title: 'Nueva Visita',
			contentURL: 'po/visi/edit',
			store: false,
			width: 600,
			height: 600,
			buttons: {
				'Guardar': function(){
					var data = {
						fec: p.$w.find('[name=fecent]').val(),
						ent: p.$w.find('[name=ent]').find("input").val(),
						sal: p.$w.find('[name=sal]').find("input").val(),
						visitante: p.$w.find('[name=visitante] [name=mini_enti]').data('data'),
						entidad: {
							es_empresa: (p.$w.find('[name=empresa]').prop('checked') === true),
							entidad: "--",
						},
						motivo: p.$w.find('[name=motivo]').val(),
						funcionario: p.$w.find('[name=trabajador] [name=mini_enti]').data('data'),
						lugar_reunion: p.$w.find('[name=lugar]').val(),
					};
					if(data.fec == ""){
						p.$w.find('[name=fecent]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione la fecha y hora de entrada!",type:"error"});
					}
					/*if(data.fecsal == ""){
						p.$w.find('[name=fecsal]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione la fecha y hora de salida!",type:"error"});
					}*/
					if(data.ent == ""){
						p.$w.find('[name=ent]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione la fecha y hora de salida!",type:"error"});
					}
					if(data.sal == ""){
						p.$w.find('[name=sal]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione la fecha y hora de salida!",type:"error"});
					}
					if(data.visitante == null){
						p.$w.find('[name=visitante] [name=btnEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un visitante!",type:"error"});
					}else data.visitante = mgEnti.dbRel(data.visitante);
					
					if(data.entidad.es_empresa == false) {
						if(p.$w.find('[name=entidad_text]').val() == ""){
							p.$w.find('[name=entidad_text]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe escribir a que entidad representa el visitante!",type:"error"});
						}	
						data.entidad.entidad = p.$w.find('[name=entidad_text]').val();
					}else{
						if(p.$w.find('[name=entidad_emp] [name=mini_enti]').data('data') == null){
							p.$w.find('[name=entidad_emp] [name=btnEnti]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe seleccionar la empresa por la que viene el visitante!",type:"error"});
						}else data.entidad.entidad = mgEnti.dbRel(p.$w.find('[name=entidad_emp] [name=mini_enti]').data('data'));
					}
		
					if(data.motivo == ""){
						p.$w.find('[name=motivo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar un motivo por el cual se realizo la visita!",type:"error"});
					}
					if(data.funcionario == null){
						p.$w.find('[name=trabajador] [name=btnEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione el trabajador a ser visitado!",type:"error"});
					}else data.funcionario = mgEnti.dbRel(data.funcionario);
					if(data.lugar_reunion == ""){
						p.$w.find('[name=lugar]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione el lugar de la visita!",type:"error"});
					}
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post("po/visi/save",data,function(rpta){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Visitante agregado con &eacute;xito!"});
						poVisi.init();
						K.closeWindow(p.$w.attr('id'));
					},'json');
				},
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNew');
				p.$w.find('[name=noempresa_ent]').show();
				p.$w.find('[name=empresa_ent]').hide();

				p.$w.find('[name=fecent]').val(K.date()).datepicker();
				p.$w.find('[name=ent]').datetimepicker({
                    format: 'LT',
                    //useCurrent: false //Important! See issue #1075
                });
                p.$w.find('[name=sal]').datetimepicker({
                    format: 'LT',
                    //useCurrent: false //Important! See issue #1075
                });
                p.$w.find('[name=ent]').on("dp.change", function (e) {
            		p.$w.find('[name=sal]').data("DateTimePicker").minDate(e.date);
        		});
                p.$w.find('[name=sal]').on("dp.change", function (e) {
            		p.$w.find('[name=ent]').data("DateTimePicker").maxDate(e.date);
        		});
				/*p.$w.find('[name=btnEnti]').click(function(){
					mgEnti.windowSelect({
						callback: function(data){
							p.$w.find('[name=entidad]').html(mgEnti.formatName(data)).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-icon-search'}});*/
				p.$w.find('[name=visitante] .panel-title').html('VISITANTE');
				p.$w.find('[name=visitante] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=visitante] [name=mini_enti]'),data);
						console.log(p.$w.find('[name=visitante]').data('data'));
					},bootstrap: true});
				});
				p.$w.find('[name=entidad_emp] .panel-title').html('EMPRESA');
				p.$w.find('[name=entidad_emp] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=entidad_emp] [name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'E'},
					]});
				});
				p.$w.find('[name=trabajador] [name=btnSel]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						mgEnti.fillMini(p.$w.find('[name=trabajador] [name=mini_enti]'),data);
					},bootstrap: true,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.trabajador',value: {$exists: true}}
					]});
				});
				p.$w.find('[name=empresa]').change(function(){
					if( p.$w.find('[name=empresa]').prop('checked') ) {
    					p.$w.find('[name=noempresa_ent]').hide();
						p.$w.find('[name=empresa_ent]').show();
					}else{
						p.$w.find('[name=noempresa_ent]').show();
						p.$w.find('[name=empresa_ent]').hide();
					}
				});
				//p.$w.find('tr:last').hide();
			}
		});
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return poVisi;
	}
);