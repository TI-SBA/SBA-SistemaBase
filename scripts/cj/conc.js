/***************************conceptos **************************/
cjConc = {
	states: {
		H: {
			descr: "Habilitado",
			color: "#006532"
		},
		D: {
			descr: "Deshabilitado",
			color: "#CCCCCC"
		}
	},
	dbRel: function(data){
		if(data==null)
			return console.warning('El Servicio no Existe');
		else
			return {
				_id: data._id.$id,
				nomb: data.nomb,
				cod: data.cod
			};
	},
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjConc',
			titleBar: {
				title: 'Conceptos'
			}
		});

		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cj/conc',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de concepto' ).width('250');
				$mainPanel.find('[name=obj]').html( 'concepto(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					cjConc.windowNew({tipo: '559'});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						cjConc.loadData({page: 1,url: 'cj/conc/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						cjConc.loadData({page: 1,url: 'cj/conc/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				cjConc.loadData({page: 1,url: 'cj/conc/lista'});
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
					$li.eq(0).css('background',cjConc.states[result.estado].color).addClass('vtip').attr('title',cjConc.states[result.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( result.nomb );
					$li.eq(3).html( result.cod );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						if($(this).data('estado')=='D') return K.notification({title: 'Concepto Deshabilitado',text: 'Debe habilitar para acceder a la edici&oacute;n!',type: 'info'});
						cjConc.windowEdit({id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html(),tipo: params.tipo});
					}).data('estado',result.estado).contextMenu("conMenPeConc", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								//$('#conMenListEd_ver',menu).remove();
								if(K.tmp.data('estado')=='H') $('#conMenPeConc_hab',menu).remove();
								else $('#conMenPeConc_edi,#conMenPeConc_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenPeConc_ver': function(t) {
									cjConc.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenPeConc_edi': function(t) {
									cjConc.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html(),tipo: params.tipo});
								},
								'conMenPeConc_hab': function(t) {
									K.sendingInfo();
									$.post('cj/conc/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
										K.clearNoti();
										K.notification({title: 'Concepto Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenPeConc_des': function(t) {
									K.sendingInfo();
									$.post('cj/conc/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
										K.clearNoti();
										K.notification({title: 'Concepto Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						cjConc.loadData(params);
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
		new K.Modal({
			id: 'windowNewConc',
			title: 'Nuevo Concepto',
			contentURL: 'cj/conc/edit',
			icon: 'ui-icon-plusthick',
			width: 480,
			height: 350,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val(),
						cod: p.$w.find('[name=cod]').val().replace(/\s/g,"_"),
						igv: p.$w.find('[name=igv] :selected').val(),
						servicios: []
					};
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de Concepto!',type: 'error'});
					}
					if(data.cod==''){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo para el Concepto!',type: 'error'});
					}else if(data.cod.indexOf('__VALUE__')!=-1){
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'El c&oacute;digo de concepto no puede contener la variable reservada <i>__VALUE__</i>!',type: 'error'});
					}
					if(p.$w.find('[name=rbtnEnla]:checked').val()=='CL'){
						if(p.$w.find('[name=clasif]').data('data')==null){
							p.$w.find('[name=btnClasi]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un clasificador de gastos para el Concepto!',type: 'error'});
						}else{
							var tmp = p.$w.find('[name=clasif]').data('data');
							data.clasificador = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb
							};
						}
						if(p.$w.find('[name=cuenta]').data('data')==null){
							p.$w.find('[name=btnCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable para el Concepto!',type: 'error'});
						}else{
							var tmp = p.$w.find('[name=cuenta]').data('data');
							data.cuenta = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								descr: tmp.descr
							};
						}
					}else{
						if(p.$w.find('[name=cuenta]').data('data')==null){
							p.$w.find('[name=btnCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable para el Concepto!',type: 'error'});
						}else{
							var tmp = p.$w.find('[name=cuenta]').data('data');
							data.cuenta = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								descr: tmp.descr
							};
						}
					}
					if(p.$w.find('[name=form]').data('data')==null){
						p.$w.find('[name=btnForm]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una f&oacute;rmula para el Concepto!',type: 'error'});
					}else data.formula = p.$w.find('[name=form]').data('data');
					for(var i=0,j=p.$w.find('[name=serv]').length-1; i<j; i++){
						var serv = p.$w.find('[name=serv]').eq(i).data('data');
						if(serv==null){
							p.$w.find('[name=btnServ]').eq(i).click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un servicio!',type: 'error'});
						}
						data.servicios.push(serv._id.$id);
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/conc/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Concepto fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnServ]').die('click');
				p.$w.find('[name=btnAgre]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewConc');
				p.$w.find('tr:eq(4)').buttonset();

				p.$w.find('#rbtnEnla1').click(function(){
					//p.$w.find('label:eq(7)').html('Clasificador');
					p.$w.find('[name=btnClasi]').closest('tr').show();
				});
				p.$w.find('#rbtnEnla2').click(function(){
					//p.$w.find('label:eq(7)').html('Cuenta');
					p.$w.find('[name=btnClasi]').closest('tr').hide();
				});
				p.$w.find('[name=btnClasi]').click(function(){
					prClas.windowSelect({callback: function(data){
						p.$w.find('[name=clasif]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnForm]').click(function(){
					cjConc.windowForm({
						tipo: p.tipo,
						val: p.$w.find('[name=form]').data('data'),
						callback: function(data){
							p.$w.find('[name=form]').html(data).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-icon-calculator'}});
				p.$w.find('[name=btnServ]').live('click',function(){
					var $row = $(this).closest('.item');
					mgServ.windowSelect({callback: function(data){
						$row.find('[name=serv]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name=btnAgre]').live('click',function(){
					var $row = p.$w.find('.gridReference').clone();
					$row.find('[name=btnServ]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody").append( $row.children() );
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('.gridBody .item').length<1){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('[name=btnServ]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				var $row = p.$w.find('.gridReference').clone();
				$row.find('[name=btnServ]').button({icons: {primary: 'ui-icon-search'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item" />');
	        	p.$w.find(".gridBody").append( $row.children() );
				p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({
			id: 'windowEditConc'+p.id,
			title: 'Editar Concepto: '+p.nomb,
			contentURL: 'cj/conc/edit',
			icon: 'ui-icon-pencil',
			width: 480,
			height: 350,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						nomb: p.$w.find('[name=nomb]').val(),
						descr: p.$w.find('[name=descr]').val(),
						igv: p.$w.find('[name=igv] :selected').val(),
						servicios: []
					};
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre de Concepto!',type: 'error'});
					}
					if(p.$w.find('[name=rbtnEnla]:checked').val()=='CL'){
						if(p.$w.find('[name=clasif]').data('data')==null){
							p.$w.find('[name=btnClasi]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un clasificador de gastos para el Concepto!',type: 'error'});
						}else{
							var tmp = p.$w.find('[name=clasif]').data('data');
							data.clasificador = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								nomb: tmp.nomb
							};
						}
						if(p.$w.find('[name=cuenta]').data('data')==null){
							p.$w.find('[name=btnCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable para el Concepto!',type: 'error'});
						}else{
							var tmp = p.$w.find('[name=cuenta]').data('data');
							data.cuenta = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								descr: tmp.descr
							};
						}
					}else{
						if(p.$w.find('[name=cuenta]').data('data')==null){
							p.$w.find('[name=btnCuen]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta contable para el Concepto!',type: 'error'});
						}else{
							var tmp = p.$w.find('[name=cuenta]').data('data');
							data.cuenta = {
								_id: tmp._id.$id,
								cod: tmp.cod,
								descr: tmp.descr
							};
						}
					}
					if(p.$w.find('[name=form]').data('data')==null){
						p.$w.find('[name=btnForm]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una f&oacute;rmula para el Concepto!',type: 'error'});
					}else data.formula = p.$w.find('[name=form]').data('data');
					for(var i=0,j=p.$w.find('[name=serv]').length-1; i<j; i++){
						var serv = p.$w.find('[name=serv]').eq(i).data('data');
						if(serv==null){
							p.$w.find('[name=btnServ]').eq(i).click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un servicio!',type: 'error'});
						}
						data.servicios.push(serv._id.$id);
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/conc/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'El Concepto fue actualizado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnServ]').die('click');
				p.$w.find('[name=btnAgre]').die('click');
				p.$w.find('[name=btnEli]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowEditConc'+p.id);
				K.block({$element: p.$w});
				p.$w.find('#rbtnEnla1').click(function(){
					//p.$w.find('label:eq(7)').html('Clasificador');
					p.$w.find('[name=btnClasi]').closest('tr').show();
				});
				p.$w.find('#rbtnEnla2').click(function(){
					//p.$w.find('label:eq(7)').html('Cuenta');
					p.$w.find('[name=btnClasi]').closest('tr').hide();
				});
				p.$w.find('[name=btnClasi]').click(function(){
					prClas.windowSelect({callback: function(data){
						p.$w.find('[name=clasif]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({callback: function(data){
						p.$w.find('[name=cuenta]').html(data.cod).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});







				p.$w.find('[name=btnForm]').click(function(){
					cjConc.windowForm({
						tipo: p.tipo,
						val: p.$w.find('[name=form]').data('data'),
						callback: function(data){
							p.$w.find('[name=form]').html(data).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-icon-calculator'}});
				p.$w.find('[name=btnServ]').live('click',function(){
					var $row = $(this).closest('.item');
					mgServ.windowSelect({callback: function(data){
						$row.find('[name=serv]').html(data.nomb).data('data',data);
					}});
				});
				p.$w.find('[name=btnAgre]').live('click',function(){
					var $row = p.$w.find('.gridReference').clone();
					$row.find('[name=btnServ]').button({icons: {primary: 'ui-icon-search'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" />');
		        	p.$w.find(".gridBody").append( $row.children() );
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('.gridBody .item').length<1){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('[name=btnServ]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
					}
					p.$w.find('.gridBody [name=btnAgre]').remove();
					p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
					p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				});
				$.post('cj/conc/get','id='+p.id,function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=cod]').replaceWith('<span>'+data.cod+'</span>');
					p.$w.find('[name=form]').html(data.formula).data('data',data.formula);
					p.$w.find('[name=igv]').val(data.igv);
					if(data.clasificador!=null){
						p.$w.find('input:radio[name=rbtnEnla]').attr('checked',false);
						p.$w.find('input:radio[name=rbtnEnla]:nth(0)').attr('checked',true);
						p.$w.find('[name=btnClasi]').closest('tr').show();
						p.$w.find('[name=clasif]').html(data.clasificador.cod).data('data',data.clasificador);
						if(data.cuenta!=null)
							p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
					}else{
						p.$w.find('input:radio[name=rbtnEnla]').attr('checked',false);
						p.$w.find('input:radio[name=rbtnEnla]:nth(1)').attr('checked',true);
						p.$w.find('[name=cuenta]').html(data.cuenta.cod).data('data',data.cuenta);
						p.$w.find('[name=btnClasi]').closest('tr').hide();
					}
					p.$w.find('tr:eq(4)').buttonset();
					//p.$w.find('tr:eq(4)').buttonset('refresh');
					for(var i=0,j=data.servicios.length; i<j; i++){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('[name=serv]').html(data.servicios[i].nomb).data('data',data.servicios[i])
						$row.find('[name=btnServ]').button({icons: {primary: 'ui-icon-search'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
						p.$w.find('.gridBody [name=btnAgre]').remove();
						p.$w.find('.gridBody li:last').append('<button name="btnAgre">Agregar</button>');
						p.$w.find('.gridBody [name=btnAgre]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowForm: function(p){
		new K.Modal({
			id: 'windowCreateForm',
			title: 'Editor de F&oacute;rmulas',
			contentURL: 'cj/conc/form',
			icon: 'ui-icon-calculator',
			width: 610,
			height: 450,
			buttons: {
				"Actualizar": function(){
					var val = p.$w.find('[name=cod]').val();
					if(val!=''){
						if(p.$w.find('[name=confir]').data('check')==true){
							p.callback(val);
						}else{
							p.$w.find('[name=cod]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una f&oacute;rmula v&aacute;lida!',type: 'error'});
						}
					}else{
						p.$w.find('[name=cod]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una f&oacute;rmula!',type: 'error'});
					}
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowCreateForm');
				var __VALUE__ = 42;
				if(p.val!=null) p.$w.find('[name=cod]').val(p.val);
				K.block({$element: p.$w});
				p.$w.find('[name=tabs]').tabs();
				p.$w.find('#tabs-1,#tabs-2,#tabs-3,#tabs-4').css('padding','0px');
				$.post('cj/conc/all_var',function(data){
					if(data.conc!=null){
						for(p.i=0; p.i<data.conc.length; p.i++){
							//eval('var '+data[p.i].cod+' = function(){'+data[p.i].formula+'};');
							//eval('var '+data.conc[p.i].cod+' = 10100;'); //comment 17012020 jovad
							var $row = p.$w.find('#tabs-1 .gridReference').clone();
							$row.find('li:eq(0)').html(data.conc[p.i].nomb);
							$row.find('li:eq(1)').html(data.conc[p.i].cod);
							$row.wrapInner('<a class="item">');
							$row.find('a').dblclick(function(){
								p.$w.find('[name=cod]').val(p.$w.find('[name=cod]').val()+$(this).data('cod')).keyup();
							}).data('cod',data.conc[p.i].cod);
							p.$w.find('#tabs-1 .gridBody').append($row.children());
						}
					}
					if(data.vari!=null){
						for(p.i=0; p.i<data.vari.length; p.i++){
							eval('var '+data.vari[p.i].cod+' = 10100;');
							var $row = p.$w.find('#tabs-2 .gridReference').clone();
							$row.find('li:eq(0)').html(data.vari[p.i].nomb);
							$row.find('li:eq(1)').html(data.vari[p.i].cod);
							$row.wrapInner('<a class="item">');
							$row.find('a').dblclick(function(){
								p.$w.find('[name=cod]').val(p.$w.find('[name=cod]').val()+$(this).data('cod')).keyup();
							}).data('cod',data.vari[p.i].cod);
							p.$w.find('#tabs-2 .gridBody').append($row.children());
						}
					}
					if(data.defs!=null){
						var SERV = {};
						for(p.i=0; p.i<data.defs.length; p.i++){
							eval('SERV.'+data.defs[p.i].cod+' = 10100;');
							var $row = p.$w.find('#tabs-3 .gridReference').clone();
							$row.find('li:eq(0)').html(data.defs[p.i].nomb);
							$row.find('li:eq(1)').html('SERV.'+data.defs[p.i].cod);
							$row.wrapInner('<a class="item">');
							$row.find('a').dblclick(function(){
								p.$w.find('[name=cod]').val(p.$w.find('[name=cod]').val()+$(this).data('cod')).keyup();
							}).data('cod','SERV.'+data.defs[p.i].cod);
							p.$w.find('#tabs-3 .gridBody').append($row.children());
						}
					}
					p.$w.find('[name=cod]').keyup(function(){
						var val = $(this).val();
						if(val!=''){
							try{
								eval(val);
								p.$w.find('table:first th:eq(1)').removeClass('ui-state-error');
								p.$w.find('[name=rpta]').html('F&oacute;rmula correcta!');
								p.$w.find('[name=confir]').removeClass('ui-icon-circle-close')
									.addClass('ui-icon-circle-check').data('check',true);
							}catch(mierror){
								//K.notification({text: "Error detectado: " + mierror,type: 'info'});
								p.$w.find('table:first th:eq(1)').addClass('ui-state-error');
								p.$w.find('[name=rpta]').html('Revise su f&oacute;rmula!');
								p.$w.find('[name=confir]').removeClass('ui-icon-circle-check')
							   		.addClass('ui-icon-circle-close').data('check',false);
							}
						}
					}).keyup();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetails: function(p){
		new K.Modal({
			id: 'windowDetailsConc'+p.id,
			title: 'Historial de Concepto: '+p.nomb,
			contentURL: 'cj/conc/details',
			icon: 'ui-icon-gear',
			height: 320,
			width: 770,
			onContentLoaded: function(){
				p.$w = $('#windowDetailsConc'+p.id);
				K.block({$element: p.$w});
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				$.post('cj/conc/get','id='+p.id,function(data){
					p.$w.find('div:first').html(data.nomb);
					if(data.historico!=null)
						for(var i=0; i<data.historico.length; i++){
							var $row = p.$w.find('.gridReference').clone();
							if(data.historico[i].fecreg!=null)
								$row.find('li:eq(0)').html(ciHelper.dateFormat(data.historico[i].fecreg));
							if(data.historico[i].autor!=null)
								$row.find('li:eq(1)').html(ciHelper.enti.formatName(data.historico[i].autor));
							if(data.historico[i].descr!='')
								$row.find('li:eq(2)').html(data.historico[i].descr);
							else
								$row.find('li:eq(2)').html(data.nomb);
							$row.find('li:eq(3)').html(data.historico[i].formula);
							$row.wrapInner('<a class="item">');
							p.$w.find('.gridBody').append($row.children());
						}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.estado = 'H';
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			$.post('cj/conc/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (i=0; i < data.paging.total_page_items; i++) {
						var result = data.items[i];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
						$li.eq(1).html( result.cod );
						$li.eq(2).html( result.formula );
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
			id: 'windowSelectCaja',
			title: 'Seleccionar Caja',
			contentURL: 'cj/conc/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un Concepto!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectCaja');
				K.block({$element: p.$w});
				p.$w.find('.grid').height('320px');
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
