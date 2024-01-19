/*******************************************************************************
Entidades */
tdGest = {
	loadData: function(params){
		params.nomb = $('.divSearch [name=buscar]').val();
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					if(result.tipo_enti=='E') $li.eq(1).html( result.nomb );
					else $li.eq(1).html( result.appat + ' ' + result.apmat+', '+result.nomb );
					if(result.docident!=null)
						if(result.docident[0]!=null)
							$li.eq(2).html( result.docident[0].num );
					if(result.domicilios != null)
					if(result.domicilios.length > 0 )
						$li.eq(3).html( result.domicilios[0].direccion );
					if(result.telefonos != null)
					if(result.telefonos.length > 0 )						
						$li.eq(4).html( result.telefonos[0].num );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('tipo_enti',result.tipo_enti).dblclick(function(){
						$this = $(this);
						ciDetails.windowDetailsEnti({
							id: $(this).data('id'),
							tipo_enti: $(this).data('tipo_enti'),
							nomb: $(this).find('li:eq(1)').html(),
							buttons: {
								"Nuevo Expediente": function(){
									K.closeWindow('windowDetailEnti'+$this.data('id'));
									tdExpd.windowNewExp({gestor: $this.data('data'),noTupa: true});
								},
								"Ver Expedientes": function(){
									K.closeWindow('windowDetailEnti'+$this.data('id'));
									tdGest.windowExpdGestor({id: $this.data('id'),nomb: $this.find('li:eq(1)').html()});
								},
								"Editar": function(){
									K.closeWindow('windowDetailEnti'+$this.data('id'));
									$.post('td/gest/get','_id='+$this.data('id'),function(data){
										var params = new Object;
										params.id = $this.data('id');
										params.nomb = $this.find('li:eq(1)').html();
										params.tipo_enti = $this.data('tipo_enti');
										params.data = data;
										params.callBack = tdGest.cbEdit;
										//tdGest.windowEdit(params);
										ciEdit.windowEditEntidad(params);
									},'json');
								}/*,
								"Eliminar": function(){
									var params = new Object;
									params.id = $this.data('id');
									params.nomb = $this.find('li:eq(1)').html();
									tdGest.windowDel(params);
								}*/
							}
						});
					}).data('data',result).contextMenu('conMenGest', {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							$('#conMenGest_eli', menu).remove();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenGest_edi': function(t) {
								$.post('td/gest/get','_id='+K.tmp.data('id'),function(data){
									var params = new Object;
									params.id = K.tmp.data('id');
									params.nomb = K.tmp.find('li:eq(1)').html();
									params.tipo_enti = K.tmp.data('tipo_enti');
									params.data = data;
									params.callBack = tdGest.cbEdit;
									//tdGest.windowEdit(params);
									ciEdit.windowEditEntidad(params);
								},'json');
							},
							/*'conMenGest_eli': function(t) {
								var params = new Object;
								params.id = K.tmp.data('id');
								params.nomb = K.tmp.find('li').eq(1).html();
								tdGest.windowDel(params);
							},*/
							'conMenGest_exp': function(t) {
								tdGest.windowExpdGestor({id: K.tmp.data('id'),nomb: K.tmp.find('li').eq(1).html()});
							},
							'conMenGest_agr': function(t) {
								tdExpd.windowNewExp({gestor: K.tmp.data('data'),noTupa: true});
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
						tdGest.loadData(params);
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
	cbEdit: function(data){
		$.post('td/gest/save',data,function(rpta){
			K.notification({title: ciHelper.titleMessages.regiAct,text: 'Gestor actualizado!'});
			K.closeWindow('windowEntiEdit'+data._id);
			if($.cookie('action')=='tdGest') tdGest.init();
		});
	},
	windowDel: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar gestor '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> el gestor <strong>'+p.nomb+'</strong>&#63;',
			icon: 'ui-icon-notification',
			type: 'modal',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function() {
					K.sendingInfo();
					$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('mg/enti/delete','_id='+p.id,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiEli,text: 'Entidad eliminada!'});
						K.closeWindow('windowDelete');
						K.closeWindow('windowDetailEnti'+p.id);
						if($.cookie('action')=='tdGest') tdGest.init();
					});
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	},
	windowExpdGestor: function(p){
		K.Window({
			id: 'windowExpdGestor'+p.id,
			title: 'Expedientes del Gestor '+p.nomb,
			contentURL: 'td/gest/expd',
			icon: 'ui-icon-folder-collapsed',
			width: 765,
			height: 490,
			onContentLoaded: function(){
				p.$w = $('#windowExpdGestor'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=obj]').html( 'expediente(s)' );
				p.$w.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.resize(function(){
					p.$w.find('.grid:eq(1)').height((p.$w.height()-p.$w.find('.grid:eq(0)').height()-p.$w.find('.div-bottom').outerHeight()-10)+'px');
				}).resize();
				p.$w.find('.grid:eq(0)').css('overflow','hidden');
				p.$w.find('.grid:eq(1)').scroll(function(){
					p.$w.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				tdGest.loadDataExpd({
					$w: p.$w,
					params: {
						gestor: p.id,
						page: 1,
						url: 'td/expd/listaexpdgest'
					}
				});
			}
		});
	},
	loadDataExpd: function(p){
		var params = p.params;
		params.page_rows = 20;
		params.page = (params.page) ? params.page : 1;
		$.post(params.url, params, function(data){
			if ( data.items != null ){
			if ( data.paging.total_page_items > 0 ) {
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = p.$w.find('.gridReference').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tdExpd.states[result.estado].color).addClass('vtip').attr('title',tdExpd.states[result.estado].descr);
					$li.eq(1).html( result.num );
					$li.eq(2).html(result.traslados[result.traslados.length-1].origen.organizacion.nomb );
					$li.eq(3).html( result.concepto );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					if(result.fecven!=null) $li.eq(5).html( ciHelper.dateFormat(result.fecven) );
					$row.wrapInner('<a id="'+result._id.$id+'" class="item"  href="javascript: void(0);" />');
					$row.find('a').data('data',result).dblclick(function(){
						tdExpd.windowDetailsExpd({id: $(this).data('data')._id.$id, data: $(this).data('data'), readOnly: true});
					});
					p.$w.find(".gridBody").append( $row.children() );
				}
				count = p.$w.find(".gridBody .item").length;
				p.$w.find('#No-Results').hide();
				p.$w.find('#Results [name=showing]').html( count );
				p.$w.find('#Results [name=founded]').html( data.paging.total_items );
				p.$w.find('#Results').show();
				
				$moreresults = p.$w.find("[name=moreresults]").unbind('click');
				if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					p.$w.find(".gridFoot").show();
					$moreresults.click( function(){
						p.$w.find('.grid').scrollTo( p.$w.find(".gridBody a:last"), 800 );
						p.params.page = parseFloat(data.paging.page) + 1;
						tdGest.loadDataExpd(p);
						$(this).button( "option", "disabled", true );
					});
					p.$w.find( "[name=moreresults]").button( "option", "disabled", false );
		        }else{
		        	p.$w.find(".gridFoot").hide();
					p.$w.find( "[name=moreresults]").button( "option", "disabled", true );
		        }
			} else {
				p.$w.find('#No-Results').show();
				p.$w.find('#Results').hide();
				p.$w.find("[name=moreresults]").button( "option", "disabled", true );
			}
		}else{
			p.$w.find('#No-Results').show();
			p.$w.find('#Results').hide();
			p.$w.find("[name=moreresults]").button( "option", "disabled", true );
		}
		K.unblock({$element: p.$w});
		}, 'json');
	}
};
define(
	['td/expd'],
	function(tdExpd){
		return tdGest;
	}
);