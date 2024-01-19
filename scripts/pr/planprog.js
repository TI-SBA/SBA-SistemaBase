/*******************************************************************************
Plan Operativo - Programacion por dependencias */
prPlanProg = {
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
				$p.find('[name=prPlanProg]').click(function(){ prPlanProg.init(); }).addClass('ui-state-highlight');
				$p.find('[name=prPlanEjec]').click(function(){ prPlanEjec.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pr',
			action: 'prPlanProg',
			titleBar: {
				title: 'Programaci&oacute;n del plan operativo Institucional'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			//contentURL: 'pr/plan/index_prog',
			contentURL: 'pr/plan/index_prog_v1',
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
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prPlanProg.windowNew({
						etapa : "P"
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});				
				$mainPanel.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.periodo = $('#mainPanel').find('[name=periodo]').val();
					params.programa = $('#mainPanel').find('[name=programa]').val();
					//params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
					params.organomb= $mainPanel.find('[name=organomb]').html();
					params.estadonomb= $mainPanel.find('[name=estadolabel] span').html();
					params.etapa = "P";
					params.page_rows = 99999;
				    params.page = (params.page) ? params.page : 1;
					var url = 'pr/plan/prog_print_v1?'+$.param(params);
					K.windowPrint({
						id:'windowPlanProgPrint',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				//$mainPanel.find('[name=estadolabel]').html('&nbsp; Estado : <button name="btnAprobar">Aprobar</button>');
				if(K.session.tasks["pr.plan.prog.org"]){
					$mainPanel.find('[name=btnAprobar]').click(function(){
						ciHelper.confirm(
							'Esta seguro(a) de aprobar este POI ?',
							function () {
								//if($mainPanel.find('[name=organizacion]').val()!=""){
								if($mainPanel.find('[name=programa]').val()!=""){									
									if($('#mainPanel').find('.gridBody .item').length>0){
										if($('#mainPanel').find('.gridBody .item').data('data').etapa=="P"){
											for(var i=0;i<$('#mainPanel').find('.gridBody .item').length;i++){
												$.post('pr/plan/save_aprobar','id='+$('#mainPanel').find('.gridBody .item').eq(i).data().id,function(){					
												});
											}
											//$.post('pr/plan/save_create_eval','organizacion_id='+$('#mainPanel').find('.gridBody .item').eq(0).data().data.organizacion._id+'&organizacion_nomb='+$('#mainPanel').find('.gridBody .item').eq(0).data().data.organizacion.nomb+'&periodo='+$('#mainPanel').find('[name=periodo]').val(),function(){					
											$.post('pr/plan/save_create_eval_v1','programa_id='+$('#mainPanel').find('.gridBody .item').eq(0).data().data.programa._id.$id+'&programa_nomb='+$('#mainPanel').find('.gridBody .item').eq(0).data().data.programa.nomb+'&periodo='+$('#mainPanel').find('[name=periodo]').val(),function(){					
											});
											K.clearNoti();
											K.notification({title: 'Unidad Eliminada',text: 'Las Actividades fueron aprobadas con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
											$('#mainPanel .gridBody').empty();
											prPlanProg.loadData({
												page: 1,
												//url: 'pr/plan/prog_lista'
												url: 'pr/plan/prog_lista_v1'
											});
										}else{
											return K.notification({title: ciHelper.titleMessages.regiGua,text: 'Las Actividades ya han sido aprobadas anteriormente!',type: 'error'});
										}
									}else{
										return K.notification({title: ciHelper.titleMessages.regiGua,text: 'Debe haber como minimo una actividad programada!',type: 'error'});
									}
								}else{
									//return K.notification({title: ciHelper.titleMessages.regiGua,text: 'Debe tener seleccionada una organizaci&oacute;n!',type: 'error'});
									return K.notification({title: ciHelper.titleMessages.regiGua,text: 'Debe tener seleccionada un programa!',type: 'error'});
								}
							},
							function () {
								//nothing
							}										
						);						
					}).button({icons: {primary: 'ui-icon-plusthick'}});
				}
				//$mainPanel.find('[name=organomb]').html("Organizaci&oacute;n");
				$mainPanel.find('[name=organomb]').html("Programa");
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100});;
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear()); 
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					prPlanProg.loadData({
						page: 1,
						//url: 'pr/plan/prog_lista'
						url: 'pr/plan/prog_lista_v1'
					});
				});
				if($mainPanel.find('[name=programa]').val()==""){
					$mainPanel.find('[name=programa]').val(K.session.enti.roles.trabajador.programa._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.programa.nomb);
				}
				/*if($mainPanel.find('[name=organizacion]').val()==""){
					$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				}*/
				if(K.session.tasks["pr.plan.prog.org"]){
					$mainPanel.find('[name=FilOrga]').buttonset();
					$mainPanel.find('#rbtnOrgaSelect').click(function(){
						/*ciSearch.windowSearchOrga({callback: function(data){
							//$mainPanel.find('[name=organizacion]').val(data._id.$id);
							//$mainPanel.find('[name=organomb]').html(data.nomb);
							$mainPanel.find('[name=programa]').val(data._id.$id);
							$mainPanel.find('[name=organomb]').html(data.nomb);
							$('#mainPanel .gridBody').empty();
							//prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista'});
							prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
						}});*/
						mgProg.windowSelect({callback: function(data){
							$mainPanel.find('[name=programa]').val(data._id.$id);
							$mainPanel.find('[name=organomb]').html(data.nomb);
							$('#mainPanel .gridBody').empty();
							prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
						}});
					});
					$mainPanel.find('#rbtnOrgaX').click(function(){
						//$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
						//$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
						$mainPanel.find('[name=programa]').val(K.session.enti.roles.trabajador.programa._id.$id);
						$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.programa.nomb);
						$('#mainPanel .gridBody').empty();
						//prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista'});
						prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
					});
				}else{
					//$mainPanel.find('[name=orgainput]').empty();
					//$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
					//$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
					$mainPanel.find('[name=orgainput]').empty();
					$mainPanel.find('[name=programa]').val(K.session.enti.roles.trabajador.programa._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.programa.nomb);
				}
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						//prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista'});
						prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
					}else{
						$("#mainPanel .gridBody").empty();
						prPlanProg.loadData({page: 1,url: 'pr/plan/prog_search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.periodo = $('#mainPanel').find('[name=periodo]').val();
		params.programa = $('#mainPanel').find('[name=programa]').val();
		//params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
		params.etapa = "P";
		params.page_rows = 100;
	    params.page = (params.page) ? params.page : 1;    
	    $.sum = function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	    };
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				$mainPanel.find('[name=btnImprimir]').show();
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( result.actividad );
					$li.eq(2).html( result.unidad.nomb );
					var a=0;
					var total = 0;
					$li.eq(3).html( result.metas.programadas[0] );
					$li.eq(4).html( result.metas.programadas[1] );
					$li.eq(5).html( result.metas.programadas[2] );
					$li.eq(6).html( parseFloat(result.metas.programadas[0]) + parseFloat(result.metas.programadas[1]) + parseFloat(result.metas.programadas[2]) );
					$li.eq(7).html( result.metas.programadas[3] );
					$li.eq(8).html( result.metas.programadas[4] );
					$li.eq(9).html( result.metas.programadas[5] );
					$li.eq(10).html( parseFloat(result.metas.programadas[3]) + parseFloat(result.metas.programadas[4]) + parseFloat(result.metas.programadas[5]) );
					$li.eq(11).html( result.metas.programadas[6] );
					$li.eq(12).html( result.metas.programadas[7] );
					$li.eq(13).html( result.metas.programadas[8] );
					$li.eq(14).html( parseFloat(result.metas.programadas[6]) + parseFloat(result.metas.programadas[7]) + parseFloat(result.metas.programadas[8]) );
					$li.eq(15).html( result.metas.programadas[9] );
					$li.eq(16).html( result.metas.programadas[10] );
					$li.eq(17).html( result.metas.programadas[11] );
					$li.eq(18).html( parseFloat(result.metas.programadas[9]) + parseFloat(result.metas.programadas[10]) + parseFloat(result.metas.programadas[11]) );
					$li.eq(19).html( $.sum(result.metas.programadas));
					/*
					for(var e=3;e<=15;e++){
						if(e<15){
							$li.eq(e).html(result.metas.programadas[a]);
							total = parseFloat(result.metas.programadas[a])+total;
						}else{
							$li.eq(e).html(total);
						}

						a++;
					}*/
					if(result.etapa=="P" && K.session.tasks["pr.plan.prog.org"]!="1"){
						$mainPanel.find('[name=estadolabel]').html("&nbsp;Estado: <span class=\"label\">En Revisi&oacute;n</span>");
						$mainPanel.find('[name=btnAprobar]').hide();
						$mainPanel.find('[name=btnAgregar]').show();
					}else if(result.etapa=="E"){
						$mainPanel.find('[name=estadolabel]').html("&nbsp;Estado: <span class=\"label label-success\">Aprobado</span>");
						$mainPanel.find('[name=btnAprobar]').hide();
						$mainPanel.find('[name=btnAgregar]').hide();
					}else if(result.etapa=="P" && K.session.tasks["pr.plan.prog.org"]=="1"){
						$mainPanel.find('[name=estadolabel]').html("&nbsp;Estado: <span class=\"label\">En Revisi&oacute;n</span>");
						$mainPanel.find('[name=btnAprobar]').show();
						$mainPanel.find('[name=btnAgregar]').show();
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenPrPlanProg", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').etapa=="E" || K.session.tasks["pr.plan.prog"]!=1){
									$('#conMenPrPlanProg_ediAct,#conMenPrPlanProg_eliAct',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenPrPlanProg_ediAct': function(t) {
									prPlanProg.windowEdit({id: K.tmp.data('id'),etapa:K.tmp.data('data').etapa});
								},
								'conMenPrPlanProg_eliAct': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de eliminar esta actividad ?',
										function () {
											var data = {
												id: K.tmp.data('id')
											};
											K.sendingInfo();
											$.post('pr/plan/deleteplan_v1',data,function(){
												K.clearNoti();
												K.notification({title: 'Fuente Eliminada',text: 'La Actividad Programada  seleccionada ha sido eliminado con &eacute;xito!'});
												$('#pageWrapperLeft .ui-state-highlight').click();
											});
										},
										function () {
											//nothing
										}										
									);			
								}
							}
						});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
	      } else {
	        $mainPanel.find('[name=estadolabel]').html("");
	        $mainPanel.find('[name=btnAprobar]').hide();
	        $mainPanel.find('[name=btnAgregar]').show();
	        $mainPanel.find('[name=btnImprimir]').hide();
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewPlanActi',
			title: 'Nueva Actividad',
			contentURL: 'pr/plan/progeditacti',
			icon: 'ui-icon-plusthick',
			width: 430,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.periodo = p.$w.find('[name=periodo]').html();
					data.actividad = p.$w.find('[name=descr]').val();
					if(data.actividad==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la Actividad!',type: 'error'});
					}
					data.unidad = new Object;
					data.etapa = p.etapa;
					var split = p.$w.find('[name=unid] :selected').val();
					dataunid = split.split("*");
					data.unidad._id = dataunid[0];
					data.unidad.cod = dataunid[1];
					data.unidad.nomb = dataunid[2];
					data.unidad.abrev = dataunid[3];
					data.metas = new Object;
					data.metas.programadas = new Array;
					/*Metas para meses*/
					if(p.etapa=="P"){
						if(p.$w.find('[name=enero]').val()!="")	data.metas.programadas.push(p.$w.find('[name=enero]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=febrero]').val()!="")	data.metas.programadas.push(p.$w.find('[name=febrero]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=marzo]').val()!="")	data.metas.programadas.push(p.$w.find('[name=marzo]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=abril]').val()!="")	data.metas.programadas.push(p.$w.find('[name=abril]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=mayo]').val()!="")	data.metas.programadas.push(p.$w.find('[name=mayo]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=junio]').val()!="")	data.metas.programadas.push(p.$w.find('[name=junio]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=julio]').val()!="")	data.metas.programadas.push(p.$w.find('[name=julio]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=agosto]').val()!="")	data.metas.programadas.push(p.$w.find('[name=agosto]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=septiembre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=septiembre]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=octubre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=octubre]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=noviembre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=noviembre]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=diciembre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=diciembre]').val());
						else data.metas.programadas.push("0");

					}else{
						for(var i=0;i<=11;i++){
							data.metas.programadas.push("0");
						}
					}
					data.metas.ejecutadas = new Array;
					for(var i=0;i<=11;i++){
						data.metas.ejecutadas.push("0");
					}
					/*data.organizacion = new Object;
					data.organizacion._id = K.session.enti.roles.trabajador.organizacion._id.$id;
					data.organizacion.nomb = K.session.enti.roles.trabajador.organizacion.nomb;
					data.trabajador = new Object;
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);*/
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/plan/save_activ_v1',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Actividad fue registrada con &eacute;xito!'});
						$('#mainPanel .gridBody').empty();
						prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
						//prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewPlanActi');
				K.block({$element: p.$w});
				if(p.etapa=="E"){
					p.$w.find('[name=metas]').hide();
				}
				p.$w.find('[name=enero]').numeric();
				p.$w.find('[name=febrero]').numeric();
				p.$w.find('[name=marzo]').numeric();
				p.$w.find('[name=abril]').numeric();
				p.$w.find('[name=mayo]').numeric();
				p.$w.find('[name=junio]').numeric();
				p.$w.find('[name=julio]').numeric();
				p.$w.find('[name=agosto]').numeric();
				p.$w.find('[name=septiembre]').numeric();
				p.$w.find('[name=octubre]').numeric();
				p.$w.find('[name=noviembre]').numeric();
				p.$w.find('[name=diciembre]').numeric();
				p.$w.find('[name=periodo]').html($('#mainPanel').find('[name=periodo]').val());
				if(p.cod){
					p.$w.find('[name=parentcod]').html(p.cod+".");
				}
				$.post('pr/unid/all',function(data){
					var $cbo = p.$w.find('[name=unid]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							var nomb = data[i].nomb;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							var abrev = data[i].abrev;
							$cbo.append('<option value="'+id+'*'+cod+'*'+nomb+'*'+abrev+'">'+nomb+'</option>');
						}
					}
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditPlanActi'+p.id,
			title: 'Editar Actividad',
			contentURL: 'pr/plan/progeditacti',
			icon: 'ui-icon-plusthick',
			width: 430,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.actividad = p.$w.find('[name=descr]').val();
					if(data.actividad==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n para la Actividad!',type: 'error'});
					}
					data.unidad = new Object;
					data.etapa = p.etapa;
					var split = p.$w.find('[name=unid] :selected').val();
					dataunid = split.split("*");
					data.unidad._id = dataunid[0];
					data.unidad.cod = dataunid[1];
					data.unidad.nomb = dataunid[2];
					data.unidad.abrev = dataunid[3];
					data.metas = new Object;
					data.metas.programadas = new Array;
					/*Metas para meses*/
					if(p.etapa=="P"){
						if(p.$w.find('[name=enero]').val()!="")	data.metas.programadas.push(p.$w.find('[name=enero]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=febrero]').val()!="")	data.metas.programadas.push(p.$w.find('[name=febrero]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=marzo]').val()!="")	data.metas.programadas.push(p.$w.find('[name=marzo]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=abril]').val()!="")	data.metas.programadas.push(p.$w.find('[name=abril]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=mayo]').val()!="")	data.metas.programadas.push(p.$w.find('[name=mayo]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=junio]').val()!="")	data.metas.programadas.push(p.$w.find('[name=junio]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=julio]').val()!="")	data.metas.programadas.push(p.$w.find('[name=julio]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=agosto]').val()!="")	data.metas.programadas.push(p.$w.find('[name=agosto]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=septiembre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=septiembre]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=octubre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=octubre]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=noviembre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=noviembre]').val());
						else data.metas.programadas.push("0");
						if(p.$w.find('[name=diciembre]').val()!="")	data.metas.programadas.push(p.$w.find('[name=diciembre]').val());
						else data.metas.programadas.push("0");
					}else{
						for(var i=0;i<=11;i++){
							data.metas.programadas.push("0");
						}
					}
					data.metas.ejecutadas = new Array;
					for(var i=0;i<=11;i++){
						data.metas.ejecutadas.push("0");
					}
					/*data.organizacion = new Object;
					data.organizacion._id = K.session.enti.roles.trabajador.organizacion._id.$id;
					data.organizacion.nomb = K.session.enti.roles.trabajador.organizacion.nomb;
					data.trabajador = new Object;
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);*/
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					//$.post('pr/plan/save_update_activ',data,function(){
					$.post('pr/plan/save_update_activ_v1',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Actividad fue registrada con &eacute;xito!'});
						$('#mainPanel .gridBody').empty();
						//prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista'});
						prPlanProg.loadData({page: 1,url: 'pr/plan/prog_lista_v1'});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditPlanActi'+p.id);
				K.block({$element: p.$w});
				$.post('pr/plan/get','id='+p.id,function(data){
					if(p.etapa=="E"){
						p.$w.find('[name=metas]').hide();
					}else{
						p.$w.find('[name=enero]').val(data.metas.programadas[0]).numeric();
						p.$w.find('[name=febrero]').val(data.metas.programadas[1]).numeric();
						p.$w.find('[name=marzo]').val(data.metas.programadas[2]).numeric();
						p.$w.find('[name=abril]').val(data.metas.programadas[3]).numeric();
						p.$w.find('[name=mayo]').val(data.metas.programadas[4]).numeric();
						p.$w.find('[name=junio]').val(data.metas.programadas[5]).numeric();
						p.$w.find('[name=julio]').val(data.metas.programadas[6]).numeric();
						p.$w.find('[name=agosto]').val(data.metas.programadas[7]).numeric();
						p.$w.find('[name=septiembre]').val(data.metas.programadas[8]).numeric();
						p.$w.find('[name=octubre]').val(data.metas.programadas[9]).numeric();
						p.$w.find('[name=noviembre]').val(data.metas.programadas[10]).numeric();
						p.$w.find('[name=diciembre]').val(data.metas.programadas[11]).numeric();
					}
					p.$w.find('[name=periodo]').html(data.periodo);
					p.$w.find('[name=descr]').html(data.actividad);
					$.post('pr/unid/all',function(data2){
						var $cbo = p.$w.find('[name=unid]');
						if(data2!=null){
							for(var i=0; i<data2.length; i++){
								var nomb = data2[i].nomb;
								var cod = data2[i].cod;
								var id = data2[i]._id.$id;
								var abrev = data2[i].abrev;
								var selected ="";
								if(data.unidad._id==id) selected = "selected=selected";
								$cbo.append('<option value="'+id+'*'+cod+'*'+nomb+'*'+abrev+'" '+selected+'>'+nomb+'</option>');
							}
						}
					},'json');
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}	
};
