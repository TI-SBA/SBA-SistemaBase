peEntiPra = {
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
	init: function(){
		if($('#pageWrapper [child=enti]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pe/navg/enti',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="enti" />');
					$p.find("[name=peEnti]").after( $row.children() );
				}
				$p.find('[name=peEnti]').data('enti',$('#pageWrapper [child=enti]:first').data('enti'));
				$p.find('[name=peEntiPra]').click(function(){ peEntiPra.init(); }).addClass('ui-state-highlight');
				$p.find('[name^=peEntiTrab]').click(function(){
					$.cookie('tipo_contrato',$(this).attr('name').substring(10));
					peEntiTrab.init();
				});
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'peEntiPra',
			titleBar: {
				title: 'Cuentas: Practicantes'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/prac',
			store: false,
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de practicante' ).width('250');
				$mainPanel.find('[name=obj]').html( 'practicante(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').click(function(){
					peEntiPra.windowNew();
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						peEntiPra.loadData({page: 1,url: 'pe/prac/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						peEntiPra.loadData({page: 1,url: 'pe/prac/search'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				peEntiPra.loadData({page: 1,url: 'pe/prac/lista'});
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
					$li.eq(0).css('background',peEntiPra.states[result.roles.practicante.estado].color).addClass('vtip').attr('title',peEntiPra.states[result.roles.practicante.estado].descr);
					$li.eq(1).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(2).html( ciHelper.enti.formatName(result) );
					$li.eq(3).html( result.roles.practicante.organizacion.nomb );
					$li.eq(4).html( ciHelper.dateFormat(result.fecreg) );
					$row.wrapInner('<a class="item" />');
					$row.find('a').data('id',result._id.$id).dblclick(function(){
						peEntiPra.windowDetails({id: $(this).data('id'),nomb: $(this).find('li:eq(2)').html()});
					}).data('estado',result.roles.practicante.estado).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenListEd_edi',menu).remove();
								if(K.tmp.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peEntiPra.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									K.sendingInfo();
									$.post('pe/prac/upd',{_id: K.tmp.data('id'),estado: 'H'},function(){
										K.clearNoti();
										K.notification({title: 'Practicante Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
										$('#pageWrapperLeft .ui-state-highlight').click();
									});
								},
								'conMenListEd_des': function(t) {
									K.sendingInfo();
									$.post('pe/prac/upd',{_id: K.tmp.data('id'),estado: 'D'},function(){
										K.clearNoti();
										K.notification({title: 'Practicante Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
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
						peEntiPra.loadData(params);
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
		p = {
			cbEnti: function(data){
				if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
				else p.$w.find('[name=foto]').removeAttr('src');
				p.$w.find('[name=nomb]').data('data',data).data('id',data._id.$id)
				.html( data.nomb + ' '+ data.appat + ' ' + data.apmat ).attr('title',data.nomb + ' '+ data.appat + ' ' + data.apmat).tooltip();
				p.$w.find('[name=docident]').html( data.docident[0].num );
				if(data.domicilios!=null) p.$w.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
				else p.$w.find('[name=direc]').html('--');
				if(data.telefonos!=null) p.$w.find('[name=telf]').html( data.telefonos[0].num );
				else p.$w.find('[name=telf]').html('--');
			}
		};
		new K.Window({
			id: 'windowNewPra',
			title: 'Nuevo Practicante',
			contentURL: 'pe/prac/edit',
			icon: 'ui-icon-plusthick',
			width: 440,
			height: 390,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.$w.find('[name=nomb]').data('id'),
						propina: p.$w.find('[name=monto]').val(),
						fec_ini: p.$w.find('[name=fec]').val()
					};
					if(data._id==null){
						p.$w.find('[name=btnSelEnt]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una entidad!',type: 'error'});
					}else if(data.propina==''||parseInt(data.propina)==0){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto mayor a 0!',type: 'error'});
					}
					tmp = p.$w.find('[name=orga]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnSelOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}else{
						data.orga = {
							_id: tmp._id.$id,
							nomb: tmp.nomb
						};
					}
					if(data.fec_ini==''){
						p.$w.find('[name=fec]').datepicker('show');
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de inicio!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.find('.ui-dialog-buttonpane button').button('disable');
					$.post('pe/prac/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Practicante fue registrado con &eacute;xito!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewPra');
				p.$w.find('[name=monto]').numeric().spinner({step: 0.1,min: 0}).val(0);
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnSelEnt]').click(function(){
					ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbEnti,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.practicante',value: {$exists: false}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgrEnt]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbEnti,reqs: {tipo_enti: 'P'}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=fec]').datepicker();
				p.$w.find('[name=btnSelOrga]').click(function(){
					ciSearch.windowSearchOrga({$window: p.$w,callback: function(data){
						p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
			}
		});
	},
	windowDetails: function(p){
		new K.Window({
			id: 'windowDetailsPrac'+p.id,
			title: 'Practicante: '+p.nomb,
			contentURL: 'pe/prac/details',
			store: false,
			icon: 'ui-icon-person',
			width: 450,
			height: 340,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsPrac'+p.id);
				p.$w.tabs();
				p.$w.find('#tabs-1,#tabs-2,fieldset').css('padding','0px');
				p.$w.find('#tabs-1,#tabs-2').css('height','300px').css('overflow','auto');
				K.block({$element: p.$w});
				$.post('pe/prac/get','id='+p.id,function(data){
					if(data.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+data.imagen.$id);
					else p.$w.find('[name=foto]').removeAttr('src');
					p.$w.find('[name=nomb]').html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
					p.$w.find('[name=docident]').html( data.docident[0].num );
					if(data.domicilios!=null) p.$w.find('[name=direc]').html( data.domicilios[0].direccion ).attr('title',data.domicilios[0].direccion).tooltip();
					else p.$w.find('[name=direc]').html('--');
					if(data.telefonos!=null) p.$w.find('[name=telf]').html( data.telefonos[0].num );
					else p.$w.find('[name=telf]').html('--');
					p.$w.find('[name=monto]').html( ciHelper.formatMon(data.roles.practicante.propina) );
					p.$w.find('[name=fec]').html( ciHelper.dateFormatOnlyDay(data.roles.practicante.fec_ini) );
					p.$w.find('[name=orga]').html( ciHelper.enti.formatName(data.roles.practicante.organizacion) );
					//K.unblock({$element: p.$w});
				},'json');
				$.post('pe/prop/all','id='+p.id,function(data2){
					if(data2.length>0){
						for(i=0;i<data2.length;i++){
							var result = data2[i];
							var $row = p.$w.find('.gridReference').clone();
							$li = $('li',$row);						
							$li.eq(0).html( result.periodo.mes+"-"+result.periodo.ano );
							$li.eq(1).html( result.propina );
							$li.eq(2).html( ciHelper.enti.formatName(result.trabajador) );
							$li.eq(3).html( ciHelper.dateFormat(result.fecreg) );
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							p.$w.find(".gridBody").append( $row.children() );	
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontro un historial de propinas para este practicante!',type: 'error'});
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};