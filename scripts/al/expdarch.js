/*************************************************************************
  Expedientes */
alExpdArch = {
	init: function(){
		if($('#pageWrapper [child=alexpd]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('al/navg/expd',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="alexpd" />');
					$p.find("[name=alExpd]").after( $row.children() );
				}
				$p.find('[name=alExpd]').data('alexpd',$('#pageWrapper [child=alexpd]:first').data('alexpd'));
				$p.find('[name=alExpdActi]').click(function(){ alExpdActi.init(); });
				$p.find('[name=alExpdArch]').click(function(){ alExpdArch.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'al',
			action: 'alExpdArch',
			titleBar: {
				title: 'Expedientes Archivados'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/expd',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el numero del expediente' ).width('250');
				$mainPanel.find('[name=obj]').html( 'expediente(es) archivado(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					alExpdArch.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});					
				$mainPanel.find('[name=tipo]').change(function(){
					$("#mainPanel .gridBody").empty();
					if($(this).find('option:selected').val()=='P'){
						$mainPanel.find('.gridHeader li:eq(2)').html('Denunciante');
						$mainPanel.find('.gridHeader li:eq(3)').html('Denunciado');
					}else if($(this).find('option:selected').val()=='A'){
						$mainPanel.find('.gridHeader li:eq(2)').html('Solicitante');
						$mainPanel.find('.gridHeader li:eq(3)').html('Entidad');
					}else{
						$mainPanel.find('.gridHeader li:eq(2)').html('Demandante');
						$mainPanel.find('.gridHeader li:eq(3)').html('Demandado');
					}
					alExpdArch.loadData({page: 1,url: 'al/expd/lista'});
				});
				$mainPanel.find('[name=encargado]').change(function(){
					$("#mainPanel .gridBody").empty();
					alExpdArch.loadData({page: 1,url: 'al/expd/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alExpdArch.loadData({page: 1,url: 'al/expd/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alExpdArch.loadData({page: 1,url: 'al/expd/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alExpdArch.loadData({page: 1,url: 'al/expd/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.archivado = true;
		params.tipo = $('#mainPanel').find('[name=tipo] :selected').val();
		params.encargado = $('#mainPanel').find('[name=encargado] :selected').val();
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
					var demandante = '';
					var demandado = '';
					if($.type(result.demandante)==="string"){
						demandante = result.demandante;
					}else{
						demandante = ciHelper.enti.formatName(result.demandante);		
					}
					if($.type(result.demandado)==="string"){
						demandado = result.demandado;
					}else{
						demandado = ciHelper.enti.formatName(result.demandado);
					}
					$li.eq(2).html( demandante);
					$li.eq(3).html( demandado);
					$li.eq(4).html( result.materia );
					$li.eq(5).html( result.juzgado );
					$li.eq(6).html( (result.estado).substr(0,50) );
					if(result.inmueble)$li.eq(7).html( "Si" );
					else $li.eq(7).html( "No" );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result).dblclick(function(){
						alExpdActi.windowDetails({id: $(this).data('id'), numero : $(this).data('data').numero});
					}).contextMenu("conMenAlExpdAct", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								var dt = K.tmp.data('data');
								if(dt.inmueble==null)excep+='#conMenAlExpdAct_inm,';
								$(excep+"#conMenAlExpdAct_arch",menu).remove();
								return menu;
							},
							bindings: {
								'conMenAlExpdAct_ver': function(t) {
									alExpdActi.windowDetails({id: K.tmp.data('id'), numero : K.tmp.find('li:eq(1)').html()});
								},
								'conMenAlExpdAct_edi': function(t) {
									alExpdActi.windowEdit({id: K.tmp.data('id'), numero : K.tmp.find('li:eq(1)').html()});
								},
								'conMenAlExpdAct_inm': function(t) {
									inLoca.windowDetailsInEspa({
										id: K.tmp.data('data').inmueble._id,
										nomb: K.tmp.data('data').inmueble.local.nomb+" "+K.tmp.data('data').inmueble.descr
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
						alExpdArch.loadData(params);
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
			id: 'windowNewAlExpdArch',
			title: 'Nuevo Expediente Archivado',
			contentURL: 'al/expd/edit_expd',
			icon: 'ui-icon-plusthick',
			width: 650,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.numero = p.$w.find('[name=numero]').val();
					if(data.numero==''){
						p.$w.find('[name=numero]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero !',type: 'error'});
					}
					data.encargado = $('[name=encargado] :selected').val();
					data.observ = p.$w.find('[name=observ]').val();
					data.tipo = p.$w.find('[name=tipo] :selected').val();
					data.materia = p.$w.find('[name=materia]').val();
					if(data.materia==''){
						p.$w.find('[name=materia]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Materia !',type: 'error'});
					}
					data.juzgado = p.$w.find('[name=juzgado]').val();
					if(data.juzgado==''){
						p.$w.find('[name=juzgado]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Juzgado !',type: 'error'});
					}
					data.estado = p.$w.find('[name=estado]').val();
					if(data.estado==''){
						p.$w.find('[name=estado]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Estado !',type: 'error'});
					}
					data.ubicacion = p.$w.find('[name=ubicacion]').val();
					if(data.envio==''){
						p.$w.find('[name=ubicacion]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Ubicaci&oacute;n !',type: 'error'});
					}
					data.demandante = p.$w.find('[name=demandante]').val();
					data.demandado = p.$w.find('[name=demandado]').val();		
					if(p.$w.find('[name=FilIn] :checked').val()=="S"){
						var inmueble = p.$w.find('[name=inmueble]').data('data');	
						if(inmueble==null){
							p.$w.find('[name=inmueble]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Inmueble!',type: 'error'});
						}else{
							data.inmueble = inmueble;					
						}
					}
					data.trabajador_autor = new Object;
					data.trabajador_autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.archivado = {
						motivo:'Expediente Creado Manualmente para su Archivacion (Sistema SBPA)'
					};
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/expd/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Expediente fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewAlExpdArch');
				K.block({$element: p.$w});
				p.$w.find('[name=numero]').focus();	
				p.$w.find('[name=FilIn]').buttonset();
				p.$w.find('#rbtnSelectS').click(function(){
					/*inLoca.selectEsp({callback: function(data){
						var to_save = new Object;
						to_save._id = data._id.$id;
						to_save.descr = data.descr;
						to_save.local = new Object;
						to_save.local._id = data.ubic.local._id.$id;
						to_save.local.nomb = data.ubic.local.nomb;
						to_save.local.direc = data.ubic.local.direc;
						p.$w.find('[name=inmueble]').val(to_save._id).data('data',to_save);
						p.$w.find('[name=labinmu]').html(to_save.local.nomb+" "+to_save.descr);
					}});*/
					inLoca.selectEspAll({callback: function(data){
						var to_save = new Object;
						to_save._id = data._id.$id;
						to_save.descr = data.descr;
						to_save.local = new Object;
						to_save.local._id = data.ubic.local._id.$id;
						to_save.local.nomb = data.ubic.local.nomb;
						to_save.local.direc = data.ubic.local.direc;
						p.$w.find('[name=inmueble]').val(to_save._id).data('data',to_save);
						p.$w.find('[name=labinmu]').html(to_save.local.nomb+" "+to_save.descr+" "+data.ubic.ref);
					}});
				});
				p.$w.find('#rbtnSelectN').click(function(){
					p.$w.find('[name=labinmu]').html("");
					p.$w.find('[name=inmueble]').val("");
				});
				p.$w.find('#rbtnSelectS').click(function(){
					p.$w.find('[name=inmueble]').val("0");
				});
				K.unblock({$element: p.$w});
			}
		});
	} 
};