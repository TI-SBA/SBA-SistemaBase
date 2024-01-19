/*******************************************************************************
Tipos de Nota */
ctEpresAuxG = {
	states : {
		"A":{descr:"Aperturado"},
		"C":{descr:"Cerrado"}
	},
	verifySald : function(){
		K.unblock({$element: $('#pageWrapperMain')});
		K.clearNoti();
		$mainPanel.find('[name=btnAgregar]').button( "option", "disabled", true );
		$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
		if($mainPanel.find('[name=depen]').data('data')==null){
			//$mainPanel.find('[name=btnOrga]').click();
			return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Dependencia!',type: 'error'});
		}
		if($mainPanel.find('[name=clas_esp]').data('data')==null){
			//$mainPanel.find('[name=btnEsp]').click();
			return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Clasificador tipo Sub-Especifico!',type: 'error'});
		}
		var data = new Object;
		data.periodo = {
				mes:$mainPanel.find('[name=mes] :selected').val(),
				ano:$mainPanel.find('[name=ano]').val()
		};
		data.organizacion = $mainPanel.find('[name=depen]').data('data')._id.$id;
		data.especifica = $mainPanel.find('[name=clas_esp]').data('data')._id.$id;
		data.fuente = $mainPanel.find('[name=fuen] :selected').val();
		if($mainPanel.find('[name=meta]').data('data')!=null){
			data.meta = $mainPanel.find('[name=meta]').data('data')._id.$id;
		}
		$.post('ct/saldg/filter_one',data,function(data){
			if(data!=null){
				$mainPanel.find('[name=estado]').html(ctEpresAuxI.states[data.estado].descr).data('data',data);
				$mainPanel.find('[name=depen]').html(data.organizacion.nomb).data('data',data.organizacion);
				$mainPanel.find('[name=clas_esp]').html(data.especifica.cod).data('data',data.especifica);
				$mainPanel.find('[name=descr_esp]').html(data.especifica.nomb);
				if(data.meta){
					$mainPanel.find('[name=meta]').html(data.meta.cod+' '+data.meta.nomb).data('data',data.meta);
				}
				if(data.estado=="A"){
					$mainPanel.find('[name=btnAgregar]').button( "option", "disabled", false );
					$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", false );
				}else if(data.estado=="C"){
					$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
				}
				$mainPanel.find('.gridBody').empty();
				ctEpresAuxG.loadData({
					e_p_debe_ini:data.ejec_pres.debe_ini,
					e_p_haber_ini:data.ejec_pres.haber_ini,
					a_debe_ini:data.asignaciones.debe_ini,
					a_haber_ini:data.asignaciones.haber_ini,
					e_g_debe_ini:data.ejec_gasto.debe_ini,
					e_g_haber_ini:data.ejec_gasto.haber_ini,
					estado:data.estado,
					url: 'ct/auxg/lista'
				});
			}else{
				$mainPanel.find('.gridBody').empty();
				$mainPanel.find('[name=btnAgregar]').button( "option", "disabled", false );
				$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
				$mainPanel.find('[name=estado]').html('No Aperturado').data('data',null);
			}
		},'json');		
	},
	sumAll :function(){
		if($mainPanel.find('[name=estado]').data('data')!=null){
			var e_p_total_d = 0;
			var e_p_total_h = 0;
			var a_total_d = 0;
			var a_total_h = 0;
			var e_g_total_d = 0;
			var e_g_total_h = 0;
			for(i=0;i<($mainPanel.find('.gridBody .item').length-1);i++){
				/** Ejecucion presupuestaria:Debe*/
				var e_p_total_d_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(6).html();
				if(e_p_total_d_grid=="") e_p_total_d_grid="0";
				var e_p_total_h_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(7).html();
				if(e_p_total_h_grid=="") e_p_total_h_grid="0";
				var a_total_d_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(9).html();
				if(a_total_d_grid=="") a_total_d_grid="0";
				var a_total_h_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(10).html();
				if(a_total_h_grid=="") a_total_h_grid="0";
				var e_g_total_d_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(12).html();
				if(e_g_total_d_grid=="") e_g_total_d_grid="0";
				var e_g_total_h_grid = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(13).html();
				if(e_g_total_h_grid=="") e_g_total_h_grid="0";	
				e_p_total_d = parseFloat(e_p_total_d_grid) + e_p_total_d;
				e_p_total_h = parseFloat(e_p_total_h_grid) + e_p_total_h;
				a_total_d = parseFloat(a_total_d_grid) + a_total_d;
				a_total_h = parseFloat(a_total_h_grid) + a_total_h;
				e_g_total_d = parseFloat(e_g_total_d_grid) + e_g_total_d;
				e_g_total_h = parseFloat(e_g_total_h_grid) + e_g_total_h;
			}		
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(6).html(K.round(e_p_total_d,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(7).html(K.round(e_p_total_h,2));	
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(9).html(K.round(a_total_d,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(10).html(K.round(a_total_h,2));		
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(12).html(K.round(e_g_total_d,2));
			$mainPanel.find('.gridBody .total').eq(0).find('li').eq(13).html(K.round(e_g_total_h,2));	
			var tipo_saldo = $mainPanel.find('.gridBody .item').eq(1).data('data').tipo_saldo;
			if(tipo_saldo=="O1"){
				$mainPanel.find('.gridBody .total').eq(0).find('li').eq(8).html(K.round(e_p_total_d-e_p_total_h,2));
				$mainPanel.find('.gridBody .total').eq(0).find('li').eq(11).html(K.round(a_total_d-a_total_h,2));
				$mainPanel.find('.gridBody .total').eq(0).find('li').eq(14).html(K.round(e_g_total_d-e_g_total_h,2));
			}else if(tipo_saldo=="O2"){
				$mainPanel.find('.gridBody .total').eq(0).find('li').eq(8).html(K.round(-e_p_total_d+e_p_total_h,2));
				$mainPanel.find('.gridBody .total').eq(0).find('li').eq(11).html(K.round(-a_total_d+a_total_h,2));
				$mainPanel.find('.gridBody .total').eq(0).find('li').eq(14).html(K.round(-e_g_total_d+e_g_total_h,2));
			}
			
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
				$p.find('[name=ctEpresAuxG]').click(function(){ ctEpresAuxG.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctEpresCuadce]').click(function(){ ctEpresCuadce.init(); });
				$p.find('[name=ctEpresPpres]').click(function(){ ctEpresPpres.init(); });
				$p.find('[name=ctEpresMovi]').click(function(){ ctEpresMovi.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctEpresAuxG',
			titleBar: {
				title: 'Auxiliares Standard de Gastos'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/auxg',
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
			    });			
				$mainPanel.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()).keyup(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresAuxG.verifySald();
				}); 
				$mainPanel.find('[name=mes]').hide(); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					ctEpresAuxG.verifySald();
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresAuxG.verifySald();
			    });
				$mainPanel.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$mainPanel.find('[name=depen]').html(data.nomb).data('data',data);
						ctEpresAuxG.verifySald();
					}});
				}).button({icons: {primary:'ui-icon-search'},text:false});
				$mainPanel.find('[name=btnEsp]').click(function(){					
					prClas.windowSelect({callback: function(data){
						$mainPanel.find('[name=clas_esp]').html(data.cod).data('data',data);
						$mainPanel.find('[name=descr_esp]').html(data.nomb);
						ctEpresAuxG.verifySald();
					}});
				}).button({icons: {primary:'ui-icon-search'},text:false});
				$mainPanel.find('[name=btnMeta]').click(function(){
					prMeta.windowSelect({callback:function(data){
						$mainPanel.find('[name=meta]').html(data.cod+' '+data.nomb).data('data',data);
						ctEpresAuxG.verifySald();
					}});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
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
					$('#mainPanel .gridBody').empty();
					ctEpresAuxG.verifySald();
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctEpresAuxG.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});	
				$mainPanel.find('[name=btnCerrarperiodo]').click(function(){
					var data = new Object;
					data._id = $mainPanel.find('[name=estado]').data('data')._id.$id;
					data.estado = "C";
					data.ejec_pres = new Object;
					data.ejec_pres.debe_ini = $mainPanel.find('[name=estado]').data('data').ejec_pres.debe_ini;
					data.ejec_pres.haber_ini = $mainPanel.find('[name=estado]').data('data').ejec_pres.haber_ini;
					data.ejec_pres.debe_fin = $mainPanel.find('.gridBody .total').find('li').eq(6).html();
					data.ejec_pres.haber_fin = $mainPanel.find('.gridBody .total').find('li').eq(7).html();
					data.asignaciones = new Object;
					data.asignaciones.debe_ini = $mainPanel.find('[name=estado]').data('data').asignaciones.debe_ini;
					data.asignaciones.haber_ini = $mainPanel.find('[name=estado]').data('data').asignaciones.haber_ini;
					data.asignaciones.debe_fin = $mainPanel.find('.gridBody .total').find('li').eq(9).html();
					data.asignaciones.haber_fin = $mainPanel.find('.gridBody .total').find('li').eq(10).html();
					data.ejec_gasto = new Object;
					data.ejec_gasto.debe_ini = $mainPanel.find('[name=estado]').data('data').ejec_gasto.debe_ini;
					data.ejec_gasto.haber_ini = $mainPanel.find('[name=estado]').data('data').ejec_gasto.haber_ini;
					data.ejec_gasto.debe_fin = $mainPanel.find('.gridBody .total').find('li').eq(12).html();
					data.ejec_gasto.haber_fin = $mainPanel.find('.gridBody .total').find('li').eq(13).html();
					$.post('ct/saldg/cerrar',data,function(){
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El periodo fue cerrado con &eacute;xito!'});
						ctEpresAuxG.verifySald();
					});					
				}).button({icons: {primary: 'ui-icon-check'}});
				$mainPanel.find('[name=mes]').change();
				//ctEpresAuxI.loadData({page: 1,url: 'ct/auxi/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.saldo = $mainPanel.find('[name=estado]').data('data')._id.$id;
	    $.post(params.url, params, function(data){
			if ( data.items!=null ) { 
				var $row1 = $('.gridReference','#mainPanel').clone();
				$li1 = $('li',$row1);
				$li1.eq(5).html("Total").addClass('ui-state-default ui-button-text-only');
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				$("#mainPanel .gridBody").append( $row1.children() );		
				var $row1 = $('.gridReference','#mainPanel').clone();
				$li1 = $('li',$row1);
				$li1.eq(6).html(K.round(params.e_p_debe_ini,2));
				$li1.eq(7).html(K.round(params.e_p_haber_ini,2));	
				$li1.eq(9).html(K.round(params.a_debe_ini,2));
				$li1.eq(10).html(K.round(params.a_haber_ini,2));		
				$li1.eq(12).html(K.round(params.e_g_debe_ini,2));
				$li1.eq(13).html(K.round(params.e_g_haber_ini,2));	
				if(data.items[0].tipo_saldo=="O1"){
					$li1.eq(8).html(K.round(parseFloat(params.e_p_debe_ini)-parseFloat(params.e_p_haber_ini),2));
					$li1.eq(11).html(K.round(parseFloat(params.a_debe_ini)-parseFloat(params.a_haber_ini),2));
					$li1.eq(14).html(K.round(parseFloat(params.e_g_debe_ini)-parseFloat(params.e_g_haber_ini),2));
				}else if(data.items[0].tipo_saldo=="O2"){
					$li1.eq(8).html(K.round(-parseFloat(params.e_p_debe_ini)+parseFloat(params.e_p_haber_ini),2));
					$li1.eq(11).html(K.round(-parseFloat(params.a_debe_ini)+parseFloat(params.a_haber_ini),2));
					$li1.eq(14).html(K.round(-parseFloat(params.e_g_debe_ini)+parseFloat(params.e_g_haber_ini),2));
				}
				$row1.wrapInner('<a class="item" href="javascript: void(0);" />');
				$("#mainPanel .gridBody .total").before( $row1.children() );
				for (i=0; i < data.items.length; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					var fecha1 = result.fecini;
					fecha1 = fecha1.split("-");
					var fecha2 = result.fecfin;
					var slash = "";
					if(result.fecfin!=""){
						fecha2 = fecha2.split("-");
						slash = "/";
						var fechafin = fecha2[2];
					}else{
						var fechafin = "";
					}
					$li.eq(1).html(ciHelper.meses[parseFloat(fecha1[1])-1]);
					
					$li.eq(2).html( fecha1[2]+slash+fechafin);
					$li.eq(3).html( result.clase );
					$li.eq(4).html( result.numero );
					var subdetalles = '';
					if(result.subdetalles){
						for(j=0;j<result.subdetalles.length;j++){
							var open_parent = "";
							var close_parent = "";
							if(result.subdetalles[j].tipo=="N"){
								open_parent = "(";
								close_parent = ")";
							}
							subdetalles += '<i>'+result.subdetalles[j].descr+' - '+open_parent+result.subdetalles[j].monto+close_parent+'</i><br />';
						}
					}
					$li.eq(5).html( result.detalle+'<br/>'+subdetalles);
					if(result.ejec_pres.monto=="") var result_ejec_pres_monto="0";
					else var result_ejec_pres_monto=result.ejec_pres.monto;
					if(result.ejec_pres.tipo=="D"){
						$li.eq(6).html( K.round(result_ejec_pres_monto,2) );						
						if(result.tipo_saldo=="O1"){
							$li.eq(8).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(8).html())+parseFloat(result_ejec_pres_monto),2));
						}else if(result.tipo_saldo=="O2"){
							$li.eq(8).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(8).html())-parseFloat(result_ejec_pres_monto),2));
						}
					}
					else if(result.ejec_pres.tipo=="H"){
						$li.eq(7).html( K.round(result_ejec_pres_monto,2) );						
						if(result.tipo_saldo=="O1"){
							$li.eq(8).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(8).html())-parseFloat(result_ejec_pres_monto),2));
						}else if(result.tipo_saldo=="O2"){
							$li.eq(8).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(8).html())+parseFloat(result_ejec_pres_monto),2));
						}
					}	
					
					if(result.asignaciones.monto=="") var result_asignaciones_monto="0";
					else var result_asignaciones_monto=result.asignaciones.monto;
					if(result.asignaciones.tipo=="D"){
						$li.eq(9).html( K.round(result_asignaciones_monto,2) );					
						if(result.tipo_saldo=="O1"){
							$li.eq(11).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(11).html())+parseFloat(result_asignaciones_monto),2));
						}else if(result.tipo_saldo=="O2"){
							$li.eq(11).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(11).html())-parseFloat(result_asignaciones_monto),2));
						}
					}
					else if(result.asignaciones.tipo=="H"){
						$li.eq(10).html( K.round(result_asignaciones_monto,2) );					
						if(result.tipo_saldo=="O1"){
							$li.eq(11).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(11).html())-parseFloat(result_asignaciones_monto),2));
						}else if(result.tipo_saldo=="O2"){
							$li.eq(11).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(11).html())+parseFloat(result_asignaciones_monto),2));
						}
					}
					
					if(result.ejec_gasto.monto=="") var result_ejec_gasto_monto="0";
					else var result_ejec_gasto_monto=result.ejec_gasto.monto;
					if(result.ejec_gasto.tipo=="D"){
						$li.eq(12).html( K.round(result_ejec_gasto_monto,2) );					
						if(result.tipo_saldo=="O1"){
							$li.eq(14).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(14).html())+parseFloat(result_ejec_gasto_monto),2));
						}else if(result.tipo_saldo=="O2"){
							$li.eq(14).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(14).html())-parseFloat(result_ejec_gasto_monto),2));
						}
					}
					else if(result.ejec_gasto.tipo=="H"){
						$li.eq(13).html( K.round(result_ejec_gasto_monto,2) );					
						if(result.tipo_saldo=="O1"){
							$li.eq(14).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(14).html())-parseFloat(result_ejec_gasto_monto),2));
						}else if(result.tipo_saldo=="O2"){
							$li.eq(14).html(K.round(parseFloat($mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-2).find('li').eq(14).html())+parseFloat(result_ejec_gasto_monto),2));
						}
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenList", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(params.estado=="C") excep+='#conMenList_edi';
								$(excep+',#conMenList_about,#conMenList_imp,#conMenList_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									ctEpresAuxG.windowEdit({id: K.tmp.data('id')});
								}
							}
						});
		        	$("#mainPanel .gridBody .total").before( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
				ctEpresAuxG.sumAll();
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results').hide();
	      } else {
	        $('#No-Results').hide();
	        $('#Results').hide();
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		p.calgrid = function(){
			if(p.$w.find('.gridBody .item').length>1){
				var total = 0;
				for(i=0;i<(p.$w.find('.gridBody .item').length-1);i++){
					if(p.$w.find('.gridBody .item').eq(i).find('[name=monto]').val()!=""){
						var signo = "+";
						if(p.$w.find('.gridBody .item').eq(i).find('[type=radio]:checked').val()=="N") signo = "-";
						total = parseFloat(signo+p.$w.find('.gridBody .item').eq(i).find('[name=monto]').val()) + total;
					}
				}
				p.$w.find('.gridBody .total').eq(0).find('li').eq(1).html(total);
			}
		};
		new K.Window({
			id: 'windowNewctEpresAuxg',
			title: 'Nuevo Registro del Auxiliar de Gastos',
			contentURL: 'ct/auxg/edit',
			icon: 'ui-icon-plusthick',
			width: 580,
			height: 450,
			modal: true,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;					
					data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.estado = "A";
					if($mainPanel.find('[name=estado]').data('data')==null){
						data.saldo = new Object;					
						data.saldo.periodo = new Object;
						data.saldo.periodo.ano = $mainPanel.find('[name=ano]').val();
						data.saldo.periodo.mes = p.$w.find('[name=mes] :selected').val();
						data.saldo.organizacion = new Object;
						data.saldo.organizacion._id = $mainPanel.find('[name=depen]').data('data')._id.$id;
						data.saldo.organizacion.nomb = $mainPanel.find('[name=depen]').data('data').nomb;
						data.saldo.organizacion.actividad = $mainPanel.find('[name=depen]').data('data').actividad;
						data.saldo.organizacion.componente = $mainPanel.find('[name=depen]').data('data').componente;
						data.saldo.fuente = new Object;
						data.saldo.fuente._id = $mainPanel.find('[name=fuen] :selected').data('data')._id.$id;
						data.saldo.fuente.cod = $mainPanel.find('[name=fuen] :selected').data('data').cod;
						data.saldo.fuente.rubro = $mainPanel.find('[name=fuen] :selected').data('data').rubro;						
						data.saldo.especifica = new Object;
						data.saldo.especifica._id = $mainPanel.find('[name=clas_esp]').data('data')._id.$id;
						data.saldo.especifica.cod = $mainPanel.find('[name=clas_esp]').data('data').cod;
						data.saldo.especifica.tipo = $mainPanel.find('[name=clas_esp]').data('data').tipo;
						data.saldo.especifica.nomb = $mainPanel.find('[name=clas_esp]').data('data').nomb;
						if($mainPanel.find('[name=meta]').data('data')!=null){
							data.saldo.meta = new Object;
							data.saldo.meta._id = $mainPanel.find('[name=meta]').data('data')._id.$id;
							data.saldo.meta.cod = $mainPanel.find('[name=meta]').data('data').cod;
							data.saldo.meta.nomb = $mainPanel.find('[name=meta]').data('data').nomb;
						}
					}else{
						data.saldo = new Object;
						data.saldo._id = $mainPanel.find('[name=estado]').data('data')._id.$id;
					}
					data.fecini = p.$w.find('[name=fec1]').val();
					if(data.fecini==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar la primera Fecha!',type: 'error'});
					}
					data.fecfin = p.$w.find('[name=fec2]').val();
					data.clase = p.$w.find('[name=clase] :selected').val();
					data.numero = p.$w.find('[name=numero]').val();
					if(data.numero==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo n&uacute;mero!',type: 'error'});
					}
					data.detalle = p.$w.find('[name=detalle]').val();
					if(data.detalle==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo detalle!',type: 'error'});
					}
					data.subdetalles = new Array;
					if(p.$w.find('.gridBody .item').length>1){
						for(i=0;i<(p.$w.find('.gridBody .item').length-1);i++){
							var subdetalle = {
									descr:p.$w.find('.gridBody .item').eq(i).find('[name=descr]').val(),
									tipo:p.$w.find('.gridBody .item').eq(i).find('[type=radio]:checked').val(),
									monto:p.$w.find('.gridBody .item').eq(i).find('[name=monto]').val()
							};
							if(subdetalle.descr!="" && subdetalle.monto!=""){
								data.subdetalles.push(subdetalle);
							}
						}
					}
					data.ejec_pres = new Object;
					data.ejec_pres.tipo = p.$w.find('[name=rbtnRadEjePres]:checked').val();
					data.ejec_pres.monto = p.$w.find('[name=monto_ejec_pres]').val();
					data.asignaciones = new Object;
					data.asignaciones.tipo = p.$w.find('[name=rbtnRadAsign]:checked').val();
					data.asignaciones.monto = p.$w.find('[name=monto_asig]').val();
					data.ejec_gasto = new Object;
					data.ejec_gasto.tipo = p.$w.find('[name=rbtnRadEjeGast]:checked').val();
					data.ejec_gasto.monto = p.$w.find('[name=monto_ejec_gast]').val();
					data.tipo_saldo = p.$w.find('[name=saldos]:checked').val();
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/auxg/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El auxiliar de gastos fue registrado con &eacute;xito!'});
						ctEpresAuxG.verifySald();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose : function(){
				p.$w.find('[name=btnAdd]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p.$w.find('[name=monto]').die('keyup');
				p.$w.find("#TipoPos").die('click');
				p.$w.find("#TipoNeg").die('click');
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewctEpresAuxg');
				K.block({$element: p.$w});
				p.$w.find('[name=periodo]').html($mainPanel.find('[name=ano]').val());
				p.$w.find('[name=clasif]').html($mainPanel.find('[name=clas_esp]').data('data').cod+' '+$mainPanel.find('[name=clas_esp]').data('data').nomb);
				p.$w.find('[name=orga]').html($mainPanel.find('[name=depen]').data('data').cod+''+$mainPanel.find('[name=depen]').data('data').nomb);
				p.$w.find('[name=fuen]').html($mainPanel.find('[name=fuen] :selected').data('data').cod+" - "+$mainPanel.find('[name=fuen] :selected').data('data').rubro);
				if($mainPanel.find('[name=meta]').data('data')!=null){
					p.$w.find('#meta').show();
					p.$w.find('[name=meta]').html($mainPanel.find('[name=meta]').data('data').cod+' '+$mainPanel.find('[name=meta]').data('data').nomb);
				}
				p.$w.find('[name=RadEjePres]').buttonset();
				p.$w.find('[name=RadEjeGast]').buttonset();
				p.$w.find('[name=RadAsign]').buttonset();
				p.$w.find('[name=fec1]').datepicker();
				p.$w.find('[name=fec2]').datepicker();
				/** Grilla: Sub-Detalles */
				var $row1 = p.$w.find('.gridReference').clone();
				$li1 = $('li',$row1);	
				$li1.eq(0).html( "Total" ).addClass('ui-state-default ui-button-text-only');
				$li1.eq(1).html("");
				$li1.eq(2).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(3).html("").addClass('ui-state-default ui-button-text-only');
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody").append( $row1.children() );		
				var $row = p.$w.find('.gridReference').clone();
				$li = $('li',$row);	
				$row.find('[type=radio]').attr('name','tipo_0');
				$row.wrapInner('<a class="item" href="javascript: void(0);" />');
				$row.find('[name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				p.$w.find(".gridBody .total").before( $row.children() );	
				var fila = 0;
				p.$w.find('[name=btnAdd]').live('click',function(){
					fila +=1; 
					var $row = p.$w.find('.gridReference').clone();
					$li = $('li',$row);
					$row.find('[type=radio]').attr('name','tipo_'+fila);
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					p.$w.find(".gridBody .total").before( $row.children() );
					p.$w.find('.gridBody [name=btnAdd]').remove();
					p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
					p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					p.calgrid();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli]').live('click',function(){
					if(p.$w.find('.gridBody .item').length>2){
						$(this).closest('.item').remove();			
						p.$w.find('.gridBody [name=btnAdd]').remove();
						p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
						p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						p.calgrid();
					}
				});
				p.$w.find('[name=monto]').live('keyup',function(){
					p.calgrid();
				});
				p.$w.find("#TipoPos").live('click',function() {
					p.calgrid();
				});
				p.$w.find("#TipoNeg").live('click',function() {
					p.calgrid();
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = new Object;
		p.calgrid = function(){
			if(p.$w.find('.gridBody .item').length>1){
				var total = 0;
				for(i=0;i<(p.$w.find('.gridBody .item').length-1);i++){
					if(p.$w.find('.gridBody .item').eq(i).find('[name=monto]').val()!=""){
						var signo = "+";
						if(p.$w.find('.gridBody .item').eq(i).find('[type=radio]:checked').val()=="N") signo = "-";
						total = parseFloat(signo+p.$w.find('.gridBody .item').eq(i).find('[name=monto]').val()) + total;
					}
				}
				p.$w.find('.gridBody .total').eq(0).find('li').eq(1).html(total);
			}
		};
		new K.Window({
			id: 'windowNewctEpresAuxg',
			title: 'Editar Registro del Auxiliar de Gastos',
			contentURL: 'ct/auxg/edit',
			icon: 'ui-icon-plusthick',
			width: 580,
			height: 450,
			modal: true,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.fecini = p.$w.find('[name=fec1]').val();
					if(data.fecini==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar la primera Fecha!',type: 'error'});
					}
					data.fecfin = p.$w.find('[name=fec2]').val();
					data.clase = p.$w.find('[name=clase] :selected').val();
					data.numero = p.$w.find('[name=numero]').val();
					if(data.numero==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo n&uacute;mero!',type: 'error'});
					}
					data.detalle = p.$w.find('[name=detalle]').val();
					if(data.detalle==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo detalle!',type: 'error'});
					}
					data.subdetalles = new Array;
					if(p.$w.find('.gridBody .item').length>1){
						for(i=0;i<(p.$w.find('.gridBody .item').length-1);i++){
							var subdetalle = {
									descr:p.$w.find('.gridBody .item').eq(i).find('[name=descr]').val(),
									tipo:p.$w.find('.gridBody .item').eq(i).find('[type=radio]:checked').val(),
									monto:p.$w.find('.gridBody .item').eq(i).find('[name=monto]').val()
							};
							if(subdetalle.descr!="" && subdetalle.monto!=""){
								data.subdetalles.push(subdetalle);
							}
						}
					}
					data.ejec_pres = new Object;
					data.ejec_pres.tipo = p.$w.find('[name=rbtnRadEjePres]:checked').val();
					data.ejec_pres.monto = p.$w.find('[name=monto_ejec_pres]').val();
					data.asignaciones = new Object;
					data.asignaciones.tipo = p.$w.find('[name=rbtnRadAsign]:checked').val();
					data.asignaciones.monto = p.$w.find('[name=monto_asig]').val();
					data.ejec_gasto = new Object;
					data.ejec_gasto.tipo = p.$w.find('[name=rbtnRadEjeGast]:checked').val();
					data.ejec_gasto.monto = p.$w.find('[name=monto_ejec_gast]').val();
					data.tipo_saldo = p.$w.find('[name=saldos]:checked').val();
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/auxg/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El auxiliar de gastos fue modificado con &eacute;xito!'});
						var data = $mainPanel.find('[name=estado]').data('data');
						$mainPanel.find('.gridBody').empty();
						ctEpresAuxG.loadData({
							e_p_debe_ini:data.ejec_pres.debe_ini,
							e_p_haber_ini:data.ejec_pres.haber_ini,
							a_debe_ini:data.asignaciones.debe_ini,
							a_haber_ini:data.asignaciones.haber_ini,
							e_g_debe_ini:data.ejec_gasto.debe_ini,
							e_g_haber_ini:data.ejec_gasto.haber_ini,
							estado:data.estado,
							url: 'ct/auxg/lista'
						});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose : function(){
				p.$w.find('[name=btnAdd]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p.$w.find('[name=monto]').die('keyup');
				p.$w.find("#TipoPos").die('click');
				p.$w.find("#TipoNeg").die('click');
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewctEpresAuxg');
				K.block({$element: p.$w});
				p.$w.find('[name=RadEjePres]').buttonset();
				p.$w.find('[name=RadEjeGast]').buttonset();
				p.$w.find('[name=RadAsign]').buttonset();
				p.$w.find('[name=fec1]').datepicker();
				p.$w.find('[name=fec2]').datepicker();
				/** Grilla: Sub-Detalles */
				var $row1 = p.$w.find('.gridReference').clone();
				$li1 = $('li',$row1);	
				$li1.eq(0).html( "Total" ).addClass('ui-state-default ui-button-text-only');
				$li1.eq(1).html("");
				$li1.eq(2).html("").addClass('ui-state-default ui-button-text-only');
				$li1.eq(3).html("").addClass('ui-state-default ui-button-text-only');
				$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
				p.$w.find(".gridBody").append( $row1.children() );		
				var $row = p.$w.find('.gridReference').clone();
				$li = $('li',$row);	
				$row.find('[type=radio]').attr('name','tipo_0');
				$row.wrapInner('<a class="item" href="javascript: void(0);" />');
				$row.find('[name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				p.$w.find(".gridBody .total").before( $row.children() );	
				var fila = 0;
				p.$w.find('[name=btnAdd]').live('click',function(){
					fila +=1; 
					var $row = p.$w.find('.gridReference').clone();
					$li = $('li',$row);
					$row.find('[type=radio]').attr('name','tipo_'+fila);
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
					p.$w.find(".gridBody .total").before( $row.children() );
					p.$w.find('.gridBody [name=btnAdd]').remove();
					p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
					p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					p.calgrid();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=btnEli]').live('click',function(){
					if(p.$w.find('.gridBody .item').length>2){
						$(this).closest('.item').remove();			
						p.$w.find('.gridBody [name=btnAdd]').remove();
						p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
						p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						p.calgrid();
					}
				});
				p.$w.find('[name=monto]').live('keyup',function(){
					p.calgrid();
				});
				p.$w.find("#TipoPos").live('click',function() {
					p.calgrid();
				});
				p.$w.find("#TipoNeg").live('click',function() {
					p.calgrid();
				});
				$.post('ct/auxg/get','id='+p.id,function(data){
					p.$w.find('[name=mes]').hide();
					p.$w.find('[name=periodo]').html(ciHelper.meses[parseFloat(data.saldo.periodo.mes)-1]+" - "+data.saldo.periodo.ano);
					p.$w.find('[name=clasif]').html(data.saldo.especifica.cod);
					p.$w.find('[name=orga]').html(data.saldo.organizacion.nomb);
					p.$w.find('[name=fuen]').selectVal(data.saldo.fuente._id.$id);
					if(data.saldo.meta){
						p.$w.find('#meta').show();
						p.$w.find('[name=meta]').html(data.saldo.meta.cod+' '+data.saldo.meta.nomb);
					}
					p.$w.find('[name=fec1]').val(data.fecini);
					p.$w.find('[name=fec2]').val(data.fecfin);
					p.$w.find('[name=clase]').selectVal(data.clase);
					p.$w.find('[name=numero]').val(data.numero);
					p.$w.find('[name=detalle]').val(data.detalle);
					if(data.subdetalles){
						var fila = 20;
						p.$w.find('.gridBody .item').eq(0).remove();
						for(i=0;i<data.subdetalles.length;i++){
							fila +=1; 
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);
							$row.find('[name=descr]').val(data.subdetalles[i].descr);
							$row.find('[name=monto]').val(data.subdetalles[i].monto);
							if(data.subdetalles[i].tipo=="N")$row.find('[type=radio]').eq(1).attr('checked',true);
							$row.find('[type=radio]').attr('name','tipo_'+fila);
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
							$row.wrapInner('<a class="item" />');
							p.$w.find(".gridBody .total").before( $row.children() );
							p.$w.find('.gridBody [name=btnAdd]').remove();
							p.$w.find('.gridBody .item').eq(p.$w.find('.gridBody .item').length-2).find('li:last').append('<button name="btnAdd">Agregar</button>');
							p.$w.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						}
						p.calgrid();
					}
					if(data.ejec_pres.tipo=="H")p.$w.find('#RadEjePresHaber').click();
					if(data.ejec_gasto.tipo=="H")p.$w.find('#RadEjeGastHaber').click();
					if(data.asignaciones.tipo=="H")p.$w.find('#RadAsignHaber').click();
					p.$w.find('[name=monto_ejec_pres]').val(data.ejec_pres.monto);
					p.$w.find('[name=monto_ejec_gast]').val(data.ejec_gasto.monto);
					p.$w.find('[name=monto_asig]').val(data.asignaciones.monto);
					if(data.tipo_saldo=="O2")p.$w.find('[name=saldos]').eq(1).click();
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	}	
};