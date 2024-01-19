/*******************************************************************************
Tipos de Nota */
ctEpresMovi = {
	states : {
		"A":{descr:"Aperturado"},
		"C":{descr:"Cerrado"}
	},
	sumAll :function(){
		var s_a_d = 0;
		var s_a_h = 0;
		var s_m_d = 0;
		var s_m_h = 0;
		var s_s_d = 0;
		var s_s_h = 0;
		var s_p_d = 0;
		var s_p_h = 0;
		for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
			if($mainPanel.find('.gridBody .item').eq(i).data('data').cuentas.hijos){
				s_a_d = parseFloat($mainPanel.find('.gridBody .item').eq(i).find('li').eq(2).html()) + s_a_d;
				s_a_h = parseFloat($mainPanel.find('.gridBody .item').eq(i).find('li').eq(3).html()) + s_a_h;
				
				var grid_s_m_d = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
				if(grid_s_m_d=="")grid_s_m_d = "0";
				s_m_d = parseFloat(grid_s_m_d) + s_m_d;
				
				var grid_s_m_h = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(1).val();
				if(grid_s_m_h=="")grid_s_m_h = "0";
				s_m_h = parseFloat(grid_s_m_h) + s_m_h;
				
				s_s_d = parseFloat($mainPanel.find('.gridBody .item').eq(i).find('li').eq(6).html()) + s_s_d;
				s_s_h = parseFloat($mainPanel.find('.gridBody .item').eq(i).find('li').eq(7).html()) + s_s_h;
				s_p_d = parseFloat($mainPanel.find('.gridBody .item').eq(i).find('li').eq(8).html()) + s_p_d;
				s_p_h = parseFloat($mainPanel.find('.gridBody .item').eq(i).find('li').eq(9).html()) + s_p_h;
			}
		}
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(2).html(K.round(s_a_d,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(3).html(K.round(s_a_h,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(4).html(K.round(s_m_d,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(5).html(K.round(s_m_h,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(6).html(K.round(s_s_d,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(7).html(K.round(s_s_h,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(8).html(K.round(s_p_d,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(9).html(K.round(s_p_h,2));
	},
	sumHor : function(){
		for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
			var input_0 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
			var input_1 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(1).val();
			if(input_0=="")input_0 = "0.00";
			if(input_1=="")input_1 = "0.00";
			var debe_a = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(2).html();
			var haber_a = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(3).html();
			var suma_d = parseFloat(input_0)+parseFloat(debe_a);
			var suma_h = parseFloat(input_1)+parseFloat(haber_a);
			var saldo_d = suma_d - suma_h ;
			if(suma_h>suma_d) saldo_d = 0;
			var saldo_h = suma_h - suma_d ;
			if(suma_d>suma_h) saldo_h = 0;
			$mainPanel.find('.gridBody .item').eq(i).find('li').eq(6).html(K.round(suma_d,2));
			$mainPanel.find('.gridBody .item').eq(i).find('li').eq(7).html(K.round(suma_h,2));
			$mainPanel.find('.gridBody .item').eq(i).find('li').eq(8).html(K.round(saldo_d,2));
			$mainPanel.find('.gridBody .item').eq(i).find('li').eq(9).html(K.round(saldo_h,2));
		}
	},
	init: function(){
		if($('#pageWrapper [child=epres]').length<=0){
			$.post('ct/navg/epres',function(data){
				var $p = $('#pageWrapperLeft');
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="epres" />');
					$p.find("[name=ctEpres]").after( $row.children() );
				}
				$p.find('[name=ctEpres]').data('epres',$('#pageWrapper [child=epres]:first').data('epres'));
				$p.find('[name=ctEpresAuxI]').click(function(){ ctEpresAuxI.init(); });
				$p.find('[name=ctEpresAuxG]').click(function(){ ctEpresAuxG.init(); });
				$p.find('[name=ctEpresCuadce]').click(function(){ ctEpresCuadce.init(); });
				$p.find('[name=ctEpresPpres]').click(function(){ ctEpresPpres.init(); });
				$p.find('[name=ctEpresMovi]').click(function(){ ctEpresMovi.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctEpresMovi',
			titleBar: {
				title: 'Movimientos de las Cuentas'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/movi',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height()-$mainPanel.find('table').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100}).change(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresMovi.loadData({page:1});
			    });			
				$mainPanel.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()).keyup(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresMovi.loadData({page:1});
				}); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					ctEpresMovi.loadData({page:1});
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresMovi.loadData({page:1});
			    });
				$mainPanel.find('[name=btnGuardar]').click(function(){
					var data = new Object;
					data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.estado = "A";
					data.periodo = {
							mes : $mainPanel.find('[name=mes] :selected').val(),
							ano : $mainPanel.find('[name=ano]').val()
					};
					data.movimientos = new Array;
					for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
						var movimiento = new Object;
						movimiento.cuenta = new Object;
						movimiento.cuenta._id = $mainPanel.find('.gridBody .item').eq(i).data('data')._id.$id;
						movimiento.cuenta.cod = $mainPanel.find('.gridBody .item').eq(i).data('data').cod;
						movimiento.cuenta.descr = $mainPanel.find('.gridBody .item').eq(i).data('data').descr;
						movimiento.debe_actual = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
						if(movimiento.debe_actual=="")movimiento.debe_actual="0.00";
						movimiento.haber_actual = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(1).val();
						if(movimiento.haber_actual=="")movimiento.haber_actual="0.00";
						movimiento.debe_anterior = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(2).html();
						movimiento.haber_anterior = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(3).html();
						data.movimientos.push(movimiento);
					}
					K.sendingInfo();
					$.post('ct/movi/save',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Los movimientos fuer&oacute;n guardados con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});	
				$mainPanel.find('[name=btnCerrar]').click(function(){
					var data = new Object;
					data.periodo = {
							mes : $mainPanel.find('[name=mes] :selected').val(),
							ano : $mainPanel.find('[name=ano]').val()
					};
					K.sendingInfo();
					$.post('ct/movi/cerrar',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Periodo Fue cerrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				}).button({icons: {primary: 'ui-icon-check'}});
				$mainPanel.find('[name=btnEstructura]').click(function(){
					ctEpresMovi.windowEstructura();
				}).button({icons: {primary: 'ui-icon-note'}});
				$mainPanel.find('[name=mes]').change();
				$mainPanel.find('[name^=debe_]').live('keyup',function(){
					var montoing = parseFloat($(this).val());
					var cuenpadre  = $(this).attr("padre");
					var hijos = $mainPanel.find('.gridBody [padre='+cuenpadre+']');
						var recalcular = 0;
						for(i=0;i<hijos.length;i++){
							//recalcular.push(hijos.eq(i).val());
							if(hijos.eq(i).val()=="") monto=0;
							else monto=hijos.eq(i).val();
							recalcular = parseFloat(monto) + recalcular;
						}
						$mainPanel.find('.gridBody [name='+cuenpadre+']').val(recalcular).keyup();	
						ctEpresMovi.sumHor();
						ctEpresMovi.sumAll();
				});
				$mainPanel.find('[name^=haber_]').live('keyup',function(){
					var montoing = parseFloat($(this).val());
					var cuenpadre  = $(this).attr("padre");
					var hijos = $mainPanel.find('.gridBody [padre='+cuenpadre+']');
						var recalcular = 0;
						for(i=0;i<hijos.length;i++){
							//recalcular.push(hijos.eq(i).val());
							if(hijos.eq(i).val()=="") monto=0;
							else monto=hijos.eq(i).val();
							recalcular = parseFloat(monto) + recalcular;
						}
						$mainPanel.find('.gridBody [name='+cuenpadre+']').val(recalcular).keyup();	
						ctEpresMovi.sumHor();
						ctEpresMovi.sumAll();
				});
				$mainPanel.find('[name=btnExportar]').click(function(){
					window.open('ct/repo/movi?mes='+$mainPanel.find('[name=mes] :selected').val()+'&ano='+$mainPanel.find('[name=ano]').val());
				}).button({icons: {primary: 'ui-icon-print'}});
				$mainPanel.find('[name=btnCerrar]').button( "option", "disabled", true );
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
	    	mes: $mainPanel.find('[name=mes] :selected').val(),
	    	ano: $mainPanel.find('[name=ano]').val()
		});
	    $.post("ct/movi/lista", params, function(data){
	    	if ( data.items!=null ) { 
	    		$mainPanel.find('[name=btnEstructura]').button( "option", "disabled", true );
	    		$mainPanel.find('[name=btnCerrar]').button( "option", "disabled", false );
	    		$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", false );
	    		$mainPanel.find('[name=estado]').html(ctEpresMovi.states[data.items[0].estado].descr);
	    		if(data.items[0].estado=="C"){
	    			$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", true );
	    			$mainPanel.find('[name=btnCerrar]').button( "option", "disabled", true );
	    			$mainPanel.find('[name=btnExportar]').button( "option", "disabled", false );
	    		}
	    		var $row1 = $('.gridReference','#mainPanel').clone();
				$li1 = $('li',$row1);
				$li1.eq(0).html("");
				$li1.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
				$li1.eq(4).html("");
				$li1.eq(5).html("");
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				$("#mainPanel .gridBody").append( $row1.children() );
				for(i=0; i < data.items.length; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html(result.cuenta.cod);
					$li.eq(1).html(result.cuenta.descr);
					$li.eq(2).html(K.round(result.debe_anterior,2));
					$li.eq(3).html(K.round(result.haber_anterior,2));
					var hijos = false;
					if(result.cuenta.cuentas.hijos){
						hijos = true;
					}
					$row.find('input').eq(0).attr('padre','debe_'+result.cuenta.cuentas.padre+'').attr('name','debe_'+result.cuenta._id.$id+'').attr('readonly',hijos).val(K.round(result.debe_actual,2));
					$row.find('input').eq(1).attr('padre','haber_'+result.cuenta.cuentas.padre+'').attr('name','haber_'+result.cuenta._id.$id+'').attr('readonly',hijos).val(K.round(result.haber_actual,2));					
					$row.wrapInner('<a id="'+result.cuenta._id.$id+'" name="'+result.cuenta.cod+'" class="item" href="javascript: void(0);" />');
					$row.find('a').data('data',result.cuenta);
				    $("#mainPanel .gridBody .total").before( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
				}
				ctEpresMovi.sumHor();
				ctEpresMovi.sumAll();
	    	}else {
	    		$mainPanel.find('[name=btnEstructura]').button( "option", "disabled", false );
	    		$mainPanel.find('[name=btnCerrar]').button( "option", "disabled", true );
	    		$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", false );
	    		$mainPanel.find('[name=btnExportar]').button( "option", "disabled", true );
	    		$mainPanel.find('[name=estado]').html("No Aperturado");
	    		$.post('ct/estr/lista',params,function(data2){
		        	if(data2!=null){
		        		var $row1 = $('.gridReference','#mainPanel').clone();
						$li1 = $('li',$row1);
						$li1.eq(0).html("");
						$li1.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
						$li1.eq(4).html("");
						$li1.eq(5).html("");
						$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
						$("#mainPanel .gridBody").append( $row1.children() );		
						for (i=0; i < data2.cuentas.length; i++) {
							var $row = $('.gridReference','#mainPanel').clone();
							$li = $('li',$row);
							$li.eq(0).html(data2.cuentas[i].cod);
							$li.eq(1).html(data2.cuentas[i].descr);
							$li.eq(2).html(data2.cuentas[i].debe_anterior);
							$li.eq(3).html(data2.cuentas[i].haber_anterior);
							var hijos = false;
							if(data2.cuentas[i].cuentas.hijos){
								hijos = true;
							}
							$row.find('input').eq(0).attr('padre','debe_'+data2.cuentas[i].cuentas.padre+'').attr('name','debe_'+data2.cuentas[i]._id.$id+'').attr('readonly',hijos);
							$row.find('input').eq(1).attr('padre','haber_'+data2.cuentas[i].cuentas.padre+'').attr('name','haber_'+data2.cuentas[i]._id.$id+'').attr('readonly',hijos);					
							$row.wrapInner('<a id="'+data2.cuentas[i]._id.$id+'" name="'+data2.cuentas[i].cod+'" class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',data2.cuentas[i]);
					        $("#mainPanel .gridBody .total").before( $row.children() );
							ciHelper.gridButtons($("#mainPanel .gridBody"));
						} 
		        	}else{
		        		$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", true );
		        		K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se ha generado una estructura para generar movimientos de cuenta!',type: 'error'});
		        	}      	 
	    		},'json');
	    	}
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowEstructura: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewctEpresMoviEstr',
			title: 'Definir Estructura',
			contentURL: 'ct/movi/estructura',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			modal: true,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.cuentas = new Array;
					if(p.$w.find('.gridBody input:checked').length>0){
						for(i=0;i<p.$w.find('.gridBody .item').length;i++){
							if(p.$w.find('.gridBody .item').eq(i).find('input:checked').val()=="1"){
								var cuenta = {
										_id:p.$w.find('.gridBody .item').eq(i).data('data')._id.$id,
										cod:p.$w.find('.gridBody .item').eq(i).data('data').cod,
										descr:p.$w.find('.gridBody .item').eq(i).data('data').descr
								};
								data.cuentas.push(cuenta);
							}
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe Seleccionar al menos una cuenta!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/estr/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Estructura fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewctEpresMoviEstr');
				K.block({$element: p.$w});
				var params = {page:1,page_rows:1000,tipo:"PR",id:""};
				$.post('ct/pcon/lista',params,function(data){
					if(data.items!=null){
						for(i=0;i<data.items.length;i++){
							var $row1 = p.$w.find('.gridReference').clone();
							$li1 = $('li',$row1);
							$li1.eq(1).html(data.items[i].cod);
							$li1.eq(2).html(data.items[i].descr);						
							$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
							$row1.find('a').data('data',data.items[i]);
							p.$w.find('.gridBody').append( $row1.children() );	
						}
					}
					K.unblock({$element: p.$w});
				},'json');				
			}
		});
	}	
};