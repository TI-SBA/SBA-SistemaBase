/*******************************************************************************
Ordenes de Compra: todos */
ctOrdeComp = {
	states: {
		P:{descr:'Pendiente',color:'gray'},
		A:{descr:'Aprobado',color:'green'}
	},
	init: function(){
		if($('#pageWrapper [child=orde]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ct/navg/orde',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="orde" />');
					$p.find("[name=ctOrde]").after( $row.children() );
				}
				$p.find('[name=ctOrde]').data('orde',$('#pageWrapper [child=orde]:first').data('orde'));
				$p.find('[name=ctOrdeComp]').click(function(){ ctOrdeComp.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctOrdeServ]').click(function(){ ctOrdeServ.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctOrdeComp',
			titleBar: {
				title: '&Oacute;rdenes de Compra: Aprobadas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'lg/orde/todo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de orden' ).width('250');
				$mainPanel.find('[name=obj]').html( 'orden(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').remove();
				$mainPanel.find('[name=btnAgregarCerti]').remove();
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						ctOrdeComp.loadData({page: 1,url: 'lg/orde/lista_cont'});
					}else{
						$("#mainPanel .gridBody").empty();
						ctOrdeComp.loadData({page: 1,url: 'lg/orde/search',tipo: 'C'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				ctOrdeComp.loadData({page: 1,url: 'lg/orde/lista_cont'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',ctOrdeComp.states[result.estado_cont].color).addClass('vtip').attr('title',ctOrdeComp.states[result.estado_cont].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( 'Orden de Compra N&deg;'+result.cod );
					if(result.proveedor.tipo_enti=='E') $li.eq(3).html( result.proveedor.nomb );
					else $li.eq(3).html( result.proveedor.nomb+' '+result.proveedor.appat+' '+result.proveedor.apmat );
					$li.eq(4).html( result.almacen.nomb );
					$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result).dblclick(function(){
						ctOrdeComp.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).contextMenu("conMenCtOrde", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								//$('#conMenLgOrde_edi,#conMenLgOrde_con,#conMenLgOrde_rev,#conMenLgOrde_fin',menu).remove();
								if(K.tmp.data('data').estado_cont=="A"){
									$('#conMenCtOrde_edi,#conMenCtOrde_apr',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenCtOrde_ver': function(t) {
									ctOrdeComp.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html(),read:true});
								},
								'conMenCtOrde_edi': function(t) {
									ctOrdeComp.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
								},
								'conMenCtOrde_apr': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de generar auxiliares de cuentas?',
										function () {
											K.sendingInfo();
											$.post('lg/orde/apro_cont',{_id:K.tmp.data('id')},function(){
												K.clearNoti();
												K.notification({title: ciHelper.titleMessages.regiGua,text: 'Los auxiliares standard fueron generados con &eacute;xito!'});
												ctOrdeComp.init();
											});
										},
										function () {
											//nothing
										}										
									);	
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
						ctOrdeComp.loadData(params);
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
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsOrde'+p.id,
			title: p.nomb,
			contentURL: 'lg/orde/edit_cont',
			icon: 'ui-icon-document',
			width: 785,
			height: 470,
			buttons: {
				"Imprimir": function(){
					var url = 'lg/repo/orde?id='+p.id;
					K.windowPrint({
						id:'windowLgOrdePrint',
						title: p.nomb,
						url: url
					});
				},
				"Guardar": function(){
					var data = {};
					data._id = p.id;
					data.auxs = new Array;
					if(p.$w.find('.gridBody:eq(2) .item').length){
						for(i=0;i<p.$w.find('.gridBody:eq(2) .item').length;i++){
							var $row = p.$w.find('.gridBody:eq(2) .item').eq(i);
							var item = {
								monto:$row.find('[name=monto]').val(),
								tipo:$row.find('[name=tipo] :selected').val(),
								saldo:$row.find('[name=saldo] :selected').val()
							};
							if($row.find('[name=orga]').data('data')!=null){
								item.organizacion = {
									_id:$row.find('[name=orga]').data('data')._id.$id,
									nomb:$row.find('[name=orga]').data('data').nomb,
									actividad:{
										_id:$row.find('[name=orga]').data('data').actividad._id.$id,
										cod:$row.find('[name=orga]').data('data').actividad.cod,
										nomb:$row.find('[name=orga]').data('data').actividad.nomb
									},
									componente:{
										_id:$row.find('[name=orga]').data('data').componente._id.$id,
										cod:$row.find('[name=orga]').data('data').componente.cod,
										nomb:$row.find('[name=orga]').data('data').componente.nomb
									}
								};
							}
							if($row.find('[name=cuen]').data('data')!=null){
								item.cuenta = {
									_id:$row.find('[name=cuen]').data('data')._id.$id,
									cod:$row.find('[name=cuen]').data('data').cod,
									descr:$row.find('[name=cuen]').data('data').descr,
									tipo:$row.find('[name=cuen]').data('data').tipo
								};
							}else{
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Es necesario seleccionar una cuenta contable!',type: 'error'});
							}
							if(item.monto==""){
								K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
								return $row.find('[name=monto]').focus();
							}
							data.auxs.push(item);
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('lg/orde/save_cont',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La informacion se ha guardado con &eacute;xito!'});
						ctOrdeComp.init();
					});
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose:function(){
				p.$w.find('[name=btnSelOrga]').die('click');
				p.$w.find('[name=btnSelCuen]').die('click');
				p.$w.find('[name=btnEli]').die('click');
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsOrde'+p.id);
				if(p.read){
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').eq(1).button('disable');
					p.$w.find('[name=btnAddRow]').remove();
					p.$w.find('[name=btnSelOrga]').remove();
					p.$w.find('[name=btnSelCuen]').remove();
					p.$w.find('[name=btnEli]').remove();
					p.$w.find('[name=tipo]').attr('disabled','disabled');
					p.$w.find('[name=saldo]').attr('disabled','disabled');
				}
				K.block({$element: p.$w});
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$w.find('.grid').eq(3).bind('scroll',function(){
					p.$w.find('.grid').eq(2).scrollLeft(p.$w.find('.grid').eq(3).scrollLeft());
				});
				//p.$w.find('[name=tabs]').tabs();
				//p.$w.find('[name=rpta]').buttonset();
				p.$w.find('[name=btnSelOrga]').live('click',function(){
					var $this = $(this).closest('.item');
					ciSearch.windowSearchOrga({callback: function(data){
						$this.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name=btnSelCuen]').live('click',function(){
					var $this = $(this).closest('.item');
					ctPcon.windowSelect({callback: function(data){
						$this.find('[name=cuen]').html(data.cod).data('data',data);
					}});
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
				});
				p.$w.find('[name=btnAddRow]').click(function(){
					var $row = p.$w.find('.gridReference:eq(2)').clone();
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text:false});
					$row.find('[name=btnSelOrga]').button({icons: {primary: 'ui-icon-search'},text:false});
					$row.find('[name=btnSelCuen]').button({icons: {primary: 'ui-icon-search'},text:false});
					$row.wrapInner('<a class="item"/>');
					p.$w.find('.gridBody:eq(2)').append($row.children());
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$.post('lg/orde/get','id='+p.id,function(data){
					p.$w.find('#tabs-1,#tabs-2,fieldset').css('padding','0px');
					p.$w.find('#tabs-1,#tabs-2').css('height','425px').css('overflow','auto');
					p.$w.find('[name=cod]').html('Orden de Compra N&deg;'+data.cod);
					p.$w.find('[name=estado]').html(lgOrde.states[data.estado].descr).css('color',lgOrde.states[data.estado].color);
					p.$w.find('[name=trabajador]').html(ciHelper.enti.formatName(data.trabajador));
					p.$w.find('[name=fecreg]').html(ciHelper.dateFormat(data.fecreg));
					p.$w.find('[name=proveedor]').html(ciHelper.enti.formatName(data.proveedor)).click(function(){
						ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
					}).data('id',data.proveedor._id.$id).data('tipo_enti',data.proveedor);
					p.$w.find('[name=ref]').html(data.ref);
					p.$w.find('[name=observv]').html(data.observ);
					p.$w.find('[name=fuente]').html(data.fuente.cod+' - '+data.fuente.rubro);
					for(var i=0; i<data.productos.length; i++){
						var $row = p.$w.find('.gridReference:eq(0)').clone();
						$li = $('li',$row);
						$li.eq(0).html(data.productos[i].item);
						$li.eq(1).html(data.productos[i].producto.clasif.cod);
						$li.eq(2).html(data.productos[i].producto.clasif.nomb);
						$li.eq(3).html(data.productos[i].producto.cod);
						var $ul = $('<ul><li style="min-width: 305px;max-width: 305px;"><b>'+data.productos[i].producto.nomb+'</b></li></ul>');
						$li.eq(4).append($ul);
						for(var j=0; j<data.productos[i].asignacion.length; j++){
							var $ul = $('<ul><li>'+data.productos[i].asignacion[j].organizacion.nomb+': cant. '+data.productos[i].asignacion[j].cantidad+' S/.'+data.productos[i].asignacion[j].monto+'</i></li></ul>');
							$li.eq(4).append($ul);
						}
						$li.eq(5).html(data.productos[i].producto.unidad.nomb);
						$li.eq(6).html(data.productos[i].cant);
						$li.eq(7).html(ciHelper.formatMon(data.productos[i].precio));
						$li.eq(8).html(ciHelper.formatMon(data.productos[i].subtotal));
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody:eq(0)").append( $row.children() );
					}
					var $row = p.$w.find('.gridReference:eq(0)').clone();
					$row.find('li').html('');
					$row.find('li:eq(6)').css('min-width','70px').css('max-width','70px');
					$row.find('li:eq(7)').html('Total').addClass('ui-state-default ui-button-text-only');
					$row.find('li:eq(8)').html(ciHelper.formatMon(data.precio_total)).css('padding-left','10px');
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody:eq(0)").append( $row.children() );
		        	for(var i=0; i<data.afectacion.length; i++){
						var $row = p.$w.find('.gridReference:eq(1)').clone();
						$li = $('li',$row);
						$li.eq(0).html(data.afectacion[i].organizacion.nomb);
						if(data.afectacion[i].organizacion.actividad!=null) $li.eq(1).html(data.afectacion[i].organizacion.actividad.cod);
						if(data.afectacion[i].organizacion.componente!=null) $li.eq(2).html(data.afectacion[i].organizacion.componente.cod);
						var tot = 0;
						for(var j=0; j<data.afectacion[i].gasto.length; j++){
							tot = tot + parseFloat(data.afectacion[i].gasto[j].monto);
						}
						$li.eq(3).html(ciHelper.formatMon(tot));
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody:eq(1)").append( $row.children() );
					}
					p.$w.find('[name=almacen]').html(data.almacen.local.direccion);
					p.$w.find('[name=fecent]').html(ciHelper.dateFormat(data.fecent));
					var $rev = p.$w.find('#tabs-2');
					var $table = $rev.find('table:last');
					$table.find('span:eq(0)').html(data.trabajador.cargo.organizacion.nomb);
					if(data.trabajador.tipo_enti=='E') $table.find('span:eq(2)').html(data.trabajador.nomb);
					else $table.find('span:eq(2)').html(data.trabajador.nomb+' '+data.trabajador.appat+' '+data.trabajador.apmat);
					$table.find('span:eq(2)').click(function(){
						ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
					}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
					$table.find('span:eq(3)').html(ciHelper.dateFormat(data.fecreg));
					if(data.recepcion!=null){
						var $tab = p.$w.find('#tabs-1 fieldset:last table');
						$tab.append('<tr><td>&nbsp;</td><td>&nbsp;</td><td><label>Fecha de entrega ejecutada</label></td><td><span>'+ciHelper.dateFormat(data.recepcion.fec)+'</span></td></tr>');
						$tab.append('<tr><td><label>Observaciones</label></td><td><span>'+data.recepcion.observ+'</span></td></tr>');
					}
					if(data.auxs){
						for(i=0;i<data.auxs.length;i++){
							var $row = p.$w.find('.gridReference:eq(2)').clone();
							$row.find('[name=orga]').html(data.auxs[i].organizacion.nomb).data('data',data.auxs[i].organizacion);
							$row.find('[name=cuen]').html(data.auxs[i].cuenta.cod).data('data',data.auxs[i].cuenta);
							$row.find('[name=monto]').val(data.auxs[i].monto);
							$row.find('[name=tipo]').find('[value='+data.auxs[i].tipo+']').attr('selected','selecred');
							$row.find('[name=saldo]').find('[value='+data.auxs[i].saldo+']').attr('selected','selecred');
							$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text:false});
							$row.find('[name=btnSelOrga]').button({icons: {primary: 'ui-icon-search'},text:false});
							$row.find('[name=btnSelCuen]').button({icons: {primary: 'ui-icon-search'},text:false});
							$row.wrapInner('<a class="item"/>');
							p.$w.find('.gridBody:eq(2)').append($row.children());
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};