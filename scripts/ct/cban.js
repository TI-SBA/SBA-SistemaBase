/*******************************************************************************
conciliacion bancaria */
ctCban = {
	moneda:{
		"S":{descr:"Soles",simbol:"S/."},
		"D":{descr:"Dolares",simbol:"USD $."}
	},
	states :{
		"A":{descr:"Activo",color:"gray"},
		"F":{descr:"Finalizado",color:"green"}
	},
	btnUpdate :function(mes,ano){
		$.post('ts/ctban/all_cban','mes='+mes+'&ano='+ano,function(data){
			if(data!=null){
				$('#mainPanel').find('[name=btnAgregar]').button( "option", "disabled", false );
			}else{
				$('#mainPanel').find('[name=btnAgregar]').button( "option", "disabled", true );
			}
		},'json');
	},
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctCban',
			titleBar: {
				title: 'Conciliaciones Bancarias'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/cban',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de clasificador' ).width('250');
				$mainPanel.find('[name=obj]').html( 'conciliacion(es) bancaria(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				
				$mainPanel.find('[name=mes]').change(function(){
					$("#mainPanel .gridBody").empty();
					ctCban.loadData({page: 1,url: 'ct/cban/lista'});
					ctCban.btnUpdate($mainPanel.find('[name=mes] :selected').val(),$mainPanel.find('[name=ano]').val());
				});
				$mainPanel.find('[name=ano]').spinner();
				$mainPanel.find('[name=ano]').parent().find('.ui-button').height('14px').click(function(){
					$("#mainPanel .gridBody").empty();
					ctCban.loadData({page: 1,url: 'ct/cban/lista'});
					ctCban.btnUpdate($mainPanel.find('[name=mes] :selected').val(),$mainPanel.find('[name=ano]').val());				});
				$mainPanel.find('[name=ano]').keyup(function(){
					$("#mainPanel .gridBody").empty();
					ctCban.loadData({page: 1,url: 'ct/cban/lista'});
					ctCban.btnUpdate($mainPanel.find('[name=mes] :selected').val(),$mainPanel.find('[name=ano]').val());				});
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()); 
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctCban.windowNew({
						ano:$mainPanel.find('[name=ano]').val(),
						mes:$mainPanel.find('[name=mes] :selected').val()
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						ctCban.loadData({page: 1,url: 'ct/cban/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						ctCban.loadData({page: 1,url: 'ct/cban/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				ctCban.loadData({page: 1,url: 'ct/cban/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.mes = $mainPanel.find('[name=mes] :selected').val();
		params.ano = $mainPanel.find('[name=ano]').val();
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',ctCban.states[result.estado].color).addClass('vtip').attr('title',ctCban.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.periodo.ano );
					$li.eq(3).html( ciHelper.meses[parseFloat(result.periodo.mes)-1]);
					$li.eq(4).html( result.cuenta_banco.banco.nomb);
					$li.eq(5).html( result.cuenta_banco.cod);
					$li.eq(6).html( ctCban.moneda[result.cuenta_banco.moneda].descr);
					$li.eq(7).html( result.saldo_extracto);
					$li.eq(8).html( result.saldo_bancos);
					$li.eq(9).html( result.diferencia);
					$li.eq(10).html( ciHelper.enti.formatName(result.activo.autor));
					$li.eq(11).html( ciHelper.dateFormat(result.fecreg));
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenctCban", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').estado=="F") excep +='#conMenctCban_edi,#conMenctCban_fin';
								$(excep+',#conMenList_about,#conMenList_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenctCban_ver': function(t) {
									ctCban.windowDetails({id: K.tmp.data('id')});
								},
								'conMenctCban_edi': function(t) {
									ctCban.windowEdit({id: K.tmp.data('id'),ano:$mainPanel.find('[name=ano]').val(),
										mes:$mainPanel.find('[name=mes] :selected').val()});
								},
								'conMenctCban_fin': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado :"F"
										};
										K.sendingInfo();
										$.post('ct/cban/fin',data,function(){
											K.clearNoti();
											K.notification({title: 'Conciliaci&oacute;n Finalizada',text: 'La Conciliaci&oacute;n seleccionada ha sido eliminada con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
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
						ctCban.loadData(params);
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
		p.calgrid1 = function(){
			if(p.$w.find('.gridBody:eq(1) .item').length>1){
				total = 0;
				for(i=0;i<(p.$w.find('.gridBody:eq(1) .item').length-1);i++){
					if(p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()!=""){
						total = parseFloat(p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()) + total;
					}
				}
				p.$w.find('.gridBody:eq(1) .total').find('li').eq(3).html(K.round(total,2));
				p.$w.find('[name=dep_no_reg]').html(K.round(total,2));
				p.tot();
			}
		};
		p.calgrid2 = function(){
			if(p.$w.find('.gridBody:eq(2) .item').length>1){
				total = 0;
				for(i=0;i<(p.$w.find('.gridBody:eq(2) .item').length-1);i++){
					if(p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()!=""){
						total = parseFloat(p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()) + total;
					}
				}
				p.$w.find('.gridBody:eq(2) .total').find('li').eq(2).html(K.round(total,2));
				p.$w.find('[name=gast_no_reg]').html(K.round(total,2));
				p.tot();
			}
		};
		p.tot = function(){
			var gastos_no_reg = parseFloat(p.$w.find('[name=gast_no_reg]').html());
			var depos_no_reg = parseFloat(p.$w.find('[name=dep_no_reg]').html());
			var cheques_pendi = parseFloat(p.$w.find('[name=pendi]').html());	
			if(p.$w.find('[name=saldo]').val()!="") { 
				var sald_segun_libr = parseFloat(p.$w.find('[name=saldo]').val());
			}else{
				var sald_segun_libr = 0;
			}
			var total = - gastos_no_reg + depos_no_reg + cheques_pendi + sald_segun_libr;
			p.$w.find('[name=sald_bancos]').html(K.round(total,2));
			p.$w.find('[name=sald_seg_libr]').html(K.round(total,2));
		};
		new K.Window({
			id: 'windowNewctCban',
			title: 'Nueva Conciliaci&oacute;n Bancaria',
			contentURL: 'ct/cban/edit',
			icon: 'ui-icon-plusthick',
			width: 600,
			modal:true,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.observ = p.$w.find('[name=observ]').val();
					data.estado = "A";
					data.activo = new Object;
					data.activo.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.periodo = new Object;				
					data.periodo.ano = p.ano;
					data.periodo.mes = p.mes;
					data.cuenta_banco = new Object;
					var cuenta_banco = p.$w.find('[name=c_ban] :selected').data('data');
					data.cuenta_banco._id = cuenta_banco._id.$id;
					data.cuenta_banco.banco = new Object;
					data.cuenta_banco.banco._id = cuenta_banco.banco._id.$id;
					data.cuenta_banco.banco.nomb = cuenta_banco.banco.nomb;
					data.cuenta_banco.cod_banco = cuenta_banco.cod_banco;
					data.cuenta_banco.nomb = cuenta_banco.nomb;
					data.cuenta_banco.cod = cuenta_banco.cod;
					data.cuenta_banco.moneda = cuenta_banco.moneda;
					
					data.cheques = new Array;
					if(p.$w.find('.gridBody:eq(0) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(0) .item').length;i++){
							data.cheques.push(p.$w.find('.gridBody:eq(0) .item').eq(i).data('data'));
						}
					}
					data.rectificaciones = new Array;
					if(p.$w.find('.gridBody:eq(3) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(3) .item').length;i++){
							data.rectificaciones.push(p.$w.find('.gridBody:eq(3) .item').eq(i).data('data'));
						}
					}
					data.depositos = new Array;
					if(p.$w.find('.gridBody:eq(1) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(1) .item').length;i++){
							var obj = {
									fecha:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(0).val(),
									deposito:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(1).val(),
									agencia:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(2).val(),
									monto:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()
							};
							data.depositos.push(obj);
						}
					}
					data.gastos = new Array;
					if(p.$w.find('.gridBody:eq(2) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(2) .item').length;i++){
							var obj = {
									fecha:p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(0).val(),
									descr:p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(1).val(),
									monto:p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()
							};
							data.gastos.push(obj);
						}
					}
					data.total_cheques = p.$w.find('[name=pendi]').html();
					data.total_depositos = p.$w.find('[name=dep_no_reg]').html();
					data.total_gastos = p.$w.find('[name=gast_no_reg]').html();
					data.total_rectificaciones = p.$w.find('.gridBody:eq(3) .total').find('li').eq(3).html();
					data.saldo_libro_bancos = p.$w.find('[name=saldo]').val();
					data.saldo_bancos = parseFloat(data.saldo_libro_bancos)+parseFloat(data.total_cheques)+parseFloat(data.total_depositos)-parseFloat(data.total_gastos);
					data.saldo_extracto = p.$w.find('[name=sald_seg_extr]').val();
					data.diferencia = parseFloat(data.saldo_extracto)-parseFloat(data.saldo_bancos);
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/cban/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Tipo de nota fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewctCban');
				K.block({$element: p.$w});
				p.$w.find('[name=mes]').html(ciHelper.meses[parseFloat(p.mes)-1]);
				p.$w.find('[name=ano]').html(p.ano);
				p.$w.find('[name=pendi]').html("0.00");
				p.$w.find('[name=dep_no_reg]').html("0.00");
				p.$w.find('[name=gast_no_reg]').html("0.00");
				p.$w.find('[name=sald_bancos]').html("0.00");	
				p.$w.find('[name=btnSeleccionarCheques]').click(function(){
					tsCompNue.windowSelectCheque({callback: function(data){
						if ( data ) {
							p.$w.find("[name=grid_cheques]").empty();
							var $row1 = p.$w.find('.gridReference_cheques').clone();
							$li1 = $('li',$row1);	
							$li1.eq(0).addClass('ui-state-default ui-button-text-only');
							$li1.eq(1).addClass('ui-state-default ui-button-text-only');
							$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
							$li1.eq(3).html(K.round(data.total,2));
							$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
							p.$w.find('[name=pendi]').html(K.round(data.total,2));
							p.$w.find("[name=grid_cheques]").append( $row1.children() );
							for (i=0; i < data.cheques.length; i++) {
								var $row = p.$w.find('.gridReference_cheques').clone();
								$li = $('li',$row);	
								$li.eq(0).html( data.cheques[i].fecha );
								$li.eq(1).html( data.cheques[i].cheque );
								$li.eq(2).html( ciHelper.enti.formatName(data.cheques[i].detalle) );
								$li.eq(3).html( data.cheques[i].monto );
								$row.wrapInner('<a class="item" href="javascript: void(0);" />');
								$row.find('a').data('data',data.cheques[i]);
								p.$w.find("[name=grid_cheques] .total").before( $row.children() );		
							}			
						}
						p.tot();
					},'cuenta':p.$w.find('[name=c_ban] :selected').val(),'ano':p.ano,'mes':p.mes});
				}).button();
				$.post('ts/ctban/all_cban','mes='+p.mes+'&ano='+p.ano,function(data){
					var $cbo = p.$w.find('[name=c_ban]');
					if(data!=null){
						if(data.length==0){
							K.closeWindow(p.$w.attr('id'));
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear primero cuentas bancarias!',type: 'error'});
						}
						for(var i=0; i<data.length; i++){		
							var id = data[i]._id.$id;
							var cod_banco = data[i].cod_banco;
							var nomb = data[i].nomb;
							var cod = data[i].cod;
							var moneda = data[i].moneda;
							$cbo.append('<option value="'+id+'" >'+cod+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
						p.$w.find('[name=moneda]').html(ctCban.moneda[data[0].moneda].descr);
						p.$w.find('[name=banco]').html(data[0].banco.nomb);
					}else{
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear primero cuentas bancarias!',type: 'error'});
					}
				},'json');
				p.$w.find('[name=c_ban]').change(function(){
					var dat = $(this).find('option:selected').data('data');
					p.$w.find('[name=moneda]').html(ctCban.moneda[dat.moneda].descr);
					p.$w.find('[name=banco]').html(dat.banco.nomb);
					p.$w.find(".gridBody:eq(0)").empty();
					p.$w.find(".gridBody:eq(3)").empty();
				});
				/** Grilla: Depositos no Registrados en libros */
				var $row1 = p.$w.find('.gridReference_dep_no_reg').clone();
				$li1 = $('li',$row1);	
				$li1.eq(0).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(1).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
				$li1.eq(3).html("");
				$li1.eq(4).html("");
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody:eq(1)").append( $row1.children() );		
				var $row = p.$w.find('.gridReference_dep_no_reg').clone();
				$li = $('li',$row);	
				$row.wrapInner('<a class="item" href="javascript: void(0);" />');
				$row.find('[name=fec1]').datepicker();
				$row.find('[name=btnAdd1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
				p.$w.find(".gridBody:eq(1) .total").before( $row.children() );	
				p.$w.find('[name=btnAdd1]').live('click',function(){
					var $row = p.$w.find('.gridReference_dep_no_reg').clone();
					$li = $('li',$row);
					$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					$row.find('[name=fec1]').datepicker();
					p.$w.find(".gridBody:eq(1) .total").before( $row.children() );
					p.$w.find('.gridBody:eq(1) [name=btnAdd1]').remove();
					p.$w.find('.gridBody:eq(1) .item').eq(p.$w.find('.gridBody:eq(1) .item').length-2).find('li:last').append('<button name="btnAdd1">Agregar</button>');
					p.$w.find('.gridBody:eq(1) [name=btnAdd1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli1]').live('click',function(){
					if(p.$w.find('.gridBody:eq(1) .item').length>2){
						$(this).closest('.item').remove();			
						p.$w.find('.gridBody:eq(1) [name=btnAdd1]').remove();
						p.$w.find('.gridBody:eq(1) .item').eq(p.$w.find('.gridBody:eq(1) .item').length-2).find('li:last').append('<button name="btnAdd1">Agregar</button>');
						p.$w.find('.gridBody:eq(1) [name=btnAdd1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						p.calgrid1();
					}
				});
				p.$w.find('[name=mon1]').live('keyup',function(){
					p.calgrid1();
				});
				/** /Grilla: Depositos no Registrados en libros */
				
				/** Grilla: Gastos no Registrados en libros */
				var $row = p.$w.find('.gridReference_gast_no_reg').clone();
				$li = $('li',$row);	
				$li.eq(0).html("").addClass('ui-state-default ui-button-text-only');
				$li.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
				$li.eq(2).html("");
				$li.eq(3).html("");
				$row.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody:eq(2)").append( $row.children() );	
				var $tr = p.$w.find('.gridReference_gast_no_reg').clone();
				$tr.wrapInner('<a class="item" href="javascript: void(0);" />');
				$tr.find('[name=fec2]').datepicker();
				$tr.find('[name=btnAdd2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$tr.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
				p.$w.find(".gridBody:eq(2) .item").before( $tr.children() );	
				p.$w.find('[name=btnAdd2]').live('click',function(){
					var $row = p.$w.find('.gridReference_gast_no_reg').clone();
					$li = $('li',$row);
					$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					$row.find('[name=fec2]').datepicker();
					p.$w.find(".gridBody:eq(2) .total").before( $row.children() );
					p.$w.find('.gridBody:eq(2) [name=btnAdd2]').remove();
					p.$w.find('.gridBody:eq(2) .item').eq(p.$w.find('.gridBody:eq(2) .item').length-2).find('li:last').append('<button name="btnAdd2">Agregar</button>');
					p.$w.find('.gridBody:eq(2) [name=btnAdd2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});		
				p.$w.find('[name=btnEli2]').live('click',function(){
					if(p.$w.find('.gridBody:eq(2) .item').length>2){
						$(this).closest('.item').remove();
						p.$w.find('.gridBody:eq(2) [name=btnAdd2]').remove();
						p.$w.find('.gridBody:eq(2) .item').eq(p.$w.find('.gridBody:eq(2) .item').length-2).find('li:last').append('<button name="btnAdd2">Agregar</button>');
						p.$w.find('.gridBody:eq(2) [name=btnAdd2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						p.calgrid2();
					}
				});
				p.$w.find('[name=mon2]').live('keyup',function(){
					p.calgrid2();
				});
				p.$w.find('[name=saldo]').live('keyup',function(){
					p.calgrid2();
				});
				p.$w.find('[name=sald_seg_extr]').live('keyup',function(){
					p.$w.find('[name=dif_simb]').html(K.round(parseFloat($(this).val())-parseFloat(p.$w.find('[name=sald_seg_libr]').html()),2));
				});
				/** /Grilla: Gastos no Registrados en libros */		
				p.$w.find('[name=btnRectificar]').click(function(){
					if(p.$w.find('[name=dif_simb]').html()!=""){
						tsCompNue.windowRectiCheque({callback: function(data){
							if ( data ) {
								p.$w.find(".gridBody:eq(3)").empty();
								var $row1 = p.$w.find('.gridReference_rect').clone();
								$li1 = $('li',$row1);	
								$li1.eq(0).addClass('ui-state-default ui-button-text-only');
								$li1.eq(1).addClass('ui-state-default ui-button-text-only');
								$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
								$li1.eq(3).html(K.round(data.total,2));
								$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
								p.$w.find(".gridBody:eq(3)").append( $row1.children() );
								for (i=0; i < data.cheques.length; i++) {
									var $row = p.$w.find('.gridReference_cheques').clone();
									$li = $('li',$row);	
									$li.eq(0).html( data.cheques[i].cheque );
									$li.eq(1).html( data.cheques[i].monto );
									$li.eq(2).html( data.cheques[i].estracto );
									$li.eq(3).html( data.cheques[i].diferencia );
									$row.wrapInner('<a class="item" href="javascript: void(0);" />');
									$row.find('a').data('data',data.cheques[i]);
									p.$w.find(".gridBody:eq(3) .total").before( $row.children() );		
								}			
							}
							p.tot();
						},'cuenta':p.$w.find('[name=c_ban] :selected').val(),'ano':p.ano,'mes':p.mes,diferencia:p.$w.find('[name=dif_simb]').html(),moneda:p.$w.find('[name=c_ban] :selected').data('data').moneda});
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No existe Diferencia en la Conciliación!',type: 'error'});
					}
				}).button();
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = new Object;
		p.calgrid1 = function(){
			if(p.$w.find('.gridBody:eq(1) .item').length>1){
				total = 0;
				for(i=0;i<(p.$w.find('.gridBody:eq(1) .item').length-1);i++){
					if(p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()!=""){
						total = parseFloat(p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()) + total;
					}
				}
				p.$w.find('.gridBody:eq(1) .total').find('li').eq(3).html(K.round(total,2));
				p.$w.find('[name=dep_no_reg]').html(K.round(total,2));
				p.tot();
			}
		};
		p.calgrid2 = function(){
			if(p.$w.find('.gridBody:eq(2) .item').length>1){
				total = 0;
				for(i=0;i<(p.$w.find('.gridBody:eq(2) .item').length-1);i++){
					if(p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()!=""){
						total = parseFloat(p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()) + total;
					}
				}
				p.$w.find('.gridBody:eq(2) .total').find('li').eq(2).html(K.round(total,2));
				p.$w.find('[name=gast_no_reg]').html(K.round(total,2));
				p.tot();
			}
		};
		p.tot = function(){
			var gastos_no_reg = parseFloat(p.$w.find('[name=gast_no_reg]').html());
			var depos_no_reg = parseFloat(p.$w.find('[name=dep_no_reg]').html());
			var cheques_pendi = parseFloat(p.$w.find('[name=pendi]').html());	
			if(p.$w.find('[name=saldo]').val()!="") { 
				var sald_segun_libr = parseFloat(p.$w.find('[name=saldo]').val());
			}else{
				var sald_segun_libr = 0;
			}
			var total = - gastos_no_reg + depos_no_reg + cheques_pendi + sald_segun_libr;
			p.$w.find('[name=sald_bancos]').html(K.round(total,2));
			p.$w.find('[name=sald_seg_libr]').html(K.round(total,2));
		};
		new K.Window({
			id: 'windowEditctCban'+p.id,
			title: 'Edit Conciliaci&oacute;n Bancaria',
			contentURL: 'ct/cban/edit',
			icon: 'ui-icon-plusthick',
			width: 600,
			modal:true,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.observ = p.$w.find('[name=observ]').val();
					data.activo = new Object;
					data.activo.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.cheques = new Array;
					if(p.$w.find('.gridBody:eq(0) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(0) .item').length;i++){
							data.cheques.push(p.$w.find('.gridBody:eq(0) .item').eq(i).data('data'));
						}
					}
					data.rectificaciones = new Array;
					if(p.$w.find('.gridBody:eq(3) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(3) .item').length;i++){
							data.rectificaciones.push(p.$w.find('.gridBody:eq(3) .item').eq(i).data('data'));
						}
					}
					data.depositos = new Array;
					if(p.$w.find('.gridBody:eq(1) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(1) .item').length;i++){
							var obj = {
									fecha:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(0).val(),
									deposito:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(1).val(),
									agencia:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(2).val(),
									monto:p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()
							};
							data.depositos.push(obj);
						}
					}
					data.gastos = new Array;
					if(p.$w.find('.gridBody:eq(2) .item').length>1){
						for(i=0;i<p.$w.find('.gridBody:eq(2) .item').length;i++){
							var obj = {
									fecha:p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(0).val(),
									descr:p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(1).val(),
									monto:p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()
							};
							data.gastos.push(obj);
						}
					}
					data.total_cheques = p.$w.find('[name=pendi]').html();
					data.total_depositos = p.$w.find('[name=dep_no_reg]').html();
					data.total_gastos = p.$w.find('[name=gast_no_reg]').html();
					data.total_rectificaciones = p.$w.find('.gridBody:eq(3) .total').find('li').eq(3).html();
					data.saldo_libro_bancos = p.$w.find('[name=saldo]').val();
					data.saldo_bancos = parseFloat(data.saldo_libro_bancos)+parseFloat(data.total_cheques)+parseFloat(data.total_depositos)-parseFloat(data.total_gastos);
					data.saldo_extracto = p.$w.find('[name=sald_seg_extr]').val();
					data.diferencia = parseFloat(data.saldo_extracto)-parseFloat(data.saldo_bancos);
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/cban/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Tipo de nota fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditctCban'+p.id);
				K.block({$element: p.$w});			
				p.$w.find('[name=pendi]').html("0.00");
				p.$w.find('[name=dep_no_reg]').html("0.00");
				p.$w.find('[name=gast_no_reg]').html("0.00");
				p.$w.find('[name=sald_bancos]').html("0.00");					
				$.post('ts/ctban/all_cban','mes='+p.mes+'&ano='+p.ano,function(data){
					var $cbo = p.$w.find('[name=c_ban]');
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
						p.$w.find('[name=moneda]').html(ctCban.moneda[data[0].moneda].descr);
						p.$w.find('[name=banco]').html(data[0].banco.nomb);
					}
				},'json');
				p.$w.find('[name=c_ban]').change(function(){
					var dat = $(this).find('option:selected').data('data');
					p.$w.find('[name=moneda]').html(ctCban.moneda[dat.moneda].descr);
					p.$w.find('[name=banco]').html(dat.banco.nomb);
					p.$w.find(".gridBody:eq(0)").empty();
					p.$w.find(".gridBody:eq(3)").empty();
				});
				p.$w.find('[name=c_ban]').attr('disabled','disabled');
				/** Grilla: Depositos no Registrados en libros */
				var $row1 = p.$w.find('.gridReference_dep_no_reg').clone();
				$li1 = $('li',$row1);	
				$li1.eq(0).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(1).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
				$li1.eq(3).html("");
				$li1.eq(4).html("");
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody:eq(1)").append( $row1.children() );		
				p.$w.find('[name=btnAdd1]').live('click',function(){
					var $row = p.$w.find('.gridReference_dep_no_reg').clone();
					$li = $('li',$row);
					$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					$row.find('[name=fec1]').datepicker();
					p.$w.find(".gridBody:eq(1) .total").before( $row.children() );
					p.$w.find('.gridBody:eq(1) [name=btnAdd1]').remove();
					p.$w.find('.gridBody:eq(1) .item').eq(p.$w.find('.gridBody:eq(1) .item').length-2).find('li:last').append('<button name="btnAdd1">Agregar</button>');
					p.$w.find('.gridBody:eq(1) [name=btnAdd1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli1]').live('click',function(){
					if(p.$w.find('.gridBody:eq(1) .item').length>2){
						$(this).closest('.item').remove();			
						p.$w.find('.gridBody:eq(1) [name=btnAdd1]').remove();
						p.$w.find('.gridBody:eq(1) .item').eq(p.$w.find('.gridBody:eq(1) .item').length-2).find('li:last').append('<button name="btnAdd1">Agregar</button>');
						p.$w.find('.gridBody:eq(1) [name=btnAdd1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						p.calgrid1();
					}
				});
				p.$w.find('[name=mon1]').live('keyup',function(){
					p.calgrid1();
				});
				/** /Grilla: Depositos no Registrados en libros */
				
				/** Grilla: Gastos no Registrados en libros */
				var $row = p.$w.find('.gridReference_gast_no_reg').clone();
				$li = $('li',$row);	
				$li.eq(0).html("").addClass('ui-state-default ui-button-text-only');
				$li.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
				$li.eq(2).html("");
				$li.eq(3).html("");
				$row.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody:eq(2)").append( $row.children() );	
				p.$w.find('[name=btnAdd2]').live('click',function(){
					var $row = p.$w.find('.gridReference_gast_no_reg').clone();
					$li = $('li',$row);
					$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					$row.find('[name=fec2]').datepicker();
					p.$w.find(".gridBody:eq(2) .total").before( $row.children() );
					p.$w.find('.gridBody:eq(2) [name=btnAdd2]').remove();
					p.$w.find('.gridBody:eq(2) .item').eq(p.$w.find('.gridBody:eq(2) .item').length-2).find('li:last').append('<button name="btnAdd2">Agregar</button>');
					p.$w.find('.gridBody:eq(2) [name=btnAdd2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});		
				p.$w.find('[name=btnEli2]').live('click',function(){
					if(p.$w.find('.gridBody:eq(2) .item').length>2){
						$(this).closest('.item').remove();
						p.$w.find('.gridBody:eq(2) [name=btnAdd2]').remove();
						p.$w.find('.gridBody:eq(2) .item').eq(p.$w.find('.gridBody:eq(2) .item').length-2).find('li:last').append('<button name="btnAdd2">Agregar</button>');
						p.$w.find('.gridBody:eq(2) [name=btnAdd2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						p.calgrid2();
					}
				});
				p.$w.find('[name=mon2]').live('keyup',function(){
					p.calgrid2();
				});
				p.$w.find('[name=saldo]').live('keyup',function(){
					p.calgrid2();
				});
				p.$w.find('[name=sald_seg_extr]').live('keyup',function(){
					p.$w.find('[name=dif_simb]').html(K.round(parseFloat($(this).val())-parseFloat(p.$w.find('[name=sald_seg_libr]').html()),2));
				});
				/** /Grilla: Gastos no Registrados en libros */					
				/** Llenado de Data */
				$.post('ct/cban/get','id='+p.id,function(data){
					p.$w.find('[name=mes]').html(ciHelper.meses[parseFloat(data.periodo.mes)-1]);
					p.$w.find('[name=ano]').html(data.periodo.ano);
					p.$w.find('[name=c_ban]').selectVal(data.cuenta_banco._id.$id);
					p.$w.find('[name=moneda]').html(ctCban.moneda[data.cuenta_banco.moneda].descr);
					p.$w.find('[name=banco]').html(data.cuenta_banco.banco.nomb);
					p.$w.find('[name=saldo]').val(data.saldo_libro_bancos);
					p.$w.find('[name=pendi]').html(data.total_cheques);
					p.$w.find('[name=c_ban]').append('<option value="'+data.cuenta_banco._id.$id+'" selected>'+data.cuenta_banco.cod+'</option>');
					p.$w.find('[name=btnSeleccionarCheques]').click(function(){
						tsCompNue.windowSelectCheque({callback: function(data){
							if ( data ) {
								p.$w.find("[name=grid_cheques]").empty();
								var $row1 = p.$w.find('.gridReference_cheques').clone();
								$li1 = $('li',$row1);	
								$li1.eq(0).addClass('ui-state-default ui-button-text-only');
								$li1.eq(1).addClass('ui-state-default ui-button-text-only');
								$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
								$li1.eq(3).html(K.round(data.total,2));
								$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
								p.$w.find('[name=pendi]').html(K.round(data.total,2));
								p.$w.find("[name=grid_cheques]").append( $row1.children() );
								for (i=0; i < data.cheques.length; i++) {
									var $row = p.$w.find('.gridReference_cheques').clone();
									$li = $('li',$row);	
									$li.eq(0).html( data.cheques[i].fecha );
									$li.eq(1).html( data.cheques[i].cheque );
									$li.eq(2).html( ciHelper.enti.formatName(data.cheques[i].detalle) );
									$li.eq(3).html( data.cheques[i].monto );
									$row.wrapInner('<a class="item" href="javascript: void(0);" />');
									$row.find('a').data('data',data.cheques[i]);
									p.$w.find("[name=grid_cheques] .total").before( $row.children() );		
								}			
							}
							p.tot();
						},'cuenta':data.cuenta_banco._id.$id,'ano':p.ano,'mes':p.mes});
					}).button();
					p.$w.find('[name=btnRectificar]').click(function(){
						if(p.$w.find('[name=dif_simb]').html()!=""){
							tsCompNue.windowRectiCheque({callback: function(data){
								if ( data ) {
									p.$w.find(".gridBody:eq(3)").empty();
									var $row1 = p.$w.find('.gridReference_rect').clone();
									$li1 = $('li',$row1);	
									$li1.eq(0).addClass('ui-state-default ui-button-text-only');
									$li1.eq(1).addClass('ui-state-default ui-button-text-only');
									$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
									$li1.eq(3).html(K.round(data.total,2));
									$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
									p.$w.find(".gridBody:eq(3)").append( $row1.children() );
									for (i=0; i < data.cheques.length; i++) {
										var $row = p.$w.find('.gridReference_cheques').clone();
										$li = $('li',$row);	
										$li.eq(0).html( data.cheques[i].cheque );
										$li.eq(1).html( data.cheques[i].monto );
										$li.eq(2).html( data.cheques[i].estracto );
										$li.eq(3).html( data.cheques[i].diferencia );
										$row.wrapInner('<a class="item" href="javascript: void(0);" />');
										$row.find('a').data('data',data.cheques[i]);
										p.$w.find(".gridBody:eq(3) .total").before( $row.children() );		
									}			
								}
								p.tot();
							},'cuenta':data.cuenta_banco._id.$id,'ano':p.ano,'mes':p.mes,diferencia:p.$w.find('[name=dif_simb]').html(),moneda:data.cuenta_banco.moneda});
						}else{
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No existe Diferencia en la Conciliación!',type: 'error'});
						}
					}).button();
					/**Cheques pendientes*/
					if(data.cheques){
						p.$w.find("[name=grid_cheques]").empty();
						var $row1 = p.$w.find('.gridReference_cheques').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(3).html(K.round(data.total_cheques,2));
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find("[name=grid_cheques]").append( $row1.children() );
						
						for (i=0; i < data.cheques.length; i++) {							
							var $row = p.$w.find('.gridReference_cheques').clone();
							$li = $('li',$row);	
							$li.eq(0).html( ciHelper.dateFormat(data.cheques[i].fecha) );
							$li.eq(1).html( data.cheques[i].cheque );
							$li.eq(2).html( ciHelper.enti.formatName(data.cheques[i].detalle) );
							$li.eq(3).html( data.cheques[i].monto );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							var dat = {
									fecha:ciHelper.dateFormat(data.cheques[i].fecha),
									cheque:data.cheques[i].cheque,
									detalle:data.cheques[i].detalle,
									monto:data.cheques[i].monto
							};
							$row.find('a').data('data',dat);
							p.$w.find(".gridBody:eq(0) .total").before( $row.children() );		
						}			
					}
					/** /Cheques pendientes*/
					p.$w.find('[name=dep_no_reg]').html(data.total_depositos);
					if(data.depositos){
						p.$w.find(".gridBody:eq(1)").empty();
						var $row1 = p.$w.find('.gridReference_rect').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(3).html(K.round(data.total_depositos,2));
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(1)").append( $row1.children() );
						for(i=0;i<data.depositos.length;i++){
							var $row = p.$w.find('.gridReference_dep_no_reg').clone();
							$li = $('li',$row);
							$li.eq(0).find('input').val(ciHelper.dateFormatBDNotHour(data.depositos[i].fecha));
							$li.eq(1).find('input').val(data.depositos[i].deposito);
							$li.eq(2).find('input').val(data.depositos[i].agencia);
							$li.eq(3).find('input').val(data.depositos[i].monto);
							$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" />');
							$row.find('[name=fec1]').datepicker();
							p.$w.find(".gridBody:eq(1) .total").before( $row.children() );
							p.$w.find('.gridBody:eq(1) [name=btnAdd1]').remove();
							p.$w.find('.gridBody:eq(1) .item').eq(p.$w.find('.gridBody:eq(1) .item').length-2).find('li:last').append('<button name="btnAdd1">Agregar</button>');
							p.$w.find('.gridBody:eq(1) [name=btnAdd1]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
					}
					p.$w.find('[name=gast_no_reg]').html(data.total_gastos);
					if(data.gastos){
						p.$w.find(".gridBody:eq(2)").empty();
						var $row1 = p.$w.find('.gridReference_gast_no_reg').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).html("").addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html(K.round(data.total_gastos,2));
						$li1.eq(3).html("");
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(2)").append( $row1.children() );
						for(i=0;i<data.gastos.length;i++){
							var $row = p.$w.find('.gridReference_gast_no_reg').clone();
							$li = $('li',$row);
							$li.eq(0).find('input').val(ciHelper.dateFormatBDNotHour(data.gastos[i].fecha));
							$li.eq(1).find('input').val(data.gastos[i].descr);
							$li.eq(2).find('input').val(data.gastos[i].monto);
							$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" />');
							$row.find('[name=fec2]').datepicker();
							p.$w.find(".gridBody:eq(2) .total").before( $row.children() );
							p.$w.find('.gridBody:eq(2) [name=btnAdd2]').remove();
							p.$w.find('.gridBody:eq(2) .item').eq(p.$w.find('.gridBody:eq(2) .item').length-2).find('li:last').append('<button name="btnAdd2">Agregar</button>');
							p.$w.find('.gridBody:eq(2) [name=btnAdd2]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}						
					}
					p.$w.find('[name=saldo_bancos]').html(data.saldo_bancos);
					p.$w.find('[name=sald_seg_extr]').val(data.saldo_extracto);
					p.$w.find('[name=sald_seg_libr]').html(data.saldo_bancos);
					p.$w.find('[name=dif_simb]').html(data.diferencia);
					p.$w.find('[name=observ]').val(data.observ);
					if(data.rectificaciones){
						var $row1 = p.$w.find('.gridReference_rect').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(3).html(K.round(data.total_rectificaciones,2));
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(3)").append( $row1.children() );
						for(i=0;i<data.rectificaciones.length;i++){
							var $row = p.$w.find('.gridReference_rect').clone();
							$li = $('li',$row);	
							$li.eq(0).html( data.rectificaciones[i].cheque );
							$li.eq(1).html( data.rectificaciones[i].monto );
							$li.eq(2).html( data.rectificaciones[i].estracto );
							$li.eq(3).html( data.rectificaciones[i].diferencia );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',data.rectificaciones[i]);
							p.$w.find(".gridBody:eq(3) .total").before( $row.children() );	
						}
					}
					p.$w.find('[name=sald_bancos]').html(K.round(data.saldo_bancos,2));
					p.$w.find('[name=sald_seg_libr]').html(K.round(data.saldo_bancos,2));
				},'json');	
				/** /Llenado de Data */
				
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		if(p==null) p = new Object;
		p.calgrid1 = function(){
			if(p.$w.find('.gridBody:eq(1) .item').length>1){
				total = 0;
				for(i=0;i<(p.$w.find('.gridBody:eq(1) .item').length-1);i++){
					if(p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()!=""){
						total = parseFloat(p.$w.find('.gridBody:eq(1) .item').eq(i).find('input').eq(3).val()) + total;
					}
				}
				p.$w.find('.gridBody:eq(1) .total').find('li').eq(3).html(K.round(total,2));
				p.$w.find('[name=dep_no_reg]').html(K.round(total,2));
				p.tot();
			}
		};
		p.calgrid2 = function(){
			if(p.$w.find('.gridBody:eq(2) .item').length>1){
				total = 0;
				for(i=0;i<(p.$w.find('.gridBody:eq(2) .item').length-1);i++){
					if(p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()!=""){
						total = parseFloat(p.$w.find('.gridBody:eq(2) .item').eq(i).find('input').eq(2).val()) + total;
					}
				}
				p.$w.find('.gridBody:eq(2) .total').find('li').eq(2).html(K.round(total,2));
				p.$w.find('[name=gast_no_reg]').html(K.round(total,2));
				p.tot();
			}
		};
		p.tot = function(){
			var gastos_no_reg = parseFloat(p.$w.find('[name=gast_no_reg]').html());
			var depos_no_reg = parseFloat(p.$w.find('[name=dep_no_reg]').html());
			var cheques_pendi = parseFloat(p.$w.find('[name=pendi]').html());	
			if(p.$w.find('[name=saldo]').val()!="") { 
				var sald_segun_libr = parseFloat(p.$w.find('[name=saldo]').val());
			}else{
				var sald_segun_libr = 0;
			}
			var total = -gastos_no_reg + depos_no_reg + cheques_pendi + sald_segun_libr;
			p.$w.find('[name=sald_bancos]').html(K.round(total,2));
			p.$w.find('[name=sald_seg_libr]').html(K.round(total,2));
		};
		new K.Window({
			id: 'windowDetailsctCban'+p.id,
			title: 'Ver Conciliaci&oacute;n Bancaria',
			contentURL: 'ct/cban/edit',
			icon: 'ui-icon-plusthick',
			width: 600,
			height: 450,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsctCban'+p.id);
				K.block({$element: p.$w});			
				p.$w.find('[name=pendi]').html("0.00");
				p.$w.find('[name=dep_no_reg]').html("0.00");
				p.$w.find('[name=gast_no_reg]').html("0.00");
				p.$w.find('[name=sald_bancos]').html("0.00");
				p.$w.find('[name=btnRectificar]').remove();
				p.$w.find('[name=btnSeleccionarCheques]').remove();
				$.post('ts/ctban/all_cban','mes='+p.mes+'&ano='+p.ano,function(data){
					var $cbo = p.$w.find('[name=c_ban]');
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
						p.$w.find('[name=moneda]').html(ctCban.moneda[data[0].moneda].descr);
						p.$w.find('[name=banco]').html(data[0].banco.nomb);
					}
				},'json');
				p.$w.find('[name=c_ban]').change(function(){
					var dat = $(this).find('option:selected').data('data');
					p.$w.find('[name=moneda]').html(ctCban.moneda[dat.moneda].descr);
					p.$w.find('[name=banco]').html(dat.banco.nomb);
					p.$w.find(".gridBody:eq(0)").empty();
					p.$w.find(".gridBody:eq(3)").empty();
				});
				p.$w.find('[name=c_ban]').attr('disabled','disabled');
				/** Grilla: Depositos no Registrados en libros */
				var $row1 = p.$w.find('.gridReference_dep_no_reg').clone();
				$li1 = $('li',$row1);	
				$li1.eq(0).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(1).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
				$li1.eq(3).html("");
				$li1.eq(4).html("");
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody:eq(1)").append( $row1.children() );		
				/** /Grilla: Depositos no Registrados en libros */
				
				/** Grilla: Gastos no Registrados en libros */
				var $row = p.$w.find('.gridReference_gast_no_reg').clone();
				$li = $('li',$row);	
				$li.eq(0).html("").addClass('ui-state-default ui-button-text-only');
				$li.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
				$li.eq(2).html("");
				$li.eq(3).html("");
				$row.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody:eq(2)").append( $row.children() );	
				/** /Grilla: Gastos no Registrados en libros */					
				/** Llenado de Data */
				$.post('ct/cban/get','id='+p.id,function(data){
					p.$w.find('[name=mes]').html(ciHelper.meses[parseFloat(data.periodo.mes)-1]);
					p.$w.find('[name=ano]').html(data.periodo.ano);
					p.$w.find('[name=c_ban]').selectVal(data.cuenta_banco._id.$id);
					p.$w.find('[name=moneda]').html(ctCban.moneda[data.cuenta_banco.moneda].descr);
					p.$w.find('[name=banco]').html(data.cuenta_banco.banco.nomb);
					p.$w.find('[name=saldo]').val(data.saldo_libro_bancos);
					p.$w.find('[name=pendi]').html(data.total_cheques);
					p.$w.find('[name=c_ban]').append('<option value="'+data.cuenta_banco._id.$id+'" selected>'+data.cuenta_banco.cod+'</option>');					
					/**Cheques pendientes*/
					if(data.cheques){
						p.$w.find("[name=grid_cheques]").empty();
						var $row1 = p.$w.find('.gridReference_cheques').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(3).html(K.round(data.total_cheques,2));
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find("[name=grid_cheques]").append( $row1.children() );
						
						for (i=0; i < data.cheques.length; i++) {							
							var $row = p.$w.find('.gridReference_cheques').clone();
							$li = $('li',$row);	
							$li.eq(0).html( ciHelper.dateFormat(data.cheques[i].fecha) );
							$li.eq(1).html( data.cheques[i].cheque );
							$li.eq(2).html( ciHelper.enti.formatName(data.cheques[i].detalle) );
							$li.eq(3).html( data.cheques[i].monto );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							var dat = {
									fecha:ciHelper.dateFormat(data.cheques[i].fecha),
									cheque:data.cheques[i].cheque,
									detalle:data.cheques[i].detalle,
									monto:data.cheques[i].monto
							};
							$row.find('a').data('data',dat);
							p.$w.find(".gridBody:eq(0) .total").before( $row.children() );		
						}			
					}
					/** /Cheques pendientes*/
					p.$w.find('[name=dep_no_reg]').html(data.total_depositos);
					if(data.depositos){
						p.$w.find(".gridBody:eq(1)").empty();
						var $row1 = p.$w.find('.gridReference_rect').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(3).html(K.round(data.total_depositos,2));
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(1)").append( $row1.children() );
						for(i=0;i<data.depositos.length;i++){
							var $row = p.$w.find('.gridReference_dep_no_reg').clone();
							$li = $('li',$row);
							$li.eq(0).find('input').val(ciHelper.dateFormatBDNotHour(data.depositos[i].fecha));
							$li.eq(1).find('input').val(data.depositos[i].deposito);
							$li.eq(2).find('input').val(data.depositos[i].agencia);
							$li.eq(3).find('input').val(data.depositos[i].monto);
							$row.find('[name=btnEli1]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" />');
							$row.find('[name=fec1]').datepicker();
							$row.find('[name=btnEli1]').remove();
							p.$w.find(".gridBody:eq(1) .total").before( $row.children() );
							p.$w.find('.gridBody:eq(1) [name=btnAdd1]').remove();
						}
					}
					p.$w.find('[name=gast_no_reg]').html(data.total_gastos);
					if(data.gastos){
						p.$w.find(".gridBody:eq(2)").empty();
						var $row1 = p.$w.find('.gridReference_gast_no_reg').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).html("").addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html(K.round(data.total_gastos,2));
						$li1.eq(3).html("");
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(2)").append( $row1.children() );
						for(i=0;i<data.gastos.length;i++){
							var $row = p.$w.find('.gridReference_gast_no_reg').clone();
							$li = $('li',$row);
							$li.eq(0).find('input').val(ciHelper.dateFormatBDNotHour(data.gastos[i].fecha));
							$li.eq(1).find('input').val(data.gastos[i].descr);
							$li.eq(2).find('input').val(data.gastos[i].monto);
							$row.find('[name=btnEli2]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" />');
							$row.find('[name=fec2]').datepicker();
							$row.find('[name=btnEli2]').remove();
							p.$w.find(".gridBody:eq(2) .total").before( $row.children() );
							p.$w.find('.gridBody:eq(2) [name=btnAdd2]').remove();
						}						
					}
					p.$w.find('[name=saldo_bancos]').html(data.saldo_bancos);
					p.$w.find('[name=sald_seg_extr]').val(data.saldo_extracto);
					p.$w.find('[name=sald_seg_libr]').html(data.saldo_bancos);
					p.$w.find('[name=dif_simb]').html(data.diferencia);
					p.$w.find('[name=observ]').val(data.observ);
					if(data.rectificaciones){
						var $row1 = p.$w.find('.gridReference_rect').clone();
						$li1 = $('li',$row1);	
						$li1.eq(0).addClass('ui-state-default ui-button-text-only');
						$li1.eq(1).addClass('ui-state-default ui-button-text-only');
						$li1.eq(2).html( "Total" ).addClass('ui-state-default ui-button-text-only');
						$li1.eq(3).html(K.round(data.total_rectificaciones,2));
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						p.$w.find(".gridBody:eq(3)").append( $row1.children() );
						for(i=0;i<data.rectificaciones.length;i++){
							var $row = p.$w.find('.gridReference_rect').clone();
							$li = $('li',$row);	
							$li.eq(0).html( data.rectificaciones[i].cheque );
							$li.eq(1).html( data.rectificaciones[i].monto );
							$li.eq(2).html( data.rectificaciones[i].estracto );
							$li.eq(3).html( data.rectificaciones[i].diferencia );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',data.rectificaciones[i]);
							p.$w.find(".gridBody:eq(3) .total").before( $row.children() );	
						}
					}
					p.$w.find('[name=sald_bancos]').html(K.round(data.saldo_bancos,2));
					p.$w.find('[name=sald_seg_libr]').html(K.round(data.saldo_bancos,2));
				},'json');	
				/** /Llenado de Data */
				
				K.unblock({$element: p.$w});
			}
		});
	}
};