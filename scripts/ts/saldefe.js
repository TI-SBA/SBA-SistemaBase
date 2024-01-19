/*******************************************************************************
Conceptos */
tsSaldEfe = {
	states:{
		"A":{
			color:"green",
			descr:"Aperturado"
		},
		"C":{
			color:"black",
			descr:"Cerrado"
		}
	},
	init: function(){
		if($('#pageWrapper [child=sald]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/sald',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="sald" />');
					$p.find("[name=tsSald]").after( $row.children() );
				}
				$p.find('[name=tsSald]').data('sald',$('#pageWrapper [child=sald]:first').data('sald'));
				$p.find('[name=tsSaldEfe]').click(function(){ tsSaldEfe.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsSaldCue]').click(function(){ tsSaldCue.init(); });
				$p.find('[name=tsSaldBan]').click(function(){ tsSaldBan.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsSaldEfe',
			titleBar: {
				title: 'Saldos: Efectivo'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/sald/index_efe',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre del concepto' ).width('250');
				$mainPanel.find('[name=obj]').html( 'saldo(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsSaldEfe.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$.post('ts/sald/all','tipo=E',function(data){
					if(data!=null){
						$mainPanel.find('[name=btnAgregar]').button( "option", "disabled", true );;
					}
				},'json');
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsSaldEfe.loadData({page: 1,url: 'ts/sald/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsSaldEfe.loadData({page: 1,url: 'ts/sald/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsSaldEfe.loadData({page: 1,url: 'ts/sald/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.tipo = "E";
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tsSaldEfe.states[result.estado].color).addClass('vtip').attr('title',tsSaldEfe.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.periodo );
					$li.eq(3).html( result.saldo_deudor_inicial );
					$li.eq(4).html( result.saldo_acreedor_inicial);
					$li.eq(5).html( result.saldo_deudor_final );
					$li.eq(6).html( result.saldo_acreedor_final);
					$li.eq(7).html( ciHelper.dateFormat(result.apertura.fec));
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenTsSaldAll", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').estado=="C"){
									$('#conMenTsSaldAll_cer',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenTsSaldAll_cer': function(t) {
									K.sendingInfo();
									$.post('ts/sald/cerrar',{_id: K.tmp.data('id')},function(){
										K.clearNoti();
										K.notification({title: 'Saldo Cerrado',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										//$("#mainPanel .gridBody").empty();
										tsSaldEfe.init();
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
						tsSaldEfe.loadData(params);
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
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewtsSaldEfe',
			title: 'Nuevo Saldo',
			contentURL: 'ts/sald/edit_efe',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						tipo: 'E',
						estado: 'A',
						periodo: p.$w.find('[name=ano]').val()+(parseFloat(p.$w.find('[name=mes] :selected').val()<10)?"0":"")+p.$w.find('[name=mes] :selected').val()+'00'
					};
					/*data.apertura = new Object;
					data.apertura.autor = ciHelper.enti.dbTrabRel(K.session.enti);*/
					data.saldo_deudor_inicial = p.$w.find('[name=sald_deud]').val();
					data.saldo_acreedor_inicial = p.$w.find('[name=sald_acre]').val();
					data.saldo_deudor_final = p.$w.find('[name=sald_deud]').val();
					data.saldo_acreedor_final = p.$w.find('[name=sald_acre]').val();
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/sald/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Saldo fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewtsSaldEfe');
				K.block({$element: p.$w});
				var d = new Date();
				p.$w.find('[name=ano]').val(d.getFullYear()); 
				p.$w.find('[name=ano]').spinner();
				p.$w.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				K.unblock({$element: p.$w});
			}
		});
	}
	
};