peBole = {
	states: {
		R: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		X:{
			descr: "Anulado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Anulado</span>'
		}
	},
	digitRedo: 2,
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'pe',
			action: 'peBole',
			titleBar: {
				title: 'Boletas de Pago'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Pagos','Descuentos','Total',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'pe/bole/lista',
					params: {},
					itemdescr: 'boleta(s)',
					toolbarHTML: '<select class="form-control col-sm-4" name="tipo"></select>&nbsp;'+
						'<input type="text" class="form-control col-sm-4" name="periodo">&nbsp;'+
						'<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>&nbsp;'+
						'<button name="btnLote" class="btn btn-primary"><i class="fa fa-database"></i> Generar Lote</button>'+
						'<button name="btnImprimirLote" class="btn btn-primary"><i class="fa fa-database"></i> Imprimir Lote</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							peBole.windowNew({
								contrato: p.$w.find('[name=tipo] option:selected').data('data')
							});
						});
						$el.find('[name=btnImprimirLote]').click(function(){
							var periodo = p.$w.find('[name=periodo]').val();
							if(periodo==''){
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un periodo!',
									type: 'error'
								});
							}
							var mes = periodo.substr(0,2),
							ano = periodo.substr(3,6);
							var params = {
								tipo: $el.find('[name=tipo] :selected').val(),
								ano: ano,
								mes: mes
							};
							K.windowPrint({
								id:'windowPeFichaPrint',
								title: "Ficha del Trabajador",
								url: "pe/bole/print_bole_lote?"+$.param(params)
							});
						});
						$el.find('[name=tipo]').change(function(){
							var periodo = $el.find('[name=periodo]').val();
							console.log(periodo);
							if(periodo==''){
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un periodo!',
									type: 'error'
								});
							}
							var mes = periodo.substr(0,2),
							ano = periodo.substr(3,6);
							$grid.reinit({params: {
								tipo: $(this).val(),
								ano: ano,
								mes: mes
							}});
						});
						$el.find('[name=periodo]').datepicker({
						    format: "mm-yyyy",
						    viewMode: "months", 
						    minViewMode: "months"
						}).val(ciHelper.date.get.now_per())
						.on('changeDate', function(ev){
							console.log(ev);
							var periodo = moment(ev.date).format('MM-YYYY'),
							//var periodo = p.$w.find('[name=periodo]').val(),
							mes = periodo.substr(0,2),
							ano = periodo.substr(3,6),
							last_day = ''+ciHelper.date.daysInMonth(mes,ano);
							console.log(periodo);
							$el.find('[name=periodo]').val(periodo);
							$el.find('[name=tipo]').change();
						});
						$el.find('[name=btnLote]').click(function(){
							/****************************************************************************************
							 * GENERAR LOTE DE PLANILLAS
							 ****************************************************************************************/
							ciHelper.confirm('&#191;Desea iniciar una generaci&oacute;n en Lote para el Periodo de <b>'+$('#mainPanel [name=periodo]').val()+'</b>&#63;',
							function(){
								K.sendingInfo();
								$.post('pe/trab/all_tipo',{
									tipo: p.$w.find('[name=tipo] option:selected').data('data').cod
								},function(data){
									var trabs = [];
									for (var i in data){
										trabs.push(data[i]);
									}
									var periodo = p.$w.find('[name=periodo]').val();
									if(periodo==''){
										return K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'Debe seleccionar un periodo!',
											type: 'error'
										});
									}
									var mes = periodo.substr(0,2),
									ano = periodo.substr(3,6);
									peBole.windowNew({
										ano: ano,
										mes: mes,
										contrato: p.$w.find('[name=tipo] option:selected').data('data'),
										tipo: p.$w.find('[name=tipo] option:selected').data('data').cod,
										trabs: trabs
									});
								},'json');
							},function(){
								$.noop();
							},'Lote de Planillas');
						});
						$.post('pe/cont/all',function(data){
							var $cbo = $el.find('[name=tipo]');
							for(var i=0; i<data.length; i++){
								$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
								$cbo.find('option:last').data('data',data[i]);
							}
							$cbo.change();
						},'json');
					},
					stopLoad: true,
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+peBole.states[data.estado].label+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.append('<td>'+data.total_pago+'</td>');
						$row.append('<td>'+data.total_desc+'</td>');
						$row.append('<td>'+data.total+'</td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.ult_autor)+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							peBole.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='R') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									peBole.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									peBole.windowNew({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peBole.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('in/tipo/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											peBole.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
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
		$.extend(p,{
			//mode: 'TEST',
			mode: 'PRODUCCION',
			parser: math.parser(),
			vacaciones: false,
			total: {
				pago: 0,
				descuento: 0,
				aporte: 0
			},
			advertir: function(){
				K.msg({title: 'Nuevos Datos!',text: 'Se recomienda refrescar los conceptos!',type: 'error'});
			},
			loadConc: function(){
				p.conceptos = [];
				var enti = p.$w.find('[name=nomb]').data('data'),
				ini = p.$w.find('[name=fecini]').val(),
				fin = p.$w.find('[name=fecfin]').val(),
				periodo = p.$w.find('[name=periodo]').val(),
				mes_periodo = periodo.substr(0,2),
				ano_periodo = periodo.substr(3,6);
				p.entidad = enti;
				if(enti==null){
					p.$w.find('[name=btnSelEnt]').click();
					return K.msg({title: ciHelper.titles.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
				}else if(ini==''){
					p.$w.find('[name=fecini]').focus().datepicker('show');
					return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de inicio!',type: 'error'});
				}else if(fin==''){
					p.$w.find('[name=fecfin]').focus().datepicker('show');
					return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha de fin!',type: 'error'});
				}else{
					//return console.log(p);
					$.post('pe/bole/trab_per',{
						doc: 'boleta',
						enti: enti._id.$id,
						ano: ano_periodo,
						mes: mes_periodo,
						ini: ini,
						fin: fin,
						tipo: p.contrato.cod
					},function(data){
						data.enti = p.trabs[p.i].maestro;
						p.parser.eval('TRAB_TIPO_COMI = "M"');
						for(x in data.enti){
							if(data.enti[x]==false) data.enti[x] = 'boolean(0)';
							else if(data.enti[x]==true) data.enti[x] = 'boolean(1)';
							p.parser.eval('TRAB_'+x+' = '+data.enti[x]);







							//p.parser.eval('TRAB_'+x, math.round('TRAB_'+x, 2) );









						}
						p.parser.eval('VIDA_SEGURO = "undefined"');
						p.parser.eval('APORTE_OBLIGATORIO = 0');
						p.parser.eval('PRIMA_SEGURO = 0');
						p.parser.eval('COMISION_FLUJO_REMUNERACION = 0');
						p.parser.eval('COMISION_MIXTA_REMUNERACION = 0');
						p.parser.eval('SPP_MIX = 0');
						p.parser.eval('SPP_COM = 0');
						p.parser.eval('SPP_AP = 0');
						p.parser.eval('SPP_PR = 0');
						p.parser.eval('SNP_276 = 0');
						p.parser.eval('SNP = 0');
						p.parser.eval('ONP = 0');
						p.parser.eval('AFP_PRIMA = 0');
						p.parser.eval('AFPCOMISION = 0');
						p.parser.eval('DEVENGADO_DU_037_94 = "undefined"');
						p.parser.eval('DEVENGADO_DU_073_97_AN = "undefined"');
						p.parser.eval('DL_25697 = "undefined"');
						p.parser.eval('DL_25981 = "undefined"');
						p.parser.eval('DL_25897_10 = "undefined"');
						p.parser.eval('DU_118_94 = "undefined"');
						p.parser.eval('DU_098_96 = "undefined"');
						p.parser.eval('LEY_26504 = "undefined"');
						p.parser.eval('LEY_26504_SN = "undefined"');
						p.dias_trabajados = p.parser.get('TRAB_DIAS_TRAB');
						p.parser.set('TRAB_MES_ACTUAL',mes_periodo);
						p.$w.find('[name=dias_trab]').attr('placeholder',p.dias_trabajados);
						if(p.$w.find('[name=dias_trab]').val()!=''){
							p.parser.set('TRAB_DIAS_TRAB',p.$w.find('[name=dias_trab]').val());
							p.dias_trabajados = p.parser.get('TRAB_DIAS_TRAB');
						}
						p.vacaciones = false;
						p.parser.set('TRAB_VACACIONES',false);
						if(p.$w.find('[name=vacaciones]').is(':checked')==true){
							p.parser.set('TRAB_VACACIONES',true);
						}
						for(var i=0,j=data.vars.length; i<j; i++){
							p.parser.eval(data.vars[i].cod+' = '+data.vars[i].valor+'');
						}
						for(var i=0,j=data.pago.length; i<j; i++){
							if(p.filtro(data.pago[i],true)){
								try{
									var formula = data.pago[i].formula;
									data.pago[i].formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+data.pago[i].cod+"__");
									p.parser.eval('__VALUE'+data.pago[i].cod+'__ = 0');
									p.parser.eval(data.pago[i].cod+' = '+data.pago[i].formula+'');
									if(data.pago[i].formula.indexOf('"')==-1)
										p.parser.eval(data.pago[i].cod+'='+data.pago[i].cod);
									p.parser.set(data.pago[i].cod, math.round( p.parser.get(data.pago[i].cod) , peBole.digitRedo) );
								}catch(e){
									p.parser.eval(data.pago[i].cod+' = 0');
								}
							}
						}
						for(var i=0,j=data.descuento.length; i<j; i++){
							if(p.filtro(data.descuento[i],true)){
								try{
									var formula = data.descuento[i].formula;
									data.descuento[i].formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+data.descuento[i].cod+"__");
									p.parser.eval('__VALUE'+data.descuento[i].cod+'__ = 0');
									p.parser.eval(data.descuento[i].cod+' = '+data.descuento[i].formula+'');
									if(data.descuento[i].formula.indexOf('"')==-1)
										p.parser.eval(data.descuento[i].cod+'='+data.descuento[i].cod);
									p.parser.set(data.descuento[i].cod, math.round( p.parser.get(data.descuento[i].cod) , peBole.digitRedo) );
								}catch(e){
									p.parser.eval(data.descuento[i].cod+' = 0');
								}
							}
						}
						for(var i=0,j=data.aporte.length; i<j; i++){
							if(p.filtro(data.aporte[i],true)){
								try{
									var formula = data.aporte[i].formula;
									data.aporte[i].formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+data.aporte[i].cod+"__");
									p.parser.eval('__VALUE'+data.aporte[i].cod+'__ = 0');
									p.parser.eval(data.aporte[i].cod+' = '+data.aporte[i].formula+'');
									if(data.aporte[i].formula.indexOf('"')==-1)
										p.parser.eval(data.aporte[i].cod+'='+data.aporte[i].cod);
									p.parser.set(data.aporte[i].cod, math.round( p.parser.get(data.aporte[i].cod) , peBole.digitRedo) );
								}catch(e){
									p.parser.eval(data.aporte[i].cod+' = 0');
								}
							}
						}
						for(var i=0,j=data.bono.length; i<j; i++){
							try{
								p.parser.eval(data.bono[i].cod+' = '+data.bono[i].valor+'');
							}catch(e){
								p.parser.eval(data.bono[i].cod+' = 0');
							}
						}
						/*****************************************************************************************************
						* AHORA SE DIBUJAN LAS GRILLAS
						*****************************************************************************************************/
						p.$w.find('[name=gridPago] tbody').empty();
						p.$w.find('[name=gridDesc] tbody').empty();
						p.$w.find('[name=gridApor] tbody').empty();
						for(var i=0,j=data.pago.length; i<j; i++){
							if(p.filtro(data.pago[i])){
								var $row = $('<tr class="item" name="'+data.pago[i].cod+'">');
								$row.append('<td>'+data.pago[i].descr+'('+data.pago[i].cod+')</td>');
								if(data.pago[i].formula.indexOf('__VALUE')!=-1){
									$row.append('<td><input type="text" class="form-control" name="valor" value="0" /></td>');
									$row.find('[name=valor]').keyup(function(){
										var valor = $(this).val(),
										data = $(this).closest('.item').data('data');
										p.parser.eval('__VALUE'+data.cod+'__='+valor);
										p.refresh();
									});
								}else{
									$row.append('<td>');
								}
								$row.append('<td>'+p.parser.get(data.pago[i].cod)+'</td>');
								$row.append('<td><input type="text" class="form-control" name="glosa" /></td>');
								$row.data('data',data.pago[i]);
								p.$w.find('[name=gridPago] tbody').append($row);
							}
						}
						for(var i=0,j=data.descuento.length; i<j; i++){
							if(p.filtro(data.descuento[i])){
								var $row = $('<tr class="item" name="'+data.descuento[i].cod+'">');
								$row.append('<td>'+data.descuento[i].descr+'('+data.descuento[i].cod+')</td>');
								if(data.descuento[i].formula.indexOf('__VALUE')!=-1){
									$row.append('<td><input type="text" class="form-control" name="valor" value="0" /></td>');
									$row.find('[name=valor]').keyup(function(){
										var valor = $(this).val(),
										data = $(this).closest('.item').data('data');
										p.parser.eval('__VALUE'+data.cod+'__='+valor);
										p.refresh();
									});
								}else{
									$row.append('<td>');
								}
								$row.append('<td>'+p.parser.get(data.descuento[i].cod)+'</td>');
								$row.append('<td><input type="text" class="form-control" name="glosa" /></td>');
								$row.data('data',data.descuento[i]);
								p.$w.find('[name=gridDesc] tbody').append($row);
							}
						}
						for(var i=0,j=data.aporte.length; i<j; i++){
							if(p.filtro(data.aporte[i])){
								var $row = $('<tr class="item" name="'+data.aporte[i].cod+'">');
								$row.append('<td>'+data.aporte[i].descr+'</td>');
								if(data.aporte[i].formula.indexOf('__VALUE')!=-1){
									$row.append('<td><input type="text" class="form-control" name="valor" value="0" /></td>');
									$row.find('[name=valor]').keyup(function(){
										var valor = $(this).val(),
										data = $(this).closest('.item').data('data');
										p.parser.eval('__VALUE'+data.cod+'__='+valor);
										p.refresh();
									});
								}else{
									$row.append('<td>');
								}
								$row.append('<td>'+p.parser.get(data.aporte[i].cod)+'</td>');
								$row.append('<td><input type="text" class="form-control" name="glosa" /></td>');
								$row.data('data',data.aporte[i]);
								p.$w.find('[name=gridApor] tbody').append($row);
							}
						}
						p.data = data;
						p.refresh();
					},'json');
				}
			},
			refresh: function(check){
				var data = p.data,
				array_bonos = [];
				p.total = {
					pago: 0,
					descuento: 0,
					aporte: 0
				}
				for(var i=0,j=data.bono.length; i<j; i++){
					if(data.bono[i].tipo=='P'){
						if(p.filtro(data.bono[i])){
							if(p.mode=='TEST') console.log(data.bono[i].cod);
							p.parser.eval(data.bono[i].cod+' = '+data.bono[i].formula+'');
							if(data.bono[i].formula.indexOf('"')==-1){
								p.parser.eval(data.bono[i].cod+'='+data.bono[i].cod);
							}
							p.parser.set(data.bono[i].cod, math.round( p.parser.get(data.bono[i].cod) , peBole.digitRedo) );
							p.$w.find('[name='+data.bono[i].cod+'] td:eq(2)').html(K.round(p.parser.get(data.bono[i].cod),peBole.digitRedo))
								.data('valor',K.round(p.parser.get(data.bono[i].cod),peBole.digitRedo));
							array_bonos.push(data.bono[i].cod);
						}
					}
				}
				for(var i=0,j=data.pago.length; i<j; i++){
					if(p.filtro(data.pago[i])){
						if(p.mode=='TEST') console.log(data.pago[i].cod);
						if(array_bonos.indexOf(data.pago[i].cod)==-1){
							p.parser.eval(data.pago[i].cod+' = '+data.pago[i].formula+'');
							if(data.pago[i].formula.indexOf('"')==-1){
								p.parser.eval(data.pago[i].cod+'='+data.pago[i].cod);
							}
							var tmp_val = math.round( p.parser.get(data.pago[i].cod) , peBole.digitRedo);
							p.parser.set(data.pago[i].cod, tmp_val );
							p.$w.find('[name='+data.pago[i].cod+'] td:eq(2)').html(tmp_val)
								.data('valor',tmp_val);
						}
					}
				}
				for(var i=0,j=data.bono.length; i<j; i++){
					if(data.bono[i].tipo=='D'){
						if(p.filtro(data.bono[i])){
							if(p.mode=='TEST') console.log(data.bono[i].cod);
							p.parser.eval(data.bono[i].cod+' = '+data.bono[i].formula+'');
							if(data.bono[i].formula.indexOf('"')==-1){
								p.parser.eval(data.bono[i].cod+'='+data.bono[i].cod);
							}
							p.parser.set(data.bono[i].cod, math.round( p.parser.get(data.bono[i].cod) , peBole.digitRedo) );
							p.$w.find('[name='+data.bono[i].cod+'] td:eq(2)').html(K.round(p.parser.get(data.bono[i].cod),peBole.digitRedo))
								.data('valor',K.round(p.parser.get(data.bono[i].cod),peBole.digitRedo));
							array_bonos.push(data.bono[i].cod);
						}
					}
				}
				for(var i=0,j=data.descuento.length; i<j; i++){
					if(p.filtro(data.descuento[i])){
						if(p.mode=='TEST') console.log(data.descuento[i].cod);
						if(array_bonos.indexOf(data.descuento[i].cod)==-1){
							p.parser.eval(data.descuento[i].cod+' = '+data.descuento[i].formula+'');
							if(data.descuento[i].formula.indexOf('"')==-1){
								p.parser.eval(data.descuento[i].cod+'='+data.descuento[i].cod);
							}
							p.parser.set(data.descuento[i].cod, math.round( p.parser.get(data.descuento[i].cod) , peBole.digitRedo) );
							p.$w.find('[name='+data.descuento[i].cod+'] td:eq(2)').html(K.round(p.parser.get(data.descuento[i].cod),peBole.digitRedo))
								.data('valor',K.round(p.parser.get(data.descuento[i].cod),peBole.digitRedo));
							//p.total.descuento = p.total.descuento + p.parser.get(data.descuento[i].cod);
						}
					}
				}
				for(var i=0,j=data.aporte.length; i<j; i++){
					if(p.filtro(data.aporte[i])){
						if(p.mode=='TEST') console.log(data.aporte[i].cod);
						if(array_bonos.indexOf(data.aporte[i].cod)==-1){
							p.parser.eval(data.aporte[i].cod+' = '+data.aporte[i].formula+'');
							if(data.aporte[i].formula.indexOf('"')==-1){
								p.parser.eval(data.aporte[i].cod+'='+data.aporte[i].cod);
							}
							p.parser.set(data.aporte[i].cod, math.round( p.parser.get(data.aporte[i].cod) , peBole.digitRedo) );
							p.$w.find('[name='+data.aporte[i].cod+'] td:eq(2)').html(K.round(p.parser.get(data.aporte[i].cod),peBole.digitRedo))
								.data('valor',K.round(p.parser.get(data.aporte[i].cod),peBole.digitRedo));
							//p.total.aporte = p.total.aporte + p.parser.get(data.aporte[i].cod);
						}
					}
				}


				/*
				 * si existe alguna variable del total, se ignora la suma y se reemplaza por esta
				 */
				for(var i=0,j=p.$w.find('[name=gridDesc] tbody .item').length; i<j; i++){
					p.total.descuento += parseFloat(p.$w.find('[name=gridDesc] tbody .item').eq(i).find('td:eq(2)').data('valor'));
				}
				for(var i=0,j=p.$w.find('[name=gridApor] tbody .item').length; i<j; i++){
					p.total.aporte += parseFloat(p.$w.find('[name=gridApor] tbody .item').eq(i).find('td:eq(2)').data('valor'));
				}
				if(!isNaN(p.parser.get('TOTAL_PAGO')))
					p.total.pago = p.parser.get('TOTAL_PAGO');
				if(!isNaN(p.parser.get('TOTAL_PAGO_CAS')))
					p.total.pago = p.parser.get('TOTAL_PAGO_CAS');
				if(!isNaN(p.parser.get('TOTAL_DSCTO')))
					p.total.descuento = p.parser.get('TOTAL_DSCTO');
				if(!isNaN(p.parser.get('TOTAL_DSCTO_CAS')))
					p.total.descuento = p.parser.get('TOTAL_DSCTO_CAS');






				/*if(check==null){
					p.refresh(1);
				}else{*/
					//console.log(p.total);
					p.neto = K.round(p.total.pago-p.total.descuento,2);
					p.redondeo_dif = '0';
					p.redondeo = p.neto+"";
					p.redondeo = p.redondeo.substring(p.redondeo.length-1,p.redondeo.length);
					p.$w.find('[name=neto]').html('S/.'+p.neto);
					p.$w.find('[name=neto_pagar]').html('S/.'+p.neto);
					if(parseInt(p.redondeo)>0){
						p.$w.find('[name=redondeo]').html('S/.'+'0.0'+p.redondeo);
						if(parseInt(p.redondeo)>5){
							p.redondeo = 10-parseInt(p.redondeo);
							p.redondeo_dif = '1';
							p.$w.find('[name=neto]').html('S/.'+p.neto);
							p.$w.find('[name=neto_pagar]').html('S/.'+p.neto);
							p.$w.find('[name=redondeo]').html('S/.'+'0.0'+p.redondeo);
							p.redondeo = p.redondeo/100;
							p.neto_pagar = (parseFloat(p.neto)+0.1)-p.redondeo;
						}else if(parseInt(p.redondeo)==5){
							p.redondeo = 5;
							p.redondeo_dif = '0';
							p.$w.find('[name=redondeo]').html('S/.'+'0.0'+p.redondeo);
							p.redondeo = p.redondeo/100;
							p.neto_pagar = parseFloat(p.neto)-p.redondeo;
						}else{
							p.redondeo = p.redondeo/100;
							p.neto_pagar = parseFloat(p.neto)-p.redondeo;
						}
					}else{
						p.redondeo = p.redondeo/100;
						p.neto_pagar = parseFloat(p.neto)-p.redondeo;
					}
					p.$w.find('[name=neto_pagar]').html('S/.'+parseFloat(p.neto_pagar).toFixed(2));
					/**************************************************************************************/
					p.$w.find('[name=gridPago] tfoot').remove();
					p.$w.find('[name=gridPago] table').append('<tfoot>');
					var $row = $('<tr class="item danger">');
					$row.append('<td colspan="2">TOTAL DE PAGOS</td>');
					$row.append('<td>'+ciHelper.formatMon(p.total.pago)+'</td>');
					$row.append('<td>');
					p.$w.find('[name=gridPago] table tfoot').append($row);
					/**************************************************************************************/
					p.$w.find('[name=gridDesc] tfoot').remove();
					p.$w.find('[name=gridDesc] table').append('<tfoot>');
					var $row = $('<tr class="item danger">');
					$row.append('<td colspan="2">TOTAL DE DESCUENTOS</td>');
					$row.append('<td>'+ciHelper.formatMon(p.total.descuento)+'</td>');
					$row.append('<td>');
					p.$w.find('[name=gridDesc] table tfoot').append($row);
					/**************************************************************************************/
					p.$w.find('[name=gridApor] tfoot').remove();
					p.$w.find('[name=gridApor] table').append('<tfoot>');
					var $row = $('<tr class="item danger">');
					$row.append('<td colspan="2">TOTAL DE APORTES</td>');
					$row.append('<td>'+ciHelper.formatMon(p.total.aporte)+'</td>');
					$row.append('<td>');
					p.$w.find('[name=gridApor] table tfoot').append($row);
					/**************************************************************************************/
				//}
					if(p.trabs!=null){
						p.$w.find('#div_buttons button:first').click();
					}
			},
			filtro: function(concepto, guardar=false){
				var ok = false;
				if(concepto.filtro!=null){
					var ok_tot = 0;
					for(var ii=0; ii<concepto.filtro.length; ii++){
						var filtro = concepto.filtro[ii];
						switch(filtro.tipo){
							case '1':
								if(p.entidad.roles.trabajador.nivel!=null){
									if(p.entidad.roles.trabajador.nivel._id.$id==filtro.valor._id.$id)
										ok_tot++;
								}
								break;
							case '2':
								if(p.entidad.roles.trabajador.nivel_carrera!=null){
									if(p.entidad.roles.trabajador.nivel_carrera._id.$id==filtro.valor._id.$id)
										ok_tot++;
								}
								break;
							case '3':
								if(p.entidad.roles.trabajador.modalidad!=null){
									if(p.entidad.roles.trabajador.modalidad==filtro.valor)
										ok_tot++;
								}
								break;
							case '4':
								if(p.entidad.roles.trabajador.cargo_clasif!=null){
									if(p.entidad.roles.trabajador.cargo_clasif._id.$id==filtro.valor._id.$id)
										ok_tot++;
								}
								break;
							case '5':
								if(p.entidad.roles.trabajador.grupo_ocup!=null){
									if(p.entidad.roles.trabajador.grupo_ocup._id.$id==filtro.valor._id.$id)
										ok_tot++;
								}
								break;
							case '6':
								if(p.entidad.roles.trabajador.tipo!=null){
									if(p.entidad.roles.trabajador.tipo==filtro.valor)
										ok_tot++;
								}
								break;
							case '7':
								if(p.entidad.roles.trabajador.eps!=null){
									if(p.entidad.roles.trabajador.eps==filtro.valor)
										ok_tot++;
								}
								break;
							case '8':
								if(p.entidad.roles.trabajador.pension!=null){
									if(filtro.valor=="SNP"){
										if(p.entidad.roles.trabajador.pension._id.$id=="51a535414d4a13c40900001c"){
											ok_tot++;
										}
									}else if(filtro.valor=="SPP"){
										if(p.entidad.roles.trabajador.pension._id.$id!="51a535414d4a13c40900001c"){
											ok_tot++;
										}
									}
								}
								break;
							case '9':
								if(p.entidad.roles.trabajador.comision!=null){
									if(p.entidad.roles.trabajador.comision==filtro.valor)
										ok_tot++;
								}
								break;
						}
					}
					if(ok_tot==concepto.filtro.length){
					//if(ok_tot>0){
						ok = true;
						/*p.conceptos.push({
							concepto: {
								_id: ((concepto._id!=null)?concepto._id.$id:''),
								cod: concepto.cod,
								nomb: concepto.descr,
								formula: concepto.formula,
								imprimir: concepto.imprimir,
								tipo: concepto.tipo
							},
							subtotal: K.round(parseFloat(0),2)
						});*/
					}
					if(filtro.tipo=='6'){
						if(ok_tot>0){
							ok = true;
						}
					}
				}else{
					ok = true;
					/*p.conceptos.push({
						concepto: {
							_id: ((concepto._id!=null)?concepto._id.$id:''),
							cod: concepto.cod,
							nomb: concepto.descr,
							formula: concepto.formula,
							imprimir: concepto.imprimir,
							tipo: concepto.tipo
						},
						subtotal: K.round(parseFloat(0),2)
					});*/
				}
				if(ok==true&&guardar==true){
					p.conceptos.push({
						concepto: {
							_id: ((concepto._id!=null)?concepto._id.$id:''),
							cod: concepto.cod,
							nomb: concepto.descr,
							formula: concepto.formula,
							imprimir: concepto.imprimir,
							planilla: concepto.planilla,
							tipo: concepto.tipo
						},
						subtotal: K.round(parseFloat(0),2)
					});
				}else{
					if(guardar==true){
						if(concepto.cod=='COMISION_MIXTA_REMUNERACION'||concepto.cod=='COMISION_FLUJO_REMUNERACION'||concepto.cod=='PRIMA_SEGURO'||concepto.cod=='SNP_276'||concepto.cod=='SPP_MIX'||concepto.cod=='SPP_COM'||concepto.cod=='APORTE_OBLIGATORIO'||concepto.cod=='PRIMA_SEGURO'){
							p.conceptos.push({
								concepto: {
									_id: ((concepto._id!=null)?concepto._id.$id:''),
									cod: concepto.cod,
									nomb: concepto.descr,
									formula: '0',
									imprimir: concepto.imprimir,
									planilla: concepto.planilla,
									tipo: concepto.tipo
								},
								subtotal: 0
							});
							ok = false;
						}
					}
				}
				return ok;
			}
		});
		new K.Panel({ 
			title: 'Nueva Boleta',
			contentURL: 'pe/bole/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var periodo = p.$w.find('[name=periodo]').val();
						var data = {
							periodo: {
						    	mes: periodo.substr(0,2),
								ano: periodo.substr(3,6),
								inicio: p.$w.find('[name=fecini]').val(),
								fin: p.$w.find('[name=fecfin]').val()
							}
						};
						
						if(p.id!=null){
							data._id = p.id;
						}
						if(p.documento!=null){
							data._id = p.documento._id;
						}
						if(p.vacaciones==true)
							data.vacaciones = true;
						/*if(p.enti==null){
							p.$w.find('[name=btnSelEnt]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un trabajador!',type: 'error'});
						}else data.trabajador = ciHelper.enti.dbTrabRel(p.enti);*/
						if(data.periodo.inicio==''){
							p.$w.find('[name=ini]').datepicker('show');
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de inicio!',type: 'error'});
						}else if(data.periodo.fin==''){
							p.$w.find('[name=fin]').datepicker('show');
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de fin!',type: 'error'});
						}
						/*data.contrato = {
							_id: p.contrato._id.$id,
							nomb: p.contrato.nomb,
							cod: p.contrato.cod
						};*/
						$.each(p.conceptos,function(){
							if(p.$w.find('[name='+this.concepto.cod+']').length!=0){
								this.glosa = p.$w.find('[name='+this.concepto.cod+']').find('[name=glosa]').val();
								this.subtotal = parseFloat(p.$w.find('[name='+this.concepto.cod+']').find('td').eq(2).html());
							}else{
								this.glosa = '';
								this.subtotal = 0;
							}
						});
						$.extend(data,{
							conceptos: p.conceptos,
							total_pago: K.round(p.total.pago,2),
							total_desc: K.round(p.total.descuento,2),
							total_apor: K.round(p.total.aporte,2),
							total: K.round(p.neto,2),
							redondeo: p.redondeo,
							redondeo_dif: p.redondeo_dif,
							neto: parseFloat(p.neto_pagar).toFixed(1)+'0',
							dias_trabajados: p.dias_trabajados
						});
						if(parseFloat(data.total)<0){
							K.unblock();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'El total de la boleta no puede ser negativo!',
								type: 'error'
							});
						}
						if(p.indicador!=null){
							data.indicador = p.indicador;
						}
						//return console.log(data);
						K.sendingInfo();
						$.post('pe/bole/save',data,function(){
							if(p.trabs==null){
								K.clearNoti();
								//K.closeWindow(p.$w.attr('id'));
								K.notification({title: ciHelper.titleMessages.regiGua,text: 'El trabajador fue registrado con &eacute;xito!'});
								//$('#pageWrapperLeft .ui-state-highlight').click();
								//$('#mainPanel').find('[name=periodo]').change();
								pePlan.init();
							}else{
								p.i++;
								if(p.i<=(p.trabs.length-1)){
									p.parser.clear();
									var trab = p.trabs[p.i];
									p.enti = trab.trabajador;
									p.contrato = trab.contrato;
									p.documento = {_id: trab._id.$id};
									//p.planilla = trab.planilla;
									p.$w.find('[name=nomb]').data('data',p.enti)
										.html( ciHelper.enti.formatName(p.enti) ).attr('title',ciHelper.enti.formatName(p.enti)).tooltip();
									if(p.mode=='TEST'){
										console.log('==========>');
										console.info(p.enti);
									}
									$('.progress-bar').html((p.i+1)+' trabajadores de '+(p.trabs.length));
									p.loadConc();
								}else{
									K.clearNoti();
									K.notification({title: ciHelper.titleMessages.regiGua,text: 'Lote realizado con &eacute;xito!'});
									pePlan.init();
									K.unblock();
								}
							}
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						pePlan.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnRefresh]').click(function(){
					p.loadConc();
				});
				new K.grid({
					$el: p.$w.find('[name=gridPago]'),
					search: false,
					pagination: false,
					cols: ['Concepto','Valor','Monto','Glosa'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridDesc]'),
					search: false,
					pagination: false,
					cols: ['Concepto','Valor','Monto','Glosa'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridApor]'),
					search: false,
					pagination: false,
					cols: ['Concepto','Valor','Monto','Glosa'],
					onlyHtml: true
				});
				p.$w.find('[name=btnSelEnt]').click(function(){
					mgEnti.windowSelect({bootstrap: true,callback: function(data){
						p.enti = data;
						K.block();
						p.$w.find('[name=nomb]').data('data',data)
							.html( ciHelper.enti.formatName(data) ).attr('title',ciHelper.enti.formatName(data)).tooltip();
						p.loadConc();
						p.$w.find('[name=dni]').html( data.docident[0].num );
						if(data.roles.trabajador.cargo._id!=null)
							p.$w.find('[name=cargo]').html( data.roles.trabajador.cargo.nomb );
						else
							p.$w.find('[name=cargo]').html( data.roles.trabajador.cargo.funcion );
						//p.$w.find('[name=organizacion]').html( data.roles.trabajador.organizacion.nomb ).attr('title',data.roles.trabajador.organizacion.nomb).tooltip();
						p.$w.find('[name=organizacion]').html( data.roles.trabajador.programa.nomb ).attr('title',data.roles.trabajador.programa.nomb).tooltip();
						if(data.roles.trabajador.nivel!=null) p.$w.find('[name=nivel]').html( data.roles.trabajador.nivel.nomb );
						p.$w.find('[name=essalud]').html( data.roles.trabajador.essalud );
						if(data.roles.trabajador.pension!=null){
							p.$w.find('[name=pension]').html( data.roles.trabajador.pension.nomb );
							p.$w.find('[name=cui]').html( data.roles.trabajador.cod_aportante );
						}else{
							p.$w.find('[name=pension]').html('--');
							p.$w.find('[name=cui]').html('--');
						}
						K.unblock();
					},filter: [
						{nomb: 'tipo_enti',value: 'P'},
						//{nomb: 'roles.trabajador',value: {$exists: true}},
						{nomb: 'roles.trabajador.contrato.cod',value: p.contrato.cod}
					]});
				});
				p.$w.find('.i-checks').iCheck({
					checkboxClass: 'icheckbox_square-green',
					radioClass: 'iradio_square-green'
				});
				p.$w.find('[name=periodo]').datepicker({
				    format: "mm-yyyy",
				    viewMode: "months",
				    minViewMode: "months"
				}).val(ciHelper.date.get.now_per())
				.on('changeDate', function(ev){
					console.log(ev);
					var periodo = moment(ev.date).format('MM-YYYY'),
					//var periodo = p.$w.find('[name=periodo]').val(),
					mes = periodo.substr(0,2),
					ano = periodo.substr(3,6);
					if(parseFloat(mes)==1){
						mes = 12;
						ano = parseFloat(ano)-1;
					}else{
						mes = parseFloat(mes)-1;
					}
					var last_day = moment(ano+'-'+mes+'-01').date(0).format('DD');
					console.log(periodo);
					p.$w.find('[name=fecini]').val(ano+'-'+mes+'-01');
					p.$w.find('[name=fecfin]').val(ano+'-'+mes+'-'+(last_day.length==2?last_day:'0'+last_day));
					p.advertir();
				}).change(function(){
					//var periodo = p.$w.find('[name=periodo]').val(),
					var periodo = moment(ev.date).format('MM-YYYY'),
					mes = periodo.substr(0,2),
					ano = periodo.substr(3,6);
					if(parseFloat(mes)==1){
						mes = 12;
						ano--;
					}else{
						mes = parseFloat(mes)-1;
					}
					var last_day = moment(ano+'-'+mes+'-01').date(0).format('DD');
					p.$w.find('[name=fecini]').val(ano+'-'+mes+'-01');
					p.$w.find('[name=fecfin]').val(ano+'-'+mes+'-'+(last_day.length==2?last_day:'0'+last_day));
					p.advertir();
				});
				if(p.ano!=null&&p.mes!=null){
					p.$w.find('[name=periodo]').val(p.mes+'-'+p.ano);
				}
				p.$w.find('[name=fecini],[name=fecfin]').datepicker()
				.on('changeDate', function(ev){
					p.advertir();
				}).change(function(){
					p.advertir();
				});
				
			    p.$w.find('[name=dias_trab]').change(function(){ p.advertir(); });
			    p.$w.find('[name=vacaciones]').click(function(){ p.advertir(); });
			    if(p.planilla!=null){
			    	p.$w.find('[name=periodo]').val(p.planilla.periodo.mes+'-'+p.planilla.periodo.ano);
			    	p.$w.find('[name=fecini]').val(moment(p.planilla.fecini.sec,"X").format("YYYY-MM-DD"));
					p.$w.find('[name=fecfin]').val(moment(p.planilla.fecfin.sec,"X").format("YYYY-MM-DD"));
			    }else{
			    	var periodo = p.planilla.periodo.mes+'-'+p.planilla.periodo.ano,
					mes = periodo.substr(0,2),
					ano = periodo.substr(3,6);
					if(parseFloat(mes)==1){
						mes = 12;
						ano--;
					}else{
						mes = parseFloat(mes)-1;
					}
					var last_day = ''+ciHelper.date.daysInMonth(mes,ano);
					p.$w.find('[name=fecini]').val(ano+'-'+mes+'-01');
					p.$w.find('[name=fecfin]').val(ano+'-'+mes+'-'+(last_day.length==2?last_day:'0'+last_day));
			    }
			    /*if(p.trabajador==null){
			    	p.$w.find('[name=btnSelEnt]').click();
			    }*/
			    if(p.trabs==null&&p.enti==null&&p.id==null){
						p.$w.find('[name=btnSelEnt]').click();
				}else if(p.id!=null){
					K.block();
					$.post('pe/bole/get',{_id: p.id},function(data){
						p.enti = data.trabajador;
						p.contrato = data.contrato;
						p.$w.find('[name=nomb]').data('data',data.trabajador)
							.html( ciHelper.enti.formatName(data.trabajador) ).attr('title',ciHelper.enti.formatName(data.trabajador)).tooltip();
						p.$w.find('[name=dni]').html( data.trabajador.docident[0].num );
						if(data.trabajador.roles.trabajador.cargo._id!=null)
							p.$w.find('[name=cargo]').html( data.trabajador.roles.trabajador.cargo.nomb );
						else
							p.$w.find('[name=cargo]').html( data.trabajador.roles.trabajador.cargo.funcion );
						//p.$w.find('[name=organizacion]').html( data.trabajador.roles.trabajador.organizacion.nomb ).attr('title',data.trabajador.roles.trabajador.organizacion.nomb).tooltip();
						p.$w.find('[name=organizacion]').html( data.trabajador.roles.trabajador.programa.nomb ).attr('title',data.trabajador.roles.trabajador.programa.nomb).tooltip();
						if(data.trabajador.roles.trabajador.nivel!=null) p.$w.find('[name=nivel]').html( data.trabajador.roles.trabajador.nivel.nomb );
						p.$w.find('[name=essalud]').html( data.trabajador.roles.trabajador.essalud );
						if(data.trabajador.roles.trabajador.pension!=null){
							p.$w.find('[name=pension]').html( data.trabajador.roles.trabajador.pension.nomb );
							p.$w.find('[name=cui]').html( data.trabajador.roles.trabajador.cod_aportante );
						}else{
							p.$w.find('[name=pension]').html('--');
							p.$w.find('[name=cui]').html('--');
						}
						if(data.trabajador.roles.trabajador.programa.actividad!=null) p.$w.find('[name=actividad]').html( data.trabajador.roles.trabajador.programa.actividad.cod );
						if(data.trabajador.roles.trabajador.programa.componente!=null) p.$w.find('[name=componente]').html( data.trabajador.roles.trabajador.programa.componente.cod );




















						//





















					},'json');
				}else if(p.enti!=null){
					K.block();
					p.$w.find('[name=periodo]').val(ciHelper.meses[p.mes-1]+' '+p.ano);
					p.$w.find('[name=periodo]').data('mes',p.mes-1);
		    		p.$w.find('[name=periodo]').data('ano',p.ano);
		    		var mes_ant = parseInt(p.mes)-1,
		    		ano_ant = parseInt(p.ano);
		    		if(mes_ant==0){
		    			ano_ant--;
		    			mes_ant = 12;
		    		}
					var last_day = ''+ciHelper.date.daysInMonth(mes_ant,ano_ant);
		    		p.$w.find('[name=ini]').val(ano_ant+'-'+(mes_ant>10?mes_ant:'0'+mes_ant)+'-01');
					p.$w.find('[name=fin]').val(ano_ant+'-'+(mes_ant>10?mes_ant:'0'+mes_ant)+'-'+(last_day.length==2?last_day:'0'+last_day));
					if(p.enti.imagen!=null) p.$w.find('[name=foto]').attr('src','ci/files/get?id='+p.enti.imagen.$id);
					else p.$w.find('[name=foto]').removeAttr('src');
					p.$w.find('[name=nomb]').data('data',p.enti)
					.html( ciHelper.enti.formatName(p.enti) ).attr('title',ciHelper.enti.formatName(p.enti)).tooltip();
					p.loadConc();
					p.$w.find('[name=dni]').html( p.enti.docident[0].num );
					if(p.enti.roles.trabajador.cargo._id!=null)
						p.$w.find('[name=cargo]').html( p.enti.roles.trabajador.cargo.nomb );
					else
						p.$w.find('[name=cargo]').html( p.enti.roles.trabajador.cargo.funcion );
					p.$w.find('[name=orga]').html( p.enti.roles.trabajador.programa.nomb ).attr('title',p.enti.roles.trabajador.programa.nomb).tooltip();
					if(p.enti.roles.trabajador.nivel!=null) p.$w.find('[name=nivel]').html( p.enti.roles.trabajador.nivel.nomb );
					p.$w.find('[name=essalud]').html( p.enti.roles.trabajador.essalud );
					if(p.enti.roles.trabajador.pension!=null){
						p.$w.find('[name=pension]').html( p.enti.roles.trabajador.pension.nomb );
						p.$w.find('[name=cod_aportante]').html( p.enti.roles.trabajador.cod_aportante );
					}else{
						p.$w.find('[name=pension]').html('--');
						p.$w.find('[name=cod_aportante]').html('--');
					}
					K.unblock();
				}else{
					p.i = 0;
					p.indicador = ciHelper.date.get.now_ymdhi();
					var trab = p.trabs[p.i];
					p.enti = trab.trabajador;
					//p.planilla = trab.planilla;
					p.contrato = trab.contrato;
					p.documento = {_id: trab._id.$id};
					p.$w.find('[name=nomb]').data('data',p.enti)
						.html( ciHelper.enti.formatName(p.enti) ).attr('title',ciHelper.enti.formatName(p.enti)).tooltip();
					periodo = p.planilla.periodo.mes+'-'+p.planilla.periodo.ano;

					var _periodo = periodo.split('-');
					if(parseFloat(_periodo[0])==1){
						_periodo[0] = 12;
						_periodo[0] = parseFloat(_periodo[1])-1;
					}else{
						_periodo[0] = parseFloat(_periodo[0])-1;
					}
					var fecha_ini = moment(p.planilla.fecini.sec,"X").format("YYYY-MM-DD");
					var fecha_fin = moment(p.planilla.fecfin.sec,"X").format("YYYY-MM-DD");
					p.loadConc();
					K.block({
						text: 'Espere por favor mientras se realiza el lote de creaci&oacute;n de Boletas de Pago.<br />'+
							'Por favor no cierre el navegador!'
					});
				}
			}
		});
	}
};
define(
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return peBole;
	}
);