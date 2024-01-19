/*******************************************************************************
espacios */
cmEspa = {
	colores: {
		'D': 'white',
		'P': 'gray',
		'O': '#ABF26D',
		'S': '#469400'
	},
	zonas: {
		"N": {descr: 'Normal'},
		'P': {descr: 'Preferencial'},
		'A': {descr: 'A'},
		'B': {descr: 'B'},
		'C': {descr: 'C'}
	},
	estados: {
		'D': {
			descr: 'Disponible',
			color: "#006532"
		},
		'C': {
			descr: 'Concedido',
			color: "#CCCCCC"
		}
	},
	tipos: {
		mausoleo: {
			'B': {nomb: 'B&oacute;veda'},
			'C': {nomb: 'Capilla'},
			'R': {nomb: 'Cripta'}
		},
		nicho: {
			'N': {nomb: 'Normal'},
			'P': {nomb: 'P&aacute;rvulo'}
		}
	},
	conce: {
		estado: {
			'T': {nomb: 'Temporal'},
			'P': {nomb: 'Permanente'}
		}
	},
	tipo_oper: {
		'CS':'Concesion',
		'CT':'Construccion',
		'AS':'Asignacion',
		'AD':'Adjuntacion',
		'TP':'Traspaso',
		'IN':'Inhumacion',
		'TI':'Traslado Interno',
		'TE':'Traslado Externo (hacia otro cementerio)',
		'TEO':'Traslado Externo (desde otro cementerio)',
		'CO':'Colocacion',
		'CV':'Conversion'
	},
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmEspa',
			titleBar: {
				title: 'Espacios'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				var $grid = new K.grid({
					cols: ['','','ID','Nombre','Tipo','Sector','Registrado'],
					data: 'cm/espa/search',
					params: {},
					itemdescr: 'espacio(s)',
					toolbarHTML: '<button name="btnAgregarNic">Agregar Nicho</button>'+
						'<button name="btnAgregarMau">Agregar Mausoleo</button>'+
						'<button name="btnAgregarTum">Agregar Tumba</button>'+
						'Tipo de Espacio: <select name="tipo">'+
							'<option value="">--</option>'+
							'<option value="N">Nicho</option>'+
							'<option value="M">Mausoleo</option>'+
							'<option value="T">Tumba</option>'+
						'</select>&nbsp;'+
						'Cuadrante: <select name="sector">'+
				 			'<option value="">--</option>'+
				 			'<option value="A">Cuadrante A</option>'+
				 			'<option value="B">Cuadrante B</option>'+
				 			'<option value="C">Cuadrante C</option>'+
				 			'<option value="D">Cuadrante D</option>'+
				 			'<option value="E">Cuadrante E</option>'+
				 			'<option value="F">Cuadrante F</option>'+
				 			'<option value="G">Cuadrante G</option>'+
				 		'</select>&nbsp;'+
				 		'N&uacute;mero: <input type="text" class="form-control" size="6" name="num" /><button></button>'+
						'Fila <input type="text" class="form-control" size="6" name="fila" /><button></button>&nbsp;'+
						'Piso: <input type="text" class="form-control" size="6" name="piso" /><button></button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregarNic]').click(function(){
							cmEspa.windowNewNicho();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
						$el.find('[name=btnAgregarMau]').click(function(){
							cmEspa.windowNewMausoleo();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
						$el.find('[name=btnAgregarTum]').click(function(){
							cmEspa.windowNewTumba();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
						$el.find('[name=fila]').numeric();
						$el.find('[name=piso]').numeric();
						$el.find('[name=num]').numeric();
						$el.find('[name=tipo],[name=sector]').change(function(){
					    	$el.find('[name=fila]').val('');
					    	$el.find('[name=piso]').val('');
					    	$el.find('[name=num]').val('');
					    	var params = {
					    		sector: $el.find('[name=sector] option:selected').val(),
					    		tipo: $el.find('[name=tipo] option:selected').val(),
					    		num: $el.find('[name=num]').val(),
					    		fila: $el.find('[name=fila]').val(),
					    		piso: $el.find('[name=piso]').val()
					    	};
					    	$grid.reinit({params: params});
						});
						$el.find('button:eq(3),button:eq(4),button:eq(5)').click(function(){
							var params = {
					    		sector: $el.find('[name=sector] option:selected').val(),
					    		tipo: $el.find('[name=tipo] option:selected').val(),
					    		num: $el.find('[name=num]').val(),
					    		fila: $el.find('[name=fila]').val(),
					    		piso: $el.find('[name=piso]').val()
					    	};
					    	$grid.reinit({params: params});
						}).button({icons: {primary: 'ui-icon-search'},text: false});
						$el.find('[name=fila],[name=piso],[name=num]').keyup(function(e){
							if(e.keyCode == 13){
								var params = {
						    		sector: $el.find('[name=sector] option:selected').val(),
						    		tipo: $el.find('[name=tipo] option:selected').val(),
						    		num: $el.find('[name=num]').val(),
						    		fila: $el.find('[name=fila]').val(),
						    		piso: $el.find('[name=piso]').val()
						    	};
						    	$grid.reinit({params: params});
							}
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.find('td:last').css('background',cmEspa.estados[data.estado].color).addClass('vtip').attr('title',cmEspa.estados[data.estado].descr);
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data._id.$id+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						if(data.mausoleo!=null){
							$row.append('<td>Mausoleo</td>');
						}else if(data.nicho!=null){
							$row.append('<td>Nicho</td>');
						}else if(data.tumba!=null){
							$row.append('<td>Tumba</td>');
						}
						$row.append('<td>Cuadrante '+data.sector+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							switch($(this).data('tipo')){
								case 'N':
									cmEspa.windowDetailsNicho({_id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html(),data: $(this).data('data')});
									break;
								case 'M':
									cmEspa.windowDetailsMauso({_id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html(),data: $(this).data('data')});
									break;
								case 'T':
									cmEspa.windowDetailsTumba({_id: $(this).data('id'),nomb: $(this).find('li:eq(3)').html(),data: $(this).data('data')});
									break;
							}
						}).data('estado',data.estado).contextMenu("conMenCmEspa", {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('data').imagen==null)
									$('#conMenCmEspa_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenCmEspa_ver': function(t) {
									switch(K.tmp.data('tipo')){
										case 'N':
											cmEspa.windowDetailsNicho({_id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html(),data: K.tmp.data('data')});
											break;
										case 'M':
											cmEspa.windowDetailsMauso({_id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html(),data: K.tmp.data('data')});
											break;
										case 'T':
											cmEspa.windowDetailsTumba({_id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html(),data: K.tmp.data('data')});
											break;
									}
								},
								'conMenCmEspa_edi': function(t) {
									switch(K.tmp.data('tipo')){
										case 'N':
											cmEspa.windowEditNicho({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
											break;
										case 'M':
											cmEspa.windowEditMausoleo({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
											break;
										case 'T':
											cmEspa.windowEditTumba({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(3)').html()});
											break;
									}
								},
								'conMenCmEspa_eli': function(t) {
									ciHelper.confirm('&iquest;Est&aacute; seguro(a) de eliminar este Espacio del Mapa&#63;',function () {
										K.sendingInfo();
										$.post('cm/espa/delete_mapa',{_id: K.tmp.data('id')},function(){
											K.clearNoti();
											K.notification({title: 'Espacio eliminado del Mapa',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											$('#pageWrapperLeft .ui-state-highlight').click();
										});
									},function () {
										$.noop();
									});
								}
							}
						});
						if(data.mausoleo!=null){
							$row.data('tipo','M');
						}else if(data.nicho!=null){
							$row.data('tipo','N');
						}else if(data.tumba!=null){
							$row.data('tipo','T');
						}
						return $row;
					}
				});
			}
		});
	},
	showDetailsEspa: function(p){
		if(p.modal!=true && p.modal==null) p.modal = false;
		$.post('cm/espa/get_one','_id='+p.id,function(data){
			if(data.nicho!=null){
				cmEspa.windowDetailsNicho({data: data,modal: p.modal});
			}else if(data.mausoleo!=null){
				cmEspa.windowDetailsMauso({data: data,modal: p.modal});
			}else if(data.tumba!=null){
				cmEspa.windowDetailsTumba({data: data,modal: p.modal});
			}else if(data.osario!=null){
				//cmEspa.windowDetailsMauso({data: data});
			}
		},'json');
	},
	windowNewNicho: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNewNicho',
			title: 'Nuevo Nicho',
			contentURL: 'cm/espa/nicho',
			icon: 'ui-icon-tag',
			width: 468,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						capacidad: p.$w.find('[name=capa]').val(),
						estado: 'D',
						nicho: {
							fila: p.$w.find('[name=nomb]').val(),
							num: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=rbtn_tipo]:checked').val(),
							piso: p.$w.find('[name=pisos]').val(),
							pabellon: {}
						},
						costo: p.$w.find('[name=precio]').val(),
						precio_temp: p.$w.find('[name=precio_temp]').val(),
						precio_perp: p.$w.find('[name=precio_perp]').val(),
						precio_vida: p.$w.find('[name=precio_vid]').val()
					};
					if(data.nicho.fila == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese fila del nicho!",type:"error"});
					}
					if(data.nicho.num == ""){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese n&uacute;mero del nicho!",type:"error"});
					}
					var tmp = p.$w.find('[name=pabellon]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnPabellon]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un pabell&oacute;n para el nicho!",type:"error"});
					}
					data.nicho.pabellon._id = tmp._id.$id;
					data.nicho.pabellon.nomb = tmp.nomb;
					data.nicho.pabellon.num = tmp.num;
					data.sector = tmp.sector;
					data.nomb = data.nicho.pabellon.nomb+", "+data.nicho.pabellon.num+", Piso "+data.nicho.piso+", Fila "+data.nicho.fila+", Numero "+data.nicho.num;
					if(data.costo == ""){
						/*p.$w.find('[name=precio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese costo del nicho!",type:"error"});*/
					}
					if(data.precio_temp == ""){
						p.$w.find('[name=precio_temp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese precio temporal del nicho!",type:"error"});
					}
					if(data.precio_perp == ""){
						p.$w.find('[name=precio_perp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese precio permanente del nicho!",type:"error"});
					}
					if(data.precio_vida == ""){
						p.$w.find('[name=precio_vid]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese precio en vida del nicho!",type:"error"});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/espa/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Nicho agregado con e&eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewNicho');
				p.$w.find('[name^=precio]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('.buttonRow').buttonset();
				p.$w.find('[name=btnPabellon]').click(function(){
					cmPabe.windowSelect({callback: function(data){
						p.$w.find('[name=pabellon]').html(data.nomb).data('data',data);
						p.$w.find('[name=sector]').html('Cuadrante '+data.sector);
						p.$w.find('[name=btnPabellon]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
			}
		});
	},
	windowEditNicho: function(p){
		new K.Modal({
			id: 'windowEditNicho'+p.id,
			title: 'Editar Nicho',
			contentURL: 'cm/espa/nicho',
			icon: 'ui-icon-pencil',
			width: 468,
			height: 400,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						capacidad: p.$w.find('[name=capa]').val(),
						estado: 'D',
						nicho: {
							fila: p.$w.find('[name=nomb]').val(),
							num: p.$w.find('[name=num]').val(),
							tipo: p.$w.find('[name=rbtn_tipo]:checked').val(),
							piso: p.$w.find('[name=pisos]').val(),
							pabellon: {}
						},
						costo: p.$w.find('[name=precio]').val(),
						precio_temp: p.$w.find('[name=precio_temp]').val(),
						precio_perp: p.$w.find('[name=precio_perp]').val(),
						precio_vida: p.$w.find('[name=precio_vid]').val()
					};
					if(data.nicho.fila == ""){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese fila del nicho!",type:"error"});
					}
					if(data.nicho.num == ""){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese n&uacute;mero del nicho!",type:"error"});
					}
					var tmp = p.$w.find('[name=pabellon]').data('data');
					if(tmp==null){
						p.$w.find('[name=btnPabellon]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un pabell&oacute;n para el nicho!",type:"error"});
					}
					data.nicho.pabellon._id = tmp._id.$id;
					data.nicho.pabellon.nomb = tmp.nomb;
					data.nicho.pabellon.num = tmp.num;
					data.sector = tmp.sector;
					data.nomb = data.nicho.pabellon.nomb+", "+data.nicho.pabellon.num+", Piso "+data.nicho.piso+", Fila "+data.nicho.fila+", Numero "+data.nicho.num;
					if(data.costo == ""){
						/*p.$w.find('[name=precio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese costo del nicho!",type:"error"});*/
					}
					if(data.precio_temp == ""){
						p.$w.find('[name=precio_temp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese precio temporal del nicho!",type:"error"});
					}
					if(data.precio_perp == ""){
						p.$w.find('[name=precio_perp]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese precio permanente del nicho!",type:"error"});
					}
					if(data.precio_vida == ""){
						p.$w.find('[name=precio_vid]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese precio en vida del nicho!",type:"error"});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/espa/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: "Nicho actualizado con &eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditNicho'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name^=precio]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('[name=btnPabellon]').click(function(){
					cmPabe.windowSelect({callback: function(data){
						p.$w.find('[name=pabellon]').html(data.nomb).data('data',data);
						p.$w.find('[name=sector]').html('Cuadrante '+data.sector);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				$.post('cm/espa/get',{_id: p.id},function(data){
					p.$w.find('[name=capa]').val(data.capacidad);
					p.$w.find('[name=nomb]').val(data.nicho.fila);
					p.$w.find('[name=pisos]').val(data.nicho.piso);
					p.$w.find('[name=num]').val(data.nicho.num);
					p.$w.find('[name=capa]').val(data.capacidad);
					if(data.nicho.tipo=='N'){
						p.$w.find('[name=rbtn_tipo]:eq(0)').attr('checked',true);
						p.$w.find('[name=rbtn_tipo]:eq(1)').attr('checked',false);
					}else{
						p.$w.find('[name=rbtn_tipo]:eq(0)').attr('checked',false);
						p.$w.find('[name=rbtn_tipo]:eq(1)').attr('checked',true);
					}
					p.$w.find('.buttonRow').buttonset();
					data.nicho.pabellon.sector = data.sector;
					p.$w.find('[name=pabellon]').html(data.nicho.pabellon.nomb).data('data',data.nicho.pabellon);
					p.$w.find('[name=sector]').html('Cuadrante '+data.sector);
					p.$w.find('[name=precio]').val(data.costo);
					p.$w.find('[name=precio_temp]').val(data.precio_temp);
					p.$w.find('[name=precio_vid]').val(data.precio_vida);
					p.$w.find('[name=precio_perp]').val(data.precio_perp);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewMausoleo: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNewMauso',
			title: 'Nuevo Mausoleo',
			contentURL: 'cm/espa/mauso',
			icon: 'ui-icon-tag',
			width: 468,
			height: 320,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						capacidad: p.$w.find('[name=capa]').val(),
						sector: p.$w.find('[name=sector] option:selected').val(),
						estado: 'D'
					};
					data.mausoleo = {
						zona: p.$w.find('[name=rbtn_zona]:checked').val(),
						lote: p.$w.find('[name=lote]').val(),
						denominacion: p.$w.find('[name=deno]').val(),
						tipo: p.$w.find('[name=rbtn_tip_mau]:checked').val(),
						medidas: {
							largo: p.$w.find('[name=largo]').val(),
							ancho: p.$w.find('[name=ancho]').val(),
							altura1: p.$w.find('[name=alt1]').val(),
							altura2: p.$w.find('[name=alt2]').val()
						},
						ref: p.$w.find('[name=refm]').val()
					};
					/*if(data.capacidad == ""){
						p.$w.find('[name=capa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la capacidad m&aacute;xima del mausoleo!",type:"error"});
					}*/
					if(data.mausoleo.lote == ""){
						p.$w.find('[name=lote]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar el lote del mausoleo!",type:"error"});
					}
					/*if(data.mausoleo.medidas.largo == ""){
						p.$w.find('[name=largo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese medida del largo!",type:"error"});
					}
					if(data.mausoleo.medidas.ancho == ""){
						p.$w.find('[name=ancho]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese medida del ancho!",type:"error"});
					}*/
					data.nomb = data.mausoleo.denominacion+", Zona "+cmEspa.zonas[data.mausoleo.zona].descr+", Lote "+data.mausoleo.lote;
					data.precio_perp = p.$w.find('[name=precio]').val();
					if(data.precio_perp == ""){
						p.$w.find('[name=precio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el precio de mausoleo!",type:"error"});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/espa/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Mausoleo agregado con e&eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewMauso');
				p.$w.find('[name^=precio]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				p.$w.find('.buttonRow').buttonset();
			}
		});
	},
	windowEditMausoleo: function(p){
		new K.Modal({ 
			id: 'windowEditMauso'+p.id,
			title: 'Editar Mausoleo',
			contentURL: 'cm/espa/mauso',
			icon: 'ui-icon-pencil',
			width: 468,
			height: 320,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						capacidad: p.$w.find('[name=capa]').val(),
						sector: p.$w.find('[name=sector] option:selected').val()
					};
					data.mausoleo = {
						zona: p.$w.find('[name=rbtn_zona]:checked').val(),
						lote: p.$w.find('[name=lote]').val(),
						denominacion: p.$w.find('[name=deno]').val(),
						tipo: p.$w.find('[name=rbtn_tip_mau]:checked').val(),
						medidas: {
							largo: p.$w.find('[name=largo]').val(),
							ancho: p.$w.find('[name=ancho]').val(),
							altura1: p.$w.find('[name=alt1]').val(),
							altura2: p.$w.find('[name=alt2]').val()
						},
						ref: p.$w.find('[name=refm]').val()
					};
					if(data.capacidad == ""){
						p.$w.find('[name=capa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la capacidad m&aacute;xima del mausoleo!",type:"error"});
					}
					if(data.mausoleo.lote == ""){
						p.$w.find('[name=lote]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar el lote del mausoleo!",type:"error"});
					}
					if(data.mausoleo.medidas.largo == ""){
						p.$w.find('[name=largo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese medida del largo!",type:"error"});
					}
					if(data.mausoleo.medidas.ancho == ""){
						p.$w.find('[name=ancho]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese medida del ancho!",type:"error"});
					}
					data.nomb = data.mausoleo.denominacion+", Zona "+cmEspa.zonas[data.mausoleo.zona].descr+", Lote "+data.mausoleo.lote;
					data.precio_perp = p.$w.find('[name=precio]').val();
					if(data.precio_perp == ""){
						p.$w.find('[name=precio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el precio de mausoleo!",type:"error"});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/espa/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: "Mausoleo actualizado con e&eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditMauso'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name^=precio]').numeric().spinner({step: 0.1,min: 0});
				p.$w.find('.ui-button').css('height','14px');
				$.post('cm/espa/get',{_id: p.id},function(data){
					p.$w.find('[name=sector]').selectVal(data.sector);
					p.$w.find('[name=capa]').val(data.capacidad);
					if(data.mausoleo.zona=='N'){
						p.$w.find('[name=rbtn_zona]:eq(0)').attr('checked',true);
						p.$w.find('[name=rbtn_zona]:eq(1)').attr('checked',false);
					}else{
						p.$w.find('[name=rbtn_zona]:eq(0)').attr('checked',false);
						p.$w.find('[name=rbtn_zona]:eq(1)').attr('checked',true);
					}
					if(data.mausoleo.tipo=='B'){
						p.$w.find('[name=rbtn_tip_mau]:eq(0)').attr('checked',true);
						p.$w.find('[name=rbtn_tip_mau]:eq(1)').attr('checked',false);
						p.$w.find('[name=rbtn_tip_mau]:eq(2)').attr('checked',false);
					}else if(data.mausoleo.tipo=='C'){
						p.$w.find('[name=rbtn_tip_mau]:eq(0)').attr('checked',false);
						p.$w.find('[name=rbtn_tip_mau]:eq(1)').attr('checked',true);
						p.$w.find('[name=rbtn_tip_mau]:eq(2)').attr('checked',false);
					}else if(data.mausoleo.tipo=='R'){
						p.$w.find('[name=rbtn_tip_mau]:eq(0)').attr('checked',false);
						p.$w.find('[name=rbtn_tip_mau]:eq(1)').attr('checked',false);
						p.$w.find('[name=rbtn_tip_mau]:eq(2)').attr('checked',true);
					}
					p.$w.find('.buttonRow').buttonset();
					p.$w.find('[name=lote]').val(data.mausoleo.lote);
					p.$w.find('[name=deno]').val(data.mausoleo.denominacion);
					p.$w.find('[name=largo]').val(data.mausoleo.medidas.largo);
					p.$w.find('[name=ancho]').val(data.mausoleo.medidas.ancho);
					p.$w.find('[name=alt1]').val(data.mausoleo.medidas.altura1);
					p.$w.find('[name=alt2]').val(data.mausoleo.medidas.altura2);
					p.$w.find('[name=refm]').val(data.mausoleo.ref);
					p.$w.find('[name=precio]').val(data.precio_perp);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewTumba: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowNewTumba',
			title: 'Nueva Tumba',
			contentURL: 'cm/espa/tumba',
			icon: 'ui-icon-tag',
			width: 468,
			height: 180,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						capacidad: p.$w.find('[name=capa]').val(),
						sector: p.$w.find('[name=sector] option:selected').val(),
						estado: 'D',
						tumba: {
							denominacion: p.$w.find('[name=denom]').val(),
							ref: p.$w.find('[name=reft]').val()
						}
					};
					if(data.capacidad == ""){
						p.$w.find('[name=capa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la capacidad m&aacute;xima de la tumba!",type:"error"});
					}
					if(data.tumba.denominacion == ""){
						p.$w.find('[name=denom]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la denominaci&oacute;n de la tumba!",type:"error"});
					}
					if(data.tumba.ref == ""){
						p.$w.find('[name=reft]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la referencia de la tumba!",type:"error"});
					}
					data.nomb = data.tumba.denominacion;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/espa/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Tumba agregada con e&eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNewTumba');
			}
		});
	},
	windowEditTumba: function(p){
		if(p==null) p = {};
		new K.Modal({ 
			id: 'windowEditTumba'+p.id,
			title: 'Editar Tumba',
			contentURL: 'cm/espa/tumba',
			icon: 'ui-icon-pencil',
			width: 468,
			height: 180,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id,
						capacidad: p.$w.find('[name=capa]').val(),
						sector: p.$w.find('[name=sector] option:selected').val(),
						tumba: {
							denominacion: p.$w.find('[name=denom]').val(),
							ref: p.$w.find('[name=reft]').val()
						}
					};
					if(data.capacidad == ""){
						p.$w.find('[name=capa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la capacidad m&aacute;xima de la tumba!",type:"error"});
					}
					if(data.tumba.denominacion == ""){
						p.$w.find('[name=denom]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la denominaci&oacute;n de la tumba!",type:"error"});
					}
					if(data.tumba.ref == ""){
						p.$w.find('[name=reft]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la referencia de la tumba!",type:"error"});
					}
					data.nomb = data.tumba.denominacion;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/espa/save",data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: "Tumba actualizada con e&eacute;xito!"});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					},'json');
				},
				"Cancelar": function() { 
					K.closeWindow(p.$w.attr('id')); 
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEditTumba'+p.id);
				K.block({$element: p.$w});
				$.post('cm/espa/get',{_id: p.id},function(data){
					p.$w.find('[name=sector]').selectVal(data.sector);
					p.$w.find('[name=capa]').val(data.capacidad);
					p.$w.find('[name=reft]').val(data.tumba.ref);
					p.$w.find('[name=denom]').val(data.tumba.denominacion);
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetailsMauso: function(p){
		var params = {
			id: 'windowDetailsMauso'+p.data._id.$id,
			title: 'Espacio: '+p.data.nomb,
			contentURL: 'cm/espa/details_mauso',
			icon: 'ui-icon-tag',
			width: 650,
			height: 380,
			buttons:{
				"Agregar registro Historico":function(){
					cmOper.windowNewHist({id:p.data._id.$id});
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsMauso'+p.data._id.$id);
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
				p.$w.find('.ui-layout-center').css('overflow','hidden');
				p.$w.find('[name=spNomb]').html( p.data.mausoleo.denominacion );
				p.$w.find('[name=spLote]').html( p.data.mausoleo.lote );
				p.$w.find('[name=spCapa]').html( p.data.capacidad );
				p.$w.find('[name=spRef]').html( p.data.mausoleo.ref );
				p.$w.find('[name=spTipo]').html( cmEspa.tipos.mausoleo[p.data.mausoleo.tipo].nomb );
				p.$w.find('[name=spFecreg]').html( ciHelper.dateFormatLong(p.data.fecreg) );
				p.$w.find('[name=spEstado]').html( cmEspa.estados[p.data.estado].descr );
				if(p.data.propietario!=null){
					p.$w.find('[name=prop]').html( p.data.propietario.nomb+' '+p.data.propietario.appat+' '+p.data.propietario.apmat );
					p.$w.find('[name=prop]').click(function(){
						cmProp.windowDetails({id: $(this).data('id'),nomb: p.$w.find('[name=spProp]').html(),modal: true});
					}).data('id',p.data.propietario._id.$id).data('tipo_enti',p.data.propietario.tipo_enti).css({
						'text-decoration': 'underline',
						'cursor': 'pointer'
					});
				}else p.$w.find('[name=prop]').closest('tr').hide();
				var ocupantes = p.data.ocupantes;
				if(ocupantes!=null){
					for(var i=0; i<ocupantes.length; i++){
						var $row = p.$w.find('.tableRefOcup').clone();
						$row.attr('name',ocupantes[i]._id.$id);
						$row.find('[name=spOcupNomb]').html( ocupantes[i].nomb+' '+ocupantes[i].appat+' '+ocupantes[i].apmat ).click(function(){
							cmOcup.windowDetails({id: $(this).data('id'),nomb: $(this).data('data'),	modal: true});
						}).data('id',ocupantes[i]._id.$id).data('tipo_enti',ocupantes[i].tipo_enti)
							.data('data',mgEnti.formatName(ocupantes[i])).css({
							'text-decoration': 'underline',
							'cursor': 'pointer'
						});
						$row.removeClass('tableRefOcup').show();
						p.$w.find('.tableRefOcup').before($row);
						p.$w.find('.tableRefOcup').before('<hr>');
					}
				}
				$.post('cm/espa/get_opers','_id='+p.data._id.$id,function(data){
					if(data!=null){
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
							if(result.anulacion!=null)
								$row.find('li:eq(2)').html( '<span style="color:red;">Anulada</span>' );
							else if(result.ejecucion!=null)
								$row.find('li:eq(2)').html( 'Ejecutado' );
							else
								$row.find('li:eq(2)').html( 'Activa' );
							$row.find('li:eq(3)').html( ciHelper.dateFormatLong(result.fecreg) );
							$row.wrapInner('<a class="item" />');
							$row.find('a').click(function(){
								cmOper.showDetails({data: $(this).data('data')});
							}).data('data',result);
							p.$w.find('.gridBody:last').append($row.children());
							if(result.construccion!=null){
								p.$w.find('[name=spConsFecreg]').html( ciHelper.dateFormatLong(result.fecreg) );
								p.$w.find('[name=spConsFecprog]').html( ciHelper.dateFormatLong(result.programacion.fecprog) );
								p.$w.find('[name=spConsFecven]').html( ciHelper.dateFormatLong(result.construccion.fecven) );
								p.$w.find('[name=spConsCapa]').html( result.construccion.capacidad );
								p.$w.find('[name=spConsLargo]').html( (result.construccion.largo!=''?result.construccion.largo:'0')+' m.' );
								p.$w.find('[name=spConsAncho]').html( (result.construccion.ancho!=''?result.construccion.ancho:'0')+' m.' );
								p.$w.find('[name=spConsAlt1]').html( (result.construccion.altura1!=''?result.construccion.altura1:'0')+' m.' );
								p.$w.find('[name=spConsAlt2]').html( (result.construccion.altura2!=''?result.construccion.altura2:'0')+' m.' );
								//ultima observacion
								p.$w.find('[name=spConsObserv]').html( result.programacion.observ );
								if(result.construccion.finalizacion!=null){
									p.$w.find('[name=section2] label:eq(1)').html( 'Inicio de Construcci&oacute;n' );
									p.$w.find('[name=Fecprog]').html( ciHelper.dateFormatLong(result.ejecucion.fecini) );
									p.$w.find('[name=section2] label:eq(2)').html( 'Fin de Construcci&oacute;n' );
									p.$w.find('[name=Fecven]').html( ciHelper.dateFormatLong(result.ejecucion.fecfin) );
									if(result.construccion.capacidad!=result.construccion.finalizacion.capacidad){
										p.$w.find('[name=spConsCapa]').html( result.construccion.finalizacion.capacidad + '&nbsp;<label style="color:red;">No coincide con solicitud: '+result.construccion.capacidad+'</label>' );
									}
									if(result.construccion.ancho!=result.construccion.finalizacion.ancho){
										p.$w.find('[name=spConsLargo]').html( (result.construccion.finalizacion.ancho!=''?result.construccion.finalizacion.ancho:'0') + ' m.&nbsp;<label style="color:red;">No coincide con solicitud: '+(result.construccion.ancho!=''?result.construccion.ancho:'0')+' m.</label>' );
									}
									if(result.construccion.largo!=result.construccion.finalizacion.largo){
										p.$w.find('[name=spConsLargo]').html( (result.construccion.finalizacion.largo!=''?result.construccion.finalizacion.largo:'0') + ' m.&nbsp;<label m. style="color:red;">No coincide con solicitud: '+(result.construccion.largo!=''?result.construccion.largo:'0')+' m.</label>' );
									}
									if(result.construccion.altura1!=result.construccion.finalizacion.altura1){
										p.$w.find('[name=spConsAlt1]').html( (result.construccion.finalizacion.altura1!=''?result.construccion.finalizacion.altura1:'0') + ' m.&nbsp;<label s m.tyle="color:red;">No coincide con solicitud: '+(result.construccion.altura1!=''?result.construccion.altura1:'0')+' m.</label>' );
									}
									if(result.construccion.altura2!=result.construccion.finalizacion.altura2){
										p.$w.find('[name=spConsAlt2]').html( (result.construccion.finalizacion.altura2!=''?result.construccion.finalizacion.altura2:'0') + ' m.&nbsp;<label s m.tyle="color:red;">No coincide con solicitud: '+(result.construccion.altura2!=''?result.construccion.altura2:'0')+' m.</label>' );
									}
									if(result.ejecucion.observ!='') p.$w.find('[name=spConsObserv]').html( result.ejecucion.observ );
								}else{
									p.$w.find('[name=section2] table').before('<label style="color: red;"><b>Construcci&oacute;n Pendiente</b></label>');
								}
							}else if(result.asignacion!=null){
								if(p.$w.find('[name='+result.ocupante._id.$id+']').length>0){
									var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
									$table.find('[name=spOcupFecasig]').html( ciHelper.dateFormatLong(result.fecreg) );
								}
							}else if(result.inhumacion!=null){
								if(p.$w.find('[name='+result.ocupante._id.$id+']').length>0){
									var $table = p.$w.find('[name='+result.ocupante._id.$id+']');
									if(result.ejecucion!=null)
										$table.find('[name=spOcupFecinh]').html( ciHelper.dateFormatLong(result.ejecucion.fecini) );
								}
							}
						}
					}
					$.post('cm/oper/all_hist',{espacio:p.data._id.$id},function(data){
						if(data!=null){
							for(i=0;i<data.length;i++){
								var $row = p.$w.find('.gridReference').clone();
								$row.find('li:eq(0)').html(cmEspa.tipo_oper[data[i].tipo]);
								$row.find('li:eq(1)').html( 'Fecha de Operacion' );
								$row.find('li:eq(2)').html( ciHelper.dateFormatLong(data[i].fecoper) );
								$row.wrapInner('<a class="item" />');
								$row.find('a').click(function(){
									cmOper.windowHistDetails({id: $(this).data('data')._id.$id});
								}).data('data',data[i]);
								p.$w.find('.gridBody:last').append($row.children());
							}
						}
						K.unblock({$element: p.$w});
					},'json');	
				},'json');
			}
		};
		if(p.modal) new K.Modal(params);
		else new K.Window(params);
	},
	windowDetailsNicho: function(p){
		var params = {
			id: 'windowDetailsNicho'+p.data._id.$id,
			title: 'Espacio: '+p.data.nomb,
			contentURL: 'cm/espa/details_nicho',
			icon: 'ui-icon-tag',
			width: 800,
			height: 380,
			buttons:{
				"Agregar registro Historico":function(){
					cmOper.windowNewHist({id:p.data._id.$id});
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowDetailsNicho'+p.data._id.$id);
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
				p.$w.find('.ui-layout-center').css('overflow','hidden');
				/*Datos generales*/		
				p.$w.find('[name=spPabe]').html( p.data.nicho.pabellon.nomb );
				p.$w.find('[name=spPiso]').html( p.data.nicho.piso );
				p.$w.find('[name=spFila]').html( p.data.nicho.fila );
				p.$w.find('[name=spNum]').html( p.data.nicho.num );
				if(p.data.estado=='C'){
					p.$w.find('[name=spCapa]').html( p.data.capacidad );					
					if(p.data.propietario.appat!=null) p.$w.find('[name=spProp]').html( p.data.propietario.nomb+" "+p.data.propietario.appat+" "+p.data.propietario.apmat );
					else p.$w.find('[name=spProp]').html( p.data.propietario.nomb);		
					p.$w.find('[name=spProp]').click(function(){
						cmProp.windowDetails({id: $(this).data('id'),nomb: p.$w.find('[name=spProp]').html(),modal: true});
					}).data('id',p.data.propietario._id.$id).data('tipo_enti',p.data.propietario.tipo_enti);
				}
				else{
					p.$w.find('[name=capas]').hide();				
					p.$w.find('[name=prop]').hide();
				}
				p.$w.find('[name=spTipo]').html( cmEspa.tipos.nicho[p.data.nicho.tipo].nomb );				
				p.$w.find('[name=spReg]').html( ciHelper.dateFormatLong(p.data.fecreg) );
				p.$w.find('[name=spEstado]').html( cmEspa.estados[p.data.estado].descr ).css("font-weight","bold");
				if(p.data.estado=="D") p.$w.find('[name=spEstado]').css("color","green");
				/*ocupantes*/
				
				for (ocu in p.data.ocupantes){
					$.post('cm/espa/datos_ocupante',{_id:p.data.ocupantes[ocu]._id.$id},function(data){
						var tmp_ocu;
						for (ocu in p.data.ocupantes){
							if(p.data.ocupantes[ocu]._id.$id==data.ocupante_id)
								tmp_ocu = ocu;
						}
						var $row = Object();		
						$row.ocu = p.$w.find('.tableRefOcup').clone();
						$row.ocu.attr('name',p.data.ocupantes[tmp_ocu]._id.$id);
						$row.ocu.find('[name=spOcupNomb]').html( data.ocupante ).click(function(){
							cmOcup.windowDetails({id: $(this).data('id'),nomb: $(this).data('data'),	modal: true});
						}).data('id',p.data.ocupantes[tmp_ocu]._id.$id).data('tipo_enti',p.data.ocupantes[tmp_ocu].tipo_enti)
							.data('data',data.ocupante);
						if(data.fecinhu!=null) $row.ocu.find('[name=spOcupFecinh]').html(ciHelper.dateFormatLong(data.fecinhu));
						else $row.ocu.find('[name=spOcupFecinh]').html('<b>No Ejecutada</b>');
						
						$row.ocu.find('[name=spOper]').html(data.tipoper);														
						$row.ocu.find('[name=spOcupFecasig]').html(ciHelper.dateFormatLong(data.fecoper));
						
						$row.ocu.removeClass('tableRefOcup').show();
						p.$w.find('.tableRefOcup').before($row.ocu);
						p.$w.find('.tableRefOcup').before('<hr>');
					},'json');
				}
				$.post('cm/espa/operaciones',{_id:p.data._id.$id, oper:'',campo:'espacio._id'},function(d){
					for (acs in d){		
						/*conceciones*/
						if(d[acs].concesion){	
							var nomb=d[acs].propietario.nomb;var color="";
							if(d[acs].propietario.appat!=null) nomb += " "+d[acs].propietario.appat+" "+d[acs].propietario.apmat;
							p.$w.find('[name=concesion]').append('<tr><td colspan="2" width="200px"><a><span name="'+d[acs].propietario._id.$id+'">'+nomb+'</span></a></td><td style="color:#666666;"><b>Fecha</b></td><td>'+ciHelper.dateFormatLong(d[acs].fecreg)+'</td></tr>');
							p.$w.find('[name='+d[acs].propietario._id.$id+']').click(function(){
								 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',d[acs].propietario._id.$id).data('tipo_enti',d[acs].propietario.tipo_enti);
							
							if(d[acs].concesion.fecven!=null){
								if(ciHelper.dateDiffNow(d[acs].concesion.fecven)<=0) color='style="color:#CC0000 !important;"'; 
								p.$w.find('[name=concesion]').append('<tr><td style="color:#666666;font-weight:bold;">Condici&oacute;n</td><td>'+cmEspa.conce.estado[d[acs].concesion.condicion].nomb+'</td><td style="color:#666666;font-weight:bold;">Vencimiento</td><td '+color+'>'+ciHelper.dateFormatLong(d[acs].concesion.fecven)+'</td></tr>');
							}
							else p.$w.find('[name=concesion]').append('<tr><td style="color:#666666;font-weight:bold;">Condici&oacute;n</td><td>'+cmEspa.conce.estado[d[acs].concesion.condicion].nomb+'</td><td colspan=2></td></tr>');

							if(d[acs].anulacion!=null){
								p.$w.find('[name=concesion]').append('<tr><td style="color:#666666;font-weight:bold;">Se anul&oacute; el</td><td>'+ciHelper.dateFormatLong(d[acs].anulacion.fecanl)+'</td></tr>');
							}
						}
						var $row = p.$w.find('.gridReference').clone();
						var result = d[acs];
						if(result.concesion != null)
							$row.find('li:eq(0)').html('Concesi&oacute;n');							
						if(result.construccion != null)
							$row.find('li:eq(0)').html('Construcci&oacute;n');						
						if(result.asignacion != null)
							$row.find('li:eq(0)').html('Asignaci&oacute;n');						
						if(result.adjuntacion != null)
							$row.find('li:eq(0)').html('Adjuntaci&oacute;n');						
						if(result.traspaso != null)
							$row.find('li:eq(0)').html('Traspaso');						
						if(result.inhumacion != null)
							$row.find('li:eq(0)').html('Inhumaci&oacute;n');						
						if(result.traslado != null)
							$row.find('li:eq(0)').html('Traslado');						
						if(result.colocacion != null)
							$row.find('li:eq(0)').html('Colocaci&oacute;n');
						if(result.anular_asignacion != null)
							$row.find('li:eq(0)').html( 'Anular Asignaci&oacute;n' );
						if(result.anular_concesion != null)
							$row.find('li:eq(0)').html( 'Anular Concesi&oacute;n' );
						if(result.conversion != null)
							$row.find('li:eq(0)').html( 'Conversi&oacute;n' );						
						$row.find('li:eq(1)').html( 'Registrado' );
							if(result.anulacion!=null)
								$row.find('li:eq(2)').html( '<span style="color:red;">Anulada</span>' );
							else if(result.ejecucion!=null)
								$row.find('li:eq(2)').html( 'Ejecutado' );
							else
								$row.find('li:eq(2)').html( 'Activa' );
						$row.find('li:eq(3)').html( ciHelper.dateFormatLong(result.fecreg) );
						$row.wrapInner('<a class="item" />');
						$row.find('a').click(function(){
                            cmOper.showDetails({data: $(this).data('data')});
						}).data('data',result);
						p.$w.find('.gridBody:last').append($row.children());						
					}
					$.post('cm/oper/all_hist',{espacio:p.data._id.$id},function(data){
						if(data!=null){
							for(i=0;i<data.length;i++){
								var $row = p.$w.find('.gridReference').clone();
								$row.find('li:eq(0)').html(cmEspa.tipo_oper[data[i].tipo]);
								$row.find('li:eq(1)').html( 'Fecha de Operacion' );
								$row.find('li:eq(2)').html( ciHelper.dateFormatLong(data[i].fecoper) );
								$row.wrapInner('<a class="item" />');
								$row.find('a').click(function(){
									cmOper.windowHistDetails({id: $(this).data('data')._id.$id});
								}).data('data',data[i]);
								p.$w.find('.gridBody:last').append($row.children());
							}
						}
						K.unblock({$element: p.$w});
					},'json');
				},'json');	
			}
		};
		if(p.modal==true) new K.Modal(params);
		else new K.Window(params);
	},
	windowDetailsTumba: function(p){
		var params = {
			id: 'windowDetailsTumba'+p.data._id.$id,
			title: 'Espacio: '+p.data.nomb,
			contentURL: 'cm/espa/details_tumba',
			icon: 'ui-icon-tag',
			width: 800,
			height: 380,
			buttons:{
				"Agregar registro Historico":function(){
					cmOper.windowNewHist({id:p.data._id.$id});
				}
			},
			onContentLoaded: function(){				
				p.$w = $('#windowDetailsTumba'+p.data._id.$id);
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
				p.$w.find('.ui-layout-center').css('overflow','hidden');
				
				/*Datos generales*/
				$.post('cm/espa/get_one',{_id:p.data._id.$id},function(data){
					if(p.data.estado=='C'){
						p.$w.find('[name=spCapa]').html( data.capacidad );					
						if(data.propietario.appat!=null) p.$w.find('[name=spProp]').html( data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat );
						else p.$w.find('[name=spProp]').html( data.propietario.nomb);		
						p.$w.find('[name=spProp]').click(function(){
							cmProp.windowDetails({id: $(this).data('id'),nomb: p.$w.find('[name=spProp]').html(),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						p.$w.find('[name=spRef]').html( data.tumba.ref );	
					}
					else{
						p.$w.find('[name=capas]').hide();				
						p.$w.find('[name=prop]').hide();
						p.$w.find('[name=ref]').hide();
					}					
					p.$w.find('[name=spReg]').html( ciHelper.dateFormatLong(data.fecreg) );
					p.$w.find('[name=spEstado]').html( cmEspa.estados[data.estado].descr ).css("font-weight","bold");
					if(p.data.estado=="D") p.$w.find('[name=spEstado]').css("color","green");
					/*ocupantes*/
					var $row = Object();
					for (ocu in p.data.ocupantes){
						$.post('cm/espa/datos_ocupante',{_id:p.data.ocupantes[ocu]._id.$id},function(data){		
							$row.ocu = p.$w.find('.tableRefOcup').clone();
							$row.ocu.attr('name',p.data.ocupantes[ocu]._id.$id);
							$row.ocu.find('[name=spOcupNomb]').html( data.ocupante ).click(function(){
								cmOcup.windowDetails({id: $(this).data('id'),nomb: data.ocupante,	modal: true});
							}).data('id',p.data.ocupantes[ocu]._id.$id).data('tipo_enti',p.data.ocupantes[ocu].tipo_enti);
							if(data.fecinhu) $row.ocu.find('[name=spOcupFecinh]').html(ciHelper.dateFormatLong(data.fecinhu));
							else $row.ocu.find('[name=spOcupFecinh]').html('<b>No Ejecutada</b>');
							
							$row.ocu.find('[name=spOper]').html(data.tipoper);														
							$row.ocu.find('[name=spOcupFecasig]').html(ciHelper.dateFormatLong(data.fecoper));					
							
							$row.ocu.removeClass('tableRefOcup').show();
							p.$w.find('.tableRefOcup').before($row.ocu);
							p.$w.find('.tableRefOcup').before('<hr>');
						},'json');					
					}
					$.post('cm/espa/operaciones',{_id:p.data._id.$id, oper:'',campo:'espacio._id'},function(d){
						for (acs in d){		
							/*conceciones*/
							if(d[acs].concesion){	
								var nomb=d[acs].propietario.nomb;var color="";
								if(d[acs].propietario.appat!=null) nomb += " "+d[acs].propietario.appat+" "+d[acs].propietario.apmat;
								p.$w.find('[name=concesion]').append('<tr><td colspan="2" width="200px"><a><span name="'+d[acs].propietario._id.$id+'">'+nomb+'</span></a></td><td style="color:#666666;"><b>Fecha</b></td><td>'+ciHelper.dateFormatLong(d[acs].fecreg)+'</td></tr>');
								p.$w.find('[name='+d[acs].propietario._id.$id+']').click(function(){
									 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
								}).data('id',d[acs].propietario._id.$id).data('tipo_enti',d[acs].propietario.tipo_enti);
								if(d[acs].concesion.fecven){
									if(ciHelper.dateDiffNow(d[acs].concesion.fecven)<=0) color='style="color:#CC0000 !important;"'; 
									p.$w.find('[name=concesion]').append('<tr><td style="color:#666666;font-weight:bold;">Condici&oacute;n</td><td>'+cmEspa.conce.estado[d[acs].concesion.condicion].nomb+'</td><td style="color:#666666;font-weight:bold;">Vencimiento</td><td '+color+'>'+ciHelper.dateFormatLong(d[acs].concesion.fecven)+'</td></tr>');
								}
								else p.$w.find('[name=concesion]').append('<tr><td style="color:#666666;font-weight:bold;">Condici&oacute;n</td><td>'+cmEspa.conce.estado[d[acs].concesion.condicion].nomb+'</td><td colspan=2></td></tr>');
							}							
																
							var $row = p.$w.find('.gridReference').clone();
							var result = d[acs];
							if(result.concesion != null)
								$row.find('li:eq(0)').html('Concesi&oacute;n');							
							if(result.construccion != null)
								$row.find('li:eq(0)').html('Construcci&oacute;n');						
							if(result.asignacion != null)
								$row.find('li:eq(0)').html('Asignaci&oacute;n');						
							if(result.adjuntacion != null)
								$row.find('li:eq(0)').html('Adjuntaci&oacute;n');						
							if(result.traspaso != null)
								$row.find('li:eq(0)').html('Traspaso');						
							if(result.inhumacion != null)
								$row.find('li:eq(0)').html('Inhumaci&oacute;n');						
							if(result.traslado != null)
								$row.find('li:eq(0)').html('Traslado');						
							if(result.colocacion != null)
								$row.find('li:eq(0)').html('Colocaci&oacute;n');
							if(result.anular_asignacion != null)
								$row.find('li:eq(0)').html( 'Anular Asignaci&oacute;n' );
							if(result.anular_concesion != null)
								$row.find('li:eq(0)').html( 'Anular Concesi&oacute;n' );
							if(result.conversion != null)
								$row.find('li:eq(0)').html( 'Conversi&oacute;n' );
							if(result.traslado_ext != null)
								$row.find('li:eq(0)').html( 'Traslado externo (desde otro cementerio)' );
						/*	if(result.anular_asignacion != null)
								$row.find('li:eq(0)').html( 'Anular Asignaci&oacute;n' ).click(function(){
									cmOper.windowDetailsConc({id: $(this).data('id'),modal: true});
								}).data('id',result._id.$id);						
							if(result.anular_concesion != null)
								$row.find('li:eq(0)').html( 'Anular Concesi&oacute;n' ).click(function(){
									cmOper.windowDetailsConc({id: $(this).data('id'),modal: true});
								}).data('id',result._id.$id);			*/			
							$row.find('li:eq(1)').html( 'Registrado' );
							if(result.anulacion!=null)
								$row.find('li:eq(2)').html( '<span style="color:red;">Anulada</span>' );
							else if(result.ejecucion!=null)
								$row.find('li:eq(2)').html( 'Ejecutado' );
							else
								$row.find('li:eq(2)').html( 'Activa' );
							$row.find('li:eq(3)').html( ciHelper.dateFormatLong(result.fecreg) );
							$row.wrapInner('<a class="item" />');
							$row.find('a').click(function(){
	                            cmOper.showDetails({data: $(this).data('data')});
							}).data('data',result);
							p.$w.find('.gridBody:last').append($row.children());		
						}
						$.post('cm/oper/all_hist',{espacio:p.data._id.$id},function(data){
							if(data!=null){
								for(i=0;i<data.length;i++){
									var $row = p.$w.find('.gridReference').clone();
									$row.find('li:eq(0)').html(cmEspa.tipo_oper[data[i].tipo]);
									$row.find('li:eq(1)').html( 'Fecha de Operacion' );
									$row.find('li:eq(2)').html( ciHelper.dateFormatLong(data[i].fecoper) );
									$row.wrapInner('<a class="item" />');
									$row.find('a').click(function(){
										cmOper.windowHistDetails({id: $(this).data('data')._id.$id});
									}).data('data',data[i]);
									p.$w.find('.gridBody:last').append($row.children());
								}
							}
							K.unblock({$element: p.$w});
						},'json');
					},'json');			
				},'json');
			}
		};
		if(p.modal==true) new K.Modal(params);
		else new K.Window(params);
	},
	windowNewServ: function(p){
		if(p==null) p = {};
		$.extend(p,{
			loadConc: function(){
				var $table,espacio,conceptos,variables,servicio,SERV={},__VALUE__=0,cuotas=0;
				SERV = {
					SALDO: 0,
					FECVEN: 0,
					CM_PREC_PERP: 0,
					CM_PREC_TEMP: 0,
					CM_PREC_VIDA: 0,
					CM_ACCE_PREC: 0,
					CM_TIPO_ESPA: 0
				};
				if(p.getEspa!=null){
					espacio = p.getEspa();
					if(espacio!=null){
						if(espacio.precio_perp!=null) SERV.CM_PREC_PERP = espacio.precio_perp;
						if(espacio.precio!=null) SERV.CM_PREC_TEMP = espacio.precio;
						if(espacio.precio_vida!=null) SERV.CM_PREC_VIDA = espacio.precio_vida;
						if(espacio.nicho) SERV.CM_TIPO_ESPA = 1;
						else if(espacio.mausoleo) SERV.CM_TIPO_ESPA = 2;
						else if(espacio.tumba) SERV.CM_TIPO_ESPA = 3;
						else SERV.CM_TIPO_ESPA = 4;
					}
				}
				if(p.getAcce!=null){
					SERV.CM_ACCE_PREC = p.getAcce();
					if(SERV.CM_ACCE_PREC==null){
						return K.notification({
							title: 'Accesorios no seleccionados',
							text: 'Debe seleccionar accesorios para poder realizar los c&aacute;lculos!',
							type: 'error'
						});
					}
				}
				variables = p.$w.data('vars');
				if(variables==null){
					return K.notification({
						title: 'Servicio no seleccionado',
						text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',
						type: 'error'
					});
				}
				for(var i=0,j=variables.length; i<j; i++){
					try{
						if(variables[i].valor=='true') eval('var '+variables[i].cod+' = true;');
						else if(variables[i].valor=='false') eval('var '+variables[i].cod+' = false;');
						else eval('var '+variables[i].cod+' = '+variables[i].valor+';');
					}catch(e){
						console.warn('error en carga de variables');
					}
				}
				$table = p.$w.find('fieldset:last');
				$table.find(".gridBody").empty();
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({
						title: 'Servicio no seleccionado',
						text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',
						type: 'error'
					});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					var $row = $table.find('.gridReference').clone();
					var monto = eval(conceptos[i].formula);
					$row.find('li:eq(0)').html(conceptos[i].nomb);
					if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
						var formula = conceptos[i].formula;
						formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
						$row.find('li:eq(1)').html('<input type="text" size="7" name="codform'+conceptos[i].cod+'">');
						$row.find('[name^=codform]').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
							$(this).change();
						}}).change(function(){
							var val = parseFloat($(this).val()),
							formula = $(this).data('form'),
							cod = $(this).data('cod'),
							$row = $(this).closest('.item');
							eval("var __VALUE"+cod+"__ = "+val+";");
							var monto = eval(formula);
							$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
							$row.data('monto',monto);
							p.calcConc();
						}).data('form',formula).data('cod',conceptos[i].cod);
						$row.find('li:eq(1) .ui-button').css('height','14px');
					}
					$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
					$row.wrapInner('<a class="item" name="'+conceptos[i]._id.$id+'" />');
					$row.find('.item').data('monto',monto);
					$table.find(".gridBody").append( $row.children() );
				}
				p.calcConc();
			},
			calcConc: function(){
				var $table, servicio, conceptos, total = 0, cuotas=0;
				p.$fec_pags_array = [];
				$table = p.$w.find('fieldset:last');
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					return K.notification({
						title: 'Servicio no seleccionado',
						text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',
						type: 'error'
					});
				}
				for(var i=0,j=conceptos.length; i<j; i++){
					total += parseFloat($table.find('.item').eq(i).data('monto'));
				}
				if(conceptos.length!=$table.find('.item').length){
					$table.find('.item:last').remove();
				}
				var $row = $table.find('.gridReference').clone();
				$row.find('li:eq(0),li:eq(1)').addClass('ui-button ui-widget ui-state-default');
				$row.find('li:eq(1)').html('Total');
				$row.find('li:eq(2)').html(ciHelper.formatMon(total));
				$row.wrapInner('<a class="item" />');
				$row.find('.item').data('total',total);
				$table.find(".gridBody").append( $row.children() );
			}
		});
		new K.Modal({
			id: 'windowInNewCuen',
			title: 'Registrar Cobro de Servicio',
			contentURL: 'cm/espa/cuen',
			icon: 'ui-icon-plusthick',
			width: 530,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
					operacion: p.data._id.$id,
						cliente: ciHelper.enti.dbRel(p.data.propietario),
						modulo: 'CM',
						espacio: p.data.espacio,
						servicio: p.$w.find('[name^=serv]').data('data'),
						fecven: p.$w.find('[name=fecven]').val(),
						conceptos: [],
			            total: parseFloat(p.$w.find('.item:last').data('total')),
			            saldo: parseFloat(p.$w.find('.item:last').data('total')),
			            moneda: 'S'
					};
					data.espacio._id = data.espacio._id.$id;
					if(data.servicio==null){
						p.$w.find('[name=btnServ]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un servicio!',
							type: 'error'
						});
					}else{
						data.servicio = {
							_id: data.servicio._id.$id,
							nomb: data.servicio.nomb,
							organizacion: {
				                _id: data.servicio.organizacion._id.$id,
				                nomb: data.servicio.organizacion.nomb
							}
						};
					}
					data.observ = 'Cobro de <b>'+data.servicio.nomb+'</b> correspondiente a <i>'+
						ciHelper.meses[ciHelper.date.getMonth()-1]+' de '+ciHelper.date.getYear()+'</i> para <b>'+
						data.espacio.nomb+'</b>';
					if(data.fecven==''){
						p.$w.find('[name=fecven]').datepicker('show');
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar una fecha de vencimiento!',
							type: 'error'
						});
					}
			        var $table = p.$w.find('fieldset:last'),
			        conceptos = $table.find('[name^=serv]').data('concs');
			        for(var i=0,j=conceptos.length; i<j; i++){
			            var tmp = {
			              concepto: {
			                _id: conceptos[i]._id.$id,
			                cod: conceptos[i].cod,
			                nomb: conceptos[i].nomb,
			                formula: conceptos[i].formula
			              }
			            };
			            if(conceptos[i].clasificador!=null){
			              tmp.concepto.clasificador = {
			                _id: conceptos[i].clasificador._id.$id,
			                nomb: conceptos[i].clasificador.nomb,
			                cod: conceptos[i].clasificador.cod
			              };
			            }
			            if(conceptos[i].cuenta!=null){
			              tmp.concepto.cuenta = {
			                _id: conceptos[i].cuenta._id.$id,
			                descr: conceptos[i].cuenta.descr,
			                cod: conceptos[i].cuenta.cod
			              };
			            }
			            tmp.monto = parseFloat($table.find('.item').eq(i).data('monto'));
			            tmp.saldo = tmp.monto;
			            data.conceptos.push(tmp);
			        }
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cj/cuen/save',data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({
							title: ciHelper.titleMessages.regiGua,
							text: 'La Cuenta por cobrar fue registrada con &eacute;xito!'
						});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowInNewCuen');
				K.block({$element: p.$w});
				p.$w.find('.payment').eq(1).bind('scroll',function(){
					p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
				});
				p.$w.find('[name^=btnServ]').click(function(){
					var $row = $(this).closest('tr');
					mgServ.windowSelect({callback: function(data){
						$row.find('[name^=serv]').html('').removeData('data');
						p.$w.find('[id^=tabsConcPayment] .gridBody').empty();
						$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
							if(concs.serv==null){
								return K.notification({
									title: 'Servicio inv&aacute;lido',
									text: 'El servicio seleccionado no tiene conceptos asociados!',
									type: 'error'
								});
							}
							p.$w.data('vars',concs.vars);
							$row.find('[name^=serv]').html(data.nomb).data('data',data).data('concs',concs.serv);
							p.$w.find('[name^=btnServ]').button('option','text',false);
							p.loadConc();
						},'json');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=fecven]').datepicker();
				$.post('cm/oper/get','id='+p.id,function(data){
					p.data = data;
					p.$w.find('[name=nomb]').html(mgEnti.formatName(data.propietario));
					p.$w.find('[name=dni]').html(mgEnti.formatIden(data.propietario));
					p.$w.find('[name=espacio]').html(data.espacio.nomb);
					p.$w.find('[name=fec]').html(ciHelper.dateFormatNowOnlyDay());
					K.unblock({$element: p.$w});					
				},'json');
			}
		});
	},
	windowSelect: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowSelectEspa',
			title: 'Seleccionar Espacio',
			contentURL: 'cm/espa/select',
			store: false,
			icon: 'ui-icon-person',
			width: 560,
			height: 185,
			buttons: {
				'Seleccionar': function(){
					if(p.$w.find('.ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una Caja!',type: 'error'});
					}
					p.callback(p.$w.find('.ui-state-highlight').closest('.item').data('data'));
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(pa){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelectEspa');
				$(window).resize(function(){
					var $this = $('#windowSelectEspa');
					$this.dialog( "option", "height", $(window).height() )
						.dialog( "option", "width", $(window).width() )
						.dialog( "option", "position", [ 0 , 0 ] )
						.dialog( "option", "draggable", false )
						.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$westPanel = p.$w.find('.ui-layout-west');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			250
				});
				p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
				p.$westPanel.css('z-index',$.ui.dialog.maxZ);
				p.enti = [];
				p.$w.find('[name=fila]').numeric();
				p.$w.find('[name=piso]').numeric();
				p.$w.find('[name=num]').numeric();
				p.$w.find('[name=tipo]').change(function(){
			    	p.$w.find('[name=fila]').val('');
			    	p.$w.find('[name=piso]').val('');
			    	p.$w.find('[name=num]').val('');
					if($(this).find('option:selected').val()=='N'){
			    		p.$w.find('table:eq(0) tr:eq(2),table:eq(0) tr:eq(3),table:eq(0) tr:eq(4)').show();
					}else{
			    		p.$w.find('table:eq(0) tr:eq(2),table:eq(0) tr:eq(3),table:eq(0) tr:eq(4)').hide();
			    	}
			    	var params = {
			    		sector: p.$w.find('[name=sector] option:selected').val(),
			    		tipo: p.$w.find('[name=tipo] option:selected').val(),
			    		num: p.$w.find('[name=num]').val(),
			    		fila: p.$w.find('[name=fila]').val(),
			    		piso: p.$w.find('[name=piso]').val()
			    	};
					if(p.filter!=null)
						params.filter = p.filter;
			    	$grid.reinit({params: params});
				});
				p.$w.find('[name=sector]').change(function(){
			    	var params = {
			    		sector: p.$w.find('[name=sector] option:selected').val(),
			    		tipo: p.$w.find('[name=tipo] option:selected').val(),
			    		num: p.$w.find('[name=num]').val(),
			    		fila: p.$w.find('[name=fila]').val(),
			    		piso: p.$w.find('[name=piso]').val()
			    	};
					if(p.filter!=null)
						params.filter = p.filter;
			    	$grid.reinit({params: params});
				});
				p.$w.find('table:eq(0) button').click(function(){
					var params = {
			    		sector: p.$w.find('[name=sector] option:selected').val(),
			    		tipo: p.$w.find('[name=tipo] option:selected').val(),
			    		num: p.$w.find('[name=num]').val(),
			    		fila: p.$w.find('[name=fila]').val(),
			    		piso: p.$w.find('[name=piso]').val()
			    	};
					if(p.filter!=null)
						params.filter = p.filter;
			    	$grid.reinit({params: params});
				}).button({icons: {primary: 'ui-icon-search'},text: false});
				p.$w.find('[name=fila],[name=piso],[name=num]').keyup(function(e){
					if(e.keyCode == 13){
				    	var params = {
				    		sector: p.$w.find('[name=sector] option:selected').val(),
				    		tipo: p.$w.find('[name=tipo] option:selected').val(),
				    		num: p.$w.find('[name=num]').val(),
				    		fila: p.$w.find('[name=fila]').val(),
				    		piso: p.$w.find('[name=piso]').val()
				    	};
						if(p.filter!=null)
							params.filter = p.filter;
				    	$grid.reinit({params: params});
					}
				});
				var params = {
					sector: p.$w.find('[name=sector] option:selected').val(),
		    		tipo: p.$w.find('[name=tipo] option:selected').val(),
		    		fila: p.$w.find('[name=fila]').val(),
		    		piso: p.$w.find('[name=piso]').val()
				};
				if(p.filter!=null)
					params.filter = p.filter;
				var $grid = new K.grid({
					$el: p.$mainPanel,
					cols: ['','Nombre','Tipo','Sector'],
					data: 'cm/espa/search',
					params: params,
					itemdescr: 'espacio(s)',
					toolbarHTML: '',
					onContentLoaded: function($el){
						$el.find('.search input').css('width','100%');
					},
					fill: function(data,$row){
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>'+data.nomb+'</td>');
						if(data.mausoleo!=null){
							$row.append('<td>Mausoleo</td>');
						}else if(data.nicho!=null){
							$row.append('<td>Nicho</td>');
						}else if(data.tumba!=null){
							$row.append('<td>Tumba</td>');
						}
						$row.append('<td>Cuadrante '+data.sector+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
						}).data('data',data).contextMenu('conMenListSel', {
							onShowMenu: function(e, menu) {
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								return menu;
							},
							bindings: {
								'conMenListSel_sel': function(t){
									p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').click();
								}
							}
						});
						return $row;
					}
				});
			}
		});
	}
};
define(
	['cm/pabe','cm/mapa','cm/ocup','cm/prop','cm/oper'],
	function(cmPabe,cmMapa,cmOcup,cmProp,cmOper){
		return cmEspa;
	}
);