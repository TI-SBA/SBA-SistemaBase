/*******************************************************************************
boletas de maternidad */
pePlanMat = {
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
				$p.find('[name=pePlanFal]').click(function(){ pePlanFal.init(); });
				$p.find('[name=pePlanBon]').click(function(){ pePlanBon.init(); });
				$p.find('[name=pePlanEnf]').click(function(){ pePlanEnf.init(); });
				$p.find('[name=pePlanSep]').click(function(){ pePlanSep.init(); });
				$p.find('[name=pePlanVac]').click(function(){ pePlanVac.init(); });
				$p.find('[name=pePlanVei]').click(function(){ pePlanVei.init(); });
				$p.find('[name=pePlanTre]').click(function(){ pePlanTre.init(); });
				$p.find('[name=pePlanMat]').click(function(){ pePlanMat.init(); }).addClass('ui-state-highlight');
				$p.find('[name^=pePlanBole]').click(function(){
					$.cookie('tipo_contrato',$(this).attr('name').substring(10));
					pePlanBole.init();
				});
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'pePlanMat',
			titleBar: {
				title: 'Reembolso Subsidio - Maternidad'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/bole',
			onContentLoaded: function(){
				$('#pageWrapperLeft [name=pePlanMat]').addClass('ui-state-highlight');
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
					pePlanMat.windowPagar();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					pePlanMat.windowNew();
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
						pePlanMat.loadData({page: 1,url: 'pe/docs/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						pePlanMat.loadData({page: 1,url: 'pe/docs/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				pePlanMat.loadData({page: 1,url: 'pe/docs/lista'});
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
			doc: 'maternidad',
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
					$li.eq(0).css('background',pePlanMat.states[result.estado].color).addClass('vtip').attr('title',pePlanMat.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( 'N&deg;'+result.cod );
					$li.eq(3).html( ciHelper.enti.formatName(result.trabajador) );
					if(result.trabajador.roles.trabajador.cargo._id!=null)
						$li.eq(4).html( result.trabajador.roles.trabajador.cargo.nomb );
					else
						$li.eq(4).html( result.trabajador.roles.trabajador.cargo.funcion );
					$li.eq(5).html( ciHelper.formatMon(result.maternidad.total_previo2) );
					$li.eq(6).html( ciHelper.formatMon(result.maternidad.total_descuentos) );
					$li.eq(7).html( ciHelper.formatMon(result.maternidad.total_pagar) );
					$li.eq(8).html( ciHelper.formatMon(0) );
					$li.eq(9).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						pePlanMat.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
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
								pePlanMat.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeSubs_imp': function(t) {
								K.windowPrint({
									id:'windowPeBolePrint',
									title: "Boleta",
									url: "pe/docs/mat_print?id="+K.tmp.data('id')
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
						pePlanMat.loadData(params);
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
		new K.Window({
			id: 'windowNewMat',
			title: 'Reembolso Subsidio - Maternidad',
			contentURL: 'pe/docs/mat_edit',
			store: false,
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						maternidad: {
							refs: [],
							meses: [],
							descuentos: []
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
					for(var i=0,j=p.$w.find('fieldset:eq(2) .gridCont:eq(0) .item').length; i<j; i++){
						var $row = p.$w.find('fieldset:eq(2) .gridCont:eq(0) .item').eq(i);
						data.maternidad.meses.push($row.data('data'));
					}
					for(var i=0,j=p.$w.find('.gridCont:eq(0) .item').length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(0) .item').eq(i);
						if($row.find('[name=descr]').val()!=''||$row.find('[name=num1]').val()!=''){
							if($row.find('[name=descr]').val()==''){
								$row.find('[name=descr]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar una descripci&oacute;n de la referencia!',
									type: 'error'
								});
							}
							if($row.find('[name=num1]').val()==''){
								$row.find('[name=num1]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un total de d&iacute;as de la referencia!',
									type: 'error'
								});
							}
							data.maternidad.refs.push({
								descr: $row.find('[name=descr]').val(),
								num: $row.find('[name=num1]').val()
							});
						}
					}
					for(var i=0,j=p.$w.find('.gridCont:eq(2) .item').length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(2) .item').eq(i);
						if($row.find('[name=descr]').val()!=''||$row.find('[name=num]').val()!=''){
							if($row.find('[name=descr]').val()==''){
								$row.find('[name=descr]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar una descripci&oacute;n del descuento!',
									type: 'error'
								});
							}
							if($row.find('[name=num]').val()==''){
								$row.find('[name=num]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un total de descuento!',
									type: 'error'
								});
							}
							data.maternidad.descuentos.push({
								descr: $row.find('[name=descr]').val(),
								num: $row.find('[name=num]').val()
							});
						}
					}
					$.extend(data.maternidad,{
						total_meses: parseFloat(p.$w.find('[name=total_remus]').data('data')),
						promedio_diario: p.$w.find('[name=subs_dia]').html(),
						dias_subsidiados: parseInt(p.$w.find('.gridCont:eq(0) .result').data('data')),
						redondeo1: p.$w.find('[name=redondeo]').html(),
						total_previo1: p.$w.find('[name=total]').html(),
						total_descuentos: parseFloat(p.$w.find('.gridCont:eq(2) .result').data('data')),
						total_previo2: p.$w.find('[name=total_pagar_prev]').html(),
						redondeo2: p.$w.find('[name=redondeo_prev]').html(),
						total_pagar: p.$w.find('[name=total_pagar]').html(),
						primer_total: p.$w.find('[name=subs_total]').data('data')
					});
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
				p.$w.find('[name=btnEli2]').die('click');
				p.$w.find('[name=btnAgr2]').die('click');
				p.$w.find('[name=btnEli1]').die('click');
				p.$w.find('[name=btnAgr1]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewMat');
				K.block({$element: p.$w});
				/*
				 * Se crean las grillas de certificados
				 */
				var $grid = p.$w.find('.gridCont:eq(0)'),
				$row = $grid.find('.gridReference').clone();
				$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" />');
				$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num1" />');
				$row.find('li:eq(2)').html('<button name="btnEli1">Eliminar</button><button name="btnAgr1">Agregar</button>');
				$row.find('[name=num1]').keyup(function(){
					p.sum();
				}).numeric();
				$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.find('[name=btnAgr1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.wrapInner('<a class="item">');
				$grid.find('.gridBody').append($row.children());
				var $grid = p.$w.find('.gridCont:eq(2)'),
				$row = $grid.find('.gridReference').clone();
				$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" />');
				$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num" />');
				$row.find('li:eq(2)').html('<button name="btnEli2">Eliminar</button><button name="btnAgr2">Agregar</button>');
				$row.find('[name=num]').keyup(function(){
					p.sum();
				}).numeric();
				$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.find('[name=btnAgr2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.wrapInner('<a class="item">');
				$grid.find('.gridBody').append($row.children());
				/*
				 * botones de las grillas
				 */
				p.$w.find('[name=btnEli2]').live('click',function(){
					var $grid = $(this).closest('.gridCont');
					$(this).closest('.item').remove();
					if($grid.find('.gridBody .item').length==0){
						$grid.append('<button name="btnAgr2"></button>');
						$grid.find('[name=btnAgr2]').click();
					}else{
						if($grid.find('.gridBody [name=btnAgr2]').length==0){
							$grid.find('.item:last li:eq(2)').append('<button name="btnAgr2">Agregar</button>');
							$grid.find('[name=btnAgr2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
					}
					p.sum();
				});
				p.$w.find('[name=btnAgr2]').live('click',function(){
					var $grid = $(this).closest('.gridCont'),
					$row = $grid.find('.gridReference').clone();
					$grid.find('[name=btnAgr2]').remove();
					$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" />');
					$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num" />');
					$row.find('li:eq(2)').html('<button name="btnEli2">Eliminar</button><button name="btnAgr2">Agregar</button>');
					$row.find('[name=num]').keyup(function(){
						p.sum();
					}).numeric();
					$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.find('[name=btnAgr2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					$row.wrapInner('<a class="item">');
					$grid.find('.gridBody').append($row.children());
					p.sum();
				});
				p.$w.find('[name=btnEli1]').live('click',function(){
					var $grid = $(this).closest('.gridCont');
					$(this).closest('.item').remove();
					if($grid.find('.gridBody .item').length==0){
						$grid.append('<button name="btnAgr1"></button>');
						$grid.find('[name=btnAgr1]').click();
					}else{
						if($grid.find('.gridBody [name=btnAgr]').length==0){
							$grid.find('.item:last li:eq(2)').append('<button name="btnAgr1">Agregar</button>');
							$grid.find('[name=btnAgr1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
					}
					p.sum();
				});
				p.$w.find('[name=btnAgr1]').live('click',function(){
					var $grid = $(this).closest('.gridCont'),
					$row = $grid.find('.gridReference').clone();
					$grid.find('[name=btnAgr1]').remove();
					$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" />');
					$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num1" />');
					$row.find('li:eq(2)').html('<button name="btnEli1">Eliminar</button><button name="btnAgr1">Agregar</button>');
					$row.find('[name=num1]').keyup(function(){
						p.sum();
					}).numeric();
					$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.find('[name=btnAgr1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					$row.wrapInner('<a class="item">');
					$grid.find('.gridBody').append($row.children());
					p.sum();
				});
				p.sum();
				/*
				 * Se selecciona el trabajador
				 */
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
						var $grid = p.$w.find('.gridCont:eq(2)'),
						$row = $grid.find('.gridReference').clone();
						$grid.find('.gridBody').empty();
						$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" value="SNP 13%"/>');
						$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num" />');
						$row.find('li:eq(2)').html('<button name="btnEli2">Eliminar</button><button name="btnAgr2">Agregar</button>');
						$row.find('[name=num]').keyup(function(){
							p.sum();
						}).numeric();
						$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgr2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						$grid.find('.gridBody').append($row.children());
						$row = $grid.find('.gridReference').clone();
						$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" value="Tardanzas"/>');
						$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num" />');
						$row.find('li:eq(2)').html('<button name="btnEli2">Eliminar</button><button name="btnAgr2">Agregar</button>');
						$row.find('[name=num]').keyup(function(){
							p.sum();
						}).numeric();
						$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgr2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						$grid.find('.gridBody').append($row.children());
						$row = $grid.find('.gridReference').clone();
						$row.find('li:eq(0)').html('<input type="text" size="55" name="descr" value="ITF (0.005%)"/>');
						$row.find('li:eq(1)').html('<input type="text" size="4" value="0" name="num" />');
						$row.find('li:eq(2)').html('<button name="btnEli2">Eliminar</button><button name="btnAgr2">Agregar</button>');
						$row.find('[name=num]').keyup(function(){
							p.sum();
						}).numeric();
						$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.find('[name=btnAgr2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.wrapInner('<a class="item">');
						$grid.find('.gridBody').append($row.children());
						p.sum();
						$.post('mg/orga/get','id='+data.roles.trabajador.organizacion._id.$id,function(data){
							if(data.actividad!=null) p.$w.find('[name=actividad]').html( data.actividad.cod );
							if(data.componente!=null) p.$w.find('[name=componente]').html( data.componente.cod );
						},'json');
						$.post('pe/docs/get_enfermedad',{_id: data._id.$id},function(data){
							if(data==null){
								K.unblock({$element: p.$w});
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'El trabajador seleccionado no cuenta con boletas de pago dentro de los &uacute;ltimos 12 meses!',
									type: 'error'
								});
							}
							var $grid = p.$w.find('fieldset:eq(2) .gridCont:eq(0)'),
							mes = ciHelper.date.getMonth(),
							ano = ciHelper.date.getYear(),
							remus = [];
							for(var i=0,j=12; i<j; i++){
								mes_t = mes-i;
								ano_t = ano;
								if(mes_t<=0){
									mes_t = 12+mes_t;
									ano_t--;
								}
								remus.push({
									per: ciHelper.meses[parseInt(mes_t)-1]+'-'+ano_t,
									total: 0,
									mes: mes_t,
									ano: ano_t
								});
							}
							for(var i=0,j=data.length; i<j; i++){
								for(var ii=0; ii<12; ii++){
									if(parseInt(data[i].periodo.ano)==remus[ii].ano){
										if(parseInt(data[i].periodo.mes)==remus[ii].mes){
											remus[ii].total = parseFloat(data[i].total);
										}
									}
								}
							}
							var tmp = 0;
							for(var ii=0; ii<12; ii++){
								if(remus[ii].total!=0)
									tmp = remus[ii].total;
								else
									remus[ii].total = tmp;
							}
							var remus_total = 0;
							for(var i=11; i>=0; i--){
								var $row = $grid.find('.gridReference').clone();
								$row.find('li:eq(0)').html(remus[i].per);
								$row.find('li:eq(1)').html(remus[i].total);
								$row.wrapInner('<a class="item">');
								$row.find('.item').data('data',remus[i]);
								$grid.find('.gridBody').append($row.children());
								remus_total += remus[i].total;
							}
							p.$w.find('[name=total_remus]').html(remus_total).data('data',remus_total);
							p.calc();
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
		$.extend(p,{
			sum: function(){
				var $grid = p.$w.find('.gridCont:eq(0)'),
				total = 0;
				for(var i=0,j=$grid.find('[name=num1]').length; i<j; i++){
					total += parseInt($grid.find('[name=num1]').eq(i).val());
				}
				$grid.find('.result').remove();
				var $row = $grid.find('.gridReference').clone();
				$row.find('li:eq(2)').html(total);
				$row.wrapInner('<a class="result">');
				$row.find('.result').data('data',total);
				$grid.find('.gridBody').append($row.children());
				var $grid = p.$w.find('.gridCont:eq(2)'),
				total = 0;
				for(var i=0,j=$grid.find('[name=num]').length; i<j; i++){
					total += parseFloat($grid.find('[name=num]').eq(i).val());
				}
				$grid.find('.result').remove();
				var $row = $grid.find('.gridReference').clone();
				$row.find('li:eq(1)').html('Total Descuentos');
				$row.find('li:eq(2)').html(total);
				$row.wrapInner('<a class="result">');
				$row.find('.result').data('data',total);
				$grid.find('.gridBody').append($row.children());
				p.calc();
			},
			calc: function(){
				if(p.$w.find('[name=nomb]').data('data')==null){
					p.$w.find('[name=btnSelEnt]').click();
					K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe seleccionar un trabajador!',
						type: 'error'
					});
				}
				if(p.$w.find('.gridCont:eq(2) .result').data('data')==null){
					K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe ingresar al menos una cantidad subsidiada!',
						type: 'error'
					});
				}
				var total_remus = p.$w.find('[name=total_remus]').data('data'),
				dias = parseInt(p.$w.find('.gridCont:eq(0) .result').data('data')),
				dias = (dias!=null?dias:0),
				total_dia = total_remus/30/12,
				total_subs = total_dia * dias,
				subs_total_prev = total_subs,
				total = parseInt(subs_total_prev),
				redondeo = subs_total_prev - total,
				desc_total = parseFloat(p.$w.find('.gridCont:eq(2) .result').data('data')),
				total_pagar_prev = total - desc_total,
				total_pagar = parseInt(total_pagar_prev),
				redondeo_prev = total_pagar_prev - total_pagar;
				p.$w.find('[name=divi_1]').html('12 meses X 30');
				p.$w.find('[name=total_remus_div]').html(K.round(total_remus/12,2));
				p.$w.find('[name=divi_2]').html('30');
				p.$w.find('[name=total_dia]').html(K.round(total_remus/30/12,2));
				p.$w.find('[name=subs_dia]').html(K.round(total_dia,2));
				p.$w.find('[name=subs_dias]').html(dias);
				p.$w.find('[name=subs_total]').html(K.round(total_subs,2)).data('data',total_subs);
				p.$w.find('[name=subs_total_prev]').html(K.round(subs_total_prev,2));
				p.$w.find('[name=total]').html(total);
				p.$w.find('[name=redondeo]').html(K.round(redondeo,2));
				p.$w.find('[name=total_pagar_prev]').html(total_pagar_prev);
				p.$w.find('[name=redondeo_prev]').html(K.round(redondeo_prev,2));
				p.$w.find('[name=total_pagar]').html(total_pagar);
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsMat',
			title: 'Reembolso Subsidio - Maternidad',
			contentURL: 'pe/docs/mat_details',
			store: false,
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Guardar": function(){
					for(var i=0,j=p.$w.find('.gridCont:eq(2) .item').length; i<j; i++){
						var $row = p.$w.find('.gridCont:eq(2) .item').eq(i);
						if($row.find('[name=descr]').val()!=''||$row.find('[name=num]').val()!=''){
							if($row.find('[name=descr]').val()==''){
								$row.find('[name=descr]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar una descripci&oacute;n del descuento!',
									type: 'error'
								});
							}
							if($row.find('[name=num]').val()==''){
								$row.find('[name=num]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar un total de descuento!',
									type: 'error'
								});
							}
							data.maternidad.descuentos.push({
								descr: $row.find('[name=descr]').val(),
								num: $row.find('[name=num]').val()
							});
						}
					}
				},
				"Cerrrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsMat');
				K.block({$element: p.$w});
				$.post('pe/docs/get',{id: p.id},function(data){
					if(data.trabajador.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.trabajador.imagen.$id);
					else p.$w.find('[name=foto]').removeAttr('src');
					p.$w.find('[name=nomb]').html( ciHelper.enti.formatName(data.trabajador) )
						.attr('title',ciHelper.enti.formatName(data.trabajador)).tooltip();
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
					$.post('mg/orga/get','id='+data.trabajador.roles.trabajador.cargo.organizacion._id.$id,function(data){
						if(data.actividad!=null) p.$w.find('[name=actividad]').html( data.actividad.cod );
						if(data.componente!=null) p.$w.find('[name=componente]').html( data.componente.cod );
					},'json');
					var dias = 0;
					for(var i=0,j=data.maternidad.refs.length; i<j; i++){
						var $grid = p.$w.find('fieldset:eq(1) .gridCont:eq(0)'),
						$row = $grid.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.maternidad.refs[i].descr);
						$row.find('li:eq(1)').html(data.maternidad.refs[i].num);
						$row.wrapInner('<a class="item">');
						$grid.find('.gridBody').append($row.children());
						dias += parseInt(data.maternidad.refs[i].num);
					}
					for(var i=0,j=data.maternidad.meses.length; i<j; i++){
						var $grid = p.$w.find('fieldset:eq(2) .gridCont:eq(0)'),
						$row = $grid.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.maternidad.meses[i].per);
						$row.find('li:eq(1)').html(data.maternidad.meses[i].total);
						$row.wrapInner('<a class="item">');
						$grid.find('.gridBody').append($row.children());
					}
					for(var i=0,j=data.maternidad.descuentos.length; i<j; i++){
						var $grid = p.$w.find('fieldset:eq(2) .gridCont:eq(1)'),
						$row = $grid.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.maternidad.descuentos[i].descr);
						$row.find('li:eq(1)').html(data.maternidad.descuentos[i].num);
						$row.wrapInner('<a class="item">');
						$grid.find('.gridBody').append($row.children());
					}
					p.$w.find('[name=divi_1]').html('12 meses X 30');
					p.$w.find('[name=total_remus_div]').html(K.round(parseFloat(data.maternidad.total_meses)/12,2));
					p.$w.find('[name=divi_2]').html('30');
					p.$w.find('[name=subs_dias]').html(dias);
					p.$w.find('[name=subs_total]').html(K.round(parseFloat(data.maternidad.total_previo1)+parseFloat(data.maternidad.redondeo1),2));
					p.$w.find('[name=subs_total_prev]').html(parseFloat(data.maternidad.total_previo1)+parseFloat(data.maternidad.redondeo1));
					p.$w.find('[name=total_dia]').html(data.maternidad.promedio_diario);
					p.$w.find('[name=total_remus]').html(data.maternidad.total_meses);
					p.$w.find('[name=subs_dia]').html(data.maternidad.promedio_diario);
					p.$w.find('[name=redondeo]').html(data.maternidad.redondeo1);
					p.$w.find('[name=total]').html(data.maternidad.total_previo1);
					p.$w.find('[name=total_pagar_prev]').html(data.maternidad.total_previo2);
					p.$w.find('[name=redondeo_prev]').html(data.maternidad.redondeo2);
					p.$w.find('[name=total_pagar]').html(data.maternidad.total_pagar);
					p.$w.find('[name=subs_total]').html(data.maternidad.primer_total);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};