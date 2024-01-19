/*******************************************************************************
Cuentas Bancarias */
tsCtban = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green"
		},
		D: {
			descr: "Deshabilitado",
			color: "gray"
		}
	},
	dbRel: function(item){
		var rpta = {
			_id: item._id.$id,
			cod: item.cod,
			descr: item.descr
		};
		if(item.cuenta!=null){
			rpta.cuenta = {
				_id: item.cuenta._id,
				cod: item.cuenta.cod,
				descr: item.cuenta.descr
			}
			if(item.cuenta._id.$id!=null)
				rpta.cuenta._id = item.cuenta._id.$id;
		}
		return rpta;
		
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsCtban',
			titleBar: {
				title: 'Cuentas Bancarias'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/ctban',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de la cuenta bancaria' ).width('250');
				$mainPanel.find('[name=obj]').html( 'cuenta(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsCtban.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsCtban.loadData({page: 1,url: 'ts/ctban/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsCtban.loadData({page: 1,url: 'ts/ctban/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsCtban.loadData({page: 1,url: 'ts/ctban/lista'});
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
					$li.eq(0).css('background',tsCtban.states[result.estado].color).addClass('vtip').attr('title',tsCtban.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( result.cod );
					$li.eq(4).html( (result.moneda=='S')?'Nuevos Soles':'D&oacute;lares' );
					$li.eq(5).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						tsCtban.windowDetails({id: $(this).data('id')});
					}).data('data',result).contextMenu("conMenTsCtBan", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').estado=="H") excep+='#conMenTsCtBan_hab';
								else if(K.tmp.data('data').estado=="D") excep+='#conMenTsCtBan_edi,#conMenTsCtBan_des';
								$(excep+',#conMenSpOrd_about',menu).remove();
								return menu;
							},
							bindings: {
								'conMenTsCtBan_edi': function(t) {
									tsCtban.windowEdit({id: K.tmp.data('id')});
								},
								'conMenTsCtBan_ver': function(t) {
									tsCtban.windowDetails({id: K.tmp.data('id')});
								},
								'conMenTsCtBan_hab': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado:"H"
										};
										K.sendingInfo();
										$.post('ts/ctban/save',data,function(){
											K.clearNoti();
											K.notification({title: 'Cuenta Bancaria Habilitada',text: 'La Cuenta bancaria seleccionada ha sido habilitada con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenTsCtBan_des': function(t) {
									var data = {
											_id: K.tmp.data('id'),
											estado:"D"
										};
										K.sendingInfo();
										$.post('ts/ctban/save',data,function(){
											K.clearNoti();
											K.notification({title: 'Cuenta Bancaria Deshabilitada',text: 'La Cuenta bancaria seleccionada ha sido deshabilitada con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
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
						tsCtban.loadData(params);
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
		if(p==null) p = {};
		new K.Window({
			id: 'windowDetailstsCtban',
			title: 'Ver Cuenta Bancaria',
			contentURL: 'ts/ctban/details',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 160,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailstsCtban');
				K.block({$element: p.$w});
				$.post('ts/ctban/get','id='+p.id,function(data){
					if(data.banco){
						p.$w.find('[name=entidad]').html(data.banco.nomb);
					}
					p.$w.find('[name=cod_ban]').html(data.cod_banco);
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=num]').html(data.cod);
					if(data.cuenta){
						p.$w.find('[name=cuenta]').html(data.cuenta.cod+' - '+data.cuenta.descr);
					}
					p.$w.find('[name=moneda]').html((result.moneda=='S')?'Nuevos Soles':'D&oacute;lares');
					p.$w.find('[name=descr]').html(data.descr);
					K.unblock({$element: p.$w});
				},'json');
				
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Window({
			id: 'windowNewtsCtban',
			title: 'Nueva Cuenta Bancaria',
			contentURL: 'ts/ctban/edit',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 260,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						cod_banco: p.$w.find('[name=cod_ban]').val(),
						nomb: p.$w.find('[name=nomb]').val(),
						cod: p.$w.find('[name=num]').val(),
						descr: p.$w.find('[name=descr]').val(),
						estado: "H",
						moneda: p.$w.find('[name=moneda] option:selected').val()
					};
					if(data.cod_banco==''){
						p.$w.find('[name=cod_ban]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de entidad bancaria!',type: 'error'});
					}
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					if(data.cod==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero !',type: 'error'});
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta!=null){
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Cuenta Contable !',type: 'error'});
					}
					var entidad = p.$w.find('[name=entidad]').data('data');
					if(entidad!=null){
						data.banco = new Object;
						data.banco._id = entidad._id.$id;
						data.banco.tipo_enti = entidad.tipo_enti;
						data.banco.nomb = entidad.nomb;
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una entidad Financiera !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/ctban/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La cuenta bancaria fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewtsCtban');
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=entidad]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrBene]').button('option','text',false);
					},filter: [{nomb: 'tipo_enti',value: 'E'}, ]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEnti]').after('&nbsp;<button name="btnAgrBene">Agregar</button>');
				p.$w.find('[name=btnAgrBene]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.$w.find('[name=entidad]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnEnti]').button('option','text',false);
						p.$w.find('[name=btnAgrBene]').button('option','text',false);
					},reqs: {tipo_enti: 'E'}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=cuenta]').attr('size',15);
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=cod_ban]').numeric();
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEdittsCtban',
			title: 'Editar Cuenta Bancaria',
			contentURL: 'ts/ctban/edit',
			icon: 'ui-icon-pencil',
			width: 450,
			height: 260,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						cod_banco: p.$w.find('[name=cod_ban]').val(),
						nomb: p.$w.find('[name=nomb]').val(),
						cod: p.$w.find('[name=num]').val(),
						descr: p.$w.find('[name=descr]').val(),
						estado: "H",
						moneda: p.$w.find('[name=moneda] option:selected').val()
					};
					if(data.cod_banco==''){
						p.$w.find('[name=cod_ban]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de entidad bancaria!',type: 'error'});
					}
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					if(data.cod==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un n&uacute;mero !',type: 'error'});
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta!=null){
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
					}
					var entidad = p.$w.find('[name=entidad]').data('data');
					if(entidad!=null){
						data.banco = new Object;
						data.banco._id = entidad._id.$id;
						data.banco.tipo_enti = entidad.tipo_enti;
						data.banco.nomb = entidad.nomb;
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una entidad Financiera !',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/ctban/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La cuenta bancaria fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEdittsCtban');
				K.block({$element: p.$w});
				p.$w.find('[name=cod_ban]').numeric();
				p.$w.find('[name=cuenta]').attr('size',15);
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod+' '+data.descr).data('data',data);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEnti]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=entidad]').html(data.nomb).data('data',data);
					},filter: [{nomb: 'tipo_enti',value: 'E'}, ]});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				p.$w.find('[name=btnEnti]').after('&nbsp;<button name="btnAgrBene">Agregar</button>');
				p.$w.find('[name=btnAgrBene]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.$w.find('[name=entidad]').html(data.nomb).data('data',data);
					},reqs: {tipo_enti: 'E'}});
				}).button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$.post('ts/ctban/get','id='+p.id,function(data){
					p.$w.find('[name=cod_ban]').val(data.cod_banco);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=num]').val(data.cod);
					p.$w.find('[name=moneda]').selectVal(data.moneda);
					p.$w.find('[name=descr]').val(data.descr);
					if(data.cuenta){
						p.$w.find('[name=cuenta]').val(data.cuenta.cod).data('data',data.cuenta);
					}
					p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					if(data.banco){
						p.$w.find('[name=entidad]').html(data.banco.nomb).data('data',data.banco);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p.bootstrap!=null){
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Cuenta Bancaria',
				buttons: {
					'Seleccionar': function(){
						if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
						}
					},
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowSelect');
					var params = {};
					p.$grid = new K.grid({
						$el: p.$w.find('[name=tmp]'),
						cols: ['','C&oacute;digo','Descripci&oacute;n'],
						data: 'ts/ctban/lista',
						params: {},
						itemdescr: 'Cuentas Bancarias(s)',
						
						onLoading: function(){ 
							K.block({$element: p.$w});
						},
						onComplete: function(){ 
							K.unblock({$element: p.$w});
						},
						fill: function(data,$row){
							$row.append('<td>');
							$row.append('<td>'+data.cod+'</td>');
							$row.append('<td>'+data.descr+'</td>');
							$row.data('data',data).dblclick(function(){
								p.$w.find('.modal-footer button:first').click();
							}).contextMenu('conMenListSel', {
								bindings: {
									'conMenListSel_sel': function(t) {
										p.$w.find('.modal-footer button:first').click();
									}
								}
							});
							return $row;
						}
					});
				}
			});
		}else{
			new K.Modal({
				id: 'windowSelect',
				content: '<div name="tmp"></div>',
				width: 750,
				height: 400,
				title: 'Seleccionar Cuenta Bancaria',
				buttons: {
					"Seleccionar": {
						icon: 'fa-check',
						type: 'info',
						f: function(){
							if(p.$w.find('.highlights').data('data')!=null){
								p.callback(p.$w.find('.highlights').data('data'));
								K.closeWindow(p.$w.attr('id'));
							}else{
								K.clearNoti();
								return K.msg({
									title: ciHelper.titles.infoReq,
									text: 'Debe seleccionar un item!',
									type: 'error'
								});
							}
						}
					},
					"Cancelar": {
						icon: 'fa-ban',
						type: 'danger',
						f: function(){
							K.closeWindow(p.$w.attr('id'));
						}
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Codigo','Descripcion'],
					data: 'ts/ctban/lista',
					params: {},
					itemdescr: 'Caja(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						
						$row.data('data',data).dblclick(function(){
							p.$w.find('.modal-footer button:first').click();
						}).contextMenu('conMenListSel', {
							bindings: {
								'conMenListSel_sel': function(t) {
									p.$w.find('.modal-footer button:first').click();
								}
							}
						});
						return $row;
					}
				});
				}
			});
		}

		
	}
};