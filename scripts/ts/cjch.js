/*******************************************************************************
Cajas chicas */
tsCjch = {
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
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'ts',
			action: 'tsCjch',
			titleBar: {
				title: 'Cajas Chicas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/cjch',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de la caja' ).width('250');
				$mainPanel.find('[name=obj]').html( 'caja(s) chica(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsCjch.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsCjch.loadData({page: 1,url: 'ts/cjch/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsCjch.loadData({page: 1,url: 'ts/cjch/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsCjch.loadData({page: 1,url: 'ts/cjch/lista'});
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
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tsCjch.states[result.estado].color).addClass('vtip').attr('title',tsCjch.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result).dblclick(function(){
						tsCjch.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).contextMenu("conMenTsCjCh", {
						onShowMenu: function(e, menu) {
						    var excep = '',
						    $target = $(e.target);
							$target.closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$target.closest('.item').find('ul').addClass('ui-state-highlight');
							$target.closest('.item').click();
							K.tmp = $target.closest('.item');
							if(K.tmp.data('data').estado=="H") excep+='#conMenTsCjCh_hab';
							else if(K.tmp.data('data').estado=="D") excep+='#conMenTsCjCh_edi,#conMenTsCjCh_des';
							$(excep+',#conMenTsCjCh_act,#conMenSpOrd_about,#conMenTsConc_ver',menu).remove();
							return menu;
						},
						bindings: {
							'conMenTsCjCh_ver': function(t) {
								tsCjch.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenTsCjCh_edi': function(t) {
								tsCjch.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							},
							'conMenTsCjCh_hab': function(t) {
								K.sendingInfo();
								$.post('ts/cjch/save',{_id: K.tmp.data('id'),estado: "H"},function(){
									K.clearNoti();
									K.notification({title: 'Caja Chica Habilitada',text: 'La Caja Chica seleccionada ha sido habilitada con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
							},
							'conMenTsCjCh_des': function(t) {
								K.sendingInfo();
								$.post('ts/cjch/save',{_id: K.tmp.data('id'),estado: "D"},function(){
									K.clearNoti();
									K.notification({title: 'Caja Chica Deshabilitada',text: 'La Caja Chica seleccionada ha sido deshabilitada con &eacute;xito!'});
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
						prFuen.loadData(params);
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
		new K.Window({
			id: 'windowNewtsCjch',
			title: 'Nueva Caja Chica',
			contentURL: 'ts/cjch/edit',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 220,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val(),
						local: p.$w.find('[name=local]').data('data'),
						organizacion: p.$w.find('[name=orga]').data('data'),
						cod: p.$w.find('[name=rendicion]').val(),
						monto: p.$w.find('[name=monto]').val()
					};
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					if(data.local==null){
						p.$w.find('[name=btnLoca]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un local!',type: 'error'});
					}else{
						data.local = {
							_id: data.local._id.$id,
							descr: data.local.descr,
							direccion: data.local.direccion
						};
					}
					if(data.organizacion==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = {
							_id: data.organizacion._id.$id,
							nomb: data.organizacion.nomb,
							actividad: {
								_id: data.organizacion.actividad._id.$id,
								cod: data.organizacion.actividad.cod,
								nomb: data.organizacion.actividad.nomb
							},
							componente: {
								_id: data.organizacion.componente._id.$id,
								cod: data.organizacion.componente.cod,
								nomb: data.organizacion.componente.nomb
							}
						};
					}
					if(data.cod==''){
						p.$w.find('[name=rendicion]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una rendici&oacute;n!',type: 'error'});
					}
					if(data.monto==''){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/cjch/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La caja chica fue registrada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewtsCjch');
				p.$w.find('[name=btnLoca]').click(function(){
					ciSearch.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
						p.$w.find('[name=btnLoca]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=rendicion]').val(1).numeric().spinner({step: 1,min: 1})
					.parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=monto]').val(0).numeric().spinner({step: 0.1,min: 0})
					.parent().find('.ui-button').css('height','14px');
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEdittsCjch'+p.id,
			title: 'Editar Caja Chica: '+p.nomb,
			contentURL: 'ts/cjch/edit',
			icon: 'ui-icon-pencil',
			width: 500,
			height: 200,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val(),
						local: p.$w.find('[name=local]').data('data'),
						organizacion: p.$w.find('[name=orga]').data('data'),
						monto: p.$w.find('[name=monto]').val()
					};
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					if(data.local==null){
						p.$w.find('[name=btnLoca]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un local!',type: 'error'});
					}else{
						data.local = {
							_id: data.local._id.$id,
							descr: data.local.descr,
							direccion: data.local.direccion
						};
					}
					if(data.organizacion==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.organizacion = {
							_id: data.organizacion._id.$id,
							nomb: data.organizacion.nomb,
							actividad: {
								_id: data.organizacion.actividad._id.$id,
								cod: data.organizacion.actividad.cod,
								nomb: data.organizacion.actividad.nomb
							},
							componente: {
								_id: data.organizacion.componente._id.$id,
								cod: data.organizacion.componente.cod,
								nomb: data.organizacion.componente.nomb
							}
						};
					}
					if(data.monto==''){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/cjch/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'La caja chica fue actualizada con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEdittsCjch'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnLoca]').click(function(){
					ciSearch.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
						p.$w.find('[name=btnLoca]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
						p.$w.find('[name=btnOrga]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=rendicion]').closest('tr').remove();
				p.$w.find('[name=monto]').numeric().spinner({step: 0.1,min: 0})
					.parent().find('.ui-button').css('height','14px');
				$.post('ts/cjch/get','id='+p.id,function(data){
					p.$w.find('[name=local]').html(data.local.descr).data('data',data.local);
					p.$w.find('[name=orga]').html(data.organizacion.nomb).data('data',data.organizacion);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=monto]').val(data.monto);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailstsCjch'+p.id,
			title: 'Caja Chica: '+p.nomb,
			contentURL: 'ts/cjch/details',
			icon: 'ui-icon-document',
			width: 500,
			height: 210,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailstsCjch'+p.id);
				K.block({$element: p.$w});
				$.post('ts/cjch/get','id='+p.id,function(data){
					p.$w.find('[name=orga]').html(data.organizacion.nomb);
					p.$w.find('[name=nomb]').html(data.nomb);
					if(data.descr!='') p.$w.find('[name=descr]').html(data.descr);
					else p.$w.find('[name=descr]').closest('tr').remove();
					p.$w.find('[name=local]').html(data.local.descr);
					p.$w.find('[name=direc]').html(data.local.direccion);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Caja',
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
					cols: ['','Nombre'],
					data: 'ts/cjch/lista',
					params: {},
					itemdescr: 'Caja(s)',
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						
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
};