/*************************************************************************
  Contingencias : A Favor */
alContFav = {
	tipo: {
		"A":"Administrativos",
		"L":"Laborales",
		"T":"Contesioso Administrativo",
		"C":"Civiles",
		"P":"Penales",
		"O":"Otros"
	},
	init: function(){
		if($('#pageWrapper [child=alcont]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('al/navg/cont',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="alcont" />');
					$p.find("[name=alCont]").after( $row.children() );
				}
				$p.find('[name=alCont]').data('alcont',$('#pageWrapper [child=alexpd]:first').data('alcont'));
				$p.find('[name=alContFav]').click(function(){ alContFav.init(); }).addClass('ui-state-highlight');
				$p.find('[name=alContCont]').click(function(){ alContCont.init(); });
			},'json');
		}
		K.initMode({
			mode: 'al',
			action: 'alContFav',
			titleBar: {
				title: 'Contigencias a Favor'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/cont/index_fav',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el numero de la contingencia' ).width('250');
				$mainPanel.find('[name=obj]').html( 'contigencia(s) a favor' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					alContFav.windowNew({
						clasif:"F"
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=tipo]').change(function(){
					$("#mainPanel .gridBody").empty();
					alContFav.loadData({page: 1,url: 'al/cont/lista'});
					if($mainPanel.find('[name=tipo] :selected').val()=="P"){
						$mainPanel.find('.gridHeader li').eq(2).html("Denunciante");
						$mainPanel.find('.gridHeader li').eq(3).html("Denunciado");
					}else{
						$mainPanel.find('.gridHeader li').eq(2).html("Demandante");
						$mainPanel.find('.gridHeader li').eq(3).html("Demandado");
					}
				});
				$mainPanel.find('#rbtnSelectP').click(function(){
					$mainPanel.find('.gridHeader li').eq(2).html("Denunciante");
					$mainPanel.find('.gridHeader li').eq(3).html("Denunciado");
				});
				$mainPanel.find('#rbtnSelectC').click(function(){
					$mainPanel.find('.gridHeader li').eq(2).html("Demandante");
					$mainPanel.find('.gridHeader li').eq(3).html("Demandado");
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alContFav.loadData({page: 1,url: 'al/cont/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alContFav.loadData({page: 1,url: 'al/cont/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alContFav.loadData({page: 1,url: 'al/cont/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.clasificacion = 'F';
		params.tipo = $('#mainPanel').find('[name=tipo] :selected').val();
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
					$li.eq(1).html( result.numero );
					$li.eq(2).html( result.demandante );
					$li.eq(3).html( result.demandado );	
					$li.eq(4).html( result.materia);
					$li.eq(5).html( result.monto.soles);
					$li.eq(6).html( result.monto.dolares);
					$li.eq(7).html( result.costo);
					$li.eq(8).html( result.fecha);
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result)
					.contextMenu("conMenAlContFav", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								//$(menu).remove();
								return menu;
							},
							bindings: {
								'conMenAlContFav_edi': function(t) {
									alContFav.windowEdit({id: K.tmp.data('id'), num : K.tmp.find('li:eq(1)').html(),clasif: K.tmp.data('data').clasificacion});
								},
								'conMenAlContFav_eli': function(t) {
									var data = {
											id: K.tmp.data('id')
										};
										K.sendingInfo();
										$.post('al/cont/delete',data,function(){
											K.clearNoti();
											K.notification({title: 'Contingencia eliminada',text: 'La Contingencia seleccionada ha sido eliminada con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenAlContFav_ver': function(t) {
									alContFav.windowDetails({id: K.tmp.data('id')});
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
						alContFav.loadData(params);
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
			id: 'windowDetAlCont'+p.id,
			title: 'Contingencia N&deg;: '+p.num,
			contentURL: 'al/cont/details_cont',
			icon: 'ui-icon-plusthick',
			width: 500,
			height: 380,
			buttons: {
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetAlCont'+p.id);
				K.block({$element: p.$w});
				$.post('al/cont/get','id='+p.id,function(data){
					K.unblock({$element: p.$w});
					p.$w.find('[name=numero]').html(data.numero);
					p.$w.find('[name=demandante]').html(data.demandante);
					p.$w.find('[name=demandado]').html(data.demandado);
					p.$w.find('[name=tipo]').html(alContFav.tipo[data.tipo]);
					p.$w.find('[name=materia]').html(data.materia);
					p.$w.find('[name=montosoles]').html(data.monto.soles);
					p.$w.find('[name=montodolares]').html(data.monto.dolares);
					p.$w.find('[name=estimacion]').html(data.costo);
					p.$w.find('[name=fecprob]').html(data.fecha);
					p.$w.find('[name=principio]').html(data.principio);
					p.$w.find('[name=observ]').html(data.observ);
					K.unblock({$element: p.$w});
				},'json');
				
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewAlCont',
			title: 'Nueva Contingencia',
			contentURL: 'al/cont/edit_cont',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.clasificacion = p.clasif;
					data.tipo = p.$w.find('[name=tipo] :selected').val();
					data.costo = p.$w.find('[name=estimacion]').val();
					if(data.costo==''){
						p.$w.find('[name=estimacion]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Estimaci&oacute;n !',type: 'error'});
					}
					data.numero = p.$w.find('[name=numero]').val();
					if(data.numero==''){
						p.$w.find('[name=numero]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero !',type: 'error'});
					}
					data.observ = p.$w.find('[name=obsv]').val();
					data.materia = p.$w.find('[name=materia]').val();
					if(data.materia==''){
						p.$w.find('[name=materia]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Materia !',type: 'error'});
					}
					data.monto = new Object;
					data.monto.soles = p.$w.find('[name=montosoles]').val();
					data.monto.dolares = p.$w.find('[name=montodolares]').val();
					data.principio = p.$w.find('[name=prin_juridico]').val();
					if(data.principio==''){
						p.$w.find('[name=prin_juridico]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Principio Juridico !',type: 'error'});
					}
					data.fecha = p.$w.find('[name=fecprob]').val();
					if(data.fecha==''){
						p.$w.find('[name=fecprob]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Fecha Probable !',type: 'error'});
					}
					data.demandante = p.$w.find('[name=demandante]').val();
					data.demandado = p.$w.find('[name=demandado]').val();
					data.trabajador = new Object;
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/cont/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Contingencia fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewAlCont');
				K.block({$element: p.$w});
				if(p.clasif=="F")p.$w.find('#cont').remove();
				else if(p.clasif=="C")p.$w.find('#fav').remove();
				p.$w.find('[name=numero]').focus();
				p.$w.find('[name=montosoles]').numeric().spinner();
				p.$w.find('[name=montosoles]').parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=montodolares]').numeric().spinner();
				p.$w.find('[name=montodolares]').parent().find('.ui-button').css('height','14px');
				//p.$w.find('[name=estimacion]').numeric().spinner();
				//p.$w.find('[name=estimacion]').parent().find('.ui-button').css('height','14px');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditAlCont'+p.id,
			title: 'Editar Contingencia',
			contentURL: 'al/cont/edit_cont',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.tipo = p.$w.find('[name=tipo] :selected').val();
					data.costo = p.$w.find('[name=estimacion]').val();
					if(data.costo==''){
						p.$w.find('[name=estimacion]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una Estimaci&oacute;n !',type: 'error'});
					}
					if(p.clasificacion=="F"){
						p.$w.find('#cont').remove();
					}else if(p.clasificacion=="C"){
						p.$w.find('#fav').remove();
					}
					data.numero = p.$w.find('[name=numero]').val();
					if(data.numero==''){
						p.$w.find('[name=numero]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero !',type: 'error'});
					}
					data.observ = p.$w.find('[name=obsv]').val();
					data.materia = p.$w.find('[name=materia]').val();
					if(data.materia==''){
						p.$w.find('[name=materia]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Materia !',type: 'error'});
					}
					data.monto = new Object;
					data.monto.soles = p.$w.find('[name=montosoles]').val();
					data.monto.dolares = p.$w.find('[name=montodolares]').val();
					data.principio = p.$w.find('[name=prin_juridico]').val();
					if(data.principio==''){
						p.$w.find('[name=prin_juridico]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Principio Juridico !',type: 'error'});
					}
					data.fecha = p.$w.find('[name=fecprob]').val();
					if(data.fecha==''){
						p.$w.find('[name=fecprob]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Fecha Probable !',type: 'error'});
					}
					data.demandante = p.$w.find('[name=demandante]').val();
					data.demandado = p.$w.find('[name=demandado]').val();
					data.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/cont/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La Contingencia fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditAlCont'+p.id);
				K.block({$element: p.$w});
				if(p.clasif=="F")p.$w.find('#cont').remove();
				else if(p.clasif=="C")p.$w.find('#fav').remove();
				p.$w.find('[name=numero]').focus();	
				p.$w.find('[name=montosoles]').numeric().spinner();
				p.$w.find('[name=montosoles]').parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=montodolares]').numeric().spinner();
				p.$w.find('[name=montodolares]').parent().find('.ui-button').css('height','14px');
				$.post('al/cont/get','id='+p.id,function(data){
					if(data.tipo=="C") p.$w.find('[name=tipo] option[value="C"]').attr('selected', 'selected');
					else if(data.tipo=="P")p.$w.find('[name=tipo] option[value="P"]').attr('selected', 'selected');
					else if(data.tipo=="A")p.$w.find('[name=tipo] option[value="A"]').attr('selected', 'selected');
					else if(data.tipo=="L")p.$w.find('[name=tipo] option[value="L"]').attr('selected', 'selected');
					else if(data.tipo=="T")p.$w.find('[name=tipo] option[value="T"]').attr('selected', 'selected');
					else if(data.tipo=="O")p.$w.find('[name=tipo] option[value="O"]').attr('selected', 'selected');	
					p.$w.find('[name=numero]').val(data.numero);
					p.$w.find('[name=materia]').val(data.materia);
					p.$w.find('[name=obsv]').val(data.observ);
					p.$w.find('[name=estimacion]').val(data.costo);
					p.$w.find('[name=montosoles]').val(data.monto.soles);
					p.$w.find('[name=montodolares]').val(data.monto.dolares);
					p.$w.find('[name=prin_juridico]').val(data.principio);
					p.$w.find('[name=fecprob]').val(data.fecha);
					p.$w.find('[name=demandante]').val(data.demandante);
					p.$w.find('[name=demandado]').val(data.demandado);
					K.unblock({$element: p.$w});
				},'json');
				
			}
		});
	}	
};