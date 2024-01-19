/*******************************************************************************
Notas numericas */
ctNotaNum = {
	init: function(){
		if($('#pageWrapper [child=nota]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ct/navg/nota',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="nota" />');
					$p.find("[name=ctNota]").after( $row.children() );
				}
				$p.find('[name=ctNota]').data('nota',$('#pageWrapper [child=nota]:first').data('nota'));
				$p.find('[name=ctNotaLit]').click(function(){ ctNotaLit.init(); });
				$p.find('[name=ctNotaNum]').click(function(){ ctNotaNum.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctNotaNum',
			titleBar: {
				title: 'Nota a los estados financieros: Num&eacute;ricas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/nota/index_num',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el codigo de una cuenta contable' ).width('250');
				$mainPanel.find('[name=obj]').html( 'nota(s) a los estados financieros' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctNotaNum.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				/**** */
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						ctNotaNum.loadData({page: 1,url: 'ct/nota/lista_num'});
					}else{
						$("#mainPanel .gridBody").empty();
						ctNotaNum.loadData({page: 1,url: 'ct/nota/search_num'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				ctNotaNum.loadData({page: 1,url: 'ct/nota/lista_num'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					var result = data.items[i],
					$row = $('.gridReference','#mainPanel').clone(),
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( ciHelper.meses[parseFloat(result.periodo.mes)-1]+' - '+result.periodo.ano );
					$li.eq(2).html( result.num );
					$li.eq(3).html( result.nomb );
					$li.eq(4).html( ciHelper.enti.formatName(result.autor_numerica) );
					$li.eq(5).html( ciHelper.dateFormat(result.fec_numerica) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id)
					.contextMenu("conMenList", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$(excep+',#conMenList_about,#conMenList_imp,#conMenList_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									ctNotaNum.windowEdit({id: K.tmp.data('id')});
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
						ctNotaNum.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
		        }else{
					$("#mainPanel .gridFoot").hide();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
		        }
	      }else{
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
		new K.Window({
			id: 'windowNewctNotaNum',
			title: 'Nueva Nota Numerica',
			contentURL: 'ct/nota/num_edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					if(p.$w.find('[name=num]').data('data')==null){
						p.$w.find('[name=btnNum]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir una nota literal!',type: 'error'});
					}
					var tmp = p.$w.find('[name=num]').data('data'),
					data = {
						_id: tmp._id.$id,
						tipo: p.$w.find('[name=rbtnTipo]:checked').val(),
						documento: p.$w.find('[name=documento] :selected').val(),
						clase: p.$w.find('[name=clase] :selected').val(),
						subclase: p.$w.find('[name=subclase] :selected').val()
					};
					if(data.tipo=='A'){
						data.activos = [];
						for(var i=0,j=p.$w.find('.gridBody:last .item').length; i<j; i++){
							var $row = p.$w.find('.gridBody:last .item').eq(i),
							cuenta = $row.data('data'),
							item = {
								cuenta: {
									_id: cuenta._id.$id,
									cod: cuenta.cod,
									descr: cuenta.descr
								},
								valor_bruto: $row.find('[name^=bruto]').val(),
								depreciacion: $row.find('[name^=depre]').val(),
								valor_neto: $row.find('[name^=neto]').val()
							};
							if(item.valor_bruto==''){
								$row.find('[name^=bruto]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el valor bruto!',type: 'error'});
							}
							/*if(item.depreciacion==''){
								$row.find('[name^=depre]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar la depreciaci&oacute;n!',type: 'error'});
							}*/
							if(item.valor_neto==''){
								$row.find('[name^=neto]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el valor neto!',type: 'error'});
							}
							data.activos.push(item);
						}
						if(data.activos.length==0){
							p.$w.find('[name=btnAddCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir al menos una cuenta!',type: 'error'});
						}
					}else{
						data.otros = [];
						for(var i=0;i<p.$w.find('.gridBody:first .item').length;i++){
							var monto = p.$w.find('.gridBody:first .item').eq(i).find('[name^=monto]').val();
							if(monto=="")monto="0";
							if(monto!="0"){
								var insertcuen = {
									cuenta:{
										_id:p.$w.find('.gridBody:first .item').eq(i).data('data')._id.$id,
										cod:p.$w.find('.gridBody:first .item').eq(i).find('li').eq(0).html(),
										descr:p.$w.find('.gridBody:first .item').eq(i).find('li').eq(1).html()
									},
									monto:monto
								};
								data.otros.push(insertcuen);
							}
						}
						if(data.otros.length==0){
							p.$w.find('[name=btnAddCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir al menos una cuenta!',type: 'error'});
						}
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/nota/save_num',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La nota num&eacute;rica fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name^=monto_]').die('change');
				p.$w.find('[name=btnEliCuen]').die('click');
				p.$w.find('[name^=bruto_]').die('change');
				p.$w.find('[name^=depre_]').die('change');
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewctNotaNum');
				p.$w.find('[name=btnAddCuen]').click(function(){
					var params = {callback: function(data1){
						if(p.$w.find('[name=rbtnTipo]:checked').val()=='O'){
							$.post('ct/pcon/lista',{
								id:data1._id.$id,
								page_rows:100000,
								page:1,
								texto:""
							},function(data){
								if(data!=null){
									for(i=0;i<data.items.length;i++){
										if(p.$w.find(".gridBody:first [id="+data.items[i]._id.$id+"]").length<=0){
											var $row = p.$w.find('.gridReference:first').clone(),
											readonly = "",
											result = data.items[i];
											$li = $('li',$row);
											$li.eq(0).html(result.cod);
											$li.eq(1).html(result.descr);
											$li.eq(2).html('<input type="text" size="8" padre="monto_'+result.cuentas.padre+'" name="monto_'+result._id.$id+'">');
											$row.find('[name^=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }});
											$row.find('.ui-button').css('height','14px');										
											$row.find('[name=btnEliCuen]').button({icons: {primary: 'ui-icon-trash'},text: false});
											$row.wrapInner('<a name="'+result.cod+'" id="'+result._id.$id+'" class="item" />');
											$row.find('a').data('data',result );
											p.$w.find(".gridBody:first").append( $row.children() );
										}
									}
								}
							},'json');
						}else{
							if(p.$w.find(".gridBody:last [id="+data1._id.$id+"]").length<=0){
								var $row = p.$w.find('.gridReference:last').clone(),
								readonly = "",
								result = data1;
								$li = $('li',$row);
								$li.eq(0).html(result.cod);
								$li.eq(1).html(result.descr);
								$li.eq(2).html('<input type="text" size="8" padre="bruto_'+result.cuentas.padre+'" name="bruto_'+result._id.$id+'">');
								$li.eq(3).html('<input type="text" size="8" padre="depre_'+result.cuentas.padre+'" name="depre_'+result._id.$id+'">');
								$li.eq(4).html('<input type="text" size="8" padre="neto_'+result.cuentas.padre+'" name="neto_'+result._id.$id+'">');
								$row.find('[name^=bruto],[name^=depre],[name^=neto]').numeric().spinner({step: 0.1,min: 0});
								$row.find('.ui-button').css('height','14px');
								$row.find('[name=btnEliCuen]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a name="'+result.cod+'" id="'+result._id.$id+'" class="item" />');
								$row.find('a').data('data',result );
								p.$w.find(".gridBody:last").append( $row.children() );
							}
						}
					}};
					if(p.$w.find('[name=rbtnTipo]:checked').val()=='O') params.digit = 4;
					ctPcon.windowSelect(params);
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEliCuen]').live('click',function(){
					var cuencod = $(this).closest('.item').attr("name");
					p.$w.find('.gridBody [name^="'+cuencod+'"]').remove();
				});
				p.$w.find('[name^=monto_]').live('change',function(){
					var montoing = parseFloat($(this).val()),
					cuenpadre  = $(this).attr("padre"),
					hijos = p.$w.find('.gridBody:first [padre='+cuenpadre+']'),
					recalcular = 0;
					for(i=0;i<hijos.length;i++){
						//recalcular.push(hijos.eq(i).val());
						if(hijos.eq(i).val()=="") monto=0;
						else monto=hijos.eq(i).val();
						recalcular = parseFloat(monto) + recalcular;
					}
					p.$w.find('.gridBody:first [name='+cuenpadre+']').val(K.round(recalcular,2)).change();	
				});
				p.$w.find('[name^=bruto_]').live('change',function(){
					var cod = $(this).closest('.item').attr("name");
					var montoing = parseFloat($(this).val()),
					cuenpadre  = $(this).attr("padre"),
					hijos = p.$w.find('.gridBody:eq(1) [padre='+cuenpadre+']'),
					recalcular = 0;
					for(i=0;i<hijos.length;i++){
						//recalcular.push(hijos.eq(i).val());
						if(hijos.eq(i).val()=="") monto=0;
						else monto=hijos.eq(i).val();
						recalcular = parseFloat(monto) + recalcular;
					}					
					/** Sum Hor */
					var depre = p.$w.find('.gridBody:eq(1) [name^="'+cod+'"]').find('input:eq(1)').val();
					if(depre=="")depre=0;
					/** /Sum Hor */
					p.$w.find('.gridBody:eq(1) [name="'+cod+'"]').find('input:eq(2)').val(K.round(parseFloat(montoing)-parseFloat(depre),2));
					p.$w.find('.gridBody:eq(1) [name='+cuenpadre+']').val(K.round(recalcular,2)).change();	
				});
				p.$w.find('[name^=depre_]').live('change',function(){
					var cod = $(this).closest('.item').attr("name");
					var montoing = parseFloat($(this).val()),
					cuenpadre  = $(this).attr("padre"),
					hijos = p.$w.find('.gridBody:eq(1) [padre='+cuenpadre+']'),
					recalcular = 0;
					for(i=0;i<hijos.length;i++){
						//recalcular.push(hijos.eq(i).val());
						if(hijos.eq(i).val()=="") monto=0;
						else monto=hijos.eq(i).val();
						recalcular = parseFloat(monto) + recalcular;
					}
					/** Sum Hor */
					var bruto = p.$w.find('.gridBody:eq(1) [name^="'+cod+'"]').find('input:eq(0)').val();
					if(bruto=="")bruto=0;
					/** /Sum Hor */
					p.$w.find('.gridBody:eq(1) [name="'+cod+'"]').find('input:eq(2)').val(K.round(-parseFloat(montoing)+parseFloat(bruto),2));
					p.$w.find('.gridBody:eq(1) [name='+cuenpadre+']').val(K.round(recalcular,2)).change();	
				});
				p.$w.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month+1).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth())
			    .data('ano',ciHelper.date.getYear());
				p.$w.find('td:eq(7)').buttonset();
				p.$w.find('#rbtnTipo1').click(function(){
					p.$w.find('[name=tipoA]').show();
					p.$w.find('[name=tipoO]').hide();
				}).click();
				p.$w.find('#rbtnTipo2').click(function(){
					p.$w.find('[name=tipoA]').hide();
					p.$w.find('[name=tipoO]').show();
				});
				p.$w.find('[name=btnNum]').click(function(){
					ctNotaLit.windowSelect({callback: function(data){
						p.$w.find('[name=num]').html(data.num).data('data',data);
						p.$w.find('[name=nomb]').html(data.nomb);
					},mes:p.$w.find('[name=periodo]').data('mes'),ano:p.$w.find('[name=periodo]').data('ano')});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find(".gridBody:eq(0)").sortable({
					start : function(event, ui) {
						cod = ui.item.attr('name');
					    if (p.$w.find('.gridBody:eq(0)').find('[name^="'+cod+'"]').length > 0) {
					        first_rows = p.$w.find('.gridBody:eq(0)').find('[name^="'+cod+'"]').map(function(i, e) {
					            var $tr = $(e);
					            return {
					                tr : $tr.clone(true),
					                id : $tr.attr('id')
					            };	
					        }).get();
					        p.$w.find('.gridBody:eq(0)').find('[name^="'+cod+'"]').addClass('cloned');
					    }
					},
					stop : function(event, ui) {
					    if (first_rows.length > 1) {
					        $.each(first_rows, function(i, item) {
					            $(item.tr).removeAttr('style').insertBefore(ui.item);
					        });
					        p.$w.find('.gridBody:eq(0)').find('.cloned').remove();
					        first_rows = {};
					    }
					}
				});
				p.$w.find(".gridBody:eq(1)").sortable({
					start : function(event, ui) {
						cod = ui.item.attr('name');
					    if (p.$w.find('.gridBody:eq(1)').find('[name^="'+cod+'"]').length > 0) {
					        first_rows = p.$w.find('.gridBody:eq(1)').find('[name^="'+cod+'"]').map(function(i, e) {
					            var $tr = $(e);
					            return {
					                tr : $tr.clone(true),
					                id : $tr.attr('id')
					            };	
					        }).get();
					        p.$w.find('.gridBody:eq(1)').find('[name^="'+cod+'"]').addClass('cloned');
					    }
					},
					stop : function(event, ui) {
					    if (first_rows.length > 1) {
					        $.each(first_rows, function(i, item) {
					            $(item.tr).removeAttr('style').insertBefore(ui.item);
					        });
					        p.$w.find('.gridBody:eq(1)').find('.cloned').remove();
					        first_rows = {};
					    }
					}
				});
				p.$w.find('.grid:eq(0)').css('overflow','hidden');
				p.$w.find('.grid:eq(1)').scroll(function(){
					p.$w.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				p.$w.find('.grid:eq(2)').css('overflow','hidden');
				p.$w.find('.grid:eq(3)').scroll(function(){
					p.$w.find('.grid:eq(2)').scrollLeft($(this).scrollLeft());
				});
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowEditctNotaNum'+p.id,
			title: 'Editar Nota Numerica',
			contentURL: 'ct/nota/num_edit',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					data = {
						_id: p.id,
						tipo: p.$w.find('[name=rbtnTipo]:checked').val(),
						documento: p.$w.find('[name=documento] :selected').val(),
						clase: p.$w.find('[name=clase] :selected').val(),
						subclase: p.$w.find('[name=subclase] :selected').val()
					};
					if(data.tipo=='A'){
						data.activos = [];
						for(var i=0,j=p.$w.find('.gridBody:last .item').length; i<j; i++){
							var $row = p.$w.find('.gridBody:last .item').eq(i),
							cuenta = $row.data('data'),
							item = {
								cuenta: {
									_id: cuenta._id.$id,
									cod: cuenta.cod,
									descr: cuenta.descr
								},
								valor_bruto: $row.find('[name^=bruto]').val(),
								depreciacion: $row.find('[name^=depre]').val(),
								valor_neto: $row.find('[name^=neto]').val()
							};
							if(item.valor_bruto==''){
								$row.find('[name^=bruto]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el valor bruto!',type: 'error'});
							}
							/*if(item.depreciacion==''){
								$row.find('[name^=depre]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar la depreciaci&oacute;n!',type: 'error'});
							}*/
							if(item.valor_neto==''){
								$row.find('[name^=neto]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el valor neto!',type: 'error'});
							}
							data.activos.push(item);
						}
						if(data.activos.length==0){
							p.$w.find('[name=btnAddCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir al menos una cuenta!',type: 'error'});
						}
					}else{
						data.otros = [];
						for(var i=0;i<p.$w.find('.gridBody:first .item').length;i++){
							var monto = p.$w.find('.gridBody:first .item').eq(i).find('[name^=monto]').val();
							if(monto=="")monto="0";
							if(monto!="0"){
								var insertcuen = {
									cuenta:{
										_id:p.$w.find('.gridBody:first .item').eq(i).data('data')._id.$id,
										cod:p.$w.find('.gridBody:first .item').eq(i).find('li').eq(0).html(),
										descr:p.$w.find('.gridBody:first .item').eq(i).find('li').eq(1).html()
									},
									monto:monto
								};
								data.otros.push(insertcuen);
							}
						}
						if(data.otros.length==0){
							p.$w.find('[name=btnAddCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe elegir al menos una cuenta!',type: 'error'});
						}
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ct/nota/save_num',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La nota num&eacute;rica fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name^=monto_]').die('change');
				p.$w.find('[name=btnEliCuen]').die('click');
				p.$w.find('[name^=bruto_]').die('change');
				p.$w.find('[name^=depre_]').die('change');
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditctNotaNum'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnAddCuen]').click(function(){
					var params = {callback: function(data1){
						if(p.$w.find('[name=rbtnTipo]:checked').val()=='O'){
							$.post('ct/pcon/lista',{
								id:data1._id.$id,
								page_rows:100000,
								page:1,
								texto:""
							},function(data){
								if(data!=null){
									for(i=0;i<data.items.length;i++){
										if(p.$w.find(".gridBody:first [id="+data.items[i]._id.$id+"]").length<=0){
											var $row = p.$w.find('.gridReference:first').clone(),
											readonly = "",
											result = data.items[i];
											$li = $('li',$row);
											$li.eq(0).html(result.cod);
											$li.eq(1).html(result.descr);
											$li.eq(2).html('<input type="text" size="8" padre="monto_'+result.cuentas.padre+'" name="monto_'+result._id.$id+'">');
											$row.find('[name^=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }});
											$row.find('.ui-button').css('height','14px');										
											$row.find('[name=btnEliCuen]').button({icons: {primary: 'ui-icon-trash'},text: false});
											$row.wrapInner('<a name="'+result.cod+'" id="'+result._id.$id+'" class="item" />');
											$row.find('a').data('data',result );
											p.$w.find(".gridBody:first").append( $row.children() );
										}
									}
								}
							},'json');
						}else{
							if(p.$w.find(".gridBody:last [id="+data1._id.$id+"]").length<=0){
								var $row = p.$w.find('.gridReference:last').clone(),
								readonly = "",
								result = data1;
								$li = $('li',$row);
								$li.eq(0).html(result.cod);
								$li.eq(1).html(result.descr);
								$li.eq(2).html('<input type="text" size="8" padre="bruto_'+result.cuentas.padre+'" name="bruto_'+result._id.$id+'">');
								$li.eq(3).html('<input type="text" size="8" padre="depre_'+result.cuentas.padre+'" name="depre_'+result._id.$id+'">');
								$li.eq(4).html('<input type="text" size="8" padre="neto_'+result.cuentas.padre+'" name="neto_'+result._id.$id+'">');
								$row.find('[name^=bruto],[name^=depre],[name^=neto]').numeric().spinner({step: 0.1,min: 0});
								$row.find('.ui-button').css('height','14px');
								$row.find('[name=btnEliCuen]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a name="'+result.cod+'" id="'+result._id.$id+'" class="item" />');
								$row.find('a').data('data',result );
								p.$w.find(".gridBody:last").append( $row.children() );
							}
						}
					}};
					if(p.$w.find('[name=rbtnTipo]:checked').val()=='O') params.digit = 4;
					ctPcon.windowSelect(params);
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEliCuen]').live('click',function(){
					var cuencod = $(this).closest('.item').attr("name");
					p.$w.find('.gridBody [name^="'+cuencod+'"]').remove();
				});
				p.$w.find('[name^=monto_]').live('change',function(){
					var montoing = parseFloat($(this).val()),
					cuenpadre  = $(this).attr("padre"),
					hijos = p.$w.find('.gridBody:first [padre='+cuenpadre+']'),
					recalcular = 0;
					for(i=0;i<hijos.length;i++){
						//recalcular.push(hijos.eq(i).val());
						if(hijos.eq(i).val()=="") monto=0;
						else monto=hijos.eq(i).val();
						recalcular = parseFloat(monto) + recalcular;
					}
					p.$w.find('.gridBody:first [name='+cuenpadre+']').val(K.round(recalcular,2)).change();	
				});
				p.$w.find('[name^=bruto_]').live('change',function(){
					var cod = $(this).closest('.item').attr("name");
					var montoing = parseFloat($(this).val()),
					cuenpadre  = $(this).attr("padre"),
					hijos = p.$w.find('.gridBody:eq(1) [padre='+cuenpadre+']'),
					recalcular = 0;
					for(i=0;i<hijos.length;i++){
						//recalcular.push(hijos.eq(i).val());
						if(hijos.eq(i).val()=="") monto=0;
						else monto=hijos.eq(i).val();
						recalcular = parseFloat(monto) + recalcular;
					}					
					/** Sum Hor */
					var depre = p.$w.find('.gridBody:eq(1) [name^="'+cod+'"]').find('input:eq(1)').val();
					if(depre=="")depre=0;
					/** /Sum Hor */
					p.$w.find('.gridBody:eq(1) [name="'+cod+'"]').find('input:eq(2)').val(K.round(parseFloat(montoing)-parseFloat(depre),2));
					p.$w.find('.gridBody:eq(1) [name='+cuenpadre+']').val(K.round(recalcular,2)).change();	
				});
				p.$w.find('[name^=depre_]').live('change',function(){
					var cod = $(this).closest('.item').attr("name");
					var montoing = parseFloat($(this).val()),
					cuenpadre  = $(this).attr("padre"),
					hijos = p.$w.find('.gridBody:eq(1) [padre='+cuenpadre+']'),
					recalcular = 0;
					for(i=0;i<hijos.length;i++){
						//recalcular.push(hijos.eq(i).val());
						if(hijos.eq(i).val()=="") monto=0;
						else monto=hijos.eq(i).val();
						recalcular = parseFloat(monto) + recalcular;
					}
					/** Sum Hor */
					var bruto = p.$w.find('.gridBody:eq(1) [name^="'+cod+'"]').find('input:eq(0)').val();
					if(bruto=="")bruto=0;
					/** /Sum Hor */
					p.$w.find('.gridBody:eq(1) [name="'+cod+'"]').find('input:eq(2)').val(K.round(-parseFloat(montoing)+parseFloat(bruto),2));
					p.$w.find('.gridBody:eq(1) [name='+cuenpadre+']').val(K.round(recalcular,2)).change();	
				});
				p.$w.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth()-1)
			    .data('ano',ciHelper.date.getYear());
				p.$w.find('td:eq(7)').buttonset();
				p.$w.find('#rbtnTipo1').click(function(){
					p.$w.find('[name=tipoA]').show();
					p.$w.find('[name=tipoO]').hide();
				}).click();
				p.$w.find('#rbtnTipo2').click(function(){
					p.$w.find('[name=tipoA]').hide();
					p.$w.find('[name=tipoO]').show();
				});
				p.$w.find('[name=btnNum]').click(function(){
					ctNotaLit.windowSelect({callback: function(data){
						p.$w.find('[name=num]').html(data.num).data('data',data);
						p.$w.find('[name=nomb]').html(data.nomb);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('.grid:eq(0)').css('overflow','hidden');
				p.$w.find('.grid:eq(1)').scroll(function(){
					p.$w.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				p.$w.find('.grid:eq(2)').css('overflow','hidden');
				p.$w.find('.grid:eq(3)').scroll(function(){
					p.$w.find('.grid:eq(2)').scrollLeft($(this).scrollLeft());
				});
				p.$w.find(".gridBody:eq(0)").sortable({
					start : function(event, ui) {
						cod = ui.item.attr('name');
					    if (p.$w.find('.gridBody:eq(0)').find('[name^="'+cod+'"]').length > 0) {
					        first_rows = p.$w.find('.gridBody:eq(0)').find('[name^="'+cod+'"]').map(function(i, e) {
					            var $tr = $(e);
					            return {
					                tr : $tr.clone(true),
					                id : $tr.attr('id')
					            };	
					        }).get();
					        p.$w.find('.gridBody:eq(0)').find('[name^="'+cod+'"]').addClass('cloned');
					    }
					},
					stop : function(event, ui) {
					    if (first_rows.length > 1) {
					        $.each(first_rows, function(i, item) {
					            $(item.tr).removeAttr('style').insertBefore(ui.item);
					        });
					        p.$w.find('.gridBody:eq(0)').find('.cloned').remove();
					        first_rows = {};
					    }
					}
				});
				p.$w.find(".gridBody:eq(1)").sortable({
					start : function(event, ui) {
						cod = ui.item.attr('name');
					    if (p.$w.find('.gridBody:eq(1)').find('[name^="'+cod+'"]').length > 0) {
					        first_rows = p.$w.find('.gridBody:eq(1)').find('[name^="'+cod+'"]').map(function(i, e) {
					            var $tr = $(e);
					            return {
					                tr : $tr.clone(true),
					                id : $tr.attr('id')
					            };	
					        }).get();
					        p.$w.find('.gridBody:eq(1)').find('[name^="'+cod+'"]').addClass('cloned');
					    }
					},
					stop : function(event, ui) {
					    if (first_rows.length > 1) {
					        $.each(first_rows, function(i, item) {
					            $(item.tr).removeAttr('style').insertBefore(ui.item);
					        });
					        p.$w.find('.gridBody:eq(1)').find('.cloned').remove();
					        first_rows = {};
					    }
					}
				});
				/** Llenado de Data */
				$.post('ct/nota/get','id='+p.id,function(data){
					p.$w.find('[name=btnNum]').hide();
					p.$w.find('[name=num]').html(data.num);
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=documento]').selectVal(data.documento);
					p.$w.find('[name=clase]').selectVal(data.clase);
					p.$w.find('[name=subclase]').selectVal(data.subclase);
					if(data.tipo=="O"){
						p.$w.find('#rbtnTipo2').click();
						if(data.otros){
							for(i=0;i<data.otros.length;i++){
								var $row = p.$w.find('.gridReference:first').clone(),
								result = data.otros[i];
								$li = $('li',$row);
								$li.eq(0).html(result.cuenta.cod);
								$li.eq(1).html(result.cuenta.descr);
								$li.eq(2).html('<input type="text" size="8" padre="monto_'+result.cuenta.cuentas.padre+'" name="monto_'+result.cuenta._id.$id+'" value="'+result.monto+'">');
								$row.find('[name^=monto]').numeric().spinner({step: 0.1,min: 0,stop: function(){ $(this).change(); }});
								$row.find('.ui-button').css('height','14px');										
								$row.find('[name=btnEliCuen]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a name="'+result.cuenta.cod+'" id="'+result.cuenta._id.$id+'" class="item" />');
								$row.find('a').data('data',result.cuenta );
								p.$w.find(".gridBody:first").append( $row.children() );
							}
						}				
					}else{
						if(data.activos){
							for(i=0;i<data.activos.length;i++){
								var $row = p.$w.find('.gridReference:last').clone(),
								result = data.activos[i];
								$li = $('li',$row);
								$li.eq(0).html(result.cuenta.cod);
								$li.eq(1).html(result.cuenta.descr);
								$li.eq(2).html('<input type="text" size="8" padre="bruto_'+result.cuenta.cuentas.padre+'" name="bruto_'+result.cuenta._id.$id+'" value="'+result.valor_bruto+'">');
								$li.eq(3).html('<input type="text" size="8" padre="depre_'+result.cuenta.cuentas.padre+'" name="depre_'+result.cuenta._id.$id+'" value="'+result.depreciacion+'">');
								$li.eq(4).html('<input type="text" size="8" padre="neto_'+result.cuenta.cuentas.padre+'" name="neto_'+result.cuenta._id.$id+'" value="'+result.valor_neto+'">');
								$row.find('[name^=bruto],[name^=depre],[name^=neto]').numeric().spinner({step: 0.1,min: 0});
								$row.find('.ui-button').css('height','14px');
								$row.find('[name=btnEliCuen]').button({icons: {primary: 'ui-icon-trash'},text: false});
								$row.wrapInner('<a name="'+result.cuenta.cod+'" id="'+result.cuenta._id.$id+'" class="item" />');
								$row.find('a').data('data',result.cuenta );
								p.$w.find(".gridBody:last").append( $row.children() );
							}
						}
						
					}
					K.unblock({$element: p.$w});
				},'json');
				/** /Llenado de Data */		
			}
		});
	}
};