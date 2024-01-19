/*******************************************************************************
cuentas todas las dependencias */
cjCuenTod = {
	init: function(){
		if($('#pageWrapper [child=cuen]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('cj/navg/cuen',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="cuen" />');
					$p.find("[name=cjCuen]").after( $row.children() );
				}
				$p.find('[name=cjCuen]').data('cuen',$('#pageWrapper [child=cuen]:first').data('cuen'));
				$p.find('[name=cjCuenTod]').click(function(){ cjCuenTod.init(); }).find('ul').addClass('ui-state-highlight');
				$p.find('[name=cjCuenPor]').click(function(){ cjCuenPor.init(); });
			},'json');
		}
		K.initMode({
			mode: 'cj',
			action: 'cjCuenTod',
			titleBar: {
				title: 'Cuentas por cobrar: Por dependencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/cuen/toda',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de caja' ).width('250');
				$mainPanel.find('[name=obj]').html( 'caja(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnEmitir]').click(function(){
					K.clearNoti();
					var cuentas = [],
					cliente = $mainPanel.find('[name=cuenta]:checked').eq(0).closest('.item').data('cliente'),
					orga = $mainPanel.find('[name=cuenta]:checked').eq(0).closest('.item').data('orga'),
					moneda = $mainPanel.find('[name=cuenta]:checked').eq(0).closest('.item').data('moneda');
					if($mainPanel.find('[name=cuenta]:checked').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos una cuenta!',type: 'error'});
					}
					for(var i=0,j=$mainPanel.find('[name=cuenta]:checked').length; i<j; i++){
						if($mainPanel.find('[name=cuenta]:checked').eq(i).closest('.item').data('cliente')!=cliente){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar cuentas del mismo cliente!',type: 'error'});
						}
						if($mainPanel.find('[name=cuenta]:checked').eq(i).closest('.item').data('moneda')!=moneda){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar cuentas con la misma moneda!',type: 'error'});
						}
						if($mainPanel.find('[name=cuenta]:checked').eq(i).closest('.item').data('orga')!=orga){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar cuentas con la misma organizaci&oacute;n!',type: 'error'});
						}
						cuentas.push($mainPanel.find('[name=cuenta]:checked').eq(i).val());
					}
					cjCuen.windowNewCompr({cuentas: cuentas,cliente: cliente});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjCuenTod.loadData({page: 1,url: 'cj/cuen/lista_all'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjCuenTod.loadData({page: 1,url: 'cj/cuen/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=estado]').change(function(){
					$("#mainPanel .gridBody").empty();
					cjCuenTod.loadData({page: 1,url: 'cj/cuen/lista_all'});
				});
				$mainPanel.find('[name=divOrga]').buttonset();
				$mainPanel.find('#rbtnOrgaSelect').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$mainPanel.find('[name=orga]').html(data.nomb).data('id',data._id.$id);
						$('#mainPanel .gridBody').empty();
						cjCuenTod.loadData({page: 1,url: 'cj/cuen/lista_all'});
					}});
				});
				$mainPanel.find('#rbtnOrgaX').click(function(){
					$mainPanel.find('[name=orga]').html("Organizaci&oacute;n").removeData('id');
					$('#mainPanel .gridBody').empty();
					cjCuenTod.loadData({page: 1,url: 'cj/cuen/lista_all'});
				});
				cjCuenTod.loadData({page: 1,url: 'cj/cuen/lista_all'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			texto: $('.divSearch [name=buscar]').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1,
		    estado: $mainPanel.find('[name=estado] option:selected').val(),
		    orga: $mainPanel.find('[name=orga]').data('id')
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).css('background',cjCuen.states[result.estado].color).addClass('vtip').attr('title',cjCuen.states[result.estado].descr);
					$li.eq(1).html('<input type="checkbox" name="cuenta" value="'+result._id.$id+'">');
					if(result.observ!=null) $li.eq(2).html( result.observ );
					else $li.eq(2).html( result.servicio.nomb);
					$li.eq(3).html( result.servicio.organizacion.nomb );
					$li.eq(4).html( ciHelper.enti.formatName(result.cliente) );
					$li.eq(5).html( ciHelper.formatMon(result.total,result.moneda) );
					$li.eq(6).html( ciHelper.formatMon(result.saldo,result.moneda) );
					$li.eq(7).html( ciHelper.enti.formatName(result.autor) );
					$li.eq(8).html( ciHelper.dateFormat(result.fecreg) );
					$li.eq(9).html( ciHelper.dateFormat(result.fecven) );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					var orga = result.servicio.organizacion._id;
					if(orga.$id!=null)
						orga = result.servicio.organizacion._id.$id;
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						cjCuen.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html()});
					}).data('estado',result.estado).data('orga',orga).data('cliente',result.cliente._id.$id)
					.data('data',result).contextMenu("conMenCjCuen", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							if(K.tmp.data('data').operacion==null) $('#conMenCjCuen_ori',menu).remove();
							$('#conMenCjCuen_anu',menu).remove();
							return menu;
						},
						bindings: {
							'conMenCjCuen_ver': function(t) {
								cjCuen.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
							},
							'conMenCjCuen_ori': function(t) {
								var data = K.tmp.data('data');
								if(data.modulo=='IN'){
									inArre.windowDetails({id: data.operacion.$id});
								}else if(data.modulo=='CM'){
									$.post('cm/oper/get',{id: data.operacion.$id},function(oper){
										cmOper.showDetails({data: oper});
									},'json');
								}
							},
							'conMenCjCuen_anu': function(t) {
								K.sendingInfo();
								$.post('cj/cuen/save',{_id: K.tmp.data('id'),estado: 'X'},function(){
									K.clearNoti();
									K.notification({title: 'Cuenta Anulada',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
									$('#pageWrapperLeft .ui-state-highlight').click();
								});
							}
						}
					});
					$row.find('li:eq(0),li:eq(2),li:eq(3),li:eq(4),li:eq(5),li:eq(6),li:eq(7),li:eq(8),li:eq(9)')
					.click(function(){
						var $check = $(this).closest('.item').find('[name=cuenta]');
						if($check.is(':checked')==false) $check.attr('checked','checked');
						else $check.removeAttr('checked');
						if($(this).closest('.item').data('estado')=='I'){
							K.clearNoti();
							$check.removeAttr('checked');
							K.notification({title: 'Letra Protestada',text: 'Para pagar esta cuenta debe primero levantar la letra en Inmuebles!',type: 'info'});
						}
					});
					$row.find('[name=cuenta]').click(function(){
						if($(this).closest('.item').data('estado')=='I'){
							K.clearNoti();
							$(this).removeAttr('checked');
							K.notification({title: 'Letra Protestada',text: 'Para pagar esta cuenta debe primero levantar la letra en Inmuebles!',type: 'info'});
						}
					});
					if(result.estado=='C'||result.estado=='X'){
						$row.find('[name=cuenta]').replaceWith('<span>--</span>');
					}
					$row.find('a').data('moneda',result.moneda);
		        	$("#mainPanel .gridBody").append( $row.children() );
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
						cjCuenTod.loadData(params);
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
	}
};