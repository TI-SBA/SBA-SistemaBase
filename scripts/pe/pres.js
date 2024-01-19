/*******************************************************************************
presupuestos analiticos */
pePres = {
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
			action: 'pePres',
			titleBar: {
				title: 'Presupuestos Anal&iacute;ticos de Personal'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/pres',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el periodo de presupuesto' ).width('250');
				$mainPanel.find('[name=obj]').html( 'presupuesto(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					peCuad.windowSelect({callback: function(data){
						pePres.windowNew({cap: data});
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnConso]').click(function(){
					pePres.windowSelectPeriodo();
				}).button({icons: {primary: 'ui-icon-document'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						pePres.loadData({page: 1,url: 'pe/pres/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						pePres.loadData({page: 1,url: 'pe/pres/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				pePres.loadData({page: 1,url: 'pe/pres/lista'});
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
					$li.eq(0).css('background',pePres.states[result.estado].color).addClass('vtip').attr('title',pePres.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.periodo );
					$li.eq(3).html( result.organizacion.nomb );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						pePres.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).data('orga',result.organizacion._id.$id).contextMenu("conMenPePres", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('estado')=='P') $('#conMenPePres_vig',menu).remove();
								else if(K.tmp.data('estado')=='A') $('#conMenPePres_edi,#conMenPePres_apr',menu).remove();
								else $(',#conMenPePres_vig,#conMenPePres_edi,#conMenPePres_apr',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPePres_ver': function(t) {
									pePres.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenPePres_edi': function(t) {
									pePres.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenPePres_apr': function(t) {
									K.sendingInfo();
									$.post('pe/pres/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
										K.clearNoti();
										K.notification({title: 'PAP Aprobado',text: 'La aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenPePres_vig': function(t) {
									K.sendingInfo();
									$.post('pe/pres/vigente',{
										_id: K.tmp.data('id'),
										orga: K.tmp.data('orga')
									},function(){
										K.clearNoti();
										$('#pageWrapperLeft .ui-state-highlight').click();
										K.notification({title: 'PAP en Vigencia',text: 'El PAP seleccionado ahora est&aacute; vigente!'});
									});
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
						pePres.loadData(params);
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
		$.extend(p,{
			calcRow: function($row){
				var basica = ($row.find('[name=bas]').val()=='') ? 0 : parseFloat($row.find('[name=bas]').val()),
				reunif = ($row.find('[name=reu]').val()=='') ? 0 : parseFloat($row.find('[name=reu]').val());
				$row.find('li:eq(7)').html(ciHelper.formatMon(+basica+reunif));
				$row.find('li:eq(10)').html(ciHelper.formatMon((+basica)*12));
				$row.find('li:eq(11)').html(ciHelper.formatMon((+reunif)*12));
				$row.find('li:eq(12)').html(ciHelper.formatMon((+basica+reunif)*12));
			}
		});
		new K.Window({
			id: 'windowEditPres',
			title: 'Nuevo Presupuesto Anal&iacute;tico de Personal',
			contentURL: 'pe/pres/edit',
			icon: 'ui-icon-document',
			width: 800,
			height: 390,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						periodo: p.cap.periodo,
						organizacion: {
							_id: p.cap.organizacion._id.$id,
							nomb: p.cap.organizacion.nomb,
							componente: {
								_id: p.cap.organizacion.componente._id.$id,
								nomb: p.cap.organizacion.componente.nomb,
								cod: p.cap.organizacion.componente.cod
							},
							actividad: {
								_id: p.cap.organizacion.actividad._id.$id,
								nomb: p.cap.organizacion.actividad.nomb,
								cod: p.cap.organizacion.actividad.cod
							}
						},
						items: []
					};
					for(var i=0; i<p.$w.find('.gridBody .item').length; i++){
						var $row = p.$w.find('.gridBody .item').eq(i);
						var item = {
							item: i+1,
							basica: $row.find('[name=bas]').val(),
							reunificada: $row.find('[name=reu]').val(),
							plaza_pres: $row.find('[name=pre]').val(),
							plaza_ocu: $row.find('[name=ocu]').val(),
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
						var tmp = $row.find('[name=btnNiv]').data('data');
						if(tmp==null){
							$row.find('[name=btnNiv]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un nivel remunerativo!',type: 'error'});
						}else{
							item.nivel = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								abrev: tmp.abrev
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
						if(item.basica==''){
							$row.find('[name=bas]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n b&aacute;sica!',type: 'error'});
						}else if(item.reunificada==''){
							$row.find('[name=reu]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n reunificada!',type: 'error'});
						}else if(item.plaza_pres==''){
							$row.find('[name=pre]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad de plazas presentadas!',type: 'error'});
						}else if(item.plaza_ocu==''){
							$row.find('[name=ocu]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad de plazas ocupadas!',type: 'error'});
						}
						item.principal = K.round(parseFloat(item.basica),2)+K.round(parseFloat(item.reunificada),2);
						data.items.push(item);
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/pres/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El PAP fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnCar]').die('click');
				p.$w.find('[name=btnNiv]').die('click');
				p.$w.find('[name=btnGrup]').die('click');
				p.$w.find('[name=btnAgre]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditPres');
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				p.$w.find('[name=btnCar]').live('click',function(){
					var $this = $(this);
					peClas.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.nomb);
						$this.closest('.item').find('li:eq(2)').html(data.cod);
					}});
				});
				p.$w.find('[name=btnNiv]').live('click',function(){
					var $this = $(this);
					peNive.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.abrev);
						$this.closest('.item').find('[name=bas]').val(data.basica);
						$this.closest('.item').find('[name=reu]').val(data.reunificada);
						p.calcRow($this.closest('.item'));
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
					$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('.ui-button').css('height','14px');
					$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
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
						$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
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
				p.$w.find('[name=orga]').html(p.cap.organizacion.nomb);
				p.$w.find('[name=periodo]').html(p.cap.periodo);
				if(p.cap.items!=null){
					for(var i=0,j=p.cap.items.length; i<j; i++){
						var result = p.cap.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(result.item);
						$row.find('[name=btnCar]').data('data',result.cargo_clasif);
						$row.find('li:eq(1) span').html(result.cargo_clasif.nomb);
						$row.find('li:eq(2)').html(result.cargo_clasif.cod);
						$row.find('[name=btnGrup]').data('data',result.grupo_ocu);
						$row.find('li:eq(4) span').html(result.grupo_ocu.sigla);
						$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('[name=pre]').val(parseFloat(result.con_presupuesto)+parseFloat(result.sin_presupuesto));
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}else{
					var $row = p.$w.find('.gridReference').clone();
					$row.find('li:eq(0)').html('1');
					$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('.ui-button').css('height','14px');
					$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody").append( $row.children() );
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}
			}
		});
	},
	windowEdit: function(p){
		$.extend(p,{
			calcRow: function($row){
				var basica = $row.find('[name=bas]').val(),
				reunif = $row.find('[name=reu]').val();
				if(basica=='') basica = 0;
				else basica = parseFloat(basica);
				if(reunif=='') reunif = 0;
				else reunif = parseFloat(reunif);
				$row.find('li:eq(7)').html(ciHelper.formatMon(+basica+reunif));
				$row.find('li:eq(10)').html(ciHelper.formatMon((+basica)*12));
				$row.find('li:eq(11)').html(ciHelper.formatMon((+reunif)*12));
				$row.find('li:eq(12)').html(ciHelper.formatMon((+basica+reunif)*12));
			}
		});
		new K.Window({
			id: 'windowEditPres'+p.id,
			title: 'Editar PAP: '+p.nomb,
			contentURL: 'pe/pres/edit',
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
							basica: $row.find('[name=bas]').val(),
							reunificada: $row.find('[name=reu]').val(),
							plaza_pres: $row.find('[name=pre]').val(),
							plaza_ocu: $row.find('[name=ocu]').val(),
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
						var tmp = $row.find('[name=btnNiv]').data('data');
						if(tmp==null){
							$row.find('[name=btnNiv]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un nivel remunerativo!',type: 'error'});
						}else{
							item.nivel = {
								_id: tmp._id.$id,
								nomb: tmp.nomb,
								abrev: tmp.abrev
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
						if(item.basica==''){
							$row.find('[name=bas]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n b&aacute;sica!',type: 'error'});
						}else if(item.reunificada==''){
							$row.find('[name=reu]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una remuneraci&oacute;n reunificada!',type: 'error'});
						}else if(item.plaza_pres==''){
							$row.find('[name=pre]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad de plazas presentadas!',type: 'error'});
						}else if(item.plaza_ocu==''){
							$row.find('[name=ocu]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad de plazas ocupadas!',type: 'error'});
						}
						item.principal = K.round(parseFloat(item.basica),2)+K.round(parseFloat(item.reunificada),2);
						data.items.push(item);
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/pres/save',data,function(){
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
				p.$w.find('[name=btnNiv]').die('click');
				p.$w.find('[name=btnGrup]').die('click');
				p.$w.find('[name=btnAgre]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditPres'+p.id);
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
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCar]').live('click',function(){
					var $this = $(this);
					peClas.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.nomb);
						$this.closest('.item').find('li:eq(2)').html(data.cod);
					}});
				});
				p.$w.find('[name=btnNiv]').live('click',function(){
					var $this = $(this);
					peNive.windowSelect({callback: function(data){
						$this.data('data',data);
						$this.parent().find('span').html(data.abrev);
						$this.closest('.item').find('[name=bas]').val(data.basica);
						$this.closest('.item').find('[name=reu]').val(data.reunificada);
						p.calcRow($this.closest('.item'));
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
					$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
						p.calcRow($(this).closest('.item'));
					}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
					$row.find('.ui-button').css('height','14px');
					$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
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
						$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('.ui-button').css('height','14px');
						$row.find('[name=btnCar],[name=btnNiv],[name=btnGrup]').button({icons: {primary: 'ui-icon-search'},text: false});
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
				$.post('pe/pres/get','id='+p.id,function(data){
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=periodo]').val(data.periodo);
					for(var i=0; i<data.items.length; i++){
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(result.item);
						$row.find('[name=btnCar]').data('data',result.cargo_clasif);
						$row.find('li:eq(1) span').html(result.cargo_clasif.nomb);
						$row.find('li:eq(2)').html(result.cargo_clasif.cod);
						$row.find('[name=btnNiv]').data('data',result.nivel);
						$row.find('li:eq(3) span').html(result.nivel.abrev);
						$row.find('[name=btnGrup]').data('data',result.grupo_ocu);
						$row.find('li:eq(4) span').html(result.grupo_ocu.sigla);
						$row.find('[name=bas],[name=reu]').numeric().spinner({min: 0,step: 0.1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('[name=bas]').val(result.basica);
						$row.find('[name=reu]').val(result.reunificada);
						$row.find('[name=pre],[name=ocu]').numeric().spinner({min: 0,step: 1,stop: function(event,ui){
							p.calcRow($(this).closest('.item'));
						}}).keyup(function(){ p.calcRow($(this).closest('.item')); });
						$row.find('[name=pre]').val(result.plaza_pres);
						$row.find('[name=ocu]').val(result.plaza_ocu);
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
		new K.Window({
			id: 'windowDetailsPres'+p.id,
			title: 'PAP '+p.nomb,
			contentURL: 'pe/pres/details',
			icon: 'ui-icon-document',
			width: 800,
			height: 415,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsPres'+p.id);
				K.block({$element: p.$w});
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				$.post('pe/pres/get','id='+p.id,function(data){
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=periodo]').html(data.periodo);
					for(var i=0; i<data.items.length; i++){
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$row.find('li:eq(0)').html(result.item);
						$row.find('li:eq(1)').html(result.cargo_clasif.nomb);
						$row.find('li:eq(2)').html(result.cargo_clasif.cod);
						$row.find('li:eq(3)').html(result.nivel.abrev);
						$row.find('li:eq(4)').html(result.grupo_ocu.sigla);
						$row.find('li:eq(5)').html('S/.'+result.basica);
						$row.find('li:eq(6)').html('S/.'+result.reunificada);
						$row.find('li:eq(7)').html('S/.'+result.principal);
						$row.find('li:eq(8)').html(result.plaza_pres);
						$row.find('li:eq(9)').html(result.plaza_ocu);
						$row.find('li:eq(10)').html('S/.'+K.round(parseFloat(result.basica),2));
						$row.find('li:eq(11)').html('S/.'+K.round(parseFloat(result.reunificada),2));
						$row.find('li:eq(12)').html('S/.'+K.round(parseFloat(result.principal),2));
						$row.find('li:eq(13)').html(result.observ);
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelectPeriodo: function(p){
		p = {};
		new K.Modal({
			id: 'windowSelectPeriodo',
			title: 'Seleccionar Periodo',
			contentURL: 'pe/pres/periodo',
			icon: 'ui-icon-document',
			width: 220,
			height: 40,
			buttons: {
				"Seleccionar": function(){
					pePres.windowConso({periodo: p.$w.find('[name=periodo]').val()});
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
			id: 'windowConsoPres',
			title: 'Presupuesto Anal&iacute;tico de Personal Consolidado',
			contentURL: 'pe/pres/conso',
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
				p.$w = $('#windowConsoPres');
				K.block({$element: p.$w});
		    	$.post('pe/pres/get_conso','periodo='+p.periodo,function(pres){
		    		if(pres==null){
		    			K.unblock({$element: p.$w});
		    			K.closeWindow(p.$w.attr('id'));
		    			return K.notification({title: 'PAP no registrado',text: 'El periodo seleccionado no tiene PAP registrados!',type: 'info',icon: 'ui-icon-calendar'});
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
							$row.find('li:eq(3)').html(result.nivel.abrev);
							$row.find('li:eq(4)').html(result.grupo_ocu.sigla);
							$row.find('li:eq(5)').html('S/.'+result.basica);
							$row.find('li:eq(6)').html('S/.'+result.reunificada);
							$row.find('li:eq(7)').html('S/.'+result.principal);
							$row.find('li:eq(8)').html(result.plaza_pres);
							$row.find('li:eq(9)').html(result.plaza_ocu);
							$row.find('li:eq(10)').html('S/.'+K.round(parseFloat(result.basica),2));
							$row.find('li:eq(11)').html('S/.'+K.round(parseFloat(result.reunificada),2));
							$row.find('li:eq(12)').html('S/.'+K.round(parseFloat(result.principal),2));
							$row.find('li:eq(13)').html(result.observ);
							$row.wrapInner('<a class="item" />');
				        	$trab.find(".gridBody").append( $row.children() );
						}
						p.$w.find('[name=pres]').append($trab.children());
		    		}
					K.unblock({$element: p.$w});
		    	},'json');
			}
		});
	}
};