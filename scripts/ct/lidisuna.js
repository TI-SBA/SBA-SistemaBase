/*******************************************************************************
libro diario sunat */
ctLidiSuna = {
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
				$p.find('[name=ctLidiBene]').click(function(){ ctLidiBene.init(); });
				$p.find('[name=ctLidiSuna]').click(function(){ ctLidiSuna.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctLidiSuna',
			titleBar: {
				title: 'Libro Diario SUNAT'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/lidi/suna',
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
					K.clearNoti();
					if($(this).data('cierre')==false)
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar todas las glosas para cerrar el periodo!',type: 'info'});
					ciHelper.confirm(
						'Esta seguro(a) de cerrar este periodo?',
						function () {
							K.sendingInfo();
							$.post('ct/lidi/cerrar_suna',{
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
				}).data('cierre',false).button({icons: {primary: 'ui-icon-gear'}});
				$mainPanel.find('[name=btnSel]').click(function(){
					ctLidiSuna.windowSelectNotc();
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
			    	ctLidiSuna.loadData({url: 'ct/lidi/lista_suna'});
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
			$mainPanel.find('[name=btnCerrar]').data('cierre',false);
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
					$row.find('li:eq(2)').html(ciHelper.dateFormat(result.fec));
					if(result.glosa!=null){
						$row.find('li:eq(3)').html(result.glosa);
						cont++;
					}
					if(result.cod_libro!=null) $row.find('li:eq(4)').html(result.cod_libro.cod);
					if(result.num!=null) $row.find('li:eq(5)').html(result.num);
					if(result.num_doc!=null) $row.find('li:eq(6)').html(result.num_doc);
					$row.find('li:eq(7)').html(result.cuenta.cod);
					$row.find('li:eq(8)').html(result.cuenta.descr);
					var it_debe = "";
					var it_haber = "";
					if(parseFloat(result.debe)!=0)it_debe = ciHelper.formatMon(result.debe);
					if(parseFloat(result.haber)!=0)it_haber = ciHelper.formatMon(result.haber);
					$row.find('li:eq(9)').html(it_debe).css({'text-align':'right'});
					$row.find('li:eq(10)').html(it_haber).css({'text-align':'right'});
					$row.find('li:eq(11)').html(result.plan.cod).css({'text-align':'center'});
					$row.find('li:eq(12)').html(result.estado_sunat).css({'text-align':'center'});
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
									ctLidiSuna.windowComplet({id: data._id.$id,nomb: data.cod,data:data});
								}
							}
						});
					}
					$mainPanel.find(".gridBody").append( $row.children() );
					if(ciHelper.strlen(result.cuenta.cod)==4){
						debe += parseFloat(result.debe);
						haber += parseFloat(result.haber);
					}
			        if(result.estado=='A') ciHelper.gridButtons($("#mainPanel .gridBody"));
				}
				var $row = $mainPanel.find('.gridReference').clone();
				$row.find('li:eq(9)').html(ciHelper.formatMon(debe)).css({'text-align':'right'});
				$row.find('li:eq(10)').html(ciHelper.formatMon(haber)).css({'text-align':'right'});
				$row.find('li:eq(1),li:eq(2),li:eq(3),li:eq(4),li:eq(5),li:eq(6),li:eq(7),li:eq(8),li:eq(11),li:eq(12)').remove();
				$row.find('li:eq(0)').replaceWith('<li style="min-width:1490px;max-width:1490px;text-align:right;" class="ui-button ui-widget ui-state-default ui-button-text-only">Total</li>');
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
	windowSelectNotc: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowSelectNotc',
			title: 'Seleccionar Notas',
			contentURL: 'ct/lidi/select',
			icon: 'ui-icon-search',
			width: 380,
			height: 350,
			buttons: {
				'Seleccionar': function(){
					K.clearNoti();
					var tmp = p.$w.find('[name=pcta] option:selected').data('data'),
					data = {
						mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						ano: $mainPanel.find('[name=periodo]').data('ano'),
						pcta: {
							_id: tmp._id.$id,
							cod: tmp.cod
						},
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
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/lidi/save_suna',data,function(){
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
				p.$w.find('table:first tr:first').after('<tr><td><label>Plan de Cuentas</label></td><td><select name="pcta"></select></td></tr>')
				p.$w.find('[name=periodo]').html($mainPanel.find('[name=periodo]').val());
				p.$w.find('[name=btnSel]').click(function(){
					p.$w.find('[name^=rchkNotc]').attr('checked','checked');
				}).button({icons: {primary: 'ui-icon-check'}});
				$.post('ct/notc/lista_all',{
					pcta: true,
					mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
					ano: $mainPanel.find('[name=periodo]').data('ano')
				},function(data){
					if(data.notas!=null){
						for(var i=0,j=data.notas.length; i<j; i++){
							var $row = p.$w.find('.gridReference').clone();
							$row.find('li:eq(0)').html('<input type="checkbox" name="rchkNotc'+data.notas[i]._id.$id+'" id="'+data.notas[i]._id.$id+'" />');
							$row.find('li:eq(1)').html(data.notas[i].tipo.nomb);
							$row.find('li:eq(2)').html(data.notas[i].num);
							$row.wrapInner('<a class="item">');
							$row.find('a').data('data',data.notas[i]).click(function(){
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
					if(data.pcta!=null){
						var $cbo = p.$w.find('[name=pcta]');
						for(var i=0,j=data.pcta.length; i<j; i++){
							$cbo.append('<option value="'+data.pcta[i]._id.$id+'">'+data.pcta[i].cod+' - '+data.pcta[i].descr+'</option>');
							$cbo.find('option:last').data('data',data.pcta[i]);
						}
					}else{
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Informaci&oacute;n faltante',text: 'No existen Planes de Cuentas!',type: 'info'});
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowComplet: function(p){
		new K.Modal({
			id: 'windowSelectNotc',
			title: 'Completar Datos del Libro Diario de la SUNAT',
			contentURL: 'ct/lidi/comp',
			icon: 'ui-icon-pencil',
			width: 400,
			height: 300,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						glosa: p.$w.find('[name=glosa]').val(),
						cod_libro: {
							_id: p.$w.find('[name=cod_libro] option:selected').val(),
							cod: p.$w.find('[name=cod_libro] option:selected').data('data').cod
						},
						num: p.$w.find('[name=num]').val(),
						num_doc: p.$w.find('[name=num_doc]').val()
					};
					if(data.glosa==''){
						p.$w.find('[name=glosa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos la glosa!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/lidi/comp_suna',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El libro diario ha sido actualizado!'});
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
				p.$w = $('#windowSelectNotc');
				K.block({$element: p.$w});
				$.post('ct/coli/all',function(data){
					if(data==null){
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar antes C&oacute;digos de Libros!',type: 'info'});
					}
					var $cbo = p.$w.find('[name=cod_libro]');
					for(var i=0,j=data.length; i<j; i++){
						$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].cod+' - '+data[i].descr+'</option>');
						$cbo.find('option:last').data('data',data[i]);
					}
					if(p.data.glosa!=null) p.$w.find('[name=glosa]').val(p.data.glosa);
					if(p.data.cod_libro!=null) p.$w.find('[name=cod_libro]').selectVal(p.data.cod_libro._id.$id);
					if(p.data.num!=null) p.$w.find('[name=num]').val(p.data.num);
					if(p.data.num_doc!=null) p.$w.find('[name=num_doc]').val(p.data.num_doc);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};