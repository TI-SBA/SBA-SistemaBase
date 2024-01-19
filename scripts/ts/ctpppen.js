/*******************************************************************************
Cuentas por Pagar : Pendientes */
tsCtppPen = {
	init: function(){
		if($('#pageWrapper [child=ctpp]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/ctpp',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="ctpp" />');
					$p.find("[name=tsCtpp]").after( $row.children() );
				}
				$p.find('[name=tsCtpp]').data('ctpp',$('#pageWrapper [child=ctpp]:first').data('ctpp'));
				$p.find('[name=tsCtppPen]').click(function(){ tsCtppPen.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsCtppAll]').click(function(){ tsCtppAll.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsCtppPen',
			titleBar: {
				title: 'Cuentas por Pagar Pendientes'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/ctpp/index_pen',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el motivo de la cuenta por pagar' ).width('250');
				$mainPanel.find('[name=obj]').html( 'cuenta(s) por pagar' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=checkall]').click(function(){
					if($mainPanel.find('[name=checkall]').attr('checked')){
						$mainPanel.find('[name=check]').attr('checked',true);
					}else{
						$mainPanel.find('[name=check]').attr('checked',false);
					}
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsCtppPen.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnComprob]').click(function(){			
					if($mainPanel.find('.gridBody .item').length>0){
						var cuentas = new Array;
						var origen = new Array;
						for(i=0;i<$mainPanel.find('.gridBody .item').length;i++){
							if($mainPanel.find('.gridBody .item').eq(i).find('input:checked').val()=="1"){
								cuentas.push($mainPanel.find('.gridBody .item').eq(i).data('data'));
								origen.push($mainPanel.find('.gridBody .item').eq(i).data('data').origen);
							}
						}
						$.unique(origen);
						if(origen.length>1){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Los Comprobantes deben ser de un mismo origen!',type: 'error'});
						}
						if(cuentas.length>0){
							tsCompNue.windowNew({cuentas:cuentas,origen:origen[0]});
						}else{
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos un comprobante de pago!',type: 'error'});
						}					
					}										
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsCtppPen.loadData({page: 1,url: 'ts/ctpp/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsCtppPen.loadData({page: 1,url: 'ts/ctpp/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=modulo]').change(function(){
					$("#mainPanel .gridBody").empty();
					tsCtppPen.loadData({page: 1,url: 'ts/ctpp/lista'});
				});
				tsCtppPen.loadData({page: 1,url: 'ts/ctpp/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		if($mainPanel.find('[name=modulo] :selected').val()!=""){
			params.modulo = $mainPanel.find('[name=modulo] :selected').val();
		}
		params.estado = "P";
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html('<input type="checkbox" name="check" value="1" />');
					if(result.beneficiario)$li.eq(2).html( ciHelper.enti.formatName(result.beneficiario) );
					else $li.eq(2).html("--");
					$li.eq(3).html( result.motivo );
					if(result.afectacion){
						if(result.afectacion.length>1){
							$li.eq(4).html("Varios");
						}else{
							if(result.afectacion[0].organizacion == null)
								$li.eq(4).html("---");
							else{
								$li.eq(4).html(result.afectacion[0].organizacion.nomb);
							}
						}
					}else{
						$li.eq(4).html("--");
					}
					$li.eq(5).html( ciHelper.formatMon(result.total_pago) );
					$li.eq(6).html( ciHelper.formatMon(result.total_desc) );
					$li.eq(7).html( ciHelper.formatMon(result.total) );
					$li.eq(8).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenTsCtpp", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(excep+',#conMenSpOrd_about',menu).remove();
								return menu;
							},
							bindings: {
								'conMenTsCtpp_ver': function(t) {
									tsCtppPen.windowDetails({id: K.tmp.data('id')});
								},
								'conMenTsCtpp_anu': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de anular esta cuenta por Pagar?',
										function () {
											var data = {
												_id: K.tmp.data('id'),
												estado:"X"
											};
											K.sendingInfo();
											$.post('ts/ctpp/save',data,function(){
												K.clearNoti();
												K.notification({title: 'Concepto Habilitado',text: 'La cuenta por pagar seleccionada ha sido Anulada con &eacute;xito!'});
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
						tsCtppPen.loadData(params);
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
			p = new Object;
			p.afec = function (){
				console.log("afectacion creada");
				p.$w.find("#afectaciones").empty();
				for(var i=0; i<(p.$w.find('[name=tab_p] .item').length); i++){
					var asignable = p.$w.find('[name=tab_p] .item').eq(i).data('asignable');
					var conc = p.$w.find('[name=tab_p] .item').eq(i).data('data');
					if(asignable){
						var asig = p.$w.find('[name=tab_p] .item').eq(i).find('[name=asignaciones]').data('asig').asignaciones;
						if(asig!=null){
							for(j=0;j<asig.length;j++){
								if(p.$w.find('#afectaciones [name='+asig[j].organizacion._id+']').length>0){
									var tot = parseFloat(p.$w.find('#afectaciones [name='+asig[j].organizacion._id+']').data('monto'));
									p.$w.find('#afectaciones [name='+asig[j].organizacion._id+']').data('monto',tot+parseFloat(asig[j].monto));
								}else{
									var $row1 = p.$w.find('.ref_afec').clone();
									$li1 = $('li',$row1);
									$li1.eq(0).html(asig[j].organizacion.nomb);
									$row1.wrapInner('<a class="item" name="'+asig[j].organizacion._id+'" />');
						        	p.$w.find("#afectaciones").append( $row1.children() );
						        	p.$w.find('#afectaciones [name='+asig[j].organizacion._id+']').data('orga',asig[j].organizacion).data('gastos',[]);
								}
								var gastos = p.$w.find('#afectaciones [name='+asig[j].organizacion._id+']').data('gastos');
								var sec = false;
								for(var k=0; k<gastos.length; k++){
									if(gastos[k].clasificador._id.$id==conc.clasificador._id.$id){
										gastos[k].monto = parseFloat(gastos[k].monto) + parseFloat(asig[j].monto);
										sec = true;
									}
								}
								if(sec==false){
									gastos.push({
										clasificador: conc.clasificador,
										monto: parseFloat(asig[j].monto)
									});
								}
								p.$w.find('#afectaciones [name='+asig[j].organizacion._id+']').data('gastos',gastos);
							}
						}
					}
				}
			};
		new K.Window({
			id: 'windowNewtsCtpp',
			title: 'Nueva Cuenta por pagar',
			contentURL: 'ts/ctpp/edit',
			icon: 'ui-icon-plusthick',
			width: 700,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;		
					data.estado = "P";
					//data.autor = new Object;
					//data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					var beneficiario = p.$w.find('[name=beneficiario]').data('data');
					if(beneficiario==null){
						p.$w.find('[name=beneficiario]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un beneficiario!',type: 'error'});
					}
					data.beneficiario = ciHelper.enti.dbRel(beneficiario);
					data.conceptos = new Array;
					data.afectacion = [];
					for(var i=0; i<(p.$w.find('#afectaciones .item').length); i++){
						var afect = [];
						var or = p.$w.find('#afectaciones .item').eq(i).data('orga');
						var tt = p.$w.find('#afectaciones .item').eq(i).data('gastos');
						var monto_afec = 0;
						for(var j=0; j<tt.length; j++){
							afect.push({
								clasificador: {
									_id: tt[j].clasificador._id.$id,
									cod: tt[j].clasificador.cod,
									nomb: tt[j].clasificador.nomb
								},
								monto: tt[j].monto
							});
							monto_afec = parseFloat(tt[j].monto)+monto_afec;
						}
						var orggg = {
							nomb: or.nomb,
							actividad: {
								_id: or.actividad._id.$id,
								nomb: or.actividad.nomb,
								cod: or.actividad.cod
							},
							componente: {
								_id: or.componente._id.$id,
								nomb: or.componente.nomb,
								cod: or.componente.cod
							}
						};
						if($.type(or._id)=='object') orggg._id = or._id.$id;
						else orggg._id = or._id;
						data.afectacion.push({
							organizacion: orggg,
							gasto: afect,
							monto: monto_afec
						});
					}
					$items_pagos = p.$w.find('[name=tab_p] .item');
					var total_pagos = 0;
					for(i=0;i<$items_pagos.length;i++){
						total_pagos = total_pagos + parseFloat($items_pagos.eq(i).find('[name=monto]').val());
						var conce = {
								concepto : {
										_id:$items_pagos.eq(i).data('data')._id.$id,
										tipo:$items_pagos.eq(i).data('data').tipo,
										nomb:$items_pagos.eq(i).data('data').nomb		
								},
								tipo : $items_pagos.eq(i).data('data').tipo,
								moneda : "S",
								monto : $items_pagos.eq(i).find('[name=monto]').val()
						};
						if($items_pagos.eq(i).data('data').clasificador){
							conce.concepto.clasificador = {
									_id : $items_pagos.eq(i).data('data').clasificador._id.$id,
									cod:$items_pagos.eq(i).data('data').clasificador.cod,
									nomb:$items_pagos.eq(i).data('data').clasificador.nomb
							};
						}else if($items_pagos.eq(i).data('data').cuenta){
							conce.concepto.cuenta = $items_pagos.eq(i).data('data').cuenta;
							conce.concepto.cuenta = {
									_id : $items_pagos.eq(i).data('data').cuenta._id.$id,
									cod:$items_pagos.eq(i).data('data').cuenta.cod,
									descr:$items_pagos.eq(i).data('data').cuenta.descr
							};
						}
						if($items_pagos.eq(i).find('[name=asignaciones]').data('asig')!=null){
							conce.asignacion = $items_pagos.eq(i).find('[name=asignaciones]').data('asig').asignaciones;
						}
						data.conceptos.push(conce);
					}
					if(data.conceptos.length==0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos un concepto!',type: 'error'});
					}
					/** Descuentos */
					$items_desc = p.$w.find('[name=tab_d] .item');
					var total_desc = 0;
					for(i=0;i<$items_desc.length;i++){
						total_desc = total_desc + parseFloat($items_desc.eq(i).find('[name=monto]').val());
						var conce2 = {
								concepto : {
										_id:$items_desc.eq(i).data('data')._id.$id,
										tipo:$items_desc.eq(i).data('data').tipo,
										nomb:$items_desc.eq(i).data('data').nomb
								},
								tipo : $items_desc.eq(i).data('data').tipo,
								moneda : "S",
								monto : $items_desc.eq(i).find('[name=monto]').val(),
								beneficiario : ciHelper.enti.dbRel($items_desc.eq(i).find('[name=bene]').data('data'))
						};
						if($items_desc.eq(i).data('data').clasificador){
							conce2.concepto.clasificador = {
									_id : $items_desc.eq(i).data('data').clasificador._id.$id,
									cod:$items_desc.eq(i).data('data').clasificador.cod,
									nomb:$items_desc.eq(i).data('data').clasificador.nomb
							};
						}else if($items_desc.eq(i).data('data').cuenta){
							conce2.concepto.cuenta = {
									_id : $items_desc.eq(i).data('data').cuenta._id.$id,
									cod:$items_desc.eq(i).data('data').cuenta.cod,
									descr:$items_desc.eq(i).data('data').cuenta.descr
							};
						}
						data.conceptos.push(conce2);
					}
					/** /Descuentos */
					data.total_pago = total_pagos;
					data.total_desc = total_desc;
					data.total = total_pagos-total_desc;
					data.motivo = p.$w.find('[name=motivo]').val();
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/ctpp/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Cuenta por pagar fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
					
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnAdd]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p.$w.find('[name=btnAsig]').die('click');
				p.$w.find('[name=btnBene]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewtsCtpp');
				K.block({$element: p.$w});
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=beneficiario]').val(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrBene]').button('option','text',false);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEnti]').after('&nbsp;<button name="btnAgrBene">Agregar</button>');
				p.$w.find('[name=btnAgrBene]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.$w.find('[name=beneficiario]').val(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrBene]').button('option','text',false);
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('#tabs_conceptos').tabs();
				p.$w.find('[name=AddConcepto_p]').click(function(){
					var item = p.$w.find('[name=tab_p] .item');
					if(item.length>0){
						if(item.eq(item.length-1).find('input').val()!=""){
							tsConc.windowSelect({callback: function(data){
								if(p.$w.find("[name=tab_p] #"+data._id.$id+"").length<1){
									var $row = p.$w.find('.gridReference').clone();
									$li = $('li',$row);					
									$li.eq(0).html(data.nomb+'<br /><span name="asignaciones"></span>');
									var asignable = true;
									if(data.clasificador){
										$row.find('[name=monto]').attr('readonly','readonly');
									}else if(data.cuenta){
										$li.eq(1).html('---');
										var asignable = false;
									}
									$row.find('[name=btnAsig]').button().css({'width':'auto'});
									$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
									$row.wrapInner('<a class="item" id="'+data._id.$id+'"/>');
									$row.find('a').data('data',data ).data('asignable',asignable);
									p.$w.find("[name=tab_p]").append( $row.children() );
								}else{
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El concepto seleccionado ya existe en la bandeja!',type: 'error'});
								}
							}});
						}else{
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El concepto anterior esta incompleto!',type: 'error'});
						}
					}else{
						tsConc.windowSelect({callback: function(data){
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);					
							$li.eq(0).html(data.nomb+'<br /><span name="asignaciones"></span>');
							var asignable = true;
							if(data.clasificador){
								$row.find('[name=monto]').attr('readonly','readonly');
							}else if(data.cuenta){
								$li.eq(1).html('---');
								var asignable = false;
							}
							$row.find('[name=btnAsig]').button().css({'width':'auto'});
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" id="'+data._id.$id+'"/>');
							$row.find('a').data('data',data ).data('asignable',asignable);
							p.$w.find("[name=tab_p]").append( $row.children() );
						}});
					}
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=AddConcepto_d]').click(function(){
					var item = p.$w.find('[name=tab_d] .item');
					if(item.length>0){
						if(item.eq(item.length-1).find('input').val()!=""){
							tsConc.windowSelect({callback: function(data){
								if(p.$w.find("[name=tab_d] #"+data._id.$id+"").length<1){
									var $row = p.$w.find('.gridReference_desc').clone();
									$li = $('li',$row);					
									$li.eq(0).html(data.nomb);
									$row.find('[name=btnBene]').button({icons: {primary: 'ui-icon-search'},text:false});
									$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
									$row.wrapInner('<a class="item" id="'+data._id.$id+'"/>');
									$row.find('a').data('data',data ).data('asignable',false);
									p.$w.find("[name=tab_d]").append( $row.children() );
								}else{
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El concepto seleccionado ya existe en la bandeja!',type: 'error'});
								}
							}});
						}else{
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El concepto anterior esta incompleto!',type: 'error'});
						}
					}else{
						tsConc.windowSelect({callback: function(data){
							var $row = p.$w.find('.gridReference_desc').clone();
							$li = $('li',$row);					
							$li.eq(0).html(data.nomb);
							$row.find('[name=btnBene]').button({icons: {primary: 'ui-icon-search'},text:false});
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" id="'+data._id.$id+'"/>');
							$row.find('a').data('data',data ).data('asignable',false);
							p.$w.find("[name=tab_d]").append( $row.children() );
						}});
					}
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					p.afec();
				});
				p.$w.find('[name=btnAsig]').live('click',function(){
					var $row = $(this).closest('.item');
					tsCtppPen.windowAsignar({callback: function(data){
						$row.find('[name=monto]').val(data.monto).data('monto',data.monto);
						$row.find('[name=asignaciones]').empty();
						for(i=0;i<data.asignaciones.length;i++){
							$row.find('[name=asignaciones]').append(data.asignaciones[i].organizacion.nomb+': S/. '+data.asignaciones[i].monto+'<br />');
						}
						$row.find('[name=asignaciones]').data('asig',data);
						p.afec();
					},concepto:$row.data('data').nomb});
				});
				p.$w.find('[name=btnBene]').live('click',function(){
					var $row = $(this).closest('.item');
					ciSearch.windowSearchEnti({callback: function(data){
						if(data.tipo_enti=="P"){
							$row.find('[name=bene]').html(data.appat+" "+data.apmat+", "+data.nomb).data('data',data);
						}else{
							$row.find('[name=bene]').html(data.nomb).data('data',data);
						}
					}});
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailstsCtpp'+p.id,
			title: 'Ver Cuenta por pagar',
			contentURL: 'ts/ctpp/details',
			icon: 'ui-icon-search',
			width: 700,
			height: 450,
			buttons: {				
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailstsCtpp'+p.id);
				K.block({$element: p.$w});
				p.$w.find('#tabs_conceptos').tabs();				
				$.post('ts/ctpp/get','id='+p.id,function(data){
					p.$w.find('[name=beneficiario]').html(ciHelper.enti.formatName(data.beneficiario));
					p.$w.find('[name=motivo]').html(data.motivo);
					if(data.conceptos){
						if(data.conceptos.length>0){
							for(i=0;i<data.conceptos.length;i++){
								/** Pagos */
								if(data.conceptos[i].tipo=="P"){
									var $row = p.$w.find('.gridReference').clone();
									$li = $('li',$row);		
									if(data.conceptos[i].concepto){ 
										var nomb = data.conceptos[i].concepto.nomb;
									}else {
										var nomb = data.conceptos[i].observ;
									}
									$li.eq(0).html(nomb+'<br /><span name="asignaciones"></span>');	
									if(data.conceptos[i].asignacion){
										for(j=0;j<data.conceptos[i].asignacion.length;j++){
											$li.eq(1).append(data.conceptos[i].asignacion[j].organizacion.nomb+': S/. '+data.conceptos[i].asignacion[j].monto+'<br />');
										}
									}else{
										$li.eq(1).html("--");
									}
									$row.find('[name=monto]').html(K.round(parseFloat(data.conceptos[i].monto),2));
									$row.wrapInner('<a class="item"/>');
									p.$w.find("[name=tab_p]").append( $row.children() );
								}else if(data.conceptos[i].tipo=="D"){
									/** Descuentos */
									var $row = p.$w.find('.gridReference_desc').clone();
									$li = $('li',$row);	
									if(data.conceptos[i].concepto){ 
										var nomb = data.conceptos[i].concepto.nomb;
									}else {
										var nomb = data.conceptos[i].observ;
									}
									$li.eq(0).html(nomb);
									$row.find('[name=monto]').html(K.round(parseFloat(data.conceptos[i].monto),2));
									$row.find('[name=bene]').html(ciHelper.enti.formatName(data.conceptos[i].beneficiario));
									$row.wrapInner('<a class="item"/>');
									p.$w.find("[name=tab_d]").append( $row.children() );
								}	
							}
						}
					}
				},'json');								
				K.unblock({$element: p.$w});
			}
		});
	},
	windowAsignar: function(p){
		p.calcTot = function(){
				var total = 0;
				for(var i=0; i<(p.$w.find('.gridBody .item').length-1); i++){
					if(p.$w.find('.gridBody .item').eq(i).find('[name=subtotal]').val()!="")
						total = parseFloat(total) + parseFloat(p.$w.find('.gridBody .item').eq(i).find('[name=subtotal]').val());
				}
				p.$w.find('[name=total]:eq(0)').html(K.round(total,2));
			
		};
		new K.Modal({
			id: 'windowAsigtsCtpp',
			title: 'Asignaci&oacute;n del Gasto',
			contentURL: 'ts/ctpp/asignar',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 300,
			buttons: {
				"Asignar": function(){
					var data = new Object;
					data.asignaciones = new Array;
					$items = p.$w.find('.item');
					for(i=0;i<($items.length-1);i++){
						var dataorga = $items.eq(i).find('[name=orga]').data('data');
						var asig = {
								organizacion:{
									_id:dataorga._id.$id,
									nomb:dataorga.nomb,
									actividad:dataorga.actividad,
									componente:dataorga.componente
								},
								monto :$items.eq(i).find('[name=subtotal]').val()
						};
						data.asignaciones.push(asig);
					}
					data.monto = p.$w.find('.total li').eq(2).html();
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnAdd]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p.$w.find('[name=btnOrga]').die('click');
				p.$w.find('[name=subtotal]').die('keyup');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowAsigtsCtpp');
				K.block({$element: p.$w});
				p.$w.find('[name=concepto]').html(p.concepto);
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						if(data.tipo_enti=="P"){
							p.$w.find('[name=beneficiario]').val(data.appat+" "+data.apmat+", "+data.nomb).data('data',data);
						}else{
							p.$w.find('[name=beneficiario]').val(data.nomb).data('data',data);
						}
						p.$w.find('[name=result]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('#tabs_conceptos').tabs();
				p.$w.find('[name=AddConcepto_p]').click(function(){
					tsConc.windowSelect({callback: function(data){
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html(data.nomb);
						if(data.cuenta){
							$li.eq(1).html('<input type="text" name="orga"><button name="btnOrga" style="float: left;">Seleccionar</button>');
						}else if(data.clasificador){
							$li.eq(1).html('---');
						}
						$row.find('[name=btnOrga]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
						$row.find('a').data('data',data );
						p.$w.find("[name=tab_p]").append( $row.children() );
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnAdd]').live('click',function(){
					var $row = p.$w.find('.gridReference').clone();
					$li = $('li',$row);
					$li.eq(0).html(p.$w.find('.gridBody:eq(0) .item').length);
					$row.find('[name=btnOrga]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					p.$w.find(".gridBody .total").before( $row.children() );
					p.$w.find('.gridBody [name=btnAdd]').remove();
					p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
					p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('.gridBody .item').length<2){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$row.find('li:eq(0)').html('1');
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody .total").before( $row.children() );
					}else{
						for(var i=0; i<(p.$w.find('.gridBody .item').length-1); i++){
							p.$w.find('.gridBody .item').eq(i).find('li:first').html(i+1);
						}
					}
					p.$w.find('.gridBody [name=btnAdd]').remove();
					p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
					p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					p.calcTot();
				});
				p.$w.find('[name=subtotal]').live('keyup',function(){
					p.calcTot();
				});
				p.$w.find('[name=btnOrga]').live('click',function(){
					var $row = $(this).closest('.item');
					ciSearch.windowSearchOrga({callback: function(data){
						$row.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				});
				var $row = p.$w.find('.gridReference').clone();
				$row.find('li:eq(0)').html('1');
				$row.find('[name=btnOrga]').button({icons: {primary: 'ui-icon-search'},text: false});
				$row.find('[name=btnAdd]').button({icons: {primary: 'ui-icon-plustick'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$w.find(".gridBody").append( $row.children() );
				//Definir Total
				var $row = p.$w.find('.gridReference').clone();
				var $li = $('li',$row);
				$li.eq(1).html('Total').addClass('ui-state-default ui-button-text-only').css({'float':'right'});
				$li.eq(3).html("");
				$li.eq(2).attr('name','total').html("0.00").css('padding-left','10px');
				$row.wrapInner('<a class="item total" />');
				p.$w.find(".gridBody").append( $row.children() );
				K.unblock({$element: p.$w});
			}
		});
	},
	windowAsignar2: function(p){
		new K.Modal({
			id: 'windowAsigtsCtppinComp',
			title: 'Asignaci&oacute;n del Gasto',
			contentURL: 'ts/ctpp/asignar2',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 300,
			buttons: {
				"Asignar": function(){
					var data = new Object;
					data.asignaciones = new Array;
					$items = p.$w.find('.item');
					for(i=0;i<($items.length);i++){
						var dataorga = $items.eq(i).find('[name=orga]').data('data');
						var asig = {
								organizacion:{
									_id:dataorga._id.$id,
									nomb:dataorga.nomb,
									actividad:dataorga.actividad,
									componente:dataorga.componente
								}
						};
						data.asignaciones.push(asig);
					}
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnAdd]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p.$w.find('[name=btnOrga]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowAsigtsCtppinComp');
				K.block({$element: p.$w});
				p.$w.find('[name=btnAdd]').live('click',function(){
					var $row = p.$w.find('.gridReference').clone();
					$li = $('li',$row);
					$li.eq(0).html(p.$w.find('.gridBody:eq(0) .item').length+1);
					$row.find('[name=btnOrga]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					p.$w.find(".gridBody").append( $row.children() );
					p.$w.find('.gridBody [name=btnAdd]').remove();
					p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-1).find('li:last').append('<button name="btnAdd">Agregar</button>');
					p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli]').live('click',function(){					
					if(p.$w.find('.gridBody .item').length>1){
						$(this).closest('.item').remove();
						for(var i=0; i<(p.$w.find('.gridBody .item').length); i++){
							p.$w.find('.gridBody .item').eq(i).find('li:first').html(i+1);
						}
					}
					p.$w.find('.gridBody [name=btnAdd]').remove();
					p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-1).find('li:last').append('<button name="btnAdd">Agregar</button>');
					p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					p.$w.find('.gridBody [name=btnOrga]').button({icons: {primary: 'ui-icon-search'},text: false});
				});
				p.$w.find('[name=btnOrga]').live('click',function(){
					var $row = $(this).closest('.item');
					ciSearch.windowSearchOrga({callback: function(data){
						if(!data.actividad || !data.componente){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Esta organizaci&oacute;n no tiene enlazado una Actividad/Componente!',type: 'error'});
						}
						$row.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				});
				var $row = p.$w.find('.gridReference').clone();
				$row.find('li:eq(0)').html('1');
				$row.find('[name=btnOrga]').button({icons: {primary: 'ui-icon-search'},text: false});
				$row.find('[name=btnAdd]').button({icons: {primary: 'ui-icon-plustick'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$w.find(".gridBody").append( $row.children() );
				K.unblock({$element: p.$w});
			}
		});
	}
	
};