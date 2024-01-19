/*******************************************************************************
libro mayor beneficencia */
ctLimaBene = {
	init: function(){
		if($('#pageWrapper [child=lima]').length<=0){
			$.post('ct/navg/lima',function(data){
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
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="lima" />');
					$p.find("[name=ctLima]").after( $row.children() );
				}
				$p.find('[name=ctLima]').data('lima',$('#pageWrapper [child=lima]:first').data('lima'));
				$p.find('[name=ctLimaSuna]').click(function(){ ctLimaSuna.init(); });
				$p.find('[name=ctLimaBene]').click(function(){ ctLimaBene.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctLimaBene',
			titleBar: {
				title: 'Libro Mayor Beneficencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/lima/bene',
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
							K.sendingInfo();
							$.post('ct/lima/cerrar_bene',{
								//cuenta: $mainPanel.find('[name=cuenta]').data('id'),
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
					ctLimaBene.windowSelectNotc();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						var substr_count = data.cod.split('.').length - 1,
						length = data.cod.length - substr_count;
						if(length==4){
							$mainPanel.find('[name=cuenta]').html(data.cod)
							.data('id',data._id.$id).data('data',data);
							$mainPanel.find('[name=descr]').html(data.descr);
					    	$mainPanel.find('.gridBody').empty();
					    	ctLimaBene.loadData({url: 'ct/lima/lista'});
						}else{
							return K.notification({title: 'Cuenta err&oacute;nea',text: 'Debe seleccionar una cuenta contable de cuatro d&iacute;gitos!',type: 'info'});
						}
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            //$(this).change();
			        },
			        onChangeMonthYear: function(year,month,inst){
			            $(this).data('mes',month-1).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month-1, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth()-1)
			    .data('ano',ciHelper.date.getYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
			    	ctLimaBene.loadData({url: 'ct/lima/lista'});
			    });
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			cuenta: $mainPanel.find('[name=cuenta]').data('id'),
			mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
			ano: $mainPanel.find('[name=periodo]').data('ano')
		});
		if(params.cuenta==null){
			$mainPanel.find('[name=btnCta]').click();
			return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta!',type: 'error'});
		}
	    $.post(params.url, params, function(data){
			if ( data!=null ) {
				if(data.estado=='C') $mainPanel.find('[name=btnCerrar],[name=btnSel]').hide();
				else $mainPanel.find('[name=btnCerrar],[name=btnSel]').show();
				var $row = $mainPanel.find('.gridReference').clone();
				$row.find('li:eq(4)').html(ciHelper.formatMon(data.debe_ini));
				$row.find('li:eq(5)').html(ciHelper.formatMon(data.haber_ini));
				$row.find('li:eq(6)').html(ciHelper.formatMon(parseFloat(data.debe_ini)-parseFloat(data.haber_ini)));
				$mainPanel.find(".gridBody").append( $row.children() );
				for (var i=0,j=data.notas.length; i<j; i++) {
					var result = data.notas[i],
					$row = $mainPanel.find('.gridReference').clone();
					$row.wrapInner('<a class="item" />');
					if(result.tipo_nota!=null) $row.find('li:eq(1)').html(result.tipo_nota.nomb+' '+result.num);
					else $row.find('li:eq(1)').html('NC '+result.num);
					$row.find('li:eq(2)').html(result.concepto);
					if(result.folio!=null) $row.find('li:eq(3)').html(result.folio);
					if(data.estado=='C'){
						$row.find('li:eq(0)').html('<button name="btnGrid">M&aacute;s Acciones</button>');
						$row.find('a').data('index',i).contextMenu("conMenCtLima", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								return menu;
							},
							bindings: {
								'conMenCtLima_agr': function(t){
									new K.Modal({
										id: 'windowAgrFolio',
										title: 'Agregar Folio',
										contentURL: 'ct/lima/folio',
										icon: 'ui-icon-pencil',
										width: 350,
										height: 45,
										buttons: {
											"Guardar": function(){
												K.sendingInfo();
												$.post('ct/lima/folio_bene',{
													mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
													ano: $mainPanel.find('[name=periodo]').data('ano'),
													cuenta: $mainPanel.find('[name=cuenta]').data('id'),
													nota: K.tmp.data('index'),
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
											if(K.tmp.find('li:eq(3)').html()!='')
												$('#windowAgrFolio [name=folio]').val(K.tmp.find('li:eq(3)').html());
										}
									});
								}
							}
						});
					}
					if(result.tipo=='D') $row.find('li:eq(4)').html(ciHelper.formatMon(result.monto));
					else $row.find('li:eq(5)').html(ciHelper.formatMon(result.monto));
		        	$("#mainPanel .gridBody").append( $row.children() );
		        	if(data.estado=='C') ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
				var $row = $mainPanel.find('.gridReference').clone();
				$row.find('li:eq(4)').html(ciHelper.formatMon(data.debe_fin));
				$row.find('li:eq(5)').html(ciHelper.formatMon(data.haber_fin));
				$row.find('li:eq(6)').html(ciHelper.formatMon(parseFloat(data.debe_fin)-parseFloat(data.haber_fin)));
				$row.find('li:eq(1),li:eq(2),li:eq(3)').remove();
				$row.find('li:eq(0)').replaceWith('<li style="min-width:680px;max-width:680px;text-align:right;" class="ui-button ui-widget ui-state-default ui-button-text-only">Total</li>');
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
			contentURL: 'ct/lima/select',
			icon: 'ui-icon-search',
			width: 360,
			height: 350,
			buttons: {
				'Seleccionar': function(){
					var data = {
						mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						ano: $mainPanel.find('[name=periodo]').data('ano'),
						cuenta: {
							_id: $mainPanel.find('[name=cuenta]').data('id'),
							cod: $mainPanel.find('[name=cuenta]').data('data').cod,
							descr: $mainPanel.find('[name=cuenta]').data('data').descr
						},
						notas: []
					};
					for(var i=0,j=p.$w.find('[name^=rchkNotc]:checked').length; i<j; i++){
						var tmp = p.$w.find('.item').eq(i).data('data');
						tmp._id = tmp._id.$id;
						if(tmp.concepto==null) tmp.concepto = '';
						tmp.tipo._id = tmp.tipo._id.$id;
						for(var k=0,l=tmp.cuentas.length; k<l; k++){
							tmp.cuentas[k].cuenta._id = tmp.cuentas[k].cuenta._id.$id;
						}
						data.notas.push(tmp);
					}
					if(data.notas.length==0)
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger al menos una nota!',type: 'error'});
					$.post('ct/lima/save_bene',data,function(){
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El libro mayor ha sido generado!'});
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
					ano: $mainPanel.find('[name=periodo]').data('ano'),
					cuenta: $mainPanel.find('[name=cuenta]').data('id')
				},function(data){
					if(data!=null){
						for(var i=0,j=data.length; i<j; i++){
							var $row = p.$w.find('.gridReference').clone();
							$row.find('li:eq(0)').html('<input type="checkbox" name="rchkNotc'+data[i]._id.$id+'" id="'+data[i]._id.$id+'" />');
							$row.find('li:eq(1)').html(data[i].tipo.nomb);
							$row.find('li:eq(2)').html(data[i].num);
							$row.wrapInner('<a class="item">');
							$row.find('a').data('data',data[i]).click(function(){
								if($(this).find("[name^=rchkNotc]").is(":checked")==true)
									$(this).find('[name^=rchkNotc]').removeAttr('checked');
								else
									$(this).find('[name^=rchkNotc]').attr('checked','checked');
							});
							p.$w.find('.gridBody').append($row.children());
						}
					}else{
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Informaci&oacute;n faltante',text: 'No existen notas de contabilidad para el periodo seleccionado!',type: 'info'});
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};