/*******************************************************************************
Plan Operativo - Ejecucion */
prPlanEjec = {
	states: [ "P" , "C" ],
	init: function(){
		if($('#pageWrapper [child=plan]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pr/navg/plan',function(data){
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
					$p.find("[name=prPlan]").after( $row.children() );
				}
				$p.find('[name=prPlan]').data('plan',$('#pageWrapper [child=plan]:first').data('plan'));
				$p.find('[name=prPlanProgDep]').click(function(){ prPlanProgDep.init(); });
				$p.find('[name=prPlanEjecDep]').click(function(){ prPlanEjecDep.init(); });
				$p.find('[name=prPlanProg]').click(function(){ prPlanProg.init(); });
				$p.find('[name=prPlanEjec]').click(function(){ prPlanEjec.init(); }).addClass('ui-state-highlight');
			},'json');
		}	
		K.initMode({
			mode: 'pr',
			action: 'prPlanEjec',
			titleBar: {
				title: 'Ejecuci&oacute;n del plan operativo Institucional'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/plan/index_ejec',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				if($mainPanel.find('[name=organizacion]').val()==""){
					$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				}
				$mainPanel.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.periodo = $('#mainPanel').find('[name=periodo]').val();
					params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
					params.trimestre = $('#mainPanel').find('[name=trimestre] :selected').val();
					params.organomb= $mainPanel.find('[name=organomb]').html();
					params.etapa = "E";
					params.texto = $('.divSearch [name=buscar]').val();
					params.page_rows = 20;
				    params.page = (params.page) ? params.page : 1;
					var url = 'pr/plan/ejec_print?'+$.param(params);
					K.windowPrint({
						id:'windowPlanEjecPrint',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				if(K.session.tasks["pr.plan.prog.org"]){
					$mainPanel.find('[name=FilOrga]').buttonset();
					$mainPanel.find('#rbtnOrgaSelect').click(function(){
						ciSearch.windowSearchOrga({callback: function(data){
							$mainPanel.find('[name=organizacion]').val(data._id.$id);
							$mainPanel.find('[name=organomb]').html(data.nomb);
							//console.log(data);
							$('#mainPanel .gridBody').empty();
							prPlanEjec.loadData({page: 1,url: 'pr/plan/ejec_lista'});
						}});
					});
					$mainPanel.find('#rbtnOrgaX').click(function(){
						$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
						$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
						$('#mainPanel .gridBody').empty();
						prPlanEjec.loadData({page: 1,url: 'pr/plan/ejec_lista'});
					});
				}else{
					$mainPanel.find('[name=orgainput]').empty();
					$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				}
				$mainPanel.find('[name=btnAgregar]').click(function(){
				if($mainPanel.find('[name=organizacion]').val()!=""){
						if($mainPanel.find('.gridBody .item').length>0){
							if(K.session.enti.roles.trabajador.organizacion._id.$id!=$mainPanel.find('.gridBody .item').data('eval').organizacion._id || $mainPanel.find('.gridBody .item').data('eval').estado=="C"){
								prPlanEjec.windowDetails({
									data : $('#mainPanel').find('.gridBody .item').data('eval')
								});
							}else{
								prPlanEjec.windowNew({
									data : $('#mainPanel').find('.gridBody .item').data('eval')
								});
							}
						}else{
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe haber como minimo una actividad!',type: 'error'});
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.regiGua,text: 'Debe tener seleccionada una organizaci&oacute;n!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-document'}});
				
				
				$mainPanel.find('[name=btnAgregarAct]').click(function(){
						prPlanProg.windowNew({
							etapa : "E"
						});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
						$mainPanel.find('[name=btnCerrarTrim]').click(function(){
							if($mainPanel.find('.gridBody .item').length>0){
								if($mainPanel.find('.gridBody .item').data('eval').estado=="P"){
									if($mainPanel.find('.gridBody .item').data('eval').conclusiones!=""){
										if($mainPanel.find('.gridBody .item').data('eval').dificultades!=""){
											if($mainPanel.find('.gridBody .item').data('eval').recomedaciones!=""){
												if($mainPanel.find('.gridBody .item').data('eval').logros!=""){
													var data = new Object;
													data._id = $mainPanel.find('.gridBody .item').data('eval')._id.$id;
													data.estado = "C";
													K.sendingInfo();
													$.post('pr/plan/save_create_eval',data,function(){
														K.clearNoti();
														K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Trimestre fue cerrado con &eacute;xito!'});
														$('#pageWrapperLeft .ui-state-highlight').click();
													});	
												}else{
													return K.notification({title: ciHelper.titleMessages.infoReq,text: 'La evaluaci&oacute;n no tiene "logros"!',type: 'error'});
												}
											}else{
												return K.notification({title: ciHelper.titleMessages.infoReq,text: 'La evaluaci&oacute;n no tiene "recomendaciones"!',type: 'error'});	
											}
										}else{
											return K.notification({title: ciHelper.titleMessages.infoReq,text: 'La evaluaci&oacute;n no tiene "dificultades"!',type: 'error'});
										}
									}else{
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'La evaluaci&oacute;n no tiene "conclusiones"!',type: 'error'});
									}								
								}else{
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Este trimestre ya fue Cerrado!',type: 'error'});
								}
							}else{
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe haber como minimo una actividad!',type: 'error'});
							}
						}).button({icons: {primary: 'ui-icon-locked'}});
				$mainPanel.find('[name=mes1]').html("Ene");
				$mainPanel.find('[name=mes2]').html("Feb");
				$mainPanel.find('[name=mes3]').html("Mar");
				$mainPanel.find('[name=totmes]').html("I Trim");
				$mainPanel.find('[name=trimestre]').change(function(){
					var trim = $mainPanel.find('[name=trimestre] :selected').val();
					if(trim=="1"){
						$mainPanel.find('[name=mes1]').html("Ene");
						$mainPanel.find('[name=mes2]').html("Feb");
						$mainPanel.find('[name=mes3]').html("Mar");
						$mainPanel.find('[name=totmes]').html("I Trim");
					}else if(trim=="2"){
						$mainPanel.find('[name=mes1]').html("Abr");
						$mainPanel.find('[name=mes2]').html("May");
						$mainPanel.find('[name=mes3]').html("Jun");
						$mainPanel.find('[name=totmes]').html("II Trim");
					}else if(trim=="3"){
						$mainPanel.find('[name=mes1]').html("Jul");
						$mainPanel.find('[name=mes2]').html("Ago");
						$mainPanel.find('[name=mes3]').html("Set");
						$mainPanel.find('[name=totmes]').html("III Trim");
					}else if(trim=="4"){
						$mainPanel.find('[name=mes1]').html("Oct");
						$mainPanel.find('[name=mes2]').html("Nov");
						$mainPanel.find('[name=mes3]').html("Dic");
						$mainPanel.find('[name=totmes]').html("IV Trim");
					}
					$('#mainPanel .gridBody').empty();
					prPlanEjec.loadData({page: 1,url: 'pr/plan/ejec_lista'});
				});
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100});;
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear());
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					prPlanEjec.loadData({
						page: 1,
						url: 'pr/plan/ejec_lista'
					}); 
				});		
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});

				$mainPanel.find('[name=btnImprimirEva]').click(function(){	
					params = new Object;
					params.periodo = $('#mainPanel').find('[name=periodo]').val();
					params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
					params.trimestre = $('#mainPanel').find('[name=trimestre] :selected').val();
					params.organomb= $mainPanel.find('[name=organomb]').html();
					var url = 'pr/plan/planevac_print?'+$.param(params);
					K.windowPrint({
						id:'windowPlanEjecPrint',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				prPlanEjec.loadData({page: 1,url: 'pr/plan/ejec_lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.periodo = $('#mainPanel').find('[name=periodo]').val();
		params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
		var paramtrim = $('#mainPanel').find('[name=trimestre] :selected').val();
		params.trimestre = $('#mainPanel').find('[name=trimestre] :selected').val();
		params.etapa = "E";
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.sum = function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	    };
	    
	    $.post(params.url, params, function(data){
			if ( data.items!=null ) { 
				$mainPanel.find('[name=btnImprimir]').show();
				for (i=0; i < data.items.length; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',prPlanEjec.states[data.eval.estado].color).addClass('vtip').attr('title',prPlanEjec.states[data.eval.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.actividad );
					$li.eq(3).html( result.unidad.nomb );
					$li.eq(4).html( $.sum(result.metas.programadas) );
					var trimestre = $('#mainPanel').find('[name=trimestre] :selected').val();
					if(trimestre=="1"){
						var sum_prog_trim = parseFloat(result.metas.programadas[0]) + parseFloat(result.metas.programadas[1]) + parseFloat(result.metas.programadas[2]);
					}else if(trimestre=="2"){
						var sum_prog_trim = parseFloat(result.metas.programadas[3]) + parseFloat(result.metas.programadas[4]) + parseFloat(result.metas.programadas[5]);
					}else if(trimestre=="3"){
						var sum_prog_trim = parseFloat(result.metas.programadas[6]) + parseFloat(result.metas.programadas[7]) + parseFloat(result.metas.programadas[8]);
					}else if(trimestre=="4"){
						var sum_prog_trim = parseFloat(result.metas.programadas[9]) + parseFloat(result.metas.programadas[10]) + parseFloat(result.metas.programadas[11]);
					}
					$li.eq(5).html( sum_prog_trim );
					if(trimestre=="1"){
						$li.eq(6).html( result.metas.ejecutadas[0] );
						$li.eq(7).html( result.metas.ejecutadas[1] );
						$li.eq(8).html( result.metas.ejecutadas[2] );
					}else if(trimestre=="2"){
						$li.eq(6).html( result.metas.ejecutadas[3] );
						$li.eq(7).html( result.metas.ejecutadas[4] );
						$li.eq(8).html( result.metas.ejecutadas[5] );
					}else if(trimestre=="3"){
						$li.eq(6).html( result.metas.ejecutadas[6] );
						$li.eq(7).html( result.metas.ejecutadas[7] );
						$li.eq(8).html( result.metas.ejecutadas[8] );
					}else if(trimestre=="4"){
						$li.eq(6).html( result.metas.ejecutadas[9] );
						$li.eq(7).html( result.metas.ejecutadas[10] );
						$li.eq(8).html( result.metas.ejecutadas[11] );
					}
					var sum_trim = parseFloat($li.eq(6).html()) + parseFloat($li.eq(7).html()) + parseFloat($li.eq(8).html());
					$li.eq(9).html( sum_trim );
					if(sum_prog_trim!="0"){
					$li.eq(10).html( Math.round((sum_trim*100/sum_prog_trim)*100)/100+"%" );
					$li.eq(11).html( Math.round((sum_trim*100/$.sum(result.metas.programadas))*100)/100+"%" );
					}else{
						$li.eq(10).html( "-" );
						$li.eq(11).html( "-" );
					}
					if(data.eval.estado=="P"){
						$mainPanel.find('[name=btnAgregarAct]').show();
					}else{
						$mainPanel.find('[name=btnAgregarAct]').hide();
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('eval',data.eval).data('data',result)
					.contextMenu("conMenPrPlanEjec", {
							onShowMenu: function(e, menu) {
								var excep = '';
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('eval').estado=="C"){
									excep +='#conMenPrPlanEjec_Ingr';
								}
								$(excep+',#conMenPrPlanEjec_Cerr',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPrPlanEjec_Ingr': function(t) {
									prPlanEjec.windowNewMet({id: K.tmp.data('id'),descr: K.tmp.find('li:eq(2)').html(),unid: K.tmp.find('li:eq(3)').html(),mes1: K.tmp.find('li:eq(6)').html(),mes2: K.tmp.find('li:eq(7)').html(),mes3: K.tmp.find('li:eq(8)').html()});
								}
							}
						});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }		     
	      } else {	      
	        $mainPanel.find('[name=btnAgregarAct]').hide();
	        $mainPanel.find('[name=btnImprimir]').hide();
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsPlanEjecEval'+p.data._id.$id,
			title: 'Evaluaci&oacute;n de Actividades',
			contentURL: 'pr/plan/ejecdetailseval',
			store: false,
			icon: 'ui-icon-document',
			width: 430,
			height: 380,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsPlanEjecEval'+p.data._id.$id);
				K.block({$element: p.$w});
				p.$w.find("[name=periodo]").html(p.data.periodo);
				p.$w.find("[name=trimestre]").html(p.data.trimestre);
				p.$w.find("[name=organizacion]").html(p.data.organizacion.nomb);
				p.$w.find("[name=log]").html(p.data.logros);
				p.$w.find("[name=dif]").html(p.data.dificultades);
				p.$w.find("[name=rec]").html(p.data.recomendaciones);
				p.$w.find("[name=con]").html(p.data.conclusiones);
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewPlanEval',
			title: 'Nueva Evaluaci&oacute;n',
			contentURL: 'pr/plan/ejecediteval',
			icon: 'ui-icon-plusthick',
			width: 430,
			height: 380,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.data._id.$id;
					data.logros = p.$w.find('[name=log]').val();
					data.dificultades = p.$w.find('[name=dif]').val();
					data.recomendaciones = p.$w.find('[name=rec]').val();
					data.conclusiones = p.$w.find('[name=con]').val();
					data.trabajador = new Object;
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/plan/save_create_eval',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Evaluaci&oacute;n fue registrada con &eacute;xito!'});
						$('#mainPanel .gridBody').empty();
						prPlanEjec.loadData({page: 1,url: 'pr/plan/ejec_lista'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewPlanEval');
				K.block({$element: p.$w});
				p.$w.find("[name=periodo]").html(p.data.periodo);
				p.$w.find("[name=trimestre]").html(p.data.trimestre);
				p.$w.find("[name=organizacion]").html(p.data.organizacion.nomb);
				p.$w.find("[name=log]").val(p.data.logros);
				p.$w.find("[name=dif]").val(p.data.dificultades);
				p.$w.find("[name=rec]").val(p.data.recomendaciones);
				p.$w.find("[name=con]").val(p.data.conclusiones);
				p.$w.find("[name=prTrim]").buttonset();
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNewMet: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewPlanMet',
			title: 'Ingresar Metas',
			contentURL: 'pr/plan/ejeceditmet',
			icon: 'ui-icon-plusthick',
			width: 430,
			height: 180,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.trimestre = $('#mainPanel').find("[name=trimestre] :selected").val();
					data.metas = new Object;
					data.metas.ejecutadas = new Array();
					data.metas.ejecutadas[0]=p.$w.find("[name=mesvalue1]").val();
					data.metas.ejecutadas[1]=p.$w.find("[name=mesvalue2]").val();
					data.metas.ejecutadas[2]=p.$w.find("[name=mesvalue3]").val();
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/plan/save_activ',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Las metas fueron registradas con &eacute;xito!'});
						$('#mainPanel .gridBody').empty();
						prPlanEjec.loadData({page: 1,url: 'pr/plan/ejec_lista'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewPlanMet');
				K.block({$element: p.$w});
				p.$w.find("[name=descr]").html(p.descr);
				p.$w.find("[name=unid]").html(p.unid);
				var met_trim = $('#mainPanel').find("[name=trimestre] :selected").val();
				if(met_trim=="1"){
					p.$w.find("[name=mes1]").html("Enero : ");
					p.$w.find("[name=mes2]").html("Febrero : ");
					p.$w.find("[name=mes3]").html("Marzo : ");
				}else if(met_trim=="2"){
					p.$w.find("[name=mes1]").html("Abril : ");
					p.$w.find("[name=mes2]").html("Mayo : ");
					p.$w.find("[name=mes3]").html("Junio : ");
				}else if(met_trim=="3"){
					p.$w.find("[name=mes1]").html("Julio : ");
					p.$w.find("[name=mes2]").html("Agosto : ");
					p.$w.find("[name=mes3]").html("Setiembre : ");
				}else if(met_trim=="4"){
					p.$w.find("[name=mes1]").html("Octubre : ");
					p.$w.find("[name=mes2]").html("Noviembre : ");
					p.$w.find("[name=mes3]").html("Diciembre : ");
				}
				p.$w.find("[name=mesvalue1]").val(p.mes1).numeric();
				p.$w.find("[name=mesvalue2]").val(p.mes2).numeric();
				p.$w.find("[name=mesvalue3]").val(p.mes3).numeric();
				K.unblock({$element: p.$w});
			}
		});
	}
};
prPlanEjec.states["P"] = {
		descr: "Pendiente",
		color: "#CCCCCC"
};
prPlanEjec.states["C"] = {
		descr: "Completado",
		color: "#4E861A"
};