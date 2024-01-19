/*******************************************************************************
boletas de beneficios sociales y vacaciones truncas */
pePlanVac = {
	states: {
		R: {
			descr: "Registrada",
			color: "#CCCCCC"
		},
		P: {
			descr: "Pagada",
			color: "#006532"
		},
		A: {
			descr: "Anulada",
			color: "#FF0000"
		}
	},
	init: function(){
		var tipo = $.cookie('tipo_contrato');
		if($('#pageWrapper [child=plan]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pe/navg/plan',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="plan" />');
					$p.find("[name=pePlan]").after( $row.children() );
				}
				$p.find('[name=pePlan]').data('plan',$('#pageWrapper [child=plan]:first').data('plan'));
				$p.find('[name=pePlanEnf]').click(function(){ pePlanEnf.init(); });
				$p.find('[name=pePlanTre]').click(function(){ pePlanTre.init(); });
				$p.find('[name=pePlanVei]').click(function(){ pePlanVei.init(); });
				$p.find('[name=pePlanFal]').click(function(){ pePlanFal.init(); });
				$p.find('[name=pePlanBon]').click(function(){ pePlanBon.init(); });
				$p.find('[name=pePlanQui]').click(function(){ pePlanQui.init(); });
				$p.find('[name=pePlanMat]').click(function(){ pePlanMat.init(); });
				$p.find('[name=pePlanVac]').click(function(){ pePlanVac.init(); }).addClass('ui-state-highlight');
				$p.find('[name=pePlanSep]').click(function(){ pePlanSep.init(); });
				$p.find('[name^=pePlanBole]').click(function(){
					$.cookie('tipo_contrato',$(this).attr('name').substring(10));
					pePlanBole.init();
				});
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'pePlanVac',
			titleBar: {
				title: 'Beneficios Sociales - Vacaciones Truncas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/bole',
			onContentLoaded: function(){
				$('#pageWrapperLeft [name=pePlanVac]').addClass('ui-state-highlight');
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('label:first').remove();
				$mainPanel.find('br:first').remove();
				$mainPanel.find('[name=periodo],[name=btnPagar],[name=btnPlanilla],[name=btnPlanillaImp]').remove();
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo de boleta' ).width('250');
				$mainPanel.find('[name=obj]').html( 'boleta(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnConso]').remove();
				$mainPanel.find('[name=btnPagar]').click(function(){
					pePlanVac.windowPagar();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					pePlanVac.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnPlanilla]').click(function(){
					if($mainPanel.find('.gridBody .item').length>0){
						window.open("pe/bole/export?"+$.param({
							periodo: $mainPanel.find('[name=periodo]').data('ano'),
							mes: +$mainPanel.find('[name=periodo]').data('mes')+1
						}));
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontraron boletas para generar la planilla!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnPlanillaImp]').click(function(){
					if($mainPanel.find('.gridBody .item').length>0){
						var url = "pe/bole/print?"+$.param({
							periodo: $mainPanel.find('[name=periodo]').data('ano'),
							mes: +$mainPanel.find('[name=periodo]').data('mes')+1
						});
						K.windowPrint({
							id:"windowPrintPePlan",
							title:"Planilla",
							url:url
						});
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontraron boletas para generar la planilla!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						pePlanVac.loadData({page: 1,url: 'pe/docs/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						pePlanVac.loadData({page: 1,url: 'pe/docs/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				pePlanVac.loadData({page: 1,url: 'pe/docs/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		K.sendingInfo();
		if(params==null){
			params = {url: 'pe/docs/lista'};
			$mainPanel.find('.gridBody').empty();
		}
		$.extend(params,{
			doc: 'vacaciones',
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
			page: (params.page) ? params.page : 1
		});
	    $.post(params.url, params, function(data){
			K.clearNoti();
			K.notification({text: 'Boletas recibidas!',type: 'info',icon: 'ui-icon-folder-open'});
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',pePlanVac.states[result.estado].color).addClass('vtip').attr('title',pePlanVac.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( 'N&deg;'+result.cod );
					$li.eq(3).html( ciHelper.enti.formatName(result.trabajador) );
					if(result.trabajador.roles.trabajador.cargo._id!=null)
						$li.eq(4).html( result.trabajador.roles.trabajador.cargo.nomb );
					else
						$li.eq(4).html( result.trabajador.roles.trabajador.cargo.funcion );
					$li.eq(5).html( ciHelper.formatMon(result.vacaciones.total_pagar) );
					$li.eq(6).html( ciHelper.formatMon(result.vacaciones.total_descuentos) );
					$li.eq(7).html( ciHelper.formatMon(result.neto) );
					$li.eq(8).html( ciHelper.formatMon(result.vacaciones.total_patronal) );
					$li.eq(9).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						pePlanVac.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).data('estado',result.estado).contextMenu("conMenPeSubs", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenPeSubs_ver': function(t) {
								pePlanVac.windowDetails({id: K.tmp.data('id')});
							},
							'conMenPeSubs_imp': function(t) {
								K.windowPrint({
									id:'windowPeBolePrint',
									title: "Boleta",
									url: "pe/docs/vac_print?id="+K.tmp.data('id')
								});
							},
							'conMenPeSubs_eli': function(t) {
								ciHelper.confirm('&iquest;Est&aacute; seguro(a) de eliminar este Subsidio&#63;',function () {
									K.sendingInfo();
									$.post('pe/docs/delete',{_id: K.tmp.data('id')},function(){
										K.clearNoti();
										K.notification({title: 'Subsidio Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},function () {
									$.noop();
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
						pePlanVac.loadData(params);
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
		if(p==null) p = {};
		$.extend(p,{
			calc: function(){
				var remuneracion_principal = 0;
				if(p.enti!=null){
					if(p.enti.roles.trabajador.nivel_carrera!=null){
						remuneracion_principal = parseFloat(p.enti.roles.trabajador.nivel_carrera.basica) + parseFloat(p.enti.roles.trabajador.nivel_carrera.reunificada);
					}
					if(p.enti.roles.trabajador.nivel!=null){
						remuneracion_principal = parseFloat(p.enti.roles.trabajador.nivel.basica) + parseFloat(p.enti.roles.trabajador.nivel.reunificada);
					}
					var total_anos_bene = p.$w.find('[name=total_anos_bene]').val();
					if(total_anos_bene=='') total_anos_bene = 0;
					else total_anos_bene = parseInt(total_anos_bene);
					var beneficios_sociales = K.round(remuneracion_principal/2*total_anos_bene,2);
					p.$w.find('[name=bene_remu]').html(remuneracion_principal)
						.data('data',remuneracion_principal);
					p.$w.find('[name=bene_total]').html(K.round(remuneracion_principal/2*total_anos_bene,2))
						.data('data',beneficios_sociales);
				}
				var mes = parseInt(p.$w.find('[name=meses]').val()),
				dia = parseInt(p.$w.find('[name=dias]').val()),
				remuneracion = parseFloat(p.$w.find('[name=vaca_remu]').data('data')),
				total_mes = parseFloat(K.round(remuneracion/12*mes,2)),
				total_dia = parseFloat(K.round(remuneracion/360*dia,2)),
				total_pre = parseFloat(K.round(total_mes+total_dia,2)),
				total_descuentos = 0,
				total_patronal = 0;
				p.$w.find('[name=vaca_periodo]').html(p.$w.find('[name=periodo]').val());
				p.$w.find('[name=vaca_mes]').html(mes+' Mes(es)');
				p.$w.find('[name=total_mes]').html(K.round(total_mes,2)).data('data',total_mes);
				p.$w.find('[name=vaca_dia]').html(dia+' D&iacute;a(s)');
				p.$w.find('[name=total_dia]').html(K.round(total_dia,2)).data('data',total_dia);
				p.$w.find('[name=total_pre]').html(K.round(total_pre,2)).data('data',total_pre);
				/*
				 * Calculo de descuentos
				 */
				for(var i=0,j=p.$w.find('.gridCont:eq(0) .item').length; i<j; i++){
					var $row = p.$w.find('.gridCont:eq(0) .item').eq(i),
					porc = $row.find('[name=val]').val(),
					porc = (porc!='')?parseFloat(porc):0,
					tot_des = K.round(porc*total_pre/100,2);
					$row.find('li:eq(2)').html(tot_des).data('data',tot_des);
					total_descuentos += parseFloat(tot_des);
				}
				p.$w.find('[name=total_desc]').html(K.round(total_descuentos,2)).data('data',total_descuentos);
				var total_pagar = total_pre - total_descuentos;
				if(p.$w.find('[name=elemento1]').is(':checked')){
					total_pagar += parseFloat(p.$w.find('[name=bene_total]').data('data'));
				}
				p.$w.find('[name=total]').html(K.round(total_pagar,2)).data('data',total_pagar);
				/*
				 * Calculo de patronales
				 */
				for(var i=0,j=p.$w.find('.gridCont:eq(1) .item').length; i<j; i++){
					var $row = p.$w.find('.gridCont:eq(1) .item').eq(i),
					porc = $row.find('[name=val]').val(),
					porc = (porc!='')?parseFloat(porc):0,
					tot_des = K.round(porc*total_pre/100,2);
					$row.find('li:eq(2)').html(tot_des).data('data',tot_des);
					total_patronal += parseFloat(tot_des);
				}
			}
		});
		new K.Window({
			id: 'windowNewVac',
			title: 'Beneficios Sociales y Vacaciones Truncas',
			contentURL: 'pe/docs/vac_ben_edit',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						vacaciones: {
							ultima_remuneracion: parseFloat(p.$w.find('[name=vaca_remu]').data('data')),
							meses: p.$w.find('[name=meses]').val(),
							dias: p.$w.find('[name=dias]').val(),
							periodo: p.$w.find('[name=periodo]').val()
						},
						trabajador: null
					};
					if(p.enti==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
					}else data.trabajador = ciHelper.enti.dbTrabRel(p.enti);
					data.contrato = {
						_id: p.enti.roles.trabajador.contrato._id.$id,
						nomb: p.enti.roles.trabajador.contrato.nomb,
						cod: p.enti.roles.trabajador.contrato.cod
					};
					if(p.$w.find('[name=elemento1]').is(':checked')){
						data.vacaciones.bonificacion = {
							total_anos: p.$w.find('[name=total_anos_bene]').val(),
							remuneracion_principal: parseFloat(p.$w.find('[name=bene_remu]').data('data')),
							pagar: parseFloat(p.$w.find('[name=bene_total]').data('data'))
						};
					}
					if(data.vacaciones.periodo==''){
						p.$w.find('[name=periodo]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar un periodo v&aacute;lido!',
							type: 'error'
						});
					}
					if(data.vacaciones.meses==''){
						p.$w.find('[name=meses]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar un n&uacute;mero v&aacute;lido de meses!',
							type: 'error'
						});
					}else data.vacaciones.meses = parseInt(data.vacaciones.meses);
					if(data.vacaciones.dias==''){
						p.$w.find('[name=dias]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar un n&uacute;mero v&aacute;lido de d&iacute;as!',
							type: 'error'
						});
					}else data.vacaciones.dias = parseInt(data.vacaciones.dias);
					data.vacaciones.total_mes = parseFloat(K.round(data.vacaciones.ultima_remuneracion/12*data.vacaciones.meses,2));
					data.vacaciones.total_dia = parseFloat(K.round(data.vacaciones.ultima_remuneracion/360*data.vacaciones.dias,2));
					data.vacaciones.total = parseFloat(K.round(data.vacaciones.total_mes+data.vacaciones.total_dia,2));
					$.extend(data.vacaciones,{
						descuentos: [],
						aportaciones: [],
						total_descuentos: 0,
						total_patronal: 0
					});
					/*
					 * Calculo de descuentos
					 */
					for(var i=0,j=p.$w.find('.gridCont:eq(0) .item').length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(0) .item').eq(i),
						porc = $row.find('[name=val]').val(),
						porc = (porc!='')?parseFloat(porc):0,
						tot_des = K.round(porc*data.vacaciones.total/100,2);
						if(porc==0){
							$row.find('[name=val]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un porcentaje mayor a 0!',
								type: 'error'
							});
						}
						if($row.find('[name=descr]').val()==''){
							$row.find('[name=descr]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una descripci&oacute;n para la retenci&oacute;n!',
								type: 'error'
							});
						}
						data.vacaciones.descuentos.push({
							descr: $row.find('[name=descr]').val(),
							val: $row.find('[name=val]').val()
						});
						data.vacaciones.total_descuentos += parseFloat(tot_des);
					}
					if(data.vacaciones.descuentos.length==0){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar al menos una retenci&oacute;n!',
							type: 'error'
						});
					}
					/*
					 * Calculo de descuentos
					 */
					for(var i=0,j=p.$w.find('.gridCont:eq(1) .item').length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(1) .item').eq(i),
						porc = $row.find('[name=val]').val(),
						porc = (porc!='')?parseFloat(porc):0,
						tot_des = K.round(porc*data.vacaciones.total/100,2);
						if(porc==0){
							$row.find('[name=val]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un porcentaje mayor a 0!',
								type: 'error'
							});
						}
						if($row.find('[name=descr]').val()==''){
							$row.find('[name=descr]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un una descripci&oacute;n para la retenci&oacute;n!',
								type: 'error'
							});
						}
						data.vacaciones.aportaciones.push({
							descr: $row.find('[name=descr]').val(),
							val: $row.find('[name=val]').val()
						});
						data.vacaciones.total_patronal += parseFloat(tot_des);
					}
					if(data.vacaciones.descuentos.length==0){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar al menos un aporte patronal!',
							type: 'error'
						});
					}
					data.vacaciones.total_pagar = data.vacaciones.total - data.vacaciones.total_descuentos;
					if(p.$w.find('[name=elemento1]').is(':checked')){
						data.vacaciones.total_pagar += parseFloat(p.$w.find('[name=bene_total]').data('data'));
					}
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/docs/save_doc',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La boleta fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnAgr]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewVac');
				K.block({$element: p.$w});
				p.$w.find('[name=elemento1]').change(function(){
					if($(this).is(":checked"))
						p.$w.find('[name=beneficios]').show();
					else
						p.$w.find('[name=beneficios]').hide();
					p.calc();
				}).change();
				p.$w.find('[name=meses],[name=dias]').keyup(function(){
					p.calc();
				}).numeric();
				p.$w.find('[name=periodo]').keyup(function(){
					p.calc();
				});
				p.$w.find('[name=btnAgr]').live('click',function(){
					var $grid = $(this).closest('.gridCont'),
					$row = $grid.find('.gridReference').clone();
					$grid.find('[name=btnAgr]').remove();
					$row.find('li:eq(0)').html('<input type="text" size="45" name="descr" />');
					$row.find('li:eq(1)').html('<input type="text" size="4"  name="val" value="0" />%');
					$row.find('li:eq(2)').html('<span name="tot"></span>');
					$row.find('li:eq(3)').html('<button name="btnEli">Eliminar</button>&nbsp;<button name="btnAgr">Agregar</button>');
					$row.find('[name=val]').keyup(function(){
						p.calc();
					}).numeric();
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					$row.wrapInner('<a class="item">');
					$grid.find('.gridBody').append($row.children());
					p.calc();
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					var $grid = $(this).closest('.gridCont');
					$(this).closest('.item').remove();
					if($grid.find('.item').length==0){
						$grid.append('<button name="btnAgr"></button>');
						$grid.find('[name=btnAgr]').click();
					}else{
						if($grid.find('[name=btnAgr]').length==0){
							$grid.find('.item:last li:eq(3)').append('<button name="btnAgr">Agregar</button>');
							$grid.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
					}
					p.calc();
				});
				/*
				 * Se selecciona el trabajador
				 */
				p.$w.find('[name=total_anos_bene]').numeric().keyup(function(){
					p.calc();
				}).val(0);
				p.$w.find('[name=btnSelEnt]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.enti = data;
						K.block({$element: p.$w});
						if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
						else p.$w.find('[name=foto]').removeAttr('src');
						p.$w.find('[name=nomb]').data('data',data)
						.html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
						p.$w.find('[name=dni]').html( data.docident[0].num );
						if(data.roles.trabajador.cargo._id!=null)
							p.$w.find('[name=cargo]').html( data.roles.trabajador.cargo.nomb );
						else
							p.$w.find('[name=cargo]').html( data.roles.trabajador.cargo.funcion );
						p.$w.find('[name=orga]').html( data.roles.trabajador.organizacion.nomb ).attr('title',data.roles.trabajador.organizacion.nomb).tooltip();
						if(data.roles.trabajador.nivel!=null) p.$w.find('[name=nivel]').html( data.roles.trabajador.nivel.nomb );
						p.$w.find('[name=essalud]').html( data.roles.trabajador.essalud );
						if(data.roles.trabajador.pension!=null){
							p.$w.find('[name=pension]').html( data.roles.trabajador.pension.nomb );
							p.$w.find('[name=cod_aportante]').html( data.roles.trabajador.cod_aportante );
							var porc = data.roles.trabajador.pension.porcentajes;
							if(porc.length==3)
								porc = parseFloat(porc[2].val);
							else
								porc = parseFloat(porc[0].val);
							p.$w.find('[name=desc_total]').data('data',porc);
							p.$w.find('[name=desc]').html( data.roles.trabajador.pension.nomb+' '+porc+'%' );
						}else{
							p.$w.find('[name=pension]').html('--');
							p.$w.find('[name=cod_aportante]').html('--');
						}
						if(data.roles.trabajador.fecing!=null){
							p.$w.find('[name=ini]').html(ciHelper.dateFormatBD(data.roles.trabajador.fecing));
						}
						$.post('mg/orga/get','id='+data.roles.trabajador.organizacion._id.$id,function(data){
							if(data.actividad!=null) p.$w.find('[name=actividad]').html( data.actividad.cod );
							if(data.componente!=null) p.$w.find('[name=componente]').html( data.componente.cod );
						},'json');
						$.post('mg/enti/get',{_id: data._id.$id},function(entidad){
							p.enti = entidad;
							var remuneracion_principal = 0;
							if(entidad.roles.trabajador.nivel_carrera!=null){
								remuneracion_principal = parseFloat(entidad.roles.trabajador.nivel_carrera.basica) + parseFloat(entidad.roles.trabajador.nivel_carrera.reunificada);
							}
							if(entidad.roles.trabajador.nivel!=null){
								remuneracion_principal = parseFloat(entidad.roles.trabajador.nivel.basica) + parseFloat(entidad.roles.trabajador.nivel.reunificada);
							}
							var total_anos_bene = p.$w.find('[name=total_anos_bene]').val();
							if(total_anos_bene=='') total_anos_bene = 0;
							else total_anos_bene = parseInt(total_anos_bene);
							var beneficios_sociales = K.round(remuneracion_principal/2*total_anos_bene,2);
							p.$w.find('[name=bene_remu]').html(remuneracion_principal)
								.data('data',remuneracion_principal);
							p.$w.find('[name=bene_total]').html(K.round(remuneracion_principal/2*total_anos_bene,2))
								.data('data',beneficios_sociales);
							/*
							 * Preguntar sobre las retenciones, fijo son los porcentajes de las AFPs pero no coinciden
							 * ¿habrán llenado todo?
							 */
							var $grid = p.$w.find('.gridCont:eq(0)'),
							$row = $grid.find('.gridReference').clone();
							$grid.find('.gridBody').empty();
							$row.find('li:eq(0)').html('<input type="text" size="45" name="descr" value="S.P.P." />');
							$row.find('li:eq(1)').html('<input type="text" size="4"  name="val" value="10" />%');
							$row.find('li:eq(2)').html('<span name="tot"></span>');
							$row.find('li:eq(3)').html('<button name="btnEli">Eliminar</button>');
							$row.find('[name=val]').keyup(function(){
								p.calc();
							}).numeric();
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item">');
							$grid.find('.gridBody').append($row.children());
							var $row = $grid.find('.gridReference').clone();
							$row.find('li:eq(0)').html('<input type="text" size="45" name="descr" value="Prima Seguro" />');
							$row.find('li:eq(1)').html('<input type="text" size="4"  name="val" value="1.23" />%');
							$row.find('li:eq(2)').html('<span name="tot"></span>');
							$row.find('li:eq(3)').html('<button name="btnEli">Eliminar</button>');
							$row.find('[name=val]').keyup(function(){
								p.calc();
							}).numeric();
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item">');
							$grid.find('.gridBody').append($row.children());
							var $row = $grid.find('.gridReference').clone();
							$row.find('li:eq(0)').html('<input type="text" size="45" name="descr" value="Comision RA" />');
							$row.find('li:eq(1)').html('<input type="text" size="4"  name="val" value="1.60" />%');
							$row.find('li:eq(2)').html('<span name="tot"></span>');
							$row.find('li:eq(3)').html('<button name="btnEli">Eliminar</button>&nbsp;<button name="btnAgr">Agregar</button>');
							$row.find('[name=val]').keyup(function(){
								p.calc();
							}).numeric();
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							$row.wrapInner('<a class="item">');
							$grid.find('.gridBody').append($row.children());
							/*
							 * Grilla de patronales
							 */
							var $grid = p.$w.find('.gridCont:eq(1)'),
							$row = $grid.find('.gridReference').clone();
							$row.find('li:eq(0)').html('<input type="text" size="45" name="descr" value="Comision RA" />');
							$row.find('li:eq(1)').html('<input type="text" size="4"  name="val" value="1.60" />%');
							$row.find('li:eq(2)').html('<span name="tot"></span>');
							$row.find('li:eq(3)').html('<button name="btnEli">Eliminar</button>&nbsp;<button name="btnAgr">Agregar</button>');
							$row.find('[name=val]').keyup(function(){
								p.calc();
							}).numeric();
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
							$row.wrapInner('<a class="item">');
							$grid.find('.gridBody').append($row.children());
							/*
							 * Ultima boleta del trabajador
							 */
							if(entidad.roles.trabajador.ultima_boleta==null){
								K.closeWindow(p.$w.attr('id'));
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'El trabajador no tiene boletas de referencia!',
									type: 'error'
								});
							}else{
								p.$w.find('[name=vaca_remu]').html(entidad.roles.trabajador.ultima_boleta.total)
									.data('data',entidad.roles.trabajador.ultima_boleta.total);
								p.calc();
							}
							K.unblock({$element: p.$w});
						},'json');
					},filter: [
						{nomb: 'tipo_enti',value: 'P'},
						{nomb: 'roles.trabajador',value: {$exists: true}},
						{nomb: 'roles.trabajador.fecing',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowPePlanVac',
			title: 'Beneficios Sociales y Vacaciones Truncas',
			contentURL: 'pe/docs/vac_ben_details',
			icon: 'ui-icon-clipboard',
			width: 800,
			height: 450,
			buttons: {
				'Cerrar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowPePlanVac');
				K.block({$element: p.$w});
				$.post('pe/docs/get',{id: p.id},function(data){
					if(data.trabajador.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.trabajador.imagen.$id);
					else p.$w.find('[name=foto]').removeAttr('src');
					p.$w.find('[name=nomb]').data('data',data.trabajador)
					.html( ciHelper.enti.formatName(data.trabajador) ).attr('title',ciHelper.enti.formatName(data.trabajador)).tooltip();
					p.$w.find('[name=dni]').html( data.trabajador.docident[0].num );
					if(data.trabajador.roles.trabajador.cargo._id!=null)
						p.$w.find('[name=cargo]').html( data.trabajador.roles.trabajador.cargo.nomb );
					else
						p.$w.find('[name=cargo]').html( data.trabajador.roles.trabajador.cargo.funcion );
					p.$w.find('[name=orga]').html( data.trabajador.roles.trabajador.organizacion.nomb ).attr('title',data.trabajador.roles.trabajador.organizacion.nomb).tooltip();
					if(data.trabajador.roles.trabajador.nivel!=null) p.$w.find('[name=nivel]').html( data.trabajador.roles.trabajador.nivel.nomb );
					p.$w.find('[name=essalud]').html( data.trabajador.roles.trabajador.essalud );
					if(data.trabajador.roles.trabajador.pension!=null){
						p.$w.find('[name=pension]').html( data.trabajador.roles.trabajador.pension.nomb );
						p.$w.find('[name=cod_aportante]').html( data.trabajador.roles.trabajador.cod_aportante );
						var porc = data.trabajador.roles.trabajador.pension.porcentajes;
						if(porc.length==3)
							porc = parseFloat(porc[2].val);
						else
							porc = parseFloat(porc[0].val);
						p.$w.find('[name=desc_total]').data('data',porc);
						p.$w.find('[name=desc]').html( data.trabajador.roles.trabajador.pension.nomb+' '+porc+'%' );
					}else{
						p.$w.find('[name=pension]').html('--');
						p.$w.find('[name=cod_aportante]').html('--');
					}
					if(data.trabajador.roles.trabajador.fecing!=null){
						p.$w.find('[name=ini]').html(ciHelper.dateFormatBD(data.trabajador.roles.trabajador.fecing));
					}
					$.post('mg/orga/get','id='+data.trabajador.roles.trabajador.organizacion._id.$id,function(data){
						if(data.actividad!=null) p.$w.find('[name=actividad]').html( data.actividad.cod );
						if(data.componente!=null) p.$w.find('[name=componente]').html( data.componente.cod );
					},'json');
					if(data.vacaciones.bonificacion!=null){
						p.$w.find('[name=bene_remu]').html(data.vacaciones.bonificacion.remuneracion_principal);
						p.$w.find('[name=bene_total]').html(data.vacaciones.bonificacion.pagar);
					}else{
						p.$w.find('fieldset:eq(1)').remove();
					}
					p.$w.find('[name=vaca_remu]').html(data.vacaciones.ultima_remuneracion);
					p.$w.find('[name=periodo]').html(data.vacaciones.periodo);
					p.$w.find('[name=meses]').html(data.vacaciones.meses);
					p.$w.find('[name=dias]').html(data.vacaciones.dias);
					p.$w.find('[name=vaca_periodo]').html(data.vacaciones.periodo);
					p.$w.find('[name=vaca_mes]').html(data.vacaciones.meses+' Mes(es)');
					p.$w.find('[name=total_mes]').html(K.round(data.vacaciones.total_mes,2));
					p.$w.find('[name=vaca_dia]').html(data.vacaciones.dias+' D&iacute;a(s)');
					p.$w.find('[name=total_dia]').html(K.round(data.vacaciones.total_dia,2));
					p.$w.find('[name=total_pre]').html(K.round(data.vacaciones.total,2));
					var total_desc = 0;
					for(var i=0,j=data.vacaciones.descuentos.length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(0) .gridReference').clone();
						$row.find('li:eq(0)').html(data.vacaciones.descuentos[i].descr);
						$row.find('li:eq(1)').html(data.vacaciones.descuentos[i].val);
						var desc = K.round(parseFloat(data.vacaciones.descuentos[i].val)*parseFloat(data.vacaciones.total)/100,2);
						$row.find('li:eq(2)').html(desc);
						$row.wrapInner('<a class="item">');
						p.$w.find('.gridCont:eq(0) .gridBody').append($row.children());
						total_desc += parseFloat(desc);
					}
					p.$w.find('[name=total_desc]').html(K.round(total_desc,2));
					p.$w.find('[name=total]').html(K.round(data.vacaciones.total_pagar,2));
					for(var i=0,j=data.vacaciones.aportaciones.length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(1) .gridReference').clone();
						$row.find('li:eq(0)').html(data.vacaciones.aportaciones[i].descr);
						$row.find('li:eq(1)').html(data.vacaciones.aportaciones[i].val);
						$row.find('li:eq(2)').html(K.round(parseFloat(data.vacaciones.aportaciones[i].val)*parseFloat(data.vacaciones.total)/100,2));
						$row.wrapInner('<a class="item">');
						p.$w.find('.gridCont:eq(1) .gridBody').append($row.children());
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};