/*************************************************************************
  Expedientes */
alExpdActi = {
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
				$p.find('[name=alExpdActi]').click(function(){ alExpdActi.init(); }).addClass('ui-state-highlight');
				$p.find('[name=alExpdArch]').click(function(){ alExpdArch.init(); });
			},'json');
		}
		K.initMode({
			mode: 'al',
			action: 'alExpdActi',
			titleBar: {
				title: 'Expedientes Activos'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'al/expd',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el número o nombre del demandado' ).width('200');
				$mainPanel.find('[name=obj]').html( 'expediente(es) activo(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					alExpdActi.windowNew();
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
					alExpdActi.loadData({page: 1,url: 'al/expd/lista'});
				});
				$mainPanel.find('[name=encargado]').change(function(){
					$("#mainPanel .gridBody").empty();
					alExpdActi.loadData({page: 1,url: 'al/expd/lista'});
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						alExpdActi.loadData({page: 1,url: 'al/expd/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						alExpdActi.loadData({page: 1,url: 'al/expd/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				alExpdActi.loadData({page: 1,url: 'al/expd/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.archivado = false;
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
					//$li.eq(6).html( result.estado );
					$li.eq(6).html( result.estado.substr(0,400)+'[...]' );
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
								if(dt.inmueble==null)excep+='#conMenAlExpdAct_inm';
								$(excep+",#conMenList_imp,#conMenList_about",menu).remove();
								return menu;
							},
							bindings: {
								'conMenAlExpdAct_eli': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de eliminar expediente ?',
										function () {
											$.post('al/expd/delete',{id:K.tmp.data('id')},function(){
												alExpdActi.init();
											});
										},
										function () {
											//nothing
										}										
									);
									
								},
								'conMenAlExpdAct_edi': function(t) {
									alExpdActi.windowEdit({id: K.tmp.data('id'), numero : K.tmp.find('li:eq(1)').html()});
								},
								'conMenAlExpdAct_inm': function(t) {
									inLoca.windowDetailsInEspa({
										id: K.tmp.data('data').inmueble._id,
										nomb: K.tmp.data('data').inmueble.local.nomb+" "+K.tmp.data('data').inmueble.descr
									});							
								},
								'conMenAlExpdAct_ver': function(t) {									
									alExpdActi.windowDetails({id: K.tmp.data('id'), numero : K.tmp.find('li:eq(1)').html()});
								},
								'conMenAlExpdAct_arch': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de archivar expediente ?',
										function () {
											alExpdActi.windowArchivar({id: K.tmp.data('id'), numero : K.tmp.find('li:eq(1)').html()});
										},
										function () {
											//nothing
										}										
									);		
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
						alExpdActi.loadData(params);
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
			id: 'windowDetailsAlExpd'+p.id,
			title: 'Expediente N '+p.numero,
			contentURL: 'al/expd/details_expd',
			icon: 'ui-icon-plusthick',
			width: 400,
			height: 350,
			buttons: {
				"Imprimir":function(){
					var url = 'al/expd/print?id='+p.id;
					K.windowPrint({
						id:'windowAlExpdPrint',
						title: "Reporte / Informe",
						url: url
					});
				},
				"Historico": function(){
					alExpdActi.windowHistorico({id:p.id,numero:p.numero});
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsAlExpd'+p.id);
				K.block({$element: p.$w});
				$.post('al/expd/get','id='+p.id,function(data){
					p.$w.find('[name=numero]').html(data.numero);
					p.$w.find('[name=materia]').html(data.materia);
					p.$w.find('[name=juzgado]').html(data.juzgado);
					p.$w.find('[name=estado]').html(data.estado);
					if(data.tipo=="C"){
						p.$w.find('[name=tipo]').html("Civiles");
					}else if(data.tipo=="P"){
						p.$w.find('[name=tipo]').html("Penales");
						p.$w.find('label:eq(2)').html("<b>Denunciante</b>");
						p.$w.find('label:eq(3)').html("<b>Denunciado</b>");
					}else if(data.tipo=="A"){
						p.$w.find('[name=tipo]').html("Administrativos");
						p.$w.find('label:eq(2)').html("<b>Solicitante</b>");
						p.$w.find('label:eq(3)').html("<b>Entidad</b>");
					}else if(data.tipo=="L"){
						p.$w.find('[name=tipo]').html("Laborales");
					}else if(data.tipo=="T"){
						p.$w.find('[name=tipo]').html("Contensioso Administrativos");
					}else if(data.tipo=="S"){
						p.$w.find('[name=tipo]').html("Sucesion Intestada");
					}		
					p.$w.find('[name=ubicacion]').html(data.ubicacion);
					p.$w.find('[name=observ]').html(data.observ);
					if($.type(data.demandante)==='string'){
						p.$w.find('[name=demandante]').html(data.demandante);
					}else{	
						p.$w.find('[name=demandante]').html( ciHelper.enti.formatName(data.demandante));
					}
					if($.type(data.demandado)==='string'){
						p.$w.find('[name=demandado]').html(data.demandado);
					}else{
						p.$w.find('[name=demandado]').html( ciHelper.enti.formatName(data.demandado));
					}
					p.$w.find('[name=resultdemandante]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					p.$w.find('[name=resultdemandado]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					if(data.inmueble){
						p.$w.find('[name=inmueble]').html(data.inmueble.descr+" - "+data.inmueble.local.direc);
					}else{
						p.$w.find('[name=inmueble]').html("No");
					}
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowNewAlTipoExpd',
			title: 'Nuevo Expediente',
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
					/** Data para el Historico */
					var data2 = new Object;
					data2.numero = data.numero;
					data2.estado = data.estado;		
					data2.materia = data.materia;
					data2.juzgado = data.juzgado;
					data2.ubicacion = data.ubicacion;
					data2.observ = data.observ;
					data2.trabajador = new Object;
					data2.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/expd/save_hist',data2,function(){
						K.clearNoti();//Historico guardado
					});
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
				p.$w = $('#windowNewAlTipoExpd');
				K.block({$element: p.$w});
				p.$w.find('[name=numero]').focus();	
				p.$w.find('[name=FilIn]').buttonset();
				p.$w.find('#rbtnSelectS').click(function(){
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
	},
	windowEdit: function(p){
		new K.Window({
			id: 'windowEditAlExpd'+p.id,
			title: 'Editar Expediente :'+p.numero,
			contentURL: 'al/expd/edit_expd2',
			icon: 'ui-icon-plusthick',
			width: 550,
			height: 450,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.observ = p.$w.find('[name=observ]').val();
					data.materia = p.$w.find('[name=materia]').val();
					data.demandante = p.$w.find('[name=demandante]').val();
					data.demandado = p.$w.find('[name=demandado]').val();		
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
					if(data.ubicacion==''){
						p.$w.find('[name=ubicacion]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Ubicaci&oacute;n !',type: 'error'});
					}
					data.trabajador_autor = ciHelper.enti.dbTrabRel(K.session.enti);
					if(p.$w.find('[name=FilIn] :checked').val()=="S"){
						var inmueble = p.$w.find('[name=inmueble]').data('data');	
						if(inmueble==null){
							p.$w.find('[name=inmueble]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un Inmueble!',type: 'error'});
						}else{
							data.inmueble = inmueble;					
						}
					}
					/** Data para el Historico */
					var data2 = new Object;
					data2.numero = p.numero;
					data2.estado = data.estado;		
					data2.materia = data.materia;
					data2.juzgado = data.juzgado;
					data2.ubicacion = data.ubicacion;
					data2.observ = data.observ;
					data2.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/expd/save_hist',data2,function(){
						K.clearNoti();//Historico guardado
					});
					$.post('al/expd/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Expediente fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditAlExpd'+p.id);
				K.block({$element: p.$w});	
				$.post('al/expd/get','id='+p.id,function(data){
					p.$w.find('[name=numero]').html(data.numero);
					p.$w.find('[name=materia]').val(data.materia);
					p.$w.find('[name=juzgado]').val(data.juzgado);
					if($.type(data.demandante)==='string'){
						p.$w.find('[name=demandante]').val(data.demandante);
					}else{	
						p.$w.find('[name=demandante]').val( ciHelper.enti.formatName(data.demandante));
					}
					if($.type(data.demandado)==='string'){
						p.$w.find('[name=demandado]').val(data.demandado);
					}else{
						p.$w.find('[name=demandado]').val( ciHelper.enti.formatName(data.demandado));
					}
					p.$w.find('[name=estado]').val(data.estado);
					if(data.tipo=="C")p.$w.find('[name=tipo]').html("Civil");
					else if(data.tipo=="P")p.$w.find('[name=tipo]').html("Penales");
					else if(data.tipo=="A")p.$w.find('[name=tipo]').html("Administrativos");
					else if(data.tipo=="L")p.$w.find('[name=tipo]').html("Laborales");
					else if(data.tipo=="T")p.$w.find('[name=tipo]').html("Contensioso Administrativo");
					p.$w.find('[name=ubicacion]').val(data.ubicacion);
					p.$w.find('[name=observ]').val(data.observ);
					if(data.inmueble){
						p.$w.find('[name=inmueble]').val(data.inmueble._id).data('data',data.inmueble);
						p.$w.find('[name=labinmu]').html(data.inmueble.local.nomb+" - "+data.inmueble.descr);
						p.$w.find('#rbtnSelectS').attr('checked','checked')
					}
					p.$w.find('[name=FilIn]').buttonset();
					p.$w.find('#rbtnSelectS').click(function(){
						/*inLoca.windowSelectLoca({callback: function(data){
								p.$w.find('[name=inmueble]').val(data._id.$id).data('data',data);
								p.$w.find('[name=labinmu]').html(data.nomb);
							p.$w.find('[name=resultinmueble]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
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
					p.$w.find('[name=lastdata]').data('data',data);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowArchivar: function(p){
		new K.Window({
			id: 'windowArchiAlExpd'+p.id,
			title: 'Archivar Expediente :'+p.numero,
			contentURL: 'al/expd/arch_expd',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 150,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					data.archivado = new Object;
					data.archivado.fecini = p.$w.find('[name=fecini]').val();
					if(data.archivado.fecini==''){
						p.$w.find('[name=fecini]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Fecha Inicio !',type: 'error'});
					}
					data.archivado.fecfin = p.$w.find('[name=fecfin]').val();
					if(data.archivado.fecfin==''){
						p.$w.find('[name=fecfin]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Fecha Fin !',type: 'error'});
					}
					data.archivado.motivo = p.$w.find('[name=motivo]').val();
					if(data.archivado.motivo==''){
						p.$w.find('[name=motivo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe llenar el campo Motivo !',type: 'error'});
					}
					/** Data para el Historico */
					expd = p.$w.find('[name=motivo]').data('data');
					var data2 = new Object;
					data2.numero = expd.numero;
					data2.estado = expd.estado;		
					data2.materia = expd.materia;
					data2.juzgado = expd.juzgado;
					data2.ubicacion = expd.ubicacion;
					data2.observ = expd.observ;
					data2.trabajador = ciHelper.enti.dbTrabRel(K.session.enti);
					data2.archivado = new Object;
					data2.archivado.fecini = data.archivado.fecini;
					data2.archivado.fecfin = data.archivado.fecfin;
					data2.archivado.motivo = data.archivado.motivo;
					K.sendingInfo();
					p.$w.dialog("widget").find('.ui-dialog-buttonpane button').button('disable');
					$.post('al/expd/save_hist',data2,function(){
						K.clearNoti();//Historico guardado
					});
					K.sendingInfo();
					$.post('al/expd/archivar',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Expediente fue Archivado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowArchiAlExpd'+p.id);
				K.block({$element: p.$w});	
				p.$w.find('[name=fecini]').focus();
				p.$w.find('[name=fecini]').datepicker();
				p.$w.find('[name=fecfin]').datepicker();
				$.post('al/expd/get','id='+p.id,function(data){
					p.$w.find('[name=motivo]').data('data',data);
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowHistorico: function(p){
		p.searchHist = function(params){
			params.numero = p.numero;
			params.page = (params.page) ? params.page : 1;
			$.post('al/expd/lista_expd_hist',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
						$li = p.$w.find('table');
						if(data.items[0].trabajador.tipo_enti=="E"){
							$li.find('[name=autor]').html( data.items[0].trabajador.nomb );	
						}else{
							$li.find('[name=autor]').html( data.items[0].trabajador.appat+ " "+data.items[0].trabajador.apmat+", "+data.items[0].trabajador.nomb );	
						}						
						p.$w.find('[name=fecac]').html(ciHelper.dateFormat(data.items[0].fecactualizacion));
						$li.find('[name=materia]').html( data.items[0].materia );
						$li.find('[name=juzgado]').html( data.items[0].juzgado );
						$li.find('[name=estado]').html( data.items[0].estado );
						$li.find('[name=ubicacion]').html( data.items[0].ubicacion );
						$li.find('[name=observ]').html( data.items[0].observ );
						if(data.items[0].archivado){
							$li.find('[name=arch]').show();
							$li.find('[name=motivo]').html( data.items[0].archivado.motivo );
							$li.find('[name=fecini]').html(ciHelper.dateFormat(data.items[0].archivado.fecini));
							$li.find('[name=fecfin]').html(ciHelper.dateFormat(data.items[0].archivado.fecfin));
						}else{
							$li.find('[name=arch]').hide();
						}
					
					$next = p.$w.find("[name=next]").unbind();
					$back = p.$w.find("[name=back]").unbind();
					$first = p.$w.find("[name=first]").unbind();
					$last = p.$w.find("[name=last]").unbind();
					//$last.button( "option", "disabled", false );
					if(params.page==data.paging.total_pages){
						$last.button( "option", "disabled", true );
						$back.button( "option", "disabled", false );
					}else{
						$last.button( "option", "disabled", false );
						$last.click( function(){
							params.page = parseFloat(data.paging.total_pages);
							p.searchHist( params );							
						});
					}
								
					if(params.page==1){
						$back.button( "option", "disabled", true );
						$first.button( "option", "disabled", true );					
					}else{
						$first.button( "option", "disabled", false );
						$back.click( function(){
							params.page = parseFloat(data.paging.page) -1;
							p.searchHist( params );
						});
						$first.click( function(){
							params.page = 1;
							p.searchHist( params );
						});
					}	
					if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
						$next.click( function(){
							params.page = parseFloat(data.paging.page) + 1;
							p.searchHist( params );
							$back.button( "option", "disabled", false );
						});
						$next.button( "option", "disabled", false );
					}else 
						$next.button( "option", "disabled", true );						
				} else {
					p.$w.find("[name=first]").button( "option", "disabled", true );
					p.$w.find("[name=last]").button( "option", "disabled", true );
					p.$w.find("[name=next]").button( "option", "disabled", true );
					p.$w.find("[name=back]").button( "option", "disabled", true );
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Window({
			id: 'windowHistAlExpd'+p.id,
			title: 'Historico del Expediente N&deg;:'+p.numero,
			contentURL: 'al/expd/hist_expd',
			icon: 'ui-icon-plusthick',
			width: 450,
			height: 450,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowHistAlExpd'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=first]').button();
				p.$w.find('[name=back]').button();
				p.$w.find('[name=next]').button();
				p.$w.find('[name=last]').button();
				$.post('al/expd/get','id='+p.id,function(data){
					p.$w.find('[name=numero]').html(data.numero);
					p.$w.find('[name=encargado]').html(data.encargado);
					if(data.encargado=="B"){
						p.$w.find('[name=encargado]').html("Sociedad de Beneficencia Publica de Arequipa");
					}else if(data.encargado=="C"){
						p.$w.find('[name=encargado]').html("Contraloria");
					}else if(data.encargado=="M"){
						p.$w.find('[name=encargado]').html("Mimdes");
					}
					if($.type(data.demandante)==='string'){
						p.$w.find('[name=demandante]').html(data.demandante);
					}else{	
						p.$w.find('[name=demandante]').html( ciHelper.enti.formatName(data.demandante));
					}
					if($.type(data.demandado)==='string'){
						p.$w.find('[name=demandado]').html(data.demandado);
					}else{
						p.$w.find('[name=demandado]').html( ciHelper.enti.formatName(data.demandado));
					}
				},'json');
				p.searchHist({page: 1});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.archivado = false;
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('al/expd/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.numero );		
						if(result.demandante.tipo_enti=="P"){
							$li.eq(1).html( result.demandante.appat+" "+result.demandante.apmat+", "+result.demandante.nomb );
						}else{
							$li.eq(1).html( result.demandante.nomb );
						}
						if(result.demandado.tipo_enti=="P"){
							$li.eq(2).html( result.demandado.appat+" "+result.demandado.apmat+", "+result.demandado.nomb );
						}else{
							$li.eq(2).html( result.demandado.nomb );
						}
						$li.eq(3).html( result.estado );
						if(result.archivado)$li.eq(4).html( "Archivado");
						else $li.eq(4).html( "Activo" );
						$row.wrapInner('<a class="item" href="javascript: void(0);" />');
						$row.find('a').data('id',result._id.$id).dblclick(function(){
							p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
						}).data('data',result);
						p.$w.find(".gridBody").append( $row.children() );
					}
					p.$w.find('[name=showing]').html( p.$w.find(".gridBody a").length );
					p.$w.find('[name=founded]').html( data.paging.total_items );
					
					$moreresults = p.$w.find("[name=moreresults]").unbind();
					if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
						$moreresults.click( function(){
							params.page = parseFloat(data.paging.page) + 1;
							p.search( params );
							//$(this).button( "option", "disabled", true );
						});
						$moreresults.button( "option", "disabled", false );
					}else
						$moreresults.button( "option", "disabled", true );
				} else {
					p.$w.find("[name=moreresults]").button( "option", "disabled", true );
					$('[name=showing]').html( 0 );
					$('[name=founded]').html( data.paging.total_items );
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowSelectAlExpd',
			title: 'Seleccionar Expediente',
			contentURL: 'al/expd/select_expd',
			icon: 'ui-icon-search',
			width: 730,
			height: 400,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Componente!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectAlExpd');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('370px');
				p.$w.find("[name=moreresults]").button({icons: {primary: 'ui-icon-triangle-1-s'}});
				p.$w.find("[name=buscar]").keyup(function(e){
					if(e.keyCode == 13) p.$w.find('[name=btnBuscar]').click();
				});
				p.$w.find('[name=btnBuscar]').click(function(){
					p.$w.find('.gridBody').empty();
					p.search({page: 1});
				}).button({icons: {primary: 'ui-icon-search'},text: false}).click();
			}
		});
	}
};