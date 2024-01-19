/*******************************************************************************
Conceptos */
tsCompNue = {
	tipo_pago : {
			"C":{descr:"Cheque"},
			"T":{descr:"Transferencia"}
	},
	init: function(){
		if($('#pageWrapper [child=comp]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/comp',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="comp" />');
					$p.find("[name=tsComp]").after( $row.children() );
				}
				$p.find('[name=tsComp]').data('comp',$('#pageWrapper [child=comp]:first').data('comp'));
				$p.find('[name=tsCompNue]').click(function(){ tsCompNue.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsCompAll]').click(function(){ tsCompAll.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsCompNue',
			titleBar: {
				title: 'Comprobantes de Pago Nuevos'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/comp',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de un comprobante' ).width('250');
				$mainPanel.find('[name=obj]').html( 'comprobante(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsCompNue.loadData({page: 1,url: 'ts/comp/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsCompNue.loadData({page: 1,url: 'ts/comp/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsCompNue.loadData({page: 1,url: 'ts/comp/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.estado = "R";
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
					$li.eq(1).html( "Comprobante de Pago "+ciHelper.codigos(result.cod,6) );
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('estado',result.estado).data('data',result)
					.contextMenu("conMenTsComp", {
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
								'conMenTsComp_ver': function(t) {
									tsCompNue.windowDetails({id: K.tmp.data('id')});
								},
								'conMenTsComp_edi': function(t) {
									tsCompNue.windowEdit({id: K.tmp.data('id')});
								},
								'conMenTsComp_pag': function(t) {
									tsCompNue.windowPagar({id: K.tmp.data('id')});
								},
								'conMenTsComp_anu': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de anular este comprobante?',
										function () {
											var data = {
												_id: K.tmp.data('id'),
												data: K.tmp.data('data')
											};
											K.sendingInfo();
											$.post('ts/comp/anu2',data,function(){
												K.clearNoti();
												K.notification({title: 'Comprobante Anulado',text: 'El Comprobante de pago seleccionado ha sido anulado con &eacute;xito!'});
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
						tsCompNue.loadData(params);
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
		if(p==null) p = new Object;
		p.cod_prog = function(){
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(0).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(0).html(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('programas'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('programas').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(1).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(1).html(p.$w.find('[name=grid_cod] .item').eq(0).data('programas')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(2).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(2).html(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(3).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(3).html(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('obras'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('obras').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(4).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(4).html(p.$w.find('[name=grid_cod] .item').eq(0).data('obras')[0]);
			}
		};
		p.push_cod_prog = function(actividad,componente){
			$.post('ts/comp/cod_prog','actividad='+actividad+'&componente='+componente,function(res){
				var cod_prog = p.$w.find('[name=grid_cod] .item').eq(0).data('data');
				if(cod_prog==null){
					var new_cod = [];
					new_cod.push(res);
					p.$w.find('[name=grid_cod] .item').eq(0).data('data',new_cod);
				}else{
					cod_prog.push(res);
					p.$w.find('[name=grid_cod] .item').eq(0).data('data',cod_prog);
				}
				var pliegos = p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos');
				if(pliegos !=null){
					pliegos.push(res.pliego.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos',pliegos);
				}else{
					var pliego_new = [];
					pliego_new.push(res.pliego.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos',pliego_new);
				}
				
				var programas = p.$w.find('[name=grid_cod] .item').eq(0).data('programas');
				if(programas !=null){
					programas.push(res.programa.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('programas',programas);
				}else{
					var programa_new = [];
					programa_new.push(res.programa.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('programas',programa_new);
				}
				
				var subprogramas = p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas');
				if(subprogramas !=null){
					subprogramas.push(res.subprograma.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas',subprogramas);
				}else{
					var subprograma_new = [];
					subprograma_new.push(res.subprograma.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas',subprograma_new);
				}
				
				var proyectos = p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos');
				if(proyectos !=null){
					proyectos.push(res.proyecto.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos',proyectos);
				}else{
					var proyecto_new = [];
					proyecto_new.push(res.proyecto.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos',proyecto_new);
				}
				
				var obras = p.$w.find('[name=grid_cod] .item').eq(0).data('obras');
				if(obras !=null){
					obras.push(res.obra.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('obras',obras);
				}else{
					var obra_new = [];
					obra_new.push(res.obra.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('obras',obra_new);
				}
				p.cod_prog();
			},'json');	
		};
		p.sumBene = function(){
			if(p.$w.find('[name=tab_c] .item').length>1&&p.$w.find('[name=tab_t] .item').length>1){
				var total_b_1 = 0;
				for(i=0;i<(p.$w.find('[name=tab_t] .item').length-1);i++){
					total_b_1+=parseFloat(p.$w.find('[name=tab_t] .item').eq(i).data('monto'));
				}
				p.$w.find('[name=tab_t] .total').find('li:eq(2)').html(K.round(total_b_1,2));
				var total_b_2 = 0;
				for(i=0;i<(p.$w.find('[name=tab_c] .item').length-1);i++){
					total_b_2+=parseFloat(p.$w.find('[name=tab_c] .item').eq(i).data('monto'));
				}
				//console.log(total_b_1);
				//console.log(total_b_2);
				p.$w.find('[name=tab_c] .total').find('li:eq(2)').html(K.round(total_b_2,2));
			}
		};
		new K.Window({
			id: 'windowNewtsComp',
			title: 'Nuevo Comprobante de Pago',
			contentURL: 'ts/comp/edit',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;		
					data.estado= "R";
					//data.autor = new Object;
					//data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.cod = p.$w.find('[name=cod]').html();
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}				
					data.descr = p.$w.find('[name=descr]').val();
					data.ref = p.$w.find('[name=ref]').val();
					var cuenta_banco = p.$w.find('[name=pag_cuenta] :selected').data('data');
					data.cuenta_banco = new Object;
					data.cuenta_banco._id = cuenta_banco._id.$id;
					data.cuenta_banco.cod_banco = cuenta_banco.cod_banco;
					data.cuenta_banco.nomb = cuenta_banco.nomb;
					data.cuenta_banco.cod = cuenta_banco.cod;
					data.cuenta_banco.moneda = cuenta_banco.moneda;
					if(!cuenta_banco.cuenta){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ha ocurrido un error la Cuenta Bancaria Seleccionada no tiene cuenta contable relacionada, comuniquelo al area correspondiente!',type: 'error'});
					}
					data.cuenta_banco.cuenta = {
						_id:cuenta_banco.cuenta._id.$id,
						cod:cuenta_banco.cuenta.cod,
						descr:cuenta_banco.cuenta.descr
					};
					/******* Comprobante*/
					data.moneda = "S";
					var serie = p.$w.find('[name=serie]').val();
					if(serie!=""){
						data.comprobante = new Object;
						data.comprobante.serie = serie;
						data.comprobante.num = p.$w.find('[name=num]').val();
						if(data.comprobante.num==""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero par el comprobante!',type: 'error'});
						}
						data.comprobante.tipo = p.$w.find('[name=compr] :selected').val();
					}
					data.items = new Array;
					if(p.cuentas!=null){
						for(i=0;i<p.cuentas.length;i++){
							var item = {
									cuenta_pagar:{
										_id : p.cuentas[i]._id.$id,
										beneficiario : p.cuentas[i].beneficiario,
										motivo : p.cuentas[i].motivo
									}
							};
							item.conceptos=new Object;
							for(j=0;j<p.cuentas[i].conceptos.length;j++){
								item.conceptos[j]=new Object;
								if(p.cuentas[i].conceptos[j].concepto){
									item.conceptos[j].concepto = p.cuentas[i].conceptos[j].concepto;
									item.conceptos[j].monto = p.cuentas[i].conceptos[j].monto;
								}else{
									item.conceptos[j]={
											tipo : p.cuentas[i].conceptos[j].tipo,
											observ : p.cuentas[i].conceptos[j].observ,
											monto : p.cuentas[i].conceptos[j].monto
									};
								}
							}
							data.items.push(item);
						}
					}
					data.beneficiarios = new Array;
					$bene_cheque = p.$w.find('[name=tab_c] .item');
					if(p.$w.find('#tabs_forma').data('data')==0){
						data.tipo_pago = "C";
						for(i=0;i<($bene_cheque.length-1);i++){
							var bene = new Object;
							bene.beneficiario = $bene_cheque.eq(i).data('data');						
							bene.cheque = $bene_cheque.eq(i).find('[name=cheque]').val();
							if(bene.cheque==""){
								$bene_cheque.eq(i).find('[name=cheque]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe poner un cheque para el beneficiario : '+bene.beneficiario.nomb+' !',type: 'error'});
							}
							bene.monto = $bene_cheque.eq(i).find('li').eq(2).html();
							data.beneficiarios.push(bene);
						}
					}else if(p.$w.find('#tabs_forma').data('data')==1){
						data.tipo_pago = "T";
						for(i=0;i<($bene_cheque.length-1);i++){
							var bene = new Object;
							bene.beneficiario = $bene_cheque.eq(i).data('data');
							bene.monto = $bene_cheque.eq(i).find('li').eq(2).html();
							data.beneficiarios.push(bene);
						}
					}					
					data.objeto_gasto = new Array;
					$objeto_gasto = p.$w.find('#grid_est .item');
					if($objeto_gasto.length>0){
						for(i=0;i<$objeto_gasto.length;i++){
							var obj_g = {
									clasificador: {
										_id: $objeto_gasto.eq(i).data('data')._id.$id,
										nomb: $objeto_gasto.eq(i).data('data').nomb,
										cod: $objeto_gasto.eq(i).data('data').cod
									},
									monto: $objeto_gasto.eq(i).find('li').eq(2).html()
							};
							data.objeto_gasto.push(obj_g);
						}
					}
					data.cont_patrimonial = new Array;
					$cont_patr = p.$w.find('[name=grid_cont_patr] .item');
					if($cont_patr.length>0){
						for(i=0;i<$cont_patr.length;i++){
							data.cont_patrimonial.push($cont_patr.eq(i).data('data'));
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un item en Contabilidad Patrimonial!',type: 'error'});
					}
					data.cont_presupuestal = new Array;
					$cont_pres = p.$w.find('[name=grid_cont_pres] .item');
					if($cont_pres.length>0){
						for(i=0;i<$cont_pres.length;i++){
							data.cont_presupuestal.push($cont_pres.eq(i).data('data'));
						}
					}
					data.retenciones = new Array;
					$rete = p.$w.find('[name=grid_rete_dedu] .item');
					if($rete.length>0){
						for(i=0;i<$rete.length;i++){
							data.retenciones.push($rete.eq(i).data('data'));
						}
					}
					data.cod_programatica = p.$w.find('[name=grid_cod] .item').eq(0).data('data');
					if(data.cod_programatica==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Comprobante debe tener codificacion programatica!',type: 'error'});
					}
					var fuente = p.$w.find('[name=fuente] :selected').data('data');
					data.fuente = new Object;
					data.fuente._id = fuente._id.$id;
					data.fuente.cod = fuente.cod;
					K.sendingInfo();
					p.$w.parent().find('.ui-dialog-buttonpane button').button('disable');
					console.log(data);
					$.post('ts/comp/save',data,function(rpta){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Comprobante fue registrado con &eacute;xito!'});
						tsCompNue.windowDetails({id:rpta});
						tsCompNue.init();
						//$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewtsComp');
				K.block({$element: p.$w});
				p.$w.find('#tabs_conceptos').tabs();
				p.$w.find('#tabs_forma').tabs({
					select: function(e, ui) {
                    	var thistab = ui;
                    	//runMethod(thistab.index);
                    	p.$w.find('#tabs_forma').data('data',thistab.index);
                	}
				});
				$.post('ts/comp/edit_data',function(data){
					p.$w.find('[name=cod]').html(data.cod);
				},'json');
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(1).click();
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(0).click();
				p.$w.find('[name=RadTipo1]').buttonset();
				p.$w.find('[name=RadTipo2]').buttonset();
				p.$w.find('[name=btnCuen1]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta1]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCuen2]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta2]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCuen3]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta3]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
				});
				p.$w.find('[name=cuenta1]').autocomplete({
					source: function( request, response ) {
					    $.ajax({
					    	type:'post',
					        url: "ct/pcon/search",
					        data: {page:1,page_rows:5,texto:request.term,tipo:""},
					        dataType: "json",
					        success: function( data ) {
					            response( $.map( data.items, function( item ) {
					                return {
					                	data:item,
					                	id: item.id,
					                    label: item.cod,
					                    value: item.cod
					                }
					            }));
					        }
					    });
					},
					open: function(){
						p.$w.find('[name=cuenta1]').removeData('data');
						p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
					},
					select: function( event, ui ) {
						p.$w.find('[name=cuenta1]').val( ui.item.label );
						p.$w.find('[name=cuenta1]').data('data',ui.item.data);
						p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						return false;
					}
				});
				p.$w.find('[name=add_cont_pres]').click(function(){
					if(p.$w.find('[name=cuenta1]').data('data')!=null){
						if(p.$w.find('[name=importe1]').val()!=""){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find('[name=cuenta1]').data('data').cod);
							$li.eq(1).html(p.$w.find('[name=cuenta1]').data('data').descr);
							if(p.$w.find('[name=rbtnEnlace1]:checked').val()=="D") $li.eq(2).html(p.$w.find('[name=importe1]').val());
							else if(p.$w.find('[name=rbtnEnlace1]:checked').val()=="H") $li.eq(3).html(p.$w.find('[name=importe1]').val());
							$row.wrapInner('<a class="item" />');
							var data = {
									cuenta:{
										_id:p.$w.find('[name=cuenta1]').data('data')._id.$id,
										cod:p.$w.find('[name=cuenta1]').data('data').cod,
										descr:p.$w.find('[name=cuenta1]').data('data').descr
									},
									tipo:p.$w.find('[name=rbtnEnlace1]:checked').val(),
									monto:p.$w.find('[name=importe1]').val(),
									moneda:"S"
							};
							$row.find('a').data('data',data );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_cont_pres]").append( $row.children() );
							/** reset data */
							p.$w.find('[name=cuenta1]').data('data',null).val("");
							p.$w.find('[name=importe1]').val("");
							p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						}else{
							return K.notification({title: "Error al agregar",text: 'Debe ingresar un monto!',type: 'error'});
						}
					}else{
						return K.notification({title: "Error al agregar",text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						p.$w.find('[name=btnCuen1]').click();
					}
				}).button();
				p.$w.find('[name=cuenta2]').autocomplete({
					source: function( request, response ) {
					    $.ajax({
					    	type:'post',
					        url: "ct/pcon/search",
					        data: {page:1,page_rows:5,texto:request.term,tipo:""},
					        dataType: "json",
					        success: function( data ) {
					            response( $.map( data.items, function( item ) {
					                return {
					                	data:item,
					                	id: item.id,
					                    label: item.cod,
					                    value: item.cod
					                }
					            }));
					        }
					    });
					},
					open: function(){
						p.$w.find('[name=cuenta2]').removeData('data');
						p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
					},
					select: function( event, ui ) {
						p.$w.find('[name=cuenta2]').val( ui.item.label );
						p.$w.find('[name=cuenta2]').data('data',ui.item.data);
						p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						return false;
					}
				});
				p.$w.find('[name=add_cont_patr]').click(function(){
					if(p.$w.find('[name=cuenta2]').data('data')!=null){
						if(p.$w.find('[name=importe2]').val()!=""){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find('[name=cuenta2]').data('data').cod);
							$li.eq(1).html(p.$w.find('[name=cuenta2]').data('data').descr);
							if(p.$w.find('[name=rbtnEnlace2]:checked').val()=="D") $li.eq(2).html(p.$w.find('[name=importe2]').val());
							else if(p.$w.find('[name=rbtnEnlace2]:checked').val()=="H") $li.eq(3).html(p.$w.find('[name=importe2]').val());
							$row.wrapInner('<a class="item" />');
							var data = {
									cuenta:{
										_id:p.$w.find('[name=cuenta2]').data('data')._id.$id,
										cod:p.$w.find('[name=cuenta2]').data('data').cod,
										descr:p.$w.find('[name=cuenta2]').data('data').descr
									},
									tipo:p.$w.find('[name=rbtnEnlace2]:checked').val(),
									monto:p.$w.find('[name=importe2]').val(),
									moneda:"S"
							};
							$row.find('a').data('data',data );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_cont_patr]").append( $row.children() );
							/** reset data */
							p.$w.find('[name=cuenta2]').data('data',null).val("");
							p.$w.find('[name=importe2]').val("");
							p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						}else{
							return K.notification({title: "Error al agregar",text: 'Debe ingresar un monto!',type: 'error'});
						}
					}else{
						return K.notification({title: "Error al agregar",text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						p.$w.find('[name=btnCuen2]').click();
					}
				}).button();
				/** Detalle del Gasto */
				if(p.cuentas!=null){
					var total_pag = 0;
					var total = 0;
					for(i=0;i<p.cuentas.length;i++){
						var $row = p.$w.find('.gridReference_det').clone();
						$li = $('li',$row);
						$li.eq(0).html(i+1);
						$li.eq(1).html(p.cuentas[i].motivo);
						//$li.eq(3).html(p.cuentas[i].total_pago);
						$row.wrapInner('<a class="item" id="'+p.cuentas[i]._id.$id+'" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=grid_det]").append( $row.children() );
						if(p.cuentas[i].afectacion){
							p.$w.find('[name=grid_det] #'+p.cuentas[i]._id.$id+' li').eq(2).html(p.cuentas[i].afectacion[0].organizacion.actividad.cod+" / "+p.cuentas[i].afectacion[0].organizacion.componente.cod);
							p.$w.find('[name=grid_det] #'+p.cuentas[i]._id.$id+' li').eq(3).html(K.round(p.cuentas[i].afectacion[0].monto,2)).css({"text-align":"right"});
							if(p.cuentas[i].afectacion.length>1){
								for(j=1;j<p.cuentas[i].afectacion.length;j++){
									var $row2 = p.$w.find('.gridReference_det').clone();
									$li2 = $('li',$row2);
									$li2.eq(2).html(p.cuentas[i].afectacion[j].organizacion.actividad.cod+" / "+p.cuentas[i].afectacion[j].organizacion.componente.cod);
									$li2.eq(3).html(K.round(p.cuentas[i].afectacion[j].monto,2)).css({"text-align":"right"});
									$row2.wrapInner('<a class="item" />');
									p.$w.find("[name=grid_det]").append( $row2.children() );
								}
							}							
							total_pag = parseFloat(p.cuentas[i].total_pago) + total_pag;
						}else{
							p.$w.find('[name=grid_det] #'+p.cuentas[i]._id.$id+'').remove();
						}
						total = parseFloat(p.cuentas[i].total) + total;
					}
					if(p.origen=="D"){
						p.$w.find('[name=detalle_del_gasto]').hide();
					}
					var $row = p.$w.find('.gridReference_det').clone();
					$li = $('li',$row);
					$li.eq(0).addClass('ui-state-default ui-button-text-only');
					$li.eq(1).addClass('ui-state-default ui-button-text-only');
					$li.eq(2).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
					$li.eq(3).html(K.round(total_pag,2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item" />');
					//$row.find('a').data('data',data );
					p.$w.find("[name=grid_det]").append( $row.children() );
				}
				/** /Detalle del Gasto */
				p.$w.find('[name=monto_string]').html(ciHelper.monto2string.covertirNumLetras(total+""));
				p.$w.find('#totales .total li').eq(1).html("S/. "+K.round(total,2)).css({"text-align":"right"});
				/** Pagos */
				if(p.cuentas!=null){
					var total_pag = 0;
					for(i=0;i<p.cuentas.length;i++){
						var $row = p.$w.find('.gridReference_conc').clone();
						$li = $('li',$row);
						$li.eq(0).html(i+1);
						$li.eq(1).html(p.cuentas[i].motivo);
						$li.eq(2).html(K.round(p.cuentas[i].total_pago,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=tab_p]").append( $row.children() );
						if(p.cuentas[i].conceptos.length>0){
							for(j=0;j<p.cuentas[i].conceptos.length;j++){
								if(p.cuentas[i].conceptos[j].tipo=="P"){
									var $row2 = p.$w.find('.gridReference_conc').clone();
									$li2 = $('li',$row2);
									if(p.cuentas[i].conceptos[j].concepto){
										$li2.eq(1).html(p.cuentas[i].conceptos[j].concepto.nomb);
									}else{
										$li2.eq(1).html(p.cuentas[i].conceptos[j].observ);
									}
									$li2.eq(2).html(K.round(p.cuentas[i].conceptos[j].monto,2));
									$row2.wrapInner('<a class="item" />');
									p.$w.find("[name=tab_p]").append( $row2.children() );
								}
							}
						}
						total_pag = parseFloat(p.cuentas[i].total_pago) + total_pag;
					}
					var $row = p.$w.find('.gridReference_conc').clone();
					$li = $('li',$row);
					$li.eq(0).addClass('ui-state-default ui-button-text-only');
					$li.eq(1).html("Total pagos").addClass('ui-state-default ui-button-text-only');
					$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item" />');
					//$row.find('a').data('data',data );
					p.$w.find("[name=tab_p]").append( $row.children() );
					p.$w.find('#totales .pago li').eq(1).html("S/. "+K.round(total_pag,2)).css({"text-align":"right"});
				}
				/** /Pagos */
				
				/** Descuentos */
				if(p.cuentas!=null){
					var total_des = 0;
					for(i=0;i<p.cuentas.length;i++){
						if(parseFloat(p.cuentas[i].total_desc)>0){
							var $row = p.$w.find('.gridReference_conc').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find("[name=tab_d] #conceptoo").length+1);
							$li.eq(1).html(p.cuentas[i].motivo);
							$li.eq(2).html(K.round(p.cuentas[i].total_desc,2)).css({"text-align":"right"});
							$row.wrapInner('<a class="item" id="conceptoo"/>');
							//$row.find('a').data('data',data );
							p.$w.find("[name=tab_d]").append( $row.children() );
							if(p.cuentas[i].conceptos.length>0){
								for(j=0;j<p.cuentas[i].conceptos.length;j++){
									if(p.cuentas[i].conceptos[j].tipo=="D"){
										var $row2 = p.$w.find('.gridReference_conc').clone();
										$li2 = $('li',$row2);
										if(p.cuentas[i].conceptos[j].concepto){
											$li2.eq(1).html(p.cuentas[i].conceptos[j].concepto.nomb);
										}else{
											$li2.eq(1).html(p.cuentas[i].conceptos[j].observ);
										}
										$li2.eq(2).html(K.round(p.cuentas[i].conceptos[j].monto,2));
										$row2.wrapInner('<a class="item" />');
										p.$w.find("[name=tab_d]").append( $row2.children() );
									}
								}
							}
							total_des = parseFloat(p.cuentas[i].total_desc) + total_des;
						}
					}
					var $row = p.$w.find('.gridReference_conc').clone();
					$li = $('li',$row);
					$li.eq(0).addClass('ui-state-default ui-button-text-only');
					$li.eq(1).html("Total descuentos").addClass('ui-state-default ui-button-text-only');
					$li.eq(2).html(K.round(total_des,2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item" />');
					//$row.find('a').data('data',data );
					p.$w.find("[name=tab_d]").append( $row.children() );
					p.$w.find('#totales .desc li').eq(1).html("S/. "+K.round(total_des,2)).css({"text-align":"right"});
				}
				/** /Descuentos */
				
				/** Estadistica Objeto del Gasto */
				if(p.cuentas!=null){
					for(i=0;i<p.cuentas.length;i++){
						if(p.cuentas[i].afectacion){
							for(j=0;j<p.cuentas[i].afectacion.length;j++){
								if(p.cuentas[i].afectacion[j].gasto){
									for(k=0;k<p.cuentas[i].afectacion[j].gasto.length;k++){
										var clasi = p.$w.find('#grid_est [name='+p.cuentas[i].afectacion[j].gasto[k].clasificador._id.$id+']');
										if(clasi.length>0){
											var monto = clasi.find('li').eq(2).html();
											clasi.find('li').eq(2).html(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].afectacion[j].gasto[k].monto),2)).css({"text-align":"right"});
										}else{
											var $row2 = p.$w.find('.gridReference_est').clone();
											$li2 = $('li',$row2);
											$li2.eq(0).html(p.cuentas[i].afectacion[j].gasto[k].clasificador.cod);
											$li2.eq(1).html(p.cuentas[i].afectacion[j].gasto[k].clasificador.nomb);
											$li2.eq(2).html(K.round(p.cuentas[i].afectacion[j].gasto[k].monto,2)).css({"text-align":"right"});
											$row2.wrapInner('<a class="item" name="'+p.cuentas[i].afectacion[j].gasto[k].clasificador._id.$id+'"/>');
											$row2.find('a').data('data',p.cuentas[i].afectacion[j].gasto[k].clasificador);
											p.$w.find("#grid_est").append( $row2.children() );
										}
									}
								}
							}
						}
					}
				}
				/** /Estadistica Objeto del Gasto */
				
				/** Retenciones y/o deducciones */
				if(p.cuentas!=null){
					for(i=0;i<p.cuentas.length;i++){
						if(p.cuentas[i].conceptos){
							for(j=0;j<p.cuentas[i].conceptos.length;j++){
								if(p.cuentas[i].conceptos[j].concepto&&p.cuentas[i].conceptos[j].concepto.cuenta){
									if(p.cuentas[i].conceptos[j].tipo=="D"){
										var reten = p.$w.find('[name=grid_rete_dedu] #'+p.cuentas[i].conceptos[j].concepto.cuenta._id.$id+'');
										if(reten.length>0){
											var monto = reten.data('monto');
											reten.find('li').eq(2).html(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].conceptos[j].monto),2)).css({"text-align":"right"});
											reten.data('monto',parseFloat(monto)+parseFloat(p.cuentas[i].conceptos[j].monto));
										}else{
											var $row2 = p.$w.find('.gridReference_rete_dedu').clone();
											$li2 = $('li',$row2);
											$li2.eq(0).html(p.cuentas[i].conceptos[j].concepto.cuenta.cod);
											$li2.eq(1).html(p.cuentas[i].conceptos[j].concepto.cuenta.descr);
											$li2.eq(2).html(K.round(p.cuentas[i].conceptos[j].monto,2)).css({"text-align":"right"});
											$row2.wrapInner('<a class="item" id="'+p.cuentas[i].conceptos[j].concepto.cuenta._id.$id+'"/>');
											$row2.find('a').data('data',p.cuentas[i].conceptos[j]).data('monto',p.cuentas[i].conceptos[j].monto);
											p.$w.find("[name=grid_rete_dedu]").append( $row2.children() );
										}		
										//console.log(p.cuentas[i].conceptos[j].monto);
									}
								}								
							}
						}
					}
				}
				p.$w.find('[name=cuenta3]').autocomplete({
					source: function( request, response ) {
					    $.ajax({
					    	type:'post',
					        url: "ct/pcon/search",
					        data: {page:1,page_rows:5,texto:request.term,tipo:""},
					        dataType: "json",
					        success: function( data ) {
					            response( $.map( data.items, function( item ) {
					                return {
					                	data:item,
					                	id: item.id,
					                    label: item.cod,
					                    value: item.cod
					                }
					            }));
					        }
					    });
					},
					open: function(){
						p.$w.find('[name=cuenta3]').removeData('data');
						p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
					},
					select: function( event, ui ) {
						p.$w.find('[name=cuenta3]').val( ui.item.label );
						p.$w.find('[name=cuenta3]').data('data',ui.item.data);
						p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						return false;
					}
				});
				p.$w.find('[name=add_rete_dedu]').click(function(){
					if(p.$w.find('[name=cuenta3]').data('data')!=null){
						if(p.$w.find('[name=importe3]').val()!=""){
							var $row = p.$w.find('.gridReference_rete_dedu').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find('[name=cuenta3]').data('data').cod);
							$li.eq(1).html(p.$w.find('[name=cuenta3]').data('data').descr);
							$li.eq(2).html(p.$w.find('[name=importe3]').val());
							$row.wrapInner('<a class="item" />');
							var data = {
									cuenta:{
										_id:p.$w.find('[name=cuenta3]').data('data')._id.$id,
										cod:p.$w.find('[name=cuenta3]').data('data').cod,
										descr:p.$w.find('[name=cuenta3]').data('data').descr
									},
									monto:p.$w.find('[name=importe3]').val(),
									moneda:"S"
							};
							$row.find('a').data('data',data );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_rete_dedu]").append( $row.children() );
							/** reset data */
							p.$w.find('[name=cuenta3]').data('data',null).val("");
							p.$w.find('[name=importe3]').val("");
							p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						}else{
							return K.notification({title: "Error al agregar",text: 'Debe ingresar un monto!',type: 'error'});
						}
					}else{
						return K.notification({title: "Error al agregar",text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						p.$w.find('[name=btnCuen3]').click();
					}
				}).button();
				/** /Retenciones y/o deducciones */
				
				/** Formas de Pago */
				if(p.cuentas!=null){
					var total_pag = 0;
					for(i=0;i<p.cuentas.length;i++){
						/* Cheques */
						if(p.cuentas[i].beneficiario){
							var bene = p.$w.find('[name=tab_c] #'+p.cuentas[i].beneficiario._id.$id+'');
							if(bene.length>0){
								var monto = bene.data('monto');
								bene.find('li').eq(2).html(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].total),2)).css({"text-align":"right"});
								bene.data('monto',parseFloat(monto)+parseFloat(p.cuentas[i].total));
							}else{
								var $row = p.$w.find('.gridReference_forma_cheque').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find('[name=tab_c] .item').length+1);
								$li.eq(1).html(ciHelper.enti.formatName(p.cuentas[i].beneficiario));
								$li.eq(2).html(K.round(p.cuentas[i].total,2)).css({"text-align":"right"});
								$li.eq(3).html('<input type="text" name="cheque" size="9">');
								$row.wrapInner('<a class="item" id='+p.cuentas[i].beneficiario._id.$id+'/>');
								$row.find('a').data('data',p.cuentas[i].beneficiario).data('monto',p.cuentas[i].total);
								p.$w.find("[name=tab_c]").append( $row.children() );
							}
							/* Transferencias */
							var bene = p.$w.find('[name=tab_t] #'+p.cuentas[i].beneficiario._id.$id+'');
							if(bene.length>0){
								var monto = bene.data('monto');
								bene.find('li').eq(2).html(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].total),2)).css({"text-align":"right"});
								bene.data('monto',parseFloat(monto)+parseFloat(p.cuentas[i].total));
							}else{
								var $row = p.$w.find('.gridReference_forma_cheque').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find('[name=tab_t] .item').length+1);
								$li.eq(1).html(ciHelper.enti.formatName(p.cuentas[i].beneficiario));
								$li.eq(2).html(K.round(p.cuentas[i].total,2)).css({"text-align":"right"});
								$row.wrapInner('<a class="item" id='+p.cuentas[i].beneficiario._id.$id+'/>');
								$row.find('a').data('data',p.cuentas[i].beneficiario).data('monto',p.cuentas[i].total);;
								p.$w.find("[name=tab_t]").append( $row.children() );
							}
							total_pag = parseFloat(p.cuentas[i].total) + total_pag;
						}
					}
					var $row = p.$w.find('.gridReference_forma_trans').clone();
					$li = $('li',$row);
					$li.eq(0).addClass('ui-state-default ui-button-text-only');
					$li.eq(1).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
					$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item total" />');
					p.$w.find("[name=tab_t]").append( $row.children() );
					
					var $row = p.$w.find('.gridReference_forma_cheque').clone();
					$li = $('li',$row);
					$li.eq(0).addClass('ui-state-default ui-button-text-only');
					$li.eq(1).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
					$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item total" />');
					p.$w.find("[name=tab_c]").append( $row.children() );
				}
				/** /Formas de Pago */
				
				/** Cuenta Bancaria */			
				$.post('ts/ctban/all',function(data){
					var $cbo = p.$w.find('[name=pag_cuenta]');
					if(data!=null){
						for(var i=0; i<data.length; i++){		
							var id = data[i]._id.$id;
							var cod_banco = data[i].cod_banco;
							var nomb = data[i].nomb;
							var cod = data[i].cod;
							var moneda = data[i].moneda;
							$cbo.append('<option value="'+id+'" >'+cod+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
				},'json');
				/** /Cuenta Bancaria */
				
				/** Asignaciones */
				//if(p.origen=="D"){
					p.$w.find('[name=btnAsignar]').show().click(function(){
						var $row = $(this).closest('.item');
						tsCtppPen.windowAsignar2({callback: function(data){
							$row.find('[name=asignaciones]').empty();
							for(i=0;i<data.asignaciones.length;i++){
								p.push_cod_prog(data.asignaciones[i].organizacion.actividad._id.$id,data.asignaciones[i].organizacion.componente._id.$id);
							}
							p.cod_prog();
							//$row.find('[name=asignaciones]').data('asig',data);
						}});
					}).button();
				//}
				/** /Asignaciones */
				
				/** Codificacion programatica */
				var $row = p.$w.find('.gridReference_cod').clone();
				$li = $('li',$row);
				$row.wrapInner('<a class="item" />');
				p.$w.find("[name=grid_cod]").append( $row.children() );
				/** /Codificacion programatica */
				$.post('pr/fuen/all',function(data){
					var $cbo = p.$w.find('[name=fuente]');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo.append('<option value="'+id+'" >'+rubro+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
				},'json');
				if(p.origen=="P"){
					if(p.cuentas!=null){
						for(i=0;i<p.cuentas.length;i++){
							if(p.cuentas[i].afectacion){
								for(j=0;j<p.cuentas[i].afectacion.length;j++){
									p.push_cod_prog(p.cuentas[i].afectacion[j].organizacion.actividad._id.$id,p.cuentas[i].afectacion[j].organizacion.componente._id.$id);	
								}
							}
						}
					}
					/*p.$w.scroll(function(){
						p.cod_prog();
					});*/
					//p.cod_prog();
				}
				/** Add Beneficiarios */
				p.$w.find('[name=btnSelectBene]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=add_bene]').html(ciHelper.enti.formatName(data)).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAddBene]').click(function(){
					if(p.$w.find('[name=add_bene]').data('data')==null){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un beneficiario!',type: 'error'});
						return p.$w.find('[name=btnSelectBene]').click();
					}
					if(p.$w.find('[name=add_monto]').val()==""){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
						return p.$w.find('[name=add_monto]').focus();
					}
					/* Cheques */
					var $row = p.$w.find('.gridReference_forma_cheque').clone();
					$li = $('li',$row);
					$li.eq(0).html(p.$w.find('[name=tab_c] .item').length+1);
					$li.eq(1).html(ciHelper.enti.formatName(p.$w.find('[name=add_bene]').data('data')));
					$li.eq(2).html(K.round(p.$w.find('[name=add_monto]').val(),2)).css({"text-align":"right"});
					$li.eq(3).html('<input type="text" name="cheque" size="9">');
					$row.wrapInner('<a class="item" id='+p.$w.find('[name=add_bene]').data('data')._id.$id+'/>');
					var bene = {
						_id: p.$w.find('[name=add_bene]').data('data')._id,
						tipo_enti: p.$w.find('[name=add_bene]').data('data').tipo_enti,
						nomb: p.$w.find('[name=add_bene]').data('data').nomb
					};
					if(bene.tipo_enti=="P"){
						bene.appat = p.$w.find('[name=add_bene]').data('data').appat;
						bene.apmat = p.$w.find('[name=add_bene]').data('data').apmat;
					}
					$row.find('a').data('data',bene).data('monto',p.$w.find('[name=add_monto]').val());
					p.$w.find("[name=tab_c] .total").before( $row.children() );
					/* Transferencias */
					var $row = p.$w.find('.gridReference_forma_cheque').clone();
					$li = $('li',$row);
					$li.eq(0).html(p.$w.find('[name=tab_t] .item').length+1);
					$li.eq(1).html(ciHelper.enti.formatName(p.$w.find('[name=add_bene]').data('data')));
					$li.eq(2).html(K.round(p.$w.find('[name=add_monto]').val(),2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item" id='+p.$w.find('[name=add_bene]').data('data')._id.$id+'/>');
					$row.find('a').data('data',bene).data('monto',p.$w.find('[name=add_monto]').val());;
					p.$w.find("[name=tab_t] .total").before( $row.children() );
					p.sumBene();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				/** ./Add Beneficiarios */
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		p.cod_prog = function(){
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(0).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(0).html(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('programas'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('programas').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(1).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(1).html(p.$w.find('[name=grid_cod] .item').eq(0).data('programas')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(2).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(2).html(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(3).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(3).html(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('obras'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('obras').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(4).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(4).html(p.$w.find('[name=grid_cod] .item').eq(0).data('obras')[0]);
			}
		};
		p.push_cod_prog = function(actividad,componente){
			$.post('ts/comp/cod_prog','actividad='+actividad+'&componente='+componente,function(res){
				var cod_prog = p.$w.find('[name=grid_cod] .item').eq(0).data('data');
				if(cod_prog==null){
					var new_cod = [];
					new_cod.push(res);
					p.$w.find('[name=grid_cod] .item').eq(0).data('data',new_cod);
				}else{
					cod_prog.push(res);
					p.$w.find('[name=grid_cod] .item').eq(0).data('data',cod_prog);
				}
				var pliegos = p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos');
				if(pliegos !=null){
					pliegos.push(res.pliego.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos',pliegos);
				}else{
					var pliego_new = [];
					pliego_new.push(res.pliego.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos',pliego_new);
				}
				
				var programas = p.$w.find('[name=grid_cod] .item').eq(0).data('programas');
				if(programas !=null){
					programas.push(res.programa.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('programas',programas);
				}else{
					var programa_new = [];
					programa_new.push(res.programa.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('programas',programa_new);
				}
				
				var subprogramas = p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas');
				if(subprogramas !=null){
					subprogramas.push(res.subprograma.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas',subprogramas);
				}else{
					var subprograma_new = [];
					subprograma_new.push(res.subprograma.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas',subprograma_new);
				}
				
				var proyectos = p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos');
				if(proyectos !=null){
					proyectos.push(res.proyecto.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos',proyectos);
				}else{
					var proyecto_new = [];
					proyecto_new.push(res.proyecto.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos',proyecto_new);
				}
				
				var obras = p.$w.find('[name=grid_cod] .item').eq(0).data('obras');
				if(obras !=null){
					obras.push(res.obra.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('obras',obras);
				}else{
					var obra_new = [];
					obra_new.push(res.obra.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('obras',obra_new);
				}
				p.cod_prog();
			},'json');	
		};
		p.sumBene = function(){
			if(p.$w.find('[name=tab_c] .item').length>1&&p.$w.find('[name=tab_t] .item').length>1){
				var total_b_1 = 0;
				for(i=0;i<(p.$w.find('[name=tab_t] .item').length-1);i++){
					total_b_1+=parseFloat(p.$w.find('[name=tab_t] .item').eq(i).data('monto'));
				}
				p.$w.find('[name=tab_t] .total').find('li:eq(2)').html(K.round(total_b_1,2));
				var total_b_2 = 0;
				for(i=0;i<(p.$w.find('[name=tab_c] .item').length-1);i++){
					total_b_2+=parseFloat(p.$w.find('[name=tab_c] .item').eq(i).data('monto'));
				}
				//console.log(total_b_1);
				//console.log(total_b_2);
				p.$w.find('[name=tab_c] .total').find('li:eq(2)').html(K.round(total_b_2,2));
			}
		};
		new K.Window({
			id: 'windowEditsComp'+p.id,
			title: 'Editar Comprobante de Pago',
			contentURL: 'ts/comp/edit',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;	
					data._id = p.id;
					data.estado= "R";
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}				
					data.descr = p.$w.find('[name=descr]').val();
					data.ref = p.$w.find('[name=ref]').val();
					var cuenta_banco = p.$w.find('[name=pag_cuenta] :selected').data('data');
					data.cuenta_banco = new Object;
					data.cuenta_banco._id = cuenta_banco._id.$id;
					data.cuenta_banco.cod_banco = cuenta_banco.cod_banco;
					data.cuenta_banco.nomb = cuenta_banco.nomb;
					data.cuenta_banco.cod = cuenta_banco.cod;
					data.cuenta_banco.moneda = cuenta_banco.moneda;
					data.cuenta_banco.cuenta = {
							_id:cuenta_banco.cuenta._id.$id,
							cod:cuenta_banco.cuenta.cod,
							descr:cuenta_banco.cuenta.descr
					};
					/******* Comprobante*/
					var serie = p.$w.find('[name=serie]').val();
					if(serie!=""){
						data.comprobante = new Object;
						data.comprobante.serie = serie;
						data.comprobante.num = p.$w.find('[name=num]').val();
						if(data.comprobante.num==""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero par el comprobante!',type: 'error'});
						}
						data.comprobante.tipo = p.$w.find('[name=compr] :selected').val();
					}
					data.beneficiarios = new Array;
					$bene_cheque = p.$w.find('[name=tab_c] .item');
					if(p.$w.find('#tabs_forma').data('data')==0){
						data.tipo_pago = "C";
						if($bene_cheque.length>1){
							for(i=0;i<($bene_cheque.length-1);i++){
								var bene = new Object;
								bene.beneficiario = $bene_cheque.eq(i).data('data');						
								bene.cheque = $bene_cheque.eq(i).find('[name=cheque]').val();
								if(bene.cheque==""){
									$bene_cheque.eq(i).find('[name=cheque]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe poner un cheque para el beneficiario : '+bene.beneficiario.nomb+' !',type: 'error'});
								}
								bene.monto = $bene_cheque.eq(i).find('li').eq(2).html();
								data.beneficiarios.push(bene);
							}
						}
					}else if(p.$w.find('#tabs_forma').data('data')==1){
						data.tipo_pago = "T";
						if($bene_cheque.length>1){
							for(i=0;i<($bene_cheque.length-1);i++){
								var bene = new Object;
								bene.beneficiario = $bene_cheque.eq(i).data('data');
								bene.monto = $bene_cheque.eq(i).find('li').eq(2).html();
								data.beneficiarios.push(bene);
							}
						}
					}					
					data.cont_patrimonial = new Array;
					$cont_patr = p.$w.find('[name=grid_cont_patr] .item');
					if($cont_patr.length>0){
						for(i=0;i<$cont_patr.length;i++){
							data.cont_patrimonial.push($cont_patr.eq(i).data('data'));
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un item en Contabilidad Patrimonial!',type: 'error'});
					}
					data.cont_presupuestal = new Array;
					$cont_pres = p.$w.find('[name=grid_cont_pres] .item');
					if($cont_pres.length>0){
						for(i=0;i<$cont_pres.length;i++){
							data.cont_presupuestal.push($cont_pres.eq(i).data('data'));
						}
					}
					data.retenciones = new Array;
					$rete = p.$w.find('[name=grid_rete_dedu] .item');
					if($rete.length>0){
						for(i=0;i<$rete.length;i++){
							data.retenciones.push($rete.eq(i).data('data'));
						}
					}
					data.cod_programatica = p.$w.find('[name=grid_cod] .item').eq(0).data('data');
					if(data.cod_programatica==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El Comprobante debe tener codificacion programatica!',type: 'error'});
					}
					var fuente = p.$w.find('[name=fuente] :selected').data('data');
					data.fuente = new Object;
					data.fuente._id = fuente._id.$id;
					data.fuente.cod = fuente.cod;
					K.sendingInfo();
					p.$w.parent().find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/comp/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Comprobante fue registrado con &eacute;xito!'});
						tsCompNue.init();
						//$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditsComp'+p.id);
				K.block({$element: p.$w});
				p.$w.find('#tabs_conceptos').tabs();
				p.$w.find('#tabs_forma').tabs({
					select: function(e, ui) {
                    	var thistab = ui;
                    	//runMethod(thistab.index);
                    	p.$w.find('#tabs_forma').data('data',thistab.index);
                	}
				});
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(1).click();
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(0).click();
				p.$w.find('[name=RadTipo1]').buttonset();
				p.$w.find('[name=RadTipo2]').buttonset();
				p.$w.find('[name=btnCuen1]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta1]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCuen2]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta2]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCuen3]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta3]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
				});
				p.$w.find('[name=cuenta1]').autocomplete({
					source: function( request, response ) {
					    $.ajax({
					    	type:'post',
					        url: "ct/pcon/search",
					        data: {page:1,page_rows:5,texto:request.term,tipo:""},
					        dataType: "json",
					        success: function( data ) {
					            response( $.map( data.items, function( item ) {
					                return {
					                	data:item,
					                	id: item.id,
					                    label: item.cod,
					                    value: item.cod
					                }
					            }));
					        }
					    });
					},
					open: function(){
						p.$w.find('[name=cuenta1]').removeData('data');
						p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
					},
					select: function( event, ui ) {
						p.$w.find('[name=cuenta1]').val( ui.item.label );
						p.$w.find('[name=cuenta1]').data('data',ui.item.data);
						p.$w.find('[name=result-cuen1]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						return false;
					}
				});
				p.$w.find('[name=add_cont_pres]').click(function(){
					if(p.$w.find('[name=cuenta1]').data('data')!=null){
						if(p.$w.find('[name=importe1]').val()!=""){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find('[name=cuenta1]').data('data').cod);
							$li.eq(1).html(p.$w.find('[name=cuenta1]').data('data').descr);
							if(p.$w.find('[name=rbtnEnlace1]:checked').val()=="D") $li.eq(2).html(p.$w.find('[name=importe1]').val());
							else if(p.$w.find('[name=rbtnEnlace1]:checked').val()=="H") $li.eq(3).html(p.$w.find('[name=importe1]').val());
							$row.wrapInner('<a class="item" />');
							var data = {
									cuenta:{
										_id:p.$w.find('[name=cuenta1]').data('data')._id.$id,
										cod:p.$w.find('[name=cuenta1]').data('data').cod,
										descr:p.$w.find('[name=cuenta1]').data('data').descr
									},
									tipo:p.$w.find('[name=rbtnEnlace1]:checked').val(),
									monto:p.$w.find('[name=importe1]').val(),
									moneda:"S"
							};
							$row.find('a').data('data',data );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_cont_pres]").append( $row.children() );
							/** reset data */
							p.$w.find('[name=cuenta1]').data('data',null).val("");
							p.$w.find('[name=importe1]').val("");	
						}else{
							return K.notification({title: "Error al agregar",text: 'Debe ingresar un monto!',type: 'error'});
						}
					}else{
						return K.notification({title: "Error al agregar",text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						p.$w.find('[name=btnCuen1]').click();
					}
				}).button();
				p.$w.find('[name=cuenta2]').autocomplete({
					source: function( request, response ) {
					    $.ajax({
					    	type:'post',
					        url: "ct/pcon/search",
					        data: {page:1,page_rows:5,texto:request.term,tipo:""},
					        dataType: "json",
					        success: function( data ) {
					            response( $.map( data.items, function( item ) {
					                return {
					                	data:item,
					                	id: item.id,
					                    label: item.cod,
					                    value: item.cod
					                }
					            }));
					        }
					    });
					},
					open: function(){
						p.$w.find('[name=cuenta2]').removeData('data');
						p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
					},
					select: function( event, ui ) {
						p.$w.find('[name=cuenta2]').val( ui.item.label );
						p.$w.find('[name=cuenta2]').data('data',ui.item.data);
						p.$w.find('[name=result-cuen2]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						return false;
					}
				});
				p.$w.find('[name=add_cont_patr]').click(function(){
					if(p.$w.find('[name=cuenta2]').data('data')!=null){
						if(p.$w.find('[name=importe2]').val()!=""){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find('[name=cuenta2]').data('data').cod);
							$li.eq(1).html(p.$w.find('[name=cuenta2]').data('data').descr);
							if(p.$w.find('[name=rbtnEnlace2]:checked').val()=="D") $li.eq(2).html(p.$w.find('[name=importe2]').val());
							else if(p.$w.find('[name=rbtnEnlace2]:checked').val()=="H") $li.eq(3).html(p.$w.find('[name=importe2]').val());
							$row.wrapInner('<a class="item" />');
							var data = {
									cuenta:{
										_id:p.$w.find('[name=cuenta2]').data('data')._id.$id,
										cod:p.$w.find('[name=cuenta2]').data('data').cod,
										descr:p.$w.find('[name=cuenta2]').data('data').descr
									},
									tipo:p.$w.find('[name=rbtnEnlace2]:checked').val(),
									monto:p.$w.find('[name=importe2]').val(),
									moneda:"S"
							};
							$row.find('a').data('data',data );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_cont_patr]").append( $row.children() );
							/** reset data */
							p.$w.find('[name=cuenta2]').data('data',null).val("");
							p.$w.find('[name=importe2]').val("");	
						}else{
							return K.notification({title: "Error al agregar",text: 'Debe ingresar un monto!',type: 'error'});
						}
					}else{
						return K.notification({title: "Error al agregar",text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						p.$w.find('[name=btnCuen2]').click();
					}
				}).button();
				p.$w.find('[name=cuenta3]').autocomplete({
					source: function( request, response ) {
					    $.ajax({
					    	type:'post',
					        url: "ct/pcon/search",
					        data: {page:1,page_rows:5,texto:request.term,tipo:""},
					        dataType: "json",
					        success: function( data ) {
					            response( $.map( data.items, function( item ) {
					                return {
					                	data:item,
					                	id: item.id,
					                    label: item.cod,
					                    value: item.cod
					                }
					            }));
					        }
					    });
					},
					open: function(){
						p.$w.find('[name=cuenta3]').removeData('data');
						p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
					},
					select: function( event, ui ) {
						p.$w.find('[name=cuenta3]').val( ui.item.label );
						p.$w.find('[name=cuenta3]').data('data',ui.item.data);
						p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						return false;
					}
				});
				p.$w.find('[name=add_rete_dedu]').click(function(){
					if(p.$w.find('[name=cuenta3]').data('data')!=null){
						if(p.$w.find('[name=importe3]').val()!=""){
							var $row = p.$w.find('.gridReference_rete_dedu').clone();
							$li = $('li',$row);
							$li.eq(0).html(p.$w.find('[name=cuenta3]').data('data').cod);
							$li.eq(1).html(p.$w.find('[name=cuenta3]').data('data').descr);
							$li.eq(2).html(p.$w.find('[name=importe3]').val());
							$row.wrapInner('<a class="item" />');
							var data = {
									cuenta:{
										_id:p.$w.find('[name=cuenta3]').data('data')._id.$id,
										cod:p.$w.find('[name=cuenta3]').data('data').cod,
										descr:p.$w.find('[name=cuenta3]').data('data').descr
									},
									monto:p.$w.find('[name=importe3]').val(),
									moneda:"S"
							};
							$row.find('a').data('data',data );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_rete_dedu]").append( $row.children() );
							/** reset data */
							p.$w.find('[name=cuenta3]').data('data',null).val("");
							p.$w.find('[name=importe3]').val("");
							p.$w.find('[name=result-cuen3]').removeClass('ui-icon-circle-check').addClass('ui-icon-circle-close');
						}else{
							return K.notification({title: "Error al agregar",text: 'Debe ingresar un monto!',type: 'error'});
						}
					}else{
						return K.notification({title: "Error al agregar",text: 'Debe seleccionar una cuenta contable!',type: 'error'});
						p.$w.find('[name=btnCuen3]').click();
					}
				}).button();
				/** Cuenta Bancaria */			
				$.post('ts/ctban/all',function(data){
					var $cbo = p.$w.find('[name=pag_cuenta]');
					if(data!=null){
						for(var i=0; i<data.length; i++){		
							var id = data[i]._id.$id;
							var cod_banco = data[i].cod_banco;
							var nomb = data[i].nomb;
							var cod = data[i].cod;
							var moneda = data[i].moneda;
							$cbo.append('<option value="'+id+'" >'+cod+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
				},'json');
				/** /Cuenta Bancaria */
				$.post('ts/comp/get','id='+p.id,function(data){
					p.$w.find('[name=cod]').html(ciHelper.codigos(data.cod,6));
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=ref]').val(data.ref);
					if(data.comprobante){
							p.$w.find('[name=compr]').find('[value='+data.comprobante.tipo+']').attr('selected','selected');
							p.$w.find('[name=serie]').val(data.comprobante.serie);
							p.$w.find('[name=num]').val(data.comprobante.num);
					}
					/** Detalle del Gasto */
					if(data.items!=null){
						var total_pag = 0;
						var total = 0;
						for(i=0;i<data.items.length;i++){
							var $row = p.$w.find('.gridReference_det').clone();
							$li = $('li',$row);
							$li.eq(0).html(i+1);
							$li.eq(1).html(data.items[i].motivo);
							//$li.eq(3).html(p.cuentas[i].total_pago);
							$row.wrapInner('<a class="item" id="'+data.items[i]._id.$id+'" />');
							//$row.find('a').data('data',data );
							p.$w.find("[name=grid_det]").append( $row.children() );
							if(data.items[i].afectacion){
								p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+' li').eq(2).html(data.items[i].afectacion[0].organizacion.actividad.cod+" / "+data.items[i].afectacion[0].organizacion.componente.cod);
								p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+' li').eq(3).html(K.round(data.items[i].afectacion[0].monto,2)).css({"text-align":"right"});
								if(data.items[i].afectacion.length>1){
									for(j=1;j<data.items[i].afectacion.length;j++){
										var $row2 = p.$w.find('.gridReference_det').clone();
										$li2 = $('li',$row2);
										$li2.eq(2).html(data.items[i].afectacion[j].organizacion.actividad.cod+" / "+data.items[i].afectacion[j].organizacion.componente.cod);
										$li2.eq(3).html(K.round(data.items[i].afectacion[j].monto,2)).css({"text-align":"right"});
										$row2.wrapInner('<a class="item" />');
										p.$w.find("[name=grid_det]").append( $row2.children() );
									}
								}							
								total_pag = parseFloat(data.items[i].total_pago) + total_pag;
							}else{
								p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+'').remove();
							}
							total = parseFloat(data.items[i].total) + total;
						}
						var $row = p.$w.find('.gridReference_det').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
						$li.eq(3).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=grid_det]").append( $row.children() );
					}
					/** /Detalle del Gasto */
					p.$w.find('[name=monto_string]').html(ciHelper.monto2string.covertirNumLetras(total+""));
					p.$w.find('#totales .total li').eq(1).html("S/. "+K.round(total,2)).css({"text-align":"right"});
					/** Pagos */
					if(data.items!=null){
						var total_pag = 0;
						for(i=0;i<data.items.length;i++){
							var $row = p.$w.find('.gridReference_conc').clone();
							$li = $('li',$row);
							$li.eq(0).html(i+1);
							$li.eq(1).html(data.items[i].motivo);
							$li.eq(2).html(K.round(data.items[i].total_pago,2)).css({"text-align":"right"});
							$row.wrapInner('<a class="item" />');
							//$row.find('a').data('data',data );
							p.$w.find("[name=tab_p]").append( $row.children() );
							if(data.items[i].conceptos.length>0){
								for(j=0;j<data.items[i].conceptos.length;j++){
									if(data.items[i].conceptos[j].tipo=="P"){
										var $row2 = p.$w.find('.gridReference_conc').clone();
										$li2 = $('li',$row2);
										if(data.items[i].conceptos[j].concepto){
											$li2.eq(1).html(data.items[i].conceptos[j].concepto.nomb);
										}else{
											$li2.eq(1).html(data.items[i].conceptos[j].observ);
										}
										$li2.eq(2).html(K.round(data.items[i].conceptos[j].monto,2));
										$row2.wrapInner('<a class="item" />');
										p.$w.find("[name=tab_p]").append( $row2.children() );
									}
								}
							}
							total_pag = parseFloat(data.items[i].total_pago) + total_pag;
						}
						var $row = p.$w.find('.gridReference_conc').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total pagos").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=tab_p]").append( $row.children() );
						p.$w.find('#totales .pago li').eq(1).html("S/. "+K.round(total_pag,2)).css({"text-align":"right"});
					}
					/** /Pagos */
					
					/** Descuentos */
					if(data.items!=null){
						var total_des = 0;
						for(i=0;i<data.items.length;i++){
							if(parseFloat(data.items[i].total_desc)>0){
								var $row = p.$w.find('.gridReference_conc').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find("[name=tab_d] #conceptoo").length+1);
								$li.eq(1).html(data.items[i].motivo);
								$li.eq(2).html(K.round(data.items[i].total_desc,2)).css({"text-align":"right"});
								$row.wrapInner('<a class="item" id="conceptoo"/>');
								//$row.find('a').data('data',data );
								p.$w.find("[name=tab_d]").append( $row.children() );
								if(data.items[i].conceptos.length>0){
									for(j=0;j<data.items[i].conceptos.length;j++){
										if(data.items[i].conceptos[j].tipo=="D"){
											var $row2 = p.$w.find('.gridReference_conc').clone();
											$li2 = $('li',$row2);
											if(data.items[i].conceptos[j].concepto){
												$li2.eq(1).html(data.items[i].conceptos[j].concepto.nomb);
											}else{
												$li2.eq(1).html(data.items[i].conceptos[j].observ);
											}
											$li2.eq(2).html(K.round(data.items[i].conceptos[j].monto,2));
											$row2.wrapInner('<a class="item" />');
											p.$w.find("[name=tab_d]").append( $row2.children() );
										}
									}
								}
								total_des = parseFloat(data.items[i].total_desc) + total_des;
							}
						}
						var $row = p.$w.find('.gridReference_conc').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total descuentos").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_des,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=tab_d]").append( $row.children() );
						p.$w.find('#totales .desc li').eq(1).html("S/. "+K.round(total_des,2)).css({"text-align":"right"});
					}
					/** /Descuentos */
					
					/** Estadistica Objeto del Gasto */
					if(data.objeto_gasto!=null){
						for(i=0;i<data.objeto_gasto.length;i++){
							var $row2 = p.$w.find('.gridReference_est').clone();
							$li2 = $('li',$row2);
							$li2.eq(0).html(data.objeto_gasto[i].clasificador.cod);
							$li2.eq(1).html(data.objeto_gasto[i].clasificador.nomb);
							$li2.eq(2).html(K.round(data.objeto_gasto[i].monto,2)).css({"text-align":"right"});
							$row2.wrapInner('<a class="item" />');
							p.$w.find("#grid_est").append( $row2.children() );
						}
					}
					/** /Estadistica Objeto del Gasto */
					
					/** Retenciones y/o deducciones */
					/*if(data.items!=null){
						for(i=0;i<data.items.length;i++){
							if(data.items[i].conceptos.length>0){
								for(j=0;j<data.items[i].conceptos.length;j++){
									if(data.items[i].conceptos[j].tipo=="D"){
										var reten = p.$w.find('[name=grid_rete_dedu] #'+data.items[i].conceptos[j].concepto.cuenta._id.$id+'');
										if(reten.length>0){
											var monto = reten.data('monto');
											reten.find('li').eq(2).html(K.round(parseFloat(monto)+parseFloat(data.items[i].conceptos[j].monto),2)).css({"text-align":"right"});
											reten.data('monto',parseFloat(monto)+parseFloat(data.items[i].conceptos[j].monto));
										}else{
											var $row2 = p.$w.find('.gridReference_rete_dedu').clone();
											$li2 = $('li',$row2);
											$li2.eq(0).html(data.items[i].conceptos[j].concepto.cuenta.cod);
											$li2.eq(1).html(data.items[i].conceptos[j].concepto.cuenta.descr);
											$li2.eq(2).html(K.round(data.items[i].conceptos[j].monto,2)).css({"text-align":"right"});
											$row2.wrapInner('<a class="item" id="'+data.items[i].conceptos[j].concepto.cuenta._id.$id+'"/>');
											$row2.find('a').data('data',data.items[i].conceptos[j]).data('monto',data.items[i].conceptos[j].monto);
											p.$w.find("[name=grid_rete_dedu]").append( $row2.children() );
										}		
									}
								}
							}
						}
					}*/
					if(data.retenciones){
						for(i=0;i<data.retenciones.length;i++){
							var $row = p.$w.find('.gridReference_rete_dedu').clone();
							$li = $('li',$row);
							$li.eq(0).html(data.retenciones[i].cuenta.cod);
							$li.eq(1).html(data.retenciones[i].cuenta.descr);
							$li.eq(2).html(data.retenciones[i].monto);
							$row.wrapInner('<a class="item" />');
							var data2 = {
									cuenta:{
										_id:data.retenciones[i].cuenta._id.$id,
										cod:data.retenciones[i].cuenta.cod,
										descr:data.retenciones[i].cuenta.descr
									},
									monto:data.retenciones[i].monto,
									moneda:data.retenciones[i].moneda
							};
							$row.find('a').data('data',data2 );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_rete_dedu]").append( $row.children() );
						}
					}
					/** /Retenciones y/o deducciones */
					
					/** Formas de Pago */		
					if(data.beneficiarios!=null){
						var total_pag = 0;
						for(i=0;i<data.beneficiarios.length;i++){
							/* Cheques */
							if(data.beneficiarios[i].beneficiario){
								var $row = p.$w.find('.gridReference_forma_cheque').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find('[name=tab_c] .item').length+1);
								$li.eq(1).html(ciHelper.enti.formatName(data.beneficiarios[i].beneficiario));
								$li.eq(2).html(K.round(data.beneficiarios[i].monto,2)).css({"text-align":"right"});
								$li.eq(3).html('<input type="text" name="cheque" value="'+data.beneficiarios[i].cheque+'" size="9">');
								$row.wrapInner('<a class="item" id='+data.beneficiarios[i].beneficiario._id.$id+'/>');
								$row.find('a').data('data',data.beneficiarios[i].beneficiario).data('monto',data.beneficiarios[i].monto);
								p.$w.find("[name=tab_c]").append( $row.children() );
								/* Transferencias */
								var $row = p.$w.find('.gridReference_forma_cheque').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find('[name=tab_t] .item').length+1);
								$li.eq(1).html(ciHelper.enti.formatName(data.beneficiarios[i].beneficiario));
								$li.eq(2).html(K.round(data.beneficiarios[i].monto,2)).css({"text-align":"right"});
								$row.wrapInner('<a class="item" id='+data.beneficiarios[i].beneficiario._id.$id+'/>');
								$row.find('a').data('data',data.beneficiarios[i].beneficiario).data('monto',data.beneficiarios[i].monto);
								p.$w.find("[name=tab_t]").append( $row.children() );
								total_pag = parseFloat(data.beneficiarios[i].monto) + total_pag;
							}
						}
						var $row = p.$w.find('.gridReference_forma_trans').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item total" />');
						p.$w.find("[name=tab_t]").append( $row.children() );
						
						var $row = p.$w.find('.gridReference_forma_cheque').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item total" />');
						p.$w.find("[name=tab_c]").append( $row.children() );
					}
					
					/*if(data.beneficiarios!=null){
						for(i=0;i<data.beneficiarios.length;i++){
							p.$w.find('[name=tab_c] #'+data.beneficiarios[i].beneficiario._id.$id+' input').val(data.beneficiarios[i].cheque);
						}
					}*/		
					/** /Formas de Pago */
					
					/** Contabilidad Presupuestal */
					if(data.cont_presupuestal){
						for(i=0;i<data.cont_presupuestal.length;i++){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(data.cont_presupuestal[i].cuenta.cod);
							$li.eq(1).html(data.cont_presupuestal[i].cuenta.descr);
							if(data.cont_presupuestal[i].tipo=="D") $li.eq(2).html(data.cont_presupuestal[i].monto);
							else if(data.cont_presupuestal[i].tipo=="H") $li.eq(3).html(data.cont_presupuestal[i].monto);
							$row.wrapInner('<a class="item" />');
							var data2 = {
									cuenta:{
										_id:data.cont_presupuestal[i].cuenta._id.$id,
										cod:data.cont_presupuestal[i].cuenta.cod,
										descr:data.cont_presupuestal[i].cuenta.descr
									},
									tipo:data.cont_presupuestal[i].tipo,
									monto:data.cont_presupuestal[i].monto,
									moneda:data.cont_presupuestal[i].moneda
							};
							$row.find('a').data('data',data2 );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_cont_pres]").append( $row.children() );
						}
					}
					/** /Contabilidad Presupuestal */
					
					/** Contabilidad Patrimonial */
					if(data.cont_patrimonial){
						for(i=0;i<data.cont_patrimonial.length;i++){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(data.cont_patrimonial[i].cuenta.cod);
							$li.eq(1).html(data.cont_patrimonial[i].cuenta.descr);
							if(data.cont_patrimonial[i].tipo=="D") $li.eq(2).html(K.round(data.cont_patrimonial[i].monto,2));
							else if(data.cont_patrimonial[i].tipo=="H") $li.eq(3).html(K.round(data.cont_patrimonial[i].monto,2));
							$row.wrapInner('<a class="item" />');
							var data2 = {
									cuenta:{
										_id:data.cont_patrimonial[i].cuenta._id.$id,
										cod:data.cont_patrimonial[i].cuenta.cod,
										descr:data.cont_patrimonial[i].cuenta.descr
									},
									tipo:data.cont_patrimonial[i].tipo,
									monto:data.cont_patrimonial[i].monto,
									moneda:data.cont_patrimonial[i].moneda
							};
							$row.find('a').data('data',data2 );
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							p.$w.find("[name=grid_cont_patr]").append( $row.children() );
						}
					}
					/** /Contabilidad Patrimonial */
					/** Codificacion programatica */
					p.$w.find('[name=btnAsignar]').show().click(function(){
						var $row = $(this).closest('.item');
						tsCtppPen.windowAsignar2({callback: function(data2){
							$row.find('[name=asignaciones]').empty();
							for(i=0;i<data2.asignaciones.length;i++){
								p.push_cod_prog(data2.asignaciones[i].organizacion.actividad._id.$id,data2.asignaciones[i].organizacion.componente._id.$id);
							}
							//p.cod_prog();
							//$row.find('[name=asignaciones]').data('asig',data);
						}});
					}).button();
					if(data.cod_programatica!=null){
						if(data.cod_programatica.length>0){
							for(i=0;i<data.cod_programatica.length;i++){
								p.push_cod_prog(data.cod_programatica[i].actividad._id.$id,data.cod_programatica[i].componente._id.$id);
							}
						}
					}
					/** /Codificacion programatica */
					var $row = p.$w.find('.gridReference_cod').clone();
					$li = $('li',$row);
					$row.wrapInner('<a class="item" />');
					p.$w.find("[name=grid_cod]").append( $row.children() );
					$.post('pr/fuen/all',function(data3){
						var $cbo2 = p.$w.find('[name=fuente]');
						if(data3!=null){
							for(var i=0; i<data3.length; i++){
								var rubro = data3[i].rubro;
								var cod = data3[i].cod;
								var id = data3[i]._id.$id;
								$cbo2.append('<option value="'+id+'" >'+rubro+'</option>');
								$cbo2.find('option:last').data('data',data3[i]);
							}
						}
					},'json');					
					p.$w.find('[name=pag_cuenta]').selectVal(data.cuenta_banco._id.$id);
					p.$w.find('[name=fuente]').selectVal(data.fuente._id.$id);
					if(!data.comprobante){
						p.$w.find('[name=compr]').hide();
					}
				},'json');
				/** Add Beneficiarios */
				p.$w.find('[name=btnSelectBene]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=add_bene]').html(ciHelper.enti.formatName(data)).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAddBene]').click(function(){
					if(p.$w.find('[name=add_bene]').data('data')==null){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un beneficiario!',type: 'error'});
						return p.$w.find('[name=btnSelectBene]').click();
					}
					if(p.$w.find('[name=add_monto]').val()==""){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
						return p.$w.find('[name=add_monto]').focus();
					}
					/* Cheques */
					var $row = p.$w.find('.gridReference_forma_cheque').clone();
					$li = $('li',$row);
					$li.eq(0).html(p.$w.find('[name=tab_c] .item').length+1);
					$li.eq(1).html(ciHelper.enti.formatName(p.$w.find('[name=add_bene]').data('data')));
					$li.eq(2).html(K.round(p.$w.find('[name=add_monto]').val(),2)).css({"text-align":"right"});
					$li.eq(3).html('<input type="text" name="cheque" size="9">');
					$row.wrapInner('<a class="item" id='+p.$w.find('[name=add_bene]').data('data')._id.$id+'/>');
					var bene = {
						_id: p.$w.find('[name=add_bene]').data('data')._id,
						tipo_enti: p.$w.find('[name=add_bene]').data('data').tipo_enti,
						nomb: p.$w.find('[name=add_bene]').data('data').nomb
					};
					if(bene.tipo_enti=="P"){
						bene.appat = p.$w.find('[name=add_bene]').data('data').appat;
						bene.apmat = p.$w.find('[name=add_bene]').data('data').apmat;
					}
					$row.find('a').data('data',bene).data('monto',p.$w.find('[name=add_monto]').val());
					p.$w.find("[name=tab_c] .total").before( $row.children() );
					/* Transferencias */
					var $row = p.$w.find('.gridReference_forma_cheque').clone();
					$li = $('li',$row);
					$li.eq(0).html(p.$w.find('[name=tab_t] .item').length+1);
					$li.eq(1).html(ciHelper.enti.formatName(p.$w.find('[name=add_bene]').data('data')));
					$li.eq(2).html(K.round(p.$w.find('[name=add_monto]').val(),2)).css({"text-align":"right"});
					$row.wrapInner('<a class="item" id='+p.$w.find('[name=add_bene]').data('data')._id.$id+'/>');
					$row.find('a').data('data',bene).data('monto',p.$w.find('[name=add_monto]').val());;
					p.$w.find("[name=tab_t] .total").before( $row.children() );
					p.sumBene();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				/** ./Add Beneficiarios */
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		p.cod_prog = function(){
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(0).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(0).html(p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('programas'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('programas').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(1).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(1).html(p.$w.find('[name=grid_cod] .item').eq(0).data('programas')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(2).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(2).html(p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(3).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(3).html(p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos')[0]);
			}
			$.unique(p.$w.find('[name=grid_cod] .item').eq(0).data('obras'));
			if(p.$w.find('[name=grid_cod] .item').eq(0).data('obras').length>1){
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(4).html("V");
			}else{
				p.$w.find('[name=grid_cod] .item').eq(0).find('li').eq(4).html(p.$w.find('[name=grid_cod] .item').eq(0).data('obras')[0]);
			}
		};
		p.push_cod_prog = function(actividad,componente){
			$.post('ts/comp/cod_prog','actividad='+actividad+'&componente='+componente,function(res){
				var cod_prog = p.$w.find('[name=grid_cod] .item').eq(0).data('data');
				if(cod_prog==null){
					var new_cod = [];
					new_cod.push(res);
					p.$w.find('[name=grid_cod] .item').eq(0).data('data',new_cod);
				}else{
					cod_prog.push(res);
					p.$w.find('[name=grid_cod] .item').eq(0).data('data',cod_prog);
				}
				var pliegos = p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos');
				if(pliegos !=null){
					pliegos.push(res.pliego.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos',pliegos);
				}else{
					var pliego_new = [];
					pliego_new.push(res.pliego.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('pliegos',pliego_new);
				}
				
				var programas = p.$w.find('[name=grid_cod] .item').eq(0).data('programas');
				if(programas !=null){
					programas.push(res.programa.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('programas',programas);
				}else{
					var programa_new = [];
					programa_new.push(res.programa.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('programas',programa_new);
				}
				
				var subprogramas = p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas');
				if(subprogramas !=null){
					subprogramas.push(res.subprograma.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas',subprogramas);
				}else{
					var subprograma_new = [];
					subprograma_new.push(res.subprograma.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('subprogramas',subprograma_new);
				}
				
				var proyectos = p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos');
				if(proyectos !=null){
					proyectos.push(res.proyecto.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos',proyectos);
				}else{
					var proyecto_new = [];
					proyecto_new.push(res.proyecto.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('proyectos',proyecto_new);
				}
				
				var obras = p.$w.find('[name=grid_cod] .item').eq(0).data('obras');
				if(obras !=null){
					obras.push(res.obra.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('obras',obras);
				}else{
					var obra_new = [];
					obra_new.push(res.obra.cod);
					p.$w.find('[name=grid_cod] .item').eq(0).data('obras',obra_new);
				}
				p.cod_prog();
			},'json');	
		};
		new K.Window({
			id: 'windowDetailstsComp'+p.id,
			title: 'Ver Comprobante de Pago',
			contentURL: 'ts/comp/details',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Imprimir": function(){
					var url = 'ts/comp/print?id='+p.id;
					K.windowPrint({
						id:'windowctCbanPrint',
						title: "Imprimir Comprobante de Pago",
						url: url
					});
				},
				"Pagar":function(){
					K.closeWindow(p.$w.attr('id'));
					tsCompNue.windowPagar({id:p.id});
				},
				"Anular":function(){
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');					
					var data = {
							_id: p.id,
							data: p.data
						};
						K.sendingInfo();
						$.post('ts/comp/anu2',data,function(){
							K.clearNoti();
							K.notification({title: 'Comprobante Anulado',text: 'El Comprobante de pago seleccionado ha sido anulado con &eacute;xito!'});
							K.closeWindow(p.$w.attr('id'));
							tsCompAll.init();
							tsCompNue.windowDetails({id:p.id});					
						});					
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailstsComp'+p.id);
				K.block({$element: p.$w});
				p.$w.find('#tabs_conceptos').tabs();				
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(1).click();
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(0).click();	
				/** /Codificacion programatica */
				$.post('ts/comp/get','id='+p.id,function(data){				
					//p.estado = data.estado;
					p.data = data;
					if(data.estado=="X"||data.estado=="C"){
						p.$w.parent().find('.ui-dialog-buttonpane button').eq(1).button('disable');
						p.$w.parent().find('.ui-dialog-buttonpane button').eq(2).button('disable');
					}
					if(data.tipo_pago=="T"){
						var dis_form = 0;
					}else{
						var dis_form = 1;
					}
					p.$w.find('#tabs_forma').tabs({
						select: function(e, ui) {
	                    	var thistab = ui;
	                    	//runMethod(thistab.index);
	                    	p.$w.find('#tabs_forma').data('data',thistab.index);
	                	},
	                	disabled: [dis_form]
					});
					p.$w.find('[name=cod]').html(ciHelper.codigos(data.cod,6));
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=ref]').html(data.ref);
					if(data.comprobante){
						p.$w.find('[name=compr]').find('[value='+data.comprobante.tipo+']').attr('selected','selected');
						p.$w.find('[name=serie]').replaceWith(data.comprobante.serie);
						p.$w.find('[name=num]').replaceWith(data.comprobante.num);
						p.$w.find('[name=compr]').attr('disabled','disabled');
					}else{
						p.$w.find('[name=compr]').closest('tr').remove();
					}
					/** Detalle del Gasto */
					if(data.items!=null){
						var total_pag = 0;
						var total = 0;
						for(i=0;i<data.items.length;i++){
							var $row = p.$w.find('.gridReference_det').clone();
							$li = $('li',$row);
							$li.eq(0).html(i+1);
							$li.eq(1).html(data.items[i].motivo);
							//$li.eq(3).html(p.cuentas[i].total_pago);
							$row.wrapInner('<a class="item" id="'+data.items[i]._id.$id+'" />');
							//$row.find('a').data('data',data );
							p.$w.find("[name=grid_det]").append( $row.children() );
							if(data.items[i].afectacion){
								p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+' li').eq(2).html(data.items[i].afectacion[0].organizacion.actividad.cod+" / "+data.items[i].afectacion[0].organizacion.componente.cod);
								p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+' li').eq(3).html(K.round(data.items[i].afectacion[0].monto,2));
								if(data.items[i].afectacion.length>1){
									for(j=1;j<data.items[i].afectacion.length;j++){
										var $row2 = p.$w.find('.gridReference_det').clone();
										$li2 = $('li',$row2);
										$li2.eq(2).html(data.items[i].afectacion[j].organizacion.actividad.cod+" / "+data.items[i].afectacion[j].organizacion.componente.cod);
										$li2.eq(3).html(K.round(data.items[i].afectacion[j].monto,2));
										$row2.wrapInner('<a class="item" />');
										p.$w.find("[name=grid_det]").append( $row2.children() );
									}
								}							
								total_pag = parseFloat(data.items[i].total_pago) + total_pag;
							}else{
								p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+'').remove();
							}
							total = parseFloat(data.items[i].total) + total;
						}
						var $row = p.$w.find('.gridReference_det').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
						$li.eq(3).html(K.round(total_pag,2));
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=grid_det]").append( $row.children() );
					}
					/** /Detalle del Gasto */
					p.$w.find('[name=monto_string]').html(ciHelper.monto2string.covertirNumLetras(total+""));
					p.$w.find('#totales .total li').eq(1).html("S/. "+K.round(total,2));
					/** Pagos */
					if(data.items!=null){
						var total_pag = 0;
						for(i=0;i<data.items.length;i++){
							var $row = p.$w.find('.gridReference_conc').clone();
							$li = $('li',$row);
							$li.eq(0).html(i+1);
							$li.eq(1).html(data.items[i].motivo);
							$li.eq(2).html(K.round(data.items[i].total_pago,2)).css({"text-align":"right"});
							$row.wrapInner('<a class="item" />');
							//$row.find('a').data('data',data );
							p.$w.find("[name=tab_p]").append( $row.children() );
							if(data.items[i].conceptos.length>0){
								for(j=0;j<data.items[i].conceptos.length;j++){
									if(data.items[i].conceptos[j].tipo=="P"){
										var $row2 = p.$w.find('.gridReference_conc').clone();
										$li2 = $('li',$row2);
										if(data.items[i].conceptos[j].concepto){
											$li2.eq(1).html(data.items[i].conceptos[j].concepto.nomb);
										}else{
											$li2.eq(1).html(data.items[i].conceptos[j].observ);
										}
										$li2.eq(2).html(K.round(data.items[i].conceptos[j].monto,2));
										$row2.wrapInner('<a class="item" />');
										p.$w.find("[name=tab_p]").append( $row2.children() );
									}
								}
							}
							total_pag = parseFloat(data.items[i].total_pago) + total_pag;
						}
						var $row = p.$w.find('.gridReference_conc').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total pagos").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=tab_p]").append( $row.children() );
						p.$w.find('#totales .pago li').eq(1).html("S/. "+K.round(total_pag,2));
					}
					/** /Pagos */
					
					/** Descuentos */
					if(data.items!=null){
						var total_des = 0;
						for(i=0;i<data.items.length;i++){
							if(parseFloat(data.items[i].total_desc)>0){
								var $row = p.$w.find('.gridReference_conc').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find("[name=tab_d] #conceptoo").length+1);
								$li.eq(1).html(data.items[i].motivo);
								$li.eq(2).html(K.round(data.items[i].total_desc,2)).css({"text-align":"right"});
								$row.wrapInner('<a class="item" id="conceptoo"/>');
								//$row.find('a').data('data',data );
								p.$w.find("[name=tab_d]").append( $row.children() );
								if(data.items[i].conceptos.length>0){
									for(j=0;j<data.items[i].conceptos.length;j++){
										if(data.items[i].conceptos[j].tipo=="D"){
											var $row2 = p.$w.find('.gridReference_conc').clone();
											$li2 = $('li',$row2);
											if(data.items[i].conceptos[j].concepto){
												$li2.eq(1).html(data.items[i].conceptos[j].concepto.nomb);
											}else{
												$li2.eq(1).html(data.items[i].conceptos[j].observ);
											}
											$li2.eq(2).html(K.round(data.items[i].conceptos[j].monto,2));
											$row2.wrapInner('<a class="item" />');
											p.$w.find("[name=tab_d]").append( $row2.children() );
										}
									}
								}
								total_des = parseFloat(data.items[i].total_desc) + total_des;
							}
						}
						var $row = p.$w.find('.gridReference_conc').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total descuentos").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_des,2));
						$row.wrapInner('<a class="item" />');
						//$row.find('a').data('data',data );
						p.$w.find("[name=tab_d]").append( $row.children() );
						p.$w.find('#totales .desc li').eq(1).html("S/. "+K.round(total_des,2));
					}
					/** /Descuentos */
					
					/** Estadistica Objeto del Gasto */
					if(data.objeto_gasto!=null){
						for(i=0;i<data.objeto_gasto.length;i++){
							var $row2 = p.$w.find('.gridReference_est').clone();
							$li2 = $('li',$row2);
							$li2.eq(0).html(data.objeto_gasto[i].clasificador.cod);
							$li2.eq(1).html(data.objeto_gasto[i].clasificador.nomb);
							$li2.eq(2).html(K.round(data.objeto_gasto[i].monto,2));
							$row2.wrapInner('<a class="item" />');
							p.$w.find("#grid_est").append( $row2.children() );
						}
					}
					/** /Estadistica Objeto del Gasto */
					
					/** Retenciones y/o deducciones */
					if(data.items!=null){
						for(i=0;i<data.items.length;i++){
							if(data.items[i].conceptos.length>0){
								for(j=0;j<data.items[i].conceptos.length;j++){
									if(data.items[i].conceptos[j].tipo=="D"){
										var reten = p.$w.find('[name=grid_rete_dedu] #'+data.items[i].conceptos[j].concepto.cuenta._id.$id+'');
										if(reten.length>0){
											var monto = reten.data('monto');
											reten.find('li').eq(2).html(K.round(parseFloat(monto)+parseFloat(data.items[i].conceptos[j].monto),2)).css({"text-align":"right"});
											reten.data('monto',parseFloat(monto)+parseFloat(data.items[i].conceptos[j].monto));
										}else{
											var $row2 = p.$w.find('.gridReference_rete_dedu').clone();
											$li2 = $('li',$row2);
											$li2.eq(0).html(data.items[i].conceptos[j].concepto.cuenta.cod);
											$li2.eq(1).html(data.items[i].conceptos[j].concepto.cuenta.descr);
											$li2.eq(2).html(K.round(data.items[i].conceptos[j].monto,2)).css({"text-align":"right"});
											$row2.wrapInner('<a class="item" id="'+data.items[i].conceptos[j].concepto.cuenta._id.$id+'"/>');
											$row2.find('a').data('data',data.items[i].conceptos[j]).data('monto',data.items[i].conceptos[j].monto);
											p.$w.find("[name=grid_rete_dedu]").append( $row2.children() );
										}		
									}
								}
							}
						}
					}
					/** /Retenciones y/o deducciones */
					
					/** Formas de Pago */		
					if(data.beneficiarios!=null){
						var total_pag = 0;
						for(i=0;i<data.beneficiarios.length;i++){
							/* Cheques */
							if(data.beneficiarios[i].beneficiario){
								var $row = p.$w.find('.gridReference_forma_cheque').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find('[name=tab_c] .item').length+1);
								$li.eq(1).html(ciHelper.enti.formatName(data.beneficiarios[i].beneficiario));
								$li.eq(2).html(K.round(data.beneficiarios[i].monto,2)).css({"text-align":"right"});
								$li.eq(3).html('<input type="text" readonly="readonly" name="cheque" value="'+data.beneficiarios[i].cheque+'" size="9">');
								$row.wrapInner('<a class="item" id='+data.beneficiarios[i].beneficiario._id.$id+'/>');
								$row.find('a').data('data',data.beneficiarios[i].beneficiario).data('monto',data.beneficiarios[i].monto);
								p.$w.find("[name=tab_c]").append( $row.children() );
								/* Transferencias */
								var $row = p.$w.find('.gridReference_forma_cheque').clone();
								$li = $('li',$row);
								$li.eq(0).html(p.$w.find('[name=tab_t] .item').length+1);
								$li.eq(1).html(ciHelper.enti.formatName(data.beneficiarios[i].beneficiario));
								$li.eq(2).html(K.round(data.beneficiarios[i].monto,2)).css({"text-align":"right"});
								$row.wrapInner('<a class="item" id='+data.beneficiarios[i].beneficiario._id.$id+'/>');
								$row.find('a').data('data',data.beneficiarios[i].beneficiario).data('monto',data.beneficiarios[i].monto);
								p.$w.find("[name=tab_t]").append( $row.children() );
								total_pag = parseFloat(data.beneficiarios[i].monto) + total_pag;
							}
						}
						var $row = p.$w.find('.gridReference_forma_trans').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item total" />');
						p.$w.find("[name=tab_t]").append( $row.children() );
						
						var $row = p.$w.find('.gridReference_forma_cheque').clone();
						$li = $('li',$row);
						$li.eq(0).addClass('ui-state-default ui-button-text-only');
						$li.eq(1).html("Total a Pagar").addClass('ui-state-default ui-button-text-only');
						$li.eq(2).html(K.round(total_pag,2)).css({"text-align":"right"});
						$row.wrapInner('<a class="item total" />');
						p.$w.find("[name=tab_c]").append( $row.children() );
					}
					/** /Formas de Pago */
					
					/** Contabilidad Presupuestal */
					if(data.cont_presupuestal){
						for(i=0;i<data.cont_presupuestal.length;i++){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(data.cont_presupuestal[i].cuenta.cod);
							$li.eq(1).html(data.cont_presupuestal[i].cuenta.descr);
							if(data.cont_presupuestal[i].tipo=="D") $li.eq(2).html(K.round(data.cont_presupuestal[i].monto,2));
							else if(data.cont_presupuestal[i].tipo=="H") $li.eq(3).html(K.round(data.cont_presupuestal[i].monto,2));
							$row.wrapInner('<a class="item" />');
							p.$w.find("[name=grid_cont_pres]").append( $row.children() );
						}
					}
					/** /Contabilidad Presupuestal */
					
					/** Contabilidad Patrimonial */
					if(data.cont_patrimonial){
						for(i=0;i<data.cont_patrimonial.length;i++){
							var $row = p.$w.find('.gridReference_cont').clone();
							$li = $('li',$row);
							$li.eq(0).html(data.cont_patrimonial[i].cuenta.cod);
							$li.eq(1).html(data.cont_patrimonial[i].cuenta.descr);
							if(data.cont_patrimonial[i].tipo=="D") $li.eq(2).html(K.round(data.cont_patrimonial[i].monto,2));
							else if(data.cont_patrimonial[i].tipo=="H") $li.eq(3).html(K.round(data.cont_patrimonial[i].monto,2));
							$row.wrapInner('<a class="item" />');
							p.$w.find("[name=grid_cont_patr]").append( $row.children() );
						}
					}
					/** /Contabilidad Patrimonial */
					/** Codificacion programatica */
					var $row = p.$w.find('.gridReference_cod').clone();
					$li = $('li',$row);
					$row.wrapInner('<a class="item" />');
					p.$w.find("[name=grid_cod]").append( $row.children() );
					if(data.cod_programatica!=null){
						if(data.cod_programatica.length>0){
							for(i=0;i<data.cod_programatica.length;i++){
								p.push_cod_prog(data.cod_programatica[i].actividad._id.$id,data.cod_programatica[i].componente._id.$id);
							}
						}
					}
					/** /Codificacion programatica */
					p.$w.find('[name=pag_cuenta]').html(data.cuenta_banco.cod+" "+data.cuenta_banco.nomb);
					if(data.tipo_pago=="T"){
						p.$w.find('#tabs_forma ul').find('li').eq(1).find('a').click();
					}
					p.$w.find('[name=fuente]').html(data.fuente.cod);
					if(!data.comprobante){
						p.$w.find('[name=compr]').hide();
					}
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowPagar: function(p){
		new K.Window({
			id: 'windowPagartsComp',
			title: 'Pagar Comprobante de Pago',
			contentURL: 'ts/comp/pagar',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
			    "Guardar":function(){
					data = new Object;
					data._id = p.id;
					data.estado_sunat = "1";
					data.cod_operacion = p.$w.find('[name=cod_oper]').html();
					data.cod = p.$w.find('[name=cod]').html();
					data.descr = p.$w.find('[name=mov_descr]').val();
					var entidad = p.$w.find('[name=mov_tdoc] :selected').data('data');
					data.entidades = new Object;
					if(entidad){
						data.entidades.tipo_doc = entidad.cod;
						data.entidades.num_doc = entidad.val;
						data.entidades.nomb = p.$w.find('[name=mov_bene]').html();
					}else{
						data.entidades.tipo_doc = "-";
						data.entidades.num_doc = "-";
						data.entidades.nomb = "Varios";
					}
					var entidades = p.$w.find('[name=mov_bene]').data('data');
					data.entidades.entidad = new Array();
					for(i=0;i<entidades.length;i++){
						var enti = new Object;
						enti = ciHelper.enti.dbRel(entidades[i].beneficiario);
						enti.docident = entidades[i].beneficiario.docident;
						data.entidades.entidad.push(enti);
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta!=null){
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
						data.tipo = "H";
						data.monto = p.$w.find('[name=monto]').html();
					}else{
						p.$w.find('[name=cuenta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Cuenta Contable!',type: 'error'});
					}		
					var medio_pago = p.$w.find('[name=medio_pago] :selected').data('data');
					data.medio_pago = new Object;
					data.medio_pago._id = medio_pago._id.$id;
					data.medio_pago.cod = medio_pago.cod;
					data.medio_pago.descr = medio_pago.descr;
					data.monto_original = p.total;
					data.organizacion = p.organizacion;
					K.sendingInfo();
					p.$w.parent().find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/comp/pagar_save',data,function(rpta){
						K.clearNoti();
						if(rpta.error!=true){
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Comprobante fue registrado con &eacute;xito!'});
							tsCompNue.windowDetails({id:p.id});
							tsCompNue.init();
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.closeWindow(p.$w.attr('id'));
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe crear un saldo inicial del Efectivo!',
								type: 'error'
							});
						}
					},'json');	
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPagartsComp');
				K.block({$element: p.$w});
				p.$w.find('#tabs_conceptos').tabs();
				p.$w.find('#tabs_forma').tabs({
					select: function(e, ui) {
                    	var thistab = ui;
                    	//runMethod(thistab.index);
                    	p.$w.find('#tabs_forma').data('data',thistab.index);
                	}
				});
				$.post('ts/tipo/all',function(data3){
					var $cbo2 = p.$w.find('[name=medio_pago]');
					if(data3!=null){
						for(var i=0; i<data3.length; i++){
							var descr = data3[i].descr;
							var cod = data3[i].cod;
							var id = data3[i]._id.$id;
							$cbo2.append('<option value="'+id+'" >'+descr+'</option>');
							$cbo2.find('option:last').data('data',data3[i]);
						}
					}
				},'json');
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(1).click();
				p.$w.find('#tabs_forma .ui-tabs-nav a').eq(0).click();
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				/** /Codificacion programatica */
				$.post('ts/comp/get','id='+p.id,function(data){
					if(data.comprobante){
						p.$w.find('[name=compr]').find('[value='+data.comprobante.tipo+']').attr('selected','selected');
						p.$w.find('[name=serie]').val(data.comprobante.serie);
						p.$w.find('[name=num]').val(data.comprobante.num);
					}else{
						p.$w.find('[name=compr]').closest('tr').remove();
					}
					p.$w.find('[name=cod]').html(ciHelper.codigos(data.cod,6));
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=ref]').html(data.ref);
					p.$w.find('[name=cod_oper]').html("CP "+ciHelper.codigos(data.cod,6));
					p.$w.find('[name=fec_oper]').html(ciHelper.dateFormat(data.fecreg));
					p.$w.find('[name=mov_bene]').data('data',data.beneficiarios);
					if(data.beneficiarios.length<=1||!data.beneficiarios){
						p.$w.find('[name=mov_bene]').html(ciHelper.enti.formatName(data.beneficiarios[0].beneficiario));
						if(ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario).length>0){
							$select = p.$w.find('[name=mov_tdoc]');
							for(i=0;i<ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario).length;i++){
								$select.append('<option value="'+ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i].key+'">'+ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i].nomb+' ('+ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i].cod+')</option>');
								$select.find('option:last').data('data',ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i]);
							}
							$select.find('option:first').attr('selected','selected');
							p.$w.find('[name=mov_ndoc]').html(ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[0].val);
						}
						p.$w.find('[name=mov_tdoc]').change(function(){
							var tdoc = $(this).find('option:selected').data('data');
							p.$w.find('[name=mov_ndoc]').html(tdoc.key+' - '+tdoc.val);
						});
					}else{
						p.$w.find('[name=mov_bene]').html("Varios");
						p.$w.find('[name=mov_tdoc]').append('<option value="-">--</option>');
						p.$w.find('[name=mov_ndoc]').html("--");
					}
					p.$w.find('[name=forma_pago]').html(tsCompNue.tipo_pago[data.tipo_pago].descr);
					p.$w.find('[name=cuen_ban]').html(data.cuenta_banco.cod+" &laquo;"+data.cuenta_banco.nomb+"&raquo;");
					var cheques = '';
					for(i=0;i<data.beneficiarios.length;i++){
						cheques = cheques+", "+data.beneficiarios[i].cheque;
					}
					if(data.tipo_pago=="C"){
						p.$w.find('[name=doc_sust]').html("Cheque(s)"+cheques);
						p.$w.find('[name=tdoc_sust_tr]').remove();
						p.$w.find('[name=doc_sust_trans_tr]').remove();
					}else if(data.tipo_pago=="T"){
						
					}			
					/** Detalle del Gasto */
					if(data.items!=null){
						var total_pag = 0;
						var total = 0;
						p.organizacion = new Array;
						for(i=0;i<data.items.length;i++){
							total = parseFloat(data.items[i].total) + total;
							/** Organizaciones Afectadas */
							if(data.items[i].afectacion){						
								for(j=1;j<data.items[i].afectacion.length;j++){
									p.organizacion.push({
										_id:data.items[i].afectacion[j].organizacion._id.$id,
										nomb:data.items[i].afectacion[j].organizacion.nomb
									});
								}										
							}
							/** /Organizaciones Afectadas */
						}
						p.total = total;
					}
					/** /Detalle del Gasto */
					p.$w.find('[name=monto_string]').html(ciHelper.monto2string.covertirNumLetras(total+""));
					p.$w.find('#totales .total li').eq(1).html("S/. "+total);
					p.$w.find('[name=monto]').html(total);
					/** Pagos */
					if(data.items!=null){
						var total_pag = 0;
						for(i=0;i<data.items.length;i++){
							total_pag = parseFloat(data.items[i].total_pago) + total_pag;
						}
						p.$w.find('#totales .pago li').eq(1).html("S/. "+total_pag);					
					}
					/** /Pagos */
					
					/** Descuentos */
					if(data.items!=null){
						var total_des = 0;
						for(i=0;i<data.items.length;i++){
							if(parseFloat(data.items[i].total_desc)>0){						
								total_des = parseFloat(data.items[i].total_desc) + total_des;
							}
						}
						p.$w.find('#totales .desc li').eq(1).html("S/. "+total_des);
					}
					/** /Descuentos */								
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowSelectCheque: function(p){
		p.calc = function(){
			if(p.$w.find('.gridBody .item').length>0){
				var total = 0;
				for(i=0;i<p.$w.find('.gridBody .item').length;i++){
					if(p.$w.find('.gridBody .item').eq(i).find('input').is(':checked')){
						total = total + parseFloat(p.$w.find('.gridBody .item').eq(i).find('li').eq(5).html());
					}
				}
				p.$w.find('.gridBody .total').find('li').eq(5).html(total);
			}
		};
		p.search = function(params){
			params.estado = "C";
			params.cuenta = p.cuenta;
			params.tipo_pago = "C";
			params.ano = p.ano;
			params.mes = p.mes;
			var $row1 = p.$w.find('.gridReference_cheques').clone();
			$li1 = $('li',$row1);	
			$li1.eq(0).addClass('ui-state-default ui-button-text-only');
			$li1.eq(1).addClass('ui-state-default ui-button-text-only');
			$li1.eq(2).addClass('ui-state-default ui-button-text-only');
			$li1.eq(3).html( "Total" ).addClass('ui-state-default ui-button-text-only');
			$li1.eq(4).html("");
			$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
			p.$w.find(".gridBody").append( $row1.children() );
			
			$.post('ts/comp/all_cheques',params,function(data){
				if ( data.items ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						for(j=0;j<result.beneficiarios.length;j++){
							var $row = p.$w.find('.gridReference_cheques').clone();
							$li = $('li',$row);	
							$li.eq(0).html( ciHelper.dateFormat(result.fecreg) );
							$li.eq(1).html( result.beneficiarios[j].cheque );
							$li.eq(2).html( ciHelper.enti.formatName(result.beneficiarios[j].beneficiario) );
							$li.eq(3).html( result.beneficiarios[j].monto );
							var dataa = {
									fecha: ciHelper.dateFormat(result.fecreg),
									cheque : result.beneficiarios[j].cheque,
									detalle : result.beneficiarios[j].beneficiario,
									monto : result.beneficiarios[j].monto
							};
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',dataa);
							p.$w.find(".gridBody .total").before( $row.children() );
						}		
					}
				} else {
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encotrarón cheques en este periodo y cuenta bancaria!',type: 'error'});
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowSelecttsCompCheque',
			title: 'Seleccionar Cheques',
			contentURL: 'ts/comp/select_cheques',
			icon: 'ui-icon-search',
			width: 600,
			height: 400,
			buttons: {
				"Guardar": function(){
					if(p.$w.find('.gridBody .item').length>0){
						var total = 0;
						var data = new Object;
						data.cheques = new Array;
						for(i=0;i<p.$w.find('.gridBody .item').length;i++){
							if(p.$w.find('.gridBody .item').eq(i).find('input').is(':checked')){
								total = total + parseFloat(p.$w.find('.gridBody .item').eq(i).find('li').eq(5).html());
								data.cheques.push(p.$w.find('.gridBody .item').eq(i).data('data'));
							}
						}
						data.total = total;
					}
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelecttsCompCheque');
				K.block({$element: p.$w});
				p.search({page: 1});
				p.$w.find('[name=checkitem]').live('click',function(){
					var $file = $(this).closest('.item');
					if($(this).is(':checked')){
						$file.find('li').eq(5).html($file.find('li').eq(3).html());
					}else{
						$file.find('li').eq(5).html("");
					}
					p.calc();
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowRectiCheque: function(p){
		p.calc = function(){
			if(p.$w.find('.gridBody .item').length>0){
				var total = 0;
				for(i=0;i<p.$w.find('.gridBody .item').length;i++){
					if(p.$w.find('.gridBody .item').eq(i).find('input:eq(0)').is(':checked')){
						var get_monto = p.$w.find('.gridBody .item').eq(i).find('li').eq(6).html();
						if(get_monto=="")get_monto = 0;
						total = total + parseFloat(get_monto);
					}
				}
				p.$w.find('.gridBody .total').find('li').eq(6).html(K.round(total,2));
			}
		};
		p.search = function(params){
			params.estado = "C";
			params.cuenta = p.cuenta;
			params.tipo_pago = "C";
			params.ano = p.ano;
			params.mes = p.mes;
			var $row1 = p.$w.find('.gridReference_cheques').clone();
			$li1 = $('li',$row1);	
			$li1.eq(0).addClass('ui-state-default ui-button-text-only');
			$li1.eq(1).addClass('ui-state-default ui-button-text-only');
			$li1.eq(2).addClass('ui-state-default ui-button-text-only');
			$li1.eq(3).addClass('ui-state-default ui-button-text-only');
			$li1.eq(4).html("").addClass('ui-state-default ui-button-text-only');
			$li1.eq(5).html( "Total" ).addClass('ui-state-default ui-button-text-only');
			$li1.eq(6).html("");
			$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
			p.$w.find(".gridBody").append( $row1.children() );
			
			$.post('ts/comp/all_cheques',params,function(data){
				if ( data.items ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						for(j=0;j<result.beneficiarios.length;j++){
							var $row = p.$w.find('.gridReference_cheques').clone();
							$li = $('li',$row);	
							$li.eq(0).html( ciHelper.dateFormat(result.fecreg) );
							$li.eq(1).html( result.beneficiarios[j].cheque );
							$li.eq(2).html( ciHelper.enti.formatName(result.beneficiarios[j].beneficiario) );
							$li.eq(3).html( result.beneficiarios[j].monto );
							var dataa = {
									cheque : result.beneficiarios[j].cheque,
									monto : result.beneficiarios[j].monto
							};
							$row.find('[name=extracto]').attr("readonly","readonly");
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',dataa);
							p.$w.find(".gridBody .total").before( $row.children() );
						}		
					}
				} else {
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encotrarón cheques en este periodo y cuenta bancaria!',type: 'error'});
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowRectitsCompCheque',
			title: 'Rectificar Cheques',
			contentURL: 'ts/comp/recti_cheques',
			icon: 'ui-icon-search',
			width: 800,
			height: 400,
			buttons: {
				"Guardar": function(){
					if(p.$w.find('.gridBody .item').length>0){
						var total = 0;
						var data = new Object;
						data.cheques = new Array;
						for(i=0;i<p.$w.find('.gridBody .item').length;i++){
							if(p.$w.find('.gridBody .item').eq(i).find('input').is(':checked')){
								total = total + parseFloat(p.$w.find('.gridBody .item').eq(i).find('li').eq(6).html());
								var dat = p.$w.find('.gridBody .item').eq(i).data('data');
								dat.estracto = p.$w.find('.gridBody .item').eq(i).find('[name=extracto]').val();
								dat.diferencia = p.$w.find('.gridBody .item').eq(i).find('li').eq(6).html();
								data.cheques.push(dat);
							}
						}
						data.total = total;
					}
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowRectitsCompCheque');
				K.block({$element: p.$w});
				p.search({page: 1});
				p.$w.find('[name=moneda_simbol]').html(ctCban.moneda[p.moneda].simbol);
				p.$w.find('[name=diferencia]').html(p.diferencia);
				p.$w.find('[name=checkitem]').live('click',function(){
					var $file = $(this).closest('.item');
					if($(this).is(':checked')){
						$file.find('[name=extracto]').removeAttr("readonly");
					}else{
						$file.find('[name=extracto]').attr("readonly","readonly");
						$file.find('[name=extracto]').val("");
						$file.find('li').eq(6).html("");			
					}
					p.calc();
				});
				p.$w.find('[name=extracto]').live('keyup',function(){
					var $file = $(this).closest('.item');
					var monto = $file.find('li').eq(3).html();
					var extracto = $file.find('[name=extracto]').val();
					var diferencia = parseFloat(monto)-parseFloat(extracto);
					$file.find('li').eq(6).html(K.round(diferencia,2));
					p.calc();
				});
				K.unblock({$element: p.$w});
			}
		});
	}
};