/*******************************************************************************
boletas de pago */
pePlanVei = {
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
				$p.find('[name=pePlanVei]').click(function(){ pePlanVei.init(); }).addClass('ui-state-highlight');
				$p.find('[name=pePlanTre]').click(function(){ pePlanTre.init(); });
				$p.find('[name=pePlanVac]').click(function(){ pePlanVac.init(); });
				$p.find('[name=pePlanFal]').click(function(){ pePlanFal.init(); });
				$p.find('[name=pePlanBon]').click(function(){ pePlanBon.init(); });
				$p.find('[name=pePlanEnf]').click(function(){ pePlanEnf.init(); });
				$p.find('[name=pePlanQui]').click(function(){ pePlanQui.init(); });
				$p.find('[name=pePlanMat]').click(function(){ pePlanMat.init(); });
				$p.find('[name=pePlanSep]').click(function(){ pePlanSep.init(); });
				$p.find('[name^=pePlanBole]').click(function(){
					$.cookie('tipo_contrato',$(this).attr('name').substring(10));
					pePlanBole.init();
				});
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'pePlanVei',
			titleBar: {
				title: 'Bonificaci&oacute;n de 25 a&ntilde;os'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/bole',
			onContentLoaded: function(){
				$('#pageWrapperLeft [name=pePlanVei]').addClass('ui-state-highlight');
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
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
					pePlanVei.windowPagar();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					pePlanVei.windowNew();
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
						pePlanVei.loadData({page: 1,url: 'pe/docs/lista_vei'});
					}else{
						$("#mainPanel .gridBody").empty();
						pePlanVei.loadData({page: 1,url: 'pe/docs/search_vei'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				var date = new Date();
				$mainPanel.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        /*onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        },*/
			        onChangeMonthYear: function(year,month,inst){
			            $(this).data('mes',month-1).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month-1, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[date.getMonth()]+' '+date.getFullYear())
			    .data('mes',+date.getMonth())
			    .data('ano',date.getFullYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
					pePlanVei.loadData({page: 1,url: 'pe/docs/lista_vei'});
			    });
				pePlanVei.loadData({page: 1,url: 'pe/docs/lista_vei'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		K.sendingInfo();
		if(params==null){
			params = {url: 'pe/docs/lista_vei'};
			$mainPanel.find('.gridBody').empty();
		}
		$.extend(params,{
			texto: $('.divSearch [name=buscar]').val(),
    		mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
    		ano: $mainPanel.find('[name=periodo]').data('ano'),
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
					$li.eq(0).css('background',pePlanVei.states[result.estado].color).addClass('vtip').attr('title',pePlanVei.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( 'N&deg;'+result.cod );
					$li.eq(3).html( ciHelper.enti.formatName(result.trabajador) );
					if(result.trabajador.roles.trabajador.cargo._id!=null)
						$li.eq(4).html( result.trabajador.roles.trabajador.cargo.nomb );
					else
						$li.eq(4).html( result.trabajador.roles.trabajador.cargo.funcion );
					$li.eq(5).html( ciHelper.formatMon(result.total_pago) );
					$li.eq(6).html( ciHelper.formatMon(result.total_desc) );
					$li.eq(7).html( ciHelper.formatMon(result.neto) );
					$li.eq(8).html( ciHelper.formatMon(result.total_apor) );
					$li.eq(9).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						pePlanVei.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
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
								pePlanVei.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenPeSubs_imp': function(t) {
								K.windowPrint({
									id:'windowPeBolePrint',
									title: "Boleta",
									url: "pe/bole/print_bole?id="+K.tmp.data('id')
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
						pePlanVei.loadData(params);
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
			conceptos: [],
			loadConc: function(){
				p.conceptos = [];
				var enti = p.$w.find('[name=nomb]').data('data'),
				fin = p.$w.find('[name=fin]').val();
				p.entidad = enti;
				if(enti==null){
					p.$w.find('[name=btnSelEnt]').click();
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
				}else{
					p.$w.find('[name=tiempo]').html( ciHelper.date.getStringAge(new Date(enti.roles.trabajador.fecing.sec*1000),p.$w.find('[name=fecfin]').datepicker("getDate")) );
					$.post('pe/bole/trab_per',{doc: 'quinquenio',enti: enti._id.$id},function(data){
						p.forfal = [];
						var __VALUE__ = 0;
						var TRAB = data.enti;
						for(x in TRAB){
							if(TRAB[x]=='true') TRAB[x] = true;
							else if(TRAB[x]=='false') TRAB[x] = false;
						}
						for(var i=0,j=data.vars.length; i<j; i++){
							try{
								if(data.vars[i].valor=='true') eval('var '+data.vars[i].cod+' = true;');
								else if(data.vars[i].valor=='false') eval('var '+data.vars[i].cod+' = false;');
								else eval('var '+data.vars[i].cod+' = '+data.vars[i].valor+';');
							}catch(e){
								p.forfal.push({
									nomb: data.vars[i].cod,
									form: data.vars[i].valor
								});
							}
						}
						for(var i=0,j=data.pago.length; i<j; i++){
							if(data.pago[i].formula.indexOf('__VALUE__')!=-1) eval('var '+data.pago[i].cod+' = 0;');
							try{
								if(data.pago[i].formula=='true') eval('var '+data.pago[i].cod+' = true;');
								else if(data.pago[i].formula=='false') eval('var '+data.pago[i].cod+' = false;');
								else eval('var '+data.pago[i].cod+' = '+data.pago[i].formula+';');
							}catch(e){
								p.forfal.push({
									nomb: data.pago[i].cod,
									form: data.pago[i].formula
								});
							}
						}
						for(var i=0,j=data.descuento.length; i<j; i++){
							if(data.descuento[i].formula.indexOf('__VALUE__')!=-1) eval('var '+data.descuento[i].cod+' = 0;');
							try{
								if(data.descuento[i].formula=='true') eval('var '+data.descuento[i].cod+' = true;');
								else if(data.descuento[i].formula=='false') eval('var '+data.descuento[i].cod+' = false;');
								else eval('var '+data.descuento[i].cod+' = '+data.descuento[i].formula+';');
							}catch(e){
								p.forfal.push({
									nomb: data.descuento[i].cod,
									form: data.descuento[i].formula
								});
							}
						}
						for(var i=0,j=data.aporte.length; i<j; i++){
							if(data.aporte[i].formula.indexOf('__VALUE__')!=-1) eval('var '+data.aporte[i].cod+' = 0;');
							try{
								if(data.aporte[i].formula=='true') eval('var '+data.aporte[i].cod+' = true;');
								else if(data.aporte[i].formula=='false') eval('var '+data.aporte[i].cod+' = false;');
								else eval('var '+data.aporte[i].cod+' = '+data.aporte[i].formula+';');
							}catch(e){
								p.forfal.push({
									nomb: data.aporte[i].cod,
									form: data.aporte[i].formula
								});
							}
						}
						for(var i=0,j=data.bono.length; i<j; i++){
							eval('var '+data.bono[i].cod+' = 0;');
							try{
								if(data.bono[i].formula=='true') eval('var '+data.bono[i].cod+' = true;');
								else if(data.bono[i].formula=='false') eval('var '+data.bono[i].cod+' = false;');
								else eval('var '+data.bono[i].cod+' = '+data.bono[i].formula+';');
							}catch(e){
								p.forfal.push({
									nomb: data.bono[i].cod,
									form: data.bono[i].formula
								});
							}
						}
						var functioncheck = function(forfal){
							var tmpf = [];
							for(var i=0,j=forfal.length; i<j; i++){
								try{
									if(forfal[i].form=='true') eval('var '+forfal[i].nomb+' = true;');
									else if(forfal[i].form=='false') eval('var '+forfal[i].nomb+' = false;');
									else eval('var '+forfal[i].nomb+' = '+forfal[i].form+';');
								}catch(e){
									tmpf.push({
										nomb: forfal[i].nomb,
										form: forfal[i].form
									});
								}
							}
							if(forfal.second==null){
								if(tmpf.length>0){
									tmpf.second = true;
									functioncheck(tmpf);
								}
							}
						};
						functioncheck(p.forfal);
						p.$g = p.$w.find('.gridCont:eq(0)');
						p.$g.find(".gridBody").empty();
						p.total = {
							pago: 0,
							descuento: 0,
							aporte: 0
						};
						for(var i=0; i<data.pago.length; i++){
							var ok = false;
							if(data.pago[i].filtro!=null){
								var ok_tot = 0;
								for(var ii=0; ii<data.pago[i].filtro.length; ii++){
									var filtro = data.pago[i].filtro[ii];
									switch(filtro.tipo){
										case '1':
											if(p.entidad.roles.trabajador.nivel!=null){
												if(p.entidad.roles.trabajador.nivel._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '2':
											if(p.entidad.roles.trabajador.nivel_carrera!=null){
												if(p.entidad.roles.trabajador.nivel_carrera._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '3':
											if(p.entidad.roles.trabajador.modalidad!=null){
												if(p.entidad.roles.trabajador.modalidad==filtro.valor)
													ok_tot++;
											}
											break;
										case '4':
											if(p.entidad.roles.trabajador.cargo_clasif!=null){
												if(p.entidad.roles.trabajador.cargo_clasif._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '5':
											if(p.entidad.roles.trabajador.grupo_ocup!=null){
												if(p.entidad.roles.trabajador.grupo_ocup._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '6':
											if(p.entidad.roles.trabajador.tipo!=null){
												if(p.entidad.roles.trabajador.tipo==filtro.valor)
													ok_tot++;
											}
											break;
										case '7':
											if(p.entidad.roles.trabajador.eps!=null){
												if(p.entidad.roles.trabajador.eps==filtro.valor)
													ok_tot++;
											}
											break;
									}
								}
								if(ok_tot==data.pago[i].filtro.length){
									ok = true;
								}
							}else ok = true;
							if(ok==true){
								var monto = eval(data.pago[i].formula);
								var $row = p.$g.find('.gridReference').clone();
								$row.find('li:eq(0)').html(data.pago[i].descr);
								if(data.pago[i].formula.indexOf('__VALUE__')!=-1){
									var formula = data.pago[i].formula;
									formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+data.pago[i].cod+"__");
									$row.find('li:eq(1)').html('<input type="text" size="7" name="codform'+data.pago[i].cod+'">');
									$row.find('[name^=codform]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
										$(this).change();
									}}).change(function(){
										var val = parseFloat($(this).val()),
										formula = $(this).data('form'),
										cod = $(this).data('cod'),
										$row = $(this).closest('.item');
										eval("var __VALUE"+cod+"__ = "+val+";"); 
										var monto = eval(formula);
										$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
										$row.data('monto',monto);
										p.conceptos[$row.data('index')].subtotal = monto;
										eval('var '+cod+' = '+monto);
										for(var ii=0,jj=data.pago.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(0)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										for(var ii=0,jj=data.descuento.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(1)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										for(var ii=0,jj=data.aporte.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(2)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										p.calcTot();
									}).data('form',formula).data('cod',data.pago[i].cod);
									$row.find('li:eq(1) .ui-button').css('height','14px');
								}else{
									$row.find('li:eq(2)').data('formula',data.pago[i].formula);
								}
								$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
								$row.wrapInner('<a class="item" name="'+data.pago[i]._id.$id+'" />');
								$row.find('.item').data('monto',monto).data('index',p.conceptos.length);
								p.$g.find(".gridBody").append( $row.children() );
								p.total.pago = p.total.pago + parseFloat(monto);
								p.conceptos.push({
									concepto: {
										_id: data.pago[i]._id.$id,
										cod: data.pago[i].cod,
										nomb: data.pago[i].descr,
										formula: data.pago[i].formula,
										tipo: data.pago[i].tipo
									},
									subtotal: K.round(parseFloat(monto),2)
								});
							}
						}
						var $row = p.$g.find('.gridReference').clone();
						$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
						$row.find('li:eq(1)').html('Total');
						$row.find('li:eq(2)').html(ciHelper.formatMon(p.total.pago));
						$row.wrapInner('<a class="item" />');
						p.$g.find(".gridBody").append( $row.children() );
						p.$g = p.$w.find('.gridCont:eq(1)');
						p.$g.find(".gridBody").empty();
						for(var i=0; i<data.descuento.length; i++){
							var ok = false;
							if(data.descuento[i].filtro!=null){
								var ok_tot = 0;
								for(var ii=0; ii<data.descuento[i].filtro.length; ii++){
									var filtro = data.descuento[i].filtro[ii];
									switch(filtro.tipo){
										case '1':
											if(p.entidad.roles.trabajador.nivel!=null){
												if(p.entidad.roles.trabajador.nivel._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '2':
											if(p.entidad.roles.trabajador.nivel_carrera!=null){
												if(p.entidad.roles.trabajador.nivel_carrera._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '3':
											if(p.entidad.roles.trabajador.modalidad!=null){
												if(p.entidad.roles.trabajador.modalidad==filtro.valor)
													ok_tot++;
											}
											break;
										case '4':
											if(p.entidad.roles.trabajador.cargo_clasif!=null){
												if(p.entidad.roles.trabajador.cargo_clasif._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '5':
											if(p.entidad.roles.trabajador.grupo_ocup!=null){
												if(p.entidad.roles.trabajador.grupo_ocup._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '6':
											if(p.entidad.roles.trabajador.tipo!=null){
												if(p.entidad.roles.trabajador.tipo==filtro.valor)
													ok_tot++;
											}
											break;
										case '7':
											if(p.entidad.roles.trabajador.eps!=null){
												if(p.entidad.roles.trabajador.eps==filtro.valor)
													ok_tot++;
											}
											break;
									}
								}
								if(ok_tot==data.descuento[i].filtro.length){
									ok = true;
								}
							}else ok = true;
							if(ok==true){
								var monto = eval(data.descuento[i].formula);
								var $row = p.$g.find('.gridReference').clone();
								$row.find('li:eq(0)').html(data.descuento[i].descr);
								if(data.descuento[i].formula.indexOf('__VALUE__')!=-1){
									var formula = data.descuento[i].formula;
									formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+data.descuento[i].cod+"__");
									$row.find('li:eq(1)').html('<input type="text" size="7" name="codform'+data.descuento[i].cod+'">');
									$row.find('[name^=codform]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
										$(this).change();
									}}).change(function(){
										var val = parseFloat($(this).val()),
										formula = $(this).data('form'),
										cod = $(this).data('cod'),
										$row = $(this).closest('.item');
										eval("var __VALUE"+cod+"__ = "+val+";"); 
										var monto = eval(formula);
										$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
										$row.data('monto',monto);
										p.conceptos[$row.data('index')].subtotal = monto;
										eval('var '+cod+' = '+monto);
										for(var ii=0,jj=data.pago.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(0)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										for(var ii=0,jj=data.descuento.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(1)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										for(var ii=0,jj=data.aporte.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(2)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										p.calcTot();
									}).data('form',formula).data('cod',data.descuento[i].cod);
									$row.find('li:eq(1) .ui-button').css('height','14px');
								}else{
									$row.find('li:eq(2)').data('formula',data.descuento[i].formula);
								}
								$row.find('li:eq(2)').html('S/.'+K.round(parseFloat(monto),2));
								$row.wrapInner('<a class="item" name="'+data.descuento[i]._id.$id+'" />');
								$row.find('.item').data('monto',monto).data('index',p.conceptos.length);
								p.$g.find(".gridBody").append( $row.children() );
								p.total.descuento = p.total.descuento + parseFloat(monto);
								p.conceptos.push({
									concepto: {
										_id: data.descuento[i]._id.$id,
										cod: data.descuento[i].cod,
										nomb: data.descuento[i].descr,
										formula: data.descuento[i].formula,
										tipo: data.descuento[i].tipo
									},
									subtotal: K.round(parseFloat(monto),2)
								});
							}
						}
						var $row = p.$g.find('.gridReference').clone();
						$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
						$row.find('li:eq(1)').html('Total');
						$row.find('li:eq(2)').html(ciHelper.formatMon(p.total.descuento));
						$row.wrapInner('<a class="item" />');
						p.$g.find(".gridBody").append( $row.children() );
						p.$g = p.$w.find('.gridCont:eq(2)');
						p.$g.find(".gridBody").empty();
						for(var i=0; i<data.aporte.length; i++){
							var ok = false;
							if(data.aporte[i].filtro!=null){
								var ok_tot = 0;
								for(var ii=0; ii<data.aporte[i].filtro.length; ii++){
									var filtro = data.aporte[i].filtro[ii];
									switch(filtro.tipo){
										case '1':
											if(p.entidad.roles.trabajador.nivel!=null){
												if(p.entidad.roles.trabajador.nivel._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '2':
											if(p.entidad.roles.trabajador.nivel_carrera!=null){
												if(p.entidad.roles.trabajador.nivel_carrera._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '3':
											if(p.entidad.roles.trabajador.modalidad!=null){
												if(p.entidad.roles.trabajador.modalidad==filtro.valor)
													ok_tot++;
											}
											break;
										case '4':
											if(p.entidad.roles.trabajador.cargo_clasif!=null){
												if(p.entidad.roles.trabajador.cargo_clasif._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '5':
											if(p.entidad.roles.trabajador.grupo_ocup!=null){
												if(p.entidad.roles.trabajador.grupo_ocup._id.$id==filtro.valor._id.$id)
													ok_tot++;
											}
											break;
										case '6':
											if(p.entidad.roles.trabajador.tipo!=null){
												if(p.entidad.roles.trabajador.tipo==filtro.valor)
													ok_tot++;
											}
											break;
										case '7':
											if(p.entidad.roles.trabajador.eps!=null){
												if(p.entidad.roles.trabajador.eps==filtro.valor)
													ok_tot++;
											}
											break;
									}
								}
								if(ok_tot==data.aporte[i].filtro.length){
									ok = true;
								}
							}else ok = true;
							if(ok==true){
								var monto = eval(data.aporte[i].formula);
								var $row = p.$g.find('.gridReference').clone();
								$row.find('li:eq(0)').html(data.aporte[i].descr);
								if(data.aporte[i].formula.indexOf('__VALUE__')!=-1){
									var formula = data.aporte[i].formula;
									formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+data.aporte[i].cod+"__");
									$row.find('li:eq(1)').html('<input type="text" size="7" name="codform'+data.aporte[i].cod+'">');
									$row.find('[name^=codform]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
										$(this).change();
									}}).change(function(){
										var val = parseFloat($(this).val()),
										formula = $(this).data('form'),
										cod = $(this).data('cod'),
										$row = $(this).closest('.item');
										eval("var __VALUE"+cod+"__ = "+val+";"); 
										var monto = eval(formula);
										$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
										$row.data('monto',monto);
										p.conceptos[$row.data('index')].subtotal = monto;
										eval('var '+cod+' = '+monto);
										for(var ii=0,jj=data.pago.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(0)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										for(var ii=0,jj=data.descuento.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(1)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										for(var ii=0,jj=data.aporte.length; ii<jj; ii++){
											var $table = p.$w.find('.gridCont:eq(2)'),
											$row = $table.find('.gridBody .item').eq(ii),
											$cell = $row.find('li').eq(2),
											monto = eval($cell.data('formula'));
											if($cell.data('formula')!=null){
												$cell.html(ciHelper.formatMon(monto,p.$w.find('[name=cboMoneda] option:selected').val()));
												$row.data('monto',monto);
											}
										}
										p.calcTot();
									}).data('form',formula).data('cod',data.aporte[i].cod);
									$row.find('li:eq(1) .ui-button').css('height','14px');
								}else{
									$row.find('li:eq(2)').data('formula',data.aporte[i].formula);
								}
								$row.find('li:eq(2)').html('S/.'+K.round(parseFloat(monto),2));
								$row.wrapInner('<a class="item" name="'+data.aporte[i]._id.$id+'" />');
								$row.find('.item').data('monto',monto).data('index',p.conceptos.length);
								p.$g.find(".gridBody").append( $row.children() );
								p.total.aporte = p.total.aporte + parseFloat(monto);
								p.conceptos.push({
									concepto: {
										_id: data.aporte[i]._id.$id,
										cod: data.aporte[i].cod,
										nomb: data.aporte[i].descr,
										formula: data.aporte[i].formula,
										tipo: data.aporte[i].tipo
									},
									subtotal: K.round(parseFloat(monto),2)
								});
							}
						}
						var $row = p.$g.find('.gridReference').clone();
						$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
						$row.find('li:eq(1)').html('Total');
						$row.find('li:eq(2)').html(ciHelper.formatMon(p.total.aporte));
						$row.wrapInner('<a class="item" />');
						p.$g.find(".gridBody").append( $row.children() );
						p.neto = K.round(p.total.pago-p.total.descuento,2);
						p.redondeo = p.neto+"";
						p.redondeo = p.redondeo.substring(p.redondeo.length-1,p.redondeo.length);
						p.$w.find('[name=neto]').html('S/.'+p.neto);
						p.$w.find('[name=neto_pagar]').html('S/.'+p.neto);
						if(parseInt(p.redondeo)>0)
							p.$w.find('[name=redondeo]').html('S/.'+'0.0'+p.redondeo);
						p.neto_pagar = parseFloat(p.neto);
						if(parseInt(p.redondeo)>5) p.neto_pagar = parseFloat(p.neto) - 0.07;
						p.neto_pagar = p.neto_pagar.toFixed(1);
						p.$w.find('[name=neto_pagar]').html('S/.'+parseFloat(p.neto_pagar).toFixed(1)+'0');
					},'json');
				}
			},
			calcTot: function(){
				p.total.pago = 0;
				p.$w.find('.gridCont:eq(0) .item:last').remove();
				for(var i=0,j=p.$w.find('.gridCont:eq(0) .item').length; i<j; i++){
					p.total.pago = p.total.pago + parseFloat(p.$w.find('.gridCont:eq(0) .item').eq(i).data('monto'));
				}
				var $row = p.$w.find('.gridCont:eq(0) .gridReference').clone();
				$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html('Total');
				$row.find('li:eq(2)').html(ciHelper.formatMon(p.total.pago));
				$row.wrapInner('<a class="item" />');
				p.$w.find('.gridCont:eq(0) .gridBody').append( $row.children() );
				p.total.descuento = 0;
				p.$w.find('.gridCont:eq(1) .item:last').remove();
				for(var i=0,j=p.$w.find('.gridCont:eq(1) .item').length; i<j; i++){
					p.total.descuento = p.total.descuento + parseFloat(p.$w.find('.gridCont:eq(1) .item').eq(i).data('monto'));
				}
				var $row = p.$w.find('.gridCont:eq(1) .gridReference').clone();
				$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html('Total');
				$row.find('li:eq(2)').html(ciHelper.formatMon(p.total.descuento));
				$row.wrapInner('<a class="item" />');
				p.$w.find('.gridCont:eq(1) .gridBody').append( $row.children() );
				p.total.aporte = 0;
				p.$w.find('.gridCont:eq(2) .item:last').remove();
				for(var i=0,j=p.$w.find('.gridCont:eq(2) .item').length; i<j; i++){
					p.total.aporte = p.total.aporte + parseFloat(p.$w.find('.gridCont:eq(2) .item').eq(i).data('monto'));
				}
				var $row = p.$w.find('.gridCont:eq(2) .gridReference').clone();
				$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
				$row.find('li:eq(1)').html('Total');
				$row.find('li:eq(2)').html(ciHelper.formatMon(p.total.aporte));
				$row.wrapInner('<a class="item" />');
				p.$w.find('.gridCont:eq(2) .gridBody').append( $row.children() );
				p.neto = K.round(p.total.pago-p.total.descuento,2);
				p.redondeo = p.neto+"";
				p.redondeo = p.redondeo.substring(p.redondeo.length-1,p.redondeo.length);
				p.$w.find('[name=neto]').html('S/.'+p.neto);
				p.$w.find('[name=neto_pagar]').html('S/.'+p.neto);
				if(parseInt(p.redondeo)>0)
					p.$w.find('[name=redondeo]').html('S/.'+'0.0'+p.redondeo);
				p.neto_pagar = parseFloat(p.neto);
				if(parseInt(p.redondeo)>5) p.neto_pagar = parseFloat(p.neto) - 0.07;
				p.neto_pagar = p.neto_pagar.toFixed(1);
				p.$w.find('[name=neto_pagar]').html(parseFloat(p.neto_pagar).toFixed(1)+'0');
				p.$w.find('[name=neto_pagar]').html(ciHelper.formatMon(parseFloat(p.$w.find('[name=neto_pagar]').html())*2));
			}
		});
		new K.Window({
			id: 'windowNewVei',
			title: 'Compensaci&oacute;n de 25 a&ntilde;os',
			contentURL: 'pe/docs/vei_edit',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						periodo: {
							inicio: p.$w.find('[name=ini]').data('data'),
							fin: p.$w.find('[name=fin]').val()
						},
						bonificaciones: {
							tipo: 'V',
							ref: p.$w.find('[name=ref]').val(),
							descr: p.$w.find('[name=descr]').val(),
							nota: p.$w.find('[name=nota]').val()
						}
					};
					if(p.enti==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
					}else data.trabajador = ciHelper.enti.dbTrabRel(p.enti);
					if(data.periodo.fin==''){
						p.$w.find('[name=fin]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de fin!',type: 'error'});
					}else{
						data.periodo.ano = p.$w.find('[name=fin]').datepicker('getDate').getFullYear();
						data.periodo.mes = parseInt(p.$w.find('[name=fin]').datepicker('getDate').getMonth())+1;
					}
					if(data.bonificaciones.ref==''){
						p.$w.find('[name=ref]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una referencia!',type: 'error'});
					}
					if(data.bonificaciones.descr==''){
						p.$w.find('[name=descr]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una descripci&oacute;n!',type: 'error'});
					}
					$.each(p.conceptos,function(){
						this.glosa = p.$w.find('[name='+this.concepto._id+']').find('[name=glosa]').val();
					});
					$.extend(data,{
						conceptos: p.conceptos,
						total_pago: K.round(p.total.pago,2),
						total_desc: K.round(p.total.descuento,2),
						total_apor: K.round(p.total.aporte,2),
						total: K.round(p.neto,2),
						redondeo: '0.0'+p.redondeo,
						neto: parseFloat(p.neto_pagar).toFixed(1)+'0'
					});
					data.contrato = {
						_id: p.enti.roles.trabajador.contrato._id.$id,
						nomb: p.enti.roles.trabajador.contrato.nomb,
						cod: p.enti.roles.trabajador.contrato.cod
					};
					if(parseFloat(data.total)<0){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'El total de la boleta no puede ser negativo!',
							type: 'error'
						});
					}
					data.neto = parseFloat(data.neto)*2;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/docs/save_doc',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La boleta fue registrada con &eacute;xito!'});
						//$('#pageWrapperLeft .ui-state-highlight').click();
						$mainPanel.find('[name=periodo]').change();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewVei');
				K.block({$element: p.$w});
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$w.find('.grid').eq(3).bind('scroll',function(){
					p.$w.find('.grid').eq(2).scrollLeft(p.$w.find('.grid').eq(3).scrollLeft());
				});
				p.$w.find('.grid').eq(5).bind('scroll',function(){
					p.$w.find('.grid').eq(4).scrollLeft(p.$w.find('.grid').eq(5).scrollLeft());
				});
				p.$w.find('[name=btnSelEnt]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.enti = data;
						K.block({$element: p.$w});
						if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
						else p.$w.find('[name=foto]').removeAttr('src');
						p.$w.find('[name=nomb]').data('data',data)
						.html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
						p.loadConc();
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
							K.unblock({$element: p.$w});
						},'json');
					},filter: [
						{nomb: 'tipo_enti',value: 'P'},
						{nomb: 'roles.trabajador',value: {$exists: true}},
						{nomb: 'roles.trabajador.fecing',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=fin]').datepicker();
				p.$w.find('[name=tabs]').tabs();
				p.$w.find('#tabs-1,#tabs-2,#tabs-3').css('padding','0px');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsBole'+p.id,
			title: 'Boleta de Pago '+p.nomb,
			contentURL: 'pe/bole/details',
			icon: 'ui-icon-document',
			width: 650,
			height: 400,
			buttons: {
				"Imprimir": function(){
					//pePlanVei.windowPrint({id: p.id,nomb: p.nomb});
					K.windowPrint({
						id:'windowPeBolePrint',
						title: "Boleta",
						url: "pe/bole/print_bole?id="+p.id
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsBole'+p.id);
				p.$w.find('[name=tabs]').tabs();
				p.$w.find('#tabs-1,#tabs-2,#tabs-3').css('padding','0px');
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$w.find('.grid').eq(3).bind('scroll',function(){
					p.$w.find('.grid').eq(2).scrollLeft(p.$w.find('.grid').eq(3).scrollLeft());
				});
				p.$w.find('.grid').eq(5).bind('scroll',function(){
					p.$w.find('.grid').eq(4).scrollLeft(p.$w.find('.grid').eq(5).scrollLeft());
				});
				K.block({$element: p.$w});
				$.post('pe/bole/get','id='+p.id,function(data){
					if(data.trabajador.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.trabajador.imagen.$id);
					else p.$w.find('[name=foto]').removeAttr('src');
					p.$w.find('[name=nomb]').html( ciHelper.enti.formatName(data.trabajador) ).attr('title',ciHelper.enti.formatName(data.trabajador)).tooltip();
					p.$w.find('[name=dni]').html( data.trabajador.docident[0].num );
					if(data.trabajador.roles.trabajador.cargo._id!=null)
						p.$w.find('[name=cargo]').html( data.trabajador.roles.trabajador.cargo.nomb );
					else
						p.$w.find('[name=cargo]').html( data.trabajador.roles.trabajador.cargo.funcion );
					p.$w.find('[name=orga]').html( data.trabajador.roles.trabajador.organizacion.nomb ).attr('title',data.trabajador.roles.trabajador.organizacion.nomb).tooltip();
					if(data.trabajador.roles.trabajador.nivel!=null)
						p.$w.find('[name=nivel]').html( data.trabajador.roles.trabajador.nivel.nomb );
					p.$w.find('[name=essalud]').html( data.trabajador.roles.trabajador.essalud );
					if(data.trabajador.roles.trabajador.pension!=null)
						p.$w.find('[name=pension]').html( data.trabajador.roles.trabajador.pension.nomb );
					p.$w.find('[name=cod_aportante]').html( data.trabajador.roles.trabajador.cod_aportante );
					if(data.trabajador.actividad!=null) p.$w.find('[name=actividad]').html( data.trabajador.orga.actividad.cod );
					if(data.trabajador.componente!=null) p.$w.find('[name=componente]').html( data.trabajador.orga.componente.cod );
					p.$w.find('[name=periodo]').html(ciHelper.meses[parseInt(data.periodo.mes)-1]+' del '+data.periodo.ano);
					p.$w.find('[name=ini]').html(ciHelper.dateFormat(data.periodo.inicio));
					p.$w.find('[name=fin]').html(ciHelper.dateFormat(data.periodo.fin));
					for(var i=0, j=data.conceptos.length; i<j; i++){
						switch(data.conceptos[i].concepto.tipo){
							case "P":
								p.$g = p.$w.find('.gridCont:eq(0)');
								break;
							case "D":
								p.$g = p.$w.find('.gridCont:eq(1)');
								break;
							case "A":
								p.$g = p.$w.find('.gridCont:eq(2)');
								break;
						}
						$row = p.$g.find('.gridReference').clone();
						$row.find('li:eq(0)').html(data.conceptos[i].concepto.nomb);
						$row.find('li:eq(1)').html('S/.'+data.conceptos[i].subtotal);
						$row.find('li:eq(2)').html(data.conceptos[i].glosa);
						$row.wrapInner('<a class="item" />');
						p.$g.find(".gridBody").append( $row.children() );
					}
					p.$g = p.$w.find('.gridCont:eq(0)');
					var $row = p.$g.find('.gridReference').clone();
					$row.find('li:eq(0)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
					$row.find('li:eq(0)').html('Total');
					$row.find('li:eq(1)').html('S/.'+data.total_pago);
					$row.wrapInner('<a class="item" />');
					p.$g.find(".gridBody").append( $row.children() );
					p.$g = p.$w.find('.gridCont:eq(1)');
					var $row = p.$g.find('.gridReference').clone();
					$row.find('li:eq(0)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
					$row.find('li:eq(0)').html('Total');
					$row.find('li:eq(1)').html('S/.'+data.total_desc);
					$row.wrapInner('<a class="item" />');
					p.$g.find(".gridBody").append( $row.children() );
					p.$g = p.$w.find('.gridCont:eq(2)');
					var $row = p.$g.find('.gridReference').clone();
					$row.find('li:eq(0)').addClass('ui-button ui-widget ui-state-default ui-button-text-only');
					$row.find('li:eq(0)').html('Total');
					$row.find('li:eq(1)').html('S/.'+data.total_apor);
					p.$g.find(".gridBody").append( $row.children() );
					$row.wrapInner('<a class="item" />');
					p.$w.find('[name=neto]').html(ciHelper.formatMon(data.total));
					p.$w.find('[name=redondeo]').html(ciHelper.formatMon(data.redondeo));
					p.$w.find('[name=neto_pagar]').html(ciHelper.formatMon(data.neto));
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowPagar: function(p){
		K.block({message: 'Espere por favor mientras se genera el pago de las boletas seleccionadas!'});
		$.extend(p,{
			mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
    		ano: $mainPanel.find('[name=periodo]').data('ano'),
    		doc: 'bonificaciones',
    		tipo: 'V'
		});
		K.sendingInfo();
		$.post('pe/docs/generar_pago',p,function(){
			K.unblock();
			K.clearNoti();
			K.notification({title: ciHelper.titleMessages.regiGua,text: 'La operaci&oacute;n fue realizada con &eacute;xito!'});
			pePlanVei.loadData();
		},'json');
	}
};