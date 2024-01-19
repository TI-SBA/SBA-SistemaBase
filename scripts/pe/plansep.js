/*******************************************************************************
Liquidacion por fallecimiento y gastos por sepelio */
pePlanSep = {
	init: function(){
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
				$p.find('[name=pePlanFal]').click(function(){ pePlanFal.init(); });
				$p.find('[name=pePlanBon]').click(function(){ pePlanBon.init(); });
				$p.find('[name=pePlanEnf]').click(function(){ pePlanEnf.init(); });
				$p.find('[name=pePlanQui]').click(function(){ pePlanQui.init(); });
				$p.find('[name=pePlanSep]').click(function(){ pePlanSep.init(); }).addClass('ui-state-highlight');
				$p.find('[name=pePlanVac]').click(function(){ pePlanVac.init(); });
				$p.find('[name=pePlanVei]').click(function(){ pePlanVei.init(); });
				$p.find('[name=pePlanTre]').click(function(){ pePlanTre.init(); });
				$p.find('[name=pePlanMat]').click(function(){ pePlanMat.init(); });
				$p.find('[name^=pePlanBole]').click(function(){
					$.cookie('tipo_contrato',$(this).attr('name').substring(10));
					pePlanBole.init();
				});
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'pePlanSep',
			titleBar: {
				title: 'Subsidio por fallecimiento y gastos de sepelio'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/docs/sepe',
			store: false,
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el criterio de búsqueda' ).width('250');
				$mainPanel.find('[name=obj]').html( 'compensacion(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					pePlanSep.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						pePlanSep.loadData({page: 1,url: 'pe/docs/sepe_lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						pePlanSep.loadData({page: 1,url: 'pe/docs/sepe_search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				pePlanSep.loadData({page: 1,url: 'pe/docs/sepe_lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
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
					$li.eq(0).css('background',pePlanSep.states[result.estado].color).addClass('vtip').attr('title',pePlanSep.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( ciHelper.enti.formatName(result.trabajador) );
					$li.eq(3).html( result.sepelios.beneficiario.nomb );
					$li.eq(4).html( ciHelper.formatMon(result.neto) );
					$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						pePlanSep.windowDetails({id: $(this).data('id')});
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
								pePlanSep.windowDetails({id: K.tmp.data('id')});
							},
							'conMenPeSubs_imp': function(t) {
								K.windowPrint({
									id:'windowPeBoleSepePrint',
									title: "Boleta/Sepelio",
									url: "pe/docs/sepe_print?id="+K.tmp.data('id')
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
	/*windowNew: function(p){
		if(p==null) p = {};
		ciSearch.windowSearchEnti({callback: function(data){
			p.enti = data;
			new K.Window({
				id: 'windowNewSubSepe',
				title: 'Subsidio por Sepelio',
				contentURL: 'pe/docs/vaca_edit',
				icon: 'ui-icon-plusthick',
				width: 800,
				height: 450,
				buttons: {
					"Guardar": function(){
						K.clearNoti();
						var data = {
							trabajador: ciHelper.enti.dbTrabRelAll(p.enti),
							sepelio: {
								beneficiario:{}
							}
						};
						bene = p.$w.find('[name=bene]').data('data');
						p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
						$.post('pe/docs/vaca_save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El documento fue registrado con &eacute;xito!'});
							$('#pageWrapperLeft .ui-state-highlight').click();
						});
					},
					"Cancelar": function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onContentLoaded: function(){
					p.$w = $('#windowNewTrabVaca');
					p.$w.find('label').css('color','gray');
					K.block({$element: p.$w});
					if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
					else p.$w.find('[name=foto]').removeAttr('src');
					p.$w.find('[name=nomb]').data('data',data)
					.html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
					p.$w.find('[name=orga]').html( data.roles.trabajador.organizacion.nomb ).attr('title',data.roles.trabajador.organizacion.nomb).tooltip();
					p.$w.find('[name=cargo]').html( data.roles.trabajador.cargo.nomb );
					if(data.roles.trabajador.nivel!=null) p.$w.find('[name=nivel]').html( data.roles.trabajador.nivel.nomb );
					if(data.roles.trabajador.nivel_carrera!=null) p.$w.find('[name=nivel_carrera]').html( data.roles.trabajador.nivel_carrera.nomb );
					p.$w.find('[name=fecing]').html(ciHelper.dateFormatOnlyDay(data.roles.trabajador.fecing));
					p.$w.find('[name=fecces]').html(ciHelper.dateFormatOnlyDay(data.roles.trabajador.cese.fec));
					p.$w.find('[name=motivo]').html( data.roles.trabajador.cese.motivo );
					p.$w.find('[name=ref]').html( data.roles.trabajador.cese.ref );
					p.$w.find('[name=pension]').html( data.roles.trabajador.pension.nomb );
					$.post('mg/orga/get','id='+data.roles.trabajador.organizacion._id.$id,function(data){
						p.enti.roles.trabajador.cargo.organizacion = data;
						p.enti.roles.trabajador.organizacion = data;
						p.$w.find('[name=fecini]').datepicker().change(function(){ p.loadConc(); });
						p.$w.find('[name=fecfin]').datepicker().change(function(){ p.loadConc(); });
						p.$w.find('[name=tabs]').tabs();
						p.$w.find('#tabs-1,#tabs-2,#tabs-3').css('padding','0px');
						p.loadConc();
						K.unblock({$element: p.$w});
					},'json');
				}
			});
		},filter: [
			{nomb: 'tipo_enti',value: 'P'},
			{nomb: 'roles.trabajador',value: {$exists: true}},
			{nomb: 'roles.trabajador.fecing',value: {$exists: true}},
			{nomb: 'roles.trabajador.cese',value: {$exists: true}},
		]});
	},*/
	windowNew: function(p){
		if(p==null) p = new Object;
		p.calcFall = function(){
			var remu = parseFloat(p.$w.find('[name=remu]').html());
			if(p.$w.find('[name=check_difu]').is(':checked')){
				p.$w.find('[name=subs_fall]').html(K.round(remu*4,2));
			}else{
				p.$w.find('[name=subs_fall]').html(K.round(remu*2,2));
			}
			p.calcSepe();
			p.calcTot();
		};
		p.calcSepe = function(){
			var remu = parseFloat(p.$w.find('[name=remu]').html());
			if(p.$w.find('[name=sep_check]').is(':checked')){
				p.$w.find('[name=subs_sepe]').html(K.round(remu*2,2));
			}else{
				p.$w.find('[name=subs_sepe]').html("0.00");
			}
		};
		p.calcTot = function(){
			var fall = parseFloat(p.$w.find('[name=subs_fall]').html());
			var sepe = parseFloat(p.$w.find('[name=subs_sepe]').html());
			p.$w.find('[name=total_pagar]').html(K.round(fall+sepe,2));
		};
		new K.Window({
			id: 'windowPeNewSepe',
			title: 'Nuevo Subsidio por gastos de sepelio',
			contentURL: 'pe/docs/sep_edit',
			icon: 'ui-icon-plusthick',
			width: 650,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.estado = "R";
					trab = p.$w.find('[name=trab]').data('data');
					if(trab!=null){
						data.trabajador = ciHelper.enti.dbTrabRel(trab);
						data.contrato = {
							_id: trab.roles.trabajador.contrato._id.$id,
							nomb: trab.roles.trabajador.contrato.nomb,
							cod: trab.roles.trabajador.contrato.cod
						};
					}else{
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar un Trabajador!',type: 'error'});
						return p.$w.find('[name=btnSelectResp]').click();
					}					
					data.sepelios = new Object;
					data.sepelios.beneficiario = {
						nomb:p.$w.find('[name=bene_nomb]').val(),
						dni:p.$w.find('[name=bene_dni]').val(),
						parentesco:p.$w.find('[name=bene_pare]').val()
					}
					if(!p.$w.find('[name=check_difu]').is(':checked')){
						data.sepelios.difunto = {
							nomb:p.$w.find('[name=difu_nomb]').val(),
							dni:p.$w.find('[name=difu_dni]').val()
						};
					}
					data.sepelios.fecfall = p.$w.find('[name=difu_fec]').val();
					data.sepelios.sepelio = (p.$w.find('[name=sep_check]').is(':checked'))?"1":"0";
					data.sepelios.remuneracion = p.$w.find('[name=remu]').html();
					data.sepelios.pago_fall = p.$w.find('[name=subs_fall]').html();
					data.sepelios.pago_sepe = p.$w.find('[name=subs_sepe]').html();
					data.total = p.$w.find('[name=total_pagar]').html();
					data.neto = data.total;
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/docs/sepe_save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Liquidacion fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPeNewSepe');
				K.block({$element: p.$w});
				p.$w.find('[name=btnSelectTrab]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: function(data){
						$.post('pe/docs/last_bole',{_id:data._id.$id},function(rpta){
							if(rpta.items!=null){
								p.$w.find('[name=trab]').html(ciHelper.enti.formatName(data)).data('data',data);
								p.$w.find('[name=trab_dni]').html( data.docident[0].num );
								if(data.roles.trabajador.nivel)
									p.$w.find('[name=trab_niv]').html( data.roles.trabajador.nivel.nomb );								
								p.$w.find('[name=remu]').html(K.round(rpta.items[0].total_pago,2));
								p.calcFall();
							}else{
								K.notification({title: ciHelper.titleMessages.infoReq,text: 'El trabajador seleccionado no cuenta con pagos anteriores!',type: 'error'});
							}
						},'json');
						
					},filter: [
						{nomb: 'tipo_enti',value: 'P'},
						{nomb: 'roles.trabajador',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
				p.$w.find('[name=check_difu]').click(function(){
					if($(this).is(':checked')){
						p.$w.find('[name=difunto]').hide();
					}else{
						p.$w.find('[name=difunto]').show();
					}
					p.calcFall();
				});
				p.$w.find('[name=sep_check]').click(function(){
					p.calcFall();
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPeDetailsSepe'+p.id,
			title: 'Ver Subsidio por gastos de sepelio',
			contentURL: 'pe/docs/sep_edit',
			icon: 'ui-icon-plusthick',
			width: 650,
			height: 300,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPeDetailsSepe'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnSelectTrab]').remove();
				$.post('pe/docs/get',{id:p.id},function(data){
					p.$w.find('[name=trab]').html(ciHelper.enti.formatName(data.trabajador));
					if(data.trabajador.docident)
						p.$w.find('[name=trab_dni]').html( data.trabajador.docident[0].num );
					if(data.trabajador.nivel)
						p.$w.find('[name=trab_niv]').html( data.trabajador.nivel.nomb );
					p.$w.find('[name=bene_nomb]').replaceWith(data.sepelios.beneficiario.nomb);
					p.$w.find('[name=bene_dni]').replaceWith(data.sepelios.beneficiario.dni);
					p.$w.find('[name=bene_pare]').replaceWith(data.sepelios.beneficiario.parentesco);
					if(data.sepelios.difunto){
						p.$w.find('[name=difu_nomb]').replaceWith(data.sepelios.difunto.nomb);
						p.$w.find('[name=difu_dni]').replaceWith(data.sepelios.difunto.dni);
						p.$w.find('[name=check_difu]').attr('disabled','disabled');
					}else{
						p.$w.find('[name=check_difu]').attr('checked','checked').attr('disabled','disabled');
						p.$w.find('[name=difunto]').hide();
					}
					p.$w.find('[name=difu_fec]').replaceWith(data.sepelios.fecfall);
					p.$w.find('[name=sep_check]').attr('disabled','disabled');
					if(data.sepelios.sepelio==true){
						p.$w.find('[name=sep_check]').attr('checked','checked');
					}
					p.$w.find('[name=remu]').html(data.sepelios.remuneracion);
					p.$w.find('[name=subs_fall]').html(data.sepelios.pago_fall);
					p.$w.find('[name=subs_sepe]').html(data.sepelios.pago_sepe);
					p.$w.find('[name=total_pagar]').html(data.neto);
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	}
};