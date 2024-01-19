/*******************************************************************************
Control Patrimonial */
ctCpat = {
	init: function(){
		K.initMode({
			mode: 'ct',
			action: 'ctCpat',
			titleBar: {
				title: 'Control Patrimonial'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/cpat',
			store: false,
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'pabellon(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnIni]').click(function(){
					K.incomplete();
				}).button();
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctCpat.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnCerrar]').click(function(){
					K.incomplete();
				}).button({icons: {primary: 'ui-icon-circle-close'}});
				$mainPanel.find('[name=periodo]').datepicker( {
			        showButtonPanel: true,
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            ctCpat.loadData({url: 'ct/cpat/lista'});
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth()-1)
			    .data('ano',ciHelper.date.getYear()).change(function(){
			    	ctCpat.loadData({url: 'ct/cpat/lista'});
			    });
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.outerHeight()-$mainPanel.find('.grid:eq(0)').outerHeight()-$mainPanel.find('div:first').outerHeight()-$mainPanel.find('.div-bottom').outerHeight())+'px');
				}).resize();
				$.post('ct/cpat/get_pabe',function(data){
					if(data.pabe==null){
						K.block({$element: $('#pageWrapperMain'),message: 'Pabellones no creados!'});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe crear pabellones!',type: 'error'});
					}
					var $select = $mainPanel.find('[name=pabellon]');
					for(var i=0,j=data.pabe.length; i<j; i++){
						$select.append('<option value="'+data.pabe[i]._id.$id+'">'+data.pabe[i].nomb+', '+data.pabe[i].num+'</option>');
						$select.find('option:last').data('data',data.pabe[i]);
					}
					$select.change(function(){
						$mainPanel.find('.gridBody').empty();
						$('.class-pabe,.class-tot').remove();
						var pabe = $(this).find('option:selected').data('data');
						for(var i=1,j=parseFloat(pabe.filas); i<=j; i++){
							$mainPanel.find('.gridHeader ul:first').append('<li class="class-pabe ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">'
							+'<ul style="display:block">'
							+'<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">'+i+'&deg; a S/.</li>'
							+'</ul>'
							+'<ul style="display:block">'
							+'<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Cant.</li>'
							+'<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Valor</li>'
							+'</ul>'
							+'</li>');
							$mainPanel.find('.gridReference ul').append('<li class="class-pabe" style="min-width:100px;max-width:100px;"></li>'
							+'<li class="class-pabe" style="min-width:100px;max-width:100px;"></li>');
						}
						$mainPanel.find('.gridHeader ul:first').append('<li class="class-tot ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">'
						+'<ul style="display:block">'
						+'<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:200px;max-width:200px;">Total</li>'
						+'</ul>'
						+'<ul style="display:block">'
						+'<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Cant.</li>'
						+'<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">Valor</li>'
						+'</ul>'
						+'</li>');
						$mainPanel.find('.gridReference ul').append('<li class="class-tot" style="min-width:100px;max-width:100px;"></li>'
						+'<li class="class-tot" style="min-width:100px;max-width:100px;"></li>');
						$mainPanel.resize();
						$mainPanel.find('[name=periodo]').change();
					}).change();
				    K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			/*img: $mainPanel.find('[name=pabellon] option:selected').data('data').imagen.$id,*/
			pabe: $mainPanel.find('[name=pabellon] option:selected').val(),
	    	mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
	    	ano: $mainPanel.find('[name=periodo]').data('ano')
		});
	    $.post(params.url, params, function(data){
			if ( data!=null ) {
				var tots = [],cants = [];
				for (i=0; i < data.length; i++) {
					result = data[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html( ciHelper.dateFormatOnlyDay(result.fecha) );
					$li.eq(1).html( ciHelper.enti.formatName(result.cliente) );
					$li.eq(2).html( result.espacio.num );
					$li.eq(3).html( result.comprobante );
					$row.find('.class-pabe').eq((parseInt(result.espacio.fila)*2)).html(result.cantidad);
					$row.find('.class-pabe').eq((parseInt(result.espacio.fila)*2)+1).html(ciHelper.formatMon(result.espacio.costo));
					if(tots[parseInt(result.espacio.fila)]==null){
						tots[parseInt(result.espacio.fila)] = 0;
						cants[parseInt(result.espacio.fila)] = 0;
					}
					tots[parseInt(result.espacio.fila)] += parseFloat(result.espacio.costo);
					cants[parseInt(result.espacio.fila)] += parseInt(result.cantidad);
					$row.find('.class-tot:eq(0)').html(result.cantidad);
					$row.find('.class-tot:eq(1)').html(ciHelper.formatMon(result.espacio.costo));
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
		        	$("#mainPanel .gridBody").append( $row.children() );
		        }
				var $row = $('.gridReference','#mainPanel').clone();
				$row.find('li:eq(1),li:eq(2),li:eq(3)').remove();
				$row.find('li:eq(0)').replaceWith('<li style="min-width:380px;max-width:380px;text-align: right;" class="ui-button ui-widget ui-state-default ui-button-text-only">Total</li>');
				for(var i=0,j=parseInt($mainPanel.find('[name=pabellon] option:selected').data('data').filas); i<j; i++){
					$row.find('.class-pabe').eq((i*2)).html(cants[i]?cants[i]:'0');
					$row.find('.class-pabe').eq((i*2)+1).html(tots[i]?ciHelper.formatMon(tots[i]):ciHelper.formatMon(0));
				}
				$row.wrapInner('<a class="item" href="javascript: void(0);" />');
	        	$("#mainPanel .gridBody").append( $row.children() );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowNew: function(p){
		p = {
			pabe: $mainPanel.find('[name=pabellon] option:selected').data('data'),
	    	mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
	    	ano: $mainPanel.find('[name=periodo]').data('ano')
		};
		new K.Modal({
			id: 'windowCtCpatNew',
			title: 'Nuevo Registro del Control Patrimonial',
			contentURL: 'ct/cpat/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 220,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					if(p.$w.find('[name=num] option:selected').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir un espacio v&aacute;lido!',type: 'error'});
					}
					if(p.$w.find('[name=concepto]').data('data')==null){
						p.$w.find('[name=btnConc]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir un concepto!',type: 'error'});
					}
					var data = {
						periodo: {
					    	mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
					    	ano: $mainPanel.find('[name=periodo]').data('ano')
						},
						pabellon: {
							_id: $mainPanel.find('[name=pabellon] option:selected').data('data')._id.$id,
							nomb: $mainPanel.find('[name=pabellon] option:selected').data('data').nomb,
							num: $mainPanel.find('[name=pabellon] option:selected').data('data').num
						},
						espacio: {
							_id: p.$w.find('[name=num] option:selected').data('data')._id.$id,
							nomb: p.$w.find('[name=num] option:selected').data('data').nomb,
							costo: p.$w.find('[name=num] option:selected').data('data').costo,
							fila: p.$w.find('[name=num] option:selected').data('data').nicho.fila,
							num: p.$w.find('[name=num] option:selected').data('data').nicho.num
						},
						cantidad: p.$w.find('[name=cant]').html(),
						comprobante: p.$w.find('[name=comp]').val(),
						fecha: p.$w.find('[name=fecha]').val(),
						cliente: ciHelper.enti.dbRel(p.$w.find('[name=concepto]').data('data')),
						tipo: p.$w.find('[name=rbtnTipo]:checked').val()
					};
					if(data.comprobante==''){
						p.$w.find('[name=comp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un comprobante!',type: 'error'});
					}
					if(data.fecha==''){
						p.$w.find('[name=fecha]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("ct/cpat/save",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						$mainPanel.find('[name=periodo]').change();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "El registro se guard&oacute; con &eacute;xito!"});
					},'json');
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCtCpatNew');
				K.block({$element: p.$w});
				$.post('cm/espa/lista_nichos',{_id: p.pabe._id.$id},function(data){
					p.espa = data.items;
					if(p.espa==null){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay espacios para el pabell&oacute;n seleccionado!',type: 'info'});
						return K.closeWindow(p.$w.attr('id'));
					}
					p.$w.find('[name=fecha]').datepicker({
						changeMonth: false,
					    changeYear: false,
						minDate: new Date($mainPanel.find('[name=periodo]').data('ano'),$mainPanel.find('[name=periodo]').data('mes'),1),
						maxDate: new Date($mainPanel.find('[name=periodo]').data('ano'),$mainPanel.find('[name=periodo]').data('mes'),31)
					});
					p.$w.find('[name=periodo]').html($mainPanel.find('[name=periodo]').val());
					p.$w.find('[name=pabellon]').html($mainPanel.find('[name=pabellon] option:selected').html());
					p.$w.find('td:eq(5)').buttonset();
					p.$w.find('#rbtnTipo1').click(function(){
						p.$w.find('[name=cant]').html('1');
						p.$w.find('[name=fila]').change();
					}).click();
					p.$w.find('#rbtnTipo2').click(function(){
						p.$w.find('[name=cant]').html('-1');
						p.$w.find('[name=fila]').change();
					});
					p.$w.find('[name=num]').change(function(){
						if($(this).find('option:selected').data('data')!=null)
							p.$w.find('[name=monto]').html(ciHelper.formatMon($(this).find('option:selected').data('data').costo));
						else
							p.$w.find('[name=monto]').html(ciHelper.formatMon(0));
					});
					var $cbo = p.$w.find('[name=fila]');
					for(var i=0; i<parseInt(p.pabe.filas); i++){
						$cbo.append('<option value="'+(i+1)+'">'+(i+1)+'</option>');
					}
					$cbo.change(function(){
						var $this = $(this),
						$cbo = p.$w.find('[name=num]').empty();
						for(var i=0,j=p.espa.length; i<j; i++){
							if(parseInt(p.espa[i].nicho.fila)==$this.find('option:selected').val()){
								if(p.$w.find('[name=rbtnTipo]:checked').val()=='P'&&p.espa[i].estado=='D'){
									$cbo.append('<option value="'+p.espa[i]._id.$id+'">'+p.espa[i].nicho.num+'</option>');
									$cbo.find('option:last').data('data',p.espa[i]);
								}
								if(p.$w.find('[name=rbtnTipo]:checked').val()=='D'&&p.espa[i].estado=='C'){
									$cbo.append('<option value="'+p.espa[i]._id.$id+'">'+p.espa[i].nicho.num+'</option>');
									$cbo.find('option:last').data('data',p.espa[i]);
								}
							}
						}
						$cbo.change();
					}).change();
					p.$w.find('[name=btnConc]').click(function(){
						ciSearch.windowSearchEnti({callback: function(data){
							p.$w.find('[name=concepto]').html(ciHelper.enti.formatName(data)).data('data',data);
						}});
					}).button({icons: {primary: 'ui-icon-search'}});
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};