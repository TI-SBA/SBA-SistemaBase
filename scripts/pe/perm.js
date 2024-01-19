/*******************************************************************************
permisos */
pePerm = {
	states: {
		"P": {
			descr: "Pendiente",
			color: "#CCCCCC"
		},
		"A": {
			descr: "Aprobado",
			color: "#003265"
		},
		"X": {
			descr: "Anulado",
			color: "#FF1F0F"
		}
	},
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'pePerm',
			titleBar: {
				title: 'Permisos'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/perm',
			store: false,
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de trabajador' ).width('250');
				$mainPanel.find('[name=obj]').html( 'permiso(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					pePerm.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						pePerm.loadData({page: 1,url: 'pe/perm/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						pePerm.loadData({page: 1,url: 'pe/perm/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				pePerm.loadData({page: 1,url: 'pe/perm/lista'});
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
					$li.eq(0).css('background',pePerm.states[result.estado].color).addClass('vtip').attr('title',pePerm.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( ciHelper.enti.formatName(result.trabajador) );
					$li.eq(3).html( result.trabajador.cargo.organizacion.nomb );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$li.eq(5).html( ciHelper.dateFormat(result.fecini) );
					$li.eq(6).html( ciHelper.dateFormat(result.fecfin) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						pePerm.windowEdit({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).contextMenu("conMenListAp", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							$('#conMenListAp_ver',menu).remove();
							if(K.tmp.data('estado')!='P')
								$('#conMenListAp_edi,#conMenListAp_apr,#conMenListAp_anu',menu).remove();
							return menu;
						},
						bindings: {
							'conMenListAp_edi': function(t) {
								pePerm.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
							},
							'conMenListAp_apr': function(t) {
								K.sendingInfo();
								$.post('pe/perm/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
									K.clearNoti();
									K.notification({title: 'Permiso Aprobado',text: 'La aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
							},
							'conMenListAp_anu': function(t) {
								K.sendingInfo();
								$.post('pe/perm/save',{_id: K.tmp.data('id'),estado: 'X'},function(){
									K.clearNoti();
									K.notification({title: 'Permiso Anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						pePerm.loadData(params);
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
			id: 'windowNewPerm',
			title: 'Nuevo Permiso',
			contentURL: 'pe/perm/edit',
			icon: 'ui-icon-plusthick',
			width: 445,
			height: 100,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						trabajador: p.$w.find('[name=trabajador]').data('data'),
						fecini: p.$w.find('[name=inicio]').val(),
						fecfin: p.$w.find('[name=fin]').val()
					};
					if(data.trabajador==null){
						p.$w.find('[name=trabajador]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un trabajador para otorgar el permiso!',
							type: 'error'
						});
					}else{
						data.trabajador = ciHelper.enti.dbTrabRel(data.trabajador);
					}
					if(data.fecini==''){
						p.$w.find('[name=inicio]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de inicio para otorgar el permiso!',
							type: 'error'
						});
					}
					if(data.fecfin==''){
						p.$w.find('[name=fin]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de finalizaci&oacute;n para otorgar el permiso!',
							type: 'error'
						});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/perm/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Permiso fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewPerm');
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=trabajador]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnSel]').button('option','text',false);
					},filter: [
  					    {nomb: 'roles.trabajador',value: {$exists: true}}
  					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=inicio]').datetimepicker({
					minuteGrid: 10,
					minDate: 0
				});
				p.$w.find('[name=fin]').datetimepicker({
					minuteGrid: 10,
					minDate: 0
				});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditPerm'+p.id,
			title: 'Editar Permiso de '+p.nomb,
			contentURL: 'pe/perm/edit',
			icon: 'ui-icon-pencil',
			width: 445,
			height: 100,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						trabajador: p.$w.find('[name=trabajador]').data('data'),
						fecini: p.$w.find('[name=inicio]').val(),
						fecfin: p.$w.find('[name=fin]').val()
					};
					if(data.trabajador==null){
						p.$w.find('[name=trabajador]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un trabajador para otorgar el permiso!',
							type: 'error'
						});
					}else{
						data.trabajador = ciHelper.enti.dbTrabRel(data.trabajador);
					}
					if(data.fecini==''){
						p.$w.find('[name=inicio]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de inicio para otorgar el permiso!',
							type: 'error'
						});
					}
					if(data.fecfin==''){
						p.$w.find('[name=fin]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar una fecha de finalizaci&oacute;n para otorgar el permiso!',
							type: 'error'
						});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/perm/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Permiso fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditPerm'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=btnSel]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						p.$w.find('[name=trabajador]').html(ciHelper.enti.formatName(data)).data('data',data);
					},filter: [
  					    {nomb: 'roles.trabajador',value: {$exists: true}}
  					]});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				p.$w.find('[name=inicio]').datetimepicker({
					minuteGrid: 10,
					minDate: 0
				});
				p.$w.find('[name=fin]').datetimepicker({
					minuteGrid: 10,
					minDate: 0
				});
				$.post('pe/perm/get',{_id: p.id},function(data){
					p.$w.find('[name=trabajador]').html(ciHelper.enti.formatName(data.trabajador)).data('data',data.trabajador);
					p.$w.find('[name=inicio]').val(ciHelper.dateFormatBDNotSec(data.fecini));
					p.$w.find('[name=fin]').val(ciHelper.dateFormatBDNotSec(data.fecfin));
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};