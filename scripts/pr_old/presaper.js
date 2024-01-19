/*******************************************************************************
Cuentas contables */
prPresAper = {
	origens: ["CN","PAP","CN"],
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
				$p.find('[name=prPresAper]').click(function(){ prPresAper.init(); }).addClass('ui-state-highlight');
				$p.find('[name=prPresModi]').click(function(){ prPresModi.init(); });
				$p.find('[name=prPresModi_Nota]').click(function(){ prPresModiNota.init(); });
				$p.find('[name=prPresModi_Cred]').click(function(){ prPresModiCred.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pr',
			action: 'prPresAper',
			titleBar: {
				title: 'Presupuesto Institucional de Apertura'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/pres/index_aper',
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
				$mainPanel.find('[name=btnExportar]').click(function(){
					if($mainPanel.find('.item').length>0){
						var params = new Object;
						params.tipo = $('#mainPanel').find('[name=FilTipo] :selected').val();
						params.periodo = $('#mainPanel').find('[name=periodo]').val();
						params.mes = $('#mainPanel').find('[name=mes] :selected').val();
						params.prog = $('#mainPanel').find('[name=prog] :selected').val();
						params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
						params.clasificador = $('#mainPanel').find('[name=clasificador]').val();
						params.page_rows = 99999;
					    params.page = 1;
						window.open("pr/pres/aper_export?"+$.param(params));
					}else{
						return K.notification({title: 'Error',text: 'No existen partidas para generar el reporte!!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-extlink'}});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					prPresAper.windowNew({tipo: $('#mainPanel').find('[name=rbtnTipo]:checked').val()});
				}).button({icons: {primary: 'ui-icon-plusthick'}});				
				$mainPanel.find('[name=btnImprimir]').click(function(){
					if($mainPanel.find('.item').length>0){
						var params = {
							tipo: $('#mainPanel [name=FilTipo] :selected').val(),
							periodo: $('#mainPanel [name=periodo]').val(),
							mes: $('#mainPanel').find('[name=mes] :selected').val(),
							prog: $('#mainPanel').find('[name=prog] :selected').val(),
							organizacion: $('#mainPanel').find('[name=organizacion]').val(),
							clasificador: $('#mainPanel').find('[name=clasificador]').val(),
							organomb: $('#mainPanel').find('[name=organomb]').html(),
							clasnomb: $('#mainPanel').find('[name=clasnomb]').html(),
							page_rows: 99999,
							page: 1
						}, url = 'pr/pres/aper_print?'+$.param(params);
						K.windowPrint({
							id:'windowAperPartPrint',
							title: "Reporte / Informe",
							url: url
						});
					}else{
						return K.notification({title: 'Error',text: 'No existen partidas para generar el reporte!!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-print'}});
				$mainPanel.find('[name=organomb]').html("Organizaci&oacute;n");
				$mainPanel.find('[name=clasnomb]').html("");
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100}).change(function(){
					$('#mainPanel .gridBody').empty();
					prPresAper.loadData({
						page: 1,
						url: 'pr/pres/aper_lista'
					});
			    });			
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear()); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					prPresAper.loadData({
						page: 1,
						url: 'pr/pres/aper_lista'
					}); 
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					prPresAper.loadData({
						page: 1,
						url: 'pr/pres/aper_lista'
					});
			    });
				$mainPanel.find('[name=FilClas]').buttonset();
				$mainPanel.find('#rbtnClasSelect').click(function(){
					prClas.windowSelect({callback: function(data){
						$mainPanel.find('[name=clasificador]').val(data._id.$id);
						$mainPanel.find('[name=clasnomb]').html(data.cod);
						$('#mainPanel .gridBody').empty();
						prPresAper.loadData({
							page: 1,
							url: 'pr/pres/aper_lista'
						});

					}});
				});
				$mainPanel.find('#rbtnClasX').click(function(){
						$mainPanel.find('[name=clasificador]').val("");
						$mainPanel.find('[name=clasnomb]').html("");
						$('#mainPanel .gridBody').empty();
						prPresAper.loadData({
							page: 1,
							url: 'pr/pres/aper_lista'
						});
				});
				//ORGANIZACION
				$mainPanel.find('[name=FilOrga]').buttonset();
				$mainPanel.find('#rbtnOrgaSelect').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$mainPanel.find('[name=organizacion]').val(data._id.$id);
						$mainPanel.find('[name=organomb]').html(data.nomb);
						$('#mainPanel .gridBody').empty();
						prPresAper.loadData({
							page: 1,
							url: 'pr/pres/aper_lista'
						});
					}});
				});
				$mainPanel.find('#rbtnOrgaX').click(function(){
						$mainPanel.find('[name=organizacion]').val("");
						$mainPanel.find('[name=organomb]').html("Organizaci&oacute;n");
						$('#mainPanel .gridBody').empty();
						prPresAper.loadData({
							page: 1,
							url: 'pr/pres/aper_lista'
						});
				});
				///

				$mainPanel.find('[name=FilTipo]').change(function(){
					$('#mainPanel .gridBody').empty();
					prPresAper.loadData({
						page: 1,
						url: 'pr/pres/aper_lista'
					});
			    });
				prPresAper.loadData({
					page: 1,
					url: 'pr/pres/aper_lista'
				});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.tipo = $('#mainPanel').find('[name=FilTipo] :selected').val();
		params.periodo = $('#mainPanel').find('[name=periodo]').val();
		params.mes = $('#mainPanel').find('[name=mes] :selected').val();
		params.prog = $('#mainPanel').find('[name=prog] :selected').val();
		params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
		params.programa = $('#mainPanel').find('[name=programa]').val();
		params.clasificador = $('#mainPanel').find('[name=clasificador]').val();
		params.page_rows = 999999999999;
	    params.page = (params.page) ? params.page : 1;
	    $.sum = function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	    };
	    K.loading();
	    $.post(params.url, params, function(data){
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
								var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								var dt = K.tmp.data('data');
								if(dt.clasificadores.hijos)excep+='#conMenPrPresAper_ediPart';
								$(excep+',#conMenPrPresAper_eliPart',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPrPresAper_ediPart': function(t) {
									prPresAper.windowEdit({id: K.tmp.data('id'),cod: K.tmp.find('li:eq(1)').html(),nomb:K.tmp.find('li:eq(2)').html()});
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
			id: 'windowNewAperPart',
			title: 'Nuevo Partida',
			modal:true,
			contentURL: 'pr/pres/aperpartnew',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
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
					var prog = p.$w.find('[name=prog]').data('data');
					if(prog!=null){
						data.prog = {
							_id:prog._id.$id,
							actividad:prog.actividad,
							componente:prog.componente
						};
					}
					data.fuente = new Object();
					var split = p.$w.find('[name=fuente] :selected').val();
					datafuente = split.split("-");
					data.fuente._id = datafuente[0];
					data.fuente.cod = datafuente[1];
					data.fuente.nomb = datafuente[2];
					//data.trabajador = new Object;
					//data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					data.periodo = new Object;
					data.periodo.ano = p.$w.find('[name=periodo]').html();
					data.periodo.mes = p.$w.find('[name=mes] :selected').val();
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/pres/save_v1',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El clasificador de gasto fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewAperPart');
				K.block({$element: p.$w});
				p.$w.find('[name=importe]').numeric();
				p.$w.find('[name=periodo]').html($('#mainPanel').find('[name=periodo]').val());
				/*
				//ORGANIZACION
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').val(data.nomb).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				*/
				/*
				p.$w.find("[name=btnDire]").click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=dire]').html(data.nomb).data('data',data);
						p.$w.find('[name=id_dire]').html(data._id.$id).data('data',data);
					},bootstrap: true});
				});
				*/
				p.$w.find('[name=btnActi]').click(function(){
					prActi.windowSelectComp({callback: function(data){
						if(data.nivel=="AC"){
							return K.notification({title: 'Error de Selecci&oacute;n',text: 'Usted solo puede seleccionar un componente!!',type: 'error'});
						}else{
						p.$w.find('[name=acti]').val(data.cod).data('data',data);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						}
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
				/*--------------------PROGRAMA--------------------*/
				p.$w.find('[name=btnProg]').click(function(){
					mgProg.windowSelect({callback: function(data){
						p.$w.find('[name=prog]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				/*-----------------------------------------------*/
				/*---------------------META---------------------*/
				p.$w.find('[name=btnMeta]').click(function(){
					prMeta.windowSelect({callback:function(data){
						p.$w.find('[name=meta]').html(data.cod+' '+data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				/*----------------------------------------------*/
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
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditAperPart'+p.id,
			title: 'Editar Partida :'+p.cod+' - '+p.nomb,
			modal:true,
			contentURL: 'pr/pres/aperpartedit',
			icon: 'ui-icon-plusthick',
			width: 880,
			height: 250,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.partidasq = new Array;
					if(p.$w.find('.gridBody .item').length>0){
					for(var i=0; i<p.$w.find('.gridBody .item').length; i++){
						var part = new Object; 
						var tmp = p.$w.find('.gridBody .item').eq(i).data('data');
						part = tmp._id.$id;
						data.partidasq.push(part);
					}
					}else{
					data.partidasq.push("1");
					}
					data.partidase = new Array;
					if(p.$w.find('[name=saveData]').data('list').length>0){
						for(var i=0; i<p.$w.find('[name=saveData]').data('list').length; i++){
							var parte = new Object; 
							var tmp = p.$w.find('[name=saveData]').data('list')[i];
							parte = tmp._id.$id;
							data.partidase.push(parte);
						}
					}else{
						return K.notification({title: 'Error',text: 'No existen partidas a eliminar!!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pr/pres/aper_update',data,function(){
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
				p.$w = $('#windowEditAperPart'+p.id);
				K.block({$element: p.$w});
				params = new Object();
				params.id = p.id;
				params.tipo = $('#mainPanel').find('[name=FilTipo] :selected').val();
				params.periodo = $('#mainPanel').find('[name=periodo]').val();
				params.mes = $('#mainPanel').find('[name=mes] :selected').val();
				params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
				params.clasificador = $('#mainPanel').find('[name=clasificador]').val();
				$.post('pr/pres/all_partidas_filter',params,function(data){
					if(data!=null){
						for (i=0; i < data.length; i++) {
							var result = data[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( prPresAper.origens[result.origen].descr );
							$li.eq(1).html( ciHelper.dateFormat(result.fecreg));
							$li.eq(2).html( result.importe );
							$li.eq(3).html( result.fuente.nomb );
							$li.eq(4).html( result.ref );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).dblclick(function(){
								p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
							}).data('data',result);
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find(".gridBody").append( $row.children() );
						}
						p.$w.find("[name=saveData]").data('list',data);
					}else{
						return K.notification({title: 'Error',text: 'No se encontraron partidas a eliminar!!',type: 'error'});
						K.closeWindow(p.$w.attr('id'));
					}	
				},'json');
				var partidas=new Array;
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
				});
				K.unblock({$element: p.$w});
			}
		});
	}	
};
prPresAper.origens['CN'] = {
		descr: 'Cuadro de Necesidades'
	};
prPresAper.origens['PAP'] = {
		descr: 'Personal'
	};
prPresAper.origens['PM'] = {
		descr: 'Partida Manual'
	};