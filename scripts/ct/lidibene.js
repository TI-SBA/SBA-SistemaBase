/*******************************************************************************
auxiliares pasivo */
ctLidiBene = {
	init: function(){
		if($('#pageWrapper [child=lidi]').length<=0){
			$.post('ct/navg/lidi',function(data){
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
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="lidi" />');
					$p.find("[name=ctLidi]").after( $row.children() );
				}
				$p.find('[name=ctLidi]').data('lidi',$('#pageWrapper [child=lidi]:first').data('lidi'));
				$p.find('[name=ctLidiSuna]').click(function(){ ctLidiSuna.init(); });
				$p.find('[name=ctLidiBene]').click(function(){ ctLidiBene.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctLidiBene',
			titleBar: {
				title: 'Libro Diario Beneficencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/lidi/bene',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'registro(s)' );
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').outerHeight()-$mainPanel.find('div:first').outerHeight())+'px');
				}).resize();
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnCerrar]').click(function(){
					ciHelper.confirm(
						'Esta seguro(a) de cerrar este periodo?',
						function () {
							K.clearNoti();
							K.sendingInfo();
							$.post('ct/lidi/cerrar_bene',{
								mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
								ano: $mainPanel.find('[name=periodo]').data('ano')
							},function(){
								K.clearNoti();
								K.notification({title: 'Periodo cerrado',text: 'El cierre se realiz&oacute; con &eacute;xito!'});
								$mainPanel.find('[name=periodo]').change();
							});
						},
						function () {
							//nothing
						}
					);
				}).button({icons: {primary: 'ui-icon-gear'}});
				$mainPanel.find('[name=btnSel]').click(function(){
					ctLidiBene.windowSelectNotc();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        },
			        onChangeMonthYear: function(year,month,inst){
			            /*$(this).data('mes',month-1).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month-1, 1)));
			            $(this).change();*/
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth()-1)
			    .data('ano',ciHelper.date.getYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
			    	ctLidiBene.loadData({url: 'ct/lidi/lista'});
			    });
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
			ano: $mainPanel.find('[name=periodo]').data('ano')
		});
	    $.post(params.url, params, function(data){
			if ( data!=null ) {
				if(data.cerrado==true) $mainPanel.find('[name=btnCerrar],[name=btnSel]').hide();
				else $mainPanel.find('[name=btnCerrar],[name=btnSel]').show();
				var $row = $mainPanel.find('.gridReference').clone();
				$row.find('li:eq(6)').html(ciHelper.formatMon(data.debe_ini)).css({'text-align':'right'});
				$row.find('li:eq(7)').html(ciHelper.formatMon(data.haber_ini)).css({'text-align':'right'});
				$row.wrapInner('<a class="item">');
				$mainPanel.find(".gridBody").append( $row.children() );
				for (var i=0,j=data.notas.length; i<j; i++) {
					var result = data.notas[i],
					$row = $mainPanel.find('.gridReference').clone();
					$row.find('li:eq(4)').html('-------------- NC '+ciHelper.codigos(result.numero,3)+' --------------');
					$row.wrapInner('<a class="item" />');
					$mainPanel.find(".gridBody").append( $row.children() );
					for(var k=0,l=result.cuentas.length; k<l; k++){
						var $row = $mainPanel.find('.gridReference').clone(),
						substr_count = result.cuentas[k].cuenta.cod.split('.').length - 1,
						length = result.cuentas[k].cuenta.cod.length - substr_count;
						$row.wrapInner('<a class="item" />');
						if(length==4){
							if(result.cuentas[k].folio!=null) $row.find('li:eq(1)').html(result.cuentas[k].folio);
							$row.find('li:eq(2)').html(result.cuentas[k].cuenta.cod);
							if(data.cerrado==true){
								$row.find('li:eq(0)').html('<button name="btnGrid">M&aacute;s Acciones</button>');
								$row.find('a').data('cuenta',i).data('index',k).contextMenu("conMenCtLidi", {
									onShowMenu: function(e, menu) {
									    var excep = '';	
										$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
										$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
										$(e.target).closest('.item').click();
										K.tmp = $(e.target).closest('.item');
										return menu;
									},
									bindings: {
										'conMenCtLidi_agr': function(t){
											new K.Modal({
												id: 'windowAgrFolio',
												title: 'Agregar Folio',
												contentURL: 'ct/lidi/folio',
												icon: 'ui-icon-pencil',
												width: 350,
												height: 45,
												buttons: {
													"Guardar": function(){
														K.sendingInfo();
														$.post('ct/lidi/folio_bene',{
															mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
															ano: $mainPanel.find('[name=periodo]').data('ano'),
															cuenta: K.tmp.data('index'),
															nota: K.tmp.data('cuenta'),
															folio: $('#windowAgrFolio [name=folio]').val()
														},function(){
															K.clearNoti();
															K.closeWindow('windowAgrFolio');
															K.notification({title: 'Folio agregado',text: 'El folio se agreg&oacute; con &eacute;xito!'});
															$mainPanel.find('[name=periodo]').change();
														});
													},
													"Cerrar": function(){
														K.closeWindow('windowAgrFolio');
													}
												},
												onContentLoaded: function(){
													if(K.tmp.find('li:eq(1)').html()!='')
														$('#windowAgrFolio [name=folio]').val(K.tmp.find('li:eq(1)').html());
												}
											});
										}
									}
								});
							}
							if(result.cuentas[k].tipo=='D') $row.find('li:eq(6)').html(ciHelper.formatMon(result.cuentas[k].monto)).css({'text-align':'right'});
							else $row.find('li:eq(7)').html(ciHelper.formatMon(result.cuentas[k].monto)).css({'text-align':'right'});
						}else{
							$row.find('li:eq(3)').html(result.cuentas[k].cuenta.cod);
							$row.find('li:eq(5)').html(ciHelper.formatMon(result.cuentas[k].monto)).css({'text-align':'right'});
						}
						$row.find('li:eq(4)').html(result.cuentas[k].cuenta.descr);
			        	$("#mainPanel .gridBody").append( $row.children() );
			        	if(data.cerrado==true) ciHelper.gridButtons($("#mainPanel .gridBody"));
					}
					if(result.concepto!=''){
						var $row = $mainPanel.find('.gridReference').clone();
						$row.find('li:eq(4)').html('<b>'+result.concepto+'</b>');
						$row.wrapInner('<a class="item" />');
						$mainPanel.find(".gridBody").append( $row.children() );
					}
		        }
				var $row = $mainPanel.find('.gridReference').clone();
				$row.find('li:eq(6)').html(ciHelper.formatMon(data.debe_fin)).css({'text-align':'right'});
				$row.find('li:eq(7)').html(ciHelper.formatMon(data.haber_fin)).css({'text-align':'right'});
				$row.find('li:eq(1),li:eq(2),li:eq(3),li:eq(4),li:eq(5)').remove();
				$row.find('li:eq(0)').replaceWith('<li style="min-width:820px;max-width:820px;text-align:right;" class="ui-button ui-widget ui-state-default ui-button-text-only">Total</li>');
				$row.wrapInner('<a class="item" />');
				$mainPanel.find(".gridBody").append( $row.children() );
			}else{
				$mainPanel.find('[name=btnCerrar],[name=btnSel]').show();
			}
			$('#mainPanel').resize();
			K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowSelectNotc: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowSelectNotc',
			title: 'Seleccionar Notas',
			contentURL: 'ct/lidi/select',
			icon: 'ui-icon-search',
			width: 360,
			height: 350,
			buttons: {
				'Seleccionar': function(){
					K.clearNoti();
					var data = {
						mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						ano: $mainPanel.find('[name=periodo]').data('ano'),
						notas: []
					};
					for(var i=0,j=p.$w.find('[name^=rchkNotc]:checked').length; i<j; i++){
						var tmp = p.$w.find('.item').eq(i).data('data');
						tmp._id = tmp._id.$id;
						if(tmp.concepto==null) tmp.concepto = '';
						for(var k=0,l=tmp.cuentas.length; k<l; k++){
							tmp.cuentas[k].cuenta._id = tmp.cuentas[k].cuenta._id.$id;
						}
						data.notas.push(tmp);
					}
					if(data.notas.length==0)
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger al menos una nota!',type: 'error'});
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/lidi/save_bene',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El libro diario ha sido generado!'});
						$mainPanel.find('[name=periodo]').change();
						K.closeWindow(p.$w.attr('id'));
					});
				},
				'Cerrar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectNotc');
				K.block({$element: p.$w});
				p.$w.find('[name=periodo]').html($mainPanel.find('[name=periodo]').val());
				p.$w.find('[name=btnSel]').click(function(){
					p.$w.find('[name^=rchkNotc]').attr('checked','checked');
				}).button({icons: {primary: 'ui-icon-check'}});
				$.post('ct/notc/lista_all',{
					mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
					ano: $mainPanel.find('[name=periodo]').data('ano')
				},function(data){
					if(data!=null){
						for(var i=0,j=data.length; i<j; i++){
							var $row = p.$w.find('.gridReference').clone();
							$row.find('li:eq(0)').html('<input type="checkbox" name="rchkNotc'+data[i]._id.$id+'" id="'+data[i]._id.$id+'" />');
							$row.find('li:eq(1)').html(data[i].tipo.nomb);
							$row.find('li:eq(2)').html(data[i].num);
							$row.wrapInner('<a class="item">');
							$row.find('a').data('data',data[i])/*.click(function(){
								if($(this).find("[name^=rchkNotc]").is(":checked")==true)
									$(this).find('[name^=rchkNotc]').removeAttr('checked');
								else
									$(this).find('[name^=rchkNotc]').attr('checked','checked');
							});*/
							p.$w.find('.gridBody').append($row.children());
						}
						K.unblock({$element: p.$w});
					}else{
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Informaci&oacute;n faltante',text: 'No existen notas de contabilidad para el periodo seleccionado!',type: 'info'});
					}
				},'json');
			}
		});
	}
};