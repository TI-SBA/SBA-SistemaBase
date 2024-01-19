/*******************************************************************************
Conceptos */
tsComp = {
	states: {
		R: {
			descr: "Registrado",
			color: "#CCC",
			label: '<span class="label label-warning">Registrado</span>'
		},
		C: {
			descr: "Cancelado",
			color: "#CCC",
			label: '<span class="label label-info">Cancelado</span>'
		},
		X: {
			descr: "Anulado",
			color: "#CCC",
			label: '<span class="label label-danger">Anulado</span>'
		},
	},
	tipo_pago : {
			"C":{descr:"Cheque"},
			"T":{descr:"Transferencia"}
	},
	init: function(){
		if($('#pageWrapper [child=comp]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/comp',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="comp" />');
					$p.find("[name=tsComp]").after( $row.children() );
				}
				$p.find('[name=tsComp]').data('comp',$('#pageWrapper [child=comp]:first').data('comp'));
				$p.find('[name=tsCompNue]').click(function(){ tsComp.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsCompAll]').click(function(){ tsCompAll.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsComp',
			titleBar: {
				title: 'Comprobantes de Pago'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Abrev.',{n:'Registrado',f:'fecreg'}],
					data: 'ts/comp/lista',
					params: {},
					itemdescr: 'comprobante(s) de pago',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button> <button name="btnAgregarManual" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Manual</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tsComp.windowPreNew();
						});

						$el.find('[name=btnAgregarManual]').click(function(){
							tsComp.windowNew({manual:true});
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
						//console.log(data.estado);
						$row.append('<td>'+tsComp.states[data.estado].label+'</td>');
						$row.append('<td>Comprobante de Pago '+ciHelper.codigos(data.cod,6)+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</td>');
						$row.data('id',data._id.$id).data('estado',data.estado).data('data',data)
						.contextMenu("conMenTsComp", {
							onShowMenu: function($row, menu) {
							    var excep = '';	
								//$(excep+',#conMenSpOrd_about',menu).remove();
								if($row.data('data').estado=='C'||$row.data('data').estado=='X'){
									$(excep+'#conMenTsComp_pag, #conMenTsComp_anu, #conMenTsComp_edi',menu).remove();
								}
								return menu;
							},
							bindings: {
								'conMenTsComp_ver': function(t) {
									tsComp.windowDetails({id: K.tmp.data('id')});
								},
								'conMenTsComp_edi': function(t) {
									tsComp.windowNew({id: K.tmp.data('id')});
								},
								'conMenTsComp_pag': function(t) {
									tsComp.windowPagar({id: K.tmp.data('id')});
								},
								'conMenTsComp_anu': function(t) {
									ciHelper.confirm(
										'Esta seguro(a) de anular este comprobante?',
										function () {
											var data = {
												_id: K.tmp.data('id'),
												data: K.tmp.data('data')
											};
											K.sendingInfo();
											$.post('ts/comp/anu2',data,function(){
												K.clearNoti();
												K.notification({title: 'Comprobante Anulado',text: 'El Comprobante de pago seleccionado ha sido anulado con &eacute;xito!'});
												$('#pageWrapperLeft .ui-state-highlight').click();
											});
										},
										function () {
											//nothing
										}
									);
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowPreNew: function(p){
		if(p==null) p = new Object;
		tsCtpp.windowSelect({callback:function(data){
			tsComp.windowNew({cuentas:data});
		}});
	},
	windowNew: function(p){
		if(p==null) p = new Object;
		p.refereceFillData = function(tipo,data){
			var $element = p.$w.find('#referencia_format');
			switch(tipo){
				case "TEXTO_PLANO":
					$element.find('[name=format_1_referencia]').val(data.texto_plano);
					break;
				case "COMPRA":
					//data.table_ref = [];
					if(data.table_ref.length>0){
						for(var i=0;i<data.table_ref.length;i++){
							p.$w.find('[name=btnRefAddRow]').click();
							var $row = $element.find('[name=table_ref] tbody tr:last');
							//console.log($row);
							$row.find('[name=table_ref_item_referencia]').val(data.table_ref[i].referencia);
							//$row.find('[name=table_ref_item_actividad]').val(data.table_ref[i].actividad);
							$row.find('[name=table_ref_item_factura]').val(data.table_ref[i].factura);
							$row.find('[name=table_ref_item_monto]').val(data.table_ref[i].monto);
							$row.find('[name=table_ref_item_requerimiento]').val(data.table_ref[i].requerimiento);
						}
						if(p.manual){
							var $row = $element.find('[name=table_ref] tfoot tr:last');
							$row.find('[name=table_ref_cert]').val(data.certificacion);
							$row.find('[name=table_ref_prov]').val(data.proveido);
							$row.find('[name=table_ref_adic]').val(data.adicionales);
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "SERVICIO":
					if(data.table_ref.length>0){
						for(var i=0;i<data.table_ref.length;i++){
							p.$w.find('[name=btnRefAddRow]').click();
							var $row = $element.find('[name=table_ref] tbody tr:last');
							$row.find('[name=table_ref_item_referencia]').val(data.table_ref[i].referencia);
							$row.find('[name=table_ref_item_monto]').val(data.table_ref[i].monto);
							$row.find('[name=table_ref_item_requerimiento]').val(data.table_ref[i].requerimiento);
						}
						if(p.manual){
							var $row = $element.find('[name=table_ref] tfoot tr:last');
							$row.find('[name=table_ref_cert]').val(data.certificacion);
							$row.find('[name=table_ref_prov]').val(data.proveido);
							$row.find('[name=table_ref_adic]').val(data.adicionales);
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "LOCACION":
					if(data.table_ref.length>0){
						for(var i=0;i<data.table_ref.length;i++){
							p.$w.find('[name=btnRefAddRow]').click();
							var $row = $element.find('[name=table_ref] tbody tr:last');
							$row.find('[name=table_ref_item_referencia]').val(data.table_ref[i].referencia);
							$row.find('[name=table_ref_item_rxh]').val(data.table_ref[i].rxh);
							$row.find('[name=table_ref_item_monto]').val(data.table_ref[i].monto);
							$row.find('[name=table_ref_item_nombres]').val(data.table_ref[i].nombres);
							$row.find('[name=table_ref_item_oficio]').val(data.table_ref[i].oficio);
							$row.find('[name=table_ref_item_ruc]').val(data.table_ref[i].ruc);
						}
						var $row = $element.find('[name=table_ref] tfoot tr:last');
						$row.find('[name=table_ref_apro]').val(data.aprobacion);
						$row.find('[name=table_ref_cont]').val(data.contratos);
					
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "SERVICIO_TELEFONICO":
					//data.table_ref = [];
					if(data.table_ref.length>0){
						for(var i=0;i<data.table_ref.length;i++){
							p.$w.find('[name=btnRefAddRow]').click();
							var $row = $element.find('[name=table_ref] tbody tr:last');
							//console.log($row);
							$row.find('[name=table_ref_item_programa]').val(data.table_ref[i].programa);
							$row.find('[name=table_ref_item_numero]').val(data.table_ref[i].numero);
							$row.find('[name=table_ref_item_recibo]').val(data.table_ref[i].recibo);
							$row.find('[name=table_ref_item_monto]').val(data.table_ref[i].monto);
							$row.find('[name=table_ref_item_referencia]').val(data.table_ref[i].referencia);
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
			}
		};
		p.referenceReturnData = function(){
			var tipo = p.$w.find('[name=tipo_referencia] :selected').val();
			var $element = p.$w.find('#referencia_format');
			var data = {
				tipo:tipo
			};
			switch(tipo){
				case "TEXTO_PLANO":
					data.texto_plano = $element.find('[name=format_1_referencia]').val();
					break;
				case "COMPRA":
					data.table_ref = [];
					if($element.find('[name=table_ref] tbody tr').length>0){
						for(var i=0;i<$element.find('[name=table_ref] tbody tr').length;i++){
							var $row = $element.find('[name=table_ref] tbody tr').eq(i);
							var _item = {
								referencia:$row.find('[name=table_ref_item_referencia]').val(),
								//actividad:$row.find('[name=table_ref_item_actividad]').val(),
								factura:$row.find('[name=table_ref_item_factura]').val(),
								monto:$row.find('[name=table_ref_item_monto]').val(),
								requerimiento:$row.find('[name=table_ref_item_requerimiento]').val(),
							};
							if(!parseFloat(_item.monto)){
								$row.find('[name=table_ref_item_monto]').focus();
								K.msg({title:'Datos Requeridos',text:'Un monto ingresado en la tabla de referencia es incorrecto',type:'error'});
								return null;
							}else{
								data.table_ref.push(_item);
							}
						}
						if(p.manual){
							for(var i=1;i<$element.find('[name=table_ref] tfoot tr').length;i++){
								var $row = $element.find('[name=table_ref] tfoot tr').eq(i);
								data.certificacion=$row.find('[name=table_ref_cert]').val();
								data.proveido=$row.find('[name=table_ref_prov]').val();
								data.adicionales=$row.find('[name=table_ref_adic]').val();
							}
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "SERVICIO":
					data.table_ref = [];
					if($element.find('[name=table_ref] tbody tr').length>0){
						for(var i=0;i<$element.find('[name=table_ref] tbody tr').length;i++){
							var $row = $element.find('[name=table_ref] tbody tr').eq(i);
							var _item = {
								referencia:$row.find('[name=table_ref_item_referencia]').val(),
								monto:$row.find('[name=table_ref_item_monto]').val(),
								requerimiento:$row.find('[name=table_ref_item_requerimiento]').val(),
							};
							if(!parseFloat(_item.monto)){
								$row.find('[name=table_ref_item_monto]').focus();
								K.msg({title:'Datos Requeridos',text:'Un monto ingresado en la tabla de referencia es incorrecto',type:'error'});
								return null;
							}else{
								data.table_ref.push(_item);
							}
						}
						if(p.manual){
							for(var i=1;i<$element.find('[name=table_ref] tfoot tr').length;i++){
								var $row = $element.find('[name=table_ref] tfoot tr').eq(i);
								data.certificacion=$row.find('[name=table_ref_cert]').val();
								data.proveido=$row.find('[name=table_ref_prov]').val();
								data.adicionales=$row.find('[name=table_ref_adic]').val();
							}
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "LOCACION":
					data.table_ref = [];
					if($element.find('[name=table_ref] tbody tr').length>0){
						for(var i=0;i<$element.find('[name=table_ref] tbody tr').length;i++){
							var $row = $element.find('[name=table_ref] tbody tr').eq(i);
							var _item = {
								referencia:$row.find('[name=table_ref_item_referencia]').val(),
								rxh:$row.find('[name=table_ref_item_rxh]').val(),
								monto:$row.find('[name=table_ref_item_monto]').val(),
								nombres:$row.find('[name=table_ref_item_nombres]').val(),
								oficio:$row.find('[name=table_ref_item_oficio]').val(),
								ruc:$row.find('[name=table_ref_item_ruc]').val(),
							};
							if(!parseFloat(_item.monto)){
								$row.find('[name=table_ref_item_monto]').focus();
								K.msg({title:'Datos Requeridos',text:'Un monto ingresado en la tabla de referencia es incorrecto',type:'error'});
								return null;
							}else{
								data.table_ref.push(_item);
							}
						}
						for(var i=1;i<$element.find('[name=table_ref] tfoot tr').length;i++){
							var $row = $element.find('[name=table_ref] tfoot tr').eq(i);
							data.aprobacion=$row.find('[name=table_ref_apro]').val();
							data.contratos=$row.find('[name=table_ref_cont]').val();
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "SERVICIO_TELEFONICO":
					data.table_ref = [];
					if($element.find('[name=table_ref] tbody tr').length>0){
						for(var i=0;i<$element.find('[name=table_ref] tbody tr').length;i++){
							var $row = $element.find('[name=table_ref] tbody tr').eq(i);
							var _item = {
								programa:$row.find('[name=table_ref_item_programa]').val(),
								numero:$row.find('[name=table_ref_item_numero]').val(),
								recibo:$row.find('[name=table_ref_item_recibo]').val(),
								monto:$row.find('[name=table_ref_item_monto]').val(),
								referencia:$row.find('[name=table_ref_item_referencia]').val(),
							};
							if(!parseFloat(_item.monto)){
								$row.find('[name=table_ref_item_monto]').focus();
								K.msg({title:'Datos Requeridos',text:'Un monto ingresado en la tabla de referencia es incorrecto',type:'error'});
								return null;
							}else{
								data.table_ref.push(_item);
							}
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
			}
			return data;
		};
		p.referenceManager = function(){
			var tipo = p.$w.find('[name=tipo_referencia] :selected').val();
			var $element = p.$w.find('#referencia_format');
			$element.empty();
			switch(tipo){
				case "TEXTO_PLANO":
					//$element.append('<div class="form-group"><label>Referencia</label><textarea value="format_1_referencia" class="form-control"></textarea></div>');
					$element.append('<div class="form-group"><label>Referencia</label><textarea name="format_1_referencia" class="form-control"></textarea></div>');
					break;
				case "COMPRA":
					$element.append('<table name="table_ref" class="table table-bordered" />');
					//$element.find('table').append('<thead><tr><th colspan="6"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>REFERENCIA</th><th>ACTIVIDAD</th><th>FACTURA</th><th>MONTO</th><th>REQUERIMIENTO</th><th></th></tr></thead>')
					$element.find('table').append('<thead><tr><th colspan="6"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>REFERENCIA</th><th>FACTURA</th><th>MONTO</th><th>REQUERIMIENTO</th><th></th></tr></thead>')
					$element.find('table').append('<tbody />');
					$element.find('table').append('<tfoot />');
					//$element.find('tfoot').append('<tr><td colspan="3">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					$element.find('tfoot').append('<tr><td colspan="2">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					
					if(p.manual){
						p.$w.find('[name=desc]').val("COMPRA DE [TEXTO AQUI] ");
						$element.find('tfoot').append('<tr><td colspan="1">CERTIFICACION </td><td><input type="text" name="table_ref_cert" class="form-control" value="C.I. N? XXX-XXXX-SBPA-OPP"></td><td colspan="1">PROVEIDO </td><td><input type="text" name="table_ref_prov" class="form-control" value="C.I. N? XXX-XXXX-SBPA-OPP"></td><td colspan="1">ADICIONALES </td><td><input type="text" name="table_ref_adic" class="form-control" value="C.I. N? XXX-XXXX-SBPA-OPP"></td></tr>');
					}

					//$element.append('No esta disponible este formato');
					$element.find('[name=btnRefAddRow]').click(function(){
						var $row = $('<tr class="item" />');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
						//$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_actividad"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_factura"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_requerimiento"></td>');
						$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');
						$row.find('[name=btnRefRemoveRow]').click(function(){
							$(this).closest('tr').remove();
						});
						$row.find('[name=table_ref_item_monto]').keyup(function(){
							var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
							var total = 0;
							for(var i=0;i<$inputs_monto.length;i++){
								if(parseFloat($inputs_monto.eq(i).val())){
									total+=parseFloat($inputs_monto.eq(i).val());
								}
							}
							p.$w.find('[name=table_ref_total]').val(K.round(total,2));
						});
						$element.find('tbody').append($row);
					});
					break;
				case "SERVICIO":
					$element.append('<table name="table_ref" class="table table-bordered" />');
					$element.find('table').append('<thead><tr><th colspan="6"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>REFERENCIA</th><th>FACTURA</th><th>MONTO</th><th>REQUERIMIENTO</th><th></th></tr></thead>')
					$element.find('table').append('<tbody />');
					$element.find('table').append('<tfoot />');
					$element.find('tfoot').append('<tr><td colspan="2">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					
					if(p.manual){
						p.$w.find('[name=desc]').val("PAGO POR SERVICIO DE [TEXTO AQUI] ");
						$element.find('tfoot').append('<tr><td colspan="1">CERTIFICACION </td><td><input type="text" name="table_ref_cert" class="form-control" value="C.I. N? XXX-XXXX-SBPA-OPP"></td><td colspan="1">PROVEIDO </td><td><input type="text" name="table_ref_prov" class="form-control" value="C.I. N? XXX-XXXX-SBPA-OPP"></td><td colspan="1">ADICIONALES </td><td><input type="text" name="table_ref_adic" class="form-control" value="C.I. N? XXX-XXXX-SBPA-OPP"></td></tr>');
					}

					//$element.append('No esta disponible este formato');
					$element.find('[name=btnRefAddRow]').click(function(){
						var $row = $('<tr class="item" />');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_requerimiento"></td>');
						$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');
						$row.find('[name=btnRefRemoveRow]').click(function(){
							$(this).closest('tr').remove();
						});
						$row.find('[name=table_ref_item_monto]').keyup(function(){
							var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
							var total = 0;
							for(var i=0;i<$inputs_monto.length;i++){
								if(parseFloat($inputs_monto.eq(i).val())){
									total+=parseFloat($inputs_monto.eq(i).val());
								}
							}
							p.$w.find('[name=table_ref_total]').val(K.round(total,2));
						});
						$element.find('tbody').append($row);
					});
					break;
				case "LOCACION":
					if(p.manual){
						p.$w.find('[name=desc]').val("PAGO PARA PRESTAR ASISTENCIA A LA BENEFICENCIA, PRESTANDO LOS SERVICIOS QUE SE DESCRIBEN EN LOS TERMINOS DE REFERENCIA DEL SERVICIO - [REEMPLAZAR TEXTO AQUI]");					
					}
					
					$element.append('<table name="table_ref" class="table table-bordered" />');
					$element.find('table').append('<thead><tr><th colspan="6"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>REFERENCIA</th><th>R.X H.</th><th>MONTO</th><th>NOMBRES</th><th>OFICIO</th><th>RUC</th><th></th></tr></thead>')
					$element.find('table').append('<tbody />');
					$element.find('table').append('<tfoot />');
					$element.find('tfoot').append('<tr><td colspan="2">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					$element.find('tfoot').append('<tr><td colspan="1">APROBACION </td><td><input type="text" name="table_ref_apro" class="form-control" value="C.I. N? XXX-XXXX-SBPA-XXXX"></td><td><td colspan="1">CONTRATOS</td><td><input type="text" name="table_ref_cont" class="form-control" value="C.I. N? XXX/-XXXX-SBPA-XXXX"></td></td></tr>');
					//$element.append('No esta disponible este formato');
					$element.find('[name=btnRefAddRow]').click(function(){
						var $row = $('<tr class="item" />');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_rxh"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_nombres"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_oficio"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_ruc"></td>');
						$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');
						$row.find('[name=btnRefRemoveRow]').click(function(){
							$(this).closest('tr').remove();
						});
						$row.find('[name=table_ref_item_monto]').keyup(function(){
							var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
							var total = 0;
							for(var i=0;i<$inputs_monto.length;i++){
								if(parseFloat($inputs_monto.eq(i).val())){
									total+=parseFloat($inputs_monto.eq(i).val());
								}
							}
							p.$w.find('[name=table_ref_total]').val(K.round(total,2));
						});
						$element.find('tbody').append($row);
					});
					break;
				case "SERVICIO_TELEFONICO":
					$element.append('<table name="table_ref" class="table table-bordered" />');
					$element.find('table').append('<thead><tr><th colspan="7"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>PROGRAMA</th><th>NRO. RPM</th><th>RECIBO NRO.</th><th>CARGO FIJO</th><th>REFERENCIA</th><th></th></tr></thead>')
					$element.find('table').append('<tbody />');
					$element.find('table').append('<tfoot />');
					$element.find('tfoot').append('<tr><td colspan="3">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					//$element.append('No esta disponible este formato');
					$element.find('[name=btnRefAddRow]').click(function(){
						var $row = $('<tr class="item" />');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_programa"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_numero"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_recibo"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
						$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');
						$row.find('[name=btnRefRemoveRow]').click(function(){
							$(this).closest('tr').remove();
						});
						$row.find('[name=table_ref_item_monto]').keyup(function(){
							var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
							var total = 0;
							for(var i=0;i<$inputs_monto.length;i++){
								if(parseFloat($inputs_monto.eq(i).val())){
									total+=parseFloat($inputs_monto.eq(i).val());
								}
							}
							p.$w.find('[name=table_ref_total]').val(K.round(total,2));
						});
						$element.find('tbody').append($row);
					});
					break;
			}
		};
		p.sumBene = function(){
			if(p.$w.find('[name=tab_c] .item').length>1&&p.$w.find('[name=tab_t] .item').length>1){
				var total_b_1 = 0;
				for(i=0;i<(p.$w.find('[name=tab_t] .item').length-1);i++){
					total_b_1+=parseFloat(p.$w.find('[name=tab_t] .item').eq(i).data('monto'));
				}
				p.$w.find('[name=tab_t] .total').find('li:eq(2)').html(K.round(total_b_1,2));
				var total_b_2 = 0;
				for(i=0;i<(p.$w.find('[name=tab_c] .item').length-1);i++){
					total_b_2+=parseFloat(p.$w.find('[name=tab_c] .item').eq(i).data('monto'));
				}
				//console.log(total_b_1);
				//console.log(total_b_2);
				p.$w.find('[name=tab_c] .total').find('li:eq(2)').html(K.round(total_b_2,2));
			}
		};
		p.calcuateDetGasto = function(){
			var conc_total_pago = 0;
			var conc_total_desc = 0;
			var conc_total_neto = 0;
			if(p.$w.find('[name=grid_det2] tbody tr').length>0){
				for(var i=0;i<p.$w.find('[name=grid_det2] tbody tr').length;i++){
					var $row = p.$w.find('[name=grid_det2] tbody tr').eq(i);
					var _item_pag = parseFloat($row.find('td').eq(2).html());
					var _item_des = parseFloat($row.find('td').eq(3).html());
					conc_total_pago+=_item_pag;
					conc_total_desc+=_item_des;
				}
				conc_total_neto = conc_total_pago-conc_total_desc;
			}
			p.$w.find('[name=conc_total_pago]').val(K.round(conc_total_pago,2));
			p.$w.find('[name=conc_total_desc]').val(K.round(conc_total_desc,2));
			p.$w.find('[name=conc_total_neto]').val(K.round(conc_total_neto,2));

			if(p.manual){
				var tipo = p.$w.find('[name=tipo_referencia] :selected').val();
				//POR PEDIDOS DE TESORERIA PARA HACERLO MAS RAPIDO SE AUTOLLENAN LOS CAMPOS AL INGRESAR CONCEPTOS
				var coprde_default = "51a939724d4a13a812000158";
				var coprha_default = "51a93c204d4a13a812000162";
				switch(tipo){
					case "LOCACION":
						var esga_default = "51f291e24d4a13ac0b000128";
						/*ESTADISTICA DE GASTO*/
						$.post('pr/clas/get','id='+esga_default,function(data){
							var $row = $('<tr class="item">');
							$row.append('<td>'+data.cod+' - '+data.nomb+'</td>');
							$row.append('<td>'+ciHelper.formatMon(conc_total_neto)+'</td>');
							$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								p.refresh();
							});
							$row.data('data',{
								clasificador: prClas.dbRel(data),
								monto: conc_total_neto
							});
							p.$w.find('[name=grid_est] tbody').append($row);
						},'json');
						var copade_default = "51b09b864d4a13b417000011";
						var copaha_default = "51a8fcc74d4a13540a0000a4";
					break;
					case "COMPRA":
						var copade_default = "51b09b864d4a13b417000011";
						var copaha_default = "51a8fcc74d4a13540a0000a4";
					break;
					case "SERVICIO":
						var copade_default = "51b09b864d4a13b417000011";
						var copaha_default = "51a8fcc74d4a13540a0000a4";
					break;
				}

				/*CONTABILIDAD PRESUPUESTAL*/
				$.post('ct/pcon/get','id='+coprde_default,function(data){
					var $row = $('<tr class="item">');
					$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+ciHelper.formatMon(conc_total_neto)+'</td>');
						$row.append('<td>');
					$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
					$row.find('button').click(function(){
						$(this).closest('.item').remove();
						p.refresh();
					});
					$row.data('data',{
						cuenta: ctPcon.dbRel(data),
						tipo: "D",
						monto: conc_total_neto
					});
					p.$w.find('[name=grid_cont_pres] tbody').append($row);
				},'json');
				$.post('ct/pcon/get','id='+coprha_default,function(data){
					var $row = $('<tr class="item">');
					$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(conc_total_neto)+'</td>');
					$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
					$row.find('button').click(function(){
						$(this).closest('.item').remove();
						p.refresh();
					});
					$row.data('data',{
						cuenta: ctPcon.dbRel(data),
						tipo: "H",
						monto: conc_total_neto
					});
					p.$w.find('[name=grid_cont_pres] tbody').append($row);
				},'json');

				/*CONTABILIDAD PATRIMONIAL*/
				$.post('ct/pcon/get','id='+copade_default,function(data){
					var $row = $('<tr class="item">');
					$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+ciHelper.formatMon(conc_total_neto)+'</td>');
						$row.append('<td>');
					$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
					$row.find('button').click(function(){
						$(this).closest('.item').remove();
						p.refresh();
					});
					$row.data('data',{
						cuenta: ctPcon.dbRel(data),
						tipo: "D",
						monto: conc_total_neto
					});
					p.$w.find('[name=grid_cont_patr] tbody').append($row);
				},'json');
				$.post('ct/pcon/get','id='+copaha_default,function(data){
					var $row = $('<tr class="item">');
					$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>');
						$row.append('<td>'+ciHelper.formatMon(conc_total_neto)+'</td>');
					$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
					$row.find('button').click(function(){
						$(this).closest('.item').remove();
						p.refresh();
					});
					$row.data('data',{
						cuenta: ctPcon.dbRel(data),
						tipo: "H",
						monto: conc_total_neto
					});
					p.$w.find('[name=grid_cont_patr] tbody').append($row);
				},'json');
				
			}
			


		}
		$.extend(p,{
			cuenta: [],
			refresh: function(){
				var data = {};
				if(p.$w.find('[name=nomb]').val()!='') data.nomb = p.$w.find('[name=nomb]').val();
				if(p.$w.find('[name=fec]').val()!='') data.fec = p.$w.find('[name=fec]').val();
				if(p.$w.find('[name=gridProg] tbody .item').length!=0){
					data.cod_programatica = [];
					for(var i=0,j=p.$w.find('[name=gridProg] tbody .item').length; i<j; i++){
						var tmp = p.$w.find('[name=gridProg] tbody .item').eq(i).data('data');
						data.cod_programatica.push({
							actividad: tmp.actividad._id.$id,
							componente: tmp.componente._id.$id
						});
					}
				}
				if(p.$w.find('[name=gridPres] tbody .item').length!=0){
					data.cont_presupuestal = [];
					for(var i=0,j=p.$w.find('[name=gridPres] tbody .item').length; i<j; i++){
						var tmp = p.$w.find('[name=gridPres] tbody .item').eq(i).data('data');
						data.cont_presupuestal.push(tmp);
					}
				}
				if(p.$w.find('[name=gridPatr] tbody .item').length!=0){
					data.cont_patrimonial = [];
					for(var i=0,j=p.$w.find('[name=gridPatr] tbody .item').length; i<j; i++){
						var tmp = p.$w.find('[name=gridPatr] tbody .item').eq(i).data('data');
						data.cont_patrimonial.push(tmp);
					}
				}
				if(p.$w.find('[name=gridRete] tbody .item').length!=0){
					data.retenciones = [];
					for(var i=0,j=p.$w.find('[name=gridRete] tbody .item').length; i<j; i++){
						var tmp = p.$w.find('[name=gridRete] tbody .item').eq(i).data('data');
						data.retenciones.push(tmp);
					}
				}
				$('iframe').attr('src', 'ts/comp/preview?'+$.param(data));
			}
		});
		/*********************************
		*
		*
		*Falta implementar el campo SECTOR
		*
		*
		**********************************/
		p.base_cod_prog = {
			sector:'34',
			pliego:'--',
			programa:'--',
			subprograma:'--',
			proyecto:'--',
			obra:'--',
			fuente:'--'
		};
		p.push_cod_prog = function(actividad, componente){
			$.post('ts/comp/cod_prog','actividad='+actividad+'&componente='+componente,function(res){
				console.log(res);
				if(res.pliego!=null){
					if(p.base_cod_prog.pliego=='--'){
						p.base_cod_prog.pliego = res.pliego.cod;
					}else if(p.base_cod_prog.pliego=='V'){
						//does nothing
					}else{
						if(p.base_cod_prog.pliego!=res.pliego.cod){
							p.base_cod_prog.pliego = 'V';
						}
					}
				}
				if(res.programa!=null){
					if(p.base_cod_prog.programa=='--'){
						p.base_cod_prog.programa = res.programa.cod;
					}else if(p.base_cod_prog.programa=='V'){
						//does nothing
					}else{
						if(p.base_cod_prog.programa!=res.programa.cod){
							p.base_cod_prog.programa = 'V';
						}
					}
				}
				
				if(res.subprograma!=null){
					if(p.base_cod_prog.subprograma=='--'){
						p.base_cod_prog.subprograma = res.subprograma.cod;
					}else if(p.base_cod_prog.subprograma=='V'){
						//does nothing
					}else{
						if(p.base_cod_prog.subprograma!=res.subprograma.cod){
							p.base_cod_prog.subprograma = 'V';
						}
					}
				}
				
				if(res.proyecto!=null){
					if(p.base_cod_prog.proyecto=='--'){
						p.base_cod_prog.proyecto = res.proyecto.cod;
					}else if(p.base_cod_prog.proyecto=='V'){
						//does nothing
					}else{
						if(p.base_cod_prog.proyecto!=res.proyecto.cod){
							p.base_cod_prog.proyecto = 'V';
						}
					}
				}
				
				if(res.obra!=null){
					if(p.base_cod_prog.obra=='--'){
						p.base_cod_prog.obra = res.obra.cod;
					}else if(p.base_cod_prog.obra=='V'){
						//does nothing
					}else{
						if(p.base_cod_prog.obra!=res.obra.cod){
							p.base_cod_prog.obra = 'V';
						}
					}
				}

				/*Llenar campos en html*/
				p.$w.find('[name=cod_prog_sector]').val(p.base_cod_prog.sector);
				p.$w.find('[name=cod_prog_pliego]').val(p.base_cod_prog.pliego);
				p.$w.find('[name=cod_prog_programa]').val(p.base_cod_prog.programa);
				p.$w.find('[name=cod_prog_subprograma]').val(p.base_cod_prog.subprograma);
				p.$w.find('[name=cod_prog_proyecto]').val(p.base_cod_prog.proyecto);
				p.$w.find('[name=cod_prog_obra]').val(p.base_cod_prog.obra);
			},'json');
		};
		new K.Panel({
			title: 'Nuevo Comprobante de Pago',
			contentURL: 'ts/comp/edit',
			store: false,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					if(p.id!=null){
						data._id = p.id;
					}
					data.estado= "R";
					//data.cod = p.$w.find('[name=cod]').html();
					data.cod = p.$w.find('[name=cod]').val();
					data.descr = p.$w.find('[name=desc]').val();
					data.nomb = p.$w.find('[name=nomb]').val();
					if(data.nomb==''){
						p.$w.find('[name=nomb]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					data.referencia=p.referenceReturnData();//Obtiene informacion de las referencias dinamicas
					if(data.referencia==null){
						return false;
					}

					var cuenta_banco = p.$w.find('[name=pag_cuenta] :selected').data('data');
					data.cuenta_banco = new Object;
					data.cuenta_banco._id = cuenta_banco._id.$id;
					data.cuenta_banco.cod_banco = cuenta_banco.cod_banco;
					data.cuenta_banco.nomb = cuenta_banco.nomb;
					data.cuenta_banco.cod = cuenta_banco.cod;
					data.cuenta_banco.moneda = cuenta_banco.moneda;
					if(!cuenta_banco.cuenta){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ha ocurrido un error la Cuenta Bancaria Seleccionada no tiene cuenta contable relacionada, comuniquelo al area correspondiente!',type: 'error'});
					}
					data.cuenta_banco.cuenta = {
						_id:cuenta_banco.cuenta._id.$id,
						cod:cuenta_banco.cuenta.cod,
						descr:cuenta_banco.cuenta.descr
					};
					/******* Comprobante*/
					data.moneda = "S";
					var serie = p.$w.find('[name=serie]').val();
					if(serie!=""){
						data.comprobante = new Object;
						data.comprobante.serie = serie;
						data.comprobante.num = p.$w.find('[name=num]').val();
						if(data.comprobante.num==""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un numero par el comprobante!',type: 'error'});
						}
						data.comprobante.tipo = p.$w.find('[name=compr] :selected').val();
					}
					data.items = new Array;
					if(p.cuentas!=null){
						for(i=0;i<p.cuentas.length;i++){
							var item = {
									cuenta_pagar:{
										_id : p.cuentas[i]._id.$id,
										beneficiario : p.cuentas[i].beneficiario,
										motivo : p.cuentas[i].motivo
									}
							};
							item.conceptos=new Object;
							for(j=0;j<p.cuentas[i].conceptos.length;j++){
								item.conceptos[j]=new Object;
								if(p.cuentas[i].conceptos[j].concepto){
									item.conceptos[j].concepto = p.cuentas[i].conceptos[j].concepto;
									item.conceptos[j].monto = p.cuentas[i].conceptos[j].monto;
								}else{
									item.conceptos[j]={
											tipo : p.cuentas[i].conceptos[j].tipo,
											observ : p.cuentas[i].conceptos[j].observ,
											monto : p.cuentas[i].conceptos[j].monto
									};
								}
							}
							data.items.push(item);
						}
					}

					if(p.manual){
						data.manual = 1;
						var _items = {
							modulo:"TS",
							motivo:'Cuenta por pagar Manual',
							origen:'M',
							total_pago: p.$w.find('[name=conc_total_pago]').val(),
							total_desc: p.$w.find('[name=conc_total_desc]').val(),
							total: p.$w.find('[name=conc_total_neto]').val(),
							conceptos:[]
						};
						if(p.$w.find('[name=grid_det2] tbody tr').length>0){
							for (var i=0;i<p.$w.find('[name=grid_det2] tbody tr').length;i++){
								var $row = p.$w.find('[name=grid_det2] tbody tr').eq(i);
								var tipo = 'P';
								var monto = parseFloat($row.find('td').eq(2).html());
								if(parseFloat($row.find('td').eq(3).html())>0){
									tipo = 'D';
									monto = parseFloat($row.find('td').eq(3).html());
								}
								_items.conceptos.push({
									tipo:tipo,
									observ:$row.find('td').eq(1).text(),
									monto:monto,
									moneda:'S'
								});
							}
							data.items.push(_items);
						}else{
							K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Este es un comprobante manual, debe ingresarse al menos un concepto!',
								type: 'error'
							});
							return false;
						}
					}

					data.tipo_pago = p.$w.find('[name=forma_pago] :selected').val();
					data.beneficiarios = new Array;
					$bene_cheque = p.$w.find('[name=grid_form_pago] .item');
					if(data.tipo_pago == "C"){
						for(i=0;i<($bene_cheque.length);i++){
							var bene = new Object;
							bene.beneficiario = $bene_cheque.eq(i).data('data');						
							bene.cheque = $bene_cheque.eq(i).find('[name=cheque]').val();
							if(bene.cheque==""){
								$bene_cheque.eq(i).find('[name=cheque]').focus();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe poner un cheque para el beneficiario : '+bene.beneficiario.nomb+' !',type: 'error'});
							}
							bene.monto = $bene_cheque.eq(i).find('[name=form_pago_subtotal]').val();
							data.beneficiarios.push(bene);
						}
					}else if(data.tipo_pago == "T"){
						for(i=0;i<($bene_cheque.length);i++){
							var bene = new Object;
							bene.beneficiario = $bene_cheque.eq(i).data('data');
							bene.monto = $bene_cheque.eq(i).find('[name=form_pago_subtotal]').val();
							data.beneficiarios.push(bene);
						}
					}
					if(data.beneficiarios.length==0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Este comprobante debe tener al menos un beneficiario!',type: 'error'});
					}
					data.objeto_gasto = new Array;
					//$objeto_gasto = p.$w.find('#grid_est [name=myname] .item');
					$objeto_gasto = p.$w.find('[name=grid_est] .item');
					if($objeto_gasto.length>0){
						for(i=0;i<$objeto_gasto.length;i++){
							var obj_g = {
									clasificador: {
										//_id: $objeto_gasto.eq(i).data('data').clasificador._id.$id,
										_id: $objeto_gasto.eq(i).data('data').clasificador._id,
										nomb: $objeto_gasto.eq(i).data('data').clasificador.nomb,
										cod: $objeto_gasto.eq(i).data('data').clasificador.cod
									},
									monto: $objeto_gasto.eq(i).data('data').monto
							};
							data.objeto_gasto.push(obj_g);
						}
					}
					data.cont_patrimonial = new Array;
					$cont_patr = p.$w.find('[name=grid_cont_patr] .item');
					if($cont_patr.length>0){
						for(i=0;i<$cont_patr.length;i++){
							data.cont_patrimonial.push($cont_patr.eq(i).data('data'));
						}
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un item en Contabilidad Patrimonial!',type: 'error'});
					}
					data.cont_presupuestal = new Array;
					$cont_pres = p.$w.find('[name=grid_cont_pres] .item');
					if($cont_pres.length>0){
						for(i=0;i<$cont_pres.length;i++){
							data.cont_presupuestal.push($cont_pres.eq(i).data('data'));
						}
					}
					data.retenciones = new Array;
					$rete = p.$w.find('[name=grid_rete_dedu] .item');
					if($rete.length>0){
						for(i=0;i<$rete.length;i++){
							data.retenciones.push($rete.eq(i).data('data'));
						}
					}
					data.cod_programatica = {
						sector:p.$w.find('[name=cod_prog_sector]').val(),
						pliego:p.$w.find('[name=cod_prog_pliego]').val(),
						programa:p.$w.find('[name=cod_prog_programa]').val(),
						subprograma:p.$w.find('[name=cod_prog_subprograma]').val(),
						proyecto:p.$w.find('[name=cod_prog_proyecto]').val(),
						obra:p.$w.find('[name=cod_prog_obra]').val()
					};
					if(data.cod_programatica.pliego==""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No esta completa la codificacion programatica',type: 'error'});
					}
					var fuente = p.$w.find('[name=cod_prog_fuente] :selected').data('data');
					data.fuente = new Object;
					data.fuente._id = fuente._id.$id;
					data.fuente.cod = fuente.cod;
					//console.log("============== START SEND DATA =================");
					//console.log(data);
					//return false;
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post('ts/comp/save',data,function(rpta){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Comprobante fue registrado con &eacute;xito!'});
						tsComp.windowDetails({id:rpta});
						tsComp.init();
						//$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					tsComp.init();
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				$.post('ts/comp/edit_data',function(data){
					//p.$w.find('[name=cod]').html(data.cod);
					p.$w.find('[name=cod]').val(data.cod);
				},'json');
				p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd()).datepicker();
				p.$w.find('[name=tipo_referencia]').change(function(){
					p.referenceManager();
				}).change();
				new K.grid({
					$el: p.$w.find('[name=grid_det]'),
					search: false,
					pagination: false,
					cols: ['Nro.','Cuenta por Pagar','Actividad/Componente','Monto'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_det2]'),
					search: false,
					pagination: false,
					cols: ['Nro.','Cuenta por Pagar','Pago','Descuento'],
					onlyHtml: true,
					toolbarHTML: '<div class="form-group col-sm-3"><input type="text" placeholder="ingresar Concepto" name="manual_conc_observ" class="form-control"></div>'+
						'<div class="form-group col-sm-3"><input type="number" placeholder="ingresar Monto" name="manual_conc_monto" class="form-control"></div>'+
						'<div class="form-group col-sm-3"><select name="manual_conc_tipo" class="form-control"><option value="P">Pago</option><option value="D">Descuento</option></select></div>'+
						'<div class="col-sm-3"><button class="btn btn-info" name="btnAddConcepto"><i class="fa fa-plus"></i> Agregar</button></div>',
					onContentLoaded: function($el){
						if(p.manual){
							$el.closest('.datagrid-header-left').removeClass('col-md-8');
							/*var $row = $('<tr class="item" />');
							$row.append('<td>1</td>');
							$row.append('<td>Cuenta por Pagar Manual</td>');
							$row.append('<td>0.00</td>').css({'text-align':'right'});
							$row.append('<td>0.00</td>').css({'text-align':'right'});
							p.$w.find("[name=grid_det2] tbody").append( $row );*/

							$el.find('[name=btnAddConcepto]').click(function(){
								var concepto = p.$w.find('[name=manual_conc_observ]').val();
								var monto = p.$w.find('[name=manual_conc_monto]').val();
								if(!parseFloat(monto)){
									K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'El monto ingresado es incorrecto!',
										type: 'error'
									});
									return $el.find('[name=manual_conc_monto]').focus();	
								}
								if(concepto==''){
									K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe ingresar un concepto para continuar!',
										type: 'error'
									});
									return $el.find('[name=manual_conc_observ]').focus();		
								}
								var tipo = p.$w.find('[name=manual_conc_tipo] :selected').val();
								var $row2 = $('<tr class="item" />');
								$row2.append('<td><button name="btnDeleteConcManual" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></td>');
								$row2.append('<td><span name="manual_conc_i_observ">'+concepto+'</span></td>');
								if(tipo=="P"){
									$row2.append('<td>'+K.round(monto,2)+'</td>').css({'text-align':'right'});
									$row2.append('<td>0.00</td>').css({'text-align':'right'});
								}else if(tipo=="D"){
									$row2.append('<td>0.00</td>').css({'text-align':'right'});
									$row2.append('<td>'+K.round(monto,2)+'</td>').css({'text-align':'right'});
								}
								$row2.find('[name=btnDeleteConcManual]').click(function(){
									$(this).closest('tr').remove();
									p.calcuateDetGasto();
								});
								p.$w.find("[name=grid_det2] tbody").append($row2);

								p.$w.find('[name=manual_conc_observ]').val("");
								p.$w.find('[name=manual_conc_monto]').val("");
								p.calcuateDetGasto();
							});
						}else{

						}
						/*p.$w.on('click','[name=btnDeleteConcManual]',function(){
							$(this).closest('tr').remove();
						});*/
						var $tfoot = $('<tfoot />');
						var $row_pago = $('<tr />');
						$row_pago.append('<td style="text-align:right;" colspan="3">Total Pago</td>');
						$row_pago.append('<td style="width:120px;"><input type="text" name="conc_total_pago" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_pago);

						var $row_desc = $('<tr />');
						$row_desc.append('<td style="text-align:right;" colspan="3">Total Descuento</td>');
						$row_desc.append('<td style="width:120px;"><input type="text" name="conc_total_desc" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_desc);

						var $row_neto = $('<tr />');
						$row_neto.append('<td style="text-align:right;" colspan="3">Total Neto</td>');
						$row_neto.append('<td style="width:120px;"><input type="text" name="conc_total_neto" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_neto);
						p.$w.find('[name=grid_det2] table').append($tfoot);
					}
				});
				/** Detalle del Gasto */
				if(p.cuentas!=null){
					var total_pag = 0;
					var total = 0;
					for(i=0;i<p.cuentas.length;i++){
						var $row = $('<tr class="item" id="'+p.cuentas[i]._id.$id+'" />');
						$row.append('<td>'+(i+1)+'</td>');
						$row.append('<td>'+p.cuentas[i].motivo+'</td>');
						p.$w.find("[name=grid_det] tbody").append($row);
						console.log(p.cuentas[i]);
						if(p.cuentas[i].afectacion){
							p.$w.find('[name=grid_det] #'+p.cuentas[i]._id.$id).append('<td>'+p.cuentas[i].afectacion[0].organizacion.actividad.cod+" / "+p.cuentas[i].afectacion[0].organizacion.componente.cod+'</td>');
							p.$w.find('[name=grid_det] #'+p.cuentas[i]._id.$id).append('<td>'+K.round(p.cuentas[i].afectacion[0].monto,2)+'</td>').css({"text-align":"right"});
							if(p.cuentas[i].afectacion.length>1){
								for(j=1;j<p.cuentas[i].afectacion.length;j++){
									var $row2 = $('<tr class="item" />');
									$row2.append('<td></td>');
									$row2.append('<td></td>');
									$row2.append('<td>'+p.cuentas[i].afectacion[j].organizacion.actividad.cod+" / "+p.cuentas[i].afectacion[j].organizacion.componente.cod+'</td>');
									$row2.append('<td>'+K.round(p.cuentas[i].afectacion[j].monto,2)+'</td>').css({"text-align":"right"});
									p.$w.find("[name=grid_det] tbody").append($row2);
								}
							}
							total_pag = parseFloat(p.cuentas[i].total_pago) + total_pag;
						}else{
							p.$w.find('[name=grid_det] #'+p.cuentas[i]._id.$id+'').remove();
						}
						total = parseFloat(p.cuentas[i].total) + total;
					}
				}

				if(p.cuentas!=null){
					var total_pag = 0;
					var total_des = 0;
					for(i=0;i<p.cuentas.length;i++){
						var $row = $('<tr class="item" />');
						$row.append('<td>'+(i+1)+'</td>');
						$row.append('<td>'+p.cuentas[i].motivo+'</td>');
						$row.append('<td>'+K.round(p.cuentas[i].total_pago,2)+'</td>').css({'text-align':'right'});
						$row.append('<td>'+K.round(p.cuentas[i].total_desc,2)+'</td>').css({'text-align':'right'});
						p.$w.find("[name=grid_det2] tbody").append( $row );
						if(p.cuentas[i].conceptos.length>0){
							for(j=0;j<p.cuentas[i].conceptos.length;j++){
								var $row2 = $('<tr class="item" />');
								$row2.append('<td></td>');
								if(p.cuentas[i].conceptos[j].concepto){
									$row2.append('<td>'+p.cuentas[i].conceptos[j].concepto.nomb+'</td>');
								}else{
									$row2.append('<td>'+p.cuentas[i].conceptos[j].observ+'</td>');
								}
								if(p.cuentas[i].conceptos[j].tipo=="P"){
									$row2.append('<td>'+K.round(p.cuentas[i].conceptos[j].monto,2)+'</td>').css({'text-align':'right'});
									$row2.append('<td>'+K.round(0,2)+'</td>').css({'text-align':'right'});
								}else if(p.cuentas[i].conceptos[j].tipo=="D"){
									$row2.append('<td>'+K.round(0,2)+'</td>').css({'text-align':'right'});
									$row2.append('<td>'+K.round(p.cuentas[i].conceptos[j].monto,2)+'</td>').css({'text-align':'right'});
								}
								p.$w.find("[name=grid_det2] tbody").append($row2);
							}
						}
						total_pag = parseFloat(p.cuentas[i].total_pago) + total_pag;
						total_des = parseFloat(p.cuentas[i].total_desc) + total_des;
					}
					p.$w.find('[name=conc_total_pago]').val(K.round(total_pag,2));
					p.$w.find('[name=conc_total_desc]').val(K.round(total_des,2));
					p.$w.find('[name=conc_total_neto]').val(K.round(total_pag-total_des,2));
				}
				/** /Estadistica objeto del gasto */
				new K.grid({
					$el: p.$w.find('[name=grid_est]'),
					search: false,
					pagination: false,
					cols: ['Clasificador','Importe',''],
					onlyHtml: true,
					toolbarHTML: '<div class="form-group col-sm-6"><select name="clasi_est" class="form-control" style="width:100%"></select></div>'+
						'<div class="form-group col-sm-6"><input type="number" name="cant_est" class="form-control"></div>'+
						'<div class="col-sm-6"><button class="btn btn-info"><i class="fa fa-plus"></i> Agregar</button></div>',
					onContentLoaded: function($el){
						$el.closest('.datagrid-header-left').removeClass('col-md-8');
						$el.find('[name=clasi_est]').select2({
							ajax: {
								url: "pr/clas/all",
								dataType: 'json',
								delay: 800,
								data: function (params) {
									return {
										text: params.term,
										autocomplete:'1'
									};
								},
								processResults: function (data, params) {
									var results = [];
									if(data!=null){
										$.each(data,function(i,item){
											results.push({
												id:item._id.$id,
												text:item.cod+' - '+item.nomb,
												data:item
											});
										})	
									}
									return {
										results: results,
									};
								},
								cache: true
							},
							escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
							minimumInputLength: 3,
							templateResult: function(item){
								return '<span>'+item.text+'</span>';
							},
							templateSelection: function(item){
								return '<span>'+item.text+'</span>';
							}
						});
						$el.find('button').click(function(){
							var cuenta = $el.find('[name=clasi_est]').data('select2'),
							cant = $el.find('[name=cant_est]').val();
							if(cuenta==null){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Hubo un error al reconocer el clasificador!',
									type: 'error'
								});
								return $el.find('[name=clasi_est]').focus();
							}else{
								cuenta = cuenta.data();
								if(cuenta==null||cuenta[0]==null){
									K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Debe seleccionar un clasificador para continuar!',
										type: 'error'
									});
									return $el.find('[name=clasi_est]').focus();
								}
							}
							cuenta = cuenta[0].data;
							if(cant==''){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cantidad!',
									type: 'error'
								});
								return $el.find('[name=cant_pat]').focus();
							}
							var $row = $('<tr class="item">');
							$row.append('<td>'+cuenta.cod+' - '+cuenta.nomb+'</td>');
							$row.append('<td>'+ciHelper.formatMon(cant)+'</td>');
							$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								p.refresh();
							});
							$row.data('data',{
								clasificador: prClas.dbRel(cuenta),
								monto: cant
							});
							p.$w.find('[name=grid_est] tbody').append($row);
							$el.find('[name=clasi_est]').select2('val','');
							$el.find('[name=cant_est]').val('');
							$el.find('[name=clasi_est]').closest('.form-group').removeClass('has-success')
						    		.addClass('has-error');
						    p.refresh();
						});
					}
				});
				/** Estadistica Objeto del Gasto (Existan cuentas)*/
				if(p.cuentas!=null){
					for(i=0;i<p.cuentas.length;i++){
						if(p.cuentas[i].afectacion){
							for(j=0;j<p.cuentas[i].afectacion.length;j++){
								if(p.cuentas[i].afectacion[j].gasto){
									for(k=0;k<p.cuentas[i].afectacion[j].gasto.length;k++){
										var clasi = p.$w.find('#grid_est [name='+p.cuentas[i].afectacion[j].gasto[k].clasificador._id.$id+']');
										if(clasi.length>0){
											var monto = clasi.find('td').eq(1).html();
											clasi.find('td').eq(1).html(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].afectacion[j].gasto[k].monto),2));
										}else{
											var $row = $('<tr class="item" name="'+p.cuentas[i].afectacion[j].gasto[k].clasificador._id.$id+'">');
											$row.append('<td>'+p.cuentas[i].afectacion[j].gasto[k].clasificador.cod+'</td>');
											$row.append('<td>'+K.round(p.cuentas[i].afectacion[j].gasto[k].monto,2)+'</td>');
											$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('button').click(function(){
												$(this).closest('.item').remove();
												p.refresh();
											});
											$row.data('data',{
												clasificador: prClas.dbRel(p.cuentas[i].afectacion[j].gasto[k].clasificador),
												monto: p.cuentas[i].afectacion[j].gasto[k].monto
											});
											p.$w.find('[name=grid_est] tbody').append($row);
										}
									}
								}
							}
						}
					}
				}
				/** /Codificacion Programatica */

				new K.grid({
					$el: p.$w.find('[name=grid_cod]'),
					search: false,
					pagination: false,
					cols: ['Organizaci&oacute;n','',''],
					onlyHtml: true,
					toolbarHTML: '<button class="btn btn-info"><i class="fa fa-plus"></i> Asignar</button>',
					onContentLoaded: function($el){
						$el.find('button').click(function(){
							mgOrga.windowSelect({
								params:{
									actividad:true,
									componente:true
								},
								callback: function(data){
									console.log(data);
									if(data.actividad!=null &&data.componente !=null){
										var $row = $('<tr class="item">');
										$row.append('<td>'+data.nomb+'</td>');
										$row.append('<td><select class="form-control"><option value="51a6316b4d4a132807000002">RECURSOS DIRECTAMENTE RECAUDADOS</option><option value="51a631924d4a132807000003">DONACIONES Y TRANSFERENCIAS</option></select></td>');
										$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('button').click(function(){
											$(this).closest('.item').remove();
											p.refresh();
										});
										$row.data('data',data);
										p.push_cod_prog(data.actividad._id.$id,data.componente._id.$id);
										p.$w.find('[name=grid_cod] tbody').append($row);
										p.refresh();
									}else return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'Dicho codigo programatico no contiene una actividad o un componente, comunicarse con la oficina de Presupuesto!',
										type: 'error'
									});
								}
							});
						});
					}
				});

				/** /Contabilidad Presupuestal */
				new K.grid({
					$el: p.$w.find('[name=grid_cont_pres]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber',''],
					onlyHtml: true,
					toolbarHTML: '<div class="form-group col-sm-6"><input type="text" placeholder="buscar cuenta..." name="cuenta_pre" class="form-control"></div>'+
						'<div class="form-group col-sm-6"><input type="number" name="cant_pre" class="form-control"></div>'+
						'<div class="form-group col-sm-6"><select name="tipo_pre" class="form-control"><option value="D">Debe</option><option value="H">Haber</option></select></div>'+
						'<div class="col-sm-6"><button class="btn btn-info"><i class="fa fa-plus"></i> Agregar</button></div>',
					onContentLoaded: function($el){
						$el.closest('.datagrid-header-left').removeClass('col-md-8');
						$el.find('button').click(function(){
							var cuenta = $el.find('[name=cuenta_pre]').data('data'),
							tipo = $el.find('[name=tipo_pre] option:selected').val(),
							cant = $el.find('[name=cant_pre]').val();
							if(cuenta==null){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cuenta!',
									type: 'error'
								});
								return $el.find('[name=cuenta_pre]').focus();
							}
							if(cant==''){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cantidad!',
									type: 'error'
								});
								return $el.find('[name=cant_pre]').focus();
							}
							var $row = $('<tr class="item">');
							$row.append('<td>'+cuenta.cod+'</td>');
							if(tipo=='D'){
								$row.append('<td>'+ciHelper.formatMon(cant)+'</td>');
								$row.append('<td>');
							}else{
								$row.append('<td>');
								$row.append('<td>'+ciHelper.formatMon(cant)+'</td>');
							}
							$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								p.refresh();
							});
							$row.data('data',{
								cuenta: ctPcon.dbRel(cuenta),
								tipo: tipo,
								monto: cant
							});
							p.$w.find('[name=grid_cont_pres] tbody').append($row);
							$el.find('[name=cuenta_pre]').val('').removeData('data');
							$el.find('[name=cant_pre]').val('');
							$el.find('[name=cuenta_pre]').closest('.form-group').removeClass('has-success')
						    		.addClass('has-error');
						    p.refresh();
						});
					}
				});

				/** /Contabilidad Patrimonial */
				new K.grid({
					$el: p.$w.find('[name=grid_cont_patr]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber',''],
					onlyHtml: true,
					toolbarHTML: '<div class="form-group col-sm-6"><input type="text" placeholder="buscar cuenta..." name="cuenta_pat" class="form-control"></div>'+
						'<div class="form-group col-sm-6"><input type="number" name="cant_pat" class="form-control"></div>'+
						'<div class="form-group col-sm-6"><select name="tipo_pat" class="form-control"><option value="D">Debe</option><option value="H">Haber</option></select></div>'+
						'<div class="col-sm-6"><button class="btn btn-info"><i class="fa fa-plus"></i> Agregar</button></div>',
					onContentLoaded: function($el){
						$el.closest('.datagrid-header-left').removeClass('col-md-8');
						p.$w.find('[name=grid_cont_patr] fuelux').height(parseFloat(p.$w.find('[name=grid_cont_patr] fuelux').height())+240+'px');
						$el.find('button').click(function(){
							var cuenta = $el.find('[name=cuenta_pat]').data('data'),
							tipo = $el.find('[name=tipo_pat] option:selected').val(),
							cant = $el.find('[name=cant_pat]').val();
							if(cuenta==null){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cuenta!',
									type: 'error'
								});
								return $el.find('[name=cuenta_pat]').focus();
							}
							if(cant==''){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cantidad!',
									type: 'error'
								});
								return $el.find('[name=cant_pat]').focus();
							}
							var $row = $('<tr class="item">');
							$row.append('<td>'+cuenta.cod+'</td>');
							if(tipo=='D'){
								$row.append('<td>'+ciHelper.formatMon(cant)+'</td>');
								$row.append('<td>');
							}else{
								$row.append('<td>');
								$row.append('<td>'+ciHelper.formatMon(cant)+'</td>');
							}
							$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								p.refresh();
							});
							$row.data('data',{
								cuenta: ctPcon.dbRel(cuenta),
								tipo: tipo,
								monto: cant
							});
							p.$w.find('[name=grid_cont_patr] tbody').append($row);
							$el.find('[name=cuenta_pat]').val('').removeData('data');
							$el.find('[name=cant_pat]').val('');
							$el.find('[name=cuenta_pat]').closest('.form-group').removeClass('has-success')
						    		.addClass('has-error');
						    p.refresh();
						});
					}
				});
				/******************************************************************
				 * RETENCIONES Retenciones y/o Deducciones
				******************************************************************/
				new K.grid({
					$el: p.$w.find('[name=grid_rete_dedu]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Monto',''],
					onlyHtml: true,
					toolbarHTML: '<div class="form-group col-sm-6"><input type="text" placeholder="buscar cuenta..." name="cuenta_ret" class="form-control"></div>'+
						'<div class="form-group col-sm-6"><input type="number" name="cant_ret" class="form-control"></div>'+
						'<div class="col-sm-6"><button class="btn btn-info"><i class="fa fa-plus"></i> Agregar</button></div>',
					onContentLoaded: function($el){
						$el.closest('.datagrid-header-left').removeClass('col-md-8');
						$el.find('button').click(function(){
							var cuenta = $el.find('[name=cuenta_ret]').data('data'),
							cant = $el.find('[name=cant_ret]').val();
							if(cuenta==null){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cuenta!',
									type: 'error'
								});
								return $el.find('[name=cuenta_ret]').focus();
							}
							if(cant==''){
								K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe escoger una cantidad!',
									type: 'error'
								});
								return $el.find('[name=cant_ret]').focus();
							}
							var $row = $('<tr class="item">');
							$row.append('<td>'+cuenta.cod+'</td>');
							$row.append('<td>'+ciHelper.formatMon(cant)+'</td>');
							$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('button').click(function(){
								$(this).closest('.item').remove();
								p.refresh();
							});
							$row.data('data',{
								cuenta: ctPcon.dbRel(cuenta),
								monto: cant
							});
							p.$w.find('[name=grid_rete_dedu] tbody').append($row);
							$el.find('[name=cuenta_ret]').val('').removeData('data');
							$el.find('[name=cant_ret]').val('');
							$el.find('[name=cuenta_ret]').closest('.form-group').removeClass('has-success')
						    		.addClass('has-error');
						    p.refresh();
						});
					}
				});
				/** Retenciones y/o deducciones (no manuales)*/
				if(p.cuentas!=null){
					//console.log("==========start retenciones============");
					for(i=0;i<p.cuentas.length;i++){
						if(p.cuentas[i].conceptos){
							for(j=0;j<p.cuentas[i].conceptos.length;j++){
								if(p.cuentas[i].conceptos[j].concepto&&p.cuentas[i].conceptos[j].concepto.cuenta){
									if(p.cuentas[i].conceptos[j].tipo=="D"){
										//console.log(p.cuentas[i].conceptos[j]);
										var reten = p.$w.find('[name=grid_rete_dedu] #'+p.cuentas[i].conceptos[j].concepto.cuenta._id.$id+'');
										if(reten.length>0){
											var monto = reten.data('monto');
											reten.find('td').eq(1).html(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].conceptos[j].monto),2));
											var _data = reten.data('data');
											_data.monto = parseFloat(monto)+parseFloat(p.cuentas[i].conceptos[j].monto);
											reten.data('data',_data);
										}else{
											var $row = $('<tr class="item" id="'+p.cuentas[i].conceptos[j].concepto.cuenta._id.$id+'">');
											$row.append('<td>'+p.cuentas[i].conceptos[j].concepto.cuenta.cod+'</td>');
											$row.append('<td>'+K.round(p.cuentas[i].conceptos[j].monto,2)+'</td>');
											$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('button').click(function(){
												$(this).closest('.item').remove();
												p.refresh();
											});
											$row.data('data',{
												cuenta: ctPcon.dbRel(p.cuentas[i].conceptos[j].concepto.cuenta),
												monto: p.cuentas[i].conceptos[j].monto
											});
											p.$w.find('[name=grid_rete_dedu] tbody').append($row);
										}		
										//console.log(p.cuentas[i].conceptos[j].monto);
									}
								}								
							}
						}
					}
					//console.log("==========end retenciones============");
				}
				/***********************************************
				* BENEFICIARIOS MEDIO DE PAGO
				***********************************************/
				new K.grid({
					$el: p.$w.find('[name=grid_form_pago]'),
					search: false,
					pagination: false,
					cols: ['Nro.','Beneficiario','Sub-total','Cheque'],
					onlyHtml: true,
					toolbarHTML: '<div class="form-group col-sm-6"><span name="add_bene"></span> <button name="btnSelectBene" class="btn btn-success">Seleccionar Beneficiario</button></div>'+
						'<div class="form-group col-sm-6"><input type="text" name="add_monto" class="form-control" placeholder="Monto"></div>'+
						'<div class="col-sm-6"><button name="btnAddBene" class="btn btn-success">Agregar Beneficiario</button></div>',
					onContentLoaded: function($el){
						var $tfoot = $('<tfoot />');
						var $row_pago = $('<tr />');
						$row_pago.append('<td style="text-align:right;" colspan="2">Total</td>');
						$row_pago.append('<td style="width:120px;"><input type="text" name="form_pago_total" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_pago);
						p.$w.find('[name=grid_form_pago] table').append($tfoot);


						$el.closest('.datagrid-header-left').removeClass('col-md-8');
						$el.find('[name=btnSelectBene]').click(function(){
							mgEnti.windowSelect({bootstrap:true, callback: function(data){
								p.$w.find('[name=add_bene]').html(ciHelper.enti.formatName(data)).data('data',data);
							}});
						}).button({icons: {primary: 'ui-icon-search'}});
						/** Add Beneficiarios */
						$el.find('[name=btnAddBene]').click(function(){
							if($el.find('[name=add_bene]').data('data')==null){
								K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un beneficiario!',type: 'error'});
								return $el.find('[name=btnSelectBene]').click();
							}
							if($el.find('[name=add_monto]').val()==""){
								K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un monto!',type: 'error'});
								return $el.find('[name=add_monto]').focus();
							}
							var $row = $('<tr class="item" />');
							$row.append('<td>'+(p.$w.find('[name=grid_form_pago] tbody tr').length+1)+'</td>');
							$row.append('<td>'+ciHelper.enti.formatName($el.find('[name=add_bene]').data('data'))+'</td>');
							$row.append('<td style="width:120px;"><input type="text" name="form_pago_subtotal" value="'+K.round($el.find('[name=add_monto]').val(),2)+'" class="form-control"></td>').css({"text-align":"right"});
							$row.append('<td style="width:150px;"><input type="text" name="cheque" class="form-control"></td>');

							if(p.$w.find('[name=forma_pago] :selected').val()=='T'){
								$row.find('[name=cheque]').attr('disabled','disabled');
							}

							$row.data('data',$el.find('[name=add_bene]').data('data')).data('monto',parseFloat($el.find('[name=add_monto]').val()));
							p.$w.find("[name=grid_form_pago] tbody").append( $row );

							if (p.manual) {
								var tipo = p.$w.find('[name=tipo_referencia] :selected').val();
								switch(tipo){
									case "LOCACION":
										/*AGREGAR EL BENEFICIARIO A DESCRIPCION SEG?N TESORER?A*/
											var $row = $('<tr class="item" />');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_rxh"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_nombres"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_oficio"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_ruc"></td>');
											$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');

											$row.find('[name=table_ref_item_nombres]').val(ciHelper.enti.formatName($el.find('[name=add_bene]').data('data')));
											$row.find('[name=table_ref_item_monto]').val(parseFloat($el.find('[name=add_monto]').val()));

											$row.find('[name=btnRefRemoveRow]').click(function(){
												$(this).closest('tr').remove();
											});
											//PRIMERA VEZ NO TIENE MONTOS
											var $inputs_monto = $row.find('[name=table_ref_item_monto]');
											var total = 0;
											for(var i=0;i<$inputs_monto.length;i++){
												//console.log($el.find('[name=add_bene]').data('data'));
												//console.log(ciHelper.enti.formatName($el.find('[name=add_bene]').data('data')));
												if(parseFloat($inputs_monto.eq(i).val())){
													total+=parseFloat($inputs_monto.eq(i).val());
												}
											}
											p.$w.find('[name=table_ref_total]').val(K.round(total,2));
											//SI CAMBIAS MONTO IGUAL CALCULA EL TOTAL
											$row.find('[name=table_ref_item_monto]').keyup(function(){
												var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
												var total = 0;
												for(var i=0;i<$inputs_monto.length;i++){
													if(parseFloat($inputs_monto.eq(i).val())){
														total+=parseFloat($inputs_monto.eq(i).val());
													}
												}
												p.$w.find('[name=table_ref_total]').val(K.round(total,2));
											});
											p.$w.find('#referencia_format').find('tbody').append($row);
											//p.calcuateDetGasto();
											

										/*LUEGO SE CREA UNA CUENTA POR PAGAR*/
											var $row2 = $('<tr class="item" />');
											$row2.append('<td><button name="btnDeleteConcManual" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></td>');
											$row2.append('<td><span name="manual_conc_i_observ">'+'CHEQUE DE '+ciHelper.enti.formatName($el.find('[name=add_bene]').data('data'))+'</span></td>');

											$row2.append('<td>'+K.round(parseFloat($el.find('[name=add_monto]').val()),2)+'</td>').css({'text-align':'right'});
											$row2.append('<td>0.00</td>').css({'text-align':'right'});
											
											$row2.find('[name=btnDeleteConcManual]').click(function(){
												$(this).closest('tr').remove();
												p.calcuateDetGasto();
											});
											p.$w.find("[name=grid_det2] tbody").append($row2);

											p.$w.find('[name=manual_conc_observ]').val("");
											p.$w.find('[name=manual_conc_monto]').val("");
											p.calcuateDetGasto();
											break
									case "COMPRA":
										/*AGREGAR EL BENEFICIARIO A DESCRIPCION SEG?N TESORER?A SERA UNICO*/
											var $row = $('<tr class="item" />');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_factura"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_requerimiento"></td>');
											$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');

											$row.find('[name=table_ref_item_monto]').val(parseFloat($el.find('[name=add_monto]').val()));
											p.$w.find('[name=nomb]').val(ciHelper.enti.formatName($el.find('[name=add_bene]').data('data')));

											$row.find('[name=btnRefRemoveRow]').click(function(){
												$(this).closest('tr').remove();
											});

											//PRIMERA VEZ NO TIENE MONTOS
											var $inputs_monto = $row.find('[name=table_ref_item_monto]');
											var total = 0;
											for(var i=0;i<$inputs_monto.length;i++){
												if(parseFloat($inputs_monto.eq(i).val())){
													total+=parseFloat($inputs_monto.eq(i).val());
												}
											}
											p.$w.find('[name=table_ref_total]').val(K.round(total,2));

											//SI CAMBIAS MONTO IGUAL CALCULA EL TOTAL
											$row.find('[name=table_ref_item_monto]').keyup(function(){
												var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
												var total = 0;
												for(var i=0;i<$inputs_monto.length;i++){
													if(parseFloat($inputs_monto.eq(i).val())){
														total+=parseFloat($inputs_monto.eq(i).val());
													}
												}
												p.$w.find('[name=table_ref_total]').val(K.round(total,2));
											});
											p.$w.find('#referencia_format').find('tbody').append($row);
											

										/*LUEGO SE CREA UNA CUENTA POR PAGAR*/
											var $row2 = $('<tr class="item" />');
											$row2.append('<td><button name="btnDeleteConcManual" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></td>');
											$row2.append('<td><span name="manual_conc_i_observ">'+'CHEQUE DE '+ciHelper.enti.formatName($el.find('[name=add_bene]').data('data'))+'</span></td>');

											$row2.append('<td>'+K.round(parseFloat($el.find('[name=add_monto]').val()),2)+'</td>').css({'text-align':'right'});
											$row2.append('<td>0.00</td>').css({'text-align':'right'});
											
											$row2.find('[name=btnDeleteConcManual]').click(function(){
												$(this).closest('tr').remove();
												p.calcuateDetGasto();
											});
											p.$w.find("[name=grid_det2] tbody").append($row2);

											p.$w.find('[name=manual_conc_observ]').val("");
											p.$w.find('[name=manual_conc_monto]').val("");
											p.calcuateDetGasto();
											break
									case "SERVICIO":
										/*AGREGAR EL BENEFICIARIO A DESCRIPCION SEG?N TESORER?A SERA UNICO*/
											var $row = $('<tr class="item" />');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
											$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_requerimiento"></td>');
											$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');

											$row.find('[name=table_ref_item_monto]').val(parseFloat($el.find('[name=add_monto]').val()));
											p.$w.find('[name=nomb]').val(ciHelper.enti.formatName($el.find('[name=add_bene]').data('data')));

											$row.find('[name=btnRefRemoveRow]').click(function(){
												$(this).closest('tr').remove();
											});

											//PRIMERA VEZ NO TIENE MONTOS
											var $inputs_monto = $row.find('[name=table_ref_item_monto]');
											var total = 0;
											for(var i=0;i<$inputs_monto.length;i++){
												if(parseFloat($inputs_monto.eq(i).val())){
													total+=parseFloat($inputs_monto.eq(i).val());
												}
											}
											p.$w.find('[name=table_ref_total]').val(K.round(total,2));

											//SI CAMBIAS MONTO IGUAL CALCULA EL TOTAL
											$row.find('[name=table_ref_item_monto]').keyup(function(){
												var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
												var total = 0;
												for(var i=0;i<$inputs_monto.length;i++){
													if(parseFloat($inputs_monto.eq(i).val())){
														total+=parseFloat($inputs_monto.eq(i).val());
													}
												}
												p.$w.find('[name=table_ref_total]').val(K.round(total,2));
											});
											p.$w.find('#referencia_format').find('tbody').append($row);
											

										/*LUEGO SE CREA UNA CUENTA POR PAGAR*/
											var $row2 = $('<tr class="item" />');
											$row2.append('<td><button name="btnDeleteConcManual" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></td>');
											$row2.append('<td><span name="manual_conc_i_observ">'+'CHEQUE DE '+ciHelper.enti.formatName($el.find('[name=add_bene]').data('data'))+'</span></td>');

											$row2.append('<td>'+K.round(parseFloat($el.find('[name=add_monto]').val()),2)+'</td>').css({'text-align':'right'});
											$row2.append('<td>0.00</td>').css({'text-align':'right'});
											
											$row2.find('[name=btnDeleteConcManual]').click(function(){
												$(this).closest('tr').remove();
												p.calcuateDetGasto();
											});
											p.$w.find("[name=grid_det2] tbody").append($row2);

											p.$w.find('[name=manual_conc_observ]').val("");
											p.$w.find('[name=manual_conc_monto]').val("");
											p.calcuateDetGasto();
											break
								}
							}else{
								//No hacer nada
							}
							

							//p.sumBene();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
						/** ./Add Beneficiarios */

					}
				});
				if(p.cuentas!=null){
					var total_bene_pag = 0;
					for(i=0;i<p.cuentas.length;i++){
						/* Cheques */
						if(p.cuentas[i].beneficiario){
							var bene = p.$w.find('[name=grid_form_pago] #'+p.cuentas[i].beneficiario._id.$id+'');
							if(bene.length>0){
								var monto = bene.data('monto');
								bene.find('[name=form_pago_subtotal]').val(K.round(parseFloat(monto)+parseFloat(p.cuentas[i].total),2)).css({"text-align":"right"});
								bene.data('monto',parseFloat(monto)+parseFloat(p.cuentas[i].total));
							}else{
								var $row = $('<tr class="item" />');
								$row.append('<td>'+(p.$w.find('[name=grid_form_pago] tbody tr').length+1)+'</td>');
								$row.append('<td>'+ciHelper.enti.formatName(p.cuentas[i].beneficiario)+'</td>');
								$row.append('<td style="width:120px;"><input type="text" name="form_pago_subtotal" value="'+K.round(p.cuentas[i].total,2)+'" class="form-control"></td>').css({"text-align":"right"});
								$row.append('<td style="width:150px;"><input type="text" name="cheque" class="form-control"></td>');
								$row.data('data',p.cuentas[i].beneficiario).data('monto',p.cuentas[i].total);
								p.$w.find("[name=grid_form_pago] tbody").append( $row );
							}
							total_bene_pag = parseFloat(p.cuentas[i].total) + total_bene_pag;
						}
					}
					p.$w.find('[name=form_pago_total]').val(total_bene_pag);
				}
				p.$w.find('[name=nomb],[name=fec]')
					.blur(function(){ p.refresh(); });
				p.$w.find('[name=forma_pago]').change(function(){
					if(p.$w.find('[name=forma_pago] :selected').val()=='T'){
						p.$w.find('[name=cheque]').attr('disabled','disabled');
					}else{
						p.$w.find('[name=cheque]').removeAttr('disabled');
					}
				});
				$.post('ct/pcon/all',function(cuentas){
					for(var i=0; i<cuentas.length; i++){
						p.cuenta.push();
					}
					p.cuenta = $.each(cuentas,function(i,item){
						item.id = item._id.$id;
						item.busqueda = item.cod+' '+item.descr;
					});
					p.$w.find('[name=cuenta_pre]').typeaheadmap({ 
						source: p.cuenta,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.cuenta,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							p.$w.find('[name=cuenta_pre]').data('data',cuenta);
						    if(cuenta!=null){
						    	p.$w.find('[name=cuenta_pre]').closest('.form-group').removeClass('has-error')
						    		.addClass('has-success');
						    }
						}
					});
					p.$w.find('[name=cuenta_pat]').typeaheadmap({ 
						source: p.cuenta,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.cuenta,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							p.$w.find('[name=cuenta_pat]').data('data',cuenta);
						    if(cuenta!=null){
						    	p.$w.find('[name=cuenta_pat]').closest('.form-group').removeClass('has-error')
						    		.addClass('has-success');
						    }
						}
					});
					p.$w.find('[name=cuenta_ret]').typeaheadmap({ 
						source: p.cuenta,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.cuenta,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							p.$w.find('[name=cuenta_ret]').data('data',cuenta);
						    if(cuenta!=null){
						    	p.$w.find('[name=cuenta_ret]').closest('.form-group').removeClass('has-error')
						    		.addClass('has-success');
						    }
						}
					});

					/** Cuenta Bancaria */			
					$.post('ts/ctban/all',function(ctban){
						var $cbo = p.$w.find('[name=pag_cuenta]');
						if(ctban!=null){
							for(var i=0; i<ctban.length; i++){		
								var id = ctban[i]._id.$id;
								var cod_banco = ctban[i].cod_banco;
								var nomb = ctban[i].nomb;
								var cod = ctban[i].cod;
								var moneda = ctban[i].moneda;
								$cbo.append('<option value="'+id+'" >'+cod+' - '+ctban[i].nomb+'</option>');
								$cbo.find('option:last').data('data',ctban[i]);
							}
						}
						$.post('pr/fuen/all',function(fuente){
							var $cbo = p.$w.find('[name=cod_prog_fuente]');
							if(fuente!=null){
								for(var i=0; i<fuente.length; i++){
									var rubro = fuente[i].rubro;
									var cod = fuente[i].cod;
									var id = fuente[i]._id.$id;
									$cbo.append('<option value="'+id+'" >'+rubro+'</option>');
									$cbo.find('option:last').data('data',fuente[i]);
								}
							}
							if(p.id!=null){
								$.post('ts/comp/get','_id='+p.id,function(data){
									p.$w.find('[name=cod]').html(ciHelper.codigos(data.cod,6));
									p.$w.find('[name=nomb]').val(data.nomb);
									p.$w.find('[name=forma_pago]').val(data.tipo_pago);
									p.$w.find('[name=cod_prog_fuente]').val(data.fuente._id.$id);
									p.$w.find('[name=pag_cuenta]').val(data.cuenta_banco._id.$id);
									/* Codificacion Programatica */
									p.$w.find('[name=cod_prog_sector]').val(data.cod_programatica.sector);
									p.$w.find('[name=cod_prog_pliego]').val(data.cod_programatica.pliego);
									p.$w.find('[name=cod_prog_programa]').val(data.cod_programatica.programa);
									p.$w.find('[name=cod_prog_subprograma]').val(data.cod_programatica.subprograma);
									p.$w.find('[name=cod_prog_proyecto]').val(data.cod_programatica.proyecto);
									p.$w.find('[name=cod_prog_obra]').val(data.cod_programatica.obra);
									if(data.referencia!=null){
										p.$w.find('[name=tipo_referencia]').val(data.referencia.tipo).change();
										p.refereceFillData(data.referencia.tipo,data.referencia);
									}
									/* ./Codificacion Programatica */
									if(data.items!=null){
										var total_pag = 0;
										var total = 0;
										for(i=0;i<data.items.length;i++){
											var $row = $('<tr class="item" id="'+data.items[i]._id.$id+'" />');
											$row.append('<td>'+(i+1)+'</td>');
											$row.append('<td>'+data.items[i].motivo+'</td>');
											p.$w.find("[name=grid_det] tbody").append($row);
											if(data.items[i].afectacion){
												p.$w.find('[name=grid_det] #'+data.items[i]._id.$id).append('<td>'+data.items[i].afectacion[0].organizacion.actividad.cod+" / "+data.items[i].afectacion[0].organizacion.componente.cod+'</td>');
												p.$w.find('[name=grid_det] #'+data.items[i]._id.$id).append('<td>'+K.round(data.items[i].afectacion[0].monto,2)+'</td>').css({"text-align":"right"});
												if(data.items[i].afectacion.length>1){
													for(j=1;j<data.items[i].afectacion.length;j++){
														var $row2 = $('<tr class="item" />');
														$row2.append('<td></td>');
														$row2.append('<td></td>');
														$row2.append('<td>'+data.items[i].afectacion[j].organizacion.actividad.cod+" / "+data.items[i].afectacion[j].organizacion.componente.cod+'</td>');
														$row2.append('<td>'+K.round(data.items[i].afectacion[j].monto,2)+'</td>').css({"text-align":"right"});
														p.$w.find("[name=grid_det] tbody").append($row2);
													}
												}
												total_pag = parseFloat(data.items[i].total_pago) + total_pag;
											}else{
												p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+'').remove();
											}
											total = parseFloat(data.items[i].total) + total;
										}
									}
									/** /Detalle del Gasto */

									if(data.items!=null){
										var total_pag = 0;
										var total_des = 0;
										for(i=0;i<data.items.length;i++){
											var $row = $('<tr class="item" />');
											$row.append('<td>'+(i+1)+'</td>');
											$row.append('<td>'+data.items[i].motivo+'</td>');
											$row.append('<td>'+K.round(data.items[i].total_pago,2)+'</td>').css({'text-align':'right'});
											$row.append('<td>'+K.round(data.items[i].total_desc,2)+'</td>').css({'text-align':'right'});
											p.$w.find("[name=grid_det2] tbody").append( $row );
											if(data.items[i].conceptos.length>0){
												for(j=0;j<data.items[i].conceptos.length;j++){
													var $row2 = $('<tr class="item" />');
													$row2.append('<td></td>');
													if(data.items[i].conceptos[j].concepto){
														$row2.append('<td>'+data.items[i].conceptos[j].concepto.nomb+'</td>');
													}else{
														$row2.append('<td>'+data.items[i].conceptos[j].observ+'</td>');
													}
													if(data.items[i].conceptos[j].tipo=="P"){
														$row2.append('<td>'+K.round(data.items[i].conceptos[j].monto,2)+'</td>').css({'text-align':'right'});
														$row2.append('<td>'+K.round(0,2)+'</td>').css({'text-align':'right'});
													}else if(data.items[i].conceptos[j].tipo=="D"){
														$row2.append('<td>'+K.round(0,2)+'</td>').css({'text-align':'right'});
														$row2.append('<td>'+K.round(data.items[i].conceptos[j].monto,2)+'</td>').css({'text-align':'right'});
													}
													p.$w.find("[name=grid_det2] tbody").append($row2);
												}
											}
											total_pag = parseFloat(data.items[i].total_pago) + total_pag;
											total_des = parseFloat(data.items[i].total_desc) + total_des;
										}
										p.$w.find('[name=conc_total_pago]').val(K.round(total_pag,2));
										p.$w.find('[name=conc_total_desc]').val(K.round(total_des,2));
										p.$w.find('[name=conc_total_neto]').val(K.round(total_pag-total_des,2));
									}
									
									/** Estadistica Objeto del Gasto */
									if(data.objeto_gasto!=null){
										for(i=0;i<data.objeto_gasto.length;i++){
											var $row2 = $('<tr class="item" />');
											$row2.append('<td>'+data.objeto_gasto[i].clasificador.cod+' - '+data.objeto_gasto[i].clasificador.nomb+'</td>');
											$row2.append('<td>'+K.round(data.objeto_gasto[i].monto,2)+'</td>');
											p.$w.find("[name=grid_est] tbody").append( $row2 );
										}
									}
									/** /Estadistica Objeto del Gasto */
									if(data.retenciones){
										for(i=0;i<data.retenciones.length;i++){
											var $row = $('<tr class="item" />');
											$row.append('<td>'+data.retenciones[i].cuenta.cod+'</td>');
											$row.append('<td>'+data.retenciones[i].monto+'</td>');
											var data2 = {
													cuenta:{
														_id:data.retenciones[i].cuenta._id.$id,
														cod:data.retenciones[i].cuenta.cod,
														descr:data.retenciones[i].cuenta.descr
													},
													monto:data.retenciones[i].monto,
													moneda:data.retenciones[i].moneda
											};
											$row.data('data',data2 );
											p.$w.find("[name=grid_rete_dedu] tbody").append( $row );
										}
									}
									/** /Retenciones y/o deducciones */
									
									/** Formas de Pago */		
									if(data.beneficiarios!=null){
										var total_pag = 0;
										for(i=0;i<data.beneficiarios.length;i++){
											/* Cheques */
											if(data.beneficiarios[i].beneficiario){
												var $row = $('<tr class="item" id="'+data.beneficiarios[i].beneficiario._id.$id+'" />');
												$row.append('<td>'+(i+1)+'</td>');
												$row.append('<td>'+ciHelper.enti.formatName(data.beneficiarios[i].beneficiario)+'</td>');
												$row.append('<td style="width:120px;"><input type="text" name="form_pago_subtotal" value="'+K.round(data.beneficiarios[i].monto,2)+'" class="form-control"></td>').css({"text-align":"right"});
												$row.append('<td style="width:150px;"><input type="text" name="cheque" value="'+data.beneficiarios[i].cheque+'" class="form-control"></td>');
												$row.data('data',data.beneficiarios[i].beneficiario).data('monto',data.beneficiarios[i].total);
												p.$w.find("[name=grid_form_pago] tbody").append( $row );
												total_pag = parseFloat(data.beneficiarios[i].monto) + total_pag;
											}
										}
									}
									/** /Formas de Pago */
									
									/** Contabilidad Presupuestal */
									if(data.cont_presupuestal){
										for(i=0;i<data.cont_presupuestal.length;i++){
											var $row = $('<tr class="item">');
											$row.append('<td>'+data.cont_presupuestal[i].cuenta.cod+'</td>');
											if(data.cont_presupuestal[i].tipo=='D'){
												$row.append('<td>'+ciHelper.formatMon(data.cont_presupuestal[i].monto)+'</td>');
												$row.append('<td>');
											}else{
												$row.append('<td>');
												$row.append('<td>'+ciHelper.formatMon(data.cont_presupuestal[i].monto)+'</td>');
											}
											$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('button').click(function(){
												$(this).closest('.item').remove();
												p.refresh();
											});
											$row.data('data',{
												cuenta: ctPcon.dbRel(data.cont_presupuestal[i].cuenta),
												tipo: data.cont_presupuestal[i].tipo,
												monto: data.cont_presupuestal[i].monto
											});
											p.$w.find('[name=grid_cont_pres] tbody').append($row);
										}
									}
									/** /Contabilidad Presupuestal */
									
									/** Contabilidad Patrimonial */
									if(data.cont_patrimonial){
										for(i=0;i<data.cont_patrimonial.length;i++){
											var $row = $('<tr class="item">');
											$row.append('<td>'+data.cont_patrimonial[i].cuenta.cod+'</td>');
											if(data.cont_patrimonial[i].tipo=='D'){
												$row.append('<td>'+ciHelper.formatMon(data.cont_patrimonial[i].monto)+'</td>');
												$row.append('<td>');
											}else{
												$row.append('<td>');
												$row.append('<td>'+ciHelper.formatMon(data.cont_patrimonial[i].monto)+'</td>');
											}
											$row.append('<td><button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
											$row.find('button').click(function(){
												$(this).closest('.item').remove();
												p.refresh();
											});
											$row.data('data',{
												cuenta: ctPcon.dbRel(data.cont_patrimonial[i].cuenta),
												tipo: data.cont_patrimonial[i].tipo,
												monto: data.cont_patrimonial[i].monto
											});
											p.$w.find('[name=grid_cont_patr] tbody').append($row);
										}
									}
									K.unblock({$element: p.$w});
								},'json')
							}else{
								K.unblock({$element: p.$w});
							}
							
						},'json');
					},'json');
					p.refresh();
				},'json');
				//K.unblock({$element: p.$w});
			}
		});
	},
	windowDetails: function(p){
		p.refereceFillData = function(tipo,data){
			var $element = p.$w.find('#referencia_format');
			switch(tipo){
				case "TEXTO_PLANO":
					$element.find('[name=format_1_referencia]').val(data.texto_plano);
					break;
				case "COMPRA":
					//data.table_ref = [];
					if(data.table_ref.length>0){
						for(var i=0;i<data.table_ref.length;i++){
							p.$w.find('[name=btnRefAddRow]').click();
							var $row = $element.find('[name=table_ref] tbody tr:last');
							//console.log($row);
							$row.find('[name=table_ref_item_referencia]').val(data.table_ref[i].referencia);
							$row.find('[name=table_ref_item_actividad]').val(data.table_ref[i].actividad);
							$row.find('[name=table_ref_item_factura]').val(data.table_ref[i].factura);
							$row.find('[name=table_ref_item_monto]').val(data.table_ref[i].monto);
							$row.find('[name=table_ref_item_requerimiento]').val(data.table_ref[i].requerimiento);
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "SERVICIO_TELEFONICO":
					//data.table_ref = [];
					if(data.table_ref.length>0){
						for(var i=0;i<data.table_ref.length;i++){
							p.$w.find('[name=btnRefAddRow]').click();
							var $row = $element.find('[name=table_ref] tbody tr:last');
							//console.log($row);
							$row.find('[name=table_ref_item_programa]').val(data.table_ref[i].programa);
							$row.find('[name=table_ref_item_numero]').val(data.table_ref[i].numero);
							$row.find('[name=table_ref_item_recibo]').val(data.table_ref[i].recibo);
							$row.find('[name=table_ref_item_monto]').val(data.table_ref[i].monto);
							$row.find('[name=table_ref_item_referencia]').val(data.table_ref[i].referencia);
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
			}
		};
		p.referenceReturnData = function(){
			var tipo = p.$w.find('[name=tipo_referencia] :selected').val();
			var $element = p.$w.find('#referencia_format');
			var data = {
				tipo:tipo
			};
			switch(tipo){
				case "TEXTO_PLANO":
					data.texto_plano = $element.find('[name=format_1_referencia]').val();
					break;
				case "COMPRA":
					data.table_ref = [];
					if($element.find('[name=table_ref] tbody tr').length>0){
						for(var i=0;i<$element.find('[name=table_ref] tbody tr').length;i++){
							var $row = $element.find('[name=table_ref] tbody tr').eq(i);
							var _item = {
								referencia:$row.find('[name=table_ref_item_referencia]').val(),
								actividad:$row.find('[name=table_ref_item_actividad]').val(),
								factura:$row.find('[name=table_ref_item_factura]').val(),
								monto:$row.find('[name=table_ref_item_monto]').val(),
								requerimiento:$row.find('[name=table_ref_item_requerimiento]').val(),
							};
							if(!parseFloat(_item.monto)){
								$row.find('[name=table_ref_item_monto]').focus();
								K.msg({title:'Datos Requeridos',text:'Un monto ingresado en la tabla de referencia es incorrecto',type:'error'});
								return null;
							}else{
								data.table_ref.push(_item);
							}
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
				case "SERVICIO_TELEFONICO":
					data.table_ref = [];
					if($element.find('[name=table_ref] tbody tr').length>0){
						for(var i=0;i<$element.find('[name=table_ref] tbody tr').length;i++){
							var $row = $element.find('[name=table_ref] tbody tr').eq(i);
							var _item = {
								programa:$row.find('[name=table_ref_item_programa]').val(),
								numero:$row.find('[name=table_ref_item_numero]').val(),
								recibo:$row.find('[name=table_ref_item_recibo]').val(),
								monto:$row.find('[name=table_ref_item_monto]').val(),
								referencia:$row.find('[name=table_ref_item_referencia]').val(),
							};
							if(!parseFloat(_item.monto)){
								$row.find('[name=table_ref_item_monto]').focus();
								K.msg({title:'Datos Requeridos',text:'Un monto ingresado en la tabla de referencia es incorrecto',type:'error'});
								return null;
							}else{
								data.table_ref.push(_item);
							}
						}
					}else{
						K.msg({title:'Datos Requeridos',text:'No se encontraron items en la tabla de referencia',type:'error'});
						return null;
					}
					break;
			}
			return data;
		};
		p.referenceManager = function(){
			var tipo = p.$w.find('[name=tipo_referencia] :selected').val();
			var $element = p.$w.find('#referencia_format');
			$element.empty();
			switch(tipo){
				case "TEXTO_PLANO":
					$element.append('<div class="form-group"><label>Referencia</label><textarea value="format_1_referencia" class="form-control"></textarea></div>');
					break;
				case "COMPRA":
					$element.append('<table name="table_ref" class="table table-bordered" />');
					$element.find('table').append('<thead><tr><th colspan="6"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>REFERENCIA</th><th>ACTIVIDAD</th><th>FACTURA</th><th>MONTO</th><th>REQUERIMIENTO</th><th></th></tr></thead>')
					$element.find('table').append('<tbody />');
					$element.find('table').append('<tfoot />');
					$element.find('tfoot').append('<tr><td colspan="3">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					//$element.append('No esta disponible este formato');
					$element.find('[name=btnRefAddRow]').click(function(){
						var $row = $('<tr class="item" />');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_actividad"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_factura"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_requerimiento"></td>');
						$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');
						$row.find('[name=btnRefRemoveRow]').click(function(){
							$(this).closest('tr').remove();
						});
						$row.find('[name=table_ref_item_monto]').keyup(function(){
							var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
							var total = 0;
							for(var i=0;i<$inputs_monto.length;i++){
								if(parseFloat($inputs_monto.eq(i).val())){
									total+=parseFloat($inputs_monto.eq(i).val());
								}
							}
							p.$w.find('[name=table_ref_total]').val(K.round(total,2));
						});
						$element.find('tbody').append($row);
					});
					break;
				case "SERVICIO_TELEFONICO":
					$element.append('<table name="table_ref" class="table table-bordered" />');
					$element.find('table').append('<thead><tr><th colspan="7"><button class="btn btn-success" name="btnRefAddRow">Agregar Fila</button></th></tr><tr><th>PROGRAMA</th><th>NRO. RPM</th><th>RECIBO NRO.</th><th>CARGO FIJO</th><th>REFERENCIA</th><th></th></tr></thead>')
					$element.find('table').append('<tbody />');
					$element.find('table').append('<tfoot />');
					$element.find('tfoot').append('<tr><td colspan="3">Total</td><td><input type="text" name="table_ref_total" class="form-control" value="0.00"></td><td></td></tr>');
					//$element.append('No esta disponible este formato');
					$element.find('[name=btnRefAddRow]').click(function(){
						var $row = $('<tr class="item" />');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_programa"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_numero"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_recibo"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_monto"></td>');
						$row.append('<td style="width:160px;"><input type="text" class="form-control" name="table_ref_item_referencia"></td>');
						$row.append('<td style="width:50px;"><button class="btn btn-danger" name="btnRefRemoveRow"><i class="fa fa-trash"></i></button></td>');
						$row.find('[name=btnRefRemoveRow]').click(function(){
							$(this).closest('tr').remove();
						});
						$row.find('[name=table_ref_item_monto]').keyup(function(){
							var $inputs_monto = p.$w.find('[name=table_ref_item_monto]');
							var total = 0;
							for(var i=0;i<$inputs_monto.length;i++){
								if(parseFloat($inputs_monto.eq(i).val())){
									total+=parseFloat($inputs_monto.eq(i).val());
								}
							}
							p.$w.find('[name=table_ref_total]').val(K.round(total,2));
						});
						$element.find('tbody').append($row);
					});
					break;
			}
		};
		new K.Panel({
			title: 'Ver Comprobante de Pago',
			contentURL: 'ts/comp/edit',
			buttons: {
				"Imprimir": function(){
					var url = 'ts/comp/print?id='+p.id;
					K.windowPrint({
						id:'windowctCbanPrint',
						title: "Imprimir Comprobante de Pago",
						url: url
					});
				},
				"Pagar":function(){
					K.closeWindow(p.$w.attr('id'));
					tsComp.windowPagar({id:p.id});
				},
				"Anular":function(){
					//p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');					
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					var data = {
							_id: p.id,
							data: p.data
						};
						K.sendingInfo();
						$.post('ts/comp/anu2',data,function(){
							K.clearNoti();
							K.notification({title: 'Comprobante Anulado',text: 'El Comprobante de pago seleccionado ha sido anulado con &eacute;xito!'});
							K.closeWindow(p.$w.attr('id'));
							//tsComp.init();
							tsComp.windowDetails({id:p.id});
						});					
				},
				"Cerrar": function(){
					tsComp.init();
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=fec]').datepicker();
				p.$w.find('[name=tipo_referencia]').change(function(){
					p.referenceManager();
				}).change();
				new K.grid({
					$el: p.$w.find('[name=grid_det]'),
					search: false,
					pagination: false,
					cols: ['Nro.','Cuenta por Pagar','Actividad/Componente','Monto'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_det2]'),
					search: false,
					pagination: false,
					cols: ['Nro.','Cuenta por Pagar','Pago','Descuento'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						var $tfoot = $('<tfoot />');
						var $row_pago = $('<tr />');
						$row_pago.append('<td style="text-align:right;" colspan="3">Total Pago</td>');
						$row_pago.append('<td style="width:120px;"><input type="text" name="conc_total_pago" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_pago);

						var $row_desc = $('<tr />');
						$row_desc.append('<td style="text-align:right;" colspan="3">Total Descuento</td>');
						$row_desc.append('<td style="width:120px;"><input type="text" name="conc_total_desc" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_desc);

						var $row_neto = $('<tr />');
						$row_neto.append('<td style="text-align:right;" colspan="3">Total Neto</td>');
						$row_neto.append('<td style="width:120px;"><input type="text" name="conc_total_neto" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_neto);
						p.$w.find('[name=grid_det2] table').append($tfoot);
					}
				});
				
				/** /Detalle del Gasto */
				new K.grid({
					$el: p.$w.find('[name=grid_est]'),
					search: false,
					pagination: false,
					cols: ['Clasificador','Importe',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					}
				});

				new K.grid({
					$el: p.$w.find('[name=grid_cod]'),
					search: false,
					pagination: false,
					cols: ['Organizaci&oacute;n','',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_cont_pres]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					}
				});
				new K.grid({
					$el: p.$w.find('[name=grid_cont_patr]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Debe','Haber',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						
					}
				});
				/******************************************************************
				 * RETENCIONES
				******************************************************************/
				new K.grid({
					$el: p.$w.find('[name=grid_rete_dedu]'),
					search: false,
					pagination: false,
					cols: ['Cuenta Contable','Monto',''],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
					}
				});
				/***********************************************
				* BENEFICIARIOS MEDIO DE PAGO
				***********************************************/
				new K.grid({
					$el: p.$w.find('[name=grid_form_pago]'),
					search: false,
					pagination: false,
					cols: ['Nro.','Beneficiario','Sub-total','Cheque'],
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function($el){
						var $tfoot = $('<tfoot />');
						var $row_pago = $('<tr />');
						$row_pago.append('<td style="text-align:right;" colspan="2">Total</td>');
						$row_pago.append('<td style="width:120px;"><input type="text" name="form_pago_total" class="form-control" value="0.00" disabled></td>');
						$tfoot.append($row_pago);
						p.$w.find('[name=grid_form_pago] table').append($tfoot);
					}
				});		
				$.post('ts/ctban/all',function(ctban){
					var $cbo = p.$w.find('[name=pag_cuenta]');
					if(ctban!=null){
						for(var i=0; i<ctban.length; i++){
							var id = ctban[i]._id.$id;
							var cod_banco = ctban[i].cod_banco;
							var nomb = ctban[i].nomb;
							var cod = ctban[i].cod;
							var moneda = ctban[i].moneda;
							$cbo.append('<option value="'+id+'" >'+cod+' - '+ctban[i].nomb+'</option>');
							$cbo.find('option:last').data('data',ctban[i]);
						}
					}
					$.post('pr/fuen/all',function(fuente){
						var $cbo = p.$w.find('[name=cod_prog_fuente]');
						if(fuente!=null){
							for(var i=0; i<fuente.length; i++){
								var rubro = fuente[i].rubro;
								var cod = fuente[i].cod;
								var id = fuente[i]._id.$id;
								$cbo.append('<option value="'+id+'" >'+rubro+'</option>');
								$cbo.find('option:last').data('data',fuente[i]);
							}
						}
						if(p.id!=null){
							$.post('ts/comp/get','_id='+p.id,function(data){
								switch(data.estado){
									case "X":
										p.$w.find('#div_buttons button').eq(1).attr('disabled','disabled');//Pagar
										p.$w.find('#div_buttons button').eq(2).attr('disabled','disabled');//Anular
										break;
									case "C":
										p.$w.find('#div_buttons button').eq(1).attr('disabled','disabled');//Pagar
										p.$w.find('#div_buttons button').eq(2).attr('disabled','disabled');//Anular
										break;
								}
								p.$w.find('[name=cod]').html(ciHelper.codigos(data.cod,6));
								p.$w.find('[name=nomb]').val(data.nomb);
								p.$w.find('[name=forma_pago]').val(data.tipo_pago);
								p.$w.find('[name=cod_prog_fuente]').val(data.fuente._id.$id);
								p.$w.find('[name=pag_cuenta]').val(data.cuenta_banco._id.$id);
								/* Codificacion Programatica */
								p.$w.find('[name=cod_prog_sector]').val(data.cod_programatica.sector);
								p.$w.find('[name=cod_prog_pliego]').val(data.cod_programatica.pliego);
								p.$w.find('[name=cod_prog_programa]').val(data.cod_programatica.programa);
								p.$w.find('[name=cod_prog_subprograma]').val(data.cod_programatica.subprograma);
								p.$w.find('[name=cod_prog_proyecto]').val(data.cod_programatica.proyecto);
								p.$w.find('[name=cod_prog_obra]').val(data.cod_programatica.obra);
								if(data.referencia!=null){
									p.$w.find('[name=tipo_referencia]').val(data.referencia.tipo).change();
									p.refereceFillData(data.referencia.tipo,data.referencia);
								}
								p.$w.find('[name=btnRefAddRow]').hide();
								/* ./Codificacion Programatica */
								if(data.items!=null){
									var total_pag = 0;
									var total = 0;
									for(i=0;i<data.items.length;i++){
										var $row = $('<tr class="item" id="'+data.items[i]._id.$id+'" />');
										$row.append('<td>'+(i+1)+'</td>');
										$row.append('<td>'+data.items[i].motivo+'</td>');
										p.$w.find("[name=grid_det] tbody").append($row);
										if(data.items[i].afectacion){
											p.$w.find('[name=grid_det] #'+data.items[i]._id.$id).append('<td>'+data.items[i].afectacion[0].organizacion.actividad.cod+" / "+data.items[i].afectacion[0].organizacion.componente.cod+'</td>');
											p.$w.find('[name=grid_det] #'+data.items[i]._id.$id).append('<td>'+K.round(data.items[i].afectacion[0].monto,2)+'</td>').css({"text-align":"right"});
											if(data.items[i].afectacion.length>1){
												for(j=1;j<data.items[i].afectacion.length;j++){
													var $row2 = $('<tr class="item" />');
													$row2.append('<td></td>');
													$row2.append('<td></td>');
													$row2.append('<td>'+data.items[i].afectacion[j].organizacion.actividad.cod+" / "+data.items[i].afectacion[j].organizacion.componente.cod+'</td>');
													$row2.append('<td>'+K.round(data.items[i].afectacion[j].monto,2)+'</td>').css({"text-align":"right"});
													p.$w.find("[name=grid_det] tbody").append($row2);
												}
											}
											total_pag = parseFloat(data.items[i].total_pago) + total_pag;
										}else{
											p.$w.find('[name=grid_det] #'+data.items[i]._id.$id+'').remove();
										}
										total = parseFloat(data.items[i].total) + total;
									}
								}
								/** /Detalle del Gasto */

								if(data.items!=null){
									var total_pag = 0;
									var total_des = 0;
									for(i=0;i<data.items.length;i++){
										var $row = $('<tr class="item" />');
										$row.append('<td>'+(i+1)+'</td>');
										$row.append('<td>'+data.items[i].motivo+'</td>');
										$row.append('<td>'+K.round(data.items[i].total_pago,2)+'</td>').css({'text-align':'right'});
										$row.append('<td>'+K.round(data.items[i].total_desc,2)+'</td>').css({'text-align':'right'});
										p.$w.find("[name=grid_det2] tbody").append( $row );
										if(data.items[i].conceptos.length>0){
											for(j=0;j<data.items[i].conceptos.length;j++){
												var $row2 = $('<tr class="item" />');
												$row2.append('<td></td>');
												if(data.items[i].conceptos[j].concepto){
													$row2.append('<td>'+data.items[i].conceptos[j].concepto.nomb+'</td>');
												}else{
													$row2.append('<td>'+data.items[i].conceptos[j].observ+'</td>');
												}
												if(data.items[i].conceptos[j].tipo=="P"){
													$row2.append('<td>'+K.round(data.items[i].conceptos[j].monto,2)+'</td>').css({'text-align':'right'});
													$row2.append('<td>'+K.round(0,2)+'</td>').css({'text-align':'right'});
												}else if(data.items[i].conceptos[j].tipo=="D"){
													$row2.append('<td>'+K.round(0,2)+'</td>').css({'text-align':'right'});
													$row2.append('<td>'+K.round(data.items[i].conceptos[j].monto,2)+'</td>').css({'text-align':'right'});
												}
												p.$w.find("[name=grid_det2] tbody").append($row2);
											}
										}
										total_pag = parseFloat(data.items[i].total_pago) + total_pag;
										total_des = parseFloat(data.items[i].total_desc) + total_des;
									}
									p.$w.find('[name=conc_total_pago]').val(K.round(total_pag,2));
									p.$w.find('[name=conc_total_desc]').val(K.round(total_des,2));
									p.$w.find('[name=conc_total_neto]').val(K.round(total_pag-total_des,2));
								}
								
								/** Estadistica Objeto del Gasto */
								if(data.objeto_gasto!=null){
									for(i=0;i<data.objeto_gasto.length;i++){
										var $row2 = $('<tr class="item" />');
										$row2.append('<td>'+data.objeto_gasto[i].clasificador.cod+' - '+data.objeto_gasto[i].clasificador.nomb+'</td>');
										$row2.append('<td>'+K.round(data.objeto_gasto[i].monto,2)+'</td>');
										p.$w.find("[name=grid_est] tbody").append( $row2 );
									}
								}
								/** /Estadistica Objeto del Gasto */
								if(data.retenciones){
									for(i=0;i<data.retenciones.length;i++){
										var $row = $('<tr class="item" />');
										$row.append('<td>'+data.retenciones[i].cuenta.cod+'</td>');
										$row.append('<td>'+data.retenciones[i].monto+'</td>');
										var data2 = {
												cuenta:{
													_id:data.retenciones[i].cuenta._id.$id,
													cod:data.retenciones[i].cuenta.cod,
													descr:data.retenciones[i].cuenta.descr
												},
												monto:data.retenciones[i].monto,
												moneda:data.retenciones[i].moneda
										};
										$row.data('data',data2 );
										p.$w.find("[name=grid_rete_dedu] tbody").append( $row );
									}
								}
								/** /Retenciones y/o deducciones */
								
								/** Formas de Pago */		
								if(data.beneficiarios!=null){
									var total_pag = 0;
									for(i=0;i<data.beneficiarios.length;i++){
										/* Cheques */
										if(data.beneficiarios[i].beneficiario){
											var $row = $('<tr class="item" id="'+data.beneficiarios[i].beneficiario._id.$id+'" />');
											$row.append('<td>'+(i+1)+'</td>');
											$row.append('<td>'+ciHelper.enti.formatName(data.beneficiarios[i].beneficiario)+'</td>');
											$row.append('<td style="width:120px;"><input type="text" name="form_pago_subtotal" value="'+K.round(data.beneficiarios[i].monto,2)+'" class="form-control"></td>').css({"text-align":"right"});
											$row.append('<td style="width:150px;"><input type="text" name="cheque" value="'+data.beneficiarios[i].cheque+'" class="form-control"></td>');
											$row.data('data',data.beneficiarios[i].beneficiario).data('monto',data.beneficiarios[i].total);
											p.$w.find("[name=grid_form_pago] tbody").append( $row );
											total_pag = parseFloat(data.beneficiarios[i].monto) + total_pag;
										}
									}
									p.$w.find('[name=form_pago_total]').val(total_pag);
								}
								/** /Formas de Pago */
								
								/** Contabilidad Presupuestal */
								if(data.cont_presupuestal){
									for(i=0;i<data.cont_presupuestal.length;i++){
										var $row = $('<tr class="item">');
										$row.append('<td>'+data.cont_presupuestal[i].cuenta.cod+'</td>');
										if(data.cont_presupuestal[i].tipo=='D'){
											$row.append('<td>'+ciHelper.formatMon(data.cont_presupuestal[i].monto)+'</td>');
											$row.append('<td>');
										}else{
											$row.append('<td>');
											$row.append('<td>'+ciHelper.formatMon(data.cont_presupuestal[i].monto)+'</td>');
										}
										$row.append('<td></td>');
										$row.data('data',{
											cuenta: ctPcon.dbRel(data.cont_presupuestal[i].cuenta),
											tipo: data.cont_presupuestal[i].tipo,
											monto: data.cont_presupuestal[i].monto
										});
										p.$w.find('[name=grid_cont_pres] tbody').append($row);
									}
								}
								/** /Contabilidad Presupuestal */
								
								/** Contabilidad Patrimonial */
								if(data.cont_patrimonial){
									for(i=0;i<data.cont_patrimonial.length;i++){
										var $row = $('<tr class="item">');
										$row.append('<td>'+data.cont_patrimonial[i].cuenta.cod+'</td>');
										if(data.cont_patrimonial[i].tipo=='D'){
											$row.append('<td>'+ciHelper.formatMon(data.cont_patrimonial[i].monto)+'</td>');
											$row.append('<td>');
										}else{
											$row.append('<td>');
											$row.append('<td>'+ciHelper.formatMon(data.cont_patrimonial[i].monto)+'</td>');
										}
										$row.append('<td></td>');
										$row.data('data',{
											cuenta: ctPcon.dbRel(data.cont_patrimonial[i].cuenta),
											tipo: data.cont_patrimonial[i].tipo,
											monto: data.cont_patrimonial[i].monto
										});
										p.$w.find('[name=grid_cont_patr] tbody').append($row);
									}
								}
								p.$w.find('input, select, textarea').attr('disabled','disabled');
								K.unblock({$element: p.$w});
							},'json')
						}else{
							K.unblock({$element: p.$w});
						}
					},'json');
				},'json');
			}
		});
	},
	windowPagar: function(p){
		new K.Window({
			id: 'windowPagartsComp',
			title: 'Pagar Comprobante de Pago',
			contentURL: 'ts/comp/pagar',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 450,
			buttons: {
			    "Guardar":function(){
					data = new Object;
					data._id = p.id;
					data.estado_sunat = "1";
					data.cod_operacion = p.$w.find('[name=cod_oper]').html();
					data.cod = p.$w.find('[name=cod]').html();
					data.descr = p.$w.find('[name=mov_descr]').val();
					var entidad = p.$w.find('[name=mov_tdoc] :selected').data('data');
					data.entidades = new Object;
					if(entidad){
						data.entidades.tipo_doc = entidad.cod;
						data.entidades.num_doc = entidad.val;
						data.entidades.nomb = p.$w.find('[name=mov_bene]').html();
					}else{
						data.entidades.tipo_doc = "-";
						data.entidades.num_doc = "-";
						data.entidades.nomb = "Varios";
					}
					var entidades = p.$w.find('[name=mov_bene]').data('data');
					data.entidades.entidad = new Array();
					for(i=0;i<entidades.length;i++){
						var enti = new Object;
						enti = ciHelper.enti.dbRel(entidades[i].beneficiario);
						enti.docident = entidades[i].beneficiario.docident;
						data.entidades.entidad.push(enti);
					}
					var cuenta = p.$w.find('[name=cuenta]').data('data');
					if(cuenta!=null){
						data.cuenta = new Object;
						data.cuenta._id = cuenta._id.$id;
						data.cuenta.cod = cuenta.cod;
						data.cuenta.descr = cuenta.descr;
						data.tipo = "H";
						data.monto = p.$w.find('[name=monto]').html();
					}else{
						p.$w.find('[name=cuenta]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una Cuenta Contable!',type: 'error'});
					}		
					var medio_pago = p.$w.find('[name=medio_pago] :selected').data('data');
					data.medio_pago = new Object;
					data.medio_pago._id = medio_pago._id.$id;
					data.medio_pago.cod = medio_pago.cod;
					data.medio_pago.descr = medio_pago.descr;
					data.monto_original = p.total;
					data.organizacion = p.organizacion;
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post('ts/comp/pagar_save',data,function(rpta){
						K.clearNoti();
						if(rpta.error!=true){
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El Comprobante fue registrado con &eacute;xito!'});
							tsComp.windowDetails({id:p.id});
							tsComp.init();
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.closeWindow(p.$w.attr('id'));
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe crear un saldo inicial del Efectivo!',
								type: 'error'
							});
						}
					},'json');	
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowPagartsComp');
				K.block({$element: p.$w});
				
				$.post('ts/tipo/all',function(data3){
					var $cbo2 = p.$w.find('[name=medio_pago]');
					if(data3!=null){
						for(var i=0; i<data3.length; i++){
							var descr = data3[i].descr;
							var cod = data3[i].cod;
							var id = data3[i]._id.$id;
							$cbo2.append('<option value="'+id+'" >'+descr+'</option>');
							$cbo2.find('option:last').data('data',data3[i]);
						}
					}
				},'json');
				
				p.$w.find('[name=btnCuen]').click(function(){
					ctPcon.windowSelect({bootstrap:true, callback: function(data){
						p.$w.find('[name=cuenta]').val(data.cod).data('data',data);
						p.$w.find('[name=result-cuen]').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				/** /Codificacion programatica */
				$.post('ts/comp/get','_id='+p.id,function(data){
					if(data.comprobante){
						p.$w.find('[name=compr]').find('[value='+data.comprobante.tipo+']').attr('selected','selected');
						p.$w.find('[name=serie]').val(data.comprobante.serie);
						p.$w.find('[name=num]').val(data.comprobante.num);
					}else{
						p.$w.find('[name=compr]').closest('tr').remove();
					}
					p.$w.find('[name=cod]').html(ciHelper.codigos(data.cod,6));
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=ref]').html(data.ref);
					p.$w.find('[name=cod_oper]').html("CP "+ciHelper.codigos(data.cod,6));
					p.$w.find('[name=fec_oper]').html(ciHelper.date.format.bd_ymd(data.fecreg));
					p.$w.find('[name=mov_bene]').data('data',data.beneficiarios);
					if(data.beneficiarios.length<=1||!data.beneficiarios){
						p.$w.find('[name=mov_bene]').html(ciHelper.enti.formatName(data.beneficiarios[0].beneficiario));
						if(ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario).length>0){
							$select = p.$w.find('[name=mov_tdoc]');
							for(i=0;i<ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario).length;i++){
								$select.append('<option value="'+ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i].key+'">'+ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i].nomb+' ('+ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i].cod+')</option>');
								$select.find('option:last').data('data',ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[i]);
							}
							$select.find('option:first').attr('selected','selected');
							p.$w.find('[name=mov_ndoc]').html(ciHelper.enti.relDoc(data.beneficiarios[0].beneficiario)[0].val);
						}
						p.$w.find('[name=mov_tdoc]').change(function(){
							var tdoc = $(this).find('option:selected').data('data');
							p.$w.find('[name=mov_ndoc]').html(tdoc.key+' - '+tdoc.val);
						});
					}else{
						p.$w.find('[name=mov_bene]').html("Varios");
						p.$w.find('[name=mov_tdoc]').append('<option value="-">--</option>');
						p.$w.find('[name=mov_ndoc]').html("--");
					}
					p.$w.find('[name=forma_pago]').html(tsComp.tipo_pago[data.tipo_pago].descr);
					p.$w.find('[name=cuen_ban]').html(data.cuenta_banco.cod+" &laquo;"+data.cuenta_banco.nomb+"&raquo;");
					var cheques = '';
					if(data.beneficiarios!=null){
						for(i=0;i<data.beneficiarios.length;i++){
							cheques = cheques+", "+data.beneficiarios[i].cheque;
						}
					}
					
					if(data.tipo_pago=="C"){
						p.$w.find('[name=doc_sust]').html("Cheque(s)"+cheques);
						p.$w.find('[name=tdoc_sust_tr]').remove();
						p.$w.find('[name=doc_sust_trans_tr]').remove();
					}else if(data.tipo_pago=="T"){
						
					}			
					/** Detalle del Gasto */
					if(data.items!=null){
						var total_pag = 0;
						var total = 0;
						p.organizacion = new Array;
						for(i=0;i<data.items.length;i++){
							total = parseFloat(data.items[i].total) + total;
							/** Organizaciones Afectadas */
							if(data.items[i].afectacion){						
								for(j=1;j<data.items[i].afectacion.length;j++){
									p.organizacion.push({
										_id:data.items[i].afectacion[j].organizacion._id.$id,
										nomb:data.items[i].afectacion[j].organizacion.nomb
									});
								}										
							}
							/** /Organizaciones Afectadas */
						}
						p.total = total;
					}
					/** /Detalle del Gasto */
					p.$w.find('[name=monto_string]').html(ciHelper.monto2string.covertirNumLetras(total+""));
					p.$w.find('#totales .total li').eq(1).html("S/. "+total);
					p.$w.find('[name=monto]').html(total);
					/** Pagos */
					if(data.items!=null){
						var total_pag = 0;
						for(i=0;i<data.items.length;i++){
							total_pag = parseFloat(data.items[i].total_pago) + total_pag;
						}
						p.$w.find('#totales .pago li').eq(1).html("S/. "+total_pag);					
					}
					/** /Pagos */
					
					/** Descuentos */
					if(data.items!=null){
						var total_des = 0;
						for(i=0;i<data.items.length;i++){
							if(parseFloat(data.items[i].total_desc)>0){						
								total_des = parseFloat(data.items[i].total_desc) + total_des;
							}
						}
						p.$w.find('#totales .desc li').eq(1).html("S/. "+total_des);
					}
					/** /Descuentos */								
				},'json');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowSelectCheque: function(p){
		p.calc = function(){
			if(p.$w.find('.gridBody .item').length>0){
				var total = 0;
				for(i=0;i<p.$w.find('.gridBody .item').length;i++){
					if(p.$w.find('.gridBody .item').eq(i).find('input').is(':checked')){
						total = total + parseFloat(p.$w.find('.gridBody .item').eq(i).find('li').eq(5).html());
					}
				}
				p.$w.find('.gridBody .total').find('li').eq(5).html(total);
			}
		};
		p.search = function(params){
			params.estado = "C";
			params.cuenta = p.cuenta;
			params.tipo_pago = "C";
			params.ano = p.ano;
			params.mes = p.mes;
			var $row1 = p.$w.find('.gridReference_cheques').clone();
			$li1 = $('li',$row1);	
			$li1.eq(0).addClass('ui-state-default ui-button-text-only');
			$li1.eq(1).addClass('ui-state-default ui-button-text-only');
			$li1.eq(2).addClass('ui-state-default ui-button-text-only');
			$li1.eq(3).html( "Total" ).addClass('ui-state-default ui-button-text-only');
			$li1.eq(4).html("");
			$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
			p.$w.find(".gridBody").append( $row1.children() );
			
			$.post('ts/comp/all_cheques',params,function(data){
				if ( data.items ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						for(j=0;j<result.beneficiarios.length;j++){
							var $row = p.$w.find('.gridReference_cheques').clone();
							$li = $('li',$row);	
							$li.eq(0).html( ciHelper.dateFormat(result.fecreg) );
							$li.eq(1).html( result.beneficiarios[j].cheque );
							$li.eq(2).html( ciHelper.enti.formatName(result.beneficiarios[j].beneficiario) );
							$li.eq(3).html( result.beneficiarios[j].monto );
							var dataa = {
									fecha: ciHelper.dateFormat(result.fecreg),
									cheque : result.beneficiarios[j].cheque,
									detalle : result.beneficiarios[j].beneficiario,
									monto : result.beneficiarios[j].monto
							};
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',dataa);
							p.$w.find(".gridBody .total").before( $row.children() );
						}		
					}
				} else {
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encotraron cheques en este periodo y cuenta bancaria!',type: 'error'});
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowSelecttsCompCheque',
			title: 'Seleccionar Cheques',
			contentURL: 'ts/comp/select_cheques',
			icon: 'ui-icon-search',
			width: 600,
			height: 400,
			buttons: {
				"Guardar": function(){
					if(p.$w.find('.gridBody .item').length>0){
						var total = 0;
						var data = new Object;
						data.cheques = new Array;
						for(i=0;i<p.$w.find('.gridBody .item').length;i++){
							if(p.$w.find('.gridBody .item').eq(i).find('input').is(':checked')){
								total = total + parseFloat(p.$w.find('.gridBody .item').eq(i).find('li').eq(5).html());
								data.cheques.push(p.$w.find('.gridBody .item').eq(i).data('data'));
							}
						}
						data.total = total;
					}
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowSelecttsCompCheque');
				K.block({$element: p.$w});
				p.search({page: 1});
				p.$w.find('[name=checkitem]').live('click',function(){
					var $file = $(this).closest('.item');
					if($(this).is(':checked')){
						$file.find('li').eq(5).html($file.find('li').eq(3).html());
					}else{
						$file.find('li').eq(5).html("");
					}
					p.calc();
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowRectiCheque: function(p){
		p.calc = function(){
			if(p.$w.find('.gridBody .item').length>0){
				var total = 0;
				for(i=0;i<p.$w.find('.gridBody .item').length;i++){
					if(p.$w.find('.gridBody .item').eq(i).find('input:eq(0)').is(':checked')){
						var get_monto = p.$w.find('.gridBody .item').eq(i).find('li').eq(6).html();
						if(get_monto=="")get_monto = 0;
						total = total + parseFloat(get_monto);
					}
				}
				p.$w.find('.gridBody .total').find('li').eq(6).html(K.round(total,2));
			}
		};
		p.search = function(params){
			params.estado = "C";
			params.cuenta = p.cuenta;
			params.tipo_pago = "C";
			params.ano = p.ano;
			params.mes = p.mes;
			var $row1 = p.$w.find('.gridReference_cheques').clone();
			$li1 = $('li',$row1);	
			$li1.eq(0).addClass('ui-state-default ui-button-text-only');
			$li1.eq(1).addClass('ui-state-default ui-button-text-only');
			$li1.eq(2).addClass('ui-state-default ui-button-text-only');
			$li1.eq(3).addClass('ui-state-default ui-button-text-only');
			$li1.eq(4).html("").addClass('ui-state-default ui-button-text-only');
			$li1.eq(5).html( "Total" ).addClass('ui-state-default ui-button-text-only');
			$li1.eq(6).html("");
			$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
			p.$w.find(".gridBody").append( $row1.children() );
			
			$.post('ts/comp/all_cheques',params,function(data){
				if ( data.items ) {
					for (i=0; i < data.items.length; i++) {
						var result = data.items[i];
						for(j=0;j<result.beneficiarios.length;j++){
							var $row = p.$w.find('.gridReference_cheques').clone();
							$li = $('li',$row);	
							$li.eq(0).html( ciHelper.dateFormat(result.fecreg) );
							$li.eq(1).html( result.beneficiarios[j].cheque );
							$li.eq(2).html( ciHelper.enti.formatName(result.beneficiarios[j].beneficiario) );
							$li.eq(3).html( result.beneficiarios[j].monto );
							var dataa = {
									cheque : result.beneficiarios[j].cheque,
									monto : result.beneficiarios[j].monto
							};
							$row.find('[name=extracto]').attr("readonly","readonly");
							$row.wrapInner('<a class="item" href="javascript: void(0);" />');
							$row.find('a').data('data',dataa);
							p.$w.find(".gridBody .total").before( $row.children() );
						}		
					}
				} else {
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encotraron cheques en este periodo y cuenta bancaria!',type: 'error'});
				}
				K.unblock({$element: p.$w});
			},'json');
		};
		new K.Modal({
			id: 'windowRectitsCompCheque',
			title: 'Rectificar Cheques',
			contentURL: 'ts/comp/recti_cheques',
			icon: 'ui-icon-search',
			width: 800,
			height: 400,
			buttons: {
				"Guardar": function(){
					if(p.$w.find('.gridBody .item').length>0){
						var total = 0;
						var data = new Object;
						data.cheques = new Array;
						for(i=0;i<p.$w.find('.gridBody .item').length;i++){
							if(p.$w.find('.gridBody .item').eq(i).find('input').is(':checked')){
								total = total + parseFloat(p.$w.find('.gridBody .item').eq(i).find('li').eq(6).html());
								var dat = p.$w.find('.gridBody .item').eq(i).data('data');
								dat.estracto = p.$w.find('.gridBody .item').eq(i).find('[name=extracto]').val();
								dat.diferencia = p.$w.find('.gridBody .item').eq(i).find('li').eq(6).html();
								data.cheques.push(dat);
							}
						}
						data.total = total;
					}
					p.callback(data);
					K.closeWindow(p.$w.attr('id'));
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowRectitsCompCheque');
				K.block({$element: p.$w});
				p.search({page: 1});
				p.$w.find('[name=moneda_simbol]').html(ctCban.moneda[p.moneda].simbol);
				p.$w.find('[name=diferencia]').html(p.diferencia);
				p.$w.find('[name=checkitem]').live('click',function(){
					var $file = $(this).closest('.item');
					if($(this).is(':checked')){
						$file.find('[name=extracto]').removeAttr("readonly");
					}else{
						$file.find('[name=extracto]').attr("readonly","readonly");
						$file.find('[name=extracto]').val("");
						$file.find('li').eq(6).html("");			
					}
					p.calc();
				});
				p.$w.find('[name=extracto]').live('keyup',function(){
					var $file = $(this).closest('.item');
					var monto = $file.find('li').eq(3).html();
					var extracto = $file.find('[name=extracto]').val();
					var diferencia = parseFloat(monto)-parseFloat(extracto);
					$file.find('li').eq(6).html(K.round(diferencia,2));
					p.calc();
				});
				K.unblock({$element: p.$w});
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','mg/orga','ts/ctpp','pr/clas'],
	function(mgEnti,ctPcon,mgOrga, tsCtpp, prClas){
		return tsComp;
	}
);