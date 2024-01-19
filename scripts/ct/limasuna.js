/*******************************************************************************
libro mayor sunat */
ctLimaSuna = {
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
				$p.find('[name=ctLimaBene]').click(function(){ ctLimaBene.init(); });
				$p.find('[name=ctLimaSuna]').click(function(){ ctLimaSuna.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctLimaSuna',
			titleBar: {
				title: 'Libro Mayor SUNAT'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/lima/suna',
			store: false,
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
							$.post('ct/lima/cerrar_suna',{
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
					K.clearNoti();
					if($mainPanel.find('[name=cuenta]').data('id')==null){
						$mainPanel.find('[name=btnCta]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable!',type: 'info'});
					}
					K.sendingInfo();
					$.post('ct/lima/save_suna',{
						cuenta: $mainPanel.find('[name=cuenta]').data('id'),
						mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						ano: $mainPanel.find('[name=periodo]').data('ano')
					},function(data){
						K.clearNoti();
						if(data.rpta==false){
							K.notification({title: 'Informaci&oacute;n Incompleta',text: 'No hay un periodo cerrado en el Libro Diario de SUNAT para el periodo seleccionado!',type: 'info'});
						}else{
							K.notification({title: 'Periodo generado',text: 'La generaci&oacute;n se realiz&oacute; con &eacute;xito!'});
							$mainPanel.find('[name=periodo]').change();
						}
					},'json');
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						if(data.cuentas.hijos==null){
							$mainPanel.find('[name=cuenta]').html(data.cod)
							.data('id',data._id.$id).data('data',data);
							$mainPanel.find('[name=descr]').html(data.descr);
					    	$mainPanel.find('.gridBody').empty();
					    	ctLimaSuna.loadData({url: 'ct/lima/lista_suna'});
						}else{
							return K.notification({title: 'Cuenta err&oacute;nea',text: 'Debe seleccionar una cuenta contable de &uacute;ltimo nivel!',type: 'info'});
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
			    	ctLimaSuna.loadData({url: 'ct/lima/lista_suna'});
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
				if(data[0].estado=='C') $mainPanel.find('[name=btnCerrar],[name=btnSel]').hide();
				else $mainPanel.find('[name=btnCerrar],[name=btnSel]').show();
				var debe = 0,
				haber = 0,
				cont = 0;
				for(var i=0,j=data.length; i<j; i++){
					var $row = $mainPanel.find('.gridReference').clone(),
					result = data[i];
					$row.find('li:eq(1)').html(ciHelper.codigos(result.cod,4));
					$row.find('li:eq(2)').html(result.glosa);
					$row.find('li:eq(3)').html(ciHelper.formatMon(result.debe));
					$row.find('li:eq(4)').html(ciHelper.formatMon(result.haber));
					$row.find('li:eq(5)').html(result.estado_sunat);
					$row.wrapInner('<a class="item">');
					if(result.estado=='A'){
						$row.find('li:eq(0)').html('<button name="btnGrid">M&aacute;s Acciones</button>');
						$row.find('a').data('data',result).contextMenu("conMenCtLisu", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								return menu;
							},
							bindings: {
								'conMenCtLisu_comp': function(t){
									var data = K.tmp.data('data');
									ctLimaSuna.windowComplet({id: data._id.$id,nomb: data.cod,data:data});
								}
							}
						});
					}
					$mainPanel.find(".gridBody").append( $row.children() );
					debe += parseFloat(result.debe);
					haber += parseFloat(result.haber);
			        if(result.estado=='A') ciHelper.gridButtons($("#mainPanel .gridBody"));
				}
				var $row = $mainPanel.find('.gridReference').clone();
				$row.find('li:eq(3)').html(ciHelper.formatMon(debe));
				$row.find('li:eq(4)').html(ciHelper.formatMon(haber));
				$row.find('li:eq(1),li:eq(2)').remove();
				$row.find('li:eq(0)').replaceWith('<li style="min-width:535px;max-width:535px;text-align:right;" class="ui-button ui-widget ui-state-default ui-button-text-only">Total</li>');
				$row.wrapInner('<a class="item" />');
				$mainPanel.find(".gridBody").append( $row.children() );
				if(data.length==cont) $mainPanel.find('[name=btnCerrar]').data('cierre',true);
			}else{
				$mainPanel.find('[name=btnCerrar],[name=btnSel]').show();
			}
			$('#mainPanel').resize();
			K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowComplet: function(p){
		new K.Modal({
			id: 'windowCompNotc',
			title: 'Completar Datos del Libro Mayor de la SUNAT',
			contentURL: 'ct/lima/comp',
			icon: 'ui-icon-pencil',
			width: 400,
			height: 50,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						diario: p.$w.find('[name=diario]').val()
					};
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/lima/comp_suna',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El libro mayor ha sido actualizado!'});
						$mainPanel.find('[name=periodo]').change();
						K.closeWindow(p.$w.attr('id'));
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCompNotc');
				if(p.data.diario!=null) p.$w.find('[name=diario]').val(p.data.diario);
			}
		});
	}
};