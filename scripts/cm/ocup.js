/*******************************************************************************
Ocupantes */
cmOcup = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmCuenOcup',
			titleBar: {
				title: 'Cuentas: Ocupantes'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','Nombre','Documento de Identidad','Ubicaci&oacute;n','Propietario'],
					data: 'cm/ocup/lista',
					params: {},
					itemdescr: 'ocupante(s)',
					toolbarHTML: '<button name="btnAgregar">Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							cmOper.windowRegiOcup();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+mgEnti.formatName(data)+'</td>');
						$row.append('<td>'+mgEnti.formatIden(data)+'</td>');
						if(data.roles.ocupante.espacio!=null)
							$row.append('<td>'+data.roles.ocupante.espacio.nomb+'</td>');
						else
							$row.append('<td>');
						if(data.roles.ocupante.propietario!=null)
							$row.append('<td>'+mgEnti.formatName(data.roles.ocupante.propietario)+'</td>');
						else
							$row.append('<td>');
						$row.data('id',data._id.$id).data('tipo_enti',data.tipo_enti).dblclick(function(){
							cmOcup.windowDetails({
								id: $(this).data('id'),
								nomb: $(this).find('td:eq(1)').html()
							});
						}).data('data',data).contextMenu('conMenCmOcup', {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenCmOcup_cob',menu).remove();
								return menu;
							},
							bindings: {
								'conMenCmOcup_ver': function(t){
									cmOcup.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(1)').html()
									});
								},
								'conMenCmOcup_ope': function(t) {
									var tmp = K.tmp.data('data').roles.ocupante.espacio;
									tmp.propietario = K.tmp.data('data').roles.ocupante.propietario;
									cmOper.newOper({data: tmp});
								},
								'conMenCmOcup_edi': function(t){
									K.closeWindow('windowDetailEnti'+K.tmp.data('id'));
									var params = new Object;
									params.id = K.tmp.data('data')._id.$id;
									params.nomb = K.tmp.find('td:eq(1)').html();
									params.tipo_enti = K.tmp.data('data').tipo_enti;
									params.data = K.tmp.data('data');
									params.callBack = function(data){
										$.post('mg/enti/save',data,function(rpta){
											K.notification({title: ciHelper.titleMessages.regiAct,text: 'Ocupante actualizado!'});
											K.closeWindow('windowEntiEdit'+data._id);
											if($.cookie('action')=='cmOcup') cmOcup.loadData();
										});
									};
									ciEdit.windowEditEntidad(params);
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	cbEdit: function(data){
		$.post('mg/enti/save',data,function(rpta){
			K.notification({title: ciHelper.titleMessages.regiAct,text: 'Ocupante actualizado!'});
			K.closeWindow('windowEntiEdit'+data._id);
			if($.cookie('action')=='cmOcup') cmOcup.init();
		});
	},
	windowDel: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar ocupante '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> el ocupante <strong>'+p.nomb+'</strong>&#63;',
			icon: 'ui-icon-info',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function() {
					K.notification({text: 'Enviando informaci&oacute;n...'});
					$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					$.post('mg/enti/delete','_id='+p.id,function(){
						K.notification({title: ciHelper.titleMessages.regiEli,text: 'Entidad eliminada!'});
						K.closeWindow('windowDelete');
						K.closeWindow('windowDetailEnti'+p.id);
						if($.cookie('action')=='cmOcup') cmOcup.init();
					});
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	},
	windowDetails: function(p){
		var params = {
			id: 'windowDetailsOcup'+p.id,
			title: 'Ocupante: '+p.nomb,
			contentURL: 'cm/ocup/details',
			icon: 'ui-icon-contact',
			width: 650,
			height: 380,
			onContentLoaded: function(){
				p.$w = $('#windowDetailsOcup'+p.id);
				p.$w.find('label').css('color','#656565');
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				});
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$.post('cm/ocup/get','_id='+p.id,function(data){
					if(data.entidad.roles.ocupante.espacio){
						p.$w.find('[name=ubicacion]').html(data.entidad.roles.ocupante.espacio.nomb).click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id'),modal: true});
						}).data('id',data.entidad.roles.ocupante.espacio._id.$id).css({
							'text-decoration': 'underline',
							'cursor': 'pointer'
						});
					}else p.$w.find('[name=ubicacion]').parent().parent().hide();			
					p.$w.find('[name=spFecreg]').html( ciHelper.dateFormatLong(data.entidad.fecreg) );
					if(data.entidad.roles.ocupante.propietario){
						if(data.entidad.roles.ocupante.propietario.tipo_enti=='E') p.$w.find('[name=titular]').html( data.entidad.roles.ocupante.propietario.nomb );
						else p.$w.find('[name=titular]').html( data.entidad.roles.ocupante.propietario.nomb+' '+data.entidad.roles.ocupante.propietario.appat+' '+data.entidad.roles.ocupante.propietario.apmat );
						p.$w.find('[name=titular]').click(function(){
							cmProp.windowDetails({id: $(this).data('data')._id.$id,nomb: $(this).data('data').nomb+' '+$(this).data('data').appat+' '+$(this).data('data').apmat,modal: true});
						}).data('data',data.entidad.roles.ocupante.propietario).css({
							'text-decoration': 'underline',
							'cursor': 'pointer'
						});
					}else p.$w.find('[name=titular]').parent().parent().hide();						
					p.$w.find('[name=spNomb]').html(data.entidad.nomb);
					p.$w.find('[name=spAppat]').html(data.entidad.appat);
					p.$w.find('[name=spApmat]').html(data.entidad.apmat);
					p.$w.find('[name=spIdent]').html(data.entidad.docident[0].num);
					if(data.entidad.fecnac!=null) p.$w.find('[name=spFecnac]').html( ciHelper.dateFormatLong(data.entidad.fecnac) );
					if(data.opers){
						for(var i=0; i<data.opers.length; i++){
							var $row = p.$w.find('.gridReference').clone();
							var result = data.opers[i];
							if(result.concesion != null)
								$row.find('li:eq(0)').html( 'Concesi&oacute;n' );
							if(result.construccion != null)
								$row.find('li:eq(0)').html( 'Construcci&oacute;n' );
							if(result.asignacion != null)
								$row.find('li:eq(0)').html( 'Asignaci&oacute;n' );
							if(result.adjuntacion != null)
								$row.find('li:eq(0)').html( 'Adjuntaci&oacute;n' );
							if(result.traspaso != null)
								$row.find('li:eq(0)').html( 'Traspaso' );
							if(result.inhumacion != null)
								$row.find('li:eq(0)').html( 'Inhumaci&oacute;n' );
							if(result.traslado != null)
								$row.find('li:eq(0)').html( 'Traslado' );
							if(result.colocacion != null)
								$row.find('li:eq(0)').html( 'Colocaci&oacute;n' );
							if(result.anular_asignacion != null)
								$row.find('li:eq(0)').html( 'Anular Asignaci&oacute;n' );
							if(result.anular_concesion != null)
								$row.find('li:eq(0)').html( 'Anular Concesi&oacute;n' );
							/*$row.find('li:eq(1)').html( 'Registrado' );
							$row.find('li:eq(2)').html( ciHelper.dateFormatLong(result.fecreg) );*/
							if(result.anulacion!=null)
								$row.find('li:eq(1)').html( '<span style="color:red;">Anulada</span>'+'<br />'+mgEnti.formatName(result.trabajador) );
							else if(result.ejecucion!=null)
								$row.find('li:eq(1)').html( 'Ejecutado'+'<br />'+mgEnti.formatName(result.trabajador) );
							else
								$row.find('li:eq(1)').html( 'Activa'+'<br />'+mgEnti.formatName(result.trabajador) );
							
							
							
							
							
							
							
							
							
							
							console.log(result);
							if(result.recibos==null)
								$row.find('li:eq(2)').html( ciHelper.dateFormatLong(result.fecreg) );
							else{
								$row.find('li:eq(2)').html( ciHelper.dateFormatLong(result.fecreg)+'<br />'
									+'<b>RC '+result.recibos[0].serie+'-'+result.recibos[0].num+'</b><br />'
									+mgEnti.formatName(result.recibos[0].cliente) );
								$row.find('li:eq(3)').html('<button name="btnRec">Ver Recibo de Caja</button>');
								$row.find('[name=btnRec]').click(function(){
									K.windowPrint({
										id:'windowcjFactPrint',
										title: "Recibo de Caja",
										url: "cj/comp/print_reci?id="+$(this).data('id')
									});
								}).data('id',result.recibos[0]._id.$id)
									.button({icons: {primary: 'ui-icon-search'}});
								$row.find('[name=btnRec]').css('height','auto')
									.css('width','auto');
							}
							
								
								
								
								
								
								
								
								
								
								
							$row.wrapInner('<a class="item" />');
							$row.find('li:eq(0),li:eq(1),li:eq(2)').click(function(){
								cmOper.showDetails({data: $(this).data('data')});
							}).data('data',result);
							p.$w.find('.gridBody:last').append($row.children());
							if(result.asignacion!=null){								
								p.$w.find('[name=spFecasig]').html( ciHelper.dateFormatLong(result.fecreg) );
							}
							if(result.inhumacion!=null){
								p.$w.find('[name=spFecdef]').html( ciHelper.dateFormatLong(result.inhumacion.fecdef) );
								p.$w.find('[name=spNum]').html( result.inhumacion.partdef );
								if(result.inhumacion.edad!=null){
									p.$w.find('[name=edad]').html( result.inhumacion.edad );
								}
								if(result.inhumacion.causa!=null){
									p.$w.find('[name=causa]').html( result.inhumacion.causa );
								}
								if(result.inhumacion.municipalidad!=null){
									p.$w.find('[name=municipalidad]').html( result.inhumacion.municipalidad.nomb );
									p.$w.find('[name=municipalidad]').click(function(){
										ciDetails.windowDetailsEnti({id: $(this).data('data')._id.$id,tipo_enti: $(this).data('data').tipo_enti,modal: true});
									}).data('data',result.inhumacion.municipalidad).css({
										'text-decoration': 'underline',
										'cursor': 'pointer'
									});
								}
								if(result.inhumacion.funeraria!=null){
									p.$w.find('[name=funeraria]').html( result.inhumacion.funeraria.nomb );
									p.$w.find('[name=funeraria]').click(function(){
										ciDetails.windowDetailsEnti({id: $(this).data('data')._id.$id,tipo_enti: $(this).data('data').tipo_enti,modal: true});
									}).data('data',result.inhumacion.funeraria).css({
										'text-decoration': 'underline',
										'cursor': 'pointer'
									});
								}
								if(result.ejecucion!=null)
									if(result.ejecucion.fecini!=null)
										p.$w.find('[name=spFecinh]').html( ciHelper.dateFormatLong(result.ejecucion.fecini) );
							}
						}
					}
					
					p.$w.find('.ui-layout-center').css('overflow','hidden');
					K.unblock({$element: p.$w});
				},'json');
			}
		};
		if(p.modal!=null)
			new K.Modal(params);
		else new K.Window(params);
	}
};
define(
	['cm/espa','cm/mapa','cm/prop','cm/pabe','cm/oper'],
	function(cmEspa,cmMapa,cmProp,cmPabe,cmOper){
		return cmOcup;
	}
);