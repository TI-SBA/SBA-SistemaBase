/*******************************************************************************
Conceptos */
tsSaldBan = {
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
				$p.find('[name=tsSaldEfe]').click(function(){ tsSaldEfe.init(); });
				$p.find('[name=tsSaldBan]').click(function(){ tsSaldBan.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsSaldCue]').click(function(){ tsSaldCue.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsSaldBan',
			titleBar: {
				title: 'Saldos: Bancos'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/sald/index_cue',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre del concepto' ).width('250');
				$mainPanel.find('[name=obj]').html( 'cuenta(s) corriente(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					tsSaldBan.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$.post('ts/ctban/all_nr?tipo=B',function(data){
					if(data.length<1){
						$mainPanel.find('[name=btnAgregar]').button( "option", "disabled", true );
					}
				},'json');
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tsSaldBan.loadData({page: 1,url: 'ts/sald/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tsSaldBan.loadData({page: 1,url: 'ts/sald/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				tsSaldBan.loadData({page: 1,url: 'ts/sald/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.tipo = "B";
		params.texto = $('.divSearch [name=buscar]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',tsSaldBan.states[result.estado].color).addClass('vtip').attr('title',tsSaldBan.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.periodo );
					$li.eq(3).html( result.cuenta_banco.cod);
					$li.eq(4).html( result.saldo_deudor_inicial );
					$li.eq(5).html( result.saldo_acreedor_inicial);
					$li.eq(6).html( result.saldo_deudor_final );
					$li.eq(7).html( result.saldo_acreedor_final);
					$li.eq(8).html( ciHelper.dateFormat(result.apertura.fec));
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
						tsSaldBan.loadData(params);
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
			id: 'windowNewtsSaldBan',
			title: 'Nuevo Saldo',
			contentURL: 'ts/sald/edit_cue',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 120,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var cuenta_banco = p.$w.find('[name=ct_ban] :selected').data('data'),
					data = {
						tipo: 'B',
						periodo: p.$w.find('[name=ano]').val()+(parseFloat(p.$w.find('[name=mes] :selected').val()<10)?"0":"")+p.$w.find('[name=mes] :selected').val()+'00',
						estado: 'A',
						saldo_deudor_inicial: p.$w.find('[name=sald_deud]').val(),
						saldo_acreedor_inicial: p.$w.find('[name=sald_acre]').val(),
						saldo_deudor_final: p.$w.find('[name=sald_deud]').val(),
						saldo_acreedor_final: p.$w.find('[name=sald_acre]').val(),
						cuenta_banco: {
							_id: cuenta_banco._id.$id,
							cod_enti_financiera: cuenta_banco.cod_banco,
							nomb: cuenta_banco.nomb,
							cod: cuenta_banco.cod,
							moneda: cuenta_banco.moneda
						}
					};
					/*data.periodo.mes = p.$w.find('[name=mes] :selected').val();
					data.periodo.ano = p.$w.find('[name=ano]').val();*/
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('ts/sald/save_ban',data,function(){
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
				p.$w = $('#windowNewtsSaldBan');
				K.block({$element: p.$w});
				var d = new Date();
				p.$w.find('[name=ano]').val(d.getFullYear()); 
				p.$w.find('[name=ano]').spinner();
				p.$w.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				$.post('ts/ctban/all_nr?tipo=B',function(data){
					var $cbo = p.$w.find('[name=ct_ban]');
					if(data!=null){
						for(var i=0; i<data.length; i++){		
							var id = data[i]._id.$id;
							var cod_banco = data[i].cod_banco;
							var nomb = data[i].nomb;
							var cod = data[i].cod;
							var moneda = data[i].moneda;
							$cbo.append('<option value="'+id+'" >'+cod+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	}	
};