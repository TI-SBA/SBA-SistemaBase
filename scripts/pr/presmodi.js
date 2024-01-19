/*******************************************************************************
Presupuesto Institucional: Modificado */
prPresModi = {
	init: function(){
		if($('#pageWrapper [child=pres]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pr/navg/pres',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="pres" />');
					$p.find("[name=prPres]").after( $row.children() );
				}
				$p.find('[name=prPres]').data('pres',$('#pageWrapper [child=pres]:first').data('pres'));
				$p.find('[name=prPresAper]').click(function(){ prPresAper.init(); });
				$p.find('[name=prPresModi]').click(function(){ prPresModi.init(); }).addClass('ui-state-highlight');
				$p.find('[name=prPresModi_Nota]').click(function(){ prPresModiNota.init(); });
				$p.find('[name=prPresModi_Cred]').click(function(){ prPresModiCred.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pr',
			action: 'prPresModi',
			titleBar: {
				title: 'Presupuesto Institucional Modificado'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/pres/index_modi',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-30)+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prPresModi.windowNew({tipo: $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnAgregarNota]').click(function(){
					prPresModi.windowNewNota({tipo: $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=organomb]').html("Organizaci&oacute;n");
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100}).change(function(){
					$('#mainPanel .gridBody').empty();
					prPresModi.loadData({
						page: 1,
						url: 'pr/pres/modi_lista'
					});
			    });	
				$mainPanel.find('[name=btnImprimir]').click(function(){
					if($mainPanel.find('.item').length>0){
						var params = {
								tipo: $('#mainPanel [name=FilTipo] :selected').val(),
								periodo: $('#mainPanel [name=periodo]').val(),
								mes: $('#mainPanel').find('[name=mes] :selected').val(),
								organizacion: $('#mainPanel').find('[name=organizacion]').val(),
								clasificador: $('#mainPanel').find('[name=clasificador]').val(),
								organomb: $('#mainPanel').find('[name=organomb]').html(),
								clasnomb: $('#mainPanel').find('[name=clasnomb]').html(),
								page_rows: 99999,
								page: 1
							}, url = 'pr/pres/modi_print?'+$.param(params);
							console.log(url);
							K.windowPrint({
								id:'windowAperPartPrint',
								title: "Reporte / Informe",
								url: url
							});
					}else{
						return K.notification({title: 'Error',text: 'No existen partidas para generar el reporte!!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-print'}});
				$mainPanel.find('[name=btnExportar]').click(function(){
					if($mainPanel.find('.item').length>0){
						var params = new Object;
						params.tipo = $('#mainPanel').find('[name=FilTipo] :selected').val();
						params.periodo = $('#mainPanel').find('[name=periodo]').val();
						params.mes = $('#mainPanel').find('[name=mes] :selected').val();
						params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
						params.clasificador = $('#mainPanel').find('[name=clasificador]').val();
						params.page_rows = 99999;
					    params.page = 1;
						window.open("pr/pres/modi_export?"+$.param(params));
					}else{
						return K.notification({title: 'Error',text: 'No existen partidas para generar el reporte!!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-extlink'}});
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear()); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					prPresModi.loadData({
						page: 1,
						url: 'pr/pres/modi_lista'
					}); 
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					prPresModi.loadData({
						page: 1,
						url: 'pr/pres/modi_lista'
					});
			    });
				$mainPanel.find('[name=FilClas]').buttonset();
				$mainPanel.find('#rbtnClasSelect').click(function(){
					prClas.windowSelect({callback: function(data){
						$mainPanel.find('[name=clasificador]').val(data._id.$id);
						$mainPanel.find('[name=clasnomb]').html(data.nomb);
						$('#mainPanel .gridBody').empty();
						prPresModi.loadData({
							page: 1,
							url: 'pr/pres/modi_lista'
						});
					}});
				});
				$mainPanel.find('#rbtnClasX').click(function(){
						$mainPanel.find('[name=clasificador]').val("");
						$mainPanel.find('[name=clasnomb]').html("");
						$('#mainPanel .gridBody').empty();
						prPresModi.loadData({
							page: 1,
							url: 'pr/pres/modi_lista'
						});
				});
				$mainPanel.find('[name=FilOrga]').buttonset();
				$mainPanel.find('#rbtnOrgaSelect').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$mainPanel.find('[name=organizacion]').val(data._id.$id);
						$mainPanel.find('[name=organomb]').html(data.nomb);
						$('#mainPanel .gridBody').empty();
						prPresModi.loadData({
							page: 1,
							url: 'pr/pres/modi_lista'
						});
					}});
				});
				$mainPanel.find('#rbtnOrgaX').click(function(){
						$mainPanel.find('[name=organizacion]').val("");
						$mainPanel.find('[name=organomb]').html("Organizaci&oacute;n");
						$('#mainPanel .gridBody').empty();
						prPresModi.loadData({
							page: 1,
							url: 'pr/pres/modi_lista'
						});
				});
				$mainPanel.find('[name=FilTipo]').change(function(){
					$('#mainPanel .gridBody').empty();
					prPresModi.loadData({
						page: 1,
						url: 'pr/pres/modi_lista'
					});
			    });
				prPresModi.loadData({
					page: 1,
					url: 'pr/pres/modi_lista'
				});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){		
		params.tipo = $('#mainPanel').find('[name=FilTipo] :selected').val();
		params.periodo = $('#mainPanel').find('[name=periodo]').val();
		params.mes = $('#mainPanel').find('[name=mes] :selected').val();
		params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
		params.clasificador = $('#mainPanel').find('[name=clasificador]').val();
		params.page_rows = 999999999;
	    params.page = (params.page) ? params.page : 1;
	    $.sum = function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	    };
	    K.block({$element: $('#pageWrapperMain')});
	    $.post(params.url, params, function(data){
	    	K.loading();    	
	    	if(data.items==null){
	    		K.unblock({$element: $('#pageWrapperMain')});
	    		return K.notification({title: 'Error de Informaci&oacute;n',text: 'Es necesario tener Clasificadores!!',type: 'error'});    		
	    	}
			if ( data.items.length>0 ) { 
				for (i=0; i < data.items.length; i++) {
					result = data.items[i];
					result2 = data.importes[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( result.cod );
					$li.eq(2).html( result.nomb );
					var e=0;
					var total = 0;
					if(data.num_fuen=="3"){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Actualmente no hay fuentes de financiamiento!',type: 'error'});
					}
					for(a=3; a<=data.num_fuen; a++){
						if(a!=data.num_fuen){
							$li.eq(a).html(K.round($.sum(result2.importe[e]),2)).css({"text-align":"right"});							
							total = $.sum(result2.importe[e])+total;
						}else{						
							$li.eq(a).html( K.round(total,2)).css({"text-align":"right"});	
						}
						e++;
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenPrPresAper", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenList_about,#conMenPrPresAper_eliPart,#conMenPrPresAper_ediPart',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPrPresAper_ediPart': function(t) {
								prPresAper.windowEdit({id: K.tmp.data('id')});
								}
							}
						});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }	
				K.clearNoti();
	      } else {
	    	K.clearNoti();
	    	K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se Encontrar&oacute;n Partidas!',type: 'error'});
	        $('#No-Results').show();
	        $('#Results').hide();
	        $( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewPresModi',
			title: 'Nueva Modificaci&oacute;n',
			contentURL: 'pr/pres/editmodi',
			icon: 'ui-icon-plusthick',
			modal:true,
			width: 720,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
						var data = new Object;
							data.num_credito = p.$w.find("[name=num_credito]").val();
							data.ampliaciones = new Array;
							if(p.$w.find('[name=gridampli] .item').length>0){
								for(var i=0; i<p.$w.find('[name=gridampli] .item').length; i++){
									var part = new Object; 
									var tmp = p.$w.find('[name=gridampli] .item').eq(i).data('data');
									part = tmp;
									data.ampliaciones.push(part);
								}
							}else{
								data.ampliaciones=0;
							}
							data.transferencias=0;
							K.sendingInfo();
							p.$w.parent().find('.ui-dialog-buttonpane button').button('disable');
							$.post('pr/pres/save_modi',data,function(){
								K.clearNoti();
								K.closeWindow(p.$w.attr('id'));
								K.notification({title: ciHelper.titleMessages.regiAct,text: 'Partida Presupuestal actualizada con &eacute;xito!'});
								$('#pageWrapperLeft .ui-state-highlight').click();
							});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewPresModi');
				K.block({$element: p.$w});
				var d = new Date();
				p.$w.find('[name=periodo]').html($('#mainPanel').find('[name=periodo]').val());
				//p.$w.find('[name=num_credito]').numeric().spinner({step: 1,min: 0,max: 2100});;
				//p.$w.find('[name=num_credito]').parent().find('.ui-button').css('height','14px');
				$.post('pr/pres/get_num_credito',{periodo:$('#mainPanel').find('[name=periodo]').val()},function(data2){
					if(data2==null){
						p.$w.find('[name=num_credito]').val("1");
					}else{
						p.$w.find('[name=num_credito]').val(data2[0].num_credito + 1);
					}
				},'json');
				p.$w.find('[name=btnAmpli]').click(function(){
					prPresModi.windowNewAmpli({callback: function(data){
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('.gridReference2').hide();
				p.$w.find('[name=btnTrans]').click(function(){
					prPresModi.windowNewTrans({callback: function(data){
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEliAmp]').live('click',function(){
					$(this).closest('.item').remove();
				});
				p.$w.find('[name=btnEliTra]').live('click',function(){
					$(this).closest('.item').remove();
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNewNota: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewPresModi_nota',
			title: 'Nueva Nota Modificatoria',
			contentURL: 'pr/pres/edit_nota',
			icon: 'ui-icon-plusthick',
			modal:true,
			width: 970,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
						var data = new Object;
							data.num_nota = p.$w.find("[name=num_nota]").val();
							data.ampliaciones=0;
							data.transferencias = new Array;
							if(p.$w.find('[name=gridtrans] .item').length>0){
								for(var i=0; i<p.$w.find('[name=gridtrans] .item').length; i++){
									var part2 = new Object; 
									var tmp2 = p.$w.find('[name=gridtrans] .item').eq(i).data('data');
									part2 = tmp2;
									data.transferencias.push(part2);
								}
							}else{
								data.transferencias=0;
							}
							K.sendingInfo();
							p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
							$.post('pr/pres/save_modi',data,function(){
								K.clearNoti();
								K.closeWindow(p.$w.attr('id'));
								K.notification({title: ciHelper.titleMessages.regiAct,text: 'La Nota modificatoria fue creada con &eacute;xito!'});
								$('#pageWrapperLeft .ui-state-highlight').click();
							});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewPresModi_nota');
				K.block({$element: p.$w});
				var d = new Date();
				p.$w.find('[name=periodo]').html($('#mainPanel').find('[name=periodo]').val());
				//p.$w.find('[name=num_credito]').numeric().spinner({step: 1,min: 0,max: 2100});;
				//p.$w.find('[name=num_credito]').parent().find('.ui-button').css('height','14px');
				$.post('pr/pres/get_num_nota',{periodo:$('#mainPanel').find('[name=periodo]').val()},function(data2){
					if(data2==null){
						p.$w.find('[name=num_nota]').val("1");
					}else{
						p.$w.find('[name=num_nota]').val(data2[0].num_nota + 1);
					}
				},'json');
				p.$w.find('[name=btnAmpli]').click(function(){
					prPresModi.windowNewAmpli({callback: function(data){
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('.gridReference2').hide();
				p.$w.find('[name=btnTrans]').click(function(){
					prPresModi.windowNewTrans({callback: function(data){
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEliAmp]').live('click',function(){
					$(this).closest('.item').remove();
				});
				p.$w.find('[name=btnEliTra]').live('click',function(){
					$(this).closest('.item').remove();
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNewAmpli: function(p){
		new K.Modal({
			id: 'windowPresNewAmpli',
			title: 'Nueva Ampliaci&oacute;n',
			contentURL: 'pr/pres/newampli',
			icon: 'ui-icon-search',
			width: 550,
			height: 200,
			buttons: {
				"Guardar": function(){				
					var data = new Object;
					data.importe = p.$w.find('[name=importe]').val();
					if(data.importe==''){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Importe!',type: 'error'});
					}
					data.ref = p.$w.find('[name=ref]').val();
					if(data.ref==''){
						p.$w.find('[name=ref]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Referencias!',type: 'error'});
					}
					var orga = p.$w.find('[name=orga]').data('data');
					if(orga==null){
						p.$w.find('[name=orga]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = new Object;
						data.organizacion._id = orga._id.$id;
						data.organizacion.nomb = orga.nomb;
						data.actividad = orga.actividad;
						data.componente = orga.componente;
						data.actividad._id = data.actividad._id.$id;
						data.componente._id = data.componente._id.$id;
					}
					var clas = p.$w.find('[name=clas]').data('data');
					if(clas==null){
						p.$w.find('[name=clas]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Clasificador!',type: 'error'});
					}else{
						data.clasificador = new Object;
						data.clasificador._id = clas._id.$id;
						data.clasificador.cod = clas.cod;
						data.clasificador.nomb = clas.nomb;
						data.clasificador.tipo = clas.tipo;
					}
					var meta = p.$w.find('[name=meta]').data('data');
					if(meta!=null){
						data.meta = {
							_id:meta._id.$id,
							cod:meta.cod,
							nomb:meta.nomb
						};
					}
					data.fuente = new Object();
					var split = p.$w.find('[name=fuente] :selected').val();
					datafuente = split.split("-");
					data.fuente._id = datafuente[0];
					data.fuente.cod = datafuente[1];
					data.fuente.nomb = datafuente[2];
					data.trabajador = new Object;
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					data.periodo = new Object;
					data.periodo.ano = $('#windowNewPresModi').find('[name=periodo]').html();
					//d = new Date();
					//data.periodo.mes = (parseFloat(d.getMonth()) + 1);
					data.periodo.mes = $('#windowNewPresModi').find('[name=mes] :selected').val();
					//K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					var $row = $('#windowNewPresModi').find('.gridReference:eq(0)').clone();
					$li = $('li',$row);
					$li.eq(0).html(data.organizacion.nomb);
					$li.eq(1).html(data.clasificador.cod);
					$li.eq(2).html(data.fuente.cod);
					$li.eq(3).html(data.importe);
					$row.find('[name=btnEliAmp]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					$row.data('data',data);
					$row.find('a').data('data',data);
					$('#windowNewPresModi').find("[name=gridampli]").append( $row.children() );
					K.closeWindow(p.$w.attr('id'));			
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPresNewAmpli');
				K.block({$element: p.$w});
				p.$w.find('[name=importe]').numeric();
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').val(data.nomb).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnClas]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
						p.$w.find('[name=clas]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMeta]').click(function(){
					prMeta.windowSelect({callback:function(data){
						p.$w.find('[name=meta]').html(data.cod+' '+data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('pr/fuen/all',function(data){
					var $cbo = p.$w.find('[name=fuente]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo.append('<option value="'+id+'-'+cod+'-'+rubro+'" >'+rubro+'</option>');
						}
					}
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNewTrans: function(p){
		new K.Modal({
			id: 'windowPresNewTrans',
			title: 'Nueva Transferencia',
			contentURL: 'pr/pres/newtrans',
			icon: 'ui-icon-search',
			width: 480,
			height: 400,
			buttons: {
				"Guardar": function(){
					var data = new Object;
					data.origen = new Object;
					data.destino = new Object;					
					data.importe = p.$w.find('[name=importe]').val();
					if(data.importe==''){
						p.$w.find('[name=importe]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Importe!',type: 'error'});
					}
					data.ref = p.$w.find('[name=ref]').val();
					if(data.ref==''){
						p.$w.find('[name=ref]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Referencias!',type: 'error'});
					}
					data.fuente = new Object();
					var split = p.$w.find('[name=fuente] :selected').val();
					datafuente = split.split("-");
					data.fuente._id = datafuente[0];
					data.fuente.cod = datafuente[1];
					data.fuente.nomb = datafuente[2];
					var clasorig = p.$w.find('[name=clasorig]').data('data');
					if(clasorig==null){
						p.$w.find('[name=clasorig]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Clasificador!',type: 'error'});
					}else{
						data.origen.clasificador = new Object;
						data.origen.clasificador._id = clasorig._id.$id;
						data.origen.clasificador.cod = clasorig.cod;
						data.origen.clasificador.nomb = clasorig.nomb;
						data.origen.clasificador.tipo = clasorig.tipo;
					}
					var orgaorig = p.$w.find('[name=orgaorig]').data('data');
					if(orgaorig==null){
						p.$w.find('[name=orgaorig]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Organizaci&oacute;n!',type: 'error'});
					}else{
						data.origen.organizacion = new Object;
						data.origen.organizacion._id = orgaorig._id.$id;
						data.origen.organizacion.nomb = orgaorig.nomb;
						data.origen.actividad = orgaorig.actividad;
						data.origen.componente = orgaorig.componente;
						data.origen.actividad._id = data.origen.actividad._id.$id;
						data.origen.componente._id = data.origen.componente._id.$id;
					}
					var metaorig = p.$w.find('[name=meta_orig]').data('data');
					if(metaorig!=null){
						data.origen.meta = {
							_id:metaorig._id.$id,
							cod:metaorig.cod,
							nomb:metaorig.nomb
						};
					}
					var clasdest = p.$w.find('[name=clasdest]').data('data');
					if(clasdest==null){
						p.$w.find('[name=clasdest]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Clasificador!',type: 'error'});
					}else{
						data.destino.clasificador = new Object;
						data.destino.clasificador._id = clasdest._id.$id;
						data.destino.clasificador.cod = clasdest.cod;
						data.destino.clasificador.nomb = clasdest.nomb;
						data.destino.clasificador.tipo = clasdest.tipo;
					}	
					var orgadest = p.$w.find('[name=orgadest]').data('data');
					if(orgadest==null){
						p.$w.find('[name=orgadest]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Organizaci&oacute;n!',type: 'error'});
					}else{
						data.destino.organizacion = new Object;
						data.destino.organizacion._id = orgadest._id.$id;
						data.destino.organizacion.nomb = orgadest.nomb;
						data.destino.actividad = orgadest.actividad;
						data.destino.componente = orgadest.componente;
						data.destino.actividad._id = data.destino.actividad._id.$id;
						data.destino.componente._id = data.destino.componente._id.$id;
					}
					var metadest = p.$w.find('[name=meta_dest]').data('data');
					if(metadest!=null){
						data.destino.meta = {
							_id:metadest._id.$id,
							cod:metadest.cod,
							nomb:metadest.nomb
						};
					}
					data.trabajador = new Object;
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					data.periodo = new Object;
					data.periodo.ano = $('#windowNewPresModi_nota').find('[name=periodo]').html();
					//d = new Date();
					//data.periodo.mes = parseFloat(d.getMonth())+1;
					data.periodo.mes = $('#windowNewPresModi_nota').find('[name=mes] :selected').val();
					//K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					var $row = $('#windowNewPresModi_nota').find('.gridReference2:eq(0)').clone();
					$li = $('li',$row);
					$li.eq(0).html(data.origen.organizacion.nomb);
					$li.eq(1).html(data.origen.clasificador.cod);
					$li.eq(2).html(data.destino.organizacion.nomb);
					$li.eq(3).html(data.destino.clasificador.cod);
					$li.eq(4).html(data.fuente.cod);
					$li.eq(5).html(data.importe);
					$row.find('[name=btnEliTra]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					$row.data('data',data);
					$row.find('a').data('data',data);
					$('#windowNewPresModi_nota').find("[name=gridtrans]").append( $row.children() );
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPresNewTrans');
				K.block({$element: p.$w});
				p.$w.find('[name=importe]').numeric();
				p.$w.find('[name=btnOrgaDest]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orgadest]').val(data.nomb).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnClasDest]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
						p.$w.find('[name=clasdest]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnOrgaOrig]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orgaorig]').val(data.nomb).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnClasOrig]').click(function(){
					prClas.windowSelect({callback: function(data){
						if(data.clasificadores.hijos!=null){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un Clasificador del &uacute;ltimo nivel!!',type: 'error'});
						}else{
						p.$w.find('[name=clasorig]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMeta_orig]').click(function(){
					prMeta.windowSelect({callback:function(data){
						p.$w.find('[name=meta_orig]').html(data.cod+' '+data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMeta_dest]').click(function(){
					prMeta.windowSelect({callback:function(data){
						p.$w.find('[name=meta_dest]').html(data.cod+' '+data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$.post('pr/fuen/all',function(data){
					var $cbo = p.$w.find('[name=fuente]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo.append('<option value="'+id+'-'+cod+'-'+rubro+'" >'+rubro+'</option>');
						}
					}
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	}
};