/*******************************************************************************
Propietarios */
cmProp = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmCuenPro',
			titleBar: {
				title: 'Cuentas: Propietarios'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cm/prop',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de entidad' ).width('250');
				$mainPanel.find('[name=obj]').html( 'entidad(es)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					//ciCreate.windowNewEntidad({roles: {propietario: {espacios: new Array(),ocupantes: new Array()}},callBack: cmProp.init});
					ciCreate.windowNewEntidad({roles: {propietario: true},callBack: cmProp.init});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13)
						$('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cmProp.loadData({page: 1,url: 'cm/prop/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cmProp.loadData({page: 1,url: 'cm/prop/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				var $li = $("#mainPanel .gridHeader li");
				$li.eq(0).attr('filter','_id');
				$li.eq(1).attr('filter','nomb');
				$("#mainPanel .gridHeader").find('li:eq(0),li:eq(1)').click( function(){
					$this = $(this);
					var order = 1;
					if($this.find('.ui-sorter').length>0){
						if($this.find('.ui-asc').length>0){
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
						}else{
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-n ui-asc" style="float:right"></span>');
							order = -1;
						}
					}else{
						$this.closest('.gridHeader').find('.ui-sorter').remove();
						$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
					}
					$("#mainPanel .gridBody").empty();
					cmProp.loadData({page: 1,url: 'cm/prop/lista',filter: $(this).attr("filter"),order: order});
				});
				cmProp.loadData({page: 1,url: 'cm/prop/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
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
					$li.eq(2).html( result.docident[0].num );
					if(result.domicilios != null)
					if(result.domicilios.length > 0 )
						$li.eq(3).html( result.domicilios[0].direccion );
					if(result.telefonos != null)
					if(result.telefonos.length > 0 )						
						$li.eq(4).html( result.telefonos[0].num );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('tipo_enti',result.tipo_enti).dblclick(function(){
						cmProp.windowDetails({
							id: $(this).data('id'),
							nomb: $(this).find('li:eq(1)').html()
						});
					}).data('data',result).contextMenu("conMenCmOper", {
							onShowMenu: function(e, menu) {
								var excep='';
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								//$('#conMenCmOper_ocu,#conMenCmOper_regiOcup',menu).remove();
								if(K.session.tasks["cm.ctas"]=="0")excep+='#conMenCmOper_pro,#conMenCmOper_editPro';
								else if(K.session.tasks["cm.oper.conc"]=="0")excep+='#conMenCmOper_coc,#conMenCmOper_anc';
								else if(K.session.tasks["cm.oper"]=="0")excep+='#conMenCmOper_asi,#conMenCmOper_cos,#conMenCmOper_inh,#conMenCmOper_traExt,#conMenCmOper_traInt,#conMenCmOper_col,#conMenCmOper_trs,#conMenCmOper_ana';
								$(excep+',#conMenCmOper_ocu,#conMenCmOper_regiOcup',menu).remove();
								K.tmp = $(e.target).closest('.item');
								return menu;
							},
							bindings: {
								'conMenCmOper_pro': function(t) {
									cmProp.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('li:eq(1)').html()
									});
								},
								'conMenCmOper_editPro': function(t){
									K.closeWindow('windowDetailEnti'+$this.data('id'));
									var params = new Object;
									params.id = K.tmp.data('data')._id.$id;
									params.nomb = K.tmp.find('li:eq(1)').html();
									params.tipo_enti = K.tmp.data('data').tipo_enti;
									params.data = K.tmp.data('data');
									params.callBack = cmProp.cbEdit;
									ciEdit.windowEditEntidad(params);
								},
								'conMenCmOper_coc': function(t) {
									cmOper.windowNewConcesion({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_asi': function(t) {
									cmOper.windowNewAsignacion({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_cos': function(t) {
									cmOper.windowNewConstruccion({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_inh': function(t) {
									cmOper.windowNewInhumacion({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_traExt': function(t) {
									cmOper.windowNewTraslado({entidad: K.tmp.data('data'), tipotras: 2});
								},
								'conMenCmOper_traInt': function(t) {
									cmOper.windowNewTraslado({entidad: K.tmp.data('data'), tipotras: 0});
								},
								'conMenCmOper_traCoa': function(t) {
									cmOper.windowNewTraslado({entidad: K.tmp.data('data'), tipotras: 1});
								},
								'conMenCmOper_col': function(t) {
									cmOper.windowNewColo({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_trs': function(t){
									cmOper.windowNewTrasp({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_anc': function(t){
									cmOper.windowAnulCon({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_ana': function(t){
									cmOper.windowAnulAsig({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_anu': function(t){
									cmOper.windowAnuOper({entidad: K.tmp.data('data')});
								},
								'conMenCmOper_ren': function(t){
									cmOper.windowReno({entidad: K.tmp.data('data')});
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
						cmProp.loadData(params);
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
		$.post('mg/enti/save',data,function(rpta){
			K.notification({title: ciHelper.titleMessages.regiAct,text: 'Propietario actualizado!'});
			K.closeWindow('windowEntiEdit'+data._id);
			if($.cookie('action')=='cmProp') cmProp.init();
		});
	},
	windowDel: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar propietario '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> el propietario <strong>'+p.nomb+'</strong>&#63;',
			icon: 'ui-icon-info',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function() {
					K.notification({text: 'Enviando informaci&oacute;n...'});
					$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					$.post('mg/enti/delete','_id='+p.id,function(){
						K.notification({title: ciHelper.titleMessages.regiEli,text: 'Entidad eliminada!'});
						K.closeWindow('windowDelete');
						K.closeWindow('windowDetailEnti'+p.id);
						if($.cookie('action')=='cmProp') cmProp.init();
					});
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	},
	windowDetails: function(p){
		var params = {
			id: 'windowDetailsProp'+p.id,
			title: 'Propietario: '+p.nomb,
			contentURL: 'cm/prop/details',
			icon: 'ui-icon-contact',
			width: 650,
			height: 380,
			onContentLoaded: function(){
				p.$w = $('#windowDetailsProp'+p.id);
				p.$w.find('label').css('color','#656565');
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				});
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$.post('cm/prop/get','_id='+p.id,function(data){
					p.$w.find('[name=spFecreg]').html(ciHelper.dateFormatLong(data.entidad.fecreg));
					p.$w.find('[name=spNomb]').html(data.entidad.nomb);
					p.$w.find('[name=spAppat]').html(data.entidad.appat);
					p.$w.find('[name=spApmat]').html(data.entidad.apmat);
					p.$w.find('[name=spIdent]').html(data.entidad.docident[0].num);
					if(data.entidad.fecnac!=null) p.$w.find('[name=spFecnac]').html( ciHelper.dateFormatLong(data.entidad.fecnac) );
					if(data.entidad.telefonos!=null) p.$w.find('[name=spTelef]').html( data.entidad.telefonos[0].num );
					if(data.entidad.domicilios!=null) p.$w.find('[name=spDirec]').html( data.entidad.domicilios[0].direccion );
					var espacios = data.entidad.roles.propietario.espacios;
					if(espacios!=null){
						for(var i=0; i<espacios.length; i++){
							var $row = p.$w.find('.tableRefEspa').clone();
							$row.attr('name',espacios[i]._id.$id);
							$row.find('[name=spEspaNomb]').html( espacios[i].nomb ).click(function(){
								cmEspa.showDetailsEspa({id: $(this).data('id'),modal: true});
							}).data('id',espacios[i]._id.$id).css({
								'text-decoration': 'underline',
								'cursor': 'pointer'
							});
							$row.removeClass('tableRefEspa').show();
							p.$w.find('.tableRefEspa').before($row);
							p.$w.find('.tableRefEspa').before('<hr>');
						}
					}
					var ocupantes = data.entidad.roles.propietario.ocupantes;
					if(ocupantes!=null){
						for(var i=0; i<ocupantes.length; i++){
							var $row = p.$w.find('.tableRefOcup').clone();
							$row.attr('name',ocupantes[i]._id.$id);
							$row.find('[name=spOcupNomb]').html( ocupantes[i].nomb+' '+ocupantes[i].appat+' '+ocupantes[i].apmat ).click(function(){
								cmOcup.windowDetails({id: $(this).data('data')._id.$id,nomb: $(this).data('data').nomb+' '+$(this).data('data').appat+' '+$(this).data('data').apmat,modal: true});
							}).data('data',ocupantes[i]).css({
								'text-decoration': 'underline',
								'cursor': 'pointer'
							});
							$row.removeClass('tableRefOcup').show();
							p.$w.find('.tableRefOcup').before($row);
							p.$w.find('.tableRefOcup').before('<hr>');
						}
					}
					if(data.opers!=null)
					for(var i=0; i<data.opers.length; i++){
						var $row = p.$w.find('.gridReference').clone();
						var result = data.opers[i];
						if(result.concesion != null)
							$row.find('li:eq(0)').html( 'Concesi&oacute;n' );
						if(result.construccion != null)
							$row.find('li:eq(0)').html( 'Construcci&oacute;n' );
						if(result.asignacion != null)
							$row.find('li:eq(0)').html( 'Asignaci&oacute;n' );
						if(result.adjuntacion != null)
							$row.find('li:eq(0)').html( 'Adjuntaci&oacute;n' );
						if(result.traspaso != null)
							$row.find('li:eq(0)').html( 'Traspaso' );
						if(result.inhumacion != null)
							$row.find('li:eq(0)').html( 'Inhumaci&oacute;n' );
						if(result.traslado != null)
							$row.find('li:eq(0)').html( 'Traslado' );
						if(result.colocacion != null)
							$row.find('li:eq(0)').html( 'Colocaci&oacute;n' );
						if(result.anular_asignacion != null)
							$row.find('li:eq(0)').html( 'Anular Asignaci&oacute;n' );
						if(result.anular_concesion != null)
							$row.find('li:eq(0)').html( 'Anular Concesi&oacute;n' );
						if(result.conversion != null)
							$row.find('li:eq(0)').html( 'Conversi&oacute;n' );
						$row.find('li:eq(1)').html( 'Registrado' );
						$row.find('li:eq(2)').html( ciHelper.dateFormatLong(result.fecreg) );
						$row.wrapInner('<a class="item" />');
						$row.find('a').click(function(){
							cmOper.showDetails({data: $(this).data('data')});
						}).data('data',result);
						p.$w.find('.gridBody:last').append($row.children());
						if(result.concesion!=null){
							if(p.$w.find('[name='+result.espacio._id.$id+']').length>0){
								var $table = p.$w.find('[name='+result.espacio._id.$id+']');
								$table.find('[name=spFecConce]').html( ciHelper.dateFormatLong(result.fecreg) );
								if(result.concesion.fecven!=null)
									$table.find('[name=spFecVenc]').html( ciHelper.dateFormatLong(result.concesion.fecven) );
								else
									$table.find('label:last').hide();
							}
						}else if(result.asignacion!=null){
							if(p.$w.find('[name='+result.espacio._id.$id+']').length>0){
								var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
								$table.find('[name=spFecAsig]').html( ciHelper.dateFormatLong(result.fecreg) );
								$table.find('[name=spEspaNomb]').html( result.espacio.nomb );
							}
						}else if(result.inhumacion!=null){
							if(p.$w.find('[name='+result.espacio._id.$id+']').length>0){
								var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
								if(result.ejecucion!=null)
									$table.find('[name=spFecInh]').html( ciHelper.dateFormatLong(result.ejecucion.fecini) );
							}
						}
					}
					if(data.extras!=null)
					for(var i=0; i<data.extras.length; i++){
						var result = data.extras[i];
						if(result.asignacion==true){
							if(p.$w.find('[name='+result.espacio._id.$id+']').length>0){
								var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
								$table.find('[name=spFecAsig]').html( ciHelper.dateFormatLong(result.fecreg) );
								$table.find('[name=spEspaNomb]').html( result.espacio.nomb );
							}
						}else if(result.traslado!=null){
							if(p.$w.find('[name='+result.espacio._id.$id+']').length>0){
								var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
								$table.find('label:eq(1)').html('Trasladado');
								$table.find('[name=spFecAsig]').html( ciHelper.dateFormatLong(result.fecreg) );
								$table.find('[name=spEspaNomb]').html( result.traslado.espacio_destino.nomb );
							}
						}else if(result.inhumacion!=null){
							if(p.$w.find('[name='+result.espacio._id.$id+']').length>0){
								var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
								if(result.ejecucion!=null)
									$table.find('[name=spFecInh]').html( ciHelper.dateFormatLong(result.ejecucion.fecini) );
							}
						}
					}
					p.$w.find('.ui-layout-center').css('overflow','hidden');
					K.unblock({$element: p.$w});
				},'json');
			}
		};
		if(p.modal==true){
			new K.Modal(params);
		}else new K.Window(params);
	}
};
define(
	['cm/espa','cm/mapa','cm/ocup','cm/pabe','cm/oper'],
	function(cmEspa,cmMapa,cmOcup,cmPabe,cmOper){
		return cmProp;
	}
);