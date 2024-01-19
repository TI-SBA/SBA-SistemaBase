/*******************************************************************************
trabajadores */
peEntiTrab = {
	states: {
		H: {
			descr: "Habilitado",
			color: "#006532"
		},
		D: {
			descr: "Deshabilitado",
			color: "#CCCCCC"
		}
	},
	sexo: {
		F: "Femenino",
		M: 'Masculino'
	},
	tipo_superior: {
		T: "T&eacute;cnico",
		U: "Universitario",
		M: "Maestr&iacute;a",
		D: "Doctorado"
	},
	tipo: {
		N: 'Nombrado',
		C: 'Contratado',
		SN: 'Salud Nombrado',
		SC: 'Salud Contratado'
	},
	estado_civil: {
		SO: "Soltero",
		CA: "Casado",
		VI: "Viudo",
		DI: "Divorciado",
		SE: "Separado",
		CO: "Conviviente"
	},
	cese: {
		DS: 'Despido',
		RE: 'Renuncia',
		JU: 'Jubilaci&oacute;n',
		DF: 'Defunci&oacute;n'
	},
	modalidad: {
		CP: 'Convocatoria P&uacute;blica - CAS',
		SP: 'Sustituci&oacute;n de Contrato',
		DD: 'Designado Directamente',
		OT: 'Otros'
	},
	init: function(){
		var tipo = $.cookie('tipo_contrato');
		if($('#pageWrapper [child=enti]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pe/navg/enti',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="enti" />');
					$p.find("[name=peEnti]").after( $row.children() );
				}
				$p.find('[name=peEnti]').data('enti',$('#pageWrapper [child=enti]:first').data('enti'));
				$p.find('[name=peEntiPra]').click(function(){ peEntiPra.init(); });
				$p.find('[name^=peEntiTrab]').click(function(){
					$.cookie('tipo_contrato',$(this).attr('name').substring(10));
					peEntiTrab.init();
				});
				$('#pageWrapperLeft [name=peEntiTrab'+tipo+']').addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'peEntiTrab',
			titleBar: {
				title: 'Cuentas: Trabajadores '+tipo
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/trab',
			onContentLoaded: function(){
				$('#pageWrapperLeft [name=peEntiTrab'+tipo+']').addClass('ui-state-highlight');
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de trabajador' ).width('250');
				$mainPanel.find('[name=obj]').html( 'trabajador(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					peEntiTrab.windowNew({tipo: tipo});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						peEntiTrab.loadData({page: 1,tipo: tipo,url: 'pe/trab/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						peEntiTrab.loadData({page: 1,tipo: tipo,url: 'pe/trab/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				peEntiTrab.loadData({page: 1,tipo: tipo,url: 'pe/trab/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
	    	page: (params.page) ? params.page : 1
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) {
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',peEntiTrab.states[result.roles.trabajador.estado].color).addClass('vtip').attr('title',peEntiTrab.states[result.roles.trabajador.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( ciHelper.enti.formatName(result) );
					if(result.roles.trabajador.cargo._id!=null) $li.eq(3).html( result.roles.trabajador.cargo.nomb );
					else $li.eq(3).html( result.roles.trabajador.cargo.funcion );
					$li.eq(4).html( result.roles.trabajador.organizacion.nomb );
					$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
					if(result.roles.trabajador.ficha!=null) var ficha = result.roles.trabajador.ficha.$id;
					else var ficha = null;
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('tipo_enti',result.tipo_enti).dblclick(function(){
						peEntiTrab.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).data('estado',result.roles.trabajador.estado).data('enti',ciHelper.enti.dbTrabRel(result))
					.data('ficha',ficha).data('tipo',result.roles.trabajador.contrato.cod)
					.data('tipo_id',result.roles.trabajador.contrato._id.$id).contextMenu("conMenPeTrab", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							//$('#conMenPeTrab_hab',menu).remove();
							//$('#conMenPeTrab_edi',menu).remove();
							//$('#conMenPeTrab_bon',menu).remove();
							if(K.tmp.data('estado')=='H') $('#conMenPeTrab_hab',menu).remove();
							else $('#conMenPeTrab_leg,#conMenPeTrab_agr,#conMenPeTrab_ent,#conMenPeTrab_edi,#conMenPeTrab_des,#conMenPeTrab_act,#conMenPeTrab_reg,#conMenPeTrab_vle',menu).remove();
							if(K.tmp.data('ficha')==null) $('#conMenPeTrab_vle,#conMenPeTrab_reg',menu).remove();
							return menu;
						},
						bindings: {
							'conMenPeTrab_eli': function(t) {
								K.sendingInfo();
								$.post('pe/trab/delete',{
									_id: K.tmp.data('id')
								},function(){
									K.clearNoti();
									K.notification({title: ciHelper.titleMessages.regiAct,text: 'El trabajador fue eliminado con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
							},
							'conMenPeTrab_ver': function(t) {
								peEntiTrab.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_edi': function(t) {
								peEntiTrab.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html(),tipo: K.tmp.data('tipo_id')});
							},
							'conMenPeTrab_ent': function(t){
								K.closeWindow('windowDetailEnti'+K.tmp.data('id'));
								$.post('mg/enti/get','_id='+K.tmp.data('id'),function(data){
									ciEdit.windowEditEntidad({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('li:eq(2)').html(),
										tipo_enti: K.tmp.data('tipo_enti'),
										data: data,
										callBack: function(data){
											$.post('cm/prop/save',data,function(rpta){
												K.notification({title: ciHelper.titleMessages.regiAct,text: 'Trabajador actualizado!'});
												K.closeWindow('windowEntiEdit'+data._id);
												$('#pageWrapperLeft .ui-state-highlight').click();
											});
										}
									});
								},'json');
							},
							'conMenPeTrab_act': function(t) {
								peEntiTrab.windowAct({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_vle': function(t) {
								peEntiTrab.windowDetailsLega({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_leg': function(t) {
								peEntiTrab.windowLeg({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html(),ficha: K.tmp.data('ficha'),enti: K.tmp.data('enti')});
							},
							'conMenPeTrab_bon': function(t){
								peEntiTrab.windowDetailsBon({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_agr': function(t){
								peEntiTrab.windowBon({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html(),tipo: K.tmp.data('tipo')});
							},
							'conMenPeTrab_impleg': function(t) {
								var url = "pe/trab/print_legajo?id="+K.tmp.data('id');
								K.windowPrint({
									id:"windowPrintPeLegPrint",
									title:"Imprimir Legajo",
									url:url
								});
							},
							'conMenPeTrab_hab': function(t) {
								K.sendingInfo();
								$.post('pe/trab/upd',{_id: K.tmp.data('id'),'estado': 'H'},function(){
									K.clearNoti();
									K.notification({title: 'Trabajador Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
							},
							'conMenPeTrab_des': function(t) {
								peEntiTrab.windowDes({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_imp': function(t) {
								K.sendingInfo();
								K.windowPrint({
									id:'windowPeFichaPrint',
									title: "Ficha del Trabajador",
									url: "pe/trab/print_ficha?id="+K.tmp.data('id')
								});
							},
							'conMenPeTrab_his': function(t) {
								peEntiTrab.windowDetailsHisto({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_ret': function(t) {
								peEntiTrab.windowRetencion({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeTrab_tip': function(t){
								peEntiTrab.cambiarTipo({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(2)').html(),
									contrato: K.tmp.data('tipo_id')
								});
							}
						}
					});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results [name=showing]').html( count );
		        $('#Results [name=founded]').html( data.paging.total_items );
		        $('#Results').show();
		        
		        $moreresults = $("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						peEntiTrab.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
		        }else{
					$("#mainPanel .gridFoot").hide();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
		        }
	      } else {
	        $('#No-Results').show();
	        $('#Results').hide();
	        $( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowNew: function(p){
		p.cbEnti = function(data){
			if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
			else p.$w.find('[name=foto]').removeAttr('src');
			p.$w.find('[name=nomb]').data('data',data)
			.html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
			p.$w.find('[name=docident]').html( data.docident[0].num );
			if(data.domicilios!=null) p.$w.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
			else p.$w.find('[name=direc]').html('--');
			if(data.telefonos!=null) p.$w.find('[name=telf]').html( data.telefonos[0].num );
			else p.$w.find('[name=telf]').html('--');
		};
		new K.Window({
			id: 'windowNewTrab',
			title: 'Nuevo Trabajador',
			contentURL: 'pe/trab/edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						observ: p.$w.find('[name=observ]').val(),
						comision: p.$w.find('[name=comision] option:selected').val()
					},
					flag_nivel = false,
					tmp = p.$w.find('[name=nomb]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una entidad!',
							type: 'error'
						});
					}else{
						data._id = tmp._id.$id;
					}
					data.contrato = {
						_id: p.contrato._id.$id,
						nomb: p.contrato.nomb,
						cod: p.contrato.cod
					};
					tmp = p.$w.find('[name=oficina]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnSelOfi]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar una oficina!',
							type: 'error'
						});
					}
					data.oficina = {
						_id: tmp._id.$id,
						nomb: tmp.nomb
					};
					for(var i=0,j=p.contrato.campos.length; i<j; i++){
						if(p.contrato.campos[i].name=="campo1"){
							data.ruc = p.$w.find('[name=ruc]').val();
							if(data.ruc==''){
								p.$w.find('[name=ruc]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un n&uacute;mero de RUC!',
									type: 'error'
								});
							}
						}else if(p.contrato.campos[i].name=="campo2"){
							tmp = p.$w.find('[name=cargo]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelCar]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un cargo!',
									type: 'error'
								});
							}else{
								data.cargo = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									organizacion: {
										_id: tmp.organizacion._id.$id,
										nomb: tmp.organizacion.nomb,
										componente: {
											_id: tmp.organizacion.componente._id.$id,
											nomb: tmp.organizacion.componente.nomb,
											cod: tmp.organizacion.componente.cod
										},
										actividad: {
											_id: tmp.organizacion.actividad._id.$id,
											nomb: tmp.organizacion.actividad.nomb,
											cod: tmp.organizacion.actividad.cod
										}
									}
								};
							}
						}else if(p.contrato.campos[i].name=="campo3"){
							data.cargo = p.$w.find('[name=funcion]').val();
							if(data.cargo==''){
								p.$w.find('[name=funcion]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar la funci&oacute;n a desempe&ntilde;ar!',
									type: 'error'
								});
							}else{
								data.cargo = {
									funcion: data.cargo
								};
								tmp = p.$w.find('[name=orga_funcion]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelOrgFunc]').click();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar una organizaci&oacute;n!',
										type: 'error'
									});
								}else{
									data.cargo.organizacion = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										componente: {
											_id: tmp.componente._id.$id,
											nomb: tmp.componente.nomb,
											cod: tmp.componente.cod
										},
										actividad: {
											_id: tmp.actividad._id.$id,
											nomb: tmp.actividad.nomb,
											cod: tmp.actividad.cod
										}
									};
								}
							}
						}else if(p.contrato.campos[i].name=="campo4"){
							flag_nivel = true;
							tmp = p.$w.find('[name=nivel]').data('data');
							if(tmp!=null){
								data.nivel = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									abrev: tmp.abrev,
									salario: tmp.salario,
									basica: tmp.basica,
									reunificada: tmp.reunificada
								};
							}
						}else if(p.contrato.campos[i].name=="campo5"){
							flag_nivel = true;
							tmp = p.$w.find('[name=nivel2]').data('data');
							if(tmp!=null){
								data.nivel_carrera = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									abrev: tmp.abrev,
									salario: tmp.salario,
									basica: tmp.basica,
									reunificada: tmp.reunificada
								};
							}
						}else if(p.contrato.campos[i].name=="campo6"){
							data.salario = p.$w.find('[name=salario]').val();
							if(data.salario==''){
								p.$w.find('[name=salario]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un salario!',
									type: 'error'
								});
							}
						}else if(p.contrato.campos[i].name=="campo7"){
							data.modalidad = p.$w.find('[name=mod] option:selected').val();
						}else if(p.contrato.campos[i].name=="campo8"){
							tmp = p.$w.find('[name=descr]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelLoc]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un local!',
									type: 'error'
								});
							}else{
								data.local = {
									_id: tmp._id.$id,
									descr: tmp.descr,
									direccion: tmp.direccion
								};
							}
						}else if(p.contrato.campos[i].name=="campo9"){
							data.cod_tarjeta = p.$w.find('[name=tarjeta]').val();
						}else if(p.contrato.campos[i].name=="campo10"){
							tmp = p.$w.find('[name=turno]').data('data');
							if(tmp!=null){
								data.turno = {
									_id: tmp._id.$id,
									nomb: tmp.nomb
								};
							}
						}else if(p.contrato.campos[i].name=="campo11"){
							tmp = p.$w.find('[name=clas]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelCla]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cargo clasificado!',type: 'error'});
							}else{
								data.cargo_clasif = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									cod: tmp.cod
								};
							}
						}else if(p.contrato.campos[i].name=="campo12"){
							tmp = p.$w.find('[name=grup]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelGru]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un grupo ocupacional!',type: 'error'});
							}else{
								data.grupo_ocup = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									sigla: tmp.sigla
								};
							}
						}else if(p.contrato.campos[i].name=="campo13"){
							data.essalud = p.$w.find('[name=essalud]').val();
						}else if(p.contrato.campos[i].name=="campo14"){
							var tmp = p.$w.find('[name=sist] option:selected').data('data');
							if(tmp!=null){
								data.pension = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									tipo: tmp.tipo,
									porcentajes: tmp.porcentajes
								};
								if(p.$w.find('[name=cod_apor]').val()!=''){
									data.cod_aportante = p.$w.find('[name=cod_apor]').val();
								}else{
									p.$w.find('[name=cod_apor]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de aportante!',type: 'error'});
								}
							}
						}else if(p.contrato.campos[i].name=="campo15"){
							data.tipo = p.$w.find('input:radio[name^=rbtnTipTra]:checked').val();
						}else if(p.contrato.campos[i].name=="campo16"){
							data.eps = p.$w.find('input:radio[name^=rbtnAfiEps]:checked').val();
						}
					}
					if(flag_nivel==true){
						if(data.nivel==null&&data.nivel_carrera==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un nivel designado o uno de carrera!',
								type: 'error'
							});
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El trabajador fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewTrab');
				K.block({$element: p.$w});
				$.post('pe/trab/edit_new',function(data){
					for(var i=0,j=data.cont.length; i<j; i++){
						if(data.cont[i].cod==p.tipo)
							p.contrato = data.cont[i];
					}
					if(p.contrato.campos==null){
						K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El tipo de contrato seleccionado no tiene campos definidos!',
							type: 'error'
						});
						return K.closeWindow(p.$w.attr('id'));
					}
					p.$w.find('[name=btnSelEnt]').click(function(){
						ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbEnti,filter: [
						    {nomb: 'tipo_enti',value: 'P'},
						    {nomb: 'roles.trabajador',value: {$exists: false}}
						]});
					}).button({icons: {primary: 'ui-icon-search'}});
					p.$w.find('[name=btnSelOfi]').click(function(){
						require(['mg/ofic'],function(mgOfic){
							mgOfic.windowSelect({callback: function(data){
								p.$w.find('[name=oficina]').html(data.nomb)
									.data('data',data);
							}});
						});
					}).button({icons: {primary: 'ui-icon-search'}});
					p.$w.find('[name=btnAgrEnt]').click(function(){
						ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbEnti,reqs: {tipo_enti: 'P'}});
					}).button({icons: {primary: 'ui-icon-plusthick'}});
					p.$w.find('[name^=campo]').hide();
					p.$w.find('fieldset:eq(3) td:eq(2)').html(p.contrato.cod);
					for(var i=0,j=p.contrato.campos.length; i<j; i++){
						if(p.contrato.campos[i].name=="campo1"){
							p.$w.find('[name=campo1]').show();
						}else if(p.contrato.campos[i].name=="campo2"){
							p.$w.find('[name=campo2]').show();
							p.$w.find('[name=btnSelCar]').click(function(){
								peCarg.windowSelect({callback: function(data){
									p.$w.find('[name=cargo]').html(data.nomb).data('data',data);
									p.$w.find('[name=orga]').html(data.organizacion.nomb);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo3"){
							p.$w.find('[name=campo2]').show();
							var $tab = p.$w.find('[name=campo2] table').empty();
							$tab.append('<tr>');
							$tab.find('tr:last').append('<td><label>Funci&oacute;n a desempe&ntilde;ar</label></td>');
							$tab.find('tr:last').append('<td><input type="text" name="funcion" size="50"></td>');
							$tab.append('<tr>');
							$tab.find('tr:last').append('<td><label>Organizaci&oacute;n</label></td>');
							$tab.find('tr:last').append('<td><span name="orga_funcion"></span>&nbsp;<button name="btnSelOrgFunc">Seleccionar</button></td>');
							p.$w.find('[name=btnSelOrgFunc]').click(function(){
								ciSearch.windowSearchOrga({callback: function(data){
									p.$w.find('[name=orga_funcion]').html(data.nomb).data('data',data);
									p.$w.find('[name=btnSelOrgFunc]').button('option','text',false);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo4"){
							p.$w.find('[name=campo4]').show();
							p.$w.find('[name=btnSelNiv]').click(function(){
								peNive.windowSelect({callback: function(data){
									p.$w.find('[name=nivel]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnSelNiv]').after('<button name="btnSelNiv_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv_clean]').click(function(){
								p.$w.find('[name=nivel]').html('').removeData('data');
							}).button({icons: {primary: 'ui-icon-trash'},text: false});
						}else if(p.contrato.campos[i].name=="campo5"){
							p.$w.find('[name=campo5]').show();
							p.$w.find('[name=btnSelNiv2]').click(function(){
								peNive.windowSelect({callback: function(data){
									p.$w.find('[name=nivel2]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnSelNiv2]').after('<button name="btnSelNiv2_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv2_clean]').click(function(){
								p.$w.find('[name=nivel2]').html('').removeData('data');
							}).button({icons: {primary: 'ui-icon-trash'},text: false});
						}else if(p.contrato.campos[i].name=="campo6"){
							p.$w.find('[name=campo6]').show();
							p.$w.find('[name=salario]').numeric().spinner({step: 0.1,min: 0});
							p.$w.find('[name=salario]').parent().find('.ui-button').css('height','14px');
						}else if(p.contrato.campos[i].name=="campo7"){
							p.$w.find('[name=campo7]').show();
						}else if(p.contrato.campos[i].name=="campo8"){
							p.$w.find('[name=campo8]').show();
							p.$w.find('[name=btnSelLoc]').click(function(){
								ciSearch.windowSelectLocal({callback: function(data){
									p.$w.find('[name=descr]').html(data.descr).data('data',data);
									p.$w.find('[name=direccion]').html(data.direccion);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo9"){
							p.$w.find('[name=campo9]').show();
						}else if(p.contrato.campos[i].name=="campo10"){
							p.$w.find('[name=campo10]').show();
							p.$w.find('[name=btnSelTur]').click(function(){
								peTurn.windowSelect({callback: function(data){
									p.$w.find('[name=turno]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnSelTur]').after('<button name="btnSelTur_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelTur_clean]').click(function(){
								p.$w.find('[name=turno]').html('').removeData('data');
							}).button({icons: {primary: 'ui-icon-trash'},text: false});
						}else if(p.contrato.campos[i].name=="campo11"){
							p.$w.find('[name=campo11]').show();
							p.$w.find('[name=btnSelCla]').click(function(){
								peClas.windowSelect({callback: function(data){
									p.$w.find('[name=clas]').html(data.nomb).data('data',data);
									p.$w.find('[name=codclas]').html(data.cod);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo12"){
							p.$w.find('[name=campo12]').show();
							p.$w.find('[name=btnSelGru]').click(function(){
								peGrup.windowSelect({callback: function(data){
									p.$w.find('[name=grup]').html(data.nomb).data('data',data);
									p.$w.find('[name=codgrup]').html(data.sigla);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo13"){
							p.$w.find('[name=campo13]').show();
							
						}else if(p.contrato.campos[i].name=="campo14"){
							p.$w.find('[name=campo14]').show();
							if(data.pension==null){
								K.closeWindow(p.$w.attr('id'));
								return K.notification({
									title: 'Sistemas de Pensi&oacute;n no especificados',
									text: 'Debe de ingresar al menos un Sistema de Pensi&oacute;n para poder crear una ficha de trabajador!',
									type: 'info'
								});
							}
							var $sel = p.$w.find('[name=sist]');
							$sel.append('<option>--</option>');
							if(data.pension!=null){
								for(var k=0; k<data.pension.length; k++){
									$sel.append('<option value="'+data.pension[k]._id.$id+'">'+data.pension[k].nomb+'</option>');
									$sel.find('option:last').data('data',data.pension[k]);
								}
							}
						}else if(p.contrato.campos[i].name=="campo15"){
							p.$w.find('[name=campo15]').show();
							p.$w.find('[name=tipotrab]').buttonset();
						}else if(p.contrato.campos[i].name=="campo16"){
							p.$w.find('[name=campo16]').show();
							p.$w.find('[name=afilia]').buttonset();
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditTrab'+p.id,
			title: 'Editar Trabajador: '+p.nomb,
			contentURL: 'pe/trab/edit',
			icon: 'ui-icon-pencil',
			width: 550,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						observ: p.$w.find('[name=observ]').val(),
						comision: p.$w.find('[name=comision] option:selected').val(),
						contrato: {
							_id: p.contrato._id.$id,
							nomb: p.contrato.nomb,
							cod: p.contrato.cod
						}
					},
					flag_nivel = false;
					tmp = p.$w.find('[name=oficina]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnSelOfi]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar una oficina!',
							type: 'error'
						});
					}
					data.oficina = {
						_id: tmp._id.$id,
						nomb: tmp.nomb
					};
					for(var i=0,j=p.contrato.campos.length; i<j; i++){
						if(p.contrato.campos[i].name=="campo1"){
							data.ruc = p.$w.find('[name=ruc]').val();
							if(data.ruc==''){
								p.$w.find('[name=ruc]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un n&uacute;mero de RUC!',
									type: 'error'
								});
							}
						}else if(p.contrato.campos[i].name=="campo2"){
							tmp = p.$w.find('[name=cargo]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelCar]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un cargo!',
									type: 'error'
								});
							}else{
								data.cargo = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									organizacion: {
										_id: tmp.organizacion._id.$id,
										nomb: tmp.organizacion.nomb,
										componente: {
											_id: tmp.organizacion.componente._id.$id,
											nomb: tmp.organizacion.componente.nomb,
											cod: tmp.organizacion.componente.cod
										},
										actividad: {
											_id: tmp.organizacion.actividad._id.$id,
											nomb: tmp.organizacion.actividad.nomb,
											cod: tmp.organizacion.actividad.cod
										}
									}
								};
							}
						}else if(p.contrato.campos[i].name=="campo3"){
							data.cargo = p.$w.find('[name=funcion]').val();
							if(data.cargo==''){
								p.$w.find('[name=funcion]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar la funci&oacute;n a desempe&ntilde;ar!',
									type: 'error'
								});
							}else{
								data.cargo = {
									funcion: data.cargo
								};
								tmp = p.$w.find('[name=orga_funcion]').data('data');
								if(tmp==null){
									p.$w.find('[name=btnSelOrgFunc]').click();
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar una organizaci&oacute;n!',
										type: 'error'
									});
								}else{
									data.cargo.organizacion = {
										_id: tmp._id.$id,
										nomb: tmp.nomb,
										componente: {
											_id: tmp.componente._id.$id,
											nomb: tmp.componente.nomb,
											cod: tmp.componente.cod
										},
										actividad: {
											_id: tmp.actividad._id.$id,
											nomb: tmp.actividad.nomb,
											cod: tmp.actividad.cod
										}
									};
								}
							}
						}else if(p.contrato.campos[i].name=="campo4"){
							flag_nivel = true;
							tmp = p.$w.find('[name=nivel]').data('data');
							if(tmp!=null){
								data.nivel = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									abrev: tmp.abrev,
									salario: tmp.salario,
									basica: tmp.basica,
									reunificada: tmp.reunificada
								};
							}
						}else if(p.contrato.campos[i].name=="campo5"){
							flag_nivel = true;
							tmp = p.$w.find('[name=nivel2]').data('data');
							if(tmp!=null){
								data.nivel_carrera = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									abrev: tmp.abrev,
									salario: tmp.salario,
									basica: tmp.basica,
									reunificada: tmp.reunificada
								};
							}
						}else if(p.contrato.campos[i].name=="campo6"){
							data.salario = p.$w.find('[name=salario]').val();
							if(data.salario==''){
								p.$w.find('[name=salario]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un salario!',
									type: 'error'
								});
							}
						}else if(p.contrato.campos[i].name=="campo7"){
							data.modalidad = p.$w.find('[name=mod] option:selected').val();
						}else if(p.contrato.campos[i].name=="campo8"){
							tmp = p.$w.find('[name=descr]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelLoc]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un local!',
									type: 'error'
								});
							}else{
								data.local = {
									_id: tmp._id.$id,
									descr: tmp.descr,
									direccion: tmp.direccion
								};
							}
						}else if(p.contrato.campos[i].name=="campo9"){
							data.cod_tarjeta = p.$w.find('[name=tarjeta]').val();
						}else if(p.contrato.campos[i].name=="campo10"){
							tmp = p.$w.find('[name=turno]').data('data');
							if(tmp!=null){
								data.turno = {
									_id: tmp._id.$id,
									nomb: tmp.nomb
								};
							}
						}else if(p.contrato.campos[i].name=="campo11"){
							tmp = p.$w.find('[name=clas]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelCla]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cargo clasificado!',type: 'error'});
							}else{
								data.cargo_clasif = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									cod: tmp.cod
								};
							}
						}else if(p.contrato.campos[i].name=="campo12"){
							tmp = p.$w.find('[name=grup]').data('data');
							if(tmp==null){
								p.$w.find('[name=btnSelGru]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un grupo ocupacional!',type: 'error'});
							}else{
								data.grupo_ocup = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									sigla: tmp.sigla
								};
							}
						}else if(p.contrato.campos[i].name=="campo13"){
							data.essalud = p.$w.find('[name=essalud]').val();
						}else if(p.contrato.campos[i].name=="campo14"){
							var tmp = p.$w.find('[name=sist] option:selected').data('data');
							if(tmp!=null){
								data.pension = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									tipo: tmp.tipo,
									porcentajes: tmp.porcentajes
								};
								if(p.$w.find('[name=cod_apor]').val()!=''){
									data.cod_aportante = p.$w.find('[name=cod_apor]').val();
								}/*else{
									p.$w.find('[name=cod_apor]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de aportante!',type: 'error'});
								}*/
							}
						}else if(p.contrato.campos[i].name=="campo15"){
							data.tipo = p.$w.find('input:radio[name^=rbtnTipTra]:checked').val();
						}else if(p.contrato.campos[i].name=="campo16"){
							data.eps = p.$w.find('input:radio[name^=rbtnAfiEps]:checked').val();
						}
					}
					if(flag_nivel==true){
						if(data.nivel==null&&data.nivel_carrera==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un nivel designado o uno de carrera!',
								type: 'error'
							});
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/save_edit',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El trabajador fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditTrab'+p.id);
				p.$w.find('td:first').remove();
				p.$w.find('[name=btnSelEnt]').remove();
				p.$w.find('[name=btnAgrEnt]').remove();
				K.block({$element: p.$w});
				p.$w.find('[name=btnSelOfi]').click(function(){
					require(['mg/ofic'],function(mgOfic){
						mgOfic.windowSelect({callback: function(data){
							p.$w.find('[name=oficina]').html(data.nomb)
								.data('data',data);
						}});
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('pe/trab/edit_new',function(data){
					for(var i=0,j=data.cont.length; i<j; i++){
						if(data.cont[i]._id.$id==p.tipo)
							p.contrato = data.cont[i];
					}
					p.$w.find('[name^=campo]').hide();
					p.$w.find('fieldset:eq(3) td:eq(2)').html(p.contrato.cod);
					for(var i=0,j=p.contrato.campos.length; i<j; i++){
						if(p.contrato.campos[i].name=="campo1"){
							p.$w.find('[name=campo1]').show();
						}else if(p.contrato.campos[i].name=="campo2"){
							p.$w.find('[name=campo2]').show();
							p.$w.find('[name=btnSelCar]').click(function(){
								require(['pe/carg'],function(peCarg){
									peCarg.windowSelect({callback: function(data){
										p.$w.find('[name=cargo]').html(data.nomb).data('data',data);
										p.$w.find('[name=orga]').html(data.organizacion.nomb);
									}});
								});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo3"){
							p.$w.find('[name=campo2]').show();
							var $tab = p.$w.find('[name=campo2] table').empty();
							$tab.append('<tr>');
							$tab.find('tr:last').append('<td><label>Funci&oacute;n a desempe&ntilde;ar</label></td>');
							$tab.find('tr:last').append('<td><input type="text" name="funcion" size="50"></td>');
							$tab.append('<tr>');
							$tab.find('tr:last').append('<td><label>Organizaci&oacute;n</label></td>');
							$tab.find('tr:last').append('<td><span name="orga_funcion"></span>&nbsp;<button name="btnSelOrgFunc">Seleccionar</button></td>');
							p.$w.find('[name=btnSelOrgFunc]').click(function(){
								ciSearch.windowSearchOrga({callback: function(data){
									p.$w.find('[name=orga_funcion]').html(data.nomb).data('data',data);
									p.$w.find('[name=btnSelOrgFunc]').button('option','text',false);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo4"){
							p.$w.find('[name=campo4]').show();
							p.$w.find('[name=btnSelNiv]').click(function(){
								peNive.windowSelect({callback: function(data){
									p.$w.find('[name=nivel]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnSelNiv]').after('<button name="btnSelNiv_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv_clean]').click(function(){
								p.$w.find('[name=nivel]').html('').removeData('data');
							}).button({icons: {primary: 'ui-icon-trash'},text: false});
						}else if(p.contrato.campos[i].name=="campo5"){
							p.$w.find('[name=campo5]').show();
							p.$w.find('[name=btnSelNiv2]').click(function(){
								peNive.windowSelect({callback: function(data){
									p.$w.find('[name=nivel2]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnSelNiv2]').after('<button name="btnSelNiv2_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelNiv2_clean]').click(function(){
								p.$w.find('[name=nivel2]').html('').removeData('data');
							}).button({icons: {primary: 'ui-icon-trash'},text: false});
						}else if(p.contrato.campos[i].name=="campo6"){
							p.$w.find('[name=campo6]').show();
							p.$w.find('[name=salario]').numeric().spinner({step: 0.1,min: 0});
							p.$w.find('[name=salario]').parent().find('.ui-button').css('height','14px');
						}else if(p.contrato.campos[i].name=="campo7"){
							p.$w.find('[name=campo7]').show();
						}else if(p.contrato.campos[i].name=="campo8"){
							p.$w.find('[name=campo8]').show();
							p.$w.find('[name=btnSelLoc]').click(function(){
								ciSearch.windowSelectLocal({callback: function(data){
									p.$w.find('[name=descr]').html(data.descr).data('data',data);
									p.$w.find('[name=direccion]').html(data.direccion);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo9"){
							p.$w.find('[name=campo9]').show();
						}else if(p.contrato.campos[i].name=="campo10"){
							p.$w.find('[name=campo10]').show();
							p.$w.find('[name=btnSelTur]').click(function(){
								peTurn.windowSelect({callback: function(data){
									p.$w.find('[name=turno]').html(data.nomb).data('data',data);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
							p.$w.find('[name=btnSelTur]').after('<button name="btnSelTur_clean">Deseleccionar</button>');
							p.$w.find('[name=btnSelTur_clean]').click(function(){
								p.$w.find('[name=turno]').html('').removeData('data');
							}).button({icons: {primary: 'ui-icon-trash'},text: false});
						}else if(p.contrato.campos[i].name=="campo11"){
							p.$w.find('[name=campo11]').show();
							p.$w.find('[name=btnSelCla]').click(function(){
								peClas.windowSelect({callback: function(data){
									p.$w.find('[name=clas]').html(data.nomb).data('data',data);
									p.$w.find('[name=codclas]').html(data.cod);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo12"){
							p.$w.find('[name=campo12]').show();
							p.$w.find('[name=btnSelGru]').click(function(){
								peGrup.windowSelect({callback: function(data){
									p.$w.find('[name=grup]').html(data.nomb).data('data',data);
									p.$w.find('[name=codgrup]').html(data.sigla);
								}});
							}).button({icons: {primary: 'ui-icon-search'}});
						}else if(p.contrato.campos[i].name=="campo13"){
							p.$w.find('[name=campo13]').show();
							
						}else if(p.contrato.campos[i].name=="campo14"){
							p.$w.find('[name=campo14]').show();
							if(data.pension==null){
								K.closeWindow(p.$w.attr('id'));
								return K.notification({
									title: 'Sistemas de Pensi&oacute;n no especificados',
									text: 'Debe de ingresar al menos un Sistema de Pensi&oacute;n para poder crear una ficha de trabajador!',
									type: 'info'
								});
							}
							var $sel = p.$w.find('[name=sist]');
							$sel.append('<option>--</option>');
							if(data.pension!=null){
								for(var k=0; k<data.pension.length; k++){
									$sel.append('<option value="'+data.pension[k]._id.$id+'">'+data.pension[k].nomb+'</option>');
									$sel.find('option:last').data('data',data.pension[k]);
								}
							}
						}else if(p.contrato.campos[i].name=="campo15"){
							p.$w.find('[name=campo15]').show();
							p.$w.find('[name=tipotrab]').buttonset();
						}else if(p.contrato.campos[i].name=="campo16"){
							p.$w.find('[name=campo16]').show();
							p.$w.find('[name=afilia]').buttonset();
						}
					}
					$.post('mg/enti/get','_id='+p.id,function(data){
						p.data = data;
						if(data.roles.trabajador.oficina!=null){
							p.$w.find('[name=oficina]').html(data.roles.trabajador.oficina.nomb)
								.data('data',data.roles.trabajador.oficina);
						}
						if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
						else p.$w.find('[name=foto]').removeAttr('src');
						p.$w.find('[name=nomb]').data('data',data)
						.html(ciHelper.enti.formatName(data)).attr('title',ciHelper.enti.formatName(data)).tooltip();
						p.$w.find('[name=docident]').html( data.docident[0].num );
						if(data.domicilios!=null) p.$w.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
						else p.$w.find('[name=direc]').html('--');
						if(data.telefonos!=null) p.$w.find('[name=telf]').html( data.telefonos[0].num );
						else p.$w.find('[name=telf]').html('--');
						if(data.roles.trabajador.cargo._id!=null){
							p.$w.find('[name=cargo]').html(data.roles.trabajador.cargo.nomb).data('data',data.roles.trabajador.cargo);
							p.$w.find('[name=orga]').html(data.roles.trabajador.cargo.organizacion.nomb);
						}
						if(data.roles.trabajador.cargo._id==null){
							p.$w.find('[name=funcion]').val(data.roles.trabajador.cargo.funcion);
							p.$w.find('[name=orga_funcion]').html(data.roles.trabajador.cargo.organizacion.nomb).data('data',data.roles.trabajador.cargo.organizacion);
						}
						if(data.roles.trabajador.nivel!=null)
							p.$w.find('[name=nivel]').html(data.roles.trabajador.nivel.nomb).data('data',data.roles.trabajador.nivel);
						if(data.roles.trabajador.nivel_carrera!=null)
							p.$w.find('[name=nivel2]').html(data.roles.trabajador.nivel_carrera.nomb).data('data',data.roles.trabajador.nivel_carrera);
						for(var i=0,j=data.docident.length; i<j; i++){
							if(data.docident[i].tipo=='RUC'){
								p.$w.find('[name=ruc]').val(data.docident[i].num);
								i++;
							}
						}
						if(data.roles.trabajador.salario!=null)
							p.$w.find('[name=salario]').val(data.roles.trabajador.salario);
						if(data.roles.trabajador.local!=null){
							p.$w.find('[name=descr]').html(data.roles.trabajador.local.descr).data('data',data.roles.trabajador.local);
							p.$w.find('[name=direccion]').html(data.roles.trabajador.local.direccion);
						}
						if(data.roles.trabajador.cod_tarjeta!=null)
							p.$w.find('[name=tarjeta]').val(data.roles.trabajador.cod_tarjeta);
						if(data.roles.trabajador.turno!=null)
							p.$w.find('[name=turno]').html(data.roles.trabajador.turno.nomb).data('data',data.roles.trabajador.turno);
						if(data.roles.trabajador.cargo_clasif!=null){
							p.$w.find('[name=clas]').html(data.roles.trabajador.cargo_clasif.nomb).data('data',data.roles.trabajador.cargo_clasif);
							p.$w.find('[name=codclas]').html(data.roles.trabajador.cargo_clasif.cod);
						}
						if(data.roles.trabajador.grupo_ocup!=null){
							p.$w.find('[name=grup]').html(data.roles.trabajador.grupo_ocup.nomb).data('data',data.roles.trabajador.grupo_ocup);
							p.$w.find('[name=codgrup]').html(data.roles.trabajador.grupo_ocup.sigla);
						}
						if(data.roles.trabajador.essalud!=null)
							p.$w.find('[name=essalud]').val(data.roles.trabajador.essalud);
						if(data.roles.trabajador.modalidad!=null)
							p.$w.find('[name=mod]').selectVal(data.roles.trabajador.modalidad);
						if(data.roles.trabajador.pension!=null){
							p.$w.find('[name=sist]').val(data.roles.trabajador.pension._id.$id);
							p.$w.find('[name=cod_apor]').val(data.roles.trabajador.cod_aportante);
						}
						if(data.roles.trabajador.tipo!=null){
							if(data.roles.trabajador.tipo=='N'){
								p.$w.find('[name^=rbtnTipTra]:eq(0)').attr('checked',true);
								p.$w.find('[name^=rbtnTipTra]:eq(1)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(2)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(3)').attr('checked',false);
							}else if(data.roles.trabajador.tipo=='C'){
								p.$w.find('[name^=rbtnTipTra]:eq(0)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(1)').attr('checked',true);
								p.$w.find('[name^=rbtnTipTra]:eq(2)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(3)').attr('checked',false);
							}else if(data.roles.trabajador.tipo=='SN'){
								p.$w.find('[name^=rbtnTipTra]:eq(0)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(1)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(2)').attr('checked',true);
								p.$w.find('[name^=rbtnTipTra]:eq(3)').attr('checked',false);
							}else if(data.roles.trabajador.tipo=='SC'){
								p.$w.find('[name^=rbtnTipTra]:eq(0)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(1)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(2)').attr('checked',false);
								p.$w.find('[name^=rbtnTipTra]:eq(3)').attr('checked',true);
							}
							p.$w.find('[name=tipotrab]').buttonset();
						}
						if(data.roles.trabajador.eps!=null){
							if(data.roles.trabajador.eps==true){
								p.$w.find('[name^=rbtnAfiEps]:eq(0)').attr('checked',true);
								p.$w.find('[name^=rbtnAfiEps]:eq(1)').attr('checked',false);
							}else{
								p.$w.find('[name^=rbtnAfiEps]:eq(0)').attr('checked',false);
								p.$w.find('[name^=rbtnAfiEps]:eq(1)').attr('checked',true);
							}
							p.$w.find('[name=afilia]').buttonset();
						}
						if(data.roles.trabajador.observ!=null)
							p.$w.find('[name=observ]').val(data.roles.trabajador.observ);
						if(data.roles.trabajador.comision!=null)
							p.$w.find('[name=comision]').selectVal(data.roles.trabajador.comision);
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowAct: function(p){
		new K.Window({
			id: 'windowActTrab'+p.id,
			title: 'Actualizar ficha de trabajador: '+p.nomb,
			contentURL: 'pe/trab/ficha',
			icon: 'ui-icon-gear',
			width: 655,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					p.index = p.$w.tabs("option", "active");
					var data = {
						entidad: {
							_id: p.enti._id.$id,
							tipo_enti: p.enti.tipo_enti,
							nomb: p.enti.nomb,
							appat: p.enti.appat,
							apmat: p.enti.apmat
						},
						sexo: p.$w.find('input:radio[name^=rbtnSexEnt]:checked').val(),
						estudios: {
							primaria: p.$w.find('input:radio[name^=rbtnPrim]:checked').val(),
							secundaria: p.$w.find('input:radio[name^=rbtnSec]:checked').val()
						},
						estado_civil: p.$w.find('input:radio[name^=rbtnEst]:checked').val()
					};
					if(p.$w.find('[name=subsidio]').val()!='') data.subsidio = p.$w.find('[name=subsidio]').val();
					if(p.$w.find('[name=grupo]').val()!='') data.sangre = p.$w.find('[name=grupo]').val();
					if(p.$w.find('[name=fecingadm]').val()!='') data.fec_adm_pub = p.$w.find('[name=fecingadm]').val();
					if(p.$w.find('[name=fecingben]').val()!='') data.fec_adm_sbpa = p.$w.find('[name=fecingben]').val();
					if(p.$w.find('[name=fecnac]').val()!='') data.fecnac = p.$w.find('[name=fecnac]').val();
					if(p.$w.find('[name=cod_apor]').val()!='') data.cod_aportante = p.$w.find('[name=cod_apor]').val();
					if(p.$w.find('[name=resolu]').val()!='') data.ref = p.$w.find('[name=resolu]').val();
					if(p.ficha!=null) data._id = p.ficha._id.$id;
					var tmp = p.$w.find('[name=sist] option:selected').data('data');
					if(tmp!=null)
						data.pension = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							tipo: tmp.tipo,
							porcentajes: tmp.porcentajes
						};
					if(data.estudios.primaria=='0') data.estudios.primaria = false;
					else data.estudios.primaria = true;
					if(data.estudios.secundaria=='0') data.estudios.secundaria = false;
					else data.estudios.secundaria = true;
					for(var i=0; i<p.$gb1.find('.item').length; i++){
						p.$w.tabs("option", "active", 1);
						var $row = p.$gb1.find('.item').eq(i);
						tmp = {
							tipo: $row.find('[name=tipo] option:selected').val()
						};
						if($row.find('[name^=rbtnCom]:checked').val()=='0') tmp.completa = false;
						else tmp.completa = true;
						if($row.find('[name=lugar]').val()!=''||$row.find('[name=grado]').val()!=''){
							tmp.lugar = $row.find('[name=lugar]').val();
							if(tmp.lugar==''){
								$row.find('[name=lugar]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el lugar de estudio!',type: 'error'});
							}
							tmp.grado = $row.find('[name=grado]').val();
							if(tmp.grado==''){
								$row.find('[name=grado]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el grado de estudio!',type: 'error'});
							}
							tmp.fecini = $row.find('[name=ini]').val();
							if(tmp.fecini==''){
								$row.find('[name=ini]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de inicio de estudio!',type: 'error'});
							}
							tmp.fecfin = $row.find('[name=fin]').val();
							if(tmp.fecfin==''){
								$row.find('[name=fin]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de fin de estudio!',type: 'error'});
							}
							if(data.estudios.superior==null) data.estudios.superior = [];
							data.estudios.superior.push(tmp);
						}
					}
					/* Colegiatura */
					tmp = {
						colegio: p.$w.find('[name=cole]').val(),
						cod: p.$w.find('[name=numcole]').val(),
						fec: p.$w.find('[name=feccol]').val()
					};
					if(tmp.colegio!=''||tmp.cod!=''||tmp.fec!=''){
						p.$w.tabs("option", "active", 1);
						if(tmp.colegio==''){
							p.$w.find('[name=cole]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el lugar de estudio!',type: 'error'});
						}else if(tmp.cod==''){
							p.$w.find('[name=numcole]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el c&oacute;digo de colegiatura!',type: 'error'});
						}else if(tmp.fec==''){
							p.$w.find('[name=feccol]').datepicker('show');
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de colegiatura!',type: 'error'});
						}
						data.colegiatura = tmp;
					}
					/* Certificaciones */
					for(var i=0; i<p.$gb2.find('.item').length; i++){
						p.$w.tabs("option", "active", 1);
						var $row = p.$gb2.find('.item').eq(i);
						tmp = {
							descr: $row.find('[name=descr]').val(),
							lugar: $row.find('[name=lugar]').val(),
							fecini: $row.find('[name=ini]').val(),
							fecfin: $row.find('[name=fin]').val()
						};
						if(tmp.descr!=''||tmp.lugar!=''){
							if(tmp.descr==''){
								$row.find('[name=descr]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la descripci&oacute;n del lugar de estudio!',type: 'error'});
							}else if(tmp.lugar==''){
								$row.find('[name=lugar]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el lugar de estudio!',type: 'error'});
							}else if(tmp.fecini==''){
								$row.find('[name=ini]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de inicio de estudio!',type: 'error'});
							}else if(tmp.fecfin==''){
								$row.find('[name=fin]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de fin de estudio!',type: 'error'});
							}
							if(data.certificaciones==null) data.certificaciones = [];
							data.certificaciones.push(tmp);
						}
					}
					/* Idiomas */
					for(var i=0; i<p.$gb3.find('.item').length; i++){
						p.$w.tabs("option", "active", 1);
						var $row = p.$gb3.find('.item').eq(i);
						tmp = {
							idioma: $row.find('[name=idioma]').val(),
							lugar: $row.find('[name=lugar]').val()
						};
						if(tmp.idioma!=''||tmp.lugar!=''){
							if(tmp.idioma==''){
								$row.find('[name=idioma]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el idioma de estudio!',type: 'error'});
							}else if(tmp.lugar==''){
								$row.find('[name=lugar]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el lugar de estudio!',type: 'error'});
							}
							if($row.find('[id^=chkIdi1]').attr('checked')) tmp.habla = true;
							else tmp.habla = false;
							if($row.find('[id^=chkIdi2]').attr('checked')) tmp.lee = true;
							else tmp.lee = false;
							if($row.find('[id^=chkIdi3]').attr('checked')) tmp.escribe = true;
							else tmp.escribe = false;
							if(tmp.habla==false&&tmp.lee==false&&tmp.escribe==false) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe indicar si habla, lee o escribe en <b>"'+tmp.idioma+'"</b>!',type: 'error'});
							if(data.idiomas==null) data.idiomas = [];
							data.idiomas.push(tmp);
						}
					}
					/* Padre */
					tmp = p.$gb4.find('[name=enti]').data('data');
					if(tmp!=null){
						p.$w.tabs("option", "active", 2);
						if(data.familia==null) data.familia = {};
						data.familia.padre = {
							_id: tmp._id.$id,
							vivo: p.$gb4.find('input:radio[name^=rbtnPad]:checked').val(),
							fecnac: p.$gb4.find('[name=fec]').val()
						};
						if(data.familia.padre.vivo=='0') data.familia.padre.vivo = false;
						else data.familia.padre.vivo = true;
						if(data.familia.padre.fecnac==''){
							p.$gb4.find('[name=fec]').datepicker('show');
							return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nacimiento del padre del trabajador!',type: 'error'});
						}
					}
					/* Madre */
					tmp = p.$gb5.find('[name=enti]').data('data');
					if(tmp!=null){
						p.$w.tabs("option", "active", 2);
						if(data.familia==null) data.familia = {};
						data.familia.madre = {
							_id: tmp._id.$id,
							vivo: p.$gb5.find('input:radio[name^=rbtnMad]:checked').val(),
							fecnac: p.$gb5.find('[name=fec]').val()
						};
						if(data.familia.madre.vivo=='0') data.familia.madre.vivo = false;
						else data.familia.madre.vivo = true;
						if(data.familia.madre.fecnac==''){
							p.$gb5.find('[name=fec]').datepicker('show');
							return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nacimiento de la madre del trabajador!',type: 'error'});
						}
					}
					/* Hermanos */
					for(var i=0; i<p.$gb6.find('.item').length; i++){
						p.$w.tabs("option", "active", 2);
						var $row = p.$gb6.find('.item').eq(i);
						tmp = $row.find('[name=enti]').data('data');
						if(tmp!=null){
							if(data.familia==null) data.familia = {};
							var hno = {
								_id: tmp._id.$id,
								vivo: $row.find('input:radio[name^=rbtnViv]:checked').val(),
								fecnac: $row.find('[name=fec]').val()
							};
							if(hno.vivo=='0') hno.vivo = false;
							else hno.vivo = true;
							if(hno.fecnac==''){
								$row.find('[name=fec]').datepicker('show');
								return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nacimiento del hermano(a) del trabajador!',type: 'error'});
							}
							if(data.familia.hermanos==null) data.familia.hermanos = [];
							data.familia.hermanos.push(hno);
						}
					}
					/* Pareja */
					if(data.estado_civil!='SO'){
						tmp = p.$gb7.find('[name=enti]').data('data');
						if(tmp==null) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el conyuge del trabajador!',type: 'error'});
						if(data.familia==null) data.familia = {};
						data.familia.conyuge = {
							_id: tmp._id.$id,
							fecnac: p.$gb7.find('[name=fec]').val()
						};
						if(data.familia.conyuge.fecnac==''){
							p.$gb7.find('[name=fec]').datepicker('show');
							return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nacimiento del conyuge del trabajador!',type: 'error'});
						}
					}
					/* Hijos */
					for(var i=0; i<p.$gb8.find('.item').length; i++){
						p.$w.tabs("option", "active", 2);
						var $row = p.$gb8.find('.item').eq(i);
						tmp = $row.find('[name=enti]').data('data');
						if(tmp!=null){
							if(data.familia==null) data.familia = {};
							var hijo = {
								_id: tmp._id.$id,
								sexo: $row.find('input:radio[name^=rbtnSex]:checked').val(),
								fecnac: $row.find('[name=fec]').val(),
								estado_civil: $row.find('[name=estado] option:selected').val()
							};
							if(hijo.fecnac==''){
								$row.find('[name=fec]').datepicker('show');
								return  K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el nacimiento del hijo(a) del trabajador!',type: 'error'});
							}
							if(data.familia.hijos==null) data.familia.hijos = [];
							data.familia.hijos.push(hijo);
						}
					}
					/* Experiencia publica */
					for(var i=0; i<p.$gb9.find('.item').length; i++){
						p.$w.tabs("option", "active", 3);
						var $row = p.$gb9.find('.item').eq(i);
						tmp = {
							tipo: 'PU',
							lugar: $row.find('[name=instituto]').val(),
							cargo: $row.find('[name=cargo]').val(),
							motivo: $row.find('[name=salida]').val(),
							fecini: $row.find('[name=ini]').val(),
							fecfin: $row.find('[name=fin]').val()
						};
						if(tmp.lugar!=''||tmp.cargo!=''||tmp.motivo!=''){
							if(tmp.lugar==''){
								$row.find('[name=instituto]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el lugar de trabajo!',type: 'error'});
							}else if(tmp.cargo==''){
								$row.find('[name=cargo]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el cargo de trabajo!',type: 'error'});
							}else if(tmp.motivo==''){
								$row.find('[name=salida]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el motivo de cese de trabajo!',type: 'error'});
							}else if(tmp.fecini==''){
								$row.find('[name=ini]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de inicio de estudio!',type: 'error'});
							}else if(tmp.fecfin==''){
								$row.find('[name=fin]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de fin de estudio!',type: 'error'});
							}
							if(data.experiencia==null) data.experiencia = [];
							data.experiencia.push(tmp);
						}
					}
					/* Experiencia privada */
					for(var i=0; i<p.$gb10.find('.item').length; i++){
						p.$w.tabs("option", "active", 3);
						var $row = p.$gb10.find('.item').eq(i);
						tmp = {
							tipo: 'PR',
							lugar: $row.find('[name=instituto]').val(),
							cargo: $row.find('[name=cargo]').val(),
							motivo: $row.find('[name=salida]').val(),
							fecini: $row.find('[name=ini]').val(),
							fecfin: $row.find('[name=fin]').val()
						};
						if(tmp.lugar!=''||tmp.cargo!=''||tmp.motivo!=''){
							if(tmp.lugar==''){
								$row.find('[name=instituto]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el lugar de trabajo!',type: 'error'});
							}else if(tmp.cargo==''){
								$row.find('[name=cargo]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el cargo de trabajo!',type: 'error'});
							}else if(tmp.motivo==''){
								$row.find('[name=salida]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el motivo de cese de trabajo!',type: 'error'});
							}else if(tmp.fecini==''){
								$row.find('[name=ini]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de inicio de estudio!',type: 'error'});
							}else if(tmp.fecfin==''){
								$row.find('[name=fin]').datepicker('show');
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la fecha de fin de estudio!',type: 'error'});
							}
							if(data.experiencia==null) data.experiencia = [];
							data.experiencia.push(tmp);
						}
					}
					p.$w.tabs("option", "active",p.index);
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/save_ficha',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La ficha de <b>'+p.nomb+'</b> fue actualizada con &eacute;xito!'});
						K.closeWindow(p.$w.attr('id'));
						$('#mainPanel [name=btnBuscar]').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$gb1.find('[name=btnEli]').die('click');
				p.$gb1.find('[name=btnAgr]').die('click');
				p.$gb2.find('[name=btnEli]').die('click');
				p.$gb2.find('[name=btnAgr]').die('click');
				p.$gb3.find('[name=btnEli]').die('click');
				p.$gb3.find('[name=btnAgr]').die('click');
				p.$gb6.find('[name=btnEli]').die('click');
				p.$gb6.find('[name=btnAgr]').die('click');
				p.$gb6.find('[name=btnSelEnt]').die('click');
				p.$gb6.find('[name=btnAgrEnt]').die('click');
				p.$gb8.find('[name=btnEli]').die('click');
				p.$gb8.find('[name=btnAgr]').die('click');
				p.$gb8.find('[name=btnSelEnt]').die('click');
				p.$gb8.find('[name=btnAgrEnt]').die('click');
				p.$gb9.find('[name=btnEli]').die('click');
				p.$gb9.find('[name=btnAgr]').die('click');
				p.$gb10.find('[name=btnEli]').die('click');
				p.$gb10.find('[name=btnAgr]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowActTrab'+p.id);
				K.block({$element: p.$w});
				p.$w.tabs();
				p.$w.find('#tabs-2,#tabs-3,#tabs-4,fieldset').css('padding','0px');
				p.$w.find('#tabs-2,#tabs-3').css('height','410px').css('overflow','auto');
				p.$w.find('input:radio[name^=rbtnSexEnt]').each(function(){
					$(this).attr('id',$(this).attr('id')+p.id);
					$(this).attr('name',$(this).attr('name')+p.id);
				});
				p.$w.find('label[for^=rbtnSexEnt]').each(function(){
					$(this).attr('for',$(this).attr('for')+p.id);
				});
				p.$w.find('input:radio[name^=rbtnPrim]').each(function(){
					$(this).attr('id',$(this).attr('id')+p.id);
					$(this).attr('name',$(this).attr('name')+p.id);
				});
				p.$w.find('label[for^=rbtnPrim]').each(function(){
					$(this).attr('for',$(this).attr('for')+p.id);
				});
				p.$w.find('input:radio[name^=rbtnSec]').each(function(){
					$(this).attr('id',$(this).attr('id')+p.id);
					$(this).attr('name',$(this).attr('name')+p.id);
				});
				p.$w.find('label[for^=rbtnSec]').each(function(){
					$(this).attr('for',$(this).attr('for')+p.id);
				});
				p.$w.find('input:radio[name^=rbtnEst]').each(function(){
					$(this).attr('id',$(this).attr('id')+p.id);
					$(this).attr('name',$(this).attr('name')+p.id);
				});
				p.$w.find('label[for^=rbtnEst]').each(function(){
					$(this).attr('for',$(this).attr('for')+p.id);
				});
				p.$w.find('input:radio[name^=rbtnPad]').each(function(){
					$(this).attr('id',$(this).attr('id')+p.id);
					$(this).attr('name',$(this).attr('name')+p.id);
				});
				p.$w.find('label[for^=rbtnPad]').each(function(){
					$(this).attr('for',$(this).attr('for')+p.id);
				});
				p.$w.find('input:radio[name^=rbtnMad]').each(function(){
					$(this).attr('id',$(this).attr('id')+p.id);
					$(this).attr('name',$(this).attr('name')+p.id);
				});
				p.$w.find('label[for^=rbtnMad]').each(function(){
					$(this).attr('for',$(this).attr('for')+p.id);
				});
				p.$w.find('[name=rbtn]').buttonset();
				p.$w.find('[name=fecingadm]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				p.$w.find('[name=fecingben]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				p.$w.find('[name=fecnac]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				p.$w.find('[name=feccol]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				/* Primera grilla */
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$gb1 = p.$w.find('.gridBody:eq(0)');
				p.$gr1 = p.$w.find('.gridReference:eq(0)');
				p.$gb1.find('[name=btnAgr]').live('click',function(){
					var date = new Date();
					var $row = p.$gr1.clone();
					$row.find('[name=rbtnCom]').attr('name','rbtnCom'+date.toISOString());
					$row.find('[id=rbtnCom0-]').attr('id','rbtnCom0-'+date.toISOString());
					$row.find('[for=rbtnCom0-]').attr('for','rbtnCom0-'+date.toISOString());
					$row.find('[id=rbtnCom1-]').attr('id','rbtnCom1-'+date.toISOString());
					$row.find('[for=rbtnCom1-]').attr('for','rbtnCom1-'+date.toISOString());
					$row.find('li:eq(1)').buttonset();
					$row.find('li:eq(1) .ui-button').css('width','auto');
					$row.find('[name=ini]').attr('placeholder','Ejem: 1980-05-09').datepicker();
					$row.find('[name=fin]').attr('placeholder','Ejem: 1980-05-09').datepicker();
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb1.append( $row.children() );
					p.$gb1.find('[name=btnAgr]').remove();
					p.$gb1.find('.item').eq(p.$gb1.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb1.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb1.find('[name=btnEli]').live('click',function(){
					var date = new Date();
					$(this).closest('.item').remove();
					if(p.$gb1.find('.item').length<1){
						var $row = p.$gr1.clone();
						$row.find('[name=rbtnCom]').attr('name','rbtnCom'+date.toISOString());
						$row.find('[id=rbtnCom0-]').attr('id','rbtnCom0-'+date.toISOString());
						$row.find('[for=rbtnCom0-]').attr('for','rbtnCom0-'+date.toISOString());
						$row.find('[id=rbtnCom1-]').attr('id','rbtnCom1-'+date.toISOString());
						$row.find('[for=rbtnCom1-]').attr('for','rbtnCom1-'+date.toISOString());
						$row.find('li:eq(1)').buttonset();
						$row.find('li:eq(1) .ui-button').css('width','auto');
						$row.find('[name=ini]').attr('placeholder','Ejem: 1980-05-09').datepicker();
						$row.find('[name=fin]').attr('placeholder','Ejem: 1980-05-09').datepicker();
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb1.append( $row.children() );
					}
					p.$gb1.find('[name=btnAgr]').remove();
					p.$gb1.find('.item').eq(p.$gb1.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb1.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var date = new Date();
				var $row = p.$gr1.clone();
				$row.find('[name=rbtnCom]').attr('name','rbtnCom'+date.toISOString());
				$row.find('[id=rbtnCom0-]').attr('id','rbtnCom0-'+date.toISOString());
				$row.find('[for=rbtnCom0-]').attr('for','rbtnCom0-'+date.toISOString());
				$row.find('[id=rbtnCom1-]').attr('id','rbtnCom1-'+date.toISOString());
				$row.find('[for=rbtnCom1-]').attr('for','rbtnCom1-'+date.toISOString());
				$row.find('li:eq(1)').buttonset();
				$row.find('li:eq(1) .ui-button').css('width','auto');
				$row.find('[name=ini]').attr('disabled','disabled').datepicker();
				$row.find('[name=fin]').attr('disabled','disabled').datepicker();
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb1.append( $row.children() );
				p.$gb1.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				/* Segunda grilla */
				p.$w.find('.grid').eq(4).bind('scroll',function(){
					p.$w.find('.grid').eq(3).scrollLeft(p.$w.find('.grid').eq(4).scrollLeft());
				});
				p.$gb2 = p.$w.find('.gridBody:eq(2)');
				p.$gr2 = p.$w.find('.gridReference:eq(2)');
				p.$gb2.find('[name=btnAgr]').live('click',function(){
					var $row = p.$gr2.clone();
					$row.find('[name=ini]').attr('placeholder','Ejem: 1980-05-09').datepicker();
					$row.find('[name=fin]').attr('placeholder','Ejem: 1980-05-09').datepicker();
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb2.append( $row.children() );
					p.$gb2.find('[name=btnAgr]').remove();
					p.$gb2.find('.item').eq(p.$gb2.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb2.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb2.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$gb2.find('.item').length<1){
						var $row = p.$gr2.clone();
						$row.find('[name=ini]').attr('placeholder','Ejem: 1980-05-09').datepicker();
						$row.find('[name=fin]').attr('placeholder','Ejem: 1980-05-09').datepicker();
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb2.append( $row.children() );
					}
					p.$gb2.find('[name=btnAgr]').remove();
					p.$gb2.find('.item').eq(p.$gb2.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb2.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var $row = p.$gr2.clone();
				$row.find('[name=ini]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				$row.find('[name=fin]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb2.append( $row.children() );
				p.$gb2.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				/* Tercera grilla */
				p.$w.find('.grid').eq(6).bind('scroll',function(){
					p.$w.find('.grid').eq(5).scrollLeft(p.$w.find('.grid').eq(6).scrollLeft());
				});
				p.$gb3 = p.$w.find('.gridBody:eq(3)');
				p.$gr3 = p.$w.find('.gridReference:eq(3)');
				p.$gb3.find('[name=btnAgr]').live('click',function(){
					var date = new Date();
					var $row = p.$gr3.clone();
					$row.find('[id=chkIdi1-]').attr('id','chkIdi1-'+date.toISOString());
					$row.find('[for=chkIdi1-]').attr('for','chkIdi1-'+date.toISOString());
					$row.find('[id=chkIdi2-]').attr('id','chkIdi2-'+date.toISOString());
					$row.find('[for=chkIdi2-]').attr('for','chkIdi2-'+date.toISOString());
					$row.find('[id=chkIdi3-]').attr('id','chkIdi3-'+date.toISOString());
					$row.find('[for=chkIdi3-]').attr('for','chkIdi3-'+date.toISOString());
					$row.find('li:eq(1)').buttonset();
					$row.find('li:eq(1) .ui-button').css('width','auto');
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb3.append( $row.children() );
					p.$gb3.find('[name=btnAgr]').remove();
					p.$gb3.find('.item').eq(p.$gb3.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb3.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb3.find('[name=btnEli]').live('click',function(){
					var date = new Date();
					$(this).closest('.item').remove();
					if(p.$gb3.find('.item').length<1){
						var $row = p.$gr3.clone();
						$row.find('[id=chkIdi1-]').attr('id','chkIdi1-'+date.toISOString());
						$row.find('[for=chkIdi1-]').attr('for','chkIdi1-'+date.toISOString());
						$row.find('[id=chkIdi2-]').attr('id','chkIdi2-'+date.toISOString());
						$row.find('[for=chkIdi2-]').attr('for','chkIdi2-'+date.toISOString());
						$row.find('[id=chkIdi3-]').attr('id','chkIdi3-'+date.toISOString());
						$row.find('[for=chkIdi3-]').attr('for','chkIdi3-'+date.toISOString());
						$row.find('li:eq(1)').buttonset();
						$row.find('li:eq(1) .ui-button').css('width','auto');
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb3.append( $row.children() );
					}
					p.$gb3.find('[name=btnAgr]').remove();
					p.$gb3.find('.item').eq(p.$gb3.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb3.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var date = new Date();
				var $row = p.$gr3.clone();
				$row.find('[id=chkIdi1-]').attr('id','chkIdi1-'+date.toISOString());
				$row.find('[for=chkIdi1-]').attr('for','chkIdi1-'+date.toISOString());
				$row.find('[id=chkIdi2-]').attr('id','chkIdi2-'+date.toISOString());
				$row.find('[for=chkIdi2-]').attr('for','chkIdi2-'+date.toISOString());
				$row.find('[id=chkIdi3-]').attr('id','chkIdi3-'+date.toISOString());
				$row.find('[for=chkIdi3-]').attr('for','chkIdi3-'+date.toISOString());
				$row.find('li:eq(1)').buttonset();
				$row.find('li:eq(1) .ui-button').css('width','auto');
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb3.append( $row.children() );
				p.$gb3.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				/* Cuarta grilla -> padre */
				p.$gb4 = p.$w.find('.gridBody:eq(4)');
				p.$gb4.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbPadre,filter: [
					    {nomb: 'tipo_enti',value: 'P'}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$gb4.find('[name=btnAgr]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbPadre,reqs: {tipo_enti: 'P'}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.cbPadre = function(data){
					p.$gb4.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',data);
					if(data.fecnac!=null) p.$gb4.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(data.fecnac));
					else p.$gb4.find('[name=fec]').val('');
				};
				p.$gb4.find('li:eq(1)').buttonset();
				p.$gb4.find('.ui-button').css('width','auto');
				p.$gb4.find('[name=fec]').attr('disabled','disabled').datepicker();
				/* Quinta grilla -> madre */
				p.$gb5 = p.$w.find('.gridBody:eq(5)');
				p.$gb5.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbMadre,filter: [
					    {nomb: 'tipo_enti',value: 'P'}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$gb5.find('[name=btnAgr]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbMadre,reqs: {tipo_enti: 'P'}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.cbMadre = function(data){
					p.$gb5.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',data);
					if(data.fecnac!=null) p.$gb5.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(data.fecnac));
					else p.$gb5.find('[name=fec]').val('');
				};
				p.$gb5.find('li:eq(1)').buttonset();
				p.$gb5.find('.ui-button').css('width','auto');
				p.$gb5.find('[name=fec]').attr('placeholder','Ejem: 1980-05-09').datepicker();
				/* Sexta grilla -> hermanos */
				p.$w.find('.grid').eq(12).bind('scroll',function(){
					p.$w.find('.grid').eq(11).scrollLeft(p.$w.find('.grid').eq(12).scrollLeft());
				});
				p.$gb6 = p.$w.find('.gridBody:eq(6)');
				p.$gr6 = p.$w.find('.gridReference:eq(6)');
				p.$gb6.find('[name=btnAgr]').live('click',function(){
					var date = new Date();
					var $row = p.$gr6.clone();
					$row.find('[name=rbtnViv]').attr('name','rbtnViv'+date.toISOString());
					$row.find('[id=rbtnViv0-]').attr('id','rbtnViv0-'+date.toISOString());
					$row.find('[for=rbtnViv0-]').attr('for','rbtnViv0-'+date.toISOString());
					$row.find('[id=rbtnViv1-]').attr('id','rbtnViv1-'+date.toISOString());
					$row.find('[for=rbtnViv1-]').attr('for','rbtnViv1-'+date.toISOString());
					$row.find('li:eq(1)').buttonset();
					$row.find('[name=fec]').attr('disabled','disabled').datepicker();
					$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
					$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
					$row.find('.ui-button').css('width','auto');
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb6.append( $row.children() );
					p.$gb6.find('[name=btnAgr]').remove();
					p.$gb6.find('.item').eq(p.$gb6.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb6.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb6.find('[name=btnEli]').live('click',function(){
					var date = new Date();
					$(this).closest('.item').remove();
					if(p.$gb6.find('.item').length<1){
						var $row = p.$gr6.clone();
						$row.find('[name=rbtnViv]').attr('name','rbtnViv'+date.toISOString());
						$row.find('[id=rbtnViv0-]').attr('id','rbtnViv0-'+date.toISOString());
						$row.find('[for=rbtnViv0-]').attr('for','rbtnViv0-'+date.toISOString());
						$row.find('[id=rbtnViv1-]').attr('id','rbtnViv1-'+date.toISOString());
						$row.find('[for=rbtnViv1-]').attr('for','rbtnViv1-'+date.toISOString());
						$row.find('li:eq(1)').buttonset();
						$row.find('[name=fec]').attr('disabled','disabled').datepicker();
						$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
						$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
						$row.find('.ui-button').css('width','auto');
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb6.append( $row.children() );
					}
					p.$gb6.find('[name=btnAgr]').remove();
					p.$gb6.find('.item').eq(p.$gb6.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb6.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb6.find('[name=btnSelEnt]').live('click',function(){
					p.$gb6.find('.sel').removeClass('sel');
					$(this).closest('.item').addClass('sel');
					ciSearch.windowSearchEnti({callback: p.cbHer,filter: [
					    {nomb: 'tipo_enti',value: 'P'}
					]});
				});
				p.$gb6.find('[name=btnAgrEnt]').live('click',function(){
					p.$gb6.find('.sel').removeClass('sel');
					$(this).closest('.item').addClass('sel');
					ciCreate.windowNewEntidad({callBack: p.cbHer,reqs: {tipo_enti: 'P'}});
				});
				var date = new Date();
				var $row = p.$gr6.clone();
				$row.find('[name=rbtnViv]').attr('name','rbtnViv'+date.toISOString());
				$row.find('[id=rbtnViv0-]').attr('id','rbtnViv0-'+date.toISOString());
				$row.find('[for=rbtnViv0-]').attr('for','rbtnViv0-'+date.toISOString());
				$row.find('[id=rbtnViv1-]').attr('id','rbtnViv1-'+date.toISOString());
				$row.find('[for=rbtnViv1-]').attr('for','rbtnViv1-'+date.toISOString());
				$row.find('li:eq(1)').buttonset();
				$row.find('[name=fec]').attr('disabled','disabled').datepicker();
				$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
				$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
				$row.find('.ui-button').css('width','auto');
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb6.append( $row.children() );
				p.$gb6.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				p.cbHer = function(data){
					if(p.$gb6.find('[name='+data._id.$id+']').length<1){
						var $row = p.$gb6.find('.sel');
						$row.attr('name',data._id.$id);
						$row.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',data);
						if(data.fecnac!=null) $row.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(data.fecnac));
						else $row.find('[name=fec]').val('');
					}else K.notification({title: 'Entidad repetidad',text: 'No puede elegir a la misma entidad como hermano!',type: 'error'});
				};
				/* Setima grilla -> pareja */
				p.$w.find('[name=estado_civil]').buttonset();
				p.$w.find('[id^=rbtnEst]').click(function(){
					p.$w.find('[name=estado_civ] .grid').show();
				});
				p.$w.find('[id^=rbtnEst1]').click(function(){
					p.$w.find('[name=estado_civ] .grid').hide();
				}).click();
				p.$gb7 = p.$w.find('.gridBody:eq(7)');
				p.$gb7.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbPare,filter: [
					    {nomb: 'tipo_enti',value: 'P'}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$gb7.find('[name=btnAgr]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbPare,reqs: {tipo_enti: 'P'}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.cbPare = function(data){
					p.$gb7.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',data);
					if(data.fecnac!=null) p.$gb7.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(data.fecnac));
					else p.$gb7.find('[name=fec]').val('');
				};
				p.$gb7.find('.ui-button').css('width','auto');
				p.$gb7.find('[name=fec]').attr('disabled','disabled').datepicker();
				/* Octava grilla -> hijos */
				p.$w.find('.grid').eq(16).bind('scroll',function(){
					p.$w.find('.grid').eq(15).scrollLeft(p.$w.find('.grid').eq(16).scrollLeft());
				});
				p.$gb8 = p.$w.find('.gridBody:eq(8)');
				p.$gr8 = p.$w.find('.gridReference:eq(8)');
				p.$gb8.find('[name=btnAgr]').live('click',function(){
					var date = new Date();
					var $row = p.$gr8.clone();
					$row.find('[name=rbtnSex]').attr('name','rbtnSex'+date.toISOString());
					$row.find('[id=rbtnSex0-]').attr('id','rbtnSex0-'+date.toISOString());
					$row.find('[for=rbtnSex0-]').attr('for','rbtnSex0-'+date.toISOString());
					$row.find('[id=rbtnSex1-]').attr('id','rbtnSex1-'+date.toISOString());
					$row.find('[for=rbtnSex1-]').attr('for','rbtnSex1-'+date.toISOString());
					$row.find('li:eq(1)').buttonset();
					$row.find('[name=fec]').attr('disabled','disabled').datepicker();
					$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
					$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
					$row.find('.ui-button').css('width','auto');
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb8.append( $row.children() );
					p.$gb8.find('[name=btnAgr]').remove();
					p.$gb8.find('.item').eq(p.$gb8.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb8.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb8.find('[name=btnEli]').live('click',function(){
					var date = new Date();
					$(this).closest('.item').remove();
					if(p.$gb8.find('.item').length<1){
						var $row = p.$gr8.clone();
						$row.find('[name=rbtnSex]').attr('name','rbtnSex'+date.toISOString());
						$row.find('[id=rbtnSex0-]').attr('id','rbtnSex0-'+date.toISOString());
						$row.find('[for=rbtnSex0-]').attr('for','rbtnSex0-'+date.toISOString());
						$row.find('[id=rbtnSex1-]').attr('id','rbtnSex1-'+date.toISOString());
						$row.find('[for=rbtnSex1-]').attr('for','rbtnSex1-'+date.toISOString());
						$row.find('li:eq(1)').buttonset();
						$row.find('[name=fec]').attr('disabled','disabled').datepicker();
						$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
						$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
						$row.find('.ui-button').css('width','auto');
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb8.append( $row.children() );
					}
					p.$gb8.find('[name=btnAgr]').remove();
					p.$gb8.find('.item').eq(p.$gb8.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb8.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb8.find('[name=btnSelEnt]').live('click',function(){
					p.$gb8.find('.sel').removeClass('sel');
					$(this).closest('.item').addClass('sel');
					ciSearch.windowSearchEnti({callback: p.cbHij,filter: [
					    {nomb: 'tipo_enti',value: 'P'}
					]});
				});
				p.$gb8.find('[name=btnAgrEnt]').live('click',function(){
					p.$gb8.find('.sel').removeClass('sel');
					$(this).closest('.item').addClass('sel');
					ciCreate.windowNewEntidad({callBack: p.cbHij,reqs: {tipo_enti: 'P'}});
				});
				var date = new Date();
				var $row = p.$gr8.clone();
				$row.find('[name=rbtnSex]').attr('name','rbtnSex'+date.toISOString());
				$row.find('[id=rbtnSex0-]').attr('id','rbtnSex0-'+date.toISOString());
				$row.find('[for=rbtnSex0-]').attr('for','rbtnSex0-'+date.toISOString());
				$row.find('[id=rbtnSex1-]').attr('id','rbtnSex1-'+date.toISOString());
				$row.find('[for=rbtnSex1-]').attr('for','rbtnSex1-'+date.toISOString());
				$row.find('li:eq(1)').buttonset();
				$row.find('[name=fec]').attr('disabled','disabled').datepicker();
				$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
				$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
				$row.find('.ui-button').css('width','auto');
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb8.append( $row.children() );
				p.$gb8.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				p.cbHij = function(data){
					if(p.$gb8.find('[name='+data._id.$id+']').length<1){
						var $row = p.$gb8.find('.sel');
						$row.attr('name',data._id.$id);
						$row.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',data);
						if(data.fecnac!=null) $row.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(data.fecnac));
						else $row.find('[name=fec]').val('');
					}else K.notification({title: 'Entidad repetidad',text: 'No puede elegir a la misma entidad como hijo!',type: 'error'});
				};
				/* Novena grilla */
				p.$w.find('.grid').eq(18).bind('scroll',function(){
					p.$w.find('.grid').eq(17).scrollLeft(p.$w.find('.grid').eq(18).scrollLeft());
				});
				p.$gb9 = p.$w.find('.gridBody:eq(9)');
				p.$gr9 = p.$w.find('.gridReference:eq(9)');
				p.$gb9.find('[name=btnAgr]').live('click',function(){
					var $row = p.$gr9.clone();
					$row.find('[name=ini]').attr('disabled','disabled').datepicker();
					$row.find('[name=fin]').attr('disabled','disabled').datepicker();
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb9.append( $row.children() );
					p.$gb9.find('[name=btnAgr]').remove();
					p.$gb9.find('.item').eq(p.$gb9.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb9.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb9.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$gb9.find('.item').length<1){
						var $row = p.$gr9.clone();
						$row.find('[name=ini]').attr('disabled','disabled').datepicker();
						$row.find('[name=fin]').attr('disabled','disabled').datepicker();
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb9.append( $row.children() );
					}
					p.$gb9.find('[name=btnAgr]').remove();
					p.$gb9.find('.item').eq(p.$gb9.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb9.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var $row = p.$gr9.clone();
				$row.find('[name=ini]').attr('disabled','disabled').datepicker();
				$row.find('[name=fin]').attr('disabled','disabled').datepicker();
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb9.append( $row.children() );
				p.$gb9.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				/* Decima grilla */
				p.$w.find('.grid').eq(20).bind('scroll',function(){
					p.$w.find('.grid').eq(19).scrollLeft(p.$w.find('.grid').eq(20).scrollLeft());
				});
				p.$gb10 = p.$w.find('.gridBody:eq(10)');
				p.$gr10 = p.$w.find('.gridReference:eq(10)');
				p.$gb10.find('[name=btnAgr]').live('click',function(){
					var $row = p.$gr10.clone();
					$row.find('[name=ini]').attr('disabled','disabled').datepicker();
					$row.find('[name=fin]').attr('disabled','disabled').datepicker();
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$gb10.append( $row.children() );
					p.$gb10.find('[name=btnAgr]').remove();
					p.$gb10.find('.item').eq(p.$gb10.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb10.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$gb10.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$gb10.find('.item').length<1){
						var $row = p.$gr10.clone();
						$row.find('[name=ini]').attr('disabled','disabled').datepicker();
						$row.find('[name=fin]').attr('disabled','disabled').datepicker();
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$gb10.append( $row.children() );
					}
					p.$gb10.find('[name=btnAgr]').remove();
					p.$gb10.find('.item').eq(p.$gb10.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
					p.$gb10.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var $row = p.$gr10.clone();
				$row.find('[name=ini]').attr('disabled','disabled').datepicker();
				$row.find('[name=fin]').attr('disabled','disabled').datepicker();
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$gb10.append( $row.children() );
				p.$gb10.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$.post('pe/trab/edit_ficha','id='+p.id,function(data){
					p.enti = data.enti;
					if(p.enti.subsidio!=null) p.$w.find('[name=subsidio]').val(p.enti.subsidio);
					/*if(data.sist==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Sistemas de Pensi&oacute;n no especificados',text: 'Debe de ingresar al menos un Sistema de Pensi&oacute;n para poder crear una ficha de trabajador!',type: 'info'});
					}*/
					var $sel = p.$w.find('[name=sist]');
					$sel.append('<option>--</option>');
					if(data.sist!=null){
						for(var i=0; i<data.sist.length; i++){
							$sel.append('<option value="'+data.sist[i]._id.$id+'">'+data.sist[i].nomb+'</option>');
							$sel.find('option:last').data('data',data.sist[i]);
						}
					}
					if(p.enti.roles.trabajador.cod_aportante!=null) p.$w.find('[name=cod_apor]').val(p.enti.roles.trabajador.cod_aportante);
					if(p.enti.roles.trabajador.pension!=null) p.$w.find('[name=sist]').selectVal(p.enti.roles.trabajador.pension._id.$id);
					if(p.enti.estado_civil!=null){
            switch(p.enti.estado_civil){
              case "SO": p.$w.find('[id^=rbtnEst1]').attr('checked',true).click(); break;
              case "CA": p.$w.find('[id^=rbtnEst2]').attr('checked',true).click(); break;
              case "DI": p.$w.find('[id^=rbtnEst3]').attr('checked',true).click(); break;
              case "SE": p.$w.find('[id^=rbtnEst4]').attr('checked',true).click(); break;
              case "CO": p.$w.find('[id^=rbtnEst5]').attr('checked',true).click(); break;
              default: p.$w.find('[id^=rbtnEst1]').attr('checked',true).click(); break;
            }
           }else p.$w.find('[id^=rbtnEst1]').attr('checked',true).click();
          if(p.enti.sexo!=null){
            if(p.enti.sexo=='M'){
              p.$w.find('[name^=rbtnSexEnt]:eq(0)').attr('checked',true);
              p.$w.find('[name^=rbtnSexEnt]:eq(1)').attr('checked',false);
            }else{
              p.$w.find('[name^=rbtnSexEnt]:eq(0)').attr('checked',false);
              p.$w.find('[name^=rbtnSexEnt]:eq(1)').attr('checked',true);
            }
           }else{
              p.$w.find('[name^=rbtnSexEnt]:eq(0)').attr('checked',false);
              p.$w.find('[name^=rbtnSexEnt]:eq(1)').attr('checked',true);
            }
					if(data.ficha!=null){
						p.ficha = data.ficha;
						if(p.ficha.fec_adm_pub!=null) p.$w.find('[name=fecingadm]').val(ciHelper.dateFormatBDNotHour(p.ficha.fec_adm_pub));
						if(p.ficha.fec_adm_sbpa!=null) p.$w.find('[name=fecingben]').val(ciHelper.dateFormatBDNotHour(p.ficha.fec_adm_sbpa));
						if(p.ficha.fecnac!=null) p.$w.find('[name=fecnac]').val(ciHelper.dateFormatBDNotHour(p.ficha.fecnac));
						if(p.ficha.ref!=null) p.$w.find('[name=resolu]').val(p.ficha.ref);
						if(p.ficha.estudios.primaria==true){
							p.$w.find('[name^=rbtnPrim]:eq(0)').attr('checked',true);
							p.$w.find('[name^=rbtnPrim]:eq(1)').attr('checked',false);
						}else{
							p.$w.find('[name^=rbtnPrim]:eq(0)').attr('checked',false);
							p.$w.find('[name^=rbtnPrim]:eq(1)').attr('checked',true);
						}
						if(p.ficha.estudios.secundaria==true){
							p.$w.find('[name^=rbtnSec]:eq(0)').attr('checked',true);
							p.$w.find('[name^=rbtnSec]:eq(1)').attr('checked',false);
						}else{
							p.$w.find('[name^=rbtnSec]:eq(0)').attr('checked',false);
							p.$w.find('[name^=rbtnSec]:eq(1)').attr('checked',true);
						}
						if(p.ficha.estudios.superior!=null){
							p.$gb1.empty();
							for(var i=0; i<p.ficha.estudios.superior.length; i++){
								var $row = p.$gr1.clone();
								$row.find('[name=rbtnCom]').attr('name','rbtnCom'+p.id+i);
								$row.find('[id=rbtnCom0-]').attr('id','rbtnCom0-'+p.id+i);
								$row.find('[for=rbtnCom0-]').attr('for','rbtnCom0-'+p.id+i);
								$row.find('[id=rbtnCom1-]').attr('id','rbtnCom1-'+p.id+i);
								$row.find('[for=rbtnCom1-]').attr('for','rbtnCom1-'+p.id+i);
								if(p.ficha.estudios.superior[i].completa){
									$row.find('[name^=rbtnCom]:eq(0)').attr('checked',true);
									$row.find('[name^=rbtnCom]:eq(1)').attr('checked',false);
								}else{
									$row.find('[name^=rbtnCom]:eq(0)').attr('checked',false);
									$row.find('[name^=rbtnCom]:eq(1)').attr('checked',true);
								}
								$row.find('li:eq(1)').buttonset();
								$row.find('li:eq(1) .ui-button').css('width','auto');
								$row.find('[name=lugar]').val(p.ficha.estudios.superior[i].lugar);
								$row.find('[name=grado]').val(p.ficha.estudios.superior[i].grado);
								$row.find('[name=tipo]').selectVal(p.ficha.estudios.superior[i].tipo);
								$row.find('[name=ini]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.estudios.superior[i].fecini));
								$row.find('[name=fin]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.estudios.superior[i].fecfin));
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a class="item" />');
					        	p.$gb1.append( $row.children() );
							}
							p.$gb1.find('[name=btnAgr]').remove();
							p.$gb1.find('.item').eq(p.$gb1.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
							p.$gb1.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
						if(p.ficha.colegiatura!=null){
							p.$w.find('[name=cole]').val(p.ficha.colegiatura.colegio);
							p.$w.find('[name=numcole]').val(p.ficha.colegiatura.cod);
							p.$w.find('[name=feccol]').val(ciHelper.dateFormatBDNotHour(p.ficha.colegiatura.fec));
						}
						if(p.ficha.certificaciones!=null){
							p.$gb2.empty();
							for(var i=0; i<p.ficha.certificaciones.length; i++){
								var $row = p.$gr2.clone();
								$row.find('[name=descr]').val(p.ficha.certificaciones[i].descr);
								$row.find('[name=lugar]').val(p.ficha.certificaciones[i].lugar);
								$row.find('[name=ini]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.certificaciones[i].fecini));
								$row.find('[name=fin]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.certificaciones[i].fecfin));
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a class="item" />');
					        	p.$gb2.append( $row.children() );
							}
							p.$gb2.find('[name=btnAgr]').remove();
							p.$gb2.find('.item').eq(p.$gb2.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
							p.$gb2.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
						if(p.ficha.idiomas!=null){
							p.$gb3.empty();
							for(var i=0; i<p.ficha.idiomas.length; i++){
								var $row = p.$gr3.clone();
								$row.find('[id=chkIdi1-]').attr('id','chkIdi1-'+p.id+i);
								$row.find('[for=chkIdi1-]').attr('for','chkIdi1-'+p.id+i);
								if(p.ficha.idiomas[i].habla==true) $row.find('[id^=chkIdi1-]').attr('checked',true);
								$row.find('[id=chkIdi2-]').attr('id','chkIdi2-'+p.id+i);
								$row.find('[for=chkIdi2-]').attr('for','chkIdi2-'+p.id+i);
								if(p.ficha.idiomas[i].lee==true) $row.find('[id^=chkIdi2-]').attr('checked',true);
								$row.find('[id=chkIdi3-]').attr('id','chkIdi3-'+p.id+i);
								$row.find('[for=chkIdi3-]').attr('for','chkIdi3-'+p.id+i);
								if(p.ficha.idiomas[i].escribe==true) $row.find('[id^=chkIdi3-]').attr('checked',true);
								$row.find('li:eq(1)').buttonset();
								$row.find('li:eq(1) .ui-button').css('width','auto');
								$row.find('[name=lugar]').val(p.ficha.idiomas[i].lugar);
								$row.find('[name=idioma]').val(p.ficha.idiomas[i].idioma);
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a class="item" />');
					        	p.$gb3.append( $row.children() );
							}
							p.$gb3.find('[name=btnAgr]').remove();
							p.$gb3.find('.item').eq(p.$gb1.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
							p.$gb3.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
						if(p.ficha.familia!=null){
							if(p.ficha.familia.padre!=null){
								p.$w.find('[name=padre] [name=enti]').html(ciHelper.enti.formatName(p.ficha.familia.padre)).data('data',p.ficha.familia.padre);
								if(p.ficha.familia.padre.vivo){
									p.$w.find('[name=padre] [name^=rbtnPad]:eq(0)').attr('checked',true);
									p.$w.find('[name=padre] [name^=rbtnPad]:eq(1)').attr('checked',false);
								}else{
									p.$w.find('[name=padre] [name^=rbtnPad]:eq(0)').attr('checked',false);
									p.$w.find('[name=padre] [name^=rbtnPad]:eq(1)').attr('checked',true);
								}
								p.$w.find('[name=padre] li:eq(1)').buttonset('refresh');
								p.$w.find('[name=padre] .ui-button').css('width','auto');
								p.$w.find('[name=padre] [name=fec]').val(ciHelper.dateFormatBDNotHour(p.ficha.familia.padre.fecnac));
							}
							if(p.ficha.familia.madre!=null){
								p.$w.find('[name=madre] [name=enti]').html(ciHelper.enti.formatName(p.ficha.familia.madre)).data('data',p.ficha.familia.madre);
								if(p.ficha.familia.madre.vivo){
									p.$w.find('[name=madre] [name^=rbtnMad]:eq(0)').attr('checked',true);
									p.$w.find('[name=madre] [name^=rbtnMad]:eq(1)').attr('checked',false);
								}else{
									p.$w.find('[name=madre] [name^=rbtnMad]:eq(0)').attr('checked',false);
									p.$w.find('[name=madre] [name^=rbtnMad]:eq(1)').attr('checked',true);
								}
								p.$w.find('[name=madre] li:eq(1)').buttonset('refresh');
								p.$w.find('[name=madre] .ui-button').css('width','auto');
								p.$w.find('[name=madre] [name=fec]').val(ciHelper.dateFormatBDNotHour(p.ficha.familia.madre.fecnac));
							}
							if(p.ficha.familia.hermanos!=null){
								p.$gb6.empty();
								for(var i=0; i<p.ficha.familia.hermanos.length; i++){
									var $row = p.$gr6.clone();
									$row.find('[name=rbtnViv]').attr('name','rbtnViv'+p.id+i);
									$row.find('[id=rbtnViv0-]').attr('id','rbtnViv0-'+p.id+i);
									$row.find('[for=rbtnViv0-]').attr('for','rbtnViv0-'+p.id+i);
									$row.find('[id=rbtnViv1-]').attr('id','rbtnViv1-'+p.id+i);
									$row.find('[for=rbtnViv1-]').attr('for','rbtnViv1-'+p.id+i);
									if(p.ficha.familia.hermanos[i].vivo){
										$row.find('[name^=rbtnViv]:eq(0)').attr('checked',true);
										$row.find('[name^=rbtnViv]:eq(1)').attr('checked',false);
									}else{
										$row.find('[name^=rbtnViv]:eq(0)').attr('checked',false);
										$row.find('[name^=rbtnViv]:eq(1)').attr('checked',true);
									}
									$row.find('li:eq(1)').buttonset();
									$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
									$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
									$row.find('.ui-button').css('width','auto');
									$row.find('[name=enti]').html(ciHelper.enti.formatName(p.ficha.familia.hermanos[i])).data('data',p.ficha.familia.hermanos[i]);
									$row.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(p.ficha.familia.hermanos[i].fecnac)).attr('disabled','disabled').datepicker();
									$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
									$row.wrapInner('<a class="item" name="'+p.ficha.familia.hermanos[i]._id.$id+'" />');
						        	p.$gb6.append( $row.children() );
								}
								p.$gb6.find('[name=btnAgr]').remove();
								p.$gb6.find('.item').eq(p.$gb6.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
								p.$gb6.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							}
							if(p.ficha.familia.conyuge!=null){
								p.$gb7.find('[name=enti]').html(ciHelper.enti.formatName(p.ficha.familia.conyuge)).data('data',p.ficha.familia.conyuge);
								p.$gb7.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(p.ficha.familia.conyuge.fecnac));
							}
							if(p.ficha.familia.hijos!=null){
								p.$gb8.empty();
								for(var i=0; i<p.ficha.familia.hijos.length; i++){
									var $row = p.$gr8.clone();
									$row.find('[name=rbtnSex]').attr('name','rbtnSex'+p.id+i);
									$row.find('[id=rbtnSex0-]').attr('id','rbtnSex0-'+p.id+i);
									$row.find('[for=rbtnSex0-]').attr('for','rbtnSex0-'+p.id+i);
									$row.find('[id=rbtnSex1-]').attr('id','rbtnSex1-'+p.id+i);
									$row.find('[for=rbtnSex1-]').attr('for','rbtnSex1-'+p.id+i);
									if(p.ficha.familia.hijos[i].sexo=='M'){
										$row.find('[name^=rbtnSex]:eq(0)').attr('checked',true);
										$row.find('[name^=rbtnSex]:eq(1)').attr('checked',false);
									}else{
										$row.find('[name^=rbtnSex]:eq(0)').attr('checked',false);
										$row.find('[name^=rbtnSex]:eq(1)').attr('checked',true);
									}
									$row.find('li:eq(1)').buttonset();
									$row.find('[name=btnSelEnt]').button({icons: {primary: 'ui-icon-search'}});
									$row.find('[name=btnAgrEnt]').button({icons: {primary: 'ui-icon-plusthick'}});
									$row.find('.ui-button').css('width','auto');
									$row.find('[name=enti]').html(ciHelper.enti.formatName(p.ficha.familia.hijos[i])).data('data',p.ficha.familia.hijos[i]);
									$row.find('[name=fec]').val(ciHelper.dateFormatBDNotHour(p.ficha.familia.hijos[i].fecnac)).attr('disabled','disabled').datepicker();
									$row.find('[name=estado]').selectVal(p.ficha.familia.hijos[i].estado_civil);
									$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
									$row.wrapInner('<a class="item" name="'+p.ficha.familia.hijos[i]._id.$id+'" />');
						        	p.$gb8.append( $row.children() );
								}
								p.$gb8.find('[name=btnAgr]').remove();
								p.$gb8.find('.item').eq(p.$gb8.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
								p.$gb8.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							}
						}
						if(p.ficha.experiencia){
							p.$gb9.empty();
							p.$gb10.empty();
							for(var i=0; i<p.ficha.experiencia.length; i++){
								if(p.ficha.experiencia[i].tipo=='PU'){
									var $row = p.$gr9.clone();
									$row.find('[name=instituto]').val(p.ficha.experiencia[i].lugar);
									$row.find('[name=cargo]').val(p.ficha.experiencia[i].cargo);
									$row.find('[name=salida]').val(p.ficha.experiencia[i].motivo);
									$row.find('[name=ini]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecini));
									$row.find('[name=fin]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecfin));
									$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
									$row.wrapInner('<a class="item" />');
						        	p.$gb9.append( $row.children() );
								}else{
									var $row = p.$gr10.clone();
									$row.find('[name=instituto]').val(p.ficha.experiencia[i].lugar);
									$row.find('[name=cargo]').val(p.ficha.experiencia[i].cargo);
									$row.find('[name=salida]').val(p.ficha.experiencia[i].motivo);
									$row.find('[name=ini]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecini));
									$row.find('[name=fin]').attr('disabled','disabled').datepicker().val(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecfin));
									$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
									$row.wrapInner('<a class="item" />');
						        	p.$gb10.append( $row.children() );
								}
							}
							if(p.$gb9.find('.item').length<1){
								var $row = p.$gr9.clone();
								$row.find('[name=ini]').attr('disabled','disabled').datepicker();
								$row.find('[name=fin]').attr('disabled','disabled').datepicker();
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a class="item" />');
					        	p.$gb9.append( $row.children() );
								p.$gb9.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							}
							if(p.$gb10.find('.item').length<1){
								var $row = p.$gr10.clone();
								$row.find('[name=ini]').attr('disabled','disabled').datepicker();
								$row.find('[name=fin]').attr('disabled','disabled').datepicker();
								$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a class="item" />');
					        	p.$gb10.append( $row.children() );
								p.$gb10.find('.[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							}
							p.$gb9.find('[name=btnAgr]').remove();
							p.$gb9.find('.item').eq(p.$gb9.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
							p.$gb9.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							p.$gb10.find('[name=btnAgr]').remove();
							p.$gb10.find('.item').eq(p.$gb10.find('.item').length-1).find('li:last').append('<button name="btnAgr">Agregar</button>');
							p.$gb10.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
						if(p.enti.sexo=='M'){
							p.$w.find('[name^=rbtnSexEnt]:eq(0)').attr('checked',true);
							p.$w.find('[name^=rbtnSexEnt]:eq(1)').attr('checked',false);
						}else{
							p.$w.find('[name^=rbtnSexEnt]:eq(0)').attr('checked',false);
							p.$w.find('[name^=rbtnSexEnt]:eq(1)').attr('checked',true);
						}
						if(p.enti.sangre!=null) p.$w.find('[name=grupo]').val(p.enti.sangre);
						if(p.enti.roles.trabajador.cod_aportante!=null) p.$w.find('[name=cod_apor]').val(p.enti.roles.trabajador.cod_aportante);
						if(p.enti.roles.trabajador.pension!=null) p.$w.find('[name=sist]').selectVal(p.enti.roles.trabajador.pension._id.$id);
						p.$w.find('[name^=rbtnEst]').attr('checked',false);
						switch(p.enti.estado_civil){
							case "SO": p.$w.find('[id^=rbtnEst1]').attr('checked',true).click(); break;
							case "CA": p.$w.find('[id^=rbtnEst2]').attr('checked',true).click(); break;
							case "DI": p.$w.find('[id^=rbtnEst3]').attr('checked',true).click(); break;
							case "SE": p.$w.find('[id^=rbtnEst4]').attr('checked',true).click(); break;
							case "CO": p.$w.find('[id^=rbtnEst5]').attr('checked',true).click(); break;
						}
						p.$w.find('[name=rbtn]').buttonset('refresh');
						p.$w.find('[name=estado_civil]').buttonset('refresh');
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsTrab'+p.id,
			title: 'Detalles de trabajador: '+p.nomb,
			contentURL: 'pe/trab/details',
			icon: 'ui-icon-person',
			width: 670,
			height: 420,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsTrab'+p.id);
				K.block({$element: p.$w});
				p.$w.tabs();
				p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4,#tabs-5,#tabs-6,#tabs-7,fieldset').css('padding','0px');
				p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4,#tabs-5,#tabs-6,#tabs-7').css('height','380px').css('overflow','auto');
				$.post('pe/trab/get_trab','id='+p.id,function(data){
					p.enti = data.enti;
					p.$1 = p.$w.find('#tabs-1');
					if(p.enti.imagen!=null) p.$1.find('[name=foto]').attr('src','ci/files/get?id='+p.enti.imagen.$id);
					else p.$1.find('[name=foto]').removeAttr('src');
					p.$1.find('[name=nomb]').html( ciHelper.enti.formatName(p.enti) ).attr('title',ciHelper.enti.formatName(p.enti)).tooltip();
					p.$1.find('[name=docident]').html( p.enti.docident[0].num );
					if(p.enti.domicilios!=null) p.$1.find('[name=direc]').html( p.enti.domicilios[0].direccion ).attr('title',p.enti.domicilios[0].direccion).tooltip();
					else p.$1.find('[name=direc]').html('--');
					if(p.enti.telefonos!=null) p.$1.find('[name=telf]').html( p.enti.telefonos[0].num );
					else p.$1.find('[name=telf]').html('--');
					if(p.enti.roles.trabajador.cargo.nomb!=null)
						p.$1.find('[name=cargo]').html(p.enti.roles.trabajador.cargo.nomb);
					else
						p.$1.find('[name=cargo]').html(p.enti.roles.trabajador.cargo.funcion);
					p.$1.find('[name=orga]').html(p.enti.roles.trabajador.organizacion.nomb);
					if(p.enti.roles.trabajador.nivel!=null){
						p.$1.find('[name=nivel]').html(p.enti.roles.trabajador.nivel.nomb);
					}else p.$1.find('[name=nivel]').closest('fieldset').hide();
					if(p.enti.roles.trabajador.nivel_carrera!=null) p.$1.find('[name=nivel2]').html(p.enti.roles.trabajador.nivel_carrera.nomb);
					else p.$1.find('[name=nivel2]').closest('tr').hide();
					p.$1.find('[name=descr]').html(p.enti.roles.trabajador.local.descr);
					p.$1.find('[name=direccion]').html(p.enti.roles.trabajador.local.direccion);
					if(p.enti.roles.trabajador.turno!=null) p.$1.find('[name=turno]').html(p.enti.roles.trabajador.turno.nomb);
					if(p.enti.roles.trabajador.cargo_clasif!=null){
						p.$1.find('[name=clas]').html(p.enti.roles.trabajador.cargo_clasif.nomb);
						p.$1.find('[name=codclas]').html(p.enti.roles.trabajador.cargo_clasif.cod);
					}else p.$1.find('[name=clas]').closest('fieldset').hide();
					if(p.enti.roles.trabajador.grupo_ocup!=null){
						p.$1.find('[name=grup]').html(p.enti.roles.trabajador.grupo_ocup.nomb);
						p.$1.find('[name=codgrup]').html(p.enti.roles.trabajador.grupo_ocup.sigla);
					}else p.$1.find('[name=codgrup]').closest('fieldset').hide();
					p.$1.find('[name=tipo]').html(p.enti.roles.trabajador.contrato.nomb);
					p.$1.find('[name=tarjeta]').html(p.enti.roles.trabajador.cod_tarjeta);
					p.$1.find('[name=essalud]').html(p.enti.roles.trabajador.essalud);
					if(p.enti.roles.trabajador.tipo!=null) p.$1.find('[name=tipotrab]').html(peEntiTrab.tipo[p.enti.roles.trabajador.tipo]);
					if(p.enti.roles.trabajador.eps!=null) p.$1.find('[name=afilia]').html((p.enti.roles.trabajador.eps==true)?'Afiliado a EPS':'No afiliado');
					p.$2 = p.$w.find('#tabs-2');
					p.$2.find('[name=sexo]').html(peEntiTrab.sexo[p.enti.sexo]);
					if(p.enti.sangre!=null) p.$w.find('[name=grupo]').html(p.enti.sangre);
					if(p.enti.roles.trabajador.cese!=null){
						p.$2.find('[name=feccese]').html(ciHelper.dateFormatBDNotHour(p.enti.roles.trabajador.cese.fec));
						p.$2.find('[name=cesemotivo]').html(peEntiTrab.cese[p.enti.roles.trabajador.cese.motivo]);
						p.$2.find('[name=ceseobserv]').html(p.enti.roles.trabajador.cese.observ);
					}else p.$2.find('fieldset:last').hide();
					if(data.ficha!=null){
						p.ficha = data.ficha;
						if(p.enti.roles.trabajador.pension!=null) p.$w.find('[name=sist]').html(p.enti.roles.trabajador.pension.nomb);
						if(p.ficha.fec_adm_pub!=null) p.$w.find('[name=fecingadm]').html(ciHelper.dateFormatBDNotHour(p.ficha.fec_adm_pub));
						if(p.ficha.fec_adm_sbpa!=null) p.$w.find('[name=fecingben]').html(ciHelper.dateFormatBDNotHour(p.ficha.fec_adm_sbpa));
						if(p.enti.roles.trabajador.cod_aportante!=null) p.$w.find('[name=cod_apor]').html(p.enti.roles.trabajador.cod_aportante);
						if(p.ficha.ref!=null) p.$w.find('[name=resolu]').html(p.ficha.ref);
						p.$3 = p.$w.find('#tabs-3');
						if(p.ficha.estudios.primaria==true) p.$3.find('[name=primaria]').html('Completa');
						else p.$3.find('[name=primaria]').html('Incompleta');
						if(p.ficha.estudios.secundaria==true) p.$3.find('[name=secundaria]').html('Completa');
						else p.$3.find('[name=secundaria]').html('Incompleta');
						p.$w.find('.grid').eq(1).bind('scroll',function(){
							p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
						});
						p.$gb1 = p.$w.find('.gridBody:eq(0)');
						p.$gr1 = p.$w.find('.gridReference:eq(0)');
						if(p.ficha.estudios.superior!=null){
							for(var i=0; i<p.ficha.estudios.superior.length; i++){
								var $row = p.$gr1.clone();
								$row.find('li:eq(0)').html(peEntiTrab.tipo_superior[p.ficha.estudios.superior[i].tipo]);
								if(p.ficha.estudios.superior[i].completa) $row.find('li:eq(1)').html('Completa');
								else $row.find('li:eq(1)').html('Incompleta');
								$row.find('li:eq(2)').html(ciHelper.dateFormatBDNotHour(p.ficha.estudios.superior[i].fecini));
								$row.find('li:eq(3)').html(ciHelper.dateFormatBDNotHour(p.ficha.estudios.superior[i].fecfin));
								$row.find('li:eq(4)').html(p.ficha.estudios.superior[i].lugar);
								$row.find('li:eq(5)').html(p.ficha.estudios.superior[i].grado);
								$row.wrapInner('<a class="item" />');
					        	p.$gb1.append( $row.children() );
							}
						}if(p.ficha.colegiatura!=null){
							p.$3.find('[name=cole]').html(p.ficha.colegiatura.colegio);
							p.$3.find('[name=numcole]').html(p.ficha.colegiatura.cod);
							p.$3.find('[name=feccol]').html(ciHelper.dateFormatBDNotHour(p.ficha.colegiatura.fec));
						}
						p.$w.find('.grid').eq(4).bind('scroll',function(){
							p.$w.find('.grid').eq(3).scrollLeft(p.$w.find('.grid').eq(4).scrollLeft());
						});
						p.$gb2 = p.$w.find('.gridBody:eq(2)');
						p.$gr2 = p.$w.find('.gridReference:eq(2)');
						if(p.ficha.certificaciones!=null){
							for(var i=0; i<p.ficha.certificaciones.length; i++){
								var $row = p.$gr2.clone();
								$row.find('li:eq(0)').html(p.ficha.certificaciones[i].descr);
								$row.find('li:eq(1)').html(p.ficha.certificaciones[i].lugar);
								$row.find('li:eq(2)').html(ciHelper.dateFormatBDNotHour(p.ficha.certificaciones[i].fecini));
								$row.find('li:eq(3)').html(ciHelper.dateFormatBDNotHour(p.ficha.certificaciones[i].fecfin));
								$row.wrapInner('<a class="item" />');
					        	p.$gb2.append( $row.children() );
							}
						}
						p.$w.find('.grid').eq(6).bind('scroll',function(){
							p.$w.find('.grid').eq(5).scrollLeft(p.$w.find('.grid').eq(6).scrollLeft());
						});
						p.$gb3 = p.$w.find('.gridBody:eq(3)');
						p.$gr3 = p.$w.find('.gridReference:eq(3)');
						if(p.ficha.idiomas!=null){
							for(var i=0; i<p.ficha.idiomas.length; i++){
								var $row = p.$gr3.clone();
								$row.find('[id=chkIdi1-]').attr('id','chkIdi1-'+p.id+i);
								$row.find('[for=chkIdi1-]').attr('for','chkIdi1-'+p.id+i);
								if(p.ficha.idiomas[i].habla==true) $row.find('[id^=chkIdi1-]').attr('checked',true);
								$row.find('[id=chkIdi2-]').attr('id','chkIdi2-'+p.id+i);
								$row.find('[for=chkIdi2-]').attr('for','chkIdi2-'+p.id+i);
								if(p.ficha.idiomas[i].lee==true) $row.find('[id^=chkIdi2-]').attr('checked',true);
								$row.find('[id=chkIdi3-]').attr('id','chkIdi3-'+p.id+i);
								$row.find('[for=chkIdi3-]').attr('for','chkIdi3-'+p.id+i);
								if(p.ficha.idiomas[i].escribe==true) $row.find('[id^=chkIdi3-]').attr('checked',true);
								$row.find('li:eq(1)').buttonset();
								$row.find('li:eq(1) .ui-button').css('width','auto');
								$row.find('[id^=chkIdi]').button( "option", "disabled", true );
								$row.find('li:eq(0)').html(p.ficha.idiomas[i].idioma);
								$row.find('li:eq(2)').html(p.ficha.idiomas[i].lugar);
								$row.wrapInner('<a class="item" />');
					        	p.$gb3.append( $row.children() );
							}
						}
						if(p.ficha.familia!=null){
							if(p.ficha.familia.padre!=null){
								p.$w.find('[name=padre] li:eq(0)').html(ciHelper.enti.formatName(p.ficha.familia.padre));
								if(p.ficha.familia.padre.vivo) p.$w.find('[name=padre] li:eq(1)').html('Si');
								else p.$w.find('[name=padre] li:eq(1)').html('No');
								p.$w.find('[name=padre] li:eq(2)').html(ciHelper.dateFormatBDNotHour(p.ficha.familia.padre.fecnac));
							}
							if(p.ficha.familia.madre!=null){
								p.$w.find('[name=madre] li:eq(0)').html(ciHelper.enti.formatName(p.ficha.familia.madre));
								if(p.ficha.familia.madre.vivo) p.$w.find('[name=madre] li:eq(1)').html('Si');
								else p.$w.find('[name=madre] li:eq(1)').html('No');
								p.$w.find('[name=madre] li:eq(2)').html(ciHelper.dateFormatBDNotHour(p.ficha.familia.madre.fecnac));
							}
							p.$w.find('.grid').eq(12).bind('scroll',function(){
								p.$w.find('.grid').eq(11).scrollLeft(p.$w.find('.grid').eq(12).scrollLeft());
							});
							p.$gb6 = p.$w.find('.gridBody:eq(6)');
							p.$gr6 = p.$w.find('.gridReference:eq(6)');
							if(p.ficha.familia.hermanos!=null){
								for(var i=0; i<p.ficha.familia.hermanos.length; i++){
									var $row = p.$gr6.clone();
									$row.find('li:eq(0)').html(ciHelper.enti.formatName(p.ficha.familia.hermanos[i]));
									if(p.ficha.familia.hermanos[i].vivo) $row.find('li:eq(1)').html('Si');
									else $row.find('li:eq(1)').html('No');
									$row.find('li:eq(2)').html(ciHelper.dateFormatBDNotHour(p.ficha.familia.hermanos[i].fecnac));
									$row.wrapInner('<a class="item" />');
						        	p.$gb6.append( $row.children() );
								}
							}
							p.$w.find('[name=estado_civil]').html(peEntiTrab.estado_civil[p.enti.estado_civil]);
							p.$gb7 = p.$w.find('.gridBody:eq(7)');
							p.$gr7 = p.$w.find('.gridReference:eq(7)');
							if(p.ficha.familia.conyuge!=null){
								p.$gb7.find('li:eq(0)').html(ciHelper.enti.formatName(p.ficha.familia.conyuge));
								p.$gb7.find('li:eq(1)').html(ciHelper.dateFormatBDNotHour(p.ficha.familia.conyuge.fecnac));
							}
							p.$w.find('.grid').eq(16).bind('scroll',function(){
								p.$w.find('.grid').eq(15).scrollLeft(p.$w.find('.grid').eq(16).scrollLeft());
							});
							p.$gb8 = p.$w.find('.gridBody:eq(8)');
							p.$gr8 = p.$w.find('.gridReference:eq(8)');
							if(p.ficha.familia.hijos!=null){
								for(var i=0; i<p.ficha.familia.hijos.length; i++){
									var $row = p.$gr8.clone();
									$row.find('li:eq(0)').html(ciHelper.enti.formatName(p.ficha.familia.hijos[i]));
									$row.find('li:eq(1)').html(peEntiTrab.sexo[p.ficha.familia.hijos[i].sexo]);
									$row.find('li:eq(2)').html(ciHelper.dateFormatBDNotHour(p.ficha.familia.hijos[i].fecnac)).attr('disabled','disabled').datepicker();
									$row.find('li:eq(3)').html(peEntiTrab.estado_civil[p.ficha.familia.hijos[i].estado_civil]);
									$row.wrapInner('<a class="item" />');
						        	p.$gb8.append( $row.children() );
								}
							}
						}else p.$w.tabs("disable", 3);
						if(p.ficha.experiencia){
							p.$w.find('.grid').eq(18).bind('scroll',function(){
								p.$w.find('.grid').eq(17).scrollLeft(p.$w.find('.grid').eq(18).scrollLeft());
							});
							p.$gb9 = p.$w.find('.gridBody:eq(9)');
							p.$gr9 = p.$w.find('.gridReference:eq(9)');
							p.$w.find('.grid').eq(20).bind('scroll',function(){
								p.$w.find('.grid').eq(19).scrollLeft(p.$w.find('.grid').eq(20).scrollLeft());
							});
							p.$gb10 = p.$w.find('.gridBody:eq(10)');
							p.$gr10 = p.$w.find('.gridReference:eq(10)');
							for(var i=0; i<p.ficha.experiencia.length; i++){
								if(p.ficha.experiencia[i].tipo=='PU'){
									var $row = p.$gr9.clone();
									$row.find('li:eq(0)').html(p.ficha.experiencia[i].lugar);
									$row.find('li:eq(1)').html(p.ficha.experiencia[i].cargo);
									$row.find('li:eq(2)').html(p.ficha.experiencia[i].motivo);
									$row.find('li:eq(3)').html(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecini));
									$row.find('li:eq(4)').html(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecfin));
									$row.wrapInner('<a class="item" />');
						        	p.$gb9.append( $row.children() );
								}else{
									var $row = p.$gr10.clone();
									$row.find('li:eq(0)').html(p.ficha.experiencia[i].lugar);
									$row.find('li:eq(1)').html(p.ficha.experiencia[i].cargo);
									$row.find('li:eq(2)').html(p.ficha.experiencia[i].motivo);
									$row.find('li:eq(3)').html(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecini));
									$row.find('li:eq(4)').html(ciHelper.dateFormatBDNotHour(p.ficha.experiencia[i].fecfin));
									$row.wrapInner('<a class="item" />');
						        	p.$gb10.append( $row.children() );
								}
							}
						}else p.$w.tabs("disable", 4);
						/*p.$w.tabs("disable", 5);
						p.$w.tabs("disable", 6);
						if(p.ficha.mer_dem!=null){
							p.$gb11 = p.$w.find('#tabs-6');
							p.$gb12 = p.$w.find('#tabs-7');
							p.$w.tabs("disable", 5);
							p.$w.tabs("disable", 6);
							for(var i=0,j=p.ficha.mer_dem.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = p.ficha.mer_dem[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=motivo]').html(result.motivo);
								$row.find('[name=rpta]').html(result.rpta);
								$row.find('[name=ref]').html(result.ref);
								if(result.tipo=='M'){
									$row.find('label:eq(1)').html('Premio');
									p.$gb11.append($row.children());
									p.$w.tabs("enable", 5);
								}else{
									$row.find('label:eq(1)').html('Sanci&oacute;n');
									p.$gb12.append($row.children());
									p.$w.tabs("enable", 6);
								}
							}
						}*/
					}else{
						p.$w.tabs("disable", 1);
						p.$w.tabs("disable", 2);
						p.$w.tabs("disable", 3);
						p.$w.tabs("disable", 4);
					}
					
					
					
					
					
					
					
					
					
					
					if(p.enti.roles.trabajador.bonos!=null){
						for(var i=0,j=p.enti.roles.trabajador.bonos.length; i<j; i++){
							var $row = p.$w.find('[id=tabs-6] div:eq(0)').clone(),
							result = p.enti.roles.trabajador.bonos[i];
							$row.find('legend').html(result.cod);
							$row.find('span').html(result.formula);
							p.$w.find('[id=tabs-6] div:eq(1)').append($row.children());
						}
					}else p.$w.tabs("disable", 5);
					
					
					
					
					
					
					
					
					
					
					
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetailsLega: function(p){
		new K.Window({
			id: 'windowDetailsLega'+p.id,
			title: 'Legajo de '+p.nomb,
			contentURL: 'pe/trab/details_lega',
			icon: 'ui-icon-gear',
			width: 650,
			height: 305,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsLega'+p.id);
				K.block({$element: p.$w});
				$.post('pe/trab/get_ficha','id='+p.id,function(data){
					if(data.vacaciones==null&&data.licencias==null&&data.meritos==null&&data.demeritos==null&&data.licencias==null&&data.declaraciones==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: 'Informaci&oacute;n inv&aacute;lida',text: 'El trabajador no posee un legajo!',type: 'info'});
					}
					p.$w.tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
					p.$w.find( "li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
					p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4,#tabs-5,#tabs-6,fieldset').css('padding','0px');
					p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4,#tabs-5,#tabs-6').css('height','300px').css('overflow','auto');
					p.$w.find('[name=fecha]').datepicker();
					if(data!=null){
						var toto = 0;
						if(data.vacaciones!=null){
							for(var i=0,j=data.vacaciones.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.vacaciones[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								p.$w.find('#tabs-1 [name=cont]').append($row.children());
							}
							if(data.vacaciones.length==0){
								p.$w.tabs("disable", 0);
							}else toto++;
						}else p.$w.tabs("disable", 0);
						if(data.licencias!=null){
							for(var i=0,j=data.licencias.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.licencias[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								p.$w.find('#tabs-2 [name=cont]').append($row.children());
							}
							if(data.licencias.length==0){
								p.$w.tabs("disable", 1);
							}else toto++;
						}else p.$w.tabs("disable", 1);
						if(data.meritos!=null){
							for(var i=0,j=data.meritos.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.meritos[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								p.$w.find('#tabs-3 [name=cont]').append($row.children());
							}
							if(data.meritos.length==0){
								p.$w.tabs("disable", 2);
							}else toto++;
						}else p.$w.tabs("disable", 2);
						if(data.demeritos!=null){
							for(var i=0,j=data.demeritos.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.demeritos[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								p.$w.find('#tabs-4 [name=cont]').append($row.children());
							}
							if(data.demeritos.length==0){
								p.$w.tabs("disable", 3);
							}else toto++;
						}else p.$w.tabs("disable", 3);
						if(data.comisiones!=null){
							for(var i=0,j=data.comisiones.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.comisiones[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								p.$w.find('#tabs-5 [name=cont]').append($row.children());
							}
							if(data.comisiones.length==0){
								p.$w.tabs("disable", 4);
							}else toto++;
						}else p.$w.tabs("disable", 4);
						if(data.declaraciones!=null){
							for(var i=0,j=data.declaraciones.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.declaraciones[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								p.$w.find('#tabs-6 [name=cont]').append($row.children());
							}
							if(data.declaraciones.length==0){
								p.$w.tabs("disable", 5);
							}else toto++;
						}else p.$w.tabs("disable", 5);
						if(toto<=0){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({title: 'Informaci&oacute;n inv&aacute;lida',text: 'El trabajador no posee un legajo!',type: 'info'});
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowLeg: function(p){
		$.extend(p,{
			del: {
				vac: [],
				lic: [],
				mer: [],
				dem: [],
				com: [],
				dec: []
			}
		});
		new K.Modal({
			id: 'windowRegLega'+p.id,
			title: 'Actualizar Legajo',
			contentURL: 'pe/trab/lega',
			icon: 'ui-icon-gear',
			width: 650,
			height: 325,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						enti: p.enti
					};
					if(p.ficha!=null) data._id = p.ficha;
					for(var i=0,j=p.$w.find('#tabs-1 [name=edit]').length; i<j; i++){
						if(data.vac==null) data.vac = [];
						var $row = p.$w.find('#tabs-1 [name=edit]').eq(i);
						data.vac.push({
							fec: $row.find('legend').html(),
							descr: $row.find('[name=descr]').html()
						});
					}
					for(var i=0,j=p.$w.find('#tabs-2 [name=edit]').length; i<j; i++){
						if(data.lic==null) data.lic = [];
						var $row = p.$w.find('#tabs-2 [name=edit]').eq(i);
						data.lic.push({
							fec: $row.find('legend').html(),
							descr: $row.find('[name=descr]').html()
						});
					}
					for(var i=0,j=p.$w.find('#tabs-3 [name=edit]').length; i<j; i++){
						if(data.mer==null) data.mer = [];
						var $row = p.$w.find('#tabs-3 [name=edit]').eq(i);
						data.mer.push({
							fec: $row.find('legend').html(),
							descr: $row.find('[name=descr]').html()
						});
					}
					for(var i=0,j=p.$w.find('#tabs-4 [name=edit]').length; i<j; i++){
						if(data.dem==null) data.dem = [];
						var $row = p.$w.find('#tabs-4 [name=edit]').eq(i);
						data.dem.push({
							fec: $row.find('legend').html(),
							descr: $row.find('[name=descr]').html()
						});
					}
					for(var i=0,j=p.$w.find('#tabs-5 [name=edit]').length; i<j; i++){
						if(data.com==null) data.com = [];
						var $row = p.$w.find('#tabs-5 [name=edit]').eq(i);
						data.com.push({
							fec: $row.find('legend').html(),
							descr: $row.find('[name=descr]').html()
						});
					}
					for(var i=0,j=p.$w.find('#tabs-6 [name=edit]').length; i<j; i++){
						if(data.dec==null) data.dec = [];
						var $row = p.$w.find('#tabs-6 [name=edit]').eq(i);
						data.dec.push({
							fec: $row.find('legend').html(),
							descr: $row.find('[name=descr]').html()
						});
					}
					data.delet = p.del;
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/save_lega',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Legajo fue actualizado con &eacute;xito!'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowRegLega'+p.id);
				p.$w.find('[name=init] table').after('<button name="btnAgr">Agregar</button>');
				for(var i=0; i<6; i++){
					var $row = p.$w.find('[name=init]').clone();
					p.$w.find('[id^=tabs]').eq(i).append($row.children());
				}
				p.$w.find('#tabs-1 [name=btnAgr]').click(function(){
					if(p.$w.find('#tabs-1 [name=fecha]:first').val()==''){
						p.$w.find('#tabs-1 [name=fecha]:first').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para las vacaciones!',type: 'error'});
					}else if(p.$w.find('#tabs-1 [name=descr]:first').val()==''){
						p.$w.find('#tabs-1 [name=descr]:first').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para las vacaciones!',type: 'error'});
					}
					var $row = p.$w.find('[name=clone]').clone();
					$row.find('fieldset').attr('name','edit');
					$row.find('legend').html(p.$w.find('#tabs-1 [name=fecha]:first').val());
					$row.find('[name=descr]').html(p.$w.find('#tabs-1 [name=descr]:first').val());
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('fieldset').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					p.$w.find('#tabs-1 [name=cont]').append($row.children());
					p.$w.find('#tabs-1 [name=fecha]:first').val('');
					p.$w.find('#tabs-1 [name=descr]:first').val('');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('#tabs-2 [name=btnAgr]').click(function(){
					if(p.$w.find('#tabs-2 [name=fecha]:first').val()==''){
						p.$w.find('#tabs-2 [name=fecha]:first').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para las licencias!',type: 'error'});
					}else if(p.$w.find('#tabs-2 [name=descr]:first').val()==''){
						p.$w.find('#tabs-2 [name=descr]:first').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para las licencias!',type: 'error'});
					}
					var $row = p.$w.find('[name=clone]').clone();
					$row.find('fieldset').attr('name','edit');
					$row.find('legend').html(p.$w.find('#tabs-2 [name=fecha]:first').val());
					$row.find('[name=descr]').html(p.$w.find('#tabs-2 [name=descr]:first').val());
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('fieldset').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					p.$w.find('#tabs-2 [name=cont]').append($row.children());
					p.$w.find('#tabs-2 [name=fecha]:first').val('');
					p.$w.find('#tabs-2 [name=descr]:first').val('');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('#tabs-3 [name=btnAgr]').click(function(){
					if(p.$w.find('#tabs-3 [name=fecha]:first').val()==''){
						p.$w.find('#tabs-3 [name=fecha]:first').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para los m&eacute;ritos!',type: 'error'});
					}else if(p.$w.find('#tabs-3 [name=descr]:first').val()==''){
						p.$w.find('#tabs-3 [name=descr]:first').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para los m&eacute;ritos!',type: 'error'});
					}
					var $row = p.$w.find('[name=clone]').clone();
					$row.find('fieldset').attr('name','edit');
					$row.find('legend').html(p.$w.find('#tabs-3 [name=fecha]:first').val());
					$row.find('[name=descr]').html(p.$w.find('#tabs-3 [name=descr]:first').val());
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('fieldset').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					p.$w.find('#tabs-3 [name=cont]').append($row.children());
					p.$w.find('#tabs-3 [name=fecha]:first').val('');
					p.$w.find('#tabs-3 [name=descr]:first').val('');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('#tabs-4 [name=btnAgr]').click(function(){
					if(p.$w.find('#tabs-4 [name=fecha]:first').val()==''){
						p.$w.find('#tabs-4 [name=fecha]:first').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para los dem&eacute;ritos!',type: 'error'});
					}else if(p.$w.find('#tabs-4 [name=descr]:first').val()==''){
						p.$w.find('#tabs-4 [name=descr]:first').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para los dem&eacute;ritos!',type: 'error'});
					}
					var $row = p.$w.find('[name=clone]').clone();
					$row.find('fieldset').attr('name','edit');
					$row.find('legend').html(p.$w.find('#tabs-4 [name=fecha]:first').val());
					$row.find('[name=descr]').html(p.$w.find('#tabs-4 [name=descr]:first').val());
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('fieldset').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					p.$w.find('#tabs-4 [name=cont]').append($row.children());
					p.$w.find('#tabs-4 [name=fecha]:first').val('');
					p.$w.find('#tabs-4 [name=descr]:first').val('');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('#tabs-5 [name=btnAgr]').click(function(){
					if(p.$w.find('#tabs-5 [name=fecha]:first').val()==''){
						p.$w.find('#tabs-5 [name=fecha]:first').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para las comisiones!',type: 'error'});
					}else if(p.$w.find('#tabs-5 [name=descr]:first').val()==''){
						p.$w.find('#tabs-5 [name=descr]:first').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para las comisiones!',type: 'error'});
					}
					var $row = p.$w.find('[name=clone]').clone();
					$row.find('fieldset').attr('name','edit');
					$row.find('legend').html(p.$w.find('#tabs-5 [name=fecha]:first').val());
					$row.find('[name=descr]').html(p.$w.find('#tabs-5 [name=descr]:first').val());
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('fieldset').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					p.$w.find('#tabs-5 [name=cont]').append($row.children());
					p.$w.find('#tabs-5 [name=fecha]:first').val('');
					p.$w.find('#tabs-5 [name=descr]:first').val('');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('#tabs-6 [name=btnAgr]').click(function(){
					if(p.$w.find('#tabs-6 [name=fecha]:first').val()==''){
						p.$w.find('#tabs-6 [name=fecha]:first').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para las declaraciones!',type: 'error'});
					}else if(p.$w.find('#tabs-6 [name=descr]:first').val()==''){
						p.$w.find('#tabs-6 [name=descr]:first').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para las declaraciones!',type: 'error'});
					}
					var $row = p.$w.find('[name=clone]').clone();
					$row.find('fieldset').attr('name','edit');
					$row.find('legend').html(p.$w.find('#tabs-6 [name=fecha]:first').val());
					$row.find('[name=descr]').html(p.$w.find('#tabs-6 [name=descr]:first').val());
					$row.find('[name=btnEli]').click(function(){
						$(this).closest('fieldset').remove();
					}).button({icons: {primary: 'ui-icon-trash'},text: false});
					p.$w.find('#tabs-6 [name=cont]').append($row.children());
					p.$w.find('#tabs-6 [name=fecha]:first').val('');
					p.$w.find('#tabs-6 [name=descr]:first').val('');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				K.block({$element: p.$w});
				$.post('pe/trab/get_ficha','id='+p.id,function(data){
					if(data!=null) p.ficha = data._id.$id;
					p.$w.tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
					p.$w.find( "li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
					p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4,#tabs-5,#tabs-6,fieldset').css('padding','0px');
					p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4,#tabs-5,#tabs-6').css('height','320px').css('overflow','auto');
					p.$w.find('[name=fecha]').datepicker();
					if(data!=null){
						if(data.vacaciones!=null){
							for(var i=0,j=data.vacaciones.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.vacaciones[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								$row.find('[name=btnEli]').click(function(){
									p.del.vac.push($(this).data('index'));
									$(this).closest('fieldset').remove();
								}).button({icons: {primary: 'ui-icon-trash'},text: false})
								.data('index',i);
								p.$w.find('#tabs-1 [name=cont]').append($row.children());
							}
						}
						if(data.licencias!=null){
							for(var i=0,j=data.licencias.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.licencias[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								$row.find('[name=btnEli]').click(function(){
									p.del.lic.push($(this).data('index'));
									$(this).closest('fieldset').remove();
								}).button({icons: {primary: 'ui-icon-trash'},text: false})
								.data('index',i);
								p.$w.find('#tabs-2 [name=cont]').append($row.children());
							}
						}
						if(data.meritos!=null){
							for(var i=0,j=data.meritos.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.meritos[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								$row.find('[name=btnEli]').click(function(){
									p.del.mer.push($(this).data('index'));
									$(this).closest('fieldset').remove();
								}).button({icons: {primary: 'ui-icon-trash'},text: false})
								.data('index',i);
								p.$w.find('#tabs-3 [name=cont]').append($row.children());
							}
						}
						if(data.demeritos!=null){
							for(var i=0,j=data.demeritos.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.demeritos[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								$row.find('[name=btnEli]').click(function(){
									p.del.dem.push($(this).data('index'));
									$(this).closest('fieldset').remove();
								}).button({icons: {primary: 'ui-icon-trash'},text: false})
								.data('index',i);
								p.$w.find('#tabs-4 [name=cont]').append($row.children());
							}
						}
						if(data.comisiones!=null){
							for(var i=0,j=data.comisiones.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.comisiones[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								$row.find('[name=btnEli]').click(function(){
									p.del.com.push($(this).data('index'));
									$(this).closest('fieldset').remove();
								}).button({icons: {primary: 'ui-icon-trash'},text: false})
								.data('index',i);
								p.$w.find('#tabs-5 [name=cont]').append($row.children());
							}
						}
						if(data.declaraciones!=null){
							for(var i=0,j=data.declaraciones.length; i<j; i++){
								var $row = p.$w.find('[name=clone]').clone();
								var result = data.declaraciones[i];
								$row.find('legend').html(ciHelper.dateFormatOnlyDay(result.fec));
								$row.find('[name=descr]').html(result.descr);
								$row.find('[name=btnEli]').click(function(){
									p.del.dec.push($(this).data('index'));
									$(this).closest('fieldset').remove();
								}).button({icons: {primary: 'ui-icon-trash'},text: false})
								.data('index',i);
								p.$w.find('#tabs-6 [name=cont]').append($row.children());
							}
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDes: function(p){
		new K.Modal({
			id: 'windowTrabDes'+p.id,
			title: 'Deshabilitar: '+p.nomb,
			contentURL: 'pe/trab/des',
			icon: 'ui-icon-circle-close',
			width: 350,
			height: 100,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: K.tmp.data('id'),
						estado: 'D',
						cese: {
							motivo: p.$w.find('[name=motivo] option:selected').val(),
							observ: p.$w.find('[name=observ]').val()
						}
					};
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/upd',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Trabajador Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowTrabDes'+p.id);
			}
		});
	},
	windowBon: function(p){
		new K.Modal({
			id: 'windowTrabBono'+p.id,
			title: 'Agregar Bono para '+p.nomb,
			contentURL: 'pe/trab/bono',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 200,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						tipo: p.$w.find('[name=tipo] :selected').val(),
						cod: p.$w.find('[name=cod]').val(),
						cod_sunat: p.$w.find('[name=cod_sunat]').val(),
						descr: p.$w.find('[name=descr]').val(),
						formula: p.$w.find('[name=form]').data('data')
					};
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/save_bono',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Bono fue registrado con &eacute;xito!'});
						$('#mainPanel [name=btnBuscar]').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowTrabBono'+p.id);
				p.$w.find('[name=btnForm]').click(function(){
					peConc.windowForm({
						tipo: p.tipo,
						val: p.$w.find('[name=form]').data('data'),
						callback: function(data){
							p.$w.find('[name=form]').html(data).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-icon-calculator'}});
			}
		});
	},
	windowDetailsBon: function(p){
		new K.Modal({
			id: 'windowDetailsTrabBono'+p.id,
			title: 'Bonos de '+p.nomb,
			contentURL: 'pe/trab/details_bono',
			icon: 'ui-icon-gear',
			width: 400,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id
					};
					data.bonos = new Array;
					if(p.$w.find('[name=gridBody] .item').length>0){
						for(i=0;i<p.$w.find('[name=gridBody] .item').length;i++){
							var item = {
								tipo:p.$w.find('[name=gridBody] .item').eq(i).find('[name=tipo] :selected').val(),
								cod:p.$w.find('[name=gridBody] .item').eq(i).find('[name=codigo]').val(),
								cod_sunat:p.$w.find('[name=gridBody] .item').eq(i).find('[name=codigo_sunat]').val(),
								descr:p.$w.find('[name=gridBody] .item').eq(i).find('[name=descr]').val(),
								formula:p.$w.find('[name=gridBody] .item').eq(i).find('[name=formula]').val()
							};
							data.bonos.push(item);
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					console.log(data);
					$.post('pe/trab/update_bono',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Los bonos fueron modificados con &eacute;xito!'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ 
				p.$w.find('[name=btnEli]').die('click');
				p = null; 
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsTrabBono'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('fieldset').remove();
				});
				$.post('pe/trab/get','id='+p.id,function(data){
					if(data.roles.trabajador.bonos==null){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'El trabajador no tiene bonos asignados',type: 'info'});
						K.closeWindow(p.$w.attr('id'));
					}else{
						for(var i=0,j=data.roles.trabajador.bonos.length; i<j; i++){
							var $row = p.$w.find('div:eq(0)').clone(),
							result = data.roles.trabajador.bonos[i];
							$row.find('legend').html(result.cod+' <button name="btnEli">Eliminar</button>');
							$row.find('[name=tipo]').find('[value='+result.tipo+']').attr('selected','selected');
							$row.find('[name=codigo]').val(result.cod);
							$row.find('[name=codigo_sunat]').val(result.cod_sunat);
							$row.find('[name=formula]').val(result.formula);
							$row.find('[name=descr]').val(result.descr);
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'}});
							p.$w.find('div:eq(1)').append($row.children());
						}
						K.unblock({$element: p.$w});
					}
				},'json');
			}
		});
	},
	windowDetailsHisto: function(p){
		new K.Window({
			id: 'windowDetailsHistoTrab'+p.id,
			title: 'Hist&oacute;rico de '+p.nomb,
			contentURL: 'pe/trab/histo',
			icon: 'ui-icon-calendar',
			width: 620,
			height: 410,
			buttons: {
				'Cerrar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsHistoTrab'+p.id);
				K.block({$element: p.$w});
				$.post('pe/trab/get','id='+p.id,function(data){
					var $table = p.$w.find('[name=clone]').clone();
					if(data.imagen!=null) $table.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
					else $table.find('[name=foto]').removeAttr('src');
					$table.find('[name=nomb]').html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
					$table.find('[name=docident]').html( data.docident[0].num );
					if(data.domicilios!=null) $table.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
					else $table.find('[name=direc]').html('--');
					if(data.telefonos!=null) $table.find('[name=telf]').html( data.telefonos[0].num );
					else $table.find('[name=telf]').html('--');
					$table.find('[name=cargo]').html(data.roles.trabajador.cargo.nomb);
					$table.find('[name=orga]').html(data.roles.trabajador.organizacion.nomb);
					if(data.roles.trabajador.nivel!=null){
						$table.find('[name=nivel]').html(data.roles.trabajador.nivel.nomb);
					}else $table.find('[name=nivel]').closest('fieldset').hide();
					if(data.roles.trabajador.nivel_carrera!=null) $table.find('[name=nivel2]').html(data.roles.trabajador.nivel_carrera.nomb);
					else $table.find('[name=nivel2]').closest('tr').hide();
					$table.find('[name=descr]').html(data.roles.trabajador.local.descr);
					$table.find('[name=direccion]').html(data.roles.trabajador.local.direccion);
					if(data.roles.trabajador.turno!=null) $table.find('[name=turno]').html(data.roles.trabajador.turno.nomb);
					if(data.roles.trabajador.cargo_clasif!=null){
						$table.find('[name=clas]').html(data.roles.trabajador.cargo_clasif.nomb);
						$table.find('[name=codclas]').html(data.roles.trabajador.cargo_clasif.cod);
					}else $table.find('[name=clas]').closest('fieldset').hide();
					if(data.roles.trabajador.grupo_ocup!=null){
						$table.find('[name=grup]').html(data.roles.trabajador.grupo_ocup.nomb);
						$table.find('[name=codgrup]').html(data.roles.trabajador.grupo_ocup.sigla);
					}else $table.find('[name=codgrup]').closest('fieldset').hide();
					$table.find('[name=tipo]').html(data.roles.trabajador.contrato.nomb);
					$table.find('[name=tarjeta]').html(data.roles.trabajador.cod_tarjeta);
					$table.find('[name=essalud]').html(data.roles.trabajador.essalud);
					if(data.roles.trabajador.tipo!=null) $table.find('[name=tipotrab]').html(peEntiTrab.tipo[data.roles.trabajador.tipo]);
					if(data.roles.trabajador.eps!=null) $table.find('[name=afilia]').html((data.roles.trabajador.eps==true)?'Afiliado a EPS':'No afiliado');
					p.$w.find('[id^=tabs-]').eq(0).find('[name=cont]').append($table.children());
					if(data.roles.trabajador.historico!=null){
						for(var i=0,j=data.roles.trabajador.historico.length; i<j; i++){
							var index = j-i-1,
							result = data.roles.trabajador.historico[index];
							p.$w.find('ul').append('<li><a href="#tabs-'+(i+2)+'">'+((result.fec!=null)?ciHelper.dateFormat(result.fec):ciHelper.dateFormatNow())+'</a></li>');
							p.$w.append('<div id="tabs-'+(i+2)+'"><div style="height: 390px;width: 450px;overflow: auto;" name="cont"></div></div>');
							var $table = p.$w.find('[name=clone]').clone();
							if(data.imagen!=null) $table.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
							else $table.find('[name=foto]').removeAttr('src');
							$table.find('[name=nomb]').html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
							$table.find('[name=docident]').html( data.docident[0].num );
							if(data.domicilios!=null) $table.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
							else $table.find('[name=direc]').html('--');
							if(data.telefonos!=null) $table.find('[name=telf]').html( data.telefonos[0].num );
							else $table.find('[name=telf]').html('--');
							$table.find('[name=cargo]').html(result.cargo.nomb);
							$table.find('[name=orga]').html(result.organizacion.nomb);
							if(result.nivel!=null){
								$table.find('[name=nivel]').html(result.nivel.nomb);
							}else $table.find('[name=nivel]').closest('fieldset').hide();
							if(result.nivel_carrera!=null) $table.find('[name=nivel2]').html(result.nivel_carrera.nomb);
							else $table.find('[name=nivel2]').closest('tr').hide();
							$table.find('[name=descr]').html(result.local.descr);
							$table.find('[name=direccion]').html(result.local.direccion);
							if(result.turno!=null) $table.find('[name=turno]').html(result.turno.nomb);
							if(result.cargo_clasif!=null){
								$table.find('[name=clas]').html(result.cargo_clasif.nomb);
								$table.find('[name=codclas]').html(result.cargo_clasif.cod);
							}else $table.find('[name=clas]').closest('fieldset').hide();
							if(result.grupo_ocup!=null){
								$table.find('[name=grup]').html(result.grupo_ocup.nomb);
								$table.find('[name=codgrup]').html(result.grupo_ocup.sigla);
							}else $table.find('[name=codgrup]').closest('fieldset').hide();
							$table.find('[name=tipo]').html(result.contrato.nomb);
							$table.find('[name=tarjeta]').html(result.cod_tarjeta);
							$table.find('[name=essalud]').html(result.essalud);
							if(result.tipo!=null) $table.find('[name=tipotrab]').html(peEntiTrab.tipo[result.tipo]);
							if(result.eps!=null) $table.find('[name=afilia]').html((result.eps==true)?'Afiliado a EPS':'No afiliado');
							p.$w.find('[id^=tabs-]').eq(i+1).find('[name=cont]').append($table.children());
						}
					}
					p.$w.tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
					p.$w.find( "li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
					p.$w.find('[id^=tabs-],fieldset').css('padding','0px');
					p.$w.find('[id^=tabs-]').css('height','400px').css('overflow','auto');
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowRetencion: function(p){
		$.extend(p,{
			enti: function(data){
				p.$row.find('[name=nomb]').html(ciHelper.enti.formatName(data));
				p.$row.data('data',ciHelper.enti.dbRel(data));
			}
		});
		new K.Window({
			id: 'windowRetencion'+p.id,
			title: 'Retenci&oacute;n Jur&iacute;dica de '+p.nomb,
			contentURL: 'pe/trab/reten',
			store: false,
			icon: 'ui-icon-refresh',
			width: 520,
			height: 180,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						retencion: []
					};
					if(p.$w.find('.item').length!=0){
						for(var i=0,j=p.$w.find('.item').length; i<j; i++){
							var $row = p.$w.find('.item').eq(i),
							tmp = {
								entidad: $row.data('data'),
								val: $row.find('[name=val]').val()
							};
							if(tmp.entidad==null){
								$row.find('[name=btnSel]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una entidad!',
									type: 'error'
								});
							}
							if(tmp.val==''){
								$row.find('[name=val]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar el valor del porcentaje!',
									type: 'error'
								});
							}
							data.retencion.push(tmp);
						}
					}else{
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar al menos una entidad!',
							type: 'error'
						});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/trab/save_retencion',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El trabajador fue actualizado con &eacute;xito!'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnEliminar]').die('click');
				p.$w.find('[name=btnAgregar]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowRetencion'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnAgregar]').live('click',function(){
					p.$w.find('[name=btnAgregar]').remove();
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html('<span name="nomb"></span>&nbsp;<button name="btnAgr">Agregar</button><button name="btnSel">Seleccionar</button>');
					$row.find('li:eq(1)').html('<input type="text" name="val" size="7">');
					$row.find('li:eq(2)').html('<button name="btnEliminar">Eliminar</button>'+
						'<button name="btnAgregar">Agregar</button>');
					$row.find('[name=val]').numeric().spinner({min: 0,max: 99.9,step: 0.1});
					$row.find('.ui-button').height('14px');
					$row.find('[name=btnAgr]').click(function(){
						p.$row = $(this).closest('.item');
						ciCreate.windowNewEntidad({$window: p.$w,callBack: p.enti});
					}).button({icons: {primary: 'ui-icon-plusthick'},text: false});
					$row.find('[name=btnSel]').click(function(){
						p.$row = $(this).closest('.item');
						ciSearch.windowSearchEnti({$window: p.$w,callback: p.enti});
					}).button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnAgregar]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					$row.find('[name=btnEliminar]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item">');
					p.$w.find('.gridBody').append($row.children());
				});
				p.$w.find('[name=btnEliminar]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('[name=btnAgregar]').length==0){
						p.$w.find('.item:last').find('li:eq(3)').append('<button name="btnAgregar">Agregar</button>');
						p.$w.find('[name=btnAgregar]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					}
					if(p.$w.find('.item').length==0){
						p.$w.append('<button name="btnAgregar"></button>');
						p.$w.find('[name=btnAgregar]').click();
					}
				});
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				$.post('pe/trab/get',{id: p.id},function(data){
					if(data.roles.trabajador.retencion!=null){
						for(var i=0,j=data.roles.trabajador.retencion.length; i<j; i++){
							p.$w.find('[name=btnAgregar]').remove();
							var $row = p.$w.find('.gridReference').clone()
							item = data.roles.trabajador.retencion[i];
							$row.find('li:eq(0)').html('<span name="nomb">'+ciHelper.enti.formatName(item.entidad)+'</span>&nbsp;<button name="btnAgr">Agregar</button><button name="btnSel">Seleccionar</button>');
							$row.find('li:eq(1)').html('<input type="text" name="val" size="7" value="'+item.val+'">');
							$row.find('li:eq(2)').html('<button name="btnEliminar">Eliminar</button>'+
								'<button name="btnAgregar">Agregar</button>');
							$row.find('[name=val]').numeric().spinner({min: 0,max: 99.9,step: 0.1});
							$row.find('.ui-button').height('14px');
							$row.find('[name=btnAgr]').click(function(){
								p.$row = $(this).closest('.item');
								ciCreate.windowNewEntidad({$window: p.$w,callBack: p.enti});
							}).button({icons: {primary: 'ui-icon-plusthick'},text: false});
							$row.find('[name=btnSel]').click(function(){
								p.$row = $(this).closest('.item');
								ciSearch.windowSearchEnti({$window: p.$w,callback: p.enti});
							}).button({icons: {primary: 'ui-icon-search'},text: false});
							$row.find('[name=btnAgregar]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							$row.find('[name=btnEliminar]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item">');
							p.$w.find('.gridBody').append($row.children());
						}
					}else{
						p.$w.append('<button name="btnAgregar"></button>');
						p.$w.find('[name=btnAgregar]').click();
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	cambiarTipo: function(p){
		peCont.windowSelect({
			callback: function(contrato){
				if(p.contrato==contrato._id.$id){
					return K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe escoger un tipo de contrato distinto al que ya tiene el trabajador!',
						type: 'error'
					});
				}else if(contrato.campos==null){
					return K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe escoger un tipo de contrato con campos ya definidos!',
						type: 'error'
					});
				}else{
					p.tipo = contrato._id.$id;
					peEntiTrab.windowEdit(p);
				}
			}
		});
	}
};
require(['pe/nive','pe/carg','pe/grup','pe/clas','pe/equi','pe/conc'],function(peNive,peCarg,peGrup,peClas,peEqui,peConc){
	$.noop();
});