/*******************************************************************************
Tipos de Nota */
ctEpresPpres = {
	states : {
		"A":{descr:"Aperturado"},
		"C":{descr:"Cerrado"}
	},
	sumHier :function(){
		if($mainPanel.find('.gridBody .item').length>1){
			var tot_ppto = 0;
			var tot_ingr = 0;
			var tot_acum = 0;
			var tot_sald = 0;
			for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
				if($mainPanel.find('.gridBody .item').eq(i).data('data').clasificadores.hijos){
					var _id = $mainPanel.find('.gridBody .item').eq(i).data('data')._id.$id;
					var total = 0;
					var total_acum = 0;
					var total_saldo = 0;
					for(j=0;j<$mainPanel.find('.gridBody [padre='+_id+']').length;j++){
						var val_for_grid = $mainPanel.find('.gridBody [padre='+_id+']').eq(j).find('li').eq(2).html();
						if(val_for_grid=="")val_for_grid = "0.00";
						var val_for_grid_acum = $mainPanel.find('.gridBody [padre='+_id+']').eq(j).find('li').eq(4).html();
						if(val_for_grid_acum=="")val_for_grid_acum = "0.00";
						var val_for_grid_saldo = $mainPanel.find('.gridBody [padre='+_id+']').eq(j).find('li').eq(5).html();
						if(val_for_grid_saldo=="")val_for_grid_saldo = "0.00";
						total = parseFloat(val_for_grid) + total;
						total_acum = parseFloat(val_for_grid_acum) + total_acum;
						total_saldo = parseFloat(val_for_grid_saldo) + total_saldo;
					}
				}else{
					var tot_ppto_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(2).html();
					if(tot_ppto_grid=="")tot_ppto_grid = "0.00";
					var tot_ingr_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(3).html();
					if(tot_ingr_grid=="")tot_ingr_grid = "0.00";
					var tot_acum_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(4).html();
					if(tot_acum_grid=="")tot_acum_grid = "0.00";
					var tot_sald_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(5).html();
					if(tot_sald_grid=="")tot_sald_grid = "0.00";
					tot_ppto = parseFloat(tot_ppto_grid) + tot_ppto ;
					tot_ingr = parseFloat(tot_ingr_grid) + tot_ingr ;
					tot_acum = parseFloat(tot_acum_grid) + tot_acum ;
					tot_sald = parseFloat(tot_sald_grid) + tot_sald ;
				}
				$mainPanel.find('.gridBody .item').eq(i).find('li').eq(2).html(K.round(total,2));
				$mainPanel.find('.gridBody .item').eq(i).find('li').eq(4).html(K.round(total_acum,2));
				$mainPanel.find('.gridBody .item').eq(i).find('li').eq(5).html(K.round(total_saldo,2));
			}
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(2).html(K.round(tot_ppto,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(3).html(K.round(tot_ingr,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(4).html(K.round(tot_acum,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(5).html(K.round(tot_sald,2));
		}
	},
	sumAll:function(){
		if($mainPanel.find('.gridBody .item').length>1){
			var tot_ppto = 0;
			var tot_ingr = 0;
			var tot_acum = 0;
			var tot_sald = 0;
			for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
				if(!$mainPanel.find('.gridBody .item').eq(i).data('data').clasificadores.hijos){			
					var tot_ppto_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(2).html();
					if(tot_ppto_grid=="")tot_ppto_grid = "0.00";
					var tot_ingr_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(3).html();
					if(tot_ingr_grid=="")tot_ingr_grid = "0.00";
					var tot_acum_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(4).html();
					if(tot_acum_grid=="")tot_acum_grid = "0.00";
					var tot_sald_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(5).html();
					if(tot_sald_grid=="")tot_sald_grid = "0.00";
					tot_ppto = parseFloat(tot_ppto_grid) + tot_ppto ;
					tot_ingr = parseFloat(tot_ingr_grid) + tot_ingr ;
					tot_acum = parseFloat(tot_acum_grid) + tot_acum ;
					tot_sald = parseFloat(tot_sald_grid) + tot_sald ;
				}
			}
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(2).html(K.round(tot_ppto,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(3).html(K.round(tot_ingr,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(4).html(K.round(tot_acum,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(5).html(K.round(tot_sald,2));
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
				$p.find('[name=ctEpresPpres]').click(function(){ ctEpresPpres.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctEpresMovi]').click(function(){ ctEpresMovi.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctEpresPpres',
			titleBar: {
				title: 'Proceso presupuestario de Ingresos'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/proc',
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
					ctEpresPpres.loadData({page:1});
			    });			
				$mainPanel.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()).keyup(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresPpres.loadData({page:1});
				}); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					ctEpresPpres.loadData({page:1});
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresPpres.loadData({page:1});
			    });
				$mainPanel.find('[name=estado]').html('No Aperturado');
				$mainPanel.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$mainPanel.find('[name=depen]').val(data.nomb).data('data',data);
						$mainPanel.find('[name=actividad]').html(data.actividad.nomb);
						$mainPanel.find('[name=componente]').html(data.componente.nomb);
						$mainPanel.find('.gridBody').empty();
						ctEpresPpres.loadData({page:1});
					}});
				}).button({icons: {primary:'ui-icon-search'},text:false});
				$mainPanel.find('[name=btnCerrarperiodo]').click(function(){
					$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", false );
					if($mainPanel.find('.gridBody .item').length>1){
						var data = new Object;
						data.estado = "C";
						data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
						data.periodo = {
								mes : $mainPanel.find('[name=mes] :selected').val(),
								ano : $mainPanel.find('[name=ano]').val()
						};
						data.organizacion = new Object;
						data.organizacion._id = $mainPanel.find('[name=depen]').data('data')._id.$id;
						data.organizacion.nomb = $mainPanel.find('[name=depen]').data('data').nomb;
						data.organizacion.actividad = $mainPanel.find('[name=depen]').data('data').actividad;
						data.organizacion.componente = $mainPanel.find('[name=depen]').data('data').componente;
						data.fuente = new Object;
						data.fuente._id = $mainPanel.find('[name=fuen] :selected').data('data')._id.$id;
						data.fuente.cod = $mainPanel.find('[name=fuen] :selected').data('data').cod;
						data.fuente.rubro = $mainPanel.find('[name=fuen] :selected').data('data').rubro;
						data.filas = new Array;
						for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
							var row_data = $mainPanel.find('.gridBody .item').eq(i).data('data');
							var row_html = $mainPanel.find('.gridBody .item').eq(i);
							var item = {
									clasificador:{
										_id:row_data._id.$id,
										cod:row_data.cod,
										tipo:row_data.tipo.$id,
										nomb:row_data.nomb
									},
									ingreso:row_html.find('li').eq(3).html(),
									acumulado:row_html.find('li').eq(4).html(),
									saldo:row_html.find('li').eq(5).html()
							};
							item.pim = new Array();
							for(j=0;j<row_data.ppto.length;j++){
								item.pim.push({
									_id:row_data.ppto[j]._id,
									importe:row_data.ppto[j].importe
								});
							}
							data.filas.push(item);
						}
						K.sendingInfo();
						$.post('ct/proc/save',data,function(){
							K.clearNoti();
							$('#pageWrapperLeft .ui-state-highlight').click();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El proceso presupuestario de ingresos fue cerrado con &eacute;xito!'});
						});
					}
				}).button({icons: {primary: 'ui-icon-check'}});
				$mainPanel.find('[name=btnSumar]').click(function(){
					if($mainPanel.find('.item').length>0){
						for(i=0;i<$mainPanel.find('.item');i++){
							ctEpresPpres.sumHier();
						}
						ctEpresPpres.sumAll();
					}	
				}).button({icons: {primary: 'ui-icon-check'}});
				$mainPanel.find('[name=mes]').change();
				$mainPanel.find('[name=btnCerrar]').button( "option", "disabled", true );
				$.post('pr/fuen/all',function(data){
					var $cbo = $mainPanel.find('[name=fuen]');
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
				$mainPanel.find('[name=fuen]').change(function(){
					$mainPanel.find('.gridBody').empty();
					ctEpresPpres.loadData({page:1});
				});
				$mainPanel.find('[name=tipo]').change(function(){
					if($(this).val()=="I"){
						ctEpresPpres.init();
					}else if($(this).val()=="G"){
						ctEpresPpresg.init();
					}					
				});
				$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
			    $mainPanel.find('[name=btnSumar]').button( "option", "disabled", true );
			}
		});
		$('#pageWrapperMain').layout();
		K.unblock({$element: $('#pageWrapperMain')});
	},
	loadData: function(params){
		if($mainPanel.find('[name=depen]').data('data')==null){
			return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Seleccione una organizaci&oacute;n!',type: 'error'});
		}
		$.extend(params,{
	    	mes: $mainPanel.find('[name=mes] :selected').val(),
	    	ano: $mainPanel.find('[name=ano]').val(),
	    	organizacion:$mainPanel.find('[name=depen]').data('data')._id.$id,
	    	fuente: $mainPanel.find('[name=fuen] :selected').val()
		});
	    $.post("ct/proc/lista_saved", params, function(data){
	    	$mainPanel.find('[name=estado]').html("Aperturado");
	    	if ( data.items!=null ) { 
	    		$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
	    		$mainPanel.find('[name=btnSumar]').button( "option", "disabled", true );
	    		$mainPanel.find('[name=estado]').html("Cerrado");
	    		var $row1 = $('.gridReference','#mainPanel').clone();
				$li1 = $('li',$row1);
				$li1.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				$("#mainPanel .gridBody").append( $row1.children() );
				for(i=0; i < data.items.length; i++) {
					 result = data.items[i];
					 var $row = $('.gridReference','#mainPanel').clone();
					 $li = $('li',$row);
					 $li.eq(0).html(result.clasificador.cod);
					 $li.eq(1).html(result.clasificador.nomb);
					 var tot_pim = 0;
					 for(j=0;j<result.pim.length;j++){
						 tot_pim = tot_pim + parseFloat(result.pim[j].importe);
					 }
					 $li.eq(2).html(K.round(tot_pim,2));
					 $li.eq(3).html(K.round(result.ingreso,2));
					 $li.eq(4).html(K.round(result.acumulado,2));
					 $li.eq(5).html(K.round(result.saldo,2));
					 $row.wrapInner('<a id="'+result._id.$id+'" padre="'+result.clasificador.clasificadores.padre+'" name="'+result.cod+'" class="item" href="javascript: void(0);" />');
				     $row.find('a').data('data',result.clasificador);
					 $("#mainPanel .gridBody .total").before( $row.children() );
					 ciHelper.gridButtons($("#mainPanel .gridBody"));
					 K.unblock({$element: $('#pageWrapperMain')});
				 }
				ctEpresPpres.sumAll();
	    	}else {
	    		$.post("ct/proc/lista", params, function(data){
	    			$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", false );
	    			$mainPanel.find('[name=btnSumar]').button( "option", "disabled", false );
		    		var $row1 = $('.gridReference','#mainPanel').clone();
					$li1 = $('li',$row1);
					$li1.eq(1).html("Total").addClass('ui-state-default ui-button-text-only');
					$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
					$("#mainPanel .gridBody").append( $row1.children() );
					if(data.items!=null){
						 for(i=0; i < data.items.length; i++) {
							 result = data.items[i];
							 var $row = $('.gridReference','#mainPanel').clone();
							 $li = $('li',$row);
							 $li.eq(0).html(result.cod);
							 $li.eq(1).html(result.nomb);
							 var tot_ppto = 0;
							 for(j=0;j<result.ppto.length;j++){
								 tot_ppto = tot_ppto + parseFloat(result.ppto[j].importe);
							 }
							 $li.eq(2).html(K.round(tot_ppto,2));
							 $li.eq(3).html(K.round(ciHelper.sum(result.monto),2));
							 $li.eq(4).html(K.round(parseFloat(result.acum_ant)+parseFloat(ciHelper.sum(result.monto)),2));
							 $li.eq(5).html(K.round(parseFloat(tot_ppto)-parseFloat(result.acum_ant)-parseFloat(ciHelper.sum(result.monto)),2));
							 var hijos = false;
							 if(result.clasificadores.hijos){
							 	hijos = true;
							 }
							 $row.wrapInner('<a id="'+result._id.$id+'" padre="'+result.clasificadores.padre+'" name="'+result.cod+'" class="item" href="javascript: void(0);" />');
							 $row.find('a').data('data',result);
						     $("#mainPanel .gridBody .total").before( $row.children() );
							 ciHelper.gridButtons($("#mainPanel .gridBody"));
							 K.unblock({$element: $('#pageWrapperMain')});
						 }
					}else{
						$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
					    $mainPanel.find('[name=btnSumar]').button( "option", "disabled", true );
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontraron auxiliares de ingreso para armar el reporte!',type: 'error'});
					}
	    		},'json');
	    	}
	      $mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
	      $mainPanel.find('[name=btnSumar]').button( "option", "disabled", true );
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};