/*******************************************************************************
cuadro de asignacion */
peCuad = {
	states: {
		P: {
			descr: "Pendiente",
			color: "#CCCCCC"
		},
		A: {
			descr: "Aprobado",
			color: "#006532"
		},
		V: {
			descr: "Vigente",
			color: "#247AC4"
		},
		C: {
			descr: "Caducado",
			color: "#000"
		}
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peCuad',
			titleBar: {
				title: 'Cuadro de Asignaci&oacute;n'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/cuad',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el periodo de presupuesto' ).width('250');
				$mainPanel.find('[name=obj]').html( 'cuadro(s) de asignaci&oacute;n' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					peCuad.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnConso]').click(function(){
					peCuad.windowSelectPeriodo();
				}).button({icons: {primary: 'ui-icon-document'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						peCuad.loadData({page: 1,url: 'pe/cuad/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						peCuad.loadData({page: 1,url: 'pe/cuad/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				peCuad.loadData({page: 1,url: 'pe/cuad/lista'});
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
					$li.eq(0).css('background',peCuad.states[result.estado].color).addClass('vtip').attr('title',peCuad.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.periodo );
					$li.eq(3).html( result.organizacion.nomb );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						peCuad.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).data('orga',result.organizacion._id.$id).contextMenu("conMenPeCuad", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenPeCuad_vig',menu).remove();
								if(K.tmp.data('estado')=='P') $('#conMenPeCuad_vig',menu).remove();
								else if(K.tmp.data('estado')=='A') $('#conMenPeCuad_edi,#conMenPeCuad_apr',menu).remove();
								else $('#conMenPeCuad_vig,#conMenPeCuad_edi,#conMenPeCuad_apr',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeCuad_ver': function(t) {
									peCuad.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenPeCuad_edi': function(t) {
									peCuad.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenPeCuad_apr': function(t) {
									K.sendingInfo();
									$.post('pe/cuad/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
										K.clearNoti();
										K.notification({title: 'CAP Aprobado',text: 'La aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								}/*,
								'conMenPeCuad_vig': function(t) {
									K.sendingInfo();
									$.post('pe/cuad/vigente',{
										_id: K.tmp.data('id'),
										orga: K.tmp.data('orga')
									},function(){
										K.clearNoti();
										$('#pageWrapperLeft .ui-state-highlight').click();
										K.notification({title: 'CAP en Vigencia',text: 'El CAP seleccionado ahora est&aacute; vigente!'});
									});
								}*/
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
						peCuad.loadData(params);
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
	windowNew: function(p){
		if(p==null) p = {};
		$.extend(p,{
			calcRow: function($row){
				var sin = ($row.find('[name=sin]').val()=='') ? 0 : parseFloat($row.find('[name=sin]').val()),
				con = ($row.find('[name=con]').val()=='') ? 0 : parseFloat($row.find('[name=con]').val());
				$row.find('li:eq(4)').html(sin+con);
			}
		});
		new K.Window({
			id: 'windowEditCuad',
			title: 'Nuevo Cuadro de Asignaci&oacute;n de Personal',
			contentURL: 'pe/cuad/edit',
			icon: 'ui-icon-document',
			width: 800,
			height: 390,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						periodo: p.$w.find('[name=periodo]').val(),
						items: []
					};
					var tmp = p.$w.find('[name=orga]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							componente: {
								_id: tmp.componente._id.$id,
								nomb: tmp.componente.nomb,
								cod: tmp.componente.cod
							},
							actividad: {
								_id: tmp.actividad._id.$id,
								nomb: tmp.actividad.nomb,
								cod: tmp.actividad.cod
							}
						};
					}
					$.post('pe/cuad/veri',{
						peri: data.periodo,
						orga: data.organizacion._id
					},function(rpta){
						if(rpta!=null){
							peCuad.windowDetails({id: rpta._id.$id,nomb: rpta.organizacion.nomb,modal: true});
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ya existe un Cuadro de Asignaci&oacute;n de Personal para '+rpta.organizacion.nomb+'!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('.gridBody .item').length; i++){
							var $row = p.$w.find('.gridBody .item').eq(i);
							var item = {
								item: i+1,
								con_presupuesto: $row.find('[name=con]').val(),
								sin_presupuesto: $row.find('[name=sin]').val(),
								observ: $row.find('[name=obs]').val()
							};
							var tmp = $row.find('[name=btnCar]').data('data');
							if(tmp==null){
								$row.find('[name=btnCar]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cargo!',type: 'error'});
							}else{
								item.cargo_clasif = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									cod: tmp.cod
								};
							}
							var tmp = $row.find('[name=btnCarEst]').data('data');
							if(tmp==null){
								$row.find('[name=btnCarEst]').click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un cargo estructurado!',
									type: 'error'
								});
							}else{
								item.cargo = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									cod: tmp.cod
								};
							}
							var tmp = $row.find('[name=btnGrup]').data('data');
							if(tmp==null){
								$row.find('[name=btnGrup]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un grupo ocupacional!',type: 'error'});
							}else{
								item.grupo_ocu = {
									_id: tmp._id.$id,
									nomb: tmp.nomb,
									sigla: tmp.sigla
								};
							}
							if(item.con_presupuesto==''&&item.sin_presupuesto==''){
								$row.find('[name=con]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad con o sin presupuesto!',type: 'error'});
							}else if(item.con_presupuesto=='') item.con_presupuesto = 0;
							else if(item.sin_presupuesto=='') item.sin_presupuesto = 0;
							if(item.observ==''){
								/*$row.find('[name=obs]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una observaci&oacute;n!',type: 'error'});*/
							}
							data.items.push(item);
						}
						K.sendingInfo();
						p.$w.find('.ui-dialog-buttonpane button').button('disable');
						$.post('pe/cuad/save',data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El CAP fue registrado con &eacute;xito!'});
							$('#pageWrapperLeft .ui-state-highlight').click();
						});
					},'json');
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnCar]').die('click');
				p.$w.find('[name=btnCarEst]').die('click');
				p.$w.find('[name=btnGrup]').die('click');
				p.$w.find('[name=btnAgre]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditCuad');
				p.$w.find('[name=periodo]').val(ciHelper.date.getYear()).numeric().spinner({step: 1,min: 1900,max: 2100});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCar]').live('click',function(){
					var $this = $(this);
					peClas.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.nomb);
						$this.closest('.item').find('li:eq(2)').html(data.cod);
					}});
				});
				p.$w.find('[name=btnCarEst]').live('click',function(){
					var $this = $(this);
					peCarg.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.nomb);
						$this.closest('.item').find('li:eq(4)').html(data.cod);
					}});
				});
				p.$w.find('[name=btnGrup]').live('click',function(){
					var $this = $(this);
					peGrup.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.sigla);
					}});
				});
				p.$w.find('[name=btnAgre]').live('click',function(){
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html(p.$w.find('.gridBody .item').length+1);
					$row.find('[name=con],[name=sin]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('.ui-button').css('height','14px');
					$row.find('[name=btnCar],[name=btnCarEst],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody").append( $row.children() );
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('.gridBody .item').length<1){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html('1');
						$row.find('[name=con],[name=sin]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnCarEst],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}else{
						for(var i=0; i<p.$w.find('.gridBody .item').length; i++){
							p.$w.find('.gridBody .item').eq(i).find('li:first').html(i+1);
						}
					}
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var $row = p.$w.find('.gridReference').clone();
				$row.find('li:eq(0)').html('1');
				$row.find('[name=con],[name=sin]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
					p.calcRow($(this).closest('.item'));
				}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
				$row.find('.ui-button').css('height','14px');
				$row.find('[name=btnCar],[name=btnCarEst],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$w.find(".gridBody").append( $row.children() );
				p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
			}
		});
	},
	windowEdit: function(p){
		$.extend(p,{
			calcRow: function($row){
				var sin = ($row.find('[name=sin]').val()=='') ? 0 : parseFloat($row.find('[name=sin]').val()),
				con = ($row.find('[name=con]').val()=='') ? 0 : parseFloat($row.find('[name=con]').val());
				$row.find('li:eq(4)').html(sin+con);
			}
		});
		new K.Window({
			id: 'windowEditCuad'+p.id,
			title: 'Editar CAP: '+p.nomb,
			contentURL: 'pe/cuad/edit',
			icon: 'ui-icon-pencil',
			width: 800,
			height: 415,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						periodo: p.$w.find('[name=periodo]').val(),
						items: []
					};
					var tmp = p.$w.find('[name=orga]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = {
							_id: tmp._id.$id,
							nomb: tmp.nomb,
							componente: {
								_id: tmp.componente._id.$id,
								nomb: tmp.componente.nomb,
								cod: tmp.componente.cod
							},
							actividad: {
								_id: tmp.actividad._id.$id,
								nomb: tmp.actividad.nomb,
								cod: tmp.actividad.cod
							}
						};
					}
					for(var i=0; i<p.$w.find('.gridBody .item').length; i++){
						var $row = p.$w.find('.gridBody .item').eq(i);
						var item = {
							item: i+1,
							con_presupuesto: $row.find('[name=con]').val(),
							sin_presupuesto: $row.find('[name=sin]').val(),
							observ: $row.find('[name=obs]').val()
						};
						var tmp = $row.find('[name=btnCar]').data('data');
						if(tmp==null){
							$row.find('[name=btnCar]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un cargo!',type: 'error'});
						}else{
							item.cargo_clasif = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								cod: tmp.cod
							};
						}
						var tmp = $row.find('[name=btnCarEst]').data('data');
						if(tmp==null){
							$row.find('[name=btnCarEst]').click();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un cargo estructurado!',
								type: 'error'
							});
						}else{
							item.cargo = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								cod: tmp.cod
							};
						}
						var tmp = $row.find('[name=btnGrup]').data('data');
						if(tmp==null){
							$row.find('[name=btnGrup]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un grupo ocupacional!',type: 'error'});
						}else{
							item.grupo_ocu = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								sigla: tmp.sigla
							};
						}
						if(item.con_presupuesto==''&&item.sin_presupuesto==''){
							$row.find('[name=con]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad con o sin presupuesto!',type: 'error'});
						}else if(item.con_presupuesto=='') item.con_presupuesto = 0;
						else if(item.sin_presupuesto=='') item.sin_presupuesto = 0;
						if(item.observ==''){
							/*$row.find('[name=obs]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una observaci&oacute;n!',type: 'error'});*/
						}
						data.items.push(item);
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/cuad/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El PAP fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnCar]').die('click');
				p.$w.find('[name=btnCarEst]').die('click');
				p.$w.find('[name=btnNiv]').die('click');
				p.$w.find('[name=btnGrup]').die('click');
				p.$w.find('[name=btnAgre]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditCuad'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=periodo]').val(ciHelper.date.getYear()).numeric().spinner({step: 1,min: 1900,max: 2100});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				p.$w.find('[name=btnCar]').live('click',function(){
					var $this = $(this);
					peClas.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.nomb);
						$this.closest('.item').find('li:eq(2)').html(data.cod);
					}});
				});
				p.$w.find('[name=btnCarEst]').live('click',function(){
					var $this = $(this);
					peCarg.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.nomb);
						$this.closest('.item').find('li:eq(2)').html(data.cod);
					}});
				});
				p.$w.find('[name=btnGrup]').live('click',function(){
					var $this = $(this);
					peGrup.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.sigla);
					}});
				});
				p.$w.find('[name=btnAgre]').live('click',function(){
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html(p.$w.find('.gridBody .item').length+1);
					$row.find('[name=con],[name=sin]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('.ui-button').css('height','14px');
					$row.find('[name=btnCar],[name=btnCarEst],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody").append( $row.children() );
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('.gridBody .item').length<1){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html('1');
						$row.find('[name=con],[name=sin]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnCarEst],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}else{
						for(var i=0; i<p.$w.find('.gridBody .item').length; i++){
							p.$w.find('.gridBody .item').eq(i).find('li:first').html(i+1);
						}
					}
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				$.post('pe/cuad/get','id='+p.id,function(data){
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=periodo]').val(data.periodo);
					for(var i=0; i<data.items.length; i++){
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(result.item);
						$row.find('[name=btnCar]').data('data',result.cargo_clasif);
						$row.find('li:eq(1) span').html(result.cargo_clasif.nomb);
						$row.find('li:eq(2)').html(result.cargo_clasif.cod);
						$row.find('[name=btnCarEst]').data('data',result.cargo);
						$row.find('li:eq(3) span').html(result.cargo.nomb);
						$row.find('li:eq(4)').html(result.cargo.cod);
						$row.find('[name=btnGrup]').data('data',result.grupo_ocu);
						$row.find('li:eq(5) span').html(result.grupo_ocu.sigla);
						$row.find('[name=con],[name=sin]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnCarEst],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=sin]').val(result.sin_presupuesto);
						$row.find('[name=con]').val(result.con_presupuesto);
						$row.find('[name=obs]').val(result.observ);
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
			        	p.calcRow(p.$w.find('.gridBody .item:last'));
					}
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		var window = {
			id: 'windowDetailsCuad'+p.id,
			title: 'CAP '+p.nomb,
			contentURL: 'pe/cuad/details',
			icon: 'ui-icon-document',
			width: 800,
			height: 390,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsCuad'+p.id);
				K.block({$element: p.$w});
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				$.post('pe/cuad/get','id='+p.id,function(data){
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=periodo]').html(data.periodo);
					for(var i=0; i<data.items.length; i++){
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(result.item);
						$row.find('li:eq(1)').html(result.cargo_clasif.nomb);
						$row.find('li:eq(2)').html(result.cargo_clasif.cod);
						$row.find('li:eq(3)').html(result.cargo.nomb);
						$row.find('li:eq(4)').html(result.cargo.cod);
						$row.find('li:eq(5)').html(result.grupo_ocu.sigla);
						$row.find('li:eq(6)').html(parseFloat(result.con_presupuesto)+parseFloat(result.sin_presupuesto));
						$row.find('li:eq(7)').html(result.con_presupuesto);
						$row.find('li:eq(8)').html(result.sin_presupuesto);
						$row.find('li:eq(9)').html(result.observ);
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		};
		if(p.modal==true) new K.Modal(window);
		else new K.Window(window);
	},
	windowSelectPeriodo: function(p){
		p = {};
		new K.Modal({
			id: 'windowSelectPeriodo',
			title: 'Seleccionar Periodo',
			contentURL: 'pe/cuad/periodo',
			icon: 'ui-icon-document',
			width: 220,
			height: 40,
			buttons: {
				"Seleccionar": function(){
					peCuad.windowConso({periodo: p.$w.find('[name=periodo]').val()});
					K.closeWindow(p.$w.attr('id'));
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectPeriodo');
				p.$w.find('[name=periodo]').numeric().spinner({step: 1}).val(ciHelper.date.getYear());
				p.$w.find('.ui-button').height('14px');
			}
		});
	},
	windowConso: function(p){
		new K.Window({
			id: 'windowConsoCuad',
			title: 'Consolidado Cuadro de Asignaci&oacute;n de Personal',
			contentURL: 'pe/cuad/conso',
			icon: 'ui-icon-document',
			width: 810,
			height: 400,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowConsoCuad');
				K.block({$element: p.$w});
		    	$.post('pe/cuad/get_conso','periodo='+p.periodo,function(pres){
		    		if(pres==null){
		    			K.unblock({$element: p.$w});
		    			K.closeWindow(p.$w.attr('id'));
		    			return K.notification({title: 'CAP no registrado',text: 'El periodo seleccionado no tiene CAP registrados!',type: 'info',icon: 'ui-icon-calendar'});
		    		}
		    		for(var j=0; j<pres.length; j++){
		    			var data = pres[j];
		    			var $trab = p.$w.find('[name=ref]').clone();
		    			$trab.find('.grid').eq(1).bind('scroll',function(){
		    				$this = $(this);
							$this.closest('fieldset').find('.grid').eq(0).scrollLeft($this.closest('fieldset').find('.grid').eq(1).scrollLeft());
						});
		    			$trab.find('[name=orga]').html(data.organizacion.nomb);
		    			$trab.find('[name=periodo]').html(data.periodo);
						for(var i=0; i<data.items.length; i++){
							var result = data.items[i];
							var $row = $trab.find('.gridReference').clone();
							var $li = $('li',$row);
							$row.find('li:eq(0)').html(result.item);
							$row.find('li:eq(1)').html(result.cargo_clasif.nomb);
							$row.find('li:eq(2)').html(result.cargo_clasif.cod);
							$row.find('li:eq(3)').html(result.cargo.nomb);
							$row.find('li:eq(4)').html(result.cargo.cod);
							$row.find('li:eq(5)').html(result.grupo_ocu.sigla);
							$row.find('li:eq(6)').html(parseFloat(result.con_presupuesto)+parseFloat(result.sin_presupuesto));
							$row.find('li:eq(7)').html(result.con_presupuesto||0);
							$row.find('li:eq(8)').html(result.sin_presupuesto||0);
							$row.find('li:eq(9)').html(result.observ);
							$row.wrapInner('<a class="item" />');
				        	$trab.find(".gridBody").append( $row.children() );
						}
						p.$w.find('[name=pres]').append($trab.children());
		    		}
					K.unblock({$element: p.$w});
		    	},'json');
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.estado = 'A';
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('pe/cuad/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.organizacion.nomb );
						$li.eq(1).html( result.periodo );
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						$row.find('a').data('id',result._id.$id).dblclick(function(){
							p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
						}).data('data',result);
						p.$w.find(".gridBody").append( $row.children() );
					}
					p.$w.find('[name=showing]').html( p.$w.find(".gridBody a").length );
					p.$w.find('[name=founded]').html( data.paging.total_items );
					
					$moreresults = p.$w.find("[name=moreresults]").unbind();
					if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
						$moreresults.click( function(){
							params.page = parseFloat(data.paging.page) + 1;
							p.search( params );
							//$(this).button( "option", "disabled", true );
						});
						$moreresults.button( "option", "disabled", false );
					}else
						$moreresults.button( "option", "disabled", true );
				} else {
					p.$w.find("[name=moreresults]").button( "option", "disabled", true );
					$('[name=showing]').html( 0 );
					$('[name=founded]').html( data.paging.total_items );
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowSelectCuad',
			title: 'Seleccionar Cuadro de Asignaci&oacute;n',
			contentURL: 'pe/cuad/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Cuadro de Asignaci&oacute;n!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectCuad');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('320px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.search({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	}
};