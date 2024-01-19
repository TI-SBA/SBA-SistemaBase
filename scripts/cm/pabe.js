/*******************************************************************************
pabellones */
cmPabe = {
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
		K.initMode({
			mode: 'cm',
			action: 'cmPabe',
			titleBar: {
				title: 'Pabellones'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Nombre',f:'nomb'},{n:'Filas',f:'filas'},{n:'Pisos',f:'pisos'},{n:'Sector',f:'sector'},{n:'Registrado',f:'fecreg'}],
					width: [40,300,80,80,100,90],
					data: 'cm/pabe/Lista',
					params: {},
					itemdescr: 'pabellon(es)',
					toolbarHTML: '<button name="btnAgregar">Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							cmPabe.windowNew();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+' '+data.num+'</td>');
						$row.append('<td>'+data.filas+'</td>');
						$row.append('<td>'+data.pisos+'</td>');
						$row.append('<td>'+'Cuadrante '+data.sector+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							cmPabe.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(3)').html(),data: $(this).data('data')});
						}).contextMenu("conMenListEd", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								$('#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									cmPabe.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								},
								'conMenListEd_edi': function(t) {
									cmPabe.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNewPabellon',
			title: 'Nuevo Pabell&oacute;n',
			contentURL: 'cm/pabe/new',
			icon: 'ui-icon-bookmark',
			width: 470,
			height: 330,
			buttons: {
				"Guardar": function() {
					K.clearNoti();
					var data = {
						glosa: p.$w.find('[name=glosa]').val(),
						nomb: p.$w.find('[name=nomb]').val(),
						num: p.$w.find('[name=num]').val(),
						pisos: p.$w.find('[name=pisos]').val(),
						filas: p.$w.find('[name=filas]').val(),
						ref: p.$w.find('[name=ref]').val(),
						sector: p.$w.find('[name=sector] option:selected').val()
					};
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la denominaci&oacute;n de pabell&oacute;n!',type: 'error'});
					}
					if(data.num==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de pabell&oacute;n!',type: 'error'});
					}
					if(data.pisos==''){
						p.$w.find('[name=pisos]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de pisos de pabell&oacute;n!',type: 'error'});
					}
					if(data.filas==''){
						p.$w.find('[name=filas]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de filas de pabell&oacute;n!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/pabe/save",data,function(result){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Pabell&oacute;n agregado!"});
						$('#pageWrapperLeft .ui-state-highlight').click();
						K.closeWindow(p.$w.attr('id'));
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewPabellon');
				K.block({
					$element: p.$w
				});
				p.$w.find('[name=pisos]').spinner({step: 1,min: 1}).numeric().parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=filas]').spinner({step: 1,min: 1,max: 6}).numeric().parent().find('.ui-button').css('height','14px');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEdit: function(p){
		new K.Modal({ 
			id: 'windowEditPabellon'+p.id,
			title: 'Editar Pabell&oacute;n '+p.nomb,
			contentURL: 'cm/pabe/new',
			icon: 'ui-icon-pencil',
			width: 470,
			height: 330,
			buttons: {
				"Guardar": function() {
					K.clearNoti();
					var data = {
						_id: p.id,
						glosa: p.$w.find('[name=glosa]').val(),
						nomb: p.$w.find('[name=nomb]').val(),
						num: p.$w.find('[name=num]').val(),
						pisos: p.$w.find('[name=pisos]').val(),
						filas: p.$w.find('[name=filas]').val(),
						ref: p.$w.find('[name=ref]').val(),
						sector: p.$w.find('[name=sector] option:selected').val()
					};
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la denominaci&oacute;n de pabell&oacute;n!',type: 'error'});
					}
					if(data.num==''){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de pabell&oacute;n!',type: 'error'});
					}
					if(data.pisos==''){
						p.$w.find('[name=pisos]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de pisos de pabell&oacute;n!',type: 'error'});
					}
					if(data.filas==''){
						p.$w.find('[name=filas]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el n&uacute;mero de filas de pabell&oacute;n!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/pabe/save",data,function(result){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: "Pabell&oacute;n actualizado!"});
						$('#pageWrapperLeft .ui-state-highlight').click();
						K.closeWindow(p.$w.attr('id'));
					},'json');
				},
				"Cancelar": function() {
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditPabellon'+p.id);
				K.block({
					$element: p.$w
				});
				p.$w.find('[name=pisos]').spinner({step: 1,min: 1}).numeric().parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=filas]').spinner({step: 1,min: 1,max: 6}).numeric().parent().find('.ui-button').css('height','14px');
				$.post('cm/pabe/get',{_id: p.id},function(data){
					p.$w.find('[name=glosa]').val(data.glosa);
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=num]').val(data.num);
					p.$w.find('[name=pisos]').val(data.pisos);
					p.$w.find('[name=filas]').val(data.filas);
					p.$w.find('[name=ref]').val(data.ref);
					p.$w.find('[name=sector]').selectVal(data.sector);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowSelect: function(p){
		p.search = function(params){
			params.texto = p.$w.find('[name=buscar]').val();
			params.page_rows = 20;
			params.page = (params.page) ? params.page : 1;
			if(p.filter!=null){
				params.filter = p.filter;
			}
			$.post('cm/pabe/search',params,function(data){
				if ( data.paging.total_page_items > 0 ) {
					for (xx=0; xx < data.paging.total_page_items; xx++) {
						var result = data.items[xx];
						var $row = p.$w.find('.gridReference').clone();
						$li = $('li',$row);
						$li.eq(0).html( result.nomb );
						$li.eq(1).html( result.num );
						$li.eq(2).html( 'Cuadrante '+result.sector );
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
		var tmp_lock = false;
		new K.Modal({
			id: 'windowSelectPabe',
			title: 'Seleccionar Pabell&oacute;n',
			contentURL: 'cm/pabe/select',
			icon: 'ui-icon-search',
			width: 510,
			height: 350,
			buttons: {
				"Seleccionar": function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger un pabell&oacute;n!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					tmp_lock = true;
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				if(tmp_lock==false){
					if(p.cancel!=null)
						p.cancel();
				}
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelectPabe');
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
define(
	['cm/espa','cm/mapa','cm/ocup','cm/prop','cm/oper'],
	function(cmEspa,cmMapa,cmOcup,cmProp,cmOper){
		return cmPabe;
	}
);