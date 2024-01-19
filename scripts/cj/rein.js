/******************************RECIBO DE INGRESOS DE LA FACTURACION ELECTRONICA********************************************/
cjErein = {
	states: {
		RG: {
			descr: "Registrado",
			color: "#CCC",
			label: '<span class="label label-primary">Registrado</span>'
		},
		RB: {
			descr: "Recibido",
			color: "black",
			label: '<span class="label label-danger">Anulado</span>'
		},
		X: {
			descr: "Anulado",
			color: "black",
			label: '<span class="label label-warning">Pendiente</span>'
		},
		RE: {
			descr: "Registrado Libro Efectivo",
			color: "blue",
			label: '<span class="label label-info">Reemplazado</span>'
		},
		RC: {
			descr: "Registrado Libro Cta Cte",
			color: "black",
			label: '<span class="label label-info">Reemplazado</span>'
		}
	},
	tipo_inm: {
		A: 'Alquileres',
		P: 'Playas'
	},
	Modulo: {
		CM: 'Cementerio',
		MH: 'Moises Heresi',
		IN: 'Inmuebles'
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			cod: item.cod,
			modulo: item.modulo,
			tipo_inm: item.tipo_inm

		};
	},
	init: function(){
		K.initMode({
			mode: 'cj',
			action: 'cjErein',
			titleBar: {
				title: 'Recibo de Ingresos'
			}
		});
		new K.Panel({
			onContentLoaded: function(){
				$.post('cj/caje/cajas_trabajador',{_id: K.session.enti._id.$id}, function(cajas){
					if(cajas!=null){
						if(cajas.length>0){
					   		var $grid = new K.grid({
								cols: ['','',{n:'Recibo de Ingreso',f:'num'},'Planilla','Tipo','Total',{n:'Fecha',f:'fec'}],
								data: 'ts/rein/lista',
								params: {
									//organizacion: '51a50edc4d4a13441100000e'
									modulo: 'IN'
								},
								itemdescr: 'recibo(s) de ingresos',
								toolbarHTML: '<select name="caja" class="form-control"></select><button type="button" class="btn btn-primary" name="btnAgregar2">Nuevo EComprobante</button>',
								onContentLoaded: function($el){
									$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height('auto'))+240+'px');
									for(var i=0;i<cajas.length;i++){
										$el.find('[name=caja]').append('<option value="'+cajas[i]._id.$id+'">'+cajas[i].nomb+'</option>');
										$el.find('[name=caja]').find('option:last').data('data',cajas[i]);
									}
									$el.find('[name=btnAgregar2]').click(function(){
										cjErein.windowGen({caja: {
											_id: $el.find('[name=caja] :selected').val(),
											nomb: $el.find('[name=caja] :selected').text(),
											modulo: $el.find('[name=caja] :selected').data('data').modulo
										}});
									});
									$el.find('[name=caja]').change(function(){
										$grid.reinit({params:{caja:$el.find('[name=caja] :selected').val()}});
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
									$row.append('<td>'+cjErein.states[data.estado].label+'</td>');
									$row.append('<td>Recibo de Ingresos N&deg'+data.cod+'</td>');
									$row.append('<td>N&deg'+data.planilla+'</td>');
									$row.append('<td>'+cjErein.tipo_inm[data.tipo_inm]+'</td>');
									$row.append('<td>'+ciHelper.formatMon(data.total,data.moneda)+'</td>');
									if(ciHelper.date.format.bd_ymd(data.fec)==ciHelper.date.format.bd_ymd(data.fecfin))
										$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
									else
										$row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+' - '+ciHelper.date.format.bd_ymd(data.fecfin)+'</td>');
									$row.data('id',data._id.$id).dblclick(function(){
										K.windowPrint({
											id:'windowcjFactPrint',
											title: "Recibo de Caja",
											url: "cj/ecom/reci_ing?_id="+$(this).data('id')
										});
									}).data('estado',data.estado).contextMenu("conMencjErein", {
										onShowMenu: function($row, menu) {
											if($row.data('estado')=='X'){
												$('#conMenInComp_cam,#conMenInComp_pag',menu).remove();
											}
											return menu;
										},
										bindings: {
											'conMencjErein_imp': function(t) {
												K.windowPrint({
													id:'windowcjFactPrint',
													title: "Recibo de Caja",
													url: "cj/ecom/reci_ing?_id="+K.tmp.data('id')
												});
											},
											'conMencjErein_pla': function(t) {
												window.open("cj/ecom/planilla?_id="+K.tmp.data('id'));
											},
											'conMencjErein_anu': function(t) {
												K.incomplete();
												return 0;
												cjErein.windowAnular({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
											}
										}
									});
									return $row;
								}
							});
				   		}
					}
				},'json');
			}
		});
	},
	windowGen: function(p){
	if(p==null) p = {};
	$.extend(p,{
		fill: function(){
			if(p.$w.find('[name=fec]').val()>p.$w.find('[name=fecfin]').val()){
				p.$w.find('[name=fecfin]').datepicker('setValue',p.$w.find('[name=fec]').val());
			}
			var orga = p.$w.find('[name=orga]').data('data');
			if(orga==null){
				p.$w.find('[name=btnOrga]').click();
				return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
			}
			K.block({$element: p.$w});
			$.post('cj/rein/get_rec_ecom',{
				tipo_inm: p.$w.find('[name=tipo_inm] option:selected').val(),
				fec: p.$w.find('[name=fec]').val(),
				fecfin: p.$w.find('[name=fecfin]').val(),
				orga: '51a50edc4d4a13441100000e',//orga._id.$id
				actividad: '51e995a64d4a13ac0b00000e',//orga.actividad._id.$id
				componente: '51e99cec4d4a13ac0b000011'//orga.componente._id.$id
			},function(data){
				p.conf = data.conf;
				var tmp_ctas_pat = [];
				p.$w.find('[name=gridComp] tbody').empty();
				p.$w.find('[name=gridAnu] tbody').empty();
				p.$w.find('[name=gridCod] tbody').empty();
				p.$w.find('[name=gridPag] tbody').empty();
				p.$w.find('[name=gridCont] tbody').empty();
				if(data.ecom==null){
					K.unblock({$element: p.$w});
					return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay comprobantes registrados para la fecha seleccionada!',type: 'error'});
				}
				p.$w.find('[name=planilla]').val(data.planilla);
				p.prog = data.prog;
				var cuenta_igv = p.conf.IGV;
				tmp_ctas_pat.push({
					cod: cuenta_igv.cod.substr(0,9),
					cuenta: cuenta_igv,
					total: 0
				});
				var $row = $('<tr class="item">');
				$row.append('<td>'+data.prog.pliego.cod+'</td>');
				$row.append('<td>'+data.prog.programa.cod+'</td>');
				$row.append('<td>'+data.prog.subprograma.cod+'</td>');
				$row.append('<td>'+data.prog.proyecto.cod+'</td>');
				$row.append('<td>'+data.prog.obra.cod+'</td>');
				//$row.append('<td>'+orga.actividad.cod+'</td>');
				//$row.append('<td>'+orga.componente.cod+'</td>');
				$row.append('<td><select name="fuente"></td>');
				for(var k=0,l=p.fuen.length; k<l; k++){
					$row.find('select').append('<option value="'+p.fuen[k]._id.$id+'">'+p.fuen[k].cod+'</option>');
					$row.find('select option:last').data('data',p.fuen[k]);
				}
				p.$w.find('[name=gridCod] tbody').append($row);
				// Efectivo en pagos
				var $row = $('<tr class="item">');
				$row.append('<td>Efectivo Soles</td>');
				$row.append('<td>');
				$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
				$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
				$row.append('<td>');
				$row.data('total',0);
				p.$w.find('[name=gridPag] tbody').append($row);
				var $row = $('<tr class="item">');
				$row.append('<td>Efectivo D&oacute;lares</td>');
				$row.append('<td>');
				$row.append('<td>'+ciHelper.formatMon(0,'D')+'</td>');
				$row.append('<td>'+ciHelper.formatMon(0)+'</td>');
				$row.append('<td>');
				$row.data('total',0);
				p.$w.find('[name=gridPag] tbody').append($row);
				
				// Bucle de comprobantes
				var tot_sol = 0,
				tot_dol = 0,
				tot_dol_sol = 0,
				total = 0;
				total_ = 0;

				// LOS COMPROBANTES ELECTRONICOS DE ESTE RECIBO DE INGRESO PERMITEN 4 TIPOS DE PAGOS:
				//	a) Pagos de alquileres mensuales enteros
				//	b) Pago de alquileres mensuales parciales
				//	c) Pago de Actas de conciliacion
				//	d) Pago de Playas
				//  e) Pago de Farmacias
				//	f) Pago de Agua Chapi
				//	h) Pago de Servicio

				if(data.ecom!==null) for(var i=0,j=data.ecom.length; i<j; i++){
					var result = data.ecom[i];
					// CUANDO EL COMPROBANTE ELECTRONICO SE ENCUENTRE ANULADO
					if(result.estado=='X'){
						var $row = $('<tr class="item">');
						$row.append('<td>'+result.tipo+' '+result.serie+' '+result.numero+'</td>');
						if(result.cliente_nomb!=null)
							$row.append('<td>'+result.cliente_nomb+'</td>');
						$row.data('data',{
							_id: result._id.$id,
							tipo: result.tipo,
							serie: result.serie,
							num: result.numero
						});
						p.$w.find('[name=gridAnu] tbody').append($row);
					}else{
						for(var k=0,l=result.items.length; k<l; k++){
							var item = result.items[k];
							/**
							* PARA PAGOS MENSuALES COMPLETOS Y PARCIALES
							*/
							if(item.tipo=="pago_meses" || item.tipo=="pago_parcial"){
								for(var m=0,n=item.conceptos.length; m<n; m++){
									var conc = item.conceptos[m],
									$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
									tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
									if(conc.pago!=null){
										conc_pare_pago=conc.pago;
									}
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: conc.cuenta.cod.substr(0,9),
											cuenta: conc.cuenta,
											total: parseFloat(K.round(tot_conc,2))
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(tot_conc,2));
									}
									if($row.length>0){
										tot_conc_sec = tot_conc + parseFloat($row.data('total'));
										$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
										$row.data('total',tot_conc_sec);
									}else{
										var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
										$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
										$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
										$row.append('<td>'+conc.descr+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
										$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
										//if(conc.pago != null){
										$row.data('total',tot_conc).data('data',{
											cuenta: {
												_id: conc.cuenta._id.$id,
												cod: conc.cuenta.cod,
												descr: conc.cuenta.descr
											},
											comprobante: {
												_id: result._id.$id,
												tipo: result.tipo,
												serie: result.serie,
												num: result.numero
											},
											contrato: conc.alquiler.contrato.$id,
											concepto: result.cliente_nomb+' - '+result.inmueble.direccion+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes
											//concepto: result.cliente_nomb+' - '+'DE UN INMUEBLE'+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes
										});
										//}
										p.$w.find('[name=gridComp] tbody').append($row);
									}
									total += parseFloat(K.round(tot_conc,2));
								}
							}
							/**
							* PARA PAGOS RELACIONADOS A ACTAS DE CONCILIACION
							*/
							else if(item.tipo=="pago_acta"){
								for(var m=0,n=item.conceptos.length; m<n; m++){
										var conc = item.conceptos[m];
										//if(conc._id==null){
										//	conc = {
										//		_id: {$id:'IGV'+result._id.$id},
										//		nomb: item.concepto
										//	}
										//}
										if(conc.cuenta==null){
											console.log("Concepto con cuenta null");
											console.log(conc);
											console.log("Fin cuenta null");
											if(item.cuenta._id.$id==null) item.cuenta._id = {
												$id: item.cuenta._id
											};
											conc.cuenta = item.cuenta;
										}
										//var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+']'),
										var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
										tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
										
										
										var tmp_ctas_pat_i = -1;
										for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
											if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
												tmp_ctas_pat_i = tmp_i;
												tmp_i = tmp_j;
											}
										}
										if(tmp_ctas_pat_i==-1){
											tmp_ctas_pat.push({
												cod: conc.cuenta.cod.substr(0,9),
												cuenta: conc.cuenta,
												total: parseFloat(K.round(conc.monto,2))
											});
										}else{
											tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(conc.monto,2));
										}
										/*if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
											console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
										}*/
										//tmp_ctas_pat
										
										
										if($row.length>0){
											tot_conc_sec = tot_conc + parseFloat($row.data('total'));
											$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
											$row.data('total',tot_conc_sec);
										}else{
											var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
											//var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+'">');
											$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
											$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
											//$row.append('<td>'+result.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
											$row.append('<td>'+conc.descr+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
											$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
											$row.data('total',tot_conc).data('data',{
												cuenta: {
													_id: conc.cuenta._id.$id,
													cod: conc.cuenta.cod,
													descr: conc.cuenta.descr
												},
												comprobante: {
													_id: result._id.$id,
													tipo: result.tipo,
													serie: result.serie,
													num: result.num
												},
												//concepto: result.servicio.nomb+' - '+ciHelper.enti.formatName(result.cliente)+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')
												//concepto: result.cliente_nomb+' - '+result.inmueble.direccion+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes


												//concepto: result.cliente_nomb+' - '+"result.inmueble.direccion"+' - '+conc_pare_pago.ano+'-'+conc_pare_pago.mes
												concepto: 'Pago de Acta de Conciliaci&oacute;n - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')
											});
											p.$w.find('[name=gridComp] tbody').append($row);
										}
										total += parseFloat(K.round(tot_conc,2));
								}
							}
							/**
							* PARA PAGO DE SERVICIOS DE CUENTAS POR COBRAR COMO LUZ, GARANTIAS, ETC
							*/
							else if(item.tipo=="cuenta_cobrar"){
								for(var m=0,n=item.conceptos.length; m<n; m++){
									var conce = item.conceptos[m];
									if(conce.concepto!==null){
										var conc = conce.concepto;
										if(conc._id==null){
											conc = {
												_id: {$id:'IGV'+result._id.$id},
												nomb: item.concepto
											}
										}
										if(conc.cuenta==null){
											if(conce.cuenta._id.$id==null) conce.cuenta._id = {
												$id: conce.cuenta._id
											};
											conc.cuenta = conce.cuenta;
										}
										var $row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+']'),
										tot_conc = (result.moneda=='PEN')?parseFloat(conce.monto):parseFloat(conce.monto)*parseFloat(result.tipo_cambio);
										
										
										
										
										
										
										
										var tmp_ctas_pat_i = -1;
										for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
											if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
												tmp_ctas_pat_i = tmp_i;
												tmp_i = tmp_j;
											}
										}
										if(tmp_ctas_pat_i==-1){
											tmp_ctas_pat.push({
												cod: conc.cuenta.cod.substr(0,9),
												cuenta: conc.cuenta,
												total: parseFloat(K.round(conce.monto,2))
											});
										}else{
											tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(conce.monto,2));
										}
										/*if(conc.cuenta.cod.substr(0,9)=='2103.03.4'){
											console.log(tmp_ctas_pat[tmp_ctas_pat_i].total);
										}*/
										//tmp_ctas_pat
										}
									
									
									
									
									
									
									if($row.length>0){
										tot_conc_sec = tot_conc + parseFloat($row.data('total'));
										$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
										$row.data('total',tot_conc_sec);
									}else{
										var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'-'+conc._id.$id+'">');
										$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
										$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
										$row.append('<td>'+item.cuenta_cobrar.servicio.nomb+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
										$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
										$row.data('total',tot_conc).data('data',{
											cuenta: {
												_id: conc.cuenta._id.$id,
												cod: conc.cuenta.cod,
												descr: conc.cuenta.descr
											},
											comprobante: {
												_id: result._id.$id,
												tipo: result.tipo,
												serie: result.serie,
												num: result.numero
											},
											concepto: item.cuenta_cobrar.servicio.nomb+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')
										});
										p.$w.find('[name=gridComp] tbody').append($row);
									}
									total += parseFloat(K.round(tot_conc,2));
								}
							}
							/**
							* PARA PAGO DE SERVICIOS
							*/
							else if(item.tipo=="servicio"){
								for(var m=0,n=item.conceptos.length; m<n; m++){
									var conc = item.conceptos[m],
									$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
									tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
									
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: conc.cuenta.cod.substr(0,9),
											cuenta: conc.cuenta,
											total: parseFloat(K.round(tot_conc,2))
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(tot_conc,2));
									}
									if($row.length>0){
										tot_conc_sec = tot_conc + parseFloat($row.data('total'));
										$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
										$row.data('total',tot_conc_sec);
									}else{
										var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
										$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
										$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
										$row.append('<td>'+result.cliente_nomb+'</td>');
										$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
										$row.data('total',tot_conc).data('data',{
											cuenta: {
												_id: conc.cuenta._id.$id,
												cod: conc.cuenta.cod,
												descr: conc.cuenta.descr
											},
											comprobante: {
												_id: result._id.$id,
												tipo: result.tipo,
												serie: result.serie,
												num: result.numero
											},
											concepto: result.cliente_nomb
										});
										p.$w.find('[name=gridComp] tbody').append($row);
									}
								}
							}
							/**
							* PARA PAGO DE FARMACIAS O AGUA CHAPI
							*/
							else if(item.tipo=="farmacia" || item.tipo=="agua_chapi"){
								for(var m=0,n=item.conceptos.length; m<n; m++){
									var conc = item.conceptos[m],
									$row = p.$w.find('[name=gridComp] [name='+result._id.$id+'-'+conc.cuenta._id.$id+']'),
									tot_conc = (result.moneda=='PEN')?parseFloat(conc.monto):parseFloat(conc.monto)*parseFloat(result.tipo_cambio);
									if(conc.pago!=null){
										conc_pare_pago=conc.pago;
									}
									var tmp_ctas_pat_i = -1;
									for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
										if(tmp_ctas_pat[tmp_i].cod==conc.cuenta.cod.substr(0,9)){
											tmp_ctas_pat_i = tmp_i;
											tmp_i = tmp_j;
										}
									}
									if(tmp_ctas_pat_i==-1){
										tmp_ctas_pat.push({
											cod: conc.cuenta.cod.substr(0,9),
											cuenta: conc.cuenta,
											total: parseFloat(K.round(tot_conc,2))
										});
									}else{
										tmp_ctas_pat[tmp_ctas_pat_i].total += parseFloat(K.round(tot_conc,2));
									}
									if($row.length>0){
										tot_conc_sec = tot_conc + parseFloat($row.data('total'));
										$row.find('td:eq(3)').html(ciHelper.formatMon(tot_conc_sec));
										$row.data('total',tot_conc_sec);
									}else{
										var $row = $('<tr class="item" name="'+result._id.$id+'-'+conc.cuenta._id.$id+'">');
										$row.append('<td>'+conc.cuenta.cod+' - '+conc.cuenta.descr+'</td>');
										$row.append('<td>'+result.tipo+' '+result.serie+' - '+result.numero+'</td>');
										$row.append('<td>'+conc.descr+' - '+result.cliente_nomb+((result.moneda=='USD')?' - Tipo de Cambio: '+ciHelper.formatMon(result.tipo_cambio):'')+'</td>');
										$row.append('<td>'+ciHelper.formatMon(tot_conc)+'</td>');
										$row.data('total',tot_conc).data('data',{
											cuenta: {
												_id: conc.cuenta._id.$id,
												cod: conc.cuenta.cod,
												descr: conc.cuenta.descr
											},
											comprobante: {
												_id: result._id.$id,
												tipo: result.tipo,
												serie: result.serie,
												num: result.numero
											},
											//producto: conc.producto.$id,
											concepto: result.cliente_nomb+' - '+conc.descr
										});
										p.$w.find('[name=gridComp] tbody').append($row);
									}
									total += parseFloat(K.round(tot_conc,2));
								}
							}
						}
						//suma de EFECTIVOS como soles y dolares
						if(result.efectivos!=null){
							if(parseFloat(result.efectivos[0].monto)!=0) tot_sol += parseFloat(result.efectivos[0].monto);
							if(parseFloat(result.efectivos[1].monto)!=0){
								tot_dol += parseFloat(result.efectivos[1].monto);
								tot_dol_sol += parseFloat(result.efectivos[1].monto)*parseFloat(result.tipo_cambio);
							}
						}
						//suma de VOUCHERS como cuentas de banco y detracciones
						if(result.vouchers!=null){
							for(var k=0,l=result.vouchers.length; k<l; k++){
								var $row = $('<tr class="item vouc">');
								$row.append('<td>'+'Voucher - '+result.vouchers[k].num+'</td>');
								$row.append('<td>'+result.vouchers[k].cuenta_banco.nomb+'</td>');
								$row.append('<td>'+ciHelper.formatMon(result.vouchers[k].monto,result.vouchers[k].moneda)+'</td>');
								$row.append('<td>'+ciHelper.formatMon((result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tipo_cambio))+'</td>');
								$row.append('<td>'+result.cliente_nomb+'</td>');
								$row.data('data',{
									num: result.vouchers[k].num,
									cuenta_banco: {
										_id: result.vouchers[k].cuenta_banco._id.$id,
										nomb: result.vouchers[k].cuenta_banco.nomb,
										cod_banco: result.vouchers[k].cuenta_banco.cod_banco,
										cod: result.vouchers[k].cuenta_banco.cod,
										moneda: result.vouchers[k].cuenta_banco.moneda
									},
									monto: parseFloat(result.vouchers[k].monto),
									cliente: result.cliente_nomb,
									tc: (result.tipo_cambio!=null)?result.tipo_cambio:0
								});
								$row.data('total',parseFloat(result.vouchers[k].monto));
								$row.data('moneda',parseFloat(result.vouchers[k].moneda));
								$row.data('total_sol',(result.vouchers[k].moneda=='S')?result.vouchers[k].monto:parseFloat(result.vouchers[k].monto)*parseFloat(result.tipo_cambio));
								p.$w.find('[name=gridPag] tbody').append($row);
							}
						}
						total_ += parseFloat(K.round(parseFloat(result.total),2));
									
					}
				}
				total = total_;
				// GENERACION AUTOMATICA DE CONTABILIDAD PATRIMONIAL
				var tmp_to = 0;
				for(var tmp_i=0,tmp_j=tmp_ctas_pat.length; tmp_i<tmp_j; tmp_i++){
					var $row = $('<tr class="item">');
					$row.append('<td>'+tmp_ctas_pat[tmp_i].cod+'</td>');
					$row.append('<td>');
					$row.append('<td>'+ciHelper.formatMon(tmp_ctas_pat[tmp_i].total)+'</td>');
					var tmp_cta_a = tmp_ctas_pat[tmp_i].cuenta;
					tmp_cta_a.tipo = 'D';
					$row.data('data',tmp_cta_a).data('total',tmp_ctas_pat[tmp_i].total).attr('name',tmp_ctas_pat[tmp_i].cuenta._id.$id);
					p.$w.find('[name=gridCont] tbody').append($row);
					tmp_to += parseFloat(tmp_ctas_pat[tmp_i].total);
				}
				tmp_to = parseFloat(K.round(tmp_to,2));
				var $row = $('<tr class="item">');
				$row.append('<td>1101.0101</td>');
				$row.append('<td>'+ciHelper.formatMon(tmp_to)+'</td>');
				$row.append('<td>');
				$row.data('data',{
					_id: '51a6473a4d4a13540a000009',
					cod: '1101.0101',
					descr: 'Caja M/N',
					tipo: 'H'
				}).data('total',tmp_to).attr('name','51a6473a4d4a13540a000009');
				p.$w.find('[name=gridCont] tbody .item:eq(0)').before($row);
				// TOTALES
				var $row = $('<tr class="item result">');
				$row.append('<td>');
				$row.append('<td>');
				$row.append('<td>Parcial</td>');
				$row.append('<td>'+ciHelper.formatMon(total)+'</td>');
				$row.data('total',total);
				p.$w.find('[name=gridComp] tbody').append($row);
				p.$w.find('[name=gridPag] .item').eq(0).data('total',tot_sol)
				.find('td:eq(2)').html(ciHelper.formatMon(tot_sol));
				p.$w.find('[name=gridPag] .item').eq(0)
				.find('td:eq(3)').html(ciHelper.formatMon(tot_sol));
				p.$w.find('[name=gridPag] .item').eq(1).data('total',tot_dol).data('total_sol',tot_dol_sol)
				.find('td:eq(2)').html(ciHelper.formatMon(tot_dol,'D'));
				p.$w.find('[name=gridPag] .item').eq(1)
				.find('td:eq(3)').html(ciHelper.formatMon(tot_dol_sol));
				/*p.calcHab();
				p.calcDeb();*/
				K.unblock({$element: p.$w});
			},'json');
		}
	});
	new K.Panel({
		title: 'Recibo de Ingresos',
		contentURL: 'cj/ecom/gen_bootstrap',
		store: false,
		buttons: {
			"Guardar": {
				icon: 'fa-save',
				type: 'success',
				f: function(){
					K.clearNoti();
					var data = {
						cod: p.$w.find('[name=num]').val(),
						modulo: 'IN',
						planilla: p.$w.find('[name=planilla]').val(),
						iniciales: p.$w.find('[name=iniciales]').val(),
						fec: p.$w.find('[name=fec]').val(),
						fecfin: p.$w.find('[name=fecfin]').val(),
						tipo_inm: p.$w.find('[name=tipo_inm] option:selected').val(),
						observ: p.$w.find('[name=observ]').val(),
						detalle: [],
						glosa: p.$w.find('[name=observ]').val(),
						cont_patrimonial: [],
						total: 0,
						efectivos: [],
						fuente: {
							_id: p.$w.find('[name=fuente] option:selected').data('data')._id.$id,
							cod: p.$w.find('[name=fuente] option:selected').data('data').cod,
							rubro: p.$w.find('[name=fuente] option:selected').data('data').rubro
						}
					},tot_deb=0,tot_hab=0,
					tmp = p.$w.find('[name=orga]').data('data');
					
					if(data.fec==''){
						p.$w.find('[name=fec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
					}
					if(data.fecfin==''){
						p.$w.find('[name=fecfin]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
					}
					if(tmp==null){
						p.$w.find('[name=btnOrga]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una organizaci&oacute;n!',type: 'error'});
					}
					
					for(var i=0,j=(p.$w.find('[name=gridComp] tbody .item').length-1); i<j; i++){
						var det = p.$w.find('[name=gridComp] tbody .item').eq(i).data('data');
						$.extend(det,{
							monto: parseFloat(K.round(p.$w.find('[name=gridComp] tbody .item').eq(i).data('total'),2))
						});
						if(det.cuenta._id.$id!=null)
							det.cuenta._id = det.cuenta._id.$id;
						data.total += parseFloat(K.round(parseFloat(det.monto),2));
						data.detalle.push(det);
					}
					if(data.detalle.length==0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay ning&uacute;n comprobante para los filtros seleccionados!',type: 'error'});
					}
					for(var i=0,j=p.$w.find('[name=gridAnu] tbody .item').length; i<j; i++){
						if(data.comprobantes_anulados==null) data.comprobantes_anulados = [];
						data.comprobantes_anulados.push(p.$w.find('[name=gridAnu] tbody .item').eq(i).data('data'));
					}
					tot_deb = 0;
					tot_hab = 0;
					for(var i=0,j=p.$w.find('[name=gridCont] tbody .item').length; i<j; i++){
						var tmp = p.$w.find('[name=gridCont] tbody .item').eq(i).data('data');
						if(tmp!=null){
							if(tmp.tipo=='D')
								tot_deb += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
							if(tmp.tipo=='H')
								tot_hab += parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total'));
							data.cont_patrimonial.push({
								cuenta: {
									_id: tmp._id.$id,
									cod: tmp.cod,
									descr: tmp.descr
								},
								tipo: tmp.tipo,
								moneda: 'S',
								monto: K.round(parseFloat(p.$w.find('[name=gridCont] tbody .item').eq(i).data('total')),2)
							});
						}
					}
					
					data.efectivos.push({
						moneda: 'S',
						monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(0)').data('total'))
					});
					var tmp = {
						moneda: 'D',
						monto: parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'))
					};
					if(tmp.monto!=0) tmp.tc = parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total_sol'))/parseFloat(p.$w.find('[name=gridPag] tbody .item:eq(1)').data('total'));
					data.efectivos.push(tmp);
					for(var i=0,j=p.$w.find('[name=gridPag] tbody .vouc').length; i<j; i++){
						if(data.vouchers==null) data.vouchers = [];
						data.vouchers.push(p.$w.find('[name=gridPag] tbody .vouc').eq(i).data('data'));
					}
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post('cj/ecom/save_rein',data,function(rein){
						K.clearNoti();
						K.windowPrint({
							id:'windowcjFactPrint',
							title: "Recibo de Caja",
							url: "in/comp/reci_ing2?_id="+rein._id.$id
						});
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'El recibo de ingresos fue registrado con &eacute;xito!'});
						cjErein.init();
					},'json');
				}
			},
			"Cancelar": {
				icon: 'fa-ban',
				type: 'danger',
				f: function(){
					cjErein.init();
				}
			}
		},
		onContentLoaded: function(){
			p.$w = $('#mainPanel');
			K.block({$element: p.$w});
			//p.$w.find('[name=div_ini]').hide();
			p.$w.find('[name=tipo_tipo]').change(function(){
				p.fill();
			});
			p.$w.find('[name=fec]').val(ciHelper.date.get.now_ymd());
			p.$w.find('[name=fecfin]').datepicker({format: 'yyyy-mm-dd'})
				.on('changeDate', function(ev){
					p.fill();
				});
			p.$w.find('[name=fec]').datepicker({format: 'yyyy-mm-dd'})
				.on('changeDate', function(ev){
					p.fill();
				});
			p.$w.find('[name=btnOrga]').click(function(){
				mgOrga.windowSelect({callback: function(data){
					p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					p.$w.find('[name=btnOrga]').button('option','text',false);
					p.$w.find('[name=fec]').change();
				}});
			}).button({icons: {primary: 'ui-icon-search'}});
			p.$w.find('[name=btnOrga]').click(function(){
				mgOrga.windowSelect({callback: function(data){
					p.$w.find('[name=orga]').html(data.nomb).data('data',data);
					p.$w.find('[name=btnOrga]').button('option','text',false);
					p.$w.find('[name=fec]').change();
				}});
			}).button({icons: {primary: 'ui-icon-search'}});
			p.$w.find('[name=btnCaja]').click(function(){
				cjCaja.windowSelect({callback: function(data){
					p.$w.find('[name=caja]').html(data.nomb).data('data',data);
					p.$w.find('[name=btnCaja]').button('option','text',false);
					p.$w.find('[name=fec]').change();
				}});
			}).button({icons: {primary: 'ui-icon-search'}});
			p.$w.find('[name=respo]').html(mgEnti.formatName(K.session.enti));
			new K.grid({
				$el: p.$w.find('[name=gridComp]'),
				search: false,
				pagination: false,
				cols: ['Cuenta Contable','Comprobante','Concepto','Importe'],
				onlyHtml: true
			});
			new K.grid({
				$el: p.$w.find('[name=gridAnu]'),
				search: false,
				pagination: false,
				cols: ['Comprobante','Cliente'],
				onlyHtml: true
			});
			new K.grid({
				$el: p.$w.find('[name=gridCod]'),
				search: false,
				pagination: false,
				cols: ['Pliego','Programa','SubPrograma','Proyecto','Obra','Actividad','Componente','Fuente de Financiamiento'],
				onlyHtml: true
			});
			new K.grid({
				$el: p.$w.find('[name=gridPag]'),
				search: false,
				pagination: false,
				cols: ['Pagos','Cuenta Bancaria','Monto','Monto en Soles','Cliente'],
				onlyHtml: true
			});
			new K.grid({
				$el: p.$w.find('[name=gridCont]'),
				search: false,
				pagination: false,
				cols: ['Cuenta Contable','Debe','Haber'],
				onlyHtml: true
			});
			$.post('cj/rein/get_cod',function(data){
				p.cod = data.cod;
				p.fuen = data.fuen;
				p.$w.find('[name=num]').val(data.cod);
				if(K.session.enti.roles!=null){
					if(K.session.enti.roles.trabajador!=null){
						p.$w.find('[name=orga]').html(K.session.enti.roles.trabajador.programa.nomb)
							.data('data',K.session.enti.roles.trabajador.programa);
						p.$w.find('[name=btnOrga]').button('option','text',false);
						p.fill();
					}
				}
				K.unblock({$element: p.$w});
			},'json');
		}
	});
}
};
define(
	['mg/enti','mg/orga'],
	function(mgEnti,mgOrga){
		return cjErein;
	}
);